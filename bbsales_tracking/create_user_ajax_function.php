<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$id=$_REQUEST['salesId'];
$admin_type=$_REQUEST['admin_type'];
if($id!="")
{
	$user_count = $db->rp_getTotalRecord("dealer_distributor_network","sales_executive_id='".$id."' AND isDelete=0",0);
	if($user_count=='0')
	{
		$sales_DataR = $db->rp_getData("sales_executive","*","id='".$id."' AND isDelete=0 AND isActive=1","",0);
		$sales_DataD = mysqli_fetch_array($sales_DataR);
		$adate	= date('Y-m-d H:i:s');
		$rows 	= array(
			"name",
			"type",
			"admin_type",
			"user_id",
			"sales_executive_id",
			"username",
			"email",
			"password",
			"isDelete",
			"adate"
		);
		$values = array(
			$sales_DataD['name'],
			2,
			$admin_type,
			0,
			$id,
			$sales_DataD['username'],
			$sales_DataD['email'],
			$sales_DataD['password'],
			0,
			$adate
		);

		$Insert = $db->rp_insert("dealer_distributor_network",$values,$rows,0);	
		if($Insert)
		{
			$result=array('ack'=>1,'ack_msg'=>"System User Create Successfully..");
			echo '{"data":'.json_encode($result).'}';
		}
		else
		{
			$result=array("ack"=>0,"ack_msg"=>"User Creation Failed.Please Try Again..");
			echo '{"data":'.json_encode($result).'}';		
		}
	}
	else
	{
		$result=array("ack"=>0,"ack_msg"=>"No Such Data Found.Please Try Again..");
		echo '{"data":'.json_encode($result).'}';
	}
}
else
{
	$result=array("ack"=>0,"ack_msg"=>"Something went wrong!! Please Try again!!");
	echo '{"data":'.json_encode($result).'}';
}
?>
<?php require_once 'disconnect.php';  ?>