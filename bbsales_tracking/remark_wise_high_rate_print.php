<?php
$page_id = 671;
$page_slug = 'remark_wise_report';
include('connect.php');
require_once('../include/class.remark_analysis_report.php');

$visitId = isset($_REQUEST['visit_id']) ? (int) $_REQUEST['visit_id'] : 0;
if ($visitId <= 0) {
	die('Invalid visit.');
}

$sql = "SELECT
		v.id,
		v.user_id,
		v.customer_id,
		v.inquiry_id,
		v.start_date_time,
		v.created_date,
		v.firm_name,
		v.client_name,
		se.name AS sales_person_name,
		e.company_name AS customer_company,
		e.cname AS customer_person,
		e.client_code AS customer_code,
		e.turnover AS customer_turnover,
		e.turnover_year AS customer_turnover_year,
		e.address AS customer_address,
		e.main_city AS customer_city,
		noi.company_name AS inquiry_company,
		noi.person_name AS inquiry_person
	FROM visit v
	LEFT JOIN sales_executive se ON se.id = v.user_id
	LEFT JOIN executive e ON e.id = v.customer_id
	LEFT JOIN no_order_inquiry noi ON noi.id = v.inquiry_id
	WHERE v.isDelete = 0 AND v.id = '" . $visitId . "'
	LIMIT 1";
$res = mysqli_query($db->myconn, $sql);
if (!$res || !($row = mysqli_fetch_assoc($res))) {
	die('Visit not found.');
}

$visitDateSource = ($row['start_date_time'] != '' && $row['start_date_time'] != '0000-00-00 00:00:00')
	? $row['start_date_time'] : $row['created_date'];
$visitDate = ($visitDateSource != '' && strtotime($visitDateSource) !== false) ? date('d/m/Y', strtotime($visitDateSource)) : '-';
$visitTime = ($visitDateSource != '' && strtotime($visitDateSource) !== false) ? date('h:i A', strtotime($visitDateSource)) : '-';

$customerName = '';
if ((int) $row['customer_id'] > 0) {
	$customerName = ($row['customer_company'] != '') ? $row['customer_company'] : $row['customer_person'];
} else if ((int) $row['inquiry_id'] > 0) {
	$customerName = ($row['inquiry_company'] != '') ? $row['inquiry_company'] : $row['inquiry_person'];
} else {
	$customerName = ($row['firm_name'] != '') ? $row['firm_name'] : $row['client_name'];
}

$clientCode = trim((string) $row['customer_code']);
$address = trim((string) $row['customer_address']);
$city = trim((string) $row['customer_city']);
if ($city != '' && $address != '') {
	$address .= ', ' . $city;
} else if ($city != '') {
	$address = $city;
}
$turnover = trim((string) $row['customer_turnover']);
$turnYear = trim((string) $row['customer_turnover_year']);
$turnLabel = ($turnover != '') ? $turnover : '-';
if ($turnover != '' && $turnYear != '' && $turnYear != '0') {
	$turnLabel .= ' (' . $turnYear . ')';
}

$hf = null;
$items = array();
$hrRes = $db->rp_getData('visit_high_rate_form', '*', "visit_id='" . $visitId . "' AND isDelete=0", 'id DESC', 0);
if ($hrRes) {
	$hf = mysqli_fetch_assoc($hrRes);
}
if ($hf) {
	$itemRes = $db->rp_getData('visit_high_rate_form_item', '*', "high_rate_form_id='" . (int) $hf['id'] . "' AND isDelete=0", 'sort_order ASC, id ASC', 0);
	if ($itemRes) {
		while ($it = mysqli_fetch_assoc($itemRes)) {
			if ($it['given_rate'] == '' && $it['qty'] == '' && $it['customer_rate'] == '' && empty($it['remark'])) {
				continue;
			}
			$items[] = $it;
		}
	}
	if (trim((string) $hf['customer_name']) != '') {
		$customerName = trim((string) $hf['customer_name']);
	}
}

