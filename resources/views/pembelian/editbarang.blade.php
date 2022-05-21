<form action="#" method="POST" id="frmEditbarang">
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Kode Barang" field="kode_barang" icon="feather icon-credit-card" value="{{ $detailpembelian->kode_barang }}" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama Barang" field="nama_barang" icon="feather icon-box" value="{{ $detailpembelian->nama_barang }}" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" value="{{ $detailpembelian->keterangan }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <x-inputtext label="Qty" field="qty" icon="feather icon-file" value="{{ desimal($detailpembelian->qty) }}" />
        </div>
        <div class="col-4">
            <x-inputtext label="Harga" field="harga" icon="feather icon-file" value="{{ desimal($detailpembelian->harga) }}" right />
        </div>
        <div class="col-4">
            <x-inputtext label="Penyesuaian" field="penyesuaian" icon="feather icon-file" value="{{ desimal($detailpembelian->penyesuaian) }}" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_akun" id="kode_akun_2" class="form-control akun">
                    <option value="">Kode Akun</option>
                    @foreach ($coa as $d)
                    <option {{ $detailpembelian->kode_akun ==  $d->kode_akun ? 'selected' : '' }} value="{{ $d->kode_akun }}"><b>{{ $d->kode_akun }}</b> - {{ $d->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Konversi Gram" field="konversi_gram" icon="fa fa-balance-scale" value="{{ $detailpembelian->konversi_gram }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <div class="vs-checkbox-con vs-checkbox-primary">
                    <input type="checkbox" class="cabangcheck" name="cabangcheck" value="1" {{ !empty($detailpembelian->kode_cabang) ? 'checked' : '' }}>
                    <span class="vs-checkbox">
                        <span class="vs-checkbox--check">
                            <i class="vs-icon feather icon-check"></i>
                        </span>
                    </span>
                    <span class="">Cabang ?</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-sm-12" id="pilihcabang">
            <div class="form-group">
                <select name="kode_cabang" id="kode_cabang" class="form-control">
                    <option value="">Cabang</option>
                    @foreach ($cabang as $d)
                    <option {{ $detailpembelian->kode_cabang == $d->kode_cabang ?'selected':'' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <a href="#" id="updatebarang" class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</a>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        $('#kode_akun_2').select2({
            dropdownParent: $('#mdleditbarang')
        });
        $("#frmEditbarang").find('.cabangcheck').change(function() {
            if (this.checked) {
                $("#frmEditbarang").find("#pilihcabang").show();
            } else {
                $("#frmEditbarang").find("#pilihcabang").hide();
                $("#frmEditbarang").find("#kode_cabang").val("").change();
            }
        });

        function hidecabang() {
            if ($("#frmEditbarang").find('.cabangcheck').is(':checked')) {
                $("#frmEditbarang").find("#pilihcabang").show();
            } else {
                $("#frmEditbarang").find("#pilihcabang").hide();
                $("#frmEditbarang").find("#kode_cabang").val("").change();
            }
        }

        hidecabang();

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
            //alert(total_4);
            $("#grandtotal").text(total_4);
        }

        function loaddetailpembelian() {
            var nobukti_pembelian = $("#nobukti_pembelian").val();
            $.ajax({
                type: 'POST'
                , url: '/pembelian/showdetailpembelian'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pembelian: nobukti_pembelian
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailpembelian").html(respond);
                    loadtotal();
                }
            });
        }
        $("#updatebarang").click(function(e) {
            e.preventDefault();
            var nobukti_pembelian = "{{ $detailpembelian->nobukti_pembelian }}";
            var kode_barang = $("#frmEditbarang").find("#kode_barang").val();
            var kode_akun = $("#frmEditbarang").find("#kode_akun").val();
            var keterangan = $("#frmEditbarang").find("#keterangan").val();
            var qty = $("#frmEditbarang").find("#qty").val();
            var harga = $("#frmEditbarang").find("#harga").val();
            var penyesuaian = $("#frmEditbarang").find("#penyesuaian").val();
            var kode_cabang = $("#frmEditbarang").find("#kode_cabang").val();
            var no_urut = "{{ $detailpembelian->no_urut }}";
            var konversi_gram = $("#konversi_gram").val();
            if (kode_akun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Akun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmEditbarang").find("#kode_akun").focus();
                });
            } else if ($("#frmEditbarang").find('.cabangcheck').is(':checked') && kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/pembelian/updatebarang'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , nobukti_pembelian: nobukti_pembelian
                        , kode_barang: kode_barang
                        , keterangan: keterangan
                        , qty: qty
                        , harga: harga
                        , penyesuaian: penyesuaian
                        , kode_akun: kode_akun
                        , kode_cabang: kode_cabang
                        , no_urut: no_urut
                        , konversi_gram: konversi_gram
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 0) {
                            swal("Berhasil", "Data Berhasil Diupdate", "success");
                        } else {
                            swal("Oops", "Data Gagak Diupdate", "warning");
                        }
                        $("#mdleditbarang").modal("hide");
                        loaddetailpembelian();
                    }
                });
            }

        });
    })

</script>
