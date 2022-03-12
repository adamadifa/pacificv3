<form action="/kaskecil/store" id="frmInputkaskecil">
    <input type="hidden" id="cektutuplaporan">
    <div class="row">
        @if (Auth::user()->kode_cabang =="PCF" && Auth::user()->level != "admin keuangan")
        <div class="col-lg-12 col-sm-12">
            <div class="form-group  ">
                <select name="kode_cabang" id="kode_cabang" class="form-control">
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabang as $c)
                    <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{
                                            $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @else
        @if (Auth::user()->kode_cabang=="PCF")
        @php
        $cbg = "PST";
        @endphp
        @else
        @php
        $cbg = Auth::user()->kode_cabang;
        @endphp
        @endif
        <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ $cbg }}">
        @endif
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="nobukti" label="No. Bukti" icon="feather icon-credit-card" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="tgl_kaskecil" label="Tanggal" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="keterangan" label="Keterangan" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="jumlah" label="Jumlah" icon="feather icon-file" right />
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
                            <div class="vs-radio-con vs-radio-success">
                                <input type="radio" name="inout" checked="" value="K">
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
                                <input type="radio" name="inout" value="D">
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
                                <input type="radio" name="peruntukan" value="PCF">
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
                                <input type="radio" name="peruntukan" value="MP">
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
            <div class="form-group">
                <a href="#" class="btn btn-info btn-block" id="tambahitem"><i class="feather icon-plus-square mr-2"></i> Tambah Item</a>
            </div>
        </div>
    </div>
    @endif
    <table class="table table-hover-animation">
        <thead class="thead-dark">
            <tr>
                <th>No</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th>Akun</th>
                <th>IN/OUT</th>
                @if (Auth::user()->kode_cabang == "PCF")
                <th>Peruntukan</th>
                @endif
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="loadkaskeciltemp">

        </tbody>
    </table>
</form>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    $(function() {
        $("#jumlah").maskMoney();

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

        function loadkaskeciltemp() {
            var nobukti = $('#frmInputkaskecil').find('#nobukti').val();
            var kode_cabang = $('#frmInputkaskecil').find('#kode_cabang').val();
            $.ajax({
                type: 'POST'
                , url: '/getkaskeciltemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti: nobukti
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#loadkaskeciltemp").html(respond);
                }
            });
        }

        $('#frmInputkaskecil').find('#nobukti').keyup(function() {
            loadkaskeciltemp();
        });

        $('#frmInputkaskecil').find('#kode_cabang').change(function() {
            loadkaskeciltemp();
        });;

        function simpantemp() {
            var nobukti = $('#frmInputkaskecil').find('#nobukti').val();
            var tgl_kaskecil = $("#tgl_kaskecil").val();
            var keterangan = $("#keterangan").val();
            var jumlah = $("#jumlah").val();
            var kode_akun = $("#kode_akun").val();
            var kode_cabang = $('#frmInputkaskecil').find('#kode_cabang').val();
            var inout = $("input[name='inout']:checked").val();
            var peruntukan = $("input[name='peruntukan']:checked").val();
            $.ajax({
                type: 'POST'
                , url: '/kaskecil/storetemp'
                , data: {
                    _token: "{{ csrf_token(); }}"
                    , kode_cabang: kode_cabang
                    , nobukti: nobukti
                    , tgl_kaskecil: tgl_kaskecil
                    , keterangan: keterangan
                    , jumlah: jumlah
                    , kode_akun: kode_akun
                    , inout: inout
                    , peruntukan: peruntukan
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 0) {
                        swal("Success", "Data Berhasil Disimpan", "success");
                    } else {
                        swal("Success", "Data Gagal Disimpan", "success");
                    }
                }
            });
        }
        $("#tambahitem").click(function() {
            var nobukti = $('#frmInputkaskecil').find('#nobukti').val();
            var tgl_kaskecil = $("#tgl_kaskecil").val();
            var keterangan = $("#keterangan").val();
            var jumlah = $("#jumlah").val();
            var kode_akun = $("#kode_akun").val();
            var kode_cabang = $('#frmInputkaskecil').find('#kode_cabang').val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            if (cektutuplaporan == 1) {
                swal({
                    title: 'Oops'
                    , text: 'Laporan Sudah Di Tutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#frmInputkaskecil').find('#kode_cabang').focus();
                });
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#frmInputkaskecil').find('#kode_cabang').focus();
                });
            } else if (nobukti == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Bukti Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#frmInputkaskecil').find('#nobukti').focus();
                });
            } else if (tgl_kaskecil == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_kaskecil").focus();
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
            } else {
                simpantemp();
                loadkaskeciltemp();
            }

        });

        $("#frmInputkaskecil").submit(function() {


        });

    });

</script>
