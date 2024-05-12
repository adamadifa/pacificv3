@extends('layouts.midone')
@section('titlepage', strtoupper($textjm))
@section('content')
   <div class="content-wrapper">
      <div class="content-header row">
         <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
               <div class="col-12">
                  <h2 class="content-header-title float-left mb-0">{{ strtoupper($textjm) }}</h2>
                  <div class="breadcrumb-wrapper col-12">
                     <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Data {{ strtoupper($textjm) }}</a>
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
         <div class="row">
            <div class="col-lg-9 col-sm-12 col-md-12">
               <div class="row">
                  <div class="col-md-12 col-sm-12 col-lg-12">
                     <div class="card">
                        <div class="card-header">
                           <a href="#" class="btn btn-primary" id="inputmutasi"><i class="fa fa-plus mr-1"></i> Tambah Data</a>
                        </div>
                        <div class="card-body">
                           <form action="{{ URL::current() }}">

                              <div class="row">
                                 <div class="col-lg-12 col-sm-12">
                                    <div class="form-group  ">
                                       <select name="kode_cabang" id="kode_cabang" class="form-control">
                                          @if (Auth::user()->kode_cabang != 'PCF')
                                             <option value="">Pilih Cabang</option>
                                          @else
                                             <option value="">Semua Cabang</option>
                                          @endif
                                          @foreach ($cabang as $c)
                                             <option {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }} value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                                          @endforeach
                                       </select>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-lg-6 col-md-12 col-sm-12">
                                    <x-inputtext label="Dari" field="dari" value="{{ Request('dari') }}" icon="feather icon-calendar" datepicker />
                                 </div>
                                 <div class="col-lg-6 col-md-12 col-sm-12">
                                    <x-inputtext label="Sampai" field="sampai" value="{{ Request('sampai') }}" icon="feather icon-calendar" datepicker />
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-12">
                                    <div class="form-group">
                                       <button class="btn btn-primary btn-block"><i class="fa fa-search mr-1"></i>Cari</button>
                                    </div>
                                 </div>
                              </div>
                           </form>
                           <div class="table-responsive">
                              <table class="table table-hover-animation">
                                 <thead class="thead-dark">
                                    <tr>
                                       <th>No. Mutasi</th>
                                       <th>Tgl {{ ucwords(strtolower($textjm)) }}</th>
                                       <th>Cabang</th>
                                       <th style="width: 55%">Keterangan</th>
                                       <th>IN/OUT</th>
                                       <th></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach ($mutasi as $d)
                                       <tr>
                                          <td>{{ $d->no_mutasi_gudang_cabang }}</td>
                                          <td>{{ date('d-m-Y', strtotime($d->tgl_mutasi_gudang_cabang)) }}</td>
                                          <td>{{ $d->kode_cabang }}</td>
                                          <td>{{ $d->keterangan }}</td>
                                          <td>
                                             @if ($d->jenis_mutasi == 'PENYESUAIAN')
                                                @if ($d->inout_good == 'IN')
                                                   <span class="badge bg-success">IN</span>
                                                @else
                                                   <span class="badge bg-danger">OUT</span>
                                                @endif
                                             @elseif($d->jenis_mutasi == 'PENYESUAIAN BAD')
                                                @if ($d->inout_bad == 'IN')
                                                   <span class="badge bg-success">IN</span>
                                                @else
                                                   <span class="badge bg-danger">OUT</span>
                                                @endif
                                             @endif
                                          </td>
                                          <td>
                                             <div class="btn-group">
                                                <a href="#" class="ml-1 edit" no_mutasi_gudang_cabang="{{ Crypt::encrypt($d->no_mutasi_gudang_cabang) }}"><i
                                                      class="feather icon-edit success"></i></a>
                                                <a href="#" class="ml-1 detail" no_mutasi_gudang_cabang="{{ Crypt::encrypt($d->no_mutasi_gudang_cabang) }}"><i
                                                      class="feather icon-file-text info"></i></a>
                                                <form method="POST" class="deleteform" action="/mutasigudangcabang/{{ Crypt::encrypt($d->no_mutasi_gudang_cabang) }}/delete">
                                                   @csrf
                                                   @method('DELETE')
                                                   <a href="#" tanggal="{{ $d->tgl_mutasi_gudang_cabang }}" class="delete-confirm ml-1">
                                                      <i class="feather icon-trash danger"></i>
                                                   </a>
                                                </form>
                                             </div>

                                          </td>
                                       </tr>
                                    @endforeach
                                 </tbody>
                              </table>
                              {{ $mutasi->links('vendor.pagination.vuexy') }}
                           </div>

                           <!-- DataTable ends -->
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-lg-3 col-sm-12">
               @include('layouts.nav_mutasi_gudangcabang')
            </div>
         </div>
         <!-- Data list view end -->
      </div>
   </div>
   <!-- Detail Surat Jalan -->
   <div class="modal fade text-left" id="mdldetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myModalLabel18">Detail {{ ucwords(strtolower($textjm)) }}</h4>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div id="loaddetail"></div>
            </div>
         </div>
      </div>
   </div>

   <div class="modal fade text-left" id="mdlinput" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myModalLabel18">Input {{ ucwords(strtolower($textjm)) }}</h4>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div id="loadinput"></div>
            </div>
         </div>
      </div>
   </div>
   <div class="modal fade text-left" id="mdledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myModalLabel18">Edit {{ ucwords(strtolower($textjm)) }}</h4>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div id="loadedit"></div>
            </div>
         </div>
      </div>
   </div>
