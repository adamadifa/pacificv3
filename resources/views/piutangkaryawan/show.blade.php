<div class="row">
    <div class="col-3">
        <div class="row">
            <div class="col-12">
                @if($pinjaman->foto == null)
                @if($pinjaman->jenis_kelamin == "1")
                <img src="{{ asset('app-assets/images/male.jpg') }}" class="card-img" style="height: 250px !important">
                @else
                <img src="{{ asset('app-assets/images/female.jpg') }}" class="card-img" style="height: 250px !important">
                @endif
                @else
                @php
                $path = Storage::url('karyawan/'.$pinjaman->foto);
                @endphp
                <img src="{{ url($path) }}" class="card-img" style="height: 250px !important">
                @endif
            </div>
        </div>
    </div>
    <div class="col-9">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <tr>
                        <th>No. Pinjaman</th>
                        <td>{{ $pinjaman->no_pinjaman_nonpjp }}</td>
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
                    <tr>
                        <th>Jumlah Pinjaman</th>
                        <td style="text-align: right; font-weight:bold" id="jmlpinjaman">{{ rupiah($pinjaman->jumlah_pinjaman) }}</td>
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
    <div class="col-12">
        <div class="row">
            <div class="col-12">
                @if (in_array($level,array('admin','manager hrd')))
                <a href="#" class="btn btn-primary mb-1" id="inputbayar" no_pinjaman_nonpjp="{{ $pinjaman->no_pinjaman_nonpjp }}">Input Bayar</a>
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
        $("#inputbayar").click(function(e) {
            e.preventDefault();
            var no_pinjaman_nonpjp = $(this).attr("no_pinjaman_nonpjp");
            $('#mdlinputbayarpinjaman').modal({
                backdrop: 'static'
                , keyboard: false
            });
            $.ajax({
                type: 'POST'
                , url: '/pembayaranpiutangkaryawan/create'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman_nonpjp: no_pinjaman_nonpjp
                }
                , cache: false
                , success: function(respond) {
                    $("#loadinputbayarpinjaman").html(respond);
                }
            });
        });

        function loadhistoribayar() {
            var no_pinjaman_nonpjp = "{{ $pinjaman->no_pinjaman_nonpjp }}";
            $.ajax({
                type: 'POST'
                , url: '/piutangkaryawan/gethistoribayar'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman_nonpjp: no_pinjaman_nonpjp
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

        loadhistoribayar();

    });

</script>
