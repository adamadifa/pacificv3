<div id="print" style="position: absolute; z-index:1;  background-color:white">
    <p style="text-align: center">
        ------------------------------------------------------------<br>
        LEMBAR UNTUK PELANGGAN<br>
        @if (in_array($faktur->kode_pelanggan,$pelangganmp))
        CV MAKMUR PERMATA<br>
        Jln. Perintis Kemerdekaan 001/003<br>
        Karsamenak, Kawalu, Kota Tasikmalaya<br>
        @else
        CV PACIFIC CABANG {{ strtoupper($faktur->nama_cabang) }}<br>
        {{ $faktur->alamat_cabang }}<br>
        @endif
        ------------------------------------------------------------<br>
    </p>
    <p style="display:flex; justify-content: space-between">
        <span>{{ $faktur->no_fak_penj }}({{ $faktur->jenistransaksi }})</span><span>{{ $faktur->nama_karyawan }}</span>
    </p>
    <p>
        {{ date("d-m-Y H:i:s",strtotime($faktur->date_created)) }}<br>
        {{ $faktur->kode_pelanggan }} - {{ $faktur->nama_pelanggan }}<br>
        {{ $faktur->alamat_pelanggan }} - {{ $faktur->alamat_pelanggan }}<br>
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
        <span>Total</span><span>
            @php
            $totalnonppn = $faktur->subtotal - $faktur->potongan - $faktur->potistimewa - $faktur->penyharga;
            @endphp
            {{ rupiah($totalnonppn)  }}
        </span>
    </div>
    <div style="display: flex; justify-content:space-between">
        <span>RETUR</span><span>{{ rupiah($retur->totalretur) }}</span>
    </div>
    <div style="display: flex; justify-content:space-between">
        <span>PPN</span><span>{{ rupiah($faktur->ppn) }}</span>
    </div>
    <div style="display: flex; justify-content:space-between">
        <span>Total</span><span>{{ rupiah($faktur->total-$retur->totalretur) }}</span>
    </div>
    ------------------------------------------------------------<br>
    <p>Pembayaran</p>
    @php
    $totalbayar = 0;
    @endphp
    @foreach ($pembayaran as $d)
    @php
    $totalbayar += $d->bayar;
    @endphp
    <div style="display: flex; justify-content:space-between">
        <span>{{ date("d-m-y",strtotime($d->tglbayar)) }}</span><span>{{ rupiah($d->bayar) }}</span>
    </div>
    @endforeach
    <div style="display: flex; justify-content:space-between">
        <span>Total Bayar</span><span>{{ rupiah($totalbayar) }}</span>
    </div>
    <div style="display: flex; justify-content:space-between">
        <span>Sisa Bayar</span><span>{{ rupiah($faktur->total - $retur->totalretur - $totalbayar) }}</span>
    </div>
    <p style="text-align: center">
        Terimakasih<br>
        www.pedasalami.com
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
$data .="------------------------------------------------<br>";
$data .="             LEMBAR UNTUK PELANGGAN <br>";
if(in_array($faktur->kode_pelanggan,$pelangganmp)){
$data .="                CV MAKMUR PERMATA        <br>";
}else{
$data .= "                CV PACIFIC        <br>";
}
$data .="Jl. Perintis Kemerdekaan 001/003<br>";
$data .="Karsamenak,Kawalu,Tasikmalaya<br>";

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
