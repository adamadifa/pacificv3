<form action="/setoranpenjualan/store" method="POST" id="frmSetoranpenjualan">
    @csrf
    <input type="hidden" id="cektutuplaporan">
    <input type="hidden" id="ceksetoran" name="ceksetoran">
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal LHP" field="tgl_lhp" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        @if (Auth::user()->kode_cabang =="PCF")
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
        <input type="hidden" name="kode_cabang" id="kode_cabang" value="{{ Auth::user()->kode_cabang }}">
        @endif
    </div>
    <div class="row" id="pilihsalesman">
        <div class="col-12">
            <div class="form-group  ">
                <select name="id_karyawan" id="id_karyawan" class="form-control">
                    <option value="">Pilih Salesman</option>
                </select>
            </div>
        </div>
    </div>
    <div class="divider divider-left">
        <div class="divider-text"><i class="feather icon-file mr-1"></i>LHP</div>
    </div>
    <div class="row">
        <div class="col-12">
            <input type="hidden" id="lhp_tunai" name="lhp_tunai">
            <input type="hidden" id="lhp_tagihan" name="lhp_tagihan">
            <input type="hidden" id="total_lhp" name="total_lhp">
            <table class="table table-bordered">
                <tr>
                    <td style="font-weight: bold">Tunai</td>
                    <td id="lhp_tunai_text" class="text-right success" style="font-weight: bold"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Tagihan</td>
                    <td id="lhp_tagihan_text" class="text-right success" style="font-weight: bold"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Total</td>
                    <td id="total_lhp_text" class="text-right success" style="font-weight: bold"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="divider divider-left">
        <div class="divider-text"><i class="feather icon-file mr-1"></i>SETORAN</div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Setoran Uang Kertas" field="setoran_kertas" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Setoran Uang Logam" field="setoran_logam" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <input type="hidden" id="setoran_bg" name="setoran_bg">
            <input type="hidden" id="setoran_transfer" name="setoran_transfer">
            <input type="hidden" id="total_setoran" name="total_setoran">
            <input type="hidden" id="selisih" name="selisih">
            <input type="hidden" id="girotocash" name="girotocash">
            <input type="hidden" id="girototransfer" name="girototransfer">
            <table class="table table-bordered">
                <tr>
                    <td style="font-weight: bold">Setoran Giro</td>
                    <td id="setoran_bg_text" class="text-right danger" style="font-weight: bold"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Setoran Transfer</td>
                    <td id="setoran_transfer_text" class="text-right danger" style="font-weight: bold"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Total</td>
                    <td id="total_setoran_text" class="text-right danger" style="font-weight: bold"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Selisih</td>
                    <td id="selisih_text" class="text-right danger" style="font-weight: bold"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Ganti Giro Ke Cash</td>
                    <td id="girotocash_text" class="text-right warning" style="font-weight: bold"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Ganti Giro Ke Transfer</td>
                    <td id="girototransfer_text" class="text-right warning" style="font-weight: bold"></td>
                </tr>
            </table>
        </div>

    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block">Submit</button>
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
            var tanggal = $("#tgl_lhp").val();
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "penjualan"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#frmSetoranpenjualan").find("#cektutuplaporan").val(respond);
                }
            });
        }

        cektutuplaporan();

        function loadsalesmancabang(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#frmSetoranpenjualan").find('#id_karyawan').html(respond);
                }
            });
        }


        function addCommas(nStr) {
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + '.' + '$2');
            }
            return x1 + x2;
        }

        function loadselisih() {
            var total_lhp = $("#total_lhp").val();
            var total_setoran = $("#total_setoran").val();
            var totallhp = total_lhp.replace(/\./g, '');
            var totalsetoran = total_setoran.replace(/\./g, '');
            var selisih = parseInt(totalsetoran) - parseInt(totallhp);
            $("#selisih_text").text(addCommas(selisih));
        }

        function ceksetoran() {
            var tgl_lhp = $("#tgl_lhp").val();
            var id_karyawan = $("#frmSetoranpenjualan").find('#id_karyawan').val();
            $.ajax({
                type: 'POST'
                , url: '/setoranpenjualan/ceksetoran'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tgl_lhp: tgl_lhp
                    , id_karyawan: id_karyawan
                }
                , cache: false
                , success: function(respond) {
                    $("#ceksetoran").val(respond);
                }
            });
        }

        function loadtotalsetoran() {

            var setoran_kertas = $("#setoran_kertas").val();
            var setoran_logam = $("#setoran_logam").val();
            var setoran_bg = $("#setoran_bg").val();
            var setoran_transfer = $("#setoran_transfer").val();

            var setorankertas = setoran_kertas.replace(/\./g, '');
            var setoranlogam = setoran_logam.replace(/\./g, '');
            var setoranbg = setoran_bg.replace(/\./g, '');
            var setorantransfer = setoran_transfer.replace(/\./g, '');

            if (setorankertas == "") {
                var setorankertas = 0;
            }

            if (setoranlogam == "") {
                var setoranlogam = 0;
            }

            if (setoranbg == "") {
                var setoranbg = 0;
            }

            if (setorantransfer == "") {
                var setorantransfer = 0;
            }
            var totalsetoran = parseInt(setorankertas) + parseInt(setoranlogam) + parseInt(setoranbg) + parseInt(setorantransfer);
            $("#total_setoran").val(totalsetoran);
            $("#total_setoran_text").text(addCommas(totalsetoran));
        }

        $("#setoran_kertas, #setoran_logam").maskMoney();

        $("#setoran_kertas, #setoran_logam").keyup(function() {
            loadtotalsetoran();
            loadselisih();
        });


        $("#setoran_kertas, #setoran_logam").css("color", "red");

        function loadlhp() {
            var tgl_lhp = $("#tgl_lhp").val();
            var id_karyawan = $("#frmSetoranpenjualan").find('#id_karyawan').val();
            $.ajax({
                type: 'POST'
                , url: '/setoranpenjualan/getsetoranpenjualan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tgl_lhp: tgl_lhp
                    , id_karyawan: id_karyawan
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    datasetoran = respond.split("|");
                    //LHP
                    $("#lhp_tunai_text").text(datasetoran[0]);
                    $("#lhp_tunai").val(datasetoran[0]);
                    $("#lhp_tagihan_text").text(datasetoran[1]);
                    $("#lhp_tagihan").val(datasetoran[1]);
                    $("#total_lhp_text").text(datasetoran[7]);
                    $("#total_lhp").val(datasetoran[7]);

                    //Setoran
                    $("#setoran_bg_text").text(datasetoran[2]);
                    $("#setoran_bg").val(datasetoran[2]);
                    $("#setoran_transfer_text").text(datasetoran[4]);
                    $("#setoran_transfer").val(datasetoran[4]);
                    $("#total_setoran_text").text(datasetoran[5]);
                    $("#total_setoran").val(datasetoran[5]);
                    $("#girotocash_text").text(datasetoran[3]);
                    $("#girotocash").val(datasetoran[3]);
                    $("#girototransfer_text").text(datasetoran[6]);
                    $("#girototransfer").val(datasetoran[6]);

                    loadselisih();
                }
            });
        }

        $("#kode_cabang,#id_karyawan,#tgl_lhp").change(function(e) {
            loadlhp();
            cektutuplaporan();
            ceksetoran();
        });

        $("#frmSetoranpenjualan").find('#kode_cabang').change(function(e) {
            var kode_cabang = $(this).val();
            loadsalesmancabang(kode_cabang);
        });

        $("#frmSetoranpenjualan").submit(function() {
            var tgl_lhp = $("#tgl_lhp").val();
            var kode_cabang = $("#frmSetoranpenjualan").find('#kode_cabang').val();
            var id_karyawan = $("#frmSetoranpenjualan").find('#id_karyawan').val();
            var total_lhp = $("#total_lhp").val();
            var cektutuplaporan = $("#frmSetoranpenjualan").find("#cektutuplaporan").val();
            if (cektutuplaporan > 0) {
                swal({
                    title: 'Oops'
                    , text: 'Periode Laporan Sudah Ditutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_lhp").focus();
                });

                return false;
            } else if (tgl_lhp == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_lhp").focus();
                });

                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });

                return false;
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#id_karyawan").focus();
                });

                return false;
            } else if (total_lhp == "" || total_lhp == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Tidak Ada Transaksi Untuk Salesman Ini Pada Tanggal Tersebut !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_lhp").focus();
                });

                return false;
            } else if (ceksetoran > 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data Sudah Ada !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_lhp").focus();
                });

                return false;
            }
        });
    });

</script>
