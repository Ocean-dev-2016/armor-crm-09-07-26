<?php 
$page_id= 572;$page_slug= 'no_order_inquiry';
include("connect.php");
//print_r($_REQUEST);exit;
$inquiry_where="";
// if($_REQUEST['inquiry_year']=="" && $_REQUEST['inquiry_year']=="undefined")
// {
// 	$_REQUEST['inquiry_year']=date("Y");
// }
$inquiry_where="isDelete=0";

if($_REQUEST['inquiry_year']!= "" && $_REQUEST['inquiry_year']!="undefined")
{
	$inquiry_where.="  AND  Year(inquiry_date)='".$_REQUEST['inquiry_year']."'";
}

if($_REQUEST['inquiry_month']!= "" && $_REQUEST['inquiry_month']!="undefined")
{
	$inquiry_where.="  AND  MONTH(inquiry_date)='".$_REQUEST['inquiry_month']."'";
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	if($_REQUEST['inquiry_inquiry_created_by']!= "" && $_REQUEST['inquiry_inquiry_created_by']!="undefined")
	{
		$inquiry_where.="  AND  inquiry_created_by='".$_REQUEST['inquiry_inquiry_created_by']."'";
	} 
}
else
{
	// $inquiry_where.=" AND ( inquiry_created_by = '" . $_SESSION[SITE_SESS . 'REFERANCE_ID'] . "' )";
}

if($_REQUEST['inquiry_inquiry_assigned_to']!= "" && $_REQUEST['inquiry_inquiry_assigned_to']!="undefined")
{
	$inquiry_where.="  AND  inquiry_assign_to='".$_REQUEST['inquiry_inquiry_assigned_to']."' ";
}


if($_REQUEST['inquiry_todate']!="" && $_REQUEST['inquiry_fromdate']!="" && $_REQUEST['inquiry_month'] == "" && $_REQUEST['inquiry_todate']!="undefined" && $_REQUEST['inquiry_fromdate']!="undefined" && $_REQUEST['inquiry_month']!="undefined")
{
	// echo "dasdsad";exit;
	$inquiry_where.="  AND  DATE(inquiry_date)>='".date('Y-m-d',strtotime($_REQUEST['inquiry_todate']))."' AND  DATE(inquiry_date)<='".date('Y-m-d',strtotime($_REQUEST['inquiry_fromdate']))."'";
	$_REQUEST['inquiry_todate']=date('Y-m-d',strtotime($_REQUEST['inquiry_todate']));
	$_REQUEST['inquiry_fromdate']=date('Y-m-d',strtotime($_REQUEST['inquiry_fromdate']));
}

// $inquiry_where.="  AND  DATE(inquiry_date)>='".date('Y-m-d',strtotime($_REQUEST['inquiry_todate']))."' AND  DATE(inquiry_date)<='".date('Y-m-d',strtotime($_REQUEST['inquiry_fromdate']))."'";
// 	$_REQUEST['inquiry_todate']=date('Y-m-d',strtotime($_REQUEST['inquiry_todate']));
// 	$_REQUEST['inquiry_fromdate']=date('Y-m-d',strtotime($_REQUEST['inquiry_fromdate']));

// for link 
$inquiry_link.="no_order_inquiry_manage.php?";
if($_REQUEST['inquiry_year']== "" || $_REQUEST['inquiry_month']== "")
{
	$inquiry_link.="&inquiry_year=".date("Y");
}

if($_REQUEST['inquiry_month']!= "" && $_REQUEST['inquiry_year']== "")
{
	$inquiry_link.="&inquiry_month=".$_REQUEST['inquiry_month'];
}

if($_REQUEST['inquiry_month']!= "" && $_REQUEST['inquiry_year']!="")
{
	$inquiry_link.="&inquiry_month=".$_REQUEST['inquiry_month']."&inquiry_year=".$_REQUEST['inquiry_year'];
}

