<?php

namespace App\Http\Controllers;

use App\Models\Limitkredit;
use Barryvdh\DomPDF\Facade as PDF;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LimitkreditController extends Controller
{
    public function index(Request $request)
    {
        $pelanggan = '"' . $request->nama_pelanggan . '"';
        $query = Limitkredit::query();
        $query->select('pengajuan_limitkredit_v3.*', 'nama_pelanggan');
        $query->orderBy('tgl_pengajuan', 'desc');
        $query->orderBy('no_pengajuan', 'asc');
        $query->join('pelanggan', 'pengajuan_limitkredit_v3.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        if (empty($request->nama_pelanggan) && empty($request->dari) && empty($request->sampai) && empty($request->status)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }
        if (!empty($request->nama_pelanggan)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }


        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pengajuan', [$request->dari, $request->sampai]);
        }



        $limitkredit = $query->paginate(15);
        $limitkredit->appends($request->all());
        return view('limitkredit.index', compact('limitkredit'));
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

    public function delete($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $hapus = DB::table('pengajuan_limitkredit_v3')
            ->where('no_pengajuan', $no_pengajuan)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
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
            ->select('no_pengajuan')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->where('status', 0)
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
                DB::table('pengajuan_limitkredit_v3')
                    ->where('no_pengajuan', $last_no_pengajuan_pelanggan)
                    ->delete();
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
            dd($e);
            DB::rollback();
            return redirect('/limitkredit')->with(['warning' => 'Data Pengajuan Limit Kredit Gagal di Simpan Hubungi Tim IT']);
        }
    }
}
