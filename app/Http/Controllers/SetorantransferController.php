<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class SetorantransferController extends Controller
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
        $query = Transfer::query();
        $query->select(
            'kode_transfer',
            'tgl_transfer',
            'nama_pelanggan',
            'karyawan.kode_cabang',
            'namabank',
            DB::raw('SUM(transfer.jumlah) as jumlah'),
            'tglcair',
            'transfer.status',
            'ket',
            'tglbayar',
            'ledger_bank.no_bukti',
            'tgl_setoranpusat'
        );

        $query->leftJoin('historibayar', 'transfer.id_transfer', '=', 'historibayar.id_transfer');
        $query->leftJoin('ledger_bank', 'transfer.kode_transfer', '=', 'ledger_bank.no_ref');
        $query->join('penjualan', 'transfer.no_fak_penj', '=', 'penjualan.no_fak_penj');
        $query->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $query->leftJoin('setoran_pusat', 'transfer.kode_transfer', '=', 'setoran_pusat.no_ref');
        $query->orderBy('tglcair', 'desc');
        $query->orderBy('nama_pelanggan', 'asc');
        $query->groupBy('transfer.kode_transfer', 'tgl_transfer', 'nama_pelanggan', 'karyawan.kode_cabang', 'namabank', 'tglcair', 'transfer.status', 'ket', 'ledger_bank.no_bukti', 'tglbayar', 'tgl_setoranpusat');
        if (empty($request->kode_transfer) && empty($request->nama_pelanggan) && empty($request->dari) && empty($request->sampai) && $request->status === null) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }
        if (!empty($request->nama_pelanggan)) {
            $query->WhereRaw("MATCH(nama_pelanggan) AGAINST('" . $pelanggan .  "')");
        }

        if ($request->status !== null) {
            $query->where('transfer.status', $request->status);
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
        $transfer = $query->paginate(15);
        $transfer->appends($request->all());
        return view('setorantransfer.index', compact('transfer'));
    }


    public function create(Request $request)
    {
        $kode_transfer = $request->kode_transfer;
        $transfer = DB::table('transfer')
            ->select('kode_transfer', 'nama_pelanggan', 'namabank', 'jumlah', 'tglcair')
            ->join('penjualan', 'transfer.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->where('kode_transfer', $kode_transfer)
            ->groupByRaw('kode_transfer,nama_pelanggan,namabank,jumlah,tglcair')
            ->first();
        $bank = Bank::where('show_on_cabang', 1)->get();
        return view('setorantransfer.create', compact('transfer', 'bank'));
    }

    public function store(Request $request)
    {
        $kode_transfer = $request->kode_transfer;
        $tgl_setoranpusat = $request->tgl_setoranpusat;
        $kode_bank = $request->kode_bank;
        $tahunini = date("y");
        $transfer = DB::table('transfer')
            ->select('kode_transfer', 'nama_pelanggan', 'namabank', 'jumlah', 'tglcair', 'tglbayar', 'karyawan.kode_cabang', 'omset_bulan', 'omset_tahun', 'transfer.status')
            ->join('penjualan', 'transfer.no_fak_penj', '=', 'penjualan.no_fak_penj')
            ->join('karyawan', 'transfer.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->leftJoin('historibayar', 'transfer.id_transfer', '=', 'historibayar.id_transfer')
            ->where('kode_transfer', $kode_transfer)
            ->groupByRaw('kode_transfer,nama_pelanggan,namabank,jumlah,tglcair,tglbayar,kode_cabang,omset_bulan,omset_tahun,transfer.status')
            ->first();

        $omset_bulan = $transfer->omset_bulan;
        $omset_tahun = $transfer->omset_tahun;
        $jumlah = $request->jumlah;
        $pelanggan = $transfer->nama_pelanggan;
        $status = $transfer->status;
        $kode_cabang = $transfer->kode_cabang;
        $tglbayar = $transfer->tglbayar;

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
            'no_ref' => $kode_transfer,
            'transfer' => $jumlah,
            'keterangan' => "SETOR TRANSFER PELANGGAN " . $pelanggan,
            'status' => '0',
            'omset_bulan' => $omset_bulan,
            'omset_tahun' => $omset_tahun
        );

        DB::beginTransaction();
        try {
            DB::table('setoran_pusat')->insert($data);
            if ($status == 1) {
                $dataupdate = [
                    'status' => 1,
                    'tgl_diterimapusat' => $tglbayar
                ];

                DB::table('setoran_pusat')->where('no_ref', $kode_transfer)->update($dataupdate);
            } else if ($status == 2) {
                $dataupdate = [
                    'status' => 2,
                    'tgl_diterimapusat' => NULL
                ];
                DB::table('setoran_pusat')->where('no_ref', $kode_transfer)->update($dataupdate);
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Transfer Berhasil di Setorkan']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Transfer Gagal di Setorkan,  Silahkan Hubungi Tim IT']);
        }
    }

    public function delete($kode_transfer)
    {
        $kode_transfer = Crypt::decrypt($kode_transfer);
        $hapus = DB::table('setoran_pusat')->where('no_ref', $kode_transfer)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Setoran Transfer Di Batalkan']);
        } else {
            return Redirect::back()->with(['warning' => 'Setoran Transfer Gagal Di Batalkan']);
        }
    }
}