<?php
include 'view/header.php';
if (isset($_POST['submit'])) {
    $ok = 0;
    $tidakOk = 0;
    $notIn = 0;
    $search = $_POST['key'];
    
    $results = $twitter->search(['count' => 30, 'q' => urlencode($search), "
                        result_type" => "recent", 'tweet_mode' => 'extended',
                        'locale' => 'id']);
    foreach ($results as $tweet) {
        $string = $tweet->full_text;
        
        //cleasing
        $text_clean = new Clean($string);
        $text_clean = $text_clean->toString();
        
        //stem
        $text_stem   = $stemmer->stem($text_clean);
        if (empty($text_stem)) {
            $notIn++;
            break;
        }
        // calculations:
        $scores = $sentiment->score($text_stem);
        $class = $sentiment->categorise($text_stem);
        
        if ($db->query("SELECT id FROM tweets WHERE id_tweet = '$tweet->id'")->count() == 0) {
            $data = [
                "id_tweet" => $tweet->id,
                "text_dirty" => $string,
                "text_clean" => $text_clean,
                "text_steam" => $text_stem,
                "label" => $class,
                "hastag" => $search
            ];
            
            if ($db->insert("tweets", $data)) {
                $ok++;
            }
        } else {
            $tidakOk++;
        }
    }
}

$page = (isset($_GET['page'])) ? $_GET['page'] : 1;

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content container-fluid">
      
      <div class="row">

          <div class="col-md-12">
            <?php if (isset($search)): ?>
              <div class="alert alert-success"> <?= $ok ?> Tweet baru dimasukan, <?= $tidakOk ?> sudah ada , <?= $notIn ?> kosong </div>
            <?php endif;?>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Ambil Data</h3>
                </div>
              
              <div class="box-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file">Kata Kunci ( hastag ) </label>
                        <input type="text" name="key" id="" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="ambil" name="submit" class="btn btn-primary">
                    </div>
                </form>
              </div>
            </div>
            
          </div>

        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Daftar Barang</h3>
              
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <p>Total record :  <?= $db->tweets->count() ?></p>
              <table class="table table-bordered">
                <tbody><tr>
                  <th>Id Tweet</th>
                  <th>Tweet</th>
                  <th>Text Clean</th>
                  <th>Text Stem</th>
                  <th>Label</th>
                  <th>Kunci</th>
                </tr>
                <?php foreach ($db->tweets->paginate(15, $page, ['id' => 'DESC']) as $tweet):  ?>
                    <tr>
                        <td><?= $tweet['id_tweet'] ?></td>
                        <td><?= $tweet['text_dirty'] ?></td>
                        <td><?= $tweet['text_clean'] ?></td>
                        <td><?= $tweet['text_steam'] ?></td>
                        <td> <span class="label label-<?php
                            if ($tweet['label'] == 'positif') {
                                echo 'success';
                            } elseif ($tweet['label'] == 'negatif') {
                                echo 'danger';
                            } elseif ($tweet['label'] == 'netral') {
                                echo 'info';
                            }
                        ?>"><?= $tweet['label'] ?></span> </td>
                        <td><?= $tweet['hastag'] ?></td>
                    </tr>
                <?php endforeach;?>
              </tbody></table>
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <?= $db->createLinks() ?>
            </div>
          </div>
        </div>
      </div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php
include 'view/footer.php';
?>