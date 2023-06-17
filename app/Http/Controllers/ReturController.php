<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Harga;
use App\Models\Retur;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use PDOException;

class ReturController extends Controller
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
        $pelanggan = '"' . $request->nama_pelanggan . '"';
        $query = Retur::query();
        $query->select('retur.*', 'nama_pelanggan', 'nama_karyawan', 'karyawan.kode_cabang');
        $query->orderBy('tglretur', 'desc');
        $query->orderBy('no_retur_penj', 'asc');
        $query->join('penjualan', 'retur.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        if (empty($request->no_fak_penj) && empty($request->nama_pelanggan) && empty($request->dari) && empty($request->sampai)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }
        if (!empty($request->nama_pelanggan)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }

        if (!empty($request->no_fak_penj)) {
            $query->where('retur.no_fak_penj', $request->no_fak_penj);
        }

        if (!empty($request->jenis_retur)) {
            $query->where('jenis_retur', $request->jenis_retur);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tglretur', [$request->dari, $request->sampai]);
        }
        // if ($this->cabang != "PCF") {
        //     if ($this->cabang == "GRT") {
        //         $query->where('karyawan.kode_cabang', 'TSM');
        //     } else {
        //         $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        //         $cabang[] = "";
        //         foreach ($cbg as $c) {
        //             $cabang[] = $c->kode_cabang;
        //         }
        //         $query->whereIn('karyawan.kode_cabang', $cabang);
        //     }
        // }

        if ($this->cabang != "PCF") {
            $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
            $cabang[] = "";
            foreach ($cbg as $c) {
                $cabang[] = $c->kode_cabang;
            }
            $query->whereIn('karyawan.kode_cabang', $cabang);
        }

        if (Auth::user()->level == "salesman") {
            $query->where('penjualan.id_karyawan', Auth::user()->id_salesman);
        }
        $retur = $query->paginate(15);

        $retur->appends($request->all());

        if (Auth::user()->level == "salesman") {
            return view('retur.indexsalesman', compact('retur'));
        } else {
            return view('retur.index', compact('retur'));
        }
    }

    public function create()
    {
        return view('retur.create');
    }

    public function createv2()
    {
        $kodepelanggan = Cookie::get('kodepelanggan');
        $kode_pelanggan = $kodepelanggan != null ? Crypt::decrypt($kodepelanggan) : '';
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->first();
        if (Auth::user()->level == "salesman") {

            return view('retur.createv2', compact('pelanggan'));
        } else {
            return view('retur.createv3', compact('pelanggan'));
        }
    }

    public function showbarangtemp(Request $request)
    {
        $kode_pelanggan = $request->kode_pelanggan;
        $barang = DB::table('detailretur_temp')
            ->select('detailretur_temp.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('barang', 'detailretur_temp.kode_barang', '=', 'barang.kode_barang')
            ->where('detailretur_temp.kode_pelanggan', $kode_pelanggan)
            ->get();
        return view('retur.showbarangtemp', compact('barang'));
    }

    public function showbarangtempv2(Request $request)
    {
        $kode_pelanggan = $request->kode_pelanggan;
        $detailtemp = DB::table('detailretur_temp')
            ->select('detailretur_temp.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('barang', 'detailretur_temp.kode_barang', '=', 'barang.kode_barang')
            ->where('detailretur_temp.kode_pelanggan', $kode_pelanggan)
            ->get();

        if (Auth::user()->level == "salesman") {
            return view('retur.showbarangtempv3', compact('detailtemp'));
        } else {
            return view('retur.showbarangtempv2', compact('detailtemp'));
        }
    }

    public function storebarangtemp(Request $request)
    {
        $id_user = Auth::user()->id;
        $barang = Harga::where('kode_barang', $request->kode_barang)->first();
        $kode_pelanggan = $request->kode_pelanggan;
        $cek = DB::table('detailretur_temp')->where('kode_barang', $request->kode_barang)->where('kode_pelanggan', $kode_pelanggan)->count();
        if (empty($cek)) {
            $simpan = DB::table('detailretur_temp')
                ->insert([
                    'kode_barang' => $request->kode_barang,
                    'jumlah' => 0,
                    'harga_dus' => $barang->harga_returdus,
                    'harga_pack' => $barang->harga_returpack,
                    'harga_pcs' => $barang->harga_returpcs,
                    'subtotal' => 0,
                    'kode_pelanggan' => $kode_pelanggan,
                    'id_admin' => $id_user
                ]);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        } else {
            echo 1;
        }
    }



    public function storebarangtempv2(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $harga_dus = $request->hargadus;
        $harga_pack = $request->hargapack;
        $harga_pcs = $request->hargapcs;
        $jumlah = $request->jumlah;
        $subtotal = $request->subtotal;
        $id_admin = Auth::user()->id;
        $kode_pelanggan = $request->kode_pelanggan;
        $data = [
            'kode_barang' => $kode_barang,
            'jumlah' => $jumlah,
            'harga_dus' => $harga_dus,
            'harga_pack' => $harga_pack,
            'harga_pcs' => $harga_pcs,
            'subtotal' => $subtotal,
            'id_admin' => $id_admin,
            'kode_pelanggan' => $kode_pelanggan
        ];


        $cek = DB::table('detailretur_temp')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->where('kode_pelanggan', $kode_pelanggan)->count();
        if ($cek > 0) {
            echo 1;
        } else {
            try {
                DB::table('detailretur_temp')->insert($data);
                echo 0;
            } catch (Exception $e) {
                echo $e;
            }
        }
    }

    public function cekreturtemp(Request $request)
    {
        $kode_pelanggan = $request->kode_pelanggan;
        $barang = DB::table('detailretur_temp')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->count();
        echo $barang;
    }


    public function deletebarangtemp(Request $request)
    {
        $kode_pelanggan = $request->kode_pelanggan;
        $hapus = DB::table('detailretur_temp')
            ->where('kode_barang', $request->kode_barang)
            ->where('kode_pelanggan', $kode_pelanggan)
            ->delete();
        if ($hapus) {
            echo 1;
        } else {
            echo 2;
        }
    }

    public function updatedetailtemp(Request $request)
    {
        $kode_pelanggan = $request->kode_pelanggan;
        $barang = DB::table('barang')->where('kode_barang', $request->kode_barang)->first();
        $detailtemp = DB::table('detailretur_temp')->where('kode_barang', $request->kode_barang)->where('kode_pelanggan', $kode_pelanggan)->first();
        $jmldus = $request->jmldus * $barang->isipcsdus;
        $jmlpack = $request->jmlpack * $barang->isipcs;
        $jmlpcs = $request->jmlpcs;



        //$cekpromo = DB::table('detailpenjualan_temp')->where('kode_barang', $request->kode_barang)->where('id_admin', $id_user)->where('promo', $promo)->count();
        $harga_dus = str_replace(".", "", $request->harga_dus);
        $harga_pack = str_replace(".", "", $request->harga_pack);
        $harga_pcs = str_replace(".", "", $request->harga_pcs);
        $totalqty = $jmldus + $jmlpack + $jmlpcs;
        $total = $request->total;

        DB::table('detailretur_temp')
            ->where('kode_barang', $request->kode_barang)
            ->where('kode_pelanggan', $kode_pelanggan)
            ->update([
                'jumlah' => $totalqty,
                'harga_dus' => $harga_dus,
                'harga_pack' => $harga_pack,
                'harga_pcs' => $harga_pcs,
                'subtotal' => $total,
            ]);
    }

    public function loadtotalreturtemp(Request $request)
    {
        $detail = DB::table('detailretur_temp')
            ->select(DB::raw('SUM(subtotal) AS total'))
            ->where('kode_pelanggan', $request->kode_pelanggan)
            ->first();
        echo rupiah($detail->total);
    }

    public function getfakturpelanggan(Request $request)
    {
        $faktur = DB::table('penjualan')
            ->select('no_fak_penj')
            ->where('kode_pelanggan', $request->kode_pelanggan)
            ->orderBy('tgltransaksi', 'desc')
            ->get();
        echo "<option value=''>Pilih No. Faktur</option>";
        foreach ($faktur as $d) {
            echo "<option value='$d->no_fak_penj'>$d->no_fak_penj</option>";
        }
    }

    public function store(Request $request)
    {
        $kode_pelanggan = $request->kode_pelanggan;
        $no_fak_penj = $request->no_fak_penj;
        $tglretur = $request->tglretur;
        $jenis_retur = $request->jenis_retur;
        $kode_cabang = $request->kode_cabang;
        $no_ref = $request->no_retur_penj;
        $subtotal = $request->subtotal;
        $id_admin = Auth::user()->id;
        $tanggalr = explode("-", $tglretur);
        $tahunr = $tanggalr[0];
        $bulanr = $tanggalr[1];
        $harir = $tanggalr[2];
        $thnr = substr($tahunr, 2, 2);
        $tanggalretur   = $thnr . $bulanr . $harir;
        $lastretur = DB::table('retur')
            ->select('no_retur_penj')
            ->where('tglretur', $tglretur)
            ->whereRaw('LEFT(no_retur_penj,1) = "R"')
            ->orderBy('no_retur_penj', 'desc')
            ->first();

        if ($lastretur == null) {
            $last_no_retur_penj = "R" . $tanggalretur . "000";
        } else {
            $last_no_retur_penj = $lastretur->no_retur_penj;
        }
        $no_retur_penj = buatkode($last_no_retur_penj, 'R' . $tanggalretur, 3);


        DB::beginTransaction();
        try {
            if ($jenis_retur == "pf") {
                $subtotal_pf = $request->subtotal;
                $subtotal_gb = 0;
                $total = $subtotal_pf + $subtotal_gb;
                $cekfaktur = DB::table('penjualan')->where('no_fak_penj', $no_fak_penj)->first();
                if ($cekfaktur->jenistransaksi == 'tunai') {
                    $historibayar = DB::table('historibayar')
                        ->where('no_fak_penj', $no_fak_penj)
                        ->where('tglbayar', $cekfaktur->tgltransaksi)
                        ->first();


                    if ($historibayar != null) {
                        DB::table('historibayar')
                            ->where('no_fak_penj', $no_fak_penj)
                            ->wherenull('status_bayar')
                            ->where('tglbayar', $cekfaktur->tgltransaksi)
                            ->update([
                                'bayar' =>  DB::raw('bayar -' . $total)
                            ]);

                        DB::table('buku_besar')
                            ->where('no_ref', $historibayar->nobukti)
                            ->update([
                                'debet' =>  DB::raw('debet -' . $total)
                            ]);
                    }
                }
            } else {
                $subtotal_pf = $request->subtotal;
                $subtotal_gb = $request->subtotal;
                $total = $subtotal_pf - $subtotal_gb;
            }

            DB::table('retur')
                ->insert([
                    'no_retur_penj' => $no_retur_penj,
                    'no_ref' => $no_ref,
                    'no_fak_penj' => $no_fak_penj,
                    'tglretur' => $tglretur,
                    'subtotal_gb' => $subtotal_gb,
                    'subtotal_pf' => $subtotal_pf,
                    'total' => $total,
                    'jenis_retur' => $jenis_retur,
                    'id_admin' => $id_admin
                ]);

            $detail = DB::table('detailretur_temp')->where('kode_pelanggan', $kode_pelanggan)->get();
            foreach ($detail as $t) {
                DB::table('detailretur')
                    ->insert([
                        'no_retur_penj' => $no_retur_penj,
                        'no_fak_penj' => $no_fak_penj,
                        'kode_barang' => $t->kode_barang,
                        'harga_dus' => $t->harga_dus,
                        'harga_pack' => $t->harga_pack,
                        'harga_pcs' => $t->harga_pcs,
                        'jumlah' => $t->jumlah,
                        'subtotal' => $t->subtotal,
                        'id_admin' => $id_admin
                    ]);


                // if ($jenis_retur == "gb") {
                //     DB::table('detailreturgb')
                //         ->insert([
                //             'no_retur_penj' => $no_retur_penj,
                //             'no_fak_penj' => $no_fak_penj,
                //             'kode_barang' => $t->kode_barang,
                //             'harga_dus' => $t->harga_dus,
                //             'harga_pack' => $t->harga_pack,
                //             'harga_pcs' => $t->harga_pcs,
                //             'jumlah' => $t->jumlah,
                //             'subtotal' => $t->subtotal,
                //             'id_admin' => $id_admin
                //         ]);
                // }
            }

            DB::table('detailretur_temp')->where('kode_pelanggan', $kode_pelanggan)->delete();
            DB::commit();
            return redirect('/retur')->with(['success' => 'Data Retur Berhasil di Simpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/retur')->with(['warning' => 'Data Retur Gagal di Simpan']);
        }
    }
    public function show(Request $request)
    {
        $detail = DB::table('detailretur')
            ->select('detailretur.*', 'kode_produk', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('barang', 'detailretur.kode_barang', '=', 'barang.kode_barang')
            ->where('no_retur_penj', $request->no_retur_penj)
            ->get();

        if (Auth::user()->level != "salesman") {
            return view('retur.show', compact('detail'));
        } else {
            return view('retur.showforsales', compact('detail'));
        }
    }

    public function delete($no_retur_penj)
    {
        $no_retur_penj = Crypt::decrypt($no_retur_penj);
        $retur = DB::table('retur')
            ->select('retur.*', 'penjualan.jenistransaksi', 'penjualan.tgltransaksi', 'penjualan.total as totalpenjualan')
            ->join('penjualan', 'retur.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->where('no_retur_penj', $no_retur_penj)->first();

        $historibayar = DB::table('historibayar')
            ->where('no_fak_penj', $retur->no_fak_penj)
            ->where('tglbayar', $retur->tgltransaksi)
            ->first();
        $nobukti = $historibayar != null ? $historibayar->nobukti : '';
        DB::beginTransaction();
        try {
            DB::table('retur')
                ->where('no_retur_penj', $no_retur_penj)
                ->delete();

            if ($retur->jenistransaksi == "tunai" and $retur->jenis_retur = "pf") {
                DB::table('historibayar')
                    ->where('no_fak_penj', $retur->no_fak_penj)
                    ->where('tglbayar', $retur->tgltransaksi)
                    ->update([
                        'bayar' => $retur->totalpenjualan
                    ]);

                DB::table('buku_besar')
                    ->where('no_ref', $nobukti)
                    ->update([
                        'debet' =>  $retur->totalpenjualan
                    ]);
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function laporanretur()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('retur.laporan.frm.lap_retur', compact('cabang'));
    }

    public function cetaklaporanretur(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->first();
        $query = Retur::query();
        $query->selectRaw('no_retur_penj,no_ref,retur.no_fak_penj,penjualan.kode_pelanggan,nama_pelanggan,pasar,hari,
        karyawan.kode_cabang,tglretur,subtotal_gb,subtotal_pf,retur.total,jenistransaksi,retur.date_created,retur.date_updated');
        $query->join('penjualan', 'retur.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->whereBetween('tglretur', [$dari, $sampai]);
        if ($request->kode_cabang != "") {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }
        if ($request->id_karyawan != "") {
            $query->where('penjualan.id_karyawan', $request->id_karyawan);
        }

        if ($request->kode_pelanggan != "") {
            $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
        }
        $query->orderBy('tglretur', 'asc');
        $retur = $query->get();

        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Retur Periode $dari-$sampai-$time.xls");
        }
        return view('retur.laporan.cetak_retur', compact('retur', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan'));
    }

    public function inputbarangtemp(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        if (!empty($request->kategori_salesman)) {
            $kategori_salesman = $request->kategori_salesman;
        } else {
            $kategori_salesman = "NORMAL";
        }
        $kode_pelanggan = $request->kode_pelanggan;
        // $barang = DB::table('barang')
        //     ->select('barang.*')
        //     ->where('kode_cabang', $kode_cabang)->where('kategori_harga', $kategori_salesman)
        //     ->join('master_barang', 'barang.kode_produk', '=', 'master_barang.kode_produk')->where('status', 1)
        //     ->orderBy('barang.kode_produk')
        //     ->get();

        // echo $kode_cabang . $kategori_salesman . $kode_pelanggan;
        // die;

        $cekpelanggan = DB::table('barang')->where('kode_pelanggan', $kode_pelanggan)->count();
        if ($cekpelanggan > 0) {
            $barang = Harga::orderby('nama_barang', 'asc')
                ->select('barang.*')
                ->where('kode_cabang', $kode_cabang)
                ->where('kode_pelanggan', $kode_pelanggan)
                ->join('master_barang', 'barang.kode_produk', '=', 'master_barang.kode_produk')->where('status', 1)
                ->get();

            // $barangnew = DB::table('barang_new')
            //     ->select('barang_new.*')
            //     ->where('kode_cabang', $kode_cabang)
            //     ->where('kode_pelanggan', $kode_pelanggan)
            //     ->join('master_barang', 'barang_new.kode_produk', '=', 'master_barang.kode_produk')->where('barang_new.status_harga', 1)
            //     ->orderBy('barang_new.kode_produk', 'asc')
            //     ->get();
        } else {
            if ($kategori_salesman == "TOCANVASER") {
                $barang = Harga::orderby('nama_barang', 'asc')
                    ->select('barang.*')
                    ->join('master_barang', 'barang.kode_produk', '=', 'master_barang.kode_produk')->where('status', 1)
                    ->where('kode_cabang', $kode_cabang)
                    ->where('kategori_harga', 'TO')
                    ->orwhere('kode_cabang', $kode_cabang)
                    ->where('kategori_harga', 'CANVASER')
                    ->get();
                // $barangnew = DB::table('barang_new')
                //     ->select('barang_new.*')
                //     ->join('master_barang', 'barang_new.kode_produk', '=', 'master_barang.kode_produk')->where('barang_new.status_harga', 1)
                //     ->where('kode_cabang', $kode_cabang)
                //     ->where('kategori_harga', 'TO')
                //     ->orwhere('kode_cabang', $kode_cabang)
                //     ->where('kategori_harga', 'CANVASER')
                //     ->orderby('barang_new.kode_produk', 'asc')
                //     ->get();
            } else {
                $barang = Harga::orderby('nama_barang', 'asc')
                    ->select('barang.*')
                    ->join('master_barang', 'barang.kode_produk', '=', 'master_barang.kode_produk')->where('status', 1)
                    ->where('kode_cabang', $kode_cabang)
                    ->where('kategori_harga', $kategori_salesman)
                    ->get();

                // $barangnew = DB::table('barang_new')
                //     ->select('barang_new.*')
                //     ->join('master_barang', 'barang_new.kode_produk', '=', 'master_barang.kode_produk')->where('barang_new.status_harga', 1)
                //     ->where('kode_cabang', $kode_cabang)
                //     ->where('kategori_harga', $kategori_salesman)
                //     ->orderBy('barang_new.kode_produk', 'asc')
                //     ->get();
            }
        }
        return view('retur.inputbarangtemp', compact('barang'));
    }
}
