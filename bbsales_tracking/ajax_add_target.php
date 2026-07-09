<?php 
$page_id=585;$page_slug='meeting_master';
include("connect.php");
$ctable 	= "meeting_member";
$mode=$_REQUEST['mode'];
$sales_executive_id=$_REQUEST['sales_executive_id'];
$target_year=$_REQUEST['target_year'];
$target_month=$_REQUEST['target_month'];
$target_amount = $_REQUEST['target_amount'] != "" ? $_REQUEST['target_amount'] : "";
$target_quantity=$_REQUEST['target_quantity'];

if($mode=="add_target")
{
	$check_duplicate=0;
	$check_duplicate=$db->rp_getTotalRecord("target","target_year='".$target_year."' AND target_month='".$target_month."' AND sales_executive_id='".$sales_executive_id."' AND isDelete=0");
	
	if($check_duplicate==0)
	{	
		$rows=array("sales_executive_id","target_year","target_month","target_amount","target_quantity");
		$values=array($sales_executive_id,$target_year,$target_month,$target_amount,$target_quantity);
		$insert_id=$db->rp_insert("target",$values,$rows,0);
		if($insert_id>0)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Data Added Successfully");
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Data Added Failed");
		}
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Duplicate Record Found for the same Target");
	}

		echo json_encode($ack);
}

if($mode=="delete_member")
{
	$id=$_REQUEST['id'];
	$delete=$db->rp_update("target",array("isDelete"=>1),"id='".$id."'");
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