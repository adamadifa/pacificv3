<form autocomplete="off" id="frmUpdateTransfer" method="POST" action="/transfer/update">
    @csrf
    <input type="hidden" name="kode_transfer" value="{{ $transfer->kode_transfer }}">
    <input type="hidden" id="statustransfer" name="statustransfer" value="{{ $transfer->status }}">
    <input type="hidden" name="tgl_transfer" value="{{ $transfer->tgl_transfer }}">
    <input type="hidden" name="tglcair" value="{{ $transfer->tglcair }}">
    <input type="hidden" name="pelanggan" value="{{ $transfer->nama_pelanggan }}">

    <input type="hidden" name="kode_cabang" value="{{ $transfer->kode_cabang }}">
    <table class="table">
        <tr>
            <td>Kode Transfer</td>
            <td>{{ $transfer->kode_transfer }}</td>
        </tr>
        <tr>
            <td>Kode Pelanggan</td>
            <td>{{ $transfer->kode_pelanggan }}</td>
        </tr>
        <tr>
            <td>Nama Pelanggan</td>
            <td>{{ $transfer->nama_pelanggan }}</td>
        </tr>
        <tr>
            <td>Nama Bank</td>
            <td>{{ $transfer->namabank }}</td>
        </tr>
        <tr>
            <td>Jumlah</td>
            <input type="hidden" name="jumlah" value="{{ $transfer->jumlah }}">
            <td style="font-weight: bold">{{ rupiah($transfer->jumlah) }}</td>
        </tr>
        <tr>
            <td>Jatuh Tempo</td>
            <input type="hidden" value="{{ $transfer->tglcair }}" name="jatuhtempo">
            <td>{{ DateToIndo2($transfer->tglcair) }}</td>
        </tr>
    </table>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="statusaksi" id="statusaksi" class="form-control">
                    <option value="">Status</option>
                    <option value="0">Pending</option>
                    <option value="1">Diterima</option>
                    <option value="2">Ditolak</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row" id="tglditerima">
        <div class="col-12">
            <x-inputtext label="Tanggal Diterima" field="tgl_diterima" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row" id="tglditolak">
        <div class="col-12">
            <x-inputtext label="Tanggal Ditolak" field="tgl_ditolak" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row" id="bank">
        <div class="col-12">
            <div class="form-group">
                <select class="form-control" id="bankpenerima" name="bank">
                    <option value="">Bank Penerima</option>
                    @foreach ($bank as $d)
                        <option value="{{ $d->kode_bank }}">{{ $d->nama_bank }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <h5 id="omset">Omset</h5>
    <hr>
    <div class="row" id="omsetbulan">
        <div class="col-12">
            {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
            <div class="form-group">
                <select class="form-control" id="bulan" name="bulan">
                    <option value="">Omset Bulan</option>
                    <?php
                $bulanini = date("m");
                for ($i = 1; $i < count($bulan); $i++) {
                ?>
                    <option <?php if ($bulanini == $i) {
                        echo 'selected';
                    } ?> value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                    <?php
                }
                ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row" id="omsettahun">
        <div class="col-12">
            <div class="form-group">
                <select class="form-control" id="tahun" name="tahun">
                    <?php
                $tahunmulai = 2020;
                for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                ?>
                    <option <?php if (date('Y') == $thn) {
                        echo 'Selected';
                    } ?> value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                    <?php
                }
                ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" id="btnSubmit">
                    <i class="feather icon-send"></i>
                    Proses
                </button>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script>
    $(function() {
        $("#bankpenerima").selectize();
        function diterima() {
            $("#tglditerima").show();
            $("#tglditolak").hide();
            $("#bank").show();
            $("#omset").show();
            $("#omsetbulan").show();
            $("#omsettahun").show();
        }

        function ditolak() {
            $("#tglditolak").show();
            $("#tglditerima").hide();
            $("#bank").show();
            $("#omset").hide();
            $("#omsetbulan").hide();
            $("#omsettahun").hide();
        }

        function hidetanggal() {
            $("#tglditolak").hide();
            $("#tglditerima").hide();
            $("#bank").hide();
            $("#omset").hide();
            $("#omsetbulan").hide();
            $("#omsettahun").hide();
        }

        hidetanggal();
        $("#statusaksi").change(function() {
            var status = $("#statusaksi").val();
            if (status == 1) {
                diterima();
            } else if (status == 2) {
                ditolak();
            } else {
                hidetanggal();
            }
        });

        $("#frmUpdateTransfer").submit(function(e) {
            var status = $("#statusaksi").val();
            var tgl_diterima = $("#tgl_diterima").val();
            var tgl_ditolak = $("#tgl_ditolak").val();
            var bankpenerima = $("#bankpenerima").val();
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            $("#btnSubmit").attr("disabled", true);
            if (status == "") {
                swal({
                    title: 'Oops',
                    text: 'Pilih Status Aksi!',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#statusaksi").focus();
                });
                return false;
            } else {
                if (status == 1) {
                    if (tgl_diterima == "") {
                        swal({
                            title: 'Oops',
                            text: 'Tanggal Cair Harus Diisi !',
                            icon: 'warning',
                            showConfirmButton: false
                        }).then(function() {
                            $("#tgl_diterima").focus();
                        });
                        return false;
                    } else if (bankpenerima == "") {
                        swal({
                            title: 'Oops',
                            text: 'Bank Harus Diisi !',
                            icon: 'warning',
                            showConfirmButton: false
                        }).then(function() {
                            $("#bank").focus();
                        });
                        return false;
                    } else if (bulan == "") {
                        swal({
                            title: 'Oops',
                            text: 'Omset Bulan Harus Diisi !',
                            icon: 'warning',
                            showConfirmButton: false
                        }).then(function() {
                            $("#bulan").focus();
                        });
                        return false;
                    } else if (tahun == "") {
                        swal({
                            title: 'Oops',
                            text: 'Omset Tahun Harus Diisi !',
                            icon: 'warning',
                            showConfirmButton: false
                        }).then(function() {
                            $("#tgl_diterima").focus();
                        });
                        return false;
                    } else {
                        return true;
                    }
                } else if (status == 2) {
                    if (tgl_ditolak == "") {
                        swal({
                            title: 'Oops',
                            text: 'Tanggal Ditolak Harus Diisi !',
                            icon: 'warning',
                            showConfirmButton: false
                        }).then(function() {
                            $("#tgl_ditolak").focus();
                        });
                        return false;
                    } else if (bankpenerima == "") {
                        swal({
                            title: 'Oops',
                            text: 'Bank Harus Diisi !',
                            icon: 'warning',
                            showConfirmButton: false
                        }).then(function() {
                            $("#bank").focus();
                        });
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        });


    });
</script>
