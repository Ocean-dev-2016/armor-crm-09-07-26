<?php
/**
 * Admin — Channel Partner wise sales report (CP → end customer item sales).
 */
$page_id = 565;
$page_slug = 'channel_partner_sales_report';
$page = 'channel_partner_sales_report';
$main_page = 'channel_partner';
$page_title = "CP Sales Report";
$page_hierarchy = array(
	array("link" => "", "title" => "Sales & Marketing"),
	array("link" => "executive_manage.php?flag=channel_partner", "title" => "Channel Partner"),
	array("link" => "channel_partner_sales_report.php", "title" => $page_title)
);
include("connect.php");

if (function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db)) {
	$db->rp_location("channel_partner_ledger.php");
}

$from = isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : date('01-m-Y');
$to = isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : date('d-m-Y');
$cpFilter = isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0;

$fromSql = date('Y-m-d', strtotime($from));
$toSql = date('Y-m-d', strtotime($to));

$where = "o.isDelete=0 AND o.channel_partner_order_flag=1 AND o.cp_order_mode='customer' AND o.channel_partner_customer_id>0";
$where .= " AND DATE(o.order_date) >= '" . mysqli_real_escape_string($db->myconn, $fromSql) . "'";
$where .= " AND DATE(o.order_date) <= '" . mysqli_real_escape_string($db->myconn, $toSql) . "'";
if ($cpFilter > 0) {
	$where .= " AND o.customer_id='" . $cpFilter . "'";
}

$sql = "SELECT o.customer_id AS cp_id,
	e.company_name AS cp_name,
	e.client_code AS cp_code,
	COUNT(DISTINCT o.id) AS order_cnt,
	COUNT(DISTINCT o.channel_partner_customer_id) AS party_cnt,
	COALESCE(SUM(o.grand_total),0) AS sales_amt,
	COALESCE(SUM(CASE WHEN o.payment_received_flag=1 THEN o.payment_received_amount ELSE 0 END),0) AS received_amt
	FROM orders o
	LEFT JOIN executive e ON e.id=o.customer_id
	WHERE $where
	GROUP BY o.customer_id, e.company_name, e.client_code
	ORDER BY e.company_name ASC";
$res = mysqli_query($db->myconn, $sql);

$detailSql = "SELECT o.id, o.order_no, o.order_date, o.grand_total, o.payment_received_flag, o.payment_received_amount,
	o.customer_id AS cp_id, e.company_name AS cp_name,
	c.company_name AS party_name,
	GROUP_CONCAT(CONCAT(opi.pro_name, ' x', opi.pro_qty) SEPARATOR '; ') AS items
	FROM orders o
	LEFT JOIN executive e ON e.id=o.customer_id
	LEFT JOIN channel_partner_customer c ON c.id=o.channel_partner_customer_id
	LEFT JOIN order_product_item opi ON opi.order_id=o.id AND opi.isDelete=0
	WHERE $where
	GROUP BY o.id
	ORDER BY o.id DESC
	LIMIT 500";
