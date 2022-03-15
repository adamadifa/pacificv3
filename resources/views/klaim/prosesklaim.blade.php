<form action="/klaim/storeprosesklaim" method="POST" id="frmProsesklaim">
    @csrf
    <table class="table" style="font-size:14px">
        <tr>
            <td>Kode Klaim</td>
            <td>:</td>
            <td>
                <input type="hidden" name="kode_klaim" id="kode_klaim" value="{{ $klaim->kode_klaim }}">
                <b>{{ $klaim->kode_klaim }}</b>
            </td>
        </tr>
        <tr>
            <td>Tanggal Klaim</td>
            <td>:</td>
            <td><b>{{ date("d-m-Y",strtotime($klaim->tgl_klaim)) }}</b></td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>:</td>
            <td><b>{{ ucwords(strtolower($klaim->keterangan)) }}</b></td>
        </tr>
        <tr>
            <td>Kode Cabang</td>
            <td>:</td>
            <td><b>{{ $klaim->kode_cabang }} </b></td>
        </tr>
        <tr>
            <td>Status</td>
            <td>:</td>
            <td>
                <?php
                if ($klaim->status == '0') {
                    $keterangan = "Belum Di Proses";
                    $color       = "bg-danger";
                } else {
                    $keterangan = "Sudah di Proses";
                    $color       = "bg-success";
                }
                ?>
                <span class="badge <?php echo $color; ?>"><?php echo $keterangan; ?></span>
            </td>

        </tr>
    </table>
    <table class="table" style="font-size:12px">
        <thead class="thead-dark" style="font-size:14px;">
            <tr>
                <th style="width:13%">TGL</th>
                <th style="width:12%">No Bukti</th>
                <th>Keterangan</th>
                <th>Penerimaan</th>
                <th>Pengeluaran</th>
                <th>Saldo</th>
            </tr>
            <tr>
                <th>SALDO AWAL</th>
                <th colspan="4"></th>
                <th style="text-align:right">
                    <?php
                if (!empty($saldoawal)) {
                    echo number_format($saldoawal, '0', '', '.');
                }
                ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $saldo = $saldoawal;
            $totalpenerimaan  = 0;
            $totalpengeluaran = 0;
            $totalpenerimaan2 = 0;
            foreach ($detail as $d) {
                if ($d->status_dk == 'K') {
                    $penerimaan   = $d->jumlah;
                    $s = $penerimaan;
                    $pengeluaran  = 0;
                } else {
                    $penerimaan = 0;
                    $pengeluaran = $d->jumlah;
                    $s = -$pengeluaran;
                }
                $saldo = $saldo + $s;
                $totalpenerimaan = $totalpenerimaan + $penerimaan;
                $totalpengeluaran = $totalpengeluaran + $pengeluaran;
                if ($d->keterangan != 'Penerimaan Kas Kecil') {
                    $totalpenerimaan2 = $totalpenerimaan2 + $penerimaan;
                }
        ?>
            <tr>
                <td><?php echo $d->tgl_kaskecil; ?></td>
                <td><?php echo $d->nobukti; ?></td>
                <td><?php echo ucwords(strtolower($d->keterangan)); ?></td>
                <td align="right" style="color:green"><?php if (!empty($penerimaan)) {echo number_format($penerimaan, '0', '', '.');} ?></td>
                <td align="right" style="color:red"><?php if (!empty($pengeluaran)) {echo number_format($pengeluaran, '0', '', '.');} ?></td>
                <td align="right"><?php if (!empty($saldo)) {echo number_format($saldo, '0', '', '.');} ?></td>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">TOTAL</th>
                <td align="right" style="color:green"><b><?php if (!empty($totalpenerimaan)) {echo number_format($totalpenerimaan, '0', '', '.');} ?></b></td>
                <td align="right" style="color:red"><b><?php if (!empty($totalpengeluaran)) { echo number_format($totalpengeluaran, '0', '', '.');} ?></b></td>
                <td align="right"><b><?php if (!empty($saldo)) {echo number_format($saldo, '0', '', '.');} ?></b></td>
            </tr>
        </tfoot>
    </table>
    <table class="table">
        <tr>
            <td class="bg-primary text-white">Penggantian Kas Kecil</td>
            <?php
        if ($klaim->kode_cabang == 'PST') {
            $penggantian = $totalpengeluaran - $totalpenerimaan2;
        } else {
            $penggantian = $totalpengeluaran - $totalpenerimaan2;
        }
        ?>
            <td colspan="2" align="right"><b><?php if (!empty($penggantian)) {echo number_format($penggantian, '0', '', '.');} ?></b></td>
            <td class="bg-primary text-white">Saldo Awal</td>
            <td colspan="2" align="right"><b><?php if (!empty($saldoawal['jumlah'])) {echo number_format($saldoawal['jumlah'], '0', '', '.');} ?></b></td>
        </tr>
        <tr>
            <td class="bg-primary text-white">Terbilang</td>
            <td colspan="2" align="right"><b><?php echo ucwords(terbilang($penggantian)); ?></b></td>
            <td class="bg-primary text-white">Penerimaan Pusat</td>
            <td colspan="2" align="right"><b><?php if (!empty($totalpenerimaan)) {echo number_format($totalpenerimaan, '0', '', '.');} ?></b></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2"></td>
            <td class="bg-primary text-white">Total</td>
            <td colspan="2" align="right"><b><?php if (!empty($saldoawal + $totalpenerimaan - $totalpenerimaan2)) {echo number_format($saldoawal + $totalpenerimaan - $totalpenerimaan2, '0', '', '.');} ?></b></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2"></td>
            <td class="bg-primary text-white">Pengeluaran Kas Kecil</td>
            <td colspan="2" align="right"><b><?php if (!empty($totalpengeluaran)) {echo number_format($totalpengeluaran, '0', '', '.');} ?></b></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2"></td>
            <td class="bg-primary text-white">Saldo Akhir</td>
            <td colspan="2" align="right">
                <input type="hidden" name="saldo_akhir" value="{{ $saldo }}">
                <b><?php if (!empty($saldo)) {echo rupiah($saldo);} ?></b>
            </td>
        </tr>
    </table>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="tanggal" label="Tanggal Proses Klaim" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="keterangan" label="Keterangan" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="jumlah" label="Jumlah" icon="feather icon-file" value="{{ rupiah($penggantian) }}" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="bank" id="bank" class="form-control">
                    <option value="">Pilih Bank</option>
                    @foreach ($bank as $d)
                    <option {{ $d->kode_bank == 'BNI CV' ? 'selected' : '' }} value="{{ $d->kode_bank }}">{{ $d->nama_bank }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i> Proses Klaim</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    $(function() {
        $("#jumlah").maskMoney();
        $("#frmProsesklaim").submit(function() {
            var tanggal = $("#tanggal").val();
            var keterangan = $("#keterangan").val();
            var jumlah = $("#jumlah").val();
            var bank = $("#bank").val();
            if (tanggal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tanggal").focus();
                });

                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#keterangan").focus();
                });

                return false;
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });

                return false;
            } else if (bank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#bank").focus();
                });

                return false;
            }
        });
    });

</script>
