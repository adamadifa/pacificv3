@extends('layouts.midone')
@section('titlepage', 'Approve Komisi')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Approve Komisi</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/komisiapprove">Approve Komisi</a>
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


                    <div class="table-responsive">
                        <table class="table table-hover-animation">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">Cabang</th>
                                    <th>GM Mkt</th>
                                    <th>GM Adm</th>
                                    <th>Direktur</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="loadapprovekomisi">
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
        function loadapprovekomisi() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            $.ajax({
                type: 'POST'
                , url: '/getapprovekomisi'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadapprovekomisi").html(respond);
                }
            });
        }

        loadapprovekomisi();
        $("#bulan, #tahun").change(function() {
            loadapprovekomisi();
        });

    });

</script>
@endpush
