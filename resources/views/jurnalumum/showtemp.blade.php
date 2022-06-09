@foreach ($jurnaltemp as $d)
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
    <td>{{ $d->kode_akun }}</td>
    <td>{{ $d->nama_akun }}</td>
    <td class="text-right">{{ rupiah($debet) }}</td>
    <td class="text-right">{{ rupiah($kredit) }}</td>
    <td>
        <a href="#" class="hapus" data-id="{{ $d->id }}"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach

<script>
    $(function() {
        function cektemp() {
            var kode_dept = $("#frmjurnalumum").find("#kode_dept").val();
            $.ajax({
                type: 'POST'
                , url: '/jurnalumum/cektemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_dept: kode_dept
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektemp").val(respond);
                }
            });
        }

        function loadtemp() {
            var kode_dept = $("#frmjurnalumum").find("#kode_dept").val();
            $("#loadtemp").load("/jurnalumum/" + kode_dept + "/showtemp");
            cektemp();
        }

        $(".hapus").click(function() {
            var id = $(this).attr("data-id");
            $.ajax({
                type: 'POST'
                , url: '/jurnalumum/deletetemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id: id
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 0) {
                        swal({
                            title: 'Success'
                            , text: 'Data Berhasil Dihapus !'
                            , icon: 'success'
                            , showConfirmButton: false
                        }).then(function() {
                            loadtemp();

                        });
                    }
                }
            });
        });
    });

</script>
