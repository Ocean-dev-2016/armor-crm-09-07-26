<?php 
//file_get_contents("https://cmkcrm.cmkgroup.in/service/service_crone.php?key=1226&s=177"); 

// Connect to Database
/*include('connect.php');
$Start_Time=date("Y-m-d");
$endtime=date('Y-m-d', strtotime('+1 day', strtotime($Start_Time)));
$trade_json=file_get_contents('https://www.tradeindia.com/utils/my_inquiry.html?userid=4922269&profile_id=6598457&key=23d1ce6c4fe90c10aa80e5860dcd51d3&from_date='.$Start_Time.'&to_date='.$endtime);					
$result=($trade_json!="")?(array)json_decode($trade_json,true):array();
//print_r($result);exit;
$count_total=sizeof($result);
$total_insert=0;
$total_not_insert=0;
foreach($result as $V)
{
	$rfi_id=$V['rfi_id'];
	$r=$db->rp_getTotalRecord("no_order_inquiry","rfi_id='".$rfi_id."' AND isDelete=0");
	if($r==0)
	{		
		$mobile_number=(isset($V['sender_mobile']) && $V['sender_mobile']!="")?$V['sender_mobile']:"";
		$country=(isset($V['sender_country']) && $V['sender_country']!="")?$V['sender_country']:"";
		$state=(isset($V['sender_state']) && $V['sender_state']!="")?$V['sender_state']:"";
		$city=(isset($V['sender_city']) && $V['sender_city']!="")?$V['sender_city']:"";
		$remark=(isset($V['message']) && $V['message']!="")?$V['message']:"";
		$customer_requirement=(isset($V['message']) && $V['message']!="")?$V['message']:"";
		$email_address=(isset($V['sender_email']) && $V['sender_email']!="")?$V['sender_email']:"";
		$address=(isset($V['address']) && $V['address']!="")?$V['address']:"";
		$whatsapp_number=(isset($V['sender_other_mobiles']) && $V['sender_other_mobiles']!="")?$V['sender_other_mobiles']:"";
		$contact_person=(isset($V['sender_name']) && $V['sender_name']!="")?$V['sender_name']:"";
		$company_name=(isset($V['sender_name']) && $V['sender_name']!="")?$V['sender_name']:"";
		$date_of_call=(isset($V['generated_date']) && $V['generated_date']!="")?date("Y-m-d",strtotime($V['generated_date'])):"";

		// new Added
			$classArea = $db->getCassAreaIdFromName($state,$city);
			$class_id = $classArea['class_id'];
			$area_id = $classArea['area_id'];
		// new Added

		$sales_id = $db->rp_getValue("sales_executive_map_area","sales_executive_id","class_id='".$class_id."' AND area_id='".$area_id."' AND executive_type!='sales_manager' AND isDelete=0",0);
		$inquiry_assign_to = ($inquiry_assign_to!="" || $inquiry_assign_to!=0)?$inquiry_assign_to:4;
		$inquiry_created_by = ($inquiry_created_by!="" || $inquiry_created_by!=0)?$inquiry_created_by:4;
		$sales_executive_id = ($sales_executive_id!="" || $sales_executive_id!=0)?$sales_executive_id:4;

		$rows 	= array(
				"company_name",
				"person_name",
				"mobile_number",
				"other_mobile_no",
				"date_of_call",
				"datetime",
				"inquiry_date",
				"country",
				"state",
				"city",
				"executive_type",
				"source_of_inquiry",
				"sales_executive_id",
				"inquiry_created_by",
				"inquiry_assign_to",
				"description",
				"quotation_flag",
				"customer_requirement",
				"email_address",
				"address",
				"rfi_id",
				"dealer_id",
			);
		$values = array(
				$company_name,
				$contact_person,
				$mobile_number,
				$whatsapp_number,
				$date_of_call,
				$date_of_call,
				$date_of_call,
				$country,
				$state,
				$city,
				4,
				6,
				$sales_executive_id,
				$inquiry_created_by,
				$inquiry_assign_to,
				addslashes($remark),
				0,
				addslashes($customer_requirement),
				$email_address,
				addslashes($address),
				$rfi_id,
				-1
			);

		$uid = $db->rp_insert("no_order_inquiry",$values,$rows,0);
		if($uid)
		{	
			//india mart inquiry notification
				if($inquiry_assign_to!="" && $inquiry_assign_to!=0)
				{
					$inquiry_assign_name = $db->rp_getValue("sales_executive","name","id='".$inquiry_assign_to."'",0);
					$type = "inquiry";
					$title   = "India Mart Inquiry Assigned To ".$inquiry_assign_name.;
					$inq_no  = $db->getLastInsertId("no_order_inquiry");		
					$body    = "India Mart Inquiry Assigned To ".$inquiry_assign_name." ON ".date("Y-m-d H:i:s");
					$click_action="no_order_inquiry_grid.php?type=0";
					
					$Data = [
						'type'	=> $type,
			            'title' => $title,
			            'body' =>  $body,
			            'description' => $body,
			            "user_id"  => $inquiry_assign_to,
			            "reference_id"   => $uid,
						"item_id"        => $uid,
						"reference_type" => 'no_order_inquiry',
			            'icon' => NOTIFICATIONICON,
			            'image' => NOTIFICATIONIMAGE,
			            'click_action'=> ADMINSITEURL.$click_action,
					];

					$ReferanceArray = [
			            'reference_id' => 	$uid,
			            'reference_table' => "no_order_inquiry",
					];

					$id = $inquiry_assign_to;
			    	if($id!="")
				    {
					    //panel
					    $Upperlevel1 = '1';
					    $UpperlevelAll = '1';
						$db->send_notificationpanel($Data,$id,$ReferanceArray,$Upperlevel1,$UpperlevelAll);
					    //panel
					}
				}
			//india mart inquiry notification
			$total_insert++;			
		}
		else
		{
			$total_not_insert++;
		}

	}
}
if($total_insert==$count_total)
{
	$rows1=array(
		"type",
		"date_time"
	);
	$values1=array(
		"trade_india",
		$enddttime
	);
	$insert=$db->rp_insert("leads_cron_data",$values1,$rows1,0);
	$response=array('ack' => 1,'ack_message' =>'Data Insert Successfully',"total_record"=>$count_total,"total_insert_record"=>$total_insert,"total_not_insert_record"=>$total_not_insert);
}
else
{
	$response=array('ack' => 0,'ack_message' =>'Not Insert',"total_record"=>$count_total,"total_insert_record"=>$total_insert,"total_not_insert_record"=>$total_not_insert);
}
echo json_encode($response);*/
?>