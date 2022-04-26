@foreach ($detail as $d)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->kode_barang }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td>{{ $d->keterangan }}</td>
    <td class="text-right">{{ desimal($d->qty) }}</td>
    <td>
        <div class="btn-group" role="group" aria-label="Basic example">

            <a href="#" kode_barang="{{ $d->kode_barang }}" nobukti_pemasukan="{{ $d->nobukti_pemasukan }}" class="edit"><i class="feather icon-edit success"></i></a>
            <a href="#" kode_barang="{{ $d->kode_barang }}" nobukti_pemasukan="{{ $d->nobukti_pemasukan }}" class="hapus ml-1"><i class="feather icon-trash danger"></i></a>
        </div>
    </td>
</tr>
@endforeach
<script>
    $(function() {
        $(".edit").click(function(e) {
            var nobukti_pemasukan = $(this).attr("nobukti_pemasukan");
            var kode_barang = $(this).attr("kode_barang");
            var cektutuplaporan = $("#cektutuplaporan").val();
            if (cektutuplaporan > 0) {
                swal("Oops", "Laporan Periode Ini Sudah Ditutup !", "warning");
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/pemasukanproduksi/editbarang'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , nobukti_pemasukan: nobukti_pemasukan
                        , kode_barang: kode_barang
                    }
                    , cache: false
                    , success: function(respond) {
                        $("#loadedit").html(respond);
                    }
                });
                $('#mdledit').modal({
                    backdrop: 'static'
                    , keyboard: false
                });
            }
        });

        function cekbarang() {
            var nobukti_pemasukan = $("#nobukti_pemasukan").val();
            $.ajax({
                type: 'POST'
                , url: '/pemasukanproduksi/cekbarang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pemasukan: nobukti_pemasukan
                }
                , cache: false
                , success: function(respond) {
                    $("#cekbarang").val(respond);
                }
            });
        }


        function loaddetail() {
            var nobukti_pemasukan = $("#no_bukti").val();
            $("#loaddetailpemasukan").load("/pemasukanproduksi/" + nobukti_pemasukan + "/showbarang");
            cekbarang();
        }

        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_barang = $(this).attr("kode_barang");
            var nobukti_pemasukan = $("#nobukti_pemasukan").val();
            event.preventDefault();
            swal({
                    title: `Anda Yakin Data ini Akan Dihapus ?`
                    , text: "Jika dihapus Data Ini Akan Hilang "
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        var cektutuplaporan = $("#cektutuplaporan").val();
                        if (cektutuplaporan > 0) {
                            swal("Oops", "Laporan Periode Ini Sudah Di Tutup !", "warning");
                        } else {
                            $.ajax({
                                type: 'POST'
                                , url: '/pemasukanproduksi/deletebarang'
                                , data: {
                                    _token: "{{ csrf_token() }}"
                                    , nobukti_pemasukan: nobukti_pemasukan
                                    , kode_barang: kode_barang
                                }
                                , cache: false
                                , success: function(respond) {
                                    if (respond == 0) {
                                        swal(
                                            'Deleted!'
                                            , 'Data Berhasil Dihapus'
                                            , 'success'
                                        )
                                        loaddetail();
                                    } else {
                                        swal(
                                            'Deleted!'
                                            , 'Data Gagal Dihapus'
                                            , 'danger'
                                        )
                                    }
                                }
                            });
                        }

                    }
                });
        });
    });

</script>
