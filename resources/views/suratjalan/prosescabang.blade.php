<form action="/suratjalan/{{ Crypt::encrypt($mutasi->no_mutasi_gudang) }}/storeprosescabang" id="frmApprove" method="POST">
    @csrf
    <table class="table">
        <tr>
            <td>No. Surat Jalan</td>
            <td>{{$mutasi->no_mutasi_gudang}}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>{{ DateToIndo2($mutasi->tgl_mutasi_gudang)}}</td>
        </tr>
        <tr>
            <td>Cabang</td>
            <td>{{$mutasi->nama_cabang}}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>{{$mutasi->keterangan}}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>
                @if ($mutasi->status_sj==0)
                <span class="badge bg-danger">Belum Diterima Cabang</span>
                @elseif($mutasi->status_sj==1)
                <span class="badge bg-success">Sudah Diterima Cabang</span>
                @elseif($mutasi->status_sj ==2)
                <span class="badge bg-info">Transit Out</span>
                @endif

            </td>
        </tr>
    </table>
    <table class="table table-hover-animation">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Produk</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $d)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$d->kode_produk}}</td>
                <td>{{$d->nama_barang}}</td>
                <td class="text-right">{{rupiah($d->jumlah)}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="status" id="status" class="form-control">
                    <option value="">Pilih Status</option>
                    <option value="SURAT JALAN">Diterima</option>
                    <option value="TRANSIT OUT">Transit Out</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Diterima / Transit Out" field="tgl_mutasi_gudang_cabang" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn  btn-block btn-primary"><i class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#frmApprove").submit(function() {
            var status = $("#status").val();
            var tgl_mutasi_gudang_cabang = $("#tgl_mutasi_gudang_cabang");
            if (status == "") {
                swal({
                    title: 'Oops'
                    , text: 'Status Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#status").focus();
                });
                return false;
            } else if (tgl_mutasi_gudang_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_mutasi_gudang_cabang").focus();
                });
                return false;
            }
        });
    });

</script>
