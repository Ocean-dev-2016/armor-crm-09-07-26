<?php
$page_id=561;$page_slug='deep_freezer_scheme';
include("connect.php");
$id=$_REQUEST['id'];
// print_r($_REQUEST['id']);exit();
 $status	= 1;
$update = $db->rp_update("freezer_scheme",array("status"=>$status),"id='".$id."'",0);

if($_SESSION[SITE_SESS.'REFERANCE_TYPE']!=0)
{
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive 
	{   
		$status_change_by_name=$db->rp_getValue("sales_executive","name","isDelete=0 AND id='". $_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
	} 
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==3) //sales executive 
	{   
		$status_change_by_name=$db->rp_getValue("executive","cname","isDelete=0 AND id='". $_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
	}
}
else{
	$status_change_by_name="Admin";
}
if($update)
{  
	// for customer notification added by shivani
	$customer_id = $db->rp_getValue("freezer_scheme","customer_id","id='".$id."'");
	$serial_no = $db->rp_getValue("freezer_scheme","serial_no","id='".$id."'");
    $notification_description = "Your deep Freeze of serial no ".$serial_no." has been approved by ".$status_change_by_name;
      
    require_once("../include/push_notification.class.php");
    $objPushNotification=new PushNotification();
        
    $result_sales=$objPushNotification->commonNotification($customer_id,$id,"freezer_scheme","Approve Deep Freeze",$notification_description,"customer","freezer_scheme");
    // for customer notification added by shivani

	$reply=array("ack"=>1,"ack_msg"=>"Freezer scheme ".$txt." Successfully");
}
else
{
	$reply=array("ack"=>0,"ack_msg"=>"Freezer scheme ".$txt." Updated Failed");
}  
require_once 'disconnect.php'; 
echo json_encode($reply);
?>