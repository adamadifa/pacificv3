<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Kaskecil;
use App\Models\Setcoacabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class KaskecilController extends Controller
{
    protected $cabang;
    public function __construct()
    {
        // Fetch the Site Settings object
        $this->middleware(function ($request, $next) {
            $this->cabang = Auth::user()->kode_cabang;
            return $next($request);
        });


        View::share('cabang', $this->cabang);
    }
    public function index(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query = Kaskecil::query();
            $query->selectRaw('id,nobukti,tgl_kaskecil,kaskecil_detail.keterangan,kaskecil_detail.jumlah,kaskecil_detail.kode_akun,status_dk,nama_akun,kaskecil_detail.kode_klaim,klaim.keterangan as ket_klaim,no_ref,
            costratio_biaya.kode_cr, kaskecil_detail.kode_cr as kk_cr,peruntukan');
            $query->join('coa', 'kaskecil_detail.kode_akun', '=', 'coa.kode_akun');
            $query->leftJoin('klaim', 'kaskecil_detail.kode_klaim', '=', 'klaim.kode_klaim');
            $query->leftJoin('costratio_biaya', 'kaskecil_detail.kode_cr', '=', 'costratio_biaya.kode_cr');
            $query->whereBetween('tgl_kaskecil', [$request->dari, $request->sampai]);
            $query->where('kaskecil_detail.kode_cabang', $kode_cabang);
            if (!empty($request->nobukti)) {
                $query->where('nobukti', $request->nobukti);
            }
            $query->orderBy('tgl_kaskecil');
            $query->orderBy('order');
            $query->orderBy('nobukti');
            $kaskecil = $query->get();

            $qsaldoawal = Kaskecil::query();
            $qsaldoawal->selectRaw("SUM(IF( `status_dk` = 'K', jumlah, 0)) -SUM(IF( `status_dk` = 'D', jumlah, 0)) as saldo_awal");
            $qsaldoawal->where('tgl_kaskecil', '<', $request->dari);
            $qsaldoawal->where('kode_cabang', $kode_cabang);
            $saldoawal = $qsaldoawal->first();
        } else {
            $kaskecil = null;
            $saldoawal = null;
        }


        $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->first();
        if ($this->cabang !== "PCF") {
            if (Auth::user()->level == "kepala admin") {
                $cabang = Cabang::where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
            } else {
                $cabang = Cabang::where('kode_cabang', $this->cabang)->get();
            }
        } else {
            $cabang = Cabang::orderBy('kode_cabang')->get();
        }
        return view('kaskecil.index', compact('kaskecil', 'cabang', 'saldoawal'));
    }

    public function create()
    {
        $qcoa = Setcoacabang::query();
        $qcoa->select('set_coa_cabang.kode_akun', 'nama_akun');
        $qcoa->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun');
        $qcoa->where('kategori', 'Kas Kecil');
        $qcoa->groupBy('kode_akun', 'nama_akun');
        if (Auth::user()->kode_cabang != "PCF") {
            $qcoa->where('kode_cabang', Auth::user()->kode_cabang);
        }
        $qcoa->orderBy('kode_akun');
        $coa = $qcoa->get();

        $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->first();
        // if ($this->cabang != "PCF") {
        //     $cabang = Cabang::orderBy('kode_cabang')
        //         ->where('kode_cabang', $this->cabang)
        //         ->orWhere('kode_cabang', $cbg->sub_cabang)
        //         ->get();
        // } else {
        //     $cabang = Cabang::orderBy('kode_cabang')->get();
        // }

        if ($this->cabang !== "PCF") {
            if (Auth::user()->level == "kepala admin") {
                $cabang = Cabang::where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
            } else {
                $cabang = Cabang::where('kode_cabang', $this->cabang)->get();
            }
        } else {
            $cabang = Cabang::orderBy('kode_cabang')->get();
        }
        return view('kaskecil.create', compact('coa', 'cabang'));
    }

    public function getkaskeciltemp(Request $request)
    {
        $kaskeciltemp = DB::table('kaskecil_detail_temp')->where('nobukti', $request->nobukti)->where('kode_cabang', $request->kode_cabang)
            ->join('coa', 'kaskecil_detail_temp.kode_akun', '=', 'coa.kode_akun')
            ->get();

        return view('kaskecil.getkaskeciltemp', compact('kaskeciltemp'));
    }

    public function storetemp(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $status_dk = $request->inout;
        $tgl_kaskecil = $request->tgl_kaskecil;
        $nobukti = $request->nobukti;
        $keterangan = $request->keterangan;
        $jumlah  = str_replace(".", "", $request->jumlah);
        $kode_akun = $request->kode_akun;
        $peruntukan = $request->peruntukan;
        $data = array(
            'tgl_kaskecil' => $tgl_kaskecil,
            'nobukti'      => $nobukti,
            'keterangan'   => $keterangan,
            'jumlah'       => $jumlah,
            'kode_akun'    => $kode_akun,
            'kode_cabang'  => $kode_cabang,
            'status_dk'    => $status_dk,
            'peruntukan'   => $peruntukan
        );

        $simpan = DB::table('kaskecil_detail_temp')->insert($data);
        if ($simpan) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function deletetemp(Request $request)
    {
        $hapus = DB::table('kaskecil_detail_temp')->where('id', $request->id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function cekkaskeciltemp(Request $request)
    {
        $cek = DB::table('kaskecil_detail_temp')->where('nobukti', $request->nobukti)->where('kode_cabang', $request->kode_cabang)->count();
        echo $cek;
    }

    public function store(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $nobukti = $request->nobukti;
        $akun = [
            'BDG' => '1-1102',
            'BGR' => '1-1103',
            'PST' => '1-1111',
            'TSM' => '1-1112',
            'SKB' => '1-1113',
            'PWT' => '1-1114',
            'TGL' => '1-1115',
            'SBY' => '1-1116',
            'SMR' => '1-1117',
            'KLT' => '1-1118',
            'GRT' => '1-1119'
        ];
        $kaskecil_temp = DB::table('kaskecil_detail_temp')->where('nobukti', $nobukti)->get();
        DB::beginTransaction();
        try {
            foreach ($kaskecil_temp as $t) {
                $tgltransaksi = explode("-", $t->tgl_kaskecil);
                $bulan = $tgltransaksi[1];
                $tahun = $tgltransaksi[0];
                $thn = substr($tahun, 2, 2);
                $cekakun = substr($t->kode_akun, 0, 3);
                $bukubesar = DB::table('buku_besar')->whereRaw('LEFT(no_bukti,6)="GJ' . $bulan . $thn . '"')
                    ->orderBy('no_bukti', 'desc')
                    ->first();
                if ($bukubesar != null) {
                    $last_no_bukti = $bukubesar->no_bukti;
                } else {
                    $last_no_bukti = "";
                }

                $no_bukti = buatkode($last_no_bukti, 'GJ' . $bulan . $thn, 6);
                $no_bukti_2 = buatkode($no_bukti, 'GJ' . $bulan . $thn, 6);
                if ($t->status_dk == 'D' and $cekakun == '6-1' and $t->peruntukan != 'MP' or $t->status_dk == 'D' and $cekakun == '6-2' and $t->peruntukan != 'MP') {
                    $kode = "CR" . $bulan . $thn;
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
                    $kode_cr = buatkode($last_kode_cr, "CR" . $bulan . $thn, 4);

                    $data = array(
                        'tgl_kaskecil' => $t->tgl_kaskecil,
                        'nobukti'      => $t->nobukti,
                        'keterangan'   => $t->keterangan,
                        'jumlah'       => $t->jumlah,
                        'kode_akun'    => $t->kode_akun,
                        'kode_cabang'  => $t->kode_cabang,
                        'status_dk'    => $t->status_dk,
                        'order'        => 2,
                        'peruntukan'   => $t->peruntukan,
                        'kode_cr'      => $kode_cr,
                        'nobukti_bukubesar' => $no_bukti,
                        'nobukti_bukubesar_2' => $no_bukti_2
                    );

                    $datacr = [
                        'kode_cr' => $kode_cr,
                        'tgl_transaksi' => $t->tgl_kaskecil,
                        'kode_akun'    => $t->kode_akun,
                        'keterangan'   => $t->keterangan,
                        'kode_cabang'  => $t->kode_cabang,
                        'id_sumber_costratio' => 1,
                        'jumlah' => $t->jumlah
                    ];

                    DB::table('kaskecil_detail')->insert($data);
                    DB::table('costratio_biaya')->insert($datacr);
                } else {
                    $data = array(
                        'tgl_kaskecil' => $t->tgl_kaskecil,
                        'nobukti'      => $nobukti,
                        'keterangan'   => $t->keterangan,
                        'jumlah'       => $t->jumlah,
                        'kode_akun'    => $t->kode_akun,
                        'kode_cabang'  => $t->kode_cabang,
                        'status_dk'    => $t->status_dk,
                        'peruntukan'   => $t->peruntukan,
                        'order'        => 2,
                        'nobukti_bukubesar' => $no_bukti,
                        'nobukti_bukubesar_2' => $no_bukti_2
                    );
                    DB::table('kaskecil_detail')->insert($data);
                }

                if ($t->status_dk == "D") {
                    $debet = $t->jumlah;
                    $kredit = 0;
                } else {
                    $debet = 0;
                    $kredit = $t->jumlah;
                }
                $databukubesar = array(
                    'no_bukti' => $no_bukti,
                    'tanggal' => $t->tgl_kaskecil,
                    'sumber' => 'Kas Kecil',
                    'keterangan' => $t->keterangan,
                    'kode_akun' => $akun[$kode_cabang],
                    'debet' => $kredit,
                    'kredit' => $debet,
                    'nobukti_transaksi' => $t->nobukti,
                );


                $databukubesartrans = array(
                    'no_bukti' => $no_bukti_2,
                    'tanggal' => $t->tgl_kaskecil,
                    'sumber' => 'Kas Kecil',
                    'keterangan' => $t->keterangan,
                    'kode_akun' => $t->kode_akun,
                    'debet' => $debet,
                    'kredit' => $kredit,
                    'nobukti_transaksi' => $t->nobukti,
                );

                DB::table('buku_besar')->insert($databukubesar);
                DB::table('buku_besar')->insert($databukubesartrans);
            }
            DB::table('kaskecil_detail_temp')->where('nobukti', $nobukti)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);;
        }
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $kaskecil = DB::table('kaskecil_detail')->where('id', $id)->first();
        $kode_cr = $kaskecil->kode_cr;
        $nobukti_bukubesar = $kaskecil->nobukti_bukubesar;
        $nobukti_bukubesar_2 = $kaskecil->nobukti_bukubesar_2;
        DB::beginTransaction();
        try {
            DB::table('kaskecil_detail')->where('id', $id)->delete();
            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar)->delete();
            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar_2)->delete();
            DB::table('costratio_biaya')->where('kode_cr', $kode_cr)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus Hubungi Tim IT']);;
        }
    }

    public function edit($id)
    {
        $qcoa = Setcoacabang::query();
        $qcoa->select('set_coa_cabang.kode_akun', 'nama_akun');
        $qcoa->join('coa', 'set_coa_cabang.kode_akun', '=', 'coa.kode_akun');
        $qcoa->where('kategori', 'Kas Kecil');
        $qcoa->groupBy('kode_akun', 'nama_akun');
        if (Auth::user()->kode_cabang != "PCF") {
            $qcoa->where('kode_cabang', Auth::user()->kode_cabang);
        }
        $qcoa->orderBy('kode_akun');
        $coa = $qcoa->get();
        $kaskecil = DB::table('kaskecil_detail')->where('id', $id)->first();
        return view('kaskecil.edit', compact('kaskecil', 'coa'));
    }

    public function update($id, Request $request)
    {
        $id = Crypt::decrypt($id);
        $nobukti = $request->nobukti;
        $nobukti_old = $request->nobukti_old;
        $tgl_kaskecil = $request->tgl_kaskecil;
        $tgltransaksi = explode("-", $tgl_kaskecil);
        $bulan = $tgltransaksi[1];
        $tahun = $tgltransaksi[0];
        $thn = substr($tahun, 2, 2);
        $keterangan = $request->keterangan;
        $kode_akun = $request->kode_akun;
        $status_dk = $request->inout;
        $peruntukan = $request->peruntukan;
        $jumlah = str_replace(".", "", $request->jumlah);
        $kaskecil = DB::table('kaskecil_detail')->where('id', $id)->first();
        $kode_cr = $kaskecil->kode_cr;
        $kode_cabang = $kaskecil->kode_cabang;
        $nobukti_bukubesar = $kaskecil->nobukti_bukubesar;
        $nobukti_bukubesar_2 = $kaskecil->nobukti_bukubesar_2;
        $cekakun = substr($kode_akun, 0, 3);

        DB::beginTransaction();
        try {
            //Update Kas Kecil
            $datakaskecil = [
                'nobukti' => $nobukti,
                'tgl_kaskecil' => $tgl_kaskecil,
                'keterangan' => $keterangan,
                'kode_akun' => $kode_akun,
                'status_dk' => $status_dk,
                'peruntukan' => $peruntukan,
                'jumlah' => $jumlah
            ];
            DB::table('kaskecil_detail')->where('id', $id)->update($datakaskecil);
            //Update No Bukti
            $datanobukti = [
                'nobukti' => $nobukti
            ];
            DB::table('kaskecil_detail')->where('nobukti', $nobukti_old)->update($datanobukti);
            if ($status_dk == 'D' and $peruntukan != "MP" and $cekakun == '6-1' or $status_dk == 'D' and $peruntukan != "MP" and $cekakun == '6-2') {
                //Update Cost Ratio
                if (empty($kode_cr)) {
                    $kode = "CR" . $bulan . $thn;
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
                    $kode_cr = buatkode($last_kode_cr, "CR" . $bulan . $thn, 4);
                    $datacr = [
                        'kode_cr' => $kode_cr,
                        'tgl_transaksi' => $tgl_kaskecil,
                        'kode_akun'    => $kode_akun,
                        'keterangan'   => $keterangan,
                        'kode_cabang'  => $kode_cabang,
                        'id_sumber_costratio' => 1,
                        'jumlah' => $jumlah
                    ];
                    DB::table('costratio_biaya')->insert($datacr);

                    $datakaskecil = [
                        'kode_cr' => $kode_cr
                    ];
                    DB::table('kaskecil_detail')->where('id', $id)->update($datakaskecil);
                } else {

                    $datacr = [
                        'tgl_transaksi' => $tgl_kaskecil,
                        'keterangan' => $keterangan,
                        'kode_akun' => $kode_akun,
                        'jumlah' => $jumlah
                    ];
                    DB::table('costratio_biaya')->where('kode_cr', $kode_cr)->update($datacr);
                }
            } else {
                if (!empty($kode_cr)) {
                    DB::table('costratio_biaya')->where('kode_cr', $kode_cr)->delete();
                    $datakaskecil = [
                        'kode_cr' => null
                    ];
                    DB::table('kaskecil_detail')->where('id', $id)->update($datakaskecil);
                }
            }

            //Update Buku Besar

            if ($status_dk == "D") {
                $debet = $jumlah;
                $kredit = 0;
            } else {
                $debet = 0;
                $kredit = $jumlah;
            }

            $databukubesar = [
                'tanggal' => $tgl_kaskecil,
                'keterangan' => $keterangan,
                'debet' => $kredit,
                'kredit' => $debet,
                'nobukti_transaksi' => $nobukti
            ];

            $databukubesartrans = [
                'tanggal' => $tgl_kaskecil,
                'keterangan' => $keterangan,
                'debet' => $debet,
                'kredit' => $kredit,
                'nobukti_transaksi' => $nobukti
            ];

            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar)->update($databukubesar);
            DB::table('buku_besar')->where('no_bukti', $nobukti_bukubesar_2)->update($databukubesartrans);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);;
        }
    }

    public function updatecostratio()
    {
        $dari = "2021-10-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $kaskecil = DB::table('kaskecil_detail')
            ->whereBetween('tgl_kaskecil', [$dari, $sampai])
            ->whereRaw('LEFT(kode_akun,3)="6-1"')
            ->orWhereBetween('tgl_kaskecil', [$dari, $sampai])
            ->whereRaw('LEFT(kode_akun,3)="6-2"')
            ->get();
        //dd($kaskecil);
        $kode = "CR1021";
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
        $kode_cr = $last_kode_cr != null ? $cr->kode_cr : "";
        $ceksimpan = 0;
        $cekupdate = 0;
        DB::beginTransaction();
        try {
            foreach ($kaskecil as $d) {
                $kode_cr = buatkode($kode_cr, $kode, 4);
                $data = [
                    'kode_cr' => $kode_cr,
                    'tgl_transaksi' => $d->tgl_kaskecil,
                    'kode_akun' => $d->kode_akun,
                    'keterangan' => $d->keterangan,
                    'kode_cabang' => $d->kode_cabang,
                    'id_sumber_costratio' => 1,
                    'jumlah' => $d->jumlah
                ];
                $simpan = DB::table('costratio_biaya')->insert($data);
                $update = DB::table('kaskecil_detail')->where('id', $d->id)->update(['kode_cr' => $kode_cr]);
                if ($simpan) {
                    $ceksimpan++;
                }

                if ($update) {
                    $cekupdate++;
                }
                $kode_cr = $kode_cr;
            }

            echo $ceksimpan . "<br>";
            echo $cekupdate;
            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
        }
    }
}
