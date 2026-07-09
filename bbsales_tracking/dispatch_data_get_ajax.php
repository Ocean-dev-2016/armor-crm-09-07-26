<?php 
$page_id=400;$page_slug='dashboard';
include("connect.php");

// $_REQUEST['dispatch_todate']=date('Y-m-d',strtotime($_REQUEST['dispatch_todate']));
// $_REQUEST['dispatch_fromdate']=date('Y-m-d',strtotime($_REQUEST['dispatch_fromdate']));

if($_REQUEST['dispatch_year'] =="")
{
	$_REQUEST['dispatch_year']=date("Y");
}
$dispatch_where="isDelete=0";

//$single_dispatch_amount = $db->rp_getValue("dispatch_detail","SUM(grand_total)","isDelete=0 GROUP by order_id having count(*) = 1",1);


//$multiple_dispatch = $db->rp_getValue("dispatch_detail","COUNT(id)","isDelete=0 GROUP by order_id having count(*) > 1",0);
//$multiple_dispatch_amount = $db->rp_getValue("dispatch_detail","SUM(grand_total)","isDelete=0 GROUP by order_id having count(*) > 1",0);

if($_REQUEST['dispatch_year'] != "")
{
	$dispatch_where.="  AND  Year(dispatch_date)='".$_REQUEST['dispatch_year']."'";
}

if($_REQUEST['dispatch_month'] != "")
{
	$dispatch_where.="  AND  MONTH(dispatch_date)='".$_REQUEST['dispatch_month']."'";
}

if($_REQUEST['dispatch_customer_id'] != "")
{
	$dispatch_where.="  AND  customer_id='".$_REQUEST['dispatch_customer_id']."'";
} 

if($_REQUEST['dispatch_sales_id'] != "")
{
	$dispatch_where.="  AND  sales_id='".$_REQUEST['dispatch_sales_id']."'";
} 

if($_REQUEST['dispatch_todate'] !="" && $_REQUEST['dispatch_fromdate'] !="" && $_REQUEST['dispatch_month'] == "")
{
	$dispatch_where.="  AND  DATE(dispatch_date)>='".date('Y-m-d',strtotime($_REQUEST['dispatch_todate']))."' AND  DATE(dispatch_date)<='".date('Y-m-d',strtotime($_REQUEST['dispatch_fromdate']))."'";
	$_REQUEST['dispatch_todate']=date('Y-m-d',strtotime($_REQUEST['dispatch_todate']));
	$_REQUEST['dispatch_fromdate']=date('Y-m-d',strtotime($_REQUEST['dispatch_fromdate']));

}

$query="SELECT SUM(count) FROM (SELECT COUNT(order_id) AS count FROM dispatch_detail WHERE ".$dispatch_where." GROUP BY order_id HAVING count > 1) as A ";
$result = mysqli_query($query);
$row = mysqli_fetch_array($result);

$single_dispatch = $db->rp_getTotalRecord("dispatch_detail",$dispatch_where."  GROUP by order_id having count(*) = 1",0);


$multiple_dispatch = $row[0];

if($row[0] == NULL)
{
	$multiple_dispatch='0';	
}
else
{

 	$multiple_dispatch= $row[0];
}


$total_dispatch = $db->rp_getTotalRecord("dispatch_detail",$dispatch_where,0);
$get_invoice_countR = "SELECT SUM(count) FROM (SELECT COUNT(dispatch_ids) AS count FROM invoice_new WHERE isDelete=0 GROUP BY dispatch_ids HAVING count = 1) as I";
$get_invoice_countd = mysqli_query($get_invoice_countR);
$row_user1 = mysqli_fetch_array($get_invoice_countd);
$dispatch_to_invoice = $row_user1[0];
if($total_dispatch!=0)
{
	if($row_user1[0] == NULL)
	{
		$dispatch_to_invoice='0';	
	}
	else
	{
		$dispatch_to_invoice= $row_user1[0];
	}	
}
else
{
	$dispatch_to_invoice= 0;
}


$total_dispatch = $db->rp_getTotalRecord("dispatch_detail",$dispatch_where,0);
//echo $total_dispatch; exit;
if($total_dispatch!=0)
{
	$pending_dispatch = ($dispatch_to_invoice-$total_dispatch);
}
else
{
	$pending_dispatch = 0;	
}

$dispatch_link.="dispatch_manage.php?";

if($_REQUEST['dispatch_month'] != "" && $_REQUEST['dispatch_year'] == "")
{
	$dispatch_link.="&dispatch_month=".$_REQUEST['dispatch_month'];
}

