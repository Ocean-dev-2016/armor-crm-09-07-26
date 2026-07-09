<?php 
$page_id    = 612;
$page_slug  = 'packing_slip';
include("connect.php");
$ctable     = "packing_slip_item";
$mode=$_REQUEST['mode'];

$packing_type=$_REQUEST['packing_type'];
$product_id = $_REQUEST['product_id'];
$qty  = $_REQUEST['qty'];
$weight  = $_REQUEST['weight'];
$packing_id  = $_REQUEST['packing_id'];
$product_name  = $_REQUEST['product_name'];
$weight_id  = $_REQUEST['weight_id'];
$dispatch_id  = $_REQUEST['dispatch_id'];
$cartoon_no  = $_REQUEST['cartoon_no'];

$main_carton_type = $packing_type;
$main_carton_type_name = $db->rp_getValue("packing_type","name","id='".$packing_type."' AND isDelete=0",0);
$main_carton_type_weight = $db->rp_getValue("packing_type","weight","id='".$packing_type."' AND isDelete=0");
$main_carton_whole_actual_weight = $weight+$main_carton_type_weight;
$pro_weight = $weight;

if($mode=="add_packing_slip")
{

			$size_inner=$db->rp_getValue("product_weight_price","size_inner","product_id='".$product_id."' AND weight_id='".$weight_id."'",0);
			$inner_cft=$db->rp_getValue("product_weight_price","inner_cft","product_id='".$product_id."' AND weight_id='".$weight_id."'",0);
			$size_outer=$db->rp_getValue("product_weight_price","size_outer","product_id='".$product_id."' AND weight_id='".$weight_id."'",0);
			$outer_cft=$db->rp_getValue("product_weight_price","outer_cft","product_id='".$product_id."' AND weight_id='".$weight_id."'",0);
			$inner_cbm=$db->rp_getValue("product_weight_price","inner_cbm","product_id='".$product_id."' AND weight_id='".$weight_id."'",0);
			$outer_cbm=$db->rp_getValue("product_weight_price","outer_cbm","product_id='".$product_id."' AND weight_id='".$weight_id."'",0);
			if($main_carton_type==1)
			{
				if($size_inner!="" || $inner_cbm!="" )
				{
					$proname_new = "SIZE ".$size_inner ."/ CBM ".$inner_cbm;
				}
				else
				{
					$proname_new = "SIZE / CBM";
				}
			}
			else if($main_carton_type==2)
			{
				if($size_inner!="" || $inner_cbm!="" )
				{
					// echo "test";exit;
					$proname_new = "SIZE ".$size_outer ." / CBM ". $outer_cbm;
				}
				else
				{
					$proname_new = "SIZE / CBM";
				}
			}
			else
			{
				
					$proname_new = "SIZE / CBM ";
				
			}
	$count = $db->rp_getTotalRecord("packing_slip","id='".$packing_id."' AND isDelete=0",0);
	if($count>0)
	{

		$product_name1 = $db->rp_getValue("product","name","id='".$product_id."' AND isDelete=0");
		$catno = $db->rp_getValue("product_weight_price","catno","product_id='".$product_id."' AND isDelete=0");

		$new_pro_name = $product_name1." (#".$catno.")";

		$insert_keys = array("packing_slip_id","main_carton_type","main_carton_type_name","main_carton_type_count","main_carton_type_weight","main_carton_whole_actual_weight","pro_name","pro_id","weight_id","pro_qty","pro_weight","isDelete","isActive","item_size_cft","value_cft");
		$insert_value = array($packing_id,$packing_type,addslashes($main_carton_type_name),$cartoon_no,$main_carton_type_weight,$main_carton_whole_actual_weight,addslashes($new_pro_name),$product_id,$weight_id,$qty,$weight,0,1,$proname_new,$inner_cft);

		$insert = $db->rp_insert("packing_slip_item",$insert_value,$insert_keys,0);
		if($insert!=0)
		{

			/*update view status*/
			$dispatch_id  = $_REQUEST['dispatch_id'];
			$dispatch_id = implode(",",$dispatch_id);
			$update_status = $db->rp_update("packing_slip",array("grid_view_status"=>"1","dispatch_id"=>$dispatch_id),"id='".$packing_id."'");
			/*update view status*/

			$get_current_stock=$db->get_available_stock($product_id,$weight_id);
			$current_stock=$get_current_stock;

			// $current_stock=$db->rp_getValue("product_weight_price","stock_qty","product_id='".$product_id."' AND weight_id='".$weight_id."'");

			$remaining_stock_qty=$current_stock-$qty;

			$update = $db->rp_update("product_weight_price",array("stock_qty"=>$remaining_stock_qty),"product_id='".$product_id."' AND weight_id='".$weight_id."'",0);

			if($_REQUEST['dispatch_id'] != ""){

				$dispatch_id =  implode(",", $_REQUEST['dispatch_id']);
				$upadateid = $db->rp_update("dispatch_detail",array("status"=>2),"id IN (".$dispatch_id.")",0);
			}

			if($update)
			{
				$ack=array("ack"=>1,"ack_msg"=>"Data Added Successfully...");
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Data Added Failed...");
			}
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Something Went Wrong...");
		}
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"No Such Data Found...");
	}
	echo json_encode($ack);
}
if($mode=="delete_packing_Slip")
{
	$id=$_REQUEST['id'];
	
	$packingItemR = $db->rp_getData("packing_slip_item","*","id='".$id."' AND isDelete=0","",0);
	$packingItemD = mysqli_fetch_array($packingItemR);

	$delete=$db->rp_delete("packing_slip_item","id='".$id."'");
	if($delete)
	{
		/*update main stock*/
		$get_old_stock_qty = $db->rp_getValue("product_weight_price","stock_qty","product_id='".$packingItemD['pro_id']."' AND weight_id='".$packingItemD['weight_id']."' AND isDelete=0");
		
		$new_update_stock = $get_old_stock_qty + $packingItemD['pro_qty'];
		
		$update = $db->rp_update("product_weight_price",array("stock_qty"=>$new_update_stock),"product_id='".$packingItemD['pro_id']."' AND weight_id='".$packingItemD['weight_id']."'",0);	
		
		/*update main stock*/
		$ack=array("ack"=>1,"ack_msg"=>"Data Delete Successfully");
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Data Delete Failed");
	}
	echo json_encode($ack);
}
if($mode=="add_packing_remark")
{
	$count = $db->rp_getTotalRecord("packing_slip","id='".$packing_id."' AND isDelete=0",0);
	if($count>0)
	{
		$remark = $_REQUEST['remark'];
		$Update_Rows = array("remark"=>$remark);
		$updated_id = $db->rp_update("packing_slip",$Update_Rows,"id='".$packing_id."'",0);
		if($updated_id)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Remark Add Successfully..");		
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Remark Added Failed..");			
		}
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"No Such Data Found...");
	}
	echo json_encode($ack);
}
if($mode=="get_packing_item")
{
?>
<table class="table table-borderd" border="1">
	<thead>
		<tr>
			<th class="text-center" colspan="6">Client Name: <span class="set-client-name"></span></th>
		</tr>
		<tr>
			<th style="width: 10%">Delete</th>
			<th style="width: 10%">Sr. No.</th>
			<th style="width: 10%">cartoon. No.</th>
			<th style="width: 20%">Item</th>
			<th style="width: 25%">Packing Qty</th>
			<th style="width: 25%">KGs</th>
		</tr>
	</thead>
	<tbody class="main-dipatch-details-body">
		<?php
		$getPackingSplipItemDataR = $db->rp_getData("packing_slip_item","*","isDelete = 0 AND isActive = 1 AND packing_slip_id = '".$_REQUEST['packing_id']."' GROUP BY main_carton_type_count","",0);
		//$getPackingSplipItemDataR = $db->rp_getData("packing_slip_item","*","isDelete = 0 AND isActive = 1 AND packing_slip_id = '".$_REQUEST['packing_id']."' ","",1);
		$getPackingSplipItemData = array();
		$display_count = 0;
		while($getPackingSplipItemDataComp = mysqli_fetch_assoc($getPackingSplipItemDataR))
		{
			$getPackingSplipItemData[] = $getPackingSplipItemDataComp;
		}
		foreach ($getPackingSplipItemData as $item_key => $item_value) 
		{
			$getPackingSplipItemDataFullR = $db->rp_getData("packing_slip_item","*","isDelete = 0 AND isActive = 1 AND packing_slip_id = '".$_REQUEST['packing_id']."' AND main_carton_type_count='".$item_value['main_carton_type_count']."'","",0);
			$getPackingSplipItemDataFull = array();
			while($getPackingSplipItemDataFullComp = mysqli_fetch_assoc($getPackingSplipItemDataFullR))
			{
				$getPackingSplipItemDataFull[] = $getPackingSplipItemDataFullComp;
			}
			$count = 1;
			$TOTALQTY = 0;
			$TOTALWEIGHT = 0;
			foreach ($getPackingSplipItemDataFull as $item_full_key => $item_full_value) 
			{
				$TOTALQTY += $item_full_value['pro_qty'];
				$TOTALWEIGHT += $item_full_value['pro_weight'];
				?>
				<tr>
					<td>
						<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $item_full_value['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>				
					</td>
					<td><?= ++$display_count ?></td>
					<td><?=$item_value['main_carton_type_name']?> No : </b><?=$item_value['main_carton_type_count']?></td>
					<td><?=$item_full_value['pro_name'];?></td>
					<td><?=$item_full_value['pro_qty']?></td>
					<!-- <td><?= number_format($item_full_value[''],3);?></td>  -->
					<td><input type="text" class="form-control" id="pro_weight" value="<?= number_format($item_full_value['pro_weight'],3); ?>" onchange="UpdateProductWeight(this.value,<?php echo $item_full_value['id']; ?>)"></td>
				</tr>
				<?php
				$TOTALQTY += 0;
			}
			$TOTALWEIGHT += $item_full_value['main_carton_type_weight'];
			$MAINTOTALQTY += $TOTALQTY;
			$MAINTOTALWEIGHT += $TOTALWEIGHT;
			?>


			<?php
			$dispatch_id = $db->rp_getValue("packing_slip","dispatch_id","id='".$_REQUEST['packing_id']."' AND isDelete=0");

			$order_id = $db->rp_getValue("dispatch_item","order_id","dispatch_id='".$dispatch_id."' AND isDelete=0",0);

			// $dispatch_inner_size = $db->rp_getValue("dispatch_item","inner_size","dispatch_id='".$dispatch_id."' AND isDelete=0");
			// $dispatch_outre_size = $db->rp_getValue("dispatch_item","outer_size","dispatch_id='".$dispatch_id."' AND isDelete=0");

			$dispatch_inner_size = $db->rp_getValue("order_product_item","box_qty","order_id='".$order_id."' AND pro_id='".$item_full_value['pro_id']."' AND weight_id='".$item_full_value['weight_id']."' AND isDelete=0",0);
			$dispatch_outre_size = $db->rp_getValue("order_product_item","cartoon_qty","order_id='".$order_id."' AND pro_id='".$item_full_value['pro_id']."' AND weight_id='".$item_full_value['weight_id']."' AND isDelete=0",0);

			$size_inner=$db->rp_getValue("product_weight_price","size_inner","product_id='".$item_full_value['pro_id']."' AND weight_id='".$item_full_value['weight_id']."'",0);
			$inner_cft=$db->rp_getValue("product_weight_price","inner_cft","product_id='".$item_full_value['pro_id']."' AND weight_id='".$item_full_value['weight_id']."'",0);
			$size_outer=$db->rp_getValue("product_weight_price","size_outer","product_id='".$item_full_value['pro_id']."' AND weight_id='".$item_full_value['weight_id']."'",0);
			$outer_cft=$db->rp_getValue("product_weight_price","outer_cft","product_id='".$item_full_value['pro_id']."' AND weight_id='".$item_full_value['weight_id']."'",0);
			$inner_cbm=$db->rp_getValue("product_weight_price","inner_cbm","product_id='".$item_full_value['pro_id']."' AND weight_id='".$item_full_value['weight_id']."'",0);
			$outer_cbm=$db->rp_getValue("product_weight_price","outer_cbm","product_id='".$item_full_value['pro_id']."' AND weight_id='".$item_full_value['weight_id']."'",0);


			// if($item_full_value['item_size_cft']!="")
			// {
			// 	$proname_new = $item_full_value['item_size_cft'];
			// }

			// else if($dispatch_inner_size!=0 && $dispatch_outre_size==0)
			// {

			// 	$proname_new = "SIZE ".$size_inner ." / CFT ". $inner_cft;
			// }
			// else
			// {
			// 	$proname_new = "SIZE ".$size_outer ." / CFT ". $outer_cft;
			// }
         // if($item_full_value['item_size_cft']=="")
         // {
			if($item_full_value['main_carton_type']==1)
			{
				if($item_full_value['item_size_cft']!="")
				{
					$proname_new = $item_full_value['item_size_cft'];	
				}
				else if($size_inner!="" || $inner_cft!="" || $inner_cbm!="" )
				{
					// if($size_inner != "")
					// {
					// 	$
					// }
					// if($inner_cft != "")
					// {

					// }
					// if($inner_cbm!="")
					// {

					// }

					$proname_new = "SIZE ".$size_inner ." / CBM ".$inner_cbm;
				}
				else
				{
					$proname_new = "SIZE  / CBM";
				}
			}
			else if($item_full_value['main_carton_type']==2)
			{
				if($item_full_value['item_size_cft']!="")
				{
					$proname_new = $item_full_value['item_size_cft'];
				}
				else if($size_inner!="" || $outer_cft!="" || $outer_cbm!="" )
				{
					// echo "test";exit;
					$proname_new = "SIZE ".$size_outer ." CBM ". $outer_cbm;
				}
				else
				{
					$proname_new = "SIZE / CBM";
				}
			}
			else
			{
				if($item_full_value['item_size_cft']=="")
				{
					$proname_new = "SIZE / CBM ";
				}
				else
				{
					$proname_new = $item_full_value['item_size_cft'];
				}
			}
       // }
       // else
       // {
       // 	     $proname_new = $item_full_value['item_size_cft'];

       // }


			// else if($dispatch_inner_size==1)
			// {
			// 	$proname_new = "SIZE ".$size_inner ." / Hello 1 CFT ". $inner_cft;
			// }
			// else
			// {
			// 	$proname_new = "SIZE ".$size_outer ." / Hello 2 CFT ". $outer_cft;
			// }
			?>
		
			<tr style="background: #eee!important;">
				<td></td>
				<td></td>
				<td></td>
                 <div class="row">
                <div class="col-md-6">

		            <td>Size & CBM<input style="width: 250px;" type="text" class="form-control" id="pro_weight" value="<?=$proname_new; ?>" onchange="UpdateProductSizeCFT(this.value,<?php echo $item_full_value['id']; ?>)"></td>
		         </div>
                  <div class="col-md-6">
                 <td>CFT<input style="width: 150px;" type="text" class="form-control" id="pro_weight" value="<?=$item_full_value['value_cft'];?>" onchange="UpdateCFT(this.value,<?php echo $item_full_value['id']; ?>)"></td>
                    </div>
                </div>
				
				<td></td>
				<td></td>
			</tr>
			<tr style="background: #eee!important;">
				<td></td>
				<td></td>
				<td></td>
				<td><?=$item_value['main_carton_type_name']?> Weight (In Kg)</td>
				<td></td>
				<td><?= number_format($item_value['main_carton_type_weight'],3); ?></td>
			</tr>
			<tr style="background: #eee!important">
				<td></td>
				<td></td>
				<td></td>
				<td>Total</td>
				<td><?= $TOTALQTY?></td>
				<td><?= number_format($TOTALWEIGHT,3); ?></td>
			</tr>
			<tr style="background: #eee!important">
				<td></td>
				<td></td>
				<td></td>
				<td>Actual Weight</td>
				<td></td>
				<td>
					<!-- <input type="text" class="form-control" id="main_carton_whole_actual_weight" value="<?= number_format($item_value['main_carton_whole_actual_weight'],3); ?>" onchange="UpdateActualWeight(this.value,<?php echo $item_value['id']; ?>)"> -->

					<input type="text" class="form-control" id="main_carton_whole_actual_weight" value="<?= number_format($TOTALWEIGHT,3); ?>" onchange="UpdateActualWeight(this.value,<?php echo $item_value['id']; ?>)">
					
				</td>
			</tr>
			<?php
		}
		?>

	</tbody>
	<tfoot>
		<tr style="background: #eee!important">
			<td></td>
			<td></td>
			<td></td>
			<td>Grand Total</td>
			<td><?= $MAINTOTALQTY ?></td>
			<td><?= number_format($MAINTOTALWEIGHT,3); ?></td>
		</tr>
	</tfoot>
</table>
<?php
}

if($mode=="get_packing_product")
{
	?>
	<option>Select Product</option>
	<?php
	//$Dispatch_item_Data = $db->rp_getData("dispatch_item","pro_name,pro_id,weight_id,SUM(qty) AS qty","isDelete = 0 AND dispatch_id IN (".$_REQUEST['dispatch_id'].") ","pro_name ASC",0);
	$Dispatch_item_Data = $db->rp_getData("dispatch_item","pro_id,weight_id,pro_name,SUM(qty) AS qty","dispatch_id IN(".$_REQUEST['dispatch_id'].") GROUP BY pro_id,weight_id","",0);
	while ($Dispatch_item_R = mysqli_fetch_assoc($Dispatch_item_Data))
	{
		$pro_weight = $db->rp_getValue("product_weight_price","pro_weight","product_id='".$Dispatch_item_R['pro_id']."' AND weight_id='".$Dispatch_item_R['weight_id']."'");
        
        $add_manual_qty = $db->rp_getValue("inward_stock","SUM(pro_qty)","isDelete=0 AND pro_id='".$Dispatch_item_R['pro_id']."' AND weight_id='".$Dispatch_item_R['weight_id']."'",0);

		$opening_stock = $db->rp_getValue("product_weight_price","opening_stock_qty","isDelete=0 AND product_id='".$Dispatch_item_R['pro_id']."' AND weight_id='".$Dispatch_item_R['weight_id']."'",0);

		$dispatch_qty = $db->rp_getValue("packing_slip_item","SUM(pro_qty)","isDelete=0 AND pro_id='".$Dispatch_item_R['pro_id']."' AND weight_id='".$Dispatch_item_R['weight_id']."'",0);

		$get_available_stock=$db->get_available_stock($Dispatch_item_R['pro_id'],$Dispatch_item_R['weight_id']);

		$stock_qty=$get_available_stock;

		//$stock_qty = ($add_manual_qty + $opening_stock) - ($dispatch_qty) ;
        
		// $stock_qty = $db->rp_getValue("product_weight_price","stock_qty","product_id='".$Dispatch_item_R['pro_id']."' AND weight_id='".$Dispatch_item_R['weight_id']."'");

		$PackingR = $db->rp_getData("packing_slip","id","dispatch_id IN(".$_REQUEST['dispatch_id'].") AND isDelete=0","",0);
		$PackingIds = array();
		while($PackingD = mysqli_fetch_array($PackingR))
		{
			$PackingIds[] = $PackingD['id'];
		}
		$PackingIds = implode(",",$PackingIds);
		
		$packing_qty = $db->rp_getValue("packing_slip_item","SUM(pro_qty)","packing_slip_id IN (".$PackingIds.") AND pro_id='".$Dispatch_item_R['pro_id']."' AND weight_id='".$Dispatch_item_R['weight_id']."'",0);

		$remainingQty  = $Dispatch_item_R['qty'] - $packing_qty;
		?>
		<option data-name="<?=$Dispatch_item_R['pro_name']?>" data-weight_id="<?=$Dispatch_item_R['weight_id']?>" data-dispatch_qty="<?=$Dispatch_item_R['qty']?>" value="<?=$Dispatch_item_R['pro_id']?>" data-pro-weight="<?= $pro_weight ?>" data-stockqty="<?= $stock_qty ?>" data-remainig_qty="<?= $remainingQty ?>"><?=$Dispatch_item_R['pro_name'] ?></option>
		<?php
	}
}
if($mode=="update_actual_weight")
{
	$count = $db->rp_getTotalRecord("packing_slip_item","id='".$_REQUEST['packing_item_id']."' AND isDelete=0",0);
	if($count>0)
	{
		$actualweight = $_REQUEST['actualweight'];
		$Update_Rows = array("main_carton_whole_actual_weight"=>$actualweight);
		$updated_id = $db->rp_update("packing_slip_item",$Update_Rows,"id='".$_REQUEST['packing_item_id']."'",0);
		if($updated_id)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Actual Weight Update Successfully..");		
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Actual Weight Update Failed..");			
		}
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"No Such Data Found...");
	}
	echo json_encode($ack);
}

