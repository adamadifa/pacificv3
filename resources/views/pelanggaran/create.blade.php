<style>
    .form-label {
        font-weight: bold;
        margin-top: 10px;
    }

    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

</style>
<form action="/pelanggaran/store" method="POST" id="frmJmk">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Auto" field="no_surat" icon="feather icon-credit-card" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="nik" id="nik" class="form-control">
                    <option value="">Pilih Karyawan</option>
                    @foreach ($karyawan as $d)
                    <option value="{{ $d->nik }}">{{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker />
        </div>
        <div class="col-6">
            <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="jenis_pelanggaran" id="jenis_pelanggaran" class="form-control">
                    <option value="">Kategori Pelanggaran</option>
                    <option value="ST">ST</option>
                    <option value="SP1">SP1</option>
                    <option value="SP2">SP2</option>
                    <option value="SP3">SP3</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="pelanggaran" label="Pelanggaran" icon="feather icon-file-text" />
        </div>
    </div>
    <div class="row">

        <div class="col-12">
            <button type="submit" name="submit" class="btn btn-primary text-white w-100"><i class="feather icon-send mr-1"></i>Submit</button>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#nik").selectize();
        $("#jumlah").maskMoney();

        $("#frmJmk").submit(function() {
            var tgl_pembayaran = $("#tgl_pembayaran").val();
            var nik = $("#nik").val();
            var jumlah = $("#jumlah").val();

            if (tgl_pembayaran == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Pembayaran Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_pembayaran").focus();
                });
                return false;
            } else if (nik == "") {
                swal({
                    title: 'Oops'
                    , text: 'Nik Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nik").focus();
                });
                return false;
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });

                return false;
            }
        });
    });

</script>
