@extends('layouts.midone')
@section('titlepage','Jurnal Umum')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Jurnal Umum</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/jurnalumum">Jurnal Umum</a>
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
                    <a href="#" id="inputjurnalumum" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/jurnalumum">
                        <div class="row">
                            <div class="col-6">
                                <x-inputtext field="dari" value="{{ Request('dari') }}" label="Dari" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-6">
                                <x-inputtext field="sampai" value="{{ Request('sampai') }}" label="Sampai" icon="feather icon-calendar" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Cabang</option>
                                        @foreach ($cabang as $d)
                                        <option {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if ($level=="hrd")
                        <input type="hidden" name="kode_dept" id="kode_dept" value="HRD" />
                        @elseif($level=="general affair")
                        <input type="hidden" name="kode_dept" id="kode_dept" value="GAF" />
                        @else
                        <input type="hidden" name="kode_dept" id="kode_dept" value="ALL">
                        @endif
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block"><i class="fa fa-gear mr-1"></i> Set Periode</button>
                                    </div>
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
                                    <th>Keterangan</th>
                                    <th>Peruntukan</th>
                                    <th>Akun</th>
                                    <th>Debet</th>
                                    <th>Kredit</th>
                                    <th>CR</th>
                                    <th>Dept</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jurnalumum as $d)
                                @php
                                if($d->status_dk=="D"){
                                $debet = $d->jumlah;
                                $kredit = 0;
                                }else{
                                $debet = 0;
                                $kredit = $d->jumlah;
                                }
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ date("d-m-Y",strtotime($d->tanggal)) }}</td>
                                    <td>{{ $d->keterangan }}</td>
                                    <td>{{ $d->peruntukan }} <b>{{ $d->peruntukan=='PC' ? '('.$d->kode_cabang.')' : '' }}</b></td>
                                    <td><b>{{ $d->kode_akun }} </b>{{ $d->nama_akun }}</td>
                                    <td class="text-right">{{ desimal($debet) }}</td>
                                    <td class="text-right">{{ desimal($kredit) }}</td>

                                    <td>{!! !empty($d->kode_cr) ? "<i class='fa fa-check success'></i>" : "" !!}</td>
                                    <td>{{ $d->kode_dept }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="#" class="edit" kode_jurnal="{{ Crypt::encrypt($d->kode_jurnal) }}"><i class="feather icon-edit info"></i></a>
                                            <form method="POST" class="deleteform" action="/jurnalumum/{{Crypt::encrypt($d->kode_jurnal)}}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" tanggal="{{ $d->tanggal }}" class="delete-confirm ml-1">
                                                    <i class="feather icon-trash danger"></i>
                                                </a>
                                            </form>

                                        </div>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                        {{-- {{ $jurnalumum->links('vendor.pagination.vuexy') }} --}}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Input Jurnal Umum -->
<div class="modal fade text-left" id="mdlinputjurnalumum" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 960px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Jurnal Umum</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputjurnalumum"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="mdledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Jurnal Umum</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadedit"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        $("#inputjurnalumum").click(function(e) {
            e.preventDefault();
            $('#mdlinputjurnalumum').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadinputjurnalumum").load("/jurnalumum/create");
        });

        $(".edit").click(function(e) {
            e.preventDefault();
            var kode_jurnal = $(this).attr("kode_jurnal");

            $('#mdledit').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadedit").load("/jurnalumum/" + kode_jurnal + "/edit");
        });


        // function cektutuplaporan(tanggal) {
        //     $.ajax({
        //         type: "POST"
        //         , url: "/cektutuplaporan"
        //         , data: {
        //             _token: "{{ csrf_token() }}"
        //             , tanggal: tanggal
        //             , jenislaporan: "pembelian"
        //         }
        //         , cache: false
        //         , success: function(respond) {
        //             console.log(respond);
        //             $("#cektutuplaporan").val(respond);
        //         }
        //     });
        // }
        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            var tanggal = $(this).attr("tanggal");
            // cektutuplaporan(tanggal);
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
                        // var cektutuplaporan = $("#cektutuplaporan").val();
                        // if (cektutuplaporan > 0) {
                        //     swal("Oops", "Laporan Periode Ini Sudah Di Tutup !", "warning");
                        //     return false;
                        // } else {
                        //     form.submit();
                        // }

                        form.submit();
                    }
                });
        });
    });

</script>
@endpush
