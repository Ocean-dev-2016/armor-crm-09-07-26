<?php
$page_id=652;$page_slug='scheme_master';
include("connect.php");
$Where="isDelete=0 AND scheme_id='".$_REQUEST['scheme_id']."'";
			
$Results=$db->rp_getData("scheme_master_item","*",$Where,"",0);
?> 
 
<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead>
        <tr>
            <th>Sr.no</th>
			<th>Product Name</th> 
			<th>Qty</th> 
			<th>Free Product</th> 
			<th>Free Qty</th>
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
				$cnt++;
 		?>
	  	<tr class="">

			<td><?php echo $cnt; ?></td>
			<td><?= $db->rp_getValue("weight","name","id='".$R['weight_id']."' AND isDelete=0",0).'-'.$db->rp_getValue("product","name","id='".$R['product_id']."' AND isDelete=0",0);?></td>
			<td><?=$R['qty']?></td>
			<td><?= $db->rp_getValue("weight","name","id='".$R['weight_id_2']."' AND isDelete=0",0).'-'.$db->rp_getValue("product","name","id='".$R['product_id_2']."' AND isDelete=0");?></td>
			<td><?=$R['free_qty']?></td>
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
<?php require_once 'disconnect.php';  ?>