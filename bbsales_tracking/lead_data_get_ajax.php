<?php 
$page_id=620;$page_slug='lead_page';
include("connect.php");

// if($_REQUEST['lead_year'] =="")
// {
// 	$_REQUEST['lead_year']=date("Y");
// }
//print_r($_REQUEST);exit();
// if($_REQUEST['lead_year'] =="")
// {
// 	$_REQUEST['lead_year']=date("Y");
// }
$lead_where="isDelete=0";
if($_REQUEST['lead_year'] != "")
{
	$lead_where.="  AND  Year(inquiry_date)='".$_REQUEST['lead_year']."'";
}

if($_REQUEST['lead_month'] != "")
{
	$lead_where.="  AND  MONTH(inquiry_date)='".$_REQUEST['lead_month']."'";
}

if($_REQUEST['lead_inquiry_created_by'] != "")
{
	$lead_where.="  AND  inquiry_created_by='".$_REQUEST['lead_inquiry_created_by']."'";
}

if($_REQUEST['lead_inquiry_assigned_to'] != "")
{
	$lead_where.="  AND  inquiry_assign_to='".$_REQUEST['lead_inquiry_assigned_to']."'";
} 

if($_REQUEST['lead_todate'] !="" && $_REQUEST['lead_fromdate'] !="" && $_REQUEST['lead_month'] == "")
{
	$lead_where.="  AND  DATE(inquiry_date)>='".date('Y-m-d',strtotime($_REQUEST['lead_todate']))."' AND  DATE(inquiry_date)<='".date('Y-m-d',strtotime($_REQUEST['lead_fromdate']))."'";
	$_REQUEST['lead_todate']=date('Y-m-d',strtotime($_REQUEST['lead_todate']));
	$_REQUEST['lead_fromdate']=date('Y-m-d',strtotime($_REQUEST['lead_fromdate']));
}

// for link 
$lead_link.="no_order_inquiry_manage.php?";
if($_REQUEST['lead_year'] == "" || $_REQUEST['lead_month'] == "")
{
	// echo "test";exit();
	$lead_link.="&lead_year=".date("Y");
	// $invoice_link=stripcslashes($invoice_link);
}

if($_REQUEST['lead_month'] != "" && $_REQUEST['lead_year'] == "")
{
	$lead_link.="&lead_month=".$_REQUEST['lead_month'];
	// $lead_link=stripcslashes($lead_link);
}
if($_REQUEST['lead_month'] != "" && $_REQUEST['lead_year'] !="")
{
	$lead_link.="&lead_month=".$_REQUEST['lead_month']."&lead_year=".$_REQUEST['lead_year'];
	// $inquiry_link=stripcslashes($inquiry_link);
}

// if($_REQUEST['inquiry_month'] == "" && $_REQUEST['inquiry_year'] =="")
// {
// 	$inquiry_link="inquiry_manage.php?";
// 	$inquiry_link=stripcslashes($inquiry_link);
// }

if($_REQUEST['lead_inquiry_created_by'] != "")
{
	$lead_link.="&inquiry_taken_by=".$_REQUEST['lead_inquiry_created_by'];
}

if($_REQUEST['lead_inquiry_assigned_to'] != "")
{
	$lead_link.="&inquiry_assign_by=".$_REQUEST['lead_inquiry_assigned_to'];
}

if($_REQUEST['lead_todate'] != "")
{
	$lead_link.="&todate=".$_REQUEST['lead_todate'];
}

