<?php
$page_id=400;$page_slug='page_product';
include("connect.php");
$ctable = $_REQUEST['table'];
if($_REQUEST['display_order']!="" && $_REQUEST['id']!=0)
{
	$display_order=$_REQUEST['display_order'];
	$id=$_REQUEST['id'];

	$count=$db->rp_getTotalRecord($ctable,"id!='".$id."' AND display_order='".$display_order."' AND isDelete=0",0);
	if($count == 0)
	{
		$rows 	= array("display_order"=>$display_order);
		$where	= "id='".$id."'";
		$isUpdated = $db->rp_update($ctable,$rows,$where,0);
		if($isUpdated)
		{
			$response=array('ack'=>1,'ack_msg'=>'Data Update Successfully');
			echo json_encode($response);
		}
		else
		{
			$response=array('ack'=>0,'ack_msg'=>'Data Update Failed');
			echo json_encode($response);
		}
	}
	else
	{
		$response=array('ack'=>0,'ack_msg'=>'Already Available');
		echo json_encode($response);
	}
}
else
{
	$response=array('ack'=>0,'ack_msg'=>'data not found!!!');
	echo json_encode($response);
}
// include("disconnect.php"); 
?>
<?php include("disconnect.php"); ?>