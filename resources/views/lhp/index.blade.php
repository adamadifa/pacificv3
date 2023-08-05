@extends('layouts.midone')
@section('titlepage','LHP')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">LHP</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/lhp">LHP</a>
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
                <div class="card-header">
                    @if (in_array($level,$lhp_menu))
                    <a href="/lhp/create" class="btn btn-primary"><i class="fa fa-plus mr-1"></i> Buat LHP</a>
                    @endif
                </div>
                <div class="card-body">
                    <form action="/lhp" id="frmcari">
                        <div class="row">
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-3 col-sm-12">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                            <div class="col-lg-2 col-sm-2">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{
                                            $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <div class="form-group  ">
                                    <select name="id_karyawan" id="id_karyawan" class="form-control">
                                        <option value="">Semua Salesman</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')
                    <table class="table table-hover-animation">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode LHP</th>
                                <th>Tanggal</th>
                                <th>Cabang</th>
                                <th>Salesman</th>
                                <th>Rute</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Kas Kecil -->

@endsection
