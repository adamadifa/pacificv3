<div class="alert alert-danger">
    <h4 class="alert-heading"><i class="feather icon-info mr-1"></i> Warning !</h4>
    <p>Karyawan Tidak Dapat Mengajukan Pinjaman Karena Masih Memiliki Pinjaman Yang Belum Lunas, Untuk Melakukan Pinjaman Kembali Min. Sudah Membayar 75% dari Total Pinjaman Sebelumnya
        <br>
        <table class="table">
            <tr>
                <th>No. Pinjaman</th>
                <td>{{ $cekpinjaman->no_pinjaman }}</td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ $cekpinjaman->tgl_pinjaman }}</td>
            </tr>
            <tr>
                <th>Jumlah Pinjaman</th>
                <td style="text-align:right">{{ rupiah($cekpinjaman->jumlah_pinjaman) }}</td>
            </tr>
            <tr>
                <th>Total Pembayaran</th>
                <td style="text-align:right">{{ rupiah($cekpinjaman->totalpembayaran) }} ({{ $cekpinjaman->totalpembayaran / $cekpinjaman->jumlah_pinjaman * 100 }}%) </td>
            </tr>
        </table>
    </p>
</div>
