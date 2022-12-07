<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Salesman;
use Facade\FlareClient\Stacktrace\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $cabang = Cabang::all();
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
            'no_hp' => 'required|numeric',
            'pasar' => 'required',
            'hari' => 'required',
            'kode_cabang' => 'required',
            'id_karyawan' => 'required',
            'status_pelanggan' => 'required',
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

        $simpan = DB::table('pelanggan')->insert([
            'kode_pelanggan' => $kodepelanggan,
            'nik' => $request->nik,
            'no_kk' => $request->no_kk,
            'nama_pelanggan' => $request->nama_pelanggan,
            'tgl_lahir' => $request->tgl_lahir,
            'alamat_pelanggan' => $request->alamat_pelanggan,
            'alamat_toko' => $request->alamat_toko,
            'no_hp' => $request->no_hp,
            'hari' => $request->hari,
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
            'foto' => $foto
        ]);

        if ($simpan) {
            return redirect('/pelanggan')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/pelanggan')->with(['warning' => 'Data Gagal Disimpan']);
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
        $file = $pelanggan->foto;
        $request->validate([
            'nama_pelanggan' => 'required',
            'alamat_pelanggan' => 'required',
            'alamat_toko' => 'required',
            'no_hp' => 'required|numeric',
            'pasar' => 'required',
            'hari' => 'required',
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
                'hari' => $request->hari,
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
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
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
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->join('cabang', 'pelanggan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        return view('pelanggan.show', compact('data', 'penjualan', 'limitkredit'));
    }

    public function json()
    {

        $query = Pelanggan::query();
        $query->select('pelanggan.*', 'karyawan.nama_karyawan', 'karyawan.kategori_salesman', 'limitpel');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        $query->where('status_pelanggan', '1');
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
                limitpel = "' . $pelanggan->limitpel  . '"
                jatuhtempo = "' . $pelanggan->jatuhtempo  . '"
                limitpelanggan = "' . rupiah($pelanggan->limitpel)  . '"
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

    public function getpelanggan($kode_pelanggan, Request $request)
    {
        dd($kode_pelanggan);
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
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
            if ($ceklat == 0) {
                DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->update([
                    'latitude' => $latitude,
                    'longitude' => $longitude

                ]);
            }
            $cek = DB::table('checkin')->where('id_karyawan', Auth::user()->id)->where('kode_pelanggan', $kode_pelanggan)
                ->where('tgl_checkin', $hariini)
                ->count();
            if ($cek == 0) {
                DB::table('checkin')->insert([
                    'kode_checkin' => $kode_checkin,
                    'tgl_checkin' => $hariini,
                    'id_karyawan' => Auth::user()->id,
                    'kode_pelanggan' => $kode_pelanggan,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);
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
            $penjualan = $query->paginate(10);
            $penjualan->appends($request->all());


            $limitkredit = DB::table('pengajuan_limitkredit_v3')
                ->select('no_pengajuan', 'tgl_pengajuan', 'jumlah', 'jumlah_rekomendasi', 'jatuhtempo', 'jatuhtempo_rekomendasi', 'skor', 'status', 'kacab', 'mm', 'gm', 'dirut')
                ->where('kode_pelanggan', $kode_pelanggan)
                ->orderBy('tgl_pengajuan', 'asc')
                ->get();

            $piutang = DB::table('penjualan')
                ->select('penjualan.kode_pelanggan', DB::raw('SUM(IFNULL( retur.total, 0 )) AS totalretur,
                        SUM(IFNULL(penjualan.total,0) - IFNULL(retur.total,0) - IFNULL(jmlbayar,0)) AS sisapiutang'))
                ->leftJoin(
                    DB::raw("(
                        SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total FROM retur GROUP BY retur.no_fak_penj
                    ) retur"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                        SELECT no_fak_penj, IFNULL(SUM(bayar),0) as jmlbayar
                        FROM historibayar
                        GROUP BY no_fak_penj
                    ) historibayar"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'historibayar.no_fak_penj');
                    }
                )
                ->where('penjualan.kode_pelanggan', $kode_pelanggan)
                ->groupBy('penjualan.kode_pelanggan')
                ->first();
            return view('pelanggan.getpelanggan', compact('pelanggan', 'penjualan', 'limitkredit', 'piutang'));
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e;
        }
    }
}