if($_REQUEST['lead_fromdate'] != "")
{
	$lead_link.="&fromdate=".$_REQUEST['lead_fromdate'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	$dashboard_main_array_lead = array(
		
		0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$lead_where."  AND inquiry_lead_flag = '1'",0),"Total Lead",$lead_link."&type=1",593),

		1=>array("#7bd0a9",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=0 AND inquiry_lead_flag = '1'",0),"Generate Lead",$lead_link."&type=1&status=0",572),

		2=>array("#696969",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=2 AND inquiry_lead_flag = '1'",0),"Positive Lead",$lead_link."&type=1&status=2",572),

		3=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=1 AND inquiry_lead_flag = '1'",0),"In followup Lead",$lead_link."&type=1&status=1",572),

		4=>array("#65B237",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=4 AND inquiry_lead_flag = '1'",0),"Hot Lead",$lead_link."&type=1&status=4",572),

		5=>array("#3787B2",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=5 AND inquiry_lead_flag = '1'",0),"Cold Lead",$lead_link."&type=1&status=5",572),

		6=>array("#B2A137",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=6 AND inquiry_lead_flag = '1'",0),"Warm Lead",$lead_link."&type=1&status=6",572),

		7=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=-2 AND inquiry_lead_flag = '1'",0),"Cancel Lead",$lead_link."&type=1&status=-2",572),

		8=>array("#ff4500",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=-1 AND inquiry_lead_flag = '1'",0),"My Work Lead",$lead_link."&type=1&status=-1",572),

		9=>array("#65B237",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=3 AND inquiry_lead_flag = '1'",0),"Buy Later Lead",$lead_link."&type=1&status=3",572),

		10=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=11 AND inquiry_lead_flag = '1'",0),"Lost Lead",$lead_link."&type=1&status=11",572),

		//3=>array("#ff6347",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=4 AND inquiry_lead_flag = '1'",0),"Hot In followup Lead",$lead_link."&type=1&status=4",572),
		
		//4=>array("#7cfc00",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=5 AND inquiry_lead_flag = '1'",0),"Cold In followup Lead",$lead_link."&type=1&status=5",572),

		//5=>array("#fada5e",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=6 AND inquiry_lead_flag = '1'",0),"Warm In followup Lead",$lead_link."&type=1&status=6",572),

		// 6=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=-2 AND inquiry_lead_flag = '1'",0),"Non Relavent Lost Lead",$lead_link."&type=1&status=-2",572),

		// 7=>array("#ec9b97",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=-1 AND inquiry_lead_flag = '1'",0),"Not Interested Lost Lead",$lead_link."&type=1&status=-1",572),

		// 8=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$lead_where." AND status=3 AND inquiry_lead_flag = '1'",0),"Buy Later Lost Lead",$lead_link."&type=1&status=3",572),

		
		
	);
}
else
{
	// $lead_where_sales=" AND (inquiry_created_by = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ) "; 
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
	{ 
		if($rights['personal_flag']==1)
		{
			$lead_where_sales .= " AND inquiry_created_by='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
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
					$lead_where_sales .= " AND inquiry_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
					$salesWhere .= " id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";	
				}
				else
				{
					$lead_where_sales .= " AND inquiry_created_by IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
					$salesWhere .= " id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";		
				}
			} 
		}
	}

	$dashboard_main_array_lead = array(

		0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales."  AND inquiry_lead_flag = '1'",0),"Total Lead",$lead_link."&type=1",593),

		1=>array("#7bd0a9",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=0 AND inquiry_lead_flag = '1'",0),"Generate Lead",$lead_link."&type=1&status=0",572),

		2=>array("#696969",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=2 AND inquiry_lead_flag = '1'",0),"Positive Lead",$lead_link."&type=1&status=2",572),

		3=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=1 AND inquiry_lead_flag = '1'",0),"In followup Lead",$lead_link."&type=1&status=1",572),

		4=>array("#65B237",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=4 AND inquiry_lead_flag = '1'",0),"Hot Lead",$lead_link."&type=1&status=4",572),

		5=>array("#3787B2",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=5 AND inquiry_lead_flag = '1'",0),"Cold Lead",$lead_link."&type=1&status=5",572),

		6=>array("#B2A137",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=6 AND inquiry_lead_flag = '1'",0),"Warm Lead",$lead_link."&type=1&status=6",572),

		7=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=-2 AND inquiry_lead_flag = '1'",0),"Cancel Lead",$lead_link."&type=1&status=-2",572),

		8=>array("#ff4500",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=-1 AND inquiry_lead_flag = '1'",0),"My Work Lead",$lead_link."&type=1&status=-1",572),

		9=>array("#65B237",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=3 AND inquiry_lead_flag = '1'",0),"Buy Later Lead",$lead_link."&type=1&status=3",572),

		10=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=11 AND inquiry_lead_flag = '1'",0),"Lost Lead",$lead_link."&type=1&status=11",572),

		
		// 0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales."  AND inquiry_lead_flag = '1'",0),"Total Lead",$lead_link."&type=1",593),

		// 1=>array("#7bd0a9",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=0 AND inquiry_lead_flag = '1'",0),"Pending Lead",$lead_link."&type=1&status=0",572),

		// 2=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=1 AND inquiry_lead_flag = '1'",0),"In followup Lead",$lead_link."&type=1&status=1",572),

		//3=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=4 AND inquiry_lead_flag = '1'",0),"Hot In followup Lead",$lead_link."&type=1&status=4",572),

		//4=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=5 AND inquiry_lead_flag = '1'",0),"Cold In followup Lead",$lead_link."&type=1&status=5",572),

		//5=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=6 AND inquiry_lead_flag = '1'",0),"Warm In followup Lead",$lead_link."&type=1&status=6",572),

		// 6=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=-2 AND inquiry_lead_flag = '1'",0),"Non Relavent Lost Lead",$lead_link."&type=1&status=-2",572),

		// 7=>array("#ec9b97",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=-1 AND inquiry_lead_flag = '1'",0),"Not Interested Lost Lead",$lead_link."&type=1&status=-1",572),

		// 8=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=3 AND inquiry_lead_flag = '1'",0),"Buy Later Lost Lead",$lead_link."&type=1&status=3",572),

		// 9=>array("ffa07a",$db->rp_getTotalRecord("no_order_inquiry",$lead_where.$lead_where_sales." AND status=11 AND inquiry_lead_flag = '1'",0),"Lost Lead",$lead_link."&type=1&status=11",572),
		
	);
}
?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> Lead Statistic</span>
			</div>
			<div class="col-md-3" id="todate_div" >
				<label>To Date</label>
				<input type="date" name="lead_todate" id="lead_todate" value="<?= $_REQUEST['lead_todate'] ?>" class="form-control">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="lead_fromdate" id="lead_fromdate" value="<?= $_REQUEST['lead_fromdate'] ?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick=" getlead();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="lead_year" id="lead_year" >
								<option value="">Select Year </option>
								<?php 
								$reg_year=date("Y","2017");
								$curr_year=date("Y");
								$current_date=date('Y-m-d');
								$adate1 = date('Y', strtotime($current_date));
								for ($i=$curr_year-$reg_year; $i>=0;$i--) 
								{
								?>
								<option <?php echo ($_REQUEST['lead_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
								<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="lead_month" id="lead_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) 
								{
								?>
								<option <?php echo (date("m", strtotime($month))==$_REQUEST['lead_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
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
							$selected_id_create1 = $_REQUEST['lead_inquiry_assigned_to'];
							$selected_id_assign1 = $_REQUEST['lead_inquiry_assigned_to'];
							$salesWhere.="isDelete=0 AND isActive=1";
							?>
							<select class="form-control" name="lead_inquiry_created_by" id="lead_inquiry_created_by" value="<?php echo $customer_id;?>" <?=$disabled?>>
								<option value="">Lead Created By</option>
								<?php 
								$sales_r=$db->rp_getData('sales_executive',"*",$salesWhere,"name ASC",0);
								while($sales_d=mysqli_fetch_assoc($sales_r))
								{
								?>
								<option <?php if($sales_d['id']==$selected_id_create1){?> selected <?php } ?>  value="<?php echo $sales_d['id']?>"><?php echo $sales_d['name']?></option>
								<?php
								}
								?>
							</select>
						</div> 
						<div class="col-sm-3">
							<select class="form-control" name="lead_inquiry_assigned_to" id="lead_inquiry_assigned_to" value="<?php echo $sales_id;?>" <?= $disabled ?>>
								<option value="">Lead Assigned to</option>
								<?php 
								$sales_r1=$db->rp_getData('sales_executive',"*",$salesWhere,"name ASC",0);
								while($sales_d1=mysqli_fetch_assoc($sales_r1))
								{
								?>
								<option <?php if($sales_d1['id']==$selected_id_assign1){?> selected <?php } ?>  value="<?php echo $sales_d1['id']?>"><?php echo $sales_d1['name']?></option>
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
						foreach($dashboard_main_array_lead as $arr_lead)
						{
							?>
							<div class="" style="padding: 10px;display: inline-block;vertical-align:top;/*margin-right:20px;*/white-space:normal;">
								<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_lead[0] ?> solid; width: 120px; height: 100px; " >
									<a href="<?php echo $arr_lead[3]; ?>" style="text-decoration: none;">          
									<div class="desc" style="text-align: center;">
									   
									  	<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
						                    <span data-counter="counterup" data-value="<?php echo $arr_lead[1]; ?>"> <?php echo $arr_lead[1]; ?> </span>
						               </div>
						                <strong style="font-size:12px;"><?php echo $arr_lead[2]; ?></strong>
									</div>
									</a>
								</div>
							</div>
							<?php
						}	
						?>
					</div>
					<div class="row" style="margin-top: 18px;"></div>
					<!-- <br> -->
					<div class="col-md-12 col-sm-12 co-xs-6 col-lg-12">
						<div class="portlet-body ">
							<div class="portlet-title">
								<div class="caption ">
									<br><br>
									<span class="caption-subject bold uppercase font-dark">Lead Chart</span>
								</div>
							</div>
							<div id="lead" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$("#lead_year").select2();
		$("#lead_month").select2();
		$("#lead_inquiry_created_by").select2();
		$("#lead_inquiry_assigned_to").select2();
		// jQuery(document).ready(function() {
	 //    	// Graph_lead.init_lead();
	 //    	graph_lead_pie.init_lead_pie();
		// });
	</script>

<?php
$db->disconnect(); 
?>