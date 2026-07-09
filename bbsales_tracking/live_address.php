<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");

$r=$db->rp_getData("salesexecutive_tracking","longitude,latitude,id","isDelete=0");
if($r)
{
	while($d=mysqli_fetch_assoc($r))
	{
		$addr=$db->getAddress($d['latitude'],$d['longitude']);
		$db->rp_update("salesexecutive_tracking",array("app_address"=>$addr),"id='".$d['id']."'",0);
	}
}
?>