$detailRes = mysqli_query($db->myconn, $detailSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title"><h1><?php $db->pageBar($page_hierarchy); ?></h1></div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="portlet light">
				<div class="portlet-title"><div class="caption"><i class="fa fa-bar-chart"></i> Channel Partner → Customer Sales</div></div>
				<div class="portlet-body">
					<form class="form-inline" method="get" style="margin-bottom:15px;">
						<div class="form-group">
							<label>From</label>
							<input type="text" class="form-control date-picker" name="from_date" value="<?php echo htmlspecialchars($from); ?>" readonly>
						</div>
						<div class="form-group">
							<label>To</label>
							<input type="text" class="form-control date-picker" name="to_date" value="<?php echo htmlspecialchars($to); ?>" readonly>
						</div>
						<div class="form-group">
							<label>Channel Partner</label>
							<select name="cp_id" class="form-control">
								<option value="0">All</option>
								<?php
								$cps = $db->rp_getData("executive", "id,company_name,client_code", "channel_partner_flag=1 AND customer_flag=0 AND isDelete=0", "company_name ASC", 0);
								if ($cps) {
									while ($cp = mysqli_fetch_assoc($cps)) {
										$sel = ($cpFilter == (int) $cp['id']) ? 'selected' : '';
										echo '<option value="' . (int) $cp['id'] . '" ' . $sel . '>' . htmlspecialchars($cp['company_name'] . ' (' . $cp['client_code'] . ')') . '</option>';
									}
								}
								?>
							</select>
						</div>
						<button type="submit" class="btn blue">Filter</button>
						<a class="btn default" href="channel_partner_ledger.php">Open Ledger</a>
					</form>

					<h4>CP-wise Summary</h4>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Sr</th>
								<th>Channel Partner</th>
								<th>Code</th>
								<th>Orders</th>
								<th>Parties</th>
								<th>Sales Amount</th>
								<th>Payment Received</th>
								<th>Balance</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						<?php
						$sr = 0;
						$totalSales = 0;
						$totalRecv = 0;
						$has = false;
						if ($res) {
							while ($r = mysqli_fetch_assoc($res)) {
								$has = true;
								$sr++;
								$bal = (float) $r['sales_amt'] - (float) $r['received_amt'];
								$totalSales += (float) $r['sales_amt'];
								$totalRecv += (float) $r['received_amt'];
								?>
								<tr>
									<td><?php echo $sr; ?></td>
									<td><?php echo htmlspecialchars($r['cp_name']); ?></td>
									<td><?php echo htmlspecialchars($r['cp_code']); ?></td>
									<td><?php echo (int) $r['order_cnt']; ?></td>
									<td><?php echo (int) $r['party_cnt']; ?></td>
									<td><?php echo number_format((float) $r['sales_amt'], 2); ?></td>
									<td><?php echo number_format((float) $r['received_amt'], 2); ?></td>
									<td><strong><?php echo number_format($bal, 2); ?></strong></td>
									<td>
										<a class="btn btn-xs blue" href="channel_partner_ledger.php?cp_id=<?php echo (int) $r['cp_id']; ?>">Ledger</a>
										<a class="btn btn-xs green" href="channel_partner_stock_manage.php?cp_id=<?php echo (int) $r['cp_id']; ?>">Stock</a>
									</td>
								</tr>
								<?php
							}
						}
						if (!$has) {
							echo '<tr><td colspan="9" class="text-center">No CP customer sales in this period.</td></tr>';
						} else {
							?>
							<tr style="background:#f5f5f5;font-weight:700;">
								<td colspan="5" class="text-right">Total</td>
								<td><?php echo number_format($totalSales, 2); ?></td>
								<td><?php echo number_format($totalRecv, 2); ?></td>
								<td><?php echo number_format($totalSales - $totalRecv, 2); ?></td>
								<td></td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>

					<h4>Order Detail (Item Sales)</h4>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Order</th>
								<th>Date</th>
								<th>Channel Partner</th>
								<th>Party / Customer</th>
								<th>Items</th>
								<th>Amount</th>
								<th>Payment</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$hasD = false;
						if ($detailRes) {
							while ($d = mysqli_fetch_assoc($detailRes)) {
								$hasD = true;
								$paid = ((int) $d['payment_received_flag'] === 1);
								?>
								<tr>
									<td><a target="_blank" href="order_viewer.php?order_id=<?php echo (int) $d['id']; ?>"><?php echo htmlspecialchars($d['order_no']); ?></a></td>
									<td><?php echo date('d-m-Y', strtotime($d['order_date'])); ?></td>
									<td><?php echo htmlspecialchars($d['cp_name']); ?></td>
									<td><?php echo htmlspecialchars($d['party_name']); ?></td>
									<td style="font-size:12px;"><?php echo htmlspecialchars($d['items']); ?></td>
									<td><?php echo number_format((float) $d['grand_total'], 2); ?></td>
									<td><?php echo $paid ? ('Received ' . number_format((float) $d['payment_received_amount'], 2)) : 'Pending'; ?></td>
								</tr>
								<?php
							}
						}
						if (!$hasD) {
							echo '<tr><td colspan="7" class="text-center">No detail rows.</td></tr>';
						}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script>
$('.date-picker').datepicker({ autoclose: true, format: 'dd-mm-yyyy', todayHighlight: true });
</script>
</body>
</html>
