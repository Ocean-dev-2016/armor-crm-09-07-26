<?php
$page_id=580;$page_slug='price_list_master';
$ctable 	= "price_list";
$ctable1 	= "Price List";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1));
include("connect.php");
$price_list_id=$_REQUEST['pid'];
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "pricelist_master_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-md-12"> 
				<div class="col-md-12 "><br/>
                    <!-- BEGIN Portlet PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <span class="caption-subject bold  uppercase"><?= $db->rp_getValue("price_list","pricelist_name","id='".$price_list_id."'"); ?>  </span>	Price List </div> 
                                <?php $is_premium = $db->rp_getValue("price_list","is_premium","id='".$price_list_id."'"); ?>
                        </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
					<div class="portlet light">
					
						<div class="table-toolbar">
							<div class="row">
									<div class="col-md-12" style="margin-bottom: 10px;">
										<button type="button" name="inport_btn" value="Import Pricelist" onClick="importData()" class="btn btn-primary "><i class="fa fa-upload"></i> Import Pricelist</button>
										<!-- <button type="submit" name="submit" value="print" onClick="printData()" class="btn yellow "><i class="fa fa-print"></i> Print</button> -->
									</div>
								</div>
							<div class="row">
								<!-- filter -->
								<div class="col-sm-2">
									<div class="form-group  has-info filterBtn">
										<select class="form-control edited" value="<?php echo $tcid; ?>"  id="tcid" name="tcid" data-validation="required" onChange="getSubcategory(this.value);">
											<option value="">Select Top Category </option>
											<?php
											$Categories=$db->rp_getData("top_category_master","name,id","isDelete=0","name ASC");
											if($Categories)
											{
												while($C=mysqli_fetch_assoc($Categories))
												{
											?>
											<option <?= ($RequestedData['tcid']==$C['id'])?"selected":""; ?> value="<?=$C['id']?>"><?=$C['name']?></option>
											<?php
											}
											}
											?>
										</select>
										<span class="help-block"></span>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group  has-info filterBtn">
										<select class="form-control edited" value="<?php echo $subcat_id; ?>"  id="subcat_id" name="subcat_id" data-validation="required" onChange="clearDis();">
										<option value="">Select Category </option>
										</select>
										<span class="help-block"></span>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group  has-info">
										<div class="input-group">
										<input type="text" class="form-control positive_float" name="discount" id="discount">
										<span class="input-group-addon" id="rm_unit_display" name="fg_unit_display" value="">%</span>
									</div>
									</div>
								</div>
								<div class="col-sm-4">
									<button class="btn btn-success updateBtn btn-sm" type="submit" value="search">Apply</button>
									<!-- <button style="margin-left: 50px;" class="btn btn-primary filterBtn" type="submit" value="search">Search</button> -->
									<button class="btn btn-danger ClearBtn btn-sm" type="submit" value="search">Clear</button>

									<?php
										$selling_price_check = $db->rp_getTotalRecord("product_price_list","isDelete=0 AND isActive=1 AND price_list_id=".$_REQUEST['pid']."",0);

										if ($selling_price_check > 0) 
										{
									?>
											<button type="submit" name="removealldiscount" value="removealldiscount" style="font-weight: bold;color: #fff;" onClick="removealldiscount(<?=$selling_price_check?>)" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Remove All Discounted Selling Price</button>	
									<?php
										}
									?>
									<!-- filter -->
								</div>
								<div class="col-md-2 pull-right" style="margin-bottom: 10px;">
									<button type="submit" name="submit" value="print" style="background-color: #f0ad4e;color: #fff;" onClick="printData()" class="btn yellow pull-right btn-sm"><i class="fa fa-print"></i> Print</button>	
								</div>
								<div class="col-sm-12">	
									<div id="price_list_data"></div>
								</div>
							</div>
						</div>
						<div class="portlet-body">
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> 
</div> 

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>

