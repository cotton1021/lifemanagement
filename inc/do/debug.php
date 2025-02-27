<?php
include '../../inc/tool.php';

$db = new DBConnect();
$dbh = $db->getConnection();

$today = date('Y-m-d');
$todo_select = $_POST['todo_select'];

if ($_POST['action'] === 'update') {
	foreach ($todo_select as $id) {
		$todo_id = $id;
		$todo_start = $_POST['todo_start'][$id] ?? null;
		if (!empty($todo_start) && strtotime($todo_start)) {
			$todo_start = (new DateTime($todo_start))->format('Y-m-d 00:00:00');
		} else {
			$todo_start = null; // 無効な日付の場合は NULL に設定
		}
		$todo_deadline = $_POST['todo_deadline'][$id] ?? null;
		if (!empty($todo_deadline) && strtotime($todo_deadline)) {
			$todo_deadline = (new DateTime($todo_deadline))->format('Y-m-d 00:00:00');
		} else {
			$todo_deadline = null;
		}
		$todo_priority_num = $_POST['todo_priority_num'][$id] ?? 0;

		echo $todo_id . ' start:' . $todo_start . ' deadline:' . $todo_deadline . 'prioryty:' . $todo_priority_num;
	}
} elseif ($_POST['action'] === 'postpone') {
	foreach ($todo_select as $id) {
		$sql = "SELECT todo_deadline FROM todo_main WHERE todo_id = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->execute([$id]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$todo_deadline = $row['todo_deadline'];
		if ($todo_deadline == "" || $todo_deadline < $today) {
			$todo_deadline = date('Y-m-d H:i:s', strtotime($today . '+1 day'));
		} else {
			$todo_deadline = date('Y-m-d H:i:s', strtotime($todo_deadline . '+1 day'));
		}
		$stmt = $dbh->prepare("UPDATE todo_main SET todo_deadline=?, todo_update_date=CURDATE() WHERE todo_id=?");
		$stmt->execute([$todo_deadline, $id]);
	}
} elseif ($_POST['action'] === 'complete') {
	foreach ($todo_select as $id) {
		//todo_list_roop を取得
		$stmt = $dbh->prepare("SELECT TM.*,
			TL.todo_list_roop
			FROM todo_main AS TM
				LEFT JOIN todo_list AS TL
					ON TM.todo_list = TL.todo_list_id
			WHERE todo_id = ?");
		$stmt->execute([$id]);
		$task = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($task) {
			//todo_complete を更新
			$stmt = $dbh->prepare("UPDATE todo_main SET todo_complete = 1, todo_complete_date = CURDATE() WHERE todo_id = ?");
			$stmt->execute([$id]);

			//todo_list_roop が 1 なら新規登録する
			if ($task['todo_list_roop'] == 1) {
				$stmt = $dbh->prepare("
									INSERT INTO todo_main 
                        (todo_name, todo_list, todo_roop, todo_medium, todo_place, todo_priority, todo_priority_num, todo_url, todo_start, todo_deadline, todo_complete, todo_complete_date, todo_note, todo_create_date)
                    SELECT 
                        todo_name, todo_list, todo_roop, todo_medium, todo_place, todo_priority, todo_priority_num, 
                        todo_url, CURDATE(), DATE_ADD(COALESCE(todo_deadline, CURDATE()), INTERVAL 1 DAY), 
                        0, NULL, todo_note, CURDATE()
                    FROM todo_main 
                    WHERE todo_id = ?
							");
				$stmt->execute([$id]);
			}
		}
	}
} elseif ($_POST['action'] === 'delete') {
	foreach ($todo_select as $id) {
		$stmt = $dbh->prepare("UPDATE todo_main SET todo_delete_date=CURDATE(), todo_delete_flag=1 WHERE todo_id=?");
		$stmt->execute([$id]);
	}
}

$dbh = null;
