<?php
$page_id=515;$page_slug='page_inquiry';
include('connect.php');

$rm_unit_id = $_REQUEST['rm_unit_id'];

$units = $db->rp_getData("unit","*","isDelete=0","name ASC");
$results=array();
while ($unit = mysqli_fetch_assoc($units)) {
	if($unit['id']==$rm_unit_id){
		continue;
	}  
	$data = array("unit_id"=>$unit['id'],"name"=>$unit['name']);
	$results[]=$data;
}
echo json_encode(array("result"=>array("results"=>$results)),true);
?>