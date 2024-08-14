<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr style="text-align: center !important">
                <th rowspan="2">No.</th>
                <th rowspan="2">Kode</th>
                <th rowspan="2">Nama Barang</th>
                <th colspan="6">Quantity</th>
                <th rowspan="2">Subtotal</th>
                <th rowspan="2">Aksi</th>
            </tr>
            <tr style="text-align: center !important">
                <th>Dus</th>
                <th>Harga</th>
                <th>Pack</th>
                <th>Harga</th>
                <th>Pcs</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($detailtemp as $d)
                @php
                    $isipcsdus = $d->isipcsdus;
                    $isipack = $d->isipack;
                    $isipcs = $d->isipcs;
                    $jumlah = $d->jumlah;
                    $jumlah_dus = floor($jumlah / $isipcsdus);
                    if ($jumlah != 0) {
                        $sisadus = $jumlah % $isipcsdus;
                    } else {
                        $sisadus = 0;
                    }
                    if ($isipack == 0) {
                        $jumlah_pack = 0;
                        $sisapack = $sisadus;
                    } else {
                        $jumlah_pack = floor($sisadus / $isipcs);
                        $sisapack = $sisadus % $isipcs;
                    }

                    $jumlah_pcs = $sisapack;
                    $total += $d->subtotal;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->kode_barang }}</td>
                    <td>{{ $d->nama_barang }}</td>
                    <td class="text-center">{{ !empty($jumlah_dus) ? rupiah($jumlah_dus) : '' }}</td>
                    <td class="text-right">{{ rupiah($d->harga_dus) }}</td>
                    <td class="text-center">{{ !empty($jumlah_pack) ? rupiah($jumlah_pack) : '' }}</td>
                    <td class="text-right">{{ rupiah($d->harga_pack) }}</td>
                    <td class="text-center">{{ !empty($jumlah_pcs) ? rupiah($jumlah_pcs) : '' }}</td>
                    <td class="text-right">{{ rupiah($d->harga_pcs) }}</td>
                    <td class="text-right">{{ !empty($d->subtotal) ? rupiah($d->subtotal) : '' }}</td>
                    <td>
                        {{-- <a href="#" class="info edit" kode_barang="{{ $d->kode_barang }}"><i class=" feather icon-edit"></i></a> --}}
                        <a href="#" class="danger hapus" kode_barang="{{ $d->kode_barang }}"><i class=" feather icon-trash"></i></a>
                    </td>
                </tr>
            @endforeach
            <tr style="font-weight: bold">
                <td colspan="9">TOTAL</td>
                <td class="text-right">
                    {{ rupiah($total) }}
                    <input type="hidden" id="totaltemp" name="totaltemp" value="{{ $total }}">
                </td>
            </tr>

        </tbody>
    </table>
    <script>
        $(function() {
            function loadbarangtemp() {
                var kode_pelanggan = $("#kode_pelanggan").val();
                $.ajax({
                    type: 'POST',
                    url: '/retur/showbarangtempv2',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_pelanggan: kode_pelanggan,
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadbarangtemp").html(respond);
                        loadtotal();
                    }
                });
            }

            function loadtotal() {
                var kode_pelanggan = $("#kode_pelanggan").val();
                $.ajax({
                    type: 'POST',
                    url: '/loadtotalreturtemp',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_pelanggan: kode_pelanggan
                    },
                    success: function(respond) {
                        var total = parseInt(respond.replace(/\./g, ''));
                        $("#grandtotal").text(convertToRupiah(total));
                        // $("#total").val(convertToRupiah(grandtotal));
                        // $("#bruto").val(bruto);
                        $("#subtotal").val(total);
                        cektemp();
                    }
                });
            }

            function cektemp() {
                var kode_pelanggan = $("#kode_pelanggan").val();
                $.ajax({
                    type: 'POST',
                    url: '/cekreturtemp',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_pelanggan: kode_pelanggan
                    },
                    success: function(respond) {
                        $("#cektemp").val(respond);
                    }
                });
            }

            function convertToRupiah(number) {
                if (number) {
                    var rupiah = "";
                    var numberrev = number
                        .toString()
                        .split("")
                        .reverse()
                        .join("");
                    for (var i = 0; i < numberrev.length; i++)
                        if (i % 3 == 0) rupiah += numberrev.substr(i, 3) + ".";
                    return (
                        rupiah
                        .split("", rupiah.length - 1)
                        .reverse()
                        .join("")
                    );
                } else {
                    return number;
                }
            }

            $(".hapus").click(function(e) {
                e.preventDefault();
                var kode_barang = $(this).attr("kode_barang");
                var kode_pelanggan = $("#kode_pelanggan").val();
                event.preventDefault();
                swal({
                        title: `Anda Yakin Data ini Akan Dihapus ?`,
                        text: "Jika dihapus Data Ini Akan Hilang Dari Keranjang",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                type: 'POST',
                                url: '/retur/deletebarangtemp',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    kode_barang: kode_barang,
                                    kode_pelanggan: kode_pelanggan
                                },
                                cache: false,
                                success: function(respond) {
                                    swal(
                                        'Deleted!', 'Data Berhasil Dihapus', 'success'
                                    )
                                    loadbarangtemp();
                                }
                            });
                        }
                    });
            });
        });
    </script>
