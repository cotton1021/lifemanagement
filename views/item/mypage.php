<?php
	include '../../inc/tool.php';

	$title = '買い物マイページ';
	get_header($title);

	$today = date('Y-m-d');

	$db = new DBConnect();
	$dbh = $db->getConnection();

	$sql = "SELECT ISE.*
					FROM item_separate AS ISE
					WHERE 1";
	$item_separate = $dbh -> prepare($sql);
	$item_separate -> execute();

	$result = $item_separate->fetch(PDO::FETCH_ASSOC);
	$date_set = $result['item_separate_date'];

	$year = $_GET['year'];
	$this_year = date('Y',strtotime($today."-$date_set day +1 day"));
	$this_month = date('m',strtotime($today."-$date_set day +1 day"));

	/* 当月 */
	$month_start = date($this_year.'-'.$this_month.'-'.$date_set);
	$month_end = date('Y-m-d',strtotime(date('Y-m-t',strtotime(date($month_start)))."+$date_set day -1 day"));

	/* 翌月 */
	$next_month_start = date('Y-m-d',strtotime($month_end."+1 day"));
	$next_month_end = date('Y-m-d',strtotime(date('Y-m-t',strtotime($next_month_start))."+$date_set day -1 day"));

	if($year == ""){
		$year = date('Y',strtotime("-$date_set day"));
		$next_year = date('Y',strtotime("$next_month_end +1 day"));
		$prev_year = date('Y',strtotime("$month_end -1 year"));
	}else{
		$next_year = $year + 1;
		$prev_year = $year - 1;
	}

	/* 翌々月 */
	$future_month_start = date($year.'-m-d',strtotime($next_month_end."+1 day"));
	$future_month_end = date('Y-m-d',strtotime(date('Y-m-t',strtotime($future_month_start))."+$date_set day -1 day"));
	//12月～1月に跨ぐ処理
	if($future_month_start > $future_month_end){
		$future_month_end = date('Y-m-d',strtotime($future_month_end."+1 year"));
	}

	/* アーカイブ */
	$archive_month_start = date($year.'-m-d',strtotime(date('Y-01-01')."+$date_set day -1 day"));
	$archive_month_end = date('Y-m-d',strtotime(date('Y-01-t',strtotime($archive_month_start))."+$date_set day -1 day"));


	$sql = "SELECT IM.*,
					CC.com_cat,
					CG.com_genre,
					IP.item_priority_name,
					IPC.item_pc,
					IRS.item_rs
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
					WHERE item_payment_date BETWEEN ? AND ? AND IM.item_hold_flag = 0 AND IM.item_delete_flag = 0
					ORDER by IM.item_priority, IM.item_payment_date, IM.item_release_date";
	$stmt = $dbh -> prepare($sql);

?>
<body id="item" class="item_main">
<?php
	get_headerMenu();
?>
	<div class="link_area">
		<a class="prev" href="./mypage.php?year=<?php echo $prev_year?>&prev=1">過去の<br class="sp">購入履歴</a>
<?php
	if($_GET['year'] != ""){
?>
		<a class="today" href="./mypage.php">当月・翌月の<br class="sp">購入予定へ</a>
<?php
	}
?>
		<a class="next" href="./mypage.php?year=<?php echo $next_year ?>&next=1"><?php if($_GET['year'] == ""){ echo date('n/j',strtotime($next_month_end)); }?>以降の<br class="sp">購入予定</a>
	</div>
	<form method="post" action="../../inc/do/update_items.php">
