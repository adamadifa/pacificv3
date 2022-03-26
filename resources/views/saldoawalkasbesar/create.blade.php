<form action="/saldoawalkasbesar/store" method="POST" id="frmSaldokasbesar">
    @csrf
    <input type="hidden" readonly id="getsa" name="getsa" value="0" class="form-control" />
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Auto" field="kode_saldoawalkb" icon="feather icon-credit-card" disabled />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group" id="pilihcabang">
                <select name="kode_cabang" id="kode_cabang" class="form-control ">
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabang as $d)
                    <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
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
                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                    ?>
                    <option value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
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
                <a href="#" id="getsaldo" class="btn btn-success btn-block"><i class="feather icon-settings"></i> Get Saldo</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Uang Kertas" field="uang_kertas" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Uang Logam" field="uang_logam" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Giro" field="giro" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Transfer" field="transfer" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row mb-1">
        <div class="col-12">
            <div class="vs-checkbox-con vs-checkbox-primary">
                <input type="checkbox" class="aggrement" name="aggrement" value="aggrement">
                <span class="vs-checkbox">
                    <span class="vs-checkbox--check">
                        <i class="vs-icon feather icon-check"></i>
                    </span>
                </span>
                <span class="">Yakin Akan Disimpan ?</span>
            </div>
        </div>
    </div>
    <div class="row mt-5" id="tombolsimpan">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block"><i class="feather icon-send mr-1"></i> Submit</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {
        function hidetombolsimpan() {
            $("#tombolsimpan").hide();
        }
        hidetombolsimpan();
        $('.aggrement').change(function() {
            if (this.checked) {
                $("#tombolsimpan").show();
            } else {
                $("#tombolsimpan").hide();
            }
        });

        function loaddetailsaldo() {
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#frmSaldokasbesar").find("#bulan").val();
            var tahun = $("#frmSaldokasbesar").find("#tahun").val();
            if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#kode_cabang').focus();
                });
            } else if (bulan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#frmSaldokasbesar').find('#bulan').focus();
                });
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#frmSaldokasbesar').find('#tahun').focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/saldoawalkasbesar/getsaldo'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , bulan: bulan
                        , tahun: tahun
                        , kode_cabang: kode_cabang
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 1) {
                            $("#getsa").val(0);
                            swal("Oops!", "Saldo Bulan Sebelumnya Belum Diset! Atau Saldo Bulan Tersebut Sudah Ada", "warning");
                            nonaktifsaldo();
                        } else {
                            $("#getsa").val(1);
                            aktifsaldo();
                            //readonly();
                            var data = respond.split("|");
                            var uang_kertas = data[0];
                            var uang_logam = data[1];
                            var giro = data[2];
                            var transfer = data[3];
                            $("#uang_kertas").val(uang_kertas);
                            $("#uang_logam").val(uang_logam);
                            $("#giro").val(giro);
                            $("#transfer").val(transfer);
                        }
                        console.log(respond);
                    }
                });
            }
        }

        function nonaktifsaldo() {
            $("#uang_kertas").attr('disabled', 'disabled');
            $("#uang_logam").attr('disabled', 'disabled');
            $("#giro").attr('disabled', 'disabled');
            $("#transfer").attr('disabled', 'disabled');
        }

        function readonly() {
            $("#uang_kertas").attr('readonly', 'readonly');
            $("#uang_logam").attr('readonly', 'readonly');
            $("#giro").attr('readonly', 'readonly');
            $("#transfer").attr('readonly', 'readonly');
        }

        function aktifsaldo() {
            $("#uang_kertas").removeAttr('disabled');
            $("#uang_logam").removeAttr('disabled');
            $("#giro").removeAttr('disabled');
            $("#transfer").removeAttr('disabled');
            $("#uang_kertas").removeAttr('readonly');
            $("#uang_logam").removeAttr('readonly');
            $("#giro").removeAttr('readonly');
            $("#transfer").removeAttr('readonly');
        }

        nonaktifsaldo();

        $("#getsaldo").click(function(e) {
            e.preventDefault();
            loaddetailsaldo();
        });

        $("#frmSaldokasbesar").submit(function() {
            var getsa = $("#getsa").val();
            if (getsa == "" || getsa == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Lakukan Gets Saldo Dulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });

                return false;
            }
        });
    });

</script>
