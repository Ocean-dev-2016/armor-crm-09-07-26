<?php
$page_id=566;$page_slug='page_order_ajax';
require_once("connect_in.php");
include("../include/no_to_word.php");
$ntw = new NumToWord_RP;
$order_id	= $_REQUEST['order_id'];
$cart_detail_r 	= $db->rp_getData("orders","*","id='".$order_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);
$gst_no=$db->rp_getValue("executive","gst","id='".$cart_detail_d['customer_id']."'");
$order_date=($cart_detail_d['order_date']!="0000-00-00 00:00:00")?date("d-m-Y",strtotime($cart_detail_d['order_date'])):"";
if($cart_detail_d['state']=="Gujarat"){
	$gst=$cart_detail_d['cgst_amount']+$cart_detail_d['sgst_amount'];
}else{
	$gst=$cart_detail_d['igst_amount'];
}
?>

<style>
.mainDiv, table{
	border: 1px solid #595959;
	border-collapse: collapse;
	font-size: 13px;
	width:250mm!important;
	background-color: #FFF;
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
}
.hsn{
	width: 20% !important;
	min-width: 20%!important;
	max-width: 20%!important;
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
.gst{
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
</style>
<table>
	<tbody>
		<tr class="color">
			<td colspan="14" class="text-center"><h3><strong>PRIMATE INDUSTRIES</strong></h3></td>
		</tr>
		<tr>
			<td colspan="14" class="text-center"><h4>SURVEY NO 238, NEAR DWARKESH GAUSHALA, OPP.M TECH INDUSTRIED,<br/> NEAR OLD TALL NAKA, GONDAL NATIONAL HIGHWAY, RAJKOT-360004</h4></td>
		</tr>
		<tr>
			<td colspan="3"><strong>Debit Memo</strong></td>
			<td colspan="8" class="text-center no-border-left"><strong>Customer Order</strong></td>
			<td colspan="3" class="text-right no-border-left"><strong>TRIPLICATE FOR SUPPLIER</strong></td>
		</tr>
		<tr  class="color">
			<td colspan="5" class="text-center"><strong>Buyer To Party</strong></td>
			<td colspan="9" class="text-center no-border-left"><strong>Ship To Party</strong></td>
		</tr>
		<tr>
			<td colspan="5">
				<strong>M/s. : <?php echo $cart_detail_d['company_name']; ?></strong><br/>
				<?php echo $cart_detail_d['address']; ?><br/>
				<strong><?php echo $cart_detail_d['city']; ?></strong><br/>
				<strong>Place of Supply :</strong> <?php echo $cart_detail_d['state']; ?><br/>
				<strong> GSTIN No. :</strong> <?php echo $gst_no; ?>
			</td>

			<td colspan="9">
				<strong>M/s. : <?php echo CLIENT_NAME; ?></strong><br/>
				<?php echo CLIENT_ADDRESS; ?><br/>
				<strong><?php echo CLIENT_CITY; ?></strong><br/>
				<strong>Place of Supply :</strong> <?php echo CLIENT_STATE; ?><br/>
				<strong> GSTIN No. : </strong>
			</td>
		</tr>
		<tr>
			<td colspan="5"><strong>Transport :</strong> SELF<br/> <strong>Bag/Cartoons : </strong>39 <br/> <strong>P.O.No :</strong></td>
			<td colspan="9" class="color"><strong>Order No. :  <?php echo $cart_detail_d['order_no']; ?><br/> Date : <?php echo $order_date; ?></strong></td>
		</tr>
		<tr>
			<th colspan="2" class="text-center srno">SrNo</th>
			<th colspan="2" class="text-center pname">Product Name</th>
			<th colspan="2" class="text-center hsn">HSN/SAC</th>
			<th colspan="2" class="text-center qty">Qty</th>
			<th colspan="2" class="text-center rate">Rate</th>
			<th colspan="2" class="text-center gst">GST%</th>
			<th colspan="2" class="text-center amount">Amount</th>
		</tr>
		<?php
		$items=$db->rp_getData("order_product_item","*","order_id='".$order_id."'");
		if($items){
			$count=0;
			$cgst_tax=0;
			$sgst_tax=0;
			$igst_tax=0;
			while ($item=mysqli_fetch_assoc($items)) {
				$count++;
				$rate=$item['pro_qty']*$item['unitprice'];
				$cgst_tax+=$item['cgst_tax'];
				$sgst_tax+=$item['sgst_tax'];
				$igst_tax+=$item['igst_tax'];
				$tax=$item['cgst_tax']+$item['sgst_tax']+$item['igst_tax'];
			?>
			<tr>
				<td colspan="2" class="srno"><?php echo $count; ?></td>
				<td colspan="2" class="pname"><?php echo $item['pro_name']; ?></td>
				<td colspan="2" class="hsn"></td>
				<td colspan="2" class="text-right qty"><?php echo $db->rp_number_format($item['pro_qty']); ?></td>
				<td colspan="2" class="text-right rate"><?php echo $db->rp_number_format($item['unitprice']); ?></td>
				<td colspan="2" class="text-right gst"><?php echo $tax." %"; ?></td>
				<td colspan="2" class="text-right amount"><?php echo $db->rp_number_format($item['taxable']); ?></td>
			</tr>
			
			<?php
			}
			if($count<16)
			{
				for($i=0;$i<25-$count;$i++)
				{
				?>
				<tr class="border">
					<td colspan="2" class="srno"></td>
					<td colspan="2" class="pname"></td>
					<td colspan="2" class="hsn"></td>
					<td colspan="2" class="qty"></td>
					<td colspan="2" class="rate"></td>
					<td colspan="2" class="gst"></td>
					<td colspan="2" class="amount"></td>
				</tr>
				<?php 
				}
			}
			?>
			<tr>
				<td colspan="2" class="srno"></td>
				<td colspan="2" class="pname"></td>
				<td colspan="2" class="hsn"></td>
				<td colspan="2" class="qty"></td>
				<td colspan="2" class="rate"></td>
				<td colspan="2" class="gst"></td>
				<td colspan="2" class="amount"></td>
			</tr>
			<?php
		}
		?>
		<tr class="color">
			<td colspan="5"><h4><strong>GSTIN No.: 24AASFP8146N1ZF</strong></h4></td>
			<td colspan="3" class="text-right no-border-left"><?php echo $db->rp_number_format($cart_detail_d['total_qty']); ?></td>
			<td colspan="3"><strong>Sub Total</strong></td>
			<td colspan="3" class="text-right no-border-left"><strong><?php echo $db->rp_number_format($cart_detail_d['taxable']); ?></strong></td>
		</tr>
		<tr>
			<td colspan="8"><strong>Bank Name :</strong> CENTRAL BANK OF INDIA<br/> <strong>BANK A/C. No. : </strong>3560488826<br/> <strong>RTGS/IFSC Code :</strong> CBIN0280571</td>
			<td colspan="2" class="">CASH</td>
			<td colspan="2" class="text-center"><?php echo $cart_detail_d['cash_discount']; ?>%</td>
			<td colspan="2" class="text-right"><?php echo $cart_detail_d['cash_discount_amount']; ?></td>
		</tr>
		<tr class="border">
			<td colspan="8" class="border"><strong>Total GST :</strong> <?php echo $ntw->rp_convertNumToWord($gst); ?></td>
			<td colspan="3"><strong>Taxable Amount</strong></td>
			<td colspan="3" class="text-right no-border-left"><strong><?php echo $db->rp_number_format($cart_detail_d['taxable']); ?></strong></td>
		</tr>
		<tr class="border">
			<td colspan="8" rowspan="2"><strong>Bill Amount :</strong> <?php echo $ntw->rp_convertNumToWord($cart_detail_d['grand_total']); ?></td>

			<?php
			if($cart_detail_d['state']=="Gujarat"){
			?>
			<td colspan="2">CGST<br/>SGST</td>
			<td colspan="2" class="text-center no-border-left"><?php echo $cgst_tax."%<br/>".$sgst_tax."%"; ?></td>
			<td colspan="2" class="text-right no-border-left"><?php echo $db->rp_number_format($cart_detail_d['cgst_amount'],2)."<br/>".$db->rp_number_format($cart_detail_d['sgst_amount'],2); ?></td>
			<?php
			}
			else
			{
			?>
			<td colspan="2">IGST</td>
			<td colspan="2" class="text-center no-border-left"><?php echo $igst_tax."%"; ?></td>
			<td colspan="2" class="text-right no-border-left"><?php echo $db->rp_number_format($cart_detail_d['igst_amount'],2); ?></td>
			<?php
			}
			?>
		</tr>
		<tr>
			<td colspan="2">ROUND OFF</td>
			<td colspan="2" class=" no-border-left"></td>
			<td colspan="2" class="text-right no-border-left"><?php echo $db->rp_number_format($db->rp_round($cart_detail_d['roundoff'],2),2); ?></td>
		</tr>
		<tr>
			<td colspan="8"><strong>Note :</strong></td>
			<td colspan="2" class="color"><strong>Grand Total</strong></td>
			<td colspan="2" class=" no-border-left color"></td>
			<td colspan="2" class="text-right no-border-left color"><strong><?php echo $db->rp_number_format($cart_detail_d['grand_total_rounded'],2); ?></strong></td>
		</tr>
		<tr>
			<td colspan="8"><strong>Terms & Condition : </strong><br/>
				1. Goods once sold will not be accepted back. <br/>
				2. Interest @18% p.a. will be charged if payment is not made within due date. <br/>
				3. Our risk and responsibility ceases as soon as the goods leave our premises.<br/>
				4. Subject to RAJKOT Jurisdiction only. E & O.E.</td>
			<td colspan="6" class="text-right"><strong>For, PRIMATE INDUSTRIES </strong><br/><br/><br/><br/>
			(Authorised Signatory)</td>
		</tr>
	</tbody>
</table>