<?php 
$page_id=400;$page_slug='dashboard';
include("connect.php");
// $_REQUEST['complain_todate']=date('Y-m-d',strtotime($_REQUEST['complain_todate']));
// $_REQUEST['complain_fromdate']=date('Y-m-d',strtotime($_REQUEST['complain_fromdate']));

if($_REQUEST['complain_year'] =="")
{
	$_REQUEST['complain_year']=date("Y");
}

$complain_where="isDelete=0";
if($_REQUEST['complain_year'] != "")
{
	$complain_where.="  AND  Year(complain_date)='".$_REQUEST['complain_year']."'";
}

if($_REQUEST['complain_month'] != "")
{
	$complain_where.="  AND  MONTH(complain_date)='".$_REQUEST['complain_month']."'";
}

if($_REQUEST['complain_customer_id'] != "")
{
	$complain_where.="  AND  customer_id='".$_REQUEST['complain_customer_id']."'";
} 

if($_REQUEST['complain_sales_id'] != "")
{
	$complain_where.="  AND  user_id='".$_REQUEST['complain_sales_id']."'";
} 

if($_REQUEST['complain_todate'] !="" && $_REQUEST['complain_fromdate'] !="" && $_REQUEST['complain_month'] == "")
{
	$complain_where.="  AND  DATE(complain_date)>='".date('Y-m-d',strtotime($_REQUEST['complain_todate']))."' AND  DATE(complain_date)<='".date('Y-m-d',strtotime($_REQUEST['complain_fromdate']))."'";
	$_REQUEST['complain_todate']=date('Y-m-d',strtotime($_REQUEST['complain_todate']));
	$_REQUEST['complain_fromdate']=date('Y-m-d',strtotime($_REQUEST['complain_fromdate']));
}

$complain_link.="manage_complain.php?";

if($_REQUEST['complain_year'] == "" || $_REQUEST['complain_month'] == "")
{
	$complain_link.="&complain_year=".date("Y");
}

if($_REQUEST['complain_month'] != "" && $_REQUEST['complain_year'] == "")
{
	$complain_link.="&complain_month=".$_REQUEST['complain_month'];
}

if($_REQUEST['complain_month'] != "" && $_REQUEST['complain_year'] !="")
{
	$complain_link.="&complain_month=".$_REQUEST['complain_month']."&complain_year=".$_REQUEST['complain_year'];
}

if($_REQUEST['complain_sales_id'] != "")
{
	$complain_link.="&sales_id=".$_REQUEST['complain_sales_id'];
}

if($_REQUEST['complain_customer_id'] != "")
{
	$complain_link.="&customer_id=".$_REQUEST['complain_customer_id'];
}

if($_REQUEST['complain_todate'] != "")
{
	$complain_link.="&todate=".$_REQUEST['complain_todate'];
}

