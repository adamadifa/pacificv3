<?php

namespace App\Http\Controllers;

use App\Models\Opnamemutasibarangproduksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class OpnamemutasibarangproduksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Opnamemutasibarangproduksi::query();
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }

        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
        }
        $query->orderBy('tahun', 'desc');
        $query->orderBy('bulan', 'asc');
        $opname = $query->paginate(15);
        $opname->appends($request->all());
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('opnamemutasibarangproduksi.index', compact('bulan', 'opname'));
    }

    public function create()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('opnamemutasibarangproduksi.create', compact('bulan'));
    }

    public function getdetailopname(Request $request)
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

        $bulanskrg = $bulan;
        $tahunskrg = $tahun;

        $ceksaldo = DB::table('opname_gp')->where('bulan', $bulanlalu)->where('tahun', $tahunlalu)->count();
        $ceknow = DB::table('opname_gp')->where('bulan', $bulan)->where('tahun', $tahun)->count();
        $cekall = DB::table('opname_gp')->count();
        if (empty($ceksaldo) && !empty($cekall) || !empty($ceknow)) {
            echo "1";
        } else {
            $detail = DB::table('master_barang_produksi')
                ->selectRaw(" master_barang_produksi.kode_barang,
                master_barang_produksi.nama_barang,
                sa.saldoawal,
                op.opname,
                pemasukan.gudang,
                pemasukan.seasoning,
                pemasukan.trial,
                pengeluaran.pemakaian,
                pengeluaran.retur,
                pengeluaran.lainnya")
                ->leftJoin(
                    DB::raw("(
                        SELECT saldoawal_gp_detail.kode_barang,SUM( qty ) AS saldoawal FROM saldoawal_gp_detail
                        INNER JOIN saldoawal_gp ON saldoawal_gp.kode_saldoawal=saldoawal_gp_detail.kode_saldoawal
                        WHERE bulan = '$bulanskrg' AND tahun = '$tahunskrg' GROUP BY saldoawal_gp_detail.kode_barang
                    ) sa"),
                    function ($join) {
                        $join->on('master_barang_produksi.kode_barang', '=', 'sa.kode_barang');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                        SELECT opname_gp_detail.kode_barang,SUM( qty ) AS opname FROM opname_gp_detail
                        INNER JOIN opname_gp ON opname_gp.kode_opname=opname_gp_detail.kode_opname
                        WHERE bulan = '$bulanskrg' AND tahun = '$tahunskrg' GROUP BY opname_gp_detail.kode_barang
                    ) op"),
                    function ($join) {
                        $join->on('master_barang_produksi.kode_barang', '=', 'op.kode_barang');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                        SELECT
                        detail_pemasukan_gp.kode_barang,
                        SUM( IF( kode_dept = 'Gudang' , qty ,0 )) AS gudang,
                        SUM( IF( kode_dept = 'Seasoning' , qty ,0 )) AS seasoning,
                        SUM( IF( kode_dept = 'Trial' , qty ,0 )) AS trial
                        FROM
                        detail_pemasukan_gp
                        INNER JOIN pemasukan_gp ON detail_pemasukan_gp.nobukti_pemasukan = pemasukan_gp.nobukti_pemasukan
                        WHERE MONTH(tgl_pemasukan) = '$bulanskrg' AND YEAR(tgl_pemasukan) = '$tahunskrg'
                        GROUP BY detail_pemasukan_gp.kode_barang
                    ) pemasukan"),
                    function ($join) {
                        $join->on('master_barang_produksi.kode_barang', '=', 'pemasukan.kode_barang');
                    }
                )

                ->leftJoin(
                    DB::raw("(
                        SELECT
                        detail_pengeluaran_gp.kode_barang,
                        SUM( IF( kode_dept = 'Pemakaian' , qty ,0 )) AS pemakaian,
                        SUM( IF( kode_dept = 'Retur Out' , qty ,0 )) AS retur,
                        SUM( IF( kode_dept = 'Lainnya' , qty ,0 )) AS lainnya
                        FROM detail_pengeluaran_gp
                        INNER JOIN pengeluaran_gp ON detail_pengeluaran_gp.nobukti_pengeluaran = pengeluaran_gp.nobukti_pengeluaran
                        WHERE MONTH(tgl_pengeluaran) = '$bulanskrg' AND YEAR(tgl_pengeluaran) = '$tahunskrg'
                        GROUP BY detail_pengeluaran_gp.kode_barang
                    ) pengeluaran"),
                    function ($join) {
                        $join->on('master_barang_produksi.kode_barang', '=', 'pengeluaran.kode_barang');
                    }
                )

                ->where('master_barang_produksi.status', 'Aktif')
                ->groupByRaw("master_barang_produksi.kode_barang,
                master_barang_produksi.nama_barang,
                sa.saldoawal,
                op.opname,
                pemasukan.gudang,
                pemasukan.seasoning,
                pemasukan.trial,
                pengeluaran.pemakaian,
                pengeluaran.retur,
                pengeluaran.lainnya")
                ->get();

            return view('opnamemutasibarangproduksi.getdetailopname', compact('detail'));
        }
    }

    public function delete($kode_opname)
    {
        $kode_opname = Crypt::decrypt($kode_opname);
        $hapus = DB::table('opname_gp')->where('kode_opname', $kode_opname)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $thn = substr($tahun, 2, 2);
        $kode_opname = "OP" . $bulan . $thn;
        $tanggal = $request->tanggal;
        $jumlahdata = $request->jumlahdata;

        $data = [
            'kode_opname'       => $kode_opname,
            'tanggal'           => $tanggal,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
        ];

        DB::beginTransaction();
        try {
            DB::table('opname_gp')->insert($data);
            for ($i = 1; $i <= $jumlahdata; $i++) {
                $kb = "kode_barang" . $i;
                $q = "qty" . $i;
                $kode_barang  = $request->$kb;
                $qty = $request->$q;

                $detail_saldo   = array(
                    'kode_opname'    => $kode_opname,
                    'kode_barang'    => $kode_barang,
                    'qty'            => $qty
                );
                DB::table('opname_gp_detail')->insert($detail_saldo);
            }
            DB::commit();
            return redirect('/opnamemutasibarangproduksi?tahun=' . $tahun)->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/opnamemutasibarangproduksi?tahun=' . $tahun)->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }

    public function edit($kode_opname)
    {
        $kode_opname = Crypt::decrypt($kode_opname);
        $opname = DB::table('opname_gp')->where('kode_opname', $kode_opname)->first();
        $detail = DB::table('opname_gp_detail')
            ->join('master_barang_produksi', 'opname_gp_detail.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('kode_opname', $kode_opname)
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('opnamemutasibarangproduksi.edit', compact('detail', 'opname', 'bulan'));
    }

    public function editbarang($kode_opname, $kode_barang)
    {
        $detail = DB::table('opname_gp_detail')
            ->join('master_barang_produksi', 'opname_gp_detail.kode_barang', '=', 'master_barang_produksi.kode_barang')
            ->where('kode_opname', $kode_opname)
            ->where('opname_gp_detail.kode_barang', $kode_barang)
            ->first();
        return view('opnamemutasibarangproduksi.editbarang', compact('detail'));
    }

    public function updatebarang($kode_opname, $kode_barang, Request $request)
    {
        $kode_opname = Crypt::decrypt($kode_opname);
        $kode_barang = Crypt::decrypt($kode_barang);
        $qty = !empty($request->qty) ? $request->qty : 0;
        $data = [
            'qty' => $qty
        ];
        $update = DB::table('opname_gp_detail')->where('kode_opname', $kode_opname)->where('kode_barang', $kode_barang)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate, Hubungi Tim IT']);
        }
    }
}