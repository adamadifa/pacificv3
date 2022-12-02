@foreach ($produk as $d)
@php
$isipcsdus = $d->isipcsdus;
$isipack = $d->isipack;
$isipcs = $d->isipcs;
$jmlpermintaan = $d->jml_permintaan;
$jmlpermintaan_dus = floor($jmlpermintaan / $isipcsdus);
if ($jmlpermintaan != 0) {
$sisadus_permintaan = $jmlpermintaan % $isipcsdus;
} else {
$sisadus_permintaan = 0;
}
if ($isipack == 0) {
$jmlpack_permintaan = 0;
$sisapack_permintaan = $sisadus_permintaan;
} else {
$jmlpack_permintaan = floor($sisadus_permintaan / $isipcs);
$sisapack_permintaan = $sisadus_permintaan % $isipcs;
}

$jmlpcs_permintaan = $sisapack_permintaan;
@endphp
<input type="hidden" name="isipcsdus[]" value="{{ $d->isipcsdus }}">
<input type="hidden" name="isipcs[]" value="{{ $d->isipcs }}">
<input type="hidden" name="kode_produk[]" value="{{ $d->kode_produk }}">
<tr>
    <td>{{ $d->kode_produk }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td style="width: 12%">
        <input type="text" autocomplete="off" class="form-control" name="jmldus[]" value="{{ !empty($jmlpermintaan_dus) ? $jmlpermintaan_dus : '' }}">
    </td>
    <td>{{ $d->satuan }}</td>
    <td style="width:12%">
        <input type="{{ !empty($d->isipack) ? 'text' : 'hidden' }}" autocomplete="off" class="form-control" name="jmlpack[]" value="{{ !empty($jmlpack_permintaan) ? $jmlpack_permintaan : '' }}">
    </td>
    <td>PACK</td>
    <td style="width: 12%">
        <input type="text" autocomplete="off" class="form-control" name="jmlpcs[]" value="{{ !empty($jmlpcs_permintaan) ? $jmlpcs_permintaan : '' }}">
    </td>
    <td>PCS</td>
</tr>
@endforeach
