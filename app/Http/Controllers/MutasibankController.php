<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Ledger;
use Illuminate\Http\Request;

class MutasibankController extends Controller
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
        $query->where('ledger_bank.bank', $request->bank);
        $mutasibank = $query->get();
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('mutasibank.index', compact('cabang', 'mutasibank'));
    }
}
