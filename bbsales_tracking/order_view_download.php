<?php
$page_id=566;$page_slug='page_order_ajax';
require_once("connect_in.php");
include("../include/no_to_word.php");
include("../include/quotation.class.php");
$ObjQuotation=new Quotation();
$ntw = new NumToWord_RP;
$quotation_id	= $_REQUEST['order_id'];
$cart_detail_r 	= $db->rp_getData("orders","*","id='".$quotation_id."'","",0);
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
/*.addwatermark{
  background: url(../images/bgwatermark.png) no-repeat;
  background-size: cover;
}*/
</style>
</head>
<body>
<table>
	<tbody class="<?= $cl; ?>">
		<tr>
			<td colspan="16" class="no-border-bottom height-5"></td>
		</tr>
		<tr>
			<td colspan="4" class="no-border-bottom text-left border-r-width border-gray" style="width: 15%!important;"><span style="float: left" ><img width="170"  src="<?= SITEURL.VIEW_LOGO ?>"></span></td>
			<!-- <div class="vertical_line2"></div> -->
			<td colspan="12" class="no-border-bottom" style="font-size: 27px;"><strong><?php echo CLIENT_BRAND_NAME ?></strong><br>
			<p class="font-13"><strong>Factory Address : </strong><?= FACTORY_ADDRESS ?><br>
			<strong>Office Address : </strong><?= CLIENT_ADDRESS ?><br>
			<?= OFFICE_PHONE ?> <?= OFFICE_EMAIL ?> 
			</p>
			</td>
			<!-- <td colspan="4" style="text-align: right;"><span style="float: right" ><img width="125" height="150" src="<?= $brand_logo1 ?>"></span></td> -->
		</tr>
		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5"></td>
		</tr>
		<tr>

			<td colspan="5" class="no-border-right no-border-bottom bg-gray"></td>
			<td colspan="6" class="text-center no-border-right no-border-bottom" style="background-color: <?= VIEW_COLOR ?>"><b>Proforma Invoice / Sales Order</b></td>
			<td colspan="5" class="no-border-bottom bg-gray"></td>

			<!-- <td colspan="16" class="text-center" style="background-color: #E5E5E5 !important;border-bottom: none;"><strong style="background-color: #8cdee8;padding-left: 50px;padding-right: 50px;padding-top: 5px;padding-bottom: 4px;"><b>Quotation</b></strong></td> -->
		</tr>
		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5"></td>
		</tr>
		<tr class="vertical-top">
			<td colspan="5" class="no-border-bottom no-border-top border-r-width border-blue vertical-top">
				<strong>To,<br/></strong>
				<?php
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0","",0);
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
				?>
				<strong><?php echo $cart_detail_d['company_name']; ?></strong><br/><span>
				<?php echo $Distri_data_a['address']."  <br/>".$Distri_data_a['city']." - ".$Distri_data_a['zip']." , ".$Distri_data_a['state']." , ".$Distri_data_a['country'] ?></span><br/><br>
				<span>Contact Person : <?=$Distri_data_a['cname']?></span><br>
				<span>Contact Number : <?=$Distri_data_a['phone']?></span><br>
				<span>Email : <?=$Distri_data_a['email']?></span><br/>
				<span>State of Supply : <?=$Distri_data_a['state']?></span><br/>
				<strong><span>GST No : <?=$Distri_data_a['gst']?></span></strong><br/>
				<span>Vendor Code : <?= $cart_detail_d['vendor_code'] ?></span><br/>
				<!-- <span>Mobile : <?=$Distri_data_a['phone']?></span><br/> -->
				<!-- <span>Date:- :</span><br/> -->
			</td>
			<!-- <div class="vertical_line"></div> -->
			<td colspan="6" class="no-border-bottom no-border-top border-r-width border-blue vertical-top">
				<strong>Address<br/></strong>
				<strong>Shipping Address</strong><br>
				<?= $cart_detail_d['shipping_address'] ?> <br><br>
				<strong>Billing Address</strong><br>
				<?= $cart_detail_d['billing_address'] ?> <br>
			</td>
			<!-- <div class="vertical_line1"></div> -->
			<?php 
				$inquiry_date = $db->rp_getValue("no_order_inquiry","inquiry_date","isDelete=0 AND id='".$cart_detail_d['inquiry_id']."' ");
				?>
			<td colspan="5" class="no-border-bottom no-border-top vertical-top">
				<strong>Details<br/></strong>
					<table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 0mm!important;">
						<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top">Sales Order No : </td>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $cart_detail_d['order_no'] ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Sales Order Date : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= date('m-d-Y', strtotime($cart_detail_d['order_date'])); ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Quotation  No : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("quotation_detail","quotation_no","id='".$cart_detail_d['quotation_id']."'")?></td>
						</tr>
						<!-- <tr>
							<td class="no-border-right no-border-bottom no-border-left">Quotation Date : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= date('d-m-Y',strtotime($db->rp_getValue("quotation_detail","quotation_date","id='".$cart_detail_d['quotation_id']."'")))?></<td>
						</tr> -->
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Transport By : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("transport_by","name","isDelete=0 AND id='".$cart_detail_d['transport_through']."' ") ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Transporter Name : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("transport_master","name","isDelete=0 AND id='".$cart_detail_d['transport_name']."' ") ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Tendor Code : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $cart_detail_d['tendor_code'] ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Challan No : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $cart_detail_d['chalan_no'] ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">PO No : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $cart_detail_d['po_no'] ?></td>
						</tr>
					</table>
					<!-- <span>Quotation No</span> <span style="padding-left: 46px;"><?= $cart_detail_d['quotation_no'] ?></span> <br>
					<span>Quotation Date</span> <span style="margin-left: 36px;"><?= date('m-d-Y', strtotime($cart_detail_d['quotation_date'])); ?><br> -->
					<!-- <span>Inquiry No</span> <span style="margin-left: 67px;"><?= "INQ/" . $cart_detail_d['inquiry_id']; ?><br> -->
					<!-- <span>Inquiry Date</span> <span style="margin-left: 56px;"><?= date('m-d-Y', strtotime($inquiry_date)); ?><br> -->
					<!-- <span>Transport By</span> <span style="margin-left: 54px;"><?= $db->rp_getValue("transport_by","name","isDelete=0 AND id='".$cart_detail_d['transport_through']."' ") ?><br> -->
					<!-- <span>Transporter Name</span> <span style="margin-left: 20px;"><?= $db->rp_getValue("transport_master","name","isDelete=0 AND id='".$cart_detail_d['transport_name']."' ") ?><br>
					<span>Tendor Code</span> <span style="margin-left: 54px;"><?= $cart_detail_d['tendor_code'] ?><br> -->

				<!-- Quotation No : <?= $cart_detail_d['quotation_no'] ?><br>
				Quotation Date : <?= date('m-d-Y', strtotime($cart_detail_d['quotation_date'])); ?> <br>
				Inquiry No : <?= "INQ/" . $cart_detail_d['inquiry_id']; ?> <br>
				<?php 
				$inquiry_date = $db->rp_getValue("no_order_inquiry","inquiry_date","isDelete=0 AND id='".$cart_detail_d['inquiry_id']."' ");
				?>
				Inquiry Date : <?= date('m-d-Y', strtotime($inquiry_date)); ?> <br>
				Transport By : <?= $db->rp_getValue("transport_by","name","isDelete=0 AND id='".$cart_detail_d['transport_through']."' ") ?> <br>
				Transporter Name : <?= $db->rp_getValue("transport_master","name","isDelete=0 AND id='".$cart_detail_d['transport_name']."' ") ?> <br>
				Tendor Code : <?= $cart_detail_d['tendor_code'] ?> <br> -->
			</td>
		</tr>
		<!-- <tr>
			<td colspan="8"><strong>Quatation No.</strong> : <?=$cart_detail_d['quotation_no']?></td>
			
		</tr>
		<tr>
			<td colspan="8"><strong>Your inq No.</strong> : #INQ/<?=$cart_detail_d['inquiry_id']?></td>
			
		</tr>
		<tr>
			<td colspan="8">
				<?php $inq_date = $db->rp_getValue("no_order_inquiry","inquiry_date","isDelete=0 AND id='".$cart_detail_d['inquiry_id']."' ") ?>
				<strong>Inquiry Date </strong> : <?php echo date('d-m-Y', strtotime($inq_date)); ?><br>
				<strong>Vendor Code </strong> : <?=$cart_detail_d['vendor_code']?><br>
				<strong>Tendor Code </strong> : <?=$cart_detail_d['tendor_code']?><br>
				<strong>Tender No </strong>   : <?=$cart_detail_d['tendor_no']?><br>
				<strong>Transport Through </strong>   : <?= $db->rp_getValue("transport_by","name","id='".$cart_detail_d['transport_through']."'") ?><br>
				<strong>Transport Name </strong>   : <?= $db->rp_getValue("transport_master","name","transport_by_id='".$cart_detail_d['transport_through']."' AND id='".$cart_detail_d['transport_name']."'") ?>
			</td>
		</tr> -->
		<tr>
			<td colspan="16" class="no-border-top height-5"></td>
		</tr>
		<!-- <tr style="background-color: #81bfc7;" class="text-left"> -->
		<tr class="text-center" style="background-color: <?= VIEW_COLOR ?>;">
			<th colspan="1" class="srno text-center" >SR</th>
			<!-- <th colspan="1" class="text-center" class="text-center">MODEL NO </th> -->
			<th colspan="1" class="image-width text-center">Image</th>
			<th colspan="4" class="pname model text-center">Item Description</th>
			<th colspan="1" class="text-center">UNIT</th>
			<th class="pname1 text-center">HSN Code</th>
			<th colspan="1" class="text-center">Qty</th>
			<th colspan="2" class="rate text-center">Rate</th>
			<th colspan="1" class="rate text-center">Dis %</th>
			<th colspan="1" class="text-center">Net Rate</th>
			<th colspan="3" class="text-center">Total Amount<br/><span style="font-size:11px">(INR)</span></th>
		</tr>
		<?php
		$ITEMS=array();
		$items1=$db->rp_getData("order_product_item","*","order_id='".$quotation_id."'");
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
				$totalprice +=$max_total
				/*print_r(SITEURL.PRODUCT.$item['image_path']);
				exit();*/
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
					<td colspan="4" class="model" style="position: relative;"><?php if($item['weight_id']!=-1){echo $pro_name." - ".$size." - ".strtoupper($product_code); } else { echo $pro_name." - ".strtoupper($product_code); } ?><?=(isset($item['pro_description']) && $item['pro_description']!="")?"<br/><br/>".$item['pro_description']:""?></strong></td>
					<td colspan="1" class="box_qty" style="text-align: center;"><?php echo $unit_name; ?></td>
					<td class="text-center"><?php echo $hsncode; ?></td>
					<td colspan="1" class="text-center rate"><?php echo $item['pro_qty']; ?></td>

					<!-- <td colspan="2" class="text-center carrtoon_qty "><?php echo CURR.' '.round($rate_total, 2); ?></td> -->
					<td colspan="2" class="text-center carrtoon_qty "><?php echo CURR.' '.round($item['original_price'], 2); ?></td>
					<td colspan="1" class="text-center"><?=$item['discount']?></td>
					<td colspan="1" class="text-center"><?php echo CURR.' '.round($rate_total, 2); ?></td>
					<td colspan="3" class="text-center rate"><?php echo CURR.' '.round($max_total, 2); ?></td>
				</tr>
			<?php
			}
			if($count<5)
			{
				for($i=0;$i<5-$count;$i++)
				{
				?>
				<tr class="border">
					<td colspan="1" class="srno"></td>
					<td colspan="1" class="model" ></td>
					<td colspan="4" class="pname"></td>
					<td></td>
					<td colspan="1" class="box_qty"></td>
					<td colspan="1" class="box_qty"></td>
					<td colspan="2" class="carrtoon_qty"></td>
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
			<td colspan="1" class="model" ></td>
			<td colspan="4" class="pname"></td>
			<td></td>
			<td colspan="1" class="box_qty"></td>
			<td colspan="1" class="box_qty"></td>
			<td colspan="2" class="carrtoon_qty"></td>
			<td colspan="1" class="rate"></td>
			<td colspan="1" class="rate"></td>
			<td colspan="3" class="rate"></td>
		</tr>
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
				$new_final_price=$subtotal+$gst_amount;
				$rowspan=9;
				
				$whole = floor($new_final_price);      // 1
	        	$fraction = $new_final_price - $whole;
	        	$f1=  $db->rp_number_format((float)$fraction, 3, '.', '');
			}
			else if($cart_detail_d['additional_discount']!="" && $cart_detail_d['additional_discount']!=0 && $cart_detail_d['additional_discount_amount']!="" && $cart_detail_d['additional_discount_amount']!=0)
			{
				
					$rowspan=9;
			}
			else
			{
				$final_price = $totalprice+$cart_detail_d['transport_charge']+$cart_detail_d['packing_charge'];
				$gst_amount=($final_price*$GST)/100;
				$new_final_price=round($final_price+$gst_amount+$cart_detail_d['tcs_amount']);
				$final_gst_amount = $gst_amount;
				$rowspan=8;

				$fp=$final_price+$gst_amount;
				$whole = floor($fp);      // 1
	        	$fraction = $fp - $whole;
	        	$f1=  $db->rp_number_format((float)$fraction, 3, '.', '');
			}
			?>
			<td colspan="9" class="no-border-bottom" rowspan="<?= $rowspan; ?>" style="vertical-align: top;">	
				<!-- <strong> NOTE: This Quatation will be valid 30 Days from the date of Quatation.</strong><br/>
				<span>   You are requested to Deposit the payment in below Bank</span><br/>
				<span>   Our bank details are as below</span><br/><br/> -->
				<span class="font-13"><b>Terms & Condition :</b></span><br>
				<div class="row">

					<div class="col-md-12" style="height: 145px !important;">
						<span class="font-13"><?= str_replace('rn','',$cart_detail_d['terms_comdition']) ?></span><br></br>
						<!-- <span style="font-size:13px;">Freight & Insurance: To Pay</span><br>
						<span style="font-size:13px;">Warranty : 1 year Against Manufacuring Defect</span></br>
						<span style="font-size:13px;"><strong>Payment 70% ADVANCE AND 30% AFTER DELIVERY 15 DAYS </strong></span><br>
						<span style="font-size:13px;"><strong>Delivery : 1 Week</strong></span><br>
						<span style="font-size:13px;">Validity : 30 Days</span>
						<span style="font-size:13px;">Price : Basis EX-Works(Lothda)</span> -->
					</div>
				</div>
				<?php $grand_total_words = $ntw->rp_convertNumToWord($new_final_price); ?>	
				<span class="font-13"><b>Grand Total In Words</b> : <?php echo ucwords(strtolower($grand_total_words)); ?></span>
				<span></span><br>

				<span class="font-13">
					<b>Bank Details : </b>Bank Name : <?= COMPANY_BANK ?>, Bank Branch : <?= COMPANY_BANK_BRANCH ?><br>
					Bank Account No : <?= COMPANY_BANK_ACC_NO ?>, Bank IFSC Code : <?= COMPANY_BANK_IFSC ?></span><br>

				<span class="font-13"><b>Remarks</b></span><br>
				<span class="font-13"><?php echo $cart_detail_d['remarks'] ?></span><br>

				<span style="color: red;">Contact Sales Person : <?=strip_tags($cart_detail_d['faithfully'])?> &nbsp; </span>
			</td>

			<td colspan="3" class="text-left font-13"><strong>Sub Total</strong></td>			
			<td colspan="4" class="text-right font-13"><strong><?php echo CURR.' '.$db->rp_number_format(round($totalprice,2)); ?></strong></td>
		</tr>

		<?php if($cart_detail_d['cash_discount'] != "" && $cart_detail_d['cash_discount'] != "0"){ ?>
		<tr>
			<td colspan="3" class="text-left font-13"><strong>Cash Discount</strong></td>			
			<td colspan="4" class="text-right font-13"><strong><?php echo CURR.' '.$db->rp_number_format(round($cart_detail_d['cash_discount_amount'],2)); ?></strong></td>
		</tr>
		<?php } ?>
		<?php if($cart_detail_d['additional_discount_amount'] != "" && $cart_detail_d['additional_discount_amount'] != "0"){ ?>
		<tr>
			<td colspan="3" class="text-left font-13"><strong>Additional Discount</strong></td>			
			<td colspan="4" class="text-right font-13"><strong><?php echo CURR.' '.$db->rp_number_format(round($cart_detail_d['additional_discount_amount'],2)); ?></strong></td>
		</tr>
		<?php } ?>
		<tr>
			<td colspan="3" class="text-left font-13"><strong>Transport Charge</strong></td>			
			<td colspan="4" class="text-right font-13"><strong><?php echo CURR.' '.$db->rp_number_format(round($cart_detail_d['transport_charge'],2)); ?></strong></td>
		</tr>

		<tr>
			<td colspan="3" class="text-left font-13"><strong>Packing & Forwarding Charge</strong></td>			
			<td colspan="4" class="text-right font-13"><strong><?php echo CURR.' '.$db->rp_number_format(round($cart_detail_d['packing_charge'],2)); ?></strong></td>
		</tr>

		<?php
		if($cart_detail_d['igst_amount']!=0 && $cart_detail_d['country']=="India")
		{	
				if($cart_detail_d['customer_type']==7)
				{
					if (strtolower(CLIENT_STATE) == strtolower($Distri_data_a['state'])) 
					{
						?>
						<tr>
							<td colspan="3" class="text-left"><strong>C GST 0.05 %</strong></td>
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==11)
							{
								?>
								<td colspan="4" class="text-right "><strong>XXX</strong> </td>
								<?php
							} 
							else
							{
								?>
								<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($gst_amount/2))?></strong> </td>
							<?php
							}
							?>			
							
						</tr>
						<tr>
							<td colspan="3" class="text-left"><strong>S GST  0.05 %</strong></td>	
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==11)
							{
								?>
								<td colspan="4" class="text-right "><strong>XXX</strong></td>
								<?php
							} 
							else
							{
								?>
								<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($gst_amount/2))?></strong></td>
							<?php
							}
							?>			
							
						</tr>
						<?php
					}
					else
					{
						?>
							<tr>
								<td colspan="3" class="text-left"><strong>IGST 0.1 %</strong></td>	
								<?php
								if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==11)
								{
									?>
									<td colspan="4" class="text-right "><strong>XXX</strong></td>
									<?php
								} 
								else
								{
									?>
									<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($gst_amount))?></strong></td>
								<?php
								}
								?>		
								
							</tr>
							<tr>
							<td colspan="3" class="text-left"></td>			
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
							<td colspan="3" class="text-left"><strong>C GST 9 %</strong></td>
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==11)
							{
								?>
								<td colspan="4" class="text-right "><strong>XXX</strong> </td>
								<?php
							} 
							else
							{
								?>
								<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($gst_amount/2))?></strong> </td>
							<?php
							}
							?>			
							
						</tr>
						<tr>
							<td colspan="3" class="text-left"><strong>S GST 9 %</strong></td>	
							<?php
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==11)
							{
								?>
								<td colspan="4" class="text-right "><strong>XXX</strong></td>
								<?php
							} 
							else
							{
								?>
								<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($gst_amount/2))?></strong></td>
							<?php
							}
							?>			
							
						</tr>
						<?php
					}
					else
					{
						?>
							<tr>
								<td colspan="3" class="text-left"><strong>IGST 18 %</strong></td>	
								<?php
								if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==11)
								{
									?>
									<td colspan="4" class="text-right "><strong>XXX</strong></td>
									<?php
								} 
								else
								{
									?>
									<td colspan="4" class="text-right "><strong><?= (CURR. $db->rp_number_format($gst_amount))?></strong></td>
								<?php
								}
								?>		
								
							</tr>
							<tr>
							<td colspan="3" class="text-left"></td>			
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
					<td colspan="3" class="text-left"><strong></strong></td>	
					<td colspan="4" class="text-right "></td>
				</tr>
				<tr>
					<td colspan="3" class="text-left"></td>			
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
				<td colspan="3">
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
				<td colspan="3"></td>
				<td colspan="4" class="text-right"><strong></td>
				</tr>
			<?php
		}
		?>
		

		<tr>
			<td colspan="3">
				<strong>Round Off</strong>
			</td>
			<td colspan="4" class="text-right"><strong>
			<?=$f1?>
			 </strong></td>
		</tr>
		<!-- <tr style="background-color: #81bfc7;font-size: 16px;"> -->
		<tr style="background-color: <?= VIEW_COLOR ?>;font-size: 16px;">
			<td colspan="3">
				<strong>Grand Total</strong>
			</td>
			<td colspan="4" class="text-right"><strong><?php echo CURR.' '.$db->rp_number_format(round($new_final_price,2)); ?></strong></td>
		</tr>

		<tr>
			<td colspan="9" rowspan="4" style="border-top: none;border-right: none;">
				

				<!-- <span style="font-size:13px;"><b>Regards,</b></span><br>
				<span style="font-size:13px;"><b><?php echo $cart_detail_d['faithfully'] ?></b></span><br> -->
				<!-- <span style="font-size:13px;"><b>9327284882</b></span><br><br><br> -->
			</td>
			<!-- <td colspan="3" class="text-left"><strong>GST (18%)</strong></td>			
			<td colspan="4" class="text-right "><strong><?php echo $db->rp_number_format($final_gst_amount); ?></strong></td> -->	
			<td colspan="7" rowspan="4">
				<br><br><br><br><br>
				<center><strong>For, <?= CLIENT_BRAND_NAME ?>.<br>
				Authorised SIgnatory</strong></center>	
			</td>
		</tr>
		<!-- <tr>
			<td colspan="4" rowspan="4" class="text-left "><strong>Total Amount To Pay</strong></td>			
			<td colspan="4" rowspan="4" class="text-right no-border-left"><strong><?php echo $db->rp_number_format(round($new_final_price)); ?></strong></td>	
		</tr> -->
		
	</tbody>
	<!-- <tr> -->
		
	<!-- </tr> -->
</table>



</body>
</html>