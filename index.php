<?php
  // function.phpを読み込み
  require('function.php');

  // ログイン
  login_check();

  // DBの接続
  require('dbconnect.php');

  // ログインチェックを上でしているため
  // 1時間ログインしていない場合、再度ログイン
  // if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  //   // ログインしている
  //   // ログイン時間の更新
  //   $_SESSION['time'] = time();

  // ログインユーザー情報取得
  $login_sql = 'SELECT * FROM `members` WHERE `member_id`=?';
  $login_data = array($_SESSION['id']);
  $login_stmt = $dbh->prepare($login_sql);
  $login_stmt->execute($login_data);
  $login_member = $login_stmt->fetch(PDO::FETCH_ASSOC);

  // } else {
  //   // ログインしていない、または時間切れの場合
  //   header('Location: login.php');
  //   exit;
  // }

  // つぶやくボタンが押された時
  if (!empty($_POST)) {

    // 入力チェック
    if ($_POST['tweet'] == '') {
      $error['tweet'] = 'blank';
    }

    if (!isset($error)) {
      // SQL文作成(INSERT INTO)
      // tweet=つぶやいた内容
      // member_id=ログインした人のid
      // reply_tweet_id=-1
      // created=現在日時。now()を使用
      // modified=現在日時。now()を使用

      $sql = 'INSERT INTO `tweets` SET `tweet`=?, `member_id`=?, `reply_tweet_id`=?, `created`=NOW(), `modified`=NOW()';
      $data = array($_POST['tweet'], $_SESSION['id'], -1);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);

    }
  }

  // ページング機能
  // 空の変数を用意
  $page = '';

  // パラメータが存在していたらページ番号を代入
  if (isset($_GET['page'])) {
    $page = $_GET['page'];
  } else {
    $page = 1;
  }

  // 1以下のイレギュラーな数字が入ってきた時、ページ番号を強制的に1とする
  // max カンマ区切りで羅列された数字の中から最大の数字を取得する
  $page = max($page, 1);

  // 1ページ分の表示件数を指定
  $page_number = 5;

  // データの件数から最大ページを計算する
  // SQLで計算するデータを取得
  $page_sql = 'SELECT COUNT(*) AS `page_count` FROM `tweets` WHERE `delete_flag`=0';
  $page_stmt = $dbh->prepare($page_sql);
  $page_stmt->execute();

  // 全件取得(論理削除されていないもの)
  $page_count = $page_stmt->fetch(PDO::FETCH_ASSOC);

  // ceil 小数点切り上げ
  // 1~5 1ページ 6~10 2ページ...
  $all_page_number = ceil($page_count['page_count'] / $page_number);

  // パラメータのページ番号が最大ページを超えていれば、強制的に最後のページとする
  // min カンマ区切りで羅列された数字の中から最小の数字を取得する
  $page = min($page, $all_page_number);

  // 表示するデータの取得開始場所
  $start = ($page - 1) * $page_number;




  // 一覧用の投稿全件取得
  // テーブル結合
  // INNER JOIN と OUTER JOIN(left join と right join)
  // INNER JOIN = 両方のテーブルに存在するデータのみ取得
  // OUTER JOIN(left join と right join) = 複数のテーブルがあり、それらを結合する際に優先テーブルを一つ決め、そこにある情報は全て表示しながら、他のテーブルの情報に対になるデータがあれば表示する
  // 優先テーブルに指定されると、そのテーブルの情報を全て表示される
  // LIMIT = テーブルから取得する範囲を指定
  // LIMIT 取得する配列のキー,取得する数

  $tweet_sql = "SELECT `tweets`.*, `members`.`nick_name`, `members`.`picture_path` FROM `tweets` LEFT JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `delete_flag`=0 ORDER BY `tweets`.`modified` DESC LIMIT ".$start.",".$page_number."";
  $tweet_stmt = $dbh->prepare($tweet_sql);
  $tweet_stmt->execute();

  // 空の配列を用意
  $tweet_list = array(); // データがない時のエラーを防ぐ

  // 一覧用の投稿全件取得
  while (true) {
    $tweet = $tweet_stmt->fetch(PDO::FETCH_ASSOC);
    if ($tweet == false) {
      break;
    }
    // like数を取得するSQL文
    $like_sql = 'SELECT COUNT(*) AS `like_count` FROM `likes` WHERE `tweet_id`=?';
    $like_data = array($tweet['tweet_id']);
    $like_stmt = $dbh->prepare($like_sql);
    $like_stmt->execute($like_data);

    // 各投稿ごとのいいね数
    $like_count = $like_stmt->fetch(PDO::FETCH_ASSOC);

    // 一行分のデータに新しいキーを用意し、$like_countを代入
    $tweet['like_count'] = $like_count['like_count'];

    // ログインしている人がlikeしているかどうかのデータを取得
    $login_like_sql = 'SELECT COUNT(*) as `login_count` FROM `likes` WHERE `member_id`=? AND `tweet_id`=?';
    $login_like_data = array($_SESSION['id'], $tweet['tweet_id']);
    $login_like_stmt = $dbh->prepare($login_like_sql);
    $login_like_stmt->execute($login_like_data);

    // フェッチで取得
    $login_like_number = $login_like_stmt->fetch(PDO::FETCH_ASSOC);

    // echo '<br>';
    // echo '<br>';
    // echo '<pre>';
    // var_dump($login_like_number);
    // echo '</pre>';exit;

    // ログインしているユーザーがいいねしているかどうかの判定
    $tweet['login_like_flag'] = $login_like_number['login_count'];

    $tweet_list[] = $tweet;
  }

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<?php echo $login_member['nick_name']; ?>さん</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
                <?php if (isset($error) && $error['tweet'] == 'blank') { ?>
                    <p class="error">つぶやき内容を入力してください。</p>
                <?php } ?>
              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <!-- 最初のページの時、"前"のボタンを押せないようにする -->
                <?php if($page == 1) { ?>
                  <li>前</li>
                <?php } else { ?>
                  <li><a href="index.php?page=<?php echo $page -1; ?>" class="btn btn-default">前</a></li>
                <?php } ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <!-- 最後のページの時、"次"のボタンを押せないようにする -->
                <?php if($page == $all_page_number) { ?>
                  <li>次</li>
                <?php } else {  ?>
                  <li><a href="index.php?page=<?php echo $page +1; ?>" class="btn btn-default">次</a></li>
                <?php } ?>
                <!-- 現在のページ / 最大のページ -->
                <li><?php echo $page; ?> / <?php echo $all_page_number; ?></li>
          </ul>
        </form>
      </div>

      <div class="col-md-8 content-margin-top">
      <?php foreach($tweet_list as $one_tweet) { ?>
        <div class="msg">
          <img src="picture_path/<?php echo $one_tweet['picture_path']; ?>" width="48" height="48">
          <p>
            <?php echo $one_tweet['tweet']; ?><span class="name"> (<?php echo $one_tweet['nick_name']; ?>)</span>
            <?php if($_SESSION['id'] != $one_tweet['member_id']) { ?>
              [<a href="reply.php?tweet_id=<?php echo $one_tweet['tweet_id']; ?>">Re</a>]
            <?php } ?>
            <!-- いいねボタン -->
          
            <?php if ($one_tweet['login_like_flag'] == 0) { ?>
              <a href="like.php?like_tweet_id=<?php echo $one_tweet['tweet_id']; ?>"><i class="fa fa-thumbs-o-up"></i>いいね！</a>
            <?php } else { ?>
              <a href="like.php?unlike_tweet_id=<?php echo $one_tweet['tweet_id']; ?>"><i class="fa fa-thumbs-o-down"></i>よくないねー</a>
            <?php } ?>
          </p>
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $one_tweet['tweet_id']; ?>">
              <?php
              $modify_date = $one_tweet['modified'];
              // strtotime 文字型のデータを日時型に変換できる
              $modify_date = date("Y-m-d H:i", strtotime($modify_date));

              echo $modify_date;
              ?>
            </a>
            <?php if ($_SESSION['id'] == $one_tweet['member_id']) { ?>
            [<a href="edit.php?tweet_id=<?php echo $one_tweet['tweet_id']; ?>" style="color: #00994C;">編集</a>]
            [<a href="delete.php?tweet_id=<?php echo $one_tweet['tweet_id']; ?>" style="color: #F33;" onclick="return confirm('本当に削除しますか？');">削除</a>]
            <?php } ?>
            <?php if ($one_tweet['reply_tweet_id'] >= 1) { ?>
            <!-- 返信元のメッセージの詳細へ -->
            [<a href="view.php?tweet_id=<?php echo $one_tweet['reply_tweet_id']; ?>" style="color: #a9a9a9;">返信元のメッセージを表示</a>]
            <?php } ?>
          </p>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
