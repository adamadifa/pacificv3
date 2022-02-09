<?php

namespace App\Http\Controllers;

use App\Models\Limitkredit;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class LimitkreditController extends Controller
{
    public function index(Request $request)
    {
        $pelanggan = '"' . $request->nama_pelanggan . '"';
        $query = Limitkredit::query();
        $query->select('pengajuan_limitkredit_v3.*', 'nama_pelanggan');
        $query->orderBy('tgl_pengajuan', 'desc');
        $query->orderBy('no_pengajuan', 'asc');
        $query->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        if (empty($request->nama_pelanggan) && empty($request->dari) && empty($request->sampai) && empty($request->status)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }
        if (!empty($request->nama_pelanggan)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }


        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pengajuan', [$request->dari, $request->sampai]);
        }



        $limitkredit = $query->paginate(15);
        $limitkredit->appends($request->all());
        return view('limitkredit.index', compact('limitkredit'));
    }

    public function cetak($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $limitkredit = DB::table('pengajuan_limitkredit_v3')
            ->select('no_pengajuan', 'tgl_pengajuan', 'jumlah', 'jumlah_rekomendasi', 'pengajuan_limitkredit_v3.jatuhtempo', 'jatuhtempo_rekomendasi', 'skor', 'status', 'kacab', 'mm', 'gm', 'dirut')
            ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->where('no_pengajuan', $no_pengajuan)
            ->orderBy('tgl_pengajuan', 'asc')
            ->first();

        //return view('limitkredit.cetak', compact('limitkredit'));

        $pdf = PDF::loadview('limitkredit.cetak', compact('limitkredit'))->setPaper('a4');
        return $pdf->stream();
    }
}
