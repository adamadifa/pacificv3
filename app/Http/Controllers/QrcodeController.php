<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class QrcodeController extends Controller
{
    public function index()
    {
        Cookie::queue(Cookie::forget('kodepelanggan'));
        return view('scan.index');
    }
}
