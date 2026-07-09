<?php
$page_id=607;$page_slug='quotation';
include("connect.php");

$quotation_where="isDelete=0 AND status!=-1";

if($_REQUEST['quotation_year'] =="")
{
	$_REQUEST['quotation_year']=date("Y");
}

//$multiple_quotation_from_single_lead = $db->rp_getValue("quotation_detail","SUM(id)","isDelete=0 GROUP by inquiry_id",0);
/*
$q = "SELECT COUNT(count) FROM (SELECT COUNT(inquiry_id) AS count FROM quotation_detail GROUP BY inquiry_id HAVING count > 1) as A";
echo $q; exit;
$result = $conn->query($q);
print_r($result); exit;

*/
if($_REQUEST['quotation_year'] != "" && $_REQUEST['quotation_year'] != "undefined")
{
	$quotation_where.="  AND  Year(quotation_date)='".$_REQUEST['quotation_year']."'";
}

if($_REQUEST['quotation_month'] != "" && $_REQUEST['quotation_month'] != "undefined")
{
	$quotation_where.="  AND  MONTH(quotation_date)='".$_REQUEST['quotation_month']."'";
}

if($_REQUEST['quotation_customer_id'] != "" && $_REQUEST['quotation_customer_id'] != "undefined")
{
	$quotation_where.="  AND  customer_id='".$_REQUEST['quotation_customer_id']."'";
} 

if($_REQUEST['quotation_sales_id'] != "")
{
	$quotation_where.="  AND  sales_id='".$_REQUEST['quotation_sales_id']."'";
}

if($_REQUEST['quotation_todate'] !="" && $_REQUEST['quotation_fromdate'] !="" && $_REQUEST['quotation_month'] == "")
{
	$quotation_where.="  AND  DATE(quotation_date)>='".date('Y-m-d',strtotime($_REQUEST['quotation_todate']))."' AND  DATE(quotation_date)<='".date('Y-m-d',strtotime($_REQUEST['quotation_fromdate']))."'";
	$_REQUEST['quotation_todate']=date('Y-m-d',strtotime($_REQUEST['quotation_todate']));
	$_REQUEST['quotation_fromdate']=date('Y-m-d',strtotime($_REQUEST['quotation_fromdate']));
}

/*$customer_r=$db->rp_getData("executive","id,isDelete,isActive"," isActive=1 ","",0);
$customer_ids=array();
while($customer_d=mysqli_fetch_assoc($customer_r))
{
	$customer_ids[]=$customer_d['id'];
}
$customer_ids = implode(" , ", $customer_ids);
$quotation_where.="  AND  customer_id IN (".$customer_ids.") ";*/

$query="SELECT SUM(count) FROM (SELECT COUNT(inquiry_id) AS count FROM quotation_detail WHERE ".$quotation_where."  GROUP BY inquiry_id HAVING count > 1) as A";
$result = mysqli_query($query);
$row = mysqli_fetch_array($result);
$multiple_quotation_from_single_lead = $row[0];
if($row[0] == NULL)
{
	$multiple_quotation_from_single_lead = "0";	
}
else
{
	$multiple_quotation_from_single_lead = $row[0];
}

$query_2="SELECT SUM(count),SUM(total) FROM (SELECT COUNT(inquiry_id) AS count,SUM(grand_total) AS total FROM quotation_detail WHERE ".$quotation_where." GROUP BY inquiry_id HAVING count > 1) as A";
$result2 = mysqli_query($query_2);
$row2 = mysqli_fetch_array($result2);
$multiple_quotation_from_single_lead_total = $row2[1];

$single_quotation_from_single_lead = $db->rp_getTotalRecord("quotation_detail",$quotation_where." GROUP by inquiry_id having count(*) = 1",0);


$query3="SELECT SUM(count),SUM(total) FROM (SELECT COUNT(inquiry_id) AS count,SUM(grand_total) AS total FROM quotation_detail WHERE ".$quotation_where." GROUP BY inquiry_id HAVING count = 1) as A";
$result3 = mysqli_query($query3);
$row3 = mysqli_fetch_array($result3);
$single_quotation_from_single_lead_total = $row3[1];

// for quotation link
$quotation_link.="quotation_manage.php?";

