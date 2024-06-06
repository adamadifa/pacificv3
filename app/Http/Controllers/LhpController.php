<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Lhp;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class LhpController extends Controller
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
        $kode_cabang = Auth::user()->kode_cabang;
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($kode_cabang);

        $query = Lhp::query();
        $query->select('kode_lhp', 'tanggal', 'lhp.id_karyawan', 'nama_karyawan', 'kode_cabang', 'rute');
        $query->join('karyawan', 'lhp.id_karyawan', '=', 'karyawan.id_karyawan');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }

        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        } else {
            if ($kode_cabang != "PCF") {
                $query->where('kode_cabang', $kode_cabang);
            }
        }

        if (!empty($request->id_karyawan)) {
            $query->where('lhp.id_karyawan', $request->id_karyawan);
        }
        $lhp = $query->paginate(15);
        $lhp->appends($request->all());
        return view('lhp.index', compact('cabang', 'lhp'));
    }


    public function create(Request $request)
    {
        $kode_cabang = Auth::user()->kode_cabang;
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($kode_cabang);


        $tanggal = !empty($request->tanggal) ? $request->tanggal : date('Y-m-d');
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;

        $query = Penjualan::query();
        $query->selectRaw("penjualan.no_fak_penj,nama_pelanggan,
        kode_lhp,pasar,
        AB,AR,ASE,BB,DEP,SC,SP8P,SP8,SP,SP500,BR20,P1000,
        SUM(totaltunai) as totaltunai,
        SUM(IF(penjualan.jenistransaksi='kredit',total,0)) as totalkredit,
        totalbayar,totalgiro,totaltransfer,totalvoucher");
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
                SUM( IF ( kode_produk = 'SP500', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SP500,
                SUM( IF ( kode_produk = 'BR20', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS BR20,
                SUM( IF ( kode_produk = 'P1000', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS P1000
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
                SUM(IF(jenisbayar='tunai' AND status_bayar IS NULL,bayar,0)) as totaltunai,
                SUM(IF(jenisbayar='titipan',bayar,0)) as totalbayar,
                SUM(IF(status_bayar ='voucher',bayar,0)) AS totalvoucher
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
                transfer.no_fak_penj,
                SUM(jumlah) AS totaltransfer
            FROM
            transfer
            INNER JOIN penjualan ON transfer.no_fak_penj = penjualan.no_fak_penj
            WHERE tgl_transfer = '$tanggal'
            GROUP BY
                no_fak_penj
            ) transfer"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'transfer.no_fak_penj');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj,
                sum(retur.total) AS totalretur
                FROM
                retur
                WHERE tglretur = '$tanggal'
                GROUP BY
                retur.no_fak_penj
            ) returbulanini"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'returbulanini.no_fak_penj');
            }
        );
        $query->where('tgltransaksi', $tanggal);
        $query->where('karyawan.kode_cabang', $kode_cabang);
        $query->where('penjualan.id_karyawan', $id_karyawan);
        $query->orderBy('penjualan.no_fak_penj');
        $query->groupByRaw('penjualan.no_fak_penj,kode_lhp,nama_pelanggan,AB,AR
        ,ASE,BB,DEP,SC,SP8P,SP8,SP,SP500,BR20,P1000,totalbayar,totalgiro,totaltransfer,totalvoucher,pasar');
        $penjualan = $query->get();

        $no_fak_penj = [];
        foreach ($penjualan as $d) {
            $no_fak_penj[] = $d->no_fak_penj;
        }
        $historibayar = DB::table('historibayar')
            ->selectRaw('historibayar.no_fak_penj,nama_pelanggan,historibayar.kode_lhp,
            SUM(IF(status_bayar IS NULL,bayar,0)) AS totalbayar,pasar,
            SUM(IF(status_bayar ="voucher",bayar,0)) AS totalvoucher,
            IFNULL(totalgiro,0) as totalgiro,IFNULL(totaltransfer,0) as totaltransfer')
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
            ->whereNull('id_transfer')
            ->whereNull('id_giro')
            ->where('historibayar.id_karyawan', $id_karyawan)
            ->whereNotIn('historibayar.no_fak_penj', $no_fak_penj)
            ->orderBy('historibayar.no_fak_penj')
            ->groupByRaw('historibayar.no_fak_penj,nama_pelanggan,totalgiro,totaltransfer,historibayar.kode_lhp,pasar')
            ->get();

        $no_fak_penj_hb = [];
        foreach ($historibayar as $d) {
            $no_fak_penj_hb[] = $d->no_fak_penj;
        }

        $giro = DB::table('giro')
            ->selectRaw('giro.no_fak_penj,nama_pelanggan,SUM(jumlah) as totalgiro,giro.kode_lhp,pasar')
            ->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->where('tgl_giro', $tanggal)
            ->where('giro.id_karyawan', $id_karyawan)
            ->whereNotIn('giro.no_fak_penj', $no_fak_penj)
            ->whereNotIn('giro.no_fak_penj', $no_fak_penj_hb)
            ->orderBy('giro.no_fak_penj')
            ->groupByRaw('giro.no_fak_penj,nama_pelanggan,giro.kode_lhp,pasar')
            ->get();

        $transfer = DB::table('transfer')
            ->selectRaw('transfer.no_fak_penj,nama_pelanggan,SUM(jumlah) as totaltransfer,transfer.kode_lhp,pasar')
            ->join('penjualan', 'transfer.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->where('tgl_transfer', $tanggal)
            ->where('transfer.id_karyawan', $id_karyawan)
            ->whereNotIn('transfer.no_fak_penj', $no_fak_penj)
            ->whereNotIn('transfer.no_fak_penj', $no_fak_penj_hb)
            ->orderBy('transfer.no_fak_penj')
            ->groupByRaw('transfer.no_fak_penj,nama_pelanggan,transfer.kode_lhp,pasar')
            ->get();
        return view('lhp.create', compact('cabang', 'penjualan', 'historibayar', 'giro', 'transfer'));
    }

    public function store(Request $request)
    {
        $tanggal = $request->tanggal;
        $kode_cabang = $request->kode_cabang;
        $id_karyawan = $request->id_karyawan;
        $tgl = explode("-", $tanggal);
        $tahun = substr($tgl[0], 2, 2);
        $rute = $request->rute;
        $lhp = DB::table('lhp')->select('kode_lhp')->whereRaw('LEFT(kode_lhp,8) ="LHP' . $kode_cabang . $tahun . '"')->orderBy('kode_lhp', 'desc')->first();
        if ($lhp != null) {
            $lastkode_lhp = $lhp->kode_lhp;
        } else {
            $lastkode_lhp = "";
        }
        $kode_lhp = buatkode($lastkode_lhp, 'LHP' . $kode_cabang . $tahun, 4);

        //dd($_POST['no_fak_penj']);
        DB::beginTransaction();
        try {
            $data = array(
                'kode_lhp' => $kode_lhp,
                'tanggal' => $tanggal,
                'id_karyawan' => $id_karyawan,
                'rute' => $rute
            );
            DB::table('lhp')->insert($data);
            foreach ($_POST['no_fak_penj'] as $no_fak_penj) {
                $data = ['kode_lhp' => $kode_lhp];
                //echo $id;
                DB::table('penjualan')->where('no_fak_penj', $no_fak_penj)
                    ->where('tgltransaksi', $tanggal)
                    ->where('penjualan.id_karyawan', $id_karyawan)
                    ->update($data);

                DB::table('historibayar')
                    ->where('no_fak_penj', $no_fak_penj)
                    ->where('tglbayar', $tanggal)
                    ->whereNull('id_transfer')
                    ->whereNull('id_giro')
                    ->where('historibayar.id_karyawan', $id_karyawan)
                    ->update($data);

                DB::table('giro')
                    ->where('no_fak_penj', $no_fak_penj)
                    ->where('tgl_giro', $tanggal)
                    ->where('giro.id_karyawan', $id_karyawan)
                    ->update($data);

                DB::table('transfer')
                    ->where('no_fak_penj', $no_fak_penj)
                    ->where('tgl_transfer', $tanggal)
                    ->where('transfer.id_karyawan', $id_karyawan)
                    ->update($data);
            }

            DB::commit();
            return redirect('/lhp')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/lhp')->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);;
        }
    }


    public function delete($kode_lhp)
    {
        $kode_lhp = Crypt::decrypt($kode_lhp);
        DB::beginTransaction();
        try {
            DB::table('lhp')->where('kode_lhp', $kode_lhp)->delete();
            DB::table('penjualan')->where('kode_lhp', $kode_lhp)->update([
                'kode_lhp' => null
            ]);

            DB::table('historibayar')->where('kode_lhp', $kode_lhp)->update([
                'kode_lhp' => null
            ]);

            DB::table('transfer')->where('kode_lhp', $kode_lhp)->update([
                'kode_lhp' => null
            ]);

            DB::table('giro')->where('kode_lhp', $kode_lhp)->update([
                'kode_lhp' => null
            ]);
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);;
        }
    }


    public function cetak($kode_lhp)
    {

        $kode_lhp = Crypt::decrypt($kode_lhp);
        $lhp = DB::table('lhp')
            ->select('lhp.kode_lhp', 'tanggal', 'lhp.id_karyawan', 'kode_cabang')
            ->join('karyawan', 'lhp.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('kode_lhp', $kode_lhp)
            ->first();
        $kode_cabang = $lhp->kode_cabang;
        $id_karyawan = $lhp->id_karyawan;
        $tanggal = $lhp->tanggal;
        $query = Penjualan::query();
        $query->selectRaw("penjualan.no_fak_penj,nama_pelanggan,
        AB,AR,ASE,BB,DEP,SC,SP8P,SP8,SP,SP500,BR20,
        SUM(totaltunai) as totaltunai,
        SUM(IF(penjualan.jenistransaksi='kredit',total,0)) as totalkredit,
        totalbayar,totalgiro,totaltransfer,totalvoucher");
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
                SUM( IF ( kode_produk = 'SP500', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS SP500,
                SUM( IF ( kode_produk = 'BR20', detailpenjualan.jumlah/isipcsdus, NULL ) ) AS BR20
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
                SUM(IF(jenisbayar='tunai' AND status_bayar IS NULL,bayar,0)) as totaltunai,
                SUM(IF(jenisbayar='titipan',bayar,0)) as totalbayar,
                SUM(IF(status_bayar ='voucher',bayar,0)) AS totalvoucher
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
                transfer.no_fak_penj,
                SUM(jumlah) AS totaltransfer
            FROM
            transfer
            INNER JOIN penjualan ON transfer.no_fak_penj = penjualan.no_fak_penj
            WHERE tgl_transfer = '$tanggal'
            GROUP BY
                no_fak_penj
            ) transfer"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'transfer.no_fak_penj');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj,
                sum(retur.total) AS totalretur
                FROM
                retur
                WHERE tglretur = '$tanggal'
                GROUP BY
                retur.no_fak_penj
            ) returbulanini"),
            function ($join) {
                $join->on('penjualan.no_fak_penj', '=', 'returbulanini.no_fak_penj');
            }
        );
        $query->where('tgltransaksi', $tanggal);
        $query->where('karyawan.kode_cabang', $kode_cabang);
        $query->where('penjualan.id_karyawan', $id_karyawan);
        $query->where('penjualan.kode_lhp', $kode_lhp);
        $query->orderBy('penjualan.no_fak_penj');
        $query->groupByRaw('penjualan.no_fak_penj,nama_pelanggan,AB,AR,ASE,BB,DEP,SC,SP8P,SP8,SP,SP500,BR20,totalbayar,totalgiro,totaltransfer,totalvoucher');
        $penjualan = $query->get();

        $no_fak_penj = [];
        foreach ($penjualan as $d) {
            $no_fak_penj[] = $d->no_fak_penj;
        }



        $historibayar = DB::table('historibayar')
            ->selectRaw('historibayar.no_fak_penj,nama_pelanggan,
            SUM(IF(status_bayar IS NULL,bayar,0)) AS totalbayar,
            SUM(IF(status_bayar ="voucher",bayar,0)) AS totalvoucher,
            IFNULL(totalgiro,0) as totalgiro,IFNULL(totaltransfer,0) as totaltransfer')
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
            ->whereNull('id_transfer')
            ->whereNull('id_giro')
            ->where('historibayar.id_karyawan', $id_karyawan)
            ->whereNotIn('historibayar.no_fak_penj', $no_fak_penj)
            ->where('historibayar.kode_lhp', $kode_lhp)
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
            ->where('giro.kode_lhp', $kode_lhp)
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
            ->where('transfer.kode_lhp', $kode_lhp)
            ->orderBy('transfer.no_fak_penj')
            ->groupByRaw('transfer.no_fak_penj,nama_pelanggan')
            ->get();


        $allgiro = DB::table('giro')
            ->selectRaw('SUM(jumlah) as totalgiro')
            ->where('tgl_giro', $tanggal)
            ->where('giro.id_karyawan', $id_karyawan)
            ->where('giro.kode_lhp', $kode_lhp)
            ->first();


        $alltransfer = DB::table('transfer')
            ->selectRaw('SUM(jumlah) as totaltransfer')
            ->where('tgl_transfer', $tanggal)
            ->where('transfer.kode_lhp', $kode_lhp)
            ->where('transfer.id_karyawan', $id_karyawan)
            ->first();


        $rekapdp = DB::table('detailpenjualan')
            ->selectRaw('barang.kode_produk,nama_barang,SUM(jumlah) as jumlah,isipcsdus,isipack,isipcs')
            ->join('penjualan', 'detailpenjualan.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('tgltransaksi', $tanggal)
            ->where('karyawan.kode_cabang', $kode_cabang)
            ->where('penjualan.id_karyawan', $id_karyawan)
            ->where('penjualan.kode_lhp', $kode_lhp)
            ->orderBy('barang.kode_produk')
            ->groupByRaw('barang.kode_produk,nama_barang,isipcsdus,isipack,isipcs')
            ->get();

        $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
        return view('penjualan.laporan.cetak_lhp', compact('tanggal', 'penjualan', 'historibayar', 'giro', 'transfer', 'karyawan', 'allgiro', 'alltransfer', 'rekapdp'));
    }


    public function kirimlhp()
    {
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('lhp.kirimlhp', compact('bln'));
    }


    public function showkirimlhp(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $lhp = DB::table('kirim_lhp')->where('bulan', $bulan)->where('tahun', $tahun)->get();
        return view('lhp.showkirimlhp', compact('lhp', 'bln'));
    }

    public function createkirimlhp()
    {
        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->get();
        }
        return view('lhp.createkirimlhp', compact('cabang', 'bln'));
    }


    public function storekirimlhp(Request $request)
    {

        $cek = DB::table('kirim_lhp')
            ->where('kode_cabang', $request->kode_cabang)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->count();
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Sudah Ada']);
        } else {
            try {
                if ($request->hasfile('foto')) {
                    $image = $request->file('foto');
                    $image_name =  $request->kode_cabang . $request->bulan . $request->tahun . "." . $request->file('foto')->getClientOriginalExtension();
                    $foto = $image_name;
                } else {
                    $foto = NULL;
                }
                DB::table('kirim_lhp')
                    ->insert([
                        'kode_lhp' => $request->kode_cabang . $request->bulan . $request->tahun,
                        'kode_cabang' => $request->kode_cabang,
                        'bulan' => $request->bulan,
                        'tahun' => $request->tahun,
                        'tgl_lhp' => $request->tgl_lhp,
                        'jam_lhp' => $request->jam_lhp,
                        'foto' => $foto
                    ]);

                $destination_path = "/public/lhp";
                $request->file('foto')->storeAs($destination_path, $image_name);
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                return Redirect::back()->with(['warning' => 'Data Gagak Disimpan']);
            }
        }
    }


    public function approvekirimlhp(Request $request)
    {
        $update = DB::table('kirim_lhp')
            ->where('kode_lhp', $request->kode_lhp)
            ->update([
                'status' => 1
            ]);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }


    public function cancelkirimlhp(Request $request)
    {
        $update = DB::table('kirim_lhp')
            ->where('kode_lhp', $request->kode_lhp)
            ->update([
                'status' => 0
            ]);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function deletekirimlhp(Request $request)
    {
        $hapus = DB::table('kirim_lhp')->where('kode_lhp', $request->kode_lhp)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 2;
        }
    }
}
