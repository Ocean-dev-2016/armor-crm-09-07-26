<?php
$page_id=565;$page_slug='page_order';
include("connect.php");
$order_id=$_REQUEST['order_id'];
require_once('../include/class.system.php');
$system = new System();
$lr_number = $_REQUEST['lr_number'];


	if (isset($_FILES["file_path"]))
    {

    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");

    	$temp = explode(".", $_FILES["file_path"]["name"]);

    	$extension = end($temp);

    	$fileName 	= $db->clean($_FILES["file_path"]["name"]);
    
    	if($fileName!="")
    	{
    		$fileSize 	= round($_FILES["file_path"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $fileName));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$LR_copy	= 'lr_documents'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= LRCOPY_A.$LR_copy;
	    	$_FILES['file_path']['tmp_name'];
	    	move_uploaded_file($_FILES['file_path']['tmp_name'], $filePath);
	    	$new_image=true; 
    	}  
    	else
    	{
    		$LR_copy="";
    	} 
    }
    else
    {
    	$LR_copy="";
    
    }
    	if($LR_copy!="" && $lr_number!="")
    	{

    	$rows = array("lr_number" => $lr_number,"lr_image" => $LR_copy);
		$add_lr_image = $db->rp_update("orders",$rows,"id = ".$order_id,0);
		$update = $db->rp_update("orders",array("status"=>7),"id='".$order_id."'",0);

		$reply=array("ack"=>1,"developer_msg"=>"Update Successfully.","ack_msg"=>"Update Successfully.");
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Something Went Wrong!!!","ack_msg"=>"Something Went Wrong");
		}
		echo json_encode($reply);

?>