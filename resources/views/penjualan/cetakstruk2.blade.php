<div id="print" style="position: absolute; z-index:1;  background-color:white">
    <p style="text-align: center">
        ------------------------------------------------<br>
        LEMBAR UNTUK PELANGGAN<br>
        @if (in_array($faktur->kode_pelanggan,$pelangganmp))
        CV MAKMUR PERMATA<br>
        Jln. Perintis Kemerdekaan 001/003<br>
        Karsamenak, Kawalu, Kota Tasikmalaya<br>
        @else
        CV PACIFIC CABANG {{ strtoupper($faktur->nama_cabang) }}<br>
        {{ $faktur->alamat_cabang }}<br>
        @endif
        ------------------------------------------------<br>
    </p>
    <p style="display:flex; justify-content: space-between">
        <span>{{ $faktur->no_fak_penj }}({{ strtoupper($faktur->jenistransaksi) }})</span><span>{{ $faktur->nama_karyawan }}</span>
    </p>
    <p>
        {{ date("d-m-Y H:i:s",strtotime($faktur->date_created)) }}<br>
        {{ $faktur->kode_pelanggan }} - {{ $faktur->nama_pelanggan }}<br>
        {{ $faktur->alamat_pelanggan }} - {{ $faktur->alamat_pelanggan }}<br>
        ------------------------------------------------
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
    ------------------------------------------------<br>
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
    ------------------------------------------------<br>
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
        <br>
        <br>
        Tidak Di Perkenankan Transfer Ke Rekening Salesman<br>
        Apapun Jenis Transaksinya Wajib Ditandatangani <br>kedua belah pihak<br><br>
        Terimakasih<br>
        www.pedasalami.com
    </p>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-info btn-block" id="printstruk" onclick="BtPrint(document.getElementById('pre_print').innerText)"><i class="feather icon-printer mr-1"></i>Cetak Faktur
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

$len = 30;
$data = "";
$total = 0;
$data .="------------------------------------------<br>";
$data .="         LEMBAR UNTUK PELANGGAN <br>";
if(in_array($faktur->kode_pelanggan,$pelangganmp)){
$data .="            CV MAKMUR PERMATA        <br>";
$data .="   Jl. Perintis Kemerdekaan 001/003<br>";
$data .="    Karsamenak,Kawalu,Tasikmalaya<br>";
}else{
$data .="               CV PACIFIC        <br>";
}
$data .= $faktur->alamat_cabang."<br>";
$data .="------------------------------------------<br>";
$data .=sprintf("%-$len"."s\t%s\n",$faktur->no_fak_penj."(<b>".strtoupper($faktur->jenistransaksi)."</b>)","");
$data .= $faktur->nama_karyawan."<br>";
$data .=date("d-m-Y H:i:s",strtotime($faktur->date_created))."<br>";
$data .=$faktur->kode_pelanggan." - ".$faktur->nama_pelanggan."<br>";
$data .=$faktur->alamat_pelanggan."<br>";
$data .="------------------------------------------<br>";
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
    $data .=$d->nama_barang."<br>";












    // echo "LENPACK =".$lenpack."<br>";
    // echo "LENDUS =".$lendus."(".$l.")<br>";
    // echo "LENPCS =".$lenpcs."<br>";
    // echo "LENPOT =".$lenpot."<br>";
    if(!empty($jumlah_dus)){
        // $harga_dus = rupiah($jumlah_dus * $d->harga_dus);
        // $l = strlen($harga_dus);
        // if($l==9){
        //     $lendus = $len +1;
        // }else if($l==8){
        //     $lendus = $len +2;
        // }else if($l==7){
        //     $lendus = $len +3;
        // }else if($l==6){
        //     $lendus = $len +4;
        // }else if($l==5){
        //     $lendus = $len +5;
        // }else if($l==4){
        //     $lendus = $len +6;
        // }else if($l==3){
        //     $lendus = $len +7;
        // }else if($l==2){
        //     $lendus = $len +8;
        // }else if($l==1){
        //     $lendus = $len +9;
        // }
        $data .=sprintf("%-$len"."s\t%s\n", $jumlah_dus." Dus x ".rupiah($d->harga_dus),rupiah($jumlah_dus * $d->harga_dus));
    }
    if(!empty($jumlah_pack)){
        // $lpack = strlen(rupiah($jumlah_pack * $d->harga_pack));
        // //$data .= "<br>";
        // if($lpack==9){
        //     $lenpack = $len +1;
        // }else if($lpack==8){
        //     $lenpack = $len +2;
        // }else if($lpack==7){
        //     $lenpack = $len +3;
        // }else if($lpack==6){
        //     $lenpack = $len +4;
        // }else if($lpack==5){
        //     $lenpack = $len +5;
        // }else if($lpack==4){
        //     $lenpack = $len +6;
        // }else if($lpack==3){
        //     $lenpack = $len +7;
        // }else if($lpack==2){
        //     $lenpack = $len +8;
        // }else if($lpack==1){
        //     $lenpack = $len +9;
        // }
        $data .=sprintf("%-$len"."s\t%s\n", $jumlah_pack." Pck x ".rupiah($d->harga_pack),rupiah($jumlah_pack * $d->harga_pack));
    }
    if(!empty($jumlah_pcs)){
        // $lpcs = strlen(rupiah($jumlah_pcs * $d->harga_pcs));
        // if($lpcs==9){
        //     $lenpcs = $len +1;
        // }else if($lpcs==8){
        //     $lenpcs = $len +2;
        // }else if($lpcs==7){
        //     $lenpcs = $len +3;
        // }else if($lpcs==6){
        //     $lenpcs = $len +4;
        // }else if($lpcs==5){
        //     $lenpcs = $len +5;
        // }else if($lpcs==4){
        //     $lenpcs = $len +6;
        // }else if($lpcs==3){
        //     $lenpcs = $len +7;
        // }else if($lpcs==2){
        //     $lenpcs = $len +8;
        // }else if($lpcs==1){
        //     $lenpcs = $len +9;
        // }

        //$data .= "<br>";
        $data .=sprintf("%-$len"."s\t%s\n", $jumlah_pcs." Pcs x ".rupiah($d->harga_pcs),rupiah($jumlah_pcs * $d->harga_pcs));
    }
}
$data .="------------------------------------------<br>";
    // $lpot = strlen($faktur->potongan);
    // $ltotal = strlen($totalnonppn);
    // $lretur = strlen($retur->totalretur);
    // if($lpot==9){
    //     $lenpot = $len +1;
    // }else if($lpot==8){
    //     $lenpot = $len +2;
    // }else if($lpot==7){
    //     $lenpot = $len +3;
    // }else if($lpot==6){
    //     $lenpot = $len +4;
    // }else if($lpot==5){
    //     $lenpot = $len +5;
    // }else if($lpot==4){
    //     $lenpot = $len +6;
    // }else if($lpot==3){
    //     $lenpot = $len +7;
    // }else if($lpot==2){
    //     $lenpot = $len +8;
    // }else if($lpot==1){
    //     $lenpot = $len +9;
    // }

    // if($ltotal==9){
    //     $lentotal = $len +1;
    // }else if($ltotal==8){
    //     $lentotal = $len +2;
    // }else if($ltotal==7){
    //     $lentotal = $len +3;
    // }else if($ltotal==6){
    //     $lentotal = $len +4;
    // }else if($ltotal==5){
    //     $lentotal = $len +5;
    // }else if($ltotal==4){
    //     $lentotal = $len +6;
    // }else if($ltotal==3){
    //     $lentotal = $len +7;
    // }else if($ltotal==2){
    //     $lentotal = $len +8;
    // }else if($ltotal==1){
    //     $lentotal = $len +9;
    // }

    // if($lretur==9){
    //     $lenretur = $len +1;
    // }else if($lretur==8){
    //     $lenretur = $len +2;
    // }else if($lretur==7){
    //     $lenretur = $len +3;
    // }else if($lretur==6){
    //     $lenretur = $len +4;
    // }else if($lretur==5){
    //     $lenretur = $len +5;
    // }else if($lretur==4){
    //     $lenretur = $len +6;
    // }else if($lretur==3){
    //     $lenretur = $len +7;
    // }else if($lretur==2){
    //     $lenretur = $len +8;
    // }else if($lretur==1){
    //     $lenretur = $len +9;
    // }else{
    //     $lenretur = $len + 10;
    // }
