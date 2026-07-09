<?php
require_once("main.class.php");
require_once("function.class.php");
class LeaveRequest extends Functions
{
	public $db;
	public $ctable="sales_executive_information";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		   
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
	 
	 public function UpdateInfo($detail,$file)
	  {
	      	
			// echo "<pre>";
		// print_r($_FILES);
		// echo "<hr>";
		// print_r($detail);die;
			extract($detail);
			$rows = array(
				"post_applied" 				=> $post_applied,
				"reference" 				=> $reference,
				"first_name" 				=> $first_name,
				"middle_name" 				=> $middle_name,
				"surname" 					=> $surname,
				"gender" 					=> $gender, 
				"religion" 					=> $religion,
				"cast" 						=> $cast,
				"mother_tongue" 			=> $mother_tongue,
				"marital_status" 			=> $marital_status,
				"plaece_of_birth" 			=> $plaece_of_birth,
				"present_address" 			=> $present_address,
				"permanent_address" 		=> $permanent_address,
				"contact_no" 				=> $contact_no,
				"emergency_contact_person" 	=> $emergency_contact_person,
				"contact_person_relation" 	=> $contact_person_relation,
				"blood_group" 				=> $blood_group,
				"email" 					=> $email,
				"type_of_vehicle" 			=> $type_of_vehicle,
				"vehicle_model_no" 			=> $vehicle_model_no,
				"physical_disability" 		=> $physical_disability,
				"major_illness" 			=> $major_illness,
				"rp1_name" 					=> $rp1_name,
				"rp1_relation" 				=> $rp1_relation,
				"rp1_occupation" 			=> $rp1_occupation,
				"rp1_contact_no" 			=> $rp1_contact_no,
				"rp2_name" 					=> $rp2_name,
				"rp2_relation" 				=> $rp2_relation,
				"rp2_occupation" 			=> $rp2_occupation,
				"rp2_contact_no" 			=> $rp2_contact_no,
				"date" 						=> $date,
				"birth_date" 				=> $birth_date
			);
			// print_r($rows);die;
			$uid = $this->db->rp_update("sales_executive_information",$rows,"id = ".$id,0);
			if($uid!=0)
			{
				if(sizeof($file['image_path']['name']) > 0 && $file['image_path']['size'][0] != 0){
					// print_r($file);die;
					$ri = $id;
					$rt = "sales_executive_information";
					$tc = "image_path";
					$rc = "id";
					// $file = $file['image_path'];
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
							$attachment=EMPLOYEE_IMAGE;
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
					// print_r($image_path);die;
					$image_path = implode(",", $image_path);
					$upadateid = $this->db->rp_update("sales_executive_information",array("image_path"=>$image_path),"id='".$id."'",0);
				}
				else{
					$upadateid = 1;
				}
				if ($uid!=0) {
					$reply=array("ack"=>1,"developer_msg"=>"Data Update Successfull!!.","ack_msg"=>"Success! Data Update Successfully.","id"=>$uid);
				}else{
					$reply=array("ack"=>0,"developer_msg"=>"Error!!","ack_msg"=>"Error");
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Update Failed.");
			}
			return $reply;			
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
		$result['start_time']		        = htmlentities($ctable_d['start_time']);
		$result['start_date']		        = date("d-m-Y",strtotime($ctable_d['start_date']));
		$result['end_time']		            = htmlentities($ctable_d['end_time']);
		$result['end_date']		            = date("d-m-Y",strtotime($ctable_d['end_date']));
		$result['image_path']		        = $ctable_d['file_path'];
		
		//$imgpath = array();
		for ($i=0; $i < sizeof($image_path); $i++)
		{ 
			$imgpath = SITEURL."resource/image/".$this->db->rp_getValue("media","url","reference_id='".$detail['id']."' AND id='".$image_path[$i]."'",0);
		}
		$result['image_path'] = $imgpath;
		
		$reply=array("ack"=>1,"developer_msg"=>"Leave Type detail fetched!!.","ack_msg"=>"Success! Leave Type Edit Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteInfo($detail)
	{
	    $rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Leave Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Leave Failed.");
				return $reply;
			}
	}
	
	public function InsertInfo($detail,$file) 
	{
		// echo "<pre>";
		// print_r($file);
		// echo "<hr>";
		// print_r($detail);die();
		extract($detail);
		$keys = array(
			"post_applied",
			"reference",
			"first_name",
			"middle_name",
			"surname",
			"gender",
			"religion",
			"cast",
			"mother_tongue",
			"marital_status",
			"plaece_of_birth",
			"present_address",
			"permanent_address",
			"contact_no",
			"emergency_contact_person",
			"contact_person_relation",
			"blood_group",
			"email",
			"type_of_vehicle",
			"vehicle_model_no",
			"physical_disability",
			"major_illness",
			"rp1_name",
			"rp1_relation",
			"rp1_occupation",
			"rp1_contact_no",
			"rp2_name",
			"rp2_relation",
			"rp2_occupation",
			"rp2_contact_no",
			"date",
			"birth_date",
		);
		$values = array(
			$post_applied,
			$reference,
			$first_name,
			$middle_name,
			$surname,
			$gender,
			$religion,
			$cast,
			$mother_tongue,
			$marital_status,
			$plaece_of_birth,
			$present_address,
			$permanent_address,
			$contact_no,
			$emergency_contact_person,
			$contact_person_relation,
			$blood_group,
			$email,
			$type_of_vehicle,
			$vehicle_model_no,
			$physical_disability,
			$major_illness,
			$rp1_name,
			$rp1_relation,
			$rp1_occupation,
			$rp1_contact_no,
			$rp2_name,
			$rp2_relation,
			$rp2_occupation,
			$rp2_contact_no,
			$date,
			$birth_date,
		);
		$add_data = $this->db->rp_insert("sales_executive_information",$values,$keys,0);
		// print_r($add_data);
		if($add_data && $add_data > 0){
			// echo "hi";die();
			if(sizeof($file['image_path']['name']) > 0){
				$ri = $add_data;
				$rt = "sales_executive_information";
				$tc = "image_path";
				$rc = "id";
				// $file = $file['image_path'];
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
						$attachment=EMPLOYEE_IMAGE;
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
				// print_r($image_path);die;
				$image_path = implode(",", $image_path);
				$upadateid = $this->db->rp_update("sales_executive_information",array("image_path"=>$image_path),"id='".$add_data."'",0);
				if($upadateid){
					$ack=array("ack"=>1,"ack_msg"=>"Data Add Successfully!!!","dmg"=>"Data Add Successfully!!!","data_id"=>$add_data);
				}else{
					$ack=array("ack"=>0,"ack_msg"=>"Error!!!");
				}
			}
		}else{
			$ack=array("ack"=>0,"ack_msg"=>"Error!!!");
		}
		return $ack;
	}
	function getComplainCategory()
	{
		//$required_columns=$this->getRequiredColumns($required_columns);

           $result=$this->rp_getData("complain_category","id,name","isDelete=0","",0);
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

}

?>