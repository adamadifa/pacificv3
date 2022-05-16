<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Jurnalumum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class JurnalumumController extends Controller
{
    public function index(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $kode_dept = $request->kode_dept;
        $query = Jurnalumum::query();
        $query->join('coa', 'jurnal_umum.kode_akun', '=', 'coa.kode_akun');
        $query->whereBetween('tanggal', [$dari, $sampai]);
        $query->where('kode_dept', $kode_dept);
        $jurnalumum = $query->get();
        if (Auth::user()->level == "general affair") {
            $departemen = DB::table('departemen')
                ->where('kode_dept', 'GAF')
                ->where('status_pengajuan', 1)->get();
        } else {
            $departemen = DB::table('departemen')->where('status_pengajuan', 1)->get();
        }
        return view('jurnalumum.index', compact('jurnalumum', 'departemen'));
    }

    public function create($kode_dept)
    {

        $coa = Coa::orderBy('kode_akun')->get();
        return view('jurnalumum/create', compact('coa', 'kode_dept'));
    }

    public function store(Request $request)
    {
        $tanggal = $request->tanggal;
        $keterangan = $request->keterangan;
        $jumlah = !empty($request->jumlah) ? str_replace(".", "", $request->jumlah) : 0;
        $jumlah = str_replace(",", ".", $jumlah);
        $kode_akun = $request->kode_akun;
        $tgl = explode("-", $tanggal);
        $tahun  = substr($tgl[0], 2, 2);
        $bulan = $tgl[1];
        $status_dk = $request->status_dk;
        $kode_dept = $request->kode_dept;
        $jurnalumum = DB::table('jurnal_umum')
            ->select('kode_jurnal')
            ->whereRaw('LEFT(kode_jurnal,6)="JL' . $tahun . $bulan . '"')
            ->orderBy('kode_jurnal', 'desc')
            ->first();

        if ($jurnalumum != null) {
            $last_kode_jl = $jurnalumum->kode_jurnal;
        } else {
            $last_kode_jl = "";
        }
        $kode_jurnal = buatkode($last_kode_jl, 'JL' . $tahun . $bulan, 3);


        $bukubesar = DB::table('buku_besar')->whereRaw('LEFT(no_bukti,6)="GJ' . $bulan . $tahun . '"')
            ->orderBy('no_bukti', 'desc')
            ->first();
        if ($bukubesar != null) {
            $last_no_bukti_bukubesar = $bukubesar->no_bukti;
        } else {
            $last_no_bukti_bukubesar = "";
        }

        $nobukti_bukubesar = buatkode($last_no_bukti_bukubesar, 'GJ' . $bulan . $tahun, 4);
        $data = [
            'kode_jurnal' => $kode_jurnal,
            'tanggal' => $tanggal,
            'jumlah' => $jumlah,
            'keterangan' => $keterangan,
            'status_dk' => 'D',
            'kode_akun' => $kode_akun,
            'status_dk' => $status_dk,
            'nobukti_bukubesar' => $nobukti_bukubesar,
            'kode_dept' => $kode_dept
        ];

        if ($status_dk == "D") {
            $debet = $jumlah;
            $kredit = 0;
        } else {
            $debet = 0;
            $kredit = $jumlah;
        }
        $databukubesar = array(
            'no_bukti' => $nobukti_bukubesar,
            'tanggal' => $tanggal,
            'sumber' => 'Jurnal Umum ' . $kode_dept,
            'keterangan' => $keterangan,
            'kode_akun' => $kode_akun,
            'debet' => $debet,
            'kredit' => $kredit,
            'nobukti_transaksi' => $kode_jurnal
        );

        DB::beginTransaction();
        try {
            DB::table('jurnal_umum')->insert($data);
            DB::table('buku_besar')->insert($databukubesar);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
            DB::rollback();
        }
    }

    public function delete($kode_jurnal)
    {
        $kode_jurnal = Crypt::decrypt($kode_jurnal);
        //$jurnalumum = DB::table('jurnal_umum')->where('kode_jurnal', $kode_jurnal)->first();
        $jl = DB::table('jurnal_umum')->where('kode_jurnal', $kode_jurnal)->first();
        $nobukti_bukubesar = $jl->nobukti_bukubesar;
        DB::beginTransaction();
        try {
            DB::table('jurnal_umum')->where('kode_jurnal', $kode_jurnal)->delete();
            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            //dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
            DB::rollback();
        }
    }
}