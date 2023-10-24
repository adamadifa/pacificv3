<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cabang;
use App\Models\Detailmutasicabang;
use App\Models\Mutasigudangcabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class MutasigudangcabangController extends Controller
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
    public function index($jenis_mutasi, Request $request)
    {
        if ($jenis_mutasi == "hutangkirim") {
            $jm = "HUTANG KIRIM";
            $textjm = "HUTANG KIRIM";
        } else if ($jenis_mutasi == "plttr") {
            $jm = "PL TTR";
            $textjm = "PELUNASAN TTR";
        } else if ($jenis_mutasi == "gantibarang") {
            $jm = "GANTI BARANG";
            $textjm = "GANTI BARANG";
        } else if ($jenis_mutasi == "rejectpasar") {
            $jm = "REJECT PASAR";
            $textjm = "REJECT PASAR";
        } else if ($jenis_mutasi == "rejectmobil") {
            $jm = "REJECT MOBIL";
            $textjm = "REJECT MOBIL";
        } else if ($jenis_mutasi == "plhutangkirim") {
            $jm = "PL HUTANG KIRIM";
            $textjm = "PELUNASAN HUTANG KIRIM";
        } else {
            $jm = $jenis_mutasi;
            $textjm = $jenis_mutasi;
        }

        $query = Mutasigudangcabang::query();
        $query->select('mutasi_gudang_cabang.*', 'nama_karyawan', 'tujuan', 'no_kendaraan');
        $query->leftjoin('dpb', 'mutasi_gudang_cabang.no_dpb', '=', 'dpb.no_dpb');
        $query->leftjoin('karyawan', 'dpb.id_karyawan', 'karyawan.id_karyawan');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', [$request->dari, $request->sampai]);
        } else {
            $query->where('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', '>=', startreport());
        }

        lockreport($request->dari);
        if (!empty($request->no_dpb)) {
            $query->where('mutasi_gudang_cabang.no_dpb', $request->no_dpb);
        }

        $query->where('mutasi_gudang_cabang.kode_cabang', $request->kode_cabang);


        if (!empty($request->id_karyawan)) {
            $query->where('dpb.id_karyawan', $request->id_karyawan);
        }


        $query->where('jenis_mutasi', $jm);
        $query->orderBy('tgl_mutasi_gudang_cabang', 'desc');
        $query->orderBy('no_dpb', 'desc');
        $mutasi = $query->paginate(15);
        $mutasi->appends($request->all());
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        return view('mutasigudangcabang.index', compact('jenis_mutasi', 'mutasi', 'cabang', 'jm', 'textjm'));
    }

    public function repack(Request $request)
    {
        $jenis_mutasi = "REPACK";
        $jm = "REPACK";
        $textjm = "REPACK";
        $query = Mutasigudangcabang::query();
        $query->select('mutasi_gudang_cabang.*');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', [$request->dari, $request->sampai]);
        } else {
            $query->where('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', '>=', startreport());
        }

        lockreport($request->dari);

        $query->where('mutasi_gudang_cabang.kode_cabang', $request->kode_cabang);

        $query->where('jenis_mutasi', $jm);
        $query->orderBy('tgl_mutasi_gudang_cabang', 'desc');
        $query->orderBy('no_mutasi_gudang_cabang', 'desc');
        $mutasi = $query->paginate(15);
        $mutasi->appends($request->all());
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        return view('mutasigudangcabang.repack', compact('jenis_mutasi', 'mutasi', 'cabang', 'jm', 'textjm'));
    }

    public function kirimpusat(Request $request)
    {
        $jenis_mutasi = "KIRIMPUSAT";
        $jm = "KIRIM PUSAT";
        $textjm = "KIRIM PUSAT";
        $query = Mutasigudangcabang::query();
        $query->select('mutasi_gudang_cabang.*');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', [$request->dari, $request->sampai]);
        } else {
            $query->where('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', '>=', startreport());
        }

        lockreport($request->dari);

        $query->where('mutasi_gudang_cabang.kode_cabang', $request->kode_cabang);

        $query->where('jenis_mutasi', $jm);
        $query->orderBy('tgl_mutasi_gudang_cabang', 'desc');
        $query->orderBy('no_mutasi_gudang_cabang', 'desc');
        $mutasi = $query->paginate(15);
        $mutasi->appends($request->all());
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        return view('mutasigudangcabang.repack', compact('jenis_mutasi', 'mutasi', 'cabang', 'jm', 'textjm'));
    }

    public function rejectgudang(Request $request)
    {
        $jenis_mutasi = "REJECTGUDANG";
        $jm = "REJECT GUDANG";
        $textjm = "REJECT GUDANG";
        $query = Mutasigudangcabang::query();
        $query->select('mutasi_gudang_cabang.*', 'no_dok');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', [$request->dari, $request->sampai]);
        } else {
            $query->where('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', '>=', startreport());
        }

        lockreport($request->dari);

        $query->where('mutasi_gudang_cabang.kode_cabang', $request->kode_cabang);
        $query->leftJoin('mutasi_gudang_jadi', 'mutasi_gudang_cabang.no_suratjalan', '=', 'mutasi_gudang_jadi.no_mutasi_gudang');
        $query->where('mutasi_gudang_cabang.jenis_mutasi', $jm);
        $query->orderBy('tgl_mutasi_gudang_cabang', 'desc');
        $query->orderBy('no_mutasi_gudang_cabang', 'desc');
        $mutasi = $query->paginate(15);
        $mutasi->appends($request->all());
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        return view('mutasigudangcabang.rejectgudang', compact('jenis_mutasi', 'mutasi', 'cabang', 'jm', 'textjm'));
    }

    public function show($no_mutasi_gudang_cabang)
    {
        $no_mutasi_gudang_cabang = Crypt::decrypt($no_mutasi_gudang_cabang);
        $mutasi = DB::table('mutasi_gudang_cabang')
            ->select(
                'mutasi_gudang_cabang.*',
                'nama_karyawan',
                'tujuan',
                'no_kendaraan',
                'driver.nama_driver_helper as nama_driver',
                'helper1.nama_driver_helper as nama_helper_1',
                'helper2.nama_driver_helper as nama_helper_2',
                'helper3.nama_driver_helper as nama_helper_3'
            )
            ->leftjoin('dpb', 'mutasi_gudang_cabang.no_dpb', '=', 'dpb.no_dpb')
            ->join('karyawan', 'dpb.id_karyawan', 'karyawan.id_karyawan')
            ->leftJoin('driver_helper as driver', 'dpb.id_driver', '=', 'driver.id_driver_helper')
            ->leftJoin('driver_helper as helper1', 'dpb.id_helper', '=', 'helper1.id_driver_helper')
            ->leftJoin('driver_helper as helper2', 'dpb.id_helper_2', '=', 'helper2.id_driver_helper')
            ->leftJoin('driver_helper as helper3', 'dpb.id_helper_3', '=', 'helper3.id_driver_helper')
            ->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)->first();
        $detail = DB::table('detail_mutasi_gudang_cabang')
            ->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)
            ->get();
        return view('mutasigudangcabang.show', compact('mutasi', 'detail'));
    }

    public function showdetail($no_mutasi_gudang_cabang)
    {
        $no_mutasi_gudang_cabang = Crypt::decrypt($no_mutasi_gudang_cabang);
        $mutasi = DB::table('mutasi_gudang_cabang')
            ->select(
                'mutasi_gudang_cabang.*'
            )
            ->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)->first();
        $detail = DB::table('detail_mutasi_gudang_cabang')
            ->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)
            ->get();
        return view('mutasigudangcabang.showdetail', compact('mutasi', 'detail'));
    }

    public function create($jenis_mutasi)
    {
        if ($jenis_mutasi == "hutangkirim") {
            $jm = "HUTANG KIRIM";
            $textjm = "HUTANG KIRIM";
        } else if ($jenis_mutasi == "plttr") {
            $jm = "PL TTR";
            $textjm = "PELUNASAN TTR";
        } else if ($jenis_mutasi == "gantibarang") {
            $jm = "GANTI BARANG";
            $textjm = "GANTI BARANG";
        } else if ($jenis_mutasi == "rejectpasar") {
            $jm = "REJECT PASAR";
            $textjm = "REJECT PASAR";
        } else if ($jenis_mutasi == "rejectmobil") {
            $jm = "REJECT MOBIL";
            $textjm = "REJECT MOBIL";
        } else if ($jenis_mutasi == "plhutangkirim") {
            $jm = "PL HUTANG KIRIM";
            $textjm = "PELUNASAN HUTANG KIRIM";
        } else {
            $jm = $jenis_mutasi;
            $textjm = $jenis_mutasi;
        }
        $produk = Barang::orderBy('kode_produk')->where('status', 1)->get();
        return view('mutasigudangcabang.create', compact('jenis_mutasi', 'produk', 'jm', 'textjm'));
    }

    public function mutasicreate($jenis_mutasi)
    {
        if ($jenis_mutasi == "REPACK") {
            $jm = "REPACK";
            $textjm = "REPACK";
        } else if ($jenis_mutasi == "KIRIMPUSAT") {
            $jm = "KIRIM PUSAT";
            $textjm = "KIRIM PUSAT";
        }
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $produk = Barang::orderBy('kode_produk')->where('status', 1)->get();
        return view('mutasigudangcabang.mutasicreate', compact('jenis_mutasi', 'produk', 'jm', 'textjm', 'cabang'));
    }

    public function rejectgudangcreate($jenis_mutasi)
    {
        if ($jenis_mutasi == "REJECTGUDANG") {
            $jm = "REJECT GUDANG";
            $textjm = "REJECT GUDANG";
        }
        $produk = Barang::orderBy('kode_produk')->where('status', 1)->get();
        return view('mutasigudangcabang.rejectgudangcreate', compact('jenis_mutasi', 'produk', 'jm', 'textjm'));
    }


    public function edit($no_mutasi_gudang_cabang)
    {
        $no_mutasi_gudang_cabang = Crypt::decrypt($no_mutasi_gudang_cabang);
        $mutasi = DB::table('mutasi_gudang_cabang')
            ->select(
                'mutasi_gudang_cabang.*',
                'nama_karyawan',
                'tujuan',
                'no_kendaraan',
                'driver.nama_driver_helper as nama_driver',
                'helper1.nama_driver_helper as nama_helper_1',
                'helper2.nama_driver_helper as nama_helper_2',
                'helper3.nama_driver_helper as nama_helper_3'
            )
            ->leftjoin('dpb', 'mutasi_gudang_cabang.no_dpb', '=', 'dpb.no_dpb')
            ->join('karyawan', 'dpb.id_karyawan', 'karyawan.id_karyawan')
            ->leftJoin('driver_helper as driver', 'dpb.id_driver', '=', 'driver.id_driver_helper')
            ->leftJoin('driver_helper as helper1', 'dpb.id_helper', '=', 'helper1.id_driver_helper')
            ->leftJoin('driver_helper as helper2', 'dpb.id_helper_2', '=', 'helper2.id_driver_helper')
            ->leftJoin('driver_helper as helper3', 'dpb.id_helper_3', '=', 'helper3.id_driver_helper')
            ->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)->first();
        $detail = DB::table('master_barang')
            ->select('master_barang.*', 'jumlah')
            ->leftJoin(
                DB::raw("(
            SELECT
            kode_produk,
            jumlah
            FROM
                detail_mutasi_gudang_cabang
            WHERE
                no_mutasi_gudang_cabang = '$no_mutasi_gudang_cabang'
            ) dmc"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'dmc.kode_produk');
                }
            )
            ->where('master_barang.status', 1)
            ->get();
        return view('mutasigudangcabang.edit', compact('mutasi', 'detail'));
    }

    public function mutasiedit($no_mutasi_gudang_cabang)
    {
        $no_mutasi_gudang_cabang = Crypt::decrypt($no_mutasi_gudang_cabang);
        $mutasi = DB::table('mutasi_gudang_cabang')
            ->select(
                'mutasi_gudang_cabang.*'
            )
            ->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)->first();
        $detail = DB::table('master_barang')
            ->select('master_barang.*', 'jumlah')
            ->leftJoin(
                DB::raw("(
            SELECT
            kode_produk,
            jumlah
            FROM
                detail_mutasi_gudang_cabang
            WHERE
                no_mutasi_gudang_cabang = '$no_mutasi_gudang_cabang'
            ) dmc"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'dmc.kode_produk');
                }
            )
            ->get();
        return view('mutasigudangcabang.mutasiedit', compact('mutasi', 'detail'));
    }

    public function penyesuaianedit($no_mutasi_gudang_cabang)
    {
        $no_mutasi_gudang_cabang = Crypt::decrypt($no_mutasi_gudang_cabang);
        $mutasi = DB::table('mutasi_gudang_cabang')
            ->select(
                'mutasi_gudang_cabang.*'
            )
            ->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)->first();
        $detail = DB::table('master_barang')
            ->select('master_barang.*', 'jumlah')
            ->leftJoin(
                DB::raw("(
            SELECT
            kode_produk,
            jumlah
            FROM
                detail_mutasi_gudang_cabang
            WHERE
                no_mutasi_gudang_cabang = '$no_mutasi_gudang_cabang'
            ) dmc"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'dmc.kode_produk');
                }
            )
            ->get();
        return view('mutasigudangcabang.penyesuaianedit', compact('mutasi', 'detail'));
    }
    public function store(Request $request)
    {
        $no_dpb = $request->no_dpb_val;
        $dpb = DB::table('dpb')->where('no_dpb', $no_dpb)->first();
        $tgl_mutasi_gudang_cabang = $request->tgl_mutasi_gudang_cabang;
        $jenis_mutasi = $request->jenis_mutasi;

        if ($jenis_mutasi == "RETUR") {
            $kode = "RTR";
            $kondisi = "GOOD";
            $inout_good = "IN";
            $inout_bad = NULL;
            $order = 10;
        } else if ($jenis_mutasi == "HUTANG KIRIM") {
            $kode = "HK";
            $kondisi = "GOOD";
            $inout_good = "IN";
            $inout_bad = NULL;
            $order = 5;
        } else if ($jenis_mutasi == "PL TTR") {
            $kode = "PT";
            $kondisi = "GOOD";
            $inout_good = "IN";
            $inout_bad = NULL;
            $order = 6;
        } else if ($jenis_mutasi == "PENJUALAN") {
            $kode = "PNJ";
            $kondisi = "GOOD";
            $inout_good = "OUT";
            $inout_bad = NULL;
            $order = 4;
        } else if ($jenis_mutasi == "GANTI BARANG") {
            $kode = "RGB";
            $kondisi = "GOOD";
            $inout_good = "OUT";
            $inout_bad = NULL;
            $order = 7;
        } else if ($jenis_mutasi == "REJECT PASAR") {
            $kode = "RJP";
            $kondisi = "BAD";
            $inout_good = "OUT";
            $inout_bad = "IN";
            $order = 11;
        } else if ($jenis_mutasi == "REJECT MOBIL") {
            $kode = "RJM";
            $kondisi = "BAD";
            $inout_good = "OUT";
            $inout_bad = "IN";
            $order = 11;
        } else if ($jenis_mutasi == "PL HUTANG KIRIM") {
            $kode = "PH";
            $kondisi = "GOOD";
            $inout_good = "OUT";
            $inout_bad = NULL;
            $order = 8;
        } else if ($jenis_mutasi == "TTR") {
            $kode = "TR";
            $kondisi = "GOOD";
            $inout_good = "OUT";
            $inout_bad = NULL;
            $order = 9;
        } else if ($jenis_mutasi == "TTR") {
            $kode = "TR";
            $kondisi = "GOOD";
            $inout_good = "OUT";
            $inout_bad = NULL;
            $order = 9;
        } else if ($jenis_mutasi == "PROMOSI") {
            $kode = "PR";
            $kondisi = "GOOD";
            $inout_good = "OUT";
            $inout_bad = NULL;
            $order = 12;
        }
        $mutasi = DB::table('mutasi_gudang_cabang')
            ->select('no_mutasi_gudang_cabang')->where('no_dpb', $no_dpb)->where('jenis_mutasi', $jenis_mutasi)
            ->orderBy('no_mutasi_gudang_cabang', 'desc')
            ->first();
        $lastnomutasi = $mutasi != null ? $mutasi->no_mutasi_gudang_cabang : '';
        $no_mutasi = buatkode($lastnomutasi, $kode . $no_dpb, 2);
        $kode_cabang = $dpb->kode_cabang;
        $tanggal  = explode("-", $tgl_mutasi_gudang_cabang);
        $bulan    = $tanggal[1];
        $tahun    = $tanggal[0];
        if ($bulan == 12) {
            $bulan = 1;
            $tahun = $tahun + 1;
        } else {
            $bulan = $bulan + 1;
            $tahun = $tahun;
        }
        $id_admin  = Auth::user()->id;
        $kode_produk = $request->kode_produk;
        $jmldus = $request->jmldus;
        $jmlpack = $request->jmlpack;
        $jmlpcs = $request->jmlpcs;
        $isipcsdus = $request->isipcsdus;
        $isipcs = $request->isipcs;
        $data = array(
            'no_mutasi_gudang_cabang'  => $no_mutasi,
            'tgl_mutasi_gudang_cabang' => $tgl_mutasi_gudang_cabang,
            'no_dpb'                   => $no_dpb,
            'kode_cabang'              => $kode_cabang,
            'kondisi'                  => $kondisi,
            'inout_good'               => $inout_good,
            'inout_bad'               => $inout_bad,
            'jenis_mutasi'             => $jenis_mutasi,
            'order'                    => $order,
            'id_admin'                 => $id_admin
        );
        for ($i = 0; $i < count($kode_produk); $i++) {
            $jml_dus = !empty($jmldus[$i]) ? $jmldus[$i] : 0;
            $jml_pack = !empty($jmlpack[$i]) ? $jmlpack[$i] : 0;
            $jml_pcs = !empty($jmlpcs[$i]) ? $jmlpcs[$i] : 0;

            $jumlah = ($jml_dus * $isipcsdus[$i]) + ($jml_pack * $isipcs[$i]) + $jml_pcs;
            if (!empty($jumlah)) {
                $data_detail[]   = [
                    'no_mutasi_gudang_cabang' => $no_mutasi,
                    'kode_produk'             => $kode_produk[$i],
                    'jumlah'                  => $jumlah
                ];
            }
        }
        //dd($data_detail);
        $ceksa = DB::table('saldoawal_bj')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_cabang', $kode_cabang)->count();
        if ($ceksa > 0) {
            return Redirect::back()->with(['warning' => 'Data Periode Ini Sudah Ditutup, Karena Saldo Bulan Berikutnya Sudah Di Set']);
        } else {
            DB::beginTransaction();
            try {
                // $cek = DB::table('mutasi_gudang_cabang')->where('no_dpb', $no_dpb)->where('jenis_mutasi', $jenis_mutasi)->count();
                // if ($cek > 0) {
                //     return Redirect::back()->with(['warning' => 'No. DPB Sudah Ada']);
                // } else {
                DB::table('mutasi_gudang_cabang')->insert($data);
                $chunks = array_chunk($data_detail, 5);
                foreach ($chunks as $chunk) {
                    Detailmutasicabang::insert($chunk);
                    // }
                }

                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT!']);
            }
        }
    }

    public function mutasistore(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $tgl_mutasi_gudang_cabang = $request->tgl_mutasi_gudang_cabang;
        $jenis_mutasi = $request->jenis_mutasi;
        $tanggal  = explode("-", $tgl_mutasi_gudang_cabang);
        $bulan    = $tanggal[1];
        $tahun    = $tanggal[0];
        $thn = substr($tahun, 2, 2);
        if ($jenis_mutasi == "REPACK") {
            $kode = "RPC";
            $kondisi = "BAD";
            $inout_good = "IN";
            $inout_bad = "OUT";
            $order = 17;
        } else if ($jenis_mutasi == "KIRIM PUSAT") {
            $kode = "KPT";
            $kondisi = "BAD";
            $inout_good = NULL;
            $inout_bad = "OUT";
            $order = 8;
        }
        $mutasi = DB::table('mutasi_gudang_cabang')
            ->select('no_mutasi_gudang_cabang')
            ->where('jenis_mutasi', $jenis_mutasi)
            ->whereRaw('YEAR(tgl_mutasi_gudang_cabang)=' . $tahun)
            ->orderBy('no_mutasi_gudang_cabang', 'desc')
            ->first();
        $lastnomutasi = $mutasi != null ? $mutasi->no_mutasi_gudang_cabang : '';
        $no_mutasi = buatkode($lastnomutasi, $kode . $thn, 5);

        if ($bulan == 12) {
            $bulan = 1;
            $tahun = $tahun + 1;
        } else {
            $bulan = $bulan + 1;
            $tahun = $tahun;
        }
        $id_admin  = Auth::user()->id;
        $kode_produk = $request->kode_produk;
        $jmldus = $request->jmldus;
        $jmlpack = $request->jmlpack;
        $jmlpcs = $request->jmlpcs;
        $isipcsdus = $request->isipcsdus;
        $isipcs = $request->isipcs;
        $data = array(
            'no_mutasi_gudang_cabang'  => $no_mutasi,
            'tgl_mutasi_gudang_cabang' => $tgl_mutasi_gudang_cabang,
            'kode_cabang'              => $kode_cabang,
            'kondisi'                  => $kondisi,
            'inout_good'               => $inout_good,
            'inout_bad'                => $inout_bad,
            'jenis_mutasi'             => $jenis_mutasi,
            'order'                    => $order,
            'id_admin'                 => $id_admin
        );
        for ($i = 0; $i < count($kode_produk); $i++) {
            $jml_dus = !empty($jmldus[$i]) ? $jmldus[$i] : 0;
            $jml_pack = !empty($jmlpack[$i]) ? $jmlpack[$i] : 0;
            $jml_pcs = !empty($jmlpcs[$i]) ? $jmlpcs[$i] : 0;

            $jumlah = ($jml_dus * $isipcsdus[$i]) + ($jml_pack * $isipcs[$i]) + $jml_pcs;
            if (!empty($jumlah)) {
                $data_detail[]   = [
                    'no_mutasi_gudang_cabang' => $no_mutasi,
                    'kode_produk'             => $kode_produk[$i],
                    'jumlah'                  => $jumlah
                ];
            }
        }
        //dd($data_detail);
        $ceksa = DB::table('saldoawal_bj')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_cabang', $kode_cabang)->count();
        if ($ceksa > 0) {
            return Redirect::back()->with(['warning' => 'Data Periode Ini Sudah Ditutup, Karena Saldo Bulan Berikutnya Sudah Di Set']);
        } else {
            DB::beginTransaction();
            try {

                DB::table('mutasi_gudang_cabang')->insert($data);
                $chunks = array_chunk($data_detail, 5);
                foreach ($chunks as $chunk) {
                    Detailmutasicabang::insert($chunk);
                }
                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT!']);
            }
        }
    }

    public function rejectgudangstore(Request $request)
    {
        $no_suratjalan = $request->no_sj;
        $sj = DB::table('mutasi_gudang_cabang')->where('no_mutasi_gudang_cabang', $no_suratjalan)->first();
        if ($sj != null) {
            $kode_cabang = $sj->kode_cabang;
        } else {
            $kode_cabang = Auth::user()->kode_cabang;
        }
        $tgl_mutasi_gudang_cabang = $request->tgl_mutasi_gudang_cabang;
        $jenis_mutasi = $request->jenis_mutasi;
        $tanggal  = explode("-", $tgl_mutasi_gudang_cabang);
        $bulan    = $tanggal[1];
        $tahun    = $tanggal[0];
        $thn = substr($tahun, 2, 2);
        if ($jenis_mutasi == "REJECT GUDANG") {
            $kode = "RJG";
            $kondisi = "BAD";
            $inout_good = "OUT";
            $inout_bad = "IN";
            $order = 3;
        }
        $mutasi = DB::table('mutasi_gudang_cabang')
            ->select('no_mutasi_gudang_cabang')
            ->where('jenis_mutasi', $jenis_mutasi)
            ->whereRaw('YEAR(tgl_mutasi_gudang_cabang)=' . $tahun)
            ->orderBy('no_mutasi_gudang_cabang', 'desc')
            ->first();
        $lastnomutasi = $mutasi != null ? $mutasi->no_mutasi_gudang_cabang : '';
        $no_mutasi = buatkode($lastnomutasi, $kode . $thn, 5);

        if ($bulan == 12) {
            $bulan = 1;
            $tahun = $tahun + 1;
        } else {
            $bulan = $bulan + 1;
            $tahun = $tahun;
        }
        $id_admin  = Auth::user()->id;
        $kode_produk = $request->kode_produk;
        $jmldus = $request->jmldus;
        $jmlpack = $request->jmlpack;
        $jmlpcs = $request->jmlpcs;
        $isipcsdus = $request->isipcsdus;
        $isipcs = $request->isipcs;
        $data = array(
            'no_mutasi_gudang_cabang'  => $no_mutasi,
            'tgl_mutasi_gudang_cabang' => $tgl_mutasi_gudang_cabang,
            'kode_cabang'              => $kode_cabang,
            'no_suratjalan'            => $no_suratjalan,
            'kondisi'                  => $kondisi,
            'inout_good'               => $inout_good,
            'inout_bad'                => $inout_bad,
            'jenis_mutasi'             => $jenis_mutasi,
            'order'                    => $order,
            'id_admin'                 => $id_admin
        );
        for ($i = 0; $i < count($kode_produk); $i++) {
            $jml_dus = !empty($jmldus[$i]) ? $jmldus[$i] : 0;
            $jml_pack = !empty($jmlpack[$i]) ? $jmlpack[$i] : 0;
            $jml_pcs = !empty($jmlpcs[$i]) ? $jmlpcs[$i] : 0;

            $jumlah = ($jml_dus * $isipcsdus[$i]) + ($jml_pack * $isipcs[$i]) + $jml_pcs;
            if (!empty($jumlah)) {
                $data_detail[]   = [
                    'no_mutasi_gudang_cabang' => $no_mutasi,
                    'kode_produk'             => $kode_produk[$i],
                    'jumlah'                  => $jumlah
                ];
            }
        }
        //dd($data_detail);
        $ceksa = DB::table('saldoawal_bj')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_cabang', $kode_cabang)->count();
        if ($ceksa > 0) {
            return Redirect::back()->with(['warning' => 'Data Periode Ini Sudah Ditutup, Karena Saldo Bulan Berikutnya Sudah Di Set']);
        } else {
            DB::beginTransaction();
            try {

                DB::table('mutasi_gudang_cabang')->insert($data);
                $chunks = array_chunk($data_detail, 5);
                foreach ($chunks as $chunk) {
                    Detailmutasicabang::insert($chunk);
                }
                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT!']);
            }
        }
    }
    public function update($no_mutasi_gudang_cabang, Request $request)
    {
        $no_mutasi_gudang_cabang = Crypt::decrypt($no_mutasi_gudang_cabang);
        $mutasi  = DB::table('mutasi_gudang_cabang')->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)->first();
        $kode_cabang = $mutasi->kode_cabang;
        $tgl_mutasi_gudang_cabang = $request->tgl_mutasi_gudang_cabang;
        $id_admin  = Auth::user()->id;
        $tanggal  = explode("-", $tgl_mutasi_gudang_cabang);
        $bulan    = $tanggal[1];
        $tahun    = $tanggal[0];
        if ($bulan == 12) {
            $bulan = 1;
            $tahun = $tahun + 1;
        } else {
            $bulan = $bulan + 1;
            $tahun = $tahun;
        }
        $kode_produk = $request->kode_produk;
        $jmldus = $request->jmldus;
        $jmlpack = $request->jmlpack;
        $jmlpcs = $request->jmlpcs;
        $isipcsdus = $request->isipcsdus;
        $isipcs = $request->isipcs;
        if ($mutasi->jenis_mutasi == "PENYESUAIAN") {
            $inout_good = $request->inout;
            $keterangan = $request->keterangan;
            $data = array(
                'tgl_mutasi_gudang_cabang' => $tgl_mutasi_gudang_cabang,
                'inout_good' => $inout_good,
                'keterangan' => $keterangan,
                'id_admin' => $id_admin,

            );
        } else if ($mutasi->jenis_mutasi == "PENYESUAIAN BAD") {
            $inout_bad = $request->inout;
            $keterangan = $request->keterangan;
            $data = array(
                'tgl_mutasi_gudang_cabang' => $tgl_mutasi_gudang_cabang,
                'inout_bad' => $inout_bad,
                'keterangan' => $keterangan,
                'id_admin' => $id_admin,

            );
        } else {
            $data = array(
                'tgl_mutasi_gudang_cabang' => $tgl_mutasi_gudang_cabang,
                'id_admin'                 => $id_admin
            );
        }
        for ($i = 0; $i < count($kode_produk); $i++) {
            $jml_dus = !empty($jmldus[$i]) ? $jmldus[$i] : 0;
            $jml_pack = !empty($jmlpack[$i]) ? $jmlpack[$i] : 0;
            $jml_pcs = !empty($jmlpcs[$i]) ? $jmlpcs[$i] : 0;

            $jumlah = ($jml_dus * $isipcsdus[$i]) + ($jml_pack * $isipcs[$i]) + $jml_pcs;
            if (!empty($jumlah)) {
                $data_detail[]   = [
                    'no_mutasi_gudang_cabang' => $no_mutasi_gudang_cabang,
                    'kode_produk'             => $kode_produk[$i],
                    'jumlah'                  => $jumlah
                ];
            }
        }
        //dd($data_detail);
        $ceksa = DB::table('saldoawal_bj')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_cabang', $kode_cabang)->count();
        if ($ceksa > 0) {
            return Redirect::back()->with(['warning' => 'Data Periode Ini Sudah Ditutup, Karena Saldo Bulan Berikutnya Sudah Di Set']);
        } else {
            DB::beginTransaction();
            try {

                DB::table('mutasi_gudang_cabang')->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)->update($data);
                DB::table('detail_mutasi_gudang_cabang')->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)->delete();
                $chunks = array_chunk($data_detail, 5);
                foreach ($chunks as $chunk) {
                    Detailmutasicabang::insert($chunk);
                }
                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT!']);
            }
        }
    }
    public function transitin(Request $request)
    {
        $query = Mutasigudangcabang::query();
        $query->select(
            'mutasi_gudang_cabang.no_mutasi_gudang_cabang',
            'mutasi_gudang_cabang.no_suratjalan',
            'mutasi_gudang_cabang.tgl_mutasi_gudang_cabang as tgl_transitout',
            'tgl_diterimacabang'
        );
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', [$request->dari, $request->sampai]);
        } else {
            $query->where('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', '>=', startreport());
        }

        lockreport($request->dari);
        $query->leftJoin(
            DB::raw("(
            SELECT
            no_suratjalan,
            tgl_mutasi_gudang_cabang as tgl_diterimacabang
            FROM
                mutasi_gudang_cabang
            WHERE
                jenis_mutasi = 'TRANSIT IN'
            ) sj"),
            function ($join) {
                $join->on('mutasi_gudang_cabang.no_suratjalan', '=', 'sj.no_suratjalan');
            }
        );
        $query->orderBy('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', 'desc');
        $query->orderBy('tgl_diterimacabang');
        $query->where('jenis_mutasi', 'TRANSIT OUT');
        $query->where('mutasi_gudang_cabang.kode_cabang', $request->kode_cabang);
        $transit = $query->paginate(15);
        $transit->appends($request->all());
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        return view('mutasigudangcabang.transitin', compact('transit', 'cabang'));
    }

    public function transitin_create($no_mutasi_gudang_cabang)
    {
        $no_mutasi_gudang_cabang = Crypt::decrypt($no_mutasi_gudang_cabang);
        $mutasicab = DB::table('mutasi_gudang_cabang')
            ->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)
            ->first();
        $no_sj = $mutasicab->no_suratjalan;
        $mutasi = DB::table('mutasi_gudang_jadi')
            ->join('permintaan_pengiriman', 'mutasi_gudang_jadi.no_permintaan_pengiriman', '=', 'permintaan_pengiriman.no_permintaan_pengiriman')
            ->join('cabang', 'permintaan_pengiriman.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('no_mutasi_gudang', $no_sj)
            ->first();

        $detail = DB::table('detail_mutasi_gudang')
            ->join('master_barang', 'detail_mutasi_gudang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_mutasi_gudang', $no_sj)
            ->get();
        return view('mutasigudangcabang.transitin_create', compact('mutasi', 'detail', 'mutasicab'));
    }

    public function delete($no_mutasi_gudang_cabang)
    {
        $no_mutasi_gudang_cabang = Crypt::decrypt($no_mutasi_gudang_cabang);
        $hapus = DB::table('mutasi_gudang_cabang')->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function transitin_store($no_mutasi_gudang_cabang, Request $request)
    {
        $no_mutasi_gudang_cabang = Crypt::decrypt($no_mutasi_gudang_cabang);
        $mutasi_gudang_cabang = DB::table('mutasi_gudang_cabang')->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)->first();
        $no_suratjalan = $mutasi_gudang_cabang->no_suratjalan;
        $tgl_mutasi_gudang = $mutasi_gudang_cabang->tgl_mutasi_gudang_cabang;
        $tgl_mutasi_gudang_cabang =  $request->tgl_mutasi_gudang_cabang;
        $kode_cabang = $mutasi_gudang_cabang->kode_cabang;
        $id_admin = Auth::user()->id;
        $tahunini = substr(date("Y"), 2, 2);
        $transitin = DB::table('mutasi_gudang_cabang')
            ->select('no_mutasi_gudang_cabang as no_transit_in')
            ->where('kode_cabang', $kode_cabang)
            ->where('jenis_mutasi', 'TRANSIT IN')
            ->whereRaw('MID(no_mutasi_gudang_cabang,6,2) =' . $tahunini)
            ->orderBy('no_mutasi_gudang_cabang', 'desc')
            ->first();
        $last_no_transit_in = $transitin != null ? $transitin->no_transit_in : '';
        $no_transit_in = buatkode($last_no_transit_in, 'TN' . $kode_cabang . $tahunini, 2);
        $detail = DB::table('detail_mutasi_gudang_cabang')
            ->where('no_mutasi_gudang_cabang', $no_mutasi_gudang_cabang)->get();
        DB::beginTransaction();
        try {
            $data_transit_in = array(
                'no_mutasi_gudang_cabang' => $no_transit_in,
                'tgl_mutasi_gudang_cabang' => $tgl_mutasi_gudang_cabang,
                'tgl_kirim' => $tgl_mutasi_gudang,
                'no_suratjalan' => $no_suratjalan,
                'kode_cabang' => $kode_cabang,
                'kondisi' => 'GOOD',
                'inout_good' => 'IN',
                'jenis_mutasi' => 'TRANSIT IN',
                'order'  => '2',
                'id_admin' => $id_admin
            );
            DB::table('mutasi_gudang_cabang')->insert($data_transit_in);
            foreach ($detail as $d) {
                $data_detail = array(
                    'no_mutasi_gudang_cabang'   => $no_transit_in,
                    'kode_produk'  => $d->kode_produk,
                    'jumlah' => $d->jumlah
                );
                DB::table('detail_mutasi_gudang_cabang')->insert($data_detail);
            }
            DB::table('mutasi_gudang_jadi')->where('no_mutasi_gudang', $no_suratjalan)->update(['status_sj' => 1]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT!']);
        }
    }

    public function transitin_batal($no_suratjalan)
    {
        $no_suratjalan = Crypt::decrypt($no_suratjalan);
        DB::beginTransaction();
        try {
            DB::table('mutasi_gudang_cabang')->where('no_suratjalan', $no_suratjalan)->where('jenis_mutasi', 'TRANSIT IN')->delete();
            DB::table('mutasi_gudang_jadi')->where('no_mutasi_gudang', $no_suratjalan)->update(['status_sj' => 2]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dibatalkan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan, Hubungi Tim IT!']);
        }
    }

    public function getsaldogudangcabang(Request $request)
    {
        $cabang = $request->kode_cabang;
        $status = $request->status;
        $gettanggal = DB::table('saldoawal_bj')->where('kode_cabang', $request->kode_cabang)->where('status', $request->status)
            ->orderBy('tanggal', 'desc')
            ->first();
        $tahun = $gettanggal->tahun;
        $bulan = $gettanggal->bulan;
        $hari  = "1";
        $tanggal = $tahun . "-" . $bulan . "-" . $hari;
        $saldo = DB::table('master_barang')
            ->selectRaw("master_barang.kode_produk,
        nama_barang,
        isipcsdus,
        isipack,
        isipcs,
        satuan,
        jumlah AS sabulanlalu,
        sisamutasi,
        buffer,
        totalpengembalian,totalpengambilan,
        IFNULL( jumlah, 0 ) + IFNULL( sisamutasi, 0 )  AS saldoakhir")
            ->leftJoin(
                DB::raw("(
                SELECT
                kode_produk,
                jumlah
                FROM
                    saldoawal_bj_detail
                    INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                WHERE
                    status = '$status'
                    AND kode_cabang = '$cabang' AND bulan = '$bulan' AND tahun ='$tahun'
                ) sa"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'sa.kode_produk');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    SUM( IF ( inout_good = 'IN', jumlah, 0 ) ) - SUM( IF ( inout_good = 'OUT', jumlah, 0 ) ) AS sisamutasi
                FROM
                    detail_mutasi_gudang_cabang
                    INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                WHERE
                    tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'SURAT JALAN'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'TRANSIT IN'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'TRANSIT OUT'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'REJECT GUDANG'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'REJECT PASAR'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'REPACK'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'PENYESUAIAN'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'HUTANG KIRIM'
                OR tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                AND `jenis_mutasi` = 'PL HUTANG KIRIM'
                GROUP BY detail_mutasi_gudang_cabang.kode_produk

                ) mutasi"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'mutasi.kode_produk');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT kode_produk,jumlah as buffer
                FROM detail_bufferstok
                INNER JOIN buffer_stok ON detail_bufferstok.kode_bufferstok = buffer_stok.kode_bufferstok
                WHERE kode_cabang='$cabang'
                ) bf"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'bf.kode_produk');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    SUM(jml_pengambilan) as totalpengambilan,
                SUM(jml_pengembalian) as totalpengembalian
                FROM
                    detail_dpb
                    INNER JOIN dpb ON detail_dpb.no_dpb = dpb.no_dpb
                WHERE
                tgl_pengambilan BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang' GROUP BY kode_produk
                ) dpb"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'dpb.kode_produk');
                }
            )
            ->orderBy('master_barang.nama_barang')
            ->get();

        return view('mutasi_gudang_cabang.dashboard.saldogudangcabang', compact('saldo'));
    }

    public function getsaldogudangcabangbs(Request $request)
    {
        $cabang = $request->kode_cabang;
        $status = $request->status;
        $gettanggal = DB::table('saldoawal_bj')->where('kode_cabang', $request->kode_cabang)->where('status', $request->status)
            ->orderBy('tanggal', 'desc')
            ->first();
        $tahun = $gettanggal->tahun;
        $bulan = $gettanggal->bulan;
        $hari  = "1";
        $tanggal = $tahun . "-" . $bulan . "-" . $hari;
        $saldo = DB::table('master_barang')
            ->selectRaw("master_barang.kode_produk,
            nama_barang,
            isipcsdus,
            isipack,
            isipcs,
            satuan,
            jumlah AS sabulanlalu,
            sisamutasi,
            IFNULL( jumlah, 0 ) + IFNULL( sisamutasi, 0 ) AS saldoakhir")
            ->leftJoin(
                DB::raw("(
                SELECT
                kode_produk,
                jumlah
                FROM
                    saldoawal_bj_detail
                    INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                WHERE
                    status = '$status'
                    AND kode_cabang = '$cabang' AND bulan = '$bulan' AND tahun ='$tahun'
                ) sa"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'sa.kode_produk');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT kode_produk,
                SUM( IF ( inout_bad = 'IN', jumlah, 0 ) ) - SUM( IF ( inout_bad = 'OUT', jumlah, 0 ) ) AS sisamutasi
                FROM
                    detail_mutasi_gudang_cabang
                    INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                WHERE
                    tgl_mutasi_gudang_cabang BETWEEN '$tanggal' AND CURDATE()
                    AND kode_cabang = '$cabang'
                GROUP BY detail_mutasi_gudang_cabang.kode_produk
                ) mutasi"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'mutasi.kode_produk');
                }
            )
            ->orderBy('master_barang.nama_barang')
            ->get();

        return view('mutasi_gudang_cabang.dashboard.saldogudangcabangbs', compact('saldo'));
    }

    public function penyesuaian($kondisi, Request $request)
    {

        $jenis_mutasi = $kondisi;
        if ($kondisi == "good") {
            $jm = "PENYESUAIAN";
            $textjm = "PENYESUAIAN";
        } else if ($kondisi == "bad") {
            $jm = "PENYESUAIAN BAD";
            $textjm = "PENYESUAIAN BAD";
        }
        $query = Mutasigudangcabang::query();
        $query->select('mutasi_gudang_cabang.*');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', [$request->dari, $request->sampai]);
        } else {
            $query->where('mutasi_gudang_cabang.tgl_mutasi_gudang_cabang', '>=', startreport());
        }
        lockreport($request->dari);
        $query->where('mutasi_gudang_cabang.kode_cabang', $request->kode_cabang);

        $query->where('jenis_mutasi', $jm);
        $query->orderBy('tgl_mutasi_gudang_cabang', 'desc');
        $query->orderBy('no_mutasi_gudang_cabang', 'desc');
        $mutasi = $query->paginate(15);
        $mutasi->appends($request->all());
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        return view('mutasigudangcabang.penyesuaian', compact('jenis_mutasi', 'mutasi', 'cabang', 'jm', 'textjm'));
    }

    public function penyesuaiancreate($jenis_mutasi)
    {
        if ($jenis_mutasi == "bad") {
            $jm = "PENYESUAIAN BAD";
            $textjm = "PENYESUAIAN BAD";
        } else if ($jenis_mutasi == "good") {
            $jm = "PENYESUAIAN";
            $textjm = "PENYESUAIAN";
        }
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $produk = Barang::orderBy('kode_produk')->get();
        return view('mutasigudangcabang.penyesuaian_create', compact('jenis_mutasi', 'produk', 'jm', 'textjm', 'cabang'));
    }

    public function penyesuaianstore(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $tgl_mutasi_gudang_cabang = $request->tgl_mutasi_gudang_cabang;
        $jenis_mutasi = $request->jenis_mutasi;
        $keterangan = $request->keterangan;
        $inout = $request->inout;
        $tanggal  = explode("-", $tgl_mutasi_gudang_cabang);
        $bulan    = $tanggal[1];
        $tahun    = $tanggal[0];
        $thn = substr($tahun, 2, 2);

        if ($jenis_mutasi == "PENYESUAIAN") {
            $kode = "PYG";
            $kondisi = "GOOD";
            $inout_good = $inout;
            $inout_bad = NULL;
            $order = 13;
        } else if ($jenis_mutasi == "PENYESUAIAN BAD") {
            $kode = "PYB";
            $kondisi = "BAD";
            $inout_good = NULL;
            $inout_bad = $inout;
            $order = 16;
        }
        $mutasi = DB::table('mutasi_gudang_cabang')
            ->select('no_mutasi_gudang_cabang')
            ->where('jenis_mutasi', $jenis_mutasi)
            ->whereRaw('YEAR(tgl_mutasi_gudang_cabang)=' . $tahun)
            ->orderBy('no_mutasi_gudang_cabang', 'desc')
            ->first();
        $lastnomutasi = $mutasi != null ? $mutasi->no_mutasi_gudang_cabang : '';
        $no_mutasi = buatkode($lastnomutasi, $kode . $thn, 5);

        if ($bulan == 12) {
            $bulan = 1;
            $tahun = $tahun + 1;
        } else {
            $bulan = $bulan + 1;
            $tahun = $tahun;
        }
        $id_admin  = Auth::user()->id;
        $kode_produk = $request->kode_produk;
        $jmldus = $request->jmldus;
        $jmlpack = $request->jmlpack;
        $jmlpcs = $request->jmlpcs;
        $isipcsdus = $request->isipcsdus;
        $isipcs = $request->isipcs;
        $data = array(
            'no_mutasi_gudang_cabang'  => $no_mutasi,
            'tgl_mutasi_gudang_cabang' => $tgl_mutasi_gudang_cabang,
            'kode_cabang'              => $kode_cabang,
            'kondisi'                  => $kondisi,
            'inout_good'               => $inout_good,
            'inout_bad'                => $inout_bad,
            'jenis_mutasi'             => $jenis_mutasi,
            'keterangan'               => $keterangan,
            'order'                    => $order,
            'id_admin'                 => $id_admin
        );
        for ($i = 0; $i < count($kode_produk); $i++) {
            $jml_dus = !empty($jmldus[$i]) ? $jmldus[$i] : 0;
            $jml_pack = !empty($jmlpack[$i]) ? $jmlpack[$i] : 0;
            $jml_pcs = !empty($jmlpcs[$i]) ? $jmlpcs[$i] : 0;

            $jumlah = ($jml_dus * $isipcsdus[$i]) + ($jml_pack * $isipcs[$i]) + $jml_pcs;
            if (!empty($jumlah)) {
                $data_detail[]   = [
                    'no_mutasi_gudang_cabang' => $no_mutasi,
                    'kode_produk'             => $kode_produk[$i],
                    'jumlah'                  => $jumlah
                ];
            }
        }
        //dd($data_detail);
        $ceksa = DB::table('saldoawal_bj')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_cabang', $kode_cabang)->count();
        if ($ceksa > 0) {
            return Redirect::back()->with(['warning' => 'Data Periode Ini Sudah Ditutup, Karena Saldo Bulan Berikutnya Sudah Di Set']);
        } else {
            DB::beginTransaction();
            try {

                DB::table('mutasi_gudang_cabang')->insert($data);
                $chunks = array_chunk($data_detail, 5);
                foreach ($chunks as $chunk) {
                    Detailmutasicabang::insert($chunk);
                }
                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT!']);
            }
        }
    }
}
