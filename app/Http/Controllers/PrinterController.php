<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
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
    public function cetak()
    {


        $no_fak_penj = "BDGE012668";
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
            ->select('penjualan.*', 'nama_pelanggan', 'nama_karyawan', 'alamat_pelanggan', 'jenistransaksi', 'alamat_cabang', 'nama_cabang', 'print')
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
        $retur = DB::table('retur')
            ->selectRaw('SUM(total) as totalretur')
            ->where('no_fak_penj', $no_fak_penj)->first();

        $profile = CapabilityProfile::load("POS-5890");
        $connector = new RawbtPrintConnector();
        $printer = new Printer($connector, $profile);
        if ($faktur->jenistransaksi == "kredit") {
            $urllogo = base_path('/public/app-assets/images/kredit.png');
        } else {
            $urllogo = base_path('/public/app-assets/images/tunai.png');
        }
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
            $datadetail[] = new item($jumlah_dus . " Dus x " . $d->harga_dus, rupiah($jumlah_dus * $d->harga_dus));
        }
        try {
            /* Information for the receipt */
            $items = $datadetail;

            //dd($items);

            $subtotal = new item('Subtotal', '12.95');
            $tax = new item('A local tax', '1.30');
            $total = new item('Total', '14.25', true);
            /* Date is kept the same for testing */
            // $date = date('l jS \of F Y h:i:s A');
            $date = "Monday 6th of April 2015 02:56:25 PM";

            /* Start the printer */
            $logo = EscposImage::load($urllogo, false);

            /* Print top logo */
            if ($profile->getSupportsGraphics()) {
                $printer->graphics($logo);
            }
            if ($profile->getSupportsBitImageRaster() && !$profile->getSupportsGraphics()) {
                $printer->bitImage($logo);
            }

            /* Name of shop */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("ExampleMart Ltd.\n");
            $printer->selectPrintMode();
            $printer->text("Shop No. 42.\n");
            $printer->feed();


            /* Title of receipt */
            $printer->setEmphasis(true);
            $printer->text("SALES INVOICE\n");
            $printer->setEmphasis(false);

            /* Items */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setEmphasis(true);
            $printer->text(new item('', '$'));
            $printer->setEmphasis(false);
            foreach ($items as $item) {
                $printer->text($item->getAsString(32)); // for 58mm Font A
            }
            $printer->setEmphasis(true);
            $printer->text($subtotal->getAsString(32));
            $printer->setEmphasis(false);
            $printer->feed();

            /* Tax and total */
            $printer->text($tax->getAsString(32));
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text($total->getAsString(32));
            $printer->selectPrintMode();

            /* Footer */
            $printer->feed(2);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Thank you for shopping\n");
            $printer->text("at ExampleMart\n");
            $printer->text("For trading hours,\n");
            $printer->text("please visit example.com\n");
            $printer->feed(2);
            $printer->text($date . "\n");

            /* Barcode Default look */

            $printer->barcode("ABC", Printer::BARCODE_CODE39);
            $printer->feed();
            $printer->feed();


            // Demo that alignment QRcode is the same as text
            $printer2 = new Printer($connector); // dirty printer profile hack !!
            $printer2->setJustification(Printer::JUSTIFY_CENTER);
            $printer2->qrCode("https://rawbt.ru/mike42", Printer::QR_ECLEVEL_M, 8);
            $printer2->text("rawbt.ru/mike42\n");
            $printer2->setJustification();
            $printer2->feed();


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
