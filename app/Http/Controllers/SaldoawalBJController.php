<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Detailsaldobj;
use App\Models\SaldoawalBJ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class SaldoawalBJController extends Controller
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
    public function index($jenis_bj, Request $request)
    {
        $query = SaldoawalBJ::query();
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }

        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
            lockyear($request->tahun);
        } else {
            $query->where('tahun', '>=', startyear());
        }

        if (Auth::user()->kode_cabang != "PCF") {
            $query->where('kode_cabang', $request->kode_cabang);
        } else {
            if (!empty($request->kode_cabang)) {
                $query->where('kode_cabang', $request->kode_cabang);
            }
        }



        $query->where('status', $jenis_bj);
        $query->select('saldoawal_bj.*');
        $query->orderBy('tahun', 'desc');
        $query->orderBy('bulan', 'asc');
        $saldoawal = $query->paginate(15);
        $saldoawal->appends($request->all());
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalbj.index', compact('jenis_bj', 'bulan', 'cabang', 'saldoawal'));
    }

    public function show($kode_saldoawal)
    {
        $kode_saldoawal = Crypt::decrypt($kode_saldoawal);
        $saldoawal = DB::table('saldoawal_bj')->where('kode_saldoawal', $kode_saldoawal)->first();
        $detail = DB::table('saldoawal_bj_detail')
            ->join('master_barang', 'saldoawal_bj_detail.kode_produk', '=', 'master_barang.kode_produk')
            ->where('kode_saldoawal', $kode_saldoawal)->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalbj.show', compact('saldoawal', 'detail', 'bulan'));
    }

    public function delete($kode_saldoawal)
    {
        $kode_saldoawal = Crypt::decrypt($kode_saldoawal);
        $saldoawal = DB::table('saldoawal_bj')->where('kode_saldoawal', $kode_saldoawal)->first();
        $cek = DB::table('saldoawal_bj')
            ->where('bulan', '>', $saldoawal->bulan)
            ->where('tahun', '>=', $saldoawal->tahun)
            ->where('kode_cabang', $saldoawal->kode_cabang)
            ->where('status', $saldoawal->status)
            ->count();
        // echo $cek;
        // die;
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Saldo Tersebut Sudah Di kunci']);
        } else {
            $hapus = DB::table('saldoawal_bj')->where('kode_saldoawal', $kode_saldoawal)->delete();
            if ($hapus) {
                return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
            }
        }
    }

    public function create($jenis_bj)
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalbj.create', compact('bulan', 'jenis_bj', 'cabang'));
    }

    public function getdetailsaldo(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $status = $request->status;
        if ($bulan == 1) {
            $bulanlalu = 12;
            $tahunlalu = $tahun - 1;
        } else {
            $bulanlalu = $bulan - 1;
            $tahunlalu = $tahun;
        }


        $ceksaldo = DB::table('saldoawal_bj')->where('kode_cabang', $kode_cabang)->where('status', $status)->where('bulan', $bulanlalu)->where('tahun', $tahunlalu)->count();
        $ceknow = DB::table('saldoawal_bj')->where('kode_cabang', $kode_cabang)->where('status', $status)->where('bulan', $bulan)->where('tahun', $tahun)->count();
        $cekall = DB::table('saldoawal_bj')->where('kode_cabang', $kode_cabang)->where('status', $status)->count();
        if (empty($ceksaldo) && !empty($cekall) || !empty($ceknow)) {
            echo "1";
        } else {
            if ($status == "GS") {
                $detail = DB::table('master_barang')
                    ->selectRaw("master_barang.kode_produk,
                    nama_barang,
                    isipcsdus,
                    isipack,
                    isipcs,
                    satuan,
                    jumlah as sabulanlalu,
                    sisamutasi,IFNULL(jumlah,0) + IFNULL(sisamutasi,0) as saldoakhir")
                    ->leftJoin(
                        DB::raw("(
                            SELECT kode_produk,jumlah FROM saldoawal_bj_detail
                            INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                            WHERE bulan='$bulanlalu' AND tahun='$tahunlalu' AND kode_cabang='$kode_cabang' AND `status`='$status'
                        ) sa"),
                        function ($join) {
                            $join->on('master_barang.kode_produk', '=', 'sa.kode_produk');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(
                            SELECT kode_produk,
                            SUM(IF( inout_good = 'IN', jumlah, 0)) - SUM(IF( inout_good = 'OUT', jumlah, 0)) as sisamutasi
                            FROM detail_mutasi_gudang_cabang
                            INNER JOIN mutasi_gudang_cabang
                            ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                            WHERE MONTH(tgl_mutasi_gudang_cabang)='$bulanlalu' AND YEAR(tgl_mutasi_gudang_cabang)='$tahunlalu' AND kode_cabang='$kode_cabang' GROUP BY kode_produk
                        ) mutasi"),
                        function ($join) {
                            $join->on('master_barang.kode_produk', '=', 'mutasi.kode_produk');
                        }
                    )
                    ->whereRaw('IFNULL(jumlah,0) + IFNULL(sisamutasi,0) != 0')
                    ->get();
            } else {
                $detail = DB::table('master_barang')
                    ->selectRaw("master_barang.kode_produk,
                    nama_barang,
                    isipcsdus,
                    isipack,
                    isipcs,
                    satuan,
                    jumlah as sabulanlalu,
                    sisamutasi,IFNULL(jumlah,0) + IFNULL(sisamutasi,0) as saldoakhir")
                    ->leftJoin(
                        DB::raw("(
                            SELECT kode_produk,jumlah FROM saldoawal_bj_detail
                            INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                            WHERE bulan='$bulanlalu' AND tahun='$tahunlalu' AND kode_cabang='$kode_cabang' AND `status`='$status'
                        ) sa"),
                        function ($join) {
                            $join->on('master_barang.kode_produk', '=', 'sa.kode_produk');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(
                            SELECT kode_produk,
                            SUM(IF( inout_bad = 'IN', jumlah, 0)) - SUM(IF( inout_bad = 'OUT', jumlah, 0)) as sisamutasi
                            FROM detail_mutasi_gudang_cabang
                            INNER JOIN mutasi_gudang_cabang
                            ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                            WHERE MONTH(tgl_mutasi_gudang_cabang)='$bulanlalu' AND YEAR(tgl_mutasi_gudang_cabang)='$tahunlalu' AND kode_cabang='$kode_cabang' GROUP BY kode_produk
                        ) mutasi"),
                        function ($join) {
                            $join->on('master_barang.kode_produk', '=', 'mutasi.kode_produk');
                        }
                    )
                    ->whereRaw('IFNULL(jumlah,0) + IFNULL(sisamutasi,0) != 0')
                    ->get();
            }

            return view('saldoawalbj.getdetailsaldo', compact('detail'));
        }
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        if (strlen($bulan) > 1) {
            $bln = $bulan;
        } else {
            $bln = "0" . $bulan;
        }
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $status = $request->status;
        $thn = substr($tahun, 2, 2);
        $kode_saldoawal = $status . $kode_cabang . $bln . $thn;
        $tanggal = $request->tanggal;
        $url = $status == "GS" ? 'saldoawalgs' : 'saldoawalbs';
        $kode_produk = $request->kode_produk;
        $isipcsdus = $request->isipcsdus;
        $isipack = $request->isipack;
        $isipcs = $request->isipcs;
        $isipcs = $request->isipcs;
        $jmldus = $request->jmldus;
        $jmlpack = $request->jmlpack;
        $jmlpcs = $request->jmlpcs;
        $data = [
            'kode_saldoawal' => $kode_saldoawal,
            'tanggal' => $tanggal,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'status' => $status,
            'kode_cabang' => $kode_cabang,
            'id_admin' => Auth::user()->id
        ];

        for ($i = 0; $i < count($kode_produk); $i++) {
            $jml_dus = !empty($jmldus[$i]) ? $jmldus[$i] : 0;
            $jml_pack = !empty($jmlpack[$i]) ? $jmlpack[$i] : 0;
            $jml_pcs = !empty($jmlpcs[$i]) ? $jmlpcs[$i] : 0;

            $jumlah = ($jml_dus * $isipcsdus[$i]) + ($jml_pack * $isipcs[$i]) + $jml_pcs;
            if (!empty($jumlah)) {
                $detail_saldo[]   = [
                    'kode_saldoawal' => $kode_saldoawal,
                    'kode_produk' => $kode_produk[$i],
                    'jumlah' => $jumlah
                ];
            }
        }


        //dd($detail_saldo);

        //dd($chunks);
        DB::beginTransaction();
        try {
            DB::table('saldoawal_bj')->insert($data);
            $chunks = array_chunk($detail_saldo, 5);
            foreach ($chunks as $chunk) {
                Detailsaldobj::insert($chunk);
            }
            DB::commit();
            return redirect('/' . $url . '/' . $status . '?kode_cabang=' . $kode_cabang . '&tahun=' . $tahun)->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/' . $url . '/' . $status . '?kode_cabang=' . $kode_cabang . '&tahun=' . $tahun)->with(['success' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }
}
