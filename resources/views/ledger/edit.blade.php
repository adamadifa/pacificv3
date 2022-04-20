<form action="/ledger/{{ Crypt::encrypt($ledger->no_bukti) }}/update" method="post" id="FrmEditledger">
    <input type="hidden" id="cektutuplaporan">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal" field="tgl_ledger" value="{{ $ledger->tgl_ledger }}" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Pelanggan" field="pelanggan" value="{{ $ledger->pelanggan }}" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" value="{{ $ledger->keterangan }}" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Jumlah" value="{{ rupiah($ledger->jumlah) }}" field="jumlah" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_akun" id="kode_akun" class="form-control select2">
                    <option value="">Pilih Akun</option>
                    @foreach ($coa as $d)
                    <option {{ $ledger->kode_akun == $d->kode_akun ? 'selected' : '' }} value="{{ $d->kode_akun }}">{{ $d->kode_akun }} {{ $d->nama_akun }}</option>
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
                                <input type="radio" {{ $ledger->status_dk=="D" ? 'checked' : '' }} name="status_dk" value="D">
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
                                <input type="radio" name="status_dk" value="K" {{ $ledger->status_dk=="K" ? 'checked' : '' }}>
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
                <ul class="list-unstyled mb-0">
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-radio-con vs-radio-primary">
                                <input type="radio" class="peruntukan" name="peruntukan" value="PC" {{ $ledger->peruntukan=="PC" ? 'checked' : '' }}>
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
                                <input type="radio" class="peruntukan" name="peruntukan" value="MP" {{ $ledger->peruntukan=="MP" ? 'checked' : '' }}>
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
    <div class="row">
        <div class="col-12">
            <div class="form-group" id="pilihcabang">
                <select name="kode_cabang" id="kode_cabang" class="form-control ">
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabang as $d)
                    <option {{ $ledger->ket_peruntukan == $d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row" id="tombolsimpan">
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


        cektutuplaporan();

        function loadpilihcabang() {
            var peruntukan = $("input[name='peruntukan']:checked").val();
            if (peruntukan == "PC") {
                $("#pilihcabang").show();
            } else {
                $("#pilihcabang").hide();
            }
        }

        loadpilihcabang();
        $('.peruntukan').change(function() {
            loadpilihcabang();

        });



        $("#FrmEditledger").submit(function() {
            var tgl_ledger = $("#tgl_ledger").val();
            var keterangan = $("#keterangan").val();
            var pelanggan = $("#pelanggan").val();
            var jumlah = $("#jumlah").val();
            var kode_akun = $("#kode_akun").val();
            var kode_cabang = $('#FrmEditledger').find('#kode_cabang').val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            var peruntukan = $("input[name='peruntukan']:checked").val();
            //alert(peruntukan);
            if (cektutuplaporan > 0) {
                swal({
                    title: 'Oops'
                    , text: 'Laporan Sudah Ditutup !'
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
                    $('#tgl_ledger').focus();
                });
                return false;
            } else if (pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pelanggan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#pelanggan").focus();
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
            } else if (peruntukan == "PC" && kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
                return false;
            }
        });
    });

</script>
