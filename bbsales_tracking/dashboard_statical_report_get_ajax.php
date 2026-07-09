<?php
$page_id=627;$page_slug='dashboard_statical_report_page';
include("connect.php");

$ctable   = "attendance";
$ctable1  = "visit";
$ctable2  = "no_order_inquiry";
$ctable3  = "followup";
$ctable4  = "quotation_detail";
$ctable5  = "orders";
$ctable6  = "followup";
$ctable7  = "dispatch_detail";
$ctable8  = "invoice_new";
$ctable9  = "complain";
$ctable10  = "expense";
$ctable11  = "leave_request";

$sales_id=$_REQUEST['sales_id'];
$date = $_REQUEST['date'];
$date1 = $_REQUEST['date1'];


$ctable_where = "";
$ctable_where .="sales_id=".$sales_id." AND isDelete=0";
$ctable_where .= " AND DATE(date_time) >= '".date("Y-m-d",strtotime($date))."'";
$ctable_where .= " AND DATE(date_time) <= '".date("Y-m-d",strtotime($date1))."'";
$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"date_time DESC",0);
$ctable_r1 = $db->rp_getData($ctable,"*",$ctable_where,"date_time DESC",0);

// For Prospects 
$ctable_where_prospect = " isDelete=0 ";

if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_prospect .= " AND year(inquiry_date) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_prospect .= " AND  DATE(inquiry_date) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_prospect .= " AND  DATE(inquiry_date) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_prospect .= " AND  inquiry_assign_to = '".$_REQUEST['sales_id']."'";
	//$ctable_where_prospect_sales .= " AND  inquiry_assign_to = '".$_REQUEST['sales_id']."'";
}

$direct_prospact = $db->rp_getTotalRecord("no_order_inquiry","isDelete = 0 AND inq_status = '-1' AND inquiry_lead_flag='-1' AND ".$ctable_where_prospect,0);

$prospect_to_inquiry  = 0;
// For Prospects 


// For Inquiry
$ctable_where_inquiry = " isDelete=0 ";
if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_inquiry .= " AND year(inquiry_date) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_inquiry .= " AND  DATE(inquiry_date) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_inquiry .= " AND  DATE(inquiry_date) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_inquiry .= " AND  inquiry_assign_to = '".$_REQUEST['sales_id']."'";
	//$ctable_where_inquiry_sales .= " AND  inquiry_assign_to = '".$_REQUEST['sales_id']."'";
}
// For Inquiry 

// For Lead
$ctable_where_lead = " isDelete=0 ";
if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_lead .= " AND year(inquiry_date) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_lead .= " AND  DATE(inquiry_date) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_lead .= " AND  DATE(inquiry_date) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_lead .= " AND  inquiry_assign_to = '".$_REQUEST['sales_id']."'";
	//$ctable_where_lead_sales .= " AND  inquiry_assign_to = '".$_REQUEST['sales_id']."'";
}
// For Lead 

/*For Quotation*/
/*quotation*/
// $ctable_where_quotation = " isDelete=0 AND year(quotation_date) = '".date('Y')."' ";
$ctable_where_quotation = " isDelete=0 ";

if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_quotation .= " AND year(quotation_date) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_quotation .= " AND  DATE(quotation_date) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_quotation .= " AND  DATE(quotation_date) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_quotation .= " AND  sales_id = '".$_REQUEST['sales_id']."'";
}

$result = mysqli_query("SELECT SUM(count) FROM (SELECT COUNT(inquiry_id) AS count FROM quotation_detail WHERE ".$ctable_where_quotation." GROUP BY inquiry_id HAVING count > 1) as A");
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
$single_quotation_from_single_lead = $db->rp_getTotalRecord("quotation_detail",$ctable_where_quotation."GROUP by inquiry_id having count(*) = 1",0);
/*quotation*/
/*For Quotation*/


/*orders*/
// $ctable_where_orders = " isDelete=0 AND year(order_date) = '".date('Y')."' ";
$ctable_where_orders = " isDelete=0 ";

if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_orders .= " AND year(order_date) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_orders .= " AND  DATE(order_date) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_orders .= " AND  DATE(order_date) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_orders .= " AND  sales_id = '".$_REQUEST['sales_id']."'";
}

