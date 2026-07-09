<?php 
$page_id=650;$page_slug='customer_manual_stock';
$ctable 	= "production_planning";
$ctable1 	= "Add Customer Manual Stock";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = " Customer Manual Stock";
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>"meeting_manage.php","title"=>"Customer Manual Stock"));
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
                            	Customer Manual Stock
                        	</div>
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv">
								<div class="row">
									<div class="col-md-2">
										<div class="form-group">
											<label>Date<code>*</code></label>
											<input type="text" readonly="" class="form-control" name="planning_date" id="planning_date" value="<?php echo Date('d-m-Y'); ?>" />
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Sales Executive <code>*</code></label>
											<select class="form-control" name="sales_id" id="sales_id">
												<option value="">Select Sales Executive</option>
												<?php
												$sales_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1","",0);
												while($sales_d=mysqli_fetch_assoc($sales_r))
												{
												?>
												<option value="<?= $sales_d['id'] ?>"><?= $sales_d['name'] ?></option>
												<?php
												}
												?>
											</select>
											<p class="help-block"></p>
										</div>
									</div>

									<div class="col-md-2">
										<div class="form-group">
											<label>Customer <code>*</code></label>
											<select class="form-control" name="customer_id" id="customer_id">
												<option value="">select Customer</option>
												<?php
												$customer_r=$db->rp_getData("executive","*","isDelete=0 ","",0);
												while($customer_d=mysqli_fetch_assoc($customer_r))
												{
												?>
												<option value="<?= $customer_d['id'] ?>"><?= $customer_d['company_name'] ?></option>
												<?php
												}
												?>
											</select>
											<p class="help-block"></p>
										</div>
									</div>




									<div class="col-md-2">
										<div class="form-group">
											<label>Category <code>*</code></label>
											<select class="form-control" name="category_id[]" id="category_id" multiple="multiple">
												<option value="">select Category</option>
												<?php
												$cat_r=$db->rp_getData("top_category_master","*","isDelete=0 AND isActive=1",1);
												while($cat_d=mysqli_fetch_assoc($cat_r))
												{
												?>
												<option value="<?= $cat_d['id'] ?>"><?= $cat_d['name'] ?></option>
												<?php
												}
												?>
											</select>
											<p class="help-block"></p>
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label> Product <code>*</code></label>
											<select class="form-control b-3" id="product_id" name="product_id">
						                        <option value="">Select Product</option>
						                        <!-- <?php
						                        $product_r = $db->rp_getData("product","*","isDelete=0 AND isActive=1");
						                        if($product_r)
						                        {
						                            while($product_d = mysqli_fetch_assoc($product_r))
						                            {
						                            	$product_weight = $db->rp_getData("product_weight_price","weight_id,catno,stock_qty,id","product_id='".$product_d['id']."' AND isDelete=0");
														while($product_weight_d = mysqli_fetch_assoc($product_weight))
														{
															$weight_name = $db->rp_getValue("weight","name","id='".$product_weight_d['weight_id']."' AND isDelete=0 AND id!='-1'",0);

															$name = $db->rp_getValue("weight","name","id='".$product_weight_d['weight_id']."'");

															$pro_name  = $product_d['name'];

															$name1= htmlentities($name." ".$pro_name." ");
						                            		?>
						                                	<option class="pids_<?=$product_d['id']."_".$product_weight_d['weight_id']; ?>" data-weight-id="<?php echo $product_weight_d['weight_id']?>" data-name="<?php echo $name1; ?>" data-stock_qty="<?php echo $product_weight_d['stock_qty']; ?>"  data-pid="<?php echo $product_d['id']?>" data-cat_no="<?= $product_weight_d['catno'] ?>" value="<?=$product_weight_d['id']; ?>" <?=($product_id == $product_d['id'])?"selected":"";?>><?=($weight_name!="")?$product_d['name'] ." - ". $weight_name:$product_d['name']." - ".$product_weight_d['catno']?></option>
						                            		<?php
						                        		}
						                            }
						                        } 
						                        ?> -->
								            </select>
								            <input type="hidden" name="weight_id" id="weight_id" value="">
										</div>
									</div>


									<!-- <div class="col-md-2">
										<div class="form-group">

											<label style="margin-top: 30px;">Current Stock Qty : </label>
											<strong><span class='stock_qty1'></span></strong>
										</div>
									</div>	 -->
								</div>
								<div class="row">

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
									<div class="col-md-2">
										<div class="form-group">
											<label>Expiry Date </label><code>*</code>
											<input type="text" readonly="" class="form-control" name="expiry_date" id="expiry_date" value="" />
											<p class="help-block"></p>
										</div>
									</div> 

									<div class="col-md-3">
										<div class="form-group">
											<label for="">Remark</label><code>*</code>
											<div class="abc">

												<textarea class="form-control" name="remark" id="remark" spellcheck="false"> <?php echo $quantity; ?> </textarea>
												
											</div>
											<p class="help-block"></p>
										</div>
									</div>	
									
									<div class="col-md-1" style="margin-top: 25px;">
										<button type="button" id="add_stock" name="add_stock" class="btn sbold blue-ebonyclay"><i class="fa fa-plus"></i> ADD</button>
									</div>                               

									

									

								</div>
								<div class="row">

									                               

                                </div>
                                

                            </div>
                            </div>
                    </div>
                    <div class="row">
	<div class="col-md-2">
		<div class="form-group">
			<label>To Date</label>
    			<input type="text" name="to_date" id="to_date" class="form-control" readonly>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<label>From Date</label>
    			<input type="text" name="from_date" id="from_date" class="form-control" readonly>
		</div>
	</div>
	<div class="col-md-1">
		<div class="form-group">
			<label></label>
			<button type="button" onclick="getProduction();" class="btn sbold btn-success" style="margin-top: 20px;"><i class="fa fa-calendar"></i>Filter</button>
		</div>
	</div>
	<div class="col-md-1" style="margin-top: 20px;">
			<a class="btn dropdown-toggle btn-success" href="javascript:;" onClick="printReport();" title="Print" style="background-color: #f0ad4e;color: #fff;"><i class="fa fa-print"></i>Print</a>
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
		$("#warehouse_id").fSelect();
		$("#customer_id").select2();

	</script>

