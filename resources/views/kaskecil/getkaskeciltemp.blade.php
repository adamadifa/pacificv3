@foreach ($kaskeciltemp as $d)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->keterangan }}</td>
    <td align="right">{{ rupiah($d->jumlah) }}</td>
    <td>{{ $d->kode_akun }} {{ $d->nama_akun }}</td>
    <td>
        @php
        if ($d->status_dk == "D") {
        $inout = "OUT";
        $color = "danger";
        } else {
        $inout = "IN";
        $color = "success";
        }
        @endphp
        <span class="badge bg-{{ $color }}">{{ $inout }}</span>
    </td>
    @if (Auth::user()->kode_cabang == "PCF")
    <td>{{ $d->peruntukan }}</td>
    @endif
    <td>
        <a href="#" class="hapus" data-id="{{ $d->id }}"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach

<script>
    $(function() {
        function cekkaskeciltemp(callback) {
            var nobukti = $('#frmInputkaskecil').find('#nobukti').val();
            var kode_cabang = $('#frmInputkaskecil').find('#kode_cabang').val();
            $.ajax({
                type: 'POST'
                , url: '/cekkaskeciltemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti: nobukti
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#cekkaskeciltemp").val(respond);
                }
            });
        }

        function loadkaskeciltemp() {
            var nobukti = $('#frmInputkaskecil').find('#nobukti').val();
            var kode_cabang = $('#frmInputkaskecil').find('#kode_cabang').val();
            $.ajax({
                type: 'POST'
                , url: '/getkaskeciltemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti: nobukti
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#loadkaskeciltemp").html(respond);
                    cekkaskeciltemp();
                }
            });
        }
        $(".hapus").click(function(e) {
            e.preventDefault();
            var id = $(this).attr("data-id");
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
                        $.ajax({
                            type: 'POST'
                            , url: '/kaskecil/deletetemp'
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
                                    loadkaskeciltemp();
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
                });
        });
    });

</script>
