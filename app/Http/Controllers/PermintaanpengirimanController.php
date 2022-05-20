<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cabang;
use App\Models\Permintaanpengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class PermintaanpengirimanController extends Controller
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
        $query = Permintaanpengiriman::query();
        $query->select(
            'permintaan_pengiriman.no_permintaan_pengiriman',
            'tgl_permintaan_pengiriman',
            'permintaan_pengiriman.kode_cabang',
            'permintaan_pengiriman.keterangan',
            'status',
            'nama_karyawan',
            'no_mutasi_gudang',
            'no_dok',
            'tgl_mutasi_gudang',
            'status_sj'
        );
        if (!empty($request->tanggal)) {
            $query->where('tgl_permintaan_pengiriman', $request->tanggal);
        }

        if (!empty($request->status) || $request->status === '0') {
            $query->where('status', $request->status);
        }

        if (!empty($request->cabang)) {
            $query->where('permintaan_pengiriman.kode_cabang', $request->cabang);
        } else {
            if ($this->cabang != "PCF") {
                $query->where('permintaan_pengiriman.kode_cabang', $this->cabang);
            }
        }
        $query->leftJoin('karyawan', 'permintaan_pengiriman.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->leftJoin('mutasi_gudang_jadi', 'permintaan_pengiriman.no_permintaan_pengiriman', '=', 'mutasi_gudang_jadi.no_permintaan_pengiriman');
        $query->orderBy('status', 'asc');
        $query->orderBy('tgl_permintaan_pengiriman', 'desc');
        $query->orderBy('permintaan_pengiriman.no_permintaan_pengiriman', 'desc');
        $pp = $query->paginate(15);
        $pp->appends($request->all());

        if ($this->cabang != "PCF") {
            $cabang = Cabang::where('kode_cabang', $this->cabang)->get();
        } else {
            $cabang = Cabang::all();
        }
        $produk = Barang::orderBy('kode_produk')->get();
        return view('permintaanpengiriman.index', compact('pp', 'cabang', 'produk'));
    }

    public function cektemp()
    {
        $cektemp = DB::table('detail_permintaan_pengiriman_temp')->count();
        if (empty($cektemp)) {
            echo 0;
        } else {
            echo $cektemp;
        }
    }

    public function storetemp(Request $request)
    {
        $kode_produk = $request->kode_produk;
        $jumlah = str_replace(".", "", $request->jumlah);
        $cek = DB::table('detail_permintaan_pengiriman_temp')->where('kode_produk', $kode_produk)->count();
        if ($cek > 0) {
            echo 1;
        } else {
            DB::table('detail_permintaan_pengiriman_temp')->insert([
                'kode_produk' => $kode_produk,
                'jumlah' => $jumlah
            ]);

            echo 0;
        }
    }

    public function showtemp()
    {
        $detailtemp = DB::table('detail_permintaan_pengiriman_temp')
            ->select('detail_permintaan_pengiriman_temp.*', 'nama_barang')
            ->join('master_barang', 'detail_permintaan_pengiriman_temp.kode_produk', '=', 'master_barang.kode_produk')
            ->get();
        return view('permintaanpengiriman.showtemp', compact('detailtemp'));
    }

    public function deletetemp(Request $request)
    {
        $hapus = DB::table('detail_permintaan_pengiriman_temp')->where('kode_produk', $request->kode_produk)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function store(Request $request)
    {
        $no_permintaan_pengiriman = $request->no_permintaan_pengiriman;
        $tgl_permintaan_pengiriman = $request->tgl_permintaan_pengiriman;
        $kode_cabang = $request->kode_cabang;
        $keterangan = $request->keterangan;
        if ($kode_cabang == "TSM") {
            $id_karyawan = $request->id_karyawan;
        } else {
            $id_karyawan = NULL;
        }
        $id_admin = Auth::user()->id;
        $data = array(
            'no_permintaan_pengiriman'     => $no_permintaan_pengiriman,
            'tgl_permintaan_pengiriman' => $tgl_permintaan_pengiriman,
            'kode_cabang' => $kode_cabang,
            'keterangan' => $keterangan,
            'status' => 0,
            'id_admin' => $id_admin,
            'id_karyawan' => $id_karyawan
        );
        DB::beginTransaction();
        try {
            DB::table('permintaan_pengiriman')->insert($data);
            $detail = DB::table('detail_permintaan_pengiriman_temp')->get();
            foreach ($detail as $d) {
                $data_detail = array(
                    'no_permintaan_pengiriman' => $no_permintaan_pengiriman,
                    'kode_produk' => $d->kode_produk,
                    'jumlah' => $d->jumlah
                );

                DB::table('detail_permintaan_pengiriman')->insert($data_detail);
            }

            DB::table('detail_permintaan_pengiriman_temp')->delete();
            DB::commit();
            return redirect('/permintaanpengiriman')->with(['success' => 'Data Berhasil di Simpan']);
        } catch (\Exception $e) {
            ///dd($e);
            DB::rollback();
            return redirect('/permintaanpengiriman')->with(['warning' => 'Data Gagal di Simpan Hubungi Tim IT']);
        }
    }

    public function buatnopermintaan(Request $request)
    {
        $tgl_permintaan_pengiriman = $request->tgl_permintaan_pengiriman;
        $kode_cabang = $request->kode_cabang;
        $kode = strlen($kode_cabang);
        $no_permintaan  = $kode + 4;
        $pp = DB::table('permintaan_pengiriman')
            ->selectRaw('LEFT(no_permintaan_pengiriman,' . $no_permintaan . ') as no_permintaan_pengiriman')
            ->whereRaw('MID(no_permintaan_pengiriman,3,' . $kode . ')="' . $kode_cabang . '"')
            ->where('tgl_permintaan_pengiriman', $tgl_permintaan_pengiriman)
            ->orderByRaw('LEFT(no_permintaan_pengiriman,' . $no_permintaan . ') DESC')
            ->first();
        $tanggal = explode("-", $tgl_permintaan_pengiriman);
        $hari  = $tanggal[2];
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $tgl   = "." . $hari . "." . $bulan . "." . $tahun;
        if ($pp != null) {
            $lastnopermintaan = $pp->no_permintaan_pengiriman;
        } else {
            $lastnopermintaan = "";
        }
        $no_permintaan_pengiriman = buatkode($lastnopermintaan, "OR" . $kode_cabang, 2) . $tgl;

        echo $no_permintaan_pengiriman;
    }

    public function delete($no_permintaan_pengiriman)
    {
        $no_permintaan_pengiriman = Crypt::decrypt($no_permintaan_pengiriman);
        $hapus = DB::table('permintaan_pengiriman')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function show($no_permintaan_pengiriman)
    {
        $no_permintaan_pengiriman = Crypt::decrypt($no_permintaan_pengiriman);
        $pp = DB::table('permintaan_pengiriman')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->first();
        $sj = DB::table('mutasi_gudang_jadi')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->first();
        $detail = DB::table('detail_permintaan_pengiriman')
            ->join('master_barang', 'detail_permintaan_pengiriman.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->orderBy('detail_permintaan_pengiriman.kode_produk')->get();
        if ($sj != null) {
            $detailsj = DB::table('detail_mutasi_gudang')
                ->join('master_barang', 'detail_mutasi_gudang.kode_produk', '=', 'master_barang.kode_produk')
                ->where('no_mutasi_gudang', $sj->no_mutasi_gudang)->orderBy('detail_mutasi_gudang.kode_produk')->get();
        } else {
            $detailsj = null;
        }
        return view('permintaanpengiriman.show', compact('pp', 'detail', 'sj', 'detailsj'));
    }

    public function updatedetail(Request $request)
    {
        $no_permintaan_pengiriman = $request->no_permintaan_pengiriman;
        $kode_produk = $request->kode_produk;
        $jumlah = str_replace(".", "", $request->jumlah);
        $data = [
            'jumlah' => $jumlah
        ];
        DB::table('detail_permintaan_pengiriman')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->where('kode_produk', $kode_produk)->update($data);
    }
}