<?php

namespace App\Http\Controllers;

use App\Models\Detailopnamegudanglogistik;
use App\Models\Opnamegudanglogistik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class OpnamegudanglogistikController extends Controller
{
    public function index(Request $request)
    {
        $query = Opnamegudanglogistik::query();
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }

        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
            lockyear($request->tahun);
        } else {
            $query->where('tahun', '>=', startyear());
        }

        if (!empty($request->kode_kategori)) {
            $query->where('opname_gl.kode_kategori', $request->kode_kategori);
        }
        $query->select('opname_gl.*', 'kategori');
        $query->join('kategori_barang_pembelian', 'opname_gl.kode_kategori', '=', 'kategori_barang_pembelian.kode_kategori');
        $query->orderBy('tahun', 'desc');
        $query->orderBy('bulan', 'asc');
        $opname = $query->paginate(15);
        $opname->appends($request->all());
        $kategori = DB::table('kategori_barang_pembelian')->where('kode_dept', 'GDL')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('opnamegudanglogistik.index', compact('bulan', 'opname', 'kategori'));
    }

    public function edit($kode_opname)
    {
        $kode_opname = Crypt::decrypt($kode_opname);
        $opname = DB::table('opname_gl')->where('kode_opname_gl', $kode_opname)->first();
        $detail = DB::table('opname_gl_detail')
            ->join('master_barang_pembelian', 'opname_gl_detail.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('kode_opname_gl', $kode_opname)
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('opnamegudanglogistik.edit', compact('detail', 'opname', 'bulan'));
    }

    public function editbarang($kode_opname, $kode_barang)
    {
        $detail = DB::table('opname_gl_detail')
            ->join('master_barang_pembelian', 'opname_gl_detail.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('kode_opname_gl', $kode_opname)
            ->where('opname_gl_detail.kode_barang', $kode_barang)
            ->first();
        return view('opnamegudanglogistik.editbarang', compact('detail'));
    }

    public function updatebarang($kode_opname, $kode_barang, Request $request)
    {
        $kode_opname = Crypt::decrypt($kode_opname);
        $kode_barang = Crypt::decrypt($kode_barang);
        $qty = !empty($request->qty) ? str_replace(".", "", $request->qty) : 0;
        $qty = str_replace(",", ".", $qty);
        $data = [
            'qty' => $qty,
        ];
        $update = DB::table('opname_gl_detail')->where('kode_opname_gl', $kode_opname)->where('kode_barang', $kode_barang)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate, Hubungi Tim IT']);
        }
    }

    public function delete($kode_opname)
    {
        $kode_opname = Crypt::decrypt($kode_opname);
        $hapus = DB::table('opname_gl')->where('kode_opname_gl', $kode_opname)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $kategori = DB::table('kategori_barang_pembelian')->where('kode_dept', 'GDL')->get();
        return view('opnamegudanglogistik.create', compact('bulan', 'kategori'));
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

        $ceksaldo = DB::table('opname_gl')->where('bulan', $bulanlalu)->where('tahun', $tahunlalu)->where('kode_kategori', $kategori)->count();
        $ceknow = DB::table('opname_gl')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_kategori', $kategori)->count();
        $cekall = DB::table('opname_gl')->count();
        if (empty($ceksaldo) && !empty($cekall) || !empty($ceknow)) {
            echo "1";
        } else {
            $detail = DB::table('master_barang_pembelian')
                ->selectRaw(" master_barang_pembelian.kode_barang,
                master_barang_pembelian.kode_barang,
                master_barang_pembelian.nama_barang,
                kategori_barang_pembelian.kode_kategori,
                kategori_barang_pembelian.kategori,
                master_barang_pembelian.satuan,
                sa.qtysaldoawal,
                sa.totalsa,
                sa.hargasaldoawal,
                gm.totalpemasukan,
                gm.qtypemasukan,
                gm.hargapemasukan,
                op.qtyopname,
                gk.qtypengeluaran
            ")
                ->join('kategori_barang_pembelian', 'master_barang_pembelian.kode_kategori', '=', 'kategori_barang_pembelian.kode_kategori')
                ->leftJoin(
                    DB::raw("(
                        SELECT saldoawal_gl_detail.kode_barang,SUM(saldoawal_gl_detail.harga) AS hargasaldoawal,SUM( qty ) AS qtysaldoawal,SUM(saldoawal_gl_detail.harga*qty) AS
                        totalsa FROM saldoawal_gl_detail
                        INNER JOIN saldoawal_gl ON saldoawal_gl.kode_saldoawal_gl=saldoawal_gl_detail.kode_saldoawal_gl
                        WHERE bulan = '$bulan' AND tahun = '$tahun'
                        GROUP BY saldoawal_gl_detail.kode_barang
                    ) sa"),
                    function ($join) {
                        $join->on('master_barang_pembelian.kode_barang', '=', 'sa.kode_barang');
                    }
                )

                ->leftJoin(
                    DB::raw("(
                        SELECT opname_gl_detail.kode_barang,SUM( qty ) AS qtyopname FROM opname_gl_detail
                        INNER JOIN opname_gl ON opname_gl.kode_opname_gl=opname_gl_detail.kode_opname_gl
                        WHERE bulan = '$bulan' AND tahun = '$tahun'
                        GROUP BY opname_gl_detail.kode_barang
                    ) op"),
                    function ($join) {
                        $join->on('master_barang_pembelian.kode_barang', '=', 'op.kode_barang');
                    }
                )
                ->leftJoin(
                    DB::raw("(
                        SELECT detail_pemasukan.kode_barang,SUM( qty ) AS qtypemasukan,SUM( harga ) AS hargapemasukan,SUM(detail_pemasukan.harga * qty) AS totalpemasukan FROM
                        detail_pemasukan
                        INNER JOIN pemasukan ON detail_pemasukan.nobukti_pemasukan = pemasukan.nobukti_pemasukan
                        WHERE MONTH(tgl_pemasukan) = '$bulan' AND YEAR(tgl_pemasukan) = '$tahun'
                        GROUP BY detail_pemasukan.kode_barang
                    ) gm"),
                    function ($join) {
                        $join->on('master_barang_pembelian.kode_barang', '=', 'gm.kode_barang');
                    }
                )

                ->leftJoin(
                    DB::raw("(
                        SELECT detail_pengeluaran.kode_barang,SUM( qty ) AS qtypengeluaran FROM detail_pengeluaran
                        INNER JOIN pengeluaran ON detail_pengeluaran.nobukti_pengeluaran = pengeluaran.nobukti_pengeluaran
                        WHERE MONTH(tgl_pengeluaran) = '$bulan' AND YEAR(tgl_pengeluaran) = '$tahun'
                        GROUP BY detail_pengeluaran.kode_barang
                    ) gk"),
                    function ($join) {
                        $join->on('master_barang_pembelian.kode_barang', '=', 'gk.kode_barang');
                    }
                )

                ->where('master_barang_pembelian.kode_dept', 'GDL')
                ->where('master_barang_pembelian.status', 'Aktif')
                ->where('master_barang_pembelian.kode_kategori', $kategori)
                ->groupByRaw("master_barang_pembelian.kode_barang,
                master_barang_pembelian.nama_barang,
                kategori_barang_pembelian.kode_kategori,
                kategori_barang_pembelian.kategori,
                master_barang_pembelian.satuan,
                sa.qtysaldoawal,
                sa.totalsa,
                sa.hargasaldoawal,
                gm.totalpemasukan,
                gm.qtypemasukan,
                gm.hargapemasukan,
                op.qtyopname,
                gk.qtypengeluaran")
                ->orderBy('master_barang_pembelian.nama_barang')
                ->get();

            return view('opnamegudanglogistik.getdetailsaldo', compact('detail'));
        }
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $thn = substr($tahun, 2, 2);
        $kategori = $request->kategori;
        $kat = substr($kategori, 2, 3);
        $kode_opname = "OP" . $bulan . $thn . $kat;
        $tanggal = $request->tanggal;


        $kode_barang = $request->kode_barang;
        $qty = $request->qty;
        $harga = $request->harga;
        $data = [
            'kode_opname_gl' => $kode_opname,
            'tanggal'           => $tanggal,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'kode_kategori'     => $kategori
        ];

        for ($i = 0; $i < count($kode_barang); $i++) {
            $detail_saldo[]   = [
                'kode_opname_gl' => $kode_opname,
                'kode_barang'       => $kode_barang[$i],
                'qty'               => $qty[$i],
            ];
        }


        //dd($detail_saldo);

        //dd($chunks);
        DB::beginTransaction();
        try {
            DB::table('opname_gl')->insert($data);
            $chunks = array_chunk($detail_saldo, 50);
            foreach ($chunks as $chunk) {
                Detailopnamegudanglogistik::insert($chunk);
            }
            DB::commit();
            return redirect('/opnamegudanglogistik?tahun=' . $tahun)->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/opnamegudanglogistik?tahun=' . $tahun)->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }
}
