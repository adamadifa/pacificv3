<form action="/kaskecil/{{ Crypt::encrypt($kaskecil->id) }}/update" method="POST" id="frmEditkaskecil">
    <input type="hidden" name="id" id="id_kaskecil" value="{{ $kaskecil->id }}">
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
                                <input type="radio" name="inout" value="K" {{ ($kaskecil->status_dk ==  'K') ? 'checked' :'' }} {{ $disabled }}>
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
    @if ($kaskecil->kode_cabang == "PCF")
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
    @if (Auth::user()->kode_cabang == "PCF")
    <div class="row mb-1">
        <div class="col-12">
            <div class="vs-checkbox-con vs-checkbox-primary">

                <input type="checkbox" class="split_akun" name="split_akun" value="1">
                <span class="vs-checkbox">
                    <span class="vs-checkbox--check">
                        <i class="vs-icon feather icon-check"></i>
                    </span>
                </span>
                <span class="">Split Akun</span>
            </div>
        </div>
    </div>
    <div id="splitakunform">
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Keterangan" field="keterangan_split" icon="feather icon-file" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select name="kode_akun_split" id="kode_akun_split" class="form-control select2">
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
                <x-inputtext label="Jumlah" field="jumlah_split" icon="feather icon-file" value="" right />
            </div>
        </div>
        @if ($kaskecil->kode_cabang == "PST")
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <ul class="list-unstyled mb-0">
                        <li class="d-inline-block mr-2">
                            <fieldset>
                                <div class="vs-radio-con vs-radio-primary">
                                    <input type="radio" name="peruntukan_split" value="PCF">
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
                                    <input type="radio" name="peruntukan_split" value="MP">
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
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <a href="#" class="btn btn-info btn-block" id="tambahitem"><i class="feather icon-plus"></i>Tambah</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Kode Akun</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Peruntukan</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody id="loadsplit"></tbody>
                </table>
            </div>
        </div>
    </div>

    @endif
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
        $("#jumlah").maskMoney();
        $("#jumlah_split").maskMoney();
        $("#splitakunform").hide();
        $('.aggrement').change(function() {
            if (this.checked) {
                $("#tombolsimpan").show();
            } else {
                $("#tombolsimpan").hide();
            }
        });

        function loadsplit() {
            var no_bukti = $("#id_kaskecil").val();
            $("#loadsplit").load('/kaskecil/' + no_bukti + '/showsplit');
        }



        $("#tambahitem").click(function(e) {
            e.preventDefault();
            let keterangan = $("#keterangan_split").val();
            let kode_akun = $("#kode_akun_split").val();
            let jumlah = $("#jumlah_split").val();
            let peruntukan = $("input[name='peruntukan']:checked").val();
            let no_bukti = "{{ $kaskecil->id }}";


            if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#keterangan_split").focus();
                });
            } else if (kode_akun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode AKun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_akun_split").focus();
                });
            } else if (jumlah == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah_split").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/kaskecil/storesplitakun'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_akun: kode_akun
                        , keterangan: keterangan
                        , jumlah: jumlah
                        , peruntukan: peruntukan
                        , no_bukti: no_bukti
                    , }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 0) {
                            swal({
                                title: 'Oops'
                                , text: 'Data Berhasil Disimpan !'
                                , icon: 'success'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#keterangan_split").val('');
                                $("#jumlah_split").val('');
                                $("#kode_akun_split").val('').change();
                                $("#keterangan_split").focus();
                                loadsplit();
                            });
                        } else if (respond == 1) {
                            swal({
                                title: 'Oops'
                                , text: 'Data Sudah Ada !'
                                , icon: 'warning'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#keterangan_split").focus();
                            });
                        } else {
                            swal({
                                title: 'Oops'
                                , text: 'Data Gagal Disimpan, Hubungi Tim IT !'
                                , icon: 'error'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#keterangan_split").focus();
                            });
                        }
                    }

                });
            }
        });

        function hidetombolsimpan() {
            $("#tombolsimpan").hide();
        }

        hidetombolsimpan();
        $('.split_akun').change(function() {
            if (this.checked) {
                $("#splitakunform").show();
                loadsplit();
            } else {
                $("#splitakunform").hide();
            }

        });

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
            var totalsplit = $("#totalsplit").val();
            var split = $("input[name='split_akun']:checked").val();
            if (split == 1 && parseInt(jumlah.replace(/\./g, '')) != parseInt(totalsplit)) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Sama !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });

                return false;
            } else if (nobukti == "") {
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
            } else {
                $('#frmEditkaskecil').find('#nobukti').prop('disabled', false);
                $("#keterangan").prop('disabled', false);
                $("#jumlah").prop('disabled', false);
                $("#tgl_kaskecil").prop('disabled', false);
                $('input[name="inout"]').prop('disabled', false);
                $('input[name="peruntukan"]').prop('disabled', false);
            }

        });
    });

</script>
