<?php
$page_id=588;$page_slug='add_invoice';
require_once("connect_in.php");
include("../include/no_to_word.php");
include("../include/product.class.php");
$ProductObj=new Product();
$ntw = new NumToWord_RP;
$invoice_id	= $_REQUEST['invoice_id'];
$cart_detail_r 	= $db->rp_getData("invoice_new","*","id='".$invoice_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);

$invoice_date=($cart_detail_d['invoice_date']!="0000-00-00 00:00:00")?date("d-m-Y",strtotime($cart_detail_d['invoice_date'])):"";

$brand_name_r=$db->rp_getData("brand","name","id IN (".$cart_detail_d['brand_id'].") AND isDelete=0","");
$BRAND_NAME=array();
if($brand_name_r)
{
	while($brand_name_d=mysqli_fetch_assoc($brand_name_r))
	{
		$BRAND_NAME[]=$brand_name_d['name'];
	}
	$BRAND_NAME=implode(",",$BRAND_NAME);
}
else
{
	$BRAND_NAME="";
}

?>
<html>
<head>
<style>
.mainDiv, table{
	border: 1px solid #595959;
	border-collapse: collapse;
	font-size: 13px;
	width:250mm!important;
	background-color: #FFF;
	margin:auto;
  	padding:auto;
}
table , td, th {
	border: 1px solid #595959;
}
td, th {
	padding: 5px;
	height: 25px;
}
.text-center{
	text-align: center!important;
}
.text-right{
	text-align: right!important;
}
.no-border-left{
	border-left: hidden;
}
.no-border-right{
	border-right: hidden;
}
.border td{
	border-bottom: hidden !important;
}
.no-border-bottom{
	border-bottom: hidden !important;
}
.srno{
	width: 50px!important;
	min-width: 50px!important;
	max-width: 50px!important;
}
.pname{
	width: 35% !important;
	min-width: 35%!important;
	max-width: 35%!important;
	text-align: left;
}
.box_gty{
	width: 20% !important;
	min-width: 20%!important;
	max-width: 20%!important;
}
.qty{
	width: 10% !important;
	min-width: 10%!important;
	max-width: 10%!important;
}
.amount{
	width: 10% !important;
	min-width: 10%!important;
	max-width: 10%!important;
}
.color {
	background: #D3D3D3;
}
tbody
{
	/*text-transform: uppercase;*/
}
.font-size td
{
	font-size: 15px!important;
}
</style>
</head>
<body>
<table>
	<tbody>
		<tr>
			<?php 
			$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
			$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
			if(strtolower($Distri_data_a['state'])!=strtolower("Bihar"))
			{
				?>
					<td colspan="2" style="width: 28%!important"><span style="float: left"><img width="120" height="120" src="<?= SITEURL."images/cmk_logo.png"?>"></span></td>

					<td colspan="14" class="no-border-left" ><h3><strong><?php echo CLIENT_BRAND_NAME; ?></strong></h3>
						SURVEY NO. 150, PLOT NO. 11, J. K. INDUSTRIAL, KOTDA SANGANI STATE<br>
						VILLAGE LOTHADA, TAL & DIST : RAJKOT - 360022. (GUJARAT) INDIA<br>
						Mobile : 8000617876, 8980407311 , EMail : cmk.electro@gmail.com<br>
						Website : www.cmkelectropower.com GSTIN No. : 24AAECC8908Q1Z4
					</td>
				<?php
			}
			else
			{
				?>
				<td colspan="16" class="text-center"><h3><strong><?php echo BIHAR_CLIENT_BRAND_NAME; ?></strong></h3></td>
				<?php
			}
			?>
		</tr>

		<tr>
			<td colspan="16" class="text-center"><strong>TAX INVOICE</strong></td>
		</tr>
		<tr>
			<td colspan="10" style="width: 45%!important">
				<?php 
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
				/*if(strtolower($Distri_data_a['state'])!=strtolower("Bihar"))
				{*/
					?>
					Name & Address of the Consignee<br>
					<strong><?= $cart_detail_d['company_name'] ?></strong><br><br>
					<strong><?php echo $Distri_data_a['address']; ?></strong><br/>
					<strong>City: </strong><?php echo $Distri_data_a['city']; ?> - <?php echo $Distri_data_a['zip']; ?><br/>
					<strong>State: </strong><?php echo $Distri_data_a['state']; ?><br/>
					<strong>Mo: </strong><?php echo $Distri_data_a['phone']; ?><br><br>
					<strong>GST No. : <?php echo $Distri_data_a['gst']; ?></strong><br/>

					<?php
				/*}
				else
				{
					?>
					<strong><?php echo BIHAR_COMPANY_NAME; ?></strong><br/>
					<strong><?php echo BIHAR_CLIENT_ADDRESS; ?></strong><br/>
					<strong>GST no. :</strong> <?php echo BIHAR_CLIENT_GST; ?><br/>
					<strong>City: </strong><?php echo BIHAR_CLIENT_CITY; ?><br/>
					<strong>State :</strong> <?php echo BIHAR_CLIENT_STATE; ?><br/>
					<?php
				}*/
				?>
				
			</td>

			<td colspan="6" >
				<?php 
					$GetDistributor = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
					$Distri_data = mysqli_fetch_assoc($GetDistributor);
					?>

				Invoice No. : <strong><?=$cart_detail_d['invoice_no']?></strong><p1 style="float: right;">Date : <?=$invoice_date;?></p1><br>
				Challan No. : <?=$cart_detail_d['chalan_no'] ;?> <p1 style="float: right;"> </p1><br>

				P.O.No.       : <?=$cart_detail_d['po_no'] ;?><br>
				P.O.Date      : <?= date('d-m-Y', strtotime($cart_detail_d['po_date']))  ;?><br>
				Transport     : <?= $db->rp_getValue("transport_by","name","id='".$cart_detail_d['transport_through']."'") ?> <span style="float: right">Transport By : <?= $db->rp_getValue("transport_master","name","transport_by_id='".$cart_detail_d['transport_through']."' AND id='".$cart_detail_d['transport_name']."'") ?></span> <br>
				E-Way Bill No : <br>
				Area Sales Manager : <strong></strong><br>
				Vendor Code   : <br>
				<strong style="color: red;">Payment Due Date : </strong><br>
				<strong>Closing Balance As On Date :</strong>
				<?php 
					if($cart_detail_d['booking']!="")
					{
						?>
						<strong>Booking : </strong><?= $cart_detail_d['booking']; ?> <br/>
						<?php
					}
					if($cart_detail_d['transport']!="")
					{
						?>
						<strong>Transport : </strong><?= $cart_detail_d['transport']; ?> <br/>
						<?php
					}
				 ?>
				
				
			</td>
		</tr>
	</tbody>
