<?php
$page_id=569;$page_slug='dispatch_pages'; 
include("connect.php");
 
include("../include/dispatch.class.php");
$objDispatch= new Dispatch();
  
$order_id = $_REQUEST['order_id'];
$warehouse_id = $_REQUEST['warehouse_id'];
$date = date("d-m-Y");
// echo $date;exit;
if (isset($_REQUEST['order_id']) && $_REQUEST['order_id'] > 0) 
{
	$detail['id'] = $order_id;
	$detail['warehouse_id'] = $warehouse_id;
	 
	$reply1=$objDispatch->GetDispatchItems($detail);
	 
	if($reply1['ack']==1)
	{ 
		$item_info=$reply1['result'];
	}
	else
	{
		$item_info=array();
	}
}   

if (!empty($item_info)) 
{

	$count = 0;
	foreach ($item_info as $i) 
	{
		$box_qty += $i['box_qty'];
		$qty_total += $i['qty'];
		$unit_id = $db->rp_getValue("product","unit_id","id='". $i['product_id']."'");
		$unit_name = $db->rp_getValue("unit","name","id='". $unit_id."'");

		if(($i['stock']=="0" || $i['stock']=="") || ($i['stock'] < $i['qty']))
		{
			$style = 'background-color: #f13e46!important';
		}
		else
		{
			$style = "";	
		}

		$cid_id = $db->rp_getValue("top_category_master","id","isDelete=0 AND isActive=1 AND name='".$i['top_cat_name']."'",0);

		$mode="add_manual";
		// print_r($i);exit();
?>
		<tr style='<?=$style ?>'>
			<td><?php echo ++$count; ?><input type='hidden' name='count[]' value="<?php echo $count; ?>" class='count'></td>
			
			<td style="text-align: center;">
				<input type='hidden' name='product_id[]' class='product_id' value="<?php echo $i['product_id']; ?>">
				<input class="pro_id" type='hidden' name='pro_id[]' value="<?php echo $i['product_id'] . "" . $i['weight_id']; ?>">
				<input type='hidden' style="text-align:right" name='subtotal[]' value="">
				<input type='hidden' style="text-align:right" name='total[]' value="">
				<input type='hidden' style="text-align:right" name='item_name[]'>
				<input type='hidden' name='pro_name[]' value="<?php echo $i['product_name']; ?>" id='pro_name'>
				<input type='hidden' name='weight_id[]' value="<?php echo $i['weight_id']; ?>" id='weight_id'>
				<input type='hidden' class="cid" name='cid[]' value="<?php echo $i['cid']; ?>" id='cid'>
				<input class="tcid" type='hidden' name='tcid[]' value="<?php echo $i['tcid']; ?>" id='tcid'>
				<input class="order_item_id" type='hidden' name='order_item_id[]' value="<?php echo $i['order_item_id']; ?>" id='order_item_id'>


				<?php
				if($i['top_cat_name']!="" && $i['category_name']!="")
				{
					echo $i['product_name']." - ".$i['cat_no']." - <br/> <b>T</b> :".$i['top_cat_name']." - <b>C</b> :".$i['category_name'];
				}
				else if($i['top_cat_name']!="")
				{
					echo $i['product_name']." - ".$i['cat_no']." - <br/> <b>T</b> :".$i['top_cat_name'];
				}
				else if($i['category_name']!="")
				{
					echo $i['product_name']." - ".$i['cat_no']." - <br/> <b>C</b> :".$i['category_name'];
				}
				else
				{
					echo $i['product_name']." - ".$i['cat_no'];
				}
				?>
			</td>

			<td style="width: 100px;text-align: center;"><?= $unit_name ?></td>

			<td style='text-align:right'>
				<input type='hidden' name='outer_size' class='outer_size' value="<?php echo $i['outer_size']; ?>">
				<input readonly  type='text' class='form-control box_qty'  style='text-align:right;width:100px;' name='box_qty[]' class='box_qty positive' value="<?php echo $i['box']; ?>">
			</td>


			<td style="text-align:right">
				<input class="inner_size" type='hidden' name='inner_size[]' value="<?php echo $i['inner_size']; ?>">
				<input readonly name='bag[]' class='form-control bag positive' style="text-align:right;width:100px;"  type='text' value="<?php echo $i['bag']; ?>">
			</td>

			<td style='text-align:right'>
				<input type='hidden' name='loose_qty' class='loose_qty' value="<?php echo $i['loose']; ?>">
				<input readonly type='text' class='form-control loose' style='text-align:right;width:100px;' name='loose[]' class='loose positive' value="<?php echo $i['loose']; ?>">
			</td>

			<td style="text-align:right;width: 40px;">
				<input readonly type='text' style="text-align:right;width: 100px;" class='order_qty<?php echo $i['product_id'] . "_" . $i['weight_id']; ?> form-control' name='order_qty[]' value="<?php echo $i['qty']; ?>">
			</td>

			<td style="width: 100px;text-align: center;"><input readonly type='text' style="text-align:right;width: 100px;" class='available_qty<?php echo $i['product_id'] . "_" . $i['weight_id']; ?> form-control' name='available_qty[]' value="<?= $i['stock'] ?>"></td>

			<td style="text-align:right;width: 40px;">
				<input type='text' onChange='Checkstock(this,<?= $i['product_id']?>,<?=$i['weight_id']?>)' style="text-align:right;width: 100px;" class='qty1 qty<?php echo $i['product_id'] . "_" . $i['weight_id']; ?> form-control' name='qty[]' value="<?php echo $i['qty']; ?>">
			</td>
			<td class="text-center">
				<a href="add_manual_stock.php?mode=<?=$mode?>&warehouse_id=<?=$warehouse_id?>&category_id=<?=$cid_id?>&product_id=<?=$i["product_id"]?>&planning_date=<?=$date?>&stock_qty=<?=$i['stock']?>" target="_blank"	class='add_manual btn btn-success btn-sm' title='Add Manual'>Add Stock</a>
			</td>

			<td class="text-center">
				<?php
				$total_dispatch_record = $db->rp_getTotalRecord("dispatch_map_order", "order_id='" . $i['order_id'] . "' AND isDelete=0", 0);
				if ($total_dispatch_record > 0) 
				{

				} 
				else 
				{
					?>
					<a class='delete btn btn-danger btn-sm' title='Delete'><i class='fa fa-times'></i></a>
					<?php
				}
				?>
			</td>
		</tr>
<?php
	}
}  
?> 