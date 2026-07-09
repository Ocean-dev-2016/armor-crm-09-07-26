<?php 
include('connect_in.php');
  
$cus_r = $db->rp_getData("executive","*"," customer_flag=0 AND isDelete=0 AND id!=-1 ","","",0);
while($cus_d = mysqli_fetch_assoc($cus_r))
{ 
	$lastInsertId=0;	
	if($cus_d['type_of_executive']==1)
	{
		$lastInsertId=$db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_executive=1",0);  
		$code=str_pad(($lastInsertId+1), 4, '0', STR_PAD_LEFT);
		$client_code = "SPSK".($code);
	}
	else if($cus_d['type_of_executive']==2)
	{
		$lastInsertId=$db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_executive=2");  
		$code=str_pad(($lastInsertId+1), 4, '0', STR_PAD_LEFT);
		$client_code = "RTL".($code);
	}
	else if($cus_d['type_of_executive']==3)
	{
		$lastInsertId=$db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_executive=3");  
		$code=str_pad(($lastInsertId+1), 4, '0', STR_PAD_LEFT);
		$client_code = "DEL".($code);
	}
	else if($cus_d['type_of_executive']==4)
	{
		$lastInsertId=$db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_executive=4");  
		$code=str_pad(($lastInsertId+1), 4, '0', STR_PAD_LEFT);
		$client_code = "PRM".($code);
	}
	else if($cus_d['type_of_executive']==6)
	{
		$lastInsertId=$db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_executive=6");  
		$code=str_pad(($lastInsertId+1), 4, '0', STR_PAD_LEFT);
		$client_code = "B2C".($code);
	}
	else if($cus_d['type_of_executive']==8)
	{
		$lastInsertId=$db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_executive=8");  
		$code=str_pad(($lastInsertId+1), 4, '0', STR_PAD_LEFT);
		$client_code = "EXP".($code);
	}
	else if($cus_d['type_of_executive']==9)
	{
		$lastInsertId=$db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_executive=9");  
		$code=str_pad(($lastInsertId+1), 4, '0', STR_PAD_LEFT);
		$client_code = "CRP".($code);
	}
 
	$db->rp_update("executive",array("client_code"=>$client_code,"client_code_sr_by_type"=>$code),"id='".$cus_d['id']."'");
}
?>
<?php require_once 'disconnect.php';  ?>