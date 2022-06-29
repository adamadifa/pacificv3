<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class MemoController extends Controller
{

    public function index()
    {
        return view('memo.index');
    }

    public function show($kode_dept, Request $request)
    {
        $id_user = Auth::user()->id;
        $query = Memo::query();
        $query->selectRaw("memo.id,tanggal,no_memo,judul_memo,kode_dept,kategori,link,totaldownload,name,memo.id_user,cekread.id_user as status_read");
        $query->leftJoin(
            DB::raw("(
                SELECT id,COUNT(id_user) as totaldownload FROM memo_download GROUP BY id
            ) download"),
            function ($join) {
                $join->on('memo.id', '=', 'download.id');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT id,id_user FROM memo_download WHERE id_user='$id_user'
            ) cekread"),
            function ($join) {
                $join->on('memo.id', '=', 'cekread.id');
            }
        );
        $query->leftJoin(
            DB::raw("(
                SELECT id,name FROM users
            ) user"),
            function ($join) {
                $join->on('memo.id_user', '=', 'user.id');
            }
        );
        $query->where('kode_dept', $kode_dept);
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }

        if (!empty($request->judul_memo)) {
            $query->where('judul_memo', 'like', '%' . $request->judul_memo . '%');
        }

        $query->orderBy('kategori', 'asc');
        $query->orderBy('no_memo', 'asc');
        $query->orderBy('tanggal', 'desc');
        $memo = $query->paginate(30);
        $memo->appends($request->all());
        return view('memo.show', compact('memo'));
    }

    public function downloadcount(Request $request)
    {
        $id = $request->id;
        $id_user = Auth::user()->id;
        $cek = DB::table('memo_download')->where('id', $id)->where('id_user', $id_user)->count();
        if (empty($cek)) {
            DB::table('memo_download')->insert([
                'id' => $id,
                'id_user' => $id_user
            ]);
        }
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $hapus = DB::table('memo')->where('id', $id)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan, Hubungi Tim IT']);
        }
    }

    public function create()
    {
        return view('memo.create');
    }

    public function store(Request $request)
    {
        $no_memo = $request->no_memo;
        $tanggal = $request->tanggal;
        $judul_memo = $request->judul_memo;
        $kategori = $request->kategori;
        $kode_dept = $request->kode_dept;
        $link = $request->link;
        $id_user = Auth::user()->id;

        $data = [
            'no_memo' => $no_memo,
            'tanggal' => $tanggal,
            'judul_memo' => $judul_memo,
            'kategori' => $kategori,
            'kode_dept' => $kode_dept,
            'link' => $link,
            'id_user' => $id_user
        ];

        $simpan = DB::table('memo')->insert($data);
        if ($simpan) {
            return redirect('/memo')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/memo')->with(['warning' => 'Data Gagal Disimpan']);
        }
    }
}
