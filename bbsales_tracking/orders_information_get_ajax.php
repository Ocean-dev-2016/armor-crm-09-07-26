<?php
$page_id=566;$page_slug='page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");

include("../include/no_to_word.php");
$order_id=$_REQUEST['id'];
$ctable_where	= "order_id='".$_REQUEST['id']."' AND isDelete=0";
$ctable_r = $db->rp_getData("order_product_item","*",$ctable_where,"",0);
$d="";
$discount="";
$customer_id = $db->rp_getValue("orders","customer_id","id='".$_REQUEST['id']."'","",0);
?>
<div id="print_info">
<div class="row">
<div class="col-md-12">
<h4><b>Party Name : </b><?php echo $db->rp_getValue("executive","company_name","id='".$customer_id."'"); ?></h4>
<table id="datatable_1"  style="border-collapse:collapse;" class="table table-striped table-bordered table-hover">
	<thead>
            <tr>
                <th><b>No.</b></th>
                <th><b>Product Name</b></th>
                <th style="text-align:right;"><b>Product Price</b></th>
                <th style="text-align:right;"><b>Order Qty</b></th>
                <th style="text-align:right;"><b>Discount</b></th>
                <th style="text-align:right;"><b>Taxable</b></th>
                <th style="text-align:right;"><b>CGST</b></th>
                <th style="text-align:right;"><b>SGST</b></th>
                <th style="text-align:right;"><b>IGST</b></th>
                <th style="text-align:right;"><b>Total</b></th>
            </tr>
        </thead>		
	<tbody>
	<?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
			$taxable1=0;
			$tax=0;
            $total=0;
			$d=0;
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
        ?>
			<tr>
				<td><?php echo $count;  ?></td>
				<td><?php echo $ctable_d['pro_name'];  ?></td>
				<td align="right"><?php echo CURR.$db->rp_num($ctable_d['unitprice']);  ?></td>
				<td align="right"><?php echo  $ctable_d['pro_qty']; ?></td>
				<td align="right"><?php echo  $ctable_d['discount_amount']; echo '(' ; echo $ctable_d['discount']; echo '%)'; ?></td>
				<td align="right"><?php echo  $taxable=$db->rp_getValue("orders","taxable","id=".$order_id."",0);?></td>
				<td align="right"><?php echo  $ctable_d['cgst_amount'];echo '(' ; echo $ctable_d['cgst_tax']; echo '%)'; ?></td>
				<td align="right"><?php echo  $ctable_d['sgst_amount'];echo '(' ; echo $ctable_d['sgst_tax']; echo '%)'; ?></td>
				<td align="right"><?php echo  $ctable_d['igst_amount'];echo '(' ; echo $ctable_d['igst_tax']; echo '%)'; ?></td>
				<td align="right"><?php echo  CURR.$db->rp_num($ctable_d['totalprice']);  ?></td>
			</tr>
			
			<?php
			
			$taxable1+=$taxable;
			$d+=$discount;
			$total+=$ctable_d['totalprice'];
			$tax+=$ctable_d['cgst_amount']+$ctable_d['sgst_amount']+$ctable_d['igst_amount'];
				}
			}else{
				?>
				<td colspan="10" style="text-align:center;">No Data Available</td>
				<?php
			}
			?>
			<tr>
				<td colspan="9" style="text-align:right;"><b>Taxable:</b></td>
				<td align="right" ><?php echo CURR.$db->rp_num($taxable1); ?></td>
			</tr>
			
			
			<tr>
			
				<td colspan="9" style="text-align:right;"><b>Discount:
				</td>
				<td align="right">
				<?php 
				$final_discount=($total*$d)/100;
				
				echo  CURR.$db->rp_num($final_discount);
				// if($discount_type==1)
				// {
				// $d=$db->rp_getValue("orders","total_amount","id=".$order_id."")*$discount/100;
				// echo CURR.$db->rp_num($d);
				// }
				// else
				// {
				// echo CURR.$db->rp_num($discount);	
				// }
			?></td>
			</tr>
			<tr>
				<td colspan="9" style="text-align:right;"><b>Tax:</b></td>
				<td align="right" ><?php echo CURR.$db->rp_num($tax); ?></td>
			</tr>
			<tr>
			<?php
			$grand_total=0;
			
			$grand_total=($taxable1-$final_discount)+$tax;
			//$grand_total=$db->rp_getValue("orders","grand_total","id='".$_REQUEST['id']."'");
			// if($discount_type==1)
			// {
				// $grand_total=$tax+$taxbale1-$final_discount;
			// }
			// else
			// {
				// $grand_total=$tax+$taxbale1-$final_discount;
			// }
			
			?>
				<td colspan="9" style="text-align:right;"><b>Grand Total:</b></td>
				<td align="right"><?php echo CURR.$db->rp_num($grand_total); ?></td>
			</tr>
			</tbody>
			</table>
			</div>

			</div>
			
</div>
			<div class="row">
		<div class="col-sm-1" style="margin-right: 10px;">
			<a onClick="printPDF()" class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>

		</div>
		<div class="col-md-1">
			<a class="btn btn-info" onClick="genReport('<?php echo $order_id; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
		</div>
	</div>
<?php require_once "disconnect.php"; ?>