$data .=sprintf("%-$len"."s\t%s\n","Potongan",rupiah($faktur->potongan));
$data .=sprintf("%-$len"."s\t%s\n","TOTAL",rupiah($totalnonppn));
$data .=sprintf("%-$len"."s\t%s\n","RETUR",rupiah($retur->totalretur));
$data .=sprintf("%-$len"."s\t%s\n","PPN",rupiah($faktur->ppn));
$data .=sprintf("%-$len"."s\t%s\n","GRAND TOTAL",rupiah($faktur->total-$retur->totalretur));
$data .="------------------------------------------<br>";
$data .="Pembayaran<br>";
$totalbayar=0;
foreach( $pembayaran as $d ) {
    $totalbayar += $d->bayar;
$data .=sprintf("%-$len"."s\t%s\n",date('d-m-y',strtotime($d->tglbayar)),rupiah($d->bayar));
}
$data .= "
";
$data .=sprintf("%-$len"."s\t%s\n","Total Bayar",rupiah($totalbayar));
$data .=sprintf("%-$len"."s\t%s\n","Sisa Tagihan",rupiah($faktur->total - $retur->totalretur - $totalbayar));
$data .= "

";
$data .= "     *Tidak Di Perkenankan Transfer<br>";
$data .= "          Ke Rekening Salesman<br>";
$data .= "    *Apapun Jenis Transaksinya Wajib <br>";
$data .= "     ditandatangani keduabelah pihak<br><br>";
$data .= "            Terimakasih<br>";
$data .= "          www.pedasalami.com<br>";
$data .= "<br>";
$data .= "            Print Ke - ".$faktur->print + 1;
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


<script>
    $(function() {
        $("#printstruk").click(function(e) {
            var no_fak_penj = "{{ $faktur->no_fak_penj }}";
            $.ajax({
                type: 'POST'
                , url: '/penjualan/updateprint'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_fak_penj: no_fak_penj
                }
                , cache: false
                , success: function(respond) {
                    $("#mdlcetakfaktur").modal("hide");
                }
            });
        });
    });

</script>
