<?php 
$page_id=400;$page_slug='dashboard';
include("connect.php");
// $_REQUEST['expense_todate']=date('Y-m-d',strtotime($_REQUEST['expense_todate']));
// $_REQUEST['expense_fromdate']=date('Y-m-d',strtotime($_REQUEST['expense_fromdate']));
$date = date("m");
if($_REQUEST['expense_year'] =="")
{
	$_REQUEST['expense_year']=date("Y");
}

$expense_where="isDelete=0  AND isActive=1";

if($_REQUEST['expense_year'] != "")
{
	$expense_where.="  AND  Year(expense_date)='".$_REQUEST['expense_year']."'";
}

if($_REQUEST['expense_month'] != "")
{
	$expense_where.="  AND  MONTH(expense_date)='".$_REQUEST['expense_month']."'";
}

if($_REQUEST['expense_sales_id'] != "")
{
	$expense_where.="  AND  sales_executive_id='".$_REQUEST['expense_sales_id']."'";
} 

if($_REQUEST['expense_todate'] !="" && $_REQUEST['expense_fromdate'] !="" && $_REQUEST['expense_month'] == "")
{
	$expense_where.="  AND  DATE(expense_date)>='".date('Y-m-d',strtotime($_REQUEST['expense_todate']))."' AND  DATE(expense_date)<='".date('Y-m-d',strtotime($_REQUEST['expense_fromdate']))."'";
	$_REQUEST['expense_todate']=date('Y-m-d',strtotime($_REQUEST['expense_todate']));
	$_REQUEST['expense_fromdate']=date('Y-m-d',strtotime($_REQUEST['expense_fromdate']));
}

$expense_link.="expense_manage.php?";
if($_REQUEST['expense_year'] == "" || $_REQUEST['expense_month'] == "")
{
	$expense_link.="&expense_year=".date("Y");
}

if($_REQUEST['expense_month'] != "" && $_REQUEST['expense_year'] == "")
{
	$expense_link.="&expense_month=".$_REQUEST['expense_month'];
}

if($_REQUEST['expense_month'] != "" && $_REQUEST['expense_year'] !="")
{
	$expense_link.="&expense_month=".$_REQUEST['expense_month']."&expense_year=".$_REQUEST['expense_year'];
}

if($_REQUEST['expense_sales_id'] != "")
{
	$expense_link.="&sales_id=".$_REQUEST['expense_sales_id'];
}

if($_REQUEST['expense_todate'] != "")
{
	$expense_link.="&todate=".$_REQUEST['expense_todate'];
}

if($_REQUEST['expense_fromdate'] != "")
{
	$expense_link.="&fromdate=".$_REQUEST['expense_fromdate'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==13)
{
	$dashboard_main_array_expense = array(
		
		0=>array("black",$db->rp_getTotalRecord("expense",$expense_where,0),"Total Expense",$expense_link,593),

		1=>array("#7bd0a9",$db->rp_getTotalRecord("expense",$expense_where." AND expense_status=0 ",0),"Requested",$expense_link."&status=0",572),

		2=>array("#9fc1ff",$db->rp_getTotalRecord("expense",$expense_where." AND expense_status=1 ",0),"Pass",$expense_link."&status=1",572),

		3=>array("#9fc1ff",$db->rp_getTotalRecord("expense",$expense_where." AND expense_status=2 ",0),"Reject",$expense_link."&status=2",572),
	);
}
else
{
	$expense_where_sales=" AND sales_executive_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
	
	$dashboard_main_array_expense = array(
		
		0=>array("black",$db->rp_getTotalRecord("expense",$expense_where.$expense_where_sales,0),"Total Expense",$expense_link,593),

		1=>array("#7bd0a9",$db->rp_getTotalRecord("expense",$expense_where.$expense_where_sales." AND expense_status=0 ",0),"Requested",$expense_link."&status=0",572),

		2=>array("#9fc1ff",$db->rp_getTotalRecord("expense",$expense_where.$expense_where_sales." AND expense_status=1 ",0),"Pass",$expense_link."&status=1",572),
		
		3=>array("#9fc1ff",$db->rp_getTotalRecord("expense",$expense_where.$expense_where_sales." AND expense_status=2 ",0),"Reject",$expense_link."&status=2",572),
	);
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
// { ?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> Expense Statistic</span>
			</div>

			<div class="col-md-3" id="todate_div" >
				<label>To Date</label>
				<input type="date" name="expense_todate" id="expense_todate" value="<?= $_REQUEST['expense_todate'] ?>" class="form-control">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="expense_fromdate" id="expense_fromdate" value="<?= $_REQUEST['expense_fromdate'] ?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getexpense();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="expense_year" id="expense_year" >
								<option value="">Select Year </option>
								<?php 
								$reg_year=date("Y","2017");
								$curr_year=date("Y");
								$current_date=date('Y-m-d');
								$adate1 = date('Y', strtotime($current_date));
								for ($i=$curr_year-$reg_year; $i>=0;$i--) 
								{
									?>
										<option <?php echo ($_REQUEST['expense_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
										<?php
									}
									?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="expense_month" id="expense_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) {
								?>
									<option <?php echo  (date("m", strtotime($month))==$_REQUEST['expense_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==13)
							{
								$selected_id = $_REQUEST['expense_sales_id'];
								$disabled = "";
							}
							else
							{
								$selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}
							?>
							<select class="form-control expense_sales_id" name="expense_sales_id" id="expense_sales_id" value="<?php echo $sales_id;?>" <?= $disabled ?>>
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
						foreach($dashboard_main_array_expense as $arr_expense)
						{
							?>
							<div class="" style="padding: 10px;display: inline-block;vertical-align:top;/*margin-right:20px;*/white-space:normal;">
								<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_expense[0] ?> solid; width: 120px; height: 100px;">
							 		<a href="<?=  $arr_expense[3];  ?>" style="text-decoration: none;">         
										<div class="desc" style="text-align: center;">
										   	<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
							                	<span data-counter="counterup" data-value="<?php echo $arr_expense[1]; ?>"> <?php echo $arr_expense[1]; ?> </span>
							                </div>
							                <strong><?php echo $arr_expense[2]; ?></strong>
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
									<span class="caption-subject bold uppercase font-dark">Expense Chart</span>
									
								</div>
							</div>
								<div id="expense" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
								</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	$("#expense_year").select2();
	$("#expense_month").select2();
	$(".expense_customer_id").select2();
	$(".expense_sales_id").select2();
	// jQuery(document).ready(function() {
	// 	// Graph_expense.init_expense();
	// 	graph_expense_pie.init_expense_pie();
	// });
	</script>

<?php
$db->disconnect(); 
?>