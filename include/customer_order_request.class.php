<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("class.log.php");

class CustomerOrderReq extends Functions
{
	public $db,$log;
	public $ctable="customer_order_request_info";
	public $ctable2="customer_order_request_item";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;
		$this->log=new Log();		   		
    } 
	public function InsertProformaInvoice($detail,$item) 
	{
		$total_qty=0;
		$grand_total=0;
		$sales_subtotal=0;
		$tax=0;
		$subtotal=0;
		extract($detail);
		
			$ctable_where = "id='".$customer_id."' AND isDelete=0";
			$customer_detail_d = $this->db->rp_getData("customer","*",$ctable_where,"",0);
			$customer_detail_r =mysqli_fetch_assoc($customer_detail_d);
			if($customer_detail_r)
			{
				$customer_name=$customer_detail_r['customer_name'];
				$customer_address=$customer_detail_r['customer_address'];
				$customer_phone=$customer_detail_r['customer_phone'];
			}
				$discount=0;
				$tax=0;
				$created_date	= date('Y-m-d H:i:s');
				$invoice_dispatch_document_dated=date("Y-m-d",strtotime($invoice_dispatch_document_dated));
				$invoice_buyer_order_dated=date("Y-m-d",strtotime($invoice_buyer_order_dated));
				$invoice_date=date("Y-m-d",strtotime($invoice_date));
				$due_date=date("Y-m-d",strtotime($due_date));
				$rows 	= array(
							"pro_forma_invoice_no",
							"customer_id",
							"customer_name",
							"customer_phone",
							"customer_address",
							"place_of_supply",
							"pro_forma_invoice_date",
							"discount",
							"tax",
							"isActive",
							"isDelete",
							
							"invoice_reference",
							"invoice_buyer_order_dated",
							"invoice_buyer_order_no",
							"invoice_dispatch_document_dated",
							"invoice_dispatch_document_no",
							"invoice_dispatch_through",
							"invoice_terms_payment",
							"invoice_delivery_note",
							"invoice_due_date",
						);
				$values = array(
							$pro_forma_invoice_no,
							$customer_id,
							$customer_name,
							$customer_phone,
							$customer_address,
							$place_of_supply,
							$invoice_date,
							$discount,
							$tax,
							1,
							0,
							
							$invoice_reference,
							$invoice_buyer_order_dated,
							$invoice_buyer_order_no,
							$invoice_dispatch_document_dated,
							$invoice_dispatch_document_no,
							$invoice_dispatch_through,
							$invoice_terms_payment,
							$invoice_delivery_note,
							$due_date,
						);
						
				$pro_forma_invoice_id = $this->db->rp_insert($this->ctable,$values,$rows,0);
			
			if($pro_forma_invoice_id!=0)
			{
				if(!empty($item))
				{
					$subtotal=0;
					
					foreach($item as $current_item)
					{
					//print_r($current_item); 
					$current_item['fg_item_price']=$this->db->postiveNumber($current_item['fg_item_price']);
					$current_item['fg_item_qty']=$this->db->postiveNumber($current_item['fg_item_qty']);
					
					
					
					$fg_item_subtotal=$current_item['fg_item_price']*$current_item['fg_item_qty'];
					$current_packing_stock=$this->db->rp_getValue("item_fg","fg_stock_qty","id='".$current_item['fg_item_id']."'",0);
					$created_date	= date('Y-m-d H:i:s');
					
					
					
					//tax calculations
					if($place_of_supply==1)
					{
						$cgst_tax_value=$current_item['cgst_tax'];
						$sgst_tax_value=$current_item['sgst_tax'];
						$igst_tax_value=0;
						$igst_tax=0;
						$cgst_tax=($fg_item_subtotal*$cgst_tax_value)/100;
						$sgst_tax=($fg_item_subtotal*$sgst_tax_value)/100;
					}
					else
					{
						$cgst_tax_value=0;
						$sgst_tax_value=0;
						$igst_tax_value=$current_item['igst_tax'];
						$igst_tax=($fg_item_subtotal*$igst_tax_value)/100;
						$cgst_tax=0;
						$sgst_tax=0;
						
					}	
					$fg_item_tax=$total_tax=$igst_tax+$cgst_tax+$sgst_tax;
					$tax+=$total_tax;
					$amount=$total_tax+$fg_item_subtotal;
					
					$fg_item=$this->db->rp_getData("item_fg","*","id='".$current_item['fg_item_id']."'",0);
					$fg_item=mysqli_fetch_assoc($fg_item);
					
					$rows 	= array(
							"pro_forma_invoice_id",
							"fg_item_id",
							"fg_item_name",
							"fg_item_code",
							"fg_item_unit_id",
							"fg_item_unit_name",
							"fg_item_unit_slug",
							"fg_item_qty",
							"fg_item_price",
							"fg_item_subtotal",
							"fg_item_tax",
							"fg_item_grand_total",
							"igst_tax",
							"cgst_tax",
							"sgst_tax",
							"sgst_amount",
							"cgst_amount",
							"igst_amount",
							"amount",
							"isDelete"
						);
				$values = array(
							$pro_forma_invoice_id,	
							$current_item['fg_item_id'],
							$fg_item['fg_item_name'],
							$fg_item['fg_item_code'],
							$fg_item['fg_unit'],
							$fg_item['fg_unit_name'],
							$fg_item['fg_unit_slug'],
							$current_item['fg_item_qty'],
							$current_item['fg_item_price'],
							$fg_item_subtotal,
							$fg_item_tax,
							$amount,
							$igst_tax_value,
							$cgst_tax_value,
							$sgst_tax_value,
							$sgst_tax,
							$cgst_tax,
							$igst_tax,
							$amount,
							0
						);	
					$subtotal+=$fg_item_subtotal;				
					$pro_forma_invoice_item_id = $this->db->rp_insert("pro_forma_invoice_item",$values,$rows,0);
					
					$sales_subtotal+=$subtotal;
				}
					
						// Final purchase calculations
						$grand_total=$sales_subtotal+$tax;
						// Purchase
					$rows 	= array(
						"subtotal"				=> $sales_subtotal,
						"grand_total"			=> $grand_total,
						"tax"					=> $tax,
						);
						$where	= "id='".$pro_forma_invoice_id."'";
						$isUpdated_invoice=$this->db->rp_update($this->ctable,$rows,$where,0);

						
				}
				$reply=array("ack"=>1,"developer_msg"=>"Pro Forma Invoice Added.","ack_msg"=>"Success! Pro Forma Invoice Insert Successfully.");
						return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Pro Forma Invoice Insert Failed.");
				return $reply;
			}
	}
	 
	public function updateProformaInvoice($detail,$item)
	{
		//print_r($item); exit;
		$total_qty=0;
		$grand_total=0;
		$tax=0;
		$subtotal=0;
		$sales_subtotal=0;
		extract($detail);
		
		$ctable_where = "id='".$customer_id."' AND isDelete=0";
			$customer_detail_d = $this->db->rp_getData("customer","*",$ctable_where,"",0);
			$customer_detail_r =mysqli_fetch_assoc($customer_detail_d);
			if($customer_detail_r)
			{
				$customer_name=$customer_detail_r['customer_name'];
				$customer_address=$customer_detail_r['customer_address'];
				$customer_phone=$customer_detail_r['customer_phone'];
			}
			$invoice_dispatch_document_dated=date("Y-m-d",strtotime($invoice_dispatch_document_dated));
			$invoice_buyer_order_dated=date("Y-m-d",strtotime($invoice_buyer_order_dated));
			$due_date=date("Y-m-d",strtotime($due_date));
			$invoice_date=date("Y-m-d",strtotime($invoice_date));
			$rows 	                                = array(
						"pro_forma_invoice_date"	    => $invoice_date,
						"place_of_supply"	    => $place_of_supply,
						"invoice_reference"=> $invoice_reference,
						"invoice_buyer_order_dated" =>$invoice_buyer_order_dated,
						"invoice_buyer_order_no"=>$invoice_buyer_order_no,
						"invoice_dispatch_document_dated"=>$invoice_dispatch_document_dated,
						"invoice_dispatch_document_no"=>$invoice_dispatch_document_no,
						"invoice_dispatch_through"=>$invoice_dispatch_through,
						"invoice_terms_payment"=>$invoice_terms_payment,
						"invoice_delivery_note"=>$invoice_delivery_note,
						"invoice_due_date"=>$due_date,
						);
				$where = "id='".$_REQUEST['id']."'";
				$isUpdated =$this->db->rp_update($this->ctable,$rows,$where,0);
			if($isUpdated)
			{
				
				// Insert Sales Invoice Item
				$this->db->rp_delete("pro_forma_invoice_item","pro_forma_invoice_id='".$_REQUEST['id']."'",0);
				$pro_forma_invoice_id=$_REQUEST['id'];
				// For loop
				
				foreach($item as $current_item)
				{
					
					//print_r($current_item); 
					$current_item['fg_item_price']=$this->db->postiveNumber($current_item['fg_item_price']);
					$current_item['fg_item_qty']=$this->db->postiveNumber($current_item['fg_item_qty']);
					
					
					
					$fg_item_subtotal=$current_item['fg_item_price']*$current_item['fg_item_qty'];
					$current_packing_stock=$this->db->rp_getValue("item_fg","fg_stock_qty","id='".$current_item['fg_item_id']."'",0);
					$created_date	= date('Y-m-d H:i:s');
					
					
					
					//tax calculations
					if($place_of_supply==1)
					{
						$cgst_tax_value=$current_item['cgst_tax'];
						$sgst_tax_value=$current_item['sgst_tax'];
						$igst_tax_value=0;
						$igst_tax=0;
						$cgst_tax=($fg_item_subtotal*$cgst_tax_value)/100;
						$sgst_tax=($fg_item_subtotal*$sgst_tax_value)/100;
					}
					else
					{
						$cgst_tax_value=0;
						$sgst_tax_value=0;
						$igst_tax_value=$current_item['igst_tax'];
						$igst_tax=($fg_item_subtotal*$igst_tax_value)/100;
						$cgst_tax=0;
						$sgst_tax=0;
						
					}	
					$fg_item_tax=$total_tax=$igst_tax+$cgst_tax+$sgst_tax;
					$tax+=$total_tax;
					$amount=$total_tax+$fg_item_subtotal;
					
					$fg_item=$this->db->rp_getData("item_fg","*","id='".$current_item['fg_item_id']."'",0);
					$fg_item=mysqli_fetch_assoc($fg_item);
					
					$rows 	= array(
							"pro_forma_invoice_id",
							"fg_item_id",
							"fg_item_name",
							"fg_item_code",
							"fg_item_unit_id",
							"fg_item_unit_name",
							"fg_item_unit_slug",
							"fg_item_qty",
							"fg_item_price",
							"fg_item_subtotal",
							"fg_item_tax",
							"fg_item_grand_total",
							"igst_tax",
							"cgst_tax",
							"sgst_tax",
							"sgst_amount",
							"cgst_amount",
							"igst_amount",
							"amount",
							"isDelete"
						);
				$values = array(
							$pro_forma_invoice_id,	
							$current_item['fg_item_id'],
							$fg_item['fg_item_name'],
							$fg_item['fg_item_code'],
							$fg_item['fg_unit'],
							$fg_item['fg_unit_name'],
							$fg_item['fg_unit_slug'],
							$current_item['fg_item_qty'],
							$current_item['fg_item_price'],
							$fg_item_subtotal,
							$fg_item_tax,
							$amount,
							$igst_tax_value,
							$cgst_tax_value,
							$sgst_tax_value,
							$sgst_tax,
							$cgst_tax,
							$igst_tax,
							$amount,
							0
						);	
					$subtotal+=$fg_item_subtotal;				
					$pro_forma_invoice_item_id = $this->db->rp_insert("pro_forma_invoice_item",$values,$rows,0);
					$sales_subtotal+=$subtotal;
				}
				
					// Final purchase calculations
					$grand_total=$sales_subtotal+$tax;
					// Purchase
				$rows 	= array(
					"subtotal"				=> $sales_subtotal,
					"grand_total"			=> $grand_total,
					"tax"					=> $tax,
					);
					$where	= "id='".$pro_forma_invoice_id."'";
					$isUpdated_invoice=$this->db->rp_update($this->ctable,$rows,$where,0);
					$reply=array("ack"=>1,"developer_msg"=>"Pro Forma Invoice Updated.","ack_msg"=>"Success! Pro Forma Invoice Update Successfully.");
					return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Pro Forma Invoice Update Fail.","ack_msg"=>"Success! Pro Forma Invoice Update Failed.");
					return $reply;
			}
		}
	public function GetProFormaInvoice($detail)
	{
		$where = "id='".$detail."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,"",0);		
		$ctable_d = mysqli_fetch_array($ctable_r);
		$ctable_d['invoice_dispatch_document_dated']=date("d-m-Y",strtotime($ctable_d['invoice_dispatch_document_dated']));
		
		$ctable_d['invoice_buyer_order_dated']=date("d-m-Y",strtotime($ctable_d['invoice_buyer_order_dated']));
		
		$ctable_d['invoice_due_date']=date("d-m-Y",strtotime($ctable_d['invoice_due_date']));
		
		$ctable_d['pro_forma_invoice_date']=date("d-m-Y",strtotime($ctable_d['pro_forma_invoice_date']));
		
		$reply=array("ack"=>1,"developer_msg"=>"Store detail fetched!!.","ack_msg"=>"Success! Store Edit Successfully.","result"=>$ctable_d);
		return $reply;
	
	}
	public function GetProFormaInvoiceItem($detail)
	{		

		$where = "pro_forma_invoice_id='".$detail['id']."' AND isDelete=0";
		$ctable_item = $this->db->rp_getData("pro_forma_invoice_item","*",$where,"",0);
		if($ctable_item)
		{
			while($ctable_item_d = mysqli_fetch_array($ctable_item))
			{
				$result_item=array();
				$fg_items=$this->db->rp_getData("item_fg","*","id='".$ctable_item_d['fg_item_id']."' AND isDelete=0","",0);
				$fg_item=mysqli_fetch_assoc($fg_items);
				$fg_stock_qty=$fg_item['fg_stock_qty'];
				$result_item['fg_item_id']				= htmlentities($ctable_item_d['fg_item_id']);
				$result_item['fg_item_name']		    = htmlentities($ctable_item_d['fg_item_name']);
				$result_item['fg_item_qty']		        = htmlentities($ctable_item_d['fg_item_qty']);
				$result_item['fg_item_price']	        = htmlentities($ctable_item_d['fg_item_price']);
				$result_item['fg_item_subtotal']	    = htmlentities($ctable_item_d['fg_item_subtotal']);
				$result_item['current_packing_stock']	= $fg_stock_qty;
				$result_item['igst_tax']		        = htmlentities($ctable_item_d['igst_tax']);
				$result_item['cgst_tax']		        = htmlentities($ctable_item_d['cgst_tax']);
				$result_item['sgst_tax']		        = htmlentities($ctable_item_d['sgst_tax']);$result_item['amount']		            = htmlentities($ctable_item_d['amount']);
				$result[]=$result_item;
			}
			$reply=array("ack"=>1,"developer_msg"=>"Store detail fetched!!.","ack_msg"=>"Success! Update Store Successfully.","result"=>$result);
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Update not fetched!!.","ack_msg"=>"Success! Update Failed"	);
			return $reply;
		}
	
	}
	public function DeleteCustomerOrderReq($detail)
	{
		$rows 	= array(
			"isDelete"	=> "1"
			);
		$where	= "id='".$_REQUEST['id']."'";
		$where2	= "request_id='".$_REQUEST['id']."'";
		$isUpdated=$this->db->rp_update($this->ctable,$rows,$where,0);
		$isUpdatedItem=$this->db->rp_update($this->ctable2,$rows,$where2,0);
		
		if($isUpdated && $isUpdatedItem)
		{
			$reply=array("ack"=>1,"developer_msg"=>"deleted data.","ack_msg"=>"Success! Delete Customer Order Request Successfully.");
			return $reply;
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Customer Order Request Failed.");
			return $reply;
		}
		
	}
}

?>