<?php   
$page_id=583;$page_slug='page_followup';
include("connect.php");

// $_REQUEST['followup_todate']=date('Y-m-d',strtotime($_REQUEST['followup_todate']));
// $_REQUEST['followup_fromdate']=date('Y-m-d',strtotime($_REQUEST['followup_fromdate']));

// if($_REQUEST['followup_year'] =="")
// {
// 	$_REQUEST['followup_year']=date("Y");
// }
if($_REQUEST['followup_year']=="" && $_REQUEST['followup_year'] =="undefined")
{
	$_REQUEST['followup_year']=date("Y");
}
$followup_where="isDelete=0";

$CID=array();
$SEID=array();
$sales_type_r=$db->rp_getData("sales_executive","*","type='service_executive'","",0);
while($sales_type_d = mysqli_fetch_array($sales_type_r))
{
    $SEID[] = $sales_type_d['id'];
}
$SEID=implode(",",$SEID);
$followup_where .=" AND user_id NOT IN ('".$SEID."')  ";

if($_REQUEST['followup_year'] != "")
{
	$followup_where.="  AND  Year(followup_date)='".$_REQUEST['followup_year']."'";
}
if($_REQUEST['followup_month'] != "")
{
	$followup_where.="  AND  MONTH(followup_date)='".$_REQUEST['followup_month']."'";
}

if($_REQUEST['followup_customer_id'] != "")
{
	$followup_where.="  AND  visitor_id='".$_REQUEST['followup_customer_id']."' ";
} 

if($_REQUEST['followup_sales_id'] != "")
{
	$followup_where.="  AND  user_id='".$_REQUEST['followup_sales_id']."'";
} 

if($_REQUEST['followup_todate'] !="" && $_REQUEST['followup_fromdate'] !="" && $_REQUEST['followup_month'] == "")
{
	$followup_where.="  AND  DATE(followup_date)>='".date('Y-m-d',strtotime($_REQUEST['followup_todate']))."' AND  DATE(followup_date)<='".date('Y-m-d',strtotime($_REQUEST['followup_fromdate']))."'";
	$_REQUEST['followup_todate']=date('Y-m-d',strtotime($_REQUEST['followup_todate']));
	$_REQUEST['followup_fromdate']=date('Y-m-d',strtotime($_REQUEST['followup_fromdate']));

}
$followup_link.="followuplist_manage.php?";

if($_REQUEST['followup_year'] == "" || $_REQUEST['followup_month'] == "")
{
	$followup_link.="&followup_year=".date("Y");
}

if($_REQUEST['followup_month'] != "" && $_REQUEST['followup_year'] == "")
{
	$followup_link.="&followup_month=".$_REQUEST['followup_month'];
}

if($_REQUEST['followup_month'] != "" && $_REQUEST['followup_year'] !="")
{
	$followup_link.="&followup_month=".$_REQUEST['followup_month']."&followup_year=".$_REQUEST['followup_year'];
}

if($_REQUEST['followup_sales_id'] != "")
{
	$followup_link.="&sales_id=".$_REQUEST['followup_sales_id'];
}

if($_REQUEST['followup_customer_id'] != "")
{
	$followup_link.="&customer_id=".$_REQUEST['followup_customer_id'];
}

if($_REQUEST['followup_todate'] != "")
{
	$followup_link.="&todate=".$_REQUEST['followup_todate'];
}

