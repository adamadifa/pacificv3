<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\DaftarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GiroController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\HargaController;
use App\Http\Controllers\HargaControoler;
use App\Http\Controllers\JenissimpananController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\LimitkreditController;
use App\Http\Controllers\LpcController;
use App\Http\Controllers\MutasigudangcabangController;
use App\Http\Controllers\OmancabangController;
use App\Http\Controllers\OmanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PermintaanpengirimanController;
use App\Http\Controllers\RatiokomisiController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\SalesmanController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\TabunganController;
use App\Http\Controllers\TargetkomisiController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TutuplaporanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('Auth.login');
})->name('login');


Route::post('/postlogin', [AuthController::class, 'postlogin']);
Route::post('/postlogout', [AuthController::class, 'postlogout']);


//Tutup Laporan
Route::post('/cektutuplaporan', [TutuplaporanController::class, 'cektutuplaporan']);
//Cek Barang Penjualan Temporary
Route::post('/cekpenjtemp', [PenjualanController::class, 'cekpenjtemp']);

//Load Data
//Salesman
Route::post('/salesman/getsalescab', [SalesmanController::class, 'getsalescab']);
//Pelanggan
Route::post('/pelanggan/getpelanggansalesman', [PelangganController::class, 'getpelanggansalesman']);
//Kendaraan
Route::post('/kendaraan/getkendaraancab', [KendaraanController::class, 'getkendaraancab']);
//LoadStokCabang
Route::post('/getsaldogudangcabang', [MutasigudangcabangController::class, 'getsaldogudangcabang']);
Route::post('/getsaldogudangcabangbs', [MutasigudangcabangController::class, 'getsaldogudangcabangbs']);
//Oman Cabang
Route::middleware(['auth', 'ceklevel:admin,kepala admin,kepala penjualan,manager marketing,manager accounting,direktur'])->group(function () {
    Route::get('/omancabang', [OmancabangController::class, 'index']);
    Route::get('/omancabang/create', [OmancabangController::class, 'create']);
    Route::post('/cekomancabang', [OmancabangController::class, 'cekomancabang']);
    Route::post('/omancabang/show', [OmancabangController::class, 'show']);
    Route::post('/omancabang/store', [OmancabangController::class, 'store']);
    Route::get('/omancabang/{no_order}/edit', [OmancabangController::class, 'edit']);
    Route::post('/omancabang/{no_order}/update', [OmancabangController::class, 'update']);
    Route::delete('/omancabang/{no_order}/delete', [OmancabangController::class, 'delete']);
    Route::post('/getomancabang', [OmancabangController::class, 'getomancabang']);
});
//Oman Marketing
Route::middleware(['auth', 'ceklevel:admin,manager marketing,manager accounting,direktur'])->group(function () {
    Route::get('/oman', [OmanController::class, 'index']);
    Route::get('/oman/create', [OmanController::class, 'create']);
    Route::post('/cekoman', [OmanController::class, 'cekoman']);
    Route::post('/oman/store', [OmanController::class, 'store']);
    Route::get('/oman/{no_order}/edit', [OmanController::class, 'edit']);
    Route::post('/oman/{no_order}/update', [OmanController::class, 'update']);
    Route::delete('/oman/{no_order}/delete', [OmanController::class, 'delete']);
    Route::post('/oman/show', [OmanController::class, 'show']);
});


Route::middleware(['auth', 'ceklevel:admin'])->group(function () {
    //Permintaan Pengiriman
    Route::get('/permintaanpengiriman', [PermintaanpengirimanController::class, 'index']);
    Route::get('/permintaanpengiriman/cektemp', [PermintaanpengirimanController::class, 'cektemp']);
    Route::post('/permintaanpengiriman/storetemp', [PermintaanpengirimanController::class, 'storetemp']);
    Route::post('/permintaanpengiriman/deletetemp', [PermintaanpengirimanController::class, 'deletetemp']);
    Route::get('/permintaanpengiriman/showtemp', [PermintaanpengirimanController::class, 'showtemp']);
    Route::post('/permintaanpengiriman/store', [PermintaanpengirimanController::class, 'store']);
    Route::post('/permintaanpengiriman/buatnopermintaan', [PermintaanpengirimanController::class, 'buatnopermintaan']);
    Route::delete('/permintaanpengiriman/{no_permintaan_pengiriman}/delete', [PermintaanpengirimanController::class, 'delete']);

    //Ratio Komisi
    Route::get('/ratiokomisi', [RatiokomisiController::class, 'index']);
    Route::post('/ratiokomisi/getratiokomisi', [RatiokomisiController::class, 'getratiokomisi']);
    Route::post('/ratiokomisi/store', [RatiokomisiController::class, 'store']);
});




