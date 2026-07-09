<?php
$page_id=566;$page_slug='page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");

include("../include/no_to_word.php");
$order_id=$_REQUEST['id'];
$ctable_where	= "order_id='".$_REQUEST['id']."' AND isDelete=0 AND dispatched_qty>0";
$ctable_r = $db->rp_getData("order_product_item","*",$ctable_where,"",0);
$d="";
$customer_id = $db->rp_getValue("orders","customer_id","id='".$_REQUEST['id']."'","",0);
?>
<div id="print_info_dispatch">
<div class="row">
<div class="col-sm-12">
<h4><b>Party Name : </b><?php echo $db->rp_getValue("executive","company_name","id='".$customer_id."'"); ?></h4>
<table id="datatable_1"  style="border-collapse:collapse;" class="table table-striped table-bordered table-hover">
	<thead>
            <tr>
                <th><b>No.</b></th>
                <th><b>Product Name</b></th>
                <th style="text-align:right;"><b>Product Price</b></th>
                <th style="text-align:right;"><b>Order Qty</b></th>
                <th style="text-align:right;"><b>Dispatch Qty</b></th>
                <th style="text-align:right;"><b>Remaining Qty</b></th>
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
				<td align="right"><?php echo CURR.$db->rp_num($ctable_d['unitprice']);  ?></td>
				<td align="right"><?php echo  $ctable_d['pro_qty']; ?></td>
				<td align="right"><?php echo  $ctable_d['dispatched_qty']?></td>
				<td align="right"><?php echo  $ctable_d['remaining_qty']?></td>
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
			
			
			</tbody>
			</table>
			</div>

			</div>
			
</div>
			<div class="row">
		<div class="col-md-2">
			<a onClick="printPDFDispatch()" class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>
		</div>
		<div class="col-md-2">
			<a class="btn btn-info" onClick="genReportDispatch('<?php echo $order_id; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
		</div>
	</div>
	<?php require_once 'disconnect.php';  ?>