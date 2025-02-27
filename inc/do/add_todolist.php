<?php
include '../../inc/tool.php';

$todo_list_id = !empty($_POST['todo_list_id']) ? $_POST['todo_list_id'] : NULL;
$todo_list_name = $_POST['todo_list_name'];
$todo_cat = !empty($_POST['todo_cat']) ? $_POST['todo_cat'] : NULL;
$todo_genre = !empty($_POST['todo_genre']) ? $_POST['todo_genre'] : NULL;
$todo_list_roop = !empty($_POST['todo_list_roop']) ? $_POST['todo_list_roop'] : 0;
$todo_list_priority = !empty($_POST['todo_list_priority']) ? $_POST['todo_list_priority'] : NULL;
$todo_list_priority_num = !empty($_POST['todo_list_priority_num']) ? $_POST['todo_list_priority_num'] : 0;
$todo_list_start = $_POST['todo_list_start'];
$todo_list_start = (new DateTime($todo_list_start))->format('Y-m-d 00:00:00');
$todo_list_deadline = $_POST['todo_list_deadline'];
$todo_list_deadline = (new DateTime($todo_list_deadline))->format('Y-m-d 00:00:00');
$todo_list_complete = !empty($_POST['todo_list_complete']) ? $_POST['todo_list_complete'] : 0;
$todo_list_complete_date = $_POST['todo_list_complete_date'];
$todo_list_complete_date = !empty($todo_list_complete_date) ? (new DateTime($todo_list_complete_date))->format('Y-m-d 00:00:00') : NULL;
$todo_list_url = $_POST['todo_list_url'];
$todo_list_note = !empty($_POST['todo_list_note']) ? $_POST['todo_list_note'] : NULL;
$todo_list_create_date = (new DateTime())->format('Y-m-d H:i:s');
$todo_list_update_date = (new DateTime())->format('Y-m-d H:i:s');
if ($_POST['todo_list_delete_flag'] == 1) {
	$todo_list_delete_date = (new DateTime())->format('Y-m-d H:i:s');
}
$todo_list_delete_flag = !empty($_POST['todo_list_delete_flag']) ? $_POST['todo_list_delete_flag'] : 0;
$todo_list_hold_flag = !empty($_POST['todo_list_hold_flag']) ? $_POST['todo_list_hold_flag'] : 0;

$db = new DBConnect();
$dbh = $db->getConnection();

if (empty($_POST['todo_list_id'])) {
	$stmt = $dbh->prepare("INSERT INTO todo_list(todo_list_id, todo_list_name, todo_cat, todo_genre, todo_list_roop, todo_list_priority, todo_list_priority_num, todo_list_start, todo_list_deadline, todo_list_complete, todo_list_complete_date, todo_list_url, todo_list_note, todo_list_create_date) VALUES (? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,?)");
	$data = []; //配列を初期化
	$data[] = $todo_list_id;
	$data[] = $todo_list_name;
	$data[] = $todo_cat;
	$data[] = $todo_genre;
	$data[] = $todo_list_roop;
	$data[] = $todo_list_priority;
	$data[] = $todo_list_priority_num;
	$data[] = $todo_list_start;
	$data[] = $todo_list_deadline;
	$data[] = $todo_list_complete;
	$data[] = $todo_list_complete_date;
	$data[] = $todo_list_url;
	$data[] = $todo_list_note;
	$data[] = $todo_list_create_date;
} else {
	$stmt = $dbh->prepare("UPDATE todo_list SET todo_list_name=?, todo_cat=?, todo_genre=?, todo_list_roop=?, todo_list_priority=?, todo_list_priority_num=?, todo_list_start=?, todo_list_deadline=?, todo_list_complete=?, todo_list_complete_date=?, todo_list_url=?, todo_list_note=?, todo_list_update_date=?, todo_list_delete_date=?, todo_list_delete_flag=?, todo_list_hold_flag=? WHERE todo_list_id=?");
	$data = []; //配列を初期化
	$data[] = $todo_list_name;
	$data[] = $todo_cat;
	$data[] = $todo_genre;
	$data[] = $todo_list_roop;
	$data[] = $todo_list_priority;
	$data[] = $todo_list_priority_num;
	$data[] = $todo_list_start;
	$data[] = $todo_list_deadline;
	$data[] = $todo_list_complete;
	$data[] = $todo_list_complete_date;
	$data[] = $todo_list_url;
	$data[] = $todo_list_note;
	$data[] = $todo_list_update_date;
	$data[] = $todo_list_delete_date;
	$data[] = $todo_list_delete_flag;
	$data[] = $todo_list_hold_flag;
	$data[] = $todo_list_id;
}
$stmt->execute($data);
$dbh = null;

$todourl = TODO_URL;

if (empty($_POST['todo_list_id'])) {
	$redirect_url = $todourl . "todo_edit.php?list=" . $todo_list_id;
} else {
	$redirect_url = $todourl . "todo_list_edit.php?id=" . $todo_list_id;
}
header('Location: ' . $redirect_url);
