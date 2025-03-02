<?php
include '../../inc/tool.php';

$db = new DBConnect();
$dbh = $db->getConnection();

$todo_id = !empty($_POST['todo_id']) ? $_POST['todo_id'] : NULL;
$todo_name = $_POST['todo_name'];
$todo_list = $_POST['todo_list'];
$todo_roop = !empty($_POST['todo_roop']) ? $_POST['todo_roop'] : 0;
$todo_medium = isset($_POST['todo_medium']) ? $_POST['todo_medium'] : NULL;
$todo_place = isset($_POST['todo_place']) ? $_POST['todo_place'] : NULL;
$todo_priority = !empty($_POST['todo_priority']) ? $_POST['todo_priority'] : NULL;
$todo_priority_num = !empty($_POST['todo_priority_num']) ? $_POST['todo_priority_num'] : 0;
if (empty($todo_priority_num)) {
	// 同じ todo_list 内で最大の todo_priority_num を取得
	$stmt = $dbh->prepare("SELECT MAX(todo_priority_num) FROM todo_main WHERE todo_list = ? AND todo_complete=0");
	$stmt->execute([$todo_list]);
	$max_priority = $stmt->fetchColumn();

	// 最大値が NULL の場合は 1 にする（最初のデータ登録時）
	$todo_priority_num = ($max_priority !== null) ? $max_priority + 1 : 1;
}
$todo_url = $_POST['todo_url'];
$todo_start = $_POST['todo_start'];
$todo_start = (new DateTime($todo_start))->format('Y-m-d 00:00:00');
$todo_deadline = $_POST['todo_deadline'];
$todo_deadline = !empty($todo_deadline) ? (new DateTime($todo_deadline))->format('Y-m-d 00:00:00') : NULL;
$todo_complete = !empty($_POST['todo_complete']) ? $_POST['todo_complete'] : 0;
$todo_complete_date = $_POST['todo_complete_date'];
$todo_complete_date = !empty($todo_complete_date) ? (new DateTime($todo_complete_date))->format('Y-m-d 00:00:00') : NULL;
$todo_note = !empty($_POST['todo_note']) ? $_POST['todo_note'] : NULL;
$todo_create_date = (new DateTime())->format('Y-m-d H:i:s');
$todo_update_date = (new DateTime())->format('Y-m-d H:i:s');
if ($_POST['todo_delete_flag'] == 1) {
	$todo_delete_date = (new DateTime())->format('Y-m-d H:i:s');
}
$todo_delete_flag = !empty($_POST['todo_delete_flag']) ? $_POST['todo_delete_flag'] : 0;
$todo_hold_flag = !empty($_POST['todo_hold_flag']) ? $_POST['todo_hold_flag'] : 0;

$db = new DBConnect();
$dbh = $db->getConnection();

if (empty($_POST['todo_id'])) {
	$stmt = $dbh->prepare("INSERT INTO todo_main(todo_id, todo_name, todo_list, todo_roop,todo_medium, todo_place, todo_priority, todo_priority_num, todo_url, todo_start, todo_deadline, todo_complete, todo_complete_date, todo_note, todo_create_date) VALUES (? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,?)");
	$data = []; //配列を初期化
	$data[] = $todo_id;
	$data[] = $todo_name;
	$data[] = $todo_list;
	$data[] = $todo_roop;
	$data[] = $todo_medium;
	$data[] = $todo_place;
	$data[] = $todo_priority;
	$data[] = $todo_priority_num;
	$data[] = $todo_url;
	$data[] = $todo_start;
	$data[] = $todo_deadline;
	$data[] = $todo_complete;
	$data[] = $todo_complete_date;
	$data[] = $todo_note;
	$data[] = $todo_create_date;
} else {
	$stmt = $dbh->prepare("UPDATE todo_main SET todo_name=?, todo_list=?, todo_roop=?, todo_medium=?, todo_place=?, todo_priority=?, todo_priority_num=?, todo_url=?, todo_start=?, todo_deadline=?, todo_complete=?, todo_complete_date=?, todo_note=?, todo_update_date=?, todo_delete_date=?, todo_delete_flag=?, todo_hold_flag=? WHERE todo_id=?");
	$data = []; //配列を初期化
	$data[] = $todo_name;
	$data[] = $todo_list;
	$data[] = $todo_roop;
	$data[] = $todo_medium;
	$data[] = $todo_place;
	$data[] = $todo_priority;
	$data[] = $todo_priority_num;
	$data[] = $todo_url;
	$data[] = $todo_start;
	$data[] = $todo_deadline;
	$data[] = $todo_complete;
	$data[] = $todo_complete_date;
	$data[] = $todo_note;
	$data[] = $todo_update_date;
	$data[] = $todo_delete_date;
	$data[] = $todo_delete_flag;
	$data[] = $todo_hold_flag;
	$data[] = $todo_id;
}

$stmt->execute($data);

if (!empty($_POST['todo_id']) && $todo_complete == 1) {

	//todo_list_roop を取得
	$stmt = $dbh->prepare("SELECT TM.*,
			TL.todo_list_roop
			FROM todo_main AS TM
				LEFT JOIN todo_list AS TL
					ON TM.todo_list = TL.todo_list_id
			WHERE todo_id = ?");
	$stmt->execute([$todo_id]);
	$task = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($task['todo_list_roop'] == 1 || $todo_roop == 1) {
		$todo_id = NULL;
		$todo_start =  (new DateTime())->format('Y-m-d H:i:s');
		$todo_deadline = NULL;
		$todo_complete = 0;
		$todo_complete_date = NULL;

		// 同じ todo_list 内で最大の todo_priority_num を取得
		$stmt = $dbh->prepare("SELECT MAX(todo_priority_num) FROM todo_main WHERE todo_list = ? AND todo_complete=0");
		$stmt->execute([$todo_list]);
		$max_priority = $stmt->fetchColumn();

		// 最大値が NULL の場合は 1 にする（最初のデータ登録時）
		$todo_priority_num = ($max_priority !== null) ? $max_priority + 1 : 1;


		$stmt = $dbh->prepare("INSERT INTO todo_main(todo_id, todo_name, todo_list, todo_roop,todo_medium, todo_place, todo_priority, todo_priority_num, todo_url, todo_start, todo_deadline, todo_complete, todo_complete_date, todo_note, todo_create_date) VALUES (? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,?)");
		$data = []; //配列を初期化
		$data[] = $todo_id;
		$data[] = $todo_name;
		$data[] = $todo_list;
		$data[] = $todo_roop;
		$data[] = $todo_medium;
		$data[] = $todo_place;
		$data[] = $todo_priority;
		$data[] = $todo_priority_num;
		$data[] = $todo_url;
		$data[] = $todo_start;
		$data[] = $todo_deadline;
		$data[] = $todo_complete;
		$data[] = $todo_complete_date;
		$data[] = $todo_note;
		$data[] = $todo_create_date;
	}
}

$stmt->execute($data);
$dbh = null;

$todourl = TODO_URL;

if (empty($_POST['todo_id'])) {
	$redirect_url = $todourl . "todo_edit.php?list=" . $todo_list;
} else {
	$redirect_url = $todourl . "todo_edit.php?id=" . $todo_id . "&list=" . $todo_list;
}
header('Location: ' . $redirect_url);
