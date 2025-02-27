<?php
function get_header($title = ""){
	$base = BASE_URL;
	$site_name = SYSTEM_TITLE;
	if($title != ""){
		if(CMN_DEV_MODE == TRUE){
			$site_name = str_replace("［開発］", "", $site_name);
			$site_title = "［開発］".$title." | ".$site_name;
		}else{
			$site_title = $title." | ".$site_name;
		}
	}else{
		$site_title = $site_name;
	}
	$header = <<< EOF
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex,noarchive,nofollow,noimageindex">
<title>{$site_title}</title>
<link rel="stylesheet" type="text/css" href="{$base}assets/css/normalize.css">
<link rel="stylesheet" type="text/css" href="{$base}assets/css/style.css">
<link rel="stylesheet" type="text/css" media="screen and (min-width:767px)" href="{$base}assets/css/screen_pc.css">
<link rel="stylesheet" type="text/css" media="screen and (max-width:768px)" href="{$base}assets/css/screen_sp.css">
<script src="{$base}assets/lib/jquery-3.7.1.min.js"></script>
<script src="{$base}assets/lib/jquery.validate.js"></script>
</head>
EOF;
	header("Content-Type: text/html; charset=utf-8");
	echo $header;
}

function get_headerMenu(){
	$base = BASE_URL;
	$header_menu = <<< EOF
<header>
	<ul>
		<li class="item_link"><a href="{$base}views/item/mypage.php">買い物</a></li>
		<li class="todo_link"><a href="{$base}views/todo/mypage.php">ToDo</a></li>
		<li class="room_link"><a href="{$base}views/room/mypage.php" style="pointer-events: none;">Room</a></li>
	</ul>
</header>
EOF;
	header("Content-Type: text/html; charset=utf-8");
	echo $header_menu;
}
?>