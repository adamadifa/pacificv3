@extends('layouts.midone')
@section('titlepage', 'Data Transit IN')
@section('content')
   <div class="content-wrapper">
      <div class="content-header row">
         <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
               <div class="col-12">
                  <h2 class="content-header-title float-left mb-0">Data Transit IN</h2>
                  <div class="breadcrumb-wrapper col-12">
                     <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/mutasigudangcabang/transitin">Data Transit IN</a>
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
                  <div class="col-md-12 col-sm-12 col-lg-7">
                     <div class="card">
                        <div class="card-body">
                           <form action="/mutasigudangcabang/transitin">
                              <div class="row">
                                 <div class="col-12">
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
                                       <th>No. SJ</th>
                                       <th>Transit OUT</th>
                                       <th>Transit IN</th>
                                       <th>Aksi</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach ($transit as $d)
                                       <tr>
                                          <td><a href="#" no_mutasi_gudang="{{ Crypt::encrypt($d->no_suratjalan) }}" class="detail">{{ $d->no_suratjalan }}</a></td>
                                          <td><span class="badge bg-info">{{ !empty($d->tgl_transitout) ? date('d-m-Y', strtotime($d->tgl_transitout)) : '' }}</span></td>
                                          <td><span class="badge bg-success">{{ !empty($d->tgl_diterimacabang) ? date('d-m-Y', strtotime($d->tgl_diterimacabang)) : '' }}</span></td>
                                          <td>
                                             <div class="btn-group">
                                                @if (empty($d->tgl_diterimacabang))
                                                   <a href="#" no_mutasi_gudang_cabang="{{ Crypt::encrypt($d->no_mutasi_gudang_cabang) }}" class="ml-1 approve"><i
                                                         class="feather icon-external-link success"></i></a>
                                                @else
                                                   <form method="POST" class="deleteform" action="/mutasigudangcabang/transitin/{{ Crypt::encrypt($d->no_suratjalan) }}/batal">
                                                      @csrf
                                                      @method('DELETE')
                                                      <a href="#" tanggal="{{ $d->tgl_diterimacabang }}" class="delete-confirm-batal ml-1">
                                                         <i class="fa fa-close danger"></i>
                                                      </a>
                                                   </form>
                                                @endif
                                             </div>
                                          </td>
                                       </tr>
                                    @endforeach
                                 </tbody>
                              </table>
                              {{ $transit->links('vendor.pagination.vuexy') }}
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
   <!-- Approve Transit Out -->
   <div class="modal fade text-left" id="mdlapprove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myModalLabel18">Approve Transit IN</h4>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div id="loadapprove"></div>
            </div>
         </div>
      </div>
   </div>
   <!-- Detail Surat Jalan -->
   <div class="modal fade text-left" id="mdldetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title" id="myModalLabel18">Detail Surat Jalan</h4>
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
@endsection

@push('myscript')
   <script>
      $(function() {
         function loaddetail(no_mutasi_gudang) {
            $("#loaddetail").load("/suratjalan/" + no_mutasi_gudang + "/show");
         }
         $('.detail').click(function(e) {
            e.preventDefault();
            var no_mutasi_gudang = $(this).attr("no_mutasi_gudang");
            $('#mdldetail').modal({
               backdrop: 'static',
               keyboard: false
            });
            loaddetail(no_mutasi_gudang);

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
         $('.delete-confirm-batal').click(function(event) {
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

         $(".approve").click(function(e) {
            var no_mutasi_gudang_cabang = $(this).attr("no_mutasi_gudang_cabang");
            e.preventDefault();
            $("#loadapprove").load("/mutasigudangcabang/transitin/" + no_mutasi_gudang_cabang + "/create");
            $('#mdlapprove').modal({
               backdrop: 'static',
               keyboard: false
            });
         });
      });
   </script>
@endpush
