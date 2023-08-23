@foreach ($lhp as $d)
<tr>
    <td>{{$loop->iteration}}</td>
    <td>{{$d->kode_cabang}}</td>
    <td>{{$bln[$d->bulan]}}</td>
    <td>{{$d->tahun}}</td>
    <td>{{date("d-m-Y",strtotime($d->tgl_lhp))}}</td>
    <td>{{date("H:i:s",strtotime($d->jam_lhp))}}</td>
    <td>
        @if ($d->status==0)
        <span class="badge bg-warning"><i class="fa fa-history"></i> Pending</span>
        @elseif ($d->status==1)
        <span class="badge bg-success"><i class="fa fa-check"></i> Diterima</span>
        @endif
    </td>
    <td>
        @if (!empty($d->foto))
        @php
        $path = Storage::url('lpc/'.$d->foto);
        @endphp
        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Vinnie Mostowy" class="avatar pull-up">
                <a href="{{ url($path) }}" target="_blank">
                    <img class="media-object rounded-circle" src="{{ url($path)}}" alt="Avatar" height="30" width="30">
                </a>
            </li>
        </ul>
        @endif
    </td>
    <td>
        @if($d->status!=1)
        @if (in_array($level,$kirimlpc_edit))
        {{-- <a class="ml-1 edit" href="#" kode_lhp="{{ $d->kode_lhp }}"><i class="feather icon-edit success"></i></a> --}}
        @endif
        @if (in_array($level,$kirimlpc_hapus))
        <a class="ml-1 hapus" href="#" kode_lhp="{{ $d->kode_lhp }}"><i class="feather icon-trash danger"></i></a>
        @endif
        @endif


        @if (in_array($level,$kirimlpc_approve))
        @if ($d->status==0)
        <a class="ml-1 approve" kode_lhp="{{ $d->kode_lhp }}" href="#"><i class=" feather icon-check info"></i></a>
        @elseif($d->status==1)
        <a class="ml-1 cancel" kode_lhp="{{ $d->kode_lhp }}" href="#"><i class=" fa fa-close danger"></i></a>
        @endif
        @endif
    </td>

</tr>
@endforeach

<script>
    $(function() {
        function loadlhp() {
            var tahun = $("#tahun").val();
            var bulan = $("#bulan").val();
            $.ajax({
                type: 'POST'
                , url: '/lhp/kirimlhp/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadlhp").html(respond);
                }
            });
        }
        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_lhp = $(this).attr("kode_lhp");
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
                            , url: '/lhp/kirimlhp/delete'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_lhp: kode_lhp
                            }
                            , cache: false
                            , success: function(respond) {
                                if (respond == 0) {
                                    swal(
                                        'Deleted!'
                                        , 'Data Berhasil Dihapus'
                                        , 'success'
                                    )

                                    loadlhp();
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
            var kode_lhp = $(this).attr("kode_lhp");
            $.ajax({
                type: 'POST'
                , url: '/lhp/kirimlhp/edit'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_lhp: kode_lhp
                }
                , cache: false
                , success: function(respond) {
                    $("#loadeditlhp").html(respond);
                    $('#mdleditlhp').modal({
                        backdrop: 'static'
                        , keyboard: false
                    });
                }
            });
        });


        $(".approve").click(function(e) {
            e.preventDefault();
            var kode_lhp = $(this).attr("kode_lhp");
            $.ajax({
                type: 'POST'
                , url: '/lhp/kirimlhp/approve'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_lhp: kode_lhp
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 0) {
                        swal("Berhasil ", "Data Berhasil di Approve", "success");
                    } else {
                        swal("Gagal", "Data Gagal di Approve", "error");
                    }
                    loadlhp();
                }
            });
        });

        $(".cancel").click(function(e) {
            e.preventDefault();
            var kode_lhp = $(this).attr("kode_lhp");
            $.ajax({
                type: 'POST'
                , url: '/lhp/kirimlhp/cancel'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_lhp: kode_lhp
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 0) {
                        swal("Berhasil ", "Data Berhasil di Cancel", "success");
                    } else {
                        swal("Gagal", "Data Gagal di Cancel", "error");
                    }
                    loadlhp();
                }
            });
        });
    });

</script>
