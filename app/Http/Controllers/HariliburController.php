<?php

namespace App\Http\Controllers;

use App\Models\Harilibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class HariliburController extends Controller
{
    public function index(Request $request)
    {

        $query = Harilibur::query();
        if (!empty($request->bulan)) {
            $query->whereRaw('MONTH(tanggal_libur)="' . $request->bulan . '"');
        }

        if (!empty($request->tahun)) {
            $query->whereRaw('YEAR(tanggal_libur)="' . $request->tahun . '"');
        }

        $harilibur = $query->paginate(15);
        return view('harilibur.index', compact('harilibur'));
    }

    public function store(Request $request)
    {

        $tanggal = $request->tanggal;
        $keterangan = $request->keterangan;

        $tahun = substr(date('Y', strtotime($tanggal)), 2, 2);
        $harilibur = DB::table('harilibur')->whereRaw('MID(kode_libur,3,2)="' . $tahun . '"')
            ->orderBy('kode_libur', 'desc')->first();
        $last_kodelibur = $harilibur != null ? $harilibur->kode_libur : '';
        $kode_libur = buatkode($last_kodelibur, "LB" . $tahun, 3);

        $beforeday = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
        $data = [
            'kode_libur' => $kode_libur,
            'tanggal_libur' => $tanggal,
            'keterangan' => $keterangan,
            'tanggal_limajam' => $beforeday
        ];
        try {
            DB::table('harilibur')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }
}
