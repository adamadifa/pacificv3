@if ($level != 'salesman')
    @if ($level == 'kepala gudang' || $level == 'spv gudang pusat')
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <h4 class="card-title">Laporan Penjualan</h4>
                    <ul class="list-group list-group-flush">
                        <a href="/laporanpenjualan/rekappenjualan" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/rekappenjualan']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Rekap Penjualan
                            </li>
                        </a>
                    </ul>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <h4 class="card-title">Laporan Penjualan</h4>
                </div>
                <ul class="list-group list-group-flush">
                    @if (Auth::user()->level != 'admin medsos')
                        <a href="/laporanpenjualan/penjualan" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/penjualan']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Penjualan
                            </li>
                        </a>
                    @endif
                    @if (Auth::user()->level != 'staff keuangan 2' and Auth::user()->level != 'staff keuangan 3')
                        <a href="/laporankasbesarpenjualan" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporankasbesarpenjualan']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Kas Besar
                            </li>
                        </a>
                    @endif
                    @if (Auth::user()->level != 'staff keuangan 2' and
                            Auth::user()->level != 'staff keuangan 3' and
                            Auth::user()->level != 'admin pajak' and
                            Auth::user()->level != 'admin medsos')


                        <a href="/laporanretur" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanretur']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Retur
                            </li>
                        </a>

                        <a href="/laporanpenjualan/tunaikredit" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/tunaikredit']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Tunai Kredit
                            </li>
                        </a>
                        <a href="/laporanpenjualan/kartupiutang" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/kartupiutang']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Kartu Piutang
                            </li>
                        </a>
                        <a href="/laporanpenjualan/aup" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/aup']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Analisa Umur Piutang
                            </li>
                        </a>

                        <a href="/laporanpenjualan/lebihsatufaktur" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/lebihsatufaktur']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Lebih 1 Faktur
                            </li>
                        </a>
                        <a href="/laporanpenjualan/dppp" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/dppp']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>DPPP
                            </li>
                        </a>
                        <a href="/laporanpenjualan/dpp" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/dpp']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Data Pengambilan Pelanggan (DPP)
                            </li>
                        </a>
                        {{-- <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>REPO
            </li> --}}
                        <a href="/laporanpenjualan/rekapomsetpelanggan" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/rekapomsetpelanggan']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Rekap Omset Pelanggan
                            </li>
                        </a>
                        <a href="/laporanpenjualan/rekappelanggan" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/rekappelanggan']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Rekap Pelanggan
                            </li>
                        </a>
                        <a href="/laporanpenjualan/rekappenjualan" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/rekappenjualan']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Rekap Penjualan
                            </li>
                        </a>
                        <a href="/laporankendaraan/rekapkendaraan" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporankendaraan/rekapkendaraan']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Rekap Kendaraan
                            </li>
                        </a>
                        @if (in_array($level, $harga_net))
                            <a href="/laporanpenjualan/harganet" style="color:#626262">
                                <li class="list-group-item {{ request()->is(['laporanpenjualan/harganet']) ? 'active' : '' }}">
                                    <i class="feather icon-file mr-1"></i>Harga Net
                                </li>
                            </a>
                        @endif
                        <a href="/laporanpenjualan/tandaterimafaktur" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/tandaterimafaktur']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Tanda Terima Faktur
                            </li>
                        </a>
                        <a href="/laporanpenjualan/rekapwilayah" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/rekapwilayah']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Rekap Wilayah
                            </li>
                        </a>
                        <a href="/laporanpenjualan/effectivecall" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/effectivecall']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Effective Call
                            </li>
                        </a>
                        <a href="/laporanpenjualan/analisatransaksi" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/analisatransaksi']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Analisa Transaksi
                            </li>
                        </a>
                        <a href="/laporanpenjualan/tunaitransfer" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/tunaitransfer']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Tunai Transfer
                            </li>
                        </a>
                        <a href="/laporanpenjualan/lhp" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/lhp']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>LHP
                            </li>
                        </a>
                        <a href="/laporanpenjualan/routingsalesman" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/routingsalesman']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Routing Salesman
                            </li>
                        </a>
                        <a href="/laporanpenjualan/salesperfomance" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/salesperfomance']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Sales Perfomance
                            </li>
                        </a>
                        <a href="/laporanpenjualan/persentasesfa" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/persentasesfa']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Persentase SFA
                            </li>
                        </a>
                        <a href="/laporanpenjualan/persentaselokasi" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/persentaselokasi']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Persentase Lokasi , No. HP & Tandatangan
                            </li>
                        </a>

                        @if (in_array($level, $smmactivity))
                            <a href="/laporanpenjualan/smmactivity" style="color:#626262">
                                <li class="list-group-item {{ request()->is(['laporanpenjualan/smmactivity']) ? 'active' : '' }}">
                                    <i class="feather icon-file mr-1"></i>SMM Activity
                                </li>
                            </a>
                        @endif
                        @if (in_array($level, $rsmactivity))
                            <a href="/laporanpenjualan/rsmactivity" style="color:#626262">
                                <li class="list-group-item {{ request()->is(['laporanpenjualan/rsmactivity']) ? 'active' : '' }}">
                                    <i class="feather icon-file mr-1"></i>RSM Activity
                                </li>
                            </a>
                        @endif
                        <a href="/laporanpenjualan/rekaptandatangan" style="color:#626262">
                            <li class="list-group-item {{ request()->is(['laporanpenjualan/rekaptandatangan']) ? 'active' : '' }}">
                                <i class="feather icon-file mr-1"></i>Rekap Tanda Tangan
                            </li>
                        </a>
                    @endif
                </ul>
            </div>
        </div>
    @endif
