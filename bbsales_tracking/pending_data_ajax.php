<?php
if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 0) {
	$pending_quotationR = $db->rp_getData("quotation_detail", "*", "isDelete=0 AND status=0", "id DESC", 0, 30);
	$pending_OrderR = $db->rp_getData("orders", "*", "isDelete=0 AND status=0", "id DESC", 0, 30);
	$total_pending_quotation = $db->rp_getTotalRecord("quotation_detail", "isDelete=0 AND status=0", 0);
	$total_pending_order = $db->rp_getTotalRecord("orders", "isDelete=0 AND status=0", 0);
	?>
	<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
		<div class="portlet light bordered admin-dash-panel">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-file-text-o"></i>
					<span class="caption-subject bold uppercase">Pending Quotation List</span>
					<span class="badge"><?php echo (int) $total_pending_quotation; ?></span>
				</div>
				<div class="actions">
					<a href="quotation_manage.php" class="btn btn-circle btn-sm">
						<i class="fa fa-list"></i> View All
					</a>
				</div>
			</div>
			<div class="portlet-body" style="padding:0;">
				<div class="dash-table-wrap">
					<table class="table table-striped table-bordered table-hover dash-table">
						<thead>
							<tr>
								<th>Quotation No.</th>
								<th>Quotation Date</th>
								<th>Company Name</th>
								<th>Person Name</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if ($pending_quotationR && mysqli_num_rows($pending_quotationR) > 0) {
								while ($pending_quotationD = mysqli_fetch_assoc($pending_quotationR)) {
									?>
									<tr>
										<td><?php echo stripslashes($pending_quotationD['quotation_no']); ?></td>
										<td><?php echo date('d-m-Y', strtotime($pending_quotationD['quotation_date'])); ?></td>
										<td><strong><?php echo stripslashes($pending_quotationD['company_name']); ?></strong></td>
										<td><?php echo stripslashes($pending_quotationD['customer_name']); ?></td>
										<td><span class="label-pending">Pending</span></td>
									</tr>
									<?php
								}
							} else {
								?>
								<tr>
									<td colspan="5" class="no-records">No pending quotations found.</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
		<div class="portlet light bordered admin-dash-panel">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-shopping-cart"></i>
					<span class="caption-subject bold uppercase">Pending Order List</span>
					<span class="badge"><?php echo (int) $total_pending_order; ?></span>
				</div>
				<div class="actions">
					<a href="dealer_orders_manage.php?status=0" class="btn btn-circle btn-sm">
						<i class="fa fa-list"></i> View All
					</a>
				</div>
			</div>
			<div class="portlet-body" style="padding:0;">
				<div class="dash-table-wrap">
					<table class="table table-striped table-bordered table-hover dash-table">
						<thead>
							<tr>
								<th>Order No.</th>
								<th>Order Date</th>
								<th>Company Name</th>
								<th>Person Name</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if ($pending_OrderR && mysqli_num_rows($pending_OrderR) > 0) {
								while ($pending_OrderD = mysqli_fetch_assoc($pending_OrderR)) {
									?>
									<tr>
										<td><?php echo stripslashes($pending_OrderD['order_no']); ?></td>
										<td><?php echo date('d-m-Y', strtotime($pending_OrderD['order_date'])); ?></td>
										<td><strong><?php echo stripslashes($pending_OrderD['company_name']); ?></strong></td>
										<td><?php echo stripslashes($pending_OrderD['customer_name']); ?></td>
										<td><span class="label-pending">Pending</span></td>
									</tr>
									<?php
								}
							} else {
								?>
								<tr>
									<td colspan="5" class="no-records">No pending orders found.</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php
}
?>
<?php require_once 'disconnect.php'; ?>
