<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
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
}
