<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Limitkredit;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use DateTime;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LimitkreditController extends Controller
{
    protected $cabang;
    public function __construct()
    {
        // Fetch the Site Settings object
        $this->middleware(function ($request, $next) {
            $this->cabang = Auth::user()->kode_cabang;
            $this->level = Auth::user()->level;
            return $next($request);
        });
        View::share('cabang', $this->cabang);
    }
    public function index(Request $request)
    {
        $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB');
        $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
        $ega = array('TSM', 'GRT');
        $pelanggan = '"' . $request->nama_pelanggan . '"';
        $query = Limitkredit::query();
        if ($this->cabang != "PCF") {

            if (Auth::user()->id == 7) {
                $query->whereIn('pelanggan.kode_cabang', $ega);
            } else {
                $query->where('pelanggan.kode_cabang', $this->cabang);
            }
        } else {
            if (Auth::user()->id == 82) {
                $query->whereIn('pelanggan.kode_cabang', $wilayah_barat);
            }
        }
        $query->select('pengajuan_limitkredit_v3.*', 'nama_pelanggan', 'pelanggan.kode_cabang');
        $query->orderBy('tgl_pengajuan', 'desc');
        $query->orderBy('no_pengajuan', 'desc');
        $query->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->leftJoin('users', 'pengajuan_limitkredit_v3.id_approval', '=', 'users.id');
        // if (empty($request->nama_pelanggan) && empty($request->dari) && empty($request->sampai) && empty($request->status)) {
        //     $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        // }
        if (!empty($request->nama_pelanggan)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }

        if (!empty($request->kode_cabang)) {
            $query->where('pelanggan.kode_cabang', $request->kode_cabang);
        }



        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pengajuan', [$request->dari, $request->sampai]);
        }


        if ($this->level == "admin" || $this->level == "manager accounting") {
            if ($request->status == "pending") {
                $status = 0;
            } elseif ($request->status == "disetujui") {
                $status = 1;
            } elseif ($request->status == "ditolak") {
                $status = 2;
            }

            if (!empty($request->status)) {
                $query->where('pengajuan_limitkredit_v3.status', $status);
            }
        }
        if ($this->level == "kepala penjualan") {
            if ($request->status == "pending") {
                $query->whereNull('kacab');
            } else if ($request->status == "disetujui") {
                $query->whereNotNull('kacab');
                $query->where('pengajuan_limitkreidt_v3.status', '!=', 2);
                $query->orwhereNotNull('kacab');
                $query->where('pengajuan_limitkreidt_v3.status', '=', 2);
                $query->where('level', '!=', Auth::user()->level);
            } else if ($request->status == "ditolak") {
                $query->whereNotNull('kacab');
                $query->where('pengajuan_limitkreidt_v3.status', 2);
                $query->where('level', Auth::user()->level);
            }
            $query->where('jumlah', '>', 2000000);
        }


        if ($this->level == "rsm") {
            if ($request->status == "pending") {
                $query->whereNotNull('kacab');
                $query->whereNull('rsm');
                $query->where('jumlah', '>', 5000000);
            } else if ($request->status == "disetujui") {
                $query->whereNotNull('rsm');
                $query->where('pengajuan_limitkreidt_v3.status', '!=', 2);
                $query->where('jumlah', '>', 5000000);
                $query->orwhereNotNull('rsm');
                $query->where('pengajuan_limitkreidt_v3.status', '=', 2);
                $query->where('level', '!=', Auth::user()->level);
                $query->where('jumlah', '>', 5000000);
            } else if ($request->status == "ditolak") {
                $query->whereNotNull('rsm');
                $query->where('pengajuan_limitkreidt_v3.status', 2);
                $query->where('level', Auth::user()->level);
                $query->where('jumlah', '>', 5000000);
            }
        }

        if ($this->level == "manager marketing") {
            if ($request->status == "pending") {
                $query->whereNotNull('rsm');
                $query->whereNull('mm');
                $query->where('pengajuan_limitkreidt_v3.status', 0);
                $query->where('jumlah', '>', 10000000);
                $query->orWhereIn('pelanggan.kode_cabang', $wilayah_timur);
                $query->whereNotNull('rsm');
            } else if ($request->status == "disetujui") {
                $query->whereNotNull('mm');
                $query->where('pengajuan_limitkreidt_v3.status', '!=', 2);
                $query->where('jumlah', '>', 10000000);
                $query->orwhereNotNull('mm');
                $query->where('pengajuan_limitkreidt_v3.status', '=', 2);
                $query->where('level', '!=', Auth::user()->level);
                $query->where('jumlah', '>', 10000000);
            } else if ($request->status == "ditolak") {
                $query->whereNotNull('mm');
                $query->where('pengajuan_limitkreidt_v3.status', 2);
                $query->where('level', Auth::user()->level);
                $query->where('jumlah', '>', 10000000);
            }
        }
        // if ($this->level == "kepala admin") {
        //     if ($request->status == "pending") {
        //         $query->whereNull('kacab');
        //     } else if ($request->status == "disetujui") {
        //         $query->whereNotNull('kacab');
        //         $query->where('status', '!=', 2);
        //         $query->orwhereNotNull('kacab');
        //         $query->where('status', '=', 2);
        //         $query->where('level', '!=', Auth::user()->level);
        //     } else if ($request->status == "ditolak") {
        //         $query->whereNotNull('kacab');
        //         $query->where('status', 2);
        //         $query->where('level', Auth::user()->level);
        //     }
        // }
        if (
            $this->level == "kepala admin" ||
            $this->level == "admin penjualan" || $this->level == "admin penjualan dan kas kecil" ||
            $this->level == "admin penjualan dan kasir"
        ) {
            if ($request->status == "pending") {
                $query->where('pengajuan_limitkreidt_v3.status', 0);
            } else if ($request->status == "disetujui") {
                $query->where('pengajuan_limitkreidt_v3.status', 1);
            } else if ($request->status == "ditolak") {
                $query->where('pengajuan_limitkreidt_v3.status', 2);
            }
        }

        // if ($this->level == "general manager") {
        //     if ($request->status == "pending") {
        //         $query->whereNotNull('mm');
        //         $query->whereNull('gm');
        //     } else if ($request->status == "disetujui") {
        //         $query->whereNotNull('gm');
        //         $query->where('status', '!=', 2);
        //         $query->orwhereNotNull('gm');
        //         $query->where('status', '=', 2);
        //         $query->where('level', '!=', Auth::user()->level);
        //     } else if ($request->status == "ditolak") {
        //         $query->whereNotNull('mm');
        //         $query->whereNotNull('gm');
        //         $query->where('status', 2);
        //         $query->where('level', Auth::user()->level);
        //     } else {
        //         $query->whereNotNull('mm');
        //     }
        //     $query->where('jumlah', '>', 10000000);
        // }

        if ($this->level == "direktur") {
            if ($request->status == "pending") {
                $query->whereNotNull('mm');
                $query->whereNull('dirut');
                $query->where('pengajuan_limitkreidt_v3.status', 0);
                $query->where('jumlah', '>', 10000000);
            } else if ($request->status == "disetujui") {
                $query->whereNotNull('dirut');
                $query->where('pengajuan_limitkreidt_v3.status', '!=', 2);
                $query->where('jumlah', '>', 10000000);
                $query->orwhereNotNull('dirut');
                $query->where('pengajuan_limitkreidt_v3.status', '=', 2);
                $query->where('level', '!=', Auth::user()->level);
                $query->where('jumlah', '>', 10000000);
            } else if ($request->status == "ditolak") {
                $query->whereNotNull('gm');
                $query->whereNotNull('dirut');
                $query->where('pengajuan_limitkreidt_v3.status', 2);
                $query->where('level', Auth::user()->level);
                $query->where('jumlah', '>', 10000000);
            }
        }



        $limitkredit = $query->paginate(15);
        $limitkredit->appends($request->all());

        $cbg = new Cabang();
        $cabang = $cbg->getCabanggudang($this->cabang);
        $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB');
        $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
        return view('limitkredit.index', compact('limitkredit', 'cabang', 'wilayah_barat', 'wilayah_timur'));
    }

    public function create($kode_pelanggan)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $pelanggan = DB::table('pelanggan')
            ->select('pelanggan.*', 'nama_karyawan')
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->first();
        $lasttopup = DB::table('pengajuan_limitkredit_v3')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->orderBy('tgl_pengajuan', 'desc')
            ->first();
        $listfaktur = DB::table('penjualan')
            ->select('penjualan.no_fak_penj', 'tgltransaksi', DB::raw(
                'IFNULL(penjualan.total,0) - IFNULL(retur.total,0) AS nettopiutang'
            ), 'jmlbayar')
            ->leftJoin(
                DB::raw("(
                SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total FROM retur GROUP BY retur.no_fak_penj
            ) retur"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                }
            )
            ->leftJoin(
                DB::raw("(
                SELECT no_fak_penj, IFNULL(SUM(bayar),0) as jmlbayar
                FROM historibayar
                GROUP BY no_fak_penj
            ) historibayar"),
                function ($join) {
                    $join->on('penjualan.no_fak_penj', '=', 'historibayar.no_fak_penj');
                }
            )
            ->where('kode_pelanggan', $kode_pelanggan)
            ->where('status_lunas', 2)
            ->get();
        return view('limitkredit.create', compact('pelanggan', 'lasttopup', 'listfaktur'));
    }

    public function cetak($no_pengajuan)
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
        //return view('limitkredit.cetak', compact('limitkredit'));

        $pdf = PDF::loadview('limitkredit.cetak', compact('limitkredit', 'komentar'))->setPaper('a4');
        return $pdf->stream();
    }

    public function delete($no_pengajuan, $kode_pelanggan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $lastlimitpelanggan = DB::table('pengajuan_limitkredit_v3')
            ->select('no_pengajuan', 'status')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->where('no_pengajuan', '!=', $no_pengajuan)
            ->orderBy('no_pengajuan', 'desc')
            ->first();


        try {
            DB::table('pengajuan_limitkredit_v3')
                ->where('no_pengajuan', $no_pengajuan)->delete();
            DB::table('pengajuan_limitkredit_analisa_v3')
                ->where('no_pengajuan', $no_pengajuan)->delete();
            if ($lastlimitpelanggan != null) {
                $last_no_pengajuan_pelanggan = $lastlimitpelanggan->no_pengajuan;
                if ($lastlimitpelanggan->status == 1 || $lastlimitpelanggan == 2) {
                    DB::table('pengajuan_limitkredit_v3')
                        ->where('no_pengajuan', $last_no_pengajuan_pelanggan)
                        ->update([
                            'cek_ajuan' => null
                        ]);
                }
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan Hubungi Tim IT']);
        }
    }

    public function get_topup_terakhir(Request $request)
    {
        $tgl1 = new DateTime($request->topup_terakhir);
        $tgl2 = new DateTime(date('Y-m-d'));
        $lama_topup = $tgl2->diff($tgl1)->days + 1;

        // tahun
        $y = $tgl2->diff($tgl1)->y;

        // bulan
        $m = $tgl2->diff($tgl1)->m;

        // hari
        $d = $tgl2->diff($tgl1)->d;

        $usia_topup = $y . " tahun " . $m . " bulan " . $d . " hari";
        echo $lama_topup . "|" . $usia_topup;
    }
    public function store(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $tgl_pengajuan = $request->tgl_pengajuan;
        $tgl = explode("-", $tgl_pengajuan);
        $tahun = $tgl[0];
        $thn = substr($tahun, 2, 2);
        $lastlimit = DB::table('pengajuan_limitkredit_v3')
            ->select('no_pengajuan')
            ->whereRaw('YEAR(tgl_pengajuan) = "' . $tahun . '"')
            ->whereRaw('MID(no_pengajuan,4,3) = "' . $kode_cabang . '"')
            ->orderBy('no_pengajuan', 'desc')
            ->first();

        if ($lastlimit == null) {
            $last_no_pengajuan = 'PLK' . $kode_cabang . $thn . '00000';
        } else {
            $last_no_pengajuan = $lastlimit->no_pengajuan;
        }
        $no_pengajuan = buatkode($last_no_pengajuan, 'PLK' . $kode_cabang . $thn, 5);
        //echo $no_pengajuan;
        $kode_pelanggan = $request->kode_pelanggan;
        $nama_pelanggan = $request->nama_pelanggan;
        $alamat_pelanggan = $request->alamat_pelanggan;
        $nik = $request->nik;
        $alamat_toko = $request->alamat_toko;
        $longitude = $request->longitude;
        $latitude = $request->latitude;
        $no_hp = $request->no_hp;
        $hari = $request->hari;
        $status_outlet = $request->status_outlet;
        $type_outlet = $request->type_outlet;
        $cara_pembayaran = $request->cara_pembayaran;
        $kepemilikan = $request->kepemilikan;
        $lama_langganan = $request->lama_langganan;
        $lama_usaha = $request->lama_usaha;
        $jaminan = $request->jaminan;
        $omset_toko = str_replace(".", "", $request->omset_toko);
        $skor = $request->skor;
        $jatuhtempo = $request->jatuhtempo;
        $jumlah = str_replace(".", "", $request->jumlah);
        $topup_tearakhir = $request->topup_terakhir;
        if (empty($topup_tearakhir)) {
            $topup_tearakhir = date("Y-m-d");
        }
        $lama_topup = $request->lama_topup;
        $jml_faktur = $request->jml_faktur;
        $histori_transaksi  = $request->histori_transaksi;
        $uraian_analisa = $request->uraian_analisa;
        $id_admin = Auth::user()->id;

        $lastlimitpelanggan = DB::table('pengajuan_limitkredit_v3')
            ->select('no_pengajuan', 'status')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->orderBy('no_pengajuan', 'desc')
            ->first();

        DB::beginTransaction();
        try {
            DB::table('pelanggan')
                ->where('kode_pelanggan', $kode_pelanggan)
                ->update([
                    'nik' => $nik,
                    'nama_pelanggan' => $nama_pelanggan,
                    'alamat_pelanggan' => $alamat_pelanggan,
                    'alamat_toko' => $alamat_toko,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'no_hp' => $no_hp,
                    'hari'  => $hari,
                    'status_outlet' => $status_outlet,
                    'type_outlet' => $type_outlet,
                    'cara_pembayaran' => $cara_pembayaran,
                    'kepemilikan' => $kepemilikan,
                    'lama_langganan' => $lama_langganan,
                    'lama_usaha' => $lama_usaha,
                    'jaminan' => $jaminan,
                    'omset_toko' => $omset_toko
                ]);

            //Hapus Pengajuan Terakhir Yang Statusnya 0
            if ($lastlimitpelanggan != null) {
                $last_no_pengajuan_pelanggan = $lastlimitpelanggan->no_pengajuan;
                if ($lastlimitpelanggan->status == 0 || $lastlimitpelanggan == null) {
                    DB::table('pengajuan_limitkredit_v3')
                        ->where('no_pengajuan', $last_no_pengajuan_pelanggan)
                        ->delete();
                    DB::table('pengajuan_limitkredit_analisa_v3')
                        ->where('no_pengajuan', $last_no_pengajuan_pelanggan)->delete();
                } else {
                    DB::table('pengajuan_limitkredit_v3')
                        ->where('no_pengajuan', $last_no_pengajuan_pelanggan)
                        ->update([
                            'cek_ajuan' => 1
                        ]);
                }
            }



            DB::table('pengajuan_limitkredit_v3')
                ->insert([
                    'no_pengajuan' => $no_pengajuan,
                    'tgl_pengajuan' => $tgl_pengajuan,
                    'kode_pelanggan' => $kode_pelanggan,
                    'last_limit' => $request->limitpel,
                    'last_omset' => $omset_toko,
                    'jumlah'  => $jumlah,
                    'jatuhtempo' => $jatuhtempo,
                    'topup_terakhir' => $topup_tearakhir,
                    'lama_topup' => $lama_topup,
                    'jml_faktur' => $jml_faktur,
                    'histori_transaksi' => $histori_transaksi,
                    'status' => 0,
                    'skor' => $skor,
                    'id_admin' => $id_admin
                ]);

            $cek_analisa = DB::table('pengajuan_limitkredit_analisa_v3')
                ->where('no_pengajuan', $no_pengajuan)
                ->where('id_user', $id_admin)->count();
            if ($cek_analisa >= 1) {
                DB::table('pengajuan_limitkredit_analisa_v3')
                    ->where('no_pengajuan', $no_pengajuan)
                    ->where('id_user', $id_admin)
                    ->update([
                        'uraian_analisa' => $uraian_analisa
                    ]);
            } else {
                DB::table('pengajuan_limitkredit_analisa_v3')
                    ->insert([
                        'no_pengajuan' => $no_pengajuan,
                        'uraian_analisa' => $uraian_analisa,
                        'id_user' => $id_admin
                    ]);
            }
            DB::commit();
            return redirect('/limitkredit')->with(['success' => 'Data Pengajuan Limit Kredit Berhasil di Simpan']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return redirect('/limitkredit')->with(['warning' => 'Data Pengajuan Limit Kredit Gagal di Simpan Hubungi Tim IT']);
        }
    }

    public function create_uraiananalisa(Request $request)
    {
        $no_pengajuan = $request->no_pengajuan;
        $id_user = Auth::user()->id;
        $uraian_analisa = DB::table('pengajuan_limitkredit_analisa_v3')
            ->where('no_pengajuan', $no_pengajuan)
            ->where('id_user', $id_user)
            ->first();
        return view('limitkredit.create_uraiananalisa', compact('no_pengajuan', 'uraian_analisa'));
    }

    public function store_uraiananalisa(Request $request)
    {
        $id_user = Auth::user()->id;
        $cek = DB::table('pengajuan_limitkredit_analisa_v3')
            ->where('no_pengajuan', $request->no_pengajuan)
            ->where('id_user', $id_user)
            ->count();
        DB::beginTransaction();
        try {
            if (empty($cek)) {
                DB::table('pengajuan_limitkredit_analisa_v3')
                    ->insert([
                        'no_pengajuan' => $request->no_pengajuan,
                        'uraian_analisa' => $request->uraian_analisa,
                        'id_user' => $id_user
                    ]);
            } else {
                DB::table('pengajuan_limitkredit_analisa_v3')
                    ->where('no_pengajuan', $request->no_pengajuan)
                    ->where('id_user', $id_user)
                    ->update([
                        'uraian_analisa' => $request->uraian_analisa
                    ]);
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Uraian Analisa Berhasil Disimpan']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return redirect('/limitkredit')->with(['warning' => 'Data Uraian Analisa Gagal di Simpan Hubungi Tim IT']);
        }
    }

    public function penyesuaian_limit(Request $request)
    {
        $no_pengajuan = $request->no_pengajuan;
        $limitkredit = DB::table('pengajuan_limitkredit_v3')->where('no_pengajuan', $no_pengajuan)->first();
        return view('limitkredit.penyesuaian', compact('no_pengajuan', 'limitkredit'));
    }

    public function updatelimit(Request $request)
    {
        $limitkredit = DB::table('pengajuan_limitkredit_v3')->where('no_pengajuan', $request->no_pengajuan)->first();
        $jumlah_rekomendasi = str_replace(".", "", $request->jumlah_rekomendasi);
        DB::beginTransaction();
        try {
            if ($request->jatuhtempo != $request->jatuhtempo_rekomendasi) {
                DB::table('pengajuan_limitkredit_v3')
                    ->where('no_pengajuan', $request->no_pengajuan)
                    ->update([
                        'jumlah_rekomendasi' => $jumlah_rekomendasi,
                        'jatuhtempo_rekomendasi' => $request->jatuhtempo_rekomendasi
                    ]);
                if ($limitkredit->status == 1) {
                    DB::table('pelanggan')->where('kode_pelanggan', $limitkredit->kode_pelanggan)
                        ->update([
                            'jatuhtempo' => $request->jatuhtempo_rekomendasi,
                            'limitpel' => $jumlah_rekomendasi
                        ]);
                }
            } else {
                DB::table('pengajuan_limitkredit_v3')
                    ->where('no_pengajuan', $request->no_pengajuan)
                    ->update([
                        'jumlah_rekomendasi' => $jumlah_rekomendasi,
                    ]);

                if ($limitkredit->status == 1) {
                    DB::table('pelanggan')->where('kode_pelanggan', $limitkredit->kode_pelanggan)
                        ->update([
                            'limitpel' => $jumlah_rekomendasi
                        ]);
                }
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Uraian Analisa Berhasil Disimpan']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return redirect('/limitkredit')->with(['warning' => 'Data Uraian Analisa Gagal di Simpan Hubungi Tim IT']);
        }
    }

    public function approve($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $limitkredit = DB::table('pengajuan_limitkredit_v3')->where('no_pengajuan', $no_pengajuan)->first();
        $kode_pelanggan = $limitkredit->kode_pelanggan;
        $cekpelanggan = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->first();
        $kode_cabang = $cekpelanggan->kode_cabang;
        $jumlah = $limitkredit->jumlah;
        $jatuhtempo = $limitkredit->jatuhtempo;
        $jumlah_rekomendasi = $limitkredit->jumlah_rekomendasi;
        $jatuhtempo_rekomendasi = $limitkredit->jatuhtempo_rekomendasi;
        $id_admin = Auth::user()->id;
        $level = Auth::user()->level;
        $time = date("Y-m-d H:i");
        $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB');
        $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
        if (empty($jumlah_rekomendasi) && empty($jatuhtempo_rekomendasi)) {
            if (!empty($jatuhtempo)) {
                $data = [
                    'limitpel' => $jumlah,
                    'jatuhtempo' => $jatuhtempo
                ];
            } else {
                $data = [
                    'limitpel' => $jumlah
                ];
            }
        } else if (!empty($jumlah_rekomendasi) && empty($jatuhtempo_rekomendasi)) {
            if (!empty($jatuhtempo)) {
                $data = [
                    'limitpel' => $jumlah_rekomendasi,
                    'jatuhtempo' => $jatuhtempo
                ];
            } else {
                $data = [
                    'limitpel' => $jumlah_rekomendasi
                ];
            }
        } else if (empty($jumlah_rekomendasi) && !empty($jatuhtempo_rekomendasi)) {
            $data = [
                'limitpel' => $jumlah,
                'jatuhtempo' => $jatuhtempo_rekomendasi
            ];
        } else if (!empty($jumlah_rekomendasi) && !empty($jatuhtempo_rekomendasi)) {
            $data = [
                'limitpel' => $jumlah_rekomendasi,
                'jatuhtempo' => $jatuhtempo_rekomendasi
            ];
        }

        if ($level == 'kepala cabang' || $level == 'kepala penjualan') {
            $lv = 'kacab';
            if ($jumlah <= 5000000) {
                $status = 1;
            } else {
                $status = 0;
            }
        } else if ($level == 'rsm') {
            $lv = 'rsm';
            if ($jumlah <= 10000000) {
                $status = 1;
            } else {
                $status = 0;
            }
        } else if ($level == 'manager marketing') {
            $lv = 'mm';
            if ($jumlah <= 15000000) {
                $status = 1;
            } else {
                $status = 0;
            }
        } else if ($level == 'direktur') {
            $lv = 'dirut';
            $status = 1;
        }

        if ($level == "direktur") {
            $field_time = 'time_dirut';
        } else if ($level == "rsm") {
            $field_time = 'time_rsm';
        } else if ($level == "manager marketing") {
            $field_time = 'time_mm';
        } else if ($level == "general manager") {
            $field_time = 'time_gm';
        } else if ($level == "kepala cabang" || $level == "kepala penjualan") {
            $field_time = 'time_kacab';
        }

        $datastatus = [
            'status' => $status,
            $lv => $id_admin,
            'id_approval' => $id_admin,
            $field_time => $time
        ];

        $datastatus2 = [
            'status' => $status,
            'rsm' => $id_admin,
            'id_approval' => $id_admin,
            $field_time => $time
        ];

        $ceklimit = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->first();
        $penjualanpending = DB::table('penjualan')->where('kode_pelanggan', $kode_pelanggan)->where('status', 1)->get();
        //dd($penjualanpending);
        DB::beginTransaction();
        try {
            //Update Limit Kredit
            DB::table('pengajuan_limitkredit_v3')
                ->where('no_pengajuan', $no_pengajuan)
                ->update($datastatus);

            if (in_array($kode_cabang, $wilayah_timur) && $jumlah > 5000000) {
                DB::table('pengajuan_limitkredit_v3')
                    ->where('no_pengajuan', $no_pengajuan)
                    ->update($datastatus2);
            }

            //Update Pelanggan
            if ($status == 1) {
                DB::table('pelanggan')
                    ->where('kode_pelanggan', $kode_pelanggan)
                    ->update($data);
            }

            //Update Penjualan Pending
            foreach ($penjualanpending as $d) {
                $cekpiutang  = DB::table('penjualan')
                    ->select('penjualan.kode_pelanggan', DB::raw('SUM(IFNULL(penjualan.total,0) - IFNULL(retur.total,0) - IFNULL(jmlbayar,0)) AS sisapiutang'))
                    ->leftJoin(
                        DB::raw("(
                            SELECT no_fak_penj, IFNULL(SUM(bayar),0) as jmlbayar
                            FROM historibayar
                            GROUP BY no_fak_penj
                        ) historibayar"),
                        function ($join) {
                            $join->on('penjualan.no_fak_penj', '=', 'historibayar.no_fak_penj');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(
                        SELECT retur.no_fak_penj AS no_fak_penj, SUM( total ) AS total FROM retur GROUP BY retur.no_fak_penj
                    ) retur"),
                        function ($join) {
                            $join->on('penjualan.no_fak_penj', '=', 'retur.no_fak_penj');
                        }
                    )
                    ->where('penjualan.kode_pelanggan', $kode_pelanggan)
                    ->where('penjualan.no_fak_penj', '!=', $d->no_fak_penj)
                    ->groupBy('penjualan.kode_pelanggan')
                    ->first();


                $piutang = $cekpiutang->sisapiutang;
                $totalpiutang = $piutang +  $d->total;


                if ($totalpiutang <= $ceklimit->limitpel) {
                    $datapenjualan = [
                        'status' => 2
                    ];

                    DB::table('penjualan')
                        ->where('no_fak_penj', $d->no_fak_penj)
                        ->update($datapenjualan);
                }
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Setujui']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disetujui Hubungi Tim IT']);
        }
    }


    public function decline($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $limitkredit = DB::table('pengajuan_limitkredit_v3')->where('no_pengajuan', $no_pengajuan)->first();
        $kode_pelanggan = $limitkredit->kode_pelanggan;
        $tgl_pengajuan = $limitkredit->tgl_pengajuan;
        $id_admin = Auth::user()->id;
        $level = Auth::user()->level;
        $status = $limitkredit->status;
        $lastlimit = DB::table('pengajuan_limitkredit_v3')->where('tgl_pengajuan', '<', $tgl_pengajuan)->where('status', 1)->orderBy('tgl_pengajuan', 'desc')->first();

        if ($lastlimit != null) {
            $jumlah = $lastlimit->jumlah;
            $jatuhtempo = $lastlimit->jatuhtempo;
        } else {
            $jumlah = 0;
            $jatuhtempo = 0;
        }

        if (!empty($jatuhtempo)) {
            $data = [
                'limitpel' => $jumlah,
                'jatuhtempo' => $jatuhtempo
            ];
        } else {
            $data = [
                'limitpel' => $jumlah
            ];
        }

        if ($level == 'kepala cabang' || $level == "kepala penjualan") {
            $lv = 'kacab';
        } else if ($level == 'rsm') {
            $lv = 'rsm';
        } else if ($level == 'manager marketing') {
            $lv = 'mm';
        } else if ($level == 'general manager') {
            $lv = 'gm';
        } else if ($level == 'direktur') {
            $lv = 'dirut';
        }

        $datastatus = [
            'status' => 2,
            $lv => $id_admin,
            'id_approval' => $id_admin
        ];


        DB::beginTransaction();
        try {
            DB::table('pengajuan_limitkredit_v3')->where('no_pengajuan', $no_pengajuan)->update($datastatus);
            if ($status == 1 && $lastlimit == null) {
                DB::table('pelanggan')
                    ->where('kode_pelanggan', $kode_pelanggan)
                    ->update([
                        'limitpel' => $limitkredit->last_limit
                    ]);
            } elseif ($status == 1 && $lastlimit != null) {
                DB::table('pelanggan')
                    ->where('kode_Pelanggan', $kode_pelanggan)
                    ->update($data);
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Tolak']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal DI Tolak Hubungi Tim IT']);
        }
    }
}
