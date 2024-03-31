@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- Dashboard Analytics Start -->
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Permintaan Produksi {{ $bulan }} Tahun {{ date('Y') }}
                            </h4>
                        </div>
                        <div class="card-body">
                            @if ($permintaan != null)
                                <table class="table table-bordered table-striped table-hover" style="width: 100%">
                                    <tr>
                                        <td><b>No Permintaan</b></td>
                                        <td>:</td>
                                        <td><?php echo $permintaan->no_permintaan; ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Tanggal Permintaan</b></td>
                                        <td>:</td>
                                        <td><?php echo DateToIndo2($permintaan->tgl_permintaan); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>No Order</b></td>
                                        <td>:</td>
                                        <td><?php echo $permintaan->no_order; ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Bulan</b></td>
                                        <td>:</td>
                                        <td><?php echo $namabulan[$permintaan->bulan]; ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Tahun</b></td>
                                        <td>:</td>
                                        <td><?php echo $permintaan->tahun; ?></td>
                                    </tr>
                                </table>
                                <table class="table table-bordered table-striped table-hover" id="mytable">
                                    <thead>
                                        <tr>
                                            <th width="10px" style="vertical-align: middle;">No</th>
                                            <th style="vertical-align: middle; text-align: center;">Produk</th>
                                            <th style="text-align:center;vertical-align: middle;">Permintaan</th>
                                            <th style="text-align:center;vertical-align: middle;">Realisasi</th>
                                            <th style="text-align:center;vertical-align: middle;">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($detail as $d)
                                            <?php
                                            $permintaan = $d->oman_mkt - $d->stok_gudang + $d->buffer_stok;
                                            
                                            if ($d->jmlrealisasi != 0) {
                                                $persen = ($d->jmlrealisasi / $permintaan) * 100;
                                            } else {
                                                $persen = 0;
                                            }
                                            
                                            if (!empty($permintaan)) {
                                                if ($persen < 50) {
                                                    $color = 'danger';
                                                } elseif ($persen < 90) {
                                                    $color = 'warning';
                                                } else {
                                                    $color = 'success';
                                                }
                                            } else {
                                                $color = 'primary';
                                            }
                                            ?>
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $d->kode_produk }}</td>
                                                <td class="text-right">{{ rupiah($permintaan) }}</td>
                                                <td class="text-right">{{ rupiah($d->jmlrealisasi) }}</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-{{ $color }}">{{ ROUND($persen, 2) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-warning">
                                    Data Permintaan Produksi Untuk Bulan Ini Belum Tersedia ! atau Belum Di Proses..!
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-sm-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Rekap Hasil Produksi</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select class="form-control" id="tahun" name="tahun">
                                            <option value="">Tahun</option>
                                            <?php for ($t = 2019; $t <= $tahun; $t++) { ?>
                                            <option <?php if (date('Y') == $t) {
                                                echo 'selected';
                                            } ?> value="<?php echo $t; ?>"><?php echo $t; ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th rowspan="2" style="text-align:center">No</th>
                                                    <th rowspan="2" style="text-align:center">Produk</th>
                                                    <th colspan='12' style="text-align:center">Bulan</th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align:center">Jan</th>
                                                    <th style="text-align:center">Feb</th>
                                                    <th style="text-align:center">Mar</th>
                                                    <th style="text-align:center">Apr</th>
                                                    <th style="text-align:center">Mei</th>
                                                    <th style="text-align:center">Jun</th>
                                                    <th style="text-align:center">Jul</th>
                                                    <th style="text-align:center">Agu</th>
                                                    <th style="text-align:center">Sept</th>
                                                    <th style="text-align:center">Okt</th>
                                                    <th style="text-align:center">Nov</th>
                                                    <th style="text-align:center">Des</th>
                                                </tr>
                                            </thead>
                                            <tbody id="loadrekapproduksi">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Grafik Produksi</h4>
                        </div>
                        <div class="card-body">
                            <figure class="highcharts-figure">
                                <div id="loadgrafik"></div>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            function loadrekapproduksi() {
                var tahun = $("#tahun").val();
                $.ajax({
                    type: 'POST',
                    url: '/loadrekapproduksi',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tahun: tahun
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadrekapproduksi").html(respond);
                    }
                });
            }

            function loadgrafik() {
                var tahun = $("#tahun").val();
                $.ajax({
                    type: 'POST',
                    url: '/loadgrafikproduksi',
                    data: {
                        _token: "{{ csrf_token() }}",
                        tahun: tahun
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadgrafik").html(respond);
                    }
                });
            }
            $("#tahun").change(function() {
                loadrekapproduksi();
                loadgrafik();
            });

            loadgrafik();
            loadrekapproduksi();
        });
    </script>
@endpush
