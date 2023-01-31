@extends('layouts.sap.sap')
@section('content')
<style>
    .main_div {
        padding: 30px;
    }

    input,
    textarea,
    select {
        background: none;
        color: #5b5757;
        font-size: 18px;
        padding: 10px 10px 10px 15px;
        display: block;
        width: 320px;
        border: none;
        border-radius: 10px;
        border: 1px solid rgb(172, 42, 42);
        width: 100%
    }

    input:hover {
        border: 1px solid rgb(199, 50, 50);
    }

    input:focus,
    textarea:focus {
        outline: none;
        border: 1px solid rgb(207, 42, 42);
    }

    input:focus~label,
    input:valid~label,
    textarea:focus~label,
    textarea:valid~label {
        top: -8px;
        font-size: 14px;
        color: #000;
        left: 11px;
        color: rgb(172, 42, 42);
    }

    input:focus~.bar:before,
    textarea:focus~.bar:before {
        width: 320px;
    }

    input[type="password"] {
        letter-spacing: 0.3em;
    }

    .group {
        position: relative;
    }

    label {
        color: #c6c6c6;
        font-size: 14px;
        font-weight: normal;
        position: absolute;
        pointer-events: none;
        left: 15px;
        top: 12px;
        transition: 300ms ease all;
        background-color: #ecf0fb;
        padding: 0 2px;
    }

    .inputWithIcon input[type="text"] {
        padding-right: 40px;
    }

    .inputWithIcon {
        position: relative;
    }

    .inputWithIcon i {
        position: absolute;
        right: 3px;
        top: 0px;
        padding: 9px 8px;
        color: #aaa;
        transition: 0.3s;
        font-size: 1.5rem;
        color: rgb(172, 42, 42);
    }

    .inputWithIcon input[type="text"]:focus+i {
        color: dodgerBlue;
    }

    select {
        font-size: 14px !important;
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
    <div class="col-12">
        <div class="group">
            <select name="kode_cabang" class="select_join" id="kode_cabang">
                <option value="">Pilih Cabang</option>
                @foreach ($cabang as $d)
                <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col" id="getsalesperfomance">

    </div>
</div>
@endsection
@push('myscript')
<script>
    // const myDatePicker = MCDatepicker.create({
    //     el: '#tanggal'
    //     , dateFormat: 'YYYY-MM-DD'

    // , });

    // myDatePicker.onSelect(function(date, formatDate) {
    //     var tanggal = formatDate;
    //     var kode_cabang = $("#kode_cabang").val();
    //     showperfomance(tanggal, kode_cabang);
    // });

    $("#tanggal").daterangepicker({
        opens: 'left'
        , isMobile: true
        , autoApply: true
        , locale: {
            format: 'DD/MM/YYYY'
        }
    });

    // $('.drp-calendar.right').hide();
    // $('.drp-calendar.left').addClass('single');

    // $('.calendar-table').on('DOMSubtreeModified', function() {
    //     var el = $(".prev.available").parent().children().last();
    //     if (el.hasClass('next available')) {
    //         return;
    //     }
    //     el.addClass('next available');
    //     el.append('<span></span>');
    // });

    $("#kode_cabang").change(function() {
        var tanggal = $("#tanggal").val();
        var kode_cabang = $("#kode_cabang").val();
        showperfomance(tanggal, kode_cabang);
    });

    $("#tanggal").change(function() {
        var tanggal = $(this).val();
        var kode_cabang = $("#kode_cabang").val();
        showperfomance(tanggal, kode_cabang);
    });

    function showperfomance(tanggal, kode_cabang) {
        $.ajax({
            type: 'POST'
            , url: '/getsalesperfomance'
            , data: {
                _token: "{{ csrf_token() }}"
                , tanggal: tanggal
                , kode_cabang: kode_cabang
            }
            , cache: false
            , success: function(respond) {
                $("#getsalesperfomance").html(respond);
            }
        });
    }

</script>

@endpush
