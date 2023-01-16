@foreach ($kunjungan as $d)
@php

$awal = strtotime($d->checkin_time); //waktu awal
$akhir = strtotime($d->checkout_time); //waktu akhir
$diff = $akhir - $awal;
$jam = floor($diff / (60 * 60));
$menit = $diff - $jam * (60 * 60) ;
$minutes = floor($menit/60);
$j = $jam <= 9 ? '0' .$jam : $jam; $m=$minutes<=9 ? '0' .$minutes : $minutes; @endphp <tr>
    <td>{{ $d->kode_pelanggan }}</td>
    <td>{{ $d->nama_pelanggan }}</td>
    <td>{{ date("H:i",strtotime($d->checkin_time)) }}</td>
    <td>{{ !empty($d->checkout_time) ? date("H:i",strtotime($d->checkout_time)) : 0 }}</td>
    <td>{{ !empty($d->checkout_time) ? $j.':'.$m : 0}}</td>
    </tr>
    @endforeach
