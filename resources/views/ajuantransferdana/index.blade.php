@extends('layouts.midone')
@section('titlepage','Data Ajuan Transfer Dana')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Ajuan Transfer Dana</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/ajuantransferdana">Data Ajuan Transfer Dana</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <a href="#" class="btn btn-primary" id="tambahdata"><i class="fa fa-plus mr-1"></i> Tambah
                        Data</a>
                </div>
                <div class="card-body">
                    <form action="/ajuanfaktur">
                        <div class="row">
                            <div class="col-lg-6">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker
                                    value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-6">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker
                                    value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            @if (Auth::user()->kode_cabang=="PCF")
                            <div class="col-lg-2 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{
                                            $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user"
                                    value="{{ Request('nama_pelanggan') }}" />
                            </div>


                            <div class="col-lg-2 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary w-100"><i
                                        class="fa fa-search"></i> Cari Data </button>
                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')

                    <div class="table-responsive mt-2">
                        <table class="table table-hover-animation ">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.Pengajuan</th>
                                    <th style="width: 10%">Tanggal</th>
                                    <th>Nama</th>
                                    <th>Bank</th>
                                    <th>No. Rekening</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    {{-- {{ $ajuanrouting->links('vendor.pagination.vuexy') }} --}}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="mdlajuanrouting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Ajukan Routing
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadformajuanrouting">
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {

        $(".editajuan").click(function(e) {
            e.preventDefault();
            var no_pengajuan = $(this).attr("no_pengajuan");
            $("#mdlajuanrouting").modal("show");
            // /alert(kode_pelanggan);
            $("#loadformajuanrouting").load('/ajuanrouting/' + no_pengajuan + '/edit');
        });
        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `Are you sure you want to delete this record?`
                    , text: "If you delete this, it will be gone forever."
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    });

</script>
@endpush