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
            $dashboardadminpenjualan = ['admin penjualan'];
            $dashboardaccounting = ['manager accounting'];

            //Data Master
            $datamaster = ['admin', 'admin penjualan', 'manager accounting', 'kepala penjualan', 'kepala admin', 'manager marketing', 'direktur'];
            //Pelanggan
            $pelanggan = ['admin', 'admin penjualan', 'manager accounting', 'kepala penjualan', 'kepala admin', 'manager marketing', 'direktur'];
            $pelanggan_tambah = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];
            $pelanggan_edit = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];
            $pelanggan_hapus = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];
            $pelanggan_ajuanlimit = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];

            //Salesman
            $salesman = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager accounting', 'manager marketing', 'direktur'];
            $salesman_tambah = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];
            $salesman_edit = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];
            $salesman_hapus = ['admin', 'kepala penjualan', 'kepala admin', 'admin penjualan'];

            //Barang
            $barang = ['admin', 'manager accounting', 'direktur', 'manager marketing', 'general manager'];
            $barang_tambah = ['admin'];
            $barang_edit = ['admin'];
            $barang_hapus = ['admin'];

            //Harga
            $harga = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager accounting', 'manager marketing', 'direktur'];
            $harga_hapus = ['admin'];
            $harga_tambah = ['admin'];
            $harga_edit = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin'];

            $kendaraan = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager accounting', 'manager marketing', 'direktur'];
            $kendaraan_tambah = ['admin'];
            $kendaraan_edit = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin'];
            $kendaraan_hapus = ['admin'];


            $cabang = ['admin'];





            //Marketing
            $marketing = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager accounting', 'manager marketing', 'general manager', 'direktur'];

            //-----------------------------OMAN-------------------------------------------------
            $oman = ['admin'];
            $omancabang = ['admin'];
            $omanmarketing = ['admin'];
            //----------------------------Permintaaan Pengiriman--------------------------------
            $permintaanpengiriman = ['admin'];
            //----------------------------Target Komisi--------------------------------
            $komisi = ['admin', 'kepala penjualan', 'kepala admin', 'manager marketing', 'general manager', 'direktur', 'manager accounting'];
            $targetkomisi = ['admin', 'kepala penjualan', 'kepala admin', 'manager marketing', 'general manager', 'direktur', 'manager accounting'];
            $targetkomisiinput = ['admin', 'kepala penjualan', 'kepala admin'];
            $generatecashin = ['admin'];
            $ratiokomisi = ['admin', 'kepala admin', 'kepala penjualan'];
            $laporan_komisi = ['admin', 'direktur', 'kepala admin', 'manager marketing', 'general manager', 'manager accounting', 'kepala penjualan'];
            //-----------------------------Penjualan-------------------------------------------
            $penjualan_menu = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager accounting', 'manager marketing', 'general manager', 'direktur'];
            $penjualan_keuangan = ['admin', 'admin penjualan', 'kepala admin', 'staff keuangan'];
            $penjualan_input = ['admin', 'admin penjualan', 'kepala admin'];
            $penjualan_view = ['admin', 'admin penjualan', 'kepala admin'];
            //Retur
            $retur_view = ['admin', 'admin penjualan', 'kepala admin'];
            //LImit
            $limitkredit_view = ['admin', 'admin penjualan', 'kepala admin', 'manager marketing', 'manager accounting', 'general manager', 'direktur'];
            $limitkredit_hapus = ['admin', 'admin penjualan', 'kepala admin', 'kepala penjualan'];
            $limitkredit_analisa = ['admin', 'admin penjualan', 'kepala admin', 'kepala penjualan'];
            $penyesuaian_limit = ['admin', 'direktur'];
            //Laporan
            $laporan_penjualan = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager accounting', 'manager marketing', 'manager accounting', 'general manager', 'direktur'];
            $harga_net = ['admin', 'manager accounting', 'manager marketing', 'general manager', 'direktur'];
            //--------------------------------Keuangan---------------------------------------------
            $keuangan = ['admin', 'admin penjualan', 'kepala admin', 'direktur', 'manager accounting', 'general manager', 'manager marketing', 'kepala penjualan', 'staff keuangan'];
            $laporankeuangan_view = ['admin', 'direktur', 'general manager', 'manager marketing', 'manager accounting', 'kepala penjualan', 'kepala admin', 'staff keuangan'];
            $laporan_ledger = ['admin', 'direktur', 'general manager', 'manager accounting'];
            //Giro
            $giro_view = ['admin', 'admin penjualan', 'kepala admin', 'staff keuangan'];
            $giro_approved = ['admin', 'staff keuangan', 'manager keuangan'];

            //Transfer
            $transfer_view = ['admin', 'admin penjualan', 'kepala admin', 'staff keuangan'];
            $transfer_approved =  ['admin', 'staff keuangan', 'manager keuangan'];

            //Kas Kecil
            $kaskecil_menu  = ['admin', 'staff keuangan'];
            $kaskecil_view = ['admin'];
            $klaim_view = ['admin', 'staff keuangan'];
            $klaim_add = ['admin', 'kasir'];
            $klaim_hapus = ['admin', 'kasir'];
            $klaim_validasi = ['admin', 'kasir'];

            //Mutasi Bank
            $mutasibank_view = ['admin'];

            //Ledger
            $ledger_menu  = ['admin', 'staff keuangan'];
            $ledger_view = ['admin', 'staff keuangan'];
            $ledger_saldoawal = ['admin', 'staff keuangan'];

            //Kas Besar Keuangan
            $kasbesar_menu  = ['admin', 'staff keuangan'];
            $saldoawalkasbesar_view = ['admin', 'staff keuangan'];
            $setoran_menu = ['admin', 'staff keuangan'];
            $setoranpenjualan_view = ['admin'];
            $setoranpusat_view = ['admin', 'staff keuangan'];
            $setoranpusat_add = ['admin', 'kasir', 'kepala admin'];
            $setoranpusat_edit = ['admin', 'kasir', 'kepala admin'];
            $setoranpusat_hapus = ['admin', 'kasir', 'kepala admin'];
            $setorangiro_view = ['admin'];
            $setorantransfer_view = ['admin'];
            $belum_disetorkan = ['admin'];
            $lebih_disetorkan = ['admin'];

            $saldoawalpiutang = ['admin'];

            $kirimlpc = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager accounting', 'manager marketing', 'general manager', 'direktur'];
            $kirimlpc_tambah = ['admin', 'kepala penjualan', 'kepala admin', 'manager accounting'];
            $kirimlpc_edit = ['admin', 'kepala penjualan', 'kepala admin', 'manager accounting'];
            $kirimlpc_hapus = ['admin', 'kepala penjualan', 'kepala admin', 'manager accounting'];
            $kirimlpc_approve = ['admin', 'manager accounting'];
            $shareddata = [
                'level' => $level,
                'getcbg' => $getcbg,
                //Dashboard
                'dashboardadmin' => $dashboardadmin,
                'dashboardkepalapenjualan' => $dashboardkepalapenjualan,
                'dashboardkepalaadmin' => $dashboardkepalaadmin,
                'dashboardadminpenjualan' => $dashboardadminpenjualan,
                'dashboardaccounting' => $dashboardaccounting,
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
                //-----------OMAN------------------------
                'oman' => $oman,
                'omancabang' => $omancabang,
                'omanmarketing' => $omanmarketing,

                //------------Permintaan Pengiriman------
                'permintaanpengiriman' => $permintaanpengiriman,
                //------------Komisi------
                'komisi' => $komisi,
                'targetkomisi' => $targetkomisi,
                'targetkomisiinput' => $targetkomisiinput,
                'generatecashin' => $generatecashin,
                'ratiokomisi' => $ratiokomisi,
                'laporan_komisi' => $laporan_komisi,
                //------------Penjualan-------------------
                'penjualan_menu' => $penjualan_menu,
                'penjualan_keuangan' => $penjualan_keuangan,
                'penjualan_input' => $penjualan_input,
                'penjualan_view' => $penjualan_view,
                //Retur
                'retur_view' => $retur_view,
                //Limit Kredit
                'limitkredit_view' => $limitkredit_view,
                'limitkredit_hapus' => $limitkredit_hapus,
                'limitkredit_analisa' => $limitkredit_analisa,
                'penyesuaian_limit' => $penyesuaian_limit,
                //Laporan
                'laporan_penjualan' => $laporan_penjualan,
                'harga_net' => $harga_net,
                //--------------Keuangan--------------
                'keuangan' => $keuangan,
                'penjualan_keuangan' => $penjualan_keuangan,
                'laporankeuangan_view' => $laporankeuangan_view,
                'laporan_ledger' => $laporan_ledger,

                //Giro
                'giro_view' => $giro_view,
                'giro_approved' => $giro_approved,
                //Transfer
                'transfer_view' => $transfer_view,
                'transfer_approved' => $transfer_approved,

                //Kas Kecil
                'kaskecil_menu' => $kaskecil_menu,
                'kaskecil_view' => $kaskecil_view,
                'klaim_view' => $klaim_view,
                'klaim_add' => $klaim_add,
                'klaim_hapus' => $klaim_hapus,
                'klaim_validasi' => $klaim_validasi,

                //Mutasi Bank
                'mutasibank_view' => $mutasibank_view,


                //ledger
                'ledger_menu' => $ledger_menu,
                'ledger_view' => $ledger_view,
                'ledger_saldoawal' => $ledger_saldoawal,

                //Kas Besar Keuangan
                'kasbesar_menu' => $kasbesar_menu,
                'saldoawalkasbesar_view' => $saldoawalkasbesar_view,
                //Setoran
                'setoran_menu' => $setoran_menu,
                'setoranpenjualan_view' => $setoranpenjualan_view,
                'setoranpusat_view' => $setoranpusat_view,
                'setoranpusat_add' => $setoranpusat_add,
                'setoranpusat_edit' => $setoranpusat_edit,
                'setoranpusat_hapus' => $setoranpusat_hapus,
                'setorangiro_view' => $setorangiro_view,
                'setorantransfer_view' => $setorantransfer_view,
                'belum_disetorkan' => $belum_disetorkan,
                'lebih_disetorkan' => $lebih_disetorkan,


                //Utilities
                'saldoawalpiutang' => $saldoawalpiutang,

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
