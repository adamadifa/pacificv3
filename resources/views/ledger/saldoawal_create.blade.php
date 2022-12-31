<form action="/saldoawalledger/store" id="FrmSaldoawalledger" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Kode Saldo Awal" field="kode_saldoawalledger" icon="feather icon-credit-card" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="bank" id="bank" class="form-control">
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
                <select class="form-control" id="bulan" name="bulan">
                    <option value="">Bulan</option>
                    <?php
                    $bulanini = date("m");
                    for ($i = 1; $i < count($bulan); $i++) {
                    ?>
                    <option value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
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
                <select class="form-control" id="tahun" name="tahun">
                    <option value="">Tahun</option>
                    <?php
                    $tahunmulai = 2020;
                    $hariini = date("m-d");
                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                    ?>
                    <option value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                    <?php
                    }
                    if($hariini == "12-31"){
                                            $t = date('Y') + 1;
                                        ?>
                    <option value="{{ $t }}">{{ $t }}</option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-sm-12">
            <x-inputtext label="Jumlah" field="jumlah" icon="feather icon-file" right />
        </div>
        <div class="col-lg-4 col-sm-12">
            <div class="form-group">
                <a href="#" class="btn btn-success" id="getsaldo"><i class="fa fa-search"></i> Get</a>
            </div>
        </div>
    </div>
    <div class="row" id="tombolsimpan">
        <div class="col-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block"><i class="feather icon-send mr-1"></i> Submit</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {

        function loadNoMutasi() {
            var bulan = $("#FrmSaldoawalledger").find('#bulan').val();
            // var bank    = $(this).attr('data-id');
            var tahun = $("#FrmSaldoawalledger").find('#tahun').val();
            var thn = tahun.substr(2, 2);
            var x = Math.floor((Math.random() * 100) + 1);
            // alert(bulan);
            if (parseInt(bulan.length) == 1) {
                var bln = "0" + bulan;
            } else {
                var bln = bulan;
            }
            var kode = "SA" + bln + thn + x;
            $("#kode_saldoawalledger").val(kode);
        }

        $("#FrmSaldoawalledger").find('#bulan').change(function() {
            loadNoMutasi();
        });

        $("#FrmSaldoawalledger").find('#tahun').change(function() {
            loadNoMutasi();
        });
        $("#getsaldo").click(function(e) {
            e.preventDefault();
            var bank = $("#FrmSaldoawalledger").find('#bank').val();
            var bulan = $("#FrmSaldoawalledger").find('#bulan').val();
            var tahun = $("#FrmSaldoawalledger").find('#tahun').val();

            if (bank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#bank').focus();
                });
            } else if (bulan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#bulan').focus();
                });
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#tahun').focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/saldoawalledger/getsaldo'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , bank: bank
                        , bulan: bulan
                        , tahun: tahun
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 1) {
                            swal("Oops!", "Saldo Bulan Sebelumnya Belum Diset! Atau Saldo Bulan Tersebut Sudah Ada", "warning");
                        } else {
                            $("#jumlah").val(respond);
                        }
                    }
                });
            }
        });

        $("#FrmSaldoawalledger").submit(function() {
            var bank = $("#FrmSaldoawalledger").find('#bank').val();
            var bulan = $("#FrmSaldoawalledger").find('#bulan').val();
            var tahun = $("#FrmSaldoawalledger").find('#tahun').val();
            var jumlah = $("#jumlah").val();
            if (bank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#bank').focus();
                });
                return false;
            } else if (bulan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#bulan').focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#tahun').focus();
                });
                return false;
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#jumlah').focus();
                });
                return false;
            }
        });
    });

</script>
