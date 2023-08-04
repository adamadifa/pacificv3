@foreach ($karyawan as $d)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->nik }}</td>
    <td>{{ $d->nama_karyawan }}</td>
    <td>{{ $d->kode_dept }}</td>
    <td>{{ $d->nama_jabatan }}</td>
    <td>{{ $d->nama_group }}</td>
    <td>
        <div class="text-center loadingshift">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        @if (!empty($d->kode_lembur))
        <a href="#" class="hapuskaryawanlembur" nik="{{ $d->nik }}"><i class="fa fa-close danger"></i></a>
        @else
        <a href="#" class="tambahkaryawanlembur" nik="{{ $d->nik }}"><i class="fa fa-plus primary"></i></a>
        @endif
    </td>
</tr>
@endforeach


<script>
    $(function() {
        $('.loadingshift').hide();

        function loadlemburkaryawan() {
            var kode_lembur = "{{ $kode_lembur }}";
            $("#loadlemburkaryawan").load('/lembur/' + kode_lembur + '/getlemburkaryawan');
        }

        function loadlistkaryawan() {
            var kode_lembur = "{{ $kode_lembur }}";
            var id_kantor = "{{ $id_kantor }}";
            var kode_dept = $("#kode_dept_search").val();
            var id_perusahaan = $("#id_perusahaan_search").val();
            var grup = $("#grup_search").val();
            var nama_karyawan = $("#nama_karyawan_search").val();
            $.ajax({
                type: 'POST'
                , url: '/lembur/getlistkaryawan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_lembur: kode_lembur
                    , id_kantor: id_kantor
                    , kode_dept: kode_dept
                    , id_perusahaan: id_perusahaan
                    , grup: grup
                    , nama_karyawan: nama_karyawan
                }
                , cache: false
                , success: function(respond) {
                    $("#loadlistkaryawan").html(respond);
                    loadlemburkaryawan();
                }
            });
        }
        $(".tambahkaryawanlembur").click(function(e) {
            e.preventDefault();
            var kode_lembur = "{{ $kode_lembur }}";
            var nik = $(this).attr("nik");
            $(this).hide();
            $(this).closest("td").find(".loadingshift").show();
            $.ajax({
                type: 'POST'
                , url: '/lembur/storekaryawanlembur'
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
                        loadlistkaryawan();
                    }
                    $(this).closest("td").find(".loadingshift").hide();
                    $(this).show();
                }
            });
        });


        $(".hapuskaryawanlembur").click(function(e) {
            e.preventDefault();
            var kode_lembur = "{{ $kode_lembur }}";
            var nik = $(this).attr("nik");
            $(this).hide();
            $(this).closest("td").find(".loadingshift").show();
            $.ajax({
                type: 'POST'
                , url: '/lembur/hapuskaryawanlembur'
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
                        loadlistkaryawan();
                    }
                    $(this).closest("td").find(".loadingshift").hide();
                    $(this).show();
                }
            });
        });

    });

</script>
