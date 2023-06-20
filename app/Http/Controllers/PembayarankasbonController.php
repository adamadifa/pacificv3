<?php

namespace App\Http\Controllers;

use App\Models\Kasbonpotongangaji;
use App\Models\Pembayarankasbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PembayarankasbonController extends Controller
{
    public function index(Request $request)
    {

        $level = Auth::user()->level;
        $show_for_hrd = config('global.show_for_hrd');
        $show_for_hrd_2 = config('global.show_for_hrd_2');
        $level_show_all = config('global.show_all');

        $query = Kasbonpotongangaji::query();
        $query->select('kasbon_potongangaji.kode_potongan', 'bulan', 'tahun', 'totalpembayaran');
        if (empty($request->bulan) && empty($request->tahun)) {
            $bulanini = date("m");
            $tahunini = date("Y");
            $query->where('tahun', $tahunini);
        } else {

            if (!empty($request->bulan)) {
                $query->where('bulan', $request->bulan);
            }

            if (!empty($request->tahun)) {
                $query->where('tahun', $request->tahun);
            }
        }
        if (!in_array($level, $level_show_all)) {
            $query->leftJoin(
                DB::raw("(
                SELECT kode_potongan,SUM(jumlah) as totalpembayaran
                FROM kasbon_historibayar
                INNER JOIN kasbon ON kasbon_historibayar.no_kasbon = kasbon.no_kasbon
                INNER JOIN master_karyawan ON kasbon.nik = master_karyawan.nik
                WHERE master_karyawan.id_jabatan NOT IN ($show_for_hrd_2)
                GROUP BY kode_potongan
            ) hb"),
                function ($join) {
                    $join->on('kasbon_potongangaji.kode_potongan', '=', 'hb.kode_potongan');
                }
            );
        } else {
            $query->leftJoin(
                DB::raw("(
                SELECT kode_potongan,SUM(jumlah) as totalpembayaran FROM kasbon_historibayar GROUP BY kode_potongan
            ) hb"),
                function ($join) {
                    $join->on('kasbon_potongangaji.kode_potongan', '=', 'hb.kode_potongan');
                }
            );
        }
        $query->orderBy('bulan');
        $kasbonpotongangaji = $query->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('pembayarankasbon.index', compact('bulan', 'kasbonpotongangaji'));
    }


    public function generatebayarpinjaman(Request $request)
    {
        $bulan = $request->bulantagihan;
        $tahun = $request->tahuntagihan;
        $id_user = Auth::user()->id;
        $kode_potongan = "GJ" . $bulan . $tahun;
        if ($bulan == 12) {
            $bulanpotongan = 1;
            $tahunpotongan = $tahun + 1;
        } else {
            $bulanpotongan = $bulan + 1;
            $tahunpotongan = $tahun;
        }

        if ($bulan <= 9) {
            $bulan = "0" . $bulan;
        }

        if ($bulan == 1) {
            $bulanlast = 12;
            $tahunlast = $tahun - 1;
        } else {
            $bulanlast = $bulan - 1;
            $tahunlast = $tahun;
        }

        //dd($tahun);
        $cek = DB::table('kasbon_potongangaji')->count();
        $ceklast = DB::table('kasbon_potongangaji')->where('bulan', $bulanlast)->where('tahun', $tahunlast)->count();
        if ($cek > 0 && $ceklast == 0) {
            return Redirect::back()->with(['warning' => 'Bulan Sebelumnya Belum Digenerate']);
        }

        $jatuhtempo = $tahunpotongan . "-" . $bulanpotongan . "-01";

        DB::beginTransaction();
        try {
            DB::table('kasbon_potongangaji')->insert([
                'kode_potongan' => $kode_potongan,
                'bulan' => $bulan,
                'tahun' => $tahun
            ]);

            $rencana = DB::table('kasbon')->where('jatuh_tempo', $jatuhtempo)->get();


            foreach ($rencana as $d) {
                $historibayar = DB::table("kasbon_historibayar")
                    ->whereRaw('YEAR(tgl_bayar)="' . $tahun . '"')
                    ->orderBy("no_bukti", "desc")
                    ->first();
                $thn = substr($tahun, 2, 2);
                $last_nobukti = $historibayar != null ? $historibayar->no_bukti : '';
                $no_bukti  = buatkode($last_nobukti, "KB" . $thn, 4);

                //echo $thn;
                $data = [
                    'no_bukti' => $no_bukti,
                    'tgl_bayar' => $tahunpotongan . "-" . $bulanpotongan . "-01",
                    'no_kasbon' => $d->no_kasbon,
                    'jumlah' => $d->jumlah_kasbon,
                    'id_user' => $id_user,
                    'kode_potongan' => $kode_potongan
                ];
                DB::table('kasbon_historibayar')->insert($data);
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function show(Request $request)
    {
        $kode_potongan = $request->kode_potongan;
        $level = Auth::user()->level;
        $show_for_hrd = config('global.show_for_hrd');
        $level_show_all = config('global.show_all');
        $query = Pembayarankasbon::query();
        $query->select('kasbon_historibayar.no_kasbon', 'kasbon.nik', 'nama_karyawan', 'jumlah');
        $query->join('kasbon', 'kasbon_historibayar.no_kasbon', '=', 'kasbon.no_kasbon');
        $query->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik');
        $query->where('kode_potongan', $kode_potongan);
        if (!in_array($level, $level_show_all)) {
            $query->whereNotIn('master_karyawan.id_jabatan', $show_for_hrd);
        }
        $query->orderBy('no_kasbon');
        $historibayar = $query->get();

        return view('pembayarankasbon.show', compact('historibayar', 'kode_potongan'));
    }

    public function deletegenerate($kode_potongan)
    {
        $kode_potongan = Crypt::decrypt($kode_potongan);
        DB::beginTransaction();
        try {
            DB::table('kasbon_potongangaji')->where('kode_potongan', $kode_potongan)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }


    public function cetak($kode_potongan, $export)
    {
        $kode_potongan = Crypt::decrypt($kode_potongan);
        $potongan = DB::table('kasbon_potongangaji')->where('kode_potongan', $kode_potongan)->first();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $bln = $bulan[$potongan->bulan];
        $thn = $potongan->tahun;

        $level = Auth::user()->level;
        $show_for_hrd = config('global.show_for_hrd');
        $level_show_all = config('global.show_all');

        $query = Pembayarankasbon::query();
        $query->select('kasbon_historibayar.no_kasbon', 'kasbon.nik', 'nama_karyawan', 'jumlah', 'nama_jabatan', 'nama_dept', 'no_bukti');
        $query->join('kasbon', 'kasbon_historibayar.no_kasbon', '=', 'kasbon.no_kasbon');
        $query->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik');
        $query->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->where('kode_potongan', $kode_potongan);
        if (!in_array($level, $level_show_all)) {
            $query->whereNotIn('master_karyawan.id_jabatan', $show_for_hrd);
        }
        $query->orderBy('no_kasbon');
        $historibayar = $query->get();

        if ($export == "true") {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Pembayaran Kasbon Bulan $bln $thn.xls");
        }

        return view('pembayarankasbon.cetak', compact('historibayar', 'bln', 'thn'));
    }
}
