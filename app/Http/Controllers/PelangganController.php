<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Salesman;
use Facade\FlareClient\Stacktrace\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;
use PDOException;

class PelangganController extends Controller
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



        $query = Pelanggan::query();
        // if ($this->cabang != "PCF") {
        //     if ($this->cabang == "GRT") {
        //         $query->where('pelanggan.kode_cabang', 'TSM');
        //     } else {
        //         $query->where('pelanggan.kode_cabang', $this->cabang);
        //     }
        // }

        if ($this->cabang != "PCF") {
            $query->where('pelanggan.kode_cabang', $this->cabang);
        }
        if (Auth::user()->level == "salesman") {
            $query->where('pelanggan.id_sales', Auth::user()->id_salesman);
        } else {
            $wilayah = Auth::user()->wilayah;
            if (!empty($wilayah)) {
                $wilayah_user = unserialize($wilayah);
                $query->whereIn('pelanggan.kode_cabang', $wilayah_user);
            }
        }



        if (isset($request->submit) || isset($request->export)) {
            if ($request->nama != "") {
                $query->where('nama_pelanggan', 'like', '%' . $request->nama . '%');
            }


            if ($request->kode_cabang != "") {
                $query->where('pelanggan.kode_cabang', $request->kode_cabang);
            }

            if ($request->id_karyawan != "") {
                $query->where('pelanggan.id_sales', $request->id_karyawan);
            }

            if ($request->status_pelanggan != "") {
                $query->where('pelanggan.status_pelanggan', $request->status_pelanggan);
            }

            if ($request->dari != "" && $request->sampai != "") {
                $query->whereBetween('pelanggan.time_stamps', [$request->dari, $request->sampai]);
            }

            if (!empty($request->kode_pelanggan)) {
                $query->where('pelanggan.kode_pelanggan', $request->kode_pelanggan);
            }
        }
        $query->select('pelanggan.*', 'nama_karyawan');
        $query->orderBy('status_pelanggan', 'desc');
        $query->orderBy('nama_pelanggan', 'asc');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        if (isset($request->export)) {
            $pelanggan = $query->get();
        } else {

            $pelanggan = $query->paginate(15);
            $pelanggan->appends($request->all());
        }


        $query2 = Pelanggan::query();
        // if ($this->cabang != "PCF") {
        //     if ($this->cabang == "GRT") {
        //         $query2->where('pelanggan.kode_cabang', 'TSM');
        //     } else {
        //         $query2->where('pelanggan.kode_cabang', $this->cabang);
        //     }
        // }
        if (Auth::user()->level == "salesman") {
            $query2->where('pelanggan.id_sales', Auth::user()->id_salesman);
        }

        if ($this->cabang != "PCF") {
            $query2->where('pelanggan.kode_cabang', $this->cabang);
        } else {

            if (Auth::user()->id == 82) {
                $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB', 'BTN');
                $query2->whereIn('pelanggan.kode_cabang', $wilayah_barat);
            } else if (Auth::user()->id == 97) {
                $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
                $query2->whereIn('pelanggan.kode_cabang', $wilayah_timur);
            }
        }
        if (isset($request->submit)) {
            if ($request->nama != "") {
                $query2->where('nama_pelanggan', 'like', '%' . $request->nama . '%');
            }

            if ($request->kode_cabang != "") {
                $query2->where('pelanggan.kode_cabang', $request->kode_cabang);
            }

            if ($request->id_karyawan != "") {
                $query2->where('pelanggan.id_sales', $request->id_karyawan);
            }

            if ($request->status_pelanggan != "") {
                $query2->where('pelanggan.status_pelanggan', $request->status_pelanggan);
            }

            if ($request->dari != "" && $request->sampai != "") {
                $query2->whereBetween('pelanggan.time_stamps', [$request->dari, $request->sampai]);
            }

            if (!empty($request->kode_pelanggan)) {
                $query2->where('pelanggan.kode_pelanggan', $request->kode_pelanggan);
            }
        }
        $query2->select('pelanggan.*', 'nama_karyawan');
        $query2->orderBy('status_pelanggan', 'desc');
        $query2->orderBy('nama_pelanggan', 'asc');
        $query2->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');



        $queryaktif = Pelanggan::query();
        // if ($this->cabang != "PCF") {
        //     if ($this->cabang == "GRT") {
        //         $queryaktif->where('pelanggan.kode_cabang', 'TSM');
        //     } else {
        //         $queryaktif->where('pelanggan.kode_cabang', $this->cabang);
        //     }
        // }

        if ($this->cabang != "PCF") {
            $queryaktif->where('pelanggan.kode_cabang', $this->cabang);
        }

        if (Auth::user()->level == "salesman") {
            $queryaktif->where('pelanggan.id_sales', Auth::user()->id_salesman);
        } else {
            if (Auth::user()->id == 82) {
                $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB', 'BTN');
                $queryaktif->whereIn('pelanggan.kode_cabang', $wilayah_barat);
            } else if (Auth::user()->id == 97) {
                $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
                $queryaktif->whereIn('pelanggan.kode_cabang', $wilayah_timur);
            }
        }


        if (isset($request->submit)) {
            if ($request->nama != "") {
                $queryaktif->where('nama_pelanggan', 'like', '%' . $request->nama . '%');
            }

            if ($request->kode_cabang != "") {
                $queryaktif->where('pelanggan.kode_cabang', $request->kode_cabang);
            }

            if ($request->id_karyawan != "") {
                $queryaktif->where('pelanggan.id_sales', $request->id_karyawan);
            }

            if ($request->dari != "" && $request->sampai != "") {
                $queryaktif->whereBetween('pelanggan.time_stamps', [$request->dari, $request->sampai]);
            }

            if (!empty($request->kode_pelanggan)) {
                $queryaktif->where('pelanggan.kode_pelanggan', $request->kode_pelanggan);
            }
        }
        $queryaktif->select('pelanggan.*', 'nama_karyawan');
        $queryaktif->orderBy('status_pelanggan', 'desc');
        $queryaktif->orderBy('nama_pelanggan', 'asc');
        $queryaktif->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $queryaktif->where('pelanggan.status_pelanggan', 1);



        $querynonaktif = Pelanggan::query();
        // if ($this->cabang != "PCF") {
        //     if ($this->cabang == "GRT") {
        //         $querynonaktif->where('pelanggan.kode_cabang', 'TSM');
        //     } else {
        //         $querynonaktif->where('pelanggan.kode_cabang', $this->cabang);
        //     }
        // }
        if (Auth::user()->level == "salesman") {
            $querynonaktif->where('pelanggan.id_sales', Auth::user()->id_salesman);
        }

        if ($this->cabang != "PCF") {
            $querynonaktif->where('pelanggan.kode_cabang', $this->cabang);
        } else {

            if (Auth::user()->id == 82) {
                $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB', 'BTN');
                $querynonaktif->whereIn('pelanggan.kode_cabang', $wilayah_barat);
            } else if (Auth::user()->id == 97) {
                $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
                $querynonaktif->whereIn('pelanggan.kode_cabang', $wilayah_timur);
            }
        }

        if (isset($request->submit)) {
            if ($request->nama != "") {
                $querynonaktif->where('nama_pelanggan', 'like', '%' . $request->nama . '%');
            }

            if ($request->kode_cabang != "") {
                $querynonaktif->where('pelanggan.kode_cabang', $request->kode_cabang);
            }

            if ($request->id_karyawan != "") {
                $querynonaktif->where('pelanggan.id_sales', $request->id_karyawan);
            }

            if ($request->dari != "" && $request->sampai != "") {
                $querynonaktif->whereBetween('pelanggan.time_stamps', [$request->dari, $request->sampai]);
            }

            if (!empty($request->kode_pelanggan)) {
                $querynonaktif->where('pelanggan.kode_pelanggan', $request->kode_pelanggan);
            }
        }
        $querynonaktif->select('pelanggan.*', 'nama_karyawan');
        $querynonaktif->orderBy('status_pelanggan', 'desc');
        $querynonaktif->orderBy('nama_pelanggan', 'asc');
        $querynonaktif->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $querynonaktif->where('pelanggan.status_pelanggan', 0);

        $jmlpelanggan = $query2->count();
        $jmlaktif = $queryaktif->count();
        $jmlnonaktif = $querynonaktif->count();
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        if (isset($request->export)) {
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Data Pelanggan.xls");
            $cbg = $request->kode_cabang;
            $id_karyawan = $request->id_karyawan;
            $salesman = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
            $dari = $request->dari;
            $sampai = $request->sampai;

            return view('pelanggan.export', compact('pelanggan', 'cbg', 'salesman', 'dari', 'sampai'));
        } else {
            return view('pelanggan.index', compact('pelanggan', 'cabang', 'jmlpelanggan', 'jmlaktif', 'jmlnonaktif'));
        }
    }

    public function pelanggansalesman(Request $request)
    {
        // $id_karyawan = Auth::user()->id_salesman;

        // $pelanggan = DB::table('pelanggan')
        //     ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
        //     ->where('pelanggan.kode_cabang', Auth::user()->kode_cabang)
        //     ->where('id_sales', $id_karyawan)
        //     ->where('status_pelanggan', 1)
        //     ->where('nama_pelanggan', 'like', '%' . $request->nama_pelanggan . '%')
        //     ->limit(10)
        //     ->orderBy('nama_pelanggan', 'desc')
        //     ->get();
        return view('pelanggan.indexsalesman');
    }

    public function create()
    {
        $cabang = Cabang::all();
        if ($this->cabang == "PCF") {
            $pasar = DB::table('master_pasar')->get();
        } else {
            // if ($this->cabang == "GRT") {
            //     $pasar = DB::table('master_pasar')->where('kode_cabang', 'TSM')->orderBy('nama_pasar')->get();
            // } else {
            //     $pasar = DB::table('master_pasar')->where('kode_cabang', $this->cabang)->orderBy('nama_pasar')->get();
            // }

            $pasar = DB::table('master_pasar')->where('kode_cabang', $this->cabang)->orderBy('nama_pasar')->get();
        }
        return view('pelanggan.create', compact('cabang', 'pasar'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required',
            'alamat_pelanggan' => 'required',
            'alamat_toko' => 'required',
            'no_hp' => 'required',
            'pasar' => 'required',
            'kode_cabang' => 'required',
            'id_karyawan' => 'required',
            'foto' => 'mimes:png,jpg,jpeg|max:1024', // max 1MB

        ]);



        $pelanggan = DB::table('pelanggan')
            ->select('kode_pelanggan')
            ->whereRaw('LEFT(kode_pelanggan,3) = "' . $request->kode_cabang . '"')
            ->orderBy('kode_pelanggan', 'desc')
            ->first();

        $kodepelangganterakhir = $pelanggan != null ? $pelanggan->kode_pelanggan : '';
        $kodepelanggan = buatkode($kodepelangganterakhir, $request->kode_cabang . '-', 5);

        //Upload File
        if ($request->hasfile('foto')) {
            $image = $request->file('foto');
            $image_name =  $kodepelanggan . "." . $request->file('foto')->getClientOriginalExtension();
            $destination_path = "/public/pelanggan";
            $upload = $request->file('foto')->storeAs($destination_path, $image_name);
            $foto = $image_name;
        } else {
            $foto = NULL;
        }

        // Storage::putFileAs(new File('/public/pelanggan'), $image);
        // $path = Storage::putFileAs(
        //     'public/pelanggan',
        //     $request->file('foto', $image),
        // );

        if (isset($request->lokasi)) {
            $lokasi = $request->lokasi;
            $lok = explode(",", $lokasi);
            $latitude = $lok[0];
            $longitude = $lok[1];
        } else {
            $latitude = "";
            $longitude = "";
        }

        // $hari = "";
        // foreach ($request->hari as $d) {
        //     $hari .= $d . ",";
        // }

        $hari = $request->hari;
        try {
            $simpan = DB::table('pelanggan')->insert([
                'kode_pelanggan' => $kodepelanggan,
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'nama_pelanggan' => $request->nama_pelanggan,
                'tgl_lahir' => $request->tgl_lahir,
                'alamat_pelanggan' => $request->alamat_pelanggan,
                'alamat_toko' => $request->alamat_toko,
                'no_hp' => $request->no_hp,
                'hari' => $hari,
                'pasar' => $request->pasar,
                'kode_cabang' => $request->kode_cabang,
                'id_sales' => $request->id_karyawan,
                'limitpel' => $request->limitpel,
                'jatuhtempo' => $request->jatuhtempo,
                'status_pelanggan' => $request->status_pelanggan,
                'kepemilikan' => $request->kepemilikan,
                'lama_usaha' => $request->lama_usaha,
                'status_outlet' => $request->status_outlet,
                'type_outlet' => $request->type_outlet,
                'cara_pembayaran' => $request->cara_pembayaran,
                'lama_langganan' => $request->lama_langganan,
                'jaminan' => $request->jaminan,
                'omset_toko' => $request->omset_toko,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'foto' => $foto
            ]);
            if (Auth::user()->level != "salesman") {
                return redirect('/pelanggan')->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return redirect('/pelanggansalesman')->with(['success' => 'Data Berhasil Disimpan']);
            }
        } catch (\Exception $e) {

            dd($e);
            if (Auth::user()->level != "salesman") {
                return redirect('/pelanggan')->with(['warning' => 'Data Gagal Disimpan']);
            } else {
                return redirect('/pelanggansalesman')->with(['warning' => 'Data Gagal Disimpan']);
            }
        }
    }

    public function edit($kode_pelanggan)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $data = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->first();
        $cabang = Cabang::all();
        if ($this->cabang == "PCF") {
            $pasar = DB::table('master_pasar')->get();
        } else {
            // if ($this->cabang == "GRT") {
            //     $pasar = DB::table('master_pasar')->where('kode_cabang', 'TSM')->orderBy('nama_pasar')->get();
            // } else {
            //     $pasar = DB::table('master_pasar')->where('kode_cabang', $this->cabang)->orderBy('nama_pasar')->get();
            // }

            $pasar = DB::table('master_pasar')->where('kode_cabang', $this->cabang)->orderBy('nama_pasar')->get();
        }
        return view('pelanggan.edit', compact('data', 'cabang', 'pasar'));
    }

    public function delete($kode_pelanggan)
    {


        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $pelanggan = Pelanggan::where('kode_pelanggan', $kode_pelanggan)->first();
        $file = $pelanggan->foto;



        try {
            $hapus = DB::table('pelanggan')
                ->where('kode_pelanggan', $kode_pelanggan)
                ->delete();

            if ($hapus) {
                Storage::delete('public/pelanggan/' . $file);
                return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
            }
        } catch (PDOException $e) {
            $errorcode = $e->getCode();
            if ($errorcode == 23000) {
                return Redirect::back()->with(['warning' => 'Data Tidak Dapat Dihapus Karena Sudah Memiliki Transaksi']);
            }
        }
    }

    public function update(Request $request, $kode_pelanggan)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $pelanggan = Pelanggan::where('kode_pelanggan', $kode_pelanggan)->first();
        // $hari = "";

        // foreach ($request->hari as $d) {
        //     $hari .= $d . ",";
        // }

        $hari = $request->hari;

        $file = $pelanggan->foto;
        $request->validate([
            'nama_pelanggan' => 'required',
            'alamat_pelanggan' => 'required',
            'alamat_toko' => 'required',
            'no_hp' => 'required',
            'pasar' => 'required',

            'kode_cabang' => 'required',
            'id_karyawan' => 'required',
            'status_pelanggan' => 'required',
            'foto' => 'mimes:png,jpg,jpeg|max:1024', // max 1MB

        ]);

        if ($request->hasfile('foto')) {
            $foto = $kode_pelanggan . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $file;
        }
        if (isset($request->lokasi)) {
            $lokasi = $request->lokasi;
            $lok = explode(",", $lokasi);
            $latitude = $lok[0];
            $longitude = $lok[1];
        } else {
            $latitude = "";
            $longitude = "";
        }
        $simpan = DB::table('pelanggan')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->update([
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'nama_pelanggan' => $request->nama_pelanggan,
                'tgl_lahir' => $request->tgl_lahir,
                'alamat_pelanggan' => $request->alamat_pelanggan,
                'alamat_toko' => $request->alamat_toko,
                'no_hp' => $request->no_hp,
                'hari' => $hari,
                'pasar' => $request->pasar,
                'kode_cabang' => $request->kode_cabang,
                'id_sales' => $request->id_karyawan,
                'limitpel' => str_replace(".", "", $request->limitpel),
                'jatuhtempo' => $request->jatuhtempo,
                'status_pelanggan' => $request->status_pelanggan,
                'kepemilikan' => $request->kepemilikan,
                'lama_usaha' => $request->lama_usaha,
                'status_outlet' => $request->status_outlet,
                'type_outlet' => $request->type_outlet,
                'cara_pembayaran' => $request->cara_pembayaran,
                'lama_langganan' => $request->lama_langganan,
                'jaminan' => $request->jaminan,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'foto' => $foto,
                'omset_toko' => str_replace(".", "", $request->omset_toko)
            ]);

        if ($simpan) {
            //Upload File
            if ($request->hasfile('foto')) {
                Storage::delete('public/pelanggan/' . $file);
                $image = $request->file('foto');
                $image_name =  $kode_pelanggan . "." . $request->file('foto')->getClientOriginalExtension();
                $destination_path = "/public/pelanggan";
                $upload = $request->file('foto')->storeAs($destination_path, $image_name);
            }
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Updat']);
        }
    }

    public function show($kode_pelanggan, Request $request)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);

        $query = Penjualan::query();
        $query->select('penjualan.*', 'nama_pelanggan', 'nama_karyawan', 'karyawan.kode_cabang');
        $query->orderBy('tgltransaksi', 'desc');
        $query->orderBy('no_fak_penj', 'asc');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->where('penjualan.kode_pelanggan', $kode_pelanggan);

        if (!empty($request->no_fak_penj)) {
            $query->where('no_fak_penj', $request->no_fak_penj);
        }
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgltransaksi', [$request->dari, $request->sampai]);
        }
        $penjualan = $query->paginate(10);
        $penjualan->appends($request->all());


        $limitkredit = DB::table('pengajuan_limitkredit_v3')
            ->select('no_pengajuan', 'tgl_pengajuan', 'jumlah', 'jumlah_rekomendasi', 'jatuhtempo', 'jatuhtempo_rekomendasi', 'skor', 'status', 'kacab', 'mm', 'gm', 'dirut')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->orderBy('tgl_pengajuan', 'asc')
            ->get();
        $data = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)
            ->select('pelanggan.*', 'nama_karyawan', 'nama_cabang')
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->join('cabang', 'pelanggan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        return view('pelanggan.show', compact('data', 'penjualan', 'limitkredit'));
    }

    public function json()
    {

        $query = Pelanggan::query();
        $query->select('pelanggan.*', 'karyawan.nama_karyawan', 'karyawan.kategori_salesman', 'status_promo', DB::raw('IF(status_pelanggan=1,"Aktif","NonAktif") as status_pel'));
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        // $query->where('status_pelanggan', '1');
        // if ($this->cabang != "PCF") {
        //     if ($this->cabang == "GRT") {
        //         $query->where('karyawan.kode_cabang', 'TSM');
        //     } else {
        //         $query->where('karyawan.kode_cabang', $this->cabang);
        //     }
        // }
        if (Auth::user()->level == "salesman") {
            $query->where('pelanggan.id_sales', Auth::user()->id_salesman);
        }
        if ($this->cabang != "PCF") {
            $query->where('karyawan.kode_cabang', $this->cabang);
        }
        $pelanggan = $query;





        return DataTables::of($pelanggan)
            ->addColumn('action', function ($pelanggan) {
                $limitpel = !empty($pelanggan->limitpel) ? $pelanggan->limitpel : 0;
                return '<a href="#" class="btn btn-sm btn-primary"
                kode_pelanggan="' . $pelanggan->kode_pelanggan . '"
                nama_pelanggan="' . $pelanggan->nama_pelanggan . '"
                id_karyawan ="' . $pelanggan->id_sales . '"
                nama_karyawan ="' . $pelanggan->nama_karyawan . '"
                kategori_salesman ="' . $pelanggan->kategori_salesman . '"
                alamat_pelanggan ="' . ucwords(strtolower($pelanggan->alamat_pelanggan)) . '"
                no_hp ="' . $pelanggan->no_hp . '"
                pasar ="' . $pelanggan->pasar . '"
                latitude ="' . $pelanggan->latitude . '"
                longitude ="' . $pelanggan->longitude . '"
                foto = "' . $pelanggan->foto  . '"
                kode_cabang = "' . $pelanggan->kode_cabang  . '"
                limitpel = "' . $limitpel  . '"
                jatuhtempo = "' . $pelanggan->jatuhtempo  . '"
                limitpelanggan = "' . rupiah($pelanggan->limitpel)  . '"
                status = "' . $pelanggan->status_pel  . '"
                status_promo = "' . $pelanggan->status_promo . '"


                >Pilih</a>';
            })
            ->toJson();
    }

    public function getpelanggansalesman(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $query = Penjualan::query();
        $query->select('penjualan.kode_pelanggan', 'nama_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        if (!empty($kode_cabang)) {
            $query->where('karyawan.kode_cabang', $kode_cabang);
        }
        if (!empty($id_karyawan)) {
            $query->where('id_sales', $id_karyawan);
        }
        $pelanggan = $query->distinct()->get(['penjualan.kode_pelanggan']);


        //Pelanggan::where('id_sales', $id_karyawan)->where('status_pelanggan', 1)->get();
        echo "<option value=''>Semua Pelanggan</option>";
        foreach ($pelanggan as $d) {
            echo "<option value='$d->kode_pelanggan'>$d->kode_pelanggan" . "  " . "$d->nama_pelanggan</option>";
        }
    }

    public function getpelanggan(Request $request)
    {
        $minutes = 1;
        Cookie::get('kodepelanggan') == null ? Cookie::queue(Cookie::forever('kodepelanggan', $request->kode_pelanggan)) : '';
        $getcookie =  Cookie::get('kodepelanggan');
        //dd($getcookie);
        $kode_pelanggan = Cookie::get('kodepelanggan') != null ? Crypt::decrypt(Cookie::get('kodepelanggan')) : Crypt::decrypt($request->kode_pelanggan);

        $ajuanfaktur = DB::table('pengajuan_faktur')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->orderBy('tgl_pengajuan', 'desc')
            ->first();
        $jmlfaktur = $ajuanfaktur != null ? $ajuanfaktur->jmlfaktur  : 1;
        $sikluspembayaran = $ajuanfaktur != null ? $ajuanfaktur->sikluspembayaran : 0;
        $fakturkredit = DB::table('penjualan')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->where('status_lunas', 2)
            ->where('jenistransaksi', 'kredit')
            ->count();
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $tglskrg = date("d");
        $bulanskrg = date("m");
        $tahunskrg = date("y");
        $hariini = date("Y-m-d");
        $format = $tahunskrg . $bulanskrg . $tglskrg;
        $checkin = DB::table("checkin")
            ->where('tgl_checkin', $hariini)
            ->orderBy("kode_checkin", "desc")
            ->first();
        if ($checkin == null) {
            $lastkode = '';
        } else {
            $lastkode = $checkin->kode_checkin;
        }
        $kode_checkin  = buatkode($lastkode, $format, 4);


        DB::beginTransaction();
        try {
            $ceklat = DB::table('checkin')->where('kode_pelanggan', $kode_pelanggan)->count();

            $cekpelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->first();
            if (empty($cekpelanggan->status_location)) {
                DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->update([
                    'latitude' => $latitude,
                    'longitude' => $longitude

                ]);
            }

            $cek = DB::table('checkin')->where('id_karyawan', Auth::user()->id)->where('kode_pelanggan', $kode_pelanggan)
                ->where('tgl_checkin', $hariini)
                ->count();
            if ($cek == 0 && empty($getcookie)) {
                DB::table('checkin')->insert([
                    'kode_checkin' => $kode_checkin,
                    'tgl_checkin' => $hariini,
                    'id_karyawan' => Auth::user()->id,
                    'kode_pelanggan' => $kode_pelanggan,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);
            } else if ($cek == 0 && !empty($getcookie)) {
                Cookie::queue(Cookie::forget('kodepelanggan'));
                return redirect('/pelanggansalesman');
            }

            DB::commit();

            $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->first();
            $query = Penjualan::query();
            $query->select('penjualan.*', 'nama_pelanggan', 'nama_karyawan', 'karyawan.kode_cabang');
            $query->orderBy('tgltransaksi', 'desc');
            $query->orderBy('no_fak_penj', 'asc');
            $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
            $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->where('penjualan.kode_pelanggan', $kode_pelanggan);

            if (!empty($request->no_fak_penj)) {
                $query->where('no_fak_penj', $request->no_fak_penj);
            }
            if (!empty($request->dari) && !empty($request->sampai)) {
                $query->whereBetween('tgltransaksi', [$request->dari, $request->sampai]);
            }
            $penjualan = $query->paginate(5);
            $penjualan->appends($request->all());


            $limitkredit = DB::table('pengajuan_limitkredit_v3')
                ->select('no_pengajuan', 'tgl_pengajuan', 'jumlah', 'jumlah_rekomendasi', 'jatuhtempo', 'jatuhtempo_rekomendasi', 'skor', 'status', 'kacab', 'mm', 'gm', 'dirut')
                ->where('kode_pelanggan', $kode_pelanggan)
                ->orderBy('tgl_pengajuan', 'asc')
                ->get();

            // $piutang = DB::table('penjualan')
            //     ->select('penjualan.kode_pelanggan', DB::raw('SUM(IFNULL( retur.total, 0 )) AS totalretur,
            //             SUM(IFNULL(penjualan.total,0) - IFNULL(retur.total,0) - IFNULL(jmlbayar,0)) AS sisapiutang'))
            //     ->leftJoin(
            //         DB::raw("(
            //             SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total FROM retur GROUP BY retur.no_fak_penj
            //         ) retur"),
            //         function ($join) {
            //             $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
            //         }
            //     )
            //     ->leftJoin(
            //         DB::raw("(
            //             SELECT no_fak_penj, IFNULL(SUM(bayar),0) as jmlbayar
            //             FROM historibayar
            //             GROUP BY no_fak_penj
            //         ) historibayar"),
            //         function ($join) {
            //             $join->on('penjualan.no_fak_penj', '=', 'historibayar.no_fak_penj');
            //         }
            //     )
            //     ->where('penjualan.kode_pelanggan', $kode_pelanggan)
            //     ->groupBy('penjualan.kode_pelanggan')
            //     ->first();

            $salesmancheckin = DB::table('checkin')
                ->where('tgl_checkin', $hariini)
                ->where('id_karyawan', Auth::user()->id)
                ->count();
            return view('pelanggan.getpelanggan', compact('pelanggan', 'penjualan', 'limitkredit',  'salesmancheckin', 'jmlfaktur', 'fakturkredit', 'sikluspembayaran'));
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }


    public function showpelanggan(Request $request)
    {
        $kode_pelanggan = Crypt::decrypt($request->kode_pelanggan);
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->first();
        $query = Penjualan::query();
        $query->select('penjualan.*', 'nama_pelanggan', 'nama_karyawan', 'karyawan.kode_cabang');
        $query->orderBy('tgltransaksi', 'desc');
        $query->orderBy('no_fak_penj', 'asc');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->where('penjualan.kode_pelanggan', $kode_pelanggan);

        if (!empty($request->no_fak_penj)) {
            $query->where('no_fak_penj', $request->no_fak_penj);
        }
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgltransaksi', [$request->dari, $request->sampai]);
        }
        $penjualan = $query->paginate(5);
        $penjualan->appends($request->all());


        $ajuanfaktur = DB::table('pengajuan_faktur')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->orderBy('tgl_pengajuan', 'desc')
            ->first();
        $jmlfaktur = $ajuanfaktur != null ? $ajuanfaktur->jmlfaktur  : 1;
        $sikluspembayaran = $ajuanfaktur != null ? $ajuanfaktur->sikluspembayaran : 0;
        $fakturkredit = DB::table('penjualan')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->where('status_lunas', 2)
            ->where('jenistransaksi', 'kredit')
            ->count();

        $limitkredit = DB::table('pengajuan_limitkredit_v3')
            ->select('no_pengajuan', 'tgl_pengajuan', 'jumlah', 'jumlah_rekomendasi', 'jatuhtempo', 'jatuhtempo_rekomendasi', 'skor', 'status', 'kacab', 'mm', 'gm', 'dirut')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->orderBy('tgl_pengajuan', 'asc')
            ->get();

        // $piutang = DB::table('penjualan')
        //     ->select('penjualan.kode_pelanggan', DB::raw('SUM(IFNULL( retur.total, 0 )) AS totalretur,
        //                 SUM(IFNULL(penjualan.total,0) - IFNULL(retur.total,0) - IFNULL(jmlbayar,0)) AS sisapiutang'))
        //     ->leftJoin(
        //         DB::raw("(
        //                 SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total FROM retur GROUP BY retur.no_fak_penj
        //             ) retur"),
        //         function ($join) {
        //             $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
        //         }
        //     )
        //     ->leftJoin(
        //         DB::raw("(
        //                 SELECT no_fak_penj, IFNULL(SUM(bayar),0) as jmlbayar
        //                 FROM historibayar
        //                 GROUP BY no_fak_penj
        //             ) historibayar"),
        //         function ($join) {
        //             $join->on('penjualan.no_fak_penj', '=', 'historibayar.no_fak_penj');
        //         }
        //     )
        //     ->where('penjualan.kode_pelanggan', $kode_pelanggan)
        //     ->groupBy('penjualan.kode_pelanggan')
        //     ->first();
        $hariini = date("Y-m-d");
        $salesmancheckin = DB::table('checkin')
            ->where('tgl_checkin', $hariini)
            ->where('id_karyawan', Auth::user()->id)
            ->where('kode_pelanggan', $kode_pelanggan)
            ->count();

        return view('pelanggan.getpelanggan', compact('pelanggan', 'penjualan', 'limitkredit', 'salesmancheckin', 'jmlfaktur', 'sikluspembayaran', 'fakturkredit'));
    }

    public function capturetoko($kode_pelanggan)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->first();
        return view('pelanggan.capture', compact('pelanggan'));
    }

    public function storecapture(Request $request)
    {
        $kode_pelanggan = Crypt::decrypt($request->kode_pelanggan);
        $format = $kode_pelanggan;
        $lokasi = explode(",", $request->latitude);
        $latitude = $lokasi[0];
        $longitude = $lokasi[1];
        $img = $request->image;
        $folderPath = "public/pelanggan/";

        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);
        $fileName =  $format . '.png';

        $file = $folderPath . $fileName;
        $data = [
            'foto' => $fileName,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'status_location' => 1
        ];
        $update = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->update($data);
        if ($update) {
            if (Storage::exists($file)) {
                Storage::delete($file);
            }
            Storage::put($file, $image_base64);
            echo 'success|Data Pelanggan Berhasil Di Update';
        }
    }

    public function checkoutstore($kode_pelanggan)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $hariini = date("Y-m-d");
        $cek = DB::table('checkin')->where('kode_pelanggan', $kode_pelanggan)->where('tgl_checkin', $hariini)->first();
        $kode_checkin = $cek->kode_checkin;
        $checkout_time = date("Y-m-d H:i:s");
        try {
            DB::table('checkin')->where('kode_checkin', $kode_checkin)->update([
                'checkout_time' => $checkout_time
            ]);
            Cookie::queue(Cookie::forget('kodepelanggan'));
            return redirect('/pelanggansalesman');
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function checkinstore(Request $request)
    {

        //$getcookie =  Cookie::get('kodepelanggan');
        $id_karyawan = Auth::user()->id;
        $lokasi = $request->lokasi;
        $lok = explode(",", $lokasi);
        $latitude = $lok[0];
        $longitude = $lok[1];

        // echo $latitude . "," . $longitude;
        // die;
        $kode_pelanggan = $request->kode_pelanggan;
        $hariini = date("Y-m-d");
        $tglskrg = date("d");
        $bulanskrg = date("m");
        $tahunskrg = date("y");
        $hariini = date("Y-m-d");
        $format = $tahunskrg . $bulanskrg . $tglskrg;
        $checkin = DB::table("checkin")
            ->where('tgl_checkin', $hariini)
            ->orderBy("kode_checkin", "desc")
            ->first();
        if ($checkin == null) {
            $lastkode = '';
        } else {
            $lastkode = $checkin->kode_checkin;
        }
        $kode_checkin  = buatkode($lastkode, $format, 4);

        $check = DB::table('checkin')->where('tgl_checkin', $hariini)->where('id_karyawan', $id_karyawan)->where('kode_pelanggan', $kode_pelanggan)->count();
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->first();
        $status_location = $pelanggan->status_location;
        $latitude_pelanggan = $status_location == 1 ? $pelanggan->latitude : $latitude;
        $longitude_pelanggan = $status_location == 1 ? $pelanggan->longitude : $longitude;
        // echo $latitude . "," . $longitude;
        // echo "<br>";
        // echo $latitude_pelanggan . "," . $longitude_pelanggan;
        // die;
        $jarak = $this->distance($latitude_pelanggan, $longitude_pelanggan, $latitude, $longitude);
        $radius =  ROUND($jarak["meters"]);
        DB::beginTransaction();
        try {

            // if ($radius > 20) {
            //     echo 'Jarak Anda dengan toko Saat Ini adalah ' . $radius . "Meter Minimal Jarak Untuk Checkin Adalah Maksimal 20 Meter";
            // } else {
            //     if ($status_location == NULL) {
            //         DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->update([
            //             'latitude' => $latitude,
            //             'longitude' => $longitude,
            //             'status_location' => 1
            //         ]);
            //     }

            //     if ($check == 0) {
            //         $data = [
            //             'kode_checkin' => $kode_checkin,
            //             'tgl_checkin' => $hariini,
            //             'id_karyawan' => $id_karyawan,
            //             'kode_pelanggan' => $kode_pelanggan,
            //             'latitude' => $latitude,
            //             'longitude' => $longitude,
            //             'jarak' => $radius
            //         ];

            //         $simpan = DB::table('checkin')->insert($data);
            //         DB::commit();
            //         echo 'success|Terimakasih Telah Melakukan Checkin';
            //     } else {
            //         echo 'success|Terimakasih Telah Melakukan Checkin';
            //     }
            //     Cookie::queue(Cookie::forever('kodepelanggan', Crypt::encrypt($request->kode_pelanggan)));
            // }
            if ($status_location == NULL) {
                DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->update([
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'status_location' => 1
                ]);
            }

            if ($check == 0) {
                $data = [
                    'kode_checkin' => $kode_checkin,
                    'tgl_checkin' => $hariini,
                    'id_karyawan' => $id_karyawan,
                    'kode_pelanggan' => $kode_pelanggan,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'jarak' => $radius
                ];

                $simpan = DB::table('checkin')->insert($data);
                DB::commit();
                echo 'success|Terimakasih Telah Melakukan Checkin';
            } else {
                echo 'success|Terimakasih Telah Melakukan Checkin';
            }
            Cookie::queue(Cookie::forever('kodepelanggan', Crypt::encrypt($request->kode_pelanggan)));
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }

    public function deletecookie(Request $request)
    {
        $kode_pelanggan = $request->kode_pelanggan;
        Cookie::queue(Cookie::forget('kodepelanggan'));
        return redirect('/pelanggan/showpelanggan?kode_pelanggan=' . $kode_pelanggan);
    }

    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function shownonaktif()
    {
        $query = Pelanggan::query();

        $query->selectRaw('pelanggan.kode_pelanggan,nama_pelanggan,lasttransaksi,datediff(CURDATE(), lasttransaksi) as lama');
        $query->leftJoin(
            DB::raw("(
                    SELECT penjualan.kode_pelanggan, MAX(tgltransaksi) as lasttransaksi
                    FROM penjualan
                    GROUP BY penjualan.kode_pelanggan
                ) penjualan"),
            function ($join) {
                $join->on('pelanggan.kode_pelanggan', '=', 'penjualan.kode_pelanggan');
            }
        );
        $query->whereRaw('datediff(CURDATE(), lasttransaksi) > 90');
        $query->where('status_pelanggan', 1);
        if (Auth::user()->kode_cabang != "PCF") {
            $query->where('pelanggan.kode_cabang', Auth::user()->kode_cabang);
        }
        $pelanggan = $query->get();
        return view('pelanggan.shownonaktif', compact('pelanggan'));
    }

    public function updatenonaktif()
    {
        //dd('test');
        $kode_cabang = Auth::user()->kode_cabang;
        DB::beginTransaction();
        try {
            $update = DB::table('pelanggan')
                ->leftJoin(
                    DB::raw("(
                        SELECT penjualan.kode_pelanggan, MAX(tgltransaksi) as lasttransaksi
                        FROM penjualan
                        GROUP BY penjualan.kode_pelanggan
                    ) penjualan"),
                    function ($join) {
                        $join->on('pelanggan.kode_pelanggan', '=', 'penjualan.kode_pelanggan');
                    }
                )
                ->whereRaw('datediff(CURDATE(), lasttransaksi) > 90')
                ->where('status_pelanggan', 1)
                ->where('pelanggan.kode_cabang', $kode_cabang)
                ->update([
                    'status_pelanggan' => 0
                ]);
            DB::commit();
            dd($update);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }


    public function getautocompletepelanggan(Request $request)
    {
        $search = $request->search;
        if ($search == '') {
            $query = Pelanggan::query();
            $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
            if ($this->cabang != "PCF") {
                $query->where('pelanggan.kode_cabang', $this->cabang);
            }
            $query->orderBy('nama_pelanggan', 'asc');
            $query->limit(10);
            $autocomplate = $query->get();
        } else {
            $query = Pelanggan::query();
            $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
            if ($this->cabang != "PCF") {
                $query->where('kode_pelanggan', 'like', '%' . $search . '%');
                $query->where('pelanggan.kode_cabang', $this->cabang);
                $query->orWhere('nama_pelanggan', 'like', '%' . $search . '%');
                $query->where('pelanggan.kode_cabang', $this->cabang);
            } else {
                $query->where('kode_pelanggan', 'like', '%' . $search . '%');
                $query->orWhere('nama_pelanggan', 'like', '%' . $search . '%');
            }
            $query->orderBy('nama_pelanggan', 'asc');
            $query->limit(10);
            $autocomplate = $query->get();
        }


        //dd($autocomplate);
        $response = array();
        foreach ($autocomplate as $autocomplate) {
            $label = $autocomplate->kode_pelanggan . " - " . $autocomplate->nama_pelanggan . " - " . $autocomplate->kode_cabang . " - " . $autocomplate->nama_karyawan;
            $response[] = array("value" => $autocomplate->nama_pelanggan, "label" => $label, 'val' => $autocomplate->kode_pelanggan);
        }

        echo json_encode($response);
        exit;
    }
}
