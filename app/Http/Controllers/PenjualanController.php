<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Cabang;
use App\Models\Detailpenjualan;
use App\Models\Detailretur;
use App\Models\Harga;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use App\Models\Retur;
use App\Models\Salesman;
use COM;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use PDOException;
use Svg\Tag\Rect;

class PenjualanController extends Controller
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

        if (isset($request->print)) {
            if (empty($request->dari) || empty($request->sampai)) {
                return Redirect::back()->with(['danger' => 'Periode Harus Diisi']);
            }
            $pelangganmp = [
                'TSM-00548',
                'TSM-00493',
                'TSM-02234',
                'TSM-01117',
                'TSM-00494',
                'TSM-00466',
                'PST00007',
                'PST00008',
                'PST00002'
            ];
            $query = Penjualan::query();
            $query->select(
                'penjualan.*',
                'nama_pelanggan',
                'alamat_pelanggan',
                'alamat_toko',
                'nama_cabang',
                'karyawan.kode_cabang',
                'alamat_cabang',
                'nama_karyawan',
                'kategori_salesman',
                DB::raw('IFNULL(totalpf,0) - IFNULL(totalgb,0) as totalretur'),
                DB::raw('IFNULL(total,0) - (IFNULL(totalpf,0) - IFNULL(totalgb,0)) as total')
            );
            $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
            $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
            $query->leftJoin(
                DB::raw("(
                    SELECT
                    no_fak_penj,
                    SUM(subtotal_gb) AS totalgb,
                    SUM(subtotal_pf) AS totalpf
                FROM
                    retur
                GROUP BY
                    no_fak_penj
                ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            );
            $query->whereBetween('tgltransaksi', [$request->dari, $request->sampai]);
            if (!empty($request->id_karyawan)) {
                $query->where('penjualan.id_karyawan', $request->id_karyawan);
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

            if ($this->cabang != "PCF" and $this->cabang != "PST") {
                $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
                $cabang[] = "";
                foreach ($cbg as $c) {
                    $cabang[] = $c->kode_cabang;
                }
                $query->whereIn('karyawan.kode_cabang', $cabang);
            }
            $penjualan = $query->get();
            return view('penjualan.laporan.cetaksuratjalantanggal', compact('penjualan', 'pelangganmp'));
        } else {
            $dari = !empty($request->dari) ? $request->dari : date("Y-m-d");
            $sampai = !empty($request->sampai) ? $request->sampai : date("Y-m-d");
            $pelanggan = '"' . $request->nama_pelanggan . '"';
            $query = Penjualan::query();
            $query->select('penjualan.*', 'nama_pelanggan', 'nama_karyawan', 'karyawan.kode_cabang');
            $query->orderBy('tgltransaksi', 'desc');
            $query->orderBy('no_fak_penj', 'asc');
            $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
            $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            if (empty($request->no_fak_penj) && empty($request->nama_pelanggan) && empty($request->kode_pelanggan) && empty($request->id_karyawan) && empty($request->dari) && empty($request->sampai)) {
                $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
            }
            if (!empty($request->nama_pelanggan)) {
                $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
            }
            if (!empty($request->kode_pelanggan)) {
                $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
            }

            if (!empty($request->id_karyawan)) {
                $query->where('penjualan.id_karyawan', $request->id_karyawan);
            }
            if (!empty($request->no_fak_penj)) {
                $query->where('no_fak_penj', $request->no_fak_penj);
            }

            if (!empty($request->status)) {
                $query->where('status', $request->status);
            }

            if (Auth::user()->level != "salesman") {
                if (!empty($request->dari) && !empty($request->sampai)) {
                    $query->whereBetween('tgltransaksi', [$request->dari, $request->sampai]);
                }
            } else {
                if (!empty($request->dari) && !empty($request->sampai)) {
                    $query->whereBetween('tgltransaksi', [$request->dari, $request->sampai]);
                }
            }

            if (Auth::user()->level == "salesman") {
                $query->where('penjualan.id_karyawan', Auth::user()->id_salesman);
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

            if ($this->cabang != "PCF" and $this->cabang != "PST") {
                $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
                $cabang[] = "";
                foreach ($cbg as $c) {
                    $cabang[] = $c->kode_cabang;
                }
                $query->whereIn('karyawan.kode_cabang', $cabang);
            }
            $penjualan = $query->paginate(15);

            $penjualan->appends($request->all());


            // if ($this->cabang != "PCF") {
            //     if ($this->cabang == "GRT" || $this->cabang == "TSM") {
            //         $salesman = Salesman::where('kode_cabang', 'TSM')->where('nama_karyawan', '!=', '-')->orderBy('nama_karyawan')->get();
            //     } else {
            //         $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)
            //             ->orWhere('sub_cabang', $this->cabang)
            //             ->get();
            //         $cabang[] = "";
            //         foreach ($cbg as $c) {
            //             $cabang[] = $c->kode_cabang;
            //         }
            //         $salesman = Salesman::whereIn('kode_cabang', $cabang)
            //             ->where('nama_karyawan', '!=', '-')
            //             ->orderBy('nama_karyawan')->get();
            //     }
            // } else {
            //     $salesman = Salesman::orderBy('nama_karyawan')->where('nama_karyawan', '!=', '-')->get();
            // }

            if ($this->cabang != "PCF" and $this->cabang != "PST") {
                $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)
                    ->orWhere('sub_cabang', $this->cabang)
                    ->get();
                $cabang[] = "";
                foreach ($cbg as $c) {
                    $cabang[] = $c->kode_cabang;
                }
                $salesman = Salesman::whereIn('kode_cabang', $cabang)
                    ->where('nama_karyawan', '!=', '-')
                    ->orderBy('nama_karyawan')
                    ->where('status_aktif_sales', 1)
                    ->get();
            } else {
                $salesman = Salesman::orderBy('nama_karyawan')->where('nama_karyawan', '!=', '-')->where('status_aktif_sales', 1)->get();
            }
            $cabang = Cabang::orderBy('kode_cabang')->get();
            if (Auth::user()->level == "salesman") {
                return view('penjualan.indexsalesman', compact('penjualan', 'salesman', 'cabang'));
            } else {
                return view('penjualan.index', compact('penjualan', 'salesman', 'cabang'));
            }
        }
    }


    //Create

    public function create()
    {
        return view('penjualan.create');
    }


    public function create_v2()
    {
        if (Auth::user()->level == "salesman") {
            $kodepelanggan = Cookie::get('kodepelanggan');
            $kode_pelanggan = $kodepelanggan != null ? Crypt::decrypt($kodepelanggan) : '';
            $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)
                ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
                ->first();
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
            return view('penjualan.create_v3', compact('pelanggan', 'piutang'));
        } else {
            return view('penjualan.create_v2');
        }
    }


    public function editv2($no_fak_penj)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        // $cek = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj)->count();
        // if ($cek > 0) {
        //     return Redirect::back()->with(['warning' => 'Data Tidak Bisa Di Edit, Karena Sudah Ada Pembayaran Untuk Transaksi Ini']);
        // }


        $faktur = DB::table('penjualan')
            ->select(
                'penjualan.*',
                'nama_pelanggan',
                'alamat_pelanggan',
                'alamat_toko',
                'pelanggan.no_hp',
                'latitude',
                'longitude',
                'limitpel',
                'karyawan.nama_karyawan',
                'karyawan.kategori_salesman',
                'karyawan.kode_cabang'
            )
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('penjualan.no_fak_penj', $no_fak_penj)
            ->first();

        $cektitipan = DB::table('historibayar')
            ->where('tglbayar', $faktur->tgltransaksi)
            ->where('no_fak_penj', $no_fak_penj)
            ->where('jenisbayar', 'titipan')
            ->first();
        $cekvouchertunai = DB::table('historibayar')
            ->where('tglbayar', $faktur->tgltransaksi)
            ->where('no_fak_penj', $no_fak_penj)
            ->where('jenisbayar', 'tunai')
            ->where('status_bayar', 'voucher')
            ->orwhere('tglbayar', $faktur->tgltransaksi)
            ->where('no_fak_penj', $no_fak_penj)
            ->where('jenisbayar', 'transfer')
            ->where('status_bayar', 'voucher')
            ->first();

        DB::beginTransaction();
        try {
            DB::table('detailpenjualan_edit')->where('no_fak_penj', $no_fak_penj)->delete();
            $detail = DB::table('detailpenjualan')->where('no_fak_penj', $no_fak_penj)->get();
            foreach ($detail as $d) {
                DB::table('detailpenjualan_edit')->insert([
                    'no_fak_penj' => $d->no_fak_penj,
                    'kode_barang' => $d->kode_barang,
                    'harga_dus' => $d->harga_dus,
                    'harga_pack' => $d->harga_pack,
                    'harga_pcs' => $d->harga_pcs,
                    'jumlah' => $d->jumlah,
                    'subtotal' => $d->subtotal,
                    'promo' => $d->promo,
                    'id_admin' => $d->id_admin
                ]);
            }
            DB::commit();
            if (Auth::user()->level == "salesman") {
                return view('penjualan.editv3', compact('faktur', 'cektitipan', 'cekvouchertunai'));
            } else {
                return view('penjualan.editv2', compact('faktur', 'cektitipan', 'cekvouchertunai'));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return Redirect::back();
        }
    }
    public function edit($no_fak_penj)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        DB::beginTransaction();
        try {
            DB::table('detailpenjualan_edit')->where('no_fak_penj', $no_fak_penj)->delete();
            $detail = DB::table('detailpenjualan')->where('no_fak_penj', $no_fak_penj)->get();
            foreach ($detail as $d) {
                DB::table('detailpenjualan_edit')->insert([
                    'no_fak_penj' => $d->no_fak_penj,
                    'kode_barang' => $d->kode_barang,
                    'harga_dus' => $d->harga_dus,
                    'harga_pack' => $d->harga_pack,
                    'harga_pcs' => $d->harga_pcs,
                    'jumlah' => $d->jumlah,
                    'subtotal' => $d->subtotal,
                    'promo' => $d->promo,
                    'id_admin' => $d->id_admin
                ]);
            }
            $faktur = DB::table('penjualan')
                ->select(
                    'penjualan.*',
                    'nama_pelanggan',
                    'alamat_pelanggan',
                    'alamat_toko',
                    'pelanggan.no_hp',
                    'latitude',
                    'longitude',
                    'limitpel',
                    'karyawan.nama_karyawan',
                    'karyawan.kategori_salesman',
                    'karyawan.kode_cabang'
                )
                ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
                ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                ->where('penjualan.no_fak_penj', $no_fak_penj)
                ->first();

            $cektitipan = DB::table('historibayar')
                ->where('tglbayar', $faktur->tgltransaksi)
                ->where('no_fak_penj', $no_fak_penj)
                ->where('jenisbayar', 'titipan')
                ->first();
            $cekvouchertunai = DB::table('historibayar')
                ->where('tglbayar', $faktur->tgltransaksi)
                ->where('no_fak_penj', $no_fak_penj)
                ->where('jenisbayar', 'tunai')
                ->where('status_bayar', 'voucher')
                ->first();
            DB::commit();
            return view('penjualan.edit', compact('faktur', 'cektitipan', 'cekvouchertunai'));
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return Redirect::back();
        }
    }


    public function storebarangtemp(Request $request)
    {
        $barang = Harga::where('kode_barang', $request->kode_barang)->first();
        $id_user = Auth::user()->id;
        $cek = DB::table('detailpenjualan_temp')->where('kode_barang', $request->kode_barang)->where('id_admin', $id_user)->whereNull('promo')->count();
        if (empty($cek)) {
            $simpan = DB::table('detailpenjualan_temp')
                ->insert([
                    'kode_barang' => $request->kode_barang,
                    'jumlah' => 0,
                    'harga_dus' => $barang->harga_dus,
                    'harga_pack' => $barang->harga_pack,
                    'harga_pcs' => $barang->harga_pcs,
                    'subtotal' => 0,
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
        $promo = !empty($request->promo) ? $request->promo : NULL;
        $data = [
            'kode_barang' => $kode_barang,
            'jumlah' => $jumlah,
            'harga_dus' => $harga_dus,
            'harga_pack' => $harga_pack,
            'harga_pcs' => $harga_pcs,
            'subtotal' => $subtotal,
            'id_admin' => $id_admin,
            'promo' => $promo
        ];

        if ($promo == NULL) {
            $cek = DB::table('detailpenjualan_temp')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)
                ->whereNull('promo')
                ->count();
        } else {
            $cek = DB::table('detailpenjualan_temp')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)
                ->where('promo', 1)
                ->count();
        }

        if ($cek > 0) {
            echo 1;
        } else {
            try {
                DB::table('detailpenjualan_temp')->insert($data);
                echo 0;
            } catch (Exception $e) {
                echo $e;
            }
        }
    }



    public function storebarang(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $kode_barang = $request->kode_barang;
        $harga_dus = $request->hargadus;
        $harga_pack = $request->hargapack;
        $harga_pcs = $request->hargapcs;
        $jumlah = $request->jumlah;
        $subtotal = $request->subtotal;
        $id_admin = Auth::user()->id;
        $promo = !empty($request->promo) ? $request->promo : NULL;
        $data = [
            'no_fak_penj' => $no_fak_penj,
            'kode_barang' => $kode_barang,
            'jumlah' => $jumlah,
            'harga_dus' => $harga_dus,
            'harga_pack' => $harga_pack,
            'harga_pcs' => $harga_pcs,
            'subtotal' => $subtotal,
            'id_admin' => $id_admin,
            'promo' => $promo
        ];

        if ($promo == NULL) {
            $cek = DB::table('detailpenjualan_edit')->where('kode_barang', $kode_barang)->where('no_fak_penj', $no_fak_penj)
                ->whereNull('promo')
                ->count();
        } else {
            $cek = DB::table('detailpenjualan_edit')->where('kode_barang', $kode_barang)->where('no_fak_penj', $no_fak_penj)
                ->where('promo', 1)
                ->count();
        }

        if ($cek > 0) {
            echo 1;
        } else {
            try {
                DB::table('detailpenjualan_edit')->insert($data);
                echo 0;
            } catch (Exception $e) {
                echo $e;
            }
        }
    }

    // public function storebarang(Request $request)
    // {
    //     $no_fak_penj = $request->no_fak_penj;
    //     $barang = Harga::where('kode_barang', $request->kode_barang)->first();
    //     $id_user = Auth::user()->id;
    //     $cek = DB::table('detailpenjualan_edit')->where('kode_barang', $request->kode_barang)->where('no_fak_penj', $no_fak_penj)->whereNull('promo')->count();
    //     if (empty($cek)) {
    //         $simpan = DB::table('detailpenjualan_edit')
    //             ->insert([
    //                 'no_fak_Penj' => $no_fak_penj,
    //                 'kode_barang' => $request->kode_barang,
    //                 'jumlah' => 0,
    //                 'harga_dus' => $barang->harga_dus,
    //                 'harga_pack' => $barang->harga_pack,
    //                 'harga_pcs' => $barang->harga_pcs,
    //                 'subtotal' => 0,
    //                 'id_admin' => $id_user
    //             ]);
    //         if ($simpan) {
    //             echo 0;
    //         } else {
    //             echo 2;
    //         }
    //     } else {
    //         echo 1;
    //     }
    // }

    public function showbarangtemp()
    {
        $id_user = Auth::user()->id;
        $barang = DB::table('detailpenjualan_temp')
            ->select('detailpenjualan_temp.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs', 'cekjmlbarang')
            ->join('barang', 'detailpenjualan_temp.kode_barang', '=', 'barang.kode_barang')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_barang,COUNT(kode_barang) as cekjmlbarang FROM detailpenjualan_temp
                    WHERE id_admin = '$id_user'
                    GROUP BY kode_barang
                ) dbtemp"),
                function ($join) {
                    $join->on('detailpenjualan_temp.kode_barang', '=', 'dbtemp.kode_barang');
                }
            )
            ->where('id_admin', $id_user)
            ->get();
        return view('penjualan.showbarangtemp', compact('barang'));
    }

    public function showbarang(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $faktur = DB::table('penjualan')
            ->select(
                'penjualan.*'
            )
            ->where('penjualan.no_fak_penj', $no_fak_penj)
            ->first();
        $barang = DB::table('detailpenjualan_edit')
            ->select('detailpenjualan_edit.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs', 'cekjmlbarang')
            ->join('barang', 'detailpenjualan_edit.kode_barang', '=', 'barang.kode_barang')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_barang,COUNT(kode_barang) as cekjmlbarang FROM detailpenjualan_edit GROUP BY kode_barang
                ) db"),
                function ($join) {
                    $join->on('detailpenjualan_edit.kode_barang', '=', 'db.kode_barang');
                }
            )
            ->where('no_fak_penj', $no_fak_penj)
            ->get();
        return view('penjualan.showbarang', compact('barang', 'faktur'));
    }




    public function showbarangv2(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $barang = DB::table('detailpenjualan_edit')
            ->select('detailpenjualan_edit.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('barang', 'detailpenjualan_edit.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->get();
        if (Auth::user()->level == "salesman") {
            return view('penjualan.showbarangv3', compact('barang'));
        } else {
            return view('penjualan.showbarangv2', compact('barang'));
        }
    }




    public function deletebarangtemp(Request $request)
    {
        $id_user = Auth::user()->id;
        $hapus = DB::table('detailpenjualan_temp')
            ->where('kode_barang', $request->kode_barang)
            ->where('id_admin', $id_user)
            ->where('promo', $request->promo)
            ->delete();
        if ($hapus) {
            echo 1;
        } else {
            echo 2;
        }
    }


    public function deletebarang(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $hapus = DB::table('detailpenjualan_edit')
            ->where('kode_barang', $request->kode_barang)
            ->where('no_fak_penj', $no_fak_penj)
            ->where('promo', $request->promo)
            ->delete();
        if ($hapus) {
            echo 1;
        } else {
            echo 2;
        }
    }

    public function updatedetailtemp(Request $request)
    {
        $id_user = Auth::user()->id;
        $barang = DB::table('barang')->where('kode_barang', $request->kode_barang)->first();
        $detailtemp = DB::table('detailpenjualan_temp')->where('kode_barang', $request->kode_barang)->where('id_admin', $id_user)->first();
        $jmldus = $request->jmldus * $barang->isipcsdus;
        $jmlpack = $request->jmlpack * $barang->isipcs;
        $jmlpcs = $request->jmlpcs;
        // echo $request->harga_dus;
        // die;
        if (isset($request->promo)) {
            if ($request->promo == 1) {
                $promo = 1;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = 1;
                }
            } else {
                $promo = NULL;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                        echo "test1";
                    } else {
                        $wherepromo = 1;
                        echo "test2";
                    }
                } else {
                    $wherepromo = NULL;
                    echo "test";
                }
            }
        } else {
            if ($detailtemp->promo == 1) {
                $promo = 1;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = 1;
                }
            } else {
                $promo = NULL;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = NULL;
                }
            }
        }

        //$cekpromo = DB::table('detailpenjualan_temp')->where('kode_barang', $request->kode_barang)->where('id_admin', $id_user)->where('promo', $promo)->count();
        $harga_dus = str_replace(".", "", $request->harga_dus);
        $harga_pack = str_replace(".", "", $request->harga_pack);
        $harga_pcs = str_replace(".", "", $request->harga_pcs);
        $totalqty = $jmldus + $jmlpack + $jmlpcs;
        $total = $request->total;

        DB::table('detailpenjualan_temp')
            ->where('kode_barang', $request->kode_barang)
            ->where('id_admin', $id_user)
            ->where('promo', $wherepromo)
            ->update([
                'jumlah' => $totalqty,
                'harga_dus' => $harga_dus,
                'harga_pack' => $harga_pack,
                'harga_pcs' => $harga_pcs,
                'subtotal' => $total,
                'promo' => $promo
            ]);
    }


    public function updatedetail(Request $request)
    {

        $no_fak_penj = $request->no_fak_penj;
        $barang = DB::table('barang')->where('kode_barang', $request->kode_barang)->first();
        $detail = DB::table('detailpenjualan_edit')->where('kode_barang', $request->kode_barang)->where('no_fak_penj', $no_fak_penj)->first();
        $jmldus = $request->jmldus * $barang->isipcsdus;
        $jmlpack = $request->jmlpack * $barang->isipcs;
        $jmlpcs = $request->jmlpcs;
        // echo $request->harga_dus;
        // die;
        if (isset($request->promo)) {
            if ($request->promo == 1) {
                $promo = 1;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = 1;
                }
            } else {
                $promo = NULL;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                        echo "test1";
                    } else {
                        $wherepromo = 1;
                        echo "test2";
                    }
                } else {
                    $wherepromo = NULL;
                    echo "test";
                }
            }
        } else {
            if ($detail->promo == 1) {
                $promo = 1;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = 1;
                }
            } else {
                $promo = NULL;
                if (isset($request->check)) {
                    if ($request->check == "true") {
                        $wherepromo = NULL;
                    } else {
                        $wherepromo = 1;
                    }
                } else {
                    $wherepromo = NULL;
                }
            }
        }

        //$cekpromo = DB::table('detailpenjualan_temp')->where('kode_barang', $request->kode_barang)->where('id_admin', $id_user)->where('promo', $promo)->count();
        $harga_dus = str_replace(".", "", $request->harga_dus);
        $harga_pack = str_replace(".", "", $request->harga_pack);
        $harga_pcs = str_replace(".", "", $request->harga_pcs);
        $totalqty = $jmldus + $jmlpack + $jmlpcs;
        $total = $request->total;

        DB::table('detailpenjualan_edit')
            ->where('kode_barang', $request->kode_barang)
            ->where('no_fak_penj', $no_fak_penj)
            ->where('promo', $wherepromo)
            ->update([
                'jumlah' => $totalqty,
                'harga_dus' => $harga_dus,
                'harga_pack' => $harga_pack,
                'harga_pcs' => $harga_pcs,
                'subtotal' => $total,
                'promo' => $promo
            ]);
    }

    public function loadtotalpenjualantemp()
    {
        $detail = DB::table('detailpenjualan_temp')
            ->select(DB::raw('SUM(subtotal) AS total'))
            ->where('id_admin', Auth::user()->id)
            ->first();
        echo rupiah($detail->total);
    }

    public function loadtotalpenjualan(Request $request)
    {
        $detail = DB::table('detailpenjualan_edit')
            ->select(DB::raw('SUM(subtotal) AS total'))
            ->where('no_fak_penj', $request->no_fak_penj)
            ->first();
        echo rupiah($detail->total);
    }

    public function hitungdiskon(Request $request)
    {
        $tgltransaksi = $request->tgltransaksi;
        $jenistransaksi = $request->jenistransaksi;
        $id_user = Auth::user()->id;
        $detail = DB::table('detailpenjualan_temp')
            ->select('detailpenjualan_temp.kode_barang', 'promo', 'isipcsdus', 'kategori', 'jumlah')
            ->join('barang', 'detailpenjualan_temp.kode_barang', '=', 'barang.kode_barang')
            ->where('id_admin', $id_user)
            ->whereNull('promo')
            ->get();
        $jmldusswan = 0;
        $jmldusaida = 0;
        $jmldusstick = 0;
        $jmldussp = 0;
        $jmldussb = 0;


        foreach ($detail as $d) {
            $jmldus      = floor($d->jumlah / $d->isipcsdus);
            if ($d->kategori == "SWAN") {
                $jmldusswan   = $jmldusswan + $jmldus;
            }

            if ($d->kategori == "AIDA") {
                $jmldusaida   = $jmldusaida + $jmldus;
            }

            if ($d->kategori == "STICK") {
                $jmldusstick   = $jmldusstick + $jmldus;
            }



            if ($d->kategori == "SP") {
                $jmldussp   = $jmldussp + $jmldus;
            }

            if ($d->kategori == "SC") {
                $jmldussb   = $jmldussb + $jmldus;
            }
        }



        // if ($tgltransaksi >= "2023-02-01") {
        //     $diskon = DB::table('diskonfebruari')->get();
        // } else {

        // }

        $diskon = DB::table('diskon')->get();
        $diskonswan = 0;
        $diskonaida = 0;
        $diskonstick = 0;
        $diskonsp = 0;
        $diskonsb = 0;

        $diskonswantunai = 0;
        $diskonaidatunai = 0;
        $diskonsticktunai = 0;
        $diskonsptunai = 0;
        $diskonsbtunai = 0;
        //dd($diskon);
        foreach ($diskon as $p) {
            if ($p->kategori == "SWAN" and $jmldusswan >= $p->dari and $jmldusswan <= $p->sampai) {
                $diskonswan = $p->diskon;
                $diskonswantunai = $p->diskon_tunai;
            }

            if ($p->kategori == "AIDA" and $jmldusaida >= $p->dari and $jmldusaida <= $p->sampai) {
                $diskonaida = $p->diskon;
                $diskonaidatunai = $p->diskon_tunai;
            }

            if ($p->kategori == "STICK" and $jmldusstick >= $p->dari and $jmldusstick <= $p->sampai) {
                $diskonstick = $p->diskon;
                $diskonsticktunai = $p->diskon_tunai;
            }

            if ($p->kategori == "SP" and $jmldussp >= $p->dari and $jmldussp <= $p->sampai) {
                $diskonsp = $p->diskon;
                $diskonsptunai = $p->diskon_tunai;
            }

            if ($p->kategori == "SC" and $jmldussb >= $p->dari and $jmldussb <= $p->sampai) {
                $diskonsb = $p->diskon;
                $diskonsbtunai = $p->diskon_tunai;
            }
        }

        //dd($diskonsb);
        if ($jenistransaksi == "tunai") {
            $totaldiskonswan = ($jmldusswan * $diskonswan) + ($jmldusswan * $diskonswantunai);
            $totaldiskonaida = ($jmldusaida * $diskonaida) + ($jmldusaida * $diskonaidatunai);
            $totaldiskonstick = ($jmldusstick * $diskonstick) + ($jmldusstick * $diskonsticktunai);
            $totaldiskonsp = ($jmldussp * $diskonsp) + ($jmldussp * $diskonsptunai);
            $totaldiskonsb = ($jmldussb * $diskonsb) + ($jmldussb * $diskonsbtunai);
        } else {
            $totaldiskonswan = $jmldusswan * $diskonswan;
            $totaldiskonaida = $jmldusaida * $diskonaida;
            $totaldiskonstick = $jmldusstick * $diskonstick;
            $totaldiskonsp = $jmldussp * $diskonsp;
            $totaldiskonsb = $jmldussb * $diskonsb;
        }

        echo rupiah($totaldiskonswan), "|" . rupiah($totaldiskonaida) . "|" . rupiah($totaldiskonstick) . "|" . rupiah($totaldiskonsp) . "|" . rupiah($totaldiskonsb);
    }


    public function hitungdiskonpenjualan(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $jenistransaksi = $request->jenistransaksi;
        $detail = DB::table('detailpenjualan_edit')
            ->select('detailpenjualan_edit.kode_barang', 'promo', 'isipcsdus', 'kategori', 'jumlah')
            ->join('barang', 'detailpenjualan_edit.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->whereNull('promo')
            ->get();
        $jmldusswan = 0;
        $jmldusaida = 0;
        $jmldusstick = 0;
        $jmldussp = 0;
        $jmldussb = 0;
        foreach ($detail as $d) {
            $jmldus      = floor($d->jumlah / $d->isipcsdus);
            if ($d->kategori == "SWAN") {
                $jmldusswan   = $jmldusswan + $jmldus;
            }

            if ($d->kategori == "AIDA") {
                $jmldusaida   = $jmldusaida + $jmldus;
            }

            if ($d->kategori == "STICK") {
                $jmldusstick   = $jmldusstick + $jmldus;
            }


            if ($d->kategori == "SP") {
                $jmldussp   = $jmldussp + $jmldus;
            }

            if ($d->kategori == "SAMBAL") {
                $jmldussb   = $jmldussb + $jmldus;
            }
        }

        $diskon = DB::table('diskon')->get();
        $diskonswan = 0;
        $diskonaida = 0;
        $diskonstick = 0;
        $diskonsp = 0;
        $diskonsb = 0;

        $diskonswantunai = 0;
        $diskonaidatunai = 0;
        $diskonsticktunai = 0;
        $diskonsptunai = 0;
        $diskonsbtunai = 0;
        foreach ($diskon as $p) {
            if ($p->kategori == "SWAN" and $jmldusswan >= $p->dari and $jmldusswan <= $p->sampai) {
                $diskonswan = $p->diskon;
                $diskonswantunai = $p->diskon_tunai;
            }

            if ($p->kategori == "AIDA" and $jmldusaida >= $p->dari and $jmldusaida <= $p->sampai) {
                $diskonaida = $p->diskon;
                $diskonaidatunai = $p->diskon_tunai;
            }

            if ($p->kategori == "STICK" and $jmldusstick >= $p->dari and $jmldusstick <= $p->sampai) {
                $diskonstick = $p->diskon;
                $diskonsticktunai = $p->diskon_tunai;
            }

            if ($p->kategori == "SP" and $jmldussp >= $p->dari and $jmldussp <= $p->sampai) {
                $diskonsp = $p->diskon;
                $diskonsptunai = $p->diskon_tunai;
            }

            if ($p->kategori == "SC" and $jmldussb >= $p->dari and $jmldussb <= $p->sampai) {
                $diskonsb = $p->diskon;
                $diskonsbtunai = $p->diskon_tunai;
            }
        }

        if ($jenistransaksi == "tunai") {
            $totaldiskonswan = ($jmldusswan * $diskonswan) + ($jmldusswan * $diskonswantunai);
            $totaldiskonaida = ($jmldusaida * $diskonaida) + ($jmldusaida * $diskonaidatunai);
            $totaldiskonstick = ($jmldusstick * $diskonstick) + ($jmldusstick * $diskonsticktunai);
            $totaldiskonsp = ($jmldussp * $diskonsp) + ($jmldussp * $diskonsptunai);
            $totaldiskonsb = ($jmldussb * $diskonsb) + ($jmldussb * $diskonsbtunai);
        } else {
            $totaldiskonswan = $jmldusswan * $diskonswan;
            $totaldiskonaida = $jmldusaida * $diskonaida;
            $totaldiskonstick = $jmldusstick * $diskonstick;
            $totaldiskonsp = $jmldussp * $diskonsp;
            $totaldiskonsb = $jmldussb * $diskonsb;
        }

        echo rupiah($totaldiskonswan), "|" . rupiah($totaldiskonaida) . "|" . rupiah($totaldiskonstick) . "|" . rupiah($totaldiskonsp) . "|" . rupiah($totaldiskonsb);
    }



    public function hitungdiskonpenjualanv2(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $jenistransaksi = $request->jenistransaksi;
        $detail = DB::table('detailpenjualan_edit')
            ->select('detailpenjualan_edit.kode_barang', 'promo', 'isipcsdus', 'kategori', 'jumlah')
            ->join('barang', 'detailpenjualan_edit.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->whereNull('promo')
            ->get();
        $jmldusswan = 0;
        $jmldusaida = 0;
        $jmldusstick = 0;
        $jmldussp = 0;
        $jmldussb = 0;
        foreach ($detail as $d) {
            $jmldus      = floor($d->jumlah / $d->isipcsdus);
            if ($d->kategori == "SWAN") {
                $jmldusswan   = $jmldusswan + $jmldus;
            }

            if ($d->kategori == "AIDA") {
                $jmldusaida   = $jmldusaida + $jmldus;
            }

            if ($d->kategori == "STICK") {
                $jmldusstick   = $jmldusstick + $jmldus;
            }


            if ($d->kategori == "SP") {
                $jmldussp   = $jmldussp + $jmldus;
            }

            if ($d->kategori == "SC") {
                $jmldussb   = $jmldussb + $jmldus;
            }
        }

        $diskon = DB::table('diskon')->get();
        $diskonswan = 0;
        $diskonaida = 0;
        $diskonstick = 0;
        $diskonsp = 0;
        $diskonsb = 0;

        $diskonswantunai = 0;
        $diskonaidatunai = 0;
        $diskonsticktunai = 0;
        $diskonsptunai = 0;
        $diskonsbtunai = 0;
        foreach ($diskon as $p) {
            if ($p->kategori == "SWAN" and $jmldusswan >= $p->dari and $jmldusswan <= $p->sampai) {
                $diskonswan = $p->diskon;
                $diskonswantunai = $p->diskon_tunai;
            }

            if ($p->kategori == "AIDA" and $jmldusaida >= $p->dari and $jmldusaida <= $p->sampai) {
                $diskonaida = $p->diskon;
                $diskonaidatunai = $p->diskon_tunai;
            }

            if ($p->kategori == "STICK" and $jmldusstick >= $p->dari and $jmldusstick <= $p->sampai) {
                $diskonstick = $p->diskon;
                $diskonsticktunai = $p->diskon_tunai;
            }

            if ($p->kategori == "SP" and $jmldussp >= $p->dari and $jmldussp <= $p->sampai) {
                $diskonsp = $p->diskon;
                $diskonsptunai = $p->diskon_tunai;
            }

            if ($p->kategori == "SC" and $jmldussb >= $p->dari and $jmldussb <= $p->sampai) {
                $diskonsb = $p->diskon;
                $diskonsbtunai = $p->diskon_tunai;
            }
        }

        if ($jenistransaksi == "tunai") {
            $totaldiskonswan = ($jmldusswan * $diskonswan) + ($jmldusswan * $diskonswantunai);
            $totaldiskonaida = ($jmldusaida * $diskonaida) + ($jmldusaida * $diskonaidatunai);
            $totaldiskonstick = ($jmldusstick * $diskonstick) + ($jmldusstick * $diskonsticktunai);
            $totaldiskonsp = ($jmldussp * $diskonsp) + ($jmldussp * $diskonsptunai);
            $totaldiskonsb = ($jmldussb * $diskonsb) + ($jmldussb * $diskonsbtunai);
        } else {
            $totaldiskonswan = $jmldusswan * $diskonswan;
            $totaldiskonaida = $jmldusaida * $diskonaida;
            $totaldiskonstick = $jmldusstick * $diskonstick;
            $totaldiskonsp = $jmldussp * $diskonsp;
            $totaldiskonsb = $jmldussb * $diskonsb;
        }

        echo rupiah($totaldiskonswan), "|" . rupiah($totaldiskonaida) . "|" . rupiah($totaldiskonstick) . "|" . rupiah($totaldiskonsp) . "|" . rupiah($totaldiskonsb);
    }


    public function cekpenjtemp()
    {
        $id_user = Auth::user()->id;
        $barang = DB::table('detailpenjualan_temp')
            ->where('id_admin', $id_user)
            ->count();
        echo $barang;
    }


    public function cekpenj(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $barang = DB::table('detailpenjualan')
            ->where('no_fak_penj', $no_fak_penj)
            ->count();
        echo $barang;
    }

    public function cekpiutangpelanggan(Request $request)
    {
        if (isset($request->no_fak_penj) && $request->no_fak_penj != "") {
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
                ->where('penjualan.kode_pelanggan', $request->kode_pelanggan)
                ->where('penjualan.no_fak_penj', '!=', $request->no_fak_penj)
                ->groupBy('penjualan.kode_pelanggan')
                ->first();
        } else {
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
                ->where('penjualan.kode_pelanggan', $request->kode_pelanggan)
                ->groupBy('penjualan.kode_pelanggan')
                ->first();
        }

        echo $piutang->sisapiutang;
    }

    public function store(Request $request)
    {

        $ceklevel = Auth::user()->level;
        $kategori_salesman = $request->kategori_salesman;
        if ($ceklevel == "salesman" && $kategori_salesman == "TO") {
            $kodecab = Auth::user()->kode_cabang;
            $tgltrans = explode("-", $request->tgltransaksi);
            $bulantrans = $tgltrans[1];
            $tahuntrans = $tgltrans[0];
            $cekpenjualan = DB::table('penjualan')
                ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                ->where('karyawan.kode_cabang', $kodecab)
                ->whereRaw('MONTH(tgltransaksi)="' . $bulantrans . '"')
                ->whereRaw('YEAR(tgltransaksi)="' . $tahuntrans . '"')
                ->whereRaw('MID(no_fak_penj,4,2)="PR"')
                ->orderBy('no_fak_penj', 'desc')
                ->first();
            $lastnofakpenj = $cekpenjualan != null ? $cekpenjualan->no_fak_penj : '';
            $no_fak_penj = buatkode($lastnofakpenj, $kodecab . "PR" . $bulantrans . substr($tahuntrans, 2, 2), 4);
        } else {
            $no_fak_penj = $request->no_fak_penj; //ok
        }
        //$no_fak_penj = $request->no_fak_penj; //ok
        //dd($no_fak_penj);
        $tgltransaksi = $request->tgltransaksi; //ok
        $id_karyawan = $request->id_karyawan; //ok
        $kode_pelanggan = $request->kode_pelanggan; //ok
        $limitpel = $request->limitpel; //ok
        $sisapiutang = $request->sisapiutang; //ok
        $jenistransaksi = $request->jenistransaksi; //ok
        $jenisbayartunai = $request->jenisbayartunai;
        $jenisbayar = $jenisbayartunai == "transfer" ? $jenisbayartunai : $request->jenisbayar; //ok
        $subtotal = $request->subtotal; //ok
        $jatuhtempo = $request->jatuhtempo; //ok
        $bruto = $request->bruto; //ok
        $nama_pelanggan = $request->nama_pelanggan; //nama_pelanggan
        $id_admin = Auth::user()->id;
        $keterangan = $request->keterangan;
        //Potongan
        $potaida        = str_replace(".", "", $request->potaida);
        if (empty($potaida)) {
            $potaida = 0;
        } else {
            $potaida = $potaida;
        } //ok
        $potswan        = str_replace(".", "", $request->potswan);
        if (empty($potswan)) {
            $potswan = 0;
        } else {
            $potswan = $potswan;
        } //ok
        $potstick       = str_replace(".", "", $request->potstick);
        if (empty($potstick)) {
            $potstick = 0;
        } else {
            $potstick = $potstick;
        } //ok
        $potsp       = str_replace(".", "", $request->potsp);
        if (empty($potsp)) {
            $potsp = 0;
        } else {
            $potsp = $potsp;
        } //ok
        $potsb       = str_replace(".", "", $request->potsb);
        if (empty($potsb)) {
            $potsambal = 0;
        } else {
            $potsambal = $potsb;
        } //ok

        // Voucher
        $voucher       = str_replace(".", "", $request->voucher); //ok
        if (empty($voucher)) {
            $voucher = 0;
        } else {
            $voucher = $voucher;
        }

        // Potongan Istimewa
        $potisaida        = str_replace(".", "", $request->potisaida); //ok
        $potisswan        = str_replace(".", "", $request->potisswan); //ok
        $potisstick       = str_replace(".", "", $request->potisstick); //ok
        if (empty($potisaida)) {
            $potisaida = 0;
        } else {
            $potisaida = $potisaida;
        }
        if (empty($potisswan)) {
            $potisswan = 0;
        } else {
            $potisswan = $potisswan;
        }
        if (empty($potisstick)) {
            $potisstick = 0;
        } else {
            $potisstick = $potisstick;
        }

        //Penyesuaian
        $penyaida        = str_replace(".", "", $request->penyaida); //ok
        $penyswan        = str_replace(".", "", $request->penyswan); //ok
        $penystick       = str_replace(".", "", $request->penystick); //ok
        if (empty($penyaida)) {
            $penyaida = 0;
        } else {
            $penyaida = $penyaida;
        }
        if (empty($penyswan)) {
            $penyswan = 0;
        } else {
            $penyswan = $penyswan;
        }
        if (empty($penystick)) {
            $penystick = 0;
        } else {
            $penystick = $penystick;
        }

        $ppn = str_replace(".", "", $request->ppn); //ok
        if (empty($ppn)) {
            $ppn = 0;
        } else {
            $ppn = $ppn;
        }
        $potongan = $potaida + $potswan + $potstick + $potsp + $potsambal;
        $potistimewa = $potisaida + $potisswan + $potisstick;
        $penyesuaian = $penyaida + $penyswan + $penystick;
        $titipan = str_replace(".", "", $request->titipan); //ok
        $kode_cabang = $request->kode_cabang; //ok
        $tahunini  = date('y');

        //Get No Bukti
        $bayar = DB::table("historibayar")
            ->whereRaw('LEFT(nobukti,6) = "' . $kode_cabang . $tahunini . '-"')
            ->orderBy("nobukti", "desc")
            ->first();
        $lastnobukti = $bayar != null ? $bayar->nobukti : '';
        $nobukti  = buatkode($lastnobukti, $kode_cabang . $tahunini . "-", 6);



        $totalpiutang  = $sisapiutang + $subtotal;
        if ($jenistransaksi == "tunai") {
            $total = $subtotal + $voucher;
            if ($jenisbayartunai == "tunai") {
                $status_lunas = "1";
            } else {
                $status_lunas = "2";
            }
        } else {
            $status_lunas = "2";
            $total = $subtotal;
        }
        if (empty($jatuhtempo)) {
            $jatuhtempo = date("Y-m-d", strtotime("+14 day", strtotime($tgltransaksi)));
        } else {
            $jatuhtempo = date("Y-m-d", strtotime("+$jatuhtempo day", strtotime($tgltransaksi)));
        }

        if (empty($limitpel) and $jenistransaksi == 'kredit' and ($subtotal - $titipan) > 2000000 or !empty($limitpel) and $totalpiutang >= $limitpel and $jenistransaksi == 'kredit') {
            $status = 1; // Pending
        } else {
            $status = "";
        }

        if ($kode_cabang == 'TSM') {
            $akun = "1-1468";
        } else if ($kode_cabang == 'BDG') {
            $akun = "1-1402";
        } else if ($kode_cabang == 'BGR') {
            $akun = "1-1403";
        } else if ($kode_cabang == 'PWT') {
            $akun = "1-1404";
        } else if ($kode_cabang == 'TGL') {
            $akun = "1-1405";
        } else if ($kode_cabang == "SKB") {
            $akun = "1-1407";
        } else if ($kode_cabang == "GRT") {
            $akun = "1-1468";
        } else if ($kode_cabang == "SMR") {
            $akun = "1-1488";
        } else if ($kode_cabang == "SBY") {
            $akun = "1-1486";
        } else if ($kode_cabang == "PST") {
            $akun = "1-1489";
        } else if ($kode_cabang == "KLT") {
            $akun = "1-1490";
        } else if ($kode_cabang == "PWK") {
            $akun = "1-1492";
        } else if ($kode_cabang == "BTN") {
            $akun = "1-1493";
        }

        $tgl_aup    = explode("-", $tgltransaksi);
        $tahun      = substr($tgl_aup[0], 2, 2);
        $bulan      = $tgl_aup[1];

        DB::beginTransaction();
        try {
            DB::table('penjualan')->insert([
                'no_fak_penj' => $no_fak_penj,
                'tgltransaksi' => $tgltransaksi,
                'kode_pelanggan' => $kode_pelanggan,
                'id_karyawan' => $id_karyawan,
                'subtotal' => $bruto,
                'potaida' => $potaida,
                'potswan' => $potswan,
                'potstick' => $potstick,
                'potsp' => $potsp,
                'potsambal' => $potsambal,
                'potongan' => $potongan,
                'potisaida' => $potisaida,
                'potisswan' => $potisswan,
                'potisstick' => $potisstick,
                'potistimewa' => $potistimewa,
                'penyaida' => $penyaida,
                'penyswan' => $penyswan,
                'penystick' => $penystick,
                'penyharga' => $penyesuaian,
                'ppn' => $ppn,
                'total' => $total,
                'jenistransaksi' => $jenistransaksi,
                'jenisbayar' => $jenisbayar,
                'jatuhtempo' => $jatuhtempo,
                'id_admin' => $id_admin,
                'status' => $status,
                'status_lunas' => $status_lunas,
                'keterangan' => $keterangan
            ]);

            $tmp = DB::table('detailpenjualan_temp')->where('id_admin', $id_admin)
                ->select('detailpenjualan_temp.*', 'kode_akun', 'barang.nama_barang', 'barang.harga_dus as harga_dus_standar', 'barang.kode_produk')
                ->join('barang', 'detailpenjualan_temp.kode_barang', '=', 'barang.kode_barang')
                ->join('master_barang', 'barang.kode_produk', '=', 'master_barang.kode_produk')
                ->get();
            foreach ($tmp as $d) {
                // if ($d->harga_dus < $d->harga_dus_standar && $d->promo != 1  && strpos($nama_pelanggan, "KPBN") == false && $kode_cabang != "PST") {
                //     return Redirect::back()->with(['warning' => 'Harga ' . $d->kode_produk . ' Kurang Dari Harga Standar Pola Operasional']);
                // } else {
                DB::table('detailpenjualan')->insert([
                    'no_fak_penj' => $no_fak_penj,
                    'kode_barang' => $d->kode_barang,
                    'harga_dus' => $d->harga_dus,
                    'harga_pack' => $d->harga_pack,
                    'harga_pcs' => $d->harga_pcs,
                    'jumlah' => $d->jumlah,
                    'subtotal' => $d->subtotal,
                    'promo' => $d->promo,
                    'id_admin' => $id_admin
                ]);

                $bukubesar = DB::table("buku_besar")
                    ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
                    ->orderBy("no_bukti", "desc")
                    ->first();
                if ($bukubesar != null) {
                    $lastno_bukti = $bukubesar->no_bukti;
                } else {
                    $lastno_bukti = "";
                }
                $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 6);

                DB::table('buku_besar')
                    ->insert([
                        'no_bukti' => $no_bukti_bukubesar,
                        'tanggal' => $tgltransaksi,
                        'sumber' => 'Penjualan',
                        'keterangan' => "Penjualan " . $d->nama_barang,
                        'kode_akun' => $d->kode_akun,
                        'debet' => $d->subtotal,
                        'kredit' => 0,
                        'nobukti_transaksi' => $no_fak_penj,
                        'no_ref' => $no_fak_penj . $d->kode_barang
                    ]);
                // }
            }

            DB::table('detailpenjualan_temp')->where('id_admin', $id_admin)->delete();
            if ($jenistransaksi == "tunai") {
                if ($jenisbayartunai == "tunai") {
                    $bukubesar = DB::table("buku_besar")
                        ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
                        ->orderBy("no_bukti", "desc")
                        ->first();
                    $lastno_bukti = $bukubesar->no_bukti;
                    $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 6);


                    DB::table('historibayar')
                        ->insert([
                            'nobukti' => $nobukti,
                            'no_fak_penj' => $no_fak_penj,
                            'tglbayar' => $tgltransaksi,
                            'jenistransaksi' => $jenistransaksi,
                            'jenisbayar' => $jenisbayar,
                            'bayar' => $subtotal,
                            'id_admin' => $id_admin,
                            'id_karyawan' => $id_karyawan
                        ]);

                    DB::table('buku_besar')
                        ->insert([
                            'no_bukti' => $no_bukti_bukubesar,
                            'tanggal' => $tgltransaksi,
                            'sumber' => 'Kas Besar',
                            'keterangan' => "Pembayaran Piutang Pelanggan " . $nama_pelanggan,
                            'kode_akun' => $akun,
                            'debet' => $subtotal,
                            'kredit' => 0,
                            'nobukti_transaksi' => $nobukti,
                            'no_ref' => $nobukti
                        ]);
                }
                if (!empty($voucher)) {
                    $nobukti = $jenisbayartunai == "transfer" ? $nobukti : buatkode($nobukti, $kode_cabang . $tahunini . "-", 6);
                    DB::table('historibayar')
                        ->insert([
                            'nobukti' => $nobukti,
                            'no_fak_penj' => $no_fak_penj,
                            'tglbayar' => $tgltransaksi,
                            'jenistransaksi' => $jenistransaksi,
                            'jenisbayar' => $jenisbayar,
                            'bayar' => $voucher,
                            'id_admin' => $id_admin,
                            'ket_voucher' => 2,
                            'status_bayar' => 'voucher',
                            'id_karyawan' => $id_karyawan
                        ]);
                }
            } else {
                if (!empty($titipan)) {
                    $bukubesar = DB::table("buku_besar")
                        ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
                        ->orderBy("no_bukti", "desc")
                        ->first();
                    if ($bukubesar == null) {
                        $lastno_bukti = "GJ" . $bulan . $tahun . "0000";
                    } else {
                        $lastno_bukti = $bukubesar->no_bukti;
                    }

                    $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 6);
                    DB::table('historibayar')
                        ->insert([
                            'nobukti' => $nobukti,
                            'no_fak_penj' => $no_fak_penj,
                            'tglbayar' => $tgltransaksi,
                            'jenistransaksi' => $jenistransaksi,
                            'jenisbayar' => $jenisbayar,
                            'bayar' => $titipan,
                            'id_admin' => $id_admin,
                            'id_karyawan' => $id_karyawan
                        ]);

                    DB::table('buku_besar')
                        ->insert([
                            'no_bukti' => $no_bukti_bukubesar,
                            'tanggal' => $tgltransaksi,
                            'sumber' => 'Kas Besar',
                            'keterangan' => "Pembayaran Piutang Pelanggan " . $nama_pelanggan,
                            'kode_akun' => $akun,
                            'debet' => $titipan,
                            'kredit' => 0,
                            'nobukti_transaksi' => $nobukti,
                            'no_ref' => $nobukti
                        ]);
                }
            }
            DB::commit();
            if (Auth::user()->level == "salesman") {
                return redirect('/penjualan/' . Crypt::encrypt($no_fak_penj) . '/showforsales')->with(['success' => 'Data Penjualan Berhasil di Simpan']);
            } else {
                $cabangpkp = ['TSM', 'BDG', 'PWT', 'BGR'];
                if (in_array(Auth::user()->kode_cabang, $cabangpkp)) {
                    return redirect('/inputpenjualanppn')->with(['success' => 'Data Penjualan Berhasil di Simpan']);
                } else {
                    return redirect('/inputpenjualanv2')->with(['success' => 'Data Penjualan Berhasil di Simpan']);
                }
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/inputpenjualanv2')->with(['warning' => 'Data Penjualan Gagal di Simpan']);
        }
    }

    public function delete($no_fak_penj)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $hb = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj)->get();
        $giro = DB::table('giro')
            ->select('ledger_bank.no_bukti')
            ->leftJoin('ledger_bank', 'giro.no_giro', '=', 'ledger_bank.no_ref')
            ->where('giro.no_fak_penj', $no_fak_penj)
            ->get();

        $transfer = DB::table('transfer')
            ->select('ledger_bank.no_bukti')
            ->leftJoin('ledger_bank', 'transfer.kode_transfer', '=', 'ledger_bank.no_ref')
            ->where('transfer.no_fak_penj', $no_fak_penj)
            ->get();


        if ($giro->isNotEmpty() || $transfer->isNotEmpty()) {
            return Redirect::back()->with(['warning' => 'Data Tidak Dapat Dihapus Karena Memiliki Pembayaran Transfer / Giro Yang Sudah Di Aksi Oleh Keuangan']);
        }


        DB::beginTransaction();
        try {
            DB::table('penjualan')
                ->where('no_fak_penj', $no_fak_penj)
                ->delete();


            $no_ref[] = "";
            foreach ($hb as $d) {
                $no_ref[] = $d->nobukti;
            }

            $no_bukti[] = "";
            foreach ($giro as $d) {
                $no_bukti[] = $d->no_bukti;
            }

            $no_bukti_transfer[] = "";
            foreach ($transfer as $d) {
                $no_bukti_transfer[] = $d->no_bukti;
            }


            DB::table('buku_besar')
                ->whereIn('no_ref', $no_ref)
                ->delete();

            DB::table('buku_besar')
                ->whereIn('no_ref', $no_bukti)
                ->delete();

            DB::table('ledger_bank')
                ->whereIn('no_bukti', $no_bukti)
                ->delete();

            DB::table('buku_besar')
                ->whereIn('no_ref', $no_bukti_transfer)
                ->delete();

            DB::table('ledger_bank')
                ->whereIn('no_bukti', $no_bukti_transfer)
                ->delete();

            DB::table('buku_besar')
                ->where('nobukti_transaksi', $no_fak_penj)
                ->delete();

            // DB::table('buku_besar')
            //     ->leftJoin('historibayar', 'buku_besar.no_ref', '=', 'historibayar.nobukti')
            //     ->where('no_fak_penj', $no_fak_penj)
            //     ->delete();
            DB::commit();
            if (Auth::user()->level == 'salesman') {
                return redirect('/penjualan')->with(['success' => 'Data Berhasil Dihapus']);
            } else {
                return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
    public function cetakfaktur($no_fak_penj)
    {
        $pelangganmp = [
            'TSM-00548',
            'TSM-00493',
            'TSM-02234',
            'TSM-01117',
            'TSM-00494',
            'TSM-00466',
            'PST00007',
            'PST00008',
            'PST00002'
        ];
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $faktur = DB::table('penjualan')
            ->select(
                'penjualan.*',
                'nama_pelanggan',
                'alamat_pelanggan',
                'alamat_toko',
                'nama_cabang',
                'alamat_cabang',
                DB::raw('IFNULL(totalpf,0) - IFNULL(totalgb,0) as totalretur'),
                DB::raw('IFNULL(total,0) - (IFNULL(totalpf,0) - IFNULL(totalgb,0)) as total')
            )
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin(
                DB::raw("(
                    SELECT
                    no_fak_penj,
                    SUM(subtotal_gb) AS totalgb,
                    SUM(subtotal_pf) AS totalpf
                FROM
                    retur
                GROUP BY
                    no_fak_penj
                ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )
            ->where('penjualan.no_fak_penj', $no_fak_penj)
            ->first();
        $detail = DB::table('detailpenjualan')
            ->select('detailpenjualan.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->get();

        //dd($faktur);
        return view('penjualan.laporan.cetakfaktur', compact('faktur', 'detail', 'pelangganmp'));
    }

    public function cetaksuratjalan($no_fak_penj, $type)
    {
        $pelangganmp = [
            'TSM-00548',
            'TSM-00493',
            'TSM-02234',
            'TSM-01117',
            'TSM-00494',
            'TSM-00466',
            'PST00007',
            'PST00008',
            'PST00002'
        ];
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $faktur = DB::table('penjualan')
            ->select(
                'penjualan.*',
                'nama_pelanggan',
                'alamat_pelanggan',
                'alamat_toko',
                'nama_cabang',
                'karyawan.kode_cabang',
                'alamat_cabang',
                'nama_karyawan',
                'kategori_salesman',
                DB::raw('IFNULL(totalpf,0) - IFNULL(totalgb,0) as totalretur'),
                DB::raw('IFNULL(total,0) - (IFNULL(totalpf,0) - IFNULL(totalgb,0)) as total')
            )
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin(
                DB::raw("(
                    SELECT
                    no_fak_penj,
                    SUM(subtotal_gb) AS totalgb,
                    SUM(subtotal_pf) AS totalpf
                FROM
                    retur
                GROUP BY
                    no_fak_penj
                ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )
            ->where('penjualan.no_fak_penj', $no_fak_penj)
            ->first();
        $detail = DB::table('detailpenjualan')
            ->select('detailpenjualan.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->get();
        if ($type == 1) {
            return view('penjualan.laporan.cetaksuratjalan', compact('faktur', 'detail', 'pelangganmp'));
        } else if ($type == 2) {
            return view('penjualan.laporan.cetaksuratjalan2', compact('faktur', 'detail', 'pelangganmp'));
        }
    }

    public function update(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $no_fak_penj_new = $request->no_fak_penj_new;
        $tgltransaksi = $request->tgltransaksi;
        $id_karyawan = $request->id_karyawan;
        $kode_pelanggan = $request->kode_pelanggan;
        $nama_pelanggan = $request->nama_pelanggan;
        $limitpel = $request->limitpel;
        $sisapiutang = $request->sisapiutang;
        $jenistransaksi = $request->jenistransaksi;
        $jenisbayar = $request->jenisbayartunai == "transfer" && $jenistransaksi == "tunai" ? $request->jenisbayartunai : $request->jenisbayar;
        $subtotal = $request->subtotal;
        $jatuhtempo = $request->jatuhtempo;
        $bruto = $request->bruto;
        $id_admin = Auth::user()->id;
        $keterangan = $request->keterangan;
        $ppn = !empty($request->ppn) ? str_replace(".", "", $request->ppn) : 0;

        $potaida        = str_replace(".", "", $request->potaida);
        if (empty($potaida)) {
            $potaida = 0;
        } else {
            $potaida = $potaida;
        }
        $potswan        = str_replace(".", "", $request->potswan);
        if (empty($potswan)) {
            $potswan = 0;
        } else {
            $potswan = $potswan;
        }
        $potstick       = str_replace(".", "", $request->potstick);
        if (empty($potstick)) {
            $potstick = 0;
        } else {
            $potstick = $potstick;
        }
        $potsp       = str_replace(".", "", $request->potsp);
        if (empty($potsp)) {
            $potsp = 0;
        } else {
            $potsp = $potsp;
        }
        $potsb       = str_replace(".", "", $request->potsb);
        if (empty($potsb)) {
            $potsambal = 0;
        } else {
            $potsambal = $potsb;
        }

        // Voucher
        $voucher       = str_replace(".", "", $request->voucher);
        if (empty($voucher)) {
            $voucher = 0;
        } else {
            $voucher = $voucher;
        }

        // Potongan Istimewa
        $potisaida        = str_replace(".", "", $request->potisaida);
        $potisswan        = str_replace(".", "", $request->potisswan);
        $potisstick       = str_replace(".", "", $request->potisstick);
        if (empty($potisaida)) {
            $potisaida = 0;
        } else {
            $potisaida = $potisaida;
        }
        if (empty($potisswan)) {
            $potisswan = 0;
        } else {
            $potisswan = $potisswan;
        }
        if (empty($potisstick)) {
            $potisstick = 0;
        } else {
            $potisstick = $potisstick;
        }

        //Penyesuaian
        $penyaida        = str_replace(".", "", $request->penyaida);
        $penyswan        = str_replace(".", "", $request->penyswan);
        $penystick       = str_replace(".", "", $request->penystick);
        if (empty($penyaida)) {
            $penyaida = 0;
        } else {
            $penyaida = $penyaida;
        }
        if (empty($penyswan)) {
            $penyswan = 0;
        } else {
            $penyswan = $penyswan;
        }
        if (empty($penystick)) {
            $penystick = 0;
        } else {
            $penystick = $penystick;
        }

        $potongan = $potaida + $potswan + $potstick + $potsp + $potsambal;
        $potistimewa = $potisaida + $potisswan + $potisstick;
        $penyesuaian = $penyaida + $penyswan + $penystick;
        $titipan = !empty($request->titipan) ? str_replace(".", "", $request->titipan) : 0;
        $kode_cabang = $request->kode_cabang;
        $tahunini  = date('y');

        //Get No Bukti
        $bayar = DB::table("historibayar")
            ->whereRaw('LEFT(nobukti,6) = "' . $kode_cabang . $tahunini . '-"')
            ->orderBy("nobukti", "desc")
            ->first();
        $lastnobukti = $bayar != null ? $bayar->nobukti : '';
        $nobukti  = buatkode($lastnobukti, $kode_cabang . $tahunini . "-", 6);



        $totalpiutang  = $sisapiutang + $subtotal;
        if ($jenistransaksi == "tunai" && $jenisbayar == "tunai") {
            $total = $subtotal + $voucher;
            $status_lunas = "1";
        } else {
            $status_lunas = "2";
            $total = $subtotal;
        }
        if (empty($jatuhtempo)) {
            $jatuhtempo = date("Y-m-d", strtotime("+14 day", strtotime($tgltransaksi)));
        } else {
            $jatuhtempo = date("Y-m-d", strtotime("+$jatuhtempo day", strtotime($tgltransaksi)));
        }


        if (empty($limitpel) and $jenistransaksi == 'kredit' and ($subtotal - $titipan) > 2000000 or !empty($limitpel) and $totalpiutang >= $limitpel and $jenistransaksi == 'kredit') {
            $status = 1; // Pending
        } else {
            $status = "";
        }

        if ($kode_cabang == 'TSM') {
            $akun = "1-1468";
        } else if ($kode_cabang == 'BDG') {
            $akun = "1-1402";
        } else if ($kode_cabang == 'BGR') {
            $akun = "1-1403";
        } else if ($kode_cabang == 'PWT') {
            $akun = "1-1404";
        } else if ($kode_cabang == 'TGL') {
            $akun = "1-1405";
        } else if ($kode_cabang == "SKB") {
            $akun = "1-1407";
        } else if ($kode_cabang == "GRT") {
            $akun = "1-1468";
        } else if ($kode_cabang == "SMR") {
            $akun = "1-1488";
        } else if ($kode_cabang == "SBY") {
            $akun = "1-1486";
        } else if ($kode_cabang == "PST") {
            $akun = "1-1489";
        } else if ($kode_cabang == "KLT") {
            $akun = "1-1490";
        } else if ($kode_cabang == "PWK") {
            $akun = "1-1492";
        } else if ($kode_cabang == "BTN") {
            $akun = "1-1493";
        }

        $tgl_aup    = explode("-", $tgltransaksi);
        $tahun      = substr($tgl_aup[0], 2, 2);
        $bulan      = $tgl_aup[1];
        // $cek = DB::table('penjualan')->where('tgltransaksi', '2023-01-11')->get();
        // if ($cek->isEmpty()) {
        //     echo "Test";
        // } else {
        //     echo "tost";
        // }
        // dd($cek);
        DB::beginTransaction();
        try {
            DB::table('penjualan')
                ->where('no_fak_penj', $no_fak_penj)
                ->update([
                    'no_fak_penj' => $no_fak_penj_new,
                    'tgltransaksi' => $tgltransaksi,
                    'kode_pelanggan' => $kode_pelanggan,
                    'id_karyawan' => $id_karyawan,
                    'subtotal' => $bruto,
                    'potaida' => $potaida,
                    'potswan' => $potswan,
                    'potstick' => $potstick,
                    'potsp' => $potsp,
                    'potsambal' => $potsambal,
                    'potongan' => $potongan,
                    'potisaida' => $potisaida,
                    'potisswan' => $potisswan,
                    'potisstick' => $potisstick,
                    'potistimewa' => $potistimewa,
                    'penyaida' => $penyaida,
                    'penyswan' => $penyswan,
                    'penystick' => $penystick,
                    'penyharga' => $penyesuaian,
                    'ppn' => $ppn,
                    'total' => $total,
                    'jenistransaksi' => $jenistransaksi,
                    'jenisbayar' => $jenisbayar,
                    'jatuhtempo' => $jatuhtempo,
                    'keterangan' => $keterangan,
                    'status' => $status,
                    'status_lunas' => $status_lunas
                ]);

            $edit = DB::table('detailpenjualan_edit')->where('no_fak_penj', $no_fak_penj)
                ->select('detailpenjualan_edit.*', 'kode_akun', 'barang.nama_barang')
                ->join('barang', 'detailpenjualan_edit.kode_barang', '=', 'barang.kode_barang')
                ->join('master_barang', 'barang.kode_produk', '=', 'master_barang.kode_produk')
                ->get();

            if ($edit->isEmpty() && $nama_pelanggan != 'BATAL') {
                DB::rollBack();
                return Redirect::back()->with(['warning' => 'Data Error']);
            } else {
                DB::table('detailpenjualan')->where('no_fak_penj', $no_fak_penj_new)->delete();
                DB::table('buku_besar')
                    ->where('nobukti_transaksi', $no_fak_penj)
                    ->delete();
            }


            foreach ($edit as $d) {
                DB::table('detailpenjualan')->insert([
                    'no_fak_penj' => $no_fak_penj_new,
                    'kode_barang' => $d->kode_barang,
                    'harga_dus' => $d->harga_dus,
                    'harga_pack' => $d->harga_pack,
                    'harga_pcs' => $d->harga_pcs,
                    'jumlah' => $d->jumlah,
                    'subtotal' => $d->subtotal,
                    'promo' => $d->promo,
                    'id_admin' => $id_admin
                ]);

                $bukubesar = DB::table("buku_besar")
                    ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
                    ->orderBy("no_bukti", "desc")
                    ->first();
                if ($bukubesar == null) {
                    $lastno_bukti = "";
                } else {
                    $lastno_bukti = $bukubesar->no_bukti;
                }
                $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 6);

                DB::table('buku_besar')
                    ->insert([
                        'no_bukti' => $no_bukti_bukubesar,
                        'tanggal' => $tgltransaksi,
                        'sumber' => 'Penjualan',
                        'keterangan' => "Penjualan " . $d->nama_barang,
                        'kode_akun' => $d->kode_akun,
                        'debet' => $d->subtotal,
                        'kredit' => 0,
                        'nobukti_transaksi' => $no_fak_penj,
                        'no_ref' => $no_fak_penj . $d->kode_barang
                    ]);
            }
            DB::table('detailpenjualan_edit')->where('no_fak_penj', $no_fak_penj)->delete();
            if ($jenistransaksi == "tunai" && $jenisbayar == "tunai") {
                $cektransfer = DB::table('transfer')->where('no_fak_penj', $no_fak_penj_new)->where('status', 1)->count();
                $cekgiro = DB::table('giro')->where('no_fak_penj', $no_fak_penj_new)->where('status', 1)->count();

                if ($cektransfer > 0 || $cekgiro > 0) {
                    DB::rollBack();
                    return Redirect::back()->with(['warning' => 'Tidak Bisa di Ubah Karena Ada Transaksi Giro / Transfer Yang Sudah di Aksi Oleh Keuangan, Silahkan Hubungi Bagian Keuangna']);
                }
                $hb = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->get();
                $no_ref[] = "";
                foreach ($hb as $d) {
                    $no_ref[] = $d->nobukti;
                }

                DB::table('buku_besar')
                    ->whereIn('no_ref', $no_ref)
                    ->delete();


                $bukubesar = DB::table("buku_besar")
                    ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
                    ->orderBy("no_bukti", "desc")
                    ->first();
                if ($bukubesar == null) {
                    $lastno_bukti = '';
                } else {
                    $lastno_bukti = $bukubesar->no_bukti;
                }
                $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 6);


                DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->delete();
                DB::table('giro')->where('no_fak_penj', $no_fak_penj_new)->delete();
                DB::table('transfer')->where('no_fak_penj', $no_fak_penj_new)->delete();
                DB::table('historibayar')
                    ->insert([
                        'nobukti' => $nobukti,
                        'no_fak_penj' => $no_fak_penj_new,
                        'tglbayar' => $tgltransaksi,
                        'jenistransaksi' => $jenistransaksi,
                        'jenisbayar' => $jenisbayar,
                        'bayar' => $subtotal,
                        'id_admin' => $id_admin,
                        'id_karyawan' => $id_karyawan
                    ]);

                DB::table('buku_besar')
                    ->insert([
                        'no_bukti' => $no_bukti_bukubesar,
                        'tanggal' => $tgltransaksi,
                        'sumber' => 'Kas Besar',
                        'keterangan' => "Pembayaran Piutang Pelanggan " . $nama_pelanggan,
                        'kode_akun' => $akun,
                        'debet' => $subtotal,
                        'kredit' => 0,
                        'nobukti_transaksi' => $nobukti,
                        'no_ref' => $nobukti
                    ]);
                if (!empty($voucher)) {
                    $nobukti = buatkode($nobukti, $kode_cabang . $tahunini . "-", 6);
                    DB::table('historibayar')
                        ->insert([
                            'nobukti' => $nobukti,
                            'no_fak_penj' => $no_fak_penj_new,
                            'tglbayar' => $tgltransaksi,
                            'jenistransaksi' => $jenistransaksi,
                            'jenisbayar' => $jenisbayar,
                            'bayar' => $voucher,
                            'id_admin' => $id_admin,
                            'ket_voucher' => 2,
                            'status_bayar' => 'voucher',
                            'id_karyawan' => $id_karyawan
                        ]);
                }
            } else if ($jenistransaksi == "tunai" && $jenisbayar == "transfer") {
                $cektransfer = DB::table('transfer')->where('no_fak_penj', $no_fak_penj_new)->where('status', 1)->count();
                $cekgiro = DB::table('giro')->where('no_fak_penj', $no_fak_penj_new)->where('status', 1)->count();

                if ($cektransfer > 0 || $cekgiro > 0) {
                    DB::rollBack();
                    return Redirect::back()->with(['warning' => 'Tidak Bisa di Ubah Karena Ada Transaksi Giro / Transfer Yang Sudah di Aksi Oleh Keuangan, Silahkan Hubungi Bagian Keuangna']);
                }
                $hbtunai = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'tunai')->get();
                $hbtitipan = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'titipan')->where('tglbayar', $tgltransaksi)->first();

                if ($hbtunai != null) {
                    $no_ref_tunai[] = "";
                    foreach ($hbtunai as $d) {
                        $no_ref_tunai[] = $d->nobukti;
                    }

                    DB::table('buku_besar')
                        ->whereIn('no_ref', $no_ref_tunai)
                        ->delete();
                }

                if ($hbtitipan != null) {
                    DB::table('buku_besar')
                        ->where('no_ref', $hbtitipan->nobukti)->delete();
                }
                DB::table('giro')->where('no_fak_penj', $no_fak_penj_new)->delete();
                DB::table('transfer')->where('no_fak_penj', $no_fak_penj_new)->delete();
                DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->delete();

                if (!empty($voucher)) {
                    $nobukti = buatkode($nobukti, $kode_cabang . $tahunini . "-", 6);
                    DB::table('historibayar')
                        ->insert([
                            'nobukti' => $nobukti,
                            'no_fak_penj' => $no_fak_penj_new,
                            'tglbayar' => $tgltransaksi,
                            'jenistransaksi' => $jenistransaksi,
                            'jenisbayar' => $jenisbayar,
                            'bayar' => $voucher,
                            'id_admin' => $id_admin,
                            'ket_voucher' => 2,
                            'status_bayar' => 'voucher',
                            'id_karyawan' => $id_karyawan
                        ]);
                }
            } else {
                $cektransfer = DB::table('transfer')->where('no_fak_penj', $no_fak_penj_new)->where('status', 1)->count();
                $cekgiro = DB::table('giro')->where('no_fak_penj', $no_fak_penj_new)->where('status', 1)->count();

                if ($cektransfer > 0 || $cekgiro > 0) {
                    DB::rollBack();
                    return Redirect::back()->with(['warning' => 'Tidak Bisa di Ubah Karena Ada Transaksi Giro / Transfer Yang Sudah di Aksi Oleh Keuangan, Silahkan Hubungi Bagian Keuangna']);
                }
                $hbtunai = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'tunai')->get();
                $hbtitipan = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'titipan')->where('tglbayar', $tgltransaksi)->first();

                if ($hbtunai != null) {
                    $no_ref_tunai[] = "";
                    foreach ($hbtunai as $d) {
                        $no_ref_tunai[] = $d->nobukti;
                    }

                    DB::table('buku_besar')
                        ->whereIn('no_ref', $no_ref_tunai)
                        ->delete();
                }

                if ($hbtitipan != null) {
                    DB::table('buku_besar')
                        ->where('no_ref', $hbtitipan->nobukti)->delete();
                }




                DB::table('giro')->where('no_fak_penj', $no_fak_penj_new)->delete();
                DB::table('transfer')->where('no_fak_penj', $no_fak_penj_new)->delete();
                DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'tunai')->delete();
                DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'titipan')->where('tglbayar', $tgltransaksi)->delete();
                if (!empty($titipan)) {
                    $bukubesar = DB::table("buku_besar")
                        ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
                        ->orderBy("no_bukti", "desc")
                        ->first();
                    if ($bukubesar == null) {
                        $lastno_bukti = "GJ" . $bulan . $tahun . "0000";
                    } else {
                        $lastno_bukti = $bukubesar->no_bukti;
                    }

                    $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 6);

                    DB::table('historibayar')
                        ->insert([
                            'nobukti' => $nobukti,
                            'no_fak_penj' => $no_fak_penj_new,
                            'tglbayar' => $tgltransaksi,
                            'jenistransaksi' => $jenistransaksi,
                            'jenisbayar' => $jenisbayar,
                            'bayar' => $titipan,
                            'id_admin' => $id_admin,
                            'id_karyawan' => $id_karyawan
                        ]);

                    DB::table('buku_besar')
                        ->insert([
                            'no_bukti' => $no_bukti_bukubesar,
                            'tanggal' => $tgltransaksi,
                            'sumber' => 'Kas Besar',
                            'keterangan' => "Pembayaran Piutang Pelanggan " . $nama_pelanggan,
                            'kode_akun' => $akun,
                            'debet' => $titipan,
                            'kredit' => 0,
                            'nobukti_transaksi' => $nobukti,
                            'no_ref' => $nobukti
                        ]);
                }
            }
            DB::commit();
            if (Auth::user()->level == "salesman") {
                return redirect('/penjualan/' . Crypt::encrypt($no_fak_penj_new) . '/showforsales')->with(['success' => 'Data Penjualan Berhasil di Update']);
            } else {
                return redirect('/penjualan?no_fak_penj=' . $no_fak_penj_new)->with(['success' => 'Data Penjualan Berhasil di Update']);
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/penjualan?no_fak_penj=' . $no_fak_penj_new)->with(['warning' => 'Data Penjualan Gagal di Update']);
        }
    }


    // public function update(Request $request)
    // {
    //     $no_fak_penj = $request->no_fak_penj;
    //     $no_fak_penj_new = $request->no_fak_penj_new;
    //     $tgltransaksi = $request->tgltransaksi;
    //     $id_karyawan = $request->id_karyawan;
    //     $kode_pelanggan = $request->kode_pelanggan;
    //     $nama_pelanggan = $request->nama_pelanggan;
    //     $limitpel = $request->limitpel;
    //     $sisapiutang = $request->sisapiutang;
    //     $jenistransaksi = $request->jenistransaksi;
    //     $jenisbayar = $request->jenisbayartunai == "transfer" ? $request->jenisbayartunai : $request->jenisbayar;
    //     $subtotal = $request->subtotal;
    //     $jatuhtempo = $request->jatuhtempo;
    //     $bruto = $request->bruto;
    //     $id_admin = Auth::user()->id;
    //     //Potongan
    //     $potaida        = str_replace(".", "", $request->potaida);
    //     if (empty($potaida)) {
    //         $potaida = 0;
    //     } else {
    //         $potaida = $potaida;
    //     }
    //     $potswan        = str_replace(".", "", $request->potswan);
    //     if (empty($potswan)) {
    //         $potswan = 0;
    //     } else {
    //         $potswan = $potswan;
    //     }
    //     $potstick       = str_replace(".", "", $request->potstick);
    //     if (empty($potstick)) {
    //         $potstick = 0;
    //     } else {
    //         $potstick = $potstick;
    //     }
    //     $potsp       = str_replace(".", "", $request->potsp);
    //     if (empty($potsp)) {
    //         $potsp = 0;
    //     } else {
    //         $potsp = $potsp;
    //     }
    //     $potsb       = str_replace(".", "", $request->potsb);
    //     if (empty($potsb)) {
    //         $potsambal = 0;
    //     } else {
    //         $potsambal = $potsb;
    //     }

    //     // Voucher
    //     $voucher       = str_replace(".", "", $request->voucher);
    //     if (empty($voucher)) {
    //         $voucher = 0;
    //     } else {
    //         $voucher = $voucher;
    //     }

    //     // Potongan Istimewa
    //     $potisaida        = str_replace(".", "", $request->potisaida);
    //     $potisswan        = str_replace(".", "", $request->potisswan);
    //     $potisstick       = str_replace(".", "", $request->potisstick);
    //     if (empty($potisaida)) {
    //         $potisaida = 0;
    //     } else {
    //         $potisaida = $potisaida;
    //     }
    //     if (empty($potisswan)) {
    //         $potisswan = 0;
    //     } else {
    //         $potisswan = $potisswan;
    //     }
    //     if (empty($potisstick)) {
    //         $potisstick = 0;
    //     } else {
    //         $potisstick = $potisstick;
    //     }

    //     //Penyesuaian
    //     $penyaida        = str_replace(".", "", $request->penyaida);
    //     $penyswan        = str_replace(".", "", $request->penyswan);
    //     $penystick       = str_replace(".", "", $request->penystick);
    //     if (empty($penyaida)) {
    //         $penyaida = 0;
    //     } else {
    //         $penyaida = $penyaida;
    //     }
    //     if (empty($penyswan)) {
    //         $penyswan = 0;
    //     } else {
    //         $penyswan = $penyswan;
    //     }
    //     if (empty($penystick)) {
    //         $penystick = 0;
    //     } else {
    //         $penystick = $penystick;
    //     }

    //     $potongan = $potaida + $potswan + $potstick + $potsp + $potsambal;
    //     $potistimewa = $potisaida + $potisswan + $potisstick;
    //     $penyesuaian = $penyaida + $penyswan + $penystick;
    //     $titipan = str_replace(".", "", $request->titipan);
    //     $kode_cabang = $request->kode_cabang;
    //     $tahunini  = date('y');

    //     //Get No Bukti
    //     $bayar = DB::table("historibayar")
    //         ->whereRaw('LEFT(nobukti,6) = "' . $kode_cabang . $tahunini . '-"')
    //         ->orderBy("nobukti", "desc")
    //         ->first();
    //     $lastnobukti = $bayar->nobukti;
    //     $nobukti  = buatkode($lastnobukti, $kode_cabang . $tahunini . "-", 6);


    //     $totalpiutang  = $sisapiutang + $subtotal;
    //     if ($jenistransaksi == "tunai") {
    //         $total = $subtotal + $voucher;
    //         $status_lunas = "1";
    //     } else {
    //         $status_lunas = "2";
    //         $total = $subtotal;
    //     }
    //     if (empty($jatuhtempo)) {
    //         $jatuhtempo = date("Y-m-d", strtotime("+14 day", strtotime($tgltransaksi)));
    //     } else {
    //         $jatuhtempo = date("Y-m-d", strtotime("+$jatuhtempo day", strtotime($tgltransaksi)));
    //     }

    //     if (empty($limitpel) and $jenistransaksi == 'kredit' and ($subtotal - $titipan) > 2000000 or !empty($limitpel) and $totalpiutang >= $limitpel and $jenistransaksi == 'kredit') {
    //         $status = 1; // Pending
    //     } else {
    //         $status = "";
    //     }

    //     if ($kode_cabang == 'TSM') {
    //         $akun = "1-1468";
    //     } else if ($kode_cabang == 'BDG') {
    //         $akun = "1-1402";
    //     } else if ($kode_cabang == 'BGR') {
    //         $akun = "1-1403";
    //     } else if ($kode_cabang == 'PWT') {
    //         $akun = "1-1404";
    //     } else if ($kode_cabang == 'TGL') {
    //         $akun = "1-1405";
    //     } else if ($kode_cabang == "SKB") {
    //         $akun = "1-1407";
    //     } else if ($kode_cabang == "GRT") {
    //         $akun = "1-1468";
    //     } else if ($kode_cabang == "SMR") {
    //         $akun = "1-1488";
    //     } else if ($kode_cabang == "SBY") {
    //         $akun = "1-1486";
    //     } else if ($kode_cabang == "PST") {
    //         $akun = "1-1489";
    //     } else if ($kode_cabang == "KLT") {
    //         $akun = "1-1490";
    //     }

    //     $tgl_aup    = explode("-", $tgltransaksi);
    //     $tahun      = substr($tgl_aup[0], 2, 2);
    //     $bulan      = $tgl_aup[1];

    //     DB::beginTransaction();
    //     try {
    //         //Update Data Penjualan

    //         DB::table('penjualan')
    //             ->where('no_fak_penj', $no_fak_penj)
    //             ->update([
    //                 'no_fak_penj' => $no_fak_penj_new,
    //                 'tgltransaksi' => $tgltransaksi,
    //                 'kode_pelanggan' => $kode_pelanggan,
    //                 'id_karyawan' => $id_karyawan,
    //                 'subtotal' => $bruto,
    //                 'potaida' => $potaida,
    //                 'potswan' => $potswan,
    //                 'potstick' => $potstick,
    //                 'potsp' => $potsp,
    //                 'potsambal' => $potsambal,
    //                 'potongan' => $potongan,
    //                 'potisaida' => $potisaida,
    //                 'potisswan' => $potisswan,
    //                 'potisstick' => $potisstick,
    //                 'potistimewa' => $potistimewa,
    //                 'penyaida' => $penyaida,
    //                 'penyswan' => $penyswan,
    //                 'penystick' => $penystick,
    //                 'penyharga' => $penyesuaian,
    //                 'total' => $total,
    //                 'jenistransaksi' => $jenistransaksi,
    //                 'jenisbayar' => $jenisbayar,
    //                 'jatuhtempo' => $jatuhtempo,
    //                 'id_admin' => $id_admin,
    //                 'status' => $status,
    //                 'status_lunas' => $status_lunas
    //             ]);

    //         //Hapus Buku Besar


    //         if ($jenistransaksi == "tunai" && $jenisbayar == "tunai") {
    //             $hb = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->get();
    //             $no_ref[] = "";
    //             foreach ($hb as $d) {
    //                 $no_ref[] = $d->nobukti;
    //             }

    //             DB::table('buku_besar')
    //                 ->whereIn('no_ref', $no_ref)
    //                 ->delete();


    //             $bukubesar = DB::table("buku_besar")
    //                 ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
    //                 ->orderBy("no_bukti", "desc")
    //                 ->first();
    //             if ($bukubesar == null) {
    //                 $lastno_bukti = '';
    //             } else {
    //                 $lastno_bukti = $bukubesar->no_bukti;
    //             }
    //             $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 6);


    //             DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->delete();
    //             DB::table('historibayar')
    //                 ->insert([
    //                     'nobukti' => $nobukti,
    //                     'no_fak_penj' => $no_fak_penj_new,
    //                     'tglbayar' => $tgltransaksi,
    //                     'jenistransaksi' => $jenistransaksi,
    //                     'jenisbayar' => $jenisbayar,
    //                     'bayar' => $subtotal,
    //                     'id_admin' => $id_admin,
    //                     'id_karyawan' => $id_karyawan
    //                 ]);

    //             DB::table('buku_besar')
    //                 ->insert([
    //                     'no_bukti' => $no_bukti_bukubesar,
    //                     'tanggal' => $tgltransaksi,
    //                     'sumber' => 'Kas Besar',
    //                     'keterangan' => "Pembayaran Piutang Pelanggan " . $nama_pelanggan,
    //                     'kode_akun' => $akun,
    //                     'debet' => $subtotal,
    //                     'kredit' => 0,
    //                     'nobukti_transaksi' => $nobukti,
    //                     'no_ref' => $nobukti
    //                 ]);
    //             if (!empty($voucher)) {
    //                 $nobukti = buatkode($nobukti, $kode_cabang . $tahunini . "-", 6);
    //                 DB::table('historibayar')
    //                     ->insert([
    //                         'nobukti' => $nobukti,
    //                         'no_fak_penj' => $no_fak_penj_new,
    //                         'tglbayar' => $tgltransaksi,
    //                         'jenistransaksi' => $jenistransaksi,
    //                         'jenisbayar' => $jenisbayar,
    //                         'bayar' => $voucher,
    //                         'id_admin' => $id_admin,
    //                         'ket_voucher' => 2,
    //                         'status_bayar' => 'voucher',
    //                         'id_karyawan' => $id_karyawan
    //                     ]);
    //             }
    //         } else if ($jenistransaksi == "tunai" && $jenisbayar == "transfer") {

    //             $hb = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->get();
    //             $no_ref[] = "";
    //             foreach ($hb as $d) {
    //                 $no_ref[] = $d->nobukti;
    //             }

    //             DB::table('buku_besar')
    //                 ->whereIn('no_ref', $no_ref)
    //                 ->delete();

    //             DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->delete();
    //             if (!empty($voucher)) {
    //                 $nobukti = buatkode($nobukti, $kode_cabang . $tahunini . "-", 6);
    //                 DB::table('historibayar')
    //                     ->insert([
    //                         'nobukti' => $nobukti,
    //                         'no_fak_penj' => $no_fak_penj_new,
    //                         'tglbayar' => $tgltransaksi,
    //                         'jenistransaksi' => $jenistransaksi,
    //                         'jenisbayar' => $jenisbayar,
    //                         'bayar' => $voucher,
    //                         'id_admin' => $id_admin,
    //                         'ket_voucher' => 2,
    //                         'status_bayar' => 'voucher',
    //                         'id_karyawan' => $id_karyawan
    //                     ]);
    //             }
    //         } else {
    //             $hbtunai = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'tunai')->get();
    //             $hbtitipan = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'titipan')->where('tglbayar', $tgltransaksi)->first();

    //             if ($hbtunai != null) {
    //                 $no_ref_tunai[] = "";
    //                 foreach ($hbtunai as $d) {
    //                     $no_ref_tunai[] = $d->nobukti;
    //                 }

    //                 DB::table('buku_besar')
    //                     ->whereIn('no_ref', $no_ref_tunai)
    //                     ->delete();
    //             }

    //             if ($hbtitipan != null) {
    //                 DB::table('buku_besar')
    //                     ->where('no_ref', $hbtitipan->nobukti)->delete();
    //             }





    //             DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'tunai')->delete();
    //             DB::table('historibayar')->where('no_fak_penj', $no_fak_penj_new)->where('jenisbayar', 'titipan')->where('tglbayar', $tgltransaksi)->delete();
    //             if (!empty($titipan)) {
    //                 $bukubesar = DB::table("buku_besar")
    //                     ->whereRaw('LEFT(no_bukti,6) = "GJ' . $bulan . $tahun . '"')
    //                     ->orderBy("no_bukti", "desc")
    //                     ->first();
    //                 if ($bukubesar == null) {
    //                     $lastno_bukti = "GJ" . $bulan . $tahun . "0000";
    //                 } else {
    //                     $lastno_bukti = $bukubesar->no_bukti;
    //                 }

    //                 $no_bukti_bukubesar  = buatkode($lastno_bukti, 'GJ' . $bulan . $tahun, 6);

    //                 DB::table('historibayar')
    //                     ->insert([
    //                         'nobukti' => $nobukti,
    //                         'no_fak_penj' => $no_fak_penj_new,
    //                         'tglbayar' => $tgltransaksi,
    //                         'jenistransaksi' => $jenistransaksi,
    //                         'jenisbayar' => $jenisbayar,
    //                         'bayar' => $titipan,
    //                         'id_admin' => $id_admin,
    //                         'id_karyawan' => $id_karyawan
    //                     ]);

    //                 DB::table('buku_besar')
    //                     ->insert([
    //                         'no_bukti' => $no_bukti_bukubesar,
    //                         'tanggal' => $tgltransaksi,
    //                         'sumber' => 'Kas Besar',
    //                         'keterangan' => "Pembayaran Piutang Pelanggan " . $nama_pelanggan,
    //                         'kode_akun' => $akun,
    //                         'debet' => $titipan,
    //                         'kredit' => 0,
    //                         'nobukti_transaksi' => $nobukti,
    //                         'no_ref' => $nobukti
    //                     ]);
    //             }
    //         }
    //         DB::commit();
    //         return redirect('/penjualan?no_fak_penj=' . $no_fak_penj_new)->with(['success' => 'Data Penjualan Berhasil di Update']);
    //     } catch (\Exception $e) {
    //         dd($e);
    //         DB::rollback();
    //         return redirect('/penjualan?no_fak_penj=' . $no_fak_penj_new)->with(['warning' => 'Data Penjualan Gagal di Update']);
    //     }
    // }

    public function show($no_fak_penj)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $data = DB::table('penjualan')
            ->select(
                'penjualan.*',
                'nama_pelanggan',
                'alamat_pelanggan',
                'alamat_toko',
                'pelanggan.no_hp',
                'latitude',
                'longitude',
                'limitpel',
                'foto',
                'nik',
                'no_kk',
                'tgl_lahir',
                'pasar',
                'hari',
                'cara_pembayaran',
                'status_outlet',
                'type_outlet',
                'lama_usaha',
                'jaminan',
                'lama_langganan',
                'omset_toko',
                'karyawan.nama_karyawan',
                'karyawan.kategori_salesman',
                'karyawan.kode_cabang',
                'nama_cabang',
                'penjualan.keterangan',
                DB::raw('IFNULL(totalpf,0) - IFNULL(totalgb,0) as totalretur'),
                'jmlbayar'
            )
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin(
                DB::raw("(
                    SELECT
                    no_fak_penj,
                    SUM(subtotal_gb) AS totalgb,
                    SUM(subtotal_pf) AS totalpf
                FROM
                    retur
                WHERE no_fak_penj = '$no_fak_penj'
                GROUP BY
                    no_fak_penj
                ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT no_fak_penj, IFNULL(SUM(bayar),0) as jmlbayar
                FROM historibayar
                WHERE no_fak_penj = '$no_fak_penj'
                GROUP BY no_fak_penj
            ) historibayar"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'historibayar.no_fak_penj');
                }
            )
            ->where('penjualan.no_fak_penj', $no_fak_penj)
            ->first();

        $detailpenjualan = DB::table('detailpenjualan')
            ->select('detailpenjualan.*', 'nama_barang', 'isipcsdus', 'isipcs', 'isipack', 'kode_produk')
            ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->get();

        $historibayar = DB::table('historibayar')
            ->join('karyawan', 'historibayar.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('giro', 'historibayar.id_giro', '=', 'giro.id_giro')
            ->where('historibayar.no_fak_penj', $no_fak_penj)
            ->orderBy('tglbayar', 'asc')
            ->get();

        $retur = DB::table('detailretur')
            ->select('detailretur.*', 'tglretur', 'jenis_retur', 'kode_produk', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('retur', 'detailretur.no_retur_penj', '=', 'retur.no_retur_penj')
            ->join('barang', 'detailretur.kode_barang', '=', 'barang.kode_barang')
            ->where('retur.no_fak_penj', $no_fak_penj)
            ->orderBy('retur.no_retur_penj')
            ->get();
        $salesman = DB::table('karyawan')->where('kode_cabang', $data->kode_cabang)->where('status_aktif_sales', 1)->get();
        $girotolak = DB::table('giro')
            ->select('giro.id_giro', 'no_giro')
            ->leftJoin(
                DB::raw("(
                SELECT id_giro,girotocash FROM historibayar WHERE no_fak_penj ='$no_fak_penj'
            ) hb"),
                function ($join) {
                    $join->on('giro.id_giro', '=', 'hb.id_giro');
                }
            )
            ->where('giro.status', 2)
            ->where('giro.no_fak_penj', $no_fak_penj)
            ->get();

        $giro = DB::table('giro')
            ->select('giro.*', 'nama_karyawan', 'tglbayar')
            ->leftJoin('karyawan', 'giro.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin(
                DB::raw("(
                SELECT id_giro,tglbayar
                FROM historibayar
                WHERE no_fak_penj = '$no_fak_penj'
            ) historibayar"),
                function ($join) {
                    $join->on('giro.id_giro', '=', 'historibayar.id_giro');
                }
            )
            ->where('giro.no_fak_penj', $no_fak_penj)
            ->get();

        $transfer = DB::table('transfer')
            ->select('transfer.*', 'nama_karyawan', 'tglbayar')
            ->leftJoin('karyawan', 'transfer.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin(
                DB::raw("(
                SELECT id_transfer,tglbayar
                FROM historibayar
                WHERE no_fak_penj = '$no_fak_penj'
            ) historibayar"),
                function ($join) {
                    $join->on('transfer.id_transfer', '=', 'historibayar.id_transfer');
                }
            )
            ->where('transfer.no_fak_penj', $no_fak_penj)
            ->get();
        return view('penjualan.show', compact('data', 'detailpenjualan', 'retur', 'historibayar', 'salesman', 'girotolak', 'giro', 'transfer'));
    }
    public function rekapcashin(Request $request)
    {
        $dari = $request->tahun . "-" . $request->bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $rekap = DB::table('cabang')
            ->select('cabang.kode_cabang', 'nama_cabang', DB::raw('ifnull(total,0) - ifnull(totalretur,0) as netto'), 'totalbayar')
            ->leftJoin(
                DB::raw("(
                    SELECT karyawan.kode_cabang, SUM(retur.total )AS totalretur
                    FROM retur
                    INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                    WHERE tglretur BETWEEN '$dari'  AND '$sampai'
                    GROUP BY karyawan.kode_cabang
                ) retur"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'retur.kode_cabang');
                }
            )
            ->leftJoin(
                DB::raw("(
                    SELECT karyawan.kode_cabang, SUM(bayar )AS totalbayar
                    FROM historibayar
                    INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN karyawan ON historibayar.id_karyawan = karyawan.id_karyawan
                    WHERE tglbayar BETWEEN '$dari'  AND '$sampai' AND status_bayar IS NULL
                    GROUP BY karyawan.kode_cabang
                ) historibayar"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'historibayar.kode_cabang');
                }
            )
            ->leftJoin(
                DB::raw("(
                    SELECT karyawan.kode_cabang,SUM(total) as total
                    FROM penjualan
                    INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                    WHERE tgltransaksi BETWEEN '$dari'  AND '$sampai'
                    GROUP BY karyawan.kode_cabang
                ) penjualan"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'penjualan.kode_cabang');
                }
            )
            ->get();
        return view('penjualan.dashboard.rekapcashin', compact('rekap'));
    }

    public function aupdashboardall(Request $request)
    {
        $tanggal_aup = $request->tanggal_aup;
        $query = Penjualan::query();
        if ($request->exclude == "yes") {
            $query->where('cabangbarunew', '!=', 'PST');
        }
        $query->select(
            'cabangbarunew as kode_cabang',
            DB::raw("
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 15,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as duaminggu,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 31 AND datediff( '$tanggal_aup', tgltransaksi ) > 15,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as satubulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 46 AND datediff( '$tanggal_aup', tgltransaksi ) > 31,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as satubulan15,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 60 AND datediff( '$tanggal_aup', tgltransaksi ) > 46,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as duabulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 90 AND datediff( '$tanggal_aup', tgltransaksi ) > 60,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as tigabulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 180 AND datediff( '$tanggal_aup', tgltransaksi ) > 90,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as enambulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) > 180,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as lebihenambulan
            ")
        );
        $query->leftJoin(
            DB::raw("(
                SELECT
                    pj.no_fak_penj,
                    IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,
                    karyawan.nama_karyawan AS nama_sales,
                    IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                FROM
                    penjualan pj
                INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                SELECT
                    MAX( id_move ) AS id_move,
                    no_fak_penj,
                    move_faktur.id_karyawan AS salesbaru,
                    karyawan.kode_cabang AS cabangbaru
                FROM
                    move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                WHERE
                    tgl_move <= '$tanggal_aup'
                GROUP BY
                    no_fak_penj,
                    move_faktur.id_karyawan,
                    karyawan.kode_cabang
                ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT no_fak_penj, sum( historibayar.bayar ) AS jmlbayar
		        FROM historibayar WHERE tglbayar <= '$tanggal_aup' GROUP BY no_fak_penj
            ) hblalu"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total
		        FROM retur WHERE tglretur <= '$tanggal_aup' GROUP BY retur.no_fak_penj
            ) retur"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
            }
        );
        $query->where('tgltransaksi', '<=', $tanggal_aup);
        $query->whereRaw('(ifnull( penjualan.total, 0 ) - (ifnull( retur.total, 0 ))) != IFNULL( jmlbayar, 0 )');
        $query->groupBy('cabangbarunew');
        $aup = $query->get();
        return view('penjualan.dashboard.aupall', compact('aup'));
    }

    public function aupdashboardcabang(Request $request)
    {
        $tanggal_aup = $request->tanggal_aup;
        $query = Penjualan::query();
        $query->select(
            'salesbarunew as id_sales',
            'nama_sales',
            DB::raw("
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 15,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as duaminggu,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 31 AND datediff( '$tanggal_aup', tgltransaksi ) > 15,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as satubulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 46 AND datediff( '$tanggal_aup', tgltransaksi ) > 31,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as satubulan15,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 60 AND datediff( '$tanggal_aup', tgltransaksi ) > 46,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as duabulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 90 AND datediff( '$tanggal_aup', tgltransaksi ) > 60,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as tigabulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) <= 180 AND datediff( '$tanggal_aup', tgltransaksi ) > 90,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as enambulan,
            SUM(IF(datediff( '$tanggal_aup', tgltransaksi ) > 180,(IFNULL(penjualan.total,0)-IFNULL(retur.total,0)-IFNULL(jmlbayar,0)),0)) as lebihenambulan
            ")
        );
        $query->leftJoin(
            DB::raw("(
                SELECT
                    pj.no_fak_penj,
                    IF( salesbaru IS NULL, pj.id_karyawan, salesbaru ) AS salesbarunew,
                    karyawan.nama_karyawan AS nama_sales,
                    IF( cabangbaru IS NULL, karyawan.kode_cabang, cabangbaru ) AS cabangbarunew
                FROM
                    penjualan pj
                INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                SELECT
                    MAX( id_move ) AS id_move,
                    no_fak_penj,
                    move_faktur.id_karyawan AS salesbaru,
                    karyawan.kode_cabang AS cabangbaru
                FROM
                    move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                WHERE
                    tgl_move <= '$tanggal_aup'
                GROUP BY
                    no_fak_penj,
                    move_faktur.id_karyawan,
                    karyawan.kode_cabang
                ) move_fak ON ( pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT no_fak_penj, sum( historibayar.bayar ) AS jmlbayar
		        FROM historibayar WHERE tglbayar <= '$tanggal_aup' GROUP BY no_fak_penj
            ) hblalu"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total
		        FROM retur WHERE tglretur <= '$tanggal_aup' GROUP BY retur.no_fak_penj
            ) retur"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
            }
        );
        $query->where('tgltransaksi', '<=', $tanggal_aup);
        $query->where('cabangbarunew', $request->cabang);
        $query->whereRaw('(ifnull( penjualan.total, 0 ) - (ifnull( retur.total, 0 ))) != IFNULL( jmlbayar, 0 )');
        $query->groupBy('salesbarunew');
        $aup = $query->get();

        return view('penjualan.dashboard.aupcabang', compact('aup'));
    }

    function dpppdashboard(Request $request)
    {
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $cabang = $request->cabang;
        $tahunini = $tahun;
        $tahunlalu = $tahun - 1;

        $tgllalu1 = $tahunlalu . "-" . $bulan . "-01";
        $tgllalu2 = date('Y-m-t', strtotime($tgllalu1));

        $tglini1 = $tahunini . "-" . $bulan . "-01";
        $tglini2 = date('Y-m-t', strtotime($tglini1));

        $tglawaltahunlalu = $tahunlalu . "-01-01";
        $tglawaltahunini = $tahunini . "-01-01";
        if (!empty($cabang)) {
            $cbg = "AND karyawan.kode_cabang = '$cabang'";
        } else {
            $cbg = "";
        }

        $statusdpp = $request->status_dpp;
        $query = Barang::query();
        $query->select(
            'master_barang.kode_produk',
            'nama_barang',
            'isipcsdus',
            'realisasi_bulanini_tahunlalu',
            'jmltarget',
            'realisasi_bulanini_tahunini',
            'realisasi_sampaibulanini_tahunlalu',
            'jmltarget_sampaibulanini',
            'realisasi_sampaibulanini_tahunini'
        );
        $query->leftJoin(
            DB::raw("(
                SELECT kt.kode_produk,SUM(jumlah_target) as jmltarget
                FROM komisi_target_qty_detail kt
                INNER JOIN komisi_target ON kt.kode_target = komisi_target.kode_target
                INNER JOIN karyawan ON kt.id_karyawan = karyawan.id_karyawan
                WHERE bulan ='$bulan' AND tahun ='$tahunini'" . $cbg . "
                GROUP BY kt.kode_produk
            ) target"),
            function ($join) {
                $join->on('target.kode_produk', '=', 'master_barang.kode_produk');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT kt.kode_produk,SUM(jumlah_target) as jmltarget_sampaibulanini
                FROM komisi_target_qty_detail kt
                INNER JOIN komisi_target ON kt.kode_target = komisi_target.kode_target
                INNER JOIN karyawan ON kt.id_karyawan = karyawan.id_karyawan
                WHERE bulan BETWEEN '1' AND '$bulan' AND tahun ='$tahunini'" . $cbg . "
                GROUP BY kt.kode_produk
            ) target2"),
            function ($join) {
                $join->on('target2.kode_produk', '=', 'master_barang.kode_produk');
            }
        );


        if ($statusdpp = "2") {
            $query->leftJoin(
                DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_bulanini_tahunlalu
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$tgllalu1' AND '$tgllalu2'" . $cbg . "
                GROUP BY b.kode_produk
            ) dpen"),
                function ($join) {
                    $join->on('dpen.kode_produk', '=', 'master_barang.kode_produk');
                }
            );

            $query->leftJoin(
                DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_bulanini_tahunini
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$tglini1' AND '$tglini2'" . $cbg . "
			GROUP BY b.kode_produk
            ) dpen2"),
                function ($join) {
                    $join->on('dpen2.kode_produk', '=', 'master_barang.kode_produk');
                }
            );

            $query->leftJoin(
                DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_sampaibulanini_tahunlalu
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$tglawaltahunlalu' AND '$tgllalu2'" . $cbg . "
                GROUP BY b.kode_produk
            ) dpen3"),
                function ($join) {
                    $join->on('dpen3.kode_produk', '=', 'master_barang.kode_produk');
                }
            );

            $query->leftJoin(
                DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_sampaibulanini_tahunini
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                WHERE tgltransaksi BETWEEN '$tglawaltahunini' AND '$tglini2'" . $cbg . "
                GROUP BY b.kode_produk
            ) dpen4"),
                function ($join) {
                    $join->on('dpen4.kode_produk', '=', 'master_barang.kode_produk');
                }
            );
        } else {
            $query->leftJoin(
                DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_bulanini_tahunlalu
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT no_fak_penj,max(tglbayar) as lastpayment
                    FROM historibayar
                    GROUP BY no_fak_penj
                ) hb ON (hb.no_fak_penj = p.no_fak_penj)
                WHERE lastpayment BETWEEN '$tgllalu1' AND '$tgllalu2' AND status_lunas='1'" . $cbg . "
                GROUP BY b.kode_produk
            ) dpen"),
                function ($join) {
                    $join->on('dpen.kode_produk', '=', 'master_barang.kode_produk');
                }
            );

            $query->leftJoin(
                DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_bulanini_tahunini
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT no_fak_penj,max(tglbayar) as lastpayment
                    FROM historibayar
                    GROUP BY no_fak_penj
                ) hb ON (hb.no_fak_penj = p.no_fak_penj)
                WHERE lastpayment BETWEEN '$tglini1' AND '$tglini2' AND status_lunas='1'" . $cbg . "
			GROUP BY b.kode_produk
            ) dpen2"),
                function ($join) {
                    $join->on('dpen2.kode_produk', '=', 'master_barang.kode_produk');
                }
            );

            $query->leftJoin(
                DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_sampaibulanini_tahunlalu
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT no_fak_penj,max(tglbayar) as lastpayment
                    FROM historibayar
                    GROUP BY no_fak_penj
                ) hb ON (hb.no_fak_penj = p.no_fak_penj)
                WHERE lastpayment BETWEEN '$tglawaltahunlalu' AND '$tgllalu2' AND status_lunas='1'" . $cbg . "
                GROUP BY b.kode_produk
            ) dpen3"),
                function ($join) {
                    $join->on('dpen3.kode_produk', '=', 'master_barang.kode_produk');
                }
            );

            $query->leftJoin(
                DB::raw("(
                SELECT b.kode_produk,SUM(jumlah) as realisasi_sampaibulanini_tahunini
                FROM detailpenjualan dp
                INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                INNER JOIN penjualan p ON dp.no_fak_penj = p.no_fak_penj
                INNER JOIN karyawan ON p.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT no_fak_penj,max(tglbayar) as lastpayment
                    FROM historibayar
                    GROUP BY no_fak_penj
                ) hb ON (hb.no_fak_penj = p.no_fak_penj)
                WHERE lastpayment BETWEEN '$tglawaltahunini' AND '$tglini2' AND status_lunas='1'" . $cbg . "
                GROUP BY b.kode_produk
            ) dpen4"),
                function ($join) {
                    $join->on('dpen4.kode_produk', '=', 'master_barang.kode_produk');
                }
            );
        }

        $dppp = $query->get();

        return view('penjualan.dashboard.dppp', compact('dppp', 'tahunlalu', 'tahunini'));
    }

    public function laporanpenjualan()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_penjualan', compact('cabang'));
    }

    public function cetaklaporanpenjualan(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->first();
        $jenistransaksi = $request->jenistransaksi;
        $jenislaporan = $request->jenislaporan;
        if (!empty($request->kode_cabang)) {
            if ($jenislaporan == "standar") {
                $query = Penjualan::query();
                $query->selectRaw('penjualan.no_fak_penj AS no_fak_penj,
                penjualan.tgltransaksi AS tgltransaksi,
                penjualan.kode_pelanggan AS kode_pelanggan,
                pelanggan.nama_pelanggan AS nama_pelanggan,
                pelanggan.alamat_pelanggan AS alamat_pelanggan,
                pelanggan.no_hp AS no_hp,
                pelanggan.pasar AS pasar,
                pelanggan.hari AS hari,
                pelanggan.kode_cabang AS kode_cabang,
                penjualan.subtotal AS subtotal,
                penjualan.potongan AS potongan,
                penjualan.potaida as potaida,
                penjualan.potswan as potswan,
                penjualan.potstick as potstick,
                penjualan.potsp as potsp,
                penjualan.potsambal as potsambal,
                penjualan.potistimewa AS potistimewa,
                penjualan.ppn AS ppn,
                penjualan.penyharga AS penyharga,
                users.name,
                date_created,
                date_updated,
                ifnull( penjualan.total, 0 ) AS total,
                ifnull( retur.totalgb, 0 ) AS totalgb,
                ifnull( retur.totalpf, 0 ) AS totalpf,
                (ifnull( retur.totalpf, 0 ) - ifnull( retur.totalgb, 0 ) ) AS totalretur,
                (ifnull( penjualan.total, 0 ) - ( ifnull( retur.totalpf, 0 ) - ifnull( retur.totalgb, 0))) AS totalpiutang,
                penjualan.jenistransaksi AS jenistransaksi,
                penjualan.jenisbayar AS jenisbayar,
                penjualan.id_karyawan AS id_karyawan,
                karyawan.nama_karyawan AS nama_karyawan,
                penjualan.jatuhtempo AS jatuhtempo,
                penjualan.status');
                $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
                $query->leftJoin('users', 'penjualan.id_admin', '=', 'users.id');
                $query->leftJoin(
                    DB::raw("(
                SELECT
                    retur.no_fak_penj AS no_fak_penj,
                    sum(retur.subtotal_gb) AS totalgb,
                    sum(retur.subtotal_pf) AS totalpf
                FROM
                    retur
                WHERE
                    tglretur BETWEEN '$dari' AND '$sampai'
                GROUP BY
                retur.no_fak_penj
                ) retur"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                    }
                );

                $query->whereBetween('tgltransaksi', [$dari, $sampai]);
                if ($request->kode_cabang != "") {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                }

                if ($request->id_karyawan != "") {
                    $query->where('penjualan.id_karyawan', $request->id_karyawan);
                }

                if ($request->kode_pelanggan != "") {
                    $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
                }

                if ($request->jenistransaksi != "") {
                    $query->where('penjualan.jenistransaksi', $request->jenistransaksi);
                }



                if ($request->status != "") {
                    if ($request->status == "pending") {
                        $query->where('penjualan.status', 1);
                    } else if ($request->status == "disetujui") {
                        $query->where('penjualan.status', '!=', 1);
                    }
                }

                $query->groupByRaw('
                penjualan.no_fak_penj,
                tgltransaksi,
                penjualan.kode_pelanggan,
                pelanggan.nama_pelanggan,
                pelanggan.alamat_pelanggan,
                pelanggan.no_hp,
                pelanggan.pasar,
                pelanggan.hari,
                pelanggan.kode_cabang,
                penjualan.subtotal,
                penjualan.potongan,
                penjualan.potongan,
                penjualan.potaida,
                penjualan.potswan,
                penjualan.potstick,
                penjualan.potsp,
                penjualan.potsambal,
                penjualan.potistimewa,
                penjualan.ppn,
                penjualan.penyharga,
                users.name,
                penjualan.jenistransaksi,
                penjualan.jenisbayar,
                penjualan.id_karyawan,
                karyawan.nama_karyawan,
                penjualan.jatuhtempo,
                penjualan.total,
                penjualan.status,
                retur.totalgb,
                retur.totalpf,
                date_created,
                date_updated');
                $query->orderBy('tgltransaksi', 'asc');
                $query->orderBy('no_fak_penj', 'asc');
                $penjualan = $query->get();
                if (isset($_POST['export'])) {
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Laporan Penjualan Periode $dari-$sampai.xls");
                }
                return view('penjualan.laporan.cetak_penjualan', compact('penjualan', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan'));
            } else if ($jenislaporan == "rekapperpelanggan") {
                $query = Penjualan::query();
                $query->selectRaw('penjualan.kode_pelanggan,nama_pelanggan,pasar,hari,penjualan.id_karyawan,nama_karyawan,SUM(subtotal) as totalpenjualan,
                sum(potongan) as totalpotongan,sum(potistimewa) as totalpotonganistimewa,sum(penyharga) as totalpenyharga, SUM(ppn) as ppn, sum(total) as totalpenjualannetto,
                SUM(totretur) as totalretur');
                $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
                $query->leftJoin(
                    DB::raw("(
                        SELECT no_fak_penj,SUM(total) as totretur FROM retur WHERE tglretur
                        BETWEEN '$dari' AND '$sampai' GROUP BY no_fak_penj
                    ) retur"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                    }
                );
                $query->whereBetween('tgltransaksi', [$dari, $sampai]);
                if ($request->kode_cabang != "") {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                }

                if ($request->id_karyawan != "") {
                    $query->where('penjualan.id_karyawan', $request->id_karyawan);
                }

                if ($request->kode_pelanggan != "") {
                    $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
                }

                if ($request->jenistransaksi != "") {
                    $query->where('penjualan.jenistransaksi', $request->jenistransaksi);
                }



                if ($request->status != "") {
                    if ($request->status == "pending") {
                        $query->where('penjualan.status', 1);
                    } else if ($request->status == "disetujui") {
                        $query->where('penjualan.status', '!=', 1);
                    }
                }
                $query->groupByRaw('
                penjualan.kode_pelanggan,
                nama_pelanggan,
                pasar,
                Hari,
                penjualan.id_karyawan,
                nama_karyawan');
                $penjualan = $query->get();
                if (isset($_POST['export'])) {
                    $time = date("H:i:s");
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Laporan Penjualan Rekap Per Pelanggan Periode $dari-$sampai-$time.xls");
                }
                return view('penjualan.laporan.cetak_penjualan_rekapperpelanggan', compact('penjualan', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan'));
            } else if ($jenislaporan == "formatsatubaris") {
                $query = Penjualan::query();
                $query->selectRaw('penjualan.no_fak_penj,
                tgltransaksi,
                penjualan.kode_pelanggan,pelanggan.nama_pelanggan,
                penjualan.id_karyawan,karyawan.nama_karyawan,
                pelanggan.pasar,pelanggan.hari,
                AB,AR,`AS`,BB,CG,CGG,DEP,DK,DS,SP,BBP,SPP,CG5,SC,SP8,SP8P,SP500,
                penjualan.subtotal as totalbruto,
                (ifnull( r.totalpf, 0 ) - ifnull( r.totalgb, 0 ) ) AS totalretur,
                penjualan.penyharga AS penyharga,
                penjualan.potaida as potaida,
                penjualan.potswan as potswan,
                penjualan.potstick as potstick,
                penjualan.potsp as potsp,
                penjualan.potongan as potongan,
                penjualan.potistimewa,
                penjualan.subtotal,
                (ifnull( penjualan.total, 0 ) - ( ifnull( r.totalpf, 0 ) - ifnull( r.totalgb, 0))) as
                totalnetto, ppn,
                totalbayar,
                penjualan.jenistransaksi,
                penjualan.status_lunas,
                lastpayment');
                $query->leftJoin(
                    DB::raw("(
                    SELECT dp.no_fak_penj,
                    SUM(IF(kode_produk = 'AB',jumlah,0)) as AB,
                    SUM(IF(kode_produk = 'AR',jumlah,0)) as AR,
                    SUM(IF(kode_produk = 'AS',jumlah,0)) as `AS`,
                    SUM(IF(kode_produk = 'BB',jumlah,0)) as BB,
                    SUM(IF(kode_produk = 'CG',jumlah,0)) as CG,
                    SUM(IF(kode_produk = 'CGG',jumlah,0)) as CGG,
                    SUM(IF(kode_produk = 'DEP',jumlah,0)) as DEP,
                    SUM(IF(kode_produk = 'DK',jumlah,0)) as DK,
                    SUM(IF(kode_produk = 'DS',jumlah,0)) as DS,
                    SUM(IF(kode_produk = 'SP',jumlah,0)) as SP,
                    SUM(IF(kode_produk = 'BBP',jumlah,0)) as BBP,
                    SUM(IF(kode_produk = 'SPP',jumlah,0)) as SPP,
                    SUM(IF(kode_produk = 'CG5',jumlah,0)) as CG5,
                    SUM(IF(kode_produk = 'SC',jumlah,0)) as SC,
                    SUM(IF(kode_produk = 'SP8',jumlah,0)) as SP8,
                    SUM(IF(kode_produk = 'SP8-P',jumlah,0)) as SP8P,
                    SUM(IF(kode_produk = 'SP500',jumlah,0)) as SP500
                    FROM detailpenjualan dp
                    INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                    GROUP BY dp.no_fak_penj
                    ) dp"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'dp.no_fak_penj');
                    }
                );

                $query->leftJoin(
                    DB::raw("(
                    SELECT hb.no_fak_penj,
                    MAX(tglbayar) as lastpayment,
                    SUM(bayar) as totalbayar
                    FROM historibayar hb
                    GROUP BY hb.no_fak_penj
                    ) hb"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'hb.no_fak_penj');
                    }
                );

                $query->leftJoin(
                    DB::raw("(
                        SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        sum(retur.subtotal_gb) AS totalgb,
                        sum(retur.subtotal_pf) AS totalpf
                        FROM
                            retur
                        WHERE
                            tglretur
                        GROUP BY
                            retur.no_fak_penj
                    ) r"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'r.no_fak_penj');
                    }
                );

                $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
                $query->whereBetween('tgltransaksi', [$dari, $sampai]);
                if ($request->kode_cabang != "") {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                }
                if ($request->id_karyawan != "") {
                    $query->where('penjualan.id_karyawan', $request->id_karyawan);
                }

                if ($request->kode_pelanggan != "") {
                    $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
                }

                if ($request->jenistransaksi != "") {
                    $query->where('penjualan.jenistransaksi', $request->jenistransaksi);
                }

                if ($request->status != "") {
                    if ($request->status == "pending") {
                        $query->where('penjualan.status', 1);
                    } else if ($request->status == "disetujui") {
                        $query->where('penjualan.status', '!=', 1);
                    }
                }
                $query->orderBy('tgltransaksi', 'asc');
                $query->orderBy('penjualan.no_fak_penj', 'asc');
                $penjualan = $query->get();

                $barang = Barang::all();

                if (isset($_POST['export'])) {
                    $time = date("H:i:s");
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Laporan Penjualan Format Satu Baris $dari-$sampai-$time.xls");
                }
                return view('penjualan.laporan.cetak_penjualan_formatsatubaris', compact('penjualan', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan', 'barang'));
            } else if ($jenislaporan == "komisi") {
                $query = Penjualan::query();
                $query->selectRaw('penjualan.no_fak_penj,
                tgltransaksi,
                penjualan.kode_pelanggan,pelanggan.nama_pelanggan,
                penjualan.id_karyawan,karyawan.nama_karyawan,
                pelanggan.pasar,pelanggan.hari,
                AB,AR,`AS`,BB,CG,CGG,DEP,DK,DS,SP,BBP,SPP,CG5,SC,SP8,SP500,
                retur_AB,retur_AR,`retur_AS`,retur_BB,retur_CG,retur_CGG,retur_DEP,retur_DK,retur_DS,retur_SP,retur_BBP,retur_SPP,retur_CG5,retur_SC,retur_SP8,retur_SP500,
                penjualan.subtotal as totalbruto,
                (ifnull( r.totalpf, 0 ) - ifnull( r.totalgb, 0 ) ) AS totalretur,
                penjualan.penyharga AS penyharga,
                penjualan.potaida as potaida,
                penjualan.potswan as potswan,
                penjualan.potstick as potstick,
                penjualan.potsp as potsp,
                penjualan.potongan as potongan,
                penjualan.potistimewa,
                penjualan.subtotal,
                penjualan.ppn,
                (ifnull( penjualan.total, 0 ) - ( ifnull( r.totalpf, 0 ) - ifnull( r.totalgb, 0))) as totalnetto,
                totalbayar,
                penjualan.jenistransaksi,
                penjualan.status_lunas,
                lastpayment');
                $query->leftJoin(
                    DB::raw("(
                    SELECT dp.no_fak_penj,
                    SUM(IF(kode_produk = 'AB' AND promo != 1 OR kode_produk ='AB' AND promo IS NULL,jumlah,0)) as AB,
                    SUM(IF(kode_produk = 'AR' AND promo != 1 OR kode_produk ='AR' AND promo IS NULL,jumlah,0)) as AR,
                    SUM(IF(kode_produk = 'AS' AND promo != 1 OR kode_produk ='AS' AND promo IS NULL,jumlah,0)) as `AS`,
                    SUM(IF(kode_produk = 'BB' AND promo != 1 OR kode_produk ='BB' AND promo IS NULL,jumlah,0)) as BB,
                    SUM(IF(kode_produk = 'CG' AND promo != 1 OR kode_produk ='CG' AND promo IS NULL,jumlah,0)) as CG,
                    SUM(IF(kode_produk = 'CGG' AND promo != 1 OR kode_produk ='CGG' AND promo IS NULL,jumlah,0)) as CGG,
                    SUM(IF(kode_produk = 'DEP' AND promo != 1 OR kode_produk ='DEP' AND promo IS NULL,jumlah,0)) as DEP,
                    SUM(IF(kode_produk = 'DK' AND promo != 1 OR kode_produk ='DK' AND promo IS NULL,jumlah,0)) as DK,
                    SUM(IF(kode_produk = 'DS' AND promo != 1 OR kode_produk ='DS' AND promo IS NULL,jumlah,0)) as DS,
                    SUM(IF(kode_produk = 'SP' AND promo != 1 OR kode_produk ='SP' AND promo IS NULL,jumlah,0)) as SP,
                    SUM(IF(kode_produk = 'BBP' AND promo != 1 OR kode_produk ='BBP' AND promo IS NULL,jumlah,0)) as BBP,
                    SUM(IF(kode_produk = 'SPP' AND promo != 1 OR kode_produk ='SPP' AND promo IS NULL,jumlah,0)) as SPP,
                    SUM(IF(kode_produk = 'CG5' AND promo != 1 OR kode_produk ='CG5' AND promo IS NULL,jumlah,0)) as CG5,
                    SUM(IF(kode_produk = 'SC' AND promo != 1 OR kode_produk ='SC' AND promo IS NULL,jumlah,0)) as SC,
                    SUM(IF(kode_produk = 'SP8' AND promo != 1 OR kode_produk ='SP8' AND promo IS NULL,jumlah,0)) as SP8,
                    SUM(IF(kode_produk = 'SP500' AND promo != 1 OR kode_produk ='SP500' AND promo IS NULL,jumlah,0)) as SP500
                    FROM detailpenjualan dp
                    INNER JOIN barang b ON dp.kode_barang = b.kode_barang
                    GROUP BY dp.no_fak_penj
                    ) dp"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'dp.no_fak_penj');
                    }
                );

                $query->leftJoin(
                    DB::raw("(
                        SELECT retur.no_fak_penj,
                        SUM(IF(kode_produk = 'AB',jumlah,0)) as retur_AB,
                        SUM(IF(kode_produk = 'AR',jumlah,0)) as retur_AR,
                        SUM(IF(kode_produk = 'AS',jumlah,0)) as `retur_AS`,
                        SUM(IF(kode_produk = 'BB',jumlah,0)) as retur_BB,
                        SUM(IF(kode_produk = 'CG',jumlah,0)) as retur_CG,
                        SUM(IF(kode_produk = 'CGG',jumlah,0)) as retur_CGG,
                        SUM(IF(kode_produk = 'DEP',jumlah,0)) as retur_DEP,
                        SUM(IF(kode_produk = 'DK',jumlah,0)) as retur_DK,
                        SUM(IF(kode_produk = 'DS',jumlah,0)) as retur_DS,
                        SUM(IF(kode_produk = 'SP',jumlah,0)) as retur_SP,
                        SUM(IF(kode_produk = 'BBP',jumlah,0)) as retur_BBP,
                        SUM(IF(kode_produk = 'SPP' ,jumlah,0)) as retur_SPP,
                        SUM(IF(kode_produk = 'CG5',jumlah,0)) as retur_CG5,
                        SUM(IF(kode_produk = 'SC',jumlah,0)) as retur_SC,
                        SUM(IF(kode_produk = 'SP8',jumlah,0)) as retur_SP8,
                        SUM(IF(kode_produk = 'SP500',jumlah,0)) as retur_SP500
                        FROM detailretur dr
                        INNER JOIN retur ON dr.no_retur_penj = retur.no_retur_penj
                        INNER JOIN barang b ON dr.kode_barang = b.kode_barang
                        WHERE retur.jenis_retur = 'pf'
                        GROUP BY retur.no_fak_penj
                    ) returpf"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'returpf.no_fak_penj');
                    }
                );

                $query->leftJoin(
                    DB::raw("(
                    SELECT hb.no_fak_penj,
                    MAX(tglbayar) as lastpayment,
                    SUM(bayar) as totalbayar
                    FROM historibayar hb
                    GROUP BY hb.no_fak_penj
                    ) hb"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'hb.no_fak_penj');
                    }
                );

                $query->leftJoin(
                    DB::raw("(
                        SELECT
                        retur.no_fak_penj AS no_fak_penj,
                        sum(retur.subtotal_gb) AS totalgb,
                        sum(retur.subtotal_pf) AS totalpf
                        FROM
                            retur
                        WHERE
                            tglretur
                        GROUP BY
                            retur.no_fak_penj
                    ) r"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'r.no_fak_penj');
                    }
                );

                $query->leftJoin(
                    DB::raw("(
                        SELECT MAX(id_move) as id_move,no_fak_penj,move_faktur.id_karyawan as salesbaru
                        FROM move_faktur
                        WHERE tgl_move <= '$sampai'
                        GROUP BY no_fak_penj,move_faktur.id_karyawan
                    ) move_fak"),
                    function ($join) {
                        $join->on('penjualan.no_fak_penj', '=', 'move_fak.no_fak_penj');
                    }
                );

                $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
                $query->join('karyawan', DB::raw("IFNULL(salesbaru,penjualan.id_karyawan)"), '=', 'karyawan.id_karyawan');

                if ($request->kode_cabang != "") {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                }
                if ($request->id_karyawan != "") {
                    $query->whereRaw("IFNULL(salesbaru,penjualan.id_karyawan)='$request->id_karyawan'");
                }

                if ($request->kode_pelanggan != "") {
                    $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
                }

                if ($request->jenistransaksi != "") {
                    $query->where('penjualan.jenistransaksi', $request->jenistransaksi);
                }

                if ($request->status != "") {
                    if ($request->status == "pending") {
                        $query->where('penjualan.status', 1);
                    } else if ($request->status == "disetujui") {
                        $query->where('penjualan.status', '!=', 1);
                    }
                }

                $query->whereBetween('lastpayment', [$dari, $sampai]);
                $query->where('penjualan.status_lunas', 1);
                $query->orderBy('tgltransaksi', 'asc');
                $query->orderBy('penjualan.no_fak_penj', 'asc');
                $penjualan = $query->get();

                $barang = Barang::all();

                if (isset($_POST['export'])) {
                    $time = date("H:i:s");
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Laporan Penjualan Format Komisi Periode $dari-$sampai-$time.xls");
                }
                return view('penjualan.laporan.cetak_penjualan_formatkomisi', compact('penjualan', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan', 'barang'));
            }
        } else {
            $query = Penjualan::query();
            $query->selectRaw('karyawan.kode_cabang AS kode_cabang,nama_cabang,
            (ifnull( SUM(penjualan.subtotal), 0 )
            ) AS totalbruto, totalretur,ifnull( SUM(penjualan.penyharga), 0 )  as totalpenyharga,
            ifnull( SUM(penjualan.potongan), 0 )  as totalpotongan,
            ifnull( SUM(penjualan.potistimewa), 0 )  as totalpotistimewa');
            $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
            $query->leftJoin(
                DB::raw("(
                SELECT karyawan.kode_cabang, SUM(retur.total )AS totalretur FROM retur
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                WHERE tglretur BETWEEN '$dari' AND '$sampai' GROUP BY karyawan.kode_cabang
                ) retur"),
                function ($join) {
                    $join->on('karyawan.kode_cabang', '=', 'retur.kode_cabang');
                }
            );

            $query->whereBetween('tgltransaksi', [$dari, $sampai]);
            if ($request->jenistransaksi != "") {
                $query->where('penjualan.jenistransaksi', $request->jenistransaksi);
            }
            if ($request->status != "") {
                if ($request->status == "pending") {
                    $query->where('penjualan.status', 1);
                } else if ($request->status == "disetujui") {
                    $query->where('penjualan.status', '!=', 1);
                }
            }
            $query->groupByRaw(' karyawan.kode_cabang,nama_cabang,totalretur');
            $penjualan = $query->get();
            if (isset($_POST['export'])) {
                $time = date("H:i:s");
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Rekap Penjualan All Cabang $dari-$sampai-$time.xls");
            }
            return view('penjualan.laporan.cetak_penjualan_rekapallcabang', compact('penjualan', 'dari', 'sampai'));
        }
    }

    public function laporantunaikredit()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_tunaikredit', compact('cabang'));
    }


    public function cetaklaporantunaikredit(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();
        $id_karyawan = $request->id_karyawan;
        if ($request->kode_cabang     != "") {
            $kode_cabang = "AND karyawan.kode_cabang = '" . $request->kode_cabang . "' ";
        } else {
            $kode_cabang = "";
        }

        if ($request->id_karyawan != "") {
            $id_karyawan = "AND penjualan.id_karyawan = '" . $request->id_karyawan . "' ";
        } else {
            $id_karyawan = "";
        }
        $query = Barang::query();
        $query->selectRaw('master_barang.kode_produk,nama_barang,isipcsdus,satuan,isipack,isipcs,
        jumlah_tunai,totaljual_tunai,jumlah_kredit,totaljual_kredit,jumlah_tunai + jumlah_kredit as jumlah,
        totaljual_tunai + totaljual_kredit as totaljual');
        $query->leftJoin(
            DB::raw("(
                SELECT kode_produk,
                SUM( IF ( jenistransaksi ='tunai', detailpenjualan.jumlah, 0 ) ) AS jumlah_tunai,
                SUM( IF ( jenistransaksi ='tunai', detailpenjualan.subtotal, 0 ) ) AS totaljual_tunai,
                SUM( IF ( jenistransaksi ='kredit', detailpenjualan.jumlah, 0 ) ) AS jumlah_kredit,
                SUM( IF ( jenistransaksi ='kredit', detailpenjualan.subtotal, 0 ) ) AS totaljual_kredit
                FROM detailpenjualan
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai' AND promo !='1' "
                . $kode_cabang
                . $id_karyawan
                .
                " OR  tgltransaksi BETWEEN '$dari' AND '$sampai' AND promo IS NULL "
                . $kode_cabang
                . $id_karyawan
                . "
                GROUP BY kode_produk
            ) detailpenjualan"),
            function ($join) {
                $join->on('master_barang.kode_produk', '=', 'detailpenjualan.kode_produk');
            }
        );
        $query->orderBy('master_barang.kode_produk', 'asc');
        $tunaikredit = $query->get();

        $queryretur = Retur::query();
        $queryretur->selectRaw('SUM( IF ( jenistransaksi ="tunai", retur.total, 0 ) ) AS totalretur_tunai,
        SUM( IF ( jenistransaksi ="kredit", retur.total, 0 ) ) AS totalretur_kredit');
        $queryretur->join('penjualan', 'retur.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $queryretur->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $queryretur->whereBetween('tglretur', [$dari, $sampai]);
        if (!empty($request->kode_cabang)) {
            $queryretur->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->id_karyawan)) {
            $queryretur->where('penjualan.id_karyawan', $request->id_karyawan);
        }
        $retur = $queryretur->first();


        $querypotongan = Penjualan::query();
        $querypotongan->selectRaw("SUM( IF ( jenistransaksi ='tunai', penyharga, 0 ) ) AS totpenyharga_tunai,
        SUM( IF ( jenistransaksi ='kredit', penyharga, 0 ) ) AS totpenyharga_kredit,
        SUM( IF ( jenistransaksi ='tunai', potongan, 0 ) ) AS totpotongan_tunai,
        SUM( IF ( jenistransaksi ='kredit', potongan, 0 ) ) AS totpotongan_kredit,
        SUM( IF ( jenistransaksi ='tunai', potistimewa, 0 ) ) AS totpotistimewa_tunai,
        SUM( IF ( jenistransaksi ='kredit', potistimewa, 0 ) ) AS totpotistimewa_kredit,
        SUM( IF ( jenistransaksi ='tunai', ppn, 0 ) ) AS ppn_tunai,
        SUM( IF ( jenistransaksi ='kredit', ppn, 0 ) ) AS ppn_kredit");
        $querypotongan->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        if (!empty($request->kode_cabang)) {
            $querypotongan->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->id_karyawan)) {
            $querypotongan->where('penjualan.id_karyawan', $request->id_karyawan);
        }
        $querypotongan->whereBetween('tgltransaksi', [$dari, $sampai]);
        $potongan = $querypotongan->first();

        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Tunai Kredit Periode $dari-$sampai-$time.xls");
        }
        return view('penjualan.laporan.cetak_tunaikredit', compact('tunaikredit', 'salesman', 'cabang', 'dari', 'sampai', 'retur', 'potongan'));
    }

    public function laporankartupiutang()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_kartupiutang', compact('cabang'));
    }

    public function laporantandaterimafaktur()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_tandaterimafaktur', compact('cabang'));
    }
    public function cetaklaporankartupiutang(Request $request)
    {
        $no_faktur = $request->no_faktur;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $ljt = $request->ljt;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->first();
        $query = Penjualan::query();
        $query->selectRaw("penjualan.no_fak_penj AS no_fak_penj,
        penjualan.tgltransaksi AS tgltransaksi,
        datediff('$sampai', penjualan.tgltransaksi) as usiapiutang,
        penjualan.kode_pelanggan AS kode_pelanggan,
        pelanggan.nama_pelanggan AS nama_pelanggan,
        pelanggan.alamat_pelanggan AS alamat_pelanggan,
        pelanggan.jatuhtempo AS jatuhtempopel,
        pelanggan.no_hp AS no_hp,
        pelanggan.pasar AS pasar,
        pelanggan.hari AS hari,
        pelanggan.jatuhtempo AS jatuhtempo,
        pelanggan.kode_cabang AS kode_cabang,
        penjualan.total AS total,
        penjualan.jenistransaksi AS jenistransaksi,
        penjualan.jenisbayar AS jenisbayar,
        penjualan.id_karyawan AS id_karyawan,
        penjualan.status,
        salesbarunew,
        karyawan.nama_karyawan AS nama_karyawan,
        IFNULL(penjbulanini.subtotal,0) AS subtotal,
        IFNULL(penjbulanini.penyharga,0) AS penyharga,
        IFNULL(penjbulanini.potongan,0) AS potongan,
        IFNULL(penjbulanini.potistimewa,0) AS potistimewa,
        IFNULL(penjbulanini.ppn,0) AS ppn,
        (IFNULL(totalpf,0)-IFNULL(totalgb,0)) AS totalretur,
        IFNULL(penjbulanini.total,0) -(IFNULL(totalpf,0)-IFNULL(totalgb,0))  AS piutangbulanini,
        (ifnull(penjualan.total,0) - (ifnull(totalpf_last,0)-ifnull(totalgb_last,0))) AS totalpiutang,
        ifnull(bayarsebelumbulanini,0) AS bayarsebelumbulanini,lastpayment,
        ifnull(bayarbulanini,0) AS bayarbulanini");
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');


        $query->leftJoin(
            DB::raw("(
            SELECT no_fak_penj,subtotal,penyharga,potongan,potistimewa,ppn,total
            FROM penjualan
            WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
            ) penjbulanini"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'penjbulanini.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT pj.no_fak_penj,
                IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                FROM penjualan pj
                INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                LEFT JOIN (
                    SELECT
                    id_move,no_fak_penj,
                    move_faktur.id_karyawan as salesbaru,
                    karyawan.kode_cabang  as cabangbaru
                    FROM move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                    WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
                ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
            }
        );
        $query->join('karyawan', 'pjmove.salesbarunew', '=', 'karyawan.id_karyawan');
        $query->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj,
                sum(retur.subtotal_gb) AS totalgb,
                sum(retur.subtotal_pf) AS totalpf
                FROM
                retur
                WHERE tglretur BETWEEN  '$dari' AND '$sampai'
                GROUP BY
                retur.no_fak_penj
            ) returbulanini"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'returbulanini.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT no_fak_penj,sum( historibayar.bayar ) AS bayarsebelumbulanini
                FROM historibayar
                WHERE tglbayar < '$dari'
                GROUP BY no_fak_penj
            ) hblalu"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT no_fak_penj,sum( historibayar.bayar ) AS bayarbulanini
                FROM historibayar
                WHERE tglbayar BETWEEN  '$dari' AND '$sampai'
                GROUP BY no_fak_penj
            ) hbskrg"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hbskrg.no_fak_penj');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj,
                SUM(retur.subtotal_gb) AS totalgb_last,
                SUM(retur.subtotal_pf) AS totalpf_last
                FROM
                    retur
                WHERE tglretur < '$dari'
                GROUP BY
                retur.no_fak_penj
            ) r"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'r.no_fak_penj');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT hb.no_fak_penj,
            MAX(tglbayar) as lastpayment,
            SUM(bayar) as totalbayar
            FROM historibayar hb WHERE tglbayar BETWEEN '$dari' AND '$sampai'
            GROUP BY hb.no_fak_penj
            ) hblast"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hblast.no_fak_penj');
            }
        );

        $query->where('penjualan.jenistransaksi', '!=', 'tunai');
        $query->where('tgltransaksi', '<=', $sampai);
        // $query->whereRaw('(ifnull(penjualan.total,0) - (ifnull(totalpf_last,0)-ifnull(totalgb_last,0))) - IFNULL(bayarsebelumbulanini,0) > 0');
        $query->whereRaw('(ifnull(penjualan.total,0) - (ifnull(totalpf_last,0)-ifnull(totalgb_last,0))) != IFNULL(bayarsebelumbulanini,0)');
        if ($request->kode_cabang != "") {
            $query->where('cabangbarunew', $request->kode_cabang);
        }
        if ($request->id_karyawan != "") {
            $query->where('salesbarunew', $request->id_karyawan);
        }

        if ($request->kode_pelanggan != "") {
            $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
        }
        if ($ljt == 1) {
            $query->whereRaw("datediff('$sampai', penjualan.tgltransaksi) <= pelanggan.jatuhtempo");
        } else if ($ljt == 2) {
            $query->whereRaw("datediff('$sampai', penjualan.tgltransaksi) > 15");
        }

        if (isset($_POST['tandaterimafaktur'])) {
            $query->whereIn('penjualan.no_fak_penj', $no_faktur);
        }
        $query->orWhere('penjualan.jenistransaksi', '!=', 'tunai');
        $query->where('tgltransaksi', '<=', $sampai);
        $query->whereRaw('IFNULL(bayarbulanini,0) != 0');
        if ($request->kode_cabang != "") {
            $query->where('cabangbarunew', $request->kode_cabang);
        }
        if ($request->id_karyawan != "") {
            $query->where('salesbarunew', $request->id_karyawan);
        }

        if ($request->kode_pelanggan != "") {
            $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
        }
        if ($ljt == 1) {
            $query->whereRaw("datediff('$sampai', penjualan.tgltransaksi) <= pelanggan.jatuhtempo");
        } else if ($ljt == 2) {
            $query->whereRaw("datediff('$sampai', penjualan.tgltransaksi) > 15");
        }

        if (isset($_POST['tandaterimafaktur'])) {
            $query->whereIn('penjualan.no_fak_penj', $no_faktur);
        }

        $query->orderBy('tgltransaksi');
        $query->orderBy('penjualan.no_fak_penj');
        $kartupiutang = $query->get();

        // $query->selectRaw("penjualan.no_fak_penj,
        // tgltransaksi,
        // datediff('$sampai', penjualan.tgltransaksi) as usiapiutang,
        // penjualan.kode_pelanggan,
        // nama_pelanggan,
        // alamat_pelanggan,
        // pelanggan.jatuhtempo,
        // pelanggan.no_hp,
        // pasar,
        // hari,
        // penjualan.total,
        // penjualan.jenistransaksi,
        // penjualan.jenisbayar,
        // penjualan.`status`,
        // status_lunas,
        // IFNULL(IF(tgltransaksi >= '$dari' AND  tgltransaksi <= '$sampai',subtotal,0),0) as subtotal,
        // IFNULL(IF(tgltransaksi >= '$dari' AND  tgltransaksi <= '$sampai',penyharga,0),0) as penyharga,
        // IFNULL(IF(tgltransaksi >= '$dari' AND  tgltransaksi <= '$sampai',potongan,0),0) as potongan,
        // IFNULL(IF(tgltransaksi >= '$dari' AND  tgltransaksi <= '$sampai',potistimewa,0),0) as potistimewa,
        // lastpayment,
        // penjualan.id_karyawan,
        // salesbaru,
        // nama_karyawan,
        // karyawan.kode_cabang,
        // IFNULL(salesbaru,penjualan.id_karyawan) as salesbarunew,
        // (IFNULL(totalpf,0)-IFNULL(totalgb,0)) AS totalretur,
        // IFNULL(IF(tgltransaksi >= '$dari' AND  tgltransaksi <= '$sampai',total,0),0) -(IFNULL(totalpf,0)-IFNULL(totalgb,0)) as piutangbulanini,
        // (ifnull(penjualan.total,0) - (ifnull(totalpf_last,0)-ifnull(totalgb_last,0))) AS totalpiutang,
        // IFNULL(bayarsebelumbulanini,0) as bayarsebelumbulanini,
        // IFNULL(bayarbulanini,0) as bayarbulanini");
        // $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        // $query->leftJoin(
        //     DB::raw("(
        //         SELECT MAX(id_move) as id_move,no_fak_penj,move_faktur.id_karyawan as salesbaru
        //         FROM move_faktur
        //         WHERE tgl_move <= '$sampai'
        //         GROUP BY no_fak_penj,move_faktur.id_karyawan
        //     ) move_fak"),
        //     function ($join) {
        //         $join->on('penjualan.no_fak_penj', '=', 'move_fak.no_fak_penj');
        //     }
        // );

        // $query->join('karyawan', DB::raw("IFNULL(salesbaru,penjualan.id_karyawan)"), '=', 'karyawan.id_karyawan');
        // $query->leftJoin(
        //     DB::raw("(
        //         SELECT no_fak_penj,max(tglbayar) as lastpayment
        //         FROM historibayar
        //         GROUP BY no_fak_penj
        //     ) payment"),
        //     function ($join) {
        //         $join->on('penjualan.no_fak_penj', '=', 'payment.no_fak_penj');
        //     }
        // );

        // $query->leftJoin(
        //     DB::raw("(
        //         SELECT retur.no_fak_penj AS no_fak_penj,
        //             SUM(IF(tglretur >= '$dari' AND  tglretur <= '$sampai',retur.subtotal_gb,0)) AS totalgb,
        //             SUM(IF(tglretur >= '$dari' AND  tglretur <= '$sampai',retur.subtotal_pf,0)) AS totalpf,
        //             SUM(IF(tglretur < '$dari',retur.subtotal_gb,0)) AS totalgb_last,
        //             SUM(IF(tglretur < '$dari',retur.subtotal_pf,0)) AS totalpf_last
        //         FROM retur
        //         GROUP BY retur.no_fak_penj
        //     ) retur"),
        //     function ($join) {
        //         $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
        //     }
        // );

        // $query->leftJoin(
        //     DB::raw("(
        //         SELECT no_fak_penj,
        //         SUM(IF(tglbayar >= '$dari' AND  tglbayar <= '$sampai',bayar,0)) AS bayarbulanini,
        //         SUM(IF(tglbayar < '$dari',bayar,0)) AS bayarsebelumbulanini
        //         FROM historibayar
        //         GROUP BY no_fak_penj
        //     ) hb"),
        //     function ($join) {
        //         $join->on('penjualan.no_fak_penj', '=', 'hb.no_fak_penj');
        //     }
        // );

        // $query->where('penjualan.status_lunas', '2');
        // $query->where('penjualan.jenistransaksi', 'kredit');
        // $query->where('tgltransaksi', '<=', $sampai);
        // if ($request->kode_cabang != "") {
        //     $query->where('karyawan.kode_cabang', $request->kode_cabang);
        // }

        // if ($request->id_karyawan != "") {
        //     $query->whereRaw("IFNULL(salesbaru,penjualan.id_karyawan)='$request->id_karyawan'");
        // }

        // if ($request->kode_pelanggan != "") {
        //     $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
        // }

        // if ($ljt == 1) {
        //     $query->whereRaw("datediff('$sampai', penjualan.tgltransaksi) <= pelanggan.jatuhtempo");
        // } else if ($ljt == 2) {
        //     $query->whereRaw("datediff('$sampai', penjualan.tgltransaksi) > 15");
        // }

        // if (isset($_POST['tandaterimafaktur'])) {
        //     $query->whereIn('penjualan.no_fak_penj', $no_faktur);
        // }

        // $query->orwhere('penjualan.status_lunas', '1');
        // $query->where('penjualan.jenistransaksi', 'kredit');
        // $query->where('tgltransaksi', '<=', $sampai);
        // $query->where('lastpayment', '>=', $dari);
        // if ($request->kode_cabang != "") {
        //     $query->where('karyawan.kode_cabang', $request->kode_cabang);
        // }

        // if ($request->id_karyawan != "") {
        //     $query->whereRaw("IFNULL(salesbaru,penjualan.id_karyawan)='$request->id_karyawan'");
        // }

        // if ($request->kode_pelanggan != "") {
        //     $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
        // }

        // if ($ljt == 1) {
        //     $query->whereRaw("datediff('$sampai', penjualan.tgltransaksi) <= pelanggan.jatuhtempo");
        // } else if ($ljt == 2) {
        //     $query->whereRaw("datediff('$sampai', penjualan.tgltransaksi) > 15");
        // }

        // if (isset($_POST['tandaterimafaktur'])) {
        //     $query->whereIn('penjualan.no_fak_penj', $no_faktur);
        // }
        // $query->orderBy('tgltransaksi');
        // $query->orderBy('penjualan.no_fak_penj');
        // $kartupiutang = $query->get();
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Kartu Piutang Periode $dari-$sampai-$time.xls");
        }

        if (isset($_POST['tandaterimafaktur'])) {

            return view('penjualan.laporan.cetak_tandaterimafaktur', compact('kartupiutang', 'salesman', 'cabang', 'dari', 'sampai', 'pelanggan'));
        } else {
            return view('penjualan.laporan.cetak_kartupiutang', compact('kartupiutang', 'salesman', 'cabang', 'dari', 'sampai', 'pelanggan'));
        }
    }

    public function laporanaup()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_aup', compact('cabang'));
    }

    public function cetaklaporanaup(Request $request)
    {
        $tgl_aup = $request->tgl_aup;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->first();
        if (empty($request->kode_cabang)) {
            $cbg = "all";
        } else {
            $cbg = $request->kode_cabang;
        }

        if (empty($request->id_karyawan)) {
            $sales = "all";
        } else {
            $sales = $request->id_karyawan;
        }


        if (empty($request->kode_pelanggan)) {
            $idpel = "all";
        } else {
            $idpel = $request->kode_pelanggan;
        }



        if (isset($request->excludepusat)) {
            $exclude = 1;
        } else {
            $exclude = 0;
        }

        $query = Penjualan::query();
        $query->selectRaw("
        penjualan.kode_pelanggan,nama_pelanggan,karyawan.nama_karyawan,pasar,hari,pelanggan.jatuhtempo,
		CASE
		WHEN datediff('$tgl_aup', tgltransaksi) <= 15 THEN
				((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS duaminggu,
		CASE
		WHEN datediff('$tgl_aup', tgltransaksi) <= 31  AND datediff('$tgl_aup', tgltransaksi) > 15 THEN
				((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS satubulan,
		CASE
		WHEN datediff('$tgl_aup', tgltransaksi) <= 46  AND datediff('$tgl_aup', tgltransaksi) > 31 THEN
				((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS satubulan15,
		CASE
		WHEN datediff('$tgl_aup', tgltransaksi) <= 60  AND datediff('$tgl_aup', tgltransaksi) > 46 THEN
				((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS duabulan,
		CASE
		WHEN datediff('$tgl_aup', tgltransaksi) > 60 AND datediff('$tgl_aup', tgltransaksi) <= 90 THEN
				((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS lebihtigabulan,
		CASE
		WHEN datediff('$tgl_aup', tgltransaksi) > 90 AND datediff('$tgl_aup', tgltransaksi) <= 180 THEN
				((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS enambulan,
		CASE
		WHEN datediff('$tgl_aup', tgltransaksi) > 180 AND datediff('$tgl_aup', tgltransaksi) <= 360 THEN
				((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS duabelasbulan,
		CASE
		WHEN datediff('$tgl_aup', tgltransaksi) > 360 AND datediff('$tgl_aup', tgltransaksi) <= 720 THEN
				((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS duatahun,
		CASE
		WHEN datediff('$tgl_aup', tgltransaksi) > 720 THEN
				((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS lebihduatahun
        ");

        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $query->leftJoin(
            DB::raw("(
                SELECT pj.no_fak_penj,
				IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
				IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
				FROM penjualan pj
				INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
				LEFT JOIN (
					SELECT
                    id_move,no_fak_penj,
                    move_faktur.id_karyawan as salesbaru,
                    karyawan.kode_cabang  as cabangbaru
                    FROM move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                    WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$tgl_aup' GROUP BY no_fak_penj)
				) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT no_fak_penj,sum( historibayar.bayar ) AS jmlbayar
				FROM historibayar
				WHERE tglbayar <= '$tgl_aup'
				GROUP BY no_fak_penj
            ) hblalu"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj,
				SUM(total) AS total
				FROM
					retur
				WHERE tglretur <= '$tgl_aup'
				GROUP BY
					retur.no_fak_penj
            ) retur"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
            }
        );

        $query->orderBy('nama_pelanggan', 'asc');
        $query->orderBy('penjualan.kode_pelanggan', 'asc');
        $query->Where('penjualan.jenistransaksi', '!=', 'tunai');
        $query->where('tgltransaksi', '<=', $tgl_aup);
        if ($request->kode_cabang != "") {
            $query->where('cabangbarunew', $request->kode_cabang);
        }

        if ($request->id_karyawan != "") {
            $query->where('salesbarunew', $request->id_karyawan);
        }

        if ($request->kode_pelanggan != "") {
            $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
        }

        if (isset($request->excludepusat)) {
            $query->where('cabangbarunew', '!=', 'PST');
        }

        if ($tgl_aup < '2020-01-01') {
            $query->where('cabangbarunew', '!=', 'GRT');
        }
        // $query->whereRaw('(ifnull(penjualan.total,0) - (ifnull(retur.total,0))) - IFNULL(jmlbayar,0) > 0');
        $query->whereRaw('(ifnull(penjualan.total,0) - (ifnull(retur.total,0))) != IFNULL(jmlbayar,0)');
        $aup = $query->get();
        //dd($aup);
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=AUP Per Tanggal $tgl_aup-$time.xls");
        }
        return view('penjualan.laporan.cetak_aup', compact('aup', 'salesman', 'cabang', 'tgl_aup', 'pelanggan', 'cbg', 'sales', 'idpel', 'exclude'));
    }

    public function detailaup($cbg, $sales, $idpel, $tgl_aup, $kategori, $exclude)
    {
        $cabang = DB::table('cabang')->where('kode_cabang', $cbg)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $sales)->first();
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $idpel)->first();
        $query = Penjualan::query();
        $query->selectRaw('penjualan.no_fak_penj,tgltransaksi,
		penjualan.kode_pelanggan,
		nama_pelanggan,
		karyawan.nama_karyawan,
		pasar,
		hari,
		pelanggan.jatuhtempo as jt,
		((IFNULL( penjualan.total, 0 ))-(IFNULL( retur.total, 0 )))-(IFNULL( jmlbayar, 0 )) as jumlah');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $query->leftJoin(
            DB::raw("(
                SELECT pj.no_fak_penj,
				IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
				IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
				FROM penjualan pj
				INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
				LEFT JOIN (
					SELECT MAX(id_move) as id_move,no_fak_penj,move_faktur.id_karyawan as salesbaru,karyawan.kode_cabang as cabangbaru
					FROM move_faktur
					INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
					WHERE tgl_move <='$tgl_aup'
					GROUP BY no_fak_penj,move_faktur.id_karyawan,karyawan.kode_cabang
				) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
            ) pjmove"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT no_fak_penj,sum( historibayar.bayar ) AS jmlbayar
				FROM historibayar
				WHERE tglbayar <= '$tgl_aup'
				GROUP BY no_fak_penj
            ) hblalu"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj,
				SUM(total) AS total
				FROM
					retur
				WHERE tglretur <= '$tgl_aup'
				GROUP BY
					retur.no_fak_penj
            ) retur"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
            }
        );

        $query->orderBy('nama_pelanggan', 'asc');
        $query->orderBy('penjualan.kode_pelanggan', 'asc');
        $query->Where('penjualan.jenistransaksi', '!=', 'tunai');
        $query->where('tgltransaksi', '<=', $tgl_aup);
        if ($kategori == "duaminggu") {
            $query->whereRaw("datediff( '" . $tgl_aup . "', tgltransaksi ) <='15'");
        } else if ($kategori == "satubulan") {
            $query->whereRaw("datediff( '" . $tgl_aup . "', tgltransaksi ) >'15' AND datediff( '" . $tgl_aup . "', tgltransaksi ) <='31'");
        } else if ($kategori == "satubulan15") {
            $query->whereRaw("datediff( '" . $tgl_aup . "', tgltransaksi ) >'31' AND datediff( '" . $tgl_aup . "', tgltransaksi ) <='46'");
        } else if ($kategori == "duabulan") {
            $query->whereRaw("datediff( '" . $tgl_aup . "', tgltransaksi ) >'46' AND datediff( '" . $tgl_aup . "', tgltransaksi ) <='60'");
        } else if ($kategori == "tigabulan") {
            $query->whereRaw("datediff( '" . $tgl_aup . "', tgltransaksi ) >'60' AND datediff( '" . $tgl_aup . "', tgltransaksi ) <='90'");
        } else if ($kategori == "enambulan") {
            $query->whereRaw("datediff( '" . $tgl_aup . "', tgltransaksi ) >'90' AND datediff( '" . $tgl_aup . "', tgltransaksi ) <='180'");
        } else if ($kategori == "duabelasbulan") {
            $query->whereRaw("datediff( '" . $tgl_aup . "', tgltransaksi ) >'180' AND datediff( '" . $tgl_aup . "', tgltransaksi ) <='360'");
        } else if ($kategori == "duatahun") {
            $query->whereRaw("datediff( '" . $tgl_aup . "', tgltransaksi ) >'360' AND datediff( '" . $tgl_aup . "', tgltransaksi ) <='720'");
        } else if ($kategori == "lebihduatahun") {
            $query->whereRaw("datediff( '" . $tgl_aup . "', tgltransaksi ) >='720'");
        }
        if ($cbg != "all") {
            $query->where('cabangbarunew', $cbg);
        }

        if ($sales != "all") {
            $query->where('salesbarunew', $sales);
        }

        if ($idpel != "all") {
            $query->where('penjualan.kode_pelanggan', $idpel);
        }

        if ($exclude != 0) {
            $query->where('cabangbarunew', '!=', 'PST');
        }

        if ($tgl_aup < '2020-01-01') {
            $query->where('cabangbarunew', '!=', 'GRT');
        }
        $query->whereRaw('(ifnull(penjualan.total,0) - (ifnull(retur.total,0))) != IFNULL(jmlbayar,0)');
        $detailaup = $query->get();

        return view('penjualan.laporan.cetak_detailaup', compact('detailaup', 'salesman', 'cabang', 'tgl_aup', 'pelanggan', 'kategori'));
    }


    public function laporanlebihsatufaktur()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);

        return view('penjualan.laporan.frm.lap_lebihsatufaktur', compact('cabang'));
    }

    public function cetaklaporanlebihsatufaktur(Request $request)
    {
        $tanggal = $request->tanggal;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();


        $query2 = Penjualan::query();
        $query2->selectRaw('penjualan.kode_pelanggan');
        $query2->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query2->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query2->leftJoin(
            DB::raw("(
                SELECT no_fak_penj,sum( historibayar.bayar ) AS jmlbayar
				FROM historibayar
				WHERE tglbayar <= '$tanggal'
				GROUP BY no_fak_penj
            ) hblalu"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
            }
        );
        $query2->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj,
				SUM(total) AS total
				FROM
					retur
				WHERE tglretur <= '$tanggal'
				GROUP BY
					retur.no_fak_penj
            ) retur"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
            }
        );
        $query2->where('penjualan.jenistransaksi', '!=', 'tunai');
        $query2->where('tgltransaksi', '<=', $tanggal);

        if (!empty($request->kode_cabang)) {
            $query2->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->id_karyawan)) {
            $query2->where('penjualan.id_karyawan', $request->id_karyawan);
        }

        $query2->whereRaw('(ifnull(penjualan.total,0) - (ifnull(retur.total,0))) != IFNULL(jmlbayar,0)');
        $query2->groupBy('penjualan.kode_pelanggan');
        $query2->havingRaw('COUNT(penjualan.kode_pelanggan) > 1');
        $query2->orderBy('penjualan.kode_pelanggan', 'asc');
        $cekpelanggan = $query2->get();

        $kode_pelanggan = [];
        foreach ($cekpelanggan as $d) {
            $kode_pelanggan[] = $d->kode_pelanggan;
        }


        $query = Penjualan::query();
        $query->selectRaw('penjualan.no_fak_penj,tgltransaksi,penjualan.kode_pelanggan,nama_pelanggan,pasar,penjualan.total as totalpenjualan,keterangan,
        ( ifnull( penjualan.total, 0 ) - IFNULL( retur.total, 0 ) - ifnull( jmlbayar, 0 ) ) AS sisabayar, 1 AS jmlfaktur');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->leftJoin(
            DB::raw("(
                SELECT no_fak_penj,sum( historibayar.bayar ) AS jmlbayar
				FROM historibayar
				WHERE tglbayar <= '$tanggal'
				GROUP BY no_fak_penj
            ) hblalu"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj,
				SUM(total) AS total
				FROM
					retur
				WHERE tglretur <= '$tanggal'
				GROUP BY
					retur.no_fak_penj
            ) retur"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
            }
        );
        $query->where('penjualan.jenistransaksi', '!=', 'tunai');
        $query->where('tgltransaksi', '<=', $tanggal);

        if (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->id_karyawan)) {
            $query->where('penjualan.id_karyawan', $request->id_karyawan);
        }

        $query->whereRaw('(ifnull(penjualan.total,0) - (ifnull(retur.total,0))) != IFNULL(jmlbayar,0)');
        $query->whereIn('penjualan.kode_pelanggan', $kode_pelanggan);
        $query->orderBy('penjualan.kode_pelanggan', 'asc');
        $lebihsatufaktur = $query->get();
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Lebih Satu Faktur Per Tanggal $tanggal-$time.xls");
        }
        return view('penjualan.laporan.cetak_lebihsatufaktur', compact('lebihsatufaktur', 'salesman', 'cabang', 'tanggal'));
    }
    public function rekapwilayah()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_rekapwilayah', compact('cabang'));
    }

    public function cetakrekapwilayah(Request $request)
    {
        $cbg = $request->kode_cabang;
        $tahun = $request->tahun;
        $rekapwilayah = DB::table('penjualan')
            ->selectRaw("pelanggan.pasar,
                SUM(IF(MONTH(tgltransaksi)=1,total,0)) as jan,
                SUM(IF(MONTH(tgltransaksi)=2,total,0)) as feb,
                SUM(IF(MONTH(tgltransaksi)=3,total,0)) as mar,
                SUM(IF(MONTH(tgltransaksi)=4,total,0)) as apr,
                SUM(IF(MONTH(tgltransaksi)=5,total,0)) as mei,
                SUM(IF(MONTH(tgltransaksi)=6,total,0)) as jun,
                SUM(IF(MONTH(tgltransaksi)=7,total,0)) as jul,
                SUM(IF(MONTH(tgltransaksi)=8,total,0)) as agu,
                SUM(IF(MONTH(tgltransaksi)=9,total,0)) as sep,
                SUM(IF(MONTH(tgltransaksi)=10,total,0)) as okt,
                SUM(IF(MONTH(tgltransaksi)=11,total,0)) as nov,
                SUM(IF(MONTH(tgltransaksi)=12,total,0)) as des,
                SUM(total) as total")
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereRaw('YEAR(tgltransaksi)="' . $tahun . '"')
            ->where('karyawan.kode_cabang', $cbg)
            ->groupBy('pelanggan.pasar')
            ->get();
        $cabang = Cabang::where('kode_cabang', $cbg)->first();
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Omset Wilayah.xls");
        }
        return view('penjualan.laporan.cetak_rekapwilayah', compact('cabang', 'tahun', 'rekapwilayah'));
    }
    public function laporandppp()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('penjualan.laporan.frm.lap_dppp', compact('cabang', 'bulan'));
    }

    public function cetaklaporandppp(Request $request)
    {
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $produk = Barang::orderBy('kode_produk', 'asc')->get();
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $tahunlalu = $request->tahun - 1;
        $sumber = $request->sumber;

        $awaltahunini = $tahun . "-01-01";
        $awaltahunlalu = $tahunlalu . "-01-01";

        $awalbulanini = $tahun . "-" . $bulan . "-01";
        $akhirbulanini = date('Y-m-t', strtotime($awalbulanini));

        $awalbulaninilast = $tahun - 1 . "-" . $bulan . "-01";
        $akhirbulaninilast = date('Y-m-t', strtotime($awalbulaninilast));

        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $namabulan = $bln[$bulan];
        if (!empty($request->kode_cabang)) {
            $query = Salesman::query();
            $query->selectRaw('karyawan.id_karyawan,nama_karyawan,
            reallastbulanini_ab,
            reallastsampaibulanini_ab,
            realbulanini_ab,
            realsampaibulanini_ab,
            reallastbulanini_ar,realbulanini_ar,realsampaibulanini_ar,reallastsampaibulanini_ar,
            reallastbulanini_as,realbulanini_as,realsampaibulanini_as,reallastsampaibulanini_as,
            reallastbulanini_bb,realbulanini_bb,realsampaibulanini_bb,reallastsampaibulanini_bb,
            reallastbulanini_cg,realbulanini_cg,realsampaibulanini_cg,reallastsampaibulanini_cg,
            reallastbulanini_cgg,realbulanini_cgg,realsampaibulanini_cgg,reallastsampaibulanini_cgg,
            reallastbulanini_dep,realbulanini_dep,realsampaibulanini_dep,reallastsampaibulanini_dep,
            reallastbulanini_ds,realbulanini_ds,realsampaibulanini_ds,reallastsampaibulanini_ds,
            reallastbulanini_sp,realbulanini_sp,realsampaibulanini_sp,reallastsampaibulanini_sp,
            reallastbulanini_cg5,realbulanini_cg5,realsampaibulanini_cg5,reallastsampaibulanini_cg5,

            reallastbulanini_sp8,realbulanini_sp8,realsampaibulanini_sp8,reallastsampaibulanini_sp8,
            reallastbulanini_sc,realbulanini_sc,realsampaibulanini_sc,reallastsampaibulanini_sc,
            ab_bulanini,ab_sampaibulanini,
            ar_bulanini,ar_sampaibulanini,
            as_bulanini,as_sampaibulanini,
            bb_bulanini,bb_sampaibulanini,
            cg_bulanini,cg_sampaibulanini,
            cgg_sampaibulanini,cgg_bulanini,
            dep_bulanini,dep_sampaibulanini,
            ds_bulanini,ds_sampaibulanini,
            sp_bulanini,sp_sampaibulanini,
            cg5_bulanini,cg5_sampaibulanini,
            sp8_bulanini,sp8_sampaibulanini,
            sc_bulanini,sc_sampaibulanini

            ');
            if ($sumber == 1) {
                $query->leftJoin(
                    DB::raw("(
                            SELECT
                            penjualan.id_karyawan,
                            SUM(IF( kode_produk = 'AB' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ab,
                            SUM(IF( kode_produk = 'AB' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ab,
                            SUM(IF( kode_produk = 'AB' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ab,
                            SUM(IF( kode_produk = 'AB' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ab,

                            SUM(IF( kode_produk = 'AR' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ar,
                            SUM(IF( kode_produk = 'AR' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ar,
                            SUM(IF( kode_produk = 'AR' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ar,
                            SUM(IF( kode_produk = 'AR' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ar,

                            SUM(IF( kode_produk = 'AS' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_as,
                            SUM(IF( kode_produk = 'AS' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_as,
                            SUM(IF( kode_produk = 'AS' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_as,
                            SUM(IF( kode_produk = 'AS' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_as,

                            SUM(IF( kode_produk = 'BB' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_bb,
                            SUM(IF( kode_produk = 'BB' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_bb,
                            SUM(IF( kode_produk = 'BB' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_bb,
                            SUM(IF( kode_produk = 'BB' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_bb,

                            SUM(IF( kode_produk = 'CG' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cg,
                            SUM(IF( kode_produk = 'CG' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cg,
                            SUM(IF( kode_produk = 'CG' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cg,
                            SUM(IF( kode_produk = 'CG' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cg,


                            SUM(IF( kode_produk = 'CGG' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cgg,
                            SUM(IF( kode_produk = 'CGG' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cgg,
                            SUM(IF( kode_produk = 'CGG' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cgg,
                            SUM(IF( kode_produk = 'CGG' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cgg,

                            SUM(IF( kode_produk = 'DEP' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_dep,
                            SUM(IF( kode_produk = 'DEP' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_dep,
                            SUM(IF( kode_produk = 'DEP' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_dep,
                            SUM(IF( kode_produk = 'DEP' AND lastpayment >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_dep,

                            SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ds,
                            SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ds,
                            SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ds,
                            SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ds,

                            SUM(IF( kode_produk = 'SP' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sp,
                            SUM(IF( kode_produk = 'SP' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sp,
                            SUM(IF( kode_produk = 'SP' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sp,
                            SUM(IF( kode_produk = 'SP' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sp,

                            SUM(IF( kode_produk = 'CG5' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cg5,
                            SUM(IF( kode_produk = 'CG5' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cg5,
                            SUM(IF( kode_produk = 'CG5' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cg5,
                            SUM(IF( kode_produk = 'CG5' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cg5,

                            SUM(IF( kode_produk = 'SP8' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sp8,
                            SUM(IF( kode_produk = 'SP8' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sp8,
                            SUM(IF( kode_produk = 'SP8' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sp8,
                            SUM(IF( kode_produk = 'SP8' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sp8,

                            SUM(IF( kode_produk = 'SC' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sc,
                            SUM(IF( kode_produk = 'SC' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sc,
                            SUM(IF( kode_produk = 'SC' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sc,
                            SUM(IF( kode_produk = 'SC' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sc

                        FROM
                            detailpenjualan
                            INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                            INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                            INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                            LEFT JOIN (
                                SELECT no_fak_penj,max(tglbayar) as lastpayment
                                FROM historibayar
                                GROUP BY no_fak_penj
                            ) hb ON (hb.no_fak_penj = penjualan.no_fak_penj)
                        WHERE
                        lastpayment BETWEEN '$awaltahunlalu' AND '$akhirbulaninilast' AND status_lunas ='1'  OR
                        lastpayment BETWEEN '$awaltahunini' AND '$akhirbulanini' AND status_lunas ='1'
                        GROUP BY
                            penjualan.id_karyawan
                        ) realisasi"),
                    function ($join) {
                        $join->on('karyawan.id_karyawan', '=', 'realisasi.id_karyawan');
                    }
                );
            } else {
                $query->leftJoin(
                    DB::raw("(
                            SELECT
                            penjualan.id_karyawan,
                            SUM(IF( kode_produk = 'AB' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ab,
                            SUM(IF( kode_produk = 'AB' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ab,
                            SUM(IF( kode_produk = 'AB' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ab,
                            SUM(IF( kode_produk = 'AB' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ab,

                            SUM(IF( kode_produk = 'AR' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ar,
                            SUM(IF( kode_produk = 'AR' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ar,
                            SUM(IF( kode_produk = 'AR' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ar,
                            SUM(IF( kode_produk = 'AR' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ar,

                            SUM(IF( kode_produk = 'AS' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_as,
                            SUM(IF( kode_produk = 'AS' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_as,
                            SUM(IF( kode_produk = 'AS' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_as,
                            SUM(IF( kode_produk = 'AS' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_as,

                            SUM(IF( kode_produk = 'BB' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_bb,
                            SUM(IF( kode_produk = 'BB' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_bb,
                            SUM(IF( kode_produk = 'BB' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_bb,
                            SUM(IF( kode_produk = 'BB' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_bb,

                            SUM(IF( kode_produk = 'CG' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cg,
                            SUM(IF( kode_produk = 'CG' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cg,
                            SUM(IF( kode_produk = 'CG' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cg,
                            SUM(IF( kode_produk = 'CG' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cg,


                            SUM(IF( kode_produk = 'CGG' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cgg,
                            SUM(IF( kode_produk = 'CGG' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cgg,
                            SUM(IF( kode_produk = 'CGG' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cgg,
                            SUM(IF( kode_produk = 'CGG' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cgg,

                            SUM(IF( kode_produk = 'DEP' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_dep,
                            SUM(IF( kode_produk = 'DEP' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_dep,
                            SUM(IF( kode_produk = 'DEP' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_dep,
                            SUM(IF( kode_produk = 'DEP' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_dep,

                            SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ds,
                            SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ds,
                            SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ds,
                            SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ds,

                            SUM(IF( kode_produk = 'SP' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sp,
                            SUM(IF( kode_produk = 'SP' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sp,
                            SUM(IF( kode_produk = 'SP' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sp,
                            SUM(IF( kode_produk = 'SP' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sp,

                            SUM(IF( kode_produk = 'CG5' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cg5,
                            SUM(IF( kode_produk = 'CG5' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cg5,
                            SUM(IF( kode_produk = 'CG5' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cg5,
                            SUM(IF( kode_produk = 'CG5' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cg5,

                            SUM(IF( kode_produk = 'SP8' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sp8,
                            SUM(IF( kode_produk = 'SP8' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sp8,
                            SUM(IF( kode_produk = 'SP8' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sp8,
                            SUM(IF( kode_produk = 'SP8' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sp8,

                            SUM(IF( kode_produk = 'SC' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sc,
                            SUM(IF( kode_produk = 'SC' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sc,
                            SUM(IF( kode_produk = 'SC' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sc,
                            SUM(IF( kode_produk = 'SC' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sc

                        FROM
                            detailpenjualan
                            INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                            INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                            INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang

                        WHERE
                        tgltransaksi BETWEEN '$awaltahunlalu' AND '$akhirbulaninilast'  OR
                        tgltransaksi BETWEEN '$awaltahunini' AND '$akhirbulanini'
                        GROUP BY
                            penjualan.id_karyawan
                        ) realisasi"),
                    function ($join) {
                        $join->on('karyawan.id_karyawan', '=', 'realisasi.id_karyawan');
                    }
                );
            }

            $query->leftJoin(
                DB::raw("(
                        SELECT dt.id_karyawan,
                        SUM(IF(kode_produk = 'AB' AND bulan = '$bulan',jumlah_target,0)) as ab_bulanini,
                        SUM(IF(kode_produk = 'AB' AND bulan <= '$bulan',jumlah_target,0)) as ab_sampaibulanini,


                        SUM(IF(kode_produk = 'AR' AND bulan = '$bulan',jumlah_target,0)) as ar_bulanini,
                        SUM(IF(kode_produk = 'AR' AND bulan <= '$bulan',jumlah_target,0)) as ar_sampaibulanini,


                        SUM(IF(kode_produk = 'AS' AND bulan = '$bulan',jumlah_target,0)) as as_bulanini,
                        SUM(IF(kode_produk = 'AS' AND bulan <= '$bulan',jumlah_target,0)) as as_sampaibulanini,


                        SUM(IF(kode_produk = 'BB' AND bulan = '$bulan',jumlah_target,0)) as bb_bulanini,
                        SUM(IF(kode_produk = 'BB' AND bulan <= '$bulan',jumlah_target,0)) as bb_sampaibulanini,


                        SUM(IF(kode_produk = 'CG' AND bulan = '$bulan',jumlah_target,0)) as cg_bulanini,
                        SUM(IF(kode_produk = 'CG' AND bulan <= '$bulan',jumlah_target,0)) as cg_sampaibulanini,

                        SUM(IF(kode_produk = 'CGG',jumlah_target,0)) as cgg_tahun,
                        SUM(IF(kode_produk = 'CGG' AND bulan = '$bulan',jumlah_target,0)) as cgg_bulanini,
                        SUM(IF(kode_produk = 'CGG' AND bulan <= '$bulan',jumlah_target,0)) as cgg_sampaibulanini,


                        SUM(IF(kode_produk = 'DEP' AND bulan = '$bulan',jumlah_target,0)) as dep_bulanini,
                        SUM(IF(kode_produk = 'DEP' AND bulan <= '$bulan',jumlah_target,0)) as dep_sampaibulanini,


                        SUM(IF(kode_produk = 'DS' AND bulan = '$bulan',jumlah_target,0)) as ds_bulanini,
                        SUM(IF(kode_produk = 'DS' AND bulan <= '$bulan',jumlah_target,0)) as ds_sampaibulanini,


                        SUM(IF(kode_produk = 'SP' AND bulan = '$bulan',jumlah_target,0)) as sp_bulanini,
                        SUM(IF(kode_produk = 'SP' AND bulan <= '$bulan',jumlah_target,0)) as sp_sampaibulanini,


                        SUM(IF(kode_produk = 'CG5' AND bulan = '$bulan',jumlah_target,0)) as cg5_bulanini,
                        SUM(IF(kode_produk = 'CG5' AND bulan <= '$bulan',jumlah_target,0)) as cg5_sampaibulanini,

                        SUM(IF(kode_produk = 'SP8' AND bulan = '$bulan',jumlah_target,0)) as sp8_bulanini,
                        SUM(IF(kode_produk = 'SP8' AND bulan <= '$bulan',jumlah_target,0)) as sp8_sampaibulanini,

                        SUM(IF(kode_produk = 'SC' AND bulan = '$bulan',jumlah_target,0)) as sc_bulanini,
                        SUM(IF(kode_produk = 'SC' AND bulan <= '$bulan',jumlah_target,0)) as sc_sampaibulanini


                        FROM komisi_target_qty_detail dt
                        INNER JOIN komisi_target kt ON dt.kode_target = kt.kode_target
                        INNER JOIN karyawan ON dt.id_karyawan = karyawan.id_karyawan
                        WHERE tahun = '$tahun' AND bulan <='$bulan' AND karyawan.kode_cabang = '$request->kode_cabang'
                        GROUP BY dt.id_karyawan
                    ) target"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'target.id_karyawan');
                }
            );

            $query->where('karyawan.kode_cabang', $request->kode_cabang);
            $query->where('karyawan.status_aktif_sales', 1);
            $dppp = $query->get();
            if (isset($_POST['export'])) {
                $time = date("H:i:s");
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=DPPP Periode $bulan$tahun-$time.xls");
            }
            return view('penjualan.laporan.cetak_dppp_salesman', compact('dppp', 'cabang', 'namabulan', 'tahun', 'produk'));
        } else {
            $query = Cabang::query();
            $query->selectRaw('cabang.kode_cabang,
            nama_cabang,
            reallastbulanini_ab,
            reallastsampaibulanini_ab,
            realbulanini_ab,
            realsampaibulanini_ab,
            reallastbulanini_ar,realbulanini_ar,realsampaibulanini_ar,reallastsampaibulanini_ar,
            reallastbulanini_as,realbulanini_as,realsampaibulanini_as,reallastsampaibulanini_as,
            reallastbulanini_bb,realbulanini_bb,realsampaibulanini_bb,reallastsampaibulanini_bb,
            reallastbulanini_cg,realbulanini_cg,realsampaibulanini_cg,reallastsampaibulanini_cg,
            reallastbulanini_cgg,realbulanini_cgg,realsampaibulanini_cgg,reallastsampaibulanini_cgg,
            reallastbulanini_dep,realbulanini_dep,realsampaibulanini_dep,reallastsampaibulanini_dep,
            reallastbulanini_ds,realbulanini_ds,realsampaibulanini_ds,reallastsampaibulanini_ds,
            reallastbulanini_sp,realbulanini_sp,realsampaibulanini_sp,reallastsampaibulanini_sp,
            reallastbulanini_cg5,realbulanini_cg5,realsampaibulanini_cg5,reallastsampaibulanini_cg5,
            reallastbulanini_sp8,realbulanini_sp8,realsampaibulanini_sp8,reallastsampaibulanini_sp8,
            reallastbulanini_sc,realbulanini_sc,realsampaibulanini_sc,reallastsampaibulanini_sc,
            ab_bulanini,ab_sampaibulanini,
            ar_bulanini,ar_sampaibulanini,
            as_bulanini,as_sampaibulanini,
            bb_bulanini,bb_sampaibulanini,
            cg_bulanini,cg_sampaibulanini,
            cgg_sampaibulanini,cgg_bulanini,
            dep_bulanini,dep_sampaibulanini,
            ds_bulanini,ds_sampaibulanini,
            sp_bulanini,sp_sampaibulanini,
            cg5_bulanini,cg5_sampaibulanini,
            sp8_bulanini,sp8_sampaibulanini,
            sc_bulanini,sc_sampaibulanini');
            $query->leftJoin(
                DB::raw("(
                    SELECT karyawan.kode_cabang,
                    SUM(IF(kode_produk = 'AB' AND bulan = '$bulan',jumlah_target,0)) as ab_bulanini,
                    SUM(IF(kode_produk = 'AB' AND bulan <= '$bulan',jumlah_target,0)) as ab_sampaibulanini,


                    SUM(IF(kode_produk = 'AR' AND bulan = '$bulan',jumlah_target,0)) as ar_bulanini,
                    SUM(IF(kode_produk = 'AR' AND bulan <= '$bulan',jumlah_target,0)) as ar_sampaibulanini,


                    SUM(IF(kode_produk = 'AS' AND bulan = '$bulan',jumlah_target,0)) as as_bulanini,
                    SUM(IF(kode_produk = 'AS' AND bulan <= '$bulan',jumlah_target,0)) as as_sampaibulanini,


                    SUM(IF(kode_produk = 'BB' AND bulan = '$bulan',jumlah_target,0)) as bb_bulanini,
                    SUM(IF(kode_produk = 'BB' AND bulan <= '$bulan',jumlah_target,0)) as bb_sampaibulanini,


                    SUM(IF(kode_produk = 'CG' AND bulan = '$bulan',jumlah_target,0)) as cg_bulanini,
                    SUM(IF(kode_produk = 'CG' AND bulan <= '$bulan',jumlah_target,0)) as cg_sampaibulanini,

                    SUM(IF(kode_produk = 'CGG',jumlah_target,0)) as cgg_tahun,
                    SUM(IF(kode_produk = 'CGG' AND bulan = '$bulan',jumlah_target,0)) as cgg_bulanini,
                    SUM(IF(kode_produk = 'CGG' AND bulan <= '$bulan',jumlah_target,0)) as cgg_sampaibulanini,


                    SUM(IF(kode_produk = 'DEP' AND bulan = '$bulan',jumlah_target,0)) as dep_bulanini,
                    SUM(IF(kode_produk = 'DEP' AND bulan <= '$bulan',jumlah_target,0)) as dep_sampaibulanini,


                    SUM(IF(kode_produk = 'DS' AND bulan = '$bulan',jumlah_target,0)) as ds_bulanini,
                    SUM(IF(kode_produk = 'DS' AND bulan <= '$bulan',jumlah_target,0)) as ds_sampaibulanini,


                    SUM(IF(kode_produk = 'SP' AND bulan = '$bulan',jumlah_target,0)) as sp_bulanini,
                    SUM(IF(kode_produk = 'SP' AND bulan <= '$bulan',jumlah_target,0)) as sp_sampaibulanini,


                    SUM(IF(kode_produk = 'CG5' AND bulan = '$bulan',jumlah_target,0)) as cg5_bulanini,
                    SUM(IF(kode_produk = 'CG5' AND bulan <= '$bulan',jumlah_target,0)) as cg5_sampaibulanini,

                    SUM(IF(kode_produk = 'SP8' AND bulan = '$bulan',jumlah_target,0)) as sp8_bulanini,
                    SUM(IF(kode_produk = 'SP8' AND bulan <= '$bulan',jumlah_target,0)) as sp8_sampaibulanini,

                    SUM(IF(kode_produk = 'SC' AND bulan = '$bulan',jumlah_target,0)) as sc_bulanini,
                    SUM(IF(kode_produk = 'SC' AND bulan <= '$bulan',jumlah_target,0)) as sc_sampaibulanini


                    FROM komisi_target_qty_detail dt
                    INNER JOIN komisi_target kt ON dt.kode_target = kt.kode_target
                    INNER JOIN karyawan ON dt.id_karyawan = karyawan.id_karyawan
                    WHERE tahun = '$tahun' AND bulan <='$bulan'
                    GROUP BY karyawan.kode_cabang
                ) target"),
                function ($join) {
                    $join->on('cabang.kode_cabang', '=', 'target.kode_cabang');
                }
            );

            if ($sumber == 1) {
                $query->leftJoin(
                    DB::raw("(
                        SELECT
                        karyawan.kode_cabang,
                        SUM(IF( kode_produk = 'AB' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ab,
                        SUM(IF( kode_produk = 'AB' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ab,
                        SUM(IF( kode_produk = 'AB' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ab,
                        SUM(IF( kode_produk = 'AB' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ab,

                        SUM(IF( kode_produk = 'AR' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ar,
                        SUM(IF( kode_produk = 'AR' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ar,
                        SUM(IF( kode_produk = 'AR' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ar,
                        SUM(IF( kode_produk = 'AR' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ar,

                        SUM(IF( kode_produk = 'AS' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_as,
                        SUM(IF( kode_produk = 'AS' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_as,
                        SUM(IF( kode_produk = 'AS' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_as,
                        SUM(IF( kode_produk = 'AS' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_as,

                        SUM(IF( kode_produk = 'BB' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_bb,
                        SUM(IF( kode_produk = 'BB' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_bb,
                        SUM(IF( kode_produk = 'BB' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_bb,
                        SUM(IF( kode_produk = 'BB' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_bb,

                        SUM(IF( kode_produk = 'CG' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cg,
                        SUM(IF( kode_produk = 'CG' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cg,
                        SUM(IF( kode_produk = 'CG' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cg,
                        SUM(IF( kode_produk = 'CG' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cg,


                        SUM(IF( kode_produk = 'CGG' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cgg,
                        SUM(IF( kode_produk = 'CGG' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cgg,
                        SUM(IF( kode_produk = 'CGG' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cgg,
                        SUM(IF( kode_produk = 'CGG' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cgg,

                        SUM(IF( kode_produk = 'DEP' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_dep,
                        SUM(IF( kode_produk = 'DEP' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_dep,
                        SUM(IF( kode_produk = 'DEP' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_dep,
                        SUM(IF( kode_produk = 'DEP' AND lastpayment >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_dep,

                        SUM(IF( kode_produk = 'DS' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ds,
                        SUM(IF( kode_produk = 'DS' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ds,
                        SUM(IF( kode_produk = 'DS' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ds,
                        SUM(IF( kode_produk = 'DS' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ds,

                        SUM(IF( kode_produk = 'SP' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sp,
                        SUM(IF( kode_produk = 'SP' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sp,
                        SUM(IF( kode_produk = 'SP' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sp,
                        SUM(IF( kode_produk = 'SP' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sp,

                        SUM(IF( kode_produk = 'CG5' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cg5,
                        SUM(IF( kode_produk = 'CG5' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cg5,
                        SUM(IF( kode_produk = 'CG5' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cg5,
                        SUM(IF( kode_produk = 'CG5' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cg5,

                        SUM(IF( kode_produk = 'SP8' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sp8,
                        SUM(IF( kode_produk = 'SP8' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sp8,
                        SUM(IF( kode_produk = 'SP8' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sp8,
                        SUM(IF( kode_produk = 'SP8' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sp8,

                        SUM(IF( kode_produk = 'SC' AND lastpayment >= '$awalbulaninilast' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sc,
                        SUM(IF( kode_produk = 'SC' AND lastpayment >= '$awaltahunlalu' AND lastpayment <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sc,
                        SUM(IF( kode_produk = 'SC' AND lastpayment >= '$awalbulanini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sc,
                        SUM(IF( kode_produk = 'SC' AND lastpayment >= '$awaltahunini' AND lastpayment <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sc

                        FROM
                            detailpenjualan
                            INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                            INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                            INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                            LEFT JOIN (
                                SELECT no_fak_penj,max(tglbayar) as lastpayment
                                FROM historibayar
                                GROUP BY no_fak_penj
                            ) hb ON (hb.no_fak_penj = penjualan.no_fak_penj)
                        WHERE
                        lastpayment BETWEEN '$awaltahunlalu' AND '$akhirbulaninilast' AND status_lunas ='1'  OR
                        lastpayment BETWEEN '$awaltahunini' AND '$akhirbulanini' AND status_lunas ='1'
                        GROUP BY
                            karyawan.kode_cabang
                        ) realisasi"),
                    function ($join) {
                        $join->on('cabang.kode_cabang', '=', 'realisasi.kode_cabang');
                    }
                );
            } else {
                $query->leftJoin(
                    DB::raw("(
                        SELECT
                        karyawan.kode_cabang,
                        SUM(IF( kode_produk = 'AB' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ab,
                        SUM(IF( kode_produk = 'AB' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ab,
                        SUM(IF( kode_produk = 'AB' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ab,
                        SUM(IF( kode_produk = 'AB' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ab,

                        SUM(IF( kode_produk = 'AR' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ar,
                        SUM(IF( kode_produk = 'AR' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ar,
                        SUM(IF( kode_produk = 'AR' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ar,
                        SUM(IF( kode_produk = 'AR' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ar,

                        SUM(IF( kode_produk = 'AS' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_as,
                        SUM(IF( kode_produk = 'AS' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_as,
                        SUM(IF( kode_produk = 'AS' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_as,
                        SUM(IF( kode_produk = 'AS' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_as,

                        SUM(IF( kode_produk = 'BB' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_bb,
                        SUM(IF( kode_produk = 'BB' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_bb,
                        SUM(IF( kode_produk = 'BB' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_bb,
                        SUM(IF( kode_produk = 'BB' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_bb,

                        SUM(IF( kode_produk = 'CG' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cg,
                        SUM(IF( kode_produk = 'CG' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cg,
                        SUM(IF( kode_produk = 'CG' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cg,
                        SUM(IF( kode_produk = 'CG' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cg,


                        SUM(IF( kode_produk = 'CGG' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cgg,
                        SUM(IF( kode_produk = 'CGG' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cgg,
                        SUM(IF( kode_produk = 'CGG' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cgg,
                        SUM(IF( kode_produk = 'CGG' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cgg,

                        SUM(IF( kode_produk = 'DEP' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_dep,
                        SUM(IF( kode_produk = 'DEP' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_dep,
                        SUM(IF( kode_produk = 'DEP' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_dep,
                        SUM(IF( kode_produk = 'DEP' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_dep,

                        SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_ds,
                        SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_ds,
                        SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_ds,
                        SUM(IF( kode_produk = 'DS' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_ds,

                        SUM(IF( kode_produk = 'SP' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sp,
                        SUM(IF( kode_produk = 'SP' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sp,
                        SUM(IF( kode_produk = 'SP' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sp,
                        SUM(IF( kode_produk = 'SP' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sp,

                        SUM(IF( kode_produk = 'CG5' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_cg5,
                        SUM(IF( kode_produk = 'CG5' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_cg5,
                        SUM(IF( kode_produk = 'CG5' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_cg5,
                        SUM(IF( kode_produk = 'CG5' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_cg5,

                        SUM(IF( kode_produk = 'SP8' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sp8,
                        SUM(IF( kode_produk = 'SP8' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sp8,
                        SUM(IF( kode_produk = 'SP8' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sp8,
                        SUM(IF( kode_produk = 'SP8' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sp8,

                        SUM(IF( kode_produk = 'SC' AND tgltransaksi >= '$awalbulaninilast' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastbulanini_sc,
                        SUM(IF( kode_produk = 'SC' AND tgltransaksi >= '$awaltahunlalu' AND tgltransaksi <= '$akhirbulaninilast', jumlah, 0 )) AS reallastsampaibulanini_sc,
                        SUM(IF( kode_produk = 'SC' AND tgltransaksi >= '$awalbulanini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realbulanini_sc,
                        SUM(IF( kode_produk = 'SC' AND tgltransaksi >= '$awaltahunini' AND tgltransaksi <= '$akhirbulanini', jumlah, 0 )) AS realsampaibulanini_sc

                    FROM
                        detailpenjualan
                        INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                        INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                        INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang

                    WHERE
                    tgltransaksi BETWEEN '$awaltahunlalu' AND '$akhirbulaninilast' OR
                    tgltransaksi BETWEEN '$awaltahunini' AND '$akhirbulanini'
                    GROUP BY
                        karyawan.kode_cabang
                        ) realisasi"),
                    function ($join) {
                        $join->on('cabang.kode_cabang', '=', 'realisasi.kode_cabang');
                    }
                );
            }

            $dppp = $query->get();
            if (isset($_POST['export'])) {
                $time = date("H:i:s");
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=DPPP Periode $bulan$tahun-$time.xls");
            }
            return view('penjualan.laporan.cetak_dppp', compact('dppp', 'namabulan', 'tahun', 'produk'));
        }
    }

    public function laporandpp()
    {

        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_dpp', compact('cabang'));
    }

    public function cetaklaporandpp(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->first();
        $query = Detailpenjualan::query();
        $query->selectRaw("tgltransaksi,penjualan.kode_pelanggan,nama_pelanggan,pasar,alamat_pelanggan,penjualan.id_karyawan,nama_karyawan,
        SUM( IF ( kode_produk = 'BB', jumlah/isipcsdus, 0 ) ) AS BB,
        SUM( IF ( kode_produk = 'AB', jumlah/isipcsdus, 0 ) ) AS AB,
        SUM( IF ( kode_produk = 'AR', jumlah/isipcsdus, 0 ) ) AS AR,
        SUM( IF ( kode_produk = 'AS', jumlah/isipcsdus, 0 ) ) AS ASE,
        SUM( IF ( kode_produk = 'DEP', jumlah/isipcsdus, 0 ) ) AS DP,
        SUM( IF ( kode_produk = 'DK', jumlah/isipcsdus, 0 ) )  AS DK,
        SUM( IF ( kode_produk = 'DS', jumlah/isipcsdus, 0 ) )  AS DS,
        SUM( IF ( kode_produk = 'DB', jumlah/isipcsdus, 0 ) )  AS DB,
        SUM( IF ( kode_produk = 'CG', jumlah/isipcsdus, 0 ) )  AS CG,
        SUM( IF ( kode_produk = 'CGG', jumlah/isipcsdus, 0 ) ) AS CGG,
        SUM( IF ( kode_produk = 'SP', jumlah/isipcsdus, 0 ) ) AS SP,
        SUM( IF ( kode_produk = 'BBP', jumlah/isipcsdus, 0 ) ) AS BBP,
        SUM( IF ( kode_produk = 'SPP', jumlah/isipcsdus, 0 ) ) AS SPP,
        SUM( IF ( kode_produk = 'CG5', jumlah/isipcsdus, 0 ) ) AS CG5,
        SUM( IF ( kode_produk = 'SC', jumlah/isipcsdus, 0 ) ) AS SC,
        SUM( IF ( kode_produk = 'SP8', jumlah/isipcsdus, 0 ) ) AS SP8");
        $query->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang');
        $query->join('penjualan', 'detailpenjualan.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->whereBetween('tgltransaksi', [$dari, $sampai]);
        if ($request->kode_cabang != "") {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }
        if ($request->id_karyawan != "") {
            $query->where('penjualan.id_karyawan', $request->id_karyawan);
        }

        if ($request->kode_pelanggan != "") {
            $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
        }

        $query->groupByRaw('tgltransaksi,penjualan.kode_pelanggan,nama_pelanggan,pasar,alamat_pelanggan,penjualan.id_karyawan,nama_karyawan');
        $query->orderBy('tgltransaksi');
        $dpp = $query->get();
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=DPP Periode $dari-$sampai-$time.xls");
        }
        return view('penjualan.laporan.cetak_dpp', compact('dpp', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan'));
    }

    public function laporanrekapomsetpelanggan()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_rekapomsetpelanggan', compact('cabang'));
    }

    public function cetaklaporanrekapomsetpelanggan(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $date1 = date_create($dari);
        $date2 = date_create($sampai);
        $selisih = date_diff($date1, $date2);
        $periode = $selisih->m + 1;
        $query = Penjualan::query();
        $query->selectRaw("penjualan.kode_pelanggan,nama_pelanggan,pasar,
        SUM(bruto) as bruto,
        SUM(brutoswan) as brutoswan,
        SUM(brutoaida) as brutoaida,
        SUM(brutoswan) - SUM(potswan + potisswan  + potstick + potisstick ) -SUM(penyswan+penystick) - SUM(potsp) as netswan,
        SUM(brutoaida) - SUM(potaida + potisaida) - SUM(penyaida)  as netaida,
        (SUM(brutoswan) - SUM(potswan + potisswan  + potstick + potisstick ) -SUM(penyswan+penystick) - SUM(potsp)) + (SUM(brutoaida) - SUM(potaida + potisaida) - SUM(penyaida) ) as netpenjualan");
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->leftJoin(
            DB::raw("(
                SELECT no_fak_penj,
						SUM(IF(master_barang.jenis_produk = 'SWAN',detailpenjualan.subtotal,0)) as brutoswan,
						SUM(IF(master_barang.jenis_produk = 'AIDA',detailpenjualan.subtotal,0)) as brutoaida,
						SUM(detailpenjualan.subtotal) as bruto
						FROM detailpenjualan
						INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
						INNER JOIN master_barang ON barang.kode_produk = master_barang.kode_produk
						GROUP BY no_fak_penj
                ) dp"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'dp.no_fak_penj');
            }
        );

        $query->whereBetween('tgltransaksi', [$dari, $sampai]);
        if ($request->kode_cabang != "") {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->id_karyawan)) {
            $query->where('penjualan.id_karyawan', $request->id_karyawan);
        }
        $query->groupByRaw('penjualan.kode_pelanggan,nama_pelanggan,pasar');
        $query->orderBy('nama_pelanggan');
        $rekapomsetpelanggan = $query->get();

        $karyawan = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Omset Pelanggan Periode $dari-$sampai-$time.xls");
        }
        return view('penjualan.laporan.cetak_rekapomsetpelanggan', compact('rekapomsetpelanggan', 'cabang', 'dari', 'sampai', 'periode', 'karyawan'));
    }

    public function laporanrekappelanggan()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_rekappelanggan', compact('cabang'));
    }

    public function cetaklaporanrekappelanggan(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $request->kode_pelanggan)->first();
        $query = Detailpenjualan::query();
        $query->selectRaw("penjualan.kode_pelanggan,nama_pelanggan,
        SUM( IF ( kode_produk = 'AB', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS AB,
        SUM( IF ( kode_produk = 'AR', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS AR,
        SUM( IF ( kode_produk = 'AS', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS ASE,
        SUM( IF ( kode_produk = 'BB', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS BB,
        SUM( IF ( kode_produk = 'CG', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS CG,
        SUM( IF ( kode_produk = 'CGG', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS CGG,
        SUM( IF ( kode_produk = 'DB', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS DB,
        SUM( IF ( kode_produk = 'DEP', detailpenjualan.jumlah/isipcsdus,NULL ) ) AS DEP,
        SUM( IF ( kode_produk = 'DK', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS DK,
        SUM( IF ( kode_produk = 'DS', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS DS,
        SUM( IF ( kode_produk = 'SP', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SP,
        SUM( IF ( kode_produk = 'BBP', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS BBP,
        SUM( IF ( kode_produk = 'SPP', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SPP,
        SUM( IF ( kode_produk = 'CG5', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS CG5,
        SUM( IF ( kode_produk = 'SC', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SC,
        SUM( IF ( kode_produk = 'SP8', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SP8");
        $query->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang');
        $query->join('penjualan', 'detailpenjualan.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->whereBetween('tgltransaksi', [$dari, $sampai]);
        if ($request->kode_cabang != "") {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }
        if ($request->id_karyawan != "") {
            $query->where('penjualan.id_karyawan', $request->id_karyawan);
        }

        if ($request->kode_pelanggan != "") {
            $query->where('penjualan.kode_pelanggan', $request->kode_pelanggan);
        }

        $query->groupByRaw('penjualan.kode_pelanggan,nama_pelanggan');
        $rekappelanggan = $query->get();
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Pelanggan Periode $dari-$sampai-$time.xls");
        }
        return view('penjualan.laporan.cetak_rekappelanggan', compact('rekappelanggan', 'cabang', 'dari', 'sampai', 'salesman', 'pelanggan'));
    }

    public function laporanharganet()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('penjualan.laporan.frm.lap_harganet', compact('bulan'));
    }

    public function cetaklaporanharganet(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $harganet = DB::table('penjualan')
            ->selectRaw("SUM(bruto_AB) as bruto_AB,
		SUM(bruto_AR) as bruto_AR,
		SUM(bruto_AS) as bruto_AS,
		SUM(bruto_BB) as bruto_BB,
		SUM(bruto_BBP) as bruto_BBP,
		SUM(bruto_CG) as bruto_CG,
		SUM(bruto_CGG) as bruto_CGG,
		SUM(bruto_CG5) as bruto_CG5,
		SUM(bruto_DEP) as bruto_DEP,
		SUM(bruto_DS) as bruto_DS,
		SUM(bruto_SP) as bruto_SP,
		SUM(bruto_SPP) as bruto_SPP,
		SUM(bruto_SC) as bruto_SC,
		SUM(bruto_SP8) as bruto_SP8,

		SUM(qty_AB) as qty_AB,
		SUM(qty_AR) as qty_AR,
		SUM(qty_AS) as qty_AS,
		SUM(qty_BB) as qty_BB,
		SUM(qty_BBP) as qty_BBP,
		SUM(qty_CG) as qty_CG,
		SUM(qty_CGG) as qty_CGG,
		SUM(qty_CG5) as qty_CG5,
		SUM(qty_DEP) as qty_DEP,
		SUM(qty_DS) as qty_DS,
		SUM(qty_SP) as qty_SP,
		SUM(qty_SPP) as qty_SPP,
		SUM(qty_SC) as qty_SC,
		SUM(qty_SP8) as qty_SP8,


		SUM(qtydus_AB) as qtydus_AB,
		SUM(qtydus_AR) as qtydus_AR,
		SUM(qtydus_AS) as qtydus_AS,
		SUM(qtydus_BB) as qtydus_BB,
		SUM(qtydus_BBP) as qtydus_BBP,
		SUM(qtydus_CG) as qtydus_CG,
		SUM(qtydus_CGG) as qtydus_CGG,
		SUM(qtydus_CG5) as qtydus_CG5,
		SUM(qtydus_DEP) as qtydus_DEP,
		SUM(qtydus_DS) as qtydus_DS,
		SUM(qtydus_SP) as qtydus_SP,
		SUM(qtydus_SPP) as qtydus_SPP,
		SUM(qtydus_SC) as qtydus_SC,
		SUM(qtydus_SP8) as qtydus_SP8,

		SUM(potaida) as  potonganAIDA,
		SUM(IFNULL(potswan,0) + IFNULL(potstick,0) + IFNULL(potsp,0)) as potonganSWAN,

		SUM(IFNULL(qtydus_AB,0) + IFNULL(qtydus_AR,0) + IFNULL(qtydus_AS,0) + IFNULL(qtydus_CG,0) + IFNULL(qtydus_CGG,0) + IFNULL(qtydus_CG5,0)) as qtyAIDA,
		SUM(IFNULL(qtydus_BB,0) + IFNULL(qtydus_BBP,0) + IFNULL(qtydus_DEP,0) + IFNULL(qtydus_DS,0) + IFNULL(qtydus_SP,0) + IFNULL(qtydus_SPP,0) + IFNULL(qtydus_SC,0) + IFNULL(qtydus_SP8,0)) as qtySWAN,

		SUM(IFNULL(IFNULL(potaida,0) / (IFNULL(qtydus_AB,0) + IFNULL(qtydus_AR,0) + IFNULL(qtydus_AS,0) + IFNULL(qtydus_CG,0) + IFNULL(qtydus_CGG,0) + IFNULL(qtydus_CG5,0)),0)) as diskonaida,
		SUM(IFNULL((IFNULL(potswan,0) + IFNULL(potstick,0) + IFNULL(potsp,0)) / (IFNULL(qtydus_BB,0) + IFNULL(qtydus_BBP,0) + IFNULL(qtydus_DEP,0) + IFNULL(qtydus_DS,0) + IFNULL(qtydus_SP,0) + IFNULL(qtydus_SPP,0)+ IFNULL(qtydus_SC,0)+ IFNULL(qtydus_SP8,0)),0)) as diskonswan,

		SUM(IFNULL((IFNULL(potaida,0) / (IFNULL(qtydus_AB,0) + IFNULL(qtydus_AR,0) + IFNULL(qtydus_AS,0) + IFNULL(qtydus_CG,0) + IFNULL(qtydus_CGG,0) + IFNULL(qtydus_CG5,0))) * qtydus_AB,0)) as diskon_AB,
		SUM(IFNULL((IFNULL(potaida,0) / (IFNULL(qtydus_AB,0) + IFNULL(qtydus_AR,0) + IFNULL(qtydus_AS,0) + IFNULL(qtydus_CG,0) + IFNULL(qtydus_CGG,0) + IFNULL(qtydus_CG5,0))) * qtydus_AR,0)) as diskon_AR,
		SUM(IFNULL((IFNULL(potaida,0) / (IFNULL(qtydus_AB,0) + IFNULL(qtydus_AR,0) + IFNULL(qtydus_AS,0) + IFNULL(qtydus_CG,0) + IFNULL(qtydus_CGG,0) + IFNULL(qtydus_CG5,0))) * qtydus_AS,0)) as diskon_AS,
		SUM(IFNULL((IFNULL(potaida,0) / (IFNULL(qtydus_AB,0) + IFNULL(qtydus_AR,0) + IFNULL(qtydus_AS,0) + IFNULL(qtydus_CG,0) + IFNULL(qtydus_CGG,0) + IFNULL(qtydus_CG5,0))) * qtydus_CG,0)) as diskon_CG,
		SUM(IFNULL((IFNULL(potaida,0) / (IFNULL(qtydus_AB,0) + IFNULL(qtydus_AR,0) + IFNULL(qtydus_AS,0) + IFNULL(qtydus_CG,0) + IFNULL(qtydus_CGG,0) + IFNULL(qtydus_CG5,0))) * qtydus_CGG,0)) as diskon_CGG,
		SUM(IFNULL((IFNULL(potaida,0) / (IFNULL(qtydus_AB,0) + IFNULL(qtydus_AR,0) + IFNULL(qtydus_AS,0) + IFNULL(qtydus_CG,0) + IFNULL(qtydus_CGG,0) + IFNULL(qtydus_CG5,0))) * qtydus_CG5,0)) as diskon_CG5,
		SUM(IFNULL((IFNULL(potswan,0) + IFNULL(potstick,0) + IFNULL(potsp,0)) / (IFNULL(qtydus_BB,0) + IFNULL(qtydus_BBP,0) + IFNULL(qtydus_DEP,0) + IFNULL(qtydus_DS,0) + IFNULL(qtydus_SP,0) + IFNULL(qtydus_SPP,0) + IFNULL(qtydus_SC,0) + IFNULL(qtydus_SP8,0)) * qtydus_BB,0)) as diskon_BB,
		SUM(IFNULL((IFNULL(potswan,0) + IFNULL(potstick,0) + IFNULL(potsp,0)) / (IFNULL(qtydus_BB,0) + IFNULL(qtydus_BBP,0) + IFNULL(qtydus_DEP,0) + IFNULL(qtydus_DS,0) + IFNULL(qtydus_SP,0)+ IFNULL(qtydus_SPP,0) + IFNULL(qtydus_SC,0) + IFNULL(qtydus_SP8,0)) * qtydus_BBP,0)) as diskon_BBP,
		SUM(IFNULL((IFNULL(potswan,0) + IFNULL(potstick,0) + IFNULL(potsp,0)) / (IFNULL(qtydus_BB,0) + IFNULL(qtydus_BBP,0) + IFNULL(qtydus_DEP,0) + IFNULL(qtydus_DS,0) + IFNULL(qtydus_SP,0)+ IFNULL(qtydus_SPP,0) + IFNULL(qtydus_SC,0) + IFNULL(qtydus_SP8,0)) * qtydus_DEP,0)) as diskon_DEP,
		SUM(IFNULL((IFNULL(potswan,0) + IFNULL(potstick,0) + IFNULL(potsp,0)) / (IFNULL(qtydus_BB,0) + IFNULL(qtydus_BBP,0) + IFNULL(qtydus_DEP,0) + IFNULL(qtydus_DS,0) + IFNULL(qtydus_SP,0)+ IFNULL(qtydus_SPP,0) + IFNULL(qtydus_SC,0) + IFNULL(qtydus_SP8,0)) * qtydus_DS,0)) as diskon_DS,
		SUM(IFNULL((IFNULL(potswan,0) + IFNULL(potstick,0) + IFNULL(potsp,0)) / (IFNULL(qtydus_BB,0) + IFNULL(qtydus_BBP,0) + IFNULL(qtydus_DEP,0) + IFNULL(qtydus_DS,0) + IFNULL(qtydus_SP,0)+ IFNULL(qtydus_SPP,0) + IFNULL(qtydus_SC,0) + IFNULL(qtydus_SP8,0)) * qtydus_SP,0)) as diskon_SP,
		SUM(IFNULL((IFNULL(potswan,0) + IFNULL(potstick,0) + IFNULL(potsp,0)) / (IFNULL(qtydus_BB,0) + IFNULL(qtydus_BBP,0) + IFNULL(qtydus_DEP,0) + IFNULL(qtydus_DS,0) + IFNULL(qtydus_SP,0)+ IFNULL(qtydus_SPP,0) + IFNULL(qtydus_SC,0) + IFNULL(qtydus_SP8,0)) * qtydus_SPP,0)) as diskon_SPP,
		SUM(IFNULL((IFNULL(potswan,0) + IFNULL(potstick,0) + IFNULL(potsp,0)) / (IFNULL(qtydus_BB,0) + IFNULL(qtydus_BBP,0) + IFNULL(qtydus_DEP,0) + IFNULL(qtydus_DS,0) + IFNULL(qtydus_SP,0)+ IFNULL(qtydus_SPP,0) + IFNULL(qtydus_SC,0) + IFNULL(qtydus_SP8,0)) * qtydus_SC,0)) as diskon_SC,
		SUM(IFNULL((IFNULL(potswan,0) + IFNULL(potstick,0) + IFNULL(potsp,0)) / (IFNULL(qtydus_BB,0) + IFNULL(qtydus_BBP,0) + IFNULL(qtydus_DEP,0) + IFNULL(qtydus_DS,0) + IFNULL(qtydus_SP,0)+ IFNULL(qtydus_SPP,0) + IFNULL(qtydus_SC,0) + IFNULL(qtydus_SP8,0)) * qtydus_SP8,0)) as diskon_SP8,

		SUM(penyharga) as penyharga,

		SUM(IFNULL(retur_AB,0)) as retur_AB,
		SUM(IFNULL(retur_AR,0)) as retur_AR,
		SUM(IFNULL(retur_AS,0)) as retur_AS,
		SUM(IFNULL(retur_BB,0)) as retur_BB,
		SUM(IFNULL(retur_BBP,0)) as retur_BBP,
		SUM(IFNULL(retur_CG,0)) as retur_CG,
		SUM(IFNULL(retur_CGG,0)) as retur_AB,
		SUM(IFNULL(retur_CG5,0)) as retur_CG5,
		SUM(IFNULL(retur_DEP,0)) as retur_DEP,
		SUM(IFNULL(retur_DS,0)) as retur_DS,
		SUM(IFNULL(retur_SP,0)) as retur_SP,
		SUM(IFNULL(retur_SPP,0)) as retur_SPP,
		SUM(IFNULL(retur_SC,0)) as retur_SC,
		SUM(IFNULL(retur_SP8,0)) as retur_SP8")
            ->leftJoin(
                DB::raw("(
                SELECT
		dp.no_fak_penj,
		SUM(IF(b.kode_produk = 'AB',dp.subtotal,0)) as bruto_AB,
		SUM(IF(b.kode_produk = 'AR',dp.subtotal,0)) as bruto_AR,
		SUM(IF(b.kode_produk = 'AS',dp.subtotal,0)) as bruto_AS,
		SUM(IF(b.kode_produk = 'BB',dp.subtotal,0)) as bruto_BB,
		SUM(IF(b.kode_produk = 'BBP',dp.subtotal,0)) as bruto_BBP,
		SUM(IF(b.kode_produk = 'CG',dp.subtotal,0)) as bruto_CG,
		SUM(IF(b.kode_produk = 'CGG',dp.subtotal,0)) as bruto_CGG,
		SUM(IF(b.kode_produk = 'CG5',dp.subtotal,0)) as bruto_CG5,
		SUM(IF(b.kode_produk = 'DEP',dp.subtotal,0)) as bruto_DEP,
		SUM(IF(b.kode_produk = 'DS',dp.subtotal,0)) as bruto_DS,
		SUM(IF(b.kode_produk = 'SP',dp.subtotal,0)) as bruto_SP,
		SUM(IF(b.kode_produk = 'SPP',dp.subtotal,0)) as bruto_SPP,
		SUM(IF(b.kode_produk = 'SC',dp.subtotal,0)) as bruto_SC,
		SUM(IF(b.kode_produk = 'SP8',dp.subtotal,0)) as bruto_SP8,

		SUM(IF(b.kode_produk = 'AB' AND promo !=1 OR b.kode_produk = 'AB' AND promo IS NULL,dp.jumlah,0)) as   qty_AB,
		SUM(IF(b.kode_produk = 'AR' AND promo !=1 OR b.kode_produk = 'AR' AND promo IS NULL,dp.jumlah,0)) as   qty_AR,
		SUM(IF(b.kode_produk = 'AS' AND promo !=1 OR b.kode_produk = 'AS' AND promo IS NULL,dp.jumlah,0)) as   qty_AS,
		SUM(IF(b.kode_produk = 'BB' AND promo !=1 OR b.kode_produk = 'BB' AND promo IS NULL,dp.jumlah,0)) as   qty_BB,
		SUM(IF(b.kode_produk = 'BBP' AND promo !=1 OR b.kode_produk = 'BBP' AND promo IS NULL,dp.jumlah,0)) as   qty_BBP,
		SUM(IF(b.kode_produk = 'CG' AND promo !=1 OR b.kode_produk = 'CG' AND promo IS NULL,dp.jumlah,0)) as  qty_CG,
		SUM(IF(b.kode_produk = 'CGG' AND promo !=1 OR b.kode_produk = 'CGG' AND promo IS NULL,dp.jumlah,0)) as   qty_CGG,
		SUM(IF(b.kode_produk = 'CG5' AND promo !=1 OR b.kode_produk = 'CG5' AND promo IS NULL,dp.jumlah,0)) as   qty_CG5,
		SUM(IF(b.kode_produk = 'DEP' AND promo !=1 OR b.kode_produk = 'DEP' AND promo IS NULL,dp.jumlah,0)) as   qty_DEP,
		SUM(IF(b.kode_produk = 'DS' AND promo !=1 OR b.kode_produk = 'DS' AND promo IS NULL,dp.jumlah,0)) as   qty_DS,
		SUM(IF(b.kode_produk = 'SP' AND promo !=1 OR b.kode_produk = 'SP' AND promo IS NULL,dp.jumlah,0)) as   qty_SP,
		SUM(IF(b.kode_produk = 'SPP' AND promo !=1 OR b.kode_produk = 'SPP' AND promo IS NULL,dp.jumlah,0)) as   qty_SPP,
		SUM(IF(b.kode_produk = 'SC' AND promo !=1 OR b.kode_produk = 'SC' AND promo IS NULL,dp.jumlah,0)) as   qty_SC,
		SUM(IF(b.kode_produk = 'SP8' AND promo !=1 OR b.kode_produk = 'SP8' AND promo IS NULL,dp.jumlah,0)) as   qty_SP8,

		SUM(IF(b.kode_produk = 'AB' AND promo !=1 OR b.kode_produk = 'AB' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_AB,
		SUM(IF(b.kode_produk = 'AR' AND promo !=1 OR b.kode_produk = 'AR' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_AR,
		SUM(IF(b.kode_produk = 'AS' AND promo !=1 OR b.kode_produk = 'AS' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_AS,
		SUM(IF(b.kode_produk = 'BB' AND promo !=1 OR b.kode_produk = 'BB' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_BB,
		SUM(IF(b.kode_produk = 'BBP' AND promo !=1 OR b.kode_produk = 'BBP' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_BBP,
		SUM(IF(b.kode_produk = 'CG' AND promo !=1 OR b.kode_produk = 'CG' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as  qtydus_CG,
		SUM(IF(b.kode_produk = 'CGG' AND promo !=1 OR b.kode_produk = 'CGG' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_CGG,
		SUM(IF(b.kode_produk = 'CG5' AND promo !=1 OR b.kode_produk = 'CG5' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_CG5,
		SUM(IF(b.kode_produk = 'DEP' AND promo !=1 OR b.kode_produk = 'DEP' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_DEP,
		SUM(IF(b.kode_produk = 'DS' AND promo !=1 OR b.kode_produk = 'DS' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_DS,
		SUM(IF(b.kode_produk = 'SP' AND promo !=1 OR b.kode_produk = 'SP' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_SP,
		SUM(IF(b.kode_produk = 'SPP' AND promo !=1 OR b.kode_produk = 'SPP' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_SPP,
		SUM(IF(b.kode_produk = 'SC' AND promo !=1 OR b.kode_produk = 'SC' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_SC,
		SUM(IF(b.kode_produk = 'SP8' AND promo !=1 OR b.kode_produk = 'SP8' AND promo IS NULL,floor(dp.jumlah/mb.isipcsdus),0)) as   qtydus_SP8
		FROM detailpenjualan dp
		INNER JOIN barang b ON dp.kode_barang = b.kode_barang
		INNER JOIN master_barang mb ON b.kode_produk = mb.kode_produk
		GROUP BY dp.no_fak_penj
            ) detail"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'detail.no_fak_penj');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT retur.no_fak_penj,
                SUM(IF(b.kode_produk = 'AB',subtotal,0)) as   retur_AB,
                SUM(IF(b.kode_produk = 'AR',subtotal,0)) as   retur_AR,
                SUM(IF(b.kode_produk = 'AS',subtotal,0)) as   retur_AS,
                SUM(IF(b.kode_produk = 'BB',subtotal,0)) as   retur_BB,
                SUM(IF(b.kode_produk = 'BBP',subtotal,0)) as   retur_BBP,
                SUM(IF(b.kode_produk = 'CG',subtotal,0)) as   retur_CG,
                SUM(IF(b.kode_produk = 'CGG',subtotal,0)) as   retur_CGG,
                SUM(IF(b.kode_produk = 'CG5',subtotal,0)) as   retur_CG5,
                SUM(IF(b.kode_produk = 'DEP',subtotal,0)) as   retur_DEP,
                SUM(IF(b.kode_produk = 'DS',subtotal,0)) as   retur_DS,
                SUM(IF(b.kode_produk = 'SP',subtotal,0)) as   retur_SP,
                SUM(IF(b.kode_produk = 'SPP',subtotal,0)) as   retur_SPP,
                SUM(IF(b.kode_produk = 'SC',subtotal,0)) as   retur_SC,
                SUM(IF(b.kode_produk = 'SP8',subtotal,0)) as   retur_SP8
                FROM detailretur
                INNER JOIN retur ON detailretur.no_retur_penj = retur.no_retur_penj
                INNER JOIN barang b ON detailretur.kode_barang = b.kode_barang
                INNER JOIN master_barang mb ON b.kode_produk = mb.kode_produk
                WHERE tglretur BETWEEN '$dari' AND '$sampai'
                GROUP BY retur.no_fak_penj
            ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )

            ->whereBetween('tgltransaksi', [$dari, $sampai])
            ->first();

        $retur = DB::table('detailretur')
            ->selectRaw("SUM(IF(barang.kode_produk = 'AB',subtotal,0)) as   retur_AB,
		SUM(IF(barang.kode_produk = 'AR',subtotal,0)) as   retur_AR,
		SUM(IF(barang.kode_produk = 'AS',subtotal,0)) as   retur_AS,
		SUM(IF(barang.kode_produk = 'BB',subtotal,0)) as   retur_BB,
		SUM(IF(barang.kode_produk = 'BBP',subtotal,0)) as   retur_BBP,
		SUM(IF(barang.kode_produk = 'CG',subtotal,0)) as   retur_CG,
		SUM(IF(barang.kode_produk = 'CGG',subtotal,0)) as   retur_CGG,
		SUM(IF(barang.kode_produk = 'CG5',subtotal,0)) as   retur_CG5,
		SUM(IF(barang.kode_produk = 'DEP',subtotal,0)) as   retur_DEP,
		SUM(IF(barang.kode_produk = 'DS',subtotal,0)) as   retur_DS,
		SUM(IF(barang.kode_produk = 'SP',subtotal,0)) as   retur_SP,
		SUM(IF(barang.kode_produk = 'DK',subtotal,0)) as   retur_DK,
		SUM(IF(barang.kode_produk = 'SPP',subtotal,0)) as   retur_SPP,
		SUM(IF(barang.kode_produk = 'SC',subtotal,0)) as   retur_SC,
		SUM(IF(barang.kode_produk = 'SP8',subtotal,0)) as   retur_SP8,

		SUM(IF(barang.kode_produk = 'AB' AND jenis_retur='GB',subtotal,0)) as   returpeny_AB,
		SUM(IF(barang.kode_produk = 'AR' AND jenis_retur='GB',subtotal,0)) as   returpeny_AR,
		SUM(IF(barang.kode_produk = 'AS' AND jenis_retur='GB',subtotal,0)) as   returpeny_AS,
		SUM(IF(barang.kode_produk = 'BB' AND jenis_retur='GB',subtotal,0)) as   returpeny_BB,
		SUM(IF(barang.kode_produk = 'BBP' AND jenis_retur='GB',subtotal,0)) as   returpeny_BBP,
		SUM(IF(barang.kode_produk = 'CG' AND jenis_retur='GB',subtotal,0)) as   returpeny_CG,
		SUM(IF(barang.kode_produk = 'CGG' AND jenis_retur='GB',subtotal,0)) as   returpeny_CGG,
		SUM(IF(barang.kode_produk = 'CG5' AND jenis_retur='GB',subtotal,0)) as   returpeny_CG5,
		SUM(IF(barang.kode_produk = 'DEP' AND jenis_retur='GB',subtotal,0)) as   returpeny_DEP,
		SUM(IF(barang.kode_produk = 'DS' AND jenis_retur='GB',subtotal,0)) as   returpeny_DS,
		SUM(IF(barang.kode_produk = 'SP' AND jenis_retur='GB',subtotal,0)) as   returpeny_SP,
		SUM(IF(barang.kode_produk = 'DK' AND jenis_retur='GB',subtotal,0)) as   returpeny_DK,
		SUM(IF(barang.kode_produk = 'SPP' AND jenis_retur='GB',subtotal,0)) as   returpeny_SPP,
		SUM(IF(barang.kode_produk = 'SC' AND jenis_retur='GB',subtotal,0)) as   returpeny_SC,
		SUM(IF(barang.kode_produk = 'SP8' AND jenis_retur='GB',subtotal,0)) as   returpeny_SP8")
            ->join('retur', 'detailretur.no_retur_penj', '=', 'retur.no_retur_penj')
            ->join('barang', 'detailretur.kode_barang', '=', 'barang.kode_barang')
            ->join('master_barang', 'barang.kode_produk', '=', 'master_barang.kode_produk')
            ->whereBetween('tglretur', [$dari, $sampai])
            ->first();
        $produk = Barang::orderby('kode_produk')->get();
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Harga Net Periode $dari-$sampai-$time.xls");
        }
        return view('penjualan.laporan.cetak_harganet', compact('harganet', 'retur', 'produk', 'dari', 'sampai'));
    }


    public function laporanrekappenjualan()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_rekappenjualan', compact('cabang'));
    }

    public function cetaklaporanrekappenjualan(Request $request)
    {
        $dari = $request->dari;
        $tgl = explode("-", $dari);

        $sampai = $request->sampai;
        $jenislaporan = $request->jenislaporan;
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $salesman = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();
        if ($jenislaporan == 1) {
            $query = Detailpenjualan::query();
            $query->selectRaw("barang.nama_barang,barang.kode_produk,kategori_jenisproduk,SUM(detailpenjualan.subtotal) as jumlah");
            $query->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang');
            $query->join('master_barang', 'barang.kode_produk', '=', 'master_barang.kode_produk');
            $query->join('penjualan', 'detailpenjualan.no_fak_penj', '=', 'penjualan.no_fak_penj');
            $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->whereBetween('tgltransaksi', [$dari, $sampai]);
            if ($request->kode_cabang != "") {
                $query->where('karyawan.kode_cabang', $request->kode_cabang);
            }
            if ($request->id_karyawan != "") {
                $query->where('penjualan.id_karyawan', $request->id_karyawan);
            }
            $query->groupByRaw('barang.nama_barang,barang.kode_produk,kategori_jenisproduk');
            $query->orderBy('kategori_jenisproduk');
            $rekap = $query->get();

            $querypenjualan = Penjualan::query();
            $querypenjualan->selectRaw("SUM(potongan) as potongan,SUM(potistimewa) as potistimewa, SUM(penyharga) as penyharga, SUM(ppn) as ppn");
            $querypenjualan->whereBetween('tgltransaksi', [$dari, $sampai]);
            $querypenjualan->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            if ($request->kode_cabang != "") {
                $querypenjualan->where('karyawan.kode_cabang', $request->kode_cabang);
            }
            if ($request->id_karyawan != "") {
                $querypenjualan->where('penjualan.id_karyawan', $request->id_karyawan);
            }
            $penjualan = $querypenjualan->first();




            $queryretur = Retur::query();
            $queryretur->selectRaw("SUM(retur.total) as totalretur");
            $queryretur->join('penjualan', 'retur.no_fak_penj', '=', 'penjualan.no_fak_penj');
            $queryretur->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            $queryretur->whereBetween('tglretur', [$dari, $sampai]);
            if ($request->kode_cabang != "") {
                $queryretur->where('karyawan.kode_cabang', $request->kode_cabang);
            }
            if ($request->id_karyawan != "") {
                $queryretur->where('penjualan.id_karyawan', $request->id_karyawan);
            }
            $retur = $queryretur->first();
            if (isset($_POST['export'])) {
                $time = date("H:i:s");
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Rekap Penjualan Produk Periode $dari-$sampai-$time.xls");
            }
            return view('penjualan.laporan.cetak_rekappenjualanproduk', compact('rekap', 'cabang', 'salesman', 'dari', 'sampai', 'penjualan', 'retur'));
        } else if ($jenislaporan == 3) {
            $query = Detailpenjualan::query();
            $query->selectRaw("kode_produk,nama_barang,isipcsdus,
            SUM(IF(karyawan.kode_cabang = 'BDG',jumlah,0)) as BDG,
            SUM(IF(karyawan.kode_cabang = 'BDG',detailpenjualan.subtotal,0)) as JML_BDG,
            SUM(IF(karyawan.kode_cabang = 'BGR',jumlah,0)) as BGR,
            SUM(IF(karyawan.kode_cabang = 'BGR',detailpenjualan.subtotal,0)) as JML_BGR,
            SUM(IF(karyawan.kode_cabang = 'SKB',jumlah,0)) as SKB,
            SUM(IF(karyawan.kode_cabang = 'SKB',detailpenjualan.subtotal,0)) as JML_SKB,
            SUM(IF(karyawan.kode_cabang = 'PWT',jumlah,0)) as PWT,
            SUM(IF(karyawan.kode_cabang = 'PWT',detailpenjualan.subtotal,0)) as JML_PWT,
            SUM(IF(karyawan.kode_cabang = 'TGL',jumlah,0)) as TGL,
            SUM(IF(karyawan.kode_cabang = 'TGL',detailpenjualan.subtotal,0)) as JML_TGL,
            SUM(IF(karyawan.kode_cabang = 'TSM',jumlah,0)) as TSM,
            SUM(IF(karyawan.kode_cabang = 'TSM',detailpenjualan.subtotal,0)) as JML_TSM,
            SUM(IF(karyawan.kode_cabang = 'SBY',jumlah,0)) as SBY,
            SUM(IF(karyawan.kode_cabang = 'SBY',detailpenjualan.subtotal,0)) as JML_SBY,
            SUM(IF(karyawan.kode_cabang = 'SMR',jumlah,0)) as SMR,
            SUM(IF(karyawan.kode_cabang = 'SMR',detailpenjualan.subtotal,0)) as JML_SMR,
            SUM(IF(karyawan.kode_cabang = 'PST',jumlah,0)) as PST,
            SUM(IF(karyawan.kode_cabang = 'PST',detailpenjualan.subtotal,0)) as JML_PST,
            SUM(IF(karyawan.kode_cabang = 'KLT',jumlah,0)) as KLT,
            SUM(IF(karyawan.kode_cabang = 'KLT',detailpenjualan.subtotal,0)) as JML_KLT,
            SUM(IF(karyawan.kode_cabang = 'PWK',jumlah,0)) as PWK,
            SUM(IF(karyawan.kode_cabang = 'PWK',detailpenjualan.subtotal,0)) as JML_PWK,
            SUM(jumlah) as totalqty,
            SUM(detailpenjualan.subtotal) as JML");
            $query->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang');
            $query->join('penjualan', 'detailpenjualan.no_fak_penj', '=', 'penjualan.no_fak_penj');
            $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->whereBetween('tgltransaksi', [$dari, $sampai]);
            $query->where('promo', '!=', 1);
            if ($request->kode_cabang != "") {
                $query->where('karyawan.kode_cabang', $request->kode_cabang);
            }
            if ($request->id_karyawan != "") {
                $query->where('penjualan.id_karyawan', $request->id_karyawan);
            }
            $query->orwhereBetween('tgltransaksi', [$dari, $sampai]);
            $query->whereNull('promo');
            if ($request->kode_cabang != "") {
                $query->where('karyawan.kode_cabang', $request->kode_cabang);
            }
            if ($request->id_karyawan != "") {
                $query->where('penjualan.id_karyawan', $request->id_karyawan);
            }
            $query->groupByRaw("kode_produk,nama_barang,isipcsdus");
            $rekap = $query->get();
            //dd($rekap);
            if (isset($_POST['export'])) {
                $time = date("H:i:s");
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Rekap Penjualan Qty Periode $dari-$sampai-$time.xls");
            }
            return view('penjualan.laporan.cetak_rekappenjualanqty', compact('rekap', 'dari', 'sampai'));
            //dd($rekap);
        } else if ($jenislaporan == 2) {
            $bulan = $tgl[1];
            $tahun = $tgl[0];
            $query = Salesman::query();
            $query->selectRaw("karyawan.id_karyawan,
            nama_karyawan,
            karyawan.kode_cabang,
            AB,
            AR,
            ASE,
            BB,
            BBP,
            CG,
            CGG,
            CG5,
            DB,
            DEP,
            DK,
            DS,
            SC,
            SP,
            SP8,
            SP8P,
            totalbruto,
            totalretur,
            totalpotongan, totalpotistimewa,
            totalpenyharga,
            totalppn,
            totalbayar,
            penghapusanpiutang,
            diskonprogram,
            pps,
            pphk,
            vsp,
            kpbpb,
            wapu,
            pph22,
            lainnya,
            IFNULL(saldoawalpiutang,0) - IFNULL(piutanglama,0) + IFNULL(piutangpindahanbulanlalu,0) as saldoawalpiutang,
            IFNULL(saldoawalpiutang,0) -  IFNULL(piutanglamanow,0) + IFNULL(piutangpindahan,0) - IFNULL(piutanglamaberjalan,0) +
(IFNULL(totalbruto,0) - IFNULL(totalpotongan,0)-IFNULL(totalretur,0) - IFNULL(totalpotistimewa,0) - IFNULL(totalpenyharga,0) + IFNULL(totalppn,0))-IFNULL(totalbayarpiutang,0) as
            saldoakhirpiutang");
            $query->leftJoin(
                DB::raw("(
                    SELECT
                    p.id_karyawan,
                    SUM( IF ( kode_produk = 'AB', detailpenjualan.subtotal, NULL ) ) AS AB,
                    SUM( IF ( kode_produk = 'AR', detailpenjualan.subtotal, NULL ) ) AS AR,
                    SUM( IF ( kode_produk = 'AS', detailpenjualan.subtotal, NULL ) ) AS ASE,
                    SUM( IF ( kode_produk = 'BB', detailpenjualan.subtotal, NULL ) ) AS BB,
                    SUM( IF ( kode_produk = 'BBP', detailpenjualan.subtotal, NULL ) ) AS BBP,
                    SUM( IF ( kode_produk = 'CG', detailpenjualan.subtotal, NULL ) ) AS CG,
                    SUM( IF ( kode_produk = 'CGG', detailpenjualan.subtotal, NULL ) ) AS CGG,
                    SUM( IF ( kode_produk = 'CG5', detailpenjualan.subtotal, NULL ) ) AS CG5,
                    SUM( IF ( kode_produk = 'DB', detailpenjualan.subtotal, NULL ) ) AS DB,
                    SUM( IF ( kode_produk = 'DEP', detailpenjualan.subtotal, NULL ) ) AS DEP,
                    SUM( IF ( kode_produk = 'DK', detailpenjualan.subtotal, NULL ) ) AS DK,
                    SUM( IF ( kode_produk = 'DS', detailpenjualan.subtotal, NULL ) ) AS DS,
                    SUM( IF ( kode_produk = 'SC', detailpenjualan.subtotal, NULL ) ) AS SC,
                    SUM( IF ( kode_produk = 'SP8', detailpenjualan.subtotal, NULL ) ) AS SP8,
                    SUM( IF ( kode_produk = 'SP', detailpenjualan.subtotal, NULL ) ) AS SP,
                    SUM( IF ( kode_produk = 'SP8-P', detailpenjualan.subtotal, NULL ) ) AS SP8P
                    FROM
                        detailpenjualan
                        INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                        INNER JOIN penjualan p ON detailpenjualan.no_fak_penj = p.no_fak_penj
                    WHERE
                        tgltransaksi BETWEEN '$dari'
                        AND '$sampai'
                    GROUP BY
                        p.id_karyawan
                ) dp"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'dp.id_karyawan');
                }
            );

            $query->leftJoin(
                DB::raw("(
                    SELECT
                    salesbarunew,
                    SUM(retur.total) AS totalretur
                    FROM
                        retur
                        LEFT JOIN (
                            SELECT pj.no_fak_penj,
                        IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                        IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                        FROM penjualan pj
                        INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                        LEFT JOIN (
                            SELECT
                            id_move,no_fak_penj,
                            move_faktur.id_karyawan as salesbaru,
                            karyawan.kode_cabang  as cabangbaru
                            FROM move_faktur
                            INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                            WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
                        ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
                        ) pjmove ON (retur.no_fak_penj = pjmove.no_fak_penj)
                    WHERE
                        tglretur  BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                        salesbarunew
                ) retur"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'retur.salesbarunew');
                }
            );

            $query->leftJoin(
                DB::raw("(
                    SELECT
                    id_karyawan,
                    SUM(potongan) AS totalpotongan,
                    SUM(potistimewa) AS totalpotistimewa,
                    SUM(penyharga) AS totalpenyharga,
                    SUM(ppn) AS totalppn,
                    SUM(subtotal) AS totalbruto
                    FROM
                        penjualan
                    WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                        id_karyawan
                ) penjualan"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'penjualan.id_karyawan');
                }
            );

            $query->leftJoin(
                DB::raw("(
                    SELECT
                    historibayar.id_karyawan,
                        SUM(IF(status_bayar IS NULL,bayar,0)) as totalbayar,
                        SUM(IF(status_bayar='voucher' AND ket_voucher ='1',bayar,0)) as penghapusanpiutang,
                        SUM(IF(status_bayar='voucher' AND ket_voucher ='2',bayar,0)) as diskonprogram,
                        SUM(IF(status_bayar='voucher' AND ket_voucher ='3',bayar,0)) as pps,
                        SUM(IF(status_bayar='voucher' AND ket_voucher ='4',bayar,0)) as pphk,
                        SUM(IF(status_bayar='voucher' AND ket_voucher ='6',bayar,0)) as vsp,
                        SUM(IF(status_bayar='voucher' AND ket_voucher ='7',bayar,0)) as kpbpb,
                        SUM(IF(status_bayar='voucher' AND ket_voucher ='8',bayar,0)) as wapu,
                        SUM(IF(status_bayar='voucher' AND ket_voucher ='9',bayar,0)) as pph22,
                        SUM(IF(status_bayar='voucher' AND ket_voucher ='5',bayar,0)) as lainnya
                    FROM
                        historibayar
                        INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                    WHERE tglbayar BETWEEN '$dari' AND '$sampai'
                    GROUP BY
                    historibayar.id_karyawan
                ) hb"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hb.id_karyawan');
                }
            );

            $query->leftJoin(
                DB::raw("(
                    SELECT
                    pjmove.salesbarunew,
                    SUM(bayar) as totalbayarpiutang
                    FROM
                        historibayar
                        INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                        LEFT JOIN (
                            SELECT pj.no_fak_penj,
                            IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                            IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                            FROM penjualan pj
                            INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                            LEFT JOIN (
                                SELECT
                                id_move,no_fak_penj,
                                move_faktur.id_karyawan as salesbaru,
                                karyawan.kode_cabang  as cabangbaru
                                FROM move_faktur
                                INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                                WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$dari' GROUP BY no_fak_penj)
                            ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
                        ) pjmove ON (historibayar.no_fak_penj = pjmove.no_fak_penj)
                        WHERE tglbayar BETWEEN '$dari' AND '$sampai'
                        GROUP BY pjmove.salesbarunew
                ) hbpiutang"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'hbpiutang.salesbarunew');
                }
            );

            $query->leftJoin(
                DB::raw("(
                    SELECT id_karyawan,saldo_piutang as saldoawalpiutang
                    FROM saldoawal_piutang
                    WHERE bulan ='$bulan' AND tahun='$tahun'
                ) sp"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'sp.id_karyawan');
                }
            );

            $query->leftJoin(
                DB::raw("(
                    SELECT move_faktur.id_karyawan,
                    SUM(IF(tgltransaksi < '$dari',( IFNULL( total, 0 ) - IFNULL( totalreturbulanlalu, 0 ) - IFNULL( totalbayar, 0 )), 0 )) AS piutangpindahanbulanlalu,
                    SUM(IF(tgltransaksi < '$dari',( IFNULL( total, 0 ) - IFNULL( totalreturbulanlalu, 0 ) - IFNULL( totalbayar, 0 )), 0 )) AS piutangpindahan,
                    SUM(IF( tgltransaksi >= '$dari' AND tgltransaksi <= '$sampai',( IFNULL( total, 0 ) - IFNULL( totalreturberjalan, 0 ) - IFNULL( totalbayar, 0 )), 0 )) AS piutangberjalan
                    FROM move_faktur
                    INNER JOIN penjualan ON move_faktur.no_fak_penj = penjualan.no_fak_penj
                    LEFT JOIN (
                        SELECT retur.no_fak_penj AS no_fak_penj,
                        sum(IF( tglretur < '$dari',( IFNULL( retur.subtotal_pf, 0 ) - IFNULL( retur.subtotal_gb, 0 )), 0 )) AS totalreturbulanlalu,
		                sum(IF( tglretur >= '$dari' AND tglretur <= '$sampai',( IFNULL( retur.subtotal_pf, 0 ) - IFNULL( retur.subtotal_gb, 0 )), 0 )) AS totalreturberjalan
                        FROM
                        retur
                        GROUP BY
                        retur.no_fak_penj ) retur ON (move_faktur.no_fak_penj = retur.no_fak_penj)

                    LEFT JOIN (
                        SELECT no_fak_penj,sum( historibayar.bayar ) AS totalbayar
                        FROM historibayar
                        WHERE tglbayar < '$dari'
                        GROUP BY no_fak_penj
                    ) hb ON (move_faktur.no_fak_penj = hb.no_fak_penj)
                    WHERE tgl_move = '$dari'  GROUP BY move_faktur.id_karyawan
                ) piutangpindahan"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'piutangpindahan.id_karyawan');
                }
            );

            $query->leftJoin(
                DB::raw("(
                    SELECT
                        move_faktur.id_karyawan_lama,
                        SUM(IF( tgltransaksi < '$dari',( IFNULL( total, 0 ) - IFNULL( totalreturbulanlalu, 0 ) - IFNULL( totalbayar, 0 )), 0 )) AS piutanglama,
                        SUM(IF( tgltransaksi < '$dari',( IFNULL( total, 0 ) - IFNULL( totalreturbulanlalu, 0 ) - IFNULL( totalreturberjalan, 0 ) - IFNULL( totalbayar, 0 )), 0 )) AS piutanglamanow,
                        SUM(IF( tgltransaksi >= '$dari' AND tgltransaksi <= '2022-06-30',( IFNULL( total, 0 ) - IFNULL( totalreturberjalan, 0 ) - IFNULL( totalbayar, 0 )), 0 )) AS piutanglamaberjalan
                    FROM
                        move_faktur
                        INNER JOIN penjualan ON move_faktur.no_fak_penj = penjualan.no_fak_penj
                        LEFT JOIN (
                        SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            sum(IF( tglretur < '$dari',( IFNULL( retur.subtotal_pf, 0 ) - IFNULL( retur.subtotal_gb, 0 )), 0 )) AS totalreturbulanlalu,
                            sum(IF( tglretur >= '$dari' AND tglretur <= '2022-06-30',( IFNULL( retur.subtotal_pf, 0 ) - IFNULL( retur.subtotal_gb, 0 )), 0 )) AS totalreturberjalan
                            FROM retur
                            GROUP BY retur.no_fak_penj ) retur ON ( move_faktur.no_fak_penj = retur.no_fak_penj )
                            LEFT JOIN ( SELECT no_fak_penj, sum( historibayar.bayar ) AS totalbayar FROM historibayar WHERE tglbayar < '$dari' GROUP BY no_fak_penj ) hb ON ( move_faktur.no_fak_penj = hb.no_fak_penj )
                        WHERE
                            tgl_move = '$dari'
                    GROUP BY
                        move_faktur.id_karyawan_lama
                ) piutanglama"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'piutanglama.id_karyawan_lama');
                }
            );

            if ($request->kode_cabang != "") {
                $query->where('karyawan.kode_cabang', $request->kode_cabang);
            } else {
                if ($dari < '2020-01-01') {
                    $query->where('karyawan.kode_cabang', '!=', 'GRT');
                }
            }
            if ($request->id_karyawan != "") {
                $query->where('karyawan.id_karyawan', $request->id_karyawan);
            }
            $query->orderBy('karyawan.kode_cabang');
            $query->orderBy('karyawan.id_karyawan');

            $rekap = $query->get();
            if (isset($_POST['export'])) {
                $time = date("H:i:s");
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Rekap Penjualan Periode $dari-$sampai-$time.xls");
            }
            return view('penjualan.laporan.cetak_rekappenjualan', compact('rekap', 'dari', 'sampai', 'cabang', 'salesman'));
        } else if ($jenislaporan == 4) {
            $query = Detailretur::query();
            $query->selectRaw("penjualan.id_karyawan,nama_karyawan,karyawan.kode_cabang,
            SUM( IF ( kode_produk = 'AB', detailretur.jumlah/isipcsdus, NULL ) ) AS JML_AB,
            SUM( IF ( kode_produk = 'AB', detailretur.subtotal, NULL ) ) AS AB,
            SUM( IF ( kode_produk = 'AR', detailretur.jumlah/isipcsdus, NULL ) ) AS JML_AR,
            SUM( IF ( kode_produk = 'AR', detailretur.subtotal, NULL ) ) AS AR,
            SUM( IF ( kode_produk = 'AS', detailretur.jumlah/isipcsdus, NULL ) ) AS JML_ASE,
            SUM( IF ( kode_produk = 'AS', detailretur.subtotal, NULL ) ) AS ASE,
            SUM( IF ( kode_produk = 'BB', detailretur.jumlah/isipcsdus, NULL ) ) AS JML_BB,
            SUM( IF ( kode_produk = 'BB', detailretur.subtotal, NULL ) ) AS BB,
            SUM( IF ( kode_produk = 'CG', detailretur.jumlah/isipcsdus, NULL ) ) AS JML_CG,
            SUM( IF ( kode_produk = 'CG', detailretur.subtotal, NULL ) ) AS CG,
            SUM( IF ( kode_produk = 'CGG',detailretur.jumlah/isipcsdus, NULL ) ) AS JML_CGG,
            SUM( IF ( kode_produk = 'CGG',detailretur.subtotal, NULL ) ) AS CGG,
            SUM( IF ( kode_produk = 'DB',detailretur.jumlah/isipcsdus, NULL ) ) AS JML_DB,
            SUM( IF ( kode_produk = 'DB',detailretur.subtotal, NULL ) ) AS DB,
            SUM( IF ( kode_produk = 'DEP',detailretur.jumlah/isipcsdus,NULL ) ) AS JML_DEP,
            SUM( IF ( kode_produk = 'DEP',detailretur.subtotal,NULL ) ) AS DEP,
            SUM( IF ( kode_produk = 'DK',detailretur.jumlah/isipcsdus, NULL ) ) AS JML_DK,
            SUM( IF ( kode_produk = 'DK',detailretur.subtotal, NULL ) ) AS DK,
            SUM( IF ( kode_produk = 'DS',detailretur.jumlah/isipcsdus, NULL ) ) AS JML_DS,
            SUM( IF ( kode_produk = 'DS',detailretur.subtotal, NULL ) ) AS DS,
            SUM( IF ( kode_produk = 'SP',detailretur.jumlah/isipcsdus, NULL ) ) AS JML_SP,
            SUM( IF ( kode_produk = 'SP',detailretur.subtotal, NULL ) ) AS SP,
            SUM( IF ( kode_produk = 'SPP',detailretur.jumlah/isipcsdus, NULL ) ) AS JML_SPP,
            SUM( IF ( kode_produk = 'SPP',detailretur.subtotal, NULL ) ) AS SPP,
            SUM( IF ( kode_produk = 'SP8',detailretur.jumlah/isipcsdus, NULL ) ) AS JML_SP8,
            SUM( IF ( kode_produk = 'SP8',detailretur.subtotal, NULL ) ) AS SP8,
            SUM( IF ( kode_produk = 'SC',detailretur.jumlah/isipcsdus, NULL ) ) AS JML_SC,
            SUM( IF ( kode_produk = 'SC',detailretur.subtotal, NULL ) ) AS SC,
            SUM(detailretur.subtotal) as totalretur,
            total_gb");
            $query->join('barang', 'detailretur.kode_barang', '=', 'barang.kode_barang');
            $query->join('retur', 'detailretur.no_retur_penj', '=', 'retur.no_retur_penj');
            $query->join('penjualan', 'retur.no_fak_penj', '=', 'penjualan.no_fak_penj');
            $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->leftJoin(
                DB::raw("(
                    SELECT id_karyawan,SUM(subtotal_gb) as total_gb FROM retur
                    INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                    WHERE tglretur BETWEEN '$dari' AND '$sampai'
                    GROUP BY id_karyawan
                ) penj"),
                function ($join) {
                    $join->on('penjualan.id_karyawan', '=', 'penj.id_karyawan');
                }
            );
            $query->whereBetween('tglretur', [$dari, $sampai]);
            if ($request->kode_cabang != "") {
                $query->where('karyawan.kode_cabang', $request->kode_cabang);
            }
            if ($request->id_karyawan != "") {
                $query->where('penjualan.id_karyawan', $request->id_karyawan);
            }
            $query->groupByRaw('penjualan.id_karyawan,nama_karyawan,karyawan.kode_cabang,total_gb');
            $query->orderBy('karyawan.kode_cabang');
            $query->orderBy('nama_karyawan');
            $query->get();
            $rekap = $query->get();
            if (isset($_POST['export'])) {
                $time = date("H:i:s");
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Rekap Retur Periode $dari-$sampai-$time.xls");
            }
            return view('penjualan.laporan.cetak_rekapretur', compact('rekap', 'dari', 'sampai', 'cabang', 'salesman'));
        } else if ($jenislaporan == 5) {
            $tgl_aup = $request->tgl_aup;
            $query = Penjualan::query();
            $query->selectRaw("penjualan.kode_pelanggan,nama_pelanggan,pasar,hari,pelanggan.jatuhtempo,penjualan.id_karyawan,nama_karyawan,karyawan.kode_cabang,cabangbarunew,salesbarunew,
            CASE
            WHEN datediff('$tgl_aup', tgltransaksi) <= 15 THEN
                    ((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS duaminggu,
            CASE
            WHEN datediff('$tgl_aup', tgltransaksi) <= 31  AND datediff('$tgl_aup', tgltransaksi) > 15 THEN
                    ((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS satubulan,
            CASE
            WHEN datediff('$tgl_aup', tgltransaksi) <= 46  AND datediff('$tgl_aup', tgltransaksi) > 31 THEN
                    ((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS satubulan15,
            CASE
            WHEN datediff('$tgl_aup', tgltransaksi) <= 60  AND datediff('$tgl_aup', tgltransaksi) > 46 THEN
                    ((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS duabulan,
            CASE
            WHEN datediff('$tgl_aup', tgltransaksi) > 60 AND datediff('$tgl_aup', tgltransaksi) <= 90 THEN
                    ((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS lebihtigabulan,
            CASE
            WHEN datediff('$tgl_aup', tgltransaksi) > 90 AND datediff('$tgl_aup', tgltransaksi) <= 180 THEN
                    ((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS enambulan,
            CASE
            WHEN datediff('$tgl_aup', tgltransaksi) > 180 AND datediff('$tgl_aup', tgltransaksi) <= 360 THEN
                    ((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS duabelasbulan,
            CASE
            WHEN datediff('$tgl_aup', tgltransaksi) > 360 AND datediff('$tgl_aup', tgltransaksi) <= 720 THEN
                    ((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS duatahun,
            CASE
            WHEN datediff('$tgl_aup', tgltransaksi) > 720 THEN
                    ((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0)))-(ifnull(jmlbayar,0) ) END AS lebihduatahun");
            $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
            $query->leftJoin(
                DB::raw("(
                    SELECT no_fak_penj,sum( historibayar.bayar ) AS jmlbayar
				FROM historibayar
				WHERE tglbayar <= '$tgl_aup'
				GROUP BY no_fak_penj
                ) hblalu"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
                }
            );

            $query->leftJoin(
                DB::raw("(
                    SELECT retur.no_fak_penj AS no_fak_penj,
                    SUM(total) AS total
                    FROM
                        retur
                    WHERE tglretur <= '$tgl_aup'
                    GROUP BY
                        retur.no_fak_penj
                ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            );

            $query->leftJoin(
                DB::raw("(
                    SELECT pj.no_fak_penj,
                    IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                    IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                    FROM penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                    id_move,no_fak_penj,
                    move_faktur.id_karyawan as salesbaru,
                    karyawan.kode_cabang  as cabangbaru
                    FROM move_faktur
                    INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                    WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$tgl_aup' GROUP BY no_fak_penj)
                    ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
                ) pjmove"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
                }
            );

            $query->where('jenistransaksi', '!=', 'tunai');
            $query->where('tgltransaksi', '<=', $tgl_aup);
            $query->whereRaw('(ifnull(penjualan.total,0) - (ifnull(retur.total,0))) != IFNULL(jmlbayar,0)');
            if ($request->kode_cabang != "") {
                $query->where('cabangbarunew', $request->kode_cabang);
            } else {
                if ($tgl_aup < '2020-01-01') {
                    $query->where('cabangbarunew', '!=', 'GRT');
                }
            }
            $query->orderBy('cabangbarunew', 'asc');
            $query->orderBy('salesbarunew', 'asc');
            $rekap = $query->get();
            if (isset($_POST['export'])) {
                $time = date("H:i:s");
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Rekap AUP Periode $dari-$sampai-$time.xls");
            }
            return view('penjualan.laporan.cetak_rekapaup', compact('rekap', 'tgl_aup',  'cabang'));
        }
    }

    public function saldoawalpiutang()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('penjualan.saldoawalpiutang', compact('bulan', 'cabang'));
    }

    public function loadsaldoawalpiutang(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $saldoawalpiutang = DB::table('saldoawal_piutang')
            ->join('karyawan', 'saldoawal_piutang.id_karyawan', '=', 'karyawan.id_karyawan')
            ->select('saldoawal_piutang.*', 'nama_karyawan')
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        return view('penjualan.loadsaldoawalpiutang', compact('saldoawalpiutang'));
    }

    public function generatesaldoawalpiutang(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $bulanlalu = $bulan - 1;
        $tanggal = $tahun . "-" . $bulanlalu . "-01";
        $akhirtanggal  = date('Y-m-t', strtotime($tanggal));

        $piutang = DB::table('penjualan')
            ->selectRaw("salesbarunew,SUM((IFNULL(penjualan.total,0))-(IFNULL(retur.total,0))-IFNULL(jmlbayar,0))  as saldopiutang")
            ->leftJoin(
                DB::raw("(
                    SELECT no_fak_penj,sum( historibayar.bayar ) AS jmlbayar
                    FROM historibayar
                    WHERE tglbayar <= '$akhirtanggal'
                    GROUP BY no_fak_penj
                ) hblalu"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'hblalu.no_fak_penj');
                }
            )

            ->leftJoin(
                DB::raw("(
                    SELECT retur.no_fak_penj AS no_fak_penj,
                    SUM(total) AS total
                    FROM
                        retur
                    WHERE tglretur <= '$akhirtanggal'
                    GROUP BY
                        retur.no_fak_penj
                ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )

            ->leftJoin(
                DB::raw("(
                    SELECT pj.no_fak_penj,
                    IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                    IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                    FROM penjualan pj
                    INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT
                        id_move,no_fak_penj,
                        move_faktur.id_karyawan as salesbaru,
                        karyawan.kode_cabang  as cabangbaru
                        FROM move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE id_move IN (SELECT max(id_move) FROM move_faktur WHERE tgl_move <= '$akhirtanggal' GROUP BY no_fak_penj)
                    ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
                ) pjmove"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'pjmove.no_fak_penj');
                }
            )

            ->where('cabangbarunew', $kode_cabang)
            ->where('penjualan.jenistransaksi', '!=', 'tunai')
            ->where('tgltransaksi', '<=', $akhirtanggal)
            ->whereRaw('(ifnull(penjualan.total,0) - (ifnull(retur.total,0))) != IFNULL(jmlbayar,0)')
            ->groupBy('salesbarunew')
            ->get();

        foreach ($piutang as $p) {
            $kodesales = substr($p->salesbarunew, 4, 2);
            if (Strlen($bulan) == 2) {
                $bln = $bulan;
            } else {
                $bln = "0" . $bulan;
            }
            $thn = substr($tahun, 2, 2);
            $kodesaldoawalpiutang = "SP" . $kode_cabang . $bln . $thn . $kodesales;

            $cek = DB::table('saldoawal_piutang')->where('kode_saldoawalpiutang', $kodesaldoawalpiutang)->count();
            if (empty($cek)) {
                $data = [
                    'kode_saldoawalpiutang' => $kodesaldoawalpiutang,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'id_karyawan' => $p->salesbarunew,
                    'saldo_piutang' => $p->saldopiutang
                ];

                DB::table('saldoawal_piutang')->insert($data);
            } else {
                $data = [
                    'saldo_piutang' => $p->saldopiutang
                ];

                DB::table('saldoawal_piutang')->where('kode_saldoawalpiutang', $kodesaldoawalpiutang)->update($data);
            }
        }
    }

    public function updatepending($no_fak_penj)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $faktur = DB::table('penjualan')->where('no_fak_penj', $no_fak_penj)->first();

        $kode_pelanggan = $faktur->kode_pelanggan;
        $pelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->first();
        $limitpel = $pelanggan->limitpel;


        DB::beginTransaction();
        try {
            if ($faktur->status_lunas == 1) {
                DB::table('penjualan')->where('no_fak_penj', $no_fak_penj)->update(['status' => 2]);
            } else {
                $piutang = DB::table('penjualan')
                    ->selectRaw('
                        SUM(((IFNULL(penjualan.total, 0)) - (IFNULL(retur.total, 0)))) AS totalpiutang,
                        SUM(jmlbayar) AS jmlbayar
                        ')
                    ->leftJoin(
                        DB::raw("(
                            SELECT historibayar.no_fak_penj,
                            IFNULL(SUM(bayar), 0) AS jmlbayar
                            FROM
                            historibayar
                            GROUP BY historibayar.no_fak_penj
                        ) hb"),
                        function ($join) {
                            $join->on('penjualan.no_fak_penj', '=', 'hb.no_fak_penj');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(
                            SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            SUM(total) AS total
                            FROM
                                retur
                            GROUP BY
                                retur.no_fak_penj
                        ) retur"),
                        function ($join) {
                            $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                        }
                    )
                    ->where('penjualan.kode_pelanggan', $kode_pelanggan)
                    ->where('penjualan.status_lunas', '!=', 1)
                    ->orWhere('penjualan.kode_pelanggan', $kode_pelanggan)
                    ->whereNull('penjualan.status_lunas')
                    ->first();

                // dd($piutang);
                $sisapiutang = $piutang->totalpiutang - $piutang->jmlbayar;
                $totalpiutang  = $sisapiutang + $faktur->total;



                if ($sisapiutang <= $limitpel) {
                    DB::table('penjualan')->where('no_fak_penj', $no_fak_penj)->update(['status' => 2]);
                }
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            return Redirect::back()->with(['success' => 'Data Gagal Di Update']);
        }
    }

    public function rekappenjualandashboard(Request $request)
    {
        $exclude = ['TSM', 'GRT'];
        $salesgarut = ['STSM05', 'STSM09', 'STSM11'];
        $bulan = $request->bulan;
        $tahun =  $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $query = Penjualan::query();
        $query->selectRaw("
            karyawan.kode_cabang AS kode_cabang,nama_cabang,
            (ifnull( SUM( penjualan.subtotal ), 0 ) ) AS totalbruto,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.subtotal,0)),0) as totalbrutopending,
			ifnull(totalretur,0) as totalretur,
			ifnull(totalreturpending,0) as totalreturpending,

			ifnull( SUM( penjualan.penyharga ), 0 ) AS totalpenyharga,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.penyharga,0)),0) as totalpenyhargapending,


			ifnull( SUM( penjualan.potongan ), 0 ) AS totalpotongan,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.potongan,0)),0) as totalpotonganpending,

			ifnull( SUM( penjualan.potistimewa ), 0 ) AS totalpotistimewa,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.potistimewa,0)),0) as totalpotistimewapending,

            ifnull( SUM( penjualan.ppn ), 0 ) AS totalppn,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.ppn,0)),0) as totalppnpending
            ");
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        $query->leftJoin(
            DB::raw("(
                    SELECT karyawan.kode_cabang, SUM(retur.total )AS totalretur ,
                    SUM(IF(penjualan.`status`=1,retur.total,0)) as totalreturpending
                    FROM retur
                    INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                    WHERE tglretur BETWEEN '$dari' AND '$sampai'
                    GROUP BY karyawan.kode_cabang
                ) retur"),
            function ($join) {
                $join->on('karyawan.kode_cabang', '=', 'retur.kode_cabang');
            }
        );

        if ($bulan < 9 && $tahun <= 2022) {
            $query->whereNotIn('karyawan.kode_cabang', $exclude);
        }

        if (Auth::user()->id == 82) {
            $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB', 'BTN');
            $query->whereIn('karyawan.kode_cabang', $wilayah_barat);
        } else if (Auth::user()->id == 97) {
            $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
            $query->whereIn('karyawan.kode_cabang', $wilayah_timur);
        }
        $query->whereBetween('tgltransaksi', [$dari, $sampai]);
        $query->groupByRaw('karyawan.kode_cabang,nama_cabang,totalretur,totalreturpending');
        $rekappenjualancabang = $query->get();

        if ($bulan < 9 && $tahun <= 2022) {
            $rekappenjualantsm = DB::table('penjualan')
                ->selectRaw("
            karyawan.kode_cabang AS kode_cabang,nama_cabang,
            (ifnull( SUM( penjualan.subtotal ), 0 ) ) AS totalbruto,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.subtotal,0)),0) as totalbrutopending,
			ifnull(totalretur,0) as totalretur,
			ifnull(totalreturpending,0) as totalreturpending,

			ifnull( SUM( penjualan.penyharga ), 0 ) AS totalpenyharga,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.penyharga,0)),0) as totalpenyhargapending,


			ifnull( SUM( penjualan.potongan ), 0 ) AS totalpotongan,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.potongan,0)),0) as totalpotonganpending,

			ifnull( SUM( penjualan.potistimewa ), 0 ) AS totalpotistimewa,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.potistimewa,0)),0) as totalpotistimewapending
            ")
                ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->leftJoin(
                    DB::raw("(
                    SELECT karyawan.kode_cabang, SUM(retur.total )AS totalretur ,
                    SUM(IF(penjualan.`status`=1,retur.total,0)) as totalreturpending
                    FROM retur
                    INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                    WHERE tglretur BETWEEN '$dari' AND '$sampai'
                    AND penjualan.id_karyawan NOT IN ('STSM05', 'STSM09', 'STSM11')
                    GROUP BY karyawan.kode_cabang
                ) retur"),
                    function ($join) {
                        $join->on('karyawan.kode_cabang', '=', 'retur.kode_cabang');
                    }
                )
                ->whereBetween('tgltransaksi', [$dari, $sampai])
                ->where('karyawan.kode_cabang', 'TSM')
                ->whereNotIn('karyawan.id_karyawan', $salesgarut)
                ->groupByRaw('karyawan.kode_cabang,nama_cabang,totalretur,totalreturpending')
                ->first();

            $rekappenjualangrt = DB::table('penjualan')
                ->selectRaw("
            karyawan.kode_cabang AS kode_cabang,nama_cabang,
            (ifnull( SUM( penjualan.subtotal ), 0 ) ) AS totalbruto,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.subtotal,0)),0) as totalbrutopending,
			ifnull(totalretur,0) as totalretur,
			ifnull(totalreturpending,0) as totalreturpending,

			ifnull( SUM( penjualan.penyharga ), 0 ) AS totalpenyharga,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.penyharga,0)),0) as totalpenyhargapending,


			ifnull( SUM( penjualan.potongan ), 0 ) AS totalpotongan,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.potongan,0)),0) as totalpotonganpending,

			ifnull( SUM( penjualan.potistimewa ), 0 ) AS totalpotistimewa,
			ifnull(SUM(IF(penjualan.`status`=1,penjualan.potistimewa,0)),0) as totalpotistimewapending
            ")
                ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
                ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->leftJoin(
                    DB::raw("(
                    SELECT karyawan.kode_cabang, SUM(retur.total )AS totalretur ,
                    SUM(IF(penjualan.`status`=1,retur.total,0)) as totalreturpending
                    FROM retur
                    INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                    WHERE tglretur BETWEEN '$dari' AND '$sampai'
                    AND penjualan.id_karyawan  IN ('STSM05', 'STSM09', 'STSM11')
                    GROUP BY karyawan.kode_cabang
                ) retur"),
                    function ($join) {
                        $join->on('karyawan.kode_cabang', '=', 'retur.kode_cabang');
                    }
                )
                ->whereBetween('tgltransaksi', [$dari, $sampai])
                ->where('karyawan.kode_cabang', 'TSM')
                ->whereIn('karyawan.id_karyawan', $salesgarut)
                ->groupByRaw('karyawan.kode_cabang,nama_cabang,totalretur,totalreturpending')
                ->first();
        } else {
            $rekappenjualantsm = null;
            $rekappenjualangrt = null;
        }
        return view('penjualan.dashboard.rekappenjualandashboard', compact('rekappenjualancabang', 'dari', 'sampai', 'rekappenjualantsm', 'rekappenjualangrt', 'bulan', 'tahun'));
    }

    public function rekapkasbesardashboard(Request $request)
    {
        $salesgarut = ['STSM05', 'STSM09', 'STSM11'];
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $query = Pembayaran::query();
        $query->selectRaw("karyawan.kode_cabang,nama_cabang,SUM(IF(status_bayar='voucher',bayar,0)) as voucher,SUM(IF(status_bayar IS NULL,bayar,0)) as cashin");
        $query->join('karyawan', 'historibayar.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        $query->whereBetween('tglbayar', [$dari, $sampai]);
        if ($bulan < 9 && $tahun <= 2022) {
            $query->where('karyawan.kode_cabang', '!=', 'TSM');
        }

        if (Auth::user()->id == 82) {
            $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB', 'BTN');
            $query->whereIn('karyawan.kode_cabang', $wilayah_barat);
        } else if (Auth::user()->id == 97) {
            $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
            $query->whereIn('karyawan.kode_cabang', $wilayah_timur);
        }
        $query->groupByRaw('karyawan.kode_cabang,nama_cabang');
        $kasbesar = $query->get();


        if ($bulan < 9 && $tahun <= 2022) {
            $kasbesartsm = DB::table('historibayar')
                ->selectRaw("karyawan.kode_cabang,nama_cabang,SUM(IF(status_bayar='voucher',bayar,0)) as voucher,SUM(IF(status_bayar IS NULL,bayar,0)) as cashin")
                ->join('karyawan', 'historibayar.id_karyawan', '=', 'karyawan.id_karyawan')
                ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->whereBetween('tglbayar', [$dari, $sampai])
                ->where('karyawan.kode_cabang', 'TSM')
                ->whereNotIn('historibayar.id_karyawan', $salesgarut)
                ->groupByRaw('karyawan.kode_cabang,nama_cabang')
                ->first();

            $kasbesargrt = DB::table('historibayar')
                ->selectRaw("karyawan.kode_cabang,nama_cabang,SUM(IF(status_bayar='voucher',bayar,0)) as voucher,SUM(IF(status_bayar IS NULL,bayar,0)) as cashin")
                ->join('karyawan', 'historibayar.id_karyawan', '=', 'karyawan.id_karyawan')
                ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->whereBetween('tglbayar', [$dari, $sampai])
                ->where('karyawan.kode_cabang', 'TSM')
                ->whereIn('historibayar.id_karyawan', $salesgarut)
                ->groupByRaw('karyawan.kode_cabang,nama_cabang')
                ->first();
        } else {
            $kasbesartsm = null;
            $kasbesargrt = null;
        }

        return view('penjualan.dashboard.rekapkasbesardashboard', compact('kasbesar', 'kasbesartsm', 'kasbesargrt', 'bulan', 'tahun'));
    }


    public function getfaktur(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $faktur = DB::table('penjualan')->where('id_karyawan', $id_karyawan)
            ->where('status_lunas', 2)
            ->get();
        foreach ($faktur as $d) {
            echo "<option>" . $d->no_fak_penj . "</option>";
        }
    }


    public function previewfaktur(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $tgltransaksi = $request->tgltransaksi;
        $id_karyawan = $request->id_karyawan;
        $salesman = Salesman::where('id_karyawan', $id_karyawan)->first();
        $kode_pelanggan = $request->kode_pelanggan;
        $pelanggan = Pelanggan::where('kode_pelanggan', $kode_pelanggan)->first();
        $limitpel = $request->limitpel;
        $sisapiutang = $request->sisapiutang;
        $jenistransaksi = $request->jenistransaksi;
        $jenisbayartunai = $request->jenisbayartunai;
        $jenisbayar = $jenisbayartunai == "transfer" ? $jenisbayartunai : $request->jenisbayar;
        $subtotal = $request->subtotal;
        $jatuhtempo = $request->jatuhtempo;
        $bruto = $request->bruto;
        $nama_pelanggan = $request->nama_pelanggan;
        $id_admin = Auth::user()->id;
        $keterangan = $request->keterangan;
        //Potongan
        $potaida        = str_replace(".", "", $request->potaida);
        if (empty($potaida)) {
            $potaida = 0;
        } else {
            $potaida = $potaida;
        }
        $potswan        = str_replace(".", "", $request->potswan);
        if (empty($potswan)) {
            $potswan = 0;
        } else {
            $potswan = $potswan;
        }
        $potstick       = str_replace(".", "", $request->potstick);
        if (empty($potstick)) {
            $potstick = 0;
        } else {
            $potstick = $potstick;
        }
        $potsp       = str_replace(".", "", $request->potsp);
        if (empty($potsp)) {
            $potsp = 0;
        } else {
            $potsp = $potsp;
        }
        $potsb       = str_replace(".", "", $request->potsb);
        if (empty($potsb)) {
            $potsambal = 0;
        } else {
            $potsambal = $potsb;
        }

        // Voucher
        $voucher       = str_replace(".", "", $request->voucher);
        if (empty($voucher)) {
            $voucher = 0;
        } else {
            $voucher = $voucher;
        }

        // Potongan Istimewa
        $potisaida        = str_replace(".", "", $request->potisaida);
        $potisswan        = str_replace(".", "", $request->potisswan);
        $potisstick       = str_replace(".", "", $request->potisstick);
        if (empty($potisaida)) {
            $potisaida = 0;
        } else {
            $potisaida = $potisaida;
        }
        if (empty($potisswan)) {
            $potisswan = 0;
        } else {
            $potisswan = $potisswan;
        }
        if (empty($potisstick)) {
            $potisstick = 0;
        } else {
            $potisstick = $potisstick;
        }

        //Penyesuaian
        $penyaida        = str_replace(".", "", $request->penyaida);
        $penyswan        = str_replace(".", "", $request->penyswan);
        $penystick       = str_replace(".", "", $request->penystick);
        if (empty($penyaida)) {
            $penyaida = 0;
        } else {
            $penyaida = $penyaida;
        }
        if (empty($penyswan)) {
            $penyswan = 0;
        } else {
            $penyswan = $penyswan;
        }
        if (empty($penystick)) {
            $penystick = 0;
        } else {
            $penystick = $penystick;
        }

        $ppn = str_replace(".", "", $request->ppn);
        if (empty($ppn)) {
            $ppn = 0;
        } else {
            $ppn = $ppn;
        }

        $totalnonppn = str_replace(".", "", $request->totalnonppn);
        $potongan = $potaida + $potswan + $potstick + $potsp + $potsambal;
        $potistimewa = $potisaida + $potisswan + $potisstick;
        $penyesuaian = $penyaida + $penyswan + $penystick;
        $titipan = str_replace(".", "", $request->titipan);
        $kode_cabang = $request->kode_cabang;
        $tahunini  = date('y');


        $data = [
            'no_fak_penj' => $no_fak_penj,
            'kode_pelanggan' => $kode_pelanggan,
            'id_karyawan' => $id_karyawan,
            'tgltransaksi' => $tgltransaksi,
            'pelanggan' => $pelanggan,
            'salesman' => $salesman,
            'sisapiutang' => $sisapiutang,
            'potaida' => $potaida,
            'potswan' => $potswan,
            'potstick' => $potstick,
            'potsp' => $potsp,
            'potsb' => $potsambal,
            'potisaida' => $potisaida,
            'potisswan' => $potisswan,
            'potisstick' => $potisstick,
            'penyaida' => $penyaida,
            'penyswan' => $penyswan,
            'penystick' => $penystick,
            'jenistransaksi' => $jenistransaksi,
            'jenisbayartunai' => $jenisbayartunai,
            'jenisbayar' => $jenisbayar,
            'subtotal' => $subtotal,
            'voucher' => $voucher,
            'titipan' => $titipan,
            'totalpotongan' => $potaida + $potswan + $potstick + $potsp + $potsambal,
            'totalpotis' => $potisaida + $potisswan + $potisstick,
            'totalpeny' => $penyswan + $penystick + $penyaida,
            'jatuhtempo' => $jatuhtempo,
            'bruto' => $bruto,
            'ppn' => $ppn,
            'totalnonppn' => $totalnonppn,
            'kode_cabang' => $kode_cabang,
            'keterangan' => $keterangan
        ];

        $barang = DB::table('detailpenjualan_temp')
            ->select('detailpenjualan_temp.*', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs', 'barang.harga_dus as harga_dus_db', 'barang.harga_pack as harga_pack_db', 'barang.harga_pcs as harga_pcs_db')
            ->join('barang', 'detailpenjualan_temp.kode_barang', '=', 'barang.kode_barang')
            ->where('id_admin', $id_admin)
            ->get();

        $piutang = DB::table('penjualan')
            ->selectRaw('
                        penjualan.no_fak_penj,tgltransaksi, IFNULL(penjualan.total,0) - IFNULL(retur.total,0) - IFNULL(jmlbayar,0) as sisapiutang')
            ->leftJoin(
                DB::raw("(
                            SELECT historibayar.no_fak_penj,
                            IFNULL(SUM(bayar), 0) AS jmlbayar
                            FROM
                            historibayar
                            GROUP BY historibayar.no_fak_penj
                        ) hb"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'hb.no_fak_penj');
                }
            )
            ->leftJoin(
                DB::raw("(
                            SELECT
                            retur.no_fak_penj AS no_fak_penj,
                            SUM(total) AS total
                            FROM
                                retur
                            GROUP BY
                                retur.no_fak_penj
                        ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )
            ->where('penjualan.kode_pelanggan', $kode_pelanggan)
            ->where('penjualan.status_lunas', '!=', 1)
            ->orWhere('penjualan.kode_pelanggan', $kode_pelanggan)
            ->whereNull('penjualan.status_lunas')
            ->get();

        return view('penjualan.preview', compact('data', 'barang', 'piutang'));
    }

    public function effectivecall()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_effectivecall', compact('cabang'));
    }

    public function cetakeffectivecall(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $kode_cabang = $request->kode_cabang;
        $formatlaporan = $request->formatlaporan;
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Laporan Penjualan Periode $dari-$sampai.xls");
        }
        if ($formatlaporan == 1) {
            $query = Penjualan::query();
            $query->selectRaw("penjualan.id_karyawan,nama_karyawan, COUNT(no_fak_penj) as ec ");
            $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->whereBetween('tgltransaksi', [$dari, $sampai]);
            $query->where('kode_cabang', $kode_cabang);
            $query->groupBy('penjualan.id_karyawan', 'nama_karyawan');
            $ec = $query->get();
            return view('penjualan.laporan.cetak_effectivecall', compact('cabang', 'ec', 'dari', 'sampai'));
        } else {
            $query = Detailpenjualan::query();
            $query->selectRaw("penjualan.id_karyawan,nama_karyawan,
            SUM(IF(kode_produk='AB',1,0)) as ab,
            SUM(IF(kode_produk='AR',1,0)) as ar,
            SUM(IF(kode_produk='AS',1,0)) as `as`,
            SUM(IF(kode_produk='BB',1,0)) as bb,
            SUM(IF(kode_produk='DEP',1,0)) as dep,
            SUM(IF(kode_produk='SP',1,0)) as sp,
            SUM(IF(kode_produk='SC',1,0)) as sc,
            SUM(IF(kode_produk='SP8',1,0)) as sp8
            ");
            $query->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang');
            $query->join('penjualan', 'detailpenjualan.no_fak_penj', '=', 'penjualan.no_fak_penj');
            $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
            $query->whereBetween('tgltransaksi', [$dari, $sampai]);
            $query->where('karyawan.kode_cabang', $kode_cabang);
            $query->where('promo', '!=', '1');
            $query->orwhereBetween('tgltransaksi', [$dari, $sampai]);
            $query->where('karyawan.kode_cabang', $kode_cabang);
            $query->whereNull('promo');
            $query->groupBy('penjualan.id_karyawan', 'nama_karyawan');
            $ec = $query->get();
            return view('penjualan.laporan.cetak_effectivecall_produk', compact('cabang', 'ec', 'dari', 'sampai'));
        }
    }

    public function ceknofaktur(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $cek = Penjualan::where('no_fak_penj', $no_fak_penj)->count();
        echo $cek;
    }


    public function showbarangtempv2()
    {
        $id_admin = Auth::user()->id;
        $detailtemp = DB::table('detailpenjualan_temp')
            ->select('detailpenjualan_temp.*', 'nama_barang', 'isipcsdus', 'isipcs', 'isipack')
            ->join('barang', 'detailpenjualan_temp.kode_barang', '=', 'barang.kode_barang')
            ->where('id_admin', $id_admin)
            ->get();
        if (Auth::user()->level == "salesman") {
            return view('penjualan.showbarangtempv3', compact('detailtemp'));
        } else {
            return view('penjualan.showbarangtempv2', compact('detailtemp'));
        }
    }

    public function editbarangtemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $promo = $request->promo;
        $id_admin = Auth::user()->id;

        if (empty($promo)) {
            $barangtemp = DB::table('detailpenjualan_temp')
                ->select('detailpenjualan_temp.*', 'nama_barang', 'isipcsdus', 'isipcs', 'isipack', 'barang.harga_dus as harga_dus_old', 'barang.harga_pack as harga_pack_old', 'barang.harga_pcs as harga_pcs_old')
                ->join('barang', 'detailpenjualan_temp.kode_barang', '=', 'barang.kode_barang')
                ->where('detailpenjualan_temp.kode_barang', $kode_barang)
                ->whereNull('promo')->where('id_admin', $id_admin)->first();
        } else {
            $barangtemp = DB::table('detailpenjualan_temp')
                ->select('detailpenjualan_temp.*', 'nama_barang', 'isipcsdus', 'isipcs', 'isipack', 'barang.harga_dus as harga_dus_old', 'barang.harga_pack as harga_pack_old', 'barang.harga_pcs as harga_pcs_old')
                ->join('barang', 'detailpenjualan_temp.kode_barang', '=', 'barang.kode_barang')
                ->where('detailpenjualan_temp.kode_barang', $kode_barang)
                ->where('promo', 1)->where('id_admin', $id_admin)->first();
        }

        return view('penjualan.editbarangtemp', compact('barangtemp'));
    }


    public function updatebarangtemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $harga_dus = $request->hargadus;
        $harga_pack = $request->hargapack;
        $harga_pcs = $request->hargapcs;
        $jumlah = $request->jumlah;
        $subtotal = $request->subtotal;
        $id_admin = Auth::user()->id;
        $promo = !empty($request->promo) ? $request->promo : NULL;
        $data = [
            'jumlah' => $jumlah,
            'harga_dus' => $harga_dus,
            'harga_pack' => $harga_pack,
            'harga_pcs' => $harga_pcs,
            'subtotal' => $subtotal,
            'id_admin' => $id_admin,
            'promo' => $promo
        ];



        try {
            DB::table('detailpenjualan_temp')
                ->where('kode_barang', $kode_barang)
                ->where('id_admin', $id_admin)
                ->update($data);
            echo 0;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function updatebarang(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $kode_barang = $request->kode_barang;
        $harga_dus = $request->hargadus;
        $harga_pack = $request->hargapack;
        $harga_pcs = $request->hargapcs;
        $jumlah = $request->jumlah;
        $subtotal = $request->subtotal;
        $id_admin = Auth::user()->id;
        $promo = !empty($request->promo) ? $request->promo : NULL;
        $data = [
            'jumlah' => $jumlah,
            'harga_dus' => $harga_dus,
            'harga_pack' => $harga_pack,
            'harga_pcs' => $harga_pcs,
            'subtotal' => $subtotal,
            'id_admin' => $id_admin,
            'promo' => $promo
        ];

        $update =  DB::table('detailpenjualan_edit')
            ->where('kode_barang', $kode_barang)
            ->where('no_fak_penj', $no_fak_penj)
            ->update($data);

        if ($update) {
            echo 0;
        }
    }

    public function editbarang(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $promo = $request->promo;
        $no_fak_penj = $request->no_fak_penj;

        if (empty($promo)) {
            $barang = DB::table('detailpenjualan_edit')
                ->select('detailpenjualan_edit.*', 'nama_barang', 'isipcsdus', 'isipcs', 'isipack', 'barang.harga_dus as harga_dus_old', 'barang.harga_pack as harga_pack_old', 'barang.harga_pcs as harga_pcs_old')
                ->join('barang', 'detailpenjualan_edit.kode_barang', '=', 'barang.kode_barang')
                ->where('detailpenjualan_edit.kode_barang', $kode_barang)
                ->whereNull('promo')->where('no_fak_penj', $no_fak_penj)->first();
        } else {
            $barang = DB::table('detailpenjualan_edit')
                ->select('detailpenjualan_edit.*', 'nama_barang', 'isipcsdus', 'isipcs', 'isipack', 'barang.harga_dus as harga_dus_old', 'barang.harga_pack as harga_pack_old', 'barang.harga_pcs as harga_pcs_old')
                ->join('barang', 'detailpenjualan_edit.kode_barang', '=', 'barang.kode_barang')
                ->where('detailpenjualan_edit.kode_barang', $kode_barang)
                ->where('promo', 1)->where('no_fak_penj', $no_fak_penj)->first();
        }



        return view('penjualan.editbarang', compact('barang'));
    }

    public function analisatransaksi()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('penjualan.laporan.frm.lap_analisatransaksi', compact('cabang', 'bulan'));
    }



    public function tunaitransfer()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('penjualan.laporan.frm.lap_tunaitransfer', compact('cabang', 'bulan'));
    }


    public function cetakanalisatransaksi(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));

        $query = Pelanggan::query();
        $query->selectRaw('pelanggan.kode_pelanggan,nama_pelanggan,
        tunai_1,
        tunai_2,
        tunai_3,
        tunai_4,
        kredit_1,
        kredit_2,
        kredit_3,
        kredit_4,
        cash_1,
        cash_2,
        cash_3,
        cash_4,
        transfer_1,
        transfer_2,
        transfer_3,
        transfer_4,
        giro_1,
        giro_2,
        giro_3,
        giro_4,
        total,
        totalbayar,
        qty');
        $query->leftJoin(
            DB::raw("(
                SELECT penjualan.kode_pelanggan,
                SUM(IF(DAY(tgltransaksi) BETWEEN 1 AND 7 AND jenistransaksi = 'tunai',total,0)) as tunai_1,
                SUM(IF(DAY(tgltransaksi) BETWEEN 8 AND 14 AND jenistransaksi = 'tunai',total,0)) as tunai_2,
                SUM(IF(DAY(tgltransaksi) BETWEEN 15 AND 21 AND jenistransaksi = 'tunai',total,0)) as tunai_3,
                SUM(IF(DAY(tgltransaksi) BETWEEN 22 AND 31 AND jenistransaksi = 'tunai',total,0)) as tunai_4,
                SUM(IF(DAY(tgltransaksi) BETWEEN 1 AND 7 AND jenistransaksi = 'kredit',total,0)) as kredit_1,
                SUM(IF(DAY(tgltransaksi) BETWEEN 8 AND 14 AND jenistransaksi = 'kredit',total,0)) as kredit_2,
                SUM(IF(DAY(tgltransaksi) BETWEEN 15 AND 21 AND jenistransaksi = 'kredit',total,0)) as kredit_3,
                SUM(IF(DAY(tgltransaksi) BETWEEN 22 AND 31 AND jenistransaksi = 'kredit',total,0)) as kredit_4,
                SUM(total) as total
                FROM penjualan
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                AND penjualan.id_karyawan = '$id_karyawan'
                GROUP BY penjualan.kode_pelanggan
            ) penjualan"),
            function ($join) {
                $join->on('penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT penjualan.kode_pelanggan,
                SUM(IF(DAY(tglbayar) BETWEEN 1 AND 7 AND historibayar.jenisbayar = 'tunai' OR DAY(tglbayar) BETWEEN 1 AND 7 AND historibayar.jenisbayar = 'titipan',bayar,0)) as cash_1,
                SUM(IF(DAY(tglbayar) BETWEEN 8 AND 14 AND historibayar.jenisbayar = 'tunai' OR DAY(tglbayar) BETWEEN 8 AND 14 AND historibayar.jenisbayar = 'titipan',bayar,0)) as cash_2,
                SUM(IF(DAY(tglbayar) BETWEEN 15 AND 21 AND historibayar.jenisbayar = 'tunai' OR DAY(tglbayar) BETWEEN 15 AND 21 AND historibayar.jenisbayar = 'titipan',bayar,0)) as cash_3,
                SUM(IF(DAY(tglbayar) BETWEEN 22 AND 31 AND historibayar.jenisbayar = 'tunai' OR DAY(tglbayar) BETWEEN 22 AND 31 AND historibayar.jenisbayar = 'titipan',bayar,0)) as cash_4,
                SUM(IF(DAY(tglbayar) BETWEEN 1 AND 7 AND historibayar.jenisbayar = 'transfer',bayar,0)) as transfer_1,
                SUM(IF(DAY(tglbayar) BETWEEN 8 AND 14 AND historibayar.jenisbayar = 'transfer',bayar,0)) as transfer_2,
                SUM(IF(DAY(tglbayar) BETWEEN 15 AND 21 AND historibayar.jenisbayar = 'transfer',bayar,0)) as transfer_3,
                SUM(IF(DAY(tglbayar) BETWEEN 22 AND 31 AND historibayar.jenisbayar = 'transfer',bayar,0)) as transfer_4,
                SUM(IF(DAY(tglbayar) BETWEEN 1 AND 7 AND historibayar.jenisbayar = 'giro',bayar,0)) as giro_1,
                SUM(IF(DAY(tglbayar) BETWEEN 8 AND 14 AND historibayar.jenisbayar = 'giro',bayar,0)) as giro_2,
                SUM(IF(DAY(tglbayar) BETWEEN 15 AND 21 AND historibayar.jenisbayar = 'giro',bayar,0)) as giro_3,
                SUM(IF(DAY(tglbayar) BETWEEN 22 AND 31 AND historibayar.jenisbayar = 'giro',bayar,0)) as giro_4,
                SUM(bayar) as totalbayar
                FROM historibayar
                INNER JOIN penjualan ON historibayar.no_fak_penj = penjualan.no_fak_penj
                WHERE tglbayar BETWEEN '$dari' AND '$sampai'
                AND historibayar.id_karyawan = '$id_karyawan'
                GROUP BY penjualan.kode_pelanggan
            ) pembayaran"),
            function ($join) {
                $join->on('pembayaran.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT penjualan.kode_pelanggan,SUM(ROUND(jumlah/isipcsdus)) as qty
                FROM detailpenjualan
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                AND penjualan.id_karyawan = '$id_karyawan'
                GROUP BY penjualan.kode_pelanggan
            ) dp"),
            function ($join) {
                $join->on('dp.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
            }
        );

        $query->whereNotNull('total');
        $query->where('nama_pelanggan', '!=', 'BATAL');
        $query->orwhereNotNull('totalbayar');
        $query->where('nama_pelanggan', '!=', 'BATAL');
        $query->orderBy('nama_pelanggan');
        $analisatransaksi = $query->get();

        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        $salesman = Salesman::where('id_karyawan', $id_karyawan)->first();
        return view('penjualan.laporan.cetak_analisatransaksi', compact('analisatransaksi', 'cabang', 'dari', 'sampai', 'salesman'));
    }


    public function cetaktunaitransfer(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $query = Penjualan::query();
        $query->selectRaw('penjualan.no_fak_penj,tgltransaksi,penjualan.kode_pelanggan,nama_pelanggan,nama_karyawan,penjualan.total, totalretur,totalbayar');
        $query->leftJoin(
            DB::raw("(
            SELECT no_fak_penj, SUM(total) as totalretur
            FROM retur
            GROUP BY no_fak_penj
        ) retur"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
            }
        );
        $query->leftJoin(
            DB::raw("(
            SELECT no_fak_penj, SUM(bayar) as totalbayar
            FROM historibayar
            WHERE tglbayar BETWEEN '$dari' AND '$sampai'
            GROUP BY no_fak_penj
        ) hb"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hb.no_fak_penj');
            }
        );
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->whereBetween('tgltransaksi', [$dari, $sampai]);
        $query->where('jenistransaksi', 'tunai');
        $query->where('jenisbayar', 'transfer');
        if (!empty($kode_cabang)) {
            $query->where('karyawan.kode_cabang', $kode_cabang);
        }
        $tunaitransfer = $query->get();


        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        $salesman = Salesman::where('id_karyawan', $id_karyawan)->first();
        return view('penjualan.laporan.cetak_tunaitransfer', compact('tunaitransfer', 'cabang', 'dari', 'sampai', 'salesman'));
    }

    public function cetakstruk(Request $request)
    {

        // $kode_pelanggan = Crypt::encrypt('TSM-00700');
        // echo $kode_pelanggan;
        // die;
        //$no_fak_penj = "BDGPR01230003 ";
        $no_fak_penj = $request->no_fak_penj;
        $pelangganmp = [
            'TSM-00548',
            'TSM-00493',
            'TSM-02234',
            'TSM-01117',
            'TSM-00494',
            'TSM-00466',
            'PST00007',
            'PST00008',
            'PST00002'
        ];
        $faktur = DB::table('penjualan')
            ->select('penjualan.*', 'nama_pelanggan', 'nama_karyawan', 'alamat_pelanggan', 'jenistransaksi')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('no_fak_penj', $no_fak_penj)->first();

        $detail = DB::table('detailpenjualan')
            ->select('kode_produk', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs', 'jumlah', 'subtotal', 'detailpenjualan.harga_dus', 'detailpenjualan.harga_pack', 'detailpenjualan.harga_pcs')
            ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->get();

        $pembayaran = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj)->get();
        $retur = DB::table('retur')
            ->selectRaw('SUM(total) as totalretur')
            ->where('no_fak_penj', $no_fak_penj)->first();

        if (Auth::user()->id == 107) {
            return view('penjualan.cetakstruk2', compact('faktur', 'pelangganmp', 'detail', 'pembayaran', 'retur'));
        } else {

            return view('penjualan.cetakstruk', compact('faktur', 'pelangganmp', 'detail', 'pembayaran', 'retur'));
        }
    }


    //Salesman
    public function showforsales($no_fak_penj)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $data = DB::table('penjualan')
            ->select(
                'penjualan.*',
                'nama_pelanggan',
                'alamat_pelanggan',
                'alamat_toko',
                'pelanggan.no_hp',
                'latitude',
                'longitude',
                'limitpel',
                'foto',
                'nik',
                'no_kk',
                'tgl_lahir',
                'pasar',
                'hari',
                'cara_pembayaran',
                'status_outlet',
                'type_outlet',
                'lama_usaha',
                'jaminan',
                'lama_langganan',
                'omset_toko',
                'karyawan.nama_karyawan',
                'karyawan.kategori_salesman',
                'karyawan.kode_cabang',
                'nama_cabang',
                DB::raw('IFNULL(totalpf,0) - IFNULL(totalgb,0) as totalretur'),
                'jmlbayar'
            )
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin(
                DB::raw("(
                    SELECT
                    no_fak_penj,
                    SUM(subtotal_gb) AS totalgb,
                    SUM(subtotal_pf) AS totalpf
                FROM
                    retur
                WHERE no_fak_penj = '$no_fak_penj'
                GROUP BY
                    no_fak_penj
                ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT no_fak_penj, IFNULL(SUM(bayar),0) as jmlbayar
                FROM historibayar
                WHERE no_fak_penj = '$no_fak_penj'
                GROUP BY no_fak_penj
            ) historibayar"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'historibayar.no_fak_penj');
                }
            )
            ->where('penjualan.no_fak_penj', $no_fak_penj)
            ->first();

        $detailpenjualan = DB::table('detailpenjualan')
            ->select('detailpenjualan.*', 'nama_barang', 'isipcsdus', 'isipcs', 'isipack', 'kode_produk')
            ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->get();

        $historibayar = DB::table('historibayar')
            ->join('karyawan', 'historibayar.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('giro', 'historibayar.id_giro', '=', 'giro.id_giro')
            ->where('historibayar.no_fak_penj', $no_fak_penj)
            ->orderBy('tglbayar', 'asc')
            ->get();

        $retur = DB::table('detailretur')
            ->select('detailretur.*', 'tglretur', 'jenis_retur', 'kode_produk', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('retur', 'detailretur.no_retur_penj', '=', 'retur.no_retur_penj')
            ->join('barang', 'detailretur.kode_barang', '=', 'barang.kode_barang')
            ->where('retur.no_fak_penj', $no_fak_penj)
            ->orderBy('retur.no_retur_penj')
            ->get();
        $salesman = DB::table('karyawan')->where('kode_cabang', $data->kode_cabang)->where('status_aktif_sales', 1)->get();
        $girotolak = DB::table('giro')
            ->select('giro.id_giro', 'no_giro')
            ->leftJoin(
                DB::raw("(
                SELECT id_giro,girotocash FROM historibayar WHERE no_fak_penj ='$no_fak_penj'
            ) hb"),
                function ($join) {
                    $join->on('giro.id_giro', '=', 'hb.id_giro');
                }
            )
            ->where('giro.status', 2)
            ->where('giro.no_fak_penj', $no_fak_penj)
            ->get();

        $giro = DB::table('giro')
            ->select('giro.*', 'nama_karyawan', 'tglbayar')
            ->leftJoin('karyawan', 'giro.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin(
                DB::raw("(
                SELECT id_giro,tglbayar
                FROM historibayar
                WHERE no_fak_penj = '$no_fak_penj'
            ) historibayar"),
                function ($join) {
                    $join->on('giro.id_giro', '=', 'historibayar.id_giro');
                }
            )
            ->where('giro.no_fak_penj', $no_fak_penj)
            ->get();

        $transfer = DB::table('transfer')
            ->select('transfer.*', 'nama_karyawan', 'tglbayar')
            ->leftJoin('karyawan', 'transfer.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin(
                DB::raw("(
                SELECT id_transfer,tglbayar
                FROM historibayar
                WHERE no_fak_penj = '$no_fak_penj'
            ) historibayar"),
                function ($join) {
                    $join->on('transfer.id_transfer', '=', 'historibayar.id_transfer');
                }
            )
            ->where('transfer.no_fak_penj', $no_fak_penj)
            ->get();
        return view('penjualan.showforsales', compact('data', 'detailpenjualan', 'retur', 'historibayar', 'salesman', 'girotolak', 'giro', 'transfer'));
    }

    public function uploadsignature(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $format = $no_fak_penj;
        $folderPath = "public/signature/";
        $image_parts = explode(";base64,", $request->signed);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $fileName =  $format . '.png';
        $file = $folderPath . $fileName;
        $data = [
            'signature' => $fileName
        ];
        $update = DB::table('penjualan')->where('no_fak_penj', $no_fak_penj)->update($data);
        if ($update) {
            if (Storage::exists($file)) {
                Storage::delete($file);
            }
            Storage::put($file, $image_base64);
            return Redirect::back()->with(['success' => 'Tanda Tanggan Berhasil Disimpan']);
        }
    }

    public function deletesignature($no_fak_penj)
    {
        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $data = [
            'signature' => NULL
        ];
        $folderPath = "public/signature/";
        $faktur = DB::table('penjualan')->where('no_fak_penj', $no_fak_penj)->first();
        $file = $folderPath . $faktur->signature;
        $update = DB::table('penjualan')->where('no_fak_penj', $no_fak_penj)->update($data);
        if ($update) {
            Storage::delete($file);
            return Redirect::back()->with(['success' => 'Tanda Tanggan Berhasil Dihapus']);
        }
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
        return view('penjualan.inputbarangtemp', compact('barang'));
    }


    public function inputbarang(Request $request)
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
        return view('penjualan.inputbarang', compact('barang'));
    }


    public function resetpenjualantemp(Request $request)
    {
        $resettemp = DB::table('detailpenjualan_temp')->where('id_admin', Auth::user()->id)->delete();
        if ($resettemp) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function setfakturbatal(Request $request)
    {
        $no_fak_penj = $request->no_fak_batal;
        $keterangan = $request->keterangan;
        $cekpenjualan = DB::table('penjualan')->where('no_fak_penj', $no_fak_penj)
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->first();
        $kode_cabang = $cekpenjualan->kode_cabang;
        $pelangganbatal = DB::table('pelanggan')->where('nama_pelanggan', 'BATAL')->where('kode_cabang', $kode_cabang)->first();
        $kode_pelanggan = $pelangganbatal->kode_pelanggan;
        $id_karyawan = $pelangganbatal->id_sales;
        $tgltransaksi = $cekpenjualan->tgltransaksi;
        $data = [
            'no_fak_penj' => $no_fak_penj,
            'tgltransaksi' => $tgltransaksi,
            'kode_pelanggan' => $kode_pelanggan,
            'id_karyawan' => $id_karyawan,
            'subtotal' => 0,
            'potaida' => 0,
            'potswan' => 0,
            'potstick' => 0,
            'potsp' => 0,
            'potongan' => 0,
            'potisaida' => 0,
            'potisswan' => 0,
            'potisstick' => 0,
            'potsambal' => 0,
            'potistimewa' => 0,
            'penyaida' => 0,
            'penyswan' => 0,
            'penystick' => 0,
            'penyharga' => 0,
            'ppn' => 0,
            'total' => 0,
            'jenistransaksi' => 'tunai',
            'jenisbayar' => 'tunai',
            'jatuhtempo' => $tgltransaksi,
            'id_admin' => Auth::user()->id,
            'status' => 2,
            'status_lunas' => 1,
            'keterangan' => $keterangan
        ];
        DB::beginTransaction();
        try {
            DB::table('penjualan')->where('no_fak_penj', $no_fak_penj)->delete();
            DB::table('penjualan')->insert($data);
            DB::commit();
            return Redirect::back()->with(['success' => 'Faktur Berhasil di Ubah ke Faktur Batal']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => $e]);
        }
        //UpdateDataPenjualan


    }


    public function lhp()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('penjualan.laporan.frm.lap_lhp', compact('cabang'));
    }

    public function cetaklhp(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $tanggal = $request->tanggal;
        $query = Penjualan::query();
        $query->selectRaw("penjualan.no_fak_penj,nama_pelanggan,
        AB,AR,ASE,BB,DEP,SC,SP8P,SP8,SP,SP500,
        SUM(IF(penjualan.jenistransaksi='tunai',total,0)) as totaltunai,
        SUM(IF(penjualan.jenistransaksi='kredit',total,0)) as totalkredit,
        totalbayar,totalgiro,totaltransfer");
        $query->leftJoin(
            DB::raw("(
            SELECT
                detailpenjualan.no_fak_penj,
                SUM( IF ( kode_produk = 'AB', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS AB,
                SUM( IF ( kode_produk = 'AR', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS AR,
                SUM( IF ( kode_produk = 'AS', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS ASE,
                SUM( IF ( kode_produk = 'BB', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS BB,
                SUM( IF ( kode_produk = 'CG', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS CG,
                SUM( IF ( kode_produk = 'CGG', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS CGG,
                SUM( IF ( kode_produk = 'DB', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS DB,
                SUM( IF ( kode_produk = 'DEP', detailpenjualan.jumlah/isipcsdus,NULL ) ) AS DEP,
                SUM( IF ( kode_produk = 'DK', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS DK,
                SUM( IF ( kode_produk = 'DS', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS DS,
                SUM( IF ( kode_produk = 'BBP', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS BBP,
                SUM( IF ( kode_produk = 'SPP', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SPP,
                SUM( IF ( kode_produk = 'CG5', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS CG5,
                SUM( IF ( kode_produk = 'SC', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SC,
                SUM( IF ( kode_produk = 'SP8', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SP8,
                SUM( IF ( kode_produk = 'SP8-P', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SP8P,
                SUM( IF ( kode_produk = 'SP', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SP,
                SUM( IF ( kode_produk = 'SP500', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SP500
            FROM
                detailpenjualan
            INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
            INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
            WHERE tgltransaksi = '$tanggal'
            GROUP BY
                detailpenjualan.no_fak_penj
            ) dp"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'dp.no_fak_penj');
            }
        );
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->leftJoin(
            DB::raw("(
            SELECT
                no_fak_penj,
                SUM(bayar) AS totalbayar
            FROM
                historibayar
            WHERE tglbayar = '$tanggal'
            GROUP BY
                no_fak_penj
            ) hb"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'hb.no_fak_penj');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT
                no_fak_penj,
                SUM(jumlah) AS totalgiro
            FROM
                giro
            WHERE tgl_giro = '$tanggal'
            GROUP BY
                no_fak_penj
            ) giro"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'giro.no_fak_penj');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT
                no_fak_penj,
                SUM(jumlah) AS totaltransfer
            FROM
                transfer
            WHERE tgl_transfer = '$tanggal'
            GROUP BY
                no_fak_penj
            ) transfer"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'transfer.no_fak_penj');
            }
        );
        $query->where('tgltransaksi', $tanggal);
        $query->where('karyawan.kode_cabang', $request->kode_cabang);
        $query->where('penjualan.id_karyawan', $request->id_karyawan);
        $query->orderBy('penjualan.no_fak_penj');
        $query->groupByRaw('penjualan.no_fak_penj,nama_pelanggan,AB,AR,ASE,BB,DEP,SC,SP8P,SP8,SP,SP500,totalbayar,totalgiro,totaltransfer');
        $penjualan = $query->get();

        $no_fak_penj = [];
        foreach ($penjualan as $d) {
            $no_fak_penj[] = $d->no_fak_penj;
        }



        $historibayar = DB::table('historibayar')
            ->selectRaw('historibayar.no_fak_penj,nama_pelanggan,SUM(bayar) as totalbayar,totalgiro,totaltransfer')
            ->join('penjualan', 'historibayar.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->leftJoin(
                DB::raw("(
                SELECT
                    no_fak_penj,
                    SUM(jumlah) AS totalgiro
                FROM
                    giro
                WHERE tgl_giro = '$tanggal'
                GROUP BY
                    no_fak_penj
                ) giro"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'giro.no_fak_penj');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT
                    no_fak_penj,
                    SUM(jumlah) AS totaltransfer
                FROM
                    transfer
                WHERE tgl_transfer = '$tanggal'
                GROUP BY
                    no_fak_penj
                ) transfer"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'transfer.no_fak_penj');
                }
            )
            ->where('tglbayar', $tanggal)
            ->where('historibayar.id_karyawan', $id_karyawan)
            ->whereNotIn('historibayar.no_fak_penj', $no_fak_penj)
            ->orderBy('historibayar.no_fak_penj')
            ->groupByRaw('historibayar.no_fak_penj,nama_pelanggan,totalgiro,totaltransfer')
            ->get();

        $no_fak_penj_hb = [];
        foreach ($historibayar as $d) {
            $no_fak_penj_hb[] = $d->no_fak_penj;
        }

        $giro = DB::table('giro')
            ->selectRaw('giro.no_fak_penj,nama_pelanggan,SUM(jumlah) as totalgiro')
            ->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->where('tgl_giro', $tanggal)
            ->where('giro.id_karyawan', $id_karyawan)
            ->whereNotIn('giro.no_fak_penj', $no_fak_penj)
            ->whereNotIn('giro.no_fak_penj', $no_fak_penj_hb)
            ->orderBy('giro.no_fak_penj')
            ->groupByRaw('giro.no_fak_penj,nama_pelanggan')
            ->get();

        $transfer = DB::table('transfer')
            ->selectRaw('transfer.no_fak_penj,nama_pelanggan,SUM(jumlah) as totaltransfer')
            ->join('penjualan', 'transfer.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->where('tgl_transfer', $tanggal)
            ->where('transfer.id_karyawan', $id_karyawan)
            ->whereNotIn('transfer.no_fak_penj', $no_fak_penj)
            ->whereNotIn('transfer.no_fak_penj', $no_fak_penj_hb)
            ->orderBy('transfer.no_fak_penj')
            ->groupByRaw('transfer.no_fak_penj,nama_pelanggan')
            ->get();


        $allgiro = DB::table('giro')
            ->selectRaw('SUM(jumlah) as totalgiro')
            ->where('tgl_giro', $tanggal)
            ->where('giro.id_karyawan', $id_karyawan)
            ->first();


        $alltransfer = DB::table('transfer')
            ->selectRaw('SUM(jumlah) as totaltransfer')
            ->where('tgl_transfer', $tanggal)
            ->where('transfer.id_karyawan', $id_karyawan)
            ->first();
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        return view('penjualan.laporan.cetak_lhp', compact('tanggal', 'penjualan', 'historibayar', 'giro', 'transfer', 'karyawan', 'allgiro', 'alltransfer'));
    }
}
