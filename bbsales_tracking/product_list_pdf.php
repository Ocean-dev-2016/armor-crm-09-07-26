<?php
$page_id=559;$page_slug='page_product';
require_once("connect.php");
require_once("../include/product_list_where.php");
require_once("../include/product_list_html.php");

if($rights['print_flag']!=1 && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
	$db->rp_location('access_denied.php?msg=print_access_denied');
}

$with_price = (isset($_REQUEST['with_price']) && $_REQUEST['with_price'] == '1');

$flag = "Web";
$module_name = "Product";
$log_description = $module_name." List PDF Downloaded By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
$db->insertLog("product","","print","",array(),0,$log_description,$flag,$module_name,$user_id,"");

while(ob_get_level())
{
	ob_end_clean();
}

header('Content-Type: text/html; charset=utf-8');
echo productListBuildHtml($db, $_REQUEST, $with_price);
require_once 'disconnect.php';
?>
