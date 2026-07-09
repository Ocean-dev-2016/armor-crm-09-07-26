<?php
require_once("main.class.php");
require_once("function.class.php");
require_once("class.log.php");
require_once("class.system.php");
require_once("product.class.php");


class InvoceNew extends Functions
{
	public $db,$log,$product;
	public $ctable="invoice_new";
	public $ctablePerforma="proforma_invoice_info";
	public $ctablePerformaItems="proforma_invoice_item";
	public $ctableRequest="customer_order_request_info";
	public $ctableRequestItems="customer_order_request_item";
	public $RequestStatus=array("Pending","Completed");
	public $PerformaStatus=array("Pending","Accepted","Rejected");
	function __construct($id="") 
	{
		$db = new Functions();
		$conn =$db->connect();
		$this->db=$db;
		$this->log=new Log();
		$this->product=new Product();		
		$this->system=new System();		
	} 

	public function AddInvocie($detail,$products)
	{
		//print_r($products); exit;
		$customer_d=$this->db->rp_getData("executive","*","id='".$detail['cid']."' AND isDelete=0","",0);
		if($customer_d)
		{
			$adate = date("Y-m-d H:i:s");
			$customer_r=mysqli_fetch_assoc($customer_d);
			$rows 	= array(
				"dispatch_ids",
				"customer_id",
				"dealer_id",
				"super_stockist_id",
				"customer_name",
				"company_name",
				"customer_type",
				"contact_number",
				"address",
				"city",
				"state",
				"country",
				"status",
				"sales_id",		
				"terms_comdition",
				"faithfully",
				"transport_name",						
				"transport_through",						
				"transport_charge",						
				"shipping_address",						
				"billing_address",					
				"packing_charge",	
				"gst",	
				"vendor_code",
				"tendor_code",
				"entry_flag",			
				"adate",			
				"warehouse_id",	
				"lut_no",	
				"chalan_no",							
				"po_no",							
				"po_date",		
				"pan",		
			);
			$values = array(
				$detail['dispatch_ids'],
				$customer_r['id'],
				$customer_r['dealer_distributor_id'],
				$customer_r['super_stockist_id'],
				$customer_r['cname'],
				$customer_r['company_name'],
				$customer_r['type_of_executive'],
				$customer_r['phone'],
				addslashes(html_entity_decode($customer_r['address'])),
				$customer_r['city'],
				$customer_r['state'],
				$customer_r['country'],
				0,
				$detail['sales_executive_id'],
				$detail['terms_comdition'],
				$detail['faithfully'],
				$detail['transport_name'],
				$detail['transport_through'],
				$detail['transport_charge'],
				$this->db->clean($detail['shipping_address']),
				$this->db->clean($detail['billing_address']),
				$detail['packing_charge'],
				$detail['name_gstin'],
				$detail['vendor_code'],
				$detail['tendor_code'],
				1,
				$adate,
				$detail['warehouse_id'],
				$detail['lut_no'],
				$detail['chalan_no'],
				$detail['po_no'],
				$detail['po_date'],
				isset($customer_r['pan'])?$customer_r['pan']:""

			);

			$order_id = $this->db->rp_insert("invoice_new",$values,$rows,0);
			if($order_id!=0)
			{
				if(!empty($products))
				{
					foreach ($products as $p)
					{
						$product_detail=$this->db->rp_getData("product","*","id='".$p['pid']."'","","0");
						if($product_detail)
						{
							$product_detail=mysqli_fetch_assoc($product_detail);
							$top_cat_id = $product_detail['tcid'];
							$cat_id = $product_detail['cid'];
							$hsn_code = $product_detail['hsn_code'];
							$igst = $product_detail['igst'];
							$ctable_item_weight_detail=$this->db->rp_getData("product_weight_price","*","weight_id='".$p['weight_id']."' AND product_id='".$p['pid']."'","",0);
						
							if($ctable_item_weight_detail)
							{
								$ctable_item_weight_detail=mysqli_fetch_assoc($ctable_item_weight_detail);
								$weight_name=$this->db->rp_getValue("weight","name","id='".$ctable_item_weight_detail['weight_id']."'");																		
								$product_code=$ctable_item_weight_detail['catno'];
								
								if($ctable_item_weight_detail['weight_id']==-1)
								{
									$p['item_name']=addslashes(html_entity_decode($product_detail['name']." (#".stripslashes($product_code).")"));
								}
								else
								{
									$p['item_name']=addslashes(html_entity_decode($product_detail['name']." (".stripslashes($weight_name).")"." (#".stripslashes($product_code).")"));
								}
								$p['inner_size']=$ctable_item_weight_detail['inner_size'];
								$p['outer_size']=$ctable_item_weight_detail['outer_size'];
								/*$p['box_qty']=$p['qty']/$ctable_item_weight_detail['inner_size'];
								$p['cartoon_qty']=$p['box_qty']/$ctable_item_weight_detail['outer_size'];*/
								$p['box_qty']=$p['box_qty'];
								$p['cartoon_qty']=$p['cartoon_qty'];
								
								// $unitprice=$this->db->rp_getValue("product_weight_price","price","product_id='".$p['pid']."' AND weight_id='".$p['weight_id']."'",0);
								$unitprice=$p['price'];
								$unitprice=$this->db->rp_num($unitprice);
								$GST=$product_detail['igst'];
								$totalprice=$p['qty']*$unitprice;
								$totalprice=$this->db->rp_num($totalprice);
								$original_price=$p['original_price'];

								$user_discount=$p['discount'];
								if($user_discount==0)
								{
									$discount_amount=$p['discount_amount'];	
								}
								else
								{
									$discount_amount=($p['original_price']*$user_discount)/100;
								}

								$unitprice_amt=$discount_amount;
								$final_price=$this->db->rp_num($totalprice);
								
								$price_list_price=0;
								$price_list_discounted_price=0;
								$price_list_discounted_amount=0;
								$price_list_discount_type=0;
								$price_list_discount=0;

								if($price_list_id!=0)
								{
									$check_product_in_list=$this->db->rp_getTotalRecord("product_price_list","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'",0);
									if($check_product_in_list>0)
									{
										$add_price_list_id=$price_list_id;
										$price_list_price=$this->db->rp_getValue("product_price_list","price","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");										 

										$GST=$product_detail['igst'];
										$totalprice=$p['qty']*$unitprice;
										$totalprice=$this->db->rp_num($totalprice);
										// $original_price=$price_list_price;
										$final_price=$this->db->rp_num($totalprice);

										$price_list_discount_type=$this->db->rp_getValue("product_price_list","discount_type","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'"); 

										$p_discount=$this->db->rp_getValue("product_price_list","discount","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");
										 
										// $discount = $p['discount'];
										$price_list_discount=$this->db->rp_num($p_discount); 
										$discount_amount1=($p['original_price']*$price_list_discount)/100;
										$price_list_discounted_amount=$this->db->rp_num($discount_amount1);
										$price_list_discounted_price=$p['original_price']-$price_list_discounted_amount;  
									}
									else
									{
										$add_price_list_id=0;
									}
								}
								else
								{
									$add_price_list_id=0;
								}
								 
								$rows 	= array(
									"invoice_id",
									"pro_id",
									"weight_id",
									"pro_name",
									"pro_qty",
									"unitprice",
									"original_price",
									"totalprice",
									"discount",
									"discount_amount",
									"pro_description",
									"cash_discount_amount",
									"additional_discount_amount",
									"igst_tax",
									"igst_amount",
									"taxable",
									"subtotal",
									"other_charge",
									"fright_charge",
									"hsn_code",
									"dispatch_item_type",
									"box_qty",
									"cartoon_qty",
									"loose_qty",
								);
								$values = array(
									$order_id,	
									$p['pid'],
									$p['weight_id'],
									$this->db->clean($p['item_name']),
									$p['qty'],
									$unitprice,
									$original_price,
									$final_price,
									$user_discount,
									$unitprice_amt,
									$this->db->clean($p['pro_description']),
									$p['cd_discount'],
									$p['ad_discount'],
									18,
									$p['gst_amount_item'],
									$p['taxable_amount'],
									$p['sub_total'],
									$p['other_charge'],
									$p['fright_charge'],
									$hsn_code,
									$p['dispatch_item_type'],
									$p['box_qty'],
									$p['cartoon_qty'],
									isset($p['loose'])?$p['loose']:"0",
								);
								$total_qty+=$p['qty'];
								$sub_total+=$final_price;
								$item_id = $this->db->rp_insert("invoice_new_product_item",$values,$rows,0);
							}
						}
					}
					$total_items=$this->db->rp_getTotalRecord("invoice_new_product_item","invoice_id='".$order_id."' AND isDelete=0");
					if($total_items!=0)
					{
						$reply=array("ack"=>1,"developer_msg"=>"Invoice Added Successfully","ack_msg"=>"Invoice Added Successfully","order_id"=>$order_id);
						return $reply;
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Invoice Item Not inserted","ack_msg"=>"Invoice Item Not inserted");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Request Generated With Error Product Not Found ","ack_msg"=>"Request Generated With Error");
					return $reply;
				}
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Request Not Generated","ack_msg"=>"Request Generated With Error");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Customer Not Found!!","ack_msg"=>"Customer Not Found!!");
			return $reply;
		}
	}

