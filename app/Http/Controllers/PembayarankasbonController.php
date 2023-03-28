<?php

namespace App\Http\Controllers;

use App\Models\Kasbonpotongangaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PembayarankasbonController extends Controller
{
    public function index(Request $request)
    {

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
        $query->leftJoin(
            DB::raw("(
            SELECT kode_potongan,SUM(jumlah) as totalpembayaran FROM kasbon_historibayar GROUP BY kode_potongan
        ) hb"),
            function ($join) {
                $join->on('kasbon_potongangaji.kode_potongan', '=', 'hb.kode_potongan');
            }
        );
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
                $tahun = substr($tahun, 2, 2);
                $last_nobukti = $historibayar != null ? $historibayar->no_bukti : '';
                $no_bukti  = buatkode($last_nobukti, "KB" . $tahun, 4);

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
        $historibayar = DB::table('kasbon_historibayar')
            ->select('kasbon_historibayar.no_kasbon', 'kasbon.nik', 'nama_karyawan', 'jumlah')
            ->join('kasbon', 'kasbon_historibayar.no_kasbon', '=', 'kasbon.no_kasbon')
            ->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik')
            ->where('kode_potongan', $kode_potongan)
            ->orderBy('no_kasbon')
            ->get();

        return view('pembayarankasbon.show', compact('historibayar'));
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
}
