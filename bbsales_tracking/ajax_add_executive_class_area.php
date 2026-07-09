<?php 
$page_id=555;$page_slug='page_executive';
include("connect.php");
$ctable 	= "executive_map_area";
$mode=$_REQUEST['mode'];

$executive_id=$_REQUEST['executive_id'];
$class_id = $_REQUEST['class_id'];
$areaids  = $_REQUEST['area_id'];
$city_id  = $_REQUEST['city_id'];
$type  = $_REQUEST['type'];

$type_of_executive=$type;
if (sizeof($city_id)==1) {
	if (in_array(0, $city_id)) {
		$city_id = "";
	}
}
if($mode=="add_area")
{	
	if($areaids=="" && $city_id != "" )
	{
		$area_ids1 = array();
		$city_arr=implode(",",$city_id);
		$getarea = $db->rp_getData("area","*","class_id='".$class_id."'  AND city_id IN (".$city_arr.") AND isDelete=0","",0);
		while($getarea_d = mysqli_fetch_assoc($getarea))
		{
			$area_ids1[] = $getarea_d['id'];
		}
		
		for($j=0;$j<sizeof($area_ids1);$j++)
		{
			$item_area[]=array("area_id"=>$area_ids1[$j]);
		}

		foreach ($item_area as $K) {
			
			$ares_ids1 = $K['area_id'];
			$city_id_r=$db->rp_getValue("area","city_id","isDelete=0 AND id='".$ares_ids1."'",0);

			$dup_where1 = "class_id = '".$class_id."' AND  area_id = '".$ares_ids1."' AND city_id IN (".$city_arr.") AND executive_id='".$executive_id."' AND isDelete=0";

			$r1 = $db->rp_dupCheck("executive_map_area",$dup_where1,0);
			if($r1>0){
				$ack=array("ack"=>0,"ack_msg"=>"This Class And Area Are Already Added.");
			}
			else
			{
				$mapping_id = $db->rp_insert("executive_map_area",array($executive_id,$type_of_executive,$class_id,$ares_ids1,$city_id_r),array("executive_id","executive_type","class_id","area_id","city_id"),0);
			}
		}
	}
	else if ($areaids=="" && $city_id == "")
	{
		// exit("hello");
		$area_ids1 = array();
		$city_ids_r = $db->rp_getData("city","id","state_id IN (".$class_id.") AND isDelete=0","",0);
		while($city_ids_d = mysqli_fetch_assoc($city_ids_r))
		{
			$city_id_arr[] = $city_ids_d['id'];
		}
		$city_arr=implode(",",$city_id_arr);
		
		$getarea = $db->rp_getData("area","*","class_id='".$class_id."'  AND city_id IN (".$city_arr.") AND isDelete=0","",0);
		while($getarea_d = mysqli_fetch_assoc($getarea))
		{
			$area_ids1[] = $getarea_d['id'];
		}
		
		
		for($j=0;$j<sizeof($area_ids1);$j++)
		{
			$item_area[]=array("area_id"=>$area_ids1[$j]);
		}

		foreach ($item_area as $K) {
			
			$ares_ids1 = $K['area_id'];
			$city_id_r=$db->rp_getValue("area","city_id","isDelete=0 AND id='".$ares_ids1."'",0);

			$dup_where1 = "class_id = '".$class_id."' AND  area_id = '".$ares_ids1."' AND city_id IN (".$city_arr.") AND executive_id='".$executive_id."' AND isDelete=0";

			$r1 = $db->rp_dupCheck("executive_map_area",$dup_where1,0);
			if($r1>0){
				$ack=array("ack"=>0,"ack_msg"=>"This Class And Area Are Already Added.");
			}
			else
			{
				$mapping_id = $db->rp_insert("executive_map_area",array($executive_id,$type_of_executive,$class_id,$ares_ids1,$city_id_r),array("executive_id","executive_type","class_id","area_id","city_id"),0);
			}
		}
	}
	else
	{
		$size[]=sizeof($areaids);
		$value_check=sizeof($areaids);
		if(in_array($value_check,$size))
		{
			$isValidArray=true;
		}
		else
		{
			$isValidArray=false;
		}

		if($isValidArray && !empty($areaids) )
		{
			for($i=0;$i<sizeof($areaids);$i++)
			{
				$item[]=array("area_id"=>$areaids[$i]);
			}
		}

		foreach ($item as $i) {
			$ares_ids = $i['area_id'];

			$city_r=$db->rp_getValue("area","city_id","isDelete=0 AND id='".$ares_ids."'",0);

			$dup_where = "class_id = '".$class_id."' AND  area_id = '".$ares_ids."' AND city_id='".$city_r."' AND executive_id='".$executive_id."' AND isDelete=0";
			
			$r = $db->rp_dupCheck("executive_map_area",$dup_where,0);

			if($r>0 || $r !="" ){

				$ack=array("ack"=>0,"ack_msg"=>"This Class And Area Are Already Added.");
			}
			else
			{
				$mapping_id=$db->rp_insert("executive_map_area",array($executive_id,$type_of_executive,$class_id,$ares_ids,$city_r),array("executive_id","executive_type","class_id","area_id","city_id"),0);
				
			}
		}
	}

	if($mapping_id)
	{
		$ack=array("ack"=>1,"ack_msg"=>"Data Added Successfully");
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"This Class And Area Are Already Available");
	}
	echo json_encode($ack);
}

if($mode=="delete_class_area")
{
	$id=$_REQUEST['id'];
	$delete=$db->rp_update("executive_map_area",array("isDelete"=>1),"id='".$id."'");
	if($delete)
	{
		$ack=array("ack"=>1,"ack_msg"=>"Data Delete Successfully");
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Data Delete Failed");
	}
	echo json_encode($ack);
}
require_once 'disconnect.php'; 
?>