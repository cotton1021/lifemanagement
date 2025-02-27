<?php
	include '../../inc/tool.php';

$db = new DBConnect();
$dbh = $db->getConnection();

$item_select = $_POST['item_select'];

if($_POST['action'] === 'delete'){
	foreach($item_select as $id){
		$stmt = $dbh -> prepare("UPDATE common_cat SET item_delete_date=CURDATE(), item_delete_flag=1 WHERE com_cat_id=?");
		$stmt -> execute([$id]);
	}
}elseif($_POST['action'] === 'order_change'){
	$com_cat_order = $_POST['com_cat_order'];
	foreach ($com_cat_order as $id => $order) {
		$stmt = $dbh -> prepare("UPDATE common_cat SET com_cat_order=? WHERE com_cat_id=?");
		$stmt -> execute([$order, $id]);
	}
}

$dbh = null;

$item_url = ITEM_URL;
$redirect_url = $item_url."setting_cat.php";
header('Location: ' . $redirect_url);
?>