<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Detailfpb;
use App\Models\Fpb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $produk = Barang::orderBy('nama_barang')->where('status', 1)->get();
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
                $detail_dpb[]   = [
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
                $chunks = array_chunk($detail_dpb, 5);
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
}
