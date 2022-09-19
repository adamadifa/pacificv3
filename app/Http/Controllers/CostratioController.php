<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Costratio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class CostratioController extends Controller
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
        $cbg = new Cabang();
        $cabang = $cbg->getCabanggudang($this->cabang);
        $sumber = DB::table('costratio_sumber')->orderBy('id_sumber_costratio')->get();
        $query = Costratio::query();
        $query->join('coa', 'costratio_biaya.kode_akun', '=', 'coa.kode_akun');
        $query->join('costratio_sumber', 'costratio_biaya.id_sumber_costratio', '=', 'costratio_sumber.id_sumber_costratio');
        $query->where('kode_cabang', $request->kode_cabang);

        if (!empty($request->id_sumber_costratio)) {
            $query->where('costratio_biaya.id_sumber_costratio', $request->id_sumber_costratio);
        }

        $query->whereBetween('tgl_transaksi', [$request->dari, $request->sampai]);
        $query->orderBy('tgl_transaksi');
        $query->orderBy('costratio_biaya.kode_akun');
        $costratio = $query->get();
        return view('costratio.index', compact('cabang', 'sumber', 'costratio'));
    }

    public function updatecostratio()
    {
        $backup = DB::table('back_cost')->get();
        //dd($kaskecil);

        $ceksimpan = 0;
        DB::beginTransaction();
        try {
            foreach ($backup as $d) {
                $tanggal = explode("-", $d->tgl_transaksi);
                $bulan = $tanggal[1];
                $tahun = substr($tanggal[0], 2, 2);
                $kode = "CR" . $bulan . $tahun;
                $cr = DB::table('costratio_biaya')
                    ->select('kode_cr')
                    ->whereRaw('LEFT(kode_cr,6) ="' . $kode . '"')
                    ->orderBy('kode_cr', 'desc')
                    ->first();
                if ($cr != null) {
                    $last_kode_cr = $cr->kode_cr;
                } else {
                    $last_kode_cr = "";
                }
                $kode_cr = buatkode($last_kode_cr, $kode, 4);
                echo $kode_cr . "<br>";
                $data = [
                    'kode_cr' => $kode_cr,
                    'tgl_transaksi' => $d->tgl_transaksi,
                    'kode_akun' => $d->kode_akun,
                    'keterangan' => $d->keterangan,
                    'kode_cabang' => $d->kode_cabang,
                    'id_sumber_costratio' => 3,
                    'jumlah' => $d->jumlah
                ];
                $simpan = DB::table('costratio_biaya')->insert($data);
                if ($simpan) {
                    $ceksimpan++;
                }
            }

            echo $ceksimpan . "<br>";
            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
        }
    }

    public function cetak(Request $request)
    {

        $sumber = DB::table('costratio_sumber')->where('id_sumber_costratio', $request->id_sumber_costratio)->first();
        $cabang = Cabang::where('kode_cabang', $request->kode_cabang)->first();
        $dari = $request->dari;
        $sampai = $request->sampai;
        $query = Costratio::query();
        $query->join('coa', 'costratio_biaya.kode_akun', '=', 'coa.kode_akun');
        $query->join('costratio_sumber', 'costratio_biaya.id_sumber_costratio', '=', 'costratio_sumber.id_sumber_costratio');
        $query->where('kode_cabang', $request->kode_cabang);

        if (!empty($request->id_sumber_costratio)) {
            $query->where('costratio_biaya.id_sumber_costratio', $request->id_sumber_costratio);
        }

        $query->whereBetween('tgl_transaksi', [$request->dari, $request->sampai]);
        $query->orderBy('tgl_transaksi');
        $query->orderBy('costratio_biaya.kode_akun');
        $costratio = $query->get();
        $time = date("H:i:s");
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Detail Cost Ratio $dari-$sampai-$time.xls");
        return view('costratio.cetak', compact('cabang', 'sumber', 'costratio', 'cabang', 'dari', 'sampai'));
    }
}
