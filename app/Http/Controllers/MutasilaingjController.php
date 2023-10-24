<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Mutasigudangjadi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class MutasilaingjController extends Controller
{
    public function index(Request $request)
    {
        $query = Mutasigudangjadi::query();
        $query->select('no_mutasi_gudang', 'tgl_mutasi_gudang', 'inout', 'keterangan');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_mutasi_gudang', [$request->dari, $request->sampai]);
        } else {
            $query->where('tgl_mutasi_gudang', '>=', startreport());
        }

        lockreport($request->dari);
        $query->where('jenis_mutasi', 'LAINLAIN');
        $query->orderBy('tgl_mutasi_gudang', 'desc');
        $mutasi = $query->paginate(15);
        $mutasi->appends($request->all());
        $produk = Barang::orderBy('kode_produk')->get();
        return view('lainnya_gj.index', compact('produk', 'mutasi'));
    }

    public function show($no_mutasi_gudang)
    {
        $no_mutasi_gudang = Crypt::decrypt($no_mutasi_gudang);
        $mutasi = DB::table('mutasi_gudang_jadi')
            ->where('mutasi_gudang_jadi.no_mutasi_gudang', $no_mutasi_gudang)->first();
        $detail = DB::table('detail_mutasi_gudang')
            ->select('detail_mutasi_gudang.*', 'nama_barang')
            ->join('master_barang', 'detail_mutasi_gudang.kode_produk', '=', 'master_barang.kode_produk')
            ->where('no_mutasi_gudang', $no_mutasi_gudang)
            ->get();
        return view('lainnya_gj.show', compact('mutasi', 'detail'));
    }

    public function delete($no_mutasi_gudang)
    {
        $no_mutasi_gudang = Crypt::decrypt($no_mutasi_gudang);
        $hapus = DB::table('mutasi_gudang_jadi')->where('no_mutasi_gudang', $no_mutasi_gudang)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Dat Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Dat Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function buatnomormutasi(Request $request)
    {
        $tgl_mutasi_gudang = $request->tgl_mutasi_gudang;
        $jenis_mutasi = $request->jenis_mutasi;
        $mutasigudang = DB::table('mutasi_gudang_jadi')
            ->select('no_mutasi_gudang')
            ->where('jenis_mutasi', $jenis_mutasi)
            ->where('tgl_mutasi_gudang', $tgl_mutasi_gudang)
            ->orderBy('tgl_mutasi_gudang', 'desc')
            ->first();
        $tanggal = explode("-", $tgl_mutasi_gudang);
        $hari = $tanggal[2];
        $bulan  = $tanggal[1];
        $tahun = $tanggal[0];
        $tgl  = "." . $hari . "." . $bulan . "." . $tahun;
        $last_nomutasi  =  $mutasigudang != null ? $mutasigudang->no_mutasi_gudang : '';
        $kode = "ML";
        $no_mutasi_gudang = buatkode($last_nomutasi, $kode, 2) . $tgl;
        echo $no_mutasi_gudang;
    }

    public function cektemp($jenis_mutasi)
    {
        $id_admin = Auth::user()->id;
        $cek = DB::table('detail_mutasi_gudang_temp')
            ->where('jenis_mutasi', $jenis_mutasi)
            ->where('id_admin', $id_admin)->count();
        echo $cek;
    }

    public function showtemp($jenis_mutasi)
    {
        $id_admin = Auth::user()->id;
        $detailtemp = DB::table('detail_mutasi_gudang_temp')
            ->select('detail_mutasi_gudang_temp.*', 'nama_barang')
            ->join('master_barang', 'detail_mutasi_gudang_temp.kode_produk', '=', 'master_barang.kode_produk')
            ->where('id_admin', $id_admin)
            ->where('jenis_mutasi', $jenis_mutasi)
            ->get();
        return view('lainnya_gj.showtemp', compact('detailtemp'));
    }

    public function storetemp(Request $request)
    {
        $kode_produk = $request->kode_produk;
        $jumlah = !empty($request->jumlah) ? $request->jumlah : 0;
        $jenis_mutasi = $request->jenis_mutasi;
        $id_admin = Auth::user()->id;
        $data = [
            'kode_produk' => $kode_produk,
            'jumlah' => $jumlah,
            'jenis_mutasi' => $jenis_mutasi,
            'id_admin' => $id_admin
        ];
        $cek = DB::table('detail_mutasi_gudang_temp')->where('kode_produk', $kode_produk)
            ->where('jenis_mutasi', $jenis_mutasi)
            ->where('id_admin', $id_admin)->count();
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

    public function deletetemp(Request $request)
    {
        $kode_produk = $request->kode_produk;
        $jenis_mutasi = $request->jenis_mutasi;
        $id_admin = Auth::user()->id;
        $hapus = DB::table('detail_mutasi_gudang_temp')
            ->where('kode_produk', $kode_produk)
            ->where('jenis_mutasi', $jenis_mutasi)
            ->where('id_admin', $id_admin)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function store(Request $request)
    {
        $no_mutasi_gudang = $request->no_mutasi_gudang;
        $tgl_mutasi_gudang = $request->tgl_mutasi_gudang;
        $jenis_mutasi = $request->jenis_mutasi;
        $inout = $request->inout;
        $keterangan = $request->keterangan;
        $id_admin = Auth::user()->id;
        $detailtemp = DB::table('detail_mutasi_gudang_temp')->where('id_admin', $id_admin)
            ->where('jenis_mutasi', $jenis_mutasi)->get();
        DB::beginTransaction();
        try {
            $data = [
                'no_mutasi_gudang' => $no_mutasi_gudang,
                'tgl_mutasi_gudang' => $tgl_mutasi_gudang,
                'jenis_mutasi' => strtoupper($jenis_mutasi),
                'keterangan' => $keterangan,
                'inout' => $inout,
                'id_admin' => $id_admin
            ];

            DB::table('mutasi_gudang_jadi')->insert($data);
            foreach ($detailtemp as $d) {
                $datadetail = [
                    'no_mutasi_gudang' => $no_mutasi_gudang,
                    'kode_produk' => $d->kode_produk,
                    'jumlah' => $d->jumlah
                ];

                DB::table('detail_mutasi_gudang')->insert($datadetail);
            }

            DB::table('detail_mutasi_gudang_temp')->where('jenis_mutasi', $jenis_mutasi)
                ->where('id_admin', $id_admin)
                ->delete();


            DB::commit();
            return redirect('/lainnyagj')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }
}
