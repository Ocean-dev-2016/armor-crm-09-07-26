<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("push_notification.class.php");
class LeaveRequest extends Functions
{
	public $db;
	public $ctable="leave_request";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
		
		$this->objPushNotification=new PushNotification(); 
    } 
    
	/*public function InsertLeaveRequest($detail,$file) 
	{
	    extract($detail);
    	$isDuplicate = false;
    	$count = $this->db->rp_getTotalRecord("leave_request","sales_executive_id='".$sales_executive_id."' AND start_date='".$start_date."' AND start_time='".$start_time."' AND end_date='".$end_date."' AND  end_time='".$end_time."' AND isDelete=0",0);
		if($count>=1)
		{
			$isDuplicate = true;
		}
    	

    	if($isDuplicate)
    	{
    		$ack=array("ack"=>0,"ack_msg"=>"Already Data Exist!!","developer_msg"=>"Already Data Exist");
			return $ack;
    	}
    	
    	if (isset($file["image_path"]))
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
		    	$filePath 	= LEAVE_A.$image_path;
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
    	
    	$sales_executive_name = $this->db->rp_getValue("sales_executive","name","id='".$sales_executive_id."'",0);
		$rows	 = array(	
		            "sales_executive_id",
		            "sales_executive_name",
		            "user_id",
		            "leave_type",
					"reason",
					"file_path",
					"latitude",
					"longitude",
					"start_time",
					"start_date",
					"end_date",
					"end_time",
				);
		$values	 = array(
					$sales_executive_id,
					$sales_executive_name,
					$user_id,
					$leave_type,
					$leave_details,
					$image_path,
					$latitude,
					$longitude,
					$start_time,
					$start_date,
					$end_date,
					$end_time,
				);
				
		$leave_request_id = $this->db->rp_insert("leave_request",$values,$rows,0);
		if($leave_request_id)
		{
			$ack=array( "ack"=>1,"ack_msg"=>"Successfully Inserted Leave Request !!","developer_msg"=>"You got it!!","result"=>$result,);
			return $ack;
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"not inserted !!","developer_msg"=>"not inserted!!","result"=>array(),);
			return $ack;
		}
	}*/
	 
	 public function UpdateLeave($detail,$file)
	  {
	      
		    extract($detail);
        	$isDuplicate = false;
        	$count = $this->db->rp_getTotalRecord("leave_request","sales_executive_id='".$sales_executive_id."' AND start_date='".$start_date."' AND start_time='".$start_time."' AND end_date='".$end_date."' AND  end_time='".$end_time."' AND id!='".$_REQUEST['id']."' AND isDelete=0",0);
    		if($count>=1)
    		{
    			$isDuplicate = true;
    		}
    		
    		if($isDuplicate)
        	{
        		$ack=array("ack"=>0,"ack_msg"=>"Already Data Exist!!","developer_msg"=>"Already Data Exist");
    			return $ack;
        	} 
			
			$sales_executive_name = $this->db->rp_getValue("sales_executive","name","id='".$sales_executive_id."'",0);
			$rows 	= array(
				"sales_executive_id"	=> $sales_executive_id,
				"sales_executive_name"	=> $sales_executive_name,
				"user_id"			    => $user_id,
				"leave_type"            => $leave_type,
				"reason"			    => $reason,
				"latitude"	            => $latitude,
				"longitude"	            => $longitude,
				"start_time"	        => $start_time,
				"start_date"	        => $start_date,
				"end_date"	            => $end_date,
				"end_time"	            => $end_time,
				"update_entry_flag"	            => $update_entry_flag,
				"leave_category"	            => $leave_category,
			);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
			
			$image_path=array();
			if (isset($file["image_path"]) && $file["image_path"]['size']!=0) 
			{
				$ri = $_REQUEST['id'];
				$rt = "leave";
				$tc = "leave";
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
				$upadateid = $this->db->rp_update("leave_request",array("file_path"=>$image_path),"id='".$_REQUEST['id']."'",0);
			}
				
			if($uid!=0)
			{
				// send notification
				if(isset($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!="" && isset($_SESSION[SITE_SESS.'_ADMIN_TYPE']) && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!="")
				{
					if($_SESSION[SITE_SESS.'REFERANCE_TYPE']!=0)
					{
						$leave_by_name=$this->db->rp_getValue("sales_executive","name","isDelete=0 AND id='". $_SESSION[SITE_SESS.'REFERANCE_ID']."'",0); 
					}
					else{
						$leave_by_name="Admin";
					}
				}
				else
				{
					$leave_by_name=$sales_executive_name;
				}
				$leave_type_name = $this->db->rp_getValue("leave_type","name","id='".$leave_type."'");
			    $notification_description = $leave_type_name." has been edited from ".date('d-m-Y',strtotime($start_date))." to ".date('d-m-Y',strtotime($end_date))." by ".$leave_by_name; 
			    
				$result_sales=$this->objPushNotification->commonNotification($sales_executive_id,$_REQUEST['id'],"leave_request","Leave Edited",$notification_description,"sales_executive","leave_request");
				// send notification

				$reply=array("ack"=>1,"developer_msg"=>"Data Update Successfull!!.","ack_msg"=>"Success! Data Update Successfully.","id"=>$uid);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Update Failed.");
				return $reply;
			}
		}	
	public function GetEditDataLeaveType($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['sales_executive_id']		= htmlentities($ctable_d['sales_executive_id']);
		$result['sales_executive_name']		= htmlentities($ctable_d['sales_executive_name']);
		$result['leave_type']		        = htmlentities($ctable_d['leave_type']);
		$result['leave_details']		    = htmlentities($ctable_d['reason']);
		$result['latitude']		            = htmlentities($ctable_d['latitude']);
		$result['longitude']		        = htmlentities($ctable_d['longitude']);
		$result['start_time']		        = date("h:i a",strtotime($ctable_d['start_time']));
		$result['start_date']		        = date("d-m-Y",strtotime($ctable_d['start_date']));
		$result['end_time']		            = date("h:i a",strtotime($ctable_d['end_time']));
		$result['end_date']		            = date("d-m-Y",strtotime($ctable_d['end_date']));
		$result['image_path']		        = $ctable_d['file_path'];
		$result['leave_category']		        = htmlentities($ctable_d['leave_category']);
		
		//$imgpath = array();
		for ($i=0; $i < sizeof($image_path); $i++)
		{ 
			$imgpath = SITEURL."resource/image/".$this->db->rp_getValue("media","url","reference_id='".$detail['id']."' AND id='".$image_path[$i]."'",0);
		}
		$result['image_path'] = $imgpath;
		
		$reply=array("ack"=>1,"developer_msg"=>"Leave Type detail fetched!!.","ack_msg"=>"Success! Leave Type Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteLeave($detail)
	{
		$id=$detail['id'];
	    $rows 	= array(
			"isDelete"	=> "1"
		);
		$where	= "id='".$id."'";
		$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
		if($uid!=0)
		{
			// send notification 
		    $leaveData_r = $this->db->rp_getData("leave_request","*","id='".$id."'","",0);
			$leaveData_d = mysqli_fetch_assoc($leaveData_r);

			$sales_executive_id = $leaveData_d['sales_executive_id'];
			$start_date = $leaveData_d['start_date'];
			$end_date = $leaveData_d['end_date'];

			$leave_type_name = $this->db->rp_getValue("leave_type","name","id='".$leaveData_d['leave_type']."'");
			if(isset($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!="" && isset($_SESSION[SITE_SESS.'_ADMIN_TYPE']) && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!="")
			{
				if($_SESSION[SITE_SESS.'REFERANCE_TYPE']!=0)
				{
					$leave_by_name=$this->db->rp_getValue("sales_executive","name","isDelete=0 AND id='". $_SESSION[SITE_SESS.'REFERANCE_ID']."'",0); 
				}
				else{
					$leave_by_name="Admin";
				}
			}else
			{
				$leave_by_name=$this->db->rp_getValue("sales_executive","name","isDelete=0 AND id='". $sales_executive_id."'",0);
			}
		  
		    $notification_description = $leave_type_name." has been deleted from ".date('d-m-Y',strtotime($start_date))." to ".date('d-m-Y',strtotime($end_date))." by ".$leave_by_name; 
			 			
			$result_sales=$this->objPushNotification->commonNotification($sales_executive_id,$id,"leave_request","Leave Deleted",$notification_description,"sales_executive","leave_request");
			// send notification

			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Leave Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Leave Failed.");
			return $reply;
		}
	}
	
	public function InsertLeave($detail,$file) 
	{
	    //print_r($file);exit;
	    extract($detail);
    	$isDuplicate = false;
    	$count = $this->db->rp_getTotalRecord("leave_request","sales_executive_id='".$sales_executive_id."' AND start_date='".$start_date."' AND start_time='".$start_time."' AND end_date='".$end_date."' AND  end_time='".$end_time."' AND isDelete=0",0);
		if($count>=1)
		{
			$isDuplicate = true;
		}
    	
    	if($isDuplicate)
    	{
    		$ack=array("ack"=>0,"ack_msg"=>"Already Data Exist!!","developer_msg"=>"Already Data Exist");
			return $ack;
    	}
    	
    	$sales_executive_name = $this->db->rp_getValue("sales_executive","name","id='".$sales_executive_id."'",0);
		$rows	 = array(	
            "sales_executive_id",
            "sales_executive_name",
            "user_id",
            "leave_type",
			"reason",
			"latitude",
			"longitude",
			"start_time",
			"start_date",
			"end_date",
			"end_time",
			"entry_flag",
			"leave_category",
		);
		$values	 = array(
			$sales_executive_id,
			$sales_executive_name,
			$user_id,
			$leave_type,
			$reason,
			$latitude,
			$longitude,
			$start_time,
			$start_date,
			$end_date,
			$end_time,
			$entry_flag,
			$leave_category,
		);
				
		$inserted_id = $this->db->rp_insert("leave_request",$values,$rows,0);

		$image_path=array();
		if (isset($file["image_path"]) && $file["image_path"]['size']!=[0]) 
		{
			$ri = $uid;
			$rt = "leave";
			$tc = "leave";
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
			$upadateid = $this->db->rp_update("leave_request",array("file_path"=>$image_path),"id='".$uid."'",0);
		}

		if($inserted_id!=0)
		{  
			// send notification 
			if(isset($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!="" && isset($_SESSION[SITE_SESS.'_ADMIN_TYPE']) && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!="")
			{
				if($_SESSION[SITE_SESS.'REFERANCE_TYPE']!=0)
				{
					$leave_by_name=$this->db->rp_getValue("sales_executive","name","isDelete=0 AND id='". $_SESSION[SITE_SESS.'REFERANCE_ID']."'",0); 
				}
				else{
					$leave_by_name="Admin";
				}
			}
			else
			{
				$leave_by_name=$sales_executive_name;
			}
			$leave_type_name = $this->db->rp_getValue("leave_type","name","id='".$leave_type."'");
		    $notification_description = "New ".$leave_type_name." has been added from ".date('d-m-Y',strtotime($start_date))." to ".date('d-m-Y',strtotime($end_date))." by ".$leave_by_name; 
			  	
			$result_sales=$this->objPushNotification->commonNotification($sales_executive_id,$inserted_id,"leave_request","Leave Added Successfully",$notification_description,"sales_executive","leave_request");
			// send notification
			
			$ack=array( "ack"=>1,"ack_msg"=>"Successfully Inserted Leave Request !!","developer_msg"=>"You got it!!","result"=>$result,"id"=>$uid);
			return $ack;
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"not inserted !!","developer_msg"=>"not inserted!!","result"=>array(),);
			return $ack;
		}
	}
	function getComplainCategory()
	{
		//$required_columns=$this->getRequiredColumns($required_columns);

           $result=$this->db->rp_getData("complain_category","id,name","isDelete=0","",0);
           while($detail=mysqli_fetch_assoc($result))
           {
           		$detail['complain_category']=array();
           		$Category=$this->db->rp_getData("complain_sub_category","id,name,complain_category_id","complain_category_id='".$detail['id']."' AND isDelete=0","",0);
           		if($Category)
           		{
           			$Categories=array();
           			while($C=mysqli_fetch_assoc($Category))
           			{
           				$Categories[]=$C;
           			}
           			$detail['complain_category']=$Categories;
           		}

               $p[]=$detail;
           }
           if($p!=""){
                $reply=array("ack"=>1,"developer_msg"=>"Top Category detail found","ack_msg"=>"Top Category detail found.","result"=>$p);
                }else{
                	 $reply=array("ack"=>0,"developer_msg"=>"Top Category detail found","ack_msg"=>"Top Category detail found.");
              
                }

            return $reply;
               // print_r(result);
	}
	function getComplainSubcategory($cid)
		{
			//$required_columns=$this->getRequiredColumns($required_columns);

	           		$data=array();
	           		$Category=$this->db->rp_getData("complain_sub_category","id,name,complain_category_id","complain_category_id='".$cid."' AND isDelete=0","",0);
	           		if($Category)
	           		{
	           			$Categories=array();
	           			while($C=mysqli_fetch_assoc($Category))
	           			{
	           				$Categories[]=$C;
	           			}
	           			$data=$Categories;
	           		}

	              
	           if($data!=""){
	                $reply=array("ack"=>1,"developer_msg"=>"Sub Category detail found","ack_msg"=>"Sub Category detail found.","result"=>$data);
	                }else{
	                	 $reply=array("ack"=>0,"developer_msg"=>"Sub Category detail found","ack_msg"=>"Sub Category detail found.");
	              
	                }

	            return $reply;
	               // print_r(result);
		}
}

?>