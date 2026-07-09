<?php 
$page_id=633;$page_slug='add_manual_stock';
$ctable 	= "production_planning";
$ctable1 	= "Add Manual Stock";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manual Stock";
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>"meeting_manage.php","title"=>"Manual Stock"));
include("connect.php");

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" /> 
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "sales_executive_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-md-12"> 
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				<div class="col-md-12 "><br/>
                    <!-- BEGIN Portlet PORTLET-->
                    <div class="portlet box blue">
                       <div class="portlet-title">
                            <div class="caption">
                            	Manual Stock
                        	</div>
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv">
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label>Select Stock Movement <code>*</code></label>
											<select onchange="AddstockType()" class="form-control" name="stock_movement" id="stock_movement">
												<!-- <option value="">Select Stock Movement</option> -->
												<option value="2">Add Manual Stock</option>
												<option value="1">Warehouse To Warehouse</option>
											</select>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Stock Added Date <code>*</code></label>
											<?php
												if ($_REQUEST['planning_date']!="") 
												{
													$planning_date = $_REQUEST['planning_date'];
												}
											?>
											<input type="text" readonly="" class="form-control" name="planning_date" id="planning_date" value="<?php echo $planning_date; ?>" />
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>From Warehouse <code>*</code></label>
											<select class="form-control" name="warehouse_id" id="warehouse_id">
												<option value="">Select From Warehouse</option>
												<?php
												$WarehouseR=$db->rp_getData('warehouse',"*","isDelete=0","",0);
												while($WarehouseD=mysqli_fetch_assoc($WarehouseR))
												{
													if ($_REQUEST['warehouse_id']!="") 
													{
														$warehouse_id = $_REQUEST['warehouse_id'];
													}
												?>
												<option <?=($warehouse_id == $WarehouseD['id'])?"selected":"";?> value="<?php echo $WarehouseD['id']; ?>">
												<?php echo $WarehouseD['name']; ?>
												</option>
												<?php
												}
												?>
											</select>
											<p class="help-block"></p>
										</div>
									</div>									
									<div class="col-md-3">
										<div class="form-group" id="to_warehouse_div">
											<label>To Warehouse <code>*</code></label>
											<select class="form-control" name="to_warehouse_id" id="to_warehouse_id">
												<option value="">Select To Warehouse</option>
												<?php
												$WarehouseR=$db->rp_getData('warehouse',"*","isDelete=0","",0);
												while($WarehouseD=mysqli_fetch_assoc($WarehouseR))
												{
												?>
												<option <?=($warehouse_id == $WarehouseD['id'])?"selected":"";?> value="<?php echo $WarehouseD['id']; ?>">
												<?php echo $WarehouseD['name']; ?>
												</option>
												<?php
												}
												?>
											</select>
											<p class="help-block"></p>
										</div>
									</div>
								</div>
								<div class="row">	       
									<div class="col-md-2">
										<div class="form-group">
											<label>Category <code>*</code></label>
											<select class="form-control" name="category_id[]" id="category_id" multiple="multiple">
												<option value="">select Category</option>
												<?php
												$cat_r=$db->rp_getData("top_category_master","*","isDelete=0 AND isActive=1",0);
												while($cat_d=mysqli_fetch_assoc($cat_r))
												{
													if ($_REQUEST['category_id']!="") 
													{
														$category_id = $_REQUEST['category_id'];
													}
												?>
												<option <?=($category_id == $cat_d['id'])?"selected":"";?> value="<?= $cat_d['id'] ?>"><?= $cat_d['name'] ?></option>
												<?php
												}
												?>
											</select>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label> Product <code>*</code></label>
											<select class="form-control b-3" id="product_id" name="product_id">
						                        <option value="">Select Product</option> 
								            </select>
								            <input type="hidden" name="weight_id" id="weight_id" value="">
										</div>
									</div> 
								
									<div class="col-md-2">
										<div class="form-group">
											<label style="margin-top: 30px;">Current Stock Qty : </label>
											<strong><span class='stock_qty1'></span></strong>
										</div>
									</div>	

									<div class="col-md-2">
										<div class="form-group">
											<label for="">Qty <code>*</code></label>
											<div class="abc">
												<input type="text" class="form-control b-3" name="quantity" id="quantity" value="<?php echo $quantity; ?>">
											</div>

											<p style="font-size: 11px;">("+" For Adding the Stock) & ("-" For Deduct the Stock)</p>
											<p class="help-block"></p>
										</div>
									</div>

									<div class="col-md-2"  id="remark_div">
										<div class="form-group">
											<label for="">Remark</label>
											<div class="abc">
												<textarea class="form-control" name="remark" id="remark" spellcheck="false"> <?php echo $quantity; ?> </textarea>
												
											</div>
											<p class="help-block"></p>
										</div>
									</div>									

									<div class="col-md-2" style="margin-top: 25px;">
										<?php
											if($rights['insert_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
										?>
												<button type="button" id="add_stock" name="add_stock" class="btn sbold blue-ebonyclay"><i class="fa fa-plus"></i> ADD</button>
										<?php
											}
											if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
										?>
												<a class="btn dropdown-toggle btn-success" href="javascript:;" onClick="printReport();" title="Print">Print</a>
										<?php
											}
										?>
									</div>

									<div class="col-md-2" hidden>
										<div class="form-group">
											<label>Invoice No </label>
											<input type="text" class="form-control" name="invoice_no" id="invoice_no" value="<?php echo $invoice_no; ?>" />
											<p class="help-block"></p>
										</div>
									</div> 

									<div class="col-md-2" hidden>
										<div class="form-group">
											<label>invoice Date </label>
											<input type="text" readonly="" class="form-control" name="invoice_date" id="invoice_date" value="<?php echo $invoice_date; ?>" />
											<p class="help-block"></p>
										</div>
									</div> 								
																		                             
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
					<div class="portlet light">						
						<div class="portlet-body">							
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
							<div id="results"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>


