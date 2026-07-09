<?php
$page_id=403;
$page_slug="logout";
include("connect.php");

$db->rp_update("dealer_distributor_network",array("refresh_token_web"=>""),"id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'",0);

unset($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']);
unset($_SESSION[SITE_SESS.'_ADMIN_SESS_TYPE']);
unset($_SESSION['rights']);
unset($_SESSION['SESS_NAME']);
unset($_SESSION);
session_destroy();
$db->rp_location("index.php");
?>