	public function PlaceInvociePanel($detail)
	{ 
		$customer_d=$this->db->rp_getData("executive","*","id='".$detail['cid']."' AND isDelete=0");
		$customer_r=mysqli_fetch_assoc($customer_d);
		$order_items_r=$this->db->rp_getData("invoice_new_product_item","*","invoice_id='".$detail['order_id']."' AND isDelete=0","",0);
		if($order_items_r)
		{
			$total_qty=0;
			$sub_total=0;
			while($order_items_d=mysqli_fetch_assoc($order_items_r))
			{
				
				$product_detail=$this->db->rp_getData("product","*","id='".$order_items_d['pro_id']."'","","0");
				if($product_detail)
				{
					$product_detail=mysqli_fetch_assoc($product_detail);
					$ctable_item_weight_detail=$this->db->rp_getData("product_weight_price","*","weight_id='".$order_items_d['weight_id']."' AND product_id='".$order_items_d['pro_id']."'","",0);						
					if($ctable_item_weight_detail)
					{
						$ctable_item_weight_detail=mysqli_fetch_assoc($ctable_item_weight_detail);
						$weight_name=$this->db->rp_getValue("weight","name","id='".$ctable_item_weight_detail['weight_id']."'");
						$order_items_d['item_name']=addslashes(html_entity_decode($product_detail['name']." (".stripslashes($weight_name).")"));
						$order_items_d['inner_size']=$ctable_item_weight_detail['inner_size'];
						$order_items_d['box_qty']=$order_items_d['pro_qty']/$ctable_item_weight_detail['inner_size'];						
						/*if($customer_r['type_of_executive']==7)	
						{
							$GST = 0.1;
						}
						else
						{*/
							$GST=$product_detail['igst'];
						//}
						$unitprice=$this->db->rp_num($order_items_d['unitprice']);
						$totalprice=$order_items_d['pro_qty']*$unitprice;
						$totalprice=$this->db->rp_num($totalprice);
						$original_price=$unitprice;
						$final_price=$totalprice;
						
						$total_qty+=$order_items_d['pro_qty'];
						$sub_total+=$final_price;
						//$item_id = $this->db->rp_insert("order_product_item",$values,$rows,0);
					}
				}
				
				$tot_gst_amount+=$order_items_d['igst_amount'];
			}
// echo $igst_amount;exit;
			
			if($detail['cash_discount']!="" || ($detail['cash_discount_amount']!="" && $detail['cash_discount_amount']!=0))
			{
				$cash_discount=$detail['cash_discount'];
				if($cash_discount)
				{
					$cash_discount_amount=$this->db->rp_num(($sub_total*$detail['cash_discount'])/100);
				}
				else
				{
					$cash_discount_amount=$detail['cash_discount_amount'];
				}
				if($sub_total>$cash_discount_amount)
				{
					$sub_total=$this->db->rp_num($sub_total-$cash_discount_amount);
				}
				else
				{
					$sub_total=$this->db->rp_num($cash_discount_amount-$sub_total);
				}
			}
			else
			{
				$cash_discount_amount=0;
				$cash_discount=0;
				$sub_total=$this->db->rp_num($sub_total);
			}


			if($detail['additional_discount']!="" || ($detail['additional_discount_amount']!="" && $detail['additional_discount_amount']!=0))
			{
				$additional_discount=$detail['additional_discount'];
				if($additional_discount)
				{
					$additional_discount_amount=$this->db->rp_num(($sub_total*$detail['additional_discount'])/100);
				}
				else
				{
					$additional_discount_amount=$this->db->rp_num($detail['additional_discount_amount']);
				}
				if($sub_total>$additional_discount_amount)
				{
					$sub_total=$this->db->rp_num($sub_total-$additional_discount_amount);
				}
				else
				{
					$sub_total=$this->db->rp_num($additional_discount_amount-$sub_total);
				}
			}
			else
			{
				$additional_discount_amount=0;
				$additional_discount=0;
				$sub_total=$this->db->rp_num($sub_total);
			}

			if($detail['gst_apply_flag']!=0)
			{  
				$sub_total1 = $sub_total+$detail['transport_charge']+$detail['packing_charge'];
				// $gst_amount = $this->db->rp_num(($sub_total1*$GST)/100);
				$gst_amount = $tot_gst_amount;
				
				$grand_total = $this->db->rp_num($sub_total1+$gst_amount);
			}
			else
			{ 
				$sub_total1 = $sub_total+$detail['transport_charge']+$detail['packing_charge'];
				$gst_amount = 0;
				$grand_total=$sub_total1;
			}

			if($detail['tcs_apply_flag']!=0)
			{
				$sub_total1 = $sub_total+$detail['transport_charge']+$detail['packing_charge'];
				// $gst_amount = $this->db->rp_num(($sub_total1*$GST)/100);
				$tcs_amount = $this->db->rp_num(($grand_total*TCS_CHARGE_IN_PER)/100);
				$grand_total = $this->db->rp_num($grand_total+$tcs_amount);
			}
			else
			{
				$sub_total1 = $grand_total+$detail['transport_charge']+$detail['packing_charge'];
				$tcs_amount = 0;
				$grand_total=$sub_total1;
			}
// echo $grand_total;exit;

			// $trans_charge = $sub_total + $detail['transport_charge'];
			/*$sub_total += $detail['transport_charge']+$detail['packing_charge'];
			$gst_amount=$this->db->rp_num(($sub_total*$GST)/100);
			// $gst_amount = $gst_amount + $detail['transport_charge'];
			$grand_total=$this->db->rp_num($sub_total+$gst_amount);*/

			//"cash_discount_amount"=>$cash_discount_amount,"cash_discount"=>$cash_discount,"additional_discount_amount"=>$additional_discount_amount,"additional_discount"=>$additional_discount
			/*igst_amount"=>$gst_amount*/

			$dt=date("Y-m-d");
			//$tcs_amount = 0;
			$isUpdated=$this->db->rp_update("invoice_new",array("total_qty"=>$total_qty,"subtotal"=>$sub_total1,"grand_total"=>round($grand_total),"remaining_amount"=>round($grand_total),"cash_discount_amount"=>$detail['cash_discount_amount'],"cash_discount"=>$detail['cash_discount'],"additional_discount_amount"=>$detail['additional_discount_amount'],"additional_discount"=>$detail['additional_discount'],"igst_amount"=>$gst_amount,"remarks"=>$detail['remarks'], "terms_comdition" => $detail['terms_comdition'], "faithfully" => $detail['faithfully'], "transport_name" => $detail['transport_name'], "transport_through" => $detail['transport_through'], "transport_charge" => $detail['transport_charge'],"shipping_address"=>$this->db->clean($detail['shipping_address']),"billing_address"=>$this->db->clean($detail['billing_address']),"packing_charge"=>$detail['packing_charge'],"vendor_code"=>$detail['vendor_code'],"tendor_code"=>$detail['tendor_code'],"tcs_per"=>TCS_CHARGE_IN_PER,"tcs_amount"=>$tcs_amount,"update_entry_flag" => 1,"transport_charge_gst"=>$detail['transport_charge_gst'],"packing_charge_gst"=>$detail['packing_charge_gst'],"cd_gst"=>$detail['cd_gst'],"ad_gst"=>$detail['ad_gst'],"lut_no"=>$detail['lut_no'],"warehouse_id"=>$detail['warehouse_id'],"chalan_no"=>$detail['chalan_no'],"po_no"=>$detail['po_no'],"po_date"=>$detail['po_date'],"total_parcel"=>$detail['total_parcel'],"total_weight"=>$detail['total_weight']),"id='".$detail['order_id']."'",0);

			if($isUpdated)
			{
				$reply=array("ack"=>1,"developer_msg"=>"Your Invoice is Added Successfully","ack_msg"=>"Your Invoice is Added Successfully");
				return $reply;
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Your Invoice is Not Added","ack_msg"=>"Your Your Invoice is Not Added");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Something went wrong!! Product Not in cart please check","ack_msg"=>"Something went wrong!! Product Not in cart please check");
			return $reply;
		}
		
	}

