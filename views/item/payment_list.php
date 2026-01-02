<?php
include '../../inc/tool.php';

$title = '買い物支払いリスト';
get_header($title);

$today = date('Y-m-d');

$db = new DBConnect();
$dbh = $db->getConnection();

$sql = "SELECT ISE.*
					FROM item_separate AS ISE
					WHERE item_sd_id = :id";
$stmt = $dbh->prepare($sql);

/* id = 1 */
$stmt->execute([':id' => 1]);
$date_set = $stmt->fetchColumn();

/* id = 2 */
$stmt->execute([':id' => 2]);
$date_payment_set = $stmt->fetchColumn();

$month = $_GET['month'];
if ($month == "") {
	$month = date('Y-m');
}
$prev_month = date('Y-m', strtotime($month . '-01 -1 month'));
$next_month = date('Y-m', strtotime($month . '-01 +1 month'));


$sql = "SELECT IM.*,
					CC.com_cat,
					CG.com_genre,
					IP.item_priority_name,
					IPC.item_pc,
					IRS.item_rs,
					IPS.item_ps_name
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
						LEFT JOIN item_payment_separate AS IPS
							ON IM.item_payment_separate = IPS.item_ps_id
					WHERE IM.item_payment_separate = ? AND item_payment_date BETWEEN ? AND ? AND IM.item_hold_flag = 0 AND IM.item_delete_flag = 0
					ORDER by IM.item_priority, IM.item_payment_date, IM.item_release_date";
$stmt = $dbh->prepare($sql);

?>

