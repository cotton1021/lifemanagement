<?php
	include '../../inc/tool.php';

$title = '買い物-集計期間設定';
get_header($title);

$id = 1;

$db = new DBConnect();
$dbh = $db->getConnection();
    
$sql = "SELECT ISE.*
				FROM item_separate AS ISE
				WHERE ISE.item_sd_id = ?";
$stmt = $dbh -> prepare($sql);
$data[] = $id;
$stmt -> execute($data);

$rec = $stmt -> fetch(PDO::FETCH_ASSOC);

$dbh = null;

$selected_date = $rec['item_separate_date'];

?>
<body id="item" class="item_edit">
<?php
	get_headerMenu();
?>
	<section>
		<h2>集計期間</h2>
		<form method="POST" id="form" action="../../inc/do/change_item_separate.php">
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
			<span id="error_caption"></span>
			</form>
	</section>
</body>
</html>