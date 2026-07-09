<?php 

// echo "UPDATE `executive` SET client_code="",client_code_sr_by_type=0";exit;
echo "sds";exit;
$page_id=400;$page_slug='dashboard';
include('connect.php');
$ctableR = $db->rp_getData("executive","*","","",0);

$cnt=0;
$updated_cnt=0;
while ($ctableD = mysqli_fetch_assoc($ctableR)) 
{ 
	$cnt++;

	$client_code_prefix=$db->rp_getValue("company_master","prefix","id='".$ctableD['type_of_company']."' AND isDelete=0",0);
	$lastInsertIds=$db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_company='".$ctableD['type_of_company']."' AND isDelete=0",0); 
	// echo "-".$lastInsertIds.

	$lastInsertId=$lastInsertIds;
	// $lastInsertId=substr($lastInsertIds,4);

	$code=str_pad(($lastInsertId+1), 4, '0', STR_PAD_LEFT); 
	$client_code = $client_code_prefix.($code);
 
// 	$updateIds = $db->rp_update("executive",array("client_code"=>$client_code,"client_code_sr_by_type"=>$code),"id='".$ctableD['id']."'",0);
	if($updateIds)
	{
		$updated_cnt++;
	}
}
echo "Total Cnt=".$cnt;
echo "<br/>Updated Cnt=".$updated_cnt;

?>