<?php
$page_id=566;$page_slug='page_quotation_ajax';
require_once("connect_in.php");
include("../include/no_to_word.php");
include("../include/quotation.class.php");
$ObjQuotation=new Quotation();
$ntw = new NumToWord_RP;
$quotation_id	= $_REQUEST['quotation_id'];
// $update = $db->rp_update("orders",array("status"=>1),"id='".$order_id."'",0);
$cart_detail_r 	= $db->rp_getData("quotation_detail","*","id='".$quotation_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);
$inquiry_created_by=$db->rp_getValue("no_order_inquiry","inquiry_created_by","id='".$cart_detail_d['inquiry_id']."' AND isDelete=0",0);
$sales_executive=$db->rp_getValue("sales_executive","name","id='".$inquiry_created_by."'");
$order_date=($cart_detail_d['order_date']!="0000-00-00 00:00:00")?date("d-m-Y",strtotime($cart_detail_d['order_date'])):"";

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
.border td{
	border-bottom: hidden !important;
}
.no-border-bottom{
	border-bottom: hidden !important;
}
.srno{
	width: 5% !important;
	min-width: 5%!important;
	max-width: 5%!important;
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
/*.rate{
	width: 12% !important;
	min-width: 12%!important;
	max-width: 12%!important;
}*/
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
	text-transform: uppercase;
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
		<tr class="color" hidden="">
			<?php 
			$GetDistributor_b = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
			$Distri_data_b = mysqli_fetch_assoc($GetDistributor_b);
			if(strtolower($Distri_data_b['state'])!=strtolower("Bihar"))
			{
				?>
				<td colspan="16" class="text-center"><h3><strong><?php echo CLIENT_BRAND_NAME; ?></strong></h3></td>
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
			<td colspan="4" class="text-center "><span style="float: left" ><img width="180" height="100" src="<?= SITEURL."images/chanakya.png"?>"></span></td>
			<td colspan="12" class="no-border-left"><strong>QUOTATION</strong></td>
			<!-- <td colspan="4" class="text-right no-border-left"><strong>Date : <?php echo $order_date; ?></strong></td> -->
		</tr>
		<tr hidden="">
			<td colspan="8" class="text-left"></td>
			<td colspan="8" class="text-right no-border-left"><strong>Date : <?php echo $order_date; ?></strong></td>
		</tr>
		<tr  class="color" hidden="">
			<?php 
			$GetDistributor_d = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
			$Distri_data_d = mysqli_fetch_assoc($GetDistributor_d);
			if(strtolower($Distri_data_d['state'])!=strtolower("Bihar"))
			{
				?>
				<td colspan="10"  class="text-center"><strong>From : <?php echo COMPANY_NAME; ?></strong></td>
				<?php
			}
			else
			{
				?>
				<td colspan="10"  class="text-center"><strong>From : <?php echo BIHAR_COMPANY_NAME; ?></strong></td>
				<?php
			}
			?>
			<td colspan="6" class="text-center no-border-left">
				<strong>To :
				 <?php
					$GetDistributor = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
					$Distri_data = mysqli_fetch_assoc($GetDistributor);
					echo $Distri_data['company_name']." (".$Distri_data['cname'].")";
					?>
			 		
			 </strong></td>
		</tr>
		<tr>
			<td colspan="10" >

				<?php 
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
				?>
					<strong>M/S. <?php echo $cart_detail_d['company_name']; ?></strong><br/><span style="padding-left: 32px;">
					<?php echo '"'.$cart_detail_d['address']." , ".$cart_detail_d['city'].'"'." , ".$cart_detail_d['state']." , ".$cart_detail_d['country']; ?></span><br/>
					<span style="padding-left: 32px;">Email : <?php echo $db->rp_getValue("executive","email","id='".$cart_detail_d['customer_id']."'"); ?></span><br/><br/>
					<strong>Your Ref.  : <?= $cart_detail_d['reference']; ?></strong><br/>
					<strong>Kind Attn. : <?= $cart_detail_d['attn']; ?></strong><br/>
						<span style="padding-left: 82px;"><?= ($cart_detail_d['attn_no']!="")?"Contact No. : ".$cart_detail_d['attn_no']."<br/>":"";?></span>
						<span style="padding-left: 82px;"><?= ($cart_detail_d['attn_email']!="")?"Email ID : ".$cart_detail_d['attn_email']:"";?></span>
			</td>

			<td colspan="6" >
				<?php 
					$GetDistributor = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
					$Distri_data = mysqli_fetch_assoc($GetDistributor);
				?>
				<strong>Quotation No. :</strong> <?php echo $cart_detail_d['quotation_no']; ?><br/>
				<strong>Quotation Date : </strong><?php echo date('d-m-Y',strtotime($cart_detail_d['quotation_date'])); ?><br/><br/>
				<strong>Ref. Date : <?= ($cart_detail_d['reference_date']!="0000-00-00")?date('d-m-Y',strtotime($cart_detail_d['reference_date'])):""; ?></strong><br/><br/>
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
		<tr>
			<td colspan="16">
				<strong>
					Dear Sir, <br/><br/>
					As per our discussion we are quoting given below price as per your inquiry.<br/>
				</strong>
			</td>
		</tr>
		<tr>
			<td colspan="10" class=""><strong>M/S. <?php echo $cart_detail_d['company_name']; ?></strong></td>
			<td colspan="6" class=""><strong>Quotation No. : <?php echo $cart_detail_d['quotation_no']; ?></strong></td>
		</tr>
		<tr>
			<th width="10%" colspan="2" class="text-center srno">Sr.No</th>
			<th width="50%" colspan="6" class="text-center pname">Name</th>
			<th width="10%" colspan="2" class="text-center box_qty">UOM</th>
			<th width="10%" colspan="2" class="text-center carrtoon_qty">Quantity</th>
			<th width="10%" colspan="2" class="text-center ">Price (INR)</th>
			<th width="10%" colspan="2" class="text-center ">Amount<br/><span style="font-size:11px">(INR)</span></th>
		</tr>
		<?php
		// sorting based on product dispaly order & size display order
		$ITEMS=array();
		$items1=$db->rp_getData("quotation_product_item","*","quotation_id='".$quotation_id."'");
		while ($item1=mysqli_fetch_assoc($items1)) 
		{
			$item1['display_order']=$db->rp_getValue("product","display_order","id='".$item1['pro_id']."' AND isDelete=0");
			$item1['weight_display_order']=$db->rp_getValue("weight","display_order","id='".$item1['weight_id']."' AND isDelete=0");
			$ITEMS[]=$item1;
		}
		$tempArr = array();
		foreach ($ITEMS as $key => $val) {
			$tempArr['display_order'][$key] = $val['display_order'];
        	$tempArr['weight_display_order'][$key] = $val['weight_display_order'];
		}

		array_multisort($tempArr['display_order'], SORT_ASC, $tempArr['weight_display_order'], SORT_ASC,$ITEMS);
		$items=$db->rp_getData("order_product_item","*","order_id='".$order_id."'");
		if($items1){
			$count=0;
			$totalprice=0;
			$final_price=0;
			$boxqty=0;
			$cartoonqty=0;
			$totalproqty=0;
			$totalrate=0;
			$totaldiscount=0;
			$GST=0;
			// while ($item=mysqli_fetch_assoc($items)) 
			foreach($ITEMS as $item)
			{
				$display_order=$db->rp_getValue("product","display_order","id='".$item['pro_id']."' AND isDelete=0");
				$tcid=$db->rp_getValue("product","tcid","id='".$item['pro_id']."' AND isDelete=0");
				$GST=$db->rp_getValue("product","igst","id='".$item['pro_id']."' AND isDelete=0");
				$pro_name=$db->rp_getValue("product","name","id='".$item['pro_id']."' AND isDelete=0");
				$size=$db->rp_getValue("weight","name","id='".$item['weight_id']."' AND isDelete=0");
				$product_code=$db->rp_getValue("product_weight_price","catno","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'"); 
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
				<td width="5%" colspan="2" class="text-center srno"><?php echo $count; ?></td>
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
				<td colspan="2"></td>
				<td width="7%"  colspan="2"class="text-right "><?php echo $item['pro_qty']; ?></td>
				<td width="10%" colspan="2"class="text-right rate"><?php echo $db->rp_number_format($item['unitprice']); ?></td>
				<td width="15%" colspan="2" class="text-right rate"><?php echo $db->rp_number_format($item['totalprice']); ?></td>
			</tr>
			<?php
			if($cash_discount!="" && $cash_discount!=0)
			{

			}
			}
			if($count<8)
			{
				for($i=0;$i<8-$count;$i++)
				{
				?>
				<tr class="border">
					<td colspan="2" class="srno"></td>
					<td colspan="6" class="pname"></td>
					<td colspan="2" class="box_qty"></td>
					<td colspan="2" class="rate"></td>
					<td colspan="2" class="rate"></td>
					<td colspan="2" class="rate"></td>
				</tr>
				<?php 
				}
			}
			?>
			<tr>
				<td colspan="2" class="srno"></td>
				<td colspan="6" class="pname"></td>
				<td colspan="2" class="box_qty"></td>
				<td colspan="2" class="rate"></td>
				<td colspan="2" class="rate"></td>
				<td colspan="2" class="rate"></td>
				
			</tr>
			<?php
		}
		?>
		<tr class="font-size" hidden="">
			<td colspan="2"></td>
			<td colspan="6" class="pname text-right"><strong>Total</strong></td>
			<td class="text-right"><strong><?= $boxqty;?></strong></td>
			<td class="text-right"><strong><?= $cartoonqty;?></strong></td>
			<td class="text-right"><strong><?= $totalproqty;?></strong></td>
			<td class="rate"></td>
			<td class="rate"></td>
			<td class="rate"></td>
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
				$final_price=$subtotal+$gst_amount;
				$rowspan=5;
				
			}
			else
			{
				$gst_amount=($totalprice*$GST)/100;
				$final_price=$totalprice+$gst_amount;
				$final_gst_amount = $gst_amount;
				$rowspan=4;
			}
			?>
			<td colspan="10" class="" rowspan="<?= $rowspan; ?>"></td>
			<td colspan="3" class="text-left"><strong>Basic Amount</strong></td>			
			<td colspan="4" class="text-right "><strong><?php echo $db->rp_number_format($totalprice); ?></strong></td>
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
			$gst_amount=($subtotal*$GST)/100;
			$final_price=$subtotal+$gst_amount;
			?>
			<tr class="font-size">
				<!-- <td colspan="11" class="text-center " ></td> -->
				<td colspan="3" class="text-left"><strong>Cash Discount <?php echo $cart_detail_d['cash_discount']."%"; ?></strong></td>
				<td colspan="2" class="text-right "><strong><?php echo $db->rp_number_format($subtotal); ?></strong></td>
			</tr>
			<tr class="font-size">
				<!-- <td colspan="11" class="text-center " ></td> -->
				<td colspan="3" class="text-left"><strong>GST <?php echo $GST."%"; ?></strong></td>
				<td colspan="3" class="text-right "><strong><?php echo $db->rp_number_format($final_price); ?></strong></td>
			</tr>
			<tr class="font-size">
				<!-- <td colspan="11" class="text-center " ></td> -->
				<td colspan="3" class="text-rleft "><strong>Round Off</strong></td>
				<td colspan="3" class="text-right "><strong><?php 
				$whole = floor($final_price);      // 1
		        $fraction = $final_price - $whole;

		        echo $f1=  $db->rp_number_format((float)$fraction, 2, '.', '');
				// echo $db->round($final_price,0);
				 ?></strong></td>
			</tr>
			<tr class="font-size">
				<!-- <td colspan="11" class="text-center " ></td> -->
				<td colspan="2" class="text-left "><strong>SubTotal</strong></td>
				<td colspan="3" class="text-right "><strong><?php echo $db->rp_number_format(round($final_price)); ?></strong></td>
			</tr>

			<?php
		}
		else
		{
			$gst_amount=($totalprice*$GST)/100;
			$final_price=$totalprice+$gst_amount;
			$final_gst_amount = $gst_amount;
			?>
			<tr class="font-size">
				<!-- <td colspan="11" class="text-center" ></td> -->
				<td colspan="3" class="text-left "><strong>GST <?php echo $GST."%" ." (".$db->rp_number_format($final_gst_amount).")"; ?></strong></td>
				<td colspan="3" class="text-right "><strong><?php echo $db->rp_number_format($final_price); ?></strong></td>
			</tr>
			<tr class="font-size">
				<!-- <td colspan="11" class="text-center " ></td> -->
				<td colspan="3" class="text-left "><strong>Round Off</strong></td>
				<td colspan="3" class="text-right "><strong><?php 
				$whole = floor($final_price);      // 1
		        $fraction = $final_price - $whole;
		        echo $f1=  $db->rp_number_format((float)$fraction, 2, '.', '');
		        ?></strong></td>
			</tr>
			<tr class="font-size">
				<td colspan="3" class="text-left "><strong>Grand Total</strong></td>
				<td colspan="3" class="text-right "><strong><?php echo $db->rp_number_format(round($final_price)); ?></strong></td>
			</tr>
			<tr>
				<td colspan="16" class="" rowspan="">
					<strong>Amount in Words : INR <?php echo $ntw->rp_convertNumToWord($final_price); ?> </strong><br/><br/>
					<strong>Terms & Conditions : - </strong><br/><br/>
					<strong>Transportation : </strong>Extra at actual from our factory.<br/><br/>
					<strong>Installation & Commissioning : </strong> Extra at actual<br/><br/>
					<strong>GST : </strong>GST Extra<br/><br/>
					<strong>Payment : </strong> 
						<span>100% Advance Payment, Balance against Performa Invoice prior to dispatch.</span><br/>
						<span style="margin-left: 70px;">All payments are accepted either by Rajkot Cheque / by Demand Draft Payable at Rajkot.</span><br/>
						<span style="margin-left: 70px;">Any Bank charges arising out of this transaction will be borne by the client</span><br/><br/>
					<strong>Warranty : </strong>
						<span>Subject to the Terms of Payment being punctually complied with and the equipment being operated Properly.</span><br/>
						<span style="margin-left: 83px;">Raj Cooling Systems Pvt.Ltd. warranty the equipment for the period of 12 months </span><br/><span style="margin-left: 83px;"> from the date of dispatch at any site of INDIA</span><br/><br/>
					<strong>Cancellation of Order : </strong>
						<span>Once the Order is placed, cannot be cancelled for any reason.</span><br/><span style="margin-left: 180px"> In case of Order being cancelled then the entire amount of advance will be forfeited</span><br/><br/>
					<strong>Juridiction : </strong>Shall be held & concluded in Rajkot Jurisdiction<br/><br/>
					<strong>Property Right : </strong>We reserve the proprietary rights to the goods until we receive full payment in connection with this order<br/><br/>
					<strong>Validity : </strong>This Offer Valid For 2 Months Only.<br/><br/>
					<strong>Bank Details : </strong>
						<span>HDFC Bank Ltd.</span><br/>
						<span style="margin-left: 100px;">Branch : Sharda Baugh,Rajkot</span><br/>
						<span style="margin-left: 100px;">IFSC Code : HDFC0001253</span><br/>
						<span style="margin-left: 100px;">Acc No.: 50200035594569</span>
				</td>
			</tr>
			<tr>
				<td colspan="10" class="" rowspan="">	
					<span style="font-size:15px;"><b>GSTIN NO:. </b>24AAICR3608N1Z4</span><br/>
					<span style="font-size:15px;"><b>PAN No. </b>AAICR3608N</span><br/>
				</td>
				<td colspan="7">
					<?php 
					$GetDistributor_b = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
					$Distri_data_b = mysqli_fetch_assoc($GetDistributor_b);
					if(strtolower($Distri_data_b['state'])!=strtolower("Bihar"))
					{
						?>
						<h5 style="text-align: center;">For, <?php echo CLIENT_BRAND_NAME; ?></h5>
						<?php
					}
					else
					{
						?>
						<h5 style="text-align: center;">For, <?php echo BIHAR_CLIENT_BRAND_NAME; ?></h5>
						<?php
					}
					?>
					<div style="border: 1px solid #fff;height: 90px"></div>
					<h5 align="center" style="text-transform: capitalize;">(Authorized signatory)</h5>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>
</body>
</html>