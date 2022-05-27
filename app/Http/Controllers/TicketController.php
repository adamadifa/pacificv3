<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use phpDocumentor\Reflection\Types\Null_;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query();
        $query->select('ticket.*', 'users.name as nama_user', 'admin.name as nama_admin');
        $query->join('users', 'ticket.id_user', '=', 'users.id');
        $query->leftJoin('users as admin', 'ticket.id_admin', '=', 'admin.id');
        if (!empty($request->dari) || !empty($request->sampai)) {
            $query->whereBetween('tanggal_pengajuan', [$request->dari, $request->sampai]);
        }

        if (!empty($request->status)) {
            if ($request->status ==  "pending") {
                $query->where('status', 0);
            } else if ($request->status == "disetujui") {
                $query->where('status', 1);
            } else if ($request->status == "selesai") {
                $query->where('status', 2);
            }
        }

        if (Auth::user()->level != "admin" && Auth::user()->level != "manager accounting") {
            $query->where('id_user', Auth::user()->id);
        }

        $query->orderBy('tanggal_pengajuan', 'desc');
        $query->orderBy('kode_pengajuan', 'desc');
        $ticket = $query->paginate(15);
        $ticket = $ticket->appends($request->all());
        return view('ticket.index', compact('ticket'));
    }

    public function create()
    {
        return view('ticket.create');
    }

    public function store(Request $request)
    {
        $keterangan = $request->keterangan;
        $tanggal_pengajuan = date("Y-m-d H:i:s");
        $bulan = date("m");
        $tahun = substr(date("Y"), 2, 2);
        $dari = $tahun . "-" . $bulan . "-01";
        $sampai = date("Y-m-t", strtotime($dari));
        $status = 0;
        $id_user = Auth::user()->id;
        $ticket = DB::table('ticket')->whereBetween('tanggal_pengajuan', [$dari, $sampai])->orderBy('kode_pengajuan', 'desc')->first();
        $lastkode_pengajuan = $ticket != null ? $ticket->kode_pengajuan : '';
        $kode_pengajuan = buatkode($lastkode_pengajuan, "MT", 4);
        $data = [
            'kode_pengajuan' => $kode_pengajuan,
            'tanggal_pengajuan' => $tanggal_pengajuan,
            'keterangan' => $keterangan,
            'status' => $status,
            'id_user' => $id_user
        ];
        $simpan = DB::table('ticket')->insert($data);
        if ($simpan) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }

    public function delete($kode_pengajuan)
    {
        $kode_pengajuan = Crypt::decrypt($kode_pengajuan);
        $hapus = DB::table('ticket')->where('kode_pengajuan', $kode_pengajuan)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Hapus,Hubungi Tim IT']);
        }
    }

    public function approve($kode_pengajuan)
    {
        $kode_pengajuan = Crypt::decrypt($kode_pengajuan);
        $update = DB::table('ticket')->where('kode_pengajuan', $kode_pengajuan)->update(['status' => 1]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil di Approve']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal di Approve,Hubungi Tim IT']);
        }
    }

    public function done($kode_pengajuan)
    {
        $kode_pengajuan = Crypt::decrypt($kode_pengajuan);
        $data = [
            'status' => 2,
            'id_admin' => Auth::user()->id,
            'tanggal_selesai' => date("Y-m-d H:i:s")
        ];
        $update = DB::table('ticket')->where('kode_pengajuan', $kode_pengajuan)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil di Selesaikan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal di Selesaikan,Hubungi Tim IT']);
        }
    }

    public function batalapprove($kode_pengajuan)
    {
        $kode_pengajuan = Crypt::decrypt($kode_pengajuan);
        $update = DB::table('ticket')->where('kode_pengajuan', $kode_pengajuan)->update(['status' => 0]);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil di Batalkan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal di Batalkan,Hubungi Tim IT']);
        }
    }

    public function bataldone($kode_pengajuan)
    {
        $kode_pengajuan = Crypt::decrypt($kode_pengajuan);
        $data = [
            'status' => 1,
            'id_admin' => Null,
            'tanggal_selesai' => Null
        ];
        $update = DB::table('ticket')->where('kode_pengajuan', $kode_pengajuan)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data Berhasil di Selesaikan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal di Selesaikan,Hubungi Tim IT']);
        }
    }
}
