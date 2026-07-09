<?php
$page_id=566;$page_slug='page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");
include("../include/no_to_word.php");
$ctable 	= "orders";
$ctable1 	= "Orders";
//$uid=$_REQUEST['uid'];
$ctable_where = "";

// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							customer_name like '%".$db->clean($_REQUEST['searchName'])."%' OR
							order_no like '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";
}
if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!=""){
	$ctable_where .= "  sales_id = '".$db->clean($_REQUEST['sales_executive_id'])."' AND";
}
//for admin login
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
{
	/*if($_REQUEST['order_type'])
	{
	$ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'";
	}
	else
	{*/
	$ctable_where .= " isDelete=0 ";	
	//}
	
}
// for customer login
else
{
	/*if($_REQUEST['order_type'] && $_REQUEST['uid'])
	{
	$ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."' AND customer_id='".$_REQUEST['uid']."'";
	}
	else*/ if($_REQUEST['order_type'])
	{
		$ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'";
	}
	else if($_REQUEST['order_type'])
	{
		$ctable_where .= " isDelete=0 AND customer_type='".$_REQUEST['order_type']."'";
	}
	else{
		$ctable_where .= " isDelete=0";
	}
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
//For ToDate & FromDate
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
  $ctable_where .= " AND order_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
     $ctable_where .= " AND order_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
//for class----//
if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="" && $_REQUEST['class_id']!=NULL)
{
	 $ctable_where .= " AND city = '".$_REQUEST['class_id']."' ";
}
// --for customer Type//
//if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
//{
 $ctable_where .= " AND customer_type = 'normal_user' ";
//}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

if($_REQUEST['ToDate'] && $_REQUEST['FromDate']){
	$date=$_REQUEST['FromDate']." to ".$_REQUEST['ToDate'];
}
else{
	$maxdate=$db->rp_getMaxVal($ctable,"order_date","isDelete=0");
	$mindate=$db->rp_getMinVal($ctable,"order_date","isDelete=0");
	$date=date("d-m-Y",strtotime($mindate))." to ".date("d-m-Y",strtotime($maxdate));
}


$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>
<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i> &nbsp; Excel</button>
<div class="table-responsive">
<form action="" name="frm" id="print_info" method="post">
	<table id="datatable_1" class="table table-bordered table-striped dataTable" style="overflow: scroll; !important">
        <thead>
			<tr>
				<td class="header" align="center" colspan="10" ><h3><b>ORDER REPORT -<?php echo $date;?></b></h3></td>
			</tr>
            <tr class="tr">
				
                <th class="th">No.</th>
                <th class="th">Order No.</th>
                <th class="th">Company Name</th>
                <th class="th">Customer Name</th>
				<th class="th">City</th>
				<th class="th">Order Date</th>
				<th class="th">Order Amount</th>
				<th class="th">Product</th>
				
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 1;
            $sales_name='';
            while($ctable_d = mysqli_fetch_array($ctable_r)){
			/* $customer=$db->rp_getValue('executive','isActive',"id=".$ctable_d['customer_id']."",0);
			if($customer==0)
			{
				continue;
			}*/
			
        ?>
            <tr class="tr">
                <td class="td" style="width:5px;"><?php echo $count++; ?></td>
                <td class="td"><?php echo stripslashes($ctable_d['order_no']); ?></td>
				<td><?php echo $ctable_d['company_name']; ?></td>
                <td class="td"><?php echo stripslashes($ctable_d['customer_name']); ?></td>
				<?php
					$sales_name=$db->rp_getValue("sales_executive","name","id='".$ctable_d['sales_id']."'");
				?>
              
				<td>
					<?php
				
					
					echo $ctable_d['city']; ?>
					</td>
                <td class="td"><?php echo date('d-m-Y',strtotime($ctable_d['order_date'])); ?></td>
				<td align="right" class="td"><?php echo stripslashes(CURR.$db->rp_num($ctable_d['grand_total'])); ?></td>
				
             <td class="td">
				<?php
				//$order_id=$_REQUEST['id'];
				$ctable_where_p	= "order_id='".$ctable_d['id']."' AND isDelete=0";
				$ctable_p = $db->rp_getData("order_product_item","*",$ctable_where_p,"",0);
	
				?>
				<table style="width:300px;" class="table1 table-bordered table-striped dataTable">
	<thead>
	<?php
        if($ctable_p){
			?>
            <tr class="tr1">
                <th class="th1">Product Name</th>
                <th class="th1">Price</th>
                <th class="th1">Qty</th>
            </tr>
        </thead>		
	<tbody>
		<?php
            $total=0;
            while($ctable_pro = mysqli_fetch_array($ctable_p)){
        ?>
			<tr class="tr1">
				<td class="td1"><?php echo $ctable_pro['pro_name'];  ?></td>
				<td class="td1" align="right"><?php echo "Rs. ".$db->rp_num($ctable_pro['unitprice']);  ?></td>
				<td class="td1" align="right"><?php echo  $ctable_pro['pro_qty']; ?></td>
			</tr>
			
			<?php
			$total+=$ctable_pro['totalprice'];
				}
			}else{
				?>
				<td  class="tr1" colspan="3" style="text-align:center;">No Product Order</td>
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
    
</form>
</div>
<div id="loading" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box white">
				<div class="portlet-title" style="color:black;">
					<div class="caption">Loading.......
					<img src="../images/loading-spinner-blue.gif">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i> &nbsp PDF</button>
<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i>&nbsp; Excel</button>
<script>
function genReport(cid){
	if($("#datatable_1").find("tbody").find("tr").length>=2)
	{
	var rc = encodeURIComponent($("#print_info").html());
	
	$.ajax({
		type: "POST",
		url: "ordersReport_gen_ajax.php",
		data: '&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
			$("#loading").modal('show');
		},
		success: function(result){ 
		//alert(result);
				setTimeout(function(){
					$(".transCover").fadeOut(100);
					alert("Report file generated!!");
					$("#loading").modal('hide');
					window.location.href=result;
				},1500);
			}
	});
}
else
{
	toastr.error("Report Can't generated");
}

}
</script>
<?php require_once "disconnect.php"; ?>