<?php
	include dirname(__FILE__) . '/config.php';

	//ディレクトリdb内の全てのファイルをinclude_once
	$db_dir = dirname(__FILE__) . '/db';
	util_include_php_files($db_dir);

	//ディレクトリcommon内の全てのファイルをinclude_once
	$db_dir = dirname(__FILE__) . '/common';
	util_include_php_files($db_dir);

	//ディレクトリ内の全てのファイルをinclude_once
	function util_include_php_files($dir){
		if(!is_dir($dir)){
			return;
		}
		if($dh = opendir($dir)){
			while (($file = readdir($dh)) !== false){
				if($file[0] === '.'){
					continue;
				}
				$path = realpath($dir . '/' . $file);
				if(is_dir($path)){
					util_include_php_files($path);
				}else{
					if(substr($file, -4, 4) === '.php'){
						include_once $path;
					}
				}
			}
			closedir($dh);
		}
	}

?>