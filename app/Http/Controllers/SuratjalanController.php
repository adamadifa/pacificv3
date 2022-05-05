<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuratjalanController extends Controller
{
    public function create()
    {
        return view('suratjalan.create');
    }
}