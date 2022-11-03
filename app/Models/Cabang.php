<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cabang extends Model
{
    use HasFactory;
    protected $table = 'cabang';
    protected $guarded = [];

    function getCabang($cbg)
    {

        $iduser = Auth::user()->id;
        $oki = 27;
        $yulianto = 82;
        $ega = 7;
        $dadang = 97;
        $listcabang = array('BDG', 'PWK');
        $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB');
        $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
        $egacabang = array('TSM', 'GRT');
        if ($iduser == $oki) {
            $cabang = DB::table('cabang')->whereIn('kode_cabang', $listcabang)->get();
        } else if ($iduser == $yulianto) {
            $cabang = DB::table('cabang')->whereIn('kode_cabang', $wilayah_barat)->get();
        } else if ($iduser == $dadang) {
            $cabang = DB::table('cabang')->whereIn('kode_cabang', $wilayah_timur)->get();
        } else if ($iduser == $ega) {
            $cabang = DB::table('cabang')->whereIn('kode_cabang', $egacabang)->get();
        } else {
            // if ($cbg != "PCF" && $cbg != "PST") {
            //     if ($cbg == "GRT") {
            //         $cabang = DB::table('cabang')->where('kode_cabang', 'TSM')->get();
            //     } else {
            //         $cbg = DB::table('cabang')->where('kode_cabang', $cbg)->orWhere('sub_cabang', $cbg)->get();
            //         $cabang[] = "";
            //         foreach ($cbg as $c) {
            //             $cabang[] = $c->kode_cabang;
            //         }
            //         //dd($cabang);
            //         $cabang = DB::table('cabang')->whereIn('kode_cabang', $cabang)->get();
            //     }
            // } else {
            //     $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
            // }

            if ($cbg != "PCF" && $cbg != "PST") {
                $cbg = DB::table('cabang')->where('kode_cabang', $cbg)->orWhere('sub_cabang', $cbg)->get();
                $cabang[] = "";
                foreach ($cbg as $c) {
                    $cabang[] = $c->kode_cabang;
                }
                //dd($cabang);
                $cabang = DB::table('cabang')->whereIn('kode_cabang', $cabang)->get();
            } else {
                $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
            }
        }


        return $cabang;
    }


    function getCabanggudang($cbg)
    {
        $iduser = Auth::user()->id;
        $oki = 27;
        $yulianto = 82;
        $ega = 7;
        $dadang = 97;
        $listcabang = array('BDG', 'PWK');
        $wilayah_barat = array('BDG', 'TSM', 'GRT', 'PWK', 'BGR', 'SKB');
        $wilayah_timur = array('TGL', 'PWT', 'SBY', 'KLT', 'SMR');
        $egacabang = array('TSM', 'GRT');
        if ($iduser == $oki) {
            $cabang = DB::table('cabang')->whereIn('kode_cabang', $listcabang)->get();
        } else if ($iduser == $yulianto) {
            $cabang = DB::table('cabang')->whereIn('kode_cabang', $wilayah_barat)->get();
        } else if ($iduser == $dadang) {
            $cabang = DB::table('cabang')->whereIn('kode_cabang', $wilayah_timur)->get();
        } else if ($iduser == $ega) {
            $cabang = DB::table('cabang')->whereIn('kode_cabang', $egacabang)->get();
        } else {
            if ($cbg == "PCF") {
                $cabang = DB::table('cabang')->get();
            } else {
                $cabang = DB::table('cabang')->where('kode_cabang', $cbg)->orWhere('sub_cabang', $cbg)->get();
            }
        }
        return $cabang;
    }
}
