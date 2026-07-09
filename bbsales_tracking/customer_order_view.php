<?php
$page_id=566;$page_slug='page_order_ajax';
require_once("connect_in.php");
require_once("../include/no_to_word.php");
$request_id	= $_REQUEST['request_id'];
$cart_detail_r 	= $db->rp_getData("customer_order_request_info","*","id='".$request_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);
$request_no=$cart_detail_d['request_no'];
//$sales_id=$cart_detail_d['sales_id'];
$orderstatus 	= $cart_detail_d['orderstatus'];
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
            width:220mm; 
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
			<td><img src="../images/logo.png" width="100"></td>
			<td style="text-align:left;"><h4><strong>Customer Order Request</strong></h4></td>
		</tr>
	</table>
	<table class="heading" style="width:100%;border-top:1px solid #000000;border-right:1px solid #000000;border-left:1px solid #000000;">
		<tr>
			
			<td style="border-left:1px solid #000000;width:50%;"><h4><strong>Request No :</strong> #<?php echo $request_no; ?></h4></td>
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
			<td style="border-left:1px solid #000000;width:50%;"><h4><strong>Order Date :</strong> <?php echo date_format(date_create($order_d['orderdate']),'d-m-Y H:i'); ?></h4></td>
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
			$pincode= stripslashes($cart_detail_d['zip']); 
					$city 	= stripslashes($db->rp_getValue("city","name","id=".$cart_detail_d['city']."")); 
					$state 	= stripslashes($db->rp_getValue("state","name","id=".$cart_detail_d['state'].""));
					$country = stripslashes($db->rp_getValue("country","name","id=".$cart_detail_d['country']."")); 
					if($city!="" || $pincode!="" || $state!="" || $country!="")
					{
						$loc = $city." - ".$pincode.", ".$state.", ".$country;
					}
					else
					{
						echo " ";
					}
			?>
			<strong>From</strong><br>
			<strong>Name : </strong><?php echo stripslashes($cart_detail_d['company_name']); ?>
			<h2 class="heading">
					<strong>Phone</strong>: <?php echo stripslashes($cart_detail_d['phone']); ?><br/>
					
					<strong>Email</strong>: <?php echo stripslashes($cart_detail_d['email']); ?>
				</h2>
				<h2 class="heading" style="padding-top:0;"><strong> Address:</strong><?php echo stripslashes($cart_detail_d['address'])."."; ?><br><?php echo str_replace(",,",",",$loc);?></h2>
				
			</td>
			<?php
			if($cart_detail_d['customer_type']=='outlets')
			{
			$sales_id=$cart_detail_d['sales_id'];
			$sales_address=$db->rp_getValue("sales_executive","address","id=".$sales_id."");
			$sales_phone=$db->rp_getValue("sales_executive","phone","id=".$sales_id."");
			$sales_city=$db->rp_getValue("sales_executive","city","id=".$sales_id."");
			$sales_country=$db->rp_getValue("sales_executive","country","id=".$sales_id."");
			$sales_state=$db->rp_getValue("sales_executive","state","id=".$sales_id."");
			$sales_email=$db->rp_getValue("sales_executive","email","id=".$sales_id."");
			?>
			<td style="width:50%;">
			<?php
			$pincode=$db->rp_getValue("sales_executive","zip","id=".$sales_id.""); 
					$city 	= stripslashes($db->rp_getValue("city","name","id=".$sales_city."")); 
					$state 	= stripslashes($db->rp_getValue("state","name","id=".$sales_state.""));
					$country = stripslashes($db->rp_getValue("country","name","id=".$sales_country."")); 
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
			<strong>Name : </strong><?php echo stripslashes($db->rp_getValue("sales_executive","name","id=".$sales_id."")); ?>
			<h2 class="heading">
					<strong>Phone</strong>: <?php echo $sales_phone; ?><br/>
					
					<strong>Email</strong>: <?php echo $sales_email; ?>
				</h2>
				<h2 class="heading" style="padding-top:0;"><strong> Address:</strong><?php echo $sales_address."."; ?><br><?php echo str_replace(",,",",",$loc);?></h2>
				
			</td>
			<?php
			}
			
			else if($cart_detail_d['customer_type']=='dealer')
			{
			$customer_id=$cart_detail_d['customer_id'];
			$sales_id=$db->rp_getValue("executive","super_stockist_id","id=".$customer_id."");
			$sales_phone=$db->rp_getValue("executive","phone","id=".$sales_id."");
			$sales_address=$db->rp_getValue("executive","address","id=".$sales_id."");
			$sales_city=$db->rp_getValue("executive","city","id=".$sales_id."");
			$sales_country=$db->rp_getValue("executive","country","id=".$sales_id."");
			$sales_state=$db->rp_getValue("executive","state","id=".$sales_id."");
			$sales_email=$db->rp_getValue("executive","email","id=".$sales_id."");
			?>
			<td style="width:50%;">
			<?php
			$pincode=$db->rp_getValue("executive","zip","id=".$sales_id.""); 
					$city 	= stripslashes($db->rp_getValue("city","name","id=".$sales_city."")); 
					$state 	= stripslashes($db->rp_getValue("state","name","id=".$sales_state.""));
					$country = stripslashes($db->rp_getValue("country","name","id=".$sales_country."")); 
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
			<strong>Name : </strong><?php echo stripslashes($db->rp_getValue("executive","cname","id=".$sales_id."")); ?>
			<h2 class="heading">
					<strong>Phone</strong>: <?php echo $sales_phone; ?><br/>
					
					<strong>Email</strong>: <?php echo $sales_email; ?>
				</h2>
				<h2 class="heading" style="padding-top:0;"><strong> Address:</strong><?php echo $sales_address."."; ?><br><?php echo str_replace(",,",",",$loc);?></h2>
			</td>
			<?php
			}
			else if($cart_detail_d['customer_type']=='super_stockist')
			{
				$loc = SS_CITY." - ".SS_ZIP.", ".SS_STATE.", ".SS_COUNTRY;
			?>
			<td style="width:50%;">
			<strong>To,</strong><br>
			<strong>Name : </strong><?php echo SS_NAME; ?>
			<h2 class="heading">
					<strong>Phone</strong>: <?php echo SS_PHONE; ?><br/>
					
					<strong>Email</strong>: <?php echo SS_EMAIL; ?>
				</h2>
				<h2 class="heading" style="padding-top:0;"><strong> Address:</strong><?php echo SS_ADDRESS."."; ?><br><?php echo str_replace(",,",",",$loc);?></h2>
				
			</td>
			<?php
			}
			?>
		</tr>
		<tr>
    </table>
     
    <div id="content">
         
         <div id="invoice_body">
            <table border="1">
            <tr style="background:#eee;">
                <td style="text-align:center;width:65%;"  colspan="3"><strong>Product Name</strong></td>
				<td style="text-align:center;width:19%;" ><strong>Qty</strong></td>
            </tr>
            
			<?php
			$order_item_r = $db->rp_getData("customer_order_request_item","*","request_id='".$request_id."'","",0);
			if(mysqli_num_rows($order_item_r)>0){
				
				while($order_item_d = mysqli_fetch_array($order_item_r)){
					$id 		= $order_item_d['id'];
					$item_name 	= stripslashes($order_item_d['item_name']);
					$request_qty 		= $order_item_d['request_qty'];
					$pending_qty 		= $order_item_d['pending_qty'];
				?>
				<tr>
					<td style="text-align:center;width:65%;" colspan="3">
						<span class=""><?php echo $item_name; ?></span>
					</td>
					<td style="width:19%;" class="rp_right">
						<?php echo $request_qty; ?>
					</td>
					
				</tr>
				<?php
				/* $total_pro_qty+=$pro_qty;
				$total_pro_price+=$pro_price;
				$total_price+=$totalprice;
				$total_dispatched_qty+=$dispatched_qty_qty;
				$total_remaining_qty+=$remaining_qty; */
				
					}
				}
				
				?>
			  
           
             
            
			<!--<tr>
				<td colspan="5" class="rp_right">Total :</td>
				<td style="width:10%;" class="rp_right"><?php echo $total_price; ?></td>
				
			</tr-->
			
			
			<?php
			$grand_total=$db->rp_getValue("orders","grand_total","id='".$order_id."'");
			$grand_total=$total_price-$discount;
			?>
			
        </table>
			<!--table width="40%" align="right" style="width:40%" border="1">
				<tr>
				<td class="rp_right"><strong>Total Qty</strong></td>
				<td class="rp_right"  align="right"><?php echo $total_pro_qty; ?></td>
				</tr>
				<tr>
				<td class="rp_right"><strong>Total Amount</strong></td>
				<td class="rp_right"  align="right"><?php echo CURR.$db->rp_num($total_price); ?></td>
				</tr>
				
			
				<tr>
			<?php
			$discount=$db->rp_getValue("orders","discount","id='".$order_id."'",0);
			$discount_type=$db->rp_getValue("orders","discount_type","id='".$order_id."'");
			?>
				<td class="rp_right" style="text-align:right;"> <strong>Discount </strong> <?php //if($discount_type==1){echo "(".$discount."%)";}?> </td>
				<td class="rp_right">
				<?php  
				if($discount_type==1)
				{
					$d=$db->rp_getValue("orders","total_amount","id=".$order_id."")*$discount/100;
					echo CURR.$db->rp_num($d);
				}
				else
				{
					//echo '&nbsp;'."Rs.";
					echo CURR.$db->rp_num($discount);
				}
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
					
					<td class="rp_right"  align="right"><?php echo CURR.$db->rp_num($grand_total); ?></td>
				</tr>			
					
			</table-->
        </div>
        <div id="invoice_total" style="margin-top:1mm;">
            <table border="1">
                <!--tr>
                    <td style="padding-left:10px;text-align:left;">
						<strong>AMOUNT IN WORDS: </strong>
						<?php 
						$ntw = new NumToWord_RP;
						echo $ntw->rp_convertNumToWord($grand_total);
						?>
					</td>
                </tr-->
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
<script type="text/javascript">
	myWindow.close();
</script>
</html>
