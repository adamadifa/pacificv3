@foreach ($detailevaluasi as $d)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $d->agenda }}</td>
        <td>{{ $d->hasil_pembahasan }}</td>
        <td>{{ $d->action_plan }}</td>
        <td>{{ $d->pic }}</td>
        <td>{{ DateToIndo2($d->due_date) }}</td>
        <td>
            @if ($d->status == '1')
                <span class="badge bg-danger">Open</span>
            @elseif ($d->status == '2')
                <span class="badge bg-info">On Progress</span>
            @elseif ($d->status == '3')
                <span class="badge bg-success">Close</span>
            @endif
        </td>
        <td>
            <div class="btn-group">
                <a href="#" class="editagenda" kode_agenda="{{ $d->kode_agenda }}">
                    <i class="feather icon-edit info"></i>
                </a>
                <a href="#" kode_agenda="{{ $d->kode_agenda }}" agenda="{{ $d->agenda }}"
                    hasil_pembahasan="{{ $d->hasil_pembahasan }}" action_plan = "{{ $d->action_plan }}"
                    pic="{{ $d->pic }}" status="{{ $d->status }}" due_date="{{ $d->due_date }}"
                    class="hapus ml-1">
                    <i class="feather icon-trash danger"></i>
                </a>

            </div>
        </td>
    </tr>
@endforeach
<script>
    $(function() {

        function loaddetailevaluasi() {
            var kode_evaluasi = "{{ $kode_evaluasi }}";
            $("#load_detailevaluasi").load('/worksheetom/' + kode_evaluasi + '/getdetailevaluasi');
        }



        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_agenda = $(this).attr('kode_agenda');


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
                            url: '/worksheetom/deleteagenda',
                            data: {
                                _token: "{{ csrf_token() }}",
                                kode_agenda: kode_agenda,
                            },
                            cache: false,
                            success: function(respond) {
                                if (respond == 0) {
                                    swal('Deleted!', 'Data Berhasil Dihapus', 'success')
                                } else {
                                    swal('Oops!', 'Data Gagal Dihapus', 'error')
                                }
                                loaddetailevaluasi();
                            }
                        });
                    }
                });
        });
    });
</script>