function hr_p($v)
{
	return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
function hr_pay_advance($v)
{
	return ($v === '0' || $v === 0 || strcasecmp((string) $v, 'advance') === 0 || strcasecmp((string) $v, 'true') === 0);
}
function hr_pay_credit($v)
{
	return ($v === '1' || $v === 1 || strcasecmp((string) $v, '30 days') === 0);
}

$payResolved = array('payment_option' => '', 'payment_remark' => '');
if ($hf) {
	require_once('../include/class.visit.php');
	$visitObj = new Visit();
	$payResolved = $visitObj->resolveHighRatePaymentFields(
		isset($hf['payment_option']) ? $hf['payment_option'] : '',
		isset($hf['payment_remark']) ? $hf['payment_remark'] : ''
	);
}
$payOption = $payResolved['payment_option'];
$payRemark = $payResolved['payment_remark'];
$isAdvance = hr_pay_advance($payOption);
$isCredit = hr_pay_credit($payOption);
$autoPrint = isset($_REQUEST['print']) && $_REQUEST['print'] == '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>High Rate Analysis — Visit #<?php echo $visitId; ?></title>
<style>
@page { size: A4; margin: 8mm; }
* { box-sizing: border-box; }
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #111;
	margin: 0;
	padding: 0;
	background: #eee;
}
.no-print {
	text-align: center;
	padding: 10px;
	background: #3598dc;
}
.no-print button {
	background: #fff;
	border: none;
	padding: 8px 20px;
	font-size: 14px;
	cursor: pointer;
	border-radius: 3px;
	margin: 0 6px;
}
.hr-sheet {
	width: 210mm;
	min-height: 148mm;
	max-height: 148mm;
	margin: 0 auto;
	background: #fff;
	padding: 6mm 8mm;
	overflow: hidden;
}
.hr-title {
	text-align: center;
	font-size: 14px;
	font-weight: bold;
	color: #c85a12;
	margin: 0 0 6px;
	text-transform: uppercase;
	border-bottom: 2px solid #c85a12;
	padding-bottom: 4px;
}
.hr-meta {
	width: 100%;
	border-collapse: collapse;
	margin-bottom: 6px;
}
.hr-meta th, .hr-meta td {
	border: 1px solid #999;
	padding: 3px 5px;
	font-size: 9px;
	vertical-align: top;
}
.hr-meta th {
	background: #f3f3f3;
	width: 14%;
	text-align: left;
	font-weight: bold;
}
.hr-products {
	width: 100%;
	border-collapse: collapse;
	margin-bottom: 6px;
}
.hr-products th, .hr-products td {
	border: 1px solid #999;
	padding: 2px 4px;
	font-size: 8.5px;
}
.hr-products th {
	background: #f3f3f3;
	font-weight: bold;
}
.hr-payment {
	border: 1px solid #999;
	padding: 5px 8px;
	font-size: 9px;
	background: #fafafa;
}
.hr-payment-title { font-weight: bold; margin-bottom: 4px; }
.hr-pay-opt { margin-right: 18px; }
.hr-pay-opt.on { font-weight: bold; }
.hr-pay-box { display: inline-block; width: 10px; height: 10px; border: 1px solid #333; margin-right: 4px; vertical-align: middle; text-align: center; line-height: 8px; font-size: 8px; }
@media print {
	body { background: #fff; }
	.no-print { display: none !important; }
	.hr-sheet {
		margin: 0;
		box-shadow: none;
		page-break-after: avoid;
	}
}
</style>
</head>
<body>
<div class="no-print">
	<button type="button" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
	<button type="button" onclick="window.close();">Close</button>
</div>
<div class="hr-sheet">
	<div class="hr-title">High Rate Analysis Form</div>
	<table class="hr-meta">
		<tr>
			<th>Sales Person</th><td><?php echo hr_p($row['sales_person_name'] != '' ? $row['sales_person_name'] : '-'); ?></td>
			<th>Visit Date</th><td><?php echo hr_p($visitDate); ?></td>
			<th>Time</th><td><?php echo hr_p($visitTime); ?></td>
		</tr>
		<tr>
			<th>Client Code</th><td><?php echo hr_p($clientCode != '' ? $clientCode : '-'); ?></td>
			<th>Customer</th><td colspan="3"><?php echo hr_p($customerName != '' ? $customerName : '-'); ?></td>
		</tr>
		<tr>
			<th>Address</th><td colspan="3"><?php echo hr_p($address != '' ? $address : '-'); ?></td>
			<th>Turnover</th><td><?php echo hr_p($turnLabel); ?></td>
		</tr>
	</table>
	<?php if (!empty($items)) { ?>
	<table class="hr-products">
		<thead>
			<tr>
				<th style="width:34%;">Product</th>
				<th style="width:14%;">Given Rate</th>
				<th style="width:10%;">Qty</th>
				<th style="width:14%;">Customer Rate</th>
				<th>Remark</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($items as $it) { ?>
			<tr>
				<td><?php echo hr_p($it['product_name']); ?></td>
				<td><?php echo hr_p($it['given_rate']); ?></td>
				<td><?php echo hr_p($it['qty']); ?></td>
				<td><?php echo hr_p($it['customer_rate']); ?></td>
				<td><?php echo hr_p(isset($it['remark']) ? $it['remark'] : ''); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php } ?>
	<div class="hr-payment">
		<div class="hr-payment-title">Payment Condition</div>
		<span class="hr-pay-opt<?php echo $isAdvance ? ' on' : ''; ?>">
			<span class="hr-pay-box"><?php echo $isAdvance ? '&#10003;' : ''; ?></span> 1) Advance Payment
		</span>
		<span class="hr-pay-opt<?php echo $isCredit ? ' on' : ''; ?>">
			<span class="hr-pay-box"><?php echo $isCredit ? '&#10003;' : ''; ?></span> 2) 30 Day Credit
		</span>
		<?php if ($payRemark != '') { ?>
		<div style="margin-top:4px;"><b>Remark:</b> <?php echo hr_p($payRemark); ?></div>
		<?php } ?>
	</div>
</div>
<?php if ($autoPrint) { ?>
<script>window.onload = function () { window.print(); };</script>
<?php } ?>
</body>
</html>
<?php include('disconnect.php'); ?>
