@extends('layouts.sap.sap')
@section('content')
<style>
    #border1 {
        border: 1px solid #b11036;
    }

</style>
<style>
    .float {
        position: fixed;
        bottom: 70px;
        right: 20px;
        text-align: center;
        z-index: 9000;
    }

    .btn-rounded {
        border-radius: 100px;
        padding: 15px;
    }

</style>
<div class="row mb-2">
    <div class="col-12">
        <div class="inputWithIcon">
            <i class="bi bi-calendar"></i>
            <input type="text" id="tanggal" required="required" autocomplete="off" onfocus="blur()" />
            <label>Tanggal</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col" id="getsmactivity">
    </div>
</div>
<a href="/sap/smactivity/create" class="float btn btn-rounded" style="background-color:#b11036; color:white;"><i class='bx bx-plus' style="font-size:1.5rem"></i></a>
<div class="row mt-2">
    <div class="col-12">
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        $("#tanggal").daterangepicker({
            opens: 'left'
            , isMobile: true
            , autoApply: true
            , locale: {
                format: 'DD/MM/YYYY'
            }
        });


        function showactivity() {
            var tanggal = $("#tanggal").val();
            //alert(tanggal);
            $.ajax({
                type: 'POST'
                , url: '/getsmactivity'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                }
                , cache: false
                , success: function(respond) {
                    $("#getsmactivity").html(respond);
                }
            });
        }

        $("#tanggal").change(function() {
            showactivity();
        });

        showactivity();
    });

</script>
@endpush
