<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("class.log.php");
class NoOrderInquiry extends Functions
{
	public $db;
	public $ctable="no_order_inquiry";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;		
		$this->log=new Log();   
    } 
	public function InsertNoOrderInquiry($detail,$file,$item) 
	{
		extract($detail);

	//	print_r($_REQUEST['mobile_number']);exit();
		
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array("company_name","other_mobile_no","inquiry_date","address","country","state","city","executive_type","distributor_id","image_path","description","datetime","sales_executive_id","source_of_inquiry","designation","email_address","zone","inquiry_created_by","inquiry_assign_to","birth_date","dealer_id","date_of_call","class_id","area_id","inquiry_lead_flag","inquiry_type","gst_no","shipping_address","billing_address","industry_type_id","inq_status","entry_flag","main_city","city_id","type_of_company","top_category_id,contact_person","purchasing_from","pincode","mobile_number","person_name");
		
			$values = array($company_name,$other_mobile_no,date('Y-m-d',strtotime($inquiry_date)),$address,$country,$state,$city,$executive_type,$distributor_id,$detail['image_path'],$description,date('Y-m-d',strtotime($inquiry_date)),$inquiry_created_by,$source_of_inquiry,$designation,$email_address,$zone,$inquiry_created_by,$inquiry_assign_to,date('Y-m-d',strtotime($birth_date)),$dealer_id,date('Y-m-d',strtotime($date_of_call)),$class_id,$area_id,$inquiry_type,$inquiry_type,$gst_no,$shipping_address,$billing_address,$industry_type,$inq_status,$entry_flag,$main_city,$city_id,$type_of_company,$top_category_id,$contact_person,$purchasing_from,$pincode,$mobile_number,$contact_person);
					
			if($_REQUEST['type']=="-1")
			{
				$Prospect_no     = $this->db->getLastInsertId("no_order_inquiry");
				$module_name = "Raw Data";
				$flag = "Web";
				$log_description = $module_name." #INQ/".$Prospect_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
			}
			else if($_REQUEST['type']=="0")
			{
				$inq_no     = $this->db->getLastInsertId("no_order_inquiry");
				$module_name = "Inquiry";
				$flag = "Web";
				$log_description = $module_name." #INQ/".$inq_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
			}
			
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0,$log_description,$flag,$module_name);

		 		$count_value=0;
				foreach ($_REQUEST['phone'] as $key)
				{
					$item_rows = array("customer_id","phone_no","name","ref_table");
					$item_values = array($uid,addslashes(html_entity_decode($key)),$_REQUEST['customer_name'][$count_value],"no_order_inquiry");
					$count_value++;
					$item_id = $this->db->rp_insert("customer_vs_phone_no",$item_values,$item_rows,0);
				}

		 	if($inquiry_assign_to=="" || $inquiry_created_by=="")
		 	{
		 		/*auto assign inquiry*/
		 		$sales_id = $this->db->rp_getValue("sales_executive_map_area","sales_executive_id","class_id='".$class_id."' AND (city_id='".$city_id."' OR area_id='".$area_id."') AND isDelete=0",0);

		 		if($inquiry_assign_to=="")
				{
			 		$inquiry_assign_to =  ($sales_id!="" || $sales_id!=0)?$sales_id:0;
			 	}
				
				if($inquiry_created_by=="")
				{
					if(isset($_SESSION[SITE_SESS.'REFERANCE_TYPE']) && isset($_SESSION[SITE_SESS.'REFERANCE_ID']) && $_SESSION[SITE_SESS.'REFERANCE_TYPE']==2 && $_SESSION[SITE_SESS.'REFERANCE_ID']!=0)
					{
						$inquiry_created_by = $_SESSION[SITE_SESS.'REFERANCE_ID'];
						$sales_executive_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
					}
					else
					{
						$inquiry_created_by = ($sales_id!="" || $sales_id!=0)?$sales_id:0;
						$sales_executive_id = ($sales_id!="" || $sales_id!=0)?$sales_id:0;
					}
				}
				else
				{
					$sales_executive_id = $inquiry_created_by;
				}
 
				$update = $this->db->rp_update("no_order_inquiry",array("inquiry_assign_to"=>$inquiry_assign_to,"sales_executive_id"=>$sales_executive_id),"id='".$uid."'",0);
				/*auto assign inquiry*/
			}
			if($inquiry_assign_to!="" && $inquiry_assign_to!=0)
			{
		 		$inquiry_assign_name = $this->db->rp_getValue("sales_executive","name","id='".$inquiry_assign_to."'",0);

				if($_REQUEST['type']=="-1")
				{	$type="prospect";
					$title="Raw Data Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
					$Prospect_no     = $this->db->getLastInsertId("no_order_inquiry");			
					$body = " #INQ/".$Prospect_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
						$click_action="no_order_inquiry_manage.php?type=-1";
				}
				else if($_REQUEST['type']=="0")
				{
					$type="inquiry";
					$title="Inquiry Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
					$inq_no     = $this->db->getLastInsertId("no_order_inquiry");		
					$body = " #INQ/".$inq_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
						$click_action="no_order_inquiry_manage.php?type=0";
				}
				else if($_REQUEST['type']=="1"){
					$type="lead";
					$title="Lead Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
					$inq_no     = $this->db->getLastInsertId("no_order_inquiry");				
					$body = " #INQ/".$inq_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
					$click_action="no_order_inquiry_manage.php?type=1";

				}
				/*send Notification*/
				$Data = [
			            'title' => $title,
			            'body' =>  $body,
			            'icon' => NOTIFICATIONICON,
			            'image' => NOTIFICATIONIMAGE,
			            'click_action'=> SITEURL.$click_action,
				    	];
			    	$ReferanceArray = 
			    		[
			            'reference_id' => 	$uid,
			            'reference_table' => "no_order_inquiry",
						];

						$msg = array(
							"type"		     => $type,
							"title"		     => $title,
							"description"    => $body,
							"user_id"        => $inquiry_assign_to,
							"reference_id"   => $uid,
							"item_id"        => $uid,
							"reference_type" => 'no_order_inquiry',
						);
			    	$id = $inquiry_assign_to;
			    	if($id!="")
					    {
						    /*panel*/
							    $Upperlevel1 = '1';
						    	$UpperlevelAll = '1';
								$this->db->send_notificationpanel($Data,$id,$ReferanceArray,$Upperlevel1,$UpperlevelAll);
							/*panel*/

							/*application*/
							/*$where="refreshToken!='' AND id='".$inquiry_assign_to."'";
							$refreshTokens[]=$this->db->rp_getValue("sales_executive","refreshToken",$where,0);

							$this->db->send_notificationApplication($msg,$refreshTokens,1);  */
					    }
				/*send Notification*/
			}
		 	if($uid!=0)
			{
				foreach ($item as $p)
				{
					$item_name=addslashes(html_entity_decode($p['pro_name']));
					$item_rows = array("inquiry_id","pro_id","weight_id","pro_name","pro_qty","item_remark");
					$item_values = array($uid,$p['pid'],$p['weight_id'],$item_name,$p['quantity'],$p['remark']);
					$item_id = $this->db->rp_insert("no_order_inquiry_item",$item_values,$item_rows,0);
				}

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
				
				$followup_date = date("Y-m-d",strtotime($detail['first_followup_date']));
				$followup_detail = $detail['followup_detail'];
				$status_t = 0;  
				if($detail['first_followup_date']!="")
				{
					$followup_date = date("Y-m-d H:i:s",strtotime($detail['first_followup_date']));
					$followup_detail = $detail['followup_detail'];
				    $followupArr=array(
				    	"reference_table"=>'no_order_inquiry',
				    	"user_id"=>$inquiry_created_by,
					    "visitor_id"=>$dealer_id,
					    "reference_id"=>$uid,
					    "project_manager_id"=>0,
					    "description"=>$followup_detail,
					    "through"=>1,
					    "followup_date"=>$followup_date,
					    "isDelete"=>0,
					    "isActive"=>1,
					    "next_followup_id"=>0,
					    "refrence_media_id"=>0,
					    "entry_type"=>1,
					    "inquiry_created_by"=>$inquiry_created_by,
				    	"inquiry_assign_to"=>$inquiry_assign_to,
					); 

			        $ContentID=$this->db->rp_insert("followup",array_values($followupArr),array_keys($followupArr),0);
			        $upadateid = $this->db->rp_update($this->ctableNoOrderInquiry,array("status"=>1),"id='".$uid."'",0);
			        $status_t = 1;
				}

				/* Status Time Line Logic Added Dinesh */
				$this->db->addStatusTimelineEntry($uid,$status_t);
				/* Status Time Line Logic Added Dinesh */

				$reply=array("ack"=>1,"developer_msg"=>"Data Add Successfully","ack_msg"=>"Success! Data Add Successfully");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Added Failed.");
				return $reply;
			}
		
	}
	 
	public function UpdateNoOrderInquiry($detail,$file,$item)
 	{
 		extract($detail);

 		// print_r($_REQUEST['mobile_number']);exit;
		$dup_where = "mobile_number = '".$mobile_number."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
		//$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
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
					"other_mobile_no" =>$other_mobile_no,
					"inquiry_date" =>date('Y-m-d',strtotime($inquiry_date)),
					"address" => $address,
					"country" => $country,
					"state" => $state,
					"city" => $city,
					"executive_type" => $executive_type,
					"distributor_id" =>$distributor_id,
					"description" =>$description,
					"datetime" =>date('Y-m-d',strtotime($inquiry_date)),
					"sales_executive_id" =>$inquiry_created_by,
					"source_of_inquiry" =>$source_of_inquiry,
					"designation" => $designation,
					"email_address" => $email_address,
					"zone" => $zone,
					"inquiry_created_by" =>$inquiry_created_by,
					"inquiry_assign_to" =>$inquiry_assign_to,
					"contact_person" =>$contact_person,
					"birth_date" =>date('Y-m-d',strtotime($birth_date)),
					"dealer_id" => $dealer_id,
					"image_path" => $image_path,
					"date_of_call" =>date('Y-m-d',strtotime($date_of_call)),
					"class_id" => $class_id,
					"area_id" => $area_id,
					"city_id" => $city_id,
					"gst_no" => $gst_no,
					"shipping_address" => $shipping_address,
					"billing_address" => $billing_address,
					"industry_type_id" => $industry_type,
					"update_entry_flag"=>$update_entry_flag,
					"main_city"=>$main_city,
					"type_of_company"=>$type_of_company,
					"top_category_id"=>$top_category_id,
					"purchasing_from"=>$purchasing_from,
					"pincode"=>$pincode,
				);

				$where	= "id='".$_REQUEST['id']."'";
				if($_REQUEST['type']=="-1")
				{
					$module_name = "Raw Data";
					$flag = "Web";
					$log_description = $module_name." #INQ/".$_REQUEST['id']." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
				}
				else if($_REQUEST['type']=="0")
				{
					$module_name = "Inquiry";
					$flag = "Web";
					$log_description = $module_name." #INQ/".$_REQUEST['id']." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
				}
				else
				{
					$module_name = "Lead";
					$flag = "Web";
					$log_description = $module_name." #INQ/".$_REQUEST['id']." Edited By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
				}
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");

				$customer_id=$_REQUEST['id'];

				if(!empty($_REQUEST['phone']) || !empty($_REQUEST['customer_name']))
				{
					$count_value=0;
					$this->db->rp_delete("customer_vs_phone_no","customer_id='".$customer_id."'");
					foreach ($_REQUEST['phone'] as $key)
					{ 
						$item_rows_m = array("customer_id","phone_no","name","ref_table");
						// $item_values = array($customer_id,$key);
						$item_values_m = array($customer_id,addslashes(html_entity_decode($key)),$_REQUEST['customer_name'][$count_value],"no_order_inquiry");
						$count_value++;
						$item_id = $this->db->rp_insert("customer_vs_phone_no",$item_values_m,$item_rows_m,0);
					}

					// exit;
				}

				
				if($inquiry_assign_to!="" && $inquiry_assign_to!=0)
				{
					 $inquiry_assign_name = $this->db->rp_getValue("sales_executive","name","id='".$inquiry_assign_to."'",0);

					if($_REQUEST['type']=="-1")
					{	$type="prospect";
						$title="Raw Data Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
						$Prospect_no     = $this->db->getLastInsertId("no_order_inquiry");			
						$body = " #INQ/".$Prospect_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
							$click_action="no_order_inquiry_manage.php?type=-1";
					}
					else if($_REQUEST['type']=="0")
					{
						$type="inquiry";
						$title="Inquiry Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
						$inq_no     = $this->db->getLastInsertId("no_order_inquiry");		
						$body = " #INQ/".$inq_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
							$click_action="no_order_inquiry_manage.php?type=0";
					}
					else if($_REQUEST['type']=="1"){
						$type="lead";
						$title="Lead Assigned to ".$inquiry_assign_name." By ".$_SESSION[SITE_SESS.'SESS_NAME'];
						$inq_no     = $this->db->getLastInsertId("no_order_inquiry");				
						$body = " #INQ/".$inq_no." Created By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
						$click_action="no_order_inquiry_manage.php?type=1";

					}
					/*send Notification*/
					$Data = [
				            'title' => $title,
				            'body' =>  $body,
				            'icon' => NOTIFICATIONICON,
				            'image' => NOTIFICATIONIMAGE,
				            'click_action'=> SITEURL.$click_action,
					    	];
				    	$ReferanceArray = 
				    		[
				            'reference_id' => 	$_REQUEST['id'],
				            'reference_table' => "no_order_inquiry",
							];

							$msg = array(
								"type"		     => $type,
								"title"		     => $title,
								"description"    => $body,
								"user_id"        => $inquiry_assign_to,
								"reference_id"   => $_REQUEST['id'],
								"item_id"        => $_REQUEST['id'],
								"reference_type" => 'no_order_inquiry',
							);
				    	$id = $inquiry_assign_to;
				    	if($id!="")
						    {
							    /*panel*/
								    $Upperlevel1 = '1';
					    			$UpperlevelAll = '1';
									$this->db->send_notificationpanel($Data,$id,$ReferanceArray,$Upperlevel1,$UpperlevelAll);
								/*panel*/
								
								/*application*/
								/*$where="refreshToken!='' AND id='".$inquiry_assign_to."'";
								$refreshTokens[]=$this->db->rp_getValue("sales_executive","refreshToken",$where,0);
								$this->db->send_notificationApplication($msg,$refreshTokens,1);  */
								/*application*/
						    }
					/*send Notification*/
				}
				$inquiry_id = $_REQUEST['id'];
				if(!empty($item))
				{
					$this->db->rp_delete("no_order_inquiry_item","inquiry_id='".$inquiry_id."'");
					foreach ($item as $p)
					{
						$item_name=addslashes(html_entity_decode($p['pro_name']));
						$item_rows = array("inquiry_id","pro_id","weight_id","pro_name","pro_qty","item_remark");
						$item_values = array($_REQUEST['id'],$p['pid'],$p['weight_id'],$item_name,$p['quantity'],$p['remark']);
						$item_id = $this->db->rp_insert("no_order_inquiry_item",$item_values,$item_rows,0);
					}
				}

				/*image_upload*/
					$image_path=array();
					if (isset($file["image_path"]) && $file["image_path"]['size']!=[0]) 
					{
					// print_r($file);exit;
						$ri = $_REQUEST['id'];
						$rt = "inquiry";
						$tc = "inquiry";
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
						// echo"aa";exit;
					}
					else
					{
						$image_path=$detail['old_image_path'];
						$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$_REQUEST['id']."'",0);
						// echo"22";exit;
  						unset($detail['old_image_path']);
					}
				/*image_upload*/
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Data Update Successfull!!.","ack_msg"=>"Success! Data Update Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Data Update Failed.");
					return $reply;
				}
			}
			
	}	
	public function GetEditDataNoOrderInquiry($detail)
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
		$result['mobile_number']			= htmlentities($ctable_d['mobile_number']);
		$result['other_mobile_no']		= htmlentities($ctable_d['other_mobile_no']);
		$result['country']				= htmlentities($ctable_d['country']);
		$result['state']				= htmlentities($ctable_d['state']);
		$result['city']					= htmlentities($ctable_d['city']);
		$result['main_city']					= htmlentities($ctable_d['main_city']);
		$result['description']			= htmlentities($ctable_d['description']);
		$result['inquiry_date']			= ($ctable_d['inquiry_date']!="0000-00-00" && $ctable_d['inquiry_date']!="1970-01-01")?date('d-m-Y',strtotime($ctable_d['inquiry_date'])):"";
		$result['address']				= htmlentities($ctable_d['address']);
		$result['source_of_inquiry']	= htmlentities($ctable_d['source_of_inquiry']);
		$result['designation']			= htmlentities($ctable_d['designation']);
		$result['email_address']			= htmlentities($ctable_d['email_address']);
		$result['zone']					= htmlentities($ctable_d['zone']);
		$result['inquiry_created_by']	= htmlentities($ctable_d['inquiry_created_by']);
		$result['inquiry_assign_to']	= htmlentities($ctable_d['inquiry_assign_to']);
		$result['product_id']			= htmlentities($ctable_d['product_id']);
		$result['quantity']				= htmlentities($ctable_d['quantity']);
		$result['u_w_flag']				= htmlentities($ctable_d['u_w_flag']);
		$result['u_w_remark']			= htmlentities($ctable_d['u_w_remark']);
		$result['quotation_flag']		= htmlentities($ctable_d['quotation_flag']);
		$result['quotation_remark']		= htmlentities($ctable_d['quotation_remark']);
		$result['customer_requirement']	= htmlentities($ctable_d['customer_requirement']);
		$result['birth_date']	        = htmlentities(date('d-m-Y',strtotime($ctable_d['birth_date'])));
		$result['dealer_id']	        = htmlentities($ctable_d['dealer_id']);
		$result['image_path']	        = htmlentities($ctable_d['image_path']);
		$result['date_of_call']	        = ($ctable_d['date_of_call']!="0000-00-00"  && $ctable_d['date_of_call']!="1970-01-01")?date('d-m-Y',strtotime($ctable_d['date_of_call'])):"";
		$result['gst_no']	        	= $ctable_d['gst_no'];
		$result['shipping_address']	    = $ctable_d['shipping_address'];
		$result['billing_address']	    = $ctable_d['billing_address'];
		$result['industry_type']	    = $ctable_d['industry_type_id'];
		$result['area_id']	    = $ctable_d['area_id'];
		$result['type_of_company']	    = $ctable_d['type_of_company'];
		$result['top_category_id']	    = $ctable_d['top_category_id'];
		$result['purchasing_from']	    = $ctable_d['purchasing_from'];
		$result['pincode']	    = $ctable_d['pincode'];
		
		$reply=array("ack"=>1,"developer_msg"=>"Data fetched Successfully!!.","ack_msg"=>"Success! Data fetched Successfully","result"=>$result);
		return $reply;
	
	}

	public function GetInquiryItems($detail)
	{		

		$where = "inquiry_id='".$detail['id']."' AND isDelete=0";
		$ctable_item = $this->db->rp_getData("no_order_inquiry_item","*",$where,"",0);
		if($ctable_item)
		{
		while(	$ctable_item_d = mysqli_fetch_array($ctable_item))
		{
			$result_item=array();
			$result_item['product_id']				= htmlentities($ctable_item_d['pro_id']);
			$result_item['inquiry_id']				= htmlentities($ctable_item_d['inquiry_id']);
			$result_item['weight_id']				= htmlentities($ctable_item_d['weight_id']);	
			$result_item['product_name']            = htmlentities($ctable_item_d['pro_name']);
			$result_item['quantity']		        = htmlentities($ctable_item_d['pro_qty']);
			$result_item['item_remark']		        = htmlentities($ctable_item_d['item_remark']);
			$result_item['cat_no']		    		= $this->db->rp_getValue("product_weight_price","catno","product_id='".$ctable_item_d['pro_id']."'");
			$result[]=$result_item;
		}
		$reply=array("ack"=>1,"developer_msg"=>"Product Item detail fetched!!.","ack_msg"=>"Success! Update Product Item Successfully.","result"=>$result);
		return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Update not fetched!!.","ack_msg"=>"Success! Update Failed"	);
			return $reply;
		}
	
	}
	
	public function DeleteNoOrderInquiry($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			if($_REQUEST['type']=="-1")
			{
				$module_name = "Raw Data";
				$flag = "Web";
				$log_description = $module_name." #INQ/".$_REQUEST['id']." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
			}
			else if($_REQUEST['type']=="0")
			{
				$module_name = "Inquiry";
				$flag = "Web";
				$log_description = $module_name." #INQ/".$_REQUEST['id']." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
			}
			else
			{
				$module_name = "Lead";
				$flag = "Web";
				$log_description = $module_name." #INQ/".$_REQUEST['id']." Deleted By ".$_SESSION[SITE_SESS.'SESS_NAME']." ON ".date("Y-m-d H:i:s");
			}
			$uid=$this->db->rp_update($this->ctable,$rows,$where,0,$log_description,$flag,$module_name,"","");
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

	public function CancelNoOrderInquiry($detail)
	{
		$rows 	= array(
		"status"	=> "-2"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Inquiry Cancelled Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Inquiry Cancelled Failed.");
				return $reply;
			}
	}

	public function followupNoOrderInquiry($detail)
	{
		$rows 	= array(
		"status"	=> "1"
		);
		$where	= "id='".$_REQUEST['id']."'";
		$uid=$this->db->rp_update($this->ctable,$rows,$where);
		if($uid!=0)
		{
			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Inquiry Transferred To Followup Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Inquiry Not Transferred To Followup.");
			return $reply;
		}
	}

	function uploadInquiry($file)
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
						$storagename = "sheet_import/uploads/no_orders_inquiry_import".date('dmYhis').".xlsx";
						$upload_info=array("updated"=>0,"duplicate"=>array());
						move_uploaded_file($file["tmp_name"],$storagename);		
						include "../".ADMINFOLDER.'/PHPExcel/IOFactory.php';
						$inputFileName = $storagename; 
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
							/*for($i=1;$i<=$arrayCount;$i++)
							{
								if($i==1)
								{		
									// echo $allDataInSheet[$i]["A"];exit;								
									if(($allDataInSheet[$i]["A"])!="Status" || ($allDataInSheet[$i]["B"])!="Source Of Inquiry" || ($allDataInSheet[$i]["C"])!="Customer Type" || ($allDataInSheet[$i]["D"])!="Company Name" || ($allDataInSheet[$i]["E"])!="Dealer" || ($allDataInSheet[$i]["F"])!="Person Name"  || ($allDataInSheet[$i]["G"])!="Email"  || ($allDataInSheet[$i]["H"])!="Phone"  || ($allDataInSheet[$i]["I"])!="Country" || ($allDataInSheet[$i]["J"])!="State" || ($allDataInSheet[$i]["K"])!="City" || ($allDataInSheet[$i]["L"])!="Description" || ($allDataInSheet[$i]["M"])!="Inquiry Date" || ($allDataInSheet[$i]["N"])!="Inquiry Taken By" || ($allDataInSheet[$i]["O"])!="Inquiry Assigned to" || ($allDataInSheet[$i]["O"])!="Inquiry Assigned to" || ($allDataInSheet[$i]["P"])!="Address")
									{										
										$ack=array("ack"=>0,"ack_msg"=>"Wrong Excel Format Column Mismatch Try Again!!");
										return $ack;
									}
										
								}
								else
								{
									// $i=$i+1;
									$data = array();
									$status            = $this->db->clean($allDataInSheet[$i]["A"]);
									$source_of_inquiry = $this->db->clean($allDataInSheet[$i]["B"]);
									$executive_type	   = $this->db->clean($allDataInSheet[$i]["C"]);
									$company_name 	   = $this->db->clean($allDataInSheet[$i]["D"]);	
									$dealer 	       = $this->db->clean($allDataInSheet[$i]["E"]);	
									$person_name       = $this->db->clean($allDataInSheet[$i]["F"]);
									$email_address     = $this->db->clean($allDataInSheet[$i]["G"]);
									$mobile_number     = $this->db->clean($allDataInSheet[$i]["H"]);
									$country           = $this->db->clean($allDataInSheet[$i]["I"]);
									$state             = $this->db->clean($allDataInSheet[$i]["J"]);
									$city              = $this->db->clean($allDataInSheet[$i]["K"]);
									$description       = $this->db->clean($allDataInSheet[$i]["L"]);
									$inquiry_date      = $this->db->clean($allDataInSheet[$i]["M"]);
									$inquiry_created_by = $this->db->clean($allDataInSheet[$i]["N"]);
									$inquiry_assign_to  = $this->db->clean($allDataInSheet[$i]["O"]);
									$address  = $this->db->clean($allDataInSheet[$i]["P"]);
									
									$data  = $allDataInSheet['2'];// first row of sheet for dealer & outlet
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
											if($this->db->rp_getTotalRecord("no_order_inquiry","mobile_number='".$mobile_number."' AND isDelete=0",0)<=0)
											{

												$executive_type_new = $this->db->rp_getValue("customer_type","id","LOWER(name)='".strtolower($executive_type)."'",0);

												$dealer_id = $this->db->rp_getValue("executive","id","LOWER(company_name)='".strtolower($dealer)."'",0);

												$source_of_inquiry_new = $this->db->rp_getValue("source_of_inquiry","id","LOWER(name)='".strtolower($source_of_inquiry)."'",0);

												$sales_executive_id_created_by = $this->db->rp_getValue("sales_executive","id","LOWER(name)='".strtolower($inquiry_created_by)."'");

												$inquiry_assign_to_new = $this->db->rp_getValue("sales_executive","id","LOWER(name)='".strtolower($inquiry_assign_to)."'");

												$inquiry_date1 = date('Y-m-d',strtotime($inquiry_date));
												

												$this->db->rp_insert("no_order_inquiry",array($executive_type_new,$dealer_id,$company_name,$person_name,$mobile_number,$email_address,$country,$state,$city,$description,$inquiry_date1,$inquiry_date1,$source_of_inquiry_new,$sales_executive_id_created_by,$inquiry_assign_to_new,$sales_executive_id_created_by,0,$address),array("executive_type","dealer_id","company_name","person_name","mobile_number","email_address","country","state","city","description","inquiry_date","datetime","source_of_inquiry","inquiry_created_by","inquiry_assign_to","sales_executive_id","status","address"),0);
											}
										}
										
									}
									$upload_info['updated']=$upload_info['updated']+1;
									
								}
								
							}*/
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