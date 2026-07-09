<?php
$page_id=607;$page_slug='quotation';
include('connect.php');
$m = $_REQUEST['m'];
if($m=='gtc')
{
	$Result=array();
	$CurrentView=(isset($_REQUEST['view']))?$_REQUEST['view']:0;
	$quotation_R = $db->rp_getData("pipeline_stage_master","*","isDelete=0 AND quotation_status=1","",0);
	while($quotation_D = mysqli_fetch_array($quotation_R))
	{
		//$Result[] = $quotationstatus_array[$quotation_D['status']];
		$Result[] = $quotation_D;
	}
	$Container=$db->GetTaskContainer($CurrentView);
	echo $Container[0];
	foreach($Result as $Key=>$TS)
	{
		echo $View=$db->GetTaskStatusView($CurrentView,$TS,"");            
	}
	echo $Container[1];
}

else if($m=='gt')
{
	$quotationS = $db->rp_getData("pipeline_stage_master","*","isDelete=0 AND quotation_status=1","id ASC",0);
	while($quotationSS = mysqli_fetch_array($quotationS))
	{
		$where = "";
		if($_REQUEST['searchName']!="")
		{
			$where .= " AND (customer_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR company_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR quotation_no like '%" . $db->clean($_REQUEST['searchName']) . "%' ) ";
		}
		else
		{
			$where .= " AND status='".$quotationSS['id']."'";	
		}

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
			$where .= " AND ( DATE(quotation_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(quotation_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
		}
		else
		{
			$where .= "";	
		}

		if ($_SESSION[SITE_SESS . '_ADMIN_TYPE'] != 0) 
		{
			$where .= " AND sales_id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "'";
		}
		
		$TasksR = $db->rp_getData("quotation_detail","id,inquiry_id,quotation_no,customer_id,company_name,sales_id,customer_name,customer_type,contact_number,city,state,country,email,quotation_date,status,isDelete,isActive,grand_total","isDelete=0".$where,"id DESC",0,$Limit);
		while($TasksD = mysqli_fetch_assoc($TasksR))
		{
			$Tasks[] = $TasksD;
		}
	}
	/*print_r($Tasks); exit;*/
	$TASK = $Tasks;
	foreach ($TASK as $key => $value) 
	{
		$sales_quotation = $db->rp_getValue("quotation_detail","SUM(grand_total)","id = '".$value['id']."' AND isDelete=0",0);

		//$total_quotation_count = $db->rp_getTotalRecord("quotation_detail","status='0' AND isDelete=0",0);

		$sales_name = $db->rp_getValue("dealer_distributor_network","name","sales_executive_id = '".$value['sales_id']."' AND isDelete=0",0);

		$type_of_customer = $db->rp_getValue("customer_type", "name", "id='" . $value['customer_type'] . "'");

		$TASK[$key]['total_quotation'] = ($sales_quotation!="" && $sales_quotation!=null && $sales_quotation!='null' && $sales_quotation!='NULL' && $sales_quotation!=NULL && $sales_quotation!=undefined && $sales_quotation!='undefined')?$sales_quotation:0;

		$TASK[$key]['quotation_count'] = ($total_quotation_count!="" && $total_quotation_count!=null && $total_quotation_count!='null' && $total_quotation_count!='NULL' && $total_quotation_count!=NULL && $total_quotation_count!=undefined && $total_quotation_count!='undefined')?$total_quotation_count:0;

		$TASK[$key]['total_quotation'] = $db->rp_num($TASK[$key]['total_quotation']);
		$TASK[$key]['total_quotation_count'] = $db->rp_num($TASK[$key]['quotation_count']);
		$TASK[$key]['sales_executive_name'] = $sales_name;
		$TASK[$key]['customer_type'] = $type_of_customer;

		$AllQuotationTotal[$TASK[$key]['task_status']] += $TASK[$key]['total_quotation'];
		$AllQuotationCount[$TASK[$key]['task_status1']] = $TASK[$key]['total_quotation_count'];
	}

	foreach ($AllQuotationTotal as $key => $value) {
		$AllQuotationTotal[$key] = $db->rp_num($value);
	}
	foreach ($AllQuotationCount as $key1 => $value1) {
		$AllQuotationCount[$key1] = $db->rp_num($value1);
	}

	if($TASK)
	{
		$Reply=array("a"=>1,"mg"=>"Quotation Fetched Successfully","result"=>$TASK,"total_quotation"=>$AllQuotationTotal,"quotation_count"=>$AllQuotationCount);
	}
	else
	{
		$Reply=array("a"=>0,"mg"=>"No Quotation Found");
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
		$UpdateS=$db->rp_update("quotation_detail",$Data,"id='".$TaskID."'",0);
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