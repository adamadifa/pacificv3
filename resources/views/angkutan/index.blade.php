@extends('layouts.midone')
@section('titlepage','Data Angkutan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Angkutan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/angkutan">Data Angkutan</a>
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
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="/angkutan">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <x-inputtext label="Dari" field="dari" value="{{Request('dari')}}" icon="feather icon-calendar" datepicker />
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <x-inputtext label="Sampai" field="sampai" value="{{Request('sampai')}}" icon="feather icon-calendar" datepicker />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <x-inputtext label="No. Surat Jalan" field="no_surat_jalan" icon="fa fa-barcode" value="{{Request('no_surat_jalan')}}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Status</option>
                                        <option value="1" {{ Request('status')==1 ? 'selected' : '' }}>Sudah Kontrabon</option>
                                        <option value="2" {{ Request('status')==2 ? 'selected' : '' }}>Belum Kontrabon</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fa fa-search mr-1"></i> Cari</button>
                                </div>
                            </div>
                        </div>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No. SJ</th>
                                    <th>Tanggal</th>
                                    <th>Tujuan</th>
                                    <th>Angkutan</th>
                                    <th>No. Polisi</th>
                                    <th>Tarif</th>
                                    <th>Tepung</th>
                                    <th>BS</th>
                                    <th>Tgl Kontrabon</th>
                                    <th>Tgl Bayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($angkutan as $d)
                                <tr>
                                    <td>{{$d->no_surat_jalan}}</td>
                                    <td>{{date("d-m-Y",strtotime($d->tgl_input))}}</td>
                                    <td>{{$d->tujuan}}</td>
                                    <td>{{$d->angkutan}}</td>
                                    <td>{{$d->nopol}}</td>
                                    <td class="text-right">{{!empty($d->tarif) ? rupiah($d->tarif) : ''}}</td>
                                    <td class="text-right">{{!empty($d->tepung) ? rupiah($d->tepung) : ''}}</td>
                                    <td class="text-right">{{!empty($d->bs) ? rupiah($d->bs) : ''}}</td>
                                    <td>
                                        @if (empty($d->tgl_kontrabon))
                                        <i class="fa fa-history warning"></i>
                                        @else
                                        <span class="badge bg-success">{{date("d-m-Y",strtotime($d->tgl_kontrabon))}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (empty($d->tgl_bayar))
                                        <i class="fa fa-history warning"></i>
                                        @else
                                        <span class="badge bg-success">{{date("d-m-Y",strtotime($d->tgl_bayar))}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            @if (empty($d->tgl_kontrabon))
                                            <a class="ml-1 edit" href="#" no_surat_jalan="{{Crypt::encrypt($d->no_surat_jalan)}}"><i class="feather icon-edit success"></i></a>
                                            <form method="POST" class="deleteform" action="/angkutan/{{Crypt::encrypt($d->no_surat_jalan)}}/delete">
                                                @csrf
                                                @method('DELETE')
                                                <a href="#" tanggal="{{ date(" Y-m-d",strtotime($d->tgl_input)) }}" class="delete-confirm ml-1">
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
                        {{ $angkutan->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Edit Angkutan -->
<div class="modal fade text-left" id="mdledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Angkutan</h4>
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
        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "gudangpusat"
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
            // alert('test');
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

        $(".edit").click(function(e) {
            e.preventDefault();
            var no_surat_jalan = $(this).attr("no_surat_jalan");
            $("#loadedit").load("/angkutan/" + no_surat_jalan + "/edit");
            $('#mdledit').modal({
                backdrop: 'static'
                , keyboard: false
            });

        });

    });

</script>
@endpush
