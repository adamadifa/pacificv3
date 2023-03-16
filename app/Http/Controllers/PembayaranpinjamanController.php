<?php

namespace App\Http\Controllers;

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
        $query->leftJoin(
            DB::raw("(
            SELECT kode_potongan,SUM(jumlah) as totalpembayaran FROM pinjaman_historibayar GROUP BY kode_potongan
        ) hb"),
            function ($join) {
                $join->on('pinjaman_potongangaji.kode_potongan', '=', 'hb.kode_potongan');
            }
        );
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
                $tahun = substr($tahun, 2, 2);
                $last_nobukti = $historibayar != null ? $historibayar->no_bukti : '';
                $no_bukti  = buatkode($last_nobukti, "PJ" . $tahun, 4);

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
                    'bayar' => $d->jumlah
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
        $historibayar = DB::table('pinjaman_historibayar')
            ->select('pinjaman_historibayar.no_pinjaman', 'pinjaman.nik', 'nama_karyawan', 'jumlah', 'cicilan_ke')
            ->join('pinjaman', 'pinjaman_historibayar.no_pinjaman', '=', 'pinjaman.no_pinjaman')
            ->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik')
            ->where('kode_potongan', $kode_potongan)
            ->orderBy('no_pinjaman')
            ->get();

        return view('pembayaranpinjaman.show', compact('historibayar'));
    }
}
