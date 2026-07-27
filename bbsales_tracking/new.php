<?php
$page_id=566;$page_slug='page_order_ajax';
include("connect_in.php");
include("../include/no_to_word.php");
$order_id	= $_REQUEST['order_id'];
$cart_detail_r 	= $db->rp_getData("orders","*","id='".$order_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);
$order_no=$cart_detail_d['order_no'];
//$sales_id=$cart_detail_d['sales_id'];
$orderstatus 	= $cart_detail_d['orderstatus'];
$payment_method = $cart_detail_d['payment_method'];
$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
$_SESSION[SITE_SESS.'_ADMIN_TYPE'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Print Invoice</title>
    <style>
        *
        {
            margin:0;
            padding:0;
            font-family:Arial;
            font-size:10pt;
            color:#000;
        }
        body
        {
            width:100%;
            font-family:Arial;
            font-size:10pt;
            margin:0;
            padding:0;
        }
         
        p
        {
            margin:0;
            padding:0;
        }
         
        #wrapper
        {
            width:180mm;
            margin:0 15mm;
        }
         
        .page
        {
            height:297mm;
            width:210mm;
            page-break-after:always;
        }
 
        table
        {
            /*border-left: 1px solid #000000;
            border-top: 1px solid #000000;*/
             
            border-spacing:0;
            border-collapse: collapse; 
             
        }
         
        table td 
        {
           /* border-right: 1px solid #000000;
            border-bottom: 1px solid #000000;*/
            padding: 2mm;
        }
         
        table.heading
        {
           /* height:50mm;*/
        }
         
        h1.heading
        {
            font-size:14pt;
            color:#000;
            font-weight:normal;
        }
         
        h2.heading
        {
            font-size:9pt;
            color:#000;
            font-weight:normal;
        }
         
        hr
        {
            color:#ccc;
            background:#ccc;
        }
         
        #invoice_body
        {
            margin-bottom:2mm;
        }
         
        #invoice_body , #invoice_total
        {   
            width:100%;
        }
        #invoice_body table , #invoice_total table
        {
            width:100%;
           /* border-top: 1px solid #000000;*/
     
            border-spacing:0;
            border-collapse: collapse; 
             
            margin-top:5mm;
        }
         
        #invoice_body table td , #invoice_total table td
        {
            text-align:center;
            font-size:9pt;
            /*border-right: 1px solid #000000;
            border-bottom: 1px solid #000000;*/
            padding:2mm 0;
			
        }
         
        #invoice_body table td.rp_right  , #invoice_total table td.rp_right
        {
            text-align:right;
            padding-right:2mm;
            font-size:9pt;
        }
         
        #footer
        {   
            width:185mm;
            margin:0 15mm;
            padding-bottom:3mm;
        }
        #footer table
        {
            width:100%;
            border-left: 1px solid #000000;
            border-top: 1px solid #000000;
             
            background:#eee;
             
            border-spacing:0;
            border-collapse: collapse; 
        }
        #footer table td
        {
            width:25%;
            text-align:center;
            font-size:9pt;
            border-right: 1px solid #000000;
            border-bottom: 1px solid #000000;
        }
		.lineThrClass{
		text-decoration:line-through;
	}
    </style>
</head>
<body>
<center>
	<div id="wrapper" style="border:1px solid #CCC;padding:2mm;">
     
    <table class="heading" style="width:100%;border-top:1px solid #000000;border-right:1px solid #000000;border-left:1px solid #000000;">
		<tr>
			<td><img src="assets/admin/layout/img/logo-keshav.png" width="100"></td>
			<td style="text-align:left;"><h4><strong>Invoice</strong></h4></td>
		</tr>
	</table>
	<table class="heading" style="width:100%;border-top:1px solid #000000;border-right:1px solid #000000;border-left:1px solid #000000;">
		<tr>
			<td><h4><strong>Invoice :</strong> #<?php echo $invoice_id; ?></h4></td>
			<td style="border-left:1px solid #000000;"><h4><strong>Order :</strong> #<?php echo $order_no; ?></h4></td>
			<td style="border-left:1px solid #000000;">
				<h4>
					<strong>
						<?php if($cart_detail_d['payment_method']==2){ ?>
						PREPAID
						<?php }else{ ?>
						COD
						<?php } ?>
					</strong>
				</h4>
			</td>
			<td style="border-left:1px solid #000000;"><h4><strong>Order Date :</strong> <?php echo date_format(date_create($cart_detail_d['order_date']),'d-m-Y'); ?></h4></td>
		</tr>
	</table>
    <table class="heading" style="width:100%;" border="1">
       	<tr>
			<td style="width:50%;">
				<h2 class="heading"><strong>Address</strong></h2>
				<h2 class="heading">
				<?php
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
				{
				?>
					Ocean Infotech,<br>
					11-12, 4th Floor, Royal Complex.<br>
					Satya Sai Main Road, Kalawad Road,<br>
					Rajkot-360005, Gujarat, India.<br>
					<strong>Email</strong>: care@craftbox.in <br>
					<strong>GST (VAT) No.</strong> : 24091807029<br>
					<strong>CST No.</strong> : 24591807029
				<?php
				}
				else if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
				{
					$id=$db->rp_getData("executive","*","id=".$cart_detail_d['sales_id']."","",0);
					$main_data=mysqli_fetch_assoc($id);
					
					$ss_id=$main_data['super_stockist_id'];
					$ss_info=$db->rp_getData("executive","*","id=".$ss_id."");
					$data=mysqli_fetch_assoc($ss_info);
					$ss_address=$data['address'];
					$ss_email=$data['email'];
					$ss_cst=$data['cst'];
					$ss_gst=$data['gst'];
					$discount=$data['discount'];
				?>
				<br>
					<?php echo $ss_address;?>
					<strong>Email</strong>: <?php echo $ss_email;?> <br>
					<strong>GST (VAT) No.</strong> : <?php echo $ss_cst;?><br>
					<strong>CST No.</strong> :<?php echo $ss_gst;?>
				<?php
				}
				
					$phone=$main_data['phone'];
					$city=$db->rp_getValue("city","name","id=".$cart_detail_d['city']."");
					$state=$db->rp_getValue("state","name","id=".$cart_detail_d['state']."");
					$country=$db->rp_getValue("country","name","id=".$cart_detail_d['country']."");
					$email=$main_data['email'];
					$address=$main_data['address'];
					$zip=$main_data['zip'];

				?>
				</h2>
			</td>
			<td style="width:50%;">
				<h2 class="heading" style="padding-top:0;"><strong>Seller  Address</strong></h2>
				<h2 class="heading">
					<strong><?php echo stripslashes($main_data['cname']); ?></strong><br>
					<?php echo $address; ?><br>
					<?php 
					$pincode=$zip; 
					
					$loc = $city." - ".$pincode.", ".$state.", ".$country;
					echo str_replace(",,",",",$loc);
					?>
					<br>
					<strong>Phone</strong>: <?php echo $main_data['phone']; ?><br/>
					<strong>Email</strong>: <?php echo $email; ?>
				</h2>
			</td>
		</tr>
    </table>
     
    <div id="content">
    <div id="content">
         
        <div id="invoice_body">
            <table border="1">
            <tr style="background:#eee;">
                <td style="text-align:center;width:48%;" c>Product Name</td>
				<td style="text-align:center;width:10%;">Qty</td>
				<td style="text-align:center;width:20%;">Price</td>
				<td style="text-align:center;width:10%;">Sub Total</td>
            </tr>
            </table>
             
            <table border="1">
            
			<?php
			$shop_cart_r = $db->rp_getData("order_product_item","*","order_id='".$order_id."'");
			if(mysqli_num_rows($shop_cart_r)>0){
				$discount 	= 0;
				$sub_total	= 0;
				$total_qty	=0;
				$total_price=0;
				$grand_total=0;
				while($shop_cart_d = mysqli_fetch_array($shop_cart_r)){
					$id 		= $shop_cart_d['id'];
					$pid 		= $shop_cart_d['pro_id'];
					$pro_name 	= stripslashes($shop_cart_d['pro_name']);
					$qty 		= $shop_cart_d['pro_qty'];
					$orderitemstatus = $shop_cart_d['status'];
					$unitprice 	= $db->rp_num($shop_cart_d['unitprice']);
					$pro_tax	= stripslashes($shop_cart_d["pro_tax"]);
					$totalprice = $db->rp_num($shop_cart_d['totalprice']);
					$total_qty+=$qty;
					$total_price+=$unitprice;
					if($subpid>0){
						$sub_pro_r = $db->rp_getData("sub_product","*","id='".$subpid."'");
						if(mysqli_num_rows($sub_pro_r)>0){
							$sub_pro_d 	= mysqli_fetch_array($sub_pro_r);
							$pro_sku	= stripslashes($sub_pro_d["sku"]);
						}
					}else{
						$pro_r = $db->rp_getData("product","*","id='".$pid."'");
						if(mysqli_num_rows($pro_r)>0){
							$pro_d 		= mysqli_fetch_array($pro_r);
							$pro_sku	= stripslashes($pro_d["sku"]);
						}
					}
					?>
				<tr>
					 <td style="text-align:center;width:48%;" colspan="3">
						<?php echo $pro_name; ?>
						<?php  //echo "<u>SKU</u> : ".$pro_sku; ?>
					</td>
					<td style="text-align:center;width:10%;" class="rp_right"><?php echo $qty; ?></td>
					<td style="text-align:center;width:20%;" class="rp_right"><?php echo CURR.$unitprice; ?></td>
					<td style="text-align:center;width:10%;" class="rp_right">
						<?php echo CURR.$totalprice; ?>
						<?php if($shop_cart_d['discount']>0){ ?>
						<br>
						-<?php echo CURR.$db->rp_num($shop_cart_d['discount']); ?>
						<?php } 
						
						if($orderstatus==2 || $orderstatus==3 || $orderstatus==4 || $orderstatus==5){
							if($orderitemstatus==2){
							
							}else if($orderitemstatus==5 || $orderitemstatus==0){
								$singleRefund = $db->rp_getSingleItemRefundAmount($cart_id,$id);
								$singleShippingRefund = $db->rp_getSingleItemShippingRefundAmount($cart_id,$id);
								$singleCODRefund = $db->rp_getSingleItemCODRefundAmount($cart_id,$id,$payment_method);
								$totalSingleRefund += $singleRefund;
								$totalSingleShippingRefund += $singleShippingRefund;
								$totalSingleCODRefund += $singleCODRefund;
							} 
						} 
						?>
					</td>
					<?php 
					 $grand_total+=$totalprice; ?>
				</tr>
				<?php
					}
				}
				
				$final_total = $db->rp_num(($sub_total + $shipping_charge + $tax) - $discount - $shipping_discount);
				?>
			  
            <tr>
                <td colspan="6"></td>
            </tr>
             
            <tr>
				<td colspan="3" style="text-align:center;">Total</td>
				<td style="text-align:center;width:5%;"><?php echo $total_qty; ?></td>
				<td style="text-align:center;width:5%;"><?php echo CURR.$total_price; ?></td>
				<td style="width:10%;" class="rp_right"><?php echo CURR.$grand_total; ?></td>
			</tr>
        </table>
			
			<table width="40%" align="right" style="width:40%" border="1">
				<?php
				if($discount>0){
				?>
				<tr>
					<td>Discount:</td>
					<td class="rp_right">-<?php echo CURR.$db->rp_num($discount); ?></td>
				</tr>
				<?php
				}
				?>
				<?php
				if($shipping_discount>0){
				?>
				<tr>
					<td>Shipping Discount:</td>
					<td class="rp_right">-<?php echo CURR.$db->rp_num($shipping_discount); ?></td>
				</tr>
				<?php
				}
				?>
				<?php 
				if($payment_method==1){ 
					$final_total= $db->rp_num($final_total+$cod_charge);
				?>
				<tr>
					<td>COD Handling Charge</td>
					<td class="rp_right"><?php echo CURR.$cod_charge; ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td>Grand Total:</td>
					<td class="rp_right"><?php echo CURR.$grand_total; ?></td>
				</tr>
				<?php if(($orderstatus==0 && $payment_method!=1) || $orderstatus==5){ ?>
				<tr>
					<td>Refundable Amount:</td>
					<td class="rp_right">-<?php echo CURR.$refundAmount; ?></td>
				</tr>
				<?php }else if($totalSingleRefund>0){ ?>
					<?php
					if(($orderstatus==0 && $payment_method==2) || $orderstatus==5){
					?>
					
					<?php } ?>
				<?php } ?>
			</table>
        </div>
        <div id="invoice_total" style="margin-top:1mm;">
            <table border="1">
                <tr>
                    <td style="padding-left:10px;text-align:left;">
						<strong>AMOUNT IN WORDS: </strong>
						<?php 
						$ntw = new NumToWord_RP;
						echo $ntw->rp_convertNumToWord($grand_total);
						?>
					</td>
                </tr>
				<tr>
                    <td style="padding-left:10px;text-align:left;">
						<strong>DECLARATION: </strong>
						We  declare  that  this  invoice  shows  the  actual  price  of  the  goods  described  inclusive  of  taxes  and  that  all  particulars  are  true  and correct. 
						If you find selling price on this invoice to be more than MRP mentioned on the product, please inform us at <?php echo SITETITLE." contact-us/" ?> 
						Goods sold as part of this invoice are intended for end user consumption/retail sale and not for re-sale.
					</td>
                </tr>
				<tr>
                    <td style="padding-left:10px;text-align:left;">
						<strong>CUSTOMER ACKNOWLEDGEMENT: </strong>
						I  <u><?php echo stripslashes($cart_detail_d['name']); ?></u>  confirm  that  the  said  products  are  being  purchased  for  my  internal/personal  consumption  and  not  for  re-sale.  I further understand and agree with mufat.in terms and conditions for sale.
					</td>
                </tr>
            </table>
        </div>
    </div>
     
      
    </div>
     
    <htmlpagefooter name="footer">
        <hr />
        <div id="footer"> 
            <table>
                <tr><td><strong>THIS IS A COMPUTER GENERATED INVOICE AND DOES NOT REQUIRE SIGNATURE</strong></td></tr>
            </table>
        </div>
    </htmlpagefooter>
    <sethtmlpagefooter name="footer" value="on" />
</center>     
</body>
</html>