<?php

namespace App\Http\Controllers;

use App\Models\Ajuanrouting;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class AjuanroutingController extends Controller
{
    protected $cabang;
    protected $level;
    public function __construct()
    {
        // Fetch the Site Settings object
        $this->middleware(function ($request, $next) {
            $this->cabang = Auth::user()->kode_cabang;
            $this->level = Auth::user()->level;
            return $next($request);
        });
        View::share('cabang', $this->cabang);
    }


    public function index(Request $request)
    {
        $wilayah = Auth::user()->wilayah;
        $pelanggan = $request->nama_pelanggan;
        $query = Ajuanrouting::query();
        if ($this->cabang != "PCF") {
            $query->where('pelanggan.kode_cabang', $this->cabang);
        } else {
            if (!empty($wilayah)) {
                $wilayah_user = unserialize($wilayah);
                $query->whereIn('pelanggan.kode_cabang', $wilayah_user);
            }
        }
        $query->select('pengajuan_routing.*', 'nama_pelanggan', 'nama_karyawan');
        $query->join('pelanggan', 'pengajuan_routing.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $query->leftJoin('users', 'pengajuan_routing.id_approval', '=', 'users.id');
        if (!empty($request->nama_pelanggan)) {
            $query->where('nama_pelanggan', 'like', '%' . $pelanggan . '%');
        }

        if (!empty($request->kode_cabang)) {
            $query->where('pelanggan.kode_cabang', $request->kode_cabang);
        }



        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pengajuan', [$request->dari, $request->sampai]);
        }


        if ($this->level == "admin" || $this->level == "manager accounting") {
            if ($request->status == "pending") {
                $status = 0;
            } elseif ($request->status == "disetujui") {
                $status = 1;
            } elseif ($request->status == "ditolak") {
                $status = 2;
            }

            if (!empty($request->status)) {
                $query->where('pengajuan_routing.status', $status);
            }
        }
        if ($this->level == "kepala penjualan") {
            if ($request->status == "pending") {
                $query->whereNull('kacab');
            } else if ($request->status == "disetujui") {
                $query->whereNotNull('kacab');
                $query->where('pengajuan_routing.status', '!=', 2);
                $query->orwhereNotNull('kacab');
                $query->where('pengajuan_routing.status', '=', 2);
                $query->where('level', '!=', Auth::user()->level);
            } else if ($request->status == "ditolak") {
                $query->whereNotNull('kacab');
                $query->where('pengajuan_routing.status', 2);
                $query->where('level', Auth::user()->level);
            }
            //$query->where('jumlah', '>', 2000000);
        }


        if ($this->level == "rsm") {
            if ($request->status == "pending") {
                $query->whereNotNull('kacab');
                $query->whereNull('rsm');
            } else if ($request->status == "disetujui") {
                $query->whereNotNull('rsm');
                $query->where('pengajuan_routing.status', '!=', 2);
                $query->orwhereNotNull('rsm');
                $query->where('pengajuan_routing.status', '=', 2);
                $query->where('level', '!=', Auth::user()->level);
            } else if ($request->status == "ditolak") {
                $query->whereNotNull('rsm');
                $query->where('pengajuan_routing.status', 2);
                $query->where('level', Auth::user()->level);
            }
        }

        if ($this->level == "manager marketing") {
            if ($request->status == "pending") {
                $query->whereNotNull('rsm');
                $query->whereNull('mm');
                $query->where('pengajuan_routing.status', 0);
                $query->whereNotNull('rsm');
            } else if ($request->status == "disetujui") {
                $query->whereNotNull('mm');
                $query->where('pengajuan_routing.status', '!=', 2);
                $query->orwhereNotNull('mm');
                $query->where('pengajuan_routing.status', '=', 2);
                $query->where('level', '!=', Auth::user()->level);
            } else if ($request->status == "ditolak") {
                $query->whereNotNull('mm');
                $query->where('pengajuan_routing.status', 2);
                $query->where('level', Auth::user()->level);
            }
        }

        if ($this->level == "direktur") {
            if ($request->status == "pending") {
                $query->whereNotNull('mm');
                $query->whereNull('dirut');
                $query->where('pengajuan_routing.status', 0);
            } else if ($request->status == "disetujui") {
                $query->whereNotNull('dirut');
                $query->where('pengajuan_routing.status', '!=', 2);
                $query->orwhereNotNull('dirut');
                $query->where('pengajuan_routing.status', '=', 2);
                $query->where('level', '!=', Auth::user()->level);
            } else if ($request->status == "ditolak") {
                $query->whereNotNull('mm');
                $query->whereNotNull('dirut');
                $query->where('pengajuan_routing.status', 2);
                $query->where('level', Auth::user()->level);
            }
        }
        $query->orderBy('no_pengajuan');
        $ajuanrouting = $query->paginate(30);
        $ajuanrouting->appends($request->all());
        $cbg = new Cabang();
        $cabang = $cbg->getCabanggudang(Auth::user()->kode_cabang);
        return view('ajuanrouting.index', compact('ajuanrouting', 'cabang'));
    }


    public function create($kode_pelanggan)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)
            ->leftJoin('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->first();
        return view('ajuanrouting.create', compact('pelanggan'));
    }

    public function store($kode_pelanggan, Request $request)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->first();
        $kode_cabang = $pelanggan->kode_cabang;
        $tgl_pengajuan = $request->tgl_pengajuan;
        $hari = "";
        foreach ($request->hari as $d) {
            $hari .= $d . ",";
        }
        $keterangan = $request->keterangan;

        $tgl = explode("-", $tgl_pengajuan);
        $tahun = $tgl[0];
        $thn = substr($tahun, 2, 2);
        $lastajuan = DB::table('pengajuan_routing')
            ->select('no_pengajuan')
            ->whereRaw('YEAR(tgl_pengajuan) = "' . $tahun . '"')
            ->whereRaw('MID(no_pengajuan,4,3) = "' . $kode_cabang . '"')
            ->orderBy('no_pengajuan', 'desc')
            ->first();

        if ($lastajuan == null) {
            $last_no_pengajuan = 'PJR' . $kode_cabang . $thn . '00000';
        } else {
            $last_no_pengajuan = $lastajuan->no_pengajuan;
        }
        $no_pengajuan = buatkode($last_no_pengajuan, 'PJR' . $kode_cabang . $thn, 5);

        try {
            DB::table('pengajuan_routing')->insert([
                'no_pengajuan' => $no_pengajuan,
                'tgl_pengajuan' => $tgl_pengajuan,
                'kode_pelanggan' => $kode_pelanggan,
                'hari' => $hari,
                'keterangan' => $keterangan
            ]);

            if (Auth::user()->level == "salesman") {
                return redirect('/ajuanrouting/salesman')->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return redirect('/ajuanrouting')->with(['success' => 'Data Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            if (Auth::user()->level == "salesman") {
                return redirect('/ajuanrouting/salesman')->with(['warning' => 'Data Gagal Disimpan']);
            } else {
                return redirect('/ajuanrouting')->with(['warning' => 'Data Gagal Disimpan']);
            }

            //throw $th;
        }
    }


    public function edit($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $ajuanrouting = DB::table('pengajuan_routing')->where('no_pengajuan', $no_pengajuan)
            ->select('pengajuan_routing.*', 'nama_pelanggan', 'alamat_pelanggan', 'nama_karyawan')
            ->join('pelanggan', 'pengajuan_routing.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->first();
        return view('ajuanrouting.edit', compact('ajuanrouting'));
    }


    public function update($no_pengajuan, Request $request)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $tgl_pengajuan = $request->tgl_pengajuan;
        $hari = "";
        foreach ($request->hari as $d) {
            $hari .= $d . ",";
        }
        $keterangan = $request->keterangan;



        try {
            DB::table('pengajuan_routing')
                ->where('no_pengajuan', $no_pengajuan)
                ->update([
                    'tgl_pengajuan' => $tgl_pengajuan,
                    'hari' => $hari,
                    'keterangan' => $keterangan
                ]);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
            //throw $th;
        }
    }


    public function approve($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $id_admin = Auth::user()->id;
        $level = Auth::user()->level;

        if ($level == 'kepala cabang' || $level == 'kepala penjualan') {
            $lv = 'kacab';
            $status = 0;
        } else if ($level == 'rsm') {
            $lv = 'rsm';
            $status = 0;
        } else if ($level == 'manager marketing') {
            $lv = 'mm';
            $status = 0;
        } else if ($level == 'direktur') {
            $lv = 'dirut';
            $status = 1;
        }



        $datastatus = [
            'status' => $status,
            $lv => $id_admin,
            'id_approval' => $id_admin,
        ];





        DB::beginTransaction();
        try {

            DB::table('pengajuan_routing')
                ->where('no_pengajuan', $no_pengajuan)
                ->update($datastatus);

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Setujui']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disetujui Hubungi Tim IT']);
        }
    }


    public function decline($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $id_admin = Auth::user()->id;
        $level = Auth::user()->level;

        if ($level == 'kepala cabang' || $level == 'kepala penjualan') {
            $lv = 'kacab';
            $status = null;
        } else if ($level == 'rsm') {
            $lv = 'rsm';
            $status = null;
        } else if ($level == 'manager marketing') {
            $lv = 'mm';
            $status = null;
        } else if ($level == 'direktur') {
            $lv = 'dirut';
            $status = 2;
        }



        $datastatus = [
            'status' => $status,
            $lv => $id_admin,
            'id_approval' => $id_admin,
        ];





        DB::beginTransaction();
        try {

            DB::table('pengajuan_routing')
                ->where('no_pengajuan', $no_pengajuan)
                ->update($datastatus);

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Setujui']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disetujui Hubungi Tim IT']);
        }
    }


    public function delete($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        try {
            DB::table('pengajuan_routing')->where('no_pengajuan', $no_pengajuan)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => 'Data Gagal Disimpan']);
        }
    }
}
