<?php

$page_id = 405;

$page_slug = 'master_activity_dashboard_ajax';

include('connect.php');



if (!armor_is_master_activity_user()) {

	echo '<div class="alert alert-danger">Access denied.</div>';

	exit;

}



$dateFrom = isset($_REQUEST['date']) ? $_REQUEST['date'] : '';

$dateTo = isset($_REQUEST['date1']) ? $_REQUEST['date1'] : '';



$data = armor_master_activity_all_employees($db, $dateFrom, $dateTo);

$employees = $data['employees'];

$totals = $data['totals'];



$periodLabel = 'Current Year';

if ($dateFrom != '' || $dateTo != '') {

	$periodLabel = trim($dateFrom . ' to ' . $dateTo, ' to');

}



$metrics = array('attendance', 'visits', 'followups', 'quotations', 'orders', 'dispatch', 'invoice', 'complain', 'expense', 'leave');

?>

<div class="row" style="margin-bottom:10px;">

	<div class="col-md-12">

		<h4><b>Period:</b> <?php echo htmlspecialchars($periodLabel); ?></h4>

		<small class="text-muted">Click on any number to view full details in popup.</small>

	</div>

</div>

<div class="table-responsive">

<table id="activity_table" class="table table-striped table-bordered table-hover">

	<thead>

		<tr>

			<th>Sr.</th>

			<th>Employee Name</th>

			<th>Attendance</th>

			<th>Visits</th>

			<th>Followups</th>

			<th>Quotation</th>

			<th>Orders</th>

			<th>Dispatch</th>

			<th>Invoice</th>

			<th>Complain</th>

			<th>Expense</th>

			<th>Leave</th>

			<th>Detail</th>

		</tr>

	</thead>

	<tbody>

	<?php

	$sr = 1;

	foreach ($employees as $emp) {

		$c = $emp['counts'];

		$safeName = htmlspecialchars($emp['name'], ENT_QUOTES);

		?>

		<tr>

			<td><?php echo $sr++; ?></td>

			<td><?php echo htmlspecialchars($emp['name']); ?></td>

			<?php foreach ($metrics as $m) { ?>

			<td><?php echo armor_master_activity_count_cell($c[$m], $emp['id'], $m, $emp['name']); ?></td>

			<?php } ?>

			<td>

				<a class="detail-link" onclick="showEmployeeDetail(<?php echo (int) $emp['id']; ?>, '<?php echo $safeName; ?>')">

					<i class="fa fa-eye"></i> View

				</a>

			</td>

		</tr>

		<?php

	}

	if (count($employees) === 0) {

		echo '<tr><td colspan="13" class="text-center">No sales employees found.</td></tr>';

	}

	?>

	</tbody>

	<?php if (count($employees) > 0) { ?>

	<tfoot>

		<tr class="total-row">

			<td></td>

			<td>Total</td>

			<?php foreach ($metrics as $m) { ?>

			<td><?php echo $totals[$m]; ?></td>

			<?php } ?>

			<td></td>

		</tr>

	</tfoot>

	<?php } ?>

</table>

</div>

