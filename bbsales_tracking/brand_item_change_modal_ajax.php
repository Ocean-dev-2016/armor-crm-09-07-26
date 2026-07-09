<?php
$page_id=565;$page_slug='page_order';
include("connect.php");
$id = isset($_REQUEST['id'])?$_REQUEST['id']:"";
$order_idfbrand = isset($_REQUEST['order_idfbrand'])?$_REQUEST['order_idfbrand']:"";
$mode = isset($_REQUEST['mode'])?$_REQUEST['mode']:"";
$reference_table = isset($_REQUEST['reference_table'])?$_REQUEST['reference_table']:"";
?>
<?php if($mode == 'show'): ?>
<div class="row">
	<div class="col-md-6">
	    <div class="form-group">
	        <label>Select Brand <code>*</code></label>
	        <select class="form-control" name="m_order_item_brand" id="m_order_item_brand">
				<option value="">Select Brand</option>
				<?php 
					$orderItemBrand_R = $db->rp_getData("order_item_brand_master","id,name","isDelete=0 AND isActive=1");
					if ($orderItemBrand_R) {
						while($orderItemBrand_D = mysqli_fetch_assoc($orderItemBrand_R)) {
							?>
							<option value="<?= $orderItemBrand_D['id'] ?>"><?= $orderItemBrand_D['name'] ?></option>
							<?php
						}
					}
				?>
			</select>
	        <p class="help-block"></p>
	    </div>
	</div>

	<div class="col-md-5" style="margin-top: 25px;">
		<button id="chageBrand" name="submit" class="btn green">Submit</button>
	</div>
</div>
<?php elseif($mode == 'change_brand'): ?>
<?php
	$order_id = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';
	$brand_id = isset($_REQUEST['brand_id']) ? $_REQUEST['brand_id'] : '';
	$order_item_id = isset($_REQUEST['order_item_id']) ? $_REQUEST['order_item_id'] : '';
	$reference_table = isset($_REQUEST['reference_table']) ? $_REQUEST['reference_table'] : '';
	$brand_id_name = $db->rp_getValue("order_item_brand_master","name","isDelete=0 AND isActive=1 AND id='".$brand_id."'",0);

	if($reference_table=='quotation_product_item')
	{
		$colname="quotation_id";
	}
	else{
		$colname="order_id";
	}
	if ($_REQUEST['checkall'] == 'all') {
		$where = "isDelete=0 AND ".$colname." = '".$order_id."'";
	} else {
		$where = "isDelete=0 AND id='".$order_item_id."' AND ".$colname." = '".$order_id."'";
	}
	$isUpdated = $db->rp_update($reference_table,array("order_item_brand_id"=>$brand_id),$where,0);
	if ($isUpdated) {
		$ack = array("ack"=>1,"ack_msg"=>"Brand Change Successfully","brand_name"=>$brand_id_name);
	} else {
		$ack = array("ack"=>0,"ack_msg"=>"Brand Change Failed");
	}
	$db->printJSON($ack);
	die;
?>
<?php endif; ?>
<script type="text/javascript">
	$("#m_order_item_brand").select2();
	$("#chageBrand").on('click',function(){
		var rrrr = confirm("Please submit your orders before Changes brands and cancel if you have not submitted the order");
		if(rrrr)
		{
			let brand_id = $("#m_order_item_brand").val();
			if (brand_id != "") {
				let order_id = "<?php echo $order_idfbrand; ?>"
				let order_item_id = "<?php echo $id; ?>"
				let reference_table = "<?php echo $reference_table; ?>"
				$.ajax({
					type:'POST',
					url:'brand_item_change_modal_ajax.php',
					data:{
						mode:'change_brand',
						order_item_id:order_item_id,
						order_id:order_id,
						brand_id:brand_id,
						reference_table:reference_table,
						checkall:'<?php echo $_REQUEST['checkall']; ?>'
					},
					beforeSend:function () {

					},
					success:function(R){
						let res = JSON.parse(R);
						if (res.ack==1) {
							toastr.success(res.ack_msg);
						} else {
							toastr.error(res.ack_msg);
						}
						$("#brandChange").modal("hide");
						location.reload();
					}
				});
			} else {
				toastr.error("Select Brand!!!");
			}
		} else {
			$("#brandChange").modal("hide");
		}
	});
</script>