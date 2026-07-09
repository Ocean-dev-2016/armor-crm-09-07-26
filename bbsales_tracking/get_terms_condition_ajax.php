<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
// print_r($_REQUEST);exit;
$customer_type 		= $_POST["companytype"];
$where = "id ='".$customer_type."' AND isDelete=0";
$terms_comdition_d = $db->rp_getValue("company_master","trems_and_condition",$where,0);
echo str_replace('rn','',$terms_comdition_d);

require_once "disconnect.php";
?>
