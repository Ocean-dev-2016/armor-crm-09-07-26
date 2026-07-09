<?php 
$page_id=400;$page_slug='dashboard';
include("connect.php");

$inquiry_where="isDelete=0";

// if($_REQUEST['inquiry_year'] != "")
// {
// 	$inquiry_where.="  AND  Year(created_date)='".$_REQUEST['inquiry_year']."'";
// }

// if($_REQUEST['inquiry_month'] != "")
// {
// 	$inquiry_where.="  AND  MONTH(inquiry_date)='".$_REQUEST['inquiry_month']."'";
// }

// for link 


//$direct_inquiry = $db->rp_getTotalRecord("no_order_inquiry"," inq_status = '0' AND inquiry_lead_flag='0' AND ".$inquiry_where,0);

$direct_inquiry = $db->rp_getTotalRecord("no_order_inquiry","inquiry_lead_flag='0' AND inq_status!=2 AND ".$inquiry_where,0);

$prospect_to_inquiry  = $db->rp_getTotalRecord("no_order_inquiry","inq_status = 2 AND ".$inquiry_where,0);

$inquiry_to_lead  = $db->rp_getTotalRecord("no_order_inquiry","inquiry_lead_flag = '1' AND ".$inquiry_where,0);

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	$dashboard_main_array_deep_freezer = array(
		
		//0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where."  AND (inquiry_lead_flag= '1' or inquiry_lead_flag = '0')",0),"Total Inquiry",$inquiry_link."&type=0",593,"="),

		0=>array("black",$db->rp_getTotalRecord("freezer_scheme",$inquiry_where,0),"Total Freeze Booked",$inquiry_link."&type=0",593," "),

		1=>array("#7bd0a9",$direct_inquiry,"Center","#",572," "),

		2=>array("#7bd0a9",$prospect_to_inquiry,"Distributer","#",572," "),

		// 3=>array("#7bd0a9",$inquiry_to_lead,"Inquiry To Lead","#",572),

		// 4=>array("#7bd0a9",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=0 AND (inquiry_lead_flag = '0')",0),"Pending Inquiry",$inquiry_link."&type=0&status=0",572),

		// 5=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=1 AND (inquiry_lead_flag = '0')",0),"In followup Inquiry",$inquiry_link."&type=0&status=1",572),

		//6=>array("#ff6347",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=4 AND (inquiry_lead_flag = '0')",0),"Hot In followup Inquiry",$inquiry_link."&type=0&status=4",572),

		//7=>array("#7cfc00",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=5 AND (inquiry_lead_flag = '0')",0),"Cold In followup Inquiry",$inquiry_link."&type=0&status=5",572),

		//8=>array("#fada5e",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=6 AND (inquiry_lead_flag = '0')",0),"Warm In followup Inquiry",$inquiry_link."&type=0&status=6",572),

		// 9=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=-2 AND (inquiry_lead_flag = '0')",0),"Non Relavent Lost Inquiry",$inquiry_link."&type=0&status=-2",583),
		
		// 10=>array("#ec9b97",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=-1 AND (inquiry_lead_flag = '0')",0),"Not Interested Lost Inquiry",$inquiry_link."&type=0&status=-1",583),

		// 11=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=3 AND (inquiry_lead_flag = '0')",0),"Buy Later Lost Inquiry",$inquiry_link."&type=0&status=3",583),

		// 12=>array("#ffa07a",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=11 AND (inquiry_lead_flag = '0')",0),"Lost Inquiry",$inquiry_link."&type=0&status=11",583),
	);
}
else
{
	$inquiry_where_sales=" AND ( inquiry_created_by = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."') ";

	$direct_inquiry_user = ($db->rp_getTotalRecord("no_order_inquiry","isDelete = 0 AND inquiry_lead_flag='0' AND inq_status!=2 AND ".$inquiry_where.$inquiry_where_sales,0));

	$prospect_to_inquiry_user  = $db->rp_getTotalRecord("no_order_inquiry","isDelete = 0 AND (inq_status = 2) AND ".$inquiry_where.$inquiry_where_sales,0);

	$inquiry_to_lead_user  = $db->rp_getTotalRecord("no_order_inquiry","isDelete = 0 AND inquiry_lead_flag = '1' AND ".$inquiry_where.$inquiry_where_sales,0);

	$dashboard_main_array_deep_freezer = array(
		
		0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales."  AND (inquiry_lead_flag!='-1')",0),"Total Inquiry",$inquiry_link."&type=0",593,"="),

		1=>array("#7bd0a9",$direct_inquiry_user,"Direct Inquiry","#",572,"+"),

		2=>array("#7bd0a9",$prospect_to_inquiry_user,"Prospect To Inquiry","#",572,"+"),

		3=>array("#7bd0a9",$inquiry_to_lead_user,"Inquiry To Lead","#",572),

		4=>array("#7bd0a9",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=0 AND (inquiry_lead_flag = '0')",0),"Pending Inquiry",$inquiry_link."&type=0&status=0",572),

		5=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=1 AND (inquiry_lead_flag = '0')",0),"In followup Inquiry",$inquiry_link."&type=0&status=1",572),

		//6=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=4 AND (inquiry_lead_flag = '0')",0),"Hot In followup Inquiry",$inquiry_link."&type=0&status=4",583),

		//7=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=5 AND (inquiry_lead_flag = '0')",0),"Cold In followup Inquiry",$inquiry_link."&type=0&status=5",583),

		//8=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=6 AND (inquiry_lead_flag = '0')",0),"Warm In followup Inquiry",$inquiry_link."&type=0&status=6",583),

		9=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=-2 AND (inquiry_lead_flag = '0')",0),"Non Relavent Lost Inquiry",$inquiry_link."&type=0&status=-2",583),
		
		10=>array("#ec9b97",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=-1 AND (inquiry_lead_flag = '0')",0),"Not Interested Lost Inquiry",$inquiry_link."&type=0&status=-1",583),

		11=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=3 AND (inquiry_lead_flag = '0')",0),"Buy Later Lost Inquiry",$inquiry_link."&type=0&status=3",583),
		
		12=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=11 AND (inquiry_lead_flag = '0')",0),"Lost Inquiry",$inquiry_link."&type=0&status=11",583),
	);
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
// { 
	
	?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> Deep Freezer Scheme Statistic</span>
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getdeepfreezer();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row"> 
						<div class="col-sm-3">
							<select class="form-control" name="center" id="center" value="<?php echo $sales_id;?>" >
								<option value="">Select Center</option>
								<?php 
								$sales_r=$db->rp_getData('sales_executive',"*","isDelete=0 AND isActive=1","name ASC",0);
								while($sales_d=mysqli_fetch_assoc($sales_r))
								{
									?>
									<option <?php if($sales_d['id']==$_REQUEST['center']){?> selected <?php } ?>  value="<?php echo $sales_d['id']?>"><?php echo $sales_d['name']?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="distributer" id="distributer" value="<?php echo $sales_id;?>" >
								<option value="">Select Distributor</option>
								<?php 
								$sales_r=$db->rp_getData('sales_executive',"*","isDelete=0 AND isActive=1","name ASC",0);
								while($sales_d=mysqli_fetch_assoc($sales_r))
								{
									?>
									<option <?php if($sales_d['id']==$_REQUEST['distributer']){?> selected <?php } ?>  value="<?php echo $sales_d['id']?>"><?php echo $sales_d['name']?></option>
									<?php
								}
								?>
							</select>
						</div>
						<br>
						<br>
					</div>
					<br>
					<div class="row horizontal-scrollable" style=" overflow: auto;white-space:nowrap;">
						<?php
						foreach($dashboard_main_array_deep_freezer as $arr_deep_freezer)
						{
							?>
							<div class="" style="padding: 10px;display: inline-block;vertical-align:top;/*margin-right:20px;*/white-space:normal;">
								<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_deep_freezer[0] ?> solid; width: 120px; height: 100px;">
									<a href="<?php echo $arr_deep_freezer[3]; ?>" style="text-decoration: none;">        
									<div class="desc" style="text-align: center;">
									   <div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
						                    <span data-counter="counterup" data-value="<?php echo $arr_deep_freezer[1]; ?>"> <?php echo $arr_deep_freezer[1]; ?> </span>
						               	</div>
						                <strong><?php echo $arr_deep_freezer[2]; ?></strong>
									</div>
									</a>
								</div>
							</div>
							<div style="display: inline-block; padding-top: 50px;"><strong style="font-size: 25px;"><?= $arr_deep_freezer[5] ?></strong></div>
							<?php
						}	
						?>
					</div>
					<!-- <div class="col-md-12 col-sm-12 co-xs-6 col-lg-12">
						<div class="portlet-body ">
							<div class="portlet-title">
								<div class="caption ">
									<br><br>
									<span class="caption-subject bold uppercase font-dark">Deep Freezer Scheme Chart</span> -->
									<!-- <span class="caption-helper">monthly order stats...</span> -->
								<!-- </div>
							</div>
							<div id="deep_freezer" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div> -->
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$("#center").select2();
		$("#distributer").select2();
		// jQuery(document).ready(function() {
		//     // Graph_inquiry.init_inquiry();
		//     graph_inquiry_pie.init_inquiry_pie();
		// });
	</script>
	<?php require_once 'disconnect.php';  ?>