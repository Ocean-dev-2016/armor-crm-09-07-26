<?php
$page_id=405;$page_slug='app_pages';
include("connect.php");
$ctable 	= "customer_branch";
if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
{
	$service=$_REQUEST['mode'];
	if($service=="check_page_status")
	{
		if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="")
		{
			$page_id=$_REQUEST['pid'];
			$page_urls=explode(",",$db->rp_getValue("page_table","page_urls","id='".$page_id."'"));
			$result="";
			foreach($page_urls as $pu)
			{
				$result.=$db->getLabel($pu,SITEURL.ADMINFOLDER."/".$pu,"auto");
			}
			$response=array('ack'=>1,'ack_msg'=>'Page Status Available Now !!!',"result"=>$result);
			echo json_encode($response);				
		}
		else
		{
			$response=array('ack'=>0,'ack_msg'=>'Page id not found !!!');
			echo json_encode($response);
		}
		
	}				
	else
	{
		$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
		echo json_encode($response);
	}
}
else
{
	$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
	echo json_encode($response);
}
require_once 'disconnect.php'; 
?>