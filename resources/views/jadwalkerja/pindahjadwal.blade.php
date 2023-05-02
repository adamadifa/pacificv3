<div class="row">
    <div class="col-12">
        <table class="table">
            <tr>
                <th>NIK</th>
                <td>{{ $karyawan->nik }}</td>
            </tr>
            <tr>
                <th>Nama Karyawan</th>
                <td>{{ $karyawan->nama_karyawan }}</td>
            </tr>
            <tr>
                <th>Kantor</th>
                <td>{{ $karyawan->id_kantor }}</td>
            </tr>
            <tr>
                <th>Departemen</th>
                <td>{{ $karyawan->kode_dept }}</td>
            </tr>
            <tr>
                <th>Jadwal Saat Ini</th>
                <td>{{ $karyawan->nama_jadwal }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <form action="/jadwalkerja/updatejadwalkerja" method="POST">
            <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <select name="kode_jadwal" id="kode_jadwal" class="form-control">
                            <option value="">Pilih Jadwal</option>
                            @foreach ($jadwal as $d)
                            <option value="{{ $d->kode_jadwal }}">{{ $d->nama_jadwal }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button class="btn btn-primary w-100"><i class="feather icon-refresh-ccw mr-1"></i>Pindahkan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
