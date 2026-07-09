<?php 
// Connect to Database
include('connect.php');
require_once('../include/notification.class.php');
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
	
	>>>>>>>>>Services List<<<<<<<<<
	Key			Name
	
	1 	login_customer
	
*/
if($is_valid_api_key)
{	
	if($is_valid_service)
	{
		include('../include/class.customer.php');
		
		$objCustomer=new Customer();
		

//#login For Customer---------------------------------// 
		if($service=='login_customer' || $service==52)
		{
			$detail['email'] 		= $db->clean($_REQUEST['email']);
			$detail['password'] 	= $db->clean($_REQUEST['password']);
			$detail['imei'] 	= $db->clean($_REQUEST['imei']);
			$detail['refreshToken'] 	= $db->clean($_REQUEST['refreshToken']);
			if($detail['email']!="" && $detail['password']!="" && !filter_var($detail['email'], FILTER_VALIDATE_EMAIL) === false && $detail['refreshToken']!="")
			{
				
				$reply=$objCustomer->loginCustomer($detail);
				echo json_encode($reply);
				
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Email or Password not valid ");
				echo json_encode($ack);
			}
		}	
		
		else if($service=="register_customer" || $service==61)
		{
			
				$detail['email'] 		= $db->clean($_REQUEST['email']);
			$detail['password'] 	= md5($db->clean($_REQUEST['password']));
			$detail['name'] 	= $db->clean($_REQUEST['name']);
			$detail['person_name'] 	= $db->clean($_REQUEST['person_name']);
			$detail['mobile'] 	= $db->clean($_REQUEST['mobile']);
			$detail['address'] 	= $db->clean($_REQUEST['address']);
			$detail['locality'] 	= $db->clean($_REQUEST['locality']);
			$detail['pincode'] 	= $db->clean($_REQUEST['pincode']);
			$detail['city'] 	= $db->clean($_REQUEST['city']);
			$detail['state'] 	= $db->clean($_REQUEST['state']);
			$detail['country'] 	= $db->clean($_REQUEST['country']);
			
			if($detail['email']!="" && $detail['password']!="" && !filter_var($detail['email'], FILTER_VALIDATE_EMAIL) === false  && $detail['name']!=" " )	//&& $detail['person_name']!=" "
			{
					
					$reply=$objCustomer->registerCustomer($detail);
					echo json_encode($reply);
				
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Internal Error!! Some Parameters Missing!!");
				echo json_encode($ack);
			}
				
											
		}
		
		else if($service=='forget_password' || $service==62)
		{
			if(isset($_REQUEST['email']))
			{
				$detail['email']		= isset($_REQUEST['email'])?$db->clean($_REQUEST['email']):"";
				$reply=$objCustomer->ForgotPassword($detail);
				$db->printJSON($reply);
				
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>$log->getMessage('PARAMETER_MISSING_SERVICE',1),
					"developer_msg"=>$log->getMessage('PARAMETER_MISSING_SERVICE'),);
				$db->printJSON($ack);
			}	
				
			
			
		}
		else if($service=='change_password' || $service==63)
		{
			if(isset($_REQUEST['email']) && isset($_REQUEST['password']))
			{
				$email=$_REQUEST['email'];
				$password=$_REQUEST['password'];
				$ack=$objCustomer->UserChangeForgetPassword($email,$password);//phone,otp
				$db->printJSON($ack);
			}
			else{
				
				$ack=array( "ack"=>0,
							"ack_msg"=>$log->getMessage('PARAMETER_MISSING_SERVICE',1),
							"developer_msg"=>$log->getMessage('PARAMETER_MISSING_SERVICE'),
						);
				$db->printJSON($ack);
			}
				
		}
		
		else if($service=='update_customer_profile' || $service==64)
		{
			//$detail['email'] 		= $db->clean($_REQUEST['email']);
			$detail['id'] 	= $db->clean($_REQUEST['id']);
			$detail['name'] 	= $db->clean($_REQUEST['name']);
			$detail['person_name'] 	= $db->clean($_REQUEST['person_name']);
			$detail['phone'] 	= $db->clean($_REQUEST['phone']);
			$detail['address1'] 	= $db->clean($_REQUEST['address']);
			$detail['locality'] 	= $db->clean($_REQUEST['locality']);
			$detail['zip'] 	= $db->clean($_REQUEST['zip']);
			$detail['country'] 	= $db->clean($_REQUEST['country']);
			$detail['state'] 	= $db->clean($_REQUEST['state']);
			$detail['city'] 	= $db->clean($_REQUEST['city']);
			
			if($detail['id']!="" && $detail['name']!="" )	//&& $detail['person_name']!=""
			{
					
				$reply=$objCustomer->updateCustomerProfile($detail);
				echo json_encode($reply);
				
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Internal Error!! Some Parameters Missing!!");
				echo json_encode($ack);
			}
		}
		
		else if($service=='customer_change_new_password' || $service==65)
		{

			if(isset($_REQUEST['id']) && isset($_REQUEST['password']) && isset($_REQUEST['new_password']))
				{
					$id 		= isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
					$i=$db->rp_getTotalRecord("customer","id='".$id."'",0);					
					if($i)
					{
						
						$newPassword		= md5(trim($_REQUEST['new_password']))?$db->clean(md5(trim($_REQUEST['new_password']))):"";
						$password 		= isset($_REQUEST['password'])?$db->clean($_REQUEST['password']):"";
						$check=$db->rp_getValue("customer","COUNT(*)","id='".$id."' AND password='".md5($password)."'",0);
						if($check>0)
						{	if($db->aj_updateUserPasswordCustomer($id,$newPassword,$password))
							{
								$ack=array( "ack"=>1,
								"ack_msg"=>"Successfully Updated Your Password!!",
								"developer_msg"=>"You got it!!",
								"result"=>array($check),
								);
								$db->printJSON($ack);
							}
							else
							{
								$ack=array( "ack"=>0,
								"ack_msg"=>"Password Updation Fail!!",
								"developer_msg"=>"please pass correct password!!",
								);
								$db->printJSON($ack);
							}
						}
						else
						{
								$ack=array( "ack"=>0,
								"ack_msg"=>"Password Incorrect please Try Again Later!!",
								"developer_msg"=>"please pass correct password!!",
								);
								$db->printJSON($ack);
						}
					}
					else
					{
								$ack=array( "ack"=>0,
								"ack_msg"=>"User Not Found!!",
								"developer_msg"=>"User Not Found!!",
								);
								$db->printJSON($ack);
					}
				}
				else
				{
							$ack=array( "ack"=>0,
								"ack_msg"=>"Something went wrong!!! User id not found",
								"developer_msg"=>"User Not Found!!",
								);
								$db->printJSON($ack);
				}
			
		}
//----------------------------------------------------------------------------//		
//-------#Get List All Product------------------------------------------------//		
		else if($service=='get_product' || $service==3)
		{
					$p=new Product();
					$uid	=(isset($_REQUEST['uid'])&& $_REQUEST['uid']!="")?$_REQUEST['uid']:"";
					$name=(isset($_REQUEST['name'])&& $_REQUEST['name']!="")?$_REQUEST['name']:"";
					$products=array();
					$product=new Product();
					if($name!=""){
						$where="name LIKE '%".$name."%' AND isDelete=0";
					}
					else
					{$where="isDelete=0";}
					$hproduct_r = $db->rp_getData("product","id",$where,"",0);						
					if($hproduct_r)
					{
						while($hproduct_d = mysqli_fetch_assoc($hproduct_r)){
							//Fetching Only Id then using function getProductDetail get Information of that product
							$pid=$hproduct_d['id'];
							if($pid!="")
							{
							$current_prodcuts=$product->aj_getProductDetail($pid,$uid);
								if(!empty($current_prodcuts))
								{
									$products=array_merge($products,$current_prodcuts);
								}
							
							}
						}
					}
					if(!empty($products))
					{
						
						$ack=array( "ack"=>1,
							"ack_msg"=>"Product List Fetched!!",
							"developer_msg"=>"Product List Fetched!!",
							"result"=>$products,
							);
					
					}
					else
					{
						
						$ack=array( "ack"=>0,
							"ack_msg"=>"Product List not Fetched!!",
							"developer_msg"=>"Product List not Fetched!!"												
						);
					}
					$db->printJSON($ack);
			
		}
//----------------------------------------------------------------------------//
//------#Get List All category if no parma. pass /get all product upon Category id/ get product with Discount calculation ----//		
		else if($service=="get_category_product_discount_detail" || $service==4)
		{
				$cid="";
				$uid			= isset($_REQUEST['uid'])?$db->clean($_REQUEST['uid']):"";
				if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="")
				{
					// If Product id given then get information of product's category
					$pid=$_REQUEST['pid'];
					$w="id='".$pid."' AND isDelete=0";
					$cid = $db->rp_getValue("product","cid",$w,0);					
				}
				else if(isset($_REQUEST['cid']) && $_REQUEST['cid']!="")
				{
					$cid=$_REQUEST['cid'];					
				}
				if($cid!="")
				{
					$where="id='".$cid."'";
				}
				else 
				{
					$where="isDelete=0";
				}	
				$hcat_r = $db->rp_getData("category_master","id,name",$where,0);
				if($hcat_r){
					$category=array();
					while($hcat_d = mysqli_fetch_assoc($hcat_r)){
						$cid=$hcat_d['id'];
							
						if(isset($_REQUEST['p_required']) && $_REQUEST['p_required']==1)
						{
							$products=array();
							
							$product=new Product();
							$hproduct_r = $db->rp_getData("product","id","cid='".$cid."'");						
							if($hproduct_r)
							{
								while($hproduct_d = mysqli_fetch_assoc($hproduct_r)){
									//Fetching Only Id then using function getProductDetail get Information of that product
									$pid=$hproduct_d['id'];
									if($pid!="")
									{
									$current_prodcuts=$product->aj_getProductDetail($pid,$uid);
									if(!empty($current_prodcuts))
									{
										$products=array_merge($products,$current_prodcuts);
									}
									
									}
								}
							}
							$hcat_d['products']=$products;
						}
						array_push($category,$hcat_d);
						
					}
					$ack=array("ack"=>1,"result"=>$category);
					echo json_encode($ack);
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"No Category Found!!!");
					echo json_encode($ack);
				}
			}
