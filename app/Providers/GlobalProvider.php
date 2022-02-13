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

            //Dashboard

            $dashboardadmin = ['admin', 'manager marketing', 'general manager', 'direktur'];
            $dashboardkepalapenjualan = ['kepala penjualan', 'kepala cabang'];

            //Data Master
            $datamaster = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing', 'direktur'];
            $pelanggan = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing', 'direktur'];
            $salesman = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing', 'direktur'];
            $barang = ['admin', 'direktur', 'manager marketing', 'general manager'];
            $harga = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing', 'direktur'];
            $harga_hapus = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing', 'direktur'];
            $kendaraan = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing', 'direktur'];
            $kendaraan_tambah = ['admin'];
            $kendaraan_edit = ['admin' . 'admin penjualan', 'kepala penjualan', 'manager marketing', 'direktur'];
            $kendaraan_hapus = ['admin'];
            $cabang = ['admin'];





            //Marketing
            $marketing = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing', 'general manager', 'direktur'];

            //-----------------------------Penjualan-------------------------------------------
            $penjualan_menu = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing', 'general manager', 'direktur'];
            $penjualan_keuangan = ['admin', 'admin penjualan', 'kepala penjualan'];
            $penjualan_input = ['admin', 'admin penjualan', 'kepala penjualan'];
            $penjualan_view = ['admin', 'admin penjualan', 'kepala penjualan'];
            //Retur
            $retur_view = ['admin', 'admin penjualan', 'kepala penjualan'];
            //LImit
            $limitkredit_view = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing', 'general manager', 'direktur'];
            $penyesuaian_limit = ['admin', 'direktur'];
            //Laporan
            $laporan_penjualan = ['admin'];

            //Keuangan
            $keuangan = ['admin', 'admin penjualan', 'kepala penjualan'];
            $giro_view = ['admin', 'admin penjualan', 'kepala penjualan'];
            $transfer_view = ['admin', 'admin penjualan', 'kepala penjualan'];






            //Utilities
            $utilities = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing'];
            $kirimlpc = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing'];
            $shareddata = [
                'level' => $level,
                'getcbg' => $getcbg,
                //Dashboard
                'dashboardadmin' => $dashboardadmin,
                'dashboardkepalapenjualan' => $dashboardkepalapenjualan,
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
                //------------Penjualan-------------------
                'penjualan_menu' => $penjualan_menu,
                'penjualan_keuangan' => $penjualan_keuangan,
                'penjualan_input' => $penjualan_input,
                'penjualan_view' => $penjualan_view,
                //Retur
                'retur_view' => $retur_view,
                //Limit Kredit
                'limitkredit_view' => $limitkredit_view,
                'penyesuaian_limit' => $penyesuaian_limit,
                //Laporan
                'laporan_penjualan' => $laporan_penjualan,
                //Keuangan
                'keuangan' => $keuangan,
                'penjualan_keuangan' => $penjualan_keuangan,
                'giro_view' => $giro_view,
                'transfer_view' => $transfer_view,

                //Utilities
                'utilities' => $utilities,
                'kirimlpc' => $kirimlpc



            ];
            View::share($shareddata);
        });
    }
}
