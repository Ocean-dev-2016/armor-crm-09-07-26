<?php
$page_id=515;$page_slug='page_inquiry';
/*
 * @author Ravi Patel
 */
include("connect.php");
$order_id=$_REQUEST['id'];
$ctable_where	= "order_id='".$_REQUEST['id']."' AND isDelete=0";
$ctable_r = $db->rp_getData("order_product_item","*",$ctable_where,"",0);

?>
<div id="print_info">
<div class="row">
<div class="col-sm-12">
<h4><b>Personal Detail</b></h4>
<table id="datatable_1" class="table table-striped table-bordered table-hover">
	<thead>
            <tr>
                <th>No.</th>
                <th>Product Name</th>
                <th>Product Price</th>
                <th>Order Qty</th>
                <th>Total_price</th>
            </tr>
        </thead>		
	<tbody>
	<?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $total=0;
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
        ?>
			<tr>
				<td><?php echo $count;  ?></td>
				<td><?php echo $ctable_d['pro_name'];  ?></td>
				<td><?php echo $ctable_d['unitprice'];  ?></td>
				<td><?php echo  $ctable_d['pro_qty']; ?></td>
				<td><?php echo  $ctable_d['totalprice'];  ?></td>
			</tr>
			
			<?php
			$total+=$ctable_d['totalprice'];
				}
			}else{
				?>
				<td colspan="6" style="text-align:center;">No Data Available</td>
				<?php
			}
			?>
			<tr>
				<td>Total:</td>
				<td><?php echo $total; ?></td>
			</tr>
			</tbody>
			</table>
			</div>

			</div>
			
</div>
			<div class="row">
		<div class="col-md-2">
			<a onClick="printPDF()" class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>
		</div>
		<div class="col-md-2">
			<a class="btn btn-info" onClick="genReport('<?php echo $order_id; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
		</div>
	</div>
<?php require_once "disconnect.php"; ?>