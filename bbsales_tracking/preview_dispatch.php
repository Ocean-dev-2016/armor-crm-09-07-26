<?php
$page_id=566;$page_slug='page_order_ajax';
require_once("connect.php");
include("../include/no_to_word.php");
$orders_detail=$db->rp_getData("orders","*","id='".$order_id."'");
if($orders_detail){
	$cart_detail_d=mysqli_fetch_assoc($orders_detail);
}

?>
<style>

table{
		height: auto;	
	    width:220mm;
		font-family: Calibri,sans-serif;
	    font-style: normal;
	    font-weight: 400;
	    padding: 2px;
	    text-decoration: none;
		font-size: 13pt;
		margin:auto;
		padding:auto;
		border: 1px solid;
	}
.border,.border th, .border td
{
	border: 1px solid #595959;
}
.border1
{
	border-left: 1px solid #595959;
	border-bottom: 1px solid #595959;
}
.border2
{
	border-left:none;
	border-bottom: 1px solid #595959;
}
.border2 .left
{
	border-left:1px solid #595959;
}
td, th {
	
	width: 30px;
	height: 25px;
	border:none;
	padding: 5px;
}

.header,.invoice,.total
{
	text-align:left;
}
.address th,.price1,.right
{
	text-align:right;
	padding-right:11px;
}
.copy_type
{
	float:right;
	padding-right:11px;
}
span
{
	padding-left:40px;
}
.uppercase
{
	text-transform:uppercase;
}
.center{
	text-align: center;
}
</style>
<table>
	<tbody>
		<tr class="header">
			<th colspan="6">GSTIN No. :24AKNPP5184F1ZV</th>
			<th colspan="8">TAX DISPATCH</th>
			
		</tr>
		<!-- <tr class="logo">
			<td colspan="14"><img src="../images/logo.png" width="150px" height="80px"/></td>
		</tr> -->
		<tr class="address">
			<th colspan="14">Manu. : All Type of <U>U.P.V.C & Pipe Fitting, C.P.V.C. Pipe & Pipe Fitting, U.P.V.C. & C.P.V.C Solvent Cenment</U><br>Near Khodiyar Auto Garage,B/H Sasta Anja Shop, Mavdi(Rajkot). Mob. : 9823532388<br>E-Mail: prashant_engineering@yahoo.in,Web-Site : www.prashanteng.com</th>
		</tr>
		<tr class="border">
			<th colspan="7" class="header" style="border-right:hidden!important;">Debit Memo</th>
			<th colspan="7"><span class="copy_type">Original</span></th>
		</tr>
		<tr>
			<td rowspan="2" colspan="7">
				<h4 style="margin-bottom:-2px;">M/s. :  <?php echo strtoupper($cart_detail_d['company_name']); ?></h4>
				<span><?php echo stripslashes($cart_detail_d['address'])."."; ?></span><br><span><?php echo $cart_detail_d['city']; ?></span>
				<h5 style="margin-bottom:-2px;"><span><?php echo $cart_detail_d['state']; ?></span></h5>
				<strong>GSTIN No.:</strong> <?php echo $db->rp_getValue("executive","gst","id='".$cart_detail_d['customer_id']."'"); ?>
			</td>
			<td  colspan="2" class="invoice border1"><strong>Dispatch No. <br>Date </strong></td>
			<td colspan="5" class="invoice border2"><strong> : #<?php echo "K/SS/DIS/0".$db->getLastInsertId("dispatch_detail"); ?><br> : <?php echo date("d-m-Y",strtotime($dispatch_date)); ?></strong></td>
		</tr>
		<tr class="border1">
			<td colspan="2" class="invoice border1">Transport <br>Bag <br> Payment </td>
			<td colspan="5" class="invoice border2"> :<br> : <br> : </td>
		</tr>
		<tr class="border">
			<th  style="width:10%">Sr.</th>
			<th colspan="2" style="width:20%">Product Name</th>
			<th colspan="2" style="width:20%">HSN</th>
			<th colspan="2" style="width:10%">Quantity</th>
			<th colspan="2" style="width:10%">List Rate</th>
			<th  style="width:10%">Disc.</th>
			<th colspan="2" style="width:10%">Net Rate</th>
			<th colspan="2" style="width:10%">Amount</th>
		</tr>
		<?php if(!empty($dispatch_items))
			{ 

				$final_subtotal=0;	
				$final_taxable=0;	
				$final_item_total=0;	
				$total_qty=0;	
				$count=0;		
				$final_cgst_tax_amount=0;
				$final_sgst_tax_amount=0;
				$final_igst_tax_amount=0;
				$final_cgst_tax=0;
				$final_sgst_tax=0;
				$final_igst_tax=0;													
				$final_grand_total=0;													
				$final_item_cash_discount_amount=0;													
				for($i=0;$i<sizeof($dispatch_items);$i++)
				{
					
					$count++;
					$current_item=$dispatch_items[$i];
					if($current_item['qty']>0)
					{
					$order_items=$db->rp_getData("order_product_item","*","id='".$current_item['id']."' AND isDelete=0");
					$order_items = mysqli_fetch_assoc($order_items);
					$item_total=$current_item['qty']*$order_items['unitprice'];
					$discount_amount=($item_total*$order_items['discount'])/100;
					$taxable=$db->rp_round($item_total-$discount_amount);
					$item_cash_discount_amount=($taxable*$cart_detail_d['cash_discount'])/100;
					$subtotal=$db->rp_round($taxable-$item_cash_discount_amount);
					if($cart_detail_d['state']=="Gujarat"){
						$cgst_tax_amount=$db->rp_round(($subtotal*$order_items['cgst_tax'])/100,2);
						$sgst_tax_amount=$db->rp_round(($subtotal*$order_items['sgst_tax'])/100,2);
						$igst_tax_amount=0;
					}else{
						$cgst_tax_amount=0;
						$sgst_tax_amount=0;
						$igst_tax_amount=$db->rp_round(($subtotal*$order_items['igst_tax'])/100,2);
					}
					$grand_total=$db->rp_round($sgst_tax_amount+$cgst_tax_amount+$igst_tax_amount+$subtotal,2);

					$final_cgst_tax_amount+=$cgst_tax_amount;
					$final_sgst_tax_amount+=$sgst_tax_amount;
					$final_igst_tax_amount+=$igst_tax_amount;
					$final_cgst_tax+=$order_items['cgst_tax'];
					$final_sgst_tax+=$order_items['sgst_tax'];
					$final_igst_tax+=$order_items['igst_tax'];
					$final_grand_total+=$grand_total;
					$final_item_total+=$item_total;
					$final_subtotal+=$subtotal;
					$final_taxable+=$taxable;
					$final_item_cash_discount_amount+=$item_cash_discount_amount;

				?>
			<tr class="border">
				<td><?php echo $count; ?></td>
				<td colspan="2"><?php echo $order_items['pro_name']; ?></td>
				<td colspan="2" class="price1"></td>
				<td colspan="2" class="price1"><?php echo $db->rp_number_format($current_item['qty'],2); ?></td>
				<td colspan="2" class="price1"><?php echo $db->rp_number_format($order_items['unitprice'],3); ?></td>
				<td class="price1"><?php echo $db->rp_number_format($order_items['discount'],2); ?>%</td>
				<td colspan="2" class="price1"><?php echo $db->rp_number_format($taxable/$current_item['qty'],4); ?></td>
				<td colspan="2" class="price1"><?php echo $db->rp_number_format($taxable,2); ?></td>
			</tr>
			<?php
			}
		}
	}
	$order_total_rounded_grandtotal=round($final_grand_total);
	if($order_total_rounded_grandtotal>$final_grand_total)
	{
		$order_total_roundoff="+"+($order_total_rounded_grandtotal-$final_grand_total);
	}
	else
	{
		$order_total_roundoff="-"+($final_grand_total-$order_total_rounded_grandtotal);
	}
		?>
		<tr class="border" >
			<td colspan="9"><strong>Rs. (in words):</strong>
				<?php 
					$ntw = new NumToWord_RP;
					echo $ntw->rp_convertNumToWord($order_total_rounded_grandtotal);
					?></td>
			<th colspan="3" class="total" style="border-right: hidden;">Sub Total </th>
			<th colspan="2" class="price1"><span class="copy_type"><?php echo $db->rp_number_format($final_taxable,2); ?></span></th>
		</tr>
		<tr class="border">
			<?php
				if($cart_detail_d['state']=="Gujarat"){
					$gst=$final_cgst_tax_amount+$final_sgst_tax_amount;
				}else{
					$gst=$final_igst_tax_amount;
				}
			?>
			<td rowspan="2" colspan="9" ><strong>GST(in words):</strong><?php echo $ntw->rp_convertNumToWord($gst); ?></td>
			<td>Cash</td>
			<td colspan="3" class="center"><?php echo $db->rp_number_format($cart_detail_d['cash_discount'],2)." %"; ?></td>
			<td class="price1"><?php echo $db->rp_number_format($final_item_cash_discount_amount,2); ?></td>
		</tr>
		<tr class="border">
			<th colspan="3" class="total" style="border-right: hidden;">Asse. Value</th>
			<th colspan="2" class="total price1"> <span class=""><?php echo $db->rp_number_format($final_subtotal,2); ?></span></th>
		</tr>
		<tr class="border2">
			<td rowspan="3" colspan="9" class="uppercase"><strong>Bank Detail</strong>
			<br>Bank Name: Centeral Bank Of India
			<br>Branch : Jagnathplot Branch
			<br>A/C No. : 3256669489
			<br>RTGS/NEFT/IFSC Code : CBIN0280570
			</td>
			<?php
			if($cart_detail_d['state']=="Gujarat"){
			?>
			<td colspan="2" class="left">CGST<br/>SGST</td>
			<td class="border2" ></td>
			<td colspan="2" class="price1"><?php echo $final_cgst_tax_amount."<br/>".$final_sgst_tax_amount; ?></td>
			<?php
			}else{
			?>
			<td colspan="2" class="left">IGST</td>
			<td class="border2" ><?php echo $final_igst_tax; ?></td>
			<td colspan="2" class="price1"><?php echo $final_igst_tax_amount; ?></td>
			<?php
			}
			?>
			
		</tr>
		<tr class="border2">
			<td colspan="2" class="left">Round Off</td>
			<td></td>
			<td colspan="2" class="price1"><?php echo round($order_total_roundoff,4); ?></td>
		</tr>
		<tr class="border2">
			<th colspan="2" class="total left">Grand Total</th>
			<th colspan="3" class="price1"><?php echo $db->rp_number_format($order_total_rounded_grandtotal,2); ?></th>
		</tr>
		<tr>
			<td colspan="10"><strong>Terms & Condition : </strong><br/>
				1. Our responsibility cases as soon as goods delivered from our premises. <br/>
				2. Goods once sold will not be accepted back. <br/>
				3. Interest @24% per annum will be charged after 15 days from the date of Dispatch. <br/>
				4. Subject to RAJKOT Jurisdiction only. <br/>
			5. E & O.E.</td>
			<td colspan="4" class="right border-left"><strong>For, Prashant Engineering </strong><br/><br/><br/><br/>
			(Authorised Signatory)</td>
		</tr>
		
	</tbody>
</table>