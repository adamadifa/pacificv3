<?php

namespace App\Http\Controllers;

use App\Models\Pembayaranjmk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PembayaranjmkController extends Controller
{
    public function index(Request $request)
    {
        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();
        $departemen = DB::table('hrd_departemen')->get();
        $query = Pembayaranjmk::query();
        $query->select('hrd_bayarjmk.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'id_kantor');
        $query->join('master_karyawan', 'hrd_bayarjmk.nik', '=', 'master_karyawan.nik');
        $query->leftjoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        if (!empty($request->nama_karyawan_search)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan_search . '%');
        }

        if (!empty($request->id_perusahaan_search)) {
            $query->where('master_karyawan.id_perusahaan', $request->kode_dept_search);
        }


        if (!empty($request->id_kantor_search)) {
            $query->where('master_karyawan.id_kantor', $request->id_kantor_search);
        }

        if (!empty($request->kode_dept_search)) {
            $query->where('master_karyawan.kode_dept', $request->kode_dept);
        }
        $query->orderBy('no_bukti', 'desc');
        $jmk = $query->paginate(15);
        $jmk->appends($request->all());
        return view('pembayaranjmk.index', compact('kantor', 'departemen', 'jmk'));
    }

    public function create()
    {
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        return view('pembayaranjmk.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $tgl_pembayaran = $request->tgl_pembayaran;
        $nik = $request->nik;
        $jumlah = str_replace(".", "", $request->jumlah);
        $tahun = date("Y", strtotime($tgl_pembayaran));
        $thn = substr($tahun, 2, 2);
        $jmk = DB::table("hrd_bayarjmk")
            ->whereRaw('YEAR(tgl_pembayaran)="' . $tahun . '"')
            ->orderBy("no_bukti", "desc")
            ->first();
        $last_nobukti = $jmk != null ? $jmk->no_bukti : '';
        $no_bukti  = buatkode($last_nobukti, "JMK" . $thn, 3);

        $data = [
            'no_bukti' => $no_bukti,
            'tgl_pembayaran' => $tgl_pembayaran,
            'nik' => $nik,
            'jumlah' => $jumlah
        ];
        try {
            DB::table('hrd_bayarjmk')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function delete($no_bukti)
    {
        $no_bukti = Crypt::decrypt($no_bukti);
        try {
            DB::table('hrd_bayarjmk')->where('no_bukti', $no_bukti)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }


    public function edit(Request $request)
    {
        $no_bukti = $request->no_bukti;
        $jmk = DB::table('hrd_bayarjmk')->where('no_bukti', $no_bukti)->first();
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        return view('pembayaranjmk.edit', compact('jmk', 'karyawan'));
    }

    public function update($no_bukti, Request $request)
    {
        $no_bukti = Crypt::decrypt($no_bukti);
        $tgl_pembayaran = $request->tgl_pembayaran;
        $nik = $request->nik;
        $jumlah = str_replace(".", "", $request->jumlah);
        $data = [
            'nik' => $nik,
            'tgl_pembayaran' => $tgl_pembayaran,
            'jumlah' => $jumlah
        ];
        try {
            DB::table('hrd_bayarjmk')->where('no_bukti', $no_bukti)->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }
}
