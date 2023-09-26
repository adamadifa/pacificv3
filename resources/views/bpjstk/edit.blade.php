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
<form action="/bpjstk/{{ Crypt::encrypt($bpjstk->kode_bpjs_tk) }}/update" method="POST" id="frmBpjs">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Berlaku" field="tgl_berlaku" value="{{ $bpjstk->tgl_berlaku }}" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="nik" id="nik" class="form-control" disabled>
                    <option value="">Pilih Karyawan</option>
                    @foreach ($karyawan as $d)
                    <option value="{{ $d->nik }}" {{ $bpjstk->nik == $d->nik ? 'selected' : '' }}> {{ $d->nik }} - {{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Jumlah Iuran" field="iuran" value="{{ rupiah($bpjstk->iuran) }}" icon="feather icon-file" right />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit"><i class="feather icon-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $("#nik").selectize();
    $("#iuran").maskMoney();
    $("#frmBpjs").submit(function(e) {
        //e.preventDefault();
        var nik = $("#nik").val();
        var tgl_berlaku = $("#tgl_berlaku").val();
        var iuran = $("#iuran").val();
        if (tgl_berlaku == "") {
            swal({
                title: 'Oops'
                , text: 'Tanggal Berlaku Harus Diisi !'
                , icon: 'warning'
                , showConfirmButton: false
            }).then(function() {
                $("#tgl_berlaku").focus();
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
        } else if (iuran == "" || iuran == 0) {
            swal({
                title: 'Oops'
                , text: 'Jumlah Iuran Harus Diisi !'
                , icon: 'warning'
                , showConfirmButton: false
            }).then(function() {
                $("#iuran").focus();
            });
            return false;
        }

    });

</script>
