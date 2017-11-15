<?php
/**
 * Main application of Monitor.
 * 
 * Main application of Monitor.
 * 1. Confirm login
 * 2. Retrun application skelton and js apps which get fresh data and pics and refresh page.
 * 
 * Requires $_GET['serial_id']
 * 
 * @author Dr. Takeyuki UEDA
 * @copyright Copyright© Atelier UEDA 2017 - All rights reserved.
 *
 */
 
 date_default_timezone_set("Asia/Tokyo");
  session_start();

  require_once("common.php");
  require_once("vendor/autoload.php"); 
  #require_once("Log.php");
  $logfile = &Log::factory('file', 'index.out.log', 'Tls EST'); 
  $logfile->log('['.__LINE__.']'.'*** STARTED ***');

  // エラー出力しない場合
  //ini_set( 'display_errors', 0 );
  // エラー出力する場合
  //ini_set( 'display_errors', 1 );

  # 設定の読み込み
  $configfile = "uploads/".$_GET['serial_id']."/config.ini";
  $ini = parse_ini_file($configfile);

  // ログイン状態のチェック
#  if (!isset($_SESSION["USERID"]) || !isset($_SESSION["serial_id"]) || $_SESSION["serial_id"] != $_GET['serial_id']) {
  if (!isset($_SESSION["LOGINS"]) || !array_key_exists($_GET['serial_id'], $_SESSION["LOGINS"])) {
    // ログイン成功後の戻り先(パラメタ付き)をセッション変数に保存
    $_SESSION["return_url"]=$_SERVER["REQUEST_URI"];
    $logfile->log('['.__LINE__.']'.'$_SESSION["return_url"] = '.$_SESSION["return_url"]);
    // ログイン処理
    header("Location: login.php?serial_id=".$_GET['serial_id']);
    exit;
  }

  // データの設定ファイル一覧を取得
  $data_inis = glob("uploads/".$_GET['serial_id']."/*.dini");

  // check parameter
  if (!isset($_GET['year'])||!isset($_GET['month'])){exit;}
  $year  = $_GET['year'];
  $logfile->log('['.__LINE__.']'.'$year = '.$year);
  $month = $_GET['month'];
  $logfile->log('['.__LINE__.']'.'$month = '.$month);

  // get videos
  $videos = glob("uploads/".$_GET['serial_id']."/video*");

?>

<!DOCTYPE html>
<html lang="ja" id="demo">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

 <title><?=TITLE?></title>
<?php   if ($cdn): ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.5/Chart.min.js"></script>
  <link rel="stylesheet" href="https://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
  <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
  <script src="https://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>

  <!-- VUE start -->
  <script src='https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.10/vue.js'></script>
  <!-- VUE end -->

  <!-- BOOTSTRAP start -->
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
  <!-- BOOTSTRAP end -->
<?php   else: ?>
  <script src="node_modules/chart.js/node_modules/moment/min/moment.min.js"></script>
  <script src="node_modules/chart.js/dist/Chart.min.js"></script>
  <link rel="stylesheet" href="js/jquery.mobile-1.3.1.min.css" />
  <script src="node_modules/jquery/dist/jquery.min.js"></script>
  <script src="js/jquery.mobile-1.3.1.min.js"></script>

  <!-- VUE start -->
  <script src='node_modules/vue/dist/vue.js'></script>
  <!-- VUE end -->

  <!-- BOOTSTRAP start -->
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <!-- Optional theme -->
  <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <!-- Latest compiled and minified JavaScript -->
  <script src="node_modules/bootstrap/dist/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
  <!-- BOOTSTRAP end -->
<?php   endif; ?>

</head>
<body>

<!-- <div data-role="page" id="demo"> -->
<div data-role="page">
    
<div data-role="header" data-position="fixed" data-disable-page-zoom="false">

<?php   if (isset($ini["title"])): ?>
  <h1><?php echo $ini["title"]?></h1>
<?php   else: ?>
  <h1><?php echo TITLE?></h1>
<?php   endif; ?>

  <a data-role="button" data-inline="true" href="config.php?serial_id=<?php echo $_GET['serial_id']; ?>" data-icon="gear" data-transition="fade" data-ajax="false">設定変更</a>
  <div class="ui-btn-right">
    <a data-role="button" data-inline="true" href="logout.php?serial_id=<?php echo $_GET['serial_id']?>" data-transition="fade" data-ajax="false">ログアウト</a>
  </div>
</div>

<div data-role="content" id="tab1">
  <h1><?= $year ?>年</h1>
    <h2><?= $month ?>月</h2>
      <div class="row">
        <?php foreach ($videos as $path): ?>
          <?php if (file_exists($path."/".$year.$month."00")): ?>
            <?php $device = basename($path); ?>
            <div class="col-md-4 col-sm-6 col-xs-12">
              <video controls>
                <source src="uploads/<?=$_GET['serial_id']?>/<?=$device?>/<?=$year?><?=$month?>00/<?=$device?>.<?=$year?>.<?=$month?>.00.mp4">
              </video>
              <?=$device?>.<?=$year?>.<?=$month?><a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/<?=$device?>/<?=$year?><?=$month?>00/<?=$device?>.<?=$year?>.<?=$month?>.00.mp4" rel="external">ダウンロード</a>
            </div>
          <?php endif ?>
        <?php endforeach ?>
      </div><!-- <div class="row"> -->

  </div><!-- <div data-role="tabs"> -->
</div>

<div data-role="footer" data-position="fixed" data-disable-page-zoom="false">
    <h4>© Atelier UEDA🐸</h4>
</div>
</div> <!-- page -->

</body>
</html>
