<?php 
include('connect.php');
if($is_valid_api_key)
{
	if($is_valid_service)
	{
		include('../include/class.meeting.php');
		$objMeeting=new Meeting();

		if($service=='get_meeting' || $service==99)
		{
			$system=new System();
			$limit=$system->getLimit();
			$meeting = array();
			$sales_id = isset($_REQUEST['sales_id'])?$db->clean($_REQUEST['sales_id']):"";
			$meeting_type = isset($_REQUEST['meeting_type'])?$db->clean($_REQUEST['meeting_type']):"";
			$customer_id = isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";


			$Where.= "isDelete=0";
			
			if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
			{
			  $Where.= " DATE(created_date) <= '".date("Y-m-d",strtotime($_REQUEST['ToDate']))."' AND isDelete=0 AND";
			}

			if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
			{
				$Where.= " DATE(created_date) >= '".date("Y-m-d",strtotime($_REQUEST['FromDate']))."' AND isDelete=0 AND";
			}

			if($customer_id!="")
			{
				$Where.="customer_id='".$customer_id."' isDelete=0 AND";
			}

			if($meeting_type!="")
			{
				$meeting_name = $db->rp_getValue("meeting_type","name","id='".$meeting_type."'");

				$Where.= " AND meeting_type LIKE '%".$meeting_name."%' AND isDelete=0 AND isActive=1"; 
			}
			if($sales_id)
			{
				//$meeting_data = $db->rp_getData("meeting","*","sales_id='".$sales_id."' AND isDelete=0 AND isActive=1 AND ".$Where,"id DESC",0,$limit);
				$meeting_data = $db->rp_getData("meeting","*",$Where,"id DESC",0,$limit);
				if($meeting_data)
				{
					while($meeting_d = mysqli_fetch_assoc($meeting_data))
					{
						$img = explode(",", $meeting_d['image_path']);
						$imgpath = array();
						for ($i=0; $i < sizeof($img); $i++)
						{ 
							$imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$meeting_d['id']."' AND id='".$img[$i]."'");
						}
						$meeting_d['customer_name'] = $db->rp_getValue("executive","cname","id='".$meeting_d['customer_id']."'",0);
						$meeting_d['dealer_name'] = $db->rp_getValue("executive","cname","id='".$meeting_d['dealer_id']."'",0);
						$meeting_d['image_path'] = ($meeting_d['image_path']!= "")?$imgpath:[];
						$meeting_d['meeting_date'] = date('d F Y h:i A',strtotime($meeting_d['meeting_date']));
						$meeting_d['created_date'] = date('d-m-Y h:i A',strtotime($meeting_d['created_date']));
						$meeting_d['total_member'] = $db->rp_getTotalRecord("meeting_member","meeting_id='".$meeting_d['id']."' AND isDelete=0");
						$customer_type_get = $db->rp_getValue("executive","type_of_executive","id='".$meeting_d['customer_id']."'",0);
						$meeting_d['customer_type'] = $db->rp_getValue("customer_type","name","id='".$customer_type_get."'",0);
						$meeting[] = $meeting_d;
					}
				}

				if(!empty($meeting_data))
				{
					$reply=array("ack"=>1,"developer_msg"=>"Meeting Detail Get successfully!!","ack_msg"=>"Meeting Detail Get successfully!!","result"=>$meeting);
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Meeting Detail Not Get!!","ack_msg"=>"Meeting Detail Not Get!!");
				}
				echo json_encode($reply);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Meeting Detail Not Get!!","ack_msg"=>"Meeting Detail Not Get!!");
				echo json_encode($reply);
			}
		}
		else if($service=='add_meeting' || $service==100)
		{
			if(isset($_REQUEST['meeting_type']) && isset($_REQUEST['meeting_venue']) && isset($_REQUEST['dealer_id']) && isset($_REQUEST['customer_id']) && isset($_REQUEST['meeting_date']))
			{
				$detail['dealer_id']        = isset($_REQUEST['dealer_id'])?$db->clean($_REQUEST['dealer_id']):"";
				$detail['customer_id']        = isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
				$detail['meeting_venue']   = html_entity_decode($db->clean($_REQUEST['meeting_venue']));
				//$detail['meeting_date']	   = date('d-m-Y h:i A',strtotime($meeting_d['meeting_date']));
				$detail['meeting_date']	   = isset($_REQUEST['meeting_date'])?$db->clean($_REQUEST['meeting_date']):"";
				/*$detail['meeting_host']			= $db->clean($_REQUEST['meeting_host']);
				$detail['meeting_host_name']	= $db->rp_getValue("executive","cname","id='".$_REQUEST['meeting_host']."'");*/
				$detail['meeting_type']	   = $db->rp_getValue("meeting_type","name","id='".$_REQUEST['meeting_type']."'",0);
				//$detail['title']			    = $db->clean($_REQUEST['title']);
				$detail['sales_id']		   = $db->clean($_REQUEST['sales_id']);
				$detail['gift_details']		   = "";
				$detail['expence']		   ="";
				$reply=$objMeeting->InsertMeeting($detail,$_FILES);
				if($reply['ack']==1)
				{
					$MeetingR = $db->rp_getData("meeting","*","id='".$reply['id']."'");
					$meeting = mysqli_fetch_assoc($MeetingR );
					$reply=array("ack"=>1,"developer_msg"=>$reply['developer_msg'],"ack_msg"=>$reply['ack_msg'],"result"=>$meeting);
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>$reply['developer_msg'],"ack_msg"=>$reply['ack_msg']);
				}
				echo json_encode($reply);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Some Perameters Is Missing","ack_msg"=>"Failed! Meeting Insert Failed.");
				echo json_encode($reply);
			}
		}
		else if($service=='meeting_type' || $service==101)
		{
			$meeting_type = array();
			$meeting = $db->rp_getData("meeting_type","*","isDelete=0 AND isActive=1","id DESC",0);
			if($meeting)
			{
				while($meeting_type_d = mysqli_fetch_assoc($meeting))
				{
					$meeting_type[] = $meeting_type_d;
				}
			}

			if(!empty($meeting_type))
			{
				$reply=array("ack"=>1,"developer_msg"=>"Meeting Type Get successfully!!","ack_msg"=>"Meeting Type Get successfully!!","result"=>$meeting_type);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Meeting Type Not Get!!","ack_msg"=>"Meeting Type Not Get!!");
			}
			echo json_encode($reply);
		}
		else if($service=='meeting_member_suggestion' || $service==102)
		{
			$result = array();
			$mobile_no = isset($_REQUEST['mobile_no'])?$db->clean($_REQUEST['mobile_no']):"";
			if($mobile_no)
			{
				$data_d = $db->rp_getData("member","*","mobile_no LIKE '".$mobile_no."%'","mobile_no",0,"0,6");
				while($data_r = mysqli_fetch_assoc($data_d))
				{
					$result[] = $data_r;
				}
			}
			if(!empty($result))
			{
				$reply=array("ack"=>1,"developer_msg"=>"Data Get successfully!!","ack_msg"=>"Data Get successfully!!","result"=>$result);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"No Data Available!!","ack_msg"=>"No Data Available!!");
			}
			echo json_encode($reply);
		}
		else if($service=='member_vs_meeting' || $service==103)
		{
			$ctable 	= "meeting_member";
			$mode=$_REQUEST['mode'];
			$meeting_id=$_REQUEST['meeting_id'];
			$member_name=$_REQUEST['member_name'];
			$member_phone=$_REQUEST['member_phone'];

			if($mode=="add_member")
			{
				$check_duplicate=0;
				$check_member=$db->rp_getTotalRecord("member","mobile_no='".$member_phone."' AND isDelete=0");
				if($check_member==0)
				{
					// add new member
					$row=array("mobile_no","name");
					$value=array($member_phone,$member_name);
					$member_id=$db->rp_insert("member",$value,$row,0);
				}
				else
				{
					$member_id=$db->rp_getValue("member","id","mobile_no='".$member_phone."' AND isDelete=0");
					$check_duplicate=$db->rp_getTotalRecord("meeting_member","member_id='".$member_id."' AND meeting_id='".$meeting_id."' AND isDelete=0",0);
				}
				if($check_duplicate==0)
				{		
					$rows=array("meeting_id","member_id");
					$values=array($meeting_id,$member_id);

					$insert_id=$db->rp_insert("meeting_member",$values,$rows,0);
					if($insert_id>0)
					{
						$ack=array("ack"=>1,"ack_msg"=>"Member Added Successfully");
					}
					else
					{
						$ack=array("ack"=>0,"ack_msg"=>"Member Added Failed");
					}
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Duplicate Member Found for the same Meeting");
				}

					echo json_encode($ack);
			}
			if($mode=="delete_member")
			{
				$meeting_id=$_REQUEST['meeting_id'];
				$member_id=$_REQUEST['member_id'];
				$delete=$db->rp_update("meeting_member",array("isDelete"=>1),"meeting_id='".$meeting_id."' AND member_id='".$member_id."'",0);
				if($delete)
				{
					$ack=array("ack"=>1,"ack_msg"=>"Member Delete Successfully");
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Member Delete Failed");
				}
				echo json_encode($ack);
			}
			if($mode=="edit_member")
			{
				$meeting_id=$_REQUEST['meeting_id'];
				$member_id=$_REQUEST['member_id'];
				$member_name=$_REQUEST['member_name'];
				$member_phone=$_REQUEST['member_phone'];
				
				$update=$db->rp_update("member",array("name"=>$member_name,"mobile_no"=>$member_phone),"id='".$member_id."'",0);
				if($update)
				{
					$ack=array("ack"=>1,"ack_msg"=>"Member Update Successfully");
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Member Update Failed");
				}
				echo json_encode($ack);
			}
		}
		else if($service=="edit_meeting" || $service==104)
		{
		    if(isset($_REQUEST['meeting_type']) && isset($_REQUEST['meeting_venue']) && isset($_REQUEST['customer_id']) && isset($_REQUEST['meeting_date']) && isset($_REQUEST['id']))
			{
				
				$detail['dealer_id']        = isset($_REQUEST['dealer_id'])?$db->clean($_REQUEST['dealer_id']):"";
				$detail['customer_id']      = isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
				$detail['meeting_venue']    = html_entity_decode($db->clean($_REQUEST['meeting_venue']));
				$detail['meeting_date']	    = isset($_REQUEST['meeting_date'])?$db->clean($_REQUEST['meeting_date']):"";
				$detail['meeting_type']		= $db->rp_getValue("meeting_type","name","id='".$_REQUEST['meeting_type']."'");
				$detail['sales_id']			= $db->clean($_REQUEST['sales_id']);
				$detail['gift_details']     = ""; 
				$detail['expence']     = ""; 
				
				$reply=$objMeeting->UpdateMeeting($detail,$_FILES);
				if($reply['ack']==1)
				{
					$MeetingR = $db->rp_getData("meeting","*","id='".$_REQUEST['id']."'");
					$meeting = mysqli_fetch_assoc($MeetingR);
					$reply=array("ack"=>1,"developer_msg"=>$reply['developer_msg'],"ack_msg"=>$reply['ack_msg'],"result"=>$meeting);
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>$reply['developer_msg'],"ack_msg"=>$reply['ack_msg']);
				}
				echo json_encode($reply);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Some Perameters Is Missing","ack_msg"=>"Failed! Meeting Insert Failed.");
				echo json_encode($reply);
			}
		}
		else if($service=="get_meeting_member" || $service==105)
		{
		    $system=new System();
			$limit=$system->getLimit();
			if(isset($_REQUEST['meeting_id']) && $_REQUEST['meeting_id']!="")
			{
				$member_r=$db->rp_getData("meeting_member","member_id,meeting_id,image_path","meeting_id='".$_REQUEST['meeting_id']."' AND isDelete=0 AND isActive=1","",0,$limit);
				if($member_r)
				{
					$MEMBER=array();
					while($member_d=mysqli_fetch_assoc($member_r))
					{
						$img = explode(",", $member_d['image_path']);
						$imgpath1 = array();
						for ($i=0; $i < sizeof($img); $i++)
						{ 
							$imgpath1[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$member_d['member_id']."' AND id='".$img[$i]."'");
						}
						$member_d['image_path'] = ($member_d['image_path']!= "")?$imgpath1:"";
						$member_d['member_name']=$db->rp_getValue("member","name","id='".$member_d['member_id']."' AND isDelete=0");
						$member_d['member_mobile_no']=$db->rp_getValue("member","mobile_no","id='".$member_d['member_id']."' AND isDelete=0");
						$MEMBER[]=$member_d;
					}
					$ack=array( "ack"=>1,"ack_msg"=>"Member Found","developer_msg"=>"Member Found","result"=>$MEMBER);
					$db->printJSON($ack);
				}
				else
				{
					$ack=array( "ack"=>0,"ack_msg"=>"No such a Member Found","developer_msg"=>"No such a Member Found");
					$db->printJSON($ack);
				}
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Meeting ID required","developer_msg"=>"Meeting ID required");
				$db->printJSON($ack);
			}
		}

			else if($service=="get_target" || $service==107)
		{
			if(isset($_REQUEST['sales_executive_id']) && $_REQUEST['sales_executive_id']!="")
			{
				$month1 = date("m");
				$month1 = ltrim($month1, "0");
				$month = date("F");
				$year = date("Y");

				$currentMonth = new DateTime('now');
				$lastMonth = $currentMonth->sub(new DateInterval('P1M'));

				$lastmonth = $lastMonth->format('n');

				$monthArray = array(
				    '1' => 'January',
				    '2' => 'February',
				    '3' => 'March',
				    '4' => 'April',
				    '5' => 'May',
				    '6' => 'June',
				    '7' => 'July',
				    '8' => 'August',
				    '9' => 'September',
				    '10' => 'October',
				    '11' => 'November',
				    '12' => 'December'
				);

				$ctable_where .= " AND target_year = '" . $year . "'";
				$target_r = $db->rp_getData(
				    "target",
				    "*",
				    "sales_executive_id = '" . $_REQUEST['sales_executive_id'] . "' AND isDelete = 0 AND isActive = 1" . $ctable_where,
				    "",
				    0
				);

				$monthArray = array(
				    '1' => 'January',
				    '2' => 'February',
				    '3' => 'March',
				    '4' => 'April',
				    '5' => 'May',
				    '6' => 'June',
				    '7' => 'July',
				    '8' => 'August',
				    '9' => 'September',
				    '10' => 'October',
				    '11' => 'November',
				    '12' => 'December'
				);

				if($target_r)
				{
					$currmonthtar = $db->rp_getValue("target","id","isDelete=0 AND target_month='".$monthArray[$month1]."' AND target_year = '" . $year . "' AND sales_executive_id='".$_REQUEST['sales_executive_id']."'",0);

					if ($currmonthtar) 
					{
						$result['current_month'] = $monthArray[$month1];
					}
					else
					{
						$result['current_month'] = "";
					}
					
					$target_amount_total = 0;
					$target_quantity_total = 0;
					$target = array();
					$result['current_month_target_amount'] = $db->rp_getValue(
					    "target",
					    "target_amount",
					    "sales_executive_id = '" . $_REQUEST['sales_executive_id'] . "' AND isDelete = 0 AND isActive = 1 AND target_month = '" . $monthArray[$month1] . "' AND target_year = '" . $year . "'",
					    0
					);

					$result['current_month_target_qty'] = $db->rp_getValue(
					    "target",
					    "target_quantity",
					    "sales_executive_id = '" . $_REQUEST['sales_executive_id'] . "' AND isDelete = 0 AND isActive = 1 AND target_month = '" . $monthArray[$month1] . "' AND target_year = '" . $year . "'",
					    0
					);

					$target_d['current_month'] = $monthArray[$month1] . "-" . $year;

					$target_amount_total += $result['current_month_target_amount'];
					$target_quantity_total += $result['current_month_target_qty'];

					$result['sales_executive_name'] = $db->rp_getValue("sales_executive", "name", "id = '" . $_REQUEST['sales_executive_id'] . "' AND isDelete = 0");
					$incentive_percentage = $db->rp_getValue("sales_executive", "insentive_percentage", "id = '" . $_REQUEST['sales_executive_id'] . "' AND isDelete = 0", 0);


					// while($target_d=mysqli_fetch_assoc($target_r))
					// {

					// 	$target_amount_total += $target_d['target_amount'];
					// 	$target_quantity_total += $target_d['target_quantity'];
					// 	// $target_d['created_date'] = date('d-m-Y', strtotime($target_d['created_date']));

					// }

					$order_amount_rr = $db->rp_getValue("orders", "SUM(subtotal)", "MONTH(order_date) ='" . $month1 . "' AND YEAR(order_date) = '" . $year . "' AND sales_id='" . $_REQUEST['sales_executive_id'] . "' AND isDelete=0 AND (status=4 OR status=5 OR status=6)", 0);

					$order_amount = $order_amount_rr;

					$dispatch_qty = $db->rp_getValue("orders", "SUM(total_qty)", "MONTH(order_date) ='" . $month1 . "' AND YEAR(order_date) = '" . $year . "' AND sales_id='" . $_REQUEST['sales_executive_id'] . "' AND isDelete=0 AND (status=4 OR status=5 OR status=6)", 0);
					// $dispatch_qty = $db->rp_getValue("dispatch_detail", "SUM(dispatch_qty)", "isDelete=0 AND order_id IN (SELECT id FROM orders WHERE MONTH(order_date) ='" . $month1 . "' AND YEAR(order_date) = '" . $year . "' AND sales_id='" . $_REQUEST['sales_executive_id'] . "' AND isDelete=0 AND (status=4 OR status=5 OR status=6))", 0);
					// echo $dispatch_qty;
					// echo $order_amount;
					// exit;

					/* Last Month Data Get */
					$last_month_order_amount_rr = $db->rp_getValue(
					    "orders",
					    "SUM(subtotal)",
					    "MONTH(order_date) = '" . $lastmonth . "' AND YEAR(order_date) = '" . $year . "' AND sales_id = '" . $_REQUEST['sales_executive_id'] . "' AND isDelete = 0 AND (status=4 OR status=5 OR status=6)",0);

					$last_month_dispatch_qty = $db->rp_getValue(
					    "orders",
					    "SUM(total_qty)",
					    "MONTH(order_date) = '" . $lastmonth . "' AND YEAR(order_date) = '" . $year . "' AND sales_id = '" . $_REQUEST['sales_executive_id'] . "' AND isDelete = 0 AND (status=4 OR status=5 OR status=6)",0);

					/*$last_month_dispatch_qty = $db->rp_getValue(
					    "dispatch_detail",
					    "SUM(dispatch_qty)",
					    "isDelete = 0 AND order_id IN (SELECT id FROM orders WHERE MONTH(order_date) = '" . $lastmonth . "' AND YEAR(order_date) = '" . $year . "' AND sales_id = '" . $_REQUEST['sales_executive_id'] . "' AND isDelete = 0 AND (status=4 OR status=5 OR status=6))",0);*/

					$last_month_target_amount_total = $db->rp_getValue(
					    "target",
					    "target_amount",
					    "sales_executive_id = '" . $_REQUEST['sales_executive_id'] . "' AND isDelete = 0 AND isActive = 1 AND target_month = '" . $monthArray[$lastmonth] . "' AND target_year = '" . $year . "'",0);

					$last_month_target_quantity_total = $db->rp_getValue(
					    "target",
					    "target_quantity",
					    "sales_executive_id = '" . $_REQUEST['sales_executive_id'] . "' AND isDelete = 0 AND isActive = 1 AND target_month = '" . $monthArray[$lastmonth] . "' AND target_year = '" . $year . "'",0);

					$last_month_order_amount_rr = is_numeric($last_month_order_amount_rr) ? filter_var($last_month_order_amount_rr, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0;

					$last_month_dispatch_qty = is_numeric($last_month_dispatch_qty) ? filter_var($last_month_dispatch_qty, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0;
					$last_month_target_amount_total = is_numeric($last_month_target_amount_total) ? filter_var($last_month_target_amount_total, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0;

					$last_month_target_quantity_total = is_numeric($last_month_target_quantity_total) ? filter_var($last_month_target_quantity_total, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0;

					if ($last_month_order_amount_rr != 0) {
					    $last_month_pending_target = ($last_month_target_amount_total - $last_month_order_amount_rr);
					    if ($last_month_pending_target < 0) {
					        $last_month_pending_target = 0;
					    }
					} else {
					    $last_month_pending_target = $last_month_target_amount_total;
					}

					if ($last_month_dispatch_qty != 0) {
					    $last_month_pending_target_quantity = ($last_month_target_quantity_total - $last_month_dispatch_qty);
					    if ($last_month_pending_target_quantity < 0) {
					        $last_month_pending_target_quantity = 0;
					    }
					} else {
					    $last_month_pending_target_quantity = $last_month_target_quantity_total;
					}

					$target_amount_total += $last_month_pending_target;
					$target_quantity_total += $last_month_pending_target_quantity;
					/* Last Month Data Get */						

					if($order_amount==NULL)
					{
					    $result['order_amount'] =  0;
					}
					else
					{
					    $result['order_amount'] =  $order_amount;
					}

					if($dispatch_qty==NULL)
					{
					    $result['dispatch_qty'] =  0;
					}
					else
					{
					     $result['dispatch_qty'] =  $dispatch_qty;
					}

					/*this value using for calculation  calculation*/
					if ($order_amount == "NULL") {
					    $result['pending_amount'] = 0 - $target_amount_total; //pending amount = target_amount - e Sales Officer nu order amount(ORDER TABLE MATHI E YEAR AND MONTH NO GRAND TOTAL)
					} else {
					    $result['pending_amount'] = $order_amount - $target_amount_total;
					    //$result['pending_amount'] = $invoice_amount - $result['target_amount'];
					    //pending amount = target_amount - e Sales Officer nu order amount(ORDER TABLE MATHI E YEAR AND MONTH NO GRAND TOTAL)
					}

					if ($dispatch_qty == "NULL") {
					    $result['pending_qty'] = 0 - $target_quantity_total; //pending amount = target_amount - e Sales Officer nu order amount(ORDER TABLE MATHI E YEAR AND MONTH NO GRAND TOTAL)
					} else {
					    $result['pending_qty'] = $dispatch_qty - $target_quantity_total;
					    //$result['pending_amount'] = $invoice_amount - $result['target_amount'];
					    //pending amount = target_amount - e Sales Officer nu order amount(ORDER TABLE MATHI E YEAR AND MONTH NO GRAND TOTAL)
					}
					/*this value using for calculation  calculation*/

					if ($result['pending_amount'] > 0) //orderAMOUNT - TARGETAMOUNT IF(GET + THEN PLUS AMOUNT NA 10 %)
					{
					    $result['incentive_amount'] = $result['pending_amount'] * $incentive_percentage / 100;
					} else {
					    $result['incentive_amount'] = "0";
					}
					/*echo $order_amount."   ";
					echo $result['target_amount'];exit;*/
					/*this value using for Display*/
					if ($order_amount == "NULL") {
					    // echo $invoice_amount."eshkj";exit;
					    $result['pending_amount_display'] = abs(0 - $target_amount_total); //pending amount = target_amount - e Sales Officer nu order amount(ORDER TABLE MATHI E YEAR AND MONTH NO GRAND TOTAL)
					}
					// else if($result['invoice_amount'] >= $result['target_amount'])
					else if ($order_amount >= $target_amount_total) {
					    // echo $invoice_amount."2";exit;
					    $result['pending_amount_display'] = 0; //pending amount = target_amount - e Sales Officer nu order amount(ORDER TABLE MATHI E YEAR AND MONTH NO GRAND TOTAL) 
					} else {

					    // echo $invoice_amount."3";exit;
					    $result['pending_amount_display'] = abs($order_amount - $target_amount_total); //pending amount = target_amount - e Sales Officer nu order amount(ORDER TABLE MATHI E YEAR AND MONTH NO GRAND TOTAL)
					}

					if ($dispatch_qty == "NULL") {
					    // echo $invoice_amount."eshkj";exit;
					    $result['pending_qty_display'] = abs(0 - $target_quantity_total); //pending amount = target_amount - e Sales Officer nu order amount(ORDER TABLE MATHI E YEAR AND MONTH NO GRAND TOTAL)
					}
					// else if($result['invoice_amount'] >= $result['target_amount'])
					else if ($dispatch_qty >= $target_quantity_total) {
					    // echo $invoice_amount."2";exit;
					    $result['pending_qty_display'] = 0; //pending amount = target_amount - e Sales Officer nu order amount(ORDER TABLE MATHI E YEAR AND MONTH NO GRAND TOTAL) 
					} else {

					    // echo $invoice_amount."3";exit;
					    $result['pending_qty_display'] = abs($dispatch_qty - $target_quantity_total); //pending amount = target_amount - e Sales Officer nu order amount(ORDER TABLE MATHI E YEAR AND MONTH NO GRAND TOTAL)
					}
					/*this value using for Display*/

					$target[] = $result;
					
					$ack=array( "ack"=>1,"ack_msg"=>"Target Data Found","developer_msg"=>"Target Data Found","result"=>$target);
					$db->printJSON($ack);
				}
				else
				{
					$ack=array( "ack"=>0,"ack_msg"=>"No such a Target Data Found","developer_msg"=>"No such a Target Data Found");
					$db->printJSON($ack);
				}
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Sales Officer ID required","developer_msg"=>"Sales Officer ID required");
				$db->printJSON($ack);
			}
		}


		else if($service=="mobile_no_suggestion" || $service==109)
		{
			if(isset($_REQUEST['mobile_no']) && $_REQUEST['mobile_no']!="")
			{
				$member_data=$db->rp_getData("member","id,name,mobile_no","mobile_no LIKE '".$_REQUEST['mobile_no']."%'","mobile_no",0,"0,6");
				if($member_data)
				{
					$member_array=array();
					while($member_d=mysqli_fetch_assoc($member_data))
					{
						$member_array[]=$member_d;
					}
					$ack=array( "ack"=>1,"ack_msg"=>"Data Found","developer_msg"=>"Data Found","result"=>$member_array);
					$db->printJSON($ack);
				}
				else
				{
					$ack=array( "ack"=>0,"ack_msg"=>"No such a Data Found","developer_msg"=>"No such a Data Found");
					$db->printJSON($ack);
				}
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Sales Officer ID required","developer_msg"=>"Sales Officer ID required");
				$db->printJSON($ack);
			}
		}

		else if($service=="account_history" || $service==110)
        {
        	if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="")
        	{
        		$order_count = $db->rp_getTotalRecord("orders","customer_id='".$_REQUEST['cid']."' AND sales_id='".$_REQUEST['sales_id']."' AND  isDelete=0 AND (status=1 OR status=2 OR status=4)",0);
        
        		$received_amount = $db->rp_getValue("account_transaction","abs(SUM(debit))","cid='".$_REQUEST['cid']."' AND isDelete=0",0);
        		if($received_amount=="")
        		{
        			$received_amount = 0;
        		}
        		
        		$order_Amount = $db->rp_getValue("orders","SUM(grand_total)","customer_id='".$_REQUEST['cid']."' AND sales_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND (status=1 OR status=2 OR status=4)",0);
        		if($order_Amount==NULL)
        		{
        			$order_pending_amount = 0;
        		}
        		else
        		{
        			$order_pending_amount = $order_Amount - $received_amount;
        		}
        		
        		//$invoice_amount = $db->rp_getValue("invoice","SUM(amount)","customer_id='".$_REQUEST['cid']."' AND isDelete=0",0);
        		$invoice_amount = $db->rp_getValue("invoice_new","SUM(grand_total)","customer_id='".$_REQUEST['cid']."' AND isDelete=0",0);
        		if($invoice_amount==NULL)
        		{
        			$invoice_amount = 0;
        		}
        		
        		$receipt_amount = $db->rp_getValue("payment","SUM(paid_amount)","customer_id='".$_REQUEST['cid']."' AND isDelete=0",0);
        		
        		$pending_amount = $invoice_amount - $receipt_amount;
        		
        		$account_array=array();

        		//$account_history=$db->rp_getData("invoice","*","customer_id='".$_REQUEST['cid']."' AND isActive=1 AND isDelete=0","",0);
        		$account_history=$db->rp_getData("invoice_new","*","customer_id='".$_REQUEST['cid']."' AND isActive=1 AND isDelete=0","",0);
        		if($account_history)
        		{
        			while($account_history_d=mysqli_fetch_assoc($account_history))
        			{
        				
        				if ($account_history_d['image_path']!="")
        					{
           			
        	           		$account_history_d['image_path']= SITEURL.INVOICE.$account_history_d['image_path'];
        	           		}
                   		else
                   		{
                   			$account_history_d['image_path'] = "";	
                   		}
                   		if ($account_history_d['attechment']!="")
        					{
           			
        	           		$account_history_d['attechment']= SITEURL.INVOICE.$account_history_d['attechment'];
        	           		}
                   		else
                   		{
                   			$account_history_d['attechment'] = "";	
                   		}	
        				$account_history_d['invoice_date'] = date('d-m-Y h:i A',strtotime($account_history_d['invoice_date']));
        				$account_history_d['created_date'] = date('d-m-Y h:i A',strtotime($account_history_d['created_date']));
        				$account_history_d['data_type']="invoice";
        				$account_history_d['account_date'] = date('d-m-Y',strtotime($account_history_d['created_date']));
        				$account_array[]=$account_history_d;
        			}
        		}
        		$payment_history=$db->rp_getData("payment","*","customer_id='".$_REQUEST['cid']."' AND isActive=1 AND isDelete=0","",0);
        		if($payment_history)
        		{
        			
        			while($payment_history_d=mysqlii_fetch_assoc($payment_history))
        			{
        				$payment_history_d['payment_no']=$payment_history_d['receipt_no'];
        				$payment_history_d['data_type']="payment";
        				$payment_history_d['account_date'] = date('d-m-Y',strtotime($payment_history_d['payment_date']));
        				$account_array[]=$payment_history_d;
        			}
        		}
        		// print_r($account_array);
        		$tempArr = array();
				foreach ($account_array as $key => $val) {
				     $tempArr['account_date'][$key] = $val['account_date'];
				}
				array_multisort($tempArr['account_date'], SORT_ASC,$account_array); 
        		if(!empty($account_array))
        		{        			
	    			$ack=array( "ack"=>1,"ack_msg"=>"Data Found","developer_msg"=>"Data Found","order count"=>$order_count,"order_amount"=>number_format($order_Amount,2),"invoice_amount"=>number_format($invoice_amount,2),"received_amount"=>number_format($received_amount,2),"pending_amount"=>number_format($pending_amount,2),"order_pending_amount"=>number_format($order_pending_amount,2),"result"=>$account_array);
	    			$db->printJSON($ack);
        		}
        		else
        		{
        			$ack=array( "ack"=>0,"ack_msg"=>"No such a Data Found","developer_msg"=>"No such a Data Found","order count"=>number_format($order_count,2),"order_amount"=>number_format($order_Amount,2),"invoice_amount"=>number_format($invoice_amount,2),"received_amount"=>number_format($received_amount,2),"pending_amount"=>number_format($pending_amount,2),"order_pending_amount"=>number_format($order_pending_amount,2));
        			$db->printJSON($ack);
        		}
        	}
        	else
        	{
        		$ack=array( "ack"=>0,"ack_msg"=>"Sales Customer ID required","developer_msg"=>"Sales Customer ID required");
        		$db->printJSON($ack);
        	}
        }

		else if($service=='customer_type' || $service==111)
		{
			$customer_type = array();
			$customer = $db->rp_getData("customer_type","id,name","isDelete=0","",0);
			if($customer)
			{
				while($customer_type_d = mysqli_fetch_assoc($customer))
				{
					$customer_type[] = $customer_type_d;
				}
			}

			if(!empty($customer_type))
			{
				$reply=array("ack"=>1,"developer_msg"=>"Customer Type Get successfully!!","ack_msg"=>"Customer Type Get successfully!!","result"=>$customer_type);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Customer Type Not Get!!","ack_msg"=>"Customer Type Not Get!!");
			}
			echo json_encode($reply);
		}
		
		else if($service=='get_user_order_detail' || $service==120)
		{
			if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="")
			{
				$month=date("m");
				$year=date("Y");
				
				$order_count = $db->rp_getTotalRecord("orders","MONTH(order_date) ='".$month."' AND YEAR(order_date) = '".$year."' AND sales_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND (status=1 OR status=2 OR status=4)",0);
				
				$order_amount = $db->rp_getValue("orders","SUM(grand_total)","MONTH(order_date) ='".$month."' AND YEAR(order_date) = '".$year."' AND sales_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND (status=1 OR status=2 OR status=4)",0);
				
				if($order_amount==NULL)
				{
				   $order_amount = 0; 
				}
				
				$invoice_amount = $db->rp_getValue("invoice_new","SUM(grand_total)","MONTH(invoice_date) ='".$month."' AND YEAR(invoice_date) = '".$year."' AND FIND_IN_SET('".$_REQUEST['sales_id']."',sales_id) AND isDelete=0",0);
			
				if($invoice_amount==NULL)
				{
				   $invoice_amount = 0; 
				}
				
				$receipt_amount = $db->rp_getValue("payment","SUM(paid_amount)","MONTH(payment_date) ='".$month."' AND YEAR(payment_date) = '".$year."' AND FIND_IN_SET('".$_REQUEST['sales_id']."',sales_executive_id) AND isDelete=0 AND payment_status=1",0);
				if($receipt_amount==NULL)
				{
				   $receipt_amount = 0; 
				}
				
				$pending_amount = $invoice_amount - $receipt_amount;
				
				$ack=array( "ack"=>1,"ack_msg"=>"Data Found","developer_msg"=>"Data Found","order count"=>$order_count,"order_amount"=>$order_amount,"invoice_amount"=>$invoice_amount,"pending_amount"=>$pending_amount,"pending_order_amount"=>$receipt_amount);
				$db->printJSON($ack);
				
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Sales Officer ID required","developer_msg"=>"Sales Officer ID required");
				$db->printJSON($ack);
			}
		}

		else if($service=='upload_meeting_member_image' || $service==125)
		{
			if(isset($_REQUEST['meeting_id']) && $_REQUEST['meeting_id']!="" && isset($_REQUEST['member_id']) && $_REQUEST['member_id']!="")
			{
				$reply = $objMeeting->upload_member_image($_REQUEST['meeting_id'],$_REQUEST['member_id'],$_FILES);
				if($reply['ack']==1)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Member Image Update Successfully","ack_msg"=>"Member Image Update Successfully");
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Member Image Update Failed","ack_msg"=>"Member Image Update Failed");
					//echo json_encode($reply);
				}
				$db->printJSON($reply);
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Member Id AND Meeting Id required","developer_msg"=>"Member Id AND Meeting Id required");
				$db->printJSON($ack);
			}
		}
		
	}
	else
	{
		$ack=array( "ack"=>0,"ack_msg"=>"Internal error!!","developer_msg"=>"Check your API Key or contact Admin","extra"=>array("requested_params"=>$_REQUEST,"other"=>array()));
		$db->printJSON($ack);
	}
}
else
{
	$ack=array( "ack"=>0,"ack_msg"=>"Internal error!!","developer_msg"=>"Check your API Key or contact Admin","extra"=>array("requested_params"=>$_REQUEST,"other"=>array()));
	$db->printJSON($ack);
}
$db->disconnect();
?>