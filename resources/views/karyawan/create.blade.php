<style>
    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

    .col-4,
    .col-5,
    .col-6,
    .col-3 {
        padding-right: 1px !important;
    }

</style>
<form action="/supplier/store" method="post" id="frmSupplier">
    @csrf
    <div class="row" sty>
        <div class="col-12">
            <x-inputtext label="NIK" field="nik" icon="feather icon-credit-card" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. KTP" field="no_ktp" icon="feather icon-credit-card" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama Karyawan" field="nama_karyawan" icon="feather icon-user" />
        </div>
    </div>
    <div class="row">
        <div class="col-5">
            <x-inputtext label="Tempat Lahir" field="tempat_lahir" icon="feather icon-map-pin" />
        </div>
        <div class="col-7">
            <x-inputtext label="Tanggal Lahir" field="tgl_lahir" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <textarea name="alamat" id="alamat" cols="30" rows="5" class="form-control" placeholder="Alamat"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                    <option value="">Jenis Kelamin</option>
                    <option value="1">Laki Laki</option>
                    <option value="2">Perempuan</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. HP" field="no_hp" icon="feather icon-phone" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="status_kawin" id="status_kawin" class="form-control">
                    <option value="">Status Perkawinan</option>
                    <option value="1">Belum Menikah</option>
                    <option value="2">Menikah</option>
                    <option value="3">Cerai Hidup</option>
                    <option value="4">Duda</option>
                    <option value="5">Janda</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-control">
                    <option value="">Pendidikan Terakhir</option>
                    <option value="SD">SD</option>
                    <option value="SMP">SMP</option>
                    <option value="SMA">SMA</option>
                    <option value="SMK">SMK</option>
                    <option value="D1">D1</option>
                    <option value="D2">D2</option>
                    <option value="D3">D3</option>
                    <option value="D4">D4</option>
                    <option value="S1">S1</option>
                    <option value="S2">S2</option>
                    <option value="S3">S3</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <div class="form-group">
                <select name="id_perusahaan" id="id_perusahaan" class="form-control">
                    <option value="">Perusahaan</option>
                    <option value="MP">MAKMUR PERMATA</option>
                    <option value="PCF">PACIFIC</option>
                </select>
            </div>
        </div>
        <div class="col-8">
            <div class="form-group">
                <select name="id_perusahaan" id="id_perusahaan" class="form-control">
                    <option value="">Kantor Cabang / Pusat</option>
                    @foreach ($cabang as $d)
                    <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-5">
            <div class="form-group">
                <select name="id_perusahaan" id="id_perusahaan" class="form-control">
                    <option value="">Departemen</option>
                    @foreach ($departemen as $d)
                    <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-7">
            <div class="form-group">
                <select name="id_perusahaan" id="id_perusahaan" class="form-control">
                    <option value="">Jabatan</option>
                    @foreach ($jabatan as $d)
                    <option value="{{ $d->id }}">{{ $d->nama_jabatan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="id_perusahaan" id="id_perusahaan" class="form-control">
                    <option value="">Grup</option>
                    @foreach ($group as $d)
                    <option value="{{ $d->id }}">{{ $d->nama_group }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Masuk" field="tgl_masuk" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block"><i class="feather icon-send mr-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
