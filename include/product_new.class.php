<?php
require_once("main.class.php");
require_once("function.class.php");
class Product extends Functions
{
	public $db;
	public $ctable="product";
	public $order_status = array("-2"=>"Disapproved","0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Canceled","4"=>"Partially Dispatched");
	
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertProduct($detail,$file) 
	{
		extract($detail);
		$dup_where = "name = '".$name."' AND cid='".$cid."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Catno Name","ack_msg"=>"Duplication! Already Exist Catno Name.");
			return $reply;
		}
		else
		{

			if (isset($file["image_path"])) 
			{
				$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
				$temp = explode(".", $file["image_path"]["name"]);
				$extension = end($temp);
			 
				$fileName 	= $this->db->clean($file["image_path"]["name"]);	
				if($fileName!=""){
				$fileSize 	= round($file["image_path"]["size"]); // BYTES									
				$adate 		= date('Y-m-d H:i:m');
				
				$extension	= end(explode(".", $fileName));		
				if(!in_array($extension,$allowedExts))
				{
					$file_error=true;
				}
										
				$image_path	= 'image_'.substr(sha1(time()), 0, 6).".".$extension;
				$filePath 	= PRODUCT_A.$image_path;	
				$file['image_path']['tmp_name'];
				move_uploaded_file($file['image_path']['tmp_name'], $filePath);
				
				$new_image=true;
				}
				else{
					$image_path="";
				}
			}
			else
			{
				$new_image=false;
				$image_path="";
			}
			$maximum_display_order=$this->db->rp_getValue($this->ctable,"MAX(display_order)","isDelete=0");
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
				"product_type",
				"tcid",
				"cid",
				"name",					
				"product_code",					
				"max_price",
				"sell_price",
				"pro_tax",
				"slug",
				"image_path",
				"display_order",
				"descr",
				"cgst",
				"sgst",
				"igst",
				"brand_id",
				"unit_id",
				"hsn_code",
			);
			$values = array(
				$product_type,
				$tcid,
				$cid,
				$name,
				$product_code,
				$max_price,
				$sell_price,
				$pro_tax,
				$slug,
				$image_path,
				$maximum_display_order+1,
				$descr,
				$cgst,
				$sgst,
				$igst,
				$brand_id,
				$unit_id,
				$hsn_code,
			);

			/*log entry*/
				$module_name = "Product";
				$flag = "Web";
				$log_description = $module_name." ".$name." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
					
		 	$product_id = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name,"","");
			if($product_id!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Product Added.","ack_msg"=>"Success! Product Insert Successfully.","id"=>$product_id);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Product Insert Failed.");
				return $reply;
			}
		}
	}
	 
	public function UpdateProduct($detail,$file)
	{		
		extract($detail);
		$dup_where = "name = '".$name."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if(isset($file["image_path"]) && $file["image_path"]['size']!=0) 
		{
			$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
			$temp = explode(".", $file["image_path"]["name"]);
		 	$extension = end($temp);
	 
			$fileName 	= $this->db->clean($file["image_path"]["name"]);	
			if($fileName!="")
			{
				$fileSize 	= round($file["image_path"]["size"]); // BYTES
				$adate 		= date('Y-m-d H:i:m');
				$extension	= end(explode(".", $fileName));		
				if(!in_array($extension,$allowedExts))
				{
					$file_error=true;
				}
				$image_path	= 'image_'.substr(sha1(time()), 0, 6).".".$extension;
				$filePath 	= PRODUCT_A.$image_path;	
				$file['image_path']['tmp_name'];
				move_uploaded_file($file['image_path']['tmp_name'], $filePath);
				$new_image=true;
			}
			else
			{
				$image_path=$detail['old_image_path'];
				$image_path="";
			}
		}
		else
		{
			$image_path=$detail['old_image_path'];
			unset($detail['old_image_path']);
		}

		$rows 	= array(
			"product_type" => $product_type,
			"tcid"         => $tcid,
			"cid"          => $cid,
			"name"         => $name,
			"product_code" => $product_code,
			"max_price"    => $max_price,
			"sell_price"   => $sell_price,
			"pro_tax"      => $pro_tax,
			"slug"         => $slug,
			"image_path"   => $image_path,
			"descr"        => $descr,
			"cgst"         => $cgst,
			"sgst"         => $sgst,
			"igst"         => $igst,
			"brand_id"     => $brand_id,
			"unit_id"      => $unit_id,
			"hsn_code"     => $hsn_code,
		);
			
		$where	= "id='".$id."'";
			/*log entry*/
				$module_name = "Product";
				$flag = "Web";
				$log_description = $module_name." ".$name." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
			/*log entry*/
		$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
		if($uid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Product Update Successfull!!.","ack_msg"=>"Success! Product Update Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Product Update Failed.");
			return $reply;
		}
	}

	public function GetEditDataProduct($detail)
	{		
		//get product for update
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['name']			= htmlentities($ctable_d['name']);
		$result['product_type']	= htmlentities($ctable_d['product_type']);
		$result['tcid']			= stripslashes($ctable_d['tcid']);
		$result['cid']			= stripslashes($ctable_d['cid']);
		$result['name']			= htmlentities($ctable_d['name']);		
		$result['max_price']	= stripslashes($ctable_d['max_price']);
		$result['sell_price']	= stripslashes($ctable_d['sell_price']);
		$result['pro_tax']		= stripslashes($ctable_d['pro_tax']);
		$result['product_code']	= htmlentities($ctable_d['product_code']);
		$result['unit_id']		= htmlentities($ctable_d['unit_id']);
		$result['hsn_code']		= htmlentities($ctable_d['hsn_code']);
		$result['slug']			= stripslashes($ctable_d['slug']);
		$result['image_path'] 	= stripslashes($ctable_d['image_path']);		
		$result['cgst'] 		= stripslashes($ctable_d['cgst']);		
		$result['sgst'] 		= stripslashes($ctable_d['sgst']);		
		$result['igst'] 		= stripslashes($ctable_d['igst']);		
		$result['brand_id'] 	= stripslashes($ctable_d['brand_id']);	
		$result['display_unit'] = stripslashes($ctable_d['display_unit']);		
		$result['opening_stock']= stripslashes($ctable_d['opening_stock']);		
		$result['min_stock'] 	= stripslashes($ctable_d['min_stock']);		
		$result['max_stock'] 	= stripslashes($ctable_d['max_stock']);			
		$result['descr'] 		= html_entity_decode($ctable_d['descr']);

		$reply=array("ack"=>1,"developer_msg"=>"Product detail fetched!!.","ack_msg"=>"Success! Product Edit Successfully.","result"=>$result);
		return $reply;
	}
	
	public function DeleteProduct($detail)
	{
		$rows 	= array(
			"isDelete"	=> "1"
		);

		$where	= "id='".$_REQUEST['id']."'";
		/*log entry*/
			$name = $this->db->rp_getValue("category_master","name","id='".$_REQUEST['id']."'");
			$module_name = "Product";
			$flag = "Web";
			$log_description = $module_name." ".$name." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");	
		/*log entry*/
		$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
		if($uid!=0)
		{
			$where	= "product_id='".$_REQUEST['id']."'";
			$product_weight_price_id=$this->db->rp_update("product_weight_price",$rows,$where,0);
			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Product Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Product Failed.");
			return $reply;
		}
	}

	function getRequiredColumns($required_columns=array())
	{
		if(!empty($required_columns))
		{
			$required_columns_string=implode(",",$required_columns);
			return $required_columns_string;
		}
		else
		{
			return "*";
		}
	}
	
	function getProductDetail($required_columns)
	{	
		$required_columns=$this->getRequiredColumns($required_columns);	
		$name=$_REQUEST['name'];
		$limit=$this->getLimit();		
		$result=array();
		$where="name like '%".$name."%' AND isDelete=0";
		$data    = $this->db->rp_getData('product',$required_columns,$where,"",0,$limit);
		if($data)
		{
			while($row=mysqli_fetch_assoc($data))
			{
			
			// Fetching Price According to user if uid exits other wise accrofding to list 0
			$price=$row['max_price'];
			//if uid get from request than all price count upon discount as per executive given
			//else normal price get
			$uid=$_REQUEST['uid'];
			if($uid)
			{		
					$discountPer=$this->db->rp_getValue("executive","discount","id='".$uid."'",0);//$discountPer=$this->db->rp_getValue("pricelist","percentage","id='".$pricelist."'");
					
					if($discountPer!=0)
					{
						$discountAmount=$price*$discountPer/100;			
					}
					else
					{
						$discountAmount=0;
					}
					
					$finalPrice=$price-$discountAmount;
			}
			else
			{
					$finalPrice=$row['max_price'];
			}
			$row['max_price']=$price;
			$row['sell_price']=$finalPrice;
			//$row['pricing']=array();
			$weights=$this->db->rp_getData("product_weight_price","*","product_id='".$row['id']."'","",0);
			if(mysqli_num_rows($weights)>0)
			{
			while($w=mysqli_fetch_assoc($weights))
			{
				$price=$w['price'];
				//$stock=$w['opening_stock_qty'];
				//$min=$w['min_qty'];
				//$w['orignal_price']=$price;
				$w['title']=$this->db->rp_getValue("weight","name","id='".$w['weight_id']."'");
				$result[]=$row;
				//$result[]=$w;
			}
			}
			}			
			return $result;
			
		}
		else
		{
			return $result;
		}	
		
	}
	
	function getLimit($limit=array())
	{
		$limit=$this->db->getLimit();	
		if(!empty($limit) && array_key_exists("ul",$limit))
		{
			$ul=$limit['ul'];
			if(array_key_exists("ll",$limit) && $limit['ll']!="")
			{
				$ll=$limit['ll'];
			}
			else
			{
				$ll="18446744073709551615";
			}			
			$limit_string="".$ul.",".$ll;
			return $limit_string;
		}
		else
		{
			return "";
		}
	}
