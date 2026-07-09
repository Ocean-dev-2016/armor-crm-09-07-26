<?php
$page_id=577;$page_slug='visit_page';
$ctable 	= "visit";
$ctable1 	= "User";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
include("connect.php");

$name			= "";
$code			= "";

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$status = $_REQUEST['status'];
	$rows 	= array(
				"isActive"	=> $status
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where,0);
	$db->rp_location("visit_manage.php?msg=updated");
}
?>