<script type="text/javascript">
	var price_list_id="<?= $price_list_id; ?>";
	var current_date="<?= $current_date; ?>";
	$(".discounted_price").numeric();	
	$("#discount").numeric();

	$(document).ready(function()
	{
		getData();
	});
	function importData()
		{
			if(price_list_id!="")
			{
				var tcid = $("#tcid").val();
				var cid = $("#subcat_id").val();
				window.location.href = 'import_pricelist_data_crud.php?price_list_id='+price_list_id+"&tcid="+tcid+"&cid="+cid;
			}
		}


	$(".positive_float").keyup(function(event) { 
		
		var tcid=$("#tcid").val();
		var cid=$("#subcat_id").val();
		
		if ( event.keyCode == 46 || event.keyCode == 8 ) {
		// let it happen, don't do anything
		} 
		else if (/[^\d\.]/g.test(this.value)) 
		{
			toastr.error("Only Digits Allowed");
			this.value = this.value.replace(/[^\d\.]/g, '');
		}
		else if(this.value==0) 
		{
			toastr.error("Zero Value not Allowed");
			$("#discount").val("");
		}
		else if(this.value>=100) 
		{
			toastr.error("Discount Not more than 100 Allowed");
			$("#discount").val("");
		}
		else if(tcid=="")
		{
			toastr.error("Please Select Top Category First");
			$("#discount").val("");
		}
		/*else if(cid=="")
		{
			toastr.error("Please Select Category First");
			$("#discount").val("");
		}*/
		else
		{
			toastr.options.closeButton = true;

			// toastr.warning('ડિસ્કાઉન્ટ એ પ્રોડક્ટ પર લાગુ કરવામાં આવશે નહીં જેની ડિસ્કાઉન્ટ કિંમત ન્યૂનતમ કિંમત કરતાં ઓછી છે.');
			
			toastr.warning('The Discount Will Not Be Applied To The Product Whose Discount Price is Lower Than The Minimum Price.');

			// toastr.warning('In Which Product Minimum selling price is greater then or equal then the discount percentage apply another the discount not apply in the product.');
			checkDisocuntPercentage(tcid,cid,this.value);
		}
	});
	function checkDiscountNet(pid_weight_id_pricelist)
	{
		var discounted_selling_price=$("#discounted_price"+pid_weight_id_pricelist).val();
		var mrp_price=$("#mrp_price"+pid_weight_id_pricelist).val();
		var min_sell_price=$("#min_sell_price"+pid_weight_id_pricelist).val();		
		discounted_amount=mrp_price-discounted_selling_price;
		discounted_amount = discounted_amount.toFixed(4);
		discount=(discounted_amount*100)/mrp_price;
		discount=discount.toFixed(2);
		var disval="<?= CURR; ?>"+discounted_amount+" ("+discount+"%)";
		$("#dis_val"+pid_weight_id_pricelist).html(disval);		
	}
	function checkDisocuntPercentage(tcid,cid,discount)
	{
		$(".positive_float").on("change",function(event)
		{ 
			// $('.mrp'+tcid+cid).each(function () {
			if(cid!="")			
			{
				var a='mrp'+tcid+cid;
			}
			else
			{
				var a='mrp_tcid'+tcid;
			}
			$('.'+a).each(function () {			
				var is_premium = '<?= $is_premium ?>';
				var	mrp=parseFloat($(this).val());
				var min_sell_price=$(this).data("min-sell-price");
				var pid_weight_id=$(this).data("pid-weight_id");
				var discounted_amount=(mrp*discount)/100;
				discounted_amount = discounted_amount.toFixed(4);
				// alert(mrp);
				// alert(discounted_amount);
				var discounted_price=mrp-discounted_amount;

				// discounted_price = Math.floor(discounted_price* 100);
				if(is_premium==1)
				{
					$("#discounted_price"+pid_weight_id).val(discounted_price.toFixed(4));
					var disval="<?= CURR; ?>"+discounted_amount+" ("+discount+"%)";
					$("#dis_val"+pid_weight_id).html(disval);
				}
				else if ((parseFloat(discounted_price) >= parseFloat(min_sell_price)) && is_premium==0) {
					$("#discounted_price"+pid_weight_id).val(discounted_price.toFixed(4));
					var disval="<?= CURR; ?>"+discounted_amount+" ("+discount+"%)";
					$("#dis_val"+pid_weight_id).html(disval);
				} else {
					$("#discounted_price"+pid_weight_id).val("");
					$("#dis_val"+pid_weight_id).html("");
				}
			});
		});
	}
	
	function clearDis()
	{
		$("#discount").val("");		
	}
	function getSubcategory(id)
	{		
		$("#discount").val("");
		$.ajax({
            type: "POST",
            url: "ajax_getsubcategory.php",
            data: 'id='+id,
            success: function(result){
                $("#subcat_id").html(result);
            }
        });
	}
	function getData()
	{
		var tcid=$("#tcid").val();
		var cid=$("#subcat_id").val();	
		
		$.ajax({
            type: "POST",
            url: "product_price_list_data_get_ajax.php",
            data: 'tcid='+tcid+'&cid='+cid+'&price_list_id='+price_list_id,
            beforeSend:function(){             
	            // $("#loading-modal").modal('show');
	            $('.preloader').fadeIn('slow');
	        },
            success: function(result){
                $("#price_list_data").html(result);
             	// $("#loading-modal").modal('hide'); 
             	$('.preloader').fadeOut('slow');

            }
        });
	}

	$(".ClearBtn").on("click",function()
	{
		/*$("#tcid").val("");*/
		$('#tcid').val(null).trigger('change');
		$('#subcat_id').val(null).trigger('change');
		$("#subcat_id").val("");
		$("#discount").val("");
		getData();
	});
	

	function addPT(pid,weight_id,price_list_id,discount_type,alert="")
	{
		var discount=$("#discounted_price"+pid+weight_id+price_list_id).val();	
		var cat_id=$("#cat_id"+pid+weight_id+price_list_id).val();	
		var sub_cat_id=$("#sub_cat_id"+pid+weight_id+price_list_id).val();	
		var mrp_price=$("#mrp_price"+pid+weight_id+price_list_id).val();
		var discount_type1=$(".discount_check_val"+pid+weight_id+price_list_id).val();
		if(discount!="" && discount!=0)
		{	
			if(alert=="")
			{
				var r=confirm("Are you sure to Apply Discount to this Product??");
			}
			else
			{
				var r=true;
			}
			if(r)
			{	
				$.ajax({
		            type: "POST",
		            url: "update_product_price_list.php",
		            data: 'tcid='+cat_id+'&cid='+sub_cat_id+'&pid='+pid+'&weight_id='+weight_id+'&mrp_price='+mrp_price+'&price_list_id='+price_list_id+'&discount='+discount+'&discount_type='+discount_type1+'&mode=net',
		            beforeSend:function(){             
			            // $("#loading-modal").modal('show');
			            $('.preloader').fadeIn('slow');
			        },
		            success: function(result){
		                // getData();
		             	// $("#loading-modal").modal('hide'); 
		             	$('.preloader').fadeOut('slow');
		             	// $("#last_updated_date"+pid+weight_id+price_list_id).html(current_date);
		            }
		        });
			}
		}
		else
		{
			toastr.error("Please Enter Proper Discount Value");
		}
	}

	$(".updateBtn").on("click",function()
	{
		var tcid=$("#tcid").val();
		var cid=$("#subcat_id").val();
		var discount=$("#discount").val();
		if(tcid!="")
		{
			/*if(cid!="")
			{*/
				if(discount!="")
				{			
					var r=confirm("Are you sure to Apply Discount to this Product??");
					if(r)	
					{	
						if(cid!="")			
						{
							var a='mrp'+tcid+cid;
						}
						else
						{
							var a='mrp_tcid'+tcid;
						}
						$('.'+a).each(function () {			
							var	mrp=parseFloat($(this).val());
							var pid_weight_id=$(this).data("pid-weight_id");
							var pid=$(this).data("pid");
							var weight_id=$(this).data("weight-id");
							var pid_weight_id=$(this).data("pid-weight_id");
							var discount=$("#discounted_price"+pid_weight_id).val();
							addPT(pid,weight_id,price_list_id,"1","1");							
						});
					}					
				}
				else
				{
					toastr.error("Please Enter Discount");
				}
			/*}
			else
			{
				toastr.error("Please Select Category");
			}*/
		}
		else
		{
			toastr.error("Please Select Top Category");
		}
	});

	$(".filterBtn").on("change",function()
	{
		var tcid=$("#tcid").val();
		var cid=$("#subcat_id").val();
		if(tcid!="" || cid!="")
		{
			getData();
		}
		else
		{
			toastr.error("Please Select Top Category/Category");
		}
	});
