<?php
/*
 * @author Ravi Patel
 */
$page_id=567;$page_slug='page_product_final';
include("connect.php");
$ctable = "product";
$ctable1 = "Product";
include('../include/product.class.php');
$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
$product=new Product();
$ctable_where = "";
$date=date('d_M_Y');
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " name like '%".$_REQUEST['searchName']."%' AND ";
}

$ctable_where .= " isDelete='0' AND id!='0'";
//for category//
if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="" && $_REQUEST['cid']!=NULL)
{
 $ctable_where .= " AND cid = '".$_REQUEST['cid']."' ";
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
$product_list_d=$db->rp_getData('product',"*",$ctable_where,"",0);
?>
<button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i>&nbsp; Export</button>

<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i> &nbsp; Excel</button>

<form action="" name="frm" id="print_info" method="post">
			<table id="datatable_1" class="table table-bordered table-striped dataTable">
	<thead>
	<tr>
		<td class="header" align="center" colspan="6"><h3><b>PRODUCT STOCK REPORT -<?php echo $date;?></b><h3/></td>
	</tr>
		<tr>
			<th>No.</th>
			<th>Name</th>
			<th>Weight</th>
			<th>Category</th>
			<th style="text-align:right">Price(INR)</th>
			<th style="text-align:right">Quantity(Nos)</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$count = 0;
	$final_price=0;
	$price=0;
	while($product_list_r=mysqli_fetch_assoc($product_list_d))
	{
	
	$current_prodcuts=$product->aj_getProductDetail($product_list_r['id'],$uid);
	//print_r($current_prodcuts);exit;
	if(!empty($current_prodcuts))
	{
		
		foreach($current_prodcuts as $product_detail)
		{
		$category_name=$db->rp_getValue("category_master","name","id='".$product_detail['cid']."'",0);
	$count++;
	?>
		<tr>
			<td><?php echo $count; ?></td>
			<td><?php echo $product_list_r['name']; ?></td>
			<td><?php echo $product_detail['title']; ?></td>
			<td><?php echo $category_name; ?></td>
			<?php
			if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
			{
				$discount=$db->rp_getValue("executive","discount","id=".$uid."",0);
				$price=($product_detail['price']*$discount)/100;
				$final_price=$product_detail['price']-$price;
				?>
				<td align="right"><?php echo $final_price;  ?></td>
				<?php
			}
			else{
				?>
				<td align="right"><?php echo $product_detail['price'];  ?></td>
				<?php
			}
			?>
			
			<td align="right"><?php echo $product_detail['stock_qty']; ?></td>
		</tr>
	<?php
		}
	}
	}
	?>
	</tbody>
	</table>
	
</form>	
<div id="loading" class="modal fade"  data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box white">
				<div class="portlet-title">
					<div class="caption" style="color:black">
					Loading.......<img src="../images/loading-spinner-blue.gif">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<button type="button" class="btn red-haze export" name="export" onClick="genReport(this)" id="export" href="" title="Download PDF Report"><i class="fa fa-file-pdf-o"></i>&nbsp; Export</button>
<button type="button" class="btn green-haze export" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i> &nbsp; Excel</button>
<script>
function genReport(cid){
	if($("#datatable_1").find("tbody").find("tr").length>=2)
	{
	var rc = encodeURIComponent($("#print_info").html());
	//alert(rc);
	$.ajax({
		type: "POST",
		url: "productStockReport_gen_ajax.php",
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
		
