<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("class.log.php");
class RMItemMaster extends Functions
{
	public $db;
	public $log;
	public $ctable="item_rm";
	public $ctableMessurement="messurement";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;	
		$log	= new Log();		
		$this->log=$log;		
    } 
	public function RMItemMasterInsert($detail,$item) 
	{
		extract($detail);
		$dup_where = "rm_item_name = '".$rm_item_name."' AND rm_item_code='".$rm_item_code."' AND isDelete=0";
		$r = $this->db->rp_dupCheck($this->ctable,$dup_where);
		if($r){
			$reply=array("ack"=>0,"developer_msg"=>"Duplicate Row Material Item","ack_msg"=>"Duplication! Already Exist Name.");
			return $reply;
		}
		else
		{
			$rm_unit_name=$this->db->rp_getValue("unit","unit_name","id='".$rm_unit."'");
			$rm_unit_slug=$this->db->rp_getValue("unit","unit_name_slug","id='".$rm_unit."'");
			$rm_item_name_slug=$this->db->rp_createSlug($rm_item_name);
			$adate	= date('Y-m-d H:i:s');
			$rows 	= array(
						"rm_item_code",
						"rm_item_name_slug",
						"rm_item_name",
						"rm_item_category",	
						"rm_packaging_type",	          
						"rm_sku",    
						//"rm_opening_qty",	  
						//"rm_stock_qty",	      
						"rm_min_stock_qty",			    
						"rm_max_stock_qty",			             
						"rm_unit",			             
						"rm_unit_name",			             
						"rm_unit_slug",			             
						"primary_unit_value",			             
						"isDelete",	
						"isActive",	
						
					);
			$values = array(
						$rm_item_code,
						$rm_item_name_slug,
						$rm_item_name,
						$rm_item_category,	
						$rm_packaging_type	,	          
						$rm_sku			      ,    
						//$rm_opening_qty		,	  
						//$rm_opening_qty		,	      
						$rm_min_stock_qty,			    
						$rm_max_stock_qty,			             
						$rm_unit,			             
						$rm_unit_name,			             
						$rm_unit_slug,			             
						$primary_unit_value,			             
						0,			  
						1,
						
					);
					
		 	$item_rm_id = $this->db->rp_insert($this->ctable,$values,$rows,0);
			
			$this->log->insertLog($this->ctable,$item_rm_id,"insert",$this->log->slm['ITEM_RM_INSERT']." : ".$rm_item_name);
			
			if($item_rm_id!=0)
			{
				// Insert Unit Mapping
				if(!empty($item))
				{
					for($i=0;$i<sizeof($item);$i++)
					{
						$current_item=$item[$i]; 
						
						$units=$this->db->rp_getData("unit","*","id='".$current_item['unit_id']."'");
						$unit = mysqli_fetch_assoc($units);
						
						$primary_unit_data=$this->db->rp_getData("unit","*","id='".$rm_unit."'");
						$primary_unit = mysqli_fetch_assoc($primary_unit_data);
						
						$unit_conversion=$current_item['unit_value']/$primary_unit_value;
						$adate	= date('Y-m-d H:i:s');
						$rows 	= array(
								"item_id",
								"item_category_id",
								"primary_unit_id",
								"primary_unit_value",
								"alter_unit_id",
								"alter_unit_value",
								"alter_unit_slug",
								"alter_unit_name",
								"primary_unit_slug",
								"primary_unit_name",
								"unit_conversion",
								"created_date"
							);
						$values = array(
								$item_rm_id,	
								"row_material",	
								$rm_unit,
								$primary_unit_value,
								$current_item['unit_id'],
								$current_item['unit_value'],
								$unit['unit_name_slug'],
								$unit['unit_name'],
								$primary_unit['unit_name_slug'],
								$primary_unit['unit_name'],
								$unit_conversion,
								$adate
							);
						if($current_item['unit_value']!="" && $current_item['unit_value']!=0){
							$item_rm_map_unit_id = $this->db->rp_insert("item_map_unit",$values,$rows,0);
							$unit_name[]=$unit['unit_name'];
						}
					}
					$unit_list=implode(",",$unit_name);
					
					$this->log->insertLog("item_map_unit",$item_rm_id,"insert","RM Item ".$rm_item_name." Inserted  Mapped Unit :\n".$unit_list);
				}
				
				$reply=array("ack"=>1,"developer_msg"=>"Row Material Item Added.","ack_msg"=>"Success! Row Material Item Added Successfully.","id"=>$item_rm_id);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Row Material Item Could Not Be Added. ");
				return $reply;
			}
		}
	}	 
	public function RMItemMasterUpdate($detail,$item)
	{
			extract($detail);
			$rm_unit_name=$this->db->rp_getValue("unit","name","id='".$rm_unit."'");
			$rm_unit_slug=$this->db->rp_getValue("unit","name_slug","id='".$rm_unit."'");
			$dup_where = "rm_item_name = '".$rm_item_name."' AND rm_item_code='".$rm_item_code."' AND id!='".$_REQUEST['id']."' AND isDelete=0";
			$r = $this->db->rp_dupCheck($this->ctable,$dup_where,0);
			if($r){
				$reply=array("ack"=>0,"developer_msg"=>"Duplicate Row Material Item","ack_msg"=>"Duplication! Already Exist Name.");
				return $reply;
				
			}else{
				$rows 	= array(
						"rm_item_name"			                 	 =>$rm_item_name,
						"rm_item_code"		                         =>$rm_item_code,
						"rm_item_category"		                     =>$rm_item_category,
						"rm_packaging_type"		                     =>$rm_packaging_type,
						"rm_sku"		                   	 	 	 =>$rm_sku,
						//"rm_opening_qty"		                 	 =>$rm_opening_qty,
						"rm_min_stock_qty"		                     =>$rm_min_stock_qty,
						"rm_max_stock_qty"		                  	 =>$rm_max_stock_qty,
						"rm_unit"		                  	 		 =>$rm_unit,
						"rm_unit_name"		                  	 	 =>$rm_unit_name,
						"rm_unit_slug"		                  	 	 =>$rm_unit_slug,
						"primary_unit_value"		                 =>$primary_unit_value,
						"isActive"		                 		     =>$isActive,
						"isDelete"		                 	         =>$isDelete,
						);
				$where	= "id='".$_REQUEST['id']."'";
				$uid=$this->db->rp_update($this->ctable,$rows,$where);
				
				$this->log->insertLog($this->ctable,$_REQUEST['id'],"update",$this->log->slm['ITEM_RM_UPDATE']." : ".$rm_item_name);
				if($uid!=0)
				{
					// Insert Unit Mapping
					$this->db->rp_delete("item_map_unit","item_id='".$_REQUEST['id']."' AND item_category_id='row_material'",0);
					if(!empty($item))
					{
						$unit_name=array();
						for($i=0;$i<sizeof($item);$i++)
						{
							$current_item=$item[$i]; 
							
							$units=$this->db->rp_getData("unit","*","id='".$current_item['unit_id']."'");
							$unit = mysqli_fetch_assoc($units);
							
							$primary_unit_data=$this->db->rp_getData("unit","*","id='".$rm_unit."'");
							$primary_unit = mysqli_fetch_assoc($primary_unit_data);
							
							$unit_conversion=$current_item['unit_value']/$primary_unit_value;
							$adate	= date('Y-m-d H:i:s');
							$rows 	= array(
									"item_id",
									"item_category_id",
									"primary_unit_id",
									"primary_unit_value",
									"alter_unit_id",
									"alter_unit_value",
									"alter_unit_slug",
									"alter_unit_name",
									"primary_unit_slug",
									"primary_unit_name",
									"unit_conversion",
									//"created_date"
								);
							$values = array(
									$_REQUEST['id'],	
									"row_material",	
									$rm_unit,
									$primary_unit_value,
									$current_item['unit_id'],
									$current_item['unit_value'],
									"",
									$unit['name'],
									"",
									$primary_unit['name'],
									$unit_conversion,
									//$adate
								);
							if($current_item['unit_value']!="" && $current_item['unit_value']!=0){
								$item_rm_map_unit_id = $this->db->rp_insert("item_map_unit",$values,$rows,0);
								$unit_name[]=$unit['name'];
						
							}
						}
						$unit_list=implode(",",$unit_name);
					
					$this->log->insertLog("item_map_unit",$_REQUEST['id'],"update","RM Item ".$rm_item_name." Updated Mapped Unit :\n".$unit_list);
					}
					
					$reply=array("ack"=>1,"developer_msg"=>"RM Item Updated Successfully!!","ack_msg"=>"RM Item Updated Successfully!!","id"=>$_REQUEST['id']);
					return $reply;
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Row Material Item Could Not Be Updated .");
					return $reply;
				}
			}	
		}	
	public function RMItemMasterGetEditData($detail)
	{		
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where);
		if($ctable_r)
		{
			$ctable_d = mysqli_fetch_array($ctable_r);
			$result=array();
			
			$result=$ctable_d;
			$reply=array("ack"=>1,"developer_msg"=>"Row Material Item detail fetched!!.","ack_msg"=>"Success! Row Material Item Record Fetched Successfully.","result"=>$result);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Row Material Item detail not fetched!!.","ack_msg"=>"Error! Row Material Item Record Not Found");
			return $reply;
		}
	
	}	
	
	public function GetUnitMapItem($detail)
	{		
		//print_r($detail);exit;
		$where = "item_id='".$detail['id']."' AND isDelete=0 AND item_category_id=row_material";
		$ctable_item = $this->db->rp_getData("item_map_unit","*",$where,"",0);
		
	
		if($ctable_item)
		{
				
			while($ctable_item_d = mysqli_fetch_array($ctable_item))
			{
				
				$result_item=array();
				
				$result_item['alter_unit_id']	= htmlentities($ctable_item_d['alter_unit_id']);
				$result_item['alter_unit_name']	= htmlentities($ctable_item_d['alter_unit_name']);
				$result_item['alter_unit_value']	= htmlentities($ctable_item_d['alter_unit_value']);
				$result[]=$result_item;
				print_r($result);
			}
			$reply=array("ack"=>1,"developer_msg"=>"Unit mapping fetched!!.","ack_msg"=>"Success! Unit mapping Successfully Fetched.","result"=>$result);
			return $reply;
			//print_r($result);exit;
		}
	
	}
	public function RMItemMasterDelete($detail)
	{
		$category_name=$this->db->rp_getValue($this->ctable,"rm_item_name","isDelete=0 AND id='".$detail['id']."'");
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$detail['id']."'";
			$isUpdated=$this->db->rp_update($this->ctable,$rows,$where);
			
			$this->log->insertLog($this->ctable,$detail['id'],"delete",$this->log->slm['ITEM_RM_DELETE']." : ".$category_name);
			
			if($isUpdated)
			{
				$reply=array("ack"=>1,"developer_msg"=>"RM Item Deleted Successfully!!","ack_msg"=>"RM Item Deleted Successfully!!");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Row Material Item Record Could Not Be Deleted.");
				return $reply;
			}
	}
	public function RMItemMasterActive($detail)
	{
		$rows 	= array(
			"isActive"	=> $detail['isActive']
		);
			$where	= "id='".$detail['id']."'";
			$uid=$this->db->rp_update($this->ctable,$rows,$where);
			if($uid!=0)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Active status changed of Row Material Item.","ack_msg"=>"Success! Row Material Item Record Status Updated Successfully.");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Row Material Item Record Status Could Not Be Updated.");
				return $reply;
			}
	}
	public function getAllFGItemMaster()
	{
		$where="isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);
		$result=array();
		if($ctable_r)
		{
			while($ctable_d = mysqli_fetch_assoc($ctable_r)){
				$ctable_d['dia_name']=$this->rp_getValue($this->ctableMessurement,"name","id='".$ctable_d['dia']."' AND isDelete=0");
				$result[]=$ctable_d;
			}
			$reply=array("ack"=>1,"developer_msg"=>"Row Material Item detail fetched!!.","ack_msg"=>"Success! Row Material Item Record Fetched Successfully.","result"=>$result);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Row Material Item detail not found!!.","ack_msg"=>"Row Material Item Record Not Found!!");
			return $reply;
		}
	}
}

?>