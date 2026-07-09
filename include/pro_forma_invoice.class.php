<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("class.log.php");
require_once("class.system.php");

class ProFormaInvoice extends Functions
{
	public $db,$log;
	public $ctable="proforma_invoice_info";
	public $ctable_item="proforma_invoice_item";
	public $ctable_order_info="customer_order_request_info";
	public $ctable_order_item="customer_order_request_item";
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;
		$this->log=new Log();		   		
		$this->system=new System();		   		
    } 
	function InsertProformaInvoice($detail,$item) 
	{
		if($detail['customer_order_id']!="" && $detail['place_of_supply']!="" && $detail['invoice_date']!="")
		{
			$order_total_qty=0;
			$order_total_price=0;
			$order_total_item_price=0;
			$order_total_discount_amount=0;
			$order_total_taxable=0;
			$order_total_cgst_tax_amount=0;
			$order_total_sgst_tax_amount=0;
			$order_total_igst_tax_amount=0;
			$order_total_cash_discount=0;
			$order_total_subtotal=0;
			$order_total_grandtotal=0;
			$order_total_roundoff=0;
			$cash_discount=$detail['cash_discount'];
			$cash_discount=($cash_discount!="")?floatval($cash_discount):0;
			$place_of_supply=$detail['place_of_supply'];
			$count =0;
			$SkippedProduct=0;
			$columns=array(
				"invoice_no",
				"request_id",
				"customer_id",
				"customer_name",
				"company_name",
				"email",
				"address",
				"phone",
				"country",
				"state",
				"city",
				"total_qty",
				"item_totalprice",
				"discount",
				"taxable",
				"cash_discount",
				"cash_discount_amount",
				"subtotal",
				"cgst_tax_amount",
				"sgst_tax_amount",
				"igst_tax_amount",
				"grand_total",
				"grand_total_rounded",
				"remarks",
				"isActive",
				"isDelete",
				"invoice_date");

			$order_request_id=$detail['customer_order_id'];
			$order_request_info=$this->db->rp_getData("customer_order_request_info","*","id='".$order_request_id."'");
			if($order_request_info)
			{
				$detail['invoice_date']=date("Y-m-d H:i:s",strtotime($detail['invoice_date']));
				$last_invoice_no=$this->db->getlastInsertId($this->ctable);
				$pro_forma_invoice_no=PROFORMAINVOICE_NO.str_pad($last_invoice_no, 3, '0', STR_PAD_LEFT);
				$order_request_info=mysqli_fetch_assoc($order_request_info);
				$values=array($pro_forma_invoice_no,$order_request_info['id'],$order_request_info['customer_id'],$order_request_info['customer_name'],$order_request_info['company_name'],
							  $order_request_info['email'],$order_request_info['address'],$order_request_info['phone'],
							  $order_request_info['country'],$detail['place_of_supply'],$order_request_info['city'],
							  0,0,0,0,$cash_discount,0,0,0,0,0,0,0,
							  "Against Order Request#".$order_request_info['id'],"1","0",$detail['invoice_date']
							  );
				$proforma_invoice_id=$this->db->rp_insert($this->ctable,$values,$columns,0);
				if($proforma_invoice_id!=0){
					foreach ($item as $key => $i) 
					{
						$product=$this->db->rp_getData("product","*","id='".$i['item_id']."'","",0);
						if($product)
						{
							
							$product=mysqli_fetch_assoc($product);
							$product['name']=html_entity_decode(addslashes($product['name'])); 
							$weight_name=$this->db->rp_getValue("weight","name","id='".$i['weight_id']."'");
							$weight_name=html_entity_decode(addslashes($weight_name)); 
							$inner_size=$this->db->rp_getValue("product_weight_price","inner_size","product_id='".$i['item_id']."' AND weight_id='".$i['weight_id']."'",0);
							$product['name']=$product['name']." (".$weight_name.")"; 
							$count++;
							$qty=$i['qty'];
							$box_qty=$qty/$inner_size;
							$price=$i['price'];
							$discount=$i['discount'];
							$qty=($qty!="")?floatval($qty):0;
							$price=($price!="")?floatval($price):0;
							$discount=($discount!="")?floatval($discount):0;
							$total_price=$this->db->rp_round($qty*$price);
							$discount_amount=$this->db->rp_round(($discount*$total_price)/100);
							$mid_taxable=$this->db->rp_round($total_price-$discount_amount);
							$item_cash_discount_amount=$this->db->rp_round(($mid_taxable*$cash_discount)/100);
							$subtotal=$this->db->rp_round($mid_taxable-$item_cash_discount_amount);

							$cgst_tax=$product['cgst'];
							$sgst_tax=$product['sgst'];
							$igst_tax=$product['igst'];
							$cgst_tax_amount=0;
							$sgst_tax_amount=0;
							$igst_tax_amount=0;

							if($place_of_supply=='Gujarat')
							{
								$cgst_tax_amount=$this->db->rp_round(($subtotal*$cgst_tax)/100);
								$sgst_tax_amount=$this->db->rp_round(($subtotal*$sgst_tax)/100);
								$igst_tax=0;
									
							}
							else
							{
								$igst_tax_amount=$this->db->rp_round(($subtotal*$igst_tax)/100);
								$cgst_tax=0;
								$sgst_tax=0;
							}

							$grand_total=$this->db->rp_round($subtotal+$cgst_tax_amount+$sgst_tax_amount+$igst_tax_amount);
							$order_total_qty+=$qty;
							$order_total_price+=$mid_taxable;
							$order_total_item_price+=$total_price;
							$order_total_discount_amount+=$discount_amount;
							$order_total_taxable+=$mid_taxable;
							$order_total_cash_discount+=$item_cash_discount_amount;
							$order_total_subtotal+=$subtotal;
							$order_total_cgst_tax_amount+=$cgst_tax_amount;
							$order_total_sgst_tax_amount+=$sgst_tax_amount;
							$order_total_igst_tax_amount+=$igst_tax_amount;
							$order_total_grandtotal+=$grand_total;

							$item_columns=array("proforma_invoice_id", "request_id", 
									"item_id", "weight_id", 
									"customer_order_item_id", "item_name", 
									"item_qty", "box_qty", 
									"inner_size", "item_price","item_totalprice",
									"discount", "discount_amount", "taxable", 
									"cash_discount","cash_discount_amount","subtotal",
									"cgst_tax", "cgst_tax_amount", 
									"sgst_tax", "sgst_tax_amount", 
									"igst_tax", "igst_tax_amount", 
									"grandtotal", "isActive", 
									"isDelete");
							 html_entity_decode(addslashes($product['name']));
							$item_values=array($proforma_invoice_id,$order_request_info['id'],
											   $i['item_id'],$i['weight_id'],
											   $i['order_request_item_id'],html_entity_decode(addslashes($product['name'])),
											   $qty,$box_qty,$inner_size,
											   $price,$total_price,$discount,$discount_amount,$mid_taxable,
											   $cash_discount,$item_cash_discount_amount,$subtotal,
											   $cgst_tax,$cgst_tax_amount,
											   $sgst_tax,$sgst_tax_amount,
											   $igst_tax,$igst_tax_amount,$grand_total,1,0
											);

						
							$this->db->rp_insert("proforma_invoice_item",$item_values,$item_columns,0);
						}
						else
						{
							$SkippedProduct++;
						}						
					}	
					$order_total_rounded_grandtotal=round($order_total_grandtotal);
					if($order_total_rounded_grandtotal>$order_total_grandtotal)
					{
						$order_total_roundoff="+"+($order_total_rounded_grandtotal-$order_total_grandtotal);
					}
					else
					{
						$order_total_roundoff="-"+($order_total_grandtotal-$order_total_rounded_grandtotal);
					}

					$rows_update=array("total_qty"=>$order_total_qty,
										"item_totalprice"=>$order_total_item_price,
										"discount"=>$order_total_discount_amount,
										"taxable"=>$order_total_taxable,
										"cash_discount_amount"=>$order_total_cash_discount,
										"subtotal"=>$order_total_subtotal,
										"cgst_tax_amount"=>$order_total_cgst_tax_amount,
										"sgst_tax_amount"=>$order_total_sgst_tax_amount,
										"igst_tax_amount"=>$order_total_igst_tax_amount,
										"roundoff"=>$order_total_roundoff,
										"grand_total"=>$order_total_grandtotal,
										"grand_total_rounded"=>$order_total_rounded_grandtotal);
					$this->db->rp_update($this->ctable,$rows_update,"id='".$proforma_invoice_id."'",0);

					$this->UpdateOrderStatus($order_request_id);

					///////////////////////// Send Notification ///////////////////////

					$title_description="Invoice of <b>Rs.".$order_total_rounded_grandtotal."</b> for date <b>".date('d-m-Y',strtotime($detail['invoice_date']))."</b> added by <b>admin</b> to your account";
					$notification=$this->system->setNotification(0,$order_request_info['customer_id'],"Proforma Invoice Notification.",2,"Proforma Message",$title_description,"","",$detail['invoice_date'],$proforma_invoice_id,"proforma_invoice_info","customer");
					$no_details=$this->db->rp_getData("notification","*","id='".$notification."'");
					if($no_details){
						$no_details=mysqli_fetch_assoc($no_details);
						$notification_data=$no_details;
					}
					$data=$notification_data;
					
					/*$data=array("notification_id"=>$notification,
								"id"=>$proforma_invoice_id,
								"total"=>$order_total_rounded_grandtotal,
								"sales_executive_id"=>$order_request_info['customer_id'],
								"username"=>$order_request_info['customer_name'],
								"notification_type"=>2,
								"msg"=>$title_description,
								"referance_type"=>"proforma_invoice_info",
								"referance_id"=>$proforma_invoice_id,
								"user_type"=>"customer",
								"notification_title"=>"Proforma Invoice Notification");*/

					$refresh_tokens=$this->db->rp_getData("refresh_token","refresh_token","user_id='".$order_request_info['customer_id']."'","",0);
					
						if($refresh_tokens){
							$tokens=array();
							while($refresh_token=mysqli_fetch_assoc($refresh_tokens)){
								$tokens[]=$refresh_token['refresh_token'];
							}
							$result=$this->db->send_notification($data,$tokens,2);
							
						}

					$reply=array("ack"=>1,"developer_msg"=>"Proforma invoice Inserted Successfully","ack_msg"=>"Proforma invoice Inserted Successfully");
					return $reply;
				}else{
					$reply=array("ack"=>0,"developer_msg"=>"Proforma invoice Insert Failed","ack_msg"=>"Proforma invoice Insert Failed");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Customer order request not found","ack_msg"=>"Customer order request not found");
				return $reply;
			}
			
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Proforma invoice could not created customer order request, place of supply and invoice date required.","ack_msg"=>"Proforma invoice could not created customer order request, place of supply and invoice date required.");
			return $reply;
		}
		


	}
	public function DeleteProFormaInvoice($detail)
	{
		$rows 	= array(
		"isDelete"	=> "1"
		);
			$where	= "id='".$_REQUEST['id']."'";
			$where_item	= "proforma_invoice_id='".$_REQUEST['id']."'";
			$isUpdated_id=$this->db->rp_update($this->ctable,$rows,$where,0);
			$id=$this->db->rp_update($this->ctable_item,$rows,$where_item);

			if($isUpdated_id)
			{

				$order_id=$this->db->rp_getValue($this->ctable,"request_id","id='".$_REQUEST['id']."'");
				$dispatch_d=$this->db->rp_getData($this->ctable_item,"*","proforma_invoice_id='".$_REQUEST['id']."'","",0);

				while($dispatch_r=mysqli_fetch_array($dispatch_d))
				{
					$pro_id=$dispatch_r['item_id'];
					$weight_id=$dispatch_r['weight_id'];
					$qty=$dispatch_r['item_qty'];
					$order_id=$dispatch_r['request_id'];
					
					$remaining_qty=$this->db->rp_getValue($this->ctable_order_item,"pending_qty","item_id='".$pro_id."' AND weight_id='".$weight_id."' AND request_id='".$order_id."'",0);
					
					$final_remaining_qty=($remaining_qty)+($qty);
					 
					$rows 	= array(
					"pending_qty"				=>$final_remaining_qty,
					);
					$where	= "item_id='".$pro_id."' AND weight_id='".$weight_id."' AND request_id='".$order_id."'";
					$orderItemUpdated=$this->db->rp_update($this->ctable_order_item,$rows,$where,0);
				}
				$this->db->rp_update($this->ctable_order_info,array("status"=>1),"id='".$order_id."'");
				$reply=array("ack"=>1,"developer_msg"=>"ProForma invoice deleted Successfully.","ack_msg"=>"Success! ProForma invoice deleted Successfully.","type"=>$_REQUEST['id']);
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Database error!!","ack_msg"=>"Failed! Delete Dispatch Failed.");
				return $reply;
			}
	}
	function UpdateOrderStatus($customer_order_id)
	{
		$OrderInfo=$this->rp_getData($this->ctable_order_info,"*","id='".$customer_order_id."'");
		if($OrderInfo)
		{
			$OrderInfo=mysqli_fetch_assoc($OrderInfo);
			$OrderItems=$this->rp_getData($this->ctable_order_item,"*","request_id='".$customer_order_id."'");
			if($OrderItems)
			{
				while($OrderItem=mysqli_fetch_assoc($OrderItems))
				{
					$TotalPackgedQty=$this->rp_getValue($this->ctable_item,"SUM(item_qty)","customer_order_item_id='".$OrderItem['id']."' AND request_id='".$OrderItem['request_id']."' AND isDelete=0",0);
					$TotalOrdered=$OrderItem['request_qty'];
					$RemainingQty=$TotalOrdered-$TotalPackgedQty;
					$RemainingQty=($RemainingQty<0)?0:$RemainingQty;
					if($RemainingQty==0)
					{
						$OrderItemStatus=1;
					}
					else
					{
						$OrderItemStatus=0;
					}
					$isOrderItemUpdated=$this->rp_update($this->ctable_order_item,array("pending_qty"=>$RemainingQty),"id='".$OrderItem['id']."'");					
				}
				// CHeck Order Status
				$OrderStatus=2;
				/*$TotalUnCompletePackagedItem=$this->rp_getTotalRecord($this->ctable_order_item,"request_id='".$OrderInfo['id']."' AND pending_qty!=0");
				if($TotalUnCompletePackagedItem!=0)
				{
					$OrderStatus=1;
				}*/
				$TotalUnCompletePackagedItem=$this->rp_getTotalRecord($this->ctable_order_item,"request_id='".$OrderInfo['id']."' AND pending_qty=0",0);
				if($TotalUnCompletePackagedItem<=0)
				{
					$OrderStatus=1;
				}

				$isOrderUpdated=$this->rp_update($this->ctable_order_info,array("status"=>$OrderStatus),"id='".$customer_order_id."'");		

			}
		}
	}
}

?>