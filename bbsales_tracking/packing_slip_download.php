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
		<tr>
			<td colspan="16">
						<!-- <img style="width: 100%;padding: 0px !important;"  src="<?= VIEW_LOGO_All ?>"> -->
						<div style="position: absolute; width: 96%; padding-left:5px; padding-right:10px; padding-top:5px;" >
							<img   src="<?= VIEW_LOGO_All ?>">
						</div>
					</td>
		</tr>
		<br><br><br><br><br><br><br><br><br><br><br><br>

		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5 text-center"><strong>GST - <?= COMPANY_GST ?></strong></td>
		</tr>

		<tr>

			<td colspan="6" class="no-border-right no-border-bottom bg-gray" ></td>
			<td colspan="4" class="text-center no-border-right no-border-bottom" style="background-color: <?= VIEW_COLOR ?>" ><b>Packing Slip</b></td>
			<td colspan="6" class="no-border-bottom bg-gray" ></td>
		</tr>
		
		<tr>
			<td colspan="16" class="no-border-bottom no-border-top height-5"></td>
		</tr>
		
		<tr class="vertical-top">
			<td colspan="6" class="no-border-bottom no-border-top border-r-width border-blue vertical-top" >
				<strong>To,<br/></strong>
				<?php
				$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0","",0);
				$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
				?>
				<strong><?php echo $Distri_data_a['company_name']; ?></strong><br/><span>
				<?php echo  wordwrap($Distri_data_a['address'],40,"<br>\n")."  <br/>".$Distri_data_a['city']." - ".$Distri_data_a['zip']." , ".$Distri_data_a['state']." , ".$Distri_data_a['country'] ?></span><br/><br>
				<span><b>Contact Person :</b> <?=$Distri_data_a['cname']?></span><br>
				<span><b>Contact Number :</b> <?=$Distri_data_a['phone']?></span><br>
				<span><b>Email :</b> <?=$Distri_data_a['email']?></span><br/>
				<span><b>State of Supply :</b> <?=$Distri_data_a['state']?></span><br/>
				<strong><span>GST No : <?=$Distri_data_a['gst']?></span></strong><br/>
				<!-- <span>Vendor Code : <?= $cart_detail_d['vendor_code'] ?></span><br/> -->
				<!-- <span>Mobile : <?=$Distri_data_a['phone']?></span><br/> -->
				<!-- <span>Date:- :</span><br/> -->
			</td>
			<!-- <div class="vertical_line"></div> -->
			<td colspan="4"  class="no-border-bottom no-border-top border-r-width border-blue vertical-top" >
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
			<td colspan="6"  class="no-border-bottom no-border-top vertical-top">
				<strong>Details<br/></strong>
					<!-- <table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 0mm!important;"> -->
					<table class="no-border-top no-border-bottom no-border-right no-border-left" style="width: 100% !important;">
						<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><b>Packing Slip No :</b> </td>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $cart_detail_d['packing_slip_no'] ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left"><b>Packing Slip Date :</b> </td>
							<td class="no-border-right no-border-bottom no-border-left"><?= date('d-m-Y', strtotime($cart_detail_d['packing_slip_date'])); ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><b>Diapatch Order No :</b> </td>
							<td class="no-border-right no-border-bottom no-border-left no-border-top"><?= $dispatch_data_d['dispatch_no'] ?></td>
						</tr>
						<tr>
							<td class="no-border-right no-border-bottom no-border-left"><b>Dispatch Order Date :</b> </td>
							<td class="no-border-right no-border-bottom no-border-left">
								<?= date('d-m-Y', strtotime($dispatch_data_d['dispatch_date'])); ?></td>
						</tr>


						<?php
						// This is a single-line comment
						if($invoice_no != "")
						{
							?>
							<tr>
							<td class="no-border-right no-border-bottom no-border-left"><b>Invoice No:</b> </td>
							<td class="no-border-right no-border-bottom no-border-left">
								<?= $invoice_no ?></td>
							</tr>
							<?php
						}
						// This is a single-line comment

						?>

						
						
						<tr>
							<td class="no-border-right no-border-bottom no-border-left"><b>Transporter Detail :</b> </td>
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
		<tr style="background: #eee!important">
			<th colspan="1" style="width: 10%">Sr. No.</th>
			<th colspan="10">Item Name</th>
			<th colspan="1">Parcel No</th>
			<th colspan="1" style="width: 10%" class="text-center">Qty</th> <!-- 25 -->
			<th colspan="1" style="width: 10%" class="text-center">Unit</th> <!-- 25 -->
			<th colspan="1" style="width: 10%" class="text-center">Net Weight</th> <!-- 25 -->
			<th colspan="1" style="width: 10%" class="text-center">Gross Weight</th> <!-- 25 -->
		</tr>

		<?php
		$ITEMS=array();
		$items1 = $db->rp_getData("packing_slip_item","*","isDelete = 0 AND isActive = 1 AND packing_slip_id = '".$_REQUEST['id']."'","",0);
		//$items1 = $db->rp_getData("packing_slip_item","*,SUM(pro_qty) AS sum_pro_qty,SUM(pro_weight) AS sum_pro_weight","isDelete = 0 AND isActive = 1 AND packing_slip_id = '".$_REQUEST['id']."' GROUP By pro_id","",0);
		while ($item1=mysqli_fetch_assoc($items1))
		{
			$ITEMS[]=$item1;
		}
		?>
		<?php
		if($items1)
		{
			$count=0;
			$MAINTOTALQTY = 0;
			$MAINTOTALWEIGHT = 0;
			$MAINTOTALACTUALWEIGHT = 0;
			foreach($ITEMS as $item)
			{
				$count++;

				$dispatch_id = $db->rp_getValue("packing_slip","dispatch_id","id='".$_REQUEST['id']."' AND isDelete=0");
				$dispatch_inner_size = $db->rp_getValue("dispatch_item","inner_size","dispatch_id='".$dispatch_id."' AND isDelete=0");
				$dispatch_outre_size = $db->rp_getValue("dispatch_item","outer_size","dispatch_id='".$dispatch_id."' AND isDelete=0");

				$proname = $db->rp_getValue("product","name","id='".$item['pro_id']."' AND isDelete=0");
				$procode=$db->rp_getValue("product_weight_price","catno","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'",0);
				$procode=$db->rp_getValue("product_weight_price","catno","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'",0);
				$size_inner=$db->rp_getValue("product_weight_price","size_inner","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'",0);
				$inner_cft=$db->rp_getValue("product_weight_price","inner_cft","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'",0);
				$size_outer=$db->rp_getValue("product_weight_price","size_outer","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'",0);
				$outer_cft=$db->rp_getValue("product_weight_price","outer_cft","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'",0);

				if($item['item_size_cft']!="")
				{
					$proname_new = $proname ." / CODE ". $procode ." / ". $item['item_size_cft'];
				}
				else if($dispatch_inner_size==1)
				{
					$proname_new = $proname ." / CODE ". $procode ." / SIZE". $size_inner ." / CFT ". $inner_cft;
				}
				else
				{
					$proname_new = $proname ." / CODE ". $procode ." / SIZE". $size_outer ." / CFT ". $outer_cft;

				}

				$unit_id = $db->rp_getValue("product","display_unit","id='".$item['pro_id']."' AND isDelete=0");
				$unit_name = $db->rp_getValue("unit","name","id='".$unit_id."' AND isDelete=0");
				?>
				<tr>
					<td colspan="1" class="text-center"><?=$count?></td>
					<td colspan="10"><?=$proname_new?></td>
					<td colspan="1" class="text-right"><?=$item['main_carton_type_count']?></td>
					<td colspan="1" class="text-right"><?=$item['pro_qty']?></td>
					<td colspan="1" class="text-center"><?=$unit_name?></td>
					<td colspan="1" class="text-right"><?=round($item['pro_weight'],2);?></td>
					<td colspan="1" class="text-right"><?=$item['main_carton_whole_actual_weight'];?></td>
				</tr>
				<?php

				$MAINTOTALCARTON += $item['main_carton_type_count'];
				$MAINTOTALQTY += $item['pro_qty'];
				$MAINTOTALWEIGHT += $item['pro_weight'];
				$MAINTOTALACTUALWEIGHT += $item['main_carton_whole_actual_weight'];
			}
		}
		?>
		<tr>
					<td colspan="1" class="text-center"></td>
					<td colspan="10" class="text-right" style="font-weight:700">Total</td>
					<td colspan="1" class="text-right" style="font-weight:700"><?= $MAINTOTALCARTON ?></td>
					<td colspan="1" class="text-right" style="font-weight:700"><?=$MAINTOTALQTY?></td>
					<td colspan="1"></td>
					<td colspan="1" class="text-right" style="font-weight:700"><?=round($MAINTOTALWEIGHT,2);?></td>
					<td colspan="1" class="text-right" style="font-weight:700"><?=$MAINTOTALACTUALWEIGHT;?></td>
				</tr>
		<tr>
			<td colspan="6" rowspan="4" class="no-border-right">
				<br><br><br><br>
				<strong style="margin-right: 36px;">Prepared By</strong>
			</td>
			<td colspan="6" rowspan="4" class="no-border-right">
				<br><br><br><br>
				<center><strong>Checked By</strong></center>
			</td>
			<td colspan="4" class="text-right"><strong>For, <?php echo CLIENT_BRAND_NAME; ?> </strong>
				<br/><br/><br/><br/>
				(Authorised Signatory)
			</td>
		</tr>
</table>

