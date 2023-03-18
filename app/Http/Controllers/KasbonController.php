<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Kasbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class KasbonController extends Controller
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
        $query = Kasbon::query();
        $query->select('kasbon.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'totalpembayaran', 'tgl_bayar', 'jatuh_tempo');
        $query->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('departemen', 'master_karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->leftJoin(
            DB::raw("(
            SELECT no_kasbon,tgl_bayar, jumlah as totalpembayaran FROM kasbon_historibayar
        ) hb"),
            function ($join) {
                $join->on('kasbon.no_kasbon', '=', 'hb.no_kasbon');
            }
        );
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_kasbon', [$request->dari, $request->sampai]);
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
        $query->orderBy('no_kasbon', 'desc');
        $kasbon = $query->paginate(15);
        $kasbon->appends($request->all());


        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);

        $departemen = DB::table('departemen')->where('status_pengajuan', 0)->get();
        return view('kasbon.index', compact('kasbon', 'cabang', 'departemen'));
    }
    public function create($nik)
    {
        $nik = Crypt::decrypt($nik);
        $query = Karyawan::query();
        $query->select('nik', 'nama_karyawan', 'tgl_masuk', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan', 'nama_cabang', 'master_karyawan.id_jabatan');
        $query->join('departemen', 'master_karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftjoin('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang');
        $query->where('nik', $nik);
        $karyawan = $query->first();

        $kontrak = DB::table('hrd_kontrak')->where('nik', $nik)->orderBy('dari', 'desc')->first();
        return view('kasbon.create', compact('karyawan', 'kontrak'));
    }

    public function store(Request $request)
    {
        $nik = $request->nik;
        $tgl_kasbon = $request->tgl_kasbon;
        $jml_kasbon = str_replace(".", "", $request->jml_kasbon);
        $status_karyawan = $request->status_karyawan;
        $akhir_kontrak = $request->akhir_kontrak;
        $id_jabatan = $request->id_jabatan;
        $jatuh_tempo = $request->jatuh_tempo;

        $tgl = explode("-", $tgl_kasbon);
        $tahun = substr($tgl[0], 2, 2);
        $kasbon = DB::table("kasbon")
            ->whereRaw('YEAR(tgl_kasbon)="' . $tgl[0] . '"')
            ->orderBy("no_kasbon", "desc")
            ->first();
        $last_nokasbon = $kasbon != null ? $kasbon->no_kasbon : '';
        $no_kasbon  = buatkode($last_nokasbon, "KB" . $tahun, 3);

        $id_user = Auth::user()->id;

        $data = [
            'no_kasbon' => $no_kasbon,
            'tgl_kasbon' => $tgl_kasbon,
            'nik' => $nik,
            'status_karyawan' => $status_karyawan,
            'akhir_kontrak' => $akhir_kontrak,
            'id_jabatan' => $id_jabatan,
            'jumlah_kasbon' => $jml_kasbon,
            'jatuh_tempo' => $jatuh_tempo,
            'id_user' => $id_user
        ];

        try {
            DB::table('kasbon')->insert($data);
            return redirect('/kasbon')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return redirect('/kasbon')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function delete($no_kasbon)
    {
        $no_kasbon = Crypt::decrypt($no_kasbon);
        DB::beginTransaction();
        try {
            DB::table('kasbon')->where('no_kasbon', $no_kasbon)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }


    public function edit(Request $request)
    {
        $no_kasbon = $request->no_kasbon;
        $kasbon = DB::table('kasbon')
            ->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('departemen', 'master_karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('no_kasbon', $no_kasbon)->first();


        $hariini = date("Y-m-d");

        return view('kasbon.edit', compact('kasbon'));
    }
}
