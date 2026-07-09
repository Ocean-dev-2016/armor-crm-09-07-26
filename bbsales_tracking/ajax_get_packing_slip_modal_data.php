<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$_REQUEST['dispatch_id'] = implode(",", $_REQUEST['dispatch_id']);
$items = $db->rp_getData("dispatch_item","pro_id,weight_id,pro_name,SUM(qty) AS qty","dispatch_id IN(".$_REQUEST['dispatch_id'].") GROUP BY pro_id,weight_id","",0);
?>
<tr class="remove-this-before-save-click">
	<th>Sr. No.</th>
	<th>Item</th>
	<th>Requested Qty</th>
	<th>Packing Qty</th>
	<th>KGs</th>
</tr>
<tr>
	<td colspan="4">
		<b><?=$_REQUEST['packing_type_name']?> No : </b><?=$_REQUEST['total_package']?>
		<input data-total-packing="<?=$_REQUEST['total_package']?>" type="hidden" name="main_carton_type[]" class="form-control main-carton-array" value="<?=$_REQUEST['packing_type']?>">
		<input data-total-packing="<?=$_REQUEST['total_package']?>" type="hidden" name="main_carton_type_name[]" class="form-control" value="<?=$_REQUEST['packing_type_name']?>">
		<input data-total-packing="<?=$_REQUEST['total_package']?>" type="hidden" name="main_carton_type_count[]" class="form-control" value="<?=$_REQUEST['total_package']?>">
	</td>
</tr>
<?php
if($items)
{
	$count = 0;
	$totalProductQty = 0;
	while ($itemData = mysqli_fetch_assoc($items))
	{
		$totalProductQty = $totalProductQty+$itemData['qty'];
		$count++;
?>
<tr>
	<td style="width: 10%">
		<button data-total-packing="<?=$_REQUEST['total_package']?>" type="button" class="remove-this btn btn-danger btn-sm" style="padding: 5px 10px 5px 10px"><i class="fa fa-trash"></i> <b style="font-size: 14px!important" class="counter-plus-for-item" data-total-packing="<?=$_REQUEST['total_package']?>"><?=$count?></b></button>
	</td>
	<td>
		<?=$itemData['pro_name']?>
		<input data-total-packing="<?=$_REQUEST['total_package']?>" type="hidden" name="pro_name[<?=$_REQUEST['total_package']?>][]" class="form-control" value="<?=$itemData['pro_name']?>">
		<input data-total-packing="<?=$_REQUEST['total_package']?>" type="hidden" name="pro_id[<?=$_REQUEST['total_package']?>][]" class="form-control" value="<?=$itemData['pro_id']?>">
		<input data-total-packing="<?=$_REQUEST['total_package']?>" type="hidden" name="weight_id[<?=$_REQUEST['total_package']?>][]" class="form-control" value="<?=$itemData['weight_id']?>">
	</td>
	<td><?=$itemData['qty']?></td>
	<td style="width: 25%">
		<input data-total-packing="<?=$_REQUEST['total_package']?>" data-pro-id="<?=$itemData['pro_id']?>" data-pro-weight="<?=$itemData['weight_id']?>" data-change-poduct-weight-id="<?=$itemData['pro_id'];?>##<?=$itemData['weight_id'];?>"  type="text" name="pro_qty[<?=$_REQUEST['total_package']?>][]" class="form-control product-qty"  value="0.000">
	</td>
	<td style="width: 25%" class="text-right">
		<input data-total-packing="<?=$_REQUEST['total_package']?>" data-pro-id="<?=$itemData['pro_id']?>" data-pro-weight="<?=$itemData['weight_id']?>" type="text" name="pro_weight[<?=$_REQUEST['total_package']?>][]" class="form-control product-weight" value="0.000">
	</td>
</tr>
<?php
	}
}
?>
<tr style="background: #eee!important">
	<td colspan="3" class="text-right"><b><?=$_REQUEST['packing_type_name']?> Weight</b></td>
	<td class="text-center">&nbsp;</td>
	<td class="text-right">
		<input data-total-packing="<?=$_REQUEST['total_package']?>" type="text" readonly="" name="main_carton_type_weight[<?=$_REQUEST['total_package']?>][]" class="form-control product-weight" value="<?=round($_REQUEST['packing_type_weight'],3)?>">
	</td>
</tr>
<tr style="background: #eee!important">
	<td colspan="3" class="text-right"><b>Total</b></td>
	<td class="text-center">
		<input data-total-packing="<?=$_REQUEST['total_package']?>" type="text" readonly="" class="form-control product-all-total-qty" value="<?=round($totalProductQty,3)?>">
	</td>
	<td class="text-right">
		<input data-total-packing="<?=$_REQUEST['total_package']?>" type="text" readonly="" class="form-control product-all-total-weight" value="0.000">
	</td>
</tr>
<tr>
	<td colspan="3" class="text-right"><b>Actual Weight</b></td>
	<td class="text-center">&nbsp;</td>
	<td class="text-right">
		<input data-total-packing="<?=$_REQUEST['total_package']?>" name="main_carton_whole_actual_weight[<?=$_REQUEST['total_package']?>][]" type="text" class="form-control product-all-total-weight-actual" value="0.000">
	</td>
</tr>
<?php require_once 'disconnect.php';  ?>