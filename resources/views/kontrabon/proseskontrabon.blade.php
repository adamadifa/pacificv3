<table class="table">
    <tr>
        <td>No. Kontrabon</td>
        <td>{{ $kontrabon->no_kontrabon }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ DateToIndo2($kontrabon->tgl_kontrabon) }}</td>
    </tr>
    <tr>
        <td>Terima Dari</td>
        <td>{{ $kontrabon->nama_supplier }}</td>
    </tr>
</table>
<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>No. Bukti</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @php
        $totalkontrabon = 0;
        @endphp
        @foreach ($detailkontrabon as $d)
        @php
        $totalkontrabon += $d->jmlbayar;
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ date("d-m-Y",strtotime($d->tgl_pembelian)) }}</td>
            <td><a href="#" class="detailpembelian" nobukti_pembelian="{{ $d->nobukti_pembelian }}">{{ $d->nobukti_pembelian }}</a></td>
            <td class="text-right">{{ desimal($d->jmlbayar) }}</td>
        </tr>
        @endforeach
        <tr class="thead-dark">
            <th colspan="3">TOTAL</th>
            <th class="text-right">{{ desimal($totalkontrabon) }}</th>
        </tr>
    </tbody>
</table>
<table class="table table-hover-animation">
    <thead class="thead-dark">
        <tr>
            <th colspan="9">Data Pembelian <span id="nobuktipembelian"></span></th>
        </tr>
        <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Keterangan</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Subtotal</th>
            <th>Penyesuaian</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody id="loaddetailpembelian"></tbody>
</table>
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Tanggal Bayar" field="tglbayar" icon="feather icon-calendar" datepicker />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select name="kode_bank" id="kode_bank" class="form-control select2">
                        <option value="">Pilih Bank</option>
                        @foreach ($bank as $d)
                        <option value="{{ $d->kode_bank }}">{{ $d->nama_bank }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select class="form-control" id="kode_akun" name="kode_akun" data-error=".errorTxt1">
                        <option value="">Pilih Akun</option>
                        <option value="2-1300">Hutang Lainnya</option>
                        <option value="2-1200">Hutang Dagang</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-12">
                <div class="vs-checkbox-con vs-checkbox-primary">
                    <input type="checkbox" class="cekcabang" name="cekcabang" value="1">
                    <span class="vs-checkbox">
                        <span class="vs-checkbox--check">
                            <i class="vs-icon feather icon-check"></i>
                        </span>
                    </span>
                    <span class="">Dibayar Oleh Cabang ?</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Keterangan" field="keterangan" icon="fa fa-file" />
            </div>
        </div>
    </div>
</div>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    $(function() {
        function loaddetailpembelian(nobukti_pembelian) {
            $.ajax({
                type: 'POST'
                , url: '/pembelian/showdetailpembeliankontrabon'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , nobukti_pembelian: nobukti_pembelian
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailpembelian").html(respond);
                }
            });
        }

        $(".detailpembelian").click(function() {
            var nobukti_pembelian = $(this).attr("nobukti_pembelian");
            $("#nobuktipembelian").text(nobukti_pembelian);
            loaddetailpembelian(nobukti_pembelian);
        });

    });

</script>
