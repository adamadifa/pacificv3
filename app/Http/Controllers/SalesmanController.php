<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Salesman;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\RedisJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use PDOException;

class SalesmanController extends Controller
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
        $query = Salesman::query();
        if ($this->cabang != "PCF") {
            $query->where('karyawan.kode_cabang', $this->cabang);
        } else {
            $wilayah = Auth::user()->wilayah;
            if (!empty($wilayah)) {
                $wilayah_user = unserialize($wilayah);
                $query->whereIn('karyawan.kode_cabang', $wilayah_user);
            }
        }
        if (isset($request->submit)) {
            if ($request->nama != "") {
                $query->where('nama_karyawan', 'like', '%' . $request->nama . '%');
            }

            if ($request->kode_cabang != "") {
                $query->where('kode_cabang', $request->kode_cabang);
            }
        }
        $query->select('karyawan.*');
        $query->orderBy('kode_cabang', 'asc');
        $query->orderBy('id_karyawan', 'asc');
        $salesman = $query->paginate(15);
        $salesman->appends($request->all());
        $kode_cabang = Auth::user()->kode_cabang;
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($kode_cabang);
        return view('salesman.index', compact('salesman', 'cabang'));
    }

    public function create()
    {
        $cabang = Cabang::all();
        return view('salesman.create', compact('cabang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required',
            'nama_karyawan' => 'required',
            'alamat_karyawan' => 'required',
            'no_hp' => 'required|numeric',
            'kode_cabang' => 'required',
            'kategori_salesman' => 'required',
            'status_aktif_sales' => 'required',
            'status_komisi' => 'required',
            'kode_sales' => 'required'
        ]);

        $simpan = DB::table('karyawan')->insert([
            'id_karyawan' => $request->id_karyawan,
            'nama_karyawan' => $request->nama_karyawan,
            'alamat_karyawan' => $request->alamat_karyawan,
            'no_hp' => $request->no_hp,
            'kode_cabang' => $request->kode_cabang,
            'kategori_salesman' => $request->kategori_salesman,
            'status_aktif_sales' => $request->status_aktif_sales,
            'status_komisi' => $request->status_komisi,
            'kode_sales' => $request->kode_sales
        ]);

        if ($simpan) {
            return redirect('/salesman')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/salesman')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function edit($id_karyawan)
    {
        $id_karyawan = Crypt::decrypt($id_karyawan);
        $data = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        $cabang = Cabang::all();
        return view('salesman.edit', compact('data', 'cabang'));
    }

    public function update(Request $request, $id_karyawan)
    {
        $id_karyawan = Crypt::decrypt($id_karyawan);
        $request->validate([
            'nama_karyawan' => 'required',
            'alamat_karyawan' => 'required',
            'no_hp' => 'required|numeric',
            'kode_cabang' => 'required',
            'kategori_salesman' => 'required',
            'status_aktif_sales' => 'required',
            'status_komisi' => 'required',
            'kode_sales' => 'required'
        ]);

        $simpan = DB::table('karyawan')
            ->where('id_karyawan', $id_karyawan)
            ->update([
                'nama_karyawan' => $request->nama_karyawan,
                'alamat_karyawan' => $request->alamat_karyawan,
                'no_hp' => $request->no_hp,
                'kode_cabang' => $request->kode_cabang,
                'kategori_salesman' => $request->kategori_salesman,
                'status_aktif_sales' => $request->status_aktif_sales,
                'status_komisi' => $request->status_komisi,
                'kode_sales' => $request->kode_sales
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function delete($id_karyawan)
    {
        $id_karyawan = Crypt::decrypt($id_karyawan);
        try {
            $hapus = DB::table('karyawan')
                ->where('id_karyawan', $id_karyawan)
                ->delete();

            if ($hapus) {
                return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
            }
        } catch (PDOException $e) {
            $errorcode = $e->getCode();
            if ($errorcode == 23000) {
                return Redirect::back()->with(['danger' => 'Data Tidak Dapat Dihapus Karena Sudah Memiliki Transaksi']);
            }
        }
    }

    public function show(Request $request)
    {
        $id_karyawan = Crypt::decrypt($request->id_karyawan);
        $data = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        return view('salesman.show', compact('data'));
    }

    public function getsalescab(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        if (Auth::user()->level == "salesman") {
            $salesman = Salesman::where('kode_cabang', $kode_cabang)->where('status_aktif_sales', 1)->where('id_karyawan', Auth::user()->id_salesman)->get();
        } else {
            $salesman = Salesman::where('kode_cabang', $kode_cabang)->where('status_aktif_sales', 1)->get();
            // $salesman = Salesman::where('kode_cabang', $kode_cabang)->get();
        }
        $type = Auth::user()->level == "salesman" ? 1 : $request->type;
        if ($type == 1) {
            echo "<option value=''>Pilih Salesman</option>";
        } else {
            echo "<option value=''>Semua Salesman</option>";
        }
        foreach ($salesman as $d) {
            if ($id_karyawan == $d->id_karyawan || Auth::user()->id_salesman == $d->id_karyawan) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            echo "<option $selected value='$d->id_karyawan'>$d->nama_karyawan ($d->kode_cabang)</option>";
        }
    }
}
