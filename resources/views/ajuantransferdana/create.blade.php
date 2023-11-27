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

<form action="/ajuantransferdana/store" method="POST" id="frmAjuantransferdana">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Pengajuan" field="tgl_pengajuan" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama" field="nama" icon="feather icon-user" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama Bank" field="nama_bank" icon="feather icon-bank" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. Rekening" field="no_rekening" icon="feather icon-credit-card" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Jumlah" field="no_rekening" icon="feather icon-file" />
        </div>
    </div>
</form>
