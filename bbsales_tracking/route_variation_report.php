<?php
$page_id=663;$page_slug='route_variation_report';
$ctable 	= "master_route";
$ctable1 	= "Route Variantion Report";
$main_page 	= "product_mgmt";
$page 		= $ctable;
$page_title = $ctable1;
$id=isset($_REQUEST['id'])?$_REQUEST['id']:"";

$page_hierarchy=array(array("link"=>"","title"=>"Reports"),array("link"=>"sales_person_target_report_manage.php","title"=>$page_title));
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
													  <?php
													    $years = array('2020', '2021', '2022', '2023');
													    $currentYears = date('Y');
													  ?>
													  <label>Years</label><br/>
													  <select class="form-control" name="filter_year" id="filter_year">
													    <?php
													      $_REQUEST['filter_year'] = (isset($_REQUEST['filter_year']) && $_REQUEST['filter_year']!="") ? $_REQUEST['filter_year'] : $currentYears;
													      foreach ($years as $year) {
													    ?>
													    <option <?= ($year == $_REQUEST['filter_year']) ? "selected" : ""; ?> value="<?=$year?>"><?=$year?></option>
													    <?php
													      }
													    ?>
													  </select>
													</div>

						                           <div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px"	>
													  <?php
													    $months = array(
													      '01' => 'January',
													      '02' => 'February',
													      '03' => 'March',
													      '04' => 'April',
													      '05' => 'May',
													      '06' => 'June',
													      '07' => 'July ',
													      '08' => 'August',
													      '09' => 'September',
													      '10' => 'October',
													      '11' => 'November',
													      '12' => 'December'
													    );
													    $currentMonth = date('m');
													  ?>
													  <label>Month</label><br/>
													  <select class="form-control" name="filter_month" id="filter_month">
													    <?php
													      $_REQUEST['filter_month'] = (isset($_REQUEST['filter_month']) && $_REQUEST['filter_month']!="") ? $_REQUEST['filter_month'] : $currentMonth;
													      foreach ($months as $months_key => $months_value) {
													    ?>
													    <option <?= ($months_key == $_REQUEST['filter_month']) ? "selected" : ""; ?> value="<?=$months_value?>"><?=$months_value?></option>
													    <?php
													      }
													    ?>
													  </select>
													</div>

													

			                                       	<div class="col-md-2 col-xs-2 col-sm-2" style="margin-top:10px">
													  <div class="form-group">
													    <label>Search By Sales Person</label>
													    <select class="form-control" name="sales_executive_id" id="sales_executive_id">
													      <option value="">Select Sales Person</option>
													      <?php
													        $whereCustom = "";
													        if ($_SESSION[SITE_SESS.'_ADMIN_TYPE'] != 0 && $_SESSION[SITE_SESS.'_ADMIN_TYPE'] != 14) {
													          $whereCustom = "id = '".$_SESSION[SITE_SESS.'REFERANCE_ID']."' AND ";
													        }
													        $product_list_r = $db->rp_getData('sales_executive', "*", "isDelete=0 AND isActive=1 AND ".$whereCustom." type!='service_engineer' GROUP By name", "", 0);
													        while ($product_list_d = mysqli_fetch_assoc($product_list_r)) {
													      ?>
													      <option <?= ($product_list_d['id'] == $_SESSION[SITE_SESS.'REFERANCE_ID']) ? "selected" : ""; ?> value="<?php echo $product_list_d['id'];?>">
													        <?php echo $product_list_d['name']?>
													      </option>
													      <?php
													        }
													      ?>
													    </select>
													  </div>
													</div>

					                                <div class="col-md-5 col-xs-5 col-sm-5 pull-right" style="margin-top:10px;">
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
																				<a name="print" onClick="gensalespersonPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
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
$("#sales_executive_id").select2();
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
 var filter_month = '';
 var filter_year = '';
var data_url = "route_variation_report_get_ajax.php";

function searchByName(){
	searchName = $("#searchName").val();
	sales_executive_id = $("#sales_executive_id").val();
  //var filter_month = $("#filter_month").val();
	//c_id = $("#c_id").val();
	df1=$("#material_request_filter_input").val();
	filter_month=$("#filter_month").val();
	filter_year=$("#filter_year").val();
	// alert(filter_year);
	df1 = encodeURI(df1)
	//expense_status = $("#expense_status").val();
	
	
	displayRecords(100,1);
	return false;
}

