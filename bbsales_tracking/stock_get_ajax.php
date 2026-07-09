<?php
$page_id=638;$page_slug='product_stock_page';
include("connect.php");
include('../include/product.class.php');
$product=new Product();
$ctable 	= "product";
 
$ctable_where = "";

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
	$where11="";
	$pro_r1=$db->rp_getData("product_weight_price","product_id","catno LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	$PROIDS1=array();
	if($pro_r1)
	{
		while($pro_d1=mysqli_fetch_assoc($pro_r1))
		{
			$PROIDS1[]=$pro_d1['product_id'];
		}
	}
	if(!empty($PROIDS1))
	{
		$PROIDS1=implode(",", $PROIDS1);
		$where11=" OR id IN (".$PROIDS1.")";
	}
	$ctable_where .= " (LOWER(name) like '%".strtolower(trim($_REQUEST['searchName']))."%' ".$where11.") AND ";
	$isFillter=true;
}

if(isset($_REQUEST['item_name']) && $_REQUEST['item_name']!="")
{
    $ctable_where .= " (name like '%".$db->clean($_REQUEST['item_name'])."%') AND ";
    $isFillter=true;
}

if(isset($_REQUEST['category_id']) && $_REQUEST['category_id']!="" && $_REQUEST['category_id']!=NULL && $_REQUEST['category_id']!=undefined)
{
	if ($_REQUEST['category_id'] != '-1') 
	{
	 	$ctable_where .= " tcid='".$_REQUEST['category_id']."' AND ";
	}
	else
	{
		$top_category_id = $_REQUEST['top_category_id'];
	}
    $isFillter=true;
}
if(isset($_REQUEST['sub_category_id']) && $_REQUEST['sub_category_id']!="" && $_REQUEST['sub_category_id']!=NULL && $_REQUEST['sub_category_id']!=undefined)
{
    
    if ($_REQUEST['category_id'] != '-2') 
	{
	 	$ctable_where .= " cid='".$_REQUEST['sub_category_id']."' AND ";
	}
	else
	{
		$sub_category_id = $_REQUEST['sub_category_id'];
	}
    $isFillter=true;
}
 
$ctable_where .= " isDelete=0 ";
 
