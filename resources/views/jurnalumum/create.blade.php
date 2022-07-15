<form action="/jurnalumum/store" method="post" id="frmjurnalumum">
    @csrf
    <input type="hidden" id="cektutuplaporan">
    <input type="hidden" id="cektemp">
    <div class="row">
        <div class="col-12">
            <x-inputtext field="tanggal" label="Tanggal Jurnal Umum" icon="feather icon-calendar" datepicker />
        </div>
    </div>

    @if ($level=="hrd")
    <input type="hidden" name="kode_dept" id="kode_dept" value="HRD" />
    @elseif($level=="general affair")
    <input type="hidden" name="kode_dept" id="kode_dept" value="GAF" />
    @elseif($level=="spv accounting" || $level=="manager accounting")
    <input type="hidden" name="kode_dept" id="kode_dept" value="ACC" />
    @else
    <input type="hidden" name="kode_dept" id="kode_dept" value="ADM" />
    @endif


    <div class="row">
        <div class="col-5">
            <div class="form-group">
                <select name="kode_akun" id="kode_akun" class="form-control select2">
                    <option value="">Pilih Akun</option>
                    @foreach ($coa as $d)
                    <option value="{{ $d->kode_akun }}">{{ $d->kode_akun }} {{ $d->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-7">
            <x-inputtext label="Jumlah" field="jumlah" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
        </div>
    </div>
    <div class="form-group">
        <ul class="list-unstyled mb-0">
            <li class="d-inline-block mr-2">
                <fieldset>
                    <div class="vs-radio-con vs-radio-success">
                        <input type="radio" name="status_dk" checked value="D">
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
                    <div class="vs-radio-con vs-radio-danger">
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
                                <input type="radio" class="peruntukan" name="peruntukan" value="MP" checked>
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
                <a href="#" id="tambahitem" class="btn btn-primary btn-block"><i class="fa fa-plus mr-1"></i>Tambah Item</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-hover-animation">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode Akun</th>
                        <th>Nama Akun</th>
                        <th>Keterangan</th>
                        <th>Debet</th>
                        <th>Kredit</th>
                        <th>Peruntukan</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody id="loadtemp"></tbody>
            </table>
        </div>
    </div>
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
                <button class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    var h = document.getElementById('jumlah');
    h.addEventListener('keyup', function(e) {
        h.value = formatRupiah(this.value, '');
        //alert(b);
    });

    /* Fungsi formatRupiah */
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d-]/g, '').toString()
            , split = number_string.split(',')
            , sisa = split[0].length % 3
            , rupiah = split[0].substr(0, sisa)
            , ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
    }

    function convertToRupiah(angka) {
        var rupiah = '';
        var angkarev = angka.toString().split('').reverse().join('');
        for (var i = 0; i < angkarev.length; i++)
            if (i % 3 == 0) rupiah += angkarev.substr(i, 3) + '.';
        return rupiah.split('', rupiah.length - 1).reverse().join('');
    }

</script>
<script>
    $(function() {

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
        // function cektutuplaporan() {
        //     var tgltransaksi = $("#tgl_jurnalumum").val();
        //     $.ajax({
        //         type: "POST"
        //         , url: "/cektutuplaporan"
        //         , data: {
        //             _token: "{{ csrf_token() }}"
        //             , tanggal: tgltransaksi
        //             , jenislaporan: "pembelian"
        //         }
        //         , cache: false
        //         , success: function(respond) {
        //             console.log(respond);
        //             $("#cektutuplaporan").val(respond);
        //         }
        //     });
        // }

        // $("#tgl_jurnalumum").change(function() {
        //     cektutuplaporan();
        // });

        function loadtemp() {
            $("#loadtemp").load("/jurnalumum/showtemp");
            cektemp();
        }

        loadtemp();




        function cektemp() {
            var kode_dept = $("#frmjurnalumum").find("#kode_dept").val();
            $.ajax({
                type: 'POST'
                , url: '/jurnalumum/cektemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_dept: kode_dept
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektemp").val(respond);
                }
            });
        }
        $("#tambahitem").click(function() {
            var tanggal = $("#tanggal").val();
            var kode_dept = $("#frmjurnalumum").find("#kode_dept").val();
            var kode_akun = $("#frmjurnalumum").find("#kode_akun").val();
            var jumlah = $("#frmjurnalumum").find("#jumlah").val();
            var status_dk = $("input[name='status_dk']:checked").val();
            var peruntukan = $("input[name='peruntukan']:checked").val();
            var kode_cabang = $("#frmjurnalumum").find("#kode_cabang").val();
            var keterangan = $("#frmjurnalumum").find("#keterangan").val();
            if (tanggal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tanggal").focus();
                });
            } else if (kode_akun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Akun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmjurnalumum").find("#kode_akun").focus();
                });
            } else if (jumlah == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmjurnalumum").find("#jumlah").focus();
                });
            } else if (peruntukan == "PC" && kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#frmjurnalumum').find('#kode_cabang').focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/jurnalumum/storetemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , tanggal: tanggal
                        , kode_dept: kode_dept
                        , kode_akun: kode_akun
                        , status_dk: status_dk
                        , peruntukan: peruntukan
                        , kode_cabang: kode_cabang
                        , jumlah: jumlah
                        , keterangan: keterangan
                    }
                    , cache: false
                    , success: function(respond) {
                        console.log(respond);
                        if (respond == 0) {
                            swal({
                                title: 'Success'
                                , text: 'Data Berhasil Disimpan !'
                                , icon: 'success'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#frmjurnalumum").find("#kode_akun").focus();
                                //$("#frmjurnalumum").find("#kode_akun").val("").change();
                                $("#jumlah").val("");
                                loadtemp();
                            });
                        }
                    }
                });
            }
        });

        $("#frmjurnalumum").submit(function(e) {
            var tanggal = $("#tanggal").val();
            var kode_dept = $("#frmjurnalumum").find("#kode_dept").val();
            var cektemp = $("#frmjurnalumum").find("#cektemp").val();
            var keterangan = $("#keterangan").val();
            // var cektutuplaporan = $("#cektutuplaporan").val();
            // if (cektutuplaporan > 0) {
            //     swal({
            //         title: 'Oops'
            //         , text: 'Laporan Periode Ini Sudah Ditutup !'
            //         , icon: 'warning'
            //         , showConfirmButton: false
            //     }).then(function() {
            //         $("#tgl_jurnalumum").focus();
            //     });
            //     return false;
            // } else
            if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangna Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmjurnalumum").find("#kode_dept").focus();
                });
                return false;
            } else if (cektemp == "" || cektemp == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data Masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmjurnalumum").find("#kode_akun").focus();
                });
                return false;
            }
        });
    });

</script>
