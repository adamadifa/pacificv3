<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $query->select('retur.*', 'nama_pelanggan', 'nama_karyawan', 'karyawan.kode_cabang');
        $query->orderBy('tglretur', 'desc');
        $query->orderBy('no_retur_penj', 'asc');
        $query->join('penjualan', 'retur.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan');
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
        $detail = DB::table('detailretur')
            ->select('detailretur.*', 'kode_produk', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs')
            ->join('barang', 'detailretur.kode_barang', '=', 'barang.kode_barang')
            ->where('no_retur_penj', $request->no_retur_penj)
            ->get();

        return view('worksheetom.show_monitoring_retur', compact('detail'));
    }
}
