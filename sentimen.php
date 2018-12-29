<?php
include 'view/header.php';
if(isset($_POST['submit'])){
    $donutData = '';
    $barData = '';

    $key = $_POST['key'];
    $total = $db->query("SELECT label FROM tweets WHERE hastag = '$key'")->count();
    $labels = $db->query("SELECT label FROM tweets WHERE hastag = '$key' GROUP BY label")->get();
    foreach($labels as $label){
        $label = $label['label'];
        $datas[$label] = number_format(($db->query("SELECT label FROM tweets WHERE hastag = '$key' AND label = '$label'")->count() / $total) * 100, 2);
    }
    foreach($datas as $index => $val){
        if($index == 'positif'){
            $warna = '#00a65a';
        }elseif($index == 'negatif'){
            $warna = '#dd4b39';
        }elseif($index == 'netral'){
            $warna = '#00c0ef';
        }
        $donutData .= "{ label: '$index', data: $val, color: '$warna' },\n";
        $barData .= "['$index', $val],";
    }
}
$db->clear();
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content container-fluid">
      
      <div class="row">

          <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Pilih Data</h3>
                </div>
              
              <div class="box-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file">Kata Kunci ( hastag ) </label>
                        <select class="form-control" name="key">
                            <?php foreach($db->query("SELECT hastag FROM `tweets` GROUP BY hastag")->get() as $hastag):?>
                                <option value="<?= $hastag['hastag'] ?>"> <?= $hastag['hastag'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Lihat Hasil" name="submit" class="btn btn-primary">
                    </div>
                </form>
              </div>
            </div>
            
          </div>
    <?php if(isset($key)): ?>
        <!-- ROW 6 -->
        <div class="col-md-6">
            <!-- Donut chart -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <i class="fa fa-bar-chart-o"></i>

              <h3 class="box-title">Hasil Analisis : <?= $key ?> </h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div id="donut-chart" style="height: 300px;"></div>
              <p>Jumlah Tweet : <?= $total ?></p>
            </div>
            <!-- /.box-body-->
          </div>
          <!-- /.box -->
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                <i class="fa fa-bar-chart-o"></i>
                <h3 class="box-title">Hasil Analisis : <?= $key ?> </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
                </div>
                <div class="box-body">
                    <div id="bar-chart" style="height: 300px;"></div>
                    <p>Jumlah Tweet : <?= $total ?></p>
                </div>
                <!-- /.box-body-->
            </div>
        </div>
    <?php endif; ?>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php
if(isset($key)):
setJs("
/*
* BAR CHART
* ---------
*/

var bar_data = {
 data : [$barData],
 color: '#3c8dbc'
}
$.plot('#bar-chart', [bar_data], {
 grid  : {
   borderWidth: 1,
   borderColor: '#f3f3f3',
   tickColor  : '#f3f3f3'
 },
 series: {
   bars: {
     show    : true,
     barWidth: 0.5,
     align   : 'center'
   }
 },
 xaxis : {
   mode      : 'categories',
   tickLength: 0
 }
})
/* END BAR CHART */

/*
* DONUT CHART
* -----------
*/

var donutData = [
    $donutData
]
$.plot('#donut-chart', donutData, {
 series: {
   pie: {
     show       : true,
     radius     : 1,
     innerRadius: 0.5,
     label      : {
       show     : true,
       radius   : 2 / 3,
       formatter: labelFormatter,
       threshold: 0.1
     }

   }
 },
 legend: {
   show: false
 }
})
/*
* END DONUT CHART
*/
  function labelFormatter(label, series) {
    return \"<div style='font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;'>\"
      + label
      + '<br>'
      + Math.round(series.percent) + '%</div>'
  }
");
endif;
include 'view/footer.php';
?>