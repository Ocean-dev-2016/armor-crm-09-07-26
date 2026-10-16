<?php
$page_id = 675;
$page_slug = 'customer_order_report';
include("connect.php");
require_once("../include/class.customer_order_report.php");

function cor_h($value)
{
	return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function cor_money($value)
{
	return number_format((float) $value, 2);
}

function cor_date($value)
{
	if ($value === "" || $value === null || $value === "0000-00-00") {
		return "-";
	}
	return date("d/m/Y", strtotime($value));
}

function cor_days_pill($days)
{
	if ($days === null) {
		return '<span class="orp-muted">-</span>';
	}
	$class = $days <= 90 ? "orp-pill-green" : ($days <= 180 ? "orp-pill-orange" : "orp-pill-red");
	$text = $days == 0 ? "Today" : ($days == 1 ? "1 day ago" : $days . " days ago");
	return '<span class="orp-pill ' . $class . '">' . $text . '</span>';
}

$report = new CustomerOrderReport($db);
$filters = $report->normalizeFilters($_REQUEST, $rights);
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "list";

if ($action === "detail") {
	header('Content-Type: application/json; charset=utf-8');
	$lifetime = (isset($_REQUEST['scope']) && $_REQUEST['scope'] === "life");
	$detail = $report->getCustomerOrders(isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : 0, $filters, $lifetime);
	if ($detail['customer'] === "") {
		echo json_encode(array("ack" => 0, "ack_msg" => "Customer not found."));
		exit;
	}
	$rows = array();
	foreach ($detail['rows'] as $row) {
		$rows[] = array(
			$row['order_no'],
			cor_date($row['order_date']),
			$row['source'],
			$row['status'],
			$row['sales_person'],
			cor_money($row['amount']),
		);
	}
	echo json_encode(array(
		"ack" => 1,
		"customer" => $detail['customer'],
		"in_period" => $detail['in_period'] ? 1 : 0,
		"lifetime" => $lifetime ? 1 : 0,
		"period_label" => $lifetime ? "All final orders (lifetime)" : "Period: " . cor_date($filters['from']) . " to " . cor_date($filters['to']),
		"columns" => array("Order No", "Order Date", "Source", "Status", "Sales Person", "Amount"),
		"rows" => $rows,
		"total_label" => "Orders: " . count($rows) . " | Amount: " . CURR . " " . cor_money($detail['total']),
	));
	exit;
}

if ($action === "export") {
	$canExport = (
		(isset($rights['export_excel_flag']) && (int) $rights['export_excel_flag'] === 1)
		|| (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0)
	);
	if (!$canExport) {
		echo "Excel export permission required.";
		exit;
	}
	@set_time_limit(0);
	$data = $report->build($filters, false);
	$fileName = "customer_order_report_" . $filters['from'] . "_to_" . $filters['to'] . ".csv";
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="' . $fileName . '"');
	$out = fopen("php://output", "w");
	fwrite($out, "\xEF\xBB\xBF");
	fputcsv($out, array("Customer Order Report", "Period: " . cor_date($filters['from']) . " to " . cor_date($filters['to'])));
	fputcsv($out, array());
	fputcsv($out, array(
		"Sr.", "Code", "Customer", "Contact Person", "Mobile", "City", "Type", "Sales Person",
		"Period Orders", "Period Amount", "Last Order Date", "Days Since Last Order",
		"Lifetime Orders", "Lifetime Amount", "Status",
	));
	$stateLabels = array("ordered" => "Ordered", "old_only" => "Not Ordered In Period", "never" => "Never Ordered");
	$sr = 0;
	foreach ($data['rows'] as $row) {
		fputcsv($out, array(
			++$sr,
			$row['code'],
			$row['company'],
			$row['person'],
			$row['mobile'],
			$row['city'],
			$row['type'],
			$row['sales_person'],
			$row['period_orders'],
			cor_money($row['period_amount']),
			cor_date($row['last_order_date']),
			$row['days_since'] === null ? "-" : $row['days_since'],
			$row['life_orders'],
			cor_money($row['life_amount']),
			$stateLabels[$row['state']],
		));
	}
	fclose($out);
	exit;
}

$data = $report->build($filters, true);
$summary = $data['summary'];
$startSr = ($data['page'] - 1) * $data['page_size'];
$chips = $report->describeFilters($filters, $rights);
$statusTitles = $report->getStatusFilters();
$active = $filters['order_status'];
$pct = function ($part) use ($summary) {
	return $summary['total'] > 0 ? round(($part * 100) / $summary['total']) . "% of customers" : "";
};
$cards = array(
	array("all", "Total Customers", $summary['total'], "", "fa-users", "", "Everyone in this filter"),
	array("ordered", "Ordered", $summary['ordered'], "orp-card-green", "fa-check-circle", "", $pct($summary['ordered'])),
	array("not_ordered", "Not Ordered", $summary['not_ordered'], "orp-card-red", "fa-times-circle", "", $pct($summary['not_ordered'])),
	array("old_only", "Old Customer, No Order Now", $summary['old_only'], "orp-card-orange", "fa-history", "", "Ordered before, not in period"),
	array("never", "Never Ordered", $summary['never'], "orp-card-red", "fa-ban", "", "No final order ever"),
);
?>
<div class="orp-head">
	<div class="orp-head-title"><i class="fa fa-list-alt"></i> <?php echo cor_h($statusTitles[$active]); ?></div>
	<div class="orp-head-period"><i class="fa fa-calendar"></i> Period: <b><?php echo cor_date($filters['from']); ?></b> to <b><?php echo cor_date($filters['to']); ?></b></div>
</div>
<div class="orp-chips">
	<?php foreach ($chips as $chip) { ?><span class="orp-chip"><?php echo cor_h($chip); ?></span><?php } ?>
</div>

<div class="orp-cards">
	<?php foreach ($cards as $card) { ?>
		<div class="orp-card orp-card-click <?php echo $card[3]; ?><?php echo $active === $card[0] ? " active" : ""; ?>" data-status="<?php echo $card[0]; ?>" title="Click to show these customers">
			<i class="fa <?php echo $card[4]; ?>"></i>
			<div class="orp-card-label"><?php echo cor_h($card[1]); ?></div>
			<div class="orp-card-value"><?php echo (int) $card[2]; ?></div>
			<div class="orp-card-note"><?php echo cor_h($card[6]); ?></div>
		</div>
	<?php } ?>
	<div class="orp-card orp-card-blue">
		<i class="fa fa-inr"></i>
		<div class="orp-card-label">Orders In Period</div>
		<div class="orp-card-value"><?php echo (int) $summary['orders']; ?></div>
		<div class="orp-card-note"><?php echo CURR . " " . cor_money($summary['amount']); ?></div>
	</div>
</div>

<div class="orp-scroll">
	<table class="orp-table">
		<thead>
			<tr class="orp-group">
				<th colspan="4">Customer</th>
				<th colspan="2">Selected Period</th>
				<th colspan="2">Last Order</th>
				<th colspan="2">Lifetime</th>
				<th rowspan="2">Status</th>
			</tr>
			<tr class="orp-cols">
				<th>Sr.</th>
				<th style="min-width:230px;">Customer Name</th>
				<th>City</th>
				<th>Sales Person</th>
				<th>Orders</th>
				<th>Amount</th>
				<th>Date</th>
				<th>Gap</th>
				<th>Orders</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
		<?php if (empty($data['rows'])) { ?>
			<tr><td colspan="11" class="orp-empty"><i class="fa fa-search"></i>No customers found. Try another period or filter.</td></tr>
		<?php } ?>
		<?php foreach ($data['rows'] as $i => $row) {
			if ($row['state'] === "ordered") {
				$badge = '<span class="orp-badge orp-badge-ordered"><i class="fa fa-check"></i>Ordered</span>';
				$rowClass = "";
			} else if ($row['state'] === "old_only") {
				$badge = '<span class="orp-badge orp-badge-old_only"><i class="fa fa-history"></i>No Order Now</span>';
				$rowClass = "orp-row-old";
			} else {
				$badge = '<span class="orp-badge orp-badge-never"><i class="fa fa-ban"></i>Never Ordered</span>';
				$rowClass = "orp-row-never";
			}
		?>
			<tr class="<?php echo $rowClass; ?>">
				<td class="text-center"><?php echo $startSr + $i + 1; ?></td>
				<td>
					<div class="orp-name"><?php echo cor_h($row['company']); ?></div>
					<div class="orp-sub">
						<?php echo $row['code'] !== "" ? cor_h($row['code']) . " | " : ""; ?><?php echo cor_h($row['type']); ?>
						<?php if ($row['person'] !== "") { ?> | <?php echo cor_h($row['person']); ?><?php } ?>
						<?php if ($row['mobile'] !== "") { ?> | <i class="fa fa-phone"></i> <?php echo cor_h($row['mobile']); ?><?php } ?>
					</div>
				</td>
				<td><?php echo $row['city'] !== "" ? cor_h($row['city']) : '<span class="orp-muted">-</span>'; ?></td>
				<td><?php echo cor_h($row['sales_person']); ?></td>
				<td class="text-center orp-sep">
					<?php if ($row['period_orders'] > 0) { ?>
						<a class="orp-link orp-link-green cor-detail orp-num" data-id="<?php echo (int) $row['id']; ?>" title="View orders"><?php echo (int) $row['period_orders']; ?></a>
					<?php } else { ?>
						<span class="orp-muted">0</span>
					<?php } ?>
				</td>
				<td class="text-right"><?php echo $row['period_amount'] > 0 ? cor_money($row['period_amount']) : '<span class="orp-muted">-</span>'; ?></td>
				<td class="text-center orp-sep"><?php echo cor_date($row['last_order_date']); ?></td>
				<td class="text-center"><?php echo cor_days_pill($row['days_since']); ?></td>
				<td class="text-center orp-sep">
					<?php if ($row['life_orders'] > 0) { ?>
						<a class="orp-link orp-link-blue cor-detail" data-id="<?php echo (int) $row['id']; ?>" data-scope="life" title="View all orders"><?php echo (int) $row['life_orders']; ?></a>
					<?php } else { ?>
						<span class="orp-muted">0</span>
					<?php } ?>
				</td>
				<td class="text-right"><?php echo $row['life_amount'] > 0 ? cor_money($row['life_amount']) : '<span class="orp-muted">-</span>'; ?></td>
				<td class="text-center orp-sep"><?php echo $badge; ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>

<div class="orp-pager">
	<span>
		Showing <b><?php echo $data['total_rows'] > 0 ? ($startSr + 1) : 0; ?>–<?php echo $startSr + count($data['rows']); ?></b>
		of <b><?php echo (int) $data['total_rows']; ?></b> customers
	</span>
	<?php if ($data['total_pages'] > 1) { ?>
		<span class="orp-pager-links">
			<button type="button" class="btn btn-sm default cor-page" data-page="<?php echo $data['page'] - 1; ?>" <?php echo $data['page'] <= 1 ? "disabled" : ""; ?>>&laquo; Prev</button>
			Page <b><?php echo (int) $data['page']; ?></b> of <?php echo (int) $data['total_pages']; ?>
			<button type="button" class="btn btn-sm default cor-page" data-page="<?php echo $data['page'] + 1; ?>" <?php echo $data['page'] >= $data['total_pages'] ? "disabled" : ""; ?>>Next &raquo;</button>
		</span>
	<?php } ?>
</div>
<div class="orp-legend">
	<b>Gap since last order:</b>
	<span><span class="orp-pill orp-pill-green">0–90 days</span> Active</span>
	<span><span class="orp-pill orp-pill-orange">91–180 days</span> Follow up</span>
	<span><span class="orp-pill orp-pill-red">180+ days</span> Lost / Inactive</span>
	&nbsp; Click any order count to see the orders.
</div>
<?php require_once("disconnect.php"); ?>
