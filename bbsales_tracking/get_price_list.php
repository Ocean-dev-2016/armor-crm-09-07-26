<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$ss_id=isset($_POST['super_stockist_id'])?$_POST['super_stockist_id']:"";
$price_list_id=$db->rp_getValue("executive","price_list_id","id='".$ss_id."' AND isDelete=0",0);
echo $price_list_id;
?>