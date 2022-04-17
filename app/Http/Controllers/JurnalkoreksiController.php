<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\Jurnalkoreksi;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class JurnalkoreksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Jurnalkoreksi::query();
        $query->select('jurnal_koreksi.*', 'nama_barang', 'nama_akun');
        $query->join('pembelian', 'jurnal_koreksi.nobukti_pembelian', '=', 'pembelian.nobukti_pembelian');

        $query->join('master_barang_pembelian', 'jurnal_koreksi.kode_barang', '=', 'master_barang_pembelian.kode_barang');
        $query->join('coa', 'jurnal_koreksi.kode_akun', '=', 'coa.kode_akun');
        $query->whereBetween('tgl_jurnalkoreksi', [$request->dari, $request->sampai]);
        $query->orderBy('tgl_jurnalkoreksi');
        $query->orderBy('nobukti_pembelian');

        $jurnalkoreksi = $query->paginate(15);
        $jurnalkoreksi->appends($request->all());

        return view('jurnalkoreksi.index', compact('jurnalkoreksi'));
    }

    public function create()
    {
        $coa = Coa::orderBy('kode_akun')->get();
        $supplier = Supplier::orderBy('nama_supplier')->get();
        return view('jurnalkoreksi/create', compact('supplier', 'coa'));
    }


    public function store(Request $request)
    {
        $tgl_jurnalkoreksi = $request->tgl_jurnalkoreksi;
        $kode_supplier = $request->kode_supplier;
        $nobukti_pembelian = Crypt::decrypt($request->nobukti_pembelian);
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty = !empty($request->qty) ? $request->qty : 0;
        $qty = str_replace(",", ".", $qty);
        $harga = !empty($request->harga) ? str_replace(".", "", $request->harga) : 0;
        $harga = str_replace(",", ".", $harga);
        $kode_akun_debet = $request->kode_akun_debet;
        $kode_akun_kredit = $request->kode_akun_kredit;
        $totalharga = $qty * $harga;
        $tanggal = explode("-", $tgl_jurnalkoreksi);
        $tahun  = substr($tanggal[0], 2, 2);
        $bulan = $tanggal[1];

        $jurnalkoreksi = DB::table('jurnal_koreksi')
            ->select('kode_jk')
            ->whereRaw('LEFT(kode_jk,6)="JK' . $tahun . $bulan . '"')
            ->orderBy('kode_jk', 'desc')
            ->first();

        if ($jurnalkoreksi != null) {
            $last_kode_jk = $jurnalkoreksi->kode_jk;
        } else {
            $last_kode_jk = "";
        }
        $kode_jk_debet = buatkode($last_kode_jk, 'JK' . $tahun . $bulan, 3);
        $kode_jk_kredit = buatkode($kode_jk_debet, 'JK' . $tahun . $bulan, 3);


        $bukubesar = DB::table('buku_besar')->whereRaw('LEFT(no_bukti,6)="GJ' . $bulan . $tahun . '"')
            ->orderBy('no_bukti', 'desc')
            ->first();
        if ($bukubesar != null) {
            $last_no_bukti_bukubesar = $bukubesar->no_bukti;
        } else {
            $last_no_bukti_bukubesar = "";
        }

        $nobukti_bukubesar_debet = buatkode($last_no_bukti_bukubesar, 'GJ' . $bulan . $tahun, 4);
        $nobukti_bukubesar_kredit = buatkode($nobukti_bukubesar_debet, 'GJ' . $bulan . $tahun, 4);
        $datadebet = [
            'kode_jk' => $kode_jk_debet,
            'tgl_jurnalkoreksi' => $tgl_jurnalkoreksi,
            'nobukti_pembelian' => $nobukti_pembelian,
            'kode_barang' => $kode_barang,
            'harga' => $harga,
            'qty' => $qty,
            'keterangan' => $keterangan,
            'status_dk' => 'D',
            'kode_akun' => $kode_akun_debet,
            'nobukti_bukubesar' => $nobukti_bukubesar_debet
        ];

        $datakredit = [
            'kode_jk' => $kode_jk_kredit,
            'tgl_jurnalkoreksi' => $tgl_jurnalkoreksi,
            'nobukti_pembelian' => $nobukti_pembelian,
            'kode_barang' => $kode_barang,
            'harga' => $harga,
            'qty' => $qty,
            'keterangan' => $keterangan,
            'status_dk' => 'K',
            'kode_akun' => $kode_akun_kredit,
            'nobukti_bukubesar' => $nobukti_bukubesar_kredit
        ];

        $databukubesar_debet = array(
            'no_bukti' => $nobukti_bukubesar_debet,
            'tanggal' => $tgl_jurnalkoreksi,
            'sumber' => 'Jurnal Koreksi Pembelian',
            'keterangan' => $keterangan,
            'kode_akun' => $kode_akun_debet,
            'debet' => $totalharga,
            'kredit' => 0,
            'nobukti_transaksi' => $kode_jk_debet
        );

        $databukubesar_kredit = array(
            'no_bukti' => $nobukti_bukubesar_kredit,
            'tanggal' => $tgl_jurnalkoreksi,
            'sumber' => 'Jurnal Koreksi Pembelian',
            'keterangan' => $keterangan,
            'kode_akun' => $kode_akun_kredit,
            'debet' => 0,
            'kredit' => $totalharga,
            'nobukti_transaksi' => $kode_jk_kredit
        );
        DB::beginTransaction();
        try {
            DB::table('jurnal_koreksi')->insert($datakredit);
            DB::table('jurnal_koreksi')->insert($datadebet);
            DB::table('buku_besar')->insert($databukubesar_debet);
            DB::table('buku_besar')->insert($databukubesar_kredit);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            //dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
            DB::rollback();
        }
    }

    public function delete($kode_jk)
    {
        $kode_jk = Crypt::decrypt($kode_jk);
        $jurnalkoreksi = DB::table('jurnal_koreksi')->where('kode_jk', $kode_jk)->first();
        $nobukti_pembelian = $jurnalkoreksi->nobukti_pembelian;
        $kode_barang = $jurnalkoreksi->kode_barang;
        $keterangan = $jurnalkoreksi->keterangan;
        $jk = DB::table('jurnal_koreksi')->where('nobukti_pembelian', $nobukti_pembelian)
            ->where('kode_barang', $kode_barang)->where('keterangan', $keterangan)->get();

        DB::beginTransaction();
        try {
            foreach ($jk as $d) {
                DB::table('jurnal_koreksi')->where('kode_jk', $d->kode_jk)->delete();
                DB::table('buku_besar')->where('no_bukti', $d->nobukti_bukubesar)->delete();
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            //dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
            DB::rollback();
        }
    }
}