//----------------------------------------------------------------------------//
//-----------#Get All Class List#----------------------------------------------//			
		else if($service=='get_class' || $service==5)
		{
			
					$class=new ClassType();
					$c=$class->getClassDetail(array("id","name"));
					if(!empty($c))
					{
						$ack=array( "ack"=>1,
							"ack_msg"=>"Class List Fetched!!",
							"developer_msg"=>"Class List Fetched!!",
							"result"=>$c,
							);
					
					}
					else
					{
						
						$ack=array( "ack"=>0,
							"ack_msg"=>"Class List not Fetched!!",
							"developer_msg"=>"Class List not Fetched!!"												
						);
					}
					$db->printJSON($ack);
			
		}	
//----------------------------------------------------------------------------//
//---------#Get All Area List#-------------------------------------------------//		
		else if($service=='get_area' || $service==6)
		{
			
					$class=new ClassType();
					$area=$class->getAreaDetail(array("id","name","class_id"));
					if(!empty($area))
					{
						$ack=array( "ack"=>1,
							"ack_msg"=>"Area List Fetched!!",
							"developer_msg"=>"Area List Fetched!!",
							"result"=>$area,
							);
					
					}
					else
					{
						
						$ack=array( "ack"=>0,
							"ack_msg"=>"Area List not Fetched!!",
							"developer_msg"=>"Area List not Fetched!!"												
						);
					}
					$db->printJSON($ack);
			
		}
//---------------------------------------------------------------------------//
//----------#Get Area List upon Class Id--------------------------------------//	
		else if($service=='get_area_usingclass_id' || $service==7)
		{		
					if(isset($_REQUEST['class_id']) && $_REQUEST['class_id']!="")
					{
						$class_id=$_REQUEST['class_id'];		
						$class=new ClassType();
						$area=$class->getAreaDetail_usingClassId($class_id);
						$ack=array( "ack"=>1,
									"ack_msg"=>"Area Fetched Successfully  !!",
									"developer_msg"=>"You got it!!",
									"result"=>$area,
									);
									$db->printJSON($ack);
					}
					else
					{
							$ack=array( "ack"=>0,
								"ack_msg"=>"Area Not Fetched !!",
								"developer_msg"=>"Wrong email or password",
								"result"=>$area,
								);
						$db->printJSON($ack);
					}	
			}
//----------------------------------------------------------------------------//
//---------#Get All Outlets List#---------------------------------------------//	
			else if($service=='get_outlet_list' || $service==8)
			{		
					if(isset($_REQUEST['sales_executive_id']))
					{
						$executive=new Executive();
						$sales_executive_id=isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";
						$outlets=$executive->getOutletList(array("id","dealer_distributor_id","super_stockist_id","type_of_executive","company_type","company_name","cname","password","email","cst","pan","gst","vat","excise","phone","address","zip","country","state","city","class_id","discount"),$sales_executive_id);
						if(!empty($outlets))
						{
							$ack=array( "ack"=>1,
								"ack_msg"=>"Outlets List Fetched!!",
								"developer_msg"=>"Outlets List Fetched!!",
								"result"=>$outlets,
								);
						
						}
						else
						{
							
							$ack=array( "ack"=>0,
								"ack_msg"=>"Outlets List not Fetched!!",
								"developer_msg"=>"Outlets List not Fetched!!"												
							);
						}
					}
					else
					{
						$ack=array( "ack"=>0,
							"ack_msg"=>"Internal error!!",
							"developer_msg"=>"Service Parameter missing or not valid!!"
							);
					}
					$db->printJSON($ack);
			}
