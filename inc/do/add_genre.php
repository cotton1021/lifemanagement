<?php
	include '../../inc/tool.php';

$db = new DBConnect();
$dbh = $db->getConnection();

$com_genre_id = !empty($_POST['com_genre_id']) ? $_POST['com_genre_id'] : NULL;
$com_genre = $_POST['com_genre'];
if(empty($_POST['com_genre_order'])) {
	// com_genre_orderの最大値を取得
	$stmt_max = $dbh -> prepare("SELECT MAX(com_genre_order) AS max_order FROM common_genre");
	$stmt_max->execute();
	$max_order = $stmt_max->fetch(PDO::FETCH_ASSOC)['max_order'];

	// 最大値+1を新しいcom_genre_orderとして設定
	$com_genre_order = $max_order + 1;
} else {
	$com_genre_order = $_POST['com_genre_order'];
}
$item_create_date = (new DateTime())->format('Y-m-d H:i:s');
$item_update_date = (new DateTime())->format('Y-m-d H:i:s');
if($_POST['item_delete_flag']==1){
	$item_delete_date = (new DateTime())->format('Y-m-d H:i:s');
}
$item_delete_flag = !empty($_POST['item_delete_flag']) ? $_POST['item_delete_flag'] : 0;

if(empty($_POST['com_genre_id'])){
	$stmt = $dbh -> prepare("INSERT INTO common_genre(com_genre_id, com_genre, com_genre_order, item_create_date) VALUES (? ,? ,? ,?)");
	$data = []; //配列を初期化
	$data[] = $com_genre_id;
	$data[] = $com_genre;
	$data[] = $com_genre_order;
	$data[] = $item_create_date;
}else{
	$stmt = $dbh -> prepare("UPDATE common_genre SET com_genre=?, com_genre_order=?, item_update_date=? ,item_delete_date=? ,item_delete_flag=? WHERE com_genre_id=?");
	$data = []; //配列を初期化
	$data[] = $com_genre;
	$data[] = $com_genre_order;
	$data[] = $item_update_date;
	$data[] = $item_delete_date;
	$data[] = $item_delete_flag;
	$data[] = $com_genre_id;
}
$stmt -> execute($data);
$dbh = null;

$itemurl = ITEM_URL;

if(empty($_POST['com_genre_id'])){
$redirect_url = $itemurl."setting_genre.php";
}else{
	$redirect_url = $itemurl."setting_genre.php?id=".$com_genre_id;
}
header('Location: ' . $redirect_url);
?>