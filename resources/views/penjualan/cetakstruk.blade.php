<div id="print" style="position: absolute; z-index:1; background-color:white">
    <p style="text-align: center">
        ------------------------------------------------------------<br>
        CV MAKMUR PERMATA<br>
        Jln. Perintis Kemerdekaan 001/003<br>
        Karsamenak, Kawalu, Kota Tasikmalaya<br>
        ------------------------------------------------------------<br>
    </p>
    <p style="display:flex; justify-content: space-between">
        <span>{{ $faktur->no_fak_penj }}</span><span>{{ $faktur->nama_karyawan }}</span>
    </p>
    <p>
        {{ date("d-m-Y",strtotime($faktur->tgltransaksi)) }}<br>
        {{ $faktur->kode_pelanggan }} - {{ $faktur->nama_pelanggan }}<br>
        ------------------------------------------------------------
    </p>
    <p>
        @php
        $total = 0;
        @endphp
        @foreach ($detail as $d)
        @php
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
        @endphp

        <div>
            <b>{{ $d->nama_barang }}</b>
            @if (!empty($jumlah_dus))
            <div style="display: flex; justify-content:space-between">
                <span>{{ $jumlah_dus }} Dus x {{ rupiah($d->harga_dus) }}</span><span>{{ rupiah($jumlah_dus * $d->harga_dus) }}</span>
            </div>
            @endif
            @if (!empty($jumlah_pack))
            <div style="display: flex; justify-content:space-between">
                <span>{{ $jumlah_pack }} Pack x {{ rupiah($d->harga_pack) }}</span><span>{{ rupiah($jumlah_pack * $d->harga_pack) }}</span>
            </div>
            @endif
            @if (!empty($jumlah_pcs))
            <div style="display: flex; justify-content:space-between">
                <span>{{ $jumlah_pcs }} Pcs x {{ rupiah($d->harga_pcs) }}</span><span>{{ rupiah($jumlah_pcs * $d->harga_pcs) }}</span>
            </div>
            @endif
        </div>
        @endforeach
    </p>
    ------------------------------------------------------------<br>
    <div style="display: flex; justify-content:space-between">
        <span>Potongan</span><span>{{ rupiah($faktur->potongan) }}</span>
    </div>
    <div style="display: flex; justify-content:space-between">
        <span>Total</span><span>{{ rupiah($faktur->total) }}</span>
    </div>
    <p style="text-align: center">
        Terimakasih<br>
        www.pacific-tasikmalaya.com
    </p>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-info btn-block" onclick="BtPrint(document.getElementById('pre_print').innerText)"><i class="feather icon-printer mr-1"></i>Cetak Faktur
            </button>
        </div>
    </div>
</div>
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
    if(!empty($jumlah_dus)){
        $data .= "  ". sprintf("%-$len"."s\t%s\n", $jumlah_dus." Dus x ".rupiah($d->harga_dus), "    ".rupiah($jumlah_dus * $d->harga_dus));
    }
    if(!empty($jumlah_pack)){
        //$data .= "<br>";
        $data .= "  ". sprintf("%-$len"."s\t%s\n", $jumlah_pack." Pack x ".rupiah($d->harga_pack), "    ".rupiah($jumlah_pack * $d->harga_pack));
    }
    if(!empty($jumlah_pcs)){
        //$data .= "<br>";
        $data .= "  ". sprintf("%-$len"."s\t%s\n", $jumlah_pcs." Pcs x ".rupiah($d->harga_pcs), "            ".rupiah($jumlah_pcs * $d->harga_pcs));
    }
}
$data .= "  -----------------------------------<br>";
$data .= "  ". sprintf("%-$len"."s\t%s\n","Potongan","            ".rupiah($faktur->potongan));
$data .= "  ". sprintf("%-$len"."s\t%s\n","TOTAL","                    ".rupiah($faktur->total));
$data .= "

";
$data .= "            Terimakasih<br>";
$data .= "     www.pacific-tasikmalaya.com";
 echo "<pre id='pre_print' style='position: absolute; z-index:0'>$data</pre>"; ?>


<script>
    function BtPrint(prn) {

        //alert(prn);
        var S = "#Intent;scheme=rawbt;";
        var P = "package=ru.a402d.rawbtprinter;end;";
        var textEncoded = encodeURI(prn);
        window.location.href = "intent:" + textEncoded + S + P;
    }

</script>
