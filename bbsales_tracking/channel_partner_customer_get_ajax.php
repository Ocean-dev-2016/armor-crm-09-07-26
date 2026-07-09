<?php
$page_id = 555;
$page_slug = 'channel_partner_customer';
include("connect.php");
$ctable = "channel_partner_customer";
$ctable1 = "Channel Partner Customer";

$ctable_where = "";
if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
	$search = $db->clean($_REQUEST['searchName']);
	$ctable_where .= " (
		company_name LIKE '%" . $search . "%' OR
		person_name LIKE '%" . $search . "%' OR
		mobile_no LIKE '%" . $search . "%' OR
		email LIKE '%" . $search . "%'
	) AND ";
}

$ctable_where .= " isDelete='0'";

$item_per_page = ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 10;

if (isset($_REQUEST["page"])) {
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
	if (!is_numeric($page_number)) {
		die('Invalid page number!');
	}
} else {
	$page_number = 1;
}

$get_total_rows = $db->rp_getTotalRecord($ctable, $ctable_where);
$total_pages = ceil($get_total_rows / $item_per_page);
$page_position = (($page_number - 1) * $item_per_page);
$ctable_r = $db->rp_getData($ctable, "*", $ctable_where, "id DESC limit $page_position, $item_per_page");
?>
<table id="datatable_1" class="table table-bordered table-striped dataTable">
	<thead>
		<tr>
			<th></th>
			<th>Channel Partner</th>
			<th>Customer Name</th>
			<th>Person Name</th>
			<th>Mobile</th>
			<th>Email</th>
			<th>State</th>
			<th>City</th>
			<th>Pincode</th>
			<th>Created At</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if ($ctable_r) {
		while ($ctable_d = mysqli_fetch_array($ctable_r)) {
			$cp_name = "-";
			if (!empty($ctable_d['channel_partner_id'])) {
				$cp_name = $db->rp_getValue("executive", "company_name", "id='" . (int) $ctable_d['channel_partner_id'] . "'", 0);
				if ($cp_name == "") {
					$cp_name = $db->rp_getValue("executive", "cname", "id='" . (int) $ctable_d['channel_partner_id'] . "'", 0);
				}
			}
	?>
		<tr>
			<td>
				<?php if ($rights['update_flag'] == 1 || $rights['delete_flag'] == 1) { ?>
				<div class="btn-group">
					<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
						<i class="fa fa-gear"></i>
					</button>
					<ul role="menu" class="dropdown-menu">
						<?php if ($rights['update_flag'] == 1) { ?>
						<li>
							<a href="channel_partner_customer_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>">
								<span class="text-primary"><i class="fa fa-pencil"></i> Edit</span>
							</a>
						</li>
						<?php } ?>
						<?php if ($rights['delete_flag'] == 1) { ?>
						<li>
							<a href="javascript:void(0);" onClick="del_conf('<?php echo $ctable_d['id']; ?>');">
								<span class="text-danger"><i class="fa fa-times"></i> Delete</span>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
			</td>
			<td><?php echo htmlentities($cp_name); ?></td>
			<td><?php echo htmlentities($ctable_d['company_name']); ?></td>
			<td><?php echo htmlentities($ctable_d['person_name']); ?></td>
			<td><?php echo htmlentities($ctable_d['mobile_no']); ?></td>
			<td><?php echo htmlentities($ctable_d['email']); ?></td>
			<td><?php echo htmlentities($ctable_d['state']); ?></td>
			<td><?php echo htmlentities($ctable_d['city']); ?></td>
			<td><?php echo htmlentities($ctable_d['pincode']); ?></td>
			<td><?php
				$created_on = !empty($ctable_d['created_date']) ? $ctable_d['created_date'] : $ctable_d['created_at'];
				echo ($created_on != "") ? date('d-m-Y H:i', strtotime($created_on)) : "-";
			?></td>
		</tr>
	<?php
		}
	}
	?>
	</tbody>
</table>
<?php
if ($total_pages > 1) {
?>
<div class="row">
	<div class="col-md-6">
		<div class="dataTables_info">Total Records: <?php echo $get_total_rows; ?></div>
	</div>
	<div class="col-md-6">
		<div class="dataTables_paginate paging_simple_numbers">
			<ul class="pagination">
				<?php for ($i = 1; $i <= $total_pages; $i++) { ?>
					<li class="<?php echo ($i == $page_number) ? 'active' : ''; ?>">
						<a href="javascript:void(0);" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<input type="hidden" id="numRecords" value="<?php echo $item_per_page; ?>">
<?php } ?>
<?php require_once("disconnect.php"); ?>
