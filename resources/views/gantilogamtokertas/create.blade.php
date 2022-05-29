<form action="/logamtokertas/store" method="POST" id="frmLogamtokertas">
    @csrf
    <input type="hidden" id="cektutuplaporan">
    <div class="row">
        <div class="col-12">
            <x-inputtext field="tgl_logamtokertas" label="Tanggal" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_cabang" id="kode_cabang" class="form-control">
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
            <x-inputtext field="jumlah_logamtokertas" label="Jumlah" icon="feather icon-file" right />
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
<script>
    $(function() {
        function cektutuplaporan() {
            var tanggal = $("#tgl_logamtokertas").val();
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "penjualan"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#frmLogamtokertas").find("#cektutuplaporan").val(respond);
                }
            });
        }
        $("#tgl_logamtokertas").change(function() {
            cektutuplaporan();
        });
        $("#jumlah_logamtokertas").maskMoney();
        $("#frmLogamtokertas").submit(function() {
            var tgl_logamtokertas = $("#tgl_logamtokertas").val();
            var kode_cabang = $("frmLogamtokertas").find("#kode_cabang").val();
            var jumlah_logamtokertas = $("#jumlah_logamtokertas").val();
            var cektutuplaporan = $("#frmLogamtokertas").find("#cektutuplaporan").val();
            if (cektutuplaporan > 0) {
                swal({
                    title: 'Oops'
                    , text: 'Periode Laporan Sudah Ditutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_logamtokertas").focus();
                });

                return false;
            } else if (tgl_logamtokertas == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_logamtokertas").focus();
                });

                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("frmLogamtokertas").find("#kode_cabang").focus();
                });

                return false;
            } else if (jumlah_logamtokertas == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("frmLogamtokertas").find("#jumlah_logamtokertas").focus();
                });

                return false;
            }
        });
    });

</script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
