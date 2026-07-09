<?php 
$page_id=621;$page_slug='prospect_inquiry';
include("connect.php");
// if($_REQUEST['prospect_year'] =="")
// {
// 	$_REQUEST['prospect_year']=date("Y");
// }
$prospect_where.="isDelete=0 ";
if($_REQUEST['prospect_year'] != "" && $_REQUEST['todate'] =="" && $_REQUEST['fromdate'] =="")
{
	$prospect_where.="  AND  Year(inquiry_date)='".$_REQUEST['prospect_year']."' ";
}

if($_REQUEST['prospect_inquiry_created_by'] != ""  && $_REQUEST['prospect_inquiry_created_by']!="undefined")
{
	$prospect_where.="  AND  inquiry_created_by='".$_REQUEST['prospect_inquiry_created_by']."' ";
} 

if($_REQUEST['prospect_inquiry_assigned_to'] != "" && $_REQUEST['prospect_inquiry_assigned_to']!="undefined")
{
	$prospect_where.="  AND  inquiry_assign_to='".$_REQUEST['prospect_inquiry_assigned_to']."' ";
} 

if($_REQUEST['prospect_month'] != "" && $_REQUEST['prospect_month'] != "undefined")
{
	$prospect_where.="  AND  MONTH(datetime)='".$_REQUEST['prospect_month']."' ";
}

if($_REQUEST['todate'] !="" && $_REQUEST['fromdate'] !="" && $_REQUEST['prospect_month'] == "" && $_REQUEST['prospect_month'] != "undefined")
{
	$prospect_where.="  AND  DATE(datetime)>='".date('Y-m-d',strtotime($_REQUEST['todate']))."' AND  DATE(datetime)<='".date('Y-m-d',strtotime($_REQUEST['fromdate']))."' ";
	$_REQUEST['todate']=date('Y-m-d',strtotime($_REQUEST['todate']));
	$_REQUEST['fromdate']=date('Y-m-d',strtotime($_REQUEST['fromdate']));
}

// for link 
$prospect_link.="no_order_inquiry_manage.php?";

if($_REQUEST['prospect_year'] == "" || $_REQUEST['prospect_month'] == "")
{
	$prospect_link.="&prospect_year=".date("Y");
}

if($_REQUEST['prospect_month'] != "" && $_REQUEST['prospect_year'] == "")
{
	$prospect_link.="&inquiry_month=".$_REQUEST['prospect_month'];
}

if($_REQUEST['prospect_month'] != "" && $_REQUEST['prospect_year'] !="")
{
	$prospect_link.="&inquiry_month=".$_REQUEST['prospect_month']."&inquiry_year=".$_REQUEST['prospect_year'];
}

if($_REQUEST['prospect_inquiry_created_by'] != "")
{
	$prospect_link.="&inquiry_taken_by=".$_REQUEST['prospect_inquiry_created_by'];
}

if($_REQUEST['prospect_inquiry_assigned_to'] != "")
{
	$prospect_link.="&inquiry_assign_by=".$_REQUEST['prospect_inquiry_assigned_to'];
}

if($_REQUEST['todate'] != "")
{
	$prospect_link.="&todate=".$_REQUEST['todate'];
}

if($_REQUEST['fromdate'] != "")
{
	$prospect_link.="&fromdate=".$_REQUEST['fromdate'];
}

$direct_prospact = $db->rp_getTotalRecord("no_order_inquiry","isDelete = 0 AND inq_status = '-1' AND inquiry_lead_flag='-1' AND ".$prospect_where,0);

//$prospect_to_inquiry  = $db->rp_getTotalRecord("no_order_inquiry","isDelete = 0 AND (inq_status = 2) AND ".$prospect_where,0);
$prospect_to_inquiry  = 0;


