<form action="/mutasibank/store" method="post" id="FrmInputmutasibank">
    <input type="hidden" name="kode_bank" value="{{ $kode_bank }}">
    <input type="hidden" name="kode_cabang" value="{{ $kode_cabang }}">
    <input type="hidden" id="cektutuplaporan">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal" field="tgl_ledger" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Jumlah" field="jumlah" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_akun" id="kode_akun" class="form-control select2">
                    <option value="">Pilih Akun</option>
                    @foreach ($coa as $d)
                    <option value="{{ $d->kode_akun }}">{{ $d->kode_akun }} {{ $d->nama_akun }}</option>
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
                            <div class="vs-radio-con vs-radio-danger">
                                <input type="radio" checked name="debetkredit" value="D">
                                <span class="vs-radio">
                                    <span class="vs-radio--border"></span>
                                    <span class="vs-radio--circle"></span>
                                </span>
                                <span class="">Debet</span>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-radio-con vs-radio-success">
                                <input type="radio" name="debetkredit" value="K">
                                <span class="vs-radio">
                                    <span class="vs-radio--border"></span>
                                    <span class="vs-radio--circle"></span>
                                </span>
                                <span class="">Kredit</span>
                            </div>
                        </fieldset>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit"><i class="fa fa-send mr-1"></i>Submit</button>
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
            var tanggal = $("#tgl_ledger").val();
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "ledger"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }

        $("#tgl_ledger").change(function() {
            cektutuplaporan();
        });
        $("#jumlah").maskMoney();
        $("#FrmInputmutasibank").submit(function(e) {
            var tgl_ledger = $("#tgl_ledger").val();
            var keterangan = $("#keterangan").val();
            var jumlah = $("#jumlah").val();
            var kode_akun = $("#kode_akun").val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            if (cektutuplaporan == 1) {
                swal({
                    title: 'Oops'
                    , text: 'Laporan Sudah Di Tutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_ledger").focus();
                });
                return false;
            } else if (tgl_ledger == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_ledger").focus();
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
