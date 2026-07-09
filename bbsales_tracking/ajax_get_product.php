<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
require_once('../include/product.class.php');
$product=new Product();
$bid=$_REQUEST['bid'];
$cid=$_REQUEST['cid'];

$tcid=$_REQUEST['tcid'];

$order_unit_arr = array("-1"=>"Box","-2"=>"Strip","-3"=>"Pallet","1"=>"Caret","2"=>"Big Box","100"=>"Nos");
?>
<option value="">select product</option>
<?php
// $product_list_d=$db->rp_getData('product',"*","isDelete=0","",0);

if($tcid!="")
{
	$product_list_d=$db->rp_getData('product',"*","tcid IN (".$tcid.") AND isDelete=0","",0);
}
while($product_list_r=mysqli_fetch_assoc($product_list_d))
{
	$current_prodcuts=$product->aj_getProductDetail($product_list_r['id'],$cid);
	// print_r($current_prodcuts); 
	if(!empty($current_prodcuts))
	{
		$last_quotation_id = $db->rp_getValue("quotation_detail","id","customer_id='".$cid."' AND isDelete=0 ORDER BY id DESC",0);
		$last_quotation_price = $db->rp_getValue("quotation_product_item","original_price","quotation_id='".$last_quotation_id."' AND isDelete=0");
		foreach($current_prodcuts as $product_detail)
		{
			$cat_no = $db->rp_getValue("product_weight_price","catno","product_id='".$product_detail['pro_id']."' AND weight_id='".$product_detail['weight_id']."' AND isDelete=0");
			$stock_qty = $db->rp_getValue("product_weight_price","stock_qty","product_id='".$product_detail['pro_id']."' AND isDelete=0");
			$hsncode = $db->rp_getValue("product","hsn_code","id='".$product_detail['pro_id']."' AND isDelete=0");

			$top_cat_name = $db->rp_getValue("top_category_master","name","id='".$product_list_r['tcid']."' AND isDelete=0",0);
			$category_name = $db->rp_getValue("category_master","name","id='".$product_list_r['cid']."' AND isDelete=0",0);
			$unit_id_main= $db->rp_getValue("product","unit_id","id='".$product_detail['pro_id']."' AND isDelete=0");
			// $unit_id = $db->rp_getValue("product","display_unit","id='".$product_detail['pro_id']."' AND isDelete=0",0);

			$gst = $db->rp_getValue("product","igst","id='".$product_detail['pro_id']."' AND isDelete=0",0);

			$pro_master_price=$product_detail['orignal_price'];

			if($product_detail['is_including'] == 1)
			{
				if($gst != "")
				{
					$gst_val=1+($gst/100);
					$product_detail['orignal_price']=$db->rp_num($product_detail['orignal_price']/$gst_val);
				}
				else
				{
					$product_detail['orignal_price']=$db->rp_num($product_detail['orignal_price']/1);
				}
			} 

			if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==3)
			{
				$keyUnit = "customer_unit_id";
			}
			else
			{
				$keyUnit="unit_id";
			}
			$item_order_unit = $db->rp_getValue("product",$keyUnit,"id='".$product_detail['pro_id']."' AND isDelete=0");

			// $unit_name = $db->rp_getValue("unit","name","id='".$unit_id."' AND isDelete=0");
			$unit_name = $order_unit_arr[$item_order_unit];
			?>
			<option data-weight-id="<?php echo $product_detail['weight_id']?>" data-name="<?php echo $product_detail['name1']?>"  data-weight="<?php echo $product_detail['weight_id']?>" data-pricelist="<?php echo $product_detail['sell_price']?>" data-inner_size="<?php echo $product_detail['bag_qty']?>"  data-outer_size="<?php echo $product_detail['box_qty']?>" data-pro_id="<?php echo $product_detail['pro_id'] ?>" value="<?php echo $product_detail['id']?>" data-discount="<?= $product_detail['discountPer'];?>" data-original-price="<?= $product_detail['orignal_price']; ?>" data-stock_qty="<?= $product_detail['qty']?>" data-last_quot_price="<?= $last_quotation_price ?>" data-brand-id="<?= $product_detail['brand_id']?>" data-catno="<?= $cat_no?>" data-hsncode="<?= $hsncode ?>" data-stock="<?= $stock_qty ?>" data-cid="<?= $product_list_r['cid']?>" data-tcid="<?= $product_list_r['tcid']?>" data-topcat_name="<?= $top_cat_name ?>" data-cat_name="<?= $category_name ?>" data-gst="<?= $gst ?>" data-unit_name = "<?= $unit_name ?>"  data-pro_master_price = "<?= $pro_master_price ?>" data-is_including = "<?=$product_detail['is_including']?>" data-price_list_amount="<?=$product_detail['price_list_amount']?>" data-unit_main_id="<?=$unit_id_main?>" data-item_order_unit="<?=$item_order_unit?>" data-pro_weight ="<?= $product_detail['pro_weight'] ?>" data-inner_discount ="<?= $product_detail['inner_discount'] ?>" data-outer_discount ="<?= $product_detail['outer_discount'] ?>" data-is_premium ="<?= $product_detail['is_premium'] ?>" data-loose_discount="<?= $product_detail['loose_discount'] ?>"  data-min_sell_price="<?= $product_detail['minimum_selling_price']?>">
				<?php echo $product_detail['product_name']." - ".$cat_no?>
			</option>
			<?php
		}
	}
	
}
require_once "disconnect.php";
?>