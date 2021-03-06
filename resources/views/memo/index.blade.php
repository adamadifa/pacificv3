@extends('layouts.midone')
@section('titlepage','E-Manual Book')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">E-Manual Regulation Center</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">E-Manual Regulation Center</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="row mb-1">
            <div class="col-12">
                <a href="/memo/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i>Tambah Data</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a href="/memo/MKT/show">
                    <div class="card text-white">
                        <img src="{{ asset('app-assets/memo/marketing.jpg') }}" class="card-img" alt="card-img-6">
                    </div>
                </a>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a href="/memo/ACC/show">
                    <div class="card text-white">
                        <img src="{{ asset('app-assets/memo/Accounting.jpg') }}" class="card-img" alt="card-img-6">
                    </div>
                </a>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a href="/memo/KEU/show">
                    <div class="card text-white">
                        <img src="{{ asset('app-assets/memo/finance.jpg') }}" class="card-img" alt="card-img-6">
                    </div>
                </a>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a href="/memo/HRD/show">
                    <div class="card text-white">
                        <img src="{{ asset('app-assets/memo/HRD.jpg') }}" class="card-img" alt="card-img-6">
                    </div>
                </a>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a href="/memo/PMB/show">
                    <div class="card text-white">
                        <img src="{{ asset('app-assets/memo/purchasing.jpg') }}" class="card-img" alt="card-img-6">
                    </div>
                </a>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a href="/memo/GAF/show">
                    <div class="card text-white">
                        <img src="{{ asset('app-assets/memo/ga.jpg') }}" class="card-img" alt="card-img-6">
                    </div>
                </a>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a href="/memo/GAF/show">
                    <div class="card text-white">
                        <img src="{{ asset('app-assets/memo/audit.jpg') }}" class="card-img" alt="card-img-6">
                    </div>
                </a>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a href="/memo/PRD/show">
                    <div class="card text-white">
                        <img src="{{ asset('app-assets/memo/produksi.jpg') }}" class="card-img" alt="card-img-6">
                    </div>
                </a>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a href="/memo/GDG/show">
                    <div class="card text-white">
                        <img src="{{ asset('app-assets/memo/gudang.jpg') }}" class="card-img" alt="card-img-6">
                    </div>
                </a>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a href="/memo/ALL/show">
                    <div class="card text-white">
                        <img src="{{ asset('app-assets/memo/alldepartment.jpg') }}" class="card-img" alt="card-img-6">
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
