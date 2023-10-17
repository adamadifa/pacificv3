<?php

namespace App\Providers;

use App\Models\Pengajuanizin;
use App\Models\Penilaiankaryawan;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use App\Profil;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View as View;
use Request;



class GlobalProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Guard $auth)
    {

        view()->composer('*', function ($view) use ($auth) {
            if (Auth::check()) {
                $level = $auth->user()->level;
                $kat_jabatan = $auth->user()->kategori_jabatan;
                $getcbg = $auth->user()->kode_cabang;
                $id_user = $auth->user()->id;
                $kode_dept_presensi = $auth->user()->kode_dept_presensi;


                if ($kat_jabatan != null) {
                    $list_dept = $auth->user()->kode_dept != null ?  unserialize($auth->user()->kode_dept) : NULL;
                    $list_wilayah = $auth->user()->wilayah != null ? unserialize($auth->user()->wilayah) : NULL;
                    $approve_jabatan = $auth->user()->approve_jabatan != null ? unserialize($auth->user()->approve_jabatan) : [];
                    // $dept = $list_dept != null ? "'" . implode("', '", $list_dept) . "'" : '';
                    // $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';


                    // SELECT id_kategori_jabatan,COUNT(kode_penilaian) as jml FROM hrd_penilaian
                    // WHERE status IS NULL
                    // AND id_perusahaan = 'MP'
                    // AND kode_dept IN (" . $dept . ") " . $whereCabang . "
                    // GROUP BY id_kategori_jabatan

                    $inisial = ["" => "", "kepala admin" => "KA", "kepala penjualan" => "KP", "rsm" => "RSM", "manager" => "M", "general manager" => "GM", "manager hrd" => "HRD", "direktur" => "DIRUT"];
                    $kategori_jabatan_user = DB::table('hrd_kategori_jabatan')->where('id', $kat_jabatan)->first();
                    $kat_jab_user =  $kategori_jabatan_user != null ? $kategori_jabatan_user->kategori_jabatan : '';
                    $field_kategori = strtolower($inisial[strtolower($kat_jab_user)]);

                    $qpenilaian = Penilaiankaryawan::query();
                    $qpenilaian->selectRaw('COUNT(kode_penilaian) as jml');
                    $qpenilaian->whereNull('status');
                    if ($auth->user()->level != "admin" and $kat_jab_user != "DIREKTUR" and $kat_jab_user != "MANAGER HRD") {
                        $qpenilaian->whereIn('kode_dept', $list_dept);
                        if ($auth->user()->kode_cabang != "PCF") {
                            $qpenilaian->where('id_kantor', $getcbg);
                        } else {
                            if (Auth::user()->level == "rsm") {
                                $qpenilaian->whereIn('id_kantor', $list_wilayah);
                            }
                        }
                    }

                    $qpenilaian->whereIn('id_kategori_jabatan', $approve_jabatan);
                    $qpenilaian->whereNull($field_kategori);
                    if ($auth->user()->level == "direktur") {
                        $qpenilaian->whereNotNull('hrd_penilaian.hrd');
                    }
                    $penilaian = $qpenilaian->first();
                    $jmlpenilaiankar = $penilaian->jml;
                } else {
                    $jmlpenilaiankar = 0;
                }
                $memo = DB::table('memo')
                    ->selectRaw("memo.id,tanggal,no_memo,judul_memo,kode_dept,kategori,link,totaldownload,name,memo.id_user,cekread.id_user as status_read")
                    ->leftJoin(
                        DB::raw("(
                                SELECT id,COUNT(id_user) as totaldownload FROM memo_download GROUP BY id
                        ) download"),
                        function ($join) {
                            $join->on('memo.id', '=', 'download.id');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(
                            SELECT id,id_user FROM memo_download WHERE id_user='$id_user'
                        ) cekread"),
                        function ($join) {
                            $join->on('memo.id', '=', 'cekread.id');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(
                                SELECT id,name FROM users
                        ) user"),
                        function ($join) {
                            $join->on('memo.id_user', '=', 'user.id');
                        }
                    )
                    ->whereNull('cekread.id_user');

                $memo_unread = $memo->count();
                $memo_data = $memo->get();

                $ticket_pending = DB::table('ticket')->where('status', '!=', 2)->where('id_user', $id_user)->count();
                $ticket_pending_approve = DB::table('ticket')->where('status', 0)->count();
                $ticket_pending_done = DB::table('ticket')->where('status', 1)->count();




                $users = User::select("*")
                    ->whereNotNull('last_seen')
                    ->orderBy('last_seen', 'DESC')
                    ->paginate(10);


                //Pengajuan Izin
                $qpi = Pengajuanizin::query();

                $qpi->selectRaw("SUM(IF(status='i' AND jenis_izin = 'TM',1,0)) as tidakmasuk,
                    SUM(IF(status='i' AND jenis_izin = 'TL',1,0)) as terlambat,
                    SUM(IF(status='i' AND jenis_izin = 'PL',1,0)) as pulang,
                    SUM(IF(status='i' AND jenis_izin = 'KL',1,0)) as keluar,
                    SUM(IF(status='c',1,0)) as cuti,
                    SUM(IF(status='s',1,0)) as sakit,
                    SUM(IF(status='k',1,0)) as koreksi,
                    SUM(IF(status='p',1,0)) as perjalanandinas,
                    COUNT(kode_izin) as approvedirut
                    ");
                $qpi->leftJoin('master_karyawan', 'pengajuan_izin.nik', '=', 'master_karyawan.nik');
                $qpi->leftjoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
                $qpi->leftjoin('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');

                if ($level != "emf"  && $level != "direktur" && $level != "manager hrd") {
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }

                    if (!empty(Auth::user()->pic_presensi)) {
                        if ($getcbg != "PCF") {
                            $qpi->where('master_karyawan.id_kantor', $getcbg);
                        }
                    }
                    $qpi->where('status_approved', 0);
                } else if (Auth::user()->id != 57 && Auth::user()->id != 20) {
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }

                    if (!empty(Auth::user()->pic_presensi)) {
                        if ($getcbg != "PCF") {
                            $qpi->where('master_karyawan.id_kantor', $getcbg);
                        }
                    }
                }



                //Kepala Admin
                if ($level == "kepala admin" && Auth::user()->pic_presensi == null) {
                    $qpi->where('master_karyawan.id_kantor', $getcbg);
                    $qpi->where('master_karyawan.id_perusahaan', "MP");
                    $qpi->where('nama_jabatan', '!=', 'KEPALA ADMIN');
                }

                if ($level == "kepala admin" && Auth::user()->pic_presensi == 1) {
                    $qpi->where('master_karyawan.id_kantor', $getcbg);
                }

                //Kepala Penjualan

                if ($level == "kepala penjualan") {
                    if (Auth::user()->id == "27") {
                        $qpi->whereIn('master_karyawan.id_kantor', [$getcbg, 'PWK']);
                    } else {
                        $qpi->where('master_karyawan.id_kantor', $getcbg);
                    }
                    $qpi->where('nama_jabatan', '!=', 'KEPALA PENJUALAN');
                    $qpi->where('master_karyawan.id_perusahaan', "PCF");
                }

                //Manager Pembelian

                if ($level == "manager pembelian") {
                    $qpi->where('master_karyawan.kode_dept', 'PMB');
                }


                //Kepala Gudang

                if ($level == "kepala gudang") {
                    $qpi->where('master_karyawan.kode_dept', 'GDG');
                    $qpi->whereNotIN('nama_jabatan', ['MANAGER', 'ASST. MANAGER']);
                }

                //SPV Produksi

                if ($level == "spv produksi") {
                    $qpi->where('master_karyawan.kode_dept', 'PRD');
                    $qpi->whereNotIN('nama_jabatan', ['MANAGER', 'SUPERVISOR']);
                }

                //Manager Produksi

                if ($level == "manager produksi") {
                    $qpi->whereIn('master_karyawan.kode_dept', ['PRD', 'MTC']);
                    $qpi->where('nama_jabatan', '!=', 'MANAGER');
                }

                if ($level == "manager ga") {
                    $qpi->where('master_karyawan.kode_dept', 'GAF');
                    $qpi->where('nama_jabatan', '!=', 'MANAGER');
                }

                //EMF

                if ($level == "emf") {
                    $jabatan_emf = array('MANAGER', 'ASST. MANAGER');
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }
                    $qpi->where('status_approved', 0);
                    $qpi->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'HRD']);
                    $qpi->whereIn('nama_jabatan', $jabatan_emf);
                    $qpi->orWhere('master_karyawan.kode_dept', 'PDQ');
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }
                    $qpi->where('status_approved', 0);
                    $qpi->where('nama_jabatan', '!=', 'MANAGER');
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

                //     $qpi->whereIn('nik', $listkaryawan);
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

                //     $qpi->whereIn('nik', $listkaryawan);
                // }


                //Manager Marketing

                if ($level == "manager marketing") {
                    $qpi->where('master_karyawan.kode_dept', 'MKT');
                    $qpi->where('nama_jabatan', 'REGIONAL SALES MANAGER');
                }

                //Manager Audit
                if ($level == "manager audit") {
                    $qpi->where('master_karyawan.kode_dept', 'ADT');
                }


                //RSM

                if ($level == "rsm") {
                    $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
                    $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
                    $qpi->whereIn('master_karyawan.id_kantor', $list_wilayah);
                    $qpi->where('master_karyawan.kode_dept', 'MKT');
                    $qpi->where('nama_jabatan', 'KEPALA PENJUALAN');
                    $qpi->where('id_perusahaan', 'PCF');
                }


                //Pa Ardi
                if (Auth::user()->id == 57) {
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }

                    if (!empty(Auth::user()->pic_presensi)) {
                        if ($getcbg != "PCF") {
                            $qpi->where('master_karyawan.id_kantor', $getcbg);
                        }
                    }
                    $qpi->where('status_approved', 0);
                    $qpi->whereIn('grup', [1, 5]);
                    $qpi->where('id_kantor', 'PST');
                    $qpi->where('nama_jabatan', '!=', 'MANAGER');
                    $qpi->orWhere('status_approved', 0);
                    $qpi->whereIn('grup', [1, 5]);
                    $qpi->where('id_kantor', '!=', 'PST');
                    $qpi->where('nama_jabatan', '=', 'KEPALA ADMIN');
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }

                    if (!empty(Auth::user()->pic_presensi)) {
                        if ($getcbg != "PCF") {
                            $qpi->where('master_karyawan.id_kantor', $getcbg);
                        }
                    }
                }

                //Pa Ridwan

                if (Auth::user()->id == 20) {
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }

                    if (!empty(Auth::user()->pic_presensi)) {
                        if ($getcbg != "PCF") {
                            $qpi->where('master_karyawan.id_kantor', $getcbg);
                        }
                    }
                    $qpi->where('status_approved', 0);
                    $qpi->whereIn('grup', [1, 5]);
                    $qpi->where('id_kantor', 'PST');
                    //$qpi->where('nama_jabatan', '!=', 'MANAGER');
                    $qpi->orWhere('status_approved', 0);
                    $qpi->whereIn('grup', [1, 5]);
                    $qpi->where('id_kantor', '!=', 'PST');
                    $qpi->where('nama_jabatan', '=', 'KEPALA ADMIN');
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }

                    if (!empty(Auth::user()->pic_presensi)) {
                        if ($getcbg != "PCF") {
                            $qpi->where('master_karyawan.id_kantor', $getcbg);
                        }
                    }
                }

                // if (Auth::user()->id == 20) {
                //     $qpi->whereIn('grup', [1, 5]);
                //     $qpi->where('id_kantor', 'PST');
                //     $qpi->where('nama_jabatan', 'MANAGER');
                // }


                //Rani Gudang

                if (Auth::user()->id == 69) {
                    $qpi->where('grup', 11);
                    $qpi->where('id_kantor', 'PST');
                    $qpi->where('nama_jabatan', '!=', 'MANAGER');
                    $qpi->orWhere('grup', 4);
                    $qpi->where('id_kantor', 'PST');
                    $qpi->where('nama_jabatan', 'MANAGER');
                }

                //Olga

                if (Auth::user()->id == 73) {
                    $qpi->where('grup', 10);
                    $qpi->where('id_kantor', 'PST');
                }

                if ($level == "direktur") {
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }

                    if (!empty(Auth::user()->pic_presensi)) {
                        if ($getcbg != "PCF") {
                            $qpi->where('master_karyawan.id_kantor', $getcbg);
                        }
                    }
                    $qpi->whereIn('nama_jabatan', ['MANAGER', 'ASST. MANAGER']);
                    $qpi->whereNull('direktur');
                    $qpi->where('hrd', 1);
                    $qpi->orWhere('nama_jabatan', 'GENERAL MANAGER');
                    $qpi->whereNull('direktur');
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }

                    if (!empty(Auth::user()->pic_presensi)) {
                        if ($getcbg != "PCF") {
                            $qpi->where('master_karyawan.id_kantor', $getcbg);
                        }
                    }
                }


                if ($level == "manager hrd") {
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }

                    if (!empty(Auth::user()->pic_presensi)) {
                        if ($getcbg != "PCF") {
                            $qpi->where('master_karyawan.id_kantor', $getcbg);
                        }
                    }

                    // if (Auth::user()->id == 164) {
                    //     $qpi->where('head_dept', 1);
                    // }
                    $qpi->where('head_dept', 1);
                    $qpi->where('kode_dept', '!=', 'HRD');
                    $qpi->whereNull('hrd');
                    $qpi->whereNull('head_dept');
                    $qpi->where('kode_dept', 'HRD');
                    $qpi->whereNull('hrd');
                    $qpi->orWhere('direktur', 1);
                    $qpi->whereNull('hrd');
                    if (!empty($kode_dept_presensi)) {
                        $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                    }

                    if (!empty(Auth::user()->pic_presensi)) {
                        if ($getcbg != "PCF") {
                            $qpi->where('master_karyawan.id_kantor', $getcbg);
                        }
                    }
                }


                // if ($level == "direktur") {
                //     if (!empty($kode_dept_presensi)) {
                //         $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                //     }

                //     if (!empty(Auth::user()->pic_presensi)) {
                //         if ($getcbg != "PCF") {
                //             $qpi->where('master_karyawan.id_kantor', $getcbg);
                //         }
                //     }
                //     $qpi->whereNull('direktur');
                //     $qpi->whereIn('nama_jabatan', ['MANAGER', 'ASST. MANAGER']);
                //     $qpi->where('hrd', 1);
                //     //$qpi->where('nama_jabatan', '!=', 'MANAGER');
                //     $qpi->whereNull('direktur');
                //     $qpi->orWhere('nama_jabatan', 'GENERAL MANAGER');
                //     $qpi->whereNull('hrd');
                //     if (!empty($kode_dept_presensi)) {
                //         $qpi->where('master_karyawan.kode_dept', $kode_dept_presensi);
                //     }

                //     if (!empty(Auth::user()->pic_presensi)) {
                //         if ($getcbg != "PCF") {
                //             $qpi->where('master_karyawan.id_kantor', $getcbg);
                //         }
                //     }
                // }


                $pi = $qpi->first();
            } else {
                $level = "";
                $getcbg = "";
                $kat_jabatan = "";
                $id_user = "";
                $memo_unread =  null;
                $memo_data =  null;
                $ticket_pending =  null;
                $ticket_pending_approve =  null;
                $ticket_pending_done =  null;
                $jmlpenilaiankar = null;
                $users = null;
                $pi = null;
                $kode_dept_presensi = null;
            }

            $cabangpkp = ['TSM', 'BDG', 'PWT', 'BGR'];

            if ($level == "salesman") {

                if ($getcbg == "BKI") {
                    if (request()->is('inputpenjualanppn')) {
                        $pajak = "1";
                    } else {
                        $pajak = "0";
                    }
                } else {
                    if (in_array($getcbg, $cabangpkp)) {
                        $pajak = 1;
                    } else {
                        $pajak = 0;
                    }
                }
            } else {
                if (request()->is('inputpenjualanppn')) {
                    $pajak = "1";
                } else {
                    $pajak = "0";
                }
            }

            //Aproval
            $operator_pusat = ['manager', 'gm', 'manager hrd', 'direktur'];


            //Dashboard

            $memo_menu = ['admin'];
            $memo_tambah_hapus = ['admin', 'admin medsos', 'manager accounting', 'manager hrd'];


            $tracking_salesman = ['admin', 'manager marketing', 'direktur', 'manager accounting', 'kepala admin', 'kepala penjualan', 'rsm'];
            $map_pelanggan = ['admin', 'manager marketing', 'manager accounting', 'kepala admin', 'kepala penjualan', 'direktur', 'rsm'];
            $scan = ['admin', 'salesman'];

            $dashboardadmin = ['admin', 'manager marketing', 'rsm', 'general manager', 'direktur'];
            $dashboardkepalapenjualan = ['kepala penjualan'];
            $dashboardkepalaadmin = ['kepala admin', 'admin pusat'];
            $dashboardadminpenjualan = ['admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit'];
            $dashboardaccounting = ['manager accounting', 'spv accounting'];
            $dashboardstaffkeuangan = ['staff keuangan', 'staff keuangan 2', 'staff keuangan 3'];
            $dashboardadminkaskecil = ['admin kas kecil', 'audit', 'manager audit'];
            $dashboardpembelian = ['manager pembelian', 'admin pembelian'];
            $dashboard_sfa = ['admin', 'kepala admin', 'kepala penjualan', 'rsm', 'manager marketing', 'manager accounting', 'direktur'];

            //Data Master
            $datamaster = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'audit', 'manager audit',
                'manager accounting', 'spv accounting', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'manager marketing', 'rsm', 'direktur',
                'manager pembelian', 'admin pembelian', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin gudang logistik', 'admin gudang bahan', 'spv pdqc',
                'manager ga', 'general affair', 'manager ga', 'admin garut', 'admin pajak 2', 'manager hrd', 'kepala gudang', 'manager produksi', 'spv produksi', 'emf', 'admin produksi', 'spv pdqc', 'admin pdqc', 'spv maintenance'
            ];

            $pasar_menu = ['admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'audit', 'manager audit', 'kepala admin', 'admin penjualan dan kas kecil', 'direktur', 'manager accounting', 'general manager', 'admin garut', 'admin pajak 2', 'admin pusat'];
            $pasar_tambah = ['admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin penjualan dan kas kecil', 'admin garut', 'admin pusat'];
            $pasar_hapus = ['admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin penjualan dan kas kecil', 'admin garut', 'admin pusat'];
            //Pelanggan
            $pelanggan = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'manager accounting', 'spv accounting', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager marketing', 'rsm', 'direktur', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin garut', 'admin pajak 2'
            ];
            $pelanggan_tambah = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut'
            ];
            $pelanggan_edit = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut'
            ];
            $pelanggan_hapus = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut'
            ];
            $pelanggan_ajuanlimit = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut'
            ];

            //Salesman
            $salesman = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'audit', 'manager audit', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'direktur', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin garut', 'admin pajak 2'
            ];
            $salesman_tambah = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut'
            ];
            $salesman_edit = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut'
            ];
            $salesman_hapus = [
                'admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut'
            ];

            //Master Karyawan
            $karyawan_view = ['admin', 'manager hrd', 'kepala admin', 'kepala penjualan', 'manager pembelian', 'kepala gudang', 'manager produksi', 'spv produksi', 'manager accounting', 'manager ga', 'emf', 'manager marketing', 'rsm', 'direktur', 'spv pdqc', 'admin pdqc', 'manager audit', 'spv maintenance'];
            $karyawan_pinjaman = ['admin', 'kepala admin', 'kepala penjualan', 'manager pembelian', 'kepala gudang', 'manager produksi', 'spv produksi', 'manager accounting', 'manager ga', 'emf', 'manager marketing', 'rsm', 'direktur', 'spv pdqc', 'admin pdqc', 'manager audit', 'spv maintenance'];



            $karyawan_tambah = ['admin', 'manager hrd'];
            $karyawan_edit = ['admin', 'manager hrd'];
            $karyawan_hapus = ['admin', 'manager hrd'];


            $gaji_tambah = ['admin', 'manager accounting', 'manager hrd'];
            $gaji_edit = ['admin', 'manager accounting', 'manager hrd'];
            $gaji_hapus = ['admin', 'manager accounting', 'manager hrd'];

            $insentif_tambah = ['admin', 'manager accounting', 'manager hrd'];
            $insentif_edit = ['admin', 'manager accounting', 'manager hrd'];
            $insentif_hapus = ['admin', 'manager accounting', 'manager hrd'];

            //Supplier
            $supplier_menu = ['admin', 'manager pembelian', 'admin pembelian', 'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin pajak 2'];
            $supplier_tambah = ['admin', 'manager pembelian', 'admin pembelian'];
            $supplier_edit = ['admin', 'manager pembelian', 'admin pembelian'];
            $supplier_hapus = ['admin', 'manager pembelian', 'admin pembelian'];

            //Barang
            $barang = ['admin', 'manager accounting', 'spv accounting', 'direktur', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'admin pajak 2'];
            $barang_tambah = ['admin'];
            $barang_edit = ['admin'];
            $barang_hapus = ['admin'];

            //Barang
            $barangpembelian = [
                'admin', 'manager pembelian', 'admin pembelian', 'manager accounting',
                'spv accounting', 'audit', 'manager audit', 'admin gudang logistik', 'admin gudang bahan', 'spv pdqc',
                'manager ga', 'general affair', 'manager ga', 'admin pajak 2'
            ];
            $barangpembelian_tambah = ['admin', 'manager pembelian', 'admin pembelian', 'admin gudang logistik', 'admin gudang bahan', 'spv pdqc', 'manager ga', 'general affair', 'manager ga'];
            $barangpembelian_edit = ['admin', 'manager pembelian', 'admin pembelian', 'admin gudang logistik', 'admin gudang bahan', 'spv pdqc', 'manager ga', 'general affair', 'manager ga'];
            $barangpembelian_hapus = ['admin', 'manager pembelian', 'admin pembelian', 'admin gudang logistik', 'admin gudang bahan', 'spv pdqc', 'manager ga', 'general affair', 'manager ga'];

            //Harga
            $harga = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'audit', 'manager audit', 'kepala penjualan', 'kepala admin', 'admin pusat',
                'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'direktur',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin garut', 'admin pajak 2'
            ];
            $harga_hapus = ['admin'];
            $harga_tambah = ['admin'];
            $harga_edit = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala penjualan', 'kepala admin', 'admin pusat',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut'
            ];

            $kendaraan = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'audit', 'manager audit', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'direktur',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin garut', 'admin pajak 2', 'manager ga', 'general affair', 'manager ga'
            ];
            $kendaraan_tambah = ['admin', 'manager ga', 'general affair', 'manager ga'];
            $kendaraan_edit = [
                'admin', 'manager ga', 'manager ga', 'general affair'
            ];
            $kendaraan_hapus = ['admin', 'manager ga', 'general affair', 'manager ga'];


            $cabang = ['admin', 'audit', 'manager audit'];





            //Marketing
            $marketing = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'audit', 'manager audit', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting', 'manager marketing', 'rsm',
                'general manager', 'direktur', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'supervisor sales', 'admin gudang cabang dan marketing',
                'staff keuangan 2', 'staff keuangan 3', 'audit', 'manager audit', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat',
                'admin pajak', 'admin pajak 2', 'admin medsos', 'admin garut', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting'
            ];

            //-----------------------------OMAN-------------------------------------------------
            $oman = ['admin', 'manager marketing', 'rsm', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat', 'kepala admin', 'admin pusat', 'admin garut'];
            $omancabang = ['admin', 'manager marketing', 'rsm', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat', 'kepala admin', 'admin pusat', 'admin garut'];
            $omanmarketing = ['admin', 'manager marketing', 'rsm', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];

            //----------------------------Permintaaan Pengiriman--------------------------------
            $permintaanpengiriman = ['admin', 'admin gudang cabang dan marketing', 'admin garut', 'kepala admin', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil'];
            $permintaanpengiriman_tambah = ['admin', 'admin gudang cabang dan marketing', 'admin gudang pusat', 'spv gudang pusat', 'admin garut', 'kepala admin', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil'];
            $permintaanpengiriman_hapus = ['admin', 'admin gudang cabang dan marketing', 'admin gudang pusat', 'spv gudang pusat', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil'];
            $permintaanpengiriman_proses = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];
            $permintaanpengiriman_gj = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];
            //----------------------------Target Komisi--------------------------------
            $komisi = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager marketing', 'rsm', 'general manager', 'direktur', 'manager accounting', 'spv accounting', 'admin pajak 2'];
            $targetkomisi = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager marketing', 'rsm', 'general manager', 'direktur', 'manager accounting', 'spv accounting', 'admin pajak 2'];
            $targetkomisiinput = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'admin pajak 2'];
            $generatecashin = ['admin'];
            $ratiokomisi = ['admin', 'kepala admin', 'admin pusat', 'kepala penjualan', 'admin pajak 2'];
            $laporan_komisi = ['admin', 'direktur', 'kepala admin', 'admin pusat', 'manager marketing', 'rsm', 'general manager', 'manager accounting', 'spv accounting', 'kepala penjualan', 'admin pajak 2'];
            $inputpotongankomisi = ['manager marketing', 'manager accounting', 'direktur', 'admin'];

            //-----------------------------Penjualan-------------------------------------------
            $penjualan_menu = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit',
                'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting',
                'manager marketing', 'rsm', 'general manager', 'direktur',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin pajak', 'admin pajak 2', 'admin garut', 'audit', 'manager audit', 'kepala penjualan'
            ];
            $penjualan_keuangan = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin pusat', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin pajak 2'
            ];
            $penjualan_input = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin pusat',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut', 'admin pajak 2'
            ];
            $penjualan_hapus = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin pusat',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut'
            ];
            $penjualan_edit = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin pusat',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut'
            ];
            $penjualan_view = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin pusat',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin pajak', 'admin pajak 2', 'admin garut', 'audit', 'manager audit'
            ];
            //Retur
            $retur_view = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin pusat', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut', 'admin pajak 2'
            ];

            //LHP

            $lhp_menu = ['admin'];
            //LImit
            $limitkredit_view = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'audit', 'manager audit',
                'kepala admin', 'admin pusat', 'manager marketing', 'rsm',
                'manager accounting', 'spv accounting', 'general manager',
                'direktur', 'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil',
                'admin garut', 'audit', 'manager audit', 'kepala penjualan'
            ];
            $limitkredit_hapus = ['admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin pusat', 'kepala penjualan', 'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut'];
            $limitkredit_analisa = ['admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin pusat', 'kepala penjualan', 'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin garut', 'kepala penjualan'];
            $penyesuaian_limit = ['admin', 'direktur'];
            //Laporan
            $laporan_penjualan = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit',
                'kepala penjualan', 'kepala admin', 'admin pusat',
                'manager accounting', 'spv accounting', 'manager marketing', 'rsm',
                'manager accounting', 'spv accounting', 'general manager', 'direktur',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil',
                'supervisor sales', 'staff keuangan 2',
                'staff keuangan 3', 'audit', 'manager audit', 'admin pajak', 'admin pajak 2', 'admin medsos', 'admin garut', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting'
            ];
            $harga_net = [
                'admin', 'manager accounting', 'spv accounting',
                'manager marketing', 'rsm', 'general manager', 'direktur', 'audit', 'manager audit'
            ];
            //--------------------------------Keuangan---------------------------------------------
            $keuangan = [
                'admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'audit', 'manager audit', 'kepala admin', 'admin pusat', 'direktur', 'manager accounting', 'spv accounting', 'general manager',
                'manager marketing', 'rsm', 'kepala penjualan', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin kas kecil', 'audit', 'manager audit', 'kasir', 'audit', 'manager audit', 'admin garut', 'manager pembelian', 'admin pembelian',
                'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'kepala gudang', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'admin garut', 'admin pajak 2', 'manager produksi', 'spv produksi', 'emf', 'manager ga', 'manager hrd', 'admin pdqc', 'spv pdqc', 'spv maintenance'
            ];
            $laporankeuangan_view = [
                'admin', 'direktur', 'general manager', 'manager marketing', 'rsm', 'manager accounting', 'spv accounting',
                'kepala penjualan', 'kepala admin', 'admin pusat', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin kas kecil', 'audit', 'manager audit',
                'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil',
                'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin pajak 2', 'manager hrd', 'manager produksi', 'spv produksi'
            ];
            $laporan_ledger = [
                'admin', 'direktur',
                'general manager', 'manager accounting', 'spv accounting', 'admin kas kecil', 'audit', 'manager audit',
                'admin kas', 'manager accounting', 'admin kas dan penjualan', 'kepala penjualan', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil', 'staff keuangan', 'audit', 'manager audit', 'admin pajak 2',
                'admin pusat', 'staff keuangan 3'
            ];
            $laporan_kaskecil = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3',
                'admin kas kecil', 'audit', 'manager audit', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'kepala penjualan',
                'admin penjualan dan kas kecil', 'manager marketing', 'rsm', 'audit', 'manager audit', 'admin garut', 'admin pajak 2', 'admin penjualan kasir dan kas kecil'
            ];

            $laporan_saldokasbesar = [
                'admin', 'direktur',
                'general manager', 'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan',
                'kepala penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'manager marketing', 'rsm', 'audit', 'manager audit', 'admin pajak 2'
            ];
            $laporan_lpu = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat', 'staff keuangan',
                'staff keuangan 2', 'staff keuangan 3', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'kepala penjualan',
                'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'manager marketing', 'rsm', 'audit', 'manager audit', 'admin pajak 2'
            ];
            $laporan_penjualan_keuangan = [
                'admin', 'direktur',
                'general manager', 'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan',
                'kepala penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'manager marketing', 'rsm', 'audit', 'manager audit', 'admin pajak 2'
            ];
            $laporan_uanglogam = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3',
                'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'kepala penjualan', 'admin persediaan dan kasir',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'manager marketing', 'rsm', 'audit', 'manager audit', 'admin pajak 2'
            ];
            $laporan_rekapbg = [
                'admin', 'direktur',
                'general manager', 'manager accounting', 'spv accounting', 'kepala admin', 'admin pusat',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan',
                'kepala penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'manager marketing', 'rsm', 'audit', 'manager audit', 'admin pajak 2'
            ];
            //Giro
            $giro_view = ['admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin pusat', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin pajak 2'];
            $giro_approved = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'manager keuangan'];
            $giro_hapus = ['admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin pajak 2'];

            //Transfer
            $transfer_view = ['admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'salesman', 'audit', 'manager audit', 'kepala admin', 'admin pusat', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin penjualan dan kas kecil', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin pajak 2'];
            $transfer_approved =  ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'manager keuangan'];

            //Kas Kecil
            $kaskecil_menu  = [
                'admin', 'staff keuangan',
                'staff keuangan 2', 'staff keuangan 3',
                'kepala admin', 'admin pusat', 'admin kas kecil', 'audit', 'manager audit', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kas kecil',
                'admin penjualan dan kas kecil', 'admin garut', 'manager accounting', 'spv accounting', 'admin pajak 2', 'admin penjualan kasir dan kas kecil'
            ];
            $kaskecil_view = [
                'admin', 'kepala admin', 'admin pusat', 'admin kas kecil', 'audit', 'manager audit', 'admin kas', 'manager accounting', 'admin kas dan penjualan',
                'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil',
                'staff keuangan 3', 'admin garut', 'spv accounting', 'admin pajak 2', 'admin penjualan kasir dan kas kecil'
            ];
            $klaim_view = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kepala admin', 'admin pusat', 'admin kas kecil', 'audit', 'manager audit', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil', 'admin garut', 'admin garut', 'manager accounting', 'admin pajak 2', 'admin penjualan kasir dan kas kecil'];
            $klaim_add = ['admin', 'kepala admin', 'admin pusat', 'admin kas kecil', 'audit', 'manager audit', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil', 'staff keuangan 3', 'admin garut', 'admin pajak 2', 'admin penjualan kasir dan kas kecil'];
            $klaim_hapus = ['admin', 'kepala admin', 'admin pusat', 'admin kas kecil', 'audit', 'manager audit', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil', 'staff keuangan 3', 'admin garut', 'admin pajak 2', 'admin penjualan kasir dan kas kecil'];
            $klaim_validasi = ['admin', 'kepala admin', 'admin pusat', 'admin kas kecil', 'audit', 'manager audit', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil', 'staff keuangan 3', 'admin garut', 'admin pajak 2', 'admin penjualan kasir dan kas kecil'];
            $klaim_proses = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'manager accounting', 'admin pajak 2', 'admin penjualan kasir dan kas kecil'];

            //Mutasi Bank
            $mutasibank_view = ['admin', 'kepala admin', 'admin kas kecil', 'audit', 'manager audit', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kas kecil', 'admin penjualan dan kas kecil', 'admin pajak 2', 'admin penjualan kasir dan kas kecil'];

            $pinjaman_view = ['admin', 'kepala admin', 'kepala penjualan', 'manager pembelian', 'kepala gudang', 'manager produksi', 'spv produksi', 'manager accounting', 'manager ga', 'emf', 'manager marketing', 'rsm', 'manager hrd', 'staff keuangan', 'staff keuangan 3', 'admin pdqc', 'spv pdqc', 'manager audit', 'spv maintenance'];


            $pembayaranpinjaman_view = ['admin', 'manager hrd', 'manager accounting', 'staff keuangan', 'staff keuangan 3'];
            $inputbayarpinjaman = ['admin', 'manager hrd', 'manager accounting'];
            $lap_pinjaman = ['admin', 'kepala admin', 'kepala penjualan', 'manager pembelian', 'kepala gudang', 'manager produksi', 'spv produksi', 'manager accounting', 'manager ga', 'emf', 'manager marketing', 'rsm', 'manager hrd', 'direktur', 'staff keuangan', 'staff keuangan 3'];

            $piutangkaryawan_view = ['admin', 'manager accounting', 'manager hrd'];
            $pembayaranpiutangkaryawan_view = ['admin', 'manager accounting', 'manager hrd'];
            $inputbayarpiutangkaryawan = ['admin', 'manager accounting', 'manager hrd'];

            $lap_kasbon = ['admin', 'kepala admin', 'kepala penjualan', 'manager pembelian', 'kepala gudang', 'manager produksi', 'spv produksi', 'manager accounting', 'manager ga', 'emf', 'manager marketing', 'rsm', 'manager hrd', 'direktur', 'staff keuangan', 'staff keuangan 3'];

            $kasbon_view = ['admin', 'kepala admin', 'kepala penjualan', 'manager pembelian', 'kepala gudang', 'manager produksi', 'spv produksi', 'manager accounting', 'manager ga', 'emf', 'manager marketing', 'rsm', 'manager hrd', 'staff keuangan', 'staff keuangan 3', 'admin pdqc', 'spv pdqc', 'manager audit', 'spv maintenance'];
            $pembayarankasbon_view = ['admin', 'manager hrd', 'manager accounting', 'staff keuangan', 'staff keuangan 3'];
            //Ledger
            $ledger_menu  = ['admin', 'staff keuangan', 'spv accounting'];
            $ledger_view = ['admin', 'staff keuangan', 'spv accounting'];
            $ledger_saldoawal = ['admin', 'staff keuangan'];

            //Kas Besar Keuangan
            $kasbesar_menu  = [
                'admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kepala admin', 'admin pusat',
                'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir', 'admin pajak 2',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit'
            ];
            $saldoawalkasbesar_view = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin garut', 'admin pajak 2'];
            $setoran_menu = [
                'admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'kepala admin',
                'admin pusat', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin pajak 2'
            ];
            $setoranpenjualan_view = [
                'admin', 'staff keuangan', 'staff keuangan 2',
                'staff keuangan 3', 'kepala admin', 'admin pusat', 'kasir', 'audit', 'manager audit',
                'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir',
                'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin pajak 2'
            ];
            $setoranpusat_view = [
                'admin', 'staff keuangan',
                'staff keuangan 2', 'staff keuangan 3', 'kepala admin',
                'admin pusat', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan',
                'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'audit', 'manager audit', 'admin pajak 2'
            ];
            $setoranpusat_add = ['admin', 'kasir', 'audit', 'manager audit', 'admin garut', 'kepala admin', 'admin pusat', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin pajak 2'];
            $setoranpusat_edit = ['admin', 'kasir', 'audit', 'manager audit', 'admin garut', 'kepala admin', 'admin pusat', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin pajak 2'];
            $setoranpusat_hapus = ['admin', 'kasir', 'audit', 'manager audit', 'admin garut', 'kepala admin', 'admin pusat', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin pajak 2'];
            $setoranpusat_terimasetoran = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3'];
            $setorangiro_view = ['admin', 'kepala admin', 'admin pusat', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin pajak 2'];
            $setorantransfer_view = ['admin', 'kepala admin', 'admin pusat', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin pajak 2'];
            $belum_disetorkan = ['admin', 'kepala admin', 'admin pusat', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin pajak 2'];
            $lebih_disetorkan = ['admin', 'kepala admin', 'admin pusat', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin pajak 2'];
            $gantilogamtokertas = ['admin', 'kepala admin', 'admin pusat', 'kasir', 'audit', 'manager audit', 'admin garut', 'admin kas', 'manager accounting', 'admin kas dan penjualan', 'admin persediaan dan kasir', 'admin penjualan dan kasir', 'admin penjualan kasir dan kas kecil', 'admin pajak 2'];

            $saldoawalpiutang = ['admin'];
            $datausers = ['admin', 'manager accounting'];

            $kirimlpc = ['admin', 'admin penjualan', 'admin penjualan dan persediaan', 'manager accounting', 'admin kas dan penjualan', 'audit', 'manager audit', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'direktur'];
            $kirimlpc_tambah = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting'];
            $kirimlpc_edit = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting'];
            $kirimlpc_hapus = ['admin', 'kepala penjualan', 'kepala admin', 'admin pusat', 'manager accounting', 'spv accounting'];
            $kirimlpc_approve = ['admin', 'manager accounting', 'spv accounting'];




            //Pembelian
            $pembelian_menu = [
                'admin', 'direktur', 'general manager', 'manager accounting',
                'spv accounting', 'manager pembelian', 'admin pembelian',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3',
                'emf', 'admin pajak', 'admin pajak 2', 'admin gudang logistik', 'admin pusat', 'spv accounting'
            ];
            $pembelian_view = [
                'admin', 'manager pembelian', 'admin pembelian',
                'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin pajak', 'admin pajak 2', 'admin gudang logistik', 'spv accounting'
            ];
            $pembelian_hapus = ['admin', 'manager pembelian', 'admin pembelian'];
            $pembelian_tambah = ['admin', 'manager pembelian', 'admin pembelian'];
            $pembelian_edit = ['admin', 'manager pembelian', 'admin pembelian', 'spv accounting'];


            $pembelian_keuangan = ['admin', 'manager pembelian', 'admin pembelian', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3'];
            $kontrabon_view = ['admin', 'manager pembelian', 'admin pembelian', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3'];
            $kontrabon_edit_hapus = ['admin', 'admin pembelian'];
            $kontrabon_approve = ['admin', 'manager pembelian'];
            $kontrabon_proses = ['admin', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3'];

            $jatuhtempo_view = ['admin', 'manager pembelian', 'admin pembelian'];
            $jurnalkoreksi_view = ['admin', 'manager pembelian', 'admin pembelian'];
            $laporan_pembelian = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian',
                'admin pembelian', 'emf', 'admin pusat', 'admin pajak 2'
            ];
            $laporan_pembayaran_pembelian = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian',
                'emf', 'admin pusat'
            ];
            $laporan_rekappembeliansupplier = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian',
                'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_rekappembelian = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_kartuhutang = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat', 'admin pajak 2'
            ];
            $laporan_auh = [
                'admin', 'direktur', 'general manager', 'manager accounting',
                'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_bahankemasan = ['admin', 'direktur', 'general manager', 'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'];
            $laporan_rekapbahankemasan = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_jurnalkoreksi = [
                'admin', 'direktur', 'general manager', 'manager accounting', 'spv accounting',
                'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_rekapakunpembelian  = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];
            $laporan_rekapkontrabon = [
                'admin', 'direktur', 'general manager',
                'manager accounting', 'spv accounting', 'manager pembelian', 'admin pembelian', 'emf', 'admin pusat'
            ];


            $produksi_menu = [
                'admin', 'direktur', 'manager accounting', 'spv accounting',
                'admin produksi', 'manager produksi', 'spv produksi', 'audit', 'manager audit', 'admin produksi 2', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pdqc'
            ];
            $produksi_analytics = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'admin produksi', 'manager produksi', 'spv produksi', 'emf', 'admin pdqc'];
            $mutasi_produk = ['admin', 'admin produksi 2', 'admin produksi', 'manager produksi', 'spv produksi'];
            $bpbj_view = ['admin', 'admin produksi', 'manager produksi', 'spv produksi'];
            $fsthp_view = ['admin', 'admin produksi 2', 'admin produksi', 'manager produksi', 'spv produksi'];
            $mutasi_barang = ['admin', 'admin produksi', 'manager produksi', 'spv produksi'];
            $pemasukan_produksi = ['admin', 'admin produksi', 'manager produksi', 'spv produksi'];
            $pengeluaran_produksi = ['admin', 'admin produksi', 'manager produksi', 'spv produksi'];
            $saldoawal_mutasibarang_produksi = ['admin', 'admin produksi', 'manager produksi', 'spv produksi'];
            $opname_mutasibarang_produksi = ['admin', 'admin produksi', 'manager produksi', 'spv produksi'];
            $laporan_produksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin produksi', 'manager produksi', 'spv produksi', 'emf', 'admin pdqc'];
            $laporan_mutasiproduksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin produksi', 'manager produksi', 'spv produksi', 'emf', 'admin pdqc'];
            $laporan_rekapmutasiproduksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin produksi', 'manager produksi', 'spv produksi', 'emf', 'admin pdqc'];
            $laporan_pemasukanproduksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin produksi', 'manager produksi', 'spv produksi', 'emf', 'admin pdqc'];
            $laporan_pengeluaranproduksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin produksi', 'manager produksi', 'spv produksi', 'emf', 'admin pdqc'];
            $laporan_rekappersediaanbarangproduksi = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin produksi', 'manager produksi', 'spv produksi', 'emf', 'admin pdqc'];

            //Gudang
            $gudang_menu = [
                'admin', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut',
                'admin gudang', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'kepala penjualan',
                'admin gudang', 'kepala admin', 'admin pusat', 'supervisor sales', 'kepala gudang',
                'admin gudang cabang dan marketing', 'direktur', 'manager accounting',
                'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin gudang logistik',
                'admin gudang bahan', 'spv pdqc', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc', 'admin pajak 2'
            ];
            $gudang_bahan_menu = ['admin', 'kepala gudang', 'kepala gudang', 'admin gudang bahan', 'spv pdqc'];
            $gudang_logistik_menu =  ['admin', 'kepala gudang', 'kepala gudang', 'admin gudang logistik'];
            $gudang_jadi_menu =  ['admin', 'kepala gudang', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];
            $gudang_cabang_menu = [
                'admin',
                'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut',
                'admin persediaan dan kasir', 'admin persediaan dan kas kecil',
                'admin gudang cabang dan marketing', 'audit', 'manager audit', 'admin pajak 2'
            ];
            $laporan_gudang = [
                'admin', 'kepala admin', 'admin pusat',
                'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'kepala penjualan', 'supervisor sales',
                'admin gudang cabang dan marketing', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager',
                'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin gudang logistik', 'admin gudang bahan', 'spv pdqc',
                'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc', 'admin pajak 2'
            ];
            $laporan_gudang_logistik = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf',
                'admin gudang logistik', 'admin pusat'
            ];
            $laporan_gudang_bahan = [
                'admin', 'kepala gudang', 'direktur', 'manager accounting',
                'spv accounting', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat',
                'emf', 'admin gudang bahan', 'spv pdqc', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc', 'admin pajak 2'
            ];
            $laporan_gudang_jadi = [
                'admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'rsm',
                'general manager', 'audit', 'manager audit', 'emf', 'admin pembelian', 'manager pembelian', 'admin pdqc'
            ];
            $laporan_gudang_cabang = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'supervisor sales',
                'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'emf', 'admin pajak 2'
            ];


            //Gudang Logistik
            $pemasukan_gudanglogisitik = ['admin', 'kepala gudang', 'admin gudang logistik'];
            $approve_pembelian = ['admin', 'kepala gudang', 'admin gudang logistik'];
            $pengeluaran_gudanglogistik = ['admin', 'kepala gudang', 'admin gudang logistik'];
            $saldoawal_gudanglogistik = ['admin', 'kepala gudang', 'admin gudang logistik'];
            $opname_gudanglogistik = ['admin', 'kepala gudang', 'admin gudang logistik'];



            //Gudang Bahan
            $pemasukan_gudangbahan = ['admin', 'kepala gudang', 'admin gudang bahan', 'spv pdqc'];
            $pengeluaran_gudangbahan = ['admin', 'kepala gudang', 'admin gudang bahan', 'spv pdqc'];
            $saldoawal_gudangbahan = ['admin', 'kepala gudang', 'admin gudang bahan', 'spv pdqc'];
            $opname_gudangbahan = ['admin', 'kepala gudang', 'admin gudang bahan', 'spv pdqc'];

            //Gudang Jadi Pusat
            $permintaan_produksi_view = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat', 'kepala gudang', 'admin produksi', 'manager produksi', 'spv produksi'];
            $mutasi_produk_gj = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];
            $fsthp_gj_view = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];
            $suratjalan_view = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];
            $suratjalan_cetak = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];
            $suratjalan_hapus = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];
            $repackgj_view = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];
            $rejectgj_view = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];
            $lainnyagj_view = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];
            $angkutan_view = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];

            //Kontrabon Angkutan

            $gudang_jadi_keuangan = ['admin', 'kepala gudang', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin gudang pusat', 'spv gudang pusat'];
            $kontrabon_angkutan_view = ['admin', 'kepala gudang', 'staff keuangan', 'staff keuangan 2', 'staff keuangan 3', 'admin gudang pusat', 'spv gudang pusat'];
            $kontrabon_angkutan_hapus = ['admin', 'kepala gudang', 'admin gudang pusat', 'spv gudang pusat'];

            //Gudang Cabang

            $saldoawal_gs_view = ['admin', 'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin gudang cabang dan marketing', 'audit', 'manager audit', 'admin pajak 2'];
            $saldoawal_bs_view = ['admin', 'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin gudang cabang dan marketing', 'audit', 'manager audit', 'admin pajak 2'];
            $dpb_view = ['admin', 'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin gudang cabang dan marketing', 'audit', 'manager audit', 'admin pajak 2'];

            $fpb_menu = ['admin', 'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin gudang cabang dan marketing', 'audit', 'manager audit', 'admin pajak 2'];
            $mutasi_barang_cab_view = ['admin', 'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin gudang cabang dan marketing', 'audit', 'manager audit', 'admin pajak 2'];
            $suratjalancab_view = ['admin', 'admin gudang', 'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut', 'admin persediaan dan kasir', 'admin persediaan dan kas kecil', 'admin gudang cabang dan marketing', 'audit', 'manager audit', 'admin pajak 2'];


            //Laporan Gudang Logistik
            $laporan_pemasukan_gl = [
                'admin', 'kepala gudang', 'admin gudang logistik', 'direktur',
                'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pusat'
            ];
            $laporan_pengeluaran_gl = [
                'admin', 'kepala gudang', 'admin gudang logistik', 'direktur',
                'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pusat'
            ];
            $laporan_persediaan_gl = [
                'admin', 'kepala gudang', 'admin gudang logistik', 'direktur',
                'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pusat'
            ];
            $laporan_persediaanopname_gl = [
                'admin', 'kepala gudang', 'admin gudang logistik',
                'direktur', 'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pusat'
            ];

            //Laporan Gudang Bahan
            $laporan_pemasukan_gb = [
                'admin', 'kepala gudang', 'admin gudang bahan', 'spv pdqc',
                'direktur', 'manager accounting', 'spv accounting',
                'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc', 'admin pajak 2'
            ];
            $laporan_pengeluaran_gb = [
                'admin', 'kepala gudang', 'admin gudang bahan', 'spv pdqc', 'direktur',
                'manager accounting', 'spv accounting', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat',
                'emf', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc', 'admin pajak 2'
            ];
            $laporan_persediaan_gb = [
                'admin', 'kepala gudang', 'admin gudang bahan', 'spv pdqc',
                'direktur', 'manager accounting', 'spv accounting', 'audit', 'manager audit',
                'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc',
                'admin pajak 2'
            ];
            $laporan_kartugudang = [
                'admin', 'kepala gudang', 'admin gudang bahan', 'spv pdqc',
                'direktur', 'manager accounting', 'spv accounting', 'audit', 'manager audit',
                'emf', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc', 'admin pajak 2'
            ];
            $laporan_rekappersediaan = [
                'admin', 'kepala gudang',
                'admin gudang bahan', 'spv pdqc', 'direktur', 'manager accounting',
                'spv accounting', 'audit', 'manager audit', 'emf', 'admin pembelian', 'manager pembelian', 'admin pusat', 'admin pdqc', 'admin pajak 2'
            ];

            //Laporan Gudang Jadi
            $laporan_persediaan_gj = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pdqc'
            ];
            $rekap_persediaan_gj = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting',
                'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf',
                'admin pembelian', 'manager pembelian', 'admin pdqc'
            ];
            $rekap_hasiproduksi_gj = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pdqc'
            ];
            $rekap_pengeluaran_gj = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pdqc'
            ];
            $realisasi_kiriman_gj = [
                'admin', 'kepala gudang', 'direktur', 'manager accounting', 'spv accounting',
                'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pdqc'
            ];
            $realisasi_oman_gj = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pdqc'
            ];
            $laporan_angkutan = [
                'admin', 'kepala gudang', 'direktur',
                'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'admin gudang pusat', 'spv gudang pusat', 'emf', 'admin pdqc'
            ];

            //Laporan Gudang  Cabang

            $laporan_persediaan_bj = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut',
                'admin persediaan dan kasir', 'admin persediaan dan kas kecil',
                'supervisor sales', 'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'emf', 'admin pajak 2'
            ];
            $laporan_badstok_bj = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut',
                'admin persediaan dan kasir', 'admin persediaan dan kas kecil',
                'supervisor sales', 'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'emf', 'admin pajak 2'
            ];
            $laporan_rekap_bj = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'supervisor sales',
                'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'emf', 'admin pajak 2'
            ];
            $laporan_mutasidpb = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'supervisor sales',
                'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'emf', 'admin pajak 2'
            ];
            $laporan_rekonsiliasibj = [
                'admin', 'kepala penjualan',
                'kepala admin', 'admin pusat', 'admin gudang cabang', 'admin penjualan dan persediaan', 'manager accounting', 'admin garut', 'admin persediaan dan kasir',
                'admin persediaan dan kas kecil', 'supervisor sales',
                'admin gudang cabang dan marketing', 'direktur', 'manager accounting', 'spv accounting', 'manager marketing', 'rsm', 'general manager', 'audit', 'manager audit', 'emf', 'admin pajak 2'
            ];

            //Acounting
            $accounting_menu = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'manager ga', 'general affair', 'manager ga', 'manager hrd', 'kepala admin', 'kepala penjualan', 'rsm', 'manager marketing', 'manager audit', 'audit', 'admin pusat'];
            $lpc_menu = ['admin', 'spv accounting', 'manager accounting', 'kepala admin'];
            $coa_menu = ['admin', 'spv accounting', 'manager accounting'];
            $setcoacabang = ['admin', 'spv accounting', 'manager accounting'];
            $hpp_menu = ['admin', 'manager accounting', 'spv accounting'];
            $hpp_input = ['admin', 'manager accounting', 'spv accounting'];
            $hargaawal_input = ['admin', 'manager accounting', 'spv accounting'];
            $saldoawal_bukubesar_menu = ['admin', 'manager accounting', 'spv accounting'];
            $jurnalumum_menu = ['admin', 'manager accounting', 'spv accounting', 'manager ga', 'general affair', 'manager ga', 'manager hrd', 'admin pusat'];
            $costratio_menu = ['admin', 'manager accounting', 'spv accounting', 'kepala admin', 'kepala penjualan', 'admin pusat'];
            $laporan_accounting = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'manager ga', 'general affair', 'manager ga', 'manager hrd', 'kepala admin', 'kepala penjualan', 'admin pusat', 'rsm', 'manager marketing', 'manager audit', 'audit'];
            $laporan_lk = ['admin', 'direktur', 'manager accounting', 'spv accounting'];
            $laporan_rekapbj_acc = ['admin', 'direktur', 'manager accounting', 'spv accounting'];
            $laporan_bukubesar = ['admin', 'direktur', 'manager accounting', 'spv accounting'];
            $laporan_jurnalumum = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'manager ga', 'general affair', 'manager ga', 'manager hrd', 'admin pusat'];
            $laporan_costratio = ['admin', 'direktur', 'manager accounting', 'spv accounting', 'manager hrd', 'kepala admin', 'kepala penjualan', 'admin pusat', 'rsm', 'manager marketing'];


            //HRD
            $hrd_menu = ['admin', '3', '2', '11', '1', '7', '6', '4'];
            $penilaian_karyawan = ['admin', '3', '2', '11', '1', '7', '6', '4'];

            $kesepakatanbersama = ['admin', 'direktur', 'manager hrd'];
            $kontrak_menu = ['admin', 'direktur', 'manager hrd'];
            $pengajuan_izin_menu = ['admin', 'manager hrd', 'kepala admin', 'kepala penjualan', 'manager pembelian', 'kepala gudang', 'manager produksi', 'spv produksi', 'manager accounting', 'manager ga', 'emf', 'manager marketing', 'rsm', 'manager audit', 'direktur'];
            $jadwal_kerja_menu = ['admin', 'manager hrd', 'spv produksi', 'general affair', 'admin maintenance', 'spv maintenance', 'spv pdqc', 'spv gudang pusat'];
            $hari_libur_menu = ['admin', 'manager hrd'];
            $pembayaran_jmk = ['admin', 'manager hrd'];
            $pelanggaran_menu = ['admin', 'manager hrd'];
            $gaji_menu = ['admin', 'direktur', 'manager accounting', 'manager hrd'];
            $insentif_menu = ['admin', 'direktur', 'manager accounting', 'manager hrd'];
            $bpjs_menu = ['admin', 'direktur', 'manager accounting', 'manager hrd'];
            $monitoring_presensi = ['admin', 'manager hrd', 'manager audit'];
            $presensi_karyawan_menu = ['admin', 'manager hrd'];
            $lembur_menu = ['admin', 'manager hrd'];
            //General Affair
            $ga_menu = ['admin', 'manager ga', 'general affair', 'manager ga', 'direktur', 'manager accounting', 'emf'];
            $dashboard_ga = ['admin', 'manager ga', 'general affair', 'manager ga', 'direktur', 'manager accounting', 'emf'];
            $mutasi_kendaraan = ['admin', 'manager ga', 'general affair', 'manager ga', 'direktur', 'manager accounting', 'emf'];
            $service_kendaraan = ['admin', 'manager ga', 'general affair', 'manager ga', 'direktur', 'manager accounting', 'emf'];
            $bad_stock = ['admin', 'manager ga', 'general affair', 'manager ga', 'direktur', 'manager accounting', 'emf'];
            $laporan_ga = ['admin', 'manager ga', 'general affair', 'manager ga', 'direktur', 'manager accounting', 'emf'];
            $laporan_servicekendaraan = ['admin', 'manager ga', 'general affair', 'manager ga', 'direktur', 'manager accounting', 'emf'];
            $rekap_badstokga = ['admin', 'manager ga', 'general affair', 'manager ga', 'direktur', 'manager accounting', 'emf'];

            //Maintenance

            $maintenance_menu  = [
                'admin', 'admin maintenance', 'spv maintenance', 'spv accounting',
                'manager accounting', 'direktur', 'general manager', 'admin pusat'
            ];
            $maintenance_pembelian = ['admin', 'admin maintenance', 'spv maintenance'];
            $maintenance_pemasukan = ['admin', 'admin maintenance', 'spv maintenance'];

            $maintenance_pengeluaran = ['admin', 'admin maintenance', 'spv maintenance'];
            $laporan_maintenance = [
                'admin', 'admin maintenance', 'spv maintenance', 'spv accounting',
                'manager accounting', 'direktur', 'general manager', 'admin pusat'
            ];



            $tutuplaporan = ['admin', 'manager accounting'];
            $ticket_hapus = ['manager accounting'];
            $ticket_approve = ['admin', 'manager accounting'];
            $ticket_done = ['admin'];

            $lap_hrd = ['admin', 'manager hrd', 'kepala gudang', 'manager accounting'];
            $lap_gaji = ['admin', 'manager hrd', 'manager accounting', 'direktur'];

            $monitoring_sku = ['salesman'];

            $shareddata = [

                'operator_pusat' => $operator_pusat,

                'level' => $level,
                'kat_jabatan' => $kat_jabatan,
                'getcbg' => $getcbg,
                'memo_unread' => $memo_unread,
                'memo_data' => $memo_data,
                'memo_menu' => $memo_menu,
                'memo_tambah_hapus' => $memo_tambah_hapus,

                'ticket_pending' => $ticket_pending,
                'ticket_pending_approve' => $ticket_pending_approve,
                'ticket_pending_done' => $ticket_pending_done,

                'jmlpenilaiankar' => $jmlpenilaiankar,
                //Dashboard
                'dashboardadmin' => $dashboardadmin,
                'dashboardkepalapenjualan' => $dashboardkepalapenjualan,
                'dashboardkepalaadmin' => $dashboardkepalaadmin,
                'dashboardadminpenjualan' => $dashboardadminpenjualan,
                'dashboardaccounting' => $dashboardaccounting,
                'dashboardstaffkeuangan' => $dashboardstaffkeuangan,
                'dashboardadminkaskecil' => $dashboardadminkaskecil,
                'dashboardpembelian' => $dashboardpembelian,
                'dashboard_sfa' => $dashboard_sfa,
                //Data Master
                'datamaster_view' => $datamaster,

                'pasar_menu' => $pasar_menu,
                'pasar_tambah' => $pasar_tambah,
                'pasar_hapus' => $pasar_hapus,
                //Pelanggan
                'pelanggan_view' => $pelanggan,
                'pelanggan_tambah' => $pelanggan_tambah,
                'pelanggan_edit' => $pelanggan_edit,
                'pelanggan_hapus' => $pelanggan_hapus,
                'pelanggan_ajuanlimit' => $pelanggan_ajuanlimit,

                //Salesman
                'salesman_view' => $salesman,
                'salesman_tambah' => $salesman_tambah,
                'salesman_edit' => $salesman_edit,
                'salesman_hapus' => $salesman_hapus,

                //Karyawan
                'karyawan_view' => $karyawan_view,
                'karyawan_pinjaman' => $karyawan_pinjaman,
                'karyawan_tambah' => $karyawan_tambah,
                'karyawan_edit' => $karyawan_edit,
                'karyawan_hapus' => $karyawan_hapus,

                'supplier_view' => $supplier_menu,
                'supplier_tambah' => $supplier_tambah,
                'supplier_edit' => $supplier_edit,
                'supplier_hapus' => $supplier_hapus,

                //Barang Produk
                'barang_view' => $barang,
                'barang_tambah' => $barang_tambah,
                'barang_edit' => $barang_edit,
                'barang_hapus' => $barang_hapus,

                //Barang Pembelian
                'barangpembelian' => $barangpembelian,
                'barangpembelian_tambah' => $barangpembelian_tambah,
                'barangpembelian_edit' => $barangpembelian_edit,
                'barangpembelian_hapus' => $barangpembelian_hapus,
                //Harga Edit
                'harga_view' => $harga,
                'harga_hapus' => $harga_hapus,
                'harga_tambah' => $harga_tambah,
                'harga_edit' => $harga_edit,

                'kendaraan_view' => $kendaraan,
                'kendaraan_tambah' => $kendaraan_tambah,
                'kendaraan_edit' => $kendaraan_edit,
                'kendaraan_hapus' => $kendaraan_hapus,

                'cabang_view' => $cabang,

                //Data Marketing
                'marketing' => $marketing,
                //-----------OMAN------------------------
                'oman' => $oman,
                'omancabang' => $omancabang,
                'omanmarketing' => $omanmarketing,

                //------------Permintaan Pengiriman------
                'permintaanpengiriman' => $permintaanpengiriman,
                'permintaanpengiriman_tambah' => $permintaanpengiriman_tambah,
                'permintaanpengiriman_hapus' => $permintaanpengiriman_hapus,
                'permintaanpengiriman_proses' => $permintaanpengiriman_proses,
                'permintaanpengiriman_gj' => $permintaanpengiriman_gj,
                //------------Komisi------
                'komisi' => $komisi,
                'targetkomisi' => $targetkomisi,
                'targetkomisiinput' => $targetkomisiinput,
                'generatecashin' => $generatecashin,
                'ratiokomisi' => $ratiokomisi,
                'laporan_komisi' => $laporan_komisi,
                'inputpotongankomisi' => $inputpotongankomisi,
                //------------Penjualan-------------------
                'penjualan_menu' => $penjualan_menu,
                'penjualan_keuangan' => $penjualan_keuangan,
                'penjualan_input' => $penjualan_input,
                'penjualan_view' => $penjualan_view,
                'penjualan_hapus' => $penjualan_hapus,
                'penjualan_edit' => $penjualan_edit,
                //Retur
                'retur_view' => $retur_view,
                'lhp_menu' => $lhp_menu,
                //Limit Kredit
                'limitkredit_view' => $limitkredit_view,
                'limitkredit_hapus' => $limitkredit_hapus,
                'limitkredit_analisa' => $limitkredit_analisa,
                'penyesuaian_limit' => $penyesuaian_limit,
                //Laporan
                'laporan_penjualan' => $laporan_penjualan,
                'harga_net' => $harga_net,
                //--------------Keuangan--------------
                'keuangan' => $keuangan,
                'penjualan_keuangan' => $penjualan_keuangan,
                'laporankeuangan_view' => $laporankeuangan_view,
                'laporan_ledger' => $laporan_ledger,
                'laporan_kaskecil' => $laporan_kaskecil,
                'laporan_saldokasbesar' => $laporan_saldokasbesar,
                'laporan_lpu' => $laporan_lpu,
                'laporan_penjualan_keuangan' => $laporan_penjualan_keuangan,
                'laporan_uanglogam' => $laporan_uanglogam,
                'laporan_rekapbg' => $laporan_rekapbg,

                //Giro
                'giro_view' => $giro_view,
                'giro_approved' => $giro_approved,
                'giro_hapus' => $giro_hapus,
                //Transfer
                'transfer_view' => $transfer_view,
                'transfer_approved' => $transfer_approved,

                //Kas Kecil
                'kaskecil_menu' => $kaskecil_menu,
                'kaskecil_view' => $kaskecil_view,
                'klaim_view' => $klaim_view,
                'klaim_add' => $klaim_add,
                'klaim_hapus' => $klaim_hapus,
                'klaim_validasi' => $klaim_validasi,
                'klaim_proses' => $klaim_proses,

                //Mutasi Bank
                'mutasibank_view' => $mutasibank_view,

                //Pinjaman
                'pinjaman_view' => $pinjaman_view,
                'pembayaranpinjaman_view' => $pembayaranpinjaman_view,
                'inputbayarpinjaman' => $inputbayarpinjaman,
                'lap_pinjaman' => $lap_pinjaman,
                'lap_kasbon' => $lap_kasbon,


                'piutangkaryawan_view' => $piutangkaryawan_view,
                'pembayaranpiutangkaryawan_view' => $pembayaranpiutangkaryawan_view,
                'inputbayarpiutangkaryawan' => $inputbayarpiutangkaryawan,


                'kasbon_view' => $kasbon_view,
                'pembayarankasbon_view' => $pembayarankasbon_view,

                //ledger
                'ledger_menu' => $ledger_menu,
                'ledger_view' => $ledger_view,
                'ledger_saldoawal' => $ledger_saldoawal,

                //Kas Besar Keuangan
                'kasbesar_menu' => $kasbesar_menu,
                'saldoawalkasbesar_view' => $saldoawalkasbesar_view,
                //Setoran
                'setoran_menu' => $setoran_menu,
                'setoranpenjualan_view' => $setoranpenjualan_view,
                'setoranpusat_view' => $setoranpusat_view,
                'setoranpusat_add' => $setoranpusat_add,
                'setoranpusat_edit' => $setoranpusat_edit,
                'setoranpusat_hapus' => $setoranpusat_hapus,
                'setorangiro_view' => $setorangiro_view,
                'setorantransfer_view' => $setorantransfer_view,
                'belum_disetorkan' => $belum_disetorkan,
                'lebih_disetorkan' => $lebih_disetorkan,
                'gantilogamtokertas' => $gantilogamtokertas,
                'setoranpusat_terimasetoran' => $setoranpusat_terimasetoran,


                //Utilities
                'saldoawalpiutang' => $saldoawalpiutang,
                'datausers' => $datausers,

                'kirimlpc' => $kirimlpc,
                'kirimlpc_tambah' => $kirimlpc_tambah,
                'kirimlpc_edit' => $kirimlpc_edit,
                'kirimlpc_hapus' => $kirimlpc_hapus,
                'kirimlpc_approve' => $kirimlpc_approve,

                'pembelian_menu' => $pembelian_menu,
                'pembelian_view' => $pembelian_view,
                'pembelian_hapus' => $pembelian_hapus,
                'pembelian_edit' => $pembelian_edit,
                'pembelian_tambah' => $pembelian_tambah,
                'pembelian_keuangan' => $pembelian_keuangan,
                'kontrabon_view' => $kontrabon_view,
                'kontrabon_edit_hapus' => $kontrabon_edit_hapus,
                'kontrabon_proses' => $kontrabon_proses,
                'kontrabon_approve' => $kontrabon_approve,
                'jatuhtempo_view' => $jatuhtempo_view,
                'jurnalkoreksi_view' => $jurnalkoreksi_view,
                'laporan_pembelian' => $laporan_pembelian,
                'laporan_pembayaran_pembelian' => $laporan_pembayaran_pembelian,
                'laporan_rekappembeliansupplier' => $laporan_rekappembeliansupplier,
                'laporan_rekappembelian' => $laporan_rekappembelian,
                'laporan_kartuhutang' => $laporan_kartuhutang,
                'laporan_auh' => $laporan_auh,
                'laporan_bahankemasan' => $laporan_bahankemasan,
                'laporan_rekapbahankemasan' => $laporan_rekapbahankemasan,
                'laporan_jurnalkoreksi' => $laporan_jurnalkoreksi,
                'laporan_rekapakunpembelian' => $laporan_rekapakunpembelian,
                'laporan_rekapkontrabon' => $laporan_rekapkontrabon,

                //Produksi

                'produksi_menu' => $produksi_menu,
                'produksi_analytics' => $produksi_analytics,
                'mutasi_produk' => $mutasi_produk,
                'bpbj_view' => $bpbj_view,
                'fsthp_view' => $fsthp_view,
                'mutasi_barang' => $mutasi_barang,
                'pemasukan_produksi' => $pemasukan_produksi,
                'pengeluaran_produksi' => $pengeluaran_produksi,
                'saldoawal_mutasibarang_produksi' => $saldoawal_mutasibarang_produksi,
                'opname_mutasibarang_produksi' => $opname_mutasibarang_produksi,
                'laporan_produksi' => $laporan_produksi,
                'laporan_mutasiproduksi' => $laporan_mutasiproduksi,
                'laporan_rekapmutasiproduksi' => $laporan_rekapmutasiproduksi,
                'laporan_pemasukanproduksi' => $laporan_pemasukanproduksi,
                'laporan_pengeluaranproduksi' => $laporan_pengeluaranproduksi,
                'laporan_rekappersediaanbarangproduksi' => $laporan_rekappersediaanbarangproduksi,

                //Gudang

                'gudang_menu' => $gudang_menu,
                'gudang_bahan_menu' => $gudang_bahan_menu,
                'gudang_logistik_menu' => $gudang_logistik_menu,
                'gudang_jadi_menu' => $gudang_jadi_menu,
                'gudang_cabang_menu' => $gudang_cabang_menu,

                'laporan_gudang_logistik' => $laporan_gudang_logistik,
                'laporan_gudang_bahan' => $laporan_gudang_bahan,
                'laporan_gudang_jadi' => $laporan_gudang_jadi,
                'laporan_gudang_cabang' => $laporan_gudang_cabang,


                //Gudang Logistik
                'pemasukan_gudanglogistik' => $pemasukan_gudanglogisitik,
                'approve_pembelian' => $approve_pembelian,
                'pengeluaran_gudanglogistik' => $pengeluaran_gudanglogistik,
                'saldoawal_gudanglogistik' => $saldoawal_gudanglogistik,
                'opname_gudanglogistik' => $opname_gudanglogistik,


                //Gudang Bahan
                'pemasukan_gudangbahan' => $pemasukan_gudangbahan,
                'pengeluaran_gudangbahan' => $pengeluaran_gudangbahan,
                'saldoawal_gudangbahan' => $saldoawal_gudangbahan,
                'opname_gudangbahan' => $opname_gudangbahan,

                //Gudang Jadi
                'permintaan_produksi_view' => $permintaan_produksi_view,
                'mutasi_produk_gj' => $mutasi_produk_gj,
                'fsthp_gj_view' => $fsthp_gj_view,
                'suratjalan_view' => $suratjalan_view,
                'suratjalan_cetak' => $suratjalan_cetak,
                'suratjalan_hapus' => $suratjalan_hapus,
                'repackgj_view' => $repackgj_view,
                'rejectgj_view' => $rejectgj_view,
                'lainnyagj_view' => $lainnyagj_view,
                'angkutan_view' => $angkutan_view,
                //Kontrabon Angkutan
                'gudang_jadi_keuangan' => $gudang_jadi_keuangan,
                'kontrabon_angkutan_view' => $kontrabon_angkutan_view,
                'kontrabon_angkutan_hapus' => $kontrabon_angkutan_hapus,
                //Gudang Cabang
                'saldoawal_gs_view' => $saldoawal_gs_view,
                'saldoawal_bs_view' => $saldoawal_bs_view,
                'dpb_view' => $dpb_view,
                'fpb_menu' => $fpb_menu,
                'suratjalancab_view' => $suratjalancab_view,
                'mutasi_barang_cab_view' => $mutasi_barang_cab_view,

                //Laporan Gudang Logistik
                'laporan_gudang' => $laporan_gudang,
                'laporan_pemasukan_gl' => $laporan_pemasukan_gl,
                'laporan_pengeluaran_gl' => $laporan_pengeluaran_gl,
                'laporan_persediaan_gl' => $laporan_persediaan_gl,
                'laporan_persediaanopname_gl' => $laporan_persediaanopname_gl,

                //Laporan Gudang Bahan
                'laporan_pemasukan_gb' => $laporan_pemasukan_gb,
                'laporan_pengeluaran_gb' => $laporan_pengeluaran_gb,
                'laporan_persediaan_gb' => $laporan_persediaan_gb,
                'laporan_kartugudang' => $laporan_kartugudang,
                'laporan_rekappersediaan' => $laporan_rekappersediaan,

                //Laporan Gudang Jadi
                'laporan_persediaan_gj' => $laporan_persediaan_gj,
                'rekap_persediaan_gj' => $rekap_persediaan_gj,
                'rekap_hasiproduksi_gj' => $rekap_hasiproduksi_gj,
                'rekap_pengeluaran_gj' => $rekap_pengeluaran_gj,
                'realisasi_kiriman_gj' => $realisasi_kiriman_gj,
                'realisasi_oman_gj' => $realisasi_oman_gj,
                'laporan_angkutan' => $laporan_angkutan,

                //Laporan Gudang Jadi
                'laporan_persediaan_gj' => $laporan_persediaan_gj,
                'rekap_persediaan_gj' => $rekap_persediaan_gj,
                'rekap_hasiproduksi_gj' => $rekap_hasiproduksi_gj,
                'rekap_pengeluaran_gj' => $rekap_pengeluaran_gj,
                'realisasi_kiriman_gj' => $realisasi_kiriman_gj,
                'realisasi_oman_gj' => $realisasi_oman_gj,
                'laporan_angkutan' => $laporan_angkutan,

                'laporan_persediaan_bj' => $laporan_persediaan_bj,
                'laporan_badstok_bj' => $laporan_badstok_bj,
                'laporan_rekap_bj' => $laporan_rekap_bj,
                'laporan_mutasidpb' => $laporan_mutasidpb,
                'laporan_rekonsiliasibj' => $laporan_rekonsiliasibj,

                //Acounting
                'accounting_menu' => $accounting_menu,
                'lpc_menu' => $lpc_menu,
                'coa_menu' => $coa_menu,
                'setcoacabang' => $setcoacabang,
                'hpp_menu' => $hpp_menu,
                'hpp_input' => $hpp_input,
                'hargaawal_input' => $hargaawal_input,
                'saldoawal_bukubesar_menu' => $saldoawal_bukubesar_menu,
                'jurnalumum_menu' => $jurnalumum_menu,
                'costratio_menu' => $costratio_menu,
                'laporan_accounting' => $laporan_accounting,
                'laporan_lk' => $laporan_lk,
                'laporan_rekapbj_acc' => $laporan_rekapbj_acc,
                'laporan_bukubesar' => $laporan_bukubesar,
                'laporan_jurnalumum' => $laporan_jurnalumum,
                'laporan_costratio' => $laporan_costratio,

                //HRD

                'hrd_menu' => $hrd_menu,
                'penilaian_karyawan' => $penilaian_karyawan,
                'kesepakatanbersama' => $kesepakatanbersama,
                'kontrak_menu' => $kontrak_menu,
                'pengajuan_izin_menu' => $pengajuan_izin_menu,
                'jadwal_kerja_menu' => $jadwal_kerja_menu,
                'lembur_menu' => $lembur_menu,
                'hari_libur_menu' => $hari_libur_menu,
                'pembayaran_jmk' => $pembayaran_jmk,
                'pelanggaran_menu' => $pelanggaran_menu,
                'monitoring_presensi' => $monitoring_presensi,
                'presensi_karyawan_menu' => $presensi_karyawan_menu,

                'gaji_menu' => $gaji_menu,
                'gaji_tambah' => $gaji_tambah,
                'gaji_edit' => $gaji_edit,
                'gaji_hapus' => $gaji_hapus,

                'insentif_menu' => $insentif_menu,
                'bpjs_menu' => $bpjs_menu,
                'insentif_tambah' => $insentif_tambah,
                'insentif_edit' => $insentif_edit,
                'insentif_hapus' => $insentif_hapus,

                //GA
                'ga_menu' => $ga_menu,
                'dashboard_ga' => $dashboard_ga,
                'mutasi_kendaraan' => $mutasi_kendaraan,
                'service_kendaraan' => $service_kendaraan,
                'bad_stock' => $bad_stock,
                'laporan_ga' => $laporan_ga,
                'laporan_servicekendaraan' => $laporan_servicekendaraan,
                'rekap_badstokga' => $rekap_badstokga,

                'maintenance_menu' => $maintenance_menu,
                'maintenance_pembelian' => $maintenance_pembelian,
                'maintenance_pemasukan' => $maintenance_pemasukan,
                'maintenance_pengeluaran' => $maintenance_pengeluaran,
                'laporan_maintenance' => $laporan_maintenance,

                'tutuplaporan' => $tutuplaporan,

                'ticket_hapus' => $ticket_hapus,
                'ticket_approve' => $ticket_approve,
                'ticket_done' => $ticket_done,

                'users' => $users,
                'tracking_salesman' => $tracking_salesman,
                'map_pelanggan' => $map_pelanggan,
                'scan' => $scan,
                'pajak' => $pajak,
                'cabangpkp' => $cabangpkp,
                'pi' => $pi,
                'lap_hrd' => $lap_hrd,
                'lap_gaji' => $lap_gaji,
                'monitoring_sku' => $monitoring_sku

            ];
            View::share($shareddata);
        });
    }
}
