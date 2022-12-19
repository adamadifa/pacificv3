@extends('layouts.midone')
@section('titlepage','Data Penjualan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data Penjualan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/penjualan">Data Penjualan</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <div class="card">
            <div class="card-body">
                <form action="/penjualan" id="frmPenjualan">
                    <div class="row">
                        <div class="col-lg-6 col-sm-6">
                            <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                        </div>
                        <div class="col-lg-6 col-sm-6">
                            <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-sm-12">
                            <x-inputtext label="No Faktur" field="no_fak_penj" icon="feather icon-credit-card" value="{{ Request('no_fak_penj') }}" />
                        </div>
                        @if (Auth::user()->level!="salesman")
                        <div class="col-lg-2 col-sm-12">
                            <div class="form-group">
                                <select name="id_karyawan" id="id_karyawan" class="form-control select2">
                                    <option value="">Pilih Salesman</option>
                                    @foreach ($salesman as $d)
                                    <option {{ (Request('id_karyawan')==$d->id_karyawan ? 'selected':'')}} value="{{ $d->id_karyawan }}">{{ $d->nama_karyawan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <div class="col-lg-2 col-sm-12">
                            <x-inputtext label="Kode Pelanggan" field="kode_pelanggan" icon="fa fa-barcode" value="{{ Request('kode_pelanggan') }}" />
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="feather icon-user" value="{{ Request('nama_pelanggan') }}" />
                        </div>
                        <div class="col-lg-2 col-sm-10">
                            <div class="form-group">
                                <select name="status" id="status" class="form-control">
                                    <option value="">Status</option>
                                    <option {{ (Request('status')=='1' ? 'selected':'')}} value="1">PENDING</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-1 col-sm-2">
                            <button type="submit" name="submit" value="1" class="btn btn-primary search"><i class="fa fa-search"></i> </button>
                        </div>
                    </div>
                    @if (Auth::user()->level!="salesman")
                    <div class="row mb-1">
                        <div class="col-12">
                            <button class="btn btn-info" type="submit" name="print" id="cetaksuratjalan" value="submit"><i class="feather icon-printer mr-1"></i> Cetak Surat Jalan</button>
                        </div>
                    </div>
                    @endif

                </form>
                @include('layouts.notification')

                <table class="table" @if ($level=="salesman" ) style="font-size:10px !important" @endif>
                    <thead class="thead-dark">
                        <tr @if ($level=="salesman" ) style="font-size:10px !important" @endif>
                            <th>No Faktur</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            @if (Auth::user()->level != "salesman")
                            <th>Salesman</th>
                            <th>Cabang</th>
                            @endif
                            <th>T/K</th>
                            <th>Total</th>
                            @if ($level != "salesman")
                            <th>Status</th>
                            @else
                            <th>Ket</th>
                            @endif
                            @endif
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualan as $d)
                        @if ($d->status==1)
                        @php
                        $color = "#f7d97763";
                        @endphp
                        @else
                        @php
                        $color = "";
                        @endphp
                        @endif
                        <tr style="background-color:{{ $color }}">
                            <td>{{$d->no_fak_penj}}</td>
                            @if ($level != "salesman")
                            <td>{{date("d-m-Y",strtotime($d->tgltransaksi))}}</td>
                            @else
                            <td>{{date("d-m-y",strtotime($d->tgltransaksi))}}</td>
                            @endif
                            <td>{{$d->nama_pelanggan}}</td>
                            @if (Auth::user()->level != "salesman")
                            <td>{{$d->nama_karyawan}}</td>
                            <td>{{$d->kode_cabang}}</td>
                            <td>
                                @if ($d->jenistransaksi=="tunai")
                                <span class="badge bg-success">Tunai</span>
                                @else
                                <span class="badge bg-warning">Kredit</span>
                                @endif
                            </td>
                            @else
                            <td>
                                @if ($d->jenistransaksi=="tunai")
                                <span class="badge bg-success">T</span>
                                @else
                                <span class="badge bg-warning">K</span>
                                @endif
                            </td>
                            @endif


                            <td class="text-right">{{rupiah($d->total)}}</td>
                            @if (Auth::user()->level != "salesman")
                            <td>
                                @if ($d->status_lunas=="1")
                                <span class="badge bg-success">Lunas</span>
                                @else
                                <span class="badge bg-danger">Belum Lunas</span>
                                @endif
                            </td>
                            @else
                            <td>
                                @if ($d->status_lunas=="1")
                                <span class="badge bg-success">L</span>
                                @else
                                <span class="badge bg-danger">BL</span>
                                @endif
                            </td>
                            @endif
                            <td>
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    @if (in_array($level,$penjualan_edit))
                                    <a class="ml-1" href="/penjualan/{{\Crypt::encrypt($d->no_fak_penj)}}/editv2"><i class="feather icon-edit success"></i></a>
                                    @if (Auth::user()->level != "salesman")
                                    <a class="ml-1 detailpenjualan" href="/penjualan/{{ Crypt::encrypt($d->no_fak_penj) }}/show"><i class=" feather icon-file-text info"></i></a>
                                    @else
                                    <a class="ml-1 detailpenjualan" href="/penjualan/{{ Crypt::encrypt($d->no_fak_penj) }}/showforsales"><i class=" feather icon-file-text info"></i></a>
                                    @endif
                                    @endif
                                    @if (in_array($level,$penjualan_hapus))
                                    <form method="POST" name="deleteform" class="deleteform" action="/penjualan/{{ Crypt::encrypt($d->no_fak_penj) }}/delete">
                                        @csrf
                                        @method('DELETE')
                                        <a href="#" tanggal="{{ $d->tgltransaksi }}" class="delete-confirm ml-1">
                                            <i class="feather icon-trash danger"></i>
                                        </a>
                                    </form>
                                    @endif
                                    @if (Auth::user()->level != "salesman")
                                    @if ($d->status==1)
                                    <a href="/penjualan/{{ Crypt::encrypt($d->no_fak_penj) }}/updatepending" class="ml-1"><i class="fa fa-refresh accent-1"></i></a>
                                    @endif
                                    <div class="dropdown ml-1">
                                        <a class="dropdown-toggle mr-1" type="button" id="dropdownMenuButton300" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-printer primary"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton300" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -7px, 0px);">
                                            <a class="dropdown-item" target="_blank" href="/penjualan/cetakfaktur/{{ Crypt::encrypt($d->no_fak_penj) }}"><i class="feather icon-printer mr-1"></i>Cetak Faktur</a>
                                            <a class="dropdown-item" target="_blank" href="/penjualan/cetaksuratjalan/{{ Crypt::encrypt($d->no_fak_penj) }}/1"><i class="feather icon-printer mr-1"></i>Cetak Surat Jalan 1</a>
                                            <a class="dropdown-item" target="_blank" href="/penjualan/cetaksuratjalan/{{ Crypt::encrypt($d->no_fak_penj) }}/2"><i class="feather icon-printer mr-1"></i>Cetak Surat Jalan 2</a>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $penjualan->links('vendor.pagination.vuexy') }}

            </div>
        </div>

    </div>
</div>
{{-- <div class="modal fade text-left" id="mdlprint" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Cetak Surat Jalan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Tanggal" field="tanggal" datepicker icon="feather icon-calendar" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <select name="kode_cabang" id="kode_cabang" class="form-control">
                                    <option value="">Cabang</option>
                                    @foreach ($cabang as $d)
                                    <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
@endforeach
</select>
</div>
</div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <button class="btn btn-primary btn-block"><i class="feather icon-printer"></i>Cetak</button>
        </div>
    </div>
</div>
</form>
</div>
</div>
</div>
</div> --}}
@endsection
@push('myscript')
<script>
    $(function() {
        $('#cetaksuratjalan').click(function(e) {
            //e.preventDefault(); //prevents the default submit action
            $(this).closest('form').attr('target', '_blank').submit();
        });

        $('.search').click(function(e) {
            //e.preventDefault(); //prevents the default submit action
            $(this).closest('form').attr('target', '').submit();
        });

        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "penjualan"
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