//Administrator | Manager Accounting | Manager Marketing | General Manager | Direktur
Route::middleware(['auth', 'ceklevel:admin,manager accounting,manager marketing,general manager,direktur'])->group(function () {
    Route::get('/dashboardadmin', [DashboardController::class, 'dashboardadmin']);

    //Dashboard
    Route::post('/rekapcashin', [PenjualanController::class, 'rekapcashin']);

    Route::post('/dpppdashboard', [PenjualanController::class, 'dpppdashboard']);
    Route::post('/rekapkendaraandashboard', [KendaraanController::class, 'rekapkendaraandashboard']);
    Route::get('/rekappersediaandashboard', [GudangController::class, 'rekappersediaandashboard']);

    //Barang
    Route::get('/barang', [BarangController::class, 'index']);
    Route::get('/barang/create', [BarangController::class, 'create']);
    Route::post('/barang/store', [BarangController::class, 'store']);
    Route::get('/barang/{kode_produk}/edit', [BarangController::class, 'edit']);
    Route::post('/barang/{kode_produk}/update', [BarangController::class, 'update']);
    Route::delete('/barang/{kode_produk}/delete', [BarangController::class, 'delete']);

    //Kendaraan
    Route::get('/kendaraan', [KendaraanController::class, 'index']);
    Route::get('/kendaraan/create', [KendaraanController::class, 'create']);
    Route::post('/kendaraan/store', [KendaraanController::class, 'store']);
    Route::delete('/kendaraan/{id}/delete', [KendaraanController::class, 'delete']);
    Route::get('/kendaraan/{id}/edit', [KendaraanController::class, 'edit']);
    Route::post('/kendaraan/{id}/update', [KendaraanController::class, 'update']);
    Route::post('/kendaraan/show', [KendaraanController::class, 'show']);


    //Cabang
    Route::get('/cabang', [CabangController::class, 'index']);
    Route::get('/cabang/create', [CabangController::class, 'create']);
    Route::post('/cabang/store', [CabangController::class, 'store']);
    Route::get('/cabang/{kode_cabang}/edit', [CabangController::class, 'edit']);
    Route::post('/cabang/{kode_cabang}/update', [CabangController::class, 'update']);
    Route::delete('/cabang/{kode_cabang}/delete', [CabangController::class, 'delete']);
});


Route::middleware(['auth', 'ceklevel:admin penjualan'])->group(function () {
    Route::get('/dashboardadminpenjualan', [DashboardController::class, 'dashboardadminpenjualan']);
});

Route::middleware(['auth', 'ceklevel:kepala penjualan'])->group(function () {
    Route::get('/dashboardkepalapenjualan', [DashboardController::class, 'dashboardkepalapenjualan']);
});


Route::middleware(['auth', 'ceklevel:kepala admin'])->group(function () {
    Route::get('/dashboardkepalaadmin', [DashboardController::class, 'dashboardkepalaadmin']);
});

Route::middleware(['auth', 'ceklevel:manager accounting'])->group(function () {
    Route::get('/dashboardaccounting', [DashboardController::class, 'dashboardaccounting']);
});

