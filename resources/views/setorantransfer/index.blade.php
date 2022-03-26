@extends('layouts.midone')
@section('titlepage','Setoran Transfer')
@section('content')

<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Setoran Transfer</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/setorantransfer">Setoran Transfer</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="/setorantransfer">
                        <div class="row">
                            <div class="col-lg-6">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-6">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{ Request('nama_pelanggan') }}" />
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group">
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Status Transfer</option>
                                        <option {{ (Request('status')=='0' ? 'selected':'')}} value="0">Pending</option>
                                        <option {{ (Request('status')=='1' ? 'selected':'')}} value="1">Diterima</option>
                                        <option {{ (Request('status')=='2' ? 'selected':'')}} value="2">Ditolak</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Cari Data </button>
                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')
                    <table class="table table-hover-animation">
                        <thead class="thead-dark">
                            <tr>

                                <th>Pelanggan</th>
                                <th>Bank</th>
                                <th>Jumlah</th>
                                <th>Jatuh Tempo</th>
                                <th>Cabang</th>
                                <th>Status</th>
                                <th>Tgl Disetorkan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transfer as $d)
                            <tr>

                                <td>{{ ucwords(strtolower($d->nama_pelanggan)) }}</td>
                                <td>{{ strtoupper($d->namabank) }}</td>
                                <td class="text-right" style="font-weight: bold">{{ rupiah($d->jumlah) }}</td>
                                <td>{{ date("d-m-Y",strtotime($d->tglcair)) }}</td>
                                <td>{{ $d->kode_cabang }}</td>
                                <td>
                                    @if ($d->status==0)
                                    <span class="badge bg-warning"><i class="fa fa-history"></i> Pending</span>
                                    @elseif($d->status==1)
                                    <span class="badge bg-success">{{ date("d-m-Y",strtotime($d->tglbayar)) }}</span>
                                    @elseif($d->status==2)
                                    <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    @if (empty($d->tgl_setoranpusat))
                                    <span class="badge bg-danger"><i class="fa fa-history"></i> Belum Disetorkan</span>
                                    @else
                                    <span class="badge bg-success"><i class="fa fa-check"></i>{{ date("d-m-Y",strtotime($d->tgl_setoranpusat)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        @if (empty($d->tgl_setoranpusat))
                                        <a class="setorkan" href="#" kode_transfer="{{ $d->kode_transfer }}"><i class=" feather icon-external-link success"></i></a>
                                        @else
                                        <a href="/setorantransfer/{{ Crypt::encrypt($d->kode_transfer) }}/delete" class="danger"><i class="fa fa-close"></i></a>
                                        @endif
                                        <a class="ml-1 detailfaktur" href="#" kode_transfer="{{ $d->kode_transfer }}"><i class=" feather icon-file-text info"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- {{ $giro->links('vendor.pagination.vuexy') }} --}}

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Detail Transfer -->
<div class="modal fade text-left" id="mdldetailfaktur" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Detail Transfer <span id="kodetransfer"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loaddetailfaktur"></div>
            </div>
        </div>
    </div>
</div>

<!-- Setor Transfer -->
<div class="modal fade text-left" id="mdlsetorantransfer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Setorkan Transfer <span id="kodetransfer_setoran"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadsetorantransfer"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {

        function loaddetailfaktur(kode_transfer) {
            $.ajax({
                type: 'POST'
                , url: '/transfer/detailfaktur'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_transfer: kode_transfer
                }
                , cache: false
                , success: function(respond) {
                    $("#kodetransfer").text(kode_transfer);
                    $("#loaddetailfaktur").html(respond);
                }
            });
        }

        function loadsetorantransfer(kode_transfer) {
            $.ajax({
                type: 'POST'
                , url: '/setorantransfer/create'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_transfer: kode_transfer
                }
                , cache: false
                , success: function(respond) {
                    $("#kodetransfer_setoran").text(kode_transfer);
                    $("#loadsetorantransfer").html(respond);
                }
            });
        }


        $('.detailfaktur').click(function(e) {
            var kode_transfer = $(this).attr("kode_transfer");
            e.preventDefault();
            loaddetailfaktur(kode_transfer);
            $('#mdldetailfaktur').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });


        $('.setorkan').click(function(e) {
            var kode_transfer = $(this).attr("kode_transfer");
            e.preventDefault();
            loadsetorantransfer(kode_transfer);
            $('#mdlsetorantransfer').modal({
                backdrop: 'static'
                , keyboard: false
            });
        });
    });

</script>
@endpush