$result1 = mysqli_query("SELECT SUM(count) FROM (SELECT COUNT(quotation_id) AS count FROM orders WHERE ".$ctable_where_orders." AND quotation_id!='0' GROUP BY quotation_id HAVING count = 1) as A");
$row1 = mysqli_fetch_array($result1);

$quotation_to_order = $row1[0];

$direct_order = $db->rp_getValue("orders","COUNT(quotation_id)",$ctable_where_orders." AND quotation_id='0' AND status!='-1'",0);
$direct_order_amount = $db->rp_getValue("orders","sum(grand_total)",$ctable_where_orders." AND quotation_id='0' ");

$result = mysqli_query("SELECT SUM(count) FROM (SELECT COUNT(quotation_id) AS count FROM orders WHERE ".$ctable_where_orders." AND quotation_id!='0' GROUP BY quotation_id HAVING count > 1) as B");
$row = mysqli_fetch_array($result);
if($row[0] == NULL)
{
	$single_quotation_to_multiple_order='0';    
}
else
{
	$single_quotation_to_multiple_order= $row[0];
}
/*orders*/


/*dispatch*/
// $ctable_where_dispatch = " isDelete=0 AND year(dispatch_date) = '".date('Y')."' ";
$ctable_where_dispatch = " isDelete=0 ";

if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_dispatch .= " AND year(dispatch_date) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_dispatch .= " AND  DATE(dispatch_date) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_dispatch .= " AND  DATE(dispatch_date) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_dispatch .= " AND  sales_id = '".$_REQUEST['sales_id']."'";
}

$single_dispatch = $db->rp_getTotalRecord("dispatch_detail",$ctable_where_dispatch." GROUP by order_id having count(*) = 1",0);

$result = mysqli_query("SELECT SUM(count) FROM (SELECT COUNT(order_id) AS count FROM dispatch_detail WHERE".$ctable_where_dispatch." GROUP BY order_id HAVING count > 1) as A");
$row = mysqli_fetch_array($result);

$multiple_dispatch = $row[0];
if($row[0] == NULL)
{
	$multiple_dispatch='0'; 
}
else
{

	$multiple_dispatch= $row[0];
}
/*dispatch*/


/*invoice*/
$ctable_where_invoice = " isDelete=0 ";
// $ctable_where_invoice = " isDelete=0 AND year(invoice_date) = '".date('Y')."' ";

if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_invoice .= " AND year(adate) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_invoice .= " AND  DATE(adate) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_invoice .= " AND  DATE(adate) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_invoice .= " AND  sales_id = '".$_REQUEST['sales_id']."'";
}
/*invoice*/



/*visit*/
// $ctable_where_visit = " isDelete=0 AND year(created_date) = '".date('Y')."' ";
$ctable_where_visit = " isDelete=0 ";

if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_visit .= " AND year(created_date) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_visit .= " AND  DATE(created_date) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_visit .= " AND  DATE(created_date) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_visit .= " AND  user_id = '".$_REQUEST['sales_id']."'";
}
/*visit*/

/*complain*/
// $ctable_where_complain = " isDelete=0 AND year(complain_date) = '".date('Y')."' ";
$ctable_where_complain = " isDelete=0 ";
if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_complain .= " AND year(complain_date) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_complain .= " AND  DATE(complain_date) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_complain .= " AND  DATE(complain_date) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_complain .= " AND  user_id = '".$_REQUEST['sales_id']."'";
}
/*complain*/

/*expense*/
// $ctable_where_expense = " isDelete=0 AND year(expense_date) = '".date('Y')."' ";
$ctable_where_expense = " isDelete=0";
if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_expense .= " AND year(expense_date) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_expense .= " AND  DATE(expense_date) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_expense .= " AND  DATE(expense_date) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_expense .= " AND  sales_executive_id = '".$_REQUEST['sales_id']."'";
}
/*expense*/

/*leave*/
// $ctable_where_leave = " isDelete=0 AND year(start_date) = '".date('Y')."' ";
$ctable_where_leave = " isDelete=0 ";
if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_leave .= " AND year(start_date) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_leave .= " AND  DATE(start_date) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_leave .= " AND  DATE(start_date) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_leave .= " AND  sales_executive_id = '".$_REQUEST['sales_id']."'";
}
/*leave*/

