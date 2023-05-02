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
            $query->where('id_kantor', $cabang);
            $query->where('id_perusahaan', "MP");
        }

        if ($level == "kepala penjualan") {
            $query->where('id_kantor', $cabang);
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

        $query->orderBy('no_pinjaman', 'desc');
        $pinjaman = $query->paginate(15);
        $pinjaman->appends($request->all());


        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);

        $departemen = DB::table('hrd_departemen')->get();
        return view('pinjaman.index', compact('pinjaman', 'cabang', 'departemen'));
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
        $sp = DB::table('hrd_sp')->where('nik', $nik)->where('sampai', '>', $hariini)->first();



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
        $query->whereRaw('jumlah_pinjaman - totalpembayaran != 0');
        $cekpinjaman = $query->first();

        $kontrak = DB::table('hrd_kontrak')->where('nik', $nik)->orderBy('dari', 'desc')->first();

        $start = date_create($karyawan->tgl_masuk);
        $end = date_create($hariini);

        $cekmasakerja =  diffInMonths($start, $end);


        if ($sp != null) {
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
            }
        }
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
            DB::commit();
            return redirect('/pinjaman')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
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
}
