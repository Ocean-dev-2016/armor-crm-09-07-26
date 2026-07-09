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
		
			<?php 
			$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
			$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
			?>
				<tr>
					<td colspan="4" class="no-border-right" style="text-align: left;"><span style="float: left"><img width="150"  src="<?= SITEURL."images/cmk_logo.png"?>"></span></td>

					<td colspan="12" class="text-left"><h2><strong><?php echo CLIENT_BRAND_NAME; ?></strong>
						<p style="font-size: 12px;"><strong><?php echo CLIENT_ADDRESS;  ?></strong></p>
						<p style="font-size: 12px;"><strong>Mobile :<?=CLIENT_HELP_DESK?>,8980407311 , Email : <?=CLIENT_EMAIL?></strong></p>
						<p style="font-size: 12px;"><strong>Website :<?=CLIENT_WEBSITE?>  GST NO. <?=CLIENT_GST?></strong></p>

					</h3></td>

					
				</tr>
				
		
		
		<tr>
			<td colspan="5" class="text-left" style="border-right: none;"><strong class="text-center">Debit Memo</strong></td>
			<td colspan="6" align="left" style="border-left:none; border-right: none;"><strong >Tax Invoice</strong></td>
			<td colspan="5" class="text-right" style="border-left: none;"><strong class="text-center">ORIGINAL FOR RECIPIENT</strong></td>
			
		</tr>
		


		<tr>
			<?php 
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
				?>
			<td colspan="11">
				<strong>Name & Address of the Consignee</strong><br>
				<strong><?php echo $Distri_data_a['company_name'];?></strong><br/>
			</td>
			<td colspan="2">
				<strong >Invoice No :</strong><br/>
				<strong >Challan No :</strong><br/>
			</td>
			<td colspan="3">
				<strong >Date :</strong><br/>
				<strong >Date :</strong><br/>
			</td>
		</tr>
		<tr>
			<td colspan="11">
			<strong><?php echo $Distri_data_a['address']; ?></strong><br/>
					<strong>Mo :</strong><?=$Distri_data_a['phone']?> <br/>
					<strong style="font-size: 15px;">GST no. : <?php echo CLIENT_GST; ?></strong> 
				
			</td>

			<td colspan="5">
				<p><strong >Po.No :</strong></p>
				<p><strong>Po.Date</strong></p>
				<p><strong>Transport :</strong></p>
				<p><strong>E-Way Bill No :</strong></p>
				<p><strong>Area Sales Manager :</strong></p>
				<p><strong>Vendor Code :</strong></p>
				<p><strong>Payment Due Date :</strong></p>
				<p><strong>Closing Balance As On Date</strong></p>

			</td>
		</tr>
		
		<tr>
			<th width="3%" colspan="1" class="text-center srno">Sr.</th>

			<th colspan="5" class="text-center pname">Description of Goods </th>
			<th width="3%" colspan="2" class="text-center srno">HSN/SAC</th>
			<th width="10%" colspan="3" class="text-center ">Total Qty</th>
			<th width="10%" class="text-center" colspan="1">Unit</th>
			<th width="10%"  class="text-center rate" colspan="1">Rate<br/><span style="font-size:11px">(INR)</span></th>
			<th width="10%" class="text-center rate" colspan="1">GST%<br/><span style="font-size:11px">(%)</span></th>
			
			<th width="15%" colspan="2" class="text-center rate">Total<br/><span style="font-size:11px">(INR)</span></th>
			
		</tr>
		<?php
		
		$ITEMS=array();
		$items1=$db->rp_getData("order_product_item","*","order_id='".$order_id."'");
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
			
			foreach($ITEMS as $item)
			{
				$display_order=$db->rp_getValue("product","display_order","id='".$item['pro_id']."' AND isDelete=0");
				$tcid=$db->rp_getValue("product","tcid","id='".$item['pro_id']."' AND isDelete=0");
				$GST=$db->rp_getValue("product","igst","id='".$item['pro_id']."' AND isDelete=0");
				$pro_name=$db->rp_getValue("product","name","id='".$item['pro_id']."' AND isDelete=0");
				$size=$db->rp_getValue("weight","name","id='".$item['weight_id']."' AND isDelete=0");
				$product_code=$db->rp_getValue("product_weight_price","catno","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'"); 
				
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
				<td width="50%" colspan="5" class="pname">
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
				<td width="7%"  colspan="3" class="text-right "><?php echo $item['pro_qty']; ?></td>
				<td width="7%" class="text-right rate"></td>
				<td width="10%" class="text-right rate"><?php echo $db->rp_number_format($item['unitprice']); ?></td>
				<td width="11%" class="text-right "></td>
				
				<td width="15%" colspan="2" class="text-right rate"><?php echo $db->rp_number_format($item['totalprice']); ?></td>
			</tr>
			<?php
			if($cash_discount!="" && $cash_discount!=0)
			{

			}
			}
			if($count<10)
			{
				for($i=0;$i<15-$count;$i++)
				{
				?>
				<tr class="border">
					<td colspan="1" class="srno"></td>
					<td colspan="5" class="pname"></td>
					<td colspan="2"></td>
					
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
				<td colspan="5" class="pname"></td>
				<td colspan="2"></td>
				
				<td colspan="3" class="rate"></td>
				<td class="rate"></td>
				<td colspan="1" class="rate"></td>
				<td colspan="1" class="rate"></td>
				<td colspan="2" class="rate"></td>
				
			</tr>
			<?php
		}
		?>
		<tr class="font-size">
			<td colspan="8"><strong>GSTIN No. <?=CLIENT_GST?></strong></td>
			
			
			<td colspan="3" class="text-right" style="border-left: none;"><strong><?= $totalproqty;?></strong></td>
			<td class="rate"></td>
			<td class="rate"></td>
			<td class="rate"></td>
			<td colspan="2" class="rate"><?php echo $db->rp_number_format($item['totalprice']); ?></td>
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
						
				<?php 
				$GetDistributor_b = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
				$Distri_data_b = mysqli_fetch_assoc($GetDistributor_b);
				if(strtolower($Distri_data_b['state'])!=strtolower("Bihar"))
				{
					?>
					<p><span style="font-size:13px;"><b>Bank Name </b>: <?php echo COMPANY_BANK ?> </span></p></br>
					<p><span style="font-size:13px;"><b>Bank A/c. No.</b> : <?php echo COMPANY_BANK_ACC_NO ?>  </span></p>

					<p><span style="font-size:13px;"><b>RTGS/IFSC Code</b> : <?php echo COMPANY_BANK_IFSC ?> </span></p>
					<p><span style="font-size:13px;"><b>Swift Code </b>: </span></p>
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
			<td colspan="4" class="text-right "><strong></strong></td>
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
				
				<td colspan="3" class="text-left"><strong>Cash Discount <?php echo $cart_detail_d['cash_discount']."%"; ?></strong></td>
				<td colspan="2" class="text-right "><strong><?php echo $db->rp_number_format($subtotal); ?></strong></td>
		</tr>
		<tr class="font-size">
			
			<td colspan="4" class="text-left"><strong>Assessable Value</strong></td>
			<td colspan="4" class="text-right "><strong></strong></td>
		</tr>
		<tr>
			<td colspan="11">test</td>


		</tr>
		<tr>
			<td colspan="11"></td>
		</tr>
		<tr class="font-size">
			
			<td colspan="4" class="text-rleft "><strong>IGST</strong><br><br><br><br></td>
			<td colspan="4" class="text-right "><strong><?php 
		
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
			$gst_amount=($totalprice*$GST)/100;
			$final_price=$totalprice+$gst_amount;
			$final_gst_amount = $gst_amount;
			?>
			
			<tr class="font-size">
				
				<td colspan="4" class="text-left "><strong>Assessable Value </strong></td>
				<td colspan="4" class="text-right "><strong></strong></td>
			</tr>
			
			<tr class="font-size">
			
			<td colspan="8"><p style="font-size: 13px"><b>GST Amount : </b><?php echo $ntw->rp_convertNumToWord($gst_amount); ?></p></td>
			<td colspan="4" class="text-left "><strong>IGST</strong><br><br><br><br><br><br></td>
			<td colspan="4" class="text-right "><strong><?php 
			
			 ?></strong></td>
		</tr>
			<tr class="font-size">
				
				<td colspan="8"><p style="font-size: 13px"><b>Bill Amount (Words)  : </b><?php echo $ntw->rp_convertNumToWord($totalprice); ?></p></td>
				<td colspan="4" class="text-left "><strong>Grand Total</strong></td>
				<td colspan="4" class="text-right "><strong><?php echo $db->rp_number_format($item['totalprice']); ?></strong></td>
			</tr>
			<tr>
				
			
			<td colspan="8" class="" rowspan="">	
				
					<h4><b><u>Terms & Condition</u></b></h4>
					<span style="font-size:13px;"><strong>1. We undertake no responsibility for breakage, shortage in transit of our paying
useful attention to the dispatch.</strong>
</span><br/>
					<span style="font-size:13px;"><strong>2. Our risk & responsibility ceases as soon as goods leaves our premises.</strong></span><br/>
					<span style="font-size:13px;"><strong>3. Goods once sold will not be accepted back.</strong></span><br/>
					<span style="font-size:13px;"><strong>4. Interest @1.5% Per Month will be charged,
ICICINBBCTS
If invoice amount is not paid on or before due date.(As per MSME ACT 2006).</strong></span><br/>
					<span style="font-size:13px;"><strong>5. Subject to RAJKOT jurisdiction only, E.&.O.E</strong></span><br/><br/>



			</td>

			<td colspan="8" align="center">
				<div class="col-md-12">
					<p style="font-size: 15px;"><strong>FOR, CMK ELECTRO POWER PVT.LTD.</strong></p>
					
					<br/><br/><br/><br/></br></br><br><br>
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