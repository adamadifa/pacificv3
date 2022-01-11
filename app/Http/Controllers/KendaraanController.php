<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::query();
        if (isset($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }
        if (isset($request->no_polisi)) {
            $query->where('no_polisi', 'like', '%' . $request->no_polisi . '%');
        }
        $query->select('*');
        $kendaraan = $query->paginate(15);
        $kendaraan->appends($request->all());
        $cabang = Cabang::all();
        return view('kendaraan.index', compact('kendaraan', 'cabang'));
    }

    public function create()
    {
        $cabang = Cabang::all();
        return view('kendaraan.create', compact('cabang'));
    }

    public function store(Request $request)
    {

        //dd($request);
        $request->validate([
            'no_polisi' => 'required',
            'type' => 'required',
            'model' => 'required',
            'tahun' => 'required',
            // 'no_mesin' => 'required',
            // 'no_rangka' => 'required',
            // 'no_stnk' => 'required',
            // 'pajak' => 'required',
            // 'atas_nama' => 'required',
            // 'keur' => 'required',
            // 'no_uji' => 'required',
            // 'kir' => 'required',
            // 'stnk' => 'required',
            // 'sipa' => 'required',
            'pemakai' => 'required',
            // 'jabatan' => 'required',
            'kode_cabang' => 'required',
            'status' => 'required'
        ]);


        $simpan = DB::table('kendaraan')
            ->insert([
                'no_polisi' => $request->no_polisi,
                'type' => $request->type,
                'model' => $request->model,
                'tahun' => $request->tahun,
                // 'no_mesin' => $request->no_mesin,
                // 'no_rangka' => $request->no_rangka,
                // 'no_stnk' => $request->no_stnk,
                // 'pajak' => $request->pajak,
                // 'atas_nama' => $request->atas_nama,
                // 'keur' => $request->keur,
                // 'no_uji' => $request->no_uji,
                // 'kir' => $request->kir,
                // 'stnk' => $request->stnk,
                // 'sipa' => $request->sipa,
                'pemakai' => $request->pemakai,
                // 'jabatan' => $request->jabatan,
                // 'keterangan' => $request->keterangan,
                'kode_cabang' => $request->kode_cabang,
                'status' => $request->status
            ]);

        if ($simpan) {
            return redirect('/kendaraan')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/kendaraan')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function update(Request $request, $id)
    {

        $id = Crypt::decrypt($id);
        //dd($request);
        $request->validate([
            'no_polisi' => 'required',
            'type' => 'required',
            'model' => 'required',
            'tahun' => 'required',
            // 'no_mesin' => 'required',
            // 'no_rangka' => 'required',
            // 'no_stnk' => 'required',
            // 'pajak' => 'required',
            // 'atas_nama' => 'required',
            // 'keur' => 'required',
            // 'no_uji' => 'required',
            // 'kir' => 'required',
            // 'stnk' => 'required',
            // 'sipa' => 'required',
            'pemakai' => 'required',
            // 'jabatan' => 'required',
            'kode_cabang' => 'required',
            'status' => 'required'
        ]);


        $simpan = DB::table('kendaraan')
            ->where('id', $id)
            ->update([
                'no_polisi' => $request->no_polisi,
                'type' => $request->type,
                'model' => $request->model,
                'tahun' => $request->tahun,
                'no_mesin' => $request->no_mesin,
                'no_rangka' => $request->no_rangka,
                'no_stnk' => $request->no_stnk,
                'pajak' => $request->pajak,
                'atas_nama' => $request->atas_nama,
                'keur' => $request->keur,
                'no_uji' => $request->no_uji,
                'kir' => $request->kir,
                'stnk' => $request->stnk,
                'sipa' => $request->sipa,
                'pemakai' => $request->pemakai,
                'jabatan' => $request->jabatan,
                'keterangan' => $request->keterangan,
                'kode_cabang' => $request->kode_cabang,
                'status' => $request->status
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $cabang = Cabang::all();
        $data = Kendaraan::where('id', $id)->first();
        return view('kendaraan.edit', compact('cabang', 'data'));
    }
    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $hapus = DB::table('kendaraan')
            ->where('id', $id)
            ->delete();

        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function show(Request $request)
    {
        $id = $request->id;
        $data = Kendaraan::where('id', $id)->first();
        return view('kendaraan.show', compact('data'));
    }
    function rekapkendaraandashboard(Request $request)
    {
        $dari = $request->tahun . "-" . $request->bulan . "-01";
        $sampai = date('Y-m-t', strtotime($dari));
        $cabang = $request->cabang;
        $query = Kendaraan::query();
        if (!empty($cabang)) {
            $query->where('kendaraan.kode_cabang', $cabang);
        }
        $query->select('kendaraan.no_polisi', 'model', 'jml_berangkat', 'jmlpenjualan');
        $query->leftJoin(
            DB::raw("(
                SELECT no_kendaraan,COUNT(no_kendaraan) as jml_berangkat
                FROM dpb
                WHERE tgl_pengambilan BETWEEN '$dari' AND '$sampai'
                GROUP BY no_kendaraan
            ) dpb"),
            function ($join) {
                $join->on('kendaraan.no_polisi', '=', 'dpb.no_kendaraan');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT no_kendaraan,
				ROUND(SUM(IF(jenis_mutasi = 'PENJUALAN',jumlah,0) /isipcsdus),2) as jmlpenjualan
				FROM detail_mutasi_gudang_cabang
				INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
				INNER JOIN dpb ON mutasi_gudang_cabang.no_dpb = dpb.no_dpb
				INNER JOIN master_barang ON detail_mutasi_gudang_cabang.kode_produk = master_barang.kode_produk
				WHERE tgl_mutasi_gudang_cabang BETWEEN  '$dari' AND '$sampai'
				GROUP BY no_kendaraan
            ) penjualan"),
            function ($join) {
                $join->on('kendaraan.no_polisi', '=', 'penjualan.no_kendaraan');
            }
        );

        $rekapkendaraan = $query->get();
        return view('kendaraan.dashboard.rekapkendaraan', compact('rekapkendaraan'));
    }
}
