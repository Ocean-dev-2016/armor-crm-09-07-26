<?php
$page_id=607;$page_slug='quotation';
$ctable 	= "quotation_detail";
$ctable1 	= "Quotation";
$ctable2 	= "quotation";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = "Quotation History";
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"quotation_manage.php","title"=>$page_title));
include("connect.php");
$FromDate='';
$ToDate='';
$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
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
				<div class="col-xl-12">
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
							<div class="slimScrollDiv" style="position: relative;  width: auto; height: auto;">
								<div class="row">
									<div class="col-md-7 col-xs-7 col-sm-7 pull-right">
                      <div class="form-inline" role="form">
                          <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
                             	<div class="form-group">

                                <input type="text" style="width: 450px!important" placeholder="Search By Person Name/Quotation No :  " class="form-control input-large" name="searchName" id="searchName" value="" />

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
																					<a class="excel" name="excel" onClick="genReport()" id="excel" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</a>
																				</li>
																			<?php
																		}
																		?>
																		<!-- <li>
																			<a class="show-count" name="show-count" onClick="genReport()" id="show-count" title="Show Statictic"><i class="fa fa-bar-chart" aria-hidden="true"></i>Show Statistics</a>
																		</li> -->
																	</ul>
																</div>
																<a style="background-color: #20761fc7;" onclick="showStatics()" title="Show Statistics" type="button" class="btn btn-sm">
																		<i class="fa fa-eye"></i>
																	</a>
                             	</div>
                             <!-- 	<a style="padding: 6px 14px 5px 14px!important;" class="btn btn-primary" href="view_quotation_pipeline.php" target="_blank"  title="track"><span class="text-success"><i style="color: black;" class="fa fa-eye"></i></span>
															</a> -->
                          </form>
                      </div>
                  </div>
                </div>
							</div>
						</div>
					</div>
					<!-- END Portlet PORTLET-->
				</div>
				<div class="portlet-body">
					
						<div class="tab-content">
							<div class="tab-pane active" id="my_order_info">	
								<div class="row">
									<div class="col-sm-12">
										<div class="portlet light">
											<div class="col-md-6">
												<?php
												if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
												{
												}
												echo $db->getAddButton($ctable2);
												?>	
												
											</div>
											<div class="portlet-body">
												<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
												<div id="results"></div>
											</div>
										</div>
									</div>											
								</div>
							</div>
							<div class="tab-pane" id="dealer_order_info">	
								<div class="row">
									<div class="col-sm-12">
										<div class="portlet light">
											<div class="col-md-6">
											</div>
											<div class="portlet-body">
												<div class="loading-div" style="display:none;"> <img src="assets/admin/layout/img/ajax-loader.gif" alt="" style="margin-bottom: 10%;;margin-top:10%;padding-left:48%;" > </div>
												<div id="results_outlets"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					
				</div>
			</div>	
		</div>
	</div>
</div>
<div id="myModal" class="modal fade">
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

<div id="ViewDispatchInfoModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View Dispatch Information </div>
					<div class="tools">

						<a href="javascript:;" id="dispatch_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

						<a href="javascript:;" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
					</div>
				</div>
				<div class="portlet-body portlet-empty" style="">
				</div>
			</div>

		</div>
	</div>
</div>


<div class="modal fade" id="lostModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	    <form method="post" name="lost_form" id="lost_form">
	      <div class="modal-header">
	        <h4 class="modal-title" id="exampleModalLabel">Reason for Lost</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	      		<label>Reason for Lost</label>
	        	<textarea rows="4" name="lost_reason" id="lost_reason" style="width:100%;"></textarea>
	        	<input type="hidden" name="quotation_id" id="quotation_id" value="<?= $bid ?>">
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	        <button type="submit" name="submit" id="submit" class="btn btn-primary">Save changes</button>
	      </div>
	    </form>
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
var data_url = "quotation_get_ajax.php";
// var data_url_outlets = "outlet_orders_get_ajax.php";
$('#ToDate').datepicker({  datepicker: true, autoclose: true });
$('#FromDate').datepicker({  datepicker: true, autoclose: true });
var current_date = '<?= date("d-m-Y") ?>' + " " + "to" + " " + '<?= date("d-m-Y") ?>';
// alert(current_date);
var inquiry_id="";
var ToDate="";
var order_type="";
var FromDate="";
var df1=encodeURI(current_date);

var status="<?= $_REQUEST['status'] ?>";
var type="";
var sales_id="<?= $_REQUEST['sales_id'] ?>";
var flag="3";
var quotation_month="<?= $_REQUEST['quotation_month']?>";
var quotation_year="<?= $_REQUEST['quotation_year']?>";
// alert(quotation_year);
if (quotation_year != "") {
	df1 = "";
}

