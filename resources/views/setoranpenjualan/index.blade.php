@extends('layouts.midone')
@section('titlepage','Setoran Penjualan')
@section('content')

<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Setoran Penjualan</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/setoranpenjualan">Setoran Penjualan</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <input type="hidden" id="cektutuplaporan">
        <div class="col-md-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <a href="#" class="btn btn-primary" id="inputsetoranpenjualan"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                </div>
                <div class="card-body">
                    <form action="/setoranpenjualan" id="frmcari">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <x-inputtext label="Dari" field="dari" icon="feather icon-calendar" datepicker value="{{ Request('dari') }}" />
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <x-inputtext label="Sampai" field="sampai" icon="feather icon-calendar" datepicker value="{{ Request('sampai') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-12">
                                <div class="form-group  ">
                                    <select name="kode_cabang" id="kode_cabang" class="form-control">
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option {{ (Request('kode_cabang')==$c->kode_cabang ? 'selected':'')}} value="{{
                                            $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group  ">
                                    <select name="id_karyawan" id="id_karyawan" class="form-control">
                                        <option value="">Semua Salesman</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <button type="submit" name="submit" value="1" class="btn btn-primary"><i class="fa fa-search"></i> Cari Data </button>
                            </div>
                        </div>
                    </form>
                    @include('layouts.notification')
                    <a href="/setoranpenjualan/cetak?dari={{ Request('dari') }}&sampai={{ Request('sampai') }}&kode_cabang={{ Request('kode_cabang') }}&id_karyawan={{ Request('id_karyawan') }}&excel=false" target="_blank" class="btn btn-primary"><i class="feather icon-printer"></i></a>
                    <a href="/setoranpenjualan/cetak?dari={{ Request('dari') }}&sampai={{ Request('sampai') }}&kode_cabang={{ Request('kode_cabang') }}&id_karyawan={{ Request('id_karyawan') }}&excel=true" class="btn btn-success"><i class="feather icon-download"></i></a>
                    <table class="table table-bordered table-hover-animation mt-2">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center bg-primary text-white">Tgl Lhp</th>
                                <th rowspan="2" class="text-center bg-primary text-white">Salesman</th>
                                <th colspan="2" class="text-center bg-info text-white">Penjualan</th>
                                <th rowspan="2" class="text-center bg-info text-white">Total LHP</th>
                                <th colspan="5" class="text-center bg-warning text-white">Setoran</th>
                                <th rowspan="2" class="text-center bg-warning text-white">Total Setoran</th>
                                <th rowspan="2" class="text-center bg-primary text-white">Aksi</th>
                            </tr>
                            <tr>
                                <th class="text-center bg-info text-white">Tunai</th>
                                <th class="text-center bg-info text-white">Tagihan</th>
                                <th class="text-center bg-warning text-white">U. Kertas</th>
                                <th class="text-center bg-warning text-white">U. Logam</th>
                                <th class="text-center bg-warning text-white">BG/CEK</th>
                                <th class="text-center bg-warning text-white">Transfer</th>
                                <th class="text-center bg-warning text-white">Lainnya</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $totaltunai = 0;
                            $totaltagihan = 0;
                            $totallhppertgl = 0;
                            $totalsetorankertas = 0;
                            $totalsetoranlogam = 0;
                            $totalsetoranbg = 0;
                            $totalsetorantransfer = 0;
                            $totalsetoranlainnya = 0;
                            $totalsetoranpertgl = 0;

                            @endphp
                            @foreach ($setoranpenjualan as $key => $d)
                            @php
                            $totaltunai = $totaltunai + $d->lhp_tunai;
                            $totaltagihan = $totaltagihan +$d->lhp_tagihan;
                            $tglcek = @$setoranpenjualan[$key + 1]->tgl_lhp;
                            $tanggal = explode("-", $d->tgl_lhp);
                            $ceksetorantunai = $d->cektunai;
                            $setorantagihan = $d->cekkredit;
                            $setorangiro = $d->ceksetorangiro;
                            $setorantransfer = $d->ceksetorantransfer;
                            $setoranalltagihan = $d->cekkredit + $d->ceksetorangiro + $d->ceksetorantransfer;
                            $girotocash = $d->cekgirotocash;
                            $convertgiro = $d->girotocash + $d->girototransfer;
                            //echo $girotocash;
                            //Penyelesaian Kurang lebih Setor
                            $uk = $d->kurangsetorkertas - $d->lebihsetorkertas;
                            $ul = $d->kurangsetorlogam - $d->lebihsetorlogam;
                            $totallhp = $d->lhp_tunai + $d->lhp_tagihan;
                            if ($uk > 0) {
                            $opkertas = "+";
                            } else {
                            $opkertas = "+";
                            }

                            if ($ul > 0) {
                            $oplogam = "+";
                            } else {
                            $oplogam = "+";
                            }

                            $totalsetoran = $d->setoran_kertas + $uk + $d->setoran_logam + $ul + $d->setoran_bg + $d->setoran_transfer + $d->setoran_lainnya;
                            $selisih = $totalsetoran - $totallhp;
                            $kontenkertas = number_format($d->setoran_kertas, '0', '', '.') . $opkertas . number_format($uk, '0', '', '.');
                            $kontenlogam = number_format($d->setoran_logam, '0', '', '.') . $oplogam . number_format($ul, '0', '', '.');


                            if ($d->cektunai == $d->lhp_tunai) {
                            $colorsetorantunai = "bg-success text-white";
                            } else {
                            $colorsetorantunai = "bg-danger text-white";
                            }

                            if ($setoranalltagihan == $d->lhp_tagihan) {
                            $colorsetorantagihan = "bg-success text-white";
                            } else {
                            $colorsetorantagihan = "bg-danger text-white";
                            }

                            if ($d->cektunai == $d->lhp_tunai && $setoranalltagihan == $d->lhp_tagihan && $girotocash == $convertgiro) {
                            $colortotallhp = "bg-success text-white";
                            } else {
                            $colortotallhp = "bg-danger text-white";
                            }

                            $totallhppertgl = $totallhppertgl + $totallhp;
                            $totalsetorankertas = $totalsetorankertas + ($d->setoran_kertas + $uk);
                            $totalsetoranlogam = $totalsetoranlogam + ($d->setoran_logam + $ul);
                            $totalsetoranbg = $totalsetoranbg + $d->setoran_bg;
                            $totalsetorantransfer = $totalsetorantransfer + $d->setoran_transfer;
                            $totalsetoranlainnya = $totalsetoranlainnya + $d->setoran_lainnya;
                            $totalsetoranpertgl = $totalsetoranpertgl + $totalsetoran;

                            if($loop->iteration % 2){
                            $position = "right";
                            }else{
                            $position = "left";
                            }
                            @endphp
                            <tr>
                                <td>{{ date("d-m-Y",strtotime($d->tgl_lhp))  }}</td>
                                <td>{{ ucwords(strtolower($d->nama_karyawan)) }}</td>
                                <td class="{{ $colorsetorantunai }} text-right">{{ rupiah($d->lhp_tunai) }}</td>
                                <td class="{{ $colorsetorantagihan }} text-right">{{ rupiah($d->lhp_tagihan) }}</td>
                                <td class="{{ $colortotallhp }} text-right"><u><a class="text-white" target="_blank" href=" /setoranpenjualan/detailsetoran?kode_cabang={{ $d->kode_cabang }}&tgl_lhp={{ $d->tgl_lhp }}&id_karyawan={{ $d->id_karyawan }}">{{ rupiah($totallhp) }}</a></u></td>
                                <td class="text-right"><a href="#" class="detailkertas" data-toggle="popover" data-placement="{{ $position }}" data-container="body" data-original-title="Keterangan" data-content="{{ $kontenkertas }}">{{ !empty($d->setoran_kertas + $uk) ? rupiah($d->setoran_kertas + $uk) : '' }}</a></td>
                                <td class="text-right"><a href="#" class="detaillogam" data-toggle="popover" data-placement="{{ $position }}" data-container="body" data-original-title="Keterangan" data-content="{{ $kontenlogam }}">{{ !empty($d->setoran_logam + $ul) ? rupiah($d->setoran_logam + $ul) : '' }}</a></td>
                                <td class="text-right">{{ !empty($d->setoran_bg) ? rupiah($d->setoran_bg) : '' }}</td>
                                <td class="text-right">{{ !empty($d->setoran_transfer) ? rupiah($d->setoran_transfer) : '' }}</td>
                                <td class="text-right">{{ !empty($d->setoran_lainnya) ? rupiah($d->setoran_lainnya) : '' }}</td>
                                <td class="text-right">{{ rupiah($totalsetoran) }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <a href="#" class="info ml-1" data-toggle="popover" data-placement="{{ $position }}" data-container="body" data-original-title="Keterangan" data-content="{{ $d->keterangan }}"><i class="feather icon-info"></i></a>
                                        <a href="/setoranpenjualan/{{ Crypt::encrypt($d->kode_setoran) }}/synclhp" class="success ml-1"><i class="feather icon-refresh-ccw"></i></a>
                                        <a href="#" class="success ml-1 edit" kodesetoran="{{ Crypt::encrypt($d->kode_setoran) }}"><i class="feather icon-edit"></i></a>
                                        <form method="POST" class="deleteform" action="/setoranpenjualan/{{Crypt::encrypt($d->kode_setoran)}}/delete">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" tanggal="{{ $d->tgl_lhp }}" class="delete-confirm ml-1">
                                                <i class="feather icon-trash danger"></i>
                                            </a>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @php
                            if ($tglcek != $d->tgl_lhp) {

                            echo "<tr style='color:black; font-weight:bold'>
                                <th colspan='2'>TOTAL</th>
                                <th style='text-align:right'>" . number_format($totaltunai, '0', '', '.') . "</th>
                                <th style='text-align:right'>" . number_format($totaltagihan, '0', '', '.') . "</th>
                                <th style='text-align:right'>" . number_format($totallhppertgl, '0', '', '.') . "</th>
                                <th style='text-align:right'>" . number_format($totalsetorankertas, '0', '', '.') . "</th>
                                <th style='text-align:right'>" . number_format($totalsetoranlogam, '0', '', '.') . "</th>
                                <th style='text-align:right'>" . number_format($totalsetoranbg, '0', '', '.') . "</th>
                                <th style='text-align:right'>" . number_format($totalsetorantransfer, '0', '', '.') . "</th>
                                <th style='text-align:right'>" . number_format($totalsetoranlainnya, '0', '', '.') . "</th>
                                <th style='text-align:right'>" . number_format($totalsetoranpertgl, '0', '', '.') . "</th>
                                <th></th>
                            </tr>";

                            $totaltunai = 0;
                            $totaltagihan = 0;
                            $totallhppertgl = 0;
                            $totalsetorankertas = 0;
                            $totalsetoranlogam = 0;
                            $totalsetoranbg = 0;
                            $totalsetorantransfer = 0;
                            $totalsetoranpertgl = 0;
                            }
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Input Setoran Penjualan -->
<div class="modal fade text-left" id="mdlinputsetoranpenjualan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Input Setoran Penjualan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadinputsetoranpenjualan"></div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Setoran Penjualan -->
<div class="modal fade text-left" id="mdleditsetoranpenjualan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Edit Setoran Penjualan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="loadeditsetoranpenjualan"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')

<script>
    $(function() {
        var kode_cabang = $("#kode_cabang").val();
        loadsalesmancabang(kode_cabang);

        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "penjualan"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }
        $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            var tanggal = $(this).attr("tanggal");
            cektutuplaporan(tanggal);
            event.preventDefault();
            swal({
                    title: `Are you sure you want to delete this record?`
                    , text: "If you delete this, it will be gone forever."
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        var cektutuplaporan = $("#cektutuplaporan").val();
                        if (cektutuplaporan > 0) {
                            swal("Oops", "Laporan Periode Ini Sudah Di Tutup !", "warning");
                            return false;
                        } else {
                            form.submit();
                        }
                    }
                });
        });

        $(".detailkertas, .detaillogam, .info").click(function(e) {
            e.preventDefault();
        });

        function loadsalesmancabang(kode_cabang) {
            var id_karyawan = "{{ Request('id_karyawan') }}";
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , id_karyawan: id_karyawan
                }
                , cache: false
                , success: function(respond) {
                    $("#id_karyawan").html(respond);
                }
            });
        }

        $("#kode_cabang").change(function() {
            var kode_cabang = $(this).val();
            loadsalesmancabang(kode_cabang);
        });

        $("#inputsetoranpenjualan").click(function(e) {
            e.preventDefault();
            $('#mdlinputsetoranpenjualan').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadinputsetoranpenjualan").load("/setoranpenjualan/create");
        });

        $(".edit").click(function(e) {
            e.preventDefault();
            var kode_setoran = $(this).attr("kodesetoran");
            $('#mdleditsetoranpenjualan').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $("#loadeditsetoranpenjualan").load("/setoranpenjualan/" + kode_setoran + "/edit");
        });
    });

</script>
@endpush
