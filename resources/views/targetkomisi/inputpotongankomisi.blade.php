<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <tr>
                <th>ID Salesman</th>
                <td>
                    @if ($karyawan!=null)
                    @php
                    $id_karyawan = $karyawan->id_karyawan;
                    @endphp
                    @else
                    @php
                    $id_karyawan = $id_karyawan
                    @endphp
                    @endif
                    {{ $id_karyawan }}
                </td>
            </tr>
            <tr>
                <td>Nama Salesman</td>
                <td>
                    @if ($karyawan!=null)
                    @php
                    $nama_karyawan = $karyawan->nama_karyawan;
                    @endphp
                    @else
                    @php
                    $nama_karyawan = $kode == "SP" ? "SUPERVISOR" : "KEPALA PENJUALAN";
                    @endphp
                    @endif
                    {{ $nama_karyawan }}
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <input type="text" name="potongan" id="potongan" value="{{ $cek != null ? $cek->jumlah : '' }}" placeholder="Potongan" class="form-control text-end">
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
            <button class="btn btn-primary w-100" id="simpanpotongan">Simpan</button>
        </div>
    </div>
</div>
<script>
    $(function() {
        $("#simpanpotongan").click(function(e) {
            e.preventDefault();
            var id_karyawan = "{{ $karyawan != null ? $karyawan->id_karyawan : $id_karyawan }}";
            var bulan = "{{ $bulan }}";
            var tahun = "{{ $tahun }}";
            var keterangan = $("#keterangan").val();
            var potongan = $("#potongan").val();
            $.ajax({
                type: 'POST'
                , url: '/storepotongankomisi'
                , cache: false
                , data: {
                    _token: '{{ csrf_token() }}'
                    , id_karyawan: id_karyawan
                    , bulan: bulan
                    , tahun: tahun
                    , potongan: potongan
                    , keterangan: keterangan
                }
                , success: function(respond) {
                    if (respond == 0) {
                        alert('Data Tersimpan');
                        $("#exampleModal").modal("hide");
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
