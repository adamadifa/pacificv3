@foreach ($lpc as $d)
<tr>
    <td>{{$loop->iteration}}</td>
    <td>{{$d->kode_cabang}}</td>
    <td>{{$bln[$d->bulan]}}</td>
    <td>{{$d->tahun}}</td>
    <td>{{date("d-m-Y",strtotime($d->tgl_lpc))}}</td>
    <td>{{date("H:i:s",strtotime($d->jam_lpc))}}</td>
    <td>
        @if ($d->status==0)
        <span class="badge bg-warning"><i class="fa fa-history"></i> Pending</span>
        @elseif ($d->status==1)
        <span class="badge bg-success"><i class="fa fa-check"></i> Diterima</span>
        @endif
    </td>
    <td>
        @if($d->status!=1)
        @if (in_array($level,$kirimlpc_edit))
        <a class="ml-1 edit" href="#" kode_lpc="{{ $d->kode_lpc }}"><i class="feather icon-edit success"></i></a>
        @endif
        @if (in_array($level,$kirimlpc_hapus))
        <a class="ml-1 hapus" href="#" kode_lpc="{{ $d->kode_lpc }}"><i class="feather icon-trash danger"></i></a>
        @endif
        @endif


        @if (in_array($level,$kirimlpc_approve))
        @if ($d->status==0)
        <a class="ml-1 approve" kode_lpc="{{ $d->kode_lpc }}" href="#"><i class=" feather icon-check info"></i></a>
        @elseif($d->status==1)
        <a class="ml-1 cancel" kode_lpc="{{ $d->kode_lpc }}" href="#"><i class=" fa fa-close danger"></i></a>
        @endif
        @endif
    </td>
</tr>
@endforeach

<script>
    $(function() {
        function loadlpc() {
            var tahun = $("#tahun").val();
            var bulan = $("#bulan").val();
            $.ajax({
                type: 'POST'
                , url: '/lpc/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadlpc").html(respond);
                }
            });
        }
        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_lpc = $(this).attr("kode_lpc");
            event.preventDefault();
            swal({
                    title: `Anda Yakin Data ini Akan Dihapus ?`
                    , text: "Jika dihapus Data Ini Akan Hilang Dari Keranjang"
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'POST'
                            , url: '/lpc/delete'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_lpc: kode_lpc
                            }
                            , cache: false
                            , success: function(respond) {
                                if (respond == 0) {
                                    swal(
                                        'Deleted!'
                                        , 'Data Berhasil Dihapus'
                                        , 'success'
                                    )

                                    loadlpc();
                                } else {
                                    swal(
                                        'Failed!'
                                        , 'Data Gagal Dihapus'
                                        , 'danger'
                                    )
                                }

                            }
                        });
                    }
                });
        });

        $(".edit").click(function(e) {
            var kode_lpc = $(this).attr("kode_lpc");
            $.ajax({
                type: 'POST'
                , url: '/lpc/edit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_lpc: kode_lpc
                }
                , cache: false
                , success: function(respond) {
                    $("#loadeditlpc").html(respond);
                    $('#mdleditlpc').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                }
            });
        });


        $(".approve").click(function(e) {
            e.preventDefault();
            var kode_lpc = $(this).attr("kode_lpc");
            $.ajax({
                type: 'POST'
                , url: '/lpc/approve'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_lpc: kode_lpc
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 0) {
                        swal("Berhasil ", "Data Berhasil di Approve", "success");
                    } else {
                        swal("Gagal", "Data Gagal di Approve", "error");
                    }
                    loadlpc();
                }
            });
        });

        $(".cancel").click(function(e) {
            e.preventDefault();
            var kode_lpc = $(this).attr("kode_lpc");
            $.ajax({
                type: 'POST'
                , url: '/lpc/cancel'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_lpc: kode_lpc
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 0) {
                        swal("Berhasil ", "Data Berhasil di Cancel", "success");
                    } else {
                        swal("Gagal", "Data Gagal di Cancel", "error");
                    }
                    loadlpc();
                }
            });
        });
    });

</script>
