<?php
class DBConnect {
	private $dbh;

	public function __construct() {
		// PDO 接続情報の設定
		$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
		$user = DB_USER;
		$password = DB_PASSWORD;

		try {
			// PDO インスタンスの作成
			$this->dbh = new PDO($dsn, $user, $password);
			// エラーモードの設定
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			// 接続エラー時の処理
			echo "接続に失敗しました: " . $e->getMessage();
			exit;
		}
	}

	// DB 接続を返すメソッド
	public function getConnection() {
		return $this->dbh;
	}
}
?>