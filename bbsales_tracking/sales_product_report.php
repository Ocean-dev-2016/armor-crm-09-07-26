<?php
$page_id=626;$page_slug='product_sales_report';
$ctable 	= "dispatch_detail";
$ctable1 	= "dispatch_detail";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Dispatch Report";
$page_hierarchy=array(array("link"=>"","title"=>"Reports"),array("link"=>$ctable."_manage.php","title"=>$page_title));
include("connect.php");
// $FromDate="";
// $ToDate="";
// $department_list_d=$db->rp_getData('sales_executive',"*","isDelete=0 AND type!='service_executive' ","name ASC",0);
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
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
				<div class="col-md-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
				<div class="col-xl-12 ">
					<!-- BEGIN Portlet PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-filter"></i>Filters </div>
							 <div class="tools">
								<a href="javascript:;" class="collapse" data-original-title="" title=""> </a>

							</div>
						</div>
						<div class="portlet-body">
							<div class="slimScrollDiv" style="position: relative;width: auto; height: auto;">
								<div class="row">
									<div class="col-md-2 col-xs-3 col-sm-3" style="margin-top:10px">
										<label>Filter By Date</label>
										<div class="input-group">
											<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
											<span class="input-group-addon datetimerange-picker-btn"><i class="fa fa-calendar"></i>
											</span>
											<span class="input-group-btn">
								          	<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
								        	</span>
										</div>
					                </div>
					                <!-- <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<div class="form-group" role="form">
											<label>Search by State</label><br/>
					                        <select class="form-control status" multiple="multiple" name="class_id" id="class_id">
					                            <option value=""> Select State</option>
												<?php
												$id_r = $db->rp_getData("class","*",0);
												if(mysqli_num_rows($id_r)>0)
												{
													while($id_d = mysqli_fetch_array($id_r))
													{
														?>
														<option value="<?php echo $id_d['id']; ?>"><?php echo $id_d['name']; ?></option>
														<?php
													}
												}
												?>
					                        </select>
					                     </div>
					                </div>

									<div class="col-md-2  col-xs-2  col-sm-2" style="margin-top:10px">
									   <div class="form-group">
										 <label>Search by City</label><br/>
											<select class="form-control  status" multiple="multiple" name="area" id="area">
												<option value="">Select City</option>
											</select>
				                        </div>      
				                    </div> -->
				                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
									<div class="form-group">
										<label>Search by Company</label><br/>
										  <select class="form-control status"  name="type_of_company" id="type_of_company">
												<option value=""> Select Company</option>
												<?php
			                                    	$company_r = $db->rp_getData("company_master","*","isDelete=0","id DESC",0);
			                                    	if(mysqli_num_rows($company_r)>0)
			                                    	{
			                                    		while($company_d = mysqli_fetch_array($company_r))
			                                    		{
			                                    		?>
			                                    			<option value="<?php echo $company_d['id']; ?>" <?=($type_of_company == $company_d['id'])?"selected":"";?>><?php echo $company_d['name']; ?></option>
			                                    		<?php
			                                    		}
			                                    	}
			                                    ?>
			                                                        
					                	</select>
							        </div>
			                    </div>

				                    <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<div class="form-group">
											<label>Search by Customer Type</label><br/>
											<select class="form-control status" multiple="multiple" name="customer_type" id="customer_type">
												<option value=""> Select Customer Type </option>
												<?php 
													$order_type_r=$db->rp_getData('customer_type',"*","isDelete=0","name ASC",0);
													while($order_type_d=mysqli_fetch_assoc($order_type_r))
													{
														?>
												 		<option  value="<?php echo $order_type_d['id']?>"><?php echo $order_type_d['name'];?></option>
														<?php
													}
												?>
											</select>
										</div>
									</div>

									<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
										<div class="form-group">
											<label>Search by Warehouse</label><br/>
											<select class="form-control status" multiple="multiple" name="warehouse_id" id="warehouse_id">
												<option value=""> Select Warehouse</option>
												<?php 
													$warehouse_r=$db->rp_getData('warehouse',"*","isDelete=0","name ASC",0);
													while($warehouse_d=mysqli_fetch_assoc($warehouse_r))
													{
														?>
												 		<option  value="<?php echo $warehouse_d['id']?>"><?php echo $warehouse_d['name'];?></option>
														<?php
													}
												?>
											</select>
										</div>
									</div>

									<?php
									if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
				                	{ 
									?>
										<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
											<div class="form-group">
												<label>Search by Sales Person</label><br/>
												<select class="form-control status" multiple="multiple" name="sales_executive_id" id="sales_executive_id">
													<option value=""> Select Sales Person </option>
													<?php 
														$department_list_d=$db->rp_getData('sales_executive',"*","isDelete=0 AND type!='service_executive' ","name ASC",0);
														while($department_list_r=mysqli_fetch_assoc($department_list_d))
														{
															?>
															<option  value="<?php echo $department_list_r['id']?>">
															<?php echo $department_list_r['name'];?>
															</option>
															<?php
														}
													?>
												</select>
											</div>
										</div>
										<?php
									}
									?>
								</div>
								<div class="row">
									<div class="col-md-7 col-xs-7 col-sm-7 pull-right">
						                <div class="form-inline" role="form">
						                    <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
						                        <div class="form-group">
						                        	<input type="text" style="width: 300px!important" placeholder="Search By Person / Company / Order No :  " class="form-control input-large" name="searchName" id="searchName" value="" />
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
															<?php
														if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
														{ 
															?>
															<li>
																<a name="print" onClick="genOrderPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
															</li>
															<?php
															}
															if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
															{ 
																?>
															<li>
																<a class="excel" name="excel" onClick="genReportexcel()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
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
							<!-- END Portlet PORTLET-->
						</div>
						<div class="col-xl-12">
							<div class="portlet light">
								<div class="portlet-body">
									<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%; margin-top:10%;padding-left:48%;" > </div>
									<div id="results">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="myModal" class="modal fade" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i>View Order Information </div>
							<div class="tools">

								<a href="javascript:;" id="requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

								<a href="javascript:;" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
							</div>
						</div>
						<div class="portlet-body portlet-empty" style="">
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
<script type="text/javascript" src="js/fSelect.js"></script> 

<script type="text/javascript">
	$("#class_id").fSelect();
	$("#area").fSelect();
	$("#customer_type").fSelect();
	$("#sales_executive_id").fSelect();
	$("#warehouse_id").fSelect();
</script>

<script type="text/javascript">
var searchName="";
var sales_executive_id="";
var data_url = "sales_product_report_get_ajax.php";
// $('#ToDate').datepicker({  datepicker: true, autoclose: true });
// $('#FromDate').datepicker({  datepicker: true, autoclose: true });
// var ToDate="";
// var FromDate="";
var df1="";
var status="";
var type="";
var area="";
var class_id="";
var flag="1";
var customer_type="";
var type_of_company="";
var warehouse_id="";

$('#myModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","orders_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})

function searchByName(){
	searchName = $("#searchName").val();
	customer_type = $("#customer_type").val();
	type_of_company = $("#type_of_company").val();
	warehouse_id = $("#warehouse_id").val();
	class_id = $("#class_id").val();
	area = $("#area").val();
	sales_executive_id = $("#sales_executive_id").val();
	displayRecords(100,1);
	/*if(class_id!="" && area!="")
	{
		filter_class(class_id,area);
	}*/
	if(type!="")
	{
		filter_cust(type);
	}
	return false;
}
function clearSearchByName(){
	searchName = "";
	ToDate = "";
	FromDate = "";
	df1 = "";
	status = "";
	type = "";
	class_id = "";
	area = "";
	customer_type = "";
	warehouse_id = "";
	sales_executive_id="";
	type_of_company="";
	$("#searchName").val("");
	// $("#sales_executive_id").select2("val","");
	// $("#ToDate").val("");
	// $("#FromDate").val("");
	$("#material_request_filter_input").val("");
	$("#status").select2("val","");
	$("#type_of_company").select2("val","");
	// $("#customer_type").select2("val","");

	$("#class_id").fSelect("destroy");
	$("#class_id").val("");
	$("#class_id").fSelect("create");

	$("#area").fSelect("destroy");
	$("#area").val("");
	$("#area").fSelect("create");

	$("#customer_type").fSelect("destroy");
	$("#customer_type").val("");
	$("#customer_type").fSelect("create");

	$("#warehouse_id").fSelect("destroy");
	$("#warehouse_id").val("");
	$("#warehouse_id").fSelect("create");

	$("#sales_executive_id").fSelect("destroy");
	$("#sales_executive_id").val("");
	$("#sales_executive_id").fSelect("create");

	// $("#class_id").select2("val","");
	// $("#area").select2("val","");
	$("#type").select2("val","");
	$("#status").select2("val","");
	displayRecords(100,1);
}
$(".filterBtn").on("click",function()
{
	df1=$("#material_request_filter_input").val();
	sales_executive = $("#sales_executive").val();
	df1 = encodeURI(df1)
	displayRecords(100,1);
})
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function getByDate() {
	if(FromDate<=ToDate)
	{
		
		ToDate = $("#ToDate").val();
		FromDate = $("#FromDate").val();
		displayRecords(100,1);
	}
	else
	{
		alert("From Date Should Be Less Than To Date");
	}

}
function searchBySales(sid){
		
		sales_executive_id=sid;
}
function filter_cust(type){
		customer_type=type;
}

function getArea(cid)
{
	class_id= cid;
	displayRecords(100,1);
	
        $.ajax({
        type: "POST",
        url: "find_area_filter.php",
        data:'class_id='+class_id,
        success: function(data){
		 $("#area").html(data);
		//$('#area').select2("val","");
		displayRecords(100,1);
        }
    });
}


$("#class_id").on('change', function() {
	var class_id = $("#class_id").val();
	filter_class(class_id,"");
});

function filter_class(class_id,area=""){
    $.ajax({
        type: "POST",
        url: "find_area_filter.php",
        data:'class_id='+class_id+"&area="+area,
        beforeSend:function(){
            // $("#loading-modal").modal('show');  
            $('.preloader').fadeIn('slow');
        },
        success: function(data){
            $("#area").select2("destroy");
       		$("#area").fSelect("destroy");
        	$("#area").html(data);
       		$("#area").fSelect('create');
            // $("#loading-modal").modal('hide');
            $('.preloader').fadeOut('slow');
        }
    });
}
function getareaName(aid){
	class_id=$('#class_id').val();
	//$('#state').select2("val","");
	area=aid;
	displayRecords(100,1);
	
}
function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "5%" }, 
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  { "sWidth": "5%" },
			  /*{ "sWidth": "5%" },*/
			  { "sWidth": "20%","bSortable": false }
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&sales_executive_id=" + sales_executive_id + "&type=" + type + "&flag=" + flag + "&class_id=" + class_id + "&area=" + area + "&customer_type=" + customer_type + "&df=" + df1 +"&warehouse_id=" + warehouse_id+"&type_of_company="+type_of_company ,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag + "&class_id=" + class_id + "&area=" + area + "&customer_type=" + customer_type + "&df=" + df1 +"&warehouse_id=" + warehouse_id +"&type_of_company="+type_of_company,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	// $("#results").on( "change", "#numRecords", function (e){
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type + "&flag=" + flag + "&class_id=" + class_id + "&area=" + area + "&customer_id=" + customer_id + "&df=" + df1 ,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
		
	// });
}

// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
}

$(document).ready(function() {
	displayRecords(100,1);
});
function del_conf(id,quotation_id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id +'&flag=1';
	}
}

function confirmChange(id) 
{
	var r=confirm("Are you sure to forward order to production department?");
	if(r)
	{
		window.location.href="orders_crud.php?mode=isActive&id="+id+"&status=1";
	}
	
   
}

function genReport(cid){
	var rc = encodeURIComponent($("#print_info").html());
	alert(rc);
	$.ajax({
		type: "POST",
		url: "orders_info_genReport_ajax.php",
		data: 'cid='+cid+'&rc='+rc,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ 
				setTimeout(function(){
					$(".transCover").fadeOut(100);
					window.location.href=result;
				},1500);
			}
	});
}
function genReportexcel(cid)
{
	var rc = encodeURIComponent($("#print_info").html());
	// alert(rc);

	$.ajax({
		type: "POST",
		url: "orders_genreport_excel.php",
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
function printPDF() 
{
	 var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();
   
}

</script>
<script type="text/javascript">
	function genOrderPrint()
	{
		var searchName 	= $("#searchName").val();
      	searchName     	= encodeURIComponent(searchName.trim());
      	df1			   	= $("#material_request_filter_input").val();
		df1 		   	= encodeURI(df1);
		type 		   	= $("#type").val();
		class_id 		= $('#class_id').val();
		area 			= $("#area").val();
		status		   	= $("#status").val();
		type = $("#type").val();
		customer_type = $("#customer_type").val();
		sales_executive_id = $("#sales_executive_id").val();
		warehouse_id = $("#warehouse_id").val();
		type_of_company = $("#type_of_company").val();
		// alert(type);
     	var myWindow = window.open('print_dispatch1_report.php?searchName='+searchName+ "&type=" + type + "&class_id=" + class_id + "&area=" + area + "&df=" + df1  + "&customer_type=" + customer_type  + "&type=" + type + "&sales_executive_id=" + sales_executive_id + "&warehouse_id=" + warehouse_id + "&type_of_company="+type_of_company,'','width=700,height=800');
     	myWindow.print();
  //    	setTimeout(function () 
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