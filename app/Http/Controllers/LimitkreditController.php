<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LimitkreditController extends Controller
{
    public function index(Request $request)
    {
        return view('limitkredit.index');
    }
}
