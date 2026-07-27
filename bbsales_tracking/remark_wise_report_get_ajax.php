<?php
$page_id = 671;
$page_slug = 'remark_wise_report';
include("connect.php");
require_once("../include/class.remark_analysis_report.php");

$report = new RemarkAnalysisReport($db);
$fromDate = isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : "";
$toDate = isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : "";
$employeeIds = isset($_REQUEST['employee_ids']) ? $_REQUEST['employee_ids'] : "";
$customerIds = isset($_REQUEST['customer_ids']) ? $_REQUEST['customer_ids'] : "";
$remarkCode = isset($_REQUEST['remark_code']) ? $_REQUEST['remark_code'] : "";

$item_per_page = (isset($_REQUEST["show"]) && $_REQUEST["show"] != "" && is_numeric($_REQUEST["show"])) ? intval($_REQUEST["show"]) : 10;
if ($item_per_page < 1) {
	$item_per_page = 10;
}
if (isset($_REQUEST["page"]) && $_REQUEST["page"] != "") {
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
	if (!is_numeric($page_number) || (int) $page_number < 1) {
		$page_number = 1;
	} else {
		$page_number = (int) $page_number;
	}
} else {
	$page_number = 1;
}

$data = $report->build($fromDate, $toDate, $employeeIds, $customerIds, $remarkCode, $rights);
$hierarchy = $data['hierarchy'];
$remarkLabels = $data['remark_labels'];
$reasonLabels = $data['reason_labels'];
$summary = $data['summary'];
$allVisits = $data['visits'];
$get_total_rows = count($allVisits);
$total_pages = ($item_per_page > 0) ? (int) ceil($get_total_rows / $item_per_page) : 1;
if ($total_pages < 1) {
	$total_pages = 1;
}
if ($page_number > $total_pages) {
	$page_number = $total_pages;
}
$page_position = ($page_number - 1) * $item_per_page;
$visits = array_slice($allVisits, $page_position, $item_per_page);
$visits = $report->attachFormsToVisits($visits);