if($_REQUEST['inquiry_inquiry_created_by']!= "")
{
	$inquiry_link.="&inquiry_taken_by=".$_REQUEST['inquiry_inquiry_created_by'];
}

if($_REQUEST['inquiry_inquiry_assigned_to']!= "")
{
	$inquiry_link.="&inquiry_assign_by=".$_REQUEST['inquiry_inquiry_assigned_to'];
}

if($_REQUEST['inquiry_todate']!= "")
{
	$inquiry_link.="&todate=".$_REQUEST['inquiry_todate'];
}

if($_REQUEST['inquiry_fromdate']!= "")
{
	$inquiry_link.="&fromdate=".$_REQUEST['inquiry_fromdate'];
}

//$direct_inquiry = $db->rp_getTotalRecord("no_order_inquiry"," inq_status = '0' AND inquiry_lead_flag='0' AND ".$inquiry_where,0);

$direct_inquiry = $db->rp_getTotalRecord("no_order_inquiry","inquiry_lead_flag='0' AND inq_status!=2 AND ".$inquiry_where,0);

$prospect_to_inquiry  = $db->rp_getTotalRecord("no_order_inquiry","inq_status = 2 AND ".$inquiry_where,0);

$inquiry_to_lead =$db->rp_getTotalRecord("no_order_inquiry","inquiry_lead_flag = '1' AND ".$inquiry_where,0);

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	$dashboard_main_array_inquiry = array(
		
		//0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where."  AND (inquiry_lead_flag= '1' or inquiry_lead_flag = '0')",0),"Total Inquiry",$inquiry_link."&type=0",593,"="),

		0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where."  AND inquiry_lead_flag=0",0),"Total Inquiry",$inquiry_link."&type=0",593,"="),

		// 1=>array("#7bd0a9",$direct_inquiry,"Direct Inquiry","#",572,"+"),

		// 2=>array("#7bd0a9",$prospect_to_inquiry,"Prospect To Inquiry","#",572,"+"),

		// 3=>array("#7bd0a9",$inquiry_to_lead,"Inquiry To Lead","#",572),

		// 4=>array("#7bd0a9",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=0 AND (inquiry_lead_flag = '0')",0),"Pending Inquiry",$inquiry_link."&type=0&status=0",572),
		1=>array("#696969",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=0 AND (inquiry_lead_flag = '0')",0),"Generate Inquiry",$inquiry_link."&type=0&status=0",572),

		// 5=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=1 AND (inquiry_lead_flag = '0')",0),"In followup Inquiry",$inquiry_link."&type=0&status=1",572),
		2=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=2 AND (inquiry_lead_flag = '0')",0),"Positive Inquiry",$inquiry_link."&type=0&status=2",572),

		//6=>array("#ff6347",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=4 AND (inquiry_lead_flag = '0')",0),"Hot In followup Inquiry",$inquiry_link."&type=0&status=4",572),

		//7=>array("#7cfc00",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=5 AND (inquiry_lead_flag = '0')",0),"Cold In followup Inquiry",$inquiry_link."&type=0&status=5",572),

		//8=>array("#fada5e",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=6 AND (inquiry_lead_flag = '0')",0),"Warm In followup Inquiry",$inquiry_link."&type=0&status=6",572),

		// 9=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=-2 AND (inquiry_lead_flag = '0')",0),"Non Relavent Lost Inquiry",$inquiry_link."&type=0&status=-2",583),
		3=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=1 AND (inquiry_lead_flag = '0')",0),"In Followup Inquiry",$inquiry_link."&type=0&status=1",583),
		
		// 10=>array("#ec9b97",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=-1 AND (inquiry_lead_flag = '0')",0),"Not Interested Lost Inquiry",$inquiry_link."&type=0&status=-1",583),
		4=>array("#65B237",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=4 AND (inquiry_lead_flag = '0')",0),"Hot Inquiry",$inquiry_link."&type=0&status=4",583),

		5=>array("#3787B2",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=5 AND (inquiry_lead_flag = '0')",0),"Cold Inquiry",$inquiry_link."&type=0&status=5",583),

		6=>array("#B2A137",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=6 AND (inquiry_lead_flag = '0')",0),	"Warm Inquiry",$inquiry_link."&type=0&status=6",583),

		7=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=-2 AND (inquiry_lead_flag = '0')",0),"Cancel Inquiry",$inquiry_link."&type=0&status=-2",583),

		8=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=-1 AND (inquiry_lead_flag = '0')",0),"My Work Inquiry",$inquiry_link."&type=0&status=-1",583),

		9=>array("#ff4500",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=3 AND (inquiry_lead_flag = '0')",0),"Buy Later Inquiry",$inquiry_link."&type=0&status=3",583),
		10=>array("#ff4500",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=11 AND (inquiry_lead_flag = '0')",0),"Lost Inquiry",$inquiry_link."&type=0&status=11",583),
	);
}
else
{
	// $inquiry_where_sales=" AND ( inquiry_created_by = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."') ";

	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
	{ 
		if($rights['personal_flag']==1)
		{
			$inquiry_where_sales .= " AND inquiry_created_by='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
			$salesWhere .= "id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "' AND ";
		}
		else
		{ 
			if($rights['chain_vise_flag'] == 1)
		 	{ 
				$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
				$get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
			    if ($get_sales_type== "sales_manager") 
			    {
			        $sales_executive_type = "Regional Sales Manager";
			        $key="sm_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }
			    else if ($get_sales_type == "area_sales_manager") 
			    {
			        $sales_executive_type = "National Sales Manager";//Business Development Manager
			        $key="asm_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }
			    else if ($get_sales_type == "sales_officer") 
			    {
			        $sales_executive_type = "Area Sales Manager";//Area Sales Manager
			        $key="so_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }
			    else if ($get_sales_type == "sales_executive") 
			    {
			        $sales_executive_type = "Sales Officer";
			        $key="se_id";
			        $WhereCondition.=' ' .$key.'='.$check_id;
			    }
			    else
			    {
			    	$WhereCondition.=' type = "service_engineer"';
			    }

			    $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

			    $SALEID1=array();
				if($data)
				{
					while($data_d=mysqli_fetch_assoc($data))
					{
						$SALEID1[]=$data_d['id'];
					}
				}
				if(!empty($SALEID1))
				{
					$SALEID1=implode(",", $SALEID1);
					$inquiry_where_sales .= " AND inquiry_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
					$salesWhere .= " id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";	
				}
				else
				{
					$inquiry_where_sales .= " AND inquiry_created_by IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
					$salesWhere .= " id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";		
				}
			} 
		}
	} 

	$direct_inquiry_user = ($db->rp_getTotalRecord("no_order_inquiry","isDelete = 0 AND inquiry_lead_flag='0' AND inq_status!=2 AND ".$inquiry_where.$inquiry_where_sales,0));

	$prospect_to_inquiry_user  = $db->rp_getTotalRecord("no_order_inquiry","isDelete = 0 AND (inq_status = 2) AND ".$inquiry_where.$inquiry_where_sales,0);

	$inquiry_to_lead_user  = $db->rp_getTotalRecord("no_order_inquiry","isDelete = 0 AND inquiry_lead_flag = '1' AND ".$inquiry_where.$inquiry_where_sales,0);

	$dashboard_main_array_inquiry = array(
		
		//0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where."  AND (inquiry_lead_flag= '1' or inquiry_lead_flag = '0')",0),"Total Inquiry",$inquiry_link."&type=0",593,"="),

		0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales."  AND inquiry_lead_flag=0",0),"Total Inquiry",$inquiry_link."&type=0",593,"="),

		// 1=>array("#7bd0a9",$direct_inquiry,"Direct Inquiry","#",572,"+"),

		// 2=>array("#7bd0a9",$prospect_to_inquiry,"Prospect To Inquiry","#",572,"+"),

		// 3=>array("#7bd0a9",$inquiry_to_lead,"Inquiry To Lead","#",572),

		// 4=>array("#7bd0a9",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where." AND status=0 AND (inquiry_lead_flag = '0')",0),"Pending Inquiry",$inquiry_link."&type=0&status=0",572),
		1=>array("#696969",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=0 AND (inquiry_lead_flag = '0')",0),"Generate Inquiry",$inquiry_link."&type=0&status=0",572),

		// 5=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=1 AND (inquiry_lead_flag = '0')",0),"In followup Inquiry",$inquiry_link."&type=0&status=1",572),
		2=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=2 AND (inquiry_lead_flag = '0')",0),"Positive Inquiry",$inquiry_link."&type=0&status=2",572),

		//6=>array("#ff6347",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=4 AND (inquiry_lead_flag = '0')",0),"Hot In followup Inquiry",$inquiry_link."&type=0&status=4",572),

		//7=>array("#7cfc00",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=5 AND (inquiry_lead_flag = '0')",0),"Cold In followup Inquiry",$inquiry_link."&type=0&status=5",572),

		//8=>array("#fada5e",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=6 AND (inquiry_lead_flag = '0')",0),"Warm In followup Inquiry",$inquiry_link."&type=0&status=6",572),

		// 9=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=-2 AND (inquiry_lead_flag = '0')",0),"Non Relavent Lost Inquiry",$inquiry_link."&type=0&status=-2",583),
		3=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=1 AND (inquiry_lead_flag = '0')",0),"In Followup Inquiry",$inquiry_link."&type=0&status=1",583),
		
		// 10=>array("#ec9b97",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=-1 AND (inquiry_lead_flag = '0')",0),"Not Interested Lost Inquiry",$inquiry_link."&type=0&status=-1",583),
		4=>array("#65B237",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=4 AND (inquiry_lead_flag = '0')",0),"Hot Inquiry",$inquiry_link."&type=0&status=4",583),

		5=>array("#3787B2",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=5 AND (inquiry_lead_flag = '0')",0),"Cold Inquiry",$inquiry_link."&type=0&status=5",583),

		6=>array("#B2A137",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=6 AND (inquiry_lead_flag = '0')",0),	"Warm Inquiry",$inquiry_link."&type=0&status=6",583),

		7=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=-2 AND (inquiry_lead_flag = '0')",0),"Cancel Inquiry",$inquiry_link."&type=0&status=-2",583),

		8=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=-1 AND (inquiry_lead_flag = '0')",0),"My Work Inquiry",$inquiry_link."&type=0&status=-1",583),

		9=>array("#ff4500",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=3 AND (inquiry_lead_flag = '0')",0),"Buy Later Inquiry",$inquiry_link."&type=0&status=3",583),
		10=>array("#ff4500",$db->rp_getTotalRecord("no_order_inquiry",$inquiry_where.$inquiry_where_sales." AND status=11 AND (inquiry_lead_flag = '0')",0),"Lost Inquiry",$inquiry_link."&type=0&status=11",583),
	);
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
// { 
	
	?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> Inquiry Statistic</span>
			</div>
			<div class="col-md-3" id="todate_div" >
				<label>To Date</label>
				<input type="date" name="inquiry_todate" id="inquiry_todate" value="<?= $_REQUEST['inquiry_todate'] ?>" onChange="monthYearClear('inquiry')" class="form-control" autocomplete="off">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="inquiry_fromdate" id="inquiry_fromdate" value="<?= $_REQUEST['inquiry_fromdate'] ?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getinquiry();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="inquiry_year" id="inquiry_year" >
								<option value="">Select Year </option>
								<?php 
									$reg_year=date("Y","2017");
									$curr_year=date("Y");
									$current_date=date('Y-m-d');
									$adate1 = date('Y', strtotime($current_date));
									for ($i=$curr_year-$reg_year; $i>=0;$i--) 
									{
										?>
										<option <?php echo ($_REQUEST['inquiry_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
										<?php
									}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="inquiry_month" id="inquiry_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) {
								?>
									<option <?php echo (date("m", strtotime($month))==$_REQUEST['inquiry_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<?php
							/*if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
							{
								$selected_id_create = $_REQUEST['inquiry_inquiry_created_by'];
								$selected_id_assign = $_REQUEST['inquiry_inquiry_assigned_to'];

								$disabled = "";
							}
							else
							{
								$selected_id_create=  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$selected_id_assign=  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}*/
							$selected_id_create = $_REQUEST['inquiry_inquiry_created_by'];
							$selected_id_assign = $_REQUEST['inquiry_inquiry_assigned_to'];
							$salesWhere.="isDelete=0 AND isActive=1";
							?>
							<select class="form-control" name="inquiry_inquiry_created_by" id="inquiry_inquiry_created_by" value="<?php echo $customer_id;?>" <?=$disabled?>>
								<option value="">Inquiry Created By</option>
								<?php 
								$sales_r=$db->rp_getData('sales_executive',"*",$salesWhere,"name ASC",0);
								while($sales_d=mysqli_fetch_assoc($sales_r))
								{
								?>
								<option <?php if($sales_d['id']==$selected_id_create){?> selected <?php } ?>  value="<?php echo $sales_d['id']?>"><?php echo $sales_d['name']?></option>
								<?php
								}
								?>
							</select>
						</div> 
						<div class="col-sm-3">
							<select class="form-control" name="inquiry_inquiry_assigned_to" id="inquiry_inquiry_assigned_to" value="<?php echo $sales_id;?>" <?= $disabled ?>>
								<option value="">Inquiry Assigned to</option>
								<?php 
								$sales_r1=$db->rp_getData('sales_executive',"*",$salesWhere,"name ASC",0);
								while($sales_d1=mysqli_fetch_assoc($sales_r1))
								{
								?>
								<option <?php if($sales_d1['id']==$selected_id_assign){?> selected <?php } ?>  value="<?php echo $sales_d1['id']?>"><?php echo $sales_d1['name']?></option>
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
						foreach($dashboard_main_array_inquiry as $arr_inquiry)
						{
							?>
							<div class="" style="padding: 10px;display: inline-block;vertical-align:top;/*margin-right:20px;*/white-space:normal;">
								<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_inquiry[0] ?> solid; width: 120px; height: 100px;">
									<a href="<?php echo $arr_inquiry[3]; ?>" style="text-decoration: none;">        
									<div class="desc" style="text-align: center;">
									   <div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
						                    <span data-counter="counterup" data-value="<?php echo $arr_inquiry[1]; ?>"> <?php echo $arr_inquiry[1]; ?> </span>
						               	</div>
						                <strong><?php echo $arr_inquiry[2]; ?></strong>
									</div>
									</a>
								</div>
							</div>
							<div style="display: inline-block; padding-top: 50px;"><strong style="font-size: 25px;"><?= $arr_inquiry[5] ?></strong></div>
							<?php
						}	
						?>
					</div>
					<div class="col-md-12 col-sm-12 co-xs-6 col-lg-12">
						<div class="portlet-body ">
							<div class="portlet-title">
								<div class="caption ">
									<br><br>
									<span class="caption-subject bold uppercase font-dark">Inquiry Chart</span>
									<!-- <span class="caption-helper">monthly order stats...</span> -->
								</div>
							</div>
							<div id="inquiry" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$("#inquiry_year").select2();
		$("#inquiry_month").select2();
		$("#inquiry_inquiry_created_by").select2();
		$("#inquiry_inquiry_assigned_to").select2();
		// jQuery(document).ready(function() {
		//     // Graph_inquiry.init_inquiry();
		//     graph_inquiry_pie.init_inquiry_pie();
		// });
	</script>

<?php
$db->disconnect(); 
?>