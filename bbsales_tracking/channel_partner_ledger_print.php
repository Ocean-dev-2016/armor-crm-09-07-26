<?php
/**
 * Channel Partner — Party Ledger print (A4 — same layout as SO / PI / Payment Statement).
 */
$page_id = 565;
$page_slug = 'channel_partner_ledger';
include("connect.php");
include("include/channel_partner_ledger_data.php");
include("../include/no_to_word.php");

$is_cp = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
$cp_login_id = $is_cp ? (int) cp_get_login_channel_partner_id() : 0;
$is_admin = (isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) && (int) $_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0);

if (!$is_cp && !$is_admin) {
	$db->addErrorMessage("Access denied.");
	$db->rp_location("dashboard.php");
}

$cpFilter = $is_cp ? $cp_login_id : (isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0);
$partyFilter = isset($_REQUEST['party_id']) ? (int) $_REQUEST['party_id'] : 0;

if ($cpFilter <= 0) {
	$db->addErrorMessage("Select Channel Partner.");
	$db->rp_location("channel_partner_ledger.php");
}

$cp_r = $db->rp_getData("executive", "*", "id='" . $cpFilter . "' AND isDelete=0", "", 0);
$cp = $cp_r ? mysqli_fetch_assoc($cp_r) : array();
$cp_name = isset($cp['company_name']) ? $cp['company_name'] : '';

$pi_company = !empty($cp['cp_print_company_name']) ? $cp['cp_print_company_name'] : $cp_name;
$pi_gst = !empty($cp['cp_print_gst']) ? $cp['cp_print_gst'] : (isset($cp['gst']) ? $cp['gst'] : '');
$pi_pan = isset($cp['cp_print_pan']) ? $cp['cp_print_pan'] : '';
$pi_address = isset($cp['cp_print_address']) ? $cp['cp_print_address'] : '';
$pi_bank = isset($cp['cp_print_bank_details']) ? $cp['cp_print_bank_details'] : '';
$pi_terms = isset($cp['cp_print_terms']) ? $cp['cp_print_terms'] : '';

$headerImgFile = isset($cp['cp_print_header_image']) ? $cp['cp_print_header_image'] : '';
$footerImgFile = isset($cp['cp_print_footer_image']) ? $cp['cp_print_footer_image'] : '';
$headerImgSrc = '';
$footerImgSrc = '';
if ($headerImgFile != '' && defined('HEADER_A') && file_exists(HEADER_A . $headerImgFile)) {
	$headerImgSrc = HEADER_A . $headerImgFile;
}
if ($footerImgFile != '' && defined('HEADER_A') && file_exists(HEADER_A . $footerImgFile)) {
	$footerImgSrc = HEADER_A . $footerImgFile;
}

$partyLabel = 'All Parties';
$partyAddr = '';
$partyMobile = '';
$partyEmail = '';
$partyGst = '';
if ($partyFilter > 0) {
	$pr = $db->rp_getData(
		"channel_partner_customer",
		"*",
		"id='" . $partyFilter . "' AND channel_partner_id='" . $cpFilter . "' AND isDelete=0",
		"",
		0
	);
	$partyRow = $pr ? mysqli_fetch_assoc($pr) : null;
	if ($partyRow) {
		$partyLabel = $partyRow['company_name'];
		if (!empty($partyRow['person_name'])) {
			$partyLabel .= ' / ' . $partyRow['person_name'];
		}
		$partyAddr = implode(', ', array_filter(array(
			isset($partyRow['address']) ? $partyRow['address'] : '',
			isset($partyRow['city']) ? $partyRow['city'] : '',
			isset($partyRow['state']) ? $partyRow['state'] : '',
			isset($partyRow['pincode']) ? $partyRow['pincode'] : '',
		)));
		$partyMobile = isset($partyRow['mobile_no']) ? $partyRow['mobile_no'] : '';
		$partyEmail = isset($partyRow['email']) ? $partyRow['email'] : '';
		$partyGst = isset($partyRow['gst']) ? $partyRow['gst'] : '';
	}
}

list($ledger, $opening) = cp_build_customer_ledger($db, $cpFilter, $partyFilter);
$currency = defined('CURR') ? CURR : 'Rs.';
$doc_title = 'PARTY LEDGER';
$ntw = new NumToWord_RP();
$backQs = $is_cp
	? 'party_id=' . (int) $partyFilter
	: 'cp_id=' . (int) $cpFilter . '&party_id=' . (int) $partyFilter;

