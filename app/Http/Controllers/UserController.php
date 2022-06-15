<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function gantipassword()
    {
        $user = DB::table('users')->where('id', Auth::user()->id)->first();
        return view('user.gantipassword', compact('user'));
    }

    public function editprofile()
    {
        $user = DB::table('users')->where('id', Auth::user()->id)->first();
        return view('user.editprofile', compact('user'));
    }

    public function updateprofile($id, Request $request)
    {
        $id = Crypt::decrypt($id);
        $user = DB::table('users')->where('id', $id)->first();
        $name = $request->name;
        $theme = $request->theme;
        $file = $user->foto;
        $request->validate([
            'name' => 'required',
            'theme' => 'required',
            'foto' => 'mimes:png,jpg,jpeg|max:1024',
        ]);

        if ($request->hasfile('foto')) {
            $foto = $id . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $file;
        }
        $data = [
            'name' => $name,
            'theme' => $theme,
            'foto' => $foto
        ];
        $update = DB::table('users')->where('id', $id)->update($data);
        if ($update) {
            if ($request->hasfile('foto')) {
                Storage::delete('public/users/' . $file);
                $image = $request->file('foto');
                $image_name =  $id . "." . $request->file('foto')->getClientOriginalExtension();
                $destination_path = "/public/users";
                $upload = $request->file('foto')->storeAs($destination_path, $image_name);
            }
            return Redirect::back()->with(['success' => 'Data User Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data User Gagal Diupdate, Hubungi Tim IT']);
        }
    }

    public function update($id_user, Request $request)
    {
        $id = Crypt::decrypt($id_user);
        $user = DB::table('users')->where('id', $id)->first();
        $email = $request->email;
        $password_user = $user->password;
        $password_lama = $request->password_lama;
        $password_baru = $request->password_baru;

        $request->validate([
            'email' => 'required|email',
            'password_lama' => 'required',
            'password_baru' => 'required'
        ]);
        $pas = "12345";
        //dd(Hash::make($pas));
        //dd($password_lama);
        //dd(Hash::check($password_lama, $password_user));
        if (Hash::check($password_lama, $password_user)) {
            $cek_email = DB::table('users')->where('email', $email)->where('id', '!=', $id)->count();
            if ($cek_email > 0) {
                return Redirect::back()->with(['warning' => 'Email Sudah Ada']);
            } else {
                $update = DB::table('users')->where('id', $id)->update(['email' => $email, 'password' => Hash::make($password_baru)]);
                if ($update) {
                    return Redirect::back()->with(['success' => 'Data User Berhasil Diupdate']);
                } else {
                    return Redirect::back()->with(['warning' => 'Data User Gagal Diupdate, Hubungi Tim IT']);
                }
            }
        } else {
            return Redirect::back()->with(['warning' => 'Password Lama Salah, Hubungi Tim IT']);
        }
    }
}
