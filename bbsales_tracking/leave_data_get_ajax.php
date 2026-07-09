<?php 
$page_id=594;$page_slug='leave_request';
include("connect.php");
// $_REQUEST['leave_todate']=date('Y-m-d',strtotime($_REQUEST['leave_todate']));
// $_REQUEST['leave_fromdate']=date('Y-m-d',strtotime($_REQUEST['leave_fromdate']));
$leave_where="isDelete=0";
if($_REQUEST['leave_year'] =="")
{
	$_REQUEST['leave_year']=date("Y");
}

if($_REQUEST['leave_year'] != "")
{
	$leave_where.="  AND  Year(start_date)='".$_REQUEST['leave_year']."'";
}

if($_REQUEST['leave_month'] != "")
{
	$leave_where.="  AND  MONTH(start_date)='".$_REQUEST['leave_month']."'";
}

if($_REQUEST['leave_sales_id'] != "")
{
	$leave_where.="  AND  sales_executive_id='".$_REQUEST['leave_sales_id']."'";
} 

if($_REQUEST['leave_todate'] !="" && $_REQUEST['leave_fromdate'] !="" && $_REQUEST['leave_month'] == "")
{
	$leave_where.="  AND  DATE(start_date)>='".date('Y-m-d',strtotime($_REQUEST['leave_todate']))."' AND  DATE(start_date)<='".date('Y-m-d',strtotime($_REQUEST['leave_fromdate']))."'";
	$_REQUEST['leave_todate']=date('Y-m-d',strtotime($_REQUEST['leave_todate']));
	$_REQUEST['leave_fromdate']=date('Y-m-d',strtotime($_REQUEST['leave_fromdate']));

}


$leave_link.="leave_request_manage.php?";

if($_REQUEST['leave_year'] == "" || $_REQUEST['leave_month'] == "")
{
	$leave_link.="&leave_year=".date("Y");
}

if($_REQUEST['leave_month'] != "" && $_REQUEST['leave_year'] == "")
{
	$leave_link.="&leave_month=".$_REQUEST['leave_month'];
}

if($_REQUEST['leave_month'] != "" && $_REQUEST['leave_year'] !="")
{
	$leave_link.="&leave_month=".$_REQUEST['leave_month']."&leave_year=".$_REQUEST['leave_year'];
}

if($_REQUEST['leave_sales_id'] != "")
{
	$leave_link.="&sales_id=".$_REQUEST['leave_sales_id'];
}