if($_REQUEST['dispatch_month'] != "" && $_REQUEST['dispatch_year'] !="")
{
	$dispatch_link.="&dispatch_month=".$_REQUEST['dispatch_month']."&dispatch_year=".$_REQUEST['dispatch_year'];
}

if($_REQUEST['dispatch_sales_id'] != "")
{
	$dispatch_link.="&sales_id=".$_REQUEST['dispatch_sales_id'];
}

if($_REQUEST['dispatch_customer_id'] != "")
{
	$dispatch_link.="&customer_id=".$_REQUEST['dispatch_customer_id'];
}

if($_REQUEST['dispatch_todate'] != "")
{
	$dispatch_link.="&todate=".$_REQUEST['dispatch_todate'];
}

if($_REQUEST['dispatch_fromdate'] != "")
{
	$dispatch_link.="&fromdate=".$_REQUEST['dispatch_fromdate'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==11)
{	
	$dashboard_main_array_dispatch = array(
		
		0=>array("black",$db->rp_getTotalRecord("dispatch_detail",$dispatch_where,0),"Total Dispatch Note","#",$db->rp_getValue("dispatch_detail","sum(grand_total)",$dispatch_where,0),$dispatch_link,"="),

		1=>array("#ffa07a",$single_dispatch,"Single Dispatch Note","#",$single_dispatch_amount,"#","+"),

		2=>array("#ffa07a",$multiple_dispatch,"Multiple Dispatch Note","#",$multiple_dispatch_amount,"#"),

		//3=>array("#7bd0a9",$db->rp_getTotalRecord("dispatch_detail",$dispatch_where." AND status=0 ",0),"Pending Dispatch Note","#",$db->rp_getValue("dispatch_detail","sum(grand_total)",$dispatch_where." AND status=0",0),$dispatch_link."&status=0"),
		
		3=>array("#7bd0a9",$pending_dispatch,"Pending Dispatch Note","#",$db->rp_getValue("dispatch_detail","sum(grand_total)",$dispatch_where." AND status=0",0),$dispatch_link."&status=0"),

		//4=>array("#9fc1ff",$db->rp_getTotalRecord("dispatch_detail",$dispatch_where." AND status=1 ",0),"Dispatch Note To Packing Slip","#",$db->rp_getValue("dispatch_detail","sum(grand_total)",$dispatch_where." AND status=1",0),$dispatch_link."&status=1"),
		
		4=>array("#9fc1ff",$dispatch_to_invoice,"Dispatch Note To Invoice","#",$db->rp_getValue("dispatch_detail","sum(grand_total)",$dispatch_where." AND status=1",0),$dispatch_link."&status=1"),

		//5=>array("#9fc1ff",$db->rp_getTotalRecord("dispatch_detail",$dispatch_where." AND status=2 ",0),"Packing Slip Created","#",$db->rp_getValue("dispatch_detail","sum(grand_total)",$dispatch_where." AND status=2",0),$dispatch_link."&status=2"),
	);
}
else
{
	$dispatch_where_sales=" AND sales_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";

	$single_dispatch_user = $db->rp_getTotalRecord("dispatch_detail",$dispatch_where.$dispatch_where_sales." GROUP by order_id having count(*) = 1",0);


	$query2="SELECT SUM(count) FROM (SELECT COUNT(order_id) AS count FROM dispatch_detail WHERE ".$dispatch_where.$dispatch_where_sales." GROUP BY order_id HAVING count > 1) as A";

	$result_user = mysqli_query($query2);
	
	$row_user = mysqli_fetch_array($result_user);
	
	$multiple_dispatch_user = $row_user[0];

	if($row[0] == NULL)
	{
		$multiple_dispatch_user='0';	
	}
	else
	{
		$multiple_dispatch_user= $row[0];
	}
	
	$dashboard_main_array_dispatch = array(
		
		0=>array("black",$db->rp_getTotalRecord("dispatch_detail",$dispatch_where.$dispatch_where_sales,0),"Total Dispatch Note","#",$db->rp_getValue("dispatch_detail","sum(grand_total)",$dispatch_where.$dispatch_where_sales,0),$dispatch_link,"="),

		1=>array("#ffa07a",$single_dispatch_user,"Single Dispatch Note","#",$single_dispatch_amount,"#","+"),

		2=>array("#ffa07a",$multiple_dispatch_user,"Multiple Dispatch Note","#",$multiple_dispatch_amount,"#"),

		3=>array("#7bd0a9",$db->rp_getTotalRecord("dispatch_detail",$dispatch_where.$dispatch_where_sales." AND status=0 ",0),"Pending Dispatch Note","#",$db->rp_getValue("dispatch_detail","sum(grand_total)",$dispatch_where.$dispatch_where_sales." AND status=0",0),$dispatch_link."&status=0"),

		4=>array("#9fc1ff",$db->rp_getTotalRecord("dispatch_detail",$dispatch_where.$dispatch_where_sales." AND status=2 ",0),"Dispatch Note To Packing Slip","#",$db->rp_getValue("dispatch_detail","sum(grand_total)",$dispatch_where.$dispatch_where_sales." AND status=2",0),$dispatch_link."&status=2"),
		
		//5=>array("#9fc1ff",$db->rp_getTotalRecord("dispatch_detail",$dispatch_where.$dispatch_where_sales." AND status=3 ",0),"Packing Slip Created","#",$db->rp_getValue("dispatch_detail","sum(grand_total)",$dispatch_where.$dispatch_where_sales." AND status=3",0),$dispatch_link."&status=3"),
	);
}

?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> Dispatch Statistic</span>
			</div>
			<div class="col-md-3" id="todate_div">
				<label>To Date</label>
				<input type="date" name="dispatch_todate" id="dispatch_todate" value="<?= $_REQUEST['dispatch_todate'] ?>" class="form-control">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="dispatch_fromdate" id="dispatch_fromdate" value="<?= $_REQUEST['dispatch_fromdate'] ?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return  getdispatch();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="dispatch_year" id="dispatch_year" >
								<option value="">Select Year </option>
								<?php 
									$reg_year=date("Y","2017");
									$curr_year=date("Y");
									$current_date=date('Y-m-d');
									$adate1 = date('Y', strtotime($current_date));
									for ($i=$curr_year-$reg_year; $i>=0;$i--) 
									{
										?>
										<option <?php echo ($_REQUEST['dispatch_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
										<?php
									}
									?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="dispatch_month" id="dispatch_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) {
								?>
									<option <?php echo (date("m", strtotime($month))==$_REQUEST['dispatch_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control dispatch_customer_id" name="customer_id" id="dispatch_customer_id" value="<?php echo $customer_id;?>">
								<option value="">Select Customer</option>
								<?php 
								$cus_r=$db->rp_getData('executive',"*","isDelete=0","id DESC",0);
								while($cus_d=mysqli_fetch_assoc($cus_r))
								{
									?>
									<option <?php if($cus_d['id']==$_REQUEST['dispatch_customer_id']){?> selected <?php } ?>  value="<?php echo $cus_d['id']?>"><?php echo $cus_d['company_name']?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==11)
							{
								$selected_id = $_REQUEST['dispatch_sales_id'];
								$disabled = "";
							}
							else
							{
								$selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}
							?>
							<select class="form-control dispatch_sales_id" name="sales_id" id="dispatch_sales_id" value="<?php echo $sales_id;?>" <?=  $disabled ?>>
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
					<div class="row horizontal-scrollable" style=" overflow: auto; white-space:nowrap;">
						<?php
						foreach($dashboard_main_array_dispatch as $arr_dispatch)
						{
							?>
							<div class="" style="padding: 10px; display: inline-block; vertical-align:top; white-space:normal;">
								<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_dispatch[0] ?> solid; width: 120px; height: 100px;">
									<a href="<?php echo $arr_dispatch[5]; ?>" style="text-decoration: none;">      
									<div class="desc" style="text-align: center;">
									   	<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
						                    <span data-counter="counterup" data-value="<?php echo $arr_dispatch[1]; ?>"> <?php echo $arr_dispatch[1]; ?> </span>
						               	</div>
						                <!-- <strong style="font-size: 10px;"><?php echo "&#2352; ".$db->rp_number_format($arr_dispatch[4],2); ?></strong> --><br/>
						                <strong><?php echo $arr_dispatch[2]; ?></strong>
									</div>
									</a>
								</div>
							</div>
							<div style="display: inline-block; padding-top: 50px;"><strong style="font-size: 25px;"><?= $arr_dispatch[6] ?></strong></div>
							<?php
						}	
						?>
					</div>	
					<div class="row" style="margin-top: 19px;"></div>
					<div class="col-md-12 col-sm-12 co-xs-6 col-lg-12">
						<div class="portlet-body ">
							<div class="portlet-title">
								<div class="caption">
									<br><br>
									<span class="caption-subject bold uppercase font-dark">Dispatch Chart</span>
								</div>
							</div>
							<div id="dispatch" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
	$("#dispatch_year").select2();
	$("#dispatch_month").select2();
	$(".dispatch_customer_id").select2();
	$(".dispatch_sales_id").select2();
	// jQuery(document).ready(function() {
	// 	// Graph_dispatch.init_dispatch();
	// 	graph_dispatch_pie.init_dispatch_pie();
	// });
	</script>
<?php
$db->disconnect(); 
?>