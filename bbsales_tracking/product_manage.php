<?php
$page_id=559;$page_slug='page_product';
$ctable 	= "product";
$ctable1 	= "Product";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable."_manage.php","title"=>$page_title));
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "dashboard.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">			
			<div class="row">
				<div class="col-sm-12">
					<?php $db->printSuccessMessage(); ?>
					<?php $db->printErrorMessage(); ?>
				</div>
				<div class="col-md-12 "><br/>
                    <!-- BEGIN Portlet PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-filter"></i>Filters 
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="slimScrollDiv" style="position: relative;  width: auto; height: auto;">
								
								<!-- <div class="row filter_list">
									<div class="col-md-2 col-xs-12 col-sm-12" style="margin-top:10px">
										<label>Search By Top Category</label>
                                        <div class="form-group">
                                             <select class="form-control status" name="top_category_id" id="top_category_id"  onChange="getTopCat(this.value);">
												<option value="">Select Top Category</option>
                                                 
												 <?php 
													$top_category_list_r=$db->rp_getData('top_category_master',"*","isDelete=0","",0);
													while($top_category_list_d=mysqli_fetch_assoc($top_category_list_r))
													{
														?>
														<option <?php echo ($category_id==$top_category_list_d['id'])?"selected":"" ; ?> value="<?php echo $top_category_list_d['id']?>">
														<?php echo $top_category_list_d['name'];?>
														</option>
														<?php
													}
												?>
                                            </select>
										</div>
                                    </div>
                                    <div class="col-md-2 col-xs-12 col-sm-12" style="margin-top:10px">
                                    	<label>Search By Category</label>
                                        <div class="form-group">
                                             <select class="form-control status" name="category_id" id="category_id"  onChange="getSubCat(this.value);">
												<option value="">--- Select Category ---</option>
                                                 
												 <?php 
													$category_list_d=$db->rp_getData('category_master',"*","isDelete=0","",0);
													while($category_list_r=mysqli_fetch_assoc($category_list_d))
													{
														?>
														<option <?php echo ($category_id==$category_list_r['id'])?"selected":"" ; ?> value="<?php echo $category_list_r['id']?>">
														<?php echo $category_list_r['name'];?>
														</option>
														<?php
													}
												?>		 
												<?php 
												$category_r=$db->rp_getData("category_master","*","isDelete=0",0);
												while($category_d=mysqli_fetch_assoc()){
												?>
												<option value="<?php echo $category_d['id'];?>"><?php echo $category_d['name'];?></option>
												<?php }?>
												
												
                                            </select>
										</div>
                                    </div>    	
                                </div> -->
							   	<div class="row">
							   		 <div class="col-md-5 col-xs-5 col-sm-5">
										<?php
										echo $db->getAddButton($ctable);
										?>	

										<a class="btn btn-primary" href='#addProductUnit' data-toggle='modal'><i class="fa fa-pencil"></i> Add Order Unit</a>
									 </div>
	                            	  <div class="col-md-7 col-xs-7 col-sm-7 pull-right">
                             <div class="form-inline" role="form">
                                    <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
                                       	<div class="form-group">
                                          <input type="text" style="width: 450px!important" placeholder="Search By Product Name/Code  :  " class="form-control input-large" name="searchName" id="searchName" value="" /> 
                                       	</div>

                                       	<div class="form-group">
                                          <input class="btn btn-danger btn-sm" type="submit" value="search">
                                       	</div>

                                       	<div class="form-group">
                                          <input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
                                       	</div>

                                      	<div class="form-group">
											<label class="checkbox-inline" style="font-weight:600;margin-right:10px;">
												<input type="checkbox" id="print_with_price" value="1"> Print with Price
											</label>
										</div>
                                      	<div class="form-group">
			                                    <div class="btn-group">

													<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
														<i class="fa fa-gear"></i>
													</button>
													
													<ul role="menu" class="dropdown-menu dropdown-menu-right pull-right">

														<?php
														if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
														{ ?>
															<li>
																<a name="print" onClick="genproductPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
															</li>
															<li>
																<a name="product_pdf" onClick="genProductPdf()" title="Download Product PDF"><i class="fa fa-file-pdf-o"></i> Product PDF</a>
															</li>
															<?php
														}
														?>

														<?php
														if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
														{ ?>
															<li>
																<a class="excel" name="excel" onClick="genReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
															</li>
														  <?php
														}
														?>
													</ul>
												</div>
	                                       	</div>
                                    </form>
                                    </div>
                                </div>	
								</div>
							</div>
	                                
	                            </div>
                            </div>
                        </div>
                    </div>
					
						<div class="portlet light">
							<div class="table-toolbar">
								<div class="row">
									<!-- <div class="col-md-6">
										<?php
											echo $db->getAddButton($ctable);
										?>	
									</div> -->
									<!-- <div  style="margin-right: 0px;" class="col-md-2 pull-right">
										<div class="btn-group">
											<input type="hidden" name="disp_count" value="<?php echo $count; ?>">
											<button type="submit" name="submit" onClick="document.frm.submit();" class="btn btn-primary btn-flat" >Update</button>
										</div>
									</div>	 -->

								</div>
							</div>
							<div class="portlet-body">
								<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
								<div id="results"></div>
							</div>
						</div>
					
				</div>
			</div>
		</div>
	</div>

