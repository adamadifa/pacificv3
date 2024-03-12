<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Pengajuanizin;
use App\Models\Presensi;
use DateTime;
use Faker\Core\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class PengajuanizinController extends Controller
{
    protected $cabang;
    public function __construct()
    {
        // Fetch the Site Settings object
        $this->middleware(function ($request, $next) {
            $this->cabang = Auth::user()->kode_cabang;
            return $next($request);
        });


        View::share('cabang', $this->cabang);
    }
    public function index(Request $request)
    {
        $level = Auth::user()->level;
        $cabang = Auth::user()->kode_cabang;
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;
        $dari = $request->dari_search;
        $sampai = $request->sampai_search;
        $nama_karyawan = $request->nama_karyawan;
        $kode_dept = $request->kode_dept;
        $id_kantor = $request->kode_cabang;
        $pi = new Pengajuanizin();
        $pengajuan_izin = $pi->getpengajuan($level, $cabang, $kode_dept_presensi, $dari, $sampai, $nama_karyawan, $kode_dept, $id_kantor)->paginate(100);
        $pengajuan_izin->appends($request->all());
        //dd($pengajuan_izin);

        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);

        $departemen = DB::table('hrd_departemen')->get();

        if (request()->is('pengajuanizin')) {
            return view('pengajuanizin.index', compact('pengajuan_izin', 'cabang', 'departemen'));
        } else if (request()->is('pengajuanizin/izinpulang')) {
            return view('pengajuanizin.izinpulang', compact('pengajuan_izin', 'cabang', 'departemen'));
        } else if (request()->is('pengajuanizin/izinkeluar')) {
            return view('pengajuanizin.izinkeluar', compact('pengajuan_izin', 'cabang', 'departemen'));
        } else if (request()->is('pengajuanizin/izinterlambat')) {
            return view('pengajuanizin.izinterlambat', compact('pengajuan_izin', 'cabang', 'departemen'));
        } else if (request()->is('pengajuanizin/sakit')) {
            return view('pengajuanizin.sakit', compact('pengajuan_izin', 'cabang', 'departemen'));
        } else if (request()->is('pengajuanizin/cuti')) {
            return view('pengajuanizin.cuti', compact('pengajuan_izin', 'cabang', 'departemen'));
        } else if (request()->is('pengajuanizin/koreksipresensi')) {
            return view('pengajuanizin.koreksipresensi', compact('pengajuan_izin', 'cabang', 'departemen'));
        } else if (request()->is('pengajuanizin/perjalanandinas')) {
            return view('pengajuanizin.perjalanandinas', compact('pengajuan_izin', 'cabang', 'departemen'));
        }
    }

    public function approve(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $dari = $data->dari;
        $sampai = $data->sampai;
        $nik = $data->nik;
        $status = $data->status;
        $kode_izin = $data->kode_izin;
        $status_approve = $data->status_approved;
        $level = Auth::user()->level;
        $cekjadwal = DB::table('master_karyawan')->where('nik', $nik)->first();
        $kode_jadwal = $cekjadwal->kode_jadwal;
        if (isset($request->approve)) {
            try {
                if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 1
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 1
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        if ($status_approve != 1) {
                            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                                'hrd' => 1,
                                'status_approved' => 1
                            ]);

                            while (strtotime($dari) <= strtotime($sampai)) {
                                $tgl = date("D", strtotime($dari));
                                $cekperjalanandinas = DB::table('pengajuan_izin')
                                    ->where('status', 'p')
                                    ->whereRaw('"' . $dari . '" >= dari')
                                    ->whereRaw('"' . $dari . '" <= sampai')
                                    ->where('nik', $nik)
                                    ->first();

                                if ($cekperjalanandinas != null) {
                                    $cekjadwaldinas = DB::table('jadwal_kerja')
                                        ->where('nama_jadwal', 'NON SHIFT')
                                        ->where('kode_cabang', $cekperjalanandinas->kode_cabang)->first();
                                    $kode_jadwal = $cekjadwaldinas->kode_jadwal;
                                }
                                $ceklibur = DB::table('harilibur')->where('tanggal_limajam', $dari)->count();
                                if ($ceklibur > 0) {
                                    $hariini = "Sabtu";
                                } else {
                                    $hariini = hari($tgl);
                                }

                                $jadwal = DB::table('jadwal_kerja_detail')
                                    ->join('jadwal_kerja', 'jadwal_kerja_detail.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
                                    ->where('hari', $hariini)->where('jadwal_kerja_detail.kode_jadwal', $kode_jadwal)
                                    ->first();
                                $kode_jam_kerja_jadwal = $jadwal != null ? $jadwal->kode_jam_kerja : null;
                                $jam_kerja = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja_jadwal)->first();
                                $kode_jam_kerja = $jam_kerja != null ? $jam_kerja->kode_jam_kerja : null;
                                $datapresensi[] = [
                                    'nik' => $nik,
                                    'tgl_presensi' => $dari,
                                    'jam_in' => null,
                                    'jam_out' => null,
                                    'lokasi_in' => null,
                                    'lokasi_out' => null,
                                    'kode_jadwal' => $kode_jadwal,
                                    'kode_jam_kerja' => $kode_jam_kerja,
                                    'status' => $status,
                                    'kode_izin' => $kode_izin
                                ];
                                $dari = date("Y-m-d", strtotime("+1 day", strtotime($dari)));
                            }

                            $chunks = array_chunk($datapresensi, 5);
                            foreach ($chunks as $chunk) {
                                Presensi::insert($chunk);
                            }
                            DB::commit();
                            return Redirect::back()->with(['success' => 'Pengajuan Izin Disetujui']);
                        } else {
                            return Redirect::back()->with(['warning' => 'Data Sudah Disetujui']);
                        }
                    } catch (\Exception $e) {
                        dd($e);
                        DB::rollBack();
                        return Redirect::back()->with(['warning' => 'Pengajuan Gagal Disetujui']);
                    }
                }

                return Redirect::back()->with(['success' => 'Pengajuan Izin Disetujui']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Disetujui']);
            }
        }

        if (isset($request->decline)) {
            try {
                if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 2
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 2
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                            'hrd' => 2,
                            'status_approved' => 2
                        ]);

                        DB::table('presensi')->where('kode_izin', $kode_izin)->delete();
                        DB::commit();
                        return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
                    } catch (\Exception $e) {
                        dd($e);
                        DB::rollBack();
                        return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
                    }
                }

                return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
            }
        }
    }


    public function batalkan(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $kode_izin = $data->kode_izin;
        $level = Auth::user()->level;
        try {
            $izin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
            $jenis_izin = $izin->jenis_izin;
            if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'head_dept' => NULL
                ]);
            } else if ($level == "direktur") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'direktur' => NULL
                ]);
            } else {
                DB::beginTransaction();
                try {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'hrd' => NULL,
                        'status_approved' => NULL
                    ]);

                    if ($jenis_izin == "TL") {
                        DB::table('presensi')->where('kode_izin_terlambat', $kode_izin)->update(['kode_izin_terlambat' => NULL]);
                    } else if ($jenis_izin == "PL") {
                        DB::table('presensi')->where('kode_izin_pulang', $kode_izin)->update(['kode_izin_pulang' => NULL]);
                    } else if ($jenis_izin == "KL") {
                        DB::table('presensi')->where('kode_izin', $kode_izin)->update(['kode_izin' => NULL]);
                    } else {
                        DB::table('presensi')->where('kode_izin', $kode_izin)->delete();
                    }
                    DB::commit();
                    return Redirect::back()->with(['success' => 'Pengajuan Izin Dibatalkan']);
                } catch (\Exception $e) {
                    dd($e);
                    DB::rollBack();
                    return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Dibatalkan']);
                }
            }

            return Redirect::back()->with(['success' => 'Pengajuan Izin Dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Dibatalkan']);
        }
    }



    public function approveizinpulang(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $dari = $data->dari;
        $nik = $data->nik;
        $kode_izin = $data->kode_izin;
        $jam_pulang = $dari . " " . $data->jam_pulang;
        //dd($jam_pulang);
        $level = Auth::user()->level;
        $status_approve = $data->status_approved;
        if (isset($request->approve)) {
            $cekpresensi =  DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->count();
            if (empty($cekpresensi)) {
                return Redirect::back()->with(['warning' => 'Karyawan Tersebut Belum Melakukan Presensi, Silahkan Lakukan Presensi Terlebih Dahulu, atau Input di Koreksi Presensi']);
            }
            try {
                if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 1
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 1
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        if ($status_approve != 1) {
                            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                                'hrd' => 1,
                                'status_approved' => 1
                            ]);

                            DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->update([
                                'jam_out' => $jam_pulang,
                                'kode_izin_pulang' => $kode_izin
                            ]);
                            DB::commit();
                            return Redirect::back()->with(['success' => 'Pengajuan Izin Disetujui']);
                        } else {
                            return Redirect::back()->with(['warning' => 'Data Sudah Disetujui']);
                        }
                    } catch (\Exception $e) {
                        dd($e);
                        DB::rollBack();
                        return Redirect::back()->with(['warning' => 'Pengajuan Gagal Disetujui']);
                    }
                }

                return Redirect::back()->with(['success' => 'Pengajuan Izin Disetujui']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Disetujui']);
            }
        }

        if (isset($request->decline)) {
            try {
                if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 2
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 2
                    ]);
                } else {
                    try {
                        DB::beginTransaction();
                        DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                            'hrd' => 2,
                            'status_approved' => 2
                        ]);
                        DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->update([
                            'jam_out' => null,
                            'kode_izin_pulang' => null
                        ]);
                        DB::commit();
                        return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
                    }
                }

                return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
            }
        }
    }



    public function batalkanizinpulang(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $dari = $data->dari;
        $nik = $data->nik;
        $kode_izin = $data->kode_izin;
        $level = Auth::user()->level;
        try {
            if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'head_dept' => null
                ]);
            } else if ($level == "direktur") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'direktur' => null
                ]);
            } else {
                try {
                    DB::beginTransaction();
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'hrd' => null,
                        'status_approved' => null
                    ]);
                    DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->update([
                        'jam_out' => null,
                        'kode_izin_pulang' => null
                    ]);
                    DB::commit();
                    return Redirect::back()->with(['success' => 'Pengajuan Izin Dibatalkan']);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Dibatalkan']);
                }
            }

            return Redirect::back()->with(['success' => 'Pengajuan Izin Dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Dibatalkan']);
        }
    }


    public function approveizinkeluar(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $dari = $data->dari;
        $nik = $data->nik;
        $kode_izin = $data->kode_izin;
        //dd($jam_pulang);
        $level = Auth::user()->level;
        $status_approve = $data->status_approved;
        if (isset($request->approve)) {
            $cekpresensi =  DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->count();
            if (empty($cekpresensi)) {
                return Redirect::back()->with(['warning' => 'Karyawan Tersebut Belum Melakukan Presensi, Silahkan Lakukan Presensi Terlebih Dahulu, atau Input di Koreksi Presensi']);
            }
            try {
                if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 1
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 1
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        if ($status_approve != 1) {
                            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                                'hrd' => 1,
                                'status_approved' => 1
                            ]);

                            DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->update([
                                'kode_izin' => $kode_izin
                            ]);
                            DB::commit();
                            return Redirect::back()->with(['success' => 'Pengajuan Izin Disetujui']);
                        } else {
                            return Redirect::back()->with(['warning' => 'Data Sudah Disetujui']);
                        }
                    } catch (\Exception $e) {
                        dd($e);
                        DB::rollBack();
                        return Redirect::back()->with(['warning' => 'Pengajuan Gagal Disetujui']);
                    }
                }

                return Redirect::back()->with(['success' => 'Pengajuan Izin Disetujui']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Disetujui']);
            }
        }

        if (isset($request->decline)) {
            try {
                if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 2
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 2
                    ]);
                } else {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'hrd' => 2,
                        'status_approved' => 2
                    ]);

                    DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->update([
                        'kode_izin' => NULL
                    ]);
                }

                return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
            }
        }
    }


    public function batalkanizinkeluar(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $dari = $data->dari;
        $nik = $data->nik;
        $kode_izin = $data->kode_izin;
        //dd($jam_pulang);
        $level = Auth::user()->level;


        try {
            if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'head_dept' => NULL
                ]);
            } else if ($level == "direktur") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'direktur' => NULL
                ]);
            } else {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'hrd' => NULL,
                    'status_approved' => NULL
                ]);

                DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->update([
                    'kode_izin' => NULL
                ]);
            }

            return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
        }
    }


    public function create()
    {
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;
        $kar = new Karyawan();
        $karyawan = $kar->getkaryawanpengajuan($kode_dept_presensi);
        $mastercuti = DB::table('hrd_mastercuti')->get();
        return view('pengajuanizin.create', compact('karyawan', 'mastercuti'));
    }

    public function createizinterlambat()
    {
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;
        $kar = new Karyawan();
        $karyawan = $kar->getkaryawanpengajuan($kode_dept_presensi);
        return view('pengajuanizin.createterlambat', compact('karyawan'));
    }


    public function createizinabsen()
    {
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;
        $kar = new Karyawan();
        $karyawan = $kar->getkaryawanpengajuan($kode_dept_presensi);
        return view('pengajuanizin.createizinabsen', compact('karyawan'));
    }


    public function createizinkeluar()
    {
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;
        $kar = new Karyawan();
        $karyawan = $kar->getkaryawanpengajuan($kode_dept_presensi);
        return view('pengajuanizin.createizinkeluar', compact('karyawan'));
    }

    public function createizinpulang()
    {
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;
        $kar = new Karyawan();
        $karyawan = $kar->getkaryawanpengajuan($kode_dept_presensi);
        return view('pengajuanizin.createizinpulang', compact('karyawan'));
    }


    public function createizinsakit()
    {
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;
        $kar = new Karyawan();
        $karyawan = $kar->getkaryawanpengajuan($kode_dept_presensi);

        return view('pengajuanizin.createizinsakit', compact('karyawan'));
    }

    public function createizincuti()
    {
        $mastercuti = DB::table('hrd_mastercuti')->get();
        $mastercutikhusus = DB::table('hrd_mastercutikhusus')->get();
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;
        $kar = new Karyawan();
        $karyawan = $kar->getkaryawanpengajuan($kode_dept_presensi);
        return view('pengajuanizin.createizincuti', compact('karyawan', 'mastercuti', 'mastercutikhusus'));
    }



    public function createkoreksi()
    {
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;
        $kar = new Karyawan();
        $karyawan = $kar->getkaryawanpengajuan($kode_dept_presensi);
        $kode_cabang = Auth::user()->kode_cabang;
        if ($kode_cabang == "PCF") {
            $kode_cabang = "PST";
        }
        $jadwal = DB::table('jadwal_kerja')->orderBy('kode_jadwal')
            ->where('kode_cabang', $kode_cabang)
            ->get();
        return view('pengajuanizin.createkoreksi', compact('karyawan', 'jadwal'));
    }

    public function createperjalanandinas()
    {
        $kode_dept_presensi = Auth::user()->kode_dept_presensi;
        $kar = new Karyawan();
        $karyawan = $kar->getkaryawanpengajuan($kode_dept_presensi);
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('pengajuanizin.createperjalanandinas', compact('karyawan', 'cabang'));
    }

    public function store(Request $request)
    {
        $nik = $request->nik;

        // $dari = $request->jenis_izin == "PL" || $request->jenis_izin == "KL" || $request->jenis_izin == "TL" ? date("Y-m-d") : $request->dari;
        $dari = $request->dari;
        // $sampai =  $request->jenis_izin == "PL" || $request->jenis_izin == "KL" || $request->jenis_izin == "TL" ? date("Y-m-d") : $request->sampai;
        $sampai = $request->sampai;
        $jmlhari = $request->jmlhari;
        $status = $request->status;
        $keterangan = $request->keterangan;
        $jenis_izin = $request->jenis_izin;
        $jam_pulang = $request->jam_pulang;
        $jam_keluar = $request->jam_keluar;
        $jam_terlambat = $request->jam_terlambat;
        $jenis_cuti = $request->jenis_cuti;
        $kat_cuti_khusus = $request->kat_cuti_khusus;

        $tgl_pengajuan = strtotime(date("Y-m-d", strtotime($dari)));
        $tigahari    = date('Y-m-d', strtotime("+3 day", $tgl_pengajuan));

        if (Auth::user()->level != "admin") {
            if (date('Y-m-d') > $tigahari) {
                return Redirect::back()->with(['warning' => 'Pengjuan Izin /Sakit /Cuti Tidak Boleh Lebih Dari 3 Hari']);
            }
        }


        $keperluan = $request->keperluan;
        $tgl = explode("-", $dari);
        $tahun = substr($tgl[0], 2, 2);
        $bulan = $tgl[1];
        $izin = DB::table("pengajuan_izin")
            ->join('master_karyawan', 'pengajuan_izin.nik', '=', 'master_karyawan.nik')
            ->whereRaw('YEAR(dari)="' . $tgl[0] . '"')
            ->whereRaw('MONTH(dari)="' . $tgl[1] . '"')
            ->whereRaw('LENGTH(kode_izin)=10')
            ->orderBy("kode_izin", "desc")
            ->first();

        $last_kodeizin = $izin != null ? $izin->kode_izin : '';
        $kode_izin  = buatkode($last_kodeizin, "IZ"  . $tahun . $bulan, 4);


        if ($request->hasFile('sid')) {
            $sid = $kode_izin . "." . $request->file('sid')->getClientOriginalExtension();
        } else {
            $sid = null;
        }


        if ($request->hasFile('doccuti')) {
            $doccuti = $kode_izin . "." . $request->file('doccuti')->getClientOriginalExtension();
        } else {
            $doccuti = null;
        }

        $kode_cabang = $request->kode_cabang;
        $data = [
            'kode_izin' => $kode_izin,
            'nik' => $nik,
            'dari' => $dari,
            'sampai' => $sampai,
            'jmlhari' => $jmlhari,
            'status' => $status,
            'keterangan' => $keterangan,
            'sid' => $sid,
            'jenis_izin' => $jenis_izin,
            'jam_pulang' => $jam_pulang,
            'jam_keluar' => $jam_keluar,
            'jam_terlambat' => $jam_terlambat,
            'jenis_cuti' => $jenis_cuti,
            'kode_cuti_khusus' => $kat_cuti_khusus,
            'doccuti' => $doccuti,
            'kode_cabang' => $kode_cabang,
            'keperluan' => $keperluan
        ];

        try {
            $simpan = DB::table('pengajuan_izin')->insert($data);
            if ($simpan) {
                if ($request->hasFile('sid')) {
                    $folderPath = "public/uploads/sid/";
                    $request->file('sid')->storeAs($folderPath, $sid);
                }

                if ($request->hasFile('doccuti')) {
                    $folderPath = "public/uploads/doccuti/";
                    $request->file('doccuti')->storeAs($folderPath, $doccuti);
                }
            }
            if ($status == "c") {
                return redirect('/pengajuanizin/cuti')->with(['success' => 'Data Berhasil Disimpan']);
            } elseif ($status == "s") {
                return redirect('/pengajuanizin/sakit')->with(['success' => 'Data Berhasil Disimpan']);
            } elseif ($status == "k") {
                return redirect('/pengajuanizin/perjalanandinas')->with(['success' => 'Data Berhasil Disimpan']);
            } elseif ($jenis_izin == "TM") {
                return redirect('/pengajuanizin')->with(['success' => 'Data Berhasil Disimpan']);
            } elseif ($jenis_izin == "KL") {
                return redirect('/pengajuanizin/izinkeluar')->with(['success' => 'Data Berhasil Disimpan']);
            } elseif ($jenis_izin == "PL") {
                return redirect('/pengajuanizin/izinpulang')->with(['success' => 'Data Berhasil Disimpan']);
            } elseif ($jenis_izin == "TL") {
                return redirect('/pengajuanizin/izinterlambat')->with(['success' => 'Data Berhasil Disimpan']);
            }
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['error' => 'Data Gagal Disimpan']);
        }
    }


    public function storekoreksipresensi(Request $request)
    {
        $nik = $request->nik;

        $status = "k";
        $tgl_presensi = $request->tgl_presensi;
        $jam_masuk = $request->jam_masuk;
        $jam_masuk_old = $request->jam_masuk_old;
        $jam_pulang = $request->jam_pulang;
        $jam_pulang_old = $request->jam_pulang_old;
        $keterangan = $request->keterangan;
        $kode_jadwal = $request->kode_jadwal;
        $kode_jadwal_old = $request->kode_jadwal_old;
        $tgl = explode("-", $tgl_presensi);
        $tahun = substr($tgl[0], 2, 2);
        $bulan = $tgl[1];

        $tgl_pengajuan = strtotime(date("Y-m-d", strtotime($tgl_presensi)));
        $tigahari    = date('Y-m-d', strtotime("+3 day", $tgl_pengajuan));

        if (Auth::user()->level != "admin") {
            if (date('Y-m-d') > $tigahari) {
                return Redirect::back()->with(['warning' => 'Pengjuan Izin /Sakit /Cuti Tidak Boleh Lebih Dari 3 Hari']);
            }
        }
        $izin = DB::table("pengajuan_izin")
            ->join('master_karyawan', 'pengajuan_izin.nik', '=', 'master_karyawan.nik')
            ->whereRaw('YEAR(dari)="' . $tgl[0] . '"')
            ->whereRaw('MONTH(dari)="' . $tgl[1] . '"')
            ->whereRaw('LENGTH(kode_izin)=10')
            ->orderBy("kode_izin", "desc")
            ->first();

        $last_kodeizin = $izin != null ? $izin->kode_izin : '';
        $kode_izin  = buatkode($last_kodeizin, "IZ"  . $tahun . $bulan, 4);
        $data = [
            'kode_izin' => $kode_izin,
            'nik' => $nik,
            'dari' => $tgl_presensi,
            'status' => $status,
            'keterangan' => $keterangan,
            'jam_masuk' => $jam_masuk,
            'jam_masuk_old' => $jam_masuk_old,
            'jam_pulang' => $jam_pulang,
            'jam_pulang_old' => $jam_pulang_old,
            'kode_jadwal' => $kode_jadwal,
            'kode_jadwal_old' => $kode_jadwal_old
        ];

        try {
            DB::table('pengajuan_izin')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }


    public function delete($kode_izin)
    {
        $kode_izin = Crypt::decrypt($kode_izin);
        try {
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => 'Data Gagal Dihapus']);
        }
    }



    public function approveizinterlambat(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $dari = $data->dari;
        $nik = $data->nik;
        $kode_izin = $data->kode_izin;
        $jam_terlambat = $dari . " " . $data->jam_terlambat;
        //dd($jam_pulang);
        $level = Auth::user()->level;
        $status_approve = $data->status_approved;
        if (isset($request->approve)) {
            $cekpresensi =  DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->count();
            if (empty($cekpresensi)) {
                return Redirect::back()->with(['warning' => 'Karyawan Tersebut Belum Melakukan Presensi, Silahkan Lakukan Presensi Terlebih Dahulu, atau Input di Koreksi Presensi']);
            }
            try {
                if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 1
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 1
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        if ($status_approve != 1) {
                            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                                'hrd' => 1,
                                'status_approved' => 1
                            ]);

                            DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->update([
                                'kode_izin_terlambat' => $kode_izin,
                                'jam_in' => $jam_terlambat
                            ]);
                            DB::commit();
                            return Redirect::back()->with(['success' => 'Pengajuan Izin Disetujui']);
                        } else {
                            return Redirect::back()->with(['warning' => 'Data Sudah Disetujui']);
                        }
                    } catch (\Exception $e) {
                        dd($e);
                        DB::rollBack();
                        return Redirect::back()->with(['warning' => 'Pengajuan Gagal Disetujui']);
                    }
                }

                return Redirect::back()->with(['success' => 'Pengajuan Izin Disetujui']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Disetujui']);
            }
        }

        if (isset($request->decline)) {
            try {
                if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 2
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 2
                    ]);
                } else {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'hrd' => 2,
                        'status_approved' => NULL
                    ]);

                    DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->update([
                        'kode_izin_terlambat' => NULL
                    ]);
                }

                return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
            }
        }
    }



    public function batalkanizinterlambat(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $dari = $data->dari;
        $nik = $data->nik;
        $kode_izin = $data->kode_izin;
        $level = Auth::user()->level;

        try {
            if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'head_dept' => NULL
                ]);
            } else if ($level == "direktur") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'direktur' => NULL
                ]);
            } else {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'hrd' => NULL,
                    'status_approved' => NULL
                ]);

                DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $dari)->update([
                    'kode_izin_terlambat' => NULL
                ]);
            }

            return Redirect::back()->with(['success' => 'Pengajuan Izin Dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Dibatalkan']);
        }
    }


    public function updatejammasukkk(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $jam_masuk = $request->jam_masuk_kk;

        try {
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update(['jam_masuk' => $jam_masuk]);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            dd($e);
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    public function getpresensihariini(Request $request)
    {
        $nik = $request->nik;
        $tgl_presensi = $request->tgl_presensi;
        $presensi = DB::table('presensi')
            ->select('jam_in', 'jam_out', 'presensi.kode_jadwal')
            ->leftJoin(
                DB::raw("(
                SELECT
                    jadwal_kerja_detail.kode_jadwal,nama_jadwal,kode_jam_kerja
                FROM
                    jadwal_kerja_detail
                INNER JOIN jadwal_kerja ON jadwal_kerja_detail.kode_jadwal = jadwal_kerja.kode_jadwal
                GROUP BY
                jadwal_kerja_detail.kode_jadwal,nama_jadwal,kode_jam_kerja
                ) jadwal"),
                function ($join) {
                    $join->on('presensi.kode_jadwal', '=', 'jadwal.kode_jadwal');
                    $join->on('presensi.kode_jam_kerja', '=', 'jadwal.kode_jam_kerja');
                }
            )
            ->where('tgl_presensi', $tgl_presensi)
            ->where('nik', $nik)
            ->first();
        if ($presensi != null) {
            $result = date("H:i", strtotime($presensi->jam_in)) . "|" . date("H:i", strtotime($presensi->jam_out)) . "|" . $presensi->kode_jadwal;
        } else {
            $result = "" . "|" . "" . "|" . "";
        }


        return $result;
    }



    public function approvekoreksipresensi(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $tgl_presensi = $data->dari;
        $nik = $data->nik;
        $status_approve = $data->status_approved;
        $level = Auth::user()->level;
        $kode_jadwal = $data->kode_jadwal;
        if ($kode_jadwal == "JD004") {
            $tgl_pulang = date('Y-m-d', strtotime('+1 day', strtotime($tgl_presensi)));
        } else {
            $tgl_pulang = $tgl_presensi;
        }
        $hariini = hari($tgl_presensi);
        $jam_in = $tgl_presensi . " " . $data->jam_masuk;

        $jam_out = $tgl_pulang . " " . $data->jam_pulang;
        $jadwal = DB::table('jadwal_kerja_detail')
            ->join('jadwal_kerja', 'jadwal_kerja_detail.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
            ->where('hari', $hariini)->where('jadwal_kerja_detail.kode_jadwal', $kode_jadwal)->first();


        if (isset($request->approve)) {
            try {
                if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 1
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 1
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        if ($status_approve != 1) {
                            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                                'hrd' => 1,
                                'status_approved' => 1
                            ]);
                            //DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $tgl_presensi)->delete();
                            //Cek Data Presensi Karyawan Pada Tanggal Tersebut
                            $cekpresensi = DB::table('presensi')->where('nik', $nik)
                                ->where('tgl_presensi', $tgl_presensi)->first();

                            if ($cekpresensi == null) {
                                //Jika Karyawan Tersebut Belum Melakukan Presensi
                                $datapresensi = [
                                    'nik' => $nik,
                                    'tgl_presensi' => $tgl_presensi,
                                    'status' => 'h',
                                    'jam_in' => $jam_in,
                                    'jam_out' => $jam_out,
                                    'kode_jadwal' => $kode_jadwal,
                                    'kode_jam_kerja' => $jadwal->kode_jam_kerja,
                                    'kode_izin' => $kode_izin
                                ];
                                DB::table('presensi')->insert($datapresensi);
                            } else {
                                //Jika Karyawan Tersebut Sudah Absen
                                $datapresensi = [
                                    'jam_in' => $jam_in,
                                    'jam_out' => $jam_out,
                                    'kode_jadwal' => $kode_jadwal,
                                    'kode_jam_kerja' => $jadwal->kode_jam_kerja,
                                    'kode_izin' => $kode_izin
                                ];
                                DB::table('presensi')
                                    ->where('nik', $nik)
                                    ->where('tgl_presensi', $tgl_presensi)
                                    ->update($datapresensi);
                            }

                            DB::commit();
                            return Redirect::back()->with(['success' => 'Koreksi Presensi Disetujui']);
                        } else {
                            return Redirect::back()->with(['warning' => 'Data Sudah Disetujui']);
                        }
                    } catch (\Exception $e) {
                        dd($e);
                        DB::rollBack();
                        return Redirect::back()->with(['warning' => 'Koreksi Presensi Gagal Disetujui']);
                    }
                }

                return Redirect::back()->with(['success' => 'Koreksi Presensi Disetujui']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Koreksi Presensi Gagal Disetujui']);
            }
        }

        if (isset($request->decline)) {
            try {
                if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 2
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 2
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                            'hrd' => 2,
                            'status_approved' => 2
                        ]);

                        //DB::table('presensi')->where('kode_izin', $kode_izin)->delete();

                        //Cek Presensi
                        $cekpresensi = DB::table('presensi')->where('kode_izin', $kode_izin)->first();
                        if ($cekpresensi != null) {
                            $jam_masuk_old = $tgl_presensi . " " . $data->jam_masuk_old;
                            $jam_pulang_old = $tgl_presensi . " " . $data->jam_pulang_old;
                            $kode_jadwal_old = $data->kode_jadwal_old;
                            $jadwal_old = DB::table('jadwal_kerja_detail')
                                ->join('jadwal_kerja', 'jadwal_kerja_detail.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
                                ->where('hari', $hariini)->where('jadwal_kerja_detail.kode_jadwal', $kode_jadwal_old)->first();
                            $datapresensi = [
                                'jam_in' => $jam_masuk_old,
                                'jam_out' => $jam_pulang_old,
                                'kode_jadwal' => $kode_jadwal_old,
                                'kode_jam_kerja' => $jadwal_old->kode_jam_kerja,
                                'kode_izin' => NULL
                            ];
                            DB::table('presensi')
                                ->where('nik', $nik)
                                ->where('tgl_presensi', $tgl_presensi)
                                ->update($datapresensi);
                        }
                        DB::commit();
                        return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
                    } catch (\Exception $e) {
                        dd($e);
                        DB::rollBack();
                        return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
                    }
                }

                return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
            }
        }
    }



    public function approveperjalanandinas(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $level = Auth::user()->level;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $status_approve = $data->status_approved;


        if (isset($request->approve)) {
            try {
                if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 1
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 1
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        if ($status_approve != 1) {
                            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                                'hrd' => 1,
                                'status_approved' => 1
                            ]);
                            //DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $tgl_presensi)->delete();
                            //Cek Data Presensi Karyawan Pada Tanggal Tersebut
                            DB::commit();
                            return Redirect::back()->with(['success' => 'Pengajuan Izin Disetujui']);
                        } else {
                            return Redirect::back()->with(['warning' => 'Data Sudah Disetujui']);
                        }
                    } catch (\Exception $e) {
                        dd($e);
                        DB::rollBack();
                        return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Disetujui']);
                    }
                }

                return Redirect::back()->with(['success' => 'Pengajuan izin  Disetujui']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Disetujui']);
            }
        }

        if (isset($request->decline)) {
            try {
                if ($level != "manager hrd" && $level != "spv presensi") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 2
                    ]);
                } else if ($level == "direktur") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'direktur' => 2
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                            'hrd' => 2,
                            'status_approved' => 2
                        ]);
                        DB::commit();
                        return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
                    } catch (\Exception $e) {
                        dd($e);
                        DB::rollBack();
                        return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
                    }
                }

                return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
            }
        }
    }


    public function batalkanperjalanandinas($kode_izin)
    {
        $level = Auth::user()->level;
        try {
            if ($level != "manager hrd" && $level != "spv presensi") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'head_dept' => NULL
                ]);
            } else if ($level == "direktur") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'direktur' => NULL
                ]);
            } else {
                DB::beginTransaction();
                try {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'hrd' => NULL,
                        'status_approved' => NULL
                    ]);
                    DB::commit();
                    return Redirect::back()->with(['success' => 'Pengajuan Izin Dibatalkan']);
                } catch (\Exception $e) {
                    dd($e);
                    DB::rollBack();
                    return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Dibatalkan']);
                }
            }

            return Redirect::back()->with(['success' => 'Pengajuan Izin Dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Dibatalkan']);
        }
    }
    public function batalkankoreksipresensi($kode_izin)
    {
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $tgl_presensi = $data->dari;
        $nik = $data->nik;
        $level = Auth::user()->level;
        $hariini = hari($tgl_presensi);

        try {
            if ($level != "manager hrd" && $level != "direktur" && $level != "spv presensi") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'head_dept' => NULL
                ]);
            } else if ($level == "direktur") {
                DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                    'direktur' => NULL
                ]);
            } else {
                DB::beginTransaction();
                try {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'hrd' => NULL,
                        'status_approved' => NULL
                    ]);

                    //DB::table('presensi')->where('kode_izin', $kode_izin)->delete();

                    //Cek Presensi
                    $cekpresensi = DB::table('presensi')->where('kode_izin', $kode_izin)->first();
                    if ($cekpresensi != null) {
                        $jam_masuk_old = $tgl_presensi . " " . $data->jam_masuk_old;
                        $jam_pulang_old = $tgl_presensi . " " . $data->jam_pulang_old;
                        $kode_jadwal_old = $data->kode_jadwal_old;
                        $jadwal_old = DB::table('jadwal_kerja_detail')
                            ->join('jadwal_kerja', 'jadwal_kerja_detail.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal')
                            ->where('hari', $hariini)->where('jadwal_kerja_detail.kode_jadwal', $kode_jadwal_old)->first();
                        $datapresensi = [
                            'jam_in' => $jam_masuk_old,
                            'jam_out' => $jam_pulang_old,
                            'kode_jadwal' => $kode_jadwal_old,
                            'kode_jam_kerja' => $jadwal_old->kode_jam_kerja,
                            'kode_izin' => NULL
                        ];
                        DB::table('presensi')
                            ->where('nik', $nik)
                            ->where('tgl_presensi', $tgl_presensi)
                            ->update($datapresensi);
                    }
                    DB::commit();
                    return Redirect::back()->with(['success' => 'Pengajuan Izin Dibatalkan']);
                } catch (\Exception $e) {
                    dd($e);
                    DB::rollBack();
                    return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Dibatalkan']);
                }
            }

            return Redirect::back()->with(['success' => 'Pengajuan Izin Dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Dibatalkan']);
        }
    }

    public function create_kethrd($kode_izin)
    {
        $izin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        return view('pengajuanizin.create_kethrd', compact('izin'));
    }


    public function update_kethrd($kode_izin, Request $request)
    {
        try {
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                'keterangan_hrd' => $request->keterangan_hrd
            ]);

            return Redirect::back()->with(['success' => 'Komentar Berhasil Ditambahkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Komentar Gagal Ditambahkan']);
        }
    }

    public function cetak($kode_izin)
    {
        $kode_izin = Crypt::decrypt($kode_izin);
        $izin = DB::table('pengajuan_izin')
            ->join('master_karyawan', 'pengajuan_izin.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->where('kode_izin', $kode_izin)
            ->first();
        return view('pengajuanizin.cetak', compact('izin'));
    }
}
