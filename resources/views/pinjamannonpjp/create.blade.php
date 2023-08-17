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

<form method="POST" action="/pinjaman/store" id="frmPinjaman">
    @csrf
    <input type="hidden" id="cekpembayaran">
    <div class="row" id="step1">
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
                    <td>{{ $karyawan->id_perusahaan=="MP" ? "Makmur Permata" : "CV.Pacific Tasikmalaya" }}</td>
                </tr>
                <tr>
                    <th>Kantor</th>
                    <td>{{ $karyawan->nama_cabang }}</td>
                </tr>
                <tr>
                    <th>Masa Kerja</th>
                    <td>
                        @php
                        $awal = date_create($karyawan->tgl_masuk);
                        $akhir = date_create(date('Y-m-d')); // waktu sekarang
                        $diff = date_diff( $awal, $akhir );
                        echo $diff->y . ' tahun, '.$diff->m.' bulan, '.$diff->d.' Hari'
                        @endphp
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <input type="hidden" name="status_karyawan" id="status_karyawan" value="{{ $karyawan->status_karyawan }}">
                        {{ $karyawan->status_karyawan=="T" ? "Karyawan Tetap" : "Karyawan Kontrak" }}
                    </td>
                </tr>



            </table>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tanggal Pinjaman</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tanggal Pinjaman" value="" field="tgl_pinjaman" icon="feather icon-calendar" datepicker />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Jumlah Pinjaman</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Jumlah Pinjaman" value="" field="jml_pinjaman" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Angsuran</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Angsuran" value="" field="angsuran" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Jumlah Angsuran / Bulan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Jumlah Angsuran" value="" field="jml_angsuran" icon="feather icon-file" right readonly />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Mulai Cicilan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Mulai Cicilan" value="" field="mulai_cicilan" icon="feather icon-calendar" readonly />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button href="#" class="btn btn-primary btn-block" id="btnSubmit"><i class="feather icon-send mr-1"></i>Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="step2">

    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
