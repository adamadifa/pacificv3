<form action="/suratjalan/store" method="post" id="frmSuratjalan">
    @csrf
    <input type="hidden" id="cektutuplaporan">
    <input type="hidden" id="cektempsj">
    <input type="hidden" name="no_permintaan_pengiriman" id="no_permintaan_pengiriman_sj" value="{{ Crypt::encrypt($permintaanpengiriman->no_permintaan_pengiriman) }}">
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
            <x-inputtext label="Tanggal Surat jalan" field="tgl_mutasi_gudang" icon="feather icon-calendar" datepicker />
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
                    <option value="PURWAKARTA" data-tarif="0">PURWAKARTA</option>

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
                <select name="angkutan" id="angkutan" class="form-control select2">
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
                    <option value="CAHAYA BARU">ANGKUTAN CAHAYA BARU</option>

                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <td>No. Permintaan</td>
                    <td>{{ $permintaanpengiriman->no_permintaan_pengiriman }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>{{ DateToIndo2($permintaanpengiriman->tgl_permintaan_pengiriman) }}</td>
                </tr>
                <tr>
                    <td>Cabang</td>
                    <td>{{ strtoupper($permintaanpengiriman->nama_cabang) }}</td>
                </tr>
                <tr>
                    <td>Keterangan</td>
                    <td>{{ $permintaanpengiriman->keterangan }}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>
                        @if ($permintaanpengiriman->status==1)
                        <span class="badge bg-success">Sudah Di Proses</span>
                        @else
                        <span class="badge bg-danger">Belum Di Proses</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-hover-animation">
                <thead class="thead-dark">
                    <tr>
                        <th colspan="2">Detail Permintaan</th>
                        <th><a href="#" class="btn btn-success btn-sm" id="masukankerealisasi"><i class="fa fa-upload mr-1"></i>Masukan Ke Realisasi</a></th>
                    </tr>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detailpermintaan as $d)
                    <tr>
                        <td>{{ $d->kode_produk }}</td>
                        <td>{{ ucwords(strtolower($d->nama_barang)) }}</td>
                        <td class="text-right" style="font-weight: bold">{{ rupiah($d->jumlah) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <div class="form-group">
                <select name="kode_produk" id="kode_produk" class="form-control select2">
                    <option value="">Produk</option>
                    @foreach ($produk as $d)
                    <option value="{{ $d->kode_produk }}">{{ $d->kode_produk }} | {{ $d->nama_barang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-sm-12 col-md-12">
            <x-inputtext field="jumlah" label="Qty" icon="feather icon-file" right />
        </div>
        <div class="row">
            <div class="col-lg-2 col-sm-12 col-md-12">
                <div class="form-group">
                    <a href="#" id="tambahproduksj" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-hover-animation">
                <thead class="thead-dark">
                    <tr>
                        <th colspan="4">Realisasi Permintaan</th>
                    </tr>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="loadrealisasipermintaan">
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mb-1">
        <div class="col-12">
            <div class="vs-checkbox-con vs-checkbox-primary">
                <input type="checkbox" class="aggrement" name="aggrement" value="aggrement">
                <span class="vs-checkbox">
                    <span class="vs-checkbox--check">
                        <i class="vs-icon feather icon-check"></i>
                    </span>
                </span>
                <span class="">Yakin Akan Disimpan ?</span>
            </div>
        </div>
    </div>
    <div class="row mt-3" id="tombolsimpan">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block"><i class="feather icon-send mr-1"></i> Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    $(function() {
        $('.aggrement').change(function() {
            if (this.checked) {
                $("#tombolsimpan").show();
            } else {
                $("#tombolsimpan").hide();
            }
        });

        function hidetombolsimpan() {
            $("#tombolsimpan").hide();
        }

        hidetombolsimpan();


        $('#no_dok').mask('AAAAAAAAAAA', {
            'translation': {
                A: {
                    pattern: /[A-Za-z0-9-]/
                }
            }
        });
        $("#tgl_mutasi_gudang").change(function() {
            var tgl_mutasi_gudang = $(this).val();
            var kode_cabang = "{{ $permintaanpengiriman->kode_cabang }}";
            $.ajax({
                type: 'POST'
                , url: '/suratjalan/buatnomorsj'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tgl_mutasi_gudang: tgl_mutasi_gudang
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#no_mutasi_gudang").val("");
                    $("#no_mutasi_gudang").val(respond);
                }
            });
            cektutuplaporan(tgl_mutasi_gudang);
        });

        function cektemp() {
            var no_permintaan_pengiriman = "{{ $permintaanpengiriman->no_permintaan_pengiriman }}";
            $.ajax({
                type: 'POST'
                , url: '/suratjalan/cektemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_permintaan_pengiriman: no_permintaan_pengiriman

                }
                , cache: false
                , success: function(respond) {
                    $("#cektempsj").val(respond);
                }
            });
        }

        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "gudangpusat"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }

        $("#tarif,#tepung,#bs").maskMoney();

        function loadrealisasipermintaan() {
            var no_permintaan_pengiriman = "{{ Crypt::encrypt($permintaanpengiriman->no_permintaan_pengiriman) }}";
            $("#loadrealisasipermintaan").load("/suratjalan/" + no_permintaan_pengiriman + "/showtemp");
            cektemp();
        }
        loadrealisasipermintaan();

        $("#masukankerealisasi").click(function(e) {
            e.preventDefault();
            var no_permintaan_pengiriman = "{{ $permintaanpengiriman->no_permintaan_pengiriman }}";
            $.ajax({
                type: 'POST'
                , url: '/suratjalan/masukankerealisasi'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_permintaan_pengiriman: no_permintaan_pengiriman
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 0) {
                        swal("Berhasil", "Data Berhasil Disimpan", "success");
                    } else if (respond == 1) {
                        swal("Ooops", "Data Sudah Ada", "warning");
                    } else {
                        swal("Oops", "Data Gagal Disimpan, Hubungi Tim IT");
                    }

                    loadrealisasipermintaan();
                }
            });
        });
        $("#tambahproduksj").click(function(e) {
            var no_permintaan_pengiriman = "{{ $permintaanpengiriman->no_permintaan_pengiriman }}";
            var kode_produk = $("#frmSuratjalan").find("#kode_produk").val();
            var jumlah = $("#frmSuratjalan").find("#jumlah").val();
            if (kode_produk == "") {
                swal({
                    title: 'Oops'
                    , text: 'Produk Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmSuratjalan").find("#kode_produk").focus();
                });
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmSuratjalan").find("#jumlah").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/suratjalan/storetemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , no_permintaan_pengiriman: no_permintaan_pengiriman
                        , kode_produk: kode_produk
                        , jumlah: jumlah
                    }
                    , success: function(respond) {
                        if (respond == 0) {
                            swal("Berhasil", "Data Berhasil Disimpan", "success");
                        } else if (respond == 1) {
                            swal("Ooops", "Data Sudah Ada", "warning");
                        } else {
                            swal("Oops", "Data Gagal Disimpan, Hubungi Tim IT");
                        }

                        loadrealisasipermintaan();
                    }
                });
            }
        });
        $("#frmSuratjalan").submit(function() {
            var no_mutasi_gudang = $("#no_mutasi_gudang").val();
            var tgl_mutasi_gudang = $("#tgl_mutasi_gudang").val();
            var no_dok = $("#no_dok").val();
            var tujuan = $("#tujuan").val();
            var nopol = $("#nopol").val();
            var tarif = $("#tarif").val();
            var tepung = $("#tepung").val();
            var bs = $("#bs").val();
            var angkutan = $("#angkutan").val();
            var cektutuplaporan = $("#cektutuplaporan").val();
            var cektemp = $("#cektempsj").val();
            if (cektutuplaporan == 1) {
                swal({
                    title: 'Oops'
                    , text: 'Laporan Sudah Di Tutup !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_mutasi_gudang").focus();
                });

                return false;
            } else if (cektemp == "" || cektemp == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmSuratjalan").find("#kode_produk").focus();
                });
                return false;
            } else if (no_mutasi_gudang == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Surat Jalan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_mutasi_gudang").focus();
                });
                return false;
            } else if (tgl_mutasi_gudang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_mutasi_gudang").focus();
                });
                return false;
            } else if (tujuan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tujuan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tujuan").focus();
                });
                return false;
            }
        });
    });

</script>
