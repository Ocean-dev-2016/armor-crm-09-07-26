<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("class.log.php");
require_once("product.class.php");

class Inward extends Functions
{
	public $db,$log,$product;
	public $ctable="inward_store";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;
		$this->log=new Log();
		$this->product=new Product();		
    } 
	public function InsertInward($detail,$item) 
	{
		//print_r($item);exit;
		$total_qty=0;
		$grand_total=0;
		$subtotal=0;
		$total=0;
		$sum=0;
		$qty=0;
		extract($detail);
//----Insert Data In Orders Table ----------------//				
				$adate	= date('Y-m-d');
				$vendor_name=$this->db->rp_getValue("vendor","cname","id='".$vendor."'");
				$rows 	= array(
							"vendor",
							"vendor_name",
							"sales_id",
							"remark",
							"adate",
						);
				$values = array(
							$vendor,
							$vendor_name,
							"0",
							$remark,
							$adate
						);
						
				$inward_store_id = $this->db->rp_insert($this->ctable,$values,$rows,0);
			if($inward_store_id!=0)
			{
// ---------Insert Order Product Item------------------------------------//
			
				if(!empty($item))
				{
					$total_final=0;
					$total_qty=0;
					for($i=0;$i<sizeof($item);$i++)
					{
						$current_item=$item[$i]; 
						
							$total=$current_item['price']*$current_item['qty'];
							$product_price=$current_item['price'];
								$adate	= date('Y-m-d H:i:s');
								$rows 	= array(
								"inward_store_id",
								"product_id",
								"weight_id",
								"product_name",
								"product_code",
								"receive_qty",
								"product_price",
								"totalprice",
								"adate",
								"isDelete"
							);
							$values = array(
								$inward_store_id,	
								$current_item['product_id'],
								$current_item['weight_id'],
								$current_item['pro_name'],
								"",
								$current_item['qty'],
								$product_price,
								$total,
								$adate,
								"0",
						);
						$total_final+=$total;
						$total_qty+=$current_item['qty'];
						$item_id = $this->db->rp_insert("inward_store_item",$values,$rows,0);
						$all_order_items_with_pid=$this->db->rp_getData("product_weight_price","*","product_id='".$current_item['product_id']."' AND weight_id
						='".$current_item['weight_id']."'","id ASC",0);
						$receive_qty=$current_item['qty'];
						$item_list=array();
						if($all_order_items_with_pid)
						{
						while($current_order_item=mysqli_fetch_assoc($all_order_items_with_pid))
							{
								$product_qty=$current_order_item['stock_qty'];
								if($receive_qty>0)
								{
									$new_receive_qty=$receive_qty+$product_qty;
									// Update qty in order_product_item 
									$order_product_item_id=$current_order_item['product_id'];
									$row 	= array(
											"stock_qty"=>$new_receive_qty,
										);
									$update_dispatch_qty = $this->db->rp_update("product_weight_price",$row,"product_id='".$current_item['product_id']."' AND weight_id='".$current_item['weight_id']."'",0);
									
								}
								else
								{
									break;
								}
								
							}				
					}	
					}	
						// Final total calculations (amount,qty update in main orders table after inserting product item)
						$total=$total_final;
						
					$rows 	= array(
						"total_qty"				=>$total_qty,
						"grand_total"			=> $total,
						);
						$where	= "id='".$inward_store_id."'";
						$order=$this->db->rp_update($this->ctable,$rows,$where,0);

						$reply=array("ack"=>1,"developer_msg"=>"Product Item Order Added.","ack_msg"=>"Success! Product Item Order Successfully.","type"=>$order_id);
						return $reply;
						
				}
				$reply=array("ack"=>1,"developer_msg"=>"Product Item Order Added.","ack_msg"=>"Success! Product Item Order Successfully.");
						return $reply;
			}
			
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Product Item added Failed.");
				return $reply;
			}
	}
