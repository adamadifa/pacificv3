<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LemburController extends Controller
{
    public function index(Request $request)
    {
        $query = Lembur::query();
        if (!empty($request->bulan)) {
            $query->whereRaw('MONTH(tanggal_dari)="' . $request->bulan . '"');
        }

        if (!empty($request->tahun)) {
            $query->whereRaw('YEAR(tanggal_sampai)="' . $request->tahun . '"');
        }



        if (Auth::user()->kode_cabang != "PCF" && Auth::user()->kode_cabang != "PST") {
            $query->where('id_kantor', Auth::user()->kode_cabang);
        } else {
            $level_search = array("manager hrd", "admin");
            if (in_array(Auth::user()->level, $level_search)) {
                if (!empty($request->id_kantor_search)) {
                    $query->where('id_kantor', $request->id_kantor_search);
                }
            } else {
                $query->where('id_kantor', 'PST');
                $query->where('kode_dept', Auth::user()->kode_dept_presensi);
            }
        }

        $query->orderBy('tanggal_dari', 'desc');
        $lembur = $query->paginate(15);
        $lembur->appends($request->all());

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

        $cbg = new Cabang();
        if (Auth::user()->kode_cabang != "PCF") {
            $cabang = $cbg->getCabangpresensi(Auth::user()->kode_cabang);
        } else {
            $cabang = $cbg->getCabangpresensi("PST");
        }

        $departemen = DB::table('hrd_departemen')->orderBy('kode_dept')->get();
        return view('lembur.index', compact('cabang', 'departemen', 'lembur'));
    }

    public function store(Request $request)
    {

        $tanggal_dari = $request->tanggal_dari;
        $tanggal_sampai = $request->tanggal_sampai;
        $jam_dari = $request->jam_dari;
        $jam_sampai = $request->jam_sampai;

        $dari = $tanggal_dari . " " . $jam_dari;
        $sampai = $tanggal_sampai . " " . $jam_sampai;
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $keterangan = $request->keterangan;
        $tahun = substr(date('Y', strtotime($tanggal_dari)), 2, 2);
        $lembur = DB::table('lembur')->whereRaw('MID(kode_lembur,3,2)="' . $tahun . '"')
            ->orderBy('kode_lembur', 'desc')->first();
        $last_kodelembur = $lembur != null ? $lembur->kode_lembur : '';
        $kode_lembur = buatkode($last_kodelembur, "LM" . $tahun, 3);


        $data = [
            'kode_lembur' => $kode_lembur,
            'tanggal_dari' => $dari,
            'tanggal_sampai' => $sampai,
            'id_kantor' => $id_kantor,
            'kode_dept' => $kode_dept,
            'keterangan' => $keterangan,
        ];
        try {

            // if (Auth::user()->kode_cabang != "PCF" && Auth::user()->kode_cabang != "PST") {
            //     $cek = DB::table('lembur')->whereRaw('dari="' . $dari . '"')
            //         ->where('id_kantor', $id_kantor)
            //         ->count();
            // } else {
            //     $cek = DB::table('lembur')->whereRaw('dari="' . $dari . '"')
            //         ->where('kode_dept', $kode_dept)
            //         ->where('id_kantor', $id_kantor)
            //         ->count();
            // }

            // if ($cek > 0) {
            //     return Redirect::back()->with(['warning' => 'Tanggal Lembur Sudah Diinputkan Sebelumnya']);
            // }
            DB::table('lembur')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function delete($kode_lembur)
    {
        $kode_lembur = Crypt::decrypt($kode_lembur);
        try {
            DB::table('lembur')->where('kode_lembur', $kode_lembur)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}
