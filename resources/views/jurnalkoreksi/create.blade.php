<form action="/jurnalkoreksi/store" method="post" id="frmJurnalkoreksi">
    @csrf
    <input type="hidden" id="cektutuplaporan">
    <div class="row">
        <div class="col-12">
            <x-inputtext field="tgl_jurnalkoreksi" label="Tanggal Jurnal Koreksi" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_supplier" id="kode_supplier" class="form-control select2">
                    <option value="">Semua Supplier</option>
                    @foreach ($supplier as $d)
                    <option value="{{ $d->kode_supplier }}">{{ $d->nama_supplier }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="nobukti_pembelian" id="nobukti_pembelian" class="form-control select2">
                    <option value="">Pilih No. Bukti Pembelian</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_barang" id="kode_barang" class="form-control select2">
                    <option value="">Pilih Barang</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Qty" field="qty" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Harga" field="harga" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Total" field="total" icon="feather icon-file" right readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <table class="table">
                    <tr>
                        <td>Debet</td>
                        <td>
                            <select name="kode_akun_debet" id="kode_akun_debet" class="form-control select2">
                                <option value="">Pilih Akun</option>
                                @foreach ($coa as $d)
                                <option value="{{ $d->kode_akun }}">{{ $d->kode_akun }} {{ $d->nama_akun }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Kredit</td>
                        <td>
                            <select name="kode_akun_kredit" id="kode_akun_kredit" class="form-control select2">
                                <option value="">Pilih Akun</option>
                                @foreach ($coa as $d)
                                <option value="{{ $d->kode_akun }}">{{ $d->kode_akun }} {{ $d->nama_akun }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
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
    var h = document.getElementById('harga');
    h.addEventListener('keyup', function(e) {
        h.value = formatRupiah(this.value, '');
        //alert(b);
    });

    var q = document.getElementById('qty');
    q.addEventListener('keyup', function(e) {
        q.value = formatRupiah(this.value, '');
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

        function cektutuplaporan() {
            var tgltransaksi = $("#tgl_jurnalkoreksi").val();
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tgltransaksi
                    , jenislaporan: "pembelian"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }

        $("#tgl_jurnalkoreksi").change(function() {
            cektutuplaporan();
        });


        $("#kode_supplier").change(function() {
            var kode_supplier = $(this).val();
            $("#nobukti_pembelian").load("/pembelian/" + kode_supplier + "/getpembelianjurnalkoreksi");
        });

        $("#nobukti_pembelian").change(function() {
            var nobukti_pembelian = $(this).val();
            $("#kode_barang").load("/pembelian/" + nobukti_pembelian + "/getbarangjurnalkoreksi");
        });

        function addCommas(nStr) {
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }

        function loadtotal() {
            var qty = $("#qty").val();
            var harga = $("#harga").val();
            if (qty.length === 0) {
                var qty_1 = 0;
                var qty_2 = 0;
            } else {
                var qty_1 = qty.replace(/\./g, '');
                var qty_2 = qty.replace(/\,/g, '.');

            }


            if (harga.length === 0) {
                var harga_1 = 0;
                var harga_2 = 0;
            } else {
                var harga_1 = harga.replace(/\./g, '');
                var harga_2 = harga_1.replace(/\,/g, '.');

            }



            var total = parseFloat(qty_2) * parseFloat(harga_2);
            var total_1 = addCommas(total.toFixed(2));
            var total_2 = total_1.replace(/\./g, '-');
            var total_3 = total_2.replace(/\,/g, '.');
            var total_4 = total_3.replace(/\-/g, ',');


            $("#total").val(total_4);

        }

        $("#qty, #harga").on('keyup', function() {
            loadtotal();
        });

        $("#frmJurnalkoreksi").submit(function(e) {
            var tgl_jurnalkoreksi = $("#tgl_jurnalkoreksi").val();
            var kode_supplier = $("#kode_supplier").val();
            var nobukti_pembelian = $("#nobukti_pembelian").val();
            var kode_barang = $("#kode_barang").val();
            var keterangan = $("#keterangan").val();
            var qty = $("#qty").val();
            var harga = $("#harga").val();
            var kode_akun_debet = $("#kode_akun_debet").val();
            var kode_akun_kredit = $("#kode_akun_kredit").val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            if (cektutuplaporan > 0) {
                swal({
                    title: 'Oops'
                    , text: 'Laporan Periode Ini Sudah Ditutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_jurnalkoreksi").focus();
                });
                return false;
            } else if (tgl_jurnalkoreksi == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_jurnalkoreksi").focus();
                });
                return false;
            } else if (kode_supplier == "") {
                swal({
                    title: 'Oops'
                    , text: 'Supplier Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_supplier").focus();
                });
                return false;
            } else if (nobukti_pembelian == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Bukti Pembelian Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nobukti_pembelian").focus();
                });
                return false;
            } else if (kode_barang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Barang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_barang").focus();
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
            } else if (qty == "") {
                swal({
                    title: 'Oops'
                    , text: 'Qty Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#qty").focus();
                });
                return false;
            } else if (harga == "") {
                swal({
                    title: 'Oops'
                    , text: 'Harga Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#harga").focus();
                });
                return false;
            } else if (kode_akun_debet == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Akun Debet Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_akun_debet").focus();
                });
                return false;
            } else if (kode_akun_kredit == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Akun Kredit Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_akun_kredit").focus();
                });
                return false;
            }
        });
    });

</script>
