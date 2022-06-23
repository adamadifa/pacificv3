<?php

namespace App\Http\Controllers;

use App\Models\Pemasukanproduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;


class PemasukanproduksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemasukanproduksi::query();
        if (!empty($request->nobukti_pemasukan)) {
            $query->where('nobukti_pemasukan', $request->nobukti_pemasukan);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pemasukan', [$request->dari, $request->sampai]);
        }
        $query->orderBy('tgl_pemasukan', 'desc');
        $pemasukanproduksi = $query->paginate(15);
        $pemasukanproduksi->appends($request->all());
        return view('pemasukanproduksi.index', compact('pemasukanproduksi'));
    }

    public function show(Request $request)
    {
        $nobukti_pemasukan = Crypt::decrypt($request->nobukti_pemasukan);
        $pemasukanproduksi = DB::table('pemasukan_gp')->where('nobukti_pemasukan', $nobukti_pemasukan)->first();
        $detail = DB::table('detail_pemasukan_gp')
            ->select('detail_pemasukan_gp.*', 'nama_barang', 'satuan')
            ->join('master_barang_produksi', 'detail_pemasukan_gp.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('nobukti_pemasukan', $nobukti_pemasukan)->get();
        return view('pemasukanproduksi.show', compact('detail', 'pemasukanproduksi'));
    }

    public function delete($nobukti_pemasukan)
    {
        $nobukti_pemasukan  = Crypt::decrypt($nobukti_pemasukan);
        $hapus = DB::table('pemasukan_gp')->where('nobukti_pemasukan', $nobukti_pemasukan)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        return view('pemasukanproduksi.create');
    }

    public function edit($nobukti_pemasukan)
    {
        $nobukti_pemasukan = Crypt::decrypt($nobukti_pemasukan);
        $pemasukanproduksi = DB::table('pemasukan_gp')->where('nobukti_pemasukan', $nobukti_pemasukan)->first();
        return view('pemasukanproduksi.edit', compact('pemasukanproduksi'));
    }

    public function getbarang($kode_dept)
    {
        if ($kode_dept != "Trial") {
            $barang = DB::table('master_barang_produksi')
                ->where('kode_dept', $kode_dept)
                ->orderBy('kode_barang')->get();
        } else {
            $barang = DB::table('master_barang_produksi')
                ->orderBy('kode_barang')->get();
        }
        return view('pemasukanproduksi.getbarang', compact('barang'));
    }

    public function storetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty = $request->qty;
        $id_admin = Auth::user()->id;

        $cek = DB::table('detailpemasukan_temp_gp')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->count();
        if ($cek > 0) {
            echo 1;
        } else {
            $data = [
                'kode_barang' => $kode_barang,
                'keterangan' => $keterangan,
                'qty' => $qty,
                'id_admin' => $id_admin
            ];
            $simpan = DB::table('detailpemasukan_temp_gp')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function storebarang(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty = $request->qty;


        $cek = DB::table('detail_pemasukan_gp')->where('kode_barang', $kode_barang)->where('nobukti_pemasukan', $nobukti_pemasukan)->count();
        if ($cek > 0) {
            echo 1;
        } else {
            $data = [
                'nobukti_pemasukan' => $nobukti_pemasukan,
                'kode_barang' => $kode_barang,
                'keterangan' => $keterangan,
                'qty' => $qty,
            ];
            $simpan = DB::table('detail_pemasukan_gp')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }
    public function showtemp()
    {
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpemasukan_temp_gp')
            ->select('detailpemasukan_temp_gp.*', 'nama_barang', 'satuan')
            ->join('master_barang_produksi', 'detailpemasukan_temp_gp.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('id_admin', $id_admin)->get();
        return view('pemasukanproduksi.showtemp', compact('detail'));
    }

    public function showbarang($nobukti_pemasukan)
    {
        $nobukti_pemasukan = Crypt::decrypt($nobukti_pemasukan);
        $detail = DB::table('detail_pemasukan_gp')
            ->select('detail_pemasukan_gp.*', 'nama_barang', 'satuan')
            ->join('master_barang_produksi', 'detail_pemasukan_gp.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('nobukti_pemasukan', $nobukti_pemasukan)->get();
        return view('pemasukanproduksi.showbarang', compact('detail'));
    }

    public function deletetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $id_admin = Auth::user()->id;
        $hapus = DB::table('detailpemasukan_temp_gp')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function deletebarang(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $kode_barang = $request->kode_barang;
        $hapus = DB::table('detail_pemasukan_gp')->where('kode_barang', $kode_barang)->where('nobukti_pemasukan', $nobukti_pemasukan)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function cektemp()
    {
        $id_admin = Auth::user()->id;
        $cek = DB::table('detailpemasukan_temp_gp')->where('id_admin', $id_admin)->count();
        echo $cek;
    }

    public function cekbarang(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $cek = DB::table('detail_pemasukan_gp')->where('nobukti_pemasukan', $nobukti_pemasukan)->count();
        echo $cek;
    }

    public function store(Request $request)
    {

        $tgl_pemasukan = $request->tgl_pemasukan;
        $tanggal = explode("-", $tgl_pemasukan);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $thn = substr($tahun, 2, 2);
        $blnthn = $bulan . $thn;
        $pemasukanproduksi = DB::table('pemasukan_gp')
            ->whereRaw('MID(nobukti_pemasukan,6,4)=' . $blnthn)
            ->orderBy('nobukti_pemasukan', 'desc')
            ->first();

        if ($pemasukanproduksi != null) {
            $lastnobukti_pemasukan = $pemasukanproduksi->nobukti_pemasukan;
        } else {
            $lastnobukti_pemasukan = "";
        }

        $format = "PRDM/" . $bulan . $thn . "/";
        $nobukti_pemasukan = buatkode($lastnobukti_pemasukan, $format, 3);
        //dd($lastnobukti_pemasukan);
        $kode_dept = $request->kode_dept;
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpemasukan_temp_gp')->where('id_admin', $id_admin)->get();
        DB::beginTransaction();
        try {
            $data = [
                'nobukti_pemasukan' => $nobukti_pemasukan,
                'tgl_pemasukan' => $tgl_pemasukan,
                'kode_dept' => $kode_dept
            ];
            DB::table('pemasukan_gp')->insert($data);
            foreach ($detail as $d) {
                $datadetail = [
                    'nobukti_pemasukan' => $nobukti_pemasukan,
                    'kode_barang' => $d->kode_barang,
                    'keterangan' => $d->keterangan,
                    'qty' => $d->qty
                ];

                DB::table('detail_pemasukan_gp')->insert($datadetail);
            }
            DB::table('detailpemasukan_temp_gp')->where('id_admin', $id_admin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data  Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data  Gagal di Simpan, Hubungi Tim IT']);
        }
    }

    public function editbarang(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $kode_barang = $request->kode_barang;
        $barang = DB::table('detail_pemasukan_gp')
            ->select('detail_pemasukan_gp.*', 'nama_barang', 'satuan')
            ->join('master_barang_produksi', 'detail_pemasukan_gp.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('nobukti_pemasukan', $nobukti_pemasukan)
            ->where('detail_pemasukan_gp.kode_barang', $kode_barang)
            ->first();

        return view('pemasukanproduksi.editbarang', compact('barang'));
    }

    public function updatebarang(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty = !empty($request->qty) ? $request->qty : 0;
        $data = [
            'keterangan' => $keterangan,
            'qty' => $qty
        ];

        $update = DB::table('detail_pemasukan_gp')->where('nobukti_pemasukan', $nobukti_pemasukan)->where('kode_barang', $kode_barang)->update($data);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function update($nobukti_pemasukan, Request $request)
    {
        $nobukti_pemasukan = Crypt::decrypt($nobukti_pemasukan);
        $tgl_pemasukan = $request->tgl_pemasukan;
        $kode_dept = $request->kode_dept;
        $data = [
            'tgl_pemasukan' => $tgl_pemasukan,
            'kode_dept' => $kode_dept
        ];

        $update = DB::table('pemasukan_gp')->where('nobukti_pemasukan', $nobukti_pemasukan)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate, Hubungi Tim IT']);
        }
    }
}
