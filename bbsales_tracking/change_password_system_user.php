<?php 
include("connect.php");

if(isset($_POST['nPassword']) && isset($_POST['userId']))
{
	$table="dealer_distributor_network";
	$password=$_POST['nPassword'];
	$id=$_POST['userId'];
	$row=array("password"=>md5($password));
	$where="id=".$id;
	$type= $db->rp_getValue($table,"type","id='".$id."'");
	$customer_id= $db->rp_getValue($table,"customer_id","id='".$id."'");
	$r=$db->rp_update($table,$row,$where,0);
	if($r)
	{
		if($type==3)
		{
			$db->rp_update("executive",$row,"id='".$customer_id."'");
		}
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