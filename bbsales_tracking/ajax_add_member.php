<?php 
$page_id=585;$page_slug='meeting_master';
include("connect.php");
$ctable 	= "meeting_member";
$mode=$_REQUEST['mode'];
$meeting_id=$_REQUEST['meeting_id'];
$member_name=$_REQUEST['member_name'];
$member_phone=$_REQUEST['member_phone'];

if($mode=="add_member")
{
	$check_duplicate=0;
	$check_member=$db->rp_getTotalRecord("member","mobile_no='".$member_phone."' AND isDelete=0");
	if($check_member==0)
	{
		// add new member
		$row=array("mobile_no","name");
		$value=array($member_phone,$member_name);
		$member_id=$db->rp_insert("member",$value,$row,0);
	}
	else
	{
		$member_id=$db->rp_getValue("member","id","mobile_no='".$member_phone."' AND isDelete=0");
		$check_duplicate=$db->rp_getTotalRecord("meeting_member","member_id='".$member_id."' AND meeting_id='".$meeting_id."' AND isDelete=0",0);
	}
	if($check_duplicate==0)
	{		
		$rows=array("meeting_id","member_id");
		$values=array($meeting_id,$member_id);

		$insert_id=$db->rp_insert("meeting_member",$values,$rows,0);
		if($insert_id>0)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Member Added Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Member Added Failed");
		}
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Duplicate Member Found for the same Meeting");
	}

		echo json_encode($ack);
}
if($mode=="delete_member")
{
	$id=$_REQUEST['id'];
	$delete=$db->rp_update("meeting_member",array("isDelete"=>1),"id='".$id."'");
	if($delete)
	{
		$ack=array("ack"=>1,"ack_msg"=>"Member Delete Successfully");
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Member Delete Failed");
	}

	echo json_encode($ack);
}
require_once 'disconnect.php'; 
?>