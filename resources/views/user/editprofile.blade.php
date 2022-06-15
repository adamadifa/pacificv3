@extends('layouts.midone')
@section('titlepage', 'Ganti Password')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Edit Profile</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/user/gantipassword">User</a></li>
                            <li class="breadcrumb-item"><a href="#">Edit Profile</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">

    <form class="form" action="/user/{{ Crypt::encrypt($user->id) }}/updateprofile" method="POST" enctype="multipart/form-data">
        <div class="col-md-12">
            @include('layouts.notification')
            <div class="row">
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Profile</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Nama" field="name" icon="feather icon-user" value="{{ $user->name }}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group  @error('foto') error @enderror">
                                            <div class="custom-file">
                                                <input type="file" name="foto" class="custom-file-input" id="inputGroupFile01">
                                                <label class="custom-file-label" for="inputGroupFile01">Upload Foto</label>
                                            </div>
                                            @error('foto')
                                            <div class="help-block">
                                                <ul role="alert">
                                                    <li>{{ $message }}</li>
                                                </ul>
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3 col-sm-12">
                                        @if (!empty($user->foto))
                                        @php
                                        $path = Storage::url('users/'.$user->foto);
                                        @endphp
                                        <img class="card-img img-fluid" src="{{ url($path) }}" alt="Card image">
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="theme" id="theme" class="form-control">
                                                <option value="">Pilih Tema</option>
                                                <option {{ $user->theme == 1 ? 'selected' : '' }} value="1">Light Mode</option>
                                                <option {{ $user->theme == 2 ? 'selected' : '' }} value="2">Dark Mode</option>
                                            </select>
                                        </div>
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
