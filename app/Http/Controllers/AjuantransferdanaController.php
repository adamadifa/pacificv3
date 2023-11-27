<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjuantransferdanaController extends Controller
{
    public function index()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabanggudang(Auth::user()->kode_cabang);
        return view('ajuantransferdana.index', compact('cabang'));
    }

    public function create()
    {
        return view('ajuantransferdana.create');
    }
}
