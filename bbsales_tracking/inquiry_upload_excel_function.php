<?php 
$page_id=605;$page_slug='customer_inquiry';
include('connect.php');
if(true)
{	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
	{
		// print_r($_FILES['discount_sheet']);exit;
		$service=$_REQUEST['mode'];
		require_once("../include/class.no_order_inquiry.php");
		$objInquiry= new NoOrderInquiry();
		if($service=="upload_discount")
		{		
			if(isset($_FILES['discount_sheet']) && $_FILES['discount_sheet']['error']==0)
			{			
				$ack=$objInquiry->uploadInquiry($_FILES['discount_sheet']);
			}
			else
			{
				$ack=array( "ack"=>0,
						"ack_msg"=>"File Not Found or Corrupted File",
					);
				
			}				
			$db->printJSON($ack);
		}
	}
	else
	{
		$ack=array( "ack"=>0,
				"ack_msg"=>"Internal error!!",
			);
		$db->printJSON($ack);
	}

}
else
{
	$ack=array( "ack"=>0,
				"ack_msg"=>"Internal error!!",
				"developer_msg"=>"Check your API Key or contact Admin",				
			);

	$db->printJSON($ack);
}
require_once 'disconnect.php'; 
?>		