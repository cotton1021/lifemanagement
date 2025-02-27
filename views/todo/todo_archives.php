<?php
include '../../inc/tool.php';

$title = 'ToDoアーカイブ';
get_header($title);

$today = date('Y-m-d');

$db = new DBConnect();
$dbh = $db->getConnection();

$month = $_GET['month'];
?>

<body id="todo" class="todo_main todo_archives">
<?php
	get_headerMenu();
?>
	<div class="link_area">
		<a class="prev" href="./todo_archives.php">2025年1月の<br class="sp">達成リスト</a>
		<a class="today" href="./mypage.php">ToDoトップへ</a>
		<a class="next" href="./todo_list.php">リスト<br class="sp">一覧へ</a>
	</div>
	<section>
		<h2>ToDo達成リスト（開発中）</h2>
		<div class="list">
			<div class="daily">
				<h3>2025/02/04（木）</h3>
				<ul class="list">
					<li>
						リスト名
						<ul class="task">
							<li>
								タスク名タスク名タスク名タスク名タスク名タスク名タスク名タスク名
								<a class="change_button" href="" target="_blank">修正</a>
							</li>
							<li>
								タスク名
								<a class="change_button" href="" target="_blank">修正</a>
							</li>
							<li>
								タスク名
								<a class="change_button" href="" target="_blank">修正</a>
							</li>
						</ul>
					</li>
					<li>
						リスト名
						<ul class="task">
							<li>
								タスク名
								<a class="change_button" href="" target="_blank">修正</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<div class="daily">
				<h3>2025/02/03（木）</h3>
				<ul class="list">
					<li>
						リスト名
						<ul class="task">
							<li>
								タスク名
								<a class="change_button" href="" target="_blank">修正</a>
							</li>
							<li>
								タスク名
								<a class="change_button" href="" target="_blank">修正</a>
							</li>
						</ul>
					</li>
					<li>
						リスト名
						<ul class="task">
							<li>
								タスク名
								<a class="change_button" href="" target="_blank">修正</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<div class="daily">
				<h3>2025/02/02（木）</h3>
				<ul class="list">
					<li>
						リスト名
						<ul class="task">
							<li>
								タスク名
								<a class="change_button" href="" target="_blank">修正</a>
							</li>
						</ul>
					</li>
					<li>
						リスト名
						<ul class="task">
							<li>
								タスク名
								<a class="change_button" href="" target="_blank">修正</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</section>
	<div class="setting_area">
		<div><a href="./setting.php" target="_blank"><span class="icon"><img src="../../assets/img/setting.svg" alt="設定"><span class="hover">設定</span></span></a></div>
		<div><a href=""><span class="icon"><img src="../../assets/img/search.svg" alt="検索"><span class="hover">検索</span></span></a></div>
		<div><a href="./todo_list_edit.php" target="_blank"><span class="icon"><img src="../../assets/img/list.svg" alt="新規リスト"><span class="hover">新規リスト</span></span></a></div>
		<div><a href="./todo_edit.php" target="_blank"><span class="icon"><img src="../../assets/img/todo.svg" alt="新規ToDo"><span class="hover">新規ToDo</span></span></a></div>
	</div>

	<script>
	</script>

</body>