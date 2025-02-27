<?php
	include '../../inc/tool.php';

$db = new DBConnect();
$dbh = $db->getConnection();

$com_cat_id = !empty($_POST['com_cat_id']) ? $_POST['com_cat_id'] : NULL;
if(empty($_POST['com_cat_order'])) {
	// com_cat_orderの最大値を取得
	$stmt_max = $dbh -> prepare("SELECT MAX(com_cat_order) AS max_order FROM common_genre");
	$stmt_max->execute();
	$max_order = $stmt_max->fetch(PDO::FETCH_ASSOC)['max_order'];

	// 最大値+1を新しいcom_cat_orderとして設定
	$com_cat_order = $max_order + 1;
} else {
	$com_cat_order = $_POST['com_cat_order'];
}
$com_cat_order = $_POST['com_cat_order'];
$item_create_date = (new DateTime())->format('Y-m-d H:i:s');
$item_update_date = (new DateTime())->format('Y-m-d H:i:s');
if($_POST['item_delete_flag']==1){
	$item_delete_date = (new DateTime())->format('Y-m-d H:i:s');
}
$item_delete_flag = !empty($_POST['item_delete_flag']) ? $_POST['item_delete_flag'] : 0;


if(empty($_POST['com_cat_id'])){
	$stmt = $dbh -> prepare("INSERT INTO common_cat(com_cat_id, com_cat, com_cat_order, item_create_date) VALUES (? ,? ,? ,?)");
	$data = []; //配列を初期化
	$data[] = $com_cat_id;
	$data[] = $com_cat;
	$data[] = $com_cat_order;
	$data[] = $item_create_date;
}else{
	$stmt = $dbh -> prepare("UPDATE common_cat SET com_cat=?, com_cat_order=?, item_update_date=? ,item_delete_date=? ,item_delete_flag=? WHERE com_cat_id=?");
	$data = []; //配列を初期化
	$data[] = $com_cat;
	$data[] = $com_cat_order;
	$data[] = $item_update_date;
	$data[] = $item_delete_date;
	$data[] = $item_delete_flag;
	$data[] = $com_cat_id;
}
$stmt -> execute($data);
$dbh = null;

$itemurl = ITEM_URL;

if(empty($_POST['com_cat_id'])){
$redirect_url = $itemurl."setting_cat.php";
}else{
	$redirect_url = $itemurl."setting_cat.php?id=".$com_cat_id;
}
header('Location: ' . $redirect_url);
?>