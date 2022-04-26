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
            <x-inputtext label="Berat" field="berat" value="{{ $barang->qty_berat }}" icon="feather icon-file" />
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
                , url: '/pengeluaranproduksi/cekbarang'
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

        function loaddetail() {
            var nobukti_pengeluaran = $("#no_bukti").val();
            $("#loaddetailpengeluaran").load("/pengeluaranproduksi/" + nobukti_pengeluaran + "/showbarang");
            cekbarang();
        }
        $("#updatebarang").click(function(e) {
            e.preventDefault();
            var nobukti_pengeluaran = "{{ $barang->nobukti_pengeluaran }}";
            var kode_barang = "{{ $barang->kode_barang }}";
            var keterangan = $("#frmEdit").find("#keterangan").val();
            var qty = $("#frmEdit").find("#qty").val();
            var berat = $("#frmEdit").find("#berat").val();
            if (qty == "" || qty == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Qty Harus Diisi Dulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmEdit").find("#qty").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/pengeluaranproduksi/updatebarang'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , nobukti_pengeluaran: nobukti_pengeluaran
                        , kode_barang: kode_barang
                        , keterangan: keterangan
                        , qty: qty
                        , berat: berat
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
                            $("#berat").val("");
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
