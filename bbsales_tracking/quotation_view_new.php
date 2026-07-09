<?php
$page_id=566;$page_slug='page_order_ajax';
require_once("connect_in.php");
include("../include/no_to_word.php");
include("../include/quotation.class.php");
$ObjQuotation=new Quotation();
$ntw = new NumToWord_RP;
$quotation_id	= $_REQUEST['quotation_id'];
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
$brand_logo1 = SITEURL."images/chanakya_quo.png";
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
.border td{
	border-bottom: hidden !important;
}
.no-border-bottom{
	border-bottom: hidden !important;
}
.srno{
	width: 7% !important;
	min-width: 7%!important;
	max-width: 7%!important;
}
.pname{
	width: 18% !important;
	min-width: 18%!important;
	max-width: 18%!important;
	text-align: left;
}
.model{
	width: 30% !important;
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
	width: 10% !important;
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
.color {
	background: #D3D3D3;
}
tbody
{
	text-transform: uppercase;
}
.font-size td
{
	font-size: 15px!important;
}
.addwatermark{
  background: url(../images/bgwatermark.png) no-repeat;
  background-size: cover;
}
</style>
</head>
<body>
<table>
	<tbody class="<?= $cl; ?>">
		<tr>
			<td colspan="4" style="text-align: left;width: 23%!important"><span style="float: left" ><img width="100" height="100" src="<?= $brand_logo ?>"></span></td>
			<td colspan="8" class="no-border-left no-border-right" style="font-size: 22px; "><strong>CHANAKYA ENGINEERING PRODUCTS</strong></td>
			<td colspan="4" style="text-align: right;"><span style="float: right" ><img width="125" height="150" src="<?= $brand_logo1 ?>"></span></td>
		</tr>
		<tr>
			<td colspan="8">
				<strong><?php echo COMPANY_NAME; ?></strong><br/>
				<strong><?php echo CLIENT_ADDRESS; ?></strong><br/>
				<strong>Mo:-</strong> <?php echo CLIENT_HELP_DESK; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>E-Mail:-</strong> <?php echo CLIENT_EMAIL; ?><br/>
				<strong>GSTIN:</strong> <?php echo CLIENT_GST; ?><br/>
			</td>
			<td colspan="8">
				<strong>To,</strong><br/>
				<?php
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
				?>
				<strong>M/S. <?php echo $cart_detail_d['company_name']; ?></strong><br/><span>
				<?php echo '"'.$cart_detail_d['address']." , ".$cart_detail_d['city'].'"'." , ".$cart_detail_d['state']." , ".$cart_detail_d['country']; ?></span><br/>
				<span>Transport :</span><br/>
				<span>Contact :</span><br/>
				<span>GSTIN :</span><br/>
				<span>Date:- :</span><br/>
			</td>
		</tr>
		<tr>
			<td colspan="16">
				<strong>
					Dear Sir, <br/>
					As per our discussion we are quoting given below price as per your inquiry.<br/>
				</strong>
			</td>
		</tr>
		<tr>
			<th colspan="1" class="text-center srno">Sr.No</th>
			<th colspan="5" class="text-center model">Model--Color-Silver-Carbon Black</th>
			<th colspan="2" class="pname text-center" class="text-center">Discription</th>
			<th colspan="1" class="text-center box_qty">MRP (INR)</th>
			<th colspan="1" class="text-center rate ">Net Rate</th>
			<th colspan="2" class="text-center box_qty">GST (18%)</th>
			<th colspan="2" class="text-center carrtoon_qty">PCS.</th>
			<th colspan="2" class="text-center rate ">Total<br/><span style="font-size:11px">(INR)</span></th>
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
				$GST=$db->rp_getValue("product","igst","id='".$item['pro_id']."' AND isDelete=0");
				// 
				$pro_name=$db->rp_getValue("product","name","id='".$item['pro_id']."' AND isDelete=0");
				$size=$db->rp_getValue("weight","name","id='".$item['weight_id']."' AND isDelete=0");
				$product_code=$db->rp_getValue("product_weight_price","catno","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'",0);
				
				
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
				/*$rate_total=($item['original_price']-($item['original_price']*0.68))/1.18;*/
				$rate_total1 = ($item['original_price']-$discount);
				$rate_total = round(($rate_total1/1.18),2);
				$box_qty_total = $rate_total*0.18;
				$max_total = $item['pro_qty']*$rate_total;
				$totalprice +=$max_total
				/*print_r(SITEURL.PRODUCT.$item['image_path']);
				exit();*/
				?>

				<tr>
					<td colspan="1" class="text-center srno"><?php echo $count; ?></td>

					<td colspan="5" class="model" style="position: relative;"><img style="width: 300px;height: 150px; margin-left: 50px;" src="<?php echo SITEURL.PRODUCT.$item['image_path'] ?>"><strong><?php echo $product_code; ?></strong></td>
					<td colspan="2" class="pname" class="text-center" ><?php if($item['weight_id']!=-1){echo $pro_name." - ".$size." - ".strtoupper($product_code); } else { echo $pro_name." - ".strtoupper($product_code); } ?></td>
					<td colspan="1" class="box_qty" style="text-align: center;"><?php echo $item['original_price']; ?></td>

					<td colspan="1" class="text-center rate"><?php echo round($rate_total, 2); ?></td>
					<!-- <td colspan="1" class="text-center rate"><?php echo $db->rp_number_format($item['unitprice']); ?></td> -->

					<td colspan="2" class="box_qty" style="text-align: center;"><?php echo round($box_qty_total, 2); ?></td>
					<!-- <td colspan="2" class="box_qty" style="text-align: center;"><?php echo $db->rp_number_format($item['original_price']*18/100); ?></td> -->

					<td colspan="2" class="text-center carrtoon_qty "><?php echo $item['pro_qty']; ?></td>
					
					<td colspan="2" class="text-center rate"><?php echo round($max_total, 2); ?></td>
					<!-- <td colspan="2" class="text-center rate"><?php echo $db->rp_number_format($item['totalprice']); ?></td> -->

				</tr>
			<?php
			}
			if($count<10)
			{
				for($i=0;$i<10-$count;$i++)
				{
				?>
				<tr class="border">
					<td colspan="1" class="srno"></td>
					<td colspan="5" class="model" ></td>
					<td colspan="2" class="pname"></td>
					<td colspan="1" class="box_qty"></td>
					<td colspan="1" class="box_qty"></td>
					<td colspan="2" class="carrtoon_qty"></td>
					<td colspan="2" class="rate"></td>
					<td colspan="2" class="rate"></td>
				</tr>
				<?php 
				}
			}
		}
		?>
		<tr>
			<td colspan="1" class="srno"></td>
			<td colspan="5" class="model" ></td>
			<td colspan="2" class="pname"></td>
			<td colspan="1" class="box_qty"></td>
			<td colspan="1" class="box_qty"></td>
			<td colspan="2" class="carrtoon_qty"></td>
			<td colspan="2" class="rate"></td>
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
				$gst_amount=($subtotal*$GST)/100;
				$final_price = $subtotal;
				$new_final_price=$subtotal+$gst_amount;
				$rowspan=4;
				
			}
			else
			{
				$final_price = $totalprice+$cart_detail_d['transport_charge'];
				$gst_amount=($final_price*$GST)/100;
				$new_final_price=$final_price+$gst_amount;
				$final_gst_amount = $gst_amount;
				$rowspan=3;
			}
			?>
			<td colspan="8" class="" rowspan="<?= $rowspan; ?>">	
				<strong> NOTE: This Quatation will be valid 30 Days from the date of Quatation.</strong><br/>
				<span>   You are requested to Deposit the payment in below Bank</span><br/>
				<span>   Our bank details are as below</span><br/><br/>
			</td>
			<td colspan="4" class="text-left"><strong>SubTotal</strong></td>			
			<td colspan="4" class="text-right "><strong><?php echo $db->rp_number_format(round($totalprice,2)); ?></strong></td>
		</tr>
		<tr>
			<td colspan="4" class="text-left"><strong>Transportation</strong></td>			
			<td colspan="4" class="text-right "><strong><?= $cart_detail_d['transport_charge']?></strong></td>
		</tr>
		<tr>
			<td colspan="4" class="text-left"><strong>Grand Total</strong></td>			
			<td colspan="4" class="text-right "><strong><?php echo $db->rp_number_format(round($final_price,3)); ?></strong></td>
		</tr>
		<tr>
			<td colspan="8" rowspan="4">	
				<span style="font-size:13px;"><b>Bank Name </b>: <?php echo COMPANY_BANK ?> </span><br/>
				<span style="font-size:13px;"><b>Account Number</b> : <?php echo COMPANY_BANK_ACC_NO ?></span><br/>
				<span style="font-size:13px;"><b>IFSC Code</b> : <?php echo COMPANY_BANK_IFSC ?> </span><br/>
				<span style="font-size:13px;"><b>Branch </b>: <?php echo COMPANY_BANK_BRANCH ?></span>
			</td>
			<td colspan="4" class="text-left"><strong>GST (18%)</strong></td>			
			<td colspan="4" class="text-right "><strong><?php echo $db->rp_number_format($final_gst_amount); ?></strong></td>	
		</tr>
		<tr>
			<td colspan="4" rowspan="4" class="text-left "><strong>Total Amount To Pay</strong></td>			
			<td colspan="4" rowspan="4" class="text-right no-border-left"><strong><?php echo $db->rp_number_format(round($new_final_price)); ?></strong></td>	
		</tr>
		
	</tbody>
	<!-- <tr> -->
		
	<!-- </tr> -->
</table>

<table>
	<tr>
		<td style="border-style: none;border-color: white;">
			
		<span style="font-size:13px;"><b>Thanking you</b></span><br>
		<span style="font-size:13px;"><b>Your faithfully,</b></span><br>
		<span style="font-size:13px;"><b>Dharmendrasinh Kathia</b></span><br><br><br>

		<span style="font-size:13px;"><b>Terms & Condition :</b></span><br>
		<div class="row">
			<div class="col-md-4">
				<span style="font-size:13px;">(1) Price including GST.</span><br>
				<span style="font-size:13px;">(2) Price ex work factory (Rajkot).</span><br>
				<span style="font-size:13px;">(3) Subject to Rajkot jurisdiction.</span>
			</div>
			<div class="col-md-8">
				<span style="font-size:13px;">(4) Payment 30% at the time of order & balance or 100% before dispatch.</span><br>
				<span style="font-size:13px;">(5) We are not liable for any transport safety.</span><br>
				<span style="font-size:13px;">(6) Price list can be changed any time without prior notice.</span>
			</div>
		</div>	
		</td>
	</tr>
</table>

</body>
</html>