@foreach ($lemburkaryawan as $key => $d)
@php
$grup = @$lemburkaryawan[$key + 1]->grup;
@endphp
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->nik }}</td>
    <td>{{ $d->nama_karyawan }}</td>
    <td>{{ $d->kode_dept }}</td>
    <td>{{ $d->nama_jabatan }}</td>
    <td>{{ $d->nama_group }}</td>
    <td>
        <a href="#" class="hapus" kode_lembur="{{ $d->kode_lembur }}" nik="{{ $d->nik }}">
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
        function loadlemburkaryawan(kode_lembur) {
            $("#loadlemburkaryawan").load('/lembur/' + kode_lembur + '/getlemburkaryawan');
        }

        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_lembur = $(this).attr("kode_lembur");
            var nik = $(this).attr("nik");
            $.ajax({
                type: 'POST'
                , url: '/lembur/hapuslemburkaryawan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_lembur: kode_lembur
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
                            loadlemburkaryawan(kode_lembur);
                        });
                    }
                }
            });
        });
    });

</script>
