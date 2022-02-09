<title>Cetak Ajuan Limit Kredit</title>
<style>
    @page {
        margin: 20px 20px 10px 30px !important;
        padding: 0px 0px 0px 0px !important;
    }

    .judul {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-size: 20px;
        text-align: center;
        color: #005e2f
    }

    .judul2 {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-size: 16px;


    }

    .huruf {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
    }

    .ukuranhuruf {
        font-size: 12px;
    }

    .datatable3 {
        border: 1px solid #05090e;
        border-collapse: collapse;
        /* font-size: 10px; */
        /*float:left; */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        width: 100%;


    }

    .datatable3 td {
        border: 1px solid #000000;
        padding: 6px;
        font-size: 12px;

    }


    .datatable3 th {
        border: 1px solid #000000;
        font-weight: bold;
        padding: 4px;
        text-align: center;
        font-size: 12px;
        background-color: green;
        color: white;
    }

    hr.style2 {
        border-top: 3px double #8c8b8b;
    }

</style>
<table style="width:60%" class="datatable3">
    <tr>
        <td>

            <?php
            $path = public_path('pac.png');
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            ?>
            <img src="<?php echo $base64?>" width="60" height="70" />


        </td>
        <td>

            <b style="font-size:18px">CV PACIFIC</b><br>
            <div style="font-size:14px; font-family:Tahoma">
                Jln Perintis Kemerdekaan No. 106 Tasikmalaya
                Tlp. (0265) 330794, 337694. Fax (0265) 332329
                Emai: pacific.tasikmalaya@gmail.com
            </div>

        </td>

    </tr>
</table>
