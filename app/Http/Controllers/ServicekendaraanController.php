<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Servicekendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Stmt\TryCatch;

class ServicekendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Servicekendaraan::query();

        if (isset($request->no_polisi)) {
            $query->where('no_polisi', '%');
        }
        $query->select('no_invoice', 'kendaraan_service.no_polisi', 'merk', 'tipe', 'tipe_kendaraan', 'tgl_service', 'nama_bengkel', 'kendaraan_service.kode_cabang');
        $query->join('kendaraan', 'kendaraan_service.no_polisi', '=', 'kendaraan.no_polisi');
        $query->join('bengkel', 'kendaraan_service.kode_bengkel', '=', 'bengkel.kode_bengkel');
        $service = $query->paginate(15);
        $service->appends($request->all());

        $kendaraan = Kendaraan::orderBy('no_polisi')->get();
        return view('servicekendaraan.index', compact('service', 'kendaraan'));
    }


    public function create()
    {
        $kendaraan = Kendaraan::orderBy('no_polisi')->get();
        $bengkel = DB::table('bengkel')->get();
        return view('servicekendaraan.create', compact('kendaraan', 'bengkel'));
    }

    public function getitemservice()
    {
        $itemservice = DB::table('kendaraan_service_item')->orderBy('kode_item', 'desc')->get();
        echo "<option value=''>Pilih Item</option>";
        foreach ($itemservice as $d) {
            echo "<option value='$d->kode_item'>" . $d->kode_item . " " . $d->nama_item . "</option>";
        }
    }

    public function storeitemservice(Request $request)
    {
        $nama_item = $request->nama_item;
        $jenis = $request->jenis;
        $item = DB::table("kendaraan_service_item")
            ->orderBy("kode_item", "desc")
            ->first();

        $lastkodeitem = $item != null ? $item->kode_item : '';

        $kode_item  = buatkode($lastkodeitem, "SV", 4);
        $data = [
            'kode_item' => $kode_item,
            'nama_item' => $nama_item,
            'jenis' => $jenis
        ];
        $simpan = DB::table('kendaraan_service_item')->insert($data);
        if ($simpan) {
            echo 1;
        } else {
            echo 2;
        }
    }

    public function storetemp(Request $request)
    {
        $no_invoice = $request->no_invoice;
        $kode_item = $request->kode_item;
        $qty = $request->qty;
        $harga = str_replace(".", "", $request->harga);

        $data = [
            'no_invoice' => $no_invoice,
            'kode_item' => $kode_item,
            'qty' => $qty,
            'harga' => $harga
        ];
        $cek = DB::table('kendaraan_service_detail_temp')->where('no_invoice', $no_invoice)->where('kode_item', $kode_item)->count();

        if ($cek > 0) {
            echo 3;
        } else {
            $simpan = DB::table('kendaraan_service_detail_temp')->insert($data);
            if ($simpan) {
                echo 1;
            } else {
                echo 2;
            }
        }
    }

    public function showtemp(Request $request)
    {
        $no_invoice = $request->no_invoice;

        $temp = DB::table('kendaraan_service_detail_temp')
            ->selectRaw('no_invoice,kendaraan_service_detail_temp.kode_item,nama_item,qty,harga')
            ->join('kendaraan_service_item', 'kendaraan_service_detail_temp.kode_item', '=', 'kendaraan_service_item.kode_item')
            ->where('no_invoice', $no_invoice)->get();

        return view('servicekendaraan.showtemp', compact('temp'));
    }

    public function deletetemp(Request $request)
    {
        $no_invoice = $request->no_invoice;
        $kode_item = $request->kode_item;
        $hapus = DB::table('kendaraan_service_detail_temp')->where('no_invoice', $no_invoice)->where('kode_item', $kode_item)->delete();
        if ($hapus) {
            echo 1;
        } else {
            echo 2;
        }
    }


    public function getbengkel()
    {
        $bengkel = DB::table('bengkel')->orderBy('kode_bengkel', 'desc')->get();
        echo "<option value=''>Pilih Bengkel</option>";
        foreach ($bengkel as $d) {
            echo "<option value='$d->kode_bengkel'>" . $d->kode_bengkel . " " . $d->nama_bengkel . "</option>";
        }
    }

    public function storenewbengkel(Request $request)
    {
        $nama_bengkel = $request->nama_bengkel;
        $bengkel = DB::table("bengkel")
            ->orderBy("kode_bengkel", "desc")
            ->first();

        $lastkode = $bengkel != null ? $bengkel->kode_bengkel : '';

        $kode_bengkel  = buatkode($lastkode, "BK", 4);
        $data = [
            'kode_bengkel' => $kode_bengkel,
            'nama_bengkel' => $nama_bengkel
        ];
        $simpan = DB::table('bengkel')->insert($data);
        if ($simpan) {
            echo 1;
        } else {
            echo 2;
        }
    }

    public function store(Request $request)
    {
        $no_invoice = $request->no_invoice;
        $tgl_service = $request->tgl_service;
        $no_polisi = $request->no_polisi;
        $kode_bengkel = $request->kode_bengkel;
        $kendaraan = DB::table('kendaraan')->where('no_polisi', $no_polisi)->first();
        $kode_cabang = $kendaraan->kode_cabang;
        $temp = DB::table('kendaraan_service_detail_temp')->where('no_invoice', $no_invoice);
        $cektemp = $temp->count();
        $datatemp = $temp->get();

        $data = [
            'no_invoice' => $no_invoice,
            'tgl_service' => $tgl_service,
            'no_polisi' => $no_polisi,
            'kode_bengkel' => $kode_bengkel,
            'kode_cabang' => $kode_cabang
        ];

        if ($cektemp == 0) {
            return Redirect::back()->with(['warning' => 'Data Item Masih Kosong']);
        } else {
            DB::beginTransaction();
            try {
                DB::table('kendaraan_service')->insert($data);
                foreach ($datatemp as $d) {
                    $datatmp = [
                        'no_invoice' => $no_invoice,
                        'kode_item' => $d->kode_item,
                        'qty' => $d->qty,
                        'harga' => $d->harga
                    ];

                    DB::table('kendaraan_service_detail')->insert($datatmp);
                }

                DB::table('kendaraan_service_detail_temp')->where('no_invoice', $no_invoice)->delete();
                DB::commit();
                return redirect('/servicekendaraan')->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                die;
                DB::rollBack();
                return redirect('/servicekendaraan')->with(['warning' => 'Data Gagal  Disimpan']);
            }
        }
    }

    public function delete($no_invoice)
    {
        $no_invoice = Crypt::decrypt($no_invoice);
        $hapus = DB::table('kendaraan_service')->where('no_invoice', $no_invoice)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data gagal Dihapus']);
        }
    }

    public function show(Request $request)
    {
        $no_invoice = $request->no_invoice;
        $service = DB::table('kendaraan_service')
            ->join('bengkel', 'kendaraan_service.kode_bengkel', '=', 'bengkel.kode_bengkel')
            ->where('no_invoice', $no_invoice)->first();
        $detail = DB::table('kendaraan_service_detail')
            ->select('kendaraan_service_detail.kode_item', 'nama_item', 'qty', 'harga')
            ->join('kendaraan_service_item', 'kendaraan_service_detail.kode_item', '=', 'kendaraan_service_item.kode_item')
            ->where('no_invoice', $no_invoice)
            ->get();
        return view('servicekendaraan.show', compact('service', 'detail'));
    }
}
