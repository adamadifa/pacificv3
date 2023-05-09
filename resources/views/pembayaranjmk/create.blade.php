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
<div class="row">
    <div class="col-12">
        <x-inputtext label="Auto" field="no_bukti" icon="feather icon-credit-card" readonly />
    </div>
</div>
<div class="row">
    <div class="col-12">
        <x-inputtext label="Tanggal Pembayaran" field="tgl_pembayaran" icon="feather icon-calendar" datepicker />
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
    <div class="col-12">
        <x-inputtext field="jumlah" label="Jumlah" icon="feather icon-dollar-sign" right />
    </div>
</div>
<div class="row">

    <div class="col-12">
        <a class="btn btn-primary text-white w-100"><i class="feather icon-send mr-1">Kirim</i></a>
    </div>
</div>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#nik").selectize();
        $("#jumlah").maskMoney();
    });

</script>
