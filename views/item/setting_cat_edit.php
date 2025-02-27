<?php
	include '../../inc/tool.php';

$title = '買い物-カテゴリ登録';
get_header($title);

$id = $_GET['id'];

$db = new DBConnect();
$dbh = $db->getConnection();
    
$sql = "SELECT CC.*
				FROM common_cat AS CC
				WHERE CC.com_cat_id = ?";
$stmt = $dbh -> prepare($sql);
$data[] = $id;
$stmt -> execute($data);

$rec = $stmt -> fetch(PDO::FETCH_ASSOC);

$dbh = null;

?>
<script>
$(document).ready(function() {
	$("#form").validate({
		rules: {
			com_cat: {
				required: true,
			}
		},
		messages: {
			com_cat: "タイトルは必須項目です。",
		},
		//表示位置指定
		errorPlacement: function(error, element) {
			switch(element.attr('name')) {
				case "com_cat":
					error.insertAfter($('#error_caption'));
					break;
			}
		}
	});
});
</script>
<body id="item" class="item_edit setting_edit">
<?php
	get_headerMenu();
?>
	<section>
		<h2>カテゴリ編集</h2>
		<form method="POST" id="form" action="../../inc/do/add_cat.php">
			<input type="hidden" name="com_cat_id" value="<?php echo $rec['com_cat_id']?>">
			<table>
				<tr>
					<th>タイトル</th>
					<td><input type="text" name="com_cat" value="<?php echo $rec['com_cat']?>"></td>
				</tr>
				<tr>
					<th>並び順</th>
					<td><input type="text" name="com_cat_order" value="<?php echo $rec['com_cat_order']?>"></td>
				</tr>
				<tr>
					<th>削除</th>
					<td><input type="checkbox" name="item_delete_flag" id="item_delete_flag" value="1" <?php if($rec['item_delete_flag'] == 1){ echo ' checked';} ?>></td>
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
			if(confirm('<?php echo $rec['com_cat']?>を削除しますか？')){
				$('#item_delete_flag').prop('checked', true);
			}else{
				return false;
			}
		}
	});
});
</script>
</html>