<?php
//Buat Kode Otomatis

use Illuminate\Support\Facades\DB;

function buatkode($nomor_terakhir, $kunci, $jumlah_karakter = 0)
{
    /* mencari nomor baru dengan memecah nomor terakhir dan menambahkan 1
    string nomor baru dibawah ini harus dengan format XXX000000
    untuk penggunaan dalam format lain anda harus menyesuaikan sendiri */
    $nomor_baru = intval(substr($nomor_terakhir, strlen($kunci))) + 1;
    //    menambahkan nol didepan nomor baru sesuai panjang jumlah karakter
    $nomor_baru_plus_nol = str_pad($nomor_baru, $jumlah_karakter, "0", STR_PAD_LEFT);
    //    menyusun kunci dan nomor baru
    $kode = $kunci . $nomor_baru_plus_nol;
    return $kode;
}


function desimal($nilai)
{

    return number_format($nilai, '2', ',', '.');
}
function desimal3($nilai)
{
    return number_format($nilai, '3', ',', '.');
}

function rupiah($nilai)
{

    return number_format($nilai, '0', ',', '.');
}


function DateToIndo2($date2)
{ // fungsi atau method untuk mengubah tanggal ke format indonesia
    // variabel BulanIndo merupakan variabel array yang menyimpan nama-nama bulan
    $BulanIndo2 = array(
        "Januari", "Februari", "Maret",
        "April", "Mei", "Juni",
        "Juli", "Agustus", "September",
        "Oktober", "November", "Desember"
    );

    $tahun2 = substr($date2, 0, 4); // memisahkan format tahun menggunakan substring
    $bulan2 = substr($date2, 5, 2); // memisahkan format bulan menggunakan substring
    $tgl2   = substr($date2, 8, 2); // memisahkan format tanggal menggunakan substring

    $result = $tgl2 . " " . $BulanIndo2[(int)$bulan2 - 1] . " " . $tahun2;
    return ($result);
}


function penyebut($nilai)
{
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10) . " belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
    }
    return $temp;
}

function terbilang($nilai)
{
    if ($nilai < 0) {
        $hasil = "minus " . trim(penyebut($nilai));
    } else {
        $hasil = trim(penyebut($nilai));
    }
    return $hasil;
}


function diffInMonths(\DateTime $date1, \DateTime $date2)
{
    $diff = $date1->diff($date2);

    $months = $diff->y * 12 + $diff->m + $diff->d / 30;

    return (int) round($months);
}


//Seting Cabang Baru

function getAkunpiutangcabang($kode_cabang)
{
    if ($kode_cabang == 'TSM') {
        $akun = "1-1468";
    } else if ($kode_cabang == 'BDG') {
        $akun = "1-1402";
    } else if ($kode_cabang == 'BGR') {
        $akun = "1-1403";
    } else if ($kode_cabang == 'PWT') {
        $akun = "1-1404";
    } else if ($kode_cabang == 'TGL') {
        $akun = "1-1405";
    } else if ($kode_cabang == "SKB") {
        $akun = "1-1407";
    } else if ($kode_cabang == "GRT") {
        $akun = "1-1487";
    } else if ($kode_cabang == "SMR") {
        $akun = "1-1488";
    } else if ($kode_cabang == "SBY") {
        $akun = "1-1486";
    } else if ($kode_cabang == "PST") {
        $akun = "1-1489";
    } else if ($kode_cabang == "KLT") {
        $akun = "1-1490";
    } else if ($kode_cabang == "PWK") {
        $akun = "1-1492";
    } else if ($kode_cabang == "BTN") {
        $akun = "1-1493";
    } else if ($kode_cabang == "BKI") {
        $akun = "1-1494";
    } else {
        $akun = "99";
    }

    return $akun;
}



function getAkunkaskecil()
{
    $akun = [
        'BDG' => '1-1102',
        'BGR' => '1-1103',
        'PST' => '1-1111',
        'TSM' => '1-1112',
        'SKB' => '1-1113',
        'PWT' => '1-1114',
        'TGL' => '1-1115',
        'SBY' => '1-1116',
        'SMR' => '1-1117',
        'KLT' => '1-1118',
        'GRT' => '1-1119',
        'PWK' => '1-1120',
        'BTN' => '1-1121',
        'BKI' => '1-1122'
    ];

    return $akun;
}


