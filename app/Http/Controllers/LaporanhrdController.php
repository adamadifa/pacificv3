<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class LaporanhrdController extends Controller
{
    public function presensi()
    {

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $departemen = DB::table('hrd_departemen')->orderBy('nama_dept')->get();
        $cbg = new Cabang();
        if (Auth::user()->kode_cabang == "PCF" || Auth::user()->kode_cabang == "PST") {
            if (
                Auth::user()->level == "manager hrd" || Auth::user()->level == "admin"
                || Auth::user()->level == "manager accounting"
                || Auth::user()->level == "spv presensi"
                || Auth::user()->level == "rom"
            ) {
                $cabang = $cbg->getCabang("PST");
            } else {
                $cabang = DB::table('cabang')->where('kode_cabang', 'PST')->get();
            }
        } else {
            $cabang = $cbg->getCabang(Auth::user()->kode_cabang);
        }

        $show_all = config('global.show_all');
        return view('presensi.laporan.lap_presensi', compact('bulan', 'departemen', 'cabang', 'show_all'));
    }


    public function gaji()
    {

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $departemen = DB::table('hrd_departemen')->orderBy('nama_dept')->get();
        $cbg = new Cabang();
        if (Auth::user()->kode_cabang == "PCF" || Auth::user()->kode_cabang == "PST") {
            if (Auth::user()->level == "manager hrd" || Auth::user()->level == "admin" || Auth::user()->level == "spv presensi" || Auth::user()->level == "manager accounting") {
                $cabang = $cbg->getCabang("PST");
            } else {
                $cabang = DB::table('cabang')->where('kode_cabang', 'PST')->get();
            }
        } else {
            $cabang = $cbg->getCabang(Auth::user()->kode_cabang);
        }
        return view('presensi.laporan.lap_gaji', compact('bulan', 'departemen', 'cabang'));
    }

    public function getkantor(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $kantor = DB::table('master_karyawan')
            ->select('id_kantor')
            ->where('kode_dept', $kode_dept)
            ->groupBy('id_kantor')
            ->get();
        return view('presensi.laporan.getkantor', compact('kantor'));
    }

    public function getdepartemen(Request $request)
    {
        $id_kantor = $request->id_kantor;
        $kode_dept = Auth::user()->kode_dept_presensi;
        if (
            Auth::user()->kode_cabang == "PCF" || Auth::user()->kode_cabang == "PST"
        ) {
            if (
                Auth::user()->level == "manager hrd"
                || Auth::user()->level == "admin"
                || Auth::user()->level == "spv presensi"
                || Auth::user()->level == "manager accounting"
                || Auth::user()->level == "rom"
            ) {
                $departemen = DB::table('master_karyawan')
                    ->select('master_karyawan.kode_dept', 'nama_dept')
                    ->where('id_kantor', $id_kantor)
                    ->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
                    ->groupByRaw('master_karyawan.kode_dept,nama_dept')
                    ->get();
            } else if (Auth::user()->level == "emf") {
                $departemen = DB::table('master_karyawan')
                    ->select('master_karyawan.kode_dept', 'nama_dept')
                    ->where('id_kantor', $id_kantor)
                    ->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'PDQ', 'MTC', 'HRD'])
                    ->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
                    ->groupByRaw('master_karyawan.kode_dept,nama_dept')
                    ->get();
            } else {
                $departemen = DB::table('master_karyawan')
                    ->select('master_karyawan.kode_dept', 'nama_dept')
                    ->where('id_kantor', $id_kantor)
                    ->where('master_karyawan.kode_dept', $kode_dept)
                    ->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
                    ->groupByRaw('master_karyawan.kode_dept,nama_dept')
                    ->get();
            }
        } else {
            $departemen = DB::table('master_karyawan')
                ->select('master_karyawan.kode_dept', 'nama_dept')
                ->where('id_kantor', $id_kantor)
                ->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
                ->groupByRaw('master_karyawan.kode_dept,nama_dept')
                ->get();
        }
        if (
            Auth::user()->level == "manager hrd" || Auth::user()->level == "direktur"
            || Auth::user()->level == "spv presensi" || Auth::user()->level == "admin"
            || Auth::user()->level == "rom"
        ) {
            echo "<option value=''>Semua Departemen</option>";
        }
        foreach ($departemen as $d) {
            echo "<option value='$d->kode_dept'>$d->nama_dept</option>";
        }
    }

    public function getgroup(Request $request)
    {
        $id_kantor = $request->id_kantor;
        $kode_dept = Auth::user()->kode_dept_presensi;
        if (Auth::user()->kode_cabang == "PCF" || Auth::user()->kode_cabang == "PST") {
            if (Auth::user()->level == "manager hrd" || Auth::user()->level == "admin") {
                $group = DB::table('master_karyawan')
                    ->select('master_karyawan.grup', 'nama_group')
                    ->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
                    ->where('id_kantor', $id_kantor)
                    ->groupByRaw('master_karyawan.grup,nama_group')
                    ->get();
            } else {
                $group = DB::table('master_karyawan')
                    ->select('master_karyawan.grup', 'nama_group')
                    ->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
                    ->where('id_kantor', $id_kantor)
                    ->where('master_karyawan.kode_dept', $kode_dept)
                    ->groupByRaw('master_karyawan.grup,nama_group')
                    ->get();
            }
        } else {
            $group = DB::table('master_karyawan')
                ->select('master_karyawan.grup', 'nama_group')
                ->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
                ->where('id_kantor', $id_kantor)
                ->groupByRaw('master_karyawan.grup,nama_group')
                ->get();
        }

        return view('presensi.laporan.getgroup', compact('group'));
    }


    public function cetakpresensi(Request $request)
    {


        //dd(ceklibur('2023-06-18', 'BDG'));
        $kode_dept = $request->kode_dept;
        $id_kantor = $request->id_kantor;
        $id_group = $request->id_group;
        $bulan = $request->bulan; //01
        $bl = $bulan;
        $tahun = $request->tahun; //2024
        $jenislaporan = $request->jenis_laporan;
        $jenislaporan_gaji = $request->jenis_laporan_gaji;
        $kode_potongan = "GJ" . $bulan . $tahun;
        $level = Auth::user()->level;
        $show_for_hrd = config('global.show_for_hrd');
        $level_show_all = config('global.show_all');
        if ($bulan == 1) {
            $lastbulan = 12;
            $lasttahun = $tahun - 1; //2023
        } else {
            $lastbulan = $bulan - 1;
            $lasttahun = $tahun;
        }

        if ($bulan == 12) {
            $nextbulan = 1;
            $nexttahun = $tahun + 1;
        } else {
            $nextbulan = $bulan + 1;
            $nexttahun = $tahun;
        }
        $lastbulan = $lastbulan < 10 ?  "0" . $lastbulan : $lastbulan;
        $bulan = $bulan < 10 ?  "0" . $bulan : $bulan;

        if ($jenislaporan == 2) {
            $dari = $tahun . "-" . $bulan . "-01";
            $sampai = date("Y-m-t", strtotime($dari));
        } else {
            $dari = $lasttahun . "-" . $lastbulan . "-21";
            $sampai = $tahun . "-" . $bulan . "-20";
        }



        $daribulangaji = $dari;
        $berlakugaji = $sampai;
        //dd($berlakugaji);

        $datalibur = ceklibur($dari, $sampai);
        $dataliburpenggantiminggu = cekliburpenggantiminggu($dari, $sampai);
        $dataminggumasuk = cekminggumasuk($dari, $sampai);
        $datawfh = cekwfh($dari, $sampai);
        $datawfhfull = cekwfhfull($dari, $sampai);
        $datalembur = ceklembur($dari, $sampai, 1);
        $datalemburharilibur = ceklembur($dari, $sampai, 2);

        // echo json_encode($datalembur);
        // die;



        // Define search list with multiple key=>value pair
        //$search_items = array('id_kantor' => "TSM", 'tanggal_libur' => "2023-06-17");

        // Call search and pass the array and
        // the search list
        //$res = cektgllibur($ceklibur, $search_items);
        //dd(empty($res));

        //dd($sampai);
        $select_date = "";
        $field_date = "";

        $i = 1;
        while (strtotime($dari) <= strtotime($sampai)) {
            $rangetanggal[] = $dari;
            $select_date .= "MAX(IF(tgl_presensi = '$dari', CONCAT(
                IFNULL(jam_in,'NA'),
                '|',IFNULL(jam_out,'NA'),
                '|',IFNULL(nama_jadwal,'NA'),
                '|',IFNULL(jam_kerja.jam_masuk,'NA'),
                '|',IFNULL(jam_kerja.jam_pulang,'NA'),
                '|',IFNULL(presensi.status,'NA'),
                '|',IFNULL(presensi.kode_izin,'NA'),
                '|',IFNULL(presensi.kode_izin_terlambat,'NA'),
                '|',IFNULL(presensi.kode_izin_pulang,'NA'),
                '|',IFNULL(pengajuan_izin.jam_keluar,'NA'),
                '|',IFNULL(pengajuan_izin.jam_masuk,'NA'),
                '|',IFNULL(jam_kerja.total_jam,'NA'),
                '|',IFNULL(pengajuan_izin.sid,'NA'),
                '|',IFNULL(jam_kerja.jam_awal_istirahat,'NA'),
                '|',IFNULL(jam_kerja.jam_istirahat,'NA'),
                '|',IFNULL(jam_kerja.total_jam,'NA'),
                '|',IFNULL(jam_kerja.lintashari,'NA'),
                '|',IFNULL(izinpulang.direktur,'NA'),
                '|',IFNULL(pengajuan_izin.direktur,'NA'),
                '|',IFNULL(pengajuan_izin.keperluan,'NA'),
                '|',IFNULL(izinterlambat.direktur,'NA'),
                '|',IFNULL(pengajuan_izin.jenis_cuti,'NA')
                ),NULL)) as hari_" . $i . ",";

            $field_date .= "hari_" . $i . ",";
            $i++;
            $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
        }


        // dd($bulan);
        //dd($rangetanggal);
        $jmlrange = count($rangetanggal);
        $lastrange = $jmlrange - 1;
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $departemen = DB::table('hrd_departemen')->where('kode_dept', $kode_dept)->first();
        $kantor = DB::table('cabang')->where('kode_cabang', $id_kantor)->first();
        $group = DB::table('hrd_group')->where('id', $id_group)->first();
        // if ($jmlrange == 30) {
        //     array_push($rangetanggal, $rangetanggal[$lastrange]);
        // } else if ($jmlrange == 29) {
        //     array_push($rangetanggal, $rangetanggal[$lastrange], $rangetanggal[$lastrange]);
        // }
        $jmlrange = count($rangetanggal);
        $lastrange = $jmlrange - 1;

        //dd($jmlrange);
        $query = Karyawan::query();

        //dd($jmlrange);
        $query->selectRaw("
                $field_date
                master_karyawan.*,nama_group,nama_dept,nama_jabatan,nama_cabang,klasifikasi,no_rekening,
                iu_masakerja,iu_lembur,iu_penempatan,iu_kpi,
                im_ruanglingkup, im_penempatan,im_kinerja,
                gaji_pokok,
                t_jabatan,t_masakerja,t_tanggungjawab,t_makan,t_istri,t_skill,
                cicilan_pjp,jml_kasbon,jml_nonpjp,jml_pengurang,jml_penambah,
                bpjs_kesehatan.perusahaan,bpjs_kesehatan.pekerja,bpjs_kesehatan.keluarga,iuran_kes,
                bpjs_tenagakerja.k_jht,bpjs_tenagakerja.k_jp,iuran_tk
            ");
        $query->leftJoin(
            DB::raw("(
            SELECT
                $select_date
                presensi.nik
            FROM
                presensi
            LEFT JOIN pengajuan_izin ON presensi.kode_izin = pengajuan_izin.kode_izin
            LEFT JOIN pengajuan_izin as izinpulang ON presensi.kode_izin_pulang = izinpulang.kode_izin
            LEFT JOIN pengajuan_izin as izinterlambat ON presensi.kode_izin_terlambat = izinterlambat.kode_izin
            LEFT JOIN jadwal_kerja ON presensi.kode_jadwal = jadwal_kerja.kode_jadwal
            LEFT JOIN jam_kerja ON presensi.kode_jam_kerja = jam_kerja.kode_jam_kerja
            WHERE tgl_presensi BETWEEN '$rangetanggal[0]' AND  '$sampai'
            GROUP BY
                presensi.nik
            ) presensi"),
            function ($join) {
                $join->on('presensi.nik', '=', 'master_karyawan.nik');
            }
        );
        $query->leftJoin('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id');
        $query->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftJoin('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang');
        $query->leftJoin(
            DB::raw("(
                    SELECT nik,gaji_pokok,t_jabatan,t_masakerja,t_tanggungjawab,
                    t_makan,t_istri,t_skill
                    FROM hrd_mastergaji
                    WHERE kode_gaji IN (SELECT MAX(kode_gaji) as kode_gaji FROM hrd_mastergaji
                    WHERE tgl_berlaku <= '$berlakugaji'  GROUP BY nik)
                ) hrdgaji"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'hrdgaji.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                    SELECT nik,iu_masakerja,iu_lembur,iu_penempatan,iu_kpi,
                    im_ruanglingkup,im_penempatan,im_kinerja
                    FROM hrd_masterinsentif WHERE kode_insentif IN (SELECT MAX(kode_insentif) as kode_insentif FROM hrd_masterinsentif
                    WHERE tgl_berlaku <= '$berlakugaji'  GROUP BY nik)
                ) hrdinsentif"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'hrdinsentif.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                    SELECT nik,perusahaan,pekerja,keluarga,iuran as iuran_kes
                    FROM bpjs_kesehatan WHERE kode_bpjs_kes IN (SELECT MAX(kode_bpjs_kes) as kode_bpjs_kes FROM bpjs_kesehatan
                    WHERE tgl_berlaku <= '$berlakugaji'  GROUP BY nik)
                ) bpjs_kesehatan"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'bpjs_kesehatan.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                    SELECT nik,k_jht,k_jp,iuran as iuran_tk
                    FROM bpjs_tenagakerja WHERE kode_bpjs_tk IN (SELECT MAX(kode_bpjs_tk) as kode_bpjs_tk FROM bpjs_tenagakerja
                    WHERE tgl_berlaku <= '$berlakugaji'  GROUP BY nik)
                ) bpjs_tenagakerja"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'bpjs_tenagakerja.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                   SELECT nik, SUM(jumlah) as cicilan_pjp
                   FROM pinjaman_historibayar
                   INNER JOIN pinjaman ON pinjaman_historibayar.no_pinjaman = pinjaman.no_pinjaman
                   WHERE kode_potongan = '$kode_potongan'
                   GROUP BY nik
                ) pjp"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'pjp.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                   SELECT nik, SUM(jumlah) as jml_kasbon
                   FROM kasbon_historibayar
                   INNER JOIN kasbon ON kasbon_historibayar.no_kasbon = kasbon.no_kasbon
                   WHERE kode_potongan = '$kode_potongan'
                   GROUP BY nik
                ) kasbon"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'kasbon.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                   SELECT nik, SUM(jumlah) as jml_pengurang, SUM(jumlah_penambah) as jml_penambah
                   FROM pengurang_gaji
                   WHERE kode_gaji = '$kode_potongan'
                   GROUP BY nik
                ) penguranggaji"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'penguranggaji.nik');
            }
        );


        $query->leftJoin(
            DB::raw("(
                   SELECT nik, SUM(jumlah) as jml_nonpjp
                   FROM pinjaman_nonpjp_historibayar
                   INNER JOIN pinjaman_nonpjp ON pinjaman_nonpjp_historibayar.no_pinjaman_nonpjp = pinjaman_nonpjp.no_pinjaman_nonpjp
                   WHERE kode_potongan = '$kode_potongan'
                   GROUP BY nik
                ) pinjamannonpjp"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'pinjamannonpjp.nik');
            }
        );
        if (!empty($kode_dept)) {
            $query->where('master_karyawan.kode_dept', $kode_dept);
        }

        if (!empty($id_kantor)) {
            $query->where('master_karyawan.id_kantor', $id_kantor);
        }

        if (!empty($id_group)) {
            $query->where('master_karyawan.grup', $id_group);
        }

        if (request()->is('laporanhrd/gaji/cetak')) {
            if (!in_array($level, $level_show_all)) {
                $query->whereNotIn('id_jabatan', $show_for_hrd);
            } else {
                if (!empty($request->manajemen)) {
                    if ($request->manajemen == 1) {
                        $query->WhereIn('id_jabatan', $show_for_hrd);
                    } else if ($request->manajemen == 2) {
                        $query->whereNotIn('id_jabatan', $show_for_hrd);
                    }
                }
            }
        }
        $query->where('status_aktif', 1);
        $query->where('tgl_masuk', '<=', $sampai);
        $query->orWhere('status_aktif', 0);
        $query->where('tgl_off_gaji', '>=', $daribulangaji);
        $query->where('tgl_masuk', '<=', $sampai);
        if (!empty($kode_dept)) {
            $query->where('master_karyawan.kode_dept', $kode_dept);
        }

        if (!empty($id_kantor)) {
            $query->where('master_karyawan.id_kantor', $id_kantor);
        }

        if (!empty($id_group)) {
            $query->where('master_karyawan.grup', $id_group);
        }

        if (request()->is('laporanhrd/gaji/cetak')) {
            if (!in_array($level, $level_show_all)) {
                $query->whereNotIn('id_jabatan', $show_for_hrd);
            } else {
                if (!empty($request->manajemen)) {
                    if ($request->manajemen == 1) {
                        $query->WhereIn('id_jabatan', $show_for_hrd);
                    } else if ($request->manajemen == 2) {
                        $query->whereNotIn('id_jabatan', $show_for_hrd);
                    }
                }
            }
        }

        if (!empty($request->manajemen && $request->manajemen == 1)) {
            $query->orderBy('id_jabatan');
        }
        $query->orderByRaw('nik,nama_karyawan');

        $presensi = $query->get();




        //dd($rangetanggal);
        //dd(request()->is('laporanhrd/presensipsm/cetak'));
        if (request()->is('laporanhrd/presensipsm/cetak')) {
            if (isset($_POST['export'])) {
                echo "EXPORT";
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Laporan Presensi Format P/S/M.xls");
                return view('presensi.laporan.cetak_psmexcel', compact('departemen', 'kantor', 'group', 'namabulan', 'bulan', 'tahun', 'jmlrange', 'rangetanggal', 'presensi', 'datalibur', 'dataliburpenggantiminggu', 'dataminggumasuk', 'datawfh', 'datawfhfull', 'datalembur', 'datalemburharilibur'));
            } else {
                return view('presensi.laporan.cetakpsm', compact('departemen', 'kantor', 'group', 'namabulan', 'bulan', 'tahun', 'jmlrange', 'rangetanggal', 'presensi', 'datalibur', 'dataliburpenggantiminggu', 'dataminggumasuk', 'datawfh', 'datawfhfull', 'datalembur', 'datalemburharilibur'));
            }
        } else if (request()->is('laporanhrd/gaji/cetak')) {


            if ($jenislaporan_gaji == 2) {
                if (isset($_POST['export'])) {
                    echo "EXPORT";
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Rekap Gaji Detail.xls");
                }
                return view('gaji.laporan.cetak_gaji_rekap', compact('departemen', 'kantor', 'group', 'namabulan', 'bulan', 'tahun', 'jmlrange', 'rangetanggal', 'presensi', 'datalibur', 'dataliburpenggantiminggu', 'dataminggumasuk', 'datawfh', 'datawfhfull', 'datalembur', 'datalemburharilibur', 'sampai'));
            } else if ($jenislaporan_gaji == 7) {
                if (isset($_POST['export'])) {
                    echo "EXPORT";
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Rekap Gaji Detail.xls");
                }
                return view('gaji.laporan.cetak_thr_rekap', compact('departemen', 'kantor', 'group', 'namabulan', 'bulan', 'tahun', 'jmlrange', 'rangetanggal', 'presensi', 'datalibur', 'dataliburpenggantiminggu', 'dataminggumasuk', 'datawfh', 'datawfhfull', 'datalembur', 'datalemburharilibur', 'sampai'));
            } else if ($jenislaporan_gaji == 3) {
                if (isset($_POST['export'])) {
                    echo "EXPORT";
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Slip Detail.xls");
                }
                return view('gaji.laporan.cetak_slip', compact('departemen', 'kantor', 'group', 'namabulan', 'bulan', 'tahun', 'jmlrange', 'rangetanggal', 'presensi', 'datalibur', 'dataliburpenggantiminggu', 'dataminggumasuk', 'datawfh', 'datawfhfull', 'datalembur', 'datalemburharilibur', 'sampai'));
            } else if ($jenislaporan_gaji == 4) {
                if (isset($_POST['export'])) {
                    echo "EXPORT";
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Rekening Gaji.xls");
                }
                return view('gaji.laporan.cetak_rekening', compact('departemen', 'kantor', 'group', 'namabulan', 'bulan', 'tahun', 'jmlrange', 'rangetanggal', 'presensi', 'datalibur', 'dataliburpenggantiminggu', 'dataminggumasuk', 'datawfh', 'datawfhfull', 'datalembur', 'datalemburharilibur', 'sampai'));
            } else if ($jenislaporan_gaji == 5) {
                if (isset($_POST['export'])) {
                    echo "EXPORT";
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Rekening Gaji.xls");
                }
                return view('gaji.laporan.cetak_thr', compact('departemen', 'kantor', 'group', 'namabulan', 'bulan', 'tahun', 'jmlrange', 'rangetanggal', 'presensi', 'datalibur', 'dataliburpenggantiminggu', 'dataminggumasuk', 'datawfh', 'datawfhfull', 'datalembur', 'datalemburharilibur', 'sampai'));
            } else if ($jenislaporan_gaji == 6) {
                if (isset($_POST['export'])) {
                    echo "EXPORT";
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Rekening Gaji.xls");
                }
                $cekUmk = cekUmk();
                return view('gaji.laporan.cetak_bpjs', compact('departemen', 'kantor', 'group', 'namabulan', 'bulan', 'tahun', 'jmlrange', 'rangetanggal', 'presensi', 'datalibur', 'dataliburpenggantiminggu', 'dataminggumasuk', 'datawfh', 'datawfhfull', 'datalembur', 'datalemburharilibur', 'sampai', 'cekUmk'));
            } else {
                if (isset($_POST['export'])) {
                    echo "EXPORT";
                    // Fungsi header dengan mengirimkan raw data excel
                    header("Content-type: application/vnd-ms-excel");
                    // Mendefinisikan nama file ekspor "hasil-export.xls"
                    header("Content-Disposition: attachment; filename=Laporan Gaji .xls");
                }
                return view('gaji.laporan.cetak_gaji', compact('departemen', 'kantor', 'group', 'namabulan', 'bulan', 'tahun', 'jmlrange', 'rangetanggal', 'presensi', 'datalibur', 'dataliburpenggantiminggu', 'dataminggumasuk', 'datawfh', 'datawfhfull', 'datalembur', 'datalemburharilibur', 'sampai'));
            }
        } else {
            if (isset($_POST['export'])) {
                echo "EXPORT";
                // Fungsi header dengan mengirimkan raw data excel
                header("Content-type: application/vnd-ms-excel");
                // Mendefinisikan nama file ekspor "hasil-export.xls"
                header("Content-Disposition: attachment; filename=Laporan Presensi Detail.xls");
            }
            return view('presensi.laporan.cetak', compact('departemen', 'kantor', 'group', 'namabulan', 'bulan', 'tahun', 'jmlrange', 'rangetanggal', 'presensi', 'datalibur', 'dataliburpenggantiminggu', 'dataminggumasuk', 'datawfh', 'datawfhfull', 'datalembur', 'datalemburharilibur'));
        }
    }


    public function slip($nik, $bulan, $tahun)
    {
        $bulan = $bulan * 1;
        $nik = Crypt::decrypt($nik);
        $bl = $bulan;
        $kode_potongan = "GJ" . $bulan . $tahun;
        if ($bulan == 1) {
            $lastbulan = 12;
            $lasttahun = $tahun - 1; //2023
        } else {
            $lastbulan = $bulan - 1;
            $lasttahun = $tahun;
        }

        if ($bulan == 12) {
            $nextbulan = 1;
            $nexttahun = $tahun + 1;
        } else {
            $nextbulan = $bulan + 1;
            $nexttahun = $tahun;
        }
        $lastbulan = $lastbulan < 10 ?  "0" . $lastbulan : $lastbulan;
        $bulan = $bulan < 10 ?  "0" . $bulan : $bulan;

        $dari = $lasttahun . "-" . $lastbulan . "-21";
        $sampai = $tahun . "-" . $bulan . "-20";



        $daribulangaji = $dari;
        $berlakugaji = $sampai;
        //dd($berlakugaji);

        $datalibur = ceklibur($dari, $sampai);
        $dataliburpenggantiminggu = cekliburpenggantiminggu($dari, $sampai);
        $dataminggumasuk = cekminggumasuk($dari, $sampai);
        $datawfh = cekwfh($dari, $sampai);
        $datawfhfull = cekwfhfull($dari, $sampai);
        $datalembur = ceklembur($dari, $sampai, 1);
        $datalemburharilibur = ceklembur($dari, $sampai, 2);

        // echo json_encode($datalembur);
        // die;



        // Define search list with multiple key=>value pair
        //$search_items = array('id_kantor' => "TSM", 'tanggal_libur' => "2023-06-17");

        // Call search and pass the array and
        // the search list
        //$res = cektgllibur($ceklibur, $search_items);
        //dd(empty($res));

        //dd($sampai);
        $select_date = "";
        $field_date = "";
        $i = 1;
        while (strtotime($dari) <= strtotime($sampai)) {
            $rangetanggal[] = $dari;
            $select_date .= "MAX(IF(tgl_presensi = '$dari', CONCAT(
                IFNULL(jam_in,'NA'),
                '|',IFNULL(jam_out,'NA'),
                '|',IFNULL(nama_jadwal,'NA'),
                '|',IFNULL(jam_kerja.jam_masuk,'NA'),
                '|',IFNULL(jam_kerja.jam_pulang,'NA'),
                '|',IFNULL(presensi.status,'NA'),
                '|',IFNULL(presensi.kode_izin,'NA'),
                '|',IFNULL(presensi.kode_izin_terlambat,'NA'),
                '|',IFNULL(presensi.kode_izin_pulang,'NA'),
                '|',IFNULL(pengajuan_izin.jam_keluar,'NA'),
                '|',IFNULL(pengajuan_izin.jam_masuk,'NA'),
                '|',IFNULL(jam_kerja.total_jam,'NA'),
                '|',IFNULL(pengajuan_izin.sid,'NA'),
                '|',IFNULL(jam_kerja.jam_awal_istirahat,'NA'),
                '|',IFNULL(jam_kerja.jam_istirahat,'NA'),
                '|',IFNULL(jam_kerja.total_jam,'NA'),
                '|',IFNULL(jam_kerja.lintashari,'NA'),
                '|',IFNULL(izinpulang.direktur,'NA'),
                '|',IFNULL(pengajuan_izin.direktur,'NA'),
                '|',IFNULL(pengajuan_izin.keperluan,'NA'),
                '|',IFNULL(izinterlambat.direktur,'NA'),
                '|',IFNULL(pengajuan_izin.jenis_cuti,'NA')
                ),NULL)) as hari_" . $i . ",";

            $field_date .= "hari_" . $i . ",";
            $i++;
            $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
        }


        // dd($bulan);
        //dd($rangetanggal);
        $jmlrange = count($rangetanggal);
        $lastrange = $jmlrange - 1;
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

        // if ($jmlrange == 30) {
        //     array_push($rangetanggal, $rangetanggal[$lastrange]);
        // } else if ($jmlrange == 29) {
        //     array_push($rangetanggal, $rangetanggal[$lastrange], $rangetanggal[$lastrange]);
        // }
        $jmlrange = count($rangetanggal);
        $lastrange = $jmlrange - 1;

        //dd($jmlrange);
        $query = Karyawan::query();

        //dd($jmlrange);
        $query->selectRaw("
                $field_date
                master_karyawan.*,nama_group,nama_dept,nama_jabatan,nama_cabang,klasifikasi,no_rekening,
                iu_masakerja,iu_lembur,iu_penempatan,iu_kpi,
                im_ruanglingkup, im_penempatan,im_kinerja,
                gaji_pokok,
                t_jabatan,t_masakerja,t_tanggungjawab,t_makan,t_istri,t_skill,
                cicilan_pjp,jml_kasbon,jml_nonpjp,jml_pengurang,jml_penambah,
                bpjs_kesehatan.perusahaan,bpjs_kesehatan.pekerja,bpjs_kesehatan.keluarga,iuran_kes,
                bpjs_tenagakerja.k_jht,bpjs_tenagakerja.k_jp,iuran_tk
            ");
        $query->leftJoin(
            DB::raw("(
            SELECT
                $select_date
                presensi.nik
            FROM
                presensi
            LEFT JOIN pengajuan_izin ON presensi.kode_izin = pengajuan_izin.kode_izin
            LEFT JOIN pengajuan_izin as izinpulang ON presensi.kode_izin_pulang = izinpulang.kode_izin
            LEFT JOIN pengajuan_izin as izinterlambat ON presensi.kode_izin_terlambat = izinterlambat.kode_izin
            LEFT JOIN jadwal_kerja ON presensi.kode_jadwal = jadwal_kerja.kode_jadwal
            LEFT JOIN jam_kerja ON presensi.kode_jam_kerja = jam_kerja.kode_jam_kerja
            WHERE tgl_presensi BETWEEN '$rangetanggal[0]' AND  '$sampai'
            GROUP BY
                presensi.nik
            ) presensi"),
            function ($join) {
                $join->on('presensi.nik', '=', 'master_karyawan.nik');
            }
        );
        $query->leftJoin('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id');
        $query->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftJoin('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang');
        $query->leftJoin(
            DB::raw("(
                    SELECT nik,gaji_pokok,t_jabatan,t_masakerja,t_tanggungjawab,
                    t_makan,t_istri,t_skill
                    FROM hrd_mastergaji
                    WHERE kode_gaji IN (SELECT MAX(kode_gaji) as kode_gaji FROM hrd_mastergaji
                    WHERE tgl_berlaku <= '$berlakugaji'  GROUP BY nik)
                ) hrdgaji"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'hrdgaji.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                    SELECT nik,iu_masakerja,iu_lembur,iu_penempatan,iu_kpi,
                    im_ruanglingkup,im_penempatan,im_kinerja
                    FROM hrd_masterinsentif WHERE kode_insentif IN (SELECT MAX(kode_insentif) as kode_insentif FROM hrd_masterinsentif
                    WHERE tgl_berlaku <= '$berlakugaji'  GROUP BY nik)
                ) hrdinsentif"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'hrdinsentif.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                    SELECT nik,perusahaan,pekerja,keluarga,iuran as iuran_kes
                    FROM bpjs_kesehatan WHERE kode_bpjs_kes IN (SELECT MAX(kode_bpjs_kes) as kode_bpjs_kes FROM bpjs_kesehatan
                    WHERE tgl_berlaku <= '$berlakugaji'  GROUP BY nik)
                ) bpjs_kesehatan"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'bpjs_kesehatan.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                    SELECT nik,k_jht,k_jp,iuran as iuran_tk
                    FROM bpjs_tenagakerja WHERE kode_bpjs_tk IN (SELECT MAX(kode_bpjs_tk) as kode_bpjs_tk FROM bpjs_tenagakerja
                    WHERE tgl_berlaku <= '$berlakugaji'  GROUP BY nik)
                ) bpjs_tenagakerja"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'bpjs_tenagakerja.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                   SELECT nik, SUM(jumlah) as cicilan_pjp
                   FROM pinjaman_historibayar
                   INNER JOIN pinjaman ON pinjaman_historibayar.no_pinjaman = pinjaman.no_pinjaman
                   WHERE kode_potongan = '$kode_potongan'
                   GROUP BY nik
                ) pjp"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'pjp.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                   SELECT nik, SUM(jumlah) as jml_kasbon
                   FROM kasbon_historibayar
                   INNER JOIN kasbon ON kasbon_historibayar.no_kasbon = kasbon.no_kasbon
                   WHERE kode_potongan = '$kode_potongan'
                   GROUP BY nik
                ) kasbon"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'kasbon.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                   SELECT nik, SUM(jumlah) as jml_pengurang, SUM(jumlah_penambah) as jml_penambah
                   FROM pengurang_gaji
                   WHERE kode_gaji = '$kode_potongan'
                   GROUP BY nik
                ) penguranggaji"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'penguranggaji.nik');
            }
        );

        $query->leftJoin(
            DB::raw("(
                   SELECT nik, SUM(jumlah) as jml_nonpjp
                   FROM pinjaman_nonpjp_historibayar
                   INNER JOIN pinjaman_nonpjp ON pinjaman_nonpjp_historibayar.no_pinjaman_nonpjp = pinjaman_nonpjp.no_pinjaman_nonpjp
                   WHERE kode_potongan = '$kode_potongan'
                   GROUP BY nik
                ) pinjamannonpjp"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'pinjamannonpjp.nik');
            }
        );


        $query->where('master_karyawan.nik', $nik);
        $query->where('status_aktif', 1);
        $query->where('tgl_masuk', '<=', $sampai);
        $query->orWhere('status_aktif', 0);
        $query->where('tgl_off_gaji', '>=', $daribulangaji);
        $query->where('tgl_masuk', '<=', $sampai);
        $query->where('master_karyawan.nik', $nik);

        $presensi = $query->get();



        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('gaji.laporan.slip', compact(
            'bulan',
            'tahun',
            'namabulan',
            'jmlrange',
            'rangetanggal',
            'presensi',
            'datalibur',
            'dataliburpenggantiminggu',
            'dataminggumasuk',
            'datawfh',
            'datawfhfull',
            'datalembur',
            'datalemburharilibur',
            'sampai'
        ));
    }


    public function rekapterlambat()
    {
        $departemen = DB::table('hrd_departemen')->orderBy('nama_dept')->get();
        $cbg = new Cabang();
        if (Auth::user()->kode_cabang == "PCF" || Auth::user()->kode_cabang == "PST") {
            if (
                Auth::user()->level == "manager hrd" || Auth::user()->level == "admin"
                || Auth::user()->level == "manager accounting"
                || Auth::user()->level == "spv presensi"
                || Auth::user()->level == "rom"
            ) {
                $cabang = $cbg->getCabang("PST");
            } else {
                $cabang = DB::table('cabang')->where('kode_cabang', 'PST')->get();
            }
        } else {
            $cabang = $cbg->getCabang(Auth::user()->kode_cabang);
        }
        return view('presensi.laporan.rekapterlambat', compact('departemen', 'cabang'));
    }


    public function cetakrekapterlambat(Request $request)
    {
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $id_group = $request->id_group;
        $dari = $request->dari;
        $sampai = $request->sampai;


        //dd($presensi);

        $departemen = DB::table('hrd_departemen')->where('kode_dept', $kode_dept)->first();
        $kantor = DB::table('cabang')->where('kode_cabang', $id_kantor)->first();
        $group = DB::table('hrd_group')->where('id', $id_group)->first();
        return view('presensi.laporan.cetakrekapketerlambatan', compact('departemen', 'kantor', 'group', 'dari', 'sampai'));
    }


    public function presensipsm()
    {

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $departemen = DB::table('hrd_departemen')->orderBy('nama_dept')->get();
        $cbg = new Cabang();
        if (Auth::user()->kode_cabang == "PCF" || Auth::user()->kode_cabang == "PST") {
            if (
                Auth::user()->level == "manager hrd" || Auth::user()->level == "admin"
                || Auth::user()->level == "manager accounting"
                || Auth::user()->level == "spv presensi"
                || Auth::user()->level == "rom"
            ) {
                $cabang = $cbg->getCabang("PST");
            } else {
                $cabang = DB::table('cabang')->where('kode_cabang', 'PST')->get();
            }
        } else {
            $cabang = $cbg->getCabang(Auth::user()->kode_cabang);
        }
        $show_all = config('global.show_all');
        return view('presensi.laporan.lap_presensipsm', compact('bulan', 'departemen', 'cabang', 'show_all'));
    }

    public function getterlambat(Request $request)
    {
        $id_kantor = $request->id_kantor;
        $kode_dept = $request->kode_dept;
        $id_group = $request->id_group;
        $dari = $request->dari;
        $sampai = $request->sampai;
        $query = Presensi::query();
        $query->select(
            'tgl_presensi',
            'presensi.nik',
            'nama_karyawan',
            'tgl_masuk',
            'master_karyawan.kode_dept',
            'nama_dept',
            'jenis_kelamin',
            'nama_jabatan',
            'id_perusahaan',
            'id_kantor',
            'klasifikasi',
            'status_karyawan',
            'presensi.kode_jadwal',
            'nama_jadwal',
            'jam_kerja.jam_masuk',
            'jam_kerja.jam_pulang',
            'jam_in',
            'jam_out',
            'presensi.status as status_presensi',
            'presensi.kode_izin',
            'kode_izin_terlambat',
            'pengajuan_izin.status as status_izin',
            'pengajuan_izin.jenis_izin',
            'pengajuan_izin.jam_keluar',
            'pengajuan_izin.jam_masuk as jam_masuk_kk',
            'total_jam',
            'kode_izin_pulang',
            'jam_istirahat',
            'jam_awal_istirahat',
            'sid',
            'jadwal_kerja.kode_cabang as jadwalcabang',
            'lokasi_in',
            'lokasi_out',
            'presensi.id',
            'pin',
            'check_denda'
        );
        $query->join('master_karyawan', 'presensi.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftjoin('jadwal_kerja', 'presensi.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal');
        $query->leftjoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja');
        $query->leftjoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin');
        if (!empty($id_kantor)) {
            $query->where('master_karyawan.id_kantor', $id_kantor);
        }

        if (!empty($kode_dept)) {
            $query->where('master_karyawan.kode_dept', $kode_dept);
        }

        if (!empty($id_group)) {
            $query->where('master_karyawan.grup', $id_group);
        }

        $query->whereRaw('DATE_FORMAT(jam_in, "%H:%i") > DATE_FORMAT(jam_kerja.jam_masuk,"%H:%i")');
        $query->whereBetween('tgl_presensi', [$dari, $sampai]);
        $query->orderBy('presensi.nik');
        $presensi = $query->get();

        return view('presensi.laporan.loadterlambat', compact('presensi', 'dari', 'sampai', 'id_kantor', 'kode_dept', 'id_group'));
    }


    public function updatecheckdenda(Request $request)
    {
        $id = $request->id;
        try {
            DB::table('presensi')->where('id', $id)->update(['check_denda' => 1]);
            return 0;
        } catch (\Exception $e) {
            return $e;
        }
    }


    public function cancelcheckdenda(Request $request)
    {
        $id = $request->id;
        try {
            DB::table('presensi')->where('id', $id)->update(['check_denda' => null]);
            return 0;
        } catch (\Exception $e) {
            return $e;
        }
    }
}
