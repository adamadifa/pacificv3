<?php

namespace App\Http\Controllers;

use App\Models\Pengajuanizin;
use Illuminate\Http\Request;

class PengajuanizinController extends Controller
{
    public function index()
    {
        $query = Pengajuanizin::query();
        $query->selectRaw('pengajuan_izin.*', 'nama_karyawan', '');
        return view('pengajuanizin.index');
    }
}
