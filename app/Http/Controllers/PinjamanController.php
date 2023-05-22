<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\View;
use PDO;

class PinjamanController extends Controller
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
        $show_for_hrd = config('global.show_for_hrd');
        $level_show_all = config('global.show_all');
        $query = Pinjaman::query();
        $query->select('pinjaman.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'totalpembayaran', 'tgl_ledger');
        $query->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman,SUM(jumlah) as totalpembayaran FROM pinjaman_historibayar GROUP BY no_pinjaman
        ) hb"),
            function ($join) {
                $join->on('pinjaman.no_pinjaman', '=', 'hb.no_pinjaman');
            }
        );
        $query->leftJoin('ledger_bank', 'pinjaman.no_pinjaman', '=', 'ledger_bank.no_ref');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_pinjaman', [$request->dari, $request->sampai]);
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

        if ($request->status === "1" || $request->status === 0) {
            $query->where('pinjaman.status', $request->status);
        }

        if ($level == "kepala admin") {
            $query->where('master_karyawan.id_kantor', $cabang);
            $query->where('master_karyawan.id_perusahaan', "MP");
            $query->where('nama_jabatan', '!=', 'KEPALA ADMIN');
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
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'SUPERVISOR']);
        }

        if ($level == "manager produksi") {
            $query->whereIn('master_karyawan.kode_dept', ['PRD', 'MTC']);
            $query->where('nama_jabatan', '!=', 'MANAGER');
        }

        if ($level == "manager ga") {
            $query->where('master_karyawan.kode_dept', 'GAF');
        }

        if ($level == "emf") {
            $query->whereIn('master_karyawan.kode_dept', ['PMB', 'PRD', 'GAF', 'GDG', 'HRD', 'PDQ']);
        }


        if ($level == "manager marketing") {
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'REGIONAL SALES MANAGER');
        }

        if ($level == "rsm") {
            $list_wilayah = Auth::user()->wilayah != null ? unserialize(Auth::user()->wilayah) : NULL;
            $wilayah = $list_wilayah != null ? "'" . implode("', '", $list_wilayah) . "'" : '';
            $query->whereIn('master_karyawan.id_kantor', $list_wilayah);
            $query->where('master_karyawan.kode_dept', 'MKT');
            $query->where('nama_jabatan', 'KEPALA PENJUALAN');
            $query->where('id_perusahaan', 'PCF');
        }

        if (!in_array($level, $level_show_all)) {
            $query->whereNotIn('id_jabatan', $show_for_hrd);
        }

        $query->orderBy('no_pinjaman', 'desc');
        $pinjaman = $query->paginate(15);
        $pinjaman->appends($request->all());


        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);

        $departemen = DB::table('hrd_departemen')->get();
        $hakakses = config('global.pinjamanpage');
        if (in_array($level, $hakakses)) {
            return view('pinjaman.index', compact('pinjaman', 'cabang', 'departemen'));
        } else {
            echo "Anda Tidak Memiliki Hak Akses";
        }
    }
    public function create($nik)
    {
        $nik = Crypt::decrypt($nik);
        $query = Karyawan::query();
        $query->select('nik', 'nama_karyawan', 'tgl_masuk', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan', 'nama_cabang');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftjoin('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang');
        $query->where('nik', $nik);
        $karyawan = $query->first();

        $gaji = DB::table('hrd_mastergaji')
            ->selectRaw('IFNULL(gaji_pokok,0)+IFNULL(t_jabatan,0)+IFNULL(t_masakerja,0)+IFNULL(t_tanggungjawab,0)+IFNULL(t_makan,0)+IFNULL(t_istri,0)+IFNULL(t_skill,0) as gajitunjangan,gaji_pokok')
            ->where('nik', $nik)->orderBy('tgl_berlaku', 'desc')->first();

        $jmk = DB::table('hrd_bayarjmk')
            ->selectRaw('SUM(jumlah) as jml_jmk')
            ->where('nik', $nik)
            ->groupBy('nik')
            ->first();
        $hariini = date("Y-m-d");
        $sp = DB::table('hrd_sp')->where('nik', $nik)->where('sampai', '>', $hariini)
            ->orderBy('dari', 'desc')
            ->first();



        $query = Pinjaman::query();
        $query->select('pinjaman.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'totalpembayaran');
        $query->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin(
            DB::raw("(
            SELECT no_pinjaman,SUM(jumlah) as totalpembayaran FROM pinjaman_historibayar GROUP BY no_pinjaman
        ) hb"),
            function ($join) {
                $join->on('pinjaman.no_pinjaman', '=', 'hb.no_pinjaman');
            }
        );
        $query->where('pinjaman.nik', $nik);
        $query->whereRaw('IFNULL(jumlah_pinjaman,0) - IFNULL(totalpembayaran,0) != 0');
        $cekpinjaman = $query->first();

        $kontrak = DB::table('hrd_kontrak')->where('nik', $nik)->orderBy('dari', 'desc')->first();

        $start = date_create($karyawan->tgl_masuk);
        $end = date_create($hariini);

        $cekmasakerja =  diffInMonths($start, $end);

        $jenis_sp = $sp != null ? $sp->ket : '';
        $id_kantor = $karyawan->id_kantor;

        //echo $jenis_sp . "-" . $id_kantor;
        if (
            $sp != null && $jenis_sp == "SP3" && $id_kantor == "PST"
            || $sp != null && $jenis_sp == "SP2" && $id_kantor == "PST"
            || $sp != null && $jenis_sp == "SP1" && $id_kantor != "PST"
        ) {
            return view('pinjaman.notifsp', compact('sp'));
        } else if ($karyawan->status_karyawan == "K" && $cekmasakerja < 15) {
            return view('pinjaman.notifmasakerjakurang', compact('cekmasakerja'));
        } else {
            if ($cekpinjaman != null) {
                $jumlah_pinjaman = $cekpinjaman->jumlah_pinjaman;
                $minpembayar = (75 / 100) * $jumlah_pinjaman;
                if ($cekpinjaman->totalpembayaran >= $minpembayar) {
                    return view('pinjaman.create', compact('karyawan', 'gaji', 'jmk', 'kontrak'));
                } else {
                    return view('pinjaman.notiftopup', compact('cekpinjaman'));
                }
            } else {
                return view('pinjaman.create', compact('karyawan', 'gaji', 'jmk', 'kontrak'));
                // return view('pinjaman.create2', compact('karyawan', 'gaji', 'jmk', 'kontrak'));
            }
        }
        //return view('pinjaman.create2', compact('karyawan', 'gaji', 'jmk', 'kontrak'));
    }

    public function store(Request $request)
    {
        $tgl_pinjaman = $request->tgl_pinjaman;
        $nik  = $request->nik;
        $status_karyawan = $request->status_karyawan;
        $akhir_kontrak = $request->akhir_kontrak;
        $gapok_tunjangan = str_replace(".", "", $request->gapok_tunjangan);
        $tenor_max = $request->tenor_max;
        $angsuran_max = str_replace(".", "", $request->angsuran_max);
        $jmk = str_replace(".", "", $request->jmk);
        $jmk_sudahbayar = str_replace(".", "", $request->jmk_sudahbayar);
        $plafon_max = str_replace(".", "", $request->plafon_max);
        $jumlah_pinjaman = str_replace(".", "", $request->jml_pinjaman);
        $angsuran = $request->angsuran;
        $jumlah_angsuran = str_replace(".", "", $request->jml_angsuran);
        $mulai_cicilan = $request->mulai_cicilan;
        $bln_cicilan = date("m", strtotime($mulai_cicilan));
        $thn_cicilan = date("Y", strtotime($mulai_cicilan));
        $karyawan = DB::table('master_karyawan')->where('nik', $nik)
            ->leftJoin('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->first();


        if ($bln_cicilan == 1) {
            $bln_cicilan = 12;
            $thn_cicilan = $thn_cicilan - 1;
        } else {
            $bln_cicilan = $bln_cicilan - 1;
            $thn_cicilan = $thn_cicilan;
        }
        $tanggal = $request->tgl_pinjaman;
        $tgl = explode("-", $tanggal);
        $tahun = substr($tgl[0], 2, 2);
        $pinjaman = DB::table("pinjaman")
            ->whereRaw('YEAR(tgl_pinjaman)="' . $tgl[0] . '"')
            ->orderBy("no_pinjaman", "desc")
            ->first();
        $last_nopinjaman = $pinjaman != null ? $pinjaman->no_pinjaman : '';
        $no_pinjaman  = buatkode($last_nopinjaman, "PJK" . $tahun, 3);
        $cicilan_terakhir = $jumlah_angsuran + ($jumlah_pinjaman - ($jumlah_angsuran * $angsuran));
        $cekpembayaran = DB::table('pinjaman_potongangaji')->where('bulan', $bln_cicilan)->where('tahun', $thn_cicilan)->count();
        if ($cekpembayaran > 0) {
            return Redirect::back()->with(['warning' => 'Pinjaman Pada Periode Ini Sudah Ditutup']);
        }
        $data = [
            'no_pinjaman' => $no_pinjaman,
            'tgl_pinjaman' => $tgl_pinjaman,
            'nik' => $nik,
            'status_karyawan' => $status_karyawan,
            'akhir_kontrak' => $akhir_kontrak,
            'gapok_tunjangan' => $gapok_tunjangan,
            'tenor_max' => $tenor_max,
            'angsuran_max' => $angsuran_max,
            'jmk' => $jmk,
            'jmk_sudahbayar' => $jmk_sudahbayar,
            'plafon_max' => $plafon_max,
            'jumlah_pinjaman' => $jumlah_pinjaman,
            'angsuran' => $angsuran,
            'jumlah_angsuran' => $jumlah_angsuran,
            'mulai_cicilan' => $mulai_cicilan,
            'id_user' => Auth::user()->id
        ];
        $tgl_cicilan = explode("-", $mulai_cicilan);
        $bln = $tgl_cicilan[1];
        DB::beginTransaction();
        try {
            DB::table('pinjaman')->insert($data);
            for ($i = 1; $i <= $angsuran; $i++) {
                if ($bln > 12) {
                    $blncicilan = $bln - 12;
                    $tahun = $tgl_cicilan[0] + 1;
                } else {
                    $blncicilan = $bln;
                    $tahun = $tgl_cicilan[0];
                }

                if ($i == $angsuran) {
                    $cicilan = $cicilan_terakhir;
                } else {
                    $cicilan = $jumlah_angsuran;
                }

                DB::table('pinjaman_rencanabayar')
                    ->insert([
                        'no_pinjaman' => $no_pinjaman,
                        'cicilan_ke' => $i,
                        'bulan' => $blncicilan,
                        'tahun' => $tahun,
                        'jumlah' => $cicilan
                    ]);

                $bln++;
            }
            //087708590299 Ika
            //0811211451 Ardi

            $notmanagement = config('global.show_for_hrd');
            $data = [
                'api_key' => 'NHoqE4TUf6YLQhJJQAGSUjj4wOMyzh',
                'sender' => '6289670444321',
                'number' => '082218771107',
                'message' => '*' . $nik . "-" . $karyawan->nama_karyawan . '*, dari Departemen ' . $karyawan->nama_dept . ' Mengajukan Pinjaman dengan Nomor Pinjaman *' . $no_pinjaman . '* dan total pinjaman *' . $request->jml_pinjaman . '* Menunggu untuk Segera di proses.'
            ];

            $ardi = [
                'api_key' => 'NHoqE4TUf6YLQhJJQAGSUjj4wOMyzh',
                'sender' => '6289670444321',
                'number' => '082218770017',
                'message' => '*' . $nik . "-" . $karyawan->nama_karyawan . '*, dari Departemen ' . $karyawan->nama_dept . ' Mengajukan Pinjaman dengan Nomor Pinjaman *' . $no_pinjaman . '* dan total pinjaman *' . $request->jml_pinjaman . '* Menunggu untuk Segera di proses.'
            ];

            if (in_array($karyawan->id_jabatan, $notmanagement)) {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://wa.pedasalami.com/send-message',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
            }

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://wa.pedasalami.com/send-message',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($ardi),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            //echo $response;
            DB::commit();
            return redirect('/pinjaman')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            //return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
            dd($e);
        }
    }


    public function edit(Request $request)
    {
        $no_pinjaman = $request->no_pinjaman;
        $pinjaman = DB::table('pinjaman')
            ->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('no_pinjaman', $no_pinjaman)->first();


        $hariini = date("Y-m-d");

        return view('pinjaman.edit', compact('pinjaman'));
    }

    public function update($no_pinjaman, Request $request)
    {
        $no_pinjaman = Crypt::decrypt($no_pinjaman);
        $tgl_pinjaman = $request->tgl_pinjaman;
        $jumlah_pinjaman = str_replace(".", "", $request->jml_pinjaman);
        $angsuran = $request->angsuran;
        $jumlah_angsuran = str_replace(".", "", $request->jml_angsuran);
        $mulai_cicilan = $request->mulai_cicilan;

        $cicilan_terakhir = $jumlah_angsuran + ($jumlah_pinjaman - ($jumlah_angsuran * $angsuran));

        $data = [
            'tgl_pinjaman' => $tgl_pinjaman,
            'jumlah_pinjaman' => $jumlah_pinjaman,
            'angsuran' => $angsuran,
            'jumlah_angsuran' => $jumlah_angsuran,
            'mulai_cicilan' => $mulai_cicilan
        ];
        $tgl_cicilan = explode("-", $mulai_cicilan);
        $bln = $tgl_cicilan[1];
        DB::beginTransaction();
        try {
            DB::table('pinjaman')
                ->where('no_pinjaman', $no_pinjaman)->update($data);
            DB::table('pinjaman_rencanabayar')->where('no_pinjaman', $no_pinjaman)->delete();
            for ($i = 1; $i <= $angsuran; $i++) {
                if ($bln > 12) {
                    $bln = 1;
                    $tahun = $tgl_cicilan[0] + 1;
                } else {
                    $bln = $bln;
                    $tahun = $tgl_cicilan[0];
                }

                if ($i == $angsuran) {
                    $cicilan = $cicilan_terakhir;
                } else {
                    $cicilan = $jumlah_angsuran;
                }

                DB::table('pinjaman_rencanabayar')
                    ->insert([
                        'no_pinjaman' => $no_pinjaman,
                        'cicilan_ke' => $i,
                        'bulan' => $bln,
                        'tahun' => $tahun,
                        'jumlah' => $cicilan
                    ]);

                $bln++;
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
            //dd($e);
        }
    }

    public function delete($no_pinjaman)
    {
        $no_pinjaman = Crypt::decrypt($no_pinjaman);
        DB::beginTransaction();
        try {
            DB::table('pinjaman')->where('no_pinjaman', $no_pinjaman)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    public function show(Request $request)
    {
        $no_pinjaman = $request->no_pinjaman;
        $pinjaman = DB::table('pinjaman')
            ->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('pinjaman.no_pinjaman', $no_pinjaman)->first();


        $hariini = date("Y-m-d");

        return view('pinjaman.show', compact('pinjaman'));
    }

    public function getrencanabayar(Request $request)
    {
        $no_pinjaman = $request->no_pinjaman;
        $rencana = DB::table('pinjaman_rencanabayar')->where('no_pinjaman', $no_pinjaman)->get();
        return view('pinjaman.getrencanabayar', compact('rencana'));
    }

    public function gethistoribayar(Request $request)
    {
        $no_pinjaman = $request->no_pinjaman;
        $histori = DB::table('pinjaman_historibayar')
            ->join('users', 'pinjaman_historibayar.id_user', '=', 'users.id')
            ->where('no_pinjaman', $no_pinjaman)
            ->orderBy('tgl_bayar', 'desc')
            ->get();
        return view('pinjaman.gethistoribayar', compact('histori', 'no_pinjaman'));
    }

    public function approve($no_pinjaman)
    {
        $no_pinjaman = Crypt::decrypt($no_pinjaman);
        $pinjaman = DB::table('pinjaman')
            ->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('no_pinjaman', $no_pinjaman)->first();
        try {
            DB::table('pinjaman')->where('no_pinjaman', $no_pinjaman)->update([
                'status' => 1
            ]);
            $data = [
                'api_key' => 'NHoqE4TUf6YLQhJJQAGSUjj4wOMyzh',
                'sender' => '6289670444321',
                'number' => '082218770017',
                'message' => '*' . $pinjaman->nama_karyawan . '*, Ajuan Pinjaman dengan Nomor Pinjaman *' . $pinjaman->no_pinjaman . '* dengan total pinjaman *' . rupiah($pinjaman->jumlah_pinjaman) . '* sudah di proses oleh bagian keuangan, silahkan tunggu 1 x 24 jam untuk proses pencairan dana ke rekening.'
            ];
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://wa.pedasalami.com/send-message',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            echo $response;

            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }

    public function decline($no_pinjaman)
    {
        $no_pinjaman = Crypt::decrypt($no_pinjaman);
        try {
            DB::table('pinjaman')->where('no_pinjaman', $no_pinjaman)->update([
                'status' => 0
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Di Update']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }



    public function cetakformulir($no_pinjaman)
    {
        $no_pinjaman = Crypt::decrypt($no_pinjaman);
        $pinjaman = DB::table('pinjaman')
            ->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('pinjaman.no_pinjaman', $no_pinjaman)->first();


        $hariini = date("Y-m-d");

        return view('pinjaman.cetakformulir', compact('pinjaman'));
    }


    public function prosespinjaman(Request $request)
    {
        $no_pinjaman = $request->no_pinjaman;
        $pinjaman = DB::table('pinjaman')
            ->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('no_pinjaman', $no_pinjaman)->first();

        $bank = DB::table('master_bank')->where('kode_cabang', 'PST')->get();
        $hariini = date("Y-m-d");

        return view('pinjaman.proses', compact('pinjaman', 'bank'));
    }

    public function storeprosespinjaman($no_pinjaman, Request $request)
    {
        $no_pinjaman = Crypt::decrypt($no_pinjaman);
        $pinjaman = DB::table('pinjaman')
            ->join('master_karyawan', 'pinjaman.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('no_pinjaman', $no_pinjaman)->first();
        $status = $request->statusaksi;
        $tgl_transfer = $request->tgl_transfer;
        $tanggal = explode("-", $tgl_transfer);
        $tahun = substr($tanggal[0], 2, 2);
        $cabang = "PST";
        $nama_karyawan = $pinjaman->nama_karyawan;
        $bank = $request->bank;
        $jumlah = $pinjaman->jumlah_pinjaman;
        $no_hp = $pinjaman->no_hp;
        //dd($no_hp);
        DB::beginTransaction();
        try {
            if ($status == 1) {
                DB::table('pinjaman')->where('no_pinjaman', $no_pinjaman)->update([
                    'status' => 1
                ]);

                DB::table('ledger_bank')->where('no_ref', $no_pinjaman)->delete();
                $lastledger = DB::table('ledger_bank')
                    ->select('no_bukti')
                    ->whereRaw('LEFT(no_bukti,7) ="LR' . $cabang . $tahun . '"')
                    ->orderBy('no_bukti', 'desc')
                    ->first();
                if ($lastledger == null) {
                    $last_no_bukti = 'LR' . $cabang . $tahun . '0000';
                } else {
                    $last_no_bukti = $lastledger->no_bukti;
                }
                $no_bukti = buatkode($last_no_bukti, 'LR' . $cabang . $tahun, 4);

                DB::table('ledger_bank')
                    ->insert([
                        'no_bukti'        => $no_bukti,
                        'no_ref'          => $no_pinjaman,
                        'bank'            => $bank,
                        'tgl_ledger'      => $tgl_transfer,
                        'pelanggan'       => $nama_karyawan,
                        'keterangan'      => "Piutang Karyawan " . $nama_karyawan,
                        'kode_akun'       => '1-1451',
                        'jumlah'          => $jumlah,
                        'status_dk'       => 'D',
                        'status_validasi' => 1,
                    ]);
                if (!empty($no_hp)) {
                    $data = [
                        'api_key' => 'NHoqE4TUf6YLQhJJQAGSUjj4wOMyzh',
                        'sender' => '6289670444321',
                        'number' => $no_hp,
                        'message' => '*' . $pinjaman->nama_karyawan . '*, Ajuan Pinjaman dengan Nomor Pinjaman *' . $pinjaman->no_pinjaman . '* dengan total pinjaman *' . rupiah($pinjaman->jumlah_pinjaman) . '* sudah di proses oleh bagian keuangan, silahkan tunggu 1 x 24 jam untuk proses pencairan dana ke rekening.'
                    ];
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://wa.pedasalami.com/send-message',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($data),
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                }
            } else if ($status == 2) {
                DB::table('pinjaman')->where('no_pinjaman', $no_pinjaman)->update([
                    'status' => 2
                ]);
                DB::table('ledger_bank')->where('no_ref', $no_pinjaman)->delete();
            } else {
                DB::table('pinjaman')->where('no_pinjaman', $no_pinjaman)->update([
                    'status' => 0
                ]);
                DB::table('ledger_bank')->where('no_ref', $no_pinjaman)->delete();
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }
}
