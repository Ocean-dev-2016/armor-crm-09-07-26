<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("push_notification.class.php"); 
class Expense extends Functions
{
	public $db;
	public $ctable="expense";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		  

		$this->objPushNotification=new PushNotification(); 
    } 
	public function InsertExpense($detail,$file) 
	{
		$system = new System();
		extract($detail);

		/*$dup_where = "expense_date = '".$expense_date."' AND sales_executive_id='".$sales_executive_id."' AND isDelete=0 AND isActive=1";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Already Exist Expense Date","ack_msg"=>"Expense already Exist on this date.");
			return $reply;
		}
		else
		{*/
		// $expense_date=date('Y-m-d',strtotime($detail['expense_date']));
		// $total=$DA + $TA + $MOA + $NA + $extra;

			// print_r($detail);exit();
		
		if($detail!="")
		{
			$expense_type=$this->db->rp_getValue("expence_sub_category","expense_type","id='".$subcategory_id."'",0);
			$fix_amount=$this->db->rp_getValue("expence_sub_category","fix_amount","id='".$subcategory_id."'",0);

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
					$filePath 	= EXPENCE_A.$image_path;	
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

			$expense_date=date('Y-m-d H:i:s');
			$adate	= date('Y-m-d H:i:s');
			$expense_claim_type = isset($expense_claim_type) && $expense_claim_type != "" ? $expense_claim_type : 1;
			$advance_expense_type = isset($advance_expense_type) && $advance_expense_type != "" ? $advance_expense_type : 0;
			$rows 	= array(
				"sales_executive_id",
				"category_id",
				"subcategory_id",
				"total",
				"remark",
				"expense_date",
				"expense_type",
				"expense_claim_type",
				"advance_expense_type",
				"fix_amount",
				"isDelete",
				"isActive",
				"entry_flag",
				"image_path",
			);
			$values = array(
				$sales_executive_id,	
				$category_id,	
				$subcategory_id,	
				$total,		
				$remark,	
				$expense_date,	
				$expense_type,
				$expense_claim_type,
				$advance_expense_type,
				$fix_amount,	
				0,
				1,
				$entry_flag,
				$image_path,
			);
					
		 	$inserted_id = $this->db->rp_insert($this->ctable,$values,$rows,0);
				
			if($inserted_id)
			{
				$type=$this->db->rp_getValue("sales_executive","type","id=".$sales_executive_id."");
				  
				/*$title_description="Expense of <b>Rs.".$total."</b> for date <b>".date('d-m-Y',strtotime($expense_date))."</b> added by <b>admin</b> to your account";
				$notification=$system->setNotification(0,$sales_executive_id,"Expense Notification.",1,"Expense Message",$title_description,"","",$expense_date,$inserted_id,"expense",$type);*/
				
				// send notification
				$expence_category_nm = $this->db->rp_getValue("expence_category","name","id='".$category_id."'",0);
				if($_SESSION[SITE_SESS.'REFERANCE_TYPE']!=0)
				{
					$expense_by_name=$this->db->rp_getValue("sales_executive","name","isDelete=0 AND id='". $_SESSION[SITE_SESS.'REFERANCE_ID']."'",0); 
				}
				else{
					$expense_by_name="Admin";
				}
			    $notification_description = "Your ".$expence_category_nm." for date ".date("d-m-Y",strtotime($expense_date))." has been added by ".$expense_by_name;  

				$result_sales=$this->objPushNotification->commonNotification($sales_executive_id,$inserted_id,"expense","Expense Added",$notification_description,"sales_executive","expense");
				// send notification

				$reply=array("ack"=>1,"developer_msg"=>"Expense Added.","ack_msg"=>"Success! Expense Insert Successfully.","inserted_id"=>$inserted_id);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Expense Insert Failed.");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Expense is 0 any one expense require -Insert Failed.");
			return $reply;
		}
		// }
	}
	 
	public function InsertExpense_service($detail,$file) 
	{

		$system = new System();
		extract($detail);
		$expense_date=date('Y-m-d',strtotime($detail['expense_date']));

		/*if($subcat_slug=="car" || $subcat_slug=="bike")
		{*/
		$expense_type = $this->db->rp_getValue("expence_sub_category","expense_type","id='".$subcategory_id."' AND expense_category_id='".$category_id."' AND isDelete=0",0);

		if($expense_type==3)
		{
			$date_time = date('Y-m-d H:i:s');

			$min_time = $this->db->rp_getValue("expence_sub_category","min_time","id='".$subcategory_id."' AND isDelete=0",0);
			$max_time = $this->db->rp_getValue("expence_sub_category","max_time","id='".$subcategory_id."' AND isDelete=0",0);

			$fix_amount = $this->db->rp_getValue("expence_sub_category","fix_amount","id='".$subcategory_id."' AND isDelete=0",0);

			/*echo $date_time. "\n" ;
			echo $valid_min_time." - ".$valid_max_time ; exit;*/
			if($min_time!="" && $max_time!="")
			{
				$valid_min_time=date("Y-m-d H:i",strtotime($min_time));
				$valid_max_time=date("Y-m-d H:i",strtotime($max_time));
				if($date_time>=$valid_min_time && $date_time<=$valid_max_time)
				{
					$expense_date=date('Y-m-d',strtotime($detail['expense_date']));
					$adate	= date('Y-m-d H:i:s');
					$rows 	= array(
						"sales_executive_id",
						"category_id",
						"subcategory_id",
						"expense_type",
						"expense_claim_type",
						"expense_date",
						"start_kilometer",
						"end_kilometer",
						"total_kilometer",
						"fix_amount",
						"total",
						"remark",
						"isDelete",
						"isActive",
						"entry_flag",
					);
					$values = array(
						$sales_executive_id,
						$category_id,	
						$subcategory_id,
						$expense_type,
						isset($expense_claim_type) && $expense_claim_type != "" ? $expense_claim_type : 1,
						$expense_date,	
						0,	
						0,	
						0,	
						$fix_amount,	
						$total,	
						$remark,	
						0,
						1,
						$entry_flag,
					);
								
				 	$inserted_id = $this->db->rp_insert($this->ctable,$values,$rows,0);
				 	/*$reply=array("ack"=>1,"developer_msg"=>"Expense Added.","ack_msg"=>"Success! Expense Insert Successfully.","inserted_id"=>$inserted_id);
					return $reply;*/
				}	
				else
				{
					$reply=array( "ack"=>0,"ack_msg"=>"You are late","developer_msg"=>"not inserted!!");
					return $reply;
				}
			}
			else
			{
				$expense_date=date('Y-m-d',strtotime($detail['expense_date']));
				$adate	= date('Y-m-d H:i:s');
				$rows 	= array(
					"sales_executive_id",
					"category_id",
					"subcategory_id",
					"expense_type",
					"expense_claim_type",
					"expense_date",
					"start_kilometer",
					"end_kilometer",
					"total_kilometer",
					"fix_amount",
					"total",
					"remark",
					"isDelete",
					"isActive",
					"entry_flag",
				);
				$values = array(
					$sales_executive_id,
					$category_id,	
					$subcategory_id,
					$expense_type,
					isset($expense_claim_type) && $expense_claim_type != "" ? $expense_claim_type : 1,
					$expense_date,	
					0,	
					0,	
					0,	
					$fix_amount,	
					$total,	
					$remark,	
					0,
					1,
					$entry_flag,
				);	
			 	$inserted_id = $this->db->rp_insert($this->ctable,$values,$rows,0);
			}
		}
		else
		{
			$fix_amount = $this->db->rp_getValue("expence_sub_category","fix_amount","id='".$subcategory_id."' AND isDelete=0",0);

			if($start_kilometer=="" && $end_kilometer=="" && $total_kilometer=="")
			{
				$start_kilometer = 0;
				$end_kilometer   = 0;
				$total_kilometer = 0;
			}
			$expense_date=date('Y-m-d',strtotime($detail['expense_date']));
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
				"sales_executive_id",
				"category_id",
				"subcategory_id",
				"expense_type",
				"expense_claim_type",
				"expense_date",
				"start_kilometer",
				"end_kilometer",
				"total_kilometer",
				"fix_amount",
				"total",
				"remark",
				"isDelete",
				"isActive",
				"entry_flag",
			);
			$values = array(
				$sales_executive_id,
				$category_id,	
				$subcategory_id,
				$expense_type,
				isset($expense_claim_type) && $expense_claim_type != "" ? $expense_claim_type : 1,
				$expense_date,	
				$start_kilometer,	
				$end_kilometer,	
				$total_kilometer,	
				$fix_amount,	
				$total,	
				$remark,	
				0,
				1,
				$entry_flag,
			);
					
		 	$inserted_id = $this->db->rp_insert($this->ctable,$values,$rows,0);
		 
		}

		if($inserted_id)
		{
			$image_path=array();
			if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
			{
				$ri = $inserted_id;
				$rt = "expense";
				$tc = "expense";
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
				$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$inserted_id."'",0);
			}
			$expense_by_name=$this->db->rp_getValue("sales_executive","name","id=".$sales_executive_id."");

			// send notification
		    $expence_category_nm = $this->db->rp_getValue("expence_category","name","id='".$category_id."'",0);
				 
			$notification_description = "Your ".$expence_category_nm." for date ".date("d-m-Y",strtotime($expense_date))." has been added by ".$expense_by_name;  
			  	
			$result_sales=$this->objPushNotification->commonNotification($sales_executive_id,$inserted_id,"expense","Expense Added",$notification_description,"sales_executive","expense");
			// send notification

			$reply=array("ack"=>1,"developer_msg"=>"Expense Added.","ack_msg"=>"Success! Expense Insert Successfully.","inserted_id"=>$inserted_id);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Expense Insert Failed.");
			return $reply;
		}
			// }
				/*else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Expense is 0 any one expense require -Insert Failed.");
					return $reply;
				}*/
			//}
		/*}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"You Can Not Add Expense For This Category.","ack_msg"=>"You Can Not Add Expense For This Category.");
			return $reply;
		}*/
	}
	
	public function RejectExpense($detail)
	{
		$system = new System();
		$rows 	= array(
		"isActive"	=> "0"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$refreshTokens=array();
				$expense_r=$this->db->rp_getdata("expense","*","id='".$_REQUEST['id']."'");
				$expense_d=mysqli_fetch_assoc($expense_r);
				$sales_id=$expense_d['sales_executive_id'];
				$expense_date=$expense_d['expense_date'];
				$total=$expense_d['total'];
				$type=$this->db->rp_getValue("sales_executive","type","id=".$sales_id."");
				
				///////////////////////// Send Notification ///////////////////////

				$title_description="Expense of Rs.".$total." for date ".date('d-m-Y',strtotime($expense_date))." Rejected by admin";
				$notification=$system->setNotification(0,$sales_id,"Expense Notification.",1,"Expense Message",$title_description,"","",$expense_date,$_REQUEST['id'],"expense",$type);
				//print_r($notification); exit;

				/*$no_details=$this->db->rp_getData("notification","*","id='".$notification."'");
				if($no_details){
					$no_details=mysqli_fetch_assoc($no_details);
					$notification_data=$no_details;
				}
				$data=$notification_data;

				$refresh_tokens=$this->db->rp_getData("sales_executive","refreshToken","id='".$sales_id."'","",0);
				
					if($refresh_tokens){
						$tokens=array();
						while($refresh_token=mysqli_fetch_assoc($refresh_tokens)){
							$tokens[]=$refresh_token['refreshToken'];
						}
						$result=$this->db->send_notification($data,$tokens);
						//print_r($result); exit;
					}*/



				$reply=array("ack"=>1,"developer_msg"=>"Reject data.","ack_msg"=>"Success! Reject This Expense Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Reject Expense Failed.");
				return $reply;
			}
	}
	
	public function UpdateExpense_service($detail,$file) 
	{
		$system = new System();
		extract($detail);
		$expense_date=date('Y-m-d',strtotime($detail['expense_date']));

		$expense_type = $this->db->rp_getValue("expence_sub_category","expense_type","id='".$subcategory_id."' AND expense_category_id='".$category_id."' AND isDelete=0",0);

		if($expense_type==3)
		{
			$date_time = date('Y-m-d H:i:s');

			$min_time = $this->db->rp_getValue("expence_sub_category","min_time","id='".$subcategory_id."' AND isDelete=0",0);
			$max_time = $this->db->rp_getValue("expence_sub_category","max_time","id='".$subcategory_id."' AND isDelete=0",0);

			$fix_amount = $this->db->rp_getValue("expence_sub_category","fix_amount","id='".$subcategory_id."' AND isDelete=0",0);

			$valid_min_time=date("Y-m-d H:i",strtotime($min_time));
			$valid_max_time=date("Y-m-d H:i",strtotime($max_time));

			/*echo $date_time. "\n" ;
			echo $valid_min_time." - ".$valid_max_time ; exit;*/

			if($date_time>=$valid_min_time && $date_time<=$valid_max_time)
			{
				$expense_date=date('Y-m-d',strtotime($detail['expense_date']));
				$adate	= date('Y-m-d H:i:s');
				$rows 	= array(
					"sales_executive_id" =>$sales_executive_id,
					"category_id"        =>$category_id,
					"subcategory_id"     =>$subcategory_id,
					"expense_type"       =>$expense_type,
					"expense_date"       =>$expense_date,
					"start_kilometer"    =>0,
					"end_kilometer"      =>0,
					"total_kilometer"    =>0,
					"fix_amount"         =>$fix_amount,
					"total"              =>$total,
					"remark"             =>$remark,
				);
				
				$Where = "id='".$id."'";	
			 	$inserted_id = $this->db->rp_update($this->ctable,$rows,$Where,0);
			 	$reply=array("ack"=>1,"developer_msg"=>"Expense Update.","ack_msg"=>"Success! Expense Update Successfully.","inserted_id"=>$id);
				return $reply;
			}	
			else
			{
				$reply=array( "ack"=>0,"ack_msg"=>"You are late","developer_msg"=>"not inserted!!");
				return $reply;
			}
		}
		else
		{
			$fix_amount = $this->db->rp_getValue("expence_sub_category","fix_amount","id='".$subcategory_id."' AND isDelete=0",0);

			if($start_kilometer=="" && $end_kilometer=="" && $total_kilometer=="")
			{
				$start_kilometer = 0;
				$end_kilometer   = 0;
				$total_kilometer = 0;
			}
			$expense_date=date('Y-m-d',strtotime($detail['expense_date']));
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
				"sales_executive_id" =>$sales_executive_id,
				"category_id"        =>$category_id,
				"subcategory_id"     =>$subcategory_id,
				"expense_type"       =>$expense_type,
				"expense_date"       =>$expense_date,
				"start_kilometer"    =>$start_kilometer,
				"end_kilometer"      =>$end_kilometer,
				"total_kilometer"    =>$total_kilometer,
				"fix_amount"         =>$fix_amount,
				"total"              =>$total,
				"remark"             =>$remark,
						
			);
			
					
			$Where = "id='".$id."'";	
			$inserted_id1 = $this->db->rp_update($this->ctable,$rows,$Where,0);
		 	if($inserted_id1)
			{
				$image_path=array();
				/*if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
				{
					$ri = $inserted_id1;
					$rt = "expense";
					$tc = "expense";
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
					$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$inserted_id1."'",0);
				}*/
				//$username=$this->db->rp_getValue("sales_executive","username","id=".$sales_executive_id."");
				$reply=array("ack"=>1,"developer_msg"=>"Expense Update.","ack_msg"=>"Success! Expense Update Successfully.","inserted_id"=>$id);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Expense Update Failed.");
				return $reply;
			}
		}
	}

	public function AddVehicleexpense($detail,$file)
	{
		extract($detail);

		$type_flag = isset($type_flag) ? $type_flag : "";
		$subcat_slug = isset($subcat_slug) ? $subcat_slug : "";
		$sales_executive_id = isset($sales_executive_id) ? $sales_executive_id : "";
		$start_km = isset($start_km) ? $start_km : "";
		$end_km = isset($end_km) ? $end_km : "";
		$tmp_id = isset($id) && $id != "" ? $id : (isset($_REQUEST['id']) ? $_REQUEST['id'] : "");

		if ($sales_executive_id == "" || $subcat_slug == "") {
			return array("ack" => 0, "developer_msg" => "sales_executive_id and subcat_slug required", "ack_msg" => "Sales person and vehicle type are required.");
		}

		$subcat_id = $this->db->rp_getValue("expence_sub_category", "id", "slug='" . $subcat_slug . "' AND sales_executive_id='" . $sales_executive_id . "' AND isDelete=0 AND isActive=1", 0);
		$category_id = $this->db->rp_getValue("expence_sub_category", "expense_category_id", "slug='" . $subcat_slug . "' AND sales_executive_id='" . $sales_executive_id . "' AND isDelete=0 AND isActive=1", 0);
		$fix_amount = $this->db->rp_getValue("expence_sub_category", "fix_amount", "slug='" . $subcat_slug . "' AND sales_executive_id='" . $sales_executive_id . "' AND isDelete=0 AND isActive=1", 0);

		if (!$subcat_id || !$category_id) {
			return array("ack" => 0, "developer_msg" => "Vehicle subcategory not found", "ack_msg" => "Expense Sub Category for " . $subcat_slug . " is not configured for this sales person.");
		}

		/* type_flag: 1 = Start KM, 2 = End KM */
		if ($type_flag == "1" || $type_flag == 1) {
			if ($start_km === "" || !is_numeric($start_km)) {
				return array("ack" => 0, "developer_msg" => "start_km required", "ack_msg" => "Please enter Start km.");
			}

			// Close any open trip for same SE + vehicle without end
			$openWhere = "sales_executive_id='" . $sales_executive_id . "' AND subcategory_id='" . $subcat_id . "' AND isDelete=0 AND isActive=1 AND (end_date_time IS NULL OR end_date_time='' OR end_date_time='0000-00-00 00:00:00')";
			$openRow = $this->db->rp_getData("expense_tmp", "id", $openWhere, "id DESC", 0);
			if ($openRow) {
				$openD = mysqli_fetch_assoc($openRow);
				if (!empty($openD['id'])) {
					return array("ack" => 0, "developer_msg" => "Open trip exists id=" . $openD['id'], "ack_msg" => "Please complete previous trip first (enter End km).", "inserted_id" => $openD['id']);
				}
			}

			$start_date_time = isset($start_date_time) && $start_date_time != "" ? $start_date_time : date('Y-m-d H:i:s');
			$rows = array(
				"sales_executive_id",
				"category_id",
				"subcategory_id",
				"start_date_time",
				"start_km",
				"isDelete",
				"isActive",
			);
			$value = array(
				$sales_executive_id,
				$category_id,
				$subcat_id,
				$start_date_time,
				$start_km,
				0,
				1,
			);

			$inserted_id = $this->db->rp_insert("expense_tmp", $value, $rows, 0);
			if (!$inserted_id) {
				return array("ack" => 0, "developer_msg" => "expense_tmp insert failed", "ack_msg" => "Failed! Expense Start Failed.");
			}

			$start_image = $this->saveVehicleExpenseImage($file, $inserted_id);
			if ($start_image != "") {
				$this->db->rp_update("expense_tmp", array("start_image" => $start_image), "id='" . $inserted_id . "'", 0);
			}

			return array("ack" => 1, "developer_msg" => "Expense trip started.", "ack_msg" => "Success! Start km saved.", "inserted_id" => $inserted_id);
		}

		// STOP / End KM
		if ($type_flag == "2" || $type_flag == 2) {
			if ($end_km === "" || !is_numeric($end_km)) {
				return array("ack" => 0, "developer_msg" => "end_km required", "ack_msg" => "Please enter End km.");
			}

			$data = false;
			if ($tmp_id != "") {
				$data = $this->db->rp_getData("expense_tmp", "*", "id='" . $tmp_id . "' AND sales_executive_id='" . $sales_executive_id . "' AND isDelete=0", "", 0);
			}

			// If id not passed or not found by id, lookup the latest open trip for this sales person
			if (!$data || mysqli_num_rows($data) == 0) {
				$openWhere = "sales_executive_id='" . $sales_executive_id . "' AND isDelete=0 AND isActive=1 AND (end_date_time IS NULL OR end_date_time='' OR end_date_time='0000-00-00 00:00:00')";
				if ($subcat_id) {
					$openWhere .= " AND subcategory_id='" . $subcat_id . "'";
				}
				$data = $this->db->rp_getData("expense_tmp", "*", $openWhere, "id DESC LIMIT 1", 0);
			}

			if (!$data || mysqli_num_rows($data) == 0) {
				return array("ack" => 0, "developer_msg" => "expense_tmp not found", "ack_msg" => "Trip not found. Please start trip again.");
			}
			$r = mysqli_fetch_assoc($data);
			$tmp_id = $r['id'];
			$start_kilometer = floatval($r['start_km']);
			$end_kilometer = floatval($end_km);

			if ($end_kilometer < $start_kilometer) {
				return array("ack" => 0, "developer_msg" => "end_km < start_km", "ack_msg" => "End km must be greater than or equal to Start km (" . $start_kilometer . ").");
			}

			$total_kilometer = $end_kilometer - $start_kilometer;
			$fix_amount = $this->db->rp_getValue("expence_sub_category", "fix_amount", "id='" . $r['subcategory_id'] . "' AND isDelete=0", 0);
			if ($fix_amount === false || $fix_amount === "") {
				$fix_amount = 0;
			}
			$total = $total_kilometer * floatval($fix_amount);

			$end_image = $this->saveVehicleExpenseImage($file, $tmp_id);
			$updRows = array(
				"end_date_time" => date('Y-m-d H:i:s'),
				"end_km" => $end_km,
			);
			if ($end_image != "") {
				$updRows["end_image"] = $end_image;
			}
			$this->db->rp_update("expense_tmp", $updRows, "id='" . $tmp_id . "'", 0);

			// refresh after update
			$data2 = $this->db->rp_getData("expense_tmp", "*", "id='" . $tmp_id . "'", "", 0);
			$r2 = mysqli_fetch_assoc($data2);
			$start_path = isset($r2['start_image']) ? $r2['start_image'] : "";
			$end_path = isset($r2['end_image']) ? $r2['end_image'] : "";
			$new_path = trim($start_path . ($start_path != "" && $end_path != "" ? "," : "") . $end_path, ",");

			$expenseRows = array(
				"sales_executive_id",
				"category_id",
				"subcategory_id",
				"expense_type",
				"expense_claim_type",
				"expense_date",
				"image_path",
				"start_kilometer",
				"end_kilometer",
				"total_kilometer",
				"fix_amount",
				"total",
				"remark",
				"isDelete",
				"isActive",
				"entry_flag",
			);
			$expenseValues = array(
				$r2['sales_executive_id'],
				$r2['category_id'],
				$r2['subcategory_id'],
				2, // kilometer type
				1, // Regular
				date('Y-m-d'),
				$new_path,
				$start_kilometer,
				$end_kilometer,
				$total_kilometer,
				$fix_amount,
				$total,
				"Vehicle " . $subcat_slug . " trip",
				0,
				1,
				5,
			);

			$expense_id = $this->db->rp_insert("expense", $expenseValues, $expenseRows, 0);
			if (!$expense_id) {
				return array("ack" => 0, "developer_msg" => "expense insert failed", "ack_msg" => "Failed! Expense Update Failed.");
			}

			// link media to final expense
			foreach (array($start_path, $end_path) as $mediaId) {
				if ($mediaId != "" && is_numeric($mediaId)) {
					$this->db->rp_update("media", array("reference_id" => $expense_id), "id='" . $mediaId . "'", 0);
				}
			}

			// mark tmp completed
			$this->db->rp_update("expense_tmp", array("isActive" => 0), "id='" . $tmp_id . "'", 0);

			return array(
				"ack" => 1,
				"developer_msg" => "Expense trip completed. total_km=" . $total_kilometer . " total=" . $total,
				"ack_msg" => "Success! Expense saved for " . $total_kilometer . " km.",
				"inserted_id" => $expense_id,
				"tmp_id" => $tmp_id,
				"start_km" => $start_kilometer,
				"end_km" => $end_kilometer,
				"total_kilometer" => $total_kilometer,
				"fix_amount" => $fix_amount,
				"total" => $total,
			);
		}

		return array("ack" => 0, "developer_msg" => "Invalid type_flag", "ack_msg" => "Invalid request. type_flag must be 1 (start) or 2 (end).");
	}

	private function saveVehicleExpenseImage($file, $referenceId)
	{
		if (!isset($file["image_path"]) || empty($file["image_path"]["name"])) {
			return "";
		}

		$names = $file["image_path"]["name"];
		$tmps = $file["image_path"]["tmp_name"];
		$isMulti = is_array($names);
		$count = $isMulti ? count($names) : 1;
		$imageIds = array();
		$allowed = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG", "PNG");

		for ($i = 0; $i < $count; $i++) {
			$fileName = $isMulti ? $names[$i] : $names;
			$fileTmp = $isMulti ? $tmps[$i] : $tmps;
			if ($fileName == "" || $fileTmp == "") {
				continue;
			}
			$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
			if (!in_array($extension, $allowed)) {
				continue;
			}
			$attachment = "../resource/image/";
			@move_uploaded_file($fileTmp, $attachment . $fileName);
			$Values = array($fileName, $fileName, $fileName, $extension, date("Y-m-d H:i:s"), $referenceId, "expense", "expense");
			$Columns = array("title", "orignal_title", "url", "ext", "upload_date", "reference_id", "reference_table", "reference_column");
			$MediaID = $this->db->rp_insert("media", $Values, $Columns, 0);
			if ($MediaID) {
				$imageIds[] = $MediaID;
			}
		}

		return !empty($imageIds) ? implode(",", $imageIds) : "";
	}

	public function InsertAdvanceExpense($detail, $file)
	{
		extract($detail);

		if ($sales_executive_id == "" || $category_id == "" || $total == "") {
			return array("ack" => 0, "developer_msg" => "Required fields missing", "ack_msg" => "sales_executive_id, category_id and total are required.");
		}

		$categoryWhere = "id='" . $category_id . "' AND isDelete=0 AND isActive=1";
		$categoryClaimType = $this->db->rp_getValue("expence_category", "expense_claim_type", $categoryWhere, 0);
		if ($categoryClaimType === false || $categoryClaimType === "") {
			return array("ack" => 0, "developer_msg" => "Invalid category", "ack_msg" => "Expense category not found.");
		}
		if ($categoryClaimType != "2") {
			return array("ack" => 0, "developer_msg" => "Category is not advance type", "ack_msg" => "Please select a valid Advance Expense category.");
		}

		$expense_date = isset($expense_date) && $expense_date != "" ? date('Y-m-d', strtotime($expense_date)) : date('Y-m-d');
		$image_path = "";
		if (isset($file["image_path"]) && !empty($file["image_path"]["name"])) {
			$allowedExts = array("jpg", "jpeg", "png", "gif", "JPG", "JPEG", "PNG");
			if (is_array($file["image_path"]["name"])) {
				$fileName = $this->db->clean($file["image_path"]["name"][0]);
				$fileTmp = $file["image_path"]["tmp_name"][0];
			} else {
				$fileName = $this->db->clean($file["image_path"]["name"]);
				$fileTmp = $file["image_path"]["tmp_name"];
			}
			if ($fileName != "") {
				$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
				if (in_array($extension, $allowedExts)) {
					$attachment = "../resource/image/";
					move_uploaded_file($fileTmp, $attachment . $fileName);
					$Values = array($fileName, $fileName, $fileName, $extension, date("Y-m-d H:i:s"), 0, "expense", "expense");
					$Columns = array("title", "orignal_title", "url", "ext", "upload_date", "reference_id", "reference_table", "reference_column");
					$MediaID = $this->db->rp_insert("media", $Values, $Columns, 0);
					if ($MediaID) {
						$image_path = $MediaID;
					}
				}
			}
		}

		$rows = array(
			"sales_executive_id",
			"category_id",
			"subcategory_id",
			"expense_type",
			"expense_claim_type",
			"advance_expense_type",
			"expense_date",
			"image_path",
			"start_kilometer",
			"end_kilometer",
			"total_kilometer",
			"fix_amount",
			"total",
			"remark",
			"isDelete",
			"isActive",
			"entry_flag",
		);
		$advance_expense_type = isset($advance_expense_type) && $advance_expense_type != "" ? $advance_expense_type : 0;
		$values = array(
			$sales_executive_id,
			$category_id,
			0,
			1,
			2,
			$advance_expense_type,
			$expense_date,
			$image_path,
			0,
			0,
			0,
			0,
			$total,
			$remark,
			0,
			1,
			$entry_flag,
		);

		$inserted_id = $this->db->rp_insert($this->ctable, $values, $rows, 0);
		if ($inserted_id) {
			if ($image_path != "") {
				$this->db->rp_update("media", array("reference_id" => $inserted_id), "id='" . $image_path . "'", 0);
				$this->db->rp_update($this->ctable, array("image_path" => $image_path), "id='" . $inserted_id . "'", 0);
			}

			$expence_category_nm = $this->db->rp_getValue("expence_category", "name", "id='" . $category_id . "'", 0);
			$expense_by_name = $this->db->rp_getValue("sales_executive", "name", "id=" . $sales_executive_id . "");
			$notification_description = "Your advance " . $expence_category_nm . " for date " . date("d-m-Y", strtotime($expense_date)) . " has been added by " . $expense_by_name;
			$this->objPushNotification->commonNotification($sales_executive_id, $inserted_id, "expense", "Advance Expense Added", $notification_description, "sales_executive", "expense");

			return array("ack" => 1, "developer_msg" => "Advance Expense Added.", "ack_msg" => "Success! Advance Expense Added Successfully.", "inserted_id" => $inserted_id);
		}

		return array("ack" => 0, "developer_msg" => "Database error!!", "ack_msg" => "Failed! Advance Expense Insert Failed.");
	}

	public function ensureAutoCloseTripColumns()
	{
		$colCheck = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `expense_tmp` LIKE 'auto_closed'");
		if (!$colCheck || mysqli_num_rows($colCheck) == 0) {
			@mysqli_query($this->db->myconn, "ALTER TABLE `expense_tmp` ADD `auto_closed` TINYINT(1) NOT NULL DEFAULT 0");
		}
		$colCheck2 = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `expense_tmp` LIKE 'auto_closed_remark'");
		if (!$colCheck2 || mysqli_num_rows($colCheck2) == 0) {
			@mysqli_query($this->db->myconn, "ALTER TABLE `expense_tmp` ADD `auto_closed_remark` VARCHAR(255) NULL DEFAULT NULL");
		}
		$colCheck3 = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `expense_tmp` LIKE 'auto_closed_at'");
		if (!$colCheck3 || mysqli_num_rows($colCheck3) == 0) {
			@mysqli_query($this->db->myconn, "ALTER TABLE `expense_tmp` ADD `auto_closed_at` DATETIME NULL DEFAULT NULL");
		}
	}

	public function hasAutoClosedColumn()
	{
		$colCheck = @mysqli_query($this->db->myconn, "SHOW COLUMNS FROM `expense_tmp` LIKE 'auto_closed'");
		return ($colCheck && mysqli_num_rows($colCheck) > 0);
	}

	/**
	 * Auto-close open vehicle trips at day end (11:30 PM).
	 * Only trips STARTED on the same target date. Keeps record in expense_tmp only — no expense table entry.
	 *
	 * @param string $targetDate  Y-m-d (default today)
	 * @param array  $options     allow_early=true skips 11:30 PM guard (past-date catch-up only)
	 */
	public function autoCloseForgottenTrips($targetDate = "", $options = array())
	{
		$this->ensureAutoCloseTripColumns();

		if ($targetDate == "") {
			$targetDate = date('Y-m-d');
		} else {
			$targetDate = date('Y-m-d', strtotime($targetDate));
		}

		$today = date('Y-m-d');
		$nowTime = date('H:i:s');
		$cutoffTime = '23:30:00';
		$allowEarly = !empty($options['allow_early']);

		// Today's trips: close only after 11:30 PM (cron time). Not during the day.
		if ($targetDate == $today && !$allowEarly && $nowTime < $cutoffTime) {
			return array(
				"ack" => 1,
				"developer_msg" => "Skipped - before 11:30 PM for today (" . $nowTime . ")",
				"ack_msg" => "Today's open trips will auto-close at 11:30 PM only. Current time: " . date('h:i A') . ".",
				"closed_count" => 0,
				"closed_ids" => array(),
				"target_date" => $targetDate,
				"skipped" => true,
				"errors" => array(),
			);
		}

		$endDateTime = $targetDate . ' 23:30:00';
		$remark = 'Auto closed at 11:30 PM - End KM not entered by employee';

		// Same-day only: trip must have started on target date (not old backlog trips)
		$where = "isDelete=0 AND isActive=1 AND (end_date_time IS NULL OR end_date_time='' OR end_date_time='0000-00-00 00:00:00') AND DATE(start_date_time)='" . $targetDate . "' AND auto_closed=0";
		$data = $this->db->rp_getData("expense_tmp", "*", $where, "id ASC", 0);

		$closed = 0;
		$closedIds = array();
		$errors = array();

		if ($data) {
			while ($row = mysqli_fetch_assoc($data)) {
				$tmpId = $row['id'];
				$startKm = isset($row['start_km']) ? $row['start_km'] : 0;
				$upd = array(
					"end_km" => $startKm,
					"end_date_time" => $endDateTime,
					"isActive" => 0,
					"auto_closed" => 1,
					"auto_closed_remark" => $remark,
					"auto_closed_at" => date('Y-m-d H:i:s'),
				);
				$ok = $this->db->rp_update("expense_tmp", $upd, "id='" . $tmpId . "'", 0);
				if ($ok) {
					$closed++;
					$closedIds[] = $tmpId;
				} else {
					$errors[] = $tmpId;
				}
			}
		}

		return array(
			"ack" => 1,
			"developer_msg" => "Auto closed " . $closed . " trip(s) started on " . $targetDate,
			"ack_msg" => $closed . " trip(s) auto-closed for " . date('d-m-Y', strtotime($targetDate)) . " (same-day only).",
			"closed_count" => $closed,
			"closed_ids" => $closedIds,
			"target_date" => $targetDate,
			"skipped" => false,
			"errors" => $errors,
		);
	}

	/** Re-open wrongly auto-closed trips (admin fix). */
	public function revertAutoClosedTrips($tripIds = array())
	{
		if (!is_array($tripIds) || empty($tripIds)) {
			return array("ack" => 0, "ack_msg" => "No trip IDs provided.", "reverted_count" => 0);
		}
		$ids = array();
		foreach ($tripIds as $id) {
			$id = intval($id);
			if ($id > 0) {
				$ids[] = $id;
			}
		}
		if (empty($ids)) {
			return array("ack" => 0, "ack_msg" => "Invalid trip IDs.", "reverted_count" => 0);
		}
		$idList = implode(",", $ids);
		$where = "id IN (" . $idList . ") AND auto_closed=1 AND isDelete=0";
		$data = $this->db->rp_getData("expense_tmp", "id", $where, "", 0);
		$reverted = 0;
		$revertedIds = array();
		if ($data) {
			while ($row = mysqli_fetch_assoc($data)) {
				$upd = array(
					"end_km" => "",
					"end_date_time" => "",
					"isActive" => 1,
					"auto_closed" => 0,
					"auto_closed_remark" => "",
					"auto_closed_at" => "",
				);
				$ok = $this->db->rp_update("expense_tmp", $upd, "id='" . $row['id'] . "'", 0);
				if ($ok) {
					$reverted++;
					$revertedIds[] = $row['id'];
				}
			}
		}
		return array(
			"ack" => 1,
			"ack_msg" => $reverted . " trip(s) re-opened.",
			"reverted_count" => $reverted,
			"reverted_ids" => $revertedIds,
		);
	}

}

?>