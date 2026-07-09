<?php
$page_id=566;$page_slug='page_order_ajax';
require_once("connect_in.php");
include("../include/no_to_word.php");
include("../include/quotation.class.php");
$ObjQuotation=new Quotation();
$ntw = new NumToWord_RP;
$invoice_id	= $_REQUEST['sales_return_id'];
$format_type=isset($_REQUEST['format_type'])?$_REQUEST['format_type']:1;
if($format_type==1)
{
	$title="Original";
}
else if($format_type==2)
{
	$title="Duplicate";
}
else if($format_type==3)
{
	$title="Triplicate";
}
else if($format_type==4)
{
	$title="";
}
$cart_detail_r 	= $db->rp_getData("sales_return","*","id='".$invoice_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);
$invoice_date=($cart_detail_d['invoice_date']!="0000-00-00 00:00:00")?date("d-m-Y",strtotime($cart_detail_d['invoice_date'])):""; 
/*for watermark*/
$cl = "";
if($cart_detail_d['status']!=1)
{
	$cl = "addwatermark";
}


/*get order no*/
$dispatchid = $cart_detail_d['dispatch_ids'];
$orderids = array();
$DispatchR = $db->rp_getData("dispatch_detail","order_id","id='".$dispatchid."'");
while($DispatchD = mysqli_fetch_assoc($DispatchR))
{
	$orderids[] = $DispatchD['order_id'];
}
$orderids = implode(",", $orderids);
$OrderR = $db->rp_getData("orders","order_no,order_date,po_no,po_date","id IN (".$orderids.") AND isDelete=0");
$OrderD = mysqli_fetch_assoc($OrderR);

/*get order no*/


