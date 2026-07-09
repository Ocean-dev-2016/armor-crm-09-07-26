<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php");

$mode       = $_REQUEST['mode'];
$product_id = $_REQUEST['product_id'];
$weight_id 	= $_REQUEST['weight_id'];
$catno 		= $_REQUEST['catno'];	
$price 		= $_REQUEST['price'];	
$stock_qty 	= $_REQUEST['stock_qty'];
$min        = 0;		
$inner      = 0;				
$outer      = 1;	

if($mode=="add_product_varient")
{
	if(isset($product_id) && $product_id!="")
	{
		$check_duplicate=0;
		$check_duplicate = $db->rp_dupCheck("product_weight_price","catno = '".$catno."' AND isDelete=0",0);
		if($check_duplicate==0)
		{
			$rows1=array("weight_id","product_id","price","stock_qty","opening_stock_qty","min_qty","inner_size","outer_size","catno");

			$values1=array($weight_id,$product_id,$price,$stock_qty,$stock_qty,$min,$inner,$outer,$catno);

			$insert_id=$db->rp_insert("product_weight_price",$values1,$rows1,0);
			
			if($insert_id!=0)
			{
				$ack=array("ack"=>1,"ack_msg"=>"Data Added Successfully");
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Data Added Failed");
			}
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Duplicate Record Found.");
		}
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Something Went Wrong.Please Check Again");
	}
	echo json_encode($ack);
}
if($mode=="get_product_varient")
{
	?>
	<div class="col-sm-12 col-xs-12">
		<table>
			<thead class="portlet grey-cascade box1">
				<tr>
					<th>Variant</th>
					<th>Product code</th>
					<th>Price<br><span style="font-size:12px">(INR)</span></th>
					<th>Qty<br><span style="font-size:12px">(NOS)</span></th>
					<th>Stock<br><span style="font-size:12px">(NOS)</span></th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$pro_varient_R = $db->rp_getData("product_weight_price","*","product_id='".$_REQUEST['product_id']."' AND isDelete=0");
				if($pro_varient_R)
				{
					While($pro_varient_D = mysqli_fetch_array($pro_varient_R))
					{
						?>
							<tr>
								<td><?php echo $db->rp_getValue("weight","name","id='".$pro_varient_D['weight_id']."'") ?></td>
								<td><?php echo $pro_varient_D['catno'] ?></td>
								<td><?php echo $pro_varient_D['price'] ?></td>
								<td><?php echo $pro_varient_D['outer_size'] ?></td>
								<td><?php echo $pro_varient_D['stock_qty'] ?></td>
								<td><a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $pro_varient_D['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a></td>
							</tr>
						<?php
					}
				}
				else
				{
					?>
					<th>
						<td colspan="6" style="text-align:center;">No Data Availble</td>
					</th>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}
if($mode=="delete_product_varient")
{
	$id=$_REQUEST['id'];
	$delete=$db->rp_delete("product_weight_price","product_id='".$product_id."' AND id='".$id."'",0);
	if($delete)
	{
		$ack=array("ack"=>1,"ack_msg"=>"Product Variant Delete Successfully");
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Product Variant Delete Failed");
	}
	echo json_encode($ack);
}
require_once 'disconnect.php'; 
?>