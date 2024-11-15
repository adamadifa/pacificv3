<style>
   body {
      letter-spacing: 0px;
      font-family: Calibri;
      font-size: 14px;
   }

   table {
      font-family: Tahoma;
      font-size: 14px;
   }

   .garis5,
   .garis5 td,
   .garis5 tr,
   .garis5 th {
      border: 2px solid black;
      border-collapse: collapse;
   }

   .table {
      border: solid 1px #000000;
      width: 100%;
      font-size: 12px;
      margin: auto;
   }

   .table th {
      border: 1px #000000;
      font-size: 12px;

      font-family: Arial;
   }

   .table td {
      border: solid 1px #000000;
   }
</style>
<table border="0" width="100%">
   <tr>
      <td style="width:150px">
         <table class="garis5">
            <tr>
               <td>FAKTUR</td>
            </tr>
            <tr>
               <td>NOMOR {{ $faktur->no_fak_penj }}</td>
            </tr>
            @if (!empty($faktur->no_po))
               <tr>
                  <td>PO : {{ $faktur->no_po }}</td>
               </tr>
            @endif
         </table>
      </td>
      <td colspan="6" align="left">
         <b>
            @if (in_array($faktur->kode_pelanggan, $pelangganmp))
               <b>CV MAKMUR PERMATA </b><br>
               <b>Jln. Perintis Kemerdekaan RT 001 / RW 003 Kelurahan Karsamenak Kecamatan Kawalu Kota Tasikmalaya
                  46182 <br>
                  NPWP : 863860342425000</b>
            @else
               @if ($faktur->tgltransaksi < '2024-03-01')
                  <b>
                     <b>CV PACIFIC CABANG {{ strtoupper($faktur->nama_cabang) }}</b><br>
                     <b>{{ $faktur->alamat_cabang }}</b>
                  </b>
               @else
                  <b>
                     <b>{{ strtoupper($cabang->nama_pt) }}</b><br>
                     <b>{{ $cabang->alamat_cabang }}</b>
                  </b>
               @endif
            @endif
         </b>
      </td>
   </tr>
   <tr>
      <td colspan="7" align="center">
         <hr>
      </td>
   </tr>
   @if ($faktur->kode_cabang == 'BDG')
      <tr>
         <td width="10%">Tgl Faktur</td>
         <td width="1%">:</td>
         <td width="25%">{{ DateToIndo2($faktur->tgltransaksi) }}</td>
         <td>Nama Customer</td>
         <td>:</td>
         <td><b>{{ $faktur->kode_pelanggan }}</b> - {{ $faktur->nama_pelanggan }} ({{ $faktur->no_hp }})</td>
      </tr>
      <tr>
         <td>Jenis Transaksi</td>
         <td>:</td>
         <td>{{ strtoupper($faktur->jenistransaksi) }}
            {{ $faktur->jenistransaksi == 'kredit' ? '( JT : ' . DateToIndo2(date('Y-m-d', strtotime('14 day', strtotime($faktur->tgltransaksi)))) . ')' : '' }}
         </td>
         <td>Salesman</td>
         <td>:</td>
         <td><b>{{ $faktur->id_karyawan }}</b> - {{ $faktur->nama_karyawan }}</td>
      </tr>
      <tr>
         <td>Pola Operasi</td>
         <td>:</td>
         <td> {{ $faktur->kategori_salesman }}</td>
         <td>Alamat</td>
         <td>:</td>
         <td>
            @if (!empty($faktur->alamat_toko))
               {{ $faktur->alamat_toko }}
            @else
               {{ $faktur->alamat_pelanggan }}
            @endif
            @if ($faktur->kode_cabang == 'BDG')
               ({{ $faktur->pasar }})
            @endif
         </td>
      </tr>
   @else
      <tr>
         <td width="10%">Tgl Faktur</td>
         <td width="1%">:</td>
         <td width="25%">{{ DateToIndo2($faktur->tgltransaksi) }}</td>
         <td>Nama Customer</td>
         <td>:</td>
         <td>{{ $faktur->nama_pelanggan }}</td>
      </tr>
      <tr>
         <td>Jenis Transaksi</td>
         <td>:</td>
         <td>{{ strtoupper($faktur->jenistransaksi) }}</td>
         <td>Alamat</td>
         <td>:</td>
         <td>
            @if (!empty($faktur->alamat_toko))
               {{ $faktur->alamat_toko }}
            @else
               {{ $faktur->alamat_pelanggan }}
            @endif
            @if ($faktur->kode_cabang == 'BDG')
               ({{ $faktur->pasar }})
            @endif
         </td>
      </tr>
   @endif


   <tr>
      <td colspan="7">

         <table class="garis5" width="100%">
            <thead>
               <tr style="padding: 10px">
                  <th rowspan="2">NO</th>
                  <th rowspan="2">KODE BARANG</th>
                  <th rowspan="2">NAMA BARANG</th>
                  <th rowspan="2">HARGA</th>
                  <th colspan="3">JUMLAH</th>
                  <th rowspan="2">TOTAL</th>
                  @if ($faktur->kode_cabang == 'BDG')
                     <th rowspan="2">Keterangan</th>
                  @endif
               </tr>
               <tr>
                  <th>DUS</th>
                  <th>PACK</th>
                  <th>PCS</th>
               </tr>
            </thead>
            <tbody>

               @foreach ($detail as $b)
                  @php
                     $jmldus = floor($b->jumlah / $b->isipcsdus);
                     $sisadus = $b->jumlah % $b->isipcsdus;
                     if ($b->isipack == 0) {
                         $jmlpack = 0;
                         $sisapack = $sisadus;
                     } else {
                         $jmlpack = floor($sisadus / $b->isipcs);
                         $sisapack = $sisadus % $b->isipcs;
                     }
                     $jmlpcs = $sisapack;
                  @endphp


                  <tr style="padding:  10px">
                     <td align="center">{{ $loop->iteration }}</td>
                     <td>{{ $b->kode_barang }}</td>
                     <td>{{ $b->nama_barang }}</td>
                     <td align="right">{{ rupiah($b->harga_dus) }}</td>
                     <td align="center"><?php echo $jmldus; ?></td>
                     <td align="center"><?php echo $jmlpack; ?></td>
                     <td align="center"><?php echo $jmlpcs; ?></td>
                     <td align="right">{{ rupiah($b->subtotal) }}</td>
                     @if ($faktur->kode_cabang == 'BDG')
                        <td></td>
                     @endif
                  </tr>
               @endforeach
               <tr>
                  <td colspan="4"></td>
                  <td colspan="3" align="center">Jumlah</td>
                  <td align="right">{{ rupiah($faktur->subtotal) }}</td>
               </tr>
               <tr>
                  <td colspan="4"></td>
                  <td colspan="3" align="center">Diskon</td>
                  <td align="right">{{ rupiah($faktur->potongan) }}</td>
               </tr>
               <?php if ($faktur->potistimewa != 0) { ?>
               <tr>
                  <td colspan="4"></td>
                  <td colspan="3" align="center">Potongan Istimewa</td>
                  <td align="right">{{ rupiah($faktur->potistimewa) }}</td>
               </tr>
               <?php } ?>
               <tr>
                  <td colspan="4"></td>
                  <td colspan="3" align="center">Penyesuaian</td>
                  <td align="right">{{ rupiah($faktur->penyharga) }}</td>
               </tr>
               <?php if (!empty($faktur->ppn)) { ?>
               <tr>
                  <td colspan="4"></td>
                  <td colspan="3" align="center">DPP</td>
                  <td align="right">
                     {{ rupiah($faktur->subtotal - $faktur->potongan - $faktur->penyharga - $faktur->potistimewa) }}
                  </td>
               </tr>
               <tr>
                  <td colspan="4"></td>
                  <td colspan="3" align="center">PPN</td>
                  <td align="right">{{ rupiah($faktur->ppn) }}</td>
               </tr>
               <?php } ?>
               <tr>
                  <td colspan="4"></td>
                  <td colspan="3" align="center">Retur</td>
                  <td align="right">{{ rupiah($faktur->totalretur) }}</td>
               </tr>
               <tr>
                  <td colspan="4"></td>
                  <td colspan="3" align="center">Total Pembayaran</td>
                  <td align="right">{{ rupiah($faktur->total) }}</td>
               </tr>
               @if ($faktur->kode_cabang == 'BDG')
                  <tr>
                     <td colspan="4"></td>
                     <td colspan="3" align="center">Terbilang</td>
                     <td align="right"><i>{{ ucwords(terbilang($faktur->total)) }}</i></td>
                  </tr>
               @endif
            </tbody>

         </table>

      </td>
   </tr>
   @if ($faktur->kode_cabang == 'BDG')
      <tr>
         <table class="garis5" width="100%">
            <tr style="font-weight:bold; text-align:center">
               <td>Dibuat</td>
               <td>Diserahkan</td>
               <td>Diterima</td>
               <td>Mengetahui</td>
               <td rowspan="3">

                  <div style="display: flex; align-items: center; height:20px;">
                     <div
                        style="width:10px; height:10px; border:1px solid black; margin-bottom:5px; margin-left:5px">
                     </div>
                     <div style="margin-left: 10px; margin-bottom:5px">Cash</div>
                  </div>
                  <div style="display: flex; align-items: center; height:20px;">
                     <div
                        style="width:10px; height:10px; border:1px solid black; margin-bottom:5px; margin-left:5px">
                     </div>
                     <div style="margin-left: 10px; margin-bottom:5px">Transfer</div>
                  </div>
                  <div style="display: flex; align-items: center; height:20px;">
                     <div
                        style="width:10px; height:10px; border:1px solid black; margin-bottom:5px; margin-left:5px">
                     </div>
                     <div style="margin-left: 10px; margin-bottom:5px">Check/Giro</div>
                  </div>
               </td>
            </tr>
            <tr style="font-weight:bold;">
               <td style="height: 40px"></td>
               <td></td>
               <td>
                  @if (Auth::user()->kode_cabang != 'SKB')
                     @if (!empty($faktur->signature))
                        @php
                           $path = Storage::url('signature/' . $faktur->signature);
                        @endphp
                        <img src="{{ url($path) }}" alt="" style="width:100px; height:100px">
                     @endif
                  @endif

               </td>
               <td></td>
            </tr>
            <tr style="font-weight:bold; text-align:center">
               <td>Penjualan</td>
               <td>Pengirim</td>
               <td>Pelanggan</td>
               <td>Pejabat Cabang</td>
            </tr>
         </table>
      </tr>
   @else
      <tr>
         <table class="garis5" width="100%">
            <tr style="font-weight:bold; text-align:center">
               <td>Dibuat</td>
               <td>Diserahkan</td>
               <td>Diterima</td>
               <td>Mengetahui</td>
               <td>Jam Masuk</td>
            </tr>
            <tr style="font-weight:bold;">
               <td rowspan="3"></td>
               <td rowspan="3" style="width:20%; text-align:center">

               </td>
               <td rowspan="3" style="width:20%; text-align:center">
                  @if (Auth::user()->kode_cabang != 'SKB')
                     @if (!empty($faktur->signature))
                        @php
                           $path = Storage::url('signature/' . $faktur->signature);
                        @endphp
                        <img src="{{ url($path) }}" alt="" style="width:100px; height:100px">
                     @endif
                  @endif
               </td>
               <td rowspan="3"></td>

            </tr>
            <tr>
               <td style="height: 20px"></td>
            </tr>
            <tr>
               <td style="font-weight:bold; text-align:center">Jam Keluar</td>
            </tr>
            <tr style="font-weight:bold; text-align:center">
               <td>Penjualan</td>
               <td>Pengirim</td>
               <td>Pelanggan</td>
               <td>Security</td>
               <td></td>
            </tr>
         </table>
      </tr>
   @endif
</table>
@if (in_array($faktur->kode_pelanggan, $pelangganmp))
   <i>
      Untuk Pembayaran bisa Melalui:<br>
      <b>Rekening BCA CV Makmur Permata No. 0543772221</b><br>
      <b>Rekening BCA CV Makmur Permata No. 0773092265</b><br>
   </i>
@endif
