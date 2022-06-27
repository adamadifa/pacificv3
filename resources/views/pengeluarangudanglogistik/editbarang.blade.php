<form action="#" id="frmEdit">
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Kode Barang" field="kode_barang" value="{{ $barang->kode_barang }}" icon="feather icon-box" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama Barang" field="nama_barang" value="{{ $barang->nama_barang }}" icon="feather icon-file" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" value="{{ $barang->keterangan }}" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Qty" field="qty" value="{{ $barang->qty }}" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <div class="vs-checkbox-con vs-checkbox-primary">
                    <input type="checkbox" class="cabangcheck" name="cabangcheck" value="1" {{ !empty($barang->kode_cabang) ? 'checked' : '' }}>
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
    <div class="row" id="pilihcbg">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_cabang" id="kode_cabang" class="form-control">
                    <option value="">Cabang</option>
                    @foreach ($cabang as $d)
                    <option {{ $barang->kode_cabang == $d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                    @endforeach
                </select>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <a href="#" class="btn btn-primary btn-block" id="updatebarang"><i class="fa fa-send mr-1"></i> Submit</a>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {

        function cekbarang() {
            var nobukti_pengeluaran = $("#nobukti_pengeluaran").val();
            $.ajax({
                type: 'POST'
                , url: '/pengeluarangudanglogistik/cekbarang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pengeluaran: nobukti_pengeluaran
                }
                , cache: false
                , success: function(respond) {
                    $("#cekbarang").val(respond);
                }
            });
        }

        $('.cabangcheck').change(function() {
            if (this.checked) {
                $("#pilihcbg").show();
            } else {
                $("#pilihcbg").hide();
                $("#frmEdit").find("#kode_cabang").val("").change();
            }
        });

        function hidecabang() {
            if ($("#frmEdit").find('.cabangcheck').is(':checked')) {
                $("#pilihcbg").show();
            } else {
                $("#pilihcbg").hide();
            }

        }

        hidecabang();

        function loaddetail() {
            var nobukti_pengeluaran = $("#no_bukti").val();
            $("#loaddetailpengeluaran").load("/pengeluarangudanglogistik/" + nobukti_pengeluaran + "/showbarang");
            cekbarang();
        }

        $("#updatebarang").click(function(e) {
            e.preventDefault();
            var nobukti_pengeluaran = "{{ $barang->nobukti_pengeluaran }}";
            var kode_barang = "{{ $barang->kode_barang }}";
            var no_urut = "{{ $barang->no_urut }}";
            var keterangan = $("#frmEdit").find("#keterangan").val();
            var qty = $("#frmEdit").find("#qty").val();
            var kode_cabang = $("#frmEdit").find("#kode_cabang").val();
            if (qty == "" || qty == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Qty Harus Diisi Dulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmEdit").find("#qty").focus();
                });
            } else if ($("#frmEdit").find('.cabangcheck').is(':checked') && kode_cabang == "") {
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
                    , url: '/pengeluarangudanglogistik/updatebarang'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , nobukti_pengeluaran: nobukti_pengeluaran
                        , kode_barang: kode_barang
                        , no_urut: no_urut
                        , keterangan: keterangan
                        , qty: qty
                        , kode_cabang: kode_cabang
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 2) {
                            swal("Oops", "Data Gagal Disimpan", "warning");
                        } else {
                            swal("Berhasil", "Data Berhasil Disimpan", "success");
                            $("#kode_barang").val("");
                            $("#nama_barang").val("");
                            $("#keterangan").val("");
                            $("#qty").val("");
                            $("#kode_cabang").val("").change();
                            $("#nama_barang").focus();
                        }

                        loaddetail();
                        $("#mdledit").modal("hide");
                    }


                });
            }
        });
    });

</script>
