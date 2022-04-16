<?php

namespace App\Http\Controllers;

use App\Models\Kontrabon;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KontrabonController extends Controller
{
    public function index(Request $request)
    {
        $query = Kontrabon::query();
        $query->selectRaw("kontrabon.no_kontrabon,no_dokumen,tgl_kontrabon,kategori,nama_supplier,totalbayar,tglbayar,jenisbayar");
        $query->join('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier');
        $query->leftJoin('historibayar_pembelian', 'kontrabon.no_kontrabon', '=', 'historibayar_pembelian.no_kontrabon');
        $query->leftJoin(
            DB::raw('(
                SELECT no_kontrabon,SUM(jmlbayar) as totalbayar
                FROM detail_kontrabon
                GROUP BY no_kontrabon
            ) detailkontrabon'),
            function ($join) {
                $join->on('kontrabon.no_kontrabon', '=', 'detailkontrabon.no_kontrabon');
            }
        );

        if (!empty($request->no_kontrabon)) {
            $query->where('kontrabon.no_kontrabon', $request->no_kontrabon);
        }

        if (!empty($request->no_dokumen)) {
            $query->where('kontrabon.no_dokumen', $request->no_dokumen);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_kontrabon', [$request->dari, $request->sampai]);
        }
        if (!empty($request->kode_supplier)) {
            $query->where('kontrabon.kode_supplier', $request->kode_supplier);
        }

        if (!empty($request->status)) {
            if ($request->status == 1) {
                $query->whereNull('tglbayar');
            } else {
                $query->whereNotNull('tglbayar');
            }
        }

        if (!empty($request->kategori)) {
            $query->where('kategori', $request->kategori);
        }
        $query->orderBy('tgl_kontrabon', 'desc');
        $kontrabon = $query->paginate(15);
        $kontrabon->appends($request->all());
        $supplier = Supplier::orderBy('nama_supplier')->get();
        return view('kontrabon.index', compact('supplier', 'kontrabon'));
    }

    public function show(Request $request)
    {
        $no_kontrabon = Crypt::decrypt($request->no_kontrabon);
        $kontrabon = DB::table('kontrabon')
            ->select('kontrabon.*', 'nama_supplier')
            ->join('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier')
            ->where('no_kontrabon', $no_kontrabon)->first();
        $detailkontrabon = DB::table('detail_kontrabon')
            ->select('detail_kontrabon.*', 'tgl_pembelian')
            ->join('pembelian', 'detail_kontrabon.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian')
            ->where('no_kontrabon', $no_kontrabon)->get();
        return view('kontrabon.show', compact('kontrabon', 'detailkontrabon'));
    }

    public function create()
    {
        return view('kontrabon.create');
    }

    public function storetemp(Request $request)
    {
        $id_admin = Auth::user()->id;
        $nobukti_pembelian = $request->nobukti_pembelian;
        $kode_supplier = $request->kode_supplier;
        $jml_bayar = str_replace(".", "", $request->jmlbayar);
        $jmlbayar = str_replace(",", ".", $jml_bayar);
        $keterangan = $request->keterangan;
        $cek = DB::table('detailkontrabon_temp')->where('nobukti_pembelian', $nobukti_pembelian)->where('id_admin', $id_admin)->count();
        if (!empty($cek)) {
            echo 1;
        } else {
            $data = [
                'nobukti_pembelian' => $nobukti_pembelian,
                'kode_supplier' => $kode_supplier,
                'jmlbayar' => $jmlbayar,
                'keterangan' => $keterangan,
                'id_admin' => $id_admin
            ];

            $simpan = DB::table('detailkontrabon_temp')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function showtemp(Request $request)
    {
        $kode_supplier = $request->kode_supplier;
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailkontrabon_temp')->where('id_admin', $id_admin)->where('kode_supplier', $kode_supplier)->get();
        return view('kontrabon.showtemp', compact('detail'));
    }

    public function deletetemp(Request $request)
    {
        $nobukti_pembelian = $request->nobukti_pembelian;
        $hapus = DB::table('detailkontrabon_temp')->where('nobukti_pembelian', $nobukti_pembelian)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function store(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $tgl_kontragbon = $request->tgl_kontrabon;
        $kategori = $request->kategori;
        $kode_supplier = $request->kode_supplier;
        $no_dokumen = $request->no_dokumen;
        $jenisbayar = $request->jenisbayar;
        $id_admin = Auth::user()->id;

        $detailtemp = DB::table('detailkontrabon_temp')->where('kode_supplier', $kode_supplier)->where('id_admin', $id_admin)->get();
        DB::beginTransaction();
        try {
            $data = [
                'no_kontrabon' => $no_kontrabon,
                'tgl_kontrabon' => $tgl_kontragbon,
                'kategori' => $kategori,
                'kode_supplier' => $kode_supplier,
                'no_dokumen' => $no_dokumen,
                'jenisbayar' => $jenisbayar,
                'id_admin' => Auth::user()->id
            ];

            DB::table('kontrabon')->insert($data);
            foreach ($detailtemp as $d) {
                $datadetail = [
                    'no_kontrabon' => $no_kontrabon,
                    'nobukti_pembelian' => $d->nobukti_pembelian,
                    'jmlbayar' => $d->jmlbayar,
                    'keterangan' => $d->keterangan
                ];
                DB::table('detail_kontrabon')->insert($datadetail);
            }
            DB::table('detailkontrabon_temp')->where('kode_supplier', $kode_supplier)->where('id_admin', $id_admin)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Kontrabon Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Kontrabon Gagal di Simpan']);
        }
    }

    public function edit($no_kontrabon)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $kontrabon = DB::table('kontrabon')
            ->select('kontrabon.*', 'nama_supplier')
            ->join('supplier', 'kontrabon.kode_supplier', '=', 'supplier.kode_supplier')
            ->where('no_kontrabon', $no_kontrabon)->first();
        $detail = DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabon)->get();
        return view('kontrabon.edit', compact('kontrabon', 'detail'));
    }

    public function showdetail(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $detail = DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabon)->get();
        return view('kontrabon.showdetail', compact('detail'));
    }

    public function storedetail(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $nobukti_pembelian = $request->nobukti_pembelian;
        $jml_bayar = str_replace(".", "", $request->jmlbayar);
        $jmlbayar = str_replace(",", ".", $jml_bayar);
        $keterangan = $request->keterangan;
        $cek = DB::table('detail_kontrabon')->where('nobukti_pembelian', $nobukti_pembelian)->where('no_kontrabon', $no_kontrabon)->count();
        if (!empty($cek)) {
            echo 1;
        } else {
            $data = [
                'no_kontrabon' => $no_kontrabon,
                'nobukti_pembelian' => $nobukti_pembelian,
                'jmlbayar' => $jmlbayar,
                'keterangan' => $keterangan,
            ];

            $simpan = DB::table('detail_kontrabon')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function deletedetail(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $nobukti_pembelian = $request->nobukti_pembelian;
        $hapus = DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabon)->where('nobukti_pembelian', $nobukti_pembelian)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function updatedetail(Request $request)
    {
        $no_kontrabon = $request->no_kontrabon;
        $nobukti_pembelian = $request->nobukti_pembelian;
        $jml_bayar = str_replace(".", "", $request->jmlbayar);
        $jmlbayar = str_replace(",", ".", $jml_bayar);

        $data = [
            'jmlbayar' => $jmlbayar
        ];
        $update = DB::table('detail_kontrabon')->where('no_kontrabon', $no_kontrabon)->where('nobukti_pembelian', $nobukti_pembelian)->update($data);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function update($no_kontrabon, Request $request)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $tgl_kontrabon = $request->tgl_kontrabon;
        $kategori = $request->kategori;
        $jenisbayar = $request->jenisbayar;
        $no_dokumen = $request->no_dokumen;

        $data = [
            'tgl_kontrabon' => $tgl_kontrabon,
            'kategori' => $kategori,
            'jenisbayar' => $jenisbayar,
            'no_dokumen' => $no_dokumen
        ];

        $update = DB::table('kontrabon')->where('no_kontrabon', $no_kontrabon)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan !']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT !']);
        }
    }
}
