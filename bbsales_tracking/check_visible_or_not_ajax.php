<?php
$page_id=400;$page_slug='page_product';
include("connect.php");

// echo "<pre>"; print_r($_REQUEST); exit;

if($_REQUEST['id']!="" && $_REQUEST['id']!=0)
{
	$id=$_REQUEST['id'];

	$count=$db->rp_getTotalRecord("product","id IN (".$id.") AND isDelete=0",0);
	
	if($count != 0)
	{
		$rows 	= array("isVisible"=>$_REQUEST['isVisible']);
		$where	= "id IN (".$id.")";
		$isUpdated = $db->rp_update("product",$rows,$where,0);
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
}
?>
<?php require_once 'disconnect.php';  ?>