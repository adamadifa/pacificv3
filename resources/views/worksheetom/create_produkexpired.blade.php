<form action="#" id="frmCreateprodukexpired">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_produk" id="kode_produk" class="form-control">
                    <option value="">Pilih Produk</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <x-inputtext label="Dus" field="dus" icon="feather icon-file" />
        </div>
        <div class="col-4">
            <x-inputtext label="Pack" field="pack" icon="feather icon-file" />
        </div>
        <div class="col-4">
            <x-inputtext label="Pcs" field="pcs" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Expired" field="tanggal_expired" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100"><i class="feather icon-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        $("#frmCreateprodukexpired").click(function(e) {
            var tanggal = $("#frmCreateprodukexpired").find("#tanggal").val();
            var kode_produk = $("#frmCreateprodukexpired").find("#kode_produk").val();
            var tanggal_expired = $("#frmCreateprodukexpired").find("#tanggal_expired").val();
            if (tanggal == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#frmCreateprodukexpired").find("#tanggal").focus();
                });

                return false;
            } else if (kode_produk == "") {
                swal({
                    title: 'Oops',
                    text: 'Produk Harus Dpilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#frmCreateprodukexpired").find("#kode_produk").focus();
                });

                return false;
            } else if (tanggal_expired == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Expired Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#frmCreateprodukexpired").find("#tanggal_expired").focus();
                });

                return false;

            }
        });
    });
</script>
