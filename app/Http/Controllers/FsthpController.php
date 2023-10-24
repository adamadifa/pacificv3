<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Mutasiproduksi;
use COM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class FsthpController extends Controller
{

    public function index(Request $request)
    {
        $query = Mutasiproduksi::query();
        $query->where('jenis_mutasi', 'FSTHP');
        if (!empty($request->tanggal)) {
            $query->where('tgl_mutasi_produksi', $request->tanggal);
        } else {
            $query->where('tgl_mutasi_produksi', '>=', startreport());
        }
        $query->orderBy('tgl_mutasi_produksi', 'desc');
        $query->orderBy('time_stamp', 'desc');
        $fsthp = $query->paginate(15);
        $fsthp->appends($request->all());
        lockreport($request->tanggal);
        return view('fsthp.index', compact('fsthp'));
    }
    public function show(Request $request)
    {
        $no_mutasi_produksi = Crypt::decrypt($request->no_mutasi_produksi);
        $fsthp = DB::table('mutasi_produksi')->where('no_mutasi_produksi', $no_mutasi_produksi)->first();
        $detail = DB::table('detail_mutasi_produksi')
            ->select('detail_mutasi_produksi.*', 'nama_barang')
            ->join('master_barang', 'detail_mutasi_produksi.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_mutasi_produksi', $no_mutasi_produksi)
            ->orderBy('shift')
            ->get();
        return view('fsthp.show', compact('fsthp', 'detail'));
    }

    public function getbarang()
    {
        $barang = Barang::orderBy('kode_produk')->get();
        return view('fsthp.getbarang', compact('barang'));
    }

    public function delete($no_mutasi_produksi)
    {
        $no_mutasi_produksi = Crypt::decrypt($no_mutasi_produksi);
        $hapus = DB::table('mutasi_produksi')->where('no_mutasi_produksi', $no_mutasi_produksi)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function showtemp($kode_produk, $unit, $shift)
    {
        $id_admin = Auth::user()->id;
        $detail = DB::table('detail_mutasi_produksi_temp')
            ->select('detail_mutasi_produksi_temp.*', 'nama_barang')
            ->join('master_barang', 'detail_mutasi_produksi_temp.kode_produk', '=', 'master_barang.kode_produk')
            ->where('detail_mutasi_produksi_temp.kode_produk', $kode_produk)
            ->where('unit', $unit)
            ->where('shift', $shift)
            ->where('id_admin', $id_admin)
            ->where('inout', 'OUT')
            ->orderBy('shift')->get();
        return view('fsthp.showtemp', compact('detail'));
    }





    public function buat_nomor_fsthp(Request $request)
    {
        $tgl_mutasi_produksi = $request->tgl_mutasi_produksi;
        $kode_produk = $request->kode_produk;
        $shift = $request->shift;

        // $kode = strlen($kode_produk);
        // $no_fsthp = $kode + 3;

        // $bpbj = DB::table('mutasi_produksi')
        //     ->selectRaw("LEFT(no_mutasi_produksi,$no_fsthp) as no_fsthp")
        //     ->whereRaw("MID(no_mutasi_produksi,2," . $kode . ")='" . $kode_produk . "'")
        //     ->where('tgl_mutasi_produksi', $tgl_mutasi_produksi)
        //     ->where('jenis_mutasi', 'FSTHP')
        //     ->orderByRaw("LEFT(no_mutasi_produksi," . $no_fsthp . ") DESC")
        //     ->first();

        $namabulan = array("", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");
        $tanggal = explode("-", $tgl_mutasi_produksi);
        $hari = $tanggal[2];
        $bulan = $tanggal[1] + 0;
        //echo $bl;
        $tahun = $tanggal[0];
        $tgl = "/" . $hari . "/" . $namabulan[$bulan] . "/" . $tahun;
        // if ($bpbj != null) {
        //     $last_nobpbj = $bpbj->no_bpbj;
        // } else {
        //     $last_nobpbj = "";
        // }
        // $no_bpbj = buatkode($last_nobpbj, $kode_produk, 2) . $tgl;
        $no_fsthp = "F" . $kode_produk . "/0" . $shift . $tgl;
        echo $no_fsthp;
    }

    public function storetemp(Request $request)
    {
        $kode_produk = $request->kode_produk;
        $shift = $request->shift;
        $unit = $request->unit;
        $jumlah = !empty($request->jumlah) ? str_replace(".", "", $request->jumlah) : 0;
        $id_admin = Auth::user()->id;

        $cek = DB::table('detail_mutasi_produksi_temp')->where('kode_produk', $kode_produk)->where('shift', $shift)->where('unit', $unit)->where('id_admin', $id_admin)->count();
        $data = [
            'kode_produk' => $kode_produk,
            'jumlah' => $jumlah,
            'shift' => $shift,
            'unit' => $unit,
            'inout' => 'OUT',
            'id_admin' => $id_admin
        ];


        if ($cek > 0) {
            echo 1;
        } else {
            $simpan = DB::table('detail_mutasi_produksi_temp')->insert($data);
            if ($simpan) {
                echo 0;
            } else {
                echo 2;
            }
        }
    }

    public function cekfsthptemp(Request $request)
    {
        $kode_produk = $request->kode_produk;
        $unit = $request->unit;
        $shift = $request->shift;
        $id_admin = Auth::user()->id;
        $cek = DB::table('detail_mutasi_produksi_temp')
            ->where('kode_produk', $kode_produk)
            ->where('unit', $unit)
            ->where('shift', $shift)
            ->where('id_admin', $id_admin)
            ->where('inout', 'OUT')->count();
        echo $cek;
    }

    public function deletetemp(Request $request)
    {
        $kode_produk = $request->kode_produk;
        $shift = $request->shift;
        $unit = $request->unit;
        $id_admin = Auth::user()->id;
        $hapus = DB::table('detail_mutasi_produksi_temp')->where('kode_produk', $kode_produk)->where('id_admin', $id_admin)->where('shift', $shift)->where('unit', $unit)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function store(Request $request)
    {
        $no_mutasi_produksi = $request->no_fsthp;
        $tgl_mutasi_produksi = $request->tgl_mutasi_produksi;
        $kode_produk = $request->kode_produk;
        $unit = $request->unit;
        $shift = $request->shift;
        $id_admin = Auth::user()->id;

        $detailtemp = DB::table('detail_mutasi_produksi_temp')
            ->where('kode_produk', $kode_produk)
            ->where('shift', $shift)
            ->where('unit', $unit)
            ->where('inout', 'OUT')
            ->where('id_admin', $id_admin)->get();
        $cek = DB::table('mutasi_produksi')->where('no_mutasi_produksi', $no_mutasi_produksi)->count();
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Sudah Ada']);
        } else {


            DB::beginTransaction();
            try {
                $data = [
                    'no_mutasi_produksi' => $no_mutasi_produksi,
                    'tgl_mutasi_produksi' => $tgl_mutasi_produksi,
                    'unit' => $unit,
                    'inout' => 'OUT',
                    'id_admin' => $id_admin,
                    'jenis_mutasi' => 'FSTHP'
                ];

                DB::table('mutasi_produksi')->insert($data);
                foreach ($detailtemp as $d) {
                    $datadetail = [
                        'no_mutasi_produksi' => $no_mutasi_produksi,
                        'kode_produk' => $d->kode_produk,
                        'shift' => $d->shift,
                        'jumlah' => $d->jumlah
                    ];

                    DB::table('detail_mutasi_produksi')->insert($datadetail);
                }

                DB::table('detail_mutasi_produksi_temp')->where('kode_produk', $kode_produk)
                    ->where('shift', $shift)
                    ->where('unit', $unit)
                    ->where('inout', 'OUT')->where('id_admin', $id_admin)->delete();
                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubunti Tim IT']);
            }
        }
    }

    public function approve($no_mutasi_produksi)
    {
        $no_mutasi_produksi = Crypt::decrypt($no_mutasi_produksi);
        $fsthp = DB::table('mutasi_produksi')->where('no_mutasi_produksi', $no_mutasi_produksi)->first();
        $detailfsthp = DB::table('detail_mutasi_produksi')->where('no_mutasi_produksi', $no_mutasi_produksi)->get();
        $id_admin = Auth::user()->id;
        $cek = DB::table('mutasi_gudang_jadi')->where('no_mutasi_gudang', $no_mutasi_produksi)->count();
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Sudah Ada']);
        } else {
            $data = [
                'no_mutasi_gudang' => $no_mutasi_produksi,
                'tgl_mutasi_gudang' => $fsthp->tgl_mutasi_produksi,
                'inout' => 'IN',
                'jenis_mutasi' => 'FSTHP',
                'id_admin' => $id_admin

            ];
            DB::beginTransaction();
            try {
                DB::table('mutasi_gudang_jadi')->insert($data);
                foreach ($detailfsthp as $d) {
                    $data_detail = [
                        'no_mutasi_gudang' => $no_mutasi_produksi,
                        'kode_produk'  => $d->kode_produk,
                        'shift'  => $d->shift,
                        'jumlah' => $d->jumlah
                    ];

                    DB::table('detail_mutasi_gudang')->insert($data_detail);
                }
                DB::table('mutasi_produksi')->where('no_mutasi_produksi', $no_mutasi_produksi)->update(['status' => 1]);
                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil Di Batalkan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan, Hubungi Tim IT']);
            }
        }
    }

    public function batalkanapprove($no_mutasi_produksi)
    {
        $no_mutasi_produksi = Crypt::decrypt($no_mutasi_produksi);

        DB::beginTransaction();
        try {
            DB::table('mutasi_gudang_jadi')->where('no_mutasi_gudang', $no_mutasi_produksi)->delete();
            DB::table('detail_mutasi_gudang')->where('no_mutasi_gudang', $no_mutasi_produksi)->delete();
            DB::table('mutasi_produksi')->where('no_mutasi_produksi', $no_mutasi_produksi)->update(['status' => null]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Batalkan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan, Hubungi Tim IT']);
        }
    }
}
