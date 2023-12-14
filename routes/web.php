<?php

use App\Http\Controllers\AjuanfakturController;
use App\Http\Controllers\AjuanroutingController;
use App\Http\Controllers\AjuantransferdanaController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AngkutanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BadstokController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangpembelianController;
use App\Http\Controllers\BelumsetorController;
use App\Http\Controllers\BpbjController;
use App\Http\Controllers\BpjskesehatanController;
use App\Http\Controllers\BpjstkController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\CostratioController;
use App\Http\Controllers\DaftarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DpbController;
use App\Http\Controllers\DriverhelperController;
use App\Http\Controllers\FpbController;
use App\Http\Controllers\FsthpController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\GiroController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\HargaawalController;
use App\Http\Controllers\HargaController;
use App\Http\Controllers\HargaControoler;
use App\Http\Controllers\HariliburController;
use App\Http\Controllers\HppController;
use App\Http\Controllers\InsentifController;
use App\Http\Controllers\JadwalkerjaController;
use App\Http\Controllers\JaminanController;
use App\Http\Controllers\JenissimpananController;
use App\Http\Controllers\JurnalkoreksiController;
use App\Http\Controllers\JurnalumumController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KasbonController;
use App\Http\Controllers\KaskecilController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\KesepakatanbersamaController;
use App\Http\Controllers\KlaimController;
use App\Http\Controllers\KonfigurasijadwalController;
use App\Http\Controllers\KontrabonangkutanController;
use App\Http\Controllers\KontrabonController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\LaporanaccountingController;
use App\Http\Controllers\LaporangaController;
use App\Http\Controllers\LaporangudangbahanController;
use App\Http\Controllers\LaporangudangcabangController;
use App\Http\Controllers\LaporangudangjadiController;
use App\Http\Controllers\LaporangudanglogistikController;
use App\Http\Controllers\LaporanhrdController;
use App\Http\Controllers\LaporankeuanganController;
use App\Http\Controllers\LaporanmaintenanceController;
use App\Http\Controllers\LaporanpembelianController;
use App\Http\Controllers\Laporanproduksi;
use App\Http\Controllers\LaporanproduksiController;
use App\Http\Controllers\LebihsetorController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\LhpController;
use App\Http\Controllers\LimitkreditController;
use App\Http\Controllers\LogaktivitasController;
use App\Http\Controllers\LogamtokertasController;
use App\Http\Controllers\LpcController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\MutasibankController;
use App\Http\Controllers\MutasigudangcabangController;
use App\Http\Controllers\MutasikendaraanController;
use App\Http\Controllers\MutasilaingjController;
use App\Http\Controllers\OmancabangController;
use App\Http\Controllers\OmanController;
use App\Http\Controllers\OpnamegudangbahanController;
use App\Http\Controllers\OpnamegudanglogistikController;
use App\Http\Controllers\OpnamemutasibarangproduksiController;
use App\Http\Controllers\Pasarcontroller;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\PemasukangudangbahanController;
use App\Http\Controllers\PemasukangudanglogistikController;
use App\Http\Controllers\PemasukanmaintenanceController;
use App\Http\Controllers\PemasukanproduksiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PembayaranjmkController;
use App\Http\Controllers\PembayarankasbonController;
use App\Http\Controllers\PembayaranpinjamanController;
use App\Http\Controllers\PembayaranpinjamannonpjpController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PengajuanizinController;
use App\Http\Controllers\PengeluarangudangbahanController;
use App\Http\Controllers\PengeluarangudanglogistikController;
use App\Http\Controllers\PengeluaranmaintenanceController;
use App\Http\Controllers\PengeluaranproduksiController;
use App\Http\Controllers\PenilaiankaryawanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PermintaanpengirimanController;
use App\Http\Controllers\PermintaanproduksiController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\PinjamannonpjpController;
use App\Http\Controllers\PiutangkaryawanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\QrcodeController;
use App\Http\Controllers\RatiokomisiController;
use App\Http\Controllers\RealisasiController;
use App\Http\Controllers\RekeningkaryawanController;
use App\Http\Controllers\RepackrejectgudangjadiController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\SaldoawalBJController;
use App\Http\Controllers\SaldoawalbukubesarController;
use App\Http\Controllers\SaldoawalgudangbahanController;
use App\Http\Controllers\SaldoawalgudanglogistikController;
use App\Http\Controllers\SaldoawalkasbesarController;
use App\Http\Controllers\SaldoawalmutasibarangproduksiController;
use App\Http\Controllers\SalesmanController;
use App\Http\Controllers\SapController;
use App\Http\Controllers\ServicekendaraanController;
use App\Http\Controllers\SetcoacabangController;
use App\Http\Controllers\SetorangiroController;
use App\Http\Controllers\SetoranpenjualanController;
use App\Http\Controllers\SetoranpusatController;
use App\Http\Controllers\SetorantransferController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SuratjalanController;
use App\Http\Controllers\TabunganController;
use App\Http\Controllers\TargetkomisiController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TutuplaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\WorksheetomController;
use App\Models\Barangpembelian;
use App\Models\Harilibur;
use App\Models\Logamtokertas;
use App\Models\Pemasukangudanglogistik;
use App\Models\Pengajuanizin;
use App\Models\Penilaiankaryawan;
use App\Models\Penjualan;
use App\Models\Permintaanpengiriman;
use App\Models\Pinjaman;
use App\Models\Rekeningkaryawan;
use App\Models\Saldoawalmutasibarangproduksi;
use App\Models\Setcoacabang;
use App\Models\Setoranpenjualan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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

Route::get('/agent1', function () {
    return request()->userAgent();
});




Route::middleware(['guest'])->group(function () {
    // Route::get('/paneladmin', function () {
    //     return view('Auth.login');
    // })->name('login');

    Route::get('/', function () {
        return view('Auth.login');
    })->name('login');

    Route::get('/loginsap', function () {
        return view('sap.auth.login');
    })->name('loginsap');
});



Route::post('/postlogin', [AuthController::class, 'postlogin']);
Route::post('/postloginsap', [AuthController::class, 'postloginsap']);

