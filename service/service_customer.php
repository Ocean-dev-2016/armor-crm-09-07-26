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
			$detail['phone'] 		= $db->clean($_REQUEST['phone']);
			$detail['password'] 	= $db->clean($_REQUEST['password']);
			$detail['imei'] 	= $db->clean($_REQUEST['imei']);
			$detail['refreshToken'] 	= $db->clean($_REQUEST['refreshToken']);
			if($detail['phone']!="" && $detail['password']!="" && $detail['refreshToken']!="")
			{ 	
				$reply=$objCustomer->loginCustomer($detail);
				echo json_encode($reply);
				
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Mobile No or Password not valid ");
				echo json_encode($ack);
			}
		}	
		
		else if($service=="register_customer" || $service==61)
		{
			
			$detail['email'] 		= $db->clean($_REQUEST['email']);
			$detail['password'] 	= md5($db->clean($_REQUEST['password']));
			$detail['name'] 	= $db->clean($_REQUEST['name']);
			$detail['mobile'] 	= $db->clean($_REQUEST['mobile']);
			$detail['address'] 	= $db->clean($_REQUEST['address']);
			$detail['locality'] 	= $db->clean($_REQUEST['locality']);
			$detail['pincode'] 	= $db->clean($_REQUEST['pincode']);
			$detail['city'] 	= $db->clean($_REQUEST['city']);
			$detail['state'] 	= $db->clean($_REQUEST['state']);
			$detail['country'] 	= $db->clean($_REQUEST['country']);
			$detail['company_name'] 	= $db->clean($_REQUEST['company_name']);
			
			if($detail['email']!="" && $detail['password']!="" && !filter_var($detail['email'], FILTER_VALIDATE_EMAIL) === false  && $detail['name']!="" && $detail['company_name']!="")	
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
			$detail['phone'] 	= $db->clean($_REQUEST['phone']);
			$detail['address1'] 	= $db->clean($_REQUEST['address']);
			$detail['locality'] 	= $db->clean($_REQUEST['locality']);
			$detail['zip'] 	= $db->clean($_REQUEST['zip']);
			$detail['country'] 	= $db->clean($_REQUEST['country']);
			$detail['state'] 	= $db->clean($_REQUEST['state']);
			$detail['city'] 	= $db->clean($_REQUEST['city']);
			
			if($detail['id']!="" && $detail['name']!="")	
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
							// When dates are not provided, show full expense history (no month restriction).
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
		else if($service=="get_customer_leads" || $service==143)	
		{
			if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="")
			{
				$system=new System();
				$limit=$system->getLimit();
				$customer_type = $_REQUEST['customer_type'];
				$source_of_inquiry = $_REQUEST['source_of_inquiry'];
				$person_name = $_REQUEST['person_name'];
				$company_name = $_REQUEST['company_name'];
				$mobile_number = $_REQUEST['mobile_no'];
				$assign_to = $_REQUEST['assign_to'];
				$created_by = $_REQUEST['created_by'];
				$status = $_REQUEST['status'];
				$status_type=array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"Working");

				if($customer_type!="")
				{
					$where.=" executive_type = '".$customer_type."' AND ";
				}

				if($source_of_inquiry!="")
				{
					$where.=" source_of_inquiry = '".$source_of_inquiry."' AND ";
				}

				if($person_name!="")
				{
					$where.=" person_name LIKE '%".$person_name."%' AND ";
				}

				if($company_name!="")
				{
					$where.=" company_name LIKE '%".$company_name."%' AND ";
				}

				if($mobile_number!="")
				{
					$where.=" mobile_number LIKE '%".$mobile_number."%' AND ";
				}

				if($assign_to!="")
				{
					$where.= " inquiry_assign_to = '".$assign_to."' AND ";
				}

				if($created_by!="")
				{
					$where.= " inquiry_created_by = '".$created_by."' AND ";
				}

				if($status!="")
				{
					$where.=" status = '".$status."' AND ";
				}

				if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
				{
				 	$where .= " date_of_call <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' OR ";
				}

				if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
				{
					$where .= " date_of_call >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' AND ";
				}
				
				//$where.="inquiry_created_by='".$_REQUEST['sales_id']."' AND isDelete=0";

				$where.="isDelete=0 AND (inquiry_assign_to = '".$_REQUEST['sales_id']."' OR inquiry_created_by = '".$_REQUEST['sales_id']."' OR sales_executive_id ='".$_REQUEST['sales_id']."')";
				$leads_r=$db->rp_getData("customer_inquiry","*",$where,"id DESC",0,$limit);
				if($leads_r)
				{	
					while($leads_d=mysqli_fetch_assoc($leads_r))
					{
						$u_w_array = array("0"=>"","1"=>"Yes","2"=>"No");
						
						$quotation_array = array("1"=>"Yes","2"=>"No");

						$leads_d['source_of_inquiry_slug']	=$inquiry_type_array[$leads_d['source_of_inquiry']];

						$leads_d['u_w_flag_name']	= $u_w_array[$leads_d['u_w_flag']];

						$leads_d['quotation_flag_name']	=$u_w_array[$leads_d['quotation_flag']];

						$weight_name = $db->rp_getValue("weight","name","id='".$leads_d['product_id']."' AND isDelete=0",0);

						$leads_d['prouct_name']	= $db->rp_getValue("product","name","id='".$leads_d['product_id']."'",0)."-".$weight_name;
						
						$leads_d['inquiry_created_by_name']=$db->rp_getValue("sales_executive","name","id='".$leads_d['sales_executive_id']."'");
						
						$leads_d['inquiry_assign_to_name']=$db->rp_getValue("sales_executive","name","id='".$leads_d['inquiry_assign_to']."'");
						
						$leads_d['product_name']=$db->rp_getValue("product","name","id='".$leads_d['product_id']."'");
						
						$leads_d['executive_type_slug']=$db->rp_getValue("customer_type","name","id='".$leads_d['executive_type']."'");
						
						$leads_d['source_of_inquiry_slug'] = $db->rp_getValue("source_of_inquiry","name","id='".$leads_d['source_of_inquiry']."'");

						$leads_d['status_slug']	=$status_type[$leads_d['status']];
				
						$leads_d['color_code'] = $db->inquiry_status_color[$leads_d['status_slug']];

						$leads_d['date_of_call']=date("d-m-Y",strtotime($leads_d['date_of_call']));
							
						$img = explode(",", $leads_d['image_path']);
					 	$imgpath = array();
						for ($i=0; $i < sizeof($img); $i++)
						{ 
							$imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$leads_d['id']."' AND id='".$img[$i]."'");
						}
						$leads_d['image_path'] = ($leads_d['image_path']!= "")?$imgpath:[];
						/*if($leads_d['image_path']!="")
	                    {	                        
	                        $leads_d['image_path']=SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$leads_d["id"]."' AND id='".$leads_d['image_path']."'",0);
	                    }
	                    else
	                    {
	                        $leads_d['image_path'] = DEFAULTIMG;
	                    }*/
						$data[]=$leads_d;
					}

					/*Get Inquiry Status*/
						$inquiry_status = array();
						$InquiryData = $db->rp_getData('customer_inquiry',"DISTINCT(status)","(inquiry_assign_to = '".$_REQUEST['sales_id']."' OR inquiry_created_by = '".$_REQUEST['sales_id']."' OR sales_executive_id='".$_REQUEST['sales_id']."') AND isDelete=0","",0,$limit);
						$inquiry_status_array=array("0"=>"Generate","1"=>"In Followup","2"=>"Interested","-1"=>"Not Interested","3"=>"Working");
						$inquiry_status_key = array("0","1","2","-1","3");
						while($Inquiry_d = mysqli_fetch_assoc($InquiryData))
						{
							$Inquiry_d['count']=$db->rp_getTotalRecord("customer_inquiry","(inquiry_assign_to = '".$_REQUEST['sales_id']."' OR inquiry_created_by = '".$_REQUEST['sales_id']."' OR sales_executive_id ='".$_REQUEST['sales_id']."' ) AND status='".$Inquiry_d['status']."'  AND isDelete=0",0);

							if (($key_inquiry = array_search($Inquiry_d['status'], $inquiry_status_key)) !== false) {
							    unset($inquiry_status_key[$key_inquiry]);
							}

							$Inquiry_d['status_slug'] = $inquiry_status_array[$Inquiry_d['status']];
							$Inquiry_d['status'] = $Inquiry_d['status'];

							$Inquiry_d['color_code'] = $db->inquiry_status_color[$Inquiry_d['status_slug']];

							if($Inquiry_d['color_code']=="")
							{
								$Inquiry_d['color_code'] = "";
							}

							if($Inquiry_d['status_slug']=="")
							{
								$Inquiry_d['status_slug'] = "";
							}
							$inquiry_status[]=$Inquiry_d;
						}
						foreach ($inquiry_status_key as $key => $remainval_inquiry) {
							$Inquiry_d['count'] = 0;
							$Inquiry_d['status'] = $remainval_inquiry;
							$Inquiry_d['status_slug'] = $inquiry_status_array[$remainval_inquiry];
							$Inquiry_d['color_code'] = $db->inquiry_status_color[$inquiry_status_array[$remainval_inquiry]];
							$inquiry_status[]=$Inquiry_d;
						}
					/*Get Inquiry Status*/

					$ack=array("ack"=>1,"ack_msg"=>"Customer Leads get successfully","result"=>$data,"customer_inquiry_status"=>$inquiry_status);
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"No Data found in Customer Leads");
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
			}
			$db->printJSON($ack);
		}
		
		else if($service=="add_customer_leads" || $service==144)
		{
			// var_dump($_REQUEST);
			if(isset($_REQUEST['company_name']) && $_REQUEST['company_name']!="" && isset($_REQUEST['contact_person']) && $_REQUEST['contact_person']!="" && isset($_REQUEST['mobile_number']) && $_REQUEST['mobile_number']!="" && isset($_REQUEST['country']) && $_REQUEST['country']!="" && isset($_REQUEST['state']) && $_REQUEST['state']!="" && isset($_REQUEST['city']) && $_REQUEST['city']!="" && isset($_REQUEST['inquiry_created_by']) && $_REQUEST['inquiry_created_by']!="")
			{
				$detail['source_of_inquiry'] = isset($_REQUEST['source_of_inquiry'])?$db->clean($_REQUEST['source_of_inquiry']):"";
				$detail['executive_type'] =isset($_REQUEST['executive_type'])? $db->clean($_REQUEST['executive_type']):"";
				$detail['company_name'] =isset($_REQUEST['company_name'])? $db->clean($_REQUEST['company_name']):"";
				$detail['contact_person'] =isset($_REQUEST['contact_person'])? $db->clean($_REQUEST['contact_person']):"";
				$detail['person_name'] =isset($_REQUEST['person_name'])? $db->clean($_REQUEST['contact_person']):"";
				$detail['mobile_number'] =isset($_REQUEST['mobile_number'])? $db->clean($_REQUEST['mobile_number']):"";
				$detail['whatsapp_number'] =isset($_REQUEST['whatsapp_number'])? $db->clean($_REQUEST['whatsapp_number']):"";
				$detail['country'] =isset($_REQUEST['country'])? $db->clean($_REQUEST['country']):"";
				$detail['state'] =isset($_REQUEST['state'])? $db->clean($_REQUEST['state']):"";
				$detail['city'] =isset($_REQUEST['city'])? $db->clean($_REQUEST['city']):"";
				$detail['inquiry_created_by'] =isset($_REQUEST['inquiry_created_by'])? $db->clean($_REQUEST['inquiry_created_by']):"";
				$detail['inquiry_assign_to'] =isset($_REQUEST['inquiry_assign_to'])? $db->clean($_REQUEST['inquiry_assign_to']):"";
				$detail['inquiry_date'] =isset($_REQUEST['inquiry_date'])? $db->clean($_REQUEST['inquiry_date']):"";
				$detail['first_followup_date'] =isset($_REQUEST['first_followup_date'])? $db->clean($_REQUEST['first_followup_date']):"";
				$detail['followup_detail'] =isset($_REQUEST['followup_detail'])? $db->clean($_REQUEST['followup_detail']):"";
				$detail['date_of_call'] =isset($_REQUEST['date_of_call'])? $db->clean($_REQUEST['date_of_call']):"";
				$detail['email_address'] =isset($_REQUEST['email_address'])? $db->clean($_REQUEST['email_address']):"";
				$detail['address'] =isset($_REQUEST['address'])? $db->clean($_REQUEST['address']):"";
				$detail['distributor_id'] =isset($_REQUEST['distributor_id'])? $db->clean($_REQUEST['distributor_id']):"";
				$detail['country'] =isset($_REQUEST['country'])? "India":"";
				$detail['image_path'] =isset($_REQUEST['image_path'])? $db->clean($_REQUEST['image_path']):"";
				$detail['product_id'] =isset($_REQUEST['product_id'])? $db->clean($_REQUEST['product_id']):"";
				$detail['quantity'] =isset($_REQUEST['quantity'])? $db->clean($_REQUEST['quantity']):"";
				$detail['remark'] =isset($_REQUEST['remark'])? $db->clean($_REQUEST['remark']):"";
				$detail['quotation_flag'] =isset($_REQUEST['quotation_flag'])? $db->clean($_REQUEST['quotation_flag']):"";
				$detail['customer_requirement'] =isset($_REQUEST['customer_requirement'])? $db->clean($_REQUEST['customer_requirement']):"";

				require_once("../include/class.customer_inquiry.php");
				$objCustomerIquiry= new CustomerInquiry();
				if($_REQUEST['lead_id']!="")
				{
					$ack=$objCustomerIquiry->UpdateCustomerLead($detail);
				}
				else
				{
					$ack=$objCustomerIquiry->InsertCustomerInquiry($detail,$_FILES);
				}
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Internal error!!","developer_msg"=>"Check your API Key or contact Admin","extra"=>array("requested_params"=>$_REQUEST,"other"=>array()));
			}
			$db->printJSON($ack);
		}

		else if($service=='get_inquiry_assign_user' || $service==145)
		{
			$user = array();
			$Sales_r = $db->rp_getData("sales_executive","id,name,username","isDelete=0","id DESC",0);
			if($Sales_r)
			{
				while($Sales_d = mysqli_fetch_assoc($Sales_r))
				{
					$user[] = $Sales_d;
				}
			}

			if(!empty($user))
			{
				$reply=array("ack"=>1,"developer_msg"=>"Data Get successfully!!","ack_msg"=>"Data Get successfully!!","result"=>$user);
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Data Not Get!!","ack_msg"=>"Data Not Get!!");
			}
			echo json_encode($reply);
		}

		else if($service=="edit_customer_leads" || $service==146)
		{
			if(isset($_REQUEST['company_name']) && $_REQUEST['company_name']!="" && isset($_REQUEST['contact_person']) && $_REQUEST['contact_person']!="" && isset($_REQUEST['mobile_number']) && $_REQUEST['mobile_number']!="" && isset($_REQUEST['country']) && $_REQUEST['country']!="" && isset($_REQUEST['state']) && $_REQUEST['state']!="" && isset($_REQUEST['city']) && $_REQUEST['city']!="" && isset($_REQUEST['inquiry_created_by']) && $_REQUEST['inquiry_created_by']!="" && $_REQUEST['lead_id']!="")
			{
				$detail['source_of_inquiry'] = isset($_REQUEST['source_of_inquiry'])?$db->clean($_REQUEST['source_of_inquiry']):"";
				$detail['executive_type'] = isset($_REQUEST['executive_type'])? $db->clean($_REQUEST['executive_type']):"";
				$detail['company_name'] = isset($_REQUEST['company_name'])? $db->clean($_REQUEST['company_name']):"";
				$detail['contact_person'] = isset($_REQUEST['contact_person'])? $db->clean($_REQUEST['contact_person']):"";
				$detail['person_name'] = isset($_REQUEST['person_name'])? $db->clean($_REQUEST['contact_person']):"";
				$detail['mobile_number'] = isset($_REQUEST['mobile_number'])? $db->clean($_REQUEST['mobile_number']):"";
				$detail['whatsapp_number'] = isset($_REQUEST['whatsapp_number'])? $db->clean($_REQUEST['whatsapp_number']):"";
				$detail['country'] = isset($_REQUEST['country'])? $db->clean($_REQUEST['country']):"";
				$detail['state'] =isset($_REQUEST['state'])? $db->clean($_REQUEST['state']):"";
				$detail['city'] =isset($_REQUEST['city'])? $db->clean($_REQUEST['city']):"";
				$detail['inquiry_created_by'] =isset($_REQUEST['inquiry_created_by'])? $db->clean($_REQUEST['inquiry_created_by']):"";
				$detail['inquiry_assign_to'] =isset($_REQUEST['inquiry_assign_to'])? $db->clean($_REQUEST['inquiry_assign_to']):"";
				$detail['inquiry_date'] =isset($_REQUEST['inquiry_date'])? $db->clean($_REQUEST['inquiry_date']):"";
				$detail['first_followup_date'] =isset($_REQUEST['first_followup_date'])? $db->clean($_REQUEST['first_followup_date']):"";
				$detail['followup_detail'] =isset($_REQUEST['followup_detail'])? $db->clean($_REQUEST['followup_detail']):"";
				$detail['date_of_call'] =isset($_REQUEST['date_of_call'])? $db->clean($_REQUEST['date_of_call']):"";
				$detail['email_address'] =isset($_REQUEST['email_address'])? $db->clean($_REQUEST['email_address']):"";
				$detail['address'] =isset($_REQUEST['address'])? $db->clean($_REQUEST['address']):"";
				$detail['distributor_id'] =isset($_REQUEST['distributor_id'])? $db->clean($_REQUEST['distributor_id']):"";
				$detail['country'] =isset($_REQUEST['country'])? "India":"";
				$detail['image_path'] =isset($_REQUEST['image_path'])? $db->clean($_REQUEST['image_path']):"";
				$detail['product_id'] =isset($_REQUEST['product_id'])? $db->clean($_REQUEST['product_id']):"";
				$detail['quantity'] =isset($_REQUEST['quantity'])? $db->clean($_REQUEST['quantity']):"";
				$detail['remark'] =isset($_REQUEST['remark'])? $db->clean($_REQUEST['remark']):"";
				$detail['quotation_flag'] =isset($_REQUEST['quotation_flag'])? $db->clean($_REQUEST['quotation_flag']):"";
				$detail['customer_requirement'] =isset($_REQUEST['customer_requirement'])? $db->clean($_REQUEST['customer_requirement']):"";
				$detail['lead_id'] =isset($_REQUEST['lead_id'])? $db->clean($_REQUEST['lead_id']):"";

				require_once("../include/class.customer_inquiry.php");
				
				$objCustomerIquiry= new CustomerInquiry();
				
				$ack=$objCustomerIquiry->UpdateCustomerLead($detail);
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"Internal error!!","developer_msg"=>"Check your API Key or contact Admin","extra"=>array("requested_params"=>$_REQUEST,"other"=>array()));
			}
			$db->printJSON($ack);
		}
	else if($service=='generate_inquiry_to_lead' || $service==147)
		{
			$IsInquiry = $db->rp_getTotalRecord("no_order_inquiry","id='".$_REQUEST['inquiry_id']."' AND inquiry_lead_flag='0' AND isDelete=0",0);
			if($IsInquiry>0)
			{ 			
				$R1 = $db->rp_getData("no_order_inquiry","*","isDelete=0 AND id='".$_REQUEST['inquiry_id']."' AND inquiry_lead_flag='0' ","",0);
				$D1 = mysqli_fetch_assoc($R1);

				if($D1['company_name']!="" && $D1['state']!="" && $D1['city']!="" && $D1['mobile_number']!="")
				{ 
					if($D1['gst_no']!="")
					{ 
						$customer_count = $db->rp_getTotalRecord("executive","gst='".$D1['gst_no']."' AND isDelete=0",0);
					}
					else
					{ 
						$customer_count=$db->rp_getTotalRecord("executive","company_name='".$D1['company_name']."' AND mobile_no1='".$D1['mobile_number']."' AND isDelete=0",0);
						/*$customer_count = $db->rp_getTotalRecord("executive","company_name='".$D1['company_name']."' AND city='".$D1['city']."' AND isDelete=0",0);*/
					}
					//echo $customer_count;exit;
					$entry_flag= isset($_REQUEST['entry_flag'])?$db->clean($_REQUEST['entry_flag']):"5"; 
					if($customer_count<=0)
					{
						// code for client code // 
						$client_code_prefix=$db->rp_getValue("company_master","prefix","id='".$D1['type_of_company']."' AND isDelete=0",0);
						$lastInsertIds=$db->rp_getValue("executive","MAX(`client_code_sr_by_type`)","type_of_company='".$D1['type_of_company']."' AND isDelete=0",0); 

						$code=str_pad(($lastInsertIds+1), 4, '0', STR_PAD_LEFT); 
						$client_code = $client_code_prefix.($code); 
						// code for client code // 

						$dup_where = " (mobile_no1 = '" . $D1['mobile_number'] . "' OR client_code = '" . $client_code . "') AND company_name = '".$D1['company_name']."' AND isDelete=0";

						$r = $db->rp_dupCheck('executive', $dup_where,0);

						if ($r) 
						{
							$reply = array("ack" => 0, "developer_msg" => "Mobile number already assigned to another customer!! Try another number.", "ack_msg" => "A mobile number or client code already exists, or the company name is already associated with another customer. Please check.");
							$db->printJSON($reply,1);
						} else {

							$category_data_r=$db->rp_getData("top_category_master","id","isDelete=0","",0); 
							while($category_data_d=mysqli_fetch_assoc($category_data_r)) 
							{
								$catArr[]=$category_data_d['id'];
							}
							$catids=implode(',',$catArr);

							$M1=$db->rp_getData("customer_vs_phone_no","*","isDelete=0 AND customer_id='".$_REQUEST['inquiry_id']."' AND ref_table='no_order_inquiry'","",0);
							while($MD1=mysqli_fetch_assoc($M1))
							{
								$M_IDS[]=$MD1['phone_no'];
								$CONTACT_IDS[]=$MD1['name'];
							}

							$add_rows = array("type_of_executive","company_name","cname","email","mobile_no1","whatsapp_no","address","country","state","main_city","city","customer_flag","gst","industry_type_id","entry_flag","shipping_address,billing_address","client_code","zone","client_code_sr_by_type","seid","top_category_id","type_of_company","zip","booking_pincode");
							
							$seid=($_REQUEST['sales_id'])?$_REQUEST['sales_id']:""; 

							$add_values = array($D1['executive_type'],$D1['company_name'],$D1['person_name'],$D1['email_address'],$D1['mobile_number'],$D1['other_mobile_no'],$D1['address'],$D1['country'],$D1['state'],$D1['main_city'],$D1['city'],1,$D1['gst_no'],$D1['industry_type_id'],$entry_flag,str_replace(array("\n", "\r"), ' ',$D1['shipping_address']),str_replace(array("\n", "\r"), ' ',$D1['billing_address']),$client_code,$D1['zone'],$code,$seid,$catids,$D1['type_of_company'],$D1['pincode'],$D1['pincode']);
						
							$InsretId = $db->rp_insert("executive",$add_values,$add_rows,0);

							if ($InsretId) {
								$count_value=0;
								foreach ($M_IDS as $key)
								{
									$item_rows1 = array("customer_id","phone_no","name","ref_table");
									$item_values1 = array($InsretId,addslashes(html_entity_decode($key)),$CONTACT_IDS[$count_value],"executive");
									$count_value++;
									$item_id = $db->rp_insert("customer_vs_phone_no",$item_values1,$item_rows1,0);
								}

								$item_rows = array("customer_id","shipping_address");
								$item_values = array($InsretId,$D1['shipping_address']);
								$item_id = $db->rp_insert("customer_vs_shipping_address",$item_values,$item_rows,0);
							}

							/*add class area*/

							/* Added Code By DINESH */
							if ($D1['area_id'] == "" || $D1['area_id'] == null || $D1['area_id'] == NULL || empty($D1['area_id'])) {
								
								$D1['area_id'] = $db->rp_getValue( "area", "id", " class_id='".$D1['class_id']."' AND city_id='".$D1['city_id']."' AND name LIKE '%".strtolower(trim($D1['main_city']))."%'", 0 );
							}
							/* Added Code By DINESH */

							if($D1['class_id']!=0 && $D1['area_id']!=0 && $D1['city_id'])
							{
								$class_id = $D1['class_id'];
								$area_id = $D1['area_id'];
								$city_id = $D1['city_id'];
							}
							else
							{
								$classArea = $db->getCassAreaIdFromName($D1['state'],$D1['main_city'],$D1['city']);
								$class_id = $classArea['class_id'];
								$area_id = $classArea['area_id'];
								$city_id =  $classArea['city_id'];
							}
							
							$mapping_id=$db->rp_insert("executive_map_area",array($InsretId,$D1['executive_type'],$class_id,$area_id,$city_id),array("executive_id","executive_type","class_id","area_id","city_id"),0);
							/*add class area*/ 
							require_once("../include/class.executive.php");
							$objClass= new Executive();
							$objClass->CreateCustomerAccount($InsretId);

							$Isupdate = $db->rp_update("no_order_inquiry",array("dealer_id"=>$InsretId),"id='".$_REQUEST['inquiry_id']."'");
						}

					}
					else
					{
						if($D1['gst_no']!="")
						{
							$dealer_id = $db->rp_getValue("executive","id","gst='".$D1['gst_no']."' AND isDelete=0",0);

						}else	{
							$dealer_id = $db->rp_getValue("executive","id","company_name='".$D1['company_name']."' AND phone='".$D1['mobile_number']."' AND isDelete=0",0);
						}
							
						
						$Isupdate = $db->rp_update("no_order_inquiry",array("dealer_id"=>$dealer_id),"id='".$_REQUEST['inquiry_id']."'");
					}


					$sales_name = $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'",0);
					$company_name  = $db->rp_getValue("no_order_inquiry","company_name","id='".$_REQUEST['inquiry_id']."' AND isDelete=0",0);
					$module_name = "Inquiry";
					$flag = "Application";
					$log_description = $module_name." #INQ/".$_REQUEST['inquiry_id']." Convert To Lead By ".$sales_name." ON ".date("Y-m-d H:i:s");
					
					$update_data = array("inquiry_lead_flag"=>1,"inquiry_type"=>1,"lead_date"=>date('Y-m-d'),"entry_flag"=>$entry_flag,"inq_status"=>3);
					$update = $db->rp_update("no_order_inquiry",$update_data,"id='".$_REQUEST['inquiry_id']."'",0,$log_description,$flag,$module_name,$_REQUEST['sales_id']);

					if($update)
					{
						$ack = array("ack"=>1,"ack_msg"=>"Inquiry Convert To Lead Successfully");
					}
					else
					{
						$ack = array("ack"=>0,"ack_msg"=>"Something Went Wrong.Please Check");	
					}
				}
				else
				{
					$ack = array("ack"=>0,"ack_msg"=>"Compay Name, State, City, Contact Number is Required. Please Fill the required Detail For Create Customer.");
				}
			} 
			else
			{
				$ack = array("ack"=>0,"ack_msg"=>"There Is No Such Inquiry Found.Please Check And Try Again.");
			}
			
			$db->printJSON($ack);
			
		}
		                                                                                                                                                                                                                                                                                                                                                                  
		else if($service=='add_inquiry_product' || $service==148)
		
		{
			$inquiry_id    = isset($_REQUEST['inquiry_id'])?$db->clean($_REQUEST['inquiry_id']):"";
			$pro_id    = isset($_REQUEST['pro_id'])?$db->clean($_REQUEST['pro_id']):"";
			$weight_id    = isset($_REQUEST['weight_id'])?$db->clean($_REQUEST['weight_id']):"";
			$pro_qty    = isset($_REQUEST['pro_qty'])?$db->clean($_REQUEST['pro_qty']):"";
			$item_remark    = isset($_REQUEST['item_remark'])?$db->clean($_REQUEST['item_remark']):"";
			if($inquiry_id)
			{
				$pro_name = $db->rp_getValue("product","name","id='".$_REQUEST['pro_id']."' AND isDelete=0",0) ." - ".$db->rp_getValue("product_weight_price","catno","product_id='".$pro_id."' AND isDelete=0"); 

				$insert_array = array("inquiry_id","pro_id","weight_id","pro_name","pro_qty","item_remark");
	
				$insert_value = array($inquiry_id,$pro_id,$weight_id,$pro_name,$pro_qty,$item_remark);

				$Insert = $db->rp_insert("no_order_inquiry_item",$insert_value,$insert_array,0);
				if($Insert)
				{
					$ack=array("ack"=>1,"ack_msg"=>"Inquiry Item added Successfully.","developer_msg"=>"Inquiry Item added Successfully.");
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Inquiry Item added Failed.","developer_msg"=>"Inquiry Item added Failed.");
				}
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Inquiry Id Required.","developer_msg"=>"Inquiry Id Required.");
			}
			$db->printJSON($ack);
			
		}
		else if($service=='get_inquiry_product' || $service==149)
		{
			$inquiry_id    = isset($_REQUEST['inquiry_id'])?$db->clean($_REQUEST['inquiry_id']):"";
			if($inquiry_id)
			{
				$inquiry_product_r = $db->rp_getData("no_order_inquiry_item","*","inquiry_id='".$inquiry_id."' AND isDelete=0",0); 
				//$Inquiry_D = mysqli_fetch_assoc($Inquiry_R);

				if($inquiry_product_r)
				{
					$product_inquiry=array();
					while($inquiry_product_d = mysqli_fetch_assoc($inquiry_product_r))
					{
						$product_inquiry[]=$inquiry_product_d;
					}
									
					$ack=array("ack"=>1,"ack_msg"=>"Inquiry Item get Successfully.","developer_msg"=>"Inquiry Item get Successfully.","result"=>$product_inquiry);
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Inquiry Item get Failed.","developer_msg"=>"Inquiry Item get Failed.");
				}
			}
			else
			{
					$ack=array("ack"=>0,"ack_msg"=>"Inquiry Item get Failed.","developer_msg"=>"Inquiry Item get Failed.");
			}
		
		$db->printJSON($ack);
			
	}
	else if($service=='get_inquiry_attachment' || $service==150)
		{
			$inquiry_id    = isset($_REQUEST['inquiry_id'])?$db->clean($_REQUEST['inquiry_id']):"";
			if($inquiry_id)
			{
				$inquiry_attachment_r = $db->rp_getData("no_order_inquiry_attachment","*","inquiry_id='".$inquiry_id."' AND isDelete=0","id DESC"  ,0); 
				//$Inquiry_D = mysqli_fetch_assoc($Inquiry_R);

				if($inquiry_attachment_r)
				{
					$attachment_inquiry=array();
					while($inquiry_attachment_d = mysqli_fetch_assoc($inquiry_attachment_r))
					{
						$inquiry_attachment_d["file_name"] = $inquiry_attachment_d["image_path"];
						$inquiry_attachment_d["image_path"]=SITEURL.INQUIRY_ATTACH_IMAGE.$inquiry_attachment_d["image_path"];
						$attachment_inquiry[]=$inquiry_attachment_d;
					}
									
					$ack=array("ack"=>1,"ack_msg"=>"Inquiry Attachment get Successfully.","developer_msg"=>"Inquiry Attachment get Successfully.","result"=>$attachment_inquiry);
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Inquiry Attachment get Failed.","developer_msg"=>"Inquiry Attachment get Failed.");
				}
			}
			else
			{
					$ack=array("ack"=>0,"ack_msg"=>"Inquiry Attachment get Failed.","developer_msg"=>"Inquiry Attachment get Failed.");
			}
		
		$db->printJSON($ack);
			
	}
	else if($server=="delete_inquiry_item" || $service==151)
		{
			
			if(isset($_REQUEST['id']) && $_REQUEST['id']!="")
			{
				
				$delete_inquiry=$db->rp_update("no_order_inquiry_item",array("isDelete"=>1),"id='".$_REQUEST['id']."'");
				
				if($delete_inquiry)
				{
					$ack=array("ack"=>1,"ack_msg"=>"Inquiry Item Delete Successfully");
					echo json_encode($ack);
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Inquiry Item Deete Failed");
					echo json_encode($ack);
				}
			}
			else
			{
				$ack=array( "ack"=>0,
                "ack_msg"=>"Inquiry Item ID required!!",
                "developer_msg"=>"Inquiry ID required!!",
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
$db->disconnect();
?>