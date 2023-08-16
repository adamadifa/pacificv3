<form action="/permintaanpengiriman/{{ Crypt::encrypt($pp->no_permintaan_pengiriman) }}/updatetanggal" method="POST">
    @csrf
    <table class="table">
        <tr>
            <td>No. Permintaan Pengiriman</td>
            <td>{{ $pp->no_permintaan_pengiriman }}</td>
        </tr>
        <tr>
            <td>Kode Cabang</td>
            <td>{{ strtoupper($pp->kode_cabang) }}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>{{ $pp->keterangan }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>
                @if ($pp->status==1)
                <span class="badge bg-success"><i class="fa fa-check mr-1"></i> Sudah Di Proses</span>
                @else
                <span class="badge bg-danger"><i class="fa fa-history mr-1"></i> Belum Di Proses</span>
                @endif
            </td>
        </tr>
    </table>
    <div class="form-group">
        <div class="col-12">
            <x-inputtext label="Tanggal" field="tanggal" value="{{ $pp->tgl_permintaan_pengiriman }}" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="form-group">
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100"><i class="feather icon-send mr-1"></i>Update</button>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
