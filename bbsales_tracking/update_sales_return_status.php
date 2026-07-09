<?php
$page_id=588;$page_slug='add_invoice';
include("connect.php");
$invoice_id	= $_REQUEST['invoice_id'];
$status	= $_REQUEST['status'];
$customer_id = $db->rp_getValue("sales_return","customer_id","id='".$invoice_id."'");
$invoice_no = $db->rp_getValue("sales_return","invoice_no","id='".$invoice_id."'");
// $update = $db->rp_update("sales_return",array("status"=>$status),"id='".$invoice_id."'",0);
if($status==1)
{
	$txt="Approve";
	/*log entry*/
	$ctable = "sales_return";
	$last_id = $invoice_id;
	$flag = "Web";
    $module_name = "Sales Return";
    $log_description = $module_name." ".$invoice_no." Status Change To ". $txt." By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
    $db->insertLog($ctable,$last_id,"status_change","",$insert,0,$log_description,$flag,$module_name,$user_id,$customer_id);
	/*log entry*/
}
else if($status==-2)
{
	$txt="Dispprove";
	/*log entry*/
	$ctable = "sales_return";
	$last_id = $invoice_id;
	$flag = "Web";
    $module_name = "Sales Return";
    $log_description = $module_name." ".$invoice_no." Status Change To ". $txt." By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
    $db->insertLog($ctable,$last_id,"status_change","",$insert,0,$log_description,$flag,$module_name,$user_id,$customer_id);
	/*log entry*/
}
else if($status==3)
{
	$txt="Cancel";
	/*log entry*/
	$ctable = "sales_return";
	$last_id = $invoice_id;
	$flag = "Web";
    $module_name = "Sales Return";
    $log_description = $module_name." ".$invoice_no." Status Change To ". $txt." By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
    $db->insertLog($ctable,$last_id,"status_change","",$insert,0,$log_description,$flag,$module_name,$user_id,$customer_id);
	/*log entry*/

	/*delete Customer Leager */
	$db->rp_update("account_transaction",array("isDelete"=>1),"reference_id='".$invoice_id."' AND reference_table='sales_return'",0);
	/*delete Customer Leager */
}
/*if($update)
{*/
	if($status==1)
	{ 
		$invoice_data = $db->rp_getData("sales_return","*","id='".$invoice_id."' AND isDelete=0");
		$invoice_data_d=mysqli_fetch_assoc($invoice_data);
		
		// $dispatch_data = $db->rp_getData("dispatch_detail","*","id IN (".$invoice_data_d['dispatch_ids'].") AND isDelete=0");

		
		$isVaid = true; 
		if($isVaid)
		{
			$db->rp_update("dispatch_detail",array("status"=>1),"id IN (".$invoice_data_d['dispatch_ids'].")");
			
			//update invoice number
			//$new_id = ($invoice_id - 1);
			$invoice_date = date('d-m-Y');
			$invoice_no     = $db->rp_getValue("sales_return","MAX(`invoice_sr_no`)","isDelete=0",0);
			$invoice_no2    = $invoice_no+1;
			$final_invoice_no  = OUTLETS_INVOICE_NO . str_pad($invoice_no2, 2, '0', STR_PAD_LEFT);
			$UPDATE         = $db->rp_update("sales_return",array("invoice_no"=>$final_invoice_no,"invoice_sr_no"=>$invoice_no2,"invoice_date"=>date('Y-m-d',strtotime($invoice_date))),"id='".$invoice_id."'",0);
			//update invoice number
			
			// $update = $db->rp_update("sales_return",array("status"=>$status),"id='".$invoice_id."'",0);
			$update = $db->rp_update("sales_return",array("status"=>$status),"id='".$invoice_id."'",0);
			// $dispatch_id = $db->rp_getValue("sales_return","dispatch_ids","id='".$invoice_id."'",0);
			// $db->rp_update("dispatch_detail",array("status"=>1),"id='".$dispatch_id."'",0);
		}
		else
		{
			$update = false;
		}
	}
	else
	{
		$update = $db->rp_update("sales_return",array("status"=>$status),"id='".$invoice_id."'",0);
	}
	if($update)
	{
		if($status==1)
		{
			require_once('../include/class.system.php');
			$system = new System();
			
			$AccountInfo=$db->rp_getData("account","*","cid='".$invoice_data_d['customer_id']."'","",0);
			$AccountInfo=mysqli_fetch_assoc($AccountInfo);
			// $AccountInfo=$system->GetAccountInfo($invoice_data_d['customer_id']);
			if($AccountInfo)
			{
				$AccountID=$AccountInfo['id'];
				$AccountNo=$AccountInfo['acc_no'];
				$Columns=array("cid","account_id","account_no","type","credit","amount","reference_id","reference_table","description","payment_date");
				$credit=$db->rp_getValue("sales_return","grand_total_rounded","id='".$invoice_id."'",0);
				$grand_total=$db->rp_getValue("sales_return","grand_total_rounded","id='".$invoice_id."'",0);
				$payment_date=date('Y-m-d');
				$payment_type = 0;
				$remark = "Sales Return Entry Of Sales Return No. <a target='_blank' href='sales_return_viewer.php?sales_return_id=".$invoice_id."'>". $db->rp_getValue("sales_return","invoice_no","id='".$invoice_id."'",0)."</a>";
				$Values=array($invoice_data_d['customer_id'],$AccountID,$AccountNo,$payment_type,$credit,$grand_total,$invoice_id,"sales_return",$remark,$payment_date);
				/*entry account transaction*/
				$TransctionID=$db->rp_insert("account_transaction",$Values,$Columns,0);
			}
		}
		$reply=array("ack"=>1,"ack_msg"=>"Sales Return ".$txt." Successfully");
	}
	else
	{
		if($isVaid)
		{
			$reply=array("ack"=>0,"ack_msg"=>"Sales Return ".$txt." Fail");
		}
		else
		{
			$reply=array("ack"=>0,"ack_msg"=>"Sales Return ".$txt." Fail!!, Some item dose not have stock please check stock first!!");
		}
	}
/*}
else
{
	$reply=array("ack"=>0,"ack_msg"=>"Invoice ".$txt." Updated Failed");
}*/
/*Notification code*/

    require_once("../include/push_notification.class.php");
    $objPushNotification= new PushNotification();
    
    $user_id = $db->rp_getValue("sales_return","sales_id","id='".$invoice_id."'");
    $invoice_no  = $db->rp_getValue("sales_return","invoice_no","id='".$invoice_id."'");
    $notification_title="Invoice Status Change";
    $notification_description = "Your Invoice Status is ".$txt." for Invoice No ".$invoice_no;
    $notification_type="1";
    $type_slug="";

    $rows 	= array("user_id","referance_id","referance_type","notification_title","notification_description","notification_type","type_slug");
    $values = array($user_id,$invoice_id,"sales_return",$notification_title,$notification_description,$notification_type,$type_slug);
    $insert = $db->rp_insert("notification",$values,$rows,0);

	$msg = array(
			"type"		     => 'invoice',
			"title"		     => $notification_title,
			"description"    => $notification_description,
			"user_id"        => $user_id,
			"reference_id"   => $invoice_id,
			"item_id"        => $invoice_id,
			"reference_type" => 'sales_return',
		);
	$where="refreshToken!='' AND id='".$user_id."'";
	$refreshTokens[]=$db->rp_getValue("sales_executive","refreshToken",$where,0);
	$result=$objPushNotification->send_notification1($msg,$refreshTokens,1);
/*Notification code*/
echo json_encode($reply);
?>