<div class="row">
    <div class="col-3">
        <div class="form-group">
            <select name="kode_dept_search" id="kode_dept_search" class="form-control">
                <option value="">Departemen</option>
                @foreach ($departemen as $d)
                <option {{ Request('kode_dept_search')==$d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-2">
        <div class="form-group">
            <select name="id_perusahaan_search" id="id_perusahaan_search" class="form-control">
                <option value="">MP/PCF</option>
                <option value="MP" {{ Request('id_perusahaan_search') == "MP" ? "selected" : "" }}>MP</option>
                <option value="PCF" {{ Request('id_perusahaan_search') == "PCF" ? "selected" : "" }}>PCF</option>
            </select>
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <select name="grup_search" id="grup_search" class="form-control">
                <option value="">Grup</option>
                @foreach ($group as $d)
                <option {{ Request('grup_search')==$d->id ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->nama_group }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-4">
        <x-inputtext label="Nama Karyawan" field="nama_karyawan_search" icon="feather icon-users" />
    </div>
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
        $('.loadingkaryawan').hide();

        function loadlistkaryawan() {
            var kode_libur = "{{ $kode_libur }}";
            var id_kantor = "{{ $id_kantor }}";
            var kode_dept = $("#kode_dept_search").val();
            var id_perusahaan = $("#id_perusahaan_search").val();
            var grup = $("#grup_search").val();
            var nama_karyawan = $("#nama_karyawan_search").val();
            $("#loadlistkaryawan").html("");
            $('.loadingkaryawan').show();
            $.ajax({
                type: 'POST'
                , url: '/harilibur/getlistkaryawan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_libur: kode_libur
                    , id_kantor: id_kantor
                    , kode_dept: kode_dept
                    , id_perusahaan: id_perusahaan
                    , grup: grup
                    , nama_karyawan: nama_karyawan
                }
                , cache: false
                , success: function(respond) {
                    $('.loadingkaryawan').hide();
                    $("#loadlistkaryawan").html(respond);
                }
            });
        }

        loadlistkaryawan();

        $("#kode_dept_search,#id_perusahaan_search,#grup_search").change(function(e) {
            loadlistkaryawan();
        });

        $("#nama_karyawan_search").keyup(function(e) {
            loadlistkaryawan();
        });
    });

</script>
