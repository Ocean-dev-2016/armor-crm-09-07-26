<?php
$page_id=566;$page_slug='page_order_ajax';
require_once("connect_in.php");
require_once("../include/no_to_word.php");
require_once("../include/quotation.class.php");
$ObjQuotation=new Quotation();
$ntw = new NumToWord_RP;
	$quotation_id	= $_REQUEST['quotation_id'];
	$isMpdfMode = (!empty($_REQUEST['app_pdf']) || !empty($_REQUEST['mpdf']));
	$cart_detail_r 	= $db->rp_getData("quotation_detail","*","id='".$quotation_id."'","",0);
	$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);
$order_date=($cart_detail_d['order_date']!="0000-00-00 00:00:00")?date("d-m-Y",strtotime($cart_detail_d['order_date'])):"";
$brand_product_id = $db->rp_getValue("quotation_product_item","pro_id","quotation_id='".$quotation_id."' AND isDelete=0",0);
$brand_id = $db->rp_getValue("product","brand_id","id='".$brand_product_id."' AND isDelete=0",0);
$brand_img = $db->rp_getValue("brand","image_path","id='".$brand_id."' AND isDelete=0",0);
if($brand_img!="")
{
	$brand_logo = SITEURL."images/top_category/".$brand_img;
}
else
{
	$brand_logo = SITEURL."images/chanakya.png";
}
$brand_logo1 = SITEURL."images/cmk_logo_1.png";
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
/*for watermark*/
$cl = "";
if($cart_detail_d['status']!=1)
{
	$cl = "addwatermark";
}
if ($isMpdfMode) {
	$cl = "";
}
$max_discount = $db->rp_getValue("quotation_product_item","MAX(`discount`)","quotation_id='".$quotation_id."'",0);
if($max_discount=="0")
{
	$colSpan1= 4;
}
else
{
	$colSpan1= 3;
}
$items122=$db->rp_getData("quotation_product_item","*","quotation_id='".$quotation_id."'");
		$items122_d 	= mysqli_fetch_assoc($items122);
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
		.discount_p{
			width: 6% !important;
			min-width: 6%!important;
			max-width: 6%!important;
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
		/*.addwatermark{
		background: url(../images/bgwatermark.png) no-repeat;
		background-size: cover;
		}*/
		</style>
	</head>
	<body>
		<table>
			<tbody class="<?= $cl; ?>">
				<!-- <tr>
					<td colspan="16" class="no-border-bottom height-5"></td>
				</tr> -->
				<tr>
					<td colspan="16" style="border-bottom:none;padding:4px 0;">
						<img style="width:100%;max-height:<?= $isMpdfMode ? '70' : '90' ?>px;display:block;" src="<?= VIEW_LOGO_All ?>">
					</td>
				</tr>
				<tr>
					<td colspan="16" class="no-border-bottom no-border-top height-5 text-center"><strong>GST - <?= COMPANY_GST ?></strong></td>
				</tr>
				<tr>
					<td colspan="5" class="no-border-right no-border-bottom bg-gray"></td>
					<td colspan="5" class="text-center no-border-right no-border-bottom" style="background-color: <?= VIEW_COLOR ?>"><b>Proposal</b></td>
					<td colspan="6" class="no-border-bottom bg-gray"></td>
				</tr>
				<tr>
					<td colspan="16" class="no-border-bottom no-border-top height-5"></td>
				</tr>
				<tr class="vertical-top">
					<td colspan="5" class="no-border-bottom no-border-top border-r-width border-blue">
						<strong>To,<br/></strong>
						<?php
						$GetDistributor_a = $db->rp_getData("quotation_detail","*","id='".$quotation_id."' AND isDelete=0","",0);
						$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
						?>
						<strong><?php echo $Distri_data_a['company_name']; ?></strong><br/><span>
						<?php echo wordwrap($Distri_data_a['address'],40,"<br>\n")."  <br/>".$Distri_data_a['city']." - ".$Distri_data_a['zip']." , ".$Distri_data_a['state']." , ".$Distri_data_a['country'] ?></span><br/><br>
						<span>Contact Person : <?=$Distri_data_a['customer_name']?></span><br>
						<span>Contact Number : <?=$Distri_data_a['contact_number']?></span><br>
						<span>Email : <?= $Distri_data_a['email'] ?></span><br/>
						<span>State of Supply : <?=$Distri_data_a['state']?></span><br/>
						<strong><span>GST No : <?=$Distri_data_a['gst']?></span></strong><br/>
						<span>Vendor Code : <?= $cart_detail_d['vendor_code'] ?></span><br/>
					</td>
					<!-- <div class="vertical_line"></div> -->
					<td colspan="5" class="no-border-bottom no-border-top border-r-width border-blue">
						<strong>Address<br/></strong>
						<strong>Shipping Address</strong><br>
						<?= wordwrap($cart_detail_d['shipping_address'],40,"<br>\n"); ?> <br><br>
						<strong>Billing Address</strong><br>
						<?= wordwrap($cart_detail_d['billing_address'],40,"<br>\n"); ?> <br>
					</td>
					<!-- <div class="vertical_line1"></div> -->
					<?php
						$inquiry_date = $db->rp_getValue("no_order_inquiry","inquiry_date","isDelete=0 AND id='".$cart_detail_d['inquiry_id']."' ");
					?>
					<td colspan="6" class="no-border-bottom no-border-top">
						<strong>Details<br/></strong>
						<table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 100% !important;">
							<tr>
								<td class="no-border-right no-border-bottom no-border-left no-border-top">Quotation No : </td>
								<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $cart_detail_d['quotation_no'] ?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left">Quotation Date : </td>
								<td class="no-border-right no-border-bottom no-border-left"><?= date('d-m-Y', strtotime($cart_detail_d['quotation_date'])); ?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left">Inquiry No : </td>
								<td class="no-border-right no-border-bottom no-border-left"><?= "INQ/" . $cart_detail_d['inquiry_id']; ?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left">Inquiry Date : </td>
								<td class="no-border-right no-border-bottom no-border-left"><?= date('d-m-Y', strtotime($inquiry_date)); ?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left">Transport By : </td>
								<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("transport_by","name","isDelete=0 AND id='".$cart_detail_d['transport_through']."' ") ?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left">Transporter Detail : </td>
								<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("transport_master","name","isDelete=0 AND id='".$cart_detail_d['transport_name']."' ") ?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left">Tendor Code : </td>
								<td class="no-border-right no-border-bottom no-border-left"><?= $cart_detail_d['tendor_code'] ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="16" class="no-border-top height-5"></td>
				</tr>
			</tbody>
		</table>
		<table>
			<tbody class="<?= $cl; ?>">
				<tr class="text-center" style="background-color: <?= VIEW_COLOR ?>;">
					<th colspan="1" class="srno text-center" >SR</th>
					<th colspan="1" class="image-width text-center">Image</th>
					<th colspan="4" class="pname model text-center">Item Description</th>
					<th colspan="1" class="text-center">UNIT</th>
					<th class="pname1 text-center">HSN Code</th>
					<th colspan="1" class="text-center carrtoon_qty">Qty</th>
					<th colspan="2" class="rate text-center">Rate</th>
					<?php if($max_discount != "0"){ ?>
					<th colspan="1" class="discount_p text-center">Dis %</th>
					<?php } ?>
					<th colspan="1" class="text-center">Net Rate</th>
					<th colspan="<?=$colSpan1;?>" class="text-center">Total Amount</th>
				</tr>
				<?php
				$ITEMS=array();
				$items1=$db->rp_getData("quotation_product_item","*","quotation_id='".$quotation_id."'");
				while ($item1=mysqli_fetch_assoc($items1))
				{
					$item1['display_order']=$db->rp_getValue("product","display_order","id='".$item1['pro_id']."' AND isDelete=0");
					$item1['weight_display_order']=$db->rp_getValue("weight","display_order","id='".$item1['weight_id']."' AND isDelete=0");
					$item1['image_path']=$db->rp_getValue("product","image_path","id='".$item1['pro_id']."' AND isDelete=0","",0);
					if($item1['image_path']!="" )
				{
				$img=SITEURL.PRODUCT.$item1['image_path'];
				// $br="";
				}
				else
				{
				$img=SITEURL."images/no_image_found.jpg";
				//$br="border:1px solid #000";
				}
				$ITEMS[]=$item1;
					// print_r($item1);
					// exit();
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
						// $GST=$db->rp_getValue("product","igst","id='".$item['pro_id']."' AND isDelete=0",0);
						if($cart_detail_d['igst_amount']!=0)
						{
							$GST=18;
						}
						else
						{
								$GST=0;
						}
						//
						$pro_name=$db->rp_getValue("product","name","id='".$item['pro_id']."' AND isDelete=0");
						$size=$db->rp_getValue("weight","name","id='".$item['weight_id']."' AND isDelete=0");
						$product_code=$db->rp_getValue("product_weight_price","catno","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'",0);
						$unit_id = $db->rp_getValue("product","unit_id","id='".$item['pro_id']."' AND isDelete=0");
						$unit_name = $db->rp_getValue("unit","name","id='".$unit_id."' AND isDelete=0");
						
						$hsncode=$db->rp_getValue("product","hsn_code","id='".$item['pro_id']."' AND isDelete=0",0);
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
						$rate=$item['pro_qty']*$item['unitprice'];
						/*$totalprice+=$item['totalprice'];*/
						$boxqty+=$item['box_qty'];
						$cartoonqty+=$item['cartoon_qty'];
						$totalproqty+=$item['pro_qty'];
						// $totalrate+=$price;
						$totalrate+=$item['unitprice'];
						$totaldiscount+=$item['discount'];
						$discount = $item['discount_amount'];
						$rate_total1 = ($item['original_price']-$discount);
						$rate_total = round(($rate_total1),2);
						$box_qty_total = $rate_total*0.18;
						$max_total = $item['pro_qty']*$rate_total;
						$totalprice +=$max_total;
						$sub_total=$totalprice;
						/*print_r(SITEURL.PRODUCT.$item['image_path']);
						exit();*/
						if($cart_detail_d['currency_code']==1)
				{
				$currency = CURR;
				}
				else if($cart_detail_d['currency_code']==2)
				{
				$currency = DOLLAR;
				}
				if($cart_detail_d['igst_amount']=="" || $cart_detail_d['igst_amount']=="0")
				{
				$grand_total_new=$cart_detail_d['grand_total_rounded'];
				}
				else
				{
				$grand_total_new=$cart_detail_d['grand_total_rounded']+$cart_detail_d['igst_amount'];
				}
				?>
				<tr>
					<td colspan="1" class="text-center srno"><strong><?php echo $count; ?></strong></td>
					<?php
					if($item['image_path']!="")
					{
					?>
					<td colspan="1" class="box_qty text-center"><img style="width: 80px;" src="<?php echo SITEURL.PRODUCT.$item['image_path'] ?>"></td>
					<?php
					}
					else
					{
					?>
					<td colspan="1" class="box_qty text-center"><img style="width: 80px;" src="<?php echo SITEURL.PRODUCT.'default.png' ?>"></td>
					<?php
					}
					?>
					<!-- <td colspan="1" class="" class="text-center" ><?php echo $product_code; ?></td> -->
					<td colspan="4" class="model" style="position: relative;"><?php if($item['weight_id']!=-1){echo "<b>#".$product_code."</b>-".$pro_name." - ".$size." - ".strtoupper($product_code); } else { echo "<b>#".$product_code."</b>-".$pro_name." - ".strtoupper($product_code); } ?><?=(isset($item['pro_description']) && $item['pro_description']!="")?"<br/><br/>".$item['pro_description']:""?></strong></td>
					<td colspan="1" class="box_qty" style="text-align: center;"><?php echo $unit_name; ?></td>
					<td class="text-center"><?php echo $hsncode; ?></td>
					<td colspan="1" class="text-center carrtoon_qty"><?php echo $item['pro_qty']; ?></td>
					<!-- <td colspan="1" class="box_qty" style="text-align: center;"><?php echo $unit_name; ?></td> -->
					<!-- <td colspan="2" class="text-center carrtoon_qty "><?php echo $currency.' '.round($rate_total, 2); ?></td> -->
					<td colspan="2" class="text-center rate "><?php echo $currency.' '.round($item['original_price'], 2); ?></td>
					<?php if($max_discount != "0"){ ?>
					<td colspan="1" class="text-center discount_p">
						<!-- update file   -->
						<?php
							if($item['discount'] != "0")
							{
						?>
						<?=$item['discount']?>
						<?php
						}
						else
						{
						echo " ";
						}
						?>
						<!-- update file   -->
					</td>
					<?php } ?>
					<?php
						if($max_discount == "0")
						{
							$colSpan = 4;
						}
						else
						{
								$colSpan = 3;
						}
					?>
					<td colspan="1" class="text-center"><?php echo $currency.' '.round($rate_total, 2); ?></td>
					<td colspan="<?=$colSpan;?>" class="text-center rate"><?php echo $currency.' '.round($max_total, 2); ?></td>
				</tr>
				<?php
				}
				if(!$isMpdfMode && $count<5)
				{
					for($i=0;$i<12-$count;$i++)
					{
				?>
				<tr class="border">
					<td colspan="1" class="srno"></td>
					<td colspan="1" class="model" ></td>
					<td colspan="4" class="pname"></td>
					<td></td>
					<td colspan="1" class="box_qty"></td>
					<td colspan="1" class="carrtoon_qty"></td>
					<td colspan="2" class="rate"></td>
					
					<?php if($max_discount != "0"){ ?>
					<td colspan="1" class="discount_p text-center"></td>
					<?php } ?>
					<td colspan="1" class="rate"></td>
					<td colspan="<?=$colSpan1;?>" class="rate"></td>
				</tr>
				<?php
				}
				}
				}
				?>
				<tr>
					<td colspan="1" class="srno"></td>
					<td colspan="1" class="model" ></td>
					<td colspan="4" class="pname"></td>
					<td></td>
					<td colspan="1" class="box_qty"></td>
					<td colspan="1" class="carrtoon_qty"></td>
					<td colspan="2" class="rate"></td>
					<?php if($max_discount != "0"){ ?>
					<td colspan="1" class="discount_p text-center"></td>
					<?php } ?>
					<td colspan="1" class="rate"></td>
					<td colspan="<?=$colSpan1;?>" class="rate"></td>
				</tr>
			</tbody>
		</table>
		<table>
			<tbody class="<?= $cl; ?>">
				<tr class="font-size">
					<?php
					if($cart_detail_d['cash_discount']!="" && $cart_detail_d['cash_discount']!=0 && $cart_detail_d['cash_discount_amount']!="" && $cart_detail_d['cash_discount_amount']!=0 && $cart_detail_d['additional_discount']!="" && $cart_detail_d['additional_discount']!=0 && $cart_detail_d['additional_discount_amount']!="" && $cart_detail_d['additional_discount_amount']!=0)
					{
						$rowspan=10;
					}
					else if($cart_detail_d['cash_discount']!="" && $cart_detail_d['cash_discount']!=0 && $cart_detail_d['cash_discount_amount']!="" && $cart_detail_d['cash_discount_amount']!=0)
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
						$gst_amount=($subtotal*$GST)/100;
						$final_price = $subtotal;
						$new_final_price=$subtotal+$gst_amount+$cart_detail_d['transport_charge']+$cart_detail_d['packing_charge']+$cart_detail_d['tcs_amount'];
						//13-11-2021 by milan
						$rowspan=9;
						
					$whole = floor($grand_total_new);      // 1
					$fraction = $grand_total_new - $whole;
					$f1=  $db->rp_number_format((float)$fraction, 3, '.', '');
					}
					//13-11-2021 by milan
					else if($cart_detail_d['additional_discount']!="" && $cart_detail_d['additional_discount']!=0 && $cart_detail_d['additional_discount_amount']!="" && $cart_detail_d['additional_discount_amount']!=0)
					{
						// if($cart_detail_d['cash_discount']!="" && $cart_detail_d['cash_discount']!=0 && $cart_detail_d['cash_discount_amount']!="" && $cart_detail_d['cash_discount_amount']!=0)
							// {
							$totalprice=$totalprice-$cart_detail_d['cash_discount_amount'];
							$add_dis_amount=($totalprice*$cart_detail_d['additional_discount'])/100;
							if($add_dis_amount>$totalprice)
							{
								$subtotal=$add_dis_amount-$totalprice;
							}
							else
							{
								$subtotal=$totalprice-$add_dis_amount;
							}
							$gst_amount=($subtotal*$GST)/100;
							$final_price = $subtotal;
							//13-11-2021 by milan
							// $new_final_price=$subtotal+$gst_amount;
							$new_final_price=$subtotal+$gst_amount+$cart_detail_d['transport_charge']+$cart_detail_d['packing_charge']+$cart_detail_d['tcs_amount'];
							//13-11-2021 by milan
							$rowspan=9;
					$whole = floor($grand_total_new);      // 1
					$fraction = $grand_total_new - $whole;
					$f1=  $db->rp_number_format((float)$fraction, 3, '.', '');
					// }
					}
					
					//13-11-2021 by milan
					// else if($cart_detail_d['cash_discount']=="" && $cart_detail_d['cash_discount']==0 && $cart_detail_d['cash_discount_amount']=="" && $cart_detail_d['cash_discount_amount']==0 && $cart_detail_d['additional_discount']=="" && $cart_detail_d['additional_discount']==0 && $cart_detail_d['additional_discount_amount']=="" && $cart_detail_d['additional_discount_amount']==0)
					else
					{
						$final_price = $totalprice+$cart_detail_d['transport_charge']+$cart_detail_d['packing_charge'];
						$gst_amount=($final_price*$GST)/100;
						$new_final_price=round($final_price+$gst_amount+$cart_detail_d['tcs_amount']);
						$final_gst_amount = $gst_amount;
							$rowspan=8;
						// $hello = "Hello3";
						// $fp=$final_price+$gst_amount;
						$fp=$grand_total_new;
						$whole = floor($fp);      // 1
					$fraction = $fp - $whole;
					$f1=  $db->rp_number_format((float)$fraction, 3, '.', '');
					}
					?>
					<td colspan="8" class="" rowspan="<?= $rowspan; ?>" style="vertical-align: top;">
						<span class="font-13"><b>Terms & Condition : </b></span><br>
						<div class="row">
							<div class="col-md-12" style="height: 90px !important;">
								<span class="font-13"><?= $cart_detail_d['terms_comdition'] ?></span><br>
							</div>
						</div>
						<?php $grand_total_words = $ntw->rp_convertNumToWord($new_final_price); ?>
						<span class="font-13"><b>Grand Total In Words</b> :
							<?php	if($cart_detail_d['tcs_amount'] !="" && $cart_detail_d['tcs_amount'] != "0")
							{
								//echo $currency.' '.$db->rp_number_format(round($cart_detail_d['grand_total'],2));
							}
							else{
								//echo $currency.' '.$db->rp_number_format($grand_total_new1);
							}
							?>
							
							<?php echo ucwords(strtolower($grand_total_words)); ?>
						</span>
						<span></span><br>
						<span class="font-13">
							<b>Bank Details : </b>Bank Name : <?= COMPANY_BANK ?>, Bank Branch : <?= COMPANY_BANK_BRANCH ?><br>
						Bank Account No : <?= COMPANY_BANK_ACC_NO ?>, Bank IFSC Code : <?= COMPANY_BANK_IFSC ?></span><br>
						<span class="font-13"><b>Remarks</b></span><br>
						<span class="font-13"><?php echo $cart_detail_d['remarks'] ?></span><br>
						<span style="color: red;">Contact Sales Person : <?=strip_tags($cart_detail_d['faithfully'])?> &nbsp; </span>
					</td>
					<td colspan="4" class="text-left font-13"><strong>Sub Total</strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency.' '.$db->rp_number_format(round($sub_total,2)); ?></strong></td>
				</tr>
				<?php if($cart_detail_d['cash_discount'] != "" && $cart_detail_d['cash_discount'] != "0"){ ?>
				<tr>
					<td colspan="4" class="text-left font-13"><strong>Cash Discount (<?= $cart_detail_d['cash_discount']; ?> %)</strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency.' '.$db->rp_number_format(round($cart_detail_d['cash_discount_amount'],2)); ?></strong></td>
				</tr>
				<?php } ?>
				<?php if($cart_detail_d['additional_discount'] != "" && $cart_detail_d['additional_discount'] != "0"){ ?>
				<tr>
					<td colspan="4" class="text-left font-13"><strong>Additonal Discount (<?= $cart_detail_d['additional_discount']; ?> %)</strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency.' '.$db->rp_number_format(round($cart_detail_d['additional_discount_amount'],2)); ?></strong></td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="4" class="text-left font-13"><strong>Transport Charge</strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency.' '.$db->rp_number_format(round($cart_detail_d['transport_charge'],2)); ?></strong></td>
				</tr>
				<tr>
					<td colspan="4" class="text-left font-13"><strong>Packing & Forwarding Charge</strong></td>
					<td colspan="4" class="text-right font-13"><strong><?php echo $currency.' '.$db->rp_number_format(round($cart_detail_d['packing_charge'],2)); ?></strong></td>
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
					<td colspan="4" class="text-left"><strong>C GST 0.05 %</strong></td>
					<td colspan="4" class="text-right "><strong><?= ($currency. $db->rp_number_format($cart_detail_d['igst_amount']/2))?></strong> </td>
				</tr>
				<tr>
					<td colspan="4" class="text-left"><strong>S GST 0.05 %</strong></td>
					<td colspan="4" class="text-right "><strong><?= ($currency. $db->rp_number_format($cart_detail_d['igst_amount']/2))?></strong></td>
				</tr>
				<?php
				}
				else
				{
				?>
				<tr>
					<td colspan="4" class="text-left"><strong>IGST 0.1 %</strong></td>
					<td colspan="4" class="text-right "><strong><?= ($currency. $db->rp_number_format($cart_detail_d['igst_amount']))?></strong></td>
				</tr>
				<tr>
					<td colspan="4" class="text-left"></td>
					<td colspan="4" class="text-right "></td>
				</tr>
				<?php
				}
				}
				else
				{
				if (strtolower(CLIENT_STATE) == strtolower($Distri_data_a['state']))
				{
				?>
				<tr>
					<td colspan="4" class="text-left"><strong>C GST 9 %</strong></td>
					<td colspan="4" class="text-right "><strong><?= ($currency. $db->rp_number_format($cart_detail_d['igst_amount']/2))?></strong> </td>
				</tr>
				<tr>
					<td colspan="4" class="text-left"><strong>S GST 9 %</strong></td>
					<td colspan="4" class="text-right "><strong><?= ($currency. $db->rp_number_format($cart_detail_d['igst_amount']/2))?></strong></td>
				</tr>
				<?php
				}
				else
				{
				?>
				<tr>
					<td colspan="4" class="text-left"><strong>IGST 18 %</strong></td>
					<td colspan="4" class="text-right "><strong><?= ($currency. $db->rp_number_format($cart_detail_d['igst_amount']))?></strong></td>
				</tr>
				<tr>
					<td colspan="4" class="text-left"></td>
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
					<td colspan="4" class="text-left"></td>
					<td colspan="4" class="text-right "></td>
				</tr>
				<tr>
					<td colspan="4" class="text-left"></td>
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
					<td colspan="4">
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
					<td colspan="4"></td>
					<td colspan="4" class="text-right"><strong></td>
				</tr>
				<?php
				}
				?>
				
				<tr>
					<td colspan="4">
						<strong>Round Off</strong>
					</td>
					<td colspan="4" class="text-right"><strong>
						<?php
						if($f1 !="" ){
							echo $f1;
						}
						else{
								echo "0.000";
						}
						?>
					</strong></td>
				</tr>
				<!-- <tr style="background-color: #81bfc7;font-size: 16px;"> -->
				<tr style="background-color: <?= VIEW_COLOR ?>;font-size: 16px;">
					<td colspan="4">
						<strong>Grand Total</strong>
					</td>
					<!-- <td colspan="4" class="text-right"><strong><?php echo $currency.' '.$db->rp_number_format(round($new_final_price,2)); ?></strong></td> -->
					<?php
					if(round($f1)<= 0.5)
					{
						$grand_total_new1=$cart_detail_d['grand_total']-$f1;
					}
					else
					{
						$grand_total_new1=$cart_detail_d['grand_total']-$f1+1;
					}
					?>
					<td colspan="4" class="text-right"><strong>
						<?php	if($cart_detail_d['tcs_amount'] !="" && $cart_detail_d['tcs_amount'] != "0")
						{
							echo $currency.' '.$db->rp_number_format(round($cart_detail_d['grand_total'],2));
						}
						else{
							echo $currency.' '.$db->rp_number_format($grand_total_new1);
						}
						?>
						
						<!-- <?php echo $currency.' '.$db->rp_number_format(round($cart_detail_d['grand_total'],2)); ?> -->
						</strong>
					</td>
				</tr>

				</tbody>
			<!-- <tr> -->
			
			<!-- </tr> -->
		</table>

		<table>
			<tbody>
				<tr>

			<td colspan="5" rowspan="4" class="no-border-right" style="vertical-align:bottom;padding-top:<?= $isMpdfMode ? '25' : '40' ?>px;">
				<center><strong>Prepared By&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></center>	
			</td>

			<td colspan="5" rowspan="4" class="no-border-right" style="vertical-align:bottom;padding-top:<?= $isMpdfMode ? '25' : '40' ?>px;">
				<center><strong>Checked By</strong></center>	
			</td>

			<td colspan="6" rowspan="4" style="vertical-align:bottom;padding-top:<?= $isMpdfMode ? '25' : '40' ?>px;">

				<center><strong>For, <?= CLIENT_BRAND_NAME ?>.</strong></center>

				<center><strong>Authorised SIgnatory</strong></center>	
			</td>
		</tr>
			</tbody>
			<!-- <tr> -->
			
			<!-- </tr> -->
		</table>
	</body>
</html>