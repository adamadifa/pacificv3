@if (in_array($level, $laporan_gudang_logistik))
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title">Gudang Logistik</h4>
            </div>
            <ul class="list-group list-group-flush">
                @if (in_array($level, $laporan_pemasukan_gl))
                    <a href="/laporangudanglogistik/pemasukan" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudanglogistik/pemasukan']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laporan Pemasukan Barang
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_pengeluaran_gl))
                    <a href="/laporangudanglogistik/pengeluaran" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudanglogistik/pengeluaran']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laporan Pengeluaran Barang
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_persediaan_gl))
                    <a href="/laporangudanglogistik/persediaan" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudanglogistik/persediaan']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laporan Persediaan Barang
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_persediaanopname_gl))
                    <a href="/laporangudanglogistik/persediaanopname" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudanglogistik/persediaanopname']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laporan Persediaan Opname
                        </li>
                    </a>
                @endif
            </ul>
        </div>
    </div>
@endif
@if (in_array($level, $laporan_gudang_bahan))
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title">Gudang Bahan</h4>
            </div>
            <ul class="list-group list-group-flush">
                @if (in_array($level, $laporan_pemasukan_gb))
                    <a href="/laporangudangbahan/pemasukan" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangbahan/pemasukan']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laporan Pemasukan Barang
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_pengeluaran_gb))
                    <a href="/laporangudangbahan/pengeluaran" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangbahan/pengeluaran']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laporan Pengeluaran Barang
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_persediaan_gb))
                    <a href="/laporangudangbahan/persediaan" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangbahan/persediaan']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laporan Persediaan Barang
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_kartugudang))
                    <a href="/laporangudangbahan/kartugudang" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangbahan/kartugudang']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Kartu Gudang Bahan / Kemasan
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_rekappersediaan))
                    <a href="/laporangudangbahan/rekappersediaan" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangbahan/rekappersediaan']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Rekap Persediaan
                        </li>
                    </a>
                @endif

            </ul>
        </div>
    </div>
@endif
@if (in_array($level, $laporan_gudang_jadi))
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title">Gudang Jadi</h4>
            </div>
            <ul class="list-group list-group-flush">
                @if (in_array($level, $laporan_persediaan_gj))
                    <a href="/laporangudangjadi/persediaan" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangjadi/persediaan']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laporan Persediaan
                        </li>
                    </a>
                @endif
                @if (in_array($level, $rekap_persediaan_gj))
                    <a href="/laporangudangjadi/rekappersediaan" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangjadi/rekappersediaan']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Rekap Persediaan
                        </li>
                    </a>
                @endif
                @if (in_array($level, $rekap_hasiproduksi_gj))
                    <a href="/laporangudangjadi/rekaphasilproduksi" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangjadi/rekaphasilproduksi']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Rekap Hasil Produksi
                        </li>
                    </a>
                @endif
                @if (in_array($level, $rekap_pengeluaran_gj))
                    <a href="/laporangudangjadi/rekappengeluaran" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangjadi/rekappengeluaran']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Rekap Pengeluaran
                        </li>
                    </a>
                @endif
                @if (in_array($level, $realisasi_kiriman_gj))
                    <a href="/laporangudangjadi/realisasikiriman" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangjadi/realisasikiriman']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Realisasi Kiriman
                        </li>
                    </a>
                @endif
                @if (in_array($level, $realisasi_oman_gj))
                    <a href="/laporangudangjadi/realisasioman" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangjadi/realisasioman']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Realisasi OMAN
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_angkutan))
                    <a href="/laporangudangjadi/angkutan" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangjadi/angkutan']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laporan Angkutan
                        </li>
                    </a>
                @endif
            </ul>
        </div>
    </div>
@endif
@if (in_array($level, $laporan_gudang_cabang))
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title">Gudang Cabang</h4>
            </div>
            <ul class="list-group list-group-flush">
                @if (Auth::user()->id == '35')
                    <a href="/laporanpenjualan/penjualan" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporanpenjualan/penjualan']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Penjualan
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_persediaan_bj))
                    <a href="/laporangudangcabang/persediaan" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangcabang/persediaan']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laporan Persediaan
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_badstok_bj))
                    <a href="/laporangudangcabang/badstok" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangcabang/badstok']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laporan Badstok
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_rekap_bj))
                    <a href="/laporangudangcabang/rekapbj" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangcabang/rekapbj']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Rekap Persediaan
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_mutasidpb))
                    <a href="/laporangudangcabang/mutasidpb" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangcabang/mutasidpb']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Mutasi DPB
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_rekonsiliasibj))
                    <a href="/laporangudangcabang/rekonsiliasibj" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporangudangcabang/rekonsiliasibj']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Rekonsiliasi BJ
                        </li>
                    </a>
                @endif
                <a href="/worksheetom/monitoringretur" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['worksheetom/monitoringretur']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Monitoring Retur
                    </li>
                </a>

            </ul>
        </div>
    </div>
@endif
