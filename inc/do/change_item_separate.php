<?php
	include '../../inc/tool.php';

$item_sd_id = 1;
$item_separate_date = $_POST['item_separate_date'];

$db = new DBConnect();
$dbh = $db->getConnection();

$stmt = $dbh -> prepare("UPDATE item_separate SET item_separate_date=? WHERE item_sd_id=?");
$data = []; //配列を初期化
$data[] = $item_separate_date;
$data[] = $item_sd_id;

$stmt -> execute($data);
$dbh = null;

$itemurl = ITEM_URL;

$redirect_url = $itemurl."setting_separate.php";

header('Location: ' . $redirect_url);
?>