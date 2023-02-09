<form action="/pembayaran/{{ $giro->id_giro }}/updategiro" id="frmeditGiro" method="POST">
    @csrf
    <input type="hidden" id="sisabayaredit" value="{{ $sisabayar }}">
    <div class="form-body">
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Tanggal Pencatatan Giro" value="{{ $giro->tgl_giro }}" field="tgl_giro_edit" icon="feather icon-calendar" datepicker />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    @if (Auth::user()->level=="salesman")
                    <input type="hidden" name="id_karyawan" id="id_karyawan_edit" value="{{ $giro->id_karyawan }}" />
                    @else
                    <select name="id_karyawan" id="id_karyawan_edit" class="form-control">
                        <option value="">Salesman</option>
                        @foreach ($salesman as $d)
                        <option @if ($giro->id_karyawan == $d->id_karyawan)
                            selected
                            @endif value="{{ $d->id_karyawan }}">{{ $d->nama_karyawan }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-inputtext label="No. Giro" field="no_giro_edit" value="{{ $giro->no_giro }}" icon="feather icon-credit-card" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Nama Bank" field="namabank_giro_edit" value="{{ $giro->namabank }}" icon="fa fa-bank" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Jatuh Tempo" field="tglcair_edit" value="{{ $giro->tglcair }}" icon="feather icon-calendar" datepicker />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Jumlah" field="jumlah_giro_edit" value="{{ rupiah($giro->jumlah) }}" icon="feather icon-file" right />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <button class="btn btn-primary btn-block"><i class="feather icon-send"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {

        function cektutuplaporan(tanggal) {
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
                    $("#cektutuplaporan").val(respond);
                }
            });
        }

        $("#tgl_giro_edit").change(function() {
            cektutuplaporan($(this).val());
        });

        $("#jumlah_giro_edit").maskMoney();

        $("#frmeditGiro").submit(function(e) {
            //e.preventDefault();
            var tgl_giro = $("#tgl_giro_edit").val();
            var id_karyawan = $("#id_karyawan_edit").val();
            var no_giro = $("#no_giro_edit").val();
            var namabank = $("#namabank_giro_edit").val();
            var tglcair = $("#tglcair_edit").val();
            var sisabayar = "{{ $sisabayar }}";
            var jumlah = $("#jumlah_giro_edit").val();
            var jmlbayar = parseInt(jumlah.replace(/\./g, ''));
            var cektutuplaporan = $("#cektutuplaporan").val();
            var sisabayar = parseInt($("#sisabayaredit").val());
            // alert(sisabayar);
            // return false;
            //alert(sisabayar);
            if (cektutuplaporan > 0) {
                swal("Peringatan", "Laporan Periode Ini Sudah Ditutup !", "warning");
                return false;
            } else if (tgl_giro == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Giro Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_giro_edit").focus();
                });
                return false;
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman Penagih Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#id_karyawan_giro_edit").focus();
                });
                return false;
            } else if (no_giro == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Giro Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_giro_edit").focus();
                });
                return false;
            } else if (namabank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#namabank_giro_edit").focus();
                });
                return false;
            } else if (tglcair == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jatuh Temp Harus Diisi   !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tglcair_edit").focus();
                });
                return false;
            } else if (jumlah == "" || jumlah === "0") {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah  Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah_giro_edit").focus();
                });
                return false;
            } else if (jmlbayar > parseInt(sisabayar)) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Bayar Melebihi Sisa Bayar  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah_giro_edit").focus();
                });
                return false;
            } else {
                return true;
            }
        })
    });

</script>
