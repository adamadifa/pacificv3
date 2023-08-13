@extends('layouts.sap.sap')
@section('content')
<style>
    #border1 {
        border: 1px solid #b11036;
    }

</style>
<style>
    .float {
        position: fixed;
        bottom: 70px;
        right: 20px;
        text-align: center;
        z-index: 9000;
    }

    .btn-rounded {
        border-radius: 100px;
        padding: 15px;
    }

</style>
<form action="/sap/pelanggan" />
<div class="row mb-2">
    <div class="col-12">
        <div class="inputWithIcon">
            <i class="bi bi-calendar"></i>
            <input type="text" id="tanggal" required="required" autocomplete="off" onfocus="blur()" />
            <label>Tanggal</label>
        </div>
    </div>
</div>
<div class="row mt-2">
    <div class="col-12">
        <div class="group">
            <select name="kode_cabang" class="select_join" id="kode_cabang">
                <option value="">Pilih Cabang</option>
                @foreach ($cabang as $d)
                <option {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        <button class="btn w-100" type="submit" name="submit" style="background-color:#b11036; color:white">Cari Data</button>
    </div>
</div>
</form>
<a href="/sap/smactivity/create" class="float btn btn-rounded" style="background-color:#b11036; color:white;"><i class='bx bx-plus' style="font-size:1.5rem"></i></a>
<div class="row mt-2">
    <div class="col-12">
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        $("#tanggal").daterangepicker({
            opens: 'left'
            , isMobile: true
            , autoApply: true
            , locale: {
                format: 'DD/MM/YYYY'
            }
        });
    });

</script>
@endpush
