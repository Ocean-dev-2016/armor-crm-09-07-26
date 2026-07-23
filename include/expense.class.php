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
		$subcat_id = $this->db->rp_getValue("expence_sub_category","id","slug='".$subcat_slug."' AND sales_executive_id='".$sales_executive_id."' AND isDelete=0 ",0);
		$category_id = $this->db->rp_getValue("expence_sub_category","expense_category_id","slug='".$subcat_slug."' AND sales_executive_id='".$sales_executive_id."' AND isDelete=0 ");
		/*$type_flag = array("1"=>"Start","2"=>"stop");*/
		if($type_flag==1)
		{
			$rows 	= array(
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

			$inserted_id = $this->db->rp_insert("expense_tmp",$value,$rows,0);
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
					$file_name = $file['image_path']['name'];
					$file_size = $file['image_path']['size'];
					$file_tmp  = $file['image_path']['tmp_name'];
					$file_type = $file['image_path']['type'];
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
				$upadateid1 = $this->db->rp_update("expense_tmp",array("start_image"=>$image_path),"
					id='".$inserted_id."'",0);
				
			}
			if($inserted_id)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Expense Added.","ack_msg"=>"Success! Expense Insert Successfully.","inserted_id"=>$inserted_id);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Expense Update Failed.");
				return $reply;
			}
		}
		else
		{
			$rows 	= array(
				"end_date_time" =>date('Y-m-d H:i:s'), 
				"end_km"        => $end_km, 
			);

			$Where = "id='".$_REQUEST['id']."'";	
			$upadateid1 = $this->db->rp_update("expense_tmp",$rows,$Where,0);
			$image_path=array();
			if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
			{
				$ri = $_REQUEST['id'];
				$rt = "expense";
				$tc = "expense";
				$rc = "id";
				for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
				{
					//print_r($file["image_path"]);
					$file_name = $file['image_path']['name'];
					$file_size = $file['image_path']['size'];
					$file_tmp = $file['image_path']['tmp_name'];
					$file_type = $file['image_path']['type'];
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
				$upadateid1 = $this->db->rp_update("expense_tmp",array("end_image"=>$image_path),"
					id='".$_REQUEST['id']."'",0);
			}

			if($upadateid1)
			{

				$data = $this->db->rp_getData("expense_tmp","*","id='".$_REQUEST['id']."'",'',0);
				$r=mysqli_fetch_assoc($data);


				$start_path = $r['start_image'];
				$end_path   = $r['end_image'];


				$new_path =  $start_path.",".$end_path; 

				$start_kilometer = $r['start_km'];
				$end_kilometer = $r['end_km'];
				$total_kilometer = $end_kilometer - $start_kilometer;

				$fix_amount = $this->db->rp_getValue("expence_sub_category","fix_amount","id='".$r['subcategory_id']."' AND isDelete=0",0);

				$total = $total_kilometer * $fix_amount;

				// exit("sdf");
				$rows = array("sales_executive_id","category_id","subcategory_id","expense_type","expense_date","image_path","start_kilometer","end_kilometer","total_kilometer","fix_amount","total");

				$values = array($sales_executive_id,$category_id,$subcat_id,"2",date('Y-m-d'),$new_path,$start_kilometer,$end_kilometer,$total_kilometer,$fix_amount,$total);

				$inserted_id1 = $this->db->rp_insert("expense",$values,$rows,0);
				if($inserted_id1)
				{
					for($i1=0;$i1<=1;$i1++)
					{
						if($i1==0)
						{
							$where = "id='".$start_path."' ";
						}
						else
						{
							$where = "id='".$end_path."' ";	
						}
						$update = $this->db->rp_update("media",array("reference_id"=>$inserted_id1),$where,0);
					}
				}

				$reply=array("ack"=>1,"developer_msg"=>"Expense Added.","ack_msg"=>"Success! Expense Insert Successfully.","inserted_id"=>$_REQUEST['id']);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Expense Update Failed.");
				return $reply;
			}

		}

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

}

?>