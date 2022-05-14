<?php

namespace App\Http\Controllers;

use App\Models\Detailsaldoawalbb;
use App\Models\Saldoawalbukubesar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SaldoawalbukubesarController extends Controller
{
    public function index(Request $request)
    {
        $query = Saldoawalbukubesar::query();
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }

        if (!empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
        }


        $query->select('saldoawal_bb.*');
        $query->orderBy('tahun', 'desc');
        $query->orderBy('bulan', 'asc');
        $saldoawal = $query->paginate(15);
        $saldoawal->appends($request->all());
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalbukubesar.index', compact('bulan', 'saldoawal'));
    }

    public function delete($kode_saldoawal)
    {
        $kode_saldoawal = Crypt::decrypt($kode_saldoawal);
        $hapus = DB::table('saldoawal_bb')->where('kode_saldoawal_bb', $kode_saldoawal)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalbukubesar.create', compact('bulan'));
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

        $dari = $tahunlalu . "-" . $bulanlalu . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $ceksaldo = DB::table('saldoawal_bb')->where('bulan', $bulanlalu)->where('tahun', $tahunlalu)->count();
        $ceknow = DB::table('saldoawal_bb')->where('bulan', $bulan)->where('tahun', $tahun)->count();
        $cekall = DB::table('saldoawal_bb')->count();
        if (empty($ceksaldo) && !empty($cekall) || !empty($ceknow)) {
            echo "1";
        } else {
            $detail = DB::table('coa')
                ->select(
                    'coa.kode_akun',
                    'nama_akun',
                    'parrent.sub_akun as parent',
                    'coa.sub_akun',
                    'saldoawal',
                    'jmlmutasi',
                    DB::raw('IFNULL(saldoawal,0) + IFNULL(jmlmutasi,0) as saldoakhir')
                )
                ->leftJoin(
                    DB::raw("(
                        SELECT kode_akun,jumlah as saldoawal
                        FROM detailsaldoawal_bb
                        INNER JOIN saldoawal_bb ON detailsaldoawal_bb.kode_saldoawal_bb = saldoawal_bb.kode_saldoawal_bb
                        WHERE bulan = '$bulanlalu' AND tahun ='$tahunlalu'
                    ) sa"),
                    function ($join) {
                        $join->on('coa.kode_akun', '=', 'sa.kode_akun');
                    }
                )

                ->leftJoin(
                    DB::raw("(
                        SELECT kode_akun,SUM(debet-kredit) as jmlmutasi
                        FROM buku_besar
                        WHERE tanggal BETWEEN '$dari' AND '$sampai'
                        GROUP BY kode_akun
                    ) mutasi"),
                    function ($join) {
                        $join->on('coa.kode_akun', '=', 'mutasi.kode_akun');
                    }
                )

                ->leftJoin(
                    DB::raw("(
                        SELECT sub_akun
                        FROM coa
                        GROUP BY sub_akun
                    ) parrent"),
                    function ($join) {
                        $join->on('coa.kode_akun', '=', 'parrent.sub_akun');
                    }
                )
                ->orderBy('kode_akun')
                ->get();
            return view('saldoawalbukubesar.getdetailsaldo', compact('detail'));
        }
    }

    public function store(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $thn = substr($tahun, 2, 2);
        $kode_saldoawal = "GJ" . $bulan . $thn;
        $tanggal = $request->tanggal;

        $kode_akun = $request->kode_akun;
        $jumlah = $request->jumlah;
        $data = [
            'kode_saldoawal_bb' => $kode_saldoawal,
            'tanggal'           => $tanggal,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
        ];

        for ($i = 0; $i < count($kode_akun); $i++) {
            if (!empty($jumlah[$i])) {
                $detail_saldo[]   = [
                    'kode_saldoawal_bb' => $kode_saldoawal,
                    'kode_akun' => $kode_akun[$i],
                    'jumlah' => $jumlah[$i]
                ];
            }
        }


        //dd($detail_saldo);

        //dd($chunks);
        DB::beginTransaction();
        try {
            DB::table('saldoawal_bb')->insert($data);
            $chunks = array_chunk($detail_saldo, 50);
            foreach ($chunks as $chunk) {
                Detailsaldoawalbb::insert($chunk);
            }
            DB::commit();
            return redirect('/saldoawalbb?tahun=' . $tahun)->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect('/saldoawalbb?tahun=' . $tahun)->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }

    public function show($kode_saldoawal)
    {
        $saldoawal = DB::table('saldoawal_bb')->where('kode_saldoawal_bb', $kode_saldoawal)->first();
        $detail = DB::table('coa')
            ->select(
                'coa.kode_akun',
                'nama_akun',
                'parrent.sub_akun as parent',
                'coa.sub_akun',
                'saldoawal'
            )
            ->leftJoin(
                DB::raw("(
                SELECT kode_akun,jumlah as saldoawal
                FROM detailsaldoawal_bb
                WHERE kode_saldoawal_bb = '$kode_saldoawal'
            ) sa"),
                function ($join) {
                    $join->on('coa.kode_akun', '=', 'sa.kode_akun');
                }
            )
            ->leftJoin(
                DB::raw("(
                    SELECT sub_akun
                    FROM coa
                    GROUP BY sub_akun
                ) parrent"),
                function ($join) {
                    $join->on('coa.kode_akun', '=', 'parrent.sub_akun');
                }
            )
            ->orderBy('kode_akun')
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('saldoawalbukubesar.show', compact('detail', 'saldoawal', 'bulan'));
    }
}