if($_REQUEST['quotation_year'] == "" || $_REQUEST['quotation_month'] == "")
{
	$quotation_link.="&quotation_year=".date("Y");
}

if($_REQUEST['quotation_month'] != "" && $_REQUEST['quotation_year'] == "")
{
	$quotation_link.="&quotation_month=".$_REQUEST['quotation_month'];
}

if($_REQUEST['quotation_month'] != "" && $_REQUEST['quotation_year'] !="")
{
	$quotation_link.="&quotation_month=".$_REQUEST['quotation_month']."&quotation_year=".$_REQUEST['quotation_year'];
}

if($_REQUEST['quotation_sales_id'] != "")
{
	$quotation_link.="&sales_id=".$_REQUEST['quotation_sales_id'];
}

if($_REQUEST['quotation_customer_id'] != "")
{
	$quotation_link.="&customer_id=".$_REQUEST['quotation_customer_id'];
}
if($_REQUEST['quotation_todate'] != "")
{
	$quotation_link.="&todate=".$_REQUEST['quotation_todate'];
}

if($_REQUEST['quotation_fromdate'] != "")
{
	$quotation_link.="&fromdate=".$_REQUEST['quotation_fromdate'];
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{

	$dashboard_main_array_graph = array(
			
		0=>array("black",$db->rp_getTotalRecord("quotation_detail",$quotation_where,0),"Total Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where,0),$quotation_link,607,"="),

		// 1=>array("black",$multiple_quotation_from_single_lead,"Multiple Quotation From Lead","#",$multiple_quotation_from_single_lead_total,"#",607,"+"),

		// 2=>array("black",$single_quotation_from_single_lead,"Single Quotation From Lead","#",$single_quotation_from_single_lead_total,"#",607),


		// 3=>array("#9fc1ff",$db->rp_getTotalRecord("quotation_detail",$quotation_where." AND status=0",0),"Pending-Waiting for Approval Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where." AND status=0",0),$quotation_link."&status=0",607),

		4=>array("#7bd0a9",$db->rp_getTotalRecord("quotation_detail",$quotation_where." AND status=1",0),"Approved Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where." AND status=1",0),$quotation_link."&status=1",607),

		5=>array("#126608",$db->rp_getTotalRecord("quotation_detail",$quotation_where." AND status=4",0),"Quotation To Order","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where." AND status=4",0),$quotation_link."&status=4",607),

		// 6=>array("#ec9b97",$db->rp_getTotalRecord("quotation_detail",$quotation_where." AND status=3",0),"Cancel Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where." AND status=3",0),$quotation_link."&status=3",607),

		7=>array("grey",$db->rp_getTotalRecord("quotation_detail",$quotation_where." AND status=5",0),"Lost Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where." AND status=5",0),$quotation_link."&status=5",607),

		// 8=>array("#ffa07a",$db->rp_getTotalRecord("quotation_detail",$quotation_where." AND status=-2",0),"Disapproved Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where." AND status=-2",0),$quotation_link."&status=-2",607),
	);
}
else
{
	$quotation_where_sales=" AND sales_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";
	
	$query="SELECT SUM(count) FROM (SELECT COUNT(inquiry_id) AS count FROM quotation_detail WHERE ".$quotation_where.$quotation_where_sales."  GROUP BY inquiry_id HAVING count > 1) as A";
		$result = mysqli_query($query);
	$row = mysqli_fetch_array($result);
	$multiple_quotation_from_single_lead = $row[0];
	if($row[0] == NULL)
	{
		$multiple_quotation_from_single_lead = "0";	
	}
	else
	{
		$multiple_quotation_from_single_lead = $row[0];
	}

	$query_2="SELECT SUM(count),SUM(total) FROM (SELECT COUNT(inquiry_id) AS count,SUM(grand_total) AS total FROM quotation_detail WHERE ".$quotation_where.$quotation_where_sales." GROUP BY inquiry_id HAVING count > 1) as A";
	$result2 = mysqli_query($query_2);
	$row2 = mysqli_fetch_array($result2);
	$multiple_quotation_from_single_lead_total = $row2[1];

	$single_quotation_from_single_lead = $db->rp_getTotalRecord("quotation_detail",$quotation_where.$quotation_where_sales." GROUP by inquiry_id having count(*) = 1",0);


	$query3="SELECT SUM(count),SUM(total) FROM (SELECT COUNT(inquiry_id) AS count,SUM(grand_total) AS total FROM quotation_detail WHERE ".$quotation_where.$quotation_where_sales." GROUP BY inquiry_id HAVING count = 1) as A";
	$result3 = mysqli_query($query3);
	$row3 = mysqli_fetch_array($result3);

	$single_quotation_from_single_lead_total = $row3[1];

	$dashboard_main_array_graph = array(
		
		0=>array("black",$db->rp_getTotalRecord("quotation_detail",$quotation_where.$quotation_where_sales,0),"Total Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where.$quotation_where_sales,0),$quotation_link,607),

		// 1=>array("black",$multiple_quotation_from_single_lead,"Multiple Quotation From Lead","#",$multiple_quotation_from_single_lead_total,"#",607,"+"),

		// 2=>array("black",$single_quotation_from_single_lead,"Single Quotation From Lead","#",$single_quotation_from_single_lead_total,"#",607),

		// 3=>array("#9fc1ff",$db->rp_getTotalRecord("quotation_detail",$quotation_where.$quotation_where_sales." AND status=0",0),"Pending-Waiting for Approval Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where.$quotation_where_sales." AND status=0",0),$quotation_link."&status=0",607),

		4=>array("#7bd0a9",$db->rp_getTotalRecord("quotation_detail",$quotation_where.$quotation_where_sales." AND status=1",0),"Approved Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where.$quotation_where_sales." AND status=1",0),$quotation_link."&status=1",607),

		5=>array("#126608",$db->rp_getTotalRecord("quotation_detail",$quotation_where.$quotation_where_sales." AND status=4",0),"Quotation To Order","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where.$quotation_where_sales." AND status=4",0),$quotation_link."&status=4",607),

		// 6=>array("#ec9b97",$db->rp_getTotalRecord("quotation_detail",$quotation_where.$quotation_where_sales." AND status=3",0),"Cancel Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where.$quotation_where_sales." AND status=3",0),$quotation_link."&status=3",607),

		7=>array("grey",$db->rp_getTotalRecord("quotation_detail",$quotation_where.$quotation_where_sales." AND status=5",0),"Lost Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where.$quotation_where_sales." AND status=5",0),$quotation_link."&status=5",607),

		// 8=>array("#ffa07a",$db->rp_getTotalRecord("quotation_detail",$quotation_where.$quotation_where_sales." AND status=-2",0),"Disapproved Quotation","#",$db->rp_getValue("quotation_detail","sum(grand_total)",$quotation_where.$quotation_where_sales." AND status=-2",0),$quotation_link."&status=-2",607),
	);
}
?>
<style type="text/css">
	.horizontal-scrollable > .row {
        overflow-x: auto ! important;
        white-space: nowrap ! important;
    }
      
    .horizontal-scrollable > .row > .col-lg-4 {
        display: inline-block ! important;
        float: none ! important;
    }
    /* Decorations */
      
    .col-lg-4 {
        color: white ! important;
        font-size: 24px ! important;
        padding-bottom: 20px ! important;
        padding-top: 18px ! important;
    }
      
    .col-lg-4:nth-child(2n+1) {
        background: green ! important;
    }
      
    .col-lg-4:nth-child(2n+2) {
        background: black ! important;
    }
</style>



	<div class="portlet light div-set-height">
		<div class="portlet-title">
			<div class="caption caption-md">
				<i class="icon-bar-chart font-dark hide"></i>
				<span class="caption-subject font-dark bold uppercase">  Quotation Statistic </span>
			</div>
			<div class="col-md-3" id="todate_div" >
				<label>To Date</label>
				<input type="date" name="quotation_todate" id="quotation_todate" value="<?= $_REQUEST['quotation_todate'] ?>" class="form-control">
			</div>
			<div class="col-md-3" id="todate_div">
				<label>From Date</label>
				<input type="date" name="quotation_fromdate" id="quotation_fromdate" value="<?= $_REQUEST['quotation_fromdate'] ?>" class="form-control">
			</div>
			<span style="float: right;">
				<a href="javascript:;"  onClick="return  getquotation();" class="btn btn-circle red-sunglo ">
				<i class="fa fa-refresh"></i>Refresh </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12 pull-right">
					<div class="row">
						<div class="col-sm-3">
							<select onChange="" class="form-control" name="quotation_year" id="quotation_year" >
								<option value="">Select Year </option>
								<?php 
								$reg_year=date("Y","2017");
								$curr_year=date("Y");
								$current_date=date('Y-m-d');
								$adate1 = date('Y', strtotime($current_date));
								for ($i=$curr_year-$reg_year; $i>=0;$i--)
								{
									?>
									<option <?php echo ($_REQUEST['quotation_year'] == $reg_year+$i)?"selected":"" ; ?> value="<?php echo $reg_year+$i; ?>"><?php echo $reg_year+$i; ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control" name="quotation_month" id="quotation_month">
								<option value="">Select Month</option>
								<?php 
								$months = array("January", "February", "March", "April", "May", "June","July","August","September ","October ","November","December");
								foreach ($months as $month) {
								?>
									<option <?php echo (date("m", strtotime($month))==$_REQUEST['quotation_month'])?"selected":"" ; ?> value="<?php echo date("m", strtotime($month));?>"><?php echo $month;?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<select class="form-control quotation_customer_id" name="customer_id" id="quotation_customer_id" value="<?php echo $customer_id;?>">
								<option value="">Select Customer</option>
								<?php 
								$cus_r=$db->rp_getData('executive',"*","isDelete=0","id DESC",0);
								while($cus_d=mysqli_fetch_assoc($cus_r))
								{
									?>
									<option <?php if($cus_d['id']==$_REQUEST['quotation_customer_id']){?> selected <?php } ?>  value="<?php echo $cus_d['id']?>"><?php echo $cus_d['company_name']?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div class="col-sm-3">
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
							{
								$selected_id = $_REQUEST['quotation_sales_id'];
								$disabled = "";
							}
							else
							{
								$selected_id =  $_SESSION[SITE_SESS.'REFERANCE_ID'];
								$disabled = "disabled";
							}
							?>
							<select class="form-control quotation_sales_id" name="quotation_sales_id" id="quotation_sales_id" value="<?php echo $sales_id;?>" <?php echo $disabled; ?>>
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
					foreach($dashboard_main_array_graph as $arr_graph)
					{
						?>
						<div class="" style="padding: 10px;display: inline-block;vertical-align:top;/*margin-right:20px;*/white-space:normal;">
							<div class="dashboard-stat " style="border: 1px solid; border-bottom: 10px <?= $arr_graph[0] ?> solid; width: 120px; height: 100px; ">
							   	<a href="<?php echo $arr_graph[5]; ?>" style="text-decoration: none;">      
									<div class="desc" style="text-align: center;">
										<div class="number" style="font-size:25px;padding-top: 0px; text-align: center; ">
				                    		<span data-counter="counterup" data-value="<?php echo $arr_graph[1]; ?>"><?php echo $arr_graph[1]; ?></span>
				               			</div>
				               			<strong style="font-size: 10px;"><?php echo "&#2352;".$db->rp_number_format($arr_graph[4],2); ?></strong><br/>
				               			<strong style="font-size: 10px;"><?php echo $arr_graph[2]; ?></strong>
				               		</div>
								</a>
							</div>
						</div>
						<div style="display: inline-block; padding-top: 50px;"><strong style="font-size: 25px;"><?= $arr_graph[7] ?></strong></div>
						<?php
					}	
					?>
					</div>
					
					<?php
						$pending_quotationR = $db->rp_getData("quotation_detail","*","isDelete=0 AND status=0","id DESC");
						$pending_OrderR = $db->rp_getData("orders","*","isDelete=0 AND status=0","id DESC");
						$salse_executive_list_results = $db->rp_getData("sales_executive","*","isDelete=0 ","id DESC");
					?>
					<div class="col-md-12 col-sm-12 co-xs-6 col-lg-12">
						<div class="portlet-body ">
							<div class="portlet-title">
								<div class="caption ">
									<br><br>
									<span class="caption-subject bold uppercase font-dark">Quotation Chart</span>
								</div>
							</div>
							<div id="quotation" class="CSSAnimationChart m-t-40 " style="width: 104%!important; height: 316px!important;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
<script type="text/javascript">
	$("#quotation_year").select2();
	$("#quotation_month").select2();
	$(".quotation_customer_id").select2();
	$(".quotation_sales_id").select2();
</script>
<?php
$db->disconnect(); 
?>