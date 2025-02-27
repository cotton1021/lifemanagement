<?php
	include '../../inc/tool.php';

	$title = '設定';
	get_header($title);
?>
<body id="item" class="setting_main">
<?php
	get_headerMenu();
?>
	<h2>設定</h2>
	<section>
		<ul class="setting_menu">
			<li><a href="./setting_cat.php">カテゴリ設定</a></li>
			<li><a href="./setting_genre.php">ジャンル設定</a></li>
			<li><a href="./setting_relate.php" style="pointer-events: none;">カテゴリ-ジャンル紐付け（準備中）</a></li>
			<li><a href="./setting_separate.php">集計期間</a></li>
		</ul>
	</section>
</html>