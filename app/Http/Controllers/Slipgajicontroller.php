<?php

namespace App\Http\Controllers;

use App\Models\Slipgaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class Slipgajicontroller extends Controller
{
    public function index()
    {
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $query = Slipgaji::query();
        $query->orderBy('bulan');
        $slipgaji  = $query->get();
        return view('slipgaji.index', compact('slipgaji', 'namabulan'));
    }

    public function create()
    {
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

        return view('slipgaji.create', compact('namabulan'));
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_gaji = 'GJ' . $bulan . $tahun;

        if ($bulan == 12) {
            $bulan_cair = 1;
            $tahun_cair = $tahun + 1;
        } else {
            $bulan_cair = $bulan + 1;
            $tahun_cair = $tahun;
        }

        $tanggal_cair = $tahun_cair . "-" . $bulan_cair . "-01";
        try {
            $cek = DB::table('slip_gaji')->where('bulan', $bulan)->where('tahun', $tahun)->count();
            if ($cek > 0) {
                return Redirect::back()->with(['warning' => 'Data Sudah Ada']);
            }
            DB::table('slip_gaji')->insert([
                'kode_gaji' => $kode_gaji,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'status' => $request->status,
                'tanggal' => $tanggal_cair
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }


    public function edit($kode_gaji)
    {
        $kode_gaji = Crypt::decrypt($kode_gaji);
        $slipgaji = DB::table('slip_gaji')->where('kode_gaji', $kode_gaji)->first();
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('slipgaji.edit', compact('slipgaji', 'namabulan'));
    }


    public function update(Request $request, $kode_gaji)
    {
        $kode_gaji = Crypt::decrypt($kode_gaji);
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        if ($bulan == 12) {
            $bulan_cair = 1;
            $tahun_cair = $tahun + 1;
        } else {
            $bulan_cair = $bulan + 1;
            $tahun_cair = $tahun;
        }

        $tanggal_cair = $tahun_cair . "-" . $bulan_cair . "-01";
        try {

            DB::table('slip_gaji')->where('kode_gaji', $kode_gaji)->update([
                'kode_gaji' => $kode_gaji,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'status' => $request->status,
                'tanggal' => $tanggal_cair
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }
    public function setpenambahpengurang($kode_gaji)
    {
        $kode_gaji = Crypt::decrypt($kode_gaji);
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $slipgaji = DB::table('slip_gaji')->where('kode_gaji', $kode_gaji)->first();
        $tambahkurang = DB::table('pengurang_gaji')->where('kode_gaji', $kode_gaji)
            ->join('master_karyawan', 'pengurang_gaji.nik', '=', 'master_karyawan.nik')
            ->orderBy('nama_karyawan')->get();
        return view('slipgaji.setpenambahpengurang', compact('slipgaji', 'namabulan', 'tambahkurang'));
    }

    public function tambahkaryawan($kode_gaji)
    {
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();

        return view('slipgaji.tambahkaryawan', compact('karyawan', 'kode_gaji'));
    }

    public function storepenambahpengurang(Request $request, $kode_gaji)
    {
        $kode_gaji = Crypt::decrypt($kode_gaji);
        try {
            $cek = DB::table('pengurang_gaji')->where('kode_gaji', $kode_gaji)->where('nik', $request->nik)->count();
            if ($cek > 0) {
                return Redirect::back()->with(['warning' => 'Data Sudah Ada']);
            }
            DB::table('pengurang_gaji')->insert([
                'kode_gaji' => $kode_gaji,
                'nik' => $request->nik,
                'jumlah' => str_replace(".", "", $request->pengurang),
                'jumlah_penambah' => str_replace(".", "", $request->penambah),
                'keterangan' => $request->keterangan
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }
    public function deletepenambahpengurang($kode_gaji, $nik)
    {
        $kode_gaji = Crypt::decrypt($kode_gaji);
        $nik = Crypt::decrypt($nik);

        try {
            DB::table('pengurang_gaji')->where('kode_gaji', $kode_gaji)->where('nik', $nik)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }


    public function delete($kode_gaji)
    {
        $kode_gaji = Crypt::decrypt($kode_gaji);


        try {
            DB::table('slip_gaji')->where('kode_gaji', $kode_gaji)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }
}
