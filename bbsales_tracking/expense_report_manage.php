<?php
$page_id=596;$page_slug='expense_report_page';
$ctable 	= "expense";
$ctable1 	= "Expense Report";
$main_page 	= "product_mgmt";
$page 		= $ctable;
$page_title = $ctable1;
$id=isset($_REQUEST['id'])?$_REQUEST['id']:"";
$page_hierarchy=array(array("link"=>"","title"=>"Reports"),array("link"=>$ctable."_manage.php","title"=>$page_title));
include("connect.php");
$date1 = "01-".date('m-Y')." to ".date('t-m-Y');
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
<style type="text/css">
	.modal-dialog
	{
	    width: 800px;
	    margin: 30px auto;
	}
	 #wrapper
	{
	    width:190mm;
	    margin:0 50mm;
	}
</style>
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
				<div class="col-sm-12">
					<?php $db->printSuccessMessage(); ?>
					<?php $db->printErrorMessage(); ?>
				</div>
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-12">
                        	<!-- BEGIN EXAMPLE TABLE PORTLET-->
                        	<!-- <div class="portlet light"> -->
					    		<div class="table-toolbar">
									<div class="row">
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
						                        <!-- <div class="slimScrollDiv"  style="position: relative;width: auto; height: auto;"> -->
						                            <div class="row">
						                                <!-- <div class="col-md-6  col-xs-6  col-sm-6" style="margin-top:10px">
															<div class="form-inline" role="form">
																<div class="form-group">
						                                            <label>Filter By Created Date : &nbsp;</label><br/>
						                                                <input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="" placeholder="From Date">
						                                                <input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="" placeholder="To Date">
						                                        </div>
					                                            <div class="form-group">
					                                                 <label>&nbsp;&nbsp;</label>
					                                                <input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="" placeholder="To Date">
					                                            </div>
					                                            <div class="form-group">
					                                                <input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
					                                                <input class="btn btn-success btn-sm" type="submit" value="Clear" onClick="clearSearchByName();">
					                                            </div>
															</div>
						                                </div> -->
						                                <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
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
				                                       	<div class="col-md-2  col-xs-2  col-sm-2" style="margin-top:10px">
															<div class="form-group">
						                                           	<label>Search By Sales Person</label> 
						                                           	<select class="form-control" name="sales_executive_id" multiple="multiple" id="sales_executive_id">
																	<option value="">select Sales Person</option>
														<?php

															if ($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0) 
															{
																$whereCustom = "";
																$whereCustom = "isDelete=0 AND isActive=1";

																if($rights['personal_flag']==1)
																{
																 	$whereCustom .= " AND id='" . $_SESSION[SITE_SESS.'REFERANCE_ID'] . "' AND ";
																}
																else 
																{
																	if($rights['chain_vise_flag'] == 1)
																 	{	
																		$exeType = $db->rp_getValue("sales_executive","type","id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."'");
																		// echo $exeType;
																		if($exeType=='sales_manager')
																		{ 
																			$whereCustom .= " AND (sm_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."') AND ";
																		}
																		else if($exeType=='area_sales_manager' || $exeType=='dispatch_sales_manager')
																		{ 
																			$whereCustom .= " AND (asm_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."') AND ";
																		}
																		else if($exeType=='sales_officer')
																		{ 
																			$whereCustom .= " AND (so_id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' OR id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."') AND ";
																		}
																		else
																		{
																			$whereCustom .= " AND id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' AND ";
																		}
																	}
																}
															}	


															$product_list_r=$db->rp_getData('sales_executive',"*",$whereCustom."  type!='service_engineer' GROUP By name","",0);													
															while($product_list_d=mysqli_fetch_assoc($product_list_r))
															{
																?>
																<option <?=($product_list_d['id']==$_SESSION[SITE_SESS.'REFERANCE_ID'])?"selected":"";?> <?php echo $product_list_d['name']?> value="<?php echo $product_list_d['id'];?>">
																<?php echo $product_list_d['name']?>
																</option>
																<?php
															}
														?>
																	</select>
					                                        </div>
						                                </div>
						                                <div class="col-md-2  col-xs-2  col-sm-2" style="margin-top:10px">
															<div class="inline"  style="width:50!important;" role="form">
																<div class="form-group">
						                                           	<label>Search By Category Name</label> 
						                                           	<select class="form-control" name="c_id" id="c_id" multiple="multiple" >
																	<option value="">select Category Name</option>
																	<?php
																		$cname_r=$db->rp_getData('expence_category',"*","","",0);
																		while($cname_d=mysqli_fetch_assoc($cname_r))
																		{
																		?>
																		<option <?php echo $cname_d['name']?> value="<?php echo $cname_d['id'];?>">
																		<?php echo $cname_d['name']?>
																		</option>
																		<?php
																		}
																		?>
																	</select>
						                                        </div>
															</div>
						                                </div>
						                                <div class="col-md-2  col-xs-2  col-sm-2" style="margin-top:10px">
															<div class="inline"  style="width:50!important;" role="form">
																<div class="form-group">
						                                           	<label>Search By Expense Status</label> 
						                                           	<select class="form-control" name="expense_status" multiple="multiple" id="expense_status">
																	<option value="">select Expense Status</option>
																	<?php
																		$es_r=$db->rp_getData('expense',"*","1=1 GROUP By expense_status","",0);
																		while($es_d=mysqli_fetch_assoc($es_r))
																		{
																			?>
																			<option <?php echo $es_d['expense_status']?> value="<?php echo $es_d['expense_status'];?>">
																			<?php if($es_d['expense_status']==0){echo "Pending"; }else if($es_d['expense_status']==1){echo "Passed"; } else if($es_d['expense_status']==2){echo "Rejected"; }?>
																			</option>
																			<?php
																		}
																		?>
																	</select>
						                                        </div>
															</div>
						                                </div>
						                                <div class="col-md-4 col-xs-4 col-sm-4 pull-right" style="margin-top:10px;">
							                                <div class="form-inline" role="form">
							                                	<label>Search By Name :</label>
							                                    <form class="form-inline" role="form" onSubmit="return searchByName();">
							                                       	<div class="form-group">

							                                          <input type="text" style="width:180px!important" placeholder="Search By Name :  " class="form-control input-small" name="searchName" id="searchName" value="" />
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
																			<ul role="menu" class="dropdown-menu dropdown-menu-right pull-right" >
																				<!-- <li>
																					<a onClick="Importexcel(this)" data-toggle="modal" data-target="#uploadLeeds"><i class="fa fa-download"></i>Import</a>
																				</li> -->
																				<?php
																		if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
																		{ 
																			?>
																				<li>
																					<a name="print" onClick="genexpensePrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
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
												<!-- </div> -->
											</div>
            							</div>
            						</div>
            					</div>
            				
            		
            
    


	 
										
										<div class="col-md-6">
											<table class="table" style="margin-bottom:-40px;">
											<input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
													<tr>
														<td><input type="hidden"  name="FromDate" id="FromDate" value="<?php echo $FromDate; ?>" placeholder="From Date">
														</td>
														<td>
															<input type="hidden"  name="ToDate" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
														</td>											
														<td>
														<input class="btn btn-danger btn-sm" type="hidden" value="Filter" onClick="getByDate();">
														</td>
														<td>
														<input class="btn btn-success btn-sm" type="hidden" value="Clear" onClick="getByDate();">
														</td>
													</tr>
											</table> 						
										</div>
									</div>
								</div>
							<!-- </div> -->
                        <!-- END EXAMPLE TABLE PORTLET-->
                    	</div>
                    </div>
				</div>
				<div class="portlet light">
					<div class="table-toolbar">
						<div class="portlet-body">
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
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script> 

<script type="text/javascript">
$("#sales_executive_id").fSelect();
$("#c_id").fSelect();
$("#expense_status").fSelect();
</script>

<script type="text/javascript">
var date = new Date();
// $('#ToDate').datepicker({  datepicker: true, autoclose: true ,format:'dd-mm-yyyy'});
// $('#FromDate').datepicker({  datepicker: true, autoclose: true,format:'dd-mm-yyyy' });
/*$('#FromDate').datepicker("setDate", new Date(date.getFullYear(), date.getMonth(), 1));
$('#ToDate').datepicker("setDate", new Date(date.getFullYear(), date.getMonth() + 1, 1));*/
var searchName="";
// var ToDate="";
// var FromDate="";
var df1="";
var sales_executive_id="";
var c_id="";
var expense_status="";
var data_url = "expense_report_get_ajax.php";

function searchByName(){
	searchName = $("#searchName").val();
	sales_executive_id = $("#sales_executive_id").val();
	c_id = $("#c_id").val();
	expense_status = $("#expense_status").val();
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1)
	if(expense_status!="")
	{
		searchBystatus();
	}
	if(c_id!="")
	{
		filter_cname();
	}
	displayRecords(100,1);
	return false;
}
function searchBystatus(){
	expense_status = $("#expense_status").val();
	return false;
}
function filter_cname(id){
	c_id = id;
	return false;
}
$(".filterBtn").on("click",function()
{
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1);
	// sales_executive_id = $("#sales_executive_id").val();
	c_id = $("#c_id").val();
	expense_status = $("#expense_status").val();
	displayRecords(100,1);
})
function getByDate() {

	var checkindatestr = $("#FromDate").val();
	var dateParts = checkindatestr.split("-");
	var checkindate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

	var checkindatestr1 = $("#ToDate").val();
	var dateParts1 = checkindatestr1.split("-");
	var now = new Date(dateParts1[2], dateParts1[1] - 1, dateParts1[0]);
	var difference = now - checkindate;
	var days = difference / (1000*60*60*24);
	ToDate = $("#ToDate").val();
	FromDate = $("#FromDate").val();
	if(days>=0)
	{
		displayRecords(100,1);
	}
	else
	{
		toastr.error("From Date Should Be Less Than To Date");
	}
	
}
function clearSearchByName(){
	searchName = "";
	c_id = "";
	sales_executive_id = "";
	df1 = "";
	expense_status = "";
	// ToDate = "";
	// FromDate = "";
	$("#material_request_filter_input").val("");
	// $('#sales_executive_id').html('<option value="">Select Sales Officer</option>');
	$("#searchName").val("");
	// $('#sales_executive_id').select2("val","");
	// $('#expense_status').select2("val","");
	// $('#c_id').select2("val","");

	$("#sales_executive_id").fSelect("destroy");
	$("#sales_executive_id").val("");
	$("#sales_executive_id").fSelect("create");

	$("#expense_status").fSelect("destroy");
	$("#expense_status").val("");
	$("#expense_status").fSelect("create");

	$("#c_id").fSelect("destroy");
	$("#c_id").val("");
	$("#c_id").fSelect("create");

	// $("#class_id").fSelect("destroy");
	// $("#class_id").val("");
	// $("#class_id").fSelect("create");

	// $("#class_id").fSelect("destroy");
	// $("#class_id").val("");
	// $("#class_id").fSelect("create");
	// $('#FromDate').datepicker("setDate", new Date(date.getFullYear(), date.getMonth(), 1));
	// $('#ToDate').datepicker("setDate", new Date(date.getFullYear(), date.getMonth() + 1, 1));
	displayRecords(100,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchName").click();
	}
});

