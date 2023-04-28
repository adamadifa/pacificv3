<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pengajuanizin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class PengajuanizinController extends Controller
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
        $query = Pengajuanizin::query();
        $query->select('pengajuan_izin.*', 'nama_karyawan', 'nama_jabatan', 'kode_dept');
        $query->join('master_karyawan', 'pengajuan_izin.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->where('jenis_izin', 'TM');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('dari', [$request->dari, $request->sampai]);
        }

        if (!empty($request->kode_cabang)) {
            $query->where('master_karyawan.id_kantor', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $query->where('master_karyawan.kode_dept', $request->kode_dept);
        }

        if (!empty($request->nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        $pengajuan_izin = $query->get();
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);

        $departemen = DB::table('departemen')->where('status_pengajuan', 0)->get();
        return view('pengajuanizin.index', compact('pengajuan_izin', 'cabang', 'departemen'));
    }
}