<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="js/jquery-aj.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script> 
<script type="text/javascript">
	$("#category_id").fSelect({
	    numDisplayed: 1,
	});
</script>
<script>
	$(document).ready(function() {
		AddstockType();
	});
	function AddstockType(){
		var selectedValue = $("#stock_movement").val();

	    if (selectedValue === '1') {
	      // If "Warehouse To Warehouse" is selected, show and enable the "To Warehouse" field
	      $('#to_warehouse_div').show();
	      $('#to_warehouse_id').prop('disabled', false);
	      $('#remark_div').hide();
	      $('#remark').prop('disabled', true);
	    } else {
	      // Otherwise, hide and disable the "To Warehouse" field
	      $('#to_warehouse_div').hide();
	      $('#to_warehouse_id').prop('disabled', true);
	      $('#remark_div').show();
	      $('#remark').prop('disabled', false);
	    }
	}
</script>
<script type="text/javascript">
	$("#quantity").numeric();
	$('#planning_date').datepicker({
		datepicker: true,
		autoclose: true,
		dateFormat: 'dd-mm-yy',
		maxDate: 0
	});


	$('#invoice_date').datepicker({
		datepicker: true,
		autoclose: true,
		dateFormat: 'dd-mm-yy',
		maxDate: 0
	});

	
</script>

<script type="text/javascript">
var sales_executive_id="<?= $_REQUEST['sales_id']?>";
var type="<?= $_REQUEST['type']?>";
var stock_qty = "";
//var planning_date="<?= $_REQUEST['planning_date']?>";


$("#product_id").change(function() {
	stock_qty = $("#product_id").find('option:selected').data('stock_qty');
	// alert(stock_qty);

	$(".stock_qty1").html(stock_qty);
	// alert(stock_qty);
	var weight_1 = $("#product_id").find('option:selected').data('weight-id');
	$("#weight_id").val(weight_1);
})

$("#add_stock").on("click",function()
{
	var stock_movement = $("#stock_movement").val();
	// var stock_qty =$(".stock_qty1").val();
	if (stock_movement == 1) {
		var to_warehouse_id = $("#to_warehouse_id").val();
	}else{
		var to_warehouse_id = "";
	}
	var product_id = $("#product_id").find('option:selected').data('pid');
	var quantity=$("#quantity").val();
	var planning_date=$("#planning_date").val();
	var p_name = $("#product_id").find('option:selected').data('name');
	// var weight = $("#product_id").find('option:selected').data('weight-id');
	var weight = $("#weight_id").val();

	var remark = $("#remark").val();

	var invoice_no = $("#invoice_no").val();
	var invoice_date = $("#invoice_date").val();
	var warehouse_id = $("#warehouse_id").val();
	var stock_qty = $("#product_id").find('option:selected').data('stock_qty'); 

	if(quantity==0){
		toastr.error("You cannot enter '0' as a QTY");
	}
	else if(quantity<0 && stock_movement==1){
		toastr.error("Please enter QTY greater than zero");
	} 
	else if(stock_qty<quantity && stock_movement==1){
		toastr.error("You cannot transfer QTY to another warehouse more than your current available stock.");
	}
	else{
		if(product_id!="" && planning_date!="" && warehouse_id!="")
		{
			var new_stock=parseFloat(stock_qty)+parseFloat(quantity);
			if(quantity<0 && new_stock<0)
			{  
				toastr.error("You cannot add minus stock QTY more than your Available Stock!!"); 
			}
			else
			{
				$.ajax({
					type: "POST",
					url: "ajax_add_manual_stock.php",
					data: {
						product_id:product_id,
						quantity:quantity,
						planning_date:planning_date,
						p_name:p_name,
						weight:weight,
						remark:remark,

						invoice_no:invoice_no,
						invoice_date:invoice_date,
						warehouse_id:warehouse_id,
						stock_movement:stock_movement,
						to_warehouse_id:to_warehouse_id,

						mode:"insert_stock",
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
							$("#product_id").select2("destroy");
							$("#product_id").val("");
							$("#product_id").select2();

							$("#category_id").trigger("change"); 

							$("#quantity").val("");
							$("#remark").val("");
							$(".stock_qty1").html("");
							getProduction();						 
						}
						else
						{
							toastr.error(msg, 'Error!!')
						}
					}
				});		
			}
		}	
		else
		{
			toastr.error("Please Select Product, Qty, Date & Warehouse.");
		}
	} 
});

