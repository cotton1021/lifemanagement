<?php
	include '../../inc/tool.php';

$db = new DBConnect();
$dbh = $db->getConnection();

$item_create_date = (new DateTime())->format('Y-m-d H:i:s');
$item_update_date = (new DateTime())->format('Y-m-d H:i:s');

function emptyToNull($value) {
	return ($value === '' ? null : $value);
}

$insertSql = "INSERT INTO item_main(item_name, item_cat, item_genre, item_price, item_payment_separate, item_release_date, item_buy_date, item_payment_date, item_pay_confirm, item_release_season, item_price_confirm, item_priority, item_url, item_note, item_hold_flag, item_todo_medium, item_todo_place, item_create_date) VALUES (? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,?)";

$updateSql = "UPDATE item_main SET item_name=?, item_cat=?, item_genre=?, item_price=?, item_payment_separate=?, item_release_date=?, item_buy_date=?, item_payment_date=?, item_pay_confirm=?, item_release_season=?, item_price_confirm=?, item_priority=?, item_url=?, item_note=?, item_hold_flag=?, item_todo_medium=?, item_todo_place=?,item_update_date=? ,item_delete_date=? ,item_delete_flag=? WHERE item_id=?";

$dbh->beginTransaction();
try {

	$insertStmt = $dbh->prepare($insertSql);
	$updateStmt = $dbh->prepare($updateSql);

	foreach ($_POST['items'] as $item) {

		$item_id = $item['item_id'] ?? null;
		$item_name = $item['item_name'] ?? null;
		$item_cat = emptyToNull($item['item_cat'] ?? null);
		$item_genre = emptyToNull($item['item_genre'] ?? null);
		$item_price = $item['item_price'] ?? null;
		$item_payment_separate = $item['item_payment_separate'] ?? 1;
		$item_release_date = !empty($item['item_release_date'])	? (new DateTime($item['item_release_date']))->format('Y-m-d 00:00:00') : null;
		$item_buy_date = !empty($item['item_buy_date'])	? (new DateTime($item['item_buy_date']))->format('Y-m-d 00:00:00') : null;
		$item_payment_date = !empty($item['item_payment_date'])	? (new DateTime($item['item_payment_date']))->format('Y-m-d 00:00:00') : null;
		$item_pay_confirm = $item['item_pay_confirm'] ?? 0;
		$item_release_season = emptyToNull($item['item_release_season'] ?? null);
		$item_price_confirm = $item['item_price_confirm'] ?? 0;
		$item_priority = emptyToNull($item['item_priority'] ?? null);
		$item_url = $item['item_url'] ?? null;
		$item_note = $item['item_note'] ?? null;
		$item_hold_flag = $item['item_hold_flag'] ?? 0;
		$item_todo_medium = emptyToNull($item['item_todo_medium'] ?? null);
		$item_todo_place = emptyToNull($item['item_todo_place'] ?? null);
		$item_delete_flag = $item['item_delete_flag'] ?? 0;
		$item_delete_date = $item_delete_flag ? date('Y-m-d H:i:s') : null;

		if (empty($item['item_id'])) {
			$insertStmt -> execute([
				$item_name,
				$item_cat,
				$item_genre,
				$item_price,
				$item_payment_separate,
				$item_release_date,
				$item_buy_date,
				$item_payment_date,
				$item_pay_confirm,
				$item_release_season,
				$item_price_confirm,
				$item_priority,
				$item_url,
				$item_note,
				$item_hold_flag,
				$item_todo_medium,
				$item_todo_place,
				$item_create_date
			]);
		}else{
			$updateStmt -> execute([
				$item_name,
				$item_cat,
				$item_genre,
				$item_price,
				$item_payment_separate,
				$item_release_date,
				$item_buy_date,
				$item_payment_date,
				$item_pay_confirm,
				$item_release_season,
				$item_price_confirm,
				$item_priority,
				$item_url,
				$item_note,
				$item_hold_flag,
				$item_todo_medium,
				$item_todo_place,
				$item_update_date,
				$item_delete_date,
				$item_delete_flag,
				$item_id
			]);
		}
	}
	$dbh->commit();
}catch (Exception $e){
	$dbh->rollBack();
	throw $e;
}

$dbh = null;

header('Location: ' . ITEM_URL . 'mypage.php');
exit;
?>