<?php
	// 1.データベースに接続
	// mysql:dbname=接続するDB名;host=パソコンのアドレス
	// 空欄を入れてはいけないルール
	$dsn = 'mysql:dbname=seed_sns;host=localhost';
	// XAMPP環境下ではユーザー名はroot、パスワードは空
	$user = 'root';
	$password = '';
	// このプログラムが存在している場所と同じサーバーを指定
	$dbh = new PDO($dsn, $user, $password);
	$dbh->query('SET NAMES utf8');
?>