<?php

$page_id=593;$page_slug='attendance_page';
include("connect.php");


$m=$_REQUEST['m'];
$id=$_REQUEST['id'];

// echo "<pre>"; print_r($_REQUEST); exit;

if($id!="" && $m!="")
{	
    
	if($m=="delete_attendance")
	{	

		if($id!="")
		{
		    
	        $row = array("isDelete"=> 1,);
	       	$Where = "id='".$id."'";
	       	$update = $db->rp_update("attendance",$row,$Where,0);

	        $reply=array("ack"=>1,"ack_msg"=>"Attendance Deleted Successfully");
		}
	}
}
else
{
	$reply=array("ack"=>0,"ack_msg"=>"Something went wrong!! Please Try again!!");
}
require_once("disconnect.php");
echo json_encode($reply);
?>