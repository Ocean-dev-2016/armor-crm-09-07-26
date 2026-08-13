<?php
$page_id = 672;
$page_slug = 'assign_kra';
include('connect.php');

$ctable = 'executive';
$ctable_where = 'isDelete=0 AND isActive=1 AND seid!=0 AND seid IS NOT NULL AND seid!=\'\'';

$searchName = isset($_REQUEST['searchName']) ? $db->clean($_REQUEST['searchName']) : '';
$filter_state = isset($_REQUEST['filter_state']) ? $db->clean($_REQUEST['filter_state']) : '';
$filter_seid = isset($_REQUEST['filter_seid']) ? (int) $_REQUEST['filter_seid'] : 0;
$page_num = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
$show = isset($_REQUEST['show']) ? (int) $_REQUEST['show'] : 25;
if ($show <= 0) {
	$show = 25;
}
$start = ($page_num - 1) * $show;

if ($searchName !== '') {
	$ctable_where .= " AND (company_name LIKE '%" . $searchName . "%' OR cname LIKE '%" . $searchName . "%' OR client_code LIKE '%" . $searchName . "%' OR mobile_no1 LIKE '%" . $searchName . "%')";
}
if ($filter_state !== '') {
	$ctable_where .= " AND state='" . $filter_state . "'";
}
if ($filter_seid > 0) {
	$ctable_where .= ' AND seid=' . $filter_seid;
}

$total = (int) $db->rp_getTotalRecord($ctable, $ctable_where, 0);
$ctable_r = $db->rp_getData($ctable, 'id, company_name, cname, state, city, client_code, mobile_no1, seid, modify_date', $ctable_where, 'company_name ASC', 0, $start . ',' . $show);
?>
<div class="kra-total-bar">
	<span class="label label-primary"><i class="fa fa-users"></i> Total Assigned: <?php echo number_format((int) $total); ?></span>
	<span class="text-muted" style="margin-left:10px;font-size:12px;">Showing page <?php echo (int) $page_num; ?> of <?php echo max(1, (int) ceil($total / $show)); ?></span>
</div>
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th width="45" class="text-center">No.</th>
				<th>Firm Name</th>
				<th>Person Name</th>
				<th>State</th>
				<th>City</th>
				<th>Client Code</th>
				<th>Phone</th>
				<th>Sales Person</th>
				<th width="130">Last Updated</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ($ctable_r && mysqli_num_rows($ctable_r) > 0) {
				$i = $start + 1;
				while ($row = mysqli_fetch_assoc($ctable_r)) {
					$spName = $db->rp_getValue('sales_executive', 'name', 'id=' . (int) $row['seid'] . ' AND isDelete=0', 0);
					$spState = $db->rp_getValue('sales_executive', 'state', 'id=' . (int) $row['seid'] . ' AND isDelete=0', 0);
					$spLabel = $spName;
					if ($spState !== false && $spState !== '') {
						$spLabel .= ' - ' . $spState;
					}
					$modDate = ($row['modify_date'] && $row['modify_date'] !== '0000-00-00 00:00:00')
						? date('d-m-Y H:i', strtotime($row['modify_date']))
						: '-';
			?>
					<tr>
						<td class="text-center"><?php echo $i++; ?></td>
						<td><?php echo htmlspecialchars(stripslashes($row['company_name'])); ?></td>
						<td><?php echo htmlspecialchars(stripslashes($row['cname'])); ?></td>
						<td><?php echo htmlspecialchars(stripslashes($row['state'])); ?></td>
						<td><?php echo htmlspecialchars(stripslashes($row['city'])); ?></td>
						<td><?php echo htmlspecialchars(stripslashes($row['client_code'])); ?></td>
						<td><?php echo htmlspecialchars(stripslashes($row['mobile_no1'])); ?></td>
						<td><?php echo htmlspecialchars(stripslashes($spLabel)); ?></td>
						<td><?php echo $modDate; ?></td>
					</tr>
			<?php
				}
			} else {
			?>
				<tr>
					<td colspan="9" class="text-center">No assigned customers found.</td>
				</tr>
			<?php
			}
			?>
		</tbody>
	</table>
</div>
<?php
$totalPages = $show > 0 ? (int) ceil($total / $show) : 1;
if ($totalPages < 1) {
	$totalPages = 1;
}
if ($page_num < 1) {
	$page_num = 1;
}
if ($page_num > $totalPages) {
	$page_num = $totalPages;
}
if ($totalPages > 1) {
?>
<div class="row">
	<div class="col-md-12 text-center">
		<ul class="pagination pagination-sm" style="margin:12px 0 0;">
			<?php echo $db->rp_paginate_function($show, $page_num, $total, $totalPages); ?>
		</ul>
	</div>
</div>
<?php
}
include 'disconnect.php';
