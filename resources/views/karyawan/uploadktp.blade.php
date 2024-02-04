<form action="/karyawan/{{ Crypt::encrypt($karyawan->nik) }}/uploadktp" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <tr>
                    <th>NIK</th>
                    <td>
                        <input type="hidden" name="nik" id="nik" value="{{ $karyawan->nik }}">
                        {{ $karyawan->nik }}
                    </td>
                </tr>
                <tr>
                    <th>Nama Karyawan</th>
                    <td>{{ $karyawan->nama_karyawan }}</td>
                </tr>
                <tr>
                    <th>Departmen</th>
                    <td>{{ $karyawan->nama_dept }}</td>
                </tr>
                <tr>
                    <th>Jabatan</th>
                    <td>{{ $karyawan->nama_jabatan }}</td>
                </tr>
                <tr>
                    <th>Perusahaan</th>
                    <td>{{ $karyawan->id_perusahaan == 'MP' ? 'Makmur Permata' : 'CV.Pacific Tasikmalaya' }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <input type="file" name="ktp">
        </div>
    </div>
    @if (!empty($karyawan->ktp))
        @php
            $url = url('/storage/ktp/' . $karyawan->ktp);
        @endphp
        <img src="{{ $url }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded avatar"
            width="200">
    @endif
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button href="#" class="btn btn-primary btn-block" id="btnSubmit"><i
                        class="feather icon-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>

</form>
