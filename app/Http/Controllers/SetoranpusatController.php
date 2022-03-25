<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Cabang;
use App\Models\Setoranpusat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SetoranpusatController extends Controller
{
    public function index(Request $request)
    {
        $query = Setoranpusat::query();
        $query->select('setoran_pusat.*', 'nama_bank');
        $query->join('master_bank', 'setoran_pusat.bank', '=', 'master_bank.kode_bank');
        $query->whereBetween('tgl_setoranpusat', [$request->dari, $request->sampai]);
        if (!empty($request->kode_bank)) {
            $query->where('bank', $request->kode_bank);
        }

        if (!empty($request->kode_cabang)) {
            $query->where('setoran_pusat.kode_cabang', $request->kode_cabang);
        }
        $query->orderBy('tgl_setoranpusat');
        $query->orderBy('kode_setoranpusat');
        $setoranpusat = $query->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bank = Bank::where('show_on_cabang', 1)->get();
        return view('setoranpusat.index', compact('cabang', 'bank', 'setoranpusat'));
    }

    public function create()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bank = Bank::where('show_on_cabang', 1)->get();
        return view('setoranpusat.create', compact('bank', 'cabang'));
    }

    public function store(Request $request)
    {
        $tgl_setoranpusat = $request->tgl_setoranpusat;
        $kode_cabang = $request->kode_cabang;
        $kode_bank = $request->kode_bank;
        $uang_kertas = !empty($request->uang_kertas) ? str_replace(".", "", $request->uang_kertas) : 0;
        $uang_logam = !empty($request->uang_logam) ? str_replace(".", "", $request->uang_logam) : 0;
        $keterangan = $request->keterangan;
        $tanggal = explode("-", $tgl_setoranpusat);
        $hari = $tanggal[2];
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $tahunini = date("y");
        $setoranpusat = DB::table('setoran_pusat')
            ->select('kode_setoranpusat')
            ->whereRaw('LEFT(kode_setoranpusat,4)="SB' . $tahunini . '"')
            ->orderBy('kode_setoranpusat', 'desc')
            ->first();
        $last_kode_setoranpusat = $setoranpusat->kode_setoranpusat;
        $kode_setoranpusat   = buatkode($last_kode_setoranpusat, 'SB' . $tahunini, 5);
        $data = [
            'kode_setoranpusat' => $kode_setoranpusat,
            'tgl_setoranpusat'  => $tgl_setoranpusat,
            'kode_cabang' => $kode_cabang,
            'bank' => $kode_bank,
            'uang_kertas' => $uang_kertas,
            'uang_logam' => $uang_logam,
            'keterangan' => $keterangan,
            'status' => '0'
        ];
        if ($bulan == 12) {
            $bulan = 1;
            $tahun = $tahun + 1;
        } else {
            $bulan = $bulan + 1;
            $tahun = $tahun;
        }
        $ceksaldo = DB::table('saldoawal_kasbesar')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_cabang', $kode_cabang)->count();
        if (empty($ceksaldo)) {
            $simpan = DB::table('setoran_pusat')->insert($data);
            if ($simpan) {
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);
            }
        } else {
            return Redirect::back()->with(['warning' => 'Periode Laporan Sudah Ditutup']);
        }
    }

    public function delete($kode_setoranpusat)
    {
        $kode_setoranpusat = Crypt::decrypt($kode_setoranpusat);
        $hapus = DB::table('setoran_pusat')->where('kode_setoranpusat', $kode_setoranpusat)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus Hubungi Tim IT']);
        }
    }

    public function edit($kode_setoranpusat)
    {
        $setoranpusat = DB::table('setoran_pusat')->where('kode_setoranpusat', $kode_setoranpusat)->first();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bank = Bank::where('show_on_cabang', 1)->get();
        return view('setoranpusat.edit', compact('bank', 'cabang', 'setoranpusat'));
    }

    public function update($kode_setoranpusat, Request $request)
    {
        $kode_setoranpusat = Crypt::decrypt($kode_setoranpusat);
        $uang_kertas = !empty($request->uang_kertas) ? str_replace(".", "", $request->uang_kertas) : 0;
        $uang_logam = !empty($request->uang_logam) ? str_replace(".", "", $request->uang_logam) : 0;
        $setoranpusat = DB::table('setoran_pusat')->where('kode_setoranpusat', $kode_setoranpusat)->first();
        $status = $setoranpusat->status;
        if ($status == 0) {
            $data = [
                'tgl_setoranpusat' => $request->tgl_setoranpusat,
                'kode_cabang' => $request->kode_cabang,
                'bank' => $request->kode_bank,
                'uang_kertas' => $uang_kertas,
                'uang_logam' => $uang_logam,
                'keterangan' => $request->keterangan
            ];
        } else {
            $data = [
                'tgl_setoranpusat' => $request->tgl_setoranpusat,
                'keterangan' => $request->keterangan
            ];
        }

        $update = DB::table('setoran_pusat')->where('kode_setoranpusat', $kode_setoranpusat)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate Hubungi Tim IT']);
        }
    }
}
