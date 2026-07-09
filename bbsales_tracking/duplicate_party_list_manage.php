<?php
$page_id=627;$page_slug='inquiry_cancel_report';
// $ctable 	= "no_order_inquiry";
$ctable 	= "sales_executive";
$ctable1 	= "Inquiry";
$main_page 	= $ctable;
$page 		= $ctable;
$page_title = "Duplicate Party List";
$page_hierarchy=array(array("link"=>"","title"=>"Report"),array("link"=>$ctable."_manage.php","title"=>$page_title));
$FromDate="";
$ToDate="";
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
				<div class="col-md-12 "><br/>
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
									<div class="col-md-6  col-xs-6 col-sm-6" style="margin-top:10px">
									</div>
                                	<div class="col-md-6  col-xs-6 col-sm-6" style="margin-top:10px">
										<div>
									  		<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
									   			<div class="form-group">
													<input type="text" placeholder="Search By Person / Company / Phone:" class="form-control input-medium" name="searchName" id="searchName" value="" style="width: 300px!important"/>
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
	                                                            <a name="print" onClick="geninquiryPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
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
									<!-- <div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">

										<?php
												if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{ 
													?>


										<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="geninquiryPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
										<?php
											}
											if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
												?>
										<button type="button" class="btn green-haze btn-sm" name="export" onClick="genReportexcel(this)" id="export" href="" title="Download Excel Report"><i class="fa fa-file-excel-o"></i>Excel</button>
										<?php
										}
										?>
										
									</div> -->
								</div>
							</div>
                        </div>
                    </div>
                    <!-- END Portlet PORTLET-->
                </div>
					<div class="portlet light">
					
						<!-- <div class="table-toolbar">
							<div class="row">
								<div class="col-md-12">
									<h4 class="text-right" style="font-weight: bold;">Total Count : <span id="totalcount">0</span></h4>
								</div>
							</div>
						</div> -->
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
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script>

<script type="text/javascript">
	$("#state").fSelect();
	$("#city").fSelect();
	$("#type").fSelect(); 
</script>

<script type="text/javascript"> 
var df1="";
var type="";
var searchName=""; 
var city_id="";
var state_id="";
var data_url = "duplicate_party_list_get_ajax.php";
 
function searchByName(){
	searchName = $("#searchName").val();
	state_id = $("#state").val();
	city_id = $("#city").val();
	type = $("#type").val(); 
	displayRecords(500,1); 
	return false;
}
function clearSearchByName(){ 
	searchName = "";
	type = "";
	df1 = "";
	// ToDate = "";
	// FromDate = "";
	city = "";
	state = "";
	// $("#ToDate").val("");
	// $("#FromDate").val("");
	$("#material_request_filter_input").val("");
	$("#searchName").val("");
	$("#type").select2("val","");
	$("#state_id").select2("val","");
	$("#city").select2("val","");
	$("#state").select2("val","");
	displayRecords(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});


$("#state").on('change', function() {
	var state = $("#state").val();
	filter_state(state,"");
});


function filter_state(state_id,city_id=""){
   	$.ajax({
        type: "POST",
        url: "find_city.php",
       	data:'state_id='+state_id+"&city="+city_id,
		beforeSend:function(){
			// $("#loading-modal").modal('show');	
			$('.preloader').fadeIn('slow');
		},
        success: function(data){
        	$("#city").select2("destroy");
       		$("#city").fSelect("destroy");
        	$("#city").html(data);
       		$("#city").fSelect('create');
			// $("#loading-modal").modal('hide');
			$('.preloader').fadeOut('slow');
        }
    });
}

function filter_city(city_id){
	state_id = $("#state").val();
	city_id = $("#city").val();
	displayRecords(500,1);
}	 
function getSalesExecutive(type){
	type=type;
}

function getByDate() {
	if($("#FromDate").val() != '' && $("#ToDate").val() != '' ){
		ToDate = $("#ToDate").val();
		FromDate = $("#FromDate").val();
		displayRecords(500,1);
	}
	else
	{
		alert("Please Select Date");
	}
}

$(".filterBtn").on("click",function()
{
	df1=$("#material_request_filter_input").val();
	// sales_executive = $("#sales_executive").val();
	df1 = encodeURI(df1)
	displayRecords(500,1);
})

function loadDataTable(){
	
	$('#datatable_1').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
        "aoColumns": [
			  { "sWidth": "10%" }, 
			  { "sWidth": "30%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%" },
			  { "sWidth": "20%","bSortable": false }
			],
			"oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	/*var state_id = $("#state").val();
	state_id = encodeURIComponent(state_id.trim());
	var city_id = $("#city").val();
	city_id = encodeURIComponent(city_id.trim());*/
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type   +"&state=" + state_id +"&city=" + city_id + "&df=" + df1,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type   +"&state=" + state_id +"&city=" + city_id + "&df=" + df1,{"page":page}, function(){ //get content from PHP page
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
	displayRecords(500,1);
}); 
function genReportexcel(){
	var show = $("#numRecords").val();
	state = $("#state").val();
	city_id = $("#city").val();
	df1 = $("#material_request_filter_input").val();
	// alert(status);
	$.ajax({
		type: "POST",
		url: "duplicate_party_list_report_genreport_excel.php",
		data: '&searchName='+searchName+'&type='+type +"&state=" + state +"&city_id=" + city_id +"&show=" + show +"&status=" + status +"&df1=" + df1,
		beforeSend: function() {
			$(".transCover").fadeIn(800);
			$("#loading").modal('show');
		},
		success: function(result){ //alert(result);
				setTimeout(function(){
					$(".transCover").fadeOut(500);
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
 $(".datetimerange-picker-input").val(picker.startDate.format('YYYY-MM-DD')+" to "+picker.endDate.format('YYYY-MM-DD'));
});
</script>
<script type="text/javascript">
	function geninquiryPrint(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
		var type = $("#type").val(); 
		state_id = $("#state").val();
		city_id = $("#city").val();
		df1 = $("#material_request_filter_input").val();
		var show = $("#numRecords").val();
		// alert(show);
     	var myWindow = window.open('print_inquiry_cancel_report_ajax.php?searchName='+searchName + "&type=" + type   +"&state=" + state_id +"&city=" + city_id + "&df=" + df1 + "&show=" + show ,'','width=700,height=800');
     	// myWindow.print();
     	setTimeout(function () 
		{
			myWindow.print();
			var ival = setInterval(function() 
			{
			    myWindow.close();
			    clearInterval(ival);
			}, 200);
		}, 500);
    }
</script>
</body>
</html>