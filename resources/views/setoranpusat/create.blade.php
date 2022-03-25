<form method="POST" action="/setoranpusat/store" id="frmSetoranpusat">
    @csrf
    <input type="hidden" id="cektutuplaporan">
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Setoran" field="tgl_setoranpusat" icon="feather icon-calendar" datepicker />
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
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="form-group">
                <select name="kode_bank" id="kode_bank" class="form-control">
                    <option value="">Pilih Bank</option>
                    @foreach ($bank as $d)
                    <option {{ Request('kode_bank')==$d->kode_bank ? 'selected' :'' }} value="{{ $d->kode_bank }}">{{ $d->nama_bank }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Setoran Uang Kertas" field="uang_kertas" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Setoran Uang Logam" field="uang_logam" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <input type="hidden" id="totalsetoran" name="total_setoran">
            <table class="table">
                <tr>
                    <td style="font-weight: bold">TOTAL</td>
                    <td class="text-right" style="font-weight: bold" id="totalsetoran_text"></td>
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
            var tanggal = $("#tgl_setoranpusat").val();
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
                    $("#frmSetoranpusat").find("#cektutuplaporan").val(respond);
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

        function loadtotalsetoran() {

            var uang_kertas = $("#uang_kertas").val();
            var uang_logam = $("#uang_logam").val();


            var uangkertas = uang_kertas.replace(/\./g, '');
            var uanglogam = uang_logam.replace(/\./g, '');


            if (uangkertas == "") {
                var uangkertas = 0;
            }

            if (uanglogam == "") {
                var uanglogam = 0;
            }


            var totalsetoran = parseInt(uangkertas) + parseInt(uanglogam);
            $("#totalsetoran").val(totalsetoran);
            $("#totalsetoran_text").text(addCommas(totalsetoran));
        }

        $("#uang_kertas, #uang_logam").maskMoney();

        $("#uang_kertas, #uang_logam").keyup(function() {
            loadtotalsetoran();
        });

        $("#tgl_setoranpusat").change(function() {
            cektutuplaporan();
        });
        $("#frmSetoranpusat").submit(function() {
            var tgl_setoranpusat = $("#tgl_setoranpusat").val();
            var kode_cabang = $("#frmSetoranpusat").find("#kode_cabang").val();
            var kode_bank = $("#frmSetoranpusat").find("#kode_bank").val();
            var uang_kertas = $("#uang_kertas").val();
            var uang_logam = $("#uang_logam").val();
            var totalsetoran = $("#totalsetoran").val();
            var keterangan = $("#keterangan").val();
            var cektutuplaporan = $("#frmSetoranpusat").find("#cektutuplaporan").val();
            if (cektutuplaporan > 0) {
                swal({
                    title: 'Oops'
                    , text: 'Periode Laporan Sudah Ditutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_setoranpusat").focus();
                });

                return false;
            } else if (tgl_setoranpusat == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_setoranpusat").focus();
                });

                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });

                return false;
            } else if (kode_bank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_bank").focus();
                });

                return false;
            } else if (totalsetoran == "" || totalsetoran == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Setoran Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#uang_kertas").focus();
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
            }
        });


    });

</script>
