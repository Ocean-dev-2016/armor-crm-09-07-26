<?php 
$page_id=583;$page_slug='future_followup_manage';
include("connect.php");
include('../include/followup.class.php');
$ObjFollowup=new Followup();
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
		else if($followup_flag=="customer_followup")
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
			// added by shivani (if admin create then get sales from customer)
			if($followup_flag=="no_order_inquiry" || $followup_flag=="inquiry_followup")
			{
				$reference_table = "no_order_inquiry";
				$cuscol = "dealer_id"; 
			}
			if($followup_flag=="customer_inquiry" || $followup_flag=="leads_followup")
			{
				$reference_table = "customer_inquiry";
			}
			if($followup_flag=="request_followup")
			{
				$reference_table = "request";
			}
			if($followup_flag=="complain_followup")
			{
				$reference_table = "complain";
			}
			if($followup_flag=="followup")
			{
				$reference_table = "sales_executive";
			}
			if($followup_flag=="manual_invoice_import")
			{
				$reference_table = "manual_invoice_import";
				$cuscol = "customer_id";
			}
			if($followup_flag=="sales_executive")
			{
				$reference_table = "sales_executive";
			}
			if($followup_flag=="customer_followup")
			{
				$reference_table = "executive";
				$cuscol = "id";
			}
			if($followup_flag=="quotation_followup" || $followup_flag=="quotation_detail")
			{
				$reference_table = "quotation_detail";
				$cuscol = "customer_id";
			}
			if($user_id==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
			{
				$idx = $db->rp_getValue($reference_table,$cuscol,"id='".$inquiry_id."'",0);
				$user_id = $db->rp_getValue("executive","seid","id='".$idx."'",0);
				if($user_id=="")
				{
					$user_id=$_REQUEST['sales_id'];
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
	else if($m=='add_rating')
	{
		$rating=isset($_REQUEST['rating'])?$_REQUEST['rating']:"";
		$remark=isset($_REQUEST['remark'])?$_REQUEST['remark']:"";
		$id=isset($_REQUEST['visitor_id'])?$_REQUEST['visitor_id']:"";
		if($id!="" && $rating!="")
		{
			$values=array("rating"=>$rating,"remark"=>$remark);
			$update = $db->rp_update("visitor",$values,"id='".$id."'");
			if($update)
			{
				$reply=array("a"=>1,"mg"=>"Rating Updated Successfully!!","dmg"=>"Rating Updated Successfully!!");
				echo json_encode($reply);	
			}
			else
			{
				$reply=array("a"=>0,"mg"=>"Rating Updated Failed!!","dmg"=>"Rating Updated Failed!!");
				echo json_encode($reply);	
			}
			
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Rating Required!!","dmg"=>"Rating Required!!");
			echo json_encode($reply);	
		}
		
	}
	else if($m=='get_rating')
	{
		$id=isset($_REQUEST['visitor_id'])?$_REQUEST['visitor_id']:"";
		if($id!="")
		{
			$rating = $db->rp_getValue("visitor","rating","id='".$id."'");
			$remark = $db->rp_getValue("visitor","remark","id='".$id."'");
			if($rating)
			{
				$reply=array("a"=>1,"mg"=>"Rating Get Successfully!!","dmg"=>"Rating Added Successfully!!","rating"=>$rating,"remark"=>$remark);
				echo json_encode($reply);	
			}
			else
			{
			
			}
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Rating Not Found!!","dmg"=>"Rating Not Found!!");
			echo json_encode($reply);	
		}
	}
	else if($m=='delete_content')
	{
		
		$ctable_editor_home = "editor_home";
		$ctable_editor_home_item = "editor_home_item";
		$id=isset($_REQUEST['content_id'])?$_REQUEST['content_id']:"";
		if($id!="" && $id!=0)
		{
			$values = array("isDelete"=>1);
			$db->rp_update($ctable_editor_home,$values,"id='".$_REQUEST['content_id']."'");
			$db->rp_update($ctable_editor_home_item,$values,"home_id='".$_REQUEST['content_id']."'");
			$reply=array("a"=>1,"mg"=>"Section Delete Successfully!!","dmg"=>"Section Delete Successfully!!");
			echo json_encode($reply);	
			
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Section Delete Failed!!","dmg"=>"Section Delete Failed!!");
			echo json_encode($reply);	
		}
		
	}
	else if($m=='delete_content_item')
	{
		
		$ctable_editor_home = "editor_home_item";
		$ctable_editor_home_item = "editor_home_item";
		$id=isset($_REQUEST['content_id'])?$_REQUEST['content_id']:"";
		$channel_id=isset($_REQUEST['channel_id'])?$_REQUEST['channel_id']:"";
		$Total_channel = $db->rp_GetTotalRecord($ctable_editor_home_item,"home_id='".$id."' AND isDelete=0");
		if($Total_channel>1)
		{
			if($id!="" && $id!=0 && $channel_id!="" && $channel_id!=0)
			{
				$values = array("isDelete"=>1);
				$db->rp_update($ctable_editor_home_item,$values,"home_id='".$_REQUEST['content_id']."' AND channel_id='".$channel_id."'");
				$reply=array("a"=>1,"mg"=>"Section Item Delete Successfully!!","dmg"=>"Section Item Delete Successfully!!");
				echo json_encode($reply);	
				
			}
			else
			{
				$reply=array("a"=>0,"mg"=>"Section Item Delete Failed!!","dmg"=>"Section Item Delete Failed!!");
				echo json_encode($reply);	
			}
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"You Can Not Delete Last Channel!!","dmg"=>"You Can Not Delete Last Channel!!");
			echo json_encode($reply);	
		}
		
		
	}
	else if($m=='get_channel')
	{
		$q=isset($_REQUEST['q'])?$_REQUEST['q']:"";
		if($q!="")
		{
			$_REQUEST["show"]=(isset($_REQUEST["show"]))?$_REQUEST["show"]:100;
			$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;
			if(isset($_REQUEST["page"])){
			$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
			if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
			}else{
			$page_number = 1; //if there's no page number, set it to 1
			}
			$page_position = (($page_number-1) * $item_per_page);
			$limit=$page_position.", ".$item_per_page;
			$tag_details=array();
			
				$tag_details[]=array("type"=>"channel_name","sub_type"=>"","id"=>array($q));
			$data=$objChannel->getChannelFromQuery("accurate",$tag_details,0,array(),true,$limit);
			echo json_encode($data);	
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Section Title Required!!","dmg"=>"Section Title Required!!");
				echo json_encode($reply);	
		}
		
	}
	else if($m=='update_content_display_order')
	{
		$content=isset($_REQUEST['content'])?$_REQUEST['content']:"";
		if(!empty($content))
		{
			foreach($content as $c)
			{
				$DisplayOrder=$c['do'];
				$ContetnID=$c['cid'];
				$db->rp_update("editor_home",array("display_order"=>$DisplayOrder),"id='".$ContetnID."'");
			}
			$reply=array("a"=>0,"mg"=>"Section Display Order Updated","dmg"=>"Section Display Order Updated");
			echo json_encode($reply);	
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Section Title Required!!","dmg"=>"Section Title Required!!");
				echo json_encode($reply);	
		}
		
	}
	else if($m=='get_content_channel')
	{
		$content_id=isset($_REQUEST['content_id'])?$_REQUEST['content_id']:"";
		if($content_id!="")
		{
			$channels=$db->rp_getData("editor_home_item","*","home_id='".$content_id."' AND isDelete=0 AND isActive=1");
			if($channels)
			{
				while($channelDetail=mysqli_fetch_assoc($channels))
				{
					$channelDetail=$objChannel->getChannelDetail($channelDetail['channel_id']);
					?>
					<option selected title="<?php echo $channelDetail['channel_name'];?>" value="<?php echo $channelDetail['id']?>"><?php echo $channelDetail['channel_name'];?></option>
					<?php 
				}
			}
			
		}
		else
		{
			$reply=array("a"=>0,"mg"=>"Section Title Required!!","dmg"=>"Section Title Required!!");
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

					$invoice_id = $db->rp_getValue("followup","reference_id","id='".$id."' AND isDelete=0",0);
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