//----------Use for get All product Using Product Weight AND customer's Discount-------//
	function aj_getProductDetail($pid,$uid="")
	{
		$price_list_id=$this->db->rp_getValue("executive","price_list_id","id='".$uid."'",0);
		$proudcts=array();
		$q = 1;
		$product_r = $this->db->rp_getData("product","id,cid,name,max_price,sell_price,pro_tax,image_path,descr,cgst,sgst,igst,brand_id","id='".$pid."' AND isDelete=0","",0);
		if(mysqli_num_rows($product_r)>0){
			
			$product_detail=mysqli_fetch_assoc($product_r);	
			
			/*-----------------------------------------------------------*/
			// Fetching Comelete ImagePath of that product
			$product_detail['image_path']=(file_exists("../".PRODUCT.$product_detail['image_path']))?SITEURL.PRODUCT.$product_detail['image_path']:"";
			/*-----------------------------------------------------------*/
			// Fetching Price According to user if uid exits other wise accrofding to list 0
			$weights=$this->db->rp_getData("product_weight_price","id,product_id,weight_id,price,stock_qty,inner_size,outer_size,catno","product_id='".$product_detail['id']."' ","",0);

			


			if($weights){
			while($w=mysqli_fetch_assoc($weights))
			{
				$total_purchse = $this->db->rp_getValue("purchase_invoice_item","SUM(pro_qty)","pro_id='".$product_detail['id']."'  AND weight_id='".$w['weight_id']."' AND isDelete=0");

				$total_sales = $this->db->rp_getValue("sales_invoice_item","SUM(pro_qty)","pro_id='".$product_detail['id']."'  AND weight_id='".$w['weight_id']."' AND isDelete=0"); 

				$diff = ($total_purchse - $total_sales);

				$price=$w['price'];
				// $stock_qty=$w['stock_qty'];
				$stock_qty=$diff;
				$w['orignal_price']=$price;
				$w['title']=$this->db->rp_getValue("weight","name","id='".$w['weight_id']."'");				
				if($uid!="")
				{
					$discountPer=$this->db->rp_getValue("price_table","discount","uid='".$uid."' AND tcid='".$product_detail['tcid']."'",0);
					//$discountPer=0;
					if($price_list_id!=0)
					{
						$check_product_in_list=$this->db->rp_getTotalRecord("product_price_list","pid='".$product_detail['id']."' AND weight_id='".$w['weight_id']."' AND price_list_id='".$price_list_id."'",0);
						if($check_product_in_list>0)
						{
							$add_price_list_id=$price_list_id;
							$price_list_price=$this->db->rp_getValue("product_price_list","price","pid='".$product_detail['id']."' AND weight_id='".$w['weight_id']."' AND price_list_id='".$price_list_id."'",0);											
							$unitprice=$this->db->rp_getValue("product_price_list","discounted_price","pid='".$product_detail['id']."' AND weight_id='".$w['weight_id']."' AND price_list_id='".$price_list_id."'",0);											
							$discountPer=$this->db->rp_getValue("product_price_list","discount","pid='".$product_detail['id']."' AND weight_id='".$w['weight_id']."' AND price_list_id='".$price_list_id."'",0);											
							$finalPrice=$this->db->rp_num($unitprice);
						}
						else
						{
							$finalPrice=$price;
						}
					}
					else
					{
						$finalPrice=$price;
					}
					// $finalPrice=$price-$discountAmount;
				}
				else
				{
					$finalPrice=$price;
				}

				// change here
				$w['sell_price']=round($finalPrice,2);

				$w['product_id']=$product_detail['id'];
				$w['pro_id']=$w['product_id'];
				$w['cid']=$product_detail['cid'];
				$w['product_name']=$w['title']." ".$product_detail['name']." ";
				$w['name1']= htmlentities($w['title']." ".$product_detail['name']." ");
				if($w['catno']!="")
				{
					$w['name']=$product_detail['name']." - ".$w['title']." - #".$w['catno']."";
				}
				else
				{					
					$w['name']=$product_detail['name']."(".$w['title'].")";
				}
				$w['max_price']=$product_detail['max_price'];
				$w['pro_tax']=$product_detail['pro_tax'];
				$w['image_path']=$product_detail['image_path'];
				$w['descr']=$product_detail['descr'];
				$w['cgst']=$product_detail['cgst'];
				$w['igst']=$product_detail['igst'];
				$w['sgst']=$product_detail['sgst'];
				$w['brand_id']=$product_detail['brand_id'];
				$w['title']=$w['title'];
				$w['catno']=$w['catno'];
				$w['bag_qty']=$w['inner_size'];
				$w['box_qty']=$w['outer_size'];
				$w['qty']=$stock_qty;
				$w['discountPer']=$discountPer;
				$w['final_qty']=$this->db->rp_getValue("product_weight_price","stock_qty","product_id='".$product_detail['id']."' AND bid='".$w['bid']."' AND weight_id='".$w['weight_id']."'",0);
				$proudcts[]=$w;
				$product_detail[]=$w;				
			}
			}
						
			// Fetching Favourite Flag if uid exits
			/*-----------------------------------------------------------*/
			
			// Fetching Category of that product
			$cid		=$product_detail['cid'];
			$cat_d		= mysqli_fetch_assoc($this->db->rp_getData("category_master","name","id=".$cid));			
			$product_detail['category']=stripslashes($cat_d['name']);
			$product_detail['type']=stripslashes($cat_d['name']);
			// Fetching Category of that product			
			/*-----------------------------------------------------------*/
			return $proudcts;
		}else{
			
			return array();
		}
	}
