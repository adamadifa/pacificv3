<form action="{{ route('realisasi.update') }}" id="frmrealisasi" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="bulan" id="bulan" class="form-control">
                    <option value="">Bulan</option>
                    <?php
                    $bl = $realisasi->bulan;
                    for ($i = 1; $i < count($bln); $i++) {
                    ?>
                    <option <?php if ($bl == $i) {
                        echo 'selected';
                    } ?> value="<?php echo $i; ?>"><?php echo $bln[$i]; ?>
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
                <select name="tahun" id="tahun" class="form-control">
                    <option value="">Tahun</option>
                    <?php
                    $tahun = $realisasi->tahun;
                    $tahunmulai = 2021;
                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                    ?>
                    <option <?php if ($tahun == $thn) {
                        echo 'selected';
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
                <input type="text" value="{{ $realisasi->im }}" name="im" id="im" class="form-control"
                    placeholder="Internam Memo">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" value="{{ $realisasi->ajuan }}" name="ajuan" id="ajuan" class="form-control"
                    placeholder="Ajuan">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="hidden" value="{{ $realisasi->id }}" name="id" id="id" class="form-control"
                    placeholder="ID">
                <input type="hidden" value="{{ $realisasi->kode_pelanggan }}" name="kode_pelanggan" id="kode_pelanggan"
                    class="form-control" placeholder="Nama Pelanggan">
                <input type="text" value="{{ $realisasi->nama_pelanggan }}" name="nama_pelanggan" id="nama_pelanggan"
                    class="form-control" placeholder="Nama Pelanggan">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="number" value="{{ $realisasi->nominal }}" name="nominal" id="nominal"
                    class="form-control money" placeholder="Nominal">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" value="{{ $realisasi->keterangan }}" name="keterangan" id="keterangan"
                    class="form-control" placeholder="Keterangan">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" value="{{ $realisasi->bentuk_hadiah }}" name="bentuk_hadiah" id="bentuk_hadiah"
                    class="form-control" placeholder="Bentuk Hadiah">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit" id="simpanrealisasi"><i
                        class="feather icon-send"></i> Submit</button>
            </div>
        </div>
    </div>
</form>
<div class="modal fade text-left" id="mdlpelanggan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document"
        style="max-width: 960px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Pelanggan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover-animation tabelpelanggan" style="width:100% !important"
                    id="tabelpelanggan">
                    <thead class="thead-dark">
                        <tr>
                            <th>Kode Pelanggan</th>
                            <th>Nama Pelanggan</th>
                            <th>Pasar</th>
                            <th>Salesman</th>
                            <th>Kode Cabang</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('app-assets/js/jquery.maskMoney.js') }}"></script>
<script>
    $(function() {

        $("#frmrealisasi").submit(function(e) {
            var tgl_realisasi = $("#tgl_realisasi").val();
            var no_fak_penj = $("#no_fak_penj").val();
            var nominal = $("#nominal").val();
            var hasil_konfirmasi = $("#hasil_konfirmasi").val();
            var catatan = $("#catatan").val();
            var action = $("#action").val();
            var saran = $("#saran").val();
            if (tgl_realisasi == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tgl_realisasi").focus();
                });
                return false;
            } else if (no_fak_penj == "") {
                swal({
                    title: 'Oops',
                    text: 'No Faktur Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#no_fak_penj").focus();
                });
                return false;
            } else if (nominal == "") {
                swal({
                    title: 'Oops',
                    text: 'Nominal Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#nominal").focus();
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
            }
        });
    });
</script>
