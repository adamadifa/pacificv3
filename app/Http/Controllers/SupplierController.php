<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PDOException;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        if ($request->nama_supplier != "") {
            $query->where('nama_supplier', 'like', '%' . $request->nama_supplier . '%');
        }
        $supplier = $query->paginate(15);
        $supplier->appends($request->all());
        return view('supplier.index', compact('supplier'));
    }

    public function create()
    {
        $supplier = DB::table('supplier')->orderBy('kode_supplier', 'desc')->first();
        $last_kodesupplier = $supplier->kode_supplier;
        $kode_supplier = buatkode($last_kodesupplier, "SP", 4);
        return view('supplier.create', compact('kode_supplier'));
    }

    public function store(Request $request)
    {
        $kode_supplier = $request->kode_supplier;
        $nama_supplier = $request->nama_supplier;
        $alamat_supplier = $request->alamat_supplier;
        $contact_supplier = $request->contact_supplier;
        $nohp_supplier = $request->nohp_supplier;
        $email = $request->email;
        $norekening = $request->norekening;

        $data = [
            'kode_supplier' => $kode_supplier,
            'nama_supplier' => $nama_supplier,
            'alamat_supplier' => $alamat_supplier,
            'contact_supplier' => $contact_supplier,
            'nohp_supplier' => $nohp_supplier,
            'email' => $email,
            'norekening' => $norekening
        ];

        $simpan = DB::table('supplier')->insert($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function delete($kode_supplier)
    {
        $kode_supplier = Crypt::decrypt($kode_supplier);
        try {
            DB::table('supplier')
                ->where('kode_supplier', $kode_supplier)
                ->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (PDOException $e) {
            $errorcode = $e->getCode();
            if ($errorcode == 23000) {
                return Redirect::back()->with(['warning' => 'Data Tidak Dapat Dihapus Karena Sudah Memiliki Transaksi']);
            }
        }
    }

    public function edit($kode_supplier)
    {
        $supplier = Supplier::where('kode_supplier', $kode_supplier)->first();
        return view('supplier.edit', compact('supplier'));
    }

    public function update($kode_supplier, Request $request)
    {
        $kode_supplier = Crypt::decrypt($kode_supplier);
        $nama_supplier = $request->nama_supplier;
        $alamat_supplier = $request->alamat_supplier;
        $contact_supplier = $request->contact_supplier;
        $nohp_supplier = $request->nohp_supplier;
        $email = $request->email;
        $norekening = $request->norekening;

        $data = [
            'nama_supplier' => $nama_supplier,
            'alamat_supplier' => $alamat_supplier,
            'contact_supplier' => $contact_supplier,
            'nohp_supplier' => $nohp_supplier,
            'email' => $email,
            'norekening' => $norekening
        ];

        $simpan = DB::table('supplier')->where('kode_supplier', $kode_supplier)->update($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update, Hubungi Tim IT !']);
        }
    }

    public function getsupplier()
    {
        return view('supplier.getsupplier');
    }

    public function json()
    {

        $query = Supplier::query();
        $supplier = $query;
        return DataTables::of($supplier)
            ->addColumn('action', function ($supplier) {
                return '<a href="#"
                kode_supplier="' . $supplier->kode_supplier . '" nama_supplier="' . $supplier->nama_supplier . '"
                ><i class="feather icon-external-link success"></i></a>';
            })
            ->toJson();
    }
}