@endsection
@push('myscript')
   <script>
      $(function() {
         function loaddetail(no_mutasi_gudang_cabang) {
            $("#loaddetail").load("/mutasigudangcabang/" + no_mutasi_gudang_cabang + "/showdetail");
         }
         $('.detail').click(function(e) {
            e.preventDefault();
            var no_mutasi_gudang_cabang = $(this).attr("no_mutasi_gudang_cabang");
            $('#mdldetail').modal({
               backdrop: 'static',
               keyboard: false
            });
            loaddetail(no_mutasi_gudang_cabang);

         });

         function loadedit(no_mutasi_gudang_cabang) {
            $("#loadedit").load("/mutasigudangcabang/" + no_mutasi_gudang_cabang + "/penyesuaianedit");
         }
         $('.edit').click(function(e) {
            e.preventDefault();
            var no_mutasi_gudang_cabang = $(this).attr("no_mutasi_gudang_cabang");
            $('#mdledit').modal({
               backdrop: 'static',
               keyboard: false
            });
            loadedit(no_mutasi_gudang_cabang);

         });

         function cektutuplaporan(tanggal) {
            $.ajax({
               type: "POST",
               url: "/cektutuplaporan",
               data: {
                  _token: "{{ csrf_token() }}",
                  tanggal: tanggal,
                  jenislaporan: "gudangcabang"
               },
               cache: false,
               success: function(respond) {
                  console.log(respond);
                  $("#cektutuplaporan").val(respond);
               }
            });
         }

         $("#inputmutasi").click(function(e) {
            e.preventDefault();
            var jenis_mutasi = "{{ $jenis_mutasi }}";
            $("#loadinput").load("/mutasigudangcabang/" + jenis_mutasi + "/penyesuaiancreate");
            $('#mdlinput').modal({
               backdrop: 'static',
               keyboard: false
            });
         });


         $('.delete-confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            var tanggal = $(this).attr("tanggal");
            cektutuplaporan(tanggal);
            event.preventDefault();
            swal({
                  title: `Apakah Kamu Yakin Akan Membatalkan Data Ini ?`,
                  text: "Jika Kamu Batalkan, Maka Data Ini Akan dikembalikan seperti semula.",
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
               })
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
      });
   </script>
@endpush
