@extends('layouts.midone')
@section('titlepage','Data Users')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Data User</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/user">User</a>
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
        <div class="col-md-8 col-sm-8">
            <div class="card">
                <div class="card-body">
                    <form action="/user">
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <x-inputtext label="Nama User" field="nama" icon="feather icon-user" value="{{ Request('nama') }}" />
                            </div>
                            @if (Auth::user()->kode_cabang =="PCF")

                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="" class="form-control">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif

                            <div class="col-lg-4 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search mr-2"></i> Search</button>
                            </div>
                        </div>

                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID User</th>
                                    <th>Nama User</th>
                                    <th>Level</th>
                                    <th>Cabang</th>
                                    <th>Status</th>
                                    <th>Last Seen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user as $d)
                                @php
                                $status = $d->status;
                                @endphp
                                <tr>
                                    <td>{{ $d->id }}</td>
                                    <td>{{ $d->name }}</td>
                                    <td>{{ ucwords($d->level) }}</td>
                                    <td>{{ $d->kode_cabang }}</td>
                                    <td>
                                        <a href="/user/{{ Crypt::encrypt($d->id) }}/activated">
                                            <span class="badge bg-{{ $status==1?'success':'danger' }}">{{ $status==1?'Aktif':'Non Aktif' }}</span>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-info">{{ $d->last_seen }}</span></td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <form method="POST" class="deleteform" action="/user/{{Crypt::encrypt($d->id)}}/delete">
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
