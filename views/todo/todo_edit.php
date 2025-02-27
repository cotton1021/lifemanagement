<?php
	include '../../inc/tool.php';

$title = 'ToDo-ToDo登録';
get_header($title);

$id = $_GET['id'];
$list = $_GET['list'];

$db = new DBConnect();
$dbh = $db->getConnection();

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
				WHERE TM.todo_id = ?";
$stmt = $dbh -> prepare($sql);
$data[] = $id;
$stmt -> execute($data);

$rec = $stmt -> fetch(PDO::FETCH_ASSOC);

$todo_start = $rec['todo_start'];
$todo_start = (new DateTime($todo_start))->format("Y-m-d");
$todo_deadline = $rec['todo_deadline'];
$todo_deadline = (!empty($todo_deadline)) ? (new DateTime($todo_deadline))->format("Y-m-d") : '';
$todo_complete_date = $rec['todo_complete_date'];
$todo_complete_date = (!empty($todo_complete_date)) ? (new DateTime($todo_complete_date))->format("Y-m-d") : '';
$todo_complete = $rec['todo_complete'];

/* list */
$sql = "SELECT TL.todo_list_id,
				TL.todo_list_name
				FROM todo_list AS TL
				WHERE 1";
$todo_list = $dbh -> prepare($sql);
$todo_list -> execute();

/* medium */
$sql = "SELECT TME.*
				FROM todo_medium AS TME
				WHERE 1";
$todo_med = $dbh -> prepare($sql);
$todo_med -> execute();

/* place */
$sql = "SELECT TP.*
				FROM todo_place AS TP
				WHERE 1";
$todo_pla = $dbh -> prepare($sql);
$todo_pla -> execute();

/* priority */
$sql = "SELECT IP.*
				FROM item_priority AS IP
				WHERE 1";
$item_priority = $dbh -> prepare($sql);
$item_priority -> execute();

?>
<script>
$(document).ready(function() {
	$("#form").validate({
		rules: {
			todo_name: {
				required: true,
			},
			todo_list: {
				required: true,
			},
		},
		messages: {
			todo_name: "タイトルは必須項目です。",
			todo_list: "所属リストは必須項目です。"
		},
		//表示位置指定
		errorPlacement: function(error, element) {
			switch(element.attr('name')) {
				case "todo_name":
					error.insertAfter($('#error_caption'));
					break;
				case "todo_list":
					error.insertAfter($('#error_caption'));
					break;
			}
		}
	});
});
</script>
<body id="item" class="todo_edit">
<?php
	get_headerMenu();
?>
	<section>
		<h2>ToDo編集</h2>
		<form method="POST" id="form" action="../../inc/do/add_todo.php">
			<input type="hidden" name="todo_id" value="<?php echo $rec['todo_id']?>">
			<table>
				<tr>
					<th>タイトル</th>
					<td><input type="text" name="todo_name" value="<?php echo $rec['todo_name']?>"></td>
				</tr>
				<tr>
					<th>所属リスト</th>
					<td>
						<select name="todo_list" class="list_select">
							<option value="">選択してください</option>
<?php
	foreach($todo_list as $rec_l){
?>
							<option value="<?php echo $rec_l['todo_list_id']?>" <?php if($rec_l['todo_list_id'] == $rec['todo_list'] || $rec_l['todo_list_id'] == $list){?>selected<?php } ?>><?php echo $rec_l['todo_list_name']?></option>
<?php
	}
?>
						</select>
						<input type="checkbox" name="todo_roop" class="todo_check" value="1"<?php if($todo_roop == 1){ echo ' checked';}?>><label for="todo_roop">繰り返しタスク（個別指定）</label>
					</td>
				</tr>
				<tr>
					<th>場所</th>
					<td>
						<select name="todo_place" class="todo_select">
							<option value="">未選択</option>
<?php
	foreach($todo_pla as $rec_p){
?>
							<option value="<?php echo $rec_p['todo_place_id']?>" <?php if( $rec_p['todo_place_id'] == $rec['todo_place']){?>selected<?php } ?>><?php echo $rec_p['todo_place_name']?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>媒体</th>
					<td>
						<select name="todo_medium" class="todo_select">
						<option value="">未選択</option>
<?php
	foreach($todo_med as $rec_m){
?>
							<option value="<?php echo $rec_m['todo_medium_id']?>" <?php if( $rec_m['todo_medium_id'] == $rec['todo_medium']){?>selected<?php } ?>><?php echo $rec_m['todo_medium_name']?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>優先度</th>
					<td>
						<select name="todo_priority">
							<option value="">未選択</option>
<?php
	foreach($item_priority as $rec_p){
?>
							<option value="<?php echo $rec_p['item_priority_id']?>" <?php if($rec_p['item_priority_id'] == $rec['todo_priority']){?>selected<?php } ?>><?php echo $rec_p['item_priority_name']?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>優先順位</th>
					<td>
						<input type="number" name="todo_priority_num" value="<?php echo $rec['todo_priority_num']?>">
					</td>
				</tr>
				<tr>
					<th>開始日</th>
					<td>
						<input type="date" name="todo_start" value="<?php echo $todo_start?>">
					</td>
				</tr>
				<tr>
					<th>締切</th>
					<td>
						<input type="date" name="todo_deadline" value="<?php echo htmlspecialchars($todo_deadline, ENT_QUOTES, 'UTF-8'); ?>">
					</td>
				</tr>
				<tr>
					<th>対応完了日</th>
					<td>
						<input type="date" name="todo_complete_date" value="<?php if(isset($todo_complete_date)){ echo htmlspecialchars($todo_complete_date, ENT_QUOTES, 'UTF-8');} ?>">
						<input type="checkbox" name="todo_complete" class="todo_check" value="1"<?php if($todo_complete == 1){ echo ' checked';}?>><label for="todo_complete">完了済</label>
					</td>
				</tr>
				<tr>
					<th>URL</th>
					<td><input type="text" name="todo_url" value="<?php echo $rec['todo_url']?>"></td>
				</tr>
				<tr>
					<th>備考</th>
					<td><textarea name="todo_note"><?php echo $rec['todo_note']?></textarea></td>
				</tr>
				<tr>
					<th>非表示</th>
					<td class="delete">
						<input type="checkbox" name="todo_hold_flag" id="todo_hold_flag" value="1" <?php if($rec['todo_hold_flag'] == 1){ echo ' checked';} ?>><label for="todo_hold_flag">保留</label>
						<input type="checkbox" name="todo_delete_flag" id="todo_delete_flag" value="1" <?php if($rec['todo_delete_flag'] == 1){ echo ' checked';} ?>><label for="todo_delete_flag">削除</label>
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
<script>
	$(function(){
  $('.price_check').on('click', function() {
    if ($(this).prop('checked')){
      // 一旦全てをクリアして再チェックする
      $('.price_check').prop('checked', false);
      $(this).prop('checked', true);
    }
  });
	$('#update').on('click', function() {
		if($("#todo_delete_flag").prop('checked')){
			if(confirm('<?php echo $rec['todo_name']?>を削除しますか？')){
				$('#todo_delete_flag').prop('checked', true);
			}else{
				return false;
			}
		}
	});
});
</script>
</html>