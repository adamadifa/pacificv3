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
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\SalesmanController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\TabunganController;
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

Route::middleware(['auth', 'ceklevel:admin'])->group(function () {
    Route::get('/dashboardadmin', [DashboardController::class, 'dashboardadmin']);

    //Dashboard
    Route::post('/rekapcashin', [PenjualanController::class, 'rekapcashin']);
    Route::post('/aupdashboardall', [PenjualanController::class, 'aupdashboardall']);
    Route::post('/aupdashboardcabang', [PenjualanController::class, 'aupdashboardcabang']);
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

//Administrator | Admin Penjualan
Route::middleware(['auth', 'ceklevel:admin,admin penjualan'])->group(function () {

    Route::get('/dashboardadminpenjualan', [DashboardController::class, 'dashboardadminpenjualan']);

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

    //Salesman
    Route::get('/salesman', [SalesmanController::class, 'index']);
    Route::get('/salesman/create', [SalesmanController::class, 'create']);
    Route::post('/salesman/store', [SalesmanController::class, 'store']);
    Route::get('/salesman/{id_karyawan}/edit', [SalesmanController::class, 'edit']);
    Route::post('/salesman/{id_karyawan}/update', [SalesmanController::class, 'update']);
    Route::delete('/salesman/{id_karyawan}/delete', [SalesmanController::class, 'delete']);
    Route::post('/salesman/show', [SalesmanController::class, 'show']);
    Route::post('/salesman/getsalescab', [SalesmanController::class, 'getsalescab']);

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


    //Penjualan
    Route::get('/penjualan', [PenjualanController::class, 'index']);
    Route::get('/penjualan/create', [PenjualanController::class, 'create']);
    Route::post('/penjualan/storebarangtemp', [PenjualanController::class, 'storebarangtemp']);
    Route::post('/penjualan/deletebarangtemp', [PenjualanController::class, 'deletebarangtemp']);
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
    Route::post('/giro/update',[GiroController::class,'update']);


    //Transfer
    Route::post('/pembayaran/storetransfer', [PembayaranController::class, 'storetransfer']);
    Route::delete('/pembayaran/{id_transfer}/deletetransfer', [PembayaranController::class, 'deletetransfer']);
    Route::post('/pembayaran/edittransfer', [PembayaranController::class, 'edittransfer']);
    Route::post('/pembayaran/{id_transfer}/updatetransfer', [PembayaranController::class, 'updatetransfer']);


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



});
