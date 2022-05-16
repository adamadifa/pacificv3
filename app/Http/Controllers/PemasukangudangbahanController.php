<?php

namespace App\Http\Controllers;

use App\Models\Pemasukangudangbahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PemasukangudangbahanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemasukangudangbahan::query();
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pemasukan', [$request->dari, $request->sampai]);
        }

        if (!empty($request->nobukti_pemasukan)) {
            $query->where('nobukti_pemasukan', $request->nobukti_pemasukan);
        }
        $query->orderBy('tgl_pemasukan', 'desc');
        $pemasukan = $query->paginate(15);
        $pemasukan->appends($request->all());

        return view('pemasukangudangbahan.index', compact('pemasukan'));
    }

    public function show(Request $request)
    {
        $nobukti_pemasukan = Crypt::decrypt($request->nobukti_pemasukan);
        $pemasukan = DB::table('pemasukan_gb')->where('nobukti_pemasukan', $nobukti_pemasukan)->first();
        $detail = DB::table('detail_pemasukan_gb')
            ->select('detail_pemasukan_gb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pemasukan_gb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pemasukan', $nobukti_pemasukan)->get();
        return view('pemasukangudangbahan.show', compact('detail', 'pemasukan'));
    }

    public function delete($nobukti_pemasukan)
    {
        $nobukti_pemasukan  = Crypt::decrypt($nobukti_pemasukan);
        $hapus = DB::table('pemasukan_gb')->where('nobukti_pemasukan', $nobukti_pemasukan)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dibatalkan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dibatalkan, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        return view('pemasukangudangbahan.create');
    }

    public function cektemp()
    {
        $id_admin = Auth::user()->id;
        $cek = DB::table('detailpemasukan_temp_gb')->where('id_admin', $id_admin)->count();
        echo $cek;
    }

    public function getbarang()
    {
        $barang = DB::table('master_barang_pembelian')->where('kode_dept', 'GDB')->orderBy('kode_barang')->get();

        return view('pemasukangudangbahan.getbarang', compact('barang'));
    }

    public function showtemp()
    {
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpemasukan_temp_gb')
            ->select('detailpemasukan_temp_gb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detailpemasukan_temp_gb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('id_admin', $id_admin)->get();
        return view('pemasukangudangbahan.showtemp', compact('detail'));
    }

    public function storetemp(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty_unit = $request->qty_unit;
        $qty_berat = $request->qty_berat;
        $qty_lebih = $request->qty_lebih;
        $id_admin = Auth::user()->id;

        // $cek = DB::table('detailpemasukan_temp_gb')->where('kode_barang', $kode_barang)->where('id_admin', $id_admin)->count();
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
        $simpan = DB::table('detailpemasukan_temp_gb')->insert($data);
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
        $id_admin = Auth::user()->id;
        $hapus = DB::table('detailpemasukan_temp_gb')->where('id', $id)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }


    public function store(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $tgl_pemasukan = $request->tgl_pemasukan;
        $departemen = $request->departemen;
        $id_admin = Auth::user()->id;
        $detail = DB::table('detailpemasukan_temp_gb')->where('id_admin', $id_admin)->get();
        $cek = DB::table('pemasukan_gb')->where('nobukti_pemasukan', $nobukti_pemasukan)->count();
        if ($cek > 0) {
            return Redirect::back()->with(['warning' => 'Data Dengan No. Bukti Pemasukan' . $nobukti_pemasukan . 'Sudah Ada']);
        } else {
            DB::beginTransaction();
            try {
                $data = [
                    'nobukti_pemasukan' => $nobukti_pemasukan,
                    'tgl_pemasukan' => $tgl_pemasukan,
                    'departemen' => $departemen,
                ];
                DB::table('pemasukan_gb')->insert($data);
                foreach ($detail as $d) {
                    $datadetail = [
                        'nobukti_pemasukan' => $nobukti_pemasukan,
                        'kode_barang' => $d->kode_barang,
                        'keterangan' => $d->keterangan,
                        'qty_unit' => $d->qty_unit,
                        'qty_berat' => !empty($d->qty_berat) ? $d->qty_berat : 0,
                        'qty_lebih' => !empty($d->qty_lebih) ? $d->qty_lebih : 0
                    ];

                    DB::table('detail_pemasukan_gb')->insert($datadetail);
                }
                DB::table('detailpemasukan_temp_gb')->where('id_admin', $id_admin)->delete();
                DB::commit();
                return Redirect::back()->with(['success' => 'Data  Berhasil di Simpan']);
            } catch (\Exception $e) {
                //dd($e);
                DB::rollback();
                return Redirect::back()->with(['warning' => 'Data  Gagal di Simpan, Hubungi Tim IT']);
            }
        }
    }

    public function edit($nobukti_pemasukan)
    {
        $nobukti_pemasukan = Crypt::decrypt($nobukti_pemasukan);
        $pemasukan = DB::table('pemasukan_gb')->where('nobukti_pemasukan', $nobukti_pemasukan)->first();
        return view('pemasukangudangbahan.edit', compact('pemasukan'));
    }

    public function cekbarang(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $cek = DB::table('detail_pemasukan_gb')->where('nobukti_pemasukan', $nobukti_pemasukan)->count();
        echo $cek;
    }

    public function showbarang($nobukti_pemasukan)
    {
        $nobukti_pemasukan = Crypt::decrypt($nobukti_pemasukan);
        $detail = DB::table('detail_pemasukan_gb')
            ->select('detail_pemasukan_gb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pemasukan_gb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pemasukan', $nobukti_pemasukan)->get();
        return view('pemasukangudangbahan.showbarang', compact('detail'));
    }

    public function storebarang(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $kode_barang = $request->kode_barang;
        $keterangan = $request->keterangan;
        $qty_unit = !empty($request->qty_unit) ? $request->qty_unit : 0;
        $qty_berat = !empty($request->qty_berat) ? $request->qty_berat : 0;
        $qty_lebih = !empty($request->qty_lebih) ? $request->qty_lebih : 0;
        $cek = DB::table('detail_pemasukan_gb')->where('kode_barang', $kode_barang)->where('nobukti_pemasukan', $nobukti_pemasukan)->count();
        // if ($cek > 0) {
        //     echo 1;
        // } else {
        $data = [
            'nobukti_pemasukan' => $nobukti_pemasukan,
            'kode_barang' => $kode_barang,
            'keterangan' => $keterangan,
            'qty_unit' => $qty_unit,
            'qty_berat' => $qty_berat,
            'qty_lebih' => $qty_lebih,
        ];
        $simpan = DB::table('detail_pemasukan_gb')->insert($data);
        if ($simpan) {
            echo 0;
        } else {
            echo 2;
        }
        // }
    }
    public function editbarang(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $kode_barang = $request->kode_barang;
        $barang = DB::table('detail_pemasukan_gb')
            ->select('detail_pemasukan_gb.*', 'nama_barang', 'satuan')
            ->join('master_barang_pembelian', 'detail_pemasukan_gb.kode_barang', '=', 'master_barang_pembelian.kode_barang')
            ->where('nobukti_pemasukan', $nobukti_pemasukan)
            ->where('detail_pemasukan_gb.kode_barang', $kode_barang)
            ->first();

        return view('pemasukangudangbahan.editbarang', compact('barang'));
    }

    public function updatebarang(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $kode_barang = $request->kode_barang;
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

        $update = DB::table('detail_pemasukan_gb')->where('nobukti_pemasukan', $nobukti_pemasukan)->where('kode_barang', $kode_barang)->update($data);
        if ($update) {
            echo 0;
        } else {
            echo 2;
        }
    }

    public function deletebarang(Request $request)
    {
        $nobukti_pemasukan = $request->nobukti_pemasukan;
        $kode_barang = $request->kode_barang;
        $hapus = DB::table('detail_pemasukan_gb')->where('kode_barang', $kode_barang)->where('nobukti_pemasukan', $nobukti_pemasukan)->delete();
        if ($hapus) {
            echo 0;
        } else {
            echo 1;
        }
    }

    public function update($nobukti_pemasukan, Request $request)
    {
        $nobukti_pemasukan = Crypt::decrypt($nobukti_pemasukan);
        $tgl_pemasukan = $request->tgl_pemasukan;
        $departemen = $request->departemen;
        $data = [
            'tgl_pemasukan' => $tgl_pemasukan,
            'departemen' => $departemen,
        ];

        $update = DB::table('pemasukan_gb')->where('nobukti_pemasukan', $nobukti_pemasukan)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate, Hubungi Tim IT']);
        }
    }
}