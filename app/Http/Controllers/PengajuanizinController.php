<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pengajuanizin;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $query = Pengajuanizin::query();
        $query->select('pengajuan_izin.*', 'nama_karyawan', 'nama_jabatan', 'kode_dept', 'nama_cuti');
        $query->join('master_karyawan', 'pengajuan_izin.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftjoin('hrd_mastercuti', 'pengajuan_izin.jenis_cuti', '=', 'hrd_mastercuti.kode_cuti');

        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('dari', [$request->dari, $request->sampai]);
        }

        if (!empty($request->kode_cabang)) {
            $query->where('master_karyawan.id_kantor', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $query->where('master_karyawan.kode_dept', $request->kode_dept);
        }

        if (!empty($request->nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        if ($level == "kepala admin") {
            $query->where('id_kantor', $this->cabang);
            $query->where('id_perusahaan', "MP");
        }

        if ($level == "manager hrd") {
            $query->where('pengajuan_izin.head_dept', 1);
        }

        if ($level == "kepala penjualan") {
            $query->where('id_kantor', $this->cabang);
            $query->where('id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $query->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $query->where('master_karyawan.kode_dept', 'GDG');
        }

        if ($level == "manager produksi") {
            $query->where('master_karyawan.kode_dept', 'PRD');
        }

        if ($level == "manager ga") {
            $query->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $query->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'PDQ']);
        }


        if ($level == "manager marketing") {
            $query->where('master_karyawan.kode_dept', 'MKT');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $query->whereIn('master_karyawan.id_kantor', $list_wilayah);
        }
        if (request()->is('pengajuanizin')) {
            $query->where('jenis_izin', 'TM');
        } else if (request()->is('pengajuanizin/izinpulang')) {
            $query->where('jenis_izin', 'PL');
        } else if (request()->is('pengajuanizin/izinkeluar')) {
            $query->where('jenis_izin', 'KL');
        } else if (request()->is('pengajuanizin/sakit')) {
            $query->where('pengajuan_izin.status', 's');
        } else if (request()->is('pengajuanizin/cuti')) {
            $query->where('pengajuan_izin.status', 'c');
        }
        $pengajuan_izin = $query->get();
        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);

        $departemen = DB::table('departemen')->where('status_pengajuan', 0)->get();

        if (request()->is('pengajuanizin')) {
            return view('pengajuanizin.index', compact('pengajuan_izin', 'cabang', 'departemen'));
        } else if (request()->is('pengajuanizin/izinpulang')) {
            return view('pengajuanizin.izinpulang', compact('pengajuan_izin', 'cabang', 'departemen'));
        } else if (request()->is('pengajuanizin/izinkeluar')) {
            return view('pengajuanizin.izinkeluar', compact('pengajuan_izin', 'cabang', 'departemen'));
        } else if (request()->is('pengajuanizin/sakit')) {
            return view('pengajuanizin.sakit', compact('pengajuan_izin', 'cabang', 'departemen'));
        } else if (request()->is('pengajuanizin/cuti')) {
            return view('pengajuanizin.cuti', compact('pengajuan_izin', 'cabang', 'departemen'));
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
        if (isset($request->approve)) {
            try {
                if ($level != "manager hrd") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 1
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
                                $datapresensi[] = [
                                    'nik' => $nik,
                                    'tgl_presensi' => $dari,
                                    'jam_in' => null,
                                    'jam_out' => null,
                                    'lokasi_in' => null,
                                    'lokasi_out' => null,
                                    'kode_jam_kerja' => null,
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
                if ($level != "manager hrd") {
                    DB::table('pengajuan_izin')->where('kode_izind', $kode_izin)->update([
                        'head_dept' => 2
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



    public function approveizinpulang(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $dari = $data->dari;
        $sampai = $data->sampai;
        $nik = $data->nik;
        $status = $data->status;
        $kode_izin = $data->kode_izin;
        $hariini = date("Y-m-d");
        $jam_pulang = $hariini . " " . $data->jam_pulang;
        //dd($jam_pulang);
        $level = Auth::user()->level;
        $status_approve = $data->status_approve;
        if (isset($request->approve)) {
            try {
                if ($level != "manager hrd") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 1
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        if ($status_approve != 1) {
                            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                                'hrd' => 1,
                                'status_approved' => 1
                            ]);

                            DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $hariini)->update([
                                'jam_out' => $jam_pulang,
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
                if ($level != "manager hrd") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 2
                    ]);
                } else {
                    try {
                        DB::beginTransaction();
                        DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                            'hrd' => 2,
                            'status_approved' => 2
                        ]);
                        DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $hariini)->update([
                            'jam_out' => null,
                            'kode_izin' => null
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


    public function approveizinkeluar(Request $request)
    {
        $kode_izin = $request->kode_izin;
        $data = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->first();
        $dari = $data->dari;
        $sampai = $data->sampai;
        $nik = $data->nik;
        $status = $data->status;
        $kode_izin = $data->kode_izin;
        $hariini = date("Y-m-d");
        $jam_pulang = $hariini . " " . $data->jam_pulang;
        //dd($jam_pulang);
        $level = Auth::user()->level;
        $status_approve = $data->status_approved;
        if (isset($request->approve)) {
            try {
                if ($level != "manager hrd") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 1
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        if ($status_approve != 1) {
                            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                                'hrd' => 1,
                                'status_approved' => 1
                            ]);

                            DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $hariini)->update([
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
                if ($level != "manager hrd") {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'head_dept' => 2
                    ]);
                } else {
                    DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->update([
                        'hrd' => 2,
                        'status_approved' => 2
                    ]);
                }

                return Redirect::back()->with(['success' => 'Pengajuan Izin Ditolak']);
            } catch (\Exception $e) {
                return Redirect::back()->with(['warning' => 'Pengajuan Izin Gagal Ditolak']);
            }
        }
    }


    public function create()
    {
        $karyawan = DB::table('master_karyawan')->orderBy('nama_karyawan')->get();
        $mastercuti = DB::table('hrd_mastercuti')->get();
        return view('pengajuanizin.create', compact('karyawan', 'mastercuti'));
    }
}