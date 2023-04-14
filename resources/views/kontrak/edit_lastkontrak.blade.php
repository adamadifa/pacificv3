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
<form action="/kontrak/{{ Crypt::encrypt($kontrak->no_kontrak) }}/updatelastkontrak" method="POST" id="frmKontrak">
    @csrf

    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <input type="hidden" name="nik" value="{{ $kontrak->nik }}">
                        <select name="nik_show" id="nik" class="form-control" disabled>
                            <option value="">Pilih Karyawan</option>
                            @foreach ($karyawan as $d)
                            <option {{ $kontrak->nik == $d->nik ? 'selected' : '' }} value="{{ $d->nik }}">{{ $d->nama_karyawan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <x-inputtext field="kontrak_dari" value="{{ $kontrak->dari }}" label="Dari" icon="feather icon-calendar" datepicker />
                </div>
                <div class="col-6">
                    <x-inputtext field="kontrak_sampai" value="{{ $kontrak->sampai }}" label="Sampai" icon="feather icon-calendar" datepicker />
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" type="submit"><i class="feather icon-send mr-1"></i>Update Kontrak</button>
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
            }

        });
    });

</script>
