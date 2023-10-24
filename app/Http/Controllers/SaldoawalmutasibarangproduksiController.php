<?php

namespace App\Http\Controllers;

use App\Models\Saldoawalmutasibarangproduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SaldoawalmutasibarangproduksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Saldoawalmutasibarangproduksi::query();
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }

        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
            lockyear($request->tahun);
        } else {
            $query->where('tahun', '>=', startyear());
        }
        $query->orderBy('tahun', 'desc');
        $query->orderBy('bulan', 'asc');
        $saldoawal = $query->paginate(15);
        $saldoawal->appends($request->all());
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

        return view('saldoawalmutasibarangproduksi.index', compact('bulan', 'saldoawal'));
    }

    public function delete($kode_saldoawal)
    {
        $kode_saldoawal = Crypt::decrypt($kode_saldoawal);
        $hapus = DB::table('saldoawal_gp')->where('kode_saldoawal', $kode_saldoawal)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function edit($kode_saldoawal)
    {
        $kode_saldoawal = Crypt::decrypt($kode_saldoawal);
        $saldoawal = DB::table('saldoawal_gp')->where('kode_saldoawal', $kode_saldoawal)->first();
        $detail = DB::table('saldoawal_gp_detail')
            ->join('master_barang_produksi', 'saldoawal_gp_detail.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('kode_saldoawal', $kode_saldoawal)
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalmutasibarangproduksi.edit', compact('detail', 'saldoawal', 'bulan'));
    }

    public function editbarang($kode_saldoawal, $kode_barang)
    {
        $detail = DB::table('saldoawal_gp_detail')
            ->join('master_barang_produksi', 'saldoawal_gp_detail.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('kode_saldoawal', $kode_saldoawal)
            ->where('saldoawal_gp_detail.kode_barang', $kode_barang)
            ->first();
        return view('saldoawalmutasibarangproduksi.editbarang', compact('detail'));
    }

    public function updatebarang($kode_saldoawal, $kode_barang, Request $request)
    {
        $kode_saldoawal = Crypt::decrypt($kode_saldoawal);
        $kode_barang = Crypt::decrypt($kode_barang);
        $qty = !empty($request->qty) ? $request->qty : 0;
        $data = [
            'qty' => $qty
        ];
        $update = DB::table('saldoawal_gp_detail')->where('kode_saldoawal', $kode_saldoawal)->where('kode_barang', $kode_barang)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalmutasibarangproduksi.create', compact('bulan'));
    }

    public function getdetailsaldo(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        if ($bulan == 1) {
            $bulanlalu = 12;
            $tahunlalu = $tahun - 1;
        } else {
            $bulanlalu = $bulan - 1;
            $tahunlalu = $tahun;
        }

        $ceksaldo = DB::table('saldoawal_gp')->where('bulan', $bulanlalu)->where('tahun', $tahunlalu)->count();
        $ceknow = DB::table('saldoawal_gp')->where('bulan', $bulan)->where('tahun', $tahun)->count();
        $cekall = DB::table('saldoawal_gp')->count();
        if (empty($ceksaldo) && !empty($cekall) || !empty($ceknow)) {
            echo "1";
        } else {
            $detail = DB::table('master_barang_produksi')
                ->selectRaw(" master_barang_produksi.kode_barang,
                master_barang_produksi.nama_barang,
                pemasukan.qtypemasukan,
                pengeluaran.qtypengeluaran,
                sa.qtysaldoawal")
                ->leftJoin(
                    DB::raw("(
                        SELECT saldoawal_gp_detail.kode_barang,SUM( qty ) AS qtysaldoawal FROM saldoawal_gp_detail
                        INNER JOIN saldoawal_gp ON saldoawal_gp.kode_saldoawal=saldoawal_gp_detail.kode_saldoawal
                        WHERE bulan = '$bulanlalu' AND tahun = '$tahunlalu' GROUP BY saldoawal_gp_detail.kode_barang
                    ) sa"),
                    function ($join) {
                        $join->on('master_barang_produksi.kode_barang', '=', 'sa.kode_barang');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                        SELECT detail_pemasukan_gp.kode_barang,SUM( qty ) AS qtypemasukan FROM
                        detail_pemasukan_gp
                        INNER JOIN pemasukan_gp ON detail_pemasukan_gp.nobukti_pemasukan = pemasukan_gp.nobukti_pemasukan
                        WHERE MONTH(tgl_pemasukan) = '$bulanlalu' AND YEAR(tgl_pemasukan) = '$tahunlalu'
                        GROUP BY detail_pemasukan_gp.kode_barang
                    ) pemasukan"),
                    function ($join) {
                        $join->on('master_barang_produksi.kode_barang', '=', 'pemasukan.kode_barang');
                    }
                )

                ->leftJoin(
                    DB::raw("(
                        SELECT detail_pengeluaran_gp.kode_barang,SUM( qty ) AS qtypengeluaran FROM detail_pengeluaran_gp
                        INNER JOIN pengeluaran_gp ON detail_pengeluaran_gp.nobukti_pengeluaran = pengeluaran_gp.nobukti_pengeluaran
                        WHERE MONTH(tgl_pengeluaran) = '$bulanlalu' AND YEAR(tgl_pengeluaran) = '$tahunlalu'
                        GROUP BY detail_pengeluaran_gp.kode_barang
                    ) pengeluaran"),
                    function ($join) {
                        $join->on('master_barang_produksi.kode_barang', '=', 'pengeluaran.kode_barang');
                    }
                )

                ->where('master_barang_produksi.status', 'Aktif')
                ->groupByRaw("master_barang_produksi.kode_barang,
                master_barang_produksi.nama_barang,
                pemasukan.qtypemasukan,
                pengeluaran.qtypengeluaran,
                sa.qtysaldoawal")
                ->get();

            return view('saldoawalmutasibarangproduksi.getdetailsaldo', compact('detail'));
        }
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $thn = substr($tahun, 2, 2);
        $kode_saldoawal = "GB" . $bulan . $thn;
        $tanggal = $request->tanggal;
        $jumlahdata = $request->jumlahdata;

        $data = [
            'kode_saldoawal'    => $kode_saldoawal,
            'tanggal'           => $tanggal,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
        ];

        DB::beginTransaction();
        try {
            DB::table('saldoawal_gp')->insert($data);
            for ($i = 1; $i <= $jumlahdata; $i++) {
                $kb = "kode_barang" . $i;
                $q = "qty" . $i;
                $kode_barang  = $request->$kb;
                $qty = $request->$q;

                $detail_saldo   = array(
                    'kode_saldoawal'    => $kode_saldoawal,
                    'kode_barang'       => $kode_barang,
                    'qty'               => $qty
                );
                DB::table('saldoawal_gp_detail')->insert($detail_saldo);
            }
            DB::commit();
            return redirect('/saldoawalmutasibarangproduksi?tahun=' . $tahun)->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/saldoawalmutasibarangproduksi?tahun=' . $tahun)->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }
}
