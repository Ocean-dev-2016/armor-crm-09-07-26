<?php
require_once __DIR__ . '/include/app.config.loader.php';
$c = armor_get_app_config();
$conn = null;
foreach ($c['db_ports'] as $port) {
	$conn = @mysqli_connect($c['db_host'], $c['db_user'], $c['db_pass'], $c['db_name'], (int) $port);
	if ($conn) break;
}
if (!$conn) {
	die("DB fail\n");
}
mysqli_set_charset($conn, 'utf8');

$reorderWhere = "isDelete=0 AND REPLACE(REPLACE(LOWER(IFNULL(description,'')), '-', ''), ' ', '') LIKE '%reorder%'";

echo "=== COLUMNS ===\n";
foreach (array('send_to_brm', 'send_to_brm_date', 'send_to_brm_by') as $col) {
	$r = mysqli_query($conn, "SHOW COLUMNS FROM followup LIKE '{$col}'");
	echo $col . ': ' . ($r && mysqli_num_rows($r) ? 'YES' : 'NO') . "\n";
}

echo "\n=== TOTALS ===\n";
$q = function ($sql) use ($conn) {
	$r = mysqli_query($conn, $sql);
	$row = $r ? mysqli_fetch_assoc($r) : array('c' => '?');
	return $row['c'];
};

echo "All Re-Order followups: " . $q("SELECT COUNT(1) c FROM followup WHERE {$reorderWhere}") . "\n";
echo "Admin Panel Re-Order (entry_type=1): " . $q("SELECT COUNT(1) c FROM followup WHERE {$reorderWhere} AND entry_type='1'") . "\n";
echo "send_to_brm=1: " . $q("SELECT COUNT(1) c FROM followup WHERE {$reorderWhere} AND IFNULL(send_to_brm,0)=1") . "\n";
echo "send_to_brm=0: " . $q("SELECT COUNT(1) c FROM followup WHERE {$reorderWhere} AND IFNULL(send_to_brm,0)=0") . "\n";
echo "Pending status=0 + send_to_brm=1: " . $q("SELECT COUNT(1) c FROM followup WHERE {$reorderWhere} AND entry_type='1' AND IFNULL(send_to_brm,0)=1 AND status=0") . "\n";

$today = date('Y-m-d');
echo "\n=== DUE (created+20) vs TODAY {$today} ===\n";
echo "Due today + sent BRM: " . $q("SELECT COUNT(1) c FROM followup WHERE {$reorderWhere} AND entry_type='1' AND IFNULL(send_to_brm,0)=1 AND DATE_ADD(DATE(created_date), INTERVAL 20 DAY)='{$today}'") . "\n";
echo "Due <= today pending + sent BRM: " . $q("SELECT COUNT(1) c FROM followup WHERE {$reorderWhere} AND entry_type='1' AND IFNULL(send_to_brm,0)=1 AND DATE_ADD(DATE(created_date), INTERVAL 20 DAY)<='{$today}' AND (response='' OR response IS NULL OR status=0)") . "\n";

echo "\n=== LAST 15 Re-Order (newest id) ===\n";
$r = mysqli_query($conn, "SELECT id, entry_type, send_to_brm, status, created_date, followup_date,
	DATE_ADD(DATE(created_date), INTERVAL 20 DAY) AS due20, description, LEFT(IFNULL(response,''),40) AS resp
	FROM followup WHERE {$reorderWhere}
	ORDER BY id DESC LIMIT 15");
while ($row = mysqli_fetch_assoc($r)) {
	echo sprintf(
		"#%s | entry=%s | brm=%s | status=%s | created=%s | due20=%s | fu=%s | %s\n",
		$row['id'],
		$row['entry_type'],
		$row['send_to_brm'],
		$row['status'],
		$row['created_date'],
		$row['due20'],
		$row['followup_date'],
		$row['description']
	);
}

echo "\n=== Sample that should show in BRM report (today,pending default) ===\n";
$r = mysqli_query($conn, "SELECT id, created_date,
	DATE_ADD(DATE(created_date), INTERVAL 20 DAY) AS due20, description, send_to_brm, status
	FROM followup
	WHERE {$reorderWhere} AND entry_type='1' AND IFNULL(send_to_brm,0)=1
	AND DATE_ADD(DATE(created_date), INTERVAL 20 DAY) <= '{$today}'
	AND response = ''
	ORDER BY id DESC LIMIT 10");
$n = 0;
while ($row = mysqli_fetch_assoc($r)) {
	$n++;
	echo "#{$row['id']} | created={$row['created_date']} | due={$row['due20']} | brm={$row['send_to_brm']} | {$row['description']}\n";
}
echo "Shown sample count: {$n}\n";
