<?php
$page_id=633;$page_slug='manual_stock_page';
include("connect.php");
$Where="isDelete=0";
$Results=$db->rp_getData("inward_stock","*",$Where,"id DESC",0);
?> 
<style>
    .table-scrollable 
{
    width: auto;
    height: 600px;
    overflow-x: scroll;
    overflow-y: scroll;
    border: 1px solid #e7ecf1;
    margin: 10px 0 !important;
}
</style>

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
<div class="table-scrollable">
	<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
	    <thead class="fix-th">
	        <tr>
	            <th>Sr.no</th>
				<th>Product Name</th> 
				<th>Qty</th> 
				<th>Stock Added Date</th> 

				<!-- <th>Invoice No</th> 
				<th>Invoice Date</th>  -->
				<th>Warehouse</th> 

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
						<td><?php echo $R['pro_name']." - ".$db->rp_getValue("product_weight_price","catno","product_id='".$R['pro_id']."' AND weight_id='".$R['weight_id']."'",0); ?></td>
						<td><?php echo $R['pro_qty']; ?></td>
						<td><?php echo date('d-m-Y',strtotime($R['planning_date'])); ?></td>

						<!-- <td><?php echo $R['invoice_no']; ?></td> -->
						<?php 
							if($R['invoice_date']!="" && $R['invoice_date']!="01-01-1970" && $R['invoice_date']!="0000-00-00" && $R['invoice_date']!="1970-01-01"){

								$invoice_date1 = date('d-m-Y',strtotime($R['invoice_date']));

							}else{
								$invoice_date1 = "";							
							}

						 ?>
						<!-- <td><?php echo $invoice_date1; ?></td> -->
						<td><?php echo $warehouse_name; ?></td>

						<td><?php echo $R['remark']; ?></td>
						<td>
							<?php
								if($rights['delete_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
								{ 
							?>
									<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $R['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>	
							<?php
								}
							?>			
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
</div>
<?php require_once "disconnect.php"; ?>