//Admin Penjualan | Kepala Penjualan | Kepala Cabang | Manager Accounting | Manager Marketing | General manager | Direktur
Route::middleware(['auth', 'ceklevel:admin,admin penjualan,kepala penjualan,kepala admin,manager accounting,manager marketing,general manager,direktur'])->group(function () {

    //Limit Kredit
    Route::get('/limitkredit', [LimitkreditController::class, 'index']);
    Route::get('/limitkredit/{no_pengajuan}/cetak', [LimitkreditController::class, 'cetak']);
    Route::post('/limitkredit/create_uraiananalisa', [LimitkreditController::class, 'create_uraiananalisa']);
    Route::post('/limitkredit/store_uraiananalisa', [LimitkreditController::class, 'store_uraiananalisa']);
    Route::get('/limitkredit/{no_pengajuan}/approve', [LimitkreditController::class, 'approve']);
    Route::get('/limitkredit/{no_pengajuan}/decline', [LimitkreditController::class, 'decline']);

    //Penjualan
    //Laporan Penjualan
    Route::get('/laporanpenjualan/penjualan', [PenjualanController::class, 'laporanpenjualan']);
    Route::post('/laporanpenjualan/cetak', [PenjualanController::class, 'cetaklaporanpenjualan']);
    //Laporan Retur
    Route::get('/laporanretur', [ReturController::class, 'laporanretur']);
    Route::post('/laporanretur/cetak', [ReturController::class, 'cetaklaporanretur']);
    //Laporan Kas Besar Penjualan
    Route::get('/laporankasbesarpenjualan', [PembayaranController::class, 'laporankasbesarpenjualan']);
    Route::post('/laporankasbesarpenjualan/cetak', [PembayaranController::class, 'cetaklaporankasbesarpenjualan']);
    //Laporan Tunai Kredit
    Route::get('/laporanpenjualan/tunaikredit', [PenjualanController::class, 'laporantunaikredit']);
    Route::post('/laporanpenjualan/tunaikredit/cetak', [PenjualanController::class, 'cetaklaporantunaikredit']);
    //Laporan Kartu Piutang
    Route::get('/laporanpenjualan/kartupiutang', [PenjualanController::class, 'laporankartupiutang']);
    Route::post('/laporanpenjualan/kartupiutang/cetak', [PenjualanController::class, 'cetaklaporankartupiutang']);
    //Laporan Kartu Piutang
    Route::get('/laporanpenjualan/aup', [PenjualanController::class, 'laporanaup']);
    Route::post('/laporanpenjualan/aup/cetak', [PenjualanController::class, 'cetaklaporanaup']);
    Route::get('/laporanpenjualan/detailaup/{cbg}/{sales}/{idpel}/{tgl_aup}/{kategori}/{exclude}', [PenjualanController::class, 'detailaup']);
    //Lebih Satu Faktur
    Route::get('/laporanpenjualan/lebihsatufaktur', [PenjualanController::class, 'laporanlebihsatufaktur']);
    Route::post('/laporanpenjualan/lebihsatufaktur/cetak', [PenjualanController::class, 'cetaklaporanlebihsatufaktur']);
    //DPPP
    Route::get('/laporanpenjualan/dppp', [PenjualanController::class, 'laporandppp']);
    Route::post('/laporanpenjualan/dppp/cetak', [PenjualanController::class, 'cetaklaporandppp']);
    //DPP
    Route::get('/laporanpenjualan/dpp', [PenjualanController::class, 'laporandpp']);
    Route::post('/laporanpenjualan/dpp/cetak', [PenjualanController::class, 'cetaklaporandpp']);
    //Rekap Omset Pelanggan
    Route::get('/laporanpenjualan/rekapomsetpelanggan', [PenjualanController::class, 'laporanrekapomsetpelanggan']);
    Route::post('/laporanpenjualan/rekapomsetpelanggan/cetak', [PenjualanController::class, 'cetaklaporanrekapomsetpelanggan']);
    //Rekap  Pelanggan
    Route::get('/laporanpenjualan/rekappelanggan', [PenjualanController::class, 'laporanrekappelanggan']);
    Route::post('/laporanpenjualan/rekappelanggan/cetak', [PenjualanController::class, 'cetaklaporanrekappelanggan']);
    //Harga Net
    Route::get('/laporanpenjualan/harganet', [PenjualanController::class, 'laporanharganet']);
    Route::post('/laporanpenjualan/harganet/cetak', [PenjualanController::class, 'cetaklaporanharganet']);

    //Rekap Penjualan
    Route::get('/laporanpenjualan/rekappenjualan', [PenjualanController::class, 'laporanrekappenjualan']);
    Route::post('/laporanpenjualan/rekappenjualan/cetak', [PenjualanController::class, 'cetaklaporanrekappenjualan']);

    //Kendaraan
    //Rekap Kendaraan
    Route::get('/laporankendaraan/rekapkendaraan', [KendaraanController::class, 'laporanrekapkendaraan']);
    Route::post('/laporankendaraan/rekapkendaraan/cetak', [KendaraanController::class, 'cetaklaporanrekapkendaraan']);

    //LPC
    Route::get('/lpc', [LpcController::class, 'index']);
    Route::post('/lpc/show', [LpcController::class, 'show']);

    //Dashboard
    Route::post('/dpppdashboard', [PenjualanController::class, 'dpppdashboard']);
    Route::post('/rekapkendaraandashboard', [KendaraanController::class, 'rekapkendaraandashboard']);
    Route::post('/aupdashboardall', [PenjualanController::class, 'aupdashboardall']);
    Route::post('/aupdashboardcabang', [PenjualanController::class, 'aupdashboardcabang']);

    //Target Komisi
    Route::get('/targetkomisi', [TargetkomisiController::class, 'index']);
    Route::post('/targetkomisi/detailapprovecabang', [TargetkomisiController::class, 'detailapprovecabang']);
    Route::post('/targetkomisi/create', [TargetkomisiController::class, 'create']);
    Route::post('/targetkomisi/getlisttarget', [TargetkomisiController::class, 'getlisttarget']);
    Route::post('/targetkomisi/store', [TargetkomisiController::class, 'store']);
    Route::get('/targetkomisi/{kode_target}/generatecashin', [TargetkomisiController::class, 'generatecashin']);
    Route::post('/targetkomisi/show', [TargetkomisiController::class, 'show']);
    Route::post('/targetkomisi/loadkoreksitarget', [TargetkomisiController::class, 'loadkoreksitarget']);
    Route::post('/targetkomisi/update', [TargetkomisiController::class, 'update']);
    Route::get('/targetkomisi/{kode_target}/{kode_cabang}/approvetarget', [TargetkomisiController::class, 'approvetarget']);
    Route::get('/targetkomisi/{kode_target}/{kode_cabang}/canceltarget', [TargetkomisiController::class, 'canceltarget']);
});


