<?php
	include '../../inc/tool.php';

	$title = '買い物一覧';
	get_header($title);

	$db = new DBConnect();
	$dbh = $db->getConnection();

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
						WHERE 1=1";

	// 検索ボタンが押された場合のみにSQLを実行
	$results = [];
	if (!empty($_POST)) {
		if (!empty($_POST['item_name'])) {
			$sql .= " AND IM.item_name LIKE :item_name";
		}
		$sql .= " ORDER by IM.item_create_date DESC";
		$stmt = $dbh->prepare($sql);

		// タイトル条件がある場合のバインド
		if (!empty($_POST['item_name'])) {
				$stmt->bindValue(':item_name', '%' . $_POST['item_name'] . '%', PDO::PARAM_STR);
		}

		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$count_total = 0;/* カウントリセット */
		$count_total = count($results);

		$price_total = 0; /* 金額リセット */
		foreach($results as $rec){
			$price_total += $rec['item_price'];
		}

	}
?>
<body id="item" class="item_list">
<?php
	get_headerMenu();
?>
	<h2>買い物一覧（開発中）</h2>
	<p style="text-align:center;">タイトル検索機能のみ実装済</p>
	<form method="POSt" action="./item_search.php">
		<div class="search_area">
			<table>
				<tr>
					<th>タイトル</th>
					<td><input type="text" name="item_name" value="<?php echo $_POST['item_name']?>"></td>
					<th>カテゴリ</th>
					<td>
						<select name="" id="">
							<option value="">‐</option>
						</select>
					</td>
					<th>ジャンル</th>
					<td>
						<select name="" id="">
							<option value="">‐</option>
						</select>
					</td>
					<th>金額</th>
					<td><input type="number"> ～ <input type="number"></td>
					<th>決済</th>
					<td>
						<select name="" id="">
							<option value="">‐</option>
							<option value="">未</option>
							<option value="">済</option>
						</select>
					</td>
					<th>優先度</th>
					<td>
						<select name="" id="">
								<option value="">‐</option>
								<option value="">高</option>
								<option value="">中</option>
								<option value="">低</option>
						</select>
					</td>
					<th>発売日</th>
					<td><input type="date" name="" id=""> ～ <input type="date" name="" id=""></td>
					<th>購入日</th>
					<td><input type="date" name="" id=""> ～ <input type="date" name="" id=""></td>
					<th>決済日</th>
					<td><input type="date" name="" id=""> ～ <input type="date" name="" id=""></td>
					<th>URL</th>
					<td><input type="text"></td>
					<th>備考</th>
					<td><input type="text"></td>
					<th>ステータス</th>
					<td>
						<input type="checkbox" name="" id=""><label for="">表示中</label>
						<input type="checkbox" name="" id=""><label for="">保留</label>
						<input type="checkbox" name="" id=""><label for="">削除</label>
					</td>
				</tr>
			</table>
			<div class="button_area">
				<input type="submit" id="search" value="検索">
			</div>
		</div>
		<div class="result_area">
			<p>検索結果 <?php echo number_format($count_total);?> 件</p>
			<div>
				<p>合計金額 <?php echo number_format($price_total);?> 円</p>
				<select name="" id="">
					<option value="">登録降順</option>
					<option value="">優先度順</option>
					<option value="">発売日順</option>
					<option value="">購入日順</option>
					<option value="">決済日順</option>
					<option value="">金額降順</option>
					<option value="">登録昇順</option>
					<option value="">金額昇順</option>
				</select>
			</div>
		</div>
	</form>
	<div class="list_area">
<?php
	if (!empty($_POST)) {
?>				
		<form action="">
			<table>
				<thead>
					<tr>
						<th> </th>
						<th>タイトル</th>
						<th>カテゴリ</th>
						<th>ジャンル</th>
						<th>金額</th>
						<th>優先度</th>
						<th>発売日</th>
						<th>購入日</th>
						<th>決済日</th>
						<th>URL</th>
						<th>登録日</th>
						<th>ステータス</th><!-- ページネーション、検索結果の合計金額 -->
						<th> </th>
					</tr>
				</thead>
<?php
	}
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
		// 日付の変換処理
		$item_release_date = $rec['item_release_date'];
		if ($item_release_date && strtotime($item_release_date)) {
			$item_release_date = (new DateTime($item_release_date))->format("Y/n/j");
		} else {
		$item_release_date = '';
		}
		$item_buy_date = $rec['item_buy_date'];
		if ($item_buy_date && strtotime($item_buy_date)) {
			$item_buy_date = (new DateTime($item_buy_date))->format("Y/n/j");
		} else {
			$item_buy_date = '';
		}
		$item_payment_date = $rec['item_payment_date'];
		if ($item_payment_date && strtotime($item_payment_date)) {
			$item_payment_date = (new DateTime($item_payment_date))->format("Y/n/j");
		} else {
			$item_payment_date = '';
		}
		$item_create_date = $rec['item_create_date'];
		if ($item_create_date && strtotime($item_create_date)) {
			$item_create_date = (new DateTime($item_create_date))->format("Y/n/j");
		} else {
			$item_create_date = '';
		}
		$item_price_confirm = $rec['item_price_confirm'];
		$item_pay_confirm = $rec['item_pay_confirm'];
		$item_release_season = $rec['item_release_season'];

		if($rec['item_delete_flag']==0 && $rec['item_hold_flag']==0){
			$item_flag = "<span class=\"flag disp\">表示</span>";
		}elseif($rec['item_hold_flag']==1){
			$item_flag = "<span class=\"flag hold\">保留</span>";
		}elseif($rec['item_delete_flag']==1){
			$item_flag = "<span class=\"flag delete\">削除</span>";
		}
