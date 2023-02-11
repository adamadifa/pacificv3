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
<div class="row mt-2">
    <div class="col-12">
        <div class="group">
            <select name="bulan" class="select_join" id="bulan">
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
    </div>
</div>
<div class="row mt-2">
    <div class="col-12">
        <div class="group">
            <select name="tahun" class="select_join" id="tahun">
                <option value="">Tahun</option>
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
<div class="row mt-2">
    <div class="col-12" id="loadrealisasitargetsales">
    </div>
</div>

<script>
    $(function() {
        function loadrealisasitargetsales() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            var id_karyawan = "{{ $id_karyawan }}";

            $.ajax({
                type: 'POST'
                , url: '/sap/getrealisasitargetsales'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id_karyawan: id_karyawan
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#loadrealisasitargetsales").html(respond);
                }
            });
        }

        loadrealisasitargetsales();

        $("#bulan, #tahun").change(function() {
            loadrealisasitargetsales();
        });
    });

</script>
