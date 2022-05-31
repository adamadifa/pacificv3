<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Giro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class SetorangiroController extends Controller
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
        $pelanggan = '"' . $request->nama_pelanggan . '"';
        $query = Giro::query();
        $query->select('giro.no_giro', 'tgl_giro', 'nama_pelanggan', 'karyawan.kode_cabang', 'namabank', DB::raw('SUM(giro.jumlah) as jumlah'), 'tglcair', 'giro.status', 'ket', 'tglbayar', 'ledger_bank.no_bukti', 'tgl_setoranpusat');
        $query->leftJoin('historibayar', 'giro.id_giro', '=', 'historibayar.id_giro');
        $query->leftJoin('ledger_bank', 'giro.no_giro', '=', 'ledger_bank.no_ref');
        $query->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $query->leftJoin('setoran_pusat', 'giro.no_giro', '=', 'setoran_pusat.no_ref');
        $query->orderBy('tglcair', 'desc');
        $query->orderBy('nama_pelanggan', 'asc');
        $query->groupBy('giro.no_giro', 'tgl_giro', 'nama_pelanggan', 'karyawan.kode_cabang', 'namabank', 'tglcair', 'giro.status', 'ket', 'ledger_bank.no_bukti', 'tglbayar', 'tgl_setoranpusat');
        if (empty($request->no_giro) && empty($request->nama_pelanggan) && empty($request->dari) && empty($request->sampai) && $request->status === null) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }
        if (!empty($request->nama_pelanggan)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }

        if (!empty($request->no_giro)) {
            $query->where('giro.no_giro', $request->no_giro);
        }

        if ($request->status !== null) {
            $query->where('giro.status', $request->status);
        }

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tglcair', [$request->dari, $request->sampai]);
        }

        if ($this->cabang != "PCF") {
            if ($this->cabang == "GRT") {
                $query->where('karyawan.kode_cabang', 'TSM');
            } else {
                $cbg = DB::table('cabang')->where('kode_cabang', $this->cabang)->orWhere('sub_cabang', $this->cabang)->get();
                $cabang[] = "";
                foreach ($cbg as $c) {
                    $cabang[] = $c->kode_cabang;
                }
                $query->whereIn('karyawan.kode_cabang', $cabang);
            }
        }
        $giro = $query->paginate(15);
        $giro->appends($request->all());
        return view('setorangiro.index', compact('giro'));
    }

    public function create(Request $request)
    {
        $no_giro = $request->no_giro;
        $giro = DB::table('giro')
            ->select('no_giro', 'nama_pelanggan', 'namabank', DB::raw('SUM(jumlah) as jumlah'), 'tglcair')
            ->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->where('no_giro', $no_giro)
            ->groupByRaw('no_giro,nama_pelanggan,namabank,tglcair')
            ->first();
        $bank = Bank::where('show_on_cabang', 1)->get();
        return view('setorangiro.create', compact('giro', 'bank'));
    }

    public function store(Request $request)
    {
        $no_giro = $request->no_giro;
        $tgl_setoranpusat = $request->tgl_setoranpusat;
        $kode_bank = $request->kode_bank;
        $tahunini = date("y");
        $giro = DB::table('giro')
            ->select('no_giro', 'nama_pelanggan', 'namabank', 'jumlah', 'tglcair', 'tglbayar', 'karyawan.kode_cabang', 'omset_bulan', 'omset_tahun', 'giro.status')
            ->join('penjualan', 'giro.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('karyawan', 'giro.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->leftJoin('historibayar', 'giro.id_giro', '=', 'historibayar.id_giro')
            ->where('no_giro', $no_giro)
            ->groupByRaw('no_giro,nama_pelanggan,namabank,jumlah,tglcair,tglbayar,kode_cabang,omset_bulan,omset_tahun,giro.status')
            ->first();

        $omset_bulan = $giro->omset_bulan;
        $omset_tahun = $giro->omset_tahun;
        $jumlah = $request->jumlah;
        $pelanggan = $giro->nama_pelanggan;
        $status = $giro->status;
        $kode_cabang = $giro->kode_cabang;
        $tglbayar = $giro->tglbayar;

        $setoranpusat = DB::table('setoran_pusat')
            ->select('kode_setoranpusat')
            ->whereRaw('LEFT(kode_setoranpusat,4)="SB' . $tahunini . '"')
            ->orderBy('kode_setoranpusat', 'desc')
            ->first();
        $last_kode_setoranpusat = $setoranpusat->kode_setoranpusat;
        $kode_setoranpusat   = buatkode($last_kode_setoranpusat, 'SB' . $tahunini, 5);

        $data = array(
            'kode_setoranpusat' => $kode_setoranpusat,
            'tgl_setoranpusat'  => $tgl_setoranpusat,
            'kode_cabang' => $kode_cabang,
            'bank' => $kode_bank,
            'no_ref' => $no_giro,
            'giro' => $jumlah,
            'keterangan'        => "SETOR GIRO PELANGGAN " . $pelanggan,
            'status'            => '0',
            'omset_bulan'       => $omset_bulan,
            'omset_tahun'       => $omset_tahun
        );

        DB::beginTransaction();
        try {
            DB::table('setoran_pusat')->insert($data);
            if ($status == 1) {
                $dataupdate = [
                    'status' => 1,
                    'tgl_diterimapusat' => $tglbayar
                ];

                DB::table('setoran_pusat')->where('no_ref', $no_giro)->update($dataupdate);
            } else if ($status == 2) {
                $dataupdate = [
                    'status' => 2,
                    'tgl_diterimapusat' => NULL
                ];
                DB::table('setoran_pusat')->where('no_ref', $no_giro)->update($dataupdate);
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Giro Berhasil di Setorkan']);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Giro Gagal di Setorkan,  Silahkan Hubungi Tim IT']);
        }
    }

    public function delete($no_giro)
    {
        $no_giro = Crypt::decrypt($no_giro);
        $hapus = DB::table('setoran_pusat')->where('no_ref', $no_giro)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Setoran Giro Di Batalkan']);
        } else {
            return Redirect::back()->with(['warning' => 'Setoran Giro Gagal Di Batalkan']);
        }
    }
}
