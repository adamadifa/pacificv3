<?php

namespace App\Http\Controllers;

use App\Models\Detailsaldoawalgudangbahan;
use App\Models\Saldoawalgudangbahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SaldoawalgudangbahanController extends Controller
{
    public function index(Request $request)
    {
        $query = Saldoawalgudangbahan::query();
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }

        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
        }


        $query->select('saldoawal_gb.*');
        $query->orderBy('tahun', 'desc');
        $query->orderBy('bulan', 'asc');
        $saldoawal = $query->paginate(15);
        $saldoawal->appends($request->all());
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalgudangbahan.index', compact('bulan', 'saldoawal'));
    }

    public function delete($kode_saldoawal)
    {
        $kode_saldoawal = Crypt::decrypt($kode_saldoawal);
        $hapus = DB::table('saldoawal_gb')->where('kode_saldoawal_gb', $kode_saldoawal)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function edit($kode_saldoawal)
    {
        $kode_saldoawal = Crypt::decrypt($kode_saldoawal);
        $saldoawal = DB::table('saldoawal_gb')->where('kode_saldoawal_gb', $kode_saldoawal)->first();
        $detail = DB::table('saldoawal_gb_detail')
            ->join('master_barang_pembelian', 'saldoawal_gb_detail.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->join('kategori_barang_pembelian', 'master_barang_pembelian.kode_kategori', '=', 'kategori_barang_pembelian.kode_kategori')
            ->where('kode_saldoawal_gb', $kode_saldoawal)
            ->orderBy('nama_barang')
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalgudangbahan.edit', compact('detail', 'saldoawal', 'bulan'));
    }

    public function editbarang($kode_saldoawal, $kode_barang)
    {
        $detail = DB::table('saldoawal_gb_detail')
            ->join('master_barang_pembelian', 'saldoawal_gb_detail.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->join('kategori_barang_pembelian', 'master_barang_pembelian.kode_kategori', '=', 'kategori_barang_pembelian.kode_kategori')
            ->where('kode_saldoawal_gb', $kode_saldoawal)
            ->where('saldoawal_gb_detail.kode_barang', $kode_barang)
            ->first();
        return view('saldoawalgudangbahan.editbarang', compact('detail'));
    }

    public function updatebarang($kode_saldoawal, $kode_barang, Request $request)
    {
        $kode_saldoawal = Crypt::decrypt($kode_saldoawal);
        $kode_barang = Crypt::decrypt($kode_barang);
        $qty_unit = !empty($request->qty_unit) ? str_replace(".", "", $request->qty_unit) : 0;
        $qty_unit = str_replace(",", ".", $qty_unit);
        $qty_berat = !empty($request->qty_berat) ? str_replace(".", "", $request->qty_berat) : 0;
        $qty_berat = str_replace(",", ".", $qty_berat);
        $data = [
            'qty_unit' => $qty_unit,
            'qty_berat' => $qty_berat
        ];
        $update = DB::table('saldoawal_gb_detail')->where('kode_saldoawal_gb', $kode_saldoawal)->where('kode_barang', $kode_barang)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalgudangbahan.create', compact('bulan'));
    }

    public function getdetailsaldo(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kategori = $request->kategori;
        if ($bulan == 1) {
            $bulanlalu = 12;
            $tahunlalu = $tahun - 1;
        } else {
            $bulanlalu = $bulan - 1;
            $tahunlalu = $tahun;
        }

        $ceksaldo = DB::table('saldoawal_gb')->where('bulan', $bulanlalu)->where('tahun', $tahunlalu)->count();
        $ceknow = DB::table('saldoawal_gb')->where('bulan', $bulan)->where('tahun', $tahun)->count();
        $cekall = DB::table('saldoawal_gb')->count();
        if (empty($ceksaldo) && !empty($cekall) || !empty($ceknow)) {
            echo "1";
        } else {
            $detail = DB::table('master_barang_pembelian')
                ->selectRaw(" master_barang_pembelian.kode_barang,
                master_barang_pembelian.nama_barang,kategori,
                master_barang_pembelian.satuan,
                sa.qtyunitsa,
                sa.qtyberatsa,
                gm.qtypemb1,
                gm.qtylainnya1,
                gm.qtyreturpengganti1,
                gm.qtyreturpengganti2,
                gm.qtypemb2,
                gm.qtylainnya2,
                gk.qtyprod3,
                gk.qtyseas3,
                gk.qtypdqc3,
                gk.qtysus3,
                gk.qtylain3,
                gk.qtycabang3,
                gk.qtyprod4,
                gk.qtyseas4,
                gk.qtypdqc4,
                gk.qtysus4,
                gk.qtylain4,
                gk.qtycabang4")
                ->join('kategori_barang_pembelian', 'master_barang_pembelian.kode_kategori', '=', 'kategori_barang_pembelian.kode_kategori')
                ->leftJoin(
                    DB::raw("(
                        SELECT saldoawal_gb_detail.kode_barang,SUM( qty_unit ) AS qtyunitsa,SUM( qty_berat ) AS qtyberatsa FROM saldoawal_gb_detail
                        INNER JOIN saldoawal_gb ON saldoawal_gb.kode_saldoawal_gb=saldoawal_gb_detail.kode_saldoawal_gb
                        WHERE bulan = '$bulanlalu' AND tahun = '$tahunlalu' GROUP BY saldoawal_gb_detail.kode_barang
                    ) sa"),
                    function ($join) {
                        $join->on('master_barang_pembelian.kode_barang', '=', 'sa.kode_barang');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                        SELECT
                        detail_pemasukan_gb.kode_barang,
                        SUM( IF( departemen = 'Pembelian' , qty_unit ,0 )) AS qtypemb1,
                        SUM( IF( departemen = 'Lainnya' , qty_unit ,0 )) AS qtylainnya1,
                        SUM( IF( departemen = 'Retur Pengganti' , qty_unit ,0 )) AS qtyreturpengganti1,

                        SUM( IF( departemen = 'Pembelian' , qty_berat ,0 )) AS qtypemb2,
                        SUM( IF( departemen = 'Lainnya' , qty_berat ,0 )) AS qtylainnya2,
                        SUM( IF( departemen = 'Retur Pengganti' , qty_berat ,0 )) AS qtyreturpengganti2,
                        SUM( (IF( departemen = 'Pembelian' , qty_berat ,0 )) + (IF( departemen = 'Lainnya' , qty_berat ,0 ))) AS pemasukanqtyberat
                        FROM
                        detail_pemasukan_gb
                        INNER JOIN pemasukan_gb ON detail_pemasukan_gb.nobukti_pemasukan = pemasukan_gb.nobukti_pemasukan
                        WHERE MONTH(tgl_pemasukan) = '$bulanlalu' AND YEAR(tgl_pemasukan) = '$tahunlalu'
                        GROUP BY detail_pemasukan_gb.kode_barang
                    ) gm"),
                    function ($join) {
                        $join->on('master_barang_pembelian.kode_barang', '=', 'gm.kode_barang');
                    }
                )

                ->leftJoin(
                    DB::raw("(
                        SELECT
                        detail_pengeluaran_gb.kode_barang,
                        SUM( IF( kode_dept = 'Produksi' , qty_unit ,0 )) AS qtyprod3,
                        SUM( IF( kode_dept = 'Seasoning' , qty_unit ,0 )) AS qtyseas3,
                        SUM( IF( kode_dept = 'PDQC' , qty_unit ,0 )) AS qtypdqc3,
                        SUM( IF( kode_dept = 'Susut' , qty_unit ,0 )) AS qtysus3,
                        SUM( IF( kode_dept = 'Lainnya' , qty_unit ,0 )) AS qtylain3,
                        SUM( IF( kode_dept = 'Cabang' , qty_unit ,0 )) AS qtycabang3,

                        SUM( IF( kode_dept = 'Produksi' , qty_berat ,0 )) AS qtyprod4,
                        SUM( IF( kode_dept = 'Seasoning' , qty_berat ,0 )) AS qtyseas4,
                        SUM( IF( kode_dept = 'PDQC' , qty_berat ,0 )) AS qtypdqc4,
                        SUM( IF( kode_dept = 'Susut' , qty_berat ,0 )) AS qtysus4,
                        SUM( IF( kode_dept = 'Lainnya' , qty_berat ,0 )) AS qtylain4,
                        SUM( IF( kode_dept = 'Cabang' , qty_berat ,0 )) AS qtycabang4
                        FROM detail_pengeluaran_gb
                        INNER JOIN pengeluaran_gb ON detail_pengeluaran_gb.nobukti_pengeluaran = pengeluaran_gb.nobukti_pengeluaran
                        WHERE MONTH(tgl_pengeluaran) = '$bulanlalu' AND YEAR(tgl_pengeluaran) = '$tahunlalu'
                        GROUP BY detail_pengeluaran_gb.kode_barang
                    ) gk"),
                    function ($join) {
                        $join->on('master_barang_pembelian.kode_barang', '=', 'gk.kode_barang');
                    }
                )
                ->where('master_barang_pembelian.kode_dept', 'GDB')
                ->where('master_barang_pembelian.kode_kategori', '!=', 'K002')
                ->orderBy('master_barang_pembelian.nama_barang')
                ->get();

            return view('saldoawalgudangbahan.getdetailsaldo', compact('detail'));
        }
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $thn = substr($tahun, 2, 2);
        $kode_saldoawal = "GB" . $bulan . $thn;
        $tanggal = $request->tanggal;

        $kode_barang = $request->kode_barang;
        $qty_unit = $request->qty_unit;
        $qty_berat = $request->qty_berat;
        $data = [
            'kode_saldoawal_gb' => $kode_saldoawal,
            'tanggal'           => $tanggal,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
        ];

        for ($i = 0; $i < count($kode_barang); $i++) {
            $detail_saldo[]   = [
                'kode_saldoawal_gb' => $kode_saldoawal,
                'kode_barang'       => $kode_barang[$i],
                'qty_unit'          => $qty_unit[$i],
                'qty_berat'         => $qty_berat[$i]
            ];
        }


        //dd($detail_saldo);

        //dd($chunks);
        DB::beginTransaction();
        try {
            DB::table('saldoawal_gb')->insert($data);
            $chunks = array_chunk($detail_saldo, 50);
            foreach ($chunks as $chunk) {
                Detailsaldoawalgudangbahan::insert($chunk);
            }
            DB::commit();
            return redirect('/saldoawalgudangbahan?tahun=' . $tahun)->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/saldoawalgudangbahan?tahun=' . $tahun)->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }
}
