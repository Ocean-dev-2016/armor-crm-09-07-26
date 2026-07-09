<?php
$page_id=413;$page_slug='inward_store_page';
include("connect.php");
$inward_store_id = $_REQUEST['id'];
$ctable_where	= "id='".$_REQUEST['id']."' AND isDelete=0";
$ctable_r = $db->rp_getData("inward_store","*",$ctable_where,"",0);
//inward item
$ctable_where1	= "inward_store_id	='".$_REQUEST['id']."' AND isDelete=0";
$ctable_item = $db->rp_getData("inward_store_item","*",$ctable_where1,"",0);
?>
<div id="print_info">					
	<div class="row">
	<div class="col-md-12">	
		<table id="datatable_2" class="table table-striped table-bordered table-hover" style="width:1000px;">
		<thead>
			<tr><th colspan="4" class="bg-grey">Inward Store Information</th></tr>
				<tr>
					<th>No.</th>			
					<th>Vendor Name</th>			
					<th>Total Qty</th>	
					<th>Grand Total</th>	
				</tr>
			</thead>
			<tbody>
			<?php
        if(mysqli_num_rows($ctable_r)>0){
			$count=0;
            while($ctable_d = mysqli_fetch_array($ctable_r)){ 
			$count++;
			$sore_id=$db->rp_getValue("inward_store_item","store_id","inward_store_id='".$ctable_d['id']."'",0);			
        ?>
				<tr>
					<td><label><?php echo $count; ?></label></td>
					<td><label><?php echo $db->rp_getValue("vendor","cname","id='".$ctable_d['vendor']."'",0); ?></label></td>
					<td><label><?php echo $ctable_d['total_qty']; ?></label></td>
					<td><label><?php echo $ctable_d['grand_total']; ?></label></td>
				</tr>
				<?php
            }
        }
        ?>
			</tbody>
			
	
	<table class="table table-striped table-bordered table-hover" style="width:1000px; margin-top:50px;">	
			
		<thead>
		<tr><th colspan="5" class="bg-grey">Inward Store Item Info</th></tr>
			<tr>
				<th>No.</th>
				<th>Product Name</th>
				<th>Received Qty</th>	
				<th>Product Price</th>	
				<th>Total</th>	
			</tr>
		</thead>
		<tbody>
		<?php
		$count=0;
        if(mysqli_num_rows($ctable_item)>0){
            while($ctable_d = mysqli_fetch_array($ctable_item)){
		$count++;				
        ?>
		<tr>
			<td><label><?php echo $count; ?></label></td>
			<td><label><?php echo $ctable_d['product_name']; ?></label></td>
			<td><label><?php echo $ctable_d['receive_qty']; ?></label></td>
			<td><label><?php echo $ctable_d['product_price']; ?></label></td>
			<td><label><?php echo $ctable_d['totalprice']; ?></label></td>
		</tr>
		<?php
			}
		}
		?>
		</tbody>
		
	</table>
	</table>
	</div>
	</div>
</div>
	<div class="row">
		<div class="col-md-2">
			<a onClick="printPDF()" class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>
		</div>
		<div class="col-md-2">
			<a class="btn btn-info" onClick="genReport('<?php echo $_REQUEST['id']; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
		</div>
	</div>
	<?php require_once 'disconnect.php';  ?>
