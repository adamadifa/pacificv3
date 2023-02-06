@foreach ($listtarget as $d)
<tr>
    <td>{{$d->id_karyawan}}</td>
    <td>{{$d->nama_karyawan}}</td>
    <td style="width:7%">
        @php
        if ($d->ab > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp
        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->ab}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="AB">
    </td>
    <td style="width:7%">
        @php
        if ($d->ar > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->ar}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="AR">
    </td>
    <td style="width:7%">
        @php
        if ($d->ase > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->ase}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="AS">
    </td>
    <td style="width:7%">
        @php
        if ($d->bb > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->bb}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="BB">
    </td>
    <td style="width:7%">
        @php
        if ($d->cg > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->cg}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="CG">
    </td>
    <td style="width:7%">
        @php
        if ($d->cgg > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->cgg}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="CGG">
    </td>
    <td style="width:7%">
        @php
        if ($d->dep > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->dep}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="DEP">
    </td>
    <td style="width:7%">
        @php
        if ($d->ds > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->ds}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="DS">
    </td>
    <td style="width:7%">
        @php
        if ($d->sp > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->sp}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="SP">
    </td>
    <td style="width:7%">
        @php
        if ($d->cg5 > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->cg5}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="CG5">
    </td>
    <td style="width:7%">
        @php
        if ($d->sc > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->sc}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="SC">
    </td>
    <td style="width:7%">
        @php
        if ($d->sp8 > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->sp8}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="SP8">
    </td>

    <td style="width:7%">
        @php
        if ($d->sp500 > 0) {
        $bgcolor = "#d1ff7a";
        } else {
        $bgcolor = "";
        }
        @endphp

        <input type="number" style="background-color:{{$bgcolor}}" class="form-control text-center settargetproduksales" value="{{$d->sp500}}" idkaryawan="{{$d->id_karyawan}}" kodeproduk="SP500">
    </td>

</tr>
@endforeach

<script>
    $(function() {
        function nonaktif() {
            var kp = "{{ $cektarget != null ? $cektarget->kp : 1}}";
            if (parseInt(kp) == 0) {
                swal("Oops", "Target Sudah Di Kunci, Silahkan Hubungi Tim IT Untuk Membuka Target", "warning");
                $(".settargetproduksales").prop("disabled", true);
            }
        }


        function loadlisttarget() {
            var kode_target = $("#kode_target").val();
            var kode_cabang = $("#kode_cabang").val();
            $.ajax({
                type: 'POST'
                , url: '/targetkomisi/getlisttarget'
                , data: {
                    _token: "{{csrf_token()}}"
                    , kode_target: kode_target
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#loadlisttarget").html(respond);
                }
            });
        }

        nonaktif();

        // $(".settargetproduksales").on('change', function() {
        //     loadlisttarget();

        // });
        $(".settargetproduksales").on('keyup', function() {
            var id_karyawan = $(this).attr("idkaryawan");
            var kode_produk = $(this).attr("kodeproduk");
            var jmltarget = $(this).val();
            var kode_target = $("#kode_target").val();
            $.ajax({
                type: 'POST'
                , url: '/targetkomisi/store'
                , data: {
                    _token: "{{csrf_token()}}"
                    , kode_target: kode_target
                    , id_karyawan: id_karyawan
                    , kode_produk: kode_produk
                    , jmltarget: jmltarget
                }
                , cache: false
                , success: function(respond) {

                }
            });

        });

    });

</script>
