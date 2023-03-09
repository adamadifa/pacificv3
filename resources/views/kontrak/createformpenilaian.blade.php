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
                <div class="col-4">
                    <label for="" class="form-label">Gaji Pokok</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Gaji Pokok" value="{{ rupiah($gaji->gaji_pokok) }}" field="gaji_pokok" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Jabatan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Jabatan" value="{{ rupiah($gaji->t_jabatan) }}" field="t_jabatan" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Masa Kerja</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Masa Kerja" value="{{ rupiah($gaji->t_masakerja) }}" field="t_masakerja" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">T. Tanggung Jawab</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Tanggung Jawab" value="{{ rupiah($gaji->t_tanggungjawab) }}" field="t_tanggungjawab" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Makan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Makan" value="{{ rupiah($gaji->t_makan) }}" field="t_makan" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunjangan Istri</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Istri" value="{{ rupiah($gaji->t_istri) }}" field="t_istri" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tunj. Skill Khusus</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tunjangan Skill Khusus" value="{{ rupiah($gaji->t_skill) }}" field="t_skill" icon="feather icon-file" right />
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
<script>
    $(function() {
        $("#gaji_pokok,#t_jabatan,#t_masakerja,#t_tanggungjawab,#t_istri,#t_makan,#t_skill").maskMoney();
        $("#frmKontrak").submit(function(e) {
            //e.preventDefault();
            var dari = $("#kontrak_dari").val();
            var sampai = $("#kontrak_sampai").val();
            var id_jabatan = $("#kontrak_id_jabatan").val();
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