var customer_id="<?=$_REQUEST['customer_id']?>";
var uid="<?php echo $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']?>";
var todate="<?= date('d-m-Y',strtotime($_REQUEST['todate'])); ?>";
var fromdate="<?=date('d-m-Y',strtotime($_REQUEST['fromdate'])); ?>";
var type_of_company="";
if (todate != "" && fromdate != "" && todate != "01-01-1970" && fromdate != "01-01-1970" ) {
	
	df1 = todate+" to "+fromdate;
	fromdate = "";
	todate = "";
	quotation_year = "";
	df1 = encodeURI(df1);
}


$('#myModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","orders_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})
$('#ViewDispatchInfoModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var order_id=button.data("id");
	$("#dispatch_ajax").attr("data-url","alldispatch_information_get_ajax.php?id="+order_id);
	$("#dispatch_ajax").click();
})
function searchByName(){
	searchName = $("#searchName").val();
	inquiry_id = $("#inquiry_id").val();
	type = $("#type").val();
	sales_id = $("#sales_id").val();
	status = $("#status").val();
	type_of_company = $("#type_of_company").val();
	df1=$("#material_request_filter_input").val();
	df1 = encodeURI(df1);
	displayRecords(50,1);
	// displayRecords_outlets(500,1);
	return false;
}
function clearSearchByName(){
	searchName = "";
	inquiry_id = "";
	ToDate = "";
	FromDate = "";
	df1 = "";
	status = "";
	type = "";
	sales_id = "";
	type_of_company = "";
	$("#searchName").val("");
	$("#inquiry_id").val("");
	$("#ToDate").val("");
	$("#FromDate").val("");
	$("#material_request_filter_input").val("");
	$("#status").val("");
	$("#type").val("");
	$("#sales_id").val("");
	$("#type_of_company").val("");
	//$("#status").selec2("val","");
	displayRecords(50,1);
	// displayRecords_outlets(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
// function getByDate() {
// 	if($("#FromDate").val() != '' && $("#ToDate").val() != '' ){
// 		ToDate = $("#ToDate").val();
// 		FromDate = $("#FromDate").val();
// 		displayRecords(50,1);
// 		// displayRecords_outlets(500,1);
// 	}
// 	else
// 	{
// 		alert("Please Select Date");
// 	}

// }

function genExecutiveExcelReport() {
			var searchName = $("#searchName").val();
			// searchName = encodeURIComponent(searchName.trim());
			var customer_type = $("#customer_type").val();
			var state = $("#state").val();
			var city = $("#city").val();
			var price_list = $("#price_list").val();
			$.ajax({
				method: "POST",
				url: "executive_report_excel.php",
				data: {
					searchName: searchName,
					customer_type: customer_type,
					price_list: price_list,
					state: state,
					city: city
				},
				dataType: 'json',
				beforeSend: function() {
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					// $("#loading-modal").modal('hide');
					$('.preloader').fadeOut('slow');
					window.location.href = "<?= SITEURL ?>" + result.file_path;
				},
				/*error:function(result){
					window.location.href="<?= SITEURL ?>"+result.file_path;
				}*/
			});
		}

		function genExecutivePrint() {

			var searchName = $("#searchName").val();
			searchName = encodeURIComponent(searchName.trim());
			var customer_type = $("#customer_type").val();
			var state = $("#state").val();
			var city = $("#city").val();
			var price_list = $("#price_list").val();
			var type_of_company = $("#type_of_company").val();

			var myWindow = window.open('print_executive_ajax.php?searchName=' + searchName + "&customer_type=" + customer_type + "&state=" + state + "&city=" + city + "&price_list=" + price_list, '', 'width=700,height=800');
			myWindow.print();
		}

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
		displayRecords(50,1);
	}
	else
	{
		toastr.error("From Date Should Be Less Than To Date");
	}
	
}
function getSubCat(cid){
		status=cid;
		displayRecords(50,1);
		// displayRecords_outlets(500,1);
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
			  { "sWidth": "20%"},
			  { "sWidth": "5%" ,"bSortable": false },
			]
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	// status 	= encodeURIComponent(status.trim());
	// sales_id 	= encodeURIComponent(sales_id.trim());
	// inquiry_id 	= encodeURIComponent(inquiry_id.trim());
	// type 	= encodeURIComponent(type.trim());
	// $("#loading-modal").modal('show');
	$('.preloader').fadeIn('slow');
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&ToDate=" + ToDate + "&FromDate=" + FromDate + "&status=" + status +"&order_type=" + order_type  + "&uid=" + uid + "&flag=" + flag + "&type=" + type + "&sales_id=" + sales_id + "&df=" + df1+ "&inquiry_id=" + inquiry_id+ "&quotation_month=" + quotation_month + "&quotation_year=" + quotation_year+ "&customer_id=" + customer_id+ "&todate=" + todate+ "&fromdate=" + fromdate+ "&type_of_company=" + type_of_company,function(){
		// $("#loading-modal").modal('hide');
		$('.preloader').fadeOut('slow');
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		var type = $("#type").val();
		var status = $("#status").val();
		var sales_id = $("#sales_id").val();
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName+ "&order_type=" + order_type + "&uid=" + uid + "&flag=" + flag + "&status=" + status + "&type=" + type + "&sales_id=" + sales_id + "&df=" + df1+ "&inquiry_id=" + inquiry_id+ "&quotation_month=" + quotation_month + "&quotation_year=" + quotation_year+ "&customer_id=" + customer_id+ "&todate=" + todate+ "&fromdate=" + fromdate+ "&type_of_company=" + type_of_company,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
}
function loadDataTable_outlets(){
	$('#datatable_outlets').dataTable({
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
			  { "sWidth": "20%","bSortable": false }
			]
	});
}
// used when user change row limit
function changeDisplayRowCount(numRecords) {
	displayRecords(numRecords, 1);
	// displayRecords_outlets(numRecords,1);
}