if($_REQUEST['followup_fromdate'] != "")
{
	$followup_link.="&fromdate=".$_REQUEST['followup_fromdate'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	$dashboard_main_array_followup = array(
		
		0=>array("black",$db->rp_getTotalRecord("followup",$followup_where,0),"Total Followup",$followup_link."&followup_type=all",593),

		1=>array("#7bd0a9",$db->rp_getTotalRecord("followup",$followup_where." AND DATE(followup_date)='".date('Y-m-d')."' AND response='' ",0),"Today's followup",$followup_link."&followup_type=today",572),

		2=>array("#9fc1ff",$db->rp_getTotalRecord("followup",$followup_where." AND  DATE(followup_date)>'".date('Y-m-d')."' AND response='' ",0),"Future followup",$followup_link."&followup_type=future",572),
		3=>array("#9fc1ff",$db->rp_getTotalRecord("followup",$followup_where." AND DATE(followup_date) < '".date('Y-m-d')."' AND response=''",0),"Pending followup",$followup_link."&followup_type=pending",572),
		// 4=>array("#9fc1ff",$db->rp_getTotalRecord("followup",$followup_where." AND  response!=''",0),"Responsed followup",$followup_link."&followup_type=responsed",572),
	);
}
else
{
	// $followup_where_sales=" AND user_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
	{ 
		if($rights['personal_flag']==1)
		{
			$followup_where_sales .= " AND user_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
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
					$followup_where_sales .= " AND user_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
					$salesWhere .= " id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";
				}
				else
				{
					$followup_where_sales .= " AND user_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
					$salesWhere .= " id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";			
				}
			} 
		}
	}  
	
	$dashboard_main_array_followup = array(
		
		0=>array("black",$db->rp_getTotalRecord("followup",$followup_where.$followup_where_sales,0),"Total Followup",$followup_link."&followup_type=all",593),

		1=>array("#7bd0a9",$db->rp_getTotalRecord("followup",$followup_where.$followup_where_sales." AND DATE(followup_date)='".date('Y-m-d')."' AND response='' ",0),"Today's followup",$followup_link."&followup_type=today",572),

		2=>array("#9fc1ff",$db->rp_getTotalRecord("followup",$followup_where.$followup_where_sales." AND  DATE(followup_date)>'".date('Y-m-d')."' AND response=''",0),"Future followup",$followup_link."&followup_type=future",572),
		
		3=>array("#9fc1ff",$db->rp_getTotalRecord("followup",$followup_where.$followup_where_sales." AND DATE(followup_date) < '".date('Y-m-d')."' AND response=''",0),"Pending followup",$followup_link."&status=2",572),

		// 4=>array("#9fc1ff",$db->rp_getTotalRecord("followup",$followup_where.$followup_where_sales."  AND response!=''",0),"Responsed followup",$followup_link."&status=2",572),
	);
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
// {
 ?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> followup Statistic</span>
			</div>
			<div class="col-md-3" id="todate_div" >
				<label>To Date</label>
				<input type="date" name="followup_todate" id="followup_todate" value="<?= $_REQUEST['followup_todate'] ?>" class="form-control">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="followup_fromdate" id="followup_fromdate" value="<?= $_REQUEST['followup_fromdate'] ?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getfollowup();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="followup_year" id="followup_year" >
								<option value="">Select Year </option>
								<?php 
								$reg_year=date("Y","2017");
								$curr_year=date("Y");
								$current_date=date('Y-m-d');
								$adate1 = date('Y', strtotime($current_date));
								for ($i=$curr_year-$reg_year; $i>=0;$i--) 
								{
									?>
									<option <?php echo ($_REQUEST['followup_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="followup_month" id="followup_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) 
								{
									?>
									<option <?php echo (date("m", strtotime($month))==$_REQUEST['followup_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control followup_customer_id" name="customer_id" id="followup_customer_id" value="<?php echo $customer_id;?>">
								<option value="">Select Customer</option>
								<?php 
								$cus_r=$db->rp_getData('executive',"*","isDelete=0","id DESC",0);
								while($cus_d=mysqli_fetch_assoc($cus_r))
								{
									?>
									<option <?php if($cus_d['id']==$_REQUEST['followup_customer_id']){?> selected <?php } ?>  value="<?php echo $cus_d['id']?>"><?php echo $cus_d['cname']." - ".$cus_d['company_name']?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==13)
							{
								// $selected_id = $_REQUEST['followup_sales_id'];
								// $disabled = "";
							}
							else
							{
								// $selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								// $disabled = "disabled";
							}
							// echo $salesWhere;
							$selected_id = $_REQUEST['followup_sales_id'];
							$salesWhere.="isDelete=0 AND isActive=1";
							// echo $salesWhere;
							?>
							<select class="form-control followup_sales_id" name="sales_id" id="followup_sales_id" value="<?php echo $sales_id;?>" <?=$disabled?>>
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
						foreach($dashboard_main_array_followup as $arr_followup)
						{
							?>
							<div class="" style="padding: 10px;display: inline-block;vertical-align:top;/*margin-right:20px;*/white-space:normal;">
								<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_followup[0] ?> solid; width: 120px; height: 100px;">
									<a href="<?=  $arr_followup[3];  ?>" style="text-decoration: none;">         
										<div class="desc" style="text-align: center;">
											<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
						                    	<span data-counter="counterup" data-value="<?php echo $arr_followup[1]; ?>"> <?php echo $arr_followup[1]; ?> </span>
						               		</div>
					                		<strong><?php echo $arr_followup[2]; ?></strong>
										</div>
									</a>
								</div>
							</div>
							<?php
						}	
						?>
					</div>
					<div class="row" style="margin-top: 19px"></div>
					<div class="col-md-12 col-sm-12 co-xs-6 col-lg-12">
						<div class="portlet-body ">
							<div class="portlet-title">
								<div class="caption ">
									<br><br>
									<span class="caption-subject bold uppercase font-dark">followup Chart</span>
								</div>
							</div>
							<div id="followup" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	$("#followup_year").select2();
	$("#followup_month").select2();
	$(".followup_customer_id").select2();
	$(".followup_sales_id").select2();
	// jQuery(document).ready(function() {
	// 	// Graph_followup.init_followup();
	// 	  graph_followup_pie.init_followup_pie();
	// });
	</script>
<?php
$db->disconnect(); 
?>