//----------------------------------------------------------------------------//
//---------#Get All Orders List------------------------------------------------//			
			else if($service=='get_orders' || $service==9)
		   {
			   if(isset($_REQUEST['sales_id']) && isset($_REQUEST['customer_type']))
				{
					$sales_id=$_REQUEST['sales_id'];
					$customer_type=$_REQUEST['customer_type'];
					$get_orders = $p->getOrders($sales_id,$customer_type);
					$db->printJSON($get_orders);
				}
				else
				{
				$ack=array( "ack"=>0,
				"ack_msg"=>"Internal error!!",
				"developer_msg"=>"Service Parameter missing or not valid!!"
				);
				$db->printJSON($ack);
				}
			}
			else if($service=='get_no_order_inquiry' || $service==47)
		   {
			   if(isset($_REQUEST['sales_id']))
				{
					$sales_id=$_REQUEST['sales_id'];
					$get_orders = $p->getNoOrderInquiry($sales_id);
					$db->printJSON($get_orders);
				}
				else
				{
				$ack=array( "ack"=>0,
				"ack_msg"=>"Internal error!!",
				"developer_msg"=>"Service Parameter missing or not valid!!"
				);
				$db->printJSON($ack);
				}
			}
//----------------------------------------------------------------------------//
//---------#Create Order With require Detail(create order for multiple product)----------------------------------//			
		else if($service=='add_order_detail' || $service==10)
		{
		
				if(isset($_REQUEST['total_qty']) && isset($_REQUEST['total_amount']) && isset($_REQUEST['discount']) && isset($_REQUEST['discount_type']) && isset($_REQUEST['grand_total']) && isset($_REQUEST['customer_id']) && isset($_REQUEST['sales_id']) && isset($_REQUEST['product']))
				{
						
						$final_total ="";
						$total_qty			= isset($_REQUEST['total_qty'])?$db->clean($_REQUEST['total_qty']):"";
						$total_amount		= isset($_REQUEST['total_amount'])?$db->clean($_REQUEST['total_amount']):"";
						$discount			= isset($_REQUEST['discount'])?$db->clean($_REQUEST['discount']):"";
						$discount_type			= isset($_REQUEST['discount_type'])?$db->clean($_REQUEST['discount_type']):"";
						$grand_total		= isset($_REQUEST['grand_total'])?$db->clean($_REQUEST['grand_total']):"";
						$customer_id		= isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
						$sales_id		= isset($_REQUEST['sales_id'])?$db->clean($_REQUEST['sales_id']):"";
						$product 	= (isset($_REQUEST['product']) && $_REQUEST['product']!="")?json_decode($_REQUEST['product']):array();
						$sales_type=$db->rp_getValue("sales_executive","type","id=".$sales_id."");
						$detail=$db->rp_getData("executive","*","id=".$customer_id."","",0);
						$data=mysqli_fetch_assoc($detail);
							$customer_name=$data['cname'];
							$customer_type=$data['type_of_executive'];
							$contact_number=$data['phone'];
							$address=$data['address'];
							$city=$data['city'];
							$state=$data['state'];
							$country=$data['country'];
							$email=$data['email'];
						$order_date	= date("Y-m-d");
						$modify_date	= date("Y-m-d H:i:s");
						$cdrow 	= array(
								"total_qty",
								"total_amount",
								"discount",
								"discount_type",
								"grand_total",
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
							);
						$cdvalue = array(
								$total_qty,
								$total_amount,
								$discount,
								$discount_type,
								$grand_total,
								$customer_id,
								$sales_id,
								$sales_type,
								$customer_name,
								$customer_type,
								$contact_number,
								$address,
								$city,
								$email,
								$state,
								$country,
								$order_date,
								$modify_date,
							);
							
						$cart_id = $db->rp_insert("orders",$cdvalue,$cdrow,0);
						$row = array("order_no"=>OUTLETS_ORDER_NO.str_pad($cart_id, 3, '0', STR_PAD_LEFT));
						$update_order_no = $db->rp_update("orders",$row,"id='".$cart_id."'");
						$adate = date('Y-m-d H:i:s');
						
						foreach($product as  $p)
						{
							//product=[{"name":"product1","id":"33","price":"1325","qty":"50"}]
							$totalprice= "";
							$pro_name= $p->name;
							$pro_id= $p->id;
							$weight_id=$p->weight_id;
							$unitprice= $p->price;
							$qty=$p->qty;
							$totalprice= $db->rp_num($unitprice*$qty);
							$final_total += $totalprice;
							
							$row = array(
								"order_id",
								"pro_id",
								"weight_id",
								"pro_name",
								"unitprice",
								"pro_qty",
								"remaining_qty",
								"totalprice",
								"adate",
								"modify_date",
							);
							$value = array(
								$cart_id,
								$pro_id,
								$weight_id,
								$pro_name,
								$unitprice,
								$qty,
								$qty,
								$totalprice,
								$adate,
								$modify_date,
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
							$ack=array( "ack"=>1,
								"ack_msg"=>"Order Add Successfully!!",
								"developer_msg"=>"You got it!!",
								"result"=>$order_pro_detail,
								);
							$db->printJSON($ack);
							
				}
				else
				{
						$ack=array( "ack"=>0,
					"ack_msg"=>"Sorry Not add Product Details !! Please Try Again Later!!",
					"developer_msg"=>"not inserted!!",
					);
					$db->printJSON($ack);
				}
		}
//-----------------------------------------------------------------------------------------//
//-----------#Update Order Detail(also update Product)----------------------------------------------------//		
		else if($service=='update_order_detail' || $service==11)
		{
			//var_dump($_REQUEST);
				if(isset($_REQUEST['id']) && isset($_REQUEST['total_qty']) && isset($_REQUEST['total_amount']) && isset($_REQUEST['discount']) && isset($_REQUEST['discount_type']) && isset($_REQUEST['grand_total']) && isset($_REQUEST['customer_id'])&& isset($_REQUEST['product']))
				{
						$final_total ="";
						$id					= isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
						$total_qty			= isset($_REQUEST['total_qty'])?$db->clean($_REQUEST['total_qty']):"";
						$total_amount		= isset($_REQUEST['total_amount'])?$db->clean($_REQUEST['total_amount']):"";
						$discount			= isset($_REQUEST['discount'])?$db->clean($_REQUEST['discount']):"";
						$discount_type			= isset($_REQUEST['discount_type'])?$db->clean($_REQUEST['discount_type']):"";
						$grand_total		= isset($_REQUEST['grand_total'])?$db->clean($_REQUEST['grand_total']):"";
						$customer_id		= isset($_REQUEST['customer_id'])?$db->clean($_REQUEST['customer_id']):"";
						$product	= json_decode($_REQUEST['product']);
						$detail=$db->rp_getData("executive","*","id=".$customer_id."","",0);
						$data=mysqli_fetch_assoc($detail);
							$customer_name=$data['cname'];
							$customer_type=$data['type_of_executive'];
							$contact_number=$data['phone'];
							$address=$data['address'];
							$city=$data['city'];
							$state=$data['state'];
							$country=$data['country'];
							$email=$data['email'];
						$order_date	= date("Y-m-d");
						$modify_date	= date("Y-m-d");
						
						$cdrow 	= array(
								"total_qty" => $total_qty,
								"total_amount"=>  $total_amount,
								"discount" => $discount,
								"discount_type" => $discount_type,
								"grand_total" => $grand_total,
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
								"modify_date" =>  $modify_date,
							);
						
						$cart_id = $db->rp_update("orders",$cdrow,"id='".$id."'");
						
						$adate	= date("Y-m-d H:i:s");
						//checking for updating qty is not greter than dispatched qty//
						$order_id=$id;
						$error=array();
						$isError=false;
						foreach($product as  $p)
						{
							$pro_id     = $p->id;
							$new_order_qty 		=  $p->qty;
							
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
										$error[]=array("error_target_id"=>$pro_id,"error"=>$product_name." has dispatched qty more than your edited qty");
										
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
							$totalprice = "";
							$pro_name   = $p->name;
							$pro_id     = $p->id;
							$weight_id    = $p->weight_id;
							$unitprice  = $p->price;
							$qty =  $p->qty;
							
							$totalprice= $db->rp_num($unitprice*$qty);
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
							$row = array(
								"order_id",
								"pro_id",
								"weight_id",
								"pro_name",
								"unitprice",
								"pro_qty",
								"remaining_qty",
								"dispatched_qty",
								"totalprice",
								"adate",
								"modify_date",
							);
							$value = array(
								$id,
								$pro_id,
								$weight_id,
								$pro_name,
								$unitprice,
								$qty,
								$remaining_qty,
								$dispatch_d['dispatched_qty'],
								$totalprice,
								$adate,
								$modify_date,
							);
						
							$ins = $db->rp_insert("order_product_item",$value,$row,0);
						}
						
							$order_pro_detail=mysqli_fetch_assoc($db->rp_getData("orders","*","id='".$id."' AND isDelete=0"));
							
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
							
							$order_pro_detail['product']=$r;
							
							$ack=array( "ack"=>1,
								"ack_msg"=>"Success! Product details Updated!!",
								"developer_msg"=>"You got it!!",
								"result"=>$order_pro_detail,
								);
							$db->printJSON($ack);
					}
					else
					{
						$ack=array( "ack"=>0,"ack_msg"=>"Sorry Please Check Error Log ","developer_msg"=>"You got it!!","result"=>$error);
					$db->printJSON($ack);
					}
				}
				else
				{
					$ack=array( "ack"=>0,"ack_msg"=>"Sorry! not add Product Details !! Please Try Again Later!!","developer_msg"=>"not inserted!!");
					$db->printJSON($ack);
				}		
				
		}
		else if($service=='get_customer' || $service==12)
		{
			if(isset($_REQUEST['sales_executive_id']))
			{
				
				$sales_executive_id					= isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";
				$executive=new Executive();
				$get_customer = $executive->getCustomer($sales_executive_id);
				if($get_customer)
				{
					$db->printJSON($get_customer);
				}
				else
				{
					$ack=array( "ack"=>0,
					"ack_msg"=>"Internal error!!",
					"developer_msg"=>"Service Parameter missing or not valid!!"
					);
					$db->printJSON($ack);
				}
				
			}
			else
			{
				$ack=array( "ack"=>0,
								"ack_msg"=>"Service Parameter missing!!",
								"developer_msg"=>"Parameter missing!!"
								);
								$db->printJSON($ack);
			}
			
		}
		else if($service=='sales_executive_change_password' || $service==13)
		{

			if(isset($_REQUEST['id']) && isset($_REQUEST['password']) && isset($_REQUEST['new_password']))
				{
					$id 		= isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
					$i=$db->rp_getTotalRecord("sales_executive","id='".$id."'",0);					
					if($i)
					{
						
						$newPassword		= md5(trim($_REQUEST['new_password']))?$db->clean(md5(trim($_REQUEST['new_password']))):"";
						$password 		= isset($_REQUEST['password'])?$db->clean($_REQUEST['password']):"";
						$check=$db->rp_getValue("sales_executive","COUNT(*)","id='".$id."' AND password='".md5($password)."'",0);
						if($check>0)
						{	if($db->aj_updateUserPassword($id,$newPassword,$password))
							{
								$ack=array( "ack"=>1,
								"ack_msg"=>"Successfully Updated Your Password!!",
								"developer_msg"=>"You got it!!",
								"result"=>array($check),
								);
								$db->printJSON($ack);
							}
							else
							{
								$ack=array( "ack"=>0,
								"ack_msg"=>"Password Updation Fail!!",
								"developer_msg"=>"please pass correct password!!",
								);
								$db->printJSON($ack);
							}
						}
						else
						{
								$ack=array( "ack"=>0,
								"ack_msg"=>"Password Incorrect please Try Again Later!!",
								"developer_msg"=>"please pass correct password!!",
								);
								$db->printJSON($ack);
						}
					}
					else
					{
								$ack=array( "ack"=>0,
								"ack_msg"=>"User Not Found!!",
								"developer_msg"=>"User Not Found!!",
								);
								$db->printJSON($ack);
					}
				}
				else
				{
							$ack=array( "ack"=>0,
								"ack_msg"=>"Something went wrong!!! User id not found",
								"developer_msg"=>"User Not Found!!",
								);
								$db->printJSON($ack);
				}
			
		}
		else if($service=='get_orders_item' || $service==14)
		{
			if(isset($_REQUEST['order_id']))
			{
				$order_id	= isset($_REQUEST['order_id'])?$db->clean($_REQUEST['order_id']):"";
				$get_orders = $p->getOrders_forItem($order_id);
				$db->printJSON($get_orders);
			}
		}
/*-------------------------Forgot Password--------------------------------------*/	
		else if($service=='sales_executive_forgot_password' || $service==15)
		{
				$username 	= isset($_REQUEST['username'])?$db->clean($_REQUEST['username']):"";	
				$check=$db->rp_getValue("sales_executive","COUNT(*)","username='".$username."'");
				if($check>0)
				{
					$number=$db->rp_getValue("sales_executive","phone","username='".$username."'",0);
					$email=$db->rp_getValue("sales_executive","email","username='".$username."'",0);
					$activationCode=generateActivationCode();
					$rows=array("otp"=>$activationCode);
					$where=" email='".$email."'";		
					$db->rp_update("sales_executive",$rows,$where,0);
					
					$ack=aj_sendSecurityCode($email,$number,$activationCode);
					if($ack['ack']==1)
					{
						$ack=array( "ack"=>1,
								"ack_msg"=>"Check Your Mail For Security Code!!",
								"developer_msg"=>"You got it!!",
								"result"=>array($check),
								);
								$db->printJSON($ack);
					}
					else	
					{
						$ack=array( "ack"=>0,
								"ack_msg"=>"Sorry We Can't Proceed Right Now Try Later!!",
								"developer_msg"=>"Sorry We can't Procced!",
								);
								$db->printJSON($ack);
					}
					
					
				}
				else
				{
						$ack=array( "ack"=>0,
								"ack_msg"=>"Given Email Not Exists!!",
								"developer_msg"=>"Email Not Exists!! Enter Another Email",
								);
								$db->printJSON($ack);
				}
			
			
		}
		//------change password with new password---------//
		else if($service=='sales_executive_change_forget_password' || $service==16)
		{
				$id 	= $db->rp_getValue("sales_executive","id","username='".$db->clean($_REQUEST['username'])."'",0);	
				$newPassword		= md5(trim($_REQUEST['password']))?$db->clean(md5(trim($_REQUEST['password']))):"";				
				if($db->aj_updateUserPassword($id,$newPassword,""))
				{
					$ack=array( "ack"=>1,
								"ack_msg"=>"Password Updated!!",
								"developer_msg"=>"You got it!!",
								"result"=>array(),
								);
								$db->printJSON($ack);
				}
				else
				{
					$ack=array("ack"=>0,
							"ack_msg"=>"Password updation failed!!",
							"developer_msg"=>"password updation failed!!",
							"result"=>array(),
					);
					$db->printJSON($ack);
				}
			
		}
		//----Check security- send otp and confirm //
		else if($service=='sales_executive_check_security' || $service==17)
		{
			if(isset($_REQUEST['username']) && isset($_REQUEST['otp']))
			{
				
				$username 	= isset($_REQUEST['username'])?$db->clean($_REQUEST['username']):"";	
				$otp 	= isset($_REQUEST['otp'])?$db->clean($_REQUEST['otp']):"";	
				$check  = $db->rp_getData("sales_executive","*","username='".$username."' AND otp='".$otp."' AND isDelete=0");
				if($check>0)
				{
					
					$ack=array( "ack"=>1,
							"ack_msg"=>"Successfully Match!!",
							"developer_msg"=>"You got it!!",
							"result"=>array(mysqli_fetch_assoc($check)),
							);
							$db->printJSON($ack);
				}
				else	
				{
					$ack=array( "ack"=>0,
							"ack_msg"=>"Sorry We Can't Proceed Right Now Try Later!!",
							"developer_msg"=>"Sorry We can't Procced!",
							);
							$db->printJSON($ack);
				}
					
			}	
			else		
			{
				$ack=array( "ack"=>1,
					"ack_msg"=>"Internal error!!",
					"developer_msg"=>"Service Parameter missing or not valid!!",
					"extra"=>array("requested_params"=>$_REQUEST,
									"other"=>array()),
				);
				$db->printJSON($ack);
			}
			
		}
/*-----------------------------------------------------------------------------------*/		
/*--------------------Sales Officer Tracking-------------------------------------*/		
		else if($service=='sales_executive_tracking' || $service==18)
		{
			if(isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['longitude']) && isset($_REQUEST['latitude']))
					{
						$sales_executive_id			= isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";
						$longitude			= isset($_REQUEST['longitude'])?$db->clean($_REQUEST['longitude']):"";
						$latitude			= isset($_REQUEST['latitude'])?$db->clean($_REQUEST['latitude']):"";
						$type			= isset($_REQUEST['type'])?$db->clean($_REQUEST['type']):"";
						
						$date	= date("Y-m-d H:i:s");
						$cdrow 	= array(
								"sales_executive_id",
								"longitude",
								"latitude",
								"date",
								"type",
							);
						$cdvalue = array(
								$sales_executive_id,
								$longitude,
								$latitude,
								$date,
								$type,
							);
							
						$e_id = $db->rp_insert("salesexecutive_tracking",$cdvalue,$cdrow,0);
						$data=$db->rp_getData("salesexecutive_tracking","*","id=".$e_id ."");
							
							if($e_id!=0)
							{
								$ack=array( "ack"=>1,
								"ack_msg"=>"Tracking Detail Add Successfully!!",
								"developer_msg"=>"You got it!!",
								"result"=>array(mysqli_fetch_assoc($data))
								);
							$db->printJSON($ack);
							
							}
					else
					{
							$ack=array( "ack"=>0,
						"ack_msg"=>"Sorry Not add Tracking Details !! Please Try Again Later!!",
						"developer_msg"=>"not inserted!!",
						);
						$db->printJSON($ack);
					}
		}
		}
