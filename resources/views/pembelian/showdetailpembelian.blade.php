@php
$grandtotal = 0;
$jmldata = 1;
@endphp
@foreach ($detail as $d)
@php
$subtotal = $d->harga * $d->qty;
$total = $subtotal + $d->penyesuaian;
$grandtotal += $total;
@endphp
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->kode_barang }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td>{{ $d->keterangan }}</td>
    <td class="text-center">{{ desimal($d->qty) }}</td>
    <td class="text-right">{{ desimal($d->harga) }}</td>
    <td class="text-right">{{ desimal($subtotal) }}</td>
    <td class="text-right">{{ desimal($d->penyesuaian) }}</td>
    <td class="text-right">{{ desimal($total) }}</td>
    <td class="text-center">{{ $d->kode_akun }}</td>
    <td class="text-center">{{ $d->kode_cabang }}</td>
    <td>
        <div class="btn-group" role="group" aria-label="Basic example">
            <a href="#" class="info ml-1 edit" nobukti_pembelian="{{ $d->nobukti_pembelian }}" kode_barang="{{ $d->kode_barang }}" no_urut="{{ $d->no_urut }}"><i class="feather icon-edit"></i></a>
            <a href="#" class="ml-1 danger  hapus" nobukti_pembelian="{{ $d->nobukti_pembelian }}" kode_barang="{{ $d->kode_barang }}" no_urut="{{ $d->no_urut }}"><i class="feather icon-trash"></i></a>
        </div>
    </td>
</tr>
@php
$jmldata++;
@endphp
@endforeach
<tr class="thead-dark">
    <th colspan="8" style="font-size: 14px">TOTAL</th>
    <th class="text-right" style="font-size: 14px" id="grandtotaltemp">{{ desimal($grandtotal) }}<input type="hidden" id="jmldata" value="{{ $jmldata -1 }}"></th>
    <th colspan="3"></th>
</tr>

<script>
    $(function() {

        function addCommas(nStr) {
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }

        function loadtotal() {
            var grandtotal = $("#grandtotaltemp").text();
            var grandtotalpotongan = $("#grandtotalpotongan").text();

            if (grandtotal.length === 0) {
                var grandtotal_1 = 0;
                var grandtotal_2 = 0;
            } else {
                var grandtotal_1 = grandtotal.replace(/\./g, '');
                var grandtotal_2 = grandtotal_1.replace(/\,/g, '.');

            }

            if (grandtotalpotongan.length === 0) {
                var grandtotalpotongan_1 = 0;
                var grandtotalpotongan_2 = 0;
            } else {
                var grandtotalpotongan_1 = grandtotalpotongan.replace(/\./g, '');
                var grandtotalpotongan_2 = grandtotalpotongan_1.replace(/\,/g, '.');

            }


            var grandAll = parseFloat(grandtotal_2) - parseFloat(grandtotalpotongan_2);
            var total_1 = addCommas(grandAll.toFixed(2));
            var total_2 = total_1.replace(/\./g, '-');
            var total_3 = total_2.replace(/\,/g, '.');
            var total_4 = total_3.replace(/\-/g, ',');
            $("#grandtotal").text(total_4);
        }

        function loaddetailpembelian() {
            var nobukti_pembelian = $("#nobukti_pembelian").val();
            $.ajax({
                type: 'POST'
                , url: '/pembelian/showdetailpembelian'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pembelian: nobukti_pembelian
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailpembelian").html(respond);
                    loadtotal();
                }
            });
        }


        $('.edit').click(function(e) {
            e.preventDefault();
            var nobukti_pembelian = $(this).attr("nobukti_pembelian");
            var kode_barang = $(this).attr("kode_barang");
            var no_urut = $(this).attr("no_urut");
            var cektutuplaporan = $("#cektutuplaporan").val();
            // if ($('.cabangcheck').is(':checked')) {
            //     alert('test');
            // }
            if (cektutuplaporan > 0) {
                swal({
                    title: 'Oops'
                    , text: 'Laporan Periode Ini Sudah Ditutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
            } else {
                $('#mdleditbarang').modal({
                    backdrop: 'static'
                    , keyboard: false
                });
                $.ajax({
                    type: 'POST'
                    , url: '/pembelian/editbarang'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , nobukti_pembelian: nobukti_pembelian
                        , kode_barang: kode_barang
                        , no_urut: no_urut
                    }
                    , cache: false
                    , success: function(respond) {
                        $("#loadeditbarang").html(respond);
                    }
                });
            }
        });
        $(".hapus").click(function(e) {
            e.preventDefault();
            var nobukti_pembelian = $(this).attr("nobukti_pembelian");
            var kode_barang = $(this).attr("kode_barang");
            var no_urut = $(this).attr("no_urut");
            var cekpembayaran = "{{ $cekpembayaran }}";
            event.preventDefault();
            if (cekpembayaran > 0) {
                swal("Oops", "Data Ini Sudah Melakukan Pembayaran, Tidak dapat Dihapus, Hubungi Bagian Keuangan Untuk Konfirmasi", "warning");
            } else {
                swal({
                        title: `Anda Yakin Data ini Akan Dihapus ?`
                        , text: "Jika dihapus Data Ini Akan Hilang Dari Keranjang"
                        , icon: "warning"
                        , buttons: true
                        , dangerMode: true
                    , })
                    .then((willDelete) => {
                        if (willDelete) {
                            var cektutuplaporan = $("#cektutuplaporan").val();
                            if (cektutuplaporan > 0) {
                                swal("Oops", "Laporan Periode Ini Sudah Di Tutup !", "warning");
                                return false;
                            } else {
                                $.ajax({
                                    type: 'POST'
                                    , url: '/pembelian/deletedetail'
                                    , data: {
                                        _token: "{{ csrf_token() }}"
                                        , nobukti_pembelian: nobukti_pembelian
                                        , kode_barang: kode_barang
                                        , no_urut: no_urut
                                    }
                                    , cache: false
                                    , success: function(respond) {
                                        if (respond == 0) {
                                            swal(
                                                'Deleted!'
                                                , 'Data Berhasil Dihapus'
                                                , 'success'
                                            )
                                        } else {
                                            swal(
                                                'Deleted!'
                                                , 'Data Gagal Dihapus'
                                                , 'danger'
                                            )
                                        }

                                        loaddetailpembelian();
                                    }
                                });
                            }




                        }
                    });

            }


        });
    });

</script>