/*followup*/
// $ctable_where_followup = " isDelete=0 AND year(followup_date) = '".date('Y')."' ";
$ctable_where_followup = " isDelete=0 ";
if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_followup .= " AND  year(followup_date) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_followup .= " AND  DATE(followup_date) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_followup .= " AND  DATE(followup_date) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_followup .= " AND  user_id = '".$_REQUEST['sales_id']."'";
}
/*followup*/

/*attendance*/
$ctable_where_attendance = " isDelete=0 ";
// $ctable_where_attendance = " isDelete=0 AND year(date_time) = '".date('Y')."' ";
if($_REQUEST['date']=="" && $_REQUEST['date1']=="")
{
	$ctable_where_attendance .= " AND  year(date_time) = '".date('Y')."' ";
}
if(isset($_REQUEST['date']) && $_REQUEST['date']!="" && $_REQUEST['date']!=NULL)
{
	$ctable_where_attendance .= " AND  DATE(date_time) >= '".date("Y-m-d",strtotime($_REQUEST['date']))."' ";
}
if(isset($_REQUEST['date1']) && $_REQUEST['date1']!="" && $_REQUEST['date1']!=NULL)
{
	$ctable_where_attendance .= " AND  DATE(date_time) <= '".date("Y-m-d",strtotime($_REQUEST['date1']))."' ";
}
if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where_attendance .= " AND  sales_id = '".$_REQUEST['sales_id']."'";
}
/*attendance*/
?>

<style type="text/css">
  .no-border td
  {
	border:none!important;   
	font-size: 20px;
  }
  .pad-0
  {
	padding: 0px!important;
  }
