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
         </table>
      </td>
      <td colspan="6" align="left">
         @if (in_array($faktur->kode_pelanggan, $pelangganmp))
            <b>CV MAKMUR PERMATA </b><br>
            <b>Jln. Perintis Kemerdekaan RT 001 / RW 003 Kelurahan Karsamenak Kecamatan Kawalu Kota Tasikmalaya
               46182 <br>
               NPWP : 863860342425000</b>
         @else
            @if ($faktur->tgltransaksi < '2024-03-01')
               <b>
                  <b>CV PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}</b><br>
                  <b>{{ $faktur->alamat_cabang }}</b>
               </b>
            @else
               @php
                  $pelanggan_pwt = [
                      'PWT-00272',
                      'PWT-00635',
                      'PWT-05149',
                      'PWT-02008',
                      'PWT-02582',
                      'PWT-02260',
                      'PWT-02244',
                      'PWT-02226',
                      'PWT-02258',
                      'PWT-04420',
                      'PWT-02002',
                      'PWT-03702',
                      'PWT-05155',
                      'PWT-02871',
                      'PWT-04512',
                  ];
               @endphp
               @if ($faktur->tgltransaksi <= '2024-03-14' && in_array($faktur->kode_pelanggan, $pelanggan_pwt))
                  <b>CV MAKMUR PERMATA </b><br>
                  <b>Jln. Perintis Kemerdekaan RT 001 / RW 003 Kelurahan Karsamenak Kecamatan Kawalu Kota
                     Tasikmalaya
                     46182 <br>
                     NPWP : 863860342425000</b>
               @else
                  <b>
                     <b>{{ strtoupper($cabang->nama_pt) }}</b><br>
                     <b>{{ $cabang->alamat_cabang }}</b>
                  </b>
               @endif

            @endif

         @endif


      </td>
   </tr>
   <tr>
      <td colspan="7" align="center">
         <hr>
      </td>
   </tr>
   <tr>
      <td width="15%">Tgl Faktur</td>
      <td width="1%">:</td>
      <td width="40%">{{ DateToIndo2($faktur->tgltransaksi) }}</td>
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
                  <tr>
                     <td align="center">{{ $loop->iteration }}</td>
                     <td>{{ $b->kode_barang }}</td>
                     <td>{{ $b->nama_barang }}</td>
                     <td align="right">{{ rupiah($b->harga_dus) }}</td>
                     <td align="center">{{ $jmldus }}</td>
                     <td align="center">{{ $jmlpack }}</td>
                     <td align="center">{{ $jmlpcs }}</td>
                     <td align="right">{{ rupiah($b->subtotal) }}</td>
                  </tr>
               @endforeach
            </tbody>
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
            @if (Auth::user()->kode_cabang == 'BDG' || Auth::user()->kode_cabang == 'PCF')
               <tr>
                  <td colspan="4"></td>
                  <td colspan="3" align="center">Terbilang</td>
                  <td align="right"><i>{{ ucwords(terbilang($faktur->total)) }}</i></td>
               </tr>
            @endif
         </table>
      </td>
   </tr>
</table>
<div style="page-break-before:always;"></div>
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
                     <b>CV PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}</b><br>
                     <b>{{ $faktur->alamat_cabang }}</b>
                  </b>
               @else
                  @php
                     $pelanggan_pwt = [
                         'PWT-00272',
                         'PWT-00635',
                         'PWT-05149',
                         'PWT-02008',
                         'PWT-02582',
                         'PWT-02260',
                         'PWT-02244',
                         'PWT-02226',
                         'PWT-02258',
                         'PWT-04420',
                         'PWT-02002',
                         'PWT-03702',
                         'PWT-05155',
                         'PWT-02871',
                         'PWT-04512',
                     ];
                  @endphp
                  @if ($faktur->tgltransaksi <= '2024-03-14' && in_array($faktur->kode_pelanggan, $pelanggan_pwt))
                     <b>CV MAKMUR PERMATA </b><br>
                     <b>Jln. Perintis Kemerdekaan RT 001 / RW 003 Kelurahan Karsamenak Kecamatan Kawalu Kota
                        Tasikmalaya
                        46182 <br>
                        NPWP : 863860342425000</b>
                  @else
                     <b>
                        <b>{{ strtoupper($cabang->nama_pt) }}</b><br>
                        <b>{{ $cabang->alamat_cabang }}</b>
                     </b>
                  @endif
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
   <tr>
      <td width="15%">Tgl Faktur</td>
      <td width="1%">:</td>
      <td width="40%">{{ DateToIndo2($faktur->tgltransaksi) }}</td>
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
                  <tr>
                     <td align="center">{{ $loop->iteration }}</td>
                     <td>{{ $b->kode_barang }}</td>
                     <td>{{ $b->nama_barang }}</td>
                     <td align="right">{{ rupiah($b->harga_dus) }}</td>
                     <td align="center">{{ $jmldus }}</td>
                     <td align="center">{{ $jmlpack }}</td>
                     <td align="center">{{ $jmlpcs }}</td>
                     <td align="right">{{ rupiah($b->subtotal) }}</td>
                  </tr>
               @endforeach
            </tbody>
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
               <td colspan="3" align="center">Total </td>
               <td align="right">{{ rupiah($faktur->total) }}</td>
            </tr>
            @if (Auth::user()->kode_cabang == 'BDG' || Auth::user()->kode_cabang == 'PCF')
               <tr>
                  <td colspan="4"></td>
                  <td colspan="3" align="center">Terbilang</td>
                  <td align="right"><i>{{ ucwords(terbilang($faktur->total)) }}</i></td>
               </tr>
            @endif
         </table>
      </td>
   </tr>
