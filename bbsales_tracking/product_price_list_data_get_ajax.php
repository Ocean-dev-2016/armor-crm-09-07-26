<?php
$page_id=580;$page_slug='price_list_master';
require_once("connect.php");
$price_list_id=$_REQUEST['price_list_id'];
$tcid=$_REQUEST['tcid'];
$cid=$_REQUEST['cid'];
$where ="isDelete=0 ";
if($tcid!="")
{
	$tcids_r=$db->rp_getData("product","id","tcid='".$tcid."' AND isDelete=0");
	$PID_TCID=array();
	if($tcids_r)
	{
		while($k=mysqli_fetch_assoc($tcids_r))
		{
			$PID_TCID[]=$k['id'];
		}
		$PID_TCID=implode(",",$PID_TCID);
	}
	$where .=" AND product_id IN (".$PID_TCID.")";
}
if($cid!="")
{
	$cids_r=$db->rp_getData("product","id","cid='".$cid."' AND isDelete=0");
	$PID_CID=array();
	if($cids_r)
	{
		while($k1=mysqli_fetch_assoc($cids_r))
		{
			$PID_CID[]=$k1['id'];
		}
		$PID_CID=implode(",",$PID_CID);
	}
	$where .=" AND product_id IN (".$PID_CID.")";
}
?>
<?php $is_premium = $db->rp_getValue("price_list","is_premium","id='".$price_list_id."'"); ?>

<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
    <thead>
        <tr>
            <th>Sr.no</th>
			<th>Top Category > Category</th> 
			<th>Name</th> 
			<th>Min Sell Price</th>
			<th>Price</th> 
			<th>Discounted Selling Price</th> 
			<th>Discounted Value</th> 
			<th>Last Updated Date</th> 
			<th>Action</th>
        </tr>
    </thead>
    <tbody>
	    <?php 		    
	    $Results = $db->rp_getData("product_weight_price","id,product_id,weight_id,price,minimum_selling_price",$where,"",0);
		if($Results)
		{														
			$cnt=0;				
			while($R=mysqli_fetch_assoc($Results))
			{	
				$tcid=$db->rp_getValue("product","tcid","id='".$R['product_id']."'");				
				$cid=$db->rp_getValue("product","cid","id='".$R['product_id']."'");	
				$cnt++;
				$check_entry=$db->rp_getTotalRecord("product_price_list","pid='".$R['product_id']."' AND weight_id='".$R['weight_id']."' AND price_list_id='".$price_list_id."' AND isDelete=0");
 				?>
			  	<tr class="">
					<td><?php echo $cnt; ?></td>
					<td><?php echo $db->rp_getValue("top_category_master","name","id='".$tcid."'");?> > <?php echo $db->rp_getValue("category_master","name","id='".$cid."'");?></td>
					<td><?php echo $db->rp_getValue("product","name","id='".$R['product_id']."'"); ?>
						<?php
						if($R['weight_id']!=-1)
						{
						echo "<br/><b>Size : ".$size_name=$db->rp_getValue("weight","name","id='".$R['weight_id']."'")."</b>";
						}
						?>						
					</td>
					<td><?php echo CURR.$R['minimum_selling_price'];?></td>
					<td><?php echo CURR.$R['price'];?></td>
					<td>
						<?php
						$discounted_price=$db->rp_getValue("product_price_list","discounted_price","pid='".$R['product_id']."' AND weight_id='".$R['weight_id']."' AND price_list_id='".$price_list_id."'  AND isDelete=0");
						if($discounted_price=="" && $discounted_price==0)
						{
							$discounted_price="";
						}
					 ?>		
						<input type="text" class="form-control discounted_price" data-min-sell-price="<?= $R['minimum_selling_price'] ?>" data-sell-price="<?= $R['price'];?>" data-product_id="<?= $R['product_id']?>" data-weight_id="<?= $R['weight_id']?>" data-price_list_id="<?= $price_list_id?>"  name="discounted_price" id="discounted_price<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>" value="<?= $discounted_price?>" onchange="checkDiscountNet('<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>')">
						<input type="hidden" name="cat_id" id="cat_id<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>" value="<?= $tcid?>">
						<input type="hidden" name="sub_cat_id" id="sub_cat_id<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>" value="<?= $cid; ?>">
						<input type="hidden" name="mrp_price" id="mrp_price<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>" data-pid="<?= $R['product_id']?>" data-weight-id="<?= $R['weight_id']; ?>" class="mrp<?= $tcid?><?= $cid; ?> mrp_tcid<?= $tcid?>" value="<?= $R['price']?>" data-pid-weight_id="<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>" data-min-sell-price="<?= $R['minimum_selling_price'] ?>">
						<input type="hidden" name="min_sell_price" id="min_sell_price<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>" value="<?= $R['minimum_selling_price']?>">
					</td>
					<td>
						<?php
						if($check_entry)
						{
							$discounted_amount=$db->rp_getValue("product_price_list","discounted_amount","pid='".$R['product_id']."' AND weight_id='".$R['weight_id']."' AND price_list_id='".$price_list_id."'  AND isDelete=0");
							$discount=round($db->rp_getValue("product_price_list","discount","pid='".$R['product_id']."' AND weight_id='".$R['weight_id']."' AND price_list_id='".$price_list_id."'  AND isDelete=0"),2);
							$dis_val=CURR.$discounted_amount." (".$discount."%)";
						}
						else
						{
							$dis_val="";
						}
						?>
						<span id="dis_val<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>"><?= $dis_val; ?></span>
					</td>
					<td>
						<?php							
						if($check_entry)
						{
							$modified_date=$db->rp_getValue("product_price_list","modified_date","pid='".$R['product_id']."' AND weight_id='".$R['weight_id']."' AND price_list_id='".$price_list_id."'  AND isDelete=0");
							$created_date=$db->rp_getValue("product_price_list","created_date","pid='".$R['product_id']."' AND weight_id='".$R['weight_id']."' AND price_list_id='".$price_list_id."'  AND isDelete=0");
							if($modified_date=="0000-00-00 00:00:00")
							{
								$last_updated_date= date("d-m-Y h:i A",strtotime($created_date));
							}
							else
							{
								$last_updated_date= date("d-m-Y h:i A",strtotime($modified_date));
							}
						}
						else
						{
							$last_updated_date="";
						}
						?>
						<span id="last_updated_date<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>"><?= $last_updated_date; ?></span>
					</td>	
					<td>
						<a class="btn btn-info btn-sm clc" title="Add" onclick="addPT('<?= $R['product_id'] ?>','<?= $R['weight_id'] ?>','<?= $price_list_id?>','2');"><!-- <i class="fa fa-pencil-alt"></i> -->Net Discount</a>
						<?php
						if($check_entry)
						{
						?>
						<a class="btn btn-danger btn-sm" onclick="del_conf('<?= $R['product_id'] ?>','<?= $R['weight_id'] ?>','<?= $price_list_id?>');" title="Delete"><i class="fa fa-trash"></i></a><br/>
						<?php
						}
						else
						{
							?><br/><?php
						}
						?>

						<?php

						$discount_type = $db->rp_getValue("product_price_list","discount_type","pid='".$R['product_id']."' AND weight_id='".$R['weight_id']."' AND price_list_id='".$price_list_id."'  AND isDelete=0",0);
						?>

						<input onchange="getButton(this.value,<?= $R['product_id']?>,<?= $R['weight_id']?>,<?= $price_list_id; ?>)" <?php echo ($discount_type== '1') ?  "checked" : "" ;  ?> type="radio"  name="discount_type<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>" id="discount_type<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>" value="1" /> %<br/>

						<input onchange="getButton(this.value,<?= $R['product_id']?>,<?= $R['weight_id']?>,<?= $price_list_id; ?>)" <?php echo ($discount_type== '2') ?  "checked" : "" ;  ?> type="radio"  name="discount_type<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>" id="discount_type<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>" value="2" /> Net

						<input type="hidden" class="discount_check_val<?= $R['product_id']?><?= $R['weight_id']?><?= $price_list_id; ?>" value="">
					</td>	
			 	</tr> 
				<?php										 
			}
		}
		

		if(!$Results && !$Results_sub)
		{
			?>
			<tr>
				<td colspan="8" class="text-center">No Such a Product Found!!!</td>
			</tr>
			<?php
		}
		?>   
    </tbody>
