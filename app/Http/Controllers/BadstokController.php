<?php

namespace App\Http\Controllers;

use App\Models\Badstok;
use App\Models\Cabang;
use App\Models\Detailbadstok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BadstokController extends Controller
{
    public function index(Request $request)
    {
        $query = Badstok::query();
        $query->selectRaw('no_bs,tanggal,kode_cabang');
        $badstok = $query->paginate(15);
        $badstok->appends($request->all());
        return view('badstok.index', compact('badstok'));
    }

    public function create()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $produk = DB::table('master_barang')->orderBy('kode_produk')->where('status', 1)->get();
        return view('badstok.create', compact('cabang', 'produk'));
    }

    public function store(Request $request)
    {
        $tanggal = $request->tanggal;
        $kode_cabang = $request->kode_cabang;
        $kode_produk = $request->kode_produk;
        $jumlah = $request->jumlah;
        $tgl = explode("-", $tanggal);
        $bulan = $tgl[1];
        $tahun = substr($tgl[0], 2);
        $badstok = DB::table("badstok")
            ->whereRaw('MONTH(tanggal)=' . $bulan)
            ->whereRaw('YEAR(tanggal)=' . $tahun)
            ->orderBy("no_bs", "desc")
            ->first();

        $lastnobs = $badstok != null ? $badstok->no_bs : '';

        $no_bs  = buatkode($lastnobs, "BS" . $bulan . $tahun, 2);
        for ($i = 0; $i < count($kode_produk); $i++) {
            $jml = !empty($jumlah[$i]) ? $jumlah[$i] : 0;
            if (!empty($jml)) {
                $data_detail[]   = [
                    'no_bs' => $no_bs,
                    'kode_produk' => $kode_produk[$i],
                    'jumlah' => $jml
                ];
            }
        }

        //dd($data_detail);
        $data = [
            'no_bs' => $no_bs,
            'tanggal' => $tanggal,
            'kode_cabang' => $kode_cabang
        ];

        DB::beginTransaction();
        try {
            DB::table('badstok')->insert($data);
            $chunks = array_chunk($data_detail, 5);
            foreach ($chunks as $chunk) {
                Detailbadstok::insert($chunk);
                // }
            }

            DB::commit();
            return redirect('/badstock')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect('/badstock')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function show(Request $request)
    {
        $no_bs = $request->no_bs;
        $badstok = DB::table('badstok')->where('no_bs', $no_bs)->first();
        $detail = DB::table('badstok_detail')->where('no_bs', $no_bs)
            ->join('master_barang', 'badstok_detail.kode_produk', '=', 'master_barang.kode_produk')->get();
        return view('badstok.show', compact('badstok', 'detail'));
    }

    public function delete($no_bs)
    {
        $no_bs = Crypt::decrypt($no_bs);
        $hapus = DB::table('badstok')->where('no_bs', $no_bs)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}
