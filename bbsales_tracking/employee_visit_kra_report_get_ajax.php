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

$report = new EmployeeVisitKraReport($db);
$data = $report->build(
	isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : "",
	isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : "",
	isset($_REQUEST['employee_ids']) ? $_REQUEST['employee_ids'] : "",
	$rights
);
?>
<style>
	.kra-employee { border:1px solid #d9e2ea; margin-bottom:24px; background:#fff; }
	.kra-title { background:#2f6f44; color:#fff; padding:10px 14px; font-size:17px; font-weight:700; }
	.kra-subtitle { font-size:12px; font-weight:400; margin-top:3px; }
	.kra-kpis { display:flex; flex-wrap:wrap; gap:8px; padding:10px; background:#f5f7f9; }
	.kra-kpi { min-width:145px; border:1px solid #dce3e8; background:#fff; padding:8px 10px; }
	.kra-kpi-label { color:#63717c; font-size:11px; text-transform:uppercase; }
	.kra-kpi-value { font-size:17px; font-weight:700; margin-top:3px; }
	.kra-scroll { overflow:auto; max-height:620px; border-top:1px solid #ddd; }
	.kra-table { border-collapse:collapse; width:max-content; min-width:100%; margin:0; }
	.kra-table th,.kra-table td { border:1px solid #cfd7de; padding:5px; font-size:11px; vertical-align:top; }
	.kra-table thead th { position:sticky; top:0; z-index:3; background:#e9782e; color:#fff; text-align:center; }
	.kra-table .kra-fixed { position:sticky; left:0; z-index:2; background:#fff; }
	.kra-table thead .kra-fixed { z-index:4; background:#e9782e; }
	.kra-table .kra-summary td { background:#eef5ef; font-weight:700; text-align:center; }
	.kra-date { min-width:92px; text-align:center; }
	.kra-account { min-width:160px; max-width:220px; }
	.kra-small-col { min-width:80px; max-width:135px; }
	.kra-code { display:inline-block; margin:1px; padding:2px 5px; border-radius:3px; color:#fff; background:#337ab7; font-weight:700; }
	.kra-code-open { background:#d9534f; }
	.kra-cell-details { min-width:230px; text-align:left; padding:5px; background:#fafafa; border:1px solid #ddd; margin-top:4px; }
	.kra-cell-details div { margin-bottom:3px; }
	.kra-legend { padding:8px 10px; font-size:11px; border-top:1px solid #ddd; }
	.kra-empty { text-align:center; padding:30px; color:#777; }
</style>

<?php if ($data['range']['was_limited']) { ?>
	<div class="alert alert-warning">The selected period was limited to 62 days for report performance.</div>
<?php } ?>

<?php if (empty($data['employees'])) { ?>
	<div class="kra-empty"><h4>No accessible employee or visit data found for this filter.</h4></div>
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
			<div class="kra-kpi"><div class="kra-kpi-label">Salary</div><div class="kra-kpi-value">N/A</div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Expense + Salary</div><div class="kra-kpi-value"><?php echo kra_money($db, $employee['kpi']['approved_expense']); ?> + N/A</div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Total Sales</div><div class="kra-kpi-value"><?php echo kra_money($db, $employee['kpi']['total_sales']); ?></div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Total Visit</div><div class="kra-kpi-value"><?php echo (int) $employee['kpi']['total_visits']; ?></div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Completed / Open</div><div class="kra-kpi-value"><?php echo (int) $employee['kpi']['completed_visits']; ?> / <?php echo (int) $employee['kpi']['open_visits']; ?></div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Total Quotation</div><div class="kra-kpi-value"><?php echo (int) $employee['kpi']['total_quotations']; ?></div></div>
			<div class="kra-kpi"><div class="kra-kpi-label">Total PI Approved</div><div class="kra-kpi-value"><?php echo (int) $employee['kpi']['approved_pi']; ?></div></div>
		</div>

		<div class="kra-scroll">
			<table class="kra-table">
				<thead>
				<tr>
					<th class="kra-fixed">Sr.</th>
					<th>Code</th>
					<th class="kra-account">Account Name</th>
					<th class="kra-small-col">Turnover</th>
					<th class="kra-small-col">GST No.</th>
					<th class="kra-account">Address</th>
					<th class="kra-small-col">City</th>
					<th class="kra-small-col">Pincode</th>
					<?php foreach ($data['range']['dates'] as $date) { ?>
						<th class="kra-date"><?php echo date("d/m/Y", strtotime($date)); ?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<tr class="kra-summary">
					<td class="kra-fixed" colspan="8">Daily Visit Count / Outcome</td>
					<?php foreach ($data['range']['dates'] as $date) {
						$daily = $employee['daily'][$date]; ?>
						<td>
							<?php echo (int) $daily['total']; ?> visit<?php echo $daily['total'] == 1 ? "" : "s"; ?><br>
							<?php foreach ($daily['codes'] as $code => $count) {
								if ($count > 0) { ?><span class="kra-code"><?php echo kra_h($code); ?>:<?php echo (int) $count; ?></span><?php }
							} ?>
							<?php if ($daily['open'] > 0) { ?><span class="kra-code kra-code-open">Open:<?php echo (int) $daily['open']; ?></span><?php } ?>
						</td>
					<?php } ?>
				</tr>

				<?php $sr = 0; foreach ($employee['accounts'] as $account) { ?>
					<tr>
						<td class="kra-fixed"><?php echo ++$sr; ?></td>
						<td><?php echo kra_h($account['code']); ?></td>
						<td class="kra-account"><b><?php echo kra_h($account['company']); ?></b><br><small><?php echo kra_h($account['person']); ?> | <?php echo kra_h($account['type']); ?></small></td>
						<td><?php echo kra_h($account['turnover']); ?></td>
						<td><?php echo kra_h($account['gst']); ?></td>
						<td class="kra-account"><?php echo kra_h($account['address']); ?></td>
						<td><?php echo kra_h($account['city']); ?></td>
						<td><?php echo kra_h($account['pincode']); ?></td>
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
												<div><b>Duration:</b> <?php echo $visit['duration_minutes'] === null ? "N/A" : (int) $visit['duration_minutes'] . " min"; ?></div>
												<div><b>Outcome:</b> <?php echo kra_h($visit['remark_label']); ?><?php echo $visit['reason_label'] != "" ? " - " . kra_h($visit['reason_label']) : ""; ?></div>
												<div><b>Remark:</b> <?php echo kra_h($visit['stop_remark']); ?></div>
												<div><b>Purchasing From:</b> <?php echo kra_h($visit['product_name']); ?></div>
												<div><b>Contact:</b> <?php echo kra_h($visit['name']); ?> <?php echo kra_h($visit['mobile_no']); ?></div>
											</div>
										</details>
									<?php }
								} ?>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>
				<?php if (empty($employee['accounts'])) { ?>
					<tr><td colspan="<?php echo 8 + count($data['range']['dates']); ?>" class="kra-empty">No visits in this period.</td></tr>
				<?php } ?>
			</tbody>
		</table>
		</div>
		<div class="kra-legend">
			<b>Visit Code Chart:</b>
			<?php foreach ($data['remark_labels'] as $code => $label) { ?>
				<span class="kra-code"><?php echo kra_h($code); ?></span> <?php echo kra_h($label); ?>&nbsp;&nbsp;
			<?php } ?>
		</div>
	</div>
<?php } ?>
<?php require_once("disconnect.php"); ?>
