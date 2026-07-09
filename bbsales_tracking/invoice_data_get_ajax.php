<?php 
$page_id=400;$page_slug='dashboard';
include("connect.php");

// $_REQUEST['invoice_todate']=date('Y-m-d',strtotime($_REQUEST['invoice_todate']));
// $_REQUEST['invoice_fromdate']=date('Y-m-d',strtotime($_REQUEST['invoice_fromdate']));
$invoice_where="isDelete=0 AND status!=-1";

if($_REQUEST['invoice_year'] =="")
{
	$_REQUEST['invoice_year']=date("Y");
}

if($_REQUEST['invoice_year'] != "")
{
	$invoice_where.="  AND  Year(adate)='".$_REQUEST['invoice_year']."'";
}

if($_REQUEST['invoice_month'] != "")
{
	$invoice_where.="  AND  MONTH(adate)='".$_REQUEST['invoice_month']."'";
}

if($_REQUEST['invoice_customer_id'] != "")
{
	$invoice_where.="  AND  customer_id='".$_REQUEST['invoice_customer_id']."'";
} 

if($_REQUEST['invoice_sales_id'] != "")
{
	$invoice_where.="  AND  sales_id='".$_REQUEST['invoice_sales_id']."'";
} 

if($_REQUEST['invoice_todate'] !="" && $_REQUEST['invoice_fromdate'] !="" && $_REQUEST['invoice_month'] == "")
{
	$invoice_where.="  AND  DATE(adate)>='".date('Y-m-d',strtotime($_REQUEST['invoice_todate']))."' AND  DATE(adate)<='".date('Y-m-d',strtotime($_REQUEST['invoice_fromdate']))."'";
	$_REQUEST['invoice_todate']=date('Y-m-d',strtotime($_REQUEST['invoice_todate']));
	$_REQUEST['invoice_fromdate']=date('Y-m-d',strtotime($_REQUEST['invoice_fromdate']));
}
$invoice_link.="dealer_invoice_manage.php?";


if($_REQUEST['invoice_year'] == "" || $_REQUEST['invoice_month'] == "")
{
	$invoice_link.="&invoice_year=".date("Y");
}

if($_REQUEST['invoice_month'] != "" && $_REQUEST['invoice_year'] == "")
{
	$invoice_link.="&invoice_month=".$_REQUEST['invoice_month'];
}

if($_REQUEST['invoice_month'] != "" && $_REQUEST['invoice_year'] !="")
{
	$invoice_link.="&invoice_month=".$_REQUEST['invoice_month']."&invoice_year=".$_REQUEST['invoice_year'];
}

if($_REQUEST['invoice_sales_id'] != "")
{
	$invoice_link.="&sales_id=".$_REQUEST['invoice_sales_id'];
}

if($_REQUEST['invoice_customer_id'] != "")
{
	$invoice_link.="&customer_id=".$_REQUEST['invoice_customer_id'];
}

if($_REQUEST['invoice_todate'] != "")
{
	$invoice_link.="&todate=".$_REQUEST['invoice_todate'];
}

