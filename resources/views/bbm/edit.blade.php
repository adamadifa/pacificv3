<form action="{{ route('bbm.update') }}" id="frmbbm" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row" hidden>
        <div class="col-12">
            <div class="form-group">
                <x-inputtext field="id" label="id" value="{{ $bbm->id }}" icon="feather icon-calendar" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <x-inputtext field="tanggal" label="tanggal" value="{{ $bbm->tanggal }}" icon="feather icon-calendar"
                    datepicker />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                @php
                    $kode_cabang = Auth::user()->kode_cabang;
                    $kendaraan = DB::select("SELECT * FROM kendaraan WHERE kode_cabang = 'TSM' ORDER BY no_polisi ASC");
                @endphp
                <select class="form-control" name="no_polisi" id="no_polisi">
                    <option value="">Pilih Kendaraan</option>
                    @foreach ($kendaraan as $k)
                        <option {{ $k->no_polisi == $bbm->no_polisi ? 'selected' : '' }} value="{{ $k->no_polisi }}">
                            {{ $k->no_polisi }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                @php
                    $kode_cabang = Auth::user()->kode_cabang;
                    $driver = DB::select("SELECT * FROM driver_helper WHERE kode_cabang = 'TSM' AND kategori = 'Driver' ORDER BY nama_driver_helper ASC");
                @endphp
                <select class="form-control" name="id_driver" id="id_driver">
                    <option value="">Pilih Driver</option>
                    @foreach ($driver as $k)
                        <option {{ $k->id_driver_helper == $bbm->id_driver ? 'selected' : '' }}
                            value="{{ $k->id_driver_helper }}">{{ $k->nama_driver_helper }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="tujuan" id="tujuan" value="{{ $bbm->tujuan }}" class="form-control"
                    placeholder="Tujuan">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="saldo_awal" id="saldo_awal" value="{{ $bbm->saldo_awal }}"
                    class="form-control money" placeholder="Saldo Awal">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="saldo_akhir" id="saldo_akhir" value="{{ $bbm->saldo_akhir }}"
                    class="form-control money" placeholder="Saldo Akhir">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="jumlah_liter" id="jumlah_liter" value="{{ $bbm->jumlah_liter }}"
                    class="form-control money" placeholder="Jumlah Liter">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="keterangan" id="keterangan" value="{{ $bbm->keterangan }}"
                    class="form-control" placeholder="Keterangan">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit" id="simpanbbm"><i
                        class="feather icon-send"></i> Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script src="{{ asset('app-assets/js/jquery.maskMoney.js') }}"></script>
<script>
    $(function() {

        $("#frmbbm").submit(function(e) {
            var tgl_bbm = $("#tgl_bbm").val();
            var no_fak_penj = $("#no_fak_penj").val();
            var nominal = $("#nominal").val();
            var hasil_konfirmasi = $("#hasil_konfirmasi").val();
            var catatan = $("#catatan").val();
            var action = $("#action").val();
            var saran = $("#saran").val();
            if (tgl_bbm == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tgl_bbm").focus();
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