</table>
<table>
	<tbody>
		<tr>
			<th width="1%" class="text-center srno">Sr.No</th>
			<th width="50%" colspan="6" class="text-center pname">Name</th>
			<th width="4%" colspan="" class="text-center ">HSN</th>

			<th width="10%" colspan="2" class="text-center ">Total Qty</th>
			<th width="10%" class="text-center ">Price<br/><span style="font-size:11px">(INR)</span></th>
			<th width="10%" class="text-center rate">Discount<br/><span style="font-size:11px">(%)</span></th>
			<th width="10%"  class="text-center rate">Rate<br/><span style="font-size:11px">(INR)</span></th>
			<!-- <th width="10%"  class="text-center rate">GST %<br/><span style="font-size:11px"></span></th> -->
			<th width="15%" colspan="2" class="text-center rate">Total<br/><span style="font-size:11px">(INR)</span></th>
			
		</tr>
		<?php
		// sorting based on product dispaly order & size display order
		$ITEMS=array();
		$items1=$db->rp_getData("invoice_new_product_item","*","invoice_id='".$invoice_id."'");
		while ($item1=mysqli_fetch_assoc($items1)) 
		{
			$item1['display_order']=$db->rp_getValue("product","display_order","id='".$item1['pro_id']."' AND isDelete=0");
			$item1['weight_display_order']=$db->rp_getValue("weight","display_order","id='".$item1['weight_id']."' AND isDelete=0");
			// print_r($item1);
			$ITEMS[]=$item1;
			// $ProductObj->sortBy('weight_display_order', $ITEMS, 'desc');
			// $ProductObj->sortBy('display_order', $ITEMS, 'asc');
		}
		// exit;
		// print_r($ITEMS);exit;
		$tempArr = array();
		foreach ($ITEMS as $key => $val) {
			 $tempArr['display_order'][$key] = $val['display_order'];
        	$tempArr['weight_display_order'][$key] = $val['weight_display_order'];
		}

		array_multisort($tempArr['display_order'], SORT_ASC, $tempArr['weight_display_order'], SORT_ASC,$ITEMS);
		// sorting based on product dispaly order & size display order
    	// print_r($ITEMS);exit;

		$items=$db->rp_getData("invoice_new_product_item","*","invoice_id='".$invoice_id."'");
		if($items1){
			$count=0;
			$totalprice=0;
			$final_price=0;
			$boxqty=0;
			$cartoonqty=0;
			$totalproqty=0;
			$totalrate=0;
			$totaldiscount=0;
			$GST=18;
			// while ($item=mysqli_fetch_assoc($items)) 
			foreach($ITEMS as $item)
			{
				$display_order=$db->rp_getValue("product","display_order","id='".$item['pro_id']."' AND isDelete=0");
				$tcid=$db->rp_getValue("product","tcid","id='".$item['pro_id']."' AND isDelete=0");
				$GST=$db->rp_getValue("product","igst","id='".$item['pro_id']."' AND isDelete=0");
				$pro_name=$db->rp_getValue("product","name","id='".$item['pro_id']."' AND isDelete=0");
				$size=$db->rp_getValue("weight","name","id='".$item['weight_id']."' AND isDelete=0");
				$product_code=$db->rp_getValue("product_weight_price","catno","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'"); 
				$hsncode=$db->rp_getValue("product","hsn_code","id='".$item['pro_id']."' AND isDelete=0",0);
				/*$discount=$db->rp_getValue("price_table","discount","tcid='".$tcid."' AND uid='".$cart_detail_d['customer_id']."' AND isDelete=0");
				if($discount=="")
				{
					$discount=0;
				}*/
				if($item['discount']!="" && $item['discount']!=0)
				{
					$discount_amount=($item['unitprice']*$item['discount'])/100;
					$price=$item['unitprice']-$discount_amount;
				}
				else
				{
					$price=$item['unitprice'];
					$item['discount']=0;
				}
				$count++;
				$rate=$item['pro_qty']*$item['unitprice'];
				$totalprice+=$item['totalprice'];
				$boxqty+=$item['box_qty'];
				$cartoonqty+=$item['cartoon_qty'];
				$totalproqty+=$item['pro_qty'];
				// $totalrate+=$price;
				$totalrate+=$item['unitprice'];
				$totaldiscount+=$item['discount'];
				if($brand_name=="Prince Platinum")
				{
					$brand_name="Prince Plm";
				}
			?>
			<tr>
				<!-- <td width="5%" colspan="2" class="text-center srno"><?php echo $count; ?></td> -->
				<td width="1%" class="text-center srno"><?php echo $count; ?></td>
				<td width="50%" colspan="6" class="pname">
					<?php 
					if($item['weight_id']!=-1)
					{
						echo $pro_name." - ".$size." - ".strtoupper($product_code); 
					}
					else
					{
						echo $pro_name." - ".strtoupper($product_code); 
					}
					?>
						
				</td>
				<td class="text-center"><?php echo $hsncode; ?></td>
				<td width="7%"  colspan="2" class="text-right "><?php echo $item['pro_qty']; ?></td>
				<td width="7%" class="text-right rate"><?php echo $db->rp_number_format($item['original_price']); ?></td>
				<td width="11%" class="text-right "><?php echo $db->rp_number_format($item['discount']); ?></td>
				<td width="10%" class="text-right rate"><?php echo $db->rp_number_format($item['unitprice']); ?></td>
				<!-- <td width="10%" class="text-right rate">18.00</td> -->
				<td width="15%" colspan="2" class="text-right rate"><?php echo $db->rp_number_format($item['totalprice']); ?></td>
			</tr>
			<?php
			if($cash_discount!="" && $cash_discount!=0)
			{

			}
			}
			if($count<10)
			{
				for($i=0;$i<10-$count;$i++)
				{
				?>
				<tr class="border">
					<!-- <td colspan="2" class="srno"></td> -->
					<td width="1%" class="srno"></td>
					<td colspan="6" class="pname"></td>
					<!-- <td class="box_qty"></td> -->
					<!-- <td class=""></td> -->
					<!-- <td  class="rate"></td> -->
					<td></td>
					<td  colspan="2" class="rate"></td>
					<td  class="rate"></td>
					<td colspan="" class="rate"></td>
					<td colspan="" class="rate"></td>
					<!-- <td colspan="" class="rate"></td> -->
					<td colspan="2" class="rate"></td>
				</tr>
				<?php 
				}
			}
			?>
			<tr>
				<!-- <td colspan="2" class="srno"></td> -->
				<td width="1%" class="srno"></td>
				<td colspan="6" class="pname"></td>
				<!-- <td  class="box_qty"></td> -->
				<!-- <td class=""></td> -->
				<!-- <td class="rate"></td> -->
				<td></td>
				<td colspan="2" class="rate"></td>
				<td class="rate"></td>
				<td colspan="" class="rate"></td>
				<td colspan="" class="rate"></td>
				<!-- <td colspan="" class="rate"></td> -->
				<td colspan="2" class="rate"></td>
				
			</tr>
			<?php
		}
		?>
		<tr class="font-size">
			<!-- <td colspan="2"></td> -->
			<td width="1%"></td>
			<td colspan="7" class="pname text-right"><strong>Total</strong></td>
			<!-- <td class="text-right"><strong><?= $boxqty;?></strong></td> -->
			<!-- <td class="text-right"><strong><?= $cartoonqty;?></strong></td> -->
			<td colspan="2" class="text-right"><strong><?= $totalproqty;?></strong></td>
			<td></td>
			<td class="rate"></td>
			<td class="rate"></td>
			<!-- <td class="rate"></td> -->
			<!-- <td class="rate"></td> -->
			<td colspan="2" class="rate"></td>
		</tr>
		
		<tr class="font-size">
			<?php
			if($cart_detail_d['cash_discount']!="" && $cart_detail_d['cash_discount']!=0 && $cart_detail_d['cash_discount_amount']!="" && $cart_detail_d['cash_discount_amount']!=0)
			{
				$cash_amount=($totalprice*$cart_detail_d['cash_discount'])/100;
				if($cash_amount>$totalprice)
				{
					$subtotal=$cash_amount-$totalprice;
				}
				else
				{
					$subtotal=$totalprice-$cash_amount;
				}
				$trans_charge_a = $subtotal + $cart_detail_d['transport_charge'] + $cart_detail_d['packing_charge'];
				$gst_amount=($trans_charge_a*$GST)/100;
				$final_price=$trans_charge_a+$gst_amount;
				$rowspan=5;
			}
			else
			{
				$trans_charge_b = $totalprice + $cart_detail_d['transport_charge'] + $cart_detail_d['packing_charge'];
				$gst_amount=($trans_charge_b*$GST)/100;
				$final_price=$trans_charge_b+$gst_amount;
				$final_gst_amount = $gst_amount;
				$rowspan=6;
			}
			?>
			<td colspan="10" class="" rowspan="<?= $rowspan; ?>">	
				<h4><b><u>Bank Details</u></b></h4>			
				<?php 
				$GetDistributor_b = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
				$Distri_data_b = mysqli_fetch_assoc($GetDistributor_b);
				if(strtolower($Distri_data_b['state'])!=strtolower("Bihar"))
				{
					?>
					<!-- <span style="font-size:13px;"><b>Bank Name </b>: <?php echo COMPANY_BANK ?> </span>
					<span style="font-size:13px;margin-left: 21px"><b>Account Number</b> : <?php echo COMPANY_BANK_ACC_NO ?>  </span><br/>

					<span style="font-size:13px;"><b>IFSC Code</b> : <?php echo COMPANY_BANK_IFSC ?> </span> -->
					<!-- <span style="font-size:13px;margin-left: 57px;"><b>Branch </b>: <?php echo COMPANY_BANK_BRANCH ?></span> -->


					<span style="font-size:13px;"><b>Bank Name </b>: <?php echo COMPANY_BANK ?> </span><br>
					<span style="font-size:13px;"><b>Bank A/c No.</b> : <?php echo COMPANY_BANK_ACC_NO ?>  </span><br/>
					<span style="font-size:13px;"><b>RTGS/IFSC Code</b> : <?php echo COMPANY_BANK_IFSC ?> </span><br>
					<span style="font-size:13px;"><b>Swift Code </b> : <?php echo COMPANY_SWIFT_CODE ?></span>
					<?php
				}
				else
				{
					?>
					<span style="font-size:13px;"><b>Bank Name </b>: <?php echo COMPANY_BANK ?> </span><br>
					<span style="font-size:13px;"><b>Bank A/c No.</b> : <?php echo COMPANY_BANK_ACC_NO ?>  </span><br/>
					<span style="font-size:13px;"><b>RTGS/IFSC Code</b> : <?php echo COMPANY_BANK_IFSC ?> </span><br>
					<span style="font-size:13px;"><b>Swift Code </b>: </span>
					<?php
				}
				?>
			<br><br><p style="font-size: 13px"><b>Total Gst : </b><?php echo $ntw->rp_convertNumToWord($gst_amount); ?></p>
				<p style="font-size: 13px"><b>Bill Amount : </b><?php echo $ntw->rp_convertNumToWord(round($final_price)); ?></p>
			</td>
			
			<td colspan="3" class="text-left"><strong>SubTotal</strong></td>			
			<td colspan="2" class="text-right "><strong><?php echo $db->rp_number_format($totalprice); ?></strong></td>
		</tr>
		
		<?php
		if($cart_detail_d['cash_discount']!="" && $cart_detail_d['cash_discount']!=0 && $cart_detail_d['cash_discount_amount']!="" && $cart_detail_d['cash_discount_amount']!=0)

		{
			$cash_amount=($totalprice*$cart_detail_d['cash_discount'])/100;
			if($cash_amount>$totalprice)
			{
				$subtotal=$cash_amount-$totalprice;
			}
			else
			{
				$subtotal=$totalprice-$cash_amount;
			}
			$trans_charge_c = $subtotal + $cart_detail_d['transport_charge'] + $cart_detail_d['packing_charge'];
			$gst_amount=($trans_charge_c*$GST)/100;
			$final_price=$trans_charge_c+$gst_amount;
			?>
			<tr class="font-size">
				<td colspan="3" class="text-left"><strong>Cash Discount <?php echo $cart_detail_d['cash_discount']."%"; ?></strong></td>
				<td colspan="2" class="text-right "><strong><?php echo $db->rp_number_format($subtotal); ?></strong></td>
		</tr>
		<tr class="font-size">
			<td colspan="3" class="text-left"><strong>GST <?php echo $GST."%"; ?></strong></td>
			<td colspan="2" class="text-right "><strong><?php echo $db->rp_number_format($final_price); ?></strong></td>
		</tr>
		<tr class="font-size">
			<td colspan="3" class="text-rleft "><strong>Round Off</strong></td>
			<td colspan="2" class="text-right "><strong><?php 
			$whole = floor($final_price);    
	        $fraction = $final_price - $whole;

	        echo $f1=  $db->rp_number_format((float)$fraction, 3, '.', '');
			 ?></strong></td>
		</tr>
		<tr class="font-size">
			<td colspan="2" class="text-left "><strong>SubTotal</strong></td>
			<td colspan="2" class="text-right "><strong><?php echo $db->rp_number_format(round($final_price)); ?></strong></td>
		</tr>
			<?php
		}
		else
		{
			$trans_charge_d = $totalprice + $cart_detail_d['transport_charge'] + $cart_detail_d['packing_charge'];
			$gst_amount=($trans_charge_d*$GST)/100;
			$final_price=$trans_charge_d+$gst_amount;
			$final_gst_amount = $gst_amount;
			?>
			<tr class="font-size">
				<td colspan="3" class="text-left "><strong>Transport Charge</strong></td>
				<td colspan="2" class="text-right "><strong><?php echo $db->rp_number_format($cart_detail_d['transport_charge']); ?></strong></td>
			</tr>
			<tr class="font-size">
				<td colspan="3" class="text-left "><strong>Packing & Forwarding Charge</strong></td>
				<td colspan="2" class="text-right "><strong><?php echo $db->rp_number_format($cart_detail_d['packing_charge']); ?></strong></td>
			</tr>
			<tr class="font-size">
				<td colspan="3" class="text-left "><strong>GST <?php echo $GST."%" ." (".$db->rp_number_format($final_gst_amount).")"; ?></strong></td>
				<td colspan="2" class="text-right "><strong><?php echo $db->rp_number_format($final_price); ?></strong></td>
			</tr>
			<tr class="font-size">
			<td colspan="3" class="text-left "><strong>Round Off</strong></td>
			<td colspan="2" class="text-right "><strong><?php 
			$whole = floor($final_price);      // 1
	        $fraction = $final_price - $whole;

	        echo $f1=  $db->rp_number_format((float)$fraction, 2, '.', '');
			$gt = $final_price;
			 ?></strong></td>
		</tr>
			<tr class="font-size">
				<td colspan="3" class="text-left "><strong>Grand Total</strong></td>
				<td colspan="2" class="text-right "><strong><?php echo $db->rp_number_format(round($gt)); ?></strong></td>
			</tr>
			<tr>
				
			
			<td colspan="10" class="" rowspan="">	
				<h4><b><u>Terms & Condition</u></b></h4>
				<span style="font-size:13px;"><?= $cart_detail_d['terms_comdition'] ?></span>

				<h4><b>Remarks</b></h4>
				<span style="font-size:13px;"><strong><?= $cart_detail_d['remarks'] ?></strong></span><br><br>
				<span style="font-size:13px;"><strong><?= $cart_detail_d['faithfully'] ?></strong></span>
			</td>

			<td colspan="5" class="no-border-left text-right">
				<div class="col-md-12">
					<h5 class="text-right" style="font-size: 14px;">For, <?php echo CLIENT_BRAND_NAME; ?></h5>
					<br/><br/><br/><br/>
					<h5 class="text-right" align="center" style="text-transform: capitalize;font-size: 14px;padding-right: 45px;">(Authorized signatory)</h5>
				</div>
			</td>
			
		</tr>
			<?php
		}
		?>
	</tbody>
</table>
</body>
</html>