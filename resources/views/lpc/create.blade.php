<form action="#">
    <div class="form-body">
        <div class="form-group">
            <div class="col-12">
                <select name="kode_cabang" id="kode_cabang" class="form-control">
                    <option value="">Cabang</option>
                    @foreach ($cabang as $d)
                        <option value="{{$d->kode_cabang}}">{{$d->nama_cabang}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-12">
                <select name="bulaninput" id="bulaninput" class="form-control">
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
        <div class="form-group">
            <div class="col-12">
                <select name="tahuninput" id="tahuninput" class="form-control">
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
        <div class="form-group">
            <div class="col-12">
                <x-inputtext label="Tanggal LPC" field="tgl_lpc" datepicker icon="feather icon-calendar"/>
            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-primary btn-block" id="simpanlpc"><i class="feather icon-send"></i> Submit</button>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function(){
        $("#simpanlpc").click(function(e){
            e.preventDefault();
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#bulaninput").val();
            var tahun = $("#tahuninput").val();
            var tgl_lpc = $("#tgl_lpc").val();
            if(kode_cabang==""){
                swal({
                    title: 'Oops'
                    , text: 'Kode Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_cabang").focus();
                });
            }else if(bulan==""){
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#bulaninput").focus();
                });
            }else if(tahun==""){
                swal({
                    title: 'Oops'
                    , text: 'Tahun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tahuninput").focus();
                });
            }else if(tgl_lpc==""){
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_lpc").focus();
                });
            }
        });
    });
</script>