	public function GetOrder($detail)
	{
		$where = " id='".$detail['id']."' AND isDelete=0";
		$ctable_r = $this->db->rp_getData($this->ctable,"*",$where,1);		
		$ctable_d = mysqli_fetch_array($ctable_r);
		
		$result=array();
	    $result['customer_type']		= htmlentities($ctable_d['customer_type']);
	    $result['customer_id']			= htmlentities($ctable_d['customer_id']);
	    $result['order_no']				= htmlentities($ctable_d['order_no']);
	    $result['order_date']			= date("d-m-Y",strtotime($ctable_d['order_date']));
	    $result['total_amount']			= htmlentities($ctable_d['total_amount']);
		$result['total_qty']			= htmlentities($ctable_d['total_qty']);
		$result['grand_total']			= htmlentities($ctable_d['grand_total']);
		$result['remarks']				= htmlentities($ctable_d['remarks']);
		$result['chalan_no']			= htmlentities($ctable_d['chalan_no']);
		$result['po_no']				= htmlentities($ctable_d['po_no']);
		$result['po_date']				= htmlentities(date('d-m-Y',strtotime($ctable_d['po_date'])));
		$result['terms_comdition']		= htmlentities($ctable_d['terms_comdition']);
		$result['faithfully']			= htmlentities($ctable_d['faithfully']);
		$result['transport_name']		= htmlentities($ctable_d['transport_name']);
		$result['transport_through']	= htmlentities($ctable_d['transport_through']);
		$result['transport_charge']		= htmlentities($ctable_d['transport_charge']);
		$result['shipping_address']		= htmlentities($ctable_d['shipping_address']);
		$result['billing_address']		= htmlentities($ctable_d['billing_address']);
		$result['packing_charge']		= htmlentities($ctable_d['packing_charge']);
		$result['vendor_code']			= htmlentities($ctable_d['vendor_code']);
		$result['tendor_code']			= htmlentities($ctable_d['tendor_code']);
		$result['name_gstin']			= htmlentities($ctable_d['gst']);
		$result['cash_discount']        = htmlentities($ctable_d['cash_discount']);
		$result['additional_discount']  = htmlentities($ctable_d['additional_discount']);
		$result['cash_discount_amount'] = htmlentities($ctable_d['cash_discount_amount']);
		$result['additional_discount_amount']  = htmlentities($ctable_d['additional_discount_amount']);
		$result['igst_amount']  			   = htmlentities($ctable_d['igst_amount']);
		$result['tcs_amount']  		           = htmlentities($ctable_d['tcs_amount']);
		$result['transport_charge_per']  	   = htmlentities($ctable_d['transport_charge_gst']);
		$result['packing_charge_per']  		   = htmlentities($ctable_d['packing_charge_gst']);
		$result['cd_gst']  		               = htmlentities($ctable_d['cd_gst']);
		$result['ad_gst']  		               = htmlentities($ctable_d['ad_gst']);
		$result['warehouse_id']                = explode(",", $ctable_d['warehouse_id']);
		$result['lut_no']  		               = htmlentities($ctable_d['lut_no']);
		$result['invoice_no']  		           = htmlentities($ctable_d['invoice_no']);
		$result['total_parcel']  		       = htmlentities($ctable_d['total_parcel']);
		$result['total_weight']  		       = htmlentities($ctable_d['total_weight']);
		
		// Purchase Item
		$reply=array("ack"=>1,"developer_msg"=>"Product Item detail fetched!!.","ack_msg"=>"Success! Product Item Edit Successfully.","result"=>$result);
		return $reply;
	}

