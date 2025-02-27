<?php
	//共通カテゴリ取得
	function common_cat() {
    // グローバル変数として $dbh を使えるようにする
    global $dbh;

    // SQLクエリ
    $sql = "SELECT CC.* FROM common_cat AS CC WHERE 1";
    
    // クエリを準備
    $com_cat = $dbh->prepare($sql);
    
    // クエリを実行
    $com_cat->execute();
    
    // 結果を全て取得
    $result = $com_cat->fetchAll(PDO::FETCH_ASSOC);
    
    // 結果を返す
    return $result;
	}
?>