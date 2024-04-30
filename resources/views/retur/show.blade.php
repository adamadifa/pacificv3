<div class="row">
   <div class="col">
      <table class="table">
         <tr>
            <th>No. Retur</th>
            <td>{{ $retur->no_retur_penj }}</td>
         </tr>
         <tr>
            <th>No. Faktur</th>
            <td>{{ $retur->no_fak_penj }}</td>
         </tr>
         <tr>
            <th>Tanggal</th>
            <td>{{ DateToIndo2($retur->tglretur) }}</td>
         </tr>
         <tr>
            <th>Pelanggan</th>
            <td>{{ $retur->kode_pelanggan }} - {{ $retur->nama_pelanggan }}</td>
         </tr>
         <tr>
            <th>Alamat</th>
            <td>{{ $retur->alamat_pelanggan }} - {{ $retur->alamat_pelanggan }}</td>
         </tr>
         <tr>
            <th>No. HP</th>
            <td>{{ $retur->no_hp }} - {{ $retur->no_hp }}</td>
         </tr>
         <tr>
            <th>Jenis Retur</th>
            <td>{{ $retur->jenis_retur == 'pf' ? 'Potong Faktur' : 'Ganti Barang' }}</td>
         </tr>
      </table>
   </div>
</div>

<table class="table table-bordered">
   <thead>
      <tr>
         <th>Kode Produk</th>
         <th>Nama Barang</th>
         <th class="text-center">Dus</th>
         <th>Harga/Dus</th>
         <th class="text-center">Pack</th>
         <th>Harga/Pack</th>
         <th class="text-center">Pcs</th>
         <th>Harga/Pcs</th>
         <th>Total</th>
      </tr>
   </thead>
   <tbody>
      @php
         $totalpf = 0;
         $totalgb = 0;
         $total = 0;
      @endphp
      @foreach ($detail as $d)
         @php
            $jmldus = floor($d->jumlah / $d->isipcsdus);
            $sisadus = $d->jumlah % $d->isipcsdus;

            if ($d->isipack == 0) {
                $jmlpack = 0;
                $sisapack = $sisadus;
            } else {
                $jmlpack = floor($sisadus / $d->isipcs);
                $sisapack = $sisadus % $d->isipcs;
            }

            $jmlpcs = $sisapack;

            $total += $d->subtotal;

         @endphp
         <tr>

            <td>{{ $d->kode_produk }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td class="text-center">{{ $jmldus }}</td>
            <td class="text-right">{{ rupiah($d->harga_dus) }}</td>
            <td class="text-center">{{ $jmlpack }}</td>
            <td class="text-right">{{ rupiah($d->harga_pack) }}</td>
            <td class="text-center">{{ $jmlpcs }}</td>
            <td class="text-right">{{ rupiah($d->harga_pcs) }}</td>
            <td class="text-right">{{ rupiah($d->subtotal) }}</td>
         </tr>
      @endforeach
      <tr style="font-weight: bold">
         <td colspan="8">Total Retur</td>
         <td class="text-right">{{ rupiah($total) }}</td>
      </tr>
   </tbody>
</table>
<form action="/retur/storevalidasi" method="POST">
   @csrf
   <input type="hidden" name="no_retur_penj" value="{{ $retur->no_retur_penj }}">
   @foreach ($validasi_item as $d)
      <div class="row mb-1">
         <div class="col-12">
            <div class="vs-checkbox-con vs-checkbox-primary">
               <input type="checkbox" class="voucher" name="kode_item[]" value="{{ $d->kode_item }}">
               <span class="vs-checkbox">
                  <span class="vs-checkbox--check">
                     <i class="vs-icon feather icon-check"></i>
                  </span>
               </span>
               <span class="">{{ $d->item }}</span>
            </div>
         </div>
      </div>
   @endforeach
   <div class="row">
      <div class="col-12">
         <button class="btn btn-primary w-100" type="submit" name="submit"><i class="fa send mr-1"></i>Submit</button>
      </div>
   </div>
</form>
