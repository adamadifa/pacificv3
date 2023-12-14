<style>
    ul.ui-autocomplete {
        z-index: 1100;
    }
</style>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th rowspan="2">Kode Produk</th>
                    <th rowspan="2">Nama Barang</th>
                    <th colspan="3" style="text-align: center">Retur</th>
                    <th colspan="3" style="text-align: center">Pelunasan</th>
                </tr>
                <tr>
                    <th class="text-center">Dus</th>
                    <th class="text-center">Pack</th>
                    <th class="text-center">Pcs</th>
                    <th class="text-center">Dus</th>
                    <th class="text-center">Pack</th>
                    <th class="text-center">Pcs</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalpf = 0;
                    $totalgb = 0;
                    $total = 0;
                @endphp
                @foreach ($detail as $d)
                    @php
                        $jmldus = floor($d->jumlah / $d->isipcsdus);
                        $sisadus = $d->jumlah % $d->isipcsdus;

                        if ($d->isipack == 0) {
                            $jmlpack = 0;
                            $sisapack = $sisadus;
                        } else {
                            $jmlpack = floor($sisadus / $d->isipcs);
                            $sisapack = $sisadus % $d->isipcs;
                        }

                        $jmlpcs = $sisapack;

                        $total += $d->subtotal;

                    @endphp
                    <tr>

                        <td>{{ $d->kode_produk }}</td>
                        <td>{{ $d->nama_barang }}</td>
                        <td class="text-center">{{ $jmldus }}</td>
                        <td class="text-center">{{ $jmlpack }}</td>
                        <td class="text-center">{{ $jmlpcs }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach

            </tbody>
        </table>

    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <select name="" id="" class="form-control">
                        <option value="">Pilih Barang</option>
                        @foreach ($detail as $d)
                            <option value="{{ $d->kode_barang }}">{{ $d->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-2">
                <x-inputtext label="Dus" field="dus" icon="feather icon-file" right />
            </div>
            <div class="col-2">
                <x-inputtext label="Pack" field="pack" icon="feather icon-file" right />
            </div>
            <div class="col-2">
                <x-inputtext label="Pcs" field="pcs" icon="feather icon-file" right />
            </div>
            <div class="col-3">
                <x-inputtext field="no_dpb" label="Ketikan No. DPB " icon="fa fa-barcode" />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary w-100"><i class="feather icon-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</div>
<div class="row mt-2">
    <div class="col-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th rowspan="2">Kode Produk</th>
                    <th rowspan="2">Nama Barang</th>
                    <th colspan="3" style="text-align: center">Retur</th>
                    <th rowspan="2">No. DPB</th>
                </tr>
                <tr>
                    <th class="text-center">Dus</th>
                    <th class="text-center">Pack</th>
                    <th class="text-center">Pcs</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
<script>
    $(function() {
        $("#no_dpb").autocomplete({
            source: function(request, response) {
                // Fetch data
                $.ajax({
                    url: "/getautocompletedpb",
                    type: 'post',
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        search: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                $("#frmDpb").find("#no_dpb").val(ui.item.label);
                $("#no_dpb_val").val(ui.item.val);
                var no_dpb = ui.item.val;
                loaddpb(no_dpb);
                return false;
            }
        });
    });
</script>
