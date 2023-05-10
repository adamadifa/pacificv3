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
<form action="/pembayaranjmk/{{ Crypt::encrypt($jmk->no_bukti); }}/update" method="POST" id="frmJmk">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Auto" field="no_bukti" icon="feather icon-credit-card" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Pembayaran" value="{{ $jmk->tgl_pembayaran }}" field="tgl_pembayaran" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="nik" id="nik" class="form-control">
                    <option value="">Pilih Karyawan</option>
                    @foreach ($karyawan as $d)
                    <option {{ $jmk->nik == $d->nik ? 'selected' : '' }} value="{{ $d->nik }}">{{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="jumlah" label="Jumlah" value="{{ rupiah($jmk->jumlah) }}" icon="feather icon-dollar-sign" right />
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