//---------------------------------------------------------------------------------------//		
			else if($service=='get_report' || $service==19)
		   {
			   if(isset($_REQUEST['sales_id']) && isset($_REQUEST['from_date']) && isset($_REQUEST['to_date']))
				{
					$sales_id=$_REQUEST['sales_id'];
					$from_date=$_REQUEST['from_date'];
					$to_date=$_REQUEST['to_date'];
					$customer_type=$_REQUEST['customer_type'];
					$get_detail = $objSalesExecutive->getsalesDetail($sales_id,$from_date,$to_date,$customer_type);
					$db->printJSON($get_detail);
				}
				else
				{
				$ack=array( "ack"=>0,
				"ack_msg"=>"Internal error!!",
				"developer_msg"=>"Service Parameter missing or not valid!!"
				);
				$db->printJSON($ack);
				}
			}
			else if($service=='get_inquiry_report' || $service==46)
		   {
			   if(isset($_REQUEST['sales_id']) && isset($_REQUEST['from_date']) && isset($_REQUEST['to_date']))
				{
					$sales_id=$_REQUEST['sales_id'];
					$from_date=$_REQUEST['from_date'];
					$to_date=$_REQUEST['to_date'];
					$get_detail = $objSalesExecutive->getInquiryReport($sales_id,$from_date,$to_date);
					$db->printJSON($get_detail);
				}
				else
				{
				$ack=array( "ack"=>0,
				"ack_msg"=>"Internal error!!",
				"developer_msg"=>"Service Parameter missing or not valid!!"
				);
				$db->printJSON($ack);
				}
			}
			else if($service=="add_attendance" || $service==20)
			{
				$type=isset($_REQUEST['type'])?$db->clean($_REQUEST['type']):"";
				$sales_id	= isset($_REQUEST['sales_id'])?$db->clean($_REQUEST['sales_id']):"";
				$imei	= isset($_REQUEST['imei'])?$db->clean($_REQUEST['imei']):"";
				$date_time=date('Y-m-d H:i');
				if($imei!="" && $sales_id!="" && $type!="")
				{
					// Last Day Attendance Missing?
					$sales_executive_valid_out_time=$db->rp_getValue("sales_executive","executive_out","id='".$sales_id."'");
					$status_last_date=$db->rp_getTotalRecord("attendance","sales_id='".$sales_id."' AND DATE(date_time)='".date("Y-m-d",strtotime("-1 days"))."' AND inout_status='out'");
					if($status_last_date%2!=0)
					{
						$row 	= array(
						"sales_id",
						"imei",
						"date_time",						
						"inout_status",						
							);
							$last_day_out_time=date("Y-m-d",strtotime("-1 days"));		
							$last_day_out_time=date("Y-m-d H:i:s",strtotime($last_day_out_time." ".$sales_executive_valid_out_time));		
							
						$value = array(
								$sales_id,
								$imei,
								$last_day_out_time,			
								"Out",					
									);
							$attendance_id = $db->rp_insert("attendance",$value,$row,0);
							
					}
					
					// type in then check whether it is valid in or not
					if($type=="In")
					{
						$sales_executive_valid_in_min_time=$db->rp_getValue("sales_executive","executive_in_min","id='".$sales_id."'");
						$sales_executive_valid_in_max_time=$db->rp_getValue("sales_executive","executive_in_max","id='".$sales_id."'");
						
						$sales_executive_valid_in_min_time=date("Y-m-d H:i",strtotime($sales_executive_valid_in_min_time));
						$sales_executive_valid_in_max_time=date("Y-m-d H:i",strtotime($sales_executive_valid_in_max_time));
						
						if($date_time>=$sales_executive_valid_in_min_time && $date_time<=$sales_executive_valid_in_max_time)
						{
							$row 	= array(
							"sales_id",
							"imei",
							"date_time",						
							"inout_status",						
								);
						$value = array(
								$sales_id,
								$imei,
								$date_time,					
								"In",					
									);
							$attendance_id = $db->rp_insert("attendance",$value,$row,0);
							if($attendance_id){
								$ack=array( "ack"=>1,"ack_msg"=>"Welcome!! \n Attendance successfully submitted  !!","developer_msg"=>"attendance insert sucessfully!!");
								$db->printJSON($ack);
							}
							else
							{
								$ack=array( "ack"=>0,"ack_msg"=>"attendance insert failed !! Please Try Again Later!!","developer_msg"=>"not inserted!!");
								$db->printJSON($ack);
							}
						}
						else
						{
							$ack=array( "ack"=>0,"ack_msg"=>"You are late please contact administrator".$sales_executive_valid_in_min_time." ".$sales_executive_valid_in_max_time." ".$date_time,"developer_msg"=>"not inserted!!");
							$db->printJSON($ack);
						}
					}
					else if($type=="Out")
					{
						$inout='Out';
						$row 	= array(
							"sales_id",
							"imei",
							"date_time",						
							"inout_status",						
								);
						$value = array(
								$sales_id,
								$imei,
								$date_time,					
								"Out",					
									);
							$attendance_id = $db->rp_insert("attendance",$value,$row,0);
							if($attendance_id){
								$ack=array( "ack"=>1,"ack_msg"=>"Thank you!! Attendance submitted successfully","developer_msg"=>"attendance insert sucessfully!!");
								$db->printJSON($ack);
							}
							else
							{
								$ack=array( "ack"=>0,"ack_msg"=>"attendance insert failed !! Please Try Again Later!!","developer_msg"=>"not inserted!!");
								$db->printJSON($ack);
							}
						
					}
					else
					{
						$ack=array( "ack"=>0,"ack_msg"=>"Something went wrong !! Please Try Again Later!!","developer_msg"=>"IMEI, SALES ID and TYPE REQUIRED!!");
						$db->printJSON($ack);
					}
					
				
				}
				else
				{
					$ack=array( "ack"=>0,"ack_msg"=>"Something went wrong !! Please Try Again Later!!","developer_msg"=>"IMEI, SALES ID and TYPE REQUIRED!!");
					$db->printJSON($ack);
				}
			}
			
			else if($service=="add_expense" || $service==21)
			{
					if(isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['expense_date']))
					{
					$detail['sales_executive_id']	= isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";
					$detail['expense_date']	= isset($_REQUEST['expense_date'])?$db->clean($_REQUEST['expense_date']):"";
					$detail['DA']	= isset($_REQUEST['DA'])?$db->clean($_REQUEST['DA']):"";
					$detail['TA']	= isset($_REQUEST['TA'])?$db->clean($_REQUEST['TA']):"";
					$detail['MOA']	= isset($_REQUEST['MOA'])?$db->clean($_REQUEST['MOA']):"";
					$detail['NA']	= isset($_REQUEST['NA'])?$db->clean($_REQUEST['NA']):"";
					$detail['extra']	= isset($_REQUEST['extra'])?$db->clean($_REQUEST['extra']):"";
					$detail['total']	= isset($_REQUEST['total'])?$db->clean($_REQUEST['total']):"";
					$detail['remark']	= isset($_REQUEST['remark'])?$db->clean($_REQUEST['remark']):"";
					$detail['$created_date']=date('Y-m-d H:s:i');
						$reply=$objExpense->InsertExpense_service($detail);
						if($reply['ack']==1)
						{
							$result=$db->rp_getData("expense","sales_executive_id,expense_date,DA,TA,MOA,NA,extra,total,created_date","id='".$reply['inserted_id']."'","",0);
							$r=mysqli_fetch_assoc($result);
							$r['username']=$db->rp_getValue("sales_executive","username","id='".$r['sales_executive_id']."'",0);
							$r['expense_date']=date('d-m-Y',strtotime($r['expense_date']));
							$r['created_date']=date('d-m-Y H:i:s',strtotime($r['created_date'])); 
							$ack=array( "ack"=>1,
										"ack_msg"=>"Expense Detail Add Successfully!!",
										"developer_msg"=>"You got it!!",
										"result"=>$r,
										);
							$db->printJSON($ack);
						}
						else
						{
							
							$db->printJSON($reply);
						}
						
				}
						
				else{
					$ack=array( "ack"=>0,"ack_msg"=>"Something wrong !! Please Try Again Later!!","developer_msg"=>"not inserted!! sales id not get!!");
					$db->printJSON($ack);
				}
			}
			else if($service=='get_expense' || $service==22)
			{
					$p=new Product();
					if($sales_executive_id	=(isset($_REQUEST['sales_executive_id'])&& $_REQUEST['sales_executive_id']!="")?$_REQUEST['sales_executive_id']:"")
					{
						$ctable_where .= "sales_executive_id='".$sales_executive_id."' AND isDelete=0 AND isActive=1 ";
						if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
						{
						  $ctable_where .= " AND expense_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
						}

						 if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
						{
							 $ctable_where .= " AND expense_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
						}
						if(($_REQUEST['ToDate']=="") && ($_REQUEST['FromDate']==""))
						{
							$month=date("m");
							$ctable_where .= "AND MONTH(expense_date) = '".$month."'";
						}
					$expense_r = $db->rp_getData("expense","*",$ctable_where,"expense_date DESC",0);						
					if($expense_r)
					{
						while($expense_d = mysqli_fetch_assoc($expense_r)){
							//Fetching Only Id then using function getProductDetail get Information of that product
							$expense_d['sales_name']=$db->rp_getValue("sales_executive","username","id='".$expense_d['sales_executive_id']."'",0);
						   $expense_d['expense_date']=date('d-m-Y',strtotime($expense_d['expense_date']));
						   $grand_total+=$expense_d['total'];
						   $result[] = $expense_d;
						}
						
					}
					if(!empty($result))
					{
						$ack=array( "ack"=>1,
							"ack_msg"=>"Expenses History Found!!",
							"developer_msg"=>"Expense List Fetched!!",
							"Grand_total"=>$grand_total,
							"result"=>$result,
							);
					}
					else
					{
						$ack=array( "ack"=>0,
							"ack_msg"=>"No Expense History Found!!",
							"developer_msg"=>"Expense List not Fetched!!"												
						);
					}
					$db->printJSON($ack);
			}
		}
		else if($service=='delete_order_item' || $service==23)
		{
			if(isset($_REQUEST['id']) && isset($_REQUEST['customer_id']) && isset($_REQUEST['sales_id']) && isset($_REQUEST['product']))
			{
				$final_total = "";
				$id			 = isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
				$sales_id	 = isset($_REQUEST['sales_id'])?$db->clean($_REQUEST['sales_id']):"";
				$adate		 = date("Y-m-d H:i:s");		
				$order_id=$id;
				$error=array();
				$isError=false;
				$product	 = json_decode($_REQUEST['product']);
				foreach($product as  $p)
				{
					$pro_id     = $p->id;
					$wid     = $p->weight_id;
					//CHECK ORDER UPDATE VALID OR NOT
					
					$ordered_item_info=$db->rp_getData("order_product_item","*","pro_id='".$pro_id."' AND order_id='".$order_id."' and weight_id='".$wid."'","",0);
					if($ordered_item_info)
					{
						$ordered_item_info=mysqli_fetch_assoc($ordered_item_info);
						$product_name=$ordered_item_info['pro_name'];
						$dispatched_qty=$ordered_item_info['dispatched_qty'];
						$remaining_qty=$ordered_item_info['remaining_qty'];
						$ordered_qty=$ordered_item_info['pro_qty'];
						//check new order qty > old order qty
						if($dispatched_qty>0)
						{
							$isError=true;
							// ERROR YOU CAN NOT ENTER NEW ORDER QTY MORE THEN IT DISPATCHED
							$error[]=array("error_target_id"=>$pro_id,"error"=>$product_name." has dispatched qty more than your edited qty");
						}
					}
					
				}
				
				
				if(!$isError)
				{
					//$order_pro_detail['product']=$r;				
					$ack=array( "ack"=>1,"ack_msg"=>"Success! Order Item delete Sucessfully!!","developer_msg"=>"Success!! Order Item deleted Successfully");
					$db->printJSON($ack);
				}
				else
				{
					$ack=array( "ack"=>0,"ack_msg"=>"Order Item can not deleted.","developer_msg"=>"Order Item can not deleted!!","result"=>$error);
					$db->printJSON($ack);
				}
		}else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Sorry! Order Delete Failed !! Please Try Again Later!!","developer_msg"=>"not deleted!!");
				$db->printJSON($ack);
			}
			
		}
		else if($service=='get_all_product_list' || $service==24)
		{
					$where="isDelete=0";
					$product_r = $db->rp_getData("product","*",$where,"",0);						
					if($product_r)
					{
						$products=array();
						while($product_d = mysqli_fetch_assoc($product_r)){
							
							$products[]=$product_d;
						}
					}
					if(!empty($products))
					{
						
						$ack=array( "ack"=>1,
							"ack_msg"=>"Product List Fetched!!",
							"developer_msg"=>"Product List Fetched!!",
							"result"=>$products,
							);
					}
					else
					{
						$ack=array( "ack"=>0,
							"ack_msg"=>"Product List not Fetched!!",
							"developer_msg"=>"Product List not Fetched!!"												
						);
					}
					$db->printJSON($ack);
			
		}
		else if($service=='get_weight_list' || $service==25)
		{
					$where="isDelete=0";
					$weight_r = $db->rp_getData("weight","*",$where,"",0);						
					if($weight_r)
					{
						$weight=array();
						while($weight_d = mysqli_fetch_assoc($weight_r)){
							
							$weight[]=$weight_d;
						}
					}
					if(!empty($weight))
					{
						
						$ack=array( "ack"=>1,
							"ack_msg"=>"weight List Fetched!!",
							"developer_msg"=>"weight List Fetched!!",
							"result"=>$weight,
							);
					}
					else
					{
						
						$ack=array( "ack"=>0,
							"ack_msg"=>"weight List not Fetched!!",
							"developer_msg"=>"weight List not Fetched!!"												
						);
					}
					$db->printJSON($ack);
			
		}
		else if($service=='get_product_weight_price' || $service==26)
		{
					
					$product_weight_price_r = $db->rp_getData("product_weight_price","*","1=1","",0);						
					if($product_weight_price_r)
					{
						$product_weight_price=array();
						while($product_weight_price_d = mysqli_fetch_assoc($product_weight_price_r)){
							
							$product_weight_price[]=$product_weight_price_d;
						}
					}
					if(!empty($product_weight_price))
					{
						
						$ack=array( "ack"=>1,
							"ack_msg"=>"product weight price List Fetched!!",
							"developer_msg"=>"product weight price List Fetched!!",
							"result"=>$product_weight_price,
							);
					}
					else
					{
						
						$ack=array( "ack"=>0,
							"ack_msg"=>"product weight price List not Fetched!!",
							"developer_msg"=>"product weight price List not Fetched!!"												
						);
					}
					$db->printJSON($ack);
			
		}
		else if($service=='add_sales_executive_tracking' || $service==32)
		{
					$body= file_get_contents('php://input');
					$ins=false;
			        if(isset($_REQUEST['sales_id']) && $body!="")
					{
						$sales_id		= isset($_REQUEST['sales_id'])?$db->clean($_REQUEST['sales_id']):"";
						$tracking 	= ($body!="")?(array)json_decode($body,true):array();
						$adate = date('Y-m-d H:i:s');
						//print_r($tracking);
						for($i=0;$i<sizeof($tracking['values']);$i++)
						{
							$t=$tracking['values'][$i];
							//tracking=[{"date":"2017-05-02 15:00","lat":"12.021","long":"12.021"}]
							$d= $t['nameValuePairs']['date'];
							$lat= $t['nameValuePairs']['lat'];
							$long=$t['nameValuePairs']['long'];
							$type=$t['nameValuePairs']['type'];
							$date=date('Y-m-d H:i:s',strtotime($d));
							$row = array(
								"sales_executive_id",
								"latitude",
								"longitude",
								"date",
								"type",
							);
							$value = array(
								$sales_id,
								$lat,
								$long,
								$date,
								$type,
							);
							
							$ins = $db->rp_insert("salesexecutive_tracking",$value,$row,0);
						
						}
							if($ins)
							{
							$where = "sales_executive_id='".$_REQUEST['sales_id']."' AND isDelete=0";
							$dt = $db->rp_getData("salesexecutive_tracking","*",$where);
							$r = array();
							if($dt)
							{
								while($row=mysqli_fetch_assoc($dt))
								{
									$row['date']=date('d-m-Y H:i:s',strtotime($row['date']));
									$row['created_date']=date('d-m-Y H:i:s',strtotime($row['created_date']));
									$r[]= $row;
								}
							}
							$ack=array( "ack"=>1,
								"ack_msg"=>"Sales Officer Tracking Add Successfully!!",
								"developer_msg"=>"You got it!!",
								"result"=>$r,
								);
							$db->printJSON($ack);
							}
					}
					else
					{
						$ack=array( "ack"=>0,
						"ack_msg"=>"Sorry Not add Tracking Details !! Please Try Again Later!!",
						"developer_msg"=>"not inserted!!",
						);
						$db->printJSON($ack);
					}
		}
		else if($service=='get_notification' || $service==33)
		{
			
			$user_id=$db->getRequestedParam("user_id");
			$system = new System();
			$get_notifications= $system->getNotifications($user_id);
			if($get_notifications)
			{
				$ack=array( "ack"=>1,
							"ack_msg"=>"Successfully Get notification !!",
							"developer_msg"=>"You got it!!",
							"result"=>$get_notifications,
							);
			}
			else
			{
				$ack=array( "ack"=>0,
							"ack_msg"=>"No notification Found !!",
							"developer_msg"=>"No notification found!!",
							);
				
			}
			$db->printJSON($ack);
			
		}
		else if($service=='get_reject_expense' || $service==34)
			{
					if($sales_executive_id	=(isset($_REQUEST['sales_executive_id'])&& $_REQUEST['sales_executive_id']!="")?$_REQUEST['sales_executive_id']:"")
					{
						$ctable_where .= "sales_executive_id='".$sales_executive_id."' AND isDelete=0 AND isActive=0 ";
						if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
						{
						  $ctable_where .= " AND expense_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
						}

						 if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
						{
							 $ctable_where .= " AND expense_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
						}
						if(($_REQUEST['ToDate']=="") && ($_REQUEST['FromDate']==""))
						{
							$month=date("m");
							$ctable_where .= "AND MONTH(expense_date) = '".$month."'";
						}
					$expense_r = $db->rp_getData("expense","*",$ctable_where,"expense_date DESC",0);						
					if($expense_r)
					{
						while($expense_d = mysqli_fetch_assoc($expense_r)){
							//Fetching Only Id then using function getProductDetail get Information of that product
							$expense_d['sales_name']=$db->rp_getValue("sales_executive","username","id='".$expense_d['sales_executive_id']."'",0);
						   $expense_d['expense_date']=date('d-m-Y',strtotime($expense_d['expense_date']));
						   $grand_total+=$expense_d['total'];
						   $result[] = $expense_d;
						}
						
					}
					if(!empty($result))
					{
						
						$ack=array( "ack"=>1,
							"ack_msg"=>"Rejected Expense History Found!!",
							"developer_msg"=>"Rejected Expense History Found!!",
							"Grand_total"=>$grand_total,
							"result"=>$result,
							);
					}
					else
					{
						$ack=array( "ack"=>0,
							"ack_msg"=>"No Rejected Expense History Found!!",
							"developer_msg"=>"Rejected Expense List not Fetched!!"												
						);
					}
			}
			else
			{
					$ack=array( "ack"=>0,
							"ack_msg"=>"Internal error!!",
							"developer_msg"=>"Service Parameter missing or not valid!!"
							);
			}
			$db->printJSON($ack);
		}
		else if($service=="add_no_order_inquiry" || $service==41)
		{
			if(isset($_REQUEST['sales_executive_id']))
			{
				$detail['sales_executive_id']	= isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";
				$detail['customer_name']	    = isset($_REQUEST['customer_name'])?$db->clean($_REQUEST['customer_name']):"";
				$detail['mobile_number']	    = isset($_REQUEST['mobile_number'])?$db->clean($_REQUEST['mobile_number']):"";
				$detail['contact_person']	    = isset($_REQUEST['contact_person'])?$db->clean($_REQUEST['contact_person']):"";
				$detail['country']	            = isset($_REQUEST['country'])?$db->clean($_REQUEST['country']):"";
				$detail['state']	            = isset($_REQUEST['state'])?$db->clean($_REQUEST['state']):"";
				$detail['city']	                = isset($_REQUEST['city'])?$db->clean($_REQUEST['city']):"";
				$detail['description']	        = isset($_REQUEST['description'])?$db->clean($_REQUEST['description']):"";
				$detail['action']	            = isset($_REQUEST['action'])?$db->clean($_REQUEST['action']):"";
				$detail['$datetime']=isset($_REQUEST['datetime'])?date('Y-m-d H:s:i',strtotime($_REQUEST['datetime'])):"0000-00-00 00:00:00";
				$reply                          =$objSalesExecutive->addNoOrderInquiry($detail);
				$db->printJSON($reply);
					
			}
					
			else{
				$ack=array( "ack"=>0,"ack_msg"=>"Something wrong !! Please Try Again Later!!","developer_msg"=>"not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		}
		else if($service=="update_no_order_inquiry" || $service==42)
		{
			if(isset($_REQUEST['sales_executive_id']) && isset($_REQUEST['id']))
			{
				$detail['sales_executive_id']	= isset($_REQUEST['sales_executive_id'])?$db->clean($_REQUEST['sales_executive_id']):"";
				$detail['id']	                = isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
				$detail['customer_name']	    = isset($_REQUEST['customer_name'])?$db->clean($_REQUEST['customer_name']):"";
				$detail['mobile_number']	    = isset($_REQUEST['mobile_number'])?$db->clean($_REQUEST['mobile_number']):"";
				$detail['contact_person']	    = isset($_REQUEST['contact_person'])?$db->clean($_REQUEST['contact_person']):"";
				$detail['country']	            = isset($_REQUEST['country'])?$db->clean($_REQUEST['country']):"";
				$detail['state']	            = isset($_REQUEST['state'])?$db->clean($_REQUEST['state']):"";
				$detail['city']	                = isset($_REQUEST['city'])?$db->clean($_REQUEST['city']):"";
				$detail['description']	        = isset($_REQUEST['description'])?$db->clean($_REQUEST['description']):"";
				$detail['action']	            = isset($_REQUEST['action'])?$db->clean($_REQUEST['action']):"";
				$detail['$datetime']=isset($_REQUEST['datetime'])?date('Y-m-d H:s:i',strtotime($_REQUEST['datetime'])):"0000-00-00 00:00:00";
				$reply                          = $objSalesExecutive->updateNoOrderInquiry($detail,$id);
				$db->printJSON($reply);
					
			}
					
			else{
				$ack=array( "ack"=>0,"ack_msg"=>"Something wrong !! Please Try Again Later!!","developer_msg"=>"not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		}
		else if($service=="delete_no_order_inquiry" || $service==43)
		{
			if(isset($_REQUEST['id']))
			{
				$detail['id']	                = isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
				$reply                          =$objSalesExecutive->deleteNoOrderInquiry($id);
				$db->printJSON($reply);
					
			}
					
			else{
				$ack=array( "ack"=>0,"ack_msg"=>"Something wrong !! Please Try Again Later!!","developer_msg"=>"not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		}
		else if($service=="list_order_inquiry" || $service==40)
		{
			if(isset($_REQUEST['id']))
			{
				$detail['id']	                = isset($_REQUEST['id'])?$db->clean($_REQUEST['id']):"";
				$reply                          =$objSalesExecutive->listNoOrderInquiry($id);
				$db->printJSON($reply);
					
			}
			else{
				$ack=array( "ack"=>0,"ack_msg"=>"Something wrong !! Please Try Again Later!!","developer_msg"=>"not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		}
		else if($service=="update_executive_area" || $service==50)
		{
			$body= file_get_contents('php://input');
			if($body!="")
			{
				$areas 	= ($body!="")?(array)json_decode($body,true):array();
				$executive=new Executive();
				$reply   =$executive->MapExecutiveArea($areas);
				$db->printJSON($reply);
					
			}
			else{
				$ack=array( "ack"=>0,"ack_msg"=>"Something wrong !! Please Try Again Later!!","developer_msg"=>"not inserted!! sales id not get!!");
				$db->printJSON($ack);
			}
		}
//-----------#Get Active App detail#----------------------------------------------//			
		else if($service=='get_active_app' || $service==48)
		{
					
				
			$application_info_r  = $db->rp_getData("application_info","version_name, version_code,type,file","isActive=1  AND isDelete=0");
			if($application_info_r>0)
			{
				$result=array();
				$result=mysqli_fetch_assoc($application_info_r);
				$result['file']=SITEURL."apk/".$result['file'];
				$ack=array( "ack"=>1,
						"ack_msg"=>"Active App Information Get Successfully!!",
						"developer_msg"=>"You got it!!",
						"result"=>$result,
						);
						$db->printJSON($ack);
			}
			else	
			{
				$ack=array( "ack"=>0,
						"ack_msg"=>"No Data Avalaible!!",
						"developer_msg"=>"Sorry We can't Procced!",
						);
						$db->printJSON($ack);
			}					
			
			
		}	
//----------------------------------------------------------------------------//
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
	$email=$nt->aj_sendSecurityCode($email,"Security Check ".SITENAME."",$body);
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