</script>
<script type="text/javascript">

	function del_conf(pid,weight_id,price_list_id)
	{
		var r=confirm("Are you sure to Remove this Product From Pricelist??");
		if(r)
		{			
			$.ajax({
	        type: "POST",
	        url: "update_product_price_list.php",
	        data: 'pid='+pid+'&weight_id='+weight_id+'&price_list_id='+price_list_id+'&mode=delete',
	        beforeSend:function(){             
	            // $("#loading-modal").modal('show');
	            $('.preloader').fadeIn('slow');
	        },
	        success: function(result){
	            getData();
	         	// $("#loading-modal").modal('hide'); 
	         	$('.preloader').fadeOut('slow');
	        }
	    	});
		}
	}

	
	function printData()
	{
		var tcid=$("#tcid").val();
		var cid=$("#subcat_id").val();
		var myWindow = window.open('print_price_list.php?tcid='+tcid+"&p=1&cid="+cid+"&price_list_id="+price_list_id,'','width=1000,height=800');
	}

	function removealldiscount(price_list)
	{
		if (price_list > 0) 
		{
			var r=confirm("Are you sure to Remove All Product From Pricelist??");
			if(r)
			{			
				var pid = '<?=$_REQUEST['pid']?>';

				if (pid!="") 
				{
					$.ajax({
			        type: "POST",
			        url: "update_product_price_list.php",
			        data: 'pid='+pid+'&mode=delete_all_price_list',
			        beforeSend:function(){             
			            // $("#loading-modal").modal('show');
			            // $('.preloader').fadeIn('slow');
			        },
			        success: function(result){
			         	// $("#loading-modal").modal('hide'); 
			         	// $('.preloader').fadeOut('slow');
			         	toastr.success("Successfully Remove All Price List");
			            getData();
			        }
			    	});
				}
				else
				{
					toastr.error("Something went to wrong");
				}
			}
		}
		else
		{
			toastr.error("Pricelist Data No Found");
		}
	}
</script>
</body>
</html>