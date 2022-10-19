<form action="/costratio/store" method="post" id="frmCostratio">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext field="tanggal" label="Tanggal" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            <select name="keterangan" id="keterangan" class="form-control">
                <option value="">Pilih Keterangan</option>
                <option value="Sewa Gedung">Sewa Gedung</option>
                <option value="Ratio BS">Ratio BS</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="jumlah" label="Jumlah" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12">
            @if (Auth::user()->kode_cabang == "PCF" || Auth::user()->level=="admin pusat")
            <select name="kode_cabang" id="kode_cabang" class="form-control">
                <option value="">Cabang</option>
                @foreach ($cabang as $d)
                <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                @endforeach
            </select>
            @else
            <input type="hidden" name="kode_cabang" value="{{ Auth::user()->kode_cabang }}">
            @endif

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
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#jumlah").maskMoney();
        $("#frmCostratio").submit(function() {
            var tanggal = $("#tanggal").val();
            var keterangan = $("#keterangan").val();
            var jumlah = $("#jumlah").val();
            var kode_cabang = $("#kode_cabang").val();
            if (tanggal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmCostratio").find("#tanggal").focus();
                });
                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmCostratio").find("#keterangan").focus();
                });
                return false;
            } else if (jumlah == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmCostratio").find("#jumlah").focus();
                });
                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmCostratio").find("#kode_cabang").focus();
                });
                return false;
            }
        });
    });

</script>