function hari($hari)
{
    $hari = date("D", strtotime($hari));

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


function ceklibur($dari, $sampai)
{
    $no = 1;
    $libur = [];
    $ceklibur = DB::table('harilibur')
        ->selectRaw('tanggal_libur,
        id_kantor,
        keterangan,
        IFNULL(harilibur_karyawan.nik,"ALL") as nik')
        ->leftJoin('harilibur_karyawan', 'harilibur.kode_libur', '=', 'harilibur_karyawan.kode_libur')
        ->where('kategori', 1)
        ->whereBetween('tanggal_libur', [$dari, $sampai])->get();

    foreach ($ceklibur as $d) {
        $libur[] = [
            'nik' => $d->nik,
            'id_kantor' => $d->id_kantor,
            'tanggal_libur' => $d->tanggal_libur,
            'keterangan' => $d->keterangan
        ];
    }

    return $libur;
}


function cekliburpenggantiminggu($dari, $sampai)
{
    $no = 1;
    $libur = [];
    $ceklibur = DB::table('harilibur')
        ->selectRaw('tanggal_libur,
        id_kantor,
        keterangan,
        tanggal_diganti,
        IFNULL(harilibur_karyawan.nik,"ALL") as nik')
        ->leftJoin('harilibur_karyawan', 'harilibur.kode_libur', '=', 'harilibur_karyawan.kode_libur')
        ->where('kategori', 2)
        ->whereBetween('tanggal_libur', [$dari, $sampai])->get();

    foreach ($ceklibur as $d) {
        $libur[] = [
            'nik' => $d->nik,
            'id_kantor' => $d->id_kantor,
            'tanggal_libur' => $d->tanggal_libur,
            'keterangan' => $d->keterangan,
            'tanggal_diganti' => $d->tanggal_diganti
        ];
    }

    return $libur;
}

function cekminggumasuk($dari, $sampai)
{
    $no = 1;
    $libur = [];
    $ceklibur = DB::table('harilibur')
        ->selectRaw('tanggal_libur,
        id_kantor,
        keterangan,
        tanggal_diganti,
        IFNULL(harilibur_karyawan.nik,"ALL") as nik')
        ->leftJoin('harilibur_karyawan', 'harilibur.kode_libur', '=', 'harilibur_karyawan.kode_libur')
        ->where('kategori', 2)
        ->whereBetween('tanggal_diganti', [$dari, $sampai])->get();

    foreach ($ceklibur as $d) {
        $libur[] = [
            'nik' => $d->nik,
            'id_kantor' => $d->id_kantor,
            'tanggal_libur' => $d->tanggal_libur,
            'keterangan' => $d->keterangan,
            'tanggal_diganti' => $d->tanggal_diganti
        ];
    }

    return $libur;
}


function cekwfh($dari, $sampai)
{
    $no = 1;
    $libur = [];
    $ceklibur = DB::table('harilibur')
        ->selectRaw('tanggal_libur,
        id_kantor,
        keterangan,
        tanggal_diganti,
        IFNULL(harilibur_karyawan.nik,"ALL") as nik')
        ->leftJoin('harilibur_karyawan', 'harilibur.kode_libur', '=', 'harilibur_karyawan.kode_libur')
        ->where('kategori', 3)
        ->whereBetween('tanggal_libur', [$dari, $sampai])->get();

    foreach ($ceklibur as $d) {
        $libur[] = [
            'nik' => $d->nik,
            'id_kantor' => $d->id_kantor,
            'tanggal_libur' => $d->tanggal_libur,
            'keterangan' => $d->keterangan,
            'tanggal_diganti' => $d->tanggal_diganti
        ];
    }

    return $libur;
}


function cekwfhfull($dari, $sampai)
{
    $no = 1;
    $libur = [];
    $ceklibur = DB::table('harilibur')
        ->selectRaw('tanggal_libur,
        id_kantor,
        keterangan,
        tanggal_diganti,
        IFNULL(harilibur_karyawan.nik,"ALL") as nik')
        ->leftJoin('harilibur_karyawan', 'harilibur.kode_libur', '=', 'harilibur_karyawan.kode_libur')
        ->where('kategori', 4)
        ->whereBetween('tanggal_libur', [$dari, $sampai])->get();

    foreach ($ceklibur as $d) {
        $libur[] = [
            'nik' => $d->nik,
            'id_kantor' => $d->id_kantor,
            'tanggal_libur' => $d->tanggal_libur,
            'keterangan' => $d->keterangan,
            'tanggal_diganti' => $d->tanggal_diganti
        ];
    }

    return $libur;
}

function cektgllibur($array, $search_list)
{

    // Create the result array
    $result = array();

    // Iterate over each array element
    foreach ($array as $key => $value) {

        // Iterate over each search condition
        foreach ($search_list as $k => $v) {

            // If the array element does not meet
            // the search condition then continue
            // to the next element
            if (!isset($value[$k]) || $value[$k] != $v) {

                // Skip two loops
                continue 2;
            }
        }

        // Append array element's key to the
        //result array
        $result[] = $value;
    }

    // Return result
    return $result;
}








function pihakpertamacabang($cabang, $perusahaan)
{
    $kepalaadmin = [
        'PWT' => 'Dimas Suteja',
        'BTN' => 'Anif Ardiana',
        'BDG' => 'M. Hirzam Purnama Dimas',
        'SKB' => 'Aceng Cahya Sugianto',
        'TGL' => 'Rosihul Iman',
        'SBY' => 'Excel Delvara Bachriandy',
        'SMR' => 'Muh. Fahmi Fadil',
        'KLT' => 'Fikkry Yusuf',
        'BGR' => 'Nurman Susila',
        'GRT' => 'Dade Gunawan',
        'BKI' => 'Wahib Al Aziz',
        'PWK' => 'Ricky Irawan',
        'TSM' => 'Sri Maharani'
    ];


    $kepalapenjualan = [
        'PWT' => 'Aria Permana Wiguna',
        'BTN' => 'Bagus Eka Winarno',
        'BDG' => 'Oki Rahmat Effendy',
        'SKB' => 'Agus Hanafi',
        'TGL' => 'Iwan Santoso',
        'SBY' => 'Iman Hilman',
        'SMR' => 'Rully Wiwik H',
        'KLT' => 'Nunuk Ratmiwati',
        'BGR' => 'Uus Kuswaya',
        'GRT' => 'Purnomo Raya',
        'BKI' => 'Yohanes Dewangkorojati',
        'PWK' => 'Oki Rahmat Effendy',
        'TSM' => 'Aceng Saepul Anwar'
    ];

    if ($perusahaan == "MP") {
        return $kepalaadmin[$cabang];
    } else {
        return $kepalapenjualan[$cabang];
    }
}
