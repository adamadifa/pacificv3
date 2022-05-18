@foreach ($detail as $d)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->kode_barang }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td>{{ $d->keterangan }}</td>
    <td class="text-right">{{ desimal($d->qty_unit) }}</td>
    <td class="text-right">{{ desimal($d->qty_berat) }}</td>
    <td class="text-right">{{ desimal($d->qty_lebih) }}</td>
    <td>
        <div class="btn-group" role="group" aria-label="Basic example">

            <a href="#" id="{{ $d->id }}" class="edit"><i class="feather icon-edit success"></i></a>
            <a href="#" id="{{ $d->id }}" class="hapus ml-1"><i class="feather icon-trash danger"></i></a>
        </div>
    </td>
</tr>
@endforeach
<script>
    $(function() {
        $(".edit").click(function(e) {
            var id = $(this).attr("id");
            var cektutuplaporan = $("#cektutuplaporan").val();
            if (cektutuplaporan > 0) {
                swal("Oops", "Laporan Periode Ini Sudah Ditutup !", "warning");
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/pengeluarangudangbahan/editbarang'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , id: id

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
            var nobukti_pengeluaran = $("#nobukti_pengeluaran").val();
            $.ajax({
                type: 'POST'
                , url: '/pengeluarangudangbahan/cekbarang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pengeluaran: nobukti_pengeluaran
                }
                , cache: false
                , success: function(respond) {
                    $("#cekbarang").val(respond);
                }
            });
        }

        function loaddetail() {
            var nobukti_pengeluaran = $("#no_bukti").val();
            $("#loaddetailpengeluaran").load("/pengeluarangudangbahan/" + nobukti_pengeluaran + "/showbarang");
            cekbarang();
        }




        $(".hapus").click(function(e) {
            e.preventDefault();
            var id = $(this).attr("id");
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
                                , url: '/pengeluarangudangbahan/deletebarang'
                                , data: {
                                    _token: "{{ csrf_token() }}"
                                    , id: id
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
