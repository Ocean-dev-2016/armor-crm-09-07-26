<?php
require_once __DIR__ . '/include/app.config.loader.php';
$c = armor_get_app_config();
$host = isset($c['db_host']) ? $c['db_host'] : 'localhost';
$user = isset($c['db_user']) ? $c['db_user'] : '';
$pass = isset($c['db_pass']) ? $c['db_pass'] : '';
$name = isset($c['db_name']) ? $c['db_name'] : '';
$ports = isset($c['db_ports']) ? $c['db_ports'] : array(3306);
$conn = null;
foreach ($ports as $port) {
	$conn = @mysqli_connect($host, $user, $pass, $name, (int) $port);
	if ($conn) {
		break;
	}
}
if (!$conn) {
	echo 'DB fail: ' . mysqli_connect_error() . "\n";
	exit(1);
}
$id = 269;
$slug = 'download_cp_customer_order_pdf';
$title = 'CP Customer Order PDF Download';
$url = 'service_channel_partner.php?key=1226&s=269&channel_partner_id=&order_id=';
$r = mysqli_query($conn, "SELECT id FROM api_table WHERE id={$id} LIMIT 1");
if ($r && mysqli_num_rows($r) > 0) {
	mysqli_query($conn, "UPDATE api_table SET api_slug='{$slug}', api_title='{$title}', api_url='{$url}', isDelete=0 WHERE id={$id}");
	echo "UPDATED api {$id}\n";
} else {
	$now = date('Y-m-d H:i:s');
	$desc = '<p>' . $title . '</p>';
	$sql = "INSERT INTO api_table (id,api_slug,api_title,api_description,api_url,author,last_modification_date,isDelete,created_by,created_by_type,created_date)
		VALUES ({$id},'{$slug}','{$title}','{$desc}','{$url}','Armor CRM','{$now}',0,1,0,'{$now}')";
	$ok = mysqli_query($conn, $sql);
	echo $ok ? "INSERTED api {$id}\n" : ('ERR ' . mysqli_error($conn) . "\n");
}
$chk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id,api_slug,isDelete FROM api_table WHERE id=269"));
print_r($chk);
