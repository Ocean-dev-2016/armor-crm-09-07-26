<?php
$page_id=632;$page_slug='salesexecutive_performance_report_page';
$ctable 	= "sales_executive";
$ctable1 	= "Sales Goal Report Report";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Manage ".$ctable1." Reports";
$page_hierarchy=array(array("link"=>"","title"=>"Reports"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1));
include("connect.php");
$FromDate="";
$ToDate="";
$date_filter=date('d-m-Y');
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
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
				<div class="col-md-12 ">
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
									<div class="col-md-6  col-xs-6  col-sm-6" style="margin-top:10px">
										<div class="form-inline" role="form">
										   <div class="form-group">
												<label>Filter By Order Date : &nbsp;</label>
												<input type="text"  name="FromDate" class="form-control input-small" id="FromDate" value="<?php echo $date_filter; ?>" placeholder="From Date">
											</div>
											<!-- <div class="form-group">
												 <label>&nbsp;&nbsp;</label>
												<input type="text"  name="ToDate" class="form-control input-small" id="ToDate" value="<?php echo $ToDate; ?>" placeholder="To Date">
											</div> -->
											<!-- <div class="form-group">
														<input class="btn btn-danger btn-sm" type="submit" value="Filter" onClick="getByDate();">
											</div> -->
										</div>
									</div>
									<div class="col-md-6 col-xs-6 col-sm-6 " style="margin-top:10px">
										<form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
											<div class="form-group">
												<!-- <label>Search By Sales Officer Name : &nbsp;</label> -->
												<input type="text" class="form-control input-small" style="width: 350px!important" placeholder="Search By Sales Officer Name :" name="searchName" id="searchName" value="" />
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
														<a name="print" onClick="genReport(this)" title="Print Report"><i class="fa fa-file-pdf-o"></i>&nbsp; PDF</a>
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
					<!-- END Portlet PORTLET-->
				</div>
				<div class="col-sm-12">
					<div class="portlet light">
					
						<div class="portlet-body">
							<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
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
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script>


<script type="text/javascript">
	$("#sales_executive_type").fSelect();
</script>

<script type="text/javascript">
var searchName="";
var sales_executive_id="";
var data_url = "daily_achivement_report_get_ajax.php";
$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });
var ToDate="";
var FromDate="";
var status="";
var type="";
var area="";
var state="";

var city="";

var class_id="";
var flag="1";
$('#myModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","orders_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})
function searchByName(){
	searchName = $("#searchName").val();
	state= $("#state").val();
	state = encodeURIComponent(state);
	city= $("#city").val();

	city = encodeURIComponent(city);
	FromDate=$("#FromDate").val();
	displayRecords(100,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	ToDate = "";
	// FromDate = "";
	state="";
	city="";
	status = "";
	type = "";
	area = "";
	$("#searchName").val("");
	$("#ToDate").val("");
	// $("#FromDate").val("");
	$("#status").select2("val","");
	$("#type").select2("val","");
	$("#state").select2("val","");
	$("#city").select2("val","");
	$("#status").select2("val","");
	displayRecords(100,1);
}
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
		displayRecords(100,1);
}
function getType(tid){
		type=tid;
		displayRecords(100,1);
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
			  // { "sWidth": "5%" }, 
			  // { "sWidth": "5%" },
			  // { "sWidth": "5%" },
			  // { "sWidth": "5%" }, 
			  // { "sWidth": "5%" },
			  // { "sWidth": "5%" },
			  // { "sWidth": "5%" },
			  // { "sWidth": "5%" },
			  // { "sWidth": "5%" },
			  // { "sWidth": "5%" },
			  // { "sWidth": "5%" },
			  // { "sWidth": "5%" },
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	FromDate=$("#FromDate").val();
	
	// var state 	= $("#state").val();
	// var city 	= $("#city").val();
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&sales_executive_id=" + sales_executive_id + "&type=" + type +"&state=" + state + "&city=" + city +"&flag=" + flag + "&class_id=" + class_id + "&area=" + area ,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type +"&state=" + state + "&city=" + city + "&flag=" + flag + "&class_id=" + class_id + "&area=" + area,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	$("#results").on( "change", "#numRecords", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&type=" + type +"&state=" + state + "&city=" + city + "&flag=" + flag + "&class_id=" + class_id + "&area=" + area,{"page":page}, function(){ //get content from PHP page
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
function genReportexcel(cid){
	
	$("#state").select2("destroy");
	$("#city").select2("destroy");
	var state_id = $("#state").val();
	var city_id = $("#city").val();
	$("#state").remove();
	$("#city").remove();

	setTimeout(function(){
		var rc = encodeURIComponent($("#print_info").html());
		$.ajax({
			type: "POST",
			url: "daily_achivement_genreport.php",
			data: '&rc='+rc,
			beforeSend: function() {
				$(".transCover").fadeIn(800);
				$("#loading").modal('show');
			},
			success: function(result){ //alert(result);
				setTimeout(function(){
					$(".transCover").fadeOut(100);
					$("#loading").modal('hide');
					getCityStateBack(state_id,city_id);
					window.location.href=result;
		    		/*$("#state").show();
		    		$("#city").show();

					$("#state").select2();
		    		$("#city").select2();*/

				},1500);
			}
		});
	},500);
}
function getCityStateBack(state_id,city_id)
{
	$.ajax({
		url: "ajax_get_city_state.php",
		method: "POST",
		data: {state_id:state_id,city_id:city_id},
		success: function(result)
		{
			result = $.parseJSON(result);
			$(".class-state").html(result.state_select);
			$(".class-city").html(result.city_select);
			$("#state").select2();
		    $("#city").select2();
		}
	});
}
function printPDF() 
{
	 var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>tr{height: 30px;}table , td, th { border-collapse: collapse;border: 1px solid #000;}td, th {  padding: 5px;}th { border: 1px solid #595959;background: #f0e6cc;}.text-right{text-align: right;}.center{text-align:center;}.space{ padding: 10px;}.no-border{border-bottom: 1px solid #fff;}.state_s{display:none;}.city_s{display:none;}</style>"+$("#print_info").html());
    myWindow.print();
   
}
</script>

<script type="text/javascript">
	function genCustomerPrint(){
		
      	
      	var FromDate=$('#FromDate').val();
      	var state = $('#state').val();
		var city = $("#city").val();
		
     	var myWindow = window.open('print_daily_achivement.php?searchName='+searchName+'&FromDate='+FromDate+"&state="+state+"&city="+city,'','width=700,height=800');
     	myWindow.print();
    }
</script>
</body>
</html>