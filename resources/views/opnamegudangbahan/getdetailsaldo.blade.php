@php
$no=1;
@endphp
@foreach ($detail as $b)
@php
$qty_berat = $b->qtyberatsa + $b->qtypemb2 + $b->qtylainnya2 + $b->qtyreturpengganti2 - $b->qtyprod4 - $b->qtyseas4 - $b->qtypdqc4 - $b->qtylain4 - $b->qtysus4 - $b->qtycabang4;
$qty_unit = $b->qtyunitsa + $b->qtypemb1 + $b->qtylainnya1 + $b->qtyreturpengganti1 - $b->qtyprod3 - $b->qtyseas3 - $b->qtypdqc3 - $b->qtylain3 - $b->qtysus3 - $b->qtycabang3;
@endphp
<tr>
    <td style="width:10px">{{$loop->iteration}}</td>
    <td>
        <input type="hidden" name="kode_barang[]" id="kode_barang" value="{{$b->kode_barang}}">
        {{$b->kode_barang}}
    </td>
    <td>{{$b->nama_barang}}</td>
    <td>{{$b->kategori}}</td>
    <td class="text-right">
        <input type="hidden" name="qty_unit[]" id="qty_unit" value="{{$qty_unit}}">
        {{desimal($qty_unit)}}
    </td>
    <td class="text-right">
        <input type="hidden" name="qty_berat[]" id="qty_berat" value="{{$qty_berat}}">
        {{ desimal($qty_berat)}}
    </td>
</tr>
@endforeach
