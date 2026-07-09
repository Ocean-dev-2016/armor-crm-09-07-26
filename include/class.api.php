<?php
class Api extends Functions
{
	public $detail=array();
	public $db,$rights;
	public $ctable="api_table";
	public $primary_column="id";
	public $unique_column="api_url";
	// Public Varibale
	public $id='';public $api_slug='';public $api_title='';public $api_url='';public $author='';public $last_modification_date='';public $api_description=''; 
	public $valid_keys=array("id","api_title","api_slug","api_url","author","api_description","last_modification_date");
	function __construct($id="") 
	{
		$db = new Functions();
		$conn = $db->connect();
		$this->db=$db;

		$this->rights=$_SESSION['rights'];
    	}
	function validateKey($detail)
	{
		$error=array();
		foreach($detail as $key=>$value)
		{
			if(!in_array($key,$this->valid_keys))
			{
				$error[]=$key;
			}
		}
		
		if(empty($error))
		{
			$result=array("ack"=>1);
			return $result;
		}
		else
		{
			$result=array("ack"=>0,"error"=>$error);
			return $result;
		}
	}
	 function view($where="",$required_columns=array(),$orderby="",$limit="")
	{

        if($this->rights['view_flag']==1)
		{
           
			$result=array();
        	$required_columns=$this->getRequiredColumns($required_columns);
			$where=($where=="")?"1=1":$where;				
			// Count Total Record Without any limit
			$total_count=$this->rp_getTotalRecord($this->ctable,$where);
			// Count Show Record With limit
			$show_count=$this->rp_getTotalRecord($this->ctable,$where,0,$this->getLimits($limit));
			// Get Actual Records
			$result_r=$this->db->rp_getData($this->ctable,$required_columns,$where,$orderby,0,$this->getLimits($limit),0);
			if($result_r)
			{
				while($d=mysqli_fetch_assoc($result_r))
				{

					// Do Modification IN $d Here If Required
					$result[]=$d;
				}
				
				$reply=array("ack"=>1,"developer_msg"=>"API Detail Fetched Successfully!!","ack_msg"=>"API Detail Fetched Successfully!!","result"=>$result,"total_count"=>$total_count,"show_count"=>$show_count);
				return $reply;
			}
			else
			{

				$reply=array("ack"=>0,"developer_msg"=>"API Detail can not be Fetch!","ack_msg"=>"API Detail can not be Fetch!");
				return $reply;
			}
		}
		else
		{
			$this->db->rp_location("access_denied.php?msg=view");
			$reply=array("ack"=>0,"developer_msg"=>"API Detail cannot be Fetch!","ack_msg"=>"API Detail cannot be Fetch!");
			return $reply;
		}
		
	} 

	function insert($detail=array(),$dup_check_array=array())
	{
		//print_r($detail); exit;
		if($this->rights['insert_flag']==1)
		{
			//$detail=$this->db->cleanArray($detail);
			$validateKey=$this->validateKey($detail);
			
			if($validateKey['ack']==1)
			{
						
				// check required column validation
				if(!empty($detail))
				{
					// count record from duplicate column if required else skip checking
					if(!empty($dup_check_array))
					$count=$this->countApi($dup_check_array['key'],$dup_check_array['value']);
					else
					$count=0;	
					
					if($count<=0)
					{				
							// This is just for my F*ucking Mistake while creating database class :/
							 $extracted_array=$this->extractArray($detail);
							 $inserted_id=$this->db->rp_insert($this->ctable,$extracted_array['values'],$extracted_array['columns'],0);
							
						if($inserted_id!=0)
						{
						    
							$reply=array("ack"=>1,"inserted_id"=>$inserted_id,"developer_msg"=>"API inserted successfully!!","ack_msg"=>"API inserted successfully!!");
							return $reply;
						}
						else
						{
							$reply=array("ack"=>0,"developer_msg"=>"API  can't be inserted!!","ack_msg"=>"API can't be inserted!!");
							return $reply;
						}
					}
					
			
					else
					{
						
						$reply=array("ack"=>0,"developer_msg"=>"Duplicate Record Found!!","ack_msg"=>"Duplicate Record Found!!");
						return $reply;
					}
					
				}
				else
				{
					$reply=array("ack"=>0,"error"=>$validateKey['error'],"developer_msg"=>"Invalid Key","ack_msg"=>"Internal Error!! Try later");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"error"=>$validateKey['error'],"developer_msg"=>"Invalid Key","ack_msg"=>"Internal Error!! Try later");
				return $reply;
			}
		}
		else
		{
			$this->db->rp_location("access_denied.php?msg=insert");
			$reply=array("ack"=>0,"developer_msg"=>"API Detail cannot be Fetch!","ack_msg"=>"API Detail cannot be Fetch!");
			return $reply;
		}
	}
	
