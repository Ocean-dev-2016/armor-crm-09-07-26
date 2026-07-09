<?php 
$page_id=616;$page_slug='production_planning_page';
$ctable 	= "production_planning";
$ctable1 	= "Production Planning";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Production Planning";
$page_hierarchy=array(array("link"=>"","title"=>"Production"),array("link"=>"meeting_manage.php","title"=>"Production Planning"));
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
                            	Production Planning
                        	</div>
                        </div>
                        <div class="portlet-body">
                             <div class="slimScrollDiv">
								<div class="row"> 
								   <!-- <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">   --> 

										<div class="col-md-3">
											<div class="form-group">
												<label> Product <code>*</code></label>
												<select class="form-control b-3" id="product_id" name="product_id">
							                        <option value="">Select Product</option>
							                        <?php
							                        $product_r = $db->rp_getData("product","*","isDelete=0 AND isActive=1");
							                        if($product_r)
							                        {
							                            while($product_d = mysqli_fetch_assoc($product_r))
							                            {
							                            	$product_weight = $db->rp_getData("product_weight_price","weight_id,catno","product_id='".$product_d['id']."' AND isDelete=0");
															while($product_weight_d = mysqli_fetch_assoc($product_weight))
															{
																$weight_name = $db->rp_getValue("weight","name","id='".$product_weight_d['weight_id']."' AND isDelete=0 AND id!='-1'",0);

																$name = $db->rp_getValue("weight","name","id='".$product_weight_d['weight_id']."'");

																$pro_name  = $product_d['name'];

																$name1= htmlentities($name." ".$pro_name." ");
							                            		?>
							                                	<option class="pids_<?=$product_d['id']."_".$product_weight_d['weight_id']; ?>" data-weight-id="<?php echo $product_weight_d['weight_id']?>" data-name="<?php echo $name1; ?>"  data-pid="<?php echo $product_d['id']?>" data-cat_no="<?= $product_weight_d['catno'] ?>" value="<?=$product_d['id']; ?>" <?=($product_id == $product_d['id'])?"selected":"";?>><?=($weight_name!="")?$product_d['name'] ." - ". $weight_name:$product_d['name']." - ".$product_weight_d['catno']?></option>
							                            		<?php
							                        		}
							                            }
							                        } 
							                        ?>
									            </select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="">Qty<code>*</code></label>
												<div class="abc">
													<input type="text" class="form-control b-3" name="quantity" id="quantity" value="<?php echo $quantity; ?>">
												</div>
												<p class="help-block"></p>
											</div>
										</div>	
										<div class="col-md-2">
											<div class="form-group">
												<label>Planning Date <code>*</code></label>
												<input type="text" readonly="" class="form-control" name="planning_date" id="planning_date" value="<?php echo $planning_date; ?>" />
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-2" style="margin-top: 25px;">
											<button type="button" id="add_production" name="add_production" class="btn sbold blue-ebonyclay"><i class="fa fa-plus"></i> ADD</button>
										</div>  

	                                    <!-- <div class="col-md-2" style="margin-top: 25px;">
											<div class="form-group">
		                                     <input class="btn btn-danger btn-sm" type="submit" value="search">
		                             	    </div>   
	                             	    </div>   --> 
	                                <!-- </form> -->                      
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

<script type="text/javascript">
	$("#quantity").numeric();
	$('#planning_date').datepicker({
		datepicker: true,
		autoclose: true,
		dateFormat: 'dd-mm-yy',
		maxDate: 0
	});
</script>

<script type="text/javascript">
var sales_executive_id="<?= $_REQUEST['sales_id']?>";
var type="<?= $_REQUEST['type']?>";
// var df1="";

// function searchByName(){
// 	searchName = $("#searchName").val();
	
// 	df1=$("#material_request_filter_input").val();
// 	df1 = encodeURI(df1);
// 	displayRecords(500,1);
// 	// displayRecords_outlets(500,1);
// 	return false;
// }

$("#add_production").on("click",function()
{
	var product_id=$("#product_id").val();
	var quantity=$("#quantity").val();
	var planning_date=$("#planning_date").val();
	var p_name = $("#product_id").find('option:selected').data('name');
	var weight = $("#product_id").find('option:selected').data('weight-id');
	if(product_id!="")
	{
	  if(planning_date!="")
	  {
		$.ajax({
		type: "POST",
		url: "ajax_add_production_planning.php",
			data: {
				product_id:product_id,
				quantity:quantity,
				planning_date:planning_date,
				p_name:p_name,
				weight:weight,
				mode:"add_production_planning",
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
					$("#product_id").select2('val',"");
					$("#quantity").val();
					getProduction();	
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
	  	toastr.error("Please Select Planning Date.");
	  }
		
	}	
	else
	{
		toastr.error("Please Select Product And Qty.");
	}
});

var data_url = "production_planning_get_ajax.php";



function getProduction()
{	

	// var df1=$("#material_request_filter_input").val();
	// df1 = encodeURI(df1);

	$.ajax({
		type: "POST",
		url: "production_planning_get_ajax.php",
		// data: {
		// 		df1:df1,
				
				
		// 	},
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

function del_conf(id)
{
	var r = confirm("Are you sure you want to delete?");
	if(r)
	{
		$.ajax({
			type: "POST",
			url: "ajax_add_production_planning.php",
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
</script>
</body>
</html>