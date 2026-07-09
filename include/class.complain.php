
<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("notification.class.php");
require_once("class.system.php");
class Complain extends Functions
{
	public $db;
	public $ctable="complain";
	//public $sales_type_title=array("sales_manager"=>"Sales Manager","area_sales_manager"=>"Area Sales Manager","sales_officer"=>"Area Sales Manager","sales_executive"=>"Sales Officer");
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;		   
		$this->notification=new Notification();		   
		$this->system=new System();		   
    } 

//-----------------------------------------------------------------------------------------------//
	public function AddComplain($detail,$file)
	{
		// print_r($file);exit;
		extract($detail);

		/*if($complain_assign_to!="")
		{
			$complain_assign_to = $complain_assign_to;
		}
		else
		{ 
			$complain_assign_to = 18;
		}*/

		$complain_date = date('Y-m-d');
		$value=$this->db->getlastInsertId($this->ctable);
		$complain_no=CUSTOMER_COMPLAIN_NO.str_pad($value, 3, '0', STR_PAD_LEFT);

		$state = $this->db->rp_getValue("executive","state","id='".$customer_id."'");
		$city = $this->db->rp_getValue("executive","city","id='".$customer_id."'");
		$zone = $this->db->rp_getValue("executive","zip","id='".$customer_id."'");
		$contact_person = $this->db->rp_getValue("executive","cname","id='".$customer_id."'");


		$rows 	= array(
				"user_id",
				"customer_id",
				"contact_person",
				/*"dealer_id",*/
				"latitude",
				"longitude",
				"app_address",
				"remark",
				/*"class_id",
				"area_id",*/
				"title",
				"complain_no",
				"isActive",
				"entry_flag",
				"complain_type",
				"complain_cat_id",
				"complain_subcat_id",
				"complain_created_by",
				"complain_assign_to",
				"complain_date",
				"state",
				"city",
				"zone",
				"product_id",
				"customer_requirement",
				"type_of_company",
			);
		$values = array(
				$user_id,
				$customer_id,
				$contact_person,
				/*$dealer_id,*/
				$latitude,
				$longitude,
				$app_address,
				$remark,
				/*$class_id,
				$area_id,*/
				$title,
				$complain_no,
				1,
				$entry_flag,
				$complain_type,
				$complain_cat_id,
				$complain_subcat_id,
				$complain_created_by,
				$complain_assign_to,
				date('Y-m-d',strtotime($complain_date)),
				$state,
				$city,
				$zone,
				$product_id,
				$customer_requirement,
				$type_of_company,
			);
		$eid = $this->db->rp_insert($this->ctable,$values,$rows,0);

		/*add entry in serice form*/
			$customer_name = $this->db->rp_getValue("executive","cname","id='".$customer_id."'");
			$contact_person = $this->db->rp_getValue("executive","company_name","id='".$customer_id."'");
			$contact_no = $this->db->rp_getValue("executive","phone","id='".$customer_id."'");
			$service_keys = array("complain_id","service_no","complain_date","customer_name","address","contact_person_name","contact_no");
			$service_values = array($eid,$complain_no,$complain_date,$customer_name,$app_address,$contact_person,$contact_no);
			$this->db->rp_insert("complain_service",$service_values,$service_keys,0);
		/*add entry in serice form*/

		if($detail['application_type']=="1"){//for flutter
			//echo "string";
			$image_path=array();
			// print_r($_POST['image_path']);exit;

			if (isset($_POST['image_path'])) 
			{	
				//echo "string1";exit;
				$allowedExts = array("jpg","jpeg","png","JPG","JPEG");
				//$doc = ['zip', 'rar', 'pdf', 'doc', 'docx', 'xls','xlsx','ppt','pptx'];
				$whitelistExt = array_merge($img);
				//print_r($_POST['attachment']);
				$a=$_POST['image_path'];
				$a = json_decode($a, TRUE);

					for($i=0; $i<sizeof($a); $i++){
					    $fn = $a[$i]['fileName'];
					    $ext = pathinfo($fn, PATHINFO_EXTENSION);
					    if(!in_array($extension, $allowedExts)){
						$error .= "Extension not allowed. ";
						}
					    $f = base64_decode( $a[$i]['encoded']);
					   	$extension = end(explode(".", $fn));
					   	$attachment="../resource/image/";
						//$fileName	 = 'image_path'.substr(sha1(time()), 0, 6).".".$extension;
					    file_put_contents($attachment.$fn, $f);

				    	$MediaTitle=$fn;
				    	$MediaOrignalTitle=$fn;
				    	$MediaFileName=$fn;
				    	$UploadDate=date("Y-m-d H:i:s");
		    			$ri = $eid;
						$rt = "complain";
						$tc = "complain";
						$rc = "id";

						$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
						// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
						$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
						$MediaID=$this->db->rp_insert("media",$Values,$Columns,0);
							

						$image_path[] = $MediaID;
					}
				}
				//print_r($image_path);exit;
				if($image_path!=null)
				{
					$image_path1 = implode(",", $image_path);
				}else
				{
					$image_path1="";
				}

			//$image_path = implode(",", $image_path);
			$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path1),"id='".$eid."'",0);
		
		}else{


			$image_path=array();
			if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
			{
				$ri = $eid;
				$rt = "complain";
				$tc = "complain";
				$rc = "id";
				for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
				{
					//print_r($file["image_path"]);
					$file_name = $file['image_path']['name'][$i];
					$file_size = $file['image_path']['size'][$i];
					$file_tmp = $file['image_path']['tmp_name'][$i];
					$file_type = $file['image_path']['type'][$i];
					$extension=explode(".",$file_name);
					
					$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG");
					$extension=$extension[sizeof($extension)-1];
					if(!in_array($extension,$allowed_extentions))
					{
						$file_error=true;
					}
					$orignal_file_name=$extension[0];
					if(in_array($extension,$allowed_extentions))
					{
						$attachment="../resource/image/";
						move_uploaded_file($file_tmp,$attachment.$file_name);
					}
					$MediaTitle=$file_name;
			    	$MediaOrignalTitle=$file_name;

					$MediaFileName=$file_name;
					// $MediaType=User::$ValidMediaType[$extension];
					$UploadDate=date("Y-m-d H:i:s");
					
					// $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
					$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
					// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
					$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
					$MediaID=$this->db->rp_insert("media",$Values,$Columns,0);

					$image_path[] = $MediaID;
				}
				$image_path = implode(",", $image_path);
				$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$eid."'",0);
			}
		}		

		if($eid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Complain Add successfully!!","ack_msg"=>"Complain Add successfully!!","id"=>$eid);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Not Add");
			return $reply;
		}
	}

	public function UpdateComplain($detail,$file)
	{
		// print_r($file);exit;
		extract($detail);
		$rows 	= array(
			"user_id"     =>$user_id,
			"customer_id" =>$customer_id,
			"dealer_id"   =>$dealer_id,
			"latitude"    =>$latitude,
			"longitude"   =>$longitude,
			"app_address" =>$app_address,
			"remark"      =>$remark,
			"title"       =>$title,
		);
		
		$Where = "id='".$id."'";
		$eid = $this->db->rp_update($this->ctable,$rows,$Where,0);
		
		$image_path=array();
		/*if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
		{
			$ri = $eid;
			$rt = "complain";
			$tc = "complain";
			$rc = "id";
			for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
			{
				//print_r($file["image_path"]);
				$file_name = $file['image_path']['name'][$i];
				$file_size = $file['image_path']['size'][$i];
				$file_tmp = $file['image_path']['tmp_name'][$i];
				$file_type = $file['image_path']['type'][$i];
				$extension=explode(".",$file_name);
				
				$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG");
				$extension=$extension[sizeof($extension)-1];
				if(!in_array($extension,$allowed_extentions))
				{
					$file_error=true;
				}
				$orignal_file_name=$extension[0];
				if(in_array($extension,$allowed_extentions))
				{
					$attachment="../resource/image/";
					move_uploaded_file($file_tmp,$attachment.$file_name);
				}
				$MediaTitle=$file_name;
		    	$MediaOrignalTitle=$file_name;

				$MediaFileName=$file_name;
				// $MediaType=User::$ValidMediaType[$extension];
				$UploadDate=date("Y-m-d H:i:s");
				
				// $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
				$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
				// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
				$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
				$MediaID=$this->db->rp_insert("media",$Values,$Columns,0);

				$image_path[] = $MediaID;
			}
			$image_path = implode(",", $image_path);
			$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$eid."'",0);
		}*/		

		if($eid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Complain Update successfully!!","ack_msg"=>"Complain Update successfully!!");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Not Update");
			return $reply;
		}
	}

	public function UpdateComplainApi($detail,$file)
	{
		// print_r($file);exit;
		extract($detail);
		/*if($complain_assign_to!="")
		{
			$complain_assign_to = $complain_assign_to;
		}
		else
		{ 
			$complain_assign_to = 18;
		}*/

		$rows 	= array(
			"user_id"     =>$user_id,
			"customer_id" =>$customer_id,
			"dealer_id"   =>$customer_id,
			"complain_type"=>$complain_type,
			"complain_cat_id"=>$complain_cat_id,
			"complain_subcat_id"=>$complain_subcat_id,
			"complain_created_by"=>$complain_created_by,
			"complain_assign_to"=>$complain_assign_to,
			"product_id"=>$product_id,
			"customer_requirement"=>$customer_requirement,
			"update_entry_flag"=>$entry_flag,
			"remark"      =>$remark,
			"type_of_company"      =>$type_of_company,
			"complain_date"  =>date('Y-m-d',strtotime($complain_assign_date)),
			
		);
		
		$Where = "id='".$complain_id."'";
		$eid = $this->db->rp_update($this->ctable,$rows,$Where,0);
		
		$image_path=array();
		

		if($eid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Complain Update successfully!!","ack_msg"=>"Complain Update successfully!!");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Not Update");
			return $reply;
		}
	}
	
	public function AddComplainPanel($detail,$file)
	{	
		// print_r($detail);exit();
		extract($detail);
		$complain_date = date('Y-m-d',strtotime($complain_date));
		$value1=$this->db->getlastInsertId($this->ctable);
		$complain_no1=CUSTOMER_COMPLAIN_NO.str_pad($value1, 3, '0', STR_PAD_LEFT);

		/*if($complain_assign_to!="")
		{
			$complain_assign_to = $complain_assign_to;
		}
		else
		{
			//$complain_assign_to = $this->db->rp_getValue("sales_executive","id","type='service_executive' AND isDelete=0 AND isActive=1");
			$complain_assign_to = 18;
		}*/

		$rows_insert = array(
		    "user_id",
		    "customer_id",
		    "complain_type",
		    "complain_cat_id",
		    "complain_subcat_id",
		    "remark",
		    "state",
		    "city",
		    "zone",
		    "complain_created_by",
		    "complain_assign_to",
		    "complain_date",
		    "complain_no",
		    "isActive",
		    "entry_flag",
		    "product_id",
		    "quantity",
		    "u_w_flag",
		    "u_w_remark",
		    "quotation_flag",
		    "quotation_remark",
		    "customer_requirement",
		    "type_of_company",
		    "product_sub_category",
		    "executive_type"
		);

		$values_insert = array(
		    $complain_created_by,
		    $customer_id,
		    $complain_type,
		    $complain_cat_id,
		    $complain_subcat_id,
		    $remark,
		    $state,
		    $city,
		    $zone,
		    $complain_created_by,
		    $complain_assign_to,
		    $complain_date,
		    $complain_no1,
		    1,
		    1,
		    $product_id,
		    $quantity,
		    $u_w_flag,
		    $u_w_remark,
		    $quotation_flag,
		    $quotation_remark,
		    $customer_requirement,
		    $company_type,
		    $product_sub_category,
		    $executive_type
		);

		$insert = $this->db->rp_insert($this->ctable, $values_insert, $rows_insert, 0);

		
		if($insert!=0)
		{
			/*add entry in serice form*/
			$customer_name = $this->db->rp_getValue("executive","cname","id='".$customer_id."'");
			$contact_person = $this->db->rp_getValue("executive","company_name","id='".$customer_id."'");
			$contact_no = $this->db->rp_getValue("executive","phone","id='".$customer_id."'");
			$service_keys = array("complain_id","service_no","complain_date","customer_name","address","contact_person_name","contact_no","remark");
			$service_values = array($insert,$complain_no1,$complain_date,$customer_name,$address,$contact_person,$contact_no,$remark);
			$this->db->rp_insert("complain_service",$service_values,$service_keys,0);
			/*add entry in serice form*/

			/*image code*/
			$image_path=array();
			if (isset($file["image_path"]) && $file["image_path"]['size']!=[0])
			{
				$ri = $insert;
				$rt = "complain";
				$tc = "complain";
				$rc = "id";
				for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
				{
					$file_name = $file['image_path']['name'][$i];
					$file_size = $file['image_path']['size'][$i];
					$file_tmp = $file['image_path']['tmp_name'][$i];
					$file_type = $file['image_path']['type'][$i];
					$extension=explode(".",$file_name);

					$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG");
					$extension=$extension[sizeof($extension)-1];
					if(!in_array($extension,$allowed_extentions))
					{
						$file_error=true;
					}
					$orignal_file_name=$extension[0];
					if(in_array($extension,$allowed_extentions))
					{
						$attachment="../resource/image/";
						move_uploaded_file($file_tmp,$attachment.$file_name);
					}

					$MediaTitle=$file_name;
		    		$MediaOrignalTitle=$file_name;
		    		$MediaFileName=$file_name;
		    		$UploadDate=date("Y-m-d H:i:s");


					$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
					$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
					$MediaID=$this->db->rp_insert("media",$Values,$Columns,0);
					$image_path[] = $MediaID;
				}

				$image_path = implode(",", $image_path);
				$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$insert."'",0);
			}
			/*image code*/
			$reply=array("ack"=>1,"developer_msg"=>"Complain Add successfully!!","ack_msg"=>"Complain Add successfully!!","id"=>$insert);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Not Add");
			return $reply;
		}
	}

	public function UpdateComplainPanel($detail,$file)
	{
		extract($detail);
		$complain_date = date('Y-m-d',strtotime($complain_date));
		// print_r($file);exit;
		$rows = array(
		    // "user_id" => $user_id,
		    "complain_type"          => $complain_type,
		    "complain_cat_id"        => $complain_cat_id,
		    "complain_subcat_id"     => $complain_subcat_id,
		    "executive_type"         => $executive_type,
		    "customer_id"            => $customer_id,
		    // "address"                => $address,
		    "contact_person"         => $contact_person,
		    "state"                  => $state,
		    "city"                   => $city,
		    "zone"                   => $zone,
		    "remark"                 => $remark,
		    "complain_created_by"    => $complain_created_by,
		    "complain_assign_to"     => $complain_assign_to,
		    "complain_date"          => $complain_date,
		    "product_id"             => $product_id,
		    "product_sub_category"   => $product_sub_category,
		    "customer_requirement"   => $customer_requirement,
		    "type_of_company"           => $company_type,
		);

		
		$Where = "id='".$id."'";
		$eid = $this->db->rp_update($this->ctable,$rows,$Where,0);
		
		if ($eid) 
		{
			/*update entry in serice form*/
			// $service_no =CUSTOMER_COMPLAIN_NO.str_pad($id, 3, '0', STR_PAD_LEFT);
			$complain_service_id = $this->db->rp_getValue("complain_service","id","complain_id='".$id."'",0);
			$customer_name = $this->db->rp_getValue("executive","cname","id='".$customer_id."'");
			$contact_person = $this->db->rp_getValue("executive","company_name","id='".$customer_id."'");
			$contact_no = $this->db->rp_getValue("executive","phone","id='".$customer_id."'");
			$service_keys = array("remark");
			$service_values = array($remark);

			$rows = array(
				"complain_date" => $complain_date,
				"customer_name" => $customer_name,
				"contact_person_name" => $contact_person,
				"remark" => $remark,
			);
			$Where = "complain_id='".$id."' AND id='".$complain_service_id."'";
			$this->db->rp_update("complain_service",$rows,$Where,0);
			/*update entry in serice form*/

			// echo $old_image_path;exit;
			$image_path = $image_path_old;
			if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
			{
				$ri = $id;
				$rt = "complain";
				$tc = "complain";
				$rc = "id";
				$image_pathhhh=array();
				for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
				{
					if ($file["image_path"]['size'][$i] != 0) 
					{
						// print_r($file);
						$file_name = $file['image_path']['name'][$i];
						$file_size = $file['image_path']['size'][$i];
						$file_tmp = $file['image_path']['tmp_name'][$i];
						$file_type = $file['image_path']['type'][$i];
						$extension=explode(".",$file_name);
						
						$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG","webp");
						$extension=$extension[sizeof($extension)-1];
						if(!in_array($extension,$allowed_extentions))
						{
							$file_error=true;
						}
						$orignal_file_name=$extension[0];
						if(in_array($extension,$allowed_extentions))
						{
						    $attachment="../resource/image/";
							$name = md5($file_name);
						    $uniqueString = $name . uniqid($name);
							$file_name = 'complain_image_' . time() .$uniqueString. "." . $extension;
							move_uploaded_file($file_tmp,$attachment.$file_name);
						}
						$MediaTitle=$file_name;
				    	$MediaOrignalTitle=$file_name;

						$MediaFileName=$file_name;
						// $MediaType=User::$ValidMediaType[$extension];
						$UploadDate=date("Y-m-d H:i:s");
						
						// $Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$MediaType,$extension,$UploadDate,$ri,$rt,$tc);
						$Values=array($MediaTitle,$MediaOrignalTitle,$MediaFileName,$extension,$UploadDate,$ri,$rt,$tc);
						// $Columns=array("title","orignal_title","url","media_type","ext","upload_date","reference_id","reference_table","reference_column");
						$Columns=array("title","orignal_title","url","ext","upload_date","reference_id","reference_table","reference_column");
						$MediaID=$this->db->rp_insert("media",$Values,$Columns,0);
						// print_r($MediaID);
						// echo ",";

						$image_pathhhh[] = $MediaID;
						// print_r($image_pathhhh);
						$image_path = implode(",", $image_pathhhh);
					}
				}
						// print_r($image_path);exit;
				$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$id."'",0);
			}	
			$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$id."'",0);
		}

		
		if($eid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Complain Update successfully!!","ack_msg"=>"Complain Update successfully!!");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Not Update");
			return $reply;
		}
	}

	public function AddComplainService($detail)
	{
		extract($detail);
		$Service_count=$this->db->rp_getTotalRecord('complain_service',"complain_id='".$complain_id."' AND isDelete=0",0);
		// if($complain_count>=1)
		if($Service_count>=1)
		{
			// if (isset($file["lr_attachment"])) 
			// {
			// 	// print_r($file);exit();
			// 	// $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
			// 	$temp = explode(".", $file["lr_attachment"]["name"]);
			//  	$extension = end($temp);
			 
			// 		$fileName 	= $this->db->clean($file["lr_attachment"]["name"]);	
			// 			// echo "heelo";exit();
			// 		if($fileName!="")
			// 		{
			// 			$fileSize 	= round($file["lr_attachment"]["size"]); // BYTES									
			// 			$adate 		= date('Y-m-d H:i:m');
						
			// 			$extension	= end(explode(".", $fileName));		
			// 			if(!in_array($extension,$allowedExts))
			// 			{
			// 				$file_error=true;
			// 			}
											
			// 			$lr_attachment	= 'complain_lr_documents'.substr(sha1(time()), 0, 6).".".$extension;
			// 			$filePath 	= COMPLAIN_LRCOPY_A.$lr_attachment;	
			// 			$file['lr_attachment']['tmp_name'];
			// 			// print_r($filePath); exit;
			// 			move_uploaded_file($file['lr_attachment']['tmp_name'], $filePath);
			// 			$lr_attachment=$lr_attachment;
			// 			unset($old_lr_attachment);
			// 		}
			// 		else{
			// 			$lr_attachment=$old_lr_attachment;	
    		// 			unset($old_lr_attachment);
			// 		}
			// }

			$rows = array(
			    "customer_name"            => $customer_name,
			    "address"                  => $address,
			    "contact_person_name"      => $contact_person_name,
			    "contact_no"               => $contact_no,
			    "service_start_time"       => $service_start_time,
			    "service_end_time"         => $service_end_time,
			    "remark"                   => $remark,
			    "servicemen"               => $servicemen,
			    "type_of_product"          => $type_of_product,
			    "product"                  => $product,
			    "state_city"               => $state_city,
			    "site_name"                => $site_name,
			    "site_address"             => $site_address,
			    "contractor"               => $contractor,
			    "test_date"                => $test_date,
			    "tested_pressure"          => $tested_pressure,
			    "is_issues_testing"        => $is_issues_testing,
			    "last_maintenance_date"    => $last_maintenance_date,
			    "product_type"             => $product_type,
			    "specifications"           => $specifications,
			    "root_of_issue"            => $root_of_issue,
			    "current_scenario"         => $current_scenario,
			    "conclusion"               => $conclusion,
			    "resolution"               => $resolution,
			    "invoice_no"               => $invoice_no,
			    "invoice_date"             => $invoice_date,
			    "mt_fire_hydrant"          => $mt_fire_hydrant,
			    "mt_rrl"                   => $mt_rrl,
			    "mt_hose_reel_drum"        => $mt_hose_reel_drum,
			    "mt_branch_pipe"           => $mt_branch_pipe,
			    "mt_inlet"                 => $mt_inlet,
			    "mt_new"                   => $mt_new,
			    "sr_no"                    => $sr_no,
			);


			$where	= "complain_id='".$complain_id."'";
			$insert = $this->db->rp_update("complain_service",$rows,$where,0);
			
			if ($insert) 
			{
				$rows = array(

						"product_sub_category" 	=> $type_of_product,
						"product_id" 			=> $product,
						"remark"               	=> $remark,
						);

				$where	= "id='".$complain_id."'";
				$uid = $this->db->rp_update("complain",$rows,$where,0);
			}

		}
		else
		{
			$rows_insert = array(
			    "complain_id",
			    "service_no",
			    "complain_date",
			    "customer_name",
			    "address",
			    // "problem_report_date",
			    "contact_person_name",
			    "contact_no",
			    // "designation",
			    // "problem_report_by_client",
			    // "problem_report_observed_on_site",
			    // "corrective_action_taken",
			    "service_start_time",
			    "service_end_time",
			    "servicemen",
			    "type_of_product",
			    "product",
			    "state_city",
			    "site_name",
			    "site_address",
			    "contractor",
			    "test_date",
			    "tested_pressure",
			    "is_issues_testing",
			    "last_maintenance_date",
			    "product_type",
			    "specifications",
			    "root_of_issue",
			    "current_scenario",
			    "conclusion",
			    "resolution",
			    "invoice_no",
			    "invoice_date",
			    "mt_fire_hydrant",
			    "mt_rrl",
			    "mt_hose_reel_drum",
			    "mt_branch_pipe",
			    "mt_inlet",
			    "mt_new",
			    "sr_no",
			    "remark",
			    "isDelete",
			    "isActive"
			);

			$values_insert = array(
			    $complain_id,
			    $service_no,
			    date('Y-m-d', strtotime($complain_date)),
			    $customer_name,
			    $address,
			    // date('Y-m-d', strtotime($problem_report_date)),
			    $contact_person_name,
			    $contact_no,
			    // $designation,
			    // $problem_report_by_client,
			    // $problem_report_observed_on_site,
			    // $corrective_action_taken,
			    $service_start_time,
			    $service_end_time,
			    $servicemen,
			    $type_of_product,
			    $product,
			    $state_city,
			    $site_name,
			    $site_address,
			    $contractor,
			    $test_date,
			    $tested_pressure,
			    $is_issues_testing,
			    $last_maintenance_date,
			    $product_type,
			    $specifications,
			    $root_of_issue,
			    $current_scenario,
			    $conclusion,
			    $resolution,
			    $invoice_no,
			    $invoice_date,
			    $mt_fire_hydrant,
			    $mt_rrl,
			    $mt_hose_reel_drum,
			    $mt_branch_pipe,
			    $mt_inlet,
			    $mt_new,
			    $sr_no,
			    $remark,
			    0,
			    1
			);

			$insert = $this->db->rp_insert('complain_service', $values_insert, $rows_insert, 0);

		}

		if($insert!=0)
		{
			// foreach ($item as $p)
			// {
			// 	$item_name=addslashes(html_entity_decode($p['pro_name']));
			// 	$item_rows = array("complain_id","complain_service_id","product_id","make","sell_date","warranty","pro_name","isDelete","isActive");
			// 	$item_values = array($complain_id,$insert,$p['product_id'],$p['make'],date('Y-m-d',strtotime($p['sell_date'])),$p['warranty'],$item_name,0,1);

			// 	$item_id = $this->db->rp_insert("complain_service_item",$item_values,$item_rows,0);
			// }
			$reply=array("ack"=>1,"developer_msg"=>"Data Add successfully!!","ack_msg"=>"Data Add successfully!!","id"=>$insert);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Not Add");
			return $reply;
		}
	}

		public function AddComplainServiceApi($detail,$body)
	{
		//echo "hello";exit;
		extract($detail);
				//	print_r($body);exit;

		$complain_count=$this->db->rp_getTotalRecord('complain_service',"complain_id='".$complain_id."' AND isDelete=0",0);
		if($complain_count>=1)
		{
			$rows 	= array(
				"problem_report_date"              => date('Y-m-d',strtotime($problem_report_date)),
				"designation"                      => $designation,
				"problem_report_by_client"         => $problem_report_by_client,
				"problem_report_observed_on_site"  => $problem_report_observed_on_site,
				"corrective_action_taken"          => $corrective_action_taken,
				"service_start_time"               => $service_start_time,
				"service_end_time"                 => $service_end_time,
				"remark"                           => $remark,
			);
			$where	= "complain_id='".$complain_id."'";
			$insert=$this->db->rp_update("complain_service",$rows,$where,0);

			/*update complain status*/
			if($service_start_time!="" && $service_end_time!="")
			{
				$Update=$this->db->rp_update("complain",array("status"=>2),"id='".$complain_id."'",0);
			}
			/*update complain status*/
		}
		else
		{
			$Keys = array("problem_report_date","designation","problem_report_by_client","problem_report_observed_on_site","corrective_action_taken","service_start_time","service_end_time","remark");
			$Values = array(date('Y-m-d',strtotime($problem_report_date)),$designation,$problem_report_by_client,$problem_report_observed_on_site,$corrective_action_taken,$service_start_time,$service_end_time,$remark);
			$insert = $this->db->rp_insert('complain_service',$values_insert,$rows_insert,0); 
			/*update complain status*/
			if($service_start_time!="" && $service_end_time!="")
			{
				$Update=$this->db->rp_update("complain",array("status"=>2),"id='".$complain_id."'",0);
			}
			/*update complain status*/
		}

		if($insert!=0)
		{
			if(!empty($body))
			{
				$this->db->rp_delete("complain_service_item","complain_id=".$complain_id);
				$products 	= ($body!="")?(array)json_decode($body,true):array();
				for($i=0;$i<sizeof($products['values']);$i++)
				{
					$p=$products['values'][$i];
				//print_r($p);exit;
					$item_name = addslashes(html_entity_decode($p['nameValuePairs']['pro_name']));
					$item_rows = array("complain_id","complain_service_id","product_id","make","sell_date","warranty","pro_name");
					$item_values = array($complain_id,$insert,$p['nameValuePairs']['product_id'],$p['nameValuePairs']['make'],date('Y-m-d',strtotime($p['nameValuePairs']['sell_date'])),$p['nameValuePairs']['warranty'],$item_name);
					$item_id = $this->db->rp_insert("complain_service_item",$item_values,$item_rows,0);
				}
			}
			$reply=array("ack"=>1,"developer_msg"=>"Data Add successfully!!","ack_msg"=>"Data Add successfully!!");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Not Add");
			return $reply;
		}
	}

	public function AddImagesComplainServiceApi($detail,$body,$file,$file1)
	{	
		extract($detail);
				//print_r($file6["work_carried_by_images"]); exit;

		//print_r($file6);exit;
		if (isset($file["service_men_signature"]) && $file["service_men_signature"]['size']!=0)
		{
			$allowedExts = array("jpg,png,jpeg");
			$temp = explode(".", $file["service_men_signature"]["name"]);

			$extension = end($temp);
			$error="";
			if($file["service_men_signature"]["error"]>0) {
				$error .= "Error opening the file. ";
			}
			if($file["service_men_signature"]["type"]=="application/x-msdownload"){
				$error .= "Mime type not allowed. ";
			}
			if(!in_array($extension, $allowedExts))
			{
				
				$error .= "Extension not allowed. ";
			}
			
			$fileName = $this->db->clean($file["service_men_signature"]["name"]);
			$fileSize = round($file["service_men_signature"]["size"]); // BYTES

			$adate = date('Y-m-d H:i:m');

			$extension = end(explode(".", $fileName));
			$fileName = 'service_men_signature'.substr(sha1(time()), 0, 6).".".$extension;
			$tempPath="../resource/complain_service/".$file['service_men_signature']['name'];

			move_uploaded_file($file["service_men_signature"]['tmp_name'],$tempPath);
			$service_men_signature=$file['service_men_signature']['name'];

			unset($old_jobcard_pdf);
		}
		else
		{
			$service_men_signature="";
			//unset($old_image_path);
		}
		/*tech signature*/


		/*signature*/
		if (isset($file1["customer_signature"]) && $file1["customer_signature"]['size']!=0)
		{
			$allowedExts = array("jpg,png,jpeg");
			$temp = explode(".", $file1["customer_signature"]["name"]);

			$extension = end($temp);
			$error="";
			if($file1["customer_signature"]["error"]>0) {

			$error .= "Error opening the file. ";
			}
			if($file1["customer_signature"]["type"]=="application/x-msdownload"){
			$error .= "Mime type not allowed. ";
			}
			if(!in_array($extension, $allowedExts))
			{
				
			$error .= "Extension not allowed. ";
			}
			
			$fileName = $this->db->clean($file1["customer_signature"]["name"]);
			$fileSize = round($file1["customer_signature"]["size"]); // BYTES

			$adate = date('Y-m-d H:i:m');

			$extension = end(explode(".", $fileName));
			$fileName = 'signature'.substr(sha1(time()), 0, 6).".".$extension;
			$tempPath="../resource/complain_service/".$file1['customer_signature']['name'];

			move_uploaded_file($file1["customer_signature"]['tmp_name'],$tempPath);
			$customer_signature=$file1['customer_signature']['name'];

			unset($old_jobcard_pdf);
		}
		else
		{
			$customer_signature="";
			//unset($old_image_path);
		}
		/*signature*/
		
			$rows 	= array(
						"customer_sign" => $customer_signature,
						"serviceman_sign"      => $service_men_signature,
						);
			$where	= "complain_id='".$detail['complain_id']."'";
			$id=$this->db->rp_update("complain_service",$rows,$where,0);

			if($id!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Data Add successfully!!","ack_msg"=>"Data Add successfully!!");
			return $reply;
				}
				else
				{
					
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Not Add");
			return $reply;
				}

	
	}

	public function GetEditDataComplain($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['user_id']              = ($ctable_d['user_id']) ? htmlentities($ctable_d['user_id']) : '';
		$result['executive_type']       = ($ctable_d['executive_type']) ? htmlentities($ctable_d['executive_type']) : '';
		$result['customer_id']          = ($ctable_d['customer_id']) ? htmlentities($ctable_d['customer_id']) : '';
		$result['zone']                 = ($ctable_d['zone']) ? htmlentities($ctable_d['zone']) : '';
		$result['contact_person']       = ($ctable_d['contact_person']) ? htmlentities($ctable_d['contact_person']) : '';
		$result['dealer_id']            = ($ctable_d['dealer_id']) ? htmlentities($ctable_d['dealer_id']) : '';
		$result['complain_type']        = ($ctable_d['complain_type']) ? htmlentities($ctable_d['complain_type']) : '';
		$result['complain_cat_id']      = ($ctable_d['complain_cat_id']) ? htmlentities($ctable_d['complain_cat_id']) : '';
		$result['complain_subcat_id']   = ($ctable_d['complain_subcat_id']) ? htmlentities($ctable_d['complain_subcat_id']) : '';
		$result['image_path']           = ($ctable_d['image_path']) ? htmlentities($ctable_d['image_path']) : '';
		$result['remark']               = ($ctable_d['remark']) ? htmlentities($ctable_d['remark']) : '';
		$result['dealer_customer_id']   = ($ctable_d['dealer_customer_id']) ? htmlentities($ctable_d['dealer_customer_id']) : '';
		$result['complain_created_by']  = ($ctable_d['complain_created_by']) ? htmlentities($ctable_d['complain_created_by']) : '';
		$result['complain_assign_to']   = ($ctable_d['complain_assign_to']) ? htmlentities($ctable_d['complain_assign_to']) : '';
		$result['complain_date']        = ($ctable_d['complain_date'] != "0000-00-00" && $ctable_d['complain_date'] != "1970-01-01") ? date('d-m-Y', strtotime($ctable_d['complain_date'])) : "";
		$result['product_id']           = ($ctable_d['product_id']) ? htmlentities($ctable_d['product_id']) : '';
		$result['product_sub_category'] = ($ctable_d['product_sub_category']) ? htmlentities($ctable_d['product_sub_category']) : '';

		$result['customer_requirement'] = ($ctable_d['customer_requirement']) ? htmlentities($ctable_d['customer_requirement']) : '';

		$result['type_of_company'] 		= ($ctable_d['type_of_company']) ? htmlentities($ctable_d['type_of_company']) : '';


		
		$reply=array("ack"=>1,"developer_msg"=>"Data fetched Successfully!!.","ack_msg"=>"Success! Data fetched Successfully","result"=>$result);
		return $reply;
	
	}

	public function DeleteComplain($detail)
	{
		$rows 	= array(
			"isDelete"	=> "1"
		);	
		$where	= "id='".$_REQUEST['id']."'";

		$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
		if($uid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Data Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Delete Failed.");
			return $reply;
		}
	}

}
?>