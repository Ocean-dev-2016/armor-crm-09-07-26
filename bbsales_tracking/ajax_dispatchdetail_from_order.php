<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
// echo "hello";
$_REQUEST['dispatch_id'] - implode(",", $_REQUEST['dispatch_id'])
?>
<table class="table table-borderd">
	<tr>
		<th style="width: 5%;">Sr. No.</th>
		<th style="width: 25%;">Product Name</th>
		<th style="width: 5%;">Unit</th>
		<th style="width: 5%;">HSN Code</th>
		<th style="width: 5%;">Stock</th>
		<th style="width: 5%;" class="text-right">Qty</th>
		<th style="width: 5%;" class="text-right">Rate</th>
		<th style="width: 10%;" class="text-right">Discount</th>
		<th style="width: 10%;" class="text-right">Price</th>
		<th style="width: 10%;" class="text-right">Taxable Amount</th>
	</tr>
	<?php
		$totalAmount = 0;
		$items = $db->rp_getData("dispatch_item","*","dispatch_id IN(".$_REQUEST['dispatch_id'].") AND dispatch_item_type=1","",0);
		if($items)
		{
			$count=0;
			$cgst_tax=0;
			$sgst_tax=0;
			$igst_tax=0;
			while ($item=mysqli_fetch_assoc($items)) 
			{
				$count++;
				//$rate=$item['qty']*$item['unitprice'];
				$rate=$item['original_price'];
				$cgst_tax+=$item['cgst_tax'];
				$sgst_tax+=$item['sgst_tax'];
				$igst_tax+=$item['igst_tax'];
				$tax=$item['cgst_tax']+$item['sgst_tax']+$item['igst_tax'];
				$hsncode=$db->rp_getValue("product","hsn_code","id='".$item['pro_id']."' AND isDelete=0",0);
				$stock=$db->rp_getValue("product_weight_price","stock_qty","product_id='".$item['pro_id']."' AND isDelete=0",0);
				$unit_id = $db->rp_getValue("product","unit_id","id='". $item['pro_id']."'");
				$unit_name = $db->rp_getValue("unit","name","id='". $unit_id."'");

				$order_id = $db->rp_getValue("dispatch_detail","order_id","id='".$_REQUEST['dispatch_id']."' AND isDelete=0",0);
				
				// $igst_amount = $db->rp_getValue("dispatch_detail","igst_amount","id='".$_REQUEST['dispatch_id']."' AND isDelete=0",0);
				// if($igst_amount == ""){
				$igst_amount = $db->rp_getValue("orders","igst_amount","id='".$order_id."' AND isDelete=0",0);
				// }

				if($_REQUEST['invoice_id']!="")
				{
					$transport_charge = $db->rp_getValue("invoice_new","transport_charge","id='".$_REQUEST['invoice_id']."' AND isDelete=0",0);
					$packing_charge = $db->rp_getValue("invoice_new","packing_charge","id='".$_REQUEST['invoice_id']."' AND isDelete=0",0);
				}
				else
				{
					$transport_charge = $db->rp_getValue("orders","transport_charge","id='".$order_id."' AND isDelete=0",0);
					$packing_charge = $db->rp_getValue("orders","packing_charge","id='".$order_id."' AND isDelete=0",0);
				}

				if($_REQUEST['invoice_id']!="")
				{
					$cash_discount = $db->rp_getValue("invoice_new","cash_discount","id='".$_REQUEST['invoice_id']."' AND isDelete=0",0);
				
					$cash_discount_amount = $db->rp_getValue("invoice_new","cash_discount_amount","id='".$_REQUEST['invoice_id']."' AND isDelete=0",0);

					$additional_discount = $db->rp_getValue("invoice_new","additional_discount","id='".$_REQUEST['invoice_id']."' AND isDelete=0",0);
					
					$additional_discount_amount = $db->rp_getValue("invoice_new","additional_discount_amount","id='".$_REQUEST['invoice_id']."' AND isDelete=0",0);
				}
				else
				{
					$cash_discount = $db->rp_getValue("orders","cash_discount","id='".$order_id."' AND isDelete=0",0);
				
					$cash_discount_amount = $db->rp_getValue("orders","cash_discount_amount","id='".$order_id."' AND isDelete=0",0);

					$additional_discount = $db->rp_getValue("orders","additional_discount","id='".$order_id."' AND isDelete=0",0);
					
					$additional_discount_amount = $db->rp_getValue("orders","additional_discount_amount","id='".$order_id."' AND isDelete=0",0);
				}
				

				//$packing_charge = $db->rp_getValue("orders","packing_charge","id='".$order_id."' AND isDelete=0",0);

				$customer_type = $db->rp_getValue("dispatch_detail","order_type","id='".$_REQUEST['dispatch_id']."' AND isDelete=0",0);
				if($customer_type==7)
				{
					$GST = 0.1;
				}
				else
				{
					$GST = 18;
				}
				$tcs_amount = $db->rp_getValue("orders","tcs_amount","id='".$order_id."' AND isDelete=0",0);

				$totalAmount += ($item['qty']*$item['unitprice']);
				$totalAmount1 = $totalAmount - $cash_discount_amount;
				$totalAmount2 = $totalAmount1 - $additional_discount_amount;
				?>
				<tr>
					<td class="srno"><?php echo $count; ?></td>
					<td class="pname"><?php echo $item['pro_name']?><?=(isset($item['pro_description']) && $item['pro_description']!="")?"<br/> ( ".$item['pro_description']." ) ":""; ?></td>
					<td class="text-left"><?php echo $unit_name; ?></td>
					<td class="text-left"><?php echo $hsncode; ?></td>
					<td class="text-left"><?php echo $stock; ?></td>
					<td class="text-right qty"><?php echo $db->rp_number_format($item['qty']); ?></td>
					<td class="text-right qty"><?php echo $db->rp_number_format($rate); ?></td>
					<td class="text-right qty"><?php echo $db->rp_number_format($item['discount']); ?></td>
					<td class="text-right"><?php echo $db->rp_number_format($item['unitprice']); ?></td>
					<td class="text-right"><?php echo $db->rp_number_format($item['qty']*$item['unitprice']); ?></td>
				</tr>
				<?php
			}
		}
		?>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="text-right">Total</th>
			<th class="text-right"><?php echo $db->rp_number_format($totalAmount); ?></th>
			<input type="hidden" id="total" value="<?= $totalAmount ?>">
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="text-right">Cash Discount
			<?php 
			if ($_REQUEST['mode']=="add")
			{
				?>
				<br><br><label><b>Order Cash Discount : <?= $cash_discount ?></b></label></th>
				<?php
			}
			?>
			<th class="text-right"><input  type="text" class="form-control text-right" onChange='recalculateRow(this)' id="cd_discount" name="cd_discount" value="<?php if($_REQUEST['mode']=="add"){ echo ""; } else { echo $cash_discount; }  ?>"></th>
			<th class="text-right"><input  type="text" class="form-control text-right" onChange='recalculateRow(this)' id="cd_amount" name="cd_amount" value="<?php if($_REQUEST['mode']=="add"){ echo ""; } else { echo $cash_discount_amount; }  ?>">
			<?php 
			if ($_REQUEST['mode']=="add")
			{
				?>
				<label><b>Order CD Amt: <?= $cash_discount_amount ?></b></label>
				<?php
			}
			?>
			</th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="text-right">Additional Discount
			<?php 
			if ($_REQUEST['mode']=="add")
			{
				?>
				<br><br><label><b>Order Additional Discount : <?= $additional_discount ?></b></label></th>
				<?php
			}
			?></th>
			<th class="text-right"><input  type="text" class="form-control text-right" onChange='recalculateRow(this)' id="ad_discount" name="ad_discount" value="<?php if($_REQUEST['mode']=="add"){ echo ""; } else { echo $additional_discount; }  ?>"></th>
			<th class="text-right"><input  type="text" class="form-control text-right" onChange='recalculateRow(this)' id="ad_amount" name="ad_amount" value="<?php if($_REQUEST['mode']=="add"){ echo ""; } else { echo $additional_discount_amount; }  ?>">
			<?php 
			if ($_REQUEST['mode']=="add")
			{
				?>
				<label><b>Order AD Amt: <?= $additional_discount_amount ?></b></label>
				<?php
			}
			?></th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="text-right">Transport Charge</th>
			<th class="text-right"><input type="text" class="form-control text-right" id="transportcharge" name="transportcharge" value="<?= $transport_charge ?>" onchange="recalculateRow(this)"></th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="text-right">Packing & Forwarding Charge</th>
			<th class="text-right"><input type="text" class="form-control text-right" id="packingcharge" name="packingcharge" value="<?= $packing_charge ?>" onchange="recalculateRow(this)"></th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="text-right">Sub Total</th>
			<th class="text-right"><input readonly type="text" class="form-control text-right total_amount" id="total_amount" name="total_amount" value="<?php echo $totalAmount2+$packing_charge+$transport_charge; ?>"></th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<?php 
			if($igst_amount != "" && $igst_amount != 0)
			{ 
				?>
				<th class="text-right">GST</th>
				<th class="text-right"><input readonly type="text" class="form-control text-right total_gst" id="total_gst" name="total_gst" value="<?php echo $db->rp_number_format((($totalAmount2+$packing_charge+$transport_charge)*$GST)/100); ?>"></th>
				<?php  
			} 
			else 
			{ 
				?>
				<th class="text-right"></th>
				<th class="text-right"></th>
				<?php 
			}  
			?>
		</tr>

		<!-- <tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<?php if($tcs_amount != "" && $tcs_amount != 0){ ?>
			<th class="text-right">TCS Amount</th>
			<th class="text-right total_gst"><?php echo $db->rp_number_format($tcs_amount); ?></th>
			<?php  } else { ?>
				<th class="text-right"></th>
			<th class="text-right total_gst"></th>
			<?php }  ?>
		</tr> -->

		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="text-right">Grand Total</th>
			<?php if($igst_amount != "" && $igst_amount != 0){ ?>
			<th class="text-right"><input readonly type="text" class="form-control text-right grand_total" id="grand_total" name="grand_total" value="<?php echo (((($totalAmount2+$packing_charge+$transport_charge+$tcs_amount)*$GST)/100)+($totalAmount2+$packing_charge+$transport_charge+$tcs_amount)); ?>"></th>
			<?php  } else { ?>
				<th class="text-right"><input readonly type="text" class="form-control text-right grand_total" id="grand_total" name="grand_total" value="<?php echo ($totalAmount2+$packing_charge+$transport_charge+$tcs_amount); ?>"></th>
			<?php }  ?>
		</tr>
