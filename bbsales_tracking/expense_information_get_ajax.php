<?php
$page_id=566;$page_slug='page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");

include("../include/no_to_word.php");
$id=$_REQUEST['id'];
$ctable_where	= "id='".$_REQUEST['id']."' AND isDelete=0 AND isActive=1";
$ctable_r = $db->rp_getData("expense","*",$ctable_where,"",0);
$d="";
$discount="";

?>
<style>
#datatable_1
{
	align:center;
}
</style>
<div id="print_info">
<div class="row">
<div class="col-sm-12">
<h4 align="center"><b>Expense Detail</b></h4>
<table id="datatable_1"  style="border-collapse:collapse;" align="center" class="table table-striped table-bordered table-hover">
		
	<tbody>
	<?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $total=0;
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
        ?>
			<tr>
				<th>EXECUTIVE</th>
				<?php 
				$sales=$db->rp_getValue("sales_executive","username","id='".$ctable_d['sales_executive_id']."'",0);
				?>
				<td align="right"><?php echo $sales;  ?></td>
			</tr>
			<tr>
				<th>EXPENSE DATE</th>
				<td align="right"><?php echo date('d-m-Y',strtotime($ctable_d['expense_date']));?></td>
			</tr>
			<tr>
				<th>Category Name</th>
				<td align="right">
					<?php 
					echo $db->rp_getValue("expence_category","name","id='".$ctable_d['category_id']."'",0); 
					?>
				</td>
			</tr>
			<tr>
				<th>Remark</th>
				<td align="right"><?php echo $ctable_d['remark'];?></td>
			</tr>
			<!-- <tr>
				<th>DA</th>
				<td align="right"><?php echo $db->rp_num($ctable_d['DA']);?></td>
			</tr>
			<tr>
				<th>TA</th>
				<td align="right"><?php echo $db->rp_num($ctable_d['TA']);?></td>
			</tr>
			<tr>
				<th>MOA</th>
				<td align="right"><?php echo $db->rp_num($ctable_d['MOA']);?></td>
			</tr>	
			<tr>
				<th>NA</th>
				<td align="right"><?php echo $db->rp_num($ctable_d['NA']);?></td>
			</tr>
			<tr>
				<th>Extra</th>
				<td align="right"><?php echo $db->rp_num($ctable_d['extra']);?></td>
			</tr> -->
			<tr>
				<th><h4><b>Amount</b></h4></th>
				<td align="right"><?php echo $db->rp_num($ctable_d['total']);?></td>
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
			
		<!-- <div class="col-md-2">
			<a onClick="printPDF()" class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>
		</div> -->
		<div class="col-md-2">
			<a class="btn btn-info" onClick="genReport('<?php echo $_REQUEST['id']; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
		</div>
		<div class="col-md-2">
			<a class="btn btn-danger" onClick="del_conf('<?php echo $_REQUEST['id']; ?>');" title="Delete">Reject</a>
		</div>
	</div>
<?php require_once("disconnect.php"); ?>