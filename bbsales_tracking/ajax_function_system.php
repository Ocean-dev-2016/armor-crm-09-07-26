<?php
$page_id=400;$page_slug='page_attachments';
include("connect.php");
require_once("../include/class.log.php");
include("translations/timeago.inc.php");
$timeZone = "ASIA/KOLKATA";

function buildAdminNotificationHtml($system, $notification, $timeZone)
{
	$time = timeAgoInWords($notification['created_date'], $timeZone, 'en');
	$title = htmlspecialchars(stripslashes($notification['notification_title']), ENT_QUOTES, 'UTF-8');
	$desc = htmlspecialchars(stripslashes($notification['notification_description']), ENT_QUOTES, 'UTF-8');
	$notif_type = isset($notification['notification_type']) ? $notification['notification_type'] : '';
	$ref_type = isset($notification['referance_type']) ? $notification['referance_type'] : '';
	$ref_id = isset($notification['referance_id']) ? (int)$notification['referance_id'] : 0;
	$view_link = '';
	$followup_meta = '';
	$anchor_href = 'notification_manage.php?mode=all';

	if ($notif_type == 'followup' || $ref_type == 'followup') {
		$view_link = '<a href="followuplist_manage.php?followup_type=today" class="btn btn-xs btn-info" style="margin-right:4px;"><i class="fa fa-eye"></i> View</a>';
		$details = $system->resolveFollowupPartyNames($ref_id);
		$anchor_href = 'followuplist_manage.php?followup_type=today';
		$followup_meta = '
			<div class="notif-followup-meta">
				<span class="notif-meta-item"><i class="fa fa-user"></i> <strong>Employee:</strong> ' . htmlspecialchars(stripslashes($details['sales_name'])) . '</span>
				<span class="notif-meta-item"><i class="fa fa-building"></i> <strong>Customer:</strong> ' . htmlspecialchars(stripslashes($details['customer_name'])) . '</span>
			</div>';
		if ($details['followup_time'] != "") {
			$followup_meta .= '<div class="notif-followup-time"><i class="fa fa-clock-o"></i> ' . $details['followup_time'];
			if ($details['through_label'] != "") {
				$followup_meta .= ' &nbsp;|&nbsp; <i class="fa fa-phone"></i> ' . $details['through_label'];
			}
			$followup_meta .= '</div>';
		}
	} else {
		$anchor_href = 'notification_manage.php?mode=all';
	}

	return '<li>
		<a href="'.$anchor_href.'" class="notif-item-anchor">
			<div class="notif-item-title">'.$title.'</div>
			'.$followup_meta.'
			'.($desc != '' ? '<div class="notif-item-desc">'.$desc.'</div>' : '').'
			<div class="notif-item-time"><i class="fa fa-history"></i> '.$time.'</div>
		</a>
		<div class="notif-item-actions">
			'.$view_link.'
			<a class="btn btn-xs btn-success" onClick="aj.delete_notification(this,'.$notification['id'].')">
				<i class="fa fa-check"></i> Done
			</a>
		</div>
	</li>';
}

if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
{
		$service=$_REQUEST['mode'];
		if($service=="get_notifications")
		{
			$html=(isset($_REQUEST['html']) && ($_REQUEST['html']==true || $_REQUEST['html']=='true'))?true:false;
			$user_id="";
			$notifications=$system->getQuickNotifications(50);
			$result="";
			if($notifications)
			{
				$notifications_result=array();
				foreach($notifications as $key=>$notification)
				{
					$time=timeAgoInWords($notification['created_date'], $timeZone, 'en');
					$notification['created_date']=$time;
					$notifications_result[]=$notification;
					$result .= buildAdminNotificationHtml($system, $notification, $timeZone);
				}
				if($html==true)
				{
					echo $result;
				}
				else
				{
					$response=array('ack'=>1,'ack_msg'=>'Notifications Fetched!!!',"notification_json"=>$notifications_result,"count"=>count($notifications_result));
					echo json_encode($response);
				}
			}
			else
			{
				$result='<li class="notif-empty"><i class="fa fa-bell-slash fa-2x"></i><br/>No Notifications</li>';
				if($html==true)
				{
					echo $result;
				}
				else
				{
					$response=array('ack'=>0,'ack_msg'=>'No New Notifications!!!',"result"=>$result,"count"=>0);
					echo json_encode($response);
				}
			}
		}
		else if($service=="check_followup_toasts")
		{
			$last_id = isset($_REQUEST['last_id']) ? (int) $_REQUEST['last_id'] : 0;
			$init_only = (isset($_REQUEST['init_only']) && ($_REQUEST['init_only'] == '1' || $_REQUEST['init_only'] == 1));
			$toast_data = $system->getNewFollowupToastNotifications($last_id, $init_only);
			echo json_encode(array(
				'ack' => 1,
				'toasts' => $toast_data['toasts'],
				'max_id' => $toast_data['max_id'],
				'count' => $toast_data['count']
			));
		}
		else if($service=="employee_wise_today_followup_toast")
		{
			if (!isset($_SESSION[SITE_SESS . '_ADMIN_TYPE']) || $_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) {
				echo json_encode(array('ack' => 0, 'ack_msg' => 'Unauthorized'));
				exit;
			}
			$summary = $system->getEmployeeWiseTodayFollowupToasts(12);
			echo json_encode(array(
				'ack' => 1,
				'summary' => $summary
			));
		}
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
