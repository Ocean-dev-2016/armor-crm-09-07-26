<?php
//require_once("main.class.php");
require_once("function.class.php");
require_once("class.log.php");
class Employee extends Functions
{
	public $db,$log;
	public $etable="employee";
	public $ctable="emp_personal_info";
	public $ectable="emp_company_info";
	public $estable="emp_salary_info";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;		   
		$this->log=new Log();		   
    } 
	public function InsertEmpPersonalInfo($detail,$file)
	{
		extract($detail);
		$dup_where = "emp_code = '".$emp_code."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Duplicate code","ack_msg"=>"Already Exist this Code!!");
			return $reply;
		}
		else
		{
			//$error="";
				if (isset($file["file"]) ) {
					$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
					$temp = explode(".", $file["file"]["name"]);
					 $extension = end($temp);
				 
						$fileName 	= $this->db->clean($file["file"]["name"]);	
						if($fileName!=""){
						$fileSize 	= round($file["file"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');
						
						$extension	= end(explode(".", $fileName));				
						$fileName	= '_emp_image_'.substr(sha1(time()), 0, 6).".".$extension;
						$filePath 	= "../images/employee/".$fileName;						
						move_uploaded_file($file['file']['tmp_name'], $filePath);
						}
						else{
							$fileName="";
						}
                        if (isset($file["file"]) ) {
						$allowedExts1 = array("jpg","jpeg","png","gif","JPG","JPEG");
						$temp1 = explode(".", $file["file_document"]["name"]);
						 $extension1 = end($temp1);
					 
						
						$fileName_document 	= $this->db->clean($file["file_document"]["name"]);
						if($fileName_document!=""){
						$fileSize 	= round($file["file_document"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');
						
						$extension1	= end(explode(".", $fileName_document));				
					    $proof_document	= '_emp_document_'.substr(sha1(time()), 0, 6).".".$extension1;
						$filePath1 	= "../images/employee/document/".$proof_document;
						move_uploaded_file($file['file_document']['tmp_name'], $filePath1);
						}
						else{
							$proof_document="";
						}
						
						// Update User Profile Image in database
						$adate	= date('Y-m-d H:i:s');
						$rows 	= array(
									"emp_code",
									"first_name",
									"middle_name",
									"last_name",
									"phone",
									"other_contact",
									"perment_address",
									"residential_address",
									"birth_date",
									"blood_group",
									"remark",
									"identification_proof",
									"proof_document",
									"image",
									"isActive",
									"adate"
								);
						$values = array(
									$emp_code,
									$first_name,
									$middle_name,
									$last_name,
									$phone,
									$other_contact,
									$perment_address,
									$residential_address,
									$birth_date,
									$blood_group,
									$remark,
									$identification_proof,
									$proof_document,
									$fileName,
									$isActive,
									$adate
								);
								
						$eid = $this->db->rp_insert($this->ctable,$values,$rows,0);
						
						$this->log->insertLog($this->ctable,$eid,"insert","Employee Added By User");
						
						$var=mysqli_query($emp_img_insert);
					
						$reply=array("ack"=>1,"developer_msg"=>"image successfully uploaded!!","ack_msg"=>"Image successfully uploaded!!");
						return $reply;
				
				
			}
			if($eid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"insert Successfully","ack_msg"=>"Success! Insert Employee Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Insert Employee Failed.");
				return $reply;
			}
			}
		}
	 }
	public function UpdateEmpPersonalInfo($detail)
	{
			extract($detail);
			$dup_where = "emp_code = '".$emp_code."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$this->db->rp_location("add_".$ctable.".php?mode=edit&id=".$_REQUEST['id']."&msg=duplicate");
				die;
			}else{
				$rows 	= array(
							"emp_code"			=> $emp_code,
							"first_name"		=> $first_name,
							"middle_name"		=> $middle_name,							
							"last_name"			=> $last_name,
							"phone"				=> $phone,
							"other_contact"		=> $other_contact,
							"perment_address"	=> $perment_address,
							"residential_address"=> $residential_address,
							"birth_date"		=> $birth_date,
							"blood_group"		=> $blood_group,
							"remark"			=> $remark,
							"identification_proof"=> $identification_proof,
						);
				$where	= "id='".$_REQUEST['id']."'";
				$eid=$this->db->rp_update($this->ctable,$rows,$where);
				$this->log->insertLog($this->ctable,$_REQUEST['id'],"update","Employee Updated By User");
				if($eid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"User Update Successfull!!.","ack_msg"=>"Success! Update Employee Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Update Employee Failed.");
					return $reply;
				}
			}	
		}	
	public function getEmpPersonalInfo($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		$result['emp_code']				= htmlentities($ctable_d['emp_code']);
		$result['first_name']			= htmlentities($ctable_d['first_name']);
		$result['middle_name']			= htmlentities($ctable_d['middle_name']);
		$result['last_name']			= stripslashes($ctable_d['last_name']);
		$result['phone']				= stripslashes($ctable_d['phone']);
		$result['other_contact']		= stripslashes($ctable_d['other_contact']);
		$result['perment_address']		= stripslashes($ctable_d['perment_address']);
		$result['residential_address']	= stripslashes($ctable_d['residential_address']);
		$result['birth_date']			= htmlentities(date_format(date_create($ctable_d["birth_date"]),"d-m-Y"));
		$result['blood_group']			= htmlentities($ctable_d['blood_group']);
		$result['image']				= htmlentities($ctable_d['image']);
		$result['remark']				= stripslashes($ctable_d['remark']);
		$result['identification_proof']	= $ctable_d['identification_proof'];
		$result['proof_document']	= $ctable_d['proof_document'];
		
		$reply=array("ack"=>1,"developer_msg"=>"User detail fetched!!.","ack_msg"=>"Success! Update Employee Successfully.","result"=>$result);
		return $reply;
	
	}	
	public function DeleteEmpPersonalInfo($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$eid=$this->db->rp_update($this->ctable,$rows,$where);
			$this->log->insertLog($this->ctable,$_REQUEST['id'],"delete","Employee Deleted By User");
			if($eid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Employee Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Employee Failed.");
				return $reply;
			}
	}
	public function InsertEmpCompanyInfo($detail) 
	{ 
		extract($detail);
		
		$dup_where = "emp_id = '".$emp_id."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ectable,$dup_where,0);
		
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Duplicate code","ack_msg"=>"Already Exist this Code!!");
			return $reply;
		}
		else
		{
			 $adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"emp_id",
						"designation",
						"department",
						"joining_date",
						"shift",
						"account_number",
						"bank_name",
						"isActive",
						"adate"
					);
			$values = array(
						$emp_id,
						$designation,
						$department,
						date("Y-m-d",strtotime($joining_date)),
						$shift,
						$account_number,
						$bank_name,
						$isActive,
						$adate
					);
					
		 	$eid = $this->db->rp_insert($this->ectable,$values,$rows,0);
			$this->log->insertLog($this->ctable,$eid,"insert","Employee Company Info Added By User");
			if($eid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"insert Successfully","ack_msg"=>"Success! Company Infomation Insert Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Insert Employee Failed.");
				return $reply;
			}
		}
	 }
	public function UpdateEmpCompanyInfo($detail)
	{
			extract($detail);
			$dup_where = "emp_id = '".$emp_id."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
			if($r){
				$db->rp_location("add_".$ctable.".php?mode=edit&id=".$_REQUEST['id']."&msg=duplicate");
				die;
			}else{
				$rows 	= array(
							"designation"	=> $designation,
							"department"	=> $department,
							"joining_date"	=> date("Y-m-d",strtotime($joining_date)),					
							"shift"			=> $shift,
							"account_number"=> $account_number,
							"bank_name"		=> $bank_name,
						);
				$where	= "emp_id='".$emp_id."'";
				$eid=$this->db->rp_update($this->ectable,$rows,$where,0);
				$this->log->insertLog($this->ctable,$emp_id,"update","Employee Company Info Updated By User");
				if($eid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"User Update Successfull!!.","ack_msg"=>"Success! Company Information Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Update Record Failed.");
					return $reply;
				}
			}	
		}	
	public function getEmpCompanyInfo($detail)
	{		
		$where = " emp_id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ectable,"*",$where,"",0);
		if($ctable_r)
		{
			$ctable_d = mysqli_fetch_array($ctable_r);
			$result=array();
			$result['designation']			= htmlentities($ctable_d['designation']);
			$result['department']			= htmlentities($ctable_d['department']);
			$result['joining_date']			= htmlentities(date_format(date_create($ctable_d["joining_date"]),"d-m-Y"));
			$result['shift']				= stripslashes($ctable_d['shift']);
			$result['account_number']		= stripslashes($ctable_d['account_number']);
			$result['bank_name']			= stripslashes($ctable_d['bank_name']);
			
			$reply=array("ack"=>1,"developer_msg"=>"User detail fetched!!.","ack_msg"=>"Success! Update Record Successfully.","result"=>$result);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Comapny detail not fetched!!.","ack_msg"=>"Success! Company Info Fetched"	);
			return $reply;
		}
		
	
	}
	public function DeleteEmpCompanyInfo($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$eid=$this->db->rp_update($this->ectable,$rows,$where);
			$this->log->insertLog($this->ctable,$_REQUEST['id'],"delete","Employee Company Info Deleted By User");
			if($eid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Record Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete record Failed.");
				return $reply;
			}
	}
	public function isCompanyInfoAvailable($detail)
	{
		$count=$this->db->rp_getTotalRecord($this->ectable,"emp_id='".$detail['emp_id']."'");
		if($count>=1)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Yes Company Info Available!!.","ack_msg"=>"");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"No Company Info Not Available!!.","ack_msg"=>"");
			return $reply;
		}
		
	}
	public function isSalaryInfoAvailable($detail)
	{
		$count=$this->db->rp_getTotalRecord($this->estable,"emp_id='".$detail['emp_id']."'");
		if($count>=1)
		{
			$reply=array("ack"=>1,"developer_msg"=>"Yes Company Info Available!!.","ack_msg"=>"");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"No Company Info Not Available!!.","ack_msg"=>"");
			return $reply;
		}
		
	}
	public function InsertEmpSalaryInfo($detail) 
	{ 
		extract($detail);
		
		$dup_where = "emp_id = '".$emp_id."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->estable,$dup_where,0);
		
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Duplicate id","ack_msg"=>"Already Exist this id!!");
			return $reply;
		}
		else
		{
			 $adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"emp_id",
						"basic",
						"hra",
						"medical",
						"conv",
						"wash",
						"edu",
						"lt",
						"spe",
						"gross",
						"it",
						"pt",
						"pf",
						"net_payable",
						"remark",
						"isActive",
						"adate"
					);
			$values = array(
						$emp_id,
						$basic,
						$hra,
						$medical,
						$conv,
						$wash,
						$edu,
						$lt,
						$spe,
						$gross,
						$it,
						$pt,
						$pf,
						$net_payable,
						$remark,
						$isActive,
						$adate
					);
					
		 	$eid = $this->db->rp_insert($this->estable,$values,$rows,0);
			if($eid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"insert Successfully","ack_msg"=>"Success! Insert Record Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Insert Record Failed.");
				return $reply;
			}
		}
	 }
	public function UpdateEmpSalaryInfo($detail)
	{
			extract($detail);
			$dup_where = "emp_id = '".$emp_id."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->estable,$dup_where);
			if($r){
				$this->db->rp_location("add_".$ctable.".php?mode=edit&id=".$_REQUEST['id']."&msg=duplicate");
				die;
			}else{
				$rows 	= array(
							"basic"			=> $basic,
							"hra"			=> $hra,
							"medical"		=> $medical,
							"conv"			=> $conv,
							"wash"			=> $wash,
							"edu"			=> $edu,
							"lt"			=> $lt,
							"spe"			=> $spe,
							"gross"			=> $gross,
							"it"			=> $it,
							"pt"			=> $pt,
							"pf"			=> $pf,
							"net_payable"	=> $net_payable,
							"remark"		=> $remark,
						);
				$where	= "emp_id='".$emp_id."'";
				$eid=$this->db->rp_update($this->estable,$rows,$where,0);
				if($eid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"User Update Successfull!!.","ack_msg"=>"Success! Employee Salary Infomation Update Record Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Update Record Failed.");
					return $reply;
				}
			}	
		}	
	public function getEmpSalaryInfo($detail)
	{		
		$where = " emp_id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->estable,"*",$where,"",0);
		if($ctable_r)
		{
			$ctable_d = mysqli_fetch_array($ctable_r);
			$result=array();
			$result['basic']			= htmlentities($ctable_d['basic']);
			$result['hra']				= htmlentities($ctable_d['hra']);
			$result['medical']			= stripslashes($ctable_d['medical']);
			$result['conv']				= stripslashes($ctable_d['conv']);
			$result['wash']				= stripslashes($ctable_d['wash']);
			$result['edu']				= stripslashes($ctable_d['edu']);
			$result['lt']				= stripslashes($ctable_d['lt']);
			$result['spe']				= stripslashes($ctable_d['spe']);
			$result['gross']			= stripslashes($ctable_d['gross']);
			$result['it']				= stripslashes($ctable_d['it']);
			$result['pt']				= stripslashes($ctable_d['pt']);
			$result['pf']				= stripslashes($ctable_d['pf']);
			$result['net_payable']		= stripslashes($ctable_d['net_payable']);
			$result['remark']			= stripslashes($ctable_d['remark']);
			
			$reply=array("ack"=>1,"developer_msg"=>"User detail fetched!!.","ack_msg"=>"Success! Update Record Successfully.","result"=>$result);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Comapny detail not fetched!!.","ack_msg"=>"Success! Company Info Fetched"	);
			return $reply;
		}
		
	
	}
	public function getSalaryInfo($detail)
	{		
		$where = " emp_id='".$detail['emp_id']."' AND id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->estable,"*",$where,"",0);
		if($ctable_r)
		{
			$ctable_d = mysqli_fetch_array($ctable_r);
			$result=array();
			$result['basic']			= htmlentities($ctable_d['basic']);
			$result['hra']				= htmlentities($ctable_d['hra']);
			$result['medical']			= stripslashes($ctable_d['medical']);
			$result['conv']				= stripslashes($ctable_d['conv']);
			$result['wash']				= stripslashes($ctable_d['wash']);
			$result['edu']				= stripslashes($ctable_d['edu']);
			$result['lt']				= stripslashes($ctable_d['lt']);
			$result['spe']				= stripslashes($ctable_d['spe']);
			$result['gross']			= stripslashes($ctable_d['gross']);
			$result['it']				= stripslashes($ctable_d['it']);
			$result['pt']				= stripslashes($ctable_d['pt']);
			$result['pf']				= stripslashes($ctable_d['pf']);
			$result['net_payable']		= stripslashes($ctable_d['net_payable']);
			$result['remark']			= stripslashes($ctable_d['remark']);
			$result['year']			= stripslashes($ctable_d['year']);
			$result['month']			= stripslashes($ctable_d['month']);
			
			$reply=array("ack"=>1,"developer_msg"=>"User detail fetched!!.","ack_msg"=>"Success! Update Record Successfully.","result"=>$result);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Comapny detail not fetched!!.","ack_msg"=>"Success! Company Info Fetched"	);
			return $reply;
		}
		
	
	}
	
	public function DeleteEmpsalaryInfo($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$eid=$this->db->rp_update($this->estable,$rows,$where);
			if($eid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Record Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete record Failed.");
				return $reply;
			}
	}
	public function updateEmpImage($file,$id)
	{
				$error="";
				if (isset($file["file"])) {
					$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
					$temp = explode(".", $file["file"]["name"]);
					 $extension = end($temp);
				 
					if($file["file"]["error"]>0) {
						$error .= "Error opening the file. ";
					}
					if($file["file"]["type"]=="application/x-msdownload"){	
						$error .= "Mime type not allowed. ";
					}
					if(!in_array($extension, $allowedExts)){
						$error .= "Extension not allowed. ";
					}
					if($file["file"]["size"] > 26214400){ //26214400 Bytes = 25 MB, 102400 = 100KB
						$error .= "File size shoud be less than 25 MB ";
					}
					if($error=="") { 
						
						$fileName 	= $this->db->clean($file["file"]["name"]);			
						$fileSize 	= round($file["file"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');
						
						$extension	= end(explode(".", $fileName));						
						$fileName	= '_emp_image_'.substr(sha1(time()), 0, 6).".".$extension;
						$filePath 	= "../images/employee/".$fileName;						
						move_uploaded_file($file['file']['tmp_name'], $filePath);
						
						// Update User Profile Image in database
						$values=array("image"=>$fileName);
						$where="id='".$_REQUEST['id']."' ";
						$emp_img_update=$this->db->rp_update("emp_personal_info",$values,$where,0);
						$var=mysqli_query($emp_img_update);
					
						$reply=array("ack"=>1,"developer_msg"=>"image successfully uploaded!!","ack_msg"=>"Image successfully uploaded!!");
						return $reply;
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"image type not valid","ack_msg"=>"Invalid image or image not found.");
						return $reply;
					} 
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"image type not valid","ack_msg"=>"Invalid image or image not found.");
					return $reply;
				}
			
	}
	
	public function UpdateEmpDocument($file,$id)
	{
				$error="";
				if (isset($file["file_document"])) {
					$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
					$temp = explode(".", $file["file_document"]["name"]);
					 $extension = end($temp);
				 
					if($file["file_document"]["error"]>0) {
						$error .= "Error opening the file. ";
					}
					if($file["file_document"]["type"]=="application/x-msdownload"){	
						$error .= "Mime type not allowed. ";
					}
					if(!in_array($extension, $allowedExts)){
						$error .= "Extension not allowed. ";
					}
					if($file["file_document"]["size"] > 26214400){ //26214400 Bytes = 25 MB, 102400 = 100KB
						$error .= "File size shoud be less than 25 MB ";
					}
					if($error=="") { 
						
						$fileName 	= $this->db->clean($file["file_document"]["name"]);			
						$fileSize 	= round($file["file_document"]["size"]); // BYTES									
						$adate 		= date('Y-m-d H:i:m');
						
						$extension	= end(explode(".", $fileName));						
						$fileName	= '_emp_document_'.substr(sha1(time()), 0, 6).".".$extension;
						$filePath 	= "../images/employee/document/".$fileName;						
						move_uploaded_file($file['file_document']['tmp_name'], $filePath);
						
						// Update User Profile Image in database
						$values=array("proof_document"=>$fileName);
						$where="id='".$_REQUEST['id']."' ";
						$emp_img_update=$this->db->rp_update("emp_personal_info",$values,$where,0);
						$var=mysqli_query($emp_img_update);
					
						$reply=array("ack"=>1,"developer_msg"=>"image successfully uploaded!!","ack_msg"=>"Image successfully uploaded!!");
						return $reply;
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"image type not valid","ack_msg"=>"Invalid image or image not found.");
						return $reply;
					} 
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"image type not valid","ack_msg"=>"Invalid image or image not found.");
					return $reply;
				}
		}	
	public function getNotification($user_id,$user_type)
	{
		$result=array();
		
			$notification_r=$this->db->rp_getData("notification","id,user_id,user_type,notification_title,notification_type,notification_description","user_id='".$user_id."' AND user_type='".$user_type."'","id desc",0);
			if($notification_r){
				while($notification_d=mysqli_fetch_assoc($notification_r)){
					$notification_d['customer_name']=$this->db->rp_getValue("customer","name","id='".$user_id."'");
					$result[]=$notification_d;
				}
				$reply=array("ack"=>1,"developer_msg"=>"Notification Get Successfully.","ack_msg"=>"Notification Get Successfully.","result"=>$result);
				return $reply;
			}
			else{
				$reply=array("ack"=>0,"developer_msg"=>"Notification Not Available!","ack_msg"=>"Notification Not Available!");
				return $reply;
			}
	}
}

?>