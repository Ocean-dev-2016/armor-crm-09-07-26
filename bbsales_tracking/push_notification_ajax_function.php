<?php
$page_id=580;$page_slug='price_list_master';
include("connect.php");
require_once("../include/push_notification.class.php");
$objPushNotification= new PushNotification();
$m=$_REQUEST['m'];
$id=$_REQUEST['id'];
$get_refresh_token_table = $_REQUEST['get_refresh_token_table'];
if($id!="" && $m!="")
{	
    
	if($m=="send_to_all")
	{	
	    $type1 = $_REQUEST['type1'];
		$typeVal = implode($_REQUEST['typeVal'],",");
 
		if($type1==1)
		{
		    $user_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1 AND  refreshToken!=''","",0);
		}
		else if($type1==2)
		{
			$user_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1 AND refreshToken!='' AND id IN(".$typeVal.")","",0);
		}
		else if($type1==3)
		{
			$user_r=$db->rp_getData("executive","*","isDelete=0 AND isActive=1 AND refreshToken!=''","",0);
		}
		else if($type1==4)
		{
			$user_r=$db->rp_getData("executive","*","isDelete=0 AND isActive=1 AND refreshToken!='' AND id IN(".$typeVal.")","",0);
		}
		$notification_r=$db->rp_getData("push_notification","*","id='".$id."' AND isDelete=0");
		$D=mysqli_fetch_assoc($notification_r);
 		
		if($user_r)
		{  
			$detail=array();
			while($K=mysqli_fetch_assoc($user_r))
			{
				if($D['image_path']!="")
				{					
					 $img=SITEURL.NOTIFICATION.$D['image_path'];
				}
				else
				{
					$img="";
				}
				
				// $Mobile[]=$K['mobile_no'];				
				$detail['image_path'] = $img;
				$detail['get_refresh_token_table'] = $get_refresh_token_table;
				$detail['uid']=$K['id'];
				$detail['id']=$id;
				$detail['reference_table']="push_notification";
				$detail['notification_title']=$D['title'];
				$detail['notification_description']=$D['descr'];
				// $detail['type']=$D['type'];
				$detail['item_type']=$D['item_type'];
				$detail['item_id']=$D['item_id'];
				// $detail['notification_type']="Push Notification";
				$detail['notification_type']=$D['type'];
				$check=$objPushNotification->notificationInsert($detail);
			}
			// exit;
			$reply=array("ack"=>1,"ack_msg"=>"Notification send to all User Successfully");
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"No such a User found to send this Notification");
		} 
	}
}
else
{
	$reply=array("ack"=>0,"ack_msg"=>"Something went wrong!! Please Try again!!");
}
echo json_encode($reply);
?>
<?php require_once 'disconnect.php';  ?>