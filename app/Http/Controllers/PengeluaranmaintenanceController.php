<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaranmaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PengeluaranmaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengeluaranmaintenance::query();
        if (!empty($request->nobukti_pengeluaran)) {
            $query->where('nobukti_pengeluaran', $request->nobukti_pengeluaran);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pengeluaran', [$request->dari, $request->sampai]);
        }

        if (!empty($request->kode_dept)) {
            $query->where('kode_dept', $request->kode_dept);
        }
        $query->orderBy('tgl_pengeluaran', 'desc');
        $query->orderBy('nobukti_pengeluaran', 'desc');
        $pengeluaranmtc = $query->paginate(15);
        $pengeluaranmtc->appends($request->all());

        $cabang = ['BDG', 'BGR', 'GRT', 'PWT', 'SMR', 'SKB', 'SBY', 'TSM'];
        $departemen = DB::table('departemen')
            ->whereNotIn('kode_dept', $cabang)
            ->orderBy('nama_dept')->get();
        return view('pengeluaranmtc.index', compact('pengeluaranmtc', 'departemen'));
    }

    public function show(Request $request)
    {
        $nobukti_pengeluaran = Crypt::decrypt($request->nobukti_pengeluaran);
        $pengeluaranmtc = DB::table('pengeluaran_bb')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->first();
        $detail = DB::table('detail_pengeluaran_bb')
            ->select('detail_pengeluaran_bb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pengeluaran_bb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)->get();
        return view('pengeluaranmtc.show', compact('detail', 'pengeluaranmtc'));
    }

    public function delete($nobukti_pengeluaran)
    {
        $nobukti_pengeluaran  = Crypt::decrypt($nobukti_pengeluaran);
        $hapus = DB::table('pengeluaran_bb')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        $cabang = ['BDG', 'BGR', 'GRT', 'PWT', 'SMR', 'SKB', 'SBY', 'TSM'];
        $departemen = DB::table('departemen')
            ->whereNotIn('kode_dept', $cabang)
            ->orderBy('nama_dept')->get();
        return view('pengeluaranmtc.create', compact('departemen'));
    }

    public function cektemp()
    {
        $id_admin = Auth::user()->id;
        $cek = DB::table('detailpengeluaran_temp_bb')->where('id_admin', $id_admin)->count();
        echo $cek;
    }

    public function showtemp()
    {
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpengeluaran_temp_bb')
            ->select('detailpengeluaran_temp_bb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detailpengeluaran_temp_bb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('id_admin', $id_admin)->get();
        return view('pengeluaranmtc.showtemp', compact('detail'));
    }

    public function deletetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $id_admin = Auth::user()->id;
        $hapus = DB::table('detailpengeluaran_temp_bb')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function storetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty = $request->qty;
        $id_admin = Auth::user()->id;

        $cek = DB::table('detailpengeluaran_temp_gp')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->count();
        if ($cek > 0) {
            echo 1;
        } else {
            $data = [
                'kode_barang' => $kode_barang,
                'keterangan' => $keterangan,
                'qty' => $qty,
                'id_admin' => $id_admin
            ];
            $simpan = DB::table('detailpengeluaran_temp_bb')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function store(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $tgl_pengeluaran = $request->tgl_pengeluaran;
        $tanggal = explode("-", $tgl_pengeluaran);
        // $bulan = $tanggal[1];
        // $tahun = $tanggal[0];
        //$thn = substr($tahun, 2, 2);
        //$blnthn = $bulan . $thn;
        // $pemasukanproduksi = DB::table('pemasukan_bb')
        //     ->whereRaw('MID(nobukti_pemasukan,6,4)=' . $blnthn)
        //     ->orderBy('nobukti_pemasukan', 'desc')
        //     ->first();

        // if ($pemasukanproduksi != null) {
        //     $lastnobukti_pemasukan = $pemasukanproduksi->nobukti_pemasukan;
        // } else {
        //     $lastnobukti_pemasukan = "";
        // }

        // $format = "PRDM/" . $bulan . $thn . "/";
        // $nobukti_pemasukan = buatkode($lastnobukti_pemasukan, $format, 3);
        //dd($lastnobukti_pemasukan);
        $kode_dept = $request->kode_dept;
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpengeluaran_temp_bb')->where('id_admin', $id_admin)->get();
        DB::beginTransaction();
        try {
            $data = [
                'nobukti_pengeluaran' => $nobukti_pengeluaran,
                'tgl_pengeluaran' => $tgl_pengeluaran,
                'kode_dept' => $kode_dept,
                'status' => 1
            ];
            DB::table('pengeluaran_bb')->insert($data);
            foreach ($detail as $d) {
                $datadetail = [
                    'nobukti_pengeluaran' => $nobukti_pengeluaran,
                    'kode_barang' => $d->kode_barang,
                    'keterangan' => $d->keterangan,
                    'qty' => $d->qty
                ];

                DB::table('detail_pengeluaran_bb')->insert($datadetail);
            }
            DB::table('detailpengeluaran_temp_bb')->where('id_admin', $id_admin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data  Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data  Gagal di Simpan, Hubungi Tim IT']);
        }
    }
}