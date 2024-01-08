@if (Auth::user()->kode_cabang == 'PCF' || Auth::user()->kode_cabang == 'PST')
    @if ($level == 'manager hrd' || $level == 'admin')
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <select name="kode_dept_search" id="kode_dept_search" class="form-control">
                        <option value="">Departemen</option>
                        @foreach ($departemen as $d)
                            <option {{ $kode_dept == $d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">
                                {{ $d->nama_dept }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <select name="id_perusahaan_search" id="id_perusahaan_search" class="form-control">
                        <option value="">MP/PCF</option>
                        <option value="MP">MP</option>
                        <option value="PCF">PCF</option>
                    </select>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <select name="grup_search" id="grup_search" class="form-control grup_search">
                        <option value="">Semua Grup</option>
                        @foreach ($group as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_group }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-4">
                <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users" />
            </div>
        </div>
    @else
        <div class="row">
            <input type="hidden" name="kode_dept_search" id="kode_dept_search" value="{{ $kode_dept }}">
            <input type="hidden" name="id_perusahaan_search" id="id_perusahaan_search" value="">
            <input type="hidden" name="grup_search" id="grup_search" value="">
            <div class="col-9">
                <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users" />
            </div>
            <div class="col-3">
                <div class="form-group">
                    <select name="grup_search" id="grup_search" class="form-control grup_search">
                        <option value="">Semua Grup</option>
                        @foreach ($group as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_group }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
@endif
<div class="row mb-2 mt-1">
    <div class="col-12 d-flex justify-content-between">
        <a href="#" class="btn btn-success" id="tambahkansemua" kode_dept="{{ $kode_dept }}"
            kode_lembur="{{ $kode_lembur }}" id_kantor="{{ $id_kantor }}"><i
                class="feather icon-user-check mr-1"></i>Tambahkan Semua</a>
        <a href="#" class="btn btn-danger" id="batalkansemua" kode_dept="{{ $kode_dept }}"
            kode_lembur="{{ $kode_lembur }}" id_kantor="{{ $id_kantor }}"><i
                class="feather icon-user-x mr-1"></i>Batalkan Semua</a>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-hover-animation">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Kode Dept</th>
                    <th>Jabatan</th>
                    <th>Grup</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody id="loadlistkaryawan">
                <div class="text-center loadingkaryawan">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </tbody>
        </table>
    </div>
</div>



<script>
    $(function() {


        function loadlemburkaryawan() {
            var kode_lembur = "{{ $kode_lembur }}";
            $("#loadlemburkaryawan").load('/lembur/' + kode_lembur + '/getlemburkaryawan');
        }

        $('.loadingkaryawan').hide();

        function loadlistkaryawan() {
            var kode_lembur = "{{ $kode_lembur }}";
            var id_kantor = "{{ $id_kantor }}";
            var kode_dept = $("#kode_dept_search").val();
            var id_perusahaan = $("#id_perusahaan_search").val();
            var grup = $(".grup_search").val();
            //alert(grup);
            var nama_karyawan = $("#nama_karyawan_search").val();
            $("#loadlistkaryawan").html("");
            $('.loadingkaryawan').show();
            $.ajax({
                type: 'POST',
                url: '/lembur/getlistkaryawan',
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_lembur: kode_lembur,
                    id_kantor: id_kantor,
                    kode_dept: kode_dept,
                    id_perusahaan: id_perusahaan,
                    grup: grup,
                    nama_karyawan: nama_karyawan
                },
                cache: false,
                success: function(respond) {
                    $('.loadingkaryawan').hide();
                    $("#loadlistkaryawan").html(respond);
                }
            });
        }


        $("#tambahkansemua").click(function(e) {
            e.preventDefault();
            var kode_dept = $(this).attr('kode_dept');
            var kode_lembur = $(this).attr('kode_lembur');
            var id_kantor = $(this).attr('id_kantor');
            var grup = $(".grup_search").val();


            $.ajax({
                type: 'POST',
                url: '/lembur/storeallkaryawan',
                data: {
                    _token: '{{ csrf_token() }}',
                    kode_dept: kode_dept,
                    kode_lembur: kode_lembur,
                    id_kantor: id_kantor,
                    grup: grup
                },
                cache: false,
                success: function(respond) {
                    if (respond == 1) {
                        swal({
                            title: 'Oops',
                            text: 'Data Gagal Batalkan, Hubungi IT !',
                            icon: 'warning',
                            showConfirmButton: false
                        }).then(function() {

                        });
                    } else {
                        loadlistkaryawan();
                        loadlemburkaryawan();
                    }
                }
            });
        });


        $("#batalkansemua").click(function(e) {
            e.preventDefault();
            var kode_dept = $(this).attr('kode_dept');
            var kode_lembur = $(this).attr('kode_lembur');
            var id_kantor = $(this).attr('id_kantor');

            $.ajax({
                type: 'POST',
                url: '/lembur/cancelkaryawan',
                data: {
                    _token: '{{ csrf_token() }}',
                    kode_dept: kode_dept,
                    kode_lembur: kode_lembur,
                    id_kantor: id_kantor
                },
                cache: false,
                success: function(respond) {
                    if (respond == 1) {
                        swal({
                            title: 'Oops',
                            text: 'Data Gagal Batalkan, Hubungi IT !',
                            icon: 'warning',
                            showConfirmButton: false
                        }).then(function() {

                        });
                    } else {
                        loadlistkaryawan();
                        loadlemburkaryawan();
                    }
                }
            });
        });

        loadlistkaryawan();

        $("#kode_dept_search,#id_perusahaan_search,#grup_search").change(function(e) {
            loadlistkaryawan();
        });

        $("#nama_karyawan_search").keyup(function(e) {
            loadlistkaryawan();
        });
    });
</script>
