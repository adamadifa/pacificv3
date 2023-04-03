<style>
    .form-label {
        font-weight: bold;
        margin-top: 10px;
    }

    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

</style>
<form action="/kontrak/store" method="POST" id="frmKontrak">
    @csrf

    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="nik" id="nik" class="form-control">
                            <option value="">Pilih Karyawan</option>
                            @foreach ($karyawan as $d)
                            <option value="{{ $d->nik }}">{{ $d->nama_karyawan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <x-inputtext field="kontrak_dari" label="Dari" icon="feather icon-calendar" datepicker />
                </div>
                <div class="col-6">
                    <x-inputtext field="kontrak_sampai" label="Sampai" icon="feather icon-calendar" datepicker />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="kode_dept" id="kontrak_kode_dept" class="form-control">
                            <option value="">Departemen</option>
                            @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="id_jabatan" id="kontrak_id_jabatan" class="form-control">
                            <option value="">Jabatan Baru</option>
                            @foreach ($jabatan as $d)
                            <option value="{{ $d->id }}">{{ $d->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="id_perusahaan" id="kontrak_id_perusahaan" class="form-control">
                            <option value="">Perusahaan</option>
                            <option value="MP">MAKMUR PERMATA</option>
                            <option value="PCF">PACIFIC</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="id_kantor" id="kontrak_id_kantor" class="form-control">
                            <option value="">Kantor</option>
                            @foreach ($kantor as $d)
                            <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Gaji Pokok</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Gaji Pokok" field="gaji_pokok" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Jabatan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Jabatan" field="t_jabatan" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Masa Kerja</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Masa Kerja" field="t_masakerja" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">T. Tanggung Jawab</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Tanggung Jawab" field="t_tanggungjawab" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Makan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Makan" field="t_makan" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Istri</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Istri" field="t_istri" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunj. Skill Khusus</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Skill Khusus" field="t_skill" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" type="submit"><i class="feather icon-send mr-1"></i>Buat Kontrak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script src="{{ asset('app-assets/js/external/selectize.js') }}"></script>
<script>
    $(function() {
        $("#nik").selectize();
        $("#kontrak_id_jabatan").selectize();
        $("#gaji_pokok,#t_jabatan,#t_masakerja,#t_tanggungjawab,#t_istri,#t_makan,#t_skill").maskMoney();
        $("#frmKontrak").submit(function(e) {
            //e.preventDefault();
            var nik = $("#nik").val();
            var dari = $("#kontrak_dari").val();
            var sampai = $("#kontrak_sampai").val();
            var id_jabatan = $("#kontrak_id_jabatan").val();
            var id_perusahaan = $("#kontrak_id_perusahaan").val();
            var id_kantor = $("#kontrak_id_kantor").val();
            var kode_dept = $("#kontrak_kode_dept").val();
            var gaji_pokok = $("#gaji_pokok").val();
            var t_jabatan = $("#t_jabatan").val();
            var t_masakerja = $("#t_masakerja").val();
            var t_tanggungjawab = $("#t_tanggungjawab").val();
            var t_makan = $("#t_makan").val();
            var t_skill = $("#t_skill").val();
            if (nik == "") {
                swal({
                    title: 'Oops'
                    , text: 'Nik Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nik").focus();
                });
                return false;
            } else if (dari == "" || sampai == "") {
                swal({
                    title: 'Oops'
                    , text: 'Periode Kontrak Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kontrak_dari").focus();
                });
                return false;
            } else if (kode_dept == "") {
                swal({
                    title: 'Oops'
                    , text: 'Departemen Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kontrak_kode_dept").focus();
                });
                return false;
            } else if (id_jabatan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jabatan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kontrak_id_jabatan").focus();
                });
                return false;
            } else if (id_perusahaan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Perusahaan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kontrak_id_perusahaan").focus();
                });
                return false;
            } else if (id_kantor == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kantor Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kontrak_id_kantor").focus();
                });
                return false;
            } else if (gaji_pokok == "") {
                swal({
                    title: 'Oops'
                    , text: 'Gaji Pokok Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#gaji_pokok").focus();
                });
                return false;
            }

        });
    });

</script>