//------------Update Inward Product Item----------------------//		 
 public function UpdateInwardItem($detail,$item)
	  {
		$total_qty=0;
		$grand_total=0;
		$sum=0;
		$sum=0;
		$inward_store_id=$_REQUEST['id'];
		$error=array();
		$is_valid_everything =true;
		extract($detail);
		for($i=0;$i<sizeof($item);$i++)
		{
			$current_item=$item[$i];
			$p_id=$item['product_id'];
			$w_id=$item['weight_id'];
			$already_inward_items_with_inward_id=$this->db->rp_getData("inward_store_item","*","inward_store_id='".$inward_store_id."' AND product_id='".$p_id."' AND weight_id='".$w_id."' AND isDelete=0","id ASC",0);
					if($already_inward_items_with_inward_id)
					{
						while($current_inward_item=mysqli_fetch_assoc($already_inward_items_with_inward_id))
						{
							$inwarded_qty=$current_inward_item['receive_qty'];
							// Deduct it from main stock 
							$product_id=$current_inward_item['product_id'];
							$weight_id=$current_inward_item['weight_id'];
							$product_name=$current_inward_item['product_name'];
							$current_prodcut_stock=$this->db->rp_getValue("product_weight_price","stock_qty","product_id='".$product_id."' AND weight_id='".$weight_id."'",0);
							//echo $p->qty;
							//echo $inwarded_qty;exit;
							if($item['qty']<$inwarded_qty)
							{
								$new_product_stock=$current_prodcut_stock-$item['qty'];
								if($new_product_stock<0)
								{
									$error[]=array("product_id"=>$product_id,"msg"=>"For ".$product_name." you can't update inward qty. Inwarded qty already consumed.");
									$is_valid_everything=false;
								}
							}
							
						}
					}
		}
		if($is_valid_everything)
		{ //echo $is_valid_everything;exit;
					
					$already_inward_items_with_inward_id=$this->db->rp_getData("inward_store_item","*","inward_store_id='".$inward_store_id."' AND isDelete=0","id ASC",0);
					if($already_inward_items_with_inward_id)
					{
						while($current_inward_item=mysqli_fetch_assoc($already_inward_items_with_inward_id))
						{
							
							$inwarded_qty=$current_inward_item['receive_qty'];
							//echo $inwarded_qty;exit;
							// Deduct it from main stock 
							$product_id=$current_inward_item['product_id'];
							$weight_id=$current_inward_item['weight_id'];
							$current_prodcut_stock=$this->db->rp_getValue("product_weight_price","stock_qty","product_id='".$product_id."' AND weight_id='".$weight_id."'",0);
							//echo $current_prodcut_stock;
							//echo $inwarded_qty;
							$new_product_stock=$current_prodcut_stock-$inwarded_qty;
							//echo $new_product_stock;
							$this->db->rp_update("product_weight_price",array("stock_qty"=>$new_product_stock),"product_id='".$product_id."' AND weight_id='".$weight_id."'",0);
							$this->db->rp_delete("inward_store_item","id='".$current_inward_item['id']."'",0);
						}	
					}
					$adate	= date("Y-m-d H:i:s");
					for($i=0;$i<sizeof($item);$i++)
					{
						$current_item=$item[$i];
						$total=$current_item['price']*$current_item['qty'];		
						$totalprice = "";
						$pro_name   = $current_item['pro_name'];
						$pro_id     =$current_item['product_id'];
						$weight_id     =$current_item['weight_id'];
						$unitprice  = $current_item['price'];
						$qty 		= $current_item['qty'];
						$totalprice = $this->db->rp_num($unitprice*$qty);
						$final_total+= $totalprice;
						$final_qty+= $qty;
								
						$row = array(
									"inward_store_id",
									"product_id",
									"weight_id",
									"product_name",
									"product_price",
									"receive_qty",
									"totalprice",
									"adate",
								);
						$value = array(
									$inward_store_id,
									$pro_id,
									$weight_id,
									$pro_name,
									$unitprice,
									$qty,
									$totalprice,
									$adate,
								);
						$inward_item = $this->db->rp_insert("inward_store_item",$value,$row,0);
						//update stock qty(+) of product when Inward order
							$all_order_items_with_pid=$this->db->rp_getData("product_weight_price","*","product_id='".$pro_id."' AND weight_id='".$weight_id."'","id ASC",0);
							$receive_qty=$qty;
							if($all_order_items_with_pid)
							{
								while($current_order_item=mysqli_fetch_assoc($all_order_items_with_pid))
								{
									$product_qty=$current_order_item['stock_qty'];
									if($receive_qty>0)
									{
										$new_receive_qty=$receive_qty+$product_qty;
										// Update qty in order_product_item 
										$order_product_item_id=$current_order_item['id'];
										
										$row 	= array(
												"stock_qty"=>$new_receive_qty
											);
										$update_dispatch_qty = $this->db->rp_update("product_weight_price",$row,"product_id='".$current_order_item['product_id']."' AND weight_id='".$current_order_item['weight_id']."'",0);
									}
									else
									{
										break;
									}
									
								}
								
								
							}
								
					}
					$rows 	= array(
						"total_qty"				=> $final_qty,
						"grand_total"			=> $final_total,
						"remark"			=> $remark,
						"vendor"			=> $vendor,
						"adate"			=> $adate,
						);
						$where	= "id='".$_REQUEST['id']."'";
						$orderUpdated=$this->db->rp_update($this->ctable,$rows,$where,0);
						if($orderUpdated)
						{
							$reply=array("ack"=>1,"developer_msg"=>"Inward Item  Updated.","ack_msg"=>"Success!Inward Item  Update Successfully.","type"=>$order_id);
							return $reply;	
						}
						else
						{
						$reply=array("ack"=>0,"developer_msg"=>"Inward Item Updated Failed.","ack_msg"=>"failed! Inward Item  Updated Failed!!");
						return $reply;
						}
		}
		else
		{
		 $ack=array( "ack"=>0,"ack_msg"=>"Inward Could Not Be Updated.","developer_msg"=>"Inward Could Not Updated!! See error object","error"=>$error);
		  return $reply;
		}
	  } 
		//update if no error found(first delete all old items and insert new item)
		
			
			
