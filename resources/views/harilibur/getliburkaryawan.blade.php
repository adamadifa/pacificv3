@foreach ($liburkaryawan as $key => $d)
@php
$grup = @$liburkaryawan[$key + 1]->grup;
@endphp
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->nik }}</td>
    <td>{{ $d->nama_karyawan }}</td>
    <td>{{ $d->kode_dept }}</td>
    <td>{{ $d->nama_jabatan }}</td>
    <td>{{ $d->nama_group }}</td>
    <td>
        <a href="#" class="hapus" kode_libur="{{ $d->kode_libur }}" nik="{{ $d->nik }}">
            <i class="feather icon-trash danger"></i>
        </a>
    </td>
</tr>
@if ($grup != $d->grup)
<tr style="background-color: rgb(156, 240, 156)">
    <td colspan="7"></td>
</tr>

@endif
@endforeach

<script>
    $(function() {
        function loadliburkaryawan(kode_libur) {
            $("#loadliburkaryawan").load('/harilibur/' + kode_libur + '/getliburkaryawan');
        }

        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_libur = $(this).attr("kode_libur");
            var nik = $(this).attr("nik");
            $.ajax({
                type: 'POST'
                , url: '/harilibur/hapusliburkaryawan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_libur: kode_libur
                    , nik: nik
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 1) {
                        swal({
                            title: 'Oops'
                            , text: 'Data Gagal Disimpan, Hubungi IT !'
                            , icon: 'warning'
                            , showConfirmButton: false
                        }).then(function() {

                        });
                    } else {
                        swal({
                            title: 'Oops'
                            , text: 'Data Berhasil Dihapus !'
                            , icon: 'success'
                            , showConfirmButton: false
                        }).then(function() {
                            loadliburkaryawan(kode_libur);
                        });
                    }
                }
            });
        });
    });

</script>
