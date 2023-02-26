<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <tr>
                <th>ID Salesman</th>
                <td>{{$karyawan != null ? $karyawan->id_karyawan : $id_karyawan }}</td>
            </tr>
            <tr>
                <td>Nama Salesman</td>
                <td>{{$karyawan != null ? $karyawan->nama_karyawan : 'KEPALA PENJUALAN' }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <input type="text" name="komisiakhir" id="komisiakhir" value="{{ $cek != null ? $cek->jumlah : '' }}" placeholder="Komisi Akhir" class="form-control text-end">
        </div>
    </div>
</div>
<div class="row mt-2">
    <div class="col-12">
        <textarea name="keterangan" id="keterangan" class="form-control" cols="30" rows="5" placeholder="Keterangan">{{ $cek != null ? $cek->keterangan : '' }}</textarea>
    </div>
</div>
<div class="row mt-2">
    <div class="col-12">
        <div class="form-group">
            <button class="btn btn-primary w-100" id="simpankomisiakhir">Simpan</button>
        </div>
    </div>
</div>
<script>
    $(function() {
        $("#simpankomisiakhir").click(function(e) {
            e.preventDefault();
            var id_karyawan = "{{ $karyawan != null ? $karyawan->id_karyawan : $id_karyawan }}";
            var bulan = "{{ $bulan }}";
            var tahun = "{{ $tahun }}";
            var keterangan = $("#keterangan").val();
            var komisiakhir = $("#komisiakhir").val();
            $.ajax({
                type: 'POST'
                , url: '/storekomisiakhir'
                , cache: false
                , data: {
                    _token: '{{ csrf_token() }}'
                    , id_karyawan: id_karyawan
                    , bulan: bulan
                    , tahun: tahun
                    , komisiakhir: komisiakhir
                    , keterangan: keterangan
                }
                , success: function(respond) {
                    if (respond == 0) {
                        alert('Data Tersimpan');
                        $("#mdlkomisiakhir").modal("hide");
                        location.reload();
                    } else {
                        alert('Data Gagal Tersimpan');
                    }
                }
            });
            //alert('test');
        });
    });

</script>
