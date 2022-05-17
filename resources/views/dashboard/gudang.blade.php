@extends('layouts.midone')
@section('titlepage', 'Dashboard')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <section>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Persediaan All Cabang Berdasarkan DPB</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="text-center" id="loadingrekappersediaan">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div id="loadrekappersediaan">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    </section>
</div>
</div>


@endsection

@push('myscript')
<script>
    $(function() {

        function loadrekappersediaan() {
            $('#loadrekappersediaan').html("");
            $('#loadingrekappersediaan').show();
            $.ajax({
                type: 'GET'
                , url: '/rekappersediaandashboard'
                , cache: false
                , success: function(respond) {
                    $('#loadingrekappersediaan').hide();
                    $("#loadrekappersediaan").html(respond);
                }
            });
        }

        loadrekappersediaan();

    });

</script>
@endpush
