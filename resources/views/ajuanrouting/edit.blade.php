<style>
    .form-label {
        font-weight: bold;
        margin-top: 10px;
    }

    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }
</style>

<form action="/ajuanrouting/{{ Crypt::encrypt($ajuanrouting->no_pengajuan) }}/update" method="POST"
    id="frmAjuanrouting">
    @csrf
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <th style="width: 30%">Kode Pelanggan</th>
                    <td>{{ $ajuanrouting->kode_pelanggan }}</td>
                </tr>
                <tr>
                    <th>Nama Pelanggan</th>
                    <td>{{ $ajuanrouting->nama_pelanggan }}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{ $ajuanrouting->alamat_pelanggan }}</td>
                </tr>
                <tr>
                    <th>Salesman</th>
                    <td>{{ $ajuanrouting->nama_karyawan }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Pengajuan" value="{{ $ajuanrouting->tgl_pengajuan }}" field="tgl_pengajuan"
                icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <ul class="list-unstyled mb-0">
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-checkbox-con vs-checkbox-success">
                                <input type="checkbox" name="hari[]" <?php if (str_contains($ajuanrouting->hari,
                                'Senin')) {
                                echo 'checked';
                                }
                                ?> value="Senin">
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">Senin</span>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-checkbox-con vs-checkbox-success">
                                <input type="checkbox" name="hari[]" <?php if (str_contains($ajuanrouting->hari,
                                'Selasa')) {
                                echo 'checked';
                                }
                                ?> value="Selasa">
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">Selasa</span>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-checkbox-con vs-checkbox-success">
                                <input type="checkbox" name="hari[]" value="Rabu" <?php if
                                    (str_contains($ajuanrouting->hari,
                                'Rabu')) {
                                echo 'checked';
                                }
                                ?>>
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">Rabu</span>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-checkbox-con vs-checkbox-success">
                                <input type="checkbox" name="hari[]" value="Kamis" <?php if
                                    (str_contains($ajuanrouting->hari,
                                'Kamis')) {
                                echo 'checked';
                                }
                                ?>>
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">Kamis</span>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-checkbox-con vs-checkbox-success">
                                <input type="checkbox" name="hari[]" value="Jumat" <?php if
                                    (str_contains($ajuanrouting->hari,
                                'Jumat')) {
                                echo 'checked';
                                }
                                ?>>
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">Jumat</span>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-checkbox-con vs-checkbox-success">
                                <input type="checkbox" name="hari[]" value="Sabtu" <?php if
                                    (str_contains($ajuanrouting->hari,
                                'Sabtu')) {
                                echo 'checked';
                                }
                                ?>>
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">Sabtu</span>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-checkbox-con vs-checkbox-success">
                                <input type="checkbox" name="hari[]" value="Minggu" <?php if
                                    (str_contains($ajuanrouting->hari,
                                'Minggu')) {
                                echo 'checked';
                                }
                                ?>>
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">Minggu</span>
                            </div>
                        </fieldset>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <textarea name="keterangan" id="keterangan" class="form-control" cols="30" rows="5"
                    placeholder="Keterangan">{{ $ajuanrouting->keterangan }}</textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary w-100" type="submit"><i class="feather icon-send mr-1"></i>Submit</button>
        </div>
    </div>

</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#frmAjuanrouting").submit(function() {
            var tgl_pengajuan = $("#tgl_pengajuan").val();
            if (tgl_pengajuan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Pengajuan Harus Diisi!'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_pengajuan").focus();
                });
                return false;
            }
        });
    });

</script>