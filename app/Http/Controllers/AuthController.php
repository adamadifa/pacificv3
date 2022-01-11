<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            return redirect()->intended('/dashboardadmin');
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
