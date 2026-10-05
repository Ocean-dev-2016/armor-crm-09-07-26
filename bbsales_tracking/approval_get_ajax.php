<?php
$page_id = 674;
$page_slug = 'approval_entry';
include('connect.php');
require_once('../include/approval_entry_helper.php');
armor_approval_require_unlock(true);
armor_approval_entry_ensure_table($db);

$ctable = 'approval_entry';
$ctable_where = 'isDelete=0';

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != '') {
	$q = mysqli_real_escape_string($conn, trim($_REQUEST['searchName']));
	$ctable_where .= " AND (
		company_name LIKE '%{$q}%'
		OR person_name LIKE '%{$q}%'
		OR project_name LIKE '%{$q}%'
		OR project_builder LIKE '%{$q}%'
		OR contractor LIKE '%{$q}%'
		OR mobile LIKE '%{$q}%'
		OR given_by LIKE '%{$q}%'
	)";
}

if (isset($_REQUEST['date_filter']) && $_REQUEST['date_filter'] != '') {
	$parts = explode(' to ', $_REQUEST['date_filter']);
	if (count($parts) === 2) {
		$from = mysqli_real_escape_string($conn, trim($parts[0]));
		$to = mysqli_real_escape_string($conn, trim($parts[1]));
		$ctable_where .= " AND entry_date >= '{$from}' AND entry_date <= '{$to}'";
	}
}

if (isset($_REQUEST['status_filter']) && $_REQUEST['status_filter'] != '') {
	$st = mysqli_real_escape_string($conn, trim($_REQUEST['status_filter']));
	$ctable_where .= " AND approval_status='" . $st . "'";
}

if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
	if (isset($rights['personal_flag']) && $rights['personal_flag'] == 1) {
		$ctable_where .= " AND created_by='" . (int)$_SESSION[SITE_SESS . '_ADMIN_SESS_ID'] . "'";
	}
}

$item_per_page = (isset($_REQUEST['show']) && is_numeric($_REQUEST['show'])) ? intval($_REQUEST['show']) : 100;
$page_number = isset($_REQUEST['page']) ? filter_var($_REQUEST['page'], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH) : 1;
if ($page_number < 1) {
	$page_number = 1;
}

$total = (int)$db->rp_getTotalRecord($ctable, $ctable_where, 0);
$total_pages = $total > 0 ? (int)ceil($total / $item_per_page) : 1;
if ($page_number > $total_pages) {
	$page_number = $total_pages;
}
$offset = ($page_number - 1) * $item_per_page;

$total_amount = (float)$db->rp_getValue($ctable, 'SUM(amount)', $ctable_where, 0);
$total_amount_disp = number_format($total_amount, 2);

