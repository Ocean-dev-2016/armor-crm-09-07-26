<?php
$today_date = date('Y-m-d g:i:s');
$today_date1 = date('Y-m-d');
if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{
	$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
	$ctable_where .= " AND sales_executive_id = '".$check_id."' ";
	$ctable_where_attandance .= " AND sales_id = '".$check_id."' ";
	$ctable_where_followup .= " AND user_id = '".$check_id."' ";
	$ctable_where_inquiry .= " AND (inquiry_assign_to = '".$check_id."' OR inquiry_created_by = '".$check_id."') ";
	//$ctable_where_inquiry .= " AND (inquiry_assign_to = '".$check_id."') ";
	$ctable_where_order .= " AND created_by='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'" ;
	$ctable_where_complain .= " AND (complain_assign_to = '".$check_id."' OR complain_created_by = '".$check_id."') ";
}
/*
** >>> dashboard_main_array Parameter Description By Ravi Patel :) <<<
		-> 1 = color of box
		-> 2 = table name
		-> 3 = where condition for filtered record i.e 1=1
		-> 4 = Title of box
		-> 5 = URL 
*/
$dashboard_main_array = array(
		
		0=>array("green",$db->rp_getTotalRecord("attendance","DATE(date_time)='".$today_date1."' AND isDelete=0".$ctable_where_attandance,0),"Today's Attendance","attendance_manage.php",593),

		1=>array("green",$db->rp_getTotalRecord("no_order_inquiry","isDelete=0 AND isActive=1 AND inquiry_lead_flag=0".$ctable_where_inquiry,0),"No. of Inquiry","no_order_inquiry_grid.php?type=0",572),

		2=>array("green",$db->rp_getTotalRecord("no_order_inquiry","isDelete=0 AND isActive=1 AND inquiry_lead_flag=0 AND status=1".$ctable_where_inquiry,0),"In Followup Inquiry","no_order_inquiry_grid.php?type=0&status_id=1",572),

		3=>array("green",$db->rp_getTotalRecord("followup","status=0 AND DATE(followup_date) <= '".$today_date1."' AND  isDelete=0".$ctable_where_followup,0),"Pending Followup","followuplist_manage.php?followup_type=pending",583),
		
		4=>array("green",$db->rp_getTotalRecord("followup","DATE(followup_date)='".$today_date1."' AND isDelete=0".$ctable_where_followup,0),"Today's Followup","followuplist_manage.php?followup_type=today",583),
		
		5=>array("green",$db->rp_getTotalRecord("expense","expense_date='".$today_date1."' AND isDelete=0".$ctable_where,0),"Today's Expense","expense_manage.php",592),

		6=>array("green",$db->rp_getValue("expense","IFNULL(SUM(total),0)","expense_date='".$today_date1."' AND isDelete=0".$ctable_where,0),"Today's Expense Total","expense_manage.php",592),

		7=>array("green",$db->rp_getTotalRecord("leave_request","start_date='".$today_date1."' AND isDelete=0".$ctable_where,0),"Today's Leave","leave_request_manage.php",592),
		
		8=>array("green",$db->rp_getTotalRecord("orders","isDelete=0 AND status!=-1 AND status = '0' ".$ctable_where_order,0),"Pending Orders","dealer_orders_manage.php?status=0",565),
		
		9=>array("green",$db->rp_getTotalRecord("orders",$ctable_where_order."isDelete=0 AND order_date='".$today_date1."'",0),"Todays Orders","dealer_orders_manage.php",565),

		10=>array("green",$db->rp_getTotalRecord("executive","isDelete=0 AND id!=-1 AND customer_flag=0 ",0),"No. of Customer","executive_manage.php",555),
		
		11=>array("green",$db->rp_getTotalRecord("executive","isDelete=0 AND price_list_id=0 AND id!=-1 AND customer_flag=0",0),"No. of Customer Without <br/> Price list","executive_manage.php",555),
		13=>array("green",$db->rp_getTotalRecord("complain","isDelete=0 ".$ctable_where_complain,0),"No. of Complain","manage_report_complain.php",601),
		14=>array("green",$db->rp_getTotalRecord("complain","isDelete=0 AND isActive=1 AND status=0 ".$ctable_where_complain,0),"No. of Pending Complain","manage_report_complain.php",601),
		15=>array("green",$db->rp_getTotalRecord("complain","isDelete=0 AND isActive=1 AND status=2 ".$ctable_where_complain,0),"No. of Complete Complain","manage_report_complain.php",601),

		16=>array("green",$db->rp_getTotalRecord("orders","isDelete=0 AND dispatch_status = 1 ".$ctable_where_order,0),"Dispatch - Pending From Sales","dealer_orders_manage.php?status=0",565),

		17=>array("green",$db->rp_getTotalRecord("orders","isDelete=0 AND dispatch_status = 2 ".$ctable_where_order,0),"Dispatch -</br> Pending Purchase Side","dealer_orders_manage.php?status=0",565),

		18=>array("green",$db->rp_getTotalRecord("orders","isDelete=0 AND dispatch_status = 3 ".$ctable_where_order,0),"Dispatch - Work In Process </br> In Production","dealer_orders_manage.php?status=0",565),
		
		19=>array("green",$db->rp_getTotalRecord("orders","isDelete=0 AND status!=-1 AND status = '1' ".$ctable_where_order,0),"Total Orders","dealer_orders_manage.php?status=0",565),
		
		//8=>array("green",$db->rp_getTotalRecord("executive","isDelete=0 AND isActive=1 AND id!=-1",0),"No. of Active Customer","executive_manage.php",555),
		
		//9=>array("green",$db->rp_getTotalRecord("executive","isDelete=0 AND isActive=0 AND id!=-1",0),"No. of Deactive Customer","executive_manage.php",555),
		
		12=>array("green",$db->rp_getTotalRecord("complain",$ctable_where."isDelete=0 AND isActive=1 AND status!=2 AND status!=3 ",0),"No. of Complain","manage_report_complain.php",601),
		
		// 20=>array("green",$db->rp_getTotalRecord("orders","isDelete=0 AND status!=-1 AND status = '1' ".$ctable_where_order,0),"Total Orders","target_manage.php?status=0",556),
		
		
				
	);
?>