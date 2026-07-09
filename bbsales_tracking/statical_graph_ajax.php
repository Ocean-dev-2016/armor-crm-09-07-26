<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
{

	$service=$_REQUEST['mode'];
	if($service=="statistical_chart")
	{

		
			if($_REQUEST['report'] == "1")
			{
				// echo "string";exit();
			$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
			// echo $order_year;exit();

			
			if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1){
				$AppendWhere .=" isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
			}else{
				$AppendWhere .="isDelete=0 ";
			}
			$data = array();

			$months = array(
				"01"=>"January",
				"02"=>"February",
				"03"=>"March",
				"04"=>"April",
				"05"=>"May",
				"06"=>"June",
				"07"=>"July",
				"08"=>"August",
				"09"=>"September",
				"10"=>"October",
				"11"=>"November",
				"12"=>"December",
			);

			// SALES ORDER
			for ($i=1; $i <= 12; $i++) { 
				$m = sprintf("%02d", $i);
				// $m = '07';
				$Where = " YEAR(order_date)='".$order_year."'  AND ";
				$Where .= " MONTH(order_date)='".$m."' AND ";
				if($_REQUEST['order_sales_id'] != "")
			{
				$Where.=" sales_id='".$_REQUEST['order_sales_id']."' AND ";
			}
			if($_REQUEST['order_customer_id'] != "")
			{
				$Where.=" customer_id='".$_REQUEST['order_customer_id']."' AND ";
			}

				$Where = $Where.$AppendWhere;
				$Total = $db->rp_getTotalrecord("orders",$Where,0);
				$Amount = $db->rp_getValue("orders","SUM(grand_total)",$Where,0);
				$Amount = isset($Amount)?round($Amount):0;
				$category = "orders";
				// echo "string";exit();
				$key = $months[$m]." - ".$Total." ";
				$data[$category][]=array("month"=>$key,"value"=>$Amount,"total"=>$Total);
			}
			// print_r($data);exit();
			$response=array('ack'=>1,'ack_msg'=>'Statistical Report data fetched !!!',"result"=>$data);
			// print_r($response);exit();
			echo json_encode($response);



		}
		if($_REQUEST['report'] == "2")
		{
							// echo "string";exit();
			$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
			// echo $order_year;exit();

			
			
				$AppendWhere .="isDelete=0 ";
			
				

			$data = array();

			$months = array(
				"01"=>"January",
				"02"=>"February",
				"03"=>"March",
				"04"=>"April",
				"05"=>"May",
				"06"=>"June",
				"07"=>"July",
				"08"=>"August",
				"09"=>"September",
				"10"=>"October",
				"11"=>"November",
				"12"=>"December",
			);

			// SALES ORDER
			for ($i=1; $i <= 12; $i++) { 
				$m = sprintf("%02d", $i);
				// $m = '07';

				$Where = " YEAR(quotation_date)='".$order_year."'  AND ";
				$Where .= " MONTH(quotation_date)='".$m."' AND ";

			if($_REQUEST['quotation_sales_id'] != "")
			{
				$Where.=" sales_id='".$_REQUEST['quotation_sales_id']."' AND ";
			}
			if($_REQUEST['quotation_customer_id'] != "")
			{
				$Where.=" customer_id='".$_REQUEST['quotation_customer_id']."' AND ";
			}


				$Where = $Where.$AppendWhere;
				$Total = $db->rp_getTotalrecord("quotation_detail",$Where,0);
				$Amount = $db->rp_getValue("quotation_detail","SUM(grand_total)",$Where,0);
				$Amount = isset($Amount)?round($Amount):0;
				$category = "orders";
				// echo "string";exit();
				$key = $months[$m]." - ".$Total." ";
				$data[$category][]=array("month"=>$key,"value"=>$Amount,"total"=>$Total);
			}
			// print_r($data);exit();
			$response=array('ack'=>1,'ack_msg'=>'Statistical Report data fetched !!!',"result"=>$data);
			// print_r($response);exit();
			echo json_encode($response);
		}
		if($_REQUEST['report'] == "3")
		{
							// echo "string";exit();
			$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
			// echo $order_year;exit();

			
		
				$AppendWhere .="isDelete=0 ";
			
			$data = array();

			$months = array(
				"01"=>"January",
				"02"=>"February",
				"03"=>"March",
				"04"=>"April",
				"05"=>"May",
				"06"=>"June",
				"07"=>"July",
				"08"=>"August",
				"09"=>"September",
				"10"=>"October",
				"11"=>"November",
				"12"=>"December",
			);

			// SALES ORDER
			for ($i=1; $i <= 12; $i++) { 
				$m = sprintf("%02d", $i);
				// $m = '07';
				$Where = " YEAR(invoice_date)='".$order_year."'  AND ";
				$Where .= " MONTH(invoice_date)='".$m."' AND ";
				if($_REQUEST['invoice_sales_id'] != "")
			{
				$Where.=" sales_id='".$_REQUEST['invoice_sales_id']."' AND ";
			}
			if($_REQUEST['invoice_customer_id'] != "")
			{
				$Where.=" customer_id='".$_REQUEST['invoice_customer_id']."' AND ";
			}

				$Where = $Where.$AppendWhere;
				$Total = $db->rp_getTotalrecord("invoice_new",$Where,0);
				$Amount = $db->rp_getValue("invoice_new","SUM(grand_total)",$Where,0);
				$Amount = isset($Amount)?round($Amount):0;
				$category = "orders";
				// echo "string";exit();
				$key = $months[$m]." - ".$Total." ";
				$data[$category][]=array("month"=>$key,"value"=>$Amount,"total"=>$Total);
			}
			// print_r($data);exit();
			$response=array('ack'=>1,'ack_msg'=>'Statistical Report data fetched !!!',"result"=>$data);
			// print_r($response);exit();
			echo json_encode($response);
		}

		if($_REQUEST['report'] == "4")
		{
							// echo "string";exit();
			$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
			// echo $order_year;exit();

			
			
				$AppendWhere .="isDelete=0 ";
			
			$data = array();

			$months = array(
				"01"=>"January",
				"02"=>"February",
				"03"=>"March",
				"04"=>"April",
				"05"=>"May",
				"06"=>"June",
				"07"=>"July",
				"08"=>"August",
				"09"=>"September",
				"10"=>"October",
				"11"=>"November",
				"12"=>"December",
			);

			// SALES ORDER
			for ($i=1; $i <= 12; $i++) { 
				$m = sprintf("%02d", $i);
				// $m = '07';
				$Where = " YEAR(created_date)='".$order_year."'  AND ";
				$Where .= " MONTH(created_date)='".$m."' AND ";
				if($_REQUEST['visit_sales_id'] != "")
			{
				$Where.=" user_id='".$_REQUEST['visit_sales_id']."' AND ";
			}
			if($_REQUEST['visit_customer_id'] != "")
			{
				$Where.=" customer_id='".$_REQUEST['visit_customer_id']."' AND ";
			}

				$Where = $Where.$AppendWhere;
				$Total = $db->rp_getTotalrecord("visit",$Where,0);
				$Amount = $db->rp_getValue("visit","count(*)",$Where,0);
				$Amount = isset($Amount)?round($Amount):0;
				$category = "orders";
				// echo "string";exit();
				$key = $months[$m]." - ".$Total." ";
				$data[$category][]=array("month"=>$key,"value"=>$Amount,"total"=>$Total);
			}
			// print_r($data);exit();
			$response=array('ack'=>1,'ack_msg'=>'Statistical Report data fetched !!!',"result"=>$data);
			// print_r($response);exit();
			echo json_encode($response);
		}
		if($_REQUEST['report'] == "5")
		{
							// echo "string";exit();
			$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
			// echo $order_year;exit();

			
			
				$AppendWhere .="isDelete=0 ";
			
			$data = array();

			$months = array(
				"01"=>"January",
				"02"=>"February",
				"03"=>"March",
				"04"=>"April",
				"05"=>"May",
				"06"=>"June",
				"07"=>"July",
				"08"=>"August",
				"09"=>"September",
				"10"=>"October",
				"11"=>"November",
				"12"=>"December",
			);

			// SALES ORDER
			for ($i=1; $i <= 12; $i++) { 
				$m = sprintf("%02d", $i);
				// $m = '07';
				$Where = " YEAR(complain_date)='".$order_year."'  AND ";
				$Where .= " MONTH(complain_date)='".$m."' AND ";
				if($_REQUEST['complain_sales_id'] != "")
			{
				$Where.=" user_id='".$_REQUEST['complain_sales_id']."' AND ";
			}
			if($_REQUEST['complain_customer_id'] != "")
			{
				$Where.=" customer_id='".$_REQUEST['complain_customer_id']."' AND ";
			}

				$Where = $Where.$AppendWhere;
				$Total = $db->rp_getTotalrecord("complain",$Where,0);
				$Amount = $db->rp_getValue("complain","count(*)",$Where,0);
				$Amount = isset($Amount)?round($Amount):0;
				$category = "orders";
				// echo "string";exit();
				$key = $months[$m]." - ".$Total." ";
				$data[$category][]=array("month"=>$key,"value"=>$Amount,"total"=>$Total);
			}
			// print_r($data);exit();
			$response=array('ack'=>1,'ack_msg'=>'Statistical Report data fetched !!!',"result"=>$data);
			// print_r($response);exit();
			echo json_encode($response);
		}

		if($_REQUEST['report'] == "6")
		{
							// echo "string";exit();
			$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
			// echo $order_year;exit();

			
			if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1){
				$AppendWhere .=" isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
			}else{
				$AppendWhere .="isDelete=0 ";
			}
			$data = array();

			$months = array(
				"01"=>"January",
				"02"=>"February",
				"03"=>"March",
				"04"=>"April",
				"05"=>"May",
				"06"=>"June",
				"07"=>"July",
				"08"=>"August",
				"09"=>"September",
				"10"=>"October",
				"11"=>"November",
				"12"=>"December",
			);

			// SALES ORDER
			for ($i=1; $i <= 12; $i++) { 
				$m = sprintf("%02d", $i);
				// $m = '07';
				$Where = " YEAR(inquiry_date)='".$order_year."'  AND  inquiry_lead_flag = '0' AND ";
				$Where .= " MONTH(inquiry_date)='".$m."' AND ";
				if($_REQUEST['inquiry_inquiry_created_by'] != "")
			{
				$Where.=" inquiry_created_by='".$_REQUEST['inquiry_inquiry_created_by']."' AND ";
			}
			if($_REQUEST['inquiry_inquiry_assigned_to'] != "")
			{
				$Where.=" inquiry_assign_to='".$_REQUEST['inquiry_inquiry_assigned_to']."' AND ";
			}

				$Where = $Where.$AppendWhere;
				$Total = $db->rp_getTotalrecord("no_order_inquiry",$Where,0);
				$Amount = $db->rp_getValue("no_order_inquiry","count(*)",$Where,0);
				$Amount = isset($Amount)?round($Amount):0;
				$category = "orders";
				// echo "string";exit();
				$key = $months[$m]." - ".$Total." ";
				$data[$category][]=array("month"=>$key,"value"=>$Amount,"total"=>$Total);
			}
			// print_r($data);exit();
			$response=array('ack'=>1,'ack_msg'=>'Statistical Report data fetched !!!',"result"=>$data);
			// print_r($response);exit();
			echo json_encode($response);
		}

		if($_REQUEST['report'] == "7")
		{
							// echo "string";exit();
			$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
			// echo $order_year;exit();

			
			
				// echo "string";exit();
				$AppendWhere .="isDelete=0 AND isActive=1 ";
			
			$data = array();

			$months = array(
				"01"=>"January",
				"02"=>"February",
				"03"=>"March",
				"04"=>"April",
				"05"=>"May",
				"06"=>"June",
				"07"=>"July",
				"08"=>"August",
				"09"=>"September",
				"10"=>"October",
				"11"=>"November",
				"12"=>"December",
			);

			// SALES ORDER
			for ($i=1; $i <= 12; $i++) { 
				$m = sprintf("%02d", $i);
				// $m = '07';
				$Where = " YEAR(lead_date)='".$order_year."'  AND  inquiry_lead_flag = '1' AND ";
				$Where .= " MONTH(lead_date)='".$m."' AND ";
				if($_REQUEST['lead_inquiry_created_by'] != "")
			{
				$Where.=" inquiry_created_by='".$_REQUEST['lead_inquiry_created_by']."' AND ";
			}
			if($_REQUEST['lead_inquiry_assigned_to'] != "")
			{
				$Where.=" inquiry_assign_to='".$_REQUEST['lead_inquiry_assigned_to']."' AND ";
			}

				$Where = $Where.$AppendWhere;
				$Total = $db->rp_getTotalrecord("no_order_inquiry",$Where,0);
				$Amount = $db->rp_getValue("no_order_inquiry","count(*)",$Where,0);
				$Amount = isset($Amount)?round($Amount):0;
				$category = "orders";
				// echo "string";exit();
				$key = $months[$m]." - ".$Total." ";
				$data[$category][]=array("month"=>$key,"value"=>$Amount,"total"=>$Total);
			}
			// print_r($data);exit();
			$response=array('ack'=>1,'ack_msg'=>'Statistical Report data fetched !!!',"result"=>$data);
			// print_r($response);exit();
			echo json_encode($response);
		




		}

		if($_REQUEST['report'] == "19")
		{
							// echo "string";exit();
			$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
			// echo $order_year;exit();

			
			
				$AppendWhere .="isDelete=0 ";
			
			$data = array();

			$months = array(
				"01"=>"January",
				"02"=>"February",
				"03"=>"March",
				"04"=>"April",
				"05"=>"May",
				"06"=>"June",
				"07"=>"July",
				"08"=>"August",
				"09"=>"September",
				"10"=>"October",
				"11"=>"November",
				"12"=>"December",
			);

			// SALES ORDER
			for ($i=1; $i <= 12; $i++) { 
				$m = sprintf("%02d", $i);
				// $m = '07';



				$Where = " YEAR(dispatch_date)='".$order_year."'  AND ";
				$Where .= " MONTH(dispatch_date)='".$m."' AND ";

			if($_REQUEST['dispatch_sales_id'] != "")
			{
				$Where.=" sales_id='".$_REQUEST['dispatch_sales_id']."' AND ";
			}
			if($_REQUEST['dispatch_customer_id'] != "")
			{
				$Where.=" customer_id='".$_REQUEST['dispatch_customer_id']."' AND ";
			}

				$Where = $Where.$AppendWhere;
				$Total = $db->rp_getTotalrecord("dispatch_detail",$Where,0);
				$Amount = $db->rp_getValue("dispatch_detail","SUM(grand_total)",$Where,0);
				$Amount = isset($Amount)?round($Amount):0;
				$category = "orders";
				// echo "string";exit();
				$key = $months[$m]." - ".$Total." ";
				$data[$category][]=array("month"=>$key,"value"=>$Amount,"total"=>$Total);
			}
			// print_r($data);exit();
			$response=array('ack'=>1,'ack_msg'=>'Statistical Report data fetched !!!',"result"=>$data);
			// print_r($response);exit();
			echo json_encode($response);
		}

		if($_REQUEST['report'] == "20")
		{
							// echo "string";exit();
			$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
			// echo $order_year;exit();

			
			
				$AppendWhere .="isDelete=0 ";
			
			$data = array();

			$months = array(
				"01"=>"January",
				"02"=>"February",
				"03"=>"March",
				"04"=>"April",
				"05"=>"May",
				"06"=>"June",
				"07"=>"July",
				"08"=>"August",
				"09"=>"September",
				"10"=>"October",
				"11"=>"November",
				"12"=>"December",
			);

			// SALES ORDER
			for ($i=1; $i <= 12; $i++) { 
				$m = sprintf("%02d", $i);
				// $m = '07';

			if($_REQUEST['prospect_inquiry_created_by'] != "")
			{
				$Where.=" inquiry_created_by='".$_REQUEST['prospect_inquiry_created_by']."' AND ";
			}
			if($_REQUEST['prospect_inquiry_assigned_to'] != "")
			{
				$Where.=" inquiry_assign_to='".$_REQUEST['prospect_inquiry_assigned_to']."' AND ";
			}
				$Where = " YEAR(inquiry_date)='".$order_year."'  AND  inquiry_lead_flag = '-1' AND ";
				$Where .= " MONTH(inquiry_date)='".$m."' AND ";

				$Where = $Where.$AppendWhere;
				$Total = $db->rp_getTotalrecord("no_order_inquiry",$Where,0);
				$Amount = $db->rp_getValue("no_order_inquiry","count(*)",$Where,0);
				$Amount = isset($Amount)?round($Amount):0;
				$category = "orders";
				// echo "string";exit();
				$key = $months[$m]." - ".$Total." ";
				$data[$category][]=array("month"=>$key,"value"=>$Amount,"total"=>$Total);
			}
			// print_r($data);exit();
			$response=array('ack'=>1,'ack_msg'=>'Statistical Report data fetched !!!',"result"=>$data);
			// print_r($response);exit();
			echo json_encode($response);
		}
		if($_REQUEST['report'] == "21")
		{
							// echo "string";exit();
			$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
			// echo $order_year;exit();

			
			
				$AppendWhere .="isDelete=0 ";
			
			$data = array();

			$months = array(
				"01"=>"January",
				"02"=>"February",
				"03"=>"March",
				"04"=>"April",
				"05"=>"May",
				"06"=>"June",
				"07"=>"July",
				"08"=>"August",
				"09"=>"September",
				"10"=>"October",
				"11"=>"November",
				"12"=>"December",
			);

			// SALES ORDER
			for ($i=1; $i <= 12; $i++) { 
				$m = sprintf("%02d", $i);
				// $m = '07';
				$Where = " YEAR(expense_date)='".$order_year."'  AND ";
				$Where .= " MONTH(expense_date)='".$m."' AND ";
				if($_REQUEST['expense_sales_id'] != "")
			{
				$Where.=" sales_executive_id='".$_REQUEST['expense_sales_id']."' AND ";
			}

				$Where = $Where.$AppendWhere;
				$Total = $db->rp_getTotalrecord("expense",$Where,0);
				$Amount = $db->rp_getValue("expense","SUM(total)",$Where,0);
				$Amount = isset($Amount)?round($Amount):0;
				$category = "orders";
				// echo "string";exit();
				$key = $months[$m]." - ".$Total." ";
				$data[$category][]=array("month"=>$key,"value"=>$Amount,"total"=>$Total);
			}
			// print_r($data);exit();
			$response=array('ack'=>1,'ack_msg'=>'Statistical Report data fetched !!!',"result"=>$data);
			// print_r($response);exit();
			echo json_encode($response);
		}
		if($_REQUEST['report'] == "22")
		{
							// echo "string";exit();
			$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
			// echo $order_year;exit();

			
			
				$AppendWhere .="isDelete=0 ";
			
			$data = array();

			$months = array(
				"01"=>"January",
				"02"=>"February",
				"03"=>"March",
				"04"=>"April",
				"05"=>"May",
				"06"=>"June",
				"07"=>"July",
				"08"=>"August",
				"09"=>"September",
				"10"=>"October",
				"11"=>"November",
				"12"=>"December",
			);

			// SALES ORDER
			for ($i=1; $i <= 12; $i++) { 
				$m = sprintf("%02d", $i);

				// $m = '07';
				$Where = " YEAR(start_date)='".$order_year."'  AND ";
				$Where .= " MONTH(start_date)='".$m."' AND ";
				if($_REQUEST['leave_sales_id'] != "")
			{
				$Where.=" sales_executive_id='".$_REQUEST['leave_sales_id']."' AND ";
			}

				$Where = $Where.$AppendWhere;
				$Total = $db->rp_getTotalrecord("leave_request",$Where,0);
				$Amount = $db->rp_getValue("leave_request","count(*)",$Where,0);
				$Amount = isset($Amount)?round($Amount):0;
				$category = "orders";
				// echo "string";exit();
				$key = $months[$m]." - ".$Total." ";
				$data[$category][]=array("month"=>$key,"value"=>$Amount,"total"=>$Total);
			}
			// print_r($data);exit();
			$response=array('ack'=>1,'ack_msg'=>'Statistical Report data fetched !!!',"result"=>$data);
			// print_r($response);exit();
			echo json_encode($response);
		}






		// else
		// {
		// 	echo "test";exit();
		// }

		

	} 
	else if($service=="purchaseorder_chart")
	{
		
			if($_REQUEST['month'] == "13")
			{
				if($_REQUEST['report'] == "8")
				{

						// echo "string";exit();
						if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1)
						{
						$Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						}
						else
						{
							$Where="isDelete=0";
						}

						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=12;
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));

						// $Where .= " AND ( DATE(order_date)>='".$start_date."' AND DATE(order_date)<='".$end_date."'  ) GROUP BY order_date";
						$OrderBy="order_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"order_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();

					for ($i=1; $i <= 12; $i++) 
					{ 
							$m = sprintf("%02d", $i);
							
							$Where = " YEAR(order_date)='".$order_year."'  AND ";
							$Where .= " MONTH(order_date)='".$m."'  ";
							$total_value=$db->rp_getValue("orders","sum(grand_total)",$Where,0);
							
							// $cd = date("d",strtotime($Results['order_date']));
							
							$data[$i]=array("revenue"=>$total_value,"month"=>$i);

								
					}
						
						
						ksort($data);
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);

				}

			}
			else
			{	
				if($_REQUEST['report'] == "8")
				{

						// echo "string";exit();
						// if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1)
						// {
						// $Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						// }
						// else
						// {
						// 	$Where="isDelete=0";
						// }

						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=cal_days_in_month(CAL_GREGORIAN,$order_month,$order_year);
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));
						if($_REQUEST['order_sales_id']!="")
						{
							$Where.="  AND sales_id='".$_REQUEST['order_sales_id']."' ";
							
						}
						
						if($_REQUEST['order_customer_id']!="")
						{
							$Where.="  AND customer_id='".$_REQUEST['order_customer_id']."' ";
							
						}

						$Where .= " AND ( MONTH(order_date)='".$order_month."' AND YEAR(order_date)='".$order_year."'  ) GROUP BY id";
						$OrderBy="order_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"order_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						$Results_r=$db->rp_getdata("orders","sum(grand_total),order_date","isDelete=0".$Where,"",0);
						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();
						while($Results=mysqli_fetch_assoc($Results_r))
						{


						
						// if($Results)
						// {
						// 	foreach($Results as $R)
						// 	{ 
								$cd = date("d",strtotime($Results['order_date']));

						if($_REQUEST['order_sales_id'] !="")
								{
									$order_where.=" AND sales_id=".$_REQUEST['order_sales_id'];
								}
								if($_REQUEST['order_customer_id'] !="")
								{
									$order_where.=" AND customer_id=".$_REQUEST['order_customer_id'];
								}

								$get_total_order_count=$db->rp_getValue("orders","count(*)","isDelete=0 AND order_date='".$Results['order_date']."'".$order_where,0);
							$get_total_order=$db->rp_getValue("orders","sum(grand_total)","isDelete=0 AND order_date='".$Results['order_date']."'".$order_where,0);
								// $gt = $Results['sum(grand_total)'];
								$gt = round($get_total_order);
								$nd = 1 * $cd;
								$data[$nd]=array("revenue"=>$gt,"date"=>$nd.'('.$get_total_order_count.')');


								for($i=1;$i<=$d;$i++)
								{
									if(array_search($days[$i], array_column($data, 'date')) === false) 
									{
										$data[$i]=array("revenue"=>0,"date"=>$days[$i]);
									}
								}
						}
						// 	}
						// }
						
						ksort($data);
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);

				}

				if($_REQUEST['report'] == "9")
				{

						// echo "string";exit();
						// if($_SESSION[SITE_SESS.'_ADMIN_TYPE'] !=0)
						// {
						// $Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						// }
						// else
						// {
						// 	$Where="isDelete=0";
						// }

						

						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=cal_days_in_month(CAL_GREGORIAN,$order_month,$order_year);
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));
						if($_REQUEST['quotation_sales_id']!="")
						{
							$Where.="  AND sales_id='".$_REQUEST['quotation_sales_id']."' ";
							
						}
						
						if($_REQUEST['quotation_customer_id']!="")
						{
							$Where.="  AND customer_id='".$_REQUEST['quotation_customer_id']."' ";
							
						}
						
						
						// if($_REQUEST['quotation_sales_id'] != "")
						// {
						// 	$Where.= " AND sales_id=".$_REQUEST['quotation_sales_id'];
						// }
						$Where .= " AND MONTH(quotation_date)='".$order_month."' AND YEAR(quotation_date)='".$order_year."' GROUP BY ID ";
						$OrderBy="quotation_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"quotation_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						$Results_r=$db->rp_getdata("quotation_detail","sum(grand_total),quotation_date","isDelete=0".$Where,"",0);
						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();
						while($Results=mysqli_fetch_assoc($Results_r))
						{


						
						// if($Results)
						// {
						// 	foreach($Results as $R)
						// 	{ 
								$cd = date("d",strtotime($Results['quotation_date']));
								if($_REQUEST['quotation_sales_id'] !="")
								{
									$quotation_where.=" AND sales_id=".$_REQUEST['quotation_sales_id'];
								}
								if($_REQUEST['quotation_customer_id'] !="")
								{
									$quotation_where.=" AND customer_id=".$_REQUEST['quotation_customer_id'];
								}
								
								$get_total_quotation_count=$db->rp_getValue("quotation_detail","count(*)","isDelete=0 AND quotation_date='".$Results['quotation_date']."'".$quotation_where,0);
								$get_total_quotation=$db->rp_getValue("quotation_detail","sum(grand_total)","isDelete=0 AND quotation_date='".$Results['quotation_date']."'".$quotation_where,0);
								// $gt = $Results['sum(grand_total)'];
								$gt = round($get_total_quotation);
								$nd = 1 * $cd;
								$data[$nd]=array("revenue"=>$gt,"date"=>$nd.'('.$get_total_quotation_count.')');


								for($i=1;$i<=$d;$i++)
								{
									if(array_search($days[$i], array_column($data, 'date')) === false) 
									{
										$data[$i]=array("revenue"=>0,"date"=>$days[$i]);
									}
								}
						}
						// 	}
						// }
						
						ksort($data);
								// print_r($data);exit();
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);

				}

				if($_REQUEST['report'] == "10")
				{

						// // echo "string";exit();
						// if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1)
						// {
						// $Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						// }
						// else
						// {
						// 	$Where="isDelete=0";
						// }

						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=cal_days_in_month(CAL_GREGORIAN,$order_month,$order_year);
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));

						if($_REQUEST['invoice_sales_id']!="")
						{
							$Where.="  AND sales_id='".$_REQUEST['invoice_sales_id']."' ";
							
						}
						
						if($_REQUEST['invoice_customer_id']!="")
						{
							$Where.="  AND customer_id='".$_REQUEST['invoice_customer_id']."' ";
							
						}

						$Where .= " AND ( MONTH(
							invoice_date)='".$order_month."' AND YEAR(
								invoice_date)='".$order_year."'  ) GROUP BY 
								id";
						$OrderBy="
						invoice_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"
							invoice_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						$Results_r=$db->rp_getdata("invoice_new","sum(grand_total),invoice_date","isDelete=0".$Where,"",0);
						// $result_total=$db->rp_getValue("invoice_new","sum(grand_total)",$where,0);
						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();
						while($Results=mysqli_fetch_assoc($Results_r))
						{


						
						// if($Results)
						// {
						// 	foreach($Results as $R)
						// 	{ 
								$cd = date("d",strtotime($Results['invoice_date']));
								if($_REQUEST['invoice_sales_id'] !="")
								{
									$invoice_where.=" AND sales_id=".$_REQUEST['invoice_sales_id'];
								}
								if($_REQUEST['invoice_customer_id'] !="")
								{
									$invoice_where.=" AND customer_id=".$_REQUEST['invoice_customer_id'];
								}
								$get_total_invoice_count=$db->rp_getValue("invoice_new","count(*)","isDelete=0 AND invoice_date='".$Results['invoice_date']."'".$invoice_where,0);
								$get_total_invoice=$db->rp_getValue("invoice_new","sum(grand_total)","isDelete=0 AND invoice_date='".$Results['invoice_date']."'".$invoice_where,0);
								// $gt = $Results['sum(grand_total)'];
								$gt = round($get_total_invoice);
								$nd = 1 * $cd;
								$data[$nd]=array("revenue"=>$gt,"date"=>$nd.'('.$get_total_invoice_count.')');

								for($i=1;$i<=$d;$i++)
								{
									if(array_search($days[$i], array_column($data, 'date')) === false) 
									{
										$data[$i]=array("revenue"=>0,"date"=>$days[$i]);
									}
								}
						}
						// 	}
						// }
						
						ksort($data);
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);

				}
				if($_REQUEST['report'] == "11")
				{

						// echo "string";exit();
						// if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1)
						// {
						// $Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						// }
						// else
						// {
						// 	$Where="isDelete=0";
						// }




						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=cal_days_in_month(CAL_GREGORIAN,$order_month,$order_year);
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));

						if($_REQUEST['visit_sales_id']!="")
						{
							$Where.="  AND user_id='".$_REQUEST['visit_sales_id']."' ";
							
						}
						
						if($_REQUEST['visit_customer_id']!="")
						{
							$Where.="  AND customer_id='".$_REQUEST['visit_customer_id']."' ";
							
						}

						$Where .= " AND ( MONTH(
							created_date)='".$order_month."' AND YEAR(
								created_date)='".$order_year."'  ) GROUP BY 
								id";
						$OrderBy="
						created_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"
							invoice_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						$Results_r=$db->rp_getdata("visit","created_date","isDelete=0".$Where,"",0);
						// $result_total=$db->rp_getValue("invoice_new","sum(grand_total)",$where,0);
						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();
						while($Results=mysqli_fetch_assoc($Results_r))
						{


						
						// if($Results)
						// {
						// 	foreach($Results as $R)
						// 	{ 	

								if($_REQUEST['visit_sales_id'] !="")
								{
									$visit_where.=" AND user_id=".$_REQUEST['visit_sales_id'];
								}
								if($_REQUEST['visit_customer_id'] !="")
								{
									$visit_where.=" AND customer_id=".$_REQUEST['visit_customer_id'];
								}
								$get_visit=$db->rp_getValue("visit","count(*)","isDelete=0 AND DATE(created_date)='".date("Y-m-d",strtotime($Results['created_date']))."'".$visit_where,0);


								$cd = date("d",strtotime($Results['created_date']));
								$gt = $get_visit;
								$nd = 1 * $cd;
								$data[$nd]=array("revenue"=>$gt,"date"=>$nd);

								for($i=1;$i<=$d;$i++)
								{
									if(array_search($days[$i], array_column($data, 'date')) === false) 
									{
										$data[$i]=array("revenue"=>0,"date"=>$days[$i]);
									}
								}
						}
						// 	}
						// }
						
						ksort($data);
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);

				}

				if($_REQUEST['report'] == "12")
				{

						// echo "string";exit();
						// if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1)
						// {
						// $Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						// }
						// else
						// {
						// 	$Where="isDelete=0";
						// }

						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=cal_days_in_month(CAL_GREGORIAN,$order_month,$order_year);
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));


						if($_REQUEST['complain_sales_id']!="")
						{
							$Where.="  AND user_id='".$_REQUEST['complain_sales_id']."' ";
							
						}
						
						if($_REQUEST['complain_customer_id']!="")
						{
							$Where.="  AND customer_id='".$_REQUEST['complain_customer_id']."' ";
							
						}

						$Where .= " AND ( DATE(
							complain_date)>='".$start_date."' AND DATE(
								complain_date)<='".$end_date."'  ) GROUP BY 
								complain_date";
						$OrderBy="
						complain_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"
							invoice_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						$Results_r=$db->rp_getdata("complain","complain_date","isDelete=0".$Where,"",0);
						// $result_total=$db->rp_getValue("invoice_new","sum(grand_total)",$where,0);
						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();
						while($Results=mysqli_fetch_assoc($Results_r))
						{


						
						// if($Results)
						// {
						// 	foreach($Results as $R)
						// 	{ 	


								if($_REQUEST['complain_sales_id'] !="")
								{
									$complain_where.=" AND user_id=".$_REQUEST['complain_sales_id'];
								}
								if($_REQUEST['complain_customer_id'] !="")
								{
									$complain_where.=" AND customer_id=".$_REQUEST['complain_customer_id'];
								}
								$get_visit=$db->rp_getValue("complain","count(*)","isDelete=0 AND DATE(complain_date)='".date("Y-m-d",strtotime($Results['complain_date']))."'".$complain_where,0);
								$cd = date("d",strtotime($Results['complain_date']));
								$gt = $get_visit;
								$nd = 1 * $cd;
								$data[$nd]=array("revenue"=>$gt,"date"=>$nd);

								for($i=1;$i<=$d;$i++)
								{
									if(array_search($days[$i], array_column($data, 'date')) === false) 
									{
										$data[$i]=array("revenue"=>0,"date"=>$days[$i]);
									}
								}
						}
						// 	}
						// }
						
						ksort($data);
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);

				}
				if($_REQUEST['report'] == "13")
				{

						// echo "string";exit();
						// if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1)
						// {
						// $Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						// }
						// else
						// {
						// 	$Where="isDelete=0";
						// }

						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=cal_days_in_month(CAL_GREGORIAN,$order_month,$order_year);
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));
						if($_REQUEST['inquiry_inquiry_created_by']!="")
						{
							$Where.="  AND inquiry_created_by='".$_REQUEST['inquiry_inquiry_created_by']."' ";
							
						}
						
						if($_REQUEST['inquiry_inquiry_assigned_to']!="")
						{
							$Where.="  AND inquiry_assign_to='".$_REQUEST['inquiry_inquiry_assigned_to']."' ";
							
						}


						$Where .= " AND  inquiry_lead_flag = '0' AND ( MONTH(
							inquiry_date)='".$order_month."' AND YEAR(
								inquiry_date)='".$order_year."'  ) GROUP BY 
								id";
						$OrderBy="
						inquiry_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"
							invoice_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						$Results_r=$db->rp_getdata("no_order_inquiry","inquiry_date","isDelete=0".$Where,"",0);
						// $result_total=$db->rp_getValue("invoice_new","sum(grand_total)",$where,0);
						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();
						while($Results=mysqli_fetch_assoc($Results_r))
						{


						
						// if($Results)
						// {
						// 	foreach($Results as $R)
						// 	{ 	

								if($_REQUEST['inquiry_inquiry_created_by'] !="")
								{
									$inquiry_where.=" AND inquiry_created_by=".$_REQUEST['inquiry_inquiry_created_by'];
								}
								if($_REQUEST['inquiry_inquiry_assigned_to'] !="")
								{
									$inquiry_where.=" AND inquiry_assign_to=".$_REQUEST['inquiry_inquiry_assigned_to'];
								}
								$get_visit=$db->rp_getValue("no_order_inquiry","count(*)","isDelete=0 AND inquiry_date='".date("Y-m-d",strtotime($Results['inquiry_date']))."'AND inquiry_lead_flag = '0'".$inquiry_where,0);
								$cd = date("d",strtotime($Results['inquiry_date']));
								$gt = $get_visit;
								$nd = 1 * $cd;
								$data[$nd]=array("revenue"=>$gt,"date"=>$nd);

								for($i=1;$i<=$d;$i++)
								{
									if(array_search($days[$i], array_column($data, 'date')) === false) 
									{
										$data[$i]=array("revenue"=>0,"date"=>$days[$i]);
									}
								}
						}
						// 	}
						// }
						
						ksort($data);
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);

				}

				if($_REQUEST['report'] == "14")
				{

						// echo "string";exit();
						// if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1)
						// {
						// $Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						// }
						// else
						// {
						// 	$Where="isDelete=0";
						// }

						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=cal_days_in_month(CAL_GREGORIAN,$order_month,$order_year);
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));
						if($_REQUEST['lead_inquiry_created_by']!="")
						{
							$Where.="  AND inquiry_created_by='".$_REQUEST['lead_inquiry_created_by']."' ";
							
						}
						
						if($_REQUEST['lead_inquiry_assigned_to']!="")
						{
							$Where.="  AND inquiry_assign_to='".$_REQUEST['lead_inquiry_assigned_to']."' ";
							
						}
						$Where .= " AND  inquiry_lead_flag = '1' AND ( MONTH(
							lead_date)='".$order_month."' AND YEAR(
								lead_date)='".$order_year."'  ) GROUP BY 
								lead_date";
						$OrderBy="
						lead_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"
							invoice_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						$Results_r=$db->rp_getdata("no_order_inquiry","lead_date","isDelete=0".$Where,"",0);
						// $result_total=$db->rp_getValue("invoice_new","sum(grand_total)",$where,0);
						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();
						while($Results=mysqli_fetch_assoc($Results_r))
						{


						
						// if($Results)
						// {
						// 	foreach($Results as $R)
						// 	{ 	


								if($_REQUEST['lead_inquiry_created_by'] !="")
								{
									$lead_where.=" AND inquiry_created_by=".$_REQUEST['lead_inquiry_created_by'];
								}
								if($_REQUEST['lead_inquiry_assigned_to'] !="")
								{
									$lead_where.=" AND inquiry_assign_to=".$_REQUEST['lead_inquiry_assigned_to'];
								}
								$get_visit=$db->rp_getValue("no_order_inquiry","count(*)","isDelete=0 AND DATE(lead_date)='".date("Y-m-d",strtotime($Results['lead_date']))."' AND inquiry_lead_flag = '1'".$lead_where,0);
								$cd = date("d",strtotime($Results['lead_date']));
								$gt = $get_visit;
								$nd = 1 * $cd;
								$data[$nd]=array("revenue"=>$gt,"date"=>$nd);

								for($i=1;$i<=$d;$i++)
								{
									if(array_search($days[$i], array_column($data, 'date')) === false) 
									{
										$data[$i]=array("revenue"=>0,"date"=>$days[$i]);
									}
								}
						}
						// 	}
						// }
						
						ksort($data);
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);

				}
				if($_REQUEST['report'] == "15")
				{

						// echo "string";exit();
						// if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=0)
						// {
						// $Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						// }
						// else
						// {
						// 	$Where="isDelete=0";
						// }

						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=cal_days_in_month(CAL_GREGORIAN,$order_month,$order_year);
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));
						if($_REQUEST['prospect_inquiry_created_by']!="")
						{
							$Where.="  AND inquiry_created_by='".$_REQUEST['prospect_inquiry_created_by']."' ";
							
						}
						
						if($_REQUEST['prospect_inquiry_assigned_to']!="")
						{
							$Where.="  AND inquiry_assign_to='".$_REQUEST['prospect_inquiry_assigned_to']."' ";
							
						}
						$Where .= " AND  inquiry_lead_flag = '-1' AND ( MONTH(
							inquiry_date)='".$order_month."' AND YEAR(
								inquiry_date)='".$order_year."'  ) GROUP BY 
								id";
						$OrderBy="
						inquiry_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"
							invoice_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						$Results_r=$db->rp_getdata("no_order_inquiry","*","isDelete=0".$Where,"",0);
						// $result_total=$db->rp_getValue("invoice_new","sum(grand_total)",$where,0);
						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();
						while($Results=mysqli_fetch_assoc($Results_r))
						{


						
						// if($Results)
						// {
						// 	foreach($Results as $R)
						// 	{ 	
								if($_REQUEST['prospect_inquiry_created_by'] !="")
								{
									$prospect_where.=" AND inquiry_created_by=".$_REQUEST['prospect_inquiry_created_by'];
								}
								if($_REQUEST['prospect_inquiry_assigned_to'] !="")
								{
									$prospect_where.=" AND inquiry_assign_to=".$_REQUEST['prospect_inquiry_assigned_to'];
								}
								$get_visit=$db->rp_getValue("no_order_inquiry","count(*)","isDelete=0 AND inquiry_date='".date("Y-m-d",strtotime($Results['inquiry_date']))."' AND inquiry_lead_flag = '-1'".$prospect_where,0);
								$cd = date("d",strtotime($Results['inquiry_date']));
								$gt = $get_visit;
								$nd = 1 * $cd;
								$data[$nd]=array("revenue"=>$gt,"date"=>$nd);

								for($i=1;$i<=$d;$i++)
								{
									if(array_search($days[$i], array_column($data, 'date')) === false) 
									{
										$data[$i]=array("revenue"=>0,"date"=>$days[$i]);
									}
								}
						}
						// 	}
						// }
						
						ksort($data);
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);

				}


				if($_REQUEST['report'] == "16")
				{

						// echo "string";exit();
						if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1)
						{
						$Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						}
						else
						{
							$Where="isDelete=0";
						}

						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=cal_days_in_month(CAL_GREGORIAN,$order_month,$order_year);
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));

						$Where .= " AND ( MONTH(
							dispatch_date)='".$order_month."' AND YEAR(
								dispatch_date)='".$order_year."'  ) GROUP BY 
								id";
						$OrderBy="
						dispatch_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"
							dispatch_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						$Results_r=$db->rp_getdata("dispatch_detail","sum(grand_total),dispatch_date",$Where,"",0);
						// $result_total=$db->rp_getValue("invoice_new","sum(grand_total)",$where,0);
						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();
						while($Results=mysqli_fetch_assoc($Results_r))
						{


						
						// if($Results)
						// {
						// 	foreach($Results as $R)
						// 	{ 


								if($_REQUEST['dispatch_sales_id'] !="")
								{
									$dispatch_where.=" AND sales_id=".$_REQUEST['dispatch_sales_id'];
								}
								if($_REQUEST['dispatch_customer_id'] !="")
								{
									$dispatch_where.=" AND customer_id=".$_REQUEST['dispatch_customer_id'];
								}
								$cd = date("d",strtotime($Results['dispatch_date']));

								$get_total_dispatch_count=$db->rp_getValue("dispatch_detail","count(*)","isDelete=0 AND dispatch_date='".$Results['dispatch_date']."'".$dispatch_where,0);
								$get_total_dispatch=$db->rp_getValue("dispatch_detail","sum(grand_total)","isDelete=0 AND dispatch_date='".$Results['dispatch_date']."'".$dispatch_where);
								// $gt = $Results['sum(grand_total)'];
								$gt =$get_total_dispatch;
								$nd = 1 * $cd;
								$data[$nd]=array("revenue"=>$gt,"date"=>$nd.'('.$get_total_dispatch_count.')');

								for($i=1;$i<=$d;$i++)
								{
									if(array_search($days[$i], array_column($data, 'date')) === false) 
									{
										$data[$i]=array("revenue"=>0,"date"=>$days[$i]);
									}
								}
						}
						// 	}
						// }
						
						ksort($data);
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);

				}

				if($_REQUEST['report'] == "17")
				{

						// echo "string";exit();
						// if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1)
						// {
						// $Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						// }
						// else
						// {
						// 	$Where="isDelete=0";
						// }

						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=cal_days_in_month(CAL_GREGORIAN,$order_month,$order_year);
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));


						if($_REQUEST['expense_sales_id']!="")
						{
							$Where.="  AND sales_executive_id='".$_REQUEST['expense_sales_id']."' ";
							
						}

						$Where .= " AND ( MONTH(
							expense_date)='".$order_month."' AND YEAR(
								expense_date)='".$end_date."'  ) GROUP BY 
								id";
						$OrderBy="
						expense_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"
							expense_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						$Results_r=$db->rp_getdata("expense","sum(total),expense_date","isDelete=0".$Where,"",0);
						// $result_total=$db->rp_getValue("invoice_new","sum(total)",$where,0);
						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();
						while($Results=mysqli_fetch_assoc($Results_r))
						{


						
						// if($Results)
						// {
						// 	foreach($Results as $R)
						// 	{ 
								if($_REQUEST['expense_sales_id'] !="")
								{
									$expense_where.=" AND sales_executive_id=".$_REQUEST['expense_sales_id'];
								}
								$cd = date("d",strtotime($Results['expense_date']));
								$get_total_expense=$db->rp_getValue("expense","sum(total)","isDelete=0 AND expense_date='".$Results['expense_date']."'".$expense_where,0);
								// $gt = $Results['sum(total)'];
								$gt = $get_total_expense;
								$nd = 1 * $cd;
								$data[$nd]=array("revenue"=>$gt,"date"=>$nd);

								for($i=1;$i<=$d;$i++)
								{
									if(array_search($days[$i], array_column($data, 'date')) === false) 
									{
										$data[$i]=array("revenue"=>0,"date"=>$days[$i]);
									}
								}
						}
						// 	}
						// }
						
						ksort($data);
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);
				}


				if($_REQUEST['report'] == "18")
				{
					// echo "string";exit();
						// if($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!=1)
						// {
						// $Where="isDelete=0 AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
						// }
						// else
						// {
						// 	$Where="isDelete=0";
						// }

						// requested month and year
						$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
						$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
						
						// Get Total Days in month and year
						$d=cal_days_in_month(CAL_GREGORIAN,$order_month,$order_year);
						
						// Create Array of Total Days in month and year
						$days = range(1,$d);
						array_unshift($days,"");
						unset($days[0]);
						
						// full dates from given year and month
						$start_date = date("Y-m-d",strtotime("1-".$order_month."-".$order_year));
						$end_date = date("Y-m-d",strtotime($d."-".$order_month."-".$order_year));
						if($_REQUEST['leave_sales_id']  != "")
						{
							$Where=" AND sales_executive_id='".$_REQUEST['leave_sales_id'].'"';
						}



						$Where .= " AND ( MONTH(
							start_date)='".$order_month."' AND YEAR(
								start_date)='".$order_year."'  ) GROUP BY 
								id";
						$OrderBy="
						start_date ASC";

						// Response Column Name Specify
						$RequiredColumns = array(0=>"
							invoice_date",1=>"sum(grand_total) AS grandtotal");
						
						$RequiredColumns=implode(",",$RequiredColumns);

						// $Results=$PurchaseOrder->get_all($Where,$OrderBy,"",$RequiredColumns,0);

						$Results_r=$db->rp_getdata("leave_request","start_date","isDelete=0".$Where,"",0);
						// $result_total=$db->rp_getValue("invoice_new","sum(grand_total)",$where,0);
						// $Results=mysqli_fetch_assoc($Results_r);

						// echo "string";exit();
						$data = array();
						while($Results=mysqli_fetch_assoc($Results_r))
						{


						
						// if($Results)
						// {
						// 	foreach($Results as $R)
						// 	{ 	
								if($_REQUEST['leave_sales_id']  != "")
								{
									$leave_where.=" AND sales_executive_id='".$_REQUEST['leave_sales_id'].'"';
								}



								$get_visit=$db->rp_getValue("leave_request","count(*)","isDelete=0 AND DATE(start_date)='".date("Y-m-d",strtotime($Results['start_date']))."'".$leave_where,0);
								$cd = date("d",strtotime($Results['start_date']));
								$gt = $get_visit;
								$nd = 1 * $cd;
								$data[$nd]=array("revenue"=>$gt,"date"=>$nd);

								for($i=1;$i<=$d;$i++)
								{
									if(array_search($days[$i], array_column($data, 'date')) === false) 
									{
										$data[$i]=array("revenue"=>0,"date"=>$days[$i]);
									}
								}
						}
						// 	}
						// }
						
						ksort($data);
						$response=array('ack'=>1,'ack_msg'=>'Sales Order data fetched !!!',"result"=>$data);
						echo json_encode($response);

				}



			}
	
	}
}


else
{
	$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
	echo json_encode($response);
}

?>