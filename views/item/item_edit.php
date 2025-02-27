<?php
	include '../../inc/tool.php';

$title = '買い物-アイテム登録';
get_header($title);

$id = $_GET['id'];

$db = new DBConnect();
$dbh = $db->getConnection();
    
$sql = "SELECT IM.*,
				CC.com_cat,
				CG.com_genre,
				IP.item_priority_name
				FROM item_main AS IM
					LEFT JOIN common_cat AS CC
						ON IM.item_cat = CC.com_cat_id
					LEFt JOIN common_genre AS CG
						ON IM.item_genre = CG.com_genre_id
					LEFT JOIN item_priority AS IP
						ON IM.item_priority = IP.item_priority_id
					LEFT JOIN todo_medium AS TM
						ON IM.item_todo_medium = TM.todo_medium_id
					LEFT JOIN todo_place AS TP
						ON IM.item_todo_place = TP.todo_place_id
				WHERE IM.item_id = ?";
$stmt = $dbh -> prepare($sql);
$data[] = $id;
$stmt -> execute($data);

$rec = $stmt -> fetch(PDO::FETCH_ASSOC);

$item_release_date = $rec['item_release_date'];
$item_release_date = (new DateTime($item_release_date))->format("Y-m-d");
$item_buy_date = $rec['item_buy_date'];
$item_buy_date = (new DateTime($item_buy_date))->format("Y-m-d");
$item_payment_date = $rec['item_payment_date'];
$item_payment_date = (new DateTime($item_payment_date))->format("Y-m-d");
$item_pay_confirm = $rec['item_pay_confirm'];
$item_price_confirm = $rec['item_price_confirm'];

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

/* price_confirm */
$sql = "SELECT IPC.*
				FROM item_price_confirm AS IPC
				WHERE 1";
$price_confirm = $dbh -> prepare($sql);
$price_confirm -> execute();

/* release_season */
$sql = "SELECT IRS.*
				FROM item_release_season AS IRS
				WHERE 1";
$release_season = $dbh -> prepare($sql);
$release_season -> execute();

/* medium */
$sql = "SELECT TM.*
				FROM todo_medium AS TM
				WHERE 1";
$todo_medium = $dbh -> prepare($sql);
$todo_medium -> execute();

/* place */
$sql = "SELECT TP.*
				FROM todo_place AS TP
				WHERE 1";
$todo_place = $dbh -> prepare($sql);
$todo_place -> execute();

$dbh = null;

?>
<script>
$(document).ready(function() {
	$("#form").validate({
		rules: {
			item_name: {
				required: true,
			},
			item_price: {
				required: true,
			},
		},
		messages: {
			item_name: "タイトルは必須項目です。",
			item_price: "金額は必須項目です。"
		},
		//表示位置指定
		errorPlacement: function(error, element) {
			switch(element.attr('name')) {
				case "item_name":
					error.insertAfter($('#error_caption'));
					break;
				case "item_price":
					error.insertAfter($('#error_caption'));
					break;
			}
		}
	});
});
</script>
<body id="item" class="item_edit">
<?php
	get_headerMenu();
?>
	<section>
		<h2>アイテム編集</h2>
		<form method="POST" id="form" action="../../inc/do/add_item.php">
			<input type="hidden" name="item_id" value="<?php echo $rec['item_id']?>">
			<table>
				<tr>
					<th>タイトル</th>
					<td><input type="text" name="item_name" value="<?php echo $rec['item_name']?>"></td>
				</tr>
				<tr>
					<th>カテゴリ</th>
					<td>
						<select name="item_cat" id="">
							<option value="">未選択</option>
