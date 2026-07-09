<?php 
include('connect.php');
if($is_valid_api_key)
{
	if($is_valid_service)
	{
		include('../include/class.leave_request.php');
		$objLeaveRequest=new LeaveRequest();

		include('../include/orders.class.php');
		$objOrder=new Order();

		require_once("../include/push_notification.class.php");
		$objPushNotification=new PushNotification();
		
		if($service=='leave_type' || $service==112)
		{
			$leave_type = array();
			$leave = $db->rp_getData("leave_type","*","isDelete=0","id DESC",0);
			if($leave)
			{
				while($leave_type_d = mysqli_fetch_assoc($leave))
				{
					$leave_type[] = $leave_type_d;
				}
			}

			if(!empty($leave_type))
			{
				$reply=array("ack"=>1,"developer_msg"=>"Leave Type Get successfully!!","ack_msg"=>"Leave Type Get successfully!!","result"=>$leave_type);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Leave Type Not Get!!","ack_msg"=>"Leave Type Not Get!!");
			}
			echo json_encode($reply);
		}
		
		else if($service=='add_leave' || $service==113)
		{
		    if(isset($_REQUEST['leave_type']) && isset($_REQUEST['sales_id']) && isset($_REQUEST['start_date']) && isset($_REQUEST['start_time']) && isset($_REQUEST['end_date']) && isset($_REQUEST['end_time']) )
		    {
			    
				$detail['user_id']        	     = 0;
				$detail['leave_type']            = isset($_REQUEST['leave_type'])?$db->clean($_REQUEST['leave_type']):"";
				$detail['sales_executive_id']    = isset($_REQUEST['sales_id'])?$db->clean($_REQUEST['sales_id']):"";
				$detail['start_date']            = isset($_REQUEST['start_date'])?date("Y-m-d", strtotime($_REQUEST['start_date'])) : "";
				$detail['start_time']            = isset($_REQUEST['start_time'])?$db->clean($_REQUEST['start_time']):"";
				$detail['end_date']              = isset($_REQUEST['end_date'])?date("Y-m-d", strtotime($_REQUEST['end_date'])) : "";
				$detail['end_time']              = isset($_REQUEST['end_time'])?$db->clean($_REQUEST['end_time']):"";
				$detail['reason']                = isset($_REQUEST['reason'])?$db->clean($_REQUEST['reason']):"";
				$detail['latitude']              = isset($_REQUEST['latitude'])?$db->clean($_REQUEST['latitude']):"";
				$detail['longitude']             = isset($_REQUEST['longitude'])?$db->clean($_REQUEST['longitude']):"";
				$detail['entry_flag']             = isset($_REQUEST['entry_flag'])?$db->clean($_REQUEST['entry_flag']):"5";
				$detail['update_entry_flag']             = isset($_REQUEST['update_entry_flag'])?$db->clean($_REQUEST['update_entry_flag']):"5";
				$detail['leave_category']              = isset($_REQUEST['leave_category'])?$db->clean($_REQUEST['leave_category']):"";
				$reply=$objLeaveRequest->InsertLeave($detail,$_FILES);
				if($reply['ack']==1)
				{
					$LeaveR = $db->rp_getData("leave_request","*","id='".$reply['id']."'");
					$leave = mysqli_fetch_assoc($LeaveR);
					$reply=array("ack"=>1,"developer_msg"=>$reply['developer_msg'],"ack_msg"=>$reply['ack_msg'],"result"=>$leave);
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>$reply['developer_msg'],"ack_msg"=>$reply['ack_msg']);
				}
				echo json_encode($reply);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Some Perameters Is Missing","ack_msg"=>"Failed! Leave Insert Failed.");
				echo json_encode($reply);
			}
		}
		
		else if($service=='get_leave' || $service==114)
		{
			$meeting = array();
			$sales_id = isset($_REQUEST['sales_id'])?$db->clean($_REQUEST['sales_id']):"";
			$leave_type = isset($_REQUEST['leave_type'])?$db->clean($_REQUEST['leave_type']):"";
			$status_id = isset($_REQUEST['status_id'])?$db->clean($_REQUEST['status_id']):"";
			//$leave_category = isset($_REQUEST['leave_category'])?$db->clean($_REQUEST['leave_category']):"";
			
			if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
			{
			  //$Where .= " AND DATE(created_date) <= '".date("Y-m-d",strtotime($_REQUEST['ToDate']))."' ";
			  $Where .= " AND DATE(end_date) <= '".date("Y-m-d",strtotime($_REQUEST['ToDate']))."' ";
			}

			if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
			{
				$Where .= " AND DATE(start_date) >= '".date("Y-m-d",strtotime($_REQUEST['FromDate']))."'";
			}
			
			if($leave_type!="")
			{
				$Where.= " AND leave_type='".$leave_type."'";
			}
			if($status_id!="")
			{
				$Where.= " AND status='".$status_id."'";
			}
			
			$month=date("m");
			$leave_count = $db->rp_getTotalRecord("leave_request","MONTH(created_date) = '".$month."' AND sales_executive_id='".$sales_id."' AND isDelete=0".$Where,0);
			
			if($sales_id)
			{
				$leave_data = $db->rp_getData("leave_request","*","sales_executive_id='".$sales_id."' AND isDelete=0".$Where,"id DESC",0);
				if($leave_data)
				{
					$leave_status_array = array("0"=>"Generate","1"=>"Accepted","2"=>"Rejected","3"=>"Cancel");
					//$leave_category_array = array("1"=>"First Half","2"=>"Second Half","3"=>"Full Day");
					while($leave_d = mysqli_fetch_assoc($leave_data))
					{
					    
					    $img = explode(",", $leave_d['file_path']);
						$imgpath = array();
						for ($i=0; $i < sizeof($img); $i++)
						{ 
							$imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$leave_d['id']."' AND id='".$img[$i]."'");
						}
						$leave_d['file_path'] = ($leave_d['file_path']!= "")?$imgpath:[];
					    $leave_d['start_date'] = date('d F Y',strtotime($leave_d['start_date']));
					    if($leave_d['start_time']=="00:00:00"){
					    	$leave_d['start_time']="";
					    }
					    else{

					    	$leave_d['start_time'] = date('h:i A',strtotime($leave_d['start_time']));
					    }
						
						$leave_d['end_date'] = date('d F Y',strtotime($leave_d['end_date']));
						if($leave_d['end_time']=="00:00:00"){
					    	$leave_d['end_time']="";
					    }
					    else{
					    	
					    	$leave_d['end_time'] = date('h:i A',strtotime($leave_d['end_time']));
					    }
						
						$leave_d['leave_type'] = $db->rp_getValue("leave_type","name","id='".$leave_d['leave_type']."'",0);
						$leave_d['leave_status'] = $leave_status_array[$leave_d['status']];
						$leave_d['leave_category'] = $leave_d['leave_category'];
						$leave_d['color_code'] = $db->leave_status_color[$leave_d['leave_status']];

						$leave_d['created_date'] = date('d-m-Y h:i A',strtotime($leave_d['created_date']));
						if($leave_d['status']=="0"){

							$leave_d['action_allow']="1";
							}else{

							$leave_d['action_allow']="0";
							}
						$meeting[] = $leave_d;
					}

					/*Get Leave Count*/
				$leavecountdata = array();
				$LeaveCountData = $db->rp_getData('leave_request',"DISTINCT(status)","isDelete=0 AND status!='-1'","",0);
				//$OrderData = $this->db->rp_getData('orders',"status",$where,"id DESC",0,$limit);
				/*$status_array = array("-2"=>"Disapproved","0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Canceled","4"=>"Partially Dispatched");*/
				$status_key_array = array("0","1","2","3");

				while($leave_count_d = mysqli_fetch_assoc($LeaveCountData))
				{
					$leave_count_d['count']=$db->rp_getTotalRecord("leave_request","sales_executive_id ='".$_REQUEST['sales_id']."' AND status='".$leave_count_d['status']."' AND isDelete=0",0);
					
					if (($key = array_search($leave_count_d['status'], $status_key_array)) !== false) {
					    unset($status_key_array[$key]);
					}

					$leave_count_d['status_slug'] = $leave_status_array[$leave_count_d['status']];

					$leave_count_d['status'] = $leave_count_d['status'];
					// echo "<pre>"; print_r($Order_d);
					$leave_count_d['color_code'] = $db->leave_status_color[$leave_count_d['status_slug']];

					if($leave_count_d['color_code']=="")
					{
						$leave_count_d['color_code'] = "";
					}

					if($leave_count_d['status_slug']=="")
					{
						$leave_count_d['status_slug'] = "";
					}
					$leavecountdata[]=$leave_count_d;
				}
				foreach ($status_key_array as $key => $remainval) {
					$leave_count_d['count'] = 0;
					$leave_count_d['status'] = $remainval;
					$leave_count_d['status_slug'] = $leave_status_array[$remainval];
					$leave_count_d['color_code'] = $db->leave_status_color[$leave_status_array[$remainval]];
					$leavecountdata[]=$leave_count_d;
				}

				$leave_status = $leavecountdata;
				/*Get Order Count*/
				}

				if(!empty($leave_data))
				{
					$reply=array("ack"=>1,"developer_msg"=>"Leave Detail Get successfully!!","ack_msg"=>"Leave Detail Get successfully!!","Leave Count"=>$leave_count,"result"=>$meeting,"leave_status"=>$leave_status);
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Leave Detail Not Get!!","ack_msg"=>"Leave Detail Not Get!!");
				}
				echo json_encode($reply);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Meeting Detail Not Get!!","ack_msg"=>"Meeting Detail Not Get!!");
				echo json_encode($reply);
			}
		}
		
		
		else if($service=='update_leave' || $service==115)
        {
            if(isset($_REQUEST['id']) && isset($_REQUEST['leave_type']) && isset($_REQUEST['sales_id']) && isset($_REQUEST['start_date']) && isset($_REQUEST['start_time']) && isset($_REQUEST['end_date']) && isset($_REQUEST['end_time']) )
            {
        	    $detail['user_id']        	     = 0;
        		$detail['id']                    = isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
        		$detail['leave_type']            = isset($_REQUEST['leave_type'])?$db->clean($_REQUEST['leave_type']):"";
        		$detail['sales_executive_id']    = isset($_REQUEST['sales_id'])?$db->clean($_REQUEST['sales_id']):"";
        		$detail['start_date']            = isset($_REQUEST['start_date'])?date("Y-m-d", strtotime($_REQUEST['start_date'])) : "";
        		$detail['start_time']            = isset($_REQUEST['start_time'])?$db->clean($_REQUEST['start_time']):"";
        		$detail['end_date']              = isset($_REQUEST['end_date'])?date("Y-m-d", strtotime($_REQUEST['end_date'])) : "";
        		$detail['end_time']              = isset($_REQUEST['end_time'])?$db->clean($_REQUEST['end_time']):"";
        		$detail['reason']                = isset($_REQUEST['reason'])?$db->clean($_REQUEST['reason']):"";
        		$detail['latitude']              = isset($_REQUEST['latitude'])?$db->clean($_REQUEST['latitude']):"";
        		$detail['longitude']             = isset($_REQUEST['longitude'])?$db->clean($_REQUEST['longitude']):"";

        		$detail['entry_flag']             = isset($_REQUEST['entry_flag'])?$db->clean($_REQUEST['entry_flag']):"5";
				$detail['update_entry_flag']             = isset($_REQUEST['update_entry_flag'])?$db->clean($_REQUEST['update_entry_flag']):"5";
        
        		$reply=$objLeaveRequest->UpdateLeave($detail,$_FILES);
        		
        		if($reply['ack']==1)
        		{
        			$LeaveR = $db->rp_getData("leave_request","*","id='".$reply['id']."'");
        			$leave = mysqli_fetch_assoc($LeaveR);
        			$reply=array("ack"=>1,"developer_msg"=>$reply['developer_msg'],"ack_msg"=>$reply['ack_msg'],"result"=>$leave);
        		}
        		else
        		{
        			$reply=array("ack"=>0,"developer_msg"=>$reply['developer_msg'],"ack_msg"=>$reply['ack_msg']);
        		}
        		echo json_encode($reply);
        	}
        	else
        	{
        		$reply=array("ack"=>0,"developer_msg"=>"Some Perameters Is Missing","ack_msg"=>"Failed! Leave Insert Failed.");
        		echo json_encode($reply);
        	}
        }
        
        else if($service=='delete_leave' || $service==116)
		{
			$id=$_REQUEST['leave_id'];

			$detail['id']=$_REQUEST['leave_id'];
			$reply=$objLeaveRequest->DeleteLeave($detail);

			/*$delete=$db->rp_update("leave_request",array("isDelete"=>1),"id='".$id."'",0);
			if($delete)
			{
				$ack=array("ack"=>1,"ack_msg"=>"Data Delete Successfully");
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Data Delete Failed");
			}*/
			echo json_encode($reply);
		}
		
		else if($service=='delete_notification' || $service==117)
		{
			$notification_id = $_REQUEST['notification_id'];
			$user_id = $_REQUEST['user_id'];
			
			if($user_id!="")
			{
			    $delete=$db->rp_update("notification",array("isDelete"=>1),"id='".$notification_id."' AND user_id='".$user_id."'",0);
			}
			else
			{
			    $delete=$db->rp_update("notification",array("isDelete"=>1),"id='".$notification_id."'",0);    
			}
			if($delete)
			{
				$ack=array("ack"=>1,"ack_msg"=>"Data Delete Successfully");
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Data Delete Failed");
			}
			echo json_encode($ack);
		}
		
		else if($service=='update_notification_flag' || $service==118)
		{
			$notification_flag = $_REQUEST['notification_flag'];
			$sales_id = $_REQUEST['sales_id'];
			
			if($sales_id!="")
			{
			    $Update=$db->rp_update("sales_executive",array("notification_flag"=>$notification_flag),"id='".$sales_id."'",0);
			}
			if($Update)
			{
				$ack=array("ack"=>1,"ack_msg"=>"Notification Flag Update Successfully");
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Notification Flag Update Failed");
			}
			echo json_encode($ack);
		}
		
		else if($service=='update_sales_executive_profile' || $service==119)
		{
		    $detail['id']	    	     = isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
		    $detail['name']		         = isset($_REQUEST['name'])?$db->clean($_REQUEST['name']):"";
			$detail['email']	         = isset($_REQUEST['email'])?$db->clean($_REQUEST['email']):"";
			$detail['address']	         = isset($_REQUEST['address'])?$db->clean($_REQUEST['address']):"";
		    $detail['country']	    	 = isset($_REQUEST['country'])?$db->clean($_REQUEST['country']):"";
			$detail['state']	     	 = isset($_REQUEST['state'])?$db->clean($_REQUEST['state']):"";
			$detail['city']	     		 = isset($_REQUEST['city'])?$db->clean($_REQUEST['city']):"";
			
			require_once('../include/class.sales_executive.php');
			$inquiry1=new SalesExecutive();
			$ack=$inquiry1->UpdateSalesExecutiveProfile($detail,$_FILES);
			if($ack['ack']==1)
			{
				$reply[]=array("ack"=>1,"ack_msg"=>"Customer successfully saved !!");		
				//$updated[]=array("server_id"=>$ack['inserted_id'],"local_id"=>$local_id,"area"=>$ack['areas']);	
				$updated[]=array("server_id"=>$ack['inserted_id'],"local_id"=>$local_id,"area"=>$ack['areas']);	
			}
			else
			{
				$error[]=$ack;							
			}
			$db->printJSON($ack);
		}
		
		else if($service=='multiple_delete' || $service==121)
		{
			$id=$_REQUEST['id'];
			$type=$_REQUEST['type'];
			if(isset($_REQUEST['id']) && isset($_REQUEST['type']))
			{
			    if($type==1)
			    {
			        $ctable = "expense";    
			    }
			    
			    if($type==2)
			    {
			        $ctable = "complain";   
			    }
			    
			    if($type==3)
			    {
			        $ctable = "visit";   
			    }
			    
			    if($type==4)
			    {
			        $ctable = "meeting";    
			    }

			    if($type==5)
			    {
			        $ctable = "request";    
			    }
			    
			    $delete=$db->rp_update($ctable,array("isDelete"=>1),"id='".$id."'",0);
    			if($delete)
    			{
    				if($type==1)
    				{
    					// send notification
    					$ExpenseR = $db->rp_getData("expense","*","id='".$id."' AND isDelete=0 AND isActive=1");
					    $ExpenseData = mysqli_fetch_assoc($ExpenseR);

					    $expence_category_nm = $db->rp_getValue("expence_category","name","id='".$ExpenseData['category_id']."'",0);
 
					    $notification_description = $expence_category_nm." for date ".date("d-m-Y",strtotime($ExpenseData['expense_date']))." has been deleted";  
						 
						$objPushNotification->commonNotification($ExpenseData['sales_executive_id'],$id,"expense","Delete Expense",$notification_description,"sales_executive","expense");
						// send notification
    				}
    				$ack=array("ack"=>1,"ack_msg"=>"Data Delete Successfully");
    			}
    			else
    			{
    				$ack=array("ack"=>0,"ack_msg"=>"Data Delete Failed");
    			} 
			}
			else
			{
			    $ack=array("ack"=>0,"ack_msg"=>"Id And Type Required.");
			}
			
			echo json_encode($ack);
		}

		else if($service=='get_all_customer' || $service==126)
		{
			$customer = array();
			$user_id = isset($_REQUEST['user_id'])?$db->clean($_REQUEST['user_id']):"";
			$area_id = isset($_REQUEST['area_id'])?$db->clean($_REQUEST['area_id']):"";
			$class_id = isset($_REQUEST['class_id'])?$db->clean($_REQUEST['class_id']):"";
			// echo $user_id;exit;
			if($user_id)
			{
				if ($area_id != "" || $class_id != "") {
		            $outlet_area_r = $db->rp_getData("executive_map_area", "*", "class_id=" . $class_id . " AND area_id = '" . $area_id . "' AND isDelete=0", "", 0);

		            while ($outlet_area_d = mysqli_fetch_assoc($outlet_area_r)) {
			            $executive_id[] = $outlet_area_d['executive_id'];
			        }

			        if (!empty($executive_id)) {
			            $executive_id = array_unique($executive_id);
			            $ids = implode(",", $executive_id);
			            $cidWhere = " AND id IN (".$ids.")";
			        }
			    }

				$customer_data = $db->rp_getData("executive","*","seid='".$user_id."' AND isDelete=0 AND isActive=1".$cidWhere,"id DESC",0);
			}
			else
			{
				$customer_data = $db->rp_getData("executive","*","isDelete=0 AND isActive=1","id DESC",0);
			}
				
			if($customer_data)
			{
				while($customer_data_d = mysqli_fetch_assoc($customer_data))
				{
				    
				    $img = explode(",", $customer_data_d['image_path']);
					$imgpath = array();
					for ($i=0; $i < sizeof($img); $i++)
					{ 
						$imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$customer_data_d['id']."' AND id='".$img[$i]."'");
					}
					$customer_data_d['file_path'] = ($customer_data_d['file_path']!= "")?$imgpath:[];
					$customer_data_d['latitude'] = str_replace("\n","",$customer_data_d['latitude']);
					$customer_data_d['longitude'] = str_replace("\n","",$customer_data_d['longitude']);
				    $customer_data_d['created_date'] = date('d-m-Y h:i A',strtotime($customer_data_d['created_date']));
					$customer[] = $customer_data_d;
				}
			}

			if(!empty($customer))
			{
				$reply=array("ack"=>1,"developer_msg"=>"Customer Get successfully!!","ack_msg"=>"Customer Get successfully!!","result"=>$customer);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Customer Detail Not Get!!","ack_msg"=>"Customer Detail Not Get!!");
			}
			echo json_encode($reply);
		}

		else if($service=='update_inquiry_status' || $service==127)
		{
			$id=$_REQUEST['id'];
			$flag=$_REQUEST['flag'];
			$status = $_REQUEST['status'];
			
			if($id)
			{
				if($flag == "no_order_inquiry")
				{
					$Update=$db->rp_update("no_order_inquiry",array("status"=>$status),"id='".$id."'",0);
					$db->addStatusTimelineEntry($id,$status,$_REQUEST['user_id']);
				}
				else
				{
					$Update=$db->rp_update("customer_inquiry",array("status"=>$status),"id='".$id."'",0);
				}
				if($Update)
				{
					/*create customer*/
					/*	if($status==3)
					{
						$where = " id='".$inquiry_id."' AND isDelete=0";
						$inquiry_r = $db->rp_getData("no_order_inquiry","*",$where,"",0);
						while($inquiry_d = mysqli_fetch_assoc($inquiry_r))
						{
							$rows = array("type_of_executive","dealer_distributor_id","company_name","cname","phone","address","country","state","city");

							$values = array($inquiry_d['executive_type'],$inquiry_d['distributor_id'],$inquiry_d['company_name'],$inquiry_d['person_name'],$inquiry_d['mobile_number'],$inquiry_d['address'],$inquiry_d['country'],$inquiry_d['state'],$inquiry_d['city']);

							$insert = $db->rp_insert("executive",$values,$rows,0);

							
								if($inquiry_d['class_id']==0 && $inquiry_d['area_id']==0)
								{
									$class_id = $db->rp_getValue("class","id","name='".$inquiry_d['state']."'",0);
									$area_id = $db->rp_getValue("area","id","name='".$inquiry_d['city']."'",0);
								}
								else
								{
									$class_id = $inquiry_d['class_id'];
									$area_id = $inquiry_d['area_id'];
								}

								$rows_insert = array("class_id","area_id","executive_id","executive_type","isDelete","isActive");
								$values_insert = array($class_id,$area_id,$insert,$inquiry_d['executive_type'],0,1);
								$inserted_id = $db->rp_insert("executive_map_area",$values_insert,$rows_insert,0);

						}
						
							$row = array("isDelete"=>1);
							$update = $db->rp_update("no_order_inquiry",$row,"id='".$inquiry_id."'",0);
					
					}*/
					/*create customer*/
					$ack=array("ack"=>1,"ack_msg"=>"Inquiry Status Update Successfully");
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Inquiry Status Update Failed");
				}	
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Inquiry Id Required");
			}
			echo json_encode($ack);
		}

		else if($service=='add_vehicle_expense' || $service==128)
		{
			if(isset($_REQUEST['sales_executive_id']))
			{
				$detail['sales_executive_id']	= isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";
				$detail['start_date_time']	    = date('Y-m-d H:i:s');
				$detail['start_km']	    		= isset($_REQUEST['start_km'])?$db->clean($_REQUEST['start_km']):"";
				$detail['end_km']	    		= isset($_REQUEST['end_km'])?$db->clean($_REQUEST['end_km']):"";
				$detail['type_flag']	    	= isset($_REQUEST['type_flag'])?$db->clean($_REQUEST['type_flag']):"";
				$detail['subcat_slug']	    =  isset($_REQUEST['subcat_slug'])?$db->clean($_REQUEST['subcat_slug']):"";

				include('../include/expense.class.php');
				$objExpense=new Expense();
				
				$reply=$objExpense->AddVehicleexpense($detail,$_FILES);
				if($reply['ack']==1)
				{
					$result=$db->rp_getData("expense_tmp","*","id='".$reply['inserted_id']."'","",0);
					$r=mysqli_fetch_assoc($result);
					$ack=array("ack"=>1,"ack_msg"=>"Expense Detail Add Successfully!!","developer_msg"=>"Expense Detail Add Successfully","result"=>$r);
					$db->printJSON($ack);
				}
				else
				{
					$db->printJSON($reply);
				}
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Something wrong !! Please Try Again Later!!","developer_msg"=>"not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		}

		else if($service=='category_discount' || $service==129)
		{

			if($_REQUEST['cart_type']=="3"){
				$table="order_product_item";
			}else if($_REQUEST['cart_type']=="2"){
				$table="quotation_product_item";
			}
			else{
				$table="cart_item";

			}
			$top_category = array();
			$cart_id = $_REQUEST['cart_id'];
			$top_cat_data = $db->rp_getData("top_category_master","*","isDelete=0 AND isActive=1","id ASC",0);
			if($top_cat_data)
			{
				while($top_cat_data_d = mysqli_fetch_assoc($top_cat_data))
				{
					$category = array();
					$cat_data = $db->rp_getData("category_master","*","tcid='".$top_cat_data_d['id']."' AND isDelete=0 AND isActive=1");
					while($cat_data_d = mysqli_fetch_assoc($cat_data))
					{
						/*get category discount*/
						 if($_REQUEST['cart_type']=="2")
						 {
							$get_cat_discount = $db->rp_getValue($table,"discount","quotation_id='".$cart_id."' AND cat_id='".$cat_data_d['id']."' AND top_cat_id='".$top_cat_data_d['id']."'",0,"limit 1");
						}else{
							$get_cat_discount = $db->rp_getValue($table,"discount","order_id='".$cart_id."' AND cat_id='".$cat_data_d['id']."' AND top_cat_id='".$top_cat_data_d['id']."'",0,"limit 1");
						}
						if($get_cat_discount!="" && $get_cat_discount!=NULL)
						{
							$cat_data_d['discount'] = $get_cat_discount;
						}
						else
						{
							$cat_data_d['discount'] = "";
						}
						
						/*get category discount*/
						$category[] = $cat_data_d;
					}
					$top_cat_data_d['category'] = $category;
					$top_category[] = $top_cat_data_d;
				}
			}

			if(!empty($top_category))
			{
				$reply=array("ack"=>1,"developer_msg"=>"Data Get successfully!!","ack_msg"=>"Data Get successfully!!","result"=>$top_category);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Data Not Get!!","ack_msg"=>"Data Not Get!!");
			}
			echo json_encode($reply);
		}

		else if($service=='cart_discount_update' || $service==130)
		{
			
			if(isset($_REQUEST['cart_id']) && isset($_REQUEST['cart_id']) && isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['sales_executive_id']))
			{
				$detail['cart_id']=isset($_REQUEST['cart_id'])?$db->clean($_REQUEST['cart_id']):"";
				$detail['cart_type']=isset($_REQUEST['cart_type'])?$db->clean($_REQUEST['cart_type']):"";
				$detail['mode']=isset($_REQUEST['mode'])?$db->clean($_REQUEST['mode']):"";
				$detail['sales_executive_id']=isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";

				$products=(isset($_REQUEST['products']) && $_REQUEST['products']!="")?json_decode($_REQUEST['products'],true):array();
				/*include('../include/orders.class.php');
				$objOrder=new Order();*/
				$ack=$objOrder->UpdateCartDiscount($detail,$products);
				$db->printJSON($ack);
			}
			else
			{
				
				$ack=array( "ack"=>0,"ack_msg"=>"Internal Error!!Some Parameter Missing!","developer_msg"=>"Internal Error!!Some Parameter Missing!");
				$db->printJSON($ack);
			}
		}

		else if($service=='edit_order' || $service==131)
		{
			if(isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['order_id']) && isset($_REQUEST['order_id']))
			{
				$detail['order_id']=isset($_REQUEST['order_id'])?$db->clean($_REQUEST['order_id']):"";
				$detail['cid']=isset($_REQUEST['cid'])?$db->clean($_REQUEST['cid']):"";//customer id
				$detail['sales_executive_id']=isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";
				$detail['shipping_address']=isset($_REQUEST['shipping_address'])?str_replace(array("\n", "\r"), ' ', $_REQUEST['shipping_address']):"";
				$detail['billing_address']=isset($_REQUEST['billing_address'])?str_replace(array("\n", "\r"), ' ', $_REQUEST['billing_address']):"";
				$detail['chalan_no']=isset($_REQUEST['chalan_no'])?$db->clean($_REQUEST['chalan_no']):"";
				$detail['po_no']=isset($_REQUEST['po_no'])?$db->clean($_REQUEST['po_no']):"";
				$detail['po_date']=isset($_REQUEST['po_date'])?$db->clean($_REQUEST['po_date']):"";
				$detail['vendor_code']=isset($_REQUEST['vendor_code'])?$db->clean($_REQUEST['vendor_code']):"";
				$detail['tendor_code']=isset($_REQUEST['tendor_code'])?$db->clean($_REQUEST['tendor_code']):"";
				$detail['transport_through']=isset($_REQUEST['transport_through'])?$db->clean($_REQUEST['transport_through']):"";
				$detail['transport_name']=isset($_REQUEST['transport_name'])?$db->clean($_REQUEST['transport_name']):"";
				$detail['packing_charge']=isset($_REQUEST['packing_charge'])?$db->clean($_REQUEST['packing_charge']):"";
				$detail['transport_charge']=isset($_REQUEST['transport_charge'])?$db->clean($_REQUEST['transport_charge']):"";
				$detail['terms_comdition'] = isset($_REQUEST['terms_comdition'])?$_REQUEST['terms_comdition']:"";
				$detail['terms_condition_id'] = isset($_REQUEST['terms_condition_id'])?$_REQUEST['terms_condition_id']:"";
				$detail['faithfully'] = isset($_REQUEST['faithfully'])?$_REQUEST['faithfully']:"";
				$detail['update_entry_flag'] = isset($_REQUEST['update_entry_flag'])?$_REQUEST['update_entry_flag']:"5";
				$detail['booking_place'] = isset($_REQUEST['booking_place'])?$_REQUEST['booking_place']:"5";
				$detail['booking_pincode'] = isset($_REQUEST['booking_pincode'])?$_REQUEST['booking_pincode']:"5";
				$detail['remark'] = isset($_REQUEST['remark'])?$_REQUEST['remark']:"";
				$detail['gst']=isset($_REQUEST['gst'])?$db->clean($_REQUEST['gst']):0;
				$products=(isset($_REQUEST['products']) && $_REQUEST['products']!="")?json_decode($_REQUEST['products'],true):array();
				$ack=$objOrder->UpdateToCart($detail,$products);
				$db->printJSON($ack);
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Internal Error!!Some Parameter Missing!","developer_msg"=>"Internal Error!!Some Parameter Missing!");
				$db->printJSON($ack);
			}
		}

		else if($service=='get_document_list' || $service==132)
		{
			$document = array();
			$class_id = $_REQUEST['class_id'];
			$sales_id = $_REQUEST['sales_id'];
			$document_type = $_REQUEST['document_type'];

			$documentListWhere = "class_id='".$class_id."' AND document_type='".$document_type."' AND isDelete=0";

			if (isset($_REQUEST['sales_type'] )) {
				//$documentListWhere .= " AND sales_type IN('".$_REQUEST['sales_type']."') "; 
				$documentListWhere .= " AND FIND_IN_SET('".$_REQUEST['sales_type']."', sales_type) "; 

			}

			if (isset($_REQUEST['customer_type'])) {
				//$documentListWhere .= " AND customer_type IN('".$_REQUEST['customer_type']."') ";
				$documentListWhere .= " AND FIND_IN_SET(".$_REQUEST['customer_type'].", customer_type) "; 

			}

			/*visiting_card*/
				$visiting_card = $db->rp_getValue("sales_executive","visiting_card_file_path","id='".$sales_id."' AND isDelete=0",0);
				if($visiting_card!="")
				{
				    $ext = pathinfo($visiting_card, PATHINFO_EXTENSION);
	                $visiting_card = SITEURL.GST_VISITING_DETAIL.$visiting_card;
				    if($ext=="pdf" || $ext=="PDF")
				    {
				        $title = "visiting_card.pdf";
				    }
				    else
				    {
				        $title = "visiting_card.jpge";
				    }
				}
				else
				{
				    $visiting_card = "";
				    $title = "";
	    		}
	    	/*visiting_card*/

	    	/*price list*/
    			$pricelist_detail = $db->rp_getValue("dealer_distributor_network","price_list_path","isDelete=0 AND id=1",0);
    			if($pricelist_detail!="")
    			{
    			    $ext = pathinfo($pricelist_detail, PATHINFO_EXTENSION);
                    $price_list = SITEURL.GST_VISITING_DETAIL.$pricelist_detail;
    			    if($ext=="pdf" || $ext=="PDF")
    			    {
    			        $price_list_name = "price_list_name.pdf";
    			    }
    			    else
    			    {
    			        $price_list_name = "price_list_name.jpge";
    			    }
    			}
    			else
    			{
    			    $price_list = "";
    			    $price_list_name = "";
	    		}
	    	/*price list*/

	    	/*bank detail*/
				$bank_detail_path_detail = $db->rp_getValue("dealer_distributor_network","bank_detail_path","isDelete=0",0);
    			if($bank_detail_path_detail!="")
    			{
    			    $ext = pathinfo($bank_detail_path_detail, PATHINFO_EXTENSION);
                    $bank_detail = SITEURL.GST_VISITING_DETAIL.$bank_detail_path_detail;
    			    if($ext=="pdf" || $ext=="PDF")
    			    {
    			        $bank_detail_name = "bank_detail_name.pdf";
    			    }
    			    else
    			    {
    			        $bank_detail_name = "bank_detail_name.jpge";
    			    }
    			}
    			else
    			{
    			    $bank_detail = "";
    			    $bank_detail_name = "";
	    		}
			/*bank detail*/

			if($class_id!="" && $document_type!="")
			{
				$document_list = $db->rp_getData("document_list","*",$documentListWhere,"",0);
				if($document_list)
				{
					while($document_list_r = mysqli_fetch_assoc($document_list))
					{
						$document_list_r['class_name'] = $db->rp_getValue("class","name","id='".$document_list_r['class_id']."'");
						$document_list_r['document_type_name'] = $db->rp_getValue("document_type","name","id='".$document_list_r['document_type']."'");
						$document_list_r['image_path'] = SITEURL.GST_VISITING_DETAIL.$document_list_r['image_path'];
						if($document_list_r['image_path']=="")
						{
							$document_list_r['image_path'] = "";
						}

						$document_list_r['file_name'] = str_replace(' ', '',$document_list_r['document_name'].".".pathinfo($document_list_r['image_path'], PATHINFO_EXTENSION));

						$document[] = $document_list_r;
					}
				}

				
				if(!empty($document_list))
				{
					$ack=array("ack"=>1,"developer_msg"=>"Data Get successfully!!","ack_msg"=>"Data Get successfully!!","download_path"=>DOWNLOAD_PATH,"catalog_title"=>CATALOG_TITLE,"visiting_card_download_path"=>$visiting_card,"visiting_card_title"=>$title,"price_list"=>$price_list,"price_list_name"=>$price_list_name,"bank_title"=>$bank_detail_name,"bank_detail_file_path"=>$bank_detail,"result"=>$document);
					$db->printJSON($ack);
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"No Document List Found.","developer_msg"=>"No Document List Found.","download_path"=>DOWNLOAD_PATH,"catalog_title"=>CATALOG_TITLE,"visiting_card_download_path"=>$visiting_card,"visiting_card_title"=>$title,"price_list"=>$price_list,"price_list_name"=>$price_list_name,"bank_title"=>$bank_detail_name,"bank_detail_file_path"=>$bank_detail);
					$db->printJSON($ack);
				}
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"State Required","developer_msg"=>"State Required","download_path"=>DOWNLOAD_PATH,"catalog_title"=>CATALOG_TITLE,"visiting_card_download_path"=>$visiting_card,"visiting_card_title"=>$title,"price_list"=>$price_list,"price_list_name"=>$price_list_name,"bank_title"=>$bank_detail_name,"bank_detail_file_path"=>$bank_detail);
				$db->printJSON($ack);
			}
		}

		
		else if($service=="get_banner_list" || $service==133)
		{
			$customer_id	= isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
			$change_password_r=$db->rp_getValue("executive","update_password_flag","isDelete=0 AND id='".$customer_id."'");
			if($change_password_r==0)
			{
				$ctable 	= "promotion";
				$ctable1 	= "Banner";
	             
	            $isActive = $db->rp_getValue("executive","isActive","id='".$customer_id."'",0);
				$isDelete = $db->rp_getValue("executive","isDelete","id='".$customer_id."'",0);
				
				if($isDelete==0 )
				{
				    if($isActive==1)
					{
						/*price list*/
			    			$pricelist_detail = $db->rp_getValue("dealer_distributor_network","price_list_path","isDelete=0",0);
			    			if($pricelist_detail!="")
			    			{
			    			    $ext = pathinfo($pricelist_detail, PATHINFO_EXTENSION);
			                    $price_list = SITEURL.GST_VISITING_DETAIL.$pricelist_detail;
			    			    if($ext=="pdf" || $ext=="PDF")
			    			    {
			    			        $price_list_name = "price_list_name.pdf";
			    			    }
			    			    else
			    			    {
			    			        $price_list_name = "price_list_name.jpge";
			    			    }
			    			}
			    			else
			    			{
			    			    $price_list = "";
			    			    $price_list_name = "";
			    			}
	    				/*price list*/
	    				$ctable_r = $db->rp_getData($ctable,"*","promo_type=1 AND isDelete=0","display_order",0);
	    				$executive_r = $db->rp_getData("executive","dealer_order_view_flag,customer_update_flag,outlets_order_view_flag,customer_insert_flag,order_view_flag,order_insert_flag,order_update_flag,order_approve_flag,type_of_company","isDelete=0 AND id='".$customer_id."'","",0);
	    				$customer_detail=array();
	    				while($executive_d = mysqli_fetch_array($executive_r)){
	    					$customer_detail[]=$executive_d;
	    				}
		    			$banners=array();
		    			if(mysqli_num_rows($ctable_r)>0){
		    				while($ctable_d = mysqli_fetch_array($ctable_r)){
		    					array_push($banners,SITEURL.BANNER.$ctable_d['image_path']);
		    					
		    				}
		    				$ack=array("ack"=>1,"result"=>$banners,"price_list"=>$price_list,"price_list_name"=>$price_list_name,"customer_version"=>CUSTOMER_VERSION_CODE,"update_msg"=>CUSTOMER_VERSION_MSG,"customer_ios_version"=>CUSTOMER_IOS_VERSION_CODE,"update_ios_msg"=>CUSTOMER_IOS_VERSION_MSG,"customer_detail"=>$customer_detail);
		    				echo json_encode($ack);
		    			}
		    			else
		    			{
		    				$ack=array("ack"=>0,"ack_msg"=>"No banner found!!","price_list"=>$price_list,"price_list_name"=>$price_list_name,"customer_version"=>CUSTOMER_VERSION_CODE,"update_msg"=>CUSTOMER_VERSION_MSG,"customer_ios_version"=>CUSTOMER_IOS_VERSION_CODE,"update_ios_msg"=>CUSTOMER_IOS_VERSION_MSG,"customer_detail"=>$customer_detail);
		    				echo json_encode($ack);
		    			}
					}
					else
					{
				        $ack=array("ack"=>2,"ack_msg"=>"User Is Deactive.Please Check!!","developer_msg"=>"User Is Deactive.Please Check","customer_version"=>CUSTOMER_VERSION_CODE,"update_msg"=>CUSTOMER_VERSION_MSG,"customer_ios_version"=>CUSTOMER_IOS_VERSION_CODE,"update_ios_msg"=>CUSTOMER_IOS_VERSION_MSG);
				        echo json_encode($ack);	    
					}
				}
				else
				{
				   $ack=array("ack"=>2,"ack_msg"=>"User Is Delete.Please Check!!","developer_msg"=>"User Is Delete.Please Check");
				   echo json_encode($ack); 
				}
			}
			else
			{
				 $ack=array("ack"=>3,"ack_msg"=>"User Change Password.Please Check!!","developer_msg"=>"User Change Passeword.Please Check");
				   echo json_encode($ack);
			}
        }
        else if($service=='customer_change_password' || $service==134)
		{
			if(isset($_REQUEST['customer_id']) && isset($_REQUEST['password']) && isset($_REQUEST['new_password']))
			{
				$customer_id 		= isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
				$i=$db->rp_getTotalRecord("executive","id='".$customer_id."'",0);					
				if($i)
				{
					$newPassword		= md5(trim($_REQUEST['new_password']))?$db->clean(md5(trim($_REQUEST['new_password']))):"";
					$password 		= isset($_REQUEST['password'])?$db->clean($_REQUEST['password']):"";
					$check=$db->rp_getValue("executive","COUNT(*)","id='".$customer_id."' AND password='".md5($password)."'",0);
					if($check>0)
					{	
						if($db->aj_updateUserPasswordCustomer($customer_id,$newPassword,$password))
						{
							$ack=array( "ack"=>1,"ack_msg"=>"Successfully Updated Your Password!!",
								"developer_msg"=>"You got it!!","result"=>array($check));
							$db->printJSON($ack);
						}
						else
						{
							$ack=array( "ack"=>0,"ack_msg"=>"Password Updation Fail!!","developer_msg"=>"please pass correct password!!");
							$db->printJSON($ack);
						}
					}
					else
					{
						$ack=array( "ack"=>0,"ack_msg"=>"Your Old Password Is Incorrect please Enter Correct Password!!","developer_msg"=>"please Enter Correct Password password!!");
						$db->printJSON($ack);
					}
				}
				else
				{
					$ack=array( "ack"=>0,"ack_msg"=>"User Not Found!!","developer_msg"=>"User Not Found!!");
					$db->printJSON($ack);
				}
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Something went wrong!!! User id not found","developer_msg"=>"User Not Found!!");
				$db->printJSON($ack);
			}
		}
		else if($service=="get_customer_profile" || $service==135)
		{
			$customer_id=isset($_REQUEST['customer_id'])?$_REQUEST['customer_id']:"";
			if($customer_id!="")
			{
				$login_r=$db->rp_getData("executive","*","id='".$customer_id."'");
				if($login_d=mysqli_fetch_assoc($login_r))
				{
					$login_d['customer_type']=$db->rp_getValue("customer_type","name","id='".$login_d['type_of_executive']."'");
					if($login_d['image_path']=="")
					{
						$login_d['image_path'] = "";
					}
					else
					{
						$login_d['image_path'] = SITEURL.SUPER_STOCKIST.$login_d['image_path'];
					}
				    $login_d['class_name']=$db->rp_getValue("class","name","id='".$login_d['class_id']."'");
				     $login_d['country_id']=$db->rp_getValue("country","id","name='". $login_d['country']."' AND isDelete=0");
					 $login_d['state_id']=$db->rp_getValue("class","id","name='". $login_d['state']."' AND isDelete=0");
					 $login_d['city_id']=$db->rp_getValue("area","id","name='". $login_d['city']."' AND isDelete=0");
						$area_r=$db->rp_getData("sales_executive_map_area","area_id","class_id='".$login_d['class_id']."' AND sales_executive_id='".$login_d['id']."' AND isDelete=0","",0);
					$area_name=array();
					if($area_r)
					{
						while($area_d=mysqli_fetch_assoc($area_r))
						{
							$area_name[]=$db->rp_getValue("area","name","id='".$area_d['area_id']."'");
						}
						$area_name=implode(",",$area_name);
					}
					else
					{
						$area_name="";
					}
					$login_d['area_name']=$area_name;
					$ack=array( "ack"=>1,"ack_msg"=>"Customer Found!!","developer_msg"=>"Customer Found!!","result"=>$login_d);
					$db->printJSON($ack);
				}
				else
				{
					$ack=array( "ack"=>0,"ack_msg"=>"No such a Customer Found!!","developer_msg"=>"No such a Customer Found!!");
					$db->printJSON($ack);
				}
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Customer Required!!","developer_msg"=>"Customer Required!!");
				$db->printJSON($ack);
			}
		}

		else if($service=='update_notification_flag_customer' || $service==136)
		{
			$notification_flag = $_REQUEST['notification_flag'];
			$customer_id = $_REQUEST['customer_id'];
			
			if($customer_id!="")
			{
			    $Update=$db->rp_update("executive",array("notification_flag"=>$notification_flag),"id='".$customer_id."'",0);
			}
			if($Update)
			{
				$ack=array("ack"=>1,"ack_msg"=>"Notification Flag Update Successfully");
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Notification Flag Update Failed");
			}
			echo json_encode($ack);
		}

		else if($service=='update_customer_profile' || $service==137)
		{
		    $detail['customer_id']	 = isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
		    $detail['company_name']	 = isset($_REQUEST['company_name'])?$db->clean($_REQUEST['company_name']):"";
			$detail['cname']	     = isset($_REQUEST['cname'])?$db->clean($_REQUEST['cname']):"";
			$detail['email']	     = isset($_REQUEST['email'])?$db->clean($_REQUEST['email']):"";
		    $detail['address']	     = isset($_REQUEST['address'])?$db->clean($_REQUEST['address']):"";
			$detail['country']	     = isset($_REQUEST['country'])?$db->clean($_REQUEST['country']):"";
			$detail['state']	     = isset($_REQUEST['state'])?$db->clean($_REQUEST['state']):"";
			$detail['city']	     	 = isset($_REQUEST['city'])?$db->clean($_REQUEST['city']):"";
			
			require_once('../include/class.executive.php');
			$inquiry1=new Executive();
			$ack=$inquiry1->UpdateExecutiveProfile($detail,$_FILES);
			if($ack['ack']==1)
			{
				$reply[]=array("ack"=>1,"ack_msg"=>"Customer successfully saved !!");		
				//$updated[]=array("server_id"=>$ack['inserted_id'],"local_id"=>$local_id,"area"=>$ack['areas']);	
				$updated[]=array("server_id"=>$ack['inserted_id'],"local_id"=>$local_id,"area"=>$ack['areas']);	
			}
			else
			{
				$error[]=$ack;							
			}
			$db->printJSON($ack);
		}
		else if($service=='get_complain_category' || $service==138)
        {
			$ack=$objLeaveRequest->getComplainCategory();
            $db->printJSON($ack);
        }
        else if($service=='Add_request' || $service==139)
        {
			include('../include/class.request.php');	
			$objRequest=new Request();

			$detail['user_id'] 			= isset($_REQUEST['user_id'])?$db->clean($_REQUEST['user_id']):"";
			$detail['customer_id'] 		= isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
			$detail['dealer_id'] 		= isset($_REQUEST['dealer_id'])?$db->clean($_REQUEST['dealer_id']):"";
			$detail['latitude'] 		= isset($_REQUEST['latitude'])?$db->clean($_REQUEST['latitude']):"";
			$detail['longitude'] 		= isset($_REQUEST['longitude'])?$db->clean($_REQUEST['longitude']):"";
			$detail['remark'] 			= isset($_REQUEST['remark'])?$db->clean($_REQUEST['remark']):"";
			$detail['app_address'] 		= isset($_REQUEST['app_address'])?$db->clean($_REQUEST['app_address']):"";
			$detail['title'] 			= isset($_REQUEST['title'])?$db->clean($_REQUEST['title']):"";
			$detail['class_id'] 		= isset($_REQUEST['class_id'])?$db->clean($_REQUEST['class_id']):"";
			$detail['area_id'] 			= isset($_REQUEST['area_id'])?$db->clean($_REQUEST['area_id']):"";
			$detail['created_date'] 	= date("Y-m-d");
			$detail['entry_flag'] 		= isset($_REQUEST['entry_flag'])?$db->clean($_REQUEST['entry_flag']):"";
			$detail['request_type'] 	= isset($_REQUEST['request_type'])?$db->clean($_REQUEST['request_type']):"";
			$detail['request_cat_id'] 	= isset($_REQUEST['request_cat_id'])?$db->clean($_REQUEST['request_cat_id']):"";
			$detail['request_subcat_id'] = isset($_REQUEST['request_subcat_id'])?$db->clean($_REQUEST['request_subcat_id']):"";
			$detail['request_created_by'] = isset($_REQUEST['request_created_by'])?$db->clean($_REQUEST['request_created_by']):"";
			$detail['request_assign_to']  = isset($_REQUEST['request_assign_to'])?$db->clean($_REQUEST['request_assign_to']):"";

			$reply=$objRequest->AddRequest($detail,$_FILES);
			if($reply['ack']==1)
			{
				echo json_encode($reply);
			}
			else
			{
				echo json_encode($reply);
			}
        }

        else if($service=='get_request' || $service==140)
		{
			$system=new System();
			$limit=$system->getLimit();
			$request = array();

			$user_id = isset($_REQUEST['user_id'])?$db->clean($_REQUEST['user_id']):"";//sales
			$request_no = isset($_REQUEST['request_no'])?$db->clean($_REQUEST['request_no']):"";//sales
			$dealer_id = isset($_REQUEST['dealer_id'])?$db->clean($_REQUEST['dealer_id']):"";//sales
			$customer_id = isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
			$status = isset($_REQUEST['status'])?$db->clean($_REQUEST['status']):"";
			$class_id = isset($_REQUEST['class_id'])?$db->clean($_REQUEST['class_id']):"";
			$area_id = isset($_REQUEST['area_id'])?$db->clean($_REQUEST['area_id']):"";
			/*if($user_id)
			{*/
				
				if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
				{
				  $where .= " DATE(created_date) <= '".date("Y-m-d",strtotime($_REQUEST['ToDate']))."' AND";
				}

				if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
				{
					$where .= " DATE(created_date) >= '".date("Y-m-d",strtotime($_REQUEST['FromDate']))."' AND ";
				}

				if($status!=""){
					$where .="status='".$status."' AND ";
				}

				if($customer_id!=""){
					$where .="customer_id='".$customer_id."' AND ";
				}	

				if($dealer_id!=""){
					$where .="dealer_id='".$dealer_id."' AND ";
				}

				if($class_id!=""){
					$where .="class_id='".$class_id."' AND ";
				}

				if($area_id!=""){
					$where .="area_id='".$area_id."' AND ";
				}

				if($request_no!="")
				{
					$where.= "request_no LIKE '%".$request_no."%' AND isDelete=0 AND isActive=1";
				}
				else
				{
					$where .="user_id='".$user_id."' AND isDelete=0 AND isActive=1";
				}
				
				$request_data = $db->rp_getData("request","*",$where,"id DESC",0,$limit);
				if($request_data)
				{
					$request_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
					$status_type=array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done","3"=>"Cancel");
					while($request_d = mysqli_fetch_assoc($request_data))
					{
						$request_d['request_type_slug'] = $request_type_array[$request_d['request_type']];

						$request_d['request_cat_name'] = $db->rp_getValue("complain_category","name","id='".$request_d['request_cat_id']."'");

						$request_d['request_subcat_name'] = $db->rp_getValue("complain_sub_category","name","id='".$request_d['request_subcat_id']."'");
						
						$request_d['customer_name'] = $db->rp_getValue("executive","cname","id='".$request_d['customer_id']."'",0);
						$request_d['dealer_name'] = $db->rp_getValue("executive","cname","id='".$request_d['dealer_id']."'",0);
						$request_d['status_slug'] = $status_type[$request_d['status']];
						$request_d['color_code'] = $db->complain_status_color[$request_d['status_slug']];
						$img = explode(",", $request_d['image_path']);
						$imgpath = array();
						for ($i=0; $i < sizeof($img); $i++)
						{ 
							$imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$request_d['id']."' AND id='".$img[$i]."'");
						}
						$request_d['image_path'] = ($request_d['image_path']!= "")?$imgpath:"";
						$request_d['created_date'] = date('d F Y h:i A',strtotime($request_d['created_date']));

						$customer_type_get = $db->rp_getValue("executive","type_of_executive","id='".$request_d['customer_id']."'",0);
						$request_d['customer_type'] = $db->rp_getValue("customer_type","name","id='".$customer_type_get."'",0);

						$request[] = $request_d;
					}


					/*Get Complain Status*/
					$complain_status = array();
					$RequestData = $db->rp_getData('request',"DISTINCT(status)","user_id='".$user_id."' AND isDelete=0","id DESC",0);
					$complain_status_array=array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done");
					/*$complain_status_array=array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested");*/

					$request_status_key = array("0","1","2","-1","-2");
					while($Request_d = mysqli_fetch_assoc($RequestData))
					{
						if($user_id!="")
						{
							$Request_d['count']=$db->rp_getTotalRecord("request","user_id ='".$user_id."' AND status='".$Request_d['status']."' AND isDelete=0");
						}
						else
						{
							$Request_d['count']=$db->rp_getTotalRecord("request","customer_id ='".$customer_id."' AND status='".$Request_d['status']."' AND isDelete=0");
						}

						if (($key_complain = array_search($Request_d['status'], $request_status_key)) !== false) {
						    unset($request_status_key[$key_complain]);
						}

						$Request_d['status_slug'] = $complain_status_array[$Request_d['status']];
						$Request_d['status'] = $Request_d['status'];

						$Request_d['color_code'] = $db->complain_status_color[$Request_d['status_slug']];

						if($Request_d['color_code']=="")
						{
							$Request_d['color_code'] = "";
						}

						if($Request_d['status_slug']=="")
						{
							$Request_d['status_slug'] = "";
						}
						$request_status[]=$Request_d;
					}
					foreach ($request_status_key as $key => $remainval_complain) {
						$Request_d['count'] = 0;
						$Request_d['status'] = $remainval_complain;
						$Request_d['status_slug'] = $complain_status_array[$remainval_complain];
						$Request_d['color_code'] = $db->complain_status_color[$complain_status_array[$remainval_complain]];
						$request_status[]=$Request_d;
					}
					/*Get Complain Status*/
				}

				if($request_data)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Request Detail Get successfully!!","ack_msg"=>"Request Detail Get successfully!!","result"=>$request,"complain_status"=>$request_status);
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"No Complain Found!!","ack_msg"=>"No Request Found!!");
				}
				echo json_encode($reply);
			/*}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Complain Detail Not Get!!","ack_msg"=>"Complain Detail Not Get!!");
				echo json_encode($reply);
			}*/
		}

		if($service=='product_get' || $service==141)
		{
			$product = array();
			$product_name = $_REQUEST['product_name'];
			$tcids	= isset($_REQUEST['tcids'])?$db->clean($_REQUEST['tcids']):"";

			/*for get Commasepretd Value*/
			$proids_array = $db->getCommaSepretedData("product_weight_price","*",$product_name,"catno");
			/*for get Commasepretd Value*/
			if($proids_array!="")
			{
				$Where = "(name LIKE '%".$product_name."%' OR id IN (".$proids_array.")) AND isDelete=0";
			}
			else
			{
				$Where = "name LIKE '%".$product_name."%' AND isDelete=0";
			}
			if($tcids!=""){
				$Where.=" AND tcid IN (".$tcids.")";
			}
			$product_r = $db->rp_getData("product","id,name",$Where,"id DESC",0);
			if($product_r)
			{
				while($product_d = mysqli_fetch_assoc($product_r))
				{
					$product_weight = $db->rp_getData("product_weight_price","weight_id,catno","product_id='".$product_d['id']."' AND isDelete=0");
					while($product_weight_d = mysqli_fetch_assoc($product_weight))
					{
						$product_d['weight_id']=$product_weight_d['weight_id'];
						$product_d['product_name'] = $product_d['name'] ."-". $product_weight_d['catno'];
					}
					$product[] = $product_d;
				}
			}

			if(!empty($product))
			{
				$reply=array("ack"=>1,"developer_msg"=>"Product Get successfully!!","ack_msg"=>"Product Get successfully!!","result"=>$product);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Product Get!!","ack_msg"=>"Product Not Get!!");
			}
			echo json_encode($reply);
		}

		if($service=='get_followup_status' || $service==178)
		{
			//$status=array("4"=>"Hot","5"=>"Cold","6"=>"Warm");
			$status=array("1"=>"In Followup");
         	$Data=array(); 
         	foreach($status as $SK=>$SV)
         	{
         		$a=array(); 
         		$a['status_id']=$SK;
         		$a['name']=$SV;
         		$Data[]=$a;
         	}
         	$ack=array( "ack"=>1,"ack_msg"=>"Status Get Successfully!!","developer_msg"=>"Status Get Successfully!!","result"=>$Data);
			$db->printJSON($ack); 
		}
		else if($service=='get_complain_sub_category' || $service==186)
        {
        	$cid = isset($_REQUEST['cid'])?$db->clean($_REQUEST['cid']):"";//sales
        	if($cid!=""){
        		$ack=$objLeaveRequest->getComplainSubcategory($cid);
        	}else{
				$ack=array("ack"=>0,"developer_msg"=>"Cid Required","ack_msg"=>"Calegory Id Required");
        	}
			 $db->printJSON($ack);

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
?>