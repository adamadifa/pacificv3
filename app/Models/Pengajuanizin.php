<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Pengajuanizin extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_izin';

    function getpengajuan($level, $cabang, $kode_dept_presensi, $dari, $sampai, $nama_karyawan, $kode_dept, $id_kantor)
    {

        $query = Pengajuanizin::query();
        $query->select('pengajuan_izin.*', 'nama_karyawan', 'nama_jabatan', 'kode_dept', 'nama_cuti', 'nama_jadwal', 'master_karyawan.id_kantor', 'id_perusahaan');
        $query->join('master_karyawan', 'pengajuan_izin.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftjoin('hrd_mastercuti', 'pengajuan_izin.jenis_cuti', '=', 'hrd_mastercuti.kode_cuti');
        $query->leftjoin('jadwal_kerja', 'pengajuan_izin.kode_jadwal', '=', 'jadwal_kerja.kode_jadwal');


        if ($level != "emf" || Auth::user()->id != "57" || Auth::user()->id != "69" || Auth::user()->id != 20 || $level != "direktur") {
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('dari', [$dari, $sampai]);
            }
            if (request()->is('pengajuanizin')) {
                $query->where('jenis_izin', 'TM');
            } else if (request()->is('pengajuanizin/izinpulang')) {
                $query->where('jenis_izin', 'PL');
            } else if (request()->is('pengajuanizin/izinkeluar')) {
                $query->where('jenis_izin', 'KL');
            } else if (request()->is('pengajuanizin/izinterlambat')) {
                $query->where('jenis_izin', 'TL');
            } else if (request()->is('pengajuanizin/sakit')) {
                $query->where('pengajuan_izin.status', 's');
            } else if (request()->is('pengajuanizin/cuti')) {
                $query->where('pengajuan_izin.status', 'c');
            } else if (request()->is('pengajuanizin/koreksipresensi')) {
                $query->where('pengajuan_izin.status', 'k');
            } else if (request()->is('pengajuanizin/perjalanandinas')) {
                $query->where('pengajuan_izin.status', 'p');
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

            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }

            if (!empty($id_kantor)) {
                $query->where('master_karyawan.id_kantor', $id_kantor);
            }

            if (!empty($kode_dept)) {
                $query->where('master_karyawan.kode_dept', $kode_dept);
            }
        }


        // if ($level == "direktur") {
        //     $query->whereIn('nama_jabatan', ['MANAGER', 'GENERAL MANAGER', 'ASST. MANAGER']);
        //     $query->where('hrd', 1);
        // }


        if ($level == "direktur") {
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('dari', [$dari, $sampai]);
            }
            if (request()->is('pengajuanizin')) {
                $query->where('jenis_izin', 'TM');
            } else if (request()->is('pengajuanizin/izinpulang')) {
                $query->where('jenis_izin', 'PL');
            } else if (request()->is('pengajuanizin/izinkeluar')) {
                $query->where('jenis_izin', 'KL');
            } else if (request()->is('pengajuanizin/izinterlambat')) {
                $query->where('jenis_izin', 'TL');
            } else if (request()->is('pengajuanizin/sakit')) {
                $query->where('pengajuan_izin.status', 's');
            } else if (request()->is('pengajuanizin/cuti')) {
                $query->where('pengajuan_izin.status', 'c');
            } else if (request()->is('pengajuanizin/koreksipresensi')) {
                $query->where('pengajuan_izin.status', 'k');
            } else if (request()->is('pengajuanizin/perjalanandinas')) {
                $query->where('pengajuan_izin.status', 'p');
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

            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }

            $query->whereIn('nama_jabatan', ['MANAGER', 'ASST. MANAGER']);
            $query->where('hrd', 1);

            $query->orWhere('nama_jabatan', 'GENERAL MANAGER');
            $query->whereNull('direktur');
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('dari', [$dari, $sampai]);
            }
            if (request()->is('pengajuanizin')) {
                $query->where('jenis_izin', 'TM');
            } else if (request()->is('pengajuanizin/izinpulang')) {
                $query->where('jenis_izin', 'PL');
            } else if (request()->is('pengajuanizin/izinkeluar')) {
                $query->where('jenis_izin', 'KL');
            } else if (request()->is('pengajuanizin/izinterlambat')) {
                $query->where('jenis_izin', 'TL');
            } else if (request()->is('pengajuanizin/sakit')) {
                $query->where('pengajuan_izin.status', 's');
            } else if (request()->is('pengajuanizin/cuti')) {
                $query->where('pengajuan_izin.status', 'c');
            } else if (request()->is('pengajuanizin/koreksipresensi')) {
                $query->where('pengajuan_izin.status', 'k');
            } else if (request()->is('pengajuanizin/perjalanandinas')) {
                $query->where('pengajuan_izin.status', 'p');
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

            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }
        }

        if ($level == "kepala admin" && Auth::user()->pic_presensi == null) {
            $query->where('master_karyawan.id_kantor', $cabang);
            $query->where('master_karyawan.id_perusahaan', "MP");
            $query->where('nama_jabatan', '!=', 'KEPALA ADMIN');
        }

        if ($level == "kepala admin" && Auth::user()->pic_presensi == 1) {
            $query->where('master_karyawan.id_kantor', $cabang);
        }

        if ($level == "kepala penjualan") {
            if (Auth::user()->id == "27") {
                $query->whereIn('master_karyawan.id_kantor', [$cabang, 'PWK']);
            } else {
                $query->where('master_karyawan.id_kantor', $cabang);
            }
            $query->where('nama_jabatan', '!=', 'KEPALA PENJUALAN');
            $query->where('master_karyawan.id_perusahaan', "PCF");
        }

        if ($level == "manager pembelian") {
            $query->where('master_karyawan.kode_dept', 'PMB');
        }

        if ($level == "kepala gudang") {
            $query->where('master_karyawan.kode_dept', 'GDG');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'ASST. MANAGER']);
        }

        if ($level == "spv produksi") {
            $query->where('master_karyawan.kode_dept', 'PRD');
            // $query->whereNotIN('nama_jabatan', ['MANAGER', 'SUPERVISOR']);
        }

        if ($level == "manager produksi") {
            $query->whereIn('master_karyawan.kode_dept', ['PRD', 'MTC']);
            $query->where('nama_jabatan', '!=', 'MANAGER');
        }

        if ($level == "manager ga") {
            $query->where('master_karyawan.kode_dept', 'GAF');
            $query->where('nama_jabatan', '!=', 'MANAGER');
        }

        if ($level == "emf") {
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('dari', [$dari, $sampai]);
            }
            if (request()->is('pengajuanizin')) {
                $query->where('jenis_izin', 'TM');
            } else if (request()->is('pengajuanizin/izinpulang')) {
                $query->where('jenis_izin', 'PL');
            } else if (request()->is('pengajuanizin/izinkeluar')) {
                $query->where('jenis_izin', 'KL');
            } else if (request()->is('pengajuanizin/izinterlambat')) {
                $query->where('jenis_izin', 'TL');
            } else if (request()->is('pengajuanizin/sakit')) {
                $query->where('pengajuan_izin.status', 's');
            } else if (request()->is('pengajuanizin/cuti')) {
                $query->where('pengajuan_izin.status', 'c');
            } else if (request()->is('pengajuanizin/koreksipresensi')) {
                $query->where('pengajuan_izin.status', 'k');
            } else if (request()->is('pengajuanizin/perjalanandinas')) {
                $query->where('pengajuan_izin.status', 'p');
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
            $query->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'HRD']);
            $query->where('nama_jabatan', '=', 'MANAGER');
            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }

            $query->orWhere('master_karyawan.kode_dept', 'PDQ');
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('dari', [$dari, $sampai]);
            }
            if (request()->is('pengajuanizin')) {
                $query->where('jenis_izin', 'TM');
            } else if (request()->is('pengajuanizin/izinpulang')) {
                $query->where('jenis_izin', 'PL');
            } else if (request()->is('pengajuanizin/izinkeluar')) {
                $query->where('jenis_izin', 'KL');
            } else if (request()->is('pengajuanizin/izinterlambat')) {
                $query->where('jenis_izin', 'TL');
            } else if (request()->is('pengajuanizin/sakit')) {
                $query->where('pengajuan_izin.status', 's');
            } else if (request()->is('pengajuanizin/cuti')) {
                $query->where('pengajuan_izin.status', 'c');
            } else if (request()->is('pengajuanizin/koreksipresensi')) {
                $query->where('pengajuan_izin.status', 'k');
            } else if (request()->is('pengajuanizin/perjalanandinas')) {
                $query->where('pengajuan_izin.status', 'p');
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
            $query->where('nama_jabatan', '!=', 'MANAGER');
            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }
        }

        // if ($level == "admin pdqc") {
        //     $listkaryawan = [
        //         '08.12.100',
        //         '11.10.090',
        //         '13.02.198',
        //         '91.01.016',
        //         '03.04.045',
        //         '08.05.042',
        //         '12.09.182',
        //         '05.01.055',
        //         '13.03.202'
        //     ];

        //     $query->whereIn('nik', $listkaryawan);
        // }

        // if ($level == "spv pdqc") {
        //     $listkaryawan = [
        //         '13.03.200',
        //         '14.08.220',
        //         '13.07.021',
        //         '15.05.174',
        //         '10.08.128',
        //         '13.09.206',
        //         '13.09.209',
        //         '19.09.303',
        //         '21.06.304',
        //         '16.01.069',
        //         '18.03.305'
        //     ];

        //     $query->whereIn('nik', $listkaryawan);
        // }




        if ($level == "manager marketing") {
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'REGIONAL SALES MANAGER');
        }

        if ($level == "manager audit") {
            $query->where('master_karyawan.kode_dept', 'ADT');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $query->whereIn('master_karyawan.id_kantor', $list_wilayah);
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'KEPALA PENJUALAN');
            $query->where('id_perusahaan', 'PCF');
        }


        if (Auth::user()->id == 57) {
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('dari', [$dari, $sampai]);
            }
            if (request()->is('pengajuanizin')) {
                $query->where('jenis_izin', 'TM');
            } else if (request()->is('pengajuanizin/izinpulang')) {
                $query->where('jenis_izin', 'PL');
            } else if (request()->is('pengajuanizin/izinkeluar')) {
                $query->where('jenis_izin', 'KL');
            } else if (request()->is('pengajuanizin/izinterlambat')) {
                $query->where('jenis_izin', 'TL');
            } else if (request()->is('pengajuanizin/sakit')) {
                $query->where('pengajuan_izin.status', 's');
            } else if (request()->is('pengajuanizin/cuti')) {
                $query->where('pengajuan_izin.status', 'c');
            } else if (request()->is('pengajuanizin/koreksipresensi')) {
                $query->where('pengajuan_izin.status', 'k');
            } else if (request()->is('pengajuanizin/perjalanandinas')) {
                $query->where('pengajuan_izin.status', 'p');
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

            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }
            $query->whereIn('grup', [1, 5]);
            $query->where('id_kantor', 'PST');
            $query->where('nama_jabatan', '!=', 'MANAGER');

            $query->orWhere('id_kantor', '!=', 'PST');
            $query->whereIn('grup', [1, 5]);
            $query->where('nama_jabatan', 'KEPALA ADMIN');
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('dari', [$dari, $sampai]);
            }
            if (request()->is('pengajuanizin')) {
                $query->where('jenis_izin', 'TM');
            } else if (request()->is('pengajuanizin/izinpulang')) {
                $query->where('jenis_izin', 'PL');
            } else if (request()->is('pengajuanizin/izinkeluar')) {
                $query->where('jenis_izin', 'KL');
            } else if (request()->is('pengajuanizin/izinterlambat')) {
                $query->where('jenis_izin', 'TL');
            } else if (request()->is('pengajuanizin/sakit')) {
                $query->where('pengajuan_izin.status', 's');
            } else if (request()->is('pengajuanizin/cuti')) {
                $query->where('pengajuan_izin.status', 'c');
            } else if (request()->is('pengajuanizin/koreksipresensi')) {
                $query->where('pengajuan_izin.status', 'k');
            } else if (request()->is('pengajuanizin/perjalanandinas')) {
                $query->where('pengajuan_izin.status', 'p');
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

            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }
        }

        if (Auth::user()->id == 20) {
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('dari', [$dari, $sampai]);
            }
            if (request()->is('pengajuanizin')) {
                $query->where('jenis_izin', 'TM');
            } else if (request()->is('pengajuanizin/izinpulang')) {
                $query->where('jenis_izin', 'PL');
            } else if (request()->is('pengajuanizin/izinkeluar')) {
                $query->where('jenis_izin', 'KL');
            } else if (request()->is('pengajuanizin/izinterlambat')) {
                $query->where('jenis_izin', 'TL');
            } else if (request()->is('pengajuanizin/sakit')) {
                $query->where('pengajuan_izin.status', 's');
            } else if (request()->is('pengajuanizin/cuti')) {
                $query->where('pengajuan_izin.status', 'c');
            } else if (request()->is('pengajuanizin/koreksipresensi')) {
                $query->where('pengajuan_izin.status', 'k');
            } else if (request()->is('pengajuanizin/perjalanandinas')) {
                $query->where('pengajuan_izin.status', 'p');
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

            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }
            $query->whereIn('grup', [1, 5]);
            $query->where('id_kantor', 'PST');
            // $query->where('nama_jabatan', '!=', 'MANAGER');

            $query->orWhere('id_kantor', '!=', 'PST');
            $query->whereIn('grup', [1, 5]);
            $query->where('nama_jabatan', 'KEPALA ADMIN');
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('dari', [$dari, $sampai]);
            }
            if (request()->is('pengajuanizin')) {
                $query->where('jenis_izin', 'TM');
            } else if (request()->is('pengajuanizin/izinpulang')) {
                $query->where('jenis_izin', 'PL');
            } else if (request()->is('pengajuanizin/izinkeluar')) {
                $query->where('jenis_izin', 'KL');
            } else if (request()->is('pengajuanizin/izinterlambat')) {
                $query->where('jenis_izin', 'TL');
            } else if (request()->is('pengajuanizin/sakit')) {
                $query->where('pengajuan_izin.status', 's');
            } else if (request()->is('pengajuanizin/cuti')) {
                $query->where('pengajuan_izin.status', 'c');
            } else if (request()->is('pengajuanizin/koreksipresensi')) {
                $query->where('pengajuan_izin.status', 'k');
            } else if (request()->is('pengajuanizin/perjalanandinas')) {
                $query->where('pengajuan_izin.status', 'p');
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

            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }
        }

        // if (Auth::user()->id == 20) {
        //     $query->whereIn('grup', [1, 5]);
        //     $query->where('id_kantor', 'PST');
        //     $query->where('nama_jabatan', 'MANAGER');
        // }

        if (Auth::user()->id == 69) {
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('dari', [$dari, $sampai]);
            }
            if (request()->is('pengajuanizin')) {
                $query->where('jenis_izin', 'TM');
            } else if (request()->is('pengajuanizin/izinpulang')) {
                $query->where('jenis_izin', 'PL');
            } else if (request()->is('pengajuanizin/izinkeluar')) {
                $query->where('jenis_izin', 'KL');
            } else if (request()->is('pengajuanizin/izinterlambat')) {
                $query->where('jenis_izin', 'TL');
            } else if (request()->is('pengajuanizin/sakit')) {
                $query->where('pengajuan_izin.status', 's');
            } else if (request()->is('pengajuanizin/cuti')) {
                $query->where('pengajuan_izin.status', 'c');
            } else if (request()->is('pengajuanizin/koreksipresensi')) {
                $query->where('pengajuan_izin.status', 'k');
            } else if (request()->is('pengajuanizin/perjalanandinas')) {
                $query->where('pengajuan_izin.status', 'p');
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

            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }
            $query->where('grup', 11);
            $query->where('id_kantor', 'PST');
            $query->where('nama_jabatan', '!=', 'MANAGER');
            $query->orWhere('grup', 4);
            $query->where('id_kantor', 'PST');
            $query->where('nama_jabatan', 'MANAGER');
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('dari', [$dari, $sampai]);
            }
            if (request()->is('pengajuanizin')) {
                $query->where('jenis_izin', 'TM');
            } else if (request()->is('pengajuanizin/izinpulang')) {
                $query->where('jenis_izin', 'PL');
            } else if (request()->is('pengajuanizin/izinkeluar')) {
                $query->where('jenis_izin', 'KL');
            } else if (request()->is('pengajuanizin/izinterlambat')) {
                $query->where('jenis_izin', 'TL');
            } else if (request()->is('pengajuanizin/sakit')) {
                $query->where('pengajuan_izin.status', 's');
            } else if (request()->is('pengajuanizin/cuti')) {
                $query->where('pengajuan_izin.status', 'c');
            } else if (request()->is('pengajuanizin/koreksipresensi')) {
                $query->where('pengajuan_izin.status', 'k');
            } else if (request()->is('pengajuanizin/perjalanandinas')) {
                $query->where('pengajuan_izin.status', 'p');
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

            if (!empty($nama_karyawan)) {
                $query->where('nama_karyawan', 'like', '%' . $nama_karyawan . '%');
            }
        }

        if (Auth::user()->id == 73) {
            $query->where('grup', 9);
            $query->where('id_kantor', 'PST');
        }

        $query->orderBy('kode_izin', 'desc');
        $pengajuan_izin = $query->get();
        return $pengajuan_izin;
    }
}
