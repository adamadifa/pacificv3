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
            $dashboardkepalapenjualan = ['kepala penjualan'];
            $dashboardkepalaadmin = ['kepala admin'];

            //Data Master
            $datamaster = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager marketing', 'direktur'];
            //Pelanggan
            $pelanggan = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager marketing', 'direktur'];
            $pelanggan_tambah = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];
            $pelanggan_edit = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];
            $pelanggan_hapus = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];
            $pelanggan_ajuanlimit = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];

            //Salesman
            $salesman = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager marketing', 'direktur'];
            $salesman_tambah = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];
            $salesman_edit = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];
            $salesman_hapus = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];

            //Barang
            $barang = ['admin', 'direktur', 'manager marketing', 'general manager'];
            $barang_tambah = ['admin'];
            $barang_edit = ['admin'];
            $barang_hapus = ['admin'];

            //Harga
            $harga = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager marketing', 'direktur'];
            $harga_hapus = ['admin'];
            $harga_tambah = ['admin'];
            $harga_edit = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin'];

            $kendaraan = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager marketing', 'direktur'];
            $kendaraan_tambah = ['admin'];
            $kendaraan_edit = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin'];
            $kendaraan_hapus = ['admin'];


            $cabang = ['admin'];





            //Marketing
            $marketing = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager marketing', 'general manager', 'direktur'];

            //-----------------------------Penjualan-------------------------------------------
            $penjualan_menu = ['admin', 'admin penjualan', 'kepala admin', 'kepala admin', 'manager marketing', 'general manager', 'direktur'];
            $penjualan_keuangan = ['admin', 'admin penjualan', 'kepala admin'];
            $penjualan_input = ['admin', 'admin penjualan', 'kepala admin'];
            $penjualan_view = ['admin', 'admin penjualan', 'kepala admin'];
            //Retur
            $retur_view = ['admin', 'admin penjualan', 'kepala admin'];
            //LImit
            $limitkredit_view = ['admin', 'admin penjualan', 'kepala admin', 'manager marketing', 'general manager', 'direktur'];
            $penyesuaian_limit = ['admin', 'direktur'];
            //Laporan
            $laporan_penjualan = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager marketing', 'manager accounting', 'general manager', 'direktur'];
            $harga_net = ['admin', 'manager accounting', 'manager marketing', 'general manager', 'direktur'];
            //Keuangan
            $keuangan = ['admin', 'admin penjualan', 'kepala admin'];

            $giro_view = ['admin', 'admin penjualan', 'kepala admin'];
            $giro_approved = ['admin', 'admin keuangan', 'manager keuangan'];

            $transfer_view = ['admin', 'admin penjualan', 'kepala admin'];
            $transfer_approved =  ['admin', 'admin keuangan', 'manager keuangan'];






            //Utilities
            $utilities = ['admin', 'admin penjualan', 'kepala penjualan', 'manager marketing', 'manager accounting', 'general manager', 'direktur'];
            $kirimlpc = ['admin', 'admin penjualan', 'kepala penjualan', 'manager accounting', 'manager marketing', 'general manager', 'direktur'];
            $kirimlpc_tambah = ['admin', 'admin penjualan', 'kepala penjualan', 'manager accounting'];
            $kirimlpc_edit = ['admin', 'admin penjualan', 'kepala penjualan', 'manager accounting'];
            $kirimlpc_hapus = ['admin', 'admin penjualan', 'kepala penjualan', 'manager accounting'];
            $kirimlpc_approve = ['admin', 'manager accounting'];
            $shareddata = [
                'level' => $level,
                'getcbg' => $getcbg,
                //Dashboard
                'dashboardadmin' => $dashboardadmin,
                'dashboardkepalapenjualan' => $dashboardkepalapenjualan,
                'dashboardkepalaadmin' => $dashboardkepalaadmin,
                //Data Master
                'datamaster_view' => $datamaster,
                //Pelanggan
                'pelanggan_view' => $pelanggan,
                'pelanggan_tambah' => $pelanggan_tambah,
                'pelanggan_edit' => $pelanggan_edit,
                'pelanggan_hapus' => $pelanggan_hapus,
                'pelanggan_ajuanlimit' => $pelanggan_ajuanlimit,

                //Salesman
                'salesman_view' => $salesman,
                'salesman_tambah' => $salesman_tambah,
                'salesman_edit' => $salesman_edit,
                'salesman_hapus' => $salesman_hapus,

                //Barang Produk
                'barang_view' => $barang,
                'barang_tambah' => $barang_tambah,
                'barang_edit' => $barang_edit,
                'barang_hapus' => $barang_hapus,
                //Harga Edit
                'harga_view' => $harga,
                'harga_hapus' => $harga_hapus,
                'harga_tambah' => $harga_tambah,
                'harga_edit' => $harga_edit,

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
                'harga_net' => $harga_net,
                //Keuangan
                'keuangan' => $keuangan,
                'penjualan_keuangan' => $penjualan_keuangan,

                'giro_view' => $giro_view,
                'giro_approved' => $giro_approved,

                'transfer_view' => $transfer_view,
                'transfer_approved' => $transfer_approved,
                //Utilities
                'utilities' => $utilities,
                'kirimlpc' => $kirimlpc,
                'kirimlpc_tambah' => $kirimlpc_tambah,
                'kirimlpc_edit' => $kirimlpc_edit,
                'kirimlpc_hapus' => $kirimlpc_hapus,
                'kirimlpc_approve' => $kirimlpc_approve,


            ];
            View::share($shareddata);
        });
    }
}
