<form action="/setoranpusat/{{ Crypt::encrypt($setoranpusat->kode_setoranpusat) }}/terimasetoran" method="POST" id="frmTerimasetoran">
    @csrf
    <table class="table">
        <tr>
            <td>Tanggal</td>
            <td>{{ DateToIndo2($setoranpusat->tgl_setoranpusat) }}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>{{ $setoranpusat->keterangan }}</td>
        </tr>
        <tr>
            <td>Cabang</td>
            <td>{{ $setoranpusat->kode_cabang }}</td>
        </tr>
        <tr>
            <td>Bank</td>
            <td>{{ $setoranpusat->nama_bank }}</td>
        </tr>
        <tr>
            <td>Uang Kertas</td>
            <td class="text-right">{{ rupiah($setoranpusat->uang_kertas) }}</td>
        </tr>
        <tr>
            <td>Uang Logam</td>
            <td class="text-right">{{ rupiah($setoranpusat->uang_logam) }}</td>
        </tr>
    </table>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Penerimaan Setoran" field="tgl_diterimapusat" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select class="form-control" id="bankpenerima" name="bank">
                    <option value="">Bank Penerima</option>
                    @foreach ($bank as $d)
                    <option value="{{$d->kode_bank}}">{{$d->nama_bank}}</option>
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
                    <option <?php if ($bulanini == $i) {echo "selected";} ?> value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
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
                    <option <?php if (date('Y') == $thn) { echo "Selected";} ?> value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
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
                <button class="btn btn-primary btn-block">
                    <i class="feather icon-send"></i>
                    Submit
                </button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#frmTerimasetoran").submit(function() {
            var tgl_diterimapusat = $("#tgl_diterimapusat").val();
            var bank = $("#bankpenerima").val();
            if (tgl_diterimapusat == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_diterimapusat").focus();
                });
                return false;
            } else if (bank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank Harus Dipilih!'
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
