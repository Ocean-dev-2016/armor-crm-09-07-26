<?php 
$page_id=400;$page_slug='dashboard';
include("connect.php");

// print_r($_REQUEST);exit;

// if($_REQUEST['order_year'] =="")
// {
// 	$_REQUEST['order_year']=date("Y");
// }

if($_REQUEST['order_year']=="" && $_REQUEST['order_year'] =="undefined")
{
	$_REQUEST['order_year']=date("Y");
}
$order_where="isDelete=0";

if($_REQUEST['order_year']!= "" && $_REQUEST['order_year'] != "undefined")
{
	$order_where.="  AND  Year(order_date)='".$_REQUEST['order_year']."'";
}

if($_REQUEST['order_month']!= "" && $_REQUEST['order_month'] != "undefined")
{
	$order_where.="  AND  MONTH(order_date)='".$_REQUEST['order_month']."'";
}

if($_REQUEST['order_customer_id']!= "" && $_REQUEST['order_customer_id'] != "undefined") 
{
	$order_where.="  AND  customer_id='".$_REQUEST['order_customer_id']."'";
} 

if($_REQUEST['order_sales_id']!= "" && $_REQUEST['order_sales_id']!= "undefined")
{
	$order_where.="  AND  sales_id='".$_REQUEST['order_sales_id']."'";
} 

if($_REQUEST['order_todate']!="" && $_REQUEST['order_fromdate']!="" && $_REQUEST['order_fromdate']!="undefined" && $_REQUEST['order_todate']!="undefined")
{
	$order_where.="  AND  DATE(order_date)>='".date('Y-m-d',strtotime($_REQUEST['order_todate']))."' AND  DATE(order_date)<='".date('Y-m-d',strtotime($_REQUEST['order_fromdate']))."'";
	$order_todate=date('Y-m-d',strtotime($_REQUEST['order_todate']));
	$order_fromdate=date('Y-m-d',strtotime($_REQUEST['order_fromdate']));
}
// if($_REQUEST['order_todate'] !="" && $_REQUEST['order_fromdate'] !="" && $_REQUEST['order_fromdate'] !="undefined" && $_REQUEST['order_todate'] !="undefined")
// {
// 	$attendance_where.="  AND  DATE(date_time)>='".date('Y-m-d',strtotime($_REQUEST['attendance_todate']))."' AND  DATE(date_time)<='".date('Y-m-d',strtotime($_REQUEST['attendance_fromdate']))."'";
// 	$_REQUEST['attendance_todate']=date('Y-m-d',strtotime($_REQUEST['attendance_todate']));
// 	$_REQUEST['attendance_fromdate']=date('Y-m-d',strtotime($_REQUEST['attendance_fromdate']));
// }

/*$customer_r=$db->rp_getData("executive","id,isDelete,isActive"," isActive=1 ","",0);
$customer_ids=array();
while($customer_d=mysqli_fetch_assoc($customer_r))
{
	$customer_ids[]=$customer_d['id'];
}
$customer_ids = implode(" , ", $customer_ids);
$order_where.="  AND  customer_id IN (".$customer_ids.") ";*/

/*$quotation_to_order = $db->rp_getTotalRecord("orders","quotation_id!='0' AND ".$order_where,0);
$quotation_to_order_amount = $db->rp_getValue("orders","sum(grand_total)","isDelete=0 AND quotation_id!=0");*/

$query_1="SELECT SUM(count) FROM (SELECT COUNT(quotation_id) AS count FROM orders WHERE ".$order_where."AND quotation_id!='0' GROUP BY quotation_id HAVING count = 1) as A";
$result1 = mysqli_query($query_1);
$row1 = mysqli_fetch_array($result1);
$quotation_to_order = $row1[0];

$query_3="SELECT SUM(total),SUM(count) FROM (SELECT COUNT(quotation_id) AS count,SUM(grand_total) AS total FROM orders WHERE ".$order_where." AND quotation_id!='0' GROUP BY quotation_id HAVING count = 1) as A";
$result2 = mysqli_query($query_3);
$row2 = mysqli_fetch_array($result2);
$quotation_to_order_amount= $row2 [0];

$direct_order = $db->rp_getValue("orders","COUNT(quotation_id)",$order_where." AND quotation_id='0' AND status!='-1'",0);
$direct_order_amount = $db->rp_getValue("orders","sum(grand_total)",$order_where." AND quotation_id='0' ");

$query="SELECT SUM(count) FROM (SELECT COUNT(quotation_id) AS count FROM orders WHERE ".$order_where." AND quotation_id!='0' GROUP BY quotation_id HAVING count > 1) as B";
$result = mysqli_query($query);
$row = mysqli_fetch_array($result);
if($row[0] == NULL)
{
	$single_quotation_to_multiple_order='0';	
}
else
{
	$single_quotation_to_multiple_order= $row[0];
}

