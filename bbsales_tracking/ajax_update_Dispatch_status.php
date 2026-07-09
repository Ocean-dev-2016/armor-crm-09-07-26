<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$id=$_REQUEST['id'];
$status=$_REQUEST['status'];
$table=$_REQUEST['table'];

$update_status=$db->rp_update($table,array("dispatch_status"=>$status),"id='".$id."'",0);
if($update_status)
{
	$replay=array("ack"=>1,"ack_msg"=>"Status Updated Successfully");
}
else
{
	$replay=array("ack"=>0,"ack_msg"=>"Status Updated Failed");
}
echo json_encode($replay);

?>
<?php require_once 'disconnect.php';  ?>