	public function GetOrderItems($detail)
	{		
		$where = "invoice_id='".$detail['id']."' AND isDelete=0";
		$ctable_item = $this->db->rp_getData("invoice_new_product_item","*",$where,"",0);
		if($ctable_item)
		{
			while(	$ctable_item_d = mysqli_fetch_array($ctable_item))
			{
				$result_item=array();
				$result_item['product_id']			= htmlentities($ctable_item_d['pro_id']);
				$result_item['order_id']			= htmlentities($ctable_item_d['order_id']);
				$result_item['weight_id']			= htmlentities($ctable_item_d['weight_id']);
				$pro_name                           = $this->db->rp_getValue("product","name","id='".$ctable_item_d['pro_id']."'");	
				$size_name                          = $this->db->rp_getValue("weight","name","id='".$ctable_item_d['weight_id']."'");	
				$result_item['product_name']        = $size_name." ".$pro_name." ";
				$result_item['qty']					= htmlentities($ctable_item_d['pro_qty']);
				$result_item['inner_size']			= htmlentities($ctable_item_d['inner_size']);
				$result_item['outer_size']			= htmlentities($ctable_item_d['outer_size']);
				$result_item['box']					= htmlentities($ctable_item_d['cartoon_qty']);
				$result_item['bag']					= $this->db->rp_num(htmlentities($ctable_item_d['box_qty']));
				$result_item['loose']				= $this->db->rp_num(htmlentities($ctable_item_d['loose_qty']));
				$result_item['discount_per']		= $this->db->rp_num(htmlentities($ctable_item_d['discount']));
				$result_item['product_price']		= $this->db->rp_num(htmlentities($ctable_item_d['unitprice']));
				$result_item['original_price']		= $this->db->rp_num(htmlentities($ctable_item_d['original_price']));
				$result_item['product_total']		= $this->db->rp_num(htmlentities($ctable_item_d['totalprice']));
				$result_item['discount_amount']		= $this->db->rp_num(htmlentities($ctable_item_d['discount_amount']));
				$result_item['pro_description']		= htmlentities($ctable_item_d['pro_description']);
				$result_item['stock']		        = $this->db->rp_getValue("product_weight_price","stock_qty","product_id='".$ctable_item_d['pro_id']."'");	
				$result_item['cd_amount']			= $this->db->rp_num(htmlentities($ctable_item_d['cash_discount_amount']));
				$result_item['ad_amount']			= $this->db->rp_num(htmlentities($ctable_item_d['additional_discount_amount']));
				$result_item['gst_amount_item']		= $this->db->rp_num(htmlentities($ctable_item_d['igst_amount']));
				$result_item['taxable_amount']		= $this->db->rp_num(htmlentities($ctable_item_d['taxable']));
				$result_item['sub_total']			= $this->db->rp_num(htmlentities($ctable_item_d['subtotal']));
				$result_item['gst']         		= $this->db->rp_getValue("product","igst","id='".$result_item['product_id']."' AND isDelete=0");
				$result_item['other_charge']		= $this->db->rp_num(htmlentities($ctable_item_d['other_charge']));
				$result_item['fright_charge']		= $this->db->rp_num(htmlentities($ctable_item_d['fright_charge']));

				$top_cat_id                         = $this->db->rp_getValue("product","tcid","id='".$ctable_item_d['pro_id']."'");	
				$result_item['top_cat_name'] 		= $this->db->rp_getValue("top_category_master","name","id='".$top_cat_id."' AND isDelete=0",0);
				$cat_id                         	= $this->db->rp_getValue("product","cid","id='".$ctable_item_d['pro_id']."'");	
				$result_item['category_name'] 		= $this->db->rp_getValue("category_master","name","id='".$cat_id."' AND isDelete=0",0);
				$result_item['cat_no']		    = $this->db->rp_getValue("product_weight_price","catno","product_id='".$result_item['product_id']."'");		 

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

	public function UpdateInvoice($detail,$products)
	{
		$customer_d=$this->db->rp_getData("executive","*","id='".$detail['cid']."' AND isDelete=0","",0);
		if($customer_d)
		{
			$where="";
			if($detail['sales_executive_id']!="")
			{
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
				{
					$where=" AND sales_id = '".$detail['sales_executive_id']."' ";
				}
			}
			else
			{
				$detail['sales_executive_id']="";
				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
				{
					$where=" AND sales_id = 0 ";
				}
			}

			$check_cart_exist=$this->db->rp_getTotalRecord("invoice_new","id='".$detail['order_id']."' AND isDelete=0 ".$where,0);
			
			if($check_cart_exist!=0)
			{
				if($detail['order_id']!=0)
				{
					$order_id=$detail['order_id'];
					if(!empty($products))
					{
						// delete product
						$this->db->rp_delete("invoice_new_product_item","invoice_id='".$order_id."'");
						// delete product

						foreach ($products as $p)
						{
							$product_detail=$this->db->rp_getData("product","*","id='".$p['pid']."'","","0");
							if($product_detail)
							{
								$product_detail=mysqli_fetch_assoc($product_detail);
								$top_cat_id = $product_detail['tcid'];
								$cat_id = $product_detail['cid'];
								$hsn_code = $product_detail['hsn_code'];
								$igst = $product_detail['igst'];
								$ctable_item_weight_detail=$this->db->rp_getData("product_weight_price","*","weight_id='".$p['weight_id']."' AND product_id='".$p['pid']."'","",0);
							
								if($ctable_item_weight_detail)
								{
									$ctable_item_weight_detail=mysqli_fetch_assoc($ctable_item_weight_detail);
									$weight_name=$this->db->rp_getValue("weight","name","id='".$ctable_item_weight_detail['weight_id']."'");																		
									$product_code=$ctable_item_weight_detail['catno'];
									
									if($ctable_item_weight_detail['weight_id']==-1)
									{
										$p['item_name']=addslashes(html_entity_decode($product_detail['name']." (#".stripslashes($product_code).")"));
									}
									else
									{
										$p['item_name']=addslashes(html_entity_decode($product_detail['name']." (".stripslashes($weight_name).")"." (#".stripslashes($product_code).")"));
									}
									$p['inner_size']=$ctable_item_weight_detail['inner_size'];
									$p['outer_size']=$ctable_item_weight_detail['outer_size'];
									/*$p['box_qty']=$p['qty']/$ctable_item_weight_detail['inner_size'];
									$p['cartoon_qty']=$p['box_qty']/$ctable_item_weight_detail['outer_size'];*/
									$p['box_qty']=$p['box_qty'];
									$p['cartoon_qty']=$p['cartoon_qty'];
									
									// $unitprice=$this->db->rp_getValue("product_weight_price","price","product_id='".$p['pid']."' AND weight_id='".$p['weight_id']."'",0);
									$unitprice=$this->db->rp_num($p['price']);
									$GST=$product_detail['igst'];
									$totalprice=$p['qty']*$unitprice;
									$totalprice=$this->db->rp_num($totalprice);
									$original_price=$p['original_price'];

									$user_discount=$p['discount'];
									if($user_discount==0)
									{
										$discount_amount=$p['discount_amount'];	
									}
									else
									{
										$discount_amount=($p['original_price']*$user_discount)/100;
									}

									$unitprice_amt=$discount_amount;
									$final_price=$this->db->rp_num($totalprice);
									
									$price_list_price=0;
									$price_list_discounted_price=0;
									$price_list_discounted_amount=0;
									$price_list_discount_type=0;
									$price_list_discount=0;

									if($price_list_id!=0)
									{
										$check_product_in_list=$this->db->rp_getTotalRecord("product_price_list","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'",0);
										if($check_product_in_list>0)
										{
											$add_price_list_id=$price_list_id;
											$price_list_price=$this->db->rp_getValue("product_price_list","price","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");										 

											$GST=$product_detail['igst'];
											$totalprice=$p['qty']*$unitprice;
											$totalprice=$this->db->rp_num($totalprice);
											// $original_price=$price_list_price;
											$final_price=$this->db->rp_num($totalprice);

											$price_list_discount_type=$this->db->rp_getValue("product_price_list","discount_type","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'"); 

											$p_discount=$this->db->rp_getValue("product_price_list","discount","pid='".$p['pid']."' AND weight_id='".$ctable_item_weight_detail['weight_id']."' AND price_list_id='".$price_list_id."'");
											 
											// $discount = $p['discount'];
											$price_list_discount=$this->db->rp_num($p_discount); 
											$discount_amount1=($p['original_price']*$price_list_discount)/100;
											$price_list_discounted_amount=$this->db->rp_num($discount_amount1);
											$price_list_discounted_price=$p['original_price']-$price_list_discounted_amount;  
										}
										else
										{
											$add_price_list_id=0;
										}
									}
									else
									{
										$add_price_list_id=0;
									}
									
								$rows 	= array(
									"invoice_id",
									"pro_id",
									"weight_id",
									"pro_name",
									"pro_qty",
									"unitprice",
									"original_price",
									"totalprice",
									"discount",
									"discount_amount",
									"pro_description",
									"cash_discount_amount",
									"additional_discount_amount",
									"igst_tax",
									"igst_amount",
									"taxable",
									"subtotal",
									"other_charge",
									"fright_charge",
									"hsn_code",
									"dispatch_item_type",
									"box_qty",
									"cartoon_qty",
									"loose_qty",
								);
								$values = array(
									$order_id,	
									$p['pid'],
									$p['weight_id'],
									$this->db->clean($p['item_name']),
									$p['qty'],
									$unitprice,
									$original_price,
									$final_price,
									$user_discount,
									$unitprice_amt,
									$this->db->clean($p['pro_description']),
									$p['cd_discount'],
									$p['ad_discount'],
									$GST,
									$p['gst_amount_item'],
									$p['taxable_amount'],
									$p['sub_total'],
									$p['other_charge'],
									$p['fright_charge'],
									$hsn_code,
									$p['dispatch_item_type'],
									$p['box_qty'],
									$p['cartoon_qty'],
									isset($p['loose'])?$p['loose']:"0",
								);
									$total_qty+=$p['qty'];
									$sub_total+=$final_price;
									$item_id = $this->db->rp_insert("invoice_new_product_item",$values,$rows,0);
								}
						
							}
						}
						$total_items=$this->db->rp_getTotalRecord("invoice_new_product_item","invoice_id='".$order_id."' AND isDelete=0");
						if($total_items!=0)
						{
							$reply=array("ack"=>1,"developer_msg"=>"Invoice Updated Successfully","ack_msg"=>"Invoice Updated Successfully","order_id"=>$order_id);
							return $reply;
						}
						else
						{
							$reply=array("ack"=>0,"developer_msg"=>"Invoice Item Not Updated","ack_msg"=>"Invoice Item Not Updated");
							return $reply;
						}
					
					}
					else
					{
						$reply=array("ack"=>0,"developer_msg"=>"Request Generated With Error Product Not Found ","ack_msg"=>"Request Generated With Error");
						return $reply;
					}
				}
				else
				{
					$reply=array("ack"=>0,"developer_msg"=>"Request Not Generated","ack_msg"=>"Request Generated With Error");
					return $reply;
				}
				
			}
			else
			{
				$reply=array("ack"=>0,"developer_msg"=>"Order Not Found!!","ack_msg"=>"Order Not Found!!");
				return $reply;
			}
		}
		else
		{
			$reply=array("ack"=>0,"developer_msg"=>"Customer Not Found!!","ack_msg"=>"Customer Not Found!!");
			return $reply;
		}
	}

	
	public function GetInvocenew($detail)
	{

		$where = " id='".$detail['id']."' AND isDelete=0";
		$dispatchid = $this->rp_getValue("packing_slip","dispatch_id",$where,0);
		
		$order_id = $this->rp_getValue("dispatch_detail","order_id","id='".$dispatchid."'",0);
		$ctable_r = $this->db->rp_getData("orders","*","id='".$order_id."'","",0);		
		// $ctable_r = $this->rp_getData("dispatch_detail","*","id='".$dispatchid."'",1);

		$ctable_d = mysqli_fetch_array($ctable_r);
		
		$result=array();
	    $result['tcs_amount']  					= htmlentities($ctable_d['tcs_amount']);
	    $result['customer_id']  					= htmlentities($ctable_d['customer_id']);
	    $result['shipping_address']  				= htmlentities($ctable_d['shipping_address']);
	    $result['billing_address']  				= htmlentities($ctable_d['billing_address']);
		$result['vendor_code']  		    		= htmlentities($ctable_d['vendor_code']);
		$result['tendor_code']  		    		= htmlentities($ctable_d['tendor_code']);
		$result['transport_name']  		    		= htmlentities($ctable_d['transport_name']);
	    $result['transport_through']  				= htmlentities($ctable_d['transport_through']);
	    $result['cash_discount']  					= htmlentities($ctable_d['cash_discount']);
	    $result['cash_discount_amount']  			= htmlentities($ctable_d['cash_discount_amount']);
	    $result['additional_discount']  			= htmlentities($ctable_d['additional_discount']);
	    $result['additional_discount_amount']  		= htmlentities($ctable_d['additional_discount_amount']);
	    $result['transport_charge']  				= htmlentities($ctable_d['transport_charge']);
	    // $result['tcs_amount']  					= htmlentities($ctable_d['tcs_amount']);
	    $result['packing_charge']  					= htmlentities($ctable_d['packing_charge']);
	    $result['lut_no']  							= htmlentities($ctable_d['lut_no']);
	    $result['chalan_no']  						= htmlentities($ctable_d['chalan_no']);
	    $result['po_no']  							= htmlentities($ctable_d['po_no']);
		
		// Purchase Item
		$reply=array("ack"=>1,"developer_msg"=>"Product Item detail fetched!!.","ack_msg"=>"Success! Product Item Edit Successfully.","result"=>$result);
		return $reply;
	}

	public function GetInquirynewItem($detail)
	{
		$where = " id='".$detail['id']."' AND isDelete=0";
		$dispatchid = $this->rp_getValue("packing_slip","dispatch_id",$where,0);
		// $order_id = $this->rp_getValue("dispatch_detail","order_id","id='".$dispatchid."'",0);
		$order_id = $this->rp_getValue("dispatch_detail","order_id","id='".$dispatchid."'",0);

		//$ctable_r = $this->db->rp_getData("order_product_item","*","order_id='".$order_id."'","",0);		
		/*Update 15-04-2022*/
		$ctable_item = $this->db->rp_getData("dispatch_item","*","dispatch_id='".$dispatchid."'","",0);		
		/*Update 15-04-2022*/
		//$ctable_item = $this->db->rp_getData("order_product_item","*","order_id='".$order_id."'","",0);	
		if($ctable_item)
		{
			while(	$ctable_item_d = mysqli_fetch_array($ctable_item))
			{
				$result_item=array();
				$result_item['product_id']			= htmlentities($ctable_item_d['pro_id']);
				$result_item['order_id']			= htmlentities($ctable_item_d['order_id']);
				$result_item['weight_id']			= htmlentities($ctable_item_d['weight_id']);
				$pro_name                           = $this->db->rp_getValue("product","name","id='".$ctable_item_d['pro_id']."'");	
				$size_name                          = $this->db->rp_getValue("weight","name","id='".$ctable_item_d['weight_id']."'");	
				$result_item['product_name']        = $size_name." ".$pro_name." ";
				//$result_item['qty']					= htmlentities($ctable_item_d['pro_qty']);
				/*Update 15-04-2022*/
				$result_item['qty']					= htmlentities($ctable_item_d['qty']);
				/*Update 15-04-2022*/
				$result_item['inner_size']			= htmlentities($ctable_item_d['inner_size']);
				$result_item['outer_size']			= htmlentities($ctable_item_d['outer_size']);
				$result_item['box']					= htmlentities($ctable_item_d['cartoon_qty']);
				$result_item['bag']					= $this->db->rp_num(htmlentities($ctable_item_d['box_qty']));
				$result_item['loose']				= $this->db->rp_num(htmlentities($ctable_item_d['loose_qty']));
				$result_item['discount_per']		= $this->db->rp_num(htmlentities($ctable_item_d['discount']));
				$result_item['product_price']		= $this->db->rp_num(htmlentities($ctable_item_d['unitprice']));
				$result_item['original_price']		= $this->db->rp_num(htmlentities($ctable_item_d['original_price']));
				$result_item['product_total']		= $this->db->rp_num(htmlentities($ctable_item_d['totalprice']));
				$result_item['discount_amount']		= $this->db->rp_num(htmlentities($ctable_item_d['discount_amount']));
				$result_item['pro_description']		= htmlentities($ctable_item_d['pro_description']);
				$result_item['stock']		        = $this->db->rp_getValue("product_weight_price","stock_qty","product_id='".$ctable_item_d['pro_id']."'");	
				$result_item['cd_amount']			= $this->db->rp_num(htmlentities($ctable_item_d['cash_discount_amount']));
				$result_item['ad_amount']			= $this->db->rp_num(htmlentities($ctable_item_d['additional_discount_amount']));
				$result_item['gst_amount_item']		= $this->db->rp_num(htmlentities($ctable_item_d['igst_amount']));
				$result_item['taxable_amount']		= $this->db->rp_num(htmlentities($ctable_item_d['taxable']));
				$result_item['sub_total']			= $this->db->rp_num(htmlentities($ctable_item_d['subtotal']));
				$result_item['gst']         		= $this->db->rp_getValue("product","igst","id='".$result_item['product_id']."' AND isDelete=0");
				$result_item['other_charge']		= $this->db->rp_num(htmlentities($ctable_item_d['other_charge']));
				$result_item['fright_charge']		= $this->db->rp_num(htmlentities($ctable_item_d['fright_charge']));

				$top_cat_id                         = $this->db->rp_getValue("product","tcid","id='".$ctable_item_d['pro_id']."'");	
				$result_item['top_cat_name'] 		= $this->db->rp_getValue("top_category_master","name","id='".$top_cat_id."' AND isDelete=0",0);
				$cat_id                         	= $this->db->rp_getValue("product","cid","id='".$ctable_item_d['pro_id']."'");	
				$result_item['category_name'] 		= $this->db->rp_getValue("category_master","name","id='".$cat_id."' AND isDelete=0",0);
				$result_item['cat_no']		    = $this->db->rp_getValue("product_weight_price","catno","product_id='".$result_item['product_id']."'");		 

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
	
}