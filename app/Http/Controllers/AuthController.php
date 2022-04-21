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
            //dd(Auth::user()->kode_cabang);
            if (
                Auth::user()->level == "admin"
                || Auth::user()->level == "manager marketing"
                || Auth::user()->level == "general manager"
                || Auth::user()->level == "direktur"
            ) {
                return redirect()->intended('/dashboardadmin');
            } else if (Auth::user()->level == "admin penjualan") {
                return redirect()->intended('/dashboardadminpenjualan');
            } else if (Auth::user()->level == "kepala penjualan") {
                return redirect()->intended('/dashboardkepalapenjualan');
            } else if (Auth::user()->level == "kepala admin") {
                return redirect()->intended('/dashboardkepalaadmin');
            } else if (Auth::user()->level == "manager accounting") {
                return redirect()->intended('/dashboardaccounting');
            } else if (Auth::user()->level == "staff keuangan") {
                return redirect()->intended('/dashboardstaffkeuangan');
            } else if (Auth::user()->level == "admin kas kecil") {
                return redirect()->intended('/dashboardadminkaskecil');
            } else if (Auth::user()->level == "kasir") {
                return redirect()->intended('/dashboardkasir');
            } else if (Auth::user()->level == "manager pembelian" || Auth::user()->level == "admin pembelian") {
                return redirect()->intended('/dashboardpembelian');
            }
        } else {
            echo "Username Atau Password Salah";
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