	function update($detail=array(),$unique_key,$primary_key,$file)
	{
		if($this->rights['update_flag']==1)
		{
			//$detail=$this->db->cleanArray($detail);
			$validateKey=$this->validateKey($detail);	
		//PRINT_R($detail);EXIT;
			if($validateKey['ack']==1)
			{
				// check required column validation
				if(!empty($detail)&& $primary_key!="")
				{
					// count record from $primary_key
					$count=$this->countApi($this->primary_column,$primary_key);
					if($count>=1)
					{
						$count=$this->duplicateApi($this->unique_column,$unique_key,$primary_key);
						if($count<=0)
						{
							
									$where=$this->primary_column."=".$primary_key;
									$isUpdated=$this->db->rp_update($this->ctable,$detail,$where,0);
									
							if($isUpdated)
							{
    						    $reply=array("ack"=>1,"developer_msg"=>"API Detail Updated Successfully!!","ack_msg"=>"API Detail Updated Successfully!!");
								return $reply;
							}
							else
							{
								$reply=array("ack"=>0,"developer_msg"=>"API Detail cannot be Updated!!","ack_msg"=>"API Detail cannot be Updated!!");
								return $reply;
							}
						}
						else
						{
							$reply=array("ack"=>0,"developer_msg"=>"Duplicate Record Found!!","ack_msg"=>"Duplicate Record Found!!");
							return $reply;
						}
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"No Record Found To Update","ack_msg"=>"Record Not Found!!");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Invalid Key","ack_msg"=>"Internal Error!! Try later");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"error"=>$validateKey['error'],"developer_msg"=>"Invalid Key","ack_msg"=>"Internal Error!! Try later");
					return $reply;
			}
		}
		else
		{
			$this->db->rp_location("access_denied.php?msg=insert");
			$reply=array("ack"=>0,"developer_msg"=>"API Detail cannot be Fetch!","ack_msg"=>"API Detail cannot be Fetch!");
			return $reply;
		}
		
	}
	
	
	function delete($delete_array)
	{
		if($this->rights['delete_flag']==1)
		{
			$count=$this->countApi($delete_array['key'],$delete_array['value']);
            if($count>=1)
            {
				$this->db->rp_update($this->ctable,array("isDelete"=>1),$delete_array['key']."=".$delete_array['value'],0);
            	$reply=array("ack"=>1,"developer_msg"=>"API Deleted Successfully!!","ack_msg"=>"Banner Deleted Successfully!!");
            	return $reply;
            }
            else
            {
            	$reply=array("ack"=>0,"developer_msg"=>"No record found!!","ack_msg"=>"No record found!!");
            	return $reply;
            }
		}
		else
		{
			$this->db->rp_location("access_denied.php?msg=insert");
			$reply=array("ack"=>0,"developer_msg"=>"API cannot be Fetch!","ack_msg"=>"API cannot be Fetch!");
			return $reply;
		}		
	}
	
    function extractArray($array)
	{
		$columns=array();
		$values=array();
		foreach($array as $key=>$value)
		{
			$columns[]=$key;
			$values[]=$value;
		}
		return array("columns"=>$columns,"values"=>$values);
	}
	function getRequiredColumns($required_columns=array())
	{
		if(!empty($required_columns))
		{
			$required_columns_string=implode(",",$required_columns);
			return $required_columns_string;
		}
		else
		{
			return "*";
		}
	}
	
	function countApi($key,$value)
	{		
		$countApi = $this->db->rp_getTotalRecord($this->ctable,$key."='".$value."'",0);
		return $countApi;	
	}
	function duplicateApi($key,$value,$primary_key)
	{		
		$countBus = $this->db->rp_getTotalRecord($this->ctable,$key."='".$value."' AND ".$this->primary_column."!=".$primary_key,0);
		return $countBus;	
	}
	
	function getLimits($limit)
	{
		if($limit!="" && !empty($limit) && array_key_exists("ul",$limit) && array_key_exists("ll",$limit))
		{
		   return $limit['ul'].",".$limit['ll'];
		}
		else
		{
			return "";
		}
	}
   

}

?>