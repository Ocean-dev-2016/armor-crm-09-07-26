<?php
/**
 * Admin / Sales Executive — CP Ledger (Armor -> Channel Partner).
 */
$page_id = 565;
$page_slug = 'channel_partner_cp_ledger';
$page = 'channel_partner_cp_ledger';
$main_page = 'channel_partner';
$page_title = "CP Ledger";
$page_hierarchy = array(
	array("link" => "", "title" => "Channel Partner"),
	array("link" => "channel_partner_cp_ledger.php", "title" => $page_title)
);
include("connect.php");

$is_cp = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
if ($is_cp) {
	$db->addErrorMessage("CP ledger is visible only in admin / sales executive panel.");
	$db->rp_location("channel_partner_ledger.php");
}

$cpFilter = isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0;
if ($cpFilter <= 0) {
	$cpFilter = (int) $db->rp_getValue("executive", "id", "channel_partner_flag=1 AND customer_flag=0 AND isDelete=0", 0);
}
$cp_name = $cpFilter > 0 ? $db->rp_getValue("executive", "company_name", "id='" . $cpFilter . "'", 0) : '';

$ledger = array();
if ($cpFilter > 0) {
	$where = "customer_id='" . $cpFilter . "' AND channel_partner_order_flag=1 AND isDelete=0 AND status NOT IN (-2,3) AND (cp_order_mode='own' OR cp_order_mode='' OR cp_order_mode IS NULL OR channel_partner_customer_id=0 OR channel_partner_customer_id IS NULL)";
	$or = $db->rp_getData(
		"orders",
		"id,order_no,order_date,grand_total,payment_received_flag,payment_received_amount,payment_received_date,payment_received_type",
		$where,
		"order_date ASC, id ASC",
		0
	);
	if ($or) {
		while ($o = mysqli_fetch_assoc($or)) {
			$ledger[] = array(
				'date' => $o['order_date'],
				'sort' => strtotime($o['order_date']) . '1' . str_pad($o['id'], 8, '0', STR_PAD_LEFT),
				'particular' => 'Armor Sales Order ' . $o['order_no'],
				'vch' => $o['order_no'],
				'debit' => (float) $o['grand_total'],
				'credit' => 0,
				'type' => 'order',
				'order_id' => (int) $o['id'],
			);
			if ((int) $o['payment_received_flag'] === 1 && (float) $o['payment_received_amount'] > 0) {
				$pdate = (!empty($o['payment_received_date']) && $o['payment_received_date'] != '0000-00-00 00:00:00')
					? date('Y-m-d', strtotime($o['payment_received_date']))
					: $o['order_date'];
				$ptypeLabels = array(1 => 'Cash', 2 => 'Cheque', 3 => 'Online', 4 => 'Other');
				$pt = isset($ptypeLabels[$o['payment_received_type']]) ? $ptypeLabels[$o['payment_received_type']] : 'Payment';
				$ledger[] = array(
					'date' => $pdate,
					'sort' => strtotime($pdate) . '2' . str_pad($o['id'], 8, '0', STR_PAD_LEFT),
					'particular' => 'Payment Received (' . $pt . ') against ' . $o['order_no'],
					'vch' => 'RCPT/' . $o['order_no'],
					'debit' => 0,
					'credit' => (float) $o['payment_received_amount'],
					'type' => 'payment',
					'order_id' => (int) $o['id'],
				);
			}
		}
	}
	usort($ledger, function ($a, $b) {
		return strcmp($a['sort'], $b['sort']);
	});
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<style>
.ledger-head { background:#1f4e79; color:#fff; padding:12px 16px; margin-bottom:12px; }
.ledger-table th { background:#e8eef5; }
.bal-dr { color:#c0392b; font-weight:700; }
.bal-cr { color:#1e8449; font-weight:700; }
</style>
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
			<div class="ledger-head">
				<strong>Armor -> Channel Partner Ledger</strong>
				<?php if ($cp_name) { echo ' — ' . htmlspecialchars($cp_name); } ?>
			</div>
			<form class="form-inline" method="get" style="margin-bottom:15px;">
				<div class="form-group">
					<label>Channel Partner</label>
					<select name="cp_id" class="form-control" onchange="this.form.submit()">
						<option value="0">Select CP</option>
						<?php
						$cps = $db->rp_getData("executive", "id,company_name", "channel_partner_flag=1 AND customer_flag=0 AND isDelete=0", "company_name ASC", 0);
						if ($cps) {
							while ($cp = mysqli_fetch_assoc($cps)) {
								$sel = ($cpFilter == (int) $cp['id']) ? 'selected' : '';
								echo '<option value="' . (int) $cp['id'] . '" ' . $sel . '>' . htmlspecialchars($cp['company_name']) . '</option>';
							}
						}
						?>
					</select>
				</div>
				<a class="btn blue" href="channel_partner_admin_payment.php?cp_id=<?php echo (int) $cpFilter; ?>">Receive Payment</a>
				<a class="btn default" href="channel_partner_stock_manage.php?cp_id=<?php echo (int) $cpFilter; ?>">View Stock</a>
				<a class="btn default" href="channel_partner_ledger.php?cp_id=<?php echo (int) $cpFilter; ?>">CP Customer Ledger</a>
			</form>
			<table class="table table-bordered ledger-table">
				<thead>
					<tr>
						<th width="100">Date</th>
						<th>Particulars</th>
						<th width="140">Voucher</th>
						<th width="120" class="text-right">Debit</th>
						<th width="120" class="text-right">Credit</th>
						<th width="130" class="text-right">Balance</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$bal = 0;
				if (empty($ledger)) {
					echo '<tr><td colspan="6" class="text-center">No ledger entries.</td></tr>';
				} else {
					foreach ($ledger as $row) {
						$bal += $row['debit'] - $row['credit'];
						$balLbl = number_format(abs($bal), 2) . ($bal >= 0 ? ' Dr' : ' Cr');
						$balClass = ($bal >= 0) ? 'bal-dr' : 'bal-cr';
						?>
						<tr>
							<td><?php echo date('d-m-Y', strtotime($row['date'])); ?></td>
							<td><?php echo htmlspecialchars($row['particular']); ?></td>
							<td><?php if ($row['type'] === 'order') { ?><a target="_blank" href="order_viewer.php?order_id=<?php echo (int) $row['order_id']; ?>"><?php echo htmlspecialchars($row['vch']); ?></a><?php } else { echo htmlspecialchars($row['vch']); } ?></td>
							<td class="text-right"><?php echo $row['debit'] > 0 ? number_format($row['debit'], 2) : ''; ?></td>
							<td class="text-right"><?php echo $row['credit'] > 0 ? number_format($row['credit'], 2) : ''; ?></td>
							<td class="text-right <?php echo $balClass; ?>"><?php echo $balLbl; ?></td>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
</body>
</html>