if($_REQUEST['complain_fromdate'] != "")
{
	$complain_link.="&fromdate=".$_REQUEST['complain_fromdate'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{

	$dashboard_main_array_complain = array(
		0=>array("black",$db->rp_getTotalRecord("complain",$complain_where,0),"Total Complain",$complain_link,593),
		1=>array("#7bd0a9",$db->rp_getTotalRecord("complain",$complain_where." AND status=0 ",0),"Generated",$complain_link."&status=0",572),
		2=>array("#9fc1ff",$db->rp_getTotalRecord("complain",$complain_where." AND status=1 ",0),"In process",$complain_link."&status=1",572),
		3=>array("#9fc1ff",$db->rp_getTotalRecord("complain",$complain_where." AND status=2 ",0),"Complete",$complain_link."&status=2",572),
		4=>array("#9fc1ff",$db->rp_getTotalRecord("complain",$complain_where." AND status=-1 ",0),"Reject",$complain_link."&status=-1",572),
		5=>array("#9fc1ff",$db->rp_getTotalRecord("complain",$complain_where." AND status=-2 ",0),"Not Done",$complain_link."&status=-2",572),
	);
}
else
{

	$complain_where_sales=" AND user_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
	
	$dashboard_main_array_complain = array(
		0=>array("black",$db->rp_getTotalRecord("complain",$complain_where.$complain_where_sales,0),"Total Complain",$complain_link,593),
		1=>array("#7bd0a9",$db->rp_getTotalRecord("complain",$complain_where.$complain_where_sales." AND status=0 ",0),"Generated",$complain_link."&status=0",572),
		2=>array("#9fc1ff",$db->rp_getTotalRecord("complain",$complain_where.$complain_where_sales." AND status=1 ",0),"In process",$complain_link."&status=1",572),
		3=>array("#9fc1ff",$db->rp_getTotalRecord("complain",$complain_where.$complain_where_sales." AND status=2 ",0),"Complete",$complain_link."&status=2",572),
		4=>array("#9fc1ff",$db->rp_getTotalRecord("complain",$complain_where.$complain_where_sales." AND status=-1 ",0),"Reject",$complain_link."&status=-1",572),
		5=>array("#9fc1ff",$db->rp_getTotalRecord("complain",$complain_where.$complain_where_sales." AND status=-2 ",0),"Not Done",$complain_link."&status=-2",572),
	);
}
?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> Complain Statistic</span>
			</div>
			<div class="col-md-3" id="todate_div" >
				<label>To Date</label>
				<input type="date" name="complain_todate" id="complain_todate" value="<?= $_REQUEST['complain_todate'] ?>" class="form-control">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="complain_fromdate" id="complain_fromdate" value="<?= $_REQUEST['complain_fromdate'] ?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getcomplain();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="complain_year" id="complain_year" >
								<option value="">Select Year </option>
								<?php 
								$reg_year=date("Y","2017");
								$curr_year=date("Y");
								$current_date=date('Y-m-d');
								$adate1 = date('Y', strtotime($current_date));
								for ($i=$curr_year-$reg_year; $i>=0;$i--) 
								{
									?>
									<option <?php echo ($_REQUEST['complain_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="complain_month" id="complain_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) 
								{
									?>
									<option <?php echo (date("m", strtotime($month))==$_REQUEST['complain_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control complain_customer_id" name="customer_id" id="complain_customer_id" value="<?php echo $customer_id;?>">
								<option value="">Select Customer</option>
								<?php 
								$cus_r=$db->rp_getData('executive',"*","isDelete=0","id DESC",0);
								while($cus_d=mysqli_fetch_assoc($cus_r))
								{
									?>
									<option <?php if($cus_d['id']==$_REQUEST['complain_customer_id']){?> selected <?php } ?>  value="<?php echo $cus_d['id']?>"><?php echo $cus_d['cname']?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
							{
								$selected_id = $_REQUEST['complain_sales_id'];
								$disabled = "";
							}
							else
							{
								$selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}
							?>
							<select class="form-control complain_sales_id" name="sales_id" id="complain_sales_id" value="<?php echo $sales_id;?>" <?=$disabled?>>
								<option value="">Select Executive</option>
								<?php 
								$sales_r=$db->rp_getData('sales_executive',"*","isDelete=0","name ASC",0);
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
						foreach($dashboard_main_array_complain as $arr_complain)
						{
							?>
							<div class="" style="padding: 10px;display: inline-block;vertical-align:top;white-space:normal;">
								<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_complain[0] ?> solid; width: 120px; height: 100px;">
									<a href="<?=  $arr_complain[3];  ?>" style="text-decoration: none;">         
										<div class="desc" style="text-align: center;">
											<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
							                    <span data-counter="counterup" data-value="<?php echo $arr_complain[1]; ?>"> <?php echo $arr_complain[1]; ?> </span>
							               	</div>
					                		<strong><?php echo $arr_complain[2]; ?></strong>
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
									<span class="caption-subject bold uppercase font-dark">Complain Chart</span>
								</div>
							</div>
							<div id="complain" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div>
				</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
		$("#complain_year").select2();
		$("#complain_month").select2();
		$(".complain_customer_id").select2();
		$(".complain_sales_id").select2();
		// jQuery(document).ready(function() {
  //   		// Graph_complain.init_complain();
  //   		  graph_complain_pie.init_complain_pie();
		// });
		</script>

<?php

?>
<?php require_once 'disconnect.php';  ?>