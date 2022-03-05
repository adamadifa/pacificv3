<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Permintaanpengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermintaanpengirimanController extends Controller
{
    public function index(Request $request)
    {
        $query = Permintaanpengiriman::query();
        $query->select('no_permintaan_pengiriman', 'tgl_permintaan_pengiriman', 'permintaan_pengiriman.kode_cabang', 'keterangan', 'status', 'nama_karyawan');
        if (!empty($request->tanggal)) {
            $query->where('tgl_permintaan_pengiriman', $request->tanggal);
        }

        if (!empty($request->status) || $request->status === '0') {
            $query->where('status', $request->status);
        }
        $query->leftJoin('karyawan', 'permintaan_pengiriman.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->orderBy('status', 'asc');
        $query->orderBy('no_permintaan_pengiriman', 'desc');
        $pp = $query->paginate(15);
        $pp->appends($request->all());

        $cabang = Cabang::all();
        return view('permintaanpengiriman.index', compact('pp', 'cabang'));
    }

    public function cektemp()
    {
        $cektemp = DB::table('detail_permintaan_pengiriman_temp')->count();
        echo $cektemp;
    }
}
