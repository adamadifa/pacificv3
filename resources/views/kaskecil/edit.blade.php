<form action="/kaskecil/{{ Crypt::encrypt($kaskecil->id) }}/update" method="POST" id="frmEditkaskecil">
    <input type="hidden" id="cektutuplaporan">
    @csrf
    @php
    if(empty($kaskecil->kode_klaim) && $kaskecil->keterangan != "Penerimaan Kas Kecil" ){
    $disabled = "";
    }else{
    $disabled = "disabled";
    }
    @endphp

    <div class="row">
        <div class="col-12">
            <input type="hidden" name="nobukti_old" value="{{ $kaskecil->nobukti }}">
            <x-inputtext label="No. Bukti" field="nobukti" icon="feather icon-credit-card" value="{{ $kaskecil->nobukti }}" disabled="{{ $disabled }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Kas Kecil" field="tgl_kaskecil" icon="feather icon-calendar" datepicker value="{{ $kaskecil->tgl_kaskecil }}" disabled="{{ $disabled }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" value="{{ $kaskecil->keterangan }}" disabled="{{ $disabled }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Jumlah" field="jumlah" icon="feather icon-file" value="{{ rupiah($kaskecil->jumlah) }}" right disabled="{{ $disabled }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_akun" id="kode_akun" class="form-control select2">
                    <option value="">Pilih Akun</option>
                    @foreach ($coa as $d)
                    <option {{ ($kaskecil->kode_akun ==  $d->kode_akun) ? 'selected' :'' }} value="{{ $d->kode_akun }}">{{ $d->kode_akun }} {{ $d->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <ul class="list-unstyled mb-0">
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-radio-con vs-radio-success">
                                <input type="radio" name="inout" value="K" {{ ($kaskecil->status_dk ==  'K') ? 'selected' :'' }} {{ $disabled }}>
                                <span class="vs-radio">
                                    <span class="vs-radio--border"></span>
                                    <span class="vs-radio--circle"></span>
                                </span>
                                <span class="">IN</span>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-radio-con vs-radio-danger">
                                <input type="radio" name="inout" value="D" {{ ($kaskecil->status_dk == 'D') ? 'checked' : '' }} {{ $disabled }}>
                                <span class="vs-radio">
                                    <span class="vs-radio--border"></span>
                                    <span class="vs-radio--circle"></span>
                                </span>
                                <span class="">OUT</span>
                            </div>
                        </fieldset>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @if (Auth::user()->kode_cabang == "PCF")
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <ul class="list-unstyled mb-0">
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-radio-con vs-radio-primary">
                                <input type="radio" name="peruntukan" value="PCF" {{ ($kaskecil->peruntukan == 'PCF') ? 'checked' : '' }} {{ $disabled }}>
                                <span class="vs-radio">
                                    <span class="vs-radio--border"></span>
                                    <span class="vs-radio--circle"></span>
                                </span>
                                <span class="">Pacific</span>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-radio-con vs-radio-primary">
                                <input type="radio" name="peruntukan" value="MP" {{ ($kaskecil->peruntukan == 'MP') ? 'checked' : '' }} {{ $disabled }}>
                                <span class="vs-radio">
                                    <span class="vs-radio--border"></span>
                                    <span class="vs-radio--circle"></span>
                                </span>
                                <span class="">Makmur Permata</span>
                            </div>
                        </fieldset>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif
    <div class="row" id="tombolsimpan">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block"><i class="feather icon-send mr-1"></i> Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    $(function() {

        function cektutuplaporan() {
            var tanggal = $("#tgl_kaskecil").val();
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "kaskecil"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }

        $("#tgl_kaskecil").change(function() {
            cektutuplaporan();
        });
        cektutuplaporan();
        $("#frmEditkaskecil").submit(function() {
            var nobukti = $('#frmEditkaskecil').find('#nobukti').val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            var keterangan = $("#keterangan").val();
            var jumlah = $("#jumlah").val();
            var kode_akun = $("#kode_akun").val();
            var kode_cabang = $('#frmInputkaskecil').find('#kode_cabang').val();
            if (nobukti == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Bukti Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nobukti").focus();
                });

                return false;
            } else if (cektutuplaporan == 1) {
                swal({
                    title: 'Oops'
                    , text: 'Laporan Sudah Di Tutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#frmEditkaskecil').find('#nobukti').focus();
                });

                return false;
            } else if (tgl_kaskecil == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_kaskecil").focus();
                });
                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#keterangan").focus();
                });
                return false;
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
                return false;
            } else if (kode_akun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Akun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_akun").focus();
                });
                return false;
            }

        });
    });

</script>
