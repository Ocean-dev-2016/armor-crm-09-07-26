<?php
/**
 * Channel Partner Party Ledger (Tally-like).
 * Visible to CP login (own) and Armor Admin (any CP).
 */
$page_id = 565;
$page_slug = 'channel_partner_ledger';
$page = 'channel_partner_ledger';
$main_page = 'channel_partner';
$page_title = "CP Customer Ledger";
$page_hierarchy = array(
	array("link" => "", "title" => "Channel Partner"),
	array("link" => "channel_partner_ledger.php", "title" => $page_title)
);
include("connect.php");
include("include/channel_partner_ledger_data.php");

$is_cp = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
$cp_login_id = $is_cp ? (int) cp_get_login_channel_partner_id() : 0;
$cpFilter = $is_cp ? $cp_login_id : (isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0);
$partyFilter = isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0;

if (!$is_cp && $cpFilter <= 0) {
	/* default first CP for admin convenience */
	$cpFilter = (int) $db->rp_getValue("executive", "id", "channel_partner_flag=1 AND customer_flag=0 AND isDelete=0", 0);
}

$cp_name = $cpFilter > 0 ? $db->rp_getValue("executive", "company_name", "id='" . $cpFilter . "'", 0) : '';

$parties = array();
if ($cpFilter > 0) {
	$pr = $db->rp_getData(
		"channel_partner_customer",
		"id,company_name,person_name,mobile_no",
		"channel_partner_id='" . $cpFilter . "' AND isDelete=0",
		"company_name ASC",
		0
	);
	if ($pr) {
		while ($p = mysqli_fetch_assoc($pr)) {
			$parties[] = $p;
		}
	}
}

list($ledger, $opening) = cp_build_customer_ledger($db, $cpFilter, $partyFilter);
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
				<strong>CP Customer Ledger</strong>
				<?php if ($cp_name) { echo ' — ' . htmlspecialchars($cp_name); } ?>
				<span style="float:right;font-size:12px;">Tally / Miracle style</span>
			</div>

			<form class="form-inline" method="get" style="margin-bottom:15px;">
				<?php if (!$is_cp) { ?>
				<div class="form-group">
					<label>Channel Partner</label>
					<select name="cp_id" class="form-control" onchange="this.form.submit()">
						<option value="0">Select CP</option>
						<?php
						$cps = $db->rp_getData("executive", "id,company_name,client_code", "channel_partner_flag=1 AND customer_flag=0 AND isDelete=0", "company_name ASC", 0);
						if ($cps) {
							while ($cp = mysqli_fetch_assoc($cps)) {
								$sel = ($cpFilter == (int) $cp['id']) ? 'selected' : '';
								echo '<option value="' . (int) $cp['id'] . '" ' . $sel . '>' . htmlspecialchars($cp['company_name']) . '</option>';
							}
						}
						?>
					</select>
				</div>
				<?php } ?>
				<div class="form-group">
					<label>Party</label>
					<select name="party_id" class="form-control" onchange="this.form.submit()">
						<option value="0">All Parties</option>
						<?php foreach ($parties as $p) {
							$sel = ($partyFilter == (int) $p['id']) ? 'selected' : '';
							echo '<option value="' . (int) $p['id'] . '" ' . $sel . '>' . htmlspecialchars($p['company_name']) . '</option>';
						} ?>
					</select>
				</div>
				<?php if (!$is_cp) { ?>
					<a class="btn blue" href="channel_partner_sales_report.php?cp_id=<?php echo (int) $cpFilter; ?>">Sales Report</a>
				<?php } else { ?>
					<a class="btn green" href="channel_partner_payment.php">Receive Payment</a>
				<?php } ?>
				<?php if ($cpFilter > 0) {
					$exportQs = $is_cp
						? 'party_id=' . (int) $partyFilter
						: 'cp_id=' . (int) $cpFilter . '&party_id=' . (int) $partyFilter;
					$pdfAbsUrl = rtrim(ADMINSITEURL, '/') . '/channel_partner_ledger_print.php?' . $exportQs;
					$partyNameShare = 'All Parties';
					$waPhone = '';
					if ($partyFilter > 0) {
						foreach ($parties as $p) {
							if ((int) $p['id'] === $partyFilter) {
								$partyNameShare = $p['company_name'];
								if (!empty($p['mobile_no'])) {
									$waPhone = preg_replace('/\D+/', '', $p['mobile_no']);
									if (strlen($waPhone) === 10) {
										$waPhone = '91' . $waPhone;
									}
								}
								break;
							}
						}
					}
					$waText = "Party Ledger\nParty: " . $partyNameShare;
					if ($cp_name != '') {
						$waText .= "\nChannel Partner: " . $cp_name;
					}
					$waText .= "\n\nOpen / Print PDF:\n" . $pdfAbsUrl;
					$waHref = 'https://api.whatsapp.com/send?' . ($waPhone != '' ? ('phone=' . $waPhone . '&') : '') . 'text=' . rawurlencode($waText);
				?>
					<a class="btn green" target="_blank" rel="noopener"
						href="<?php echo htmlspecialchars($waHref); ?>"
						title="Share PDF on WhatsApp">
						<i class="fa fa-whatsapp"></i> Share PDF
					</a>
					<a class="btn red-haze" target="_blank"
						href="channel_partner_ledger_print.php?<?php echo $exportQs; ?>"
						title="Print / Save as PDF">
						<i class="fa fa-file-pdf-o"></i> Print PDF
					</a>
					<a class="btn yellow-crusta" style="color:#fff;"
						href="channel_partner_ledger_excel.php?<?php echo $exportQs; ?>"
						title="Export Excel">
						<i class="fa fa-file-excel-o"></i> Export Excel
					</a>
				<?php } ?>
			</form>

			<?php if ($cpFilter <= 0) { ?>
				<div class="alert alert-warning">Select Channel Partner.</div>
			<?php } else { ?>
			<table class="table table-bordered ledger-table">
				<thead>
					<tr>
						<th width="100">Date</th>
						<th>Particulars</th>
						<th width="120">Voucher</th>
						<th width="120" class="text-right">Debit</th>
						<th width="120" class="text-right">Credit</th>
						<th width="130" class="text-right">Balance</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$bal = $opening;
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
							<td>
								<?php if ($row['type'] === 'order') { ?>
									<a target="_blank" href="order_viewer.php?order_id=<?php echo (int) $row['order_id']; ?>"><?php echo htmlspecialchars($row['vch']); ?></a>
								<?php } else {
									echo htmlspecialchars($row['vch']);
								} ?>
							</td>
							<td class="text-right"><?php echo $row['debit'] > 0 ? number_format($row['debit'], 2) : ''; ?></td>
							<td class="text-right"><?php echo $row['credit'] > 0 ? number_format($row['credit'], 2) : ''; ?></td>
							<td class="text-right <?php echo $balClass; ?>"><?php echo $balLbl; ?></td>
						</tr>
						<?php
					}
					?>
					<tr style="background:#f0f0f0;font-weight:700;">
						<td colspan="3" class="text-right">Closing Balance</td>
						<td colspan="3" class="text-right <?php echo ($bal >= 0) ? 'bal-dr' : 'bal-cr'; ?>">
							<?php echo number_format(abs($bal), 2) . ($bal >= 0 ? ' Dr' : ' Cr'); ?>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			<?php } ?>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
</body>
</html>
