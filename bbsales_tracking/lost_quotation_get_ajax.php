<?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
include("connect.php");
?>
		<div class="portlet-body" style="margin-top: 20px;">
			<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
				<thead>
					<tr>
						<th>Quotation No</th>
						<th>Quotation Date</th>
						<th>Company Name</th>
						<th>Person Name</th>
						<th>Company Mobile No</th>
						<th>Quotation Amount</th>
						<th>Reason For Lost</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$quotation_r=$db->rp_getData("quotation_detail","*","isDelete=0 AND sales_id='".$_REQUEST['sales_id']."' AND status=5","",0);
					while($quotation_d=mysqli_fetch_assoc($quotation_r))
					{
						?>
					<tr>
						<td><?php echo stripslashes($quotation_d['quotation_no']); ?></td>
						<td><?php echo date('d-m-Y', strtotime($quotation_d['quotation_date'])); ?></td>
						<td><?php echo $quotation_d['company_name']; ?></td>
						<td><?php echo stripslashes($quotation_d['customer_name']); ?></td>
						<td><?php echo stripslashes($quotation_d['contact_number']); ?></td>
						<td align="right"><?php echo stripslashes(CURR . $db->rp_num(round($quotation_d['grand_total']))); ?></td>
						<td><?php echo $quotation_d['lost_reason']; ?></td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
<?php require_once "disconnect.php"; ?>	