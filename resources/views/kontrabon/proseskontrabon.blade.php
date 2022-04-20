<table class="table">
    <tr>
        <td>No. Kontrabon</td>
        <td>{{ $kontrabon->no_kontrabon }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ DateToIndo2($kontrabon->tgl_kontrabon) }}</td>
    </tr>
    <tr>
        <td>Terima Dari</td>
        <td>{{ $kontrabon->nama_supplier }}</td>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>No. Bukti</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @php
        $totalkontrabon = 0;
        @endphp
        @foreach ($detailkontrabon as $d)
        @php
        $totalkontrabon += $d->jmlbayar;
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ date("d-m-Y",strtotime($d->tgl_pembelian)) }}</td>
            <td><a href="#" class="detailpembelian" nobukti_pembelian="{{ $d->nobukti_pembelian }}">{{ $d->nobukti_pembelian }}</a></td>
            <td class="text-right">{{ desimal($d->jmlbayar) }}</td>
        </tr>
        @endforeach
        <tr class="thead-dark">
            <th colspan="3">TOTAL</th>
            <th class="text-right">{{ desimal($totalkontrabon) }}</th>
        </tr>
    </tbody>
</table>
<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th colspan="9">Data Pembelian <span id="nobuktipembelian"></span></th>
        </tr>
        <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Keterangan</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Subtotal</th>
            <th>Penyesuaian</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody id="loaddetailpembelian"></tbody>
</table>
<form action="/kontrabon/storeproseskontrabon" method="POST" id="frmKontrabon">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <input type="hidden" value="{{ $kontrabon->no_kontrabon }}" name="no_kontrabon" id="no_kontrabon">
                    <input type="hidden" value="{{ $totalkontrabon }}" name="jmlbayar" id="jmlbayar">
                    <input type="hidden" value="{{ $kontrabon->kode_supplier }}" name="kode_supplier" id="kode_supplier">
                    <x-inputtext label="Tanggal Bayar" field="tglbayar" icon="feather icon-calendar" datepicker />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="kode_bank" id="kode_bank" class="form-control select2">
                            <option value="">Pilih Bank</option>
                            @foreach ($bank as $d)
                            <option value="{{ $d->kode_bank }}">{{ $d->nama_bank }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select class="form-control" id="kode_akun" name="kode_akun" data-error=".errorTxt1">
                            <option value="">Pilih Akun</option>
                            <option value="2-1300">Hutang Lainnya</option>
                            <option value="2-1200">Hutang Dagang</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-12">
                    <div class="vs-checkbox-con vs-checkbox-primary">
                        <input type="checkbox" class="cekcabang" name="cekcabang" value="1">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">Dibayar Oleh Cabang ?</span>
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
            <div class="row" id="nobkk">
                <div class="col-12">
                    <x-inputtext label="No. BKK" field="no_bkk" icon="fa fa-file" />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <x-inputtext label="Keterangan" field="keterangan" icon="fa fa-file" />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    $(function() {
        function loaddetailpembelian(nobukti_pembelian) {
            $.ajax({
                type: 'POST'
                , url: '/pembelian/showdetailpembeliankontrabon'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pembelian: nobukti_pembelian
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailpembelian").html(respond);
                }
            });
        }

        $(".detailpembelian").click(function() {
            var nobukti_pembelian = $(this).attr("nobukti_pembelian");
            $("#nobuktipembelian").text(nobukti_pembelian);
            loaddetailpembelian(nobukti_pembelian);
        });
        $("#pilihcabang").hide();
        $('.cekcabang').change(function() {
            if (this.checked) {
                $("#pilihcabang").show();
            } else {
                $("#pilihcabang").hide();
            }
        });

        $("#kode_bank").change(function() {
            loadnobkk();
        });

        function loadnobkk() {
            var kode_bank = $("#kode_bank").val();
            if (kode_bank == "KAS KECIL") {
                $("#nobkk").show();
            } else {
                $("#nobkk").hide();
            }
        }
        loadnobkk();
        $("#frmKontrabon").submit(function() {
            var tglbayar = $("#tglbayar").val();
            var kode_bank = $("#kode_bank").val();
            var kode_akun = $("#kode_akun").val();
            var keterangan = $("#keterangan").val();
            var kode_cabang = $("#kode_cabang").val();
            var no_bkk = $("#no_bkk").val();
            if (tglbayar == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Bayar Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tglbayar").focus();
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

            } else if (kode_akun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Akun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_akun").focus();
                });
                return false;

            } else if ($(".cekcabang").is(':checked') && kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Pilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
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

            } else if (kode_bank == "KAS KECIL" && kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
                return false;

            } else if (kode_bank == "KAS KECIL" && no_bkk == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. BKK Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_bkk").focus();
                });
                return false;

            }
        });
    });

</script>
