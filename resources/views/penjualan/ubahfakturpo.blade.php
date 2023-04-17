<style>
    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

</style>
<div class="row">
    <div class="col-6">
        <table class="table table-bordered">
            <tr>
                <th>No. PO</th>
                <td>{{ $penjualan->no_fak_penj }}</td>
            </tr>
            <tr>
                <th>Tanggal PO</th>
                <td>{{ DateToIndo2($penjualan->tgltransaksi) }}</td>

            </tr>
            <tr>
                <th>Nama Pelanggan</th>
                <td>{{ $penjualan->nama_pelanggan }}</td>

            </tr>
            <tr>
                <th>Kode Pelanggan</th>
                <td>{{ $penjualan->kode_pelanggan }}</td>
            </tr>
            <tr>
                <th style="width:20%">Alamat</th>
                <td style="width: 30%">{{ ucwords(strtolower($penjualan->alamat_pelanggan)) }}</td>
            </tr>
        </table>
    </div>
    <div class="col-6">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <tr>
                        <th>Jenis Transaksi</th>
                        <td>{{ ucwords($penjualan->jenistransaksi) }}</td>
                    </tr>
                    <tr>
                        <th>ID Salesman</th>
                        <td>{{ $penjualan->id_karyawan }}</td>
                    </tr>
                    <tr>
                        <th>Nama Salesman</th>
                        <td>{{ $penjualan->nama_karyawan }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <form action="/penjualan/{{ Crypt::encrypt($penjualan->no_fak_penj) }}/updatepo" id="frmPO" method="POST">
            @csrf
            <div class="row">
                <div class="col-12">
                    <x-inputtext field="no_fak_penj_po" label="No. Faktur" value="{{ $no_fak_penj_auto }}" icon="feather icon-credit-card" />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <x-inputtext field="tgltransaksi_po" label="Tanggal Transaksi" value="{{ $penjualan->tgltransaksi }}" icon="feather icon-calendar" datepicker />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button class="btn btn-primary btn-block"><i class="feather icon-refresh-cw mr-1"></i>Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th style="text-align:center">Dus</th>
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
                $total = 0;
                @endphp
                @foreach ($detailpenjualan as $d)
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
                <tr @if ($d->promo ==1)
                    class="bg-warning"
                    @endif>

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
                    <td colspan="7">Subtotal</td>
                    <td class="text-right">{{ rupiah($total) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td colspan="7">Potongan</td>
                    <td class="text-right">{{ rupiah($penjualan->potongan) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td colspan="7">Potongan Istimewa</td>
                    <td class="text-right">{{ rupiah($penjualan->potistimewa) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td colspan="7">Penyesuaian</td>
                    <td class="text-right">{{ rupiah($penjualan->penyharga) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td colspan="7">Total</td>
                    <td class="text-right">
                        @php
                        $totalnonppn = $penjualan->subtotal - $penjualan->potongan - $penjualan->potistimewa - $penjualan->penyharga;
                        @endphp
                        {{ rupiah($totalnonppn)  }}
                    </td>
                </tr>
                <tr style="font-weight: bold">
                    <td colspan="7">PPN</td>
                    <td class="text-right">
                        {{ rupiah($penjualan->ppn)  }}
                    </td>
                </tr>
                <tr style="font-weight: bold">
                    <td colspan="7">Grand Total</td>
                    <td class="text-right">
                        {{ rupiah($penjualan->total)  }}
                    </td>
                </tr>
                <tr style="font-weight: bold">
                    <td colspan="7">Retur</td>
                    <td class="text-right">{{ rupiah($penjualan->totalretur) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td colspan="7">Jumlah Bayar</td>
                    <td class="text-right">{{ rupiah($penjualan->jmlbayar) }}</td>
                </tr>
                <tr style="font-weight: bold">
                    <td colspan="7">Sisa Bayar</td>
                    <td class="text-right">
                        @php
                        $sisabayar = $penjualan->total - $penjualan->totalretur - $penjualan->jmlbayar;
                        @endphp
                        {{ rupiah($sisabayar) }}
                    </td>
                </tr>
                <tr style="font-weight: bold">
                    <td colspan="7">Keterangan</td>
                    <td class="text-right">
                        @if ($sisabayar != 0)
                        <span class="badge bg-danger">BELUM LUNAS</span>
                        @else
                        <span class="badge bg-success">LUNAS</span>
                        @endif
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#frmPO").submit(function() {
            var no_fak_penj = $("#no_fak_penj_po").val();
            var tgltransaksi = $("#tgltransaksi_po").val();
            if (no_fak_penj == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Faktur Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_fak_penj_po").focus();
                });
                return false;
            } else if (tgltransaksi == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Transaksi Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgltransaksi_po").focus();
                });
                return false;
            }

        });
    });

</script>
