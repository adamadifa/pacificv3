@extends('layouts.midone')
@section('titlepage', 'Saldo Awal Piutang')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Saldo Awal Piutang</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/saldoawalpiutang">Saldo Awal Piutang</a>
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
                    <div class="row">
                        <div class="col-lg-12 col-sm-12">
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" id="generatesaldoawalpiutang"><i class="fa fa-gear mr-2"></i> Generate Saldo Awal Piutang</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>ID Sales</th>
                                    <th>Nama Sales</th>
                                    <th>Saldo Piutang</th>
                                </tr>
                            </thead>
                            <tbody id="loadsaldoawalpiutang">
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
        function loadsaldoawalpiutang() {
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            $.ajax({
                type: 'POST'
                , url: '/loadsaldoawalpiutang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#loadsaldoawalpiutang").html(respond);
                }
            });
        }
        $("#kode_cabang").change(function(e) {
            loadsaldoawalpiutang();
        });

        $("#bulan").change(function(e) {
            loadsaldoawalpiutang();
        });

        $("#tahun").change(function(e) {
            loadsaldoawalpiutang();
        });

        $("#generatesaldoawalpiutang").click(function(e) {
            e.preventDefault();
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            if (kode_cabang == "") {
                swal("Oops", "Cabang Harus Dipilih", "warning");
            } else if (bulan == "") {
                swal("Oops", "Bulan Harus Dipilih", "warning");
            } else if (tahun == "") {
                swal("Oops", "Tahun Harus Dipilih", "warning");
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/generatesaldoawalpiutang'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_cabang: kode_cabang
                        , bulan: bulan
                        , tahun: tahun
                    }
                    , cache: false
                    , success: function(respond) {
                        console.log(respond);
                        loadsaldoawalpiutang();
                    }
                });
            }

        });
    });

</script>
@endpush
