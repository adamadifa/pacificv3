<?php

namespace App\Http\Controllers;

use App\Models\Limitkredit;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

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

    public function create($kode_pelanggan)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $pelanggan = DB::table('pelanggan')
            ->select('pelanggan.*', 'nama_karyawan')
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->first();
        $lasttopup = DB::table('pengajuan_limitkredit_v3')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->orderBy('tgl_pengajuan', 'desc')
            ->first();
        $listfaktur = DB::table('penjualan')
            ->select('penjualan.no_fak_penj', 'tgltransaksi', DB::raw(
                'IFNULL(penjualan.total,0) - IFNULL(retur.total,0) AS nettopiutang'
            ), 'jmlbayar')
            ->leftJoin(
                DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total FROM retur GROUP BY retur.no_fak_penj
            ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT no_fak_penj, IFNULL(SUM(bayar),0) as jmlbayar
                FROM historibayar
                GROUP BY no_fak_penj
            ) historibayar"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'historibayar.no_fak_penj');
                }
            )
            ->where('kode_pelanggan', $kode_pelanggan)
            ->where('status_lunas', 2)
            ->get();
        return view('limitkredit.create', compact('pelanggan', 'lasttopup', 'listfaktur'));
    }

    public function cetak($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $limitkredit = DB::table('pengajuan_limitkredit_v3')
            ->select(
                'pengajuan_limitkredit_v3.*',
                'nama_pelanggan',
                'alamat_pelanggan',
                'alamat_toko',
                'latitude',
                'longitude',
                'pelanggan.no_hp',
                'status_outlet',
                'cara_pembayaran',
                'histori_transaksi',
                'lama_topup',
                'lama_usaha',
                'kepemilikan',
                'omset_toko',
                'lama_langganan',
                'type_outlet',
                'nama_karyawan',
                'karyawan.kode_cabang'
            )
            ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->where('no_pengajuan', $no_pengajuan)
            ->orderBy('tgl_pengajuan', 'asc')
            ->first();
        $komentar = DB::table('pengajuan_limitkredit_analisa_v3')->where('no_pengajuan', $no_pengajuan)->get();
        //return view('limitkredit.cetak', compact('limitkredit'));

        $pdf = PDF::loadview('limitkredit.cetak', compact('limitkredit', 'komentar'))->setPaper('a4');
        return $pdf->stream();
    }

    public function delete($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $hapus = DB::table('pengajuan_limitkredit_v3')
            ->where('no_pengajuan', $no_pengajuan)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function store(Request $request)
    {
    }
}
