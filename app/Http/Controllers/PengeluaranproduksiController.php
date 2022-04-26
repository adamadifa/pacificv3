<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaranproduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PengeluaranproduksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengeluaranproduksi::query();
        if (!empty($request->nobukti_pengeluaran)) {
            $query->where('nobukti_pengeluaran', $request->nobukti_pengeluaran);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pengeluaran', [$request->dari, $request->sampai]);
        }
        $query->orderBy('tgl_pengeluaran', 'desc');
        $query->orderBy('nobukti_pengeluaran', 'desc');
        $pengeluaranproduksi = $query->paginate(15);
        $pengeluaranproduksi->appends($request->all());
        return view('pengeluaranproduksi.index', compact('pengeluaranproduksi'));
    }

    public function show(Request $request)
    {
        $nobukti_pengeluaran = Crypt::decrypt($request->nobukti_pengeluaran);
        $pengeluaranproduksi = DB::table('pengeluaran_gp')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->first();
        $detail = DB::table('detail_pengeluaran_gp')
            ->select('detail_pengeluaran_gp.*', 'nama_barang', 'satuan')
            ->join('master_barang_produksi', 'detail_pengeluaran_gp.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)->get();
        return view('pengeluaranproduksi.show', compact('detail', 'pengeluaranproduksi'));
    }

    public function delete($nobukti_pengeluaran)
    {
        $nobukti_pengeluaran  = Crypt::decrypt($nobukti_pengeluaran);
        $hapus = DB::table('pengeluaran_gp')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        return view('pengeluaranproduksi.create');
    }

    public function cektemp()
    {
        $id_admin = Auth::user()->id;
        $cek = DB::table('detailpengeluaran_temp_gp')->where('id_admin', $id_admin)->count();
        echo $cek;
    }

    public function getbarang()
    {
        $barang = DB::table('master_barang_produksi')->orderBy('kode_barang')->get();
        return view('pengeluaranproduksi.getbarang', compact('barang'));
    }

    public function showtemp()
    {
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpengeluaran_temp_gp')
            ->select('detailpengeluaran_temp_gp.*', 'nama_barang', 'satuan')
            ->join('master_barang_produksi', 'detailpengeluaran_temp_gp.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('id_admin', $id_admin)->get();
        return view('pengeluaranproduksi.showtemp', compact('detail'));
    }

    public function storetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $barangproduksi = DB::table('master_barang_produksi')->where('kode_barang', $kode_barang)->first();
        $kode_barang_gb = $barangproduksi->kode_barang_gb;
        $keterangan = $request->keterangan;
        $qty = $request->qty;
        $berat = !empty($request->berat) ? $request->berat : 0;
        $id_admin = Auth::user()->id;

        $cek = DB::table('detailpengeluaran_temp_gp')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->count();
        if ($cek > 0) {
            echo 1;
        } else {
            $data = [
                'kode_barang' => $kode_barang,
                'kode_barang_gb' => $kode_barang_gb,
                'keterangan' => $keterangan,
                'qty' => $qty,
                'qty_berat' => $berat,
                'id_admin' => $id_admin
            ];
            $simpan = DB::table('detailpengeluaran_temp_gp')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function deletetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $id_admin = Auth::user()->id;
        $hapus = DB::table('detailpengeluaran_temp_gp')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function store(Request $request)
    {

        $tgl_pengeluaran = $request->tgl_pengeluaran;
        $tanggal = explode("-", $tgl_pengeluaran);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $thn = substr($tahun, 2, 2);
        $pengeluaranproduksi = DB::table('pengeluaran_gp')
            ->whereRaw('MONTH(tgl_pengeluaran)=' . $bulan)
            ->whereRaw('YEAR(tgl_pengeluaran)=' . $tahun)
            ->orderBy('nobukti_pengeluaran', 'desc')
            ->first();

        if ($pengeluaranproduksi != null) {
            $lastnobukti_pengeluaran = $pengeluaranproduksi->nobukti_pengeluaran;
        } else {
            $lastnobukti_pengeluaran = "";
        }

        $format = "PRDK/" . $bulan . $thn . "/";
        $nobukti_pengeluaran = buatkode($lastnobukti_pengeluaran, $format, 3);

        $kode_dept = $request->kode_dept;
        $kode_supplier = $request->kode_supplier;
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpengeluaran_temp_gp')->where('id_admin', $id_admin)->get();
        DB::beginTransaction();
        try {
            $data = [
                'nobukti_pengeluaran' => $nobukti_pengeluaran,
                'tgl_pengeluaran' => $tgl_pengeluaran,
                'kode_dept' => $kode_dept,
                'kode_supplier' => $kode_supplier
            ];
            DB::table('pengeluaran_gp')->insert($data);
            foreach ($detail as $d) {
                $datadetail = [
                    'nobukti_pengeluaran' => $nobukti_pengeluaran,
                    'kode_barang' => $d->kode_barang,
                    'keterangan' => $d->keterangan,
                    'qty' => $d->qty,
                    'qty_berat' => $d->qty_berat,
                    'kode_barang_gb' => $d->kode_barang_gb
                ];

                DB::table('detail_pengeluaran_gp')->insert($datadetail);
            }
            DB::table('detailpengeluaran_temp_gp')->where('id_admin', $id_admin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data  Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data  Gagal di Simpan, Hubungi Tim IT']);
        }
    }

    public function edit($nobukti_pengeluaran)
    {
        $nobukti_pengeluaran = Crypt::decrypt($nobukti_pengeluaran);
        $pengeluaranproduksi = DB::table('pengeluaran_gp')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->first();
        return view('pengeluaranproduksi.edit', compact('pengeluaranproduksi'));
    }

    public function cekbarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $cek = DB::table('detail_pengeluaran_gp')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->count();
        echo $cek;
    }

    public function showbarang($nobukti_pengeluaran)
    {
        $nobukti_pengeluaran = Crypt::decrypt($nobukti_pengeluaran);
        $detail = DB::table('detail_pengeluaran_gp')
            ->select('detail_pengeluaran_gp.*', 'nama_barang', 'satuan')
            ->join('master_barang_produksi', 'detail_pengeluaran_gp.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)->get();
        return view('pengeluaranproduksi.showbarang', compact('detail'));
    }

    public function storebarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $kode_barang = $request->kode_barang;
        $barangproduksi = DB::table('master_barang_produksi')->where('kode_barang', $kode_barang)->first();
        $kode_barang_gb = $barangproduksi->kode_barang_gb;
        $keterangan = $request->keterangan;
        $qty = $request->qty;
        $berat = !empty($request->berat) ? $request->berat : 0;


        $cek = DB::table('detail_pengeluaran_gp')->where('kode_barang', $kode_barang)->where('nobukti_pengeluaran', $nobukti_pengeluaran)->count();
        if ($cek > 0) {
            echo 1;
        } else {
            $data = [
                'nobukti_pengeluaran' => $nobukti_pengeluaran,
                'kode_barang' => $kode_barang,
                'keterangan' => $keterangan,
                'qty' => $qty,
                'qty_berat' => $berat,
                'kode_barang_gb' => $kode_barang_gb
            ];
            $simpan = DB::table('detail_pengeluaran_gp')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }


    public function editbarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $kode_barang = $request->kode_barang;
        $barang = DB::table('detail_pengeluaran_gp')
            ->select('detail_pengeluaran_gp.*', 'nama_barang', 'satuan')
            ->join('master_barang_produksi', 'detail_pengeluaran_gp.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)
            ->where('detail_pengeluaran_gp.kode_barang', $kode_barang)
            ->first();

        return view('pengeluaranproduksi.editbarang', compact('barang'));
    }


    public function updatebarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $kode_barang = $request->kode_barang;
        $barangproduksi = DB::table('master_barang_produksi')->where('kode_barang', $kode_barang)->first();
        $kode_barang_gb = $barangproduksi->kode_barang_gb;
        $keterangan = $request->keterangan;
        $qty = !empty($request->qty) ? $request->qty : 0;
        $berat = !empty($request->berat) ? $request->berat : 0;
        $data = [
            'keterangan' => $keterangan,
            'qty' => $qty,
            'qty_berat' => $berat,
            'kode_barang_gb' => $kode_barang_gb
        ];

        $update = DB::table('detail_pengeluaran_gp')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->where('kode_barang', $kode_barang)->update($data);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function deletebarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $kode_barang = $request->kode_barang;
        $hapus = DB::table('detail_pengeluaran_gp')->where('kode_barang', $kode_barang)->where('nobukti_pengeluaran', $nobukti_pengeluaran)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function update($nobukti_pengeluaran, Request $request)
    {
        $nobukti_pengeluaran = Crypt::decrypt($nobukti_pengeluaran);
        $tgl_pengeluaran = $request->tgl_pengeluaran;
        $kode_dept = $request->kode_dept;
        $kode_supplier = $request->kode_supplier;
        $data = [
            'tgl_pengeluaran' => $tgl_pengeluaran,
            'kode_dept' => $kode_dept,
            'kode_supplier' => $kode_supplier
        ];

        $update = DB::table('pengeluaran_gp')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate, Hubungi Tim IT']);
        }
    }
}
