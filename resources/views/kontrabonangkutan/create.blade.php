@extends('layouts.midone')
@section('titlepage','Kontrabon Angkutan')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Buat Kontrabon Angkutan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/kontrabonangkutan/create">Buat Kontrabon Anngkutan</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <!-- Data list view starts -->
        <!-- DataTable starts -->
        @include('layouts.notification')
        <form action="/kontrabonangkutan/store" method="post" id="frm">
            @csrf
            <input type="hidden" id="cektemp">
            <div class="row">
                <div class="col-lg-3 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext field="no_kontrabon" label="Auto" icon="fa fa-barcode" readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <x-inputtext field="tgl_kontrabon" label="Tanggal Kontrabon" icon="feather icon-calendar" datepicker />
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
                                            <option value="CAHAYA BIRU">ANGKUTAN CAHAYA BIRU</option>

                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="no_surat_jalan" id="no_surat_jalan" class="form-control select2">
                                            <option value="">Pilih Surat Jalan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-hover-animation">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No. SJ</th>
                                        <th>Tanggal</th>
                                        <th>Tujuan</th>
                                        <th>Angkutan</th>
                                        <th>Tarif</th>
                                        <th>Tepung</th>
                                        <th>BS</th>
                                        <th>Total</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody id="loaddetailkontrabon">
                                </tbody>
                            </table>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block" type="submit" name="submit"><i class="fa fa-send mr-1"></i>Submit</button>
                                    </div>
                                </div>
                            </div>

                            <!-- DataTable ends -->
                        </div>
                    </div>
                </div>
            </div>

        </form>

        <!-- Data list view end -->
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        function loadnosuratjalan() {
            $("#no_surat_jalan").load("/kontrabonangkutan/getnosuratjalan");
        }
        loadnosuratjalan();

        function loaddetail() {
            $("#loaddetailkontrabon").load("/kontrabonangkutan/showtemp");
            cektemp();
        }

        loaddetail();

        function cektemp() {
            $.ajax({
                type: 'POST'
                , url: '/kontrabonangkutan/cektemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });
        }
        $("#no_surat_jalan").change(function() {
            var no_surat_jalan = $("#no_surat_jalan").val();
            $.ajax({
                type: 'POST'
                , url: '/kontrabonangkutan/storetemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_surat_jalan: no_surat_jalan
                }
                , cache: false
                , success: function(respond) {
                    if (respond == 1) {
                        swal("Oops", "Data Sudah Ada", "warning");
                    } else if (respond == 2) {
                        swal("Oops", "Data Gagal Disimpan, Hubungi Tim IT", "warning");
                    } else if (respond == 0) {
                        swal("Berhasil", "Data Berhasil Disimpan", "success");
                    }
                    loaddetail();
                    loadnosuratjalan();
                }
            });
        });

        $("#frm").submit(function() {
            var tgl_kontrabon = $("#tgl_kontrabon").val();
            var angkutan = $("#angkutan").val();
            var cektemp = $("#cektemp").val();
            if (tgl_kontrabon == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_kontrabon").focus();
                });
                return false;
            } else if (angkutan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Angkutan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#angkutan").focus();
                });
                return false;
            } else if (cektemp == "" || cektemp == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data Masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_surat_jalan").focus();
                });
                return false;
            }


        });
    });

</script>
@endpush
