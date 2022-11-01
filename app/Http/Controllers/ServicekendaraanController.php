<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Servicekendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicekendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Servicekendaraan::query();

        if (isset($request->no_polisi)) {
            $query->where('no_polisi', '%');
        }
        $query->select('*');
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
        $harga = $request->harga;

        $data = [
            'no_invoice' => $no_invoice,
            'kode_item' => $kode_item,
            'qty' => $qty,
            'harga' => $harga
        ];

        $simpan = DB::table('kendaraan_service_temp')->insert($data);
        if ($simpan) {
            echo 1;
        } else {
            echo 2;
        }
    }
}
