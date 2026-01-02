<?php
include dirname(__FILE__) . '/config.php';

//ディレクトリdb内の全てのファイルをinclude_once
$db_dir = dirname(__FILE__) . '/db';
util_include_php_files($db_dir);

//ディレクトリcommon内の全てのファイルをinclude_once
$db_dir = dirname(__FILE__) . '/common';
util_include_php_files($db_dir);

//ディレクトリ内の全てのファイルをinclude_once
function util_include_php_files($dir)
{
	if (!is_dir($dir)) {
		return;
	}
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if ($file[0] === '.') {
				continue;
			}
			$path = realpath($dir . '/' . $file);
			if (is_dir($path)) {
				util_include_php_files($path);
			} else {
				if (substr($file, -4, 4) === '.php') {
					include_once $path;
				}
			}
		}
		closedir($dh);
	}
}

/* 決済日特定 */
function getSpecificDateInRange($startDateStr, $endDateStr, $targetDay)
{
	$start = new DateTime($startDateStr);
	$end = new DateTime($endDateStr);

	// 開始月の年・月を取得
	$current = clone $start;

	// 開始月から終了月まで、1ヶ月ずつ進めてチェック
	// ※通常、期間が1ヶ月程度なら2回（開始月と終了月）のチェックで済みます
	while ($current->format('Y-m') <= $end->format('Y-m')) {
		$year = $current->format('Y');
		$month = $current->format('m');

		// その月に指定の「日」が存在するかチェック（例：2月に30日は無い）
		if (checkdate((int)$month, $targetDay, (int)$year)) {
			// 該当日付のオブジェクトを作成
			$targetDate = new DateTime("$year-$month-$targetDay");

			// その日付が「開始日 <= ターゲット <= 終了日」の範囲内か判定
			if ($targetDate >= $start && $targetDate <= $end) {
				return $targetDate->format('Y-m-d');
			}
		}
		// 次の月へ移動
		$current->modify('first day of next month');
	}
	return null; // 見つからなかった場合
}

function getPreviousPeriod($referenceDateStr, $startDay)
{
	// 1. 基準日をオブジェクト化
	$refDate = new DateTimeImmutable($referenceDateStr);
	$refYear = (int)$refDate->format('Y');
	$refMonth = (int)$refDate->format('m');
	$refDay = (int)$refDate->format('d');

	// 2. 基準日が含まれる「現在の期間」の開始月を判定する
	// 基準日が start_day より小さければ、期間の開始は「前月」になる
	if ($refDay < $startDay) {
		$currentPeriodStartMonth = $refDate->modify('last month');
	} else {
		$currentPeriodStartMonth = $refDate;
	}

	// 3. 「1ヶ月前の期間」を求めるため、さらに1ヶ月戻す
	$targetPeriodStart = $currentPeriodStartMonth->modify('last month');

	// 4. 開始日を設定 (例: 2023-12-05)
	// ※指定の「日」がその月に存在しない場合（31日など）を考慮し、セット後に調整
	$year = $targetPeriodStart->format('Y');
	$month = $targetPeriodStart->format('m');
	$startDate = new DateTimeImmutable("$year-$month-$startDay");

	// 5. 終了日を計算 (開始日の1ヶ月後 - 1日)
	// 例: 2023-12-05 の1ヶ月後は 2024-01-05。その1日前は 2024-01-04
	$endDate = $startDate->modify('+1 month')->modify('-1 day');

	return [
		'start' => $startDate->format('Y-m-d'),
		'end'   => $endDate->format('Y-m-d')
	];
}
