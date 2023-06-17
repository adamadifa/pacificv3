<form action="/harilibur/{{ Crypt::encrypt($harilibur->kode_libur) }}/update" method="POST" id="frmLibur">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Auto" field="kode_libur" value="{{ $harilibur->kode_libur }}" icon="feather icon-credit-card" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal" field="tanggal" value="{{ $harilibur->tanggal_libur }}" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="id_kantor" id="id_kantor" class="form-control">
                    <option value="">Pilih Kantor</option>
                    @foreach ($cabang as $d)
                    <option {{ $harilibur->id_kantor == $d->kode_cabang  ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" value="{{ $harilibur->keterangan }}" icon="feather icon-file" />
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <button class="btn btn-primary btn-block" type="submit" name="submit"><i class="feather icon-send mr-1"></i>Submit</button>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{ asset('app-assets/js/external/selectize.js') }}"></script>
<script>
    $(function() {
        $("#frmLibur").submit(function(e) {
            var tanggal = $("#tanggal").val();
            var keterangan = $("#keterangan").val();
            if (tanggal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tanggal").focus();
                });
                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            }
        });
    });

</script>