</style>
<link rel="stylesheet" href="<?=ADMINSITEURL?>css/lightbox.css" />
<div class="row" id="report_content" style="margin:0;">
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin: 0">
		<h2><b>Dashboard Statical Report</b></h2>
 	</div>

 	<!-- total Prospect -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
  		<h3><b>Raw Data Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
	  	<thead>
			<tr>
				<th>Total Raw Data</th>
				<th>Direct Raw Data</th>
				<th>Raw Data To Inquiry</th>
			  	<th>Pending Raw Data</th>
			  	<th>In Followup Raw Data</th>
				<!-- <th>Hot In Followup Prospect</th>
				<th>Cold In Followup Prospect</th>
				<th>Warm In Followup Prospect</th> -->
				<th>Non Relevent Lost Raw Data</th>
				<th>Not Interested Lost Raw Data</th>
				<th>Buy Later Lost Raw Data</th>
				<th>Lost Raw Data</th>
			</tr>
	  	</thead>
	  	<tbody>
			<tr>
			  	<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_prospect."  AND (inquiry_lead_flag = '-1')",0) ?></td>
			  	<td><?= $direct_prospact ?></td>                
			  	<td><?= $prospect_to_inquiry ?></td>                
				<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_prospect." AND status=0 AND (inquiry_lead_flag = '-1')"); ?></td>
				<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_prospect." AND status=1 AND (inquiry_lead_flag = '-1')"); ?></td>
				<!-- <td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_prospect." AND status=4 AND (inquiry_lead_flag = '-1')"); ?></td>
				<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_prospect." AND status=5 AND (inquiry_lead_flag = '-1')"); ?></td>
				<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_prospect." AND status=6 AND (inquiry_lead_flag = '-1')"); ?></td> -->
				<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_prospect." AND status=-2 AND (inquiry_lead_flag = '-1')"); ?></td>
				<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_prospect." AND status=-1 AND (inquiry_lead_flag = '-1')"); ?></td>
				<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_prospect." AND status=3 AND (inquiry_lead_flag = '-1')"); ?></td>
				<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_prospect." AND status=11 AND (inquiry_lead_flag = '-1')"); ?></td>
			</tr>    
	  	</tbody>
	</table>
	<!-- total Prospect -->
	
	<!-- total Inquiry -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
  		<h3><b>Inquiry Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
	  	<thead>
		 	<tr>
		  		<th>Total Inquiry</th>
		  		<th>Direct Inquiry</th>
			  	<th>Raw Data to Inquiry</th>
			  	<th>Inquiry to Lead</th>
			  	<th>Pending Inquiry</th>
			  	<th>In Followup Inquiry</th>
			  	<!-- <th>Hot In Followup Inquiry</th>
			  	<th>Cold In Followup Inquiry</th>
			  	<th>Warm In Followup Inquiry</th> -->
			  	<th>Non Relevent Lost Inquiry</th>
			  	<th>Not Interested Lost Inquiry</th>
			  	<th>Buy Later Lost Inquiry</th>
			  	<th>Lost Inquiry</th>
			</tr>
	  	</thead>
	  	<tbody>
			<tr>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry."  AND inquiry_lead_flag != '-1' ",0) ?></td>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND inquiry_lead_flag='0' AND inq_status!=2 "); ?></td>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND inq_status = 2"); ?></td>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND inquiry_lead_flag = '1'"); ?></td>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND status=0 AND (inquiry_lead_flag = '0')"); ?></td>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND status=1 AND (inquiry_lead_flag = '0')"); ?></td>
		  		<!-- <td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND status=4 AND (inquiry_lead_flag = '0')"); ?></td>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND status=5 AND (inquiry_lead_flag = '0')"); ?></td>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND status=6 AND (inquiry_lead_flag = '0')"); ?></td> -->
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND status=-2 AND (inquiry_lead_flag = '0')"); ?></td>              
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND status=-1 AND (inquiry_lead_flag = '0')"); ?></td>              
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND status=3 AND (inquiry_lead_flag = '0')"); ?></td>              
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_inquiry." AND status=11 AND (inquiry_lead_flag = '0')"); ?></td>                      
			</tr>
	 	</tbody>
	</table>
	<!-- total Inquiry -->

	<!-- total Lead -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
		<h3><b>Leads Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
	  	<thead>
		 	<tr>
		  		<th>Total Lead</th>
		  		<th>Pending Lead</th>
		  		<th>In Followup Lead</th>
		  		<!-- <th>Hot In Followup Lead</th>
		  		<th>Cold In Followup Lead</th>
		  		<th>Warm In Followup Lead</th> -->
		  		<th>Non Relevent Lost Lead</th>
		  		<th>Not Interested Lost Lead</th>
		  		<th>Buy Later Lost Lead</th>
		  		<th>Lost Lead</th>
			</tr>
	  	</thead>
	  	<tbody>
			<tr>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_lead."  AND inquiry_lead_flag= '1' ",0) ?></td>              
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_lead." AND status=0 AND inquiry_lead_flag= '1'"); ?></td>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_lead." AND status=1 AND inquiry_lead_flag= '1'"); ?></td>
		  		<!-- <td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_lead." AND status=4 AND inquiry_lead_flag='1'"); ?></td>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_lead." AND status=5 AND inquiry_lead_flag='1'"); ?></td>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_lead." AND status=6 AND inquiry_lead_flag='1'"); ?></td> -->
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_lead." AND status=-2 AND inquiry_lead_flag= '1'"); ?></td>
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_lead." AND status=-1 AND inquiry_lead_flag= '1'"); ?></td> 
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_lead." AND status=3 AND inquiry_lead_flag= '1'"); ?></td>   
		  		<td><?= $db->rp_getTotalRecord($ctable2,$ctable_where_lead." AND status=11 AND inquiry_lead_flag='1'"); ?></td>     
			</tr>
	  	</tbody>
	</table>
	<!-- total Lead -->

	<!-- total quotation -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
		<h3><b>Quotation Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
	  	<thead>
			<tr>
				<th>Total Quotation</th>
				<?php if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0) 
				{ 
					?>
					<th>Multiple Quotation From Lead</th>
					<th>Single Quotation From Lead</th>
					<?php 
				} 
				?>
				<th>Pending-Waiting for Approval</th>
				<th>Approved Quotation</th>
				<th>Quotation To Order</th>
				<th>Cancel Quotation</th>
				<th>Lost Quotation</th>
				<th>Disapproved Quotation</th>
			</tr>
	  	</thead>
	  	<tbody>
		  	<tr>
				<td><?= $db->rp_getTotalRecord($ctable4,$ctable_where_quotation) ?></td> 
				<?php 
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0) 
				{ 
					?>
					<td><?= $multiple_quotation_from_single_lead ?></td>
					<td><?= $single_quotation_from_single_lead ?></td>
					<?php 
				} 
				?>
				<td><?= $db->rp_getTotalRecord($ctable4,$ctable_where_quotation." AND status=0") ?></td> 
				<td><?= $db->rp_getTotalRecord($ctable4,$ctable_where_quotation." AND status=1") ?></td> 
				<td><?= $db->rp_getTotalRecord($ctable4,$ctable_where_quotation." AND status=4") ?></td> 
				<td><?= $db->rp_getTotalRecord($ctable4,$ctable_where_quotation." AND status=3") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable4,$ctable_where_quotation." AND status=5") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable4,$ctable_where_quotation." AND status=-2") ?></td>
		  	</tr>
	 	</tbody>
	</table>
	<!-- total quotation -->

	<!-- total order -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
  		<h3><b>Orders Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
	  	<thead>
			<tr>
				<th>Total Order</th>
				<?php 
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0) 
				{ 
					?>
					<th>Quotation To Order</th>
					<th>Quotation To Multiple Order</th>
					<th>Direct Order</th>
					<?php 
				} 
				?>
				<th>Pending Order</th>
				<th>Partial Dispatch Order</th>
				<th>Full Dispatch Order</th>
				<th>Approved Order</th>
				<th>Cancelled Order</th>
				<th>Disapproved Order</th>
			</tr>
	  	</thead>
		<tbody>
			<tr>
				<td><?= $db->rp_getTotalRecord($ctable5,$ctable_where_orders." AND status!=-1") ?></td> 
				<?php 
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0) 
				{ 
					?>
					<td><?= $quotation_to_order ?></td>
					<td><?= $single_quotation_to_multiple_order ?></td>
					<td><?= $direct_order ?></td>
					<?php 
				} 
				?>
				<td><?= $db->rp_getTotalRecord($ctable5,$ctable_where_orders." AND status=0") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable5,$ctable_where_orders." AND status=4") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable5,$ctable_where_orders." AND status=2") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable5,$ctable_where_orders." AND status=1") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable5,$ctable_where_orders." AND status=3") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable5,$ctable_where_orders." AND status=-2") ?></td>
			</tr>
		</tbody>
	</table>
	<!-- total order -->

	<!-- total dispatch -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
	  <h3><b>Dispatch Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
	  	<thead>
			<tr>
				<th>Total Dispatch Note</th>
				<?php 
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0) 
				{ 
					?>
					<th>Single Dispatch Note</th>
					<th>Multiple Dispatch Note</th>
					<?php 
				} 
				?>
				<th>Pending Dispatch Note</th>
				<th>Dispatch Note To Invoice</th>
			</tr>
	  	</thead>
		<tbody>
			<tr>
				<td><?= $db->rp_getTotalRecord($ctable7,$ctable_where_dispatch) ?></td> 
				<?php 
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0) 
				{ 
					?>
					<td><?= $single_dispatch ?></td>
					<td><?= $multiple_dispatch ?></td>
					<?php 
				} 
				?>
				<td><?= $db->rp_getTotalRecord($ctable7,$ctable_where_dispatch." AND status=0") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable7,$ctable_where_dispatch." AND status=1") ?></td>
			</tr>
		</tbody>
	</table>
	<!-- total dispatch -->

	<!-- total invoice -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
	  <h3><b>Invoice Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th>Total Invoice</th>
				<th>Pending Invoice</th>
				<th>Approved Invoice</th>
				<th>Cancelled Invoice</th>
				<th>Disapproved Invoice</th>
			</tr>
		</thead>
		<tbody>
		  	<tr>
				<td><?= $db->rp_getTotalRecord($ctable8,$ctable_where_invoice) ?></td> 
				<td><?= $db->rp_getTotalRecord($ctable8,$ctable_where_invoice." AND status=0") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable8,$ctable_where_invoice." AND status=1") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable8,$ctable_where_invoice." AND status=3") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable8,$ctable_where_invoice." AND status=-2") ?></td>
		  	</tr>
		</tbody>
	</table>
	<!-- total invoice -->

	<!-- total visit -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
  		<h3><b>Visit Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
	  	<thead>
			<tr>
				<th>Total Visit</th>
			</tr>
	  	</thead>
	  	<tbody>
		  	<tr>
				<td><?= $db->rp_getTotalRecord($ctable1,$ctable_where_visit) ?></td> 
		  	</tr>
	  	</tbody>
	</table>
	<!-- total visit -->

	<!-- total complain -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
  		<h3><b>Complain Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
	  	<thead>
			<tr>
				<th>Total Complain</th>
				<th>Generated</th>
				<th>In Progress</th>
				<th>Complete</th>
				<th>Reject</th>
				<th>Not Done</th>
			</tr>
	  	</thead>
	  	<tbody>
		  	<tr>
				<td><?= $db->rp_getTotalRecord($ctable9,$ctable_where_complain) ?></td> 
				<td><?= $db->rp_getTotalRecord($ctable9,$ctable_where_complain." AND status=0") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable9,$ctable_where_complain." AND status=1") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable9,$ctable_where_complain." AND status=2") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable9,$ctable_where_complain." AND status=-1") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable9,$ctable_where_complain." AND status=-2") ?></td>
		  	</tr>
	  	</tbody>
	</table>
	<!-- total complain -->

	<!-- total Expense -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
	  <h3><b>Expense Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
	  	<thead>
			<tr>
				<th>Total Expense</th>
				<th>Requested</th>
				<th>Pass</th>
				<th>Reject</th>
			</tr>
	  	</thead>
	  	<tbody>
		  	<tr>
				<td><?= $db->rp_getTotalRecord($ctable10,$ctable_where_expense) ?></td> 
				<td><?= $db->rp_getTotalRecord($ctable10,$ctable_where_expense." AND expense_status=0") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable10,$ctable_where_expense." AND expense_status=1") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable10,$ctable_where_expense." AND expense_status=2") ?></td>
		  	</tr>
	  	</tbody>
	</table>
	<!-- total Expense -->

	<!-- total leave -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
	  <h3><b>Leave Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
	  	<thead>
			<tr>
				<th>Total Leave</th>
				<th>Generated</th>
				<th>Accepted</th>
				<th>Rejected</th>
				<th>Cancel</th>
			</tr>
	  	</thead>
	  	<tbody>
		  	<tr>
				<td><?= $db->rp_getTotalRecord($ctable11,$ctable_where_leave) ?></td> 
				<td><?= $db->rp_getTotalRecord($ctable11,$ctable_where_leave." AND status=0") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable11,$ctable_where_leave." AND status=1") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable11,$ctable_where_leave." AND status=2") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable11,$ctable_where_leave." AND status=3") ?></td>
		  	</tr>
	  	</tbody>
	</table>
	<!-- total leave -->

	<!-- total followup -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
		<h3><b>Followup Statistic</b></h3>
	</div>
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
		<thead>
		   	<tr>
				<th>Total Followup</th>
				<th>Today's Followup</th>
				<th>Future Followup</th>
				<th>Pending Followup</th>
				<th>Responsed Followup</th>
		  	</tr>
		</thead>
		<tbody>
		  	<tr>
				<td><?= $db->rp_getTotalRecord($ctable3,$ctable_where_followup) ?></td>           
				<td><?= $db->rp_getTotalRecord($ctable3,$ctable_where_followup." AND DATE(followup_date)='".date('Y-m-d')."' ") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable3,$ctable_where_followup." AND  DATE(followup_date)>'".date('Y-m-d')."' ") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable3,$ctable_where_followup." AND DATE(followup_date) < '".date('Y-m-d')."' AND response='' ") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable3,$ctable_where_followup." AND DATE(followup_date) < '".date('Y-m-d')."' AND response!=''",0) ?></td>
		  	</tr>
		</tbody>
	</table>
	<!-- total followup -->

	<!-- total Attandenace -->
	<div class="col-md-12 col-xs-12 col-sm-12 pad-0" style="margin-bottom: 20px;">
	  <h3><b>Attendance</b></h3>
	</div>
	<table class="table table-striped table-bordered mt-15 ">
	  	<thead>
		  	<tr>
			  <th>Total Attendance</th>
			  <th>In</th>
			  <th>Out</th>
		  	</tr>
	  	</thead>
	  	<tbody>
			<tr>
				<td><?= $db->rp_getTotalRecord($ctable,$ctable_where_attendance) ?></td>
				<td><?= $db->rp_getTotalRecord($ctable,$ctable_where_attendance." AND inout_status='in' ") ?></td>
				<td><?= $db->rp_getTotalRecord($ctable,$ctable_where_attendance." AND inout_status='out' ") ?></td>
			</tr>
	  	</tbody>
	</table>
	<!-- total Attandenace -->
</div>