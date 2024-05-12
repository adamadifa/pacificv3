<form action="/mutasigudangcabang/transitin/{{ Crypt::encrypt($mutasicab->no_mutasi_gudang_cabang) }}/store" method="POST" id="frmTransitin">
   @csrf
   <div class="row">
      <div class="col-12">
         <table class="table">
            <tr>
               <td>No. Surat Jalan</td>
               <td>
                  {{ $mutasi->no_mutasi_gudang }}
               </td>
            </tr>
            <tr>
               <td>Tgl SJ</td>
               <td>{{ DateToIndo2($mutasi->tgl_mutasi_gudang) }}</td>
            </tr>
            <tr>
               <td>Cabang</td>
               <td>{{ strtoupper($mutasi->nama_cabang) }}</td>
            </tr>
            <tr>
               <td>Keterangan</td>
               <td>{{ $mutasi->keterangan }}</td>
            </tr>
            <tr>
               <td>Status</td>
               <td>
                  @if ($mutasi->status_sj == 0)
                     <span class="badge bg-danger">Belum Diterima Cabang</span>
                  @elseif($mutasi->status_sj == 1)
                     <span class="badge bg-success">Sudah Diterima Cabang</span>
                  @elseif($mutasi->status_sj == 2)
                     <span class="badge bg-info">Transit Out</span>
                  @endif

               </td>
            </tr>
         </table>
      </div>
   </div>
   <div class="row">
      <div class="col-12">
         <table class="table table-hover-animation">
            <thead>
               <tr>
                  <th>Kode Produk</th>
                  <th>Nama Barang</th>
                  <th>Jumlah</th>
               </tr>
            </thead>
            <tbody>
               @foreach ($detail as $d)
                  <tr>
                     <td>{{ $d->kode_produk }}</td>
                     <td>{{ $d->nama_barang }}</td>
                     <td class="text-right">{{ rupiah($d->jumlah) }}</td>
                  </tr>
               @endforeach
            </tbody>
         </table>
      </div>
   </div>
   <div class="row">
      <divi class="col-12">
         <x-inputtext label="Tanggal Transit IN" field="tgl_mutasi_gudang_cabang" icon="feather icon-calendar" datepicker />
      </divi>
   </div>
   <div class="row">
      <div class="col-12">
         <div class="form-gropu">
            <button class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
         </div>
      </div>
   </div>
</form>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script>
   $(function() {
      $("#frmTransitin").submit(function() {
         var tgl_mutasi_gudang_cabang = $("#tgl_mutasi_gudang_cabang").val();
         if (tgl_mutasi_gudang_cabang == "") {
            swal({
               title: 'Oops',
               text: 'Tanggal Harus Diisi !',
               icon: 'warning',
               showConfirmButton: false
            }).then(function() {
               $("#tgl_mutasi_gudang_cabang").focus();
            });
            return false;
         }
      });

   });
</script>
