<link rel="stylesheet" href=" {{ asset('app-assets/js/richtexteditor/rte_theme_default.css') }}" />
<script type="text/javascript" src="{{ asset('app-assets/js/richtexteditor/rte.js') }}"></script>
<script type="text/javascript" src="{{ asset('app-assets/js/richtexteditor/plugins/all_plugins.js') }}"></script>


<form action="/worksheetom/{{ $evaluasi->kode_evaluasi }}/updateevaluasi" method="POST" id="frmEditevaluasi">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <x-inputtext label="Tanggal" value="{{ $evaluasi->tanggal }}" field="tanggal" icon="feather icon-calendar"
                    datepicker />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <x-inputtext label="Jam" value="{{ date('H:i', strtotime($evaluasi->jam)) }}" field="jam"
                    icon="feather icon-clock" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="label form-label mb-1">Peserta</div>
            <div class="form-group">
                <textarea name="peserta" class="form-control" id="peserta_edit" cols="30" rows="5" placeholder="Peserta">
                    {{ $evaluasi->peserta }}
                </textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <x-inputtext label="Tempat" value="{{ $evaluasi->tempat }}" field="tempat" icon="feather icon-map" />
            </div>
        </div>
    </div>
    @if (Auth::user()->kode_cabang == 'PCF')
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                        <option value="">Pilih Cabang</option>
                        @foreach ($cabang as $d)
                            <option {{ $evaluasi->kode_cabang == $d->kode_cabang ? 'selected' : '' }}
                                value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @else
        <input type="hidden" name="kode_cabang" value="{{ Auth::user()->kode_cabang }}" id="kode_cabang">
    @endif
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100"><i class="feather icon-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>


<script>
    $(function() {
        var editor1cfg = {}
        editor1cfg.toolbar = "basic";
        var editor1 = new RichTextEditor("#peserta_edit", editor1cfg);
        $('#jam').mask('00:00');
        $("#frmEditevaluasi").submit(function() {
            var tanggal = $("#frmEditevaluasi").find("#tanggal").val();
            var jam = $("#frmEditevaluasi").find("#jam").val();
            var peserta = $("#peserta_edit").val();
            var tempat = $("#frmEditevaluasi").find("#tempat").val();
            var kode_cabang = $("#frmEditevaluasi").find("#kode_cabang").val();
            if (tanggal == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tanggal").focus();
                });
                return false;
            } else if (jam == "") {
                swal({
                    title: 'Oops',
                    text: 'Jam Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#jam").focus();
                });
                return false;
            } else if (peserta == "") {
                swal({
                    title: 'Oops',
                    text: 'Peserta Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#peserta").focus();
                });
                return false;
            } else if (tempat == "") {
                swal({
                    title: 'Oops',
                    text: 'Tempat Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tempat").focus();
                });
                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops',
                    text: 'Cabang Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
                return false;
            }
        });
    });
