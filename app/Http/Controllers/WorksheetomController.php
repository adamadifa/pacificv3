<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Detailbuffer;
use App\Models\Detaillimitstok;
use App\Models\Detailretur;
use App\Models\Evaluasisharing;
use App\Models\Harga;
use App\Models\Kebutuhancabang;
use App\Models\Program;
use App\Models\Programpeserta;
use App\Models\Retur;
use App\Models\Visitpelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class WorksheetomController extends Controller
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
    public function monitoringretur(Request $request)
    {

        if (isset($request->cetak)) {
            return $this->cetakmonitoringretur($request);
        }

        $pelanggan = '"' . $request->nama_pelanggan . '"';
        $query = Retur::query();
        $query->select('retur.*', 'nama_pelanggan', 'nama_karyawan', 'karyawan.kode_cabang', DB::raw('IFNULL(jmlretur,0) - IFNULL(jmlpelunasan,0) as sisa'));
        $query->orderBy('tglretur', 'desc');
        $query->orderBy('no_retur_penj', 'asc');
        $query->join('penjualan', 'retur.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->leftJoin(
            DB::raw("(
            SELECT
                no_retur_penj,
                SUM(jumlah) as jmlretur
            FROM
                detailretur
            GROUP BY no_retur_penj
        ) detailretur"),
            function ($join) {
                $join->on('retur.no_retur_penj', '=', 'detailretur.no_retur_penj');
            }
        );

        $query->leftJoin(
            DB::raw("(
            SELECT
                no_retur_penj,
                SUM(jumlah) as jmlpelunasan
            FROM
                detailretur_pelunasan
            GROUP BY no_retur_penj
        ) pelunasan"),
            function ($join) {
                $join->on('retur.no_retur_penj', '=', 'pelunasan.no_retur_penj');
            }
        );
        if (empty($request->no_fak_penj) && empty($request->nama_pelanggan) && empty($request->dari) && empty($request->sampai)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }
        if (!empty($request->nama_pelanggan)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }

        if (!empty($request->no_fak_penj)) {
            $query->where('retur.no_fak_penj', $request->no_fak_penj);
        }

        if (!empty($request->jenis_retur)) {
            $query->where('jenis_retur', $request->jenis_retur);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tglretur', [$request->dari, $request->sampai]);
        }
        // if ($this->cabang != "PCF") {
        //     if ($this->cabang == "GRT") {
        //         $query->where('karyawan.kode_cabang', 'TSM');
        //     } else {
        //         $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
        //         $cabang[] = "";
        //         foreach ($cbg as $c) {
        //             $cabang[] = $c->kode_cabang;
        //         }
        //         $query->whereIn('karyawan.kode_cabang', $cabang);
        //     }
        // }
        $query->where('jenis_retur', 'gb');
        if ($this->cabang != "PCF") {
            $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
            $cabang[] = "";
            foreach ($cbg as $c) {
                $cabang[] = $c->kode_cabang;
            }
            $query->whereIn('karyawan.kode_cabang', $cabang);
        } else {
            if (!empty($request->kode_cabang)) {
                $query->where('karyawan.kode_cabang', $request->kode_cabang);
            }
        }


        $retur = $query->paginate(15);

        $retur->appends($request->all());

        $cbg = new Cabang();
        $cabang = $cbg->getCabanggudang($this->cabang);
        lockreport($request->dari);
        return view('worksheetom.monitoring_retur', compact('retur', 'cabang'));
    }



    public function cetakmonitoringretur($request)
    {

        $dari = $request->dari;
        $sampai = $request->sampai;
        $pelanggan = '"' . $request->nama_pelanggan . '"';
        $query = Detailretur::query();
        $query->select(
            'detailretur.*',
            'penjualan.kode_pelanggan',
            'nama_pelanggan',
            'pasar',
            'hari',
            'tglretur',
            'kode_produk',
            'nama_barang',
            'isipcsdus',
            'isipack',
            'isipcs',
            DB::raw('IFNULL(jumlahpelunasan,0) as jumlahpelunasan')
        );
        $query->join('retur', 'detailretur.no_retur_penj', '=', 'retur.no_retur_penj');
        $query->join('penjualan', 'retur.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        $query->join('barang', 'detailretur.kode_barang', '=', 'barang.kode_barang');
        $query->leftJoin(
            DB::raw("(
                SELECT
                    kode_barang,
                    SUM(jumlah) as jumlahpelunasan
                FROM
                    detailretur_pelunasan
                GROUP BY kode_barang
            ) pelunasan"),
            function ($join) {
                $join->on('detailretur.kode_barang', '=', 'pelunasan.kode_barang');
                $join->on('detailretur.no_retur_penj', '=', 'pelunasan.no_retur_penj');
            }
        );

        if (empty($request->no_fak_penj) && empty($request->nama_pelanggan) && empty($request->dari) && empty($request->sampai)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }
        if (!empty($request->nama_pelanggan)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }

        if (!empty($request->no_fak_penj)) {
            $query->where('retur.no_fak_penj', $request->no_fak_penj);
        }

        if (!empty($request->jenis_retur)) {
            $query->where('jenis_retur', $request->jenis_retur);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tglretur', [$request->dari, $request->sampai]);
        }
        if (Auth::user()->kode_cabang != "PCF") {
            $query->where('karyawan.kode_cabang', Auth::user()->kode_cabang);
        } else {
            if (!empty($request->kode_cabang)) {
                $query->where('karyawan.kode_cabang', $request->kode_cabang);
            }
        }
        $query->where('jenis_retur', 'gb');
        $query->orderBy('tglretur');
        $query->orderBy('no_retur_penj');
        $retur = $query->get();


        return view('worksheetom.cetak_monitoringretur', compact('retur'));
    }

    public function showmonitoringretur(Request $request)
    {
        $retur = DB::table('retur')
            ->join('penjualan', 'retur.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('no_retur_penj', $request->no_retur_penj)
            ->first();

        $detail = DB::table('detailretur')
            ->select('detailretur.*', 'kode_produk', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs', DB::raw('IFNULL(jumlahpelunasan,0) as jumlahpelunasan'))
            ->join('barang', 'detailretur.kode_barang', '=', 'barang.kode_barang')
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_barang,
                    SUM(jumlah) as jumlahpelunasan
                FROM
                    detailretur_pelunasan
                WHERE no_retur_penj ='$request->no_retur_penj'
                GROUP BY kode_barang
            ) pelunasan"),
                function ($join) {
                    $join->on('detailretur.kode_barang', '=', 'pelunasan.kode_barang');
                }
            )

            ->where('no_retur_penj', $request->no_retur_penj)
            ->get();

        //dd($detail);
        return view('worksheetom.show_monitoring_retur', compact('retur', 'detail'));
    }


    public function storepelunasanretur(Request $request)
    {
        $id_user = Auth::user()->id;
        $no_retur_penj = $request->no_retur_penj;
        $barang = Harga::where('kode_barang', $request->kode_barang)->first();
        $jumlah = $request->jumlah;
        $cek_retur = DB::table('detailretur')->where('kode_barang', $request->kode_barang)->where('no_retur_penj', $request->no_retur_penj)->first();
        $cek_pelunasan = DB::table('detailretur_pelunasan')->where('kode_barang', $request->kode_barang)->where('no_retur_penj', $request->no_retur_penj)->first();

        $jmlretur = $cek_retur != null ? $cek_retur->jumlah : 0;
        $jmlpelunasan = $cek_pelunasan != null ? $cek_pelunasan->jumlah : 0;

        if (($jmlpelunasan + $jumlah) > $jmlretur) {
            echo 1;
        } else {
            try {
                DB::table('detailretur_pelunasan')
                    ->insert([
                        'no_retur_penj' => $no_retur_penj,
                        'kode_barang' => $request->kode_barang,
                        'jumlah' => $request->jumlah,
                        'no_dpb' => $request->no_dpb,
                        'id_admin' => $id_user
                    ]);
                echo 0;
            } catch (\Exception $e) {
                echo 2;
            }
        }
    }

    public function showpelunasanretur(Request $request)
    {
        $no_retur_penj = $request->no_retur_penj;
        $pelunasanretur = DB::table('detailretur_pelunasan')
            ->select('detailretur_pelunasan.*', 'kode_produk', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs', 'no_dpb')
            ->join('barang', 'detailretur_pelunasan.kode_barang', '=', 'barang.kode_barang')
            ->where('detailretur_pelunasan.no_retur_penj', $no_retur_penj)
            ->get();
        return view('worksheetom.show_pelunasan_retur', compact('pelunasanretur', 'no_retur_penj'));
    }


    public function deletepelunasanretur(Request $request)
    {
        $no_retur_penj = $request->no_retur_penj;
        $kode_barang = $request->kode_barang;
        $no_dpb = $request->no_dpb;

        try {
            DB::table('detailretur_pelunasan')
                ->where('no_retur_penj', $no_retur_penj)
                ->where('kode_barang', $kode_barang)
                ->where('no_dpb', $no_dpb)
                ->delete();
            echo 0;
        } catch (\Exception $e) {
            dd($e);
        }
    }


    public function showdetailretur(Request $request)
    {
        $detail = DB::table('detailretur')
            ->select('detailretur.*', 'kode_produk', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs', DB::raw('IFNULL(jumlahpelunasan,0) as jumlahpelunasan'))
            ->join('barang', 'detailretur.kode_barang', '=', 'barang.kode_barang')
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_barang,
                    SUM(jumlah) as jumlahpelunasan
                FROM
                    detailretur_pelunasan
                WHERE no_retur_penj ='$request->no_retur_penj'
                GROUP BY kode_barang
            ) pelunasan"),
                function ($join) {
                    $join->on('detailretur.kode_barang', '=', 'pelunasan.kode_barang');
                }
            )

            ->where('no_retur_penj', $request->no_retur_penj)
            ->get();
        return view('worksheetom.showdetailretur', compact('detail'));
    }


    //Monitoring Program

    public function monitoringprogram(Request $request)
    {
        $query = Program::query();
        $query->join('program_reward', 'program.kode_reward', '=', 'program_reward.kode_reward');
        if (!empty($request->periode_dari) && !empty($request->periode_sampai)) {
            $query->whereBetween('tanggal', [$request->periode_dari, $request->periode_sampai]);
        }
        $query->orderBy('tanggal', 'desc');
        $query->get();
        $program = $query->paginate(15);
        $program->appends($request->all());
        return view('worksheetom.monitoring_program', compact('program'));
    }

    public function createprogram()
    {
        $reward = DB::table('program_reward')->get();
        $produk = DB::table('master_barang')->where('status', 1)->get();
        return view('worksheetom.create_program', compact('reward', 'produk'));
    }

    public function editprogram($kode_program)
    {
        $reward = DB::table('program_reward')->get();
        $produk = DB::table('master_barang')->where('status', 1)->get();
        $program = DB::table('program')->where('kode_program', $kode_program)->first();
        return view('worksheetom.edit_program', compact('reward', 'produk', 'program'));
    }

    public function storeprogram(Request $request)
    {
        $tanggal = $request->tanggal;
        $thn = date('y', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));
        $lastprogram = DB::table('program')
            ->whereRaw('YEAR(tanggal)="' . $tahun . '"')
            ->orderBy('kode_program', 'desc')
            ->first();
        $format = 'PR' . $thn;
        $last_kode = $lastprogram != null ? $lastprogram->kode_program : '';
        $kode_program = buatkode($last_kode, $format, 5);
        $nama_program = $request->nama_program;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $kode_produk = serialize($request->kode_produk);
        $kode_reward = $request->kode_reward;
        $jml_target = str_replace(".", "", $request->jml_target);
        try {
            DB::table('program')->insert([
                'kode_program' => $kode_program,
                'tanggal' => $tanggal,
                'nama_program' => $nama_program,
                'dari' => $dari,
                'sampai' => $sampai,
                'kode_produk' => $kode_produk,
                'kode_reward' => $kode_reward,
                'jml_target' => $jml_target
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function updateprogram(Request $request, $kode_program)
    {
        $tanggal = $request->tanggal;
        $nama_program = $request->nama_program;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $kode_produk = serialize($request->kode_produk);
        $kode_reward = $request->kode_reward;
        $jml_target = str_replace(".", "", $request->jml_target);
        try {
            DB::table('program')
                ->where('kode_program', $kode_program)
                ->update([
                    'tanggal' => $tanggal,
                    'nama_program' => $nama_program,
                    'dari' => $dari,
                    'sampai' => $sampai,
                    'kode_produk' => $kode_produk,
                    'kode_reward' => $kode_reward,
                    'jml_target' => $jml_target
                ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }
    public function tambahpeserta($kode_program)
    {
        $program = DB::table('program')
            ->join('program_reward', 'program.kode_reward', '=', 'program_reward.kode_reward')
            ->where('kode_program', $kode_program)
            ->first();

        return view('worksheetom.tambahpeserta', compact('program'));
    }

    public function storepeserta(Request $request)
    {
        $tgl_mulai = $request->tgl_mulai;
        $kode_program = $request->kode_program;
        $kode_pelanggan = $request->kode_pelanggan;

        try {
            DB::table('program_peserta')->insert([
                'tgl_mulai' => $tgl_mulai,
                'kode_program' => $kode_program,
                'kode_pelanggan' => $kode_pelanggan
            ]);
            echo 0;
        } catch (\Exception $e) {
            echo 1;
        }
    }

    public function getpeserta($kode_program)
    {

        $program = DB::table('program')->where('kode_program', $kode_program)->first();
        $dari = $program->dari;
        $sampai = $program->sampai;

        $start_date = date_create($program->dari); //Tanggal Masuk Kerja
        $end_date = date_create($program->sampai); // Tanggal Presensi
        $diff = date_diff($start_date, $end_date); //Hitung Masa Kerja
        $lama = ROUND($diff->days / 30);



        $list_product = unserialize($program->kode_produk);
        $produk = "";
        $jmlproduk = count($list_product);
        $i = 0;
        foreach ($list_product as $p) {
            if ($i == $jmlproduk - 1) {
                $produk .= "'" . $p . "'";
            } else {
                $produk .= "'" . $p . "',";
            }
            $i++;
        }

        $query = Programpeserta::query();
        $query->select('program_peserta.*', 'nama_pelanggan', 'pelanggan.kode_cabang', 'nama_karyawan', 'jmldus');
        $query->join('pelanggan', 'program_peserta.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $query->leftJoin(
            DB::raw("(
                SELECT
                    penjualan.kode_pelanggan,
                    SUM(floor(jumlah/isipcsdus)) as jmldus
                FROM
                    detailpenjualan
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                AND kode_produk IN ($produk)
                GROUP BY penjualan.kode_pelanggan
            ) detailpenjualan"),
            function ($join) {
                $join->on('program_peserta.kode_pelanggan', '=', 'detailpenjualan.kode_pelanggan');
            }
        );
        if (Auth::user()->kode_cabang != "PCF") {
            $query->where('pelanggan.kode_cabang', Auth::user()->kode_cabang);
        }
        $query->where('kode_program', $kode_program);
        $query->orderBy('nama_pelanggan');
        $peserta = $query->get();
        return view('worksheetom.getpeserta', compact('peserta', 'program', 'lama'));
    }

    public function deletepeserta(Request $request)
    {
        $kode_program = $request->kode_program;
        $kode_pelanggan = $request->kode_pelanggan;
        try {
            DB::table('program_peserta')->where('kode_program', $kode_program)
                ->where('kode_pelanggan', $kode_pelanggan)
                ->delete();
            echo 0;
        } catch (\Exception $e) {
            echo 1;
        }
    }

    public function deleteprogram($kode_program)
    {
        $kode_program = Crypt::decrypt($kode_program);
        try {
            DB::table('program')->where('kode_program', $kode_program)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function cetakprogram($kode_program)
    {
        $kode_program = Crypt::decrypt($kode_program);
        $program = DB::table('program')
            ->join('program_reward', 'program.kode_reward', '=', 'program_reward.kode_reward')
            ->where('kode_program', $kode_program)->first();
        $dari = $program->dari;
        $sampai = $program->sampai;

        $start_date = date_create($program->dari); //Tanggal Masuk Kerja
        $end_date = date_create($program->sampai); // Tanggal Presensi
        $diff = date_diff($start_date, $end_date); //Hitung Masa Kerja
        $lama = ROUND($diff->days / 30);

        $bulanmulai = date('m', strtotime($program->dari));
        $tahunmulai = date('Y', strtotime($program->dari));


        //Cek Last Program
        $lastprogram = DB::table('program_peserta')
            ->orderBy('tgl_mulai', 'desc')
            ->first();

        $last_tgl_mulai = $lastprogram->tgl_mulai;
        $last_bulanmulai = date('m', strtotime($last_tgl_mulai));
        $last_tahunmulai = date('Y', strtotime($last_tgl_mulai));

        if ($last_tahunmulai > $tahunmulai) {
            $bulanakhir = $last_bulanmulai + $lama - 1 + 12;
            $blnakhir = $bulanakhir - 12;
        } else {
            $bulanakhir = $last_bulanmulai + $lama - 1;
            $blnakhir = $bulanakhir;
        }

        if ($bulanakhir < 9) {
            $bulanakhir = "0" . $bulanakhir;
        }

        if ($blnakhir < 9) {
            $blnakhir = "0" . $blnakhir;
        }


        $last_tgl_akhir = $last_tahunmulai . "-" . $blnakhir . "-01";

        $e_date = date('Y-m-t', strtotime($last_tgl_akhir));


        $list_product = unserialize($program->kode_produk);
        $produk = "";
        $jmlproduk = count($list_product);
        $i = 0;
        foreach ($list_product as $p) {
            if ($i == $jmlproduk - 1) {
                $produk .= "'" . $p . "'";
            } else {
                $produk .= "'" . $p . "',";
            }
            $i++;
        }

        $select_qty_bulan = "";
        $field_qty_bulan = "";
        $jmlbln = 1;

        //dd($bulanakhir);
        for ($bl = $bulanmulai; $bl <= $bulanakhir; $bl++) {

            if ($bl <= 12) {
                $bln = $bl;
                $thn = $tahunmulai;
            } else {
                $bln = $bl - 12;
                $thn = $tahunmulai + 1;
            }

            //echo $bl;
            $select_qty_bulan .= "SUM(IF(MONTH(tgltransaksi)='$bln' AND YEAR(tgltransaksi)='$thn' AND kode_produk IN ($produk),(floor(jumlah/isipcsdus)),0)) as jml_" . $bln . $thn . ",";
            $field_qty_bulan .= "jml_" . $bln . $thn . ",";
            $jmlbln++;
        }


        $query = Programpeserta::query();
        $query->selectRaw("
        $field_qty_bulan
        program_peserta.*,
        nama_pelanggan, pelanggan.kode_cabang, nama_karyawan");
        $query->join('pelanggan', 'program_peserta.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $query->leftJoin(
            DB::raw("(
                SELECT
                    $select_qty_bulan
                    penjualan.kode_pelanggan
                FROM
                    detailpenjualan
                INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                WHERE tgltransaksi BETWEEN '$dari' AND '$e_date'
                GROUP BY penjualan.kode_pelanggan
            ) detailpenjualan"),
            function ($join) {
                $join->on('program_peserta.kode_pelanggan', '=', 'detailpenjualan.kode_pelanggan');
            }
        );


        if (Auth::user()->kode_cabang != "PCF") {
            $query->where('pelanggan.kode_cabang', Auth::user()->kode_cabang);
        }
        $query->where('kode_program', $kode_program);
        $query->orderBy('nama_pelanggan');
        $peserta = $query->get();


        //dd($peserta);
        $start_month = $bulanmulai;
        $end_month = $bulanakhir;
        $start_year = $tahunmulai;


        return view('worksheetom.cetak_program', compact('peserta', 'program', 'start_month', 'end_month', 'jmlbln', 'start_year'));
    }

    public function evaluasisharing(Request $request)
    {
        $query = Evaluasisharing::query();
        $query->select('evaluasi_sharing.*');
        if ($this->cabang != "PCF") {
            $query->where('kode_cabang', $this->cabang);
        }
        if (!empty($request->kode_cabang)) {
            $query->where('evaluasi_sharing.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->periode_dari) && !empty($request->periode_sampai)) {
            $query->whereBetween('tanggal', [$request->periode_dari, $request->periode_sampai]);
        }
        $evaluasi = $query->paginate(15);
        $evaluasi->appends(request()->all());

        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('worksheetom.evaluasi_sharing', compact('evaluasi', 'cabang'));
    }

    public function createevaluasi()
    {
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('worksheetom.create_evaluasi', compact('cabang'));
    }


    public function storeevaluasi(Request $request)
    {
        $tanggal = $request->tanggal;
        $thn = date('y', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));
        $lastevaluasi = DB::table('evaluasi_sharing')
            ->whereRaw('YEAR(tanggal)="' . $tahun . '"')
            ->orderBy('kode_evaluasi', 'desc')
            ->first();
        $format = 'EV' . $thn;
        $last_kode = $lastevaluasi != null ? $lastevaluasi->kode_evaluasi : '';
        $kode_evaluasi = buatkode($last_kode, $format, 5);
        $jam = $request->jam;
        $peserta = $request->peserta;
        $tempat = $request->tempat;
        $kode_cabang = $request->kode_cabang;

        try {
            DB::table('evaluasi_sharing')->insert([
                'kode_evaluasi' => $kode_evaluasi,
                'tanggal' => $tanggal,
                'jam' => $jam,
                'peserta' => $peserta,
                'tempat' => $tempat,
                'kode_cabang' => $kode_cabang
            ]);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function detailevaluasi($kode_evaluasi)
    {
        $evaluasi = DB::table('evaluasi_sharing')->where('kode_evaluasi', $kode_evaluasi)->first();
        return view('worksheetom.detail_evaluasi', compact('evaluasi'));
    }


    public function storedetailevaluasi(Request $request)
    {
        $kode_agenda_edit = $request->kode_agenda;
        $kode_evaluasi = $request->kode_evaluasi;
        $agenda = $request->agenda;
        $hasil_pembahasan = $request->hasil_pembahasan;
        $action_plan = $request->action_plan;
        $due_date = $request->due_date;
        $pic = $request->pic;
        $status = $request->status;

        $lastagenda = DB::table('evaluasi_sharing_detail')
            ->where('kode_evaluasi', $kode_evaluasi)
            ->orderBy('kode_agenda', 'desc')
            ->first();
        $format = $kode_evaluasi;
        $last_kode = $lastagenda != null ? $lastagenda->kode_agenda : '';
        $kode_agenda = buatkode($last_kode, $format, 3);
        try {

            if (!empty($kode_agenda_edit)) {
                DB::table('evaluasi_sharing_detail')
                    ->where('kode_agenda', $kode_agenda_edit)
                    ->update([
                        'agenda' => $agenda,
                        'hasil_pembahasan' => $hasil_pembahasan,
                        'action_plan' => $action_plan,
                        'due_date' => $due_date,
                        'pic' => $pic,
                        'status' => $status
                    ]);
            } else {
                DB::table('evaluasi_sharing_detail')->insert([
                    'kode_agenda' => $kode_agenda,
                    'kode_evaluasi' => $kode_evaluasi,
                    'agenda' => $agenda,
                    'hasil_pembahasan' => $hasil_pembahasan,
                    'action_plan' => $action_plan,
                    'due_date' => $due_date,
                    'pic' => $pic,
                    'status' => $status
                ]);
            }

            echo 0;
        } catch (\Exception $e) {
            echo 1;
        }
    }


    public function getdetailevaluasi($kode_evaluasi)
    {
        $detailevaluasi = DB::table('evaluasi_sharing_detail')->where('kode_evaluasi', $kode_evaluasi)->get();
        return view('worksheetom.getdetail_evaluasi', compact('detailevaluasi', 'kode_evaluasi'));
    }

    public function deleteagenda(Request $request)
    {
        try {
            DB::table('evaluasi_sharing_detail')->where('kode_agenda', $request->kode_agenda)->delete();
            echo 0;
        } catch (\Exception $e) {
            echo 1;
        }
    }


    public function editevaluasi($kode_evaluasi)
    {
        $evaluasi = DB::table('evaluasi_sharing')->where('kode_evaluasi', $kode_evaluasi)->first();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('worksheetom.edit_evaluasi', compact('cabang', 'evaluasi'));
    }


    public function updateevaluasi(Request $request, $kode_evaluasi)
    {
        $tanggal = $request->tanggal;
        $jam = $request->jam;
        $peserta = $request->peserta;
        $tempat = $request->tempat;
        $kode_cabang = $request->kode_cabang;

        try {
            DB::table('evaluasi_sharing')
                ->where('kode_evaluasi', $kode_evaluasi)
                ->update([
                    'tanggal' => $tanggal,
                    'jam' => $jam,
                    'peserta' => $peserta,
                    'tempat' => $tempat,
                    'kode_cabang' => $kode_cabang
                ]);
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }


    public function deleteevaluasi($kode_evaluasi)
    {
        $kode_evaluasi = Crypt::decrypt($kode_evaluasi);
        try {
            DB::table('evaluasi_sharing')->where('kode_evaluasi', $kode_evaluasi)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function cetakevaluasi($kode_evaluasi)
    {
        $kode_evaluasi = Crypt::decrypt($kode_evaluasi);
        $evaluasi = DB::table('evaluasi_sharing')->where('kode_evaluasi', $kode_evaluasi)->first();
        $detailevaluasi = DB::table('evaluasi_sharing_detail')->where('kode_evaluasi', $kode_evaluasi)->get();
        return view('worksheetom.cetak_evaluasi', compact('evaluasi', 'detailevaluasi'));
    }


    public function kebutuhancabang(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $query = Kebutuhancabang::query();
        $query->select('kebutuhan_cabang.*', 'jenis_kebutuhan');
        $query->join('kebutuhan_cabang_jenis', 'kebutuhan_cabang.kode_jenis_kebutuhan', '=', 'kebutuhan_cabang_jenis.kode_jenis_kebutuhan');
        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_jenis_kebutuhan)) {
            $query->where('kebutuhan_cabang.kode_jenis_kebutuhan', $request->kode_jenis_kebutuhan);
        }

        if (Auth::user()->kode_cabang != "PCF") {
            $query->where('kode_cabang', Auth::user()->kode_cabang);
        }
        $kc = $query->paginate(15);
        $kc->appends(request()->all());

        $jenis_kebutuhan = DB::table('kebutuhan_cabang_jenis')->orderBy('kode_jenis_kebutuhan')->get();


        if (isset($request->cetak)) {
            return view('worksheetom.cetak_kebutuhan_cabang', compact('cabang', 'kc', 'jenis_kebutuhan', 'kode_cabang'));
        } else {
            return view('worksheetom.kebutuhan_cabang', compact('cabang', 'kc', 'jenis_kebutuhan'));
        }
    }

    public function createkebutuhancabang()
    {
        $jenis_kebutuhan = DB::table('kebutuhan_cabang_jenis')->orderBy('kode_jenis_kebutuhan')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('worksheetom.create_kebutuhancabang', compact('jenis_kebutuhan', 'cabang'));
    }

    public function storekebutuhancabang(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $lastkebutuhan = DB::table('kebutuhan_cabang')
            ->where('kode_cabang', $kode_cabang)
            ->orderBy('kode_kebutuhan', 'desc')
            ->first();
        $format = 'KB' . $kode_cabang;
        $last_kode = $lastkebutuhan != null ? $lastkebutuhan->kode_kebutuhan : '';
        $kode_kebutuhan = buatkode($last_kode, $format, 5);

        $kode_jenis_kebutuhan = $request->kode_jenis_kebutuhan;
        $uraian_kebutuhan = $request->uraian_kebutuhan;
        $periode_akhir = $request->periode_akhir;

        try {
            DB::table('kebutuhan_cabang')->insert([
                'kode_kebutuhan' => $kode_kebutuhan,
                'kode_cabang' => $kode_cabang,
                'kode_jenis_kebutuhan' => $kode_jenis_kebutuhan,
                'uraian_kebutuhan' => $uraian_kebutuhan,
                'periode_akhir' => $periode_akhir
            ]);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function editkebutuhancabang($kode_kebutuhan)
    {
        $jenis_kebutuhan = DB::table('kebutuhan_cabang_jenis')->orderBy('kode_jenis_kebutuhan')->get();
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $kc = DB::table('kebutuhan_cabang')->where('kode_kebutuhan', $kode_kebutuhan)->first();
        return view('worksheetom.edit_kebutuhancabang', compact('jenis_kebutuhan', 'cabang', 'kc'));
    }


    public function updatekebutuhancabang($kode_kebutuhan, Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kode_jenis_kebutuhan = $request->kode_jenis_kebutuhan;
        $uraian_kebutuhan = $request->uraian_kebutuhan;
        $periode_akhir = $request->periode_akhir;

        try {
            DB::table('kebutuhan_cabang')
                ->where('kode_kebutuhan', $kode_kebutuhan)
                ->update([
                    'kode_cabang' => $kode_cabang,
                    'kode_jenis_kebutuhan' => $kode_jenis_kebutuhan,
                    'uraian_kebutuhan' => $uraian_kebutuhan,
                    'periode_akhir' => $periode_akhir
                ]);
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function deletekebutuhancabang($kode_kebutuhan)
    {
        $kode_kebutuhan = Crypt::decrypt($kode_kebutuhan);
        try {
            DB::table('kebutuhan_cabang')->where('kode_kebutuhan', $kode_kebutuhan)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }


    public function rekapbuffermaxsell()
    {
        $cbg = new Cabang();
        if (Auth::user()->kode_cabang == "PCF" && Auth::user()->kode_cabang == "PST") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = $cbg->getCabanggudang($this->cabang);
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('worksheetom.rekapbuffermaxsell', compact('cabang', 'bulan'));
    }


    public function cetakrekapbuffermaxsell(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan < 10 ? "0" . $request->bulan : $request->bulan;
        $tahun = $request->tahun;
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date('Y-m-t', strtotime($dari));

        $produk = DB::table('master_barang')
            ->where('status', 1)
            ->orderBy('kode_produk')->get();

        $select_buffer = "";
        $select_limitstok = "";
        $select_penjualan = "";
        $select_field = "";
        $jml_produk = 1;
        foreach ($produk as $d) {
            $select_buffer .= "SUM(IF(kode_produk='$d->kode_produk',jumlah,0)) as `buffer_" . $d->kode_produk . "`,";
            $select_limitstok .= "SUM(IF(kode_produk='$d->kode_produk',jumlah,0)) as `limit_" . $d->kode_produk . "`,";
            $select_penjualan .= "SUM(IF(kode_produk='$d->kode_produk',ROUND((jumlah/isipcsdus),3),0)) as `sellout_" . $d->kode_produk . "`,";
            $select_field .= "CONCAT(IFNULL(buffer_" . $d->kode_produk . ",0),'|',IFNULL(limit_" . $d->kode_produk . ",0),'|',IFNULL(sellout_" . $d->kode_produk . ",0)) as `data_" . $d->kode_produk . "`,";

            $jml_produk++;
        }

        $query = Cabang::query();
        $query->selectRaw("
            $select_field
            cabang.kode_cabang,nama_cabang");
        $query->leftJoin(
            DB::raw("(
                SELECT
                    $select_buffer
                    kode_cabang
                FROM
                    detail_bufferstok
                INNER JOIN buffer_stok ON detail_bufferstok.kode_bufferstok = buffer_stok.kode_bufferstok
                GROUP BY kode_cabang
                ) bufferstok"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'bufferstok.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                    $select_limitstok
                    kode_cabang
                FROM
                    limit_stok_detail
                INNER JOIN limit_stok ON limit_stok_detail.kode_limit_stok = limit_stok.kode_limit_stok
                GROUP BY kode_cabang
                ) limitstok"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'limitstok.kode_cabang');
            }
        );

        $query->leftJoin(
            DB::raw("(
                SELECT
                    $select_penjualan
                    karyawan.kode_cabang
                FROM
                    detailpenjualan
                    INNER JOIN barang ON detailpenjualan.kode_barang = barang.kode_barang
                    INNER JOIN penjualan ON detailpenjualan.no_fak_penj = penjualan.no_fak_penj
                    INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                    WHERE tgltransaksi BETWEEN '$dari' AND '$sampai'
                GROUP BY kode_cabang
                ) penjualan"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'penjualan.kode_cabang');
            }
        );

        if (!empty($kode_cabang)) {
            $query->where('cabang.kode_cabang', $kode_cabang);
        }
        $rekap = $query->get();

        $bln = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $namabulan = $bln[$request->bulan];
        return view('worksheetom.cetak_rekapbuffermaxsell', compact('rekap', 'produk', 'jml_produk', 'namabulan', 'tahun'));
    }


    public function bufferlimit(Request $request)
    {
        $cabang = DB::table('cabang')->get();
        return view('worksheetom.bufferlimit', compact('cabang'));
    }


    public function getbufferlimit(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $detail = DB::table('master_barang')
            ->select('master_barang.kode_produk', 'nama_barang', 'jmlbufferstok', 'jmllimitstok')
            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    jumlah as jmlbufferstok
                FROM
                    detail_bufferstok
                INNER JOIN buffer_stok ON detail_bufferstok.kode_bufferstok = buffer_stok.kode_bufferstok
                WHERE kode_cabang = '$request->kode_cabang'
            ) bufferstok"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'bufferstok.kode_produk');
                }
            )

            ->leftJoin(
                DB::raw("(
                SELECT
                    kode_produk,
                    jumlah as jmllimitstok
                FROM
                limit_stok_detail
                INNER JOIN limit_stok ON limit_stok_detail.kode_limit_stok = limit_stok.kode_limit_stok
                WHERE kode_cabang = '$request->kode_cabang'
            ) limitstok"),
                function ($join) {
                    $join->on('master_barang.kode_produk', '=', 'limitstok.kode_produk');
                }
            )
            ->where('status', 1)
            ->orderBy('kode_produk')
            ->get();

        return view('worksheetom.getbufferlimit', compact('detail'));
    }


    public function storebufferlimit(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kode_produk = $request->kode_produk;
        $buffer_stok = $request->bufferstok;
        $limit_stok = $request->limitstok;
        $kode_bufferstok = "BF" . $kode_cabang;
        $kode_limit_stok = "MX" . $kode_cabang;
        $detail_buffer = [];
        $detail_limit = [];
        for ($i = 0; $i < count($kode_produk); $i++) {
            $bufferstok = !empty($buffer_stok[$i]) ? $buffer_stok[$i] : 0;
            $limitstok = !empty($limit_stok[$i]) ? $limit_stok[$i] : 0;

            //echo $bufferstok . "<br>";
            if (!empty($bufferstok)) {
                $detail_buffer[]   = [
                    'kode_bufferstok' => $kode_bufferstok,
                    'kode_produk' => $kode_produk[$i],
                    'jumlah' => $bufferstok
                ];
            }

            if (!empty($limitstok)) {
                $detail_limit[]   = [
                    'kode_limit_stok' => $kode_limit_stok,
                    'kode_produk' => $kode_produk[$i],
                    'jumlah' => $limitstok
                ];
            }
        }



        // dd($kode_produk);
        DB::beginTransaction();
        try {




            if (!empty($detail_buffer)) {
                DB::table('buffer_stok')->where('kode_bufferstok', $kode_bufferstok)->delete();
                DB::table('buffer_stok')->insert([
                    'kode_bufferstok' => $kode_bufferstok,
                    'kode_cabang' => $kode_cabang,
                    'id_admin' => Auth::user()->id
                ]);
                $chunks_buffer = array_chunk($detail_buffer, 5);
                foreach ($chunks_buffer as $chunk_buffer) {
                    Detailbuffer::insert($chunk_buffer);
                }
            }

            if (!empty($detail_limit)) {
                DB::table('limit_stok')->where('kode_limit_stok', $kode_limit_stok)->delete();
                DB::table('limit_stok')->insert([
                    'kode_limit_stok' => $kode_limit_stok,
                    'kode_cabang' => $kode_cabang,
                    'id_admin' => Auth::user()->id
                ]);
                $chunks_limit = array_chunk($detail_limit, 5);
                foreach ($chunks_limit as $chunk_limit) {
                    Detaillimitstok::insert($chunk_limit);
                }
            }


            DB::commit();

            return Redirect::back()->with(['success' => 'Data Berhasil Di Udpate']);
        } catch (\Exception $e) {
            DB::rollBack();

            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Di Udpate']);
        }
    }


    public function produkexpired()
    {
        return view('worksheetom.produk_expired');
    }

    public function createprodukexpired()
    {
        return view('worksheetom.create_produkexpired');
    }


    public function laporanratiobs()
    {
        if ($this->cabang == "PCF") {
            $cabang = DB::table('cabang')->get();
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $this->cabang)->get();
        }
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('ratiobs.laporan.frm_laporanRatioBS', compact('cabang', 'bulan'));
    }


    public function cetakratiobs(Request $request)
    {
        $cabang = $request->kode_cabang;
        $bulan = $request->bulan > 9 ? $request->bulan : "0" . $request->bulan;
        $tahun = $request->tahun;

        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date('Y-m-t', strtotime($dari));

        $produk = DB::table('master_barang')->where('status', 1)
            ->orderBy('kode_produk');
        $select_mutasi = "";
        $field_mutasi = "";
        $select_total_retur = "";
        $field_total_retur = "";
        foreach ($produk->get() as $d) {

            $field_mutasi .= "retur_" . $d->kode_produk . ",reject_mobil_" . $d->kode_produk . ",reject_pasar_" . $d->kode_produk . ",reject_gudang_" . $d->kode_produk . ",repack_" . $d->kode_produk . ",";
            $field_total_retur .= "totalretur_" . $d->kode_produk . ",";

            $select_mutasi .= "
                SUM(IF(dmc.kode_produk='$d->kode_produk' AND jenis_mutasi = 'RETUR',jumlah/isipcsdus,0)) as retur_" . $d->kode_produk . ",
                SUM(IF(dmc.kode_produk='$d->kode_produk' AND jenis_mutasi = 'REJECT MOBIL',jumlah/isipcsdus,0)) as reject_mobil_" . $d->kode_produk . ",
                SUM(IF(dmc.kode_produk='$d->kode_produk' AND jenis_mutasi = 'REJECT PASAR',jumlah/isipcsdus,0)) as reject_pasar_" . $d->kode_produk . ",
                SUM(IF(dmc.kode_produk='$d->kode_produk' AND jenis_mutasi = 'REJECT GUDANG',jumlah/isipcsdus,0)) as reject_gudang_" . $d->kode_produk . ",
                SUM(IF(dmc.kode_produk='$d->kode_produk' AND jenis_mutasi = 'REPACK',jumlah/isipcsdus,0)) as repack_" . $d->kode_produk . ",";

            $select_total_retur .= "SUM(IF(kode_produk='$d->kode_produk',detailretur.subtotal,0)) as totalretur_" . $d->kode_produk . ",";
        }


        $query = Cabang::query();
        $query->selectRaw("
        $field_mutasi
        $field_total_retur
        cabang.kode_cabang,nama_cabang");
        $query->leftJoin(
            DB::raw("(
                SELECT
                $select_mutasi
                kode_cabang
                FROM detail_mutasi_gudang_cabang dmc
                INNER JOIN master_barang ON dmc.kode_produk = master_barang.kode_produk
                INNER JOIN mutasi_gudang_cabang mc ON dmc.no_mutasi_gudang_cabang = mc.no_mutasi_gudang_cabang
                WHERE tgl_mutasi_gudang_cabang BETWEEN '$dari' AND '$sampai'
                GROUP BY kode_cabang
             ) mutasicabang"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'mutasicabang.kode_cabang');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT
                $select_total_retur
                karyawan.kode_cabang
                FROM detailretur
                INNER JOIN barang ON detailretur.kode_barang = barang.kode_barang
                INNER JOIN retur ON detailretur.no_retur_penj = retur.no_retur_penj
                INNER JOIN penjualan ON retur.no_fak_penj = penjualan.no_fak_penj
                INNER JOIN karyawan ON penjualan.id_karyawan = karyawan.id_karyawan
                WHERE tglretur BETWEEN '$dari' AND '$sampai'
                GROUP BY kode_cabang
             ) hargeretur"),
            function ($join) {
                $join->on('cabang.kode_cabang', '=', 'hargeretur.kode_cabang');
            }
        );

        if (!empty($cabang)) {
            $query->where('cabang.kode_cabang', $cabang);
        }
        $ratiobs = $query->get();

        if (isset($_POST['export'])) {
            // Fungsi header dengan mengirimkan raw data excel
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Ratio BS.xls");
        }
        return view('worksheetom.cetak_ratiobs', compact('ratiobs', 'produk'));
    }


    public function createvisitpelanggan($no_fak_penj)
    {

        $penjualan = DB::table('penjualan')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('no_fak_penj', $no_fak_penj)->first();
        return view('worksheetom.create_visitpelanggan', compact('penjualan'));
    }


    public function storevisitpelanggan(Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $tanggal_visit = $request->tanggal_visit;
        $hasil_konfirmasi = $request->hasil_konfirmasi;
        $note = $request->note;
        $saran = $request->saran;
        $act_om = $request->act_om;
        $kode_cabang = $request->kode_cabang;


        $lastvisit = DB::table('visitpelanggan')
            ->join('penjualan', 'visitpelanggan.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->whereRaw('YEAR(tanggal_visit)="' . date('Y', strtotime($tanggal_visit)) . '"')
            ->where('kode_cabang', $kode_cabang)
            ->orderBy('kode_visit', 'desc')
            ->first();

        $lastkodevisit = $lastvisit != NULL ? $lastvisit->kode_visit : '';
        $kode_visit = buatkode($lastkodevisit, "VST" . $kode_cabang . substr(date('Y', strtotime($tanggal_visit)), 2, 2), 5);

        try {
            DB::table('visitpelanggan')->insert([
                'kode_visit' => $kode_visit,
                'no_fak_penj' => $no_fak_penj,
                'tanggal_visit' => $tanggal_visit,
                'hasil_konfirmasi' => $hasil_konfirmasi,
                'note' => $note,
                'saran' => $saran,
                'act_om' => $act_om,
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }


    public function visitpelanggan(Request $request)
    {
        $query = Visitpelanggan::query();
        $query->select('visitpelanggan.*', 'nama_pelanggan', 'pasar', 'nama_karyawan', 'tgltransaksi', 'total', 'jenistransaksi', 'karyawan.kode_cabang');
        $query->join('penjualan', 'visitpelanggan.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
        if (Auth::user()->kode_cabang != "PCF") {
            $query->where('karyawan.kode_cabang', Auth::user()->kode_cabang);
        } else {
            if (!empty($request->kode_cabang)) {
                $query->where('karyawan.kode_cabang', $request->kode_cabang);
            }
        }
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tanggal_visit', [$request->dari, $request->sampai]);
        }


        $visitpelanggan = $query->get();

        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);
        if (isset($request->cetak)) {
            return view('worksheetom.cetak_visitpelanggan', compact('visitpelanggan', 'cabang'));
        } else {
            return view('worksheetom.visitpelanggan', compact('visitpelanggan', 'cabang'));
        }
    }

    public function editvisitpelanggan($kode_visit)
    {

        $visitpelanggan = DB::table('visitpelanggan')
            ->join('penjualan', 'visitpelanggan.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('kode_visit', $kode_visit)->first();
        return view('worksheetom.edit_visitpelanggan', compact('visitpelanggan'));
    }


    public function updatevisitpelanggan($kode_visit, Request $request)
    {
        $no_fak_penj = $request->no_fak_penj;
        $tanggal_visit = $request->tanggal_visit;
        $hasil_konfirmasi = $request->hasil_konfirmasi;
        $note = $request->note;
        $saran = $request->saran;
        $act_om = $request->act_om;
        $kode_cabang = $request->kode_cabang;
        $kode_visit = Crypt::decrypt($kode_visit);


        try {
            DB::table('visitpelanggan')
                ->where('kode_visit', $kode_visit)
                ->update([
                    'kode_visit' => $kode_visit,
                    'no_fak_penj' => $no_fak_penj,
                    'tanggal_visit' => $tanggal_visit,
                    'hasil_konfirmasi' => $hasil_konfirmasi,
                    'note' => $note,
                    'saran' => $saran,
                    'act_om' => $act_om,
                ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function deletevisitpelanggan($kode_visit)
    {
        $kode_visit = Crypt::decrypt($kode_visit);
        try {
            DB::table('visitpelanggan')->where('kode_visit', $kode_visit)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}
