<?php
$page_id=566;$page_slug='page_order_ajax';
require_once("connect_in.php");
include("../include/no_to_word.php");
include("../include/product.class.php");
$ProductObj=new Product();
$ntw = new NumToWord_RP;
$order_id	= $_REQUEST['order_id'];
// $update = $db->rp_update("orders",array("status"=>1),"id='".$order_id."'",0);
$cart_detail_r 	= $db->rp_getData("orders","*","id='".$order_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);

// $cash_discount=$db->rp_getValue("customer","cash_discount","id='".$cart_detail_d['customer_id']."' AND isDelete=0",0);
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
		<tr class="color">
			<?php 
			$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
			$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
			if(strtolower($Distri_data_a['state'])!=strtolower("Bihar"))
			{
				?>
				<tr>
					<td colspan="4" class="no-border-right" style="text-align: left;"><span style="float: left"><img width="200"  src="<?= SITEURL."images/cmk_logo.png"?>"></span></td>

					<td colspan="12" class="text-left"><h2><strong><?php echo CLIENT_BRAND_NAME; ?></strong>
						<p style="font-size: 12px;"><strong><?=CLIENT_ADDRESS?></strong></p>
						<p style="font-size: 12px;"><strong>Mobile :<?=CLIENT_HELP_DESK?>,8980407311 , Email : <?=CLIENT_EMAIL?></strong></p>
						<p style="font-size: 12px;"><strong>Website :<?=CLIENT_WEBSITE?>  GST NO. <?=CLIENT_GST?></strong></p>

					</h3></td>

					<!-- <td colspan="4" class="no-border-left" style="text-align: right;"><span style="float: right"><img width="100" height="100" src="<?= SITEURL."images/cmk_logo.png"?>"></span></td> -->
				</tr>
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
		<!--tr>
			<td colspan="16" class="text-center"><h4><?php echo CLIENT_ADDRESS; ?></h4></td>
		</tr-->
		<tr>
			<td colspan="5" class="text-left"><strong class="text-center">Debit Memo</strong></td>
			<td colspan="6" class="text-left no-border-left no-border-right"><strong class="text-center">Tax Invoice</strong></td>
			<td colspan="5" class="text-right"><strong class="text-center">ORIGINAL FOR RECIPIENT</strong></td>
			<!-- <td colspan="4" class="text-right no-border-left"><strong>Date : <?php echo $order_date; ?></strong></td> -->
		</tr>
		<!-- <tr>
			<td colspan="8" class="text-left"></td>
			<td colspan="8" class="text-right no-border-left"><strong>Date : <?php echo $order_date; ?></strong></td>
		</tr> -->
		<!-- <tr  class="color">

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
		</tr> -->


		<tr>
			<?php 
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
				?>
			<td colspan="7">
				<strong>Name & Address of the Consignee</strong><br>
				<strong><?php echo $Distri_data_a['company_name']; ?></strong><br/>
			</td>
			<td colspan="4">
				<strong >Invoice No :</strong><br/>
				<strong >Challan No :</strong><br/>
			</td>
			<td colspan="5">
				<strong >Date :</strong><br/>
				<strong >Date :</strong><br/>
			</td>
		</tr>
		<tr>
			<td colspan="7">
			<strong><?php echo $Distri_data_a['address']; ?></strong><br/>
					<strong>Mo :</strong><?=$Distri_data_a['phone']?> <br/>
					<strong style="font-size: 15px;">GST no. : <?php echo CLIENT_GST; ?></strong> 
				
			</td>
			<td colspan="9">
				<strong >Po.No :</strong><br/>
				<strong>Po.Date</strong></br>
				<strong>Transport :</strong></br>
				<strong>E-Way Bill No :</strong></br>
				<strong>Area Sales Manager :</strong></br>
				<strong>Vendor Code :</strong></br>
				<strong>Payment Due Date :</strong></br>
				<strong>Closing Balance As On Date</strong>

			</td>
		</tr>
		<!-- <tr>
			<td colspan="7">
				<?php 
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
				?>
				
					<strong>Name & Address of the Consignee</strong>
					<strong><?php echo COMPANY_NAME; ?></strong><br/>
					<strong><?php echo CLIENT_ADDRESS; ?></strong><br/>
					<strong>Mo :</strong> <br/>
					<strong style="font-size: 15px;">GST no. : <?php echo CLIENT_GST; ?></strong> -->
					<!-- <strong>City: </strong><?php echo CLIENT_CITY; ?><br/>
					<strong>State :</strong> <?php echo CLIENT_STATE; ?><br/> -->
					
					
					
					
				
			<!-- </td>

			<td colspan="9">
				<?php 
					$GetDistributor = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
					$Distri_data = mysqli_fetch_assoc($GetDistributor);
					?> -->

				<!-- <strong>M/s. : <?php //echo $Distri_data['company_name']; ?></strong><br/> -->
				
				<!-- <strong >Invoice No :</strong><br/>
				<strong >Challan No :</strong><br/>
				<strong >Po.No :</strong><br/>
				<strong>Po.Date</strong></br>
				<strong>Transport :</strong></br>
				<strong>E-Way Bill No :</strong></br>
				<strong>Area Sales Manager :</strong></br>
				<strong>Vendor Code :</strong></br>
				<strong>Payment Due Date :</strong></br>
				<strong>Closing Balance As On Date</strong> -->



				<!-- <strong>GST No. :</strong> <?php echo $Distri_data['gst']; ?><br/>
				<strong>City: </strong><?php echo $Distri_data['city']; ?><br/>
				<strong>Place of Supply :</strong> <?php echo $Distri_data['state']; ?><br/> -->
				
				<!-- <?php 
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
		</tr> -->
		<!-- <tr>
			<td colspan="16" class="color"><strong>Order No. :  <?php echo $cart_detail_d['order_no']; ?></strong></td>
		</tr> -->
		<tr>
			<th width="3%" colspan="1" class="text-center srno">Sr.</th>

			<th width="50%" colspan="6" class="text-center pname">Description of Goods </th>
			<th width="3%" colspan="1" class="text-center srno">HSN/SAC</th>
			<th width="10%" colspan="3" class="text-center ">Total Qty</th>
			<th width="10%" class="text-center ">Unit</th>
			<th width="10%"  class="text-center rate">Rate<br/><span style="font-size:11px">(INR)</span></th>
			<th width="10%" class="text-center rate" >GST%<br/><span style="font-size:11px">(%)</span></th>
			
			<th width="15%" colspan="2" class="text-center rate">Total<br/><span style="font-size:11px">(INR)</span></th>
			
		</tr>
		<?php
		// sorting based on product dispaly order & size display order
		$ITEMS=array();
		$items1=$db->rp_getData("order_product_item","*","order_id='".$order_id."'");
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
				<td width="5%" colspan="1" class="text-center srno"><?php echo $count; ?></td>
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
				<td colspan="1"></td>
				<td width="7%"  colspan="3" class="text-right "><?php echo $item['pro_qty']; ?></td>
				<td width="7%" class="text-right rate"><!-- <?php echo $db->rp_number_format($item['original_price']); ?> --></td>
				<td width="10%" class="text-right rate"><?php echo $db->rp_number_format($item['unitprice']); ?></td>
				<td width="11%" class="text-right "><!-- <?php echo $db->rp_number_format($item['discount']); ?> --></td>
				
				<td width="15%" colspan="2" class="text-right rate"><?php echo $db->rp_number_format($item['totalprice']); ?></td>
			</tr>
			<?php
			if($cash_discount!="" && $cash_discount!=0)
			{

			}
			}
			if($count<16)
			{
				for($i=0;$i<25-$count;$i++)
				{
				?>
				<tr class="border">
					<td colspan="1" class="srno"></td>
					<td colspan="6" class="pname"></td>
					<td colspan="1"></td>
					<!-- <td class="box_qty"></td> -->
					<!-- <td class=""></td> -->
					<!-- <td  class="rate"></td> -->
					<td  colspan="3" class="rate"></td>
					<td  class="rate"></td>
					<td colspan="" class="rate"></td>
					<td colspan="" class="rate"></td>
					<td colspan="2" class="rate"></td>
				</tr>
				<?php 
				}
			}
			?>
			<tr>
				<td colspan="1" class="srno"></td>
				<td colspan="6" class="pname"></td>
				<td colspan="1"></td>
				<!-- <td  class="box_qty"></td> -->
				<!-- <td class=""></td> -->
				<!-- <td class="rate"></td> -->
				<td colspan="3" class="rate"></td>
				<td class="rate"></td>
				<td colspan="" class="rate"></td>
				<td colspan="" class="rate"></td>
				<td colspan="2" class="rate"></td>
				
			</tr>
			<?php
		}
		?>
		<tr class="font-size">
			<td colspan="7"><strong>GSTIN No. <?=CLIENT_GST?></strong></td>
			<td colspan="1" class="pname text-right"></td>
			<!-- <td class="text-right"><strong><?= $boxqty;?></strong></td> -->
			<!-- <td class="text-right"><strong><?= $cartoonqty;?></strong></td> -->
			<td colspan="3" class="text-right"><strong><?= $totalproqty;?></strong></td>
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
			<td colspan="8" class="" rowspan="2">	
				<!-- <h4><b><u>Bank Details</u></b></h4> -->			
				<?php 
				$GetDistributor_b = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
				$Distri_data_b = mysqli_fetch_assoc($GetDistributor_b);
				if(strtolower($Distri_data_b['state'])!=strtolower("Bihar"))
				{
					?>
					<span style="font-size:13px;"><b>Bank Name </b>: <?php echo COMPANY_BANK ?> </span></br>
					<span style="font-size:13px;"><b>Bank A/c. No.</b> : <?php echo COMPANY_BANK_ACC_NO ?>  </span><br/>

					<span style="font-size:13px;"><b>RTGS/IFSC Code</b> : <?php echo COMPANY_BANK_IFSC ?> </span></br>
					<span style="font-size:13px;"><b>Swift Code </b>: </span>
					<?php
				}
				else
				{
					?>
					<span style="font-size:13px;"><b>Bank Name </b>: <?php echo BIHAR_COMPANY_BANK ?> </span>
					<span style="font-size:13px;margin-left: 21px"><b>Account Number</b> : <?php echo BIHAR_COMPANY_BANK_ACC_NO ?>  </span><br/>

					<span style="font-size:13px;"><b>IFSC Code</b> : <?php echo BIHAR_COMPANY_BANK_IFSC ?> </span>
					<span style="font-size:13px;margin-left: 50px;"><b>Branch </b>: <?php echo BIHAR_COMPANY_BANK_BRANCH ?></span>
					<?php
				}
				?>
				
				
				
			</td>
			
			<td colspan="4" class="text-left"><strong>P & F Charges</strong></td>			
			<td colspan="4" class="text-right "><strong><!-- <?php echo $db->rp_number_format($totalprice); ?> --></strong></td>
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
			<td colspan="4" class="text-left"><strong><!-- GST <?php echo $GST."%"; ?> -->Assessable Value</strong></td>
			<td colspan="4" class="text-right "><strong><!-- <?php echo $db->rp_number_format($final_price); ?> --></strong></td>
		</tr>
		<tr>
			<td colspan="11">test</td>


		</tr>
		<tr>
			<td colspan="11"></td>
		</tr>
		<tr class="font-size">
			<!-- <td colspan="11" class="text-center " ></td> -->
			<td colspan="4" class="text-rleft "><strong>IGST</strong></td>
			<td colspan="4" class="text-right "><strong><?php 
			// $whole = floor($final_price);      // 1
	  //       $fraction = $final_price - $whole;

	  //       echo $f1=  $db->rp_number_format((float)$fraction, 2, '.', '');
			// echo $db->round($final_price,0);
			 ?></strong></td>
		</tr>
		<tr class="font-size">
			<!-- <td colspan="11" class="text-center " ></td> -->
			<td colspan="2" class="text-left "><strong>SubTotal</strong></td>
			<td colspan="2" class="text-right "><strong><?php echo $db->rp_number_format(round($final_price)); ?></strong></td>
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
				<td colspan="4" class="text-left "><strong>Assessable Value <!-- <?php echo $GST."%" ." (".$db->rp_number_format($final_gst_amount).")"; ?> --></strong></td>
				<td colspan="4" class="text-right "><strong><!-- <?php echo $db->rp_number_format($final_price); ?> --></strong></td>
			</tr>
			<!-- <tr>
				
			</tr>
			<tr>
				<td colspan="10"></td>
			</tr> -->
			<tr class="font-size">
			<!-- <td colspan="11" class="text-center " ></td> -->
			<td colspan="8"><p style="font-size: 13px"><b>GST Amount : </b><?php echo $ntw->rp_convertNumToWord($gst_amount); ?></p></td>
			<td colspan="4" class="text-left "><strong>IGST</strong></br><br><br><br></td>
			<td colspan="4" class="text-right "><strong><?php 
			// $whole = floor($final_price);      // 1
	  //       $fraction = $final_price - $whole;

	  //       echo $f1=  $db->rp_number_format((float)$fraction, 2, '.', '');
	        // echo 1-$f1;
			// echo $db->round($final_price,2);
			 ?></strong></td>
		</tr>
			<tr class="font-size">
				<!-- <td colspan="11" class="text-center " ></td> -->
				<td colspan="8"><p style="font-size: 13px"><b>Bill Amount (Words)  : </b><?php echo $ntw->rp_convertNumToWord($totalprice); ?></p></td>
				<td colspan="4" class="text-left "><strong>Grand Total</strong></td>
				<td colspan="4" class="text-right "><strong><?php echo $db->rp_number_format(round($final_price)); ?></strong></td>
			</tr>
			<tr>
				
			
			<td colspan="8" class="" rowspan="">	
				
					<h4><b><u>Terms & Condition</u></b></h4>
					<span style="font-size:13px;"><strong>P&F : EXTRA</strong></span><br/>
					<span style="font-size:13px;"><strong>FREIGHT & INSURANCE: TO PAY</strong></span><br/>
					<span style="font-size:13px;"><strong>WARRANTY : 1 YEAR AGAINST MANUFACURING DEFECT</strong></span><br/>
					<span style="font-size:13px;"><strong>PAYMENT 70% ADVANCE AND 30% AFTER DELIVERY 15 DAYS</strong></span><br/>
					<span style="font-size:13px;"><strong>DELIVERY : 1 WEEK</strong></span><br/><br/>
					<span style="font-size:13px;"><strong>VALIDITY : 30 DAYS PRICE : BASIS EX-WORKS(LOTHDA)</strong></span><br/><br/>



					<!-- <?php 
					$GetDistributor_e = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
					$Distri_data_e = mysqli_fetch_assoc($GetDistributor_e);
					if(strtolower($Distri_data_e['state'])!=strtolower("Bihar"))
					{
						?>	
						<span style="font-size:15px;"><b>GSTIN NO:. </b><?php echo CLIENT_GST; ?></span><br/>
						<?php
					}
					else
					{
						?>
						<span style="font-size:15px;"><b>GSTIN NO:. </b><?php echo BIHAR_CLIENT_GST; ?></span><br/>
						<?php
					}
					?> -->
			</td>

			<td colspan="8">
				<div class="col-md-12">
					<p style="font-size: 15px;"><strong>FOR, CMK ELECTRO POWER PVT.LTD.</strong></p>
					
					<br/><br/><br/><br/></br></br>
					<h5 align="center" style="text-transform: capitalize;">(Authorized signatory)</h5>
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