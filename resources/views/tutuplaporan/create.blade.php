<form action="/tutuplaporan/store" method="POST" id="frmtutuplaporan">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="bulan" id="bulan" class="form-control">
                    <option value="">Bulan</option>
                    <?php
                    $bl = date("m");
                    for ($i = 1; $i < count($bln); $i++) {
                    ?>
                    <option <?php if ($bl == $i) {
                                echo "selected";
                            } ?> value="<?php echo $i; ?>"><?php echo $bln[$i]; ?></option>
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
                <select name="tahun" id="tahun" class="form-control">
                    <option value="">Tahun</option>
                    <?php
                    $tahun = date("Y");
                    $tahunmulai = 2021;
                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                    ?>
                    <option <?php if ($tahun == $thn) {
                                echo "selected";
                            } ?> value="<?php echo $thn; ?>"><?php echo $thn; ?>
                    </option>
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
                <select name="jenis_laporan" id="jenis_laporan" class="form-control">
                    <option value="">Pilih Laporan</option>
                    <option value="penjualan">Penjualan</option>
                    <option value="pembelian">Pembelian</option>
                    <option value="kaskecil">Kas Kecil</option>
                    <option value="ledger">Ledger</option>
                    <option value="gudangcabang">Gudang Cabang</option>
                    <option value="gudangpusat">Gudang Pusat</option>
                    <option value="gudangbahan">Gudang Bahan</option>
                    <option value="gudanglogistik">Gudang Logistik</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Penutupan" field="tgl_penutupan" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#frmtutuplaporan").submit(function() {
            var bulan = $("#frmtutuplaporan").find("#bulan").val();
            var tahun = $("#frmtutuplaporan").find("#tahun").val();
            var jenis_laporan = $("#jenis_laporan").val();
            var tgl_penutupan = $("#tgl_penutupan").val();
            if (bulan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmtutuplaporan").find("#bulan").focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmtutuplaporan").find("#tahun").focus();
                });
                return false;
            } else if (jenis_laporan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jenis Laporan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("jenis_laporan").focus();
                });
                return false;
            } else if (tgl_penutupan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Penutupan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("tgl_penutupan").focus();
                });
                return false;
            }
        });
    });

</script>
