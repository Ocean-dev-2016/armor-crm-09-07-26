<?php
require_once("main.class.php");
require_once("function.class.php");
class CustomerInquiry extends Functions
{
	public $db;
	public $ctable="customer_inquiry";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
    } 
	public function InsertCustomerInquiry($detail,$file) 
	{
		//print_r($detail);exit();
		extract($detail);
		/*$dup_where = "mobile_number = '".$mobile_number."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		if($r)
		{
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Customer Mobile Number","ack_msg"=>"Duplication! Already Exist Customer Mobile Number.");
			return $reply;
		}
		else
		{*/
			/*if(isset($file['image_path']["name"]) && $file['image_path']["name"]!="") 
			{
				$file_tmp  = $file['image_path']['tmp_name'];
				$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
				$temp = explode(".", $file["image_path"]["name"]);
				$extension = end($temp);

				if($file["image_path"]["error"]>0) {
					$error .= "Error opening the file. ";
				}
				if($file["image_path"]["type"]=="application/x-msdownload"){
					$error .= "Mime type not allowed. ";
				}
				if(!in_array($extension, $allowedExts)){
					$error .= "Extension not allowed. ";
				}
			
				$fileName  = $this->db->clean($file["image_path"]["name"]);
				$fileSize  = round($file["image_path"]["size"]); // BYTES
				//echo $fileSize ;exit;
				$adate   = date('Y-m-d H:i:m');

				$extension = end(explode(".", $fileName));
			
				$fileName = 'inquiry_img_'.substr(sha1(time()), 0, 6).".".$extension;
				$tempPath=INQUIRY_IMAGE_A.$fileName;
				move_uploaded_file($file_tmp,$tempPath);
				$detail['image_path']=$fileName;
				unset($detail['old_image_path']);
			}*/

			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"company_name",
						"person_name",
						"mobile_number",
						"whatsapp_number",
						"date_of_call",
						"country",
						"state",
						"city",
						"executive_type",
						"distributor_id",
						"image_path",
						"sales_executive_id",
						"source_of_inquiry",
						"inquiry_created_by",
						"inquiry_assign_to",
						"product_id",
						"quantity",
						"remark",
						"quotation_flag",
						"customer_requirement",
						"email_address",
						"address",
					);
		// print_r($rows);exit();
			$values = array(
						$company_name,
						$contact_person,
						$mobile_number,
						$whatsapp_number,
						date('Y-m-d',strtotime($date_of_call)),
						$country,
						$state,
						$city,
						$executive_type,
						$distributor_id,
						$detail['image_path'],
						$inquiry_created_by,
						$source_of_inquiry,
						$inquiry_created_by,
						$inquiry_assign_to,
						$product_id,
						$quantity,
						$remark,
						$quotation_flag,
						$customer_requirement,
						$email_address,
						$address,
					);
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
		
			if($uid!=0)
			{
				/*image path*/
					$image_path=array();
					if (isset($file["image_path"]) && $file["image_path"]['size']!=0)
					{
						$ri = $uid;
						$rt = "inquiry";
						$tc = "inquiry";
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
						$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$uid."'",0);
					}
				/*image path*/
				/*add followup*/
					if($detail['first_followup_date']!="")
					{
						$followup_date = date("Y-m-d",strtotime($detail['first_followup_date']));
						$followup_detail = $detail['followup_detail'];
					    
					    $Values=array('customer_inquiry',$inquiry_created_by,'0',$uid,0,$followup_detail,1,$followup_date,0,1,0,0);
					    
					    $Columns=array("reference_table","user_id","visitor_id","reference_id","project_manager_id","description","through","followup_date","isDelete","isActive","next_followup_id","refrence_media_id");

				        $ContentID=$this->db->rp_insert("followup",$Values,$Columns,0);
				        $upadateid = $this->db->rp_update($this->ctable,array("status"=>1),"id='".$uid."'",0);
					}
					
				/*add followup*/
				$reply=array("ack"=>1,"developer_msg"=>"Customer Inquiry Added.","ack_msg"=>"Success! Customer Inquiry Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Customer Inquiry Insert Failed.");
				return $reply;
			}
		// }
	}
	 
	public function UpdateCustomerInquiry($detail,$file)
	{
		extract($detail);
		$dup_where = "mobile_number = '".$mobile_number."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		if($r)
		{
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Customer Mobile Number","ack_msg"=>"Duplication! Already Exist Customer Mobile Number.");
			return $reply;
		}
		else
		{
			$rows 	= array(
					"company_name" =>$company_name,
					"person_name" =>$contact_person,
					"mobile_number" =>$mobile_number,
					"whatsapp_number" =>$whatsapp_number,
					"date_of_call" =>date('Y-m-d',strtotime($date_of_call)),
					"country" => $country,
					"state" => $state,
					"city" => $city,
					"executive_type" => $executive_type,
					"distributor_id" =>$distributor_id,
					"sales_executive_id" =>$inquiry_created_by,
					"source_of_inquiry" =>$source_of_inquiry,
					"inquiry_created_by" =>$inquiry_created_by,
					"inquiry_assign_to" =>$inquiry_assign_to,
					"product_id" => $product_id,
					"quantity" =>$quantity,
					"remark" =>$remark,
					"quotation_flag" =>$quotation_flag,
					"customer_requirement" =>$customer_requirement,
					"email_address" =>$email_address,
					"address"		=> $address,
			);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
			
			/*image_upload*/
			$image_path=array();
			if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
			{
				$ri = $_REQUEST['id'];
				$rt = "customer_inquiry";
				$tc = "customer_inquiry";
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
				$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$_REQUEST['id']."'",0);
			}
			/*image_upload*/
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Customer Type Update Successfull!!.","ack_msg"=>"Success! Customer Type Update Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Customer Type Update Failed.");
				return $reply;
			}
		}
	}	

	public function GetEditDataCustomerInquiry($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['executive_type']		= htmlentities($ctable_d['executive_type']);
		$result['sales_executive_id']	= htmlentities($ctable_d['sales_executive_id']);
		$result['company_name']			= htmlentities($ctable_d['company_name']);
		$result['person_name']			= htmlentities($ctable_d['person_name']);
		$result['contact_person']		= htmlentities($ctable_d['contact_person']);
		$result['mobile_number']		= htmlentities($ctable_d['mobile_number']);
		$result['whatsapp_number']		= htmlentities($ctable_d['whatsapp_number']);
		$result['country']				= htmlentities($ctable_d['country']);
		$result['state']				= htmlentities($ctable_d['state']);
		$result['city']					= htmlentities($ctable_d['city']);
		$result['date_of_call']			= date('d-m-Y',strtotime($ctable_d['date_of_call']));
		$result['address']				= htmlentities($ctable_d['address']);
		$result['source_of_inquiry']	= htmlentities($ctable_d['source_of_inquiry']);
		$result['inquiry_created_by']	= htmlentities($ctable_d['inquiry_created_by']);
		$result['inquiry_assign_to']	= htmlentities($ctable_d['inquiry_assign_to']);
		$result['product_id']			= htmlentities($ctable_d['product_id']);
		$result['quantity']				= htmlentities($ctable_d['quantity']);
		$result['remark']			= htmlentities($ctable_d['remark']);
		$result['customer_requirement']	= htmlentities($ctable_d['customer_requirement']);
		$result['quotation_flag']	= htmlentities($ctable_d['quotation_flag']);
		$result['email_address']	= htmlentities($ctable_d['email_address']);

		
		$reply=array("ack"=>1,"developer_msg"=>"Customer Type detail fetched!!.","ack_msg"=>"Success! Customer Type Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteCustomerInquiry($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Customer Type Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Customer Type Failed.");
				return $reply;
			}
	}

	public function UpdateCustomerLead($detail)
	{
		extract($detail);
		$dup_where = "mobile_number = '".$mobile_number."' AND id!='".$_REQUEST['lead_id']."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		if($r)
		{
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Customer Mobile Number","ack_msg"=>"Duplication! Already Exist Customer Mobile Number.");
			return $reply;
		}
		else
		{
			$rows 	= array(
					"company_name" =>$company_name,
					"person_name" =>$contact_person,
					"mobile_number" =>$mobile_number,
					"whatsapp_number" =>$whatsapp_number,
					"date_of_call" =>date('Y-m-d',strtotime($date_of_call)),
					"country" => $country,
					"state" => $state,
					"city" => $city,
					"executive_type" => $executive_type,
					"distributor_id" =>$distributor_id,
					"sales_executive_id" =>$inquiry_created_by,
					"source_of_inquiry" =>$source_of_inquiry,
					"inquiry_created_by" =>$inquiry_created_by,
					"inquiry_assign_to" =>$inquiry_assign_to,
					"product_id" => $product_id,
					"quantity" =>$quantity,
					"remark" =>$remark,
					"quotation_flag" =>$quotation_flag,
					"customer_requirement" =>$customer_requirement,
					"email_address" =>$email_address,
					"address"		=> $address,
			);
			$where	= "id='".$_REQUEST['lead_id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Customer Inquiry Update Successfull!!.","ack_msg"=>"Success! Customer Inquiry Update Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Customer Inquiry Update Failed.");
				return $reply;
			}
		}
	}

	function uploadCustomerInquiry($file)
	{
		if(isset($file))
		{
			if ($file["error"] > 0)
			{
				$ack=array("ack"=>0,"ack_msg"=>"Courrpted File Try Again!!");
				return $ack;
			}
			else
			{
				if($file["size"] > 0)
				{
					$file_name = $file['name'];
					$file_size = $file['size'];
					$file_tmp = $file['tmp_name'];
					$file_type = $file['type'];
					$extension=explode(".",$file_name);
					$extension=$extension[sizeof($extension)-1];
					
					if($extension!="xlsx" && $extension!="xls" && $extension!="csv" && $extension!="CSV")
					{
						$ack=array("ack"=>0,"ack_msg"=>"Not Valid Data Sheet!! Valid Format XLS/ XLSX");
						return $ack;
					}
					else
					{
						//$storagename = "pdf/user_discount.xls";
						$storagename = "sheet_import/uploads/customer_inquiry_import".date('dmYhis').".xlsx";
						$upload_info=array("updated"=>0,"duplicate"=>array());
						move_uploaded_file($file["tmp_name"],$storagename);		
						include "../".ADMINFOLDER.'/PHPExcel/IOFactory.php';
						$inputFileName = $storagename; 
						/*require_once('class.system.php');
						$system=new System();
						$backupInfo=$system->SystemBackUp("Backup-before-importing-file-".date("Y-m-d-H-i-s"));
						$backupInfo['backup_url']=ADMINSITEURL.$backupInfo['backup_url'];*/
						try
						{							
							$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
							
						}
						catch(Exception $e)
						{							
							$ack=array("ack"=>0,"ack_msg"=>"Unformatted Excel File Try Again!!");
							return $ack;
						}
						try
						{
							$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);// get data in array form
							 //print_r($allDataInSheet);exit;
							ob_end_clean();
							// unset($allDataInSheet[1]);
								
							$arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet
							for($i=1;$i<=$arrayCount;$i++)
							{
								if($i==1)
								{		
									// echo $allDataInSheet[$i]["A"];exit;								
									if(($allDataInSheet[$i]["A"])!="Source Medium" || ($allDataInSheet[$i]["B"])!="Customer Type" || ($allDataInSheet[$i]["C"])!="Company Name" || ($allDataInSheet[$i]["D"])!="Person Name" || ($allDataInSheet[$i]["E"])!="Mobile Number" || ($allDataInSheet[$i]["F"])!="Email Address" || ($allDataInSheet[$i]["G"])!="Country" || ($allDataInSheet[$i]["H"])!="State" || ($allDataInSheet[$i]["I"])!="City" || ($allDataInSheet[$i]["J"])!="Date Of Call" || ($allDataInSheet[$i]["K"])!="Inquiry Taken By" || ($allDataInSheet[$i]["L"])!="Inquiry Assigned to" || ($allDataInSheet[$i]["M"])!="Address" || ($allDataInSheet[$i]["N"])!="Whatsapp Number")
									{										
										$ack=array("ack"=>0,"ack_msg"=>"Wrong Excel Format Column Mismatch Try Again!!");
										return $ack;
									}
										
								}
								else
								{
									// $i=$i+1;
									$data = array();
									$source_of_inquiry = $this->db->clean($allDataInSheet[$i]["A"]);
									$executive_type	   = $this->db->clean($allDataInSheet[$i]["B"]);
									$company_name 	   = $this->db->clean($allDataInSheet[$i]["C"]);	
									$person_name       = $this->db->clean($allDataInSheet[$i]["D"]);
									$mobile_number     = $this->db->clean($allDataInSheet[$i]["E"]);
									$email_address     = $this->db->clean($allDataInSheet[$i]["F"]);
									$country           = $this->db->clean($allDataInSheet[$i]["G"]);
									$state             = $this->db->clean($allDataInSheet[$i]["H"]);
									$city              = $this->db->clean($allDataInSheet[$i]["I"]);
									$date_of_call      = $this->db->clean($allDataInSheet[$i]["J"]);
									$inquiry_created_by = $this->db->clean($allDataInSheet[$i]["K"]);
									$inquiry_assign_to  = $this->db->clean($allDataInSheet[$i]["L"]);
									$address  = $this->db->clean($allDataInSheet[$i]["M"]);
									$whatsapp_number  = $this->db->clean($allDataInSheet[$i]["N"]);
									
									$data  = $allDataInSheet['2'];// first row of sheet for dealer & outlet
									// print_r($data);exit;
									// echo count($data);exit;
									$CurrentColumn='E';
									
									for($T=5;$T<=count($data);$T++)
									{			
										// echo $CurrentColumn;
										// echo $data[$CurrentColumn];						
										$top_category_id=$allDataInSheet[$i][$CurrentColumn];
										$CurrentColumn++;
										
										$discount=$allDataInSheet[$i][$CurrentColumn];
										$CurrentColumn++;
										if($top_category_id!="")
										{
											// echo "sdsd";exit;
										// is Pricelist has this product?
											if($this->db->rp_getTotalRecord("customer_inquiry","mobile_number='".$mobile_number."' AND isDelete=0",0)<=0)
											{

												$executive_type_new = $this->db->rp_getValue("customer_type","id","LOWER(name)='".strtolower($executive_type)."'");

												$source_of_inquiry_new = $this->db->rp_getValue("source_of_inquiry","id","LOWER(name)='".strtolower($source_of_inquiry)."'",0);

												$sales_executive_id_created_by = $this->db->rp_getValue("sales_executive","id","LOWER(name)='".strtolower($inquiry_created_by)."'");

												$inquiry_assign_to_new = $this->db->rp_getValue("sales_executive","id","LOWER(name)='".strtolower($inquiry_assign_to)."'");

												$this->db->rp_insert("customer_inquiry",array($executive_type_new,$company_name,$person_name,$mobile_number,$email_address,$country,$state,$city,$date_of_call,$source_of_inquiry_new,$sales_executive_id_created_by,$inquiry_assign_to_new,$sales_executive_id_created_by,$address),array("executive_type","company_name","person_name","mobile_number","whatsapp_number","email_address","country","state","city","date_of_call","source_of_inquiry","inquiry_created_by","inquiry_assign_to","sales_executive_id","address"),0);
											}
										}
										
									}
									$upload_info['updated']=$upload_info['updated']+1;
									
								}
								
							}
							$ack=array("ack"=>1,"ack_msg"=>"Data uploaded!!","log"=>$upload_info,"backup_url"=>$backupInfo['backup_url']);
							return $ack;									
						}								
						catch(Exception $e)
						{							
							$ack=array("ack"=>0,"ack_msg"=>"Unformatted Excel File Try Again!!","backup_url"=>$backupInfo['backup_url']);
							return $ack;
						}
								
						if(file_exists($storagename))
						{
							unlink($storagename);									
						}
						$ack=array("ack"=>1,"ack_msg"=>"Excel Sheet Uploaded See Log to Know More!!","log"=>$upload_info,"backup_url"=>$backupInfo['backup_url']);
						return $ack;
					}
				}
			}
		}
	}
}

?>