<?php 

include("connect.php");
if(isset($_POST['nPassword']) && isset($_POST['userId']))
{
	$table="executive";
	$password=$_POST['nPassword'];
	$id=$_POST['userId'];
	$row=array("password"=>md5($password),"update_password_flag"=>1);
	$where="id=".$id;
	
	$r=$db->rp_update($table,$row,$where);
	if($r)
	{
		$db->rp_update("dealer_distributor_network",$row,"customer_id='".$id."' AND type=3");
		$result=array('ack'=>1,'ack_msg'=>"Password successfully changed !!");
	}
	else
	{
		$result=array('ack'=>1,'ack_msg'=>"Password Change Unsuccessfull !!");
	}
	echo '{"data":'.json_encode($result).'}';
}
else
{
	$result=array('ack'=>1,'ack_msg'=>"Password Change Unsuccessfull !!");
	echo '{"data":'.json_encode($result).'}';
}

?>