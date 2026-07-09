<?php
$page_id=400;$page_slug='dashboard';
include("connect.php"); 

 
if(isset($_REQUEST['moving_days']) && $_REQUEST['moving_days']!="" && $_REQUEST['moving_days']!=NULL)
{
	$date1 = date('Y-m-d');
	$prev_days_date = date('Y-m-d', strtotime($date1. ' - '.$_REQUEST['moving_days'].' days')); 
	$order_Where = " AND order_date>='".$prev_days_date."'";

	$order_r=$db->rp_getData("orders","id","isDelete=0".$order_Where,"",0);
	if($order_r)
	{
		while($order_d = mysqli_fetch_assoc($order_r))
		{
			if(!in_array($order_d['id'], $order_ids))
			{
				$order_ids[] = $order_d['id'];
			}
		}
	}
	$oids = implode(",",$order_ids);
	// echo $oids;

	$order_item_r=$db->rp_getData("order_product_item","*","isDelete=0 AND order_id IN(".$oids.") GROUP BY pro_id","",0);
	if($order_item_r)
	{ 
		while($order_item_d = mysqli_fetch_assoc($order_item_r))
		{
			if(!in_array($order_item_d['pro_id'], $productIds))
			{
				$productIds[] = $order_item_d['pro_id'];
			}
		}
	}

	$ctable_where = "isDelete=0 AND ";
	if(isset($_REQUEST['top_category_id']) && $_REQUEST['top_category_id']!="" && $_REQUEST['top_category_id']!=NULL && $_REQUEST['top_category_id']!=undefined)
	{
	 	$ctable_where .= "tcid = '".$_REQUEST['top_category_id']."' AND "; 
	}

	$pids = implode(",",$productIds);
	$product_r=$db->rp_getData("product","*",$ctable_where."id IN(".$pids.")","",0);
	$product_r1=$db->rp_getData("product","*",$ctable_where."id NOT IN(".$pids.")","",0);
?>
<style type="text/css"> 
	.xtbl tbody {
	    display: block;
	    height: 450px;
	    overflow: auto;
	}
	.xtbl thead, .xtbl tbody tr {
	    display: table;
	    width: 100%;
	    table-layout: fixed;/* even columns width , fix width of table too*/
	}
	.xtbl thead {
	    width: calc( 100% - 0.5em )/* scrollbar is average 1em/16px width, remove it from thead width */
	}
	.xtbl{width: 100%;border: 1px solid;}

</style>
<br/>
<div class="col-md-6">
	<table class="table table-borderd xtbl">
		<thead>
			<tr><th colspan="2" class="text-center"><h4><strong>Moving Product</strong></h4></th></tr>
			<tr>
				<!-- <th width="10%">Sr No.</th>  -->
				<th>Category</th> 
				<th>Product Name</th> 
			</tr>
		</thead>
		<tbody>
			<?php
			if($product_r)
			{
				$c=0;
				while($product_d = mysqli_fetch_assoc($product_r))
				{
				$c++;
			?>
			<tr>
				<!-- <td><?= $c ?></td> -->
				<td><i class="fa fa-circle" style="font-size: 8px"></i>
					<?= $db->rp_getValue("top_category_master","name","id='".$product_d['tcid']."'"); ?>
				</td>
				<td> 
					<?= $product_d['name'] ?> (#<?= $db->rp_getValue("product_weight_price","catno","product_id='".$product_d['id']."'"); ?>)
				</td>
			</tr>
			<?php
				}
			}
			else
			{
			?>
			<tr><td colspan="2" class="text-center">No Data Found!!</td></tr>
			<?php
			}
			?>
		</tbody>
	</table>
</div>
<div class="col-md-6">
	<table class="table table-borderd xtbl">
		<thead>
			<tr><th colspan="2" class="text-center"><h4><strong>Non Moving Product</strong></h4></th></tr>
			<tr>
				<!-- <th width="10%">Sr No.</th>  -->
				<th>Category</th>
				<th>Product Name</th> 
			</tr>
		</thead>
		<tbody>
			<?php
			if($product_r1)
			{
				$c1=0;
				while($product_d1 = mysqli_fetch_assoc($product_r1))
				{
				$c1++;
			?>
			<tr style="display:table">
				<!-- <td><?= $c1 ?></td> -->
				<td><i class="fa fa-circle" style="font-size: 8px"></i>
					<?= $db->rp_getValue("top_category_master","name","id='".$product_d1['tcid']."'"); ?>
				</td>
				<td> 
					<?= $product_d1['name'] ?> (#<?= $db->rp_getValue("product_weight_price","catno","product_id='".$product_d1['id']."'"); ?>)
				</td>
			</tr>
			<?php
				}
			}
			else
			{
			?>
			<tr><td colspan="2" class="text-center">No Data Found!!</td></tr>
			<?php
			}
			?>
		</tbody>
	</table>
</div>
<?php
}
else
{
?>
<div class="col-md-12 text-center"><h3>Enter Day To See Result</h3></div>
<?php
}
<?php require_once "disconnect.php"; ?>