@else
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title">Laporan Penjualan</h4>
            </div>
            <ul class="list-group list-group-flush">
                <a href="/laporanpenjualan/penjualan" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/penjualan']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Penjualan
                    </li>
                </a>


                <a href="/laporankasbesarpenjualan" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporankasbesarpenjualan']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Kas Besar
                    </li>
                </a>




                <a href="/laporanretur" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanretur']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Retur
                    </li>
                </a>

                <a href="/laporanpenjualan/tunaikredit" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/tunaikredit']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Tunai Kredit
                    </li>
                </a>
                <a href="/laporanpenjualan/kartupiutang" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/kartupiutang']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Kartu Piutang
                    </li>
                </a>
                <a href="/laporanpenjualan/aup" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/aup']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Analisa Umur Piutang
                    </li>
                </a>

                <a href="/laporanpenjualan/lebihsatufaktur" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/lebihsatufaktur']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Lebih 1 Faktur
                    </li>
                </a>

                <a href="/laporanpenjualan/rekapomsetpelanggan" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/rekapomsetpelanggan']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Rekap Omset Pelanggan
                    </li>
                </a>
                <a href="/laporanpenjualan/rekappelanggan" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/rekappelanggan']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Rekap Pelanggan
                    </li>
                </a>

                <a href="/laporanpenjualan/rekapwilayah" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/rekapwilayah']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Rekap Wilayah
                    </li>
                </a>
                <a href="/laporanpenjualan/effectivecall" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/effectivecall']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Effective Call
                    </li>
                </a>

                <a href="/laporanpenjualan/lhp" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/lhp']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>LHP
                    </li>
                </a>
                <a href="/laporanpenjualan/routingsalesman" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/routingsalesman']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Routing Salesman
                    </li>
                </a>
                <a href="/laporanpenjualan/salesperfomance" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanpenjualan/salesperfomance']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Sales Perfomance
                    </li>
                </a>

            </ul>
        </div>
    </div>




@endif

@if (in_array($level, $laporan_komisi))
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title">Laporan Komisi</h4>
            </div>
            <ul class="list-group list-group-flush">
                <a href="/laporankomisi" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporankomisi']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Komisi
                    </li>
                </a>
                <a href="/rekapkomisi" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['rekapkomisi']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Rekap Komisi
                    </li>
                </a>
                <a href="/laporankomisidriverhelper" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporankomisidriverhelper']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Komisi Driver, Helper & Gudang
                    </li>
                </a>
                {{-- <a href="/laporaninsentif" style="color:#626262">
               <li class="list-group-item {{ request()->is(['laporaninsentif']) ? 'active' : '' }}">
                  <i class="feather icon-file mr-1"></i>Insentif KA Admin
               </li>
            </a> --}}
            </ul>
        </div>
    </div>
@endif
