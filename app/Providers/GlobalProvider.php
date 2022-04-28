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
            $dashboardstaffkeuangan = ['staff keuangan'];
            $dashboardadminkaskecil = ['admin kas kecil'];
            $dashboardpembelian = ['manager pembelian', 'admin pembelian'];

            //Data Master
            $datamaster = ['admin', 'admin penjualan', 'manager accounting', 'kepala penjualan', 'kepala admin', 'manager marketing', 'direktur', 'manager pembelian', 'admin pembelian'];
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

            //Supplier
            $supplier_menu = ['admin', 'manager pembelian', 'admin pembelian', 'manager accounting'];
            $supplier_tambah = ['admin', 'manager pembelian', 'admin pembelian'];
            $supplier_edit = ['admin', 'manager pembelian', 'admin pembelian'];
            $supplier_hapus = ['admin', 'manager pembelian', 'admin pembelian'];

            //Barang
            $barang = ['admin', 'manager accounting', 'direktur', 'manager marketing', 'general manager'];
            $barang_tambah = ['admin'];
            $barang_edit = ['admin'];
            $barang_hapus = ['admin'];

            //Barang
            $barangpembelian = ['admin', 'manager pembelian', 'admin pembelian', 'manager accounting'];
            $barangpembelian_tambah = ['admin', 'manager pembelian', 'admin pembelian'];
            $barangpembelian_edit = ['admin', 'manager pembelian', 'admin pembelian'];
            $barangpembelian_hapus = ['admin', 'manager pembelian', 'admin pembelian'];

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
            $keuangan = [
                'admin', 'admin penjualan', 'kepala admin', 'direktur', 'manager accounting', 'general manager',
                'manager marketing', 'kepala penjualan', 'staff keuangan', 'admin kas kecil', 'kasir', 'manager pembelian', 'admin pembelian'
            ];
            $laporankeuangan_view = ['admin', 'direktur', 'general manager', 'manager marketing', 'manager accounting', 'kepala penjualan', 'kepala admin', 'staff keuangan', 'admin kas kecil'];
            $laporan_ledger = ['admin', 'direktur', 'general manager', 'manager accounting'];
            $laporan_kaskecil = ['admin', 'direktur', 'general manager', 'manager accounting', 'kepala admin', 'staff keuangan', 'admin kas kecil'];
            $laporan_saldokasbesar = ['admin', 'direktur', 'general manager', 'manager accounting', 'kepala admin', 'staff keuangan'];
            $laporan_lpu = ['admin', 'direktur', 'general manager', 'manager accounting', 'kepala admin', 'staff keuangan'];
            $laporan_penjualan_keuangan = ['admin', 'direktur', 'general manager', 'manager accounting', 'kepala admin', 'staff keuangan'];
            $laporan_uanglogam = ['admin', 'direktur', 'general manager', 'manager accounting', 'kepala admin', 'staff keuangan'];
            $laporan_rekapbg = ['admin', 'direktur', 'general manager', 'manager accounting', 'kepala admin', 'staff keuangan'];
            //Giro
            $giro_view = ['admin', 'admin penjualan', 'kepala admin', 'staff keuangan'];
            $giro_approved = ['admin', 'staff keuangan', 'manager keuangan'];

            //Transfer
            $transfer_view = ['admin', 'admin penjualan', 'kepala admin', 'staff keuangan'];
            $transfer_approved =  ['admin', 'staff keuangan', 'manager keuangan'];

            //Kas Kecil
            $kaskecil_menu  = ['admin', 'staff keuangan', 'kepala admin', 'admin kas kecil'];
            $kaskecil_view = ['admin', 'kepala admin', 'admin kas kecil'];
            $klaim_view = ['admin', 'staff keuangan', 'kepala admin', 'admin kas kecil'];
            $klaim_add = ['admin', 'kepala admin', 'admin kas kecil'];
            $klaim_hapus = ['admin', 'kepala admin', 'admin kas kecil'];
            $klaim_validasi = ['admin', 'kepala admin', 'admin kas kecil'];
            $klaim_proses = ['admin', 'staff keuangan'];

            //Mutasi Bank
            $mutasibank_view = ['admin', 'kepala admin', 'admin kas kecil'];

            //Ledger
            $ledger_menu  = ['admin', 'staff keuangan'];
            $ledger_view = ['admin', 'staff keuangan'];
            $ledger_saldoawal = ['admin', 'staff keuangan'];

            //Kas Besar Keuangan
            $kasbesar_menu  = ['admin', 'staff keuangan', 'kepala admin', 'kasir'];
            $saldoawalkasbesar_view = ['admin', 'staff keuangan'];
            $setoran_menu = ['admin', 'staff keuangan', 'kepala admin', 'kasir'];
            $setoranpenjualan_view = ['admin', 'kepala admin', 'kasir'];
            $setoranpusat_view = ['admin', 'staff keuangan', 'kepala admin', 'kasir'];
            $setoranpusat_add = ['admin', 'kasir', 'kepala admin', 'kasir'];
            $setoranpusat_edit = ['admin', 'kasir', 'kepala admin', 'kasir'];
            $setoranpusat_hapus = ['admin', 'kasir', 'kepala admin', 'kasir'];
            $setoranpusat_terimasetoran = ['admin', 'staff keuangan'];
            $setorangiro_view = ['admin', 'kepala admin', 'kasir'];
            $setorantransfer_view = ['admin', 'kepala admin', 'kasir'];
            $belum_disetorkan = ['admin', 'kepala admin', 'kasir'];
            $lebih_disetorkan = ['admin', 'kepala admin', 'kasir'];

            $saldoawalpiutang = ['admin'];

            $kirimlpc = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'manager accounting', 'manager marketing', 'general manager', 'direktur'];
            $kirimlpc_tambah = ['admin', 'kepala penjualan', 'kepala admin', 'manager accounting'];
            $kirimlpc_edit = ['admin', 'kepala penjualan', 'kepala admin', 'manager accounting'];
            $kirimlpc_hapus = ['admin', 'kepala penjualan', 'kepala admin', 'manager accounting'];
            $kirimlpc_approve = ['admin', 'manager accounting'];




            //Pembelian
            $pembelian_menu = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];
            $pembelian_view = ['admin', 'manager pembelian', 'admin pembelian'];
            $pembelian_keuangan = ['admin', 'manager pembelian', 'admin pembelian'];
            $kontrabon_view = ['admin', 'manager pembelian', 'admin pembelian'];
            $kontrabon_edit_hapus = ['admin', 'admin pembelian'];
            $kontrabon_approve = ['admin', 'manager pembelian'];
            $kontrabon_proses = ['admin', 'staff keuangan'];

            $jatuhtempo_view = ['admin', 'manager pembelian', 'admin pembelian'];
            $jurnalkoreksi_view = ['admin', 'manager pembelian', 'admin pembelian'];
            $laporan_pembelian = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];
            $laporan_pembayaran_pembelian = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];
            $laporan_rekappembeliansupplier = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];
            $laporan_rekappembelian = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];
            $laporan_kartuhutang = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];
            $laporan_auh = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];
            $laporan_bahankemasan = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];
            $laporan_rekapbahankemasan = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];
            $laporan_jurnalkoreksi = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];
            $laporan_rekapakunpembelian  = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];
            $laporan_rekapkontrabon = ['admin', 'direktur', 'general manager', 'manager accounting', 'manager pembelian', 'admin pembelian'];


            $produksi_menu = ['admin'];
            $produksi_analytics = ['admin'];
            $mutasi_produk = ['admin'];
            $bpbj_view = ['admin'];
            $fsthp_view = ['admin'];
            $mutasi_barang = ['admin'];
            $pemasukan_produksi = ['admin'];
            $pengeluaran_produksi = ['admin'];
            $saldoawal_mutasibarang_produksi = ['admin'];
            $opname_mutasibarang_produksi = ['admin'];
            $laporan_produksi = ['admin'];
            $laporan_mutasiproduksi = ['admin'];
            $laporan_rekapmutasiproduksi = ['admin'];
            $laporan_pemasukanproduksi = ['admin'];
            $laporan_pengeluaranproduksi = ['admin'];
            $laporan_rekappersediaanbarangproduksi = ['admin'];

            //Gudang
            $gudang_menu = ['admin'];
            $gudang_bahan_menu = ['admin'];
            $gudang_logistik_menu = ['admin'];
            $gudang_jadi_menu = ['admin'];
            $gudang_cabang_menu = ['admin'];
            $pemasukan_gudanglogisitik = ['admin'];
            $approve_pembelian = ['admin'];

            $shareddata = [
                'level' => $level,
                'getcbg' => $getcbg,
                //Dashboard
                'dashboardadmin' => $dashboardadmin,
                'dashboardkepalapenjualan' => $dashboardkepalapenjualan,
                'dashboardkepalaadmin' => $dashboardkepalaadmin,
                'dashboardadminpenjualan' => $dashboardadminpenjualan,
                'dashboardaccounting' => $dashboardaccounting,
                'dashboardstaffkeuangan' => $dashboardstaffkeuangan,
                'dashboardadminkaskecil' => $dashboardadminkaskecil,
                'dashboardpembelian' => $dashboardpembelian,
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


                'supplier_view' => $supplier_menu,
                'supplier_tambah' => $salesman_tambah,
                'supplier_edit' => $salesman_edit,
                'supplier_hapus' => $salesman_hapus,

                //Barang Produk
                'barang_view' => $barang,
                'barang_tambah' => $barang_tambah,
                'barang_edit' => $barang_edit,
                'barang_hapus' => $barang_hapus,

                //Barang Pembelian
                'barangpembelian' => $barangpembelian,
                'barangpembelian_tambah' => $barangpembelian_tambah,
                'barangpembelian_edit' => $barangpembelian_edit,
                'barangpembelian_hapus' => $barangpembelian_hapus,
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
                'laporan_kaskecil' => $laporan_kaskecil,
                'laporan_saldokasbesar' => $laporan_saldokasbesar,
                'laporan_lpu' => $laporan_lpu,
                'laporan_penjualan_keuangan' => $laporan_penjualan_keuangan,
                'laporan_uanglogam' => $laporan_uanglogam,
                'laporan_rekapbg' => $laporan_rekapbg,

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
                'klaim_proses' => $klaim_proses,

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
                'setoranpusat_terimasetoran' => $setoranpusat_terimasetoran,


                //Utilities
                'saldoawalpiutang' => $saldoawalpiutang,

                'kirimlpc' => $kirimlpc,
                'kirimlpc_tambah' => $kirimlpc_tambah,
                'kirimlpc_edit' => $kirimlpc_edit,
                'kirimlpc_hapus' => $kirimlpc_hapus,
                'kirimlpc_approve' => $kirimlpc_approve,

                'pembelian_menu' => $pembelian_menu,
                'pembelian_view' => $pembelian_view,
                'pembelian_keuangan' => $pembelian_keuangan,
                'kontrabon_view' => $kontrabon_view,
                'kontrabon_edit_hapus' => $kontrabon_edit_hapus,
                'kontrabon_proses' => $kontrabon_proses,
                'kontrabon_approve' => $kontrabon_approve,
                'jatuhtempo_view' => $jatuhtempo_view,
                'jurnalkoreksi_view' => $jurnalkoreksi_view,
                'laporan_pembelian' => $laporan_pembelian,
                'laporan_pembayaran_pembelian' => $laporan_pembayaran_pembelian,
                'laporan_rekappembeliansupplier' => $laporan_rekappembeliansupplier,
                'laporan_rekappembelian' => $laporan_rekappembelian,
                'laporan_kartuhutang' => $laporan_kartuhutang,
                'laporan_auh' => $laporan_auh,
                'laporan_bahankemasan' => $laporan_bahankemasan,
                'laporan_rekapbahankemasan' => $laporan_rekapbahankemasan,
                'laporan_jurnalkoreksi' => $laporan_jurnalkoreksi,
                'laporan_rekapakunpembelian' => $laporan_rekapakunpembelian,
                'laporan_rekapkontrabon' => $laporan_rekapkontrabon,

                //Produksi

                'produksi_menu' => $produksi_menu,
                'produksi_analytics' => $produksi_analytics,
                'mutasi_produk' => $mutasi_produk,
                'bpbj_view' => $bpbj_view,
                'fsthp_view' => $fsthp_view,
                'mutasi_barang' => $mutasi_barang,
                'pemasukan_produksi' => $pemasukan_produksi,
                'pengeluaran_produksi' => $pengeluaran_produksi,
                'saldoawal_mutasibarang_produksi' => $saldoawal_mutasibarang_produksi,
                'opname_mutasibarang_produksi' => $opname_mutasibarang_produksi,
                'laporan_produksi' => $laporan_produksi,
                'laporan_mutasiproduksi' => $laporan_mutasiproduksi,
                'laporan_rekapmutasiproduksi' => $laporan_rekapmutasiproduksi,
                'laporan_pemasukanproduksi' => $laporan_pemasukanproduksi,
                'laporan_pengeluaranproduksi' => $laporan_pengeluaranproduksi,
                'laporan_rekappersediaanbarangproduksi' => $laporan_rekappersediaanbarangproduksi,

                //Gudang

                'gudang_menu' => $gudang_menu,
                'gudang_bahan_menu' => $gudang_bahan_menu,
                'gudang_logistik_menu' => $gudang_logistik_menu,
                'gudang_jadi_menu' => $gudang_jadi_menu,
                'gudang_cabang_menu' => $gudang_cabang_menu,
                'pemasukan_gudanglogistik' => $pemasukan_gudanglogisitik,
                'approve_pembelian' => $approve_pembelian
            ];
            View::share($shareddata);
        });
    }
}
