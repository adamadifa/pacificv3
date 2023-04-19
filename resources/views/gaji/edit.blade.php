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
<form action="/gaji/{{ Crypt::encrypt($gaji->kode_gaji) }}/update" method="POST" id="frmGaji">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Berlaku" value="{{ $gaji->tgl_berlaku }}" field="tgl_berlaku" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="nik" id="nik" class="form-control">
                    <option value="">Pilih Karyawan</option>
                    @foreach ($karyawan as $d)
                    <option {{ $gaji->nik == $d->nik ? 'selected' : '' }} value="{{ $d->nik }}"> {{ $d->nik }} - {{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Gaji Pokok</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Gaji Pokok" value="{{ rupiah($gaji->gaji_pokok) }}" field="gaji_pokok" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Tunjangan Jabatan</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Tunjangan Jabatan" value="{{ rupiah($gaji->t_jabatan) }}" field="t_jabatan" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Tunjangan Masa Kerja</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Tunjangan Masa Kerja" value="{{ rupiah($gaji->t_masakerja) }}" field="t_masakerja" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">T. Tanggung Jawab</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Tunjangan Tanggung Jawab" value="{{ rupiah($gaji->t_tanggungjawab) }}" field="t_tanggungjawab" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Tunjangan Makan</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Tunjangan Makan" value="{{ rupiah($gaji->t_makan) }}" field="t_makan" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Tunjangan Istri</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Tunjangan Istri" value="{{ rupiah($gaji->t_istri) }}" field="t_istri" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Tunj. Skill Khusus</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Tunjangan Skill Khusus" value="{{ rupiah($gaji->t_skill) }}" field="t_skill" icon="feather icon-file" right />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit"><i class="feather icon-send mr-1"></i>Update Gaji</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $("#nik").selectize();
    $("#gaji_pokok,#t_jabatan,#t_masakerja,#t_tanggungjawab,#t_istri,#t_makan,#t_skill").maskMoney();
    $("#frmGaji").submit(function(e) {
        //e.preventDefault();
        var nik = $("#nik").val();
        var gaji_pokok = $("#gaji_pokok").val();
        var t_jabatan = $("#t_jabatan").val();
        var t_masakerja = $("#t_masakerja").val();
        var t_tanggungjawab = $("#t_tanggungjawab").val();
        var t_makan = $("#t_makan").val();
        var t_skill = $("#t_skill").val();
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
        } else if (gaji_pokok == "") {
            swal({
                title: 'Oops'
                , text: 'Gaji Pokok Harus Diisi !'
                , icon: 'warning'
                , showConfirmButton: false
            }).then(function() {
                $("#gaji_pokok").focus();
            });
            return false;
        }

    });

</script>
