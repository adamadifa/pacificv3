<form action="/ledger/store" method="post" id="FrmInputledger">
    <input type="hidden" name="kode_ledger" id="kode_ledger" value="{{ Crypt::decrypt($kode_ledger) }}">
    <input type="hidden" id="cektutuplaporan">
    <input type="hidden" id="cekledgertemp">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal" field="tgl_ledger" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Pelanggan" field="pelanggan" icon="feather icon-file" />
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
                                <input type="radio" checked name="status_dk" value="D">
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
                                <input type="radio" name="status_dk" value="K">
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
                                <input type="radio" class="peruntukan" name="peruntukan" value="PC">
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
                                <input type="radio" class="peruntukan" name="peruntukan" value="MP">
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
                    <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <a href="#" class="btn btn-info btn-block" id="tambahitem"><i class="feather icon-plus-square mr-2"></i> Tambah Item</a>
            </div>
        </div>
    </div>
    <table class="table table-hover-animation" style="font-size:12px">
        <thead class="thead-dark">
            <tr>
                <th>Tgl</th>
                <th>Pelanggan</th>
                <th>Akun</th>
                <th>Keterangan</th>
                <th>Peruntukan</th>
                <th>Debet</th>
                <th>Kredit</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="loadledgertemp">

        </tbody>
    </table>
    <div class="row mb-1">
        <div class="col-12">
            <div class="vs-checkbox-con vs-checkbox-primary">
                <input type="checkbox" class="aggrement" name="aggrement" value="aggrement">
                <span class="vs-checkbox">
                    <span class="vs-checkbox--check">
                        <i class="vs-icon feather icon-check"></i>
                    </span>
                </span>
                <span class="">Yakin Akan Disimpan ?</span>
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

        $('.aggrement').change(function() {
            if (this.checked) {
                $("#tombolsimpan").show();
            } else {
                $("#tombolsimpan").hide();
            }
        });

        function hidetombolsimpan() {
            $("#tombolsimpan").hide();
        }

        hidetombolsimpan();

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


        function simpantemp() {

            var kode_ledger = $("#kode_ledger").val();
            var tgl_ledger = $("#tgl_ledger").val();
            var keterangan = $("#keterangan").val();
            var pelanggan = $("#pelanggan").val();
            var jumlah = $("#jumlah").val();
            var kode_akun = $("#kode_akun").val();
            var kode_cabang = $("#kode_cabang").val();
            var status_dk = $("input[name='status_dk']:checked").val();
            var peruntukan = $("input[name='peruntukan']:checked").val();
            $.ajax({
                type: 'POST'
                , url: '/ledger/storetemp'
                , data: {
                    _token: "{{ csrf_token(); }}"
                    , kode_ledger: kode_ledger
                    , kode_cabang: kode_cabang
                    , tgl_ledger: tgl_ledger
                    , pelanggan: pelanggan
                    , keterangan: keterangan
                    , jumlah: jumlah
                    , kode_akun: kode_akun
                    , status_dk: status_dk
                    , peruntukan: peruntukan
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 0) {
                        swal("Success", "Data Berhasil Disimpan", "success");
                        reset();
                    } else {
                        swal("Oops", "Data Gagal Disimpan, Hubungi Tim IT", "warning");
                    }
                }
            });
        }
        $("#tambahitem").click(function() {

            var tgl_ledger = $("#tgl_ledger").val();
            var keterangan = $("#keterangan").val();
            var pelanggan = $("#pelanggan").val();
            var jumlah = $("#jumlah").val();
            var kode_akun = $("#kode_akun").val();
            var kode_cabang = $('#FrmInputledger').find('#kode_cabang').val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            var peruntukan = $("input[name='peruntukan']:checked").val();

            if (cektutuplaporan == 1) {
                swal({
                    title: 'Oops'
                    , text: 'Laporan Sudah Di Tutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#tgl_ledger').focus();
                });
            } else if (tgl_ledger == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#tgl_ledger').focus();
                });
            } else if (pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pelanggan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#pelanggan").focus();
                });
            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#keterangan").focus();
                });
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
            } else if (kode_akun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Akun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_akun").focus();
                });
            } else if (peruntukan == "PC" && kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#FrmInputledger').find('#kode_cabang').focus();
                });
            } else {
                simpantemp();
                reset();
                loadledgertemp();
            }

        });

        function cekledgertemp() {
            var kode_ledger = $("#kode_ledger").val();
            $.ajax({
                type: 'POST'
                , url: '/cekledgertemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_ledger: kode_ledger
                }
                , cache: false
                , success: function(respond) {
                    $("#cekledgertemp").val(respond);
                }
            });
        }

        function loadledgertemp() {
            var kode_ledger = $("#kode_ledger").val();
            $.ajax({
                type: 'POST'
                , url: '/getledgertemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_ledger: kode_ledger
                }
                , cache: false
                , success: function(respond) {
                    $("#loadledgertemp").html(respond);
                    cekledgertemp();
                }
            });
        }

        loadledgertemp();

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


        function reset() {
            $("#keterangan").val("");
            $("#jumlah").val("");
            $("#kode_akun").val("").change();
            $("#pelanggan").focus();
            $("#pelanggan").val("");
        }
        $("#FrmInputledger").submit(function() {
            var cekledgertemp = $("#cekledgertemp").val();
            if (cekledgertemp == "" || cekledgertemp == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data Masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_ledger").focus();
                });

                return false;
            }
        });
    });

</script>