<div id="addProductUnit" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>Add Product Unit</div>
					<div class="tools"> 
						<a href="javascript:;" onclick="clearSearchByName();" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
					</div>
				</div>
				<div class="portlet-body portlet-empty" style=""> 
				    <div class="row"> 
				        <div class="col-md-6"> 
			                <label for="email"><b>Select Category</b><code>*</code></label>
			                <select class="form-control tcid1" id="tcid1">
			                    <option value="">Select Category </option>
			                    <?php
			                    $catR = $db->rp_getData("top_category_master", "name,id", "isDelete=0");
			                    if ($catR) 
			                    {
			                        while ($catD = mysqli_fetch_assoc($catR)) 
			                        {
	                            ?>
	                            <option <?= ($customer_type == $catD['id']) ? "selected" : ""; ?> value="<?= $catD['id']; ?>"><?= $catD['name']; ?></option>
	                            <?php
			                        }
			                    }
			                    ?>
			                </select> 
				        </div>
				        <div class="col-md-6"> 
			                <label for="email"><b>Select Sub Category</b></label>
			                <select class="form-control cid1" id="cid1">
			                    <option value="">Select Sub Category </option>
			                    <?php
			                    $subcatR = $db->rp_getData("category_master", "name,id", "isDelete=0");
			                    if ($subcatR) 
			                    {
			                        while ($sub = mysqli_fetch_assoc($subcatR)) 
			                        {
	                            ?>
	                            <option <?= ($customer_type == $sub['id']) ? "selected" : ""; ?> value="<?= $sub['id']; ?>"><?= $sub['name']; ?></option>
	                            <?php
			                        }
			                    }
			                    ?>
			                </select> 
				        </div>
				        <div class="col-md-6" style="margin-top:20px"> 
			                <label for="email"><b>Select Sales Order Unit</b><code>*</code></label>
			                <select class="form-control unit_id1" id="unit_id1">
			                    <option value="">Sales Order Unit</option>
								<?php 
								$order_unit_arr = array("-1"=>"Box","-2"=>"Strip","-3"=>"Pallet","1"=>"Caret","2"=>"Big Box","100"=>"Nos");
								foreach ($order_unit_arr as $key => $value) {   
								?>
								<option <?=($unit_id1==$key)?"selected":""; ?> value="<?= $key ?>"><?= $value ?></option>
								<?php 
								}
								?>
			                </select> 
				        </div>
				        <div class="col-md-6" style="margin-top:20px"> 
			                <label for="email"><b>Select Customer Order Unit</b><code>*</code></label>
			                <select class="form-control customer_unit_id" id="customer_unit_id">
			                    <option value="">Customer Order Unit</option>
								<?php  
								foreach ($order_unit_arr as $key => $value) {   
								?>
								<option <?=($customer_unit_id==$key)?"selected":""; ?> value="<?= $key ?>"><?= $value ?></option>
								<?php 
								}
								?>
			                </select> 
				        </div>
				        <div class="col-md-6" style="margin-top:20px"> 
			                <label for="email"><b>Select Inner Unit</b><code>*</code></label>
			                <select class="form-control inner_unit" id="inner_unit">
			                    <option value="">Inner Unit</option>
								<?php  
								foreach ($order_unit_arr as $key => $value) {   
								?>
								<option <?=($inner_unit==$key)?"selected":""; ?> value="<?= $key ?>"><?= $value ?></option>
								<?php 
								}
								?>
			                </select> 
				        </div>
				        <div class="col-md-6" style="margin-top:20px"> 
			                <label for="email"><b>Select Outer Unit</b><code>*</code></label>
			                <select class="form-control outer_unit" id="outer_unit">
			                    <option value="">Outer Unit</option>
								<?php 
								foreach ($order_unit_arr as $key => $value) {   
								?>
								<option <?=($outer_unit==$key)?"selected":""; ?> value="<?= $key ?>"><?= $value ?></option>
								<?php 
								}
								?>
			                </select> 
				        </div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
			    <button type="button" id="add_unit_btn" class="btn btn-success">Update</button>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>


<script type="text/javascript">

