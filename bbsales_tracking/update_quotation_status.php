<?php
$page_id=607;$page_slug='quotation';
include("connect.php");
$quotation_id	= $_REQUEST['quotation_id'];
$status	= $_REQUEST['status'];
$quotation_no = $db->rp_getValue("quotation_detail","quotation_no","id='".$quotation_id."'");
// echo '<pre>';
// print_r($_REQUEST);
// echo '</pre>';
// exit;
$update = $db->rp_update("quotation_detail",array("status"=>$status),"id='". $quotation_id."'",0);
$customer_id = $db->rp_getValue("quotation_detail","customer_id","id='".$quotation_id."'");
if($status==1)
{
	$txt="Approve";
	/*log entry*/
	$ctable = "quotation_detail";
	$last_id = $quotation_id;
	$flag = "Web";
    $module_name = "Quotation";
    $log_description = $module_name." ".$quotation_no." Status Change To ". $txt." By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
    $db->insertLog($ctable,$last_id,"update","",$insert,0,$log_description,$flag,$module_name,$user_id,$customer_id);
	/*log entry*/
}
else if($status==-2)
{
	$txt="Dispprove";
	/*log entry*/
	$ctable = "quotation_detail";
	$last_id = $quotation_id;
	$flag = "Web";
    $module_name = "Quotation";
    $log_description = $module_name." ".$quotation_no." Status Change To ". $txt." By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
    $db->insertLog($ctable,$last_id,"update","",$insert,0,$log_description,$flag,$module_name,$user_id,$customer_id);
	/*log entry*/
}
else if($status==3)
{
	$txt="Cancel";
	/*log entry*/
	$ctable = "quotation_detail";
	$last_id = $quotation_id;
	$flag = "Web";
    $module_name = "Quotation";
    $log_description = $module_name." ".$quotation_no." Status Change To ". $txt." By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
    $db->insertLog($ctable,$last_id,"update","",$insert,0,$log_description,$flag,$module_name,$user_id,$customer_id);
	/*log entry*/
}
if($update)
{
	require_once("../include/push_notification.class.php");
	$objPushNotification=new PushNotification();

	$quotation_no  = $db->rp_getValue("quotation_detail","quotation_no","id='".$quotation_id."'");
	$notification_description = "Your Quotation Status is ".$txt." for Quotation No ".$quotation_no;

	// send sales executive notification added by shivani     
	$sales_id = $db->rp_getValue("quotation_detail","sales_id","id='".$quotation_id."'");
	$objPushNotification->commonNotification($sales_id,$quotation_id,"quotation_detail","Quotation Status Change",$notification_description,"sales_executive","quotation");
	// send sales executive notification added by shivani 

	// send customer notification added by shivani
	$customer_id = $db->rp_getValue("quotation_detail","customer_id","id='".$quotation_id."'");
	$objPushNotification->commonNotification($customer_id,$quotation_id,"quotation_detail","Quotation Status Change",$notification_description,"customer","quotation");
	// send customer notification added by shivani 

	// send customer upper chanel notification added by shivani 
	$customer_type  = $db->rp_getValue("quotation_detail","customer_type","id='".$quotation_id."'");
	if ($customer_type== 2)  //distributor
	{  
	    $upper_chanel_id = $db->rp_getValue("executive","super_stockist_id","id='".$customer_id."'");
	}
	else if ($customer_type == 3) //retailer 
	{
	    $upper_chanel_id = $db->rp_getValue("executive","dealer_distributor_id","id='".$customer_id."'"); 
	} 
	$objPushNotification->commonNotification($upper_chanel_id,$quotation_id,"quotation_detail","Quotation Status Change",$notification_description,"customer","quotation");
	// send customer upper chanel notification added by shivani 

	$reply=array("ack"=>1,"ack_msg"=>"Quotation ".$txt." Successfully");
}
else
{
	$reply=array("ack"=>0,"ack_msg"=>"Quotation ".$txt." Updated Failed");
}

echo json_encode($reply);
