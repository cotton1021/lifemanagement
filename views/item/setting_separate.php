<?php
	include '../../inc/tool.php';

$title = '買い物-集計期間設定';
get_header($title);

$db = new DBConnect();
$dbh = $db->getConnection();
    
$dateSql = "SELECT ISE.*
				FROM item_separate AS ISE
				WHERE ISE.item_sd_id = 1";
$dateStmt = $dbh -> prepare($dateSql);
$data_d = [];
$dateStmt -> execute($data_d);

$dateRec = $dateStmt -> fetch(PDO::FETCH_ASSOC);

$payDateSql = "SELECT ISE.*
				FROM item_separate AS ISE
				WHERE ISE.item_sd_id = 2";
$payDateStmt = $dbh -> prepare($payDateSql);
$data_p = [];
$payDateStmt -> execute($data_p);

$payDateRec = $payDateStmt -> fetch(PDO::FETCH_ASSOC);

$psSql = "SELECT IPS.*
				FROM item_payment_separate AS IPS
				WHERE IPS.item_delete_flag = 0
				ORDER BY IPS.item_ps_order";
$psStmt = $dbh -> prepare($psSql);
$data_ps = [];
$psStmt -> execute($data_ps);

$psRec = $psStmt -> fetchAll(PDO::FETCH_ASSOC);

$dbh = null;

$selected_date = $dateRec['item_separate_date'];
$selected_payment_date = $payDateRec['item_separate_date'];

?>
<body id="item" class="item_edit separate_setting">
<?php
	get_headerMenu();
?>
	<section>
		<h2>集計期間（開始日）</h2>
		<form method="POST" id="form_date" action="../../inc/do/change_item_separate.php">
			<table>
				<tr>
					<th>集計期間（開始日）</th>
					<td>
						<select name="item_separate_date" id="">
<?php
	for($i=1; $i<=28; $i++){
		$selected = ($i == $selected_date) ? 'selected' : '';
?>
							<option value="<?php echo $i?>"<?php echo $selected ?>><?php echo $i?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
			</table>
			<div class="button_area">
				<input type="submit" id="update" value="更新">
			</div>
		</form>
		<h2>集計期間（決済）</h2>
		<form method="POST" id="form_payment" action="../../inc/do/change_payment_separate.php">
			<table>
				<tr>
					<th>集計期間（決済）</th>
					<td>
						<select name="item_separate_date" id="">
<?php
	for($i=1; $i<=28; $i++){
		$selected = ($i == $selected_payment_date) ? 'selected' : '';
?>
							<option value="<?php echo $i?>"<?php echo $selected ?>><?php echo $i?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
			</table>
			<hr>
			<table id="sortable">
				<thead>
					<tr>
						<th></th>
						<th>名称</th>
						<th>集計開始日</th>
						<th>引落日</th>
						<th>並び順</th>
					</tr>
				</thead>
<?php
	foreach($psRec as $rec){
		$selected_start_date = $rec['item_ps_start'];
		$selected_ps_payment_date = $rec['item_ps_payment'];
?>
				<tbody class="ui-sortable-handle">
					<tr>
						<td class="icon"><img src="../../assets/img/order.svg" alt=""></td>
						<td class="item_ps_name"><?php	echo $rec['item_ps_name'] ?></td>
						<td class="item_ps_start">
							<select name="item_ps_start[<?php echo $rec['item_ps_id'] ?>]" id="item_ps_start">
<?php
		for($i=1; $i<=28; $i++){
			$selected = ($i == $selected_start_date) ? 'selected' : '';
?>
							<option value="<?php echo $i?>"<?php echo $selected ?>><?php echo $i?></option>
<?php
		}
?>
							</select>
						</td>
						<td class="item_ps_payment">
							<select name="item_ps_payment[<?php echo $rec['item_ps_id'] ?>]" id="item_ps_payment">
<?php
		for($i=1; $i<=28; $i++){
			$selected = ($i == $selected_ps_payment_date) ? 'selected' : '';
?>
							<option value="<?php echo $i?>"<?php echo $selected ?>><?php echo $i?></option>
<?php
		}
?>
							</select>
						</td>
						<td class="item_order"><input type="number" name="item_ps_order[<?php echo $rec['item_ps_id'] ?>]" value="<?php echo $rec['item_ps_order'] ?>" readonly></td>
					</tr>
				</tbody>
<?php
	}
?>
			</table>
			<div class="button_area">
				<input type="submit" id="update" value="更新">
			</div>
			<span id="error_caption"></span>
		</form>
	</section>
</body>
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.min.js" integrity="sha256-Fb0zP4jE3JHqu+IBB9YktLcSjI1Zc6J2b6gTjB0LpoM=" crossorigin="anonymous"></script>
<script>
  $(function(){
    $('#sortable').sortable({handle: '.icon',cancel:'thead'});
		$('#sortable').on("sortstop", function(){
      $(this).find('[name^="item_ps_order"]').each(function(idx){
        $(this).val(idx + 1);
      })
    })
  });
</script>
</html>