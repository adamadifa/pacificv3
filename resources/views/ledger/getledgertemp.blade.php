@foreach ($ledgertemp as $d)
@if ($d->status_dk=="D")
@php
$debet = $d->jumlah;
$kredit = 0;
@endphp
@else
@php
$debet = 0;
$kredit = $d->jumlah;
@endphp
@endif
<tr>
    <td>{{ date("d/m/y",strtotime($d->tgl_ledger)) }}</td>
    <td>{{ $d->pelanggan }}</td>
    <td>{{ $d->kode_akun }} {{ $d->nama_akun }}</td>
    <td>{{ $d->keterangan }}</td>
    <td>{{ $d->peruntukan }} {{ $d->peruntukan=="PC" ? "(".$d->ket_peruntukan. ")"  : "" }}</td>
    <td class="text-right">{{ !empty($debet) ? rupiah($debet) : '' }}</td>
    <td class="text-right">{{ !empty($kredit) ? rupiah($kredit) : '' }}</td>
    <td>
        <a href="#" data-id="{{ $d->id }}" class="hapus danger"><i class="feather icon-trash"></i></a>
    </td>
</tr>
@endforeach

<script>
    $(function() {

        function cekledgertemp() {
            $.ajax({
                type: 'POST'
                , url: '/cekledgertemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#cekledgertemp").val(respond);
                }
            });
        }

        function loadledgertemp() {
            $.ajax({
                type: 'POST'
                , url: '/getledgertemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#loadledgertemp").html(respond);
                    cekledgertemp();
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
                            , url: '/ledger/deletetemp'
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
                                    loadledgertemp();
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
