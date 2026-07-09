<?php 
$page_id=400;$page_slug='dashboard';
include('connect.php');

$description = $db->rp_getValue("terms_condition","description","id='".$_REQUEST['id']."'");
echo str_replace('rn','',$description);
?>