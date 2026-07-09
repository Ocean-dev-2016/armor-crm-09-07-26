<?php 
// Connect to Database
include('connect.php');
require_once('../include/notification.class.php');
require_once('../include/product.class.php');
include('../include/class.executive.php');
include('../include/class.sales_executive.php');
include('../include/employee.class.php');
include('../include/orders.class.php');
include('../include/dispatch.class.php');


// You have DB object now use it as $db->
//First Check for API key if API key is valid then proceed other stop excute script
// Which service requested is given in params named "service"
// Response Structure given below.
/* $ack=array(
			"ack"=>1/0\2,(1= success,0=failure,2=get otp)
			"ack_msg"=>"Message will printed on View",("This message will be shown to user so make it user readable not for developers")
			"developer_msg"=>"Message for debugging",("This message will be shown to developer on debug mode")
			"extra"=>array("requested_params"=>$_REQUEST,"other"=>array()),"Extra field contains requested params array which returns all the requested params and other array will contains extra params which you want to show on debug mode"
		)
	echo json_encode($ack);
*/
if($is_valid_api_key)
{	
	if($is_valid_service)
	{
		$objSalesExecutive=new SalesExecutive();
		$system=new System();
		$objProduct=new Product();
		$objEmp=new Employee();
		$objOrder=new Order();
		$objDispatch=new Dispatch();

//#login For Sales Officer---------------------------------// 
		if($service=='get_country' || $service==27)
        {
			$detail=array();
            $ack=$system->getCountry();
            $db->printJSON($ack);
            
        }
		else if($service=='get_top_country' || $service==57)
        {
				//echo "sadadsad";  exit;
				$detail=array();
                $ack=$system->getTopCategory(array("id","name","image_path","isDelete","isActive"));
                $db->printJSON($ack);
            
        }
		else if($service=='get_category' || $service==58)
        {
				//echo "sadadsad";  exit;
				$detail=array();
                $ack=$system->getCategory(array("id","tcid","name","image_path","isDelete","isActive"));
                $db->printJSON($ack);
            
        }
        else if($service=='get_state' || $service==28)
        {
			$detail=array();
			$ack1=$system->getAllStateDetail(array("id","name","country_id","isDelete"));//id
			// print_r($ack1);exit;
            $db->printJSON($ack1);

        }
		else if($service=='get_city' || $service==38)
        {
        	$detail=array();
			$ack=$system->getAllCityDetail(array("id","name","country_id","state_id","pincode"));//id
            $db->printJSON($ack);
        }
		 else if($service=='get_class' || $service==35)
        {

           
				$detail=array();
				$ack=$system->getAllClassDetail(array("id","name","slug","isDelete","isActive"));//id
                $db->printJSON($ack);

        }
		else if($service=="get_banner" || $service==60)
		{
			$sales_id	= isset($_REQUEST['sales_id'])?$db->clean($_REQUEST['sales_id']):"";
			$ctable 	= "promotion";
			$ctable1 	= "Banner";

            
            $isActive = $db->rp_getValue("sales_executive","isActive","id='".$sales_id."'",0);
			$isDelete = $db->rp_getValue("sales_executive","isDelete","id='".$sales_id."'",0);
			
			if($isDelete==0 )
			{
			    if($isActive==1)
				{
				    /*get rights*/
    				$rights = array();
    				$sales_rights = $db->rp_getData("sales_executive","executive_in_min,executive_in_max,executive_out,super_stokist_order_view_flag,super_stokist_order_insert_flag,super_stokist_order_update_flag,super_stokist_order_delete_flag,outlets_order_view_flag,outlets_order_insert_flag,outlets_order_update_flag,outlets_order_delete_flag,dealer_order_view_flag,dealer_order_insert_flag,dealer_order_update_flag,dealer_order_delete_flag,project_order_view_flag,project_order_insert_flag,project_order_update_flag,project_order_delete_flag,oem_order_view_flag,oem_order_insert_flag,oem_order_update_flag,oem_order_delete_flag,survey_customer_view_flag,survey_customer_insert_flag,survey_customer_update_flag,survey_customer_delete_flag,customer_view_flag,customer_insert_flag,customer_update_flag,customer_delete_flag,followup_view_flag,followup_insert_flag,followup_update_flag,followup_delete_flag,create_order_view_flag,create_order_insert_flag,create_order_update_flag,create_order_delete_flag,order_history_view_flag,order_history_insert_flag,order_history_update_flag,order_history_delete_flag,complain_view_flag,complain_insert_flag,complain_update_flag,complain_delete_flag,customer_meeting_view_flag,customer_meeting_insert_flag,customer_meeting_update_flag,customer_meeting_delete_flag,near_by_me_view_flag,change_root_view_flag,change_root_insert_flag,change_root_update_flag,change_root_delete_flag,expense_view_flag,expense_insert_flag,expense_update_flag,expense_delete_flag,leave_view_flag,leave_insert_flag,leave_update_flag,leave_delete_flag,area_view_flag,area_insert_flag,area_update_flag,area_delete_flag,visit_view_flag,visit_insert_flag,visit_update_flag,visit_delete_flag,price_list_view_flag,bank_detail_view_flag,scheme_view_flag,discount_dealer_view_flag,discount_distributor_view_flag,gst_view_flag,visit_card_view_flag,traveling_view_flag,attendance_insert_flag,request_view_flag,request_insert_flag,request_update_flag,request_delete_flag,customer_leads_view_flag,customer_leads_insert_flag,customer_leads_update_flag,customer_leads_delete_flag","id='".$sales_id."' AND isDelete=0","",0);
    
    				while($sales_rights_d = mysqli_fetch_assoc($sales_rights))
    				{
    					$sales_rights_d['class_id'] = $db->rp_getValue("promotion","class_id","isDelete=0");
    					$sales_rights_d['area_id'] = $db->rp_getValue("promotion","area_id","isDelete=0");
    					$sales_rights_d['class_name'] = $db->rp_getValue("class","name","id='".$sales_rights_d['class_id']."' AND isDelete=0");
    					$area_id  = explode(",",$sales_rights_d['area_id']);
    					$newArray = array();
    					foreach ($area_id as $area) {
    			 			$newArray[] = $db->rp_getValue("area","name","id='".$area."'",0);
    			 		}
    			 		$sales_rights_d['area_name'] = implode(",", $newArray);
    			 		$sales_rights_d['tracking_local_time'] = TRACKING_TIME_LOCAL_API;
    					$sales_rights_d['tracking_live_time'] = TRACKING_TIME_LIVE_API;
    					$sales_rights_d['distance'] = DISTANCE_API;
    			 		$rights[] = $sales_rights_d;
    				}
    				/*get rights*/
    
    				/*visiting_card*/
	    			$visiting_card = $db->rp_getValue("sales_executive","visiting_card_file_path","id='".$sales_id."' AND isDelete=0");
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

    				/*Gst detail*/
	    			$gst_detail = $db->rp_getValue("dealer_distributor_network","file_path","isDelete=0",0);
	    			if($gst_detail!="")
	    			{
	    			    $ext = pathinfo($gst_detail, PATHINFO_EXTENSION);
	                    $gst_detail_file_path = SITEURL.GST_VISITING_DETAIL.$gst_detail;
	    			    if($ext=="pdf" || $ext=="PDF")
	    			    {
	    			        $gst_title = "gst_detail.pdf";
	    			    }
	    			    else
	    			    {
	    			        $gst_title = "gst_detail.jpge";
	    			    }
	    			}
	    			else
	    			{
	    			    $gst_detail_file_path = "";
	    			    $gst_title = "";
	    			}
    				/*Gst detail*/

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

    				/*scheme detail*/
    				$scheme_path = $db->rp_getValue("dealer_distributor_network","scheme_path","isDelete=0",0);
	    			if($scheme_path!="")
	    			{
	    			    $ext = pathinfo($scheme_path, PATHINFO_EXTENSION);
	                    $scheme_path = SITEURL.GST_VISITING_DETAIL.$scheme_path;
	    			    if($ext=="pdf" || $ext=="PDF")
	    			    {
	    			        $scheme_name = "scheme_name.pdf";
	    			    }
	    			    else
	    			    {
	    			        $scheme_name = "scheme_name.jpge";
	    			    }
	    			}
	    			else
	    			{
	    			    $scheme_path = "";
	    			    $scheme_name = "";
	    			}
    				/*scheme detail*/

    				/*dealer_discount_path detail*/
    				$dealer_discount_path = $db->rp_getValue("dealer_distributor_network","dealer_discount_path","isDelete=0",0);
	    			if($dealer_discount_path!="")
	    			{
	    			    $ext = pathinfo($dealer_discount_path, PATHINFO_EXTENSION);
	    			    $dealer_discount_path = SITEURL.GST_VISITING_DETAIL.$dealer_discount_path;
	    			    if($ext=="pdf" || $ext=="PDF")
	    			    {
	    			        $dealer_discount_name = "dealer_discount_name.pdf";
	    			    }
	    			    else
	    			    {
	    			        $dealer_discount_name = "dealer_discount_name.jpge";
	    			    }
	    			}
	    			else
	    			{
	    			    $dealer_discount_path = "";
	    			    $dealer_discount_name = "";
	    			}
    				/*dealer_discount_path detail*/

    				/*distributor_discount_path detail*/
    				$distributor_discount_path = $db->rp_getValue("dealer_distributor_network","distributor_discount_path","isDelete=0",0);
	    			if($dealer_discount_path!="")
	    			{
	    			    $ext = pathinfo($distributor_discount_path, PATHINFO_EXTENSION);
	                    $distributor_discount_path = SITEURL.GST_VISITING_DETAIL.$distributor_discount_path;
	    			    if($ext=="pdf" || $ext=="PDF")
	    			    {
	    			        $distributor_discount_name = "distributor_discount_name.pdf";
	    			    }
	    			    else
	    			    {
	    			        $distributor_discount_name = "distributor_discount_name.jpge";
	    			    }
	    			}
	    			else
	    			{
	    			    $distributor_discount_path = "";
	    			    $distributor_discount_name = "";
	    			}
    				/*distributor_discount_path detail*/
    				
    				$ctable_r = $db->rp_getData($ctable,"*","promo_type=1 AND isDelete=0","display_order",0);
    				$banners=array();
    				if(mysqli_num_rows($ctable_r)>0){
    				while($ctable_d = mysqli_fetch_array($ctable_r)){
    					array_push($banners,SITEURL.BANNER.$ctable_d['image_path']);
    					
    				}
    				$ack=array("ack"=>1,"result"=>$banners,"download_path"=>DOWNLOAD_PATH,"catalog_title"=>CATALOG_TITLE,"visiting_card_download_path"=>$visiting_card,"visiting_card_title"=>$title,"price_list"=>$price_list,"price_list_name"=>$price_list_name,"gst_title"=>$gst_title,"gst_detail_file_path"=>$gst_detail_file_path,"bank_title"=>$bank_detail_name,"bank_detail_file_path"=>$bank_detail,"scheme_title"=>$scheme_name,"scheme_detail_file_path"=>$scheme_path,"dealer_discount_title"=>$dealer_discount_name,"dealer_discount_file_path"=>$dealer_discount_path,"distributor_discount_title"=>$distributor_discount_name,"distributor_discount_file_path"=>$distributor_discount_path,"rights"=>$rights);
    				echo json_encode($ack);
    			}
    			else
    			{
    				$ack=array("ack"=>0,"ack_msg"=>"No banner found!!","download_path"=>DOWNLOAD_PATH,"catalog_title"=>CATALOG_TITLE,"visiting_card_download_path"=>$visiting_card,"visiting_card_title"=>$title,"price_list"=>$price_list,"price_list_name"=>$price_list_name,"gst_title"=>$gst_title,"gst_detail_file_path"=>$gst_detail_file_path,"bank_title"=>$bank_detail_name,"bank_detail_file_path"=>$bank_detail,"scheme_title"=>$scheme_name,"scheme_detail_file_path"=>$scheme_path,"dealer_discount_title"=>$dealer_discount_name,"dealer_discount_file_path"=>$dealer_discount_path,"distributor_discount_title"=>$distributor_discount_name,"distributor_discount_file_path"=>$distributor_discount_path);
    				echo json_encode($ack);
    			}
				}
				else
				{
			        $ack=array("ack"=>2,"ack_msg"=>"User Is Deactive.Please Check!!","developer_msg"=>"User Is Deactive.Please Check");
			        echo json_encode($ack);	    
				}
			}
			else
			{
			   $ack=array("ack"=>2,"ack_msg"=>"User Is Delete.Please Check!!","developer_msg"=>"User Is Delete.Please Check");
			   echo json_encode($ack); 
			}
                
			
		}
		
		
		else if($service=='get_area' || $service==36)
        {
        	$detail=array();
			$ack=$system->getAllAreaDetail(array("id","name","class_id","isDelete","isActive"));//id
            $db->printJSON($ack);

        }
		else if($service=='get_update_info' || $service==30)
        {

            $last_modify_date=$db->getRequestedParam("last_modify_date"); //country_id
            $ack=$system->getUpdateInfo($last_modify_date);
            $db->printJSON($ack);

        }
        else  if($service=='get_updates' || $service==31)
        {

             /*$table_code=$db->getRequestedParam("table_code"); //table_code
             $last_sync_date=$db->getRequestedParam("last_sync_date"); //country_id
             $user_id=$db->getRequestedParam("user_id"); //$user_id

            $ack=$system->getUpdates($table_code,$user_id,$last_sync_date);
            $db->printJSON($ack);*/

            $detail['uid']	= isset($_REQUEST['uid'])?$db->clean($_REQUEST['uid']):"";
			$detail['cid']	= isset($_REQUEST['cid'])?$db->clean($_REQUEST['cid']):"";
			$detail['tcid']	= isset($_REQUEST['tcid'])?$db->clean($_REQUEST['tcid']):"";
			$limit['ul']	= isset($_REQUEST['ul'])?$db->clean($_REQUEST['ul']):"";
			$limit['ll']	= isset($_REQUEST['ll'])?$db->clean($_REQUEST['ll']):"";
			$limit=$db->getLimit($limit);
			
			$ack=$objProduct->getProductPriceList($detail,$limit);//id
			$db->printJSON($ack);

        } 
		else if($service=='get_action' || $service==44)
        {
			$detail=array();
			$ack=$system->getNoOrderAction(array("id","name","isDelete","isActive"));//id
            $db->printJSON($ack);

        }
		else if($service=='get_product' || $service==51)
        {
			$detail['tcid']	= isset($_REQUEST['tcid'])?$db->clean($_REQUEST['tcid']):"";
			$detail['uid']	= isset($_REQUEST['uid'])?$db->clean($_REQUEST['uid']):"";
			$detail['cid']	= isset($_REQUEST['cid'])?$db->clean($_REQUEST['cid']):"";
			$limit['ul']	= isset($_REQUEST['ul'])?$db->clean($_REQUEST['ul']):"";
			$limit['ll']	= isset($_REQUEST['ll'])?$db->clean($_REQUEST['ll']):"";
			$system=new System();
			$limit=$system->getLimit();
			
			$ack=$objProduct->getProduct($detail,$limit);//id
			$db->printJSON($ack);

        }
		else  if($service=='update_orders' || $service==29)
        {

			$body= file_get_contents('php://input');
			$error_internal=array();
			$error=array();
			$str=isset($_REQUEST['result'])?$_REQUEST['result']:"";//str_replace('\\',"",$_REQUEST['result']);
			$result=($body!="")?(array)json_decode($body,true):array();
			$result_back=array();
			for($i=0;$i<sizeof($result['values']);$i++)
			{
				
			 $detail=$result['values'][$i]['nameValuePairs'];
			 $server_id=$detail['server_id'];
             if($server_id!=0)
			 {
			 	//echo "hi23";exit;
				 // Update Order
				  if(isset($detail['id']) && isset($detail['server_id']) && isset($detail['total_qty']) && isset($detail['total_amount']) && isset($detail['discount']) && isset($detail['discount_type']) && isset($detail['grand_total']) && isset($detail['customer_id'])&& isset($detail['product']))
				  {
							$final_total ="";
							$dealer_id					= isset($detail['dealer_id'])?$db->clean($detail['dealer_id']):"";
							$class_id					= isset($detail['class_id'])?$db->clean($detail['class_id']):"";
							$area_id					= isset($detail['area_id'])?$db->clean($detail['area_id']):"";
							$id					= isset($detail['server_id'])?$db->clean($detail['server_id']):"";
							

							$total_qty			= isset($detail['total_qty'])?$db->clean($detail['total_qty']):"";
							$total_amount		= isset($detail['total_amount'])?$db->clean($detail['total_amount']):"";
							$discount			= isset($detail['discount'])?$db->clean($detail['discount_amount']):"";
							$discount_amount			= isset($detail['discount_amount'])?$db->clean($detail['discount']):"";
							$discount_type			= isset($detail['discount_type'])?$db->clean($detail['discount_type']):"";
							$taxable_amount		= isset($detail['taxable_amount'])?$db->clean($detail['taxable_amount']):"";
							$cash_discount		= isset($detail['cash_discount'])?$db->clean($detail['cash_discount']):"";
							$cash_discount_amount		= isset($detail['cash_discount_amount'])?$db->clean($detail['cash_discount_amount']):"";
							$subtotal		= isset($detail['sub_total'])?$db->clean($detail['sub_total']):"";
							$cgst_amount= isset($detail['cgst_amount'])?$db->clean($detail['cgst_amount']):"";
							$sgst_amount= isset($detail['sgst_amount'])?$db->clean($detail['sgst_amount']):"";
							$igst_amount= isset($detail['igst_amount'])?$db->clean($detail['igst_amount']):"";
							$roundoff= isset($detail['round_off'])?$db->clean($detail['round_off']):"";
							$grand_total_rounded		= isset($detail['grand_total'])?$db->clean($detail['grand_total']):"";
							$grand_total		= isset($detail['grand_total'])?$db->clean($detail['grand_total']):"";
							
							$customer_id		= isset($detail['customer_id'])?$db->clean($detail['customer_id']):"";
							$product 	= (isset($detail['product']['values']) && $detail['product']['values']!="")?($detail['product']['values']):array();
							
							$detail_ext=$db->rp_getData("executive","*","id=".$customer_id."","",0);
							$data=mysqli_fetch_assoc($detail_ext);
								$customer_name=$data['cname'];
								$customer_type=$data['type_of_executive'];
								$contact_number=$data['phone'];
								$address=$data['address'];
								$city=$data['city'];
								$state=$data['state'];
								$country=$data['country'];
								$email=$data['email'];
							$order_date	= date("Y-m-d");
							
							$cdrow 	= array(
									"total_qty" => $total_qty,
									"total_amount"=>  $total_amount,
									"discount" => $discount,
									"discount_amount" => $discount_amount,
									"discount_type" => $discount_type,
									"taxable_amount" => $taxable_amount,
									"cash_discount" => $cash_discount,
									"cash_discount_amount" => $cash_discount_amount,
									"subtotal" => $subtotal,
									"cgst_amount" => $cgst_amount,
									"sgst_amount" => $sgst_amount,
									"igst_amount" => $igst_amount,
									"grand_total" => $grand_total,
									"roundoff" => $roundoff,
									"grand_total_rounded" => $grand_total_rounded,
									"customer_id" =>$customer_id,
									"customer_name"  => $customer_name,
									"customer_type"  => $customer_type,
									"contact_number"  => $contact_number,
									"address" => $address,
									"city" => $city,
									"email" => $email,
									"state" => $state,
									"country" =>  $country,
									"order_date" =>  $order_date,
									"local_id"=>$detail['id'],
									"sales_id" =>$detail['sales_id'],
									"order_date"=>$detail['order_date'],
									"adate"=>$detail['adate'],
									"modify_date"=>date("Y-m-d H:i:s"),
									"dealer_id"=>$dealer_id,
									"class_id" =>$class_id,
									"area_id" =>$area_id,
								);
							
							$cart_id = $db->rp_update("orders",$cdrow,"id='".$id."'");
							$adate	= date("Y-m-d H:i:s");
							//checking for updating qty is not greter than dispatched qty//
							$order_id=$id;
							$isError=false;
							foreach($product as  $p)
							{
								$p=$p['nameValuePairs'];
								$pro_id     = $p['pro_id'];
								$new_order_qty 		=  $p['pro_qty'];
								
								//CHECK ORDER UPDATE VALID OR NOT
								
								$ordered_item_info=$db->rp_getData("order_product_item","*","pro_id='".$pro_id."' AND order_id='".$order_id."'","",0);
								if($ordered_item_info)
								{
									$ordered_item_info=mysqli_fetch_assoc($ordered_item_info);
									$product_name=$ordered_item_info['pro_name'];
									$dispatched_qty=$ordered_item_info['dispatched_qty'];
									$remaining_qty=$ordered_item_info['remaining_qty'];
									$ordered_qty=$ordered_item_info['pro_qty'];
									//check new order qty > old order qty
									if($new_order_qty<$ordered_qty)
									{
										//check new order qty < dispatched qty
										if($new_order_qty<$dispatched_qty)
										{
											$isError=true;
											// ERROR YOU CAN NOT ENTER NEW ORDER QTY MORE THEN IT DISPATCHED
											$error[]=array("order_id"=>$order_id,"error_target_id"=>$pro_id,"error"=>$product_name." has dispatched qty more than your edited qty");
											
										}
									}
								}
								
							}
							if(!$isError)
							{
							$adate	= date("Y-m-d H:i:s");
							$db->rp_delete("order_product_item","order_id='".$id."'",0);
							foreach($product as  $p)
							{
								$p=$p['nameValuePairs'];
								$pro_name= addslashes($p['pro_name']);
								$pro_id= $p['pro_id'];
								$weight_id=$p['weight_id'];
								$unitprice= $p['unitprice'];
								$qty=$p['pro_qty'];
								$totalprice=$p['totalprice'];
								$discount=$p['discount'];
								$discount_amount=$p['discount_amount'];
								$taxable=$p['taxable'];
								$cgst_tax=$p['cgst_tax'];
								$cgst_amount=$p['cgst_amount'];
								$sgst_tax=$p['sgst_tax'];
								$sgst_amount=$p['sgst_amount'];
								$igst_tax=$p['igst_tax'];
								$igst_amount=$p['igst_tax'];
								$subtotal=$p['sub_total'];
								$grandtotal=$p['totalprice'];
								$final_total += $totalprice;
								$where = "pid='".$pro_id."' AND order_id='".$id."' AND isDelete=0 GROUP BY pid";
								$dispatch_r = $db->rp_getData("dispatch_map_order","SUM(qty) as dispatched_qty,pid",$where,"pid ASC ",0);
								if($dispatch_r){
								$dispatch_d=mysqli_fetch_assoc($dispatch_r);
								}
								else{
								$dispatch_d['dispatched_qty']=0;	
								}
								$remaining_qty=$qty-$dispatch_d['dispatched_qty'];
								$inner_size=$db->rp_getValue("product_weight_price","inner_size","product_id='".$pro_id."' AND weight_id='".$weight_id."'",0);
								$row = array(
									"order_id",
									"pro_id",
									"weight_id",
									"pro_name",
									"unitprice",
									"adate",
									"inner_size",
									"modify_date",
									"pro_qty",
									"remaining_qty",
									"dispatched_qty",
									"totalprice",
									"discount",
									"discount_amount",
									"taxable",
									"cgst_tax",
									"cgst_amount",
									"sgst_tax",
									"sgst_amount",
									"igst_tax",
									"igst_amount",
									"grandtotal",
								);
								$value = array(
									$id,
									$pro_id,
									$weight_id,
									$pro_name,
									$adate,
									$inner_size,
									date("Y-m-d H:i:s"),
									$unitprice,
									$qty,
									$remaining_qty,
									$dispatch_d['dispatched_qty'],
									$totalprice,
									$discount,
									$discount_amount,
									$taxable,
									$subtotal,
									$cgst_tax,
									$cgst_amount,
									$sgst_tax,
									$sgst_amount,
									$igst_tax,
									$igst_amount,
									$grandtotal,
								);
							
								$ins = $db->rp_insert("order_product_item",$value,$row,0);
								
							}
							
								$order_pro_detail=mysqli_fetch_assoc($db->rp_getData("orders","*","id='".$id."' AND isDelete=0"));
								$result_back[]=array("local_id"=>$detail['id'],"server_id"=>$order_pro_detail['id']);
							
								$order_pro_detail['product']=array();
								$where= "order_id='".$order_pro_detail['id']."'";
								$dt = $db->rp_getData("order_product_item","*",$where);
								$r = array();
								if($dt)
								{
									while($row=mysqli_fetch_assoc($dt))
									{
										$r[]= $row;
									}	
								}
								
								
								
						}
					}
					else
					{
						$error_internal[]="Row ".$i." Not Submitted due to some paramter missing";
					}
			}
			 else
			 {
			 	//
				// Insert Order 
				if(isset($detail['total_qty']) && isset($detail['total_amount']) && isset($detail['discount']) && isset($detail['discount_type']) && isset($detail['grand_total']) && isset($detail['customer_id']) && isset($detail['sales_id']) && isset($detail['product']))
					{
						

						$cdrow 	= array(
							"total_qty",
							"total_amount",
							"discount",
							"discount_type",
							"taxable",
							"cash_discount",
							"cash_discount_amount",
							"subtotal",
							"cgst_amount",
							"sgst_amount",
							"igst_amount",
							"grand_total",
							"roundoff",
							"grand_total_rounded",
							"customer_id",
							"sales_id",
							"sales_type",
							"customer_name",
							"customer_type",
							"contact_number",
							"address",
							"city",
							"email",
							"state",
							"country",
							"order_date",
							"modify_date",
							"company_name",
							
						);
						


						$final_total ="";
						
						//change by hardip

						$total_qty			= isset($detail['total_qty'])?$db->clean($detail['total_qty']):"";
						$total_amount			= isset($detail['subtotal'])?$db->clean($detail['subtotal']):"";
						$discount			= isset($detail['discount'])?$db->clean($detail['discount']):"";
						$discount_amount = isset($detail['discount_amount'])?$db->clean($detail['discount_amount']):"";
						$discount_type = isset($detail['discount_type'])?$db->clean($detail['discount_type']):"";
						$taxable_amount = isset($detail['taxable_amount'])?$db->clean($detail['taxable_amount']):"";
						$igst_amount = isset($detail['igst_amount'])?$db->clean($detail['igst_amount']):"";
						$sgst_amount = isset($detail['sgst_amount'])?$db->clean($detail['sgst_amount']):"";
						$cgst_amount = isset($detail['cgst_amount'])?$db->clean($detail['cgst_amount']):"";
						$cash_discount = isset($detail['cash_discount'])?$db->clean($detail['cash_discount']):"";
						$cash_discount_amount = isset($detail['cash_discount_amount'])?$db->clean($detail['cash_discount_amount']):"";
						$subtotal = isset($detail['sub_total'])?$db->clean($detail['sub_total']):"";
						$roundoff = isset($detail['round_off'])?$db->clean($detail['round_off']):"";
						$grand_total_rounded = isset($detail['grand_total'])?$db->clean($detail['grand_total']):"";
						$grand_total = $grand_total_rounded+$roundoff;
						// end
						
						
						$dealer_id					= isset($detail['dealer_id'])?$db->clean($detail['dealer_id']):"";
						$class_id					= isset($detail['class_id'])?$db->clean($detail['class_id']):"";
						$area_id					= isset($detail['area_id'])?$db->clean($detail['area_id']):"";
						$customer_id		= isset($detail['customer_id'])?$db->clean($detail['customer_id']):"";
						$sales_id		= isset($detail['sales_id'])?$db->clean($detail['sales_id']):"";
						
						$product 	= (isset($detail['product']['values']) && $detail['product']['values']!="")?($detail['product']['values']):array();
						$sales_type=$db->rp_getValue("sales_executive","type","id=".$sales_id."");
						$detail_sales=$db->rp_getData("executive","*","id=".$customer_id."","",0);
						if($detail_sales)
						{
							$data=mysqli_fetch_assoc($detail_sales);
							$customer_name=$data['cname'];
							$company_name=$data['company_name'];
							$customer_type=$data['type_of_executive'];
							$contact_number=$data['phone'];
							$address=$data['address'];
							$city=$data['city'];
							$state=$data['state'];
							$country=$data['country'];
							$email=$data['email'];
						}
						else
						{
							$customer_name=$data['cname'];
							$customer_type="";
							$contact_number="";
							$company_name="";
							$address="";
							$city="";
							$state="";
							$country="";
							$email="";
						}
						
						$order_date	= $detail['order_date'];
						$adate = date('Y-m-d H:i:s');
						$cdrow 	= array(
								"subtotal",
								"grand_total",
								"grand_total_rounded",
								"roundoff",
								"igst_amount",
								"sgst_amount",
								"cgst_amount",
								"taxable",
								"discount",
								"discount_amount",
								"discount_type",
								"total_qty",
								"total_amount",
								"cash_discount",
								"cash_discount_amount",
								"customer_id",
								"sales_id",
								"sales_type",
								"customer_name",
								"company_name",
								"customer_type",
								"contact_number",
								"address",
								"city",
								"email",
								"state",
								"country",
								"order_date",
								"adate",
								"local_id",
								"modify_date",
								"dealer_id",
								"class_id",
								"area_id",
							);
						$cdvalue = array(
								$subtotal,
								$grand_total,
								$grand_total_rounded,
								$roundoff,
								$igst_amount,
								$sgst_amount,
								$cgst_amount,
								$taxable_amount,
								$discount,
								$discount_amount,
								$discount_type,
								$total_qty,
								$total_amount,
								$cash_discount,
								$cash_discount_amount,
								$customer_id,
								$sales_id,
								$sales_type,
								$customer_name,
								$company_name,
								$customer_type,
								$contact_number,
								$db->clean($address),
								$city,
								$email,
								$state,
								$country,
								$order_date,
								$adate,
								$detail['id'],
								date("Y-m-d H:i:s"),
								$dealer_id,
								$class_id,
								$area_id,
							);
						$cart_id = $db->rp_insert("orders",$cdvalue,$cdrow,0);
						$row = array("order_no"=>OUTLETS_ORDER_NO.str_pad($cart_id, 3, '0', STR_PAD_LEFT));
						$update_order_no = $db->rp_update("orders",$row,"id='".$cart_id."'");
						
						
						foreach($product as  $p)
						{
							$p=$p['nameValuePairs'];
							//product=[{"name":"product1","id":"33","price":"1325","qty":"50"}]
							$totalprice= "";
							$pro_name= html_entity_decode(addslashes($p['pro_name']));
							$pro_id= $p['pro_id'];
							$weight_id=$p['weight_id'];
							$unitprice= $p['unitprice'];
							$qty=$p['pro_qty'];
							$inner_size=$p['inner_size'];
							$box_qty=$qty/$inner_size;
							$totalprice=$p['totalprice'];
							$discount=$p['discount'];
							$discount_amount=$p['discount_amount'];
							$taxable=$p['taxable'];
							$cgst_tax=$p['cgst_tax'];
							$cgst_amount=$p['cgst_amount'];
							$sgst_tax=$p['sgst_tax'];
							$sgst_amount=$p['sgst_amount'];
							$igst_tax=$p['igst_tax'];
							$igst_amount=$p['igst_tax'];
							$subtotal=$p['sub_total'];
							$grandtotal=$p['totalprice'];
							$cash_discount=$p['cash_discount'];
							$cash_discount_amount=$p['cash_discount_amount'];
							
							$row = array(
								"order_id",
								"pro_id",
								"weight_id",
								"pro_name",
								"unitprice",
								"pro_qty",
								"box_qty",
								"remaining_qty",
								"totalprice",
								"adate",
								"modify_date",
								"discount",
								"discount_amount",
								"taxable",
								"cgst_tax",
								"cgst_amount",
								"sgst_tax",
								"sgst_amount",
								"igst_tax",
								"igst_amount",
								"inner_size",
								"subtotal",
								"grandtotal",
								"cash_discount",
								"cash_discount_amount"
								
							);
							$value = array(
								$cart_id,
								$pro_id,
								$weight_id,
								$pro_name,
								$unitprice,
								$qty,
								$box_qty,
								$qty,
								$totalprice,
								$adate,
								date("Y-m-d H:i:s"),
								$discount,
								$discount_amount,
								$taxable,
								$cgst_tax,
								$cgst_amount,
								$sgst_tax,
								$sgst_amount,
								$igst_tax,
								$igst_amount,
								$inner_size,
								$subtotal,
								$grandtotal,
								$cash_discount,
								$cash_discount_amount
								
							);
							
							$ins = $db->rp_insert("order_product_item",$value,$row,0);
						}
							$order_pro_detail=mysqli_fetch_assoc($db->rp_getData("orders","*","id='".$cart_id."' AND isDelete=0","",0));
							$order_pro_detail['product']=array();
							$where = "order_id='".$order_pro_detail['id']."' AND isDelete=0";
							$dt = $db->rp_getData("order_product_item","*",$where);
							$r = array();
							if($dt)
							{
								while($row=mysqli_fetch_assoc($dt))
								{
									$r[]= $row;
								}
								
								$order_pro_detail['product']=$r;
							}
							$result_back[]=array("local_id"=>$detail['id'],"server_id"=>$cart_id);
							
						///////////////////////// notification ////////////////////
						$title_description="Order of <b>Rs.".$grand_total_rounded."</b> for date <b>".date('d-m-Y',strtotime($order_date))."</b> added by <b>".$customer_name."</b>";
						$notification=$system->setNotification(0,1,"Order Notification.",5,"Order Message",$title_description,"","",$order_date,$cart_id,"orders",$customer_type);	
					}
					else
					{
						echo "hi@###3121";exit;
						$error_internal[]="Row ".$i." Not Submitted due to some paramter missing";
					}
			 }
			
			}

        	$ack=array( "ack"=>1,
                "ack_msg"=>"Sync complete",
				"error_log"=>array("internal"=>$error_internal,"other"=>$error),
                "developer_msg"=>"Awwww see log hardip bhai!!",
				"result"=>$result_back
            );
			$db->printJSON($ack);
        }
		
		else  if($service=='create_update_customer' || $service==37)
        {
			// Create Customer 
			$mode=isset($_REQUEST['mode'])?$_REQUEST['mode']:"add";	
			$area_id=array();
			if(isset($_REQUEST['cname']) && isset($_REQUEST['phone']))			
			{
				$end_user_type			    = isset($_REQUEST['end_user_type'])?$db->clean($_REQUEST['end_user_type']):"";
				$type_of_executive		    = isset($_REQUEST['type_of_inquiry'])?$db->clean($_REQUEST['type_of_inquiry']):"";
				$company_type			    = isset($_REQUEST['company_type'])?$db->clean($_REQUEST['company_type']):"";
				$company_name			    = isset($_REQUEST['company_name'])?$db->clean($_REQUEST['company_name']):"";
				$address				    = isset($_REQUEST['address'])?$db->clean($_REQUEST['address']):"";
				$zip					    = isset($_REQUEST['zip'])?$db->clean($_REQUEST['zip']):"";
				$super_stockist_id		    = isset($_REQUEST['super_stockist_id'])?$db->clean($_REQUEST['super_stockist_id']):"";
				$city					    = isset($_REQUEST['city'])?$db->clean($_REQUEST['city']):"";
				$state					    = isset($_REQUEST['state'])?$db->clean($_REQUEST['state']):"";
				$country				    = isset($_REQUEST['country'])?$db->clean($_REQUEST['country']):"";
				$email					    = isset($_REQUEST['email'])?$db->clean($_REQUEST['email']):"";
				$dealer_distributor_id	    = isset($_REQUEST['dealer_distributor_id'])?$db->clean($_REQUEST['dealer_distributor_id']):"";
				$cname			            = isset($_REQUEST['cname'])?$db->clean($_REQUEST['cname']):"";
				$cst	                    = isset($_REQUEST['cst'])?$db->clean($_REQUEST['cst']):"";					
				$pan				        = isset($_REQUEST['pan'])?$db->clean($_REQUEST['pan']):"";
				$phone				        = isset($_REQUEST['phone'])?$db->clean($_REQUEST['phone']):"";
				$gst		                = isset($_REQUEST['gst'])?$db->clean($_REQUEST['gst']):"";
				$vat		                = isset($_REQUEST['vat'])?$db->clean($_REQUEST['vat']):"";					
				$excise			            = isset($_REQUEST['excise'])?$db->clean($_REQUEST['excise']):"";					
				$class_id			        = isset($_REQUEST['class_id'])?$db->clean($_REQUEST['class_id']):"";					
				$area_id		            = array($_REQUEST['area_id']);					
				$discount		            = $_REQUEST['discount'];					
				if(!empty($area_id))
				{
					for($i=0;$i<sizeof($area_id);$i++)
					{
						$item[]=array("area_id"=>$area_id[$i]);

					}
				}

				$inquiry_date			= date("Y-m-d");
				include('../include/class.executive.php');
				$inquiry=new Executive();
				if($mode=="add")
				{
				
					$ack=$inquiry->InsertExecutive($end_user_type,$type_of_executive,$company_type,$company_name,$address,$super_stockist_id,$city,$state,$country,$email,$dealer_distributor_id,$cname,$cst,$pan,$phone,$gst,$vat,$inquiry_date,$zip,$excise,$class_id,$item,$discount);
					
					if($ack['ack']==1)
					{
						$reply=array("ack"=>1,"ack_msg"=>"Customer successfully saved !!");							
					}
					else
					{
						$reply=$ack;							
					}						
				}
				else
				{
					if(isset($_REQUEST['cid']))
					{
						$executive_id=$_REQUEST['cid'];
						//echo $executive_id;exit;
						$ack=$inquiry->UpdateExecutive($executive_id,$end_user_type,$type_of_executive,$company_type,$company_name,$address,$super_stockist_id,$city,$state,$country,$email,$dealer_distributor_id,$cname,$cst,$pan,$phone,$gst,$vat,$inquiry_date,$zip,$excise,$class_id,$item,$discount,$password);						
						
						if($ack['ack']==1)
						{
							$reply=array("ack"=>0,"ack_msg"=>"Customer successfully updated!!");								
						}
						else
						{
							$reply=$ack;			
						}
					}
					else
					{
						$reply=array("ack"=>0,"ack_msg"=>"No Customer found to update!!");							
					}
				}											
			}
			else
			{
				$reply=array("ack"=>0,"ack_msg"=>"Person name, contact number and type of Customer are compalsary!!");
			}
			$db->printJSON($reply);
        }
		
		/*else if($service=="sync_offline_customers"|| $service==39)
		{
			$body= file_get_contents('php://input');
			$customers=($body!="")?(array)json_decode($body,true):array();
			$inquiry=new Executive();
			$updated=array();
			$error=array();
			$reply=array();
			for($j=0;$j<sizeof($customers['values']);$j++)
			{
				$customer=$customers['values'][$j]['nameValuePairs'];
					$area_id   =array();
					$server_id =array_key_exists('server_id',$customer)?$db->clean($customer['server_id']):0;
					$local_id  =array_key_exists('local_id',$customer)?$db->clean($customer['local_id']):0;
					$se_id     =array_key_exists('se_id',$customer)?$db->clean($customer['se_id']):0;
					if(array_key_exists('cname',$customer) && array_key_exists('phone',$customer))			
					{
						$end_user_type			    = array_key_exists('end_user_type',$customer)?$db->clean($customer['end_user_type']):"";
						$type_of_executive		    = array_key_exists('type_of_executive',$customer)?$db->clean($customer['type_of_executive']):"";
						$company_type			    = array_key_exists('company_type',$customer)?$db->clean($customer['company_type']):"";
						$company_name			    = array_key_exists('company_name',$customer)?$db->clean($customer['company_name']):"";
						$address				    = array_key_exists('address',$customer)?$db->clean($customer['address']):"";
						$zip					    = array_key_exists('zip',$customer)?$db->clean($customer['zip']):"";
						$super_stockist_id		    = array_key_exists('super_stockist_id',$customer)?$db->clean($customer['super_stockist_id']):"";
						$city					    = array_key_exists('city',$customer)?$db->clean($customer['city']):"";
						$state					    = array_key_exists('state',$customer)?$db->clean($customer['state']):"";
						$country				    = array_key_exists('country',$customer)?$db->clean($customer['country']):"";
						$email					    = array_key_exists('email',$customer)?$db->clean($customer['email']):"";
						$dealer_distributor_id	    = array_key_exists('dealer_distributor_id',$customer)?$db->clean($customer['dealer_distributor_id']):"";
						$cname			            = array_key_exists('cname',$customer)?$db->clean($customer['cname']):"";
						$cst	                    = array_key_exists('cst',$customer)?$db->clean($customer['cst']):"";	
						$pan				        = array_key_exists('pan',$customer)?$db->clean($customer['pan']):"";
						$phone				        = array_key_exists('phone',$customer)?$db->clean($customer['phone']):"";
						$gst		                = array_key_exists('gst',$customer)?$db->clean($customer['gst']):"";
						$mobile_no1		                = array_key_exists('other_contact',$customer)?$db->clean($customer['other_contact']):"";
						$vat		                = array_key_exists('vat',$customer)?$db->clean($customer['vat']):"";
						$excise			            = array_key_exists('excise',$customer)?$db->clean($customer['excise']):"";$discount			        = array_key_exists('discount',$customer)?$db->clean($customer['discount']):"";$class_id			        = array_key_exists('class_id',$customer)?$db->clean($customer['class_id']):"";$area_id		            = array(array_key_exists('area_id',$customer)?$customer['area_id']:0);if(!empty($area_id))
						{
							for($i=0;$i<sizeof($area_id);$i++)
							{
								$item[]=array("area_id"=>$area_id[$i]);

							}
						}

						$inquiry_date			= date("Y-m-d");
						
						
						if($server_id==0)
						{
						
							$ack=$inquiry->InsertExecutive($end_user_type,$type_of_executive,$company_type,$company_name,$address,$super_stockist_id,$city,$state,$country,$email,$dealer_distributor_id,$cname,$cst,$pan,$phone,$gst,$vat,$inquiry_date,$zip,$excise,$class_id,$mobile_no1,$item,$discount,$se_id,$local_id,"android","");
							
							if($ack['ack']==1)
							{
								$reply[]=array("ack"=>1,"ack_msg"=>"Customer successfully saved !!");		
								$updated[]=array("server_id"=>$ack['inserted_id'],"local_id"=>$local_id,"area"=>$ack['areas']);	
							}
							else
							{
								$error[]=$ack;							
							}						
						}
						else
						{
							
							$executive_id=$server_id;
							//echo $executive_id;exit;
							$ack=$inquiry->UpdateExecutive($executive_id,$end_user_type,$type_of_executive,$company_type,$company_name,$address,$super_stockist_id,$city,$state,$country,$email,$dealer_distributor_id,$cname,$cst,$pan,$phone,$gst,$vat,$inquiry_date,$zip,$excise,$class_id,$mobile_no1,$item,$discount,$password,$se_id,$local_id,"");
							if($ack['ack']==1)
							{
								$reply[]=array("ack"=>1,"ack_msg"=>"Customer successfully updated!!");	
								$updated[]=array("server_id"=>$server_id,"local_id"=>$local_id,"area"=>$ack['areas']);									
							}
							else
							{
								$error[]=$ack;			
							}
							
						}											
					}
					else
					{
						$reply[]=array("ack"=>0,"ack_msg"=>"Person name, contact number and type of Customer are compalsary!!");
					}
			}
			
			$ack=array( "ack"=>1,
                "ack_msg"=>"Customer Save Successfully",
                "developer_msg"=>"Customer Save Successfully",
				"result"=>$updated,
				"reply"=>$reply,
				"error"=>$error
            );
			$db->printJSON($ack);
		
		}*/

		else if($service=="sync_offline_customers"|| $service==39)
		{
			$detail['type_of_executive']	 = isset($_REQUEST['type_of_executive'])?$db->clean($_REQUEST['type_of_executive']):"";
			$detail['sales_id']			 = isset($_REQUEST['sales_id'])?$db->clean($_REQUEST['sales_id']):"";
			$detail['company_name']		 = isset($_REQUEST['company_name'])?$db->clean($_REQUEST['company_name']):"";
			$detail['person_name']		 = isset($_REQUEST['person_name'])?$db->clean($_REQUEST['person_name']):"";
			$detail['mobile_number']	 = isset($_REQUEST['mobile_number'])?$db->clean($_REQUEST['mobile_number']):"";
			$detail['other_mobile_number'] = isset($_REQUEST['other_mobile_number'])?$db->clean($_REQUEST['other_mobile_number']):"";
			$detail['email']	         = isset($_REQUEST['email'])?$db->clean($_REQUEST['email']):"";
			$detail['address']	         = isset($_REQUEST['address'])?$db->clean($_REQUEST['address']):"";
			$detail['gst_no']	         = isset($_REQUEST['gst_no'])?$db->clean($_REQUEST['gst_no']):"";
			$detail['dealer_id']	     = isset($_REQUEST['dealer_id'])?$db->clean($_REQUEST['dealer_id']):"";
			$detail['country']	    	 = isset($_REQUEST['country'])?$db->clean($_REQUEST['country']):"";
			$detail['state']	     	 = isset($_REQUEST['state'])?$db->clean($_REQUEST['state']):"";
			$detail['city']	     		 = isset($_REQUEST['city'])?$db->clean($_REQUEST['city']):"";
			$detail['class_id']	     	 = isset($_REQUEST['class_id'])?$db->clean($_REQUEST['class_id']):"";
			$detail['area_id']	     	 = isset($_REQUEST['area_id'])?$db->clean($_REQUEST['area_id']):"";

			$detail['latitude']	     	 = isset($_REQUEST['latitude'])?$db->clean($_REQUEST['latitude']):"";

			$detail['longitude']	     	 = isset($_REQUEST['longitude'])?$db->clean($_REQUEST['longitude']):"";
			$detail['whatsapp_no']	     	 = isset($_REQUEST['whatsapp_no'])?$db->clean($_REQUEST['whatsapp_no']):"";

			require_once('../include/class.executive.php');
			$inquiry1=new Executive();
			$ack=$inquiry1->InsertExecutive($detail,$_FILES);
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

		// inquiry sync
		else  if($service=='update_inquiry_sync' || $service==45)
        {
			$id	                            = isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
			$detail1['executive_type']		= isset($_REQUEST['executive_type'])?$db->clean($_REQUEST['executive_type']):"";
			$detail1['sales_executive_id']	= isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";
			$detail1['company_name']	    = isset($_REQUEST['company_name'])?$db->clean($_REQUEST['company_name']):"";
			$detail1['mobile_number']	    = isset($_REQUEST['mobile_number'])?$db->clean($_REQUEST['mobile_number']):"";
			$detail1['person_name']	    	= isset($_REQUEST['person_name'])?$db->clean($_REQUEST['person_name']):"";
			$detail1['description']	        = isset($_REQUEST['description'])?$db->clean($_REQUEST['description']):"";
			$detail1['datetime']			= date("Y-m-d H:i:s");
			$detail1['inquiry_date'] 		= isset($_REQUEST['inquiry_date'])?date("Y-m-d", strtotime($_REQUEST['inquiry_date'])) : "";
			$detail1['inquiry_assign_date'] = isset($_REQUEST['inquiry_assign_date'])?date("Y-m-d", strtotime($_REQUEST['inquiry_assign_date'])) : "";
			$detail1['dealer_id']	        = isset($_REQUEST['dealer_id'])?$db->clean($_REQUEST['dealer_id']):"";
			$detail1['latitude']	        = isset($_REQUEST['latitude'])?$db->clean($_REQUEST['latitude']):"";
			$detail1['longitude']	        = isset($_REQUEST['longitude'])?$db->clean($_REQUEST['longitude']):"";
			$detail1['address']	        	= isset($_REQUEST['address'])?$db->clean($_REQUEST['address']):"";
			$detail1['other_mobile_no']	    = isset($_REQUEST['other_mobile_no'])?$db->clean($_REQUEST['other_mobile_no']):"";
			$detail1['distributor_id']	    = isset($_REQUEST['distributor_id'])?$db->clean($_REQUEST['distributor_id']):"";
			$detail1['country']	            = isset($_REQUEST['country'])?$db->clean($_REQUEST['country']):"";
			$detail1['state']	            = isset($_REQUEST['state'])?$db->clean($_REQUEST['state']):"";
			$detail1['city']	            = isset($_REQUEST['city'])?$db->clean($_REQUEST['city']):"";
			$detail1['source_of_inquiry'] 	= isset($_REQUEST['source_of_inquiry'])?$db->clean($_REQUEST['source_of_inquiry']):"";
			$detail1['designation']	   		= isset($_REQUEST['designation'])?$db->clean($_REQUEST['designation']):"";
			$detail1['zone']	   			= isset($_REQUEST['zone'])?$db->clean($_REQUEST['zone']):"";
			$detail1['email_address']	   	= isset($_REQUEST['email_address'])?$db->clean($_REQUEST['email_address']):"";
			$detail1['inquiry_assign_to']	= isset($_REQUEST['inquiry_assign_to'])?$db->clean($_REQUEST['inquiry_assign_to']):"";
			$detail1['inquiry_created_by']	= isset($_REQUEST['inquiry_created_by'])?$db->clean($_REQUEST['inquiry_created_by']):"";
			$detail1['birth_date']	   	    = isset($_REQUEST['birth_date'])?date("Y-m-d", strtotime($_REQUEST['birth_date'])) : "";
			$detail1['product_id']	        = isset($_REQUEST['product_id'])?$db->clean($_REQUEST['product_id']):"";
			$detail1['quantity']	        = isset($_REQUEST['quantity'])?$db->clean($_REQUEST['quantity']):"";
			$detail1['u_w_flag']	        = isset($_REQUEST['u_w_flag'])?$db->clean($_REQUEST['u_w_flag']):"";
			$detail1['u_w_remark']	        = isset($_REQUEST['u_w_remark'])?$db->clean($_REQUEST['u_w_remark']):"";
			$detail1['quotation_flag']	    = isset($_REQUEST['quotation_flag'])?$db->clean($_REQUEST['quotation_flag']):"";
			$detail1['quotation_remark']	= isset($_REQUEST['quotation_remark'])?$db->clean($_REQUEST['quotation_remark']):"";
			$detail1['customer_requirement'] = isset($_REQUEST['customer_requirement'])?$db->clean($_REQUEST['customer_requirement']):"";
			$detail1['class_id'] = isset($_REQUEST['class_id'])?$db->clean($_REQUEST['class_id']):"";
			$detail1['area_id']  = isset($_REQUEST['area_id'])?$db->clean($_REQUEST['area_id']):"";
			
			if($_REQUEST['id']=="")
			{
			    $reply=$objSalesExecutive->addNoOrderInquiry($detail1,$_FILES);    
			}
			else
			{
			    $reply=$objSalesExecutive->updateNoOrderInquiry($detail1,$id);
			}
			
			$inquiry_insert_detail=mysqli_fetch_assoc($db->rp_getData("no_order_inquiry","*","id='".$reply['inserted_id']."' AND isDelete=0","",0));
			
			$result_back[]=array("local_id"=>$detail1['local_id'],"server_id"=>$reply['inserted_id'],"reply"=>$reply);
			$ack=array("ack"=>$reply['ack'],"ack_msg"=>$reply['ack_msg'],"developer_msg"=>$reply['developer_msg']);
            $db->printJSON($ack);
        }

		else if($service=='add_area' || $service==49)
        {

            //$areas=$db->getRequestedParam("areas"); //addArea
			$body= file_get_contents('php://input');
			$areas=($body!="")?(array)json_decode($body,true):array();
			$sales_id=isset($_REQUEST['sales_id'])?$_REQUEST['sales_id']:"";	
			$ack=$system->addArea($areas['values'],$sales_id);
            $db->printJSON($ack);

        }
		else if($service=='add_followup_notification' || $service==50)
        {

            $ack=$system->addFollowupNotification();
            $db->printJSON($ack);

        }
		else if($service=="get_notification" || $service==59)
		{
			if(isset($_REQUEST['user_id']) && isset($_REQUEST['user_type']))
			{
				$user_id=isset($_REQUEST['user_id'])?$db->clean($_REQUEST['user_id']):"";
				$user_type=isset($_REQUEST['user_type'])?$db->clean($_REQUEST['user_type']):"";
				
				if($user_type=="customer"){
					$ack=$objEmp->getNotification($user_id,$user_type);
					$db->printJSON($ack);
				}
				else{
					$ack=array( "ack"=>0,
							"ack_msg"=>"Not Valid User!",
							"developer_msg"=>"Not Valid User!",
						);
					$db->printJSON($ack);
				}
				
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		else if($service=="customer_order_request" || $service==66)
		{
			if(isset($_REQUEST['cid']) && isset($_REQUEST['cid']))
			{
				$detail['cid']=isset($_REQUEST['cid'])?$db->clean($_REQUEST['cid']):"";
				$products=(isset($_REQUEST['products']) && $_REQUEST['products']!="")?json_decode($_REQUEST['products'],true):array();
				
				$ack=$objOrder->AddCustomerOrderRequest($detail,$products);
					$db->printJSON($ack);
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		else if($service=="accept_invoice_order" || $service==67)
		{
			if(isset($_REQUEST['invoice_id']) && isset($_REQUEST['invoice_id']))
			{
				$detail['invoice_id']=isset($_REQUEST['invoice_id'])?$db->clean($_REQUEST['invoice_id']):"";
				
				$ack=$objOrder->AcceptinvoiceOrder($detail);
				$db->printJSON($ack);
			}
			else
			{
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		else if($service=="reject_invoice_order" || $service==68)
		{
			if(isset($_REQUEST['invoice_id']) && isset($_REQUEST['invoice_id']))
			{
				$detail['invoice_id']=isset($_REQUEST['invoice_id'])?$db->clean($_REQUEST['invoice_id']):"";
				$detail['remarks']=isset($_REQUEST['remark'])?$db->clean($_REQUEST['remark']):"";
				$ack=$objOrder->RejectinvoiceOrder($detail);
					$db->printJSON($ack);
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		else if($service=="order_request_history" || $service==69)
		{
			if(isset($_REQUEST['cid']) && isset($_REQUEST['cid']))
			{
				$detail['cid']=isset($_REQUEST['cid'])?$db->clean($_REQUEST['cid']):"";
				
				$ack=$objOrder->GetOrderRequestHistory($detail);
					$db->printJSON($ack);
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		else if($service=="order_request_history_detail" || $service==70)
		{
			if(isset($_REQUEST['id']) && isset($_REQUEST['id']))
			{
				$detail['id']=isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
				
				$ack=$objOrder->GetOrderRequestDetail($detail);
				$db->printJSON($ack);
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		else if($service=="performa_history" || $service==71)
		{
			if(isset($_REQUEST['cid']) && isset($_REQUEST['cid']))
			{
				$detail['cid']=isset($_REQUEST['cid'])?$db->clean($_REQUEST['cid']):"";
				
				$ack=$objOrder->GetPerformaHistory($detail);
					$db->printJSON($ack);
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		else if($service=="performa_history_detail" || $service==72)
		{
			if(isset($_REQUEST['id']) && isset($_REQUEST['id']))
			{
				$detail['id']=isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
				
				$ack=$objOrder->GetPerformaDetail($detail);
				$db->printJSON($ack);
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		else if($service=="get_dispatch_detail" || $service==73)
		{
			if(isset($_REQUEST['dispatch_id']) && isset($_REQUEST['dispatch_id']))
			{
				$detail['dispatch_id']=isset($_REQUEST['dispatch_id'])?$db->clean($_REQUEST['dispatch_id']):"";
				
				$ack=$objDispatch->GetDispatchDetail($detail);
				$db->printJSON($ack);
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		
		else if($service=="get_dispatched_order" || $service==74)
		{
			if(isset($_REQUEST['order_id']) && isset($_REQUEST['order_id']))
			{
				$detail['order_id']=isset($_REQUEST['order_id'])?$db->clean($_REQUEST['order_id']):"";
				
				$ack=$objDispatch->GetDispatchOrderDetail($detail);
				$db->printJSON($ack);
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		/*else if($service=="get_discount" || $service==81)
		{
			if(isset($_REQUEST['uid']) && $_REQUEST['uid']!="")
			{
				$tc_discount_r=$db->rp_getData("price_table","*","isDelete=0 AND uid='".$_REQUEST['uid']."'");
				
				if($tc_discount_r)
				{
					$Data=array();
					while($tc_discount_d=mysqli_fetch_assoc($tc_discount_r))
					{
						$tc_discount_d['top_category_name']=$db->rp_getValue("top_category_master","name","id='".$tc_discount_d['tcid']."' AND isDelete=0");
						$tc_discount_d['discount']=$db->rp_num($tc_discount_d['discount']);
						$tc_discount_d['basic']=$db->round($tc_discount_d['basic']);
						$tc_discount_d['trade']=$db->round($tc_discount_d['trade']);
						$Data[]=$tc_discount_d;
					}
				}
				else
				{
					$Data=array();
				}
				$cash_discount=$db->rp_getValue("customer","cash_discount","id='".$_REQUEST['uid']."' AND isDelete=0");
				$brand_id=$db->rp_getValue("customer","brand_id","id='".$_REQUEST['uid']."' AND isDelete=0");
				if($cash_discount=="")
				{
					$cash_discount=0;
				}
				$ack=array( "ack"=>1,
                "ack_msg"=>"Customer Cash Discount Found!!",
                "developer_msg"=>"Customer Cash Discount Found!!","result"=>$Data,"cash_discount"=>$cash_discount,"brand_id"=>$brand_id
	            );
				$db->printJSON($ack);
			}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"Internal error!!",
                "developer_msg"=>"Service Parameter missing or not valid!!",
	            );
				$db->printJSON($ack);
			}
		}*/
		else if($service=="get_news" || $service==81)
		{
		    
			$ctable 	= "news";
			$id = $_REQUEST['id'];
			if($id!="")
			{
			    $ctable_r = $db->rp_getData($ctable,"*","id='".$id."' AND isDelete=0","display_order",0);
			}
			else
			{
			    $ctable_r = $db->rp_getData($ctable,"*","isDelete=0","display_order",0);    
			}
			$news=array();
			if($ctable_r){				
				while($ctable_d = mysqli_fetch_assoc($ctable_r))
				{
				    $ctable_d['created_date'] = date('d F Y H:i A',strtotime($ctable_d['created_date']));
					$ctable_d['image_path']=SITEURL.NEWS.$ctable_d['image_path'];
					$news[]=$ctable_d;					
				}
				$ack=array("ack"=>1,"ack_msg"=>"News Found","result"=>$news);
				echo json_encode($ack);
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"No News found!!");
				echo json_encode($ack);
			}
		}
		else if($server=="delete_inquiry" || $service==82)
		{
			$flag = $_REQUEST['flag'];
			if(isset($_REQUEST['id']) && $_REQUEST['id']!="")
			{
				if($flag = "no_order_inquiry")
				{
					$delete_inquiry=$db->rp_update("no_order_inquiry",array("isDelete"=>1),"id='".$_REQUEST['id']."'");
				}
				else
				{
					$delete_inquiry=$db->rp_update("customer_inquiry",array("isDelete"=>1),"id='".$_REQUEST['id']."'");
				}
				if($delete_inquiry)
				{
					$ack=array("ack"=>1,"ack_msg"=>"Order Inquiry Delete Successfully");
					echo json_encode($ack);
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Order Inquiry Deete Failed");
					echo json_encode($ack);
				}
			}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"Inquiry ID required!!",
                "developer_msg"=>"Inquiry ID required!!",
            );
			$db->printJSON($ack);
			}
		}	
		else if($service=="search_product" || $service==83)
		{
			/*if($_REQUEST['searchName']!=""  && isset($_REQUEST['searchName']))
			{*/
				$limit=array();
				$limit['ul']=isset($_REQUEST['ul'])?$_REQUEST['ul']:"";
				$limit['ll']=isset($_REQUEST['ll'])?$_REQUEST['ll']:"";
				$uid=isset($_REQUEST['uid'])?$_REQUEST['uid']:"";

				if($uid!="")
				{
					$price_list_id=$db->rp_getValue("executive","price_list_id","id='".$uid."' AND isDelete=0");
				}
				else
				{
					$price_list_id=0;
				}
				$limit=$objProduct->getLimit($limit);
				$result=array();
				if($_REQUEST['searchName']!="" && isset($_REQUEST['searchName']))
				{					
					$PROIDS=array();
					$where=")";
					/*$pro_r1=$db->rp_getData("product_weight_price","product_id","catno LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
					if($pro_r1)
					{
						while($pro_d1=mysqli_fetch_assoc($pro_r1))
						{
							$PROIDS[]=$pro_d1['product_id'];
						}
					}
					if(!empty($PROIDS))
					{
						$PROIDS=implode(",", $PROIDS);
						$where=" OR id IN (".$PROIDS."))";
					}*/
					$pro_r=$db->rp_getData("product","*","(name LIKE '%".$_REQUEST['searchName']."%'".$where." AND isDelete=0","",0,$limit);
				}
				else
				{
					$pro_r=$db->rp_getData("product","*","isDelete=0","",0,$limit);
				}
				if($pro_r)
				{
					while($pro_d=mysqli_fetch_assoc($pro_r))
					{
						$pro_d['cat_name']=$db->rp_getValue("category_master","name","id='".$pro_d['cid']."' AND isDelete=0",0);
						$pro_d['product_code']=$db->rp_getValue("product_weight_price","catno","product_id='".$pro_d['id']."' AND isDelete=0",0);						
						$pro_d['total_size']=$db->rp_getTotalRecord("product_weight_price","product_id='".$pro_d['id']."' AND isDelete=0",0);
						$descr=html_entity_decode($pro_d['descr']);
						$descr=strip_tags($descr);
						// $descr=str_replace("\r\n","",$descr);
						// $descr=str_replace(",",",<br/>",$descr);
						$descr = nl2br($descr);
						$pro_d['descr']=$descr;
						$pid=$pro_d['id'];
						if($pro_d['image_path']!=""){
							$pro_d['image_path']=SITEURL.PRODUCT.$pro_d['image_path'];			
						}
						
						$price_r=	$db->rp_getData("product_weight_price","id,weight_id,price,product_id,inner_size,outer_size,stock_qty,catno","isDelete=0 AND product_id=".$pid,"",0);
						
						if($price_r){
							$product_weight_price=array();
							while($price_d=mysqli_fetch_assoc($price_r))
							{
								$price_d['original_price']=$db->rp_number_format($price_d['price'],2);
								
								$price_d['price']=$db->rp_number_format($price_d['price'],2);
								$price_d['discount']=0;									
								$price_d['discounted_amount']=0;
								if($price_list_id!=0)
								{
									$check_product_in_list=$db->rp_getTotalRecord("product_price_list","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."' AND price_list_id='".$price_list_id."'",0);
									if($check_product_in_list>0)
									{
										$price_d['price']=$db->rp_number_format($db->rp_getValue("product_price_list","discounted_price","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."' AND price_list_id='".$price_list_id."'"),2);

										$price_d['discount']=$db->rp_number_format($db->rp_getValue("product_price_list","discount","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."' AND price_list_id='".$price_list_id."'"),2);

										$price_d['discounted_amount']=$db->rp_number_format($db->rp_getValue("product_price_list","discounted_amount","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."'"),2);
									}
								}
								
								$price_d['name']=$db->rp_getValue("weight","name","id='".$price_d['weight_id']."'");
								$price_d['display_order']=$db->rp_getValue("weight","display_order","id='".$price_d['weight_id']."'");
								$product_weight_price[]=$price_d;
								$objProduct->sortBy('display_order', $product_weight_price, 'asc');
							}
							$pro_d['product_weight_price']=$product_weight_price;
							$product[]=$pro_d;
						}
						else{
							$pro_d['product_weight_price'] = array();
							$product[]=$pro_d;
						}
						$result[]=$pro_d;
					}
				}
				if(!empty($result))
				{
					$ack=array("ack"=>1,"result"=>$result);
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"No data Avialable");
				}
				/*$ack=array("ack"=>1,"result"=>$result);*/
				echo json_encode($ack);
			/*}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"Internal error!!",
                "developer_msg"=>"Service Parameter missing or not valid!!",
	            );
				$db->printJSON($ack);
			}*/
		}
		else if($service=="get_login_profile" || $service==84)
		{
			$se_id=isset($_REQUEST['sales_id'])?$_REQUEST['sales_id']:"";
			if($se_id!="")
			{
				$login_r=$db->rp_getData("sales_executive","*","id='".$se_id."'");
				if($login_d=mysqli_fetch_assoc($login_r))
				{
				    
				    $imgpath = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$login_d['id']."' AND id='".$login_d['image_path']."'");
				    $login_d['image_path'] = ($login_d['image_path']!= "")?$imgpath:"";
				    if($login_d['type']=='sales_manager')
				    {
				        $login_d['type'] = 'Sales Manger';
				    }
					$class_r=$db->rp_getData("sales_executive_map_area","DISTINCT(class_id)","sales_executive_id='".$login_d['id']."' AND isDelete=0");
					$class_name=array();
					if($class_r)
					{
						while($class_d=mysqli_fetch_assoc($class_r))
						{
							$class_name[]=$db->rp_getValue("class","name","id='".$class_d['class_id']."'");
						}
						$class_name=implode(",",$class_name);
					}
					else
					{
						$class_name="";
					}
					$login_d['class_name']=$class_name;
					$area_r=$db->rp_getData("sales_executive_map_area","area_id"," sales_executive_id='".$login_d['id']."' AND isDelete=0","",0);
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
					$ack=array( "ack"=>1,
	                "ack_msg"=>"Sales Officer Found!!",
	                "developer_msg"=>"Sales Officer Found!!",
	                "result"=>$login_d
		            );
					$db->printJSON($ack);
				}
				else
				{
					$ack=array( "ack"=>0,
	                "ack_msg"=>"No such a Sales Officer Found!!",
	                "developer_msg"=>"No such a Sales Officer Found!!",
		            );
					$db->printJSON($ack);
				}
			}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"Sales Officer Required!!",
                "developer_msg"=>"Sales Officer Required!!",
	            );
				$db->printJSON($ack);
			}
		}
		
		else if($service=="add_to_cart" || $service==85)
		{
			if(isset($_REQUEST['cid']) && isset($_REQUEST['cid']) && isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['sales_executive_id']))
			{
				$detail['cid']=isset($_REQUEST['cid'])?$db->clean($_REQUEST['cid']):"";//customer id
				$detail['sales_executive_id']=isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";
				$detail['order_id']=isset($_REQUEST['order_id'])?$db->clean($_REQUEST['order_id']):"";
				//$detail['cash_discount_flag']=isset($_REQUEST['cash_discount_flag'])?$db->clean($_REQUEST['cash_discount_flag']):0;
				// print_r($_REQUEST['products']);exit;
				$products=(isset($_REQUEST['products']) && $_REQUEST['products']!="")?json_decode($_REQUEST['products'],true):array();
				// print_r($products);exit;
				 // pid,qty,weight_id->product array
				//$body= file_get_contents('php://input');
				$ack=$objOrder->AddToCartApi($detail,$products);
				$db->printJSON($ack);
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}

		}
		else if($service=="place_order" || $service==86)
		{
			if(isset($_REQUEST['cid']) && isset($_REQUEST['cid']))
			{
				$detail['sales_executive_id']=isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";//customer id
				$detail['cid']=isset($_REQUEST['cid'])?$db->clean($_REQUEST['cid']):"";//customer id
				$detail['booking']=isset($_REQUEST['booking'])?$db->clean($_REQUEST['booking']):"";
				$detail['transport']=isset($_REQUEST['transport'])?$db->clean($_REQUEST['transport']):"";
				$detail['cash_discount_flag']=isset($_REQUEST['cash_discount_flag'])?$db->clean($_REQUEST['cash_discount_flag']):0;				
				$detail['class_id']=isset($_REQUEST['class_id'])?$db->clean($_REQUEST['class_id']):"";
				$detail['area_id']=isset($_REQUEST['area_id'])?$db->clean($_REQUEST['area_id']):"";
				$detail['dealer_id']=isset($_REQUEST['dealer_id'])?$db->clean($_REQUEST['dealer_id']):"";
				$detail['entry_flag']=isset($_REQUEST['entry_flag'])?$db->clean($_REQUEST['entry_flag']):"";
				$ack=$objOrder->PlaceOrder($detail);
				$db->printJSON($ack);
				
				// $products=(isset($_REQUEST['products']) && $_REQUEST['products']!="")?json_decode($_REQUEST['products'],true):array();
				// $body= file_get_contents('php://input');
				// $ack=$objOrder->PlaceOrder($detail,$body);
				// $ack=$objOrder->AddToCart($detail,$products);
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		else if($service=="delete_product" || $service==87)
		{
			if(isset($_REQUEST['cart_item_id']) && $_REQUEST['cart_item_id']!="")
			{
				$delete_item=$db->rp_delete("order_product_item","id='".$_REQUEST['cart_item_id']."'");
				if($delete_item)
				{
					$ack=array( "ack"=>1,
	                "ack_msg"=>"Product Remove From cart successfully!!",
	                "developer_msg"=>"Product Remove From cart successfully!!",
		            );
					$db->printJSON($ack);
				}
				else
				{
					$ack=array( "ack"=>0,
	                "ack_msg"=>"Porduct already Not in Cart!!",
	                "developer_msg"=>"Porduct already Not in Cart!!",
		            );
					$db->printJSON($ack);
				}
			}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"Internal error!!",
                "developer_msg"=>"Service Parameter missing or not valid!!",
	            );
				$db->printJSON($ack);
			}
		}
		else if($service=="update_product" || $service==88)
		{
			if(isset($_REQUEST['cart_item_id']) && isset($_REQUEST['pid']) && isset($_REQUEST['qty']) && isset($_REQUEST['weight_id']) & $_REQUEST['cart_item_id']!="" && $_REQUEST['pid']!="" && $_REQUEST['qty']!=""  &&  $_REQUEST['weight_id']!="")
			{
				$inner_size=$db->rp_getValue("product_weight_price","inner_size","weight_id='".$_REQUEST['weight_id']."' AND product_id='".$_REQUEST['pid']."'","",0);

				$outer_size=$db->rp_getValue("product_weight_price","outer_size","weight_id='".$_REQUEST['weight_id']."' AND product_id='".$_REQUEST['pid']."'","",0);

				$unitprice=$db->rp_getValue("order_product_item","unitprice","weight_id='".$_REQUEST['weight_id']."' AND pro_id='".$_REQUEST['pid']."'","",0);

				//$update_qty=$_REQUEST['qty'];

				if($_REQUEST['box_qty']!="" && $_REQUEST['box_qty']!=0 && $_REQUEST['cartoon_qty']!="" && $_REQUEST['cartoon_qty']!=0)
				{
					$box_qty = $_REQUEST['box_qty'] * $inner_size;
					$cartoon_qty=$_REQUEST['cartoon_qty'] * $outer_size;
					$update_qty = $box_qty + $cartoon_qty;	
				}
				else if($_REQUEST['box_qty']!="" && $_REQUEST['box_qty']!=0)
				{
					$box_qty = $_REQUEST['box_qty'] * $inner_size;
					$update_qty = $box_qty ;
				}
				else if($_REQUEST['cartoon_qty']!="" && $_REQUEST['cartoon_qty']!=0)
				{
					$cartoon_qty=$_REQUEST['cartoon_qty'] * $outer_size ;
					$update_qty = $cartoon_qty;	
				}

				
				$update_totalprice=$db->rp_num($update_qty*$unitprice);
				//$update_box_qty=$db->rp_num($_REQUEST['qty']/$inner_size);
				//$update_cartoon_qty=$db->rp_num($update_box_qty/$outer_size);

				$update_box_qty=$db->rp_num($_REQUEST['box_qty']);
				$update_cartoon_qty=$db->rp_num($_REQUEST['cartoon_qty']);

				$update_item=array(
					"pro_qty"=>$update_qty,
					"remaining_qty"=>$update_qty,
					"totalprice"=>$update_totalprice,
					"box_qty"=>$update_box_qty,
					"cartoon_qty"=>$update_cartoon_qty,
					"modified_date"=>date("Y-m-d H:i:s"),
				);

				$isUpdate=$db->rp_update("order_product_item",$update_item,"id='".$_REQUEST['cart_item_id']."' AND isDelete=0",0);	
				if($isUpdate)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Product Updated Successfully","ack_msg"=>"Product Updated Successfully");
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Product Updated Failed","ack_msg"=>"Product Updated Failed");
				}
				$db->printJSON($reply);
			}
			else
			{
				$ack=array( "ack"=>0,
	                "ack_msg"=>"Internal error!!",
	                "developer_msg"=>"Service Parameter missing or not valid!!",
	            );
				$db->printJSON($ack);
			}
		}
		else if($service=="get_cart" || $service==89)
		{
			if(isset($_REQUEST['cid']) && isset($_REQUEST['cid']) && isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['sales_executive_id']))
			{
				$order_r=$db->rp_getData("orders","*","customer_id='".$_REQUEST['cid']."' AND sales_id='".$_REQUEST['sales_executive_id']."' AND status=-1 AND isDelete=0");
				if($order_r)
				{
					$r=array();
					$order_d=mysqli_fetch_assoc($order_r);
					$order_item_r=$db->rp_getData("order_product_item","*","order_id='".$order_d['id']."' AND  isDelete=0");
					$total_qty=$db->rp_getValue("order_product_item","SUM(pro_qty)","order_id='".$order_d['id']."' AND  isDelete=0");
					$order_d['total_qty']=$total_qty;
					$subtotal=0;
					$taxable_amount=0;
					$grandtotal=0;
					if($order_item_r)
					{	
						$data=array();
						while($order_item_d=mysqli_fetch_assoc($order_item_r))
						{
							$subtotal+=$order_item_d['totalprice'];
							$GST=$db->rp_getValue("product","igst","id='".$order_item_d['pro_id']."'");
							$order_item_d['size']=$db->rp_getValue("weight","name","id='".$order_item_d['weight_id']."' AND isDelete=0");
							$data[]=$order_item_d;
						}

						$gst_amount=$db->rp_num(($subtotal*$GST)/100);
						$grandtotal=$db->rp_num($subtotal+$gst_amount);
						$order_d['gst']=$GST;
						$order_d['gst_amount']=$db->rp_num($gst_amount);
						$order_d['final_total']=$grandtotal;
						$order_d['subtotal']=$db->rp_num($subtotal);
						$whole = floor($grandtotal);      // 1
				        $fraction = $grandtotal - $whole;

				        $f1=  $db->rp_num((float)$fraction, 2, '.', '');
						$order_d['roundoff']=$db->rp_num($f1);
						$order_d['grandtotal']=$db->rp_num(round($grandtotal));
						$order_d['products']=$data;
						$r[]=$order_d;

						$ack=array("ack"=>1,"ack_msg"=>"Order Detail Found!","developer_msg"=>"Order Detail Found!","result"=>$r,"total_qty"=>$total_qty);
					}
					else
					{
						$ack=array( "ack"=>0,"ack_msg"=>"Cart is Empty!","developer_msg"=>"Cart is Empty");
						/*$data=array();
						$order_d['products']=$data;
						$r[]=$order_d;*/
					}
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Cart is Empty!","developer_msg"=>"Cart is Empty");
				}
				$db->printJSON($ack);
			}
			else
			{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>"Internal Error!!Some Parameter Missing!",
							"developer_msg"=>"Internal Error!!Some Parameter Missing!",
						);
				$db->printJSON($ack);
			}
		}
		else if($service=="get_cart_qty_count" || $service==90)
		{
			if(isset($_REQUEST['cid']) && isset($_REQUEST['cid']))
			{
				$sales_id=isset($_REQUEST['sales_executive_id'])?$_REQUEST['sales_executive_id']:"";
				$where="";
				if($sales_id!="")
				{
					$where=" AND sales_id='".$sales_id."'";
				}
				else
				{
					$where=" AND sales_id=0";
				}
				$order_r=$db->rp_getData("orders","*","customer_id='".$_REQUEST['cid']."' AND status=-1 AND isDelete=0".$where);
				if($order_r)
				{
					$r=array();
					$order_d=mysqli_fetch_assoc($order_r);					
					$total_qty=$db->rp_getValue("order_product_item","SUM(pro_qty)","order_id='".$order_d['id']."' AND  isDelete=0");
					$cartcount=$db->rp_getTotalRecord("order_product_item","order_id='".$order_d['id']."' AND  isDelete=0");

					$ack=array( "ack"=>1,
	                "ack_msg"=>"Total Qty & Cart Count Found!!",
	                "developer_msg"=>"Total Qty & Cart Count Found!!",
	                "total_qty"=>$total_qty,
	                "cartcount"=>$cartcount,
		            );
					$db->printJSON($ack);
				}
				else
				{
					$ack=array( "ack"=>0,
	                "ack_msg"=>"No Order Found!!",
	                "developer_msg"=>"No Order Found!!",
		            );
					$db->printJSON($ack);
				}
			}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"Internal error!!",
                "developer_msg"=>"Service Parameter missing or not valid!!",
	            );
				$db->printJSON($ack);
			}
		}
		else if($service=="download_order_pdf" || $service==98)
		{
			$order_id=isset($_REQUEST['order_id'])?$_REQUEST['order_id']:"";

			if(isset($_REQUEST['order_id']) && $_REQUEST['order_id']!="")
			{
				$order_id	= isset($_REQUEST['order_id'])?$db->clean($_REQUEST['order_id']):"";
				if(!empty($order_id) && $order_id!="")
				{
					$ack=$objSalesExecutive->DownloadOrder($order_id);
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Internal error!!","developer_msg"=>"Service Parameter missing or not valid!!","extra"=>array("requested_params"=>$_REQUEST,"other"=>array()));
				}
				$db->printJSON($ack);
			}
		}	
		else if($service=="get_search_customer" || $service==106)
		{
			$searchName=isset($_REQUEST['searchName'])?$_REQUEST['searchName']:"";
			if($searchName!="")
			{
				$data_r=$db->rp_getData("executive","id,company_name,phone,cname,address","company_name LIKE '%".$searchName."%' AND isDelete=0 AND isActive=1");
			}
			else
			{
				$data_r=$db->rp_getData("executive","id,company_name,phone,cname,address","isDelete=0 AND isActive=1","",0);
			}
			if($data_r)
			{
				$DATA=array();
				while($data_d=mysqli_fetch_assoc($data_r))
				{
					$DATA[]=$data_d;
				}
				$ack=array( "ack"=>1,
                "ack_msg"=>"Customer Found!!",
                "developer_msg"=>"Customer Found!!",
                "result"=>$DATA
	            );
				$db->printJSON($ack);
			}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"No Customer Found!!",
                "developer_msg"=>"No Customer Found!!",
	            );
				$db->printJSON($ack);
			}

		}
		else if ($service == 'get_source_of_inquiry' || $service == 142)
        {
            $source_of_inquiry =array();
            $source_of_inquiry_data = $db->rp_getData("source_of_inquiry","id,name","",0);   
            if($source_of_inquiry_data)
            {
            	while($source_of_inquiry_d = mysqli_fetch_assoc($source_of_inquiry_data)) 
                { 
	                $source_of_inquiry[] = $source_of_inquiry_d;
	            }    
            }   
                
            // }
                
            if(!empty($source_of_inquiry))
            {
                $ack=array("ack"=>1,"ack_msg"=>"Successfully Fetch Source Of Inquiry Detail!!","developer_msg"=>"You got it!!","result"=>$source_of_inquiry);
            }
            else
            {
                $ack=array("ack"=>0,"ack_msg"=>"No Data Available!!","developer_msg"=>"No Data Available!!",);
            }
            $db->printJSON($ack);
        }

		else
		{
			$ack=array( "ack"=>0,
                "ack_msg"=>"Internal error!!",
                "developer_msg"=>"Service Parameter missing or not valid!!",
            );
			$db->printJSON($ack);
		}
		
	}
	else
    {
        $ack=array( "ack"=>0,
                "ack_msg"=>"Internal error!!",
                "developer_msg"=>"Service Parameter missing or not valid!!",
            );
        $db->printJSON($ack);
    }
}
else
{
	$ack=array( "ack"=>0,
				"ack_msg"=>"Internal error!!",
				"developer_msg"=>"Check your API Key or contact Admin",
				"extra"=>array("requested_params"=>$_REQUEST,
								"other"=>array())
			);
	$db->printJSON($ack);
}

function aj_sendOTP($number,$activationCode)
{
	$msgId = "";
	$nt = new Notification();							
	if($number!="")
	{
		$sms=$activationCode." is Your One Time Password!!";
		$msgId=$nt->rp_checkSMS($number,$sms);	
	}
	return array('ack'=>1,'status'=>"msgId".$msgId."&OTP=".$activationCode);
}
function aj_sendSecurityCode($email,$number,$activationCode)
{
	$nt = new Notification();	
	
	$body="Hello User, Someone requested new password for your ".SITENAME." account if its you then enter this security code to application.<br> Security Code:".$activationCode."<br> Thank You,<br> Team ".SITENAME;
	$sms = $activationCode." is your ".SITENAME." security code";
	$email=$nt->aj_sendSecurityCode($email,"Security Check ".SITENAME,$body);
	$msgId="NO";
	if($number!="")
	{
		$msgId=$nt->rp_checkSMS($number,$sms);	
	}
	return array('ack'=>1,'status'=>"msgId".$msgId."&email=".$email);
}
function generateActivationCode()
{
	$characters='0123456789';
	$randStr="";
	for($i=0;$i<=5;$i++)
	{
		$randStr=$randStr.$characters[rand(0,strlen($characters)-1)];
	}
	return $randStr;
}

?>
------WebKitFormBoundary5b6Mnj9DHdSyY1Is
Content-Disposition: form-data; name="overwrite"

0