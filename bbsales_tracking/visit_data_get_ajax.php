<?php 
$page_id=577;$page_slug='visit_page';
include("connect.php");
// $_REQUEST['visit_todate']=date('Y-m-d',strtotime($_REQUEST['visit_todate']));
// $_REQUEST['visit_fromdate']=date('Y-m-d',strtotime($_REQUEST['visit_fromdate']));

if($_REQUEST['visit_year'] =="")
{
	$_REQUEST['visit_year']=date("Y");
}

$visit_where="isDelete=0";

if($_REQUEST['visit_year'] != "")
{
	$visit_where.="  AND  Year(created_date)='".$_REQUEST['visit_year']."'";
}

if($_REQUEST['visit_month'] != "")
{
	$visit_where.="  AND  MONTH(created_date)='".$_REQUEST['visit_month']."'";
}

if($_REQUEST['visit_customer_id'] != "")
{
	$visit_where.="  AND  customer_id='".$_REQUEST['visit_customer_id']."'";
} 

if($_REQUEST['visit_sales_id'] != "")
{
	$visit_where.="  AND  user_id='".$_REQUEST['visit_sales_id']."'";
} 

if($_REQUEST['visit_todate'] !="" && $_REQUEST['visit_fromdate'] !="" && $_REQUEST['visit_month'] == "")
{
	$visit_where.="  AND  DATE(created_date)>='".date('Y-m-d',strtotime($_REQUEST['visit_todate']))."' AND  DATE(created_date)<='".date('Y-m-d',strtotime($_REQUEST['visit_fromdate']))."'";
	$_REQUEST['visit_todate']=date('Y-m-d',strtotime($_REQUEST['visit_todate']));
	$_REQUEST['visit_fromdate']=date('Y-m-d',strtotime($_REQUEST['visit_fromdate']));
}

$visit_link.="visit_manage.php?";

if($_REQUEST['visit_year'] == "" || $_REQUEST['visit_month'] == "")
{
	$visit_link.="&visit_year=".date("Y");
}

if($_REQUEST['visit_month'] != "" && $_REQUEST['visit_year'] == "")
{
	$visit_link.="&visit_month=".$_REQUEST['visit_month'];
}

if($_REQUEST['visit_month'] != "" && $_REQUEST['visit_year'] !="")
{
	$visit_link.="&visit_month=".$_REQUEST['visit_month']."&visit_year=".$_REQUEST['visit_year'];
}

if($_REQUEST['visit_sales_id'] != "")
{
	$visit_link.="&sales_id=".$_REQUEST['visit_sales_id'];
}

if($_REQUEST['visit_customer_id'] != "")
{
	$visit_link.="&customer_id=".$_REQUEST['visit_customer_id'];
}

if($_REQUEST['visit_todate'] != "")
{
	$visit_link.="&todate=".$_REQUEST['visit_todate'];
}

if($_REQUEST['visit_fromdate'] != "")
{
	$visit_link.="&fromdate=".$_REQUEST['visit_fromdate'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0) 
{
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
	{ 
		if($rights['personal_flag']==1)
		{ 
			$visit_where .= " AND user_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
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
					$visit_where .= " AND user_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
					$salesWhere .= "id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";	
				}
				else
				{
					$visit_where .= " AND user_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
					$salesWhere .= " id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].") AND ";		
				}
			} 
		}
	}  
}
$dashboard_main_array_visit = array(
	0=>array("black",$db->rp_getTotalRecord("visit",$visit_where,0),"Total Visit",$visit_link),
);
?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> Visit Statistic</span>
			</div>
			<div class="col-md-3" id="todate_div" >
				<label>To Date</label>
				<input type="date" name="visit_todate" id="visit_todate" value="<?= $_REQUEST['visit_todate'] ?>" class="form-control">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="visit_fromdate" id="visit_fromdate" value="<?= $_REQUEST['visit_fromdate'] ?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getvisit();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="visit_year" id="visit_year" >
								<option value="">Select Year </option>
									<?php 
									$reg_year=date("Y","2017");
									$curr_year=date("Y");
									$current_date=date('Y-m-d');
									$adate1 = date('Y', strtotime($current_date));
									for ($i=$curr_year-$reg_year; $i>=0;$i--) {
									?>
									<option <?php echo ($_REQUEST['visit_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
									<?php
									}
									?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="visit_month" id="visit_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) {
								?>
								<option <?php echo (date("m", strtotime($month))==$_REQUEST['visit_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
								<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control visit_customer_id" name="visit_customer_id" id="visit_customer_id" value="<?php echo $customer_id;?>">
								<option value="">Select Customer</option>
								<?php 
								$cus_r=$db->rp_getData('executive',"*","isDelete=0","id DESC",0);
								while($cus_d=mysqli_fetch_assoc($cus_r))
								{
								?>
								<option <?php if($cus_d['id']==$_REQUEST['visit_customer_id']){?> selected <?php } ?>  value="<?php echo $cus_d['id']?>"><?php echo $cus_d['company_name']?> - <?php echo $cus_d['cname']?></option>
								<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==13)
							{
								// $selected_id = $_REQUEST['visit_sales_id'];
								// $disabled = "";
							}
							else
							{
								// $selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								// $disabled = "disabled";
							}
							// echo $salesWhere;
							$selected_id = $_REQUEST['visit_sales_id'];
							$salesWhere.="isDelete=0 AND isActive=1";
							// echo $salesWhere;
							?>
							<select class="form-control visit_sales_id" name="sales_id" id="visit_sales_id" value="<?php echo $sales_id;?>" <?=$disabled?>>
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
					<div class="row horizontal-scrollable" style=" overflow: auto; white-space:nowrap;">
						<?php
						foreach($dashboard_main_array_visit as $arr_visit)
						{
							?>
							<div class="" style="padding: 10px; display: inline-block; vertical-align:top; white-space:normal;">
								<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_visit[0] ?> solid; width: 120px; height: 100px;">
									 <a href="<?php echo $arr_visit[3]; ?>" style="text-decoration: none;">      
									<div class="desc" style="text-align: center;">
									   
									  	<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
						                    <span data-counter="counterup" data-value="<?php echo $arr_visit[1]; ?>"> <?php echo $arr_visit[1]; ?> </span>
						               </div>
						               <strong><?php echo $arr_visit[2]; ?></strong>
									</div>
									</a>
								</div>
							</div>
							<?php
						}	
						?>
					</div>
					<div class="row" style="margin-top: 19px;"></div>
					<div class="col-md-12 col-sm-12 co-xs-6 col-lg-12">
						<div class="portlet-body ">
							<div class="portlet-title">
								<div class="caption ">
									<br><br>
									<span class="caption-subject bold uppercase font-dark">Visit Chart</span>
								</div>
							</div>
							<div id="visit" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	$("#visit_year").select2();
	$("#visit_month").select2();
	$(".visit_customer_id").select2();
	$(".visit_sales_id").select2();
	// jQuery(document).ready(function() {
	// 	// Graph_visit.init_visit();
	// 	graph_visit_pie.init_visit_pie();
	// });
	</script>
<?php require_once 'disconnect.php';  ?>