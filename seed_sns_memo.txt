リファクタリングとは
コンピュータプログラミングにおいて、プログラムの外部から見た動作を変えずにソースコードの内部構造を整理することである。
また、いくつかのリファクタリング手法の総称としても使われる。

<!-- 画像を送る際の注意点 -->
①enctype="multipart/form-data" は画像ファイルを送る際に必要
②inputタグのtype="file" にする

move_uploaded_file()
move_uploaded_file = 画像を指定したディレクトリに保存(アップロード)する
move_uploaded_file(ファイル名, 保存先のディレクトリの位置)
注意！！！ パーミッションを確認し、StaffとEveryoneを「Read&Write」に書き換えましょう。
$_FILES['picture_path']['tmp_name'] 一時的に保存される場所 = XAMPPの中の場合はxamppfiles/tempの中

session_start()
$_SESSIONを使用する際に必要。

パスワードハッシュ生成(暗号化)
sha1より複雑な暗号化
ソルト – パスワードの解読を困難にするために、元のパスワードテキストに結合する文字列を指定します。任意の文字列を指定できますが、php7.0以降では値の指定が非推奨となり、password_hash関数が自動的に生成するソルトを使用することが推奨されている。。
コスト – 暗号化にかけるコスト（計算回数)を指定します。初期設定は10で、4〜31の間で指定することが可能です。サーバーのハードウェアが許す範囲で、10以上の値を指定することが推奨されています。許容できる範囲で最も大きい値を指定する。

例
$options = array('salt' => mcrypt_create_iv(22, MCRYPT_DEV_RANDOM), 'cost' => 12);
$hayato = password_hash('hayato', PASSWORD_DEFAULT);
↑ デフォルトで第三引数に入るのがpassword_hash関数が自動的に生成するソルトを使用することになっている