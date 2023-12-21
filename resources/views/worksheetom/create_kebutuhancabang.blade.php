<link rel="stylesheet" href=" {{ asset('app-assets/js/richtexteditor/rte_theme_default.css') }}" />
<script type="text/javascript" src="{{ asset('app-assets/js/richtexteditor/rte.js') }}"></script>
<script type="text/javascript" src="{{ asset('app-assets/js/richtexteditor/plugins/all_plugins.js') }}"></script>
<div class="row">
    <div class="col-12">
        <form action="/worksheetom/storekebutuhancabang" method="POST" id="frmCreateKebcabang">
            @csrf
            @if (Auth::user()->kode_cabang == 'PCF')
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <select name="kode_cabang" id="kode_cabang" class="form-control">
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabang as $d)
                                    <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @else
                <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ Auth::user()->kode_cabang }}">
            @endif

            <div class="row">
                <div class="col-12">
                    <select name="kode_jenis_kebutuhan" id="kode_jenis_kebutuhan" class="form-control">
                        <option value="">Jenis Kebutuhan</option>
                        @foreach ($jenis_kebutuhan as $d)
                            <option value="{{ $d->kode_jenis_kebutuhan }}">{{ $d->jenis_kebutuhan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label for="" class="form-label mb-1">Uraian Pengajuan</label>
                    <div class="form-group">
                        <textarea name="uraian_kebutuhan" id="uraian_kebutuhan" cols="30" rows="10"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <x-inputtext label="Periode Akhir" field="periode_akhir" icon="feather icon-calendar" datepicker />
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-100"><i class="feather icon-send mr-1"></i>
                            Submit</button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script>
    $(function() {
        var editor1cfg = {}
        editor1cfg.toolbar = "basic";
        var editor1 = new RichTextEditor("textarea", editor1cfg);

        $("#frmCreateKebcabang").submit(function() {
            var kode_cabang = $("#frmCreateKebcabang").find("#kode_cabang").val();
            var kode_jenis_kebutuhan = $("#frmCreateKebcabang").find("#kode_jenis_kebutuhan").val();
            var uraian_kebutuhan = $("#frmCreateKebcabang").find("#uraian_kebutuhan").val();
            var periode_akhir = $("#frmCreateKebcabang").find("#periode_akhir").val();


            if (kode_cabang == "") {
                swal({
                    title: 'Oops',
                    text: 'Cabang Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#frmCreateKebcabang").find("#kode_cabang").focus();
                });
                return false;
            } else if (kode_jenis_kebutuhan == "") {
                swal({
                    title: 'Oops',
                    text: 'Jenis Kebutuhan Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#frmCreateKebcabang").find("#kode_jenis_kebutuhan").focus();
                });
                return false;
            } else if (uraian_kebutuhan == "") {
                swal({
                    title: 'Oops',
                    text: 'Uraian Kebutuhan Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#frmCreateKebcabang").find("#uraian_kebutuhan").focus();
                });
                return false;
            } else if (periode_akhir == "") {
                swal({
                    title: 'Oops',
                    text: 'Periode Akhir Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#frmCreateKebcabang").find("#periode_akhir").focus();
                });
                return false;
            }
        });
    });
</script>
