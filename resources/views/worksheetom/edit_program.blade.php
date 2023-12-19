<form action="/worksheetom/{{ $program->kode_program }}/updateprogram" method="POST" id="frmCreateprogram">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <x-inputtext label="Tanggal" value="{{ $program->tanggal }}" field="tanggal" icon="feather icon-calendar"
                    datepicker />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <x-inputtext label="Nama Program" value="{{ $program->nama_program }}" field="nama_program"
                    icon="feather icon-file" />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <x-inputtext label="Dari" field="dari" value="{{ $program->dari }}" icon="feather icon-calendar"
                    datepicker />
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <x-inputtext label="Sampai" field="sampai" value="{{ $program->sampai }}" icon="feather icon-calendar"
                    datepicker />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_reward" id="kode_reward" class="form-control">
                    <option value="">Pilih Reward</option>
                    @foreach ($reward as $d)
                        <option {{ $program->kode_reward == $d->kode_reward ? 'selected' : '' }}
                            value="{{ $d->kode_reward }}">{{ $d->nama_reward }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row mb-1">
        @php
            $list_produk = unserialize($program->kode_produk);
        @endphp
        @foreach ($produk as $d)
            <div class="col-4">
                <div class="vs-checkbox-con vs-checkbox-primary">
                    <input type="checkbox" {{ in_array($d->kode_produk, $list_produk) ? 'checked' : '' }}
                        name="kode_produk[]" value="{{ $d->kode_produk }}">
                    <span class="vs-checkbox">
                        <span class="vs-checkbox--check">
                            <i class="vs-icon feather icon-check"></i>
                        </span>
                    </span>
                    <span class="">{{ $d->nama_barang }}</span>
                </div>
            </div>
        @endforeach

    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Jumlah Target" value="{{ rupiah($program->jml_target) }}" field="jml_target"
                icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary w-100"><i class="feather icon-send mr-1"></i>Update</button>
        </div>
    </div>
</form>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script>
    $(function() {
        $("#jml_target").maskMoney();

        $("#frmCreateprogram").submit(function() {
            var tanggal = $("#tanggal").val();
            var nama_program = $("#nama_program").val();
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();
            var kode_reward = $("#kode_reward").val();
            var jml_target = $("#jml_target").val();


            if (tanggal == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tanggal").focus();
                });
                return false;
            } else if (nama_program == "") {
                swal({
                    title: 'Oops',
                    text: 'Nama Program Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#nama_program").focus();
                });
                return false;
            } else if (dari == "") {
                swal({
                    title: 'Oops',
                    text: 'Periode Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            } else if (sampai == "") {
                swal({
                    title: 'Oops',
                    text: 'Periode Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#sampai").focus();
                });
                return false;
            } else if (kode_reward == "") {
                swal({
                    title: 'Oops',
                    text: 'Reward Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#kode_reward").focus();
                });
                return false;
            } else if ($('input[name^=kode_produk]:checked').length <= 0) {
                swal({
                    title: 'Oops',
                    text: 'Pilih Minimal 1 Produk !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#jml_target").focus();
                });

                return false;
            } else if (jml_target == "" || jml_target == 0) {
                swal({
                    title: 'Oops',
                    text: 'Jumlah Target Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#jml_target").focus();
                });
                return false;
            }

        });
    });
</script>
