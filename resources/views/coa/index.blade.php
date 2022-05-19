@extends('layouts.midone')
@section('titlepage','Data Salesman')
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
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Kode Akun</th>
                                    <th>Nama Akun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coa as $d)
                                <tr>
                                    <td>{{ $d->kode_akun }}</td>
                                    <td>{{ $d->nama_akun }}</td>
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
