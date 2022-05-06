@foreach ($detailtemp as $d)
@php
$total = $d->tarif + $d->tepung + $d->bs;
@endphp
<tr>
    <td>{{ $d->no_surat_jalan }}</td>
    <td>{{ date("d-m-Y",strtotime($d->tgl_input)) }}</td>
    <td>{{ $d->tujuan }}</td>
    <td>{{ $d->angkutan }}</td>
    <td class="text-right">{{ rupiah($d->tarif) }}</td>
    <td class="text-right">{{ rupiah($d->tepung) }}</td>
    <td class="text-right">{{ rupiah($d->bs) }}</td>
    <td class="text-right">{{ rupiah($total) }}</td>
    <td>
        <a href="#" class="hapus" no_surat_jalan="{{ $d->no_surat_jalan }}"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach
<script>
    $(function() {


        function loaddetail() {
            $("#loaddetailkontrabon").load("/kontrabonangkutan/showtemp");
        }

        $(".hapus").click(function(e) {
            e.preventDefault();
            var no_surat_jalan = $(this).attr("no_surat_jalan");
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
                            , url: '/kontrabonangkutan/deletetemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , no_surat_jalan: no_surat_jalan
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
                });
        });

    });

</script>
