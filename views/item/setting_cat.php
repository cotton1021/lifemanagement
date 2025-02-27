<?php
	include '../../inc/tool.php';

$title = '買い物-カテゴリ設定';
get_header($title);

$db = new DBConnect();
$dbh = $db->getConnection();
    
$sql = "SELECT CC.*
				FROM common_cat AS CC
				WHERE CC.item_delete_flag = 0
				ORDER by CC.com_cat_order";
$stmt = $dbh -> prepare($sql);
$data = [];
$stmt -> execute($data);

$results = $stmt -> fetchAll(PDO::FETCH_ASSOC);

$dbh = null;

?>
<body id="item" class="item_main setting_list">
<?php
	get_headerMenu();
?>
	<section>
		<h2>カテゴリ設定</h2>
		<form method="POST" id="form" action="../../inc/do/update_cat.php">
			<table id="sortable">
<?php
	foreach($results as $rec){
?>
					<tbody>
						<tr>
							<td class="icon"><img src="../../assets/img/order.svg" alt=""></td>
							<td class="check"><input type="checkbox" name="item_select[]" value="<?php echo $rec['com_cat_id']?>"></td>
							<td class="item_name"><?php	echo $rec['com_cat'] ?></td>
							<td class="item_order"><input type="number" name="com_cat_order[<?php echo $rec['com_cat_id'] ?>]" value="<?php	echo $rec['com_cat_order'] ?>" readonly></td>
							<td class="change">
								<a class="change_button" href="./setting_cat_edit.php?id=<?php echo $rec['com_cat_id']?>">編集</a>
							</td>
						</tr>
					</tbody>
<?php
	}
?>
			</table>
			<div class="setting_area">
				<div><a href="./setting.php"><span class="icon"><img src="../../assets/img/setting.svg" alt="設定"><span class="hover">設定</span></span></a></div>
				<div><input type="submit" name="action" class="icon delete edit_icon" value="delete" disabled><span class="hover">削除</span></div>
				<div><input type="submit" name="action" class="icon complete all_change" value="order_change"><span class="hover">変更確定</span></div>
				<div><a href="./setting_cat_edit.php"><span class="icon new_item"><span></span></span><span class="hover">新規追加</span></a></div>
			</div>
		</form>
	</section>
</body>
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.min.js" integrity="sha256-Fb0zP4jE3JHqu+IBB9YktLcSjI1Zc6J2b6gTjB0LpoM=" crossorigin="anonymous"></script>
<script>
	$(document).on('click',function() {
		var check_count = $('table :checked').length;
		if(check_count == 0){
			$('.edit_icon').prop('disabled',true);
		}else{
			$('.edit_icon').prop('disabled',false);
		};
	});
  $(function(){
    $('#sortable').sortable();
		$('#sortable').bind("sortstop", function(){
      $(this).find('[name^="com_cat_order"]').each(function(idx){
        $(this).val(idx + 1);
      })
    })
  });
</script>
</html>