<div class="row mb-2">
    <div class="col-12 d-flex justify-content-between">
        <a href="#" class="btn btn-success" id="tambahkansemua" id_group="{{ $id_group }}" kode_jadwal="{{ $kode_jadwal }}" kode_setjadwal="{{ $kode_setjadwal }}"><i class="feather icon-user-check mr-1"></i>Tambahkan Semua</a>
        <a href="#" class="btn btn-danger" id="batalkansemua" id_group="{{ $id_group }}" kode_jadwal="{{ $kode_jadwal }}" kode_setjadwal="{{ $kode_setjadwal }}"><i class="feather icon-user-x mr-1"></i>Batalkan Semua</a>
    </div>
</div>
<table class="table table-hover-animation">
    <thead>
        <tr>
            <th>No.</th>
            <th>Nik</th>
            <th>Nama Karyawan</th>
            <th>Jadwal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($karyawan as $d)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $d->nik }}</td>
            <td>{{ $d->nama_karyawan }}</td>
            <td>
                {{ $d->nama_jadwal }}
            </td>
            <td>
                <div class="text-center loadingshift">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                @if (!empty($d->kode_jadwal))
                @if ($d->kode_jadwal == $kode_jadwal)
                <a href="#" class="hapuskaryawanshift" id_group="{{ $d->grup }}" kode_setjadwal="{{ $kode_setjadwal }}" nik="{{ $d->nik }}"><i class="fa fa-close danger"></i></a>
                @else
                <a href="#" class="gantikaryawanshift" id_group="{{ $d->grup }}" kode_setjadwal="{{ $kode_setjadwal }}" nik="{{ $d->nik }}"><i class="fa fa-refresh warning"></i></a>
                @endif

                @else
                <a href="#" class="tambahkaryawanshift" id_group="{{ $d->grup }}" nik="{{ $d->nik }}"><i class="fa fa-plus primary"></i></a>
                @endif

            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {

        $('.loadingshift').hide();

        function loadgroup(id_group = 29) {
            //$('#loadgroup').html("");
            //$('#loadingshift').show();
            var kode_setjadwal = "{{ $kode_setjadwal }}";
            var kode_jadwal = "{{ $kode_jadwal }}";
            //$("#loadgroup").load('/konfigurasijadwal/' + id_group + '/showgroup');
            // ('#loadingrekappersediaan').hide();
            // $("#loadrekappersediaan").html(respond);
            $.ajax({
                type: 'GET'
                , url: '/konfigurasijadwal/' + id_group + '/showgroup'
                , data: {
                    kode_setjadwal: kode_setjadwal
                    , kode_jadwal: kode_jadwal
                }
                , cache: false
                , success: function(respond) {
                    //$('#loadingshift').hide("");
                    $("#loadgroup").html(respond);
                }
            });
        }


        function loadgroup2(id_group = 29) {
            $('#loadgroup').html("");
            $('#loading').show();
            var kode_setjadwal = "{{ $kode_setjadwal }}";
            var kode_jadwal = "{{ $kode_jadwal }}";
            //$("#loadgroup").load('/konfigurasijadwal/' + id_group + '/showgroup');
            // ('#loadingrekappersediaan').hide();
            // $("#loadrekappersediaan").html(respond);
            $.ajax({
                type: 'GET'
                , url: '/konfigurasijadwal/' + id_group + '/showgroup'
                , data: {
                    kode_setjadwal: kode_setjadwal
                    , kode_jadwal: kode_jadwal
                }
                , cache: false
                , success: function(respond) {
                    $('#loading').hide("");
                    $("#loadgroup").html(respond);
                }
            });
        }

        function showshift(kode_jadwal) {
            var kode_setjadwal = "{{ $kode_setjadwal }}";
            $.ajax({
                type: 'POST'
                , url: '/konfigurasijadwal/showjadwal'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_jadwal: kode_jadwal
                    , kode_setjadwal: kode_setjadwal
                }
                , cache: false
                , success: function(respond) {
                    if (kode_jadwal == "JD002") {
                        $("#loadshift1").html(respond);
                    } else if (kode_jadwal == "JD003") {
                        $("#loadshift2").html(respond);

                    } else if (kode_jadwal == "JD004") {
                        $("#loadshift3").html(respond);

                    }
                }
            });
        }


        $(".tambahkaryawanshift").click(function(e) {
            e.preventDefault();
            var kode_setjadwal = "{{ $kode_setjadwal }}";
            var kode_jadwal = "{{ $kode_jadwal }}";
            var nik = $(this).attr("nik");
            var id_group = $(this).attr('id_group');
            $(this).hide();
            $(this).closest("td").find(".loadingshift").show();
            $.ajax({
                type: 'POST'
                , url: '/konfigurasijadwal/storekaryawanshift'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_setjadwal: kode_setjadwal
                    , kode_jadwal: kode_jadwal
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
                        loadgroup(id_group);
                        showshift(kode_jadwal);
                    }
                    $(this).closest("td").find(".loadingshift").hide();
                    $(this).show();
                }
            });
        });

        $(".hapuskaryawanshift").click(function(e) {
            e.preventDefault();
            var kode_setjadwal = $(this).attr('kode_setjadwal');
            var nik = $(this).attr('nik');
            var kode_jadwal = "{{ $kode_jadwal }}";
            var id_group = $(this).attr('id_group');
            $(this).hide();
            $(this).closest("td").find(".loadingshift").show();
            $.ajax({
                type: 'POST'
                , url: '/konfigurasijadwal/hapuskaryawanshift'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_setjadwal: kode_setjadwal
                    , nik: nik
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 1) {
                        swal({
                            title: 'Oops'
                            , text: 'Data Gagal Batalkan, Hubungi IT !'
                            , icon: 'warning'
                            , showConfirmButton: false
                        }).then(function() {

                        });
                    } else {
                        loadgroup(id_group);
                        showshift(kode_jadwal);

                    }
                    $(this).closest("td").find(".loadingshift").hide();
                    $(this).show();
                }
            });
        });

        $("#tambahkansemua").click(function(e) {
            e.preventDefault();
            var id_group = $(this).attr('id_group');
            var kode_jadwal = $(this).attr('kode_jadwal');
            var kode_setjadwal = $(this).attr('kode_setjadwal');

            $.ajax({
                type: 'POST'
                , url: '/konfigurasijadwal/storeallkaryawanshift'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , id_group: id_group
                    , kode_jadwal: kode_jadwal
                    , kode_setjadwal: kode_setjadwal
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 1) {
                        swal({
                            title: 'Oops'
                            , text: 'Data Gagal Batalkan, Hubungi IT !'
                            , icon: 'warning'
                            , showConfirmButton: false
                        }).then(function() {

                        });
                    } else {
                        loadgroup2(id_group);
                        showshift(kode_jadwal);
                    }
                }
            });
        });


        $("#batalkansemua").click(function(e) {
            e.preventDefault();
            var id_group = $(this).attr('id_group');
            var kode_jadwal = $(this).attr('kode_jadwal');
            var kode_setjadwal = $(this).attr('kode_setjadwal');

            $.ajax({
                type: 'POST'
                , url: '/konfigurasijadwal/cancelallkaryawanshift'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , id_group: id_group
                    , kode_jadwal: kode_jadwal
                    , kode_setjadwal: kode_setjadwal
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 1) {
                        swal({
                            title: 'Oops'
                            , text: 'Data Gagal Batalkan, Hubungi IT !'
                            , icon: 'warning'
                            , showConfirmButton: false
                        }).then(function() {

                        });
                    } else {
                        loadgroup2(id_group);
                        showshift(kode_jadwal);
                    }
                }
            });
        });
    });

</script>
