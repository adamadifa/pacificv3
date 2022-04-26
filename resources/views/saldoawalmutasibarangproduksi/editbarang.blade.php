<form action="/saldoawalmutasibarangproduksi/{{ Crypt::encrypt($detail->kode_saldoawal) }}/{{ Crypt::encrypt($detail->kode_barang) }}/updatebarang" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <td>Kode Barang</td>
                    <td>{{ $detail->kode_barang }}</td>
                </tr>
                <tr>
                    <td>Nama Barang</td>
                    <td>{{ $detail->nama_barang }}</td>
                </tr>
            </table>
            <div class="row">
                <div class="col-12">
                    <x-inputtext label="Qty" field="qty" icon="feather icon-file" value="{{ $detail->qty }}" right />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
