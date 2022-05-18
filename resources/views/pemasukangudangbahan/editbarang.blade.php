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
            <x-inputtext label="Qty Unit" field="qty_unit" value="{{ $barang->qty_unit }}" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Qty Berat" field="qty_berat" value="{{ $barang->qty_berat }}" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Qty Lebih" field="qty_lebih" value="{{ $barang->qty_lebih }}" icon="feather icon-file" />
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
            var nobukti_pemasukan = $("#nobukti_pemasukan").val();
            $.ajax({
                type: 'POST'
                , url: '/pemasukangudangbahan/cekbarang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pemasukan: nobukti_pemasukan
                }
                , cache: false
                , success: function(respond) {
                    $("#cekbarang").val(respond);
                }
            });
        }



        function loaddetail() {
            var nobukti_pemasukan = $("#no_bukti").val();
            $("#loaddetailpemasukan").load("/pemasukangudangbahan/" + nobukti_pemasukan + "/showbarang");
            cekbarang();
        }


        $("#updatebarang").click(function(e) {
            e.preventDefault();
            var id = "{{ $barang->id }}";
            var keterangan = $("#frmEdit").find("#keterangan").val();
            var qty_unit = $("#frmEdit").find("#qty_unit").val();
            var qty_berat = $("#frmEdit").find("#qty_berat").val();
            var qty_lebih = $("#frmEdit").find("#qty_lebih").val();

            $.ajax({
                type: 'POST'
                , url: '/pemasukangudangbahan/updatebarang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id: id
                    , keterangan: keterangan
                    , qty_unit: qty_unit
                    , qty_berat: qty_berat
                    , qty_lebih: qty_lebih
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
                        $("#qty_unit").val("");
                        $("#qty_berat").val("");
                        $("#qty_lebih").val("");
                        $("#nama_barang").focus();
                    }

                    loaddetail();
                    $("#mdledit").modal("hide");
                }


            });
        });
    });

</script>