//Admin | Direktur
Route::middleware(['auth', 'ceklevel:admin,direktur'])->group(function () {
    Route::post('/limitkredit/penyesuaian_limit', [LimitkreditController::class, 'penyesuaian_limit']);
    Route::post('/limitkredit/updatelimit', [LimitkreditController::class, 'updatelimit']);
});


//Administrator | Admin Penjualan | Kepala Penjualan | Direktur | Manager Accounting
Route::middleware(['auth', 'ceklevel:admin,admin penjualan,manager accounting,kepala penjualan,kepala admin,manager marketing,direktur'])->group(function () {

    //Salesman
    Route::get('/salesman', [SalesmanController::class, 'index']);
    Route::get('/salesman/create', [SalesmanController::class, 'create']);
    Route::post('/salesman/store', [SalesmanController::class, 'store']);
    Route::get('/salesman/{id_karyawan}/edit', [SalesmanController::class, 'edit']);
    Route::post('/salesman/{id_karyawan}/update', [SalesmanController::class, 'update']);
    Route::delete('/salesman/{id_karyawan}/delete', [SalesmanController::class, 'delete']);
    Route::post('/salesman/show', [SalesmanController::class, 'show']);


    //Harga
    Route::get('/harga', [HargaController::class, 'index']);
    Route::get('/harga/create', [HargaController::class, 'create']);
    Route::get('/harga/{kode_barang}/edit', [HargaController::class, 'edit']);
    Route::post('/harga/store', [HargaController::class, 'store']);
    Route::post('/harga/{kode_barang}/update', [HargaController::class, 'update']);
    Route::post('/harga/show', [HargaController::class, 'show']);
    Route::delete('/harga/{kode_barang}/delete', [HargaController::class, 'delete']);
    Route::post('getautocompleteharga', [HargaController::class, 'getautocompleteharga']);
    Route::post('getautocompletehargaretur', [HargaController::class, 'getautocompletehargaretur']);
    Route::post('gethargabarang', [HargaController::class, 'gethargabarang']);



    //Pelanggan
    Route::get('/pelanggan', [PelangganController::class, 'index']);
    Route::get('/pelanggan/create', [PelangganController::class, 'create']);
    Route::post('/pelanggan/store', [PelangganController::class, 'store']);
    Route::get('/pelanggan/{kode_pelanggan}/edit', [PelangganController::class, 'edit']);
    Route::post('/pelanggan/{kode_pelanggan}/update', [PelangganController::class, 'update']);
    Route::delete('/pelanggan/{kode_pelanggan}/delete', [PelangganController::class, 'delete']);
    Route::get('/pelanggan/{kode_pelanggan}/show', [PelangganController::class, 'show']);
    Route::get('/pelanggan/json', [PelangganController::class, 'json'])->name('pelanggan.json');

    //Kendaraan
    Route::get('/kendaraan', [KendaraanController::class, 'index']);
    Route::get('/kendaraan/create', [KendaraanController::class, 'create']);
    Route::post('/kendaraan/store', [KendaraanController::class, 'store']);
    Route::delete('/kendaraan/{id}/delete', [KendaraanController::class, 'delete']);
    Route::get('/kendaraan/{id}/edit', [KendaraanController::class, 'edit']);
    Route::post('/kendaraan/{id}/update', [KendaraanController::class, 'update']);
    Route::post('/kendaraan/show', [KendaraanController::class, 'show']);
});