Route::get('/cekdpbwa', [WhatsappController::class, 'cekdpb']);
Route::get('/cekpenjualanwa', [WhatsappController::class, 'cekpenjualan']);
Route::middleware(['auth'])->group(function () {
    Route::post('/postlogout', [AuthController::class, 'postlogout']);
    Route::post('/logoutsap', [AuthController::class, 'logoutsap']);
    Route::get('/home', [DashboardController::class, 'home']);
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/user/{id}/activated', [UserController::class, 'activated']);
    Route::delete('/user/{id}/delete', [UserController::class, 'delete']);
    Route::get('/user/gantipassword', [UserController::class, 'gantipassword']);
    Route::get('/user/editprofile', [UserController::class, 'editprofile']);
    Route::post('/user/{id}/update', [UserController::class, 'update']);
    Route::post('/user/{id}/updateprofile', [UserController::class, 'updateprofile']);
    Route::post('/user/getusercabang', [UserController::class, 'getusercabang']);
    //Tutup Laporan
    Route::post('/cektutuplaporan', [TutuplaporanController::class, 'cektutuplaporan']);
    //Cek Barang Penjualan Temporary
    Route::post('/cekpenjtemp', [PenjualanController::class, 'cekpenjtemp']);

    //Load Data
    //Salesman
    Route::post('/salesman/getsalescab', [SalesmanController::class, 'getsalescab']);
    Route::post('/cabang/getcabang', [CabangController::class, 'getcabang']);
    //Pelanggan
    Route::post('/pelanggan/getpelanggansalesman', [PelangganController::class, 'getpelanggansalesman']);
    //Kendaraan
    Route::post('/kendaraan/getkendaraancab', [KendaraanController::class, 'getkendaraancab']);
    //Driver Helper
    Route::post('/driverhelper/getdriverhelpercab', [DriverhelperController::class, 'getdriverhelpercab']);
    //LoadStokCabang
    Route::post('/getsaldogudangcabang', [MutasigudangcabangController::class, 'getsaldogudangcabang']);
    Route::post('/getsaldogudangcabangbs', [MutasigudangcabangController::class, 'getsaldogudangcabangbs']);


    //Bank
    Route::post('/bank/getbankcabang', [BankController::class, 'getbankcabang']);

    //Load Coa Cabang
    Route::post('/coa/getcoacabang', [CoaController::class, 'getcoacabang']);
    //Loda Barang Pembleian By Kategori
    Route::post('/getbarangpembelianbykategori', [BarangpembelianController::class, 'getbarangpembelianbykategori']);

    Route::post('getautocompleteharga', [HargaController::class, 'getautocompleteharga']);
    Route::post('getautocompletedpb', [DpbController::class, 'getautocompletedpb']);
    Route::post('getautocompletefpb', [FpbController::class, 'getautocompletefpb']);
    Route::post('getautocompletesj', [SuratjalanController::class, 'getautocompletesj']);
    Route::post('getautocompletehargaretur', [HargaController::class, 'getautocompletehargaretur']);
    Route::post('gethargabarang', [HargaController::class, 'gethargabarang']);
    Route::post('/suratjalan/showsuratjalanmutasi', [SuratjalanController::class, 'showsuratjalanmutasi']);

    Route::get('/omancabang', [OmancabangController::class, 'index']);
    Route::get('/omancabang/create', [OmancabangController::class, 'create']);
    Route::post('/cekomancabang', [OmancabangController::class, 'cekomancabang']);
    Route::post('/omancabang/show', [OmancabangController::class, 'show']);
    Route::post('/omancabang/store', [OmancabangController::class, 'store']);
    Route::get('/omancabang/{no_order}/edit', [OmancabangController::class, 'edit']);
    Route::post('/omancabang/{no_order}/update', [OmancabangController::class, 'update']);
    Route::delete('/omancabang/{no_order}/delete', [OmancabangController::class, 'delete']);
    Route::post('/getomancabang', [OmancabangController::class, 'getomancabang']);

    Route::get('/oman', [OmanController::class, 'index']);
    Route::get('/oman/create', [OmanController::class, 'create']);
    Route::post('/cekoman', [OmanController::class, 'cekoman']);
    Route::post('/oman/store', [OmanController::class, 'store']);
    Route::get('/oman/{no_order}/edit', [OmanController::class, 'edit']);
    Route::post('/oman/{no_order}/update', [OmanController::class, 'update']);
    Route::delete('/oman/{no_order}/delete', [OmanController::class, 'delete']);
    Route::post('/oman/show', [OmanController::class, 'show']);

    //Permintaan Pengiriman
    Route::get('/permintaanpengiriman', [PermintaanpengirimanController::class, 'index']);
    Route::get('/permintaanpengiriman/cektemp', [PermintaanpengirimanController::class, 'cektemp']);
    Route::post('/permintaanpengiriman/storetemp', [PermintaanpengirimanController::class, 'storetemp']);
    Route::post('/permintaanpengiriman/deletetemp', [PermintaanpengirimanController::class, 'deletetemp']);
    Route::get('/permintaanpengiriman/showtemp', [PermintaanpengirimanController::class, 'showtemp']);
    Route::get('/permintaanpengiriman/{no_permintaan_pengiriman}/show', [PermintaanpengirimanController::class, 'show']);
    Route::post('/permintaanpengiriman/store', [PermintaanpengirimanController::class, 'store']);
    Route::post('/permintaanpengiriman/updatedetail', [PermintaanpengirimanController::class, 'updatedetail']);
    Route::post('/permintaanpengiriman/buatnopermintaan', [PermintaanpengirimanController::class, 'buatnopermintaan']);
    Route::delete('/permintaanpengiriman/{no_permintaan_pengiriman}/delete', [PermintaanpengirimanController::class, 'delete']);
    Route::post('/permintaanpengiriman/ubahtanggal', [PermintaanpengirimanController::class, 'ubahtanggal']);
    Route::post('/permintaanpengiriman/{no_permintaan_pengiriman}/updatetanggal', [PermintaanpengirimanController::class, 'updatetanggal']);
    //Ratio Komisi
    Route::get('/ratiokomisi', [RatiokomisiController::class, 'index']);
    Route::post('/ratiokomisi/getratiokomisi', [RatiokomisiController::class, 'getratiokomisi']);
    Route::post('/ratiokomisi/store', [RatiokomisiController::class, 'store']);

    Route::get('/dashboardadmin', [DashboardController::class, 'dashboardadmin']);
    Route::get('/dashboardga', [DashboardController::class, 'dashboardga']);
    Route::get('/dashboardsfa', [DashboardController::class, 'dashboardsfa']);
    Route::get('/dashboardsfakp', [DashboardController::class, 'dashboardsfakp']);
    Route::get('/dashboardsfarsm', [DashboardController::class, 'dashboardsfarsm']);
    Route::get('/dashboardsfagm', [DashboardController::class, 'dashboardsfagm']);

    //Dashboard
    Route::post('/rekapcashin', [PenjualanController::class, 'rekapcashin']);
    Route::post('/rekappenjualandashboard', [PenjualanController::class, 'rekappenjualandashboard']);
    Route::post('/rekapkasbesardashboard', [PenjualanController::class, 'rekapkasbesardashboard']);

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
    Route::post('/kendaraan/updatekapasitas', [KendaraanController::class, 'updatekapasitas']);

    //Mutasi Kendaraan

    Route::get('/mutasikendaraan', [MutasikendaraanController::class, 'index']);
    Route::get('/mutasikendaraan/create', [MutasikendaraanController::class, 'create']);
    Route::post('/mutasikendaraan/store', [MutasikendaraanController::class, 'store']);
    Route::delete('/mutasikendaraan/{no_mutasi}/delete', [MutasikendaraanController::class, 'delete']);

    //Item Service

    Route::get('/getitemservice', [ServicekendaraanController::class, 'getitemservice']);
    Route::post('/storeitemservice', [ServicekendaraanController::class, 'storeitemservice']);

    Route::get('/getbengkel', [ServicekendaraanController::class, 'getbengkel']);
    Route::post('/storenewbengkel', [ServicekendaraanController::class, 'storenewbengkel']);
    //Service Kendaraan
    Route::get('/servicekendaraan', [ServicekendaraanController::class, 'index']);
    Route::get('/servicekendaraan/create', [ServicekendaraanController::class, 'create']);
    Route::post('/servicekendaraan/storetemp', [ServicekendaraanController::class, 'storetemp']);
    Route::post('/servicekendaraan/showtemp', [ServicekendaraanController::class, 'showtemp']);
    Route::post('/servicekendaraan/deletetemp', [ServicekendaraanController::class, 'deletetemp']);
    Route::post('/servicekendaraan/store', [ServicekendaraanController::class, 'store']);
    Route::delete('/servicekendaraan/{no_invoice}/delete', [ServicekendaraanController::class, 'delete']);
    Route::post('/servicekendaraan/show', [ServicekendaraanController::class, 'show']);

    //Bad Stock
    Route::get('/badstock', [BadstokController::class, 'index']);
    Route::get('/badstok/create', [BadstokController::class, 'create']);
    Route::post('/badstok/store', [BadstokController::class, 'store']);
    Route::post('/badstok/show', [BadstokController::class, 'show']);
    Route::delete('/badstok/{no_bs}/delete', [BadstokController::class, 'delete']);

    //Laporan GA

    Route::get('/laporanga/servicekendaraan', [LaporangaController::class, 'servicekendaraan']);
    Route::post('/laporanga/servicekendaraan/cetak', [LaporangaController::class, 'cetakservicekendaraan']);

    Route::get('/laporanga/rekapbadstok', [LaporangaController::class, 'rekapbadstok']);
    Route::post('/laporanga/rekapbadstok/cetak', [LaporangaController::class, 'cetakrekapbadstok']);

    //Cabang
    Route::get('/cabang', [CabangController::class, 'index']);
    Route::get('/cabang/create', [CabangController::class, 'create']);
    Route::post('/cabang/store', [CabangController::class, 'store']);
    Route::get('/cabang/{kode_cabang}/edit', [CabangController::class, 'edit']);
    Route::post('/cabang/{kode_cabang}/update', [CabangController::class, 'update']);
    Route::delete('/cabang/{kode_cabang}/delete', [CabangController::class, 'delete']);

    //Limit Kredit
    Route::get('/limitkredit', [LimitkreditController::class, 'index']);
    Route::get('/limitkredit/salesman', [LimitkreditController::class, 'indexsalesman']);
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
    //Laporan Routing Salesman
    Route::get('/laporanpenjualan/routingsalesman', [PenjualanController::class, 'laporanroutingsalesman']);
    Route::post('/laporanpenjualan/routingsalesman/cetak', [PenjualanController::class, 'cetakroutingsalesman']);

    //Laporan Sales Perfomance
    Route::get('/laporanpenjualan/salesperfomance', [PenjualanController::class, 'laporansalesperfomance']);
    Route::post('/laporanpenjualan/salesperfomance/cetak', [PenjualanController::class, 'cetaksalesperfomance']);

    //Laporan SMM Activity
    Route::get('/laporanpenjualan/smmactivity', [PenjualanController::class, 'smmactivity']);
    Route::post('/laporanpenjualan/smmactivity/cetak', [PenjualanController::class, 'cetaksmmactivity']);

    //Laporan RSM Activity
    Route::get('/laporanpenjualan/rsmactivity', [PenjualanController::class, 'rsmactivity']);
    Route::post('/laporanpenjualan/rsmactivity/cetak', [PenjualanController::class, 'cetakrsmactivity']);
    //Laporan Kartu Piutang
    Route::get('/laporanpenjualan/kartupiutang', [PenjualanController::class, 'laporankartupiutang']);
    Route::post('/laporanpenjualan/kartupiutang/cetak', [PenjualanController::class, 'cetaklaporankartupiutang']);
    //Laporan Kartu Piutang
    Route::get('/laporanpenjualan/aup', [PenjualanController::class, 'laporanaup']);
    Route::post('/laporanpenjualan/aup/cetak', [PenjualanController::class, 'cetaklaporanaup']);
    Route::get('/laporanpenjualan/detailaup/{cbg}/{sales}/{idpel}/{tgl_aup}/{kategori}/{exclude}', [PenjualanController::class, 'detailaup']);

    //Laporan Tanda Terima Faktur
    Route::get('/laporanpenjualan/tandaterimafaktur', [PenjualanController::class, 'laporantandaterimafaktur']);
    Route::post('/laporanpenjualan/tandaterimafaktur/cetak', [PenjualanController::class, 'cetaktandaterimafaktur']);
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

    //rekap Wilauah
    Route::get('/laporanpenjualan/rekapwilayah', [PenjualanController::class, 'rekapwilayah']);
    Route::post('/laporanpenjualan/rekapwilayah/cetak', [PenjualanController::class, 'cetakrekapwilayah']);

    //Effective Call
    Route::get('/laporanpenjualan/effectivecall', [PenjualanController::class, 'effectivecall']);
    Route::post('/laporanpenjualan/effectivecall/cetak', [PenjualanController::class, 'cetakeffectivecall']);
    Route::post('/effectivecall/detailsalesman', [PenjualanController::class, 'detailsalesmanecen']);

    Route::get('/laporanpenjualan/lhp', [PenjualanController::class, 'lhp']);
    Route::post('/laporanpenjualan/lhp/cetak', [PenjualanController::class, 'cetaklhp']);

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
    Route::get('/laporankomisi', [TargetkomisiController::class, 'laporankomisi']);
    Route::get('/rekapkomisi', [TargetkomisiController::class, 'rekapkomisi']);
    Route::post('/laporankomisi/cetak', [TargetkomisiController::class, 'cetaklaporankomisi']);
    Route::post('/rekapkomisi/cetak', [TargetkomisiController::class, 'cetakrekapkomisi']);
    Route::get('/laporankomisidriverhelper', [TargetkomisiController::class, 'laporankomisidriverhelper']);
    Route::post('/laporankomisidriverhelper/cetak', [TargetkomisiController::class, 'cetakkomisidriverhelper']);
    Route::get('/laporaninsentif', [TargetkomisiController::class, 'laporaninsentif']);
    Route::post('/laporaninsentif/cetak', [TargetkomisiController::class, 'cetaklaporaninsentif']);
    Route::post('/getrealisasitargetsales', [TargetkomisiController::class, 'getrealisasitargetsales']);
    Route::post('/limitkredit/penyesuaian_limit', [LimitkreditController::class, 'penyesuaian_limit']);
    Route::post('/limitkredit/updatelimit', [LimitkreditController::class, 'updatelimit']);
    Route::post('/inputpotongankomisi', [TargetkomisiController::class, 'inputpotongankomisi']);
    Route::post('/inputkomisiakhir', [TargetkomisiController::class, 'inputkomisiakhir']);
    Route::post('/storepotongankomisi', [TargetkomisiController::class, 'storepotongankomisi']);
    Route::post('/storekomisiakhir', [TargetkomisiController::class, 'storekomisiakhir']);
    Route::post('/cekapprovekomisi', [TargetkomisiController::class, 'cekapprovekomisi']);
    Route::post('/cekbatal', [TargetkomisiController::class, 'cekbatal']);
    Route::post('/approvekomisi', [TargetkomisiController::class, 'approvekomisi']);
    Route::post('/getqrcode', [TargetkomisiController::class, 'getqrcode']);
    Route::post('/cancelkomisi', [TargetkomisiController::class, 'cancelkomisi']);
    Route::get('/komisiapprove', [TargetkomisiController::class, 'komisiapprove']);
    Route::post('/getapprovekomisi', [TargetkomisiController::class, 'getapprovekomisi']);
    //Kas Kecil
    Route::get('/kaskecil', [KaskecilController::class, 'index']);
    Route::get('/kaskecil/create', [KaskecilController::class, 'create']);
    Route::post('/getkaskeciltemp', [KaskecilController::class, 'getkaskeciltemp']);
    Route::post('/kaskecil/storetemp', [KaskecilController::class, 'storetemp']);
    Route::post('/kaskecil/deletetemp', [KaskecilController::class, 'deletetemp']);
    Route::post('/cekkaskeciltemp', [KaskecilController::class, 'cekkaskeciltemp']);
    Route::post('/kaskecil/store', [KaskecilController::class, 'store']);
    Route::delete('/kaskecil/{id}/delete', [KaskecilController::class, 'delete']);
    Route::get('/kaskecil/{id}/edit', [KaskecilController::class, 'edit']);
    Route::post('/kaskecil/{id}/update', [KaskecilController::class, 'update']);
    Route::post('/kaskecil/storesplitakun', [KaskecilController::class, 'storesplitakun']);
    Route::get('/kaskecil/{id}/showsplit', [KaskecilController::class, 'showsplit']);
    Route::post('/kaskecil/deletesplit', [KaskecilController::class, 'deletesplit']);

    //Mutasi Bank
    Route::get('/mutasibank', [MutasibankController::class, 'index']);
    Route::get('/mutasibank/{kode_bank}/{kode_cabang}/create', [MutasibankController::class, 'create']);
    Route::post('/mutasibank/edit', [MutasibankController::class, 'edit']);
    Route::post('/mutasibank/{no_bukti}/update', [MutasibankController::class, 'update']);
    Route::post('/mutasibank/store', [MutasibankController::class, 'store']);
    Route::delete('/mutasibank/{no_bukti}/delete', [MutasibankController::class, 'delete']);

    //Laporan Pembelian
    Route::get('/laporanpembelian', [LaporanpembelianController::class, 'index']);
    Route::get('/laporanpembelian/pembayaran', [LaporanpembelianController::class, 'pembayaran']);
    Route::get('/laporanpembelian/rekapsupplier', [LaporanpembelianController::class, 'rekapsupplier']);
    Route::get('/laporanpembelian/rekappembelian', [LaporanpembelianController::class, 'rekappembelian']);
    Route::get('/laporanpembelian/kartuhutang', [LaporanpembelianController::class, 'kartuhutang']);
    Route::get('/laporanpembelian/auh', [LaporanpembelianController::class, 'auh']);
    Route::get('/laporanpembelian/bahankemasan', [LaporanpembelianController::class, 'bahankemasan']);
    Route::get('/laporanpembelian/rekapbahankemasan', [LaporanpembelianController::class, 'rekapbahankemasan']);
    Route::get('/laporanpembelian/jurnalkoreksi', [LaporanpembelianController::class, 'jurnalkoreksi']);
    Route::get('/laporanpembelian/rekapakun', [LaporanpembelianController::class, 'rekapakun']);
    Route::get('/laporanpembelian/rekapkontrabon', [LaporanpembelianController::class, 'rekapkontrabon']);
    Route::post('/laporanpembelian/cetak', [LaporanpembelianController::class, 'cetak_pembelian']);
    Route::post('/laporanpembelian/pembayaran/cetak', [LaporanpembelianController::class, 'cetak_pembayaran']);
    Route::post('/laporanpembelian/rekapsupplier/cetak', [LaporanpembelianController::class, 'cetak_rekapsupplier']);
    Route::post('/laporanpembelian/rekappembelian/cetak', [LaporanpembelianController::class, 'cetak_rekappembelian']);
    Route::post('/laporanpembelian/kartuhutang/cetak', [LaporanpembelianController::class, 'cetak_kartuhutang']);
    Route::post('/laporanpembelian/auh/cetak', [LaporanpembelianController::class, 'cetak_auh']);
    Route::post('/laporanpembelian/bahankemasan/cetak', [LaporanpembelianController::class, 'cetak_bahankemasan']);
    Route::post('/laporanpembelian/rekapbahankemasan/cetak', [LaporanpembelianController::class, 'cetak_rekapbahankemasan']);
    Route::post('/laporanpembelian/jurnalkoreksi/cetak', [LaporanpembelianController::class, 'cetak_jurnalkoreksi']);
    Route::post('/laporanpembelian/rekapakun/cetak', [LaporanpembelianController::class, 'cetak_rekapakun']);
    Route::post('/laporanpembelian/rekapkontrabon/cetak', [LaporanpembelianController::class, 'cetak_rekapkontrabon']);

    //Kontrabon
    Route::get('/kontrabon', [KontrabonController::class, 'index']);
    Route::post('/kontrabon/show', [KontrabonController::class, 'show']);
    Route::get('/kontrabon/create', [KontrabonController::class, 'create']);
    Route::get('/kontrabon/{kode_supplier}create', [KontrabonController::class, 'create']);
    Route::post('/kontrabon/storetemp', [KontrabonController::class, 'storetemp']);
    Route::post('/kontrabon/deletetemp', [KontrabonController::class, 'deletetemp']);
    Route::get('/kontrabon/showtemp', [KontrabonController::class, 'showtemp']);
    Route::post('/kontrabon/store', [KontrabonController::class, 'store']);
    Route::get('/kontrabon/{no_kontrabon}/edit', [KontrabonController::class, 'edit']);
    Route::get('/kontrabon/showdetail', [KontrabonController::class, 'showdetail']);
    Route::post('/kontrabon/storedetail', [KontrabonController::class, 'storedetail']);
    Route::post('/kontrabon/deletedetail', [KontrabonController::class, 'deletedetail']);
    Route::post('/kontrabon/updatedetail', [KontrabonController::class, 'updatedetail']);
    Route::post('/kontrabon/{no_kontrabon}/update', [KontrabonController::class, 'update']);
    Route::delete('/kontrabon/{no_kontrabon}/delete', [KontrabonController::class, 'delete']);
    Route::get('/pembelian/jatuhtempo', [PembelianController::class, 'jatuhtempo']);
    Route::post('/kontrabon/proseskontrabon', [KontrabonController::class, 'proseskontrabon']);
    Route::post('/kontrabon/storeproseskontrabon', [KontrabonController::class, 'storeproseskontrabon']);
    Route::post('/kontrabon/editkontrabon', [KontrabonController::class, 'editkontrabon']);
    Route::post('/kontrabon/updatekontrabon', [KontrabonController::class, 'updatekontrabon']);
    Route::delete('/kontrabon/{no_kontrabon}/batalkankontrabon', [KontrabonController::class, 'batalkankontrabon']);
    Route::get('/kontrabon/{no_kontrabon}/approvekontrabon', [KontrabonController::class, 'approvekontrabon']);
    Route::get('/kontrabon/{no_kontrabon}/cancelkontrabon', [KontrabonController::class, 'cancelkontrabon']);
    Route::get('/kontrabon/{no_kontrabon}/cetak', [KontrabonController::class, 'cetak']);
    Route::post('/kontrabon/getNokontrabon', [KontrabonController::class, 'getNokontrabon']);
    Route::get('/pembelian', [PembelianController::class, 'index']);
    Route::post('/pembelian/showdetailpembelian', [PembelianController::class, 'showdetailpembelian']);
    Route::post('/pembelian/showdetailpembeliankontrabon', [PembelianController::class, 'showdetailpembeliankontrabon']);
    Route::post('/pembelian/showdetailpotongan', [PembelianController::class, 'showdetailpotongan']);

    //Barang Pembelian
    Route::get('/barangpembelian/create', [BarangpembelianController::class, 'create']);
    Route::post('/barangpembelian/store', [BarangpembelianController::class, 'store']);
    Route::delete('/barangpembelian/{kode_barang}/delete', [BarangpembelianController::class, 'delete']);
    Route::get('/barangpembelian/{kode_barang}/edit', [BarangpembelianController::class, 'edit']);
    Route::post('/barangpembelian/{kode_barang}/update', [BarangpembelianController::class, 'update']);
    Route::get('/barangpembelian/{kode_dept}/getbarang', [BarangpembelianController::class, 'getbarang']);
    Route::get('/barangpembelian/{kode_dept}/json', [BarangpembelianController::class, 'json'])->name('barang.json');

    //Suplier
    Route::get('/supplier/create', [SupplierController::class, 'create']);
    Route::post('/supplier/store', [SupplierController::class, 'store']);
    Route::delete('/supplier/{kode_supplier}/delete', [SupplierController::class, 'delete']);
    Route::get('/supplier/{kode_supplier}/edit', [SupplierController::class, 'edit']);
    Route::post('/supplier/{kode_supplier}/update', [SupplierController::class, 'update']);
    Route::get('/supplier/getsupplier', [SupplierController::class, 'getsupplier']);
    Route::get('/supplier/json', [SupplierController::class, 'json'])->name('supplier.json');

    //Pembelian
    Route::get('/pembelian/create', [PembelianController::class, 'create']);
    Route::post('/pembelian/storetemp', [PembelianController::class, 'storetemp']);
    Route::post('/pembelian/storedetailpembelian', [PembelianController::class, 'storedetailpembelian']);
    Route::post('/pembelian/showtemp', [PembelianController::class, 'showtemp']);
    Route::post('/pembelian/deletetemp', [PembelianController::class, 'deletetemp']);
    Route::post('/pembelian/store', [PembelianController::class, 'store']);
    Route::post('/pembelian/show', [PembelianController::class, 'show']);
    Route::post('/pembelian/prosespembelian', [PembelianController::class, 'prosespembelian']);
    Route::post('/pembelian/{nobukti_pembelian}/storeprosespembelian', [PembelianController::class, 'storeprosespembelian']);

    Route::get('/pembelian/{nobukti_pembelian}/edit', [PembelianController::class, 'edit']);
    Route::post('/pembelian/deletedetail', [PembelianController::class, 'deletedetail']);
    Route::post('/pembelian/editbarang', [PembelianController::class, 'editbarang']);
    Route::post('/pembelian/updatebarang', [PembelianController::class, 'updatebarang']);
    Route::get('/pembelian/{nobukti_pembelian}/inputpotongan', [PembelianController::class, 'inputpotongan']);
    Route::post('/pembelian/storepotongan', [PembelianController::class, 'storepotongan']);
    Route::post('/pembelian/{nobukti_pembelian}/update', [PembelianController::class, 'update']);
    Route::delete('/pembelian/{nobukti_pembelian}/delete', [PembelianController::class, 'delete']);
    Route::get('/pembelian/{kode_supplier}/getpembeliankontrabon', [PembelianController::class, 'getpembeliankontrabon']);
    Route::get('/pembelian/{kode_supplier}/getpembelianjurnalkoreksi', [PembelianController::class, 'getpembelianjurnalkoreksi']);
    Route::get('/pembelian/{nobukti_pembelian}/getbarangjurnalkoreksi', [PembelianController::class, 'getbarangjurnalkoreksi']);
    Route::get('/jatuhtempo', [PembelianController::class, 'jatuhtempo']);
    Route::post('/pembelian/storesplitakun', [PembelianController::class, 'storesplitakun']);
    Route::get('/pembelian/{id}/{kode_barang}/showsplit', [PembelianController::class, 'showsplit']);
    Route::post('/pembelian/deletesplit', [PembelianController::class, 'deletesplit']);
    //Jurnal Koreksi
    Route::get('/jurnalkoreksi', [JurnalkoreksiController::class, 'index']);
    Route::get('/jurnalkoreksi/create', [JurnalkoreksiController::class, 'create']);
    Route::post('/jurnalkoreksi/store', [JurnalkoreksiController::class, 'store']);
    Route::delete('/jurnalkoreksi/{kode_jk}/delete', [JurnalkoreksiController::class, 'delete']);

    //Barang Pembelian
    Route::get('/barangpembelian', [BarangpembelianController::class, 'index']);

    //Supplier
    Route::get('/supplier', [SupplierController::class, 'index']);

    //Kontrabon Angkutan
    Route::get('/kontrabonangkutan', [KontrabonangkutanController::class, 'index']);
    Route::get('/kontrabonangkutan/{no_kontrabon}/show', [KontrabonangkutanController::class, 'show']);
    Route::get('/kontrabonangkutan/{no_kontrabon}/proses', [KontrabonangkutanController::class, 'proses']);
    Route::get('/kontrabonangkutan/create', [KontrabonangkutanController::class, 'create']);
    Route::get('/kontrabonangkutan/getnosuratjalan', [KontrabonangkutanController::class, 'getnosuratjalan']);
    Route::get('/kontrabonangkutan/showtemp', [KontrabonangkutanController::class, 'showtemp']);
    Route::post('/kontrabonangkutan/deletetemp', [KontrabonangkutanController::class, 'deletetemp']);
    Route::post('/kontrabonangkutan/cektemp', [KontrabonangkutanController::class, 'cektemp']);
    Route::post('/kontrabonangkutan/storetemp', [KontrabonangkutanController::class, 'storetemp']);
    Route::post('/kontrabonangkutan/store', [KontrabonangkutanController::class, 'store']);
    Route::post('/kontrabonangkutan/proseskontrabon', [KontrabonangkutanController::class, 'proseskontrabon']);
    Route::get('/kontrabonangkutan/{no_kontrabon}/batalkan', [KontrabonangkutanController::class, 'batalkan']);
    Route::delete('/kontrabonangkutan/{no_kontrabon}/delete', [KontrabonangkutanController::class, 'delete']);

    //Saldo Awal
    Route::get('/saldoawalgs/{jenis_bj}', [SaldoawalBJController::class, 'index']);
    Route::get('/saldoawalbs/{jenis_bj}', [SaldoawalBJController::class, 'index']);
    Route::get('/saldoawalgs/{jenis_bj}/create', [SaldoawalBJController::class, 'create']);
    Route::get('/saldoawalbs/{jenis_bj}/create', [SaldoawalBJController::class, 'create']);
    Route::get('/saldoawalbj/{kode_saldoawal}/show', [SaldoawalBJController::class, 'show']);
    Route::delete('/saldoawalbj/{kode_saldoawal}/delete', [SaldoawalBJController::class, 'delete']);
    Route::post('/saldoawalbj/getdetailsaldo', [SaldoawalBJController::class, 'getdetailsaldo']);
    Route::post('/saldoawalbj/store', [SaldoawalBJController::class, 'store']);

    //DPB
    Route::get('/dpb', [DpbController::class, 'index']);
    Route::get('/dpb/create', [DpbController::class, 'create']);
    Route::post('/dpb/store', [DpbController::class, 'store']);
    Route::get('/dpb/{no_dpb}/show', [DpbController::class, 'show']);
    Route::get('/dpb/{no_dpb}/edit', [DpbController::class, 'edit']);
    Route::delete('/dpb/{no_dpb}/delete', [DpbController::class, 'delete']);
    Route::post('/dpb/{no_dpb}/update', [DpbController::class, 'update']);
    Route::post('/dpb/showdpbmutasi', [DpbController::class, 'showdpbmutasi']);


    //FPB
    Route::get('/fpb', [FpbController::class, 'index']);
    Route::get('/fpb/create', [FpbController::class, 'create']);
    Route::post('/fpb/store', [FpbController::class, 'store']);
    Route::get('/fpb/{no_fpb}/edit', [FpbController::class, 'edit']);
    Route::get('/fpb/{no_fpb}/show', [FpbController::class, 'show']);
    Route::post('/fpb/showfpb', [FpbController::class, 'showfpb']);
    Route::post('/fpb/getdetailfpb', [FpbController::class, 'getdetailfpb']);
    Route::post('/fpb/{no_fpb}/update', [FpbController::class, 'update']);
    Route::delete('/fpb/{no_fpb}/delete', [FpbController::class, 'delete']);
    //Surat Jalan Cabang
    Route::get('/suratjalancab', [SuratjalanController::class, 'index']);
    Route::get('/suratjalan/{no_mutasi_gudang}/prosescabang', [SuratjalanController::class, 'prosescabang']);
    Route::post('/suratjalan/{no_mutasi_gudang}/storeprosescabang', [SuratjalanController::class, 'storeprosescabang']);
    Route::delete('/suratjalan/{no_mutasi_gudang}/batalkansjcabang', [SuratjalanController::class, 'batalkansjcabang']);

    //Mutasi Gudang Cabang
    Route::get('/mutasigudangcabang/transitin', [MutasigudangcabangController::class, 'transitin']);
    Route::get('/mutasigudangcabang/transitin/{no_mutasi_gudang_cabang}/create', [MutasigudangcabangController::class, 'transitin_create']);
    Route::post('/mutasigudangcabang/transitin/{no_mutasi_gudang_cabang}/store', [MutasigudangcabangController::class, 'transitin_store']);
    Route::delete('/mutasigudangcabang/transitin/{no_suratjalan}/batal', [MutasigudangcabangController::class, 'transitin_batal']);
    Route::get('/mutasigudangcabang/{jenis_mutasi}', [MutasigudangcabangController::class, 'index']);
    Route::get('/mutasigudangcabang/{no_mutasi_gudang_cabang}/show', [MutasigudangcabangController::class, 'show']);
    Route::get('/mutasigudangcabang/{no_mutasi_gudang_cabang}/showdetail', [MutasigudangcabangController::class, 'showdetail']);
    Route::get('/mutasigudangcabang/{jenis_mutasi}/create', [MutasigudangcabangController::class, 'create']);
    Route::get('/mutasigudangcabang/{jenis_mutasi}/mutasicreate', [MutasigudangcabangController::class, 'mutasicreate']);
    Route::get('/mutasigudangcabang/{jenis_mutasi}/rejectgudangcreate', [MutasigudangcabangController::class, 'rejectgudangcreate']);
    Route::post('/mutasigudangcabang/store', [MutasigudangcabangController::class, 'store']);
    Route::post('/mutasigudangcabang/mutasistore', [MutasigudangcabangController::class, 'mutasistore']);
    Route::delete('/mutasigudangcabang/{no_mutasi_gudang_cabang}/delete', [MutasigudangcabangController::class, 'delete']);
    Route::get('/mutasigudangcabang/{no_mutasi_gudang_cabang}/edit', [MutasigudangcabangController::class, 'edit']);
    Route::get('/mutasigudangcabang/{no_mutasi_gudang_cabang}/mutasiedit', [MutasigudangcabangController::class, 'mutasiedit']);
    Route::get('/mutasigudangcabang/{no_mutasi_gudang_cabang}/penyesuaianedit', [MutasigudangcabangController::class, 'penyesuaianedit']);
    Route::post('/mutasigudangcabang/{no_mutasi_gudang_cabang}/update', [MutasigudangcabangController::class, 'update']);
    Route::get('/repack', [MutasigudangcabangController::class, 'repack']);
    Route::get('/kirimpusat', [MutasigudangcabangController::class, 'kirimpusat']);
    Route::get('/rejectgudang', [MutasigudangcabangController::class, 'rejectgudang']);
    Route::post('/rejectgudang/store', [MutasigudangcabangController::class, 'rejectgudangstore']);
    Route::get('/mutasigudangcabang/{kondisi}/penyesuaian', [MutasigudangcabangController::class, 'penyesuaian']);
    Route::get('/mutasigudangcabang/{jenis_mutasi}/penyesuaiancreate', [MutasigudangcabangController::class, 'penyesuaiancreate']);
    Route::post('/mutasigudangcabang/penyesuaianstore', [MutasigudangcabangController::class, 'penyesuaianstore']);

    //Laporan Gudang Cabang
    Route::get('/laporangudangcabang/persediaan', [LaporangudangcabangController::class, 'persediaan']);
    Route::get('/laporangudangcabang/badstok', [LaporangudangcabangController::class, 'badstok']);
    Route::get('/laporangudangcabang/rekapbj', [LaporangudangcabangController::class, 'rekapbj']);
    Route::get('/laporangudangcabang/mutasidpb', [LaporangudangcabangController::class, 'mutasidpb']);
    Route::get('/laporangudangcabang/rekonsiliasibj', [LaporangudangcabangController::class, 'rekonsiliasibj']);
    Route::post('/laporangudangcabang/persediaan/cetak', [LaporangudangcabangController::class, 'cetak_persediaan']);
    Route::post('/laporangudangcabang/badstok/cetak', [LaporangudangcabangController::class, 'cetak_badstok']);
    Route::post('/laporangudangcabang/rekapbj/cetak', [LaporangudangcabangController::class, 'cetak_rekapbj']);
    Route::post('/laporangudangcabang/mutasidpb/cetak', [LaporangudangcabangController::class, 'cetak_mutasidpb']);
    Route::post('/laporangudangcabang/rekonsiliasibj/cetak', [LaporangudangcabangController::class, 'cetak_rekonsiliasibj']);

    //Laporan Gudang Logistik
    Route::get('/laporangudanglogistik/pemasukan', [LaporangudanglogistikController::class, 'pemasukan']);
    Route::get('/laporangudanglogistik/pengeluaran', [LaporangudanglogistikController::class, 'pengeluaran']);
    Route::get('/laporangudanglogistik/persediaan', [LaporangudanglogistikController::class, 'persediaan']);
    Route::get('/laporangudanglogistik/persediaanopname', [LaporangudanglogistikController::class, 'persediaanopname']);
    Route::post('/laporangudanglogistik/pemasukan/cetak', [LaporangudanglogistikController::class, 'cetak_pemasukan']);
    Route::post('/laporangudanglogistik/pengeluaran/cetak', [LaporangudanglogistikController::class, 'cetak_pengeluaran']);
    Route::post('/laporangudanglogistik/persediaan/cetak', [LaporangudanglogistikController::class, 'cetak_persediaan']);
    Route::post('/laporangudanglogistik/persediaanopname/cetak', [LaporangudanglogistikController::class, 'cetak_persediaanopname']);

    //Gudang Logistik
    Route::get('/pemasukangudanglogistik', [PemasukangudanglogistikController::class, 'index']);
    Route::get('/pemasukangudanglogistik/create', [PemasukangudanglogistikController::class, 'create']);
    Route::post('/pemasukangudanglogistik/cektemp', [PemasukangudanglogistikController::class, 'cektemp']);
    Route::get('/pemasukangudanglogistik/getbarang', [PemasukangudanglogistikController::class, 'getbarang']);
    Route::get('/pemasukangudanglogistik/showtemp', [PemasukangudanglogistikController::class, 'showtemp']);
    Route::post('/pemasukangudanglogistik/storetemp', [PemasukangudanglogistikController::class, 'storetemp']);
    Route::post('/pemasukangudanglogistik/store', [PemasukangudanglogistikController::class, 'store']);
    Route::post('/pemasukangudanglogistik/deletetemp', [PemasukangudanglogistikController::class, 'deletetemp']);
    Route::post('/pemasukangudanglogistik/show', [PemasukangudanglogistikController::class, 'show']);
    Route::delete('/pemasukangudanglogistik/{nobukti_pemasukan}/delete', [PemasukangudanglogistikController::class, 'delete']);
    Route::get('/pengeluarangudanglogistik', [PengeluarangudanglogistikController::class, 'index']);
    Route::post('/pengeluarangudanglogistik/show', [PengeluarangudanglogistikController::class, 'show']);

    Route::get('/pengeluarangudanglogistik/create', [PengeluarangudanglogistikController::class, 'create']);
    Route::post('/pengeluarangudanglogistik/cektemp', [PengeluarangudanglogistikController::class, 'cektemp']);
    Route::post('/pengeluarangudanglogistik/storetemp', [PengeluarangudanglogistikController::class, 'storetemp']);
    Route::get('/pengeluarangudanglogistik/getbarang', [PengeluarangudanglogistikController::class, 'getbarang']);
    Route::get('/pengeluarangudanglogistik/showtemp', [PengeluarangudanglogistikController::class, 'showtemp']);
    Route::post('/pengeluarangudanglogistik/deletetemp', [PengeluarangudanglogistikController::class, 'deletetemp']);
    Route::post('/pengeluarangudanglogistik/store', [PengeluarangudanglogistikController::class, 'store']);
    Route::get('/pengeluarangudanglogistik/{nobukti_pengeluaran}/edit', [PengeluarangudanglogistikController::class, 'edit']);
    Route::post('/pengeluarangudanglogistik/cekbarang', [PengeluarangudanglogistikController::class, 'cekbarang']);
    Route::get('/pengeluarangudanglogistik/{nobukti_pengeluaran}/showbarang', [PengeluarangudanglogistikController::class, 'showbarang']);
    Route::post('/pengeluarangudanglogistik/storebarang', [PengeluarangudanglogistikController::class, 'storebarang']);
    Route::post('/pengeluarangudanglogistik/editbarang', [PengeluarangudanglogistikController::class, 'editbarang']);
    Route::post('/pengeluarangudanglogistik/updatebarang', [PengeluarangudanglogistikController::class, 'updatebarang']);
    Route::post('/pengeluarangudanglogistik/deletebarang', [PengeluarangudanglogistikController::class, 'deletebarang']);
    Route::post('/pengeluarangudanglogistik/{nobukti_pengeluaran}/update', [PengeluarangudanglogistikController::class, 'update']);
    Route::delete('/pengeluarangudanglogistik/{nobukti_pengeluaran}/delete', [PengeluarangudanglogistikController::class, 'delete']);


    //Saldo Awal Gudang Logistik
    Route::get('/saldoawalgudanglogistik', [SaldoawalgudanglogistikController::class, 'index']);
    Route::get('/saldoawalgudanglogistik/create', [SaldoawalgudanglogistikController::class, 'create']);
    Route::get('/saldoawalgudanglogistik/{kode_saldoawal}/edit', [SaldoawalgudanglogistikController::class, 'edit']);
    Route::get('/saldoawalgudanglogistik/{kode_saldoawal}/{kode_barang}/editbarang', [SaldoawalgudanglogistikController::class, 'editbarang']);
    Route::post('/saldoawalgudanglogistik/{kode_saldoawal}/{kode_barang}/updatebarang', [SaldoawalgudanglogistikController::class, 'updatebarang']);
    Route::post('/saldoawalgudanglogistik/getdetailsaldo', [SaldoawalgudanglogistikController::class, 'getdetailsaldo']);
    Route::post('/saldoawalgudanglogistik/store', [SaldoawalgudanglogistikController::class, 'store']);
    Route::delete('/saldoawalgudanglogistik/{kode_saldoawal}/delete', [SaldoawalgudanglogistikController::class, 'delete']);

    //Opname Gudang Logistik
    Route::get('/opnamegudanglogistik', [OpnamegudanglogistikController::class, 'index']);
    Route::get('/opnamegudanglogistik/create', [OpnamegudanglogistikController::class, 'create']);
    Route::get('/opnamegudanglogistik/{kode_opname}/edit', [OpnamegudanglogistikController::class, 'edit']);
    Route::get('/opnamegudanglogistik/{kode_opname}/{kode_barang}/editbarang', [OpnamegudanglogistikController::class, 'editbarang']);
    Route::post('/opnamegudanglogistik/{kode_opname}/{kode_barang}/updatebarang', [OpnamegudanglogistikController::class, 'updatebarang']);
    Route::post('/opnamegudanglogistik/getdetailsaldo', [OpnamegudanglogistikController::class, 'getdetailsaldo']);
    Route::post('/opnamegudanglogistik/store', [OpnamegudanglogistikController::class, 'store']);
    Route::delete('/opnamegudanglogistik/{kode_opname}/delete', [OpnamegudanglogistikController::class, 'delete']);



    //Gudang Bahan

    //Pemasukan
    Route::get('/pemasukangudangbahan', [PemasukangudangbahanController::class, 'index']);
    Route::post('/pemasukangudangbahan/show', [PemasukangudangbahanController::class, 'show']);
    Route::delete('/pemasukangudangbahan/{nobukti_pemasukan}/delete', [PemasukangudangbahanController::class, 'delete']);

    Route::get('/pemasukangudangbahan/create', [PemasukangudangbahanController::class, 'create']);
    Route::post('/pemasukangudangbahan/cektemp', [PemasukangudangbahanController::class, 'cektemp']);
    Route::post('/pemasukangudangbahan/storetemp', [PemasukangudangbahanController::class, 'storetemp']);
    Route::get('/pemasukangudangbahan/getbarang', [PemasukangudangbahanController::class, 'getbarang']);
    Route::get('/pemasukangudangbahan/showtemp', [PemasukangudangbahanController::class, 'showtemp']);
    Route::post('/pemasukangudangbahan/deletetemp', [PemasukangudangbahanController::class, 'deletetemp']);
    Route::post('/pemasukangudangbahan/store', [PemasukangudangbahanController::class, 'store']);

    Route::get('/pemasukangudangbahan/{nobukti_pemasukan}/edit', [PemasukangudangbahanController::class, 'edit']);
    Route::post('/pemasukangudangbahan/cekbarang', [PemasukangudangbahanController::class, 'cekbarang']);
    Route::get('/pemasukangudangbahan/{nobukti_pemasukan}/showbarang', [PemasukangudangbahanController::class, 'showbarang']);
    Route::post('/pemasukangudangbahan/storebarang', [PemasukangudangbahanController::class, 'storebarang']);
    Route::post('/pemasukangudangbahan/editbarang', [PemasukangudangbahanController::class, 'editbarang']);
    Route::post('/pemasukangudangbahan/updatebarang', [PemasukangudangbahanController::class, 'updatebarang']);
    Route::post('/pemasukangudangbahan/deletebarang', [PemasukangudangbahanController::class, 'deletebarang']);
    Route::post('/pemasukangudangbahan/{nobukti_pemasukan}/update', [PemasukangudangbahanController::class, 'update']);
    Route::delete('/pemasukangudangbahan/{nobukti_pemasukan}/delete', [PemasukangudangbahanController::class, 'delete']);

    //Pengeluaran
    Route::get('/pengeluarangudangbahan', [PengeluarangudangbahanController::class, 'index']);
    Route::post('/pengeluarangudangbahan/show', [PengeluarangudangbahanController::class, 'show']);
    Route::delete('/pengeluarangudangbahan/{nobukti_pemasukan}/delete', [PengeluarangudangbahanController::class, 'delete']);

    Route::get('/pengeluarangudangbahan/create', [PengeluarangudangbahanController::class, 'create']);
    Route::post('/pengeluarangudangbahan/cektemp', [PengeluarangudangbahanController::class, 'cektemp']);
    Route::post('/pengeluarangudangbahan/storetemp', [PengeluarangudangbahanController::class, 'storetemp']);
    Route::get('/pengeluarangudangbahan/getbarang', [PengeluarangudangbahanController::class, 'getbarang']);
    Route::get('/pengeluarangudangbahan/showtemp', [PengeluarangudangbahanController::class, 'showtemp']);
    Route::post('/pengeluarangudangbahan/deletetemp', [PengeluarangudangbahanController::class, 'deletetemp']);
    Route::post('/pengeluarangudangbahan/store', [PengeluarangudangbahanController::class, 'store']);

    Route::get('/pengeluarangudangbahan/{nobukti_pemasukan}/edit', [PengeluarangudangbahanController::class, 'edit']);
    Route::post('/pengeluarangudangbahan/cekbarang', [PengeluarangudangbahanController::class, 'cekbarang']);
    Route::get('/pengeluarangudangbahan/{nobukti_pemasukan}/showbarang', [PengeluarangudangbahanController::class, 'showbarang']);
    Route::post('/pengeluarangudangbahan/storebarang', [PengeluarangudangbahanController::class, 'storebarang']);
    Route::post('/pengeluarangudangbahan/editbarang', [PengeluarangudangbahanController::class, 'editbarang']);
    Route::post('/pengeluarangudangbahan/updatebarang', [PengeluarangudangbahanController::class, 'updatebarang']);
    Route::post('/pengeluarangudangbahan/deletebarang', [PengeluarangudangbahanController::class, 'deletebarang']);
    Route::post('/pengeluarangudangbahan/{nobukti_pemasukan}/update', [PengeluarangudangbahanController::class, 'update']);
    Route::delete('/pengeluarangudangbahan/{nobukti_pemasukan}/delete', [PengeluarangudangbahanController::class, 'delete']);

    //Saldo Awal Gudang bahan
    Route::get('/saldoawalgudangbahan', [SaldoawalgudangbahanController::class, 'index']);
    Route::get('/saldoawalgudangbahan/create', [SaldoawalgudangbahanController::class, 'create']);
    Route::get('/saldoawalgudangbahan/{kode_saldoawal}/edit', [SaldoawalgudangbahanController::class, 'edit']);
    Route::get('/saldoawalgudangbahan/{kode_saldoawal}/{kode_barang}/editbarang', [SaldoawalgudangbahanController::class, 'editbarang']);
    Route::post('/saldoawalgudangbahan/{kode_saldoawal}/{kode_barang}/updatebarang', [SaldoawalgudangbahanController::class, 'updatebarang']);
    Route::post('/saldoawalgudangbahan/getdetailsaldo', [SaldoawalgudangbahanController::class, 'getdetailsaldo']);
    Route::post('/saldoawalgudangbahan/store', [SaldoawalgudangbahanController::class, 'store']);
    Route::delete('/saldoawalgudangbahan/{kode_saldoawal}/delete', [SaldoawalgudangbahanController::class, 'delete']);

    //Opname Gudang Bahan
    Route::get('/opnamegudangbahan', [OpnamegudangbahanController::class, 'index']);
    Route::get('/opnamegudangbahan/create', [OpnamegudangbahanController::class, 'create']);
    Route::get('/opnamegudangbahan/{kode_opname}/edit', [OpnamegudangbahanController::class, 'edit']);
    Route::get('/opnamegudangbahan/{kode_opname}/{kode_barang}/editbarang', [OpnamegudangbahanController::class, 'editbarang']);
    Route::post('/opnamegudangbahan/{kode_opname}/{kode_barang}/updatebarang', [OpnamegudangbahanController::class, 'updatebarang']);
    Route::post('/opnamegudangbahan/getdetailsaldo', [OpnamegudangbahanController::class, 'getdetailsaldo']);
    Route::post('/opnamegudangbahan/store', [OpnamegudangbahanController::class, 'store']);
    Route::delete('/opnamegudangbahan/{kode_opname}/delete', [OpnamegudangbahanController::class, 'delete']);

    //Laporan Gudang Bahan
    Route::get('/laporangudangbahan/pemasukan', [LaporangudangbahanController::class, 'pemasukan']);
    Route::get('/laporangudangbahan/pengeluaran', [LaporangudangbahanController::class, 'pengeluaran']);
    Route::get('/laporangudangbahan/persediaan', [LaporangudangbahanController::class, 'persediaan']);
    Route::get('/laporangudangbahan/kartugudang', [LaporangudangbahanController::class, 'kartugudang']);
    Route::get('/laporangudangbahan/rekappersediaan', [LaporangudangbahanController::class, 'rekappersediaan']);

    Route::post('/laporangudangbahan/pemasukan/cetak', [LaporangudangbahanController::class, 'cetak_pemasukan']);
    Route::post('/laporangudangbahan/pengeluaran/cetak', [LaporangudangbahanController::class, 'cetak_pengeluaran']);
    Route::post('/laporangudangbahan/persediaan/cetak', [LaporangudangbahanController::class, 'cetak_persediaan']);
    Route::post('/laporangudangbahan/kartugudang/cetak', [LaporangudangbahanController::class, 'cetak_kartugudang']);
    Route::post('/laporangudangbahan/rekappersediaan/cetak', [LaporangudangbahanController::class, 'cetak_rekappersediaan']);

    //Permintaan Produksi
    Route::get('/permintaanproduksi', [PermintaanproduksiController::class, 'index']);
    Route::post('/permintaanproduksi/show', [PermintaanproduksiController::class, 'show']);
    Route::get('/permintaanproduksi/create', [PermintaanproduksiController::class, 'create']);
    Route::get('/permintaanproduksi/{no_permintaan}/approve', [PermintaanproduksiController::class, 'approve']);
    Route::get('/permintaanproduksi/{no_permintaan}/batalkanapprove', [PermintaanproduksiController::class, 'batalkanapprove']);
    Route::post('/permintaanproduksi/store', [PermintaanproduksiController::class, 'store']);
    Route::delete('/permintaanproduksi/{no_permintaan}/delete', [PermintaanproduksiController::class, 'delete']);

    //FSTHP Gudang Jadi
    Route::get('/fsthpgj', [FsthpController::class, 'index']);
    Route::get('/fsthpgj/{no_mutasi_produksi}/approve', [FsthpController::class, 'approve']);
    Route::get('/fsthpgj/{no_mutasi_produksi}/batalkanapprove', [FsthpController::class, 'batalkanapprove']);

    //Surat jalan
    Route::get('/suratjalan', [SuratjalanController::class, 'index']);
    Route::get('/suratjalan/{no_mutasi_gudang}/show', [SuratjalanController::class, 'show']);
    Route::get('/suratjalan/{no_mutasi_gudang}/cetak', [SuratjalanController::class, 'cetak']);
    Route::get('/suratjalan/{no_permintaan_pengiriman}/create', [SuratjalanController::class, 'create']);
    Route::post('/suratjalan/cektemp', [SuratjalanController::class, 'cektemp']);
    Route::get('/suratjalan/{no_permintaan_pengiriman}/showtemp', [SuratjalanController::class, 'showtemp']);
    Route::post('/suratjalan/storetemp', [SuratjalanController::class, 'storetemp']);
    Route::post('/suratjalan/deletetemp', [SuratjalanController::class, 'deletetemp']);
    Route::post('/suratjalan/masukankerealisasi', [SuratjalanController::class, 'masukankerealisasi']);
    Route::post('/suratjalan/buatnomorsj', [SuratjalanController::class, 'buatnomorsj']);
    Route::post('/suratjalan/store', [SuratjalanController::class, 'store']);
    Route::get('/suratjalan/{no_mutasi_gudang}/batalkansuratjalan', [SuratjalanController::class, 'batalkansuratjalan']);


    //Permintaan Pengiriman GJ
    Route::get('/permintaanpengirimangj', [PermintaanpengirimanController::class, 'index']);

    //RepackReject
    Route::get('/repackgj/{jenis_mutasi}', [RepackrejectgudangjadiController::class, 'index']);
    Route::get('/rejectgj/{jenis_mutasi}', [RepackrejectgudangjadiController::class, 'index']);
    Route::get('/repackrejectgj/{jenis_mutasi}/showtemp', [RepackrejectgudangjadiController::class, 'showtemp']);
    Route::get('/repackrejectgj/{no_mutasi_gudang}/show', [RepackrejectgudangjadiController::class, 'show']);
    Route::post('/repackrejectgj/{jenis_mutasi}/cektemp', [RepackrejectgudangjadiController::class, 'cektemp']);
    Route::post('/repackrejectgj/storetemp', [RepackrejectgudangjadiController::class, 'storetemp']);
    Route::post('/repackrejectgj/deletetemp', [RepackrejectgudangjadiController::class, 'deletetemp']);
    Route::post('/repackrejectgj/buatnomorrepackreject', [RepackrejectgudangjadiController::class, 'buatnomorrepackreject']);
    Route::post('/repackrejectgj/store', [RepackrejectgudangjadiController::class, 'store']);
    Route::delete('/repackrejectgj/{no_mutasi_gudang}/delete', [RepackrejectgudangjadiController::class, 'delete']);

    //Mutasi Lainnya
    Route::get('/lainnyagj', [MutasilaingjController::class, 'index']);
    Route::get('/lainnyagj/{no_mutasi_gudang}/show', [MutasilaingjController::class, 'show']);
    Route::delete('/lainnyagj/{no_mutasi_gudang}/delete', [MutasilaingjController::class, 'delete']);
    Route::post('/lainnyagj/buatnomormutasi', [MutasilaingjController::class, 'buatnomormutasi']);
    Route::post('/lainnyagj/{jenis_mutasi}/cektemp', [MutasilaingjController::class, 'cektemp']);
    Route::post('/lainnyagj/storetemp', [MutasilaingjController::class, 'storetemp']);
    Route::post('/lainnyagj/deletetemp', [MutasilaingjController::class, 'deletetemp']);
    Route::get('/lainnyagj/{jenis_mutasi}/showtemp', [MutasilaingjController::class, 'showtemp']);
    Route::post('/lainnyagj/store', [MutasilaingjController::class, 'store']);

    //Angkutan
    Route::get('/angkutan', [AngkutanController::class, 'index']);
    Route::delete('/angkutan/{no_surat_jalan}/delete', [AngkutanController::class, 'delete']);
    Route::get('/angkutan/{no_surat_jalan}/edit', [AngkutanController::class, 'edit']);
    Route::post('/angkutan/{no_surat_jalan}/update', [AngkutanController::class, 'update']);


    //Laporan Persediaan Gudang Jadi
    Route::get('/laporangudangjadi/persediaan', [LaporangudangjadiController::class, 'persediaan']);
    Route::get('/laporangudangjadi/rekappersediaan', [LaporangudangjadiController::class, 'rekappersediaan']);
    Route::get('/laporangudangjadi/rekaphasilproduksi', [LaporangudangjadiController::class, 'rekaphasilproduksi']);
    Route::get('/laporangudangjadi/rekappengeluaran', [LaporangudangjadiController::class, 'rekappengeluaran']);
    Route::get('/laporangudangjadi/realisasikiriman', [LaporangudangjadiController::class, 'realisasikiriman']);
    Route::get('/laporangudangjadi/realisasioman', [LaporangudangjadiController::class, 'realisasioman']);
    Route::get('/laporangudangjadi/angkutan', [LaporangudangjadiController::class, 'angkutan']);
    Route::post('/laporangudangjadi/persediaan/cetak', [LaporangudangjadiController::class, 'cetak_persediaan']);
    Route::post('/laporangudangjadi/rekappersediaan/cetak', [LaporangudangjadiController::class, 'cetak_rekappersediaan']);
    Route::post('/laporangudangjadi/rekaphasilproduksi/cetak', [LaporangudangjadiController::class, 'cetak_rekaphasilproduksi']);
    Route::post('/laporangudangjadi/rekappengeluaran/cetak', [LaporangudangjadiController::class, 'cetak_rekappengeluaran']);
    Route::post('/laporangudangjadi/realisasikiriman/cetak', [LaporangudangjadiController::class, 'cetak_realisasikiriman']);
    Route::post('/laporangudangjadi/realisasioman/cetak', [LaporangudangjadiController::class, 'cetak_realisasioman']);
    Route::post('/laporangudangjadi/angkutan/cetak', [LaporangudangjadiController::class, 'cetak_angkutan']);

    //Produksi
    Route::get('/produksi/analytics', [ProduksiController::class, 'analytics']);
    Route::post('/loadrekapproduksi', [ProduksiController::class, 'loadrekapproduksi']);
    Route::post('/loadgrafikproduksi', [ProduksiController::class, 'loadgrafikproduksi']);

    //Laporanproduksi
    Route::get('/laporanproduksi/mutasiproduksi', [LaporanproduksiController::class, 'mutasiproduksi']);
    Route::get('/laporanproduksi/rekapmutasiproduksi', [LaporanproduksiController::class, 'rekapmutasiproduksi']);
    Route::get('/laporanproduksi/pemasukanproduksi', [LaporanproduksiController::class, 'pemasukanproduksi']);
    Route::get('/laporanproduksi/pengeluaranproduksi', [LaporanproduksiController::class, 'pengeluaranproduksi']);
    Route::get('/laporanproduksi/rekappersediaanbarangproduksi', [LaporanproduksiController::class, 'rekappersediaanbarangproduksi']);
    Route::post('/laporanproduksi/mutasiproduksi/cetak', [LaporanproduksiController::class, 'cetak_mutasiproduksi']);
    Route::post('/laporanproduksi/rekapmutasiproduksi/cetak', [LaporanproduksiController::class, 'cetak_rekapmutasiproduksi']);
    Route::post('/laporanproduksi/pemasukanproduksi/cetak', [LaporanproduksiController::class, 'cetak_pemasukanproduksi']);
    Route::post('/laporanproduksi/pengeluaranproduksi/cetak', [LaporanproduksiController::class, 'cetak_pengeluaranproduksi']);
    Route::post('/laporanproduksi/rekappersediaanbarangproduksi/cetak', [LaporanproduksiController::class, 'cetak_rekappersediaanbarangproduksi']);

    //Saldo Awal Piutang
    Route::get('/saldoawalpiutang', [PenjualanController::class, 'saldoawalpiutang']);
    Route::post('/loadsaldoawalpiutang', [PenjualanController::class, 'loadsaldoawalpiutang']);
    Route::post('/generatesaldoawalpiutang', [PenjualanController::class, 'generatesaldoawalpiutang']);



    //BPBJ

    Route::get('/bpbj', [BpbjController::class, 'index']);
    Route::post('/bpbj/show', [BpbjController::class, 'show']);
    Route::post('/bpbj/storetemp', [BpbjController::class, 'storetemp']);
    Route::post('/bpbj/buat_nomor_bpbj', [BpbjController::class, 'buat_nomor_bpbj']);
    Route::post('/bpbj/cekbpbjtemp', [BpbjController::class, 'cekbpbjtemp']);
    Route::post('/bpbj/deletetemp', [BpbjController::class, 'deletetemp']);
    Route::get('/bpbj/{kode_produk}/showtemp', [BpbjController::class, 'showtemp']);
    Route::get('/bpbj/getbarang', [BpbjController::class, 'getbarang']);
    Route::post('/bpbj/store', [BpbjController::class, 'store']);
    Route::delete('/bpbj/{no_mutasi_produksi}/delete', [BpbjController::class, 'delete']);


    //Fsthp
    Route::get('/fsthp', [FsthpController::class, 'index']);
    Route::post('/fsthp/show', [FsthpController::class, 'show']);
    Route::post('/fsthp/storetemp', [FsthpController::class, 'storetemp']);
    Route::post('/fsthp/buat_nomor_fsthp', [FsthpController::class, 'buat_nomor_fsthp']);
    Route::post('/fsthp/cekfsthptemp', [FsthpController::class, 'cekfsthptemp']);
    Route::post('/fsthp/deletetemp', [FsthpController::class, 'deletetemp']);
    Route::get('/fsthp/{kode_produk}/{unit}/{shift}/showtemp', [FsthpController::class, 'showtemp']);
    Route::get('/fsthp/getbarang', [FsthpController::class, 'getbarang']);
    Route::post('/fsthp/store', [FsthpController::class, 'store']);
    Route::delete('/fsthp/{no_mutasi_produksi}/delete', [FsthpController::class, 'delete']);

    //Pemasukan Produksi
    Route::get('/pemasukanproduksi', [PemasukanproduksiController::class, 'index']);
    Route::get('/pemasukanproduksi/{kode_dept}/getbarang', [PemasukanproduksiController::class, 'getbarang']);
    Route::get('/pemasukanproduksi/create', [PemasukanproduksiController::class, 'create']);
    Route::get('/pemasukanproduksi/showtemp', [PemasukanproduksiController::class, 'showtemp']);
    Route::get('/pemasukanproduksi/{nobukti_pemasukan}/showbarang', [PemasukanproduksiController::class, 'showbarang']);
    Route::get('/pemasukanproduksi/{nobukti_pemasukan}/edit', [PemasukanproduksiController::class, 'edit']);
    Route::post('/pemasukanproduksi/editbarang', [PemasukanproduksiController::class, 'editbarang']);
    Route::post('/pemasukanproduksi/updatebarang', [PemasukanproduksiController::class, 'updatebarang']);
    Route::post('/pemasukanproduksi/{nobukti_pemasukan}/update', [PemasukanproduksiController::class, 'update']);
    Route::post('/pemasukanproduksi/deletetemp', [PemasukanproduksiController::class, 'deletetemp']);
    Route::post('/pemasukanproduksi/deletebarang', [PemasukanproduksiController::class, 'deletebarang']);
    Route::post('/pemasukanproduksi/show', [PemasukanproduksiController::class, 'show']);
    Route::post('/pemasukanproduksi/cektemp', [PemasukanproduksiController::class, 'cektemp']);
    Route::post('/pemasukanproduksi/cekbarang', [PemasukanproduksiController::class, 'cekbarang']);
    Route::post('/pemasukanproduksi/storetemp', [PemasukanproduksiController::class, 'storetemp']);
    Route::post('/pemasukanproduksi/storebarang', [PemasukanproduksiController::class, 'storebarang']);
    Route::post('/pemasukanproduksi/store', [PemasukanproduksiController::class, 'store']);
    Route::delete('/pemasukanproduksi/{nobukti_pemasukan}/delete', [PemasukanproduksiController::class, 'delete']);


    //Pengeluaran Produksi
    Route::get('/pengeluaranproduksi', [PengeluaranproduksiController::class, 'index']);
    Route::get('/pengeluaranproduksi/getbarang', [PengeluaranproduksiController::class, 'getbarang']);
    Route::post('/pengeluaranproduksi/show', [PengeluaranproduksiController::class, 'show']);
    Route::get('/pengeluaranproduksi/showtemp', [PengeluaranproduksiController::class, 'showtemp']);
    Route::get('/pengeluaranproduksi/create', [PengeluaranproduksiController::class, 'create']);
    Route::post('/pengeluaranproduksi/cektemp', [PengeluaranproduksiController::class, 'cektemp']);
    Route::post('/pengeluaranproduksi/storetemp', [PengeluaranproduksiController::class, 'storetemp']);
    Route::post('/pengeluaranproduksi/deletetemp', [PengeluaranproduksiController::class, 'deletetemp']);
    Route::post('/pengeluaranproduksi/store', [PengeluaranproduksiController::class, 'store']);
    Route::get('/pengeluaranproduksi/{nobukti_pengeluaran}/edit', [PengeluaranproduksiController::class, 'edit']);
    Route::post('/pengeluaranproduksi/cekbarang', [PengeluaranproduksiController::class, 'cekbarang']);
    Route::get('/pengeluaranproduksi/{nobukti_pengeluaran}/showbarang', [PengeluaranproduksiController::class, 'showbarang']);
    Route::post('/pengeluaranproduksi/storebarang', [PengeluaranproduksiController::class, 'storebarang']);
    Route::post('/pengeluaranproduksi/editbarang', [PengeluaranproduksiController::class, 'editbarang']);
    Route::post('/pengeluaranproduksi/updatebarang', [PengeluaranproduksiController::class, 'updatebarang']);
    Route::post('/pengeluaranproduksi/deletebarang', [PengeluaranproduksiController::class, 'deletebarang']);
    Route::post('/pengeluaranproduksi/{nobukti_pengeluaran}/update', [PengeluaranproduksiController::class, 'update']);
    Route::delete('/pengeluaranproduksi/{nobukti_pengeluaran}/delete', [PengeluaranproduksiController::class, 'delete']);

    //Saldo Awal Mutasi Barang Produksi
    Route::get('/saldoawalmutasibarangproduksi', [SaldoawalmutasibarangproduksiController::class, 'index']);
    Route::get('/saldoawalmutasibarangproduksi/create', [SaldoawalmutasibarangproduksiController::class, 'create']);
    Route::delete('/saldoawalmutasibarangproduksi/{kode_saldoawal}/delete', [SaldoawalmutasibarangproduksiController::class, 'delete']);
    Route::get('/saldoawalmutasibarangproduksi/{kode_saldoawal}/edit', [SaldoawalmutasibarangproduksiController::class, 'edit']);
    Route::get('/saldoawalmutasibarangproduksi/{kode_saldoawal}/{kode_barang}/editbarang', [SaldoawalmutasibarangproduksiController::class, 'editbarang']);
    Route::post('/saldoawalmutasibarangproduksi/{kode_saldoawal}/{kode_barang}/updatebarang', [SaldoawalmutasibarangproduksiController::class, 'updatebarang']);
    Route::post('saldoawalmutasibarangproduksi/getdetailsaldo', [SaldoawalmutasibarangproduksiController::class, 'getdetailsaldo']);
    Route::post('saldoawalmutasibarangproduksi/store', [SaldoawalmutasibarangproduksiController::class, 'store']);

    //Opname Mutasi Barang Produksi
    Route::get('/opnamemutasibarangproduksi', [OpnamemutasibarangproduksiController::class, 'index']);
    Route::get('/opnamemutasibarangproduksi/create', [OpnamemutasibarangproduksiController::class, 'create']);
    Route::post('opnamemutasibarangproduksi/getdetailopname', [OpnamemutasibarangproduksiController::class, 'getdetailopname']);
    Route::delete('/opnamemutasibarangproduksi/{kode_opname}/delete', [OpnamemutasibarangproduksiController::class, 'delete']);
    Route::get('/opnamemutasibarangproduksi/{kode_opname}/edit', [OpnamemutasibarangproduksiController::class, 'edit']);
    Route::get('/opnamemutasibarangproduksi/{kode_opname}/{kode_barang}/editbarang', [OpnamemutasibarangproduksiController::class, 'editbarang']);
    Route::post('/opnamemutasibarangproduksi/{kode_opname}/{kode_barang}/updatebarang', [OpnamemutasibarangproduksiController::class, 'updatebarang']);
    Route::post('opnamemutasibarangproduksi/store', [OpnamemutasibarangproduksiController::class, 'store']);





    //Acounting
    //Input HPP
    Route::get('/hpp', [HppController::class, 'index']);
    Route::post('/hpp/show', [HppController::class, 'show']);
    Route::post('/hpp/store', [HppController::class, 'store']);

    //Input Harga Awal
    Route::get('/hargaawal', [HargaawalController::class, 'index']);
    Route::post('/hargaawal/show', [HargaawalController::class, 'show']);
    Route::post('/hargaawal/store', [HargaawalController::class, 'store']);

    //Saldoawal Buku Besar
    Route::get('/saldoawalbb', [SaldoawalbukubesarController::class, 'index']);
    Route::delete('/saldoawalbb/{kode_saldoawal}/delete', [SaldoawalbukubesarController::class, 'delete']);
    Route::get('/saldoawalbb/create', [SaldoawalbukubesarController::class, 'create']);
    Route::get('/saldoawalbb/{kode_saldoawal}/show', [SaldoawalbukubesarController::class, 'show']);
    Route::post('/saldoawalbb/getdetailsaldo', [SaldoawalbukubesarController::class, 'getdetailsaldo']);
    Route::post('/saldoawalbb/store', [SaldoawalbukubesarController::class, 'store']);

    //Jurnal Umum
    Route::get('/jurnalumum', [JurnalumumController::class, 'index']);
    Route::get('/jurnalumum/create', [JurnalumumController::class, 'create']);
    Route::get('/jurnalumum/{kodejurnal}/edit', [JurnalumumController::class, 'edit']);
    Route::post('/jurnalumum/store', [JurnalumumController::class, 'store']);
    Route::post('/jurnalumum/{kode_jurnal}/update', [JurnalumumController::class, 'update']);
    Route::post('/jurnalumum/storetemp', [JurnalumumController::class, 'storetemp']);
    Route::get('/jurnalumum/showtemp', [JurnalumumController::class, 'showtemp']);
    Route::post('/jurnalumum/deletetemp', [JurnalumumController::class, 'deletetemp']);
    Route::post('/jurnalumum/cektemp', [JurnalumumController::class, 'cektemp']);
    Route::delete('/jurnalumum/{kode_jurnal}/delete', [JurnalumumController::class, 'delete']);

    //Laporan Accounting
    Route::get('/laporanaccounting/rekapbj', [LaporanaccountingController::class, 'rekapbj']);
    Route::get('/laporanaccounting/rekappersediaan', [LaporanaccountingController::class, 'rekappersediaan']);
    Route::get('/laporanaccounting/bukubesar', [LaporanaccountingController::class, 'bukubesar']);
    Route::get('/laporanaccounting/neraca', [LaporanaccountingController::class, 'neraca']);
    Route::get('/laporanaccounting/labarugi', [LaporanaccountingController::class, 'labarugi']);
    Route::get('/laporanaccounting/jurnalumum', [LaporanaccountingController::class, 'jurnalumum']);
    Route::get('/laporanaccounting/costratio', [LaporanaccountingController::class, 'costratio']);
    Route::post('/laporanaccounting/rekapbj/cetak', [LaporanaccountingController::class, 'cetak_rekapbj']);
    Route::post('/laporanaccounting/rekappersediaan/cetak', [LaporanaccountingController::class, 'cetak_rekappersediaan']);
    Route::post('/laporanaccounting/bukubesar/cetak', [LaporanaccountingController::class, 'cetak_bukubesar']);
    Route::post('/laporanaccounting/neraca/cetak', [LaporanaccountingController::class, 'cetak_neraca']);
    Route::post('/laporanaccounting/labarugi/cetak', [LaporanaccountingController::class, 'cetak_labarugi']);
    Route::post('/laporanaccounting/jurnalumum/cetak', [LaporanaccountingController::class, 'cetak_jurnalumum']);
    Route::post('/laporanaccounting/costratio/cetak', [LaporanaccountingController::class, 'cetak_costratio']);
    Route::get('/detailcrlogistik/{dari}/{sampai}/{kode_cabang}', [LaporanaccountingController::class, 'cetak_costratio_detaillogistik']);
    Route::get('/detailcrbahan/{dari}/{sampai}/{kode_cabang}', [LaporanaccountingController::class, 'cetak_costratio_detailbahan']);

    Route::get('/laporankeuangan/kaskecil', [LaporankeuanganController::class, 'kaskecil']);
    Route::post('/laporankeuangan/kaskecil/cetak', [LaporankeuanganController::class, 'cetak_kaskecil']);

    Route::get('/laporankeuangan/ledger', [LaporankeuanganController::class, 'ledger']);
    Route::post('/laporankeuangan/ledger/cetak', [LaporankeuanganController::class, 'cetak_ledger']);

    //Klaim Kas Kecil
    Route::get('/klaim', [KlaimController::class, 'index']);
    Route::get('/klaim/{kode_klaim}/{excel}/cetak', [KlaimController::class, 'cetak']);
    Route::get('/klaim/{kode_klaim}/show', [KlaimController::class, 'show']);
    Route::get('/klaim/{kode_klaim}/prosesklaim', [KlaimController::class, 'prosesklaim']);
    Route::get('/klaim/{kode_klaim}/batalkanproses', [KlaimController::class, 'batalkanproses']);
    Route::get('/klaim/{kode_klaim}/validasikaskecil', [KlaimController::class, 'validasikaskecil']);
    Route::get('/klaim/{kode_klaim}/batalkanvalidasi', [KlaimController::class, 'batalkanvalidasi']);
    Route::get('/klaim/create', [KlaimController::class, 'create']);
    Route::post('/klaim/store', [KlaimController::class, 'store']);
    Route::post('/klaim/storeprosesklaim', [KlaimController::class, 'storeprosesklaim']);
    Route::delete('/klaim/{kode_klaim}/delete', [KlaimController::class, 'delete']);

    //Laporan Keuangan

    Route::get('/laporankeuangan/penjualan', [LaporankeuanganController::class, 'penjualan']);
    Route::get('/laporankeuangan/uanglogam', [LaporankeuanganController::class, 'uanglogam']);
    Route::get('/laporankeuangan/rekapbg', [LaporankeuanganController::class, 'rekapbg']);
    Route::get('/laporankeuangan/saldokasbesar', [LaporankeuanganController::class, 'saldokasbesar']);
    Route::get('/laporankeuangan/lpu', [LaporankeuanganController::class, 'lpu']);
    Route::get('/laporankeuangan/pinjaman', [LaporankeuanganController::class, 'pinjaman']);
    Route::get('/laporankeuangan/piutangkaryawan', [LaporankeuanganController::class, 'piutangkaryawan']);
    Route::get('/laporankeuangan/kasbon', [LaporankeuanganController::class, 'kasbon']);
    Route::get('/laporankeuangan/kartupinjaman', [LaporankeuanganController::class, 'kartupinjaman']);
    Route::get('/laporankeuangan/kartukasbon', [LaporankeuanganController::class, 'kartukasbon']);
    Route::get('/laporankeuangan/kartupiutangkaryawan', [LaporankeuanganController::class, 'kartupiutangkaryawan']);
    Route::get('/laporankeuangan/kartupiutangall', [LaporankeuanganController::class, 'kartupiutangall']);

    Route::post('/laporankeuangan/penjualan/cetak', [LaporankeuanganController::class, 'cetak_penjualan']);
    Route::post('/laporankeuangan/uanglogam/cetak', [LaporankeuanganController::class, 'cetak_uanglogam']);
    Route::post('/laporankeuangan/rekapbg/cetak', [LaporankeuanganController::class, 'cetak_rekapbg']);
    Route::post('/laporankeuangan/pinjaman/cetak', [LaporankeuanganController::class, 'cetak_pinjaman']);
    Route::post('/laporankeuangan/piutangkaryawan/cetak', [LaporankeuanganController::class, 'cetak_piutangkaryawan']);
    Route::post('/laporankeuangan/pinjaman/cetakkartupinjaman', [LaporankeuanganController::class, 'cetak_kartupinjaman']);
    Route::post('/laporankeuangan/pinjaman/cetakkartukasbon', [LaporankeuanganController::class, 'cetak_kartukasbon']);
    Route::post('/laporankeuangan/piutangkaryawan/cetakkartupiutangkaryawan', [LaporankeuanganController::class, 'cetak_kartupiutangkaryawan']);
    Route::post('/laporankeuangan/piutangkaryawan/cetakkartupiutangkaryawanall', [LaporankeuanganController::class, 'cetak_kartupiutangkaryawanall']);
    Route::post('/laporankeuangan/kasbon/cetak', [LaporankeuanganController::class, 'cetak_kasbon']);
    Route::post('/laporankeuangan/saldokasbesar/cetak', [LaporankeuanganController::class, 'cetak_saldokasbesar']);
    Route::post('/laporankeuangan/lpu/cetak', [LaporankeuanganController::class, 'cetak_lpu']);

    //Salesman
    Route::get('/salesman', [SalesmanController::class, 'index']);
    Route::get('/salesman/create', [SalesmanController::class, 'create']);
    Route::post('/salesman/store', [SalesmanController::class, 'store']);
    Route::get('/salesman/{id_karyawan}/edit', [SalesmanController::class, 'edit']);
    Route::post('/salesman/{id_karyawan}/update', [SalesmanController::class, 'update']);
    Route::delete('/salesman/{id_karyawan}/delete', [SalesmanController::class, 'delete']);
    Route::post('/salesman/show', [SalesmanController::class, 'show']);

    //Penilaian Karyawan
    Route::get('/penilaiankaryawan/{kategori_jabatan}/{kantor}/list', [PenilaiankaryawanController::class, 'index']);
    Route::get('/penilaiankaryawan/{kode_penilaian}/{kategori_jabatan}/batalkan', [PenilaiankaryawanController::class, 'batalkan']);
    Route::get('/penilaiankaryawan/{kode_penilaian}/{kategori_jabatan}/approve', [PenilaiankaryawanController::class, 'approve']);
    Route::get('/penilaiankaryawan/{kode_penilaian}/{kategori_jabatan}/decline', [PenilaiankaryawanController::class, 'decline']);
    Route::post('/penilaiankaryawan/create', [PenilaiankaryawanController::class, 'create']);
    Route::post('/penilaiankaryawan/store', [PenilaiankaryawanController::class, 'store']);
    Route::get('/penilaiankaryawan/{kode_penilaian}/edit', [PenilaiankaryawanController::class, 'edit']);
    Route::get('/penilaiankaryawan/{kode_penilaian}/cetak', [PenilaiankaryawanController::class, 'cetak']);
    Route::delete('/penilaiankaryawan/{kode_penilaian}/delete', [PenilaiankaryawanController::class, 'delete']);
    Route::post('/penilaiankaryawan/{kode_penilaian}/update', [PenilaiankaryawanController::class, 'update']);



    //Harga
    Route::get('/harga', [HargaController::class, 'index']);
    Route::get('/harga/create', [HargaController::class, 'create']);
    Route::get('/harga/{kode_barang}/edit', [HargaController::class, 'edit']);
    Route::post('/harga/store', [HargaController::class, 'store']);
    Route::post('/harga/{kode_barang}/update', [HargaController::class, 'update']);
    Route::post('/harga/show', [HargaController::class, 'show']);
    Route::delete('/harga/{kode_barang}/delete', [HargaController::class, 'delete']);
    Route::post('/getbarangcabang', [HargaController::class, 'getbarangcabang']);
    Route::post('/getbarangcabangretur', [HargaController::class, 'getbarangcabangretur']);



    //Pelanggan
    Route::get('/pelanggan', [PelangganController::class, 'index']);
    Route::get('/pelanggansalesman', [PelangganController::class, 'pelanggansalesman']);
    Route::get('/pelanggan/create', [PelangganController::class, 'create']);
    Route::post('/pelanggan/store', [PelangganController::class, 'store']);
    Route::get('/pelanggan/{kode_pelanggan}/edit', [PelangganController::class, 'edit']);
    Route::post('/pelanggan/{kode_pelanggan}/update', [PelangganController::class, 'update']);
    Route::delete('/pelanggan/{kode_pelanggan}/delete', [PelangganController::class, 'delete']);
    Route::get('/pelanggan/{kode_pelanggan}/show', [PelangganController::class, 'show']);
    Route::get('/pelanggan/json', [PelangganController::class, 'json'])->name('pelanggan.json');
    Route::get('/pelanggan/getpelanggan', [PelangganController::class, 'getpelanggan']);
    Route::get('/pelanggan/showpelanggan', [PelangganController::class, 'showpelanggan']);
    Route::get('/pelanggan/deletecookie', [PelangganController::class, 'deletecookie']);
    Route::get('/pelanggan/{kode_pelanggan}/capturetoko', [PelangganController::class, 'capturetoko']);
    Route::post('/pelanggan/storecapture', [PelangganController::class, 'storecapture']);
    Route::post('/pelanggan/checkinstore', [PelangganController::class, 'checkinstore']);
    Route::get('/pelanggan/{kode_pelanggan}/checkout', [PelangganController::class, 'checkoutstore']);
    Route::get('/pelanggan/shownonaktif', [PelangganController::class, 'shownonaktif']);
    Route::get('/pelanggan/updatenonaktif', [PelangganController::class, 'updatenonaktif']);
    //Kendaraan
    Route::get('/kendaraan', [KendaraanController::class, 'index']);
    Route::get('/kendaraan/create', [KendaraanController::class, 'create']);
    Route::post('/kendaraan/store', [KendaraanController::class, 'store']);
    Route::delete('/kendaraan/{id}/delete', [KendaraanController::class, 'delete']);
    Route::get('/kendaraan/{id}/edit', [KendaraanController::class, 'edit']);
    Route::post('/kendaraan/{id}/update', [KendaraanController::class, 'update']);
    Route::get('/kendaraan/{id}/show', [KendaraanController::class, 'show']);

    //Ledger
    Route::get('/ledger', [LedgerController::class, 'index']);
    Route::get('/ledger/{kode_ledger}/create', [LedgerController::class, 'create']);
    Route::post('/ledger/storetemp', [LedgerController::class, 'storetemp']);
    Route::post('/getledgertemp', [LedgerController::class, 'getledgertemp']);
    Route::post('/cekledgertemp', [LedgerController::class, 'cekledgertemp']);
    Route::post('/ledger/deletetemp', [LedgerController::class, 'deletetemp']);
    Route::post('/ledger/store', [LedgerController::class, 'store']);
    Route::delete('/ledger/{no_bukti}/delete', [LedgerController::class, 'delete']);
    Route::get('/ledger/{no_bukti}/edit', [LedgerController::class, 'edit']);
    Route::post('/ledger/{no_bukti}/update', [LedgerController::class, 'update']);
    Route::get('/saldoawalledger', [LedgerController::class, 'saldoawal']);
    Route::get('/saldoawalledger/create', [LedgerController::class, 'saldoawal_create']);
    Route::post('/saldoawalledger/getsaldo', [LedgerController::class, 'getsaldo']);
    Route::post('/saldoawalledger/store', [LedgerController::class, 'saldoawal_store']);
    Route::delete('/saldoawalledger/{kode_saldoawalledger}/delete', [LedgerController::class, 'saldoawal_delete']);

    Route::get('/giro', [GiroController::class, 'index']);
    Route::post('/giro/detailfaktur', [GiroController::class, 'detailfaktur']);
    Route::post('/giro/prosesgiro', [GiroController::class, 'prosesgiro']);
    Route::post('/giro/update', [GiroController::class, 'update']);

    Route::get('/transfer', [TransferController::class, 'index']);
    Route::post('/transfer/detailfaktur', [TransferController::class, 'detailfaktur']);
    Route::post('/transfer/prosestransfer', [TransferController::class, 'prosestransfer']);
    Route::post('/transfer/update', [TransferController::class, 'update']);

    //Setoran Penjualan
    Route::get('/setoranpenjualan', [SetoranpenjualanController::class, 'index']);
    Route::get('/setoranpenjualan/cetak', [SetoranpenjualanController::class, 'cetak']);
    Route::get('/setoranpenjualan/detailsetoran', [SetoranpenjualanController::class, 'detailsetoran']);
    Route::get('/setoranpenjualan/create', [SetoranpenjualanController::class, 'create']);
    Route::post('/setoranpenjualan/getsetoranpenjualan', [SetoranpenjualanController::class, 'getsetoranpenjualan']);
    Route::post('/setoranpenjualan/ceksetoran', [SetoranpenjualanController::class, 'ceksetoran']);
    Route::post('/setoranpenjualan/store', [SetoranpenjualanController::class, 'store']);
    Route::get('/setoranpenjualan/{kode_setoran}/synclhp', [SetoranpenjualanController::class, 'synclhp']);
    Route::get('/setoranpenjualan/{kode_setoran}/edit', [SetoranpenjualanController::class, 'edit']);
    Route::post('/setoranpenjualan/{kode_setoran}/update', [SetoranpenjualanController::class, 'update']);
    Route::delete('/setoranpenjualan/{kode_setoran}/delete', [SetoranpenjualanController::class, 'delete']);




    //Setoran Giro
    Route::get('/setorangiro', [SetorangiroController::class, 'index']);
    Route::post('/setorangiro/create', [SetorangiroController::class, 'create']);
    Route::post('/setorangiro/store', [SetorangiroController::class, 'store']);
    Route::get('/setorangiro/{no_giro}/delete', [SetorangiroController::class, 'delete']);

    //Setoran Transfer
    Route::get('/setorantransfer', [SetorantransferController::class, 'index']);
    Route::post('/setorantransfer/create', [SetorantransferController::class, 'create']);
    Route::post('/setorantransfer/store', [SetorantransferController::class, 'store']);
    Route::get('/setorantransfer/{kode_transfer}/delete', [SetorantransferController::class, 'delete']);

    //Belum Setor
    Route::get('/belumsetor', [BelumsetorController::class, 'index']);
    Route::get('/belumsetor/{kode_saldobs}/show', [BelumsetorController::class, 'show']);
    Route::get('/belumsetor/{kode_cabang}/{bulan}/{tahun}/showtemp', [BelumsetorController::class, 'showtemp']);
    Route::get('/belumsetor/create', [BelumsetorController::class, 'create']);
    Route::post('/belumsetor/storetemp', [BelumsetorController::class, 'storetemp']);
    Route::post('/belumsetor/deletetemp', [BelumsetorController::class, 'deletetemp']);
    Route::post('/belumsetor/cektemp', [BelumsetorController::class, 'cektemp']);
    Route::post('/belumsetor/store', [BelumsetorController::class, 'store']);
    Route::delete('/belumsetor/{kode_saldobs}/delete', [BelumsetorController::class, 'delete']);


    Route::get('/lebihsetor', [LebihsetorController::class, 'index']);
    Route::get('/lebihsetor/{kode_ls}/show', [LebihsetorController::class, 'show']);
    Route::get('/lebihsetor/{kode_cabang}/{bulan}/{tahun}/showtemp', [LebihsetorController::class, 'showtemp']);
    Route::get('/lebihsetor/create', [LebihsetorController::class, 'create']);
    Route::post('/lebihsetor/storetemp', [LebihsetorController::class, 'storetemp']);
    Route::post('/lebihsetor/deletetemp', [LebihsetorController::class, 'deletetemp']);
    Route::post('/lebihsetor/cektemp', [LebihsetorController::class, 'cektemp']);
    Route::post('/lebihsetor/store', [LebihsetorController::class, 'store']);
    Route::delete('/lebihsetor/{kode_ls}/delete', [LebihsetorController::class, 'delete']);

    Route::get('/logamtokertas', [LogamtokertasController::class, 'index']);
    Route::get('/logamtokertas/create', [LogamtokertasController::class, 'create']);
    Route::post('/logamtokertas/store', [LogamtokertasController::class, 'store']);
    Route::delete('/logamtokertas/{kode_logamtokertas}/delete', [LogamtokertasController::class, 'delete']);

    Route::get('/pasar', [Pasarcontroller::class, 'index']);
    Route::get('/pasar/create', [Pasarcontroller::class, 'create']);
    Route::post('/pasar/store', [Pasarcontroller::class, 'store']);
    Route::delete('/pasar/{id}/delete', [Pasarcontroller::class, 'delete']);

    //Setoran Pusat
    Route::get('/setoranpusat', [SetoranpusatController::class, 'index']);
    Route::get('/setoranpusat/cetak', [SetoranpusatController::class, 'cetak']);
    Route::get('/setoranpusat/create', [SetoranpusatController::class, 'create']);
    Route::post('/setoranpusat/store', [SetoranpusatController::class, 'store']);
    Route::delete('/setoranpusat/{kode_setoranpusat}/delete', [SetoranpusatController::class, 'delete']);
    Route::get('/setoranpusat/{kode_setoranpusat}/edit', [SetoranpusatController::class, 'edit']);
    Route::post('/setoranpusat/{kode_setoranpusat}/update', [SetoranpusatController::class, 'update']);
    Route::get('/setoranpusat/{kode_setoranpusat}/createterimasetoran', [SetoranpusatController::class, 'createterimasetoran']);
    Route::get('/setoranpusat/{kode_setoranpusat}/batalkansetoran', [SetoranpusatController::class, 'batalkansetoran']);
    Route::post('/setoranpusat/{kode_setoranpusat}/terimasetoran', [SetoranpusatController::class, 'terimasetoran']);

    //Saldo Awal Kas Besar
    Route::get('/saldoawalkasbesar', [SaldoawalkasbesarController::class, 'index']);
    Route::get('/saldoawalkasbesar/create', [SaldoawalkasbesarController::class, 'create']);
    Route::post('/saldoawalkasbesar/getsaldo', [SaldoawalkasbesarController::class, 'getsaldo']);
    Route::post('/saldoawalkasbesar/store', [SaldoawalkasbesarController::class, 'store']);
    Route::delete('/saldoawalkasbesar/{kode_saldoawalkb}/delete', [SaldoawalkasbesarController::class, 'delete']);

    //Penjualan
    Route::get('/penjualan', [PenjualanController::class, 'index']);
    Route::post('/penjualan/getfaktur', [PenjualanController::class, 'getfaktur']);
    Route::get('/penjualan/create', [PenjualanController::class, 'create']);
    Route::get('/inputpenjualanv2', [PenjualanController::class, 'create_v2']);
    Route::get('/inputpenjualanppn', [PenjualanController::class, 'create_v2']);
    Route::post('/penjualan/storebarangtemp', [PenjualanController::class, 'storebarangtemp']);
    Route::post('/penjualan/storebarangtempv2', [PenjualanController::class, 'storebarangtempv2']);
    Route::post('/penjualan/deletebarangtemp', [PenjualanController::class, 'deletebarangtemp']);
    Route::post('/penjualan/deletebarang', [PenjualanController::class, 'deletebarang']);
    Route::get('/penjualan/showbarangtemp', [PenjualanController::class, 'showbarangtemp']);
    Route::get('/penjualan/showbarangtempv2', [PenjualanController::class, 'showbarangtempv2']);
    Route::post('/resetpenjualantemp', [PenjualanController::class, 'resetpenjualantemp']);
    Route::post('/penjualan/updatedetailtemp', [PenjualanController::class, 'updatedetailtemp']);
    Route::get('/loadtotalpenjualantemp', [PenjualanController::class, 'loadtotalpenjualantemp']);
    Route::post('/hitungdiskon', [PenjualanController::class, 'hitungdiskon']);
    Route::get('/cekpenjtemp', [PenjualanController::class, 'cekpenjtemp']);
    Route::post('/cekpiutangpelanggan', [PenjualanController::class, 'cekpiutangpelanggan']);
    Route::post('/cekfakturkredit', [PenjualanController::class, 'cekfakturkredit']);
    Route::post('/penjualan/store', [PenjualanController::class, 'store']);
    Route::post('/penjualan/previewfaktur', [PenjualanController::class, 'previewfaktur']);
    Route::get('/penjualan/cetakfaktur/{no_fak_penj}', [PenjualanController::class, 'cetakfaktur']);
    Route::get('/penjualan/cetaksuratjalan/{no_fak_penj}/{type}', [PenjualanController::class, 'cetaksuratjalan']);
    Route::delete('/penjualan/{no_fak_penj}/delete', [PenjualanController::class, 'delete']);
    Route::get('/penjualan/{no_fak_penj}/edit', [PenjualanController::class, 'edit']);
    Route::get('/penjualan/{no_fak_penj}/editv2', [PenjualanController::class, 'editv2']);
    Route::post('/penjualan/showbarang', [PenjualanController::class, 'showbarang']);
    Route::post('/penjualan/showbarangv2', [PenjualanController::class, 'showbarangv2']);
    Route::post('/cekpenj', [PenjualanController::class, 'cekpenj']);
    Route::post('/loadtotalpenjualan', [PenjualanController::class, 'loadtotalpenjualan']);
    Route::post('/hitungdiskonpenjualan', [PenjualanController::class, 'hitungdiskonpenjualan']);
    Route::post('/hitungdiskonpenjualanv2', [PenjualanController::class, 'hitungdiskonpenjualanv2']);
    Route::post('/penjualan/updatedetail', [PenjualanController::class, 'updatedetail']);
    Route::post('/penjualan/storebarang', [PenjualanController::class, 'storebarang']);
    Route::post('/penjualan/update', [PenjualanController::class, 'update']);
    Route::get('/penjualan/{no_fak_penj}/show', [PenjualanController::class, 'show']);
    Route::get('/penjualan/{no_fak_penj}/showforsales', [PenjualanController::class, 'showforsales']);
    Route::get('/penjualan/{no_fak_penj}/updatepending', [PenjualanController::class, 'updatepending']);
    Route::get('/laporanpenjualan/analisatransaksi', [PenjualanController::class, 'analisatransaksi']);
    Route::get('/laporanpenjualan/persentasesfa', [PenjualanController::class, 'persentasesfa']);
    Route::get('/laporanpenjualan/persentaselokasi', [PenjualanController::class, 'persentaselokasi']);
    Route::get('/laporanpenjualan/tunaitransfer', [PenjualanController::class, 'tunaitransfer']);
    Route::post('/laporanpenjualan/analisatransaksi/cetak', [PenjualanController::class, 'cetakanalisatransaksi']);
    Route::post('/laporanpenjualan/persentasesfa/cetak', [PenjualanController::class, 'cetakpersentasesfa']);
    Route::post('/laporanpenjualan/persentaselokasi/cetak', [PenjualanController::class, 'cetakpersentaselokasi']);
    Route::post('/laporanpenjualan/tunaitransfer/cetak', [PenjualanController::class, 'cetaktunaitransfer']);
    Route::post('/penjualan/ceknofaktur', [PenjualanController::class, 'ceknofaktur']);
    Route::post('/penjualan/editbarangtemp', [PenjualanController::class, 'editbarangtemp']);
    Route::post('/penjualan/inputbarangtemp', [PenjualanController::class, 'inputbarangtemp']);
    Route::post('/penjualan/inputbarang', [PenjualanController::class, 'inputbarang']);

    Route::post('/penjualan/editbarang', [PenjualanController::class, 'editbarang']);
    Route::post('/penjualan/updatebarang', [PenjualanController::class, 'updatebarang']);
    Route::post('/penjualan/updatebarangtemp', [PenjualanController::class, 'updatebarangtemp']);

    Route::post('/penjualan/setfakturbatal', [PenjualanController::class, 'setfakturbatal']);
    Route::post('/penjualan/updateprint', [PenjualanController::class, 'updateprint']);
    Route::post('/penjualan/ubahfakturpo', [PenjualanController::class, 'ubahfakturpo']);
    Route::post('/penjualan/{no_fak_penj}/updatepo', [PenjualanController::class, 'updatepo']);
    //Pembayaran
    Route::post('/pembayaran/store', [PembayaranController::class, 'store']);
    Route::post('/pembayaran/edit', [PembayaranController::class, 'edit']);
    Route::post('/pembayaran/{nobukti}/update', [PembayaranController::class, 'update']);
    Route::delete('/pembayaran/{nobukti}/delete', [PembayaranController::class, 'delete']);

    //Giro
    Route::post('/pembayaran/storegiro', [PembayaranController::class, 'storegiro']);
    Route::delete('/pembayaran/{id_giro}/deletegiro', [PembayaranController::class, 'deletegiro']);
    Route::delete('/pembayaran/{no_giro}/deleteallnogiro', [PembayaranController::class, 'deleteallnogiro']);
    Route::post('/pembayaran/editgiro', [PembayaranController::class, 'editgiro']);
    Route::post('/pembayaran/{id_giro}/updategiro', [PembayaranController::class, 'updategiro']);


    //Transfer
    Route::post('/pembayaran/storetransfer', [PembayaranController::class, 'storetransfer']);
    Route::delete('/pembayaran/{id_transfer}/deletetransfer', [PembayaranController::class, 'deletetransfer']);
    Route::post('/pembayaran/edittransfer', [PembayaranController::class, 'edittransfer']);
    Route::post('/pembayaran/{id_transfer}/updatetransfer', [PembayaranController::class, 'updatetransfer']);


    //Retur
    Route::get('/retur', [ReturController::class, 'index']);
    Route::get('/retur/create', [ReturController::class, 'create']);
    Route::get('/retur/createv2', [ReturController::class, 'createv2']);
    Route::post('/retur/store', [ReturController::class, 'store']);
    Route::post('/retur/showbarangtemp', [ReturController::class, 'showbarangtemp']);
    Route::post('/retur/showbarangtempv2', [ReturController::class, 'showbarangtempv2']);
    Route::post('/retur/storebarangtemp', [ReturController::class, 'storebarangtemp']);
    Route::post('/retur/storebarangtempv2', [ReturController::class, 'storebarangtempv2']);
    Route::post('/cekreturtemp', [ReturController::class, 'cekreturtemp']);
    Route::post('/retur/updatedetailtemp', [ReturController::class, 'updatedetailtemp']);
    Route::post('/retur/show', [ReturController::class, 'show']);

    Route::delete('/retur/{no_retur_penj}/delete', [ReturController::class, 'delete']);
    Route::post('/retur/deletebarangtemp', [ReturController::class, 'deletebarangtemp']);
    Route::post('/loadtotalreturtemp', [ReturController::class, 'loadtotalreturtemp']);
    Route::post('/retur/getfakturpelanggan', [ReturController::class, 'getfakturpelanggan']);
    Route::post('/retur/inputbarangtemp', [ReturController::class, 'inputbarangtemp']);
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

    //Coa
    Route::get('/coa', [CoaController::class, 'index']);
    Route::get('/coa/create', [CoaController::class, 'create']);
    Route::post('/coa/store', [CoaController::class, 'store']);
    Route::get('/coa/{kode_akun}/edit', [CoaController::class, 'edit']);
    Route::post('/coa/{kode_akun}/update', [CoaController::class, 'update']);
    Route::delete('/coa/{kode_akun}/delete', [CoaController::class, 'delete']);


    //Set Coa Cabang

    Route::get('/setcoacabang', [SetcoacabangController::class, 'index']);
    Route::get('/setcoacabang/create', [SetcoacabangController::class, 'create']);
    Route::post('/setcoacabang/store', [SetcoacabangController::class, 'store']);
    Route::delete('/setcoacabang/{id}/delete', [SetcoacabangController::class, 'delete']);
    //Maintenance
    Route::get('/maintenance/pembelian', [MaintenanceController::class, 'pembelian']);
    Route::post('/maintenance/showpembelian', [MaintenanceController::class, 'showpembelian']);
    Route::post('/maintenance/{nobukti_pembelian}/storepembelian', [MaintenanceController::class, 'storepembelian']);

    //Pemasukan Maintenance
    Route::get('/pemasukanmaintenance', [PemasukanmaintenanceController::class, 'index']);
    Route::get('/pemasukanmaintenance/create', [PemasukanmaintenanceController::class, 'create']);
    Route::get('/pemasukanmaintenance/getbarang', [PemasukanmaintenanceController::class, 'getbarang']);
    Route::get('/pemasukanmaintenance/showtemp', [PemasukanmaintenanceController::class, 'showtemp']);
    Route::post('/pemasukanmaintenance/store', [PemasukanmaintenanceController::class, 'store']);
    Route::post('/pemasukanmaintenance/storetemp', [PemasukanmaintenanceController::class, 'storetemp']);
    Route::post('/pemasukanmaintenance/deletetemp', [PemasukanmaintenanceController::class, 'deletetemp']);
    Route::post('/pemasukanmaintenance/show', [PemasukanmaintenanceController::class, 'show']);
    Route::post('/pemasukanmaintenance/cektemp', [PemasukanmaintenanceController::class, 'cektemp']);
    Route::delete('/pemasukanmaintenance/{nobukti_pemasukan}/delete', [PemasukanmaintenanceController::class, 'delete']);

    Route::get('/pengeluaranmaintenance', [PengeluaranmaintenanceController::class, 'index']);
    Route::get('/pengeluaranmaintenance/create', [PengeluaranmaintenanceController::class, 'create']);
    Route::post('/pengeluaranmaintenance/cektemp', [PengeluaranmaintenanceController::class, 'cektemp']);
    Route::get('/pengeluaranmaintenance/showtemp', [PengeluaranmaintenanceController::class, 'showtemp']);
    Route::post('/pengeluaranmaintenance/storetemp', [PengeluaranmaintenanceController::class, 'storetemp']);
    Route::post('/pengeluaranmaintenance/deletetemp', [PengeluaranmaintenanceController::class, 'deletetemp']);
    Route::post('/pengeluaranmaintenance/store', [PengeluaranmaintenanceController::class, 'store']);
    Route::post('/pengeluaranmaintenance/show', [PengeluaranmaintenanceController::class, 'show']);
    Route::delete('/pengeluaranmaintenance/{nobukti_pengeluaran}/delete', [PengeluaranmaintenanceController::class, 'delete']);

    //Laporan Maintenance
    Route::get('/laporanmaintenance/rekapbahanbakar', [LaporanmaintenanceController::class, 'rekapbahanbakar']);
    Route::post('/laporanmaintenance/rekapbahanbakar/cetak', [LaporanmaintenanceController::class, 'cetak_rekapbahanbakar']);

    Route::get('/memo', [MemoController::class, 'index']);
    Route::get('/memo/create', [MemoController::class, 'create']);
    Route::post('/memo/store', [MemoController::class, 'store']);
    Route::post('/memo/downloadcount', [MemoController::class, 'downloadcount']);
    Route::delete('/memo/{id}/delete', [MemoController::class, 'delete']);
    Route::get('/memo/{kode_dept}/show', [MemoController::class, 'show']);
    Route::post('/memo/uploadsosialisasi/create', [MemoController::class, 'uploadsosialisasi']);
    Route::post('/memo/uploadsosialisasi/edit', [MemoController::class, 'edituploadsosialisasi']);
    Route::post('/memo/uploadsosialisasi/store', [MemoController::class, 'storeuploadsosialisasi']);
    Route::post('/memo/uploadsosialisasi/update', [MemoController::class, 'updateuploadsosialisasi']);
    Route::post('/memo/uploadsosialisasi/detail', [MemoController::class, 'detailuploadsosialisasi']);

    Route::get('/tutuplaporan', [TutuplaporanController::class, 'index']);
    Route::get('/tutuplaporan/create', [TutuplaporanController::class, 'create']);
    Route::post('/tutuplaporan/store', [TutuplaporanController::class, 'store']);
    Route::get('/tutuplaporan/{kode_tutuplaporan}/bukalaporan', [TutuplaporanController::class, 'bukalaporan']);
    Route::get('/tutuplaporan/{kode_tutuplaporan}/tutuplaporan', [TutuplaporanController::class, 'tutuplaporan']);

    Route::get('/ticket', [TicketController::class, 'index']);
    Route::get('/ticket/create', [TicketController::class, 'create']);
    Route::post('/ticket/store', [TicketController::class, 'store']);
    Route::delete('/ticket/{kode_pengajuan}/delete', [TicketController::class, 'delete']);
    Route::get('/ticket/{kode_pengajuan}/approve', [TicketController::class, 'approve']);
    Route::get('/ticket/{kode_pengajuan}/batalapprove', [TicketController::class, 'batalapprove']);
    Route::get('/ticket/{kode_pengajuan}/done', [TicketController::class, 'done']);
    Route::get('/ticket/{kode_pengajuan}/bataldone', [TicketController::class, 'bataldone']);

    //Costratio
    Route::get('/updatekaskecilcr', [KaskecilController::class, 'updatecostratio']);
    Route::get('/updatepmbcr', [PembelianController::class, 'updatecostratio']);
    Route::get('/updateledgercr', [LedgerController::class, 'updatecostratio']);
    Route::get('/updatebackupcr', [CostratioController::class, 'updatecostratio']);

    Route::get('/costratio', [CostratioController::class, 'index']);
    Route::get('/costratio/create', [CostratioController::class, 'create']);
    Route::post('/costratio/store', [CostratioController::class, 'store']);
    Route::delete('/costratio/{kode_cr}/delete', [CostratioController::class, 'delete']);
    Route::get('/costratio/cetak', [CostratioController::class, 'cetak']);


    Route::get('/scan', [QrcodeController::class, 'index']);
    Route::get('/tracking', [TrackingController::class, 'index']);
    Route::get('/mappelanggan', [TrackingController::class, 'mappelanggan']);
    Route::get('/getlocationcheckin', [TrackingController::class, 'getlocationcheckin']);
    Route::get('/getlocationcheckinsalesman', [TrackingController::class, 'getlocationcheckinsalesman']);
    Route::get('/getmappelanggan', [TrackingController::class, 'getmappelanggan']);


    //Route::get('/cetakstruk', [PenjualanController::class, 'cetakstruk']);
    Route::post('/cetakstruk', [PenjualanController::class, 'cetakstruk']);
    Route::get('/cetaknota', [PenjualanController::class, 'cetakstruk']);
    Route::post('/penjualan/uploadsignature', [PenjualanController::class, 'uploadsignature']);
    Route::delete('/penjualan/{no_fak_penj}/deletesignature', [PenjualanController::class, 'deletesignature']);

    Route::post('/getkunjungan', [DashboardController::class, 'getkunjungan']);
    Route::post('/getdpb', [DashboardController::class, 'getdpb']);

    //Karyawan
    Route::get('/karyawan', [KaryawanController::class, 'index']);
    Route::get('/karyawan/create', [KaryawanController::class, 'create']);
    Route::post('/karyawan/store', [KaryawanController::class, 'store']);
    Route::get('/karyawan/{nik}/edit', [KaryawanController::class, 'edit']);
    Route::post('/karyawan/{nik}/update', [KaryawanController::class, 'update']);
    Route::get('/karyawan/{nik}/show', [KaryawanController::class, 'show']);
    Route::post('/karyawan/getkaryawankontrak', [KaryawanController::class, 'getkaryawankontrak']);
    Route::get('/karyawan/habiskontrak', [KaryawanController::class, 'habiskontrak']);

    //Rekening Karyawan
    Route::get('/rekeningkaryawan', [RekeningkaryawanController::class, 'index']);
    Route::get('/rekeningkaryawan/{nik}/edit', [RekeningkaryawanController::class, 'edit']);
    Route::post('/rekeningkaryawan/{nik}/update', [RekeningkaryawanController::class, 'update']);
    //SAP
    Route::get('/homesap', [DashboardController::class, 'homesap']);
    Route::get('/salesperformance', [SapController::class, 'salesperfomance']);
    Route::post('/getsalesperfomance', [SapController::class, 'getsalesperfomance']);
    Route::get('/sap/salesperfomance/detail', [SapController::class, 'salesperfomancedetail']);
    Route::post('/sap/getpenjualansalesman', [SapController::class, 'getpenjualansalesman']);
    Route::post('/sap/getcashinsalesman', [SapController::class, 'getcashinsalesman']);
    Route::post('/sap/getkunjungansalesman', [SapController::class, 'getkunjungansalesman']);
    Route::post('/sap/gettargetsalesman', [SapController::class, 'gettargetsalesman']);
    Route::post('/sap/getrealisasitargetsales', [SapController::class, 'getrealisasitargetsales']);
    Route::get('/sap/limitkredit', [LimitkreditController::class, 'index']);
    Route::get('/sap/limitkredit/{no_pengajuan}/show', [SapController::class, 'showlimitkredit']);
    Route::get('/sap/pelanggan', [SapController::class, 'pelanggan']);
    Route::get('/sap/smactivity', [SapController::class, 'smactivity']);
    Route::get('/sap/smactivity/create', [SapController::class, 'createsmactivity']);
    Route::post('/sap/smactivity/store', [SapController::class, 'storesmactivity']);
    Route::post('/getsmactivity', [SapController::class, 'getsmactivity']);
    Route::get('/smactivity/{kode_act_sm}/show', [DashboardController::class, 'showsmactivity']);
    Route::get('/sap/rekapactivity', [SapController::class, 'rekapactivity']);
    //Kesepakatan Bersama
    Route::get('/kesepakatanbersama', [KesepakatanbersamaController::class, 'index']);
    Route::post('/kesepakatanbersama/store', [KesepakatanbersamaController::class, 'store']);
    Route::post('/kesepakatanbersama/edit', [KesepakatanbersamaController::class, 'edit']);
    Route::post('/kesepakatanbersama/potongan', [KesepakatanbersamaController::class, 'potongan']);
    Route::post('/kesepakatanbersama/storepotongan', [KesepakatanbersamaController::class, 'storepotongan']);
    Route::post('/kesepakatanbersama/getpotongan', [KesepakatanbersamaController::class, 'getpotongan']);
    Route::post('/kesepakatanbersama/deletepotongan', [KesepakatanbersamaController::class, 'deletepotongan']);
    Route::post('/kesepakatanbersama/{no_kb}/update', [KesepakatanbersamaController::class, 'update']);
    Route::get('/kesepakatanbersama/{no_kb}/cetak', [KesepakatanbersamaController::class, 'cetak']);
    Route::delete('/kesepakatanbersama/{no_kb}/delete', [KesepakatanbersamaController::class, 'delete']);

    //Kontrak
    Route::get('/kontrak', [KontrakController::class, 'index']);
    Route::post('/kontrak/create', [KontrakController::class, 'create']);
    Route::post('/kontrak/edit', [KontrakController::class, 'edit']);
    Route::post('/kontrak/editlastkontrak', [KontrakController::class, 'editlastkontrak']);
    Route::post('/kontrak/store', [KontrakController::class, 'store']);
    Route::post('/kontrak/{no_kontrak}/update', [KontrakController::class, 'update']);
    Route::post('/kontrak/{no_kontrak}/updatelastkontrak', [KontrakController::class, 'updatelastkontrak']);
    Route::post('/kontrak/{no_kontrak}/storehistorikontrak', [KontrakController::class, 'storehistorikontrak']);
    Route::post('/kontrak/createfrompenilaian', [KontrakController::class, 'createformpenilaian']);
    Route::post('/kontrak/storefrompenilaian', [KontrakController::class, 'storefrompenilaian']);
    Route::get('/kontrak/{no_kontrak}/cetak', [KontrakController::class, 'cetak']);
    Route::delete('/kontrak/{no_kontrak}/delete', [KontrakController::class, 'delete']);
    Route::delete('/kontrak/{no_kontrak}/deletehistorikontrak', [KontrakController::class, 'deletehistorikontrak']);

    Route::post('/kontrak/createfromkb', [KontrakController::class, 'createfromkb']);
    Route::post('/kontrak/getkontrakpemutihan', [KontrakController::class, 'getkontrakpemutihan']);
    Route::post('/kontrak/getkontrakpenilaian', [KontrakController::class, 'getkontrakpenilaian']);
    //Pinjaman

    Route::get('/pinjaman/', [PinjamanController::class, 'index']);
    Route::get('/pinjaman/{nik}/create', [PinjamanController::class, 'create']);
    Route::post('pinjaman/store', [PinjamanController::class, 'store']);
    Route::post('pinjaman/edit', [PinjamanController::class, 'edit']);
    Route::post('pinjaman/{no_pinjaman}/update', [PinjamanController::class, 'update']);
    Route::delete('pinjaman/{no_pinjaman}/delete', [PinjamanController::class, 'delete']);
    Route::get('pinjaman/{no_pinjaman}/approve', [PinjamanController::class, 'approve']);
    Route::get('pinjaman/{no_pinjaman}/decline', [PinjamanController::class, 'decline']);
    Route::post('/pinjaman/show', [PinjamanController::class, 'show']);
    Route::post('/pinjaman/getrencanabayar', [PinjamanController::class, 'getrencanabayar']);
    Route::post('/pinjaman/gethistoribayar', [PinjamanController::class, 'gethistoribayar']);
    Route::get('/pinjaman/{no_pinjaman}/cetakformulir', [PinjamanController::class, 'cetakformulir']);
    Route::post('pinjaman/prosespinjaman', [PinjamanController::class, 'prosespinjaman']);
    Route::post('pinjaman/prosespinjaman', [PinjamanController::class, 'prosespinjaman']);
    Route::post('pinjaman/{no_pinjaman}/storeprosespinjaman', [PinjamanController::class, 'storeprosespinjaman']);
    //Pembayaran Pinjaman

    Route::get('/pembayaranpinjaman', [PembayaranpinjamanController::class, 'index']);
    Route::post('pembayaranpinjaman/create', [PembayaranpinjamanController::class, 'create']);
    Route::post('pembayaranpinjaman/store', [PembayaranpinjamanController::class, 'store']);
    Route::post('pembayaranpinjaman/delete', [PembayaranpinjamanController::class, 'delete']);
    Route::post('pembayaranpinjaman/generatebayarpinjaman', [PembayaranpinjamanController::class, 'generatebayarpinjaman']);
    Route::delete('pembayaranpinjaman/{kode_potongan}/deletegenerate', [PembayaranpinjamanController::class, 'deletegenerate']);
    Route::post('/pembayaranpinjaman/show', [PembayaranpinjamanController::class, 'show']);
    Route::get('/pembayaranpinjaman/{kode_potongan}/{export}/cetak', [PembayaranpinjamanController::class, 'cetak']);

    //PinjamanNonPJP
    Route::get('/pinjamannonpjp/{nik}/create', [PinjamannonpjpController::class, 'create']);
    Route::post('/pinjamannonpjp/store', [PinjamannonpjpController::class, 'store']);

    //Piutangkaryawan
    Route::get('/piutangkaryawan', [PiutangkaryawanController::class, 'index']);
    Route::delete('/piutangkaryawan/{no_pinjaman_nonpjp}/delete', [PiutangkaryawanController::class, 'delete']);
    Route::post('/piutangkaryawan/show', [PiutangkaryawanController::class, 'show']);
    Route::post('/piutangkaryawan/gethistoribayar', [PiutangkaryawanController::class, 'gethistoribayar']);

    Route::post('/pembayaranpiutangkaryawan/create', [PembayaranpinjamannonpjpController::class, 'create']);
    Route::post('/pembayaranpiutangkaryawan/store', [PembayaranpinjamannonpjpController::class, 'store']);
    Route::post('/pembayaranpiutangkaryawan/delete', [PembayaranpinjamannonpjpController::class, 'delete']);

    //Kasbon
    Route::get('/kasbon/', [KasbonController::class, 'index']);
    Route::get('/kasbon/{nik}/create', [KasbonController::class, 'create']);
    Route::post('kasbon/store', [KasbonController::class, 'store']);
    Route::delete('kasbon/{no_kasbon}/delete', [KasbonController::class, 'delete']);
    Route::post('kasbon/edit', [KasbonController::class, 'edit']);

    Route::post('kasbon/proseskasbon', [KasbonController::class, 'proseskasbon']);
    Route::post('kasbon/{no_kasbon}/storeproseskasbon', [KasbonController::class, 'storeproseskasbon']);
    Route::get('/kasbon/{no_kasbon}/cetakformulir', [KasbonController::class, 'cetakformulir']);
    //Pembayaran Kasbon
    Route::get('/pembayarankasbon', [PembayarankasbonController::class, 'index']);
    Route::post('pembayarankasbon/generatebayarpinjaman', [PembayarankasbonController::class, 'generatebayarpinjaman']);
    Route::post('/pembayarankasbon/show', [PembayarankasbonController::class, 'show']);
    Route::delete('pembayarankasbon/{kode_potongan}/deletegenerate', [PembayarankasbonController::class, 'deletegenerate']);
    Route::get('/pembayarankasbon/{kode_potongan}/{export}/cetak', [PembayarankasbonController::class, 'cetak']);

    Route::get('/gaji', [GajiController::class, 'index']);
    Route::get('/gaji/create', [GajiController::class, 'create']);
    Route::post('/gaji/store', [GajiController::class, 'store']);
    Route::get('/gaji/{kode_gaji}/edit', [GajiController::class, 'edit']);
    Route::post('/gaji/{kode_gaji}/update', [GajiController::class, 'update']);
    Route::delete('/gaji/{kode_gaji}/delete', [GajiController::class, 'delete']);

    Route::get('/insentif', [InsentifController::class, 'index']);
    Route::get('/insentif/create', [InsentifController::class, 'create']);
    Route::post('/insentif/store', [InsentifController::class, 'store']);
    Route::get('/insentif/{kode_insentif}/edit', [InsentifController::class, 'edit']);
    Route::post('/insentif/{kode_insentif}/update', [InsentifController::class, 'update']);
    Route::delete('/insentif/{kode_insentif}/delete', [InsentifController::class, 'delete']);


    //BPJS TK

    Route::get('/bpjstk', [BpjstkController::class, 'index']);
    Route::get('/bpjstk/create', [BpjstkController::class, 'create']);
    Route::post('/bpjstk/store', [BpjstkController::class, 'store']);
    Route::post('/bpjstk/{kode_bpjs_tk}/update', [BpjstkController::class, 'update']);
    Route::delete('/bpjstk/{kode_bpjs_tk}/delete', [BpjstkController::class, 'delete']);
    Route::get('/bpjstk/{kode_bpjs_tk}/edit', [BpjstkController::class, 'edit']);


    //BPJS Kesehatan

    Route::get('/bpjskesehatan', [BpjskesehatanController::class, 'index']);
    Route::get('/bpjskesehatan/create', [BpjskesehatanController::class, 'create']);
    Route::post('/bpjskesehatan/store', [BpjskesehatanController::class, 'store']);
    Route::post('/bpjskesehatan/{kode_bpjs_kes}/update', [BpjskesehatanController::class, 'update']);
    Route::delete('/bpjskesehatan/{kode_bpjs_kes}/delete', [BpjskesehatanController::class, 'delete']);
    Route::get('/bpjskesehatan/{kode_bpjs_kes}/edit', [BpjskesehatanController::class, 'edit']);

    //Pengajuan Izin
    Route::get('/pengajuanizin', [PengajuanizinController::class, 'index']);
    Route::get('/pengajuanizin/izinpulang', [PengajuanizinController::class, 'index']);
    Route::get('/pengajuanizin/izinkeluar', [PengajuanizinController::class, 'index']);
    Route::get('/pengajuanizin/sakit', [PengajuanizinController::class, 'index']);
    Route::get('/pengajuanizin/cuti', [PengajuanizinController::class, 'index']);
    Route::get('/pengajuanizin/izinterlambat', [PengajuanizinController::class, 'index']);
    Route::get('/pengajuanizin/koreksipresensi', [PengajuanizinController::class, 'index']);
    Route::get('/pengajuanizin/perjalanandinas', [PengajuanizinController::class, 'index']);
    Route::post('/pengajuanizin/approve', [PengajuanizinController::class, 'approve']);
    Route::post('/pengajuanizin/approvekoreksipresensi', [PengajuanizinController::class, 'approvekoreksipresensi']);
    Route::post('/pengajuanizin/approveperjalanandinas', [PengajuanizinController::class, 'approveperjalanandinas']);
    Route::post('/pengajuanizin/approveizinpulang', [PengajuanizinController::class, 'approveizinpulang']);
    Route::post('/pengajuanizin/approveizinkeluar', [PengajuanizinController::class, 'approveizinkeluar']);
    Route::post('/pengajuanizin/approveizinterlambat', [PengajuanizinController::class, 'approveizinterlambat']);
    Route::get('/pengajuanizin/create', [PengajuanizinController::class, 'create']);
    Route::get('/pengajuanizin/createizinterlambat', [PengajuanizinController::class, 'createizinterlambat']);
    Route::get('/pengajuanizin/createizinabsen', [PengajuanizinController::class, 'createizinabsen']);
    Route::get('/pengajuanizin/createizinkeluar', [PengajuanizinController::class, 'createizinkeluar']);
    Route::get('/pengajuanizin/createizinpulang', [PengajuanizinController::class, 'createizinpulang']);
    Route::get('/pengajuanizin/createizinsakit', [PengajuanizinController::class, 'createizinsakit']);
    Route::get('/pengajuanizin/createizincuti', [PengajuanizinController::class, 'createizincuti']);
    Route::get('/pengajuanizin/createkoreksi', [PengajuanizinController::class, 'createkoreksi']);
    Route::get('/pengajuanizin/createperjalanandinas', [PengajuanizinController::class, 'createperjalanandinas']);
    Route::post('/pengajuanizin/store', [PengajuanizinController::class, 'store']);
    Route::post('/pengajuanizin/storekoreksipresensi', [PengajuanizinController::class, 'storekoreksipresensi']);
    Route::delete('/pengajuanizin/{kode_izin}/delete', [PengajuanizinController::class, 'delete']);
    Route::post('/pengajuanizin/updatejammasukkk', [PengajuanizinController::class, 'updatejammasukkk']);
    Route::post('/pengajuanizin/getpresensihariini', [PengajuanizinController::class, 'getpresensihariini']);
    Route::get('/koreksipresensi/{kode_izin}/batalkan', [PengajuanizinController::class, 'batalkankoreksipresensi']);
    Route::get('/izinterlambat/{kode_izin}/batalkan', [PengajuanizinController::class, 'batalkanizinterlambat']);
    Route::get('/izinkeluar/{kode_izin}/batalkan', [PengajuanizinController::class, 'batalkanizinkeluar']);
    Route::get('/izinpulang/{kode_izin}/batalkan', [PengajuanizinController::class, 'batalkanizinpulang']);
    Route::get('/izinabsen/{kode_izin}/batalkan', [PengajuanizinController::class, 'batalkan']);
    Route::get('/sakit/{kode_izin}/batalkan', [PengajuanizinController::class, 'batalkan']);
    Route::get('/perjalanandinas/{kode_izin}/batalkan', [PengajuanizinController::class, 'batalkanperjalanandinas']);
    Route::get('/pengajuanizin/{kode_izin}/create_kethrd', [PengajuanizinController::class, 'create_kethrd']);
    Route::post('/pengajuanizin/{kode_izin}/update_kethrd', [PengajuanizinController::class, 'update_kethrd']);
    Route::get('/pengajuanizin/{kode_izin}/cetak', [PengajuanizinController::class, 'cetak']);

    //Jadwal Kerja
    Route::get('/jadwalkerja', [JadwalkerjaController::class, 'index']);
    Route::post('/jadwalkerja/pindahjadwal', [JadwalkerjaController::class, 'pindahjadwal']);
    Route::post('/jadwalkerja/updatejadwalkerja', [JadwalkerjaController::class, 'updatejadwalkerja']);

    //JMK
    Route::get('/pembayaranjmk', [PembayaranjmkController::class, 'index']);
    Route::get('/pembayaranjmk/create', [PembayaranjmkController::class, 'create']);
    Route::post('/pembayaranjmk/store', [PembayaranjmkController::class, 'store']);
    Route::delete('/pembayaranjmk/{no_bukti}/delete', [PembayaranjmkController::class, 'delete']);
    Route::post('/pembayaranjmk/edit', [PembayaranjmkController::class, 'edit']);
    Route::post('/pembayaranjmk/{no_bukti}/update', [PembayaranjmkController::class, 'update']);


    //Pelanggaran
    Route::get('/pelanggaran', [PelanggaranController::class, 'index']);
    Route::get('/pelanggaran/create', [PelanggaranController::class, 'create']);
    Route::post('/pelanggaran/store', [PelanggaranController::class, 'store']);
    Route::delete('/pelanggaran/{no_bukti}/delete', [PelanggaranController::class, 'delete']);
    Route::post('/pelanggaran/edit', [PelanggaranController::class, 'edit']);
    Route::post('/pelanggaran/{no_sp}/update', [PelanggaranController::class, 'update']);

    //Presensi

    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring']);
    Route::post('/presensi/updatepresensi', [PresensiController::class, 'updatepresensi']);
    Route::post('/presensi/storeupdatepresensi', [PresensiController::class, 'storeupdatepresensi']);
    Route::post('/presensi/getjamkerja', [PresensiController::class, 'getjamkerja']);
    Route::post('/presensi/show', [PresensiController::class, 'show']);
    Route::post('/presensi/checkmesin', [PresensiController::class, 'checkmesin']);
    Route::get('/presensi/presensikaryawan', [PresensiController::class, 'presensikaryawan']);
    Route::get('/presensi/{pin}/{status_scan}/{scan_date}/updatefrommachine', [PresensiController::class, 'updatefrommachine']);

    //Jadwal Shift
    Route::get('/konfigurasijadwal', [KonfigurasijadwalController::class, 'index']);
    Route::get('/konfigurasijadwal/{kode_setjadwal}/edit', [KonfigurasijadwalController::class, 'edit']);
    Route::get('/konfigurasijadwal/{kode_setjadwal}/aturjadwal', [KonfigurasijadwalController::class, 'aturjadwal']);
    Route::get('/konfigurasijadwal/{id_group}/showgroup', [KonfigurasijadwalController::class, 'showgroup']);
    Route::post('/konfigurasijadwal/{kode_setjadwal}/update', [KonfigurasijadwalController::class, 'update']);
    Route::delete('/konfigurasijadwal/{kode_setjadwal}/delete', [KonfigurasijadwalController::class, 'delete']);
    Route::post('/konfigurasijadwal/store', [KonfigurasijadwalController::class, 'store']);
    Route::post('/konfigurasijadwal/aturshift', [KonfigurasijadwalController::class, 'aturshift']);
    Route::post('/konfigurasijadwal/storekaryawanshift', [KonfigurasijadwalController::class, 'storekaryawanshift']);
    Route::post('/konfigurasijadwal/hapuskaryawanshift', [KonfigurasijadwalController::class, 'hapuskaryawanshift']);
    Route::post('/konfigurasijadwal/storeallkaryawanshift', [KonfigurasijadwalController::class, 'storeallkaryawanshift']);
    Route::post('/konfigurasijadwal/cancelallkaryawanshift', [KonfigurasijadwalController::class, 'cancelallkaryawanshift']);
    Route::post('/konfigurasijadwal/showjadwal', [KonfigurasijadwalController::class, 'showjadwal']);
    Route::post('/konfigurasijadwal/updatekaryawanshift', [KonfigurasijadwalController::class, 'updatekaryawanshift']);
    Route::post('/konfigurasijadwal/gantishift', [KonfigurasijadwalController::class, 'gantishift']);
    Route::post('/konfigurasijadwal/storegantishift', [KonfigurasijadwalController::class, 'storegantishift']);
    Route::get('/konfigurasijadwal/{kode_setjadwal}/showgantishift', [KonfigurasijadwalController::class, 'showgantishift']);
    Route::post('/konfigurasijadwal/deletegantishift', [KonfigurasijadwalController::class, 'deletegantishift']);
    Route::get('/konfigurasijadwal/{nik}/setjadwal', [KonfigurasijadwalController::class, 'setjadwal']);

    //Harilibur

    Route::get('/harilibur', [HariliburController::class, 'index']);
    Route::post('/harilibur/store', [HariliburController::class, 'store']);
    Route::delete('/harilibur/{kode_libur}/delete', [HariliburController::class, 'delete']);
    Route::get('/harilibur/{kode_libur}/edit', [HariliburController::class, 'edit']);
    Route::post('/harilibur/{kode_libur}/update', [HariliburController::class, 'update']);
    Route::get('/harilibur/{kode_libur}/tambahkaryawan', [HariliburController::class, 'tambahkaryawan']);
    Route::get('/harilibur/{kode_libur}/{id_kantor}/{kode_dept}/getkaryawan', [HariliburController::class, 'getkaryawan']);
    Route::post('/harilibur/getlistkaryawan', [HariliburController::class, 'getlistkaryawan']);
    Route::post('/harilibur/storekaryawanlibur', [HariliburController::class, 'storekaryawanlibur']);
    Route::post('/harilibur/hapuskaryawanlibur', [HariliburController::class, 'hapuskaryawanlibur']);
    Route::post('/harilibur/hapusliburkaryawan', [HariliburController::class, 'hapusliburkaryawan']);
    Route::get('/harilibur/{kode_libur}/getliburkaryawan', [HariliburController::class, 'getliburkaryawan']);
    Route::post('/harilibur/approve', [HariliburController::class, 'approve']);
    Route::get('/harilibur/{kode_libur}/batalkan', [HariliburController::class, 'batalkan']);
    Route::post('/harilibur/storeallkaryawan', [HariliburController::class, 'storeallkaryawan']);
    Route::post('/harilibur/cancelkaryawan', [HariliburController::class, 'cancelkaryawan']);

    Route::get('/laporanhrd/presensi', [LaporanhrdController::class, 'presensi']);
    Route::get('/laporanhrd/presensipsm', [LaporanhrdController::class, 'presensipsm']);
    Route::post('/laporanhrd/getkantor', [LaporanhrdController::class, 'getkantor']);
    Route::post('/laporanhrd/getdepartemen', [LaporanhrdController::class, 'getdepartemen']);
    Route::post('/laporanhrd/getgroup', [LaporanhrdController::class, 'getgroup']);
    Route::post('/laporanhrd/presensi/cetak', [LaporanhrdController::class, 'cetakpresensi']);
    Route::post('/laporanhrd/presensipsm/cetak', [LaporanhrdController::class, 'cetakpresensi']);
    Route::get('/laporanhrd/rekapterlambat', [LaporanhrdController::class, 'rekapterlambat']);
    Route::post('/laporanhrd/rekapterlambat/cetak', [LaporanhrdController::class, 'cetakrekapterlambat']);
    Route::post('/laporanhrd/getterlambat', [LaporanhrdController::class, 'getterlambat']);
    Route::post('/updatecheckdenda', [LaporanhrdController::class, 'updatecheckdenda']);
    Route::post('/cancelcheckdenda', [LaporanhrdController::class, 'cancelcheckdenda']);
    Route::get('/laporanhrd/gaji', [LaporanhrdController::class, 'gaji']);
    Route::post('/laporanhrd/gaji/cetak', [LaporanhrdController::class, 'cetakpresensi']);

    //Lembur

    Route::get('/lembur', [LemburController::class, 'index']);
    Route::post('/lembur/store', [LemburController::class, 'store']);
    Route::delete('/lembur/{kode_lembur}/delete', [LemburController::class, 'delete']);
    Route::get('/lembur/{kode_lembur}/tambahkaryawan', [LemburController::class, 'tambahkaryawan']);
    Route::get('/lembur/{kode_lembur}/{id_kantor}/{kode_dept}/getkaryawan', [LemburController::class, 'getkaryawan']);
    Route::post('/lembur/getlistkaryawan', [LemburController::class, 'getlistkaryawan']);
    Route::post('/lembur/storekaryawanlembur', [LemburController::class, 'storekaryawanlembur']);
    Route::post('/lembur/hapuskaryawanlembur', [LemburController::class, 'hapuskaryawanlembur']);
    Route::post('/lembur/storeallkaryawan', [LemburController::class, 'storeallkaryawan']);
    Route::post('/lembur/cancelkaryawan', [LemburController::class, 'cancelkaryawan']);
    Route::get('/lembur/{kode_lembur}/getlemburkaryawan', [LemburController::class, 'getlemburkaryawan']);
    Route::post('/lembur/hapuslemburkaryawan', [LemburController::class, 'hapuslemburkaryawan']);
    Route::post('/lembur/approve', [LemburController::class, 'approve']);
    Route::get('/lembur/{kode_lembur}/batalkan', [LemburController::class, 'batalkan']);
    Route::get('/lembur/{kode_lembur}/create_kethrd', [LemburController::class, 'create_kethrd']);
    Route::post('/lembur/{kode_lembur}/update_kethrd', [LemburController::class, 'update_kethrd']);
    Route::get('/monitoringsku', [PenjualanController::class, 'monitoringsku']);
    Route::post('/getsku', [PenjualanController::class, 'getsku']);

    //LHP

    Route::get('/lhp', [LhpController::class, 'index']);
    Route::get('/lhp/create', [LhpController::class, 'create']);
    Route::post('/lhp/store', [LhpController::class, 'store']);
    Route::delete('/lhp/{kode_lhp}/delete', [LhpController::class, 'delete']);
    Route::get('/lhp/{kode_lhp}/cetak', [LhpController::class, 'cetak']);

    Route::get('/lhp/kirimlhp', [LhpController::class, 'kirimlhp']);
    Route::post('/lhp/kirimlhp/show', [LhpController::class, 'showkirimlhp']);
    Route::get('/lhp/kirimlhp/create', [LhpController::class, 'createkirimlhp']);
    Route::post('/lhp/kirimlhp/store', [LhpController::class, 'storekirimlhp']);
    Route::post('/lhp/kirimlhp/approve', [LhpController::class, 'approvekirimlhp']);
    Route::post('/lhp/kirimlhp/cancel', [LhpController::class, 'cancelkirimlhp']);
    Route::post('/lhp/kirimlhp/delete', [LhpController::class, 'deletekirimlhp']);



    //Lock Location

    Route::get('/karyawan/{nik}/locklocation', [KaryawanController::class, 'locklocation']);
    Route::get('/karyawan/{nik}/unlocklocation', [KaryawanController::class, 'unlocklocation']);


    Route::controller(VisitController::class)->group(function () {
        Route::get('/visit', 'index')->name('visit');
        Route::post('/visit/show', 'show')->name('visit.show');
        Route::get('/visit/create', 'create')->name('visit.create');
        Route::post('/visit/store', 'store')->name('visit.store');
        Route::post('/visit/delete', 'delete')->name('visit.delete');
        Route::post('/visit/update', 'update')->name('visit.update');
        Route::post('/visit/edit', 'edit')->name('visit.edit');
        Route::get('/visit/laporanVisit', 'laporanVisit')->name('visit.laporanVisit');
        Route::post('/visit/cetakVisit', 'cetakVisit')->name('visit.cetakVisit');
    });

    Route::controller(RealisasiController::class)->group(function () {
        Route::get('/realisasi', 'index')->name('realisasi');
        Route::post('/realisasi/show', 'show')->name('realisasi.show');
        Route::get('/realisasi/create', 'create')->name('realisasi.create');
        Route::post('/realisasi/store', 'store')->name('realisasi.store');
        Route::post('/realisasi/delete', 'delete')->name('realisasi.delete');
        Route::post('/realisasi/update', 'update')->name('realisasi.update');
        Route::post('/realisasi/edit', 'edit')->name('realisasi.edit');
        Route::get('/realisasi/laporanRealisasi', 'laporanRealisasi')->name('realisasi.laporanRealisasi');
        Route::post('/realisasi/cetakRealisasi', 'cetakRealisasi')->name('realisasi.cetakRealisasi');
    });

    Route::controller(JaminanController::class)->group(function () {
        Route::get('/jaminan', 'index')->name('jaminan');
        Route::post('/jaminan/show', 'show')->name('jaminan.show');
        Route::get('/jaminan/create', 'create')->name('jaminan.create');
        Route::post('/jaminan/store', 'store')->name('jaminan.store');
        Route::post('/jaminan/delete', 'delete')->name('jaminan.delete');
        Route::post('/jaminan/update', 'update')->name('jaminan.update');
        Route::post('/jaminan/edit', 'edit')->name('jaminan.edit');
        Route::get('/jaminan/laporanJaminan', 'laporanJaminan')->name('jaminan.laporanJaminan');
        Route::post('/jaminan/cetakJaminan', 'cetakJaminan')->name('jaminan.cetakJaminan');
    });


    Route::post('/getnofakpenj', [PenjualanController::class, 'getnofakpenj']);
    Route::get('/penjualan/{no_fak_penj}/updatenofaktur', [PenjualanController::class, 'updatenofaktur']);
    //Route::get('/getnofakpenj', [PenjualanController::class, 'getnofakpenj']);
    //Ajuan Faktur
    Route::get('/ajuanfaktur', [AjuanfakturController::class, 'index']);
    Route::get('/ajuanfaktur/salesman', [AjuanfakturController::class, 'indexsalesman']);
    Route::get('/ajuanfaktur/{kode_pelanggan}/create', [AjuanfakturController::class, 'create']);
    Route::get('/ajuanfaktur/{kode_pelanggan}/createfromsales', [AjuanfakturController::class, 'createfromsales']);
    Route::get('/ajuanfaktur/{no_pengajuan}/edit', [AjuanfakturController::class, 'edit']);
    Route::post('/ajuanfaktur/{kode_pelanggan}/store', [AjuanfakturController::class, 'store']);
    Route::post('/ajuanfaktur/{no_pengajuan}/update', [AjuanfakturController::class, 'update']);
    Route::get('/ajuanfaktur/{no_pengajuan}/approve', [AjuanfakturController::class, 'approve']);
    Route::get('/ajuanfaktur/{no_pengajuan}/decline', [AjuanfakturController::class, 'decline']);
    Route::delete('/ajuanfaktur/{no_pengajuan}/delete', [AjuanfakturController::class, 'delete']);


    //Ajuan Routing
    Route::get('/ajuanrouting', [AjuanroutingController::class, 'index']);
    Route::get('/ajuanrouting/{kode_pelanggan}/create', [AjuanroutingController::class, 'create']);
    Route::post('/ajuanrouting/{kode_pelanggan}/store', [AjuanroutingController::class, 'store']);
    Route::get('/ajuanrouting/{no_pengajuan}/edit', [AjuanroutingController::class, 'edit']);
    Route::post('/ajuanrouting/{no_pengajuan}/update', [AjuanroutingController::class, 'update']);
    Route::get('/ajuanrouting/{no_pengajuan}/approve', [AjuanroutingController::class, 'approve']);
    Route::get('/ajuanrouting/{no_pengajuan}/decline', [AjuanroutingController::class, 'decline']);

    Route::delete('/ajuanrouting/{no_pengajuan}/delete', [AjuanroutingController::class, 'delete']);




    //Ajuan Transfer Dana View
    Route::get('/ajuantransferdana', [AjuantransferdanaController::class, 'index']);
    Route::get('/ajuantransferdana/create', [AjuantransferdanaController::class, 'create']);
    Route::post('/ajuantransferdana/store', [AjuantransferdanaController::class, 'store']);
    Route::get('/ajuantransferdana/{no_pengajuan}/edit', [AjuantransferdanaController::class, 'edit']);
    Route::post('/ajuantransferdana/{no_pengajuan}/update', [AjuantransferdanaController::class, 'update']);
    Route::get('/ajuantransferdana/{no_pengajuan}/prosesajuan', [AjuantransferdanaController::class, 'prosesajuan']);
    Route::post('/ajuantransferdana/{no_pengajuan}/proses', [AjuantransferdanaController::class, 'proses']);
    Route::get('/ajuantransferdana/{no_pengajuan}/batalkan', [AjuantransferdanaController::class, 'batalkan']);
    Route::delete('/ajuantransferdana/{no_pengajuan}/delete', [AjuantransferdanaController::class, 'delete']);
    Route::get('/ajuantransferdana/{no_pengajuan}/validasimanager', [AjuantransferdanaController::class, 'validasimanager']);
    Route::get('/ajuantransferdana/{no_pengajuan}/batalkanvalidasimanager', [AjuantransferdanaController::class, 'batalkanvalidasimanager']);

    //LogAktivitas

    Route::get('/logaktivitas', [LogaktivitasController::class, 'index']);


    Route::controller(KaryawanController::class)->group(function () {
        Route::get('/karyawan/laporanKaryawan', 'laporanKaryawan')->name('karyawan.laporanKaryawan');
        Route::post('/karyawan/cetakKaryawan', 'cetakKaryawan')->name('karyawan.cetakKaryawan');
    });

    Route::controller(KendaraanController::class)->group(function () {
        Route::get('/kendaraan/laporanKendaraan', 'laporanKendaraan')->name('kendaraan.laporanKendaraan');
        Route::post('/kendaraan/cetakLaporanKendaraan', 'cetakLaporanKendaraan')->name('kendaraan.cetakLaporanKendaraan');
    });

    //Worksheet OM

    Route::get('/worksheetom/oman', [OmancabangController::class, 'index']);
    Route::get('/worksheetom/komisisalesman', [TargetkomisiController::class, 'laporankomisi']);
    Route::get('/worksheetom/komisidriverhelper', [TargetkomisiController::class, 'laporankomisidriverhelper']);
    Route::get('/worksheetom/costratio', [LaporanaccountingController::class, 'costratio']);
    Route::get('/worksheetom/limitkredit', [LimitkreditController::class, 'index']);
    Route::get('/worksheetom/monitoringretur', [WorksheetomController::class, 'monitoringretur']);
    Route::post('/worksheetom/showmonitoringretur', [WorksheetomController::class, 'showmonitoringretur']);
    Route::post('/worksheetom/storepelunasanretur', [WorksheetomController::class, 'storepelunasanretur']);
});


Route::get('/cetak/{no_fak_penj}', [PrinterController::class, 'cetak']);
Route::get('/show', [PrinterController::class, 'show']);
Route::get('/cekphp', [PrinterController::class, 'cekphp']);
