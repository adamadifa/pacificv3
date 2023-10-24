<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Mutasiproduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BpbjController extends Controller
{
    public function index(Request $request)
    {
        $query = Mutasiproduksi::query();
        $query->where('jenis_mutasi', 'BPBJ');
        if (!empty($request->tanggal)) {
            $query->where('tgl_mutasi_produksi', $request->tanggal);
        } else {
            $query->where('tgl_mutasi_produksi', '>=', startreport());
        }
        $query->orderBy('tgl_mutasi_produksi', 'desc');
        $query->orderBy('time_stamp', 'desc');
        $bpbj = $query->paginate(15);
        $bpbj->appends($request->all());

        lockreport($request->tanggal);
        return view('bpbj.index', compact('bpbj'));
    }
    public function show(Request $request)
    {
        $no_mutasi_produksi = Crypt::decrypt($request->no_mutasi_produksi);
        $bpbj = DB::table('mutasi_produksi')->where('no_mutasi_produksi', $no_mutasi_produksi)->first();
        $detail = DB::table('detail_mutasi_produksi')
            ->select('detail_mutasi_produksi.*', 'nama_barang')
            ->join('master_barang', 'detail_mutasi_produksi.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_mutasi_produksi', $no_mutasi_produksi)
            ->orderBy('shift')
            ->get();
        return view('bpbj.show', compact('bpbj', 'detail'));
    }

    public function getbarang()
    {
        $barang = Barang::orderBy('kode_produk')->get();
        return view('bpbj.getbarang', compact('barang'));
    }

    public function delete($no_mutasi_produksi)
    {
        $no_mutasi_produksi = Crypt::decrypt($no_mutasi_produksi);
        $hapus = DB::table('mutasi_produksi')->where('no_mutasi_produksi', $no_mutasi_produksi)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function showtemp($kode_produk)
    {
        $id_admin = Auth::user()->id;
        $detail = DB::table('detail_mutasi_produksi_temp')
            ->select('detail_mutasi_produksi_temp.*', 'nama_barang')
            ->join('master_barang', 'detail_mutasi_produksi_temp.kode_produk', '=', 'master_barang.kode_produk')
            ->where('detail_mutasi_produksi_temp.kode_produk', $kode_produk)
            ->where('id_admin', $id_admin)
            ->where('inout', 'IN')
            ->orderBy('shift')->get();
        return view('bpbj.showtemp', compact('detail'));
    }

    public function buat_nomor_bpbj(Request $request)
    {
        $tgl_mutasi_produksi = $request->tgl_mutasi_produksi;
        $kode_produk = $request->kode_produk;
        $kode = strlen($kode_produk);
        $no_bpbj = $kode + 2;

        $bpbj = DB::table('mutasi_produksi')
            ->selectRaw("LEFT(no_mutasi_produksi,$no_bpbj) as no_bpbj")
            ->whereRaw("LEFT(no_mutasi_produksi," . $kode . ")='" . $kode_produk . "'")
            ->where('tgl_mutasi_produksi', $tgl_mutasi_produksi)
            ->where('jenis_mutasi', 'BPBJ')
            ->orderByRaw("LEFT(no_mutasi_produksi," . $no_bpbj . ") DESC")
            ->first();

        $namabulan = array("", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");
        $tanggal = explode("-", $tgl_mutasi_produksi);
        $hari = $tanggal[2];
        $bulan = $tanggal[1] + 0;
        //echo $bl;
        $tahun = $tanggal[0];
        $tgl = "/" . $hari . "/" . $namabulan[$bulan] . "/" . $tahun;
        if ($bpbj != null) {
            $last_nobpbj = $bpbj->no_bpbj;
        } else {
            $last_nobpbj = "";
        }
        $no_bpbj = buatkode($last_nobpbj, $kode_produk, 2) . $tgl;
        echo $no_bpbj;
    }

    public function storetemp(Request $request)
    {
        $kode_produk = $request->kode_produk;
        $shift = $request->shift;
        $jumlah = !empty($request->jumlah) ? str_replace(".", "", $request->jumlah) : 0;
        $id_admin = Auth::user()->id;

        $cek = DB::table('detail_mutasi_produksi_temp')->where('kode_produk', $kode_produk)->where('shift', $shift)
            ->where('inout', 'IN')
            ->where('id_admin', $id_admin)->count();
        $data = [
            'kode_produk' => $kode_produk,
            'jumlah' => $jumlah,
            'shift' => $shift,
            'inout' => 'IN',
            'id_admin' => $id_admin
        ];


        if ($cek > 0) {
            echo 1;
        } else {
            $simpan = DB::table('detail_mutasi_produksi_temp')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function cekbpbjtemp(Request $request)
    {
        $kode_produk = $request->kode_produk;
        $id_admin = Auth::user()->id;
        $cek = DB::table('detail_mutasi_produksi_temp')->where('kode_produk', $kode_produk)->where('id_admin', $id_admin)->where('inout', 'IN')->count();
        echo $cek;
    }

    public function deletetemp(Request $request)
    {
        $kode_produk = $request->kode_produk;
        $shift = $request->shift;
        $id_admin = Auth::user()->id;
        $hapus = DB::table('detail_mutasi_produksi_temp')->where('kode_produk', $kode_produk)->where('id_admin', $id_admin)->where('shift', $shift)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function store(Request $request)
    {
        $no_mutasi_produksi = $request->no_bpbj;
        $tgl_mutasi_produksi = $request->tgl_mutasi_produksi;
        $kode_produk = $request->kode_produk;
        $id_admin = Auth::user()->id;

        $detailtemp = DB::table('detail_mutasi_produksi_temp')->where('kode_produk', $kode_produk)->where('inout', 'IN')->where('id_admin', $id_admin)->get();
        DB::beginTransaction();
        try {
            $data = [
                'no_mutasi_produksi' => $no_mutasi_produksi,
                'tgl_mutasi_produksi' => $tgl_mutasi_produksi,
                'inout' => 'IN',
                'id_admin' => $id_admin,
                'jenis_mutasi' => 'BPBJ'
            ];

            DB::table('mutasi_produksi')->insert($data);
            foreach ($detailtemp as $d) {
                $datadetail = [
                    'no_mutasi_produksi' => $no_mutasi_produksi,
                    'kode_produk' => $d->kode_produk,
                    'shift' => $d->shift,
                    'jumlah' => $d->jumlah
                ];

                DB::table('detail_mutasi_produksi')->insert($datadetail);
            }

            DB::table('detail_mutasi_produksi_temp')->where('kode_produk', $kode_produk)->where('inout', 'IN')->where('id_admin', $id_admin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubunti Tim IT']);
        }
    }
}
