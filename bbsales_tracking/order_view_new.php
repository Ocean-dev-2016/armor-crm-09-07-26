<?php
$page_id=566;$page_slug='page_order_ajax';
require_once("connect_in.php");
include("../include/no_to_word.php");
include("../include/product.class.php");
$ProductObj=new Product();
$ntw = new NumToWord_RP;
$order_id	= $_REQUEST['order_id'];
// $update = $db->rp_update("orders",array("status"=>1),"id='".$order_id."'",0);
$cart_detail_r 	= $db->rp_getData("orders","*","id='".$order_id."'","",0);
$cart_detail_d 	= mysqli_fetch_assoc($cart_detail_r);

// $cash_discount=$db->rp_getValue("customer","cash_discount","id='".$cart_detail_d['customer_id']."' AND isDelete=0",0);
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

?>
<html>
<head>
<style>
.mainDiv, table{
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
.border td{
	border-bottom: hidden !important;
}
.no-border-bottom{
	border-bottom: hidden !important;
}
.srno{
	width: 7% !important;
	min-width: 7%!important;
	max-width: 7%!important;
}
.pname{
	width: 18% !important;
	min-width: 18%!important;
	max-width: 18%!important;
	text-align: left;
}
.model{
	width: 30% !important;
	min-width: 30%!important;
	max-width: 30%!important;
	text-align: left;
}
.box_gty{
	width: 10% !important;
	min-width: 10%!important;
	max-width: 10%!important;
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
.amount{
	width: 10% !important;
	min-width: 10%!important;
	max-width: 10%!important;
}
.carrtoon_qty{
	width: 5% !important;
	min-width: 5%!important;
	max-width: 5%!important;
}
.color {
	background: #D3D3D3;
}
.bn-bw {
	border-style: none;
	border-color: white;
}
.bottom{
	border-bottom: none;
}
.top{
	border-top: none;
}
.left{
	border-left: none;
}
.right{
	border-right: none;
}
tbody
{
	text-transform: uppercase;
}
.font-size td
{
	font-size: 15px!important;
}
.addwatermark{
  background: url(../images/bgwatermark.png) no-repeat;
  background-size: cover;
}
</style>
</head>
<body>
<table>
	<tbody >
		<tr class="color">
			<?php 
			$GetDistributor_a = $db->rp_getData("executive","*","id='".$cart_detail_d['customer_id']."' AND isDelete=0");
			$Distri_data_a = mysqli_fetch_assoc($GetDistributor_a);
			if(strtolower($Distri_data_a['state'])!=strtolower("Bihar"))
			{
				?>
				<tr>
					<td colspan="4" class="no-border-right" style="text-align: left;"><span style="float: left"><img width="100" height="100" src="<?= SITEURL."images/chanakya.png"?>"></span></td>

					<td colspan="8" class=""><h3><strong><?php echo CLIENT_BRAND_NAME; ?></strong></h3></td>

					<td colspan="4" class="no-border-left" style="text-align: right;"><span style="float: right"><img width="100" height="100" src="<?= SITEURL."images/chanakya.png"?>"></span></td>
				</tr>
				<?php
			}
			else
			{
				?>
				<td colspan="16" class="text-center"><h3><strong><?php echo BIHAR_CLIENT_BRAND_NAME; ?></strong></h3></td>
				<?php
			}
			?>
		</tr>
		<tr>
			<td colspan="8" rowspan="2" width="50%" class="bottom"><b><?php echo COMPANY_NAME; ?></b></td>
			<td colspan="4" width="25%" class="bottom" >Voucher No.</td>
			<td colspan="4" width="25%" class="bottom">Dated</td>
		</tr>
		<tr>
			
			<td colspan="4" class="top"> <b>SO/50/20/21</b></td>
			<td colspan="4" class="top"><b>15-Jan-21</b></td>
		</tr>
		<tr>
			<td colspan="8" class="bottom top"><b><?php echo CLIENT_ADDRESS; ?></b></td>
			<td colspan="4"></td>
			<td colspan="4">Mode/Terms of Payment</td>
		</tr>
		<tr>
			<td colspan="4" class="bottom top right">GSTIN/UIN: </td>
			<td colspan="4" class="bottom top left">24AGGPK2306R1Z9</td>
			<td colspan="4" class="bottom">Buyer’s Ref./Order No.</td>
			<td colspan="4" class="bottom" >Other References</td>
		</tr>
		<tr>
			<td colspan="4" class="bottom top right">State Name :  Gujarat, </td>
			<td colspan="4" class="bottom top left"> Code : 24 </td>
			<td colspan="4" class="top"><b>210115</b></td>
			<td colspan="4" class="top"></td>
		</tr>
		<tr>
			<td colspan="4" class="bottom top right">Contact :</td>
			<td colspan="4" class="bottom top left" >  9662800001</td>
			<td colspan="4" class="bottom">Dispatched through</td>
			<td colspan="4" class="bottom">Destination</td>
		</tr>
		<tr>
			<td colspan="4" class="top right">E-Mail : </td>
			<td colspan="4" class="top left">dskathia@yahoo.com</td>
			<td colspan="4" class="top"><b>J D Logistics</b></td>
			<td colspan="4" class="top"><b>Pune</b></td>
		</tr>
		<tr>
			<td colspan="8" class="bottom ">Buyer (Bill to)</td>
			<td colspan="8"  class="bn-bw"><b>Terms of Delivery</b></td>
		</tr>
		<tr>
			<td colspan="8" class="bottom top" ><b>A Esufally & C</b></td>
			<td colspan="8" rowspan="8" class="bn-bw"></td>
		</tr>
		<tr>
			<td colspan="8" class="bottom top">Kohinoor Hardware, 907 /908 Opo Gujarat Lodge, Nrsonayamaruti Chowk , Bhoriali, Budhwar Peth, Pune 411002</td>
		</tr>
		<tr>
			<td colspan="4" class="bottom top right">GSTIN/UIN : </td>
			<td colspan="4" class="bottom top left">27ABAPD9583E1ZO</td>
		</tr>
		<tr>
			<td colspan="4" class="bottom top right">PAN/IT No :</td>
			<td colspan="4" class="bottom top left"> ABAPD9583E</td>
		</tr>
		<tr>
			<td colspan="4" class="bottom top right" >State Name : Maharashtra,</td>
			<td colspan="4" class="bottom top left">  Code : 27</td>
		</tr>
		<tr>
			<td colspan="4" class="bottom top right">Contact person : </td>
			<td colspan="4" class="bottom top left">Adnan</td>
		</tr>
		<tr>
			<td colspan="4" class="bottom top right">Contact : </td>
			<td colspan="4" class="bottom top left">20-24475239/24491928, 9881251850</td>
		</tr>
		<tr>
			<td colspan="4" class="top right" >E-Mail : </td>
			<td colspan="4" class="top left" >info@aekoh.com</td>
		</tr>
		<tr>
			<th colspan="1" class="text-center srno">Sr.No</th>
			<th colspan="4" class="text-center model">Description of Goods</th>
			<th colspan="2" class="pname text-center" class="text-center">HSN/SAC</th>
			<th colspan="1" class="text-center box_qty">Due on</th>
			<th colspan="1" class="text-center rate ">Quantity</th>
			<th colspan="2" class="text-center box_qty">Rate</th>
			<th colspan="2" class="text-center carrtoon_qty">Per</th>
			<th colspan="1" class="text-center carrtoon_qty">Disc %</th>
			<th colspan="2" class="text-center rate ">Amount</th>
		</tr>
		<?php
		// sorting based on product dispaly order & size display order
		$ITEMS=array();
		$items1=$db->rp_getData("order_product_item","*","order_id='".$order_id."'");
		while ($item1=mysqli_fetch_assoc($items1)) 
		{
			$item1['display_order']=$db->rp_getValue("product","display_order","id='".$item1['pro_id']."' AND isDelete=0");
			$item1['weight_display_order']=$db->rp_getValue("weight","display_order","id='".$item1['weight_id']."' AND isDelete=0");
			// print_r($item1);
			$ITEMS[]=$item1;
			// $ProductObj->sortBy('weight_display_order', $ITEMS, 'desc');
			// $ProductObj->sortBy('display_order', $ITEMS, 'asc');
		}
		// exit;
		// print_r($ITEMS);exit;
		$tempArr = array();
		foreach ($ITEMS as $key => $val) {
			 $tempArr['display_order'][$key] = $val['display_order'];
        	$tempArr['weight_display_order'][$key] = $val['weight_display_order'];
		}

		array_multisort($tempArr['display_order'], SORT_ASC, $tempArr['weight_display_order'], SORT_ASC,$ITEMS);
		// sorting based on product dispaly order & size display order
    	// print_r($ITEMS);exit;

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
			// while ($item=mysqli_fetch_assoc($items)) 
			foreach($ITEMS as $item)
			{
				$display_order=$db->rp_getValue("product","display_order","id='".$item['pro_id']."' AND isDelete=0");
				$tcid=$db->rp_getValue("product","tcid","id='".$item['pro_id']."' AND isDelete=0");
				$GST=$db->rp_getValue("product","igst","id='".$item['pro_id']."' AND isDelete=0");
				// 
				$pro_name=$db->rp_getValue("product","name","id='".$item['pro_id']."' AND isDelete=0");
				$size=$db->rp_getValue("weight","name","id='".$item['weight_id']."' AND isDelete=0");
				$product_code=$db->rp_getValue("product_weight_price","catno","product_id='".$item['pro_id']."' AND weight_id='".$item['weight_id']."'",0);
				
				
				$image=$db->rp_getValue("product","image_path","id='".$item['pro_id']."' AND isDelete=0");

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
				$count++;
				$rate=$item['pro_qty']*$item['unitprice'];
				$totalprice+=$item['totalprice'];
				$boxqty+=$item['box_qty'];
				$cartoonqty+=$item['cartoon_qty'];
				$totalproqty+=$item['pro_qty'];
				// $totalrate+=$price;
				$totalrate+=$item['unitprice'];
				$totaldiscount+=$item['discount'];
				if($brand_name=="Prince Platinum")
				{
					$brand_name="Prince Plm";
				}
			?>

				<tr>
					<td colspan="1" class="text-center srno"><?php echo $count; ?></td>

					<td colspan="4" class="model" style="position: relative;"><img style="width: 300px;height: 150px; margin-left: 50px;" src="<?php echo SITEURL.PRODUCT.$item['image_path'] ?>"><strong><?php echo $product_code; ?></strong></td>
					<td colspan="2" class="pname" class="text-center" ><?php if($item['weight_id']!=-1){echo $pro_name." - ".$size." - ".strtoupper($product_code); } else { echo $pro_name." - ".strtoupper($product_code); } ?></td>
					<td colspan="1" class="box_qty" style="text-align: center;"><?php echo $item['original_price']; ?></td>

					<td colspan="1" class="text-center rate"><?php echo round($rate_total, 2); ?></td>
					<!-- <td colspan="1" class="text-center rate"><?php echo $db->rp_number_format($item['unitprice']); ?></td> -->

					<td colspan="2" class="box_qty" style="text-align: center;"><?php echo round($box_qty_total, 2); ?></td>
					<!-- <td colspan="2" class="box_qty" style="text-align: center;"><?php echo $db->rp_number_format($item['original_price']*18/100); ?></td> -->

					<td colspan="2" class="text-center carrtoon_qty "><?php echo $item['pro_qty']; ?></td>
					
					<td colspan="1" class="text-center rate"><?php echo round($max_total, 2); ?></td>
					<td colspan="2" class="text-center rate"><?php echo round($max_total, 2); ?></td>
					<!-- <td colspan="2" class="text-center rate"><?php echo $db->rp_number_format($item['totalprice']); ?></td> -->

				</tr>
			<?php
			}
			if($count<10)
			{
				for($i=0;$i<10-$count;$i++)
				{
				?>
				<tr class="border">
					<td colspan="1" class="srno"></td>
					<td colspan="4" class="model" ></td>
					<td colspan="2" class="pname"></td>
					<td colspan="1" class="box_qty"></td>
					<td colspan="1" class="box_qty"></td>
					<td colspan="2" class="carrtoon_qty"></td>
					<td colspan="2" class="rate"></td>
					<td colspan="1" class="rate"></td>
					<td colspan="2" class="rate"></td>
				</tr>
				<?php 
				}
			}
		}
		?>
		<tr>
			<td colspan="1" class="srno"></td>
			<td colspan="4" class="model" ></td>
			<td colspan="2" class="pname"></td>
			<td colspan="1" class="box_qty"></td>
			<td colspan="1" class="box_qty"></td>
			<td colspan="2" class="carrtoon_qty"></td>
			<td colspan="2" class="rate"></td>
			<td colspan="1" class="rate"></td>
			<td colspan="2" class="rate"></td>
		</tr>
		<tr>
			<td colspan="1" class="srno"></td>
			<td colspan="4" class="model" > <b>IGST</b></td>
			<td colspan="2" class="pname"></td>
			<td colspan="1" class="box_qty"></td>
			<td colspan="1" class="box_qty"></td>
			<td colspan="2" class="carrtoon_qty"></td>
			<td colspan="2" class="rate"></td>
			<td colspan="1" class="rate"></td>
			<td colspan="2" class="rate">3240.00</td>
		</tr>
		<tr>
			<td colspan="1" class="srno"></td>
			<td colspan="4" class="model" > Total</td>
			<td colspan="2" class="pname"></td>
			<td colspan="1" class="box_qty"></td>
			<td colspan="1" class="box_qty"><b> 50 NOC</b></td>
			<td colspan="2" class="carrtoon_qty"></td>
			<td colspan="2" class="rate"></td>
			<td colspan="1" class="rate"></td>
			<td colspan="2" class="rate">6660.00</td>
		</tr>
		<tr>
			<td class="bn-bw" colspan="12">Amount Chargeable (in words)</td>
			<td class="bn-bw" colspan="4" align="right" >E. & O.E</td>
		</tr>
		<tr>
			<td class="bn-bw" colspan="12"><b>Twenty One Thousand Two Hundred Forty INR Only</b></td>
			<td class="bn-bw" colspan="4" align="right"></td>
		</tr>
		<tr>
			<td class="bn-bw" colspan="8"></td>
			<td class="bn-bw" colspan="8">Company’s Bank Details </td>
		</tr>
		<tr>
			<td class="bn-bw" colspan="8">Company’s PAN : <b>AGGPK2306R</b></td>
			<td class="bn-bw" colspan="4">Bank Name :</td>
			<td class="bn-bw" colspan="4"><b>IDBI Bank Ltd</b></td>
		</tr>
		<tr>
			<td class="bn-bw" colspan="8"></td>
			<td class="bn-bw" colspan="4">A/c No. : </td>
			<td class="bn-bw" colspan="4"><b>164210200000377</b></td>
		</tr>
		<tr>
			<td class="bn-bw" colspan="8"></td>
			<td class="bn-bw" colspan="4">Branch & IFS Code : </td>
			<td class="bn-bw" colspan="4"><b>Rajkot & IBKL0001642</b></td>
		</tr>
		<tr>
			<td class="bn-bw" colspan="8" rowspan="4"></td>
			<td class="bn-bw" colspan="4"></td>
			<td class="bn-bw" colspan="4"><b>for Chanakya Engineering Products</b></td>
		</tr>
		<tr>
			<td class="bn-bw" colspan="8"></td>
		</tr>
		<tr>
			<td class="bn-bw" colspan="8"></td>
		</tr>
		<tr>
			<td class="bn-bw" colspan="4"></td>
			<td class="bn-bw" colspan="4">Authorised Signatory</td>
		</tr>
		
		
	</tbody>
	<!-- <tr> -->
		
	<!-- </tr> -->
</table>



</body>
</html>