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
<form action="/karyawan/{{ Crypt::encrypt($karyawan->nik) }}/update" method="post" id="frmSupplier">
    @csrf
    <div class="row" sty>
        <div class="col-12">
            <x-inputtext label="NIK" field="nik" icon="feather icon-credit-card" value="{{ $karyawan->nik }}" readonly />

        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. KTP" field="no_ktp" icon="feather icon-credit-card" value="{{ $karyawan->no_ktp }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama Karyawan" field="nama_karyawan" icon="feather icon-user" value="{{ $karyawan->nama_karyawan }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-5">
            <x-inputtext label="Tempat Lahir" field="tempat_lahir" icon="feather icon-map-pin" value="{{ $karyawan->tempat_lahir }}" />
        </div>
        <div class="col-7">
            <x-inputtext label="Tanggal Lahir" field="tgl_lahir" icon="feather icon-calendar" value="{{ $karyawan->tgl_lahir }}" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <textarea name="alamat" id="alamat" cols="30" rows="5" class="form-control" placeholder="Alamat">{{ $karyawan->alamat }}</textarea>
                <small class="danger"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control">
                    <option value="">Jenis Kelamin</option>
                    <option value="1" {{ $karyawan->jenis_kelamin == "1" ? 'selected' : '' }}>Laki Laki</option>
                    <option value="2" {{ $karyawan->jenis_kelamin == "2" ? 'selected' : '' }}>Perempuan</option>
                </select>
                <small class="danger"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. HP" field="no_hp" icon="feather icon-phone" value="{{ $karyawan->no_hp }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="status_kawin" id="status_kawin" class="form-control">
                    <option value="">Status Perkawinan</option>
                    <option value="1" {{ $karyawan->status_kawin == 1 ? 'selected' : '' }}>Belum Menikah</option>
                    <option value="2" {{ $karyawan->status_kawin == 2 ? 'selected' : '' }}>Menikah</option>
                    <option value="3" {{ $karyawan->status_kawin == 3 ? 'selected' : '' }}>Cerai Hidup</option>
                    <option value="4" {{ $karyawan->status_kawin == 4 ? 'selected' : '' }}>Duda</option>
                    <option value="5" {{ $karyawan->status_kawin == 5 ? 'selected' : '' }}>Janda</option>
                </select>
                <small class="danger"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-control">
                    <option value="">Pendidikan Terakhir</option>
                    <option value="SD" {{ $karyawan->pendidikan_terakhir == "SD" ? 'selected' : '' }}>SD</option>
                    <option value="SMP" {{ $karyawan->pendidikan_terakhir == "SMP" ? 'selected' : '' }}>SMP</option>
                    <option value="SMA" {{ $karyawan->pendidikan_terakhir == "SMA" ? 'selected' : '' }}>SMA</option>
                    <option value="SMK" {{ $karyawan->pendidikan_terakhir == "SMK" ? 'selected' : '' }}>SMK</option>
                    <option value="D1" {{ $karyawan->pendidikan_terakhir == "D1" ? 'selected' : '' }}>D1</option>
                    <option value="D2" {{ $karyawan->pendidikan_terakhir == "D2" ? 'selected' : '' }}>D2</option>
                    <option value="D3" {{ $karyawan->pendidikan_terakhir == "D3" ? 'selected' : '' }}>D3</option>
                    <option value="D4" {{ $karyawan->pendidikan_terakhir == "D4" ? 'selected' : '' }}>D4</option>
                    <option value="S1" {{ $karyawan->pendidikan_terakhir == "S1" ? 'selected' : '' }}>S1</option>
                    <option value="S2" {{ $karyawan->pendidikan_terakhir == "S2" ? 'selected' : '' }}>S2</option>
                    <option value="S3" {{ $karyawan->pendidikan_terakhir == "S3" ? 'selected' : '' }}>S3</option>
                </select>
                <small class="danger"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="id_perusahaan" id="id_perusahaan" class="form-control">
                    <option value="">Perusahaan</option>
                    <option value="MP" {{ $karyawan->id_perusahaan == "MP" ? 'selected' : '' }}>MAKMUR PERMATA</option>
                    <option value="PCF" {{ $karyawan->id_perusahaan == "PCF" ? 'selected' : '' }}>PACIFIC</option>
                </select>
                <small class="danger"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="id_kantor" id="id_kantor" class="form-control">
                    <option value="">Kantor Cabang / Pusat</option>
                    @foreach ($cabang as $d)
                    <option {{ $karyawan->id_kantor == $d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                    @endforeach
                </select>
                <small class="danger"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_dept" id="kode_dept" class="form-control">
                    <option value="">Departemen</option>
                    @foreach ($departemen as $d)
                    <option {{ $karyawan->kode_dept == $d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                    @endforeach
                </select>
                <small class="danger"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="id_jabatan" id="id_jabatan" class="form-control">
                    <option value="">Jabatan</option>
                    @foreach ($jabatan as $d)
                    <option {{ $karyawan->id_jabatan == $d->id ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->nama_jabatan }}</option>
                    @endforeach
                </select>
                <small class="danger"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="grup" id="grup" class="form-control">
                    <option value="">Grup</option>
                    @foreach ($group as $d)
                    <option {{ $karyawan->grup == $d->id ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->nama_group }}</option>
                    @endforeach
                </select>
                <small class="danger"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="klasifikasi" id="klasifikasi" class="form-control">
                    <option value="">Klasifikasi</option>
                    <option value="TKL" {{ $karyawan->klasifikasi == "TKL" ? 'selected' : '' }}>TKL</option>
                    <option value="ADMINISTRASI" {{ $karyawan->klasifikasi == "ADMINISTRASI" ? 'selected' : '' }}>ADMINISTRASI</option>
                    <option value="PENJUALAN" {{ $karyawan->klasifikasi == "PENJUALAN" ? 'selected' : '' }}>PENJUALAN</option>

                </select>
                <small class="danger"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Masuk" value="{{ $karyawan->tgl_masuk }}" field="tgl_masuk" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="status_karyawan" id="status_karyawan" class="form-control">
                    <option value="">Status Karyawan</option>
                    <option value="K" {{ $karyawan->status_karyawan == "K" ? 'selected' : '' }}>Kontrak</option>
                    <option value="T" {{ $karyawan->status_karyawan == "T" ? 'selected' : '' }}>Tetap</option>
                </select>
                <small class="danger"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit"><i class="feather icon-send mr-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</form>

<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        const nikEl = document.querySelector('#nik');
        const noktpEl = document.querySelector('#no_ktp');
        const namakaryawanEl = document.querySelector('#nama_karyawan');
        const tempatlahirEl = document.querySelector('#tempat_lahir');
        const tgllahirEl = document.querySelector('#tgl_lahir');
        const alamatEl = document.querySelector('#alamat');
        const jeniskelaminEl = document.querySelector('#jenis_kelamin');
        const nohpEl = document.querySelector('#no_hp');
        const statuskawinEl = document.querySelector('#status_kawin');
        const pendidikanterakhirEl = document.querySelector('#pendidikan_terakhir');
        const idperusahaanEl = document.querySelector('#id_perusahaan');
        const idkantorEl = document.querySelector('#id_kantor');
        const kodedeptEl = document.querySelector('#kode_dept');
        const idjabatanEl = document.querySelector('#id_jabatan');
        const grupEl = document.querySelector('#grup');
        const tglmasukEl = document.querySelector('#tgl_masuk');
        const klasifikasiEl = document.querySelector('#klasifikasi');
        const statuskaryawanEl = document.querySelector('#status_karyawan');
        const form = document.querySelector('#frmSupplier');
        const checkNik = () => {
            let valid = false;
            const min = 3
                , max = 25;
            const nik = nikEl.value.trim();

            if (!isRequired(nik)) {
                showError(nikEl, 'Nik Tidak Boleh Kosong.');
            } else {
                showSuccess(nikEl);
                valid = true;
            }
            return valid;
        };

        const checkNoKtp = () => {
            let valid = false;
            const no_ktp = noktpEl.value.trim();

            if (!isRequired(no_ktp)) {
                showError(noktpEl, 'No. KTP Tidak Boleh Kosong.');
            } else if (!isAngka(no_ktp)) {
                showError(noktpEl, 'Nik Harus Angka');
            } else {
                showSuccess(noktpEl);
                valid = true;
            }
            return valid;
        };

        const checkNamakaryawan = () => {
            let valid = false;
            const nama_karyawan = namakaryawanEl.value.trim();
            if (!isRequired(nama_karyawan)) {
                showError(namakaryawanEl, 'Nama Karyawan Tidak Boleh Kosong.');
            } else {
                showSuccess(namakaryawanEl);
                valid = true;
            }
            return valid;
        };

        const checkTempatlahir = () => {
            let valid = false;
            const tempat_lahir = tempatlahirEl.value.trim();
            if (!isRequired(tempat_lahir)) {
                showError(tempatlahirEl, 'Tempat Lahir Tidak Boleh Kosong.');
            } else {
                showSuccess(tempatlahirEl);
                valid = true;
            }
            return valid;
        };

        const checkTgllahir = () => {
            let valid = false;
            const tgl_lahir = tgllahirEl.value.trim();
            if (!isRequired(tgl_lahir)) {
                showError(tgllahirEl, 'Tanggal Lahir Tidak Boleh Kosong.');
            } else {
                showSuccess(tgllahirEl);
                valid = true;
            }
            return valid;
        };

        const checkAlamat = () => {
            let valid = false;
            const alamat = alamatEl.value.trim();
            if (!isRequired(alamat)) {
                showError(alamatEl, 'Alamat Tidak Boleh Kosong.');
            } else {
                showSuccess(alamatEl);
                valid = true;
            }
            return valid;
        };

        const checkJeniskelamin = () => {
            let valid = false;
            const jenis_kelamin = jeniskelaminEl.value.trim();
            if (!isRequired(jenis_kelamin)) {
                showError(jeniskelaminEl, 'Jenis Kelamin Tidak Boleh Kosong.');
            } else {
                showSuccess(jeniskelaminEl);
                valid = true;
            }
            return valid;
        };

        const checkNohp = () => {
            let valid = false;
            const no_hp = nohpEl.value.trim();
            if (!isRequired(no_hp)) {
                showError(nohpEl, 'No. HP Tidak Boleh Kosong.');
            } else {
                showSuccess(nohpEl);
                valid = true;
            }
            return valid;
        };

        const checkStatuskawin = () => {
            let valid = false;
            const status_kawin = statuskawinEl.value.trim();
            if (!isRequired(status_kawin)) {
                showError(statuskawinEl, 'Status Kawin Tidak Boleh Kosong.');
            } else {
                showSuccess(statuskawinEl);
                valid = true;
            }
            return valid;
        };

        // const checkPendidikanterakhir = () => {
        //     let valid = false;
        //     const pendidikan_terakhir = pendidikanterakhirEl.value.trim();
        //     if (!isRequired(pendidikan_terakhir)) {
        //         showError(pendidikanterakhirEl, 'Pendidikan Terakhir Tidak Boleh Kosong.');
        //     } else {
        //         showSuccess(pendidikanterakhirEl);
        //         valid = true;
        //     }
        //     return valid;
        // };

        const checkPerusahan = () => {
            let valid = false;
            const id_perusahaan = idperusahaanEl.value.trim();
            if (!isRequired(id_perusahaan)) {
                showError(idperusahaanEl, 'Perusahaan Tidak Boleh Kosong.');
            } else {
                showSuccess(idperusahaanEl);
                valid = true;
            }
            return valid;
        };


        const checkKantor = () => {
            let valid = false;
            const id_kantor = idkantorEl.value.trim();
            if (!isRequired(id_kantor)) {
                showError(idkantorEl, 'Kantor Cabang Tidak Boleh Kosong.');
            } else {
                showSuccess(idkantorEl);
                valid = true;
            }
            return valid;
        };

        const checkDepartemen = () => {
            let valid = false;
            const kode_dept = kodedeptEl.value.trim();
            if (!isRequired(kode_dept)) {
                showError(kodedeptEl, 'Departemen Tidak Boleh Kosong.');
            } else {
                showSuccess(kodedeptEl);
                valid = true;
            }
            return valid;
        };


        const checkGrup = () => {
            let valid = false;
            const grup = grupEl.value.trim();
            if (!isRequired(grup)) {
                showError(grupEl, 'Grup Tidak Boleh Kosong.');
            } else {
                showSuccess(grupEl);
                valid = true;
            }
            return valid;
        };

        const checkJabatan = () => {
            let valid = false;
            const id_jabatan = idjabatanEl.value.trim();
            if (!isRequired(id_jabatan)) {
                showError(idjabatanEl, 'Jabatan Tidak Boleh Kosong.');
            } else {
                showSuccess(idjabatanEl);
                valid = true;
            }
            return valid;
        };

        const checkTglmasuk = () => {
            let valid = false;
            const tgl_masuk = tglmasukEl.value.trim();
            if (!isRequired(tgl_masuk)) {
                showError(tglmasukEl, 'Tanggal Masuk Tidak Boleh Kosong.');
            } else {
                showSuccess(tglmasukEl);
                valid = true;
            }
            return valid;
        };

        const checkKlasifikasi = () => {
            let valid = false;
            const klasifikasi = klasifikasiEl.value.trim();
            if (!isRequired(klasifikasi)) {
                showError(klasifikasiEl, 'Klasifikasi Tidak Boleh Kosong.');
            } else {
                showSuccess(klasifikasiEl);
                valid = true;
            }
            return valid;
        };

        const checkStatuskaryawan = () => {
            let valid = false;
            const statuskaryawan = statuskaryawanEl.value.trim();
            if (!isRequired(statuskaryawan)) {
                showError(statuskaryawanEl, 'Klasifikasi Tidak Boleh Kosong.');
            } else {
                showSuccess(statuskaryawanEl);
                valid = true;
            }
            return valid;
        };

        const isRequired = value => value === '' ? false : true;
        const isBetween = (length, min, max) => length < min || length > max ? false : true;
        const isAngka = (angka) => {
            const re = new RegExp("^(?=.*[0-9])");
            return re.test(angka);
        };


        const showError = (input, message) => {
            // get the form-field element
            const formField = input.parentElement.parentElement.parentElement;
            const formTextarea = input.parentElement;
            // alert(formField);
            // add the error class
            formField.classList.remove('success');
            formField.classList.add('error');

            formTextarea.classList.remove('success');
            formTextarea.classList.add('error');
            // show the error message
            const error = formField.querySelector('small');
            const error2 = formTextarea.querySelector('small');
            error.textContent = message;
            //error2.textContent = message;
        };

        const showSuccess = (input) => {
            // get the form-field element
            const formField = input.parentElement.parentElement.parentElement;
            const formTextarea = input.parentElement;
            //alert(formField);

            // remove the error class
            formField.classList.remove('error');
            formField.classList.add('success');

            formTextarea.classList.remove('error');
            formTextarea.classList.add('success');

            // hide the error message
            const error = formField.querySelector('small');
            error.textContent = '';
        }

        form.addEventListener('submit', function(e) {
            // prevent the form from submitting
            e.preventDefault();


            // validate forms
            let isNikValid = checkNik()
                , isNoKtpValid = checkNoKtp()
                , isNamakaryawanValid = checkNamakaryawan()
                , isTempatlahirValid = checkTempatlahir()
                , isTgllahirValid = checkTgllahir()
                , isAlamatValid = checkAlamat()
                , isJeniskelaminValid = checkJeniskelamin()
                , isNohpValid = checkNohp()
                , isStatuskawinValid = checkStatuskawin()

                , isIdperusahaanValid = checkPerusahan()
                , isIdkantorValid = checkKantor()
                , isKodedeptValid = checkDepartemen()
                , isGrupValid = checkGrup()
                , isIdJabatan = checkJabatan()
                , isTglmasukValid = checkTglmasuk()
                , isKlasifikasiValid = checkKlasifikasi()
                , isStatuskaryawanValid = checkStatuskaryawan();

            let isFormValid = isNikValid && isNoKtpValid && isNamakaryawanValid && isTempatlahirValid && isTgllahirValid && isAlamatValid && isJeniskelaminValid && isNohpValid && isStatuskawinValid && isIdperusahaanValid && isIdkantorValid && isKodedeptValid && isGrupValid && isIdJabatan && isTglmasukValid && isKlasifikasiValid && isStatuskaryawanValid;

            // submit to the server if the form is valid
            if (isFormValid) {
                form.submit();
            }
        });


        const debounce = (fn, delay = 500) => {
            let timeoutId;
            return (...args) => {
                // cancel the previous timer
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
                // setup a new timer
                timeoutId = setTimeout(() => {
                    fn.apply(null, args)
                }, delay);
            };
        };

        form.addEventListener('input', debounce(function(e) {
            switch (e.target.id) {
                case 'nik':
                    checkNik();
                    break;
                case 'no_ktp':
                    checkNoKtp();
                    break;
                case 'nama_karyawan':
                    checkNamakaryawan();
                    break;
                case 'tempat_lahir':
                    checkTempatlahir();
                    break;
                case 'tgl_lahir':
                    checkTgllahir();
                    break;
                case 'alamat':
                    checkAlamat();
                    break;
                case 'jenis_kelamin':
                    checkJeniskelamin();
                    break;
                case 'no_hp':
                    checkNohp();
                    break;
                case 'status_kawin':
                    checkStatuskawin();
                    break;
                case 'pendidikan_terakhir':
                    checkPendidikanterakhir();
                    break;
                case 'id_perusahaan':
                    checkPerusahan();
                    break;
                case 'id_kantor':
                    checkKantor();
                    break;
                case 'kode_dept':
                    checkDepartemen();
                    break;
                case 'grup':
                    checkGrup();
                    break;

                case 'id_jabatan':
                    checkJabatan();
                    break;
                case 'tgl_masuk':
                    checkTglmasuk();
                    break;
                case 'klasifikasi':
                    checkKlasifikasi();
                    break;
                case 'status_karyawan':
                    checkStatuskaryawan();
                    break;
            }
        }));
    });

</script>
