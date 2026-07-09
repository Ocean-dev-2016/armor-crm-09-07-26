<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("notification.class.php");
require_once("class.system.php");

require_once("push_notification.class.php");
class FreezerScheme extends Functions
{
	public $db;
	public $ctable="freezer_scheme";
	//public $sales_type_title=array("sales_manager"=>"Sales Manager","area_sales_manager"=>"Area Sales Manager","sales_officer"=>"Area Sales Manager","sales_executive"=>"Sales Officer");
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;		   
		$this->notification=new Notification();		   
		$this->system=new System();		   
        $this->objPushNotification=new PushNotification();
    } 

//-----------------------------------------------------------------------------------------------//
	
	public function AddFreezeScheme($detail,$file)
	{	
		// print_r($detail);exit();
		extract($detail);
	
		$value=$this->db->getlastInsertId($this->ctable);
		//$serial_no=SERIAL_NO.str_pad($value, 3, '0', STR_PAD_LEFT);
		$serial_no=str_pad($value, 3, '0', STR_PAD_LEFT);
		
	
	if (isset($_FILES["agency_permises_photo"]))
    {
    	 // print_r($_FILES);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["agency_permises_photo"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$fileName 	= $_FILES["agency_permises_photo"]["name"];	
    	// echo $fileName;exit();
    	if($fileName!="")
    	{
    	// print_r($_FILES["agency_permises_photo"]["name"]);exit();
    		$fileSize 	= round($_FILES["agency_permises_photo"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $fileName));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$agency_permises_photo	= 'agency_permises_photo_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= AGENCY_PERMISES_PHOTO_A.$agency_permises_photo;
	    	$_FILES['agency_permises_photo']['tmp_name'];
	    	move_uploaded_file($_FILES['agency_permises_photo']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	else
    	{
    		$agency_permises_photo="";
    	}
    }

    else
    {
    	$new_image=false;
    	$agency_permises_photo="";
    }

    if (isset($_FILES["dealer_image"]))
    {
    	// print_r($fileName);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["dealer_image"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$dealer_image 	= $_FILES["dealer_image"]["name"];	
    	// echo $_FILES["dealer_image"]["name"];exit();
    	// echo $fileName;exit();
    	if($dealer_image!="")
    	{
    		// echo $dealer_image;exit();
    	// print_r($_FILES["dealer_image"]["name"]);exit();
    		$fileSize 	= round($_FILES["dealer_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $dealer_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$dealer_image	= 'dealer_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= DEALER_PHOTO_A.$dealer_image;
	    	$_FILES['dealer_image']['tmp_name'];
	    	move_uploaded_file($_FILES['dealer_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	else
    	{
    		$dealer_image="";
    	}
    }

    else
    {
    	$new_image=false;
    	$dealer_image="";
    } 

    if (isset($_FILES["distributor_image"]))
    {
    	// print_r($fileName);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["distributor_image"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$distributor_image 	= $_FILES["distributor_image"]["name"];	
    	// echo $_FILES["distributor_image"]["name"];exit();
    	// echo $fileName;exit();
    	if($distributor_image!="")
    	{
    		// echo $distributor_image;exit();
    	// print_r($_FILES["distributor_image"]["name"]);exit();
    		$fileSize 	= round($_FILES["distributor_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $distributor_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$distributor_image	= 'distributor_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= DISTRIBUTOR_PHOTO_A.$distributor_image;
	    	$_FILES['distributor_image']['tmp_name'];
	    	move_uploaded_file($_FILES['distributor_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	else
    	{
    		$distributor_image="";
    	}
    }

    else
    {
    	$new_image=false;
    	$distributor_image="";
    }
    if (isset($_FILES["company_office_image"]))
    {
    	// print_r($fileName);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["company_office_image"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$company_office_image 	= $_FILES["company_office_image"]["name"];	
    	// echo $_FILES["company_office_image"]["name"];exit();
    	// echo $fileName;exit();
    	if($company_office_image!="")
    	{
    		// echo $company_office_image;exit();
    	// print_r($_FILES["company_office_image"]["name"]);exit();
    		$fileSize 	= round($_FILES["company_office_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $company_office_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$company_office_image	= 'company_office_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= COMPANY_OFFICE_PHOTO_A.$company_office_image;
	    	$_FILES['company_office_image']['tmp_name'];
	    	move_uploaded_file($_FILES['company_office_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	else
    	{
    		$company_office_image="";
    	}
    }

    else
    {
    	$new_image=false;
    	$company_office_image="";
    }

     if (isset($_FILES["dealer_sign_image"]))
    {
    	// print_r($fileName);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["dealer_sign_image"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$dealer_sign_image 	= $_FILES["dealer_sign_image"]["name"];	
    	// echo $_FILES["dealer_sign_image"]["name"];exit();
    	// echo $fileName;exit();
    	if($dealer_sign_image!="")
    	{
    		// echo $dealer_sign_image;exit();
    	// print_r($_FILES["dealer_sign_image"]["name"]);exit();
    		$fileSize 	= round($_FILES["dealer_sign_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $dealer_sign_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$dealer_sign_image	= 'dealer_sign_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= DEALER_PHOTO_SIGN_A.$dealer_sign_image;
	    	$_FILES['dealer_sign_image']['tmp_name'];
	    	move_uploaded_file($_FILES['dealer_sign_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	else
    	{
    		$dealer_sign_image="";
    	}
    }

    else
    {
    	$new_image=false;
    	$dealer_sign_image="";
    }

     if (isset($_FILES["distributor_sign_image"]))
    {
    	// print_r($fileName);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["distributor_sign_image"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$distributor_sign_image 	= $_FILES["distributor_sign_image"]["name"];	
    	// echo $_FILES["distributor_sign_image"]["name"];exit();
    	// echo $fileName;exit();
    	if($distributor_sign_image!="")
    	{
    		// echo $distributor_sign_image;exit();
    	// print_r($_FILES["distributor_sign_image"]["name"]);exit();
    		$fileSize 	= round($_FILES["distributor_sign_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $distributor_sign_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$distributor_sign_image	= 'distributor_sign_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= DISTRIBUTOR_PHOTO_SIGN_A.$distributor_sign_image;
	    	$_FILES['distributor_sign_image']['tmp_name'];
	    	move_uploaded_file($_FILES['distributor_sign_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	else
    	{
    		$distributor_sign_image="";
    	}
    }

    else
    {
    	$new_image=false;
    	$distributor_sign_image="";
    }


   if (isset($_FILES["company_office_sign_image"]))
    {
    	// print_r($fileName);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["company_office_sign_image"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$company_office_sign_image 	= $_FILES["company_office_sign_image"]["name"];	
  
    	if($company_office_sign_image!="")
    	{
    	
    		$fileSize 	= round($_FILES["company_office_sign_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $company_office_sign_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$company_office_sign_image	= 'company_office_sign_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= COMPANY_OFFICE_PHOTO_SIGN_A.$company_office_sign_image;
	    	$_FILES['company_office_sign_image']['tmp_name'];
	    	move_uploaded_file($_FILES['company_office_sign_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	else
    	{
    		$company_office_sign_image="";
    	}
    }

    else
    {
    	$new_image=false;
    	$company_office_sign_image="";
    }

        $rows_insert 	= array("customer_id","serial_no","shop_name","contact_person","mobile_no","address","taluka","district","state","distributor_agency","center","freeze_model_no","hard_top","class_top","agency_permises_photo","dealer_image","distributor_image","company_office_image","dealer_name","dealer_mo","dealer_sign","distributor_name","distributor_mob","distributor_sign","company_office_name","company_office_mob","company_office_sign","executive_type","dealer_sign_image","distributor_sign_image","company_office_sign_image","payment","language","isActive","sales_id","entry_flag","utr");

		$values_insert = array($customer_id,$serial_no,$shop_name,$contact_person,$mobile_no,$address,$taluka,$district,$state,$distributor_agency,$center,$freeze_model_no,$hard_top,$class_top,$agency_permises_photo,$dealer_image,$distributor_image,$company_office_image,$dealer_name,$dealer_mo,$dealer_sign,$distributor_name,$distributor_mob,$distributor_sign,$company_office_name,$company_office_mob,$company_office_sign,$executive_type,$dealer_sign_image,$distributor_sign_image,$company_office_sign_image,$payment,$language,1,$sales_id,$entry_flag,$utr);
		$insert = $this->db->rp_insert($this->ctable,$values_insert,$rows_insert,0);

		  /* Multiple Image code*/
			$image_path=array();
		    if (isset($file["image_path"]) && $file["image_path"]['size']!=[0])
			{
               // echo "string";exit();
				$ri = $insert;
				$rt = "freezer_scheme";
				$tc = "freezer_scheme";
				$rc = "id";
				for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
				{
					$file_name = $file['image_path']['name'][$i];
					$file_size = $file['image_path']['size'][$i];
					$file_tmp = $file['image_path']['tmp_name'][$i];
					$file_type = $file['image_path']['type'][$i];
					$extension=explode(".",$file_name);

					$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG","pdf");
					$extension=$extension[sizeof($extension)-1];
					if(!in_array($extension,$allowed_extentions))
					{
						$file_error=true;
					}
					$orignal_file_name=$extension[0];
					if(in_array($extension,$allowed_extentions))
					{
						// echo $extension;exit();
						$attachment="../images/document_list/";
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
			/* Multiple Image code*/

        if($customer_id!=0){   
            // for customer notification added by shivani
            $notification_description = "Your deep Freeze of serial no ".$serial_no." has been added Successfully";
                 
            $result_sales=$this->objPushNotification->commonNotification($customer_id,$insert,$this->ctable,"Deep Freeze Added Successfully",$notification_description,"customer","freezer_scheme");
            // for customer notification added by shivani

           $reply=array("ack"=>1,"developer_msg"=>"
            Data Add Successfully!!","ack_msg"=>"Sheme Data Add Successfully!!","id"=>$insert);
    		return $reply;
        }
	    else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Complain Not Add");
			return $reply;
		}
	}
    public function AddFreezeSchemeImagesOtherData($detail,$file,$file1)
    {   
        // print_r($detail);exit();
        extract($detail);
        if($type=="1"){
         //   echo "string";exit;
            if (isset($_FILES["agency_permises_photo"]))
            {
                //  print_r($_FILES);exit();
                $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
                $temp = explode(".", $_FILES["agency_permises_photo"]["name"]);
                $extension = end($temp);
                // echo"hello123";exit();
                $fileName   = $_FILES["agency_permises_photo"]["name"]; 
                // echo $fileName;exit();
                if($fileName!="")
                {
                // print_r($_FILES["agency_permises_photo"]["name"]);exit();
                    $fileSize   = round($_FILES["agency_permises_photo"]["size"]); // BYTES
                    $adate      = date('Y-m-d H:i:m');
                    $extension  = end(explode(".", $fileName));
                    if(!in_array($extension,$allowedExts))
                    {
                        $file_error=true;
                    }
                    $agency_permises_photo  = 'agency_permises_photo_'.substr(sha1(time()), 0, 6).".".$extension;
                    $filePath   = AGENCY_PERMISES_PHOTO_A.$agency_permises_photo;
                    $_FILES['agency_permises_photo']['tmp_name'];
                    move_uploaded_file($_FILES['agency_permises_photo']['tmp_name'], $filePath);
                    $new_image=true;
                }
                else
                {
                    $agency_permises_photo="";
                }
            }

            else
            {
                $new_image=false;
                $agency_permises_photo="";
            }
            $upadateid = $this->db->rp_update($this->ctable,array("agency_permises_photo"=>$agency_permises_photo),"id='".$id."'",0);
             $reply=array("ack"=>1,"developer_msg"=>"
                Data Add Successfully!!","ack_msg"=>"Sheme Data Add Successfully!!","id"=>$upadateid);
            return $reply;
       }else if($type=="2"){

       // echo "hello";exit();

            if (isset($_FILES["dealer_sign_image"]))
            {
              //   print_r($fileName);exit();
                $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
                $temp = explode(".", $_FILES["dealer_sign_image"]["name"]);
                $extension = end($temp);
                // echo"hello123";exit();
                $dealer_sign_image  = $_FILES["dealer_sign_image"]["name"]; 
                // echo $_FILES["dealer_sign_image"]["name"];exit();
                // echo $fileName;exit();
                if($dealer_sign_image!="")
                {
                    // echo $dealer_sign_image;exit();
                // print_r($_FILES["dealer_sign_image"]["name"]);exit();
                    $fileSize   = round($_FILES["dealer_sign_image"]["size"]); // BYTES
                    $adate      = date('Y-m-d H:i:m');
                    $extension  = end(explode(".", $dealer_sign_image));
                    if(!in_array($extension,$allowedExts))
                    {
                        $file_error=true;
                    }
                    $dealer_sign_image  = 'dealer_sign_image_'.substr(sha1(time()), 0, 6).".".$extension;
                    $filePath   = DEALER_PHOTO_SIGN_A.$dealer_sign_image;
                    $_FILES['dealer_sign_image']['tmp_name'];
                    move_uploaded_file($_FILES['dealer_sign_image']['tmp_name'], $filePath);
                    $new_image=true;
                }
                else
                {
                    $dealer_sign_image="";
                }
            }
            else
            {
                $new_image=false;
                $dealer_sign_image="";
            }
                 
            if (isset($_FILES["dealer_image"]))
            {
                $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
                $temp = explode(".", $_FILES["dealer_image"]["name"]);
                $extension = end($temp);
                // echo"hello123";exit();
                $dealer_image   = $_FILES["dealer_image"]["name"];  
                // echo $_FILES["dealer_image"]["name"];exit();
                // echo $fileName;exit();
                if($dealer_image!="")
                {
                    // echo $dealer_image;exit();
                // print_r($_FILES["dealer_image"]["name"]);exit();
                    $fileSize   = round($_FILES["dealer_image"]["size"]); // BYTES
                    $adate      = date('Y-m-d H:i:m');
                    $extension  = end(explode(".", $dealer_image));
                    if(!in_array($extension,$allowedExts))
                    {
                        $file_error=true;
                    }
                    $dealer_image   = 'dealer_image_'.substr(sha1(time()), 0, 6).".".$extension;
                    $filePath   = DEALER_PHOTO_A.$dealer_image;
                    $_FILES['dealer_image']['tmp_name'];
                    move_uploaded_file($_FILES['dealer_image']['tmp_name'], $filePath);
                    $new_image=true;
                }
                else
                {
                    $dealer_image="";
                }
            }

            else
            {
                $new_image=false;
                $dealer_image="";
            } 

            $rows   = array(
               
                "dealer_image"=>$dealer_image,
                "dealer_name"=>$dealer_name,
                "dealer_mo"=>$dealer_mo,
                "dealer_sign"=>$dealer_sign,
                "dealer_sign_image"=>$dealer_sign_image,
                
            );
              
             $upadateid = $this->db->rp_update($this->ctable,$rows,"id='".$id."'",0);
             $reply=array("ack"=>1,"developer_msg"=>"
                    Data Add Successfully!!","ack_msg"=>"Sheme Data Add Successfully!!","id"=>$upadateid);
                return $reply;
       }else if($type=="3"){

             if (isset($_FILES["distributor_sign_image"]))
            {
                // print_r($fileName);exit();
                $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
                $temp = explode(".", $_FILES["distributor_sign_image"]["name"]);
                $extension = end($temp);
                // echo"hello123";exit();
                $distributor_sign_image     = $_FILES["distributor_sign_image"]["name"];    
                // echo $_FILES["distributor_sign_image"]["name"];exit();
                // echo $fileName;exit();
                if($distributor_sign_image!="")
                {
                    // echo $distributor_sign_image;exit();
                // print_r($_FILES["distributor_sign_image"]["name"]);exit();
                    $fileSize   = round($_FILES["distributor_sign_image"]["size"]); // BYTES
                    $adate      = date('Y-m-d H:i:m');
                    $extension  = end(explode(".", $distributor_sign_image));
                    if(!in_array($extension,$allowedExts))
                    {
                        $file_error=true;
                    }
                    $distributor_sign_image = 'distributor_sign_image_'.substr(sha1(time()), 0, 6).".".$extension;
                    $filePath   = DISTRIBUTOR_PHOTO_SIGN_A.$distributor_sign_image;
                    $_FILES['distributor_sign_image']['tmp_name'];
                    move_uploaded_file($_FILES['distributor_sign_image']['tmp_name'], $filePath);
                    $new_image=true;
                }
                else
                {
                    $distributor_sign_image="";
                }
            }

            else
            {
                $new_image=false;
                $distributor_sign_image="";
            }

             if (isset($_FILES["distributor_image"]))
            {
                // print_r($fileName);exit();
                $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
                $temp = explode(".", $_FILES["distributor_image"]["name"]);
                $extension = end($temp);
                // echo"hello123";exit();
                $distributor_image  = $_FILES["distributor_image"]["name"]; 
                // echo $_FILES["distributor_image"]["name"];exit();
                // echo $fileName;exit();
                if($distributor_image!="")
                {
                    // echo $distributor_image;exit();
                // print_r($_FILES["distributor_image"]["name"]);exit();
                    $fileSize   = round($_FILES["distributor_image"]["size"]); // BYTES
                    $adate      = date('Y-m-d H:i:m');
                    $extension  = end(explode(".", $distributor_image));
                    if(!in_array($extension,$allowedExts))
                    {
                        $file_error=true;
                    }
                    $distributor_image  = 'distributor_image_'.substr(sha1(time()), 0, 6).".".$extension;
                    $filePath   = DISTRIBUTOR_PHOTO_A.$distributor_image;
                    $_FILES['distributor_image']['tmp_name'];
                    move_uploaded_file($_FILES['distributor_image']['tmp_name'], $filePath);
                    $new_image=true;
                }
                else
                {
                    $distributor_image="";
                }
            }
            else
            {
                $new_image=false;
                $distributor_image="";
            }
                  $rows   = array(
               
                "distributor_image"=>$distributor_image,
                "distributor_name"=>$distributor_name,
                "distributor_mob"=>$distributor_mob,
                "distributor_sign"=>$distributor_sign,
                "distributor_sign_image"=>$distributor_sign_image,
                
            );
              
             $upadateid = $this->db->rp_update($this->ctable,$rows,"id='".$id."'",0);
             $reply=array("ack"=>1,"developer_msg"=>"
                    Data Add Successfully!!","ack_msg"=>"Sheme Data Add Successfully!!","id"=>$upadateid);
                return $reply;

       }else if($type="4"){
            if (isset($_FILES["company_office_sign_image"]))
            {
                    // print_r($fileName);exit();
                    $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
                    $temp = explode(".", $_FILES["company_office_sign_image"]["name"]);
                    $extension = end($temp);
                    // echo"hello123";exit();
                    $company_office_sign_image  = $_FILES["company_office_sign_image"]["name"]; 
              
                    if($company_office_sign_image!="")
                    {
                    
                        $fileSize   = round($_FILES["company_office_sign_image"]["size"]); // BYTES
                        $adate      = date('Y-m-d H:i:m');
                        $extension  = end(explode(".", $company_office_sign_image));
                        if(!in_array($extension,$allowedExts))
                        {
                            $file_error=true;
                        }
                        $company_office_sign_image  = 'company_office_sign_image_'.substr(sha1(time()), 0, 6).".".$extension;
                        $filePath   = COMPANY_OFFICE_PHOTO_SIGN_A.$company_office_sign_image;
                        $_FILES['company_office_sign_image']['tmp_name'];
                        move_uploaded_file($_FILES['company_office_sign_image']['tmp_name'], $filePath);
                        $new_image=true;
                    }
                    else
                    {
                        $company_office_sign_image="";
                    }
            }else
            {
                $new_image=false;
                $company_office_sign_image="";
            }
        if (isset($_FILES["company_office_image"]))
            {
                // print_r($fileName);exit();
                $allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
                $temp = explode(".", $_FILES["company_office_image"]["name"]);
                $extension = end($temp);
                // echo"hello123";exit();
                $company_office_image   = $_FILES["company_office_image"]["name"];  
                // echo $_FILES["company_office_image"]["name"];exit();
                // echo $fileName;exit();
                if($company_office_image!="")
                {
                    // echo $company_office_image;exit();
                // print_r($_FILES["company_office_image"]["name"]);exit();
                    $fileSize   = round($_FILES["company_office_image"]["size"]); // BYTES
                    $adate      = date('Y-m-d H:i:m');
                    $extension  = end(explode(".", $company_office_image));
                    if(!in_array($extension,$allowedExts))
                    {
                        $file_error=true;
                    }
                    $company_office_image   = 'company_office_image_'.substr(sha1(time()), 0, 6).".".$extension;
                    $filePath   = COMPANY_OFFICE_PHOTO_A.$company_office_image;
                    $_FILES['company_office_image']['tmp_name'];
                    move_uploaded_file($_FILES['company_office_image']['tmp_name'], $filePath);
                    $new_image=true;
                }
                else
                {
                    $company_office_image="";
                }
            }

            else
            {
                $new_image=false;
                $company_office_image="";
            }
                $rows   = array(
                    "company_office_image"=>$company_office_image,
                    "company_office_name"=>$company_office_name,
                    "company_office_mob"=>$company_office_mob,
                    "company_office_sign"=>$company_office_sign,
                    "company_office_sign_image"=>$company_office_sign_image,
                );
              
             $upadateid = $this->db->rp_update($this->ctable,$rows,"id='".$id."'",0);
             $reply=array("ack"=>1,"developer_msg"=>"
                    Data Add Successfully!!","ack_msg"=>"Sheme Data Add Successfully!!","id"=>$upadateid);
                return $reply;
       }
        
    }

	public function UpdateFreezeScheme($detail,$file)
	{

				extract($detail);
		
          if (isset($_FILES["agency_permises_photo"]))
         {
    	 
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["agency_permises_photo"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$fileName 	= $_FILES["agency_permises_photo"]["name"];	
    	// echo $fileName;exit();
    	if($fileName!="")
    	{
          // print_r($_FILES["agency_permises_photo"]["name"]);exit();
    		$fileSize 	= round($_FILES["agency_permises_photo"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $fileName));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$agency_permises_photo	= 'agency_permises_photo_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= AGENCY_PERMISES_PHOTO_A.$agency_permises_photo;
	    	 // echo $filePath;exit();
	    	$_FILES['agency_permises_photo']['tmp_name'];
	    	move_uploaded_file($_FILES['agency_permises_photo']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    
		else{
				$agency_permises_photo=$detail['old_file_path'];
			    // $agency_permises_photo="";
			    // echo $agency_permises_photo;exit();
		}
		}
	     else
				    {
					    $agency_permises_photo=$detail['old_file_path'];
  						unset($detail['old_file_path']);
				    }
    if (isset($_FILES["dealer_image"]))
    {
    	// print_r($fileName);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["dealer_image"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$dealer_image 	= $_FILES["dealer_image"]["name"];	
    	// echo $_FILES["dealer_image"]["name"];exit();
    	// echo $fileName;exit();
    	if($dealer_image!="")
    	{
    		// echo $dealer_image;exit();
    	// print_r($_FILES["dealer_image"]["name"]);exit();
    		$fileSize 	= round($_FILES["dealer_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $dealer_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$dealer_image	= 'dealer_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= DEALER_PHOTO_A.$dealer_image;
	    	$_FILES['dealer_image']['tmp_name'];
	    	move_uploaded_file($_FILES['dealer_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	else{
				$dealer_image=$detail['old_file_path1'];
			   
		}
		}
	     else
				    {
					    $dealer_image=$detail['old_file_path1'];
  						unset($detail['old_file_path']);
				    }

    if (isset($_FILES["distributor_image"]))
    {
    	// print_r($fileName);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["distributor_image"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$distributor_image 	= $_FILES["distributor_image"]["name"];	
    	// echo $_FILES["distributor_image"]["name"];exit();
    	// echo $fileName;exit();
    	if($distributor_image!="")
    	{
    		// echo $distributor_image;exit();
    	// print_r($_FILES["distributor_image"]["name"]);exit();
    		$fileSize 	= round($_FILES["distributor_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $distributor_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$distributor_image	= 'distributor_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= DISTRIBUTOR_PHOTO_A.$distributor_image;
	    	$_FILES['distributor_image']['tmp_name'];
	    	move_uploaded_file($_FILES['distributor_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	else{
				$distributor_image=$detail['old_file_path2'];
			   
		}
		}
	     else
				    {
					    $distributor_image=$detail['old_file_path2'];
  						unset($detail['old_file_path']);
				    }
    if (isset($_FILES["company_office_image"]))
    {
    	// print_r($fileName);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["company_office_image"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$company_office_image 	= $_FILES["company_office_image"]["name"];	
    	// echo $_FILES["company_office_image"]["name"];exit();
    	// echo $fileName;exit();
    	if($company_office_image!="")
    	{
    		// echo $company_office_image;exit();
    	// print_r($_FILES["company_office_image"]["name"]);exit();
    		$fileSize 	= round($_FILES["company_office_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $company_office_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$company_office_image	= 'company_office_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= COMPANY_OFFICE_PHOTO_A.$company_office_image;
	    	$_FILES['company_office_image']['tmp_name'];
	    	move_uploaded_file($_FILES['company_office_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	else{
				$company_office_image=$detail['old_file_path3'];
			    
		}
		}
	     else
				    {
					    $company_office_image=$detail['old_file_path3'];
  						unset($detail['old_file_path']);
				    }



   
	if (isset($_FILES["dealer_sign_image"]) && $_FILES['dealer_sign_image']['size']!=0)
    {
    	
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["dealer_sign_image"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$dealer_sign_image 	= $_FILES["dealer_sign_image"]["name"];	
    	// echo $_FILES["dealer_sign_image"]["name"];exit();
    	// echo $fileName;exit();
    	if($dealer_sign_image!="")
    	{
    	
    		$fileSize 	= round($_FILES["dealer_sign_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $dealer_sign_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$dealer_sign_image	= 'dealer_sign_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= DEALER_PHOTO_SIGN_A.$dealer_sign_image;
	    	$_FILES['dealer_sign_image']['tmp_name'];
	    	move_uploaded_file($_FILES['dealer_sign_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
       else{
				$dealer_sign_image=$detail['old_file_path4'];
			   
	   	}
		}
	     else
				    {
				    	  // echo $_REQUEST['id'];exit();
					     $dealer_sign_image=$this->db->rp_getValue("freezer_scheme","dealer_sign_image","id='".$_REQUEST['id']."'",0);
  						
				    }

     if (isset($_FILES["distributor_sign_image"]) && $_FILES['distributor_sign_image']['size']!=0)
    {
    	// print_r($fileName);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["distributor_sign_image"]["name"]);
    	$extension = end($temp);
    	
    	$distributor_sign_image 	= $_FILES["distributor_sign_image"]["name"];	
    
    	if($distributor_sign_image!="")
    	{
    	
    		$fileSize 	= round($_FILES["distributor_sign_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $distributor_sign_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$distributor_sign_image	= 'distributor_sign_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= DISTRIBUTOR_PHOTO_SIGN_A.$distributor_sign_image;
	    	$_FILES['distributor_sign_image']['tmp_name'];
	    	move_uploaded_file($_FILES['distributor_sign_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
     else{
				$distributor_sign_image=$detail['old_file_path5'];
			   
	   	}
		}
	     else
				    {
					   $distributor_sign_image=$this->db->rp_getValue("freezer_scheme","distributor_sign_image","id='".$_REQUEST['id']."'",0);

				    }

   if (isset($_FILES["company_office_sign_image"]) && $_FILES['distributor_sign_image']['size']!=0)
    {
    	// print_r($fileName);exit();
    	$allowedExts = array("jpg","jpeg","png","gif","JPG","JPEG");
    	$temp = explode(".", $_FILES["company_office_sign_image"]["name"]);
    	$extension = end($temp);
    	// echo"hello123";exit();
    	$company_office_sign_image 	= $_FILES["company_office_sign_image"]["name"];	
    	// echo $_FILES["company_office_sign_image"]["name"];exit();
    	// echo $fileName;exit();
    	if($company_office_sign_image!="")
    	{
    		// echo $company_office_sign_image;exit();
    	// print_r($_FILES["company_office_sign_image"]["name"]);exit();
    		$fileSize 	= round($_FILES["company_office_sign_image"]["size"]); // BYTES
    		$adate 		= date('Y-m-d H:i:m');
    		$extension	= end(explode(".", $company_office_sign_image));
    		if(!in_array($extension,$allowedExts))
    		{
    			$file_error=true;
    		}
	    	$company_office_sign_image	= 'company_office_sign_image_'.substr(sha1(time()), 0, 6).".".$extension;
	    	$filePath 	= COMPANY_OFFICE_PHOTO_SIGN_A.$company_office_sign_image;
	    	$_FILES['company_office_sign_image']['tmp_name'];
	    	move_uploaded_file($_FILES['company_office_sign_image']['tmp_name'], $filePath);
	    	$new_image=true;
    	}
    	  else{
				$company_office_sign_image=$detail['old_file_path6'];
			   
	   	}
		}
	     else
				    {
					   
  						$company_office_sign_image=$this->db->rp_getValue("freezer_scheme","company_office_sign_image","id='".$_REQUEST['id']."'",0);
				    }




    $rows 	= array(
			"customer_id"     =>$customer_id,
			"shop_name"=>$shop_name,
			"contact_person"    =>$contact_person,
			"mobile_no"   =>$mobile_no,
			"address" =>$address,
			"taluka"      =>$taluka,
			"district"       =>$district,
			"state"       =>$state,
			"distributor_agency"       =>$distributor_agency,
			"center"=>$center,
			"freeze_model_no"=>$freeze_model_no,
			"hard_top"=>$hard_top,
			"class_top"=>$class_top,
			"agency_permises_photo"=>$agency_permises_photo,
			"dealer_image"=>$dealer_image,
			"distributor_image"=>$distributor_image,
			"company_office_image"=>$company_office_image,
			"dealer_name"=>$dealer_name,
			"dealer_mo"=>$dealer_mo,
			"dealer_sign"=>$dealer_sign,
			"distributor_mob"=>$distributor_mob,
			"distributor_sign"=>$distributor_sign,
			"company_office_name"=>$company_office_name,
			"company_office_mob"=>$company_office_mob,
			"company_office_sign"=>$company_office_sign,
			"executive_type"=>$executive_type,
			"dealer_sign_image"=>$dealer_sign_image,
			"distributor_sign_image"=>$distributor_sign_image,
			"company_office_sign_image"=>$company_office_sign_image,
			"payment"=>$payment,
			"language"=>$language,
			"utr"=>$utr,
		);
          

		$Where = "id='".$_REQUEST['id']."'";
		$eid = $this->db->rp_update($this->ctable,$rows,$Where,0);
		if($eid)
		{     
			/*image code*/
			 if (isset($file["image_path"]) && $file["image_path"]['size']!=[0])
			{
				$ri = $insert;
				$rt = "freezer_scheme";
				$tc = "freezer_scheme";
				$rc = "id";
				for($i=0;$i<sizeof($file["image_path"]['name']);$i++)
				{
					$file_name = $file['image_path']['name'][$i];
					$file_size = $file['image_path']['size'][$i];
					$file_tmp = $file['image_path']['tmp_name'][$i];
					$file_type = $file['image_path']['type'][$i];
					$extension=explode(".",$file_name);

					$allowed_extentions=array("jpg","jpeg","png","JPEG","JPEG","PNG","pdf");
					$extension=$extension[sizeof($extension)-1];
					if(!in_array($extension,$allowed_extentions))
					{
						$file_error=true;
					}
					$orignal_file_name=$extension[0];
					if(in_array($extension,$allowed_extentions))
					{
						// echo $extension;exit();
						$attachment="../images/document_list/";
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
				$upadateid = $this->db->rp_update($this->ctable,array("image_path"=>$image_path),"id='".$_REQUEST['id']."'",0);
			}
			/*image code*/

            // for customer notification added by shivani  
            if(isset($_SESSION[SITE_SESS.'_ADMIN_SESS_ID']) && $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']!="" && isset($_SESSION[SITE_SESS.'_ADMIN_TYPE']) && $_SESSION[SITE_SESS.'_ADMIN_TYPE']!="")
            {
                if($_SESSION[SITE_SESS.'REFERANCE_TYPE']!=0)
                {
                    if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==2)
                    {
                        $edit_by_name=$this->db->rp_getValue("sales_executive","name","isDelete=0 AND id='". $_SESSION[SITE_SESS.'REFERANCE_ID']."'",0); 
                    }
                    if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==3)
                    {
                        $edit_by_name=$this->db->rp_getValue("executive","cname","isDelete=0 AND id='". $_SESSION[SITE_SESS.'REFERANCE_ID']."'",0); 
                    }
                }
                else{
                    $edit_by_name="Admin";
                }
            }else
            {
                $leave_by_name=$this->db->rp_getValue("sales_executive","name","isDelete=0 AND id='". $sales_executive_id."'",0);
            }
            $serial_no=$this->db->rp_getValue($this->ctable,"serial_no","isDelete=0 AND id='". $_REQUEST['id']."'",0);
            $notification_description ="Your deep Freeze of serial no ".$serial_no." has been Edited by ".$edit_by_name;
                 
            $result_sales=$this->objPushNotification->commonNotification($customer_id,$_REQUEST['id'],$this->ctable,"Deep Freeze Edited",$notification_description,"customer","freezer_scheme");
            // for customer notification added by shivani
			
			$reply=array("ack"=>1,"developer_msg"=>"Data Update successfully!!","ack_msg"=>"Data Update successfully!!");
			return $reply;

		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Data Update Failed!!","ack_msg"=>"Data Update Failed!!");
			return $reply;
		}

		
		
	}

	public function GetEditDataFreezerScheme($detail)
	{		
		// echo "hello";exit();
		$where = " id='".$_REQUEST['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData("freezer_scheme","*",$where,"",0);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();

		$result['customer_id']		= htmlentities($ctable_d['customer_id']);
		$result['serial_no']	= htmlentities($ctable_d['serial_no']);
		$result['shop_name']			= htmlentities($ctable_d['shop_name']);
		$result['contact_person']			= htmlentities($ctable_d['contact_person']);
		$result['mobile_no']		= htmlentities($ctable_d['mobile_no']);
		$result['address']		= htmlentities($ctable_d['address']);
		$result['taluka']		= htmlentities($ctable_d['taluka']);
		$result['district']		= htmlentities($ctable_d['district']);
		$result['state']				= htmlentities($ctable_d['state']);
		$result['distributor_agency']				= htmlentities($ctable_d['distributor_agency']);
		$result['center']					= htmlentities($ctable_d['center']);
		$result['freeze_model_no']			= htmlentities($ctable_d['freeze_model_no']);
		$result['hard_top']				= htmlentities($ctable_d['hard_top']);
		$result['class_top']	= htmlentities($ctable_d['class_top']);
		$result['agency_permises_photo']			= htmlentities($ctable_d['agency_permises_photo']);
		$result['dealer_image']			= htmlentities($ctable_d['dealer_image']);
		$result['distributor_image']			= htmlentities($ctable_d['distributor_image']);
		$result['company_office_image']			= htmlentities($ctable_d['company_office_image']);
		$result['dealer_name']			= htmlentities($ctable_d['dealer_name']);
		$result['dealer_mo']			= htmlentities($ctable_d['dealer_mo']);
		$result['dealer_sign']			= htmlentities($ctable_d['dealer_sign']);
		$result['distributor_name']			= htmlentities($ctable_d['distributor_name']);
		$result['distributor_mob']			= htmlentities($ctable_d['distributor_mob']);
		$result['distributor_sign']			= htmlentities($ctable_d['distributor_sign']);
		$result['company_office_name']			= htmlentities($ctable_d['company_office_name']);
		$result['company_office_mob']			= htmlentities($ctable_d['company_office_mob']);
		$result['company_office_sign']			= htmlentities($ctable_d['company_office_sign']);
		$result['image_path']					= htmlentities($ctable_d['image_path']);
		$result['old_image_path']	= htmlentities($ctable_d['old_image_path']);
		$result['executive_type']			= htmlentities($ctable_d['executive_type']);
		$result['dealer_sign_image']			= htmlentities($ctable_d['dealer_sign_image']);
		$result['distributor_sign_image']			= htmlentities($ctable_d['distributor_sign_image']);
		$result['company_office_sign_image']			= htmlentities($ctable_d['company_office_sign_image']);
	  $result['payment'] = htmlentities($ctable_d['payment']);
	  $result['language'] = htmlentities($ctable_d['language']);
	  $result['utr'] = htmlentities($ctable_d['utr']);
		// echo $result['product_id'];exit;
		$reply=array("ack"=>1,"developer_msg"=>"Data fetched Successfully!!.","ack_msg"=>"Success! Data fetched Successfully","result"=>$result);
		return $reply;
	
	}


public function DownloadDeepFreezer($id)
    {
        $id=$this->db->rp_getValue("freezer_scheme","id","id='".$id."'",0);
        $uname=$this->db->rp_getValue("freezer_scheme","shop_name","id='".$id."'",0);
        $uname=$this->db->rp_createSlug($uname);
        
        if($id){
            
            $count=$this->db->rp_getTotalRecord("freezer_scheme","id='".$id."'",0);
            
            if($count >0){
                $body_url=ADMINSITEURL_STATIC."bbsales_tracking/deep_freezer_scheme_pdf.php?id=".$id;
                
                $d=file_get_contents($body_url);
                $d = html_entity_decode($d);
                //print_r($d); exit;
                $relCertFileNames = array();
                $merge_file = array();

                require('../bbsales_tracking/mpdf60/mpdf.php');
                $mpdf = new mPDF('utf-8', // mode - default ''

                'A4', // format - A4, for example, default ''

                10,     // font size - default 0

                'sans-serif',  // default font family

                1,    // margin_left

                1,    // margin right

                10,   // margin top

                5,    // margin bottom

                0,    // margin header

                0,    // margin footer

                'P'); // L - landscape, P - portrait

                /*$mpdf->use_kwt = true;*/
        
                /* $mpdf->autoPageBreak = false;*/
                $mpdf->WriteHTML("p, td { font-family: freeserif; }", 1);
                $mpdf->WriteHTML($d);

                //$fileName = "deepfreezscheme_pdf".$id;
                $date=date("d-m-Y-H-i-s");
                $fileName = $date."-".$uname."-".$id;

                if(!is_dir("../bbsales_tracking/pdf/deepfreezscheme_pdf/".$fileName)){

                    mkdir("../bbsales_tracking/pdf/deepfreezscheme_pdf/".$fileName);
                }

                $pdf_file_path  = "../bbsales_tracking/pdf/deepfreezscheme_pdf/".$fileName."/".$fileName.'.pdf';
                if(file_exists($pdf_file_path)){

                    unlink($pdf_file_path);
                }

                $mpdf->Output($pdf_file_path);
                $pdf_file_path;

                $result=array();
                $result['pdf']=ADMINSITEURL."pdf/deepfreezscheme_pdf/".$fileName."/".$fileName.'.pdf';
                $result['fileName']=$fileName.'.pdf';

                $reply=array("ack"=>1,"developer_msg"=>"Order Generate Successfully","ack_msg"=>"Order Generate Successfully","result"=>$result);
                return $reply;
            }
            else{
                $reply=array("ack"=>0,"developer_msg"=>"Order Not Generate!!","ack_msg"=>"Order Not Generate!!");
                return $reply;
            }
        }
        else{
            $reply=array("ack"=>0,"developer_msg"=>"Order Id Require!!","ack_msg"=>"Order Id Require!!");
            return $reply;
        }
    }

	
		

	
}
?>
