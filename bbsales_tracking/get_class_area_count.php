<?php
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	$customer_list = $db->rp_getData("executive","id,cname,company_name,type_of_executive","customer_flag=0 AND isDelete=0 AND isActive=1","id DESC",0);
	?>
	<!-- salse executive list -->
		<div class="col-md-12 col-sm-12 co-xs-12 col-lg-12">
			<div class="portlet light ">
				<div class="portlet-title">
					<div class="caption ">
						<span class="caption-subject bold uppercase font-dark">Sales Officer Allocation Pending Report</span>
					</div>
				</div>
				<div class="portlet-body">
					<div class="row">
						<table class="table table-hover table-light">
							<thead>
								<tr class="uppercase">
									<th>count</th>
									<th colspan="2">Customer</th>
									<th colspan="2">state</th>
									<th colspan="2">City</th>
									<th colspan="2">Customer Type</th>
									<th colspan="2">Details</th>
									<!-- <th colspan="2">without Class Area</th>
									<th colspan="2">allocation Pending</th> -->
								</tr>
							</thead>
							<tbody>
								<?php
									$se_count = 0;
									$customer_area_r = $db->rp_getData("executive_map_area","*","isDelete=0 AND class_id!='0' AND area_id!='0' GROUP BY executive_id","class_id",0);

									$get_Sales_executive_count = 0;
									while($customer_area_d = mysqli_fetch_assoc($customer_area_r))
									{
										$class_id = $customer_area_d['class_id'];
										$area_id = $customer_area_d['area_id'];
										$executive_id = $customer_area_d['executive_id'];

										$get_Sales_executive_count = $db->rp_getTotalRecord("sales_executive_map_area","class_id = '".$class_id."' AND area_id = '".$area_id."' AND area_id!='0' AND class_id!='0' AND isDelete=0",0);

										$company_name = $db->rp_getValue("executive","company_name","id='".$executive_id."' AND isDelete=0");
										$state = $db->rp_getValue("executive","state","id='".$executive_id."' AND isDelete=0");
										$city = $db->rp_getValue("executive","city","id='".$executive_id."' AND isDelete=0");
										$company_name = $db->rp_getValue("executive","company_name","id='".$executive_id."' AND isDelete=0");
										$type_of_executive = $db->rp_getValue("executive","type_of_executive","id='".$executive_id."' AND isDelete=0");


										$type_of_executive_array = array("1"=>"Super Stokiest","2"=>"Distributor","3"=>"Dealer","4"=>"B2B Customer","6"=>"B2C Customer");

										if($get_Sales_executive_count<=0)
										{
											$se_count++;
											?>
											<tr>
												<td><?= $se_count ?></td>
												<td colspan="2"><?= $company_name ?>--<?=$class_id;?></td>
												<td colspan="2"><?= $state ?>--<?=$class_id;?></td>
												<td colspan="2"><?= $city ?>--<?=$area_id;?></td>
												<td colspan="2"><?= $type_of_executive_array[$type_of_executive] ?></td>
												<td colspan="2">Executive Allocation Pending</td>
											</tr>
											<?php
										}
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