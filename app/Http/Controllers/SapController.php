<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class SapController extends Controller
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
    public function salesperfomance()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        return view('sap.salesperfomance', compact('cabang'));
    }

    public function getsalesperfomance(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $tanggal = explode("-", $request->tanggal);
        $tgl1 = explode("/", $tanggal[0]);
        $tgl2 = explode("/", $tanggal[1]);
        $dari = str_replace(' ', '', implode("-", array($tgl1[2], $tgl1[1], $tgl1[0])));
        $sampai = str_replace(' ', '', implode("-", array($tgl2[2], $tgl2[1], $tgl2[0])));


        // $checkin = DB::table('checkin')
        //     ->join('users', 'checkin.id_karyawan', '=', 'users.id')
        //     ->leftJoin('karyawan', 'users.id_salesman', '=', 'karyawan.id_karyawan')
        //     ->where('tgl_checkin', $tanggal)
        //     ->where('karyawan.kode_cabang', $kode_cabang)
        //     ->get();

        $karyawan = DB::table('karyawan')->where('kode_cabang', $kode_cabang)
            ->selectRaw('karyawan.id_karyawan,nama_karyawan,totalpenjualan,totalorder,totalkunjungan,totalcust')
            ->leftJoin(
                DB::raw("(
                SELECT id_karyawan, SUM(total) as totalpenjualan,COUNT(no_fak_penj) as totalorder
                FROM penjualan
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                GROUP BY penjualan.id_karyawan
                ) pj"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'pj.id_karyawan');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT id_salesman, COUNT(kode_checkin) as totalkunjungan, COUNT(DISTINCT(kode_pelanggan)) as totalcust
                FROM checkin
                INNER JOIN users ON checkin.id_karyawan = users.id
                WHERE tgl_checkin BETWEEN '$dari' AND '$sampai'
                GROUP BY id_salesman
                ) check_in"),
                function ($join) {
                    $join->on('karyawan.id_karyawan', '=', 'check_in.id_salesman');
                }
            )
            ->where('status_aktif_sales', 1)
            ->where('nama_karyawan', '!=', '-')
            ->get();
        return view('sap.getsalesperfomance', compact('karyawan', 'dari', 'sampai'));
    }

    public function salesperfomancedetail(Request $request)
    {

        $salesman = DB::table('karyawan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('id_karyawan', $request->id_karyawan)->first();
        return view('sap.salesperfomance_detail', compact('salesman'));
    }

    public function getpenjualansalesman(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $dari = $request->dari;
        $sampai = $request->sampai;

        $penjualan = DB::table('penjualan')
            ->selectRaw('SUM(total) as totalpenjualan,
            SUM(potongan) as totalpotongan,
            SUM(potistimewa) as totalpotis,
            SUM(penyharga) as totalpeny,
            SUM(ppn) as totalppn,
            SUM(IF(jenistransaksi="tunai",subtotal,0)) as totaltunai,
            SUM(IF(jenistransaksi="kredit",subtotal,0)) as totalkredit,
            SUM(IF(jenistransaksi="kredit",subtotal,0)) as totalkredit,
            SUM(IF(penjualan.jenistransaksi="tunai",1,0)) as ordertunai,
            SUM(IF(penjualan.jenistransaksi="kredit",1,0)) as orderkredit')
            ->where('penjualan.id_karyawan', $id_karyawan)
            ->whereBetween('tgltransaksi', [$dari, $sampai])
            ->first();

        $detailpenjualan = DB::table('master_barang')
            ->selectRaw('master_barang.kode_produk,nama_barang,qty,subtotal,isipcsdus,nama_barang')
            ->leftJoin(
                DB::raw("(
                    SELECT kode_produk,
                    SUM(jumlah) as qty, SUM(detailpenjualan.subtotal) as subtotal
                    FROM detailpenjualan
                    INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                    INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                    WHERE tgltransaksi BETWEEN '$dari' AND '$sampai' AND penjualan.id_karyawan = '$id_karyawan'
                    GROUP BY kode_produk
                    ) dp"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'dp.kode_produk');
                }
            )
            ->where('status', 1)
            ->orderBy('master_barang.kode_produk')
            ->get();
        return view('sap.getpenjualansalesman', compact('penjualan', 'detailpenjualan'));
    }

    public function getcashinsalesman(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $id_karyawan = $request->id_karyawan;
        $historibayar = DB::table('historibayar')
            ->selectRaw('SUM(bayar) as totalbayar,SUM(IF(jenisbayar="tunai",bayar,0)) as totalbayartunai,
            SUM(IF(jenisbayar="titipan",bayar,0)) as totalbayartitipan,
            SUM(IF(status_bayar="voucher",bayar,0)) as totalvoucher')
            ->whereBetween('tglbayar', [$dari, $sampai])
            ->where('id_karyawan', $id_karyawan)
            ->whereNull('historibayar.id_giro')
            ->whereNull('historibayar.id_transfer')
            ->whereNull('historibayar.girotocash')
            ->orwhereBetween('tglbayar', [$dari, $sampai])
            ->where('id_karyawan', $id_karyawan)
            ->whereNull('historibayar.id_giro')
            ->whereNull('historibayar.id_transfer')
            ->where('historibayar.girotocash', 1)
            ->orwhereBetween('tglbayar', [$dari, $sampai])
            ->where('id_karyawan', $id_karyawan)
            ->whereNotNull('historibayar.id_giro')
            ->whereNull('historibayar.id_transfer')
            ->where('historibayar.girotocash', 1)
            ->first();

        $giro = DB::table('giro')
            ->selectRaw('SUM(jumlah) as totalgiro')
            ->whereBetween('tgl_giro', [$dari, $sampai])
            ->where('giro.id_karyawan', $id_karyawan)
            ->first();
        $transfer = DB::table('transfer')
            ->selectRaw('SUM(jumlah) as totaltransfer')
            ->whereBetween('tgl_transfer', [$dari, $sampai])
            ->where('transfer.id_karyawan', $id_karyawan)
            ->first();
        return view('sap.getcashinsalesman', compact('historibayar', 'giro', 'transfer'));
    }

    public function getkunjungansalesman(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $id_karyawan = $request->id_karyawan;

        $kunjungan = DB::table('checkin')
            ->selectRaw('checkin.kode_pelanggan,nama_pelanggan,alamat_pelanggan,checkin_time,no_fak_penj,date_created as checkout_time,pelanggan.foto')
            ->join('pelanggan', 'checkin.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('users', 'checkin.id_karyawan', '=', 'users.id')
            ->leftJoin(
                DB::raw("(
            SELECT kode_pelanggan,no_fak_penj,date_created
            FROM penjualan WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
            ) pj"),
                function ($join) {
                    $join->on('checkin.kode_pelanggan', '=', 'pj.kode_pelanggan');
                }
            )
            ->where('id_salesman', $id_karyawan)
            ->whereBetween('tgl_checkin', [$dari, $sampai])
            ->orderBy('checkin_time', 'asc')
            ->get();

        return view('sap.getkunjungansalesman', compact('kunjungan'));
    }

    public function gettargetsalesman(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('sap.gettargetsalesman', compact('bulan', 'id_karyawan'));
    }

    public function getrealisasitargetsales(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $id_karyawan = $request->id_karyawan;

        $realisasitarget = DB::table('komisi_target_qty_detail')
            ->selectRaw('komisi_target_qty_detail.*,nama_barang,realisasi,isipcsdus')
            ->join('komisi_target', 'komisi_target_qty_detail.kode_target', '=', 'komisi_target.kode_target')
            ->join('master_barang', 'komisi_target_qty_detail.kode_produk', '=', 'master_barang.kode_produk')
            ->leftJoin(
                DB::raw("(
                SELECT kode_produk,
                SUM(jumlah) as realisasi
                FROM detailpenjualan
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                LEFT JOIN (
                SELECT pj.no_fak_penj,
                IF(salesbaru IS NULL,pj.id_karyawan,salesbaru) as salesbarunew, karyawan.nama_karyawan as nama_sales,
                IF(cabangbaru IS NULL,karyawan.kode_cabang,cabangbaru) as cabangbarunew
                FROM penjualan pj
                INNER JOIN karyawan ON pj.id_karyawan = karyawan.id_karyawan
                    LEFT JOIN (
                        SELECT MAX(id_move) as id_move,no_fak_penj,move_faktur.id_karyawan as salesbaru,karyawan.kode_cabang as cabangbaru
                        FROM move_faktur
                        INNER JOIN karyawan ON move_faktur.id_karyawan = karyawan.id_karyawan
                        WHERE tgl_move <= '$dari'
                        GROUP BY no_fak_penj,move_faktur.id_karyawan,karyawan.kode_cabang
                    ) move_fak ON (pj.no_fak_penj = move_fak.no_fak_penj)
                ) pjmove ON (penjualan.no_fak_penj = pjmove.no_fak_penj)


                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai' AND promo != 1 AND salesbarunew = '$id_karyawan'
                OR tgltransaksi BETWEEN '$dari' AND '$sampai' AND promo IS NULL AND salesbarunew = '$id_karyawan'
                GROUP BY kode_produk
            ) realisasi"),
                function ($join) {
                    $join->on('komisi_target_qty_detail.kode_produk', '=', 'realisasi.kode_produk');
                }
            )
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('komisi_target_qty_detail.id_karyawan', $id_karyawan)
            ->where('jumlah_target', '!=', 0)
            ->get();

        return view('sap.getrealisasitargetsales', compact('realisasitarget'));
    }

    public function showlimitkredit($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $limitkredit = DB::table('pengajuan_limitkredit_v3')
            ->select(
                'pengajuan_limitkredit_v3.*',
                'nama_pelanggan',
                'alamat_pelanggan',
                'alamat_toko',
                'latitude',
                'longitude',
                'pelanggan.no_hp',
                'status_outlet',
                'cara_pembayaran',
                'histori_transaksi',
                'lama_topup',
                'lama_usaha',
                'kepemilikan',
                'omset_toko',
                'lama_langganan',
                'type_outlet',
                'nama_karyawan',
                'karyawan.kode_cabang'
            )
            ->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->where('no_pengajuan', $no_pengajuan)
            ->orderBy('tgl_pengajuan', 'asc')
            ->first();

        $komentar = DB::table('pengajuan_limitkredit_analisa_v3')->where('no_pengajuan', $no_pengajuan)->get();
        return view('sap.showlimitkredit', compact('limitkredit', 'komentar'));
    }

    public function pelanggan(Request $request)
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        $query = Pelanggan::query();
        if ($this->cabang != "PCF") {
            $query->where('pelanggan.kode_cabang', $this->cabang);
        }
        if (Auth::user()->level == "salesman") {
            $query->where('pelanggan.id_sales', Auth::user()->id_salesman);
        } else {
            if (Auth::user()->id == 82) {
                $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB', 'BTN');
                $query->whereIn('pelanggan.kode_cabang', $wilayah_barat);
            } else if (Auth::user()->id == 97) {
                $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
                $query->whereIn('pelanggan.kode_cabang', $wilayah_timur);
            }
        }



        if ($request->nama != "") {
            $query->where('nama_pelanggan', 'like', '%' . $request->nama . '%');
        }


        if ($request->kode_cabang != "") {
            $query->where('pelanggan.kode_cabang', $request->kode_cabang);
        }

        if ($request->id_karyawan != "") {
            $query->where('pelanggan.id_sales', $request->id_karyawan);
        }

        if ($request->status_pelanggan != "") {
            $query->where('pelanggan.status_pelanggan', $request->status_pelanggan);
        }

        if ($request->dari != "" && $request->sampai != "") {
            $query->whereBetween('pelanggan.time_stamps', [$request->dari, $request->sampai]);
        }

        if (!empty($request->kode_pelanggan)) {
            $query->where('pelanggan.kode_pelanggan', $request->kode_pelanggan);
        }
        $query->select('pelanggan.*', 'nama_karyawan');
        $query->orderBy('status_pelanggan', 'desc');
        $query->orderBy('nama_pelanggan', 'asc');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $pelanggan = $query->paginate(15);
        $pelanggan->appends($request->all());
        return view('sap.pelanggan', compact('cabang', 'pelanggan'));
    }
}
