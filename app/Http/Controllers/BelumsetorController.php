<?php

namespace App\Http\Controllers;

use App\Models\Belumsetor;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class BelumsetorController extends Controller
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
        $query = Belumsetor::query();
        $query->where('tahun', $request->tahun);
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }

        // if ($this->cabang != "PCF") {
        //     if ($this->cabang == "GRT") {
        //         $query->where('kode_cabang', 'TSM');
        //     } else {
        //         $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        //         $cabang[] = "";
        //         foreach ($cbg as $c) {
        //             $cabang[] = $c->kode_cabang;
        //         }
        //         $query->whereIn('kode_cabang', $cabang);
        //     }
        // }

        if ($this->cabang != "PCF") {
            $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
            $cabang[] = "";
            foreach ($cbg as $c) {
                $cabang[] = $c->kode_cabang;
            }
            $query->whereIn('kode_cabang', $cabang);
        }
        $query->orderBy('kode_cabang');
        $query->orderBy('bulan');
        $belumsetor = $query->get();



        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

        lockyear($request->tahun);
        return view('belumsetor.index', compact('bulan', 'belumsetor'));
    }

    public function create()
    {

        if ($this->cabang != "PCF") {
            $cbg = new Cabang();
            $cabang = $cbg->getCabang($this->cabang);
        } else {
            $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        }


        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('belumsetor.create', compact('cabang', 'bulan'));
    }

    public function show($kode_saldobs)
    {
        $belumsetor = DB::table('belumsetor')->where('kode_saldobs', $kode_saldobs)->first();
        $detail = DB::table('belumsetor_detail')
            ->select('belumsetor_detail.*', 'nama_karyawan')
            ->join('karyawan', 'belumsetor_detail.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('kode_saldobs', $kode_saldobs)
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('belumsetor.show', compact('detail', 'belumsetor', 'bulan'));
    }

    public function delete($kode_saldobs)
    {
        $kode_saldobs = Crypt::decrypt($kode_saldobs);
        $hapus = DB::table('belumsetor')->where('kode_saldobs', $kode_saldobs)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function showtemp($kode_cabang, $bulan, $tahun)
    {
        // echo 'test';
        // die;
        $detailtemp = DB::table('belumsetor_temp')
            ->select('belumsetor_temp.*', 'nama_karyawan')
            ->join('karyawan', 'belumsetor_temp.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
        return view('belumsetor.showtemp', compact('detailtemp'));
    }

    public function storetemp(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jumlah = !empty($request->jumlah) ? str_replace(".", "", $request->jumlah) : 0;

        $data = [
            'id_karyawan' => $id_karyawan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jumlah' => $jumlah
        ];
        DB::table('belumsetor_temp')->insert($data);
    }

    public function deletetemp(Request $request)
    {
        $hapus = DB::table('belumsetor_temp')->where('id', $request->id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function cektemp(Request $request)
    {
        $cektemp = DB::table('belumsetor_temp')
            ->join('karyawan', 'belumsetor_temp.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('karyawan.kode_cabang', $request->kode_cabang)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->count();
        echo $cektemp;
    }

    public function store(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $thn = substr($tahun, 2, 2);
        $tanggal = $tahun . "-" . $bulan . "-01";
        $kode_saldobs = "SABS" . $kode_cabang . $bulan . $thn;
        DB::beginTransaction();
        try {
            $data = [
                'kode_saldobs' => $kode_saldobs,
                'tanggal' => $tanggal,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'kode_cabang' => $kode_cabang,
                'id_admin' => Auth::user()->id

            ];

            DB::table('belumsetor')->insert($data);
            $temp = DB::table('belumsetor_temp')
                ->join('karyawan', 'belumsetor_temp.id_karyawan', '=', 'karyawan.id_karyawan')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->where('kode_cabang', $kode_cabang)
                ->get();

            foreach ($temp as $d) {
                $detail = [
                    'kode_saldobs' => $kode_saldobs,
                    'id_karyawan' => $d->id_karyawan,
                    'jumlah' => $d->jumlah
                ];

                DB::table('belumsetor_detail')->insert($detail);
            }
            DB::table('belumsetor_temp')
                ->join('karyawan', 'belumsetor_temp.id_karyawan', '=', 'karyawan.id_karyawan')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->where('kode_cabang', $kode_cabang)
                ->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);;
        }
    }
}
