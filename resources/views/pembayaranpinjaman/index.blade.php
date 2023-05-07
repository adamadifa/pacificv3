@extends('layouts.midone')
@section('titlepage','Pembayaran Pinjaman')
@section('content')
<style>
    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

    .col-4,
    .col-5,
    .col-6,
    .col-3 {
        padding-right: 1px !important;
    }

</style>
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Pembayaran Pinjaman</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/pembayaranpinjaman">Pembayaran Pinjaman</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <div class="col-md-12 col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <a href="#" id="inputbayar" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Input Pembayaran</a>
                </div>
                <div class="card-body">
                    <form action="/pembayaranpinjaman">
                        <div class="row">
                            <div class="col-4">
                                {{-- <label for="" class="form-label mb-1">Omset Bulan</label> --}}
                                <div class="form-group">
                                    <select class="form-control" id="bulan" name="bulan">
                                        <option value="">Bulan</option>
                                        <?php
                                        $bulanini = date("m");
                                        for ($i = 1; $i < count($bulan); $i++) {
                                        ?>
                                        <option {{ Request('bulan') == $i ? "selected" : "" }} value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <select class="form-control" id="tahun" name="tahun">
                                        <option value="">Tahun</option>
                                        <?php
                                        $tahunmulai = 2020;
                                        for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                        ?>
                                        <option {{ Request('tahun') == $thn ? "selected" : "" }} value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i>Cari</button>
                                </div>
                            </div>
                        </div>


                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Kode.</th>
                                    <th>Bulan</th>
                                    <th>Tahun</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pinjamanpotongangaji as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->kode_potongan }}</td>
                                    <td>{{ $bulan[$d->bulan] }}</td>
                                    <td>{{ $d->tahun }}</td>
                                    <td style="text-align: right">{{ rupiah($d->totalpembayaran) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="#" class="detail info" kode_potongan="{{ $d->kode_potongan }}"><i class="feather icon-file-text"></i></a>
                                            @if ($loop->last)
                                            <form method="POST" class="deleteform" action="/pembayaranpinjaman/{{Crypt::encrypt($d->kode_potongan)}}/deletegenerate">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>
                                            @endif

                                        </div>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- {{ $saldoawal->links('vendor.pagination.vuexy') }} --}}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>

<div class="modal fade text-left" id="mdlinputbayar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Pembayaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/pembayaranpinjaman/generatebayarpinjaman" method="POST" id="frmGenerate">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <select class="form-control" id="bulantagihan" name="bulantagihan">
                                    <option value="">Bulan</option>
                                    <?php
                                    $bulanini = date("m");
                                    for ($i = 1; $i < count($bulan); $i++) {
                                    ?>
                                    <option value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <select class="form-control" id="tahuntagihan" name="tahuntagihan">
                                    <option value="">Tahun</option>
                                    <?php
                                    $tahunmulai = 2020;
                                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                    ?>
                                    <option {{ Request('tahun') == $thn ? "selected" : "" }} value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <button class="btn btn-primary w-100"><i class="feather icon-settings mr-1"></i>Generate</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" style="z-index: 1052 !important" id="mdldetailpembayaran" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Pembayaran Pinjaman</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailpembayaran"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        $("#inputbayar").click(function(e) {
            e.preventDefault();
            $('#mdlinputbayar').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $(".detail").click(function(e) {
            e.preventDefault();
            var kode_potongan = $(this).attr("kode_potongan");
            $.ajax({
                type: 'POST'
                , url: '/pembayaranpinjaman/show'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_potongan: kode_potongan
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailpembayaran").html(respond);
                }
            });
            $('#mdldetailpembayaran').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });

        $("#frmGenerate").submit(function(e) {
            var bulantagihan = $("#bulantagihan").val();
            var tahuntagihan = $("#tahuntagihan").val();
            if (bulantagihan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#bulantagihan").focus();
                });

                return false;
            } else if (tahuntagihan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tahuntagihan").focus();
                });

                return false;
            }
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
