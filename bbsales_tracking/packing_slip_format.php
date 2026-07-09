<?php
$page_id    = 400;
require_once("connect_in.php");
require_once("../include/no_to_word.php");
$ntw = new NumToWord_RP;
$packing_slip_id = $_REQUEST['id'];
$cart_detail_r 	= $db->rp_getData("packing_slip","*","id='".$packing_slip_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);

$getPackingSplipItemDataR = $db->rp_getData("packing_slip_item","*","isDelete = 0 AND isActive = 1 AND packing_slip_id = '".$_REQUEST['id']."' GROUP BY main_carton_type_count");
	$getPackingSplipItemData = array();
while($getPackingSplipItemDataComp = mysqli_fetch_assoc($getPackingSplipItemDataR))
{
	$getPackingSplipItemData[] = $getPackingSplipItemDataComp;
}

$dispatch_data_r = $db->rp_getData("dispatch_detail","*","isDelete=0 AND id='".$cart_detail_d['dispatch_id']."' ");
$dispatch_data_d = mysqli_fetch_assoc($dispatch_data_r);

// This is a single-line comment
$invoice_no=$db->rp_getValue("invoice_new","invoice_no","dispatch_ids='".$dispatch_data_d['id']."'");
// This is a single-line comment

?>

<style>
.mainDiv, table{
	/*border: 1px solid #595959;
	border-collapse: collapse;
	font-size: 14px;
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
			<?= OFFICE_PHONE ?>&nbsp;&nbsp;&nbsp;<?= OFFICE_EMAIL ?> &nbsp;&nbsp;&nbsp;<?=OFFICE_WEBSITE?><br> <strong>GST No : </strong><?= COMPANY_GST ?>
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
			<td colspan="5" class="text-center no-border-right no-border-bottom" style="background-color: <?= VIEW_COLOR ?>"><b>Packing Slip</b></td>
			<td colspan="6" class="no-border-bottom bg-gray"></td>

			<!-- <td colspan="16" class="text-center" style="background-color: #E5E5E5 !important;border-bottom: none;"><strong style="background-color: #8cdee8;padding-left: 50px;padding-right: 50px;padding-top: 5px;padding-bottom: 4px;"><b>Quotation</b></strong></td> -->
		</tr>
		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5"></td>
		</tr>
		<tr class="vertical-top">
			<td colspan="5" class="no-border-bottom no-border-top border-r-width border-blue vertical-top">
				<strong>To,<br/></strong>
				<?php
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0","",0);
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
				?>
				<strong><?php echo $Distri_data_a['company_name']; ?></strong><br/><span>
				<?php echo  wordwrap($Distri_data_a['address'],40,"<br>\n")."  <br/>".$Distri_data_a['city']." - ".$Distri_data_a['zip']." , ".$Distri_data_a['state']." , ".$Distri_data_a['country'] ?></span><br/><br>
				<span>Contact Person : <?=$Distri_data_a['cname']?></span><br>
				<span>Contact Number : <?=$Distri_data_a['phone']?></span><br>
				<span>Email : <?=$Distri_data_a['email']?></span><br/>
				<span>State of Supply : <?=$Distri_data_a['state']?></span><br/>
				<strong><span>GST No : <?=$Distri_data_a['gst']?></span></strong><br/>
				<!-- <span>Vendor Code : <?= $cart_detail_d['vendor_code'] ?></span><br/> -->
				<!-- <span>Mobile : <?=$Distri_data_a['phone']?></span><br/> -->
				<!-- <span>Date:- :</span><br/> -->
			</td>
			<!-- <div class="vertical_line"></div> -->
			<td colspan="5" width="33%;" class="no-border-bottom no-border-top border-r-width border-blue vertical-top">
				<?php
				$shipping_address =  wordwrap($db->rp_getValue("dispatch_detail","shipping_address","id='".$cart_detail_d['dispatch_id']."'"),40,"<br>\n"); 
				$billing_address = wordwrap($db->rp_getValue("dispatch_detail","billing_address","id='".$cart_detail_d['dispatch_id']."'"),40,"<br>\n"); 
				?>
				<strong>Address<br/></strong>
				<strong>Shipping Address</strong><br>
				<!-- <?= $cart_detail_d['shipping_address'] ?> <br><br> -->
				<?= wordwrap($shipping_address,40,"<br>\n"); ?> <br><br>
				<strong>Billing Address</strong><br>
				<!-- <?= $cart_detail_d['billing_address'] ?> <br> -->
				<?= wordwrap($billing_address,40,"<br>\n"); ?> <br><br>
				<!--  update file (for sales Execuitve name)(sagar)    -->
				<strong>Sales Officer : </strong>
				<?php  
				$sales_id=$db->rp_getValue('dispatch_detail','sales_id','id="'.$cart_detail_d['dispatch_id'].'" AND isDelete=0 AND isActive=1');
                                echo $db->rp_getValue('sales_executive','name','id="'.$sales_id.'" AND isDelete=0 AND isActive=1');
				?>
				<!--  update file (for sales Execuitve name)(sagar)    -->
			</td>
			<!-- <div class="vertical_line1"></div> -->
			<?php 
				$inquiry_date = $db->rp_getValue("no_order_inquiry","inquiry_date","isDelete=0 AND id='".$cart_detail_d['inquiry_id']."' ");
				?>
			<td colspan="6" width="33%;" class="no-border-bottom no-border-top vertical-top">
				<strong>Details<br/></strong>
					<!-- <table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 0mm!important;"> -->
					<table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 100% !important;">
						<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top">Packing Slip No : </td>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $cart_detail_d['packing_slip_no'] ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Packing Slip Date : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= date('d-m-Y', strtotime($cart_detail_d['packing_slip_date'])); ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top">Diapatch Order No : </td>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $dispatch_data_d['dispatch_no'] ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Dispatch Order Date : </td>
							<td class="no-border-right no-border-bottom no-border-left">
								<?= date('d-m-Y', strtotime($dispatch_data_d['dispatch_date'])); ?></td>
						</tr>


						<?php
						// This is a single-line comment
						if($invoice_no != "")
						{
							?>
							<tr>
							<td class="no-border-right no-border-bottom no-border-left">Invoice No: </td>
							<td class="no-border-right no-border-bottom no-border-left">
								<?= $invoice_no ?></td>
							</tr>
							<?php
						}
						// This is a single-line comment

						?>

						
						
						<tr>
							<td class="no-border-right no-border-bottom no-border-left">Transporter Detail : </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= $db->rp_getValue("transport_master","name","isDelete=0 AND id='".$dispatch_data_d['transport_name']."' ") ?></td>
						</tr>
						
					</table>
					
			</td>
		</tr>
		<tr>
			<td colspan="16" class="no-border-top height-5"></td>
		</tr>


</tbody>
</table>
<table>
	<tbody>	
		<!-- <tr>
			<th class="text-center" colspan="16" style="font-size: large;">
				Client Name : <?= $db->rp_getValue("executive","cname","isDelete=0 AND id='".$cart_detail_d['customer_id']."' ")  ?>
			</th>
		</tr> -->
		<tr style="background: #eee!important">
			<th colspan="1" style="width: 10%">Sr. No.</th>
			<th colspan="13">Item</th>
			<th colspan="1" style="width: 10%" class="text-center">Qty</th> <!-- 25 -->
			<th colspan="1" style="width: 10%" class="text-center">KGs</th> <!-- 25 -->
		</tr>
		<?php
		$MAINTOTALQTY = 0;
		$MAINTOTALWEIGHT = 0;
		$main_carton_type_count = 0;
		// $MAINTOTALQTY = 0;
		// $MAINTOTALWEIGHT = 0;
		foreach ($getPackingSplipItemData as $item_key => $item_value) 
		{
		?>
		<tr>
			<td colspan="16">
				<strong><?=$item_value['main_carton_type_name']?> No : </b><?=$item_value['main_carton_type_count']?></strong>
			</td>
		</tr>
		<?php
		$getPackingSplipItemDataFullR = $db->rp_getData("packing_slip_item","*","isDelete = 0 AND isActive = 1 AND packing_slip_id = '".$_REQUEST['id']."' AND main_carton_type_count='".$item_value['main_carton_type_count']."'");
		$getPackingSplipItemDataFull = array();
		while($getPackingSplipItemDataFullComp = mysqli_fetch_assoc($getPackingSplipItemDataFullR))
		{
			$getPackingSplipItemDataFull[] = $getPackingSplipItemDataFullComp;
		}
		$count = 1;
		$TOTALQTY = 0;
		$TOTALWEIGHT = 0;
		$count_no = 1;
		foreach ($getPackingSplipItemDataFull as $item_full_key => $item_full_value) {
				$TOTALQTY += $item_full_value['pro_qty'];
				$TOTALWEIGHT += $item_full_value['pro_weight'];
				$proname = $db->rp_getValue("product","name","id='".$item_full_value['pro_id']."'",0);
				$cat_no  = $db->rp_getValue("product_weight_price","catno","product_id='".$item_full_value['pro_id']."'",0);

		?>
		<tr>
			<td colspan="1" class="text-center"><?=$count_no?></td>
			<!-- <td colspan="13"><?=$item_full_value['pro_name'];?></td> -->
			<td colspan="13"><?=$proname?></td>
			<td colspan="1" class="text-center"><?=$item_full_value['pro_qty']?></td>
			<td colspan="1" class="text-right"><?=$item_full_value['pro_weight'];?></td>
		</tr>
		<?php
		$TOTALQTY += 0;

		
		$count_no = $count_no+1;
		}
		$TOTALWEIGHT += $item_full_value['main_carton_type_weight'];
		$MAINTOTALQTY += $TOTALQTY;
		$MAINTOTALWEIGHT += $TOTALWEIGHT;
		?>
		<tr>
			<td colspan="14" class="text-right"><b><?=$item_value['main_carton_type_name']?> Weight</b></td>
			<td class="text-center">&nbsp;</td>
			<td class="text-right"><?=$item_value['main_carton_type_weight']?></td>
		</tr>
		<tr>
			<td colspan="14" class="text-right"><b>Total</b></td>
			<td class="text-center"><?=$TOTALQTY?></td>
			<td class="text-right"><?=$TOTALWEIGHT?></td>
		</tr>
		<tr>
			<td colspan="14" class="text-right"><b>Actual Weight</b></td>
			<td class="text-center">&nbsp;</td>
			<td class="text-right"><?=$item_value['main_carton_whole_actual_weight']?></td>
		<?php
		}
		?>
		<tr class="remove-this-before-save-click" style="background: #eee!important">
			<td colspan="14" class="text-right">
				<b>Grand Total</b>
			</td>
			<td class="text-center">
				<?=$MAINTOTALQTY?>
			</td>
			<td class="text-right">
				<?=$MAINTOTALWEIGHT?>
			</td>
		</tr>
	</tbody>
</table>
<br><br>
<table>
	<tr style="background: #eee!important">
		<th colspan="1" style="width: 10%">Sr. No.</th>
		<th colspan="13">Item</th>
		<th colspan="1" style="width: 10%" class="text-center">Qty</th> <!-- 25 -->
		<th colspan="1" style="width: 10%" class="text-center">KGs</th> <!-- 25 -->
	</tr>
	<?php
	$getPackingSplipItemDataR = $db->rp_getData("packing_slip_item","*,SUM(pro_qty) AS sum_pro_qty,SUM(pro_weight) AS sum_pro_weight","isDelete = 0 AND isActive = 1 AND packing_slip_id = '".$_REQUEST['id']."' GROUP BY pro_id","",0);
	$getPackingSplipItemData = array();
	while($getPackingSplipItemDataComp = mysqli_fetch_assoc($getPackingSplipItemDataR))
	{
		$getPackingSplipItemData[] = $getPackingSplipItemDataComp;
	}

		$MAINTOTALQTY = 0;
		$MAINTOTALWEIGHT = 0;
		$count_no = 0;
		foreach ($getPackingSplipItemData as $item_key => $item_value) 
		{
			$itemQty = $item_value['sum_pro_qty'];
			$itemWeight = $item_value['sum_pro_weight'];
			$proname = $db->rp_getValue("product","name","id='".$item_value['pro_id']."'",0);
			$cat_no  = $db->rp_getValue("product_weight_price","catno","product_id='".$item_value['pro_id']."'",0);
	?>
	<?php
		
	?>
	<tr>
		<td colspan="1" class="text-center"><?=++$count_no?></td>
		<td colspan="13"><?=$proname." ".$cat_no;?></td>
		<td colspan="1" class="text-center"><?=$itemQty?></td>
		<td colspan="1" class="text-right"><?=$itemWeight;?></td>
	</tr>
	<?php
		$MAINTOTALQTY += $itemQty;
		$MAINTOTALWEIGHT += $itemWeight;
	}

	?>
	<tr class="remove-this-before-save-click" style="background: #eee!important">
		<td colspan="14" class="text-right">
			<b>Total</b>
		</td>
		<td class="text-center">
			<?=$MAINTOTALQTY?>
		</td>
		<td class="text-right">
			<?=$MAINTOTALWEIGHT?>
		</td>
	</tr>
	<tr>
			<td colspan="14">
				<strong>Terms & Condition : </strong><br/>
				<span style="font-size:13px;"><?= $cart_detail_d['terms_comdition'] ?></span>
			</td>
			<td colspan="2" class="text-center"><strong>For, <?php echo CLIENT_BRAND_NAME; ?> </strong>
				<br/><br/><br/><br/>
				(Authorised Signatory)
			</td>
		</tr>
</table>

