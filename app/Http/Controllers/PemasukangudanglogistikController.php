<?php

namespace App\Http\Controllers;

use App\Models\Pemasukangudanglogistik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PemasukangudanglogistikController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemasukangudanglogistik::query();
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pemasukan', [$request->dari, $request->sampai]);
        }

        if (!empty($request->nobukti_pemasukan)) {
            $query->where('nobukti_pemasukan', $request->nobukti_pemasukan);
        }

        $query->orderBy('tgl_pemasukan', 'desc');
        $pemasukan = $query->paginate(15);
        $pemasukan->appends($request->all());

        return view('pemasukangudanglogistik.index', compact('pemasukan'));
    }

    public function create()
    {
        return view('pemasukangudanglogistik.create');
    }

    public function cektemp()
    {
        $id_admin = Auth::user()->id;
        $cek = DB::table('detailpemasukan_temp')->where('id_admin', $id_admin)->count();
        echo $cek;
    }

    public function getbarang()
    {
        $barang = DB::table('master_barang_pembelian')->where('kode_dept', 'GDL')->orderBy('kode_barang')->get();
        return view('pemasukangudanglogistik.getbarang', compact('barang'));
    }

    public function storetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty = $request->qty;
        $harga = !empty($request->harga) ? str_replace(".", "", $request->harga) : 0;
        $harga = str_replace(",", ".", $harga);
        $id_admin = Auth::user()->id;
        $detailpemasukan = DB::table('detailpemasukan_temp')->where('id_admin', $id_admin)->orderBy('no_urut', 'desc')->first();
        $no_urut = $detailpemasukan != null ? $detailpemasukan->no_urut + 1 : 1;
        // $cek = DB::table('detailpemasukan_temp')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->count();
        // if ($cek > 0) {
        //     echo 1;
        // } else {
        $data = [
            'kode_barang' => $kode_barang,
            'keterangan' => $keterangan,
            'qty' => $qty,
            'harga' => $harga,
            'id_admin' => $id_admin,
            'no_urut' => $no_urut
        ];
        $simpan = DB::table('detailpemasukan_temp')->insert($data);
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
        $hapus = DB::table('detailpemasukan_temp')->where('kode_barang', $kode_barang)->where('no_urut', $no_urut)->where('id_admin', $id_admin)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function store(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $tgl_pemasukan = $request->tgl_pemasukan;

        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpemasukan_temp')->where('id_admin', $id_admin)->get();
        DB::beginTransaction();
        try {
            $data = [
                'nobukti_pemasukan' => $nobukti_pemasukan,
                'tgl_pemasukan' => $tgl_pemasukan
            ];
            DB::table('pemasukan')->insert($data);
            foreach ($detail as $d) {
                $datadetail = [
                    'nobukti_pemasukan' => $nobukti_pemasukan,
                    'kode_barang' => $d->kode_barang,
                    'keterangan' => $d->keterangan,
                    'qty' => $d->qty,
                    'harga' => $d->qty,
                    'no_urut' => $d->no_urut
                ];

                DB::table('detail_pemasukan')->insert($datadetail);
            }
            DB::table('detailpemasukan_temp')->where('id_admin', $id_admin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data  Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data  Gagal di Simpan, Hubungi Tim IT']);
        }
    }

    public function showtemp()
    {
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpemasukan_temp')
            ->select('detailpemasukan_temp.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detailpemasukan_temp.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('id_admin', $id_admin)->get();
        return view('pemasukangudanglogistik.showtemp', compact('detail'));
    }
    public function show(Request $request)
    {
        $nobukti_pemasukan = Crypt::decrypt($request->nobukti_pemasukan);
        $pemasukan = DB::table('pemasukan')->where('nobukti_pemasukan', $nobukti_pemasukan)
            ->select('pemasukan.*', 'pembelian.kode_supplier', 'nama_supplier')
            ->leftJoin('pembelian', 'pemasukan.nobukti_pemasukan', '=', 'pembelian.nobukti_pembelian')
            ->leftJoin('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier')
            ->first();
        $detail = DB::table('detail_pemasukan')
            ->select('detail_pemasukan.*', 'nama_barang', 'satuan', 'nama_akun')
            ->join('master_barang_pembelian', 'detail_pemasukan.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->leftJoin('coa', 'detail_pemasukan.kode_akun', '=', 'coa.kode_akun')
            ->where('nobukti_pemasukan', $nobukti_pemasukan)->get();
        return view('pemasukangudanglogistik.show', compact('detail', 'pemasukan'));
    }

    public function delete($nobukti_pemasukan)
    {
        $nobukti_pemasukan  = Crypt::decrypt($nobukti_pemasukan);
        $hapus = DB::table('pemasukan')->where('nobukti_pemasukan', $nobukti_pemasukan)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dibatalkan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan, Hubungi Tim IT']);
        }
    }
}
