<?php
$page_id = 650;
$page_slug = 'channel_partner_stock';
include("connect.php");
require_once dirname(__FILE__) . '/../include/class.channel_partner_stock.php';

$is_cp_login = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
$cp_login_id = function_exists('cp_get_login_channel_partner_id') ? (int) cp_get_login_channel_partner_id() : 0;
$req_cp = isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0;
$view = isset($_REQUEST['view']) ? strtolower(trim($_REQUEST['view'])) : 'main';
if ($view !== 'inout' && $view !== 'main') {
	$view = 'main';
}

if ($is_cp_login) {
	$cp_id = $cp_login_id;
} else {
	$cp_id = $req_cp;
}

$stockObj = new ChannelPartnerStock($db);
?>
<?php if ($is_cp_login || $cp_id > 0) {
	$cpName = $db->rp_getValue("executive", "company_name", "id='" . (int) $cp_id . "'", 0);
	/* Repair older dispatched/paid orders missing outward stock */
	$stockObj->backfillMissingOutward((int) $cp_id);
	?>
	<div class="alert alert-info" style="margin-bottom:12px;">
		<strong><?php echo htmlspecialchars($cpName ? $cpName : 'Channel Partner'); ?></strong>
		<?php if ($view === 'inout') { ?>
			— Inward / Outward stock ledger (Bill No, Date)
		<?php } else { ?>
			— Main Stock (Product Name &amp; Code wise)
		<?php } ?>
		<a class="btn btn-sm yellow-crusta pull-right" style="color:#fff;margin-top:-4px;"
			href="channel_partner_stock_excel.php?view=<?php echo htmlspecialchars($view); ?>&amp;cp_id=<?php echo (int) $cp_id; ?>">
			<i class="fa fa-file-excel-o"></i> Export Excel
		</a>
	</div>

	<?php if ($view === 'main') {
		$rows = $stockObj->getMainStockByProductCode($cp_id);
		?>
		<table class="table table-bordered table-striped" id="cp_main_stock_table">
			<thead>
				<tr>
					<th width="60">Sr.</th>
					<th>Product</th>
					<th width="140" class="text-right">Available Qty</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$cnt = 0;
			$has = false;
			foreach ($rows as $r) {
				$has = true;
				$cnt++;
				$code = (isset($r['catno']) && $r['catno'] !== '' && $r['catno'] !== '-') ? $r['catno'] : '';
				$label = $code !== '' ? ($code . ' - ' . $r['pro_name']) : $r['pro_name'];
				?>
				<tr>
					<td><?php echo $cnt; ?></td>
					<td><?php echo htmlentities($label); ?></td>
					<td class="text-right"><strong><?php echo htmlentities($r['total_qty']); ?></strong></td>
				</tr>
				<?php
			}
			if (!$has) {
				?>
				<tr><td colspan="3" class="text-center">No stock found.</td></tr>
				<?php
			}
			?>
			</tbody>
		</table>
	<?php } else {
		$moves = $stockObj->getStockMovements($cp_id);
		?>
		<table class="table table-bordered table-striped" id="cp_inout_stock_table">
			<thead>
				<tr>
					<th width="50">Sr.</th>
					<th width="100">Date</th>
					<th width="130">Bill No</th>
					<th width="80">Type</th>
					<th>Product</th>
					<th width="90" class="text-right">Inward</th>
					<th width="90" class="text-right">Outward</th>
					<th width="100" class="text-right">Balance</th>
					<th>Remark</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$cnt = 0;
			$has = false;
			$running = 0;
			foreach ($moves as $m) {
				$has = true;
				$cnt++;
				$running += (float) $m['qty'];
				$isIn = ($m['txn_type'] === 'in' || (float) $m['qty_in'] > 0);
				$typeLbl = $isIn ? 'INWARD' : 'OUTWARD';
				$typeClass = $isIn ? 'label-success' : 'label-danger';
				$dateShow = '';
				if (!empty($m['date']) && $m['date'] != '0000-00-00') {
					$dateShow = date('d-m-Y', strtotime($m['date']));
				}
				$billShow = $m['bill_no'] !== '' ? $m['bill_no'] : '-';
				$code = (isset($m['catno']) && $m['catno'] !== '' && $m['catno'] !== '-') ? $m['catno'] : '';
				$label = $code !== '' ? ($code . ' - ' . $m['pro_name']) : $m['pro_name'];
				?>
				<tr>
					<td><?php echo $cnt; ?></td>
					<td><?php echo htmlspecialchars($dateShow); ?></td>
					<td>
						<?php if (!empty($m['ref_order_id']) && $m['bill_no'] !== '') { ?>
							<a target="_blank" href="order_viewer.php?order_id=<?php echo (int) $m['ref_order_id']; ?>"><?php echo htmlspecialchars($billShow); ?></a>
						<?php } else {
							echo htmlspecialchars($billShow);
						} ?>
					</td>
					<td><span class="label <?php echo $typeClass; ?>"><?php echo $typeLbl; ?></span></td>
					<td><?php echo htmlentities($label); ?></td>
					<td class="text-right" style="color:#1e8449;font-weight:600;"><?php echo $m['qty_in'] > 0 ? htmlentities($m['qty_in']) : ''; ?></td>
					<td class="text-right" style="color:#c0392b;font-weight:600;"><?php echo $m['qty_out'] > 0 ? htmlentities($m['qty_out']) : ''; ?></td>
					<td class="text-right"><strong><?php echo htmlentities($running); ?></strong></td>
					<td><?php echo htmlentities($m['remark']); ?></td>
				</tr>
				<?php
			}
			if (!$has) {
				?>
				<tr><td colspan="9" class="text-center">No inward / outward entries found.</td></tr>
				<?php
			} else {
				?>
				<tr style="background:#f0f0f0;font-weight:700;">
					<td colspan="7" class="text-right">Closing Available Qty</td>
					<td class="text-right"><?php echo htmlentities($running); ?></td>
					<td></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<p class="help-block" style="margin-top:8px;">
			<strong>Note:</strong> Inward = stock from Armor order. Outward = stock issued when customer order is Dispatched / Payment Received.
		</p>
	<?php } ?>
<?php } else {
	/* Armor: all CP stock overview */
	$cps = $db->rp_getData("executive", "id,company_name,client_code", "channel_partner_flag=1 AND customer_flag=0 AND isDelete=0", "company_name ASC", 0);
	?>
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>Sr.</th>
				<th>Channel Partner</th>
				<th>Code</th>
				<th>Products with Stock</th>
				<th>Total Qty</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$cnt = 0;
		$has = false;
		if ($cps) {
			while ($cp = mysqli_fetch_assoc($cps)) {
				$sumR = $db->rp_getData(
					"customer_inward_stock",
					"COUNT(DISTINCT pro_id) AS pro_cnt, COALESCE(SUM(pro_qty),0) AS tot",
					"customer_id='" . (int) $cp['id'] . "' AND isDelete=0",
					"",
					0
				);
				$sum = $sumR ? mysqli_fetch_assoc($sumR) : array('pro_cnt' => 0, 'tot' => 0);
				if ((float) $sum['tot'] == 0 && (int) $sum['pro_cnt'] == 0) {
					continue;
				}
				$has = true;
				$cnt++;
				?>
				<tr>
					<td><?php echo $cnt; ?></td>
					<td><?php echo htmlspecialchars($cp['company_name']); ?></td>
					<td><?php echo htmlspecialchars($cp['client_code']); ?></td>
					<td><?php echo (int) $sum['pro_cnt']; ?></td>
					<td><strong><?php echo htmlentities($sum['tot']); ?></strong></td>
					<td><a class="btn btn-xs blue" href="channel_partner_stock_manage.php?cp_id=<?php echo (int) $cp['id']; ?>">View</a></td>
				</tr>
				<?php
			}
		}
		if (!$has) {
			?>
			<tr><td colspan="6" class="text-center">No Channel Partner stock yet. Credit stock from a CP order (Action → Credit Stock to CP).</td></tr>
			<?php
		}
		?>
		</tbody>
	</table>
<?php } ?>
<?php require_once("disconnect.php"); ?>
