@extends('layouts.midone')
@section('titlepage','Data Barang Pembelian')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Barang Pembelian</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/barangpembelian">Barang Pembelian</a>
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
        <div class="col-md-12 col-sm-12">
            <div class="card">
                @if (in_array($level,$barangpembelian_tambah))
                <div class="card-header">
                    <a href="#" id="tambahbarang" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                @endif
                <div class="card-body">
                    <form action="/barangpembelian">
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Nama Barang" field="nama_barang" icon="feather icon-box" value="{{ Request('nama_barang') }}" />
                            </div>
                            @if (Auth::user()->level != "admin gudang logistik" AND Auth::user()->level != "general affair" )
                            <div class="col-lg-2 col-sm-12">
                                <select class="form-control" id="jenis_barang" name="jenis_barang">
                                    <option value="">Pilih Jenis Barang</option>
                                    <option {{ Request('jenis_barang') == 'BAHAN BAKU' ? 'selected' : '' }} value="BAHAN BAKU">BAHAN BAKU</option>
                                    {{-- <option {{ Request('jenis_barang') == 'BAHAN PEMBANTU' ? 'selected' : '' }} value="BAHAN PEMBANTU">BAHAN PEMBANTU</option> --}}
                                    <option {{ Request('jenis_barang') == 'KEMASAN' ? 'selected' : '' }} value="KEMASAN">KEMASAN</option>
                                    <option {{ Request('jenis_barang') == 'BAHAN TAMBAHAN' ? 'selected' : '' }} value="Bahan Tambahan">BAHAN TAMBAHAN</option>
                                    <option {{ Request('jenis_barang') == 'LAINNYA' ? 'selected' : '' }} value="LAINNYA">LAINNYA</option>
                                </select>
                            </div>
                            @endif

                            <div class="col-lg-2 col-sm-12">
                                <select class="form-control" id="kode_kategori" name="kode_kategori">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($kategori_barang_pembelian as $d)
                                    <option {{ Request('kode_kategori') == $d->kode_kategori ? 'selected' :'' }} value="{{ $d->kode_kategori }}">{{ strtoupper($d->kategori)  }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (Auth::user()->level != "admin gudang logistik" AND Auth::user()->level != "admin gudang bahan" AND Auth::user()->level != "general affair")
                            <div class="col-lg-2 col-sm-12">
                                <select class="form-control" id="kode_dept" name="kode_dept">
                                    <option value="">Pilih Departemen</option>
                                    @foreach ($departemen as $d)
                                    <option {{ Request('kode_dept') == $d->kode_dept ? 'selected' :'' }} value="{{ $d->kode_dept }}">{{ strtoupper($d->nama_dept) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-lg-3 col-sm-12">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search mr-1"></i> Cari</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Jenis Barang</th>
                                    <th>Kategori</th>
                                    <th>Departemen</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang_pembelian as $d)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $barang_pembelian->firstItem() - 1 }}</td>
                                    <td>{{ $d->kode_barang }}</td>
                                    <td>{{ $d->nama_barang }}</td>
                                    <td>{{ strtoupper($d->satuan) }}</td>
                                    <td>{{ $d->jenis_barang }}</td>
                                    <td>{{ $d->kategori }}</td>
                                    <td>{{ strtoupper($d->nama_dept) }}</td>
                                    <td>
                                        @if ($d->status=="Aktif")
                                        <span class="badge bg-success">{{ $d->status }}</span>
                                        @else
                                        <span class="badge bg-danger">{{ $d->status }}</span>
                                        @endif

                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            @if (in_array($level,$barangpembelian_edit))
                                            <a class="ml-1 edit" kodebarang="{{ $d->kode_barang }}" href="#"><i class="feather icon-edit success"></i></a>
                                            @endif
                                            @if (in_array($level,$barangpembelian_hapus))
                                            <form method="POST" class="deleteform" action="/barangpembelian/{{Crypt::encrypt($d->kode_barang)}}/delete">
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
                        {{ $barang_pembelian->links('vendor.pagination.vuexy') }}
                    </div>

                    <!-- DataTable ends -->
                </div>
            </div>
        </div>
        <!-- Data list view end -->
    </div>
</div>
<!-- Input Barang -->
<div class="modal fade text-left" id="mdlinputbarang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Barang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputbarang"></div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Barang -->
<div class="modal fade text-left" id="mdleditbarang" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Barang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditbarang"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {

        $('#tambahbarang').click(function(e) {
            e.preventDefault();
            $('#mdlinputbarang').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadinputbarang").load('/barangpembelian/create');
        });

        $('.edit').click(function(e) {
            var kode_barang = $(this).attr("kodebarang");
            e.preventDefault();
            $('#mdleditbarang').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadeditbarang").load('/barangpembelian/' + kode_barang + '/edit');
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
