<?php 
echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';
  session_start();
  require('dbconnect.php');

  $a_sql='SELECT COUNT(*) AS `count` FROM `likes` WHERE `member_id`=? AND `tweet_id`=?';
  $a_data=array($_SESSION['id'],$_GET['tweet_id']);
  $a_stmt=$dbh->prepare($a_sql);
  $a_stmt->execute($a_data);
  $count=$a_stmt->fetch(PDO::FETCH_ASSOC);
  var_dump($count);

if($count['count']==0){
  if(isset($_GET['tweet_id'])&&$_GET['action']=='like'){
    $sql='INSERT INTO `likes` SET `member_id`=?,`tweet_id`=?';
    $data=array($_SESSION['id'],$_GET['tweet_id']);
    $stmt=$dbh->prepare($sql);
    $stmt->execute($data);
  }
}else{
    if(isset($_GET['tweet_id'])&&$_GET['action']=='dislike'){
    $dislike_sql='DELETE FROM `likes` WHERE `member_id`=? AND `tweet_id`=?';
    $dislike_data=array($_SESSION['id'],$_GET['tweet_id']);
    $dislike_stmt=$dbh->prepare($dislike_sql);
    $dislike_stmt->execute($dislike_data);
      }
}
header('Location: index.php');
exit;

  // var_dump($data);
 ?>