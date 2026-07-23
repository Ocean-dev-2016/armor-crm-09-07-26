<?php
$page_id = 650;
$page_slug = 'channel_partner_stock';
include("connect.php");

$is_cp_login = cp_is_channel_partner_login($db);
$cp_login_id = cp_get_login_channel_partner_id();
if (!$is_cp_login || $cp_login_id <= 0) {
	echo '<div class="alert alert-danger">Access denied.</div>';
	require_once("disconnect.php");
	exit;
}

$where = "customer_id='" . (int) $cp_login_id . "' AND isDelete=0 GROUP BY pro_id, pro_name";
$rows = $db->rp_getData(
	"customer_inward_stock",
	"pro_id, pro_name, SUM(pro_qty) AS total_qty",
	$where,
	"pro_name ASC",
	0
);
?>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>Sr.</th>
			<th>Product</th>
			<th>Cat No</th>
			<th>Available Qty</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$cnt = 0;
	$has = false;
	if ($rows) {
		while ($r = mysqli_fetch_assoc($rows)) {
			$has = true;
			$cnt++;
			$catno = $db->rp_getValue("product_weight_price", "catno", "product_id='" . (int) $r['pro_id'] . "'", 0);
			?>
			<tr>
				<td><?php echo $cnt; ?></td>
				<td><?php echo htmlentities($r['pro_name']); ?></td>
				<td><?php echo htmlentities($catno ? $catno : '-'); ?></td>
				<td><?php echo htmlentities($r['total_qty']); ?></td>
			</tr>
			<?php
		}
	}
	if (!$has) {
		?>
		<tr>
			<td colspan="4" class="text-center">No stock found for your account.</td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>
<?php require_once("disconnect.php"); ?>
