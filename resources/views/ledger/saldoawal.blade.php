@extends('layouts.midone')
@section('titlepage','Saldo Awal Ledger')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Saldoawal Ledger</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/ledger/saldoawal">Saldo Awal Ledger</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <a href="#" id="inputsaldoawal" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Input Saldo Awal</a>
                </div>
                <div class="card-body">
                    <form action="/saldoawalledger" id="frmcari">
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group">
                                    <select name="bank" id="bank" class="form-control">
                                        <option value="">Semua Bank</option>
                                        @foreach ($bank as $d)
                                        <option {{ Request('bank')==$d->kode_bank ? 'selected' :'' }} value="{{ $d->kode_bank }}">{{ $d->nama_bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <div class="form-group">
                                    <select class="form-control" id="bulan" name="bulan">
                                        <option value="">Bulan</option>
                                        <?php
                                        $bulanini = date("m");
                                        for ($i = 1; $i < count($bulan); $i++) {
                                        ?>
                                        <option {{ Request('bulan') == $i ? 'selected' : '' }} value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <select class="form-control" id="tahun" name="tahun">
                                        <?php
                                        $tahunmulai = 2020;
                                        $hariini = date("m-d");
                                        for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                        ?>
                                        <option {{ Request('tahun') == $thn ? 'selected' : '' }} value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                        <?php
                                        }
                                        if($hariini == "12-31"){
                                            $t = date('Y') + 1;
                                        ?>
                                        <option value="{{ $t }}">{{ $t }}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i></button>
                            </div>
                        </div>

                    </form>
                    @include('layouts.notification')
                    <table class="table table-hover-animation">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>Bank</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($saldoawal as $d)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $bulan[$d->bulan] }}</td>
                                <td>{{ $d->tahun }}</td>
                                <td>{{ $d->nama_bank }}</td>
                                <td class="text-right">{{ desimal($d->jumlah) }}</td>
                                <td>
                                    <form method="POST" class="deleteform" action="/saldoawalledger/{{Crypt::encrypt($d->kode_saldoawalledger)}}/delete">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" class="delete-confirm ml-1">
                                            <i class="feather icon-trash danger"></i>
                                        </a>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Input Saldo Awal -->
<div class="modal fade text-left" id="mdlinputsaldoawal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Saldo Awal Ledger</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputsaldoawal"></div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('myscript')
<script>
    $(function() {

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

        $("#inputsaldoawal").click(function(e) {
            e.preventDefault();
            $("#loadinputsaldoawal").load('/saldoawalledger/create');
            $('#mdlinputsaldoawal').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });


    });

</script>
@endpush
