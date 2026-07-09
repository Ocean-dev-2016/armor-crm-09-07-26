<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$id=$_REQUEST['id'];
$status=$_REQUEST['status'];
$table=$_REQUEST['table'];

$update_status=$db->rp_update($table,array("complain_assign_to"=>$status),"id='".$id."'");
if($update_status)
{
	$replay=array("ack"=>1,"ack_msg"=>"Data Updated Successfully");
}
else
{
	$replay=array("ack"=>0,"ack_msg"=>"Data Updated Failed");
}
echo json_encode($replay);

?>
<?php require_once 'disconnect.php';  ?>