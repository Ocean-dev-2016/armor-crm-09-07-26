<?php 
include("connect.php");

if(isset($_POST['nPassword']) && isset($_POST['userId']))
{
	$table="sales_executive";
	$password=$_POST['nPassword'];
	$id=$_POST['userId'];
	$row=array("password"=>md5($password));
	$where="id=".$id;
	
	$r=$db->rp_update($table,$row,$where);
	if($r)
	{
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