if($_REQUEST['leave_todate'] != "")
{
	$leave_link.="&todate=".$_REQUEST['leave_todate'];
}
if($_REQUEST['leave_fromdate'] != "")
{
	$leave_link.="&fromdate=".$_REQUEST['leave_fromdate'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	$dashboard_main_array_leave = array(
		0=>array("black",$db->rp_getTotalRecord("leave_request",$leave_where,0),"Total Leave",$leave_link,593),
		1=>array("#7bd0a9",$db->rp_getTotalRecord("leave_request",$leave_where." AND status=0 ",0),"Generated",$leave_link."&status=0",572),
		2=>array("#9fc1ff",$db->rp_getTotalRecord("leave_request",$leave_where." AND status=1 ",0),"Accepted",$leave_link."&status=1",572),
		3=>array("#9fc1ff",$db->rp_getTotalRecord("leave_request",$leave_where." AND status=2 ",0),"Rejected",$leave_link."&status=2",572),
		4=>array("#9fc1ff",$db->rp_getTotalRecord("leave_request",$leave_where." AND status=3 ",0),"Cancel",$leave_link."&status=3",572),
	);
}
else
{
	// $leave_where_sales=" AND sales_executive_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
	{ 
		if($rights['personal_flag']==1)
		{
			$leave_where_sales .= " AND sales_executive_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
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
					$leave_where_sales .= " AND sales_executive_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
					$salesWhere .= " id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";
				}
				else
				{
					$leave_where_sales .= " AND sales_executive_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
					$salesWhere .= " id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";			
				}
			} 
		}
	} 
	$dashboard_main_array_leave = array(
		0=>array("black",$db->rp_getTotalRecord("leave_request",$leave_where.$leave_where_sales,0),"Total Leave",$leave_link,593),
		1=>array("#7bd0a9",$db->rp_getTotalRecord("leave_request",$leave_where.$leave_where_sales." AND status=0 ",0),"Generated",$leave_link."&status=0",572),
		2=>array("#9fc1ff",$db->rp_getTotalRecord("leave_request",$leave_where.$leave_where_sales." AND status=1 ",0),"Accepted",$leave_link."&status=1",572),	
		3=>array("#9fc1ff",$db->rp_getTotalRecord("leave_request",$leave_where.$leave_where_sales." AND status=2 ",0),"Rejected",$leave_link."&status=2",572),		
		4=>array("#9fc1ff",$db->rp_getTotalRecord("leave_request",$leave_where.$leave_where_sales." AND status=3 ",0),"Cancel",$leave_link."&status=3",572),
	);
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
// { 
	?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> leave Statistic</span>
			</div>

			<div class="col-md-3" id="todate_div" >
				<label>To Date</label>
				<input type="date" name="leave_todate" id="leave_todate" value="<?= $_REQUEST['leave_todate'] ?>" class="form-control">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="leave_fromdate" id="leave_fromdate" value="<?= $_REQUEST['leave_fromdate'] ?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getleave();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="leave_year" id="leave_year" >
								<option value="">Select Year </option>
								<?php 
								$reg_year=date("Y","2017");
								$curr_year=date("Y");
								$current_date=date('Y-m-d');
								$adate1 = date('Y', strtotime($current_date));
								for ($i=$curr_year-$reg_year; $i>=0;$i--) 
								{
									?>
									<option <?php echo ($_REQUEST['leave_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="leave_month" id="leave_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) 
								{
									?>
									<option <?php echo (date("m", strtotime($month))==$_REQUEST['leave_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<?php
							/*if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
							{
								$selected_id = $_REQUEST['leave_sales_id'];
								$disabled = "";
							}
							else
							{
								$selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}*/
							$selected_id = $_REQUEST['leave_sales_id'];
							$salesWhere.="isDelete=0 AND isActive=1";
							?>
							<select class="form-control leave_sales_id" name="leave_sales_id" id="leave_sales_id" value="<?php echo $sales_id;?>" <?=$disabled?>>
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
						foreach($dashboard_main_array_leave as $arr_leave)
						{
							?>
							<div class="" style="padding: 10px;display: inline-block;vertical-align:top;/*margin-right:20px;*/white-space:normal;">
								<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_leave[0] ?> solid; width: 120px; height: 100px;">
									<a href="<?=  $arr_leave[3];  ?>" style="text-decoration: none;">         
										<div class="desc" style="text-align: center;">
											<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
					                    		<span data-counter="counterup" data-value="<?php echo $arr_leave[1]; ?>"> <?php echo $arr_leave[1]; ?> </span>
					               			</div>
					                		<strong><?php echo $arr_leave[2]; ?></strong>
										</div>
									</a>
								</div>
							</div>
							<?php
						}	
						?>
					</div>
					<div class="col-md-12 col-sm-12 co-xs-6 col-lg-12">
						<div class="portlet-body ">
							<div class="portlet-title">
								<div class="caption ">
									<br><br>
									<span class="caption-subject bold uppercase font-dark">leave Chart</span>
								</div>
							</div>
							<div id="leave" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	$("#leave_year").select2();
	$("#leave_month").select2();
	$(".leave_customer_id").select2();
	$(".leave_sales_id").select2();
	// jQuery(document).ready(function() {
	// 	graph_leave_pie.init_leave_pie();
	// 	// Graph_leave.init_leave();
	// });
	</script>
<?php
$db->disconnect(); 
?>