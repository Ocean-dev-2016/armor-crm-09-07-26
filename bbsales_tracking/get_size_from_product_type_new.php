<?php
$page_id=400;$page_slug='dashboard';
include("connect.php");
$type=$_REQUEST['type'];
$mode=$_REQUEST['mode'];
$id=$_REQUEST['id'];
$a_class="";
$in_class="in";
$a_class="";
$aria_expanded="true";
?>
<style>
table{
    height: auto;	
    width:100%;
	font-family: Calibri,sans-serif;
    font-style: normal;
    font-weight: 400;
    padding: 0;
    text-decoration: none;
	font-size: 16px;
	margin:auto;
	padding:auto;
}
.box1 th
{
	border-color: #000;
	color: #FFF;
}
table{
    height: auto;	
    width:100%;
}
table , td, th {
 border: 1px solid #95A5A6;
 border-collapse: collapse;
}
td, th {
 padding: 2px;
 width: 30px;
 height: 15px;
 /*color: #FFF;*/
 /*line-height: 0.8;*/
 text-align: center;
}
h4{
	padding-left:40px;
}
th {
}
.center{
	text-align:center;
}
.left{
	text-align:left;
	padding-left:15px;
}
.right{
	text-align:right;
	padding-right:15px;
}
</style>
<?php
if($type==1)
{
	?>
	<a data-toggle="collapse" class="<?= $a_class; ?>" data-target="#collapseOne" aria-expanded="<?= $aria_expanded; ?>" aria-controls="collapseOne" style="color:#fff;">
		<div class="portlet grey-cascade box">
	 		<div class="portlet-title">
				<div class="caption" style="padding: 11px 0px 9px 10px;font-size: 18px;line-height: 18px;float: left;">Variant and Pricing <span style="float: right; "><i class="fa fa-angle-down"></i></span></div>
  			</div>
  		</div>
	</a>
	<div class="portlet grey-cascade box" style="box-shadow: none;">
  		<div id="collapseOne" class="portlet-body collapse <?= $in_class; ?>">
			<div  class="portlet-body ">	
				<div class="row" style="margin-bottom: 30px;">
					<div class="col-md-4">	
						<label>Select Variant</label>			
						<select name="weight_id" id="weight_id" class="form-control weight_id">
							<option value="">Select Size</option>	
							<?php
							$w_r = $db->rp_getData("weight","*","isDelete=0 AND id!=-1","display_order ASC");
							if($w_r)
							{
								while($w_d=mysqli_fetch_assoc($w_r))
								{
									?>
									<option <?php echo (in_array($w_d['id'],$weight_ids))?"selected":""; ?> value="<?= $w_d['id']?>"><?= $w_d['name'];?></option>
									<?php
								}
							}
							?>
						</select>
					</div>
					<div class="col-md-4">
						<label for="catno">Product code<code>*</code></label>
						<input type="text" class="form-control" name="catno" id="catno" value="<?php echo $catno; ?>">
					</div>	
					<div class="col-md-4">
						<label for="price">Price (INR)	<code>*</code></label>
						<input type="text" class="form-control" name="price" id="price" value="<?php echo $price; ?>">
					</div>												
				</div>
				<div class="row" style="margin-bottom: 30px;">
					<div class="col-md-4">
						<label for="stock_qty">Qty (NOS)<code>*</code></label>
						<input type="text" class="form-control" name="stock_qty" id="stock_qty" value="<?php echo $stock_qty; ?>">
					</div>	
					<div class="col-md-4">
						<button style="margin-top:24px;" class="btn btn-primary" type="button" id="add_product_varient">ADD</button>
						<p class="help-block"></p>
					</div>												
				</div>
				<!-- get data -->
				<div class="row">
					<div id="results"></div>
				</div>	
				<!-- get data -->
			</div>
		</div>
	</div>
	<?php
}
else if($type==2)
{
	?>
	<a data-toggle="collapse" class="<?= $a_class; ?>" data-target="#collapseOne" aria-expanded="<?= $aria_expanded; ?>" aria-controls="collapseOne" style="color:#fff;">
		<div class="portlet grey-cascade box">
	 		<div class="portlet-title">
				<div class="caption" style="padding: 11px 0px 9px 10px;font-size: 18px;line-height: 18px;float: left;">Variant and Pricing <span style="float: right; "><i class="fa fa-angle-down"></i></span></div>
  			</div>
  		</div>
	</a>
	<div class="portlet grey-cascade box" style="box-shadow: none;">
  		<div id="collapseOne" class="portlet-body collapse <?= $in_class; ?>">
			<div  class="portlet-body ">	
				<div class="row" style="margin-bottom: 30px;">
					<div class="col-md-4">	
						<label>Select Variant</label>			
						<select name="weight_id" id="weight_id" class="form-control weight_id">
							<option value="">Select Size</option>	
							<?php
							$w_r = $db->rp_getData("weight","*","isDelete=0 AND id='-1'","display_order ASC");
							if($w_r)
							{
								while($w_d=mysqli_fetch_assoc($w_r))
								{
									?>
									<option <?php echo (in_array($w_d['id'],$weight_ids))?"selected":""; ?> value="<?= $w_d['id']?>"><?= $w_d['name'];?></option>
									<?php
								}
							}
							?>
						</select>
					</div>
					<div class="col-md-4">
						<label for="catno">Product code<code>*</code></label>
						<input type="text" class="form-control" name="catno" id="catno" value="<?php echo $catno; ?>">
					</div>	
					<div class="col-md-4">
						<label for="price">Price (INR)	<code>*</code></label>
						<input type="text" class="form-control" name="price" id="price" value="<?php echo $price; ?>">
					</div>												
				</div>
				<div class="row" style="margin-bottom: 30px;">
					<div class="col-md-4">
						<label for="stock_qty">Qty (NOS)<code>*</code></label>
						<input type="text" class="form-control" name="stock_qty" id="stock_qty" value="<?php echo $stock_qty; ?>">
					</div>	
					<div class="col-md-4">
						<button style="margin-top:24px;" class="btn btn-primary" type="button" id="add_product_varient">ADD</button>
						<p class="help-block"></p>
					</div>												
				</div>
				<!-- get data -->
				<div class="row">
					<div id="results"></div>
				</div>	
				<!-- get data -->
			</div>
		</div>
	</div>
	<?php
}
?>
<script type="text/javascript">	
$("#weight_id").select2();

