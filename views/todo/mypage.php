<?php
include '../../inc/tool.php';

$title = 'ToDoマイページ';
get_header($title);

$today = date('Y-m-d');

$db = new DBConnect();
$dbh = $db->getConnection();

$interval = 3;
?>

<body id="todo" class="todo_main">
<?php
	get_headerMenu();
?>
	<div class="link_area">
		<a class="prev" href="./todo_archives.php">達成リスト</a>
		<a class="next" href="./todo_list.php">リスト一覧へ</a>
	</div>
	<section>
		<h2>本日のやることリスト（開発中）</h2>
		<?php
		// ToDoリスト読み込み
		$sql = "WITH Ranked AS (
				SELECT TM.*, 
						TME.todo_medium_name, 
						TP.todo_place_name, 
						IP.item_priority_name, 
						TL.todo_list_name,
						TL.todo_list_roop,
						ROW_NUMBER() OVER (PARTITION BY TM.todo_list ORDER BY TM.todo_priority_num ASC) AS rn
				FROM todo_main AS TM
				LEFT JOIN todo_medium AS TME ON TM.todo_medium = TME.todo_medium_id
				LEFT JOIN todo_place AS TP ON TM.todo_place = TP.todo_place_id
				LEFT JOIN item_priority AS IP ON TM.todo_priority = IP.item_priority_id
				LEFT JOIN todo_list AS TL ON TM.todo_list = TL.todo_list_id
				WHERE TM.todo_hold_flag = 0 AND TM.todo_delete_flag = 0 AND TM.todo_complete = 0
		) 
		SELECT * FROM Ranked WHERE rn = 1";

		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		//todo_list_roop で分ける
		$results_roop = [];
		$results_main = [];

		foreach ($results as $rec) {
			if ($rec['todo_list_roop'] == 1) {
				$results_roop[] = $rec;
			} else {
				$results_main[] = $rec;
			}
		}

		do {

			$interval_r = $interval;
			$interval_m = $interval;

			// 条件に合うデータのみを抽出
			$filterResults = function ($list, $interval) {
				return array_filter($list, function ($rec) use ($interval) {
					return $rec['todo_complete'] == 0 &&
						(empty($rec['todo_start']) || $rec['todo_start'] <= date('Y-m-d', strtotime("+1 days"))) &&
						(empty($rec['todo_deadline']) || $rec['todo_deadline'] <= date('Y-m-d', strtotime("+$interval days")));
				});
			};

			$results_roop = $filterResults($results_roop, $interval_r);
			$results_main = $filterResults($results_main, $interval_m);

			// どちらのデータも0件なら interval を +4 して再検索
			$hasResults = !empty($results_roop) || !empty($results_main);
			if (!$hasResults) {
				$interval += 4;
			} elseif (empty($results_roop) && !empty($results_main)) {
				$interval_r += 4;
			} elseif (!empty($results_roop) && empty($results_main)) {
				$interval_m += 4;
			} elseif ($hasResults) {
				break;
			}
		} while (!$hasResults);

		?>
		<form method="post" action="../../inc/do/update_tasks.php">
			<?php
			$todo_categories = [
				'daily' => ['title' => '習慣', 'class' => 'everyday', 'data' => $results_roop],
				'todomain' => ['title' => 'メイン', 'class' => 'todomain', 'data' => $results_main]
			];

			foreach ($todo_categories as $id => $category) {
			?>
				<div id="<?php echo $id; ?>" class="todo_note <?php echo $category['class']; ?>">
					<p class="todo_subtitle open"><?php echo $category['title'] . ' ' . count($category['data']) . '件'; ?></p>
					<table style="display: table;">
						<thead>
							<tr>
								<th> </th>
								<th> </th>
								<th>タイトル</th>
								<th>開始日</th>
								<th>締切</th>
								<th> </th>
							</tr>
						</thead>
						<?php
						foreach ($category['data'] as $todo) {
							$todo_priority = ['1' => 'high', '2' => 'middle', '3' => 'low'][$todo['todo_priority']] ?? '';

							$todo_start = (new DateTime($todo['todo_start']))->format("Y/n/j");
							$todo_deadline = !empty($todo['todo_deadline']) ? (new DateTime($todo['todo_deadline']))->format("Y/n/j") : '';

							// あと何日かカウント
							$current_date = new DateTime();
							$todo_deadline_obj = new DateTime($todo_deadline);
							$days_count_d = $current_date->diff($todo_deadline_obj);
							$todo_deadline_count = $days_count_d->invert ? 0 : $days_count_d->days + 1;
						?>
							<tbody>
								<tr>
									<td rowspan="2" class="check"><input type="checkbox" name="todo_select[]" value="<?php echo $todo['todo_id'] ?>"></td>
									<td class="deadline<?php if ($todo_deadline_count <= 3) { ?> close<?php } ?>">あと<span><?php echo $todo_deadline_count; ?></span>日</td>
									<td class="title">
										<span class="list_name"><?php echo $todo['todo_list_name']; ?></span>
										<?php echo $todo['todo_name']; ?>
										<span class="priority <?php echo $todo_priority; ?>"><?php echo $todo['item_priority_name']; ?></span><br>
										<?php if (isset($todo['todo_place'])) { ?>
											<span class="todo_place"><?php echo $todo['todo_place_name']; ?></span>
										<?php } ?>
										<?php if (isset($todo['todo_medium'])) { ?>
											<span class="todo_medium"><?php echo $todo['todo_medium_name']; ?></span>
										<?php } ?>
										<br class="sp">
										<p class="note sp"><?php echo $todo['todo_note']; ?></p>
									</td>
									<td data-label="開始日：" class="startday"><?php echo $todo_start; ?></td>
									<td data-label="締切：" class="deadline"><?php echo $todo_deadline; ?></td>
									<td rowspan="2" class="change">
										<a class="change_button" href="./todo_list.php?list=<?php echo $todo['todo_list']; ?>" target="_blank">一覧</a>
										<a class="change_button" href="./todo_edit.php?id=<?php echo $todo['todo_id']; ?>" target="_blank">変更</a>
									</td>
								</tr>
								<tr class="pc">
									<td colspan="4">
										<p class="note"><?php echo $todo['todo_list_note']; ?></p>
									</td>
								</tr>
							</tbody>
						<?php
						}
						?>
					</table>
					<input type="checkbox" name="all_check" class="all_check" id="all_<?php echo $id; ?>"><label for="all_<?php echo $id; ?>">全選択</label>
					<div class="todo_setting">
						<div><input type="submit" name="action" class="icon delete edit_icon" value="delete" disabled><span class="hover">削除</span></div>
						<div><input type="submit" name="action" class="icon postpone edit_icon" value="postpone" disabled><span class="hover">翌日へ延期</span></div>
						<div><input type="submit" name="action" class="icon complete edit_icon" value="complete" disabled><span class="hover">完了</span></div>
						<div><a href="./item_edit.php" target="_blank"><span class="icon new_item"><span></span></span></a><span class="hover">新規ToDo</span></div>
					</div>
				</div>
			<?php
			}
			?>
		</form>
		<?php
		//買い物リスト読み込み（購入タスク）
		$sql = "SELECT IM.*,
					CC.com_cat,
					CG.com_genre,
					IP.item_priority_name,
					IPC.item_pc,
					IRS.item_rs,
					TM.todo_medium_name,
					TP.todo_place_name
					FROM item_main AS IM
						LEFT JOIN common_cat AS CC
							ON IM.item_cat = CC.com_cat_id
						LEFt JOIN common_genre AS CG
							ON IM.item_genre = CG.com_genre_id
						LEFT JOIN item_priority AS IP
							ON IM.item_priority = IP.item_priority_id
						LEFT JOIN item_price_confirm AS IPC
							ON IM.item_price_confirm = IPC.item_pc_id
						LEFT JOIN item_release_season AS IRS
							ON IM.item_release_season = IRS.item_rs_id
						LEFT JOIN todo_medium AS TM
							ON IM.item_todo_medium = TM.todo_medium_id
						LEFT JOIN todo_place AS TP
							ON IM.item_todo_place = TP.todo_place_id
					WHERE item_buy_date <= ? AND IM.item_hold_flag = 0 AND IM.item_delete_flag = 0 AND IM.item_pay_confirm = 0
					ORDER by IM.item_buy_date, IM.item_priority, IM.item_release_date";
		$stmt = $dbh->prepare($sql);

		$buy_days_deadline = date('Y-m-d H:i:s', strtotime($today . "+3 days"));
		$data = [];
		$data[] = $buy_days_deadline;
		$stmt->execute($data);

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		//買い物リスト読み込み（受け取りタスク）
		$sql = "SELECT IM.*,
					CC.com_cat,
					CG.com_genre,
					IP.item_priority_name,
					IPC.item_pc,
					IRS.item_rs,
					TM.todo_medium_name,
					TP.todo_place_name
					FROM item_main AS IM
						LEFT JOIN common_cat AS CC
							ON IM.item_cat = CC.com_cat_id
						LEFt JOIN common_genre AS CG
							ON IM.item_genre = CG.com_genre_id
						LEFT JOIN item_priority AS IP
							ON IM.item_priority = IP.item_priority_id
						LEFT JOIN item_price_confirm AS IPC
							ON IM.item_price_confirm = IPC.item_pc_id
						LEFT JOIN item_release_season AS IRS
							ON IM.item_release_season = IRS.item_rs_id
						LEFT JOIN todo_medium AS TM
							ON IM.item_todo_medium = TM.todo_medium_id
						LEFT JOIN todo_place AS TP
							ON IM.item_todo_place = TP.todo_place_id
					WHERE item_release_date <= ? AND IM.item_hold_flag = 0 AND IM.item_delete_flag = 0 AND IM.item_pay_confirm = 1 AND IM.item_receive_flag = 0
					ORDER by IM.item_release_date, IM.item_priority, IM.item_buy_date";
		$stmt = $dbh->prepare($sql);

		$release_days_deadline = date('Y-m-d H:i:s', strtotime($today . "+3 days"));
		$data = [];
		$data[] = $release_days_deadline;
		$stmt->execute($data);

		$results_receive = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// 結果を統合
		$all_results = array_merge($results, $results_receive);

		$count_total = 0; /* 件数リセット */
		// 合計件数をカウント
		$count_total = count($all_results);
		?>
		<form method="post" action="../../inc/do/update_items.php">
			<div id="item" class="todo_note shopping">
				<a class="list_button" href="./todo_list.php?item=1" target="_blank">買い物一覧（1週間分）へ</a>
				<p class="todo_subtitle open">買い物 <?php echo $count_total ?>件</p>
				<table style="display: table;">
					<thead>
						<tr>
							<th> </th>
							<th> </th>
							<th>タイトル</th>
							<th>購入日</th>
							<th>発売日</th>
							<th> </th>
						</tr>
					</thead>
					<?php
					foreach ($all_results as $rec) {
						$item_priority_id = $rec['item_priority'];
						$item_priority = "";
						if ($item_priority_id == 1) {
							$item_priority = "high";
						} elseif ($item_priority_id == 2) {
							$item_priority = "middle";
						} elseif ($item_priority_id == 3) {
							$item_priority = "low";
						}
						$item_release_date = $rec['item_release_date'];
						$item_release_date = (new DateTime($item_release_date))->format("Y/n/j");
						$item_buy_date = $rec['item_buy_date'];
						$item_buy_date = (new DateTime($item_buy_date))->format("Y/n/j");
						$item_payment_date = $rec['item_payment_date'];
						$item_payment_date = (new DateTime($item_payment_date))->format("Y/n/j");
						$item_price_confirm = $rec['item_price_confirm'];
						$item_pay_confirm = $rec['item_pay_confirm'];
						$item_release_season = $rec['item_release_season'];
						$todo_medium = $rec['todo_medium_name'];
						$todo_place = $rec['todo_place_name'];

						//あと何日かカウント
						$current_date = new DateTime();
						$item_buy_date_obj = new DateTime($item_buy_date);
						$days_count_b = $current_date->diff($item_buy_date_obj);
						if ($days_count_b->invert) {
							$item_buy_count = 0;
						} else {
							$item_buy_count = $days_count_b->days;
							$item_buy_count++;
						}
						$item_release_date_obj = new DateTime($item_release_date);
						$days_count_r = $current_date->diff($item_release_date_obj);
						if ($days_count_r->invert) {
							$item_release_count = 0;
						} else {
							$item_release_count = $days_count_r->days;
							$item_release_count++;
						}
					?>
						<tbody>
							<tr>
								<td rowspan="2" class="check"><input type="checkbox" name="item_select[]" value="<?php echo $rec['item_id'] ?>"></td>
								<?php
								if ($item_pay_confirm == 0) {
								?>
									<td class="deadline<?php if ($item_buy_count <= 3) { ?> close<?php } ?>">あと<span><?php echo $item_buy_count ?></span>日</td>
								<?php
								} else {
								?>
									<td class="deadline<?php if ($item_release_count <= 3) { ?> close<?php } ?>">あと<span><?php echo $item_release_count ?></span>日</td>
								<?php
								}
								?>
								<td class="title">
									<?php
									if ($item_pay_confirm == 0) {
										if (!empty($rec['item_url'])) {
									?>
											<a href="<?php echo $rec['item_url'] ?>" target="_blank"><span class="buy_item">購入</span> <?php echo $rec['item_name'] ?></a>
										<?php
										} else {
										?>
											<span class="buy_item">購入</span> <?php echo $rec['item_name'] ?>
										<?php
										}
									} else {
										if (!empty($rec['item_url'])) {
										?>
											<a href="<?php echo $rec['item_url'] ?>" target="_blank"><span class="receive_item">発売日</span> <?php echo $rec['item_name'] ?></a>
										<?php
										} else {
										?>
											<span class="receive_item">発売日</span> <?php echo $rec['item_name'] ?>
									<?php
										}
									}
									?>
									<span class="priority <?php echo $item_priority ?>"><?php echo $rec['item_priority_name'] ?></span>
									<?php
									if ($item_pay_confirm == 0) {
									?>
										<span class="todo_medium"><?php echo $todo_medium ?></span>
									<?php
									} else {
									?>
										<span class="todo_place"><?php echo $todo_place ?></span>
									<?php
									}
									?>
									<br>
									<p class="note sp"><?php echo $rec['item_note'] ?></p>
								</td>
								<td data-label="購入日：" class="startday"><?php echo $item_buy_date ?></td>
								<td data-label="発売日：" class="deadline_date"><?php echo $item_release_date ?><?php if ($item_release_season != 0) { ?><span class="notice">（<?php echo $rec['item_rs'] ?>）</span><?php } ?></td>
								<td rowspan="2" class="change">
									<a class="change_button" href="../item/item_edit.php?id=<?php echo $rec['item_id'] ?>" target="_blank">変更</a>
								</td>
							</tr>
							<tr class="pc">
								<td colspan="4">
									<p class="note"><?php echo $rec['item_note'] ?></p>
								</td>
							</tr>
						</tbody>
					<?php
					}
					?>
				</table>
				<div class="todo_setting">
					<div><input type="submit" name="action" class="icon delete edit_icon" value="delete" disabled><span class="hover">削除</span></div>
					<div><input type="submit" name="action" class="icon postpone edit_icon" value="postpone" disabled><span class="hover">翌月へ延期</span></div>
					<div><input type="submit" name="action" class="icon complete edit_icon" value="complete" disabled><span class="hover">完了</span></div>
					<div><input type="submit" name="action" class="icon copy edit_icon" value="copy" disabled><span class="hover">コピーして新規追加</span></div>
					<div><a href="./item_edit.php" target="_blank"><span class="icon new_item"><span></span></span></a><span class="hover">新規追加</span></div>
				</div>
			</div>
		</form>
	</section>
	<div class="setting_area">
		<div><a href="./setting.php" target="_blank"><span class="icon"><img src="../../assets/img/setting.svg" alt="設定"><span class="hover">設定</span></span></a></div>
		<div><a href="./todo_csv.php"><span class="icon"><img src="../../assets/img/csv.svg" alt="検索"><span class="hover">csv一括登録</span></span></a></div>
		<div><a href=""><span class="icon"><img src="../../assets/img/search.svg" alt="検索"><span class="hover">検索</span></span></a></div>
		<div><a href="./todo_list_edit.php" target="_blank"><span class="icon"><img src="../../assets/img/list.svg" alt="新規リスト"><span class="hover">新規リスト</span></span></a></div>
		<div><a href="./todo_edit.php" target="_blank"><span class="icon"><img src="../../assets/img/todo.svg" alt="新規ToDo"><span class="hover">新規ToDo</span></span></a></div>
	</div>
</body>
<script>
	$('.todo_subtitle').on('click', function() {
		$(this).toggleClass('open');
		$(this).next().fadeToggle();
		$(this).next().next().fadeToggle();
	});
	$('.check input').on('click', function() {
		var check_count = $('table :checked').length;
		if (check_count == 0) {
			$(this).parents('.todo_note').find('.edit_icon').prop('disabled', true);
		} else {
			$(this).parents('.todo_note').find('.edit_icon').prop('disabled', false);
		};
	});

	$('.all_check').on('click', function() {
		$(this).prev('table').find('input[name="todo_select[]"]').prop('checked', this.checked);
		var check_count = $('table :checked').length;
		if (check_count == 0) {
			$(this).parents('.todo_note').find('.edit_icon').prop('disabled', true);
		} else {
			$(this).parents('.todo_note').find('.edit_icon').prop('disabled', false);
		};
	});

	$('input[name="todo_select[]"]').on('click', function() {
		if ($('.check input :checked').length == $('.check input').length) {
			$(this).parents('.todo_note').find('.all_check').prop('checked', true);
		} else {
			$(this).parents('.todo_note').find('.all_check').prop('checked', false);
		}
	});
</script>

</html>