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
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " name like '%".$_REQUEST['searchName']."%' AND ";
}

$ctable_where .= " isDelete='0' AND id!='0'";
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
			<table id="datatable_1" class="table table-bordered table-striped dataTable">
	<thead>
		<tr>
			<th>No.</th>
			<th>Name</th>
			<th>Weight</th>
			<th>Category</th>
			<th>Price(INR)</th>
			<th>Quantity(Nos)</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$count = 0;
	$final_price=0;
	$price=0;
	while($product_list_r=mysqli_fetch_assoc($product_list_d))
	{
	$count++;
	$current_prodcuts=$product->aj_getProductDetail($product_list_r['id'],$uid);
	if(!empty($current_prodcuts))
	{
		
		foreach($current_prodcuts as $product_detail)
		{
	$category_name=$db->rp_getValue("category_master","name","id='".$product_detail['cid']."'",0);
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
			
			<td align="right"><?php echo $product_list_r['qty']; ?></td>
		</tr>
	<?php
		}
	}
	}
	?>
	</tbody>
	</table>
	<div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) { echo ' selected="selected"'; }  ?> >500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) { echo ' selected="selected"'; }  ?> >1000</option>
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="dataTables_paginate paging_simple_numbers">
				<ul class="pagination">
				<?php 
				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
				?>
				</ul>
			</div>
		</div>
	</div>	
	<br />
<?php require_once "disconnect.php"; ?>					

		
