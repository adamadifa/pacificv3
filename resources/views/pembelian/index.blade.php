@extends('layouts.midone')
@section('titlepage', 'Data Pembelian')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Data Pembelian</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/pembelian">Data Pembelian</a>
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
                        @if (in_array($level, $pembelian_tambah))
                            <a href="/pembelian/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Tambah
                                Data</a>
                        @endif
                    </div>
                    <div class="card-body">
                        <form action="/pembelian">
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext field="nobukti_pembelian" value="{{ Request('nobukti_pembelian') }}"
                                        label="No. Bukti Pembelian" icon="feather icon-credit-card" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <x-inputtext field="dari" value="{{ Request('dari') }}" label="Dari"
                                        icon="feather icon-calendar" datepicker />
                                </div>
                                <div class="col-6">
                                    <x-inputtext field="sampai" value="{{ Request('sampai') }}" label="Sampai"
                                        icon="feather icon-calendar" datepicker />
                                </div>
                            </div>
                            <div class="row">
                                @if (!in_array($level, $levelgudang))

                                    <div class="col-lg-2 col-sm-12">
                                        <div class="form-group">
                                            <select name="kode_dept" id="kode_dept" class="form-control">
                                                <option value="">Semua Departemen</option>
                                                @foreach ($departemen as $d)
                                                    <option {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }}
                                                        value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-lg-3 col-sm-12">
                                    <div class="form-group">
                                        <select name="kode_supplier" id="kode_supplier" class="form-control select2">
                                            <option value="">Semua Supplier</option>
                                            @foreach ($supplier as $d)
                                                <option
                                                    {{ Request('kode_supplier') == $d->kode_supplier ? 'selected' : '' }}
                                                    value="{{ $d->kode_supplier }}">{{ $d->nama_supplier }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-12">
                                    <div class="form-group">
                                        <select name="ppn" id="ppn" class="form-control">
                                            <option {{ Request('ppn') == '-' ? 'selected' : '' }} value="-">PPN / Non
                                                PPN
                                            </option>
                                            <option {{ Request('ppn') == 1 ? 'selected' : '' }} value="1">PPN</option>
                                            <option {{ Request('ppn') == 0 ? 'selected' : '' }} value="0">Non PPN
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-12">
                                    <div class="form-group">
                                        <select name="jenistransaksi" id="jenistransaksi" class="form-control">
                                            <option value="">Tunai / Kredit</option>
                                            <option {{ Request('jenistransaksi') == 'tunai' ? 'selected' : '' }}
                                                value="tunai">Tunai</option>
                                            <option {{ Request('jenistransaksi') == 'kredit' ? 'selected' : '' }}
                                                value="kredit">Kredit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <button class="btn btn-primary"><i class="fa fa-search mr-1"></i> Cari</button>
                                    </div>
                                </div>
                            </div>

                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover-animation">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>No. Bukti</th>
                                        <th>Tanggal</th>
                                        <th>Supplier</th>
                                        <th>DEPT</th>
                                        @if (Auth::user()->level != 'admin gudang logistik')
                                            <th>PPN</th>
                                            <th>Subtotal</th>
                                            <th>Peny. JK</th>
                                            <th>Total</th>
                                            <th>Bayar</th>
                                            <th>KB</th>
                                            <th>Ket</th>
                                            {{-- <th>Fak. Pajak</th> --}}
                                            <th>T/K</th>
                                            <th>K</th>
                                        @endif
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pembelian as $d)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + $pembelian->firstItem() - 1 }}
                                            </td>
                                            <td>{{ strtoupper($d->nobukti_pembelian) }}</td>
                                            <td>{{ date('d-m-y', strtotime($d->tgl_pembelian)) }}</td>
                                            <td>{{ ucwords(strtolower($d->nama_supplier)) }}</td>
                                            <td>{{ $d->kode_dept }}</td>
                                            @if (Auth::user()->level != 'admin gudang logistik')
                                                <td>
                                                    @if (!empty($d->ppn))
                                                        <i class="fa fa-check success"></i>
                                                    @endif
                                                </td>
                                                <td class="text-right">{{ desimal($d->harga) }}</td>
                                                <td class="text-right">{{ desimal($d->penyesuaian) }}</td>
                                                <td class="text-right">{{ desimal($d->harga + $d->penyesuaian) }}</td>
                                                <td class="text-right">{{ desimal($d->jmlbayar) }}</td>
                                                <td>
                                                    @if (!empty($d->kontrabon))
                                                        <span class="badge bg-success">{{ $d->kontrabon }}</span>
                                                    @else
                                                        <i class="fa fa-history warning"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $totalharga = $d->harga + $d->penyesuaian;
                                                        if ($totalharga == $d->jmlbayar) {
                                                            echo "<span class='badge bg-success'>L</span>";
                                                        } else {
                                                            echo "<span class='badge bg-danger'>BL</span>";
                                                        }
                                                    @endphp

                                                </td>
                                                {{-- <td>
                                        @if (!empty($d->ppn) && empty($d->no_fak_pajak))
                                        <a href="#" nobukti="{{ $d->nobukti_pembelian }}" nopajak="{{ $d->no_fak_pajak }}" class="inputnopajak warning"><i class="feather icon-edit-2"></i></a>
                                    @elseif(!empty($d->ppn) && !empty($d->no_fak_pajak))
                                    <a href="#" nobukti="{{ $d->nobukti_pembelian }}" nopajak="{{ $d->no_fak_pajak }}" class="inputnopajak info">{{ $d->no_fak_pajak }}</a>
                                    @endif
                                    </td> --}}
                                                <td>
                                                    @if ($d->jenistransaksi == 'tunai')
                                                        <span class="badge bg-success">T</span>
                                                    @else
                                                        <span class="badge bg-warning">K</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($d->kategori_transaksi == 'MP')
                                                        <span class="badge bg-success">MP</span>
                                                    @elseif($d->kategori_transaksi == 'IP')
                                                        <span class="badge bg-warning">IP</span>
                                                    @elseif($d->kategori_transaksi == 'P')
                                                        <span class="badge bg-primary">P</span>
                                                    @elseif($d->kategori_transaksi == 'PCF')
                                                        <span class="badge bg-info">P</span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <a class="ml-1 detailpembelian" href="#"
                                                        nobukti_pembelian="{{ $d->nobukti_pembelian }}"><i
                                                            class=" feather icon-file-text info"></i></a>
                                                    @if (in_array($level, $pembelian_edit))
                                                        <a class="ml-1"
                                                            href="/pembelian/{{ \Crypt::encrypt($d->nobukti_pembelian) }}/edit"><i
                                                                class="feather icon-edit success"></i></a>
                                                    @endif
                                                    @if (empty($d->jmlbayar))
                                                        @if (in_array($level, $pembelian_hapus))
                                                            <form method="POST" class="deleteform"
                                                                action="/pembelian/{{ Crypt::encrypt($d->nobukti_pembelian) }}/delete">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="#" tanggal="{{ $d->tgl_pembelian }}"
                                                                    class="delete-confirm ml-1">
                                                                    <i class="feather icon-trash danger"></i>
                                                                </a>
                                                            </form>
                                                        @endif
                                                    @endif


                                                    @if (in_array($level, $approve_pembelian))
                                                        @if ($d->kode_dept == 'GDL')
                                                            @if (!empty($d->nobukti_pemasukan))
                                                                <form method="POST" class="deleteform"
                                                                    action="/pemasukangudanglogistik/{{ Crypt::encrypt($d->nobukti_pemasukan) }}/delete">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <a href="#" tanggal="{{ $d->tgl_pemasukan }}"
                                                                        class="delete-confirm-pemasukan ml-1">
                                                                        <i class="fa fa-close danger"></i>
                                                                    </a>
                                                                </form>
                                                            @else
                                                                <a href="#"
                                                                    nobukti_pembelian="{{ $d->nobukti_pembelian }}"
                                                                    class="ml-1 prosespembelian"><i
                                                                        class="feather icon-external-link success"></i></a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $pembelian->links('vendor.pagination.vuexy') }}
                        </div>

                        <!-- DataTable ends -->
                    </div>
                </div>
            </div>
            <!-- Data list view end -->
        </div>
    </div>
    <!-- Detail Salesman -->
    <div class="modal fade text-left" id="mdldetailpembelian" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel18" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width:968px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel18">Detail Pembelian</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loaddetailpembelian"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            function loaddetailpembelian(nobukti_pembelian) {
                $.ajax({
                    type: 'POST',
                    url: '/pembelian/show',
                    data: {
                        _token: "{{ csrf_token() }}",
                        nobukti_pembelian: nobukti_pembelian
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loaddetailpembelian").html(respond);
                    }
                });
            }

            function loadprosespembelian(nobukti_pembelian) {
                $.ajax({
                    type: 'POST',
                    url: '/pembelian/prosespembelian',
                    data: {
                        _token: "{{ csrf_token() }}",
                        nobukti_pembelian: nobukti_pembelian
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loaddetailpembelian").html(respond);
                    }
                });
            }
            $('.detailpembelian').click(function(e) {
                var nobukti_pembelian = $(this).attr("nobukti_pembelian");
                //alert(nobukti_pembelian);
                e.preventDefault();
                loaddetailpembelian(nobukti_pembelian);
                $('#mdldetailpembelian').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });

            $('.prosespembelian').click(function(e) {
                var nobukti_pembelian = $(this).attr("nobukti_pembelian");
                //alert(nobukti_pembelian);
                e.preventDefault();
                loadprosespembelian(nobukti_pembelian);
                $('#mdldetailpembelian').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });

            function cektutuplaporan(tanggal) {
                $.ajax({
                    type: "POST",
                    url: "/cektutuplaporan",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tanggal: tanggal,
                        jenislaporan: "pembelian"
                    },
                    cache: false,
                    success: function(respond) {
                        console.log(respond);
                        $("#cektutuplaporan").val(respond);
                    }
                });
            }

            function cektutuplaporanpemasukan(tanggal) {
                $.ajax({
                    type: "POST",
                    url: "/cektutuplaporan",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tanggal: tanggal,
                        jenislaporan: "gudanglogistik"
                    },
                    cache: false,
                    success: function(respond) {
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
                        title: `Are you sure you want to delete this record?`,
                        text: "If you delete this, it will be gone forever.",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
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

            $('.delete-confirm-pemasukan').click(function(event) {
                var form = $(this).closest("form");
                var name = $(this).data("name");
                var tanggal = $(this).attr("tanggal");
                cektutuplaporanpemasukan(tanggal);
                event.preventDefault();
                swal({
                        title: `Are you sure you want to delete this record?`,
                        text: "If you delete this, it will be gone forever.",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
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
