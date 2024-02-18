@extends('layouts.midone')
@section('titlepage', 'Input Saldo Awal BJ')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Buffer & Max Stok</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Buffer & Max Stok</a>
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
            <div class="col-md-12 col-sm-8 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form action="/worksheetom/storebufferlimit" method="POST" id="frm">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        @if (Auth::user()->kode_cabang != 'PCF')
                                            <input type="hidden" name="kode_cabang" id="kode_cabang"
                                                value="{{ Auth::user()->kode_cabang }}">
                                        @else
                                            <select name="kode_cabang" id="kode_cabang" class="form-control">
                                                <option value="">Pilih Cabang</option>
                                                @foreach ($cabang as $d)
                                                    <option
                                                        {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }}
                                                        value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <table class="table table-border">
                                <thead>
                                    <tr>
                                        <th align="">No</th>
                                        <th style="text-align:center">Nama Barang</th>
                                        <th style="text-align:center; width:15%">Buffer Stok</th>
                                        <th style="text-align:center; width:15%">Max Stok</th>
                                    </tr>
                                </thead>
                                <tbody id="loaddetail">

                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block" type="submit" name="submit"><i
                                                class="fa fa-send mr-1"></i> Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('myscript')
    <script>
        $(function() {
            function loaddetail() {
                var kode_cabang = $("#kode_cabang").val();
                if (kode_cabang == "") {
                    swal("Oops!", "Cabang Harus Diisi !", "warning");
                    return false;
                } else {
                    $.ajax({
                        type: 'POST',
                        url: '/worksheetom/getbufferlimit',
                        data: {
                            _token: "{{ csrf_token() }}",
                            kode_cabang: kode_cabang
                        },
                        cache: false,
                        success: function(respond) {
                            $("#loaddetail").html(respond);
                        }
                    });
                }
            }
            loaddetail();
            $("#kode_cabang").change(function(e) {
                loaddetail();
            });
        });
    </script>
@endpush
