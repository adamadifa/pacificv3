<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class LpcController extends Controller
{
    protected $cabang;
    public function __construct()
    {
        // Fetch the Site Settings object
        $this->middleware(function ($request, $next) {
            $this->cabang = Auth::user()->kode_cabang;
            return $next($request);
        });


        View::share('cabang', $this->cabang);
    }
    public function index()
    {
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('lpc.index', compact('bln'));
    }

    public function show(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $lpc = DB::table('lpc')->where('bulan', $bulan)->where('tahun', $tahun)->get();
        return view('lpc.show', compact('lpc', 'bln'));
    }

    public function create()
    {
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->get();
        }
        return view('lpc.create', compact('cabang', 'bln'));
    }

    public function store(Request $request)
    {

        $cek = DB::table('lpc')
            ->where('kode_cabang', $request->kode_cabang)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->count();
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Sudah Ada']);
        } else {
            try {
                if ($request->hasfile('foto')) {
                    $image = $request->file('foto');
                    $image_name =  $request->kode_cabang . $request->bulan . $request->tahun . "." . $request->file('foto')->getClientOriginalExtension();
                    $foto = $image_name;
                } else {
                    $foto = NULL;
                }
                DB::table('lpc')
                    ->insert([
                        'kode_lpc' => $request->kode_cabang . $request->bulan . $request->tahun,
                        'kode_cabang' => $request->kode_cabang,
                        'bulan' => $request->bulan,
                        'tahun' => $request->tahun,
                        'tgl_lpc' => $request->tgl_lpc,
                        'jam_lpc' => $request->jam_lpc,
                        'foto' => $foto
                    ]);

                $destination_path = "/public/lpc";
                $request->file('foto')->storeAs($destination_path, $image_name);
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                return Redirect::back()->with(['warning' => 'Data Gagak Disimpan']);
            }
        }
    }

    public function delete(Request $request)
    {
        $hapus = DB::table('lpc')->where('kode_lpc', $request->kode_lpc)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function edit(Request $request)
    {
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = DB::table('cabang')->get();
        $lpc = DB::table('lpc')->where('kode_lpc', $request->kode_lpc)->first();
        return view('lpc.edit', compact('lpc', 'bln', 'cabang'));
    }

    public function update(Request $request)
    {
        $update = DB::table('lpc')
            ->where('kode_lpc', $request->kode_lpc)
            ->update([
                'tgl_lpc' => $request->tgl_lpc,
                'jam_lpc' => $request->jam_lpc
            ]);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function approve(Request $request)
    {
        $update = DB::table('lpc')
            ->where('kode_lpc', $request->kode_lpc)
            ->update([
                'status' => 1
            ]);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function cancel(Request $request)
    {
        $update = DB::table('lpc')
            ->where('kode_lpc', $request->kode_lpc)
            ->update([
                'status' => 0
            ]);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }
}
