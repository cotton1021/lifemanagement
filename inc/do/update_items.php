<?php
	include '../../inc/tool.php';

$db = new DBConnect();
$dbh = $db->getConnection();

$item_select = $_POST['item_select'];
$action = $_POST['action'];

if($_POST['action'] === 'postpone'){
	foreach($item_select as $id){
		$sql = "SELECT item_payment_date, item_buy_date FROM item_main WHERE item_id = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->execute([$id]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$item_payment_date = $row['item_payment_date'];
		$item_payment_date = date('Y-m-d H:i:s',strtotime($item_payment_date.'+1 month'));
		$item_buy_date = $row['item_buy_date'];
		$item_buy_date = date('Y-m-d H:i:s',strtotime($item_buy_date.'+1 month'));
		$stmt = $dbh -> prepare("UPDATE item_main SET item_payment_date=?, item_buy_date=?, item_update_date=CURDATE() WHERE item_id=?");
		$stmt -> execute([$item_payment_date,$item_buy_date,$id]);
	}
	$dbh = null;
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}elseif($_POST['action'] === 'complete'){
	foreach($item_select as $id){
		$item_pay_confirm = 1;
		$stmt = $dbh -> prepare("UPDATE item_main SET 
        item_receive_flag = CASE 
            WHEN item_pay_confirm = 1 THEN 1 
            ELSE item_receive_flag 
        END,
				item_pay_confirm = CASE 
            WHEN item_pay_confirm != 1 THEN 1 
            ELSE item_pay_confirm 
        END,
        item_update_date = CURDATE()
    WHERE item_id = ?");
		$stmt->execute([$id]);
	}
	$dbh = null;
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}elseif($_POST['action'] === 'delete'){
	foreach($item_select as $id){
		$stmt = $dbh -> prepare("UPDATE item_main SET item_delete_date=CURDATE(), item_delete_flag=1 WHERE item_id=?");
		$stmt -> execute([$id]);
	}
	$dbh = null;
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}elseif($_POST['action'] === 'copy'){
	$params = [];
	foreach ($item_select as $id) {
		$params[] = 'copy_ids[]=' . urlencode((int)$id);
	}
	$query = implode('&', $params);

	$url = "../../views/item/item_edit.php?" . $query;
	header('Location: ' . $url);
	exit;
}else{
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
}
?>