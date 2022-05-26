<?php

namespace App\Http\Controllers;

use App\Models\Tutuplaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;

class TutuplaporanController extends Controller
{

    public function index(Request $request)
    {
        $bulanskrg = date("m");
        $tahunskrg = date("Y");
        $query = Tutuplaporan::query();
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        } else {
            $query->where('bulan', $bulanskrg);
        }

        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
        } else {
            $query->where('tahun', $tahunskrg);
        }
        $query->orderBy('tahun', 'desc');
        $query->orderBy('bulan', 'asc');
        $tutuplap = $query->get();
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('tutuplaporan.index', compact('bln', 'tutuplap'));
    }

    public function bukalaporan($kode_tutuplaporan)
    {
        $kode_tutuplaporan = Crypt::decrypt($kode_tutuplaporan);
        //dd($kode_tutuplaporan);
        $buka = DB::table('tutup_laporan')->where('kode_tutuplaporan', $kode_tutuplaporan)->update(['status' => 0]);
        if ($buka) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan,Hubungi Tim IT']);
        }
    }

    public function tutuplaporan($kode_tutuplaporan)
    {
        $kode_tutuplaporan = Crypt::decrypt($kode_tutuplaporan);
        //dd($kode_tutuplaporan);
        $tutup = DB::table('tutup_laporan')->where('kode_tutuplaporan', $kode_tutuplaporan)->update(['status' => 1]);
        if ($tutup) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan,Hubungi Tim IT']);
        }
    }

    public function create()
    {
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('tutuplaporan.create', compact('bln'));
    }
    public function cektutuplaporan(Request $request)
    {
        $tanggal = explode("-", $request->tanggal);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $cek = DB::table('tutup_laporan')
            ->where('jenis_laporan', $request->jenislaporan)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 1)
            ->count();
        echo $cek;
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jenis_laporan = $request->jenis_laporan;
        $tgl_penutupan = $request->tgl_penutupan;

        $tutuplap = DB::table('tutup_laporan')->where('bulan', $bulan)->where('tahun', $tahun)->orderBy('kode_tutuplaporan', 'desc')->first();
        $lastkode = $tutuplap != null ? $tutuplap->kode_tutuplaporan : '';
        $kode_tutuplaporan = buatkode($lastkode, $tahun . $bulan, 2);

        $data = [
            'kode_tutuplaporan' => $kode_tutuplaporan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jenis_laporan' => $jenis_laporan,
            'tgl_penutupan' => $tgl_penutupan,
            'status' => 1
        ];
        $cek = DB::table('tutup_laporan')->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('jenis_laporan', $jenis_laporan)
            ->count();
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Sudah Ada']);
        } else {
            $simpan = DB::table('tutup_laporan')->insert($data);
            if ($simpan) {
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
            }
        }
    }
}
