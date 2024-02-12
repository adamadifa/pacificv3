<div class="row">
    <div class="col-12">
        <table class="table bordered">
            <tr>
                <th>Tgl Pengajuan</th>
                <td>{{ DateToIndo2($ajuantransferdana->tgl_pengajuan) }}</td>
            </tr>
            <tr>
                <th>Nama Penerima</th>
                <td>{{ $ajuantransferdana->nama }}</td>
            </tr>
            <tr>
                <th>Bank</th>
                <td>{{ $ajuantransferdana->nama_bank }}</td>
            </tr>
            <tr>
                <th>No. Rekening</th>
                <td>{{ $ajuantransferdana->no_rekening }}</td>
            </tr>
            <tr>
                <th>Jumlah</th>
                <td>{{ rupiah($ajuantransferdana->jumlah) }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $ajuantransferdana->keterangan }}</td>
            </tr>
            <tr>
                <th>Cabang</th>
                <td>{{ $ajuantransferdana->kode_cabang }}</td>
            </tr>
        </table>
    </div>
</div>

<form action="/ajuantransferdana/{{ Crypt::encrypt($ajuantransferdana->no_pengajuan) }}/proses" method="POST"
    id="frmAjuantransferdana">
    @csrf
    <div class="form-group">
        <x-inputtext field="tgl_proses" label="Tanggal Proses" value="{{ $ajuantransferdana->tgl_pengajuan }}" readonly
            icon="feather icon-calendar" />
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary w-100" type="submit"><i class="feather icon-send mr-1"></i>Proses</button>

        </div>
</form>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script>
    $(function() {
        $("#frmAjuantransferdana").submit(function() {
            var tgl_proses = $("#tgl_proses").val();
            if (tgl_proses == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Proses Harus Diisi!',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tgl_proses").focus();
                });
                return false;
            }
        });
    });
</script>
