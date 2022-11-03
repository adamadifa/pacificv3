<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Detailbadstok;
use App\Models\Detailservicekendaraan;
use App\Models\Kendaraan;
use App\Models\Servicekendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporangaController extends Controller
{
    public function  servicekendaraan()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('laporanga.lap_service', compact('cabang'));
    }

    public function cetakservicekendaraan(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $no_polisi = $request->no_polisi;

        $query = Detailservicekendaraan::query();
        $query->selectRaw('kendaraan_service_detail.no_invoice,tgl_service,kendaraan_service.no_polisi,merk,tipe,tipe_kendaraan,kendaraan_service.kode_bengkel,nama_bengkel,kendaraan_service.kode_cabang,kendaraan_service_detail.kode_item,nama_item,qty,harga,total');
        $query->join('kendaraan_service', 'kendaraan_service_detail.no_invoice', '=', 'kendaraan_service.no_invoice');
        $query->join('kendaraan', 'kendaraan_service.no_polisi', '=', 'kendaraan.no_polisi');
        $query->join('bengkel', 'kendaraan_service.kode_bengkel', '=', 'bengkel.kode_bengkel');
        $query->join('kendaraan_service_item', 'kendaraan_service_detail.kode_item', '=', 'kendaraan_service_item.kode_item');
        $query->leftJoin(
            DB::raw("(
            SELECT no_invoice, SUM(qty*harga) as total FROM kendaraan_service_detail GROUP BY no_invoice
            ) detailservice"),
            function ($join) {
                $join->on('kendaraan_service_detail.no_invoice', '=', 'detailservice.no_invoice');
            }
        );

        $query->whereBetween('tgl_service', [$dari, $sampai]);
        if (!empty($kode_cabang)) {
            $query->where('kendaraan_service.kode_cabang', $kode_cabang);
        }

        if (!empty($no_polisi)) {
            $query->where('kendaraan_service.no_polisi', $no_polisi);
        }
        $service = $query->get();


        $kendaraan = Kendaraan::where('no_polisi', $no_polisi)->first();
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        return view('laporanga.cetak_servicekendaraan', compact('service', 'kendaraan', 'cabang', 'dari', 'sampai'));
    }

    public function  rekapbadstok()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('laporanga.lap_rekapbadstok', compact('cabang', 'bulan'));
    }

    public function cetakrekapbadstok(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $query = Detailbadstok::query();
        $query->selectRaw("
        badstok_detail.kode_produk,nama_barang,
        SUM(IF(DAY(tanggal)=1,jumlah,0)) as tgl_1,
        SUM(IF(DAY(tanggal)=2,jumlah,0)) as tgl_2,
        SUM(IF(DAY(tanggal)=3,jumlah,0)) as tgl_3,
        SUM(IF(DAY(tanggal)=4,jumlah,0)) as tgl_4,
        SUM(IF(DAY(tanggal)=5,jumlah,0)) as tgl_5,
        SUM(IF(DAY(tanggal)=6,jumlah,0)) as tgl_6,
        SUM(IF(DAY(tanggal)=7,jumlah,0)) as tgl_7,
        SUM(IF(DAY(tanggal)=8,jumlah,0)) as tgl_8,
        SUM(IF(DAY(tanggal)=9,jumlah,0)) as tgl_9,
        SUM(IF(DAY(tanggal)=10,jumlah,0)) as tgl_10,
        SUM(IF(DAY(tanggal)=11,jumlah,0)) as tgl_11,
        SUM(IF(DAY(tanggal)=12,jumlah,0)) as tgl_12,
        SUM(IF(DAY(tanggal)=13,jumlah,0)) as tgl_13,
        SUM(IF(DAY(tanggal)=14,jumlah,0)) as tgl_14,
        SUM(IF(DAY(tanggal)=15,jumlah,0)) as tgl_15,
        SUM(IF(DAY(tanggal)=16,jumlah,0)) as tgl_16,
        SUM(IF(DAY(tanggal)=17,jumlah,0)) as tgl_17,
        SUM(IF(DAY(tanggal)=18,jumlah,0)) as tgl_18,
        SUM(IF(DAY(tanggal)=19,jumlah,0)) as tgl_19,
        SUM(IF(DAY(tanggal)=20,jumlah,0)) as tgl_20,
        SUM(IF(DAY(tanggal)=21,jumlah,0)) as tgl_21,
        SUM(IF(DAY(tanggal)=22,jumlah,0)) as tgl_22,
        SUM(IF(DAY(tanggal)=23,jumlah,0)) as tgl_23,
        SUM(IF(DAY(tanggal)=24,jumlah,0)) as tgl_24,
        SUM(IF(DAY(tanggal)=25,jumlah,0)) as tgl_25,
        SUM(IF(DAY(tanggal)=26,jumlah,0)) as tgl_26,
        SUM(IF(DAY(tanggal)=27,jumlah,0)) as tgl_27,
        SUM(IF(DAY(tanggal)=28,jumlah,0)) as tgl_28,
        SUM(IF(DAY(tanggal)=29,jumlah,0)) as tgl_29,
        SUM(IF(DAY(tanggal)=30,jumlah,0)) as tgl_30,
        SUM(IF(DAY(tanggal)=31,jumlah,0)) as tgl_31,
        SUM(jumlah) as total
        ");

        $query->join('badstok', 'badstok_detail.no_bs', '=', 'badstok.no_bs');
        $query->join('master_barang', 'badstok_detail.kode_produk', '=', 'master_barang.kode_produk');
        $query->groupByRaw('badstok_detail.kode_produk,nama_barang,badstok.kode_cabang');
        $query->whereBetween('tanggal', [$dari, $sampai]);
        $query->orderBy('badstok.kode_cabang');
        $badstok = $query->get();

        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();

        return view('laporanga.cetak_rekapbadstok', compact('bulan', 'tahun', 'badstok', 'cabang'));
    }
}