if($_REQUEST['invoice_fromdate'] != "")
{
	$invoice_link.="&fromdate=".$_REQUEST['invoice_fromdate'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 12)
{
	$dashboard_main_array_invoice_new = array(
		
		0=>array("black",$db->rp_getTotalRecord("invoice_new",$invoice_where,0),"Total Invoice","#",$db->rp_getValue("invoice_new","sum(taxable)",$invoice_where,0),$invoice_link),

		1=>array("#9fc1ff",$db->rp_getTotalRecord("invoice_new",$invoice_where." AND status=0 ",0),"Pending Invoice","#",$db->rp_getValue("invoice_new","sum(taxable)",$invoice_where." AND status=0",0),$invoice_link."&status=0"),

		2=>array("#7bd0a9",$db->rp_getTotalRecord("invoice_new",$invoice_where." AND status=1 ",0),"Approved Invoice","#",$db->rp_getValue("invoice_new","sum(taxable)",$invoice_where." AND status=1",0),$invoice_link."&status=1"),
		
		3=>array("#ec9b97",$db->rp_getTotalRecord("invoice_new",$invoice_where." AND status=3 ",0),"Cancelled Invoice","#",$db->rp_getValue("invoice_new","sum(taxable)",$invoice_where." AND status=3",0),$invoice_link."&status=3"),
		
		4=>array("#ffa07a",$db->rp_getTotalRecord("invoice_new",$invoice_where." AND status=-2 ",0),"Disapproved Invoice","#",$db->rp_getValue("invoice_new","sum(taxable)",$invoice_where." AND status=-2",0),$invoice_link."&status=-2"),
	);

}
else
{	
	$invoice_where_sales=" AND sales_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'";
	
	$dashboard_main_array_invoice_new = array(
				
		0=>array("black",$db->rp_getTotalRecord("invoice_new",$invoice_where.$invoice_where_sales,0),"Total Invoice","#",$db->rp_getValue("invoice_new","sum(taxable)",$invoice_where.$invoice_where_sales,0),$invoice_link),

		1=>array("#9fc1ff",$db->rp_getTotalRecord("invoice_new",$invoice_where.$invoice_where_sales." AND status=0 ",0),"Pending Invoice","#",$db->rp_getValue("invoice_new","sum(taxable)",$invoice_where.$invoice_where_sales." AND status=0",0),$invoice_link."&status=0"),

		2=>array("#7bd0a9",$db->rp_getTotalRecord("invoice_new",$invoice_where.$invoice_where_sales." AND status=1 ",0),"Approved Invoice","#",$db->rp_getValue("invoice_new","sum(taxable)",$invoice_where.$invoice_where_sales." AND status=1",0),$invoice_link."&status=1"),
		
		3=>array("#ec9b97",$db->rp_getTotalRecord("invoice_new",$invoice_where.$invoice_where_sales." AND status=3 ",0),"Cancelled Invoice","#",$db->rp_getValue("invoice_new","sum(taxable)",$invoice_where.$invoice_where_sales." AND status=3",0),$invoice_link."&status=3"),
		
		4=>array("#ffa07a",$db->rp_getTotalRecord("invoice_new",$invoice_where.$invoice_where_sales." AND status=-2 ",0),"Disapproved Invoice","#",$db->rp_getValue("invoice_new","sum(taxable)",$invoice_where.$invoice_where_sales." AND status=-2",0),$invoice_link."&status=-2"),
	);
}



// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
// { 
	?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> Invoice Statistic</span>
			</div>
			<div class="col-md-3" id="todate_div" >
				<label>To Date</label>
				<input type="date" name="invoice_todate" id="invoice_todate" value="<?= $_REQUEST['invoice_todate'] ?>" class="form-control">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="invoice_fromdate" id="invoice_fromdate" value="<?= $_REQUEST['invoice_fromdate'] ?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getinvoice();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="invoice_year" id="invoice_year" >
								<option value="">Select Year </option>
								<?php 
								$reg_year=date("Y","2017");
								$curr_year=date("Y");
								$current_date=date('Y-m-d');
								$adate1 = date('Y', strtotime($current_date));
								for ($i=$curr_year-$reg_year; $i>=0;$i--) 
								{
									?>
									<option <?php echo ($_REQUEST['invoice_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="invoice_month" id="invoice_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) 
								{
									?>
									<option <?php echo (date("m", strtotime($month))==$_REQUEST['invoice_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control invoice_customer_id" name="customer_id" id="invoice_customer_id" value="<?php echo $customer_id;?>">
								<option value="">Select Customer</option>
								<?php 
								$cus_r=$db->rp_getData('executive',"*","isDelete=0","id DESC",0);
								while($cus_d=mysqli_fetch_assoc($cus_r))
								{
									?>
									<option <?php if($cus_d['id']==$_REQUEST['invoice_customer_id']){?> selected <?php } ?>  value="<?php echo $cus_d['id']?>"><?php echo $cus_d['company_name']?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] == 12)
							{
								$selected_id = $_REQUEST['invoice_sales_id'];
								$disabled = "";
							}
							else
							{
								$selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}
							?>
							<select class="form-control invoice_sales_id" name="sales_id" id="invoice_sales_id" value="<?php echo $sales_id;?>" <?=$disabled?>>
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
					foreach($dashboard_main_array_invoice_new as $arr_invoice_new)
					{
						?>
						<div class="" style="padding: 10px;display: inline-block;vertical-align:top;white-space:normal;">
							<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_invoice_new[0] ?> solid; width: 120px; height: 100px; text-align: center;">
								<a href="<?php echo $arr_invoice_new[5]; ?> " style="text-decoration: none;">    
									<div class="desc" style="text-align: center;">
									   	<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
						                	<span data-counter="counterup" data-value="<?php echo $arr_invoice_new[1]; ?>"> <?php echo $arr_invoice_new[1]; ?> </span>
						              	</div>
						                <strong style="font-size: 11px;"><?php echo "&#2352; ".$db->rp_number_format($arr_invoice_new[4]) ?></strong><br/>
						            </div>
								</a> 
								<strong ><?php echo $arr_invoice_new[2]; ?></strong>
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
									<span class="caption-subject bold uppercase font-dark">Invoice Chart</span>
								</div>
							</div>
							<div id="invoice" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	$("#invoice_year").select2();
	$("#invoice_month").select2();
	$(".invoice_customer_id").select2();
	$(".invoice_sales_id").select2();
	// jQuery(document).ready(function() {
	// 	// Graph_invoice.init_invoice();
	// 	graph_invoice_pie.init_invoice_pie();
	// });
	</script>
<?php
$db->disconnect(); 
?>