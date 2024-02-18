<form action="" method="POST">
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
                    <option <?php if ($bulanini == $i) {
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
                    <option <?php if (date('Y') == $thn) {
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
                    <option value="1">Publish</option>
                    <option value="0">Pending</option>
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
