<form action="#">
    <input type="hidden" name="kode_lpc" id="kode_lpc" value="{{ $lpc->kode_lpc }}">
    <div class="form-body">
        <div class="form-group">
            <div class="col-12">
                <select name="kode_cabang" id="kode_cabang" class="form-control" disabled>
                    <option value="">Cabang</option>
                    @foreach ($cabang as $d)
                    <option @if($lpc->kode_cabang == $d->kode_cabang)
                        selected
                        @endif value="{{$d->kode_cabang}}">{{$d->nama_cabang}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-12">
                <select name="bulaninput" id="bulaninput" class="form-control" disabled>
                    <option value="">Bulan</option>
                    <?php
                    $bl = date("m");
                    for ($i = 1; $i < count($bln); $i++) {
                    ?>
                    <option @if($lpc->bulan == $i)
                        selected
                        @endif <?php if ($bl == $i) {
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
                <select name="tahuninput" id="tahuninput" class="form-control" disabled>
                    <option value="">Tahun</option>
                    <?php
                    $tahun = date("Y");
                    $tahunmulai = 2021;
                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                    ?>
                    <option @if($lpc->tahun == $thn)
                        selected
                        @endif <?php if ($tahun == $thn) {
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
                <x-inputtext label="Tanggal LPC" value="{{ $lpc->tgl_lpc }}" field="tgl_lpc" datepicker icon="feather icon-calendar" />
            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-primary btn-block" id="updatelpc"><i class="feather icon-send"></i> Submit</button>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {

        function loadlpc() {
            var tahun = $("#tahun").val();
            var bulan = $("#bulan").val();
            $.ajax({
                type: 'POST'
                , url: '/lpc/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadlpc").html(respond);
                }
            });
        }
        $("#updatelpc").click(function(e) {
            e.preventDefault();
            var kode_lpc = $("#kode_lpc").val();
            var tgl_lpc = $("#tgl_lpc").val();
            if (tgl_lpc == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_lpc").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/lpc/update'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_lpc: kode_lpc
                        , tgl_lpc: tgl_lpc
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 0) {
                            swal("Berhasil ", "Data Berhasil Update", "success");
                        } else {
                            swal("Gagal", "Data Gagal Update", "danger");
                        }

                        loadlpc();
                        $("#mdleditlpc").modal("hide");
                    }
                });
            }
        });
    });

</script>
