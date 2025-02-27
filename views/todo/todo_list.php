<?php
include '../../inc/tool.php';

$title = 'ToDoリスト';
get_header($title);

$today = date('Y-m-d');

$db = new DBConnect();
$dbh = $db->getConnection();

$list = $_GET['list'];
$item = $_GET['item'];
?>

<body id="todo" class="todo_list">
<?php
	get_headerMenu();
?>
	<div class="link_area">
		<a class="prev" href="./todo_archives.php">達成リスト</a>
		<a class="next" href="./todo_search.php">リスト検索</a>
	</div>
	<?php
	//ToDoリスト読み込み

	$sql = "SELECT TL.*,
	CC.com_cat,
	CG.com_genre,
	IP.item_priority_name
	FROM todo_list AS TL
		LEFT JOIN common_cat AS CC
			ON TL.todo_cat = CC.com_cat_id
		LEFt JOIN common_genre AS CG
			ON TL.todo_genre = CG.com_genre_id
		LEFT JOIN item_priority AS IP
			ON TL.todo_list_priority = IP.item_priority_id
	WHERE TL.todo_list_hold_flag = 0 AND TL.todo_list_delete_flag = 0 AND TL.todo_list_complete = 0";

	// $_GET['list'] に数値が入っている場合はフィルタを追加
	if (!empty($_GET['list']) && is_numeric($_GET['list'])) {
		$sql .= " AND TL.todo_list_id = :list";
	}

	$sql .= " ORDER BY TL.todo_list_roop DESC, TL.todo_list_priority, TL.todo_list_deadline";

	$stmt = $dbh->prepare($sql);

	$data = [];
	if (!empty($_GET['list']) && is_numeric($_GET['list'])) {
		$data[':list'] = $_GET['list'];
	}

	$stmt->execute($data);

	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	?>
	<section>
		<h2>タスクリスト</h2>
		<?php
		if ($item == "") {
			foreach ($results as $rec) {
				$todo_list_id = $rec['todo_list_id'];
				$todo_list_priority_id = $rec['todo_list_priority'];
				$todo_list_priority = "";
				if ($todo_list_priority_id == 1) {
					$todo_list_priority = "high";
				} elseif ($todo_list_priority_id == 2) {
					$todo_list_priority = "middle";
				} elseif ($todo_list_priority_id == 3) {
					$todo_list_priority = "low";
				}
				$todo_list_start = $rec['todo_list_start'];
				$todo_list_start = (new DateTime($todo_list_start))->format("Y/n/j");
				$todo_list_deadline = $rec['todo_list_deadline'];
				$todo_list_deadline = (new DateTime($todo_list_deadline))->format("Y/n/j");

				$todo_list_roop = $rec['todo_list_roop'];
				if ($todo_list_roop == 1) {
					$todo_list_class = "everyday";
				} else {
					$todo_list_class = "todomain";
				}
		?>
				<form method="post" action="../../inc/do/update_tasks.php">
					<div class="daily todo_note <?php echo $todo_list_class ?>">
						<div class="list_info">
							<h3>
								<?php
								if (!empty($rec['todo_list_url'])) {
								?>
									<a href="<?php echo $rec['todo_list_url'] ?>" target="_blank"><?php echo $rec['todo_list_name'] ?></a>
								<?php
								} else {
								?>
									<?php echo $rec['todo_list_name'] ?>
								<?php
								}
								?>
								<span class="priority <?php echo $todo_list_priority ?>"><?php echo $rec['item_priority_name'] ?></span>
								<div class="tag_area">
									<?php
									if (!empty($rec['todo_cat'])) {
									?>
										<span class="todo_cat"><?php echo $rec['com_cat'] ?></span>
									<?php
									}
									if (!empty($rec['todo_genre'])) {
									?>
										<span class="todo_genre"><?php echo $rec['com_genre'] ?></span>
									<?php
									}
									?>
								</div>
							</h3>
							<div class="list_period">
								<span>開始日：<?php echo $todo_list_start ?></span>
								<span>締切：<?php echo $todo_list_deadline ?></span>
							</div>
							<a class="change_button" href="./todo_list_edit.php?id=<?php echo $rec['todo_list_id'] ?>" target="_blank">リスト情報を編集</a>
						</div>
						<p class="list_note note sp"><?php echo $rec['todo_list_note'] ?></p>
						<?php
						$sql = "SELECT TM.*,
															TME.todo_medium_name,
															TP.todo_place_name,
															IP.item_priority_name
															FROM todo_main AS TM
																LEFT JOIN todo_medium AS TME
																	ON TM.todo_medium = TME.todo_medium_id
																LEFt JOIN todo_place AS TP
																	ON TM.todo_place = TP.todo_place_id
																LEFT JOIN item_priority AS IP
																	ON TM.todo_priority = IP.item_priority_id
															WHERE TM.todo_list = ? AND TM.todo_complete = 0 AND TM.todo_hold_flag = 0 AND TM.todo_delete_flag = 0
															ORDER BY TM.todo_priority_num ASC,
															CASE WHEN TM.todo_deadline IS NULL OR TM.todo_deadline = '' THEN 1 ELSE 0 END ASC,
															TM.todo_deadline ASC,
															TM.todo_priority ASC";
						$stmt_t = $dbh->prepare($sql);
						$data = [];
						$data[] = $todo_list_id;
						$stmt_t->execute($data);

						$results_t = $stmt_t->fetchAll(PDO::FETCH_ASSOC);

						$count_total = 0; /* 件数リセット */
						$count_total = count($results_t);

						?>
						<p class="todo_subtitle open">メイン <?php echo $count_total ?>件</p>
						<div class="todo_detail sortable">
							<?php
							foreach ($results_t as $rec_t) {

								$todo_priority_id = $rec_t['todo_priority'];
								$todo_priority = "";
								if ($todo_priority_id == 1) {
									$todo_priority = "high";
								} elseif ($todo_priority_id == 2) {
									$todo_priority = "middle";
								} elseif ($todo_priority_id == 3) {
									$todo_priority = "low";
								}

								$todo_start = $rec_t['todo_start'];
								$todo_start = (new DateTime($todo_start))->format("Y-m-d");
								$todo_deadline = $rec_t['todo_deadline'];
								$todo_deadline = (!empty($todo_deadline)) ? (new DateTime($todo_deadline))->format("Y-m-d") : '';
								$todo_complete_date = $rec_t['todo_complete_date'];
								$todo_complete_date = (!empty($todo_complete_date)) ? (new DateTime($todo_complete_date))->format("Y-m-d") : '';
								$todo_complete = $rec_t['todo_complete'];

								//あと何日かカウント
								$current_date = new DateTime();
								$todo_deadline_obj = new DateTime($todo_deadline);
								$days_count_d = $current_date->diff($todo_deadline_obj);
								$todo_deadline_count = $days_count_d->invert ? 0 : $days_count_d->days + 1;

							?>
								<table style="display: table;">
									<tbody>
										<tr>
											<td rowspan="2" class="icon"><img src="../../assets/img/order.svg" alt=""></td>
											<td rowspan="2" class="check"><input type="checkbox" name="todo_select[]" value="<?php echo $rec_t['todo_id'] ?>"></td>
											<td class="deadline<?php if ($todo_deadline_count <= 3) { ?> close<?php } ?>">あと<span><?php echo $todo_deadline_count ?></span>日</td>
											<td class="title">
												<?php echo $rec_t['todo_name']; ?><span class="priority <?php echo $todo_priority ?>"><?php echo $rec_t['item_priority_name'] ?></span><br class="sp">
												<div class="tag_area">
													<?php
													if (isset($rec_t['todo_place'])) {
													?>
														<span class="todo_place"><?php echo $rec_t['todo_place_name'] ?></span>
													<?php
													}
													if (isset($rec_t['todo_medium'])) {
													?>
														<span class="todo_medium"><?php echo $rec_t['todo_medium_name'] ?></span>
													<?php
													}
													?>
												</div>
												<br class="sp">
												<p class="note sp"><?php echo $rec_t['todo_note'] ?></p>
												<div class="period_area">
													期間<br class="sp">
													<input type="date" class="startdate" name="todo_start[<?php echo $rec_t['todo_id'] ?>]" value="<?php echo $todo_start ?>"><br class="sp">
													～<br class="sp">
													<input type="date" class="enddate" name="todo_deadline[<?php echo $rec_t['todo_id'] ?>]" value="<?php echo htmlspecialchars($todo_deadline, ENT_QUOTES, 'UTF-8'); ?>">
												</div>
											</td>
											<td class="priority_num">
												優先度　<br class="pc">
												<input type="number" name="todo_priority_num[<?php echo $rec_t['todo_id']; ?>]" id="priority_num" class="priority_num" value="<?php echo $rec_t['todo_priority_num'] ?>">
											</td>
											<td rowspan="2" class="change">
												<a class="change_button" href="./todo_edit.php?id=<?php echo $rec_t['todo_id'] ?>" target="_blank">変更</a>
											</td>
										</tr>
										<tr class="pc">
											<td colspan="5">
												<p class="note"><?php echo $rec_t['todo_note'] ?></p>
											</td>
										</tr>
									</tbody>
								</table>
							<?php
							}
							?>
						</div>
						<input type="checkbox" name="all_check" class="all_check" id="all_<?php echo $todo_list_id; ?>"><label for="all_<?php echo $todo_list_id; ?>">全選択</label>
						<div class="todo_setting">
							<div><a href=""><span class="icon"><img src="../../assets/img/search.svg" alt="検索"></span></a><span class="hover">検索</span></div>
							<div><input type="submit" name="action" class="icon update edit_icon" value="update" disabled><span class="hover">更新</span></div>
							<div><input type="submit" name="action" class="icon delete edit_icon" value="delete" disabled><span class="hover">削除</span></div>
							<div><input type="submit" name="action" class="icon postpone edit_icon" value="postpone" disabled><span class="hover">翌月へ延期</span></div>
							<div><input type="submit" name="action" class="icon complete edit_icon" value="complete" disabled><span class="hover">完了</span></div>
							<div><a href="./todo_edit.php?list=<?php echo $rec['todo_list_id'] ?>" target="_blank"><span class="icon new_item"><span></span></span></a><span class="hover">新規追加</span></div>
						</div>
					</div>
				</form>
			<?php
			}
		}
		if ($list == "") {
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

			$buy_days_deadline = date('Y-m-d H:i:s', strtotime($today . "+7 days"));
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

			$release_days_deadline = date('Y-m-d H:i:s', strtotime($today . "+7 days"));
			$data = [];
			$data[] = $release_days_deadline;
			$stmt->execute($data);

			$results_teceive = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// 結果を統合
			$all_results = array_merge($results, $results_teceive);

			$count_total = 0; /* 件数リセット */
			// 合計件数をカウント
			$count_total = count($all_results);
			?>
			<form method="post" action="../../inc/do/update_items.php">
				<div id="item" class="todo_note shopping">
					<div class="list_info">
						<h3>買い物リスト（1週間）</h3>
						<a class="change_button" href="../item/mypage.php" target="_blank">買い物マイページへ</a>
					</div>
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
										<td class="deadline <?php if ($item_buy_count <= 3) { ?>close<?php } ?>">あと<span><?php echo $item_buy_count ?></span>日</td>
									<?php
									} else {
									?>
										<td class="deadline <?php if ($item_release_count <= 3) { ?>close<?php } ?>">あと<span><?php echo $item_release_count ?></span>日</td>
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
										<div class="tag_area">
											<span class="todo_medium"><?php echo $todo_medium ?></span>
											<span class="todo_place"><?php echo $todo_place ?></span>
										</div>
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
						<div><a href=""><span class="icon"><img src="../../assets/img/search.svg" alt="検索"></span></a><span class="hover">検索</span></div>
						<div><input type="submit" name="action" class="icon delete edit_icon" value="delete" disabled><span class="hover">削除</span></div>
						<div><input type="submit" name="action" class="icon postpone edit_icon" value="postpone" disabled><span class="hover">翌月へ延期</span></div>
						<div><input type="submit" name="action" class="icon complete edit_icon" value="complete" disabled><span class="hover">完了</span></div>
						<div><a href="./item_edit.php" target="_blank"><span class="icon new_item"><span></span></span></a><span class="hover">新規追加</span></div>
					</div>
				</div>
			</form>
		<?php
		}
		?>
	</section>
	<div class="setting_area">
		<div><a href="./setting.php" target="_blank"><span class="icon"><img src="../../assets/img/setting.svg" alt="設定"><span class="hover">設定</span></span></a></div>
		<div><a href="./todo_csv.php"><span class="icon"><img src="../../assets/img/csv.svg" alt="検索"><span class="hover">csv一括登録</span></span></a></div>
		<div><a href=""><span class="icon"><img src="../../assets/img/search.svg" alt="検索"><span class="hover">検索</span></span></a></div>
		<div><a href="./todo_list_edit.php" target="_blank"><span class="icon new_item"><span></span></span><span class="hover">新規リスト</span></a></div>
	</div>
</body>
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.min.js" integrity="sha256-Fb0zP4jE3JHqu+IBB9YktLcSjI1Zc6J2b6gTjB0LpoM=" crossorigin="anonymous"></script>
<script>
	$('.todo_subtitle').on('click', function() {
		$(this).toggleClass('open');
		$(this).nextAll().fadeToggle();
	});
	$(function() {
		$('.sortable').sortable();
		$('.sortable').bind("sortstop", function() {
			$(this).find('[name^="todo_priority_num"]').each(function(idx) {
				$(this).val(idx + 1);
			})
		})
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
		$(this).parents('.todo_note').find('input[name="todo_select[]"]').prop('checked', this.checked);
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