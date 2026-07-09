<?php 
$page_id=669;$page_slug='payment_followup_manage';
include("connect.php");
include('../include/payment_followup_class.php');
$ObjFollowup=new FollowupPayment();
if(isset($_GET['m']) && $_GET['m']!="")
{
	$m=$_GET['m'];
	if($m=='save_followup')
	{
		// print_r($_REQUEST);exit();
		$user_id = isset($_REQUEST['sales_id'])?$_REQUEST['sales_id']:"";
		$description=isset($_REQUEST['description'])?$_REQUEST['description']:"";
		$through=isset($_REQUEST['through'])?$_REQUEST['through']:"";
		$followup_date=isset($_REQUEST['followup_date'])?$_REQUEST['followup_date']:"";
		$visitor_id=isset($_REQUEST['visitor_id'])?$_REQUEST['visitor_id']:"";
		$entry_flag=isset($_REQUEST['entry_flag'])?$_REQUEST['entry_flag']:"1";
		$followup_status=isset($_REQUEST['followup_status'])?$_REQUEST['followup_status']:"";
		$followup_flag=isset($_REQUEST['followup_flag'])?$_REQUEST['followup_flag']:"";
		
		if($followup_flag=="quotation_followup")
		{
			$inquiry_id=isset($_REQUEST['quotation_id'])?$_REQUEST['quotation_id']:"";
		}
		else if($followup_flag=="customer_payment_followup")
		{
			$inquiry_id=isset($_REQUEST['executive_id'])?$_REQUEST['executive_id']:"";
			// $visitor_id=$inquiry_id;
		}
		else if($followup_flag=="manual_invoice_import")
		{
			$inquiry_id=isset($_REQUEST['invoice_id'])?$_REQUEST['invoice_id']:"";
			// $visitor_id=$inquiry_id;
		}
		else
		{
			$inquiry_id=isset($_REQUEST['inquiry_id'])?$_REQUEST['inquiry_id']:"";
		}
  
		//echo $inquiry_id; exit;

		$entry_type = "1";
		if($through!="" && $followup_date!="")
		{
		
			if($followup_flag=="customer_inquiry" || $followup_flag=="leads_followup")
			{
				$reference_table = "customer_inquiry";
			}
			if($followup_flag=="request_followup")
			{
				$reference_table = "request";
			}
			
			if($followup_flag=="customer_payment_followup")
			{
				$reference_table = "dealer_distributor_network";
			}
			
			if($followup_flag=="customer_followup")
			{
				$reference_table = "executive";
				$cuscol = "id";
			}
			
				// echo "test".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']; exit();
			if($user_id==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
			{

				$idx = $db->rp_getValue($reference_table,$cuscol,"id='".$inquiry_id."'",0);
				$user_id = $db->rp_getValue("executive","seid","id='".$idx."'",0);
				// echo $user_id;exit();
				if($user_id=="")
				{
					$user_id=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
					// $user_id=$_REQUEST['sales_id'];
				}
			}
			// added by shivani 
			//echo $inquiry_id; exit;
			$data=$ObjFollowup->CreateFollowup($user_id,$visitor_id,$description,$through,$followup_date,$followup_flag,$inquiry_id,$entry_type,$entry_flag);
 
			// $getStatus = $db->rp_getValue("no_order_inquiry")

			$module_name = "Inquiry";
			$flag = "Web";
			$log_description = $module_name." #INQ/".$inquiry_id." Status Chgnes To InFollowup By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
			//$db->rp_update("no_order_inquiry",array("status"=>$followup_status),"id='".$inquiry_id."' AND status = 0",0,$log_description,$flag,$module_name);
			$db->rp_update("no_order_inquiry",array("status"=>$followup_status),"id='".$inquiry_id."'",0,$log_description,$flag,$module_name);

				if($followup_flag=="no_order_inquiry" || $followup_flag=="inquiry_followup") {
					$db->addStatusTimelineEntry($inquiry_id,$followup_status,$user_id);
				}
			
			echo json_encode($data);
	
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"All Field are Required!!","dmg"=>"All Field are Required!!");
			echo json_encode($reply);	
		}
		
	}
	else if($m=='add_response')
	{
		$response=isset($_REQUEST['response'])?$_REQUEST['response']:"";
		$followup_action=isset($_REQUEST['followup_action'])?$_REQUEST['followup_action']:"";
		$followup_id=isset($_REQUEST['followup_id'])?$_REQUEST['followup_id']:"";
		$followup_reason_id=isset($_REQUEST['followup_reason_id'])?$_REQUEST['followup_reason_id']:"";
		$id=isset($_REQUEST['followup_id'])?$_REQUEST['followup_id']:"";
		$followup_future_date=isset($_REQUEST['followup_future_date'])?$_REQUEST['followup_future_date']:"";
		$entry_type = "1";
		$response_entry_flag = "1";
		if($response!="" && $followup_action!="")
		{
			$data=$ObjFollowup->AddFollowupResponse($response,$followup_action,$id,$followup_future_date,$followup_id,$followup_reason_id,$entry_type,$response_entry_flag);
				// if ($data['a'] == 1) {
				// 	$db->addStatusTimelineEntry($_REQUEST['inquiry_id'],$followup_status_id);
				// }
			
			echo json_encode($data);	
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Followup Response Required!!","dmg"=>"Followup Response Required!!");
			echo json_encode($reply);	
		}
		
	}
	else if($m=="end_followup")
	{

		$response=isset($_REQUEST['response'])?$_REQUEST['response']:"";
		$followup_action=isset($_REQUEST['followup_action'])?$_REQUEST['followup_action']:"";
		$id=isset($_REQUEST['followup_id'])?$_REQUEST['followup_id']:"";
		$followup_future_date=isset($_REQUEST['followup_future_date'])?$_REQUEST['followup_future_date']:"";
		$followup_reason_id=isset($_REQUEST['followup_reason_id'])?$_REQUEST['followup_reason_id']:"";

		$followup_status_id=isset($_REQUEST['followup_status_id'])?$_REQUEST['followup_status_id']:"";

		$entry_type = "1";
		$response_entry_flag = "1";
		if($response!="" && $followup_action!="")
		{
			$data=$ObjFollowup->AddFollowupResponse($response,$followup_action,$id,$followup_future_date,$followup_reason_id,$entry_type,$followup_status_id);
			if($data['a']==1)
			{	
					/*this is for change follwup status in Prospect,Inquiry,leads*/
					if($followup_status_id!="")
					{
						$values = array("status"=>$followup_status_id);
						$db->rp_update("no_order_inquiry",$values,"id='".$_REQUEST['inquiry_id']."'",0);

						$db->addStatusTimelineEntry($_REQUEST['inquiry_id'],$followup_status_id);
					}
					/*this is for change follwup status in Prospect,Inquiry,leads*/

					$invoice_id = $db->rp_getValue("payment_followup","reference_id","id='".$id."' AND isDelete=0",0);
				$reply=array("a"=>1,"mg"=>"Response Added Successfully!!","dmg"=>"Response Added Successfully!!","reference_id"=>$invoice_id);
				echo json_encode($reply);
			}
			else
			{
				$reply=array("a"=>0,"mg"=>"Followup Response Added Failed!!","dmg"=>"Followup Response Added Failed!!");
				echo json_encode($reply);	
			}
			
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Followup Response Required!!","dmg"=>"Followup Response Required!!");
			echo json_encode($reply);	
		}

		
	}
	else
	{
		$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Missing Service ER-2");
		echo json_encode($reply);
	}
}
else
{
	$reply=array("a"=>0,"mg"=>"Service Unavailable","dmg"=>"Missing parameters ER-1");
	echo json_encode($reply);
}
require_once("disconnect.php");
?>