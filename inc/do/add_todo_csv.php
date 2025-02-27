<?php
include '../../inc/tool.php';

$db = new DBConnect();
$dbh = $db->getConnection();

// sample01.php でアップロードされたCSVファイルは$_FILES['todo_csv_file']取得できる
$fileName = $_FILES['todo_csv_file']['name'];
$fileTmpName = $_FILES['todo_csv_file']['tmp_name'];

// ファイルパス
$filePath = '../../views/todo/csv/' . $fileName;

// CSVファイルを移動
if (!move_uploaded_file($fileTmpName, $filePath)) {
	die('ファイルのアップロードに失敗しました。');
}

// CSVファイルの読み込み
$fileContent = file($filePath);
if ($fileContent === false) {
	die('CSVファイルの読み込みに失敗しました。');
}

// CSVデータを配列に変換
$data = array_map('str_getcsv', $fileContent);

// データの登録処理
foreach ($data as $key => $row) {
	if ($key === 0) {
		continue; // 1行目はヘッダーとみなしてスキップ
	}

	$todo_name = $row[0];
	$todo_list = $row[1];
	$todo_priority_num = $row[2] ?? null;
	$todo_start = (new DateTime())->format('Y-m-d H:i:s');
	$todo_create_date = (new DateTime())->format('Y-m-d H:i:s');

	if (empty($todo_priority_num)) {
		// 最大の todo_priority_num を取得
		$stmt = $dbh->prepare("SELECT MAX(todo_priority_num) FROM todo_main WHERE todo_list = ? AND todo_complete=0");
		$stmt->execute([$todo_list]);
		$max_priority = $stmt->fetchColumn();
		$todo_priority_num = ($max_priority !== null) ? $max_priority + 1 : 1;
	}

	$stmt = $dbh->prepare("INSERT INTO todo_main (todo_name, todo_list, todo_priority_num, todo_start, todo_create_date) VALUES (:todo_name, :todo_list, :todo_priority_num, :todo_start, :todo_create_date)");
	$stmt->bindParam(':todo_name', $todo_name);
	$stmt->bindParam(':todo_list', $todo_list);
	$stmt->bindParam(':todo_priority_num', $todo_priority_num);
	$stmt->bindParam(':todo_start', $todo_start);
	$stmt->bindParam(':todo_create_date', $todo_create_date);
	$stmt->execute();
}

//CSVファイルを削除
unlink($filePath);

echo 'CSVアップロードが完了しました。';

$todourl = TODO_URL;

if (empty($_POST['todo_id'])) {
	$redirect_url = $todourl . "todo_csv.php";
} else {
	$redirect_url = $todourl . "todo_csv.php";
}
header('Location: ' . $redirect_url);
