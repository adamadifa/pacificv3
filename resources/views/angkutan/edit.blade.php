<form action="/angkutan/{{Crypt::encrypt($angkutan->no_surat_jalan)}}/update" method="post" id="frmEdit">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. Surat jalan" field="no_surat_jalan" value="{{$angkutan->no_surat_jalan}}" icon="fa fa-barcode" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="tujuan" id="tujuan" class="form-control tujuan select2">
                    <option value="">Tujuan</option>
                    <option {{$angkutan->tujuan == "BANDUNG" ? 'selected' : ''}} value="BANDUNG">BANDUNG</option>
                    <option {{$angkutan->tujuan == "PURWOKERTO" ? 'selected' : ''}} value="PURWOKERTO">PURWOKERTO</option>
                    <option {{$angkutan->tujuan == "BOGOR" ? 'selected' : ''}} value="BOGOR">BOGOR</option>
                    <option {{$angkutan->tujuan == "SUKABUMI" ? 'selected' : ''}} value="SUKABUMI">SUKABUMI</option>
                    <option {{$angkutan->tujuan == "TEGAL" ? 'selected' : ''}} value="TEGAL">TEGAL</option>
                    <option {{$angkutan->tujuan == "PEKALONGAN" ? 'selected' : ''}} value="PEKALONGAN">PEKALONGAN</option>
                    <option {{$angkutan->tujuan == "SURABAYA COL DESEL" ? 'selected' : ''}} value="SURABAYA COL DESEL">SURABAYA COL DESEL</option>
                    <option {{$angkutan->tujuan == "SURABAYA TORONTON" ? 'selected' : ''}} value="SURABAYA TORONTON">SURABAYA TORONTON</option>
                    <option {{$angkutan->tujuan == "CIREBON" ? 'selected' : ''}} value="CIREBON">CIREBON</option>
                    <option {{$angkutan->tujuan == "GARUT" ? 'selected' : ''}} value="GARUT">GARUT</option>
                    <option {{$angkutan->tujuan == "DEMAK" ? 'selected' : ''}} value="DEMAK">DEMAK</option>
                    <option {{$angkutan->tujuan == "AMBON" ? 'selected' : ''}} value="AMBON">AMBON</option>
                    <option {{$angkutan->tujuan == "TANGERANG" ? 'selected' : ''}} value="TANGERANG">TANGERANG</option>
                    <option {{$angkutan->tujuan == "KLATEN" ? 'selected' : ''}} value="KLATEN">KLATEN</option>
                    <option {{$angkutan->tujuan == "KALIPUCANG" ? 'selected' : ''}} value="KALIPUCANG">KALIPUCANG</option>
                    <option {{$angkutan->tujuan == "TASIKMALAYA" ? 'selected' : ''}} value="TASIKMALAYA">TASIKMALAYA</option>
                    <option {{$angkutan->tujuan == "PURWAKARTA" ? 'selected' : ''}} value="PURWAKARTA">PURWAKARTA</option>
                    <option {{$angkutan->tujuan == "PEMALANG" ? 'selected' : ''}} value="PEMALANG">PEMALANG</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. Polisi" field="nopol" icon="feather icon-truck" value="{{$angkutan->nopol}}" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tarif" field="tarif" icon="feather icon-dollar-sign" value="{{rupiah($angkutan->tarif)}}" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tepung" field="tepung" icon="feather icon-dollar-sign" value="{{rupiah($angkutan->tepung)}}" right />
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <x-inputtext label="BS" field="bs" icon="feather icon-dollar-sign" value="{{rupiah($angkutan->bs)}}" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="angkutan" id="angkutan" class="form-control select2">
                    <option value="">Angkutan</option>
                    <option {{$angkutan->angkutan == "ANGKUTAN KS" ? 'selected' : ''}} value="KS">ANGKUTAN KS</option>
                    <option {{$angkutan->angkutan == "KWN SUAKA" ? 'selected' : ''}} value="KWN SUAKA">ANGKUTAN KAWAN SWAKA</option>
                    <option {{$angkutan->angkutan == "AS" ? 'selected' : ''}} value="AS">ANGKUTAN AS</option>
                    <option {{$angkutan->angkutan == "SD" ? 'selected' : ''}} value="SD">ANGKUTAN SD</option>
                    <option {{$angkutan->angkutan == "WAWAN" ? 'selected' : ''}} value="WAWAN">ANGKUTAN WAWAN</option>
                    <option {{$angkutan->angkutan == "RTP" ? 'selected' : ''}} value="RTP">ANGKUTAN RTP</option>
                    <option {{$angkutan->angkutan == "KWN GOBRAS" ? 'selected' : ''}} value="KWN GOBRAS">ANGKUTAN KWN GOBRAS</option>
                    <option {{$angkutan->angkutan == "LH" ? 'selected' : ''}} value="LH">ANGKUTAN LH</option>
                    <option {{$angkutan->angkutan == "TSN" ? 'selected' : ''}} value="TSN">ANGKUTAN TSN</option>
                    <option {{$angkutan->angkutan == "MANDIRI" ? 'selected' : ''}} value="MANDIRI">ANGKUTAN MANDIRI</option>
                    <option {{$angkutan->angkutan == "GS" ? 'selected' : ''}} value="GS">ANGKUTAN GS</option>
                    <option {{$angkutan->angkutan == "CV TRESNO" ? 'selected' : ''}} value="CV TRESNO">ANGKUTAN CV TRESNO</option>
                    <option {{$angkutan->angkutan == "KS" ? 'selected' : ''}} value="KS">ANGKUTAN KS</option>
                    <option {{$angkutan->angkutan == "MSA" ? 'selected' : ''}} value="MSA">ANGKUTAN MSA</option>
                    <option {{$angkutan->angkutan == "MITRA KOMANDO" ? 'selected' : ''}} value="MITRA KOMANDO">ANGKUTAN MITRA KOMANDO</option>
                    <option {{$angkutan->angkutan == "ARP MANDIRI" ? 'selected' : ''}} value="ARP MANDIRI">ANGKUTAN ARP MANDIRI</option>
                    <option {{$angkutan->angkutan == "CAHAYA BIRU" ? 'selected' : ''}} value="CAHAYA BIRU">ANGKUTAN CAHAYA BIRU</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit" name="submit"><i class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>

</form>
<script>
    $(function() {
        $("#tarif,#tepung,#bs").maskMoney();
        $("#frmEdit").submit(function() {
            var no_surat_jalan = $("#frmEdit").find("#no_surat_jalan").val();
            if (no_surat_jalan == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Surat Jalan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmEdit").find("#no_surat_jalan").focus();
                });

                return false;
            }
        });
    });

</script>
