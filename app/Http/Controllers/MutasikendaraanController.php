<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Kendaraan;
use App\Models\Mutasikendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class MutasikendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Mutasikendaraan::query();
        if (isset($request->no_polisi)) {
            $query->where('no_polisi', $request->no_polisi);
        }
        $query->selectRaw('no_mutasi,kendaraan_mutasi.no_polisi,merk,tipe_kendaraan,tipe,kode_cabang_old,kode_cabang_new,tgl_mutasi');
        $query->join('kendaraan', 'kendaraan_mutasi.no_polisi', '=', 'kendaraan.no_polisi');
        $mutasikendaraan = $query->paginate(15);
        $mutasikendaraan->appends($request->all());
        $kendaraan = DB::table('kendaraan')->orderBy('no_polisi')->get();
        return view('mutasikendaraan.index', compact('mutasikendaraan', 'kendaraan'));
    }

    public function create()
    {
        $kendaraan = DB::table('kendaraan')->orderBy('no_polisi')->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('mutasikendaraan.create', compact('kendaraan', 'cabang'));
    }

    public function store(Request $request)
    {
        $no_polisi = $request->no_polisi;
        $kode_cabang = $request->kode_cabang;
        $kendaraan = DB::table('kendaraan')->where('no_polisi', $no_polisi)->first();
        $kode_cabang_old = $kendaraan->kode_cabang;
        $tgl_mutasi = $request->tgl_mutasi;
        $tgl = explode("-", $tgl_mutasi);
        $bulan = $tgl[1];
        $tahun = substr($tgl[0], 2);
        $mutasikendaraan = DB::table("kendaraan_mutasi")
            ->whereRaw('MONTH(tgl_mutasi)=' . $bulan)
            ->whereRaw('YEAR(tgl_mutasi)=' . $tgl[0])
            ->orderBy("no_mutasi", "desc")
            ->first();

        $lastnomutasi = $mutasikendaraan != null ? $mutasikendaraan->no_mutasi : '';

        $no_mutasi  = buatkode($lastnomutasi, "M" . $bulan . $tahun, 2);
        $data = [
            'no_mutasi' => $no_mutasi,
            'tgl_mutasi' => $tgl_mutasi,
            'no_polisi' => $no_polisi,
            'kode_cabang_old' => $kode_cabang_old,
            'kode_cabang_new' => $kode_cabang
        ];
        if ($kode_cabang == $kode_cabang_old) {
            return Redirect::back()->with(['warning' => 'Kode Cabang Sebelumnya Tidak Boleh Sama']);
        } else {
            DB::beginTransaction();
            try {
                DB::table('kendaraan_mutasi')->insert($data);
                DB::table('kendaraan')->where('no_polisi', $no_polisi)->update(['kode_cabang' => $kode_cabang]);
                DB::commit();
                return redirect('/mutasikendaraan')->with(['success' => 'Data Berhasil Di Mutasi']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return redirect('/mutasikendaraan')->with(['warning' => 'Data Gagal Di Mutasi']);
            }
        }
    }

    public function delete($no_mutasi)
    {
        $no_mutasi = Crypt::decrypt($no_mutasi);
        $mutasikendaraan = DB::table('kendaraan_mutasi')->where('no_mutasi', $no_mutasi)->first();
        $tgl_mutasi = $mutasikendaraan->tgl_mutasi;
        $no_polisi = $mutasikendaraan->no_polisi;
        $kode_cabang_old = $mutasikendaraan->kode_cabang_old;
        $cek = DB::table('kendaraan_mutasi')->where('no_polisi', $no_polisi)
            ->where('tgl_mutasi', '>', $tgl_mutasi)
            ->count();
        if ($cek > 0) {
            return redirect('/mutasikendraan')->with(['warning' => 'Data Sudah Dikunci, Silahkan Hubungi Administrator']);
        } else {
            DB::beginTransaction();
            try {
                DB::table('kendaraan')->where('no_polisi', $no_polisi)->update(['kode_cabang' => $kode_cabang_old]);
                DB::table('kendaraan_mutasi')->where('no_mutasi', $no_mutasi)->delete();
                DB::commit();
                return redirect('/mutasikendaraan')->with(['success' => 'Data Berhasil Di hapus']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return redirect('/mutasikendaraan')->with(['warning' => 'Data Gagal Di hapus']);
            }
        }
    }
}