function loadDataTable(){
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "5%" }, 
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			  { "sWidth": "30%" },
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	var c_id 	= $("#c_id").val();
	var sales_executive_id 	= $("#sales_executive_id").val();
	var expense_status 	= $("#expense_status").val();
	searchName 	= encodeURIComponent(searchName.trim());

	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1);
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&sales_executive_id=" + sales_executive_id + "&c_id=" + c_id + "&df=" + df1 + "&searchName=" + searchName + "&expense_status=" + expense_status,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&sales_executive_id=" + sales_executive_id + "&c_id=" + c_id + "&df=" + df1  + "&searchName=" + searchName + "&expense_status=" + expense_status + "&isFillter=" + true,{"page":page}, function(){ //get content from PHP page
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
	$("#material_request_filter_input").val('<?= $date1; ?>');  
   	$('#datatable_1').dataTable();
	displayRecords(100,1);   
}); 
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

<script type="text/javascript"> 
	function genReportexcel(cid){
		var rc = encodeURIComponent($("#print_info1").html());
		$.ajax({
			type: "POST",
			url: "expence_genreport_excel.php",
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
	function genexpensePrint(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	c_id = $('#c_id').val();
		sales_executive_id = $('#sales_executive_id').val();
		df1 = $("#material_request_filter_input").val();
		expense_status = $('#expense_status').val();
     	var myWindow = window.open('print_expense_report_ajax.php?searchName='+searchName+ "&sales_executive_id=" + sales_executive_id + "&c_id=" + c_id + "&df=" + df1  + "&expense_status=" + expense_status,'','width=700,height=800');
     	myWindow.print(); 
    }
</script>
</body>
</html>