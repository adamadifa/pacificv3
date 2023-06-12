<form action="/konfigurasijadwal/{{ Crypt::encrypt($konfigurasijadwal->kode_setjadwal)}}/update" method="POST" id="frmSetjadwal">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Auto" value="{{ $konfigurasijadwal->kode_setjadwal }}" field="kode_setjadwal" icon="feather icon-credit-card" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <x-inputtext label="Dari" value="{{ $konfigurasijadwal->dari }}" field="dari" icon="feather icon-calendar" datepicker />
        </div>
        <div class="col-6">
            <x-inputtext label="Sampai" value="{{ $konfigurasijadwal->sampai }}" field="sampai" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary btn-block" type="submit" name="submit"><i class="feather icon-send mr-1"></i>Update</button>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
