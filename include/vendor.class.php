<?php
require_once("main.class.php");
require_once("function.class.php");
class Vendor extends Functions
{
	public $db;
	public $ctable="vendor";
	
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;		   
    } 
	 public function InsertVendor($detail) 
	 {
		extract($detail);
		$dup_where = "cname = '".$cname."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Duplicate vendor","ack_msg"=>"Already Exist Name!!");
			return $reply;
		}
		else
		{
			 $adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"company_type",
						"cname",
						"email",
						"tin",
						"pan",
						"gst",
						"vat",
						"phone",
						"address",
						"zip",
						"country",
						"state",
						"city",
						"account_no",
						"ifsc_code",
						"bank_name",
						"isActive",
						"adate"
					);
			$values = array(
						$company_type,
						$cname,
						$email,
						$tin,
						$pan,
						$gst,
						$vat,
						$phone,
						$address,
						$zip,
						$country,
						$state,
						$city,
						$account_no,
						$ifsc_code,
						$bank_name,
						$isActive,
						$adate
					);
					
		 	$uid = $this->db->rp_insert($this->ctable,$values,$rows,0);
			$this->addVendorBranch($uid,$company_name);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Vendor Registered.","ack_msg"=>"Sucess! Insert Vendor Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Insert Record Failed.");
				return $reply;
			}
		}
	 }
	public function UpdateVendor($detail)
	  {
			extract($detail);
			$dup_where = "cname = '".$cname."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
			if($r){
				$this->db->rp_location($ctable."_crud.php?mode=edit&id=".$_REQUEST['id']."&msg=duplicate");
				die;
			}else{
				$rows 	= array(
							"company_type"	=> $company_type,
							"cname"			=> $cname,
							"email"			=> $email,
							"tin"			=> $tin,
							"pan"			=> $pan,
							"gst"			=> $gst,
							"vat"			=> $vat,
							"phone"			=> $phone,
							"address"		=> $address,
							"zip"			=> $zip,
							"country"		=> $country,
							"state"			=> $state,
							"city"			=> $city,
							"account_no"			=> $account_no,
							"ifsc_code"			=> $ifsc_code,
							"bank_name"			=> $bank_name,
						);
				$where	= "id='".$_REQUEST['id']."'";
				$uid=$this->db->rp_update($this->ctable,$rows,$where,0);
				
				if($uid!=0)
				{
					$reply=array("ack"=>1,"developer_msg"=>"Vendor Update Successfull!!.","ack_msg"=>"Success! Update Vendor Successfully.");
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Update Failed.");
					return $reply;
				}
			}	
		}	
	public function EditVendor($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where);
		$ctable_d = mysqli_fetch_array($ctable_r);
		$result=array();
		
		$result['cname']		= htmlentities($ctable_d['cname']);
		$result['company_type']	= htmlentities($ctable_d['company_type']);
		$result['email']		= stripslashes($ctable_d['email']);
		$result['tin']		= stripslashes($ctable_d['tin']);
		$result['pan']		= stripslashes($ctable_d['pan']);
		$result['gst']		= stripslashes($ctable_d['gst']);
		$result['vat']		= stripslashes($ctable_d['vat']);
		$result['phone']		= stripslashes($ctable_d['phone']);
		$result['address']		= htmlentities($ctable_d['address']);
		$result['zip']			= stripslashes($ctable_d['zip']);
		$result['country']		= $ctable_d['country'];
		$result['state'] 		= stripslashes($ctable_d['state']);
		$result['city'] 		= stripslashes($ctable_d['city']);
		$result['account_no'] 		= stripslashes($ctable_d['account_no']);
		$result['ifsc_code'] 		= stripslashes($ctable_d['ifsc_code']);
		$result['bank_name'] 		= stripslashes($ctable_d['bank_name']);
		
		$reply=array("ack"=>1,"developer_msg"=>"Vendor detail fetched!!.","ack_msg"=>"Success! Update Vendor Successfully.","result"=>$result);
		return $reply;
	
	}
	
	public function DeleteVendor($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Vendor Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete record Failed.");
				return $reply;
			}
	}
	
	function addVendorBranch($vid="",$branch_name="",$debug=0)
	{
			if($branch_name!="" && $vid!="")
		{
			$adate	= date('Y-m-d H:i:s');
			$rows=array("vid","branch_name","adate","isDelete");
			$values=array($vid,$branch_name,$adate,0);
			$cbid=$this->db->rp_insert("vendor_branch",$values,$rows,$debug);
			if($cbid!=0)
			{
				return $response=array('ack'=>1,'ack_msg'=>'Branch added Successfully !!!');
				
			}
			else
			{
				return $response=array('ack'=>0,'ack_msg'=>'Branch name can not be empty !!!');			
			}
		}
		else
		{
			return $response=array('ack'=>0,'ack_msg'=>'Branch name can not be empty !!!');	
		}
			
	}
	
	function getVendorBranches($vid="",$debug=0)
	{
	
		$result=array();
		if($vid!="")
		{
			$rows=$this->db->rp_getData("vendor_branch","*","vid='".$vid."' AND isDelete=0","",$debug);
			while($data=mysqli_fetch_assoc($rows)){
				$result[]=$data;
			}			
		}

		return $result;
	}
}

?>