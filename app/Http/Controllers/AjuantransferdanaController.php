<?php

namespace App\Http\Controllers;

use App\Models\Ajuantransferdana;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AjuantransferdanaController extends Controller
{
    public function index(Request $request)
    {

        $query = Ajuantransferdana::query();
        $query->select('*');
        if (!empty($request->nama)) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pengajuan', [$request->dari, $request->sampai]);
        }

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
}
