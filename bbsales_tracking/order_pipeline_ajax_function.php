<?php
$page_id=560;$page_slug='page_order';
include('connect.php');
$m = $_REQUEST['m'];
if($m=='gtc')
{
	$Result=array();
	$CurrentView=(isset($_REQUEST['view']))?$_REQUEST['view']:0;
	$quotation_R = $db->rp_getData("pipeline_stage_master","*","isDelete=0 AND order_status=1","",0);
	while($quotation_D = mysqli_fetch_array($quotation_R))
	{
		//$Result[] = $quotationstatus_array[$quotation_D['status']];
		$Result[] = $quotation_D;
	}
	$Container=$db->GetTaskContainerOrder($CurrentView);
	echo $Container[0];
	foreach($Result as $Key=>$TS)
	{
		echo $View=$db->GetTaskStatusViewOrder($CurrentView,$TS,"");            
	}
	echo $Container[1];
}

else if($m=='gt')
{
	$quotationS = $db->rp_getData("pipeline_stage_master","*","isDelete=0 AND order_status=1","id ASC",0);
	while($quotationSS = mysqli_fetch_array($quotationS))
	{
		$where = "";
		if($_REQUEST['searchName']!="")
		{
			$where .= " AND (customer_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR order_no like '%" . $db->clean($_REQUEST['searchName']) . "%' ) ";
		}
		else
		{
			if($quotationSS['id']=="6")
			{	
				$quotationSS['id'] = "2";
			}

			if($quotationSS['id']=="7")
			{	
				$quotationSS['id'] = "4";
			}
			$where .= " AND status='".$quotationSS['id']."'";	
		}
		//echo $where; 

		if($_REQUEST['page_id']!="")
		{
			$Limit = $_REQUEST['page_id'];
		}
		else
		{
			$Limit = "";	
		}

		if($_REQUEST['sales_id']!="")
		{
			$where .= "AND sales_id = '".$_REQUEST['sales_id']."'";
		}
		else
		{
			$where .= "";	
		}

		if($_REQUEST['type']!="")
		{
			$where .= "AND customer_type = '".$_REQUEST['type']."'";
		}
		else
		{
			$where .= "";	
		}

		if ($_REQUEST['df1'] != "") 
		{
			$date_filter_query = urldecode($_REQUEST['df1']);
			$date_filter_query_ex = explode(" to ", $date_filter_query);
			$where .= " AND ( DATE(order_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(order_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
		}
		else
		{
			$where .= "";	
		}


		if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) 
		{
			$where .= " AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
		}
		
		$TasksR = $db->rp_getData("orders","*","isDelete=0".$where,"id DESC",0,$Limit);
		while($TasksD = mysqli_fetch_assoc($TasksR))
		{
			$TasksD['terms_comdition'] = htmlspecialchars($TasksD['terms_comdition']);
			$Tasks[] = $TasksD;
		}
	}
	/*print_r($Tasks); exit;*/
	$TASK = $Tasks;
	foreach ($TASK as $key => $value) 
	{
		$orders = $db->rp_getValue("orders","SUM(grand_total)","id = '".$value['id']."' AND isDelete=0",0);

		//$total_order_count = $db->rp_getTotalRecord("orders","status='0' AND isDelete=0",0);

		$quotation_no = $db->rp_getValue("quotation_detail","quotation_no","id='".$value['quotation_id']."'");

		$sales_name = $db->rp_getValue("dealer_distributor_network","name","sales_executive_id = '".$value['sales_id']."' AND isDelete=0",0);

		$type_of_customer = $db->rp_getValue("customer_type", "name", "id='" . $value['customer_type'] . "'");

		$TASK[$key]['total_order'] = ($orders!="" && $orders!=null && $orders!='null' && $orders!='NULL' && $orders!=NULL && $orders!=undefined && $orders!='undefined')?$orders:0;

		$TASK[$key]['order_count'] = ($total_order_count!="" && $total_order_count!=null && $total_order_count!='null' && $total_order_count!='NULL' && $total_order_count!=NULL && $total_order_count!=undefined && $total_order_count!='undefined')?$total_order_count:0;

		$TASK[$key]['total_order'] = $db->rp_num($TASK[$key]['total_order']);
		$TASK[$key]['total_order_count'] = $db->rp_num($TASK[$key]['order_count']);
		$TASK[$key]['sales_executive_name'] = $sales_name;
		$TASK[$key]['customer_type'] = $type_of_customer;
		$TASK[$key]['quotation_no'] = $quotation_no;

		$AllOrderTotal[$TASK[$key]['task_status']] += $TASK[$key]['total_order'];
		$AllOrderCount[$TASK[$key]['task_status1']] = $TASK[$key]['total_order_count'];
	}

	foreach ($AllOrderTotal as $key => $value) {
		$AllOrderTotal[$key] = $db->rp_num($value);
	}
	
	foreach ($AllOrderCount as $key1 => $value1) {
		$AllOrderCount[$key1] = $db->rp_num($value1);
	}

	if($TASK)
	{
		$Reply=array("a"=>1,"mg"=>"Order Fetched Successfully","result"=>$TASK,"total_order"=>$AllOrderTotal,"order_count"=>$AllOrderCount);
	}
	else
	{
		$Reply=array("a"=>0,"mg"=>"No Order Found");
	}
	$db->printJSON($Reply);
}

else if($m=='uts')
{
	if(isset($_REQUEST['tid']) && isset($_REQUEST['s']) && isset($_REQUEST['o']))
	{
		$TaskID=$_REQUEST['tid'];
		$TaskStatus=$_REQUEST['s'];
		$TaskOldStatus=$_REQUEST['o'];
		$Data=array('status'=>$TaskStatus);
		$UpdateS=$db->rp_update("orders",$Data,"id='".$TaskID."'",0);
		$Reply=array("a"=>"1","mg"=>"Status Updated !!");
		/*printJSON($Reply);*/
	}
	else
	{
		$Reply=array("a"=>0,"mg"=>"Internal error!!");
	}
	$db->printJSON($Reply);
	require_once "disconnect.php";
}
?>