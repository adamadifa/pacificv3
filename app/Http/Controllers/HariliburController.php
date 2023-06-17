<?php

namespace App\Http\Controllers;

use App\Models\Harilibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\FuncCall;

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
        $harilibur->appends($request->all());

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('harilibur.index', compact('harilibur', 'bulan', 'cabang'));
    }

    public function store(Request $request)
    {

        $tanggal = $request->tanggal;
        $keterangan = $request->keterangan;
        $id_kantor = $request->id_kantor;
        $tahun = substr(date('Y', strtotime($tanggal)), 2, 2);
        $harilibur = DB::table('harilibur')->whereRaw('MID(kode_libur,3,2)="' . $tahun . '"')
            ->orderBy('kode_libur', 'desc')->first();
        $last_kodelibur = $harilibur != null ? $harilibur->kode_libur : '';
        $kode_libur = buatkode($last_kodelibur, "LB" . $tahun, 3);

        $beforeday = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
        $data = [
            'kode_libur' => $kode_libur,
            'tanggal_libur' => $tanggal,
            'id_kantor' => $id_kantor,
            'keterangan' => $keterangan,
            'tanggal_limajam' => $beforeday
        ];
        try {
            $cek = DB::table('harilibur')->where('tanggal_libur', $tanggal)->count();
            if ($cek > 0) {
                return Redirect::back()->with(['warning' => 'Tanggal Libur Sudah Diinputkan Sebelumnya']);
            }
            DB::table('harilibur')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function delete($kode_libur)
    {
        $kode_libur = Crypt::decrypt($kode_libur);
        try {
            DB::table('harilibur')->where('kode_libur', $kode_libur)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function edit($kode_libur)
    {
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $harilibur = DB::table('harilibur')->where('kode_libur', $kode_libur)->first();
        return view('harilibur.edit', compact('harilibur', 'cabang'));
    }

    public function update($kode_libur, Request $request)
    {
        $kode_libur = Crypt::decrypt($kode_libur);
        $tanggal = $request->tanggal;
        $keterangan = $request->keterangan;
        $id_kantor = $request->id_kantor;
        $beforeday = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
        $data = [
            'tanggal_libur' => $tanggal,
            'keterangan' => $keterangan,
            'id_kantor' => $id_kantor,
            'tanggal_limajam' => $beforeday
        ];
        try {
            $cek = DB::table('harilibur')->where('tanggal_libur', $tanggal)->where('kode_libur', '!=', $kode_libur)->count();
            if ($cek > 0) {
                return Redirect::back()->with(['warning' => 'Tanggal Libur Sudah Diinputkan Sebelumnya']);
            }
            DB::table('harilibur')
                ->where('kode_libur', $kode_libur)
                ->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }
}
