<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    public function postlogin(Request $request)
    {
        //dd($request->all());
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        //dd(Auth::attempt(['username' => $request->username, 'password' => $request->password]));
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            //dd(Auth::user()->kode_cabang);
            return redirect()->intended('/home');
        } else {
            return Redirect::back()->with(['warning' => 'Username / Password Salah']);
        }
    }

    public function postlogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
