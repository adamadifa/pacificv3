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
<form action="/kontrak/storefrompenilaian" method="POST" id="frmKontrak">
    @csrf
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <tr>
                    <th>Kode Penilaian</th>
                    <td>{{ $penilaian->kode_penilaian }}</td>
                </tr>
                <tr>
                    <th>NIK</th>
                    <td>{{ $penilaian->nik }}</td>
                </tr>
                <tr>
                    <th>Nama Karyawan</th>
                    <td>{{ $penilaian->nik }}</td>
                </tr>
                <tr>
                    <th>Jabatan Saat Ini</th>
                    <td>{{ $penilaian->nama_jabatan }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <input type="hidden" name="kode_penilaian" value="{{ $penilaian->kode_penilaian }}">
            <input type="hidden" name="nik" value="{{ $penilaian->nik }}">
            <input type="hidden" name="masa_kontrak_kerja" value="{{ $penilaian->masa_kontrak_kerja }}">
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
                        <input type="hidden" name="old_kode_dept" value="{{ $penilaian->kode_dept }}">
                        <select name="kode_dept" id="kontrak_kode_dept" class="form-control">
                            <option value="">Departemen</option>
                            @foreach ($departemen as $d)
                            <option {{ $penilaian->kode_dept==$d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <input type="hidden" name="old_id_jabatan" value="{{ $penilaian->id_jabatan }}">
                        <select name="id_jabatan" id="kontrak_id_jabatan" class="form-control">
                            <option value="">Jabatan Baru</option>
                            @foreach ($jabatan as $d)
                            <option {{ $penilaian->id_jabatan==$d->id ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <input type="hidden" name="old_id_perusahaan" value="{{ $penilaian->id_perusahaan }}">
                        <select name="id_perusahaan" id="kontrak_id_perusahaan" class="form-control">
                            <option value="">Perusahaan</option>
                            <option value="MP" {{ $penilaian->id_perusahaan =="MP" ? "selected" :"" }}>MAKMUR PERMATA</option>
                            <option value="PCF" {{ $penilaian->id_perusahaan =="PCF" ? "selected" :"" }}>PACIFIC</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <input type="hidden" name="old_id_kantor" value="{{ $penilaian->id_kantor }}">
                        <select name="id_kantor" id="kontrak_id_kantor" class="form-control">
                            <option value="">Kantor</option>
                            @foreach ($kantor as $d)
                            <option {{ $penilaian->id_kantor==$d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
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
                    <x-inputtext label="Gaji Pokok" value="{{$gaji != null ? rupiah($gaji->gaji_pokok) : 0 }}" field="gaji_pokok" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Jabatan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Jabatan" value="{{ $gaji != null ? rupiah($gaji->t_jabatan) : 0 }}" field="t_jabatan" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Masa Kerja</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Masa Kerja" value="{{ $gaji != null ? rupiah($gaji->t_masakerja) : 0 }}" field="t_masakerja" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">T. Tanggung Jawab</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Tanggung Jawab" value="{{ $gaji != null ? rupiah($gaji->t_tanggungjawab) : 0 }}" field="t_tanggungjawab" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Makan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Makan" value="{{ $gaji != null ? rupiah($gaji->t_makan) : 0 }}" field="t_makan" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Istri</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Istri" value="{{ $gaji != null ? rupiah($gaji->t_istri) : 0 }}" field="t_istri" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunj. Skill Khusus</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Skill Khusus" value="{{ $gaji != null ? rupiah($gaji->t_skill) : 0 }}" field="t_skill" icon="feather icon-file" right />
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
        $("#kontrak_id_jabatan").selectize();
        $("#gaji_pokok,#t_jabatan,#t_masakerja,#t_tanggungjawab,#t_istri,#t_makan,#t_skill").maskMoney();
        $("#frmKontrak").submit(function(e) {
            //e.preventDefault();
            var dari = $("#kontrak_dari").val();
            var sampai = $("#kontrak_sampai").val();
            var id_jabatan = $("#kontrak_id_jabatan").val();
            var kode_dept = $("#kontrak_kode_dept").val();
            var id_perusahaan = $("#kontrak_id_perusahaan").val();
            var id_kantor = $("#kontrak_id_kantor").val();
            var gaji_pokok = $("#gaji_pokok").val();
            var t_jabatan = $("#t_jabatan").val();
            var t_masakerja = $("#t_masakerja").val();
            var t_tanggungjawab = $("#t_tanggungjawab").val();
            var t_makan = $("#t_makan").val();
            var t_skill = $("#t_skill").val();

            if (dari == "" || sampai == "") {
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
            // else if (t_jabatan == "") {
            //     swal({
            //         title: 'Oops'
            //         , text: 'Tunjangan Jabatan Harus Diisi !'
            //         , icon: 'warning'
            //         , showConfirmButton: false
            //     }).then(function() {
            //         $("#t_jabatan").focus();
            //     });
            // } else if (t_tanggungjawab == "") {
            //     swal({
            //         title: 'Oops'
            //         , text: 'Tunjangan Tanggung Jawab Harus Diisi !'
            //         , icon: 'warning'
            //         , showConfirmButton: false
            //     }).then(function() {
            //         $("#t_tanggungjawab").focus();
            //     });
            // } else if (t_makan == "") {
            //     swal({
            //         title: 'Oops'
            //         , text: 'Tunjangan Makan Harus Diisi !'
            //         , icon: 'warning'
            //         , showConfirmButton: false
            //     }).then(function() {
            //         $("#t_makan").focus();
            //     });
            // } else if (t_skill == "") {
            //     swal({
            //         title: 'Oops'
            //         , text: 'Tunjangan Skill Harus Diisi !'
            //         , icon: 'warning'
            //         , showConfirmButton: false
            //     }).then(function() {
            //         $("#t_skill").focus();
            //     });
            // }
        });
    });

</script>
