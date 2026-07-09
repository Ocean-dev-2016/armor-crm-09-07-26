<?php 
$page_id=593;$page_slug='attendance_page';
include("connect.php");
// print_r($_REQUEST);exit;
// $_REQUEST['attandance_todate']=date('Y-m-d',strtotime($_REQUEST['attandance_todate']));
// $_REQUEST['attandance_fromdate']=date('Y-m-d',strtotime($_REQUEST['attandance_fromdate']));
function checkVar($var) {
    if (
        isset($var) &&
        !empty($var) &&
        $var != undefined &&
        $var != null &&
        $var != NULL &&
        $var != "" &&
        $var != 'undefined' &&
        $var != 'NULL'
    ) {
        return true;
    } else {
        return false;
    }
}

if($_REQUEST['attendance_year']=="" && $_REQUEST['attendance_year']=="undefined")
{
	$_REQUEST['attendance_year']=date("Y");
}
$attendance_where="isDelete=0";
if($_REQUEST['attendance_year']!= "" && $_REQUEST['attendance_year']!="undefined")
{
	$attendance_where.="  AND  Year(date_time)='".$_REQUEST['attendance_year']."'";
}

if($_REQUEST['attendance_month']!= "" && $_REQUEST['attendance_month']!="undefined")
{
	$attendance_where.="  AND  MONTH(date_time)='".$_REQUEST['attendance_month']."'";
}

if($_REQUEST['attendance_customer_id']!= "" && $_REQUEST['attendance_customer_id']!="undefined")
{
	$attendance_where.="  AND  customer_id='".$_REQUEST['attendance_customer_id']."'";
} 

if($_REQUEST['attendance_sales_id']!= "" && $_REQUEST['attendance_sales_id']!="undefined")
{
	$attendance_where.="  AND  sales_id='".$_REQUEST['attendance_sales_id']."'";
} 

if($_REQUEST['attendance_todate']!="" && $_REQUEST['attendance_fromdate']!="" && $_REQUEST['attendance_fromdate']!="undefined" && $_REQUEST['attendance_todate']!="undefined")
{
	$attendance_where.="  AND  DATE(date_time)>='".date('Y-m-d',strtotime($_REQUEST['attendance_todate']))."' AND  DATE(date_time)<='".date('Y-m-d',strtotime($_REQUEST['attendance_fromdate']))."'";
	$_REQUEST['attendance_todate']=date('Y-m-d',strtotime($_REQUEST['attendance_todate']));
	$_REQUEST['attendance_fromdate']=date('Y-m-d',strtotime($_REQUEST['attendance_fromdate']));
}

$attendance_link.="attendance_manage.php?redirect=dashboard";

if(checkVar($_REQUEST['attendance_month']))
{
	$attendance_link.="&attendance_month=".$_REQUEST['attendance_month'];
}
if(checkVar($_REQUEST['attendance_month']) && checkVar($_REQUEST['attendance_year']))
{
	$attendance_link.="&attendance_month=".$_REQUEST['attendance_month']."&attendance_year=".$_REQUEST['attendance_year'];
}

if(checkVar($_REQUEST['attendance_sales_id']))
{
	$attendance_link.="&id=".$_REQUEST['attendance_sales_id'];
}

if(checkVar($_REQUEST['attendance_todate']))
{
	$attendance_link.="&todate=".$_REQUEST['attendance_todate'];
}

if(checkVar($_REQUEST['attendance_fromdate']))
{
	$attendance_link.="&fromdate=".$_REQUEST['attendance_fromdate'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==13)
{ 
}
else
{ 
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
	{ 
		if($rights['personal_flag']==1)
		{
			$attendance_where .= " AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
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
					$attendance_where .= " AND sales_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
					$salesWhere .= " id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";	
				}
				else
				{
					$attendance_where .= " AND sales_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
					$salesWhere .= " id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";		
				}
			} 
		}
	}  
	// $attendance_where_sales=" AND sales_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
}
// $sales_ids=array();
// 	$create_system_user_r=$db->rp_getData("dealer_distributor_network","*","isDelete=0");
// 	while($create_system_user_d=mysqli_fetch_assoc($create_system_user_r))
// 	{
// 		$sales_ids[]=$create_system_user_d['sales_executive_id'];
// 	}
// 	$sales_ids=implode(",", $sales_ids);
	// $create_system_user_r=$db->rp_getTotalRecord("sales_executive","isDelete=0 AND isActive=1 AND sales_executive_id NOT IN (".$sales_ids.")",0);
$total_sales = $db->rp_getTotalRecord("sales_executive","isDelete=0 AND isActive=1",0);
$total_sales_punchIn = $db->rp_getTotalRecord("attendance",$attendance_where.' GROUP BY sales_id ',0);
$total_sales_NotPucnh = ($total_sales - $total_sales_punchIn);
$not_use_mobie = ($total_sales - $total_sales_NotPucnh);