if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	$dashboard_main_array_prospect = array(
		//0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where."  AND (inquiry_lead_flag = '-1' OR inq_status = 2)",0),"Total Prospect",$prospect_link."&type=-1",593,"="),
		
		0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where."  AND (inquiry_lead_flag = '-1' AND status IN (0,1,2,3,4,5,6,-1))",0),"Total Raw Data",$prospect_link."&type=-1",593,"="),
		
		// 1=>array("#7bd0a9",$direct_prospact,"Direct Raw Data","#",572,"+"),

		// 2=>array("#7bd0a9",$prospect_to_inquiry,"Raw Data To Inquiry","#",572,""),

		3=>array("#7bd0a9",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=0 AND (inquiry_lead_flag = '-1')",0),"Pending Raw Data",$prospect_link."&type=-1&status=0",572),

		4=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=1 AND (inquiry_lead_flag = '-1')",0),"In followup Raw Data",$prospect_link."&type=-1&status=1",572),

		//5=>array("#ff6347",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=4 AND (inquiry_lead_flag = '-1')",0),"Hot In followup Prospect",$prospect_link."&type=-1&status=4",572),

		//6=>array("#7cfc00",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=5 AND (inquiry_lead_flag = '-1')",0),"Cold In followup Prospect",$prospect_link."&type=-1&status=5",572),

		//7=>array("#fada5e",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=6 AND (inquiry_lead_flag = '-1')",0),"Warm In followup Prospect",$prospect_link."&type=-1&status=6",572),

		8=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=2 AND (inquiry_lead_flag = '-1')",0),"Positive Raw Data",$prospect_link."&type=-1&status=2",583),

		9=>array("#7bd0a9",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=4 AND (inquiry_lead_flag = '-1')",0),"Hot Raw Data",$prospect_link."&type=-1&status=4",583),

		10=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=5 AND (inquiry_lead_flag = '-1')",0),"Cold Raw Data",$prospect_link."&type=-1&status=5",583),

		11=>array("#ffa07a",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=6 AND (inquiry_lead_flag = '-1')",0),"Warm Raw Data",$prospect_link."&type=-1&status=6",583),
		
		12=>array("#ec9b97",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=-1 AND (inquiry_lead_flag = '-1')",0),"My Work Raw Data",$prospect_link."&type=-1&status=-1",583),

		13=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=3 AND (inquiry_lead_flag = '-1')",0),"Buy Later Lost Raw Data",$prospect_link."&type=-1&status=3",583),

		14=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=-2 AND (inquiry_lead_flag = '-1')",0),"Cancel Raw Data",$prospect_link."&type=-1&status=-2",583),

		15=>array("#ffa07a",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where." AND status=11 AND (inquiry_lead_flag = '-1')",0),"Lost Raw Data",$prospect_link."&type=-1&status=11",583),
	);
}
else
{
	// $prospect_where_sales=" AND ( inquiry_created_by = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ) ";

	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
	{ 
		if($rights['personal_flag']==1)
		{
			$prospect_where_sales .= " AND inquiry_created_by='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
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
					$prospect_where_sales .= " AND inquiry_created_by IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
					$salesWhere .= " id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";	
				}
				else
				{
					$prospect_where_sales .= " AND inquiry_created_by IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
					$salesWhere .= " id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";		
				}
			} 
		}
	}
	$direct_prospact = ($db->rp_getTotalRecord("no_order_inquiry","isDelete = 0 AND inq_status = '-1' AND inquiry_lead_flag='-1' AND ".$prospect_where.$prospect_where_sales,0));

	$prospect_to_inquiry  = 0;

	//$prospect_to_inquiry  = $db->rp_getTotalRecord("no_order_inquiry","isDelete = 0 AND (inq_status = 2) AND ".$prospect_where.$prospect_where_sales,0);
		
	$dashboard_main_array_prospect = array( 
		0=>array("black",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales."  AND (inquiry_lead_flag = '-1') AND status IN (0,1,2,3,4,5,6,-1)",0),"Total Raw Data",$prospect_link."&type=-1",593,"="),

		// 1=>array("#7bd0a9",$direct_prospact,"Direct Raw Data","#",572,"+"),

		// 2=>array("#7bd0a9",$prospect_to_inquiry,"Raw Data To Inquiry","#",572,""),

		3=>array("#7bd0a9",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=0 AND (inquiry_lead_flag = '-1')",0),"Pending Raw Data",$prospect_link."&type=-1&status=0",572),

		4=>array("#9fc1ff",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=1 AND (inquiry_lead_flag = '-1')",0),"In followup Raw Data",$prospect_link."&type=-1&status=1",572),

		//5=>array("#ff6347",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=4 AND (inquiry_lead_flag = '-1')",0),"Hot In followup Prospect",$prospect_link."&type=-1&status=4",572),

		//6=>array("#7cfc00",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=5 AND (inquiry_lead_flag = '-1')",0),"Cold In followup Prospect",$prospect_link."&type=-1&status=5",572),

		//7=>array("#fada5e",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=6 AND (inquiry_lead_flag = '-1')",0),"Warm In followup Prospect",$prospect_link."&type=-1&status=6",572),

		8=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=2 AND (inquiry_lead_flag = '-1')",0),"Positive Raw Data",$prospect_link."&type=-1&status=2",583),

		9=>array("#7bd0a9",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=4 AND (inquiry_lead_flag = '-1')",0),"Hot Raw Data",$prospect_link."&type=-1&status=4",583),

		10=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=5 AND (inquiry_lead_flag = '-1')",0),"Cold Raw Data",$prospect_link."&type=-1&status=5",583),

		11=>array("#ffa07a",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=6 AND (inquiry_lead_flag = '-1')",0),"Warm Raw Data",$prospect_link."&type=-1&status=6",583),
		
		12=>array("#ec9b97",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=-1 AND (inquiry_lead_flag = '-1')",0),"My Work Raw Data",$prospect_link."&type=-1&status=-1",583),

		13=>array("grey",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=3 AND (inquiry_lead_flag = '-1')",0),"Buy Later Lost Raw Data",$prospect_link."&type=-1&status=3",583),

		14=>array("#126608",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=-2 AND (inquiry_lead_flag = '-1')",0),"Cancel Raw Data",$prospect_link."&type=-1&status=-2",583),

		15=>array("#ffa07a",$db->rp_getTotalRecord("no_order_inquiry",$prospect_where.$prospect_where_sales." AND status=11 AND (inquiry_lead_flag = '-1')",0),"Lost Raw Data",$prospect_link."&type=-1&status=11",583),
	);
}
?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> Raw Data Statistic</span>
			</div>
			<div class="col-md-3" id="todate_div" >
				<label>To Date</label>
				<input type="date" name="todate" id="todate" value="<?= $_REQUEST['todate'] ?>" class="form-control">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="fromdate" id="fromdate" value="<?= $_REQUEST['fromdate'] ?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getprospect();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="prospect_year" id="prospect_year" >
								<option value="">Select Year </option>
								<?php 
								$reg_year=date("Y","2017");
								$curr_year=date("Y");
								$current_date=date('Y-m-d');
								$adate1 = date('Y', strtotime($current_date));
								for ($i=$curr_year-$reg_year; $i>=0;$i--) 
								{
									?>
									<option <?php echo ($_REQUEST['prospect_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="prospect_month" id="prospect_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) 
								{
									?>
									<option <?php echo (date("m", strtotime($month))==$_REQUEST['prospect_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<?php
							/*if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
							{
								$selected_id_create = $_REQUEST['prospect_inquiry_created_by'];
								$selected_id_assign = $_REQUEST['prospect_inquiry_assigned_to'];
								$disabled = "";
							}
							else
							{
								$selected_id_create=  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$selected_id_assign=  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}*/
							$selected_id_create2 = $_REQUEST['prospect_inquiry_created_by'];
							$selected_id_assign2 = $_REQUEST['prospect_inquiry_assigned_to'];
							$salesWhere.="isDelete=0 AND isActive=1";
							?>
							<select class="form-control" name="prospect_inquiry_created_by" id="prospect_inquiry_created_by" value="<?php echo $customer_id;?>" <?=$disabled?>>
								<option value="">Raw Data Created By</option>
								<?php 
								$sales_r=$db->rp_getData('sales_executive',"*",$salesWhere,"name ASC",0);
								while($sales_d=mysqli_fetch_assoc($sales_r))
								{
								?>
								<option <?php if($sales_d['id']==$selected_id_create2){?> selected <?php } ?>  value="<?php echo $sales_d['id']?>"><?php echo $sales_d['name']?></option>
								<?php
								}
								?>
							</select>
						</div> 
						<div class="col-sm-3">
							<select class="form-control" name="prospect_inquiry_assigned_to" id="prospect_inquiry_assigned_to" value="<?php echo $sales_id;?>" <?=$disabled?>>
								<option value="">Raw Data Assigned to</option>
								<?php 
								$sales_r1=$db->rp_getData('sales_executive',"*",$salesWhere,"name ASC",0);
								while($sales_d1=mysqli_fetch_assoc($sales_r1))
								{
								?>
								<option <?php if($sales_d1['id']==$selected_id_assign2){?> selected <?php } ?>  value="<?php echo $sales_d1['id']?>"><?php echo $sales_d1['name']?></option>
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
						foreach($dashboard_main_array_prospect as $arr_prospect)
						{
							?>
							<div class="" style="padding: 10px;display: inline-block;vertical-align:top;/*margin-right:20px;*/white-space:normal;">
								<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_prospect[0] ?> solid; width: 120px; height: 100px;">
									<a href="<?php echo $arr_prospect[3]; ?>" style="text-decoration: none;">
										<div class="desc" style="text-align: center;">
										   	<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
							                    <span data-counter="counterup" data-value="<?php echo $arr_prospect[1]; ?>"> <?php echo $arr_prospect[1]; ?> </span>
							               	</div>
							                <strong><?php echo $arr_prospect[2]; ?></strong>
							            </div>
									</a>
								</div>
							</div>
							<div style="display: inline-block; padding-top: 50px;"><strong style="font-size: 25px;"><?= $arr_prospect[5] ?></strong></div>
							<?php
						}	
						?>
					</div>
					<div class="col-md-12 col-sm-12 co-xs-6 col-lg-12">
						<div class="portlet-body ">
							<div class="portlet-title">
								<div class="caption ">
									<br><br>
									<span class="caption-subject bold uppercase font-dark">Raw Data Chart</span>
								</div>
							</div>
							<div id="prospect" class="CSSAnimationChart m-t-40" style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$("#prospect_year").select2();
		$("#prospect_month").select2();
		$("#prospect_inquiry_assigned_to").select2();
		$("#prospect_inquiry_created_by").select2();
	</script>
<?php
$db->disconnect(); 
?>