function rar_h($value)
{
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rar_payment_label($value)
{
	if ($value === '0' || $value === 0) {
		return 'Advance';
	}
	if ($value === '1' || $value === 1) {
		return '30 Days';
	}
	return (string) $value;
}

function rar_format_duration($minutes)
{
	if ($minutes === null || $minutes === "") {
		return "-";
	}
	$minutes = (int) $minutes;
	if ($minutes < 0) {
		return "-";
	}
	$days = (int) floor($minutes / 1440);
	$hours = (int) floor(($minutes % 1440) / 60);
	$mins = $minutes % 60;
	$parts = array();
	if ($days > 0) {
		$parts[] = $days . "d";
	}
	if ($hours > 0 || $days > 0) {
		$parts[] = $hours . "h";
	}
	$parts[] = $mins . "m";
	return implode(" ", $parts);
}

function rar_render_form_html($visit)
{
	$html = '';
	if (!empty($visit['consultant_form']) && is_array($visit['consultant_form'])) {
		$vf = $visit['consultant_form'];
		$typeLabel = (isset($vf['consultant_type']) && $vf['consultant_type'] == 'government')
			? 'Government Consultant Approval'
			: 'Private Consultant Approval';
		if (isset($vf['reason_code']) && strtoupper($vf['reason_code']) == 'C2') {
			$typeLabel = 'Government Consultant Approval';
		} else if (isset($vf['reason_code']) && strtoupper($vf['reason_code']) == 'C1') {
			$typeLabel = 'Private Consultant Approval';
		}
		$html .= '<div class="rar-form-block rar-consultant-block">';
		$html .= '<h4 style="margin-top:0;color:#1a7a3a;"><i class="fa fa-user"></i> Need Approval / Consultant Form</h4>';
		$html .= '<table class="table table-bordered table-condensed" style="margin-bottom:10px;">';
		$html .= '<tr><th style="width:160px;">Type</th><td>' . rar_h($typeLabel) . '</td></tr>';
		$html .= '<tr><th>Firm Name</th><td>' . rar_h($vf['firm_name']) . '</td></tr>';
		$html .= '<tr><th>Address</th><td>' . nl2br(rar_h($vf['address'])) . '</td></tr>';
		$html .= '<tr><th>City</th><td>' . rar_h($vf['city']) . '</td></tr>';
		$html .= '<tr><th>State</th><td>' . rar_h($vf['state']) . '</td></tr>';
		$html .= '<tr><th>Pincode</th><td>' . rar_h($vf['pincode']) . '</td></tr>';
		$html .= '<tr><th>Contact Person</th><td>' . rar_h($vf['contact_person']) . '</td></tr>';
		$html .= '<tr><th>Mobile</th><td>' . rar_h($vf['mobile']) . '</td></tr>';
		$html .= '<tr><th>Email</th><td>' . rar_h(isset($vf['email']) ? $vf['email'] : '') . '</td></tr>';
		$html .= '</table></div>';
	}
	if (!empty($visit['high_rate_form']) && is_array($visit['high_rate_form'])) {
		$hf = $visit['high_rate_form'];
		$payLabel = isset($hf['payment_option']) ? rar_payment_label($hf['payment_option']) : '';
		$html .= '<div class="rar-form-block rar-highrate-block">';
		$html .= '<h4 style="margin-top:0;color:#c85a12;"><i class="fa fa-line-chart"></i> High Rate Analysis Form</h4>';
		$html .= '<table class="table table-bordered table-condensed" style="margin-bottom:10px;">';
		$html .= '<tr><th style="width:160px;">Customer Name</th><td>' . rar_h($hf['customer_name']) . '</td></tr>';
		$html .= '<tr><th>Payment</th><td>' . rar_h($payLabel) . '</td></tr>';
		$html .= '<tr><th>Payment Remark</th><td>' . rar_h(isset($hf['payment_remark']) ? $hf['payment_remark'] : '') . '</td></tr>';
		$html .= '</table>';
		if (!empty($visit['high_rate_items'])) {
			$html .= '<table class="table table-bordered table-striped table-condensed" style="margin-bottom:0;">';
			$html .= '<thead><tr style="background:#f5f5f5;"><th>Product</th><th>Given Rate</th><th>Qty</th><th>Customer Rate</th><th>Remark</th></tr></thead><tbody>';
			foreach ($visit['high_rate_items'] as $hi) {
				$html .= '<tr>';
				$html .= '<td>' . rar_h($hi['product_name']) . '</td>';
				$html .= '<td>' . rar_h($hi['given_rate']) . '</td>';
				$html .= '<td>' . rar_h($hi['qty']) . '</td>';
				$html .= '<td>' . rar_h($hi['customer_rate']) . '</td>';
				$html .= '<td>' . rar_h(isset($hi['remark']) ? $hi['remark'] : '') . '</td>';
				$html .= '</tr>';
			}
			$html .= '</tbody></table>';
		} else {
			$html .= '<div class="text-muted">No product rows found.</div>';
		}
		$html .= '</div>';
	}
	return $html;
}
?>
<div class="row" style="margin-bottom:15px;">
	<div class="col-md-12">
		<strong>Period:</strong>
		<?php echo rar_h(date("d/m/Y", strtotime($data['range']['from']))); ?>
		to
		<?php echo rar_h(date("d/m/Y", strtotime($data['range']['to']))); ?>
		&nbsp;|&nbsp;
		<strong>Total Visits:</strong> <?php echo (int) $data['total_visits']; ?>
		&nbsp;|&nbsp;
		<strong>Total Duration:</strong> <?php echo rar_h(rar_format_duration(isset($data['total_duration_minutes']) ? $data['total_duration_minutes'] : 0)); ?>
		<?php if (!empty($data['range']['was_limited'])) { ?>
			<span class="text-warning">(Date range limited to max days)</span>
		<?php } ?>
		<?php if ((int) $data['total_visits'] < 1 && !empty($data['query_error'])) { ?>
			<div class="alert alert-danger" style="margin-top:10px;">Report query error: <?php echo rar_h($data['query_error']); ?></div>
		<?php } ?>
	</div>
</div>

<div class="row">
	<div class="col-md-5">
		<div class="portlet box green">
			<div class="portlet-title">
				<div class="caption"><i class="fa fa-list"></i> Visit Remark Code Summary</div>
			</div>
			<div class="portlet-body" style="padding:0;">
				<table class="table table-bordered table-striped rar-summary-table" style="margin:0;">
					<thead>
						<tr>
							<th style="width:90px;">Code</th>
							<th>Description</th>
							<th style="width:80px;text-align:center;">Count</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($hierarchy as $parent => $children) {
							$parentCount = isset($summary['parents'][$parent]) ? (int) $summary['parents'][$parent] : 0;
							?>
							<tr class="rar-parent-row">
								<td><span class="rar-code"><?php echo rar_h($parent); ?> -</span></td>
								<td><?php echo rar_h($remarkLabels[$parent]); ?></td>
								<td style="text-align:center;"><?php echo $parentCount; ?></td>
							</tr>
							<?php foreach ($children as $child) {
								$childCount = isset($summary['children'][$child]) ? (int) $summary['children'][$child] : 0;
								?>
								<tr class="rar-child-row">
									<td><span class="rar-code"><?php echo rar_h($child); ?></span></td>
									<td><?php echo rar_h($reasonLabels[$child]); ?></td>
									<td style="text-align:center;"><?php echo $childCount; ?></td>
								</tr>
							<?php } ?>
						<?php } ?>
						<?php if ((int) $summary['unknown'] > 0) { ?>
							<tr>
								<td><span class="rar-code">-</span></td>
								<td>Unknown / No Remark Code</td>
								<td style="text-align:center;"><?php echo (int) $summary['unknown']; ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-md-7">
		<div class="alert alert-info" style="margin-top:0;">
			<strong>Visit Remark Code</strong>
			<ul style="margin:8px 0 0 18px;padding:0;">
				<?php foreach ($hierarchy as $parent => $children) { ?>
					<li>
						<strong style="color:#1a7a3a;"><?php echo rar_h($parent); ?></strong>
						- <?php echo rar_h($remarkLabels[$parent]); ?>
						<?php if (!empty($children)) { ?>
							<ul>
								<?php foreach ($children as $child) { ?>
									<li>
										<strong style="color:#1a7a3a;"><?php echo rar_h($child); ?></strong>
										: <?php echo rar_h($reasonLabels[$child]); ?>
									</li>
								<?php } ?>
							</ul>
						<?php } ?>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>

<div class="portlet box blue-hoki">
	<div class="portlet-title">
		<div class="caption"><i class="fa fa-table"></i> Remark Wise Visit Detail</div>
	</div>
	<div class="portlet-body" style="overflow-x:auto;">
		<?php if ($get_total_rows < 1) { ?>
			<div class="alert alert-warning" style="margin:0;">No visits found for selected filters.</div>
		<?php } else { ?>
			<style>
				.rar-detail-wrap { width:100%; overflow-x:auto; }
				.rar-detail-table {
					width:100%;
					min-width:1100px;
					table-layout:fixed;
					margin:0;
					border-collapse:collapse;
					font-size:12px;
				}
				.rar-detail-table > thead > tr > th {
					background:#34495e !important;
					color:#fff !important;
					font-weight:600;
					text-align:center;
					vertical-align:middle !important;
					padding:8px 6px !important;
					border:1px solid #2c3e50 !important;
					white-space:normal;
					line-height:1.3;
				}
				.rar-detail-table > tbody > tr > td {
					vertical-align:top !important;
					padding:8px 6px !important;
					border:1px solid #e0e6ed !important;
					white-space:normal !important;
					word-wrap:break-word;
					overflow-wrap:anywhere;
					line-height:1.4;
					background:#fff;
				}
				.rar-detail-table > tbody > tr:nth-child(even) > td { background:#f8fafc; }
				.rar-c-sr { width:3%; text-align:center !important; }
				.rar-c-date { width:7%; text-align:center !important; white-space:nowrap !important; }
				.rar-c-sales { width:9%; }
				.rar-c-customer { width:26%; }
				.rar-c-duration { width:7%; text-align:center !important; font-weight:700; color:#1f4e79; white-space:nowrap !important; }
				.rar-c-remark { width:5%; text-align:center !important; }
				.rar-c-reason { width:5%; text-align:center !important; }
				.rar-c-desc { width:10%; }
				.rar-c-stop { width:14%; }
				.rar-c-form { width:7%; text-align:center !important; }
				.rar-c-status { width:7%; text-align:center !important; }
				.rar-cust-box {
					background:#f4f8fb;
					border:1px solid #d9e6f2;
					border-radius:4px;
					padding:7px 8px;
				}
				.rar-cust-code { color:#1a7a3a; font-weight:700; font-size:11px; margin-bottom:2px; }
				.rar-cust-company { font-weight:700; color:#2c3e50; font-size:13px; margin-bottom:3px; }
				.rar-cust-line { color:#555; margin-bottom:2px; }
				.rar-cust-meta { color:#666; font-size:11px; margin-bottom:2px; }
				.rar-cust-addr { color:#777; font-size:11px; margin-top:4px; line-height:1.35; }
				.rar-cust-type { margin-top:5px; }
				.rar-cust-type .label { font-size:10px; }
			</style>
			<div class="rar-detail-wrap">
			<table class="table table-bordered rar-detail-table">
				<thead>
					<tr>
						<th class="rar-c-sr">#</th>
						<th class="rar-c-date">Date</th>
						<th class="rar-c-sales">Sales Person</th>
						<th class="rar-c-customer">Customer</th>
						<th class="rar-c-duration">Visit Duration</th>
						<th class="rar-c-remark">Remark</th>
						<th class="rar-c-reason">Reason</th>
						<th class="rar-c-desc">Description</th>
						<th class="rar-c-stop">Stop Remark</th>
						<th class="rar-c-form">Form</th>
						<th class="rar-c-status">Status</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sr = $page_position;
					foreach ($visits as $visit) {
						$sr++;
						$desc = "";
						if ($visit['reason_code'] != "" && $visit['reason_label'] != "") {
							$desc = $visit['reason_code'] . ": " . $visit['reason_label'];
						} else if ($visit['remark_code'] != "" && $visit['remark_label'] != "") {
							$desc = $visit['remark_code'] . ": " . $visit['remark_label'];
						} else {
							$desc = "-";
						}
						$dateLabel = ($visit['visit_date'] != "") ? date("d/m/Y", strtotime($visit['visit_date'])) : "-";
						$hasConsultant = !empty($visit['has_consultant_form']);
						$hasHighRate = !empty($visit['has_high_rate_form']);
						$formHtml = ($hasConsultant || $hasHighRate) ? rar_render_form_html($visit) : '';
						$formTitle = '';
						if ($hasConsultant && $hasHighRate) {
							$formTitle = 'Consultant + High Rate Form';
						} else if ($hasConsultant) {
							$formTitle = 'Consultant Approval Form';
						} else if ($hasHighRate) {
							$formTitle = 'High Rate Analysis Form';
						}
						$custCode    = isset($visit['customer_code']) ? trim((string) $visit['customer_code']) : "";
						$custPerson  = isset($visit['customer_person']) ? trim((string) $visit['customer_person']) : "";
						$custMobile  = isset($visit['customer_mobile']) ? trim((string) $visit['customer_mobile']) : "";
						if ($custMobile == "" && isset($visit['inquiry_mobile'])) {
							$custMobile = trim((string) $visit['inquiry_mobile']);
						}
						$custTurnover = isset($visit['customer_turnover']) ? trim((string) $visit['customer_turnover']) : "";
						$custTurnYear = isset($visit['customer_turnover_year']) ? trim((string) $visit['customer_turnover_year']) : "";
						$custGst     = isset($visit['customer_gst']) ? trim((string) $visit['customer_gst']) : "";
						$custAddress = isset($visit['customer_address']) ? trim((string) $visit['customer_address']) : "";
						$custCity    = isset($visit['customer_city']) ? trim((string) $visit['customer_city']) : "";
						$custType    = isset($visit['customer_type']) ? trim((string) $visit['customer_type']) : "";
						$custCompany = isset($visit['customer_name']) ? trim((string) $visit['customer_name']) : "";
						$turnLabel = ($custTurnover != "") ? $custTurnover : "-";
						if ($custTurnYear != "" && $custTurnYear != "0") {
							$turnLabel .= " (" . $custTurnYear . ")";
						}
						$personMobile = trim(($custPerson != "" ? $custPerson : "-") . "  |  " . ($custMobile != "" ? $custMobile : "-"));
						?>
						<tr>
							<td class="rar-c-sr"><?php echo $sr; ?></td>
							<td class="rar-c-date"><?php echo rar_h($dateLabel); ?></td>
							<td class="rar-c-sales"><?php echo rar_h($visit['sales_person'] != "" ? $visit['sales_person'] : "-"); ?></td>
							<td class="rar-c-customer">
								<div class="rar-cust-box">
									<?php if ($custCode != "") { ?>
										<div class="rar-cust-code"><?php echo rar_h($custCode); ?></div>
									<?php } ?>
									<div class="rar-cust-company"><?php echo rar_h($custCompany != "" ? $custCompany : "-"); ?></div>
									<div class="rar-cust-line"><?php echo rar_h($personMobile); ?></div>
									<div class="rar-cust-meta"><b>Turnover:</b> <?php echo rar_h($turnLabel); ?></div>
									<div class="rar-cust-meta"><b>GST:</b> <?php echo rar_h($custGst != "" ? $custGst : "-"); ?></div>
									<div class="rar-cust-meta"><b>City:</b> <?php echo rar_h($custCity != "" ? $custCity : "-"); ?></div>
									<?php if ($custAddress != "") { ?>
										<div class="rar-cust-addr"><?php echo rar_h($custAddress); ?></div>
									<?php } ?>
									<?php if ($custType != "") { ?>
										<div class="rar-cust-type"><span class="label label-default"><?php echo rar_h($custType); ?></span></div>
									<?php } ?>
								</div>
							</td>
							<td class="rar-c-duration">
								<?php echo rar_h(rar_format_duration(isset($visit['duration_minutes']) ? $visit['duration_minutes'] : null)); ?>
							</td>
							<td class="rar-c-remark"><span class="rar-code"><?php echo rar_h($visit['remark_code'] != "" ? $visit['remark_code'] : "-"); ?></span></td>
							<td class="rar-c-reason"><span class="rar-code"><?php echo rar_h($visit['reason_code'] != "" ? $visit['reason_code'] : "-"); ?></span></td>
							<td class="rar-c-desc"><?php echo rar_h($desc); ?></td>
							<td class="rar-c-stop">
								<?php echo rar_h($visit['stop_remark'] != "" ? $visit['stop_remark'] : "-"); ?>
								<?php $visitNote = isset($visit['note']) ? trim($visit['note']) : ''; ?>
								<?php if ($visitNote != '') { ?>
									<br><small class="text-info"><i class="fa fa-sticky-note-o"></i> <?php echo rar_h($visitNote); ?></small>
								<?php } ?>
							</td>
							<td class="rar-c-form">
								<?php if ($formHtml != "") { ?>
									<button type="button"
										class="btn btn-xs btn-primary rar-view-form-btn"
										data-title="<?php echo rar_h($formTitle); ?>"
										data-visit-id="<?php echo (int) $visit['id']; ?>">
										<i class="fa fa-eye"></i> View
									</button>
									<div class="rar-form-content" id="rar-form-<?php echo (int) $visit['id']; ?>" style="display:none;">
										<?php echo $formHtml; ?>
									</div>
								<?php } else if ($visit['remark_code'] == 'C' || $visit['reason_code'] == 'C1' || $visit['reason_code'] == 'C2' || $visit['remark_code'] == 'E' || $visit['reason_code'] == 'E1') { ?>
									<span class="text-muted">No form</span>
								<?php } else { ?>
									-
								<?php } ?>
							</td>
							<td class="rar-c-status"><?php echo $visit['is_completed'] ? '<span class="label label-success">Completed</span>' : '<span class="label label-warning">Open</span>'; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			</div>
			<div class="row" style="margin-top:15px;padding:0 10px;">
				<div class="col-md-6">
					<div class="dataTables_info">
						Showing <?php echo ($get_total_rows > 0) ? ($page_position + 1) : 0; ?>
						to <?php echo min($page_position + $item_per_page, $get_total_rows); ?>
						of <?php echo (int) $get_total_rows; ?> entries
						&nbsp;|&nbsp; Rows Limit:
						<select id="rar_numRecords" class="form-control input-xsmall input-inline">
							<option value="10" <?php echo ($item_per_page == 10) ? 'selected="selected"' : ''; ?>>10</option>
							<option value="25" <?php echo ($item_per_page == 25) ? 'selected="selected"' : ''; ?>>25</option>
							<option value="50" <?php echo ($item_per_page == 50) ? 'selected="selected"' : ''; ?>>50</option>
							<option value="100" <?php echo ($item_per_page == 100) ? 'selected="selected"' : ''; ?>>100</option>
							<option value="200" <?php echo ($item_per_page == 200) ? 'selected="selected"' : ''; ?>>200</option>
							<option value="500" <?php echo ($item_per_page == 500) ? 'selected="selected"' : ''; ?>>500</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="dataTables_paginate paging_simple_numbers" style="text-align:right;">
						<ul class="pagination" style="margin:0;">
							<?php echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); ?>
						</ul>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
