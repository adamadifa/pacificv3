<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Kontrabonangkutan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KontrabonangkutanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kontrabonangkutan::query();
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_kontrabon', [$request->dari, $request->sampai]);
        }
        $query->orderBy('tgl_kontrabon', 'desc');
        $kontrabon = $query->paginate(15);
        $kontrabon->appends($request->all());
        return view('kontrabonangkutan.index', compact('kontrabon'));
    }

    public function show($no_kontrabon)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $kontrabon = DB::table('kontrabon_angkutan')->where('no_kontrabon', $no_kontrabon)->first();
        $detail = DB::table('detail_kontrabon_angkutan')
            ->join('angkutan', 'detail_kontrabon_angkutan.no_surat_jalan', '=', 'angkutan.no_surat_jalan')
            ->where('no_kontrabon', $no_kontrabon)->get();

        return view('kontrabonangkutan.show', compact('kontrabon', 'detail'));
    }

    public function delete($no_kontrabon)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $detail = DB::table('detail_kontrabon_angkutan')->where('no_kontrabon', $no_kontrabon)->get();
        foreach ($detail as $d) {
            $no_surat_jalan[] = $d->no_surat_jalan;
        }
        DB::beginTransaction();
        try {
            DB::table('kontrabon_angkutan')->where('no_kontrabon', $no_kontrabon)->delete();
            DB::table('angkutan')->whereIn('no_surat_jalan', $no_surat_jalan)->update(['tgl_kontrabon' => null]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data  Berhasil di Hapus']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data  Gagal di Hapus, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        return view('kontrabonangkutan.create');
    }

    public function getnosuratjalan()
    {
        $sj = DB::table('angkutan')
            ->select('angkutan.no_surat_jalan', 'tgl_input', 'tujuan', 'nopol', 'angkutan', 'tarif', 'tepung', 'bs')
            ->leftJoin('detail_kontrabon_angkutan', 'angkutan.no_surat_jalan', '=', 'detail_kontrabon_angkutan.no_surat_jalan')
            ->whereNull('detail_kontrabon_angkutan.no_surat_jalan')
            ->where('angkutan.tarif', '!=', 0)
            ->get();
        echo "<option value=''>No. SJ / Tgl SJ / Angkutan / Tujuan / Tarif / Tepung /Bs</option>";
        foreach ($sj as $d) {
            $tanggal = date("d-m-Y", strtotime($d->tgl_input));
            echo "<option value='$d->no_surat_jalan'>" . $d->no_surat_jalan . "/" . $tanggal . "/" . $d->angkutan . "/" . $d->tujuan . "/" . rupiah($d->tarif) . "/" . rupiah($d->tepung) . "/" . rupiah($d->bs) . "</option>";
        }
    }

    public function storetemp(Request $request)
    {
        $no_surat_jalan = $request->no_surat_jalan;
        $data = [
            'no_surat_jalan' => $no_surat_jalan,
            'id_admin' => Auth::user()->id
        ];
        $cek = DB::table('detail_kontrabon_angkutan_temp')->where('no_surat_jalan', $no_surat_jalan)
            ->where('id_admin', Auth::user()->id)->count();
        if ($cek > 0) {
            echo 1;
        } else {
            $simpan = DB::table('detail_kontrabon_angkutan_temp')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function showtemp()
    {
        $detailtemp = DB::table('detail_kontrabon_angkutan_temp')
            ->join('angkutan', 'detail_kontrabon_angkutan_temp.no_surat_jalan', '=', 'angkutan.no_surat_jalan')
            ->where('id_admin', Auth::user()->id)
            ->get();
        return view('kontrabonangkutan.showtemp', compact('detailtemp'));
    }

    public function deletetemp(Request $request)
    {
        $no_surat_jalan = $request->no_surat_jalan;
        $hapus = DB::table('detail_kontrabon_angkutan_temp')->where('no_surat_jalan', $no_surat_jalan)
            ->where('id_admin', Auth::user()->id)
            ->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function cektemp()
    {
        $cek = DB::table('detail_kontrabon_angkutan_temp')->where('id_admin', Auth::user()->id)->count();
        echo $cek;
    }

    public function store(Request $request)
    {
        $tgl_kontrabon = $request->tgl_kontrabon;
        $angkutan = $request->angkutan;
        $tanggal = explode("-", $tgl_kontrabon);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $blnthn = $bulan . substr($tahun, 2, 2);
        $kontrabon = DB::table('kontrabon_angkutan')
            ->select('no_kontrabon')
            ->whereRaw('MID(no_kontrabon,4,4)=' . $blnthn)
            ->orderBy('no_kontrabon', 'desc')
            ->first();
        $last_nokontrabon = $kontrabon != null ? $kontrabon->no_kontrabon : '';
        $no_kontrabon = buatkode($last_nokontrabon, 'KA/' . $blnthn . "/", 3);
        $detail = DB::table('detail_kontrabon_angkutan_temp')->where('id_admin', Auth::user()->id)->get();
        $data = [
            'no_kontrabon' => $no_kontrabon,
            'tgl_kontrabon' => $tgl_kontrabon,
            'keterangan' => $angkutan
        ];

        DB::beginTransaction();
        try {
            DB::table('kontrabon_angkutan')->insert($data);
            foreach ($detail as $d) {
                $datadetail = [
                    'no_kontrabon' => $no_kontrabon,
                    'no_surat_jalan' => $d->no_surat_jalan
                ];
                DB::table('detail_kontrabon_angkutan')->insert($datadetail);
                DB::table('angkutan')->where('no_surat_jalan', $d->no_surat_jalan)->update(['tgl_kontrabon' => $tgl_kontrabon]);
            }

            DB::table('detail_kontrabon_angkutan_temp')->where('id_admin', Auth::user()->id)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data  Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data  Gagal di Simpan, Hubungi Tim IT']);
        }
    }

    public function proses($no_kontrabon)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $kontrabon = DB::table('kontrabon_angkutan')->where('no_kontrabon', $no_kontrabon)->first();
        $detail = DB::table('detail_kontrabon_angkutan')
            ->join('angkutan', 'detail_kontrabon_angkutan.no_surat_jalan', '=', 'angkutan.no_surat_jalan')
            ->where('no_kontrabon', $no_kontrabon)->get();
        $bank = Bank::orderBy('kode_bank')->get();
        return view('kontrabonangkutan.proses', compact('kontrabon', 'detail', 'bank'));
    }

    public function proseskontrabon(Request $request)
    {
        $tgl_ledger = $request->tgl_ledger;
        $cbg = 'PST';
        $pelanggan = $request->pelanggan;
        $no_ref = $request->no_ref;
        $pelanggan = $request->pelanggan;
        $kode_bank = $request->kode_bank;
        $keterangan = $no_ref . "/" . $request->keterangan;
        $no_kontrabon = $request->no_kontrabon;
        $tanggal = explode("-", $tgl_ledger);
        $thn = $tanggal[0];
        $tahun = substr($tanggal[0], 2, 2);
        $bulan = $tanggal[1];
        $bank = DB::table('master_bank')->where('kode_bank', $kode_bank)->first();
        $kode_akun_bank = $bank->kode_akun;

        $ledger = DB::table('ledger_bank')->select('no_bukti')
            ->whereRaw('LEFT(no_bukti,7)="LR' . $cbg . $tahun . '"')
            ->whereRaw('LENGTH(no_bukti)=12')
            ->orderBy('no_bukti', 'desc')->first();
        if ($ledger != null) {
            $lastno_bukti = $ledger->no_bukti;
        } else {
            $lastno_bukti = "";
        }
        $no_bukti = buatkode($lastno_bukti, 'LR' . $cbg . $tahun, 4);

        $bukubesar = DB::table('buku_besar')->whereRaw('LEFT(no_bukti,6)="GJ' . $bulan . $tahun . '"')
            ->orderBy('no_bukti', 'desc')
            ->first();
        if ($bukubesar != null) {
            $last_no_bukti_bukubesar = $bukubesar->no_bukti;
        } else {
            $last_no_bukti_bukubesar = "";
        }

        // echo $last_no_bukti_bukubesar;


        $nobukti_bukubesar_angkutan = buatkode($last_no_bukti_bukubesar, 'GJ' . $bulan . $tahun, 6);
        $nobukti_bukubesar_bank_angkutan = buatkode($nobukti_bukubesar_angkutan, 'GJ' . $bulan . $tahun, 6);

        $nobukti_bukubesar_hutang = buatkode($nobukti_bukubesar_bank_angkutan, 'GJ' . $bulan . $tahun, 6);
        $nobukti_bukubesar_bank_hutang = buatkode($nobukti_bukubesar_hutang, 'GJ' . $bulan . $tahun, 6);


        $kontrabon = DB::table('detail_kontrabon_angkutan')
            ->selectRaw("
            SUM(IF(MONTH(tgl_mutasi_gudang)='$bulan' AND YEAR(tgl_mutasi_gudang)='$thn',(tarif+bs+tepung),0)) as jmlangkutan,
            SUM(IF(MONTH(tgl_mutasi_gudang)!='$bulan' AND YEAR(tgl_mutasi_gudang)<='$thn',(tarif+bs+tepung),0)) as jmlhutang")
            ->join('angkutan', 'detail_kontrabon_angkutan.no_surat_jalan', '=', 'angkutan.no_surat_jalan')
            ->join('mutasi_gudang_jadi', 'angkutan.no_surat_jalan', '=', 'mutasi_gudang_jadi.no_dok')
            ->where('no_kontrabon', $no_kontrabon)
            ->first();

        $detail = DB::table('detail_kontrabon_angkutan')->where('no_kontrabon', $no_kontrabon)->get();
        foreach ($detail as $d) {
            $no_surat_jalan[] = $d->no_surat_jalan;
        }
        if ($kontrabon != null) {
            $jmlangkutan = $kontrabon->jmlangkutan;
            $jmlhutang = $kontrabon->jmlhutang;
        } else {
            $jmlangkutan = 0;
            $jmlhutang = 0;
        }


        DB::beginTransaction();
        try {
            if (!empty($jmlangkutan)) {
                $data = array(
                    'no_bukti'            => $no_bukti,
                    'tgl_ledger'          => $tgl_ledger,
                    'bank'                => $kode_bank,
                    'no_ref'              => $no_kontrabon,
                    'pelanggan'           => $pelanggan,
                    'keterangan'          => $keterangan,
                    'kode_akun'           => '6-1114',
                    'jumlah'              => $jmlangkutan,
                    'status_validasi'     => 1,
                    'status_dk'           => 'D',
                    'peruntukan'          => 'MP',
                    'ket_peruntukan'      => 'PST',
                    'kategori' => 'GDJ',
                    'nobukti_bukubesar' => $nobukti_bukubesar_angkutan,
                    'nobukti_bukubesar_2' => $nobukti_bukubesar_bank_angkutan
                );

                $databukubesar = array(
                    'no_bukti' => $nobukti_bukubesar_angkutan,
                    'tanggal' => $tgl_ledger,
                    'sumber' => 'ledger',
                    'keterangan' => $keterangan,
                    'kode_akun' => '6-1114',
                    'debet' => $jmlangkutan,
                    'kredit' => 0,
                    'nobukti_transaksi' => $no_bukti
                );


                $databukubesarbank = array(
                    'no_bukti' => $nobukti_bukubesar_bank_angkutan,
                    'tanggal' => $tgl_ledger,
                    'sumber' => 'ledger',
                    'keterangan' => $keterangan,
                    'kode_akun' => $kode_akun_bank,
                    'debet' => 0,
                    'kredit' => $jmlangkutan,
                    'nobukti_transaksi' => $no_bukti
                );
                DB::table('buku_besar')->insert($databukubesar);
                DB::table('buku_besar')->insert($databukubesarbank);
                DB::table('ledger_bank')->insert($data);

                echo "1";
            }

            if (!empty($jmlhutang)) {

                $no_bukti_hutang = buatkode($no_bukti, 'LR' . $cbg . $tahun, 4);
                $data = array(
                    'no_bukti'            => $no_bukti_hutang,
                    'tgl_ledger'          => $tgl_ledger,
                    'no_ref'              => $no_kontrabon,
                    'bank'                => $kode_bank,
                    'pelanggan'           => $pelanggan,
                    'keterangan'          => $keterangan,
                    'kode_akun'           => '2-1800',
                    'jumlah'              => $jmlhutang,
                    'status_validasi'     => 1,
                    'status_dk'           => 'D',
                    'peruntukan'          => 'MP',
                    'ket_peruntukan'      => 'PST',
                    'kategori' => 'GDJ',
                    'nobukti_bukubesar' => $nobukti_bukubesar_hutang,
                    'nobukti_bukubesar_2' => $nobukti_bukubesar_bank_hutang
                );
                $databukubesar = array(
                    'no_bukti' => $nobukti_bukubesar_hutang,
                    'tanggal' => $tgl_ledger,
                    'sumber' => 'ledger',
                    'keterangan' => $keterangan,
                    'kode_akun' => '2-1800',
                    'debet' => $jmlangkutan,
                    'kredit' => 0,
                    'nobukti_transaksi' => $no_bukti
                );


                $databukubesarbank = array(
                    'no_bukti' => $nobukti_bukubesar_bank_hutang,
                    'tanggal' => $tgl_ledger,
                    'sumber' => 'ledger',
                    'keterangan' => $keterangan,
                    'kode_akun' => $kode_akun_bank,
                    'debet' => 0,
                    'kredit' => $jmlangkutan,
                    'nobukti_transaksi' => $no_bukti
                );
                DB::table('buku_besar')->insert($databukubesar);
                DB::table('buku_besar')->insert($databukubesarbank);
                DB::table('ledger_bank')->insert($data);

                echo "2";
            }

            $dataangkutan = [
                'tgl_bayar' => $tgl_ledger
            ];

            DB::table('angkutan')->whereIn('no_surat_jalan', $no_surat_jalan)->update($dataangkutan);
            DB::table('kontrabon_angkutan')->where('no_kontrabon', $no_kontrabon)->update(['status' => 1]);
            //die;
            DB::commit();
            //die;
            return Redirect::back()->with(['success' => 'Data Kontrabon Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Kontrabon Gagal di Simpan']);
        }
    }

    public function batalkan($no_kontrabon)
    {
        $no_kontrabon = Crypt::decrypt($no_kontrabon);
        $detail = DB::table('detail_kontrabon_angkutan')->where('no_kontrabon', $no_kontrabon)->get();
        $ledger = DB::table('ledger_bank')->where('no_ref', $no_kontrabon)->first();
        $nobukti_bukubesar = $ledger != null ? $ledger->nobukti_bukubesar : '';
        $nobukti_bukubesar_bank = $ledger != null ? $ledger->nobukti_bukubesar_2 : '';
        foreach ($detail as $d) {
            $no_surat_jalan[] = $d->no_surat_jalan;
        }
        DB::beginTransaction();
        try {
            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar)->delete();
            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar_bank)->delete();
            DB::table('ledger_bank')->where('no_ref', $no_kontrabon)->delete();
            DB::table('kontrabon_angkutan')->where('no_kontrabon', $no_kontrabon)->update(['status' => null]);
            DB::table('angkutan')->whereIn('no_surat_jalan', $no_surat_jalan)->update(['tgl_bayar' => null]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Kontrabon Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Kontrabon Gagal di Simpan']);
        }
    }
}
