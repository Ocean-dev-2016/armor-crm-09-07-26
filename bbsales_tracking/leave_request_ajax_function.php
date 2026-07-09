<?php
$page_id=594;$page_slug='leave_request';
include("connect.php");
require_once("../include/push_notification.class.php");
$objPushNotification= new PushNotification();
$m=$_REQUEST['m'];
$id=$_REQUEST['id'];

$leaveData_r = $db->rp_getData("leave_request","*","id='".$id."'");
$leaveData_d = mysqli_fetch_assoc($leaveData_r);

$user_id = $leaveData_d['sales_executive_id'];
$start_date = $leaveData_d['start_date'];
$end_date = $leaveData_d['end_date'];

$leave_type = $db->rp_getValue("leave_type","name","id='".$leaveData_d['leave_type']."'");

if($_SESSION[SITE_SESS.'REFERANCE_TYPE']!=0)
{
	if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2) //sales executive and its chain wise order
	{   
		$status_change_by_name=$db->rp_getValue("sales_executive","name","isDelete=0 AND id='". $_SESSION[SITE_SESS.'REFERANCE_ID']."'",0);
	} 
}
else{
	$status_change_by_name="Admin";
}

if($id!="" && $m!="")
{	
	if($m=="accept")
	{	
	    if($id)
		{  
			$update = $db->rp_update("leave_request",array("status"=>1),"id='".$id."'",0);
			if($update)
			{  
			    $notification_description = "Your ".$leave_type." from ".date('d-m-Y',strtotime($start_date))." to ".date('d-m-Y',strtotime($end_date))." has been approved by ".$status_change_by_name; 
				 
				$result_sales=$objPushNotification->commonNotification($user_id,$id,"leave_request","Leaved Approved",$notification_description,"sales_executive","leave_request");
 
			}
			$reply=array("ack"=>1,"ack_msg"=>"Leave Approve Successfully");
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"Leave Not Approve.Please Try again!!");
		} 
	}
	if($m=="reject")
	{
		if($id)
		{  
			$update = $db->rp_update("leave_request",array("status"=>2,"cancel_reject_reason"=>$_REQUEST['cancel_reject_reason']),"id='".$id."'",0);
			if($update)
			{ 
			    $notification_description = "Your ".$leave_type." from ".date('d-m-Y',strtotime($start_date))." to ".date('d-m-Y',strtotime($end_date))." has been rejected by ".$status_change_by_name; 
				  	
				$result_sales=$objPushNotification->commonNotification($user_id,$id,"leave_request","Leave Rejected",$notification_description,"sales_executive","leave_request");
			}
			$reply=array("ack"=>1,"ack_msg"=>"Leave Reject Successfully");
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"Leave Not Reject.Please Try again!!");
		}
	}
	if($m=="cancel")
	{
		if($id)
		{   
			$update = $db->rp_update("leave_request",array("status"=>3,"cancel_reject_reason"=>$_REQUEST['cancel_reject_reason']),"id='".$id."'",0);
			if($update)
			{  
  			    $notification_description = "Your ".$leave_type." from ".date('d-m-Y',strtotime($start_date))." to ".date('d-m-Y',strtotime($end_date))." has been cancelled by ".$status_change_by_name; 
				 
				$result_sales=$objPushNotification->commonNotification($user_id,$id,"leave_request","Leave Cancelled",$notification_description,"sales_executive","leave_request");
			}
			$reply=array("ack"=>1,"ack_msg"=>"Leave Cancel Successfully");
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"Leave Not Cancel.Please Try again!!");
		}
	}
}
else
{
	$reply=array("ack"=>0,"ack_msg"=>"Something went wrong!! Please Try again!!");
}
require_once "disconnect.php";
echo json_encode($reply);
?>