<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Logamtokertas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\MockObject\Stub\ReturnReference;

class LogamtokertasController extends Controller
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
        $query = Logamtokertas::query();
        $query->where('kode_cabang', $request->kode_cabang);
        $query->whereBetween('tgl_logamtokertas', [$request->dari, $request->sampai]);
        $logamtokertas = $query->paginate(15);
        $logamtokertas->appends($request->all());
        if ($this->cabang != "PCF") {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->get();
        } else {
            $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        }

        return view('gantilogamtokertas.index', compact('cabang', 'logamtokertas'));
    }

    public function create()
    {
        if ($this->cabang != "PCF") {
            $cabang = Cabang::where('kode_cabang', $this->cabang)->get();
        } else {
            $cabang = Cabang::orderBy('kode_cabang')->get();
        }
        return view('gantilogamtokertas.create', compact('cabang'));
    }

    public function store(Request $request)
    {

        $tgl_logamtokertas = $request->tgl_logamtokertas;
        $kode_cabang = $request->kode_cabang;
        $jumlah_logamtokertas = !empty($request->jumlah_logamtokertas) ? str_replace(".", "", $request->jumlah_logamtokertas) : 0;
        $tanggal = explode("-", $tgl_logamtokertas);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $lg = DB::table('logamtokertas')->whereRaw('YEAR(tgl_logamtokertas)=' . $tahun)->orderBy('kode_logamtokertas', 'desc')->first();
        $lastkode_logamtokertas = $lg != null ? $lg->kode_logamtokertas : '';
        $kode_logamtokertas = buatkode($lastkode_logamtokertas, "LG" . substr($tahun, 2, 2), 4);

        $data = [
            'kode_logamtokertas' => $kode_logamtokertas,
            'tgl_logamtokertas' => $tgl_logamtokertas,
            'kode_cabang' => $kode_cabang,
            'jumlah_logamtokertas' => $jumlah_logamtokertas
        ];

        $simpan = DB::table('logamtokertas')->insert($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan ,Hubungi Tim IT']);
        }
    }

    public function delete($kode_logamtokertas)
    {
        $kode_logamtokertas = Crypt::decrypt($kode_logamtokertas);
        $hapus = DB::table('logamtokertas')->where('kode_logamtokertas', $kode_logamtokertas)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus ,Hubungi Tim IT']);
        }
    }
}
