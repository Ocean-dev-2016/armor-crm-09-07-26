<?php
$page_id=400;$page_slug='dashboard';
require_once("connect.php"); 
?>
<div class="col-md-12 col-sm-12 co-xs-12 col-lg-12">
	<div class="portlet light div-set-height">
		<div class="portlet-title"> 
			<div class="col-md-5">
				<h3><b>Moving & Non Moving Product Detail</b></h3>
			</div>
			<div class="col-md-3">
				<label>Category</label>
				<select class="form-control status" name="moving_category_id" id="moving_category_id">
					<option value="">Select Category</option>
	                <?php 
					$moving_cat_r=$db->rp_getData('top_category_master',"*","isDelete=0","",0);
					while($moving_cat_d=mysqli_fetch_assoc($moving_cat_r))
					{
					?>
					<option value="<?php echo $moving_cat_d['id']?>"> <?php echo $moving_cat_d['name'];?></option>
					<?php
					}
					?>
	            </select>
	        </div>
			<div class="col-md-2">
				<label>Days</label>
				<input type="number" name="moving_days" id="moving_days" value="" class="form-control">
			</div> 
			<span>
				<a href="javascript:;" onclick="getMovingProduct()" class="btn btn-primary" style="margin-top:25px">
				<i class="fa fa-eye"></i>View Product </a>
			</span>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-sm-12" id="moving_data_result"></div>
			</div>
		</div>
	</div> 
</div>


<script type="text/javascript">
	function getMovingProduct()
	{
		var moving_days = $("#moving_days").val();
		var moving_category_id = $("#moving_category_id").val();
		if(moving_days!="")
	    {
			$.ajax({
	            type:'POST',
	            url:'moving_product_data_get_ajax.php',
	            data:
	            {
	                moving_days:moving_days,
	                top_category_id:moving_category_id,
	            }, 
	            success: function(data) {
	            	$("#moving_data_result").html(data); 
	        	}
	        });
	    }
	    else
	    {
	    	toastr.error("Please enter days to see result!!");
	    }

	}
</script>
