<?php
$page_id=566;$page_slug='page_order_ajax';
require_once("connect.php");
include("../include/no_to_word.php");
// include("../include/product.class.php");
// $ProductObj=new Product();
$ntw = new NumToWord_RP;
$orders_detail=$db->rp_getData("orders","*","id='".$order_id."'");
if($orders_detail){
	$cart_detail_d=mysqli_fetch_assoc($orders_detail);
}
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
$brand_logo1 = SITEURL."images/cmk_logo.png";
$newDispatchItemArray = array();
// print_r($_REQUEST);exit;
foreach ($_REQUEST['dispatch_items'] as $key => $value)
{
	$newDispatchItemArray[$value['id']] = $value['qty'];
}
// print_r($newDispatchItemArray);exit;
?>
<style>
.mainDiv, .tbody-class{
	border: 1px solid #595959;
	border-collapse: collapse;
	font-size: 13px;
	width:250mm!important;
	background-color: #FFF;
	margin:auto;
  	padding:auto;
}
.tbody-class , td, th {
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
.no-border-top{
	border-top: hidden !important;
}
.border td{
	border-bottom: hidden !important;
}
.no-border-bottom{
	border-bottom: hidden !important;
}
.srno{
	/*width: 5% !important;
	min-width: 5%!important;
	max-width: 5%!important;*/
	width: 1% !important;
	min-width: 1%!important;
	max-width: 1%!important;
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
/*.qty{
	width: 10% !important;
	min-width: 10%!important;
	max-width: 10%!important;
}*/
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
/*tbody
{
	text-transform: uppercase;
}*/
.font-size td
{
	font-size: 15px!important;
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
	border-right-color:<?= VIEW_LOGO ?>;
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
</style>
<table class='tbody-class'>
	<tbody>
		<tr>
			<td colspan="16" class="no-border-bottom height-5"></td>
		</tr>

		<!-- <tr>
			<td colspan="4" class="no-border-bottom text-left border-r-width border-gray" style="width: 15%!important;"><span style="float: left" ><img width="170"  src="<?= SITEURL.VIEW_LOGO ?>"></span></td>
			<td colspan="12" class="no-border-bottom" style="font-size: 27px;"><strong><?php echo CLIENT_BRAND_NAME ?></strong><br>
			<p class="font-13"><strong>Factory Address : </strong><?= FACTORY_ADDRESS ?><br>
			<strong>Office Address : </strong><?= CLIENT_ADDRESS ?><br>
			<?= OFFICE_PHONE ?> <?= OFFICE_EMAIL ?> 
			</p>
			</td>
		</tr>
		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5"></td>
		</tr> -->

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
			<td colspan="6" class="text-center no-border-right no-border-bottom" style="background-color: <?= VIEW_LOGO ?>"><b>Dispatch Order</b></td>
			<td colspan="5" class="no-border-bottom bg-gray"></td>

			<!-- <td colspan="16" class="text-center" style="background-color: #E5E5E5 !important;border-bottom: none;"><strong style="background-color: #8cdee8;padding-left: 50px;padding-right: 50px;padding-top: 5px;padding-bottom: 4px;"><b>Quotation</b></strong></td> -->
		</tr>
		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5"></td>
		</tr>
		<tr class="vertical-top">
			<td colspan="5" width="25%;" class="no-border-bottom no-border-top border-r-width border-blue vertical-top">
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
			<td colspan="6" class="no-border-bottom no-border-top border-r-width border-blue vertical-top">
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
			<td colspan="5" width="30%;" class="no-border-bottom no-border-top vertical-top">
				<strong>Details<br/></strong>
					<!-- <table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 0mm!important;"> -->
					<table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 100% !important;">
						<!-- <tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top">Dispatch Order No : </td>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $cart_detail_d['dispatch_no'] ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top">Dispatch Order Date : </td>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= date('m-d-Y', strtotime($cart_detail_d['dispatch_date'])); ?></td>
						</tr> -->
						<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top">Sales Order No : </td>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $cart_detail_d['order_no'] ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Sales Order Date : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= date('m-d-Y', strtotime($cart_detail_d['order_date'])); ?></td>
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
							<td class="no-border-right no-border-bottom no-border-left">Transport By : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("transport_by","name","isDelete=0 AND id='".$cart_detail_d['transport_through']."' ") ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Transporter Detail : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("transport_master","name","isDelete=0 AND id='".$cart_detail_d['transport_name']."' ") ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Tendor Code : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $cart_detail_d['tendor_code'] ?></td>
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
		
	</tbody>
</table>
<table class='tbody-class'>
	<tr>
		<th style="width: 5%;">SrNo</th>
		<th style="width: 85%;">Product Name</th>
		<th style="width: 10%;" class="text-right">Qty</th>
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
		$hsncode=$db->rp_getValue("product","hsn_code","id='".$item['pro_id']."' AND isDelete=0",0);
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
		$rate=$item['pro_qty']*$item['unitprice'];
		$totalprice+=$item['totalprice'];
		$s_total = $totalprice;
		$boxqty+=$item['box_qty'];
		$cartoonqty+=$item['cartoon_qty'];
		$totalproqty+=$item['pro_qty'];
		$totalrate+=$item['unitprice'];
		$totaldiscount+=$item['discount'];
		if($brand_name=="Prince Platinum")
		{
			$brand_name="Prince Plm";
		}
		if(isset($newDispatchItemArray[$item['id']]) && $newDispatchItemArray[$item['id']]!="" && $newDispatchItemArray[$item['id']]!=0){
		$count++;
?>
	<tr>
		<td  class="srno"><?php echo $count; ?></td>
		<td  class="pname"><?php echo $pro_name ?></td>
		<td  class="text-right qty"><?php echo $newDispatchItemArray[$item['id']]; ?></td>
	</tr>
<?php
	$total +=$newDispatchItemArray[$item['id']]; 
		}
	}
	if($count<16)
	{
		for($i=0;$i<25-$count;$i++)
		{
?>
		<tr class="border">
			<td  class="srno"></td>
			<td  class="pname"></td>
			<td  class="qty"></td>
		</tr>
<?php 
		}
	}
?>
	<tr>
		<td  class="srno"></td>
		<td  class="pname"></td>
		<td  class="qty"></td>
	</tr>
<?php
	}
?>
		<tr class="text-right">
			<td class="srno"></td>
			<td class="pname" style="text-align: right;">Total</td>
			<td class="qty"><?= $total; ?></td>
		</tr>
</table>
<table class='tbody-class'>
	<tbody>
		<tr>
			<td colspan="9"><strong>Terms & Condition : </strong><br/>
				<span style="font-size:13px;"><?= $cart_detail_d['terms_comdition'] ?></span>
			</td>
			<td colspan="5" class="text-center"><strong>For, <?php echo CLIENT_BRAND_NAME; ?> </strong>
				<br/><br/><br/><br/>
				(Authorised Signatory)
			</td>
		</tr>
	</tbody>
</table>