<?php
$page_id=669;$page_slug='payment_followup_manage';
$ctable     = "payment_followup";
$ctable1    = "Payment Followup";

include("connect.php");
require_once("../include/payment_followup_class.php");
$objFollowup= new FollowupPayment();


if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
  //   if($rights['delete_flag']!=1)
		// {
		// 	$db->rp_location('access_denied.php?msg=delete_access_denied');
		// }
		// echo "okkkk"; exit;
		$detail['id']=$_REQUEST['id'];
		$followup_type=$_REQUEST['followup_type'];
		$reply=$objFollowup->DeleteFollowup($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location("payment_followup_manage.php?followup_type=".$followup_type);
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}




if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="payment_followup"){
  //   if($rights['delete_flag']!=1)
		// {
		// 	$db->rp_location('access_denied.php?msg=delete_access_denied');
		// }
		// echo "mmm"; exit;
		$detail['id']=$_REQUEST['id'];
		$quotation_id=$_REQUEST['quotation_id'];
		$sales_id=$_REQUEST['sales_id'];
		$reply=$objFollowup->DeleteFollowup($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location("payment_followup_manage.php?mode=".$_REQUEST['mode']."&sales_id=".$sales_id);
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}

?>