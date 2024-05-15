<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Cetak Laporan Tunai Transfer</title>
   <style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

      body {
         font-family: 'Poppins'
      }

      .datatable3 {
         border: 2px solid #D6DDE6;
         border-collapse: collapse;
         font-size: 11px;
      }

      .datatable3 td {
         border: 1px solid #000000;
         padding: 6px;
      }

      .datatable3 th {
         border: 2px solid #828282;
         font-weight: bold;
         text-align: left;
         padding: 10px;
         text-align: center;
         font-size: 14px;
      }
   </style>
</head>

<body>
   <b style="font-size:14px;">
      PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
      <br>
      REKAP TANDA TANGAN<br>
      PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
      <br>
      @if ($salesman != null)
         SALESMAN {{ strtoupper($salesman->nama_karyawan) }}
      @else
         SEMUA SALESMAN
      @endif
      <br />
   </b>
   <table class="datatable3">
      <thead bgcolor="#024a75" style="color:white; font-size:12;">
         <tr bgcolor="#024a75" style="color:white; font-size:12;">
            <th>No.</th>
            <th>Kode Pelanggan</th>
            <th>Nama Pelanggan</th>
            <th>Tanda Tangan</th>
         </tr>
      </thead>
      <tbody>
         @php
            $ada = 0;
            $tidakada = 0;
            $total = 0;
            $no = 1;
         @endphp
         @foreach ($rekaptandatangan as $d)
            @php
               $total += 1;
            @endphp
            <tr>
               <td>{{ $loop->iteration }}</td>
               <td>{{ $d->kode_pelanggan }}</td>
               <td>{{ $d->nama_pelanggan }}</td>
               <td style="font-weight: bold" align="center">
                  @if ($d->cek > 0)
                     @php
                        $ada += 1;
                     @endphp
                     &#x2713;
                  @else
                     @php
                        $tidakada += 1;
                     @endphp
                     <span style="color:red">&#x2717;</span>
                  @endif
               </td>
            </tr>
            @php
               $no++;
            @endphp
         @endforeach
      </tbody>
      <tfoot>
         <tr>
            <th colspan="3">SUDAH ADA TANDA TANGAN</th>
            <th>{{ $ada . '/' . $total }} ({{ ROUND(($ada / $total) * 100) }}%)</th>
         </tr>
         <tr>
            <th colspan="3">BELUM ADA TANDA TANGAN</th>
            <th>{{ $tidakada . '/' . $total }} ({{ ROUND(($tidakada / $total) * 100) }}%)</th>
         </tr>
      </tfoot>
   </table>
</body>

</html>
