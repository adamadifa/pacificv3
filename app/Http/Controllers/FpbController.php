<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Detailfpb;
use App\Models\Fpb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class FpbController extends Controller
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
        $query = Fpb::query();
        $query->select(
            'fpb.*',
            'nama_karyawan',
            'driver.nama_driver_helper as nama_driver',
            'helper1.nama_driver_helper as nama_helper_1',
            'helper2.nama_driver_helper as nama_helper_2',
            'helper3.nama_driver_helper as nama_helper_3'
        );
        $query->join('karyawan', 'fpb.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->leftJoin('driver_helper as driver', 'fpb.id_driver', '=', 'driver.id_driver_helper');
        $query->leftJoin('driver_helper as helper1', 'fpb.id_helper', '=', 'helper1.id_driver_helper');
        $query->leftJoin('driver_helper as helper2', 'fpb.id_helper_2', '=', 'helper2.id_driver_helper');
        $query->leftJoin('driver_helper as helper3', 'fpb.id_helper_3', '=', 'helper3.id_driver_helper');
        if (!empty($request->no_fpb)) {
            $query->where('no_fpb', $request->no_fpb);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_permintaan', [$request->dari, $request->sampai]);
        }

        $query->where('fpb.kode_cabang', $request->kode_cabang);

        if (!empty($request->id_karyawan)) {
            $query->where('fpb.id_karyawan', $request->id_karyawan);
        }

        $query->orderBy('tgl_permintaan', 'desc');
        $query->orderBy('no_fpb', 'desc');
        $fpb = $query->paginate(15);
        $fpb->appends($request->all());

        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        return view('fpb.index', compact('fpb', 'cabang'));
    }


    public function create()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }

        $dari = date("Y-m-d");
        $sampai = date("Y-m-t", strtotime($dari));
        $hariini = explode("-", $dari);
        $bulan = $hariini[1];
        $tahun = $hariini[0];
        $kode_cabang = $this->cabang;
        $mulai = $tahun . "-" . $bulan . "-01";
        $query = Barang::query();
        $query->selectRaw("master_barang.*,saldo_awal_gs,saldo_awal_bs,pusat,transit_in,retur,lainlain_in,penyesuaian_in,penyesuaianbad_in,repack,
        penjualan,promosi,reject_pasar,reject_mobil,reject_gudang,
        transit_out,lainlain_out,penyesuaian_out,penyesuaianbad_out,kirim_pusat,sisamutasi,sisamutasibad");
        $query->leftJoin(
            DB::raw("(
                SELECT kode_produk,kode_cabang,jumlah as saldo_awal_gs
                FROM saldoawal_bj_detail
                INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                WHERE status ='GS' AND bulan ='$bulan' AND tahun='$tahun' AND kode_cabang='$kode_cabang'
            ) saldo_gs"),
            function ($join) {
                $join->on('master_barang.kode_produk', '=', 'saldo_gs.kode_produk');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT kode_produk,kode_cabang,jumlah as saldo_awal_bs
                FROM saldoawal_bj_detail
                INNER JOIN saldoawal_bj ON saldoawal_bj_detail.kode_saldoawal = saldoawal_bj.kode_saldoawal
                WHERE status ='BS' AND bulan ='$bulan' AND tahun='$tahun' AND kode_cabang='$kode_cabang'
            ) saldo_bs"),
            function ($join) {
                $join->on('master_barang.kode_produk', '=', 'saldo_bs.kode_produk');
            }
        );


        $query->leftJoin(
            DB::raw("(
                SELECT kode_produk,
                SUM(IF(jenis_mutasi = 'SURAT JALAN',jumlah,0)) as pusat,
                SUM(IF(jenis_mutasi = 'TRANSIT IN',jumlah,0)) as transit_in,
                SUM(IF(jenis_mutasi = 'RETUR',jumlah,0)) as retur,
                SUM(IF(jenis_mutasi = 'HUTANG KIRIM' AND inout_good='IN' OR jenis_mutasi='PL TTR' AND inout_good='IN',jumlah,0)) as lainlain_in,
                SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND inout_good='IN',jumlah,0)) as penyesuaian_in,
                SUM(IF(jenis_mutasi = 'PENYESUAIAN BAD' AND inout_bad='IN',jumlah,0)) as penyesuaianbad_in,
                SUM(IF(jenis_mutasi = 'REPACK',jumlah,0)) as repack,

                SUM(IF(jenis_mutasi = 'PENJUALAN',jumlah,0)) as penjualan,
                SUM(IF(jenis_mutasi = 'PROMOSI',jumlah,0)) as promosi,
                SUM(IF(jenis_mutasi = 'REJECT PASAR',jumlah,0)) as reject_pasar,
                SUM(IF(jenis_mutasi = 'REJECT MOBIL',jumlah,0)) as reject_mobil,
                SUM(IF(jenis_mutasi = 'REJECT GUDANG',jumlah,0)) as reject_gudang,
                SUM(IF(jenis_mutasi = 'TRANSIT OUT',jumlah,0)) as transit_out,
                SUM(IF(jenis_mutasi = 'PL HUTANG KIRIM' AND inout_good='OUT'
                OR jenis_mutasi='TTR' AND inout_good='OUT'
                OR jenis_mutasi='GANTI BARANG' AND inout_good='OUT',jumlah,0)) as lainlain_out,
                SUM(IF(jenis_mutasi = 'PENYESUAIAN' AND inout_good='OUT',jumlah,0)) as penyesuaian_out,
                SUM(IF(jenis_mutasi = 'PENYESUAIAN BAD' AND inout_bad='OUT',jumlah,0)) as penyesuaianbad_out,
                SUM(IF(jenis_mutasi = 'KIRIM PUSAT',jumlah,0)) as kirim_pusat
                FROM detail_mutasi_gudang_cabang
                INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                WHERE tgl_mutasi_gudang_cabang BETWEEN '$dari' AND '$sampai' AND kode_cabang='$kode_cabang'
                GROUP BY kode_produk
            ) dmc"),
            function ($join) {
                $join->on('master_barang.kode_produk', '=', 'dmc.kode_produk');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT kode_produk,
                SUM( IF(kode_cabang ='$kode_cabang' AND jenis_mutasi !='KIRIM PUSAT' AND inout_good='IN',jumlah,0)) - SUM(IF(kode_cabang='$kode_cabang' AND jenis_mutasi !='KIRIM PUSAT' AND inout_good='OUT',jumlah,0)) as sisamutasi,
                SUM(IF(kode_cabang='$kode_cabang' AND kondisi ='BAD' AND inout_bad='IN',jumlah,0)) - SUM(IF(kode_cabang='$kode_cabang' AND kondisi ='BAD' AND inout_bad='OUT',jumlah,0)) as sisamutasibad
                FROM detail_mutasi_gudang_cabang
                INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                WHERE tgl_mutasi_gudang_cabang >= '$mulai' AND tgl_mutasi_gudang_cabang < '$dari' AND kode_cabang='$kode_cabang'
                GROUP BY kode_produk
            ) mutasi"),
            function ($join) {
                $join->on('master_barang.kode_produk', '=', 'mutasi.kode_produk');
            }
        );
        $query->where('master_barang.status', 1);
        $produk = $query->get();
        return view('fpb.create', compact('cabang', 'produk'));
    }


    public function store(Request $request)
    {
        $no_fpb = $request->no_fpb;
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $no_polisi = $request->no_polisi;
        $tujuan = $request->tujuan;
        $id_driver = $request->id_driver;
        $id_helper_1 = $request->id_helper_1;
        $id_helper_2 = $request->id_helper_2;
        $id_helper_3 = $request->id_helper_3;
        $tgl_permintaan = $request->tgl_permintaan;

        $kode_produk = $request->kode_produk;
        $jmldus = $request->jmldus;
        $jmlpack = $request->jmlpack;
        $jmlpcs = $request->jmlpcs;
        $isipcsdus = $request->isipcsdus;
        $isipcs = $request->isipcs;


        $data = [
            'no_fpb' => $no_fpb,
            'id_karyawan' => $id_karyawan,
            'kode_cabang' => $kode_cabang,
            'tujuan' => $tujuan,
            'no_kendaraan' => $no_polisi,
            'tgl_permintaan' => $tgl_permintaan,
            'id_driver' => $id_driver,
            'id_helper' => $id_helper_1,
            'id_helper_2' => $id_helper_2,
            'id_helper_3' => $id_helper_3
        ];

        for ($i = 0; $i < count($kode_produk); $i++) {
            $jml_dus = !empty($jmldus[$i]) ? $jmldus[$i] : 0;
            $jml_pack = !empty($jmlpack[$i]) ? $jmlpack[$i] : 0;
            $jml_pcs = !empty($jmlpcs[$i]) ? $jmlpcs[$i] : 0;

            $jmlpcs = ($jml_dus * $isipcsdus[$i]) + ($jml_pack * $isipcs[$i]) + $jml_pcs;

            // if (!empty($jmlpcsambil)) {
            //     $jmlpengambilan = $jmlpcsambil / $isipcsdus[$i];
            // } else {
            //     $jmlpengambilan = 0;
            // }

            // $jmlpengambilan = round($jmlpengambilan, 3);
            if (!empty($jmlpcs)) {
                $detail_fpb[]   = [
                    'no_fpb' => $no_fpb,
                    'kode_produk' => $kode_produk[$i],
                    'jml_permintaan' => $jmlpcs
                ];
            }
        }
        $cek = DB::table('fpb')->where('no_fpb', $no_fpb)->count();
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Sudah Ada']);
        } else {
            DB::beginTransaction();
            try {
                DB::table('fpb')->insert($data);
                $chunks = array_chunk($detail_fpb, 5);
                foreach ($chunks as $chunk) {
                    Detailfpb::insert($chunk);
                }
                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
            }
        }
    }


    public function edit($no_fpb)
    {
        $no_fpb = Crypt::decrypt($no_fpb);
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $fpb = DB::table('fpb')->where('no_fpb', $no_fpb)->first();
        $produk = DB::table('master_barang')
            ->select(
                'master_barang.kode_produk',
                'nama_barang',
                'isipcsdus',
                'isipack',
                'isipcs',
                'satuan',
                'jml_permintaan',
            )
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    jml_permintaan
                FROM
                    fpb_detail
                WHERE no_fpb ='$no_fpb'
            ) detailfpb"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'detailfpb.kode_produk');
                }
            )
            ->where('master_barang.status', 1)
            ->get();
        return view('fpb.edit', compact('cabang', 'produk', 'fpb'));
    }


    public function update($no_fpb, Request $request)
    {
        $no_fpb_old = Crypt::decrypt($no_fpb);
        $no_fpb = $request->no_fpb;
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $no_polisi = $request->no_polisi;
        $tujuan = $request->tujuan;
        $id_driver = $request->id_driver;
        $id_helper_1 = $request->id_helper_1;
        $id_helper_2 = $request->id_helper_2;
        $id_helper_3 = $request->id_helper_3;
        $tgl_permintaan = $request->tgl_permintaan;


        $kode_produk = $request->kode_produk;
        $jmldus = $request->jmldus;
        $jmlpack = $request->jmlpack;
        $jmlpcs = $request->jmlpcs;
        $isipcsdus = $request->isipcsdus;
        $isipcs = $request->isipcs;

        $data = [
            'no_fpb' => $no_fpb,
            'id_karyawan' => $id_karyawan,
            'kode_cabang' => $kode_cabang,
            'tujuan' => $tujuan,
            'no_kendaraan' => $no_polisi,
            'tgl_permintaan' => $tgl_permintaan,
            'id_driver' => $id_driver,
            'id_helper' => $id_helper_1,
            'id_helper_2' => $id_helper_2,
            'id_helper_3' => $id_helper_3
        ];


        for ($i = 0; $i < count($kode_produk); $i++) {
            $jml_dus = !empty($jmldus[$i]) ? $jmldus[$i] : 0;
            $jml_pack = !empty($jmlpack[$i]) ? $jmlpack[$i] : 0;
            $jml_pcs = !empty($jmlpcs[$i]) ? $jmlpcs[$i] : 0;

            $jmlpcs = ($jml_dus * $isipcsdus[$i]) + ($jml_pack * $isipcs[$i]) + $jml_pcs;

            // if (!empty($jmlpcsambil)) {
            //     $jmlpengambilan = $jmlpcsambil / $isipcsdus[$i];
            // } else {
            //     $jmlpengambilan = 0;
            // }

            // $jmlpengambilan = round($jmlpengambilan, 3);
            if (!empty($jmlpcs)) {
                $detail_fpb[]   = [
                    'no_fpb' => $no_fpb,
                    'kode_produk' => $kode_produk[$i],
                    'jml_permintaan' => $jmlpcs
                ];
            }
        }




        DB::beginTransaction();
        try {
            DB::table('fpb')->where('no_fpb', $no_fpb_old)->update($data);
            DB::table('fpb_detail')->where('no_fpb', $no_fpb_old)->delete();
            $chunks = array_chunk($detail_fpb, 5);
            foreach ($chunks as $chunk) {
                Detailfpb::insert($chunk);
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }


    public function show($no_fpb)
    {
        $no_fpb = Crypt::decrypt($no_fpb);
        $fpb = DB::table('fpb')
            ->select(
                'fpb.*',
                'nama_karyawan',
                'driver.nama_driver_helper as nama_driver',
                'helper1.nama_driver_helper as nama_helper_1',
                'helper2.nama_driver_helper as nama_helper_2',
                'helper3.nama_driver_helper as nama_helper_3'
            )
            ->join('karyawan', 'fpb.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('driver_helper as driver', 'fpb.id_driver', '=', 'driver.id_driver_helper')
            ->leftJoin('driver_helper as helper1', 'fpb.id_helper', '=', 'helper1.id_driver_helper')
            ->leftJoin('driver_helper as helper2', 'fpb.id_helper_2', '=', 'helper2.id_driver_helper')
            ->leftJoin('driver_helper as helper3', 'fpb.id_helper_3', '=', 'helper3.id_driver_helper')
            ->where('no_fpb', $no_fpb)
            ->first();

        $detail = DB::table('master_barang')
            ->select(
                'master_barang.kode_produk',
                'nama_barang',
                'isipcsdus',
                'isipack',
                'isipcs',
                'satuan',
                'jml_permintaan',
                'jml_pengambilan',
            )
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    jml_permintaan
                FROM
                    fpb_detail
                WHERE no_fpb ='$no_fpb'
            ) detailfpb"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'detailfpb.kode_produk');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    jml_pengambilan
                FROM
                    detail_dpb
                INNER JOIN dpb ON detail_dpb.no_dpb = dpb.no_dpb
                WHERE no_fpb ='$no_fpb'
            ) detaildpb"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'detaildpb.kode_produk');
                }
            )
            ->whereNotNull('jml_permintaan')
            ->orwhereNotNull('jml_pengambilan')
            ->get();


        return view('fpb.show', compact('fpb', 'detail'));
    }

    public function delete($no_fpb)
    {
        $no_fpb  = Crypt::decrypt($no_fpb);
        $hapus = DB::table('fpb')->where('no_fpb', $no_fpb)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dibatalkan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan, Hubungi Tim IT']);
        }
    }

    public function getautocompletefpb(Request $request)
    {
        $search = $request->search;
        if ($search == '') {
            $query = Fpb::query();
            $query->select('fpb.*', 'nama_karyawan');
            $query->join('karyawan', 'fpb.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->join('cabang', 'fpb.kode_cabang', '=', 'cabang.kode_cabang');
            if ($this->cabang != "PCF") {
                $query->where('fpb.kode_cabang', $this->cabang);
                $query->orWhere('cabang.sub_cabang', $this->cabang);
            }
            $query->orderBy('tgl_permintaan', 'desc');
            $query->orderby('no_fpb', 'desc');
            $query->limit(10);
            $autocomplate = $query->get();
        } else {
            $query = fpb::query();
            $query->select('fpb.*', 'nama_karyawan');
            $query->join('karyawan', 'fpb.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->join('cabang', 'fpb.kode_cabang', '=', 'cabang.kode_cabang');

            if ($this->cabang != "PCF") {
                $query->where('no_fpb', 'like', '%' . $search . '%');
                $query->where('fpb.kode_cabang', $this->cabang);
                $query->orWhere('nama_karyawan', 'like', '%' . $search . '%');
                $query->where('fpb.kode_cabang', $this->cabang);
                $query->orWhere('no_fpb', 'like', '%' . $search . '%');
                $query->where('cabang.sub_cabang', $this->cabang);
                $query->orWhere('nama_karyawan', 'like', '%' . $search . '%');
                $query->where('cabang.sub_cabang', $this->cabang);
            } else {
                $query->where('no_fpb', 'like', '%' . $search . '%');
                $query->orWhere('nama_karyawan', 'like', '%' . $search . '%');
            }
            $query->orderBy('tgl_permintaan', 'desc');
            $query->orderby('no_fpb', 'desc');
            $query->limit(10);
            $autocomplate = $query->get();
        }


        //dd($autocomplate);
        $response = array();
        foreach ($autocomplate as $autocomplate) {
            $label = $autocomplate->no_fpb;
            $response[] = array("value" => $autocomplate->no_fpb, "label" => $label, 'val' => $autocomplate->no_fpb);
        }

        echo json_encode($response);
        exit;
    }


    public function showfpb(Request $request)
    {
        $no_fpb = $request->no_fpb;
        $fpb = DB::table('fpb')
            ->select(
                'fpb.*',
                'nama_karyawan',
                'driver.nama_driver_helper as nama_driver',
                'helper1.nama_driver_helper as nama_helper_1',
                'helper2.nama_driver_helper as nama_helper_2',
                'helper3.nama_driver_helper as nama_helper_3'
            )
            ->join('karyawan', 'fpb.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('driver_helper as driver', 'fpb.id_driver', '=', 'driver.id_driver_helper')
            ->leftJoin('driver_helper as helper1', 'fpb.id_helper', '=', 'helper1.id_driver_helper')
            ->leftJoin('driver_helper as helper2', 'fpb.id_helper_2', '=', 'helper2.id_driver_helper')
            ->leftJoin('driver_helper as helper3', 'fpb.id_helper_3', '=', 'helper3.id_driver_helper')
            ->where('no_fpb', $no_fpb)
            ->first();

        echo $fpb->kode_cabang . "|" . $fpb->no_kendaraan . "|" . $fpb->id_karyawan . "|" . $fpb->id_driver . "|" . $fpb->id_helper . "|" . $fpb->id_helper_2 . "|" . $fpb->id_helper_3 . "|" . $fpb->tujuan;
    }


    public function getdetailfpb(Request $request)
    {
        $no_fpb = $request->no_fpb;
        $produk = DB::table('master_barang')
            ->select(
                'master_barang.kode_produk',
                'nama_barang',
                'isipcsdus',
                'isipack',
                'isipcs',
                'satuan',
                'jml_permintaan',
            )
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    jml_permintaan
                FROM
                    fpb_detail
                WHERE no_fpb ='$no_fpb'
            ) detailfpb"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'detailfpb.kode_produk');
                }
            )
            ->where('master_barang.status', 1)
            ->get();


        return view('fpb.getdetail', compact('produk'));
    }
}
