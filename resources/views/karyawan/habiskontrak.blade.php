<ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
    <li class="nav-item">
        <a class="nav-link" id="kontraklewat-tab-justified" data-toggle="tab" href="#kontrak-lewat" role="tab" aria-controls="kontrak-lewat" aria-selected="false">Lewat JT<span class="badge badge-pill bg-danger ml-1">{{ $jml_kontrak_lewat }}</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" id="kontrakbulanini-tab-justified" data-toggle="tab" href="#kontrak-bulanini" role="tab" aria-controls="kontrak-bulanini" aria-selected="true">Bulan Ini <span class="badge badge-pill bg-danger ml-1">{{ $jml_kontrak_bulanini }}</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#kontrak-bulandepan" role="tab" aria-controls="kontrak-bulandepan" aria-selected="false">Bulan Depan <span class="badge badge-pill bg-warning ml-1">
                {{ $jml_kontrak_bulandepan }}</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="messages-tab-justified" data-toggle="tab" href="#kontrak-duabulan" role="tab" aria-controls="kontrak-duabulan" aria-selected="false">2 Bulan Lagi <span class="badge badge-pill bg-success">{{ $jml_kontrak_duabulan }}</span></a>
    </li>
</ul>
<div class="tab-content pt-1">
    <div class="tab-pane" id="kontrak-lewat" role="tabpanel" aria-labelledby="kontraklewat-tab-justified">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Dept</th>
                    <th>Kantor</th>
                    <th>Akhir Kontrak</th>
                </tr>
            </thead>
            <tbody style="font-size: 12px !important">
                @foreach ($kontrak_lewat as $d)
                <tr>
                    <td>{{ $d->nik }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td>{{ $d->nama_jabatan }}</td>
                    <td>{{ $d->kode_dept }}</td>
                    <td>{{ $d->id_kantor }}</td>
                    <td>{{ DateToIndo2($d->sampai) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="tab-pane active" id="kontrak-bulanini" role="tabpanel" aria-labelledby="kontrakbulanini-tab-justified">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Dept</th>
                    <th>Kantor</th>
                    <th>Akhir Kontrak</th>
                    <th></th>
                </tr>
            </thead>
            <tbody style="font-size: 12px !important">
                @foreach ($kontrak_bulanini as $d)
                <tr>
                    <td>{{ $d->nik }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td>{{ $d->nama_jabatan }}</td>
                    <td>{{ $d->kode_dept }}</td>
                    <td>{{ $d->id_kantor }}</td>
                    <td>{{ DateToIndo2($d->sampai) }}</td>
                    <td>
                        @if ($d->sampai < $hariini) <i class="fa fa-circle danger"></i>@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="tab-pane" id="kontrak-bulandepan" role="tabpanel" aria-labelledby="kontraklewat-tab-justified">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Dept</th>
                    <th>Kantor</th>

                </tr>
            </thead>
            <tbody style="font-size: 12px !important">
                @foreach ($kontrak_bulandepan as $d)
                <tr>
                    <td>{{ $d->nik }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td>{{ $d->nama_jabatan }}</td>
                    <td>{{ $d->kode_dept }}</td>
                    <td>{{ $d->id_kantor }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="tab-pane" id="kontrak-duabulan" role="tabpanel" aria-labelledby="kontraklewat-tab-justified">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Dept</th>
                    <th>Kantor</th>
                    <th>Akhir Kontrak</th>
                </tr>
            </thead>
            <tbody style="font-size: 12px !important">
                @foreach ($kontrak_duabulan as $d)
                <tr>
                    <td>{{ $d->nik }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td>{{ $d->nama_jabatan }}</td>
                    <td>{{ $d->kode_dept }}</td>
                    <td>{{ $d->id_kantor }}</td>
                    <td>{{ DateToIndo2($d->sampai) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
