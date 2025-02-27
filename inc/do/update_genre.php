<?php
	include '../../inc/tool.php';

$db = new DBConnect();
$dbh = $db->getConnection();

$item_select = $_POST['item_select'];

if($_POST['action'] === 'delete'){
	foreach($item_select as $id){
		$stmt = $dbh -> prepare("UPDATE common_genre SET item_delete_date=CURDATE(), item_delete_flag=1 WHERE com_genre_id=?");
		$stmt -> execute([$id]);
	}
}elseif($_POST['action'] === 'order_change'){
	$com_genre_order = $_POST['com_genre_order'];
	foreach ($com_genre_order as $id => $order) {
		$stmt = $dbh -> prepare("UPDATE common_genre SET com_genre_order=? WHERE com_genre_id=?");
		$stmt -> execute([$order, $id]);
	}
}

$dbh = null;

$item_url = ITEM_URL;
$redirect_url = $item_url."setting_genre.php";
header('Location: ' . $redirect_url);
?>