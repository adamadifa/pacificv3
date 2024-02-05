<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Kasbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class KasbonController extends Controller
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
        $query = Kasbon::query();
        $query->select('kasbon.*', 'nama_karyawan', 'nama_jabatan', 'nama_dept', 'totalpembayaran', 'tgl_bayar', 'jatuh_tempo', 'tgl_ledger');
        $query->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->leftJoin(
            DB::raw("(
            SELECT no_kasbon,tgl_bayar, jumlah as totalpembayaran FROM kasbon_historibayar
        ) hb"),
            function ($join) {
                $join->on('kasbon.no_kasbon', '=', 'hb.no_kasbon');
            }
        );
        $query->leftJoin('ledger_bank', 'kasbon.no_kasbon', '=', 'ledger_bank.no_ref');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tgl_kasbon', [$request->dari, $request->sampai]);
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

        if ($level == "spv maintenance") {
            $query->where('master_karyawan.kode_dept', 'MTC');
            $query->whereNotIN('nama_jabatan', ['MANAGER', 'SUPERVISOR']);
        }

        if ($level == "manager produksi") {
            $query->whereIn('master_karyawan.kode_dept', ['PRD', 'MTC']);
            $query->where('nama_jabatan', '!=', 'MANAGER');
        }

        if ($level == "manager ga") {
            $query->where('master_karyawan.kode_dept', 'GAF');
        }


        if ($level == "rom") {
            $query->where('nama_jabatan', 'KEPALA ADMIN');
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
            $query->whereNotIn('master_karyawan.id_jabatan', $show_for_hrd);
        }

        if ($level == "admin pdqc") {
            $listkaryawan = [
                '08.12.100',
                '11.10.090',
                '13.02.198',
                '91.01.016',
                '03.04.045',
                '08.05.042',
                '12.09.182',
                '05.01.055',
                '13.03.202',
                '17.08.287'
            ];

            $query->whereIn('kasbon.nik', $listkaryawan);
        }

        if ($level == "spv pdqc") {
            $listkaryawan = [
                '13.03.200',
                '14.08.220',
                '13.07.021',
                '15.05.174',
                '10.08.128',
                '13.09.206',
                '13.09.209',
                '19.09.303',
                '21.06.304',
                '16.01.069',
                '18.03.305'
            ];

            $query->whereIn('kasbon.nik', $listkaryawan);
        }

        if ($level == "manager audit") {
            $query->where('master_karyawan.kode_dept', 'ADT');
        }
        $query->orderBy('no_kasbon', 'desc');
        $kasbon = $query->paginate(15);
        $kasbon->appends($request->all());


        $cbg = new Cabang();
        $cabang = $cbg->getCabang($this->cabang);

        $departemen = DB::table('hrd_departemen')->get();
        $hakakses = config('global.pinjamanpage');
        if (in_array($level, $hakakses)) {
            return view('kasbon.index', compact('kasbon', 'cabang', 'departemen'));
        } else {
            echo "Anda Tidak Memiliki Hak Akses";
        }
    }
    public function create($nik)
    {
        $nik = Crypt::decrypt($nik);
        $query = Karyawan::query();
        $query->select('nik', 'nama_karyawan', 'tgl_masuk', 'nama_dept', 'jenis_kelamin', 'nama_jabatan', 'id_perusahaan', 'id_kantor', 'klasifikasi', 'status_karyawan', 'nama_cabang', 'master_karyawan.id_jabatan');
        $query->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept');
        $query->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id');
        $query->leftjoin('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang');
        $query->where('nik', $nik);
        $karyawan = $query->first();

        $kontrak = DB::table('hrd_kontrak')->where('nik', $nik)->orderBy('dari', 'desc')->first();

        $cekpinjaman = DB::table('pinjaman')
            ->select('pinjaman.*', 'totalpembayaran')
            ->where('nik', $nik)
            ->leftJoin(
                DB::raw("(
                SELECT no_pinjaman,SUM(jumlah) as totalpembayaran FROM pinjaman_historibayar GROUP BY no_pinjaman
            ) hb"),
                function ($join) {
                    $join->on('pinjaman.no_pinjaman', '=', 'hb.no_pinjaman');
                }
            )
            ->whereRaw('IFNULL(jumlah_pinjaman,0) - IFNULL(totalpembayaran,0) != 0')
            ->first();



        $no_pinjaman = $cekpinjaman != null  ?  $cekpinjaman->no_pinjaman : '';
        $angsuran_max = $cekpinjaman != null ? $cekpinjaman->angsuran_max : 0;

        $cicilan = DB::table('pinjaman_rencanabayar')->where('no_pinjaman', $no_pinjaman)->where('cicilan_ke', 1)->first();


        $kasbon_max = $cekpinjaman != null ? $cekpinjaman->angsuran_max - $cicilan->jumlah : 0;

        $cekkasbon = DB::table('kasbon')
            ->leftJoin(
                DB::raw("(
                SELECT no_kasbon,tgl_bayar, jumlah as totalpembayaran FROM kasbon_historibayar
            ) hb"),
                function ($join) {
                    $join->on('kasbon.no_kasbon', '=', 'hb.no_kasbon');
                }
            )
            ->where('nik', $nik)
            ->whereNull('totalpembayaran')
            ->count();

        if ($cekkasbon > 0) {
            return view('kasbon.notifbelumlunas');
        } else {
            return view('kasbon.create', compact('karyawan', 'kontrak', 'cicilan', 'kasbon_max'));
        }
    }

    public function store(Request $request)
    {
        $nik = $request->nik;
        $tgl_kasbon = $request->tgl_kasbon;
        $jml_kasbon = str_replace(".", "", $request->jml_kasbon);
        $status_karyawan = $request->status_karyawan;
        $akhir_kontrak = $request->akhir_kontrak;
        $id_jabatan = $request->id_jabatan;
        $jatuh_tempo = $request->jatuh_tempo;
        $bln_cicilan = date("m", strtotime($jatuh_tempo));
        $thn_cicilan = date("Y", strtotime($jatuh_tempo));
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


        $tgl = explode("-", $tgl_kasbon);
        $tahun = substr($tgl[0], 2, 2);
        $kasbon = DB::table("kasbon")
            ->whereRaw('YEAR(tgl_kasbon)="' . $tgl[0] . '"')
            ->orderBy("no_kasbon", "desc")
            ->first();
        $last_nokasbon = $kasbon != null ? $kasbon->no_kasbon : '';
        $no_kasbon  = buatkode($last_nokasbon, "KB" . $tahun, 3);

        $id_user = Auth::user()->id;
        $cekpembayaran = DB::table('kasbon_potongangaji')->where('bulan', $bln_cicilan)->where('tahun', $thn_cicilan)->count();
        if ($cekpembayaran > 0) {
            return Redirect::back()->with(['warning' => 'Kasbon Pada Periode Ini Sudah Ditutup']);
        }
        $data = [
            'no_kasbon' => $no_kasbon,
            'tgl_kasbon' => $tgl_kasbon,
            'nik' => $nik,
            'status_karyawan' => $status_karyawan,
            'akhir_kontrak' => $akhir_kontrak,
            'id_jabatan' => $id_jabatan,
            'jumlah_kasbon' => $jml_kasbon,
            'jatuh_tempo' => $jatuh_tempo,
            'id_user' => $id_user
        ];

        try {
            DB::table('kasbon')->insert($data);
            $data = [
                'api_key' => 'NHoqE4TUf6YLQhJJQAGSUjj4wOMyzh',
                'sender' => '6289670444321',
                'number' => '087708590299',
                'message' => '*' . $nik . "-" . $karyawan->nama_karyawan . '*, dari Departemen ' . $karyawan->nama_dept . ' Mengajukan Kasbon dengan Nomor Kasbon *' . $no_kasbon . '* dan total pinjaman *' . $request->jml_kasbon . '* Menunggu untuk Segera di proses.'
            ];

            $rani = [
                'api_key' => 'NHoqE4TUf6YLQhJJQAGSUjj4wOMyzh',
                'sender' => '6289670444321',
                'number' => '081221403962',
                'message' => '*' . $nik . "-" . $karyawan->nama_karyawan . '*, dari Departemen ' . $karyawan->nama_dept . ' Mengajukan Kasbon dengan Nomor Kasbon *' . $no_kasbon . '* dan total pinjaman *' . $request->jml_kasbon . '* Menunggu untuk Segera di proses.'
            ];

            $siska = [
                'api_key' => 'NHoqE4TUf6YLQhJJQAGSUjj4wOMyzh',
                'sender' => '6289670444321',
                'number' => '085942091886',
                'message' => '*' . $nik . "-" . $karyawan->nama_karyawan . '*, dari Departemen ' . $karyawan->nama_dept . ' Mengajukan Kasbon dengan Nomor Kasbon *' . $no_kasbon . '* dan total pinjaman *' . $request->jml_kasbon . '* Menunggu untuk Segera di proses.'
            ];


            $ardi = [
                'api_key' => 'NHoqE4TUf6YLQhJJQAGSUjj4wOMyzh',
                'sender' => '6289670444321',
                'number' => '0811211451',
                'message' => '*' . $nik . "-" . $karyawan->nama_karyawan . '*, dari Departemen ' . $karyawan->nama_dept . ' Mengajukan Kasbon dengan Nomor Kasbon *' . $no_kasbon . '* dan total pinjaman *' . $request->jml_kasbon . '* Menunggu untuk Segera di proses.'
            ];
            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://wa.pedasalami.com/send-message',
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => json_encode($data),
            //     CURLOPT_HTTPHEADER => array(
            //         'Content-Type: application/json'
            //     ),
            // ));

            // $response = curl_exec($curl);

            // curl_close($curl);


            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://wa.pedasalami.com/send-message',
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => json_encode($ardi),
            //     CURLOPT_HTTPHEADER => array(
            //         'Content-Type: application/json'
            //     ),
            // ));

            // $response = curl_exec($curl);

            // curl_close($curl);


            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://wa.pedasalami.com/send-message',
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => json_encode($siska),
            //     CURLOPT_HTTPHEADER => array(
            //         'Content-Type: application/json'
            //     ),
            // ));

            // $response = curl_exec($curl);

            // curl_close($curl);


            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://wa.pedasalami.com/send-message',
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => json_encode($rani),
            //     CURLOPT_HTTPHEADER => array(
            //         'Content-Type: application/json'
            //     ),
            // ));

            // $response = curl_exec($curl);

            // curl_close($curl);
            return redirect('/kasbon')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            dd($e);
            return redirect('/kasbon')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }

    public function delete($no_kasbon)
    {
        $no_kasbon = Crypt::decrypt($no_kasbon);
        DB::beginTransaction();
        try {
            DB::table('kasbon')->where('no_kasbon', $no_kasbon)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }


    public function edit(Request $request)
    {
        $no_kasbon = $request->no_kasbon;
        $kasbon = DB::table('kasbon')
            ->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('no_kasbon', $no_kasbon)->first();


        $hariini = date("Y-m-d");

        return view('kasbon.edit', compact('kasbon'));
    }


    public function proseskasbon(Request $request)
    {
        $no_pinjaman = $request->no_pinjaman;
        $no_kasbon = $request->no_kasbon;
        $kasbon = DB::table('kasbon')
            ->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('no_kasbon', $no_kasbon)->first();


        $bank = DB::table('master_bank')->where('kode_cabang', 'PST')->get();
        return view('kasbon.proses', compact('kasbon', 'bank'));
    }


    public function storeproseskasbon($no_kasbon, Request $request)
    {
        $no_kasbon = Crypt::decrypt($no_kasbon);
        $kasbon = DB::table('kasbon')
            ->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('no_kasbon', $no_kasbon)->first();
        $status = $request->statusaksi;
        $tgl_transfer = $request->tgl_transfer;
        $tanggal = explode("-", $tgl_transfer);
        $tahun = substr($tanggal[0], 2, 2);
        $cabang = "PST";
        $nama_karyawan = $kasbon->nama_karyawan;
        $bank = $request->bank;
        $jumlah = $kasbon->jumlah_kasbon;
        $no_hp = $kasbon->no_hp;
        DB::beginTransaction();
        try {
            if ($status == 1) {
                DB::table('kasbon')->where('no_kasbon', $no_kasbon)->update([
                    'status' => 1
                ]);

                DB::table('ledger_bank')->where('no_ref', $no_kasbon)->delete();
                $lastledger = DB::table('ledger_bank')
                    ->select('no_bukti')
                    ->whereRaw('LENGTH(no_bukti)=12')
                    ->whereRaw('LEFT(no_bukti,7) ="LR' . $cabang . $tahun . '"')
                    ->orderBy('no_bukti', 'desc')
                    ->first();
                if ($lastledger == null) {
                    $last_no_bukti = 'LR' . $cabang . $tahun . '0000';
                } else {
                    $last_no_bukti = $lastledger->no_bukti;
                }
                $no_bukti = buatkode($last_no_bukti, 'LR' . $cabang . $tahun, 5);

                DB::table('ledger_bank')
                    ->insert([
                        'no_bukti'        => $no_bukti,
                        'no_ref'          => $no_kasbon,
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
                        'message' => '*' . $kasbon->nama_karyawan . '*, Ajuan Kasbon dengan Nomor Kasbon *' . $kasbon->no_kasbon . '* dan total kasbon *' . rupiah($kasbon->jumlah_kasbon) . '* sudah di proses oleh bagian keuangan, silahkan tunggu 1 x 24 jam untuk proses pencairan dana ke rekening.'
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
                DB::table('kasbon')->where('no_kasbon', $no_kasbon)->update([
                    'status' => 2
                ]);
                DB::table('ledger_bank')->where('no_ref', $no_kasbon)->delete();
            } else {
                DB::table('kasbon')->where('no_kasbon', $no_kasbon)->update([
                    'status' => 0
                ]);
                DB::table('ledger_bank')->where('no_ref', $no_kasbon)->delete();
            }
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Di Update']);
        }
    }


    public function cetakformulir($no_kasbon)
    {
        $no_kasbon = Crypt::decrypt($no_kasbon);
        $kasbon = DB::table('kasbon')
            ->join('master_karyawan', 'kasbon.nik', '=', 'master_karyawan.nik')
            ->join('hrd_jabatan', 'master_karyawan.id_jabatan', '=', 'hrd_jabatan.id')
            ->join('hrd_departemen', 'master_karyawan.kode_dept', '=', 'hrd_departemen.kode_dept')
            ->join('cabang', 'master_karyawan.id_kantor', '=', 'cabang.kode_cabang')
            ->where('no_kasbon', $no_kasbon)->first();
        return view('kasbon.cetakformulir', compact('kasbon'));
    }
}
