<form action="/kontrabonangkutan/proseskontrabon" method="POST" id="frmProses">
    @csrf
    <input type="hidden" name="no_kontrabon" value="{{ $kontrabon->no_kontrabon }}">
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <td>No. Kontrabon</td>
                    <td>{{ $kontrabon->no_kontrabon }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>{{ DateToIndo2($kontrabon->tgl_kontrabon) }}</td>
                </tr>
                <tr>
                    <td>Angkutan</td>
                    <td>{{ $kontrabon->keterangan }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-hover-animation">
                <thead class="thead-dark">
                    <tr>
                        <th>No. SJ</th>
                        <th>Tanggal SJ</th>
                        <th>No. Polisi</th>
                        <th>Tujuan</th>
                        <th>Tarif</th>
                        <th>Tepung</th>
                        <th>BS</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $grandtotal = 0;
                    @endphp
                    @foreach ($detail as $d)
                    @php
                    $total = $d->tarif + $d->tepung + $d->bs;
                    $grandtotal += $total;
                    @endphp
                    <tr>
                        <td>{{ $d->no_surat_jalan }}</td>
                        <td>{{ date("d-m-y",strtotime($d->tgl_input)) }}</td>
                        <td>{{ $d->nopol }}</td>
                        <td>{{ $d->tujuan }}</td>
                        <td class="text-right">{{ rupiah($d->tarif) }}</td>
                        <td class="text-right">{{ rupiah($d->tepung) }}</td>
                        <td class="text-right">{{ rupiah($d->bs) }}</td>
                        <td class="text-right">{{ rupiah($total) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="7"></td>
                        <td class="text-right" style="font-weight: bold">{{ rupiah($grandtotal) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal" field="tgl_ledger" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. Ref" field="no_ref" icon="fa fa-barcode" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Pelanggan" field="pelanggan" icon="fa fa-user" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_bank" id="kode_bank" class="form-control select2">
                    <option value="">Pilih Bank</option>
                    @foreach ($bank as $d)
                    <option value="{{ $d->kode_bank }}">{{ $d->nama_bank }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    $(function() {
        $("#frmProses").submit(function() {
            var tgl_ledger = $("#tgl_ledger").val();
            var no_ref = $("#no_ref").val();
            var pelanggan = $("#pelanggan").val();
            var kode_bank = $("#kode_bank").val();
            var keterangan = $("#keterangan").val();
            if (tgl_ledger == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_ledger").focus();
                });
                return false;

            } else if (no_ref == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Ref Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_ref").focus();
                });
                return false;

            } else if (pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pelanggan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#pelanggan").focus();
                });
                return false;

            } else if (kode_bank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_bank").focus();
                });
                return false;

            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Dipilih !'
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