?>
				<tbody>
					<tr>
						<td rowspan="2" class="check"><input type="checkbox" name="item_select[]" value="<?php echo $rec['item_id']?>"></td>
						<td data-label="タイトル：" class="title"><?php echo $rec['item_name']?></td>
						<td data-label="カテゴリ">
<?php
		if(!empty($rec['item_cat'])){
?>
							<span class="item_cat"><?php echo $rec['com_cat']?></span>
<?php
		}else{
?>
							‐
<?php
		}
?>

						</td>
						<td data-label="ジャンル">
<?php
		if(!empty($rec['item_genre'])){
?>
							<span class="item_genre"><?php echo $rec['com_genre']?></span>
								<?php
		}else{
?>
							‐
<?php
		}
?>
						</td>
						<td data-label="金額：" class="price"><?php echo number_format($rec['item_price']);?>円<?php if($item_price_confirm > 0){?><span class="notice">（<?php echo $rec['item_pc']?>）</span><?php } ?></td>
						<td data-label="優先度："><span class="priority <?php echo $item_priority?>"><?php echo $rec['item_priority_name']?></span></td>
						<td data-label="発売日：" class="release"><?php echo $item_release_date?><?php if($item_release_season != 0){?><span class="notice">（<?php echo $rec['item_rs']?>）</span><?php } ?></td>
						<td data-label="購入日：" class="buy_date"><?php echo $item_buy_date?></td>
						<td data-label="決済日：" class="payment<?php if($item_pay_confirm == 0){echo ' not_confirm';}?>"><?php echo $item_payment_date?></td>
						<td data-label="URL：">
<?php
		if(!empty($rec['item_url'])){
?>
							<a class="item_url" href="<?php echo $rec['item_url']?>">URL</a>
<?php
		}else{
?>
							‐
<?php
		}
?>
						</td>
						<td data-label="登録日：" class="create_date"><?php echo $item_create_date?></td>
						<td data-label="ステータス：" rowspan="2" class="item_flag"><?php echo $item_flag?></td>
						<td rowspan="2" class="change">
							<a class="change_button" href="./item_edit.php?id=<?php echo $rec['item_id']?>" target="_blank">変更</a>
						</td>
					</tr>
					<tr>
						<td colspan="9">
							<p class="note"><?php echo $rec['item_note']?></p>
						</td>
					</tr>
				</tbody>
<?php
	}
?>
			</table>
		</div>
		<div class="setting_area">
			<div><a href="./setting.php" target="_blank"><span class="icon"><img src="../../assets/img/setting.svg" alt="設定"><span class="hover">設定</span></span></a></div>
			<div><a href="./mypage.php"><span class="icon"><img src="../../assets/img/main.svg" alt="マイページ"><span class="hover">マイページへ</span></span></a></div>
			<div><input type="submit" name="action" class="icon delete edit_icon" value="delete" disabled><span class="hover">削除</span></div>
			<div><input type="submit" name="action" class="icon postpone edit_icon" value="postpone" disabled><span class="hover">翌月へ延期</span></div>
			<div><input type="submit" name="action" class="icon complete edit_icon" value="complete" disabled><span class="hover">決済済</span></div>
			<div><a href="./item_edit.php" target="_blank"><span class="icon new_item"><span></span></span><span class="hover">新規追加</span></a></div>
		</div>
	</form>
</body>
<script>
	$(document).on('click',function() {
		var check_count = $('table.item_result :checked').length;
		if(check_count == 0){
			$('.edit_icon').prop('disabled',true);
		}else{
			$('.edit_icon').prop('disabled',false);
		};
	});
</script>
</html>