//-----------------------------------------------------------------------------------//	
	function getProductFromQuery($name="")
   {	
			$result = array();
			$where = "name like '%".$name."%' AND isDelete=0";
			$data    = $this->db->rp_getData('product',"*",$where);
			if($data)
			{	
				
				while($row=mysqli_fetch_assoc($data))
				{
					
					$pid=$row['id'];
					$row['orignal_price']=$row['sell_price'];	
									
					$result[]=$row;
				}
				return $result;
			}
			else
			{
				return $result;
			}
		
		
   }
///---------------------------------------------------------------------------------//  
//24-04-2017-----------------#use for get_oreders service#---------------------------------------------------------//	
	
	function getOrders($sales_id,$customer_id,$customer_type,$filter,$limit="")
    {
		$result = array();
		$where="";
		$class_id = $_REQUEST['class_id'];
		$area_id = $_REQUEST['area_id'];
		$status = $_REQUEST['status'];

		if(array_key_exists("first_date", $filter) && $filter['first_date']!="" && array_key_exists("last_date", $filter) && $filter['last_date']!="")
		{
			$where="order_date	 >='".date("Y-m-d",strtotime($filter['first_date']))."' AND order_date <='".date("Y-m-d",strtotime($filter['last_date']))."'  AND ";
		}

		if($customer_id!="")
		{
			//$where.="sales_id ='".$_REQUEST['sales_id']."' AND customer_id='".$_REQUEST['customer_id']."'  ";
			$where.=" customer_id='".$_REQUEST['customer_id']."'  AND";
		}
		else
		{
			$where.=" sales_id ='".$_REQUEST['sales_id']."' AND ";
		}

		/*filter*/
			if($class_id!="")
			{
				$where.=" class_id ='".$_REQUEST['class_id']."'  AND";
			}

			if($area_id!="")
			{
				$where.=" area_id ='".$_REQUEST['area_id']."' AND ";
			}

			if($status!="")
			{
				$where.=" status ='".$_REQUEST['status']."' AND ";
			}

			// if($customer_id!="")
			// {
			// 	$where.=" AND customer_id ='".$_REQUEST['customer_id']."' AND isDelete=0 ";
			// }

			if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
			{
			 	$where .= " order_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."'  AND";
			}

			if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
			{
				$where .= " order_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."'  AND";
			}
			$where .=" isDelete=0 AND status!=-1";

		/*filter*/

		//$customer_id=$this->db->rp_getValue('orders','customer_id',$where,0);
		//$customer=$this->db->rp_getValue('executive','isActive',"id=".$customer_id."",0);
		//$where_final="sales_id ='".$_REQUEST['sales_id']."' AND sales_type='".$_REQUEST['sales_type']."' AND isActive!=".$customer."";
		$data1   = $this->db->rp_getData('orders',"*",$where,"id DESC",0,$limit);//id,sales_id,sales_type,customer_id,customer_name,customer_type,contact_number,address,city,state,country,email,order_date,revised_date,status,total_amount,total_qty,discount,discount_type,grand_total,status
		if($data1)
		{
			while($r= mysqli_fetch_assoc($data1))
			{
				$customer=$this->db->rp_getValue('sales_executive','isActive',"id=".$r['sales_id']."",0);
				/*if($customer==0)
				{
					continue;
				}
				else
				{
				}*/

				$r['sales_name'] = $this->db->rp_getValue("sales_executive","name","id='".$r['sales_id']."' AND type='".$r['sales_type']."'",0);
				$r['total_qty'] = $this->db->rp_getValue("order_product_item","SUM(pro_qty)","order_id='".$r['id']."' AND isDelete=0",0);
				$r['country'] = $this->db->rp_getValue("country","name","id='".$r['country']."'",0);
				$r['state'] = $this->db->rp_getValue("state","name","id='".$r['state']."'",0);
			    $r['city'] = $this->db->rp_getValue("city","name","id='".$r['city']."'",0);
				$r['status']= $r['status'];	
				$r['status_slug']= $this->order_status[intval($r['status'])];
				$r['status_slug']=($r['status_slug']!="")?$r['status_slug']:"";
				$r['order_date']=date('d F Y',strtotime($r['order_date']));
				$r['grand_total_rounded']=$this->db->rp_round($r['grand_total'],0);
				$r['grand_total']=$this->db->rp_round($r['grand_total'],0);
				$r['color_code'] = $this->db->status_color[$r['status_slug']];
				// $r['revised_date']=date('d-m-Y',strtotime($r['revised_date']));

				$result[] = $r; 
			}

			/*Get Order Count*/
				$orderdata = array();
				$OrderData = $this->db->rp_getData('orders',"DISTINCT(status)","isDelete=0 AND status!='-1'","",0,$limit);
				//$OrderData = $this->db->rp_getData('orders',"status",$where,"id DESC",0,$limit);
				$status_array = array("-2"=>"Disapproved","0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Canceled","4"=>"Partially Dispatched");
				$status_key_array = array("-2","0","1","2","3","4");

				while($Order_d = mysqli_fetch_assoc($OrderData))
				{
					if($_REQUEST['sales_id']!="")
					{
						$Order_d['count']=$this->db->rp_getTotalRecord("orders","sales_id ='".$_REQUEST['sales_id']."' AND status='".$Order_d['status']."' AND isDelete=0",0);
					}
					else
					{
						$Order_d['count']=$this->db->rp_getTotalRecord("orders","customer_id ='".$_REQUEST['customer_id']."' AND status='".$Order_d['status']."' AND isDelete=0",0);
					}

					if (($key = array_search($Order_d['status'], $status_key_array)) !== false) {
					    unset($status_key_array[$key]);
					}

					$Order_d['status_slug'] = $status_array[$Order_d['status']];

					$Order_d['status'] = $Order_d['status'];
					// echo "<pre>"; print_r($Order_d);
					$Order_d['color_code'] = $this->db->status_color[$Order_d['status_slug']];

					if($Order_d['color_code']=="")
					{
						$Order_d['color_code'] = "";
					}

					if($Order_d['status_slug']=="")
					{
						$Order_d['status_slug'] = "";
					}
					$orderdata[]=$Order_d;
				}
				foreach ($status_key_array as $key => $remainval) {
					$Order_d['count'] = 0;
					$Order_d['status'] = $remainval;
					$Order_d['status_slug'] = $status_array[$remainval];
					$Order_d['color_code'] = $this->db->status_color[$status_array[$remainval]];
					$orderdata[]=$Order_d;
				}

				$order = $orderdata;
			/*Get Order Count*/
			
			if(!empty($result))
			{
				$ack=array("ack"=>1,"ack_msg"=>"Successfully Get Orders !!","developer_msg"=>"You got it!!","result"=>$result,"order_count"=>$order);
			  	return $ack;
			}
			else
			{
				$ack=array( "ack"=>0,"ack_msg"=>"No Orders Found !!","developer_msg"=>"Not found!!","result"=>$result);
			  	return $ack;
			}
		}
		else
		{
			$ack=array( "ack"=>0,"ack_msg"=>"No Orders Found1 !!","developer_msg"=>"Not found1!!","result"=>$result,);
		 	return $ack;
		}
 }
 
 	
	function getOrderDetail($detail,$limit="")
    {
		extract($detail);
		$result = array();
		
		if($from_date!="" && $to_date!=""){
			$where="customer_id ='".$_REQUEST['customer_id']."' AND customer_type='".$_REQUEST['customer_type']."' AND DATE(created_date)>= '".date_format(date_create($_REQUEST['from_date']),"Y-m-d")."' And DATE(created_date)<='".date_format(date_create($_REQUEST['to_date']),"Y-m-d")."'";
			
			
		}
		else{
			$where="customer_id ='".$_REQUEST['customer_id']."' AND customer_type='".$_REQUEST['customer_type']."'";
		}
		
		$data    = $this->db->rp_getData('orders',"id,customer_id,customer_name,company_name,customer_type,contact_number,address,city,state,country,email,order_date,revised_date,status,total_amount,total_qty,discount,discount_type,grand_total,status,grand_total_rounded,proforma_invoice_id,order_no",$where,"id DESC",0,$limit);
		if($data)
		{
		while($r= mysqli_fetch_assoc($data))
		{
			
		$r['pro_forma_invoice_no'] = $this->db->rp_getValue("proforma_invoice_info","invoice_no","id='".$r['proforma_invoice_id']."'",0);
		 $request_id = $this->db->rp_getValue("proforma_invoice_info","request_id","id='".$r['proforma_invoice_id']."'",0);
		 $r['request_no'] = $this->db->rp_getValue("customer_order_request_info","request_no","id='".$request_id."'",0);
		 $r['customer_name'] = $this->db->rp_getValue("executive","cname","id='".$r['customer_id']."'",0);
		 $r['country'] = $this->db->rp_getValue("country","name","id='".$r['country']."'",0);
		 $r['state'] = $this->db->rp_getValue("state","name","id='".$r['state']."'",0);
		 $r['city'] = $this->db->rp_getValue("city","name","id='".$r['city']."'",0);
		 $r['status']= $r['status'];	
		 $r['status_slug']= $this->order_status[intval($r['status'])];
		 $r['status_slug']=($r['status_slug']!="")?$r['status_slug']:"";
		 $r['order_date']=date('d-m-Y',strtotime($r['order_date']));
		// $r['revised_date']=date('d-m-Y',strtotime($r['revised_date']));
		 $result[] = $r; 
		}
		if(!empty($result))
		{
		$ack=array( "ack"=>1,
		  "ack_msg"=>"Successfully Get Orders of Customer!!",
		  "developer_msg"=>"You got it!!",
		  "result"=>$result,
		  );
		  return $ack;
		}
		else
		{
		$ack=array( "ack"=>0,
		  "ack_msg"=>"No Orders Found !!",
		  "developer_msg"=>"Not found!!",
		  "result"=>$result,
		  );
		  return $ack;
		}
		}
		else
		{
		$ack=array( "ack"=>0,
		 "ack_msg"=>"No Orders Found !!",
		 "developer_msg"=>"Not found!!",
		 "result"=>$result,
		 );
		 return $ack;
		}
 }
	function getOrderItemDetail($order_id)
    {
		//get order item
	    $result = array();
	    $order_pro_detail =$this->db->rp_getData("orders","*","id='".$order_id."'","id DESC",0);//id,order_no,dealer_id,class_id,area_id,sales_id,sales_type,customer_id,customer_name,customer_type,remarks,contact_number,address,country,state,city,email,order_date,total_amount,total_qty,discount,discount_type,grand_total,created_date,status,adate,isDelete,isActive
		if($order_pro_detail)
		{
			$order_pro_detail=mysqli_fetch_assoc($order_pro_detail);
				$order_pro_detail['order_date']=date('Y-m-d',strtotime($order_pro_detail['order_date']));
				$order_pro_detail['country']=$this->db->rp_getValue("country","name","id='".$order_pro_detail['country']."'");
				$order_pro_detail['state']=$this->db->rp_getValue("state","name","id='".$order_pro_detail['state']."'");
				$order_pro_detail['city']=$this->db->rp_getValue("city","name","id='".$order_pro_detail['city']."'");
				$order_pro_detail['status']= $order_pro_detail['status'];
				$order_pro_detail['status_slug']= $this->order_status[intval($order_pro_detail['status'])];
						
				$where= "order_id='".$order_pro_detail['id']."'";
				$dt = $this->db->rp_getData("order_product_item","*",$where,"",0);
				$r = array();
				if($dt)
				{
					while($r=mysqli_fetch_assoc($dt))
					{	
						$r['adate']=date('d-m-Y',strtotime($r['adate']));
						$result[] = $r;	
						
					}	
				}
			$order_pro_detail['products']=$result;
			//print_r($order_pro_detail);
			if(!empty($order_pro_detail))
			{
				$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully Get Orders !!",
						"developer_msg"=>"You got it!!",
						"result"=>$order_pro_detail,
						);
						return $ack;
			}
			else
			{
				$ack=array( "ack"=>0,
						"ack_msg"=>"No Orders Found !!",
						"developer_msg"=>"No Customer found!!",
						"result"=>$result,
						);
						return $ack;
			}
		}
		else
		{
			$ack=array( "ack"=>0,
					"ack_msg"=>"No Orders Found !!",
					"developer_msg"=>"No Customer found!!",
					"result"=>$result,
					);
					return $ack;
		}
	}

 function getNoOrderInquiry($sales_id,$filter)
    {
        require_once("class.system.php");
        $system=new System();
		$limit=$system->getLimit();
    	$mobile_number = $_REQUEST['mobile_no'];
    	$company_name = $_REQUEST['company_name'];
    	$person_name = $_REQUEST['person_name'];
    	$status = $_REQUEST['status'];
    	$assign_to = $_REQUEST['assign_to'];
		$created_by = $_REQUEST['created_by'];
		$type = $_REQUEST['type'];
    	$status_type=array("0"=>"Generate","1"=>"In Followup"/*,"2"=>"Interested"*/,"-1"=>"Not Interested","3"=>"Buy Later","-2"=>"Non Relavent Inquiry","4"=>"Hot","5"=>"Cold","6"=>"Warm"/*,"7"=>"Wrong Call"*/,"8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"Lost");
		$result = array();
		$where="isDelete=0";
		if(array_key_exists("first_date", $filter) && $filter['first_date']!="" && array_key_exists("last_date", $filter) && $filter['last_date']!="")
		{
			$where.=" AND datetime	 >='".date("Y-m-d",strtotime($filter['first_date']))."' AND datetime <='".date("Y-m-d",strtotime($filter['last_date']))."' AND ";
		}

		if($mobile_number!="")
		{
			$where.=" AND mobile_number LIKE '%".$mobile_number."%' ";
		}
		
		if($company_name!="")
		{
			$where.=" AND company_name LIKE '%".$company_name."%' ";
		}

		if($person_name!="")
		{
			$where.=" AND person_name LIKE '%".$person_name."%' ";
		}

		if($status!="")
		{
			$where.=" AND status = '".$status."'";
		}

		if($assign_to!="")
		{
			$where.= " AND inquiry_assign_to = '".$assign_to."'";
		}

		if($created_by!="")
		{
			$where.= "  AND inquiry_created_by = '".$created_by."'";
		}

		if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
		{
		 	$where .= " AND datetime <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
		}

		if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
		{
			$where .= " AND datetime >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
		}
		if(isset($_REQUEST['id']) && $_REQUEST['id']!="" && $_REQUEST['id']!=NULL)
		{
			$where .= " AND id = '".$_REQUEST['id']."' ";
		}

		if($sales_id!="")
		{
			$where.= "  AND (inquiry_assign_to = '".$sales_id."' OR inquiry_created_by = '".$sales_id."')";
		}
		if($type!="")
		{
			$where.= "  AND inquiry_type = '".$type."'";
		}
		//$where.=" AND ( sales_executive_id	 ='".$sales_id."'";

		/*$sales_class_array = array();
		$get_sales_class = $this->db->rp_getData("sales_executive_map_area","DISTINCT class_id","sales_executive_id='".$sales_id."'","",0);
		while($get_sales_class_d = mysqli_fetch_assoc($get_sales_class))
		{
			$sales_class_array[] = $get_sales_class_d['class_id'];
		}
		$sales_class_array = implode(",",$sales_class_array);*/

		/*$sales_area_array = array();
		$get_sales_area = $this->db->rp_getData("sales_executive_map_area","DISTINCT area_id","sales_executive_id='".$sales_id."'","",0);
		while($get_sales_area_d = mysqli_fetch_assoc($get_sales_area))
		{
			//$get_sales_area_d['area_id'] = $get_sales_area_d['area_id'];

			$get_sales_area_d['sales_area_name'] = $this->db->rp_getValue("area","name","id='".$get_sales_area_d['area_id']."'");

			$sales_area_array[] = "'".$get_sales_area_d['sales_area_name']."'";
		}

		$sales_area_array = implode(",", $sales_area_array);

		$where.="  OR city IN (".$sales_area_array.") AND isDelete=0 )";
*/
		$data    = $this->db->rp_getData('no_order_inquiry',"id,local_id,sales_executive_id,company_name,person_name,mobile_number,contact_person,country,
			state,city,description,action,inquiry_date,datetime,isDelete,isActive,created_date,status,image_path,latitude,longitude,address,class_id,area_id,executive_type,other_mobile_no,distributor_id,source_of_inquiry,designation,zone,product_id,quantity,u_w_flag,u_w_remark,quotation_flag,quotation_remark,customer_requirement,birth_date,inquiry_created_by,inquiry_assign_to,inquiry_assign_date,dealer_id,email_address,inquiry_lead_flag,lead_date,date_of_call,gst_no,shipping_address,billing_address,industry_type_id",$where,"id DESC",0,$limit);
		if($data)
		{
			while($r= mysqli_fetch_assoc($data))
			{
				/*$inquiry_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp","4"=>"India Mart","5"=>"Just Dial","6"=>"Trade India","7"=>"Ali baba","8"=>"Facebook","9"=>"Instagram");*/

				$u_w_array = array("1"=>"Yes","2"=>"No");
				$quotation_array = array("1"=>"Yes","2"=>"No");
				$r['source_of_inquiry_slug'] = $this->db->rp_getValue("source_of_inquiry","name","id='".$r['source_of_inquiry']."' AND isDelete=0",0);
				//$r['source_of_inquiry_slug']	=$inquiry_type_array[$r['source_of_inquiry']];

				$r['u_w_flag_name']	=$u_w_array[$r['u_w_flag']];

				$r['quotation_flag_name']	=$u_w_array[$r['quotation_flag']];

				$weight_name = $this->db->rp_getValue("weight","name","id='".$r['product_id']."' AND isDelete=0",0);

				$r['prouct_name']	= $this->db->rp_getValue("product","name","id='".$r['product_id']."'",0)."-".$weight_name;
				
				$r['status_slug']	= $status_type[$r['status']];
				$r['is_inquiry']	= $r['inquiry_lead_flag'];
				$r['inq_no'] = "#INQ/".$r['id'];
				$r['color_code'] = $this->db->inquiry_status_color[$r['status_slug']];
				

				$r['sales_name'] = $this->db->rp_getValue("sales_executive","name","id='".$r['sales_id']."' AND type='".$r['sales_type']."'",0);
				
				/*$r['country_slug'] = $r['country'];
				$r['country'] = $this->db->rp_getValue("country","name","id='".$r['country']."'",0);*/

				$r['executive_type_slug'] = $this->db->rp_getValue("customer_type","name","id='".$r['executive_type']."'",0);

				$r['dealer_name']	= $this->db->rp_getValue("executive","company_name","id='".$r['dealer_id']."'",0);
				 
				/*$r['state_slug'] = $r['state'];		
				$r['state'] = $this->db->rp_getValue("state","name","id='".$r['state']."'",0);*/
				 
				/*$r['city_slug'] =  $r['city'];
				$r['city'] = $this->db->rp_getValue("city","name","id='".$r['city']."'",0);*/
				 
				$r['action_slug'] =  $r['action'];
				$r['action'] = $this->db->rp_getValue("no_order_inquiry_action","name","id='".$r['action']."'",0);
				$r['whatsapp_no'] = "91". $r['mobile_number']."";
			 	$img = explode(",", $r['image_path']);
			 	$imgpath = array();
				for ($i=0; $i < sizeof($img); $i++)
				{ 
					$imgpath[] = SITEURL."resource/image/".$this->db->rp_getValue("media","url","reference_id='".$r['id']."' AND id='".$img[$i]."'");
				}
				$r['image_path'] = ($r['image_path']!= "")?$imgpath:[];

				$r['created_date']=date('d F Y h:i A',strtotime($r['created_date']));
				if($r['datetime']!="1970-01-01" && $r['datetime']!="0000-00-00")
				{
					$r['datetime']=date('d-m-Y h:i A',strtotime($r['datetime']));
				}
				else
				{
					$r['datetime'] = "";	
				}

				if($r['lead_date']!="1970-01-01" && $r['lead_date']!="0000-00-00")
				{
					$r['lead_date']=date('d F Y',strtotime($r['lead_date']));
				}
				else
				{
					$r['lead_date'] = "";	
				}
				$r['inquiry_date']=date('d F Y',strtotime($r['inquiry_date']));
				if($r['birth_date']!="1970-01-01")
				{
					$r['birth_date']=date('d F Y',strtotime($r['birth_date']));
				}
				else
				{
					$r['birth_date'] = "";	
				}
				if($r['date_of_call']!="1970-01-01")
				{
					$r['date_of_call']=date('d F Y',strtotime($r['date_of_call']));
				}
				else
				{
					$r['date_of_call'] = "";	
				}

				if($r['inquiry_assign_date']!="1970-01-01" && $r['inquiry_assign_date']!="0000-00-00")
				{
					$r['inquiry_assign_date']=date('d F Y',strtotime($r['inquiry_assign_date']));
				}
				else
				{
					$r['inquiry_assign_date'] = "";	
				}

				$r['inquiry_created_by_name']=$this->db->rp_getValue("sales_executive","name","id='".$r['inquiry_created_by']."'");
				$r['inquiry_assign_to_name']=$this->db->rp_getValue("sales_executive","name","id='".$r['inquiry_assign_to']."'");
				
				$result[] = $r; 
			}

			/*Get Inquiry Status*/
				$inquiry_status = array();
				$InquiryData = $this->db->rp_getData('no_order_inquiry',"DISTINCT(status)","(inquiry_assign_to = '".$sales_id."' OR inquiry_created_by = '".$sales_id."') AND inquiry_type = '".$type."' AND isDelete=0","",0);

				$inquiry_status_array=array("0"=>"Generate","1"=>"In Followup"/*,"2"=>"Interested"*/,"-1"=>"Not Interested","3"=>"Working","-2"=>"Non Relavent Inquiry","4"=>"Hot","5"=>"Cold","6"=>"Warm"/*,"7"=>"Wrong Call"*/,"8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"Lost");

				$inquiry_status_key = array("0","1"/*,"2"*/,"-1","3","-2","4","5","6"/*,"7"*/,"8","9","10","11");
				while($Inquiry_d = mysqli_fetch_assoc($InquiryData))
				{
					if($_REQUEST['sales_id']!="")
					{
						$Inquiry_d['count']=$this->db->rp_getTotalRecord("no_order_inquiry","(inquiry_assign_to = '".$sales_id."' OR inquiry_created_by = '".$sales_id."')  AND status='".$Inquiry_d['status']."' AND inquiry_type = '".$type."' AND isDelete=0",0);
					}
					else
					{
						$Inquiry_d['count']=$this->db->rp_getTotalRecord("no_order_inquiry","customer_id ='".$_REQUEST['customer_id']."' AND status='".$Inquiry_d['status']."' AND isDelete=0",0);
					}
				

					if (($key_inquiry = array_search($Inquiry_d['status'], $inquiry_status_key)) !== false) {
					    unset($inquiry_status_key[$key_inquiry]);
					}

					$Inquiry_d['status_slug'] = $inquiry_status_array[$Inquiry_d['status']];
					$Inquiry_d['status'] = $Inquiry_d['status'];

					$Inquiry_d['color_code'] = $this->db->inquiry_status_color[$Inquiry_d['status_slug']];

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
					$Inquiry_d['color_code'] = $this->db->inquiry_status_color[$inquiry_status_array[$remainval_inquiry]];
					$inquiry_status[]=$Inquiry_d;
				}
			/*Get Inquiry Status*/

			if(!empty($result))
			{
				$ack=array("ack"=>1,"ack_msg"=>"Successfully Get Inquiry !!","developer_msg"=>"You got it!!","result"=>$result,"inquiry_status"=>$inquiry_status);
			  	return $ack;
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"No Inquiry Found !!","developer_msg"=>"Not found!!","result"=>$result);
			  	return $ack;
			}
		}
		else
		{
			$ack=array( "ack"=>0,"ack_msg"=>"No Inquiry Found !!","developer_msg"=>"Not found!!","result"=>$result);
		 	return $ack;
		}
 }
  
//-----------------------------------------------------------------------------//
//24-04-2017-----------------#use for get order Product Item upon order service#---------------------------------------------------------//	
    function getOrders_forItem($order_id)
    {
		//get order item
	    $result = array();

		$order_pro_detail=mysqli_fetch_assoc($this->db->rp_getData("orders","*","id='".$order_id."'","id DESC",0));

				$order_pro_detail['gst_no']=$order_pro_detail['gst'];
				$order_pro_detail['quotation_no']=$this->db->rp_getValue("quotation_detail","quotation_no","id='".$order_pro_detail['quotation_id']."'")."";

				$order_pro_detail['quotation_date']=$this->db->rp_getValue("quotation_detail","quotation_date","id='".$order_pro_detail['quotation_id']."'")."";
				$order_pro_detail['quotation_date']=date('d-m-Y',strtotime($order_pro_detail['quotation_date']));
				if($order_pro_detail['quotation_date']=="01-01-1970" || $order_pro_detail['quotation_date']=="00-00-0000"){

					$order_pro_detail['quotation_date']="";
				}else{
				
					$order_pro_detail['quotation_date']=date('d F Y',strtotime($order_pro_detail['quotation_date']));
				}

				$order_pro_detail['po_date']=date('d-m-Y',strtotime($order_pro_detail['po_date']));
				if($order_pro_detail['po_date']=="01-01-1970" || $order_pro_detail['po_date']=="00-00-0000"){

					$order_pro_detail['po_date']="";
				}else{
				$order_pro_detail['po_date']=date('d F Y',strtotime($order_pro_detail['po_date']));

				}
				$order_pro_detail['order_date']=date('d F Y',strtotime($order_pro_detail['order_date']));
				$order_pro_detail['transport_through_name']=$this->db->rp_getValue("transport_by","name","id='".$order_pro_detail['transport_through']."'",0);

				$order_pro_detail['transport_by_name']=$this->db->rp_getValue("transport_master","name","id='".$order_pro_detail['transport_name']."'",0);

				$where= "order_id='".$order_pro_detail['id']."'";
				$dt = $this->db->rp_getData("order_product_item","*",$where);
				$r = array();
				if($dt)
				{
					$subtotal=0;
					$taxable_amount=0;
					$grandtotal=0;
					while($r=mysqli_fetch_assoc($dt))
					{					
				
						$unit_id = $this->db->rp_getValue("product", "unit_id", "id='".$r['pro_id']."'","",0);
						
						$r['unit_name'] = $this->db->rp_getValue("unit", "name", "id='" . $unit_id . "'",0);

						$r['stock']=$this->db->rp_getValue("product_weight_price","stock_qty","weight_id='".$r['weight_id']."' AND product_id='".$r['pro_id']."'  AND isDelete=0");


						$r['hsn_code'] = $this->db->rp_getValue("product", "hsn_code", "id='".$r['pro_id']."'","",0);
					
						$subtotal+=$r['totalprice'];
						$item_gst_total+=$r['item_gst_amount'];
						$GST=$this->db->rp_getValue("product","igst","id='".$r['pro_id']."'");
						$order_pro_detail['status']= $order_pro_detail['status'];
						$order_pro_detail['status_slug']= $this->order_status[intval($order_pro_detail['status'])];
						$order_pro_detail['color_code'] = $this->db->status_color[$order_pro_detail['status_slug']];
						$r['adate']=date('d-m-Y',strtotime($r['adate']));
						$result[] = $r;	
					}	
				}
				
				if($order_pro_detail)
				{

		    		$after_additional_discounted = 0;
					$after_cash_discounted = 0;

		    		
					$cash_discount = $order_pro_detail['cash_discount'];
					$cash_discount_amount = $order_pro_detail['cash_discount_amount'];
					$after_cash_discounted = $this->db->rp_num($subtotal - $cash_discount_amount);


					$additional_discount = $order_pro_detail['additional_discount'];
					$additional_discount_amount =  $order_pro_detail['additional_discount_amount'];
					$after_additional_discounted = $after_cash_discounted - $additional_discount_amount;
					

					$final_total = $after_additional_discounted;

		    		$sub_total = $this->db->rp_num((float)$sub_total, 2, '.', '');
		    		
		    		//$final_total = $final_total+$order_d['packing_charge']+$order_d['transport_charge'];
		    		$after_packing_charge=$final_total+$order_pro_detail['packing_charge'];
		    		$after_transport_charge=$after_packing_charge+$order_pro_detail['transport_charge'];
		    		
					$final_total=$after_transport_charge;
					$gst_amount=$order_pro_detail['igst_amount'];
					$tcs_amount=$order_pro_detail['tcs_amount'];
					$after_gst=$this->db->rp_num($final_total+$gst_amount);
					$grandtotal=$this->db->rp_num($final_total+$gst_amount+$tcs_amount);
					$before_roundoff=$this->db->rp_num($grandtotal,2);
					$whole = floor($before_roundoff);
			        $fraction = $before_roundoff - $whole;
			        $f1=  $this->db->rp_num((float)$fraction, 2, '.', '');
					$roundoff=$this->db->rp_num($f1,2);
			       
					//$grand_total=strval($this->db->rp_num($order_pro_detail['grand_total_rounded'],2));
					$grand_total=$this->db->rp_num(round($grandtotal),2);
					if($order_pro_detail['igst_amount']!="" && $order_pro_detail['igst_amount']!=0){
							//echo $customer_state;exit;
						/*	if (strtolower(CLIENT_STATE) == strtolower($order_pro_detail['cuatomer_state'])) 
							{
								$GST = "(CGST:9%,SGST:9%)";
							} else
							{
								$GST = "(IGST:18%)";
							}*/
					$customer_state = $this->db->rp_getValue("executive", "state", "id='" . $order_pro_detail['customer_id'] . "'",0);

							if (strtolower(CLIENT_STATE) == strtolower($customer_state)) 
							{
								if($order_pro_detail['customer_type']==7){
									$GST = "(CGST:0.05%,SGST:0.05%)";
								}else{
									$GST = "(CGST:9%,SGST:9%)";
								}
								
							} else
							{
								if($order_pro_detail['customer_type']==7){
									$GST = "(IGST:0.01%)";
								}else{
									$GST = "(IGST:18%)";
								}
								 
							}
						}else{
							$GST="";
						}

					$order_pro_detail['gst']=$GST;
					//$order_pro_detail['gst']="";
					$order_pro_detail['gst_amount']=$this->db->rp_num($gst_amount,2);
					$order_pro_detail['after_additional_discounted']=$this->db->rp_num($after_additional_discounted,2);
					$order_pro_detail['after_cash_discounted']=$this->db->rp_num($after_cash_discounted,3);
					//$order_pro_detail['after_transport_charge']=$this->db->rp_num($after_transport_charge,2);
					//$order_pro_detail['after_packing_charge']=$this->db->rp_num($after_packing_charge,2);

					$order_pro_detail['additional_discount_amount']=$this->db->rp_num($order_pro_detail['additional_discount_amount'],2);
					$order_pro_detail['cash_discount_amount']=$this->db->rp_num($order_pro_detail['cash_discount_amount'],2);
					$order_pro_detail['after_transport_charge']=$this->db->rp_num($order_pro_detail['transport_charge'],2);
					$order_pro_detail['after_packing_charge']=$this->db->rp_num($order_pro_detail['packing_charge'],2);
					$order_pro_detail['tcs_amount']=$this->db->rp_num($order_pro_detail['tcs_amount'],2);
						
					$order_pro_detail['final_total']=$this->db->rp_num($final_total,2);
					$order_pro_detail['subtotal']=$this->db->rp_num($subtotal,2);
					$order_pro_detail['roundoff']=$roundoff;
					$order_pro_detail['grand_total']=$grand_total;

			$order_pro_detail['products']=$result;
			
			if(!empty($result))
			{
				$ack=array( "ack"=>1,
						"ack_msg"=>"Successfully Get Orders !!",
						"developer_msg"=>"You got it!!",
						"result"=>$order_pro_detail,
						);
						return $ack;
			}
			else
			{
				$ack=array( "ack"=>0,
						"ack_msg"=>"No Orders Found !!",
						"developer_msg"=>"No Customer found!!",
						"result"=>$result,
						);
						return $ack;
			}
		}
		else
		{
			$ack=array( "ack"=>0,
					"ack_msg"=>"No Orders Found !!",
					"developer_msg"=>"No Customer found!!",
					"result"=>$result,
					);
					return $ack;
		}
	}

	function getProduct($detail,$limit=""){
		// WHERE PARSING START //

			$uid=isset($_REQUEST['uid'])?$_REQUEST['uid']:"";
			$where="";
			if(isset($detail['tcid']) && $detail['tcid']!=""  && $detail['cid']=="")
			{
				$tcid=$detail['tcid'];
				$where="isDelete=0 AND isActive=1 AND tcid='".$tcid."'";
			}
			else if(isset($detail['cid']) && $detail['cid']!="")
			{
				$cid=$detail['cid'];
				$where="isDelete=0 AND isActive=1 AND id='".$cid."'";
			}
			else{
				$where="isDelete=0 AND isActive=1 ";
			}
			
			
		// WHERE PARSING OVER //
		
		$price_list_id=$this->db->rp_getValue("customer","price_list_id","id='".$detail['uid']."' AND isDelete=0",0);
		$hcat_r = $this->db->rp_getData("category_master","*",$where,"",0);
		if($hcat_r){
			$product=array();
			$category=array();
			while($hcat_d = mysqli_fetch_assoc($hcat_r)){
				//Fetching Only Id then using function getProductDetail get Information of that product
				$category_id=$hcat_d['id'];				
				$pro_r=	$this->db->rp_getData("product","*","isDelete=0 AND isActive=1 AND cid=".$category_id,"display_order ASC",0,$limit);
				if($pro_r){
					
					while($pro_d=mysqli_fetch_assoc($pro_r)){
						$pro_d['cat_name']=$this->db->rp_getValue("category_master","name","id='".$pro_d['cid']."' AND isDelete=0",0);
						$pro_d['top_cat_name']=$this->db->rp_getValue("top_category_master","name","id='".$pro_d['tcid']."' AND isDelete=0",0);
						$pro_d['product_code']=$this->db->rp_getValue("product_weight_price","catno","product_id='".$pro_d['id']."' AND isDelete=0",0);
						$pro_d['unitname']=$this->db->rp_getValue("unit","name","id='".$pro_d['unit_id']."' AND isDelete=0",0);

						$pro_d['total_size']=$this->db->rp_getTotalRecord("product_weight_price","product_id='".$pro_d['id']."' AND isDelete=0",0);
						$descr=html_entity_decode($pro_d['descr']);
						$descr=strip_tags($descr);
						$descr=str_replace("\r\n","",$descr);
						$descr=str_replace(",",",<br/>",$descr);
						$pro_d['descr']=$descr;
						$pid=$pro_d['id'];
						if($pro_d['image_path']!=""){
							$pro_d['image_path']=SITEURL.PRODUCT.$pro_d['image_path'];
							
						}
						$user_discount=$this->db->rp_getValue("price_table","discount","tcid='".$pro_d['tcid']."' AND uid='".$uid."' AND isDelete=0",0);
						
						$price_r=	$this->db->rp_getData("product_weight_price","id,weight_id,price,product_id,inner_size,outer_size,catno,stock_qty","isDelete=0 AND product_id=".$pid,"",0);
						
						if($price_r){
							$product_weight_price=array();
							while($price_d=mysqli_fetch_assoc($price_r))
							{
								$price_d['original_price']=$this->db->rp_number_format($price_d['price'],2);
								/*if($user_discount!=0 && $user_discount!="")
								{
									$discount_amount=($price_d['price']*$user_discount)/100;
									$price_d['price']=$this->db->rp_number_format($price_d['price']-$discount_amount);
									$price_d['discount']=$user_discount;									
									$price_d['discount_amount']=$this->db->rp_number_format($discount_amount);									
								}
								else
								{
									$price_d['price']=$this->db->rp_number_format($price_d['price']);
									$price_d['discount']=0;									
									$price_d['discount_amount']=0;	
								}*/
								$price_d['price']=$this->db->rp_number_format($price_d['price'],2);
								$price_d['discount']=0;									
								$price_d['discounted_amount']=0;
								if($price_list_id!=0)
								{
									$check_product_in_list=$this->db->rp_getTotalRecord("product_price_list","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."' AND price_list_id='".$price_list_id."'",0);
									if($check_product_in_list>0)
									{
										$price_d['price']=$this->db->rp_number_format($this->db->rp_getValue("product_price_list","discounted_price","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."' AND price_list_id='".$price_list_id."'"),2);
										$price_d['discount']=$this->db->rp_number_format($this->db->rp_getValue("product_price_list","discount","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."' AND price_list_id='".$price_list_id."'"),2);

										$price_d['discounted_amount']=$this->db->rp_number_format($this->db->rp_getValue("product_price_list","discounted_amount","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."'"),2);
									}
									
								}
								
								$price_d['name']=$this->db->rp_getValue("weight","name","id='".$price_d['weight_id']."'");
								$price_d['display_order']=$this->db->rp_getValue("weight","display_order","id='".$price_d['weight_id']."'");
								$product_weight_price[]=$price_d;
								$this->sortBy('display_order', $product_weight_price, 'asc');
							}
							$pro_d['product_weight_price']=$product_weight_price;
							$product[]=$pro_d;
						}
					}
					
				}
				// print_r($product);exit;
				$hcat_d['products']=$product;
				// print_r($hcat_d);
				array_push($category,$product);
				// print_r($category);
			}
			if(!empty($product))
			{
				$ack=array("ack"=>1,"products"=>$product);
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"No Prodcuts found!!!");
			}	
			return $ack;
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"No Prodcuts found!!!");
			return $ack;
		}
		
		
	}


	function getProductPriceList($detail,$limit="")
	{
		// WHERE PARSING START //
			$uid=isset($_REQUEST['uid'])?$_REQUEST['uid']:"";
			$where="";
			if(isset($detail['tcid']) && $detail['tcid']!=""  && $detail['cid']=="")
			{
				$tcid=$detail['tcid'];
				$where="isDelete=0 AND isActive=1 AND tcid='".$tcid."'";
			}
			else if(isset($detail['cid']) && $detail['cid']!="")
			{
				$cid=$detail['cid'];
				$where="isDelete=0 AND isActive=1 AND id='".$cid."'";
			}
			else{
				$where="isDelete=0 AND isActive=1 ";
			}
		// WHERE PARSING OVER //
		
		$price_list_id=$this->db->rp_getValue("customer","price_list_id","id='".$detail['uid']."' AND isDelete=0",0);
		$hcat_r = $this->db->rp_getData("category_master","*",$where,"",0);
		if($hcat_r){
			$product=array();
			$category=array();
			while($hcat_d = mysqli_fetch_assoc($hcat_r)){
				//Fetching Only Id then using function getProductDetail get Information of that product
				$category_id=$hcat_d['id'];				
				$pro_r=	$this->db->rp_getData("product","*","isDelete=0 AND isActive=1 AND cid=".$category_id,"display_order ASC",0,$limit);
				if($pro_r){
					
					while($pro_d=mysqli_fetch_assoc($pro_r)){
						$pro_d['cat_name']=$this->db->rp_getValue("category_master","name","id='".$pro_d['cid']."' AND isDelete=0",0);
						$pro_d['product_code']=$this->db->rp_getValue("product_weight_price","catno","product_id='".$pro_d['id']."' AND isDelete=0",0);
						$pro_d['total_size']=$this->db->rp_getTotalRecord("product_weight_price","product_id='".$pro_d['id']."' AND isDelete=0",0);
						$descr=html_entity_decode($pro_d['descr']);
						$descr=strip_tags($descr);
						$descr=str_replace("\r\n","",$descr);
						$descr=str_replace(",",",<br/>",$descr);
						$pro_d['descr']=$descr;
						$pid=$pro_d['id'];
						if($pro_d['image_path']!=""){
							$pro_d['image_path']=SITEURL.PRODUCT.$pro_d['image_path'];						
						}
						
						$price_r=	$this->db->rp_getData("product_weight_price","id,weight_id,price,product_id,inner_size,outer_size,catno","isDelete=0 AND product_id=".$pid,"",0);
						
						if($price_r){
							$product_weight_price=array();
							while($price_d=mysqli_fetch_assoc($price_r))
							{
								$price_d['original_price']=$price_d['price'];								
								$price_d['price']=$this->db->rp_number_format($price_d['price']);
								$price_d['discount']=0;									
								$price_d['discounted_amount']=0;
								if($price_list_id!=0)
								{
									$check_product_in_list=$this->db->rp_getTotalRecord("product_price_list","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."' AND price_list_id='".$price_list_id."'",0);
									if($check_product_in_list>0)
									{
										$price_d['price']=$this->db->rp_getValue("product_price_list","discounted_price","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."' AND price_list_id='".$price_list_id."'");
										$price_d['discount']=$this->db->rp_getValue("product_price_list","discount","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."' AND price_list_id='".$price_list_id."'");
										$price_d['discounted_amount']=$this->db->rp_getValue("product_price_list","discounted_amount","pid='".$price_d['product_id']."' AND weight_id='".$price_d['weight_id']."'");
									}									
								}								
								$price_d['name']=$this->db->rp_getValue("weight","name","id='".$price_d['weight_id']."'");
								$price_d['display_order']=$this->db->rp_getValue("weight","display_order","id='".$price_d['weight_id']."'");
								$product_weight_price[]=$price_d;
								$this->sortBy('display_order', $product_weight_price, 'asc');
								
							}
							$pro_d['product_weight_price']=$product_weight_price;
							$product[]=$pro_d;
						}
					}
					
				}
				// print_r($product);exit;
				$hcat_d['products']=$product;
				// print_r($hcat_d);
				array_push($category,$product);
				// print_r($category);
			}
			if(!empty($product))
			{
				$ack=array("ack"=>1,"products"=>$product);
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"No Prodcuts found!!!");
			}	
			return $ack;
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"No Prodcuts found!!!");
			return $ack;
		}
		
		
	}

	function sortBy($field, &$array, $direction = 'asc')
	{
	    usort($array, create_function('$a, $b', '
	        $a = $a["' . $field . '"];
	        $b = $b["' . $field . '"];

	        if ($a == $b) return 0;

	        $direction = strtolower(trim($direction));

	        return ($a ' . ($direction == 'desc' ? '>' : '<') .' $b) ? -1 : 1;
	    '));

	    return true;
	}

	
}
	
?>