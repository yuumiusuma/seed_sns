<?php
	require('dbconnect.php');
	require('function.php');

	// いいね・良くないねーボタンが押された時
	if (!empty($_GET)) {
		if (isset($_GET['like_tweet_id'])) {
      like();
			// いいねのデータを作成するsql文
			// $like_sql = 'INSERT INTO `likes` SET `member_id`=?, `tweet_id`=?';
			// $like_data = array($_SESSION['id'], $_GET['like_tweet_id']);
			// $like_stmt = $dbh->prepare($like_sql);
			// $like_stmt->execute($like_data);

			// header('Location: index.php');
			// exit();
		}

		if (isset($_GET['unlike_tweet_id'])) {
      unlike();
			// いいねのデータを削除するsql文
			// $unlike_sql = 'DELETE FROM `likes` WHERE `member_id`=? AND `tweet_id`=?';
			// $unlike_data = array($_SESSION['id'], $_GET['unlike_tweet_id']);
			// $unlike_stmt = $dbh->prepare($unlike_sql);
			// $unlike_stmt->execute($unlike_data);

			// header('Location: index.php');
			// exit();
		}
	}


?>








