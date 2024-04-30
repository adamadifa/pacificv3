@php
<<<<<<< HEAD
   $no = 1;
@endphp
@foreach ($detail as $d)
   @php
      $qtysaldoawal = $d->qtysaldoawal;
      $qtypemasukan = $d->qtypemasukan;
      $qtypengeluaran = $d->qtypengeluaran;
      $hasilqty = $qtysaldoawal + $qtypemasukan - $qtypengeluaran;

      $qtyrata = $d->qtysaldoawal + $d->qtypemasukan;
      if (!empty($qtyrata)) {
          $qtyrata = $d->qtysaldoawal + $d->qtypemasukan;
      } else {
          $qtyrata = 1;
      }

      if (empty($d->hargasaldoawal) and $d->hargasaldoawal == 0) {
          $hasilharga = $d->hargapemasukan;
      } elseif (empty($d->hargapemasukan) and $d->hargapemasukan == 0) {
          $hasilharga = $d->hargasaldoawal;
      } else {
          $hasilharga = ($d->totalsa * 1 + $d->totalpemasukan * 1) / $qtyrata;
      }

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
         <td class="text-right">
            <input type="hidden" name="harga[]" value="{{ !empty($hasilharga) ? ROUND($hasilharga, 2) : 0 }}">
            {{ desimal($hasilharga) }}
         </td>
         <td class="text-right">{{ desimal($hasilqty * $hasilharga) }}</td>

      </tr>
      @php
         $no++;
      @endphp
   @endif
@endforeach
<tr>
   <td colspan="4"><input type="hidden" name="jumlahdata" id="jumlahdata" value="{{ $no - 1 }}"></td>
=======
    $no = 1;
@endphp
@foreach ($detail as $d)
    @php
        $qtysaldoawal = $d->qtysaldoawal;
        $qtypemasukan = $d->qtypemasukan;
        $qtypengeluaran = $d->qtypengeluaran;
        $hasilqty = $qtysaldoawal + $qtypemasukan - $qtypengeluaran;

        $qtyrata = $d->qtysaldoawal + $d->qtypemasukan;
        if (!empty($qtyrata)) {
            $qtyrata = $d->qtysaldoawal + $d->qtypemasukan;
        } else {
            $qtyrata = 1;
        }

        if (empty($d->hargasaldoawal) and $d->hargasaldoawal == 0) {
            $hasilharga = $d->hargapemasukan;
        } elseif (empty($d->hargapemasukan) and $d->hargapemasukan == 0) {
            $hasilharga = $d->hargasaldoawal;
        } else {
            $hasilharga = ($d->totalsa * 1 + $d->totalpemasukan * 1) / $qtyrata;
        }

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
            <td class="text-right">
                <input type="hidden" name="harga[]" value="{{ !empty($hasilharga) ? ROUND($hasilharga, 2) : 0 }}">
                {{ desimal($hasilharga) }}
            </td>
            <td class="text-right">{{ desimal($hasilqty * $hasilharga) }}</td>

        </tr>
        @php
            $no++;
        @endphp
    @endif
@endforeach
<tr>
    <td colspan="4"><input type="hidden" name="jumlahdata" id="jumlahdata" value="{{ $no - 1 }}"></td>
>>>>>>> 04cb5f87b69d039b12d171b16c76f20380ad6b5b
</tr>
