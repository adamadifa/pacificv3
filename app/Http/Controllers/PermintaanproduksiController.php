<?php

namespace App\Http\Controllers;

use App\Models\Detailpermintaanproduksi;
use App\Models\Permintaanproduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PermintaanproduksiController extends Controller
{
    public function index(Request $request)
    {
        $bulansekarang = date("m");
        $tahunsekarang = date("Y");
        $query = Permintaanproduksi::query();
        $query->select('permintaan_produksi.*', 'bulan', 'tahun');
        $query->join('oman', 'permintaan_produksi.no_order', '=', 'oman.no_order');
        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
        } else {
            $query->where('tahun', $tahunsekarang);
        }

        $query->orderBy('tahun');
        $query->orderBy('bulan');
        $permintaanproduksi = $query->get();


        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('permintaanproduksi.index', compact('permintaanproduksi', 'bulan'));
    }

    public function show(Request $request)
    {
        $no_permintaan = $request->no_permintaan;
        $permintaan = DB::table('permintaan_produksi')
            ->select('permintaan_produksi.*', 'bulan', 'tahun')
            ->join('oman', 'permintaan_produksi.no_order', '=', 'oman.no_order')
            ->where('no_permintaan', $no_permintaan)->first();
        $detail = DB::table('detail_permintaan_produksi')
            ->select('detail_permintaan_produksi.*', 'nama_barang')
            ->join('master_barang', 'detail_permintaan_produksi.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_permintaan', $no_permintaan)
            ->orderBy('detail_permintaan_produksi.kode_produk')
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('permintaanproduksi.show', compact('permintaan', 'detail', 'bulan'));
    }

    public function create()
    {
        $dataoman = DB::table('oman')
            ->where('status', 0)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->first();
        if ($dataoman->bulan > 9) {
            $bulan = $dataoman->bulan;
        } else {
            $bulan = "0" . $dataoman->bulan;
        }

        $pp =  DB::table('permintaan_produksi')
            ->select('no_permintaan')
            ->orderBy('no_permintaan', 'desc')
            ->first();
        if ($pp == null) {
            $no_permintaan_terakhir = "";
        } else {
            $no_permintaan_terakhir = $pp->no_permintaan;
        }

        $tahun = substr($dataoman->tahun, 2, 2);
        $no_permintaan = buatkode($no_permintaan_terakhir, 'PP' . $bulan . $tahun, 3);
        $tanggal = $dataoman->tahun . "-" . $dataoman->bulan . "-01";
        $akhirtanggal = date("Y-m-t", strtotime($tanggal));
        $detailoman = DB::table('detail_oman')
            ->select('detail_oman.kode_produk', 'nama_barang', DB::raw('SUM(jumlah) as jumlah'), 'saldoakhir')
            ->join('master_barang', 'detail_oman.kode_produk', '=', 'master_barang.kode_produk')
            ->leftJoin(
                DB::raw("(
                SELECT kode_produk,
                SUM(IF(`inout`='IN',jumlah,0)) - SUM(IF(`inout`='OUT',jumlah,0))  as saldoakhir
                FROM detail_mutasi_gudang
                INNER JOIN mutasi_gudang_jadi ON detail_mutasi_gudang.no_mutasi_gudang = mutasi_gudang_jadi.no_mutasi_gudang
                WHERE tgl_mutasi_gudang <= '$tanggal'
                GROUP BY kode_produk
            ) dm"),
                function ($join) {
                    $join->on('detail_oman.kode_produk', '=', 'dm.kode_produk');
                }
            )
            ->where('no_order', $dataoman->no_order)
            ->groupByRaw('detail_oman.kode_produk,nama_barang,saldoakhir')
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('permintaanproduksi.create', compact('dataoman', 'bulan', 'detailoman', 'no_permintaan'));
    }

    public function store(Request $request)
    {
        $no_permintaan = $request->no_permintaan;
        $tgl_permintaan = $request->tgl_permintaan;
        $no_order = $request->no_order;

        $oman = DB::table('oman')->where('no_order', $no_order)->first();
        $bulan = $oman->bulan;
        $tahun = $oman->tahun;
        $status = 0;

        $kode_produk = $request->kode_produk;
        $oman_mkt = $request->oman_mkt;
        $stok_gudang  = $request->saldoakhir;
        $bufferstok = $request->bufferstok;
        $data = [
            'no_permintaan' => $no_permintaan,
            'tgl_permintaan' => $tgl_permintaan,
            'no_order' => $no_order,
            'status' => $status
        ];

        for ($i = 0; $i < count($kode_produk); $i++) {
            $detailpermintaan[]   = [
                'no_permintaan' => $no_permintaan,
                'kode_produk' => $kode_produk[$i],
                'oman_mkt' => !empty($oman_mkt[$i]) ? $oman_mkt[$i] : 0,
                'stok_gudang' => !empty($stok_gudang[$i]) ? $stok_gudang[$i] : 0,
                'buffer_stok' => !empty($bufferstok[$i]) ? $bufferstok[$i] : 0,
            ];
        }
        DB::beginTransaction();
        try {
            DB::table('permintaan_produksi')->insert($data);
            $chunks = array_chunk($detailpermintaan, 5);
            foreach ($chunks as $chunk) {
                Detailpermintaanproduksi::insert($chunk);
            }
            DB::table('oman')->where('no_order', $no_order)->update(['status' => 1]);
            DB::commit();
            return redirect('/permintaanproduksi')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/permintaanproduksi')->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }

    public function delete($no_permintaan)
    {
        $no_permintaan = Crypt::decrypt($no_permintaan);
        $permintaan = DB::table('permintaan_produksi')->where('no_permintaan', $no_permintaan)->first();
        $no_order = $permintaan->no_order;
        DB::beginTransaction();
        try {
            DB::table('permintaan_produksi')->where('no_permintaan', $no_permintaan)->delete();
            DB::table('oman')->where('no_order', $no_order)->update(['status' => 0]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }

    public function approve($no_permintaan)
    {
        $no_permintaan = Crypt::decrypt($no_permintaan);
        $permintaan = DB::table('permintaan_produksi')->where('no_permintaan', $no_permintaan)->first();
        $no_order = $permintaan->no_order;
        DB::beginTransaction();
        try {
            DB::table('permintaan_produksi')->where('no_permintaan', $no_permintaan)->update(['status' => 1]);
            DB::table('oman')->where('no_order', $no_order)->update(['status' => 2]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Approve']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Di Approve, Hubungi Tim IT']);
        }
    }

    public function batalkanapprove($no_permintaan)
    {
        $no_permintaan = Crypt::decrypt($no_permintaan);
        $permintaan = DB::table('permintaan_produksi')->where('no_permintaan', $no_permintaan)->first();
        $no_order = $permintaan->no_order;
        DB::beginTransaction();
        try {
            DB::table('permintaan_produksi')->where('no_permintaan', $no_permintaan)->update(['status' => 0]);
            DB::table('oman')->where('no_order', $no_order)->update(['status' => 1]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Batalkan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan, Hubungi Tim IT']);
        }
    }
}
