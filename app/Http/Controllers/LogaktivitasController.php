<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Logaktivitas;
use Illuminate\Http\Request;

class LogaktivitasController extends Controller
{
    public function index(Request $request)
    {

        $query = Logaktivitas::query();
        $query->join('users', 'log_aktivitas.id_user', '=', 'users.id');
        $query->where('id_user', $request->id_user);
        $query->whereRaw('DATE(datetime)="' . $request->tanggal . '"');
        $log = $query->get();
        $cabang = Cabang::orderBy('nama_cabang')->get();
        return view('logaktivitas.index', compact('cabang', 'log'));
    }
}