$("#add_unit_btn").on('click',function(){
    var tcid1 = $("#tcid1").val();
    var cid1   = $("#cid1").val();

    var unit_id1   = $("#unit_id1").val();
    var customer_unit_id   = $("#customer_unit_id").val();
    
    var inner_unit   = $("#inner_unit").val();
    var outer_unit   = $("#outer_unit").val();
// alert(unit_id);
    if(tcid1=="" || tcid1==undefined)
    {
    	toastr.error("Please Select Category!!");
    }
    else if(customer_unit_id=="" || customer_unit_id==undefined)
    {
    	toastr.error("Please Select Customer Order Unit!!");
    }
    else if(inner_unit=="" || inner_unit==undefined)
    {
    	toastr.error("Please Select Inner Unit!!");
    }
    else if(outer_unit=="" || outer_unit==undefined)
    {
    	toastr.error("Please Select outer Unit!!");
    }
    else if(unit_id1=="" || unit_id1==undefined)
    {
    	toastr.error("Please Select Sales Order Unit!!");
    }
    else
    {  
	    $.ajax({
	        type: "POST",
	        url: "ajax_add_product_unit.php",
	        data: {
	            tcid: tcid1,
	            cid: cid1,
	            unit_id: unit_id1,
	            customer_unit_id: customer_unit_id,
	            inner_unit: inner_unit,
	            outer_unit: outer_unit,
	        },
	        beforeSend: function() {
	            $(".transCover").fadeIn(800);
	        },
	        success: function(result) 
	        {
	            location.reload();
	        	/*var result = $.parseJSON(result);
	            if (result.ack == 1)
	            { 
	                $(".transCover").fadeOut(100);
	                toastr.success(result.ack_msg); 
	            } 
	            else 
	            {
	                toastr.error(result.ack_msg);
	            }*/
	        }
	    })
	}
});

var searchName="";
var category_id="";
var top_category_id="";
var unit_id="";
var product_type="";
var brand_id="";
var sales_order_unit_filter="";
var data_url = "<?php echo $ctable ?>_get_ajax.php";

function getProductFilterParams()
{
	return {
		searchName: encodeURIComponent(($("#searchName").val() || "").trim()),
		top_category_id: ($("#top_category_id").length ? ($("#top_category_id").val() || "") : ""),
		category_id: ($("#category_id").length ? ($("#category_id").val() || "") : ""),
		unit_id: ($("#unit_id").length ? ($("#unit_id").val() || "") : ""),
		product_type: ($("#product_type").length ? ($("#product_type").val() || "") : ""),
		sales_order_unit_filter: ($("#sales_order_unit_filter").length ? ($("#sales_order_unit_filter").val() || "") : "")
	};
}

function buildProductFilterQuery(params)
{
	return "searchName=" + params.searchName
		+ "&top_category_id=" + params.top_category_id
		+ "&unit_id=" + params.unit_id
		+ "&product_type=" + params.product_type
		+ "&category_id=" + params.category_id
		+ "&sales_order_unit_filter=" + params.sales_order_unit_filter;
}

/*dispay order function*/
function CheckDispalyOrder(id)
{
	var display_order = $("#disp"+id).val();
	var p_id = $("#disp"+id).data("product_id");

	$.ajax({
		type: "POST",
		url: "check_display_order_ajax.php",
		data: 'display_order='+display_order+"&id="+p_id+"&table=product",
		success: function(result){
			result=$.parseJSON(result);
			if(result.ack==1)
			{
				toastr.success("Update Successfully!!","Success");
			}
			else
			{
				toastr.error("Value Already Available","Error");
				var display_order = $("#disp"+id).val(0);
			}
		}
	});
}
/*dispay order function*/
	
function searchByName(){
	displayRecords(500,1);
	return false;
}
function clearSearchByName(){
	$("#searchName").val("");
	if($("#category_id").length){ $("#category_id").select2("val",""); }
	if($("#top_category_id").length){ $("#top_category_id").select2("val",""); }
	if($("#unit_id").length){ $("#unit_id").select2("val",""); }
	if($("#product_type").length){ $("#product_type").select2("val",""); }
	if($("#brand_id").length){ $("#brand_id").select2("val",""); }
	if($("#sales_order_unit_filter").length){ $("#sales_order_unit_filter").select2("val",""); }
	displayRecords(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});

