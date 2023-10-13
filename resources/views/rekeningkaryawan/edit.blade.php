<form action="/rekeningkaryawan/{{ Crypt::encrypt($karyawan->nik) }}/update" method="post" id="frmSupplier">
    @csrf
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <td>NIK</td>
                    <td>{{ $karyawan->nik }}</td>
                </tr>
                <tr>
                    <td>Nama Karyawan</td>
                    <td>{{ $karyawan->nama_karyawan }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. Rekening" value="{{ $karyawan->no_rekening }}" field="no_rekening" icon="feather icon-credit-card" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama. Rekening" value="{{ $karyawan->nama_rekening }}" field="nama_rekening" icon="feather icon-user" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary w-100" type="submit">Update</button>
        </div>
    </div>
</form>
