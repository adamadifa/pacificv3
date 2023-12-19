<?php

namespace App\Http\Controllers;

use App\Models\Evaluasisharing;
use App\Models\Harga;
use App\Models\Program;
use App\Models\Retur;
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

        if ($this->cabang != "PCF") {
            $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
            $cabang[] = "";
            foreach ($cbg as $c) {
                $cabang[] = $c->kode_cabang;
            }
            $query->whereIn('karyawan.kode_cabang', $cabang);
        }


        $retur = $query->paginate(15);

        $retur->appends($request->all());

        lockreport($request->dari);
        return view('worksheetom.monitoring_retur', compact('retur'));
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
        $kode_program = $request->kode_program;
        $kode_pelanggan = $request->kode_pelanggan;

        try {
            DB::table('program_peserta')->insert([
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


        $peserta = DB::table('program_peserta')
            ->select('program_peserta.*', 'nama_pelanggan', 'pelanggan.kode_cabang', 'nama_karyawan', 'jmldus')
            ->join('pelanggan', 'program_peserta.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->leftJoin(
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
            )
            ->where('kode_program', $kode_program)
            ->orderBy('nama_pelanggan')
            ->get();
        return view('worksheetom.getpeserta', compact('peserta', 'program'));
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

    public function evaluasisharing(Request $request)
    {
        $query = Evaluasisharing::query();
        $query->select('evaluasi_sharing.*');
        if ($this->cabang != "PCF") {
            $query->where('kode_cabang', $this->cabang);
        }
        $evaluasi = $query->paginate(15);
        $evaluasi->appends(request()->all());
        return view('worksheetom.evaluasi_sharing', compact('evaluasi'));
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
}