function getTopCat(tcid){
	$.ajax({
		type: "POST",
		url: "ajax_getCategory.php",
		data:'tcid='+tcid,
		success: function(data){
			$("#category_id").html(data);
		}
	});
}
function getSubCat(cid){
	displayRecords(500,1);
}
function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"order": [[1, 'asc']],
		"bFilter": false,
		"bInfo": false,
		"aDataSort": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "5%" }, 
			  { "sWidth": "5%" }, 
			  { "sWidth": "5%" }, 
			  { "sWidth": "30%" }, 
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" }, 
			  { "sWidth": "10%" }, 
			]
	});
}
function displayRecords(numRecords) {
	var params = getProductFilterParams();
	var filterQuery = buildProductFilterQuery(params);
	top_category_id = params.top_category_id;
	category_id = params.category_id;
	unit_id = params.unit_id;
	product_type = params.product_type;
	sales_order_unit_filter = params.sales_order_unit_filter;
	searchName = params.searchName;

	$('.preloader').fadeIn('slow');
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&" + filterQuery, function(){
		$('.preloader').fadeOut('slow');
		loadDataTable();
		if(top_category_id != ""){
			getCategory(top_category_id, category_id);
		}
	});
	
	$("#results").off("click.productPager").on( "click.productPager", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show();
		var page = $(this).attr("data-page");
		$("#results").load(data_url+"?show=" + numRecords + "&" + filterQuery, {"page":page}, function(){
			$(".loading-div").hide();
			loadDataTable();
		});
	});
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {
	displayRecords(500,1);
});
</script>
<script type="text/javascript">
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
</script>
<script>
/*$(document).ready(function() {       
   
});*/
</script>
<script type="text/javascript">
	function genproductPrint()
	{
		var params = getProductFilterParams();
		var myWindow = window.open('print_product_ajax.php?' + buildProductFilterQuery(params),'','width=700,height=800');
     	myWindow.print();
    }

	function genProductPdf()
	{
		var params = getProductFilterParams();
		var withPrice = $("#print_with_price").is(":checked") ? "1" : "0";
		var url = 'product_list_pdf.php?' + buildProductFilterQuery(params) + '&with_price=' + withPrice;
		window.open(url, '_blank');
	}

    function genReport()
	{
		var params = getProductFilterParams();
      	$.ajax({
			method: "POST",
			url: "product_genReport_ajax.php",
			data:{
        		searchName: decodeURIComponent(params.searchName.replace(/\+/g, ' ')),
        		category_id: params.category_id,
        		top_category_id: params.top_category_id,
        		unit_id: params.unit_id,
        		product_type: params.product_type,
        		sales_order_unit_filter: params.sales_order_unit_filter,
			},
			dataType : 'json',
			beforeSend: function() {
				// $("#loading-modal").modal('show');
				$('.preloader').fadeIn('slow');
			},
			success: function(result){
	        		// $("#loading-modal").modal('hide');
        		$('.preloader').fadeOut('slow');
        		window.location.href="<?=SITEURL?>"+result.file_path;
        	},
		});

    }



    /*this is for Vsible Or Not Only*/

    // function visible_or_not(id){

	// 		if ($("#visible_or_not_id"+id).is(':checked') == true)
	// 	 	{
	// 	 		isVisible = 1;
	// 	    }
	// 	    else
	// 	    {
	// 	    	isVisible = 0;
	// 	    }

    //         $.ajax({
    //             type: "POST",
    //             url:"check_visible_or_not_ajax.php", 
	// 			// data: 'id='+id, 
	// 			data: 'id='+id+"&isVisible="+isVisible, 
                
    //             success: function(result){
	// 				result=$.parseJSON(result);
	// 				if(result.ack==1)
	// 				{
	// 					toastr.success("Update Successfully!!","Success");
	// 				}
	// 				else
	// 				{
	// 					toastr.error("Data Update Failed!!","Error");
	// 				}
	// 			}
    //         });
	// }

	/*this is for Vsible Or Not Only*/

	/*this is for Vsible Or Not Only*/

    function visible_or_not(id,type){
    		var isVisible=type;
			// if ($("#visible_or_not_id"+id).is(':checked') == true)
		 	// {
		 	// 	isVisible = 1;
		    // }
		    // else
		    // {
		    // 	isVisible = 0;
		    // }

            $.ajax({
                type: "POST",
                url:"check_visible_or_not_ajax.php", 
				// data: 'id='+id, 
				data: 'id='+id+"&isVisible="+isVisible, 
                
                success: function(result){
					result=$.parseJSON(result);
					if(result.ack==1)
					{
						toastr.success("Update Successfully!!","Success");
					}
					else
					{
						toastr.error("Data Update Failed!!","Error");
					}
					displayRecords(500,1);
				}
            });
	}

	/*this is for Vsible Or Not Only*/

	function getCategory(id,category_id="")
	{
		$.ajax({
			type: "POST",
			url: "ajax_category_grid.php",
			data: 'tcid='+id+'&cid='+category_id,
			success: function(result){
					$("#category_id").select2("destroy");
					$("#category_id").html(result);
					$("#category_id").select2();
				}
		});
	}
</script>
</body>
</html>