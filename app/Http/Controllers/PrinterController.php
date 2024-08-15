<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\RawbtPrintConnector;
use Mike42\Escpos\Printer;

class item
{
    private $name;
    private $price;
    private $dollarSign;

    public function __construct($name = '', $price = '', $dollarSign = false)
    {
        $this->name = $name;
        $this->price = $price;
        $this->dollarSign = $dollarSign;
    }

    public function getAsString($width = 48)
    {
        $rightCols = 10;
        $leftCols = $width - $rightCols;
        if ($this->dollarSign) {
            $leftCols = $leftCols / 2 - $rightCols / 2;
        }
        $left = str_pad($this->name, $leftCols);

        $sign = ($this->dollarSign ? '$ ' : '');
        $right = str_pad($sign . $this->price, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }

    public function __toString()
    {
        return $this->getAsString();
    }
}





class PrinterController extends Controller
{



    public function show()
    {
        return view('show');
    }
    public function cetak($no_fak_penj)
    {


        $no_fak_penj = Crypt::decrypt($no_fak_penj);
        $pelangganmp = [
            'TSM-00548',
            'TSM-00493',
            'TSM-02234',
            'TSM-01117',
            'TSM-00494',
            'TSM-00466',
            'PST00007',
            'PST00008',
            'PST00002'
        ];
        $faktur = DB::table('penjualan')
            ->select(
                'penjualan.*',
                'nama_pelanggan',
                'nama_karyawan',
                'alamat_pelanggan',
                'pelanggan.kode_cabang_pkp',
                'karyawan.kode_cabang',
                'jenistransaksi',
                'alamat_cabang',
                'nama_cabang',
                'nama_pt',
                'print',
                'pelanggan.jatuhtempo'
            )
            ->join('pelanggan', 'penjualan.kode_pelanggan', '=', 'pelanggan.kode_pelanggan')
            ->join('karyawan', 'penjualan.id_karyawan', '=', 'karyawan.id_karyawan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('no_fak_penj', $no_fak_penj)->first();

        $detail = DB::table('detailpenjualan')
            ->select('kode_produk', 'nama_barang', 'isipcsdus', 'isipack', 'isipcs', 'jumlah', 'subtotal', 'detailpenjualan.harga_dus', 'detailpenjualan.harga_pack', 'detailpenjualan.harga_pcs')
            ->join('barang', 'detailpenjualan.kode_barang', '=', 'barang.kode_barang')
            ->where('no_fak_penj', $no_fak_penj)
            ->get();

        $pembayaran = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj)->get();
        $cekpembayaran = DB::table('historibayar')->where('no_fak_penj', $no_fak_penj)->count();
        $retur = DB::table('retur')
            ->selectRaw('SUM(total) as totalretur')
            ->where('no_fak_penj', $no_fak_penj)->first();

        if (!empty($faktur->kode_cabang_pkp)) {
            $kode_cabang = $faktur->kode_cabang_pkp;
        } else {
            $kode_cabang = $faktur->kode_cabang;
        }

        $cbg = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        $profile = CapabilityProfile::load("POS-5890");
        $connector = new RawbtPrintConnector();
        $printer = new Printer($connector, $profile);
        // if ($faktur->jenistransaksi == "kredit") {
        //     $urllogo = base_path('/public/app-assets/images/kredit.png');
        // } else {
        //     $urllogo = base_path('/public/app-assets/images/tunai.png');
        // }
        $total = 0;
        foreach ($detail as $d) {
            $isipcsdus = $d->isipcsdus;
            $isipack = $d->isipack;
            $isipcs = $d->isipcs;
            $jumlah = $d->jumlah;
            $jumlah_dus = floor($jumlah / $isipcsdus);
            if ($jumlah != 0) {
                $sisadus = $jumlah % $isipcsdus;
            } else {
                $sisadus = 0;
            }
            if ($isipack == 0) {
                $jumlah_pack = 0;
                $sisapack = $sisadus;
            } else {
                $jumlah_pack = floor($sisadus / $isipcs);
                $sisapack = $sisadus % $isipcs;
            }
            $jumlah_pcs = $sisapack;
            $total += $d->subtotal;
            $datadetail[] = new item($d->nama_barang, "");
            if (!empty($jumlah_dus)) {
                $datadetail[] = new item($jumlah_dus . " Dus x " . $d->harga_dus, rupiah($jumlah_dus * $d->harga_dus));
            }
            if (!empty($jumlah_pack)) {
                $datadetail[] = new item($jumlah_pack . " Pck x " . $d->harga_pack, rupiah($jumlah_pack * $d->harga_pack));
            }

            if (!empty($jumlah_pcs)) {
                $datadetail[] = new item($jumlah_pcs . " Pcs x " . $d->harga_pcs, rupiah($jumlah_pcs * $d->harga_pcs));
            }
        }

        if (in_array($faktur->kode_pelanggan, $pelangganmp)) {
            $perusahaan = "CV. MAKMUR PERMATA";
            $cabang = "";
            $alamat = "Jln. Perintis Kemerdekaan 001/003 Karsamenak, Kawalu, Kota Tasikmalaya";
        } else {
            if ($faktur->tgltransaksi < "2024-03-01") {
                $perusahaan = "CV. PACIFIC";
                $cabang = "CABANG " . strtoupper($faktur->nama_cabang);
                $alamat = $faktur->alamat_cabang;
            } else {
                $perusahaan = "";
                $cabang = strtoupper($cbg->nama_pt);
                $alamat = $cbg->alamat_cabang;
            }
        }
        $totalbayar = 0;

        if (!empty($cekpembayaran)) {
            foreach ($pembayaran as $d) {
                $totalbayar += $d->bayar;
                $databayar[] = new item(date("d-m-y", strtotime($d->tglbayar)), rupiah($d->bayar));
            }
        } else {
            $databayar[] = new item('', '');
        }


        $totalretur = $retur->totalretur;
        try {
            /* Information for the receipt */
            $items = $datadetail;
            if (!empty($pembayaran)) {
                $itemsbayar = $databayar;
            }
            //dd($items);


            /* Date is kept the same for testing */
            // $date = date('l jS \of F Y h:i:s A');
            $date = date("l jS \of F Y h:i:s A");

            /* Start the printer */
            // $logo = EscposImage::load($urllogo, false);

            // /* Print top logo */
            // if ($profile->getSupportsGraphics()) {
            //     $printer->graphics($logo);
            // }
            // if ($profile->getSupportsBitImageRaster() && !$profile->getSupportsGraphics()) {
            //     $printer->bitImage($logo);
            // }

            /* Name of shop */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->setEmphasis(true);
            $printer->text($perusahaan . ".\n");
            $printer->text($cabang . ".\n");
            $printer->setEmphasis(false);
            $printer->selectPrintMode();
            $printer->text($alamat . ".\n");
            $printer->text($date . "\n");


            /* Title of receipt */
            $printer->setEmphasis(true);
            $printer->text("LEMBAR UNTUK PELANGGAN\n");
            $printer->setEmphasis(false);

            /* Items */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text(new item('', ''));
            $pelanggan_salesman = new item($faktur->no_fak_penj, $faktur->nama_karyawan);
            $printer->text($pelanggan_salesman->getAsString(32));
            $printer->text(date("d-m-Y H:i:s", strtotime($faktur->date_created)) . "\n");

            $printer->text($faktur->kode_pelanggan . " - " . $faktur->nama_pelanggan . "\n");
            $printer->text(strtolower(ucwords($faktur->alamat_pelanggan)));
            $jatuhtempo = date("Y-m-d", strtotime("+$faktur->jatuhtempo days", strtotime($faktur->tgltransaksi)));
            if ($faktur->jenistransaksi == 'kredit') {
                $printer->text("Jatuh Tempo : " . date("d-m-Y", strtotime($jatuhtempo)) . "\n");
            }
            $printer->text(new item('', ''));

            $printer->setEmphasis(true);
            foreach ($items as $item) {
                $printer->text($item->getAsString(32)); // for 58mm Font A
            }

            $subtotal = new item('Subtotal', rupiah($faktur->subtotal));
            $potongan = new item('Potongan', rupiah($faktur->potongan));
            $totalnonppn = $faktur->subtotal - $faktur->potongan - $faktur->potistimewa - $faktur->penyharga;
            $total = new item('Total', rupiah($totalnonppn));
            if (!empty($faktur->ppn)) {
                $ppn = new item('PPN', rupiah($faktur->ppn));
            }
            $_grandtotal = $faktur->total - $totalretur;
            $retur = new item('Retur', rupiah($totalretur));
            $grandtotal = new item('Grand Total', rupiah($_grandtotal));
            //$total = new item('Total', '14.25', true);


            $printer->setEmphasis(true);
            $printer->text($subtotal->getAsString(32));
            $printer->setEmphasis(false);
            $printer->feed();

            /* Tax and total */
            $printer->text($potongan->getAsString(32));
            $printer->text($total->getAsString(32));
            if (!empty($faktur->ppn)) {
                $printer->text($ppn->getAsString(32));
            }
            $printer->text($retur->getAsString(32));
            // $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->feed();
            $printer->setEmphasis(true);
            $printer->text($grandtotal->getAsString(32));
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text(strtoupper($faktur->jenistransaksi) . ".\n");
            $printer->selectPrintMode();

            /* Footer */

            if (!empty($cekpembayaran)) {
                $printer->feed();
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("PEMBAYARAN \n");
                $printer->setEmphasis(true);
                foreach ($itemsbayar as $itembayar) {
                    $printer->text($itembayar->getAsString(32)); // for 58mm Font A
                }
                $sisatagihan = $faktur->total - $totalretur - $totalbayar;
                $sisa = new item('SISA TAGIHAN', rupiah($sisatagihan));
                $grandtotalbayar = new item('TOTAL BAYAR', rupiah($totalbayar));
                $printer->text($grandtotalbayar->getAsString(32)); // for 58mm Font A
                $printer->text($sisa->getAsString(32)); // for 58mm Font A
            }



            $printer->feed(2);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Tidak Di Perkenankan Transfer \n");
            $printer->text("Ke Rekening Salesman \n");
            $printer->text("Apapun Jenis Transaksinya \n");
            $printer->text("Wajib Ditandatangani \n");
            $printer->text("kedua belah pihak,\n");
            $printer->text("Terimakasih\n");
            $printer->text("www.pedasalami.com\n");
            $printer->feed();

            if (!empty($faktur->signature)) {
                $urlsignature = base_path('/public/storage/signature/') . $faktur->signature;
                $signature = EscposImage::load($urlsignature, false);
                /* Print top logo */
                if ($profile->getSupportsGraphics()) {
                    $printer->graphics($signature);
                }
                if ($profile->getSupportsBitImageRaster() && !$profile->getSupportsGraphics()) {
                    $printer->bitImage($signature);
                }
            }







            //Faktur PERUSAHAAN
            /* Name of shop */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            //$printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("\n");
            $printer->text("\n");
            $printer->feed(2);
            // $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->setEmphasis(true);
            $printer->text($perusahaan . ".\n");
            $printer->text($cabang . ".\n");
            $printer->setEmphasis(false);
            $printer->selectPrintMode();
            $printer->text($alamat . ".\n");
            $printer->text($date . "\n");


            /* Title of receipt */
            $printer->setEmphasis(true);
            $printer->text("LEMBAR UNTUK PERUSAHAAN\n");
            $printer->setEmphasis(false);

            /* Items */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text(new item('', ''));
            $pelanggan_salesman = new item($faktur->no_fak_penj, $faktur->nama_karyawan);
            $printer->text($pelanggan_salesman->getAsString(32));
            $printer->text(date("d-m-Y H:i:s", strtotime($faktur->date_created)) . "\n");
            $printer->text($faktur->kode_pelanggan . " - " . $faktur->nama_pelanggan . "\n");
            $printer->text(strtolower(ucwords($faktur->alamat_pelanggan)));
            $jatuhtempo = date("Y-m-d", strtotime("+$faktur->jatuhtempo days", strtotime($faktur->tgltransaksi)));
            if ($faktur->jenistransaksi == 'kredit') {
                $printer->text("Jatuh Tempo : " . date("d-m-Y", strtotime($jatuhtempo)) . "\n");
            }
            $printer->text(new item('', ''));

            $printer->setEmphasis(true);
            foreach ($items as $item) {
                $printer->text($item->getAsString(32)); // for 58mm Font A
            }

            $subtotal = new item('Subtotal', rupiah($faktur->subtotal));
            $potongan = new item('Potongan', rupiah($faktur->potongan));
            $totalnonppn = $faktur->subtotal - $faktur->potongan - $faktur->potistimewa - $faktur->penyharga;
            $total = new item('Total', rupiah($totalnonppn));
            if (!empty($faktur->ppn)) {
                $ppn = new item('PPN', rupiah($faktur->ppn));
            }
            $_grandtotal = $faktur->total - $totalretur;
            $retur = new item('Retur', rupiah($totalretur));
            $grandtotal = new item('Grand Total', rupiah($_grandtotal));
            //$total = new item('Total', '14.25', true);


            $printer->setEmphasis(true);
            $printer->text($subtotal->getAsString(32));
            $printer->setEmphasis(false);
            $printer->feed();

            /* Tax and total */
            $printer->text($potongan->getAsString(32));
            $printer->text($total->getAsString(32));
            if (!empty($faktur->ppn)) {
                $printer->text($ppn->getAsString(32));
            }
            $printer->text($retur->getAsString(32));
            // $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->feed();
            $printer->setEmphasis(true);
            $printer->text($grandtotal->getAsString(32));
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text(strtoupper($faktur->jenistransaksi) . ".\n");
            $printer->selectPrintMode();

            if (!empty($cekpembayaran)) {
                $printer->feed();
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("PEMBAYARAN \n");
                $printer->setEmphasis(true);
                foreach ($itemsbayar as $itembayar) {
                    $printer->text($itembayar->getAsString(32)); // for 58mm Font A
                }
                $grandtotalbayar = new item('TOTAL BAYAR', rupiah($totalbayar));
                $sisatagihan = $faktur->total - $totalretur - $totalbayar;
                $sisa = new item('SISA TAGIHAN', rupiah($sisatagihan));
                $printer->text($grandtotalbayar->getAsString(32)); // for 58mm Font A
                $printer->text($sisa->getAsString(32)); // for 58mm Font A
            }

            /* Footer */
            $printer->feed(2);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Tidak Di Perkenankan Transfer \n");
            $printer->text("Ke Rekening Salesman \n");
            $printer->text("Apapun Jenis Transaksinya \n");
            $printer->text("Wajib Ditandatangani \n");
            $printer->text("kedua belah pihak,\n");
            $printer->text("Terimakasih\n");
            $printer->text("www.pedasalami.com\n");
            $printer->feed();

            if (!empty($faktur->signature)) {
                $urlsignature = base_path('/public/storage/signature/') . $faktur->signature;
                $signature = EscposImage::load($urlsignature, false);
                /* Print top logo */
                if ($profile->getSupportsGraphics()) {
                    $printer->graphics($signature);
                }
                if ($profile->getSupportsBitImageRaster() && !$profile->getSupportsGraphics()) {
                    $printer->bitImage($signature);
                }
            }




            // /* Barcode Default look */

            // $printer->barcode("ABC", Printer::BARCODE_CODE39);
            // $printer->feed();
            // $printer->feed();


            // // Demo that alignment QRcode is the same as text
            // $printer2 = new Printer($connector); // dirty printer profile hack !!
            // $printer2->setJustification(Printer::JUSTIFY_CENTER);
            // $printer2->qrCode("https://rawbt.ru/mike42", Printer::QR_ECLEVEL_M, 8);
            // $printer2->text("rawbt.ru/mike42\n");
            // $printer2->setJustification();
            // $printer2->feed();


            /* Cut the receipt and open the cash drawer */
            $printer->cut();
            $printer->pulse();
        } catch (Exception $e) {
            echo $e->getMessage();
        } finally {
            $printer->close();
        }
    }

    public function cekphp()
    {
        phpinfo();
    }
}
