<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pelanggan;
use App\Models\Salesman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::query();
        if (isset($request->submit) || isset($request->export)) {
            if ($request->nama != "") {
                $query->where('nama_pelanggan', 'like', '%' . $request->nama . '%');
            }

            if ($request->kode_cabang != "") {
                $query->where('pelanggan.kode_cabang', $request->kode_cabang);
            }

            if ($request->id_karyawan != "") {
                $query->where('pelanggan.id_sales', $request->id_karyawan);
            }

            if ($request->status_pelanggan != "") {
                $query->where('pelanggan.status_pelanggan', $request->status_pelanggan);
            }

            if ($request->dari != "" && $request->sampai != "") {
                $query->whereBetween('pelanggan.time_stamps', [$request->dari, $request->sampai]);
            }
        }
        $query->select('pelanggan.*', 'nama_karyawan');
        $query->orderBy('status_pelanggan', 'desc');
        $query->orderBy('nama_pelanggan', 'asc');
        $query->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        if (isset($request->export)) {
            $pelanggan = $query->get();
        } else {

            $pelanggan = $query->paginate(15);
            $pelanggan->appends($request->all());
        }


        $query2 = Pelanggan::query();
        if (isset($request->submit)) {
            if ($request->nama != "") {
                $query2->where('nama_pelanggan', 'like', '%' . $request->nama . '%');
            }

            if ($request->kode_cabang != "") {
                $query2->where('pelanggan.kode_cabang', $request->kode_cabang);
            }

            if ($request->id_karyawan != "") {
                $query2->where('pelanggan.id_sales', $request->id_karyawan);
            }

            if ($request->status_pelanggan != "") {
                $query2->where('pelanggan.status_pelanggan', $request->status_pelanggan);
            }

            if ($request->dari != "" && $request->sampai != "") {
                $query2->whereBetween('pelanggan.time_stamps', [$request->dari, $request->sampai]);
            }
        }
        $query2->select('pelanggan.*', 'nama_karyawan');
        $query2->orderBy('status_pelanggan', 'desc');
        $query2->orderBy('nama_pelanggan', 'asc');
        $query2->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');

        $queryaktif = Pelanggan::query();
        if (isset($request->submit)) {
            if ($request->nama != "") {
                $queryaktif->where('nama_pelanggan', 'like', '%' . $request->nama . '%');
            }

            if ($request->kode_cabang != "") {
                $queryaktif->where('pelanggan.kode_cabang', $request->kode_cabang);
            }

            if ($request->id_karyawan != "") {
                $queryaktif->where('pelanggan.id_sales', $request->id_karyawan);
            }

            if ($request->dari != "" && $request->sampai != "") {
                $queryaktif->whereBetween('pelanggan.time_stamps', [$request->dari, $request->sampai]);
            }
        }
        $queryaktif->select('pelanggan.*', 'nama_karyawan');
        $queryaktif->orderBy('status_pelanggan', 'desc');
        $queryaktif->orderBy('nama_pelanggan', 'asc');
        $queryaktif->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $queryaktif->where('pelanggan.status_pelanggan', 1);


        $querynonaktif = Pelanggan::query();
        if (isset($request->submit)) {
            if ($request->nama != "") {
                $querynonaktif->where('nama_pelanggan', 'like', '%' . $request->nama . '%');
            }

            if ($request->kode_cabang != "") {
                $querynonaktif->where('pelanggan.kode_cabang', $request->kode_cabang);
            }

            if ($request->id_karyawan != "") {
                $querynonaktif->where('pelanggan.id_sales', $request->id_karyawan);
            }

            if ($request->dari != "" && $request->sampai != "") {
                $querynonaktif->whereBetween('pelanggan.time_stamps', [$request->dari, $request->sampai]);
            }
        }
        $querynonaktif->select('pelanggan.*', 'nama_karyawan');
        $querynonaktif->orderBy('status_pelanggan', 'desc');
        $querynonaktif->orderBy('nama_pelanggan', 'asc');
        $querynonaktif->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan');
        $querynonaktif->where('pelanggan.status_pelanggan', 0);

        $jmlpelanggan = $query2->count();
        $jmlaktif = $queryaktif->count();
        $jmlnonaktif = $querynonaktif->count();
        $cabang = Cabang::all();
        if (isset($request->export)) {
            header("Content-type: application/vnd-ms-excel");
            // Mendefinisikan nama file ekspor "hasil-export.xls"
            header("Content-Disposition: attachment; filename=Data Pelanggan.xls");
            $cbg = $request->kode_cabang;
            $id_karyawan = $request->id_karyawan;
            $salesman = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->first();
            $dari = $request->dari;
            $sampai = $request->sampai;

            return view('pelanggan.export', compact('pelanggan', 'cbg', 'salesman', 'dari', 'sampai'));
        } else {
            return view('pelanggan.index', compact('pelanggan', 'cabang', 'jmlpelanggan', 'jmlaktif', 'jmlnonaktif'));
        }
    }

    public function create()
    {
        $cabang = Cabang::all();
        return view('pelanggan.create', compact('cabang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required',
            'alamat_pelanggan' => 'required',
            'alamat_toko' => 'required',
            'no_hp' => 'required|numeric',
            'pasar' => 'required',
            'hari' => 'required',
            'kode_cabang' => 'required',
            'id_karyawan' => 'required',
            'limitpel' => 'required',
            'jatuhtempo' => 'required',
            'status_pelanggan' => 'required',

        ]);

        $pelanggan = DB::table('pelanggan')
            ->select('kode_pelanggan')
            ->where('kode_cabang', $request->kode_cabang)
            ->whereRaw('LEFT(kode_pelanggan,3) = "' . $request->kode_cabang . '"')
            ->orderBy('kode_pelanggan', 'desc')
            ->first();

        $kodepelangganterakhir = $pelanggan->kode_pelanggan;
        $kodepelanggan = buatkode($kodepelangganterakhir, $request->kode_cabang . '-', 5);

        $simpan = DB::table('pelanggan')->insert([
            'kode_pelanggan' => $kodepelanggan,
            'nik' => $request->nik,
            'no_kk' => $request->no_kk,
            'nama_pelanggan' => $request->nama_pelanggan,
            'tgl_lahir' => $request->tgl_lahir,
            'alamat_pelanggan' => $request->alamat_pelanggan,
            'alamat_toko' => $request->alamat_toko,
            'no_hp' => $request->no_hp,
            'hari' => $request->hari,
            'pasar' => $request->pasar,
            'kode_cabang' => $request->kode_cabang,
            'id_sales' => $request->id_karyawan,
            'limitpel' => $request->limitpel,
            'jatuhtempo' => $request->jatuhtempo,
            'status_pelanggan' => $request->status_pelanggan,
            'kepemilikan' => $request->kepemilikan,
            'lama_usaha' => $request->lama_usaha,
            'status_outlet' => $request->status_outlet,
            'type_outlet' => $request->type_outlet,
            'cara_pembayaran' => $request->cara_pembayaran,
            'lama_langganan' => $request->lama_langganan,
            'jaminan' => $request->jaminan,
            'omset_toko' => $request->omset_toko
        ]);

        if ($simpan) {
            return redirect('/pelanggan')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/pelanggan')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function edit($kode_pelanggan)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $data = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)->first();
        $cabang = Cabang::all();
        return view('pelanggan.edit', compact('data', 'cabang'));
    }

    public function delete($kode_pelanggan)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $hapus = DB::table('pelanggan')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->delete();

        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function update(Request $request, $kode_pelanggan)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $request->validate([
            'nama_pelanggan' => 'required',
            'alamat_pelanggan' => 'required',
            'alamat_toko' => 'required',
            'no_hp' => 'required|numeric',
            'pasar' => 'required',
            'hari' => 'required',
            'kode_cabang' => 'required',
            'id_karyawan' => 'required',
            'limitpel' => 'required',
            'jatuhtempo' => 'required',
            'status_pelanggan' => 'required',

        ]);



        $simpan = DB::table('pelanggan')
            ->where('kode_pelanggan', $kode_pelanggan)
            ->update([
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'nama_pelanggan' => $request->nama_pelanggan,
                'tgl_lahir' => $request->tgl_lahir,
                'alamat_pelanggan' => $request->alamat_pelanggan,
                'alamat_toko' => $request->alamat_toko,
                'no_hp' => $request->no_hp,
                'hari' => $request->hari,
                'pasar' => $request->pasar,
                'kode_cabang' => $request->kode_cabang,
                'id_sales' => $request->id_karyawan,
                'limitpel' => str_replace(".", "", $request->limitpel),
                'jatuhtempo' => $request->jatuhtempo,
                'status_pelanggan' => $request->status_pelanggan,
                'kepemilikan' => $request->kepemilikan,
                'lama_usaha' => $request->lama_usaha,
                'status_outlet' => $request->status_outlet,
                'type_outlet' => $request->type_outlet,
                'cara_pembayaran' => $request->cara_pembayaran,
                'lama_langganan' => $request->lama_langganan,
                'jaminan' => $request->jaminan,
                'omset_toko' => str_replace(".", "", $request->omset_toko)
            ]);

        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Updat']);
        }
    }

    public function show($kode_pelanggan)
    {
        $kode_pelanggan = Crypt::decrypt($kode_pelanggan);
        $data = DB::table('pelanggan')->where('kode_pelanggan', $kode_pelanggan)
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->join('cabang', 'pelanggan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        return view('pelanggan.show', compact('data'));
    }
}
