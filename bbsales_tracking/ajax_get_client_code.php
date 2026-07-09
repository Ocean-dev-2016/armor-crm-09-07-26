<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
$zone_id        = $_REQUEST["zone_id"];
$company_id        = $_REQUEST["company_id"];
$mode=$_REQUEST['mode'];
$type_of_executive=$_REQUEST['type_of_executive'];
$id=$_REQUEST['id'];
//$code=$_REQUEST['last_id'];

// if($_REQUEST['mode']=='add')
// {
//if($zone_id!="")
if($company_id!="")
{ 
	$last_type_of_executive=$db->rp_getValue("executive","type_of_company","isDelete=0 AND id='".$_REQUEST['id']."'"); 
	if($company_id == $last_type_of_executive)
	{
		$client_code=$db->rp_getValue("executive","client_code","isDelete=0 AND id='".$_REQUEST['id']."'");
		$client_code_sr_by_type=$db->rp_getValue("executive","client_code_sr_by_type","isDelete=0 AND id='".$_REQUEST['id']."'");
	} 
	else
	{ 
		$client_code_prefix=$db->rp_getValue("company_master","prefix","id='".$company_id."' AND isDelete=0",0);
		$lastInsertIds=$db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_company='".$company_id."' AND isDelete=0",0); 

		// $lastInsertId=substr($lastInsertIds,4);

		$code=str_pad(($lastInsertIds+1), 4, '0', STR_PAD_LEFT);
		//echo $code;exit();
 		
		$client_code = $client_code_prefix.($code); 
	}
}
else
{
	$client_code="";
}

$result = array("client_code"=>$client_code,"client_code_sr_by_type"=>$code); 
echo json_encode($result);
?>
 