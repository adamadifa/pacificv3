<?php

namespace App\Http\Controllers;

use App\Models\Angkutan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class AngkutanController extends Controller
{
    public function index(Request $request)
    {
        $query = Angkutan::query();
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_input', [$request->dari, $request->sampai]);
        }
        if (!empty($request->no_surat_jalan)) {
            $query->where('no_surat_jalan', $request->no_surat_jalan);
        }
        if (!empty($request->status)) {
            if ($request->status == 1) {
                $query->whereNotNull('tgl_kontrabon');
            } else if ($request->status == 2) {
                $query->whereNull('tgl_kontrabon');
            }
        }
        $query->orderBy('tgl_input', 'desc');
        $angkutan = $query->paginate(15);
        $angkutan->appends($request->all());
        return view('angkutan.index', compact('angkutan'));
    }

    public function delete($no_surat_jalan)
    {
        $no_surat_jalan = Crypt::decrypt($no_surat_jalan);
        $hapus = DB::table('angkutan')->where('no_surat_jalan', $no_surat_jalan)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function edit($no_surat_jalan)
    {
        $no_surat_jalan = Crypt::decrypt($no_surat_jalan);
        $angkutan = DB::table('angkutan')->where('no_surat_jalan', $no_surat_jalan)->first();
        return view('angkutan.edit', compact('angkutan'));
    }

    public function update($no_surat_jalan, Request $request)
    {
        $no_surat_jalan = Crypt::decrypt($no_surat_jalan);
        $no_surat_jalan_new = $request->no_surat_jalan;
        $tujuan = $request->tujuan;
        $nopol = $request->nopol;
        $tarif = !empty($request->tarif) ? str_replace(".", "", $request->tarif) : 0;
        $tepung = !empty($request->tepung) ? str_replace(".", "", $request->tepung) : 0;
        $bs = !empty($request->bs) ? str_replace(".", "", $request->bs) : 0;
        $angkutan = $request->angkutan;
        DB::beginTransaction();
        try {
            $data = [
                'no_surat_jalan' => $no_surat_jalan_new,
                'tujuan' => $tujuan,
                'nopol' => $nopol,
                'tarif' => $tarif,
                'tepung' => $tepung,
                'bs' => $bs,
                'angkutan' => $angkutan
            ];

            $datasj = [
                'no_dok' => $no_surat_jalan_new
            ];

            DB::table('angkutan')->where('no_surat_jalan', $no_surat_jalan)->update($data);
            DB::table('mutasi_gudang_jadi')->where('no_dok', $no_surat_jalan)->update($datasj);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }
}
