@php
   $no = 1;
@endphp
@foreach ($detail as $d)
   @php
      $qtysaldoawal = $d->qtysaldoawal;
      $qtypemasukan = $d->qtypemasukan;
      $qtypengeluaran = $d->qtypengeluaran;
      $hasilqty = $qtysaldoawal + $qtypemasukan - $qtypengeluaran;

   @endphp
   @if (!empty($hasilqty))
      <tr>
         <td>{{ $no }}</td>
         <td>
            <input type="hidden" name="kode_barang[]" value="{{ $d->kode_barang }}">
            {{ $d->kode_barang }}
         </td>
         <td>{{ $d->nama_barang }}</td>
         <td class="text-right">
            <input type="hidden" name="qty[]" value="{{ !empty($hasilqty) ? ROUND($hasilqty, 2) : 0 }}">
            {{ desimal($hasilqty) }}
         </td>
      </tr>
      @php
         $no++;
      @endphp
   @endif
@endforeach
<tr>
   <td colspan="4"><input type="hidden" name="jumlahdata" id="jumlahdata" value="{{ $no - 1 }}"></td>
</tr>
