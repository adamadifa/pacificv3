<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatiokomisiController extends Controller
{
    public function index()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('ratiokomisi.index', compact('cabang', 'bulan'));
    }

    public function getratiokomisi(Request $request)
    {
        $ratiokomisi = DB::table('driver_helper')
            ->selectRaw('id_driver_helper,nama_driver_helper,kategori,driver_helper.ratio as ratio_default,ratioaktif,ratioterakhir')
            ->leftJoin(
                DB::raw("(
                SELECT id,set_ratio_komisi.ratio as ratioaktif
                FROM set_ratio_komisi
                INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                WHERE bulan = '$request->bulan' AND tahun = '$request->tahun' AND kode_cabang='$request->kode_cabang'
            ) ratio"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'ratio.id');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT id,set_ratio_komisi.ratio as ratioterakhir
                FROM set_ratio_komisi
                INNER JOIN driver_helper ON set_ratio_komisi.id = driver_helper.id_driver_helper
                WHERE kode_cabang ='$request->kode_cabang' AND tgl_berlaku IN (SELECT max(tgl_berlaku) FROM set_ratio_komisi)
            ) lastratio"),
                function ($join) {
                    $join->on('driver_helper.id_driver_helper', '=', 'lastratio.id');
                }
            )

            ->where('kode_cabang', $request->kode_cabang)
            ->get();

        return view('ratiokomisi.getratiokomisi', compact('ratiokomisi'));
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $ratio = $request->ratio;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $tgl_berlaku = $tahun . "-" . $bulan . "-01";

        $data = [
            'id' => $id,
            'tgl_berlaku' => $tgl_berlaku,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'ratio' => $ratio
        ];

        $dataupdate = [
            'ratio' => $ratio
        ];
        $cek = DB::table('set_ratio_komisi')->where('id', $id)->where('bulan', $bulan)->where('tahun', $tahun)->count();
        if (empty($cek)) {
            DB::table('set_ratio_komisi')->insert($data);
        } else {
            DB::table('set_ratio_komisi')->where('id', $id)->where('bulan', $bulan)->where('tahun', $tahun)->update($dataupdate);
        }
    }
}
