<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Laporan</h4>
        </div>
        <ul class="list-group list-group-flush">
            @if (in_array($level,$laporan_mutasiproduksi))
            <a href="/laporanproduksi/mutasiproduksi" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanproduksi/mutasiproduksi']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Mutasi Produksi
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_rekapmutasiproduksi))
            <a href="/laporanproduksi/rekapmutasiproduksi" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanproduksi/rekapmutasiproduksi']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Mutasi Produksi
                </li>
            </a>
            @endif

            @if (in_array($level,$laporan_pemasukanproduksi))
            <a href="/laporanproduksi/pemasukanproduksi" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanproduksi/pemasukanproduksi']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Pemasukan Barang
                </li>
            </a>
            @endif

            @if (in_array($level,$laporan_pengeluaranproduksi))
            <a href="/laporanproduksi/pengeluaranproduksi" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian/pengeluaranproduksi']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Pengeluaran Barang
                </li>
            </a>
            @endif

            @if (in_array($level,$laporan_rekappersediaanbarangproduksi))
            <a href="/laporanproduksi/rekappersediaanbarangproduksi" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanproduksi/rekappersediaanbarangproduksi']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Persediaan Barang
                </li>
            </a>
            @endif



        </ul>
    </div>
</div>
