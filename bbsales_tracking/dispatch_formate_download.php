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
		<tr>
			<td colspan="16" >
				<!-- <div style="position: absolute; width: 97%; padding-left:0px;" >
					<img   src="<?= VIEW_LOGO_All ?>">
				</div> -->
				<div style="position: absolute; width: 96%; padding-left:5px; padding-right:10px; padding-top:5px;" >
							<img   src="<?= VIEW_LOGO_All ?>">
				</div>

			</td>
		</tr>
		<br><br><br><br><br><br><br><br><br><br><br>
		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5 text-center"><strong>GST - <?= COMPANY_GST ?></strong></td>
		</tr>


	</tbody>
</table>
<table>
	<tbody>
		<tr>
			<td colspan="5" class="no-border-right no-border-bottom bg-gray"></td>
			<td colspan="5" class="text-center no-border-right no-border-bottom" style="background-color: <?= VIEW_COLOR ?>"><b>Dispatch Order</b></td>
			<td colspan="6" class="no-border-bottom bg-gray"></td>
		</tr>
		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5"></td>
		</tr>
		<tr class="vertical-top">
			<td colspan="5"  class="no-border-bottom no-border-top border-r-width border-blue vertical-top">
				<strong>To,<br/></strong>
				<?php
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0","",0);
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
				?>
				<strong><?php echo $cart_detail_d['company_name']; ?></strong><br/><span>
				<?php echo wordwrap($Distri_data_a['address'],40,"<br>\n")."  <br/>".$Distri_data_a['city']." - ".$Distri_data_a['zip']." , ".$Distri_data_a['state']." , ".$Distri_data_a['country'] ?></span><br/><br>
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
			<td colspan="5" class="no-border-bottom no-border-top border-r-width border-blue vertical-top">
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
			<td colspan="6" class="no-border-bottom no-border-top vertical-top">
				<strong>Details<br/></strong>
				<!-- <table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 0mm!important;"> -->
				<!-- <table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 100%!important;"> -->
				<table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 0mm!important;">

					<tr>
						<td class="no-border-right no-border-bottom no-border-left no-border-top w-100">Dispatch Order No : </td>
						<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $cart_detail_d['dispatch_no'] ?></td>
					</tr>
					<tr>
						<td class="no-border-right no-border-bottom no-border-left no-border-top w-100">Dispatch Order Date : </td>
						<td class="no-border-right no-border-bottom no-border-left no-border-top w-100"><?= date('d-m-Y', strtotime($cart_detail_d['dispatch_date'])); ?></td>
					</tr>
					<tr>
						<td class="no-border-right no-border-bottom no-border-left no-border-top w-100">Sales Order No : </td>
						<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $cart_detail_d['order_no'] ?></td>
					</tr>
					<tr>
						<td class="no-border-right no-border-bottom no-border-left w-100">Sales Order Date : </td>
						<td class="no-border-right no-border-bottom no-border-left"><?= date('d-m-Y', strtotime($cart_detail_d['order_date'])); ?></td>
					</tr>
					<tr>
						<td class="no-border-right no-border-bottom no-border-left w-100">Transport By : </td>
						<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("transport_by","name","isDelete=0 AND id='".$cart_detail_d['transport_through']."' ") ?></td>
					</tr>
					<tr>
						<td class="no-border-right no-border-bottom no-border-left w-100">Transporter Detail : </td>
						<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("transport_master","name","isDelete=0 AND id='".$cart_detail_d['transport_name']."' ") ?></td>
					</tr>
					<tr>
						<td class="no-border-right no-border-bottom no-border-left w-100">Tendor Code : </td>
						<td class="no-border-right no-border-bottom no-border-left"><?= $cart_detail_d['tendor_code'] ?></td>
					</tr>
					<tr>
						<td class="no-border-right no-border-bottom no-border-left w-100">Warehouse : </td>
						<td class="no-border-right no-border-bottom no-border-left"><?= $name ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="16" class="no-border-top height-5"></td>
		</tr>
		<?php
		$order_data_r=$db->rp_getData("orders","*","isDelete=0 AND order_no='".$cart_detail_d['order_no']."' ");
		$order_data_d=mysqli_fetch_assoc($order_data_r);
		?>
	</tbody>
</table>
<table>
	<tbody>
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
				$unit_id = $db->rp_getValue("product","unit_id","id='".$item['pro_id']."' AND isDelete=0");
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
	</tbody>
</table>
<table>
	<tbody>
		<tr>
			<td colspan="11">
				<strong>Terms & Condition : </strong><br/>
				<span style="font-size:13px;"><?= str_replace('rn','',$order_data_d['terms_comdition']) ?></span>
			</td>
			<td colspan="5" class="text-center"><strong>For, <?php echo CLIENT_BRAND_NAME; ?> </strong>
				<br/><br/><br/><br/>
				(Authorised Signatory)
			</td>
		</tr>
	</tbody>
</table>