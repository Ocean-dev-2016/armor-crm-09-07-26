<?php
$page_id=607;$page_slug='quotation';
include('connect.php');
$m = $_REQUEST['m'];
$type = $_REQUEST['type'];
if($m=='gtc')
{
	$Result=array();
	$CurrentView=(isset($_REQUEST['view']))?$_REQUEST['view']:0;
	$quotation_R = $db->rp_getData("pipeline_stage_master_inquiry","*","isDelete=0","display_order ASC",0);
	while($quotation_D = mysqli_fetch_array($quotation_R))
	{
		//$Result[] = $quotationstatus_array[$quotation_D['status']];
		$Result[] = $quotation_D;
	}
	$Container=$db->GetTaskContainerInquiry($CurrentView);
	echo $Container[0];
	foreach($Result as $Key=>$TS)
	{
		echo $View=$db->GetTaskStatusViewInquiry($CurrentView,$TS,$type);            
	}
	echo $Container[1];
}

else if($m=='gt')
{
	$quotationS = $db->rp_getData("pipeline_stage_master_inquiry","*","isDelete=0","display_order ASC",0);
	while($quotationSS = mysqli_fetch_array($quotationS))
	{
		$where = "";
		if($_REQUEST['searchName']!="")
		{
			$where .= " AND (company_name like '%" . $db->clean($_REQUEST['searchName']) . "%' OR mobile_number like '%" . $db->clean($_REQUEST['searchName']) . "%' OR id like '%" . $db->clean($_REQUEST['searchName']) . "%' OR person_name like '%".$_REQUEST['searchName']."%' OR country like '%".$_REQUEST['searchName']."%' OR state like '%".$_REQUEST['searchName']."%' OR city like '%".$_REQUEST['searchName']."%' OR email_address like '%".$_REQUEST['searchName']."%') ";
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
			$where .= "AND sales_executive_id = '".$_REQUEST['sales_id']."'";
		}
		else
		{
			$where .= "";	
		}

		if($_REQUEST['customer_type']!="")
		{
			$where .= "AND executive_type = '".$_REQUEST['customer_type']."'";
		}
		else
		{
			$where .= "";	
		}

		if ($_REQUEST['df1'] != "") 
		{
			$date_filter_query = urldecode($_REQUEST['df1']);
			$date_filter_query_ex = explode(" to ", $date_filter_query);
			$where .= " AND ( DATE(inquiry_date)>='" . date("Y-m-d", strtotime($date_filter_query_ex['0'])) . "' AND DATE(inquiry_date)<='" . date("Y-m-d", strtotime($date_filter_query_ex['1'])) . "'  ) ";
		}
		else
		{
			$where .= "";	
		}


		if ($_REQUEST['type']=="-1") 
		{
			$where .= " AND inquiry_lead_flag = '-1'";
		}
		else if($_REQUEST['type']=="0")
		{
			$where .= " AND inquiry_lead_flag = '0'";
		}
		else
		{
		    $where .= " AND inquiry_lead_flag = '1'";
		}

		if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
		{
		    $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
		    $where .= " AND (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') ";
		}
		
		$TasksR = $db->rp_getData("no_order_inquiry","*","isDelete=0".$where,"id DESC",0,$Limit);
		while($TasksD = mysqli_fetch_assoc($TasksR))
		{
			$Tasks[] = $TasksD;
		}
	}
	$TASK = $Tasks;
	foreach ($TASK as $key => $value) 
	{
		$total_quotation_count = $db->rp_getTotalRecord("no_order_inquiry","status='0' AND isDelete=0",0);

		$sales_name = $db->rp_getValue("dealer_distributor_network","name","sales_executive_id = '".$value['sales_executive_id']."' AND isDelete=0",0);
		$inq_assign_to = $db->rp_getValue("dealer_distributor_network","name","sales_executive_id = '".$value['inquiry_assign_to']."' AND isDelete=0",0);

		$type_of_customer = $db->rp_getValue("customer_type", "name", "id='" . $value['executive_type'] . "'");

		$TASK[$key]['quotation_count'] = ($total_quotation_count!="" && $total_quotation_count!=null && $total_quotation_count!='null' && $total_quotation_count!='NULL' && $total_quotation_count!=NULL && $total_quotation_count!=undefined && $total_quotation_count!='undefined')?$total_quotation_count:0;

		$TASK[$key]['total_quotation_count'] = $db->rp_num($TASK[$key]['quotation_count']);
		$TASK[$key]['sales_executive_name'] = $sales_name;
		$TASK[$key]['inq_assign_to'] = $inq_assign_to;
		$TASK[$key]['customer_type'] = $type_of_customer;

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
		$Reply=array("a"=>1,"mg"=>"Inquiry Fetched Successfully","result"=>$TASK,"quotation_count"=>$AllQuotationCount);
	}
	else
	{
		$Reply=array("a"=>0,"mg"=>"No Inquiry Found");
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
		$UpdateS=$db->rp_update("no_order_inquiry",$Data,"id='".$TaskID."'",0);
		$Reply=array("a"=>"1","mg"=>"Status Updated !!");
		/*printJSON($Reply);*/
	}
	else
	{
		$Reply=array("a"=>0,"mg"=>"Internal error!!");
	}
	$db->printJSON($Reply);
}
include "disconnect.php";
?>