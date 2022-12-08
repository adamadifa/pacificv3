<form action="/pembayaran/{{ $transfer->id_transfer }}/updatetransfer" id="frmeditTransfer" method="POST">
    @csrf
    <input type="hidden" name="kode_pelanggan_edit" value="{{ $kode_pelanggan }}">
    <div class="form-body">
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Tanggal Pencatatan Transfer" value="{{ $transfer->tgl_transfer }}" field="tgl_transfer_edit" icon="feather icon-calendar" datepicker />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    @if (Auth::user()->level=="salesman")
                    <input type="hidden" name="id_karyawan" id="id_karyawan_transfer_edit" value="{{ $transfer->id_karyawan }}" />
                    @else
                    <select name="id_karyawan" id="id_karyawan_transfer_edit" class="form-control">
                        <option value="">Salesman</option>
                        @foreach ($salesman as $d)
                        <option @if ($transfer->id_karyawan == $d->id_karyawan)
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
                <x-inputtext label="Nama Bank" field="namabank_transfer_edit" value="{{ $transfer->namabank }}" icon="fa fa-bank" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Jatuh Tempo" field="tglcair_transfer_edit" value="{{ $transfer->tglcair }}" icon="feather icon-calendar" datepicker />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Jumlah" field="jumlah_transfer_edit" value="{{ rupiah($transfer->jumlah) }}" icon="feather icon-file" right />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Keterangan" field="ket_edit" value="{{ $transfer->ket }}" icon="feather icon-file" right />
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

        $("#tgl_transfer_edit").change(function() {
            cektutuplaporan($(this).val());
        });
        $("#jumlah_transfer_edit").maskMoney();

        $("#frmeditTransfer").submit(function(e) {
            //e.preventDefault();
            var tgl_transfer = $("#tgl_transfer_edit").val();
            var id_karyawan = $("#id_karyawan_transfer_edit").val();
            var namabank = $("#namabank_transfer_edit").val();
            var tglcair = $("#tglcair_transfer_edit").val();
            var sisabayar = "{{ $sisabayar }}";
            var jumlah = $("#jumlah_transfer_edit").val();
            var jmlbayar = parseInt(jumlah.replace(/\./g, ''));
            var cektutuplaporan = $("#cektutuplaporan").val();
            //alert(sisabayar);
            if (cektutuplaporan > 0) {
                swal("Peringatan", "Laporan Periode Ini Sudah Ditutup !", "warning");
                return false;
            } else if (tgl_transfer == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Transfer Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_transfer_edit").focus();
                });
                return false;
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman Penagih Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#id_karyawan_transfer_edit").focus();
                });
                return false;
            } else if (namabank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#namabank_transfer_edit").focus();
                });
                return false;
            } else if (tglcair == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jatuh Tempo Harus Diisi   !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tglcair_transfer_edit").focus();
                });
                return false;
            } else if (jumlah == "" || jumlah === "0") {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah  Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah_transfer_edit").focus();
                });
                return false;
            } else {
                return true;
            }
        })
    });

</script>