<script type="text/javascript">
	$("#quantity").numeric();
	$('#planning_date').datepicker({
		datepicker: true,
		autoclose: true,
		dateFormat: 'dd-mm-yy',
		maxDate: 0
	});


	$('#expiry_date').datepicker({
		datepicker: true,
		autoclose: true,
		dateFormat: 'dd-mm-yy',
		// maxDate: 0
	});

	$('#to_date').datepicker({
		datepicker: true,
		autoclose: true,
		dateFormat: 'dd-mm-yy',
		// maxDate: 0
	});
	$('#from_date').datepicker({
		datepicker: true,
		autoclose: true,
		dateFormat: 'dd-mm-yy',
		// maxDate: 0
	});


	
</script>

<script type="text/javascript">
var sales_executive_id="<?= $_REQUEST['sales_id']?>";
var type="<?= $_REQUEST['type']?>";
//var planning_date="<?= $_REQUEST['planning_date']?>";



$("#product_id").change(function() {

	var stock_qty = $("#product_id").find('option:selected').data('stock_qty');
	$(".stock_qty1").html(stock_qty);

	var weight_1 = $("#product_id").find('option:selected').data('weight-id');
	$("#weight_id").val(weight_1);


})

$("#add_stock").on("click",function()
{
	// var product_id=$("#product_id").val();

	var product_id = $("#product_id").find('option:selected').data('pid');
	var quantity=$("#quantity").val();
	var planning_date=$("#planning_date").val();
	var p_name = $("#product_id").find('option:selected').data('name');
	// var weight = $("#product_id").find('option:selected').data('weight-id');
	var weight = $("#weight_id").val();
	var remark = $("#remark").val();
	var customer_id = $("#customer_id").val();
	var expiry_date = $("#expiry_date").val();
	var sales_id = $("#sales_id").val();

	// var invoice_no = $("#invoice_no").val();
	// var warehouse_id = $("#warehouse_id").val();
	if(product_id!="" && planning_date!="" && customer_id!="" && quantity!="" && expiry_date!=""  && remark!=""  && sales_id!="")
	{
		$.ajax({
		type: "POST",
		url: "ajax_add_customer_manual_stock.php",
			data: {
				product_id:product_id,
				quantity:quantity,
				planning_date:planning_date,
				p_name:p_name,
				weight:weight,
				remark:remark,	
				expiry_date:expiry_date,
				customer_id:customer_id,
				sales_id:sales_id,
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
					// $("#product_id").select2('val',"");/
					$("#quantity").val("");
					// $("#remark").val("");
					$(".stock_qty1").html("");
					getProduction();
					// $('#product_id').select2('open');	
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
		toastr.error("Please Fill up Require Fields");
	}
});

var data_url = "manual_stock_get_ajax.php";

function getProduction()
{	
	var to_date=$("#to_date").val();
	var from_date=$("#from_date").val();
	$.ajax({
		type: "POST",
		url: "customer_manual_stock_get_ajax.php?to_date="+to_date+"&from_date="+from_date,
		cache: false,
		beforeSend: function() {
			
		},
		success: function(json)
		{
			$("#results").html(json);
		}
	});
}

$(document).ready(function() {
	getProduction();
});


		$("#category_id").on('change', function() {
				var tcid = $("#category_id").val();
				getProductList(tcid);
			});

		function getProductList(tcid) {

			// var tcid = $("#category_id").val();
			var cid = $("#customer_id").val();

			$.ajax({
				type: "post",
				url: "ajax_get_manul_stock_product.php",

				data: "cid=" + cid+"&tcid="+tcid,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {

					/*var cd=$("#customer_id").find("option:selected").data("cash-discount");
					$("#cash_discount").val(cd);
					var ad=$("#customer_id").find("option:selected").data("add-discount");
					$("#additional_discount").val(ad);*/
					setTimeout(function() {
						$('#product_id').html(result);
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
			url: "ajax_add_customer_manual_stock.php",
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

	var myWindow =  window.open('customer_manual_stock_new_print.php?planning_date='+planning_date+"&p=1",'','width=500,height=800');
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