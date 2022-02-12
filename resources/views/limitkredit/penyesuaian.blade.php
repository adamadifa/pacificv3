<form action="/limitkredit/updatelimit" method="POST">
    @csrf
    <input type="hidden" name="no_pengajuan" value="{{ $no_pengajuan }}">
    <input type="hidden" name="jatuhtempo" value="{{ $limitkredit->jatuhtempo }}">
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Penambah / Pengurang" field="jumlah_rekomendasi" icon="feather icon-file" value="{{ rupiah($limitkredit->jumlah_rekomendasi) }}" right />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="jatuhtempo_rekomendasi" id="jatuhtempo_rekomendasi" class="form-control">
                    <option value="">Jatuh Tempo</option>
                    <option @if ($limitkredit->jatuhtempo==14)
                        selected
                        @endif value="14">14</option>
                    <option @if ($limitkredit->jatuhtempo==30)
                        selected
                        @endif value="30">30</option>
                    <option @if ($limitkredit->jatuhtempo==45)
                        selected
                        @endif value="45">45</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fa fa-info mr-2"></i>Masukan Jumlah Penambah / Pengurang (Tambahkan Minus (-) Untuk Pengurang)
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block"><i class="feather icon-send"></i> Submit</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        $("#jumlah_rekomendasi").maskMoney();
    });

</script>
