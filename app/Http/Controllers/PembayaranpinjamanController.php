<?php

namespace App\Http\Controllers;

use App\Models\Pembayaranpinjaman;
use App\Models\Pinjamanpotongangaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PembayaranpinjamanController extends Controller
{
    public function index(Request $request)
    {
        $level = Auth::user()->level;
        $show_for_hrd = config('global.show_for_hrd');
        $show_for_hrd_2 = config('global.show_for_hrd_2');
        $level_show_all = config('global.show_all');

        $query = Pinjamanpotongangaji::query();
        $query->select('pinjaman_potongangaji.kode_potongan', 'bulan', 'tahun', 'totalpembayaran');
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
                FROM pinjaman_historibayar
                INNER JOIN pinjaman ON pinjaman_historibayar.no_pinjaman = pinjaman.no_pinjaman
                INNER JOIN master_karyawan ON pinjaman.nik = master_karyawan.nik
                WHERE id_jabatan NOT IN ($show_for_hrd_2)
                GROUP BY kode_potongan
            ) hb"),
                function ($join) {
                    $join->on('pinjaman_potongangaji.kode_potongan', '=', 'hb.kode_potongan');
                }
            );
        } else {
            $query->leftJoin(
                DB::raw("(
                SELECT kode_potongan,SUM(jumlah) as totalpembayaran FROM pinjaman_historibayar GROUP BY kode_potongan
            ) hb"),
                function ($join) {
                    $join->on('pinjaman_potongangaji.kode_potongan', '=', 'hb.kode_potongan');
                }
            );
        }

        $query->orderBy('bulan');
        $pinjamanpotongangaji = $query->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('pembayaranpinjaman.index', compact('bulan', 'pinjamanpotongangaji'));
    }
    public function create(Request $request)
    {
        $no_pinjaman = $request->no_pinjaman;
        return view('pembayaranpinjaman.create', compact('no_pinjaman'));
    }

    public function store(Request $request)
    {
        $no_pinjaman = $request->no_pinjaman;
        $tgl_bayar = $request->tgl_bayar;
        $jumlah = str_replace(".", "", $request->jumlah);
        $id_user = Auth::user()->id;
        $tgl = explode("-", $tgl_bayar);
        $historibayar = DB::table("pinjaman_historibayar")
            ->whereRaw('YEAR(tgl_bayar)="' . $tgl[0] . '"')
            ->orderBy("no_bukti", "desc")
            ->first();
        $tahun = substr($tgl[0], 2, 2);
        $last_nobukti = $historibayar != null ? $historibayar->no_bukti : '';
        $no_bukti  = buatkode($last_nobukti, "PJ" . $tahun, 4);




        $rencana = DB::table('pinjaman_rencanabayar')
            ->where('no_pinjaman', $no_pinjaman)
            ->whereRaw('jumlah != bayar')
            ->orderBy('cicilan_ke', 'asc')
            ->get();

        $mulaicicilan = DB::table('pinjaman_rencanabayar')
            ->where('no_pinjaman', $no_pinjaman)
            ->whereRaw('jumlah != bayar')
            ->orderBy('cicilan_ke', 'asc')
            ->first();

        DB::beginTransaction();
        try {

            $sisa = $jumlah;
            $cicilan = "";
            $i = $mulaicicilan->cicilan_ke;
            foreach ($rencana as $d) {

                if ($sisa >= $d->jumlah) {
                    DB::table('pinjaman_rencanabayar')
                        ->where('no_pinjaman', $no_pinjaman)
                        ->where('cicilan_ke', $i)
                        ->update([
                            'bayar' => $d->jumlah
                        ]);
                    //$cicilan .=  $d->cicilan_ke . ",";
                    $sisapercicilan = $d->jumlah - $d->bayar;
                    $sisa = $sisa - $sisapercicilan;

                    if ($sisa == 0) {
                        $cicilan .=  $d->cicilan_ke;
                    } else {
                        $cicilan .=  $d->cicilan_ke . ",";
                    }

                    $coba = $cicilan;
                } else {
                    if ($sisa != 0) {
                        $sisapercicilan = $d->jumlah - $d->bayar;
                        if ($d->bayar != 0) {
                            if ($sisa >= $sisapercicilan) {
                                DB::table('pinjaman_rencanabayar')
                                    ->where('no_pinjaman', $no_pinjaman)
                                    ->where('cicilan_ke', $i)
                                    ->update([
                                        'bayar' =>  DB::raw('bayar +' . $sisapercicilan)
                                    ]);
                                $cicilan .= $d->cicilan_ke . ",";
                                $sisa = $sisa - $sisapercicilan;
                            } else {
                                DB::table('pinjaman_rencanabayar')
                                    ->where('no_pinjaman', $no_pinjaman)
                                    ->where('cicilan_ke', $i)
                                    ->update([
                                        'bayar' =>  DB::raw('bayar +' . $sisa)
                                    ]);
                                //$cicilan .= $d->cicilan_ke . ",";
                                $sisa = $sisa - $sisa;
                                if ($sisa == 0) {
                                    $cicilan .=  $d->cicilan_ke;
                                } else {
                                    $cicilan .=  $d->cicilan_ke . ",";
                                }
                            }
                        } else {
                            DB::table('pinjaman_rencanabayar')
                                ->where('no_pinjaman', $no_pinjaman)
                                ->where('cicilan_ke', $i)
                                ->update([
                                    'bayar' =>  DB::raw('bayar +' . $sisa)
                                ]);
                            //$cicilan .= $d->cicilan_ke;
                            $sisa = $sisa - $sisa;
                            if ($sisa == 0) {
                                $cicilan .=  $d->cicilan_ke;
                            } else {
                                $cicilan .=  $d->cicilan_ke . ",";
                            }
                        }
                    }
                }
                $i++;
            }

            $data = [
                'no_bukti' => $no_bukti,
                'tgl_bayar' => $tgl_bayar,
                'no_pinjaman' => $no_pinjaman,
                'jumlah' => $jumlah,
                'cicilan_ke' => $cicilan,
                'id_user' => $id_user
            ];
            DB::table('pinjaman_historibayar')->insert($data);
            DB::commit();
            echo 0;
        } catch (\Exception $e) {
            DB::rollBack();
            echo 1;
            dd($e);
        }
    }


    function delete(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $trans = DB::table('pinjaman_historibayar')->where('no_bukti', $no_bukti)->first();
        $cicilan_ke = array_map('intval', explode(',', $trans->cicilan_ke));
        $rencana = DB::table('pinjaman_rencanabayar')
            ->where('no_pinjaman', $trans->no_pinjaman)
            ->whereIn('cicilan_ke', $cicilan_ke)
            ->orderBy('cicilan_ke', 'desc')
            ->get();
        //dd($rencana);
        $mulaicicilan = DB::table('pinjaman_rencanabayar')
            ->where('no_pinjaman', $trans->no_pinjaman)
            ->whereIn('cicilan_ke', $cicilan_ke)
            ->orderBy('cicilan_ke', 'desc')
            ->first();
        //dd($mulaicicilan);
        DB::beginTransaction();
        try {
            $sisa = $trans->jumlah;
            $i = $mulaicicilan->cicilan_ke;
            foreach ($rencana as $d) {
                if ($sisa >= $d->bayar) {
                    DB::table('pinjaman_rencanabayar')
                        ->where('no_pinjaman', $trans->no_pinjaman)
                        ->where('cicilan_ke', $i)
                        ->update([
                            'bayar' => DB::raw('bayar -' . $d->bayar)
                        ]);
                    $sisa = $sisa - $d->bayar;
                } else {
                    if ($sisa != 0) {
                        DB::table('pinjaman_rencanabayar')
                            ->where('no_pinjaman', $trans->no_pinjaman)
                            ->where('cicilan_ke', $i)
                            ->update([
                                'bayar' =>  DB::raw('bayar -' . $sisa)
                            ]);
                        $sisa = $sisa - $sisa;
                    }
                }

                $i--;
            }
            DB::table('pinjaman_historibayar')
                ->where('no_bukti', $no_bukti)
                ->delete();


            DB::commit();

            echo 0;
        } catch (\Exception $e) {
            DB::rollback();
            //dd($e);
            echo 1;
        }
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


        if ($bulan == 1) {
            $bulanlast = 12;
            $tahunlast = $tahun - 1;
        } else {
            $bulanlast = $bulan - 1;
            $tahunlast = $tahun;
        }


        $cek = DB::table('pinjaman_potongangaji')->count();
        $ceklast = DB::table('pinjaman_potongangaji')->where('bulan', $bulanlast)->where('tahun', $tahunlast)->count();
        if ($cek > 0 && $ceklast == 0) {
            return Redirect::back()->with(['warning' => 'Bulan Sebelumnya Belum Digenerate']);
        }


        DB::beginTransaction();
        try {
            DB::table('pinjaman_potongangaji')->insert([
                'kode_potongan' => $kode_potongan,
                'bulan' => $bulan,
                'tahun' => $tahun
            ]);

            $rencana = DB::table('pinjaman_rencanabayar')->where('bulan', $bulanpotongan)->where('tahun', $tahunpotongan)
                ->where('bayar', 0)
                ->get();

            foreach ($rencana as $d) {
                $historibayar = DB::table("pinjaman_historibayar")
                    ->whereRaw('YEAR(tgl_bayar)="' . $tahun . '"')
                    ->orderBy("no_bukti", "desc")
                    ->first();
                $thn = substr($tahun, 2, 2);
                $last_nobukti = $historibayar != null ? $historibayar->no_bukti : '';
                $no_bukti  = buatkode($last_nobukti, "PJ" . $thn, 4);

                $data = [
                    'no_bukti' => $no_bukti,
                    'tgl_bayar' => $tahunpotongan . "-" . $bulanpotongan . "-01",
                    'no_pinjaman' => $d->no_pinjaman,
                    'jumlah' => $d->jumlah,
                    'cicilan_ke' => $d->cicilan_ke,
                    'id_user' => $id_user,
                    'kode_potongan' => $kode_potongan
                ];
                DB::table('pinjaman_historibayar')->insert($data);
                DB::table('pinjaman_rencanabayar')->where('bulan', $bulanpotongan)->where('tahun', $tahunpotongan)->where('no_pinjaman', $d->no_pinjaman)->update([
                    'bayar' => $d->jumlah,
                    'kode_potongan' => $kode_potongan
                ]);
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function deletegenerate($kode_potongan)
    {
        $kode_potongan = Crypt::decrypt($kode_potongan);
        DB::beginTransaction();
        try {
            DB::table('pinjaman_potongangaji')->where('kode_potongan', $kode_potongan)->delete();
            DB::table('pinjaman_historibayar')->where('kode_potongan', $kode_potongan)->delete();
            DB::table('pinjaman_rencanabayar')->where('kode_potongan', $kode_potongan)->update(['kode_potongan' => null, 'bayar' => 0]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function show(Request $request)
    {
        $kode_potongan = $request->kode_potongan;
        $level = Auth::user()->level;
        $show_for_hrd = config('global.show_for_hrd');
        $level_show_all = config('global.show_all');

        $query = Pembayaranpinjaman::query();
        $query->select('pinjaman_historibayar.no_pinjaman', 'pinjaman.nik', 'nama_karyawan', 'jumlah', 'cicilan_ke');
        $query->join('pinjaman', 'pinjaman_historibayar.no_pinjaman', '=', 'pinjaman.no_pinjaman');
        $query->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik');
        $query->where('kode_potongan', $kode_potongan);
        if (!in_array($level, $level_show_all)) {
            $query->whereNotIn('id_jabatan', $show_for_hrd);
        }
        $query->orderBy('no_pinjaman');
        $historibayar = $query->get();

        return view('pembayaranpinjaman.show', compact('historibayar', 'kode_potongan'));
    }


    public function cetak($kode_potongan, $export)
    {
        $kode_potongan = Crypt::decrypt($kode_potongan);
        $potongan = DB::table('pinjaman_potongangaji')->where('kode_potongan', $kode_potongan)->first();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $bln = $bulan[$potongan->bulan];
        $thn = $potongan->tahun;

        $level = Auth::user()->level;
        $show_for_hrd = config('global.show_for_hrd');
        $level_show_all = config('global.show_all');


        $query = Pembayaranpinjaman::query();
        $query->select('pinjaman_historibayar.no_pinjaman', 'pinjaman.nik', 'nama_karyawan', 'jumlah', 'cicilan_ke', 'nama_jabatan', 'nama_dept', 'no_bukti');
        $query->join('pinjaman', 'pinjaman_historibayar.no_pinjaman', '=', 'pinjaman.no_pinjaman');
        $query->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik');
        $query->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->where('kode_potongan', $kode_potongan);
        if (!in_array($level, $level_show_all)) {
            $query->whereNotIn('master_karyawan.id_jabatan', $show_for_hrd);
        }
        $query->orderBy('no_pinjaman');
        $historibayar = $query->get();

        if ($export == "true") {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Pembayaran Pinjaman Bulan $bln $thn.xls");
        }

        return view('pembayaranpinjaman.cetak', compact('historibayar', 'bln', 'thn'));
    }
}
