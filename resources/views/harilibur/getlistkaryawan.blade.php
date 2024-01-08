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
            @if (!empty($d->kode_libur))
                <a href="#" class="hapuskaryawanlibur" nik="{{ $d->nik }}"><i
                        class="fa fa-close danger"></i></a>
            @else
                <a href="#" class="tambahkaryawanlibur" nik="{{ $d->nik }}"><i
                        class="fa fa-plus primary"></i></a>
            @endif
        </td>
    </tr>
@endforeach


<script>
    $(function() {
        $('.loadingshift').hide();

        function loadliburkaryawan() {
            var kode_libur = "{{ $kode_libur }}";
            $("#loadliburkaryawan").load('/harilibur/' + kode_libur + '/getliburkaryawan');
        }

        function loadlistkaryawan() {
            var kode_libur = "{{ $kode_libur }}";
            var id_kantor = "{{ $id_kantor }}";
            var kode_dept = $("#kode_dept_search").val();
            var id_perusahaan = $("#id_perusahaan_search").val();
            var grup = $("#grup_search").val();
            var nama_karyawan = $("#nama_karyawan_search").val();
            $.ajax({
                type: 'POST',
                url: '/harilibur/getlistkaryawan',
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_libur: kode_libur,
                    id_kantor: id_kantor,
                    kode_dept: kode_dept,
                    id_perusahaan: id_perusahaan,
                    grup: grup,
                    nama_karyawan: nama_karyawan
                },
                cache: false,
                success: function(respond) {
                    $("#loadlistkaryawan").html(respond);
                    loadliburkaryawan();
                }
            });
        }
        $(".tambahkaryawanlibur").click(function(e) {
            e.preventDefault();
            var kode_libur = "{{ $kode_libur }}";
            var nik = $(this).attr("nik");
            $(this).hide();
            $(this).closest("td").find(".loadingshift").show();
            $.ajax({
                type: 'POST',
                url: '/harilibur/storekaryawanlibur',
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_libur: kode_libur,
                    nik: nik
                },
                cache: false,
                success: function(respond) {
                    if (respond == 1) {
                        swal({
                            title: 'Oops',
                            text: 'Data Gagal Disimpan, Hubungi IT !',
                            icon: 'warning',
                            showConfirmButton: false
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


        $(".hapuskaryawanlibur").click(function(e) {
            e.preventDefault();
            var kode_libur = "{{ $kode_libur }}";
            var nik = $(this).attr("nik");
            $(this).hide();
            $(this).closest("td").find(".loadingshift").show();
            $.ajax({
                type: 'POST',
                url: '/harilibur/hapuskaryawanlibur',
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_libur: kode_libur,
                    nik: nik
                },
                cache: false,
                success: function(respond) {
                    if (respond == 1) {
                        swal({
                            title: 'Oops',
                            text: 'Data Gagal Disimpan, Hubungi IT !',
                            icon: 'warning',
                            showConfirmButton: false
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
