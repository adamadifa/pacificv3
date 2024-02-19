<form action="/slipgaji/{{ Crypt::encrypt($kode_gaji) }}/storepenambahpengurang" method="POST" id="frmSlipgaji">
    @csrf
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
            <x-inputtext label="Pengurang" field="pengurang" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Penambah" field="penambah" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100"><i class="feather icon-send"></i> Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('app-assets/js/external/selectize.js') }}"></script>
<script>
    $(function() {
        $("#nik").selectize();
        $("#frmSlipgaji").submit(function(e) {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            var status = $("#status").val();

            if (bulan == "") {
                swal({
                    title: 'Oops',
                    text: 'Bulan Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#bulan").focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops',
                    text: 'Tahun Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#bulan").focus();
                });

                return false;
            }
        });
    });
</script>
