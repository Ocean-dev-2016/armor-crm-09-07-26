<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$ctable 	= "orders";
if(isset($_REQUEST['mode']) && $_REQUEST['mode']!="")
{
	$service=$_REQUEST['mode'];
	//echo $_REQUEST['mode'];exit;
	if($service=="order_data")
	{
		$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
		$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
		$order_data_r=$db->rp_getData("orders","*","MONTH(order_date)='".$order_month."' AND YEAR(order_date)='".$order_year."' AND isDelete=0 LIMIT 10","",0);
		
		?>
		<table class="table table-hover table-light">
				<thead>
					<tr class="uppercase">
						<th colspan="2"> Order </th>
						<th colspan="2"> Customer Name </th>
						<th style="text-align:right"> Order Amount  </th>                                                       
					</tr>
				</thead>
				<tbody>
				<?php 
					while($order_data_d=mysqli_fetch_assoc($order_data_r))
					{
						$customer=$db->rp_getValue('executive','isActive',"id=".$order_data_d['customer_id']."",0);
						if($customer==0)
						{
							continue;
						}
						$where=" MONTH(order_date)='".$order_month."' AND YEAR(order_date)='".$order_year."' AND id='".$order_data_d['id']."'";
						$sales_data=$db->rp_getData("orders","grand_total,id,customer_name,order_no, order_date",$where,"",0);
						if($sales_data)
						{
							$sales_data=mysqli_fetch_assoc($sales_data);
							$order_date=(date('d-m-Y',strtotime($sales_data['order_date'])))?$sales_data['order_date']:00-00-0000;
						}
						else
						{
						}
						//$size=$db->rp_getValue("messurement","name","id='".$order_data_d['dia']."'",0);
				?>
				<tr>				
					<td class="fit">
						<img class="user-pic rounded" src="assets/global/img/avatar.png"> </td>
					<td>
					<?php  
					$file_path="order_viewer.php?order_id=".$order_data_d['id']."";
					?>
					<a class="" target="_blank" href="<?php echo $file_path;?>"  title="save"><?php  echo $sales_data['order_no']; ?> </a> </td>
					<td> 
					<?php
					echo $sales_data['customer_name'];?> 
					</td>
					<td align="right" colspan="2"> 
					<?php
					echo CURR.$db->rp_num($sales_data['grand_total']);?> 
					</td>
					
				   
				</tr>
					<?php }?>
				</tbody>
			  </table>
		<?php
		
		
	}
	else if($service=="product_item_data")
	{
		$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
		
		$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
		
		$order_product_name=isset($_REQUEST['order_product_name'])?$_REQUEST['order_product_name']:$db->clean($_REQUEST['order_product_name']);
		
		$customer_id=isset($_REQUEST['customer_id'])?$_REQUEST['customer_id']:$db->clean($_REQUEST['customer_id']);
		
		$sales_id=isset($_REQUEST['sales_id'])?$_REQUEST['sales_id']:$db->clean($_REQUEST['sales_id']);
		
		$Where = "";
		if($sales_id!="" && $customer_id!="")
		{
			$Where.= "sales_id='".$sales_id."' AND customer_id='".$customer_id."'";	
		}

		else if($customer_id!="")
		{
			$Where.= "customer_id='".$customer_id."' ";
		}
		
		else if($sales_id!="")
		{
			$Where.= "sales_id='".$sales_id."' ";
		}
		
		$order_id=$db->rp_getData("orders","*",$Where,"",0);
		
		$order=array();
		
		while($orders=mysqli_fetch_assoc($order_id))
		{
			$order[]=$orders['id'];
		}
		
		$final_order = implode(",",$order);
		
		if($_REQUEST['order_product_name']!=""){
			$where="isDelete=0 AND pro_name like '%".$order_product_name."%'  GROUP BY weight_id,pro_id";
			$fg_item_data_r=$db->rp_getData("order_product_item","order_id,SUM(pro_qty) as pro_qty,pro_name, SUM(totalprice) as totalprice,unitprice",$where,"",0);
		}
		
		else if($customer_id!="")
		{
			
			$where ="order_id IN (".$final_order.") AND isDelete=0 ";
			$fg_item_data_r=$db->rp_getData("order_product_item","order_id,pro_qty,pro_name,totalprice,unitprice",$where,"",0);
		}

		else if($sales_id!="")
		{
			
			$where ="order_id IN (".$final_order.") AND isDelete=0 ";
			$fg_item_data_r=$db->rp_getData("order_product_item","order_id,pro_qty,pro_name,totalprice,unitprice",$where,"",0);
		}

		else{
			$where="isDelete=0 AND MONTH(created_date)='".$order_month."' AND YEAR(created_date)='".$order_year."' GROUP BY weight_id,pro_id";
			$fg_item_data_r=$db->rp_getData("order_product_item","order_id,SUM(pro_qty) as pro_qty,pro_name, SUM(totalprice) as totalprice,unitprice",$where,"",0);
		
		}
		//$fg_item_data_r=$db->rp_getData("orders","*","isDelete=0","id DESC limit 10",0);
		
		
			?>
				<table class="table table-hover table-light">
				<thead>
					<tr class="uppercase">
						<th></th>
						<th>Order No </th>
						<th>Sales Name </th>
						<th>Customer Name </th>
						<th>Product Name </th>
						<th style="text-align:right"> Order Qty  </th>
						<th style="text-align:right"> Amount </th>
						                                                       
					</tr>
				</thead>
				<tbody>
				<?php 
				if($fg_item_data_r)
				{
					while($sales_data=mysqli_fetch_assoc($fg_item_data_r))
					{
						$qty=($sales_data['pro_qty'])?$sales_data['pro_qty']:0;
						$totalprice=($sales_data['totalprice'])?$sales_data['totalprice']:0;
						$sales_id = $db->rp_getValue("orders","sales_id","id='".$sales_data['order_id']."'")
				?>
				<tr>				
					<td class="fit"><img class="user-pic rounded" src="assets/global/img/avatar.png"> </td>
					<td><?php echo $db->rp_getValue("orders","order_no","id='".$sales_data['order_id']."'");?></td>
					<td><?php echo $db->rp_getValue("sales_executive","name","id='".$sales_id."'");?></td>
					<td><?php echo $db->rp_getValue("orders","company_name","id='".$sales_data['order_id']."'");?></td>
					<td><?php echo  $sales_data['pro_name'];?></td>
					<td> <?php  echo $sales_data['pro_qty']; ?> </td>
					<td align="right"> <?php  echo CURR.$db->rp_num($sales_data['totalprice']); ?> </td>
					
				   
				</tr>
					<?php }
				}else{?>
					<tr>
						<td style="text-align:center;" colspan="8">No Products Order Available</td>
						</tr>
				</tbody>
			  </table>
			<?php
		}
		
			//$response=array('ack'=>0,'ack_msg'=>'Branch name can not be empty !!!');
			//echo json_encode($response);
	}
	else if($service=="sales_item_data")
	{
		$order_month=isset($_REQUEST['month'])?$_REQUEST['month']:date("m");
		$order_year=isset($_REQUEST['year'])?$_REQUEST['year']:date("Y");
		$product_name=isset($_REQUEST['product_name'])?$_REQUEST['product_name']:$db->clean($_REQUEST['product_name']);
		if($_REQUEST['product_name']!=""){
			 $where="isDelete=0 AND name like '%".$db->clean($product_name)."%'";
			// $where="isDelete=0 AND name = '".$product_name."'";
		}
		else{
			$where="isDelete=0";
		}
		
		$fg_item_data_r=$db->rp_getData("product","*",$where,"id DESC limit 10",0);
		
			?>
				<table class="table table-hover table-light">
				<thead>
					<tr class="uppercase">
						<th colspan="2"> Minimum Qty Alert </th>
						<th style="text-align:right"> Weight </th>                                                       
						<th style="text-align:left" > Category </th>                                                       
						<th style="text-align:right"> Price </th>                                                       
						<th style="text-align:right"> Quantity </th>                                                       
					</tr>
				</thead>
				<tbody>
				<?php 
				if($fg_item_data_r)
				{
					$check_stock=0;
					while($fg_item_data=mysqli_fetch_assoc($fg_item_data_r))
					{
						$minimum_stock_qty = $stock_qty=$db->rp_getValue("product_weight_price","min_qty","product_id='".$fg_item_data['id']."'",0);
						$stock_qty = $fg_item_data['stock_qty'];
						$stock_qty=$db->rp_getValue("product_weight_price","stock_qty","product_id='".$fg_item_data['id']."'",0);
						//$stock_qty = $fg_item_data['stock_qty'];
            
						
						 if($minimum_stock_qty>=$stock_qty)
						 {
						 	$check_stock++;
							//$size=$db->rp_getValue("messurement","name","id='".$fg_item_data['dia']."'",0);
							$category_name=$db->rp_getValue("category_master","name","id='".$fg_item_data['cid']."'",0);
							$weights=$db->rp_getValue("product_weight_price","weight_id","product_id='".$fg_item_data['id']."'","",0);
							$weight=$db->rp_getValue("weight","name","id='".$weights."'");
							$price=$db->rp_getValue("product_weight_price","price","product_id='".$fg_item_data['id']."'");
						?>
						<tr>				
							<td class="fit">
								<img class="user-pic rounded" src="assets/global/img/avatar.png"> </td>
							<td>
								<?php
									echo  $fg_item_data['name'];
								?>
							</td>
							<td align="right"> <?php  echo $weight; ?> </td>
							<td align="left"> <?php  echo $category_name; ?> </td>
						   <td align="right"> <?php  echo $price; ?> </td>
						   <td align="right"> <?php  echo $stock_qty; ?> </td>
						</tr>
					<?php }
					}
					if($check_stock<=0)
					{
						?>
						<tr>
							<td style="text-align:center;" colspan="6">No Data Available</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			  </table>
			<?php
		}
		else
		{
			// $response=array('ack'=>0,'ack_msg'=>'product name can not be empty !!!');
			
			//echo json_encode($response);

			?>
			<tr>
				<td style="text-align:center;" colspan="6">No Data Available</td>
			</tr>
			<?php
		}
		
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