<?php
	foreach($com_cat as $rec_c){
?>
							<option value="<?php echo $rec_c['com_cat_id']?>" <?php if( $rec_c['com_cat_id'] == $rec['item_cat']){?>selected<?php } ?>><?php echo $rec_c['com_cat']?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>ジャンル</th>
					<td>
						<select name="item_genre" id="">
						<option value="">未選択</option>
<?php
	foreach($com_genre as $rec_g){
?>
							<option value="<?php echo $rec_g['com_genre_id']?>" <?php if( $rec_g['com_genre_id'] == $rec['item_genre']){?>selected<?php } ?>><?php echo $rec_g['com_genre']?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>金額</th>
					<td>
						<input type="number" name="item_price" value="<?php echo $rec['item_price']?>">
<?php
	foreach($price_confirm as $rec_pc){
?>

						<input type="checkbox" name="item_price_confirm" class="item_check price_check" value="<?php echo $rec_pc['item_pc_id']?>" <?php if($rec_pc['item_pc_id'] == $rec['item_price_confirm']){?>checked<?php } ?>><label for="item_price_confirm"><?php echo $rec_pc['item_pc']?></label>
<?php
	}
?>
					</td>
				</tr>
				<tr>
					<th>発売日</th>
					<td>
						<input type="date" name="item_release_date" id="" value="<?php echo $item_release_date?>">
						<select name="item_release_season" class="season_select">
							<option value="">時期（任意）</option>
<?php
	foreach($release_season as $rec_rs){
?>
							<option value="<?php echo $rec_rs['item_rs_id']?>" <?php if($rec_rs['item_rs_id'] == $rec['item_release_season']){?>selected<?php } ?>><?php echo $rec_rs['item_rs']?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>購入日</th>
					<td><input type="date" name="item_buy_date" id="" value="<?php echo $item_buy_date?>"></td>
				</tr>
				<tr>
					<th>決済日</th>
					<td><input type="date" name="item_payment_date" id="" value="<?php echo $item_payment_date?>"><input type="checkbox" name="item_pay_confirm" class="item_check" value="1"<?php if($item_pay_confirm == 1){ echo ' checked';}?>><label for="item_pay_confirm">決済済</label></td>
				</tr>
				<tr>
					<th>購入媒体</th>
					<td>
						<select name="item_todo_medium" id="" class="todo_select">
<?php
	foreach($todo_medium as $rec_tm){
?>
							<option value="<?php echo $rec_tm['todo_medium_id']?>" <?php if($rec_tm['todo_medium_id'] == $rec['item_todo_medium']){?>selected<?php } ?>><?php echo $rec_tm['todo_medium_name']?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>受取場所</th>
					<td>
					<select name="item_todo_place" id="" class="todo_select">
<?php
	foreach($todo_place as $rec_tp){
?>
							<option value="<?php echo $rec_tp['todo_place_id']?>" <?php if($rec_tp['todo_place_id'] == $rec['item_todo_place']){?>selected<?php } ?>><?php echo $rec_tp['todo_place_name']?></option>
<?php
	}
?>
					</td>
				</tr>
				<tr>
					<th>優先度</th>
					<td>
						<select name="item_priority" id="">
							<option value="">未選択</option>
<?php
	foreach($item_priority as $rec_p){
?>
							<option value="<?php echo $rec_p['item_priority_id']?>" <?php if($rec_p['item_priority_id'] == $rec['item_priority']){?>selected<?php } ?>><?php echo $rec_p['item_priority_name']?></option>
<?php
	}
?>
						</select>
					</td>
				</tr>
				<tr>
					<th>URL</th>
					<td><input type="text" name="item_url" value="<?php echo $rec['item_url']?>"></td>
				</tr>
				<tr>
					<th>備考</th>
					<td><textarea name="item_note" id=""><?php echo $rec['item_note']?></textarea></td>
				</tr>
				<tr>
					<th>非表示</th>
					<td class="delete">
						<input type="checkbox" name="item_hold_flag" id="item_hold_flag" value="1" <?php if($rec['item_hold_flag'] == 1){ echo ' checked';} ?>><label for="item_hold_flag">保留</label>
						<input type="checkbox" name="item_delete_flag" id="item_delete_flag" value="1" <?php if($rec['item_delete_flag'] == 1){ echo ' checked';} ?>><label for="item_delete_flag">削除</label>
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
		if($("#item_delete_flag").prop('checked')){
			if(confirm('<?php echo $rec['item_name']?>を削除しますか？')){
				$('#item_delete_flag').prop('checked', true);
			}else{
				return false;
			}
		}
	});
});
</script>
</html>