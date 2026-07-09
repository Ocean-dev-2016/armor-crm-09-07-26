<?php
$page_id=583;$page_slug='page_followup';
$ctable     = "followup";
$ctable1    = "followup";

include("connect.php");
require_once("../include/followup.class.php");
$objFollowup= new Followup();


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
		$db->rp_location("followuplist_manage.php?followup_type=".$followup_type);
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}

if(isset($_REQUEST['inquiry_id']) && $_REQUEST['inquiry_id']>0 && $_REQUEST['mode']=="inquiry_followup"){
  //   if($rights['delete_flag']!=1)
		// {
		// 	$db->rp_location('access_denied.php?msg=delete_access_denied');
		// }
		// echo "mmm"; exit;
		$detail['id']=$_REQUEST['id'];
		$inquiry_id=$_REQUEST['inquiry_id'];
		$sales_id=$_REQUEST['sales_id'];
		$reply=$objFollowup->DeleteFollowup($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location("followup.php?mode=".$_REQUEST['mode']."&inquiry_id=".$inquiry_id."&sales_id=".$sales_id);
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}

if(isset($_REQUEST['quotation_id']) && $_REQUEST['quotation_id']>0 && $_REQUEST['mode']=="quotation_followup"){
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
		$db->rp_location("followup.php?mode=".$_REQUEST['mode']."&quotation_id=".$quotation_id."&sales_id=".$sales_id);
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="followup"){
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
		$db->rp_location("followup.php?mode=".$_REQUEST['mode']."&sales_id=".$sales_id);
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}

?>