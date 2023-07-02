<ul class="nav nav-tabs nav-justified" role="tablist">
    <li class="nav-item">
        <a class="nav-link active showgroup" id_group="29" id="sausa-tab" data-toggle="tab" href="#sausa" aria-controls="sausa" role="tab" aria-selected="true">SAUS A</a>
    </li>
    <li class="nav-item">
        <a class="nav-link showgroup" id_group="26" id="sausb-tab" data-toggle="tab" href="#sausb" aria-controls="sausb" role="tab" aria-selected="false">SAUS B</a>
    </li>
    <li class="nav-item">
        <a class="nav-link showgroup" id_group="27" id="sausc-tab" data-toggle="tab" href="#sausc" aria-controls="sausc" role="tab" aria-selected="false">SAUS C</a>
    </li>
    <li class="nav-item">
        <a class="nav-link showgroup" id_group="31" id="aidaa-tab" data-toggle="tab" href="#aidaa" aria-controls="about" role="tab" aria-selected="false">AIDA A</a>
    </li>
    <li class="nav-item">
        <a class="nav-link showgroup" id_group="28" id="aidab-tab" data-toggle="tab" href="#aidab" aria-controls="aidab" role="tab" aria-selected="false">AIDA B</a>
    </li>
    <li class="nav-item">
        <a class="nav-link showgroup" id_group="30" id="aidac-tab" data-toggle="tab" href="#aidac" aria-controls="aidac" role="tab" aria-selected="false">AIDA C</a>
    </li>
    <li class="nav-item">
        <a class="nav-link showgroup" id_group="23" id="aidac-tab" data-toggle="tab" href="#aidac" aria-controls="aidac" role="tab" aria-selected="false">PDQC</a>
    </li>
    <li class="nav-item">
        <a class="nav-link showgroup" id_group="24" id="aidac-tab" data-toggle="tab" href="#aidac" aria-controls="aidac" role="tab" aria-selected="false">QC RM</a>
    </li>
    <li class="nav-item">
        <a class="nav-link showgroup" id_group="25" id="aidac-tab" data-toggle="tab" href="#aidac" aria-controls="aidac" role="tab" aria-selected="false">QC LAB</a>
    </li>
    <li class="nav-item">
        <a class="nav-link showgroup" id_group="18" id="aidac-tab" data-toggle="tab" href="#aidac" aria-controls="aidac" role="tab" aria-selected="false">MTC</a>
    </li>
    <li class="nav-item">
        <a class="nav-link showgroup" id_group="7" id="aidac-tab" data-toggle="tab" href="#aidac" aria-controls="aidac" role="tab" aria-selected="false">SC</a>
    </li>
</ul>
<div class="row">
    <div class="col-12">
        <div class="text-center" id="loading">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div id="loadgroup"></div>
    </div>
</div>
<script>
    $(function() {
        //$("#loading").hide();

        function loadgroup(id_group = 29) {
            $('#loadgroup').html("");
            $('#loading').show();
            var kode_setjadwal = "{{ $kode_setjadwal }}";
            var shift = "{{ $shift }}";
            //$("#loadgroup").load('/konfigurasijadwal/' + id_group + '/showgroup');
            // ('#loadingrekappersediaan').hide();
            // $("#loadrekappersediaan").html(respond);
            $.ajax({
                type: 'GET'
                , url: '/konfigurasijadwal/' + id_group + '/showgroup'
                , data: {
                    kode_setjadwal: kode_setjadwal
                    , shift: shift
                }
                , cache: false
                , success: function(respond) {
                    $('#loading').hide("");
                    $("#loadgroup").html(respond);
                }
            });
        }
        $(".showgroup").click(function(e) {
            e.preventDefault();
            var id_group = $(this).attr('id_group');
            loadgroup(id_group);
        });

        loadgroup();
    });

</script>