</table>
<div style="page-break-before:always;"></div>
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
         </table>
      </td>
      <td colspan="6" align="left">
         @if (in_array($faktur->kode_pelanggan, $pelangganmp))
            <b>CV MAKMUR PERMATA </b><br>
            <b>Jln. Perintis Kemerdekaan RT 001 / RW 003 Kelurahan Karsamenak Kecamatan Kawalu Kota Tasikmalaya
               46182 <br>
               NPWP : 863860342425000</b>
         @else
            @if ($faktur->tgltransaksi < '2024-03-01')
               <b>
                  <b>CV PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}</b><br>
                  <b>{{ $faktur->alamat_cabang }}</b>
               </b>
            @else
               @php
                  $pelanggan_pwt = [
                      'PWT-00272',
                      'PWT-00635',
                      'PWT-05149',
                      'PWT-02008',
                      'PWT-02582',
                      'PWT-02260',
                      'PWT-02244',
                      'PWT-02226',
                      'PWT-02258',
                      'PWT-04420',
                      'PWT-02002',
                      'PWT-03702',
                      'PWT-05155',
                      'PWT-02871',
                      'PWT-04512',
                  ];
               @endphp
               @if ($faktur->tgltransaksi <= '2024-03-14' && in_array($faktur->kode_pelanggan, $pelanggan_pwt))
                  <b>CV MAKMUR PERMATA </b><br>
                  <b>Jln. Perintis Kemerdekaan RT 001 / RW 003 Kelurahan Karsamenak Kecamatan Kawalu Kota
                     Tasikmalaya
                     46182 <br>
                     NPWP : 863860342425000</b>
               @else
                  <b>
                     <b>{{ strtoupper($cabang->nama_pt) }}</b><br>
                     <b>{{ $cabang->alamat_cabang }}</b>
                  </b>
               @endif

            @endif

         @endif


      </td>
   </tr>
   <tr>
      <td colspan="7" align="center">
         <hr>
      </td>
   </tr>
   <tr>
      <td width="15%">Tgl Faktur</td>
      <td width="1%">:</td>
      <td width="40%">{{ DateToIndo2($faktur->tgltransaksi) }}</td>
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
               </tr>
               <tr>
                  <th>DUS</th>
                  <th>PACK</th>
                  <th>PCS</th>
               </tr>
            </thead>
            <tbody>
               @php
                  $grandtotal = 0;
               @endphp
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

                     $harga_dus_dpp = (100 / 111) * $b->harga_dus;
                     $harga_pack_dpp = (100 / 111) * $b->harga_pack;
                     $harga_pcs_dpp = (100 / 111) * $b->harga_pcs;
                     $subtotal_dus_dpp = $jmldus * $harga_dus_dpp;
                     $subtotal_pack_dpp = $jmlpack * $harga_pack_dpp;
                     $subtotal_pcs_dpp = $jmlpcs * $harga_pcs_dpp;
                     $subtotal = $subtotal_dus_dpp + $subtotal_pcs_dpp + $subtotal_pcs_dpp;
                     $grandtotal += $subtotal;
                     $potongan_dpp = (100 / 111) * $faktur->potongan;
                     $dpp = $subtotal - $potongan_dpp - $faktur->penyharga - $faktur->potistimewa;
                     $ppn = (11 / 100) * $dpp;
                  @endphp
                  <tr>
                     <td align="center">{{ $loop->iteration }}</td>
                     <td>{{ $b->kode_barang }}</td>
                     <td>{{ $b->nama_barang }}</td>
                     <td align="right">{{ rupiah($harga_dus_dpp) }}</td>
                     <td align="center">{{ $jmldus }}</td>
                     <td align="center">{{ $jmlpack }}</td>
                     <td align="center">{{ $jmlpcs }}</td>
                     <td align="right">{{ rupiah($subtotal) }}</td>
                  </tr>
               @endforeach
            </tbody>
            <tr>
               <td colspan="4"></td>
               <td colspan="3" align="center">Jumlah</td>
               <td align="right">{{ rupiah($grandtotal) }}</td>
            </tr>
            <tr>
               <td colspan="4"></td>
               <td colspan="3" align="center">Diskon</td>
               <td align="right">{{ rupiah($potongan_dpp) }}</td>
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

            <tr>
               <td colspan="4"></td>
               <td colspan="3" align="center">DPP</td>
               <td align="right">
                  {{ rupiah($dpp) }}
               </td>
            </tr>
            <tr>
               <td colspan="4"></td>
               <td colspan="3" align="center">PPN</td>
               <td align="right">{{ rupiah($ppn) }}</td>
            </tr>

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
            @if (Auth::user()->kode_cabang == 'BDG' || Auth::user()->kode_cabang == 'PCF')
               <tr>
                  <td colspan="4"></td>
                  <td colspan="3" align="center">Terbilang</td>
                  <td align="right"><i>{{ ucwords(terbilang($faktur->total)) }}</i></td>
               </tr>
            @endif
         </table>
      </td>
   </tr>
</table>
