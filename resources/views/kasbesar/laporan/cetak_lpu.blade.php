<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Penerimaan Uang (LPU) {{ $cabang->nama_cabang }} {{ date("d-m-y") }}</title>
    <style>
        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 11px;
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 6px;
        }

        .datatable3 th {
            border: 1px solid #4d4d4d;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }

    </style>
</head>

<body>
    @php
    $from = $dari;
    @endphp
    <b style="font-size:14px;">
        LAPORAN PENERIMAAN UANG (LPU)
        <br>
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        <br>
        PERIODE BULAN {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}
    </b>
    <br>
    <table class="datatable3" style="width: 300%">
        <thead>
            <tr>
                <th colspan="<?php echo $jmlsales + 2; ?>" style="background-color:#199291; color:white">PENERIMAAN UANG
                    DI CABANG
                    <?php echo strtoupper($cabang->nama_cabang); ?>
                </th>
                <th style="border:none; width:5%"></th>
                <th colspan="<?php echo $jmlbank + 2; ?>" style="background-color:#199291; color:white">PENERIMAAN UANG
                    DIPUSAT</th>
            </tr>
            <tr>
                <th>TGL</th>
                <?php
                foreach ($salesman as $s) {
                ?>
                <th>
                    <?php echo $s->nama_karyawan; ?>
                </th>
                <?php
                }
                ?>
                <th>TOTAL</th>
                <th style="border:none; width:5%"></th>
                <th>TGL</th>
                <?php
                foreach ($bank as $b) {
                ?>
                <th>
                    <?php echo $b->nama_bank; ?>
                </th>
                <?php
                }
                ?>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody style="font-size: 11px">
            <tr>
                @php
                $tgl_bulanlalu= date('Y-m-d', strtotime(date($dari) . '- 1 month'));
                $tgllast = explode("-", $tgl_bulanlalu);

                $bulanskrg = $bulan;
                $tahunskrg = $tahun;
                $bulanlast = $tgllast[1]+0;
                $tahunlast = $tgllast[0];

                @endphp
                <td><b>BELUM SETOR <br>BULAN <?php echo strtoupper($namabulan[$bulanlast]) . " " . $tahunlast; ?> </b></td>
                @php
                $totalbelumsetor = 0;
                @endphp
                @foreach ($salesman as $d)
                @php
                $belumsetor = DB::table('belumsetor_detail')
                ->select('jumlah')
                ->join('belumsetor','belumsetor_detail.kode_saldobs','=','belumsetor.kode_saldobs')
                ->where('bulan',$bulanlast)
                ->where('tahun',$tahunlast)
                ->where('id_karyawan',$d->id_karyawan)
                ->first();
                if($belumsetor != null){
                $bs = $belumsetor->jumlah;
                }else{
                $bs = 0;
                }

                $totalbelumsetor += $bs;
                @endphp
                <td style="text-align: right; font-weight:bold; color:red">{{ !empty($bs) ? rupiah($bs) : '' }}</td>
                @endforeach
                <td style="text-align: right; font-weight:bold; color:red">{{ !empty($totalbelumsetor) ? rupiah($totalbelumsetor) : '' }}</td>
                <td></td>
                <td><b>UANG DISETOR BULAN <?php echo $namabulan[$bulanlast] . " " . $tahunlast; ?></b></td>
                <?php
                $totalsetoranpusatlast = 0;
                foreach ($bank as $b) {
                    $setoranpusatlast = DB::table('setoran_pusat')
                    ->selectRaw("SUM(IFNULL(uang_kertas,0)+IFNULL(uang_logam,0)+IFNULL(giro,0)+IFNULL(transfer,0)) as totalsetoranpusat")
                    ->where('tgl_diterimapusat','<',$dari)
                    ->where('bank',$b->kode_bank)
                    ->where('kode_cabang',$kode_cabang)
                    ->where('status',1)
                    ->where('omset_bulan',$bulanskrg)
                    ->where('omset_tahun',$tahunskrg)
                    ->groupByRaw('omset_bulan,omset_tahun,bank')
                    ->first();
                    $setoranlast = $setoranpusatlast != null ? $setoranpusatlast->totalsetoranpusat : 0;
                    $totalsetoranpusatlast += $setoranlast;
                ?>
                <td style="text-align:right; font-weight:bold">
                    <?php if (!empty($setoranpusatlast->totalsetoranpusatlast)) {
                        echo rupiah($setoranpusatlast->totalsetoranpusatlast);
                    } ?></td>
                <?php
                }
                ?>
            </tr>
            <?php
            $totallhp = 0;
            while (strtotime($dari) <= strtotime($sampai)) {
            ?>
            <tr style="font-size:10px">
                <td>
                    <?php echo DateToIndo2($dari); ?>
                </td>
                @foreach ($salesman as $d)
                @php
                $lhp = DB::table('setoran_penjualan')
                ->selectRaw("SUM(lhp_tunai+lhp_tagihan) as jmllhp")
                ->where('tgl_lhp',$dari)
                ->where('id_karyawan',$d->id_karyawan)
                ->groupByRaw('tgl_lhp,id_karyawan')->first();

                if($lhp != null){
                $lhp = $lhp->jmllhp;
                }else{
                $lhp = 0;
                }
                $totallhp = $totallhp + $lhp;
                @endphp
                <td style="text-align: right; font-weight:bold;">{{ !empty($lhp) ? rupiah($lhp) : '' }}</td>
                @endforeach
                <td style="text-align: right; font-weight:bold;">{{ !empty($totallhp) ? rupiah($totallhp) : '' }}</td>
                <td></td>
                <td><?php echo DateToIndo2($dari); ?></td>
                <?php
                $totalsetoranpusat = 0;
                foreach ($bank as $b) {
                    $setoranpusat = DB::table('setoran_pusat')
                    ->selectRaw("SUM(IFNULL(uang_kertas,0)+IFNULL(uang_logam,0)+IFNULL(giro,0)+IFNULL(transfer,0)) as totalsetoranpusat")
                    ->where('tgl_diterimapusat',$dari)
                    ->where('bank',$b->kode_bank)
                    ->where('kode_cabang',$kode_cabang)
                    ->where('status',1)
                    ->where('omset_bulan',$bulanskrg)
                    ->where('omset_tahun',$tahunskrg)
                    ->groupByRaw('tgl_diterimapusat,bank')
                    ->first();
                    $setoran = $setoranpusat != null ? $setoranpusat->totalsetoranpusat : 0;
                    $totalsetoranpusat += $setoran;

                ?>
                <td style="text-align:right; font-weight:bold">{{ !empty($setoran) ?  rupiah($setoran) : '' }}</td>
                <?php
                }
                ?>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($totalsetoranpusat)) { echo rupiah($totalsetoranpusat); } ?></td>
            </tr>

            <?php
             $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari))); //looping tambah 1 date
            } ?>
            <tr style="font-size:12px; background-color:#199291; color:white">
                <td style="font-weight: bold">TOTAL</td>
                @php
                $totalall_lhp = 0;
                @endphp
                @foreach ($salesman as $d)
                @php
                $alllhp = DB::table('setoran_penjualan')
                ->selectRaw("SUM(lhp_tunai+lhp_tagihan) as jmllhp")
                ->whereBetween('tgl_lhp',[$from,$sampai])
                ->where('id_karyawan',$d->id_karyawan)
                ->groupBy('id_karyawan')->first();

                if($alllhp != null){
                $all_lhp = $alllhp->jmllhp;
                //dd($alllhp);
                }else{
                $all_lhp = 0;
                }
                $totalall_lhp += $all_lhp;
                @endphp
                <td style="text-align: right; font-weight:bold;">{{ !empty($all_lhp) ? rupiah($all_lhp) : '' }}</td>
                @endforeach
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalall_lhp) ? rupiah($totalall_lhp) : ''}}</td>
                <td></td>
                <td><b>TOTAL</b></td>
                <?php
                $totalallsetoranpusat = 0;
                $totalallsetoranpusatlast = 0;
                foreach ($bank as $b) {


                    $allsetoranpusat = DB::table('setoran_pusat')
                    ->selectRaw("SUM(IFNULL(uang_kertas,0)+IFNULL(uang_logam,0)+IFNULL(giro,0)+IFNULL(transfer,0)) as totalsetoranpusat")
                    ->where('tgl_diterimapusat','<',$dari)
                    ->where('bank',$b->kode_bank)
                    ->where('kode_cabang',$kode_cabang)
                    ->where('status',1)
                    ->where('omset_bulan',$bulanskrg)
                    ->where('omset_tahun',$tahunskrg)
                    ->groupByRaw('omset_bulan,omset_tahun,bank')
                    ->first();
                    $allsetoran = $allsetoranpusat != null ? $allsetoranpusat->totalsetoranpusat : 0;
                    $totalallsetoranpusatlast = $totalallsetoranpusatlast + $allsetoran;
                    // $qallsetoranpusat     = "SELECT SUM(uang_kertas+uang_logam+giro +IFNULL(transfer,0)) as totalsetoranpusat FROM setoran_pusat WHERE tgl_diterimapusat BETWEEN '$fromlast' AND '$sampai' AND bank='$b->kode_bank' AND kode_cabang='$cbg' AND status ='1' AND omset_bulan ='$bulanskrg' AND omset_tahun='$tahunskrg'  GROUP BY bank";
                    // $allsetoranpusat      = $this->db->query($qallsetoranpusat)->row_array();
                    // $totalallsetoranpusat = $totalallsetoranpusat + $allsetoranpusat['totalsetoranpusat'];
                ?>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($allsetoran)) { echo rupiah($allsetoran); } ?></td>
                <?php
                }
                ?>
                <td style="text-align:right; font-weight:bold"><?php if (!empty($totalallsetoranpusatlast)) { echo rupiah($totalallsetoranpusatlast); } ?></td>
            </tr>
            <tr style="font-size:12px; background-color:#199291; color:white">
                <td><b>GM
                        <?php echo strtoupper($namabulan[$bulanlast]) . " " . $tahunlast; ?>
                    </b></td>
                @php
                if ($bulanlast == 1) {
                $blnlast1 = 12;
                $thnlast1 = $tahunskrg - 1;
                } else {
                $blnlast1 = $bulanlast - 1;
                $thnlast1 = $tahunskrg;
                }
                $totalgmlast = 0;
                @endphp

                @foreach ($salesman as $d)
                @php
                $gmlast = DB::table('giro')
                ->selectRaw("IFNULL(hb.id_karyawan,giro.id_karyawan) as id_karyawan, SUM(jumlah) as jumlah")
                ->leftJoin(
                DB::raw("(SELECT id_giro,id_karyawan,tglbayar FROM historibayar GROUP BY id_giro,id_karyawan,tglbayar) hb"),
                function ($join) {
                $join->on('giro.id_giro', '=', 'hb.id_giro');
                }
                )
                ->whereRaw('IFNULL(hb.id_karyawan,giro.id_karyawan)="'.$d->id_karyawan.'"')
                ->whereRaw('MONTH(tgl_giro) ='.$bulanlast)
                ->whereRaw('YEAR(tgl_giro) ='.$tahunlast)
                ->where('omset_bulan',$bulanskrg)
                ->where('omset_tahun',$tahunskrg)

                ->orwhereRaw('IFNULL(hb.id_karyawan,giro.id_karyawan)="'.$d->id_karyawan.'"')
                ->whereRaw('MONTH(tgl_giro) ='.$blnlast1)
                ->whereRaw('YEAR(tgl_giro) ='.$thnlast1)
                ->whereRaw('MONTH(tglbayar) ='.$bulanskrg)
                ->whereRaw('YEAR(tglbayar) ='.$tahunskrg)
                ->groupByRaw('IFNULL(hb.id_karyawan,giro.id_karyawan)')
                ->first();
                if($gmlast != null){
                $gm_last = $gmlast->jumlah;
                }else{
                $gm_last =0;
                }
                $totalgmlast += $gm_last;
                @endphp
                <td style="text-align: right; font-weight:bold;">{{ !empty($gm_last) ? rupiah($gm_last) : '' }}</td>
                @endforeach
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalgmlast) ? rupiah($totalgmlast) : '' }}
                </td>
            </tr>
            <tr style="font-size:12px; background-color:#199291; color:white">
                <td><b>GM
                        <?php echo strtoupper($namabulan[$bulanskrg]) . " " . $tahunskrg; ?>
                    </b></td>
                @php
                $totalgmnow = 0;
                $tahunskr = $tahunskrg + 1;
                @endphp
                @foreach ($salesman as $d)

                @php
                $query = App\Models\Giro::query();
                $query->selectRaw(" giro.id_karyawan,SUM(jumlah) as jumlah");
                $query->leftJoin(
                DB::raw("(SELECT id_giro,tglbayar FROM historibayar GROUP BY id_giro,tglbayar) hb"),
                function ($join) {
                $join->on('giro.id_giro', '=', 'hb.id_giro');
                }
                );
                $query->where('giro.id_karyawan',$d->id_karyawan);
                $query->whereBetween('tgl_giro',[$from,$sampai]);
                $query->whereNull('tglbayar');
                $query->where('omset_bulan',0);
                $query->where('omset_tahun','');

                $query->orWhere('giro.id_karyawan',$d->id_karyawan);
                $query->whereBetween('tgl_giro',[$from,$sampai]);
                $query->where('tglbayar','>=',$sampai);

                if ($bulanskrg == 12) {
                $query->where('omset_bulan','>=',1);
                $query->where('omset_tahun','>=',$tahunskr);
                } else {
                $query->where('omset_bulan','>',$bulanskrg);
                $query->where('omset_tahun','>=',$tahunskrg);
                }


                $query->orWhere('giro.id_karyawan',$d->id_karyawan);
                $query->whereBetween('tgl_giro',[$from,$sampai]);
                $query->whereNull('tglbayar');

                if ($bulanskrg == 12) {
                $query->where('omset_bulan','>=',1);
                $query->where('omset_tahun','>=',$tahunskr);
                } else {
                $query->where('omset_bulan','>',$bulanskrg);
                $query->where('omset_tahun','>=',$tahunskrg);
                }

                $query->where('penggantian',1);

                $query->orWhere('giro.id_karyawan',$d->id_karyawan);
                $query->whereBetween('tgl_giro',[$from,$sampai]);
                $query->where('tglbayar','>=',$sampai);
                $query->where('omset_bulan','0');
                $query->where('omset_tahun','');
                $query->groupBy('giro.id_karyawan');
                $gmnow = $query->first();

                if($gmnow != null){
                $gm_now = $gmnow->jumlah;
                }else{
                $gm_now =0;
                }
                $totalgmnow += $gm_now;
                @endphp
                <td style="text-align: right; font-weight:bold;">{{ !empty($gm_now) ? rupiah($gm_now) : '' }}</td>
                @endforeach
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalgmnow) ? rupiah($totalgmnow) : '' }}
                </td>

            </tr>
            <tr style="font-size:12px; background-color:#199291; color:white">
                <td><b>UANG BELUM DI SETOR</b></td>
                @php
                $totalbs = 0;
                @endphp
                @foreach ($salesman as $d)
                @php
                $bs = DB::table('belumsetor_detail')
                ->selectRaw("SUM(jumlah) as jumlah")
                ->join('belumsetor','belumsetor_detail.kode_saldobs','=','belumsetor.kode_saldobs')
                ->where('bulan',$bulanskrg)->where('tahun',$tahunskrg)->where('id_karyawan',$d->id_karyawan)
                ->first();
                if($bs!=null){
                $belumsetor = $bs->jumlah;
                }else{
                $belumsetor = 0;
                }
                $totalbs += $belumsetor;
                @endphp
                <td style="text-align: right; font-weight:bold;">{{ !empty($belumsetor) ? rupiah($belumsetor) : '' }}
                </td>
                @endforeach
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalbs) ? rupiah($totalbs) : '' }}</td>
            </tr>
            <tr style="font-size:12px; background-color:#199291; color:white">
                <td><b>TOTAL</b></td>
                @php
                $grandall = 0;
                $grandtotal = 0;
                @endphp
                @foreach ($salesman as $d)
                @php
                $belumsetor_bulanlau = DB::table('belumsetor_detail')
                ->select('jumlah')
                ->join('belumsetor','belumsetor_detail.kode_saldobs','=','belumsetor.kode_saldobs')
                ->where('bulan',$bulanlast)
                ->where('tahun',$tahunlast)
                ->where('id_karyawan',$d->id_karyawan)
                ->first();


                if($belumsetor_bulanlau != null){
                $bs_bulanlalu = $belumsetor_bulanlau->jumlah;
                }else{
                $bs_bulanlalu = 0;
                }

                $alllhp = DB::table('setoran_penjualan')
                ->selectRaw("SUM(lhp_tunai+lhp_tagihan) as jmllhp")
                ->whereBetween('tgl_lhp',[$from,$sampai])
                ->where('id_karyawan',$d->id_karyawan)
                ->groupBy('id_karyawan')->first();

                if($alllhp != null){
                $all_lhp = $alllhp->jmllhp;
                //dd($alllhp);
                }else{
                $all_lhp = 0;
                }

                $gmlast = DB::table('giro')
                ->selectRaw("giro.id_karyawan, SUM(jumlah) as jumlah")
                ->leftJoin(
                DB::raw("(SELECT id_giro,tglbayar FROM historibayar GROUP BY id_giro,tglbayar) hb"),
                function ($join) {
                $join->on('giro.id_giro', '=', 'hb.id_giro');
                }
                )
                ->where('giro.id_karyawan',$d->id_karyawan)
                ->whereRaw('MONTH(tgl_giro) ='.$bulanlast)
                ->whereRaw('YEAR(tgl_giro) ='.$tahunlast)
                ->where('omset_bulan',$bulanskrg)
                ->where('omset_tahun',$tahunskrg)
                ->orWhere('giro.id_karyawan',$d->id_karyawan)
                ->whereRaw('MONTH(tgl_giro) ='.$blnlast1)
                ->whereRaw('YEAR(tgl_giro) ='.$thnlast1)
                ->whereRaw('MONTH(tglbayar) ='.$bulanskrg)
                ->whereRaw('YEAR(tglbayar) ='.$tahunskrg)
                ->groupBy('giro.id_karyawan')
                ->first();
                if($gmlast != null){
                $gm_last = $gmlast->jumlah;
                }else{
                $gm_last =0;
                }


                $query = App\Models\Giro::query();
                $query->selectRaw(" giro.id_karyawan,SUM(jumlah) as jumlah");
                $query->leftJoin(
                DB::raw("(SELECT id_giro,tglbayar FROM historibayar GROUP BY id_giro,tglbayar) hb"),
                function ($join) {
                $join->on('giro.id_giro', '=', 'hb.id_giro');
                }
                );
                $query->where('giro.id_karyawan',$d->id_karyawan);
                $query->whereBetween('tgl_giro',[$from,$sampai]);
                $query->whereNull('tglbayar');
                $query->where('omset_bulan',0);
                $query->where('omset_tahun','');
                $query->orWhere('giro.id_karyawan',$d->id_karyawan);
                $query->whereBetween('tgl_giro',[$from,$sampai]);
                $query->where('tglbayar','>=',$sampai);

                if ($bulanskrg == 12) {
                $query->where('omset_bulan','>=',1);
                $query->where('omset_tahun','>=',$tahunskr);
                } else {
                $query->where('omset_bulan','>',$bulanskrg);
                $query->where('omset_tahun','>=',$tahunskrg);
                }


                $query->orWhere('giro.id_karyawan',$d->id_karyawan);
                $query->whereBetween('tgl_giro',[$from,$sampai]);
                $query->whereNull('tglbayar');

                if ($bulanskrg == 12) {
                $query->where('omset_bulan','>=',1);
                $query->where('omset_tahun','>=',$tahunskr);
                } else {
                $query->where('omset_bulan','>',$bulanskrg);
                $query->where('omset_tahun','>=',$tahunskrg);
                }

                $query->where('penggantian',1);
                $query->orWhere('giro.id_karyawan',$d->id_karyawan);
                $query->whereBetween('tgl_giro',[$from,$sampai]);
                $query->where('tglbayar','>=',$sampai);
                $query->where('omset_bulan',0);
                $query->where('omset_tahun','');
                $query->groupBy('giro.id_karyawan');
                $gmnow = $query->first();

                if($gmnow != null){
                $gm_now = $gmnow->jumlah;
                }else{
                $gm_now =0;
                }

                $belumsetor_bulanini = DB::table('belumsetor_detail')
                ->selectRaw("SUM(jumlah) as jumlah")
                ->join('belumsetor','belumsetor_detail.kode_saldobs','=','belumsetor.kode_saldobs')
                ->where('bulan',$bulanskrg)->where('tahun',$tahunskrg)->where('id_karyawan',$d->id_karyawan)
                ->first();
                if($belumsetor_bulanini!=null){
                $bs_bulanini = $belumsetor_bulanini->jumlah;
                }else{
                $bs_bulanini = 0;
                }

                $grandall = $all_lhp + $bs_bulanlalu + $gm_last - $bs_bulanini - $gm_now;
                $grandtotal += $grandall;
                @endphp
                <td style="text-align: right; font-weight:bold;">{{ !empty($grandall) ? rupiah($grandall) : '' }}</td>
                @endforeach
                <td style="text-align: right; font-weight:bold;">{{ !empty($grandtotal) ? rupiah($grandtotal) : '' }}
                </td>
            </tr>

        </tbody>
    </table>
    <br>
    <br>
    <table class="datatable3" style="font-size:16px">
        <tr>
            <td style="font-weight:bold; background-color:yellow">PENERIMAAN LHP</td>
            <td style="text-align:right; font-weight:bold;">
                <?php echo rupiah($grandtotal); ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold; background-color:yellow">SETORAN SALES</td>
            <td style="text-align:right; font-weight:bold;">
                <?php echo rupiah($totalallsetoranpusatlast); ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold; background-color:yellow">SELISIH</td>
            <td style="text-align:right; font-weight:bold;">
                <?php echo rupiah(($totalallsetoranpusatlast) - $grandtotal); ?>
            </td>
        </tr>
    </table>
</body>

</html>
