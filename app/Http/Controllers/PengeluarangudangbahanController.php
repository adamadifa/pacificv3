<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pengeluarangudangbahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PengeluarangudangbahanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengeluarangudangbahan::query();

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pengeluaran', [$request->dari, $request->sampai]);
        } else {
            $query->where('tgl_pengeluaran', '>=', startreport());
        }
        lockreport($request->dari);
        if (!empty($request->nobukti_pengeluaran)) {
            $query->where('nobukti_pengeluaran', $request->nobukti_pengeluaran);
        }

        if (!empty($request->kode_dept)) {
            $query->where('kode_dept', $request->kode_dept);
        }
        $query->orderBy('tgl_pengeluaran', 'desc');
        $query->orderBy('nobukti_pengeluaran', 'desc');
        $pengeluaran = $query->paginate(15);
        $pengeluaran->appends($request->all());

        return view('pengeluarangudangbahan.index', compact('pengeluaran'));
    }

    public function show(Request $request)
    {
        $nobukti_pengeluaran = Crypt::decrypt($request->nobukti_pengeluaran);
        $pengeluaran = DB::table('pengeluaran_gb')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->first();
        $detail = DB::table('detail_pengeluaran_gb')
            ->select('detail_pengeluaran_gb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pengeluaran_gb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)->get();
        return view('pengeluarangudangbahan.show', compact('detail', 'pengeluaran'));
    }

    public function delete($nobukti_pengeluaran)
    {
        $nobukti_pengeluaran  = Crypt::decrypt($nobukti_pengeluaran);
        $hapus = DB::table('pengeluaran_gb')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dibatalkan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        $cabang = Cabang::orderBy('kode_cabang')->get();
        return view('pengeluarangudangbahan.create', compact('cabang'));
    }

    public function cektemp()
    {
        $id_admin = Auth::user()->id;
        $cek = DB::table('detailpengeluaran_temp_gb')->where('id_admin', $id_admin)->count();
        echo $cek;
    }

    public function getbarang()
    {
        $barang = DB::table('master_barang_pembelian')->where('kode_dept', 'GDB')->orderBy('kode_barang')->get();
        return view('pengeluarangudangbahan.getbarang', compact('barang'));
    }

    public function showtemp()
    {
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpengeluaran_temp_gb')
            ->select('detailpengeluaran_temp_gb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detailpengeluaran_temp_gb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('id_admin', $id_admin)->get();
        return view('pengeluarangudangbahan.showtemp', compact('detail'));
    }

    public function storetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty_unit = $request->qty_unit;
        $qty_berat = $request->qty_berat;
        $qty_lebih = $request->qty_lebih;
        $id_admin = Auth::user()->id;

        // $cek = DB::table('detailpengeluaran_temp_gb')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->count();
        // if ($cek > 0) {
        //     echo 1;
        // } else {
        $data = [
            'kode_barang' => $kode_barang,
            'keterangan' => $keterangan,
            'qty_unit' => $qty_unit,
            'qty_berat' => $qty_berat,
            'qty_lebih' => $qty_lebih,
            'id_admin' => $id_admin
        ];
        $simpan = DB::table('detailpengeluaran_temp_gb')->insert($data);
        if ($simpan) {
            echo 0;
        } else {
            echo 2;
        }
        // }
    }

    public function deletetemp(Request $request)
    {
        $id = $request->id;
        $hapus = DB::table('detailpengeluaran_temp_gb')->where('id', $id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function store(Request $request)
    {
        //$nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $tgl_pengeluaran = $request->tgl_pengeluaran;
        $tanggal = explode("-", $tgl_pengeluaran);
        $bulan = $tanggal[1];
        $tahun = $tanggal[0];
        $thn = substr($tahun, 2, 2);
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $kode_dept = $request->kode_dept;
        $unit = $request->unit;
        $kode_cabang = $request->kode_cabang;
        $pengeluaran = DB::table('pengeluaran_gb')
            ->select('nobukti_pengeluaran')
            ->whereBetween('tgl_pengeluaran', [$dari, $sampai])
            ->orderBy('nobukti_pengeluaran', 'desc')
            ->first();
        $lastnobukti_pengeluaran = $pengeluaran != null ? $pengeluaran->nobukti_pengeluaran : '';
        $nobukti_pengeluaran = buatkode($lastnobukti_pengeluaran, 'GBK/' . $bulan . $thn . "/", 3);
        if ($kode_dept == "Produksi") {
            $u = $unit;
        } else if ($kode_dept == "Cabang") {
            $u = $kode_cabang;
        } else {
            $u = null;
        }
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpengeluaran_temp_gb')->where('id_admin', $id_admin)->get();
        $cek = DB::table('pengeluaran_gb')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->count();
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Dengan No. Bukti pengeluaran' . $nobukti_pengeluaran . 'Sudah Ada']);
        } else {
            DB::beginTransaction();
            try {
                $data = [
                    'nobukti_pengeluaran' => $nobukti_pengeluaran,
                    'tgl_pengeluaran' => $tgl_pengeluaran,
                    'kode_dept' => $kode_dept,
                    'unit' => $u
                ];
                DB::table('pengeluaran_gb')->insert($data);
                foreach ($detail as $d) {
                    $datadetail = [
                        'nobukti_pengeluaran' => $nobukti_pengeluaran,
                        'kode_barang' => $d->kode_barang,
                        'keterangan' => $d->keterangan,
                        'qty_unit' => $d->qty_unit,
                        'qty_berat' => !empty($d->qty_berat) ? $d->qty_berat : 0,
                        'qty_lebih' => !empty($d->qty_lebih) ? $d->qty_lebih : 0
                    ];

                    DB::table('detail_pengeluaran_gb')->insert($datadetail);
                }
                DB::table('detailpengeluaran_temp_gb')->where('id_admin', $id_admin)->delete();
                DB::commit();
                return Redirect::back()->with(['success' => 'Data  Berhasil di Simpan']);
            } catch (\Exception $e) {
                dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data  Gagal di Simpan, Hubungi Tim IT']);
            }
        }
    }

    public function edit($nobukti_pengeluaran)
    {
        $nobukti_pengeluaran = Crypt::decrypt($nobukti_pengeluaran);
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $pengeluaran = DB::table('pengeluaran_gb')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->first();
        return view('pengeluarangudangbahan.edit', compact('pengeluaran', 'cabang'));
    }

    public function cekbarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $cek = DB::table('detail_pengeluaran_gb')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->count();
        echo $cek;
    }

    public function showbarang($nobukti_pengeluaran)
    {
        $nobukti_pengeluaran = Crypt::decrypt($nobukti_pengeluaran);
        $detail = DB::table('detail_pengeluaran_gb')
            ->select('detail_pengeluaran_gb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pengeluaran_gb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pengeluaran', $nobukti_pengeluaran)->get();
        return view('pengeluarangudangbahan.showbarang', compact('detail'));
    }

    public function editbarang(Request $request)
    {
        $id = $request->id;
        $barang = DB::table('detail_pengeluaran_gb')
            ->select('detail_pengeluaran_gb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pengeluaran_gb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('id', $id)
            ->first();

        return view('pengeluarangudangbahan.editbarang', compact('barang'));
    }

    public function updatebarang(Request $request)
    {
        $id = $request->id;
        $keterangan = $request->keterangan;
        $qty_unit = !empty($request->qty_unit) ? $request->qty_unit : 0;
        $qty_berat = !empty($request->qty_berat) ? $request->qty_berat : 0;
        $qty_lebih = !empty($request->qty_lebih) ? $request->qty_lebih : 0;
        $data = [
            'keterangan' => $keterangan,
            'qty_unit' => $qty_unit,
            'qty_berat' => $qty_berat,
            'qty_lebih' => $qty_lebih,
        ];

        $update = DB::table('detail_pengeluaran_gb')->where('id', $id)->update($data);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function deletebarang(Request $request)
    {
        $id = $request->id;

        $hapus = DB::table('detail_pengeluaran_gb')->where('id', $id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function storebarang(Request $request)
    {
        $nobukti_pengeluaran = $request->nobukti_pengeluaran;
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty_unit = !empty($request->qty_unit) ? $request->qty_unit : 0;
        $qty_berat = !empty($request->qty_berat) ? $request->qty_berat : 0;
        $qty_lebih = !empty($request->qty_lebih) ? $request->qty_lebih : 0;
        // $cek = DB::table('detail_pengeluaran_gb')->where('kode_barang', $kode_barang)->where('nobukti_pengeluaran', $nobukti_pengeluaran)->count();
        // if ($cek > 0) {
        //     echo 1;
        // } else {
        $data = [
            'nobukti_pengeluaran' => $nobukti_pengeluaran,
            'kode_barang' => $kode_barang,
            'keterangan' => $keterangan,
            'qty_unit' => $qty_unit,
            'qty_berat' => $qty_berat,
            'qty_lebih' => $qty_lebih,
        ];
        $simpan = DB::table('detail_pengeluaran_gb')->insert($data);
        if ($simpan) {
            echo 0;
        } else {
            echo 2;
        }
        // }
    }

    public function update($nobukti_pengeluaran, Request $request)
    {
        $nobukti_pengeluaran = Crypt::decrypt($nobukti_pengeluaran);
        $tgl_pengeluaran = $request->tgl_pengeluaran;
        $kode_dept = $request->kode_dept;
        $unit = $request->unit;
        $kode_cabang = $request->kode_cabang;

        if ($kode_dept == "Produksi") {
            $u = $unit;
        } else if ($kode_dept == "Cabang") {
            $u = $kode_cabang;
        } else {
            $u = null;
        }
        $data = [
            'tgl_pengeluaran' => $tgl_pengeluaran,
            'kode_dept' => $kode_dept,
            'unit' => $u
        ];

        $update = DB::table('pengeluaran_gb')->where('nobukti_pengeluaran', $nobukti_pengeluaran)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate, Hubungi Tim IT']);
        }
    }
}
