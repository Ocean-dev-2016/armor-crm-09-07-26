<?php
include __DIR__ . '/menu_permission_label.php';
$conn = mysqli_connect('127.0.0.1', 'root', '', 'kxznassm_armorfire_crm', 3306);
if (!$conn) {
	fwrite(STDERR, mysqli_connect_error() . PHP_EOL);
	exit(1);
}
mysqli_set_charset($conn, 'utf8');
$labels = armor_menu_permission_labels();
$updated = 0;
foreach ($labels as $id => $label) {
	$id = (int) $id;
	$esc = mysqli_real_escape_string($conn, $label);
	$exists = mysqli_query($conn, "SELECT id FROM page_table WHERE id={$id}");
	if ($exists && mysqli_num_rows($exists) > 0) {
		mysqli_query($conn, "UPDATE page_table SET page_title='{$esc}', isDelete=0 WHERE id={$id}");
		$updated++;
	}
}
$inserts = array(
	array(671, 'Remark Analysis Report > Remark Wise Report', 'remark_wise_report', 'remark_wise_report.php'),
	array(672, 'Sales Team Reports > Assign KRA', 'assign_kra', 'assign_kra_manage.php'),
	array(673, 'Sub Master > Quotation/PI Suggested Products', 'quotation_pi_suggest', 'quotation_pi_suggest_product_manage.php'),
	array(674, 'Approval', 'approval', 'approval_manage.php,approval_unlock.php'),
	array(675, 'Sales Team Reports > Customer Order Report', 'customer_order_report', 'customer_order_report.php'),
	array(676, 'Sales Team Reports > Pincode Wise Order Report', 'pincode_order_report', 'pincode_order_report.php'),
	array(677, 'Customer Reports > Re-Order Followup Report', 'reorder_followup_report', 'reorder_followup_report.php'),
	array(678, 'Customer Reports > BRM Customer Assign', 'brm_customer_assign', 'brm_customer_assign_manage.php'),
	array(679, 'Utility > Push Notification', 'push_notification', 'push_notification_manage.php,push_notification_crud.php,push_notification_get_ajax.php,push_notification_ajax_function.php,push_notification_get_type_data.php'),
	array(680, 'Utility > Document List', 'document_list', 'document_list_manage.php,document_list_crud.php,document_list_get_ajax.php'),
);
$inserted = 0;
foreach ($inserts as $row) {
	$id = (int) $row[0];
	$check = mysqli_query($conn, "SELECT id FROM page_table WHERE id={$id}");
	if ($check && mysqli_num_rows($check) > 0) {
		continue;
	}
	$title = mysqli_real_escape_string($conn, $row[1]);
	$slug = mysqli_real_escape_string($conn, $row[2]);
	$urls = mysqli_real_escape_string($conn, $row[3]);
	$sql = "INSERT INTO page_table (id, page_title, page_slug, page_count, page_urls, isActive, isDelete, adate, created_by, created_by_type, created_date) VALUES ({$id}, '{$title}', '{$slug}', 0, '{$urls}', 1, 0, NOW(), 1, 0, NOW())";
	if (!mysqli_query($conn, $sql)) {
		fwrite(STDERR, mysqli_error($conn) . PHP_EOL);
		exit(1);
	}
	$inserted++;
}
echo "updated={$updated} inserted={$inserted}\n";
