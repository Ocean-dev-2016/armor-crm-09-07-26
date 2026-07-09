<?php
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	//$pending_quotationR = $db->rp_getData("quotation_detail","*","isDelete=0 AND status=0","id DESC");
	$pending_OrderR = $db->rp_getData("orders","*","isDelete=0 AND status=0","id DESC");
	$salse_executive_list_results = $db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1 ","id DESC",0);
	
	?>
		<!-- salse executive list -->
		<div class="col-md-12 col-sm-12 co-xs-12 col-lg-12">
			<div class="portlet light ">
				<div class="portlet-title">
					<div class="caption ">
						<span class="caption-subject bold uppercase font-dark">Sales Officer Vs Customer Count List</span>
					</div>
				</div>
				<div class="portlet-body">
					<div class="row">
						<table class="table table-hover table-light">
							<thead>
								<tr class="uppercase">
									<th >Sr no.</th>
									<th colspan="2">Sales Officer</th>
									<th colspan="2">Customer Count</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$se_count = 0;
								while($salse_executive_list_data=mysqli_fetch_assoc($salse_executive_list_results))
								{
									
									/*get sales class area*/
									$class_id = array();
									$area_id = array();
									$sales_class_id = array();
									$sales_area_id = array();
									$sales_area_r=$db->rp_getData("sales_executive_map_area","*","sales_executive_id='".$salse_executive_list_data['id']."' AND isDelete=0","",0);
									while($sales_area_d=mysqli_fetch_assoc($sales_area_r))
									{
										$sales_class_id[] = $sales_area_d['class_id'];
										$sales_area_id[]  = $sales_area_d['area_id'];
									}
									$class_id=implode(",",$sales_class_id);
									$area_id=implode(",",$sales_area_id);
									/*get sales class area*/
									
									/*get customer class area*/
									$exeids = array();
									$executive_id = array();
									$customer_area_r=$db->rp_getData("executive_map_area","*","class_id IN (".$class_id.") AND area_id IN (".$area_id.") AND isDelete=0","",0);
									while($customer_area_d = mysqli_fetch_assoc($customer_area_r))
									{
										$executive_id[] = $customer_area_d['executive_id'];
									}
									$exeids=implode(",",$executive_id);
									/*get customer class area*/

									/*get customer count*/
									$customer_count = 0;
									$customer_count = $db->rp_getValue("executive","Count(*)","id IN (".$exeids.") AND isDelete=0 AND isActive=1",0); 
									if($customer_count=="")
									{
										$customer_count = 0;
									}
									/*get customer count*/

									$se_count++;
									?>
										<tr>
											<td><?= $se_count ?></td>
											<td colspan="2"><?= $salse_executive_list_data['name'] ?></td>
											<td colspan="2"><?= $customer_count ?></td>
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
		<!-- salse executive list -->
	<?php
}
?>