$query_2="SELECT SUM(total),SUM(count) FROM (SELECT COUNT(quotation_id) AS count,SUM(grand_total) AS total FROM orders WHERE ".$order_where." AND quotation_id!='0' GROUP BY quotation_id HAVING count > 1) as B";
$result3 = mysqli_query($query_2);
$row3 = mysqli_fetch_array($result3);
if($row3[0] == NULL)
{
	$single_quotation_to_multiple_order_total='0';	
}
else
{
	$single_quotation_to_multiple_order_total= $row3[0];
}

$order_link.="dealer_orders_manage.php?";

if($_REQUEST['order_year'] == "" || $_REQUEST['order_month'] == "")
{
	$order_link.="&order_year=".date("Y");
}

if($_REQUEST['order_month'] != "" && $_REQUEST['order_month'] != "undefined" && $_REQUEST['order_year'] == "" && $_REQUEST['order_year'] == "undefined")
{
	$order_link.="&order_month=".$_REQUEST['order_month'];
}

if($_REQUEST['order_month'] != "" && $_REQUEST['order_year'] !="" && $_REQUEST['order_month'] != "undefined" && $_REQUEST['order_year'] != "undefined")
{
	$order_link.="&order_month=".$_REQUEST['order_month']."&order_year=".$_REQUEST['order_year'];
}

if($_REQUEST['order_sales_id'] != "" && $_REQUEST['order_sales_id'] != "undefined")
{
	$order_link.="&sales_id=".$_REQUEST['order_sales_id'];
}

if($_REQUEST['order_customer_id'] != "" && $_REQUEST['order_customer_id'] != "undefined")
{
	$order_link.="&customer_id=".$_REQUEST['order_customer_id'];
}

