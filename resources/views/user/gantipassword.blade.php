@extends('layouts.midone')
@section('titlepage', 'Ganti Password')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Ganti Password</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/user/gantipassword">User</a></li>
                            <li class="breadcrumb-item"><a href="#">Ganti Password</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">

    <form class="form" action="/user/{{ Crypt::encrypt($user->id) }}/update" method="POST">
        <div class="col-md-12">
            @include('layouts.notification')
            <div class="row">
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Ganti Password</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Email" field="email" icon="feather icon-mail" value="{{ $user->email }}" />
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputpassword label="Password Lama" field="password_lama" icon="fa fa-key" pass />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputpassword label="Password Baru" field="password_baru" icon="fa fa-key" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i> Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
