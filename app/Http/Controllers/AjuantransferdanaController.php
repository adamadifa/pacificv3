<?php

namespace App\Http\Controllers;

use App\Models\Ajuantransferdana;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class AjuantransferdanaController extends Controller
{
    public function index(Request $request)
    {

        $kode_cabang = Auth::user()->kode_cabang;
        $query = Ajuantransferdana::query();
        $query->select('*');
        if (!empty($request->nama_penerima)) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pengajuan', [$request->dari, $request->sampai]);
        }

        if ($kode_cabang != "PCF") {
            $query->where('kode_cabang', $kode_cabang);
        }
        $query->orderBy('tgl_pengajuan', 'desc');
        $ajuantransferdana = $query->get();
        $cbg = new Cabang();
        $cabang = $cbg->getCabanggudang(Auth::user()->kode_cabang);
        return view('ajuantransferdana.index', compact('cabang', 'ajuantransferdana'));
    }

    public function create()
    {
        $cbg = new Cabang();
        $cabang = $cbg->getCabanggudang(Auth::user()->kode_cabang);
        return view('ajuantransferdana.create', compact('cabang'));
    }

    public function store(Request $request)
    {
        $tgl_pengajuan = $request->tgl_pengajuan;
        $nama = $request->nama;
        $nama_bank = $request->nama_bank;
        $kode_cabang = $request->kode_cabang;
        $keterangan = $request->keterangan;
        $jumlah = str_replace(".", "", $request->jumlah);
        $no_rekening = $request->no_rekening;

        $tgl = explode("-", $tgl_pengajuan);
        $tahun = $tgl[0];
        $thn = substr($tahun, 2, 2);
        $lastajuan = DB::table('pengajuan_transfer_dana')
            ->select('no_pengajuan')
            ->whereRaw('YEAR(tgl_pengajuan) = "' . $tahun . '"')
            ->whereRaw('MID(no_pengajuan,4,3) = "' . $kode_cabang . '"')
            ->orderBy('no_pengajuan', 'desc')
            ->first();

        if ($lastajuan == null) {
            $last_no_pengajuan = 'PTD' . $kode_cabang . $thn . '00000';
        } else {
            $last_no_pengajuan = $lastajuan->no_pengajuan;
        }
        $no_pengajuan = buatkode($last_no_pengajuan, 'PTD' . $kode_cabang . $thn, 5);

        try {
            DB::table('pengajuan_transfer_dana')->insert([
                'no_pengajuan' => $no_pengajuan,
                'tgl_pengajuan' => $tgl_pengajuan,
                'nama' => $nama,
                'nama_bank' => $nama_bank,
                'no_rekening' => $no_rekening,
                'jumlah' => $jumlah,
                'keterangan' => $keterangan,
                'kode_cabang' => $kode_cabang,
                'created_by' => Auth::user()->id
            ]);

            return redirect('/ajuantransferdana')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return redirect('/ajuantransferdana')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function edit($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $ajuantransferdana = DB::table('pengajuan_transfer_dana')->where('no_pengajuan', $no_pengajuan)->first();
        $cbg = new Cabang();
        $cabang = $cbg->getCabanggudang(Auth::user()->kode_cabang);
        return view('ajuantransferdana.edit', compact('cabang', 'ajuantransferdana'));
    }


    public function update($no_pengajuan, Request $request)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $tgl_pengajuan = $request->tgl_pengajuan;
        $nama = $request->nama;
        $nama_bank = $request->nama_bank;
        $kode_cabang = $request->kode_cabang;
        $keterangan = $request->keterangan;
        $jumlah = str_replace(".", "", $request->jumlah);
        $no_rekening = $request->no_rekening;



        try {
            DB::table('pengajuan_transfer_dana')
                ->where('no_pengajuan', $no_pengajuan)
                ->update([
                    'tgl_pengajuan' => $tgl_pengajuan,
                    'nama' => $nama,
                    'nama_bank' => $nama_bank,
                    'no_rekening' => $no_rekening,
                    'jumlah' => $jumlah,
                    'keterangan' => $keterangan,
                    'kode_cabang' => $kode_cabang,
                ]);

            return redirect('/ajuantransferdana')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return redirect('/ajuantransferdana')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function prosesajuan($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $ajuantransferdana = DB::table('pengajuan_transfer_dana')->where('no_pengajuan', $no_pengajuan)->first();
        $cbg = new Cabang();
        $cabang = $cbg->getCabanggudang(Auth::user()->kode_cabang);
        return view('ajuantransferdana.prosesajuan', compact('cabang', 'ajuantransferdana'));
    }


    public function proses($no_pengajuan, Request $request)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        $ajuantransfer = DB::table('pengajuan_transfer_dana')->where('no_pengajuan', $no_pengajuan)->first();
        $tgl_proses = $request->tgl_proses;
        $uang_kertas = $ajuantransfer->jumlah;
        $kode_cabang = $ajuantransfer->kode_cabang;
        $tanggal = explode("-", $tgl_proses);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $tahunini = date("y");
        $setoranpusat = DB::table('setoran_pusat')
            ->select('kode_setoranpusat')
            ->whereRaw('LEFT(kode_setoranpusat,4)="SB' . $tahunini . '"')
            ->orderBy('kode_setoranpusat', 'desc')
            ->first();
        $last_kode_setoranpusat = $setoranpusat != null ? $setoranpusat->kode_setoranpusat : '';
        $kode_setoranpusat   = buatkode($last_kode_setoranpusat, 'SB' . $tahunini, 5);
        $data = [
            'kode_setoranpusat' => $kode_setoranpusat,
            'tgl_setoranpusat'  => $tgl_proses,
            'kode_cabang' => $kode_cabang,
            'bank' => 'KAS',
            'uang_kertas' => $uang_kertas,
            'uang_logam' => 0,
            'keterangan' => 'Transfer Ke Pihak Ke 3',
            'status' => '0',
            'no_pengajuan' => $no_pengajuan
        ];
        if ($bulan == 12) {
            $bulan = 1;
            $tahun = $tahun + 1;
        } else {
            $bulan = $bulan + 1;
            $tahun = $tahun;
        }
        $ceksaldo = DB::table('saldoawal_kasbesar')->where('bulan', $bulan)->where('tahun', $tahun)->where('kode_cabang', $kode_cabang)->count();
        DB::beginTransaction();
        try {

            if (empty($ceksaldo)) {
                //Simpan Pengajuan Transfer Dana
                DB::table('pengajuan_transfer_dana')->where('no_pengajuan', $no_pengajuan)
                    ->update([
                        'tgl_proses' => $tgl_proses,
                        'proses_by' => Auth::user()->id
                    ]);
                // Simpan Setoran Pusat
                DB::table('setoran_pusat')->insert($data);
                DB::commit();
                return Redirect::back()->with(['success' => 'Data Berhasil di Proses']);
            } else {
                DB::rollBack();
                return Redirect::back()->with(['warning' => 'Periode Laporan Sudah Ditutup']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal di Proses']);
        }
    }

    public function batalkan($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        DB::beginTransaction();
        try {
            DB::table('pengajuan_transfer_dana')->where('no_pengajuan', $no_pengajuan)
                ->update([
                    'tgl_proses' => NULL,
                    'proses_by' => NULL
                ]);
            DB::table('setoran_pusat')->where('no_pengajuan', $no_pengajuan)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil di Proses']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal di Proses']);
        }
    }

    public function delete($no_pengajuan)
    {
        $no_pengajuan = Crypt::decrypt($no_pengajuan);
        try {
            DB::table('pengajuan_transfer_dana')->where('no_pengajuan', $no_pengajuan)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => 'Data Gagal Disimpan']);
        }
    }
}
