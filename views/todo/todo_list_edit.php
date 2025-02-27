<?php
	include '../../inc/tool.php';

$title = 'ToDo-ToDoリスト登録';
get_header($title);

$id = $_GET['id'];

$db = new DBConnect();
$dbh = $db->getConnection();

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
				WHERE TL.todo_list_id = ?";
$stmt = $dbh -> prepare($sql);
$data[] = $id;
$stmt -> execute($data);

$rec = $stmt -> fetch(PDO::FETCH_ASSOC);

$todo_list_start = $rec['todo_list_start'];
$todo_list_start = (new DateTime($todo_list_start))->format("Y-m-d");
$todo_list_deadline = $rec['todo_list_deadline'];
$todo_list_deadline = (new DateTime($todo_list_deadline))->format("Y-m-d");
$todo_list_complete_date = $rec['todo_list_complete_date'];
$todo_list_complete_date = !empty($todo_list_complete_date) ? (new DateTime($todo_list_complete_date))->format('Y-m-d 00:00:00') : NULL;
$todo_list_complete = $rec['todo_list_complete'];

/* cat */
$sql = "SELECT CC.*
				FROM common_cat AS CC
				WHERE CC.item_delete_flag = 0
				ORDER by CC.com_cat_order";
$com_cat = $dbh -> prepare($sql);
$com_cat -> execute();

/* genre */
$sql = "SELECT CG.*
				FROM common_genre AS CG
				WHERE CG.item_delete_flag = 0
				ORDER by CG.com_genre_order";
$com_genre = $dbh -> prepare($sql);
$com_genre -> execute();

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
			todo_list_name: {
				required: true,
			},
		},
		messages: {
			todo_list_name: "タイトルは必須項目です。",
		},
		//表示位置指定
		errorPlacement: function(error, element) {
			switch(element.attr('name')) {
				case "todo_list_name":
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
		<h2>ToDoリスト編集</h2>
		<form method="POST" id="form" action="../../inc/do/add_todolist.php">
			<input type="hidden" name="todo_list_id" value="<?php echo $rec['todo_list_id']?>">
			<table>
				<tr>
					<th>タイトル</th>
					<td><input type="text" name="todo_list_name" value="<?php echo $rec['todo_list_name']?>"></td>
				</tr>
				<tr>
					<th>繰り返し</th>
					<td><input type="checkbox" name="todo_list_roop" value="1"<?php if($todo_list_list_roop == 1){ echo ' checked';}?>></td>
				</tr>
				<tr>
					<th>カテゴリ</th>
					<td>
						<select name="todo_cat">
							<option value="">未選択</option>
<?php
	foreach($com_cat as $rec_c){
?>
							<option value="<?php echo $rec_c['com_cat_id']?>" <?php if( $rec_c['com_cat_id'] == $rec['todo_cat']){?>selected<?php } ?>><?php echo $rec_c['com_cat']?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>ジャンル</th>
					<td>
						<select name="todo_genre">
						<option value="">未選択</option>
<?php
	foreach($com_genre as $rec_g){
?>
							<option value="<?php echo $rec_g['com_genre_id']?>" <?php if( $rec_g['com_genre_id'] == $rec['todo_genre']){?>selected<?php } ?>><?php echo $rec_g['com_genre']?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>優先度</th>
					<td>
						<select name="todo_list_priority">
							<option value="">未選択</option>
<?php
	foreach($item_priority as $rec_p){
?>
							<option value="<?php echo $rec_p['item_priority_id']?>" <?php if($rec_p['item_priority_id'] == $rec['todo_list_priority']){?>selected<?php } ?>><?php echo $rec_p['item_priority_name']?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>開始日</th>
					<td>
						<input type="date" name="todo_list_start" value="<?php echo $todo_list_start?>">
					</td>
				</tr>
				<tr>
					<th>締切</th>
					<td>
						<input type="date" name="todo_list_deadline" value="<?php echo $todo_list_deadline?>">
					</td>
				</tr>
				<tr>
					<th>対応完了日</th>
					<td>
						<input type="date" name="todo_list_complete_date" value="<?php if (!empty($todo_list_complete_date)) echo htmlspecialchars($todo_list_complete_date, ENT_QUOTES, 'UTF-8'); ?>">
						<input type="checkbox" name="todo_list_complete" class="todo_check" value="1"<?php if($todo_list_complete == 1){ echo ' checked';}?>><label for="todo_complete">完了済</label>
					</td>
				</tr>
				<tr>
					<th>URL</th>
					<td><input type="text" name="todo_list_url" value="<?php echo $rec['todo_list_url']?>"></td>
				</tr>
				<tr>
					<th>備考</th>
					<td><textarea name="todo_list_note"><?php echo $rec['todo_list_note']?></textarea></td>
				</tr>
				<tr>
					<th>非表示</th>
					<td class="delete">
						<input type="checkbox" name="todo_list_hold_flag" id="todo_hold_flag" value="1" <?php if($rec['todo_list_hold_flag'] == 1){ echo ' checked';} ?>><label for="todo_hold_flag">保留</label>
						<input type="checkbox" name="todo_list_delete_flag" id="todo_delete_flag" value="1" <?php if($rec['todo_list_delete_flag'] == 1){ echo ' checked';} ?>><label for="todo_delete_flag">削除</label>
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
			if(confirm('<?php echo $rec['todo_list_name']?>を削除しますか？')){
				$('#todo_delete_flag').prop('checked', true);
			}else{
				return false;
			}
		}
	});
});
</script>
</html>