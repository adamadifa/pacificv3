<form action="#" method="POST" id="frmEditbarang">
    <input type="hidden" id="no_bukti" value="{{ Crypt::encrypt($detailpembelian->nobukti_pembelian) }}">
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Kode Barang" field="kode_barang" icon="feather icon-credit-card"
                value="{{ $detailpembelian->kode_barang }}" readonly />
        </div>
    </div>
    {{-- <input type="hidden" name="kode_barang" value="{{ $detailpembelian->kode_barang }}">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_barang_new" id="kode_barang_2" class="form-control">
                    @foreach ($barang as $d)
                    <option {{ $d->kode_barang == $detailpembelian->kode_barang ?  'selected' : '' }} value="{{ $d->kode_barang }}">{{ $d->kode_barang }} - {{ $d->nama_barang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div> --}}
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama Barang" field="nama_barang" icon="feather icon-box" value="{!! $detailpembelian->nama_barang !!}"
                readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file"
                value="{{ $detailpembelian->keterangan }}" />
        </div>
    </div>
    @if ($cekpembayaran > 0)
        <div class="row">
            <div class="col-4">
                <x-inputtext label="Qty" field="qty_edit" icon="feather icon-file"
                    value="{{ desimal($detailpembelian->qty) }}" readonly />
            </div>
            <div class="col-4">
                <x-inputtext label="Harga" field="harga_edit" icon="feather icon-file"
                    value="{{ desimal($detailpembelian->harga) }}" right readonly />
            </div>
            <div class="col-4">
                <x-inputtext label="Penyesuaian" field="penyesuaian_edit" icon="feather icon-file"
                    value="{{ desimal($detailpembelian->penyesuaian) }}" right readonly />
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-4">
                <x-inputtext label="Qty" field="qty_edit" icon="feather icon-file"
                    value="{{ desimal($detailpembelian->qty) }}" />
            </div>
            <div class="col-4">
                <x-inputtext label="Harga" field="harga_edit" icon="feather icon-file"
                    value="{{ desimal($detailpembelian->harga) }}" right />
            </div>
            <div class="col-4">
                <x-inputtext label="Penyesuaian" field="penyesuaian_edit" icon="feather icon-file"
                    value="{{ desimal($detailpembelian->penyesuaian) }}" right />
            </div>
        </div>
    @endif
    <input type="hidden" id="total_edit"
        value="{{ $detailpembelian->qty * $detailpembelian->harga + $detailpembelian->penyesuaian }}">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_akun" id="kode_akun" class="form-control akun">
                    <option value="">Kode Akun</option>
                    @foreach ($coa as $d)
                        <option {{ $detailpembelian->kode_akun == $d->kode_akun ? 'selected' : '' }}
                            value="{{ $d->kode_akun }}"><b>{{ $d->kode_akun }}</b> - {{ $d->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Konversi Gram" field="konversi_gram" icon="fa fa-balance-scale"
                value="{{ $detailpembelian->konversi_gram }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <div class="vs-checkbox-con vs-checkbox-primary">
                    <input type="checkbox" class="cabangcheck" name="cabangcheck" value="1"
                        {{ !empty($detailpembelian->kode_cabang) ? 'checked' : '' }}>
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
                        <option {{ $detailpembelian->kode_cabang == $d->kode_cabang ? 'selected' : '' }}
                            value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @php
        $level_split = ['admin', 'staff keuangan', 'spv accounting'];
    @endphp
    @if (in_array(Auth::user()->level, $level_split))
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
                    <div class="form-group">
                        <select name="kode_barang_split" id="kode_barang_split" class="form-control">
                            <option value="">Pilih Barang</option>
                            @foreach ($barang as $d)
                                <option value="{{ $d->kode_barang }}">{{ $d->kode_barang }} - {{ $d->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-4">
                    <x-inputtext label="Qty" field="qty_split" icon="feather icon-file" />
                </div>
                <div class="col-4">
                    <x-inputtext label="Harga" field="harga_split" icon="feather icon-file" right />
                </div>
                <div class="col-4">
                    <x-inputtext label="Penyesuaian" field="penyesuaian_split" icon="feather icon-file" right />
                </div>
            </div>
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
                    <x-inputtext label="Konversi Gram" field="konversi_gram_split" icon="fa fa-balance-scale" />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" class="cabangcheck_split" name="cabangcheck_split"
                                value="1">
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
                <div class="col-lg-12 col-sm-12" id="pilihcabang_split">
                    <div class="form-group">
                        <select name="kode_cabang_split" id="kode_cabang_split" class="form-control">
                            <option value="">Cabang</option>
                            @foreach ($cabang as $d)
                                <option {{ $detailpembelian->kode_cabang == $d->kode_cabang ? 'selected' : '' }}
                                    value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <a href="#" class="btn btn-info btn-block" id="tambahitem"><i
                                class="feather icon-plus"></i>Tambah</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Keterangan</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                                <th>Peny</th>
                                <th>Kode Akun</th>
                                <th>Total</th>
                                <th>Cabang</th>
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
                <a href="#" id="updatebarang" class="btn btn-primary btn-block"><i
                        class="fa fa-send mr-1"></i>Submit</a>
            </div>
        </div>
    </div>
</form>

<script>
    var h = document.getElementById('harga_edit');
    h.addEventListener('keyup', function(e) {
        h.value = formatRupiah(this.value, '');
        //alert(b);
    });

    var h_split = document.getElementById('harga_split');
    h_split.addEventListener('keyup', function(e) {
        h_split.value = formatRupiah(this.value, '');
        //alert(b);
    });

    var p = document.getElementById('penyesuaian_edit');
    p.addEventListener('keyup', function(e) {
        p.value = formatRupiah(this.value, '');
        //alert(b);
    });

    var p_split = document.getElementById('penyesuaian_split');
    p_split.addEventListener('keyup', function(e) {
        p_split.value = formatRupiah(this.value, '');
        //alert(b);
    });

    var q = document.getElementById('qty_edit');
    q.addEventListener('keyup', function(e) {
        q.value = formatRupiah(this.value, '');
        //alert(b);
    });

    var q_split = document.getElementById('qty_split');
    q_split.addEventListener('keyup', function(e) {
        q_split.value = formatRupiah(this.value, '');
        //alert(b);
    });
    /* Fungsi formatRupiah */
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d-]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

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
        $("#kode_barang_2").selectize();
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
        $("#jumlah_split").maskMoney();
        $("#splitakunform").hide();

        $('.split_akun').change(function() {
            if (this.checked) {
                $("#splitakunform").show();
                loadsplit();
            } else {
                $("#splitakunform").hide();
            }

        });

        function loadsplit() {
            var no_bukti = "{{ Crypt::encrypt($detailpembelian->nobukti_pembelian) }}";
            var kode_barang_old = $("#frmEditbarang").find("#kode_barang").val();
            $("#loadsplit").load('/pembelian/' + no_bukti + '/' + kode_barang_old + '/showsplit');
        }

        loadsplit();
        $("#kode_barang_split").selectize();
        $("#kode_akun_split").selectize();

        $("#frmEditbarang").find('.cabangcheck_split').change(function() {
            if (this.checked) {
                $("#frmEditbarang").find("#pilihcabang_split").show();
            } else {
                $("#frmEditbarang").find("#pilihcabang_split").hide();
                $("#frmEditbarang").find("#kode_cabang_split").val("").change();
            }
        });

        function hidecabangsplit() {
            if ($("#frmEditbarang").find('.cabangcheck_split').is(':checked')) {
                $("#frmEditbarang").find("#pilihcabang_split").show();
            } else {
                $("#frmEditbarang").find("#pilihcabang_split").hide();
                $("#frmEditbarang").find("#kode_cabang").val("").change();
            }
        }

        hidecabangsplit();

        $("#tambahitem").click(function(e) {
            e.preventDefault();
            let kode_barang_old = $("#frmEditbarang").find("#kode_barang").val();
            let kode_barang = $("#kode_barang_split").val();
            let qty = $("#qty_split").val();
            let harga = $("#harga_split").val();
            let penyesuaian = $("#penyesuaian_split").val();
            let keterangan = $("#keterangan_split").val();
            let kode_akun = $("#kode_akun_split").val();
            let konversi_gram = $("#konversi_gram_split").val();
            let kode_cabang = $("input[name='kode_cabang_split']:checked").val();
            let no_bukti = "{{ $detailpembelian->nobukti_pembelian }}";


            if (kode_barang == "") {
                swal({
                    title: 'Oops',
                    text: 'Barang Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#kode_barang_split").focus();
                });
            } else if (qty == "") {
                swal({
                    title: 'Oops',
                    text: 'Qty Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#qty_split").focus();
                });
            } else if (harga == "") {
                swal({
                    title: 'Oops',
                    text: 'Harga Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#harga_split").focus();
                });
            } else if (keterangan == "") {
                swal({
                    title: 'Oops',
                    text: 'Keterangan Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#keterangan_split").focus();
                });
            } else if (kode_akun == "") {
                swal({
                    title: 'Oops',
                    text: 'Kode Akun Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#kode_akun_split").focus();
                });
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/pembelian/storesplitakun',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_barang_old: kode_barang_old,
                        kode_barang: kode_barang,
                        qty: qty,
                        harga: harga,
                        penyesuaian: penyesuaian,
                        keterangan: keterangan,
                        konversi_gram: konversi_gram,
                        kode_akun: kode_akun,
                        kode_cabang: kode_cabang,
                        no_bukti: no_bukti,
                    },
                    cache: false,
                    success: function(respond) {
                        if (respond == 0) {
                            swal({
                                title: 'Oops',
                                text: 'Data Berhasil Disimpan !',
                                icon: 'success',
                                showConfirmButton: false
                            }).then(function() {
                                $("#keterangan_split").val('');
                                var $select = $('#kode_akun_split').selectize();
                                var control = $select[0].selectize;
                                control.clear();
                                $("#keterangan_split").focus();
                                $("#qty_split").val('');
                                $("#harga_split").val('');
                                $("#penyesuian_split").val('');
                                $("#konversi_gram_split").val('');
                                loadsplit();
                            });
                        } else {
                            swal({
                                title: 'Oops',
                                text: 'Data Gagal Disimpan, Hubungi Tim IT !',
                                icon: 'error',
                                showConfirmButton: false
                            }).then(function() {
                                $("#keterangan_split").focus();
                            });
                        }
                    }

                });
            }
        });
        // $("#frmEditbarang").find('#kode_akun').selectize();
        $("#frmEditbarang").find('#kode_akun').select2({
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
                type: 'POST',
                url: '/pembelian/showdetailpembelian',
                data: {
                    _token: "{{ csrf_token() }}",
                    nobukti_pembelian: nobukti_pembelian
                },
                cache: false,
                success: function(respond) {
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
            var qty = $("#frmEditbarang").find("#qty_edit").val();
            var harga = $("#frmEditbarang").find("#harga_edit").val();
            var penyesuaian = $("#frmEditbarang").find("#penyesuaian_edit").val();
            var kode_cabang = $("#frmEditbarang").find("#kode_cabang").val();
            var no_urut = "{{ $detailpembelian->no_urut }}";
            var konversi_gram = $("#konversi_gram").val();
            var split = $("input[name='split_akun']:checked").val();
            var jumlah = $("#total_edit").val();
            var totalsplit = $("#totalsplit").val();
            console.log(jumlah);
            if (split == 1 && parseInt(jumlah) != parseInt(totalsplit)) {
                swal({
                    title: 'Oops',
                    text: 'Jumlah Harus Sama !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });

                return false;
            } else if (kode_akun == "") {
                swal({
                    title: 'Oops',
                    text: 'Kode Akun Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#frmEditbarang").find("#kode_akun").focus();
                });
            } else if ($("#frmEditbarang").find('.cabangcheck').is(':checked') && kode_cabang == "") {
                swal({
                    title: 'Oops',
                    text: 'Cabang Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/pembelian/updatebarang',
                    data: {
                        _token: "{{ csrf_token() }}",
                        nobukti_pembelian: nobukti_pembelian,
                        kode_barang: kode_barang,
                        keterangan: keterangan,
                        qty: qty,
                        harga: harga,
                        penyesuaian: penyesuaian,
                        kode_akun: kode_akun,
                        kode_cabang: kode_cabang,
                        no_urut: no_urut,
                        konversi_gram: konversi_gram,
                        split_akun: split
                    },
                    cache: false,
                    success: function(respond) {
                        console.log(respond);
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
