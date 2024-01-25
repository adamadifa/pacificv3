<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Piutangkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class PiutangkaryawanController extends Controller
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

        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);

        $departemen = DB::table('hrd_departemen')->get();
        $query = Piutangkaryawan::query();
        $query->select('pinjaman_nonpjp.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'totalpembayaran');
        $query->join('master_karyawan', 'pinjaman_nonpjp.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman_nonpjp,SUM(jumlah) as totalpembayaran FROM pinjaman_nonpjp_historibayar GROUP BY no_pinjaman_nonpjp
        ) hb"),
            function ($join) {
                $join->on('pinjaman_nonpjp.no_pinjaman_nonpjp', '=', 'hb.no_pinjaman_nonpjp');
            }
        );

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pinjaman', [$request->dari, $request->sampai]);
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

        if (Auth::user()->level != "manager accounting" || Auth::user()->level != 'admin') {
            $query->whereNull('manajemen');
        }

        $query->orderBy('no_pinjaman_nonpjp', 'desc');
        $pinjaman = $query->paginate(15);
        $pinjaman->appends($request->all());

        return view('piutangkaryawan.index', compact('cabang', 'departemen', 'pinjaman'));
    }


    public function delete($no_pinjaman_nonpjp)
    {
        $no_pinjaman_nonpjp = Crypt::decrypt($no_pinjaman_nonpjp);
        DB::beginTransaction();
        try {
            DB::table('pinjaman_nonpjp')->where('no_pinjaman_nonpjp', $no_pinjaman_nonpjp)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }


    public function show(Request $request)
    {
        $no_pinjaman_nonpjp = $request->no_pinjaman_nonpjp;
        $pinjaman = DB::table('pinjaman_nonpjp')
            ->join('master_karyawan', 'pinjaman_nonpjp.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('pinjaman_nonpjp.no_pinjaman_nonpjp', $no_pinjaman_nonpjp)->first();


        $hariini = date("Y-m-d");

        return view('piutangkaryawan.show', compact('pinjaman'));
    }


    public function gethistoribayar(Request $request)
    {
        $no_pinjaman_nonpjp = $request->no_pinjaman_nonpjp;
        $histori = DB::table('pinjaman_nonpjp_historibayar')
            ->join('users', 'pinjaman_nonpjp_historibayar.id_user', '=', 'users.id')
            ->where('no_pinjaman_nonpjp', $no_pinjaman_nonpjp)
            ->orderBy('tgl_bayar', 'asc')
            ->get();
        return view('piutangkaryawan.gethistoribayar', compact('histori', 'no_pinjaman_nonpjp'));
    }
}
