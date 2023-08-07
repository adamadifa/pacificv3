<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Presensi;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PresensiController extends Controller
{

    public function hari_ini()
    {
        $hari = date("D");

        switch ($hari) {
            case 'Sun':
                $hari_ini = "Minggu";
                break;

            case 'Mon':
                $hari_ini = "Senin";
                break;

            case 'Tue':
                $hari_ini = "Selasa";
                break;

            case 'Wed':
                $hari_ini = "Rabu";
                break;

            case 'Thu':
                $hari_ini = "Kamis";
                break;

            case 'Fri':
                $hari_ini = "Jumat";
                break;

            case 'Sat':
                $hari_ini = "Sabtu";
                break;

            default:
                $hari_ini = "Tidak di ketahui";
                break;
        }

        return $hari_ini;
    }

    public function monitoring(Request $request)
    {
        $level = Auth::user()->level;
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;
        $cabang = Auth::user()->kode_cabang;
        $nama_karyawan = $request->nama_karyawan_search;
        $tanggal = !empty($request->tanggal) ? $request->tanggal : date('Y-m-d');
        // $tanggal = date("Y-m-d");
        $query = Karyawan::query();
        $query->select('master_karyawan.nik', 'nama_karyawan', 'tgl_masuk', 'master_karyawan.kode_dept', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan', 'presensi.kode_jadwal', 'nama_jadwal', 'jam_kerja.jam_masuk', 'jam_kerja.jam_pulang', 'jam_in', 'jam_out', 'presensi.status as status_presensi', 'presensi.kode_izin', 'kode_izin_terlambat', 'tgl_presensi', 'pengajuan_izin.status as status_izin', 'pengajuan_izin.jenis_izin', 'pengajuan_izin.jam_keluar', 'pengajuan_izin.jam_masuk as jam_masuk_kk', 'total_jam', 'kode_izin_pulang', 'jam_istirahat', 'jam_awal_istirahat', 'sid', 'jadwal_kerja.kode_cabang as jadwalcabang', 'lokasi_in', 'lokasi_out', 'presensi.id', 'pin', 'lintashari');
        $query->leftjoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftjoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');

        $query->leftJoin(
            DB::raw("(
            SELECT
                *
            FROM
                presensi
            WHERE tgl_presensi = '$tanggal'
            ) presensi"),
            function ($join) {
                $join->on('master_karyawan.nik', '=', 'presensi.nik');
            }
        );
        $query->leftjoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin');

        $query->leftjoin('jadwal_kerja', 'presensi.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal');
        $query->leftjoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja');

        if (Auth::user()->id != 69) {
            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }
            if (!empty($request->kode_dept_search)) {
                $query->where('master_karyawan.kode_dept', $request->kode_dept_search);
            }

            if (!empty($request->id_perusahaan_search)) {
                $query->where('master_karyawan.id_perusahaan', $request->id_perusahaan_search);
            }

            if (!empty($request->id_kantor_search)) {
                $query->where('master_karyawan.id_kantor', $request->id_kantor_search);
            }

            if (!empty($request->grup_search)) {
                $query->where('master_karyawan.grup', $request->grup_search);
            }

            if (!empty($kode_dept_presensi)) {
                $query->where('master_karyawan.kode_dept', $kode_dept_presensi);
                if ($cabang == "PCF") {
                    $query->where('master_karyawan.id_kantor', 'PST');
                } else {
                    $query->where('master_karyawan.id_kantor', $cabang);
                }
            }

            if (!empty(Auth::user()->pic_presensi)) {
                if ($cabang != "PCF") {
                    $query->where('master_karyawan.id_kantor', $cabang);
                }
            }


            $query->where('status_aktif', 1);
            // if ($level == "kepala admin") {
            //     $query->where('id_kantor', $cabang);
            //     $query->where('id_perusahaan', "MP");
            // }

            // if ($level == "kepala penjualan") {
            //     $query->where('id_kantor', $cabang);
            //     $query->where('id_perusahaan', "PCF");
            // }

            // if ($level == "manager pembelian") {
            //     $query->where('master_karyawan.kode_dept', 'PMB');
            // }

            // if ($level == "kepala gudang") {
            //     $query->where('master_karyawan.kode_dept', 'GDG');
            // }

            // if ($level == "manager produksi") {
            //     $query->where('master_karyawan.kode_dept', 'PRD');
            // }

            // if ($level == "manager ga") {
            //     $query->where('master_karyawan.kode_dept', 'GAF');
            // }

            // if ($level == "emf") {
            //     $query->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'PDQ']);
            // }


            // if ($level == "manager marketing") {
            //     $query->where('master_karyawan.kode_dept', 'MKT');
            // }

            // if ($level == "rsm") {
            //     $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            //     $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            //     $query->whereIn('master_karyawan.id_kantor', $list_wilayah);
            // }




        }

        if (Auth::user()->id == 69) {
            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }
            if (!empty($request->kode_dept_search)) {
                $query->where('master_karyawan.kode_dept', $request->kode_dept_search);
            }

            if (!empty($request->id_perusahaan_search)) {
                $query->where('master_karyawan.id_perusahaan', $request->id_perusahaan_search);
            }

            if (!empty($request->id_kantor_search)) {
                $query->where('master_karyawan.id_kantor', $request->id_kantor_search);
            }

            if (!empty($request->grup_search)) {
                $query->where('master_karyawan.grup', $request->grup_search);
            }

            if (!empty($kode_dept_presensi)) {
                $query->where('master_karyawan.kode_dept', $kode_dept_presensi);
                if ($cabang == "PCF") {
                    $query->where('master_karyawan.id_kantor', 'PST');
                } else {
                    $query->where('master_karyawan.id_kantor', $cabang);
                }
            }

            if (!empty(Auth::user()->pic_presensi)) {
                if ($cabang != "PCF") {
                    $query->where('master_karyawan.id_kantor', $cabang);
                }
            }


            $query->where('grup', 11);
            $query->where('id_kantor', 'PST');
            $query->where('nama_jabatan', '!=', 'MANAGER');
            $query->where('status_aktif', 1);
            $query->orWhere('grup', 4);
            $query->where('id_kantor', 'PST');
            $query->where('nama_jabatan', 'MANAGER');
            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }
            if (!empty($request->kode_dept_search)) {
                $query->where('master_karyawan.kode_dept', $request->kode_dept_search);
            }

            if (!empty($request->id_perusahaan_search)) {
                $query->where('master_karyawan.id_perusahaan', $request->id_perusahaan_search);
            }

            if (!empty($request->id_kantor_search)) {
                $query->where('master_karyawan.id_kantor', $request->id_kantor_search);
            }

            if (!empty($request->grup_search)) {
                $query->where('master_karyawan.grup', $request->grup_search);
            }

            if (!empty($kode_dept_presensi)) {
                $query->where('master_karyawan.kode_dept', $kode_dept_presensi);
                if ($cabang == "PCF") {
                    $query->where('master_karyawan.id_kantor', 'PST');
                } else {
                    $query->where('master_karyawan.id_kantor', $cabang);
                }
            }

            if (!empty(Auth::user()->pic_presensi)) {
                if ($cabang != "PCF") {
                    $query->where('master_karyawan.id_kantor', $cabang);
                }
            }

            $query->where('status_aktif', 1);
        }

        if (Auth::user()->id == 73) {
            $query->where('grup', 9);
            $query->where('id_kantor', 'PST');
        }

        $query->orderBy('nama_karyawan');
        $karyawan = $query->paginate(40);
        $karyawan->appends($request->all());
        $kantor = DB::table('cabang')->orderBy('kode_cabang')->get();
        $departemen = DB::table('hrd_departemen')->get();
        $group = DB::table('hrd_group')->orderBy('nama_group')->get();
        return view('presensi.monitoring', compact('karyawan', 'departemen', 'kantor', 'group'));
    }

    public function updatepresensi(Request $request)
    {
        $nik = $request->nik;
        $tgl = $request->tgl;
        $kode_jadwal = $request->kode_jadwal;
        $cek = DB::table('presensi')->where('tgl_presensi', $tgl)->where('nik', $nik)->first();
        $karyawan = DB::table('master_karyawan')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->where('nik', $nik)->first();
        if (Auth::user()->level == "admin" || Auth::user()->level == "manager hrd") {
            $jadwal = DB::table('jadwal_kerja')
                ->orderBy('kode_jadwal')->get();
        } else {
            $jadwal = DB::table('jadwal_kerja')
                ->where('kode_cabang', $karyawan->id_kantor)
                ->orderBy('kode_jadwal')->get();
        }

        return view('presensi.updatepresensi', compact('karyawan', 'tgl', 'jadwal', 'cek', 'kode_jadwal'));
    }

    public function storeupdatepresensi(Request $request)
    {
        $nik = $request->nik;
        $tgl_presensi = $request->tgl_presensi;
        $kode_jadwal = $request->kode_jadwal;
        $kode_jam_kerja = $request->kode_jam_kerja;
        $jam_masuk = $request->status == "h" && !empty($request->jam_masuk) ?  $tgl_presensi . " " . $request->jam_masuk : null;
        $jam_pulang =  $request->status == "h" && !empty($request->jam_pulang) ? $tgl_presensi . " " . $request->jam_pulang :  null;



        $nextday = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
        $tgl = date("D", strtotime($tgl_presensi));

        $cekperjalanandinas = DB::table('pengajuan_izin')
            ->where('status', 'p')
            ->whereRaw('"' . $tgl_presensi . '" >= dari')
            ->whereRaw('"' . $tgl_presensi . '" <= sampai')
            ->where('nik', $nik)
            ->first();

        if ($cekperjalanandinas != null) {
            $cekjadwaldinas = DB::table('jadwal_kerja')
                ->where('nama_jadwal', 'NON SHIFT')
                ->where('kode_cabang', $cekperjalanandinas->kode_cabang)->first();
            $kode_jadwal = $cekjadwaldinas->kode_jadwal;
        }
        // $ceklibur = DB::table('harilibur')->where('tanggal_limajam', $tgl_presensi)->count();
        // if ($ceklibur > 0) {
        //     $hariini = "Sabtu";
        // } else {
        //     $hariini = hari($tgl);
        // }
        $hariini = hari($tgl);
        $jadwal = DB::table('jadwal_kerja_detail')
            ->join('jadwal_kerja', 'jadwal_kerja_detail.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
            ->where('hari', $hariini)->where('jadwal_kerja_detail.kode_jadwal', $kode_jadwal)
            ->first();
        $jam_kerja = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->first();


        $lintashari  = $jam_kerja->lintashari;
        if (!empty($lintashari)) {
            if (!empty($request->jam_pulang)) {
                $jam_pulang = $nextday . " " . $request->jam_pulang;
            } else {
                $jam_pulang = null;
            }
        }

        //dd($jam_kerja);

        //dd($jam_pulang);
        $cekizinterlambat = DB::table('pengajuan_izin')->where('nik', $nik)->where('dari', $tgl_presensi)->where('jenis_izin', 'TL')->where('status_approved', 1)->first();

        $kode_izin = $cekizinterlambat != null  ? $cekizinterlambat->kode_izin : NULL;

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->first();
        $kode_jam_kerja = !empty($request->kode_jam_kerja) ? $request->kode_jam_kerja  : $jadwal->kode_jam_kerja;


        //dd($jam_pulang);
        if ($cek == null) {
            $data = [
                'nik' => $nik,
                'tgl_presensi' => $tgl_presensi,
                'jam_in' => $jam_masuk,
                'jam_out' => $jam_pulang,
                'kode_jadwal' => $kode_jadwal,
                'kode_jam_kerja' => $kode_jam_kerja,
                'kode_izin_terlambat' => $kode_izin,
                'status' => $request->status,
            ];

            try {
                DB::table('presensi')->insert($data);
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
            }
        } else {

            if ($request->status == "h") {
                $data = [
                    'jam_in' => $jam_masuk,
                    'jam_out' => $jam_pulang,
                    'kode_jadwal' => $kode_jadwal,
                    'kode_jam_kerja' => $kode_jam_kerja,
                    'status' => $request->status,
                ];
            } else {
                $data = [
                    'jam_in' => NULL,
                    'jam_out' => NULL,
                    'kode_jadwal' => $kode_jadwal,
                    'kode_jam_kerja' => $jadwal->kode_jam_kerja,
                    'status' => $request->status,
                ];
            }

            //dd($data);
            try {
                DB::table('presensi')
                    ->where('id', $cek->id)
                    ->update($data);
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            } catch (\Exception $e) {
                dd($e);
                return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
            }
        }
    }


    public function getjamkerja(Request $request)
    {
        $kode_jadwal = $request->kode_jadwal;
        $kode_jam_kerja = $request->kode_jam_kerja;
        $jam_kerja = DB::table('jadwal_kerja_detail')
            ->select('jadwal_kerja_detail.kode_jam_kerja', 'jam_masuk', 'jam_pulang', 'total_jam')
            ->leftJoin('jam_kerja', 'jadwal_kerja_detail.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('kode_jadwal', $kode_jadwal)
            ->groupByRaw('jadwal_kerja_detail.kode_jam_kerja,jam_masuk,jam_pulang,total_jam')
            ->get();


        echo "<option>Pilih Jam Kerja</option>";
        foreach ($jam_kerja as $d) {
            $selected = $d->kode_jam_kerja == $kode_jam_kerja  ? "selected" : "";
            echo "<option value='" . $d->kode_jam_kerja . "' $selected>" . $d->kode_jam_kerja . "-" . $d->jam_masuk . " s/d " . $d->jam_pulang . " (" . $d->total_jam . " Jam Kerja) </option>";
        }
    }


    public function show(Request $request)
    {
        $id = $request->id;
        $status = $request->status;
        $presensi = DB::table('presensi')
            ->join('master_karyawan', 'presensi.nik', '=', 'master_karyawan.nik')
            ->leftJoin('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('id', $id)->first();

        $lokasi_cabang = $presensi->lokasi_cabang;
        $lokasi = explode(",", $lokasi_cabang);
        $latitude = $lokasi[0];
        $longitude = $lokasi[1];

        if ($status == "in") {
            if (!empty($presensi->lokasi_in)) {
                return view('presensi.show', compact('presensi', 'latitude', 'longitude'));
            } else {
                return view('presensi.shownonmaps', compact('presensi', 'status'));
            }
        } else if ($status == "out") {
            if (!empty($presensi->lokasi_out)) {
                return view('presensi.show_out', compact('presensi', 'latitude', 'longitude'));
            } else {
                return view('presensi.shownonmaps', compact('presensi', 'status'));
            }
        }
    }


    public function checkmesin(Request $request)
    {
        $tanggal = $request->tanggal;
        $pin = $request->pin;
        $kode_jadwal = $request->kode_jadwal;
        if ($kode_jadwal == "JD004") {
            $nextday = date('Y-m-d', strtotime('+1 day', strtotime($tanggal)));
        } else {
            $nextday =  $tanggal;
        }
        $specific_value = $pin;


        //Mesin 1
        $url = 'https://developer.fingerspot.io/api/get_attlog';
        $data = '{"trans_id":"1", "cloud_id":"C2609075E3170B2C", "start_date":"' . $tanggal . '", "end_date":"' . $nextday . '"}';
        $authorization = "Authorization: Bearer QNBCLO9OA0AWILQD";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($result);
        $datamesin1 = $res->data;

        $filtered_array = array_filter($datamesin1, function ($obj) use ($specific_value) {
            return $obj->pin == $specific_value;
        });


        //Mesin 2
        $url = 'https://developer.fingerspot.io/api/get_attlog';
        $data = '{"trans_id":"1", "cloud_id":"C268909557211236", "start_date":"' . $tanggal . '", "end_date":"' . $nextday . '"}';
        $authorization = "Authorization: Bearer QNBCLO9OA0AWILQD";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result2 = curl_exec($ch);
        curl_close($ch);
        $res2 = json_decode($result2);
        $datamesin2 = $res2->data;

        $filtered_array_2 = array_filter($datamesin2, function ($obj) use ($specific_value) {
            return $obj->pin == $specific_value;
        });

        return view('presensi.getmesin', compact('filtered_array', 'filtered_array_2'));
    }




    public function presensikaryawan(Request $request)
    {



        $dari = $request->dari;
        $sampai = $request->sampai;
        $nik = $request->nik;
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;

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
            'lintashari'
        );
        $query->join('master_karyawan', 'presensi.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftjoin('jadwal_kerja', 'presensi.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal');
        $query->leftjoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja');
        $query->leftjoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin');
        $query->whereBetween('tgl_presensi', [$dari, $sampai]);
        $query->where('presensi.nik', $nik);
        $query->orderBy('presensi.tgl_presensi');
        $karyawan = $query->get();

        $kar = new Karyawan();
        $listkaryawan = $kar->getkaryawanpengajuan($kode_dept_presensi);
        return view('presensi.presensikaryawan', compact('karyawan', 'listkaryawan'));
    }


    public function updatefrommachine($pin, $status_scan, $scan_date)
    {
        $pin = Crypt::decrypt($pin);
        // echo $status_scan;
        // die;
        $status_scan = $status_scan % 2 == 0 ? 0 : 1;
        echo $status_scan;
        //echo $pin . " " . $status_scan . " " . $scan_date;
        $scan = $scan_date;

        $tgl_presensi   = date("Y-m-d", strtotime($scan));
        $karyawan       = DB::table('master_karyawan')->where('pin', $pin)->first();
        if ($karyawan == null) {
            echo "PIN Tidak Ditemukan";
            $nik = "";
        } else {
            $nik = $karyawan->nik;
        }



        //Cek Perjalanan Dinas
        $cekperjalanandinas = DB::table('pengajuan_izin')
            ->where('status', 'p')
            ->whereRaw('"' . $tgl_presensi . '" >= dari')
            ->whereRaw('"' . $tgl_presensi . '" <= sampai')
            ->where('nik', $nik)
            ->first();
        if ($cekperjalanandinas != null) {
            $kode_cabang = $cekperjalanandinas->kode_cabang;
        } else {
            $kode_cabang = $karyawan->id_kantor;
        }


        //Tanggal Sebelumnya
        $lastday = date('Y-m-d', strtotime('-1 day', strtotime($tgl_presensi)));

        $jam = $scan;

        //Cek Jadwal SHIFT

        $cekjadwalshift = DB::table('konfigurasi_jadwalkerja_detail')
            ->join('konfigurasi_jadwalkerja', 'konfigurasi_jadwalkerja_detail.kode_setjadwal', '=', 'konfigurasi_jadwalkerja.kode_setjadwal')
            ->whereRaw('"' . $tgl_presensi . '" >= dari')
            ->whereRaw('"' . $tgl_presensi . '" <= sampai')
            ->where('nik', $nik)
            ->first();

        $cekgantishift = DB::table('konfigurasi_gantishift')->where('tanggal', $tgl_presensi)->where('nik', $nik)->first();

        if ($cekgantishift != null) {
            $kode_jadwal = $cekgantishift->kode_jadwal;
        } else if ($cekjadwalshift != null) {
            $kode_jadwal = $cekjadwalshift->kode_jadwal;
        } else if ($cekperjalanandinas != null) {
            $cekjadwaldinas = DB::table('jadwal_kerja')
                ->where('nama_jadwal', 'NON SHIFT')
                ->where('kode_cabang', $cekperjalanandinas->kode_cabang)->first();
            $kode_jadwal = $cekjadwaldinas->kode_jadwal;
        } else {
            $kode_jadwal = $karyawan->kode_jadwal;
        }


        $ceklibur = DB::table('harilibur')
            ->where('id_kantor', $kode_cabang)
            ->where('tanggal_limajam', $tgl_presensi)->count();
        if ($ceklibur > 0) {
            $hariini = "Sabtu";
        } else {
            $hariini = hari($tgl_presensi);
        }




        $jadwal = DB::table('jadwal_kerja_detail')
            ->join('jadwal_kerja', 'jadwal_kerja_detail.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
            ->where('hari', $hariini)->where('jadwal_kerja_detail.kode_jadwal', $kode_jadwal)
            ->first();


        $jam_kerja = DB::table('jam_kerja')->where('kode_jam_kerja', $jadwal->kode_jam_kerja)->first();


        $lintashari  = $jam_kerja->lintashari;

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->first();


        if ($status_scan == 0) {
            if ($cek == null) {
                $data = [
                    'nik' => $nik,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'kode_jadwal' => $kode_jadwal,
                    'kode_jam_kerja' => $jadwal->kode_jam_kerja,
                    'status' => 'h',
                ];

                $simpan = DB::table('presensi')->insert($data);
                if ($simpan) {
                    echo "success|Terimkasih, Selamat Bekerja|in";
                } else {
                    echo "error|Maaf Gagal absen, Hubungi Tim It|in";
                }
            } else {
                try {
                    $data_masuk = [
                        'jam_in' => $jam
                    ];
                    $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_masuk);
                    echo "success|Terimkasih, Selamat Bekerja|in";
                    return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
                } catch (\Exception $e) {
                    dd($e);
                    return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
                }
            }
        } else {
            //Cek Absensi Kemarin
            $ceklastpresensi = DB::table('presensi')
                ->join('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                ->where('nik', $nik)->where('tgl_presensi', $lastday)->first();
            //Cek Lintas Hari
            $last_lintashari = $ceklastpresensi != null  ? $ceklastpresensi->lintashari : "";
            $tgl_pulang_shift_3 = date("H:i", strtotime(($jam)));


            if (!empty($last_lintashari) && $tgl_pulang_shift_3 <= "08:00" || empty($last_lintashari) && $tgl_pulang_shift_3 <= "08:00") {
                $tgl_presensi = $lastday;
            }

            $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->first();
            if ($cek == null) {
                $data = [
                    'nik' => $nik,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_out' => $jam,
                    'kode_jadwal' => $kode_jadwal,
                    'kode_jam_kerja' => $jadwal->kode_jam_kerja,
                    'status' => 'h',
                ];

                $simpan = DB::table('presensi')->insert($data);
                if ($simpan) {
                    echo "success|Terimkasih, Hati Hati Di Jalan|out";
                    return Redirect::back()->with(['success' => 'Data Berhasil di Update']);
                } else {
                    echo "error|Maaf Gagal absen, Hubungi Tim It|out";
                    return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
                }
            } else {
                $data_masuk = [
                    'jam_out' => $jam
                ];
                $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_masuk);
                if ($update) {
                    echo "success|Terimkasih, Hati Hati Di Jalan|out";
                    return Redirect::back()->with(['success' => 'Data Berhasil di Update']);
                } else {
                    echo "error|Maaf Gagal absen, Hubungi Tim It|out";
                    return Redirect::back()->with(['warning' => 'Data Gagal di Update']);
                }
            }
        }
    }
}