/*get warehouse name*/
$Warehouse_id = $cart_detail_d['warehouse_id'];
$warehouseids = array();
$warehouseR = $db->rp_getData("warehouse","*","id In (".$Warehouse_id.") AND isDelete=0","",0);
while($warehouseD = mysqli_fetch_assoc($warehouseR))
{
	$warehouseids[] = $warehouseD['name'];
}
$name = implode(",", $warehouseids);
/*get warehouse name*/

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
.no-border-bottom{
	border-bottom: hidden !important;
}
.no-border-top{
	border-top: hidden !important;
}
.border td{
	border-bottom: hidden !important;
}
.srno{
	width: 30px!important;
	min-width: 30px!important;
	max-width: 30px!important;
}
.pname{
	width: 30% !important;
	min-width: 28%!important;
	max-width: 28%!important;
	text-align: left;
}
.pname1{
	width: 10% !important;
	min-width: 23%!important;
	max-width: 23%!important;
	text-align: left;
}
.model{
	/*width: 30% !important;*/
	min-width: 30%!important;
	max-width: 30%!important;
	text-align: left;
}
.box_gty{
	width: 10% !important;
	min-width: 10%!important;
	max-width: 10%!important;
}
.qty{
	/*width: 10% !important;*/
	min-width: 10%!important;
	max-width: 10%!important;
}
.rate{
	width: 10% !important;
	min-width: 10%!important;
	max-width: 10%!important;
}
.amount{
	width: 10% !important;
	min-width: 10%!important;
	max-width: 10%!important;
}
.carrtoon_qty{
	width: 5% !important;
	min-width: 5%!important;
	max-width: 5%!important;
}
.rate
{
	width: 90px !important;
	min-width: 90px !important;
	max-width: 90px !important;
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
.image-width
{
	width: 10% !important;
	min-width: 10% !important;
	max-width: 10% !important;
}
.vertical_line
{
  border-left: 5px solid #81bfc7;
  height: 170px;
  position: absolute;
  left: 38%;
  margin-left: -3px;
  top: 0;
  margin-top: 170px;
}
.vertical_line1
{
  border-left: 5px solid #81bfc7;
  height: 170px;
  position: absolute;
  left: 58%;
  margin-left: -3px;
  top: 0;
  margin-top: 170px;
}
.vertical_line2
{
  border-left: 5px solid #E5E5E5;
  height: 90px;
  position: absolute;
  left: 28%;
  margin-left: -3px;
  top: 0;
  margin-top: 20px;
}
.border-r-width
{
	border-right-width: 5px;
}
.border-gray
{
	border-right-color:#E5E5E5;
}
.border-blue
{
	border-right-color:<?= VIEW_COLOR ?>;
}
.vertical-top
{
	vertical-align: top;
}
.height-5
{
	height: 5px;
}
.bg-gray
{
	background-color: #E5E5E5 !important;
}
.font-13
{
	font-size:13px !important;
}
</style>
</head>
<body>
<table>
	<tbody class="<?= $cl; ?>">
		<tr>
			<td colspan="16" class="header-img">
				<img style="width: 100%;padding: 0px !important;"  src="<?= VIEW_LOGO_All ?>">
			</td>
		</tr>

		<tr>
			<td colspan="7" class="no-border-top no-border-right height-5 text-center"></td>
			<td colspan="3" class="no-border-top no-border-right height-5 text-center"><b>GST - <?= COMPANY_GST ?></b></td>
			<td colspan="6" class="no-border-top height-5 text-center"></td>
		</tr>

		<tr>
			<td colspan="7" class="no-border-bottom  height-5 text-center"></td>
			<td colspan="3" class="no-border-bottom  no-border-left height-5 text-center"><b>PAN - <?= COMPANY_PAN ?></b></td>
			<td colspan="6" class="no-border-bottom  no-border-left height-5 text-center"></td>
		</tr>

		<tr>
			<td colspan="7" class="no-border-right no-border-bottom bg-gray"><b>Debit Memo</b></td>
			<td colspan="3" class="text-center no-border-right no-border-bottom bg-gray"><b>Sales Return</b></td>
			<td colspan="6" class="text-right no-border-bottom bg-gray"><b><?php echo $title; ?></b></td>
		</tr>
		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5"></td>
		</tr>
		<tr class="vertical-top">
			<td colspan="7" class="no-border-bottom no-border-top border-r-width">
				<strong  >To,<br/></strong>
				<?php
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0","",0);
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
				?>
				<strong><?php echo $cart_detail_d['company_name']; ?></strong><br/><span>
				<?php echo wordwrap($cart_detail_d['address'],40,"<br>\n")."  <br/>".$cart_detail_d['city']." - ".$cart_detail_d['zip']." , ".$cart_detail_d['state']." , ".$cart_detail_d['country'] ?></span><br/><br>
				<span><b>Contact Person :</b> <?=$cart_detail_d['customer_name']?></span><br>
				<span><b>Contact Number :</b> <?=$cart_detail_d['contact_number']?></span><br>
				<span><b>Email : </b><?=$cart_detail_d['email']?></span><br/>
				<span><b>State of Supply :</b> <?=$cart_detail_d['state']?></span><br/>
				<strong><span><b>GST No :</b> <?=$cart_detail_d['gst']?></span></strong><br/>
				<span><b>Pan No :</b> <?=$cart_detail_d['pan']?><br/>
				<span><b>Vendor Code :</b> <?= $cart_detail_d['vendor_code'] ?></span><br/>
			</td>

			<td colspan="3" class="no-border-bottom no-border-top border-r-width">
				<strong>Address<br/></strong>
				<strong>Billing Address</strong><br>
				<?= wordwrap($cart_detail_d['billing_address'],40,"<br>\n"); ?> <br><br>
				<strong>Shipping Address</strong><br>
				<?= wordwrap($cart_detail_d['shipping_address'],40,"<br>\n"); ?> <br><br>
				<strong>Transport By :</strong>
				<?= $db->rp_getValue("transport_by","name","isDelete=0 AND id='".$cart_detail_d['transport_through']."' ") ?><br>
				<strong>Transporter Detail :</strong>
				<?= $db->rp_getValue("transport_master","name","isDelete=0 AND id='".$cart_detail_d['transport_name']."' ") ?><br>
			</td>

			<td colspan="6" class="no-border-bottom no-border-top ">
				<strong>Details<br/></strong>
				<table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 60mm!important;margin:0px;">
					<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><b>Credit Note No :</b><?= $cart_detail_d['invoice_no'] ?> </td>
					</tr>
					<tr>
						<td class="no-border-right no-border-bottom no-border-left no-border-top"><b>Credit Note Date :</b> <?php if($cart_detail_d['invoice_date']!=""){ echo date('d-m-Y', strtotime($cart_detail_d['invoice_date'])); } else { echo "";}?></td>
					</tr>
					<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><b>Original Invoice No & Date :</b>
								<?php 
								$invoice_no_r=$db->rp_getData("invoice_new","invoice_no,invoice_date","isDelete=0 AND id IN(".$cart_detail_d['original_invoice_id'].")");
				$invoice_no=array();
				while ($get_invoice_d=mysqli_fetch_assoc($invoice_no_r)) 
				{
						$invoice_no[]=$get_invoice_d["invoice_no"].' - '.date('d-m-Y',strtotime($get_invoice_d["invoice_date"]));
				}
				$invoice_no=implode(", ", $invoice_no);
				echo $invoice_no;
								?>
							 </td>
					</tr>
					<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><b>Buyer's Order No :</b><?= $cart_detail_d['chalan_no'] ?> </td>
					</tr>
					<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><b>Dispatch Doc No. :</b><?= $cart_detail_d['po_no'] ?> </td>
					</tr>
					<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><b>Destination. :</b><?= $cart_detail_d['lut_no'] ?> </td>
					</tr>
					<!-- <tr>
						<td class="no-border-right no-border-bottom no-border-left no-border-top"><b>Sales Return No :</b> <?= $cart_detail_d['invoice_no'] ?></td>
					</tr> -->
				
					<!-- <tr>
						<td class="no-border-right no-border-bottom no-border-left"><b>P.O. No :</b> <?= $cart_detail_d['po_no']?></td>
					</tr> -->
					<!-- <tr>
						<td class="no-border-right no-border-bottom no-border-left"><b>P.O. Date :</b> 
						<?php

						$po_date=date('d-m-Y', strtotime($cart_detail_d['po_date']));

							if($po_date != "01-01-1970")
							{
								echo $po_date;

							} 
						?>
						</td>
					</tr> -->
					<!-- <tr>	
						<td class="no-border-right no-border-bottom no-border-left"><b>Challan No :</b> <?= $cart_detail_d['chalan_no'] ?></td>
					</tr> -->
					<!-- <tr>
						<td class="no-border-right no-border-bottom no-border-left"><b>E-Way Bill No :</b> <br/> <?= $cart_detail_d['way_bill_no'] ?></td>
					</tr> -->
					<!-- <tr>	
						<td class="no-border-right no-border-bottom no-border-left"><b>Warehouse :</b> <br/> <?= $name ?></td>
					</tr>
					<tr>	
						<td class="no-border-right no-border-bottom no-border-left"><b>LUT No :</b> <?= $cart_detail_d['lut_no'] ?></td>
					</tr> -->
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="16" class="no-border-top height-5"></td>
		</tr>
		<tr class="text-center">
			<th colspan="1" class="srno text-center" >SR</th>
			<th colspan="4" class="pname model text-center">Product</th>
			<th colspan="2" class="text-center" style="width:10%!important;">Product Code</th>
			<th style="width:1%" colspan="1" class="text-center">UNIT</th>
			<th class="pname1 text-center">HSN Code</th>
			<th style="width:10%" colspan="1" class="text-center">GST Rate</th>
			<th colspan="1" class="text-center">Qty</th>
			<!-- <th colspan="2" class="rate text-center">Rate</th> -->
			<th colspan="1" class="discount_p text-center">Discount</th>
			<th colspan="1" class="text-center">Net Rate</th>
			<th colspan="3" class="text-center">Total Amount<br/><span style="font-size:11px">(INR)</span></th>
		</tr>
		<?php
		$ITEMS=array();
		$items1=$db->rp_getData("sales_return_item","*","invoice_id='".$invoice_id."' AND isDelete=0");
		while ($item1=mysqli_fetch_assoc($items1)) 
		{
			$item1['display_order']=$db->rp_getValue("product","display_order","id='".$item1['pro_id']."' AND isDelete=0");
			$item1['weight_display_order']=$db->rp_getValue("weight","display_order","id='".$item1['weight_id']."' AND isDelete=0");
			$item1['image_path']=$db->rp_getValue("product","image_path","id='".$item1['pro_id']."' AND isDelete=0","",0);
			if($item1['image_path']!="" )
            {
                $img=SITEURL.PRODUCT.$item1['image_path'];
            }
            else
            {                               
                $img=SITEURL."images/no_image_found.jpg";
            }
            $ITEMS[]=$item1;
		}

		if($items1)
		{
			$count=0;
			$totalprice=0;
			$final_price=0;
			$boxqty=0;
			$cartoonqty=0;
			$totalproqty=0;
			$totalrate=0;
			$totaldiscount=0;
			$GST=0;
			foreach($ITEMS as $item)
			{
				$display_order=$db->rp_getValue("product","display_order","id='".$item['pro_id']."' AND isDelete=0");
				$tcid=$db->rp_getValue("product","tcid","id='".$item['pro_id']."' AND isDelete=0");
				if($cart_detail_d['igst_amount']!=0)
				{
				    if($Distri_data_a['type_of_executive']==7)
					{
						$GST=0.1;    
					}
					else
					{
						$GST=18;    
					}    
				}
				else
				{
				    $GST=0;    
				}
				$pro_name=$db->rp_getValue("product","name","id='".$item['pro_id']."' AND isDelete=0");
				$size=$db->rp_getValue("weight","name","id='".$item['weight_id']."' AND isDelete=0");
				$product_code=$db->rp_getValue("product_weight_price","catno","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'",0);
				$unit_id = $db->rp_getValue("product","display_unit","id='".$item['pro_id']."' AND isDelete=0");
				$unit_name = $db->rp_getValue("unit","name","id='".$unit_id."' AND isDelete=0");
				
				$hsncode=$db->rp_getValue("product","hsn_code","id='".$item['pro_id']."' AND isDelete=0",0);
				$gst_rate=$db->rp_getValue("product","igst","id='".$item['pro_id']."' AND isDelete=0",0);

				$image=$db->rp_getValue("product","image_path","id='".$item['pro_id']."' AND isDelete=0");

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
				$discount = $item['discount_amount'];
				$totalprice +=$item['pro_qty']*$item['unitprice'];
				// $totalgstamount+=$item['igst_amount'];
				?>
				<tr>
					<td colspan="1" class="text-center srno"><strong><?php echo $count; ?></strong></td>
					<td colspan="4" class="model" style="position: relative;"><?php if($item['weight_id']!=-1){echo $pro_name; } else { echo $pro_name; } ?><?=(isset($item['pro_description']) && $item['pro_description']!="")?"<br/><br/>".$item['pro_description']:""?></strong></td>
					<td colspan="2" class="text-center" style="position: relative;"><?php if($item['weight_id']!=-1){echo strtoupper($product_code); } else { echo strtoupper($product_code); } ?></strong></td>
					<td colspan="1" class="box_qty" style="text-align: center;"><?php echo $unit_name; ?></td>
					<td class="text-center"><?php echo $hsncode; ?></td>
					<td colspan="1" class="box_qty" style="text-align: center;"><?php echo $gst_rate." %"; ?></td>
					<td colspan="1" class="text-center rate"><?php echo $item['pro_qty']; ?></td>
					<!-- <td colspan="2" class="text-center carrtoon_qty "><?php echo CURR.' '.round($item['original_price'], 2); ?></td> -->
					<td colspan="1" class="text-center discount_p">
						<?php 
						if($item['discount']!="0")
						{ 
							?>
							<?=$item['discount']." %"?>
							<?php
						}
						else
						{ 
							?>
							<?=CURR." ".$item['discount_amount']?>
							<?php 
						} 
						?>
					</td>
					<td colspan="1" class="text-center"><?php echo CURR.' '.round($item['unitprice'], 2); ?></td>
					<td colspan="3" class="text-center rate"><?php echo CURR.' '.round($item['pro_qty']*$item['unitprice'], 2); ?></td>
				</tr>
				<?php
			}
			if($count<6)
			{
				for($i=0;$i<6-$count;$i++)
				{
					?>
					<tr class="border">
						<td colspan="1" class="srno"></td>
						<!-- <td colspan="1" class="model" ></td> -->
						<td colspan="4" class="pname"></td>
						<td colspan="2" class="pname"></td>
						<td></td>
						<td colspan="1" class="box_qty"></td>
						<td colspan="1" class="box_qty"></td>
						<td colspan="1" class="box_qty"></td>
						<!-- <td colspan="2" class="carrtoon_qty"></td> -->
						<td colspan="1" class="rate"></td>
						<td colspan="1" class="rate"></td>
						<td colspan="3" class="rate"></td>
					</tr>
					<?php 
				}
			}
		}
		?>
		<tr>
			<td colspan="1" class="srno"></td>
			<td colspan="4" class="pname"></td>
			<td colspan="2" class="pname"></td>
			<td></td>
			<td colspan="1" class="box_qty"></td>
			<td colspan="1" class="box_qty"></td>
			<td colspan="1" class="box_qty"></td>
			<!-- <td colspan="2" class="carrtoon_qty"></td> -->
			<td colspan="1" class="rate"></td> 
			<td colspan="1" class="rate"></td>
			<td colspan="3" class="rate"></td>
		</tr>

		<tr class="font-size">
			<?php
				$final_price1 = $totalprice-$cart_detail_d['cash_discount_amount'];
				$final_price2 = $final_price1-$cart_detail_d['additional_discount_amount'];
				$final_price = $final_price2+$cart_detail_d['transport_charge']+$cart_detail_d['packing_charge'];
				$gst_amount=($final_price*$GST)/100;
				$new_final_price=round($final_price+$gst_amount);
				$final_gst_amount = $gst_amount;
				$rowspan=3;

				$fp=$final_price+$gst_amount;
				$whole = floor($fp);      // 1
			    $fraction = $fp - $whole;
			    $f1=  $db->rp_number_format((float)$fraction, 3, '.', '');
			?>
			
			<td colspan="8" class="" rowspan="11" style="vertical-align: top;">	
				<!-- <?php $grand_total_words = $ntw->rp_convertNumToWord($new_final_price); ?>	 -->
				<!-- <?php $gst_amount_words = $ntw->rp_convertNumToWord($gst_amount); ?>	 -->
				<?php 
					$total_tax_amt = $totalprice - ($cart_detail_d['cash_discount_amount'] + $cart_detail_d['additional_discount_amount']) + ($cart_detail_d['transport_charge']+$cart_detail_d['packing_charge']);
					$totalgstamount = $cart_detail_d['igst_amount'];

					$totalgst = $totalgstamount;
					$cashdisgst = ($cart_detail_d['cash_discount_amount']*$cart_detail_d['cd_gst']/100);
					$adddisgst = ($cart_detail_d['additional_discount_amount']*$cart_detail_d['ad_gst']/100);
					$packinggst = ($cart_detail_d['packing_charge']*$cart_detail_d['packing_charge_gst']/100);
					$transportgst = ($cart_detail_d['transport_charge']*$cart_detail_d['transport_charge_gst']/100);
					$finalgst = $totalgst - ($cashdisgst+$adddisgst) + ($packinggst+$transportgst);

					// $grand_total_words1 = $ntw->rp_convertNumToWord(round($total_tax_amt+$finalgst,0)); 
				?>
				<!-- <?php
					$totalgst = $totalgstamount;
					$cashdisgst = ($cart_detail_d['cash_discount_amount']*$cart_detail_d['cd_gst']/100);
					$adddisgst = ($cart_detail_d['additional_discount_amount']*$cart_detail_d['ad_gst']/100);
					$packinggst = ($cart_detail_d['packing_charge']*$cart_detail_d['packing_charge_gst']/100);
					$transportgst = ($cart_detail_d['transport_charge']*$cart_detail_d['transport_charge_gst']/100);

					$finalgst = $totalgst - ($cashdisgst+$adddisgst) + ($packinggst+$transportgst);
					// $finalgst = $ntw->rp_convertNumToWord(round($totalgst - ($cashdisgst+$adddisgst) + ($packinggst+$transportgst)));
				?> -->
				<span class="font-13"><b>GST Amount</b> : <?php echo ucwords(strtolower($ntw->rp_convertNumToWord(round($finalgst)))); ?></span>
				<br><span class="font-13" ><b>Bill Amount (Words)</b> : 
					<?php 
						if($cart_detail_d['tcs_amount'] !="" && $cart_detail_d['tcs_amount'] != "0")
						{  
							$grand_total_words = $ntw->rp_convertNumToWord(round($total_tax_amt+$finalgst+$cart_detail_d['tcs_amount'],0));
						}

						else if($cart_detail_d['igst_amount'] =="" || $cart_detail_d['igst_amount'] == "0")
						{
							$grand_total_words = $ntw->rp_convertNumToWord(round($total_tax_amt,0));
						}
						else
						{
							$grand_total_words = $ntw->rp_convertNumToWord(round($total_tax_amt+$finalgst,0));
						}
						
						 echo ucwords(strtolower($grand_total_words)); ?>
				</span>
				<br><br><span class="font-13">
				<b>Bank Details : </b><br/> 
				<b>Bank Name</b> : <?= COMPANY_BANK ?><br/> 
				<b>Bank Branch</b> : <?= COMPANY_BANK_BRANCH ?><br>
				<b>Bank Account No</b> : <?= COMPANY_BANK_ACC_NO ?> <br>
				<b>Bank IFSC Code</b> : <?= COMPANY_BANK_IFSC ?></span><br>

				<div style="margin-top: 3px;">
					<span class="font-13"><b>Remarks</b></span>
					<span class="font-13"><?php echo str_replace('rn','',$cart_detail_d['remarks']) ?></span>
				</div>
				<span style="color: red;">Contact Sales Person : <?=strip_tags($cart_detail_d['faithfully'])?> &nbsp; </span><br>

				<span class="font-13"><br/><b>Terms & Condition :</b></span><br>
				<span style="font-size:13px;"><?= $cart_detail_d['terms_comdition'] ?></span>
			</td>
			<td colspan="2" rowspan="11" style="vertical-align: top;">
				<table style="width: 45mm!important; vertical-align: top;">

					<?php 
						if($cart_detail_d['total_parcel']!="" && $cart_detail_d['total_weight']!="")
						{
							
							$get_main_cartoon_count = $cart_detail_d['total_parcel'];
							$get_sum_for_weight = $cart_detail_d['total_weight'];
						}
						else
						{
							$packing_slip_id=$db->rp_getValue("packing_slip","id","isDelete=0 AND dispatch_id='".$cart_detail_d['dispatch_ids']."'");

						//	$get_main_cartoon_count=$db->rp_getValue("packing_slip_item","main_carton_type_count","isDelete=0 AND packing_slip_id='".$packing_slip_id."' GROUP BY main_carton_type_count ORDER BY id DESC ",0);
						$get_main_cartoon_count=$db->rp_getValue("packing_slip_item","MAX( main_carton_type_count )","isDelete=0 AND packing_slip_id='".$packing_slip_id."' ",0);

							$get_sum_for_weight=$db->rp_getValue("packing_slip_item","sum(main_carton_whole_actual_weight)","isDelete=0 AND packing_slip_id='".$packing_slip_id."'",0);
						}
						?>
						<tr>
							<td ><b>Total Qty.</b></td>
							<td><?=$cart_detail_d['total_qty']?></td>
						</tr>
						<!-- <tr>
							<td ><b>Total Parcel</b></td>
							<td><?=$get_main_cartoon_count?></td>
						</tr>
						<tr>
							<td ><b>Total Weight</b></td>
							<td style="width: 62px;"><?=round($get_sum_for_weight,2);?></td>
						</tr> -->
				</table>
			</td> 

			<td colspan="2" class="text-left font-13"><strong>Sub Total</strong></td>			
			<td colspan="4" class="text-right font-13"><strong><?php echo CURR.' '.$db->rp_number_format(round($totalprice,2),2); ?></strong></td>
		</tr>

		<tr>
			<td colspan="2" class="text-left font-13"><strong>Cash Discount</strong></td>			
			<td colspan="4" class="text-right font-13"><strong>
				<?php
				if($cart_detail_d['cash_discount_amount'] == "0")
				{ 
					echo "  ";;
				}
				else
				{
					echo CURR.' '.$db->rp_number_format(round($cart_detail_d['cash_discount_amount'],2),2);
				}
				?>
				</strong>
			</td>
		</tr>

		<tr>
			<td colspan="2" class="text-left font-13"><strong>Additional Discount</strong></td>			
			<td colspan="4" class="text-right font-13"><strong>
				<?php if($cart_detail_d['additional_discount_amount'] == "0")
				{
					echo "  ";
				}
				else
				{
					echo CURR.' '.$db->rp_number_format(round($cart_detail_d['additional_discount_amount'],2),2);
				} ?></strong>
			</td>
		</tr>

		<tr>
			<td colspan="2" class="text-left font-13"><strong>Transport Charge</strong></td>			
			<td colspan="4" class="text-right font-13"><strong><?php echo CURR.' '.$db->rp_number_format(round($cart_detail_d['transport_charge'],2),2); ?></strong></td>
		</tr>

		<tr>
			<td colspan="2" class="text-left font-13"><strong>Packing & Forwarding Charge</strong></td>			
			<td colspan="4" class="text-right font-13"><strong><?php echo CURR.' '.$db->rp_number_format(round($cart_detail_d['packing_charge'],2),2); ?></strong></td>
		</tr>

		<tr>
			<?php
			$total_tax_amt = $totalprice - ($cart_detail_d['cash_discount_amount'] + $cart_detail_d['additional_discount_amount']) + ($cart_detail_d['transport_charge']+$cart_detail_d['packing_charge']);
			?>
			<td colspan="2" class="text-left font-13"><strong>Total Taxable Amount</strong></td>
			<td colspan="4" class="text-right font-13"><strong><?php echo CURR.' '.$db->rp_number_format(round($total_tax_amt,2),2); ?></strong></td>
		</tr>

		<?php
		if($cart_detail_d['igst_amount']!="0")
		{
			if($Distri_data_a['type_of_executive']==7)
			{
				if (strtolower(CLIENT_STATE) == strtolower($Distri_data_a['state'])) 
				{
					?>

						<tr>
							<td colspan="2" class="text-left"><strong>C GST</strong></td>			
							<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($cart_detail_d['igst_amount']/2))?></strong> </td>
						</tr>
						<tr>
							<td colspan="2" class="text-left"><strong>S GST</strong></td>			
							<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($cart_detail_d['igst_amount']/2))?></strong></td>
						</tr>
					<?php
				}
				else
				{
						?>
						<tr>
							<td colspan="2" class="text-left"><strong>IGST</strong></td>		
							<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($cart_detail_d['igst_amount']))?></strong></td>
						</tr>
						<tr>
							<td colspan="2" class="text-left"></td>			
							<td colspan="4" class="text-right "></td>
						</tr>
						<?php
				}
			}
			else
			{
				if (strtolower(CLIENT_STATE) == strtolower($Distri_data_a['state'])) 
				{
					$totalgst = $totalgstamount;
					$cashdisgst = ($cart_detail_d['cash_discount_amount']*$cart_detail_d['cd_gst']/100);
					$adddisgst = ($cart_detail_d['additional_discount_amount']*$cart_detail_d['ad_gst']/100);
					$packinggst = ($cart_detail_d['packing_charge']*$cart_detail_d['packing_charge_gst']/100);
					$transportgst = ($cart_detail_d['transport_charge']*$cart_detail_d['transport_charge_gst']/100);

					$finalgst = $totalgst - ($cashdisgst+$adddisgst) + ($packinggst+$transportgst);
					?>
					<tr>
						<td colspan="2" class="text-left"><strong>C GST</strong></td>			
						<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($finalgst/2,2))?></strong> </td>
					</tr>
					<tr>
						<td colspan="2" class="text-left"><strong>S GST</strong></td>			
						<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($finalgst/2,2))?></strong></td>
					</tr>
					<?php
				}
				else
				{
					$totalgst = $totalgstamount;
					$cashdisgst = ($cart_detail_d['cash_discount_amount']*$cart_detail_d['cd_gst']/100);
					$adddisgst = ($cart_detail_d['additional_discount_amount']*$cart_detail_d['ad_gst']/100);
					$packinggst = ($cart_detail_d['packing_charge']*$cart_detail_d['packing_charge_gst']/100);
					$transportgst = ($cart_detail_d['transport_charge']*$cart_detail_d['transport_charge_gst']/100);

					$finalgst = $totalgst - ($cashdisgst+$adddisgst) + ($packinggst+$transportgst);
					?>
						<tr>
							<td colspan="2" class="text-left"><strong>IGST</strong></td>		
							<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($finalgst,2))?></strong></td>
						</tr>
						<tr>
							<td colspan="2" class="text-left"></td>			
							<td colspan="4" class="text-right "></td>
						</tr>
					<?php
				}
			}
		}
		else
		{
			?>
			<tr>
				<td colspan="2" class="text-left"></td>			
				<td colspan="4" class="text-right "></td>
			</tr>
			<tr>
				<td colspan="2" class="text-left"></td>			
				<td colspan="4" class="text-right "></td>
			</tr>
			<?php
		}
		?>

		<?php
		if($cart_detail_d['tcs_amount']!="0")
		{
			?>
			<tr>
				<td colspan="2">
					<strong>TCS (<?= TCS_CHARGE_IN_PER ?>%)</strong>
				</td>
				<td colspan="4" class="text-right"><strong><?=$cart_detail_d['tcs_amount']?></strong></td>
			</tr>
		<?php
		}
		else
		{	
			?>
			<tr>
				<td colspan="2"></td>
				<td colspan="4" class="text-right"><strong></td>
			</tr>
			<?php
		}
		?>
		
		<tr>
			<td colspan="2">
				<strong>Round Off</strong>
			</td>
			<td colspan="4" class="text-right"><strong>
			<?=$db->rp_number_format($f1,2)?>
			</strong></td>
		</tr>
		<tr style="font-size: 16px;">
			<td colspan="2">
				<strong>Grand Total</strong>
			</td>
			<td colspan="4" class="text-right"><strong>
				<?php if($cart_detail_d['tcs_amount'] !="" && $cart_detail_d['tcs_amount'] != "0")
				{
					// $tcs_amount = (($total_tax_amt+$finalgst)*(0.1/100));
					 $tcs_amount = $cart_detail_d['tcs_amount'];
					echo CURR.' '.$db->rp_number_format(round($total_tax_amt+$finalgst+$tcs_amount,0),2);
				}
				else
				{
					echo CURR.' '.$db->rp_number_format(round($total_tax_amt+$finalgst,0),2);
				}
				?>
				</strong>
			</td>
		</tr>	

		</tbody>
	</table>

	<!-- hsn summary -->
		<table>
			<?php
				if (strtolower(CLIENT_STATE) == strtolower($Distri_data_a['state'])) 
				{
					$gst_or_igst="Total GST Rate";
					$gst_or_igst1="Total GST Amount";
				}
				else
				{
					$gst_or_igst="IGST% Rate";
					$gst_or_igst1="IGST Amount";
				}
			?>
			<tr>
				<th class="text-center" colspan="2" >HSN/SAC</th>
				<!-- <th class="text-center" colspan="1">Qty</th> -->
				<th class="text-center" colspan="2">Taxable Value</th>
				<th class="text-center" colspan="2" ><?php echo $gst_or_igst; ?></th>
				<th class="text-center" colspan="2"><?php echo $gst_or_igst1; ?></th>
				<th class="text-center" colspan="1">CGST% Rate</th>
				<th class="text-center" colspan="2">CGST Amount</th>
				<th class="text-center" colspan="2" >SGST% Rate</th>
				<th class="text-center" colspan="3">SGST Amount</th>
			</tr>
			<?php
					$ITEMS=array();
					$items1=$db->rp_getData("sales_return_item","*","isDelete=0 AND invoice_id='".$invoice_id."' GROUP BY hsn_code","",0); 

					while ($item=mysqli_fetch_assoc($items1)) 
					{
						// echo $items1;exit;
						$ITEMS[]=$item1;
						if($items1)
						{
							$gst_rate=$db->rp_getValue("product","igst","id='".$item['pro_id']."' AND isDelete=0",0);
							$count=0;
							$gst_per_amount=0;
							$totalprice=0;
							$final_price=0;
							$boxqty=0;
							$cartoonqty=0;
							$totalproqty=0;
							$totalrate=0;
							$totaldiscount=0;
							$GST=0;
							
								$GST=$gst_rate;
								$CGST=$gst_rate/2;
								$SGST=$gst_rate/2;
								
								$Invoice=$db->rp_getData("sales_return_item","*","isDelete=0 AND invoice_id='".$invoice_id."' AND hsn_code='".$item['hsn_code']."'","",0);
								$InvoiceIds = array();
								while($Invoice_d = mysqli_fetch_assoc($Invoice))
								{
									$InvoiceIds[] = $Invoice_d['id'];
								}
								$InvoiceIds = implode(",",$InvoiceIds);
								// echo $InvoiceIds;exit;
								$total_pro_qty=$db->rp_getValue("sales_return_item","SUM(pro_qty)","id In (".$InvoiceIds.") AND isDelete=0",0);
								$total_pro_taxable=$db->rp_getValue("sales_return_item","SUM(taxable)","id In (".$InvoiceIds.") AND isDelete=0",0);
								
								// echo $total_pro_taxable;
								$cash_amount=($total_pro_taxable*$cart_detail_d['cash_discount'])/100;
								if($cash_amount>$total_pro_taxable)
								{
									$SubPrice=$cash_amount-$total_pro_taxable;
								}
								else
								{
									$SubPrice=$total_pro_taxable-$cash_amount;
								}
						        $gst_per_amount+=$db->rp_getValue("sales_return_item","SUM(igst_amount)","id In (".$InvoiceIds.") AND isDelete=0",0); 
								if (strtolower(CLIENT_STATE) == strtolower($Distri_data_a['state'])) 
								{
									// $cgst_per_amount=($SubPrice*$CGST)/100;
									// $sgst_per_amount=($SubPrice*$SGST)/100;
									$cgst_per_amount=($gst_per_amount)/2;
									$sgst_per_amount=($gst_per_amount)/2; 
									$CGST=$GST/2;
									$SGST=$GST/2;
								}
								else
								{
									$cgst_per_amount="";
									$sgst_per_amount="";
									$CGST="";
									$SGST=""; 
								}
								?>
								<tr>
									<td colspan="2" class="box_qty " style="text-align: center;"><?= $db->rp_getValue("product","hsn_code","isDelete=0 AND id='".$item['pro_id']."'",0) ?></td>
									<!-- <td colspan="1" class="text-center b_qty "><?php echo $total_pro_qty; ?></td> -->
									<td colspan="2" class="text-right rate "><?php echo CURR.' '.number_format($total_pro_taxable,2); ?></td>
									<td colspan="2" class="text-right b_qty "><?php echo $GST; ?><?= ($GST)?"%":""; ?></td>
									<td colspan="2" class="text-right b_qty "><?= ($gst_per_amount)?CURR:""; ?><?= number_format($gst_per_amount,2);  ?></td>
									<td colspan="1" class="text-right b_qty "><?php echo $CGST; ?><?= ($CGST)?"%":""; ?></td>
									<td colspan="2" class="text-right b_qty "><?= ($CGST)?CURR:""; ?><?= number_format($cgst_per_amount,2);  ?></td>
									<td colspan="2" class="text-right b_qty "><?php echo $SGST; ?><?= ($SGST)?"%":""; ?></td>
									<td colspan="3" class="text-right b_qty "><?= ($SGST)?CURR:""; ?><?= number_format($sgst_per_amount,2);  ?></td>
								</tr>
								<?php
								$Total_total_pro_qty+=$total_pro_qty;
								$Total_total_pro_taxable+=$total_pro_taxable;
								$Total_gst_per_amount+=$gst_per_amount;
								$Total_cgst_per_amount+=$cgst_per_amount;
								$Total_sgst_per_amount+=$sgst_per_amount;
						}
					}

				?>
				<tr >
					<td colspan="2" class="text-center"><strong>Total</strong></td>  
					<td colspan="2" class="text-right"><b><?php echo  CURR.' '.number_format($Total_total_pro_taxable,2); ?></b></td>
					<td colspan="2" class="text-right"><b><?php ?></b></td>
					<td colspan="2" class="text-right"><b><?= ($Total_gst_per_amount)?CURR:""; ?><?= number_format($Total_gst_per_amount,2); ?></b></td>
					<td colspan="1" class="text-right"><b><?php ?></b></td>
					<td colspan="2" class="text-right"><b><?= ($Total_cgst_per_amount)?CURR:""; ?><?= number_format($Total_cgst_per_amount,2); ?></b></td>
					<td colspan="2" class="text-right"><b><?php ?></b></td>
					<td colspan="3" class="text-right"><b><?= ($Total_sgst_per_amount)?CURR:""; ?><?= number_format($Total_sgst_per_amount,2); ?></b></td>
				</tr>
		</table>
	<!-- hsn summary -->

		
		<table>
			<tbody>
				<tr>
					<td colspan="5" rowspan="4" class="no-border-right text-left" style="width:33%;">
						<br><br><br><br>
						<strong>Prepared By</strong>	
					</td>

					<td colspan="5" rowspan="4" class="no-border-right" style="width:33%;">
						<br><br><br><br>
						<center><strong>Checked By</strong></center>	
					</td>

					<td colspan="6" rowspan="4" class="text-right" style="width:33%;">

						<strong style="margin-right: 20px;">For, <?= CLIENT_BRAND_NAME ?><br></strong>

						<br><br><br>
						<strong style="margin-right: 20px;">
						Authorised SIgnatory
						</strong>	
					</td>
				</tr>
			</tbody>
		</table>
</body>
</html>