$dashboard_main_array_attendance = array(
	0=>array("#9fc1ff",$db->rp_getValue("sales_executive","COUNT(id)","isDelete=0",0),"Total Sales Executive",$attendance_link,593),

	1=>array("#9fc1ff",$total_sales."/".$not_use_mobie,"Total Sales Person Use Mobile",$attendance_link,593),
	
	2=>array("black",$db->rp_getTotalRecord("attendance",$attendance_where,0),"Total Attendance",$attendance_link,593),
	
	3=>array("#9fc1ff",$db->rp_getTotalRecord("attendance",$attendance_where." AND inout_status='in' ",0),"In",$attendance_link."&status=in",572),
	
	4=>array("#9fc1ff",$db->rp_getTotalRecord("attendance",$attendance_where." AND inout_status='out'",0),"Out",$attendance_link."&status=out",572),
	// 4=>array("#9fc1ff",$db->rp_getValue("attendance","count(DISTINCT sales_id)",$attendance_where." AND (inout_status='in' OR inout_status='out')",0),"Total Sales Person Use Mobile ",$attendance_link,572),
	
);
// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
// {
?>
<div class="portlet light div-set-height">
	<div class="portlet-title">
		<div class="caption caption-md">
			<i class="icon-bar-chart font-dark hide"></i>
			<span class="caption-subject font-dark bold uppercase"> attendance Statistic</span>
		</div>
		<div class="col-md-3" id="todate_div" >
			<label>From Date</label>
			<input type="date" name="attendance_todate" id="attendance_todate" value="<?=$_REQUEST['attendance_todate']?>" onChange="monthYearClear('attendance')" class="form-control" autocomplete="off">
		</div>
		<div class="col-md-3" id="todate_div">
			<label>To Date</label>
			<input type="date" name="attendance_fromdate" id="attendance_fromdate" value="<?=$_REQUEST['attendance_fromdate']?>" class="form-control">
		</div>
		<span style="float: right;">
			<a href="javascript:;"  onClick="return getattendance();" class="btn btn-circle red-sunglo ">
			<i class="fa fa-refresh"></i>Refresh </a>
		</span>
	</div>
	<div class="portlet-body">
		<div class="row">
			<div class="col-sm-12 pull-right">
				<div class="row">
					<div class="col-sm-3">
						<select onChange="" class="form-control" name="attendance_year" id="attendance_year" >
							<option value="">Select Year </option>
							<?php 
							$reg_year=date("Y","2017");
							$curr_year=date("Y");
							$current_date=date('Y-m-d');
							$adate1 = date('Y', strtotime($current_date));
							for ($i=$curr_year-$reg_year; $i>=0;$i--) 
							{
								?>
								<option <?php echo ($_REQUEST['attendance_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
								<?php
							}
							?>
						</select>
					</div>
					<div class="col-sm-3">
						<select class="form-control" name="attendance_month" id="attendance_month">
							<option value="">Select Month</option>
							<?php 
							$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
							foreach ($months as $month) 
							{
								?>
								<option <?php echo (date("m", strtotime($month))==$_REQUEST['attendance_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
								<?php
							}
							?>
						</select>
					</div>
					<div class="col-sm-3">
						<?php
						if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==13)
						{
							// $selected_id = $_REQUEST['attendance_sales_id'];
							// $disabled = "";
						}
						else
						{
							// $selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
							// $disabled = "disabled";
						}
						// echo $salesWhere;
						$selected_id = $_REQUEST['attendance_sales_id'];
						$salesWhere.="isDelete=0 AND isActive=1";
						// echo $salesWhere;
						?>
						<select class="form-control attendance_sales_id" name="sales_id" id="attendance_sales_id" value="<?php echo $sales_id;?>" <?=$disabled?>>
							<option value="">Select Executive</option>
							<?php 
							$sales_r=$db->rp_getData('sales_executive',"*",$salesWhere,"name ASC",0);
							while($sales_d=mysqli_fetch_assoc($sales_r))
							{
							?>
							<option <?php if($sales_d['id']==$selected_id){?> selected <?php } ?>  value="<?php echo $sales_d['id']?>"><?php echo $sales_d['name']?></option>
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
					foreach($dashboard_main_array_attendance as $arr_attendance)
					{
					?>
					<div class="" style="padding: 10px;display: inline-block;vertical-align:top;/*margin-right:20px;*/white-space:normal;">
						<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_attendance[0] ?> solid; width: 120px; height: 100px;">
						 	<a href="<?=  $arr_attendance[3];  ?>" style="text-decoration: none;">         
								<div class="desc" style="text-align: center;">
									<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
			                    		<span data-counter="counterup" data-value="<?php echo $arr_attendance[1]; ?>"> <?php echo $arr_attendance[1]; ?> </span>
			               			</div>
			                		<strong><?php echo $arr_attendance[2]; ?></strong>
								</div>
							</a>
						</div>
					</div>
					<?php
					}	
					?>
				</div>
				<div class="row" style="margin-top: 18px"></div>
				<div class="col-md-12 col-sm-12 co-xs-6 col-lg-12">
					<div class="portlet-body ">
						<div class="portlet-title">
							<div class="caption ">
								<br><br>
								<span class="caption-subject bold uppercase font-dark">attendance Chart</span>
							</div>
						</div>
						<div id="attendance" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$("#attendance_year").select2();
$("#attendance_month").select2();
$(".attendance_customer_id").select2();
$(".attendance_sales_id").select2();
// jQuery(document).ready(function() {
// 	// Graph_attendance.init_attendance();
// 	  graph_attendance_pie.init_attendance_pie();
// });
</script>

<?php
$db->disconnect(); 
?>