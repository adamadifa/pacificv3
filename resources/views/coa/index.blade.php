@extends('layouts.midone')
@section('titlepage', 'Data Salesman')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Chart Of Account</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/coa">Chart Of Account</a>
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
            <div class="col-md-5 col-sm-5">
                <div class="card">
                    <div class="card-header">
                        <a href="/coa/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>Tambah Data</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover-animation">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Akun</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($coa as $d)
                                        @if ($d->level == 1)
                                            @php
                                                $padding = '20px';
                                            @endphp
                                        @elseif($d->level == 2)
                                            @php
                                                $padding = '40px';
                                            @endphp
                                        @elseif($d->level == 3)
                                            @php
                                                $padding = '60px';
                                            @endphp
                                        @else
                                            @php
                                                $padding = 0;
                                            @endphp
                                        @endif
                                        <tr>
                                            <td style="padding-left:{{ $padding }}; font-weight:{{ $d->level == 0 ? 'bold' : '' }}">
                                                {{ $d->kode_akun }} {{ $d->nama_akun }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="/coa/{{ Crypt::encrypt($d->kode_akun) }}/edit"><i class="feather icon-edit success"></i></a>
                                                    <form method="POST" class="deleteform" action="/coa/{{ Crypt::encrypt($d->kode_akun) }}/delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="#" class="delete-confirm ml-1">
                                                            <i class="feather icon-trash danger"></i>
                                                        </a>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        <!-- DataTable ends -->
                    </div>
                </div>
            </div>
            <!-- Data list view end -->
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
                        title: `Are you sure you want to delete this record?`,
                        text: "If you delete this, it will be gone forever.",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        }
                    });
            });
        });
    </script>
@endpush
