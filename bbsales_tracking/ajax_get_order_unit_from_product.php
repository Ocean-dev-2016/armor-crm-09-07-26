<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");

$item_customer_order_unit = $db->rp_getValue("product","customer_unit_id","id='".$_REQUEST['pro_id']."' AND isDelete=0");

if($_SESSION[SITE_SESS.'REFERANCE_TYPE']!=3)
{  
	$item_sales_order_unit = $db->rp_getValue("product","unit_id","id='".$_REQUEST['pro_id']."' AND isDelete=0",0);
}
?>
<option value="">Select Order Unit</option>
<?php 
$order_unit_arr = array("-1"=>"Box","-2"=>"Strip","-3"=>"Pallet","1"=>"Caret","2"=>"Big Box","100"=>"Nos");
foreach ($order_unit_arr as $key => $value) {  
	if($key==$item_customer_order_unit || $key==$item_sales_order_unit)
	{ 
?>
<option value="<?= $key ?>" data-unitname="<?= $value ?>"><?= $value ?></option>
<?php
	}
}
?> 
<?php require_once 'disconnect.php';  ?>