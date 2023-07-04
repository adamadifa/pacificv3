<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanhrdController extends Controller
{
    public function presensi()
    {

        $bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $departemen = DB::table('hrd_departemen')->orderBy('nama_dept')->get();
        $cbg = new Cabang();
        $cabang = $cbg->getCabang(Auth::user()->kode_cabang);
        return view('presensi.laporan.lap_presensi', compact('bulan', 'departemen', 'cabang'));
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
        $departemen = DB::table('master_karyawan')
            ->select('master_karyawan.kode_dept', 'nama_dept')
            ->where('id_kantor', $id_kantor)
            ->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->groupBy('master_karyawan.kode_dept')
            ->get();
        echo "<option value=''>Semua Departemen</option>";
        foreach ($departemen as $d) {
            echo "<option value='$d->kode_dept'>$d->nama_dept</option>";
        }
    }

    public function getgroup(Request $request)
    {
        $id_kantor = $request->id_kantor;
        $group = DB::table('master_karyawan')
            ->select('master_karyawan.grup', 'nama_group')
            ->join('hrd_group', 'master_karyawan.grup', '=', 'hrd_group.id')
            ->where('id_kantor', $id_kantor)
            ->groupByRaw('master_karyawan.grup,nama_group')
            ->get();
        return view('presensi.laporan.getgroup', compact('group'));
    }


    public function cetakpresensi(Request $request)
    {

        //dd(ceklibur('2023-06-18', 'BDG'));
        $kode_dept = $request->kode_dept;
        $id_kantor = $request->id_kantor;
        $id_group = $request->id_group;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        if ($bulan == 1) {
            $lastbulan = 12;
            $lasttahun = $tahun - 1;
        } else {
            $lastbulan = $bulan - 1;
            $lasttahun = $tahun;
        }

        $lastbulan = $lastbulan < 10 ?  "0" . $lastbulan : $lastbulan;
        $bulan = $bulan < 10 ?  "0" . $bulan : $lastbulan;

        $dari = $lasttahun . "-" . $lastbulan . "-21";
        $sampai = $tahun . "-" . $bulan . "-20";


        $datalibur = ceklibur($dari, $sampai);
        $dataliburpenggantiminggu = cekliburpenggantiminggu($dari, $sampai);
        $dataminggumasuk = cekminggumasuk($dari, $sampai);
        $datawfh = cekwfh($dari, $sampai);

        // Define search list with multiple key=>value pair
        //$search_items = array('id_kantor' => "TSM", 'tanggal_libur' => "2023-06-17");

        // Call search and pass the array and
        // the search list
        //$res = cektgllibur($ceklibur, $search_items);
        //dd(empty($res));


        while (strtotime($dari) <= strtotime($sampai)) {
            $rangetanggal[] = $dari;
            $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
        }

        //dd($rangetanggal);
        $jmlrange = count($rangetanggal);
        $lastrange = $jmlrange - 1;
        $namabulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
        $departemen = DB::table('hrd_departemen')->where('kode_dept', $kode_dept)->first();
        $kantor = DB::table('cabang')->where('kode_cabang', $id_kantor)->first();
        $group = DB::table('hrd_group')->where('id', $id_group)->first();


        $query = Karyawan::query();


        if ($jmlrange == 31) {
            $query->selectRaw('master_karyawan.nik,nama_karyawan,kode_dept,id_kantor,
                hari_1,
                hari_2,
                hari_3,
                hari_4,
                hari_5,
                hari_6,
                hari_7,
                hari_8,
                hari_9,
                hari_10,
                hari_11,
                hari_12,
                hari_13,
                hari_14,
                hari_15,
                hari_16,
                hari_17,
                hari_18,
                hari_19,
                hari_20,
                hari_21,
                hari_22,
                hari_23,
                hari_24,
                hari_25,
                hari_26,
                hari_27,
                hari_28,
                hari_29,
                hari_30,
                hari_31
            ');
            $query->leftJoin(
                DB::raw("(
            SELECT
                presensi.nik,
                MAX(IF(tgl_presensi = '$rangetanggal[0]',CONCAT(
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
                '|',IFNULL(jam_kerja.lintashari,'NA')
                ),NULL)) as hari_1,

                MAX(IF(tgl_presensi = '$rangetanggal[1]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_2,

                MAX(IF(tgl_presensi = '$rangetanggal[2]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_3,

                MAX(IF(tgl_presensi = '$rangetanggal[3]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_4,

                MAX(IF(tgl_presensi = '$rangetanggal[4]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_5,


                MAX(IF(tgl_presensi = '$rangetanggal[5]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_6,

                MAX(IF(tgl_presensi = '$rangetanggal[6]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_7,

                MAX(IF(tgl_presensi = '$rangetanggal[7]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_8,

                MAX(IF(tgl_presensi = '$rangetanggal[8]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_9,


                MAX(IF(tgl_presensi = '$rangetanggal[9]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_10,

                MAX(IF(tgl_presensi = '$rangetanggal[10]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_11,

                MAX(IF(tgl_presensi = '$rangetanggal[11]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_12,

                MAX(IF(tgl_presensi = '$rangetanggal[12]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_13,

                MAX(IF(tgl_presensi = '$rangetanggal[13]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_14,

                MAX(IF(tgl_presensi = '$rangetanggal[14]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_15,

                MAX(IF(tgl_presensi = '$rangetanggal[15]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_16,


                MAX(IF(tgl_presensi = '$rangetanggal[16]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_17,

                MAX(IF(tgl_presensi = '$rangetanggal[17]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_18,

                MAX(IF(tgl_presensi = '$rangetanggal[18]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_19,

                MAX(IF(tgl_presensi = '$rangetanggal[19]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_20,

                MAX(IF(tgl_presensi = '$rangetanggal[20]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_21,


                MAX(IF(tgl_presensi = '$rangetanggal[21]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_22,

                MAX(IF(tgl_presensi = '$rangetanggal[22]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_23,

                MAX(IF(tgl_presensi = '$rangetanggal[23]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_24,

                MAX(IF(tgl_presensi = '$rangetanggal[24]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_25,

                MAX(IF(tgl_presensi = '$rangetanggal[25]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_26,

                MAX(IF(tgl_presensi = '$rangetanggal[26]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_27,


                MAX(IF(tgl_presensi = '$rangetanggal[27]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_28,

                MAX(IF(tgl_presensi = '$rangetanggal[28]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_29,

                MAX(IF(tgl_presensi = '$rangetanggal[29]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_30,



                MAX(IF(tgl_presensi = '$rangetanggal[30]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_31
            FROM
                presensi
            LEFT JOIN pengajuan_izin ON presensi.kode_izin = pengajuan_izin.kode_izin
            LEFT JOIN jadwal_kerja ON presensi.kode_jadwal = jadwal_kerja.kode_jadwal
            LEFT JOIN jam_kerja ON presensi.kode_jam_kerja = jam_kerja.kode_jam_kerja
            WHERE tgl_presensi BETWEEN '$rangetanggal[0]' AND  '$rangetanggal[$lastrange]'
            GROUP BY
                presensi.nik
            ) presensi"),
                function ($join) {
                    $join->on('presensi.nik', '=', 'master_karyawan.nik');
                }
            );
        } elseif ($jmlrange == 30) {
            $query->selectRaw('master_karyawan.nik,nama_karyawan,kode_dept,id_kantor,
                hari_1,
                hari_2,
                hari_3,
                hari_4,
                hari_5,
                hari_6,
                hari_7,
                hari_8,
                hari_9,
                hari_10,
                hari_11,
                hari_12,
                hari_13,
                hari_14,
                hari_15,
                hari_16,
                hari_17,
                hari_18,
                hari_19,
                hari_20,
                hari_21,
                hari_22,
                hari_23,
                hari_24,
                hari_25,
                hari_26,
                hari_27,
                hari_28,
                hari_29,
                hari_30
            ');
            $query->leftJoin(
                DB::raw("(
                SELECT
                presensi.nik,
                MAX(IF(tgl_presensi = '$rangetanggal[0]',CONCAT(
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
                '|',IFNULL(jam_kerja.lintashari,'NA')
                ),NULL)) as hari_1,

                MAX(IF(tgl_presensi = '$rangetanggal[1]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_2,

                MAX(IF(tgl_presensi = '$rangetanggal[2]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_3,

                MAX(IF(tgl_presensi = '$rangetanggal[3]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_4,

                MAX(IF(tgl_presensi = '$rangetanggal[4]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_5,


                MAX(IF(tgl_presensi = '$rangetanggal[5]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_6,

                MAX(IF(tgl_presensi = '$rangetanggal[6]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_7,

                MAX(IF(tgl_presensi = '$rangetanggal[7]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_8,

                MAX(IF(tgl_presensi = '$rangetanggal[8]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_9,


                MAX(IF(tgl_presensi = '$rangetanggal[9]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_10,

                MAX(IF(tgl_presensi = '$rangetanggal[10]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_11,

                MAX(IF(tgl_presensi = '$rangetanggal[11]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_12,

                MAX(IF(tgl_presensi = '$rangetanggal[12]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_13,

                MAX(IF(tgl_presensi = '$rangetanggal[13]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_14,

                MAX(IF(tgl_presensi = '$rangetanggal[14]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_15,

                MAX(IF(tgl_presensi = '$rangetanggal[15]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_16,


                MAX(IF(tgl_presensi = '$rangetanggal[16]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_17,

                MAX(IF(tgl_presensi = '$rangetanggal[17]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_18,

                MAX(IF(tgl_presensi = '$rangetanggal[18]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_19,

                MAX(IF(tgl_presensi = '$rangetanggal[19]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_20,

                MAX(IF(tgl_presensi = '$rangetanggal[20]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_21,


                MAX(IF(tgl_presensi = '$rangetanggal[21]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_22,

                MAX(IF(tgl_presensi = '$rangetanggal[22]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_23,

                MAX(IF(tgl_presensi = '$rangetanggal[23]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_24,

                MAX(IF(tgl_presensi = '$rangetanggal[24]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_25,

                MAX(IF(tgl_presensi = '$rangetanggal[25]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_26,

                MAX(IF(tgl_presensi = '$rangetanggal[26]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_27,


                MAX(IF(tgl_presensi = '$rangetanggal[27]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_28,

                MAX(IF(tgl_presensi = '$rangetanggal[28]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_29,

                MAX(IF(tgl_presensi = '$rangetanggal[29]',CONCAT(
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
                    '|',IFNULL(jam_kerja.lintashari,'NA')
                    ),NULL)) as hari_30
            FROM
                presensi
            LEFT JOIN pengajuan_izin ON presensi.kode_izin = pengajuan_izin.kode_izin
            LEFT JOIN jadwal_kerja ON presensi.kode_jadwal = jadwal_kerja.kode_jadwal
            LEFT JOIN jam_kerja ON presensi.kode_jam_kerja = jam_kerja.kode_jam_kerja
            WHERE tgl_presensi BETWEEN '$rangetanggal[0]' AND  '$rangetanggal[$lastrange]'
            GROUP BY
                presensi.nik
            ) presensi"),
                function ($join) {
                    $join->on('presensi.nik', '=', 'master_karyawan.nik');
                }
            );
        }

        if (!empty($kode_dept)) {
            $query->where('master_karyawan.kode_dept', $kode_dept);
        }

        if (!empty($id_kantor)) {
            $query->where('master_karyawan.id_kantor', $id_kantor);
        }

        if (!empty($id_group)) {
            $query->where('master_karyawan.grup', $id_group);
        }

        $query->orderBy('nama_karyawan');
        $presensi = $query->get();
        //dd($presensi);
        return view('presensi.laporan.cetak', compact('departemen', 'kantor', 'group', 'namabulan', 'bulan', 'tahun', 'jmlrange', 'rangetanggal', 'presensi', 'datalibur', 'dataliburpenggantiminggu', 'dataminggumasuk', 'datawfh'));
    }
}
