<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Dpb;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use PDOException;

class KendaraanController extends Controller
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
        $query = Kendaraan::query();
        if ($this->cabang != "PCF") {
            $query->where('kode_cabang', $this->cabang);
        }
        if (isset($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }
        if (isset($request->no_polisi)) {
            $query->where('no_polisi', 'like', '%' . $request->no_polisi . '%');
        }
        $query->select('*');
        $kendaraan = $query->paginate(15);
        $kendaraan->appends($request->all());
        $cabang = Cabang::all();
        return view('kendaraan.index', compact('kendaraan', 'cabang'));
    }

    public function create()
    {
        $cabang = Cabang::all();
        return view('kendaraan.create', compact('cabang'));
    }

    public function store(Request $request)
    {

        //dd($request);
        $request->validate([
            'no_polisi' => 'required',
            'type' => 'required',
            'model' => 'required',
            'tahun' => 'required',
            // 'no_mesin' => 'required',
            // 'no_rangka' => 'required',
            // 'no_stnk' => 'required',
            // 'pajak' => 'required',
            // 'atas_nama' => 'required',
            // 'keur' => 'required',
            // 'no_uji' => 'required',
            // 'kir' => 'required',
            // 'stnk' => 'required',
            // 'sipa' => 'required',
            'pemakai' => 'required',
            // 'jabatan' => 'required',
            'kode_cabang' => 'required',
            'status' => 'required'
        ]);


        $simpan = DB::table('kendaraan')
            ->insert([
                'no_polisi' => $request->no_polisi,
                'type' => $request->type,
                'model' => $request->model,
                'tahun' => $request->tahun,
                // 'no_mesin' => $request->no_mesin,
                // 'no_rangka' => $request->no_rangka,
                // 'no_stnk' => $request->no_stnk,
                // 'pajak' => $request->pajak,
                // 'atas_nama' => $request->atas_nama,
                // 'keur' => $request->keur,
                // 'no_uji' => $request->no_uji,
                // 'kir' => $request->kir,
                // 'stnk' => $request->stnk,
                // 'sipa' => $request->sipa,
                'pemakai' => $request->pemakai,
                // 'jabatan' => $request->jabatan,
                // 'keterangan' => $request->keterangan,
                'kode_cabang' => $request->kode_cabang,
                'status' => $request->status
            ]);

        if ($simpan) {
            return redirect('/kendaraan')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/kendaraan')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function update(Request $request, $id)
    {

        $id = Crypt::decrypt($id);
        //dd($request);
        $request->validate([
            'no_polisi' => 'required',
            'type' => 'required',
            'model' => 'required',
            'tahun' => 'required',
            // 'no_mesin' => 'required',
            // 'no_rangka' => 'required',
            // 'no_stnk' => 'required',
            // 'pajak' => 'required',
            // 'atas_nama' => 'required',
            // 'keur' => 'required',
            // 'no_uji' => 'required',
            // 'kir' => 'required',
            // 'stnk' => 'required',
            // 'sipa' => 'required',
            'pemakai' => 'required',
            // 'jabatan' => 'required',
            'kode_cabang' => 'required',
            'status' => 'required'
        ]);


        $simpan = DB::table('kendaraan')
            ->where('id', $id)
            ->update([
                'no_polisi' => $request->no_polisi,
                'type' => $request->type,
                'model' => $request->model,
                'tahun' => $request->tahun,
                'no_mesin' => $request->no_mesin,
                'no_rangka' => $request->no_rangka,
                'no_stnk' => $request->no_stnk,
                'pajak' => $request->pajak,
                'atas_nama' => $request->atas_nama,
                'keur' => $request->keur,
                'no_uji' => $request->no_uji,
                'kir' => $request->kir,
                'stnk' => $request->stnk,
                'sipa' => $request->sipa,
                'pemakai' => $request->pemakai,
                'jabatan' => $request->jabatan,
                'keterangan' => $request->keterangan,
                'kode_cabang' => $request->kode_cabang,
                'status' => $request->status
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $cabang = Cabang::all();
        $data = Kendaraan::where('id', $id)->first();
        return view('kendaraan.edit', compact('cabang', 'data'));
    }
    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        try {
            $hapus = DB::table('kendaraan')
                ->where('id', $id)
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
        $id = $request->id;
        $data = Kendaraan::where('id', $id)->first();
        return view('kendaraan.show', compact('data'));
    }
    function rekapkendaraandashboard(Request $request)
    {
        $dari = $request->tahun . "-" . $request->bulan . "-01";
        $sampai = date('Y-m-t', strtotime($dari));
        $cabang = $request->cabang;
        $query = Kendaraan::query();
        if (!empty($cabang)) {
            $query->where('kendaraan.kode_cabang', $cabang);
        }
        $query->select('kendaraan.no_polisi', 'model', 'jml_berangkat', 'jmlpenjualan');
        $query->leftJoin(
            DB::raw("(
                SELECT no_kendaraan,COUNT(no_kendaraan) as jml_berangkat
                FROM dpb
                WHERE tgl_pengambilan BETWEEN '$dari' AND '$sampai'
                GROUP BY no_kendaraan
            ) dpb"),
            function ($join) {
                $join->on('kendaraan.no_polisi', '=', 'dpb.no_kendaraan');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT no_kendaraan,
				ROUND(SUM(IF(jenis_mutasi = 'PENJUALAN',jumlah,0) /isipcsdus),2) as jmlpenjualan
				FROM detail_mutasi_gudang_cabang
				INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
				INNER JOIN dpb ON mutasi_gudang_cabang.no_dpb = dpb.no_dpb
				INNER JOIN master_barang ON detail_mutasi_gudang_cabang.kode_produk = master_barang.kode_produk
				WHERE tgl_mutasi_gudang_cabang BETWEEN  '$dari' AND '$sampai'
				GROUP BY no_kendaraan
            ) penjualan"),
            function ($join) {
                $join->on('kendaraan.no_polisi', '=', 'penjualan.no_kendaraan');
            }
        );

        $rekapkendaraan = $query->get();
        return view('kendaraan.dashboard.rekapkendaraan', compact('rekapkendaraan'));
    }

    public function laporanrekapkendaraan()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        }
        return view('kendaraan.laporan.frm.lap_rekapkendaraan', compact('cabang'));
    }

    public function getkendaraancab(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        if ($kode_cabang == "GRT") {
            $kode_cabang = "TSM";
        }
        $no_polisi = $request->no_polisi;
        $kendaraan = Kendaraan::where('kode_cabang', $kode_cabang)->get();
        echo "<option value=''>Pilih Kendaraan</option>";
        foreach ($kendaraan as $d) {
            if ($no_polisi == $d->no_polisi) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            echo "<option $selected value='$d->no_polisi'>" . $d->no_polisi . " - " . $d->type . "</option>";
        }
    }

    public function cetaklaporanrekapkendaraan(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $cabang = DB::table('cabang')->where('kode_cabang', $request->kode_cabang)->first();
        $kendaraan = DB::table('kendaraan')->where('no_polisi', $request->no_polisi)->first();
        $query = Dpb::query();
        $query->selectRaw("dpb.tgl_pengambilan, no_kendaraan,COUNT(no_dpb) as jmlpengambilan");
        $query->where('no_kendaraan', $request->no_polisi);
        $query->whereBetween('tgl_pengambilan', [$dari, $sampai]);
        $query->groupByRaw('tgl_pengambilan,no_kendaraan');
        $historikendaraan = $query->get();

        $rekapkendaraan = DB::table('detail_dpb')
            ->selectRaw("detail_dpb.kode_produk,isipcsdus,ROUND(SUM(jml_pengambilan),2) as jml_pengambilan,
        ROUND(SUM(jml_pengembalian),2) as jml_pengembalian,
        jmlpenjualan,jmlgantibarang,jmlplhk,jmlpromosi,jmlttr,
        dpb.no_kendaraan")
            ->join('dpb', 'detail_dpb.no_dpb', '=', 'dpb.no_dpb')
            ->join('master_barang', 'detail_dpb.kode_produk', '=', 'master_barang.kode_produk')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_produk,
                    ROUND(SUM(IF(jenis_mutasi = 'PENJUALAN',jumlah,0)),2) as jmlpenjualan,
                    ROUND(SUM(IF(jenis_mutasi = 'PL HUTANG KIRIM',jumlah,0)),2) as jmlplhk,
                    ROUND(SUM(IF(jenis_mutasi = 'PROMOSI',jumlah,0)),2) as jmlpromosi,
                    ROUND(SUM(IF(jenis_mutasi = 'TTR',jumlah,0)),2) as jmlttr,
                    ROUND(SUM(IF(jenis_mutasi = 'GANTI BARANG',jumlah,0)),2) as jmlgantibarang,
                    ROUND(SUM(IF(jenis_mutasi = 'RETUR',jumlah,0)),2) as jmlretur,
                    ROUND(SUM(IF(jenis_mutasi = 'PL TTR',jumlah,0)),2) as jmlplttr,
                    ROUND(SUM(IF(jenis_mutasi = 'HUTANG KIRIM',jumlah,0)),2) as jmlhk,
                    no_kendaraan
                    FROM detail_mutasi_gudang_cabang
                    INNER JOIN mutasi_gudang_cabang ON detail_mutasi_gudang_cabang.no_mutasi_gudang_cabang = mutasi_gudang_cabang.no_mutasi_gudang_cabang
                    INNER JOIN dpb ON mutasi_gudang_cabang.no_dpb = dpb.no_dpb
                    WHERE tgl_mutasi_gudang_cabang BETWEEN '$dari' AND '$sampai' AND no_kendaraan = '$request->no_polisi'
                    GROUP BY kode_produk,no_kendaraan
                ) penjualan"),
                function ($join) {
                    $join->on('detail_dpb.kode_produk', '=', 'penjualan.kode_produk');
                }
            )
            ->whereBetween('tgl_pengambilan', [$dari, $sampai])
            ->where('dpb.no_kendaraan', $request->no_polisi)
            ->groupByRaw('kode_produk,dpb.no_kendaraan,isipcsdus,jmlpenjualan,jmlgantibarang,jmlplhk,jmlpromosi,jmlttr')
            ->get();
        if (isset($_POST['export'])) {
            $time = date("H:i:s");
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Rekap Kendaraan Periode $dari-$sampai-$time.xls");
        }

        return view('kendaraan.laporan.cetak_rekapkendaraan', compact('historikendaraan', 'rekapkendaraan', 'dari', 'sampai', 'cabang', 'kendaraan'));
    }
}