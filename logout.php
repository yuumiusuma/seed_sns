<?php
	// セッション使用時に必須
	session_start();

	// セッションの中身を空の配列で上書き
	$_SESSION = array();

	// セッションの情報を有効期限切れにする
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}

	// セッションの情報を破棄
	// session_destroy()
	// 現在のセッションに 関連づけられたすべてのデータを破棄します。
	// セッションに関するグローバル変数とセッションクッキーを破棄しないため、上で上書きする必要がある。 セッション変数の利用を再開するには session_start() をコールする必要がある。

	session_destroy();

	// cookieの情報も削除
	setcookie('email', '', time() - 3000);
    setcookie('password', '', time() - 3000);

	// ログイン後の画面に戻る
	header('Location: index.php');
	exit;

	// ログイン後の画面に行くことによってしっかりログアウト機能が実装されているか確認するため
?>



















