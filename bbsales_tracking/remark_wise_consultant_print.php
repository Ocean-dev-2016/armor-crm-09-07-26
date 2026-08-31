<?php
$page_id = 671;
$page_slug = 'remark_wise_report';
include('connect.php');

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

$vf = null;
$cfRes = $db->rp_getData('visit_consultant_form', '*', "visit_id='" . $visitId . "' AND isDelete=0", 'id DESC', 0);
if ($cfRes) {
	$vf = mysqli_fetch_assoc($cfRes);
}
if (!$vf) {
	die('Consultant form not found for this visit.');
}

$hf = null;
$hrItems = array();
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
			$hrItems[] = $it;
		}
	}
	if (trim((string) $hf['customer_name']) != '') {
		$customerName = trim((string) $hf['customer_name']);
	}
}

function cf_p($v)
{
	return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

$typeLabel = 'Private Consultant Approval';
if (isset($vf['consultant_type']) && $vf['consultant_type'] == 'government') {
	$typeLabel = 'Government Consultant Approval';
}
if (isset($vf['reason_code']) && strtoupper($vf['reason_code']) == 'C2') {
	$typeLabel = 'Government Consultant Approval';
} else if (isset($vf['reason_code']) && strtoupper($vf['reason_code']) == 'C1') {
	$typeLabel = 'Private Consultant Approval';
}

$autoPrint = isset($_REQUEST['print']) && $_REQUEST['print'] == '1';

$payOption = '';
$payRemark = '';
$isAdvance = false;
$isCredit = false;
if ($hf) {
	require_once('../include/class.visit.php');
	$visitObj = new Visit();
	$payResolved = $visitObj->resolveHighRatePaymentFields(
		isset($hf['payment_option']) ? $hf['payment_option'] : '',
		isset($hf['payment_remark']) ? $hf['payment_remark'] : ''
	);
	$payOption = $payResolved['payment_option'];
	$payRemark = $payResolved['payment_remark'];
	$isAdvance = ($payOption === '0' || $payOption === 0 || strcasecmp((string) $payOption, 'advance') === 0 || strcasecmp((string) $payOption, 'true') === 0);
	$isCredit = ($payOption === '1' || $payOption === 1 || strcasecmp((string) $payOption, '30 days') === 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title>Consultant Approval Form — Visit #<?php echo $visitId; ?></title>
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
.cf-sheet {
	width: 210mm;
	min-height: 148mm;
	margin: 0 auto;
	background: #fff;
	padding: 6mm 8mm;
}
.cf-title {
	text-align: center;
	font-size: 14px;
	font-weight: bold;
	color: #1a7a3a;
	margin: 0 0 6px;
	text-transform: uppercase;
	border-bottom: 2px solid #1a7a3a;
	padding-bottom: 4px;
}
.cf-meta {
	width: 100%;
	border-collapse: collapse;
	margin-bottom: 8px;
}
.cf-meta th, .cf-meta td {
	border: 1px solid #999;
	padding: 3px 5px;
	font-size: 9px;
	vertical-align: top;
}
.cf-meta th {
	background: #f3f3f3;
	width: 14%;
	text-align: left;
	font-weight: bold;
}
.cf-form {
	width: 100%;
	border-collapse: collapse;
}
.cf-form th, .cf-form td {
	border: 1px solid #999;
	padding: 4px 6px;
	font-size: 9.5px;
	vertical-align: top;
}
.cf-form th {
	background: #f3f3f3;
	width: 22%;
	text-align: left;
	font-weight: bold;
}
.cf-section-title {
	font-size: 11px;
	font-weight: bold;
	color: #1a7a3a;
	margin: 0 0 6px;
}
.cf-hr-title {
	font-size: 11px;
	font-weight: bold;
	color: #c85a12;
	margin: 12px 0 6px;
}
.cf-products {
	width: 100%;
	border-collapse: collapse;
	margin-bottom: 8px;
}
.cf-products th, .cf-products td {
	border: 1px solid #999;
	padding: 2px 4px;
	font-size: 8.5px;
}
.cf-products th {
	background: #f3f3f3;
	font-weight: bold;
}
.cf-payment {
	border: 1px solid #999;
	padding: 5px 8px;
	font-size: 9px;
	background: #fafafa;
}
.cf-pay-box {
	display: inline-block;
	width: 10px;
	height: 10px;
	border: 1px solid #333;
	margin-right: 4px;
	vertical-align: middle;
	text-align: center;
	line-height: 8px;
	font-size: 8px;
}
@media print {
	body { background: #fff; }
	.no-print { display: none !important; }
	.cf-sheet {
		margin: 0;
		box-shadow: none;
		page-break-after: avoid;
	}
}
</style>
</head>
<body>
<div class="no-print">
	<button type="button" onclick="window.print();">Print</button>
	<button type="button" onclick="window.close();">Close</button>
</div>
<div class="cf-sheet">
	<div class="cf-title">Consultant Approval Form</div>
	<table class="cf-meta">
		<tr>
			<th>Sales Person</th><td><?php echo cf_p($row['sales_person_name'] != '' ? $row['sales_person_name'] : '-'); ?></td>
			<th>Visit Date</th><td><?php echo cf_p($visitDate); ?></td>
			<th>Time</th><td><?php echo cf_p($visitTime); ?></td>
		</tr>
		<tr>
			<th>Client Code</th><td><?php echo cf_p($clientCode != '' ? $clientCode : '-'); ?></td>
			<th>Customer</th><td colspan="3"><?php echo cf_p($customerName != '' ? $customerName : '-'); ?></td>
		</tr>
		<tr>
			<th>Address</th><td colspan="3"><?php echo cf_p($address != '' ? $address : '-'); ?></td>
			<th>Turnover</th><td><?php echo cf_p($turnLabel); ?></td>
		</tr>
	</table>

	<div class="cf-section-title">Need Approval / Consultant Form</div>
	<table class="cf-form">
		<tr><th>Type</th><td><?php echo cf_p($typeLabel); ?></td></tr>
		<tr><th>Firm Name</th><td><?php echo cf_p($vf['firm_name']); ?></td></tr>
		<tr><th>Address</th><td><?php echo nl2br(cf_p($vf['address'])); ?></td></tr>
		<tr><th>City</th><td><?php echo cf_p($vf['city']); ?></td></tr>
		<tr><th>State</th><td><?php echo cf_p($vf['state']); ?></td></tr>
		<tr><th>Pincode</th><td><?php echo cf_p($vf['pincode']); ?></td></tr>
		<tr><th>Contact Person</th><td><?php echo cf_p($vf['contact_person']); ?></td></tr>
		<tr><th>Mobile</th><td><?php echo cf_p($vf['mobile']); ?></td></tr>
		<tr><th>Email</th><td><?php echo cf_p(isset($vf['email']) ? $vf['email'] : ''); ?></td></tr>
	</table>

	<?php if ($hf) { ?>
	<div class="cf-hr-title">High Rate Analysis Form</div>
	<?php if (!empty($hrItems)) { ?>
	<table class="cf-products">
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
			<?php foreach ($hrItems as $it) { ?>
			<tr>
				<td><?php echo cf_p($it['product_name']); ?></td>
				<td><?php echo cf_p($it['given_rate']); ?></td>
				<td><?php echo cf_p($it['qty']); ?></td>
				<td><?php echo cf_p($it['customer_rate']); ?></td>
				<td><?php echo cf_p(isset($it['remark']) ? $it['remark'] : ''); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php } ?>
	<div class="cf-payment">
		<div style="font-weight:bold;margin-bottom:4px;">Payment Condition</div>
		<span style="margin-right:18px;<?php echo $isAdvance ? 'font-weight:bold;' : ''; ?>">
			<span class="cf-pay-box"><?php echo $isAdvance ? '&#10003;' : ''; ?></span> 1) Advance Payment
		</span>
		<span style="<?php echo $isCredit ? 'font-weight:bold;' : ''; ?>">
			<span class="cf-pay-box"><?php echo $isCredit ? '&#10003;' : ''; ?></span> 2) 30 Day Credit
		</span>
		<?php if ($payRemark != '') { ?>
		<div style="margin-top:4px;"><b>Remark:</b> <?php echo cf_p($payRemark); ?></div>
		<?php } ?>
	</div>
	<?php } ?>
</div>
<?php if ($autoPrint) { ?>
<script>window.onload = function () { window.print(); };</script>
<?php } ?>
</body>
</html>
<?php include('disconnect.php'); ?>
