<?php
$page_id=623;$page_slug='product_sales_report';
/*
 * @author Ravi Patel
 */
include("connect.php");
include("../include/no_to_word.php");
$ctable 	= "dispatch_detail";
$ctable1 	= "Orders";
$ctable_where = "";
$area=$_REQUEST['area'];
$ctable_where .= " isDelete=0 AND";	

// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (customer_name like '%".$db->clean($_REQUEST['searchName'])."%' OR order_no like '%".$db->clean($_REQUEST['searchName'])."%' OR company_name like '%".$db->clean($_REQUEST['searchName'])."%') AND ";
}

if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="" && $_REQUEST['sales_executive_id']!= "null" && $_REQUEST['sales_executive_id']!=undefined){
	$ctable_where .= "  sales_id IN (".$_REQUEST['sales_executive_id'].") AND";
}

if(isset($_REQUEST['customer_type']) && $_REQUEST['customer_type']!="" && $_REQUEST['customer_type']!="null"){
	$ctable_where .= "  order_type IN (".$_REQUEST['customer_type'].") AND";
}

if(isset($_REQUEST['warehouse_id']) && $_REQUEST['warehouse_id']!="" && $_REQUEST['warehouse_id']!="null"){
	$ctable_where .= "  warehouse_id IN (".$_REQUEST['warehouse_id'].") AND";
}
if(isset($_REQUEST['type_of_company']) && $_REQUEST['type_of_company']!="" && $_REQUEST['type_of_company']!="null"){

	$type_of_company_ids=array();
	$type_of_company_r=$db->rp_getData("executive","id,type_of_company","isDelete=0 AND type_of_company='".$_REQUEST['type_of_company']."'","",0);
	while($type_of_company_d=mysqli_fetch_array($type_of_company_r))
	{
		$type_of_company_ids[]=$type_of_company_d['id'];
	}

	$type_of_company_ids=implode(",",$type_of_company_ids);

	$ctable_where .= "  customer_id IN (".$type_of_company_ids.") AND";
}

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	$ctable_where .= " isDelete=0 AND status!=-1";	
}
else
{
	$ctable_where .= " isDelete=0 AND status!=-1 AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";

}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
//status
if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 $ctable_where .= " AND status = '".$_REQUEST['status']."' ";
}

//for class----//
// if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!=NULL && $_REQUEST['class_id']!="null")
// {
// 	//$ctable_where .= "  class_id = '".$_REQUEST['class_id']."' AND ";
// 	$ctable_where .= " AND  class_id IN (".$_REQUEST['class_id'].") ";
// }
// //for area----//
// if(isset($_REQUEST['area']) && $_REQUEST['area']!="" && $_REQUEST['area']!=NULL && $_REQUEST['area']!="null")
// {
// 	//$ctable_where .= " area_id = '".$_REQUEST['area']."' AND ";
// 	$ctable_where .= " AND area_id IN (".$_REQUEST['area'].") ";
			
// }
if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(dispatch_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(dispatch_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}
// --for customer Type//
//if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
//{
 //$ctable_where .= " customer_type != 'normal_user' ";
//}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

// if($_REQUEST['ToDate'] && $_REQUEST['FromDate']){
// 	$date=$_REQUEST['FromDate']." to ".$_REQUEST['ToDate'];
// }
// else{
// 	$maxdate=$db->rp_getMaxVal($ctable,"order_date","isDelete=0");
// 	$mindate=$db->rp_getMinVal($ctable,"order_date","isDelete=0");
// 	$date=date("d-m-Y",strtotime($mindate))." to ".date("d-m-Y",strtotime($maxdate));
// }


$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<!-- <button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>
<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i> &nbsp; Excel</button> -->
<style>
.mainDiv, table{
  height: auto;   
  width:100%;
  font-family: Calibri,sans-serif;
  font-style: normal;
  font-weight: 400;
  padding: 0;
  text-decoration: none;
  font-size: 10pt;
  margin:auto;
  padding:auto;
}
tr{height: 30px;}
table , td, th { border-collapse: collapse;border: 1px solid #000;}
td, th {  padding: 5px;}
th { border: 1px solid #595959;background: #f0e6cc;}
.text-right{text-align: right;}
.center{text-align:center;}
.space{ padding: 10px;}
.no-border{border-bottom: 1px solid #fff;}
h2
{
	text-transform: uppercase;
	margin-bottom: 0px;
}
</style>
<!-- <button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>
<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i> &nbsp; Excel</button> -->
<!-- <div class="table-responsive">
<form action="" name="frm" id="print_info" method="post"> -->
	<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll; !important">
        <thead>
			<tr>
        		<th colspan="12" class="center"><h2>Dispatch Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2></th>
        	</tr>
            <tr class="tr">
				
                <th class="th">Sr. No.</th>
	            <th class="th">Order No.</th>
	            <th class="th">Dispatch No.</th>
	            <th class="th">Company Name</th>
	            <th class="th">Person Name</th>
	            <th class="th">Sales Person Name</th>
	            <th class="th">Customer Type</th>
	            <th class="th">Order Date</th>
	            <th class="th">Dispatch Date</th>
	            <th class="th">Warehouse</th>
				<th class="th">Product</th>
				
            </tr>
        </thead>
       <tbody>
			<?php
			if(mysqli_num_rows($ctable_r)>0)
			{
				$count = 1;
				$sales_name='';
				while($ctable_d = mysqli_fetch_array($ctable_r))
				{
					?>
						<tr class="tr">
							<td class="td" style="width:5px;"><?php echo $count++; ?></td>
							<td class="td"><?php echo stripslashes($ctable_d['order_no']); ?></td>
							<td class="td"><?php echo stripslashes($ctable_d['dispatch_no']); ?></td>
							<td><?php echo $db->rp_getValue('executive','company_name',"id=".$ctable_d['customer_id']."",0); ?></td>
							<td class="td"><?php echo stripslashes($ctable_d['customer_name']); ?></td>
							<td class="td"><?= $db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_id']."'"); ?></td>
							<td class="td"><?= $db->rp_getValue("customer_type","name","id='".$ctable_d['order_type']."'"); ?></td>
							<td><?php echo date('d-m-Y',strtotime($db->rp_getValue('orders','order_date',"id=".$ctable_d['order_id']."",0))); ?></td>
							<td class="td"><?php echo date('d-m-Y',strtotime($ctable_d['dispatch_date'])); ?></td>
							<td class="td"><?= $db->rp_getValue("warehouse","name","id='".$ctable_d['warehouse_id']."'"); ?></td>
							<td class="td">
								<?php
								$ctable_where_p	= "dispatch_id='".$ctable_d['id']."' AND isDelete=0";
								$ctable_p = $db->rp_getData("dispatch_item","*",$ctable_where_p,"",0);
								?>
								<table style="width:300px;" class="table1 table-bordered table-striped dataTable">
									<thead>
										<?php
										if($ctable_p)
										{
											?>
											<tr class="tr1">
								                <th class="th1">Product Name</th>
								                <th class="th1">Qty</th>
								            </tr>
											<?php
										}
										?>
									</thead>
									<tbody>
										<?php
								        $total=0;
								        while($ctable_pro = mysqli_fetch_array($ctable_p))
								        {
								        	?>
								        	<tr class="tr1">
												<td class="td1"><?php echo $ctable_pro['pro_name'];  ?></td>
												<td class="td1" align="right"><?php echo  $ctable_pro['qty']; ?></td>
											</tr>
								        	<?php
								        }
								        ?>
									</tbody>
								</table>
							</td>
						</tr>
					<?php
				}
			}
			?>
		</tbody>
    </table>
