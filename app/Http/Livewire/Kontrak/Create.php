<?php

namespace App\Http\Livewire\Kontrak;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public $nik;
    public $dari;
    public $sampai;
    public $kontrak_ke;


    public function render()
    {
        $this->dispatchBrowserEvent('contentChanged');
        $kontrak = DB::table('hrd_historikontrak')->where('nik', $this->nik)->get();
        return view('livewire.kontrak.create', [
            'nik' => $this->nik,
            'kontrak' => $kontrak
        ]);
    }

    public function save()
    {
        $this->validate([
            'kontrak_ke' => 'required',
            'dari' => 'required',
            'sampai' => 'required'
        ]);

        $data = [
            'nik' => $this->nik,
            'kontrak_ke' => $this->kontrak_ke,
            'dari' => $this->dari,
            'sampai' => $this->sampai
        ];

        $this->dispatchBrowserEvent('contentChanged');
        $cek = DB::table('hrd_historikontrak')->where('nik', $this->nik)->where('kontrak_ke', $this->kontrak_ke)->count();
        if ($cek > 0) {
            session()->flash('msg', 'Data Sudah Ada');
            session()->flash('alert', 'danger');
            $this->resetform();
        } else {
            try {

                DB::table('hrd_historikontrak')->insert($data);
                session()->flash('msg', 'Data Berhasil Disimpan');
                session()->flash('alert', 'success');
                $this->resetform();
            } catch (\Throwable $e) {
                session()->flash('msg', $e);
                session()->flash('alert', 'danger');
                $this->resetform();
            }
        }
    }

    public function delete($id)
    {
        $this->dispatchBrowserEvent('contentChanged');
        try {
            DB::table('hrd_historikontrak')->where('id', $id)->delete();
            session()->flash('msg', 'Data Berhasil Di Hapus');
            session()->flash('alert', 'success');
        } catch (\Throwable $e) {
            session()->flash('msg', $e);
            session()->flash('alert', 'danger');
        }
    }

    function resetform()
    {
        $this->kontrak_ke = "";
        $this->dari = "";
        $this->sampai = "";
    }
}
