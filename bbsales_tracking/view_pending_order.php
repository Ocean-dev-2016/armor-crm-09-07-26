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
$max_discount = $db->rp_getValue("order_product_item","MAX(`discount`)","order_id='".$quotation_id."'",0);
if($max_discount=="0")
{
	$colSpan1= 4;
}
else
{
	$colSpan1= 3;
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
			width: 26% !important;
			min-width: 28%!important;
			max-width: 30%!important;
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
			/*width: 5% !important;*/
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
			/*border-right-color:#8cdee8;*/
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
					<td colspan="16" class="header-img">
						<img style="width: 100%;padding: 0px !important;"  src="<?= VIEW_LOGO_All ?>">
					</td>
				</tr>
				<tr>
					<td colspan="16" class="no-border-bottom no-border-top height-5 text-center"><strong>GST - <?= COMPANY_GST ?></strong></td>
				</tr>
				<tr>
					<td colspan="5" class="no-border-right no-border-bottom bg-gray"></td>
					<td colspan="5" class="text-center no-border-right no-border-bottom" style="background-color: <?= VIEW_COLOR ?>"><b>Pending Dispatch</b></td>
					<td colspan="6" class="no-border-bottom bg-gray"></td>
				</tr>
				<tr>
					<td colspan="16" class="no-border-bottom no-border-top height-5 "></td>
				</tr>
				<tr class="vertical-top">
					<td colspan="5" class="no-border-bottom no-border-top border-r-width border-blue">
						<strong>To,<br/></strong>
						<?php
						$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0","",0);
						$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
						?>
						<strong><?php echo $cart_detail_d['company_name']; ?></strong><br/><span>
						<?php echo wordwrap($Distri_data_a['address'],40,"<br>\n")."  <br/>".$Distri_data_a['city']." - ".$Distri_data_a['zip']." , ".$Distri_data_a['state']." , ".$Distri_data_a['country'] ?></span><br/><br>
						<span><b>Contact Person :</b> <?=$Distri_data_a['cname']?></span><br>
						<span><b>Contact Number :</b> <?=$Distri_data_a['phone']?></span><br>
						<span><b>Email :</b> <?=$Distri_data_a['email']?></span><br/>
						<span><b>State of Supply :</b> <?=$Distri_data_a['state']?></span><br/>
						<strong><span>GST No : <?=$Distri_data_a['gst']?></span></strong><br/>
						<span><b>Pan No :</b> <?=$Distri_data_a['pan']?><br/>
						<span><b>Vendor Code :</b> <?= $cart_detail_d['vendor_code'] ?></span><br/>
					</td>

					<td colspan="5" class="no-border-bottom no-border-top border-r-width border-blue">
						<strong>Address<br/></strong>
						<strong>Shipping Address</strong><br>
						<?=  wordwrap($cart_detail_d['shipping_address'],40,"<br>\n"); ?> <br><br>
						<strong>Billing Address</strong><br>
						<?= wordwrap($cart_detail_d['billing_address'],40,"<br>\n"); ?> <br><br>
						<strong>Transport By :</strong>
						<?= $db->rp_getValue("transport_by","name","isDelete=0 AND id='".$cart_detail_d['transport_through']."' ") ?><br>
						<strong>Transporter Detail :</strong>
						<?= $db->rp_getValue("transport_master","name","isDelete=0 AND id='".$cart_detail_d['transport_name']."' ") ?><br>
					</td>


					<?php
						$inquiry_date = $db->rp_getValue("no_order_inquiry","inquiry_date","isDelete=0 AND id='".$cart_detail_d['inquiry_id']."' ");
					?>
					<td colspan="6" class="no-border-bottom no-border-top">
						<strong>Details<br/></strong>
						<table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 100% !important;">
							<tr>
								<td class="no-border-right no-border-bottom no-border-left no-border-top"><b>Sales Order No :</b> </td>
								<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $cart_detail_d['order_no'] ?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left"><b>Sales Order Date :</b> </td>
								<td class="no-border-right no-border-bottom no-border-left"><?= date('d-m-Y', strtotime($cart_detail_d['order_date'])); ?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left"><b>Quotation  No :</b> </td>
								<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("quotation_detail","quotation_no","id='".$cart_detail_d['quotation_id']."'")?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left"><b>Quotation Date :</b> </td>
								<td class="no-border-right no-border-bottom no-border-left"><?= date('d-m-Y',strtotime($db->rp_getValue("quotation_detail","quotation_date","id='".$cart_detail_d['quotation_id']."'")))?></<td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left"><b>Tendor Code :</b> </td>
								<td class="no-border-right no-border-bottom no-border-left"><?= $cart_detail_d['tendor_code'] ?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left"><b>Challan No :</b> </td>
								<td class="no-border-right no-border-bottom no-border-left"><?= $cart_detail_d['chalan_no'] ?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left"><b>PO No : </b></td>
								<td class="no-border-right no-border-bottom no-border-left"><?= $cart_detail_d['po_no'] ?></td>
							</tr>
							<tr>
								<td class="no-border-right no-border-bottom no-border-left"><b>PO Date :</b> </td>
								<td class="no-border-right no-border-bottom no-border-left"><?=date('d-m-Y',strtotime($cart_detail_d['po_date'])); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<table>
			<tbody class="">
				<tr class="text-center" style="background-color: <?= VIEW_COLOR ?>;">
					<th colspan="1" class="srno text-center" >SR</th>
					<th colspan="1" class="image-width text-center">Image</th>
					<th colspan="5" class="pname model text-center">Description of Goods</th>
					<th class="pname text-center">HSN/SAC</th>
					<th colspan="1" class="text-center pname">Quantity</th>
					<th colspan="1" class="text-center pname">Remaining Quantity</th>
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
						$rate=$item['pro_qty']*$item['unitprice'];
						$boxqty+=$item['box_qty'];
						$cartoonqty+=$item['cartoon_qty'];
						$totalproqty+=$item['pro_qty'];
						$totalrate+=$item['unitprice'];
						$totaldiscount+=$item['discount'];
						$discount = $item['discount_amount'];
						$rate_total1 = ($item['original_price']-$discount);
						$rate_total = round(($rate_total1),2);
						$box_qty_total = $rate_total*0.18;
						$max_total = $item['pro_qty']*$rate_total;
						$totalprice +=$max_total;
						$totaltaxable+=$item['taxable'];
						$totalgstamount+=$item['igst_amount'];
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
							<td colspan="5" class="model" style="position: relative;">
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
								<?=(isset($item['pro_description']) && $item['pro_description']!="")?"<br/><br/>".$item['pro_description']:""?>
							</td>
							<td class="text-center"><?php echo $hsncode; ?></td>
							<td colspan="1" class="text-center carrtoon_qty"><?php echo $item['pro_qty']." ".$unit_name; ?></td>
							<td colspan="1" class="text-center carrtoon_qty"><?php echo $item['remaining_qty']." ".$unit_name; ?></td>
						</tr>
						<?php
					}
				}

				if($count<5)
				{
					for($i=0;$i<12-$count;$i++)
					{
						?>
						<tr class="border">
							<td colspan="1" class="srno"></td>
							<td colspan="1" class="model" ></td>
							<td colspan="5" class="pname"></td>
							<td></td>
							<td colspan="1" class="carrtoon_qty"></td>
							<td colspan="1" class="box_qty"></td>
						</tr>
						<?php
					}
				}
				?>
				<tr>
					<td colspan="1" class="srno"></td>
					<td colspan="1" class="model" ></td>
					<td colspan="5" class="pname"></td>
					<td></td>
					<td colspan="1" class="carrtoon_qty"></td>
					<td colspan="1" class="box_qty"></td>
				</tr>
			</tbody>
		</table>
		<table>
			<tbody>
				<tr>
					<td colspan="5" rowspan="4" class="no-border-right text-left">
						<br><br><br><br>
						<strong style="margin-right: 36px;">Prepared By</strong>
					</td>
					<td colspan="5" rowspan="4" class="no-border-right">
						<br><br><br><br>
						<center><strong>Checked By</strong></center>
					</td>
					<td colspan="6" rowspan="4" class="text-right">
						<strong style="margin-right: 25px;">For, <?= CLIENT_BRAND_NAME ?><br></strong>
						<br><br><br>
						<strong style="margin-right: 25px;">
						Authorised SIgnatory</strong>
					</td>
				</tr>
			</tbody>
		</table>
	</body>
</html>