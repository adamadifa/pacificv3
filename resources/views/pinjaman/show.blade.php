<div class="row">
    <div class="col-6">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <tr>
                        <th>No. Pinjaman</th>
                        <td>{{ $pinjaman->no_pinjaman }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ DateToIndo2($pinjaman->tgl_pinjaman) }}</td>
                    </tr>
                    <tr>
                        <th>Nik</th>
                        <td>{{ $pinjaman->nik }}</td>
                    </tr>
                    <tr>
                        <th>Nama Karyawan</th>
                        <td>{{ $pinjaman->nama_karyawan }}</td>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <td>{{ $pinjaman->nama_jabatan }}</td>
                    </tr>
                    <tr>
                        <th>Departemen</th>
                        <td>{{ $pinjaman->nama_dept }}</td>
                    </tr>
                    <tr>
                        <th>Masa Kerja</th>
                        <td>
                            @php
                            $awal = date_create($pinjaman->tgl_masuk);
                            $akhir = date_create(date($pinjaman->tgl_pinjaman)); // waktu sekarang
                            $diff = date_diff( $awal, $akhir );
                            echo $diff->y . ' tahun, '.$diff->m.' bulan, '.$diff->d.' Hari'
                            @endphp
                        </td>
                    </tr>
                    <tr>
                        <th>Kantor</th>
                        <td>{{ $pinjaman->nama_cabang }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="#" class="btn btn-primary mb-1">Input Bayar</a>
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>No. Bukti</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <tr>
                        <th>Gaji Pokok + Tunjangan</th>
                        <td style="text-align: right">{{ rupiah($pinjaman->gapok_tunjangan) }}</td>
                    </tr>
                    <tr>
                        <th>Tenor Max</th>
                        <td style="text-align: right">{{ rupiah($pinjaman->tenor_max) }} Bulan</td>
                    </tr>
                    <tr>
                        <th>Angsuran Max</th>
                        <td style="text-align: right">{{ rupiah($pinjaman->angsuran_max) }}</td>
                    </tr>
                    <tr>
                        <th>Jasa Masa Kerja (JMK)</th>
                        <td style="text-align: right">{{ rupiah($pinjaman->jmk) }}</td>
                    </tr>
                    <tr>
                        <th>JMK Sudah DiBayar</th>
                        <td style="text-align: right">{{ rupiah($pinjaman->jmk_sudahbayar) }}</td>
                    </tr>
                    <tr>
                        <th>Plafon Max</th>
                        <td style="text-align: right">{{ rupiah($pinjaman->plafon_max) }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Pinjaman</th>
                        <td style="text-align: right; font-weight:bold">{{ rupiah($pinjaman->jumlah_pinjaman) }}</td>
                    </tr>
                    <tr>
                        <th>Angsuran</th>
                        <td>{{ $pinjaman->angsuran }} Bulan</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Cicilan Ke</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody id="loadrencanabayar"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        function loadrencanabayar() {
            var no_pinjaman = "{{ $pinjaman->no_pinjaman }}";
            $.ajax({
                type: 'POST'
                , url: '/pinjaman/getrencanabayar'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman: no_pinjaman
                }
                , cache: false
                , success: function(respond) {
                    $("#loadrencanabayar").html(respond);
                }
            });
        }

        loadrencanabayar();
    });

</script>
