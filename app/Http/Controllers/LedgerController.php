<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Cabang;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $query = Ledger::query();
        $query->select('ledger_bank.*', 'nama_akun');
        $query->join('coa', 'ledger_bank.kode_akun', '=', 'coa.kode_akun');
        $query->join('master_bank', 'ledger_bank.bank', '=', 'master_bank.kode_bank');
        $query->orderBy('tgl_ledger');
        $query->orderBy('pelanggan');
        $query->whereBetween('tgl_ledger', [$request->dari, $request->sampai]);
        $query->where('ledger_bank.bank', $request->ledger);
        $ledger = $query->get();
        $bank = Bank::orderBy('kode_bank')->get();

        if (!empty($request->dari)) {
            $tanggal = explode("-", $request->dari);
            $bulan = $tanggal[1];
            $tahun = $tanggal[0];
        } else {
            $bulan = "";
            $tahun = "";
        }

        $lastsaldoawal = DB::table('saldoawal_ledger')
            ->where('bulan', '<=', $bulan)
            ->where('tahun', '<=', $tahun)
            ->where('kode_bank', $request->ledger)
            ->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->first();
        if ($lastsaldoawal != null) {

            $sa = $lastsaldoawal->jumlah;
            $tgl_mulai = $lastsaldoawal->tahun . "-" . $lastsaldoawal->bulan . "-01";
        } else {
            $sa = 0;
            $tgl_mulai = "";
        }

        $mutasi = DB::table('ledger_bank')
            ->selectRaw("SUM(IF(status_dk='K',jumlah,0)) - SUM(IF(status_dk='D',jumlah,0)) as sisamutasi")
            ->where('bank', $request->ledger)
            ->whereBetween('tgl_ledger', [$tgl_mulai, $request->dari])
            ->first();

        $saldoawal = $sa + $mutasi->sisamutasi;
        return view('ledger.index', compact('bank', 'ledger', 'saldoawal'));
    }


    public function create($kode_ledger)
    {
        $coa = DB::table('coa')->orderBy('kode_akun')->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('ledger.create', compact('kode_ledger', 'coa', 'cabang'));
    }

    public function storetemp(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $status_dk = $request->status_dk;
        $tgl_ledger = $request->tgl_ledger;
        $pelanggan = $request->pelanggan;
        $keterangan = $request->keterangan;
        $jumlah  = str_replace(".", "", $request->jumlah);
        $kode_akun = $request->kode_akun;
        $peruntukan = $request->peruntukan;
        $id_user = Auth::user()->id;
        $data = array(
            'tgl_ledger'   => $tgl_ledger,
            'pelanggan'    => $pelanggan,
            'keterangan'   => $keterangan,
            'jumlah'       => $jumlah,
            'kode_akun'    => $kode_akun,
            'status_dk'    => $status_dk,
            'peruntukan'   => $peruntukan,
            'ket_peruntukan'  => $kode_cabang,
            'id_user' => $id_user
        );

        $simpan = DB::table('ledger_temp')->insert($data);
        if ($simpan) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function getledgertemp()
    {
        $id_user = Auth::user()->id;
        $ledgertemp = DB::table('ledger_temp')->where('id_user', $id_user)->join('coa', 'ledger_temp.kode_akun', '=', 'coa.kode_akun')->get();
        return view('ledger.getledgertemp', compact('ledgertemp'));
    }

    public function cekledgertemp(Request $request)
    {
        $id_user = Auth::user()->id;
        $cek = DB::table('ledger_temp')->where('id_user', $id_user)->count();
        echo $cek;
    }

    public function deletetemp(Request $request)
    {
        $hapus = DB::table('ledger_temp')->where('id', $request->id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }
}
