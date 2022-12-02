<?php
    function myfunction($num)
    {
        return($num*1);
    }
    $no = 1;
    foreach ($detail as $d) {
        $jumlah = array_map("myfunction",array($d->januari,$d->februari,$d->maret,$d->april,$d->mei,$d->juni,$d->juli,$d->agustus,$d->september,$d->oktober,$d->november,$d->desember));
        $produk[] = array(
                    'name' => $d->kode_produk,
                    'data' => $jumlah);
    }

 // echo json_encode($produk);
 ?>
<div id="grafik">

</div>

<script>
    var produk = <?php echo json_encode($produk) ?> ;
    Highcharts.chart('grafik', {

        title: {
            text: 'GRAFIK HASIL PRODUKSI'
        },


        yAxis: {
            title: {
                text: 'Jumlah Hasil Produksi'
            }
        },

        xAxis: {
            accessibility: {
                rangeDescription: 'Range: 2010 to 2017'
            }
            , categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },

        legend: {
            layout: 'vertical'
            , align: 'right'
            , verticalAlign: 'middle'
        },



        series: produk,

        responsive: {
            rules: [{
                condition: {
                    maxWidth: 600
                }
                , chartOptions: {
                    legend: {
                        layout: 'horizontal'
                        , align: 'center'
                        , verticalAlign: 'bottom'
                    }
                }
            }]
        }

    });

</script>
