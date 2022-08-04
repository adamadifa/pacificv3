<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Detaildpb;
use App\Models\Dpb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class DpbController extends Controller
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
        $query = Dpb::query();
        $query->select(
            'dpb.*',
            'nama_karyawan',
            'driver.nama_driver_helper as nama_driver',
            'helper1.nama_driver_helper as nama_helper_1',
            'helper2.nama_driver_helper as nama_helper_2',
            'helper3.nama_driver_helper as nama_helper_3'
        );
        $query->join('karyawan', 'dpb.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->leftJoin('driver_helper as driver', 'dpb.id_driver', '=', 'driver.id_driver_helper');
        $query->leftJoin('driver_helper as helper1', 'dpb.id_helper', '=', 'helper1.id_driver_helper');
        $query->leftJoin('driver_helper as helper2', 'dpb.id_helper_2', '=', 'helper2.id_driver_helper');
        $query->leftJoin('driver_helper as helper3', 'dpb.id_helper_3', '=', 'helper3.id_driver_helper');
        if (!empty($request->no_dpb)) {
            $query->where('no_dpb', $request->no_dpb);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pengambilan', [$request->dari, $request->sampai]);
        }

        $query->where('dpb.kode_cabang', $request->kode_cabang);

        if (!empty($request->id_karyawan)) {
            $query->where('dpb.id_karyawan', $request->id_karyawan);
        }

        $query->orderBy('tgl_pengambilan', 'desc');
        $query->orderBy('no_dpb', 'desc');
        $dpb = $query->paginate(15);
        $dpb->appends($request->all());

        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        return view('dpb.index', compact('dpb', 'cabang'));
    }

    public function show($no_dpb)
    {
        $no_dpb = Crypt::decrypt($no_dpb);
        $dpb = DB::table('dpb')
            ->select(
                'dpb.*',
                'nama_karyawan',
                'driver.nama_driver_helper as nama_driver',
                'helper1.nama_driver_helper as nama_helper_1',
                'helper2.nama_driver_helper as nama_helper_2',
                'helper3.nama_driver_helper as nama_helper_3'
            )
            ->join('karyawan', 'dpb.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('driver_helper as driver', 'dpb.id_driver', '=', 'driver.id_driver_helper')
            ->leftJoin('driver_helper as helper1', 'dpb.id_helper', '=', 'helper1.id_driver_helper')
            ->leftJoin('driver_helper as helper2', 'dpb.id_helper_2', '=', 'helper2.id_driver_helper')
            ->leftJoin('driver_helper as helper3', 'dpb.id_helper_3', '=', 'helper3.id_driver_helper')
            ->where('no_dpb', $no_dpb)
            ->first();

        $detail = DB::table('detail_dpb')
            ->join('master_barang', 'detail_dpb.kode_produk', '=', 'master_barang.kode_produk')
            ->orderBy('detail_dpb.kode_produk')
            ->where('no_dpb', $no_dpb)->get();

        $mutasidpb = DB::table('detail_mutasi_gudang_cabang')
            ->selectRaw("detail_mutasi_gudang_cabang.kode_produk,nama_barang,isipcsdus,
            SUM(IF(jenis_mutasi='PENJUALAN',jumlah,0)) as penjualan,
            SUM(IF(jenis_mutasi='HUTANG KIRIM',jumlah,0)) as hutangkirim,
            SUM(IF(jenis_mutasi='PL TTR',jumlah,0)) as pelunasanttr,
            SUM(IF(jenis_mutasi='GANTI BARANG',jumlah,0)) as gantibarang,
            SUM(IF(jenis_mutasi='PL HUTANG KIRIM',jumlah,0)) as plhutangkirim,
            SUM(IF(jenis_mutasi='TTR',jumlah,0)) as ttr,
            SUM(IF(jenis_mutasi='RETUR',jumlah,0)) as retur,
            SUM(IF(jenis_mutasi='REJECT PASAR',jumlah,0)) as rejectpasar,
            SUM(IF(jenis_mutasi='PROMOSI',jumlah,0)) as promosi")
            ->join('mutasi_gudang_cabang', 'detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang')
            ->join('master_barang', 'detail_mutasi_gudang_cabang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_dpb', $no_dpb)
            ->groupByRaw('detail_mutasi_gudang_cabang.kode_produk,nama_barang,isipcsdus')
            ->orderBy('kode_produk')
            ->get();

        return view('dpb.show', compact('dpb', 'detail', 'mutasidpb'));
    }

    public function create()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $produk = Barang::orderBy('nama_barang')->where('status', 1)->get();
        return view('dpb.create', compact('cabang', 'produk'));
    }

    public function store(Request $request)
    {
        $no_dpb = $request->no_dpb;
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $no_polisi = $request->no_polisi;
        $tujuan = $request->tujuan;
        $id_driver = $request->id_driver;
        $id_helper_1 = $request->id_helper_1;
        $id_helper_2 = $request->id_helper_2;
        $id_helper_3 = $request->id_helper_3;
        $tgl_pengambilan = $request->tgl_pengambilan;

        $kode_produk = $request->kode_produk;
        $jmldus = $request->jmldus;
        $jmlpack = $request->jmlpack;
        $jmlpcs = $request->jmlpcs;
        $isipcsdus = $request->isipcsdus;
        $isipcs = $request->isipcs;


        $data = [
            'no_dpb' => $no_dpb,
            'id_karyawan' => $id_karyawan,
            'kode_cabang' => $kode_cabang,
            'tujuan' => $tujuan,
            'no_kendaraan' => $no_polisi,
            'tgl_pengambilan' => $tgl_pengambilan,
            'id_driver' => $id_driver,
            'id_helper' => $id_helper_1,
            'id_helper_2' => $id_helper_2,
            'id_helper_3' => $id_helper_3
        ];

        for ($i = 0; $i < count($kode_produk); $i++) {
            $jml_dus = !empty($jmldus[$i]) ? $jmldus[$i] : 0;
            $jml_pack = !empty($jmlpack[$i]) ? $jmlpack[$i] : 0;
            $jml_pcs = !empty($jmlpcs[$i]) ? $jmlpcs[$i] : 0;

            $jmlpcsambil = ($jml_dus * $isipcsdus[$i]) + ($jml_pack * $isipcs[$i]) + $jml_pcs;

            if (!empty($jmlpcsambil)) {
                $jmlpengambilan = $jmlpcsambil / $isipcsdus[$i];
            } else {
                $jmlpengambilan = 0;
            }

            $jmlpengambilan = round($jmlpengambilan, 3);
            if (!empty($jmlpengambilan)) {
                $detail_dpb[]   = [
                    'no_dpb' => $no_dpb,
                    'kode_produk' => $kode_produk[$i],
                    'jml_pengambilan' => $jmlpengambilan
                ];
            }
        }
        $cek = DB::table('dpb')->where('no_dpb', $no_dpb)->count();
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Sudah Ada']);
        } else {
            DB::beginTransaction();
            try {
                DB::table('dpb')->insert($data);
                $chunks = array_chunk($detail_dpb, 5);
                foreach ($chunks as $chunk) {
                    Detaildpb::insert($chunk);
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

    public function delete($no_dpb)
    {
        $no_dpb  = Crypt::decrypt($no_dpb);
        $hapus = DB::table('dpb')->where('no_dpb', $no_dpb)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dibatalkan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan, Hubungi Tim IT']);
        }
    }

    public function edit($no_dpb)
    {
        $no_dpb = Crypt::decrypt($no_dpb);
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        $dpb = DB::table('dpb')->where('no_dpb', $no_dpb)->first();
        $produk = DB::table('master_barang')
            ->select(
                'master_barang.kode_produk',
                'nama_barang',
                'isipcsdus',
                'isipack',
                'isipcs',
                'satuan',
                'jml_pengambilan',
                'jml_pengembalian',
                'jml_penjualan'
            )
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    jml_pengambilan,
                    jml_pengembalian,
                    jml_penjualan
                FROM
                    detail_dpb
                WHERE no_dpb ='$no_dpb'
            ) detaildpb"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'detaildpb.kode_produk');
                }
            )
            ->where('master_barang.status', 1)
            ->get();
        return view('dpb.edit', compact('cabang', 'produk', 'dpb'));
    }

    public function update($no_dpb, Request $request)
    {
        $no_dpb_old = Crypt::decrypt($no_dpb);
        $no_dpb = $request->no_dpb;
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $no_polisi = $request->no_polisi;
        $tujuan = $request->tujuan;
        $id_driver = $request->id_driver;
        $id_helper_1 = $request->id_helper_1;
        $id_helper_2 = $request->id_helper_2;
        $id_helper_3 = $request->id_helper_3;
        $tgl_pengambilan = $request->tgl_pengambilan;
        $tgl_pengembalian = $request->tgl_pengembalian;

        $kode_produk = $request->kode_produk;
        $jmlduspengambilan = $request->jmlduspengambilan;
        $jmlpackpengambilan = $request->jmlpackpengambilan;
        $jmlpcspengambilan = $request->jmlpcspengambilan;

        $jmlduspengembalian = $request->jmlduspengembalian;
        $jmlpackpengembalian = $request->jmlpackpengembalian;
        $jmlpcspengembalian = $request->jmlpcspengembalian;

        $jmldusbarangkeluar = $request->jmldusbarangkeluar;
        $jmlpackbarangkeluar = $request->jmlpackbarangkeluar;
        $jmlpcsbarangkeluar = $request->jmlpcsbarangkeluar;

        $isipcsdus = $request->isipcsdus;
        $isipcs = $request->isipcs;

        $jml_helper = $request->jml_helper;
        $jml_helper_2 = $request->jml_helper_2;
        $jml_helper_3 = $request->jml_helper_3;

        $persentase_helper = $request->persentase_helper;
        $persentase_helper_2 = $request->persentase_helper_2;
        $persentase_helper_3 = $request->persentase_helper_3;

        $total_helper = $jml_helper + $jml_helper_2 + $jml_helper_3;
        $totalbarangkeluar_dus = $request->totalbarangkeluar_dus;


        //dd($jmlpackpengambilan);


        $totalbarangkeluar = 0;
        for ($i = 0; $i < count($kode_produk); $i++) {
            $jml_dus_pengambilan = !empty($jmlduspengambilan[$i]) ? $jmlduspengambilan[$i] : 0;
            $jml_pack_pengambilan = !empty($jmlpackpengambilan[$i]) ? $jmlpackpengambilan[$i] : 0;
            $jml_pcs_pengambilan = !empty($jmlpcspengambilan[$i]) ? $jmlpcspengambilan[$i] : 0;

            $jml_dus_pengembalian = !empty($jmlduspengembalian[$i]) ? $jmlduspengembalian[$i] : 0;
            $jml_pack_pengembalian = !empty($jmlpackpengembalian[$i]) ? $jmlpackpengembalian[$i] : 0;
            $jml_pcs_pengembalian = !empty($jmlpcspengembalian[$i]) ? $jmlpcspengembalian[$i] : 0;

            $jml_dus_barangkeluar = !empty($jmldusbarangkeluar[$i]) ? $jmldusbarangkeluar[$i] : 0;
            $jml_pack_barangkeluar = !empty($jmlpackbarangkeluar[$i]) ? $jmlpackbarangkeluar[$i] : 0;
            $jml_pcs_barangkeluar = !empty($jmlpcsbarangkeluar[$i]) ? $jmlpcsbarangkeluar[$i] : 0;

            $jmlpcsambil = ($jml_dus_pengambilan * $isipcsdus[$i]) + ($jml_pack_pengambilan * $isipcs[$i]) + $jml_pcs_pengambilan;
            $jmlpcskembali = ($jml_dus_pengembalian * $isipcsdus[$i]) + ($jml_pack_pengembalian * $isipcs[$i]) + $jml_pcs_pengembalian;
            $jmlpcskeluar = ($jml_dus_barangkeluar * $isipcsdus[$i]) + ($jml_pack_barangkeluar * $isipcs[$i]) + $jml_pcs_barangkeluar;

            if (!empty($jmlpcsambil)) {
                $jmlpengambilan = $jmlpcsambil / $isipcsdus[$i];
            } else {
                $jmlpengambilan = 0;
            }

            $jmlpengambilan = round($jmlpengambilan, 3);

            if (!empty($jmlpcskembali)) {
                $jmlpengembalian = $jmlpcskembali / $isipcsdus[$i];
            } else {
                $jmlpengembalian = 0;
            }

            $jmlpengembalian = round($jmlpengembalian, 3);

            if (!empty($jmlpcskeluar)) {
                $jmlbarangkeluar = $jmlpcskeluar / $isipcsdus[$i];
            } else {
                $jmlbarangkeluar = 0;
            }

            $jmlbarangkeluar = round($jmlbarangkeluar, 3);

            $totalbarangkeluar += $jmlbarangkeluar;
            if (!empty($jmlpengambilan) || !empty($jmlpengembalian) || !empty($jmlbarangkeluar)) {
                $detail_dpb[]   = [
                    'no_dpb' => $no_dpb,
                    'kode_produk' => $kode_produk[$i],
                    'jml_pengambilan' => $jmlpengambilan,
                    'jml_pengembalian' => $jmlpengembalian,
                    'jml_penjualan' => $jmlbarangkeluar,
                ];
            }
        }


        if (!empty($jml_helper)) {
            $jml_helper = $jml_helper;
        } else {
            $jml_helper = ($persentase_helper / 100) * $totalbarangkeluar;
        }

        if (!empty($jml_helper_2)) {
            $jml_helper_2 = $jml_helper_2;
        } else {
            $jml_helper_2 = ($persentase_helper_2 / 100) * $totalbarangkeluar;
        }

        if (!empty($jml_helper_3)) {
            $jml_helper_3 = $jml_helper_3;
        } else {
            $jml_helper_3 = ($persentase_helper_3 / 100) * $totalbarangkeluar;
        }

        $totalhelper_keluar = $jml_helper + $jml_helper_2 + $jml_helper_3;
        if ($totalhelper_keluar > $totalbarangkeluar) {
            return Redirect::back()->with(['warning' => 'Total Helper Lebih Dari Total Barang Keluar']);
        }

        $data = [
            'no_dpb' => $no_dpb,
            'id_karyawan' => $id_karyawan,
            'kode_cabang' => $kode_cabang,
            'tujuan' => $tujuan,
            'no_kendaraan' => $no_polisi,
            'tgl_pengambilan' => $tgl_pengambilan,
            'tgl_pengembalian' => $tgl_pengembalian,
            'id_driver' => $id_driver,
            'id_helper' => $id_helper_1,
            'jml_helper' => $jml_helper,
            'id_helper_2' => $id_helper_2,
            'jml_helper_2' => $jml_helper_2,
            'id_helper_3' => $id_helper_3,
            'jml_helper_3' => $jml_helper_3
        ];
        // echo $jml_helper . "-" . $persentase_helper . "-" . $totalbarangkeluar;
        // die;
        DB::beginTransaction();
        try {
            DB::table('dpb')->where('no_dpb', $no_dpb_old)->update($data);
            DB::table('detail_dpb')->where('no_dpb', $no_dpb_old)->delete();
            $chunks = array_chunk($detail_dpb, 5);
            foreach ($chunks as $chunk) {
                Detaildpb::insert($chunk);
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }

    public function getautocompletedpb(Request $request)
    {
        $search = $request->search;
        if ($search == '') {
            $query = Dpb::query();
            $query->select('dpb.*', 'nama_karyawan');
            $query->join('karyawan', 'dpb.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->join('cabang', 'dpb.kode_cabang', '=', 'cabang.kode_cabang');
            if ($this->cabang != "PCF") {
                $query->where('dpb.kode_cabang', $this->cabang);
                $query->orWhere('cabang.sub_cabang', $this->cabang);
            }
            $query->orderBy('tgl_pengambilan', 'desc');
            $query->orderby('no_dpb', 'desc');
            $query->limit(10);
            $autocomplate = $query->get();
        } else {
            $query = Dpb::query();
            $query->select('dpb.*', 'nama_karyawan');
            $query->join('karyawan', 'dpb.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->join('cabang', 'dpb.kode_cabang', '=', 'cabang.kode_cabang');

            if ($this->cabang != "PCF") {
                $query->where('no_dpb', 'like', '%' . $search . '%');
                $query->where('dpb.kode_cabang', $this->cabang);
                $query->orWhere('nama_karyawan', 'like', '%' . $search . '%');
                $query->where('dpb.kode_cabang', $this->cabang);
                $query->orWhere('no_dpb', 'like', '%' . $search . '%');
                $query->where('cabang.sub_cabang', $this->cabang);
                $query->orWhere('nama_karyawan', 'like', '%' . $search . '%');
                $query->where('cabang.sub_cabang', $this->cabang);
            } else {
                $query->where('no_dpb', 'like', '%' . $search . '%');
                $query->orWhere('nama_karyawan', 'like', '%' . $search . '%');
            }
            $query->orderBy('tgl_pengambilan', 'desc');
            $query->orderby('no_dpb', 'desc');
            $query->limit(10);
            $autocomplate = $query->get();
        }


        //dd($autocomplate);
        $response = array();
        foreach ($autocomplate as $autocomplate) {
            $label = $autocomplate->no_dpb . " - " . $autocomplate->nama_karyawan . " - " . $autocomplate->kode_cabang . " - " . $autocomplate->tujuan . " - " . $autocomplate->no_kendaraan;
            $response[] = array("value" => $autocomplate->nama_karyawan, "label" => $label, 'val' => $autocomplate->no_dpb);
        }

        echo json_encode($response);
        exit;
    }

    public function showdpbmutasi(Request $request)
    {
        $no_dpb = $request->no_dpb;
        $dpb = DB::table('dpb')
            ->select(
                'dpb.*',
                'nama_karyawan',
                'driver.nama_driver_helper as nama_driver',
                'helper1.nama_driver_helper as nama_helper_1',
                'helper2.nama_driver_helper as nama_helper_2',
                'helper3.nama_driver_helper as nama_helper_3'
            )
            ->join('karyawan', 'dpb.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('driver_helper as driver', 'dpb.id_driver', '=', 'driver.id_driver_helper')
            ->leftJoin('driver_helper as helper1', 'dpb.id_helper', '=', 'helper1.id_driver_helper')
            ->leftJoin('driver_helper as helper2', 'dpb.id_helper_2', '=', 'helper2.id_driver_helper')
            ->leftJoin('driver_helper as helper3', 'dpb.id_helper_3', '=', 'helper3.id_driver_helper')
            ->where('no_dpb', $no_dpb)
            ->first();

        return view('dpb.showdpbmutasi', compact('dpb'));
    }
}