//-------------Get order Detail----------------//		
	public function GetInward($detail)
	{	
	
		$where = "id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,0);		
		$ctable_d = mysqli_fetch_array($ctable_r);
		
		$result=array();
	   $result['total_qty']		= htmlentities($ctable_d['total_qty']);
	   $result['remark']		= htmlentities($ctable_d['remark']);
	   $result['vendor']		= htmlentities($ctable_d['vendor']);
		$result['grand_total']		= htmlentities($ctable_d['grand_total']);
			// Purchase Item

		$reply=array("ack"=>1,"developer_msg"=>"Inward Item detail fetched!!.","ack_msg"=>"Success! Inward Item Edit Successfully.","result"=>$result);
		return $reply;
	
	}
//------------get All ordered Product Item------------------//	
	public function GetInwardItems($detail)
	{		

			$where = "inward_store_id='".$detail['id']."' AND isDelete=0";
			$ctable_item = $this->db->rp_getData("inward_store_item","*",$where,"",0);
			if($ctable_item)
			{
			while($ctable_item_d = mysqli_fetch_array($ctable_item))
			{
				$result_item=array();
				$result_item['product_id']				= htmlentities($ctable_item_d['product_id']);
				$result_item['inward_store_id']				= htmlentities($ctable_item_d['inward_store_id']);
				$result_item['weight_id']				= htmlentities($ctable_item_d['weight_id']);
				$result_item['product_name']	= htmlentities($ctable_item_d['product_name']);
				$result_item['product_price']	= htmlentities($ctable_item_d['product_price']);
				//$result_item['product_code']	= htmlentities($ctable_item_d['product_code']);
				$result_item['qty']		= htmlentities($ctable_item_d['receive_qty']);
				$result_item['totalprice']	= $this->db->rp_num(htmlentities($ctable_item_d['totalprice']));
				$result[]=$result_item;
			}
			//print_r($result);exit;
			$reply=array("ack"=>1,"developer_msg"=>"Inward Item detail fetched!!.","ack_msg"=>"Success! Update Inward Item Successfully.","result"=>$result);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Update not fetched!!.","ack_msg"=>"Success! Update Failed"	);
			return $reply;
		}
	
	}
//------------------Delete Order Also Delete product item-----------------	//
	public function DeleteInward($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$where_item	= "inward_store_id='".$_REQUEST['id']."'";
			$inward_id=$this->db->rp_update($this->ctable,$rows,$where,0);
			if($inward_id!=0)
			{
				$already_inward_items_with_inward_id=$this->db->rp_getData("inward_store_item","*","inward_store_id='".$_REQUEST['id']."'","id ASC",0);
				if($already_inward_items_with_inward_id)
				{
					while($current_inward_item=mysqli_fetch_assoc($already_inward_items_with_inward_id))
					{
						$inwarded_qty=$current_inward_item['receive_qty'];
						// Deduct it from main stock 
						$product_id=$current_inward_item['product_id'];
						$weight_id=$current_inward_item['weight_id'];
						$current_prodcut_stock=$this->db->rp_getValue("product_weight_price","stock_qty","product_id='".$product_id."' AND weight_id='".$weight_id."'");
						$new_product_stock=$current_prodcut_stock-$inwarded_qty;
						$this->db->rp_update("product_weight_price",array("stock_qty"=>$new_product_stock),"product_id='".$product_id."' AND weight_id='".$weight_id."'",0);
					}	
				}
				$item_id=$this->db->rp_update("inward_store_item",$rows,$where_item);
				$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Inward Successfully.","type"=>$_REQUEST['id']);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Order Failed.");
				return $reply;
			}
	}
}

?>