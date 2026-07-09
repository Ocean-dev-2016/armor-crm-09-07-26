<?php
$page_id=566;$page_slug='page_order_ajax';
include("connect_in.php");
include("../include/no_to_word.php");
$dispatch_id	= $_REQUEST['dispatch_id'];
$cart_detail_r 	= $db->rp_getData("dispatch_detail","*","id='".$dispatch_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);
$dispatch_id=$cart_detail_d['id'];
$dispatch_no=$cart_detail_d['dispatch_no'];
$sales_id=$cart_detail_d['sales_id'];
$customer_id=$cart_detail_d['customer_id'];
$orderstatus 	= $cart_detail_d['orderstatus'];
$payment_method = $cart_detail_d['payment_method'];
$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
$_SESSION[SITE_SESS.'_ADMIN_TYPE'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dispatch Copy</title>
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
			<td style="text-align:left;"><h4><strong>Dispatch</strong></h4></td>
		</tr>
	</table>
	<table class="heading" style="width:100%;border-top:1px solid #000000;border-right:1px solid #000000;border-left:1px solid #000000;">
		<tr>
			
			<td style="border-left:1px solid #000000;width:50%;"><h4><strong>Dispatch :</strong> #<?php echo $dispatch_no; ?></h4></td>
			<!--<td style="border-left:1px solid #000000;">
				<h4>
					<strong>
						<?php if($order_d['payment_method']==2){ ?>
						PREPAID
						<?php }else{ ?>
						COD
						<?php } ?>
					</strong>
				</h4>
			</td>-->
			<td style="border-left:1px solid #000000;width:50%;"><h4><strong>Dispatch Date :</strong> <?php echo date_format(date_create($order_d['dispatch_date']),'d-m-Y H:i'); ?></h4></td>
		</tr>
	</table>
    <table class="heading" style="width:100%;" border="1">
       	<tr>
			<!--td style="width:50%;">
				<!--<h2 class="heading"><strong>Seller Address</strong></h2>
				<h2 class="heading">
					Unique Solution,<br>
					11-12, 1st Floor, Sadguru Sandhya Aprt.<br>
					Satya Sai Main Road, Kalawad Road,<br>
					Rajkot-360005, Gujarat, India.<br>
					<strong>Email</strong>: care@mufat.in <br>
					<strong>GST (VAT) No.</strong> : 24091807029<br>
					<strong>CST No.</strong> : 24591807029
				</h2>-->
			<!--/td-->
			
			<td style="width:50%;">
			<?php
			$customer_Detail_d=$db->rp_getData("executive","*","id='".$customer_id."'","",0);
			$customer_Detail_r=mysqli_fetch_assoc($customer_Detail_d);
			$pincode= stripslashes($customer_Detail_r['zip']); 
					$city 	= stripslashes($db->rp_getValue("city","name","id=".$customer_Detail_r['city']."")); 
					$state 	= stripslashes($db->rp_getValue("state","name","id=".$cart_detail_d['state'].""));
					$country = stripslashes($db->rp_getValue("country","name","id=".$cart_detail_d['country']."")); 
					
						$loc = CLIENT_CITY." - ".CLIENT_PINCODE.", ".CLIENT_STATE.", ".CLIENT_COUNTRY;
					
			?>
			<strong>From:</strong><br>
			<strong>Name : </strong><?php echo CLIENT_NAME; ?>
			<h2 class="heading">
					<strong>Phone</strong>: <?php echo stripslashes($cart_detail_d['contact_number']); ?><br/>
					
					<strong>Email</strong>: <?php echo CLIENT_EMAIL; ?>
				</h2>
				<h2 class="heading" style="padding-top:0;"><strong> Address:</strong><?php echo CLIENT_ADDRESS."."; ?><br><?php echo str_replace(",,",",",$loc);?></h2>
				
			</td>
			<?php
			
			$sales_id=$cart_detail_d['sales_id'];
			?>
			<td style="width:50%;">
			<?php
			$pincode=$db->rp_getValue("sales_executive","zip","id=".$sales_id.""); 
					
					if($city!="" || $pincode!="" || $state!="" || $country!="")
					{
						$loc = $city." - ".$pincode.", ".$state.", ".$country;
					}
					else
					{
						echo " ";
					}
			?>
			<strong>To,</strong><br>
			<strong>Name : </strong><?php echo stripslashes($cart_detail_d['customer_name']); ?>
			<h2 class="heading">
					<strong>Phone</strong>: <?php echo stripslashes($cart_detail_d['contact_number']); ?><br/>
					
					<strong>Email</strong>: <?php echo stripslashes($cart_detail_d['email']); ?>
				</h2>
				<h2 class="heading" style="padding-top:0;"><strong> Address:</strong><?php echo stripslashes($cart_detail_d['address'])."."; ?><br><?php echo str_replace(",,",",",$loc);?></h2>
			</td>
		</tr>
		<tr>
    </table>
     
    <div id="content">
         
         <div id="invoice_body">
            <table border="1">
            <tr style="background:#eee;">
                <td style="text-align:center;width:10%;" colspan="3"><strong>Product Name</strong></td>
				<td style="text-align:center;width:10%;"><strong>Dispatch Qty</strong></td>
				<!--td style="text-align:center;width:10%;"><strong>Remaining Qty</strong></td-->
				<td style="text-align:center;width:10%;"><strong>Amount</strong></td>
				<td style="text-align:center;width:10%;"><strong>Sub Total</strong></td>
				<td style="text-align:center;width:10%;"><strong>Discount</strong></td>
				<td style="text-align:center;width:10%;"><strong>Taxable</strong></td>
				<?php
				if($cart_detail_d['state']=="Gujarat"){
				?>
				<td style="text-align:center;width:10%;"><strong>CGST Tax</strong></td>
				<td style="text-align:center;width:10%;"><strong>SGST Tax</strong></td>
				<?php
				}else{
				?>
				<td style="text-align:center;width:10%;" colspan="2"><strong>IGST Tax</strong></td>
				<?php
				}
				?>
				
				<td style="text-align:center;width:10%;"><strong>Total</strong></td>
            </tr>
            </table>
             
            <table border="1">
            
			<?php
			$dispatch_item_r = $db->rp_getData("dispatch_item","*","dispatch_id='".$dispatch_id."'","",0);
			if(mysqli_num_rows($dispatch_item_r)>0){
				
				while($dispatch_item_d = mysqli_fetch_array($dispatch_item_r)){
					$id 		= $dispatch_item_d['id'];
					$order_id		= $dispatch_item_d['order_id'];
					$pro_id		= $dispatch_item_d['pro_id'];
					$weight_id		= $dispatch_item_d['weight_id'];
					$pro_name 	= stripslashes($dispatch_item_d['pro_name']);
					$qty 		= $dispatch_item_d['qty'];
					$pro_price 		= $db->rp_num($dispatch_item_d['unitprice']);
					$dispatched_qty 		= $dispatch_item_d['dispatched_qty'];
					$totalprice = $db->rp_num($dispatch_item_d['amount']);
					
					
					
					?>
				<tr>
					<td style="text-align:center;width:10%;" colspan="3">
						<span class=""><?php echo $pro_name; ?></span>
					</td>
					<td style="text-align:center;width:10%;" align="right">
						<?php echo $qty; ?>
					</td>
					<td style="text-align:center;width:10%;" align="right">
						<?php echo CURR.$pro_price; ?>
					</td>					
					
					<td style="text-align:center;width:10%;" align="right">
						<?php echo CURR.$totalprice; ?> 
					</td>
					<td style="text-align:center;width:10%;" align="right">
						<?php echo $dispatch_item_d['discount']; ?> 
					</td>
					<td style="text-align:center;width:10%;" align="right">
						<?php echo $dispatch_item_d['taxable']; ?> 
					</td>
					<?php
					if($cart_detail_d['state']=="Gujarat"){
					?>
					<td style="text-align:center;width:10%;" align="right">
						<?php echo $dispatch_item_d['cgst_tax_amount']." (".$dispatch_item_d['cgst_tax']."%)"; ?> 
					</td>
					<td style="text-align:center;width:10%;" align="right">
						<?php echo $dispatch_item_d['sgst_tax_amount']." (".$dispatch_item_d['sgst_tax']."%)"; ?>  
					</td>
					<?php
					}else{
					?>
					<td style="text-align:center;width:10%;" align="right" colspan="2">
						<?php echo $dispatch_item_d['igst_tax_amount']." (".$dispatch_item_d['igst_tax']."%)"; ?> 
					</td>
					<?php
					}
					?>
					<td style="text-align:center;width:10%;" align="right">
						<?php echo $dispatch_item_d['grand_total']; ?> 
					</td>
				</tr>
				<?php
				$total_pro_qty+=$qty;
				$total_pro_price+=$pro_price;
				$total_price+=$totalprice;
				$total_dispatched_qty+=$dispatched_qty_qty;
				$total_remaining_qty+=$remaining_qty;
				$final_grandtotal+=$dispatch_item_d['grand_total'];
				$final_taxable+=$dispatch_item_d['taxable'];
				$final_discount_amount+=$dispatch_item_d['discount_amount'];
					}
				}
				
				?>
			  
            <tr>
                <td colspan="5"></td>
            </tr>
             
            <tr>
				<td style="text-align:center;width:10%;" colspan="3"><strong>Total</strong></td>
				<td style="text-align:center;width:10%;" align="right"><?php echo $total_pro_qty; ?></td>
				<!--td style="text-align:center;width:10%;" align="right"><?php// echo $total_remaining_qty; ?></td-->
				<td style="text-align:center;width:10%;" align="right"><?php echo CURR.$db->rp_num($total_pro_price); ?></td>
				<td style="text-align:center;width:10%;"  align="right"><?php echo CURR.$db->rp_num($total_price);?></td>
				<td style="text-align:center;width:10%;"  align="right"></td>
				<td style="text-align:center;width:10%;"  align="right"><?php echo CURR.$db->rp_num($final_taxable);?></td>
				<td style="text-align:center;width:10%;"  align="right" colspan="2"></td>
				<td style="text-align:center;width:10%;"  align="right"><?php echo CURR.$db->rp_num($final_grandtotal);?></td>
			</tr>
			<!--<tr>
				<td colspan="5" class="rp_right">Total :</td>
				<td style="width:10%;" class="rp_right"><?php echo $total_price; ?></td>
				
			</tr-->
			
			
			
        </table>
			
        </div>
        <div id="invoice_total" style="margin-top:1mm;">
            <table border="1">
                <tr>
                    <td style="padding-left:10px;text-align:left;">
						<strong>AMOUNT IN WORDS: </strong>
						<?php 
						$ntw = new NumToWord_RP;
						echo $ntw->rp_convertNumToWord($total_price);
						?>
					</td>
                </tr>
				<tr>
                    <td style="padding-left:10px;text-align:left;">
						<strong>DECLARATION: </strong>
						We  declare  that  this  invoice  shows  the  actual  price  of  the  goods  described  inclusive  of  taxes  and  that  all  particulars  are  true  and correct. 
						If you find selling price on this invoice to be more than MRP mentioned on the product, please inform us at <?php echo SITEURL."contact-us/" ?> 
						Goods sold as part of this invoice are intended for end user consumption/retail sale and not for re-sale.
					</td>
                </tr>
				<tr>
                    <td style="padding-left:10px;text-align:left;">
						<strong>CUSTOMER ACKNOWLEDGEMENT: </strong>
						I  <u><?php echo stripslashes($cart_detail_d['name']); ?></u>  confirm  that  the  said  products  are  being  purchased  for  my  internal/personal  consumption  and  not  for  re-sale.  I further understand and agree with <?php echo SITENAME;?> terms and conditions for sale.
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
		<div style="margin-left:0px;margin-bottom:0px;font-size:10px;!important">Downloaded on:<?php echo date('d-m-Y H:i:s');?></div>
    </htmlpagefooter>
    <sethtmlpagefooter name="footer" value="on" />
</center>     
</body>
</html>
