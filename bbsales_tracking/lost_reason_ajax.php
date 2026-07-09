<?php
$page_id=607;$page_slug='quotation';
include("connect.php");

$quotation_id = $_REQUEST['quotation_id'];
$lost_reason = $_REQUEST['lost_reason'];

	$rows=array(
		"status"      => 5,
		"lost_reason" => $lost_reason,
	);
	$update=$db->rp_update("quotation_detail",$rows,"isDelete=0 AND id='".$quotation_id."' ",0);
	if($update)
	{
		/*log entry*/
			$txt = "Lost";
			$quotation_no = $db->rp_getValue("quotation_detail","quotation_no","id='".$quotation_id."'");
			$customer_id = $db->rp_getValue("quotation_detail","customer_id","id='".$quotation_id."'");
			$ctable = "quotation_detail";
			$last_id = $quotation_id;
			$flag = "Web";
		    $module_name = "Quotation";
		    $log_description = $module_name." ".$quotation_no." Status Change To ". $txt." By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
		    $db->insertLog($ctable,$last_id,"update","",$insert,0,$log_description,$flag,$module_name,$user_id,$customer_id);
		/*log entry*/
		$rows_lead=array(
			"status"      => 11,
			"lost_reason" => $lost_reason,
		);
		$lead_id=$db->rp_getValue("quotation_detail","inquiry_id","isDelete=0 AND id='".$quotation_id."' ");
		$update_lead=$db->rp_update("no_order_inquiry",$rows_lead,"isDelete=0 AND id='".$lead_id."' ",0);
		if($update_lead)
		{
			/*log entry*/
				$txt = "Lost";
				$ctable = "no_order_inquiry";
				$last_id = $lead_id;
				$flag = "Web";
			    $module_name = "Lead";
			    $log_description = $module_name." #INQ/".$lead_id." Status Change To ". $txt." By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
			    $db->insertLog($ctable,$last_id,"update","",$insert,0,$log_description,$flag,$module_name,$user_id,"");
			/*log entry*/
			$reply=array("ack"=>1,"developer_msg"=>"Data insert successfully!!","ack_msg"=>"Data insert successfully!!");
            echo json_encode($reply);
        }
        else
        {
        	$reply=array("ack"=>1,"developer_msg"=>"Data insert failed!!","ack_msg"=>"Data insert failed!!");
            echo json_encode($reply);
        }
	}
	else
	{
		$reply=array("ack"=>1,"developer_msg"=>"Data insert failed!!","ack_msg"=>"Data insert failed!!");
            echo json_encode($reply);
	}

?>
<?php require_once 'disconnect.php';  ?>