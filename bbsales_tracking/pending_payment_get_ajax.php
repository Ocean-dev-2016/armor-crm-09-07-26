<?php
include("connect.php");

$item_per_page = isset($_REQUEST["show"]) && (int) $_REQUEST["show"] > 0 ? (int) $_REQUEST["show"] : 50;
$page_number = isset($_REQUEST["page"]) ? (int) $_REQUEST["page"] : 1;
if ($page_number <= 0) {
	$page_number = 1;
}
$page_position = (($page_number - 1) * $item_per_page);

$searchName = isset($_REQUEST['searchName']) ? trim(urldecode($_REQUEST['searchName'])) : '';
$where = "isDelete=0 AND status NOT IN (-2,3) AND (payment_received_flag=0 OR payment_received_flag IS NULL)";
if ($searchName != '') {
	$safe = mysqli_real_escape_string($db->myconn, $searchName);
	$where .= " AND (company_name LIKE '%" . $safe . "%' OR customer_name LIKE '%" . $safe . "%' OR order_no LIKE '%" . $safe . "%')";
}

$total_rows = $db->rp_getTotalRecord("orders", $where);
$total_pages = $total_rows > 0 ? ceil($total_rows / $item_per_page) : 1;
$rows = $db->rp_getData("orders", "id,order_no,order_date,company_name,customer_name,client_code,grand_total", $where, "company_name ASC, customer_name ASC, order_date ASC, id ASC limit $page_position, $item_per_page", 0);
?>
<style>
.table-scrollable {
	width: auto;
	height: 600px;
	overflow-x: auto;
	overflow-y: auto;
	border: 1px solid #e7ecf1;
	margin: 10px 0 !important;
}
.fix-th {
	background-color: #f5f5f5 !important;
	position: sticky;
	top: 0;
	z-index: 1;
}
</style>
<div class="table-scrollable">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
		<thead class="fix-th">
			<tr>
				<th width="60">No</th>
				<th>Customer Name</th>
				<th width="180">Order Number</th>
				<th width="140">Order Date</th>
				<th width="160" class="text-right">Amount</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$sr = $page_position + 1;
			if ($rows) {
				while ($row = mysqli_fetch_assoc($rows)) {
					$label = trim($row['company_name']) != '' ? $row['company_name'] : 'Unknown Customer';
					if (trim($row['customer_name']) != '') {
						$label .= ' — ' . $row['customer_name'];
					}
					if (trim($row['client_code']) != '') {
						$label .= ' (' . $row['client_code'] . ')';
					}
					?>
					<tr style="background-color:#fde8e8;color:#c0392b;">
						<td><?php echo (int) $sr++; ?></td>
						<td><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></td>
						<td><a target="_blank" href="order_viewer.php?order_id=<?php echo (int) $row['id']; ?>"><?php echo htmlspecialchars($row['order_no']); ?></a></td>
						<td><?php echo !empty($row['order_date']) && $row['order_date'] != '0000-00-00' ? date('d-m-Y', strtotime($row['order_date'])) : ''; ?></td>
						<td class="text-right"><?php echo number_format((float) $row['grand_total'], 2); ?></td>
					</tr>
					<?php
				}
			} else {
				echo '<tr><td colspan="5" class="text-center">No pending payments found.</td></tr>';
			}
			?>
		</tbody>
	</table>
</div>
<?php
if ($total_rows > $item_per_page) {
	echo $db->pagination($item_per_page, $page_number, $total_rows, $total_pages);
}
?>
