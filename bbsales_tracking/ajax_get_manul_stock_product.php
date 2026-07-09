<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
require_once('../include/product.class.php');
$product=new Product();
// print_r($_REQUEST);exit;
$bid=$_REQUEST['bid'];
$cid=$_REQUEST['cid'];
$warehouse_id=$_REQUEST['warehouse_id'];
$tcid=$_REQUEST['tcid'];

if ($_REQUEST['product_id']) 
{
	$pro_id = $_REQUEST['product_id'];
}
// echo $product_id;exit;

?>
<option value="">select product</option>
<?php
// $product_list_d=$db->rp_getData('product',"*","isDelete=0","",0);

if($tcid!="")
{

	// $product_r = $db->rp_getData("product","*","isDelete=0 AND isActive=1");
	$product_list_r=$db->rp_getData('product',"*","tcid IN (".$tcid.") AND isDelete=0 AND isActive=1","",0);

}

while($product_d=mysqli_fetch_assoc($product_list_r))
{
	// print_r($product_d);exit;

	$product_weight = $db->rp_getData("product_weight_price","weight_id,catno,stock_qty,id","product_id='".$product_d['id']."' AND isDelete=0","",0);
	while($product_weight_d = mysqli_fetch_assoc($product_weight))
	{
		$weight_name = $db->rp_getValue("weight","name","id='".$product_weight_d['weight_id']."' AND isDelete=0 AND id!='-1'",0);

		$name = $db->rp_getValue("weight","name","id='".$product_weight_d['weight_id']."'");

		$pro_name  = $product_d['name'];

		$name1= htmlentities($name." ".$pro_name." ");
		$stk =$db->get_available_stock($product_d['id'],$product_weight_d['weight_id'],$warehouse_id);
		// echo $stk;exit;
		?>
    	<option <?=($pro_id==$product_d['id'])?"selected":"";?> class="pids_<?=$product_d['id']."_".$product_weight_d['weight_id']; ?>" data-weight-id="<?php echo $product_weight_d['weight_id']?>" data-name="<?php echo $name1; ?>" data-stock_qty="<?php echo $stk; ?>"  data-pid="<?php echo $product_d['id']?>" data-cat_no="<?= $product_weight_d['catno'] ?>" value="<?=$product_weight_d['id']; ?>">
    		<?=($weight_name!="")?$product_d['name'] ." - ". $weight_name:$product_d['name']." - ".$product_weight_d['catno']?></option>
		<?php
	}
	
	
}
?>
<?php require_once 'disconnect.php';  ?>