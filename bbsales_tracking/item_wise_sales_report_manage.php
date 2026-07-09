<?php
$page_id=630;$page_slug='to_do_list';
$ctable 	= "Product";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Item Wise Sales Report";
$page_hierarchy=array(array("link"=>"","title"=>"Report"),array("link"=>"item_wise_sales_report_manage.php","title"=>$page_title));
 
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>

<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/> 
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $redirect;?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-xl-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
				<div class="col-xl-12">
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
                            <div class="slimScrollDiv" style="position: relative; /*overflow: hidden;*/ width: auto; height: auto;">
								<div class="row filter_list"> 
									<div class="col-md-4 col-xs-4 col-sm-4" style="margin-top:10px">
							  			<label>Filter By Date:</label>
							  			<div class="input-group">
											<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
											<span class="input-group-addon datetimerange-picker-btn">
											<i class="fa fa-calendar"></i>
											</span>
									
											<span class="input-group-btn">
							          		<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
							        		</span>
							        	</div>
                                   	</div>
                                </div>
                                <div class="row filter_list"> 
									<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<label>Search By Category</label><br/>
								  		<div class="form-group" role="form">
											<select class="form-control" name="top_category_id" id="top_category_id">
						                		<option value="">Select Category</option>
						                		<?php
						               			$TopcatR = $db->rp_getData("top_category_master","id,name","","",0);
						               			while ($TopcatD = mysqli_fetch_assoc($TopcatR))
						               			{
				                				?>
				                				<option value="<?=$TopcatD['id']?>"><?=$TopcatD['name']?></option>
				                				<?php
						               			}
						                		?>
						                	</select>
								        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<label>Search By Sub Category</label><br/>
								  		<div class="form-group" role="form">
											<select class="form-control" name="category_id" id="category_id">
						                		<option value="">Select Sub Category</option>
						                		<?php
						               			$sub_cat_r = $db->rp_getData("category_master","id,name","","",0);
						               			while ($sub_cat_d = mysqli_fetch_assoc($sub_cat_r))
						               			{
				                				?>
				                				<option value="<?=$sub_cat_d['id']?>"><?=$sub_cat_d['name']?></option>
				                				<?php
						               			}
						                		?>
						                	</select>
								        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<label>Search By Item Name</label><br/>
								  		<div class="form-group" role="form">
											<select class="form-control" name="item_name" id="item_name">
						                		<option value="">Item Name</option>
						                		<?php
					                			$D_r = $db->rp_getData("product","id,name","","",0);
					                			while ($D = mysqli_fetch_assoc($D_r))
					                			{
					                				$product_weightR = $db->rp_getData("product_weight_price","weight_id,catno","product_id='".$D['id']."' AND isDelete=0"); 
					                				while($product_weightD = mysqli_fetch_assoc($product_weightR))
					                				{ 
					                					$weight_name = $db->rp_getValue("weight","name","id='".$product_weightD['weight_id']."' AND isDelete=0");

				                				?>
				                				<option value="<?=$D['name']?>" <?=($sid == $D['id'])?"selected":"";?>><?=$D['name']." - ".$weight_name." - ".$product_weightD['catno']?></option>
				                				<?php
						                				}
						                			}
						                		?>
						                	</select>
								        </div>
                                    </div>
                                    <!-- <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<label>Search By Customer</label><br/>
								  		<div class="form-group" role="form">
											<select class="form-control" name="customer_id" id="customer_id">
						                		<option value="">Select Customer</option>
						                		<?php
						               			$cus_r = $db->rp_getData("executive","id,cname","isDelete=0","",0);
						               			while ($cus_d = mysqli_fetch_assoc($cus_r))
						               			{
				                				?>
				                				<option <?= ($_REQUEST['customer_id']==$cus_d['id'])?"selected":""; ?> value="<?=$cus_d['id']?>"><?=$cus_d['cname']?></option>
				                				<?php
						               			}
						                		?>
						                	</select>
								        </div>
                                    </div> -->
                                    <div class="col-md-4 col-xs-4 col-sm-4 pull-right " style="margin-top:10px">
                                    	<br><form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
											<div class="form-group">
												<input type="text" placeholder="Search By Item Name/Item Code :  " class="form-control" name="searchName" id="searchName" value="" />
											</div>
											<div class="form-group">
		                                        <input class="btn btn-danger btn-sm" type="submit" value="search">
		                                    </div>

		                                    <div class="form-group">
		                                        <input class="btn btn-success btn-sm" type="button" value="clear" onClick="clearSearchByName();">
		                                    </div>

											<div class="form-group">
		                                    <div class="btn-group">

												<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
													<i class="fa fa-gear"></i>
												</button>
												
												<ul role="menu" class="dropdown-menu dropdown-menu-right pull-right">
													<!-- <li>
														<a onClick="Importexcel(this)" data-toggle="modal" data-target="#uploadLeeds"><i class="fa fa-download"></i>Import</a>
													</li> -->
													<?php
												if($rights['pdf_download_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{ 
													?>
													<li>
														<a name="print" onClick="printPDF(this)" title="Print Report"><i class="fa fa-file-pdf-o"></i>&nbsp; Print</a>
													</li>
													<?php
											}
											if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
												?>
													<li>
														<a class="excel" name="excel" onClick="genReportexcel(this)" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
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
                <!-- END Portlet PORTLET-->
            </div>
		</div>
		<div class="portlet light"> 
			<div class="portlet-body">
				<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > 
				</div>
				<div class="table-responsive">
					<div id="results"></div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?> 
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script> 
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
var searchName="";
var category_id=""; 
var item_name = "";
var top_category_id = "";
var customer_id = "<?= $_REQUEST['customer_id'] ?>";
var df="";
var data_url = "item_wise_sales_report_get_ajax.php";
 
function searchByName(){ 
	displayRecords(100,1); 
	return false;
} 
function clearSearchByName(){
	searchName = "";
	category_id = "";
	top_category_id = "";
	item_name = "";
	customer_id = "<?= $_REQUEST['customer_id'] ?>";
	df = "";
	// alert(customer_id);
	$("#searchName").val(""); 
	$("#category_id").select2("val","");
	$("#top_category_id").select2("val","");
	$("#customer_id").select2("val","");
	$("#item_name").select2("val","");
	$("#material_request_filter_input").val("");
	displayRecords(100,1);
} 

$(".filterBtn").on("click",function()
{ 
	displayRecords(100,1);
})

function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
		  { "sWidth": "10%" }, 
		  { "sWidth": "10%" }, 
		  { "sWidth": "10%" },
		  { "sWidth": "10%" },
		  { "sWidth": "10%" },
		  { "sWidth": "10%" },
		  { "sWidth": "10%" },
		]
	});
}
function displayRecords(numRecords) { 
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());

	var top_category_id 	= $("#top_category_id").val();
	var category_id 	= $("#category_id").val();
	// var customer_id 	= $("#customer_id").val();
	var item_name 	= $("#item_name").val();
	item_name 	= encodeURIComponent(item_name.trim());

	var df=$("#material_request_filter_input").val();
	df = encodeURI(df)

	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&category_id=" + category_id +"&item_name=" + item_name +"&top_category_id=" + top_category_id+"&customer_id=" + customer_id + "&df=" + df ,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&category_id=" + category_id +"&item_name=" + item_name +"&top_category_id=" + top_category_id+"&customer_id=" + customer_id + "&df=" + df ,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&category_id=" + category_id +"&item_name=" + item_name +"&top_category_id=" + top_category_id+"&customer_id=" + customer_id + "&df=" + df ,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {
	displayRecords(100,1);
});

