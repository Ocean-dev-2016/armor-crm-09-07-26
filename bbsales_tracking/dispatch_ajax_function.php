<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");
require_once("../include/class.media.php");
require_once("../include/orders.class.php");
$media=new Media();
$ctableOrder="order_detail";
$ctableVehical="vehical";
$ctableEmployee="emp_personal_info";
$ctableOrderItem="order_item";
$ctableVehicalStock="vehical_map_stock";
$ctableStore="store_master";
$ctableDispatchInfo="dispatch_info";
$ctableDispatchItems="dispatch_item";
$media=new Media();
if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
{
	$mode=$_REQUEST['mode'];
    if($mode=="order_detail")
    {
		if(isset($_REQUEST['order_id']) && $_REQUEST['order_id']!="" && isset($_REQUEST['store_id']) && $_REQUEST['store_id']!="")
		{
			$order_id=$_REQUEST['order_id'];
			$store_id=$_REQUEST['store_id'];
			$order_detail=$db->rp_getData($ctableOrder,"*","id='".$order_id."' AND isDelete=0");
			if($order_detail){
				$order_detail=mysqli_fetch_assoc($order_detail);
				$order_items=$db->rp_getData($ctableOrderItem,"*","order_id='".$order_detail['id']."' AND order_item_remaining_qty>0");
				if($order_items){
					while($order_item=mysqli_fetch_assoc($order_items)){
						$products=$db->rp_getData("product","*","id='".$order_item['order_item_id']."'");
						$products=mysqli_fetch_assoc($products);
						$order_item['width']=$products['width'];
						$order_item['height']=$products['height'];
						$order_item['depth']=$products['depth'];
						$order_item['length']=$products['length'];
						$order_item['weight']=$products['weight'];
						$order_item['unit_name']=$products['unit_name'];
						$order_item['packaging_type']=$products['packaging_type'];
						$order_item['available_qty']=$db->rp_getValue("product_store_item","stock_qty","store_id='".$store_id."' AND product_id='".$order_item['order_item_id']."'",0);
						$image=$products['image']; 
						$info=$media->getMedia(array("mid"=>$image));
						if($info['ack']==1)
						{
							$media_url=$info['result']['url'];
						}
						$order_item['media_url']=$media_url;
						$order_detail['order_item'][]=$order_item;
					}
				}
				$response=array('ack'=>1,'ack_msg'=>'Order found!!',"result"=>$order_detail);
				echo json_encode($response);
			}
			else{
				$response=array('ack'=>0,'ack_msg'=>'Order detail not found');
				echo json_encode($response);
			}
			
		}
		else
		{				
			$response=array('ack'=>0,'ack_msg'=>'No Order found');
			echo json_encode($response);
		}
    }
	else if($mode=="approved_orders")
    {
		$order_detail=$db->rp_getData($ctableOrder,"*","(order_status=2 OR order_status=3 ) AND isDelete=0");
		if($order_detail){
			while($order=mysqli_fetch_assoc($order_detail))
			{
				$result[]=$order;	
			}
			$response=array('ack'=>1,'ack_msg'=>'Orders Found',"result"=>$result);
			echo json_encode($response);
		}
		else{
			$response=array('ack'=>0,'ack_msg'=>'No order to dispatch!!');
			echo json_encode($response);
		}
		
		
    }
	else if($mode=="stores")
    {
		$store_detail=$db->rp_getData($ctableStore,"*","isActive=1 AND isDelete=0");
		if($store_detail){
			while($store=mysqli_fetch_assoc($store_detail))
			{
				$result[]=$store;	
			}
			$response=array('ack'=>1,'ack_msg'=>'Srore Found',"result"=>$result);
			echo json_encode($response);
		}
		else{
			$response=array('ack'=>0,'ack_msg'=>'No store to dispatch!!');
			echo json_encode($response);
		}
		
		
    }
	else if($mode=="vehical")
    {
		$vehical_detail=$db->rp_getData($ctableVehical,"*","isDelete=0");
		if($vehical_detail){
			while($vehical=mysqli_fetch_assoc($vehical_detail))
			{
				$result[]=$vehical;
				
			}
			$response=array('ack'=>1,'ack_msg'=>'Vehical Found',"result"=>$result);
			echo json_encode($response);
		}
		else{
			$response=array('ack'=>0,'ack_msg'=>'No Vehical!!');
			echo json_encode($response);
		}
		
		
    }
	else if($mode=="driver")
    {
		$vehical_detail=$db->rp_getData($ctableEmployee,"*","isDelete=0");
		if($vehical_detail){
			while($vehical=mysqli_fetch_assoc($vehical_detail))
			{
				$result[]=$vehical;
				
			}
			$response=array('ack'=>1,'ack_msg'=>'Drivers Found',"result"=>$result);
			echo json_encode($response);
		}
		else{
			$response=array('ack'=>0,'ack_msg'=>'Drivers Vehical!!');
			echo json_encode($response);
		}
		
		
    }
	else if($mode=="preview_dispatch")
    {
		if(isset($_REQUEST['dispatch_items']) && $_REQUEST['order_id'])
		{
			$dispatch_items=$_REQUEST['dispatch_items'];
			$order_id=$_REQUEST['order_id'];
			$dispatch_date=$_REQUEST['dispatch_date'];
			// include('preview_dispatch.php');
			include('preview_dispatch_new.php');
			
		}
		else
		{
			?>
			No Preview
			<?php 
		}
		
		
		
    }
	else if($mode=="vehical_information")
    {
		if(isset($_REQUEST['vehical_id']) && $_REQUEST['vehical_id']!="")
		{
			$vehical_id=$_REQUEST['vehical_id'];
			$driver_id=$_REQUEST['driver_id'];
			$vehical_detail=$db->rp_getData($ctableVehical,"*","isDelete=0 AND id='".$vehical_id."'");
			if($vehical_detail){
				while($vehical=mysqli_fetch_assoc($vehical_detail))
				{
					$vehical_id=$vehical['id'];
					
					$driver_detail=$db->rp_getData($ctableEmployee,"*","id='".$driver_id."'","",0);
					$count=0;
					if($driver_detail)
					{
						$driver_detail=mysqli_fetch_assoc($driver_detail);
						$vehical['driver_detail']=array("driver_name"=>$driver_detail['first_name']." ".$driver_detail['last_name'],"driver_email"=>$driver_detail['email'],"driver_phone"=>$driver_detail['phone'],"driver_other_contact"=>$driver_detail['other_contact'],"driver_address_permenant"=>$driver_detail['perment_address'],"driver_address_residential"=>$driver_detail['residential_address']);
						
						// Vehical Stock
						
						/*$vehical['stock_item']=array();
						$vehical_stock_detail=$db->rp_getData($ctableVehicalStock,"*","vehical_id='".$vehical_id."'","",0);
						if($vehical_stock_detail)
						{
							while($stock=mysqli_fetch_assoc($vehical_stock_detail))
							{
								$product_detail=$db->rp_getData("product","*","id='".$stock['product_id']."'");
								if($product_detail)
								{
									$product_detail=mysqli_fetch_assoc($product_detail);
									$media_info=$media->getMedia(array("mid"=>$product_detail['image']));	
									if($media_info['ack']==1){
										$product_image=$media_info['result']['full_url'];
									}
									else{
										$product_image="";
									}	
									$product=array("product_name"=>$product_detail['product_name'],"product_image"=>$product_image,"product_id"=>$product_detail['id'],"product_qty"=>$stock['stock_qty'],"product_code"=>$product_detail['product_code']);
									$vehical['stock_item'][]=$product;
								}
								
							}
						}*/
						$html="";
						$TotalGrandTotal=0;

						$dispatches=$db->rp_getData($ctableDispatchInfo,"*","vehical_id='".$vehical_id."' AND status=1");
						$TotalUniquePremises=$db->rp_getValue($ctableDispatchInfo,"COUNT(DISTINCT(dispatch_cbid))","vehical_id='".$vehical_id."'");
						if($dispatches)
						{
							
							while($dispatch=mysqli_fetch_assoc($dispatches))
							{
								$dispatch['order_no']=$db->rp_getValue($ctableOrder,"order_no","id='".$dispatch['order_id']."'");
								$items='';
								$countitem=0;	
								$dispatch_items=$db->rp_getData($ctableDispatchItems,"*","dispatch_id='".$dispatch['id']."'","",0);
								if($dispatch_items)
								{
									$countitem++;
									while($item=mysqli_fetch_assoc($dispatch_items))
									{
										$items.=$countitem.'.<span data-id='.$item['dispatch_item_id'].' data-target="#product-information-modal" data-toggle="modal"><b>'.$item['dispatch_item_name'].'</b></span> X '.$item['dispatch_item_qty'].'<br/>';
									}
								}

								$dispatch_grandtotal=round($dispatch['dispatch_grandtotal'],2);	
								$TotalGrandTotal+=$dispatch_grandtotal;
								$count++;
								$html.='<tr>
										<td>
										'.$count.'
										</td>
										<td>
										<b> Order&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; # '.$dispatch['order_no'].'</b><br/>
										<b> Dispatch # '.$dispatch['dispatch_no'].'</b><br/>
										Name: '.$dispatch['dispatch_billing_name'].'<br/>
										Address: '.$dispatch['dispatch_billing_address'].'<br/>
										City: '.$dispatch['dispatch_billing_city'].'<br/>
										Postal Code:'.$dispatch['dispatch_billing_pincode'].'<br/>

										</td>
										<td>
										'.$items.'
										</td>
										<td>
										'.CURR." ".$dispatch_grandtotal.'
										</td>	
									</tr>';
							}
							

							
						}
						else
						{
							$html.='<tr>
										<td colspan="4" class="text-center">
										No Previous Dispatches Found
										</td>	
									</tr>';
						}
						$html.='<tr><td></td><td><td class="text-right bold">Payment To Be Collect</td><td>'.CURR." ".$TotalGrandTotal.'</td></tr>';
						$html.='<tr><td></td><td><td class="text-right bold">Total Dispatches </td><td>'.$count.'</td></tr>';
						$html.='<tr><td></td><td><td class="text-right bold">Total Premises </td><td>'.$TotalUniquePremises.'</td></tr>';
						$vehical['html']=html_entity_decode($html);
						
						
						$result=$vehical;	
					}
					
					
				}
				$response=array('ack'=>1,'ack_msg'=>'Vehical Found',"result"=>$result);
				echo json_encode($response);
			}
			else{
				$response=array('ack'=>0,'ack_msg'=>'No Vehical!!');
				echo json_encode($response);
			}
		}
		else
		{
				$response=array('ack'=>0,'ack_msg'=>'No Vehical!!');
				echo json_encode($response);
		}
		
		
    }
    else if($mode=="product_detail")
    {
		if(isset($_REQUEST['product_id']) && $_REQUEST['product_id'])
		{
			$product_id=$_REQUEST['product_id'];
			$store_id=$_REQUEST['store_id'];
			$order_detail=$db->rp_getData($ctableOrder,"*","id='".$product_id."' AND isDelete=0");
			if($order_detail){
				$order_detail=mysqli_fetch_assoc($order_detail);
				$result[]=$order_detail;
				$order_items=$db->rp_getData($ctableOrderItem,"*","product_id='".$order_detail['id']."'");
				if($order_items){
					while($order_item=mysqli_fetch_assoc($order_items)){
						
						$result['order_item']=$order_item;
					}
				}
			}
			else{
				$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!','result'=>$result);
				echo json_encode($response);
			}
			$response=array('ack'=>1,'ack_msg'=>'Dispatch order status changed!!');
			echo json_encode($response);
		}
		else
		{				
			$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
			echo json_encode($response);
		}
    }
    
	else if($mode=="return_item")
	{
		?>
		<option value="">Select Items</option>
		<?php 
		$branch_id=isset($_REQUEST['bid'])?$_REQUEST['bid']:"";
		if($branch_id!="")
		{
			
			$product_ids=array();
			$purchase_products=$db->rp_getData("order_item","*","cbid='".$branch_id."'","",0);
			if($purchase_products)
			{
				while($p=mysqli_fetch_assoc($purchase_products))
				{
					$product_ids[]=$p['order_item_id'];
				}
			}
		}
		if(!empty($product_ids))
		{
			$product_ids=implode(",",$product_ids);
			$product_list_d=$db->rp_getData('product',"*","isDelete=0 AND id IN(".$product_ids.")","",0);
			while($product_list_r=mysqli_fetch_assoc($product_list_d))
			{

			$media_info=$media->getMedia(array("mid"=>$product_list_r['image']));	
			if($media_info['ack']==1){
				$product_image=$media_info['result']['full_url'];
			}
			else{
				$product_image=Media::$DefaultImage;
			}
				
				?>
				<option data-sellprice="<?php echo $product_list_r['selling_price']; ?>"  data-stock_qty="<?php echo $product_list_r['stock_qty']; ?>" data-name="<?php echo $product_list_r['product_name']?>" data-image="<?php echo $product_image;?>" 
				data-width="<?php echo $product_list_r['width']?>"
				data-height="<?php echo $product_list_r['height']?>"
				data-depth="<?php echo $product_list_r['depth']?>"
				data-weight="<?php echo $product_list_r['weight']?>"
				data-packaging_type="<?php echo $product_list_r['packaging_type']?>"
				data-manufacture="<?php echo $product_list_r['manufacture']?>"
				
				data-purchase_price="<?php echo $product_list_r['purchase_price']?>"
				value="<?php echo $product_list_r['id'];?>" 
				>
				<?php echo $product_list_r['product_name']; ?>
				</option>
				<?php
			}
		}
		
	}
	else if($mode=="fetch_branch_product")
	{
		if(isset($_REQUEST['cbid']) && isset($_REQUEST['query']))
		{
			$query=$_REQUEST['query'];
			$branch_id=$_REQUEST['cbid'];
			$require_column=array("id","product_name","display_name","category_id","mrp_price","sku","selling_price","purchase_price","packaging_type","barcode_no","product_code","vat_tax","stock_qty","unit_name","warranty_period","created_date","image","vat_tax","other_tax","manufacture");
			$cid=0;
			$product_name="";
			$objOrder=new Order();
			$results=$objOrder->aj_getProductDetail($branch_id,"",$query,$require_column=array());
			
			if($results)
			{
				$html='';
				foreach($results as $product_detail)
				{
						$product_detail['value']=$product_detail['product_name'];
						$result[] 	= $product_detail;	
				}
				
				$reply=array("ack"=>"1","ack_msg"=>"Product found","result"=>$result);
				echo json_encode($reply);
				
			}
			else{
				$reply=array("ack"=>"0","ack_msg"=>"No Product Found For Customer");
					echo json_encode($reply);
			}
		}
		else
		{
			$reply=array("ack"=>"0","ack_msg"=>"No Product Found For Customer");
			echo json_encode($reply);
		}
	}
	else if($mode=="get_product_information")
	{
		$product_id=$_REQUEST['id'];
		include('product_information.php');
	}
	else if($mode=="check_slab_date")
	{
		if(isset($_REQUEST['date']) && $_REQUEST['date']!="")
		{
			$date=$_REQUEST['date'];
			$date=explode("to",$date);
			$min_date=$date[0];
			$max_date=$date[1];
			$count=$db->rp_getTotalRecord("sales_commission","min_date<='".date("Y-m-d",strtotime($min_date))."' AND max_date>='".date("Y-m-d",strtotime($max_date))."' AND isDelete=0",0);
			if($count<=0)
			{
				 $response=array('ack'=>1,'ack_msg'=>'Slot Available');
       			 echo json_encode($response);
			}
			else
			{
				 $response=array('ack'=>0,'ack_msg'=>'Slot Not Available');
       			 echo json_encode($response);
			}
		}
		else
		{
			$response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
        	echo json_encode($response);
		}

	}
	else if($mode=="warhouse_checklist")
    {
		if(isset($_REQUEST['dateFilter']) && $_REQUEST['dateFilter']!="" && isset($_REQUEST['warhouse']) && $_REQUEST['warhouse']!="")
		{
			$dispatch_detail=array();
			$dateFilter=$_REQUEST['dateFilter'];
			$warhouse_id=$_REQUEST['warhouse'];
			$dateFilter=explode("to",$dateFilter);
			$FromDate=date("Y-m-d",strtotime(trim($dateFilter[0])));
			$ToDate=date("Y-m-d",strtotime(trim($dateFilter[1])));
			$ready_to_dispatch_detail=$db->rp_getData($ctableDispatchInfo,"id","dispatch_date>='".$FromDate."' AND dispatch_date<='".$ToDate."' AND  store_id='".$warhouse_id."' AND isDelete=0","",0);
			$dispatches=array();
			if($ready_to_dispatch_detail){
				while($dispatch=mysqli_fetch_assoc($ready_to_dispatch_detail))
				{
					$dispatches[]=$dispatch['id'];
				}
				$dispatches=implode(",",$dispatches);
				$dispatch_items=$db->rp_getData($ctableDispatchItems,"dispatch_item_name,dispatch_item_id,SUM(dispatch_item_qty) as qty","dispatch_id IN (".$dispatches.") GROUP BY dispatch_item_id ","",0);
				if($dispatch_items){
					while($order_item=mysqli_fetch_assoc($dispatch_items)){
						$products=$db->rp_getData("product","*","id='".$order_item['dispatch_item_id']."'");
						$products=mysqli_fetch_assoc($products);
						$order_item['item_code']=$products['product_code'];
						$order_item['width']=$products['width'];
						$order_item['height']=$products['height'];
						$order_item['depth']=$products['depth'];
						$order_item['length']=$products['length'];
						$order_item['weight']=$products['weight'];
						$order_item['unit_name']=$products['unit_name'];
						$order_item['packaging_type']=$products['packaging_type'];
						
						$image=$products['image']; 
						$info=$media->getMedia(array("mid"=>$image));
						if($info['ack']==1)
						{
							$media_url=$info['result']['url'];
						}
						$order_item['media_url']=$media_url;
						$dispatch_detail[]=$order_item;
					}
				}
				$response=array('ack'=>1,'ack_msg'=>'Dispatches found!!',"result"=>$dispatch_detail);
				echo json_encode($response);
			}
			else{
				$response=array('ack'=>0,'ack_msg'=>'Dispatches detail not found');
				echo json_encode($response);
			}
			
		}
		else
		{				
			$response=array('ack'=>0,'ack_msg'=>'No Dispatches found');
			echo json_encode($response);
		}
    }
    else if($mode=="preview_warhouse_checklist")
    {
    	$dateFilter=$_REQUEST['dateFilter'];
    	$dateFilter=explode("to",$dateFilter);
		$_REQUEST['FromDate']=date("Y-m-d",strtotime(trim($dateFilter[0])));
		$_REQUEST['ToDate']=date("Y-m-d",strtotime(trim($dateFilter[1])));
		$warhouse_id=$_REQUEST['warhouse'];
		include('preview_warhouse_checklist.php');
		
    } 
    else if($mode=="warhouse_checklist_download")
    {
    	$file=$dateFilter=$_REQUEST['dateFilter'];
    	$dateFilter=explode("to",$dateFilter);
		$FromDate=date("Y-m-d",strtotime(trim($dateFilter[0])));
		$ToDate=date("Y-m-d",strtotime(trim($dateFilter[1])));
		$warhouse_id=$_REQUEST['warhouse'];
		// ADMINSITEURL.'preview_warhouse_checklist.php?warhouse='.$warhouse_id."&FromDate=".$FromDate."&ToDate=".$ToDate;
		$content=file_get_contents(ADMINSITEURL.'preview_warhouse_checklist.php?warhouse='.$warhouse_id."&FromDate=".$FromDate."&ToDate=".$ToDate);
		require('mpdf60/mpdf.php');
		$mpdf = new mPDF('',    // mode - default ''

	 'A4',    // format - A4, for example, default ''

	 15,     // font size - default 0

	 'sans-serif',    // default font family

	 3,    // margin_left

	 3,    // margin right

	 3,     // margin top

	 3,    // margin bottom

	 0,     // margin header

	 0,     // margin footer

	 'P');  // L - landscape, P - portrait

			$mpdf->WriteHTML($content);
		$fileName = $file;

	if(!is_dir($fileName)){

		mkdir(WARHOUSE_CHECKLIST.$fileName);

	}

	$pdf_file_path	= WARHOUSE_CHECKLIST.$fileName.'.pdf';	
	$mpdf->Output($pdf_file_path);
	 $response=array('ack'=>1,'ack_msg'=>'Download Ready!!',"result"=>ADMINSITEURL.$pdf_file_path);
        echo json_encode($response);
    }





    else if($mode=="driver_checklist")
    {
		if(isset($_REQUEST['dateFilter']) && $_REQUEST['dateFilter']!="" && isset($_REQUEST['driver']) && $_REQUEST['driver']!="")
		{
			$dispatch_detail=array();
			$dateFilter=$_REQUEST['dateFilter'];
			$driver_id=$_REQUEST['driver'];
			$dateFilter=explode("to",$dateFilter);
			$FromDate=date("Y-m-d",strtotime(trim($dateFilter[0])));
			$ToDate=date("Y-m-d",strtotime(trim($dateFilter[1])));
			$vehical_id=$db->rp_getValue("vehical","id","driver_id='".$driver_id."'");
			$ready_to_dispatch_detail=$db->rp_getData($ctableDispatchInfo,"id","dispatch_date>='".$FromDate."' AND dispatch_date<='".$ToDate."' AND status=1 AND driver_id='".$driver_id."' AND isDelete=0","",0);
			$dispatches=array();
			if($ready_to_dispatch_detail){
				while($dispatch=mysqli_fetch_assoc($ready_to_dispatch_detail))
				{
					$dispatches[]=$dispatch['id'];
				}
				$dispatches=implode(",",$dispatches);
				$dispatch_items=$db->rp_getData($ctableDispatchItems,"dispatch_item_name,dispatch_item_id,SUM(dispatch_item_qty) as qty","dispatch_id IN (".$dispatches.") GROUP BY dispatch_item_id ","",0);
				if($dispatch_items){
					while($order_item=mysqli_fetch_assoc($dispatch_items)){
						$products=$db->rp_getData("product","*","id='".$order_item['dispatch_item_id']."'");
						$products=mysqli_fetch_assoc($products);
						$order_item['item_code']=$products['product_code'];
						$order_item['width']=$products['width'];
						$order_item['height']=$products['height'];
						$order_item['depth']=$products['depth'];
						$order_item['length']=$products['length'];
						$order_item['weight']=$products['weight'];
						$order_item['unit_name']=$products['unit_name'];
						$order_item['packaging_type']=$products['packaging_type'];
						
						$image=$products['image']; 
						$info=$media->getMedia(array("mid"=>$image));
						if($info['ack']==1)
						{
							$media_url=$info['result']['url'];
						}
						$order_item['media_url']=$media_url;
						$dispatch_detail[]=$order_item;
					}
				}
				$response=array('ack'=>1,'ack_msg'=>'Dispatches found!!',"result"=>$dispatch_detail);
				echo json_encode($response);
			}
			else{
				$response=array('ack'=>0,'ack_msg'=>'Dispatches detail not found');
				echo json_encode($response);
			}
			
		}
		else
		{				
			$response=array('ack'=>0,'ack_msg'=>'No Dispatches found');
			echo json_encode($response);
		}
    }
    else if($mode=="preview_driver_checklist")
    {
    	$dateFilter=$_REQUEST['dateFilter'];
    	$dateFilter=explode("to",$dateFilter);
		$_REQUEST['FromDate']=date("Y-m-d",strtotime(trim($dateFilter[0])));
		$_REQUEST['ToDate']=date("Y-m-d",strtotime(trim($dateFilter[1])));
		$driver_id=$_REQUEST['driver'];
		include('preview_driver_checklist.php');
		
    } 
    else if($mode=="driver_checklist_download")
    {
    	$file=$dateFilter=$_REQUEST['dateFilter'];
    	$dateFilter=explode("to",$dateFilter);
		$FromDate=date("Y-m-d",strtotime(trim($dateFilter[0])));
		$ToDate=date("Y-m-d",strtotime(trim($dateFilter[1])));
		$driver_id=$_REQUEST['driver'];
		// ADMINSITEURL.'preview_warhouse_checklist.php?warhouse='.$warhouse_id."&FromDate=".$FromDate."&ToDate=".$ToDate;
		$content=file_get_contents(ADMINSITEURL.'preview_driver_checklist.php?driver='.$driver_id."&FromDate=".$FromDate."&ToDate=".$ToDate);
		require('mpdf60/mpdf.php');
		$mpdf = new mPDF('',    // mode - default ''

	 'A4',    // format - A4, for example, default ''

	 15,     // font size - default 0

	 'sans-serif',    // default font family

	 3,    // margin_left

	 3,    // margin right

	 3,     // margin top

	 3,    // margin bottom

	 0,     // margin header

	 0,     // margin footer

	 'P');  // L - landscape, P - portrait

			$mpdf->WriteHTML($content);
		$fileName = $file;

	if(!is_dir($fileName)){

		mkdir(DRIVER_CHECKLIST.$fileName);

	}

	$pdf_file_path	= DRIVER_CHECKLIST.$fileName.'.pdf';	
	$mpdf->Output($pdf_file_path);
	 $response=array('ack'=>1,'ack_msg'=>'Download Ready!!',"result"=>ADMINSITEURL.$pdf_file_path);
        echo json_encode($response);
    }
	else
    {
        $response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
        echo json_encode($response);
    }
}
else
{
    $response=array('ack'=>0,'ack_msg'=>'Something went wrong Try Again!!');
    echo json_encode($response);
}
?>
<?php require_once 'disconnect.php';  ?>