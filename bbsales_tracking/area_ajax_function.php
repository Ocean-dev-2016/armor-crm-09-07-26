<?php
$page_id=606;$page_slug='page_area';
include("connect.php");
$ctable 	= "area";
if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="")
{
	$class_id=$_REQUEST['class_id'];
	$city_id=$_REQUEST['city_id'];
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
	{
		$service=$_REQUEST['mode'];
		if($service=="add_area")
		{
			$dup_where = "name= '".$db->clean($_REQUEST['area_name'])."' AND class_id='".$class_id."' AND isDelete=0";
			$r = $db->rp_dupCheck($ctable,$dup_where,0);
			if($r)
			{
				$response=array('ack'=>0,'ack_msg'=>'Area name already exists !!!');
				echo json_encode($response);
			}
			else
			{
				if(isset($_REQUEST['area_name']) && $_REQUEST['area_name']!="")
				{
					$area_slug=$db->rp_createslug($_REQUEST['area_name']);
					$area_name=$db->clean($_REQUEST['area_name']);
					$adate	= date('Y-m-d H:i:s');
					$rows=array("class_id","city_id","name","area_slug","isDelete");
					$values=array($class_id,$city_id,$area_name,$area_slug,0);
					$cbid=$db->rp_insert($ctable,$values,$rows,0);
					if($cbid!=0)
					{

						// $state_id = $db->rp_getValue("area","class_id","LOWER(name)='".trim(strtolower($area_name))."'",0);
						// $country_id = $db->rp_getValue("state","country_id","isDelete=0 AND id='".$state_id."'",0);
						// $rows_insert = array("country_id","state_id","city","name");
						// $value_insert = array($country_id,$class_id,$area_name,$area_name);
						// $insert = $db->rp_insert("city",$value_insert,$rows_insert,0);
						$response=array('ack'=>1,'ack_msg'=>'Area added Successfully !!!');
						echo json_encode($response);
					}
					else
					{
						$response=array('ack'=>0,'ack_msg'=>'Area name can not be empty !!!');
						echo json_encode($response);
					}
				}
				else
				{
					$response=array('ack'=>0,'ack_msg'=>'Area name can not be empty !!!');
					echo json_encode($response);
				}
			}
		}
		else if($service=="edit_area")
		{
				$dup_where = "class_id='".$_REQUEST['class_id']."' AND name = '".$_REQUEST['area_name']."' AND id!='".$_REQUEST['area_id']."' AND isDelete=0";
				$r = $db->rp_dupCheck($ctable,$dup_where,0);
				if($r)
				{
					$response=array('ack'=>0,'ack_msg'=>'Area name already exists !!!');
					echo json_encode($response);
				}
				else
				{
					if(isset($_REQUEST['area_id']) && $_REQUEST['area_id']!="" && isset($_REQUEST['area_name']) && $_REQUEST['area_name']!="")
					{
						$area_name=$_REQUEST['area_name'];
						$rows 	= array(
							"name"=>$area_name,				
						);
						$where	= "id='".$_REQUEST['area_id']."'";
						if($db->rp_update($ctable,$rows,$where,0))
						{
							// $area_name=$_REQUEST['area_name'];
							// $update_rows 	= array("city"=>$area_name,"name"=>$area_name);
							// $where	= "id='".$_REQUEST['area_id']."'";
							// $db->rp_update("city",$update_rows,$where,0);
							$response=array('ack'=>1,'ack_msg'=>'Area inforamtion edited Successfully !!!');
							echo json_encode($response);
						}	
						else
						{
							$response=array('ack'=>0,'ack_msg'=>'Area couldn\'t be edited try later !!!');
							echo json_encode($response);
						}	
					}
					else
					{
						$response=array('ack'=>0,'ack_msg'=>'Area name can\'t be empty or Area not found !!!');
						echo json_encode($response);			
					}
				}
		}
		else if($service=='delete_area')
		{
			if(isset($_REQUEST['area_id']) && $_REQUEST['area_id']!="")
			{
				$rows 	= array(
					"isDelete"	=> "1"
				);
				$where	= "id='".$_REQUEST['area_id']."'";
				if($db->rp_update($ctable,$rows,$where))
				{
					// $db->rp_update("city",$rows,$where);
					$response=array('ack'=>1,'ack_msg'=>'Area removed Successfully !!!');
					echo json_encode($response);
				}	
				else
				{
					$response=array('ack'=>0,'ack_msg'=>'Area can\'t be deleted !!!');
					echo json_encode($response);
				}					
			}
			else
			{
				$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
				echo json_encode($response);				
			}
		}		
		else
		{
			$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
			echo json_encode($response);
		}
	}
	else
	{
		$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
		echo json_encode($response);
	}
}
else
{
	$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
	echo json_encode($response);
}
require_once "disconnect.php";
?>