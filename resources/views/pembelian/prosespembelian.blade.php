<form action="/pembelian/{{ Crypt::encrypt($pembelian->nobukti_pembelian) }}/storeprosespembelian" method="POST"
    id="frmProsespembelian">
    @csrf
    <table class="table table-bordered">
        <tr>
            <td>No. Bukti</td>
            <td>{{ $pembelian->nobukti_pembelian }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>{{ DateToIndo2($pembelian->tgl_pembelian) }}</td>
        </tr>
        <tr>
            <td>Supplier</td>
            <td>{{ $pembelian->kode_supplier }} - {{ $pembelian->nama_supplier }}</td>
        </tr>
        <tr>
            <td>Departemen</td>
            <td>{{ $pembelian->nama_dept }}</td>
        </tr>
        <tr>
            <td>PPN</td>
            <td class="success">
                @if (!empty($pembelian->ppn))
                    <i class="fa fa-check"></i> {{ $pembelian->no_fak_pajak }}
                @endif
            </td>
        </tr>
    </table>
    <table class="table table-hover-animation">
        <thead class="thead-dark">
            <tr>
                <th colspan="9">Data Pembelian</th>
            </tr>
            <tr>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kode Akun</th>
                <th>Keterangan</th>
                <th>Qty</th>
                @if (Auth::user()->level != 'admin gudang logistik')
                    <th>Harga</th>
                    <th>Subtotal</th>
                    <th>Penyesuaian</th>
                    <th>Total</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php
                $totalpembelian = 0;
            @endphp
            @foreach ($detailpembelian as $d)
                @php
                    $total = $d->qty * $d->harga + $d->penyesuaian;
                    $totalpembelian += $total;
                @endphp
                <tr>
                    <td>{{ $d->kode_barang }}</td>
                    <td>{{ $d->nama_barang }}</td>
                    <td>{{ $d->kode_akun }} - {{ $d->nama_akun }}</td>
                    <td>{{ $d->keterangan }}</td>
                    <td class="text-center">{{ desimal($d->qty) }}</td>
                    @if (Auth::user()->level != 'admin gudang logistik')
                        <td class="text-right">{{ desimal($d->harga) }}</td>
                        <td class="text-right">{{ desimal($d->harga * $d->qty) }}</td>
                        <td class="text-right">{{ desimal($d->penyesuaian) }}</td>
                        <td class="text-right">{{ desimal($total) }}</td>
                    @endif
                </tr>
            @endforeach
            @if (Auth::user()->level != 'admin gudang logistik')
                <tr class="thead-dark">
                    <th colspan="8">TOTAL</th>
                    <th class="text-righ">{{ desimal($totalpembelian) }}</th>
                </tr>
            @endif
        </tbody>
    </table>
    <input type="hidden" name="nobukti_pembelian" value="{{ $pembelian->nobukti_pembelian }}">
    <div class="row">
        <div class="col-12">
            <x-inputtext field="tgl_pemasukan" label="Tanggal Approve" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script>
    $(function() {
        $("#frmProsespembelian").submit(function() {
            var tgl_pemasukan = $("#tgl_pemasukan").val();

            var cektutuplaporan = $("#cektutuplaporan").val();
            if (cektutuplaporan == 1) {
                swal({
                    title: 'Oops',
                    text: 'Laporan Sudah Di Tutup !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tgl_pemasukan").focus();
                });
                return false;
            } else if (tgl_pemasukan == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Pemasukan Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tgl_pemasukan").focus();
                });
                return false;
            }
        });

        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST",
                url: "/cektutuplaporan",
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggal: tanggal,
                    jenislaporan: "gudanglogistik"
                },
                cache: false,
                success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }

        $("#tgl_pemasukan").change(function() {
            var tgl_pemasukan = $("#tgl_pemasukan").val();
            cektutuplaporan(tgl_pemasukan);
        });


    });
</script>