$(document).ready(function() {
	getProductVarient();
});

$("#add_product_varient").click(function()
{
	var product_id 	= '<?php echo $_REQUEST['id'] ?>';
	var weight_id 	= $("#weight_id").val();
	var catno 		= $("#catno").val();
	var price 		= $("#price").val();
	var stock_qty   = $("#stock_qty").val();

	if(weight_id!="" && catno!="")
	{
		$.ajax({	
			type: "POST",
			url: "ajax_add_product_varient.php",
			data: {
	    		product_id:product_id,
	    		weight_id:weight_id,
	    		catno:catno,
	    		price:price,
	    		stock_qty:stock_qty,
	    		mode:"add_product_varient",
    		},
    		cache: false,
    		beforeSend:function(){
        	},
        	success:function(json)
	      	{
	        	json=$.parseJSON(json);
	      		msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					getProductVarient();
					$("#catno").val("");
					$("#price").val("");
					$("#stock_qty").val("");
					$("#weight_id").select2("val","");
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
	        }
		});
	}
	else
	{
		toastr.error("Please Select Variant And Product Code!!");
	}
});


function getProductVarient()
{	
	var product_id 	= '<?php echo $_REQUEST['id'] ?>';
	$.ajax({
		type: "POST",
		url: "ajax_add_product_varient.php",
		data: {
			product_id:product_id,
			mode:"get_product_varient",
		},
		cache: false,
		beforeSend: function() {
			
		},
		success: function(json)
		{
			$("#results").html(json);
		}
	});
}

function del_conf(id)
{
	var product_id 	= '<?php echo $_REQUEST['id'] ?>';
	var r = confirm("Are you sure you want to delete this Variant?");
	if(r)
	{
		$.ajax({
			type: "POST",
			url: "ajax_add_product_varient.php",
			data: {
				product_id:product_id,
				id:id,
				mode:"delete_product_varient",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					getProductVarient();
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			}
		});
	}
}
</script>