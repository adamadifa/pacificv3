<form action="#" method="POST" id="frmPotongan">
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Qty" field="qty_potongan" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Harga" field="harga_potongan" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Total" field="total_potongan" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_akun" id="kode_akun" class="form-control select2">
                    <option value="">Kode Akun</option>
                    @foreach ($coa as $d)
                    <option value="{{ $d->kode_akun }}"><b>{{ $d->kode_akun }}</b> - {{ $d->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <a href="#" id="simpanpotongan" class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i> Submit</a>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    var h = document.getElementById('harga_potongan');
    h.addEventListener('keyup', function(e) {
        h.value = formatRupiah(this.value, '');
        //alert(b);
    });

    var q = document.getElementById('qty_potongan');
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
            var qty = $("#frmPotongan").find("#qty_potongan").val();
            var harga = $("#frmPotongan").find("#harga_potongan").val();
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


            $("#total_potongan").val(total_4);

        }

        function loadtotal2() {
            var grandtotal = $("#grandtotaltemp").text();
            var grandtotalpotongan = $("#grandtotalpotongan").text();

            if (grandtotal.length === 0) {
                var grandtotal_1 = 0;
                var grandtotal_2 = 0;
            } else {
                var grandtotal_1 = grandtotal.replace(/\./g, '');
                var grandtotal_2 = grandtotal_1.replace(/\,/g, '.');

            }

            if (grandtotalpotongan.length === 0) {
                var grandtotalpotongan_1 = 0;
                var grandtotalpotongan_2 = 0;
            } else {
                var grandtotalpotongan_1 = grandtotalpotongan.replace(/\./g, '');
                var grandtotalpotongan_2 = grandtotalpotongan_1.replace(/\,/g, '.');

            }


            var grandAll = parseFloat(grandtotal_2) - parseFloat(grandtotalpotongan_2);
            var total_1 = addCommas(grandAll.toFixed(2));
            var total_2 = total_1.replace(/\./g, '-');
            var total_3 = total_2.replace(/\,/g, '.');
            var total_4 = total_3.replace(/\-/g, ',');
            $("#grandtotal").text(total_4);
        }
        $("#qty_potongan, #harga_potongan").on('keyup', function() {
            loadtotal();
        });
        //alert('test');


        function loaddetailpotongan() {
            var nobukti_pembelian = $("#nobukti_pembelian").val();
            $.ajax({
                type: 'POST'
                , url: '/pembelian/showdetailpotongan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pembelian: nobukti_pembelian
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailpotongan").html(respond);
                    loadtotal2();
                }
            });
        }

        $("#simpanpotongan").click(function(e) {
            e.preventDefault();
            var nobukti_pembelian = "{{ $nobukti_pembelian }}";
            var keterangan = $("#frmPotongan").find("#keterangan").val();
            var qty = $("#frmPotongan").find("#qty_potongan").val();
            var harga = $("#frmPotongan").find("#harga_potongan").val();
            var kode_akun = $("#frmPotongan").find("#kode_akun").val();
            if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmPotongan").find("#keterangan").focus();
                });
            } else if (qty == "") {
                swal({
                    title: 'Oops'
                    , text: 'Qty Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmPotongan").find("#qty").focus();
                });
            } else if (harga == "") {
                swal({
                    title: 'Oops'
                    , text: 'Harga Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmPotongan").find("#harga_potongan").focus();
                });
            } else if (kode_akun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Harga Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmPotongan").find("#kode_akun").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/pembelian/storepotongan'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , nobukti_pembelian: nobukti_pembelian
                        , keterangan: keterangan
                        , qty: qty
                        , harga: harga
                        , kode_akun: kode_akun
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 0) {
                            swal("Berhasil", "Data Berhasil Disimpan", "success");
                        } else {
                            swal("Oops", "Data Gagal Disimpan, Hu", "warning");

                        }
                        loaddetailpotongan();
                        $("#mdlinputpotongan").modal("hide");
                    }
                , });
            }
        });

    });

</script>
