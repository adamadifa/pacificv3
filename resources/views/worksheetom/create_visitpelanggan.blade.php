<form action="#" id="frmVisitpelanggan" method="POST">
    <div class="row">
        <div class="col-6">
            <table class="table">
                <tr>
                    <th>No. Faktur</th>
                    <td>:</td>
                    <td>{{ $penjualan->no_fak_penj }}</td>
                </tr>
                <tr>
                    <th>Tanggal Faktur</th>
                    <td>:</td>
                    <td>{{ DateToIndo2($penjualan->tgltransaksi) }}</td>
                </tr>
                <tr>
                    <th>Jenis Transaksi</th>
                    <td>:</td>
                    <td>{{ strtoupper($penjualan->jenistransaksi) }}</td>
                </tr>
                <tr>
                    <th>Nilai Faktur</th>
                    <td>:</td>
                    <td>{{ rupiah($penjualan->total) }}</td>
                </tr>
            </table>
        </div>
        <div class="col-6">
            <table class="table">
                <tr>
                    <th>Kode Pelanggan</th>
                    <td>:</td>
                    <td>{{ $penjualan->kode_pelanggan }}</td>
                </tr>
                <tr>
                    <th>Nama</th>
                    <td>:</td>
                    <td>{{ $penjualan->nama_pelanggan }}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>:</td>
                    <td>{{ $penjualan->alamat_pelanggan }} ({{ $penjualan->pasar }})</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <x-inputtext label="Tanggal Visit" icon="feather icon-calendar" field="tanggal_visit" datepicker />
            </div>
            <div class="form-group">
                <textarea name="hasil_konfirmasi" id="hasil_konfirmasi" cols="30" rows="3" class="form-control"
                    placeholder="Hasil Konfirmasi"></textarea>
            </div>
            <div class="form-group">
                <textarea name="note" id="note" cols="30" rows="3" class="form-control" placeholder="Note"></textarea>
            </div>
            <div class="form-group">
                <textarea name="saran" id="saran" cols="30" rows="3" class="form-control"
                    placeholder="Saran / Keluhan Produk"></textarea>
            </div>
            <div class="form-group">
                <textarea name="act_om" id="saran" cols="30" rows="3" class="form-control" placeholder="Action OM"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary w-100" type="submit"><i class="feather icon-send mr-1"></i>Submit</button>
        </div>
    </div>
</form>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
<script>
    $(function() {
        $("#frmVisitpelanggan").submit(function() {
            var tanggal_visit = $("#tanggal_visit").val();
            var hasil_konfirmasi = $("#hasil_konfirmasi").val();
            var note = $("#note").val();
            var saran = $("#saran").val();
            var act_om = $("#act_om").val();

            if (tanggal_visit == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Visit Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tanggal_visit").focus();
                });

                return false;
            } else if (hasil_konfirmasi == "") {
                swal({
                    title: 'Oops',
                    text: 'Hasil Konfirmasi Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#hasil_konfirmasi").focus();
                });

                return false;
            } else if (note == "") {
                swal({
                    title: 'Oops',
                    text: 'Note Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#note").focus();
                });

                return false;
            } else if (act_om == "") {
                swal({
                    title: 'Oops',
                    text: 'Action OM Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#act_om").focus();
                });

                return false;
            }
        });
    });
</script>
