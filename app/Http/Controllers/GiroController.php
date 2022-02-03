<?php

namespace App\Http\Controllers;

use App\Models\Giro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiroController extends Controller
{
    public function index(Request $request)
    {
        $pelanggan = '"' . $request->nama_pelanggan . '"';
        $query = Giro::query();
        $query->select('giro.no_giro', 'tgl_giro', 'nama_pelanggan', 'karyawan.kode_cabang', 'namabank', DB::raw('SUM(giro.jumlah) as jumlah'), 'tglcair', 'giro.status', 'ket', 'tglbayar', 'ledger_bank.no_bukti');
        $query->leftJoin('historibayar', 'giro.id_giro', '=', 'historibayar.id_giro');
        $query->leftJoin('ledger_bank', 'giro.no_giro', '=', 'ledger_bank.no_ref');
        $query->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $query->orderBy('tglcair', 'desc');
        $query->groupBy('giro.no_giro', 'tgl_giro', 'nama_pelanggan', 'karyawan.kode_cabang', 'namabank', 'tglcair', 'giro.status', 'ket', 'ledger_bank.no_bukti', 'tglbayar');
        if (empty($request->no_giro) && empty($request->nama_pelanggan) && empty($request->dari) && empty($request->sampai) && empty($request->status)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }
        if (!empty($request->nama_pelanggan)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }

        if (!empty($request->no_giro)) {
            $query->where('giro.no_giro', $request->no_giro);
        }

        if (!empty($request->status)) {
            $query->where('giro.status', $request->jenis_retur);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tglcair', [$request->dari, $request->sampai]);
        }

        $giro = $query->paginate(15);
        $giro->appends($request->all());
        return view('giro.index', compact('giro'));
    }


    public function detailfaktur(Request $request)
    {
        $detailfaktur = DB::table('giro')
            ->select('giro.no_fak_penj', 'jumlah', 'tgl_giro', 'giro.date_created as tgl_input', 'historibayar.date_created as tgl_aksi')
            ->leftJoin('historibayar', 'giro.id_giro', '=', 'historibayar.id_giro')
            ->where('no_giro', $request->no_giro)
            ->get();
        return view('giro.detailfaktur', compact('detailfaktur'));
    }

    public function prosesgiro(Request $request)
    {
        $giro = DB::table('giro')
            ->select('giro.no_giro', 'tgl_giro', 'penjualan.kode_pelanggan', 'nama_pelanggan', 'karyawan.kode_cabang', 'namabank', DB::raw('SUM(giro.jumlah) as jumlah'), 'tglcair', 'giro.status', 'ket', 'tglbayar', 'ledger_bank.no_bukti')
            ->leftJoin('historibayar', 'giro.id_giro', '=', 'historibayar.id_giro')
            ->leftJoin('ledger_bank', 'giro.no_giro', '=', 'ledger_bank.no_ref')
            ->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->groupBy('giro.no_giro', 'tgl_giro', 'penjualan.kode_pelanggan', 'nama_pelanggan', 'karyawan.kode_cabang', 'namabank', 'tglcair', 'giro.status', 'ket', 'ledger_bank.no_bukti', 'tglbayar')
            ->where('no_giro', $request->no_giro)
            ->first();

        return view('giro.prosesgiro', compact('giro'));
    }
}
