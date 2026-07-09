<?php
/*
 * @author Ravi Patel
 */
 $page_id=559;$page_slug='page_product';
include("connect.php");
$ctable = "product";
$ctable1 = "Product";
$ctable_where = "";
// Get the total number of rows in the table
$date=date('d_M_Y');
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " name like '%".$_REQUEST['searchName']."%' AND ";
}

$ctable_where .= " 1=1 AND isDelete='0' AND id!='0'";
if(isset($_REQUEST['category_id']) && $_REQUEST['category_id']!="" && $_REQUEST['category_id']!=NULL)
{
 $ctable_where .= " AND cid = '".$_REQUEST['category_id']."' ";
}
$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 10;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id ASC limit $page_position, $item_per_page");
?>
<button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>

<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i> &nbsp; Excel</button>
<form action="" name="frm" id="print_info" method="post">
<table id="datatable_1" class="table table-bordered table-striped dataTable">
	<thead>
	<tr>
	<td class="header" align="center" colspan="5"><h3><b>ORDER BASED PRODUCT REPORT -<?php echo $date;?></b></h3></td>
	</tr>
		<tr>
			<th>No.</th>
			<th>Name</th>
			<th>Category</th>
			<th style="text-align:right">Ordered Quantity</th>
			<th style="text-align:right">Ordered Amount</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if($ctable_r){
	if(mysqli_num_rows($ctable_r)>0){
		$count = 0;
		
		while($ctable_d = mysqli_fetch_array($ctable_r)){
			$count++;
			$category_name=$db->rp_getValue("category_master","name","id='".$ctable_d['cid']."'");
	?>
		<tr>
			<td><?php echo $count; ?></td>
			<td><?php echo stripslashes($ctable_d['name']); ?></td>
			<td><?php echo $category_name; ?></td>
			<?php
			$pro_qty=$db->rp_getData("order_product_item","SUM(pro_qty) as qty,SUM(totalprice) as price","pro_id=".$ctable_d['id']." AND isDelete=0","",0);
			$ctable_pro = mysqli_fetch_array($pro_qty);
			?>
			<td align="right"><?php
			if($ctable_pro['qty']!=0)
			{				
			echo $ctable_pro['qty'];
			}
			else{
			echo "0";
			}	
			?></td>
			<td align="right"><?php 
			if($ctable_pro['price']!=0)
			{	
			echo $ctable_pro['price'];
			}
			else{
			echo "0";
			}
			?></td>
			
		</tr>
	<?php
		}
	}
	}else{
			?>
			<tr>
			<td colspan="6"><p style="text-align:center;">No data available in table</p></td>
			</tr>
			<?php
		}
	?>
	</tbody>
	</table>
	
	</form>
<div id="loading" class="modal fade"  data-backdrop="static" data-keyboard="false">
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
<button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</button>
<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i> &nbsp; Excel</button>
<script>

function genReport(cid){
	if($("#datatable_1").find("tbody").find("tr").length>=2)
	{
	var rc = encodeURIComponent($("#print_info").html());
	
	$.ajax({
		type: "POST",
		url: "productReport_gen_ajax.php",
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