<?php 
include("connect.php");
include("../include/class.sales_executive.php");
$sales_executive=new SalesExecutive(); 
$save_flag=1;
// $month='04';
$sales_id=$_REQUEST['id'];
$month=$_REQUEST['month'];
$route_date=$_REQUEST['route_date'];
$where1="isDelete=0";
if($sales_id)
{
	$where1 .=" AND sales_executive_id=".$sales_id;
}
if($month!="")
{
	$where1 .= " AND MONTH(date)=".$month;
}
if($route_date!="")
{
	$where1 .= " AND DATE(date)=".$route_date;
}

//$where1="isDelete=0 AND sales_executive_id=".$sales_id." AND DATE(date)='2023-05-11'  GROUP BY DATE(date)";
$ctable_r = $db->rp_getData("salesexecutive_tracking_april","DATE(date) as route_date",$where1." GROUP BY DATE(date)","",1);
$updatecnt=0;
while($ctable_d=mysqli_fetch_assoc($ctable_r))
{	 
	$route_date=$ctable_d['route_date'];
	// echo $route_date;exit;
 
	$total_km=$sales_executive->find_total_distance_of_sales_executive($sales_id,$route_date);
	if($save_flag==1 && $total_km>0){
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
			$updatecnt++;
			/*$reply=array("ack"=>1,"ack_msg"=>"Update Successfully");
			$db->printJSON($reply);*/
		}
		else{
			// $reply=array("ack"=>0,"ack_msg"=>"Update Failed");
			// $db->printJSON($reply);
		}
	 
	}	
}
echo " --> updatecnt=".$updatecnt;
echo "<br/>";

$db->disconnect();
?>