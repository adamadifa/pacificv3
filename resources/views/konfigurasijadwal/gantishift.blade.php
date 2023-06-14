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
<form action="/konfigurasijadwal/storegantishift" method="POST">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="nik" id="nik" class="form-control">
                    <option value="">Pilih Karyawan</option>
                    @foreach ($karyawan as $d)
                    <option value="{{ $d->nik }}">{{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal" field="tanggal" icon="feather icon-calendar" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <select name="kode_jadwal" id="kode_jadwal" class="form-control">
                <option value="JD002">SHIFT 1</option>
                <option value="JD003">SHIFT 2</option>
                <option value="JD004">SHIFT 3</option>
            </select>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <button class="btn btn-primary w-100"><i class="feather icon-send mr-1"></i>Submit</button>
        </div>
    </div>
</form>

<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{ asset('app-assets/js/external/selectize.js') }}"></script>
<script>
    $(function() {

        var tahun_dari = "{{ $tahun_dari }}";
        var bulan_dari = "{{ $bulan_dari }}";
        var hari_dari = "{{ $hari_dari }}";
        var tahun_sampai = "{{ $tahun_sampai }}";
        var bulan_sampai = "{{ $bulan_sampai }}";
        var hari_sampai = "{{ $hari_sampai }}";
        //alert(bulan_dari);
        $("#nik").selectize();
        $('#tanggal').pickadate({
            min: [tahun_dari, bulan_dari, hari_dari]
            , max: [tahun_sampai, bulan_sampai, hari_sampai]
            , format: 'yyyy-mm-dd'
        });
    });

</script>
