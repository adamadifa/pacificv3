<form action="/supplier/store" method="post" id="frmSupplier">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Kode Supplier" field="kode_supplier" icon="feather icon-credit-card" value="{{ $kode_supplier }}" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama Supplier" field="nama_supplier" icon="feather icon-user" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Alamat" field="alamat_supplier" icon="feather icon-map" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Contact Person" field="contact_supplier" icon="feather icon-user" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. HP" field="nohp_supplier" icon="feather icon-phone" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Email" field="email" icon="feather icon-mail" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. Rekening" field="norekening" icon="feather icon-book" />
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

<script>
    $(function() {
        $("#frmSupplier").submit(function() {
            var kode_supplier = $("#kode_supplier").val();
            var nama_supplier = $("#frmSupplier").find("#nama_supplier").val();
            var alamat_supplier = $("#alamat_supplier").val();
            var contact_supplier = $("#contact_supplier").val();
            var nohp_supplier = $("#nohp_supplier").val();
            var email = $("#email").val();
            var norekening = $("#norekening").val();
            if (kode_supplier == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Supplier Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_supplier").focus();
                });
                return false;
            } else if (nama_supplier == "") {
                swal({
                    title: 'Oops'
                    , text: 'Nama Supplier Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmSupplier").find("#nama_supplier").focus();
                });
                return false;
            } else if (alamat_supplier == "") {
                swal({
                    title: 'Oops'
                    , text: 'Alamat Supplier Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#alamat_supplier").focus();
                });
                return false;
            } else if (contact_supplier == "") {
                swal({
                    title: 'Oops'
                    , text: 'Contact Person Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#contact_supplier").focus();
                });
                return false;
            } else if (nohp_supplier == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. HP Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nohp_supplier").focus();
                });
                return false;
            }
        });
    });

</script>
