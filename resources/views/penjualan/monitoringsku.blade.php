@extends('layouts.midone')
@section('titlepage','Data Pelanggan')
@section('content')
<style>
    .card {
        margin-bottom: 1rem !important;
    }

</style>

<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h4 class="content-header-title float-left mb-0">Monitoring SKU</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="row mt-1">
            <div class="col-12">
                @include('layouts.notification')
                <div class="row">
                    <div class="col-12">
                        <div class="form-group" style="margin-bottom: 5px">
                            <select class="form-control" id="bulan" name="bulan">
                                <option value="">Bulan</option>
                                <?php
                                    $bulanini = date("m");
                                    for ($i = 1; $i < count($bulan); $i++) {
                                    ?>
                                <option <?php if ($bulanini == $i) {echo "selected";} ?> value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
                                <?php
                                    }
                                    ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" id="tahun" name="tahun">
                                <?php
                                    $tahunmulai = 2020;
                                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                                    ?>
                                <option <?php if (date('Y') == $thn) { echo "Selected";} ?> value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                                <?php
                                    }
                                    ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="loadsku">
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
        function loadsku() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            $.ajax({
                type: 'POST'
                , url: '/getsku'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadsku").html(respond);
                }
            });
        }

        loadsku();

        $("#bulan, #tahun").change(function(e) {
            loadsku();
        });
    });

</script>
@endpush
