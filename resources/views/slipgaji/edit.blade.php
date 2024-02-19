<form action="/slipgaji/{{ Crypt::encrypt($slipgaji->kode_gaji) }}/update" method="POST" id="frmSlipgaji">
    @csrf
    <div class="row" id="pilihbulan">
        <div class="col-12">
            {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
            <div class="form-group">
                <select class="form-control" id="bulan" name="bulan">
                    <option value="">Bulan</option>
                    <?php
                $bulanini = date("m");
                for ($i = 1; $i < count($namabulan); $i++) {
                ?>
                    <option <?php if ($slipgaji->bulan == $i) {
                        echo 'selected';
                    } ?> value="<?php echo $i; ?>">
                        <?php echo $namabulan[$i]; ?></option>
                    <?php
                }
                ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row" id="pilihtahun">
        <div class="col-12">
            <div class="form-group">
                <select class="form-control" id="tahun" name="tahun">
                    <?php
                $tahunmulai = 2020;
                for ($thn = $tahunmulai; $thn <= date('Y') +1; $thn++) {
                ?>
                    <option <?php if ($slipgaji->tahun == $thn) {
                        echo 'Selected';
                    } ?> value="<?php echo $thn; ?>">
                        <?php echo $thn; ?></option>
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
                <select name="status" id="status" class="form-control">
                    <option value="">Pilih Status</option>
                    <option value="1" {{ $slipgaji->status === '1' ? 'selected' : '' }}>Publish</option>
                    <option value="0" {{ $slipgaji->status === '0' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100"><i class="feather icon-send"></i> Submit</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        $("#frmSlipgaji").submit(function(e) {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            var status = $("#status").val();

            if (bulan == "") {
                swal({
                    title: 'Oops',
                    text: 'Bulan Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#bulan").focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops',
                    text: 'Tahun Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#bulan").focus();
                });

                return false;
            }
        });
    });
</script>
