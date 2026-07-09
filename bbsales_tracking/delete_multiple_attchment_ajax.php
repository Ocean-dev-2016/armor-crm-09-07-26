<?php

//var_dump($_REQUEST);exit;
$page_id=555;$page_slug='page_executive';
include("connect.php");
//echo "ravi"; exit;
$complain_id = $_REQUEST['complain_id'];
$flag = $_REQUEST['flag'];
$media_id = $_REQUEST['media_id'];
$media_id_array="";
 if (isset($complain_id) && !empty($complain_id) && isset($media_id) && !empty($media_id))
 { 
	if ($flag == 'delete_complain_image') 
	{
		
		$rows = array("isDelete"=>1,);

		$db->rp_update("media",$rows,"reference_id='".$_REQUEST["complain_id"]."'AND id='".$media_id."'",0);

		// Remove Media id from customer table

		$media_id_array=$db->rp_getValue("complain","image_path","id='".$_REQUEST["complain_id"]."'",0);

		$media_id_array = explode(",", $media_id_array);

		$del_val=$media_id;
		if (($key = array_search($del_val, $media_id_array)) !== false) {
		    unset($media_id_array[$key]);
		}
		
		$media_id_array = implode(",", $media_id_array);
		$rows = array("image_path"=>$media_id_array,);

		$db->rp_update("complain",$rows,"id='".$_REQUEST["complain_id"]."'",0);

		$reply = array("ack"=>1,"ack_msg"=>"Complain Image Delete successfully ");
		
	}
	else
	{
		$reply = array("ack"=>0,"ack_msg"=>"Required Flag not available Please check.....");
	}
}
else
{
	$reply = array("ack"=>0,"ack_msg"=>"Required complain_id & media_id not available Please check.....");
}
echo json_encode($reply);
?>

