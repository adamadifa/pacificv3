<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Pelanggan extends Component
{
    public $search = '';
    public $pelanggan = '';

    public function render()
    {
        $id_karyawan = Auth::user()->id_salesman;

        $this->pelanggan =  DB::table('pelanggan')
            ->join('karyawan', 'pelanggan.id_sales', '=', 'karyawan.id_karyawan')
            ->where('pelanggan.kode_cabang', Auth::user()->kode_cabang)
            ->where('id_sales', $id_karyawan)
            ->where('status_pelanggan', 1)
            ->where('nama_pelanggan', 'like', '%' . $this->search . '%')
            ->limit(10)
            ->orderBy('nama_pelanggan', 'desc')
            ->get();
        return view('livewire.pelanggan');
    }

    public function updatedSearch()
    {
        $this->render();
    }
}
