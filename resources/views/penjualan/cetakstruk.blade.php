<?php
function maxLen($input) {
    $length = 0;
    foreach($input as $entry)
        if(strlen($entry->kode_produk)>$length) $length = strlen($entry->kode_produk);
    return $length;
}

$len = maxLen($detail);
$data = "";
$total = 0;
$data .= "  -----------------------------------<br>";
$data .= "          CV MAKMUR PERMATA        <br>";
$data .= "  Jln. Perintis Kemerdekaan 001/003<br>";
$data .= "  Karsamenak, Kawalu, Kota Tasikmalaya<br>";
$data .= "  -----------------------------------<br>";
$data .= "  ". sprintf("%-$len"."s\t%s\n",$faktur->no_fak_penj,"              ".$faktur->nama_karyawan);
$data .=    "  ". date("d-m-Y",strtotime($faktur->tgltransaksi))."<br>";
$data .=    "  ". $faktur->kode_pelanggan." - ".$faktur->nama_pelanggan."<br>";
$data .= "  -----------------------------------<br>";
foreach( $detail as $d ) {
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
    $data .= "  ".$d->nama_barang."<br>";
    $data .= "  ". sprintf("%-$len"."s\t%s\n", $jumlah_dus." x ".rupiah($d->harga_dus), "            ".rupiah($d->subtotal));
}
$data .= "  -----------------------------------<br>";
$data .= "  ". sprintf("%-$len"."s\t%s\n","Potongan","            ".rupiah($faktur->potongan));
$data .= "  ". sprintf("%-$len"."s\t%s\n","TOTAL","                    ".rupiah($faktur->total));
$data .= "

";
$data .= "            Terimakasih<br>";
$data .= "     www.pacific-tasikmalaya.com<br>";
echo "<pre id='pre_print'>$data</pre>";
?>
<button class="btn btn-green" onclick="BtPrint(document.getElementById('pre_print').innerText)">Cetak
</button>
<script>
    function BtPrint(prn) {
        var S = "#Intent;scheme=rawbt;";
        var P = "package=ru.a402d.rawbtprinter;end;";
        var textEncoded = encodeURI(prn);
        window.location.href = "intent:" + textEncoded + S + P;
    }

</script>
