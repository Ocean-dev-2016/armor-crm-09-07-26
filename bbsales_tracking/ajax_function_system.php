<?php
$page_id=400;$page_slug='page_attachments';
include("connect.php");
require_once("../include/class.log.php");
include("translations/timeago.inc.php");
$timeZone = "ASIA/KOLKATA";
if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
{
		$service=$_REQUEST['mode'];
		if($service=="get_notifications")
		{
			$html=(isset($_REQUEST['html']) && $_REQUEST['html']==true)?true:false;
			$user_id="";
			$notifications=$system->getQuickNotifications();
			//print_r($notifications);exit;
			$result="";
			if($notifications)
			{
				$notifications_result=array();
				foreach($notifications as $key=>$notification)
				{
					
					$time=timeAgoInWords($notification['created_date'], $timeZone, 'en');
				//echo $notification['notification_title'].'sd';exit;
					$notification['created_date']=$time;
					
					$notifications_result[]=$notification;
				//	echo $notification['notification_msg'];exit;
				//print_r($notifications_result);exit;
					$result.='<li>
								<div class="col1">
									<div class="cont">
										<div class="cont-col1">
											
										</div>
										<div class="cont-col2">
											<div class="desc"> '.$notification['notification_title'].'
											<br/>
												<a class="btn btn-sm btn-primary " onClick="aj.delete_notification(this,'.$notification['id'].')"> 
													<i class="fa fa-check"></i> Done 
												</a>
											</div>
										</div>
									</div>
								</div>
								<div class="col2">
									<div class="date">'.$time.'</div>
								</div>
							</li>
						';
				}
				//echo $html;exit;
				if($html==true)
				{
					//print_r($result);
					//echo 'sd';exit;
					echo $result;
				}
				else
				{
					$response=array('ack'=>1,'ack_msg'=>'Notifications Fetched!!!',"notification_json"=>$notifications_result);
					echo json_encode($response);
					
				}
				
			}
			else
			{
				$result.='<li class="text-center">
								<div class="col1">
									<h1> No Notifications</h1>
								</div>
								<div class="col2">
									
								</div>
							</li>
						';
				if($html==true)
				{
					echo $result;
				}
				else
				{
				$response=array('ack'=>0,'ack_msg'=>'No New Notifications!!!',"result"=>$result);
				echo json_encode($response);
				}
				
			}
		}
		/*if($service=="get_notifications")
		{
			$html=(isset($_REQUEST['html']) && $_REQUEST['html']==true)?true:false;
			$notifications=$system->getNotifications();
			//print_r($notifications);exit;
			$result="";
			if($notifications)
			{
				$notifications_result=array();
				foreach($notifications as $key=>$notification)
				{
					$time=timeAgoInWords($notification['created_date'], $timeZone, 'en');
					$notification['created_date']=$time;
					$notifications_result[]=$notification;
					
					$result.='<li><div class="col1"><div class="cont"><div class="cont-col1"><div class="label label-sm label-info"><i class="'.$notification['notification_icon'].'"></i></div></div><div class="cont-col2"><div class="desc">'.$notification['notification_description'].'<a class="btn btn-sm btn-primary " onClick="aj.delete_notification(this,'.$notification['id'].')"><i class="fa fa-check"></i> Done</a></div></div></div></div><div class="col2"><div class="date">'.$time.'</div></div></li>';
				}
				if($html==true)
				{
					echo $result;
				}
				else
				{
					$response=array('ack'=>1,'ack_msg'=>'Notifications Fetched!!!',"notification_json"=>$notifications_result);
					echo json_encode($response);
				}
				
			}
			else
			{
				$result.='<li class="text-center">
								<div class="col1">
									<h1> No Notifications</h1>
								</div>
								<div class="col2">
									
								</div>
							</li>
						';
				if($html==true)
				{
					echo $result;
				}
				else
				{
				$response=array('ack'=>0,'ack_msg'=>'No New Notifications!!!',"result"=>$result);
				echo json_encode($response);
				}
				
			}
			
			
		}	*/		
		else if($service=='set_notification')
		{	
			if(isset($_REQUEST['notification_id']) && $_REQUEST['notification_id']!=""&& isset($_REQUEST['notification_icon']) && $_REQUEST['notification_icon']!=""&& isset($_REQUEST['notification_msg']) && $_REQUEST['notification_msg']!="")
			{
				$system->setNotification($_REQUEST['notification_id'],1,$_REQUEST['notification_msg'],$_REQUEST['notification_icon']);
				$response=array('ack'=>1,'ack_msg'=>'Notification added');
				echo json_encode($response);		
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
				echo json_encode($response);				
			}
		}		
		else if($service=='delete_notification')
		{
			if(isset($_REQUEST['notification_id']) && $_REQUEST['notification_id']!=""){
			$notification_id=$_REQUEST['notification_id'];
			
				$system->deleteNotifications($notification_id);
				$result=array('ack'=>1,'ack_msg'=>'Notification Done!!');
				echo json_encode($result);		
			}
			else
			{
				$result=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
				echo json_encode($result);				
			}
		}
		/*else if($service=='delete_notification')
		{
			$notification_id=$_REQUEST['notification_id'];
			$isDeleted=$this->db->rp_delete("notification","id='".$id."'",1);
			if($isDeleted!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"notification deleted!!","ack_msg"=>"Success! Delete notification Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Unit Failed.");
				return $reply;
			}
		
		}
	*/
	else if($service=='fetch_state')
	{
		if(isset($_REQUEST['country_id']) && $_REQUEST['country_id']!="")
		{
			$country_id=$_REQUEST['country_id'];
			$reply=$system->fetchState($country_id);
		}
	}
	else if($service=='fetch_city')
	{
		if(isset($_REQUEST['state_id']) && $_REQUEST['state_id']!="")
		{
			$state_id=$_REQUEST['state_id'];
			$reply=$system->fetchCity($state_id);
		}
	}
}
else
{
	$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
	echo json_encode($response);
}

?>
<?php require_once 'disconnect.php';  ?>

