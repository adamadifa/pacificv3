<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use App\Profil;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                $id_user = $auth->user()->id;
                $memo = DB::table('memo')
                    ->selectRaw("memo.id,tanggal,no_memo,judul_memo,kode_dept,kategori,link,totaldownload,name,memo.id_user,cekread.id_user as status_read")
                    ->leftJoin(
                        DB::raw("(
                                SELECT id,COUNT(id_user) as totaldownload FROM memo_download GROUP BY id
                        ) download"),
                        function ($join) {
                            $join->on('memo.id', '=', 'download.id');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(
                            SELECT id,id_user FROM memo_download WHERE id_user='$id_user'
                        ) cekread"),
                        function ($join) {
                            $join->on('memo.id', '=', 'cekread.id');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(
                                SELECT id,name FROM users
                        ) user"),
                        function ($join) {
                            $join->on('memo.id_user', '=', 'user.id');
                        }
                    )
                    ->whereNull('cekread.id_user');

                $memo_unread = $memo->count();
                $memo_data = $memo->get();

                $ticket_pending = DB::table('ticket')->where('status', '!=', 2)->where('id_user', $id_user)->count();
                $ticket_pending_approve = DB::table('ticket')->where('status', 0)->count();
                $ticket_pending_done = DB::table('ticket')->where('status', 1)->count();

                $users = User::select("*")
                    ->whereNotNull('last_seen')
                    ->orderBy('last_seen', 'DESC')
                    ->paginate(10);
            } else {
                $level = "";
                $getcbg = "";
                $id_user = "";
                $memo_unread =  null;
                $memo_data =  null;
                $ticket_pending =  null;
                $ticket_pending_approve =  null;
                $ticket_pending_done =  null;
                $users = null;
            }


            //Dashboard

            $memo_menu = ['admin'];
            $memo_tambah_hapus = ['admin', 'admin medsos', 'manager accounting', 'hrd'];



            $dashboardadmin = ['admin', 'manager marketing', 'general manager', 'direktur'];
            $dashboardkepalapenjualan = ['kepala penjualan'];
            $dashboardkepalaadmin = ['kepala admin', 'admin pusat'];
            $dashboardadminpenjualan = ['admin penjualan'];
            $dashboardaccounting = ['manager accounting', 'spv accounting'];
            $dashboardstaffkeuangan = ['staff keuangan', 'staff keuangan 2', 'staff keuangan 3'];
            $dashboardadminkaskecil = ['admin kas kecil'];
            $dashboardpembelian = ['manager pembelian', 'admin pembelian'];

            //Data Master
            $datamaster = [
                'admin', 'admin penjualan',
                'manager accounting', 'spv accounting', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'manager marketing', 'direktur',
                'manager pembelian', 'admin pembelian', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'audit', 'admin gudang logistik', 'admin gudang bahan',
                'general affair', 'admin garut'
            ];

            $pasar_menu = ['admin', 'admin penjualan', 'kepala admin', 'admin penjualan dan kas kecil', 'direktur', 'manager accounting', 'general manager', 'admin garut'];
            $pasar_tambah = ['admin', 'admin penjualan', 'kepala admin', 'admin penjualan dan kas kecil', 'admin garut'];
            $pasar_hapus = ['admin', 'admin penjualan', 'kepala admin', 'admin penjualan dan kas kecil', 'admin garut'];
            //Pelanggan
            $pelanggan = [
                'admin', 'admin penjualan', 'manager accounting', 'spv accounting', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager marketing', 'direktur', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'audit', 'admin garut'
            ];
            $pelanggan_tambah = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin garut'
            ];
            $pelanggan_edit = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin garut'
            ];
            $pelanggan_hapus = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin garut'
            ];
            $pelanggan_ajuanlimit = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin garut'
            ];

            //Salesman
            $salesman = [
                'admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting', 'manager marketing', 'direktur', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'audit', 'admin garut'
            ];
            $salesman_tambah = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin garut'
            ];
            $salesman_edit = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin garut'
            ];
            $salesman_hapus = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin garut'
            ];

            //Supplier
            $supplier_menu = ['admin', 'manager pembelian', 'admin pembelian', 'manager accounting', 'spv accounting', 'audit'];
            $supplier_tambah = ['admin', 'manager pembelian', 'admin pembelian'];
            $supplier_edit = ['admin', 'manager pembelian', 'admin pembelian'];
            $supplier_hapus = ['admin', 'manager pembelian', 'admin pembelian'];

            //Barang
            $barang = ['admin', 'manager accounting', 'spv accounting', 'direktur', 'manager marketing', 'general manager', 'audit'];
            $barang_tambah = ['admin'];
            $barang_edit = ['admin'];
            $barang_hapus = ['admin'];

            //Barang
            $barangpembelian = [
                'admin', 'manager pembelian', 'admin pembelian', 'manager accounting',
                'spv accounting', 'audit', 'admin gudang logistik', 'admin gudang bahan',
                'general affair'
            ];
            $barangpembelian_tambah = ['admin', 'manager pembelian', 'admin pembelian', 'admin gudang logistik', 'admin gudang bahan', 'general affair'];
            $barangpembelian_edit = ['admin', 'manager pembelian', 'admin pembelian', 'admin gudang logistik', 'admin gudang bahan', 'general affair'];
            $barangpembelian_hapus = ['admin', 'manager pembelian', 'admin pembelian', 'admin gudang logistik', 'admin gudang bahan', 'general affair'];

            //Harga
            $harga = [
                'admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'admin pusat',
                'manager accounting', 'spv accounting', 'manager marketing', 'direktur',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'audit', 'admin garut'
            ];
            $harga_hapus = ['admin'];
            $harga_tambah = ['admin'];
            $harga_edit = [
                'admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'admin pusat',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin garut'
            ];

            $kendaraan = [
                'admin', 'admin penjualan', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting', 'manager marketing', 'direktur',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'audit', 'admin garut'
            ];
            $kendaraan_tambah = ['admin'];
            $kendaraan_edit = [
                'admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'admin pusat',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin garut'
            ];
            $kendaraan_hapus = ['admin'];


            $cabang = ['admin', 'audit'];





            //Marketing
            $marketing = [
                'admin', 'admin penjualan', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting', 'manager marketing',
                'general manager', 'direktur', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'supervisor sales', 'admin gudang cabang dan marketing',
                'staff keuangan 2', 'staff keuangan 3', 'audit', 'kepala gudang', 'admin gudang pusat',
                'admin pajak', 'admin medsos', 'admin garut', 'admin gudang cabang'
            ];

            //-----------------------------OMAN-------------------------------------------------
            $oman = ['admin', 'manager marketing', 'kepala gudang', 'admin gudang pusat', 'kepala admin', 'admin pusat', 'admin garut'];
            $omancabang = ['admin', 'manager marketing', 'kepala gudang', 'admin gudang pusat', 'kepala admin', 'admin pusat', 'admin garut'];
            $omanmarketing = ['admin', 'manager marketing', 'kepala gudang', 'admin gudang pusat'];
            //----------------------------Permintaaan Pengiriman--------------------------------
            $permintaanpengiriman = ['admin', 'admin gudang cabang dan marketing', 'admin garut'];
            $permintaanpengiriman_tambah = ['admin', 'admin gudang cabang dan marketing', 'admin gudang pusat', 'admin garut'];
            $permintaanpengiriman_hapus = ['admin', 'admin gudang cabang dan marketing', 'admin gudang pusat'];
            $permintaanpengiriman_proses = ['admin', 'kepala gudang', 'admin gudang pusat'];
            $permintaanpengiriman_gj = ['admin', 'kepala gudang', 'admin gudang pusat'];
            //----------------------------Target Komisi--------------------------------
            $komisi = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager marketing', 'general manager', 'direktur', 'manager accounting', 'spv accounting'];
            $targetkomisi = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager marketing', 'general manager', 'direktur', 'manager accounting', 'spv accounting'];
            $targetkomisiinput = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat'];
            $generatecashin = ['admin'];
            $ratiokomisi = ['admin', 'kepala admin', 'admin pusat', 'kepala penjualan'];
            $laporan_komisi = ['admin', 'direktur', 'kepala admin', 'admin pusat', 'manager marketing', 'general manager', 'manager accounting', 'spv accounting', 'kepala penjualan'];
            //-----------------------------Penjualan-------------------------------------------
            $penjualan_menu = [
                'admin', 'admin penjualan',
                'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting',
                'manager marketing', 'general manager', 'direktur',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin pajak', 'admin garut', 'audit', 'kepala penjualan'
            ];
            $penjualan_keuangan = [
                'admin', 'admin penjualan', 'kepala admin', 'admin pusat', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'audit'
            ];
            $penjualan_input = [
                'admin', 'admin penjualan', 'kepala admin', 'admin pusat',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin garut'
            ];
            $penjualan_hapus = [
                'admin', 'admin penjualan', 'kepala admin', 'admin pusat',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin garut'
            ];
            $penjualan_edit = [
                'admin', 'admin penjualan', 'kepala admin', 'admin pusat',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin garut'
            ];
            $penjualan_view = [
                'admin', 'admin penjualan', 'kepala admin', 'admin pusat',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin pajak', 'admin garut', 'audit'
            ];
            //Retur
            $retur_view = [
                'admin', 'admin penjualan', 'kepala admin', 'admin pusat', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin garut'
            ];
            //LImit
            $limitkredit_view = [
                'admin', 'admin penjualan',
                'kepala admin', 'admin pusat', 'manager marketing',
                'manager accounting', 'spv accounting', 'general manager',
                'direktur', 'admin penjualan dan kas kecil', 'admin penjualan dan kasir',
                'admin garut', 'audit', 'kepala penjualan'
            ];
            $limitkredit_hapus = ['admin', 'admin penjualan', 'kepala admin', 'admin pusat', 'kepala penjualan', 'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin garut'];
            $limitkredit_analisa = ['admin', 'admin penjualan', 'kepala admin', 'admin pusat', 'kepala penjualan', 'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin garut', 'kepala penjualan'];
            $penyesuaian_limit = ['admin', 'direktur'];
            //Laporan
            $laporan_penjualan = [
                'admin', 'admin penjualan',
                'kepala penjualan', 'kepala admin', 'admin pusat',
                'manager accounting', 'spv accounting', 'manager marketing',
                'manager accounting', 'spv accounting', 'general manager', 'direktur',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir',
                'supervisor sales', 'staff keuangan 2',
                'staff keuangan 3', 'audit', 'admin pajak', 'admin medsos', 'admin garut', 'admin gudang cabang'
            ];
            $harga_net = [
                'admin', 'manager accounting', 'spv accounting',
                'manager marketing', 'general manager', 'direktur', 'audit'
            ];
            //--------------------------------Keuangan---------------------------------------------
            $keuangan = [
                'admin', 'admin penjualan', 'kepala admin', 'admin pusat', 'direktur', 'manager accounting', 'spv accounting', 'general manager',
                'manager marketing', 'kepala penjualan', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin kas kecil', 'kasir', 'admin garut', 'manager pembelian', 'admin pembelian',
                'admin kas', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'kepala gudang', 'audit', 'admin gudang pusat', 'admin garut'
            ];
            $laporankeuangan_view = [
                'admin', 'direktur', 'general manager', 'manager marketing', 'manager accounting', 'spv accounting',
                'kepala penjualan', 'kepala admin', 'admin pusat', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin kas kecil',
                'kasir', 'admin garut', 'admin kas', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'audit'
            ];
            $laporan_ledger = [
                'admin', 'direktur',
                'general manager', 'manager accounting', 'spv accounting', 'admin kas kecil',
                'admin kas', 'kepala penjualan', 'admin persediaan dan kas kecil',
                'admin penjualan dan kas kecil', 'staff keuangan', 'audit'
            ];
            $laporan_kaskecil = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3',
                'admin kas kecil', 'admin kas', 'kepala penjualan',
                'admin penjualan dan kas kecil', 'manager marketing', 'audit', 'admin garut'
            ];

            $laporan_saldokasbesar = [
                'admin', 'direktur',
                'general manager', 'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kasir', 'admin garut', 'admin kas',
                'kepala penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'manager marketing', 'audit'
            ];
            $laporan_lpu = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat', 'staff keuangan',
                'staff keuangan 2', 'staff keuangan 3', 'kasir', 'admin garut', 'admin kas', 'kepala penjualan',
                'admin persediaan dan kasir', 'admin penjualan dan kasir', 'manager marketing', 'audit'
            ];
            $laporan_penjualan_keuangan = [
                'admin', 'direktur',
                'general manager', 'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kasir', 'admin garut', 'admin kas',
                'kepala penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'manager marketing', 'audit'
            ];
            $laporan_uanglogam = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3',
                'kasir', 'admin garut', 'admin kas', 'kepala penjualan', 'admin persediaan dan kasir',
                'admin penjualan dan kasir', 'manager marketing', 'audit'
            ];
            $laporan_rekapbg = [
                'admin', 'direktur',
                'general manager', 'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kasir', 'admin garut', 'admin kas',
                'kepala penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'manager marketing', 'audit'
            ];
            //Giro
            $giro_view = ['admin', 'admin penjualan', 'kepala admin', 'admin pusat', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'audit'];
            $giro_approved = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'manager keuangan'];
            $giro_hapus = ['admin', 'admin penjualan', 'kepala admin'];

            //Transfer
            $transfer_view = ['admin', 'admin penjualan', 'kepala admin', 'admin pusat', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'audit'];
            $transfer_approved =  ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'manager keuangan'];

            //Kas Kecil
            $kaskecil_menu  = [
                'admin', 'staff keuangan',
                'staff keuangan 2', 'staff keuangan 3',
                'kepala admin', 'admin pusat', 'admin kas kecil', 'admin kas', 'admin persediaan dan kas kecil',
                'admin penjualan dan kas kecil', 'admin garut', 'manager accounting', 'spv accounting'
            ];
            $kaskecil_view = [
                'admin', 'kepala admin', 'admin pusat', 'admin kas kecil', 'admin kas',
                'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil',
                'staff keuangan 3', 'admin garut', 'spv accounting'
            ];
            $klaim_view = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kepala admin', 'admin pusat', 'admin kas kecil', 'admin kas', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil', 'admin garut', 'admin garut', 'manager accounting'];
            $klaim_add = ['admin', 'kepala admin', 'admin pusat', 'admin kas kecil', 'admin kas', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil', 'staff keuangan 3', 'admin garut'];
            $klaim_hapus = ['admin', 'kepala admin', 'admin pusat', 'admin kas kecil', 'admin kas', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil', 'staff keuangan 3', 'admin garut'];
            $klaim_validasi = ['admin', 'kepala admin', 'admin pusat', 'admin kas kecil', 'admin kas', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil', 'staff keuangan 3', 'admin garut'];
            $klaim_proses = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'manager accounting'];

            //Mutasi Bank
            $mutasibank_view = ['admin', 'kepala admin', 'admin pusat', 'admin kas kecil', 'admin kas', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil'];

            //Ledger
            $ledger_menu  = ['admin', 'staff keuangan', 'spv accounting'];
            $ledger_view = ['admin', 'staff keuangan', 'spv accounting'];
            $ledger_saldoawal = ['admin', 'staff keuangan'];

            //Kas Besar Keuangan
            $kasbesar_menu  = [
                'admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kepala admin', 'admin pusat',
                'kasir', 'admin garut', 'admin kas', 'admin persediaan dan kasir',
                'admin penjualan dan kasir', 'audit'
            ];
            $saldoawalkasbesar_view = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin garut'];
            $setoran_menu = [
                'admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kepala admin',
                'admin pusat', 'kasir', 'admin garut', 'admin kas', 'admin persediaan dan kasir',
                'admin penjualan dan kasir', 'audit'
            ];
            $setoranpenjualan_view = [
                'admin', 'staff keuangan', 'staff keuangan 2',
                'staff keuangan 3', 'kepala admin', 'admin pusat', 'kasir',
                'admin garut', 'admin kas', 'admin persediaan dan kasir',
                'admin penjualan dan kasir', 'audit'
            ];
            $setoranpusat_view = [
                'admin', 'staff keuangan',
                'staff keuangan 2', 'staff keuangan 3', 'kepala admin',
                'admin pusat', 'kasir', 'admin garut', 'admin kas',
                'admin persediaan dan kasir', 'admin penjualan dan kasir', 'audit'
            ];
            $setoranpusat_add = ['admin', 'kasir', 'admin garut', 'kepala admin', 'admin pusat', 'kasir', 'admin garut', 'admin kas', 'admin persediaan dan kasir', 'admin penjualan dan kasir'];
            $setoranpusat_edit = ['admin', 'kasir', 'admin garut', 'kepala admin', 'admin pusat', 'kasir', 'admin garut', 'admin kas', 'admin persediaan dan kasir', 'admin penjualan dan kasir'];
            $setoranpusat_hapus = ['admin', 'kasir', 'admin garut', 'kepala admin', 'admin pusat', 'kasir', 'admin garut', 'admin kas', 'admin persediaan dan kasir', 'admin penjualan dan kasir'];
            $setoranpusat_terimasetoran = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3'];
            $setorangiro_view = ['admin', 'kepala admin', 'admin pusat', 'kasir', 'admin garut', 'admin kas', 'admin persediaan dan kasir', 'admin penjualan dan kasir'];
            $setorantransfer_view = ['admin', 'kepala admin', 'admin pusat', 'kasir', 'admin garut', 'admin kas', 'admin persediaan dan kasir', 'admin penjualan dan kasir'];
            $belum_disetorkan = ['admin', 'kepala admin', 'admin pusat', 'kasir', 'admin garut', 'admin kas', 'admin persediaan dan kasir', 'admin penjualan dan kasir'];
            $lebih_disetorkan = ['admin', 'kepala admin', 'admin pusat', 'kasir', 'admin garut', 'admin kas', 'admin persediaan dan kasir', 'admin penjualan dan kasir'];
            $gantilogamtokertas = ['admin', 'kepala admin', 'admin pusat', 'kasir', 'admin garut', 'admin kas', 'admin persediaan dan kasir', 'admin penjualan dan kasir'];

            $saldoawalpiutang = ['admin'];

            $kirimlpc = ['admin', 'admin penjualan', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'direktur'];
            $kirimlpc_tambah = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting'];
            $kirimlpc_edit = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting'];
            $kirimlpc_hapus = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting'];
            $kirimlpc_approve = ['admin', 'manager accounting', 'spv accounting'];




            //Pembelian
            $pembelian_menu = [
                'admin', 'direktur', 'general manager', 'manager accounting',
                'spv accounting', 'manager pembelian', 'admin pembelian',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3',
                'emf', 'admin pajak', 'admin gudang logistik', 'admin pusat', 'spv accounting'
            ];
            $pembelian_view = [
                'admin', 'manager pembelian', 'admin pembelian',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin pajak', 'admin gudang logistik', 'spv accounting'
            ];
            $pembelian_hapus = ['admin', 'manager pembelian', 'admin pembelian'];
            $pembelian_tambah = ['admin', 'manager pembelian', 'admin pembelian'];
            $pembelian_edit = ['admin', 'manager pembelian', 'admin pembelian', 'spv accounting'];


            $pembelian_keuangan = ['admin', 'manager pembelian', 'admin pembelian', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3'];
            $kontrabon_view = ['admin', 'manager pembelian', 'admin pembelian', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3'];
            $kontrabon_edit_hapus = ['admin', 'admin pembelian'];
            $kontrabon_approve = ['admin', 'manager pembelian'];
            $kontrabon_proses = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3'];

            $jatuhtempo_view = ['admin', 'manager pembelian', 'admin pembelian'];
            $jurnalkoreksi_view = ['admin', 'manager pembelian', 'admin pembelian'];
            $laporan_pembelian = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian',
                'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_pembayaran_pembelian = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian',
                'emf', 'admin pusat'
            ];
            $laporan_rekappembeliansupplier = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian',
                'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_rekappembelian = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_kartuhutang = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_auh = [
                'admin', 'direktur', 'general manager', 'manager accounting',
                'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_bahankemasan = ['admin', 'direktur', 'general manager', 'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'];
            $laporan_rekapbahankemasan = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_jurnalkoreksi = [
                'admin', 'direktur', 'general manager', 'manager accounting', 'spv accounting',
                'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_rekapakunpembelian  = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_rekapkontrabon = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];


            $produksi_menu = [
                'admin', 'direktur', 'manager accounting', 'spv accounting',
                'admin produksi', 'audit', 'admin produksi 2', 'kepala gudang', 'admin gudang pusat', 'emf', 'admin pdqc'
            ];
            $produksi_analytics = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'admin produksi', 'emf', 'admin pdqc'];
            $mutasi_produk = ['admin', 'admin produksi 2', 'admin produksi'];
            $bpbj_view = ['admin', 'admin produksi'];
            $fsthp_view = ['admin', 'admin produksi 2', 'admin produksi'];
            $mutasi_barang = ['admin', 'admin produksi'];
            $pemasukan_produksi = ['admin', 'admin produksi'];
            $pengeluaran_produksi = ['admin', 'admin produksi'];
            $saldoawal_mutasibarang_produksi = ['admin', 'admin produksi'];
            $opname_mutasibarang_produksi = ['admin', 'admin produksi'];
            $laporan_produksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'admin produksi', 'emf', 'admin pdqc'];
            $laporan_mutasiproduksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'admin produksi', 'emf', 'admin pdqc'];
            $laporan_rekapmutasiproduksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'admin produksi', 'emf', 'admin pdqc'];
            $laporan_pemasukanproduksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'admin produksi', 'emf', 'admin pdqc'];
            $laporan_pengeluaranproduksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'admin produksi', 'emf', 'admin pdqc'];
            $laporan_rekappersediaanbarangproduksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'admin produksi', 'emf', 'admin pdqc'];

            //Gudang
            $gudang_menu = [
                'admin', 'admin gudang cabang', 'admin garut',
                'admin gudang', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'kepala penjualan',
                'admin gudang', 'kepala admin', 'admin pusat', 'supervisor sales', 'kepala gudang',
                'admin gudang cabang dan marketing', 'direktur', 'manager accounting',
                'spv accounting', 'manager marketing', 'general manager', 'audit', 'admin gudang pusat', 'emf', 'admin gudang logistik',
                'admin gudang bahan', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc'
            ];
            $gudang_bahan_menu = ['admin', 'kepala gudang', 'kepala gudang', 'admin gudang bahan'];
            $gudang_logistik_menu =  ['admin', 'kepala gudang', 'kepala gudang', 'admin gudang logistik'];
            $gudang_jadi_menu =  ['admin', 'kepala gudang', 'kepala gudang', 'admin gudang pusat'];
            $gudang_cabang_menu = [
                'admin',
                'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut',
                'admin persediaan dan kasir', 'admin persediaan dan kas kecil',
                'admin gudang cabang dan marketing', 'audit'
            ];
            $laporan_gudang = [
                'admin', 'kepala admin', 'admin pusat',
                'admin gudang cabang', 'admin garut', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'kepala penjualan', 'supervisor sales',
                'admin gudang cabang dan marketing', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'general manager',
                'audit', 'admin gudang pusat', 'emf', 'admin gudang logistik', 'admin gudang bahan',
                'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc'
            ];
            $laporan_gudang_logistik = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'audit', 'admin gudang pusat', 'emf',
                'admin gudang logistik', 'admin pusat'
            ];
            $laporan_gudang_bahan = [
                'admin', 'kepala gudang', 'direktur', 'manager accounting',
                'spv accounting', 'audit', 'admin gudang pusat',
                'emf', 'admin gudang bahan', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc'
            ];
            $laporan_gudang_jadi = [
                'admin', 'kepala gudang', 'admin gudang pusat', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing',
                'general manager', 'audit', 'emf', 'admin pembelian', 'manager pembelian', 'admin pdqc'
            ];
            $laporan_gudang_cabang = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'supervisor sales',
                'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'audit', 'emf'
            ];


            //Gudang Logistik
            $pemasukan_gudanglogisitik = ['admin', 'kepala gudang', 'admin gudang logistik'];
            $approve_pembelian = ['admin', 'kepala gudang', 'admin gudang logistik'];
            $pengeluaran_gudanglogistik = ['admin', 'kepala gudang', 'admin gudang logistik'];
            $saldoawal_gudanglogistik = ['admin', 'kepala gudang', 'admin gudang logistik'];
            $opname_gudanglogistik = ['admin', 'kepala gudang', 'admin gudang logistik'];



            //Gudang Bahan
            $pemasukan_gudangbahan = ['admin', 'kepala gudang', 'admin gudang bahan'];
            $pengeluaran_gudangbahan = ['admin', 'kepala gudang', 'admin gudang bahan'];
            $saldoawal_gudangbahan = ['admin', 'kepala gudang', 'admin gudang bahan'];
            $opname_gudangbahan = ['admin', 'kepala gudang', 'admin gudang bahan'];

            //Gudang Jadi Pusat
            $permintaan_produksi_view = ['admin', 'kepala gudang', 'admin gudang pusat', 'kepala gudang', 'admin produksi'];
            $mutasi_produk_gj = ['admin', 'kepala gudang', 'admin gudang pusat'];
            $fsthp_gj_view = ['admin', 'kepala gudang', 'admin gudang pusat'];
            $suratjalan_view = ['admin', 'kepala gudang', 'admin gudang pusat'];
            $suratjalan_cetak = ['admin', 'kepala gudang', 'admin gudang pusat'];
            $suratjalan_hapus = ['admin', 'kepala gudang', 'admin gudang pusat'];
            $repackgj_view = ['admin', 'kepala gudang', 'admin gudang pusat'];
            $rejectgj_view = ['admin', 'kepala gudang', 'admin gudang pusat'];
            $lainnyagj_view = ['admin', 'kepala gudang', 'admin gudang pusat'];
            $angkutan_view = ['admin', 'kepala gudang', 'admin gudang pusat'];

            //Kontrabon Angkutan

            $gudang_jadi_keuangan = ['admin', 'kepala gudang', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin gudang pusat'];
            $kontrabon_angkutan_view = ['admin', 'kepala gudang', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin gudang pusat'];
            $kontrabon_angkutan_hapus = ['admin', 'kepala gudang', 'admin gudang pusat'];

            //Gudang Cabang

            $saldoawal_gs_view = ['admin', 'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin gudang cabang dan marketing', 'audit'];
            $saldoawal_bs_view = ['admin', 'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin gudang cabang dan marketing', 'audit'];
            $dpb_view = ['admin', 'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin gudang cabang dan marketing', 'audit'];
            $mutasi_barang_cab_view = ['admin', 'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin gudang cabang dan marketing', 'audit'];
            $suratjalancab_view = ['admin', 'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin gudang cabang dan marketing', 'audit'];


            //Laporan Gudang Logistik
            $laporan_pemasukan_gl = [
                'admin', 'kepala gudang', 'admin gudang logistik', 'direktur',
                'manager accounting', 'spv accounting', 'audit', 'admin gudang pusat', 'emf', 'admin pusat'
            ];
            $laporan_pengeluaran_gl = [
                'admin', 'kepala gudang', 'admin gudang logistik', 'direktur',
                'manager accounting', 'spv accounting', 'audit', 'admin gudang pusat', 'emf', 'admin pusat'
            ];
            $laporan_persediaan_gl = [
                'admin', 'kepala gudang', 'admin gudang logistik', 'direktur',
                'manager accounting', 'spv accounting', 'audit', 'admin gudang pusat', 'emf', 'admin pusat'
            ];
            $laporan_persediaanopname_gl = [
                'admin', 'kepala gudang', 'admin gudang logistik',
                'direktur', 'manager accounting', 'spv accounting', 'audit', 'admin gudang pusat', 'emf', 'admin pusat'
            ];

            //Laporan Gudang Bahan
            $laporan_pemasukan_gb = [
                'admin', 'kepala gudang', 'admin gudang bahan',
                'direktur', 'manager accounting', 'spv accounting',
                'audit', 'admin gudang pusat', 'emf', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc'
            ];
            $laporan_pengeluaran_gb = [
                'admin', 'kepala gudang', 'admin gudang bahan', 'direktur',
                'manager accounting', 'spv accounting', 'audit', 'admin gudang pusat',
                'emf', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc'
            ];
            $laporan_persediaan_gb = [
                'admin', 'kepala gudang', 'admin gudang bahan',
                'direktur', 'manager accounting', 'spv accounting', 'audit',
                'admin gudang pusat', 'emf', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc'
            ];
            $laporan_kartugudang = [
                'admin', 'kepala gudang', 'admin gudang bahan',
                'direktur', 'manager accounting', 'spv accounting', 'audit',
                'emf', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc'
            ];
            $laporan_rekappersediaan = [
                'admin', 'kepala gudang',
                'admin gudang bahan', 'direktur', 'manager accounting',
                'spv accounting', 'audit', 'emf', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc'
            ];

            //Laporan Gudang Jadi
            $laporan_persediaan_gj = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'audit', 'admin gudang pusat', 'emf', 'admin pdqc'
            ];
            $rekap_persediaan_gj = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting',
                'manager marketing', 'general manager', 'audit', 'admin gudang pusat', 'emf',
                'admin pembelian', 'manager pembelian', 'admin pdqc'
            ];
            $rekap_hasiproduksi_gj = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'audit', 'admin gudang pusat', 'emf', 'admin pdqc'
            ];
            $rekap_pengeluaran_gj = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'audit', 'admin gudang pusat', 'emf', 'admin pdqc'
            ];
            $realisasi_kiriman_gj = [
                'admin', 'kepala gudang', 'direktur', 'manager accounting', 'spv accounting',
                'manager marketing', 'general manager', 'audit', 'admin gudang pusat', 'emf', 'admin pdqc'
            ];
            $realisasi_oman_gj = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'audit', 'admin gudang pusat', 'emf', 'admin pdqc'
            ];
            $laporan_angkutan = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'audit', 'admin gudang pusat', 'emf', 'admin pdqc'
            ];

            //Laporan Gudang  Cabang

            $laporan_persediaan_bj = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut',
                'admin persediaan dan kasir', 'admin persediaan dan kas kecil',
                'supervisor sales', 'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'audit', 'emf'
            ];
            $laporan_badstok_bj = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut',
                'admin persediaan dan kasir', 'admin persediaan dan kas kecil',
                'supervisor sales', 'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'audit', 'emf'
            ];
            $laporan_rekap_bj = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'supervisor sales',
                'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'audit', 'emf'
            ];
            $laporan_mutasidpb = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'supervisor sales',
                'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'audit', 'emf'
            ];
            $laporan_rekonsiliasibj = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin garut', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'supervisor sales',
                'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'general manager', 'audit', 'emf'
            ];

            //Acounting
            $accounting_menu = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'general affair', 'hrd', 'kepala admin', 'kepala penjualan'];
            $coa_menu = ['admin', 'spv accounting', 'manager accounting'];
            $setcoacabang = ['admin', 'spv accounting', 'manager accounting'];
            $hpp_menu = ['admin', 'manager accounting', 'spv accounting'];
            $hpp_input = ['admin', 'manager accounting', 'spv accounting'];
            $hargaawal_input = ['admin', 'manager accounting', 'spv accounting'];
            $saldoawal_bukubesar_menu = ['admin', 'manager accounting', 'spv accounting'];
            $jurnalumum_menu = ['admin', 'manager accounting', 'spv accounting', 'general affair', 'hrd'];
            $costratio_menu = ['admin', 'manager accounting', 'spv accounting', 'kepala admin', 'kepala penjualan'];
            $laporan_accounting = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'general affair', 'hrd', 'kepala admin', 'kepala penjualan'];
            $laporan_rekapbj_acc = ['admin', 'direktur', 'manager accounting', 'spv accounting'];
            $laporan_bukubesar = ['admin', 'direktur', 'manager accounting', 'spv accounting'];
            $laporan_jurnalumum = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'general affair', 'hrd'];
            $laporan_costratio = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'general affair', 'hrd', 'kepala admin', 'kepala penjualan'];


            //Maintenance

            $maintenance_menu  = [
                'admin', 'admin maintenance', 'spv accounting',
                'manager accounting', 'direktur', 'general manager', 'admin pusat'
            ];
            $maintenance_pembelian = ['admin', 'admin maintenance'];
            $maintenance_pemasukan = ['admin', 'admin maintenance'];

            $maintenance_pengeluaran = ['admin', 'admin maintenance'];
            $laporan_maintenance = [
                'admin', 'admin maintenance', 'spv accounting',
                'manager accounting', 'direktur', 'general manager', 'admin pusat'
            ];

            $tutuplaporan = ['admin', 'manager accounting'];
            $ticket_hapus = ['manager accounting'];
            $ticket_approve = ['admin', 'manager accounting'];
            $ticket_done = ['admin'];
            $shareddata = [
                'level' => $level,
                'getcbg' => $getcbg,
                'memo_unread' => $memo_unread,
                'memo_data' => $memo_data,
                'memo_menu' => $memo_menu,
                'memo_tambah_hapus' => $memo_tambah_hapus,

                'ticket_pending' => $ticket_pending,
                'ticket_pending_approve' => $ticket_pending_approve,
                'ticket_pending_done' => $ticket_pending_done,
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

                'pasar_menu' => $pasar_menu,
                'pasar_tambah' => $pasar_tambah,
                'pasar_hapus' => $pasar_hapus,
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
                'supplier_tambah' => $supplier_tambah,
                'supplier_edit' => $supplier_edit,
                'supplier_hapus' => $supplier_hapus,

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
                'permintaanpengiriman_tambah' => $permintaanpengiriman_tambah,
                'permintaanpengiriman_hapus' => $permintaanpengiriman_hapus,
                'permintaanpengiriman_proses' => $permintaanpengiriman_proses,
                'permintaanpengiriman_gj' => $permintaanpengiriman_gj,
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
                'penjualan_hapus' => $penjualan_hapus,
                'penjualan_edit' => $penjualan_edit,
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
                'giro_hapus' => $giro_hapus,
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
                'gantilogamtokertas' => $gantilogamtokertas,
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
                'pembelian_hapus' => $pembelian_hapus,
                'pembelian_edit' => $pembelian_edit,
                'pembelian_tambah' => $pembelian_tambah,
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

                'laporan_gudang_logistik' => $laporan_gudang_logistik,
                'laporan_gudang_bahan' => $laporan_gudang_bahan,
                'laporan_gudang_jadi' => $laporan_gudang_jadi,
                'laporan_gudang_cabang' => $laporan_gudang_cabang,


                //Gudang Logistik
                'pemasukan_gudanglogistik' => $pemasukan_gudanglogisitik,
                'approve_pembelian' => $approve_pembelian,
                'pengeluaran_gudanglogistik' => $pengeluaran_gudanglogistik,
                'saldoawal_gudanglogistik' => $saldoawal_gudanglogistik,
                'opname_gudanglogistik' => $opname_gudanglogistik,


                //Gudang Bahan
                'pemasukan_gudangbahan' => $pemasukan_gudangbahan,
                'pengeluaran_gudangbahan' => $pengeluaran_gudangbahan,
                'saldoawal_gudangbahan' => $saldoawal_gudangbahan,
                'opname_gudangbahan' => $opname_gudangbahan,

                //Gudang Jadi
                'permintaan_produksi_view' => $permintaan_produksi_view,
                'mutasi_produk_gj' => $mutasi_produk_gj,
                'fsthp_gj_view' => $fsthp_gj_view,
                'suratjalan_view' => $suratjalan_view,
                'suratjalan_cetak' => $suratjalan_cetak,
                'suratjalan_hapus' => $suratjalan_hapus,
                'repackgj_view' => $repackgj_view,
                'rejectgj_view' => $rejectgj_view,
                'lainnyagj_view' => $lainnyagj_view,
                'angkutan_view' => $angkutan_view,
                //Kontrabon Angkutan
                'gudang_jadi_keuangan' => $gudang_jadi_keuangan,
                'kontrabon_angkutan_view' => $kontrabon_angkutan_view,
                'kontrabon_angkutan_hapus' => $kontrabon_angkutan_hapus,
                //Gudang Cabang
                'saldoawal_gs_view' => $saldoawal_gs_view,
                'saldoawal_bs_view' => $saldoawal_bs_view,
                'dpb_view' => $dpb_view,
                'suratjalancab_view' => $suratjalancab_view,
                'mutasi_barang_cab_view' => $mutasi_barang_cab_view,

                //Laporan Gudang Logistik
                'laporan_gudang' => $laporan_gudang,
                'laporan_pemasukan_gl' => $laporan_pemasukan_gl,
                'laporan_pengeluaran_gl' => $laporan_pengeluaran_gl,
                'laporan_persediaan_gl' => $laporan_persediaan_gl,
                'laporan_persediaanopname_gl' => $laporan_persediaanopname_gl,

                //Laporan Gudang Bahan
                'laporan_pemasukan_gb' => $laporan_pemasukan_gb,
                'laporan_pengeluaran_gb' => $laporan_pengeluaran_gb,
                'laporan_persediaan_gb' => $laporan_persediaan_gb,
                'laporan_kartugudang' => $laporan_kartugudang,
                'laporan_rekappersediaan' => $laporan_rekappersediaan,

                //Laporan Gudang Jadi
                'laporan_persediaan_gj' => $laporan_persediaan_gj,
                'rekap_persediaan_gj' => $rekap_persediaan_gj,
                'rekap_hasiproduksi_gj' => $rekap_hasiproduksi_gj,
                'rekap_pengeluaran_gj' => $rekap_pengeluaran_gj,
                'realisasi_kiriman_gj' => $realisasi_kiriman_gj,
                'realisasi_oman_gj' => $realisasi_oman_gj,
                'laporan_angkutan' => $laporan_angkutan,

                //Laporan Gudang Jadi
                'laporan_persediaan_gj' => $laporan_persediaan_gj,
                'rekap_persediaan_gj' => $rekap_persediaan_gj,
                'rekap_hasiproduksi_gj' => $rekap_hasiproduksi_gj,
                'rekap_pengeluaran_gj' => $rekap_pengeluaran_gj,
                'realisasi_kiriman_gj' => $realisasi_kiriman_gj,
                'realisasi_oman_gj' => $realisasi_oman_gj,
                'laporan_angkutan' => $laporan_angkutan,

                'laporan_persediaan_bj' => $laporan_persediaan_bj,
                'laporan_badstok_bj' => $laporan_badstok_bj,
                'laporan_rekap_bj' => $laporan_rekap_bj,
                'laporan_mutasidpb' => $laporan_mutasidpb,
                'laporan_rekonsiliasibj' => $laporan_rekonsiliasibj,

                //Acounting
                'accounting_menu' => $accounting_menu,
                'coa_menu' => $coa_menu,
                'setcoacabang' => $setcoacabang,
                'hpp_menu' => $hpp_menu,
                'hpp_input' => $hpp_input,
                'hargaawal_input' => $hargaawal_input,
                'saldoawal_bukubesar_menu' => $saldoawal_bukubesar_menu,
                'jurnalumum_menu' => $jurnalumum_menu,
                'costratio_menu' => $costratio_menu,
                'laporan_accounting' => $laporan_accounting,
                'laporan_rekapbj_acc' => $laporan_rekapbj_acc,
                'laporan_bukubesar' => $laporan_bukubesar,
                'laporan_jurnalumum' => $laporan_jurnalumum,
                'laporan_costratio' => $laporan_costratio,

                'maintenance_menu' => $maintenance_menu,
                'maintenance_pembelian' => $maintenance_pembelian,
                'maintenance_pemasukan' => $maintenance_pemasukan,
                'maintenance_pengeluaran' => $maintenance_pengeluaran,
                'laporan_maintenance' => $laporan_maintenance,

                'tutuplaporan' => $tutuplaporan,

                'ticket_hapus' => $ticket_hapus,
                'ticket_approve' => $ticket_approve,
                'ticket_done' => $ticket_done,

                'users' => $users

            ];
            View::share($shareddata);
        });
    }
}