$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC",0);
$warehouse_id=$_REQUEST['warehouse_id'];
if ($warehouse_id && $isFillter!="") 
{	
?>
<style type="text/css">
	.fix-th
	{
		background-color: #f5f5f5 !important;position: sticky;top: 0; z-index: 1;
	}
	.fix-th1
	{
		background-color: #e5e5e5 !important;position: sticky;top: 0; z-index: 1;
	}
</style>
<form action="" name="frm" id="frm" method="post">
	<div class="table-responsive" style="height: 600px;">
		<table id="Product_stock" class="table table-striped table-bordered table-hover" >
	        <thead class="fix-th">
	        	
	            <tr>
	                <th class="fix-th1" style="width: 5%;">No</th>
	                <th class="fix-th1">Category</th>
	                <th class="fix-th1">Sub Category</th>
	                <th class="fix-th1" colspan="3">Item Name</th>
	                <th class="fix-th1" colspan="3">Item Code</th>
	               <!--  <th class="fix-th1">Location</th> -->
	                <!-- <th class="fix-th1">Standard Stock</th> -->
	               <!--  <th class="fix-th1">Reorder Point</th> --> 
	                <th class="fix-th1" style="background-color: #808080;">Availbale Stock</th>
	                <th class="fix-th1" style="background-color: #808080;">Manual Stock</th>
	                <th class="fix-th1" style="background-color: #808080;">Dispatch Qty</th>
	                <!-- <th class="fix-th1" style="background-color: #808080;">Opening Qty</th> -->
	                <!-- <th class="fix-th1" style="background-color: #808080;">Diffrence</th> -->
	                 <!-- <th class="fix-th1" style="background-color: #8888bd;">Order Qty</th>  -->
	               <!-- <th class="fix-th1" style="background-color: #008000;">Packing Qty</th> -->
	              <!-- <th class="fix-th1" style="background-color: #008000;">Planning Qty</th>  -->
	             <!--   <th class="fix-th1" style="background-color: #8888bd;">Pending Order Qty</th> -->
	                <!-- <th class="fix-th1" style="background-color: #ff0000;">Balance(order qty - dispatch qty - stock qty)</th> -->
	                <th class="fix-th1" style="background-color: #ff0000;">Min Stock Qty</th>
	                <th class="fix-th1" style="background-color: #ff0000;">Max Stock Qty</th>
	                <th class="fix-th1">Price (Rs.)</th>
	                 <th class="fix-th1">Amount(Rs.)</th>
	               <!--  <th class="fix-th1">Check Box</th> -->
	                <th class="fix-th1">Action</th>
				</tr>
	        </thead>
	        
	        <tbody>
	        <?php
	        if(mysqli_num_rows($ctable_r)>0)
	        {
	            $count = 0;
	            $actual_Stock_count = 0;
				while($ctable_d = mysqli_fetch_assoc($ctable_r))
				{ 
					$current_prodcuts=$product->aj_getProductDetail($ctable_d['id'],$uid);		
					if(!empty($current_prodcuts))
					{
						foreach($current_prodcuts as $product_detail)
						{  
							$inward_where="warehouse_id='".$warehouse_id."' AND isDelete=0 AND pro_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."' AND reference_table!='dispatch_detail'";
							// $add_manual_qty = $db->rp_getValue("inward_stock","SUM(pro_qty)","isDelete=0 AND pro_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."'",0);

							$opening_stock = $db->rp_getValue("product_weight_price","stock_qty","isDelete=0 AND product_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."'",0);

							$disR=$db->rp_getData("dispatch_detail","id","warehouse_id='".$warehouse_id."' AND isDelete=0","",0);
						    $dispatchIds=array();
						    if($disR)
						    {
						        while($disD=mysqli_fetch_assoc($disR))
						        {
						            $dispatchIds[]=$disD['id'];
						        }
						    }
						     
						    $dispatchIds=implode(",", $dispatchIds); 
							$dispatch_qty1 = $db->rp_getValue("dispatch_item","SUM(qty)","pro_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."' AND dispatch_id IN(".$dispatchIds.") AND isDelete=0",0);

							$opening_stock_qty = $db->rp_getValue("product_weight_price","opening_stock_qty","product_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."' AND isDelete=0",0);

							$manual_stock_qty = $db->rp_getValue("inward_stock","SUM(pro_qty)",$inward_where,0);
 
							$get_available_stock=$db->get_available_stock($ctable_d['id'],$product_detail['weight_id'],$warehouse_id);

							$actual_Stock=$get_available_stock;
							

						  	$min_Stock = $db->rp_getValue("product_weight_price","min_stock_qty","isDelete=0 AND product_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."'",0);

							$max_Stock = $db->rp_getValue("product_weight_price","max_stock_qty","isDelete=0 AND product_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."'",0);   

							// $actual_Stock1 = ($add_manual_qty + $opening_stock);
							$stock_diff = ($actual_Stock - $product_detail['stock_qty']);
 
							if($actual_Stock>$min_Stock AND $actual_Stock<$max_Stock)
						    {
						    	// echo "GG";exit();
							    $color1 ='#FFFFFF';
						    } 
						    if($actual_Stock < $min_Stock)
						    {
						    	$color1='#f23b38';
						    }
						    if($actual_Stock > $max_Stock)
						    {
						    	$color1='#09d917';
						    }
	      					
	      					if(!empty($orderids))
							{ 
								// $dispatch_qty = $db->rp_getValue("dispatch_qty","SUM(qty)","isDelete=0 AND pro_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."'",0);
 
								if($reorder_point>$actual_Stock)
								{
									$color = '#D17272';
								}
								else
								{
								    $color = '#fff';
								}

								$check_val = $db->rp_getValue("product_weight_price","check_box","isDelete=0 AND product_id='".$ctable_d['id']."' AND weight_id='".$product_detail['weight_id']."'",0);

								// $diffrence = ($order_qty - $dispatch_qty - $planning_qty ) - ( $product_detail['stock_qty'] );
								
							  $diffrence = ($order_qty) - ($dispatch_qty) - ($planning_qty ) - ( $actual_Stock );
							     // $diffrence = ($actual_Stock - $order_qty);

								if($diffrence < 0)
								{
									$diffrence = abs($diffrence);
								}
								else
								{	
									$diffrence = "-" . $diffrence;
								}
							}
						    ?>
							<tr style="background-color: <?= $color; ?>">
          						<td><?php echo ++$count; ?></td>
          						<td><?php echo $db->rp_getValue("top_category_master","name","id='".$ctable_d['tcid']."'",0);?></td>
          						<td><?php echo $db->rp_getValue("category_master","name","id='".$ctable_d['cid']."'",0);?></td>
			          			<?php
			          			if($product_detail['title']=="")
			          			{
			          				?>
			          				<td colspan="3"><?php echo $ctable_d['name']; ?> </td>
			          				<?php
			          			}
			          			else
			          			{
			          				?>
			          				<td colspan="3"><?php echo $ctable_d['name']; ?> </td>
			          				<!-- <td><?php echo $ctable_d['name']." (".$product_detail['title'].")"; ?> </td> -->
			          				<?php
			          			}
			          			?>
			          			<td colspan="3"><?php echo $product_detail['catno']; ?></td>
			          			<!--  <td><input type="text" class="form-control" name="location" id="location" value="<?= $location ?>" onChange="AddLocation(<?= $ctable_d['id'] ?>,<?=$product_detail['weight_id'] ?>,this.value)"></td> --> 
			          			<!-- <td><input type="text" class="form-control" name="standard_stock" id="standard_stock" value="<?= $standard_stock ?>" onChange="AddStandard(<?= $ctable_d['id'] ?>,<?=$product_detail['weight_id'] ?>,this.value)"></td> -->
			          			<!-- <td><input type="text" class="form-control" name="reorder_point" id="reorder_point" value="<?= $reorder_point ?>" onChange="AddReorderPoint(<?= $ctable_d['id'] ?>,<?=$product_detail['weight_id'] ?>,this.value)"></td> -->
			          			<td style="background-color: <?= $color1; ?>"><?php echo $actual_Stock; ?></td>
			          			<td><?php echo $manual_stock_qty; ?></td>
			          			<td><?php echo $dispatch_qty1; ?></td>
			          			<!-- <td><?php echo $opening_stock_qty; ?></td> -->
			          			<!-- <td><?=($opening_stock_qty) - ($dispatch_qty1)?></td> -->
			          			<!-- <td><?php echo $order_qty; ?></td> -->
			          		<!-- 	<td><?php echo $dispatch_qty ?></td> -->
			          		<!-- 	<td><?php echo $planning_qty ?></td> -->
			          		<!-- 	<td><?php echo $pending_order_qty; ?></td> -->
			          			<!-- <td><?php echo $diffrence ?></td> -->
			          			<td><?php echo $min_Stock;?></td>
			          			<td><?php echo $max_Stock;?></td>
			          			<td><?php echo $product_detail['price']; ?></td>
			          			<td><?php echo $product_detail['price']*$actual_Stock?></td>
			          		<!-- 	<td><input <?php echo ($check_val==1)?"checked":"";?> type="checkbox" class="form-control masterCheck" name="chkbx" id="chkbx" value="" onChange="AddCheckbox(<?= $ctable_d['id'] ?>,<?=$product_detail['weight_id'] ?>)"><input type="hidden" class="chk_value" value="<?php echo $check_val;?>"></td> -->
			          			<td>
			          				<!-- target="_blank" href="stock_detail_manage.php?id=<?= $ctable_d['id'] ?>&weight_id=<?= $product_detail['weight_id']?>" -->
			          				<a >
			          					<span style="background-color: #44b6ae;color: #FFFFFF" class="btn text-success">View detail
			          					</span>
			          				</a>
			          			</td>
          					</tr>
							<?php
							$actual_Stock_count += $actual_Stock;
							$final_price_count += $product_detail['price'];
							$total_price_of_available_stock +=  $actual_Stock*$product_detail['price'];                          
						}
					}
					?>
					<?php
				}
	        }
			else
			{
			?>
			<tr>
				<td align="center" colspan="30"><?php echo "No Data Found";?></td>
			</tr>
			<?php
			}
			?>
	        </tbody>
	        <tfoot>
	        	<tr>
	        		<td colspan="9" align="right"><b>Total</b></td>
	        		<td align="right"><b><?php echo $actual_Stock_count; ?></b></td>
	        		<td></td>
	        		<td></td> 
	        		<td></td>
	        		<td></td>
	        		<td><b><?php echo $final_price_count; ?></b></td>
	        		<td></td>
	        		<td></td>
	        	</tr>
	        	<tr>
	        		<td colspan="15" align="right"><b>Total Price of Available Stock </b></td>
	        		 
	        	<!-- 	<td align="right"><b><?php echo ($final_price_count*$actual_Stock_count); ?></b></td> -->
	        		<td align="right"><b><?php echo ($total_price_of_available_stock); ?></b></td>
	        		<td></td> 
	        	</tr>
	        </tfoot>
	    </table>
	</div>
</form>
<?php
}
else
{
?>
<div class="row">
	<div class="col-sm-12 text-center">
		<h3> Select Category & Warehouse Filter To See Report</h3>
	</div>
</div>
<?php
}
?>
<script type="text/javascript">
	function AddLocation(product_id,weight_id,location)
	{
		$.ajax({
        	type: "POST",
        	url: "customer_ajax_function.php",
        	data:'product_id='+product_id+'&weight_id='+weight_id+'&location='+location+'&m=add_location',
        	beforeSend:function(){
            	$('.preloader').fadeIn('slow');
        	},
       		success: function(data)
       		{
       			data=$.parseJSON(data);
       			if(data['ack']==1)
       			{
       				$('.preloader').fadeOut('slow');
       			}
       			else
       			{
       				$("#loading-modal").modal('hide'); 
       				toastr.error(data['ack_msg']);
       				location.reload();
       			}
       		}
    	});
	}

	function AddStandard(product_id,weight_id,standard)
	{
		$.ajax({
        	type: "POST",
        	url: "customer_ajax_function.php",
        	data:'product_id='+product_id+'&weight_id='+weight_id+'&standard='+standard+'&m=add_standard',
        	beforeSend:function(){
            	$('.preloader').fadeIn('slow');
        	},
       		success: function(data)
       		{
       			data=$.parseJSON(data);
       			if(data['ack']==1)
       			{
       				$('.preloader').fadeOut('slow');
       			}
       			else
       			{
       				$("#loading-modal").modal('hide'); 
       				toastr.error(data['ack_msg']);
       				location.reload();
       			}
       		}
    	});
	}

	function AddReorderPoint(product_id,weight_id,reorder_point)
	{
		$.ajax({
        	type: "POST",
        	url: "customer_ajax_function.php",
        	data:'product_id='+product_id+'&weight_id='+weight_id+'&reorder_point='+reorder_point+'&m=add_reorder_point',
        	beforeSend:function(){
            	$('.preloader').fadeIn('slow');
        	},
       		success: function(data)
       		{
       			data=$.parseJSON(data);
       			if(data['ack']==1)
       			{
       				$('.preloader').fadeOut('slow');
       			}
       			else
       			{
       				$("#loading-modal").modal('hide'); 
       				toastr.error(data['ack_msg']);
       				location.reload();
       			}
       		}
    	});
	}

	function AddCheckbox(product_id,weight_id)
	{
		$.ajax({
        	type: "POST",
        	url: "customer_ajax_function.php",
        	data:'product_id='+product_id+'&weight_id='+weight_id+'&chkbx='+1+'&m=add_chk_box',
        	beforeSend:function(){
            	$('.preloader').fadeIn('slow');
        	},
       		success: function(data)
       		{
       			data=$.parseJSON(data);
       			if(data['ack']==1)
       			{
       				$('.preloader').fadeOut('slow');
       			}
       			else
       			{
       				$("#loading-modal").modal('hide'); 
       				toastr.error(data['ack_msg']);
       				location.reload();
       			}
       		}
    	});
	}
</script>
<?php require_once "disconnect.php"; ?>
