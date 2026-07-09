<?php
$page_id=566;$page_slug='page_order_ajax';
include("connect_in.php");
include("../include/no_to_word.php");
$invoice_id	= $_REQUEST['invoice_id'];
$cart_detail_r 	= $db->rp_getData("proforma_invoice_info","*","id='".$invoice_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Copy</title>
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
			
			<td style="border-left:1px solid #000000;width:50%;"><h4><strong>Invoice :</strong> #<?php echo $cart_detail_d['invoice_no']; ?></h4></td>
			<td style="border-left:1px solid #000000;width:50%;"><h4><strong>Invoice Date :</strong> <?php echo date_format(date_create($cart_detail_d['invoice_date']),'d-m-Y'); ?></h4></td>
		</tr>
	</table>
    <table class="heading" style="width:100%;" border="1">
       	<tr>
			<td style="width:50%;">
			<?php
			$pincode= stripslashes($cart_detail_d['zip']); 
					$city 	= stripslashes($cart_detail_d['city']); 
					$state 	= stripslashes($cart_detail_d['state']."");
					$country = stripslashes($cart_detail_d['country'].""); 
					if($city!="" || $pincode!="" || $state!="" || $country!="")
					{
						$loc = $city.", ".$state.", ".$country;
					}
					else
					{
						echo " ";
					}
			?>
			<strong>From:</strong><br>
			<strong>Name : </strong><?php echo stripslashes($cart_detail_d['customer_name']); ?>
			<h2 class="heading">
					<strong>Phone</strong>: <?php echo stripslashes($cart_detail_d['phone']); ?><br/>
					
					<strong>Email</strong>: <?php echo stripslashes($cart_detail_d['email']); ?>
				</h2>
				<h2 class="heading" style="padding-top:0;"><strong> Address:</strong><?php echo stripslashes($cart_detail_d['address'])."."; ?><br><?php echo str_replace(",,",",",$loc);?></h2>
				
			</td>
		</tr>
		
    </table>
    <div id="content">
         
         <div id="invoice_body">
            <table border="1">
            <tr style="background:#eee;">
                <td style="text-align:center;width:10%;" colspan="2"><strong>Product Name</strong></td>
				<td style="text-align:center;width:10%;"><strong>Qty</strong></td>
				<td style="text-align:center;width:10%;"><strong>Amount</strong></td>
				<td style="text-align:center;width:10%;"><strong>Sub Total</strong></td>
				<td style="text-align:center;width:10%;"><strong>Discount</strong></td>
				<td style="text-align:center;width:10%;"><strong>Discount Amount</strong></td>
				<td style="text-align:center;width:10%;"><strong>Taxable</strong></td>
				<?php
				if($cart_detail_d['state']=="Gujarat"){
				?>
				<td style="text-align:center;width:10%;"><strong>CGST</strong></td>
				<td style="text-align:center;width:10%;"><strong>SGST</strong></td>
				<?php
				}else{
					?>
					<td style="text-align:center;width:10%;" colspan="2"><strong>IGST</strong></td>
					<?php
				}
				?>
				
				<td style="text-align:center;width:10%;"><strong>Grand Total</strong></td>
            </tr>
            
			<?php
			$order_item_r = $db->rp_getData("proforma_invoice_item","*","proforma_invoice_id='".$invoice_id."'","",0);
			if(mysqli_num_rows($order_item_r)>0){
				
				while($order_item_d = mysqli_fetch_array($order_item_r)){
					?>
				<tr>
					<td style="text-align:center;width:10%;" colspan="2">
						<span class=""><?php echo $order_item_d['item_name']; ?></span>
					</td>
					<td style="width:10%;" class="rp_right">
						<?php echo $order_item_d['item_qty']; ?>
					</td>
					<td style="width:10%;"  class="rp_right">
						<?php echo $order_item_d['item_price']; ?>
					</td>					
					
					<td style="width:10%;" class="rp_right">
						<?php echo $order_item_d['subtotal']; ?> 
					</td>
					<td style="width:10%;"  class="rp_right">
						<?php echo $order_item_d['discount']; ?> 
					</td>
					<td style="width:10%;"  class="rp_right">
						<?php echo $order_item_d['discount_amount']; ?> 
					</td>
					<td style="width:10%;"  class="rp_right">
						<?php echo $order_item_d['taxable']; ?> 
					</td>
					<?php
					if($cart_detail_d['state']=="Gujarat"){
					?>
					<td style="width:10%;" class="rp_right">
						<?php echo $order_item_d['cgst_tax_amount']; ?> 
					</td>
					<td style="width:10%;"  class="rp_right">
						<?php echo $order_item_d['sgst_tax_amount']; ?> 
					</td>
					<?php
					}else{
						?>
						<td style="width:10%;"  class="rp_right" colspan="2">
							<?php echo $order_item_d['igst_tax_amount']; ?> 
						</td>
						<?php
					}
					?>
					<td style="width:10%;" class="rp_right">
						<?php echo $order_item_d['grandtotal']; ?> 
					</td>
				</tr>
				<?php
				
					}
				}
				
				?>
			
             
            <tr>
				<td style="text-align:center;width:10%;" colspan="2"><strong>Total</strong></td>
				<td style="width:10%;" class="rp_right"><?php echo $cart_detail_d['total_qty']; ?></td>
				<td style="width:10%;" class="rp_right"></td>
				<td style="width:10%;"  class="rp_right"><?php echo CURR.$db->rp_num($cart_detail_d['subtotal']); ?></td>
				<td style="width:10%;" class="rp_right"></td>
				<td style="width:10%;" class="rp_right"><?php echo CURR.$db->rp_num($cart_detail_d['discount']);?></td>
				<td style="width:10%;" class="rp_right"><?php echo CURR.$db->rp_num($cart_detail_d['taxable']);?></td>
				<?php
				if($cart_detail_d['state']=="Gujarat"){
				?>
				<td style="width:10%;" class="rp_right"><?php echo CURR.$db->rp_num($cart_detail_d['cgst_tax_amount']);?></td>
				<td style="width:10%;" class="rp_right"><?php echo CURR.$db->rp_num($cart_detail_d['sgst_tax_amount']);?></td>
				<?php
				}else{
					?>
				<td style="width:10%;"  class="rp_right" colspan="2"><?php echo CURR.$db->rp_num($cart_detail_d['igst_tax_amount']);?></td>	
					<?php
				}
				?>
				<td style="width:10%;"  class="rp_right"><?php echo CURR.$db->rp_num($cart_detail_d['grand_total']);?></td>
			</tr>
			<!--<tr>
				<td colspan="5" class="rp_right">Total :</td>
				<td style="width:10%;" class="rp_right"><?php echo $total_price; ?></td>
				
			</tr-->
			
			
			<?php
			$grand_total=$db->rp_getValue("orders","grand_total","id='".$order_id."'");
			$grand_total=$total_price-$discount;
			?>
			
        </table>
			<table width="40%" align="right" style="width:40%" border="1">
				<tr>
				<td class="rp_right"><strong>Total Qty</strong></td>
				<td class="rp_right"  align="right"><?php echo $cart_detail_d['total_qty']; ?></td>
				</tr>
				<tr>
				<td class="rp_right"><strong>Total Amount</strong></td>
				<td class="rp_right"  align="right"><?php echo CURR.$db->rp_num($cart_detail_d['subtotal']); ?></td>
				</tr>
				
			
				<tr>
			<?php
			$discount=$db->rp_getValue("orders","discount","id='".$order_id."'",0);
			$discount_type=$db->rp_getValue("orders","discount_type","id='".$order_id."'");
			?>
				<td class="rp_right" style="text-align:right;"> <strong>Discount </strong> <?php //if($discount_type==1){echo "(".$discount."%)";}?> </td>
				<td class="rp_right">
				<?php echo $cart_detail_d['discount'];
			?></td>
			</tr>			
				<?php
			//$grand_total=$db->rp_getValue("orders","grand_total","id='".$_REQUEST['id']."'");
			if($discount_type==1)
			{
				$grand_total=$total_price-$d;
			}
			else
			{
				$grand_total=$total_price-$discount;
			}
			
			?>
				<tr>
					<td class="rp_right"><strong>Grand Total</strong></td>
					
					<td class="rp_right"  align="right"><?php echo CURR.$db->rp_num($cart_detail_d['grand_total']); ?></td>
				</tr>			
					
			</table>
        </div>
        <div id="invoice_total" style="margin-top:1mm;">
            <table border="1">
                <tr>
                    <td style="padding-left:10px;text-align:left;">
						<strong>AMOUNT IN WORDS: </strong>
						<?php 
						$ntw = new NumToWord_RP;
						echo $ntw->rp_convertNumToWord($cart_detail_d['grand_total']);
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
     
    <sethtmlpagefooter name="footer">
        <hr />
        <div id="footer"> 
            <table>
                <tr><td><strong>THIS IS A COMPUTER GENERATED INVOICE AND DOES NOT REQUIRE SIGNATURE</strong></td></tr>
            </table>
			
        </div>
		<div style="margin-left:0px;margin-bottom:0px;font-size:10px;!important">Downloaded on:<?php echo date('d-m-Y H:i:s');?></div>
    </sethtmlpagefooter>
    <sethtmlpagefooter name="footer" value="on" />
</center>     
</body>
</html>