if($mode=="update_pro_weight")
{
	$count = $db->rp_getTotalRecord("packing_slip_item","id='".$_REQUEST['packing_item_id']."' AND isDelete=0",0);
	if($count>0)
	{ 
		$updated_id = $db->rp_update("packing_slip_item",array("pro_weight"=>$_REQUEST['pro_weight']),"id='".$_REQUEST['packing_item_id']."'",0);
		if($updated_id)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Product Weight Update Successfully..");		
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Product Weight Update Failed..");			
		}
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"No Such Data Found...");
	}
	echo json_encode($ack);
}

if($mode=="update_pro_size_cft")
{
	$count = $db->rp_getTotalRecord("packing_slip_item","id='".$_REQUEST['packing_item_id']."' AND isDelete=0",0);
	if($count>0)
	{
		$sizecft = $_REQUEST['sizecft'];
		$Update_Rows = array("item_size_cft"=>$sizecft);
		$updated_id = $db->rp_update("packing_slip_item",$Update_Rows,"id='".$_REQUEST['packing_item_id']."'",0);
		if($updated_id)
		{
			$ack=array("ack"=>1,"ack_msg"=>"Actual Weight Update Successfully..");		
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"Actual Weight Update Failed..");			
		}
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"No Such Data Found...");
	}
	echo json_encode($ack);
}
if($mode=="update_cft")
{
	$count = $db->rp_getTotalRecord("packing_slip_item","id='".$_REQUEST['packing_item_id']."' AND isDelete=0",0);
	if($count>0)
	{
		$value_cft = $_REQUEST['value_cft'];
		$Update_Rows = array("value_cft"=>$value_cft);
		$updated_id = $db->rp_update("packing_slip_item",$Update_Rows,"id='".$_REQUEST['packing_item_id']."'",0);
		if($updated_id)
		{
			$ack=array("ack"=>1,"ack_msg"=>"CFT Update Successfully");		
		}
		else
		{
			$ack=array("ack"=>0,"ack_msg"=>"CFT Update Failed..");			
		}
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"No Such Data Found...");
	}
	echo json_encode($ack);
}
require_once 'disconnect.php'; 
?>