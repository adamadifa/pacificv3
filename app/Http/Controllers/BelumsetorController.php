<?php

namespace App\Http\Controllers;

use App\Models\Belumsetor;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BelumsetorController extends Controller
{
    public function index(Request $request)
    {
        $query = Belumsetor::query();
        $query->where('tahun', $request->tahun);
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }
        $query->orderBy('kode_cabang');
        $query->orderBy('bulan');
        $belumsetor = $query->get();


        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('belumsetor.index', compact('cabang', 'bulan', 'belumsetor'));
    }

    public function create()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('belumsetor.create', compact('cabang', 'bulan'));
    }

    public function show($kode_saldobs)
    {
        $belumsetor = DB::table('belumsetor')->where('kode_saldobs', $kode_saldobs)->first();
        $detail = DB::table('belumsetor_detail')
            ->select('belumsetor_detail.*', 'nama_karyawan')
            ->join('karyawan', 'belumsetor_detail.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('kode_saldobs', $kode_saldobs)
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('belumsetor.show', compact('detail', 'belumsetor', 'bulan'));
    }

    public function delete($kode_saldobs)
    {
        $kode_saldobs = Crypt::encrypt($kode_saldobs);
        $hapus = DB::table('belumsetor')->where('kode_saldobs', $kode_saldobs)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function showtemp($kode_cabang, $bulan, $tahun)
    {
        // echo 'test';
        // die;
        $detailtemp = DB::table('belumsetor_temp')
            ->select('belumsetor_temp.*', 'nama_karyawan')
            ->join('karyawan', 'belumsetor_temp.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
        return view('belumsetor.showtemp', compact('detailtemp'));
    }
}
