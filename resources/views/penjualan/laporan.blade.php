@extends('layouts.midone')
@section('titlepage','Laporan Penjualan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Laporan Penjualan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/penjualan/laporan">Laporan Penjualan</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <div class="row">
            <div class="col-lg-9 col-sm-12">
                <div class="row">
                    <div class="col-lg-7 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="">
                                    {{-- <div class="row" id="pilihlaporan">
                                            <div class="col-lg-12 col-sm-12">
                                                <div class="form-group">
                                                    <select name="namalaporan" id="namalaporan" class="form-control">
                                                        <option value="">Pilih Laporan</option>
                                                        <option value="penjualan">Penjualan</option>
                                                        <option value="retur">Retur</option>
                                                        <option value="kasbesar">Kas Besar</option>
                                                        <option value="tunaikredit">Tunai Kredit</option>
                                                        <option value="aup">Analisa Umur Piutang (AUP)</option>
                                                        <option value="kartupiutang">Kartu Piutang</option>
                                                        <option value="lebihsatufaktur">Lebih 1 Faktur</option>
                                                        <option value="dppp">DPPP</option>
                                                        <option value="rekapomsetpelanggan">Rekap Omset Pelanggan</option>
                                                        <option value="rekappelanggan">Rekap Pelanggan</option>
                                                        <option value="rekappenjualan">Rekap Penjualan</option>
                                                        <option value="rekapkendaraan">Rekap Kendaraan</option>
                                                        <option value="harganet">Harga Net</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div> --}}
                                    <div class="row" id="pilihcabang">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="form-group  ">
                                                <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                    <option value="">Semua Cabang</option>
                                                    @foreach ($cabang as $c)
                                                    <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="pilihsalesman">
                                        <div class="col-12">
                                            <div class="form-group  ">
                                                <select name="id_karyawan" id="id_karyawan" class="form-control">
                                                    <option value="">Semua Salesman</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <!-- DataTable ends -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-12">
                <div class="card" style="height: 400.453px;">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">Laporan</h4>
                        </div>
                        <ul class="list-group list-group-flush">
                            <a href="">
                                <li class="list-group-item">
                                    <i class="feather icon-file mr-1"></i>Penjualan
                                </li>
                            </a>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>Retur
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>Kas Besar
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>Tunai Kredit
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>Analisa Umur Piutang (AUP)
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>Kartu Piutang
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>Lebih 1 Faktur
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>DPPP
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>DPP
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>REPO
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>Rekap Omset Pelanggan
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>Rekap Pelanggan
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>Rekap Penjualan
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>Rekap Kendaraan
                            </li>
                            <li class="list-group-item">
                                <i class="feather icon-file mr-1"></i>Harga Net
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-sm-12">


            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function loadsalesmancabang() {
            var kode_cabang = $("#kode_cabang").val();
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#id_karyawan").html(respond);
                }
            });
        }

        $("#kode_cabang").change(function() {
            loadsalesmancabang();
        });
    });

</script>
@endpush