$ctable_r = $db->rp_getData($ctable, '*', $ctable_where, 'entry_date DESC, id DESC LIMIT ' . (int)$offset . ', ' . (int)$item_per_page, 0);
$canUpdate = (!isset($rights['update_flag']) || $rights['update_flag'] == 1 || (int)$_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0);
$canDelete = (!isset($rights['delete_flag']) || $rights['delete_flag'] == 1 || (int)$_SESSION[SITE_SESS . '_ADMIN_TYPE'] === 0);
?>
<script type="text/javascript">
if (document.getElementById('approval_total_amount')) {
	document.getElementById('approval_total_amount').innerHTML = '<?php echo $total_amount_disp; ?>';
}
</script>
<table id="datatable_1" class="table table-bordered table-striped dataTable">
	<thead>
		<tr>
			<th style="width:5%;"></th>
			<th>No.</th>
			<th>Due Date</th>
			<th>Company Name</th>
			<th>Type of Company</th>
			<th>Person Name</th>
			<th>Designation</th>
			<th>Mobile</th>
			<th>Email</th>
			<th>Amount</th>
			<th>Given by</th>
			<th>Payment Mode</th>
			<th>Approval</th>
			<th>Attachment</th>
			<th>Project Name</th>
			<th>Project Builder</th>
			<th>Contractor</th>
			<th>Created</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if ($ctable_r && mysqli_num_rows($ctable_r) > 0) {
		$count = $offset;
		while ($d = mysqli_fetch_assoc($ctable_r)) {
			$count++;
			$dateDisp = $d['entry_date'] ? date('d/M/Y', strtotime($d['entry_date'])) : '';
			$amtDisp = ($d['amount'] !== null && $d['amount'] !== '') ? number_format((float)$d['amount'], 2) : '';
			$status = trim((string)$d['approval_status']);
			$badgeClass = 'approval-badge-pending';
			if (strcasecmp($status, 'Yes') === 0) {
				$badgeClass = 'approval-badge-yes';
			} elseif (strcasecmp($status, 'No') === 0) {
				$badgeClass = 'approval-badge-no';
			}
			$att = isset($d['attachment']) ? trim((string)$d['attachment']) : '';
			$attUrl = $att !== '' ? armor_approval_attachment_url($att) : '';
			$createdDisp = (!empty($d['created_date']) && $d['created_date'] != '0000-00-00 00:00:00')
				? date('d/M/Y', strtotime($d['created_date']))
				: '—';
			?>
			<tr>
				<td>
					<?php if ($canUpdate || $canDelete) { ?>
					<div class="btn-group">
						<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
							<i class="fa fa-gear"></i>
						</button>
						<ul role="menu" class="dropdown-menu">
							<?php if ($canUpdate) { ?>
							<li>
								<a href="approval_crud.php?mode=edit&id=<?php echo (int)$d['id']; ?>">
									<span class="text-primary"><i class="fa fa-pencil"></i> Edit</span>
								</a>
							</li>
							<?php } ?>
							<?php if ($canDelete) { ?>
							<li>
								<a href="javascript:void(0);" onClick="del_conf('<?php echo (int)$d['id']; ?>');">
									<span class="text-danger"><i class="fa fa-times"></i> Delete</span>
								</a>
							</li>
							<?php } ?>
						</ul>
					</div>
					<?php } ?>
				</td>
				<td><?php echo (int)$count; ?></td>
				<td><?php echo htmlspecialchars($dateDisp); ?></td>
				<td>
					<?php if (!empty($d['customer_id'])) { ?>
					<a href="javascript:;" class="approval-customer-visit"
						data-customer-id="<?php echo (int)$d['customer_id']; ?>"
						data-due-date="<?php echo htmlspecialchars($d['entry_date']); ?>"
						data-company="<?php echo htmlspecialchars($d['company_name']); ?>"
						title="View this month visit data">
						<?php echo htmlspecialchars($d['company_name']); ?>
					</a>
					<?php } else {
						echo htmlspecialchars($d['company_name']);
					} ?>
				</td>
				<td><?php echo htmlspecialchars($d['type_of_company']); ?></td>
				<td><?php echo htmlspecialchars($d['person_name']); ?></td>
				<td><?php echo htmlspecialchars($d['designation']); ?></td>
				<td><?php echo htmlspecialchars($d['mobile']); ?></td>
				<td><?php echo htmlspecialchars($d['email']); ?></td>
				<td><?php echo htmlspecialchars($amtDisp); ?></td>
				<td><?php echo htmlspecialchars($d['given_by']); ?></td>
				<td><?php echo htmlspecialchars($d['payment_mode']); ?></td>
				<td>
					<span class="approval-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status ? $status : 'Pending'); ?></span>
				</td>
				<td>
					<?php if ($attUrl !== '') { ?>
						<a href="<?php echo htmlspecialchars($attUrl); ?>" target="_blank" class="btn btn-xs blue" title="View attachment">
							<i class="fa fa-paperclip"></i>
						</a>
					<?php } else { ?>
						—
					<?php } ?>
				</td>
				<td><?php echo htmlspecialchars($d['project_name']); ?></td>
				<td><?php echo htmlspecialchars($d['project_builder']); ?></td>
				<td><?php echo htmlspecialchars($d['contractor']); ?></td>
				<td><?php echo htmlspecialchars($createdDisp); ?></td>
			</tr>
			<?php
		}
	} else {
		?>
		<tr>
			<td colspan="18" class="text-center">No approval entries found. Click <strong>Add New</strong> to create one.</td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>
<?php if ($total_pages > 1) { ?>
<div class="row">
	<div class="col-md-5 col-sm-12">
		<div class="dataTables_info">
			Total Records: <strong><?php echo (int)$total; ?></strong>
			&nbsp;|&nbsp; Total Amount: <strong><?php echo $total_amount_disp; ?></strong>
		</div>
	</div>
	<div class="col-md-7 col-sm-12">
		<div class="dataTables_paginate paging_simple_numbers">
			<ul class="pagination">
				<?php for ($i = 1; $i <= $total_pages; $i++) { ?>
					<li class="paginate_button <?php echo ($i == $page_number) ? 'active' : ''; ?>">
						<a href="javascript:;" data-page="<?php echo $i; ?>" onclick="displayRecords(100, <?php echo $i; ?>);"><?php echo $i; ?></a>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<?php } else { ?>
<div class="dataTables_info">
	Total Records: <strong><?php echo (int)$total; ?></strong>
	&nbsp;|&nbsp; Total Amount: <strong><?php echo $total_amount_disp; ?></strong>
</div>
<?php } ?>
