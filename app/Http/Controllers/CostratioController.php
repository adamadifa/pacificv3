<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Costratio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostratioController extends Controller
{
    public function index(Request $request)
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $sumber = DB::table('costratio_sumber')->orderBy('id_sumber_costratio')->get();
        $query = Costratio::query();
        $query->join('coa', 'costratio_biaya.kode_akun', '=', 'coa.kode_akun');
        $query->join('costratio_sumber', 'costratio_biaya.id_sumber_costratio', '=', 'costratio_sumber.id_sumber_costratio');
        $query->where('kode_cabang', $request->kode_cabang);

        if (!empty($request->id_sumber_costratio)) {
            $query->where('id_sumber_costratio', $request->id_sumber_costratio);
        }

        $query->whereBetween('tgl_transaksi', [$request->dari, $request->sampai]);

        $costratio = $query->get();
        return view('costratio.index', compact('cabang', 'sumber', 'costratio'));
    }
}
