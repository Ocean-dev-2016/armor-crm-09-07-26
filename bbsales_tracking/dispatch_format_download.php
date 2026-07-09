<?php
$page_id=566;$page_slug='page_order_ajax';
require_once("connect_in.php");
require_once("../include/no_to_word.php");
$ntw = new NumToWord_RP;
$dispatch_id	= $_REQUEST['id'];
$cart_detail_r 	= $db->rp_getData("dispatch_detail","*","id='".$dispatch_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);
$dispatch_id=$cart_detail_d['id'];
$dispatch_no=$cart_detail_d['dispatch_no'];
$gst_no=$db->rp_getValue("executive","gst","id='".$cart_detail_d['customer_id']."'");
$dispatch_date=($cart_detail_d['dispatch_date']!="0000-00-00 00:00:00")?date("d-m-Y",strtotime($cart_detail_d['dispatch_date'])):"";
if($cart_detail_d['state']=="Gujarat"){
	$gst=$cart_detail_d['cgst_amount']+$cart_detail_d['sgst_amount'];
}else{
	$gst=$cart_detail_d['igst_amount'];
}
$brand_logo1 = SITEURL."images/cmk_logo.png";

$Warehouse_id = $cart_detail_d['warehouse_id'];
$warehouseids = array();
$warehouseR = $db->rp_getData("warehouse","*","id In (".$Warehouse_id.") AND isDelete=0","",0);
while($warehouseD = mysqli_fetch_assoc($warehouseR))
{
	$warehouseids[] = $warehouseD['name'];
}
$name = implode(",", $warehouseids);
?>
<style>
.mainDiv, table{
	/*border: 1px solid #595959;
	border-collapse: collapse;
	font-size: 13px;
	width:250mm!important;
	background-color: #FFF;*/
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
.w-100
{
	width: 100% !important;
}
</style>
<table>
	<tbody>
		<!-- <tr>
			<td colspan="16" class="no-border-bottom height-5"></td>
		</tr> -->
		<!-- <tr>
			<td colspan="4" class="no-border-bottom text-left border-r-width border-gray" style="width: 15%!important;"><span style="float: left" ><img width="170"  src="<?= SITEURL.VIEW_LOGO ?>"></span></td>
			<td colspan="12" class="no-border-bottom" style="font-size: 27px;"><strong><?php echo CLIENT_BRAND_NAME ?></strong><br>
			<p class="font-13"><strong>Factory Address : </strong><?= FACTORY_ADDRESS ?><br>
			<strong>Office Address : </strong><?= CLIENT_ADDRESS ?><br>
			<?= OFFICE_PHONE ?>&nbsp;&nbsp;&nbsp;<?= OFFICE_EMAIL ?> &nbsp;&nbsp;&nbsp;<?=OFFICE_WEBSITE?><br>
			<strong>GST No : </strong><?= COMPANY_GST ?><br>
			</p>
			</td>
		</tr> -->

		<tr>
			<td colspan="16">
						<!-- <img style="width: 100%;padding: 0px !important;"  src="<?= VIEW_LOGO_All ?>"> -->
						<div style="position: absolute; width: 96%; padding-left:5px; padding-right:5px; padding-top:5px;" >
							<img   src="<?= VIEW_LOGO_All ?>">
						</div>
					</td>
		</tr>
		<tr style="background-color: <?= VIEW_COLOR ?>;">
					<td colspan="16" align="center"><b>Rate Confirmation Quotation</b></td>
				</tr> 
		<br><br><br><br><br><br><br><br><br><br><br><br>

		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5 text-center"><strong>GST - <?= COMPANY_GST ?></strong></td>
		</tr>
		<tr>
			<td colspan="6" class="no-border-right no-border-bottom bg-gray"></td>
			<td colspan="4" class="text-center no-border-right no-border-bottom" style="background-color: <?= VIEW_COLOR ?>"><b>Dispatch Order</b></td>
			<td colspan="6" class="no-border-bottom bg-gray"></td>
		</tr>
		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5"></td>
		</tr>
		<tr class="vertical-top">
			<td colspan="6"  class="no-border-bottom no-border-top border-r-width border-blue vertical-top">
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
				<!-- <span>Mobile : <?=$Distri_data_a['phone']?></span><br/> -->
				<!-- <span>Date:- :</span><br/> -->
			</td>
			<!-- <div class="vertical_line"></div> -->
			<td colspan="4"  class="no-border-bottom no-border-top border-r-width border-blue vertical-top">
				<strong>Address<br/></strong>
				<strong>Shipping Address</strong><br>
				<?= wordwrap($cart_detail_d['shipping_address'],40,"<br>\n"); ?> <br><br>
				<strong>Billing Address</strong><br>
				<?= wordwrap($cart_detail_d['billing_address'],40,"<br>\n"); ?> <br><br>
				<strong>Transport By :</strong>
				<?= $db->rp_getValue("transport_by","name","isDelete=0 AND id='".$cart_detail_d['transport_through']."' ") ?><br>
				<strong>Transporter Detail :</strong>
				<?= $db->rp_getValue("transport_master","name","isDelete=0 AND id='".$cart_detail_d['transport_name']."' ") ?><br>
			</td>
			<!-- <div class="vertical_line1"></div> -->
			<?php 
				$inquiry_date = $db->rp_getValue("no_order_inquiry","inquiry_date","isDelete=0 AND id='".$cart_detail_d['inquiry_id']."' ");
				?>
			<td colspan="6"  class="no-border-bottom no-border-top vertical-top">
				<strong>Details<br/></strong>
					<!-- <table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 0mm!important;"> -->
					<table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 100%!important;">
						<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top w-100"><b>Dispatch Order No :<b> </td>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $cart_detail_d['dispatch_no'] ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top w-100"><b>Dispatch Order Date :</b> </td>
							<td class="no-border-right no-border-bottom no-border-left no-border-top w-100"><?= date('d-m-Y', strtotime($cart_detail_d['dispatch_date'])); ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top w-100"><b>Sales Order No :</b> </td>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $cart_detail_d['order_no'] ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left w-100"><b>Sales Order Date :</b> </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= date('d-m-Y', strtotime($cart_detail_d['order_date'])); ?></td>
						</tr>
						<!-- <tr>
							<td class="no-border-right no-border-bottom no-border-left">Quotation  No : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("quotation_detail","quotation_no","id='".$cart_detail_d['quotation_id']."'")?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Quotation Date : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= date('d-m-Y',strtotime($db->rp_getValue("quotation_detail","quotation_date","id='".$cart_detail_d['quotation_id']."'")))?></<td>
						</tr> -->
						
						<tr>
							<td class="no-border-right no-border-bottom no-border-left w-100"><b>Tendor Code :</b> </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $cart_detail_d['tendor_code'] ?></td>
						</tr>

						<tr>
							<td class="no-border-right no-border-bottom no-border-left w-100"><b>Warehouse :</b> </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $name ?></td>
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
		<tr>
			<td colspan="16" class="no-border-top height-5"></td>
		</tr>
		<?php
		$order_data_r=$db->rp_getData("orders","*","isDelete=0 AND order_no='".$cart_detail_d['order_no']."' ");
		$order_data_d=mysqli_fetch_assoc($order_data_r);
		?>
		<!-- <tr>
			<td colspan="4">
				<?php
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a); 
				?>
				<strong><?php echo $cart_detail_d['company_name']." - ".$cart_detail_d['customer_name']; ?></strong><br/>
				Address : <strong><?php echo $cart_detail_d['address']; ?></strong><br/>
				City : <strong><?php echo $cart_detail_d['city']; ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				Pincode : <strong><?php echo $Distri_data_a['zip']; ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				State : <strong><?php echo $cart_detail_d['state']; ?></strong><br>
				Place of Supply :<strong> <?php echo $cart_detail_d['state']; ?></strong><br/>
				GSTIN No. :<strong> <?php echo $gst_no; ?></strong><br>
				Transport Through : <strong><?= $db->rp_getValue("transport_by","name","id='".$cart_detail_d['transport_through']."'") ?></strong> <br/>Transport : <strong><?= $db->rp_getValue("transport_master","name","transport_by_id='".$cart_detail_d['transport_through']."' AND id='".$cart_detail_d['transport_name']."'") ?></strong><br/> 
				Order.No : <strong><?php echo $cart_detail_d['order_no'] ?></strong>
			</td>
			<td colspan="6" style="vertical-align: top;">Billing Address : <br/><strong><?php echo $order_data_d['billing_address'] ?></strong></td>
			<td colspan="6" style="vertical-align: top;">Shipping Address : <br/><strong><?php echo $order_data_d['shipping_address'] ?></strong></td>
		</tr> -->
		</tbody></table>
		<table>
			<tr  style="background-color: <?= VIEW_COLOR ?>;">
				<th style="width: 5%;">SrNo</th>
				<th style="width: 80%;">Product Name</th>
				<th style="width: 5%;">Unit</th>
				<th style="width: 10%;" class="text-right">Qty</th>
			</tr>
			<?php
			$items = $db->rp_getData("dispatch_item","*","dispatch_id='".$dispatch_id."'","",0);
			if($items){
				$count=0;
				$cgst_tax=0;
				$sgst_tax=0;
				$igst_tax=0;
				while ($item=mysqli_fetch_assoc($items)) {
					$count++;
					$rate=$item['qty']*$item['unitprice'];
					$cgst_tax+=$item['cgst_tax'];
					$sgst_tax+=$item['sgst_tax'];
					$igst_tax+=$item['igst_tax'];
					$tax=$item['cgst_tax']+$item['sgst_tax']+$item['igst_tax'];
					$hsncode=$db->rp_getValue("product","hsn_code","id='".$item['pro_id']."' AND isDelete=0",0);
					$unit_id = $db->rp_getValue("product","display_unit","id='".$item['pro_id']."' AND isDelete=0");
					$unit_name = $db->rp_getValue("unit","name","id='".$unit_id."' AND isDelete=0");
			?>
			<tr>
				<td  class="srno"><?php echo $count; ?></td>
				<td  class="pname"><?php echo $item['pro_name']; ?><?=(isset($item['pro_description']) && $item['pro_description']!="")?"<br/><br/>".$item['pro_description']:""?></td>
				<td  class="pname"><?php echo $unit_name; ?></td>
				<td  class="text-right qty"><?php echo $item['qty']; ?></td>
			</tr>
			<?php
			}
			if($count<16)
			{
				for($i=0;$i<25-$count;$i++)
				{
				?>
				<tr class="border">
					<td  class="srno"></td>
					<td  class="pname"></td>
					<td  class=""></td>
					<td  class="qty"></td>
				</tr>
				<?php 
				}
			}
			?>
			<tr>
				<td  class="srno"></td>
				<td  class="pname"></td>
				<td  class=""></td>
				<td  class="qty"></td>
			</tr>
			<?php
		}
		?>
			<tr class="text-right">
				<td class="srno"></td>
				<td class=""></td>
				<td class="pname">Total</td>
				<td class="qty"><?= $cart_detail_d['dispatch_qty']; ?></td>
			</tr>
		</table>
		<table>
			<tbody>
				<tr>
					<td colspan="10">
						<strong>Terms & Condition : </strong><br/>
						<span style="font-size:13px;"><?= str_replace('rn','',$order_data_d['terms_comdition']) ?></span>
					</td>
					<td colspan="6" class="text-center" ><strong>For, <?php echo CLIENT_BRAND_NAME; ?> </strong>
						<br/><br/><br/><br/>
						(Authorised Signatory)
					</td>
				</tr>
			</tbody>
		</table>