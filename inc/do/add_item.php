<?php
	include '../../inc/tool.php';

$item_id = !empty($_POST['item_id']) ? $_POST['item_id'] : NULL;
$item_name = $_POST['item_name'];
$item_cat = !empty($_POST['item_cat']) ? $_POST['item_cat'] : NULL;
$item_genre = !empty($_POST['item_genre']) ? $_POST['item_genre'] : NULL;
$item_price = $_POST['item_price'];
$item_release_date = $_POST['item_release_date'];
$item_release_date = (new DateTime($item_release_date))->format('Y-m-d 00:00:00');
$item_buy_date = $_POST['item_buy_date'];
$item_buy_date = (new DateTime($item_buy_date))->format('Y-m-d 00:00:00');
$item_payment_date = $_POST['item_payment_date'];
$item_payment_date = (new DateTime($item_payment_date))->format('Y-m-d 00:00:00');
$item_pay_confirm = !empty($_POST['item_pay_confirm']) ? $_POST['item_pay_confirm'] : 0;
$item_release_season = !empty($_POST['item_release_season']) ? $_POST['item_release_season'] : 0;
$item_price_confirm = !empty($_POST['item_price_confirm']) ? $_POST['item_price_confirm'] : 0;
$item_priority = !empty($_POST['item_priority']) ? $_POST['item_priority'] : NULL;
$item_url = $_POST['item_url'];
$item_note = !empty($_POST['item_note']) ? $_POST['item_note'] : NULL;
$item_hold_flag = !empty($_POST['item_hold_flag']) ? $_POST['item_hold_flag'] : 0;
$item_todo_medium = !empty($_POST['item_todo_medium']) ? $_POST['item_todo_medium'] : NULL;
$item_todo_place = !empty($_POST['item_todo_place']) ? $_POST['item_todo_place'] : NULL;
$item_create_date = (new DateTime())->format('Y-m-d H:i:s');
$item_update_date = (new DateTime())->format('Y-m-d H:i:s');
if($_POST['item_delete_flag']==1){
	$item_delete_date = (new DateTime())->format('Y-m-d H:i:s');
}
$item_delete_flag = !empty($_POST['item_delete_flag']) ? $_POST['item_delete_flag'] : 0;

$db = new DBConnect();
$dbh = $db->getConnection();

if(empty($_POST['item_id'])){
	$stmt = $dbh -> prepare("INSERT INTO item_main(item_id, item_name, item_cat, item_genre, item_price, item_release_date, item_buy_date, item_payment_date, item_pay_confirm, item_release_season, item_price_confirm, item_priority, item_url, item_note, item_hold_flag, item_todo_medium, item_todo_place, item_create_date) VALUES (? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,?)");
	$data = []; //配列を初期化
	$data[] = $item_id;
	$data[] = $item_name;
	$data[] = $item_cat;
	$data[] = $item_genre;
	$data[] = $item_price;
	$data[] = $item_release_date;
	$data[] = $item_buy_date;
	$data[] = $item_payment_date;
	$data[] = $item_pay_confirm;
	$data[] = $item_release_season;
	$data[] = $item_price_confirm;
	$data[] = $item_priority;
	$data[] = $item_url;
	$data[] = $item_note;
	$data[] = $item_hold_flag;
	$data[] = $item_todo_medium;
	$data[] = $item_todo_place;	
	$data[] = $item_create_date;
}else{
	$stmt = $dbh -> prepare("UPDATE item_main SET item_name=?, item_cat=?, item_genre=?, item_price=?, item_release_date=?, item_buy_date=?, item_payment_date=?, item_pay_confirm=?, item_release_season=?, item_price_confirm=?, item_priority=?, item_url=?, item_note=?, item_hold_flag=?, item_todo_medium=?, item_todo_place=?,item_update_date=? ,item_delete_date=? ,item_delete_flag=? WHERE item_id=?");
	$data = []; //配列を初期化
	$data[] = $item_name;
	$data[] = $item_cat;
	$data[] = $item_genre;
	$data[] = $item_price;
	$data[] = $item_release_date;
	$data[] = $item_buy_date;
	$data[] = $item_payment_date;
	$data[] = $item_pay_confirm;
	$data[] = $item_release_season;
	$data[] = $item_price_confirm;
	$data[] = $item_priority;
	$data[] = $item_url;
	$data[] = $item_note;
	$data[] = $item_hold_flag;
	$data[] = $item_todo_medium;
	$data[] = $item_todo_place;	
	$data[] = $item_update_date;
	$data[] = $item_delete_date;
	$data[] = $item_delete_flag;
	$data[] = $item_id;
}
$stmt -> execute($data);
$dbh = null;

$itemurl = ITEM_URL;

if(empty($_POST['item_id'])){
$redirect_url = $itemurl."item_edit.php";
}else{
	$redirect_url = $itemurl."item_edit.php?id=".$item_id;
}
header('Location: ' . $redirect_url);
?>