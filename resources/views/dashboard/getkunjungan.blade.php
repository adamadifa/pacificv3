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
    <td>{{ date("H:i",strtotime($d->checkout_time)) }}</td>
    <td>{{ $j.':'.$m}}</td>
    </tr>
    @endforeach