$(document).ready(function() {
	displayRecords(100,1);
});
$(document).ready(function() {
	// displayRecords_outlets(500,1);
});



function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='quotation_crud.php?mode=delete&id='+id;
	}
}

function confirmChange(id) 
{
	var r=confirm("Are you sure to forward order to production department?");
	if(r)
	{
		window.location.href="quotation_crud.php?mode=isActive&id="+id+"&status=1";
	}
	
   
}
function genReport()
{
	var searchName = $("#searchName").val();
	searchName     = encodeURIComponent(searchName.trim());
	ToDate = $("#ToDate").val();
	FromDate = 	$("#FromDate").val();
	df1 = 	$("#material_request_filter_input").val();
	status = $("#status").val();
	type = $("#type").val();
	inquiry_id = $("#inquiry_id").val();

	sales_id = $("#sales_id").val();
	type_of_company = $("#type_of_company").val();

	$.ajax({
		method: "POST",
		url: "quotation_info_genReport_ajax.php",
		data: 'searchName=' + searchName + '&ToDate=' + ToDate + '&FromDate=' + FromDate + '&df=' + df1 + '&status=' + status + '&type=' + type + '&sales_id=' + sales_id + '&inquiry_id=' + inquiry_id+ '&type_of_company=' + type_of_company,
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
  //   	error:function(result){
  //   		var result = $.parseJSON(result);
  //   		alert(JSON.stringify(result))
  //   		alert(result.file_path);
		// 	window.location.href="<?=SITEURL?>"+result.file_path;
		// }
	});
}
// function printPDF() 
// {
// 	 var myWindow = window.open('','','width=700,height=800')
//     myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
//     myWindow.print();
   
// }
// function genReportDispatch(cid){
// 	var rc = encodeURIComponent($("#print_info_dispatch").html());
// 	$.ajax({
// 		type: "POST",
// 		url: "alldispatch_info_genReport_ajax.php",
// 		data: 'cid='+cid+'&rc='+rc,
// 		beforeSend: function() {
// 			$(".transCover").fadeIn(800);
// 		},
// 		success: function(result){ 
// 				setTimeout(function(){
// 					$(".transCover").fadeOut(500);
// 					window.location.href=result;
// 				},1500);
// 			}
// 	});
// }
// function printPDFDispatch() 
// {
// 	 var myWindow = window.open('','','width=700,height=800')
//     myWindow.document.write("<style>th,tr,td{border:1px solid #000;border-collapse:collapse padding:10px;}</style>"+$("#print_info_dispatch").html());
//     myWindow.print();
   
// }
</script>
<script type="text/javascript">
	function genOrderPrint(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());      
		df1 = 	$("#material_request_filter_input").val();
		status = $("#status").val();
		type = $("#type").val();
		sales_id = $("#sales_id").val();
		inquiry_id = $("#inquiry_id").val();
		type_of_company = $("#type_of_company").val();
		// alert(df1);
     	var myWindow = window.open('print_quotation_order_ajax.php?searchName='+searchName+ "&uid=" + uid + "&status=" + status + "&sales_id=" + sales_id + "&type=" + type + "&df=" + df1+ "&inquiry_id=" + inquiry_id+ "&type_of_company=" + type_of_company,'','width=700,height=800');
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


    function LostAdd(id){

	var quotation_id = id;

    $("#lost_form").on("submit", function(e) {
            e.preventDefault();
            
            var request_method = $(this).attr("method"); //get form GET/POST method
            var form_data = $("#lost_form").serialize();
            var lost_reason=$('#lost_reason').val();
                $.ajax(
                {
                    url:"lost_reason_ajax.php",
                    type:"POST",
                    // data:$("#lost_form").serialize(),

                    data: {
						quotation_id: quotation_id,
						lost_reason: lost_reason,
					},
                    
                    success:function(result)
                    {
                        let jsonData = JSON.parse(result);  
                        if(jsonData.ack==1)
                        {                          
                            toastr.success("Lost this Order!");
                            $("#lost_form")[0].reset();
                            $("#lostModal").modal('hide');
                         	location.reload();   
                        }
                        else
                        {
                            toastr.error("Something went wrong...");
                        }
                    }
                });
            
        });
    
}
</script>
</body>
</html>