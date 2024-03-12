<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Logaktivitas;
use Illuminate\Http\Request;

class LogaktivitasController extends Controller
{
    public function index(Request $request)
    {

        $dari = !empty($request->dari) ? $request->dari : date('Y-m-d');
        $sampai = !empty($request->sampai) ? $request->sampai : date('Y-m-d');
        $query = Logaktivitas::query();
        $query->join('users', 'log_aktivitas.id_user', '=', 'users.id');
        $query->where('id_user', $request->id_user);
        $query->whereRaw('DATE(datetime)>="' . $dari . '"');
        $query->whereRaw('DATE(datetime)<="' . $sampai . '"');
        $log = $query->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        return view('logaktivitas.index', compact('cabang', 'log'));
    }
}