//Administrator | Admin Penjualan
Route::middleware(['auth', 'ceklevel:admin,admin penjualan,kepala penjualan,kepala admin'])->group(function () {



    //Penjualan
    Route::get('/penjualan', [PenjualanController::class, 'index']);
    Route::get('/penjualan/create', [PenjualanController::class, 'create']);
    Route::post('/penjualan/storebarangtemp', [PenjualanController::class, 'storebarangtemp']);
    Route::post('/penjualan/deletebarangtemp', [PenjualanController::class, 'deletebarangtemp']);
    Route::post('/penjualan/deletebarang', [PenjualanController::class, 'deletebarang']);
    Route::get('/penjualan/showbarangtemp', [PenjualanController::class, 'showbarangtemp']);
    Route::post('/penjualan/updatedetailtemp', [PenjualanController::class, 'updatedetailtemp']);
    Route::get('/loadtotalpenjualantemp', [PenjualanController::class, 'loadtotalpenjualantemp']);
    Route::post('/hitungdiskon', [PenjualanController::class, 'hitungdiskon']);
    Route::get('/cekpenjtemp', [PenjualanController::class, 'cekpenjtemp']);
    Route::post('/cekpiutangpelanggan', [PenjualanController::class, 'cekpiutangpelanggan']);
    Route::post('/penjualan/store', [PenjualanController::class, 'store']);
    Route::get('/penjualan/cetakfaktur/{no_fak_penj}', [PenjualanController::class, 'cetakfaktur']);
    Route::get('/penjualan/cetaksuratjalan/{no_fak_penj}/{type}', [PenjualanController::class, 'cetaksuratjalan']);
    Route::delete('/penjualan/{no_fak_penj}/delete', [PenjualanController::class, 'delete']);
    Route::get('/penjualan/{no_fak_penj}/edit', [PenjualanController::class, 'edit']);
    Route::post('/penjualan/showbarang', [PenjualanController::class, 'showbarang']);
    Route::post('/cekpenj', [PenjualanController::class, 'cekpenj']);
    Route::post('/loadtotalpenjualan', [PenjualanController::class, 'loadtotalpenjualan']);
    Route::post('/hitungdiskonpenjualan', [PenjualanController::class, 'hitungdiskonpenjualan']);
    Route::post('/penjualan/updatedetail', [PenjualanController::class, 'updatedetail']);
    Route::post('/penjualan/storebarang', [PenjualanController::class, 'storebarang']);
    Route::post('/penjualan/update', [PenjualanController::class, 'update']);
    Route::get('/penjualan/{no_fak_penj}/show', [PenjualanController::class, 'show']);


    //Pembayaran
    Route::post('/pembayaran/store', [PembayaranController::class, 'store']);
    Route::post('/pembayaran/edit', [PembayaranController::class, 'edit']);
    Route::post('/pembayaran/{nobukti}/update', [PembayaranController::class, 'update']);
    Route::delete('/pembayaran/{nobukti}/delete', [PembayaranController::class, 'delete']);

    //Giro
    Route::post('/pembayaran/storegiro', [PembayaranController::class, 'storegiro']);
    Route::delete('/pembayaran/{id_giro}/deletegiro', [PembayaranController::class, 'deletegiro']);
    Route::post('/pembayaran/editgiro', [PembayaranController::class, 'editgiro']);
    Route::post('/pembayaran/{id_giro}/updategiro', [PembayaranController::class, 'updategiro']);
    Route::get('/giro', [GiroController::class, 'index']);
    Route::post('/giro/detailfaktur', [GiroController::class, 'detailfaktur']);
    Route::post('/giro/prosesgiro', [GiroController::class, 'prosesgiro']);
    Route::post('/giro/update', [GiroController::class, 'update']);


    //Transfer
    Route::post('/pembayaran/storetransfer', [PembayaranController::class, 'storetransfer']);
    Route::delete('/pembayaran/{id_transfer}/deletetransfer', [PembayaranController::class, 'deletetransfer']);
    Route::post('/pembayaran/edittransfer', [PembayaranController::class, 'edittransfer']);
    Route::post('/pembayaran/{id_transfer}/updatetransfer', [PembayaranController::class, 'updatetransfer']);
    Route::get('/transfer', [TransferController::class, 'index']);
    Route::post('/transfer/detailfaktur', [TransferController::class, 'detailfaktur']);
    Route::post('/transfer/prosestransfer', [TransferController::class, 'prosestransfer']);
    Route::post('/transfer/update', [TransferController::class, 'update']);

    //Retur
    Route::get('/retur', [ReturController::class, 'index']);
    Route::get('/retur/create', [ReturController::class, 'create']);
    Route::post('/retur/store', [ReturController::class, 'store']);
    Route::post('/retur/showbarangtemp', [ReturController::class, 'showbarangtemp']);
    Route::post('/retur/storebarangtemp', [ReturController::class, 'storebarangtemp']);
    Route::post('/cekreturtemp', [ReturController::class, 'cekreturtemp']);
    Route::post('/retur/updatedetailtemp', [ReturController::class, 'updatedetailtemp']);
    Route::post('/retur/show', [ReturController::class, 'show']);
    Route::delete('/retur/{no_retur_penj}/delete', [ReturController::class, 'delete']);
    Route::post('/retur/deletebarangtemp', [ReturController::class, 'deletebarangtemp']);
    Route::post('/loadtotalreturtemp', [ReturController::class, 'loadtotalreturtemp']);
    Route::post('/retur/getfakturpelanggan', [ReturController::class, 'getfakturpelanggan']);

    //LPC
    Route::get('lpc/create', [LpcController::class, 'create']);
    Route::post('lpc/store', [LpcController::class, 'store']);
    Route::post('lpc/delete', [LpcController::class, 'delete']);
    Route::post('lpc/edit', [LpcController::class, 'edit']);
    Route::post('lpc/update', [LpcController::class, 'update']);
    Route::post('lpc/approve', [LpcController::class, 'approve']);
    Route::post('lpc/cancel', [LpcController::class, 'cancel']);

    //Limit Kredit

    Route::get('/limitkredit/{kode_pelanggan}/create', [LimitkreditController::class, 'create']);
    Route::post('/limitkredit/store', [LimitkreditController::class, 'store']);
    Route::post('/limitkredit/get_topup_terakhir', [LimitkreditController::class, 'get_topup_terakhir']);
    Route::delete('/limitkredit/{no_pengajuan}/{kode_pelanggan}/delete', [LimitkreditController::class, 'delete']);
});
