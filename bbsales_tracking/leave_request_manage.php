<?php
$page_id=594;$page_slug='leave_request';
$ctable 	= "leave_request";
$ctable1 	= "Leave Request";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = $ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>$ctable."_manage.php","title"=>$page_title ));
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
                            <div class="slimScrollDiv" style="position: relative; width: auto; height: auto;">
								<div class="row filter_list">
									<div class="col-md-7 col-xs-7 col-sm-7 pull-right">
					                  <div class="form-inline" role="form">
					                    <form class="form-inline pull-right" role="form" onSubmit="return searchByName();">
					                      <div class="form-group">
					                      	<input type="text" style="width: 450px!important" placeholder="Search Here" class="form-control input-large" name="searchName" id="searchName" value="" />
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
															<a name="print" onClick="genleaverequestPrint()" title="Print Report"><i class="fa fa-print"></i>Print</a>
														</li>

													<?php
														}
														?>
														<?php
														if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
														{
															?>
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
									<!-- <div class="col-md-4 col-xs-4 col-sm-4">
								  		<label>Search By To Date AND From Date: &nbsp;</label>
								  		<div class="input-group input-medium pull-left">
											<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
											<span class="input-group-addon datetimerange-picker-btn">
												<i class="fa fa-calendar"></i>
											</span>
										
											<span class="input-group-btn">
								          	<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
								        	</span>
								        </div> 
                                     </div> -->
                                    <!-- <div class="col-md-8 col-xs-8 col-sm-8" style="margin-top:10px;">
									  	<form class="form-inline" role="form" onSubmit="return searchByName();">	
									  		<div class="form-group">
												<label>Search By Sales Person Name: &nbsp;</label>
												<input type="text" class="form-control input-medium" name="searchName" id="searchName" value="" placeholder="Search Here" />
											</div>
											<div class="form-group">
												<input class="btn red-haze btn-sm" type="submit" value="search">
												<input class="btn green-haze btn-sm" type="button" value="clear" onClick="clearSearchByName();">
											</div>
										</form>
									</div>
								  	<div class="col-md-2 col-xs-2 col-sm-2 pull-right" style="margin-top:10px">

								  		<?php
												if($rights['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
												{ 
													?>

								  		<button type="button" class="btn btn-sm print pull-right" style="background-color: #f0ad4e;color: #fff;" name="print" onClick="genleaverequestPrint()" id="print" href="" title="Download XL Report"><i class="fa fa-print"></i>Print</button>
								  		<?php
											}
											if($rights['export_excel_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
											{ 
												?>
										<button type="button" class="btn green-haze btn-sm excel " name="excel" onClick="genReport()" id="excel" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Excel</button>
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
					
						<div class="table-toolbar">
							<div class="row">
								<div class="col-md-6">
									<?php
										echo $db->getAddButton($ctable);
									?>	
								</div>								
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
	
</div>
<div id="myModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View Customer Information
					</div>
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
<script type="text/javascript">
var status="<?= $_REQUEST['status'] ?>";
var searchName="";
var leave_type = "";
var leave_category ="";
var df=encodeURI("<?= date('d-m-Y') ." to ". date('d-m-Y') ?>");
var leave_month="<?= $_REQUEST['leave_month'] ?>";
var leave_year="<?= $_REQUEST['leave_year'] ?>";
var data_url = "<?php echo $ctable ?>_get_ajax.php";
var todate="<?= date('d-m-Y',strtotime($_REQUEST['todate'])) ?>";
var fromdate="<?= date('d-m-Y',strtotime($_REQUEST['fromdate'])) ?>";
$('#myModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var requesting_id=button.data("id");
	$("#requesting_ajax").attr("data-url","executive_information_get_ajax.php?id="+requesting_id);
	$("#requesting_ajax").click();
})
if (todate != "" && fromdate != "" && todate != "01-01-1970" && fromdate != "01-01-1970" ) {
	
	df = todate+" to "+fromdate;
	fromdate = "";
	todate = "";
	leave_year = "";
	df = encodeURI(df);
}

// function searchBystatus(type){
// 	    status=type;
//         displayRecords(500,1);
// }
function searchByName(){
	searchName = $("#searchName").val();
	leave_type = $("#leave_type").val();
	leave_category = $("#leave_category").val();
	status = $("#status").val();
	displayRecords(500,1);
	return false;
}

// $(".filterBtn").on("click",function()
// {
// 	sales_executive = $("#sales_executive").val();
// 	status = $("#status").val();
// 	customer_id = $("#customer_id").val();
// 	df=$("#material_request_filter_input").val();
// 	df = encodeURI(df)
// 	displayRecords(500,1);
// })

function clearSearchByName(){
	status = "";
	searchName = "";
	leave_type = "";
	leave_category = "";
	df = "";
	$("#status").val("");
	$("#searchName").val("");
	$("#leave_type").val("");
	$("#leave_category").val("");
	$("df").val("");
	$("#material_request_filter_input").val("");
	displayRecords(500,1);
}
$("#searchName").keyup(function(event){
	if(event.keyCode == 13){
		$("#searchByName").click();
	}
});
function loadDataTable(){
	
	$('#example1').dataTable({
		"bPaginate": false,
		"order":['desc'],
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
   //      "aoColumns": [
			//   { "sWidth": "10%" }, 
			//   { "sWidth": "30%" },
			//   { "sWidth": "20%" },
			//   { "sWidth": "20%" },
			//   { "sWidth": "20%" },
			//   { "sWidth": "20%","bSortable": false }
			// ],
			// "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-cubes '></i> &nbsp; No Product Found"},
			 "order": ['asc'], /* default order is index 1 */
                    'columnDefs': [ {
                        'targets': [0], /* column index */
                        'orderable': false, /* true or false */
                    }],

                    "oLanguage": {
                        "sEmptyTable": "Sorry No Data Available!!"
                    }
	});
}
function displayRecords(numRecords) {
	var searchName 	= $("#searchName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	var leave_type = $("#leave_type").val();
	//var status = $("#status").val();
	$("#results" ).html("");
	$("#results" ).load( data_url+"?show=" + numRecords + "&searchName=" + searchName + "&status=" + status + "&df=" + df + "&leave_type=" + leave_type+ "&leave_month=" + leave_month+ "&leave_year=" + leave_year+ "&todate=" + todate+ "&fromdate=" + fromdate+ "&leave_category=" + leave_category,function(){
		loadDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results").on( "click", ".paging_simple_numbers a", function (e){
		//var status = $("#status").val();
		e.preventDefault();
		var numRecords  = $("#numRecords").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&df=" + df + "&status=" + status + "&leave_type=" + leave_type+ "&leave_month=" + leave_month+ "&leave_year=" + leave_year+ "&todate=" + todate+ "&fromdate=" + fromdate+ "&leave_category=" + leave_category,{"page":page}, function(){ //get content from PHP page
			$(".loading-div").hide(); //once done, hide loading element
			loadDataTable();
		});
		
	});
	// $("#results").on( "change", "#numRecords", function (e){
	// 	var status = $("#status").val();
	// 	e.preventDefault();
	// 	var numRecords  = $("#numRecords").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&df=" + df + "&status=" + status + "&leave_type=" + leave_type,{"page":page}, function(){ //get content from PHP page
	// 		$(".loading-div").hide(); //once done, hide loading element
	// 		loadDataTable();
	// 	});
		
	// });
	
	// $("#results").on( "change", "#leave_type", function (e){
	// 	e.preventDefault();
	// 	var status = $("#status").val();
	// 	var numRecords  = $("#numRecords").val();
	// 	var leave_type = $("#leave_type").val();
	// 	$(".loading-div").show(); //show loading element
	// 	var page = $(this).attr("data-page"); //get page number from link
	// 	$("#results").load(data_url+"?show=" + numRecords + "&searchName=" + searchName + "&leave_type=" + leave_type + "&df=" + df + "&status=" + status,{"page":page}, function(){ //get content from PHP page
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
	displayRecords(500,1);
});
function del_conf(id){
	var r = confirm("Are you sure you want to delete?");
	if(r){
		window.location.href='<?php echo $ctable; ?>_crud.php?mode=delete&id='+id;
	}
}
function genReport()
{
	var searchName     = $("#searchName").val();
	searchName     = encodeURIComponent(searchName.trim());
	//status = $("#status").val();
  	leave_type = $("#leave_type").val();
  	leave_category = $("#leave_category").val();
	df = $("#material_request_filter_input").val();

      	$.ajax({
			method: "POST",
			url: "leave_request_genReport_ajax.php",
			data:{
        		searchName:searchName,
        		status:status,
        		leave_type:leave_type,
        		leave_category:leave_category,
        		df:df,
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

function printPDF() 
{
	 var myWindow = window.open('','','width=700,height=800')
    myWindow.document.write("<style>th,tr,td{border:1px solid #000; padding:10px;}</style>"+$("#print_info").html());
    myWindow.print();
   
}

/*dispay order function*/
function CheckDispalyOrder(id)
{
	var display_order = $("#disp"+id).val();
	var size_id = $("#disp"+id).data("size-id");

	$.ajax({
		type: "POST",
		url: "check_display_order_ajax.php",
		data: 'display_order='+display_order+"&id="+size_id+"&table=weight",
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

</script>

<!-- <script type="text/javascript">
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
</script> -->
<script type="text/javascript">
	function genleaverequestPrint(){
		var searchName     = $("#searchName").val();
      	searchName     = encodeURIComponent(searchName.trim());
      	//status = $("#status").val();
      	leave_type = $("#leave_type").val();
      	leave_category = $("#leave_category").val();
		df = $("#material_request_filter_input").val();
     	var myWindow = window.open('print_leave_request_ajax.php?searchName='+searchName + "&leave_type=" + leave_type + "&df=" + df + "&status=" + status + "&leave_category=" + leave_category,'','width=700,height=800');
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
</body>
</html>
</html>