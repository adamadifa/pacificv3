<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Penerimaan Uang D (LPU) {{ $cabang->nama_cabang }} {{ date("d-m-y") }}</title>
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
    <table class="datatable3" style="width: 150%">
        <thead>
            <tr>
                <th colspan="<?php echo $jmlsales + 2; ?>" style="background-color:#199291; color:white">PENERIMAAN UANG DI CABANG <?php echo strtoupper($cabang->nama_cabang); ?></th>
                <th style="border:none; width:5%"></th>
                <th colspan="<?php echo $jmlsales + 2; ?>" style="background-color:#199291; color:white">PENERIMAAN UANG DIPUSAT</th>
            </tr>
            <tr>
                <th>TGL</th>
                <?php
                foreach ($salesman as $s) {
                ?>
                <th><?php echo $s->nama_karyawan; ?></th>
                <?php
                }
                ?>
                <th>TOTAL</th>
                <th style="border:none; width:5%"></th>
                <th>TGL</th>
                <?php
                foreach ($salesman as $d) {
                ?>
                <th><?php echo $d->nama_karyawan; ?></th>
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
                <td><b>BELUM SETOR <br>BULAN <?php echo strtoupper($namabulan[$bulanlast]) . " " . $tahunlast; ?></b></td>
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
                <td><b>BELUM SETOR <br> BULAN <?php echo strtoupper($namabulan[$bulanlast]) . " " . $tahunlast; ?></b></td>
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
            </tr>
            <?php
            $totallhp = 0;
            while (strtotime($dari) <= strtotime($sampai)) {
            ?>
            <tr style="font-size:10px">
                <td><?php echo DateToIndo2($dari); ?></td>
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
                @php
                $totallhp = 0;
                $totalsetoranpenjualan = 0;
                @endphp
                <td></td>
                <td><?php echo DateToIndo2($dari); ?></td>
                @foreach ($salesman as $d)
                @php
                $setoran = DB::table('setoran_penjualan')
                ->selectRaw("SUM(IFNULL(setoran_kertas,0) + IFNULL(setoran_logam,0) + IFNULL(setoran_transfer,0) + IFNULL(setoran_bg,0) + IFNULL(setoran_lainnya,0)) as jmlsetoran")
                ->where('tgl_lhp',$dari)
                ->where('id_karyawan',$d->id_karyawan)
                ->groupByRaw('tgl_lhp,id_karyawan')->first();

                if($setoran != null){
                $setoranpenjualan = $setoran->jmlsetoran;
                }else{
                $setoranpenjualan = 0;
                }
                $totalsetoranpenjualan = $totalsetoranpenjualan + $setoranpenjualan;
                @endphp
                <td style="text-align: right; font-weight:bold;">{{ !empty($setoranpenjualan) ? rupiah($setoranpenjualan) : '' }}</td>
                @endforeach
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalsetoranpenjualan) ? rupiah($totalsetoranpenjualan) : '' }}</td>
                @php
                $setoranpenjualan = 0;
                @endphp
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
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalall_lhp) ? rupiah($totalall_lhp) : '' }}</td>
                <td style="border:none; width:5%; background-color:white"></td>
                <td><b>TOTAL</b></td>
                @php
                $totalall_setoran = 0;
                @endphp
                @foreach ($salesman as $d)
                @php
                $allsetoran = DB::table('setoran_penjualan')
                ->selectRaw("SUM(IFNULL(setoran_kertas,0) + IFNULL(setoran_logam,0) + IFNULL(setoran_transfer,0) + IFNULL(setoran_bg,0) + IFNULL(setoran_lainnya,0)) as jmlsetoran")
                ->whereBetween('tgl_lhp',[$from,$sampai])
                ->where('id_karyawan',$d->id_karyawan)
                ->groupByRaw('id_karyawan')->first();

                if($allsetoran != null){
                $all_setoran = $allsetoran->jmlsetoran;
                }else{
                $all_setoran = 0;
                }
                $totalall_setoran = $totalall_setoran + $all_setoran;
                @endphp
                <td style="text-align: right; font-weight:bold;">{{ !empty($all_setoran) ? rupiah($all_setoran) : '' }}</td>
                @endforeach
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalall_setoran) ? rupiah($totalall_setoran) : '' }}</td>
            </tr>
            <tr style="font-size:12px; background-color:#199291; color:white">
                <td><b>GM <?php echo strtoupper($namabulan[$bulanlast]) . " " . $tahunlast; ?></b></td>
                @php
                if ($bulanlast == 1) {
                $blnlast1 = 12;
                $thnlast1 = $tahunskrg - 1;
                } else {
                $blnlast1 = $bulanlast - 1;
                $thnlast1 = $tahunskrg;
                }
                $totalgmlast = 0;

                $darilast = $thnlast."-".$blnlast."-01";
                $sampailast = date("Y-m-t",strtotime($darilast));

                $darilast1 = $thnlast1."-".$blnlast1."-01";
                $sampailast1 = date("Y-m-t",strtotime($darilast1));


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
                ->whereBetween('tgl_giro',[$darilast1,$sampailast])
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
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalgmlast) ? rupiah($totalgmlast) : '' }}</td>
                <td style="border:none; width:5%; background-color:white"></td>
                <td><b>GM <?php echo strtoupper($namabulan[$bulanlast]) . " " . $tahunlast; ?></b></td>
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
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalgmlast) ? rupiah($totalgmlast) : '' }}</td>
            </tr>
            <tr style="font-size:12px; background-color:#199291; color:white">
                <td><b>GMD <?php echo strtoupper($namabulan[$bulanskrg]) . " " . $tahunskrg; ?></b></td>
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
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalgmnow) ? rupiah($totalgmnow) : '' }}</td>
                <td style="border:none; width:5%; background-color:white"></td>
                <td><b>GMC <?php echo strtoupper($namabulan[$bulanskrg]) . " " . $tahunskrg; ?></b></td>
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
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalgmnow) ? rupiah($totalgmnow) : '' }}</td>
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
                <td style="text-align: right; font-weight:bold;">{{ !empty($belumsetor) ? rupiah($belumsetor) : '' }}</td>
                @endforeach
                <td style="text-align: right; font-weight:bold;">{{ !empty($totalbs) ? rupiah($totalbs) : '' }}</td>
                <td style="border:none; width:5%; background-color:white"></td>
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
                <td style="text-align: right; font-weight:bold;">{{ !empty($belumsetor) ? rupiah($belumsetor) : '' }}</td>
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
                <td style="text-align: right; font-weight:bold;">{{ !empty($grandtotal) ? rupiah($grandtotal) : '' }}</td>
                <td style="border:none; width:5%; background-color:white"></td>
                <td><b>TOTAL</b></td>
                @php
                $grandall_setoran = 0;
                $grandtotal_setoran = 0;
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

                $allsetoran = DB::table('setoran_penjualan')
                ->selectRaw("SUM(IFNULL(setoran_kertas,0) + IFNULL(setoran_logam,0) + IFNULL(setoran_transfer,0) + IFNULL(setoran_bg,0)+ IFNULL(setoran_lainnya,0)) as jmlsetoran")
                ->whereBetween('tgl_lhp',[$from,$sampai])
                ->where('id_karyawan',$d->id_karyawan)
                ->groupByRaw('id_karyawan')->first();

                if($allsetoran != null){
                $all_setoran = $allsetoran->jmlsetoran;
                }else{
                $all_setoran = 0;
                }

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
                $query->where('omset_bulan','>=',$bulanskrg);
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

                $grandall_setoran = $all_setoran + $bs_bulanlalu + $gm_last - $bs_bulanini - $gm_now;
                $grandtotal_setoran += $grandall_setoran;
                @endphp
                <td style="text-align: right; font-weight:bold;">{{ !empty($grandall_setoran) ? rupiah($grandall_setoran) : '' }}</td>
                @endforeach
                <td style="text-align: right; font-weight:bold;">{{ !empty($grandtotal_setoran) ? rupiah($grandtotal_setoran) : '' }}</td>
            </tr>

        </tbody>
    </table>
    <br>
    <br>
    <table class="datatable3" style="font-size:16px">
        <tr>
            <td style="font-weight:bold; background-color:yellow">PENERIMAAN LHP</td>
            <td style="text-align:right; font-weight:bold;"><?php echo rupiah($grandtotal); ?></td>
        </tr>
        <tr>
            <td style="font-weight:bold; background-color:yellow">SETORAN SALES</td>
            <td style="text-align:right; font-weight:bold;"><?php echo rupiah($grandtotal_setoran); ?></td>
        </tr>
        <tr>
            <td style="font-weight:bold; background-color:yellow">SELISIH</td>
            <td style="text-align:right; font-weight:bold;"><?php echo rupiah(($grandtotal) - $grandtotal_setoran); ?></td>
        </tr>
    </table>
</body>
</html>
