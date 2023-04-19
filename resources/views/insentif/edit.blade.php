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
<form action="/insentif/{{ Crypt::encrypt($insentif->kode_insentif) }}/update" method="POST" id="frminsentif">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Berlaku" value="{{ $insentif->tgl_berlaku }}" field="tgl_berlaku" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="nik" id="nik" class="form-control">
                    <option value="">Pilih Karyawan</option>
                    @foreach ($karyawan as $d)
                    <option {{ $insentif->nik == $d->nik ? 'selected' : '' }} value="{{ $d->nik }}"> {{ $d->nik }} - {{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="divider divider-left">
                <div class="divider-text">Insentif Umum</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Masa Kerja</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Masa Kerja" field="iu_masakerja" value="{{ rupiah($insentif->iu_masakerja) }}" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Lembur</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Lembur" field="iu_lembur" value="{{ rupiah($insentif->iu_lembur) }}" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Penempatan</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Penempatan" field="iu_penempatan" value="{{ rupiah($insentif->iu_penempatan) }}" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">KPI</label>
        </div>
        <div class="col-8">
            <x-inputtext label="KPI" field="iu_kpi" value="{{ rupiah($insentif->iu_kpi) }}" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="divider divider-left">
                <div class="divider-text">Insentif Manager</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Ruang Lingkup</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Ruang Lingkup" value="{{ rupiah($insentif->im_ruanglingkup) }}" field="im_ruanglingkup" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Penempatan</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Penempatan" field="im_penempatan" value="{{ rupiah($insentif->im_penempatan) }}" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Kinerja</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Kinerja" field="im_kinerja" value="{{ rupiah($insentif->im_kinerja) }}" icon="feather icon-file" right />
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit"><i class="feather icon-send mr-1"></i>Update Insenfit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $("#nik").selectize();
    $("#iu_masakerja,#iu_lembur,#iu_penempatan,#iu_kpi,#im_ruanglingkup,#im_penempatan,#im_kinerja").maskMoney();
    $("#frmInsentif").submit(function(e) {
        //e.preventDefault();
        var nik = $("#nik").val();
        var tgl_berlaku = $("#tgl_berlaku").val();
        if (tgl_berlaku == "") {
            swal({
                title: 'Oops'
                , text: 'Tanggal Berlaku Harus Diisi !'
                , icon: 'warning'
                , showConfirmButton: false
            }).then(function() {
                $("#tgl_berlaku").focus();
            });
            return false;
        } else if (nik == "") {
            swal({
                title: 'Oops'
                , text: 'Nik Harus Diisi !'
                , icon: 'warning'
                , showConfirmButton: false
            }).then(function() {
                $("#nik").focus();
            });
            return false;
        }

    });

</script>
