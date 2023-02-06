<style>
    .cardpenjualan {
        width: 100%;
        background-color: #ffffff;
        color: black;
        padding: 8px 10px;
        margin: 5px;
        border-radius: 10px;
        box-shadow: 2px 2px 3px rgba(88, 88, 88, 0.3);
    }

    .totalcashin {
        line-height: normal;
        margin-right: 2px;
    }

    #border1 {
        border: 1px solid #b11036;
    }

</style>
<div class="row mt-3">
    <div class="col-12 text-center">
        <h1 style="color:#b11036; font-size:3rem !important">{{ rupiah($historibayar->totalbayar + $giro->totalgiro + $transfer->totaltransfer) }}</h1>
    </div>
</div>
<div class="detailcashin d-flex justify-content-between">
    <div class="cardpenjualan d-flex justify-content-between">
        <div class="avatar avatar-40 alert-success text-success rounded-circle">
            <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="totalcashin">
            <p style="text-align: left !important; font-weight:600" class="mb-0 text-success">{{ rupiah($historibayar->totalbayartunai) }}</p>
            <p class="small text-muted mb-0" style="text-align: right !important">Tunai</p>
        </div>
    </div>
    <div class="cardpenjualan d-flex justify-content-between">
        <div class="avatar avatar-40 alert-success text-success rounded-circle">
            <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="totalcashin">
            <p style="text-align: left !important; font-weight:600" class="mb-0 text-success">{{ rupiah($historibayar->totalbayartitipan) }}</p>
            <p class="small text-muted mb-0" style="text-align: right !important">Titipan</p>
        </div>
    </div>
</div>
<div class="detailcashin d-flex justify-content-between">
    <div class="cardpenjualan d-flex justify-content-between">
        <div class="avatar avatar-40 alert-success text-success rounded-circle">
            <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="totalcashin">
            <p style="text-align: left !important; font-weight:600" class="mb-0 text-success">{{ rupiah($transfer->totaltransfer) }}</p>
            <p class="small text-muted mb-0" style="text-align: right !important">Transfer</p>
        </div>
    </div>
    <div class="cardpenjualan d-flex justify-content-between">
        <div class="avatar avatar-40 alert-success text-success rounded-circle">
            <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="totalcashin">
            <p style="text-align: left !important; font-weight:600" class="mb-0 text-success">{{ rupiah($giro->totalgiro) }}</p>
            <p class="small text-muted mb-0" style="text-align: right !important">Giro</p>
        </div>
    </div>
</div>
<div class="detailcashin d-flex justify-content-between">
    <div class="cardpenjualan d-flex justify-content-between">
        <div class="avatar avatar-40 alert-danger text-danger rounded-circle">
            <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="totalcashin">
            <p style="text-align: right !important; font-weight:600" class="mb-0 text-danger">{{ rupiah($historibayar->totalvoucher) }}</p>
            <p class="small text-muted mb-0" style="text-align: right !important">Voucher</p>
        </div>
    </div>
</div>
