<?php
$page_id = 599;
$page_slug = 'visit_report_page';
include("connect.php");
require_once("../include/class.employee_visit_kra_report.php");

function kra_h($value)
{
	return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function kra_money($db, $value)
{
	return CURR . " " . $db->rp_number_format((float) $value, 2);
}

function kra_visit_code($visit)
{
	$code = $visit['normalized_reason_code'] != "" ? $visit['normalized_reason_code'] : $visit['normalized_remark_code'];
	return ($code != "") ? $code : ($visit['is_completed'] ? "Done" : "Open");
}

function kra_format_duration($minutes)
{
	if ($minutes === null || $minutes === "") {
		return "-";
	}
	$minutes = (int) $minutes;
	if ($minutes < 0) {
		return "-";
	}
	$hours = (int) floor($minutes / 60);
	$mins = $minutes % 60;
	if ($hours > 0) {
		return $hours . "h " . $mins . "m";
	}
	return $mins . " min";
}

$report = new EmployeeVisitKraReport($db);
$data = $report->build(
	isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : "",
	isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : "",
	isset($_REQUEST['employee_ids']) ? $_REQUEST['employee_ids'] : "",
	$rights
);
$fixedColCount = 10; // Sr .. Total Visit + Visit Duration
?>
<style>
	.kra-employee { border:1px solid #d9e2ea; margin-bottom:24px; background:#fff; }
	.kra-title { background:#2f6f44; color:#fff; padding:10px 14px; font-size:17px; font-weight:700; }
	.kra-subtitle { font-size:12px; font-weight:400; margin-top:3px; }
	.kra-kpis { display:flex; flex-wrap:wrap; gap:8px; padding:10px; background:#f5f7f9; }
	.kra-kpi { min-width:145px; border:1px solid #dce3e8; background:#fff; padding:8px 10px; }
	.kra-kpi.kra-kpi-km { background:#f4fbf4; border-color:#cfe6cf; }
	.kra-kpi.kra-kpi-ekm { background:#fff8ef; border-color:#f2d1a7; }
	.kra-kpi-label { color:#63717c; font-size:11px; text-transform:uppercase; }
	.kra-kpi-value { font-size:17px; font-weight:700; margin-top:3px; }
	.kra-scroll { overflow:auto; max-height:620px; border-top:1px solid #ddd; }
	.kra-table { border-collapse:separate; border-spacing:0; width:max-content; min-width:100%; margin:0; }
	.kra-table th,.kra-table td {
		border:1px solid #cfd7de; padding:6px 5px; font-size:11px; vertical-align:top;
		background:#fff;
	}
	.kra-table thead th {
		position:sticky; top:0; z-index:5;
		background:#e9782e; color:#fff; text-align:center;
		box-shadow:0 1px 0 #cfd7de;
	}
	/* Freeze master columns (Excel-like) while scrolling date columns */
	.kra-table .kra-f0 { position:sticky; left:0;    z-index:3; min-width:40px;  width:40px; }
	.kra-table .kra-f1 { position:sticky; left:40px;  z-index:3; min-width:85px;  width:85px; }
	.kra-table .kra-f2 { position:sticky; left:125px; z-index:3; min-width:190px; width:190px; }
	.kra-table .kra-f3 { position:sticky; left:315px; z-index:3; min-width:95px;  width:95px; }
	.kra-table .kra-f4 { position:sticky; left:410px; z-index:3; min-width:105px; width:105px; }
	.kra-table .kra-f5 { position:sticky; left:515px; z-index:3; min-width:150px; width:150px; }
	.kra-table .kra-f6 { position:sticky; left:665px; z-index:3; min-width:80px;  width:80px; }
	.kra-table .kra-f7 { position:sticky; left:745px; z-index:3; min-width:70px;  width:70px; }
	.kra-table .kra-f8 { position:sticky; left:815px; z-index:3; min-width:78px;  width:78px; text-align:center; }
	.kra-table .kra-f9 { position:sticky; left:893px; z-index:3; min-width:95px;  width:95px; text-align:center; }
	.kra-table thead [class*="kra-f"] { z-index:6; background:#e9782e; color:#fff; }
	.kra-table thead .kra-f8 { background:#c85a12 !important; }
	.kra-table thead .kra-f9 { background:#a84b0f !important; }
	.kra-table tbody [class*="kra-f"] {
		background:#fff;
		box-shadow:2px 0 3px rgba(0,0,0,0.05);
	}
	.kra-table tbody .kra-f8 { background:#e8f4ff !important; }
	.kra-table tbody .kra-f9 { background:#fff4e8 !important; }
	.kra-table tbody tr.kra-summary [class*="kra-f"] { background:#eef5ef !important; }
	.kra-table .kra-summary td { background:#eef5ef; font-weight:700; text-align:center; }
	.kra-total-num { font-size:16px; font-weight:800; color:#1f4e79; }
	.kra-date { min-width:112px; text-align:center; }
	.kra-code {
		display:inline-block; margin:2px;
		padding:7px 11px; border-radius:4px;
		color:#fff; background:#337ab7;
		font-weight:800; font-size:17px; line-height:1.15;
		min-width:30px; text-align:center;
		letter-spacing:0.5px;
	}
	.kra-code-open { background:#d9534f; }
	.kra-code-sm { font-size:12px; padding:3px 6px; font-weight:700; min-width:auto; }
	.kra-cell-details { min-width:230px; text-align:left; padding:5px; background:#fafafa; border:1px solid #ddd; margin-top:4px; }
	.kra-cell-details div { margin-bottom:3px; }
	.kra-legend { padding:8px 10px; font-size:11px; border-top:1px solid #ddd; }
	.kra-empty { text-align:center; padding:30px; color:#777; }
	.kra-table details summary { list-style:none; cursor:pointer; outline:none; }
	.kra-table details summary::-webkit-details-marker { display:none; }
</style>

<?php if ($data['range']['was_limited']) { ?>
	<div class="alert alert-warning">The selected period was limited to 62 days for report performance.</div>
<?php } ?>

<?php if (!empty($data['require_employee'])) { ?>
	<div class="kra-empty"><h4>Please select at least one Sales Employee and click Show Report.</h4></div>
<?php } else if (empty($data['employees'])) { ?>
	<div class="kra-empty"><h4>No accessible employee found for this filter.</h4></div>
<?php } ?>

<?php foreach ($data['employees'] as $employee) { ?>
	<div class="kra-employee">
		<div class="kra-title">
			KEY RESULT AREA - <?php echo kra_h(strtoupper($employee['name'])); ?>
			<div class="kra-subtitle">
				<?php echo date("d/m/Y", strtotime($data['range']['from'])); ?> to <?php echo date("d/m/Y", strtotime($data['range']['to'])); ?>
				<?php if ($employee['phone'] != "") { ?> | <?php echo kra_h($employee['phone']); ?><?php } ?>
			</div>
		</div>
		<div class="kra-kpis">
			<div class="kra-kpi"><div class="kra-kpi-label">Approved Expense</div><div class="kra-kpi-value"><?php echo kra_money($db, $employee['kpi']['approved_expense']); ?></div></div>
			<div class="kra-kpi kra-kpi-km"><div class="kra-kpi-label">Total KM</div><div class="kra-kpi-value"><?php echo $db->rp_number_format((float) $employee['kpi']['total_kilometer'], 2); ?></div></div>
			<div class="kra-kpi kra-kpi-ekm"><div class="kra-kpi-label">Expense / KM</div><div class="kra-kpi-value"><?php echo kra_money($db, $employee['kpi']['expense_per_km']); ?></div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Salary</div><div class="kra-kpi-value">N/A</div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Expense + Salary</div><div class="kra-kpi-value"><?php echo kra_money($db, $employee['kpi']['approved_expense']); ?> + N/A</div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Total Sales</div><div class="kra-kpi-value"><?php echo kra_money($db, $employee['kpi']['total_sales']); ?></div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Total Visit</div><div class="kra-kpi-value"><?php echo (int) $employee['kpi']['total_visits']; ?></div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Total Duration</div><div class="kra-kpi-value"><?php echo kra_h(kra_format_duration(isset($employee['kpi']['total_duration_minutes']) ? $employee['kpi']['total_duration_minutes'] : 0)); ?></div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Completed / Open</div><div class="kra-kpi-value"><?php echo (int) $employee['kpi']['completed_visits']; ?> / <?php echo (int) $employee['kpi']['open_visits']; ?></div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">KRA Assigned</div><div class="kra-kpi-value"><?php echo (int) (isset($employee['kpi']['kra_assigned']) ? $employee['kpi']['kra_assigned'] : 0); ?></div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Total Quotation</div><div class="kra-kpi-value"><?php echo (int) (isset($employee['kpi']['total_quotations_count']) ? $employee['kpi']['total_quotations_count'] : 0); ?> | <?php echo kra_money($db, $employee['kpi']['total_quotations']); ?></div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Total PI Approved</div><div class="kra-kpi-value"><?php echo (int) (isset($employee['kpi']['approved_pi_count']) ? $employee['kpi']['approved_pi_count'] : 0); ?> | <?php echo kra_money($db, $employee['kpi']['approved_pi']); ?></div></div>
		</div>

		<div class="kra-scroll">
			<table class="kra-table">
				<thead>
				<tr>
					<th class="kra-f0">Sr.</th>
					<th class="kra-f1">Code</th>
					<th class="kra-f2">Account Name</th>
					<th class="kra-f3">Turnover</th>
					<th class="kra-f4">GST No.</th>
					<th class="kra-f5">Address</th>
					<th class="kra-f6">City</th>
					<th class="kra-f7">Pincode</th>
					<th class="kra-f8">Total Visit</th>
					<th class="kra-f9">Visit Duration</th>
					<?php foreach ($data['range']['dates'] as $date) { ?>
						<th class="kra-date"><?php echo date("d/m/Y", strtotime($date)); ?></th>
					<?php } ?>
				</tr>
				</thead>
				<tbody>
				<tr class="kra-summary">
					<td class="kra-f0" colspan="10">Daily Visit Count / Outcome</td>
					<?php foreach ($data['range']['dates'] as $date) {
						$daily = $employee['daily'][$date]; ?>
						<td>
							<?php echo (int) $daily['total']; ?> visit<?php echo $daily['total'] == 1 ? "" : "s"; ?><br>
							<?php foreach ($daily['codes'] as $code => $count) {
								if ($count > 0) { ?><span class="kra-code kra-code-sm"><?php echo kra_h($code); ?>:<?php echo (int) $count; ?></span><?php }
							} ?>
							<?php if ($daily['open'] > 0) { ?><span class="kra-code kra-code-open kra-code-sm">Open:<?php echo (int) $daily['open']; ?></span><?php } ?>
						</td>
					<?php } ?>
				</tr>

				<?php $sr = 0; foreach ($employee['accounts'] as $account) {
					$acctVisits = isset($account['total_visits']) ? (int) $account['total_visits'] : 0;
					$acctDuration = isset($account['total_duration_minutes']) ? (int) $account['total_duration_minutes'] : 0;
					?>
					<tr>
						<td class="kra-f0"><?php echo ++$sr; ?></td>
						<td class="kra-f1"><?php echo kra_h($account['code']); ?></td>
						<td class="kra-f2"><b><?php echo kra_h($account['company']); ?></b><br><small><?php echo kra_h($account['person']); ?> | <?php echo kra_h($account['type']); ?></small></td>
						<td class="kra-f3"><?php echo kra_h($account['turnover']); ?></td>
						<td class="kra-f4"><?php echo kra_h($account['gst']); ?></td>
						<td class="kra-f5"><?php echo kra_h($account['address']); ?></td>
						<td class="kra-f6"><?php echo kra_h($account['city']); ?></td>
						<td class="kra-f7"><?php echo kra_h($account['pincode']); ?></td>
						<td class="kra-f8"><span class="kra-total-num"><?php echo $acctVisits; ?></span></td>
						<td class="kra-f9"><span class="kra-total-num" style="font-size:13px;"><?php echo kra_h(kra_format_duration($acctDuration)); ?></span></td>
						<?php foreach ($data['range']['dates'] as $date) { ?>
							<td class="kra-date">
								<?php if (!empty($account['dates'][$date])) {
									foreach ($account['dates'][$date] as $visit) { ?>
										<details>
											<summary><span class="kra-code<?php echo $visit['is_completed'] ? "" : " kra-code-open"; ?>"><?php echo kra_h(kra_visit_code($visit)); ?></span></summary>
											<div class="kra-cell-details">
												<div><b>Visit #:</b> <?php echo (int) $visit['id']; ?></div>
												<div><b>Purpose:</b> <?php echo kra_h($visit['purpose_name']); ?></div>
												<div><b>Start:</b> <?php echo kra_h($visit['start_date_time']); ?></div>
												<div><b>Stop:</b> <?php echo $visit['is_completed'] ? kra_h($visit['stop_date_time']) : "Open"; ?></div>
												<div><b>Duration:</b> <?php echo $visit['duration_minutes'] === null ? "N/A" : kra_h(kra_format_duration($visit['duration_minutes'])); ?></div>
												<div><b>Outcome:</b> <?php echo kra_h($visit['remark_label']); ?><?php echo $visit['reason_label'] != "" ? " - " . kra_h($visit['reason_label']) : ""; ?></div>
												<div><b>Remark:</b> <?php echo kra_h($visit['stop_remark']); ?></div>
												<div><b>Purchasing From:</b> <?php echo kra_h($visit['product_name']); ?></div>
												<div><b>Contact:</b> <?php echo kra_h($visit['name']); ?> <?php echo kra_h($visit['mobile_no']); ?></div>
												<?php
												if (!empty($visit['consultant_form']) && is_array($visit['consultant_form'])) {
													$vf = $visit['consultant_form'];
													$typeLabel = (isset($vf['consultant_type']) && $vf['consultant_type'] == "government") ? "Government Consultant" : "Private Consultant";
													?>
													<div style="margin-top:6px;padding:6px;border:1px solid #ddd;background:#f9f9f9;font-size:11px;line-height:1.45;">
														<b>Need Approval / Consultant (<?php echo kra_h($typeLabel); ?>)</b><br>
														<b>Firm:</b> <?php echo kra_h($vf['firm_name']); ?><br>
														<b>Address:</b> <?php echo kra_h($vf['address']); ?><br>
														<b>City:</b> <?php echo kra_h($vf['city']); ?>
														&nbsp; <b>State:</b> <?php echo kra_h($vf['state']); ?>
														&nbsp; <b>Pincode:</b> <?php echo kra_h($vf['pincode']); ?><br>
														<b>Contact:</b> <?php echo kra_h($vf['contact_person']); ?>
														&nbsp; <b>Mo:</b> <?php echo kra_h($vf['mobile']); ?>
														<?php if (!empty($vf['email'])) { ?> &nbsp; <b>Mail:</b> <?php echo kra_h($vf['email']); ?><?php } ?>
													</div>
												<?php }
												if (!empty($visit['high_rate_form']) && is_array($visit['high_rate_form'])) {
													$hf = $visit['high_rate_form'];
													$payLabel = isset($hf['payment_option']) ? $hf['payment_option'] : '';
													if ($payLabel === '0' || $payLabel === 0) {
														$payLabel = 'Advance';
													} else if ($payLabel === '1' || $payLabel === 1) {
														$payLabel = '30 Days';
													}
													?>
													<div style="margin-top:6px;padding:6px;border:1px solid #ddd;background:#fff8e6;font-size:11px;line-height:1.45;">
														<b>High Rate Analysis</b><br>
														<b>Customer:</b> <?php echo kra_h($hf['customer_name']); ?>
														<?php if ($payLabel !== '') { ?> &nbsp; <b>Payment:</b> <?php echo kra_h($payLabel); ?><?php } ?>
														<?php if (!empty($hf['payment_remark'])) { ?><br><b>Payment Remark:</b> <?php echo kra_h($hf['payment_remark']); ?><?php } ?>
														<?php if (!empty($visit['high_rate_items'])) { ?>
															<table border="1" cellpadding="2" cellspacing="0" style="width:100%;margin-top:4px;font-size:10px;">
																<tr style="background:#f0f0f0;"><th>Product</th><th>Given</th><th>Qty</th><th>Cust. Rate</th><th>Remark</th></tr>
																<?php foreach ($visit['high_rate_items'] as $hi) { ?>
																	<tr>
																		<td><?php echo kra_h($hi['product_name']); ?></td>
																		<td><?php echo kra_h($hi['given_rate']); ?></td>
																		<td><?php echo kra_h($hi['qty']); ?></td>
																		<td><?php echo kra_h($hi['customer_rate']); ?></td>
																		<td><?php echo kra_h(isset($hi['remark']) ? $hi['remark'] : ''); ?></td>
																	</tr>
																<?php } ?>
															</table>
														<?php } ?>
													</div>
												<?php } ?>
											</div>
										</details>
									<?php }
								} ?>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>
				<?php if (empty($employee['accounts'])) { ?>
					<tr><td colspan="<?php echo $fixedColCount + count($data['range']['dates']); ?>" class="kra-empty">No visits in this period.</td></tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="kra-legend">
			<b>Visit Code Chart:</b>
			<?php foreach ($data['remark_labels'] as $code => $label) { ?>
				<span class="kra-code kra-code-sm"><?php echo kra_h($code); ?></span> <?php echo kra_h($label); ?>&nbsp;&nbsp;
			<?php } ?>
		</div>
	</div>
<?php } ?>
<?php require_once("disconnect.php"); ?>