if($_REQUEST['order_todate'] != "" && $_REQUEST['order_todate'] != "undefined")
{
	$order_link.="&todate=".$_REQUEST['order_todate'];
}
if($_REQUEST['order_fromdate'] != "" && $_REQUEST['order_fromdate'] != "undefined")
{
	$order_link.="&fromdate=".$_REQUEST['order_fromdate'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==11 )
{
	$dashboard_main_array_order = array(
			
		/*0=>array("black",$db->rp_getTotalRecord("orders",$order_where." AND status!=-1",0),"Total","#",$db->rp_getValue("orders","sum(grand_total)",$order_where." AND status!=-1",0),$order_link),*/
		
		// 0=>array("black",$db->rp_getTotalRecord("orders",$order_where." AND status!=-1",0),"Total Order","#",$db->rp_getValue("orders","sum(grand_total)",$order_where." AND status!=-1",0),$order_link,"="),

		0=>array("black",$db->rp_getTotalRecord("orders",$order_where." AND status!=-1",0),"Total Order","#","&#2352; ".$db->rp_number_format($db->rp_getValue("orders","sum(grand_total)",$order_where." AND status!=-1",0),2),$order_link,"="),

		// 1=>array("#ffa07a",$quotation_to_order,"Quotation To Order","#","&#2352; ".$db->rp_number_format($quotation_to_order_amount,2),"#","+"),

		// 2=>array("#ffa07a",$single_quotation_to_multiple_order,"Quotation To Multiple Order","#","&#2352; ".$db->rp_number_format($single_quotation_to_multiple_order_total,2),"#","+"),

		// 3=>array("#ffa07a",$direct_order,"Direct Order","#","&#2352; ".$db->rp_number_format($direct_order_amount,2),"#"),

		4=>array("#9fc1ff",$db->rp_getTotalRecord("orders",$order_where." AND status=0 ",0),"Pending Order","#","&#2352; ".$db->rp_number_format($db->rp_getValue("orders","sum(grand_total)",$order_where." AND status=0",0),2),$order_link."&status=0"),

		// 5=>array("grey",$db->rp_getTotalRecord("orders",$order_where." AND status=4 ",0),"Partial Dispatch Order","#","",$order_link."&status=4"),
		
		// 6=>array("#126608",$db->rp_getTotalRecord("orders",$order_where." AND status=2 ",0),"Full Dispatch Order","#","",$order_link."&status=2"),

		7=>array("#7bd0a9",$db->rp_getTotalRecord("orders",$order_where." AND status=1 ",0),"Approved Order","#","&#2352; ".$db->rp_number_format($db->rp_getValue("orders","sum(grand_total)",$order_where." AND status=1",0),2),$order_link."&status=1"),
		
		8=>array("#ec9b97",$db->rp_getTotalRecord("orders",$order_where." AND status=3 ",0),"Cancelled Order","#","&#2352; ".$db->rp_number_format($db->rp_getValue("orders","sum(grand_total)",$order_where." AND status=3",0),2),$order_link."&status=3"),

		9=>array("#ffa07a",$db->rp_getTotalRecord("orders",$order_where." AND status=-2 ",0),"Disapproved Order","#","&#2352; ".$db->rp_number_format($db->rp_getValue("orders","sum(grand_total)",$order_where." AND status=-2",0),2),$order_link."&status=-2"),
	);
}
else
{
	$order_where_sales=" AND sales_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";
	
	$query_1="SELECT SUM(count) FROM (SELECT COUNT(quotation_id) AS count FROM orders WHERE ".$order_where.$order_where_sales." AND quotation_id!='0' GROUP BY quotation_id HAVING count = 1) as A";
	$result1 = mysqli_query($query_1);
	$row1 = mysqli_fetch_array($result1);
	$quotation_to_order = $row1[0];

	$query_3="SELECT SUM(total),SUM(count) FROM (SELECT COUNT(quotation_id) AS count,SUM(grand_total) AS total FROM orders WHERE ".$order_where.$order_where_sales." AND quotation_id!='0' GROUP BY quotation_id HAVING count = 1) as A";
	$result2 = mysqli_query($query_3);
	$row2 = mysqli_fetch_array($result2);
	$quotation_to_order_amount= $row2 [0];

	$direct_order = $db->rp_getValue("orders","COUNT(quotation_id)","quotation_id='0' AND status!='-1' AND ".$order_where.$order_where_sales);
	$direct_order_amount = $db->rp_getValue("orders","sum(grand_total)",$order_where.$order_where_sales." AND quotation_id='0'");


	$query="SELECT SUM(count) FROM (SELECT COUNT(quotation_id) AS count FROM orders WHERE ".$order_where.$order_where_sales." AND quotation_id!='0' GROUP BY quotation_id HAVING count > 1) as B";
	$result = mysqli_query($query);
	$row = mysqli_fetch_array($result);

	if($row[0] == NULL)
	{
		$single_quotation_to_multiple_order='0';	
	}
	else
	{
		$single_quotation_to_multiple_order= $row[0];
	}

	$query_2="SELECT SUM(total),SUM(count) FROM (SELECT COUNT(quotation_id) AS count,SUM(grand_total) AS total FROM orders WHERE ".$order_where.$order_where_sales." AND quotation_id!='0' GROUP BY quotation_id HAVING count > 1) as B";
	$result3 = mysqli_query($query_2);
	$row3 = mysqli_fetch_array($result3);
	if($row3[0] == NULL)
	{
		$single_quotation_to_multiple_order_total='0';	
	}
	else
	{
		$single_quotation_to_multiple_order_total= $row3[0];
	}

	$quotation_to_order_sales = $db->rp_getValue("orders","COUNT(quotation_id)","isDelete=0 AND quotation_id!=0".$order_where_sales);
	
	$quotation_to_order_amount_sales = $db->rp_getValue("orders","sum(grand_total)","isDelete=0 AND quotation_id!=0".$order_where_sales);

	$dashboard_main_array_order = array(
			
		0=>array("black",$db->rp_getTotalRecord("orders",$order_where.$order_where_sales." AND status!=-1",0),"Total Order","#","&#2352; ".$db->rp_number_format($db->rp_getValue("orders","sum(grand_total)",$order_where.$order_where_sales." AND status!=-1",0),2),$order_link),
		// 1=>array("#ffa07a",$quotation_to_order,"Quotation To Order","#","&#2352; ".$db->rp_number_format($quotation_to_order_amount,2),"#","+"),

		// 2=>array("#ffa07a",$single_quotation_to_multiple_order,"Quotation To Multiple Order","#","&#2352; ".$db->rp_number_format($single_quotation_to_multiple_order_total,2),"#","+"),

		// 3=>array("#ffa07a",$direct_order,"Direct Order","#","&#2352; ".$db->rp_number_format($direct_order_amount,2),"#"),

		4=>array("#9fc1ff",$db->rp_getTotalRecord("orders",$order_where.$order_where_sales." AND status=0 ",0),"Pending Order","#","&#2352; ".$db->rp_number_format($db->rp_getValue("orders","sum(grand_total)",$order_where.$order_where_sales." AND status=0",0),2),$order_link."&status=0"),

		5=>array("grey",$db->rp_getTotalRecord("orders",$order_where.$order_where_sales." AND status=4 ",0),"Partial Dispatch Order","#","",$order_link."&status=4"),

		6=>array("#126608",$db->rp_getTotalRecord("orders",$order_where.$order_where_sales." AND status=2 ",0),"Full Dispatch Order","#","&#2352; ".$db->rp_number_format($db->rp_getValue("orders","sum(grand_total)",$order_where.$order_where_sales." AND status=2",0),2),$order_link."&status=2"),

		7=>array("#7bd0a9",$db->rp_getTotalRecord("orders",$order_where.$order_where_sales." AND status=1 ",0),"Approved Order","#","&#2352; ".$db->rp_number_format($db->rp_getValue("orders","sum(grand_total)",$order_where.$order_where_sales." AND status=1",0),2),$order_link."&status=1"),


		// 3=>array("#ec9b97",$db->rp_getTotalRecord("orders",$order_where.$order_where_sales." AND status=3 ",0),"In Dispatch","#",$db->rp_getValue("orders","sum(grand_total)",$order_where.$order_where_sales." AND status=3",0),$order_link."&status=3"),

		8=>array("#ec9b97",$db->rp_getTotalRecord("orders",$order_where.$order_where_sales." AND status=3 ",0),"Cancelled Order","#","",$order_link."&status=3"),

		9=>array("#ffa07a",$db->rp_getTotalRecord("orders",$order_where.$order_where_sales." AND status=-2 ",0),"Disapproved Order","#","&#2352; ".$db->rp_number_format($db->rp_getValue("orders","sum(grand_total)",$order_where.$order_where_sales." AND status=-2",0),2),$order_link."&status=-2"),
	);
}

?>
	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase"> Order Statistic</span>
			</div>
			<div class="col-md-3" id="todate_div" >
				<label>To Date</label>
				<input type="date" name="order_todate" id="order_todate" value="<?= $order_todate?>" onChange="monthYearClear('order')" class="form-control" autocomplete="off">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="order_fromdate" id="order_fromdate" value="<?=$order_fromdate?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return getorder();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="order_year" id="order_year" >
								<option value="">Select Year </option>
								<?php 
									$reg_year=date("Y","2017");
									$curr_year=date("Y");
									$current_date=date('Y-m-d');
									$adate1 = date('Y', strtotime($current_date));
									for ($i=$curr_year-$reg_year; $i>=0;$i--) 
									{
										?>
										<option <?php echo ($_REQUEST['order_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
										<?php
									}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="order_month" id="order_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) 
								{
									?>
									<option <?php echo (date("m", strtotime($month))==$_REQUEST['order_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control order_customer_id" name="customer_id" id="order_customer_id" value="<?php echo $customer_id;?>">
								<option value="">Select Customer</option>
								<?php 
								$cus_r=$db->rp_getData('executive',"*","isDelete=0","id DESC",0);
								while($cus_d=mysqli_fetch_assoc($cus_r))
								{
									?>
									<option <?php if($cus_d['id']==$_REQUEST['order_customer_id']){?> selected <?php } ?>  value="<?php echo $cus_d['id']?>"><?php echo $cus_d['company_name']?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==11)
							{
								$selected_id = $_REQUEST['order_sales_id'];
								$disabled = "";
							}
							else
							{
								$selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}
							?>
							<select class="form-control order_sales_id" name="sales_id" id="order_sales_id" value="<?php echo $sales_id;?>" <?php echo $disabled; ?>>
								<option value="">Select Executive</option>
								<?php 
								$sales_r=$db->rp_getData('sales_executive',"*","isDelete=0 AND isActive=1 ","name ASC",0);
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
					foreach($dashboard_main_array_order as $arr_order)
					{
						?>
						<div class="" style="padding: 10px; display: inline-block; vertical-align:top; white-space:normal;">
							<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_order[0] ?> solid; width: 120px; height: 100px;">
								<a href="<?php echo $arr_order[5]; ?>" style="text-decoration: none;">          
									<div class="desc" style="text-align: center;">
										<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
						                    <span data-counter="counterup" data-value="<?php echo $arr_order[1]; ?>"> <?php echo $arr_order[1]; ?> </span>
						               	</div>
						               	<strong style="font-size: 10px;"><?php echo $arr_order[4]; ?></strong><br/>
				                		<strong><?php echo $arr_order[2]; ?></strong>
									</div>
								</a>
							</div>
						</div>
						<div style="display: inline-block; padding-top: 50px;"><strong style="font-size: 25px;"><?= $arr_order[6] ?></strong></div>
						<?php
					}	
					?>
					</div>
					<div class="col-md-12 col-sm-12 co-xs-6 col-lg-12">
						<div class="portlet-body ">
							<div class="portlet-title">
								<div class="caption ">
									<br><br>
									<span class="caption-subject bold uppercase font-dark">Order Chart</span>
								</div>
							</div>
							<div id="orders" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$("#order_year").select2();
		$("#order_month").select2();
		$(".order_customer_id").select2();
		$(".order_sales_id").select2();
	</script>
<?php
$db->disconnect(); 
?>