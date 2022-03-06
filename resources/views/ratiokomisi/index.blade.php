@extends('layouts.midone')
@section('titlepage', 'Ratio Komisi')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Ratio Komisi</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/ratiokomisi">Ratio Komisi</a>
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
        <div class="col-md-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <form action="#">
                        <div class="row">

                            <div class="col-12">
                                <div class="form-group">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option value="{{ $c->kode_cabang }}">
                                            {{ $c->nama_cabang }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select class="form-control" id="bulan" name="bulan">
                                        <option value="">Bulan</option>
                                        <?php
                                                $bulanini = date("m");
                                                for ($i = 1; $i < count($bulan); $i++) {
                                            ?>
                                        <option <?php if (empty(Request('bulan'))) { if ($bulanini==$i) {
                                            echo 'selected' ; } } else { if (Request('bulan')==$i) { echo 'selected' ; }
                                            } ?> value="
                                            <?php echo $i; ?>">
                                            <?php echo $bulan[$i]; ?>
                                        </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <select class="form-control" id="tahun" name="tahun">
                                        <?php
                                        $tahunmulai = 2020;
                                        for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                        ?>
                                        <option <?php if (empty(Request('tahun'))) { if (date('Y')==$thn) {
                                            echo 'Selected' ; } } else { if (Request('tahun')==$thn) { echo 'selected' ;
                                            } } ?> value="
                                            <?php echo $thn; ?>">
                                            <?php echo $thn; ?>
                                        </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-lg-4 col-sm-12">
                            <button type="submit" name="submit" value="1" class="btn btn-primary"><i
                                    class="fa fa-search mr-2"></i> Search</button>
                        </div> --}}
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Ratio</th>
                                </tr>
                            </thead>
                            <tbody>
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