<?php
	if($_GET['year'] == ''){
		$date_setting_start = $month_start." 00:00:00";
		$date_setting_end = $month_end." 23:59:59";
		$disp_num = 2;
		$disp_title = "<h2>【当月】".date('n/j',strtotime($month_start))."～".date('n/j',strtotime($month_end))."の購入品</h2>";
	}elseif($_GET['year'] && $_GET['prev'] == 1){
		$date_setting_start = $archive_month_start." 00:00:00";
		$date_setting_end = $archive_month_end." 23:59:59";
		$disp_num = 12;
		$disp_title = "<h2>".date('Y/n/j',strtotime($archive_month_start))."～".date('Y/n/j',strtotime($archive_month_end))."の購入品</h2>";
	}elseif($_GET['year'] && $_GET['next'] == 1){
		$date_setting_start = $future_month_start." 00:00:00";
		$date_setting_end = $future_month_end." 23:59:59";
		$disp_num = 12;
		$disp_title = "<h2>".date('Y/n/j',strtotime($future_month_start))."～".date('Y/n/j',strtotime($future_month_end))."の購入品</h2>";
	}

	$data = [];
	$data[] = $date_setting_start;
	$data[] = $date_setting_end;
	$stmt -> execute($data);

	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	for($i=0;$i<$disp_num;$i++){

		$price_total = 0; /* 金額リセット */
		if(!empty($results)){
			foreach($results as $rec){
				$price_total += $rec['item_price'];
			}
?>
			<section>
			<?php echo $disp_title?>
			<p class="total_price">支出総額：<span><?php echo number_format($price_total);?></span>円</p>
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
			foreach($results as $rec){
				$item_priority_id = $rec['item_priority'];
				$item_priority = "";
				if($item_priority_id == 1){
					$item_priority = "high";
				}elseif($item_priority_id == 2){
					$item_priority = "middle";
				}elseif($item_priority_id == 3){
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
							<input type="checkbox" name="item_select[]" value="<?php echo $rec['item_id']?>">
							<input type="hidden" name="action" id="actionField" value="">
						</td>
						<td data-label="タイトル：" class="title">
<?php
				if(!empty($rec['item_url'])){
?>
								<a href="<?php echo $rec['item_url']?>" target="_blank"><?php echo $rec['item_name']?></a>
<?php
				}else{
?>
									<?php echo $rec['item_name']?>
<?php
				}
?>
									<span class="priority <?php echo $item_priority?>"><?php echo $rec['item_priority_name']?></span><br>
<?php
				if(!empty($rec['item_cat'])){
?>
							<span class="item_cat"><?php echo $rec['com_cat']?></span>
<?php
				}
				if(!empty($rec['item_genre'])){
?>
							<span class="item_genre"><?php echo $rec['com_genre']?></span>
<?php
				}
?>
							<br class="sp">
							<p class="note sp"><?php echo $rec['item_note']?></p>
						</td>
						<td data-label="金額：" class="price"><?php echo number_format($rec['item_price']);?>円<?php if($item_price_confirm > 0){?><span class="notice">（<?php echo $rec['item_pc']?>）</span><?php } ?></td>
						<td data-label="発売日：" class="release"><?php echo $item_release_date?><?php if($item_release_season != 0){?><span class="notice">（<?php echo $rec['item_rs']?>）</span><?php } ?></td>
						<td data-label="購入日：" class="buy_date"><?php echo $item_buy_date?></td>
						<td data-label="決済日：" class="payment<?php if($item_pay_confirm == 0){echo ' not_confirm';}?>"><?php echo $item_payment_date?></td>
						<td rowspan="2" class="change">
							<a class="change_button" href="./item_edit.php?id=<?php echo $rec['item_id']?>" target="_blank">変更</a>
						</td>
					</tr>
					<tr class="pc">
						<td colspan="5">
							<p class="note"><?php echo $rec['item_note']?></p>
						</td>
					</tr>
				</tbody>
<?php
			}
?>
			</table>
		</section>
<?php
		}
		if($_GET['year'] == ''){
			$date_setting_start = $next_month_start." 00:00:00";
			$date_setting_end = $next_month_end." 23:59:59";
			$disp_title = "<h2>【翌月】".date('n/j',strtotime($next_month_start))."～".date('n/j',strtotime($next_month_end))."の購入品</h2>";
		}elseif($_GET['year'] && $_GET['prev'] == 1){
			$archive_month_start = date('Y-m-d',strtotime($archive_month_end."+1 day"));
			$archive_month_end = date('Y-m-d',strtotime(date('Y-m-t',strtotime($archive_month_start))."+$date_set day -1 day"));
			if (strtotime($archive_month_start) >= strtotime($month_start)) {
				break;
			}
			$date_setting_start = $archive_month_start." 00:00:00";
			$date_setting_end = $archive_month_end." 23:59:59";
			$disp_title = "<h2>".date('Y/n/j',strtotime($archive_month_start))."～".date('Y/n/j',strtotime($archive_month_end))."の購入品</h2>";
		}elseif($_GET['year'] && $_GET['next'] == 1){
			$future_month_start = date('Y-m-d',strtotime($future_month_end."+1 day"));
			$future_month_end = date('Y-m-d',strtotime(date('Y-m-t',strtotime($future_month_start))."+$date_set day -1 day"));	
			$date_setting_start = $future_month_start." 00:00:00";
			$date_setting_end = $future_month_end." 23:59:59";
			$disp_title = "<h2>".date('Y/n/j',strtotime($future_month_start))."～".date('Y/n/j',strtotime($future_month_end))."の購入品</h2>";
		}

		$data = [];
		$data[] = $date_setting_start;
		$data[] = $date_setting_end;
		$stmt -> execute($data);

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	}

	$dbh = null;
?>
		<div class="setting_area">
			<div><a href="./setting.php" target="_blank"><span class="icon"><img src="../../assets/img/setting.svg" alt="設定"><span class="hover">設定</span></span></a></div>
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
	$('.total_price').on('click',function(){
		$(this).toggleClass('open');
		$(this).next().fadeToggle();
	});
	$(document).on('change', 'input[name="item_select[]"]', function () {
		const check_count = $('input[name="item_select[]"]:checked').length;
		if(check_count == 0){
			$('.edit_icon').prop('disabled',true);
		}else{
			$('.edit_icon').prop('disabled',false);
		};
	});

	function updateEditIconState() {
		const check_count = $('input[name="item_select[]"]:checked').length;
		$('.edit_icon').prop('disabled', check_count === 0);
	}

	// 初期表示時に一度実行
	$(function () {
		updateEditIconState();
	});

	// チェック状態が変わったら実行
	$(document).on('change', 'input[name="item_select[]"]', function () {
		updateEditIconState();
	});
</script>
</html>