<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::query();
        $query->selectRaw("pembelian.nobukti_pembelian,
        tgl_pembelian,
        tgl_jatuhtempo,
        ppn,
        no_fak_pajak,
        pembelian.kode_supplier,
        nama_supplier,
        pembelian.kode_dept,
        jenistransaksi,
        ref_tunai,
        harga,
        kontrabon,
        penyesuaian,
        jmlbayar");
        $query->join('supplier', 'pembelian.kode_supplier', '=', 'supplier.kode_supplier');
        $query->leftJoin(
            DB::raw('(
                SELECT nobukti_pembelian, SUM( IF ( STATUS = "PMB", ( ( qty * harga ) + penyesuaian ), 0 ) ) - SUM( IF ( STATUS = "PNJ", ( qty * harga ), 0 ) ) as harga
                FROM detail_pembelian
                GROUP BY nobukti_pembelian
            ) detailpembelian'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'detailpembelian.nobukti_pembelian');
            }
        );
        $query->leftJoin(
            DB::raw('(
                SELECT nobukti_pembelian, COUNT( nobukti_pembelian ) as kontrabon
                FROM detail_kontrabon
                GROUP BY nobukti_pembelian
            ) kontrabon'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'kontrabon.nobukti_pembelian');
            }
        );

        $query->leftJoin(
            DB::raw('(
                SELECT
                nobukti_pembelian,
                (SUM(IF( status_dk = "K" AND kode_akun = "2-1200" OR status_dk = "K" AND kode_akun = "2-1300", (qty * harga), 0))
                - SUM(IF( status_dk = "D" AND kode_akun = "2-1200" OR status_dk = "D" AND kode_akun = "2-1300", (qty * harga), 0))
                ) as penyesuaian
                FROM
                jurnal_koreksi
                GROUP BY nobukti_pembelian
            ) jurnalkoreksi'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'jurnalkoreksi.nobukti_pembelian');
            }
        );

        $query->leftJoin(
            DB::raw('(
                SELECT
                nobukti_pembelian,
                SUM(jmlbayar) as jmlbayar
                FROM
                historibayar_pembelian hb
                INNER JOIN detail_kontrabon ON hb.no_kontrabon = detail_kontrabon.no_kontrabon
                GROUP BY
                nobukti_pembelian
            ) historibayar'),
            function ($join) {
                $join->on('pembelian.nobukti_pembelian', '=', 'historibayar.nobukti_pembelian');
            }
        );


        $query->whereBetween('tgl_pembelian', [$request->dari, $request->sampai]);

        $query->orderBy('tgl_pembelian', 'desc');
        $query->orderBy('nobukti_pembelian', 'desc');
        $pembelian = $query->paginate(15);
        $pembelian->appends($request->all());
        $departemen = DB::table('departemen')->where('status_pengajuan', 1)->get();
        $supplier = Supplier::orderBy('nama_supplier')->get();
        return view('pembelian.index', compact('departemen', 'supplier', 'pembelian'));
    }
}
