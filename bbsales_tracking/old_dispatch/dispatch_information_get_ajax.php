<?php
$page_id=566;$page_slug='page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");

include("../include/no_to_word.php");
$dispatch_id=$_REQUEST['id'];
$ctable_where	= "dispatch_id='".$_REQUEST['id']."' AND isDelete=0";
$ctable_r = $db->rp_getData("dispatch_item","*",$ctable_where,"",0);
$d="";
$discount="";

?>
<div id="print_info">
<div class="row">
<div class="col-sm-12">
<h4 align="center"><b>Dispatch Product Item Detail</b></h4>
<table id="datatable_1"  style="border-collapse:collapse;" class="table table-striped table-bordered table-hover">
	<thead>
            <tr>
                <th><b>No.</b></th>
                <th><b>Product Name</b></th>
                <th style="text-align:right;"><b>Product Price</b></th>
                <th style="text-align:right;"><b>Dispatch Qty</b></th>
                <!--th style="text-align:right;"><b>Remaining Qty</b></th-->
				<th style="text-align:right;"><b>Dispatch Date</b></th>
                <th style="text-align:right;"><b>Amount</b></th>
                
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
				<td align="right"><?php echo  $ctable_d['qty']; ?></td>
				<!--td align="right"><?php
				/*$where="order_id='".$ctable_d['order_id']."' AND pro_id='".$ctable_d['pro_id']."' AND weight_id='".$ctable_d['weight_id']."'";
				$remaining_qty=$db->rp_getValue("order_product_item","remaining_qty",$where);
				echo  $remaining_qty;*/ ?></td-->
				<td align="right"><?php echo  date('d-m-Y',strtotime($ctable_d['dispatch_date']));  ?></td>
				<td align="right"><?php echo  CURR.$db->rp_num($ctable_d['amount']);  ?></td>
				
			</tr>
			
			<?php
			$total+=$ctable_d['amount'];
				}
			}else{
				?>
				<td colspan="6" style="text-align:center;">No Data Available</td>
				<?php
			}
			?>
			<tr>
				<td colspan="5" style="text-align:right;"><b>Total Amount:</b></td>
				<td align="right" ><?php echo CURR.$db->rp_num($total); ?></td>
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
			<a class="btn btn-info" onClick="genReport('<?php echo $dispatch_id; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
		</div>
	</div>
	<?php require_once 'disconnect.php';  ?>