function printPDF()
	{
		var searchName 	= $("#searchName").val();
      	searchName     	= encodeURIComponent(searchName.trim());
      	
		top_category_id     = $("#top_category_id").val();
		category_id 		= $('#category_id').val();
		item_name 			= $("#item_name").val();
		customer_id		   	= $("#customer_id").val();
		df=$("#material_request_filter_input").val();
		// alert(type);
     	var myWindow = window.open('item_wise_sales_report_print.php?searchName='+searchName+ "&top_category_id=" + top_category_id + "&category_id=" + category_id + "&item_name=" + item_name + "&customer_id=" + customer_id + "&df=" + df,'','width=700,height=800');
     		myWindow.print(); 
    }

   function genReportexcel(cid)
	{
		var rc = encodeURIComponent($("#print_info").html());
		// alert(rc);

		$.ajax({
			type: "POST",
			url: "item_wise_sales_report_excel.php",
			data: '&rc='+rc,
			beforeSend: function() {
				$(".transCover").fadeIn(800);
				$("#loading").modal('show');
			},
			success: function(result){ //alert(result);
					setTimeout(function(){
						$(".transCover").fadeOut(100);
						$("#loading").modal('hide');
						window.location.href=result;
						
					},1500);
				}
		});
	}
</script>
<script type="text/javascript">
	$(".datetimerange-picker-btn").on("click",function(){
		$(".datetimerange-picker-input",$(this).closest(".input-group")).focus();
	});
	$(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
}});
	$('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
 $(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY')+" to "+picker.endDate.format('DD-MM-YYYY'));
});
</script>
</body>
</html>