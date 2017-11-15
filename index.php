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
 * @copyright Copyright© Atelier UEDA 2016 - All rights reserved.
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
  <h1>2017年</h1>
    <a href="movies.php?serial_id=<?= $_GET['serial_id'] ?>&year=2017&month=10" rel="external">2017年10月</a>
    <h2>9月</h2>  
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20170900/video0.2017.09.00.mp4">
          </video>
          video0.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video0/20170900/video0.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20170900/video1.2017.09.00.mp4">
          </video>
          video1.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video1/20170900/video1.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20170900/video2.2017.09.00.mp4">
          </video>
          video2.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video2/20170900/video2.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20170900/video3.2017.09.00.mp4">
          </video>
          video3.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video3/20170900/video3.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20170900/video4.2017.09.00.mp4">
          </video>
          video4.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video4/20170900/video4.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video5/20170900/video5.2017.09.00.mp4">
          </video>
          video5.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20170900/video5.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video6/20170900/video6.2017.09.00.mp4">
          </video>
          video6.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video6/20170900/video6.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video7/20170900/video7.2017.09.00.mp4">
          </video>
          video7.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video7/20170900/video7.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video8/20170900/video8.2017.09.00.mp4">
          </video>
          video8.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video8/20170900/video8.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video9/20170900/video9.2017.09.00.mp4">
          </video>
          video9.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video9/20170900/video9.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video10/20170900/video10.2017.09.00.mp4">
          </video>
          video10.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video10/20170900/video10.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video11/20170900/video11.2017.09.00.mp4">
          </video>
          video11.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video11/20170900/video11.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video12/20170900/video12.2017.09.00.mp4">
          </video>
          video12.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video12/20170900/video12.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video13/20170900/video13.2017.09.00.mp4">
          </video>
          video13.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video13/20170900/video13.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video14/20170900/video14.2017.09.00.mp4">
          </video>
          video14.2017.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video14/20170900/video14.2017.09.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->

    <h2>8月</h2>  
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20170800/video0.2017.08.00.mp4">
          </video>
          video0.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video0/20170800/video0.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20170800/video1.2017.08.00.mp4">
          </video>
          video1.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video1/20170800/video1.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20170800/video2.2017.08.00.mp4">
          </video>
          video2.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video2/20170800/video2.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20170800/video3.2017.08.00.mp4">
          </video>
          video3.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video3/20170800/video3.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20170800/video4.2017.08.00.mp4">
          </video>
          video4.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video4/20170800/video4.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video5/20170800/video5.2017.08.00.mp4">
          </video>
          video5.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20170800/video5.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video6/20170800/video6.2017.08.00.mp4">
          </video>
          video6.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video6/20170800/video6.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video7/20170800/video7.2017.08.00.mp4">
          </video>
          video7.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video7/20170800/video7.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video8/20170800/video8.2017.08.00.mp4">
          </video>
          video8.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video8/20170800/video8.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video9/20170800/video9.2017.08.00.mp4">
          </video>
          video9.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video9/20170800/video9.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video10/20170800/video10.2017.08.00.mp4">
          </video>
          video10.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video10/20170800/video10.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video11/20170800/video11.2017.08.00.mp4">
          </video>
          video11.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video11/20170800/video11.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video12/20170800/video12.2017.08.00.mp4">
          </video>
          video12.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video12/20170800/video12.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video13/20170800/video13.2017.08.00.mp4">
          </video>
          video13.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video13/20170800/video13.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video14/20170800/video14.2017.08.00.mp4">
          </video>
          video14.2017.08<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video14/20170800/video14.2017.08.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->

  <h1>2017年</h1>
        <h2>7月</h2>  
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20170700/video0.2017.07.00.mp4">
          </video>
          video0.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video0/20170700/video0.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20170700/video1.2017.07.00.mp4">
          </video>
          video1.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video1/20170700/video1.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20170700/video2.2017.07.00.mp4">
          </video>
          video2.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video2/20170700/video2.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20170700/video3.2017.07.00.mp4">
          </video>
          video3.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video3/20170700/video3.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20170700/video4.2017.07.00.mp4">
          </video>
          video4.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video4/20170700/video4.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video5/20170700/video5.2017.07.00.mp4">
          </video>
          video5.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20170700/video5.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video6/20170700/video6.2017.07.00.mp4">
          </video>
          video6.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video6/20170700/video6.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video7/20170700/video7.2017.07.00.mp4">
          </video>
          video7.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video7/20170700/video7.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video8/20170700/video8.2017.07.00.mp4">
          </video>
          video8.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video8/20170700/video8.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video9/20170700/video9.2017.07.00.mp4">
          </video>
          video9.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video9/20170700/video9.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video10/20170700/video10.2017.07.00.mp4">
          </video>
          video10.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video10/20170700/video10.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video11/20170700/video11.2017.07.00.mp4">
          </video>
          video11.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video11/20170700/video11.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video12/20170700/video12.2017.07.00.mp4">
          </video>
          video12.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video12/20170700/video12.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video13/20170700/video13.2017.07.00.mp4">
          </video>
          video13.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video13/20170700/video13.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video14/20170700/video14.2017.07.00.mp4">
          </video>
          video14.2017.07<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video14/20170700/video14.2017.07.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->

      <h2>6月</h2>  
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20170600/video0.2017.06.00.mp4">
          </video>
          video0.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video0/20170600/video0.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20170600/video1.2017.06.00.mp4">
          </video>
          video1.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video1/20170600/video1.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20170600/video2.2017.06.00.mp4">
          </video>
          video2.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video2/20170600/video2.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20170600/video3.2017.06.00.mp4">
          </video>
          video3.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video3/20170600/video3.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20170600/video4.2017.06.00.mp4">
          </video>
          video4.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video4/20170600/video4.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video5/20170600/video5.2017.06.00.mp4">
          </video>
          video5.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20170600/video5.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video6/20170600/video6.2017.06.00.mp4">
          </video>
          video6.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video6/20170600/video6.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video7/20170600/video7.2017.06.00.mp4">
          </video>
          video7.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video7/20170600/video7.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video8/20170600/video8.2017.06.00.mp4">
          </video>
          video8.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video8/20170600/video8.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video9/20170600/video9.2017.06.00.mp4">
          </video>
          video9.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video9/20170600/video9.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video10/20170600/video10.2017.06.00.mp4">
          </video>
          video10.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video10/20170600/video10.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video11/20170600/video11.2017.06.00.mp4">
          </video>
          video11.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video11/20170600/video11.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video12/20170600/video12.2017.06.00.mp4">
          </video>
          video12.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video12/20170600/video12.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video13/20170600/video13.2017.06.00.mp4">
          </video>
          video13.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video13/20170600/video13.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video14/20170600/video14.2017.06.00.mp4">
          </video>
          video14.2017.06<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video14/20170600/video14.2017.06.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->

      <h2>5月</h2>  
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20170500/video0.2017.05.00.mp4">
          </video>
          video0.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video0/20170500/video0.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20170500/video1.2017.05.00.mp4">
          </video>
          video1.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video1/20170500/video1.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20170500/video2.2017.05.00.mp4">
          </video>
          video2.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video2/20170500/video2.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20170500/video3.2017.05.00.mp4">
          </video>
          video3.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video3/20170500/video3.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20170500/video4.2017.05.00.mp4">
          </video>
          video4.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video4/20170500/video4.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video5/20170500/video5.2017.05.00.mp4">
          </video>
          video5.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20170500/video5.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video6/20170500/video6.2017.05.00.mp4">
          </video>
          video6.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video6/20170500/video6.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video7/20170500/video7.2017.05.00.mp4">
          </video>
          video7.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video7/20170500/video7.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video8/20170500/video8.2017.05.00.mp4">
          </video>
          video8.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video8/20170500/video8.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video9/20170500/video9.2017.05.00.mp4">
          </video>
          video9.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video9/20170500/video9.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video10/20170500/video10.2017.05.00.mp4">
          </video>
          video10.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video10/20170500/video10.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video11/20170500/video11.2017.05.00.mp4">
          </video>
          video11.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video11/20170500/video11.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video12/20170500/video12.2017.05.00.mp4">
          </video>
          video12.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video12/20170500/video12.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video13/20170500/video13.2017.05.00.mp4">
          </video>
          video13.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video13/20170500/video13.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video14/20170500/video14.2017.05.00.mp4">
          </video>
          video14.2017.05<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video14/20170500/video14.2017.05.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->

      <h2>4月</h2>  
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20170400/video0.2017.04.00.mp4">
          </video>
          video0.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video0/20170400/video0.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20170400/video1.2017.04.00.mp4">
          </video>
          video1.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video1/20170400/video1.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20170400/video2.2017.04.00.mp4">
          </video>
          video2.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video2/20170400/video2.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20170400/video3.2017.04.00.mp4">
          </video>
          video3.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video3/20170400/video3.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20170400/video4.2017.04.00.mp4">
          </video>
          video4.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video4/20170400/video4.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video5/20170400/video5.2017.04.00.mp4">
          </video>
          video5.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20170400/video5.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video6/20170400/video6.2017.04.00.mp4">
          </video>
          video6.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video6/20170400/video6.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video7/20170400/video7.2017.04.00.mp4">
          </video>
          video7.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video7/20170400/video7.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video8/20170400/video8.2017.04.00.mp4">
          </video>
          video8.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video8/20170400/video8.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video9/20170400/video9.2017.04.00.mp4">
          </video>
          video9.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video9/20170400/video9.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video10/20170400/video10.2017.04.00.mp4">
          </video>
          video10.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video10/20170400/video10.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video11/20170400/video11.2017.04.00.mp4">
          </video>
          video11.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video11/20170400/video11.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video12/20170400/video12.2017.04.00.mp4">
          </video>
          video12.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video12/20170400/video12.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video13/20170400/video13.2017.04.00.mp4">
          </video>
          video13.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video13/20170400/video13.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video14/20170400/video14.2017.04.00.mp4">
          </video>
          video14.2017.04<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video14/20170400/video14.2017.04.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->

      <h2>3月</h2>  
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20170300/video0.2017.03.00.mp4">
          </video>
          video0.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video0/20170300/video0.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20170300/video1.2017.03.00.mp4">
          </video>
          video1.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video1/20170300/video1.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20170300/video2.2017.03.00.mp4">
          </video>
          video2.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video2/20170300/video2.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20170300/video3.2017.03.00.mp4">
          </video>
          video3.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video3/20170300/video3.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20170300/video4.2017.03.00.mp4">
          </video>
          video4.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video4/20170300/video4.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video5/20170300/video5.2017.03.00.mp4">
          </video>
          video5.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20170300/video5.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video6/20170300/video6.2017.03.00.mp4">
          </video>
          video6.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video6/20170300/video6.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video7/20170300/video7.2017.03.00.mp4">
          </video>
          video7.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video7/20170300/video7.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video8/20170300/video8.2017.03.00.mp4">
          </video>
          video8.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video8/20170300/video8.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video9/20170300/video9.2017.03.00.mp4">
          </video>
          video9.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video9/20170300/video9.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video10/20170300/video10.2017.03.00.mp4">
          </video>
          video10.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video10/20170300/video10.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video11/20170300/video11.2017.03.00.mp4">
          </video>
          video11.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video11/20170300/video11.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video12/20170300/video12.2017.03.00.mp4">
          </video>
          video12.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video12/20170300/video12.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video13/20170300/video13.2017.03.00.mp4">
          </video>
          video13.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video13/20170300/video13.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video14/20170300/video14.2017.03.00.mp4">
          </video>
          video14.2017.03<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video14/20170300/video14.2017.03.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->

      <h2>2月</h2>  
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20170200/video0.2017.02.00.mp4">
          </video>
          video0.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video0/20170200/video0.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20170200/video1.2017.02.00.mp4">
          </video>
          video1.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video1/20170200/video1.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20170200/video2.2017.02.00.mp4">
          </video>
          video2.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video2/20170200/video2.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20170200/video3.2017.02.00.mp4">
          </video>
          video3.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video3/20170200/video3.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20170200/video4.2017.02.00.mp4">
          </video>
          video4.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video4/20170200/video4.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video5/20170200/video5.2017.02.00.mp4">
          </video>
          video5.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20170200/video5.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video6/20170200/video6.2017.02.00.mp4">
          </video>
          video6.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video6/20170200/video6.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video7/20170200/video7.2017.02.00.mp4">
          </video>
          video7.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video7/20170200/video7.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video8/20170200/video8.2017.02.00.mp4">
          </video>
          video8.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video8/20170200/video8.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video9/20170200/video9.2017.02.00.mp4">
          </video>
          video9.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video9/20170200/video9.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video10/20170200/video10.2017.02.00.mp4">
          </video>
          video10.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video10/20170200/video10.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video11/20170200/video11.2017.02.00.mp4">
          </video>
          video11.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video11/20170200/video11.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video12/20170200/video12.2017.02.00.mp4">
          </video>
          video12.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video12/20170200/video12.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video13/20170200/video13.2017.02.00.mp4">
          </video>
          video13.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video13/20170200/video13.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video14/20170200/video14.2017.02.00.mp4">
          </video>
          video14.2017.02<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video14/20170200/video14.2017.02.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->

    <h2>1月</h2>  
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20170100/video0.2017.01.00.mp4">
          </video>
          video0.2017.01<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video0/20170100/video0.2017.01.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20170100/video1.2017.01.00.mp4">
          </video>
          video1.2017.01<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video1/20170100/video1.2017.01.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20170100/video2.2017.01.00.mp4">
          </video>
          video2.2017.01<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video2/20170100/video2.2017.01.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20170100/video3.2017.01.00.mp4">
          </video>
          video3.2017.01<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video3/20170100/video3.2017.01.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20170100/video4.2017.01.00.mp4">
          </video>
          video4.2017.01<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video4/20170100/video4.2017.01.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video5/20170100/video5.2017.01.00.mp4">
          </video>
          video5.2017.01<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20170100/video5.2017.01.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video6/20170100/video6.2017.01.00.mp4">
          </video>
          video6.2017.01<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video6/20170100/video6.2017.01.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video7/20170100/video7.2017.01.00.mp4">
          </video>
          video7.2017.01<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video7/20170100/video7.2017.01.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video8/20170100/video8.2017.01.00.mp4">
          </video>
          video8.2017.01<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video8/20170100/video8.2017.01.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video9/20170100/video9.2017.01.00.mp4">
          </video>
          video9.2017.01<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video9/20170100/video9.2017.01.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video10/20170100/video10.2017.01.00.mp4">
          </video>
          video10.2017.01<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video10/20170100/video10.2017.01.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->
  <h1>2016年</h1>
    <h2>12月</h2>  
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20161200/video0.2016.12.00.mp4">
          </video>
          video0.2016.12<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video0/20161200/video0.2016.12.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20161200/video1.2016.12.00.mp4">
          </video>
          video1.2016.12<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video1/20161200/video1.2016.12.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20161200/video2.2016.12.00.mp4">
          </video>
          video2.2016.12<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video2/20161200/video2.2016.12.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20161200/video3.2016.12.00.mp4">
          </video>
          video3.2016.12<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video3/20161200/video3.2016.12.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20161200/video4.2016.12.00.mp4">
          </video>
          video4.2016.12<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video4/20161200/video4.2016.12.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video5/20161200/video5.2016.12.00.mp4">
          </video>
          video5.2016.12<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161200/video5.2016.12.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video6/20161200/video6.2016.12.00.mp4">
          </video>
          video6.2016.12<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video6/20161200/video6.2016.12.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video7/20161200/video7.2016.12.00.mp4">
          </video>
          video7.2016.12<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video7/20161200/video7.2016.12.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video8/20161200/video8.2016.12.00.mp4">
          </video>
          video8.2016.12<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video8/20161200/video8.2016.12.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->

    <h2>11月</h2>
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20161100/video0.2016.11.00.mp4">
          </video>
          video0.2016.11<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20161100/video1.2016.11.00.mp4">
          </video>
          video1.2016.11<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20161100/video2.2016.11.00.mp4">
          </video>
          video2.2016.11<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20161100/video3.2016.11.00.mp4">
          </video>
          video3.2016.11<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20161100/video4.2016.11.00.mp4">
          </video>
          video4.2016.11<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video5/20161100/video5.2016.11.00.mp4">
          </video>
          video5.2016.11<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video6/20161100/video6.2016.11.00.mp4">
          </video>
          video6.2016.11<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video6/20161100/video6.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video7/20161100/video7.2016.11.00.mp4">
          </video>
          video7.2016.11<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video7/20161100/video7.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video8/20161100/video8.2016.11.00.mp4">
          </video>
          video8.2016.11<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video8/20161100/video8.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->

    <h2>10月</h2>
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20161000/video0.2016.10.00.mp4">
          </video>
          video0.2016.10<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20161000/video1.2016.10.00.mp4">
          </video>
          video1.2016.10<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20161000/video2.2016.10.00.mp4">
          </video>
          video2.2016.10<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20161000/video3.2016.10.00.mp4">
          </video>
          video3.2016.10<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20161000/video4.2016.10.00.mp4">
          </video>
          video4.2016.10<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->

    <h2>9月</h2>  
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video0/20160900/video0.2016.09.00.mp4">
          </video>
          video0.2016.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video1/20160900/video1.2016.09.00.mp4">
          </video>
          video1.2016.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video2/20160900/video2.2016.09.00.mp4">
          </video>
          video2.2016.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video3/20160900/video3.2016.09.00.mp4">
          </video>
          video3.2016.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
          <video controls>
            <source src="uploads/00000000c9c51a68/video4/20160900/video4.2016.09.00.mp4">
          </video>
          video4.2016.09<a href="./download.php?serial_id=<?= $_GET['serial_id']; ?>&name=/video5/20161100/video5.2016.11.00.mp4" rel="external">ダウンロード</a>
        </div>
      </div><!-- <div class="row"> -->
  </div><!-- <div data-role="tabs"> -->
</div>

<div data-role="footer" data-position="fixed" data-disable-page-zoom="false">
    <h4>© Atelier UEDA🐸</h4>
</div>
</div> <!-- page -->

</body>
</html>
