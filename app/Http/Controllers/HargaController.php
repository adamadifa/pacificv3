<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cabang;
use App\Models\Harga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use PDOException;

class HargaController extends Controller
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

    function index(Request $request)
    {
        $query = Harga::query();
        if ($this->cabang != "PCF") {
            $query->where('barang.kode_cabang', $this->cabang);
        }
        if (isset($request->submit)) {
            if (!empty($request->kode_cabang)) {
                $query->where('kode_cabang', $request->kode_cabang);
            }

            if (!empty($request->kategori_harga)) {
                $query->where('kategori_harga', $request->kategori_harga);
            }
        }
        $harga = $query->paginate(15);
        $harga->appends($request->all());
        $cabang = Cabang::all();
        return view('harga.index', compact('harga', 'cabang'));
    }

    public function create()
    {
        $barang = Barang::all();
        $cabang = Cabang::all();
        return view('harga.create', compact('barang', 'cabang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required',
            'kode_produk' => 'required',
            'kategori' => 'required',
            'satuan' => 'required',
            'isipcsdus' => 'required|numeric',
            'isipack' => 'required|numeric',
            'isipcs' => 'required|numeric',
            'kategori_harga' => 'required',
            'kode_cabang' => 'required',
            'harga_dus' => 'required',
            'harga_pack' => 'required',
            'harga_pcs' => 'required',
            'harga_returdus' => 'required',
            'harga_returpack' => 'required',
            'harga_returpcs' => 'required',
        ]);

        $produk = explode("|", $request->kode_produk);
        $simpan = DB::table('barang')->insert([
            'kode_barang' => $request->kode_barang,
            'kode_produk' => $produk[0],
            'nama_barang' => $produk[1],
            'kategori' => $request->kategori,
            'satuan' => $request->satuan,
            'isipcsdus' => $request->isipcsdus,
            'isipack' => $request->isipack,
            'isipcs' => $request->isipcs,
            'kategori_harga' => $request->kategori_harga,
            'kode_cabang' => $request->kode_cabang,
            'harga_dus' => str_replace(".", "", $request->harga_dus),
            'harga_pack' => str_replace(".", "", $request->harga_pack),
            'harga_pcs' => str_replace(".", "", $request->harga_pcs),
            'harga_returdus' => str_replace(".", "", $request->harga_returdus),
            'harga_returpack' => str_replace(".", "", $request->harga_returpack),
            'harga_returpcs' => str_replace(".", "", $request->harga_returpcs),
        ]);

        if ($simpan) {
            return redirect('/harga')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/harga')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function edit($kode_barang)
    {
        $kode_barang = Crypt::decrypt($kode_barang);
        $data = DB::table('barang')->where('kode_barang', $kode_barang)->first();
        $barang = Barang::all();
        $cabang = Cabang::all();
        return view('harga.edit', compact('barang', 'cabang', 'data'));
    }


    public function update(Request $request, $kode_barang)
    {
        $kode_barang = Crypt::decrypt($kode_barang);
        $request->validate([
            'kode_barang' => 'required',
            'satuan' => 'required',
            'isipcsdus' => 'required|numeric',
            'isipack' => 'required|numeric',
            'isipcs' => 'required|numeric',
            'kategori_harga' => 'required',
            'kode_cabang' => 'required',
            'harga_dus' => 'required',
            'harga_pack' => 'required',
            'harga_pcs' => 'required',
            'harga_returdus' => 'required',
            'harga_returpack' => 'required',
            'harga_returpcs' => 'required',
        ]);

        $produk = explode("|", $request->kode_produk);
        $simpan = DB::table('barang')
            ->where('kode_barang', $kode_barang)
            ->update([
                'kode_barang' => $request->kode_barang,
                'satuan' => $request->satuan,
                'isipcsdus' => $request->isipcsdus,
                'isipack' => $request->isipack,
                'isipcs' => $request->isipcs,
                'kategori_harga' => $request->kategori_harga,
                'kode_cabang' => $request->kode_cabang,
                'harga_dus' => str_replace(".", "", $request->harga_dus),
                'harga_pack' => str_replace(".", "", $request->harga_pack),
                'harga_pcs' => str_replace(".", "", $request->harga_pcs),
                'harga_returdus' => str_replace(".", "", $request->harga_returdus),
                'harga_returpack' => str_replace(".", "", $request->harga_returpack),
                'harga_returpcs' => str_replace(".", "", $request->harga_returpcs),
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Updat']);
        }
    }

    public function delete($kode_barang)
    {
        $kode_barang = Crypt::decrypt($kode_barang);
        try {
            $hapus = DB::table('barang')
                ->where('kode_barang', $kode_barang)
                ->delete();

            if ($hapus) {
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

    public function show(Request $request)
    {
        $kode_barang = Crypt::decrypt($request->kode_barang);
        $data = DB::table('barang')->where('kode_barang', $kode_barang)->first();
        return view('harga.show', compact('data'));
    }

    //Autocomplete
    public function getautocompleteharga(Request $request)
    {
        $search = $request->search;
        $kode_cabang = $request->kode_cabang;
        if (!empty($request->kategori_salesman)) {
            $kategori_salesman = $request->kategori_salesman;
        } else {
            $kategori_salesman = "NORMAL";
        }
        if ($search == '') {
            $autocomplate = Harga::orderby('nama_barang', 'asc')->select('kode_produk', 'kode_barang', 'nama_barang', 'harga_dus', 'kategori_harga')
                ->where('kode_cabang', $kode_cabang)
                ->where('kategori_harga', $kategori_salesman)
                ->limit(5)->get();
        } else {
            $autocomplate = Harga::orderby('nama_barang', 'asc')->select('kode_produk', 'kode_barang', 'nama_barang', 'harga_dus', 'kategori_harga')->where('nama_barang', 'like', '%' . $search . '%')
                ->where('kode_cabang', $kode_cabang)
                ->where('kategori_harga', $kategori_salesman)
                ->orWhere('kode_produk', 'like', '%' . $search . '%')
                ->where('kode_cabang', $kode_cabang)
                ->where('kategori_harga', $kategori_salesman)
                ->limit(5)->get();
        }


        //dd($autocomplate);
        $response = array();
        foreach ($autocomplate as $autocomplate) {
            $label = $autocomplate->kode_produk . " - " . $autocomplate->nama_barang . " - " . rupiah($autocomplate->harga_dus) . " - " . $autocomplate->kategori_harga;
            $response[] = array("value" => $autocomplate->nama_barang, "label" => $label, 'val' => $autocomplate->kode_barang);
        }

        echo json_encode($response);
        exit;
    }


    public function getautocompletehargaretur(Request $request)
    {
        $search = $request->search;
        $kode_cabang = $request->kode_cabang;
        if (!empty($request->kategori_salesman)) {
            $kategori_salesman = $request->kategori_salesman;
        } else {
            $kategori_salesman = "NORMAL";
        }
        if ($search == '') {
            $autocomplate = Harga::orderby('nama_barang', 'asc')->select('kode_produk', 'kode_barang', 'nama_barang', 'harga_returdus', 'kategori_harga')
                ->where('kode_cabang', $kode_cabang)
                ->where('kategori_harga', $kategori_salesman)
                ->limit(5)->get();
        } else {
            $autocomplate = Harga::orderby('nama_barang', 'asc')->select('kode_produk', 'kode_barang', 'nama_barang', 'harga_returdus', 'kategori_harga')->where('nama_barang', 'like', '%' . $search . '%')
                ->where('kode_cabang', $kode_cabang)
                ->where('kategori_harga', $kategori_salesman)
                ->orWhere('kode_produk', 'like', '%' . $search . '%')
                ->where('kode_cabang', $kode_cabang)
                ->where('kategori_harga', $kategori_salesman)
                ->limit(5)->get();
        }


        //dd($autocomplate);
        $response = array();
        foreach ($autocomplate as $autocomplate) {
            $label = $autocomplate->kode_produk . " - " . $autocomplate->nama_barang . " - " . rupiah($autocomplate->harga_returdus) . " - " . $autocomplate->kategori_harga;
            $response[] = array("value" => $autocomplate->nama_barang, "label" => $label, 'val' => $autocomplate->kode_barang);
        }

        echo json_encode($response);
        exit;
    }

    public function gethargabarang(Request $request)
    {
        $barang = DB::table('barang')->where('kode_barang', $request->kode_barang)->first();
        $harga_dus = $barang->harga_dus;
        $harga_pack = $barang->harga_pack;
        $harga_pcs = $barang->harga_pcs;
        echo rupiah($harga_dus) . "|" . rupiah($harga_pack) . "|" . rupiah($harga_pcs);
    }
}