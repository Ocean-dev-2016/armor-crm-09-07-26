<?php
$page_id = 676;
$page_slug = 'pincode_order_report';
include("connect.php");
require_once("../include/class.customer_order_report.php");

function por_h($value)
{
	return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function por_money($value)
{
	return number_format((float) $value, 2);
}

function por_date($value)
{
	if ($value === "" || $value === null || $value === "0000-00-00") {
		return "-";
	}
	return date("d/m/Y", strtotime($value));
}

function por_days_pill($days)
{
	if ($days === null) {
		return '<span class="orp-muted">No order yet</span>';
	}
	$class = $days <= 90 ? "orp-pill-green" : ($days <= 180 ? "orp-pill-orange" : "orp-pill-red");
	$text = $days == 0 ? "Today" : ($days == 1 ? "1 day ago" : $days . " days ago");
	return '<span class="orp-pill ' . $class . '">' . $text . '</span>';
}

/* Clickable count that opens the customer popup with a pre-selected tab. */
function por_count_link($count, $key, $label, $tab, $colorClass)
{
	$count = (int) $count;
	if ($count <= 0) {
		return '<span class="orp-muted">0</span>';
	}
	return '<a class="orp-link ' . $colorClass . ' orp-num por-customers" data-key="' . por_h($key) . '" data-label="' . por_h($label)
		. '" data-tab="' . $tab . '" title="Click to see these customers">' . $count . '</a>';
}

$report = new CustomerOrderReport($db);
$filters = $report->normalizeAreaFilters($_REQUEST, $rights);
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "list";
$isState = ($filters['group_by'] === "state");

if ($action === "customers") {
	header('Content-Type: application/json; charset=utf-8');
	$areaKey = isset($_REQUEST['area_key']) ? trim((string) $_REQUEST['area_key']) : "";
	$areaLabel = isset($_REQUEST['area_label']) ? trim((string) $_REQUEST['area_label']) : "";
	if ($areaKey === "") {
		echo json_encode(array("ack" => 0, "ack_msg" => "Area not selected."));
		exit;
	}
	if ($isState) {
		$filters['state'] = $areaKey;
	} else {
		$filters['pincode'] = $areaKey;
	}
	$filters['sort'] = "last_order_asc";
	$data = $report->build($filters, false);
	$stateLabels = array("ordered" => "Ordered", "old_only" => "No Order Now", "never" => "Never Ordered");
	$rows = array();
	foreach ($data['rows'] as $row) {
		$rows[] = array(
			"id" => $row['id'],
			"company" => $row['company'],
			"person" => $row['person'],
			"mobile" => $row['mobile'],
			"city" => $row['city'],
			"sales_person" => $row['sales_person'],
			"period_orders" => (int) $row['period_orders'],
			"period_amount" => por_money($row['period_amount']),
			"last_order" => por_date($row['last_order_date']),
			"state" => $row['state'],
			"state_label" => $stateLabels[$row['state']],
			/* legacy cells kept for safety */
			"cells" => array(
				$row['company'] . ($row['person'] !== "" ? " (" . $row['person'] . ")" : ""),
				$row['mobile'],
				$row['city'],
				$row['sales_person'],
				$row['period_orders'],
				por_money($row['period_amount']),
				por_date($row['last_order_date']),
			),
		);
	}
	if ($areaLabel === "") {
		$areaLabel = ($areaKey === CustomerOrderReport::AREA_NONE) ? "Not Set" : $areaKey;
	}
	$sum = $data['summary'];
	echo json_encode(array(
		"ack" => 1,
		"title" => ($isState ? "State: " : "Pincode: ") . $areaLabel,
		"period_label" => por_date($filters['from']) . " to " . por_date($filters['to']),
		"orders_total" => (int) $sum['orders'],
		"amount_total" => CURR . " " . por_money($sum['amount']),
		"columns" => array("Customer", "Mobile", "City", "Sales Person", "Period Orders", "Period Amount", "Last Order"),
		"rows" => $rows,
		"counts" => array(
			"all" => $sum['total'],
			"ordered" => $sum['ordered'],
			"not_ordered" => $sum['not_ordered'],
			"never" => $sum['never'],
		),
		"total_label" => "Period Orders: " . $sum['orders'] . " | Amount: " . CURR . " " . por_money($sum['amount']),
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
	$data = $report->buildArea($filters, false);
	$fileName = ($isState ? "state" : "pincode") . "_wise_order_report_" . $filters['from'] . "_to_" . $filters['to'] . ".csv";
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="' . $fileName . '"');
	$out = fopen("php://output", "w");
	fwrite($out, "\xEF\xBB\xBF");
	fputcsv($out, array(($isState ? "State" : "Pincode") . " Wise Order Report", "Period: " . por_date($filters['from']) . " to " . por_date($filters['to'])));
	fputcsv($out, array());
	$head = $isState
		? array("Sr.", "State", "Pincodes")
		: array("Sr.", "Pincode", "City", "State");
	$head = array_merge($head, array("Total Customers", "Ordered", "Not Ordered", "Never Ordered", "Coverage %", "Period Orders", "Period Amount", "Last Order Date", "Days Since"));
	fputcsv($out, $head);
	$sr = 0;
	foreach ($data['rows'] as $row) {
		$line = $isState
			? array(++$sr, $row['label'], $row['pincode_count'])
			: array(++$sr, $row['label'], $row['city'], $row['state']);
		fputcsv($out, array_merge($line, array(
			$row['customers'], $row['ordered'], $row['not_ordered'], $row['never'], $row['coverage'],
			$row['orders'], por_money($row['amount']), por_date($row['last_order_date']),
			$row['days_since'] === null ? "-" : $row['days_since'],
		)));
	}
	fclose($out);
	exit;
}

$data = $report->buildArea($filters, true);
$summary = $data['summary'];
$startSr = ($data['page'] - 1) * $data['page_size'];
$areaWord = $isState ? "States" : "Pincodes";
$areaOne = $isState ? "State" : "Pincode";
$colCount = $isState ? 12 : 13;
$chips = $report->describeFilters($filters, $rights);
$areaStatuses = $report->getAreaStatusFilters();
$active = $filters['area_status'];
$coverageAll = $summary['customers'] > 0 ? round(($summary['ordered'] * 100) / $summary['customers'], 1) : 0;
$stateName = ($filters['state'] === CustomerOrderReport::AREA_NONE) ? "Not Set" : $filters['state'];
?>
<div class="orp-head">
	<div class="orp-head-title"><i class="fa <?php echo $isState ? "fa-globe" : "fa-map-marker"; ?>"></i> <?php echo $areaOne; ?> Wise — <?php echo por_h($areaStatuses[$active]); ?></div>
	<div class="orp-head-period"><i class="fa fa-calendar"></i> Period: <b><?php echo por_date($filters['from']); ?></b> to <b><?php echo por_date($filters['to']); ?></b></div>
</div>

<?php if (!$isState && $filters['state'] !== "") { ?>
	<div class="orp-breadcrumb">
		<a href="javascript:;" class="por-back-states"><i class="fa fa-globe"></i> All States</a>
		&nbsp;<i class="fa fa-angle-right"></i>&nbsp; <b><?php echo por_h($stateName); ?></b> pincodes
		&nbsp; <a href="javascript:;" class="por-clear-state" style="font-weight:normal;">(show all pincodes)</a>
	</div>
<?php } ?>

<div class="orp-chips">
	<?php foreach ($chips as $chip) { ?><span class="orp-chip"><?php echo por_h($chip); ?></span><?php } ?>
</div>

<div class="orp-cards">
	<div class="orp-card orp-card-click<?php echo $active === "all" ? " active" : ""; ?>" data-area-status="all" title="Click to show all">
		<i class="fa <?php echo $isState ? "fa-globe" : "fa-map-marker"; ?>"></i>
		<div class="orp-card-label">Total <?php echo $areaWord; ?></div>
		<div class="orp-card-value"><?php echo (int) $summary['areas']; ?></div>
		<div class="orp-card-note">With at least one customer</div>
	</div>
	<div class="orp-card orp-card-green orp-card-click<?php echo $active === "with_orders" ? " active" : ""; ?>" data-area-status="with_orders" title="Click to show these">
		<i class="fa fa-check-circle"></i>
		<div class="orp-card-label"><?php echo $areaWord; ?> With Order</div>
		<div class="orp-card-value"><?php echo (int) $summary['areas'] - (int) $summary['areas_no_order']; ?></div>
		<div class="orp-card-note">Order received in period</div>
	</div>
	<div class="orp-card orp-card-red orp-card-click<?php echo $active === "no_orders" ? " active" : ""; ?>" data-area-status="no_orders" title="Click to show these">
		<i class="fa fa-times-circle"></i>
		<div class="orp-card-label"><?php echo $areaWord; ?> With No Order</div>
		<div class="orp-card-value"><?php echo (int) $summary['areas_no_order']; ?></div>
		<div class="orp-card-note">Not a single order in period</div>
	</div>
	<div class="orp-card">
		<i class="fa fa-users"></i>
		<div class="orp-card-label">Customers</div>
		<div class="orp-card-value"><?php echo (int) $summary['customers']; ?></div>
		<div class="orp-card-note">
			<span style="color:#2f6f44;font-weight:700;"><?php echo (int) $summary['ordered']; ?> ordered</span> /
			<span style="color:#d9534f;font-weight:700;"><?php echo (int) $summary['not_ordered']; ?> not</span>
			(<?php echo $coverageAll; ?>%)
		</div>
	</div>
	<div class="orp-card orp-card-blue">
		<i class="fa fa-inr"></i>
		<div class="orp-card-label">Orders In Period</div>
		<div class="orp-card-value"><?php echo (int) $summary['orders']; ?></div>
		<div class="orp-card-note"><?php echo CURR . " " . por_money($summary['amount']); ?></div>
	</div>
</div>

<div class="orp-scroll">
	<table class="orp-table">
		<thead>
			<tr class="orp-group">
				<th colspan="<?php echo $isState ? 3 : 4; ?>"><?php echo $areaOne; ?></th>
				<th colspan="4">Customers</th>
				<th rowspan="2">Order Coverage</th>
				<th colspan="2">Selected Period</th>
				<th rowspan="2">Last Order</th>
				<th rowspan="2">Action</th>
			</tr>
			<tr class="orp-cols">
				<th>Sr.</th>
				<?php if ($isState) { ?>
					<th style="min-width:180px;">State</th>
					<th>Pincodes</th>
				<?php } else { ?>
					<th>Pincode</th>
					<th style="min-width:150px;">City</th>
					<th>State</th>
				<?php } ?>
				<th>Total</th>
				<th>Ordered</th>
				<th>Not Ordered</th>
				<th>Never</th>
				<th>Orders</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
		<?php if (empty($data['rows'])) { ?>
			<tr><td colspan="<?php echo $colCount; ?>" class="orp-empty"><i class="fa fa-search"></i>No <?php echo strtolower($areaWord); ?> found. Try another period or filter.</td></tr>
		<?php } ?>
		<?php foreach ($data['rows'] as $i => $row) {
			$barClass = $row['coverage'] >= 50 ? "orp-bar-green" : ($row['coverage'] > 0 ? "orp-bar-orange" : "orp-bar-red");
			$rowClass = $row['ordered'] == 0 ? "orp-row-never" : "";
			$key = $row['key'];
			$label = $row['label'];
		?>
			<tr class="<?php echo $rowClass; ?>">
				<td class="text-center"><?php echo $startSr + $i + 1; ?></td>
				<?php if ($isState) { ?>
					<td><span class="orp-name"><?php echo por_h($label); ?></span></td>
					<td class="text-center"><?php echo (int) $row['pincode_count']; ?></td>
				<?php } else { ?>
					<td><span class="orp-name"><?php echo por_h($label); ?></span></td>
					<td><?php echo por_h($row['city']); ?></td>
					<td><?php echo por_h($row['state']); ?></td>
				<?php } ?>
				<td class="text-center orp-sep"><?php echo por_count_link($row['customers'], $key, $label, "all", "orp-link-blue"); ?></td>
				<td class="text-center"><?php echo por_count_link($row['ordered'], $key, $label, "ordered", "orp-link-green"); ?></td>
				<td class="text-center"><?php echo por_count_link($row['not_ordered'], $key, $label, "not_ordered", "orp-link-red"); ?></td>
				<td class="text-center"><?php echo por_count_link($row['never'], $key, $label, "never", "orp-link-red"); ?></td>
				<td class="orp-sep">
					<div class="orp-bar"><div class="orp-bar-fill <?php echo $barClass; ?>" style="width:<?php echo max(2, min(100, $row['coverage'])); ?>%;"></div></div>
					<small><b><?php echo (int) $row['ordered']; ?></b> of <?php echo (int) $row['customers']; ?> (<?php echo $row['coverage']; ?>%)</small>
				</td>
				<td class="text-center orp-sep"><?php echo $row['orders'] > 0 ? (int) $row['orders'] : '<span class="orp-muted">0</span>'; ?></td>
				<td class="text-right"><?php echo $row['amount'] > 0 ? por_money($row['amount']) : '<span class="orp-muted">-</span>'; ?></td>
				<td class="text-center orp-sep">
					<?php echo por_date($row['last_order_date']); ?><br>
					<?php echo por_days_pill($row['days_since']); ?>
				</td>
				<td class="text-center orp-sep" style="white-space:nowrap;">
					<button type="button" class="btn btn-xs blue por-customers" data-key="<?php echo por_h($key); ?>" data-label="<?php echo por_h($label); ?>" data-tab="all"><i class="fa fa-users"></i> Customers</button>
					<?php if ($isState) { ?>
						<button type="button" class="btn btn-xs green por-drill" data-key="<?php echo por_h($key === CustomerOrderReport::AREA_NONE ? CustomerOrderReport::AREA_NONE : $label); ?>"><i class="fa fa-map-marker"></i> Pincodes</button>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>

<div class="orp-pager">
	<span>
		Showing <b><?php echo $data['total_rows'] > 0 ? ($startSr + 1) : 0; ?>–<?php echo $startSr + count($data['rows']); ?></b>
		of <b><?php echo (int) $data['total_rows']; ?></b> <?php echo strtolower($areaWord); ?>
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
	<b>Coverage:</b>
	<span><span class="orp-pill orp-pill-green">50%+</span> Good</span>
	<span><span class="orp-pill orp-pill-orange">1–49%</span> Needs focus</span>
	<span><span class="orp-pill orp-pill-red">0%</span> No order from this <?php echo strtolower($areaOne); ?></span>
	&nbsp; Click any customer count to see those customers.
</div>
<?php require_once("disconnect.php"); ?>
