<?php
//Buat Kode Otomatis
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
        $akun = "1-1468";
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