</table> 

<script type="text/javascript">
	$(".discounted_price").on("change", function(event) { 
		var sellprice=parseFloat($(this).data("sell-price"));
		var min_sell_price = parseFloat($(this).data("min-sell-price"));
		var product_id = parseFloat($(this).data("product_id"));
	    var weight_id = parseFloat($(this).data("weight_id"));
	    var price_list_id = parseFloat($(this).data("price_list_id"));
		var pid_weight_id_pricelist = product_id+""+weight_id+""+price_list_id;
		var is_premium = '<?= $is_premium ?>';
		// alert(pid_weight_id_pricelist);		
		if ( event.keyCode == 46 || event.keyCode == 8 ) {
		// let it happen, don't do anything
		} 
		else if (/[^\d\.]/g.test(this.value)) 
		{
			toastr.error("Only Digits Allowed");
			this.value = this.value.replace(/[^\d\.]/g, '');
			$("#dis_val"+pid_weight_id_pricelist).html("");
		}
		else if((this.value>sellprice)) 
		{
			toastr.error("Selling Price is Not More then MRP");
			this.value="";
			$("#dis_val"+pid_weight_id_pricelist).html("");
		}
		else if ((this.value < min_sell_price) && is_premium==0) 
		{
			toastr.error("You Cannot Give Discount Less Than Minimum Selling Amount");
	        this.value = "";
	        $("#dis_val"+pid_weight_id_pricelist).html("");
	        // $("#dis_val"+pid_weight_id_pricelist).html("");
		}
		else {
			checkDiscountNet(pid_weight_id_pricelist);
		}
	});

function getButton(id,pid,weight_id,price_list_id) {
	var abc = $(".discount_check_val"+pid+weight_id+price_list_id).val(id);
}


</script>
<?php require_once 'disconnect.php';  ?>