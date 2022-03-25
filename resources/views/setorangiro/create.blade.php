<form action="/setorangiro/store" method="POST" id="frmSetorangiro">
    @csrf
    <input type="hidden" id="cektutuplaporan">
    <input type="hidden" name="no_giro" id="no_giro" value="{{ $giro->no_giro }}">
    <input type="hidden" name="jumlah" id="jumlah" value="{{ $giro->jumlah }}">
    <table class="table table-bordered">
        <tr>
            <td>No. Giro</td>
            <td>{{ $giro->no_giro }}</td>
        </tr>
        <tr>
            <td>Nama Pelanggan</td>
            <td>{{ $giro->nama_pelanggan }}</td>
        </tr>
        <tr>
            <td>Bank</td>
            <td>{{ $giro->namabank }}</td>
        </tr>
        <tr>
            <td>Jumlah</td>
            <td class="text-right">{{ rupiah($giro->jumlah) }}</td>
        </tr>
        <tr>
            <td>Jatuh Tempo</td>
            <td>{{ DateToIndo2($giro->tglcair) }}</td>
        </tr>
    </table>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Disetorkan" field="tgl_setoranpusat" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="form-group">
                <select name="kode_bank" id="kode_bank" class="form-control">
                    <option value="">Pilih Bank</option>
                    @foreach ($bank as $d)
                    <option {{ Request('kode_bank')==$d->kode_bank ? 'selected' :'' }} value="{{ $d->kode_bank }}">{{ $d->nama_bank }}</option>
                    @endforeach
                </select>
            </div>
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
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    $(function() {
        function cektutuplaporan() {
            var tanggal = $("#tgl_setoranpusat").val();
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "penjualan"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#frmSetorangiro").find("#cektutuplaporan").val(respond);
                }
            });
        }

        $("#tgl_setoranpusat").change(function() {
            cektutuplaporan();
        });
        $("#frmSetorangiro").submit(function() {
            var tgl_setoranpusat = $("#tgl_setoranpusat").val();
            var kode_bank = $("#kode_bank").val();
            var cektutuplaporan = $("#frmSetorangiro").find("#cektutuplaporan").val();
            if (cektutuplaporan > 0) {
                swal({
                    title: 'Oops'
                    , text: 'Periode Laporan Sudah Ditutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_setoranpusat").focus();
                });

                return false;
            } else if (tgl_setoranpusat == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_setoranpusat").focus();
                });
                return false;
            } else if (kode_bank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_bank").focus();
                });
                return false;
            }
        });
    });

</script>
