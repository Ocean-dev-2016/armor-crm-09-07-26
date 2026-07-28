<?php
$page_id = 650;
$page_slug = 'channel_partner_stock';
include("connect.php");
require_once dirname(__FILE__) . '/../include/class.channel_partner_stock.php';

$is_cp_login = function_exists('cp_is_channel_partner_login') && cp_is_channel_partner_login($db);
$cp_login_id = function_exists('cp_get_login_channel_partner_id') ? (int) cp_get_login_channel_partner_id() : 0;
$req_cp = isset($_REQUEST['cp_id']) ? (int) $_REQUEST['cp_id'] : 0;

if ($is_cp_login) {
	$cp_id = $cp_login_id;
} else {
	$cp_id = $req_cp;
}

$stockObj = new ChannelPartnerStock($db);
?>
<?php if ($is_cp_login || $cp_id > 0) {
	$cpName = $db->rp_getValue("executive", "company_name", "id='" . (int) $cp_id . "'", 0);
	$rows = $stockObj->getStockSummary($cp_id);
	?>
	<div class="alert alert-info" style="margin-bottom:10px;">
		<strong><?php echo htmlspecialchars($cpName ? $cpName : 'Channel Partner'); ?></strong> — Available stock (IN − OUT)
	</div>
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>Sr.</th>
				<th>Product</th>
				<th>Cat No</th>
				<th>Weight</th>
				<th>Available Qty</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$cnt = 0;
		$has = false;
		foreach ($rows as $r) {
			$has = true;
			$cnt++;
			$catno = $db->rp_getValue("product_weight_price", "catno", "product_id='" . (int) $r['pro_id'] . "' AND weight_id='" . mysqli_real_escape_string($db->myconn, (string) $r['weight_id']) . "'", 0);
			$wname = '';
			$wCheck = @mysqli_query($db->myconn, "SHOW TABLES LIKE 'weight'");
			if ($wCheck && mysqli_num_rows($wCheck) > 0) {
				$wname = $db->rp_getValue("weight", "name", "id='" . mysqli_real_escape_string($db->myconn, (string) $r['weight_id']) . "'", 0);
			}
			?>
			<tr>
				<td><?php echo $cnt; ?></td>
				<td><?php echo htmlentities($r['pro_name']); ?></td>
				<td><?php echo htmlentities($catno ? $catno : '-'); ?></td>
				<td><?php echo htmlentities($wname ? $wname : $r['weight_id']); ?></td>
				<td><strong><?php echo htmlentities($r['total_qty']); ?></strong></td>
			</tr>
			<?php
		}
		if (!$has) {
			?>
			<tr><td colspan="5" class="text-center">No stock found.</td></tr>
			<?php
		}
		?>
		</tbody>
	</table>
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
