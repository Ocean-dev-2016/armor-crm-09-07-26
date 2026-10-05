<?php
$page_id = 674;
$page_slug = 'approval_entry';
include('connect.php');
require_once('../include/approval_entry_helper.php');
armor_approval_require_unlock(true);

$customer_id = isset($_REQUEST['customer_id']) ? (int)$_REQUEST['customer_id'] : 0;
$company_name = isset($_REQUEST['company_name']) ? trim($_REQUEST['company_name']) : '';

/* Default: 1st day of last month → today */
$default_from = date('Y-m-01', strtotime('first day of last month'));
$default_to = date('Y-m-d');

$from_date = isset($_REQUEST['from_date']) ? trim($_REQUEST['from_date']) : $default_from;
$to_date = isset($_REQUEST['to_date']) ? trim($_REQUEST['to_date']) : $default_to;

if ($customer_id <= 0) {
	echo '<div class="alert alert-danger">Invalid customer.</div>';
	exit;
}

if ($from_date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from_date)) {
	$from_date = $default_from;
}
if ($to_date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date)) {
	$to_date = $default_to;
}
if ($from_date > $to_date) {
	$tmp = $from_date;
	$from_date = $to_date;
	$to_date = $tmp;
}

if ($company_name === '') {
	$company_name = $db->rp_getValue('executive', 'company_name', "id='" . $customer_id . "' AND isDelete=0", 0);
}

$from_esc = mysqli_real_escape_string($conn, $from_date);
$to_esc = mysqli_real_escape_string($conn, $to_date);

$where = "isDelete=0 AND customer_id='" . $customer_id . "' AND ("
	. "(start_date_time IS NOT NULL AND start_date_time!='0000-00-00 00:00:00'"
	. " AND DATE(start_date_time)>='" . $from_esc . "' AND DATE(start_date_time)<='" . $to_esc . "')"
	. " OR ((start_date_time IS NULL OR start_date_time='0000-00-00 00:00:00')"
	. " AND DATE(created_date)>='" . $from_esc . "' AND DATE(created_date)<='" . $to_esc . "')"
	. ")";

$visit_r = $db->rp_getData('visit', '*', $where, 'id DESC', 0);
$total = $visit_r ? mysqli_num_rows($visit_r) : 0;

$fromDisp = date('d/M/Y', strtotime($from_date));
$toDisp = date('d/M/Y', strtotime($to_date));
$companyLabel = $company_name ? $company_name : 'Customer';
?>
<div class="av-visit-wrap">
	<div class="av-filter-bar">
		<form id="approvalVisitFilterForm" onsubmit="return false;">
			<input type="hidden" name="customer_id" id="av_customer_id" value="<?php echo (int)$customer_id; ?>">
			<input type="hidden" name="company_name" id="av_company_name" value="<?php echo htmlspecialchars($company_name); ?>">
			<div class="row">
				<div class="col-md-3 col-sm-6">
					<label class="control-label">From Date</label>
					<input type="date" class="form-control" id="av_from_date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>">
				</div>
				<div class="col-md-3 col-sm-6">
					<label class="control-label">To Date</label>
					<input type="date" class="form-control" id="av_to_date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>">
				</div>
				<div class="col-md-6 col-sm-12">
					<label class="control-label">&nbsp;</label>
					<div class="av-filter-actions">
						<button type="button" class="btn blue" id="av_filter_btn"><i class="fa fa-filter"></i> Filter</button>
						<button type="button" class="btn default" id="av_reset_btn" title="Last month to today"><i class="fa fa-refresh"></i> Reset</button>
					</div>
				</div>
			</div>
		</form>
	</div>

	<div class="av-summary">
		<div class="av-summary-left">
			<i class="fa fa-building-o"></i>
			<strong><?php echo htmlspecialchars($companyLabel); ?></strong>
		</div>
		<div class="av-summary-mid">
			<i class="fa fa-calendar"></i>
			<?php echo htmlspecialchars($fromDisp); ?> <span class="text-muted">to</span> <?php echo htmlspecialchars($toDisp); ?>
		</div>
		<div class="av-summary-right">
			<span class="av-total-badge">Total Visits: <strong><?php echo (int)$total; ?></strong></span>
		</div>
	</div>

	<div class="table-responsive av-table-wrap">
		<table class="table table-striped table-bordered table-hover av-visit-table">
			<thead>
				<tr>
					<th style="width:50px;">No.</th>
					<th style="width:140px;">Sales Person</th>
					<th style="width:150px;">Visit Date &amp; Time</th>
					<th style="width:110px;">Visit Type</th>
					<th>Address</th>
					<th>Remark</th>
					<th style="width:60px;" class="text-center">View</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if ($visit_r && $total > 0) {
				$n = 0;
				while ($v = mysqli_fetch_assoc($visit_r)) {
					$n++;
					$salesName = $db->rp_getValue('sales_executive', 'name', "id='" . (int)$v['user_id'] . "'", 0);
					if ($salesName == '') {
						$salesName = '—';
					}
					$visitDt = '';
					if (!empty($v['start_date_time']) && $v['start_date_time'] != '0000-00-00 00:00:00') {
						$visitDt = date('d/M/Y h:i A', strtotime($v['start_date_time']));
					} elseif (!empty($v['created_date']) && $v['created_date'] != '0000-00-00 00:00:00') {
						$visitDt = date('d/M/Y h:i A', strtotime($v['created_date']));
					}
					$visitType = !empty($v['visit_type']) ? $v['visit_type'] : '—';
					$address = !empty($v['app_address']) ? $v['app_address'] : (!empty($v['address']) ? $v['address'] : '—');
					$remark = isset($v['remark']) ? stripslashes($v['remark']) : '';
					?>
					<tr>
						<td class="text-center"><?php echo (int)$n; ?></td>
						<td><?php echo htmlspecialchars($salesName); ?></td>
						<td><?php echo htmlspecialchars($visitDt); ?></td>
						<td><?php echo htmlspecialchars($visitType); ?></td>
						<td><?php echo htmlspecialchars($address); ?></td>
						<td><?php echo htmlspecialchars($remark); ?></td>
						<td class="text-center">
							<a href="visit_viewer.php?visit_id=<?php echo (int)$v['id']; ?>" target="_blank" class="btn btn-xs blue" title="View Visit">
								<i class="fa fa-eye"></i>
							</a>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="7" class="text-center av-empty">
						<i class="fa fa-info-circle"></i>
						No visit found for this customer from <strong><?php echo htmlspecialchars($fromDisp); ?></strong> to <strong><?php echo htmlspecialchars($toDisp); ?></strong>.
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</div>
</div>
