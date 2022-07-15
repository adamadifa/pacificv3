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
        if ($kode_dept != 'ALL') {
            $query->where('kode_dept', $kode_dept);
        }
        if ($request->kode_cabang) {
            $query->where('kode_cabang', $request->kode_cabang);
        }

        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $jurnalumum = $query->get();
        return view('jurnalumum.index', compact('jurnalumum', 'cabang'));
    }

    public function create()
    {
        $ga = [
            '5-2401',
            '6-2301',
            '6-2303',
            '6-1305',
            '5-2402',
            '5-2403',
            '6-2302',
            '1-2220',
            '1-2720',
            '1-2320',
            '1-2420',
            '1-2620',
        ];

        $hrd = [
            '6-2101',
            '5-1301',
            '5-2101',
            '2-3100',
            '8-5000',
            '2-1600',
            '2-1700',
            '2-1400',
            '2-1500',
            '2-3100',
            '1-1451',
            '8-5000',
            '2-1600',
            '2-1700',
            '2-1400',
            '2-1500'
        ];
        if (Auth::user()->level == "hrd") {
            $coa = Coa::orderBy('kode_akun')->whereIn('kode_akun', $hrd)->get();
        } else if (Auth::user()->level == "general affair") {
            $coa = Coa::orderBy('kode_akun')->whereIn('kode_akun', $ga)->get();
        } else {
            $coa = Coa::orderBy('kode_akun')->get();
        }
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('jurnalumum/create', compact('coa', 'cabang'));
    }

    public function storetemp(Request $request)
    {
        $tanggal = $request->tanggal;
        $peruntukan = $request->peruntukan;
        $kode_cabang = $request->kode_cabang;
        $kode_dept = $request->kode_dept;
        $kode_akun = $request->kode_akun;
        $status_dk = $request->status_dk;
        $jumlah = !empty($request->jumlah) ? str_replace(".", "", $request->jumlah) : 0;
        $jumlah = str_replace(",", ".", $jumlah);
        $keterangan = $request->keterangan;
        $data = [
            'tanggal' => $tanggal,
            'peruntukan' => $peruntukan,
            'kode_cabang' => $kode_cabang,
            'kode_dept' => $kode_dept,
            'kode_akun' => $kode_akun,
            'status_dk' => $status_dk,
            'jumlah' => $jumlah,
            'keterangan' => $keterangan,
            'id_user' => Auth::user()->id
        ];
        $simpan = DB::table('jurnal_umum_temp')->insert($data);
        if ($simpan) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function cektemp(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $cektemp = DB::table('jurnal_umum_temp')->where('kode_dept', $kode_dept)->count();
        echo $cektemp;
    }

    public function showtemp()
    {
        $id_user = Auth::user()->id;
        $jurnaltemp = DB::table('jurnal_umum_temp')
            ->where('id_user', $id_user)
            ->join('coa', 'jurnal_umum_temp.kode_akun', '=', 'coa.kode_akun')->get();
        return view('jurnalumum.showtemp', compact('jurnaltemp'));
    }

    public function deletetemp(Request $request)
    {
        $hapus = DB::table('jurnal_umum_temp')->where('id', $request->id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }
    public function store(Request $request)
    {
        $cabang = ['BDG', 'BGR', 'GRT', 'PWT', 'SMR', 'SKB', 'SBY', 'TSM', 'TGL', 'PST', 'KLT'];
        $kode_dept = $request->kode_dept;
        //$keterangan = $request->keterangan;



        $jurnaltemp = DB::table('jurnal_umum_temp')->where('kode_dept', $kode_dept)->get();
        DB::beginTransaction();
        try {
            foreach ($jurnaltemp as $d) {
                $kode_akun = $d->kode_akun;
                $status_dk = $d->status_dk;
                $jumlah = $d->jumlah;
                $tanggal = $d->tanggal;
                $peruntukan = $d->peruntukan;
                $kode_cabang = $d->kode_cabang;
                $tgl = explode("-", $tanggal);
                $keterangan = $d->keterangan;
                $tahun  = substr($tgl[0], 2, 2);
                $bulan = $tgl[1];
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

                $nobukti_bukubesar = buatkode($last_no_bukti_bukubesar, 'GJ' . $bulan . $tahun, 6);

                $cekakun = substr($kode_akun, 0, 3);
                if ($status_dk == 'D' and $cekakun == '6-1'  or $status_dk == 'D' and $cekakun == '6-2') {
                    $kode = "CR" . $bulan . $tahun;
                    $cr = DB::table('costratio_biaya')
                        ->select('kode_cr')
                        ->whereRaw('LEFT(kode_cr,6) ="' . $kode . '"')
                        ->orderBy('kode_cr', 'desc')
                        ->first();
                    if ($cr != null) {
                        $last_kode_cr = $cr->kode_cr;
                    } else {
                        $last_kode_cr = "";
                    }
                    $kode_cr = buatkode($last_kode_cr, "CR" . $bulan . $tahun, 4);

                    $datacr = [
                        'kode_cr' => $kode_cr,
                        'tgl_transaksi' => $tanggal,
                        'kode_akun'    => $kode_akun,
                        'keterangan'   => $keterangan,
                        'kode_cabang'  => $kode_cabang,
                        'id_sumber_costratio' => 5,
                        'jumlah' => $jumlah
                    ];

                    DB::table('costratio_biaya')->insert($datacr);
                } else {
                    $kode_cr = NULL;
                }
                $data = [
                    'kode_jurnal' => $kode_jurnal,
                    'tanggal' => $tanggal,
                    'jumlah' => $jumlah,
                    'keterangan' => $keterangan,
                    'kode_akun' => $kode_akun,
                    'status_dk' => $status_dk,
                    'nobukti_bukubesar' => $nobukti_bukubesar,
                    'kode_dept' => $kode_dept,
                    'peruntukan' => $peruntukan,
                    'kode_cabang' => $kode_cabang,
                    'kode_cr' => $kode_cr,
                    'id_user' => Auth::user()->id
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

                DB::table('jurnal_umum')->insert($data);
                DB::table('buku_besar')->insert($databukubesar);
            }
            DB::table('jurnal_umum_temp')->where('kode_dept', $kode_dept)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
            DB::rollback();
        }
    }

    public function edit($kodejurnal)
    {
        $kodejurnal = Crypt::decrypt($kodejurnal);
        $jurnalumum = DB::table('jurnal_umum')
            ->where('kode_jurnal', $kodejurnal)->first();
        $coa = Coa::orderBy('kode_akun')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('jurnalumum.edit', compact('jurnalumum', 'coa', 'cabang'));
    }

    public function update($kode_jurnal, Request $request)
    {
        $kode_jurnal = Crypt::decrypt($kode_jurnal);
        $jurnalumum = DB::table('jurnal_umum')->where('kode_jurnal', $kode_jurnal)->first();
        $nobukti_bukubesar = $jurnalumum->nobukti_bukubesar;
        $kode_cr = $jurnalumum->kode_cr;
        $status_dk = $jurnalumum->status_dk;
        $kode_akun = $request->kode_akun;
        $keterangan = $request->keterangan;
        $jumlah = !empty($request->jumlah) ? str_replace(".", "", $request->jumlah) : 0;
        $jumlah = str_replace(",", ".", $jumlah);
        $peruntukan = $request->peruntukan;
        $kode_cabang = $request->kode_cabang;
        $data = [
            'kode_akun' => $kode_akun,
            'keterangan' => $keterangan,
            'jumlah' => $jumlah,
            'peruntukan' => $peruntukan,
            'kode_cabang' => $kode_cabang
        ];

        if ($status_dk == "D") {
            $databukubesar = [
                'kode_akun' => $kode_akun,
                'keterangan' => $keterangan,
                'debet' => $jumlah
            ];
        } else {
            $databukubesar = [
                'kode_akun' => $kode_akun,
                'keterangan' => $keterangan,
                'kredit' => $jumlah
            ];
        }

        $datacr = [
            'kode_akun' => $kode_akun,
            'keterangan' => $keterangan,
            'jumlah' => $jumlah
        ];

        DB::beginTransaction();
        try {
            DB::table('jurnal_umum')->where('kode_jurnal', $kode_jurnal)->update($data);
            DB::table('costratio_biaya')->where('kode_cr', $kode_cr)->update($datacr);
            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar)->update($databukubesar);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            //dd($e);
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
        $kode_cr = $jl->kode_cr;
        DB::beginTransaction();
        try {
            DB::table('jurnal_umum')->where('kode_jurnal', $kode_jurnal)->delete();
            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar)->delete();
            DB::table('costratio_biaya')->where('kode_cr', $kode_cr)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            //dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
            DB::rollback();
        }
    }
}
