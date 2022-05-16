<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Mutasigudangcabang;
use App\Models\Mutasigudangjadi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Whoops\Run;

class SuratjalanController extends Controller
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
        $query = Mutasigudangjadi::query();
        $query->select('mutasi_gudang_jadi.*', 'permintaan_pengiriman.kode_cabang', 'tgl_mutasi_gudang_cabang', 'tgl_transitin');
        $query->join('permintaan_pengiriman', 'mutasi_gudang_jadi.no_permintaan_pengiriman', '=', 'permintaan_pengiriman.no_permintaan_pengiriman');
        $query->leftJoin('mutasi_gudang_cabang', 'mutasi_gudang_jadi.no_mutasi_gudang', '=', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang');
        $query->leftJoin(
            DB::raw("(
                SELECT no_suratjalan,tgl_mutasi_gudang_cabang as tgl_transitin
                FROM mutasi_gudang_cabang
                WHERE jenis_mutasi ='TRANSIT IN'
            ) transitin"),
            function ($join) {
                $join->on('mutasi_gudang_jadi.no_mutasi_gudang', '=', 'transitin.no_suratjalan');
            }
        );
        $query->where('mutasi_gudang_jadi.jenis_mutasi', 'SURAT JALAN');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_mutasi_gudang', [$request->dari, $request->sampai]);
        }

        if (!empty($request->no_dok)) {
            $query->where('no_dok', $request->no_dok);
        }



        if (!empty($request->status_sj)) {
            if ($request->status_sj == "BTC") {
                $query->where('status_sj', 0);
            } else if ($request->status_sj == "STC") {
                $query->where('status_sj', 1);
            } else if ($request->status_sj == "TO") {
                $query->where('status_sj', 2);
            }
        }
        if ($this->cabang == "PCF") {
            if (!empty($request->kode_cabang)) {
                $query->where('permintaan_pengiriman.kode_cabang', $request->kode_cabang);
            }
        } else {
            $query->where('permintaan_pengiriman.kode_cabang', $request->kode_cabang);
        }
        $query->orderBy('tgl_mutasi_gudang', 'desc');
        $query->orderBy('mutasi_gudang_jadi.time_stamp', 'desc');
        $mutasi = $query->paginate(15);
        $mutasi->appends($request->all());

        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        return view('suratjalan.index', compact('mutasi', 'cabang'));
    }
    public function create($no_permintaan_pengiriman)
    {
        $no_permintaan_pengiriman = Crypt::decrypt($no_permintaan_pengiriman);
        $permintaanpengiriman = DB::table('permintaan_pengiriman')
            ->join('cabang', 'permintaan_pengiriman.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->first();
        $detailpermintaan = DB::table('detail_permintaan_pengiriman')
            ->select('detail_permintaan_pengiriman.*', 'nama_barang',)
            ->join('master_barang', 'detail_permintaan_pengiriman.kode_produk', '=', 'master_barang.kode_produk')
            ->where('detail_permintaan_pengiriman.no_permintaan_pengiriman', $no_permintaan_pengiriman)
            ->orderBy('detail_permintaan_pengiriman.kode_produk')
            ->get();
        $produk = Barang::orderBy('kode_produk')->get();
        return view('suratjalan.create', compact('permintaanpengiriman', 'detailpermintaan', 'produk'));
    }

    public function cektemp(Request $request)
    {
        $no_permintaan_pengiriman = $request->no_permintaan_pengiriman;
        $cek = DB::table('detail_mutasi_gudang_temp')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->count();
        echo $cek;
    }

    public function storetemp(Request $request)
    {
        $no_permintaan_pengiriman = $request->no_permintaan_pengiriman;
        $kode_produk = $request->kode_produk;
        $jumlah = $request->jumlah;
        $id_admin = Auth::user()->id;

        $data = [
            'no_permintaan_pengiriman' => $no_permintaan_pengiriman,
            'kode_produk' => $kode_produk,
            'jumlah' => $jumlah,
            'id_admin' => $id_admin
        ];

        $cek = DB::table('detail_mutasi_gudang_temp')->where('kode_produk', $kode_produk)->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->count();
        if ($cek > 0) {
            echo 1;
        } else {
            $simpan = DB::table('detail_mutasi_gudang_temp')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function showtemp($no_permintaan_pengiriman)
    {
        $no_permintaan_pengiriman = Crypt::decrypt($no_permintaan_pengiriman);
        $detail = DB::table('detail_mutasi_gudang_temp')
            ->select('detail_mutasi_gudang_temp.*', 'nama_barang')
            ->join('master_barang', 'detail_mutasi_gudang_temp.kode_produk', '=', 'master_barang.kode_produk')
            ->orderBy('detail_mutasi_gudang_temp.kode_produk')
            ->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)
            ->get();
        return view('suratjalan.showtemp', compact('detail'));
    }

    public function deletetemp(Request $request)
    {
        $no_permintaan_pengiriman = $request->no_permintaan_pengiriman;
        $kode_produk = $request->kode_produk;
        $hapus = DB::table('detail_mutasi_gudang_temp')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->where('kode_produk', $kode_produk)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function masukankerealisasi(Request $request)
    {
        $no_permintaan_pengiriman = $request->no_permintaan_pengiriman;
        $id_admin = Auth::user()->id;
        $detailpp = DB::table('detail_permintaan_pengiriman')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->get();
        DB::beginTransaction();
        try {
            foreach ($detailpp as $d) {
                $data = [
                    'kode_produk' => $d->kode_produk,
                    'jumlah' => $d->jumlah,
                    'no_permintaan_pengiriman' => $no_permintaan_pengiriman,
                    'id_admin' => $id_admin
                ];

                $cek = DB::table('detail_mutasi_gudang_temp')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->where('kode_produk', $d->kode_produk)->count();
                if (empty($cek)) {
                    DB::table('detail_mutasi_gudang_temp')->insert($data);
                }
            }
            DB::commit();
            echo 0;
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            echo 2;
        }
    }

    public function buatnomorsj(Request $request)
    {
        $tgl_mutasi_gudang = $request->tgl_mutasi_gudang;
        $kode_cabang = $request->kode_cabang;
        $kode = strlen($kode_cabang);
        $no_sj = $kode + 4;

        $sj = DB::table('mutasi_gudang_jadi')
            ->select(DB::raw('LEFT(no_mutasi_gudang,' . $no_sj . ') as no_suratjalan'))
            ->whereRaw('MID(no_mutasi_gudang,3,' . $kode . ')="' . $kode_cabang . '"')
            ->where('tgl_mutasi_gudang', $tgl_mutasi_gudang)
            ->where('jenis_mutasi', 'SURAT JALAN')
            ->orderByRaw('LEFT(no_mutasi_gudang,' . $no_sj . ') desc')
            ->first();


        $tanggal = explode("-", $tgl_mutasi_gudang);
        $hari = $tanggal[2];
        $bulan = $tanggal[1];
        if ($bulan > 9) {
            $bl = $bulan;
        } else {
            $bl = substr($bulan, 1, 1);
        }
        $tahun = $tanggal[0];
        $tgl = "." . $hari . "." . $bl . "." . $tahun;
        $nomor_terakhir  = $sj != null ? $sj->no_suratjalan : '';
        $no_sj = buatkode($nomor_terakhir, "SJ" . $kode_cabang, 2) . $tgl;
        echo $no_sj;
    }

    public function store(Request $request)
    {
        $no_mutasi_gudang = $request->no_mutasi_gudang;
        $tgl_mutasi_gudang = $request->tgl_mutasi_gudang;
        $no_permintaan_pengiriman = Crypt::decrypt($request->no_permintaan_pengiriman);
        $tujuan = $request->tujuan;
        $tarif = !empty($request->tarif) ? str_replace(".", "", $request->tarif) : 0;
        $tepung = !empty($request->tepung) ? str_replace(".", "", $request->tepung) : 0;
        $bs = !empty($request->bs) ? str_replace(".", "", $request->bs) : 0;
        $angkutan = $request->angkutan;
        $no_dok = $request->no_dok;
        $nopol = $request->nopol;
        $id_admin = Auth::user()->id;
        $detailtemp = DB::table('detail_mutasi_gudang_temp')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->get();
        DB::beginTransaction();
        try {
            $data = [
                'no_mutasi_gudang' => $no_mutasi_gudang,
                'no_dok' => $no_dok,
                'tgl_mutasi_gudang' => $tgl_mutasi_gudang,
                'no_permintaan_pengiriman' => $no_permintaan_pengiriman,
                'inout' => 'OUT',
                'jenis_mutasi' => 'SURAT JALAN',
                'status_sj' => '0',
                'id_admin' => $id_admin
            ];
            DB::table('mutasi_gudang_jadi')->insert($data);
            foreach ($detailtemp as $d) {
                $data_detail = array(
                    'no_mutasi_gudang' => $no_mutasi_gudang,
                    'kode_produk' => $d->kode_produk,
                    'jumlah' => $d->jumlah
                );

                DB::table('detail_mutasi_gudang')->insert($data_detail);
            }

            DB::table('detail_mutasi_gudang_temp')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->delete();
            DB::table('permintaan_pengiriman')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->update(['status' => 1]);
            $dataangkutan = array(
                'no_surat_jalan'  => $no_dok,
                'angkutan'        => $angkutan,
                'tujuan'          => $tujuan,
                'nopol'           => $nopol,
                'tarif'           => $tarif,
                'bs'              => $bs,
                'tepung'          => $tepung
            );

            DB::table('angkutan')->insert($dataangkutan);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }

    public function batalkansuratjalan($no_mutasi_gudang)
    {
        $no_mutasi_gudang = Crypt::decrypt($no_mutasi_gudang);
        $mutasi = DB::table('mutasi_gudang_jadi')->where('no_mutasi_gudang', $no_mutasi_gudang)->first();
        $no_permintaan_pengiriman = $mutasi->no_permintaan_pengiriman;
        $no_dok = $mutasi->no_dok;
        DB::beginTransaction();
        try {
            DB::table('mutasi_gudang_jadi')->where('no_mutasi_gudang', $no_mutasi_gudang)->delete();
            DB::table('angkutan')->where('no_surat_jalan', $no_dok)->delete();
            DB::table('permintaan_pengiriman')->where('no_permintaan_pengiriman', $no_permintaan_pengiriman)->update(['status' => 0]);

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dibatalkan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan, Hubungi Tim IT']);
        }
    }

    public function show($no_mutasi_gudang)
    {
        $no_mutasi_gudang = Crypt::decrypt($no_mutasi_gudang);
        $mutasi = DB::table('mutasi_gudang_jadi')
            ->select('mutasi_gudang_jadi.*', 'tgl_permintaan_pengiriman', 'nama_cabang', 'permintaan_pengiriman.keterangan')
            ->join('permintaan_pengiriman', 'mutasi_gudang_jadi.no_permintaan_pengiriman', '=', 'permintaan_pengiriman.no_permintaan_pengiriman')
            ->join('cabang', 'permintaan_pengiriman.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('mutasi_gudang_jadi.no_mutasi_gudang', $no_mutasi_gudang)->first();
        $detail = DB::table('detail_mutasi_gudang')
            ->select('detail_mutasi_gudang.*', 'nama_barang')
            ->join('master_barang', 'detail_mutasi_gudang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_mutasi_gudang', $no_mutasi_gudang)
            ->get();
        return view('suratjalan.show', compact('mutasi', 'detail'));
    }

    public function cetak($no_mutasi_gudang)
    {
        $no_mutasi_gudang = Crypt::decrypt($no_mutasi_gudang);
        $mutasi = DB::table('mutasi_gudang_jadi')
            ->select('mutasi_gudang_jadi.*', 'tgl_permintaan_pengiriman', 'nama_cabang', 'alamat_cabang', 'permintaan_pengiriman.keterangan')
            ->join('permintaan_pengiriman', 'mutasi_gudang_jadi.no_permintaan_pengiriman', '=', 'permintaan_pengiriman.no_permintaan_pengiriman')
            ->join('cabang', 'permintaan_pengiriman.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('mutasi_gudang_jadi.no_mutasi_gudang', $no_mutasi_gudang)->first();
        $detail = DB::table('detail_mutasi_gudang')
            ->select('detail_mutasi_gudang.*', 'nama_barang', 'satuan')
            ->join('master_barang', 'detail_mutasi_gudang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_mutasi_gudang', $no_mutasi_gudang)
            ->get();
        return view('suratjalan.cetak', compact('mutasi', 'detail'));
    }

    public function prosescabang($no_mutasi_gudang)
    {
        $no_mutasi_gudang = Crypt::decrypt($no_mutasi_gudang);
        $mutasi = DB::table('mutasi_gudang_jadi')
            ->select('mutasi_gudang_jadi.*', 'tgl_permintaan_pengiriman', 'permintaan_pengiriman.kode_cabang', 'nama_cabang', 'permintaan_pengiriman.keterangan')
            ->join('permintaan_pengiriman', 'mutasi_gudang_jadi.no_permintaan_pengiriman', '=', 'permintaan_pengiriman.no_permintaan_pengiriman')
            ->join('cabang', 'permintaan_pengiriman.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('mutasi_gudang_jadi.no_mutasi_gudang', $no_mutasi_gudang)->first();
        $detail = DB::table('detail_mutasi_gudang')
            ->select('detail_mutasi_gudang.*', 'nama_barang')
            ->join('master_barang', 'detail_mutasi_gudang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_mutasi_gudang', $no_mutasi_gudang)
            ->get();
        return view('suratjalan.prosescabang', compact('mutasi', 'detail'));
    }

    public function storeprosescabang($no_mutasi_gudang, Request $request)
    {
        $no_mutasi_gudang = Crypt::decrypt($no_mutasi_gudang);
        $sj = DB::table('mutasi_gudang_jadi')
            ->select('mutasi_gudang_jadi.*', 'tgl_permintaan_pengiriman', 'permintaan_pengiriman.kode_cabang', 'nama_cabang', 'permintaan_pengiriman.keterangan')
            ->join('permintaan_pengiriman', 'mutasi_gudang_jadi.no_permintaan_pengiriman', '=', 'permintaan_pengiriman.no_permintaan_pengiriman')
            ->join('cabang', 'permintaan_pengiriman.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('mutasi_gudang_jadi.no_mutasi_gudang', $no_mutasi_gudang)->first();
        $tgl_mutasi_gudang = $sj->tgl_mutasi_gudang;
        $tgl_mutasi_gudang_cabang = $request->tgl_mutasi_gudang_cabang;
        $kode_cabang = $sj->kode_cabang;
        $status = $request->status;
        $id_admin = Auth::user()->id;

        $tahun = date('Y');
        $transitout = DB::table('mutasi_gudang_cabang')
            ->selectRaw("no_mutasi_gudang_cabang as no_transitout")
            ->where('kode_cabang', $kode_cabang)
            ->where('jenis_mutasi', 'TRANSIT OUT')
            ->whereRaw('MID(no_mutasi_gudang_cabang,6,2) =' . $tahun)
            ->orderBy('no_mutasi_gudang_cabang', 'desc')
            ->first();
        $lastransitout = $transitout != null ? $transitout->no_transitout : '';
        $no_transitout = buatkode($lastransitout, 'TO' . $kode_cabang . $tahun, 2);
        $detail = DB::table('detail_mutasi_gudang')
            ->select('detail_mutasi_gudang.*', 'nama_barang', 'isipcsdus')
            ->join('master_barang', 'detail_mutasi_gudang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_mutasi_gudang', $no_mutasi_gudang)
            ->get();
        $data_sj = [
            'no_mutasi_gudang_cabang' => $no_mutasi_gudang,
            'tgl_mutasi_gudang_cabang' => $tgl_mutasi_gudang_cabang,
            'tgl_kirim' => $tgl_mutasi_gudang,
            'kode_cabang' => $kode_cabang,
            'kondisi' => 'GOOD',
            'inout_good' => 'IN',
            'jenis_mutasi' => 'SURAT JALAN',
            'order' => '0',
            'id_admin' => $id_admin
        ];

        $data_transitout = [
            'no_mutasi_gudang_cabang' => $no_transitout,
            'tgl_mutasi_gudang_cabang' => $tgl_mutasi_gudang_cabang,
            'tgl_kirim' => $tgl_mutasi_gudang,
            'no_suratjalan' => $no_mutasi_gudang,
            'kode_cabang' => $kode_cabang,
            'kondisi'  => 'GOOD',
            'inout_good'  => 'OUT',
            'jenis_mutasi' => 'TRANSIT OUT',
            'order' => '1',
            'id_admin' => $id_admin
        ];

        DB::beginTransaction();
        try {
            DB::table('mutasi_gudang_cabang')->insert($data_sj);
            foreach ($detail as $d) {
                $jumlah = $d->jumlah * $d->isipcsdus;
                $data_detail = array(
                    'no_mutasi_gudang_cabang' => $no_mutasi_gudang,
                    'kode_produk' => $d->kode_produk,
                    'jumlah' => $jumlah
                );
                DB::table('detail_mutasi_gudang_cabang')->insert($data_detail);
            }
            if ($status == "SURAT JALAN") {
                $status = 1;
            } else if ($status == "TRANSIT OUT") {
                DB::table('mutasi_gudang_cabang')->insert($data_transitout);
                foreach ($detail as $d) {
                    $jumlah = $d->jumlah * $d->isipcsdus;
                    $data_to = array(
                        'no_mutasi_gudang_cabang' => $no_transitout,
                        'kode_produk' => $d->kode_produk,
                        'jumlah' => $jumlah
                    );
                    DB::table('detail_mutasi_gudang_cabang')->insert($data_to);
                }

                $status = 2;
            }


            DB::table('mutasi_gudang_jadi')->where('no_mutasi_gudang', $no_mutasi_gudang)->update(['status_sj' => $status]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT!']);
        }
    }

    public function batalkansjcabang($no_mutasi_gudang)
    {
        $no_mutasi_gudang = Crypt::decrypt($no_mutasi_gudang);
        DB::beginTransaction();
        try {
            DB::table('mutasi_gudang_cabang')->where('no_mutasi_gudang_cabang', $no_mutasi_gudang)
                ->orWhere('no_suratjalan', $no_mutasi_gudang)->delete();
            DB::table('mutasi_gudang_jadi')->where('no_mutasi_gudang', $no_mutasi_gudang)->update([
                'status_sj' => 0
            ]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT!']);
        }
    }

    public function getautocompletesj(Request $request)
    {
        $search = $request->search;
        if ($search == '') {
            $query = Mutasigudangcabang::query();
            $query->select('mutasi_gudang_cabang.*', 'no_dok');
            $query->leftjoin('mutasi_gudang_jadi', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang');
            $query->join('cabang', 'mutasi_gudang_cabang.kode_cabang', '=', 'cabang.kode_cabang');
            if ($this->cabang != "PCF") {
                $query->where('mutasi_gudang_cabang.kode_cabang', $this->cabang);
                $query->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN');
                $query->orWhere('cabang.sub_cabang', $this->cabang);
                $query->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN');
            }

            $query->orderBy('tgl_mutasi_gudang_cabang', 'desc');
            $query->orderby('no_mutasi_gudang_cabang', 'desc');
            $query->limit(10);
            $autocomplate = $query->get();
        } else {
            $query = Mutasigudangcabang::query();
            $query->select('mutasi_gudang_cabang.*', 'no_dok');
            $query->leftjoin('mutasi_gudang_jadi', 'mutasi_gudang_cabang.no_mutasi_gudang_cabang', '=', 'mutasi_gudang_jadi.no_mutasi_gudang');
            $query->join('cabang', 'mutasi_gudang_cabang.kode_cabang', '=', 'cabang.kode_cabang');
            if ($this->cabang != "PCF") {
                $query->where('no_mutasi_gudang_cabang', 'like', '%' . $search . '%');
                $query->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN');
                $query->where('mutasi_gudang_cabang.kode_cabang', $this->cabang);
                $query->orWhere('no_mutasi_gudang_cabang', 'like', '%' . $search . '%');
                $query->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN');
                $query->where('cabang.sub_cabang', $this->cabang);
                $query->orWhere('no_dok', 'like', '%' . $search . '%');
                $query->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN');
                $query->where('mutasi_gudang_cabang.kode_cabang', $this->cabang);
                $query->orWhere('no_dok', 'like', '%' . $search . '%');
                $query->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN');
                $query->where('cabang.sub_cabang', $this->cabang);
                $query->orWhere('tgl_mutasi_gudang_cabang', 'like', '%' . $search . '%');
                $query->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN');
                $query->where('mutasi_gudang_cabang.kode_cabang', $this->cabang);
                $query->orWhere('tgl_mutasi_gudang_cabang', 'like', '%' . $search . '%');
                $query->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN');
                $query->where('cabang.sub_cabang', $this->cabang);
            } else {
                $query->where('no_mutasi_gudang_cabang', 'like', '%' . $search . '%');
                $query->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN');
                $query->orWhere('no_dok', 'like', '%' . $search . '%');
                $query->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN');
                $query->orWhere('tgl_mutasi_gudang_cabang', 'like', '%' . $search . '%');
                $query->where('mutasi_gudang_cabang.jenis_mutasi', 'SURAT JALAN');
            }







            $query->orderBy('tgl_mutasi_gudang_cabang', 'desc');
            $query->orderby('no_mutasi_gudang_cabang', 'desc');
            $query->limit(10);
            $autocomplate = $query->get();
        }

        // dd($search);
        $response = array();
        foreach ($autocomplate as $autocomplate) {
            $label = $autocomplate->no_mutasi_gudang_cabang . " - " . $autocomplate->no_dok . " - " . $autocomplate->tgl_mutasi_gudang_cabang;
            $response[] = array("label" => $label, 'val' => $autocomplate->no_mutasi_gudang_cabang);
        }

        echo json_encode($response);
        exit;
    }

    public function showsuratjalanmutasi(Request $request)
    {
        $no_suratjalan = $request->no_suratjalan;
        $mutasi = DB::table('mutasi_gudang_cabang')->where('no_mutasi_gudang_cabang', $no_suratjalan)->first();
        return view('suratjalan.showsuratjalanmutasi', compact('mutasi'));
    }
}