</table>

<script type="text/javascript">
	var gst_apply_flag = 1;
	var gst = 18;
	function recalculateRow() 
	{
		var sum = $("#total").val();
		var cash_discount = $("#cd_discount").val();
		cash_discount=parseFloat(cash_discount);
		if((isNaN(cash_discount) || cash_discount=="NaN") || (cash_discount=="" || cash_discount==0))
		{
			var cash_discount_amount = $("#cd_amount").val();
			cash_discount_amount=parseFloat(cash_discount_amount);
			if((isNaN(cash_discount_amount) || cash_discount_amount=="NaN") || (cash_discount_amount=="" || cash_discount_amount==0 || cash_discount==0))
			{
				cash_discount_amount = 0;
				cash_discount=0
				$("#cd_amount").val(0);	
			}
			else
			{
				var sum = sum - cash_discount_amount;
				sum = sum.toFixed(2);
				$("#cd_amount").val(parseFloat(cash_discount_amount));	
				$("#total_amount").val('' + sum);		
			}
		}
		else
		{
			var cd = (sum * cash_discount) / 100;
			var sum = sum - parseFloat(cd);
			sum = sum.toFixed(2);
			$("#cd_amount").val(parseFloat(cd));	
			$("#total_amount").val('' + sum);
		}

		var additional_discount = $("#ad_discount").val();
		additional_discount=parseFloat(additional_discount);
		
		if((isNaN(additional_discount) || additional_discount=="NaN") || (additional_discount=="" || additional_discount==0))
		{
			var additional_discount_amount = $("#ad_amount").val();
			additional_discount_amount=parseFloat(additional_discount_amount);
			if((isNaN(additional_discount_amount) || additional_discount_amount=="NaN") || ( additional_discount_amount=="" || additional_discount_amount==0 || additional_discount==0))
			{
				additional_discount_amount = 0;
				additional_discount=0;
				$("#ad_amount").val(0);
			}
			else
			{
				var sum = sum - additional_discount_amount;
				sum = sum.toFixed(2);
				$("#ad_amount").val(parseFloat(additional_discount_amount));	
				$("#total_amount").val('' + sum);		
			}
		}
		else
		{
			var ad = (sum * additional_discount) / 100;
			var sum = sum - parseFloat(ad);
			sum = sum.toFixed(2);
			$("#ad_amount").val(ad);
			$("#total_amount").val('' + sum)
		}
		
		var transport_charge = $("#transportcharge").val();
		var packing_charge = $("#packingcharge").val();

		transport_charge=parseFloat(transport_charge);
		packing_charge=parseFloat(packing_charge);

		if(isNaN(transport_charge) || transport_charge=="NaN")
		{
			transport_charge=0
		}

		if(isNaN(packing_charge) || packing_charge=="NaN")
		{
			packing_charge=0;
		}

		var total_charges = (transport_charge + packing_charge);
		var sum = parseFloat(sum) + parseFloat(total_charges);
		$("#total_amount").val('' + sum);

		if (sum != "" && sum != "0.00") 
		{
			var gst_amount = (sum * gst) / 100;
			var gst_amount1 = (sum * gst) / 100;
			gst_amount = gst_amount.toFixed(2);
			gst_amount1 = gst_amount1.toFixed(2);
			gst_amount = parseFloat(gst_amount) + parseFloat(sum);
			gst_amount = gst_amount.toFixed(2);
			$("#total_gst").val(gst_amount1);
			var final_total = gst_amount;
		}

		var ft = Math.round(final_total);
		ft = ft.toFixed(2);
		var integr = Math.floor(final_total);
		var round_off = final_total - integr;
		round_off = round_off.toFixed(2);
		// round_off=format(round_off,3);
		$("#round_off").val('' + round_off);
		$("#grand_total").val('' + ft);
	}
</script>
<?php require_once 'disconnect.php';  ?>