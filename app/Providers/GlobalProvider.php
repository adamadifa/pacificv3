<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use App\Profil;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View as View;
use Request;



class GlobalProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Guard $auth)
    {

        view()->composer('*', function ($view) use ($auth) {
            if (Auth::check()) {
                $level = $auth->user()->level;
                $getcbg = $auth->user()->kode_cabang;
            } else {
                $level = "";
                $getcbg = "";
            }
            //Data Master
            $datamaster = ['admin', 'admin penjualan'];

            $pelanggan = ['admin', 'admin penjualan'];

            $salesman = ['admin', 'admin penjualan'];

            $barang = ['admin'];

            $harga = ['admin', 'admin penjualan'];
            $harga_hapus = ['admin', 'admin penjualan'];

            $kendaraan = ['admin', 'admin penjualan'];
            $kendaraan_tambah = ['admin'];
            $kendaraan_edit = ['admin' . 'admin penjualan'];
            $kendaraan_hapus = ['admin'];

            $cabang = ['admin'];





            //Marketing
            $marketing = ['admin'];

            //Penjualan
            $penjualan_menu = ['admin'];
            $penjualan_keuangan = ['admin'];
            $penjualan_input = ['admin'];
            $penjualan_view = ['admin'];
            $penjualan_pending_view = ['admin'];

            //Retur
            $retur_view = ['admin'];


            //Keuangan
            $keuangan = ['admin'];

            //Keuangan
            $giro_view = ['admin'];

            //Keuangan
            $transfer_view = ['admin'];

            //Utilities
            $utilities = ['admin', 'admin penjualan'];
            $kirimlpc = ['admin', 'admin penjualan'];
            $shareddata = [
                'level' => $level,
                'getcbg' => $getcbg,

                //Data Master
                'datamaster_view' => $datamaster,
                'pelanggan_view' => $pelanggan,

                'salesman_view' => $salesman,

                'barang_view' => $barang,

                'harga_view' => $harga,
                'harga_hapus' => $harga_hapus,

                'kendaraan_view' => $kendaraan,
                'kendaraan_tambah' => $kendaraan_tambah,
                'kendaraan_edit' => $kendaraan_edit,
                'kendaraan_hapus' => $kendaraan_hapus,

                'cabang_view' => $cabang,

                //Data Marketing
                'marketing' => $marketing,
                //Penjualan
                'penjualan_menu' => $penjualan_menu,
                'penjualan_keuangan' => $penjualan_keuangan,
                'penjualan_input' => $penjualan_input,
                'penjualan_view' => $penjualan_view,
                'penjualan_pending_view' => $penjualan_pending_view,

                //Retur
                'retur_view' => $retur_view,

                //Keuangan
                'keuangan' => $keuangan,
                'penjualan_keuangan' => $penjualan_keuangan,

                //Giro
                'giro_view' => $giro_view,

                //Giro
                'transfer_view' => $transfer_view,

                //Utilities
                'utilities' => $utilities,
                'kirimlpc' => $kirimlpc



            ];
            View::share($shareddata);
        });
    }
}
