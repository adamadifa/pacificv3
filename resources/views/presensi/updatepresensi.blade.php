<form action="/presensi/storeupdatepresensi" method="POST" id="frmUpdatepresensi">
    @csrf
    <input type="hidden" value="{{ $tgl }}" name="tgl_presensi">
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <th>NIK</th>
                    <td>
                        {{ $karyawan->nik }}
                        <input type="hidden" value="{{ $karyawan->nik }}" name="nik">
                    </td>
                </tr>
                <tr>
                    <th>Nama Karyawan</th>
                    <td>{{ $karyawan->nama_karyawan }}</td>
                </tr>
                <tr>
                    <th>Departemen</th>
                    <td>{{ $karyawan->nama_dept }}</td>
                </tr>
                <tr>
                    <th>Jabatan</th>
                    <td>{{ $karyawan->nama_jabatan }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row mb-1">
        <div class="col-12">
            <select name="status" id="status" class="form-control">
                <option value="">Status Kehadiran</option>
                <option value="h" {{ $cek != null ? $cek->status == "h" ? "selected" : "" : "" }}>Hadir</option>
                <option value="i" {{ $cek != null ? $cek->status == "i" ? "selected" : "" : "" }}>Izin</option>
                <option value="s" {{ $cek != null ? $cek->status == "s" ? "selected" : "" : "" }}>Sakit</option>
                <option value="a" {{ $cek != null ? $cek->status == "a" ? "selected" : "" : "" }}>Alfa</option>
            </select>
        </div>
    </div>
    <div class="row statushadir">
        <div class="col-12">
            <select name="kode_jadwal" id="kode_jadwal" class="form-control">
                <option value="">Pilih Jadwal</option>
                @foreach ($jadwal as $d)
                <option {{ $cek != null ? $cek->kode_jadwal == $d->kode_jadwal ? 'selected' : '' : '' }} value="{{ $d->kode_jadwal }}">{{ $d->nama_jadwal }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row mt-1 statushadir">
        <div class="col-6">
            <x-inputtext label="Jam Masuk" value="{{ $cek!=null ? $cek->jam_in !=null  ? date('H:i',strtotime($cek->jam_in)) : '' : '' }}" field="jam_masuk" icon="feather icon-clock" />
        </div>
        <div class="col-6">
            <x-inputtext label="Jam Pulang" value="{{ $cek!=null ? $cek->jam_out !=null  ? date('H:i',strtotime($cek->jam_out)) : '' : '' }}" field="jam_pulang" icon="feather icon-clock" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100"><i class="feather icon-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {

        function loadstatus() {
            var status = $("#status").val();
            //alert(status);
            if (status == "h") {
                $(".statushadir").show();
            } else {
                $(".statushadir").hide();
            }
        }

        loadstatus();
        $("#status").change(function(e) {
            loadstatus();
        });
        $('#jam_masuk,#jam_pulang').mask('00:00');
        $("#frmUpdatepresensi").submit(function(e) {
            var kode_jadwal = $("#kode_jadwal").val();
            var jam_masuk = $("#jam_masuk").val();
            var jam_pulang = $("#jam_pulang").val();
            var status = $("#status").val();

            if (status == "") {
                swal({
                    title: 'Oops'
                    , text: 'Status Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#status").focus();
                });
                return false;
            } else {
                if (status == "h") {
                    if (kode_jadwal == "") {
                        swal({
                            title: 'Oops'
                            , text: 'Jadwal Harus Diisi !'
                            , icon: 'warning'
                            , showConfirmButton: false
                        }).then(function() {
                            $("#kode_jadwal").focus();
                        });
                        return false;
                    } else if (jam_masuk == "") {
                        swal({
                            title: 'Oops'
                            , text: 'Jam Masuk Harus Diisi !'
                            , icon: 'warning'
                            , showConfirmButton: false
                        }).then(function() {
                            $("#jam_masuk").focus();
                        });
                        return false;
                    } else if (jam_pulang == "") {
                        swal({
                            title: 'Oops'
                            , text: 'Jam Pulang Harus Diisi !'
                            , icon: 'warning'
                            , showConfirmButton: false
                        }).then(function() {
                            $("#jam_pulang").focus();
                        });
                        return false;
                    }
                }
            }

        });
    });

</script>
