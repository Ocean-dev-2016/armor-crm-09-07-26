<?php
	include('connect_in.php');
	// print_r($_REQUEST);exit();
	$customer_id = isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):0;

	if ($customer_id != "") 
	{
		$customers_r = $db->rp_getData("executive", "id,type_of_executive,type_of_company", "isDelete=0 AND isActive=1 AND id='".$customer_id."'", "", 0);
		if ($customers_r) 
		{
			$customers_d = mysqli_fetch_array($customers_r);
			// print_r($customers_d);exit;
			$customers_d['type_of_executive'];
			$customers_d['type_of_company'];
			$customers_d['id'];

			$ack = array("ack"=>1,"ack_msg"=>"Data Fetch Successfully","type_of_executive"=>$customers_d['type_of_executive'],"type_of_company"=>$customers_d['type_of_company'],"id"=>$customers_d['id']);
		}
	}
	else
	{
		$ack = array("ack"=>1,"ack_msg"=>"Data Fetch Successfully");
	}
	echo json_encode($ack);
?>