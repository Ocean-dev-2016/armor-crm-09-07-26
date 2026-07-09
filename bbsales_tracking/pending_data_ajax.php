<?php
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	$pending_quotationR = $db->rp_getData("quotation_detail","*","isDelete=0 AND status=0","id DESC");
	$pending_OrderR = $db->rp_getData("orders","*","isDelete=0 AND status=0","id DESC");
	$salse_executive_list_results = $db->rp_getData("sales_executive","*","isDelete=0 ","id DESC");
	
	?>
		<div class="col-md-6 col-sm-6 co-xs-6 col-lg-6">
			<div class="portlet light ">
				<div class="portlet-title">
					<div class="caption ">
						<span class="caption-subject bold uppercase font-dark">Pending Quotation List</span>
						<!-- <span class="caption-helper">monthly order stats...</span> -->
					</div>
				</div>
				<div class="portlet-body">
					<div class="row">
						<table class="table table-hover table-light">
							<thead>
								<tr class="uppercase">
									<th colspan="2">Quotation No.</th>
									<th colspan="2">Quotation Date</th>
									<th colspan="2">Company Name</th>
									<th colspan="2">Person Name</th>
									<th colspan="2">Status</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while($pending_quotationD=mysqli_fetch_assoc($pending_quotationR))
								{
									?>
										<tr>
											<td colspan="2"><?= $pending_quotationD['quotation_no'] ?></td>
											<td colspan="2"><?= date('d-m-Y',strtotime($pending_quotationD['quotation_date'])) ?></td>
											<td colspan="2"><?= $pending_quotationD['company_name'] ?></td>
											<td colspan="2"><?= $pending_quotationD['customer_name'] ?></td>
											<td colspan="2">Pending</td>
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
		<div class="col-md-6 col-sm-6 co-xs-6 col-lg-6">
			<div class="portlet light ">
				<div class="portlet-title">
					<div class="caption ">
						<span class="caption-subject bold uppercase font-dark">Pending Order List</span>
						<!-- <span class="caption-helper">monthly order stats...</span> -->
					</div>
				</div>
				<div class="portlet-body">
					<div class="row">
						<table class="table table-hover table-light">
							<thead>
								<tr class="uppercase">
									<th colspan="2">Order No.</th>
									<th colspan="2">Order Date</th>
									<th colspan="2">Company Name</th>
									<th colspan="2">Person Name</th>
									<th colspan="2">Status</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while($pending_OrderD = mysqli_fetch_assoc($pending_OrderR))
								{
									?>
										<tr>
											<td colspan="2"><?= $pending_OrderD['order_no'] ?></td>
											<td colspan="2"><?= date('d-m-Y',strtotime($pending_OrderD['order_date'])) ?></td>
											<td colspan="2"><?= $pending_OrderD['company_name'] ?></td>
											<td colspan="2"><?= $pending_OrderD['customer_name'] ?></td>
											<td colspan="2">Pending</td>
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
<?php require_once 'disconnect.php';  ?>