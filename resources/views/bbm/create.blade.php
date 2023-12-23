<form action="{{ route('bbm.store') }}" id="frmbbm" enctype="multipart/form-data" method="POST" autocomplete="off">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <x-inputtext field="tanggal" label="tanggal" icon="feather icon-calendar" datepicker />
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
                        <option value="{{ $k->no_polisi }}">{{ $k->no_polisi }}</option>
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
                        <option value="{{ $k->id_driver_helper }}">{{ $k->nama_driver_helper }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="tujuan" id="tujuan" class="form-control" placeholder="Tujuan">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="saldo_awal" id="saldo_awal" class="form-control money"
                    placeholder="Saldo Awal">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="saldo_akhir" id="saldo_akhir" class="form-control money"
                    placeholder="Saldo Akhir">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="jumlah_liter" id="jumlah_liter" class="form-control money"
                    placeholder="Jumlah Liter">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Keterangan">
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
<script>
    $(function() {

        $("#saldo_awal,#saldo_akhir,#jumlah_liter").maskMoney();

        function loadbbm() {
            $.ajax({
                type: 'POST',
                url: '{{ route('bbm.show') }}',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                cache: false,
                success: function(respond) {
                    $("#loadbbm").html(respond);
                }
            });
        }

        $("#frmbbm").submit(function(e) {
            var im = $("#im").val();
            var nominal = $("#nominal").val();
            var hasil_konfirmasi = $("#hasil_konfirmasi").val();
            var keterangan = $("#keterangan").val();
            var action = $("#action").val();
            var saran = $("#saran").val();
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
                    $("#tahun").focus();
                });
                return false;
            } else if (im == "") {
                swal({
                    title: 'Oops',
                    text: 'IM Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#im").focus();
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
            } else if (keterangan == "") {
                swal({
                    title: 'Oops',
                    text: 'Keterangan Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#keterangan").focus();
                });
                return false;
            }
        });


    });
</script>
