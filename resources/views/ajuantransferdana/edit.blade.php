<style>
    .form-label {
        font-weight: bold;
        margin-top: 10px;
    }

    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }
</style>

<form action="/ajuantransferdana/{{ Crypt::encrypt($ajuantransferdana->no_pengajuan) }}/update" method="POST"
    id="frmAjuantransferdana">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Pengajuan" value="{{ $ajuantransferdana->tgl_pengajuan }}" field="tgl_pengajuan"
                icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama" field="nama" value="{{ $ajuantransferdana->nama }}"
                icon="feather icon-user" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama Bank" field="nama_bank" value="{{ $ajuantransferdana->nama_bank }}"
                icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. Rekening" value="{{ $ajuantransferdana->no_rekening }}" field="no_rekening"
                icon="feather icon-credit-card" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Jumlah" value="{{ rupiah($ajuantransferdana->jumlah) }}" field="jumlah"
                icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" value="{{ $ajuantransferdana->keterangan }}"
                icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        @if (Auth::user()->kode_cabang == 'PCF')
            <div class="col-lg-12 col-sm-12">
                <div class="form-group  ">
                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option {{ $ajuantransferdana->kode_cabang == $c->kode_cabang ? 'selected' : '' }}
                                value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary w-100" type="submit"><i class="feather icon-send mr-1"></i>Update</button>
        </div>
    </div>


</form>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script>
    $(function() {
        $("#jumlah").maskMoney();
        $("#frmAjuantransferdana").submit(function() {
            var tgl_pengajuan = $("#tgl_pengajuan").val();
            var nama = $("#nama").val();
            var nama_bank = $("#nama_bank").val();
            var no_rekening = $("#no_rekening").val();
            var jumlah = $("#jumlah").val();
            var keterangan = $("#keterangan").val();
            if (tgl_pengajuan == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Pengajuan Harus Diisi!',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tgl_pengajuan").focus();
                });
                return false;
            } else if (nama == "") {
                swal({
                    title: 'Oops',
                    text: 'Nama Harus Diisi!',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#nama").focus();
                });
                return false;
            } else if (nama_bank == "") {
                swal({
                    title: 'Oops',
                    text: 'Nama Bank Harus Diisi!',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#nama_bank").focus();
                });
                return false;
            } else if (no_rekening == "") {
                swal({
                    title: 'Oops',
                    text: 'No. Rekening Harus Diisi!',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#no_rekening").focus();
                });
                return false;
            } else if (jumlah == "") {
                swal({
                    title: 'Oops',
                    text: 'Jumlah Harus Diisi!',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops',
                    text: 'Keterangan Harus Diisi!',
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
