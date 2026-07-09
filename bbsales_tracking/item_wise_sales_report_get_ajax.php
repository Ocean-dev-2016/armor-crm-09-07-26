<?php
$page_id=630;$page_slug='to_do_list';
include("connect.php");
include('../include/product.class.php');
$product=new Product();
$ctable 	= "product";
$ctable1 	= "Attendance";
$sales_id=$_REQUEST['sales_id'];
$ctable_where = "";


if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id']!="")
{
	$query = "SELECT pro_id,weight_id from order_product_item WHERE isDelete=0 AND order_id IN (select id from orders WHERE isDelete=0 AND customer_id=".$_REQUEST['customer_id'].") GROUP BY pro_id,weight_id";
	$ordered_item = $db->rp_getQuery($query);
	// echo $ordered_item;exit;
	while($ordered_item_d = mysqli_fetch_assoc($ordered_item))
	{
		$ordered_item_drr[] = $ordered_item_d['pro_id'];
		// $ordered_item_drr['weight_id'][] = $ordered_item_d['weight_id'];
	}
	$ordered_item_str = implode(",", array_filter($ordered_item_drr));
	$ctable_where .= " id IN (".$ordered_item_str.") AND ";
	// print_r($ordered_item_str);exit();
}

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
	$where11="";
	$pro_r1=$db->rp_getData("product_weight_price","product_id","catno LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	$PROIDS1=array();
	if($pro_r1)
	{
		while($pro_d1=mysqli_fetch_assoc($pro_r1))
		{
			$PROIDS1[]=$pro_d1['product_id'];
		}
	}
	if(!empty($PROIDS1))
	{
		$PROIDS1=implode(",", $PROIDS1);
		$where11=" OR id IN (".$PROIDS1.")";
	}
	$ctable_where .= " (LOWER(name) like '%".strtolower(trim($_REQUEST['searchName']))."%' ".$where11.") AND ";
}

if(isset($_REQUEST['item_name']) && $_REQUEST['item_name']!="")
{
    $ctable_where .= " (name like '%".$db->clean($_REQUEST['item_name'])."%') AND ";
}

if(isset($_REQUEST['top_category_id']) && $_REQUEST['top_category_id']!="")
{
    $ctable_where .= " tcid='".$_REQUEST['top_category_id']."' AND ";
}
if(isset($_REQUEST['category_id']) && $_REQUEST['category_id']!="")
{
    $ctable_where .= " cid='".$_REQUEST['category_id']."' AND ";
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"]))
{
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}
else
{
	$page_number = 1; //if there's no page number, set it to 1
}

$ctable_where .= " isDelete=0 ";

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable

//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);
 
$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);


// get approve invoice 
$invoice_where="";
if(isset($_REQUEST['customer_id']) && $_REQUEST['customer_id']!="")
{
    $invoice_where = " customer_id='".$_REQUEST['customer_id']."' AND ";
}
if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
    //echo $_REQUEST['df'];exit;
    $date_filter_query = urldecode( $_REQUEST['df'] );

    $date_filter_query_ex=explode(" to ",$date_filter_query);

    $invoice_where .= "( DATE(invoice_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(invoice_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."') AND ";
}
// $invoice_r = $db->rp_getData("invoice_new","*",$invoice_where."isDelete=0 AND status=1");
// if($invoice_r)
// {
// 	while($invoice_d = mysqli_fetch_assoc($invoice_r))
// 	{
// 		$invoiceIds[] = $invoice_d['id'];
// 	}
// }
// $invoiceId = implode(",",$invoiceIds);
// get approve invoice
?>
<style type="text/css">
	.fix-th
	{
		background-color: #f5f5f5 !important;position: sticky;top: 0; z-index: 1;
	}
	.fix-th1
	{
		background-color: #e5e5e5 !important;position: sticky;top: 0; z-index: 1;
	}
</style>
	 
<div class="table-responsive" style="height: 600px;">
	<form id="print_info">
		<h3><strong>Customer Name: </strong><?= $db->rp_getValue("executive","company_name","isDelete=0 AND id='".$_REQUEST['customer_id']."'"); ?></h3>
	<table id="Product_stock" class="table table-striped table-bordered table-hover" >
        <thead class="fix-th">
        	
            <tr>
                <th class="fix-th1" style="width: 5%;">No</th>
                <th class="fix-th1">Category</th>
                <th class="fix-th1">Sub Category</th>
                <th class="fix-th1">Item Name</th>
                <th class="fix-th1">Item Code</th>
                <th class="fix-th1">Item Qty</th>
                <th class="fix-th1">Amount Without GST(&#x20B9;)</th> 
			</tr>
        </thead>
        
        <tbody>
	        <?php
	        if(mysqli_num_rows($ctable_r)>0)
	        { 
	            $count = 0; 
				while($ctable_d = mysqli_fetch_assoc($ctable_r))
				{ 
					$current_prodcuts=$product->aj_getProductDetail($ctable_d['id'],$uid);		
					if(!empty($current_prodcuts))
					{
						$sales_amount=0;
						$sales_qty=0;
						foreach($current_prodcuts as $product_detail)
						{   
							$invoice_item_where = "order_id IN(select id from orders WHERE isDelete=0 AND customer_id=".$_REQUEST['customer_id'].") AND weight_id='".$product_detail['weight_id']."' AND pro_id='".$ctable_d['id']."' AND isDelete=0";

							$sales_amount = $db->rp_getValue("order_product_item","SUM(totalprice)",$invoice_item_where,0);
							$sales_qty = $db->rp_getValue("order_product_item","SUM(pro_qty)",$invoice_item_where);
							
							$sales_amount=($sales_amount)?$sales_amount:0;
							$sales_qty=($sales_qty)?$sales_qty:0;

							$top_category_name=$db->rp_getValue("top_category_master","name","id='".$ctable_d['tcid']."'");
							$category_name=$db->rp_getValue("category_master","name","id='".$ctable_d['cid']."'");
			?>
			<tr>
				<td><?php echo ++$count; ?></td>
      			<td><?= $top_category_name; ?></td>
      			<td><?= $category_name; ?></td>
  				<td><?php echo $ctable_d['name']; ?> </td> 
      			<td><?php echo $product_detail['catno']; ?></td>
      			<td><?= $sales_qty; ?></td>
      			<td><?= $db->rp_number_format($sales_amount,2); ?></td>			          			 
			</tr>
			<?php                           
							$totQty += $sales_qty;
							$totAmt += $sales_amount;
						}
					}				 
				}
        	}
			else
			{
			?>
			<tr>
				<td align="center" colspan="6"><?php echo "No Data Found";?></td>
			</tr>
			<?php
			}
			?>
        </tbody> 
        <tfoot>
        	<tr>
        		<th colspan="4"></th>
        		<th>Total</th>
        		<th><?= $totQty; ?></th>
        		<th><?= $db->rp_number_format($totAmt,2); ?></th>			          			 
        	</tr>
        </tfoot>
    </table>
</form>
</div> 
<?php require_once("disconnect.php"); ?>