$(".filterBtn").on("click",function()
{
	df1=$("#material_request_filter_input").val();
	// sales_executive_id = $("#sales_executive_id").val();
	c_id = $("#c_id").val();
	expense_status = $("#expense_status").val();
	df1 = encodeURI(df1)
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
 	filter_month = '';
	sales_executive_id = "";
	df1 = "";
   filter_month = '<?=date('m')?>';

	// ToDate = "";
	// FromDate = "";
	$("#material_request_filter_input").val("");
	// $('#sales_executive_id').html('<option value="">Select Sales Executive</option>');
	$("#searchName").val("");
	// $('#sales_executive_id').select2("val","");
	// $('#expense_status').select2("val","");
	// $('#c_id').select2("val","");

	$("#sales_executive_id").fSelect("destroy");
	$("#sales_executive_id").val("");
	$("#sales_executive_id").fSelect("create");

	// $("#filter_month").Select2("destroy");
	// $("#filter_month").val("");
	// $("#filter_month").Select2("create");

	/*$("#filter_month").select2('destroy');
	$("#filter_month").val(filter_month);
	$("#filter_month").select2();*/
	
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
    var searchName = $("#searchName").val();
    var sales_executive_id = $("#sales_executive_id").val();
    var filter_month = encodeURIComponent($("#filter_month").val());
    var filter_year = encodeURIComponent($("#filter_year").val());
    searchName = encodeURIComponent(searchName.trim());
    $("#results").html("");
    $("#results").load(data_url + "?show=" + numRecords + "&sales_executive_id=" + sales_executive_id + "&df=" + df1 + "&searchName=" + searchName + "&filter_month=" + filter_month + "&filter_year=" + filter_year, function() {
        loadDataTable();
    });

    $("#results").on("click", ".paging_simple_numbers a", function(e) {
        e.preventDefault();
        var numRecords = $("#numRecords").val();
        $(".loading-div").show();
        var page = $(this).attr("data-page");
        $("#results").load(data_url + "?show=" + numRecords + "&sales_executive_id=" + sales_executive_id + "&df=" + df1 + "&searchName=" + searchName + "&filter_month=" + filter_month, {
            "page": page
        }, function() {
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
<script>
$(document).ready(function() {       
   $('#datatable_1').dataTable();
});
</script>

<script type="text/javascript">
	function genReport(cid){
		var rc = encodeURIComponent($("#print_info1").html());
		$.ajax({
			type: "POST",
			url: "expense_genReport_ajax.php",
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
	function genReportexcel(cid){
		var searchName     = $("#searchName").val();
  		searchName     = encodeURIComponent(searchName.trim());
      
		var sales_executive_id = $('#sales_executive_id').val();
		var filter_month=$("#filter_month").val();
		var filter_year=$("#filter_year").val();

		if (sales_executive_id != "" && filter_month != "" && filter_year != "" ) {
			
			$.ajax({
				type: "POST",
				url: "route_variation_report_genreport.php",
				data: {
					sales_executive_id:sales_executive_id,
					filter_month:filter_month,
					filter_year:filter_year
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
		    	}
			});
		}
		else
		{
			toastr.error("Please Select Sales Person Or Month Or Year");
		}
	}
	function printPDF() 
	{
		 var myWindow = window.open('','','width=700,height=800')
	    myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info1").html());
	    myWindow.print();
	   
	}
</script>
<script type="text/javascript">
	function gensalespersonPrint(){
		var searchName     = $("#searchName").val();
  		searchName     = encodeURIComponent(searchName.trim());
      
		sales_executive_id = $('#sales_executive_id').val();
		var filter_month=$("#filter_month").val();
		var filter_year=$("#filter_year").val();
     	var myWindow = window.open('print_route_variation_report.php?searchName='+searchName+ "&sales_executive_id=" + sales_executive_id + "&filter_month="+filter_month + "&filter_year="+filter_year,'','width=700,height=800');
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