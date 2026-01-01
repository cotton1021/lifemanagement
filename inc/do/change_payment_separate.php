<?php
	include '../../inc/tool.php';

$item_sd_id = 2;
$item_separate_date = $_POST['item_separate_date'];

$db = new DBConnect();
$dbh = $db->getConnection();

$stmt = $dbh -> prepare("UPDATE item_separate SET item_separate_date=? WHERE item_sd_id=?");
$data = []; //配列を初期化
$data[] = $item_separate_date;
$data[] = $item_sd_id;

$stmt -> execute($data);

$stmt =  $dbh -> prepare("UPDATE item_payment_separate SET item_ps_start=?,item_ps_payment=?,item_ps_order=? WHERE item_ps_id=?");
foreach ($_POST['item_ps_start'] as $item_ps_id => $item_ps_start) {

    $item_ps_payment = $_POST['item_ps_payment'][$item_ps_id];
    $item_ps_order   = $_POST['item_ps_order'][$item_ps_id];

    $stmt->execute([
        $item_ps_start,
        $item_ps_payment,
        $item_ps_order,
        $item_ps_id
    ]);
}

$dbh = null;

$itemurl = ITEM_URL;

$redirect_url = $itemurl."setting_separate.php";

header('Location: ' . $redirect_url);
?>