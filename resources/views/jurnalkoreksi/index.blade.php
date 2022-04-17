@extends('layouts.midone')
@section('titlepage','Jurnal Koreksi')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Jurnal Koreksi</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/jurnalkoreksi">Jurnal Koreksi</a>
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
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <a href="#" id="inputjurnalkoreksi" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/jurnalkoreksi">
                        <div class="row">
                            <div class="col-5">
                                <x-inputtext field="dari" value="{{ Request('dari') }}" label="Dari" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-5">
                                <x-inputtext field="sampai" value="{{ Request('sampai') }}" label="Sampai" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <button class="btn btn-primary"><i class="fa fa-gear mr-1"></i> Set Periode</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Tanggal</th>
                                    <th>No. Bukti</th>
                                    <th>Nama Barang</th>
                                    <th>Keterangan</th>
                                    <th>Akun</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Debet</th>
                                    <th>Kredit</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jurnalkoreksi as $d)
                                @php
                                $total = $d->qty * $d->harga;
                                if($d->status_dk=="D"){
                                $debet = $total;
                                $kredit = 0;
                                }else{
                                $debet = 0;
                                $kredit = $total;
                                }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $jurnalkoreksi->firstItem() - 1 }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tgl_jurnalkoreksi)) }}</td>
                                    <td>{{ $d->nobukti_pembelian }}</td>
                                    <td>{{ $d->nama_barang }}</td>
                                    <td>{{ $d->keterangan }}</td>
                                    <td><b>{{ $d->kode_akun }}</b> {{ $d->nama_akun }}</td>
                                    <td class="text-center">{{ desimal($d->qty) }}</td>
                                    <td class="text-right">{{ desimal($d->harga) }}</td>
                                    <td class="text-right"> {{ !empty($debet) ? desimal($debet) : '' }}</td>
                                    <td class="text-right"> {{ !empty($kredit) ? desimal($kredit) : '' }}</td>
                                    <td>
                                        <form method="POST" class="deleteform" action="/jurnalkoreksi/{{Crypt::encrypt($d->kode_jk)}}/delete">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" tanggal="{{ $d->tgl_jurnalkoreksi }}" class="delete-confirm ml-1">
                                                <i class="feather icon-trash danger"></i>
                                            </a>
                                        </form>
                                    </td>


                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                        {{ $jurnalkoreksi->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Input Jurnal Koreksi -->
<div class="modal fade text-left" id="mdlinputjurnalkoreksi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Jurnal Koreksi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputjurnalkoreksi"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        $("#inputjurnalkoreksi").click(function(e) {
            e.preventDefault();
            var dari = "{{ Request('dari') }}";
            var sampai = "{{ Request('sampai') }}";
            if (dari == "" || sampai == "") {
                swal({
                    title: 'Oops'
                    , text: 'Periode harus di Set Terlebih Dahulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#dari").focus();
                });
                return false;
            } else {
                $('#mdlinputjurnalkoreksi').modal({
                    backdrop: 'static'
                    , keyboard: false
                });
                $("#loadinputjurnalkoreksi").load("/jurnalkoreksi/create");
            }
        });


        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "pembelian"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }
        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            var tanggal = $(this).attr("tanggal");
            cektutuplaporan(tanggal);
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
                        var cektutuplaporan = $("#cektutuplaporan").val();
                        if (cektutuplaporan > 0) {
                            swal("Oops", "Laporan Periode Ini Sudah Di Tutup !", "warning");
                            return false;
                        } else {
                            form.submit();
                        }
                    }
                });
        });
    });

</script>
@endpush
