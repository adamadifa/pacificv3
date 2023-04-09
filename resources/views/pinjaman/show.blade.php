<div class="row">
    <div class="col-3">
        <div class="row">
            <div class="col-12">
                @if($pinjaman->foto == null)
                @if($pinjaman->jenis_kelamin == "1")
                <img src="{{ asset('app-assets/images/male.jpg') }}" class="card-img" style="height: 350px !important">
                @else
                <img src="{{ asset('app-assets/images/female.jpg') }}" class="card-img" style="height: 350px !important">
                @endif
                @else
                @php
                $path = Storage::url('karyawan/'.$karyawan->foto);
                @endphp
                <img src="{{ url($path) }}" class="card-img" style="height: 350px !important">
                @endif
            </div>
        </div>
    </div>
    <div class="col-4">
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

    </div>
    <div class="col-5">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    {{-- <tr>
                        <th>Gaji Pokok + Tunjangan</th>
                        <td style="text-align: right">{{ rupiah($pinjaman->gapok_tunjangan) }}</td>
                    </tr> --}}
                    {{-- <tr>
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
                    </tr> --}}
                    <tr>
                        <th>Jumlah Pinjaman</th>
                        <td style="text-align: right; font-weight:bold" id="jmlpinjaman">{{ rupiah($pinjaman->jumlah_pinjaman) }}</td>
                    </tr>
                    <tr>
                        <th>Angsuran</th>
                        <td>{{ $pinjaman->angsuran }} Bulan</td>
                    </tr>
                    <tr>
                        <th>Jumlah Bayar</th>
                        <td id="jmlbayar" style="text-align: right"></td>
                    </tr>
                    <tr>
                        <th>Sisa Tagihan</th>
                        <td id="sisatagihan" style="text-align: right"></td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</div>
<div class="row mt-2">
    <div class="col-7">
        <div class="row">
            <div class="col-12">
                @if (in_array($level,$inputbayarpinjaman))
                <a href="#" class="btn btn-primary mb-1" id="inputbayar" no_pinjaman="{{ $pinjaman->no_pinjaman }}">Input Bayar</a>
                @endif

                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>No. Bukti</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="loadhistoribayar"></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-5">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Cicilan Ke</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Realisasi</th>
                            <th>Sisa Tagihan</th>
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

        function convertToRupiah(number) {
            if (number) {
                var rupiah = "";
                var numberrev = number
                    .toString()
                    .split("")
                    .reverse()
                    .join("");
                for (var i = 0; i < numberrev.length; i++)
                    if (i % 3 == 0) rupiah += numberrev.substr(i, 3) + ".";
                return (
                    rupiah
                    .split("", rupiah.length - 1)
                    .reverse()
                    .join("")
                );
            } else {
                return number;
            }
        }


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

        function loadhistoribayar() {
            var no_pinjaman = "{{ $pinjaman->no_pinjaman }}";
            $.ajax({
                type: 'POST'
                , url: '/pinjaman/gethistoribayar'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman: no_pinjaman
                }
                , cache: false
                , success: function(respond) {
                    $("#loadhistoribayar").html(respond);
                    loadsisatagihan();
                }
            });
        }

        function loadsisatagihan() {
            var jml_pinjaman = $("#jmlpinjaman").text();
            var totalbayar = $("#totalbayar").text();

            var jp = parseInt(jml_pinjaman.replace(/\./g, ''));
            var tb = parseInt(totalbayar.replace(/\./g, ''));

            var sisa = jp - tb;
            $("#jmlbayar").text(convertToRupiah(tb));
            $("#sisatagihan").text(convertToRupiah(sisa));

        }

        loadrencanabayar();
        loadhistoribayar();


        $("#inputbayar").click(function(e) {
            e.preventDefault();
            var no_pinjaman = $(this).attr("no_pinjaman");
            $('#mdlinputbayarpinjaman').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $.ajax({
                type: 'POST'
                , url: '/pembayaranpinjaman/create'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman: no_pinjaman
                }
                , cache: false
                , success: function(respond) {
                    $("#loadinputbayarpinjaman").html(respond);
                }
            });
        });


    });

</script>
