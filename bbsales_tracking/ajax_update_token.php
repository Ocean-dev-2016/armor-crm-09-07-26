<?php
$page_id=401;
include("connect.php");
$Token = $_POST['token'];
$Update= $db->rp_update("dealer_distributor_network",array($_POST['update_to']=>$Token),"id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'",0);
if ($Update) 
{
	$reply1 = array("ack" => 1, "ack_msg" => "Update Successfully");
} 
else 
{
	$reply1 = array("ack" => 0, "ack_msg" => "Something Went Wrong");
}
echo json_encode($reply1);
?>
<?php require_once 'disconnect.php';  ?>
