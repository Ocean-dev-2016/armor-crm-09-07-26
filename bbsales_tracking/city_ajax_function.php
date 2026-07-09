<?php
$page_id=606;$page_slug='page_city';
include("connect.php");
$ctable 	= "city";
if(isset($_REQUEST['state_id']) && $_REQUEST['state_id']!="")
{
	$state_id=$_REQUEST['state_id'];
	

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
	{
		$service=$_REQUEST['mode'];
		if($service=="add_city")
		{
			$dup_where = "name= '".$db->clean($_REQUEST['city_name'])."' AND state_id='".$state_id."' AND isDelete=0";
			$r = $db->rp_dupCheck($ctable,$dup_where,0);
			if($r)
			{
				$response=array('ack'=>0,'ack_msg'=>'city name already exists !!!');
				echo json_encode($response);
			}
			else
			{
				if(isset($_REQUEST['city_name']) && $_REQUEST['city_name']!="")
				{
					$city_slug=$db->rp_createslug($_REQUEST['city_name']);
					$city_name=$db->clean($_REQUEST['city_name']);
					$adate	= date('Y-m-d H:i:s');
					
					$country_id=$db->rp_getValue("class","country_id","isDelete=0 AND id='".$state_id."'");
					$rows= array("country_id","state_id","city","name");
					$values=array($country_id,$state_id,$city_name,$city_name);
					$cbid=$db->rp_insert($ctable,$values,$rows,0);
					if($cbid!=0)
					{
						$rows=array("class_id","city_id","name","area_slug","isDelete");
						$values=array($state_id,$cbid,$city_name,$city_slug,0);

						$cid=$db->rp_insert("area",$values,$rows,0);
						
						$response=array('ack'=>1,'ack_msg'=>'city added Successfully !!!');
						echo json_encode($response);
					}
					else
					{
						$response=array('ack'=>0,'ack_msg'=>'city name can not be empty !!!');
						echo json_encode($response);
					}
				}
				else
				{
					$response=array('ack'=>0,'ack_msg'=>'city name can not be empty !!!');
					echo json_encode($response);
				}
			}
		}
		else if($service=="edit_city")
		{
				$dup_where = "state_id='".$_REQUEST['state_id']."' AND name = '".$_REQUEST['city_name']."' AND id!='".$_REQUEST['city_id']."' AND isDelete=0";
				$r = $db->rp_dupCheck($ctable,$dup_where,0);
				if($r)
				{
					$response=array('ack'=>0,'ack_msg'=>'city name already exists !!!');
					echo json_encode($response);
				}
				else
				{
					if(isset($_REQUEST['city_id']) && $_REQUEST['city_id']!="" && isset($_REQUEST['city_name']) && $_REQUEST['city_name']!="")
					{
						$city_name=$_REQUEST['city_name'];
						$rows 	= array(
							"name"=>$city_name,				
						);
						$where	= "id='".$_REQUEST['city_id']."'";
						if($db->rp_update($ctable,$rows,$where,0))
						{
							// $city_name=$_REQUEST['city_name'];
							// $update_rows 	= array("city"=>$city_name,"name"=>$city_name);
							// $where	= "id='".$_REQUEST['city_id']."'";
							// $db->rp_update("city",$update_rows,$where,0);
							$response=array('ack'=>1,'ack_msg'=>'city inforamtion edited Successfully !!!');
							echo json_encode($response);
						}	
						else
						{
							$response=array('ack'=>0,'ack_msg'=>'city couldn\'t be edited try later !!!');
							echo json_encode($response);
						}	
					}
					else
					{
						$response=array('ack'=>0,'ack_msg'=>'city name can\'t be empty or city not found !!!');
						echo json_encode($response);			
					}
				}
		}
		else if($service=='delete_city')
		{
			if(isset($_REQUEST['city_id']) && $_REQUEST['city_id']!="")
			{
				$rows 	= array(
					"isDelete"	=> "1"
				);
				$where	= "id='".$_REQUEST['city_id']."'";
				if($db->rp_update($ctable,$rows,$where))
				{
					// $db->rp_update("city",$rows,$where);
					$response=array('ack'=>1,'ack_msg'=>'city removed Successfully !!!');
					echo json_encode($response);
				}	
				else
				{
					$response=array('ack'=>0,'ack_msg'=>'city can\'t be deleted !!!');
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