$bal = $opening;
$totalDr = 0;
$totalCr = 0;
foreach ($ledger as $row) {
	$bal += $row['debit'] - $row['credit'];
	$totalDr += $row['debit'];
	$totalCr += $row['credit'];
}
$closingBal = $bal;
$closingAbs = abs($closingBal);
$closingSide = ($closingBal >= 0) ? 'Dr' : 'Cr';
$wordsClose = '';
try {
	$wordsClose = strtoupper(trim($ntw->rp_convertNumToWord(number_format($closingAbs, 2, '.', ''))));
} catch (Exception $e) {
	$wordsClose = '';
}
$minRows = 10;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo htmlspecialchars($doc_title); ?> | <?php echo htmlspecialchars($cp_name); ?></title>
<style>
	* { box-sizing: border-box; }
	html, body { margin: 0; padding: 0; }
	body { font-family: Calibri, Arial, sans-serif; font-size: 13px; color: #222; background: #eee; }
	.toolbar { background: #333; color: #fff; padding: 10px 16px; text-align: center; }
	.toolbar a, .toolbar button {
		display: inline-block; margin: 0 6px; padding: 8px 16px; background: #1a6b8a; color: #fff;
		text-decoration: none; border: 0; border-radius: 3px; cursor: pointer; font-weight: 600;
	}
	.toolbar .btn-muted { background: #666; }
	.sheet {
		width: 210mm;
		min-height: 297mm;
		max-width: 100%;
		margin: 12px auto;
		background: #fff;
		border: 1px solid #999;
		box-shadow: 0 2px 8px rgba(0,0,0,.08);
		display: flex;
		flex-direction: column;
	}
	.sheet-inner { flex: 1 1 auto; display: flex; flex-direction: column; padding: 0; }
	.sheet-body { flex: 1 1 auto; }
	.sheet-footer { flex: 0 0 auto; margin-top: auto; }
	table.main { width: 100%; border-collapse: collapse; }
	table.main td, table.main th { border: 1px solid #595959; padding: 6px 8px; vertical-align: top; }
	.header-img { width: 100%; display: block; padding: 0; margin: 0; }
	.header-fallback {
		padding: 14px 16px; border-bottom: 2px solid #333; background: #fafafa; text-align: center; font-weight: 700; font-size: 16px;
	}
	.title-row { background: #A9A9A9; text-align: center; font-weight: 700; letter-spacing: 1px; font-size: 15px; }
	.th-head { background: #1a6b8a; color: #fff; text-align: center; font-size: 12px; }
	.text-right { text-align: right; }
	.text-center { text-align: center; }
	.bal-dr { color: #c0392b; font-weight: 700; }
	.bal-cr { color: #1e8449; font-weight: 700; }
	.footer-img { width: 100%; display: block; }
	.muted { color: #666; font-size: 11px; }
	.blank-row td { height: 22px; }
	@media print {
		body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
		.toolbar, .no-print { display: none !important; }
		.sheet {
			margin: 0 auto;
			border: 0;
			box-shadow: none;
			width: 210mm;
			min-height: 297mm;
		}
		@page { size: A4 portrait; margin: 0; }
	}
</style>
</head>
<body>
<div class="toolbar no-print">
	<button type="button" onclick="window.print();">Print / Save as PDF</button>
	<a class="btn-muted" href="channel_partner_ledger.php?<?php echo $backQs; ?>">Back</a>
	<a class="btn-muted" href="javascript:window.close();">Close</a>
</div>

<div class="sheet">
	<div class="sheet-inner">
		<div class="sheet-body">
			<?php if ($headerImgSrc != '') { ?>
				<img class="header-img" src="<?php echo htmlspecialchars($headerImgSrc); ?>" alt="Header">
			<?php } else { ?>
				<div class="header-fallback">
					<?php echo htmlspecialchars($pi_company != '' ? $pi_company : 'Channel Partner'); ?>
					<div class="muted" style="font-weight:400;margin-top:4px;">Upload header image from PI Format Settings.</div>
				</div>
			<?php } ?>

			<table class="main">
				<tr><td colspan="6" class="title-row"><?php echo $doc_title; ?></td></tr>
				<tr>
					<td colspan="3" style="width:55%;">
						<strong>Party / Buyer</strong><br>
						<span style="font-size:15px;font-weight:700;text-transform:uppercase;"><?php echo htmlspecialchars($partyLabel); ?></span><br>
						<?php if ($partyAddr != '') { echo htmlspecialchars($partyAddr) . '<br>'; } ?>
						<?php if ($partyMobile != '') { echo '<strong>Mobile:</strong> ' . htmlspecialchars($partyMobile) . '<br>'; } ?>
						<?php if ($partyEmail != '') { echo '<strong>Email:</strong> ' . htmlspecialchars($partyEmail) . '<br>'; } ?>
						<?php if ($partyGst != '') { echo '<strong>GSTIN:</strong> ' . htmlspecialchars($partyGst); } ?>
					</td>
					<td colspan="3">
						<strong>Document:</strong> Party Ledger<br>
						<strong>Print Date:</strong> <?php echo date('d-M-Y'); ?><br>
						<strong>From:</strong> <?php echo htmlspecialchars($pi_company); ?><br>
						<?php if ($pi_gst != '') { echo '<strong>Seller GSTIN:</strong> ' . htmlspecialchars($pi_gst) . '<br>'; } ?>
						<?php if ($pi_pan != '') { echo '<strong>PAN:</strong> ' . htmlspecialchars($pi_pan) . '<br>'; } ?>
						<?php if (trim(strip_tags($pi_address)) != '') { ?>
							<br><strong>Address:</strong><br><?php echo html_entity_decode($pi_address); ?>
						<?php } ?>
					</td>
				</tr>
			</table>

			<table class="main" style="margin-top:-1px;">
				<tr class="th-head">
					<th style="width:11%;">Date</th>
					<th style="width:36%;">Particulars</th>
					<th style="width:15%;">Voucher</th>
					<th style="width:12%;">Debit</th>
					<th style="width:12%;">Credit</th>
					<th style="width:14%;">Balance</th>
				</tr>
				<?php
				$runBal = $opening;
				$rowCount = 0;
				if ($opening != 0) {
					$rowCount++;
					$obLbl = number_format(abs($opening), 2) . ($opening >= 0 ? ' Dr' : ' Cr');
					?>
					<tr>
						<td class="text-center"></td>
						<td><strong>Opening Balance</strong></td>
						<td class="text-center">—</td>
						<td class="text-right"><?php echo $opening > 0 ? number_format($opening, 2) : ''; ?></td>
						<td class="text-right"><?php echo $opening < 0 ? number_format(abs($opening), 2) : ''; ?></td>
						<td class="text-right <?php echo ($opening >= 0) ? 'bal-dr' : 'bal-cr'; ?>"><?php echo $obLbl; ?></td>
					</tr>
					<?php
				}
				if (empty($ledger) && $opening == 0) {
					$rowCount = 1;
					echo '<tr><td colspan="6" class="text-center">No ledger entries.</td></tr>';
				} else {
					foreach ($ledger as $row) {
						$rowCount++;
						$runBal += $row['debit'] - $row['credit'];
						$balLbl = number_format(abs($runBal), 2) . ($runBal >= 0 ? ' Dr' : ' Cr');
						$balClass = ($runBal >= 0) ? 'bal-dr' : 'bal-cr';
						?>
						<tr>
							<td class="text-center"><?php echo date('d-m-Y', strtotime($row['date'])); ?></td>
							<td><?php echo htmlspecialchars($row['particular']); ?></td>
							<td class="text-center"><?php echo htmlspecialchars($row['vch']); ?></td>
							<td class="text-right"><?php echo $row['debit'] > 0 ? number_format($row['debit'], 2) : ''; ?></td>
							<td class="text-right"><?php echo $row['credit'] > 0 ? number_format($row['credit'], 2) : ''; ?></td>
							<td class="text-right <?php echo $balClass; ?>"><?php echo $balLbl; ?></td>
						</tr>
						<?php
					}
				}
				for ($i = $rowCount; $i < $minRows; $i++) {
					echo '<tr class="blank-row"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>';
				}
				?>
			</table>

			<table class="main" style="margin-top:-1px;">
				<tr>
					<td style="width:55%;background-color:lightgray;">
						<?php if ($pi_gst != '') { ?>
							<strong>GSTIN NO. : <?php echo htmlspecialchars($pi_gst); ?></strong>
						<?php } else { ?>
							<strong>Seller :</strong> <?php echo htmlspecialchars($pi_company); ?>
						<?php } ?>
					</td>
					<td style="width:25%;background-color:lightgray;"><strong>Total Debit</strong></td>
					<td class="text-right" style="width:20%;background-color:lightgray;"><strong><?php echo number_format($totalDr, 2); ?></strong></td>
				</tr>
				<tr>
					<td rowspan="5" style="vertical-align:top;">
						<?php if (trim(strip_tags($pi_bank)) != '') { ?>
							<strong>Bank Details</strong><br>
							<?php echo html_entity_decode($pi_bank); ?>
							<br><br>
						<?php } ?>
						<?php if (trim(strip_tags($pi_terms)) != '') { ?>
							<strong>Terms &amp; Condition :</strong><br>
							<?php echo html_entity_decode($pi_terms); ?>
							<br><br>
						<?php } ?>
						<strong>Closing Balance (in words):</strong><br>
						<?php
						if ($wordsClose != '') {
							echo htmlspecialchars($wordsClose) . ' ONLY (' . $closingSide . ')';
						} else {
							echo '—';
						}
						?>
						<br><br>
						<span class="muted">This is a computer generated Party Ledger (Tally / Miracle style).</span>
					</td>
					<td><strong>Total Credit</strong></td>
					<td class="text-right"><strong><?php echo number_format($totalCr, 2); ?></strong></td>
				</tr>
				<tr>
					<td style="background:#f0f0f0;"><strong>Closing Balance</strong></td>
					<td class="text-right <?php echo ($closingBal >= 0) ? 'bal-dr' : 'bal-cr'; ?>" style="background:#f0f0f0;">
						<strong><?php echo number_format($closingAbs, 2) . ' ' . $closingSide; ?></strong>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="height:48px;vertical-align:bottom;" class="text-right">
						<strong>For, <?php echo htmlspecialchars($pi_company); ?></strong><br>
						<span class="muted">Authorised Signatory</span>
					</td>
				</tr>
			</table>
		</div>

		<div class="sheet-footer">
			<?php if ($footerImgSrc != '') { ?>
				<img class="footer-img" src="<?php echo htmlspecialchars($footerImgSrc); ?>" alt="Footer">
			<?php } ?>
		</div>
	</div>
</div>
</body>
</html>
<?php
require_once 'disconnect.php';
?>
