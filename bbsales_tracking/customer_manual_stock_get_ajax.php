<?php
$page_id=650;$page_slug='customer_manual_stock';
include("connect.php");
$Where.="isDelete=0";
if($_REQUEST['to_date'] != "")
{
	$Where.=" AND DATE(planning_date)>='".date('Y-m-d',strtotime($_REQUEST['to_date']))."' ";
}
if($_REQUEST['from_date'] != "")
{
	$Where.=" AND DATE(planning_date)<='".date('Y-m-d',strtotime($_REQUEST['from_date']))."' ";
}

$Results=$db->rp_getData("customer_inward_stock","*",$Where,"created_date DESC",0);
?> 


<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead>
    	
        <tr>
            <th>Sr.no</th>
            <th>Customer Name</th>
            <th>Sales Executive</th>
			<th>Product Name</th> 
			<th>Qty</th> 
			<th>Date</th>  
			<th>Expiry Date</th>  
			<th>Remark</th> 
			<th>Action</th>
        </tr>
    </thead>
    <tbody>
	    <?php 
		if($Results)
		{	
			$cnt=0;
			while($R=mysqli_fetch_assoc($Results))
			{
				// $warehouse_id = $db->rp_getValue("warehouse","name","id IN (".$warehouse_id.")");


				$Warehouse_id = $R['warehouse_id'];
				$warehouseids = array();
				$warehouseR = $db->rp_getData("warehouse","*","id In (".$Warehouse_id.") AND isDelete=0","",0);
				while($warehouseD = mysqli_fetch_assoc($warehouseR))
				{
					$warehouseids[] = $warehouseD['name'];
				}
				$warehouse_name = implode(",", $warehouseids);

				$cnt++;
 				?>
			  	<tr class=""> 
					<td><?php echo $cnt; ?></td>
					<td><?=$db->rp_getValue("executive","company_name","isDelete=0 AND id='".$R['customer_id']."'")?></td>
					<td><?=$db->rp_getValue("sales_executive","name","isDelete=0 AND id='".$R['sales_id']."'")?></td>
					<td><?php echo $R['pro_name']." - ".$db->rp_getValue("product_weight_price","catno","product_id='".$R['pro_id']."'"); ?></td>
					<td><?php echo $R['pro_qty']; ?></td>
					<td><?php echo date('d-m-Y',strtotime($R['planning_date'])); ?></td>

					
					<?php 
						if($R['expiry_date']!="" && $R['expiry_date']!="01-01-1970" && $R['expiry_date']!="0000-00-00" && $R['expiry_date']!="1970-01-01"){

							$expiry_date1 = date('d-m-Y',strtotime($R['expiry_date']));

						}else{
							$expiry_date1 = "";							
						}

					 ?>
					<td><?php echo $expiry_date1; ?></td>
				

					<td><?php echo $R['remark']; ?></td>
					<td>
						<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $R['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>				
					</td>
			 	</tr> 
				<?php													 
			}
		}
		else
		{
			?>
			<tr>
				<td colspan="6" class="text-center">No Data Found!!</td>
			</tr>
			<?php
		}
		?>   
    </tbody>
</table> 

<script type="text/javascript">
	
</script>
<?php require_once 'disconnect.php';  ?>