var data_url = "manual_stock_get_ajax.php";

function getProduction()
{	
	$.ajax({
		type: "POST",
		url: "manual_stock_get_ajax.php",
		cache: false,
		beforeSend: function() 
		{
			
		},
		success: function(json)
		{
			$("#results").html(json);
		}
	});
}

$(document).ready(function() {
	getProduction();

	var mode = '<?=$_REQUEST['mode']?>';

	if (mode == "add_manual") 
	{

		var category_id = '<?=$_REQUEST['category_id']?>';

		var product_id = '<?=$_REQUEST['product_id']?>';
		//alert(product_id);
		getProductList(category_id,product_id);
	}
	
	$('#product_id').trigger('change');

});

$("#category_id").on('change', function() {
		var tcid = $("#category_id").val();
		getProductList(tcid);
	});

function getProductList(tcid,product_id="") 
{

	// var tcid = $("#category_id").val();
	var cid = $("#customer_id").val();

	var warehouse_id = $("#warehouse_id").val();

	$.ajax({
		type: "post",
		url: "ajax_get_manul_stock_product.php",

		data: "cid=" + cid+"&tcid="+tcid+"&warehouse_id="+warehouse_id+"&product_id="+product_id,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
			// $("#loading-modal").modal('show');
			$('.preloader').fadeIn('slow');
		},
		success: function(result) {
			setTimeout(function() {
				$("#product_id").select2("destroy");
				$("#product_id").val("");
				$('#product_id').html(result);
				$("#product_id").select2();
				var stock_qty = $("#product_id").find('option:selected').data('stock_qty');
			   $(".stock_qty1").html(stock_qty);
			   var weight = $("#product_id").find('option:selected').data('weight-id');
			  
			   $("#weight_id").val(weight);


				// $("#loading-modal").modal('hide');
				$('.preloader').fadeOut('slow');
			});
		}

	})


	// $("#product_id").change(function() {
	// 	var inner_size = $("#product_id").find('option:selected').data('inner_size');
	// 	$(".inner_size").html(inner_size);
	// 	var outer_size = $("#product_id").find('option:selected').data('outer_size');
	// 	$(".outer_size").html(outer_size);
	// })

	// $("#qty").change(function(){
	// 	var bagids = $("#bag_box_id").val();
	// 	var qtys = $("#qty").val();
	// 	var inner_size = $("#product_id").find('option:selected').data('inner_size');
	// 	var outer_size = $("#product_id").find('option:selected').data('outer_size');
	// 	if(bagids==2)
	// 	{
	// 		var new_qty_bag = inner_size*qtys;
	// 		$(".qty").html(new_qty_bag);
	// 	}
	// 	else if(bagids==3)
	// 	{
	// 		var new_qty_box = outer_size*qtys;
	// 		$(".qty").html(new_qty_box);
	// 	}
	// 	else if(bagids==1)
	// 	{
	// 		$(".qty").html(qtys);
	// 	}
	// })

	// if (mode == "add") {
	// 	var l = $("#datatable_1").find('tbody').find('tr').length;
	// 	if (l > 0) {
	// 		alert("You lost all added Product");
	// 		$("#datatable_1").find('tbody').html("");
	// 		recalculateRow();
	// 		recalculateFinalValues();
	// 	}
	// }
}

function del_conf(id)
{
	var r = confirm("Are you sure you want to delete?");
	if(r)
	{
		$.ajax({
			type: "POST",
			url: "ajax_add_manual_stock.php",
			data: {
				id:id,
				mode:"delete_production_planning",
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
					getProduction();	
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			}
		});
	}
}

function printReport() 
{	
	var planning_date = $("#planning_date").val();

	var myWindow =  window.open('manual_stock_new_print.php?planning_date='+planning_date+"&p=1",'','width=500,height=800');
	myWindow.print();

	// setTimeout(function () 
	// {
	// 	myWindow.print();
	// 	var ival = setInterval(function() 
	// 	{
	// 	    myWindow.close();
	// 	    clearInterval(ival);
	// 	}, 200);
	// }, 500);
}

</script>
</body>
</html>