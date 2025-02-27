<?php
include '../../inc/tool.php';

$title = 'ToDo一括登録';
get_header($title);

?>
<body id="todo" class="todo_csv">
<?php
get_headerMenu();
?>
	<section>
		<h2>ToDoタスク：CSV一括登録</h2>
		<form method="POST" action="../../inc/do/add_todo_csv.php" enctype="multipart/form-data">
			<input type="file" name="todo_csv_file"><br>
			<button type="submit">アップロード</button>
			<a href="../../assets/csv/todo_csv.csv" download="todo_csv.csv">空のcsvデータをダウンロード</a>
		</form>
	</section>
</body>

</html>