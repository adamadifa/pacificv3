<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Slipgajicontroller extends Controller
{
    public function index()
    {
        return view('slipgaji.index');
    }

    public function create()
    {

        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('slipgaji.create', compact('namabulan'));
    }
}
