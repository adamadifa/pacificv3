<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pengeluarangudanglogistik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PengeluarangudanglogistikController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengeluarangudanglogistik::query();
        if (!empty($request->nobukti_pengeluaran)) {
            $query->where('nobukti_pengeluaran', $request->nobukti_pengeluaran);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pengeluaran', [$request->dari, $request->sampai]);
        }

        if (!empty($request->kode_dept)) {
            $query->where('kode_dept', $request->kode_dept);
        }
        $query->select('pengeluaran.*', 'nama_dept');
        $query->join('departemen', 'pengeluaran.kode_dept', '=', 'departemen.kode_dept');
        $query->orderBy('tgl_pengeluaran', 'desc');
        $query->orderBy('nobukti_pengeluaran', 'desc');
        $pengeluaran = $query->paginate(15);
        $pengeluaran->appends($request->all());
        $departemen = DB::table('departemen')->where('status_pengajuan', '!=', 2)->get();
        return view('pengeluarangudanglogistik.index', compact('pengeluaran', 'departemen'));
    }

    public function show(Request $request)
    {
        $nobukti_pengeluaran = Crypt::decrypt($request->nobukti_pengeluaran);
        $pengeluaran = DB::table('pengeluaran')
            ->select('pengeluaran.*', 'nama_dept')
            ->join('departemen', 'pengeluaran.kode_dept', '=', 'departemen.kode_dept')
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)->first();
        $detail = DB::table('detail_pengeluaran')
            ->select('detail_pengeluaran.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pengeluaran.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)->get();
        return view('pengeluarangudanglogistik.show', compact('detail', 'pengeluaran'));
    }

    public function delete($nobukti_pengeluaran)
    {
        $nobukti_pengeluaran  = Crypt::decrypt($nobukti_pengeluaran);
        $hapus = DB::table('pengeluaran')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $departemen = DB::table('departemen')->where('status_pengajuan', '!=', 2)->get();
        return view('pengeluarangudanglogistik.create', compact('departemen', 'cabang'));
    }

    public function cektemp()
    {
        $id_admin = Auth::user()->id;
        $cek = DB::table('detailpengeluaran_temp')->where('id_admin', $id_admin)->count();
        echo $cek;
    }

    public function getbarang()
    {
        $barang = DB::table('master_barang_pembelian')->where('kode_dept', 'GDL')->orderBy('kode_barang')->get();

        return view('pengeluarangudanglogistik.getbarang', compact('barang'));
    }

    public function showtemp()
    {
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpengeluaran_temp')
            ->select('detailpengeluaran_temp.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detailpengeluaran_temp.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('id_admin', $id_admin)->get();
        return view('pengeluarangudanglogistik.showtemp', compact('detail'));
    }

    public function storetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty = $request->qty;
        $kode_cabang = $request->kode_cabang;
        $id_admin = Auth::user()->id;
        $detailpengeluaran = DB::table('detailpengeluaran_temp')->where('id_admin', $id_admin)->orderBy('no_urut', 'desc')->first();
        $no_urut = $detailpengeluaran != null ? $detailpengeluaran->no_urut + 1 : 1;
        // $cek = DB::table('detailpengeluaran_temp')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->count();
        // if ($cek > 0) {
        //     echo 1;
        // } else {
        $data = [
            'kode_barang' => $kode_barang,
            'keterangan' => $keterangan,
            'qty' => $qty,
            'kode_cabang' => $kode_cabang,
            'id_admin' => $id_admin,
            'no_urut' => $no_urut
        ];
        $simpan = DB::table('detailpengeluaran_temp')->insert($data);
        if ($simpan) {
            echo 0;
        } else {
            echo 2;
        }
        // }
    }

    public function deletetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $no_urut = $request->no_urut;
        $id_admin = Auth::user()->id;
        $hapus = DB::table('detailpengeluaran_temp')->where('kode_barang', $kode_barang)->where('no_urut', $no_urut)->where('id_admin', $id_admin)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }


    public function store(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $tgl_pengeluaran = $request->tgl_pengeluaran;
        $kode_dept = $request->kode_dept;
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpengeluaran_temp')->where('id_admin', $id_admin)->get();
        DB::beginTransaction();
        try {
            $data = [
                'nobukti_pengeluaran' => $nobukti_pengeluaran,
                'tgl_pengeluaran' => $tgl_pengeluaran,
                'kode_dept' => $kode_dept,
            ];
            DB::table('pengeluaran')->insert($data);
            foreach ($detail as $d) {
                $datadetail = [
                    'nobukti_pengeluaran' => $nobukti_pengeluaran,
                    'kode_barang' => $d->kode_barang,
                    'keterangan' => $d->keterangan,
                    'qty' => $d->qty,
                    'kode_cabang' => $d->kode_cabang,
                    'no_urut' => $d->no_urut
                ];

                DB::table('detail_pengeluaran')->insert($datadetail);
            }
            DB::table('detailpengeluaran_temp')->where('id_admin', $id_admin)->delete();
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
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $departemen = DB::table('departemen')->where('status_pengajuan', '!=', 2)->get();
        $pengeluaran = DB::table('pengeluaran')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->first();
        return view('pengeluarangudanglogistik.edit', compact('pengeluaran', 'cabang', 'departemen'));
    }

    public function cekbarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $cek = DB::table('detail_pengeluaran')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->count();
        echo $cek;
    }

    public function showbarang($nobukti_pengeluaran)
    {
        $nobukti_pengeluaran = Crypt::decrypt($nobukti_pengeluaran);
        $detail = DB::table('detail_pengeluaran')
            ->select('detail_pengeluaran.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pengeluaran.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)->get();
        return view('pengeluarangudanglogistik.showbarang', compact('detail'));
    }

    public function storebarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty = $request->qty;
        $kode_cabang = $request->kode_cabang;


        // $cek = DB::table('detail_pengeluaran')->where('kode_barang', $kode_barang)->where('nobukti_pengeluaran', $nobukti_pengeluaran)->count();
        // if ($cek > 0) {
        //     echo 1;
        // } else {

        // }

        $detailpengeluaran = DB::table('detail_pengeluaran')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->orderBy('no_urut', 'desc')->first();
        $no_urut = $detailpengeluaran != null ? $detailpengeluaran->no_urut + 1 : 1;

        $data = [
            'nobukti_pengeluaran' => $nobukti_pengeluaran,
            'kode_barang' => $kode_barang,
            'keterangan' => $keterangan,
            'qty' => $qty,
            'kode_cabang' => $kode_cabang,
            'no_urut' => $no_urut
        ];
        $simpan = DB::table('detail_pengeluaran')->insert($data);
        if ($simpan) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function editbarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $kode_barang = $request->kode_barang;
        $no_urut = $request->no_urut;
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $barang = DB::table('detail_pengeluaran')
            ->select('detail_pengeluaran.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pengeluaran.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)
            ->where('detail_pengeluaran.kode_barang', $kode_barang)
            ->where('detail_pengeluaran.no_urut', $no_urut)
            ->first();

        return view('pengeluarangudanglogistik.editbarang', compact('barang', 'cabang'));
    }

    public function deletebarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $kode_barang = $request->kode_barang;
        $no_urut = $request->no_urut;
        $hapus = DB::table('detail_pengeluaran')
            ->where('kode_barang', $kode_barang)
            ->where('no_urut', $no_urut)
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function updatebarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $kode_barang = $request->kode_barang;
        $no_urut = $request->no_urut;
        $keterangan = $request->keterangan;
        $qty = !empty($request->qty) ? $request->qty : 0;
        $kode_cabang = $request->kode_cabang;
        $data = [
            'keterangan' => $keterangan,
            'qty' => $qty,
            'kode_cabang' => $kode_cabang,
        ];

        $update = DB::table('detail_pengeluaran')
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)
            ->where('kode_barang', $kode_barang)
            ->where('no_urut', $no_urut)
            ->update($data);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function update($nobukti_pengeluaran, Request $request)
    {
        $nobukti_pengeluaran = Crypt::decrypt($nobukti_pengeluaran);
        $tgl_pengeluaran = $request->tgl_pengeluaran;
        $kode_dept = $request->kode_dept;
        $data = [
            'tgl_pengeluaran' => $tgl_pengeluaran,
            'kode_dept' => $kode_dept,
        ];

        $update = DB::table('pengeluaran')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate, Hubungi Tim IT']);
        }
    }
}