<body id="item" class="item_main">
	<?php
	get_headerMenu();
	?>
	<div class="link_area">
		<a class="prev" href="./payment_list.php?month=<?php echo $prev_month ?>"><?php echo date('Y年m月', strtotime($prev_month . '-01')); ?>の支払リストへ</a>
		<?php if ($month != date('Y-m')): ?>
			<a class="today" href="./payment_list.php">当月の支払リストへ</a>
		<?php endif; ?>
		<a class="next" href="./payment_list.php?month=<?php echo $next_month ?>"><?php echo date('Y年m月', strtotime($next_month . '-01')); ?>の支払リストへ</a>
	</div>
	<form method="post" action="../../inc/do/update_items.php">
		<?php
	$date_setting_start = $month . "-01 00:00:00";
	$disp_num = 1;
	$disp_title = "<h2>" . date('Y年m月', strtotime($month . '-01')) . "の支払いリスト</h2>";

		for ($i = 0; $i < $disp_num; $i++) {

			$payment_base_date  = $date_setting_start;

			/* 引き落とし期間 */
			$withdraw_start = date('Y-m-' . $date_payment_set, strtotime($payment_base_date));
			$withdraw_end = date('Y-m-d', strtotime($withdraw_start . ' +1 month -1 day'));

			/* 決済手段取得 */
			$sql_p = "SELECT item_ps_id, item_ps_name, item_ps_start, item_ps_payment
						FROM item_payment_separate";
			$stmt_p = $dbh->prepare($sql_p);
			$stmt_p->execute();
			$payment_methods = $stmt_p->fetchAll(PDO::FETCH_ASSOC);

			$price_total = 0;
			$payment_totals = [];

			foreach ($payment_methods as $pm) {

				$ps_id = $pm['item_ps_id'];
				$ps_name = $pm['item_ps_name'];
				$start_day = (int)$pm['item_ps_start'];
				$pay_day   = (int)$pm['item_ps_payment'];

				/* 決済期間を計算 */
				$payment_date = getSpecificDateInRange($withdraw_start, $withdraw_end, $pay_day);
				$payment_term = getPreviousPeriod($payment_date, $start_day);
				$payment_start = $payment_term['start'];
				$payment_end = $payment_term['end'];
				if ($pay_day == 0) {
					$payment_start = $withdraw_start;
					$payment_end = $withdraw_end;
				}

				/* 各決済手段の合計金額 */
				$sql_p = "SELECT SUM(IM.item_price) AS total_price
							FROM item_main AS IM
							WHERE IM.item_payment_separate = ? AND item_payment_date BETWEEN ? AND ? AND IM.item_hold_flag = 0 AND IM.item_delete_flag = 0";
				$stmt_p = $dbh->prepare($sql_p);
				$stmt_p->execute([
					$ps_id,
					$payment_start,
					$payment_end
				]);

				$total = (int)$stmt_p->fetchColumn();

				/* 配列に保存 */
				$payment_totals[$ps_name] = $total;
				$price_total += $total;
			}
		?>
			<section>
					<?php echo $disp_title ?>
					<p class="total_price open">総額：<span><?php echo number_format($price_total); ?></span>円</p>
					<div style="display: block;">
						<?php
						foreach ($payment_totals as $name => $total):
							if ($total > 0) {
								// $pmを取得
								foreach ($payment_methods as $pm) {
									if ($pm['item_ps_name'] == $name) {
										$ps_id = $pm['item_ps_id'];
										$start_day = (int)$pm['item_ps_start'];
										$pay_day = (int)$pm['item_ps_payment'];
										break;
									}
								}
								$payment_date = getSpecificDateInRange($withdraw_start, $withdraw_end, $pay_day);
								$payment_term = getPreviousPeriod($payment_date, $start_day);
								$payment_start = $payment_term['start'];
								$payment_end = $payment_term['end'];
								if ($pay_day == 0) {
									$payment_start = $withdraw_start;
									$payment_end = $withdraw_end;
								}
								$stmt->execute([$ps_id, $payment_start, $payment_end]);
								$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
						?>
								<h3 class="price_detail">
									<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>：
									<span><?php echo number_format($total); ?></span>円
								</h3>
								<table>
									<thead>
										<tr>
											<th> </th>
											<th>タイトル</th>
											<th>金額</th>
											<th>発売日</th>
											<th>購入日</th>
											<th>決済日</th>
											<th> </th>
										</tr>
									</thead>
									<?php
									foreach ($results as $rec) {
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
									?>
										<tbody>
											<tr>
												<td rowspan="2" class="check">
													<input type="checkbox" name="item_select[]" value="<?php echo $rec['item_id'] ?>">
													<input type="hidden" name="action" id="actionField" value="">
												</td>
												<td data-label="タイトル：" class="title">
													<?php
													if (!empty($rec['item_url'])) {
													?>
														<a href="<?php echo $rec['item_url'] ?>" target="_blank"><?php echo $rec['item_name'] ?></a>
													<?php
													} else {
													?>
														<?php echo $rec['item_name'] ?>
													<?php
													}
													?>
													<span class="priority <?php echo $item_priority ?>"><?php echo $rec['item_priority_name'] ?></span><br>
													<?php
													if (!empty($rec['item_cat'])) {
													?>
														<span class="item_cat"><?php echo $rec['com_cat'] ?></span>
													<?php
													}
													if (!empty($rec['item_genre'])) {
													?>
														<span class="item_genre"><?php echo $rec['com_genre'] ?></span>
													<?php
													}
													?>
													<span class="item_ps"><?php echo $rec['item_ps_name'] ?></span>
													<br class="sp">
													<p class="note sp"><?php echo $rec['item_note'] ?></p>
												</td>
												<td data-label="金額：" class="price"><?php echo number_format($rec['item_price']); ?>円<?php if ($item_price_confirm > 0) { ?><span class="notice">（<?php echo $rec['item_pc'] ?>）</span><?php } ?></td>
												<td data-label="発売日：" class="release"><?php echo $item_release_date ?><?php if ($item_release_season != 0) { ?><span class="notice">（<?php echo $rec['item_rs'] ?>）</span><?php } ?></td>
												<td data-label="購入日：" class="buy_date"><?php echo $item_buy_date ?></td>
												<td data-label="決済日：" class="payment
									<?php if ($item_pay_confirm == 0) {
											echo ' not_confirm';
										} ?>"><?php echo $item_payment_date ?></td>
												<td rowspan="2" class="change">
													<a class="change_button" href="./item_edit.php?id=<?php echo $rec['item_id'] ?>" target="_blank">変更</a>
												</td>
											</tr>
											<tr class="pc">
												<td colspan="5">
													<p class="note"><?php echo $rec['item_note'] ?></p>
												</td>
											</tr>
										</tbody>
									<?php
									}
									?>
								</table>
						<?php
							}
						endforeach;
						?>
					</div>
				</section>
		<?php
		}

		$dbh = null;
		?>
		<div class="setting_area">
			<div><a href="./setting.php" target="_blank"><span class="icon"><img src="../../assets/img/setting.svg" alt="設定"><span class="hover">設定</span></span></a></div>
			<div><a href="./mypage.php"><span class="icon"><img src="../../assets/img/main.svg" alt="マイページ"><span class="hover">マイページへ</span></span></a></div>
			<div><a href="./item_search.php"><span class="icon"><img src="../../assets/img/search.svg" alt="検索"><span class="hover">検索</span></span></a></div>
			<div><input type="submit" name="action" class="icon delete edit_icon" value="delete"><span class="hover">削除</span></div>
			<div><input type="submit" name="action" class="icon postpone edit_icon" value="postpone"><span class="hover">翌月へ延期</span></div>
			<div><input type="submit" name="action" class="icon complete edit_icon" value="complete"><span class="hover">決済済</span></div>
			<div><input type="submit" name="action" class="icon copy edit_icon" id="copy_button" value="copy"><span class="hover">コピーして新規登録</span></div>
			<div><a href="./item_edit.php" target="_blank"><span class="icon new_item"><span></span></span><span class="hover">新規追加</span></a></div>
		</div>
	</form>
</body>
<script>
	$('.total_price').on('click', function() {
		$(this).toggleClass('open');
		$(this).next().fadeToggle();
	});
	$(document).on('change', 'input[name="item_select[]"]', function() {
		const check_count = $('input[name="item_select[]"]:checked').length;
		if (check_count == 0) {
			$('.edit_icon').prop('disabled', true);
		} else {
			$('.edit_icon').prop('disabled', false);
		};
	});

	function updateEditIconState() {
		const check_count = $('input[name="item_select[]"]:checked').length;
		$('.edit_icon').prop('disabled', check_count === 0);
	}

	// 初期表示時に一度実行
	$(function() {
		updateEditIconState();
	});

	// チェック状態が変わったら実行
	$(document).on('change', 'input[name="item_select[]"]', function() {
		updateEditIconState();
	});
</script>

</html>