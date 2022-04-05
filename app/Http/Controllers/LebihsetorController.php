<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Cabang;
use App\Models\lebihsetor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LebihsetorController extends Controller
{
    public function index(Request $request)
    {
        $query = lebihsetor::query();
        $query->where('tahun', $request->tahun);
        if (!empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }
        $query->orderBy('kode_cabang');
        $query->orderBy('bulan');
        $lebihsetor = $query->get();


        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('lebihsetor.index', compact('cabang', 'bulan', 'lebihsetor'));
    }

    public function show($kode_ls)
    {
        $lebihsetor = DB::table('lebihsetor')->where('kode_ls', $kode_ls)->first();
        $detail = DB::table('lebihsetor_detail')
            ->select('lebihsetor_detail.*', 'nama_bank')
            ->join('master_bank', 'lebihsetor_detail.kode_bank', '=', 'master_bank.kode_bank')
            ->where('kode_ls', $kode_ls)
            ->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        return view('lebihsetor.show', compact('detail', 'lebihsetor', 'bulan'));
    }

    public function create()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $bank = Bank::where('show_on_cabang', 1)->orderBy('kode_bank')->get();
        return view('lebihsetor.create', compact('cabang', 'bulan', 'bank'));
    }

    public function storetemp(Request $request)
    {
        $kode_bank = $request->kode_bank;
        $kode_cabang = $request->kode_cabang;
        $tanggal_disetorkan = $request->tanggal_disetorkan;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jumlah = !empty($request->jumlah) ? str_replace(".", "", $request->jumlah) : 0;

        $data = [
            'kode_bank' => $kode_bank,
            'tanggal_disetorkan' => $tanggal_disetorkan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jumlah' => $jumlah,
            'kode_cabang' => $kode_cabang
        ];
        DB::table('lebihsetor_temp')->insert($data);
    }

    public function showtemp($kode_cabang, $bulan, $tahun)
    {
        // echo 'test';
        // die;
        $detailtemp = DB::table('lebihsetor_temp')
            ->select('lebihsetor_temp.*', 'nama_bank')
            ->join('master_bank', 'lebihsetor_temp.kode_bank', '=', 'master_bank.kode_bank')
            ->where('lebihsetor_temp.kode_cabang', $kode_cabang)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
        return view('lebihsetor.showtemp', compact('detailtemp'));
    }

    public function deletetemp(Request $request)
    {
        $hapus = DB::table('lebihsetor_temp')->where('id', $request->id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function cektemp(Request $request)
    {
        $cektemp = DB::table('lebihsetor_temp')
            ->where('kode_cabang', $request->kode_cabang)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->count();
        echo $cektemp;
    }

    public function store(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $thn = substr($tahun, 2, 2);
        $kode_ls = "LS" . $kode_cabang . $bulan . $thn;
        DB::beginTransaction();
        try {
            $data = [
                'kode_ls' => $kode_ls,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'kode_cabang' => $kode_cabang,
                'id_admin' => Auth::user()->id

            ];

            DB::table('lebihsetor')->insert($data);
            $temp = DB::table('lebihsetor_temp')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->where('kode_cabang', $kode_cabang)
                ->get();

            foreach ($temp as $d) {
                $detail = [
                    'kode_ls' => $kode_ls,
                    'tanggal_disetorkan' => $d->tanggal_disetorkan,
                    'kode_bank' => $d->kode_bank,
                    'jumlah' => $d->jumlah
                ];

                DB::table('lebihsetor_detail')->insert($detail);
            }
            DB::table('lebihsetor_temp')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->where('kode_cabang', $kode_cabang)
                ->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);;
        }
    }

    public function delete($kode_ls)
    {
        $kode_ls = Crypt::decrypt($kode_ls);
        $hapus = DB::table('lebihsetor')->where('kode_ls', $kode_ls)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus, Hubungi Tim IT']);
        }
    }
}
