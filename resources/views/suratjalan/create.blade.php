<form action="/suratjalan/store" method="post" id="frm">
    <input type="hidden" id="cektutuplaporan">
    <input type="hidden" id="cektemp">
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. Surat Jalan" field="no_mutasi_gudang" icon="fa fa-barcode" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. Dokumen" field="no_dok" icon="fa fa-barcode" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="tujuan" id="tujuan" class="form-control tujuan select2">
                    <option value="">Tujuan</option>
                    <option value="BANDUNG" data-tarif="1050000">BANDUNG</option>
                    <option value="PURWOKERTO" data-tarif="1225000">PURWOKERTO</option>
                    <option value="BOGOR" data-tarif="1575000">BOGOR</option>
                    <option value="SUKABUMI" data-tarif="1575000">SUKABUMI</option>
                    <option value="TEGAL" data-tarif="1700000">TEGAL</option>
                    <option value="PEKALONGAN" data-tarif="1900000">PEKALONGAN</option>
                    <option value="SURABAYA COL DESEL" data-tarif="2500000">SURABAYA COL DESEL</option>
                    <option value="SURABAYA TORONTON" data-tarif="4750000">SURABAYA TORONTON</option>
                    <option value="CIREBON" data-tarif="1300000">CIREBON</option>
                    <option value="GARUT" data-tarif="800000">GARUT</option>
                    <option value="DEMAK" data-tarif="2700000">DEMAK</option>
                    <option value="AMBON" data-tarif="2500000">AMBON</option>
                    <option value="TANGERANG" data-tarif="1875000">TANGERANG</option>
                    <option value="KLATEN" data-tarif="2700000">KLATEN</option>
                    <option value="KALIPUCANG" data-tarif="1000000">KALIPUCANG</option>
                    <option value="TASIKMALAYA" data-tarif="0">TASIKMALAYA</option>

                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="No. Polisi" field="nopol" icon="feather icon-truck" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tarif" field="tarif" icon="feather icon-dollar-sign" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tepung" field="tepung" icon="feather icon-dollar-sign" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="BS" field="bs" icon="feather icon-dollar-sign" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="angkutan" id="angkutan" class="form-control">
                    <option value="">Angkutan</option>
                    <option value="KS">ANGKUTAN KS</option>
                    <option value="KWN SUAKA">ANGKUTAN KAWAN SWAKA</option>
                    <option value="AS">ANGKUTAN AS</option>
                    <option value="SD">ANGKUTAN SD</option>
                    <option value="WAWAN">ANGKUTAN WAWAN</option>
                    <option value="RTP">ANGKUTAN RTP</option>
                    <option value="KWN GOBRAS">ANGKUTAN KWN GOBRAS</option>
                    <option value="LH">ANGKUTAN LH</option>
                    <option value="TSN">ANGKUTAN TSN</option>
                    <option value="MANDIRI">ANGKUTAN MANDIRI</option>
                    <option value="GS">ANGKUTAN GS</option>
                    <option value="CV TRESNO">ANGKUTAN CV TRESNO</option>
                    <option value="KS">ANGKUTAN KS</option>
                    <option value="MSA">ANGKUTAN MSA</option>
                    <option value="MITRA KOMANDO">ANGKUTAN MITRA KOMANDO</option>
                    <option value="ARP MANDIRI">ANGKUTAN ARP MANDIRI</option>
                    <option value="CAHAYA BIRU">ANGKUTAN CAHAYA BIRU</option>

                </select>
            </div>
        </div>
    </div>

</form>
