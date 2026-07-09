<?php 
include("connect.php");
include("../include/class.sales_executive.php");

if($is_valid_api_key)
{	
	if($is_valid_service)
	{ 
		$sales_executive=new SalesExecutive(); 
		if($service=='update_sales_km' || $service==197)
        { 
        	// echo "SD";exit;
			$sales_id=(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="")?$_REQUEST['sales_id']:"";
			$save_flag=(isset($_REQUEST['save_flag']) && $_REQUEST['save_flag']!="")?$_REQUEST['save_flag']:0;
			$route_date=date('Y-m-d',strtotime($_REQUEST['route_date']));

			if($save_flag==1){
				$total_km=$sales_executive->find_total_distance_of_sales_executive($sales_id,$route_date);
				$rows=array(
					"sales_executive_id"=>$sales_id,
					"route_date"=>$route_date,
					"total_km"=>$total_km,
				);

				$where="sales_executive_id='".$sales_id."' AND DATE(route_date)='".$route_date."'";
				$isdupCheck = $db->rp_getTotalRecord("salesexecutive_tracking_km",$where,0);
				// echo $isdupCheck;exit;
				if($isdupCheck>0)
				{
					$isUpdated = $db->rp_update("salesexecutive_tracking_km",$rows,$where);
				}
				else
				{ 
					$isUpdated=$db->rp_insert("salesexecutive_tracking_km",array_values($rows),array_keys($rows),0);
				}

				if($isUpdated)
				{
					$reply=array("ack"=>1,"ack_msg"=>"Update Successfully");
					$db->printJSON($reply);
				}
				else{
					$reply=array("ack"=>0,"ack_msg"=>"Update Failed");
					$db->printJSON($reply);
				}
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Internal error!!","developer_msg"=>"Service Parameter missing or not valid!!");
				$db->printJSON($ack);
			}
		}
		else
		{
			$ack=array( "ack"=>0,"ack_msg"=>"Internal error!!","developer_msg"=>"Service Parameter missing or not valid!!");
			$db->printJSON($ack);
		}
	}
	else
	{
		$ack=array( "ack"=>0,"ack_msg"=>"Internal error!!","developer_msg"=>"Service Parameter missing or not valid!!");
		$db->printJSON($ack);
	}
}
else
{
	$ack=array( "ack"=>0,
	"ack_msg"=>"Internal error!!",
	"developer_msg"=>"Check your API Key or contact Admin",
	"extra"=>array("requested_params"=>$_REQUEST,
					"other"=>array()